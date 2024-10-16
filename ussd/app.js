const express = require('express');
const sha1 = require('sha1');
const db = require('./config/db'); // Adjust path as necessary
const fs = require('fs');
const sendSMS = require('./sms/sendSMS');
const app = express();
const PORT = 3000;

// Body parser middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Load language translations
const translations = {
  en: JSON.parse(fs.readFileSync('./i18n/en.json')),
  rw: JSON.parse(fs.readFileSync('./i18n/rw.json'))
};

// Function to get translations based on language
function t(key, lang) {
  return translations[lang][key] || key;
}

// USSD handler
app.post('/ussd', (req, res) => {
  let { sessionId, serviceCode, phoneNumber, text } = req.body;
  let textArray = text.split('*');
  let userResponse = textArray[textArray.length - 1]; // Get the last user input

  let lang = 'en'; // Default language

  // Language selection
  if (text === '') {
    return res.send(`CON ${t('language_selection', lang)}`);
  } else if (textArray[0] === '1') {
    lang = 'en';
  } else if (textArray[0] === '2') {
    lang = 'rw';
  }

  // Check if the user is registered
  db.query('SELECT * FROM users WHERE phoneNumber = ?', [phoneNumber], (err, results) => {
    if (err) {
      console.error(err);
      return res.send('END An error occurred');
    }

    if (results.length > 0) {
      let user = results[0];

      // Password authentication
      if (textArray.length === 1) {
        return res.send(`CON ${t('enter_password', lang)}`);
      } else if (textArray.length === 2) {
        if (sha1(userResponse) === user.password) {
          // Fetch available slots from the database
          db.query('SELECT id, startTime, endTime FROM slots WHERE availability = 1', (err, slots) => {
            if (err) {
              console.error(err);
              return res.send('END An error occurred while fetching slots');
            }

            if (slots.length === 0) {
              return res.send(`END ${t('no_slots_available', lang)}`);
            }

            // Display available slots to the user
            let slotMessage = `${t('available_slots', lang)}\n`;
            slots.forEach((slot, index) => {
              slotMessage += `${index + 1}. ${slot.startTime} to ${slot.endTime}\n`;
            });

            return res.send(`CON ${slotMessage}`);
          });
        } else {
          return res.send(`END ${t('wrong_password', lang)}`);
        }
      } else if (textArray.length === 3) {
        // Slot selection
        let selectedSlotIndex = parseInt(userResponse) - 1;

        db.query('SELECT id FROM slots WHERE availability = 1', (err, slots) => {
          if (err || selectedSlotIndex < 0 || selectedSlotIndex >= slots.length) {
            return res.send('END Invalid slot selection');
          }

          // Proceed to village entry, passing slotId in the text string
          let slotId = slots[selectedSlotIndex].id;
          return res.send(`CON ${t('enter_village', lang)}*${slotId}`);
        });
      } else if (textArray.length === 4) {
        // Village and slotId received in text
        let village = textArray[3];
        let slotId = textArray[2]; // Extract the slotId from the previous step
        return res.send(`CON ${t('enter_reason', lang)}*${slotId}*${village}`);
      } else if (textArray.length === 5) {
        let reason = textArray[3];
        let village = textArray[4];
        let slotId = textArray[2];
        let citizenId = user.userId;

        // Save the appointment with the selected slot
        db.query('INSERT INTO appointments (village, reason, status, citizenId, slotId) VALUES (?, ?, ?, ?, ?)',
          [village, reason, 'pending', citizenId, slotId], (err, result) => {
            if (err) {
              console.error(err);
              return res.send('END An error occurred while saving your appointment');
            }

            // Mark the slot as unavailable
            db.query('UPDATE slots SET availability = 0 WHERE id = ?', [slotId], (err, result) => {
              if (err) {
                console.error(err);
              }
              return res.send(`END ${t('appointment_successful', lang)}`);
            });
          });
      }
    } else {
      // If user doesn't exist, start registration
      if (textArray.length === 1) {
        return res.send(`CON ${t('register_username', lang)}`);
      } else if (textArray.length === 2) {
        return res.send(`CON ${t('register_email', lang)}`);
      } else if (textArray.length === 3) {
        return res.send(`CON ${t('register_password', lang)}`);
      } else if (textArray.length === 4) {
        let username = textArray[1];
        let email = textArray[2];
        let password = sha1(textArray[3]);

        // Register user
// After successfully saving the appointment
db.query('INSERT INTO appointments (village, reason, status, citizenId, slotId) VALUES (?, ?, ?, ?, ?)',
  [village, reason, 'pending', citizenId, slotId], (err, result) => {
    if (err) {
      console.error(err);
      return res.send('END An error occurred while saving your appointment');
    }

    // Mark the slot as unavailable
    db.query('UPDATE slots SET availability = 0 WHERE id = ?', [slotId], (err, result) => {
      if (err) {
        console.error(err);
      }

      // Construct detailed SMS message
      let message = `${t('appointment_sms_heading', lang)}\n` +
                    `${t('appointment_sms_village', lang)}: ${village}\n` +
                    `${t('appointment_sms_reason', lang)}: ${reason}\n` +
                    `${t('appointment_sms_status', lang)}: pending\n` +
                    `${t('appointment_sms_time', lang)}: ${slotId}`;  // Optionally, format slot time based on DB query
      
      // Send the SMS to the user
      sendSMS(phoneNumber, message);
      
      return res.send(`END ${t('appointment_successful', lang)}`);
    });
});

      }
    }
  });
});

// Start the server
app.listen(PORT, () => {
  console.log(`USSD service running on port ${PORT}`);
});

// Connect to the database
db.connect((err) => {
  if (err) {
    console.error('Database connection failed:', err.stack);
    return;
  }
  console.log('Connected to the database');
});
