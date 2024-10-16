const express = require('express');
const sha1 = require('sha1');
const db = require('./config/db'); // Adjust the path to your database configuration
const fs = require('fs');
const sendSMS = require('./sms/sendSMS'); // Import the sendSMS function
const sendEmail = require('./sendEmail'); // Import the sendEmail function

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

  let lang = 'en'; // Default language is English

  // Language selection
  if (text === '') {
    return res.send(`CON ${t('language_selection', lang)}\n1. English\n2. Kinyarwanda`);
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

          // Pass the actual slotId, not the index
          let slotId = slots[selectedSlotIndex].id;
          return res.send(`CON ${t('enter_village', lang)}`);
        });
      } else if (textArray.length === 4) {
        // Village received, proceed to ask for the reason
        let village = textArray[3]; // Correctly using the village
        return res.send(`CON ${t('enter_reason', lang)}`);
      } else if (textArray.length === 5) {
        let reason = textArray[4];  // The reason for the appointment is entered here
        let village = textArray[3];
        let slotId = textArray[2];
        let citizenId = user.userId;

        // Save the appointment with the selected slot
        db.query('INSERT INTO appointments (village, reason, status, citizenId, slotId) VALUES (?, ?, ?, ?, ?)',
          [village, reason, 'pending', citizenId, slotId], async (err, result) => {
            if (err) {
              console.error(err);
              return res.send('END An error occurred while saving your appointment');
            }

            // Prepare message details
            const smsMessage = `Your appointment has been scheduled successfully! Village: ${village}, Reason: ${reason}`;
            const emailSubject = 'Appointment Confirmation';
            const emailMessage = `Dear ${user.username},\n\nYour appointment has been scheduled successfully with the following details:\n\nVillage: ${village}\nReason: ${reason}\nSlot ID: ${slotId}\n\nThank you!`;

            // Send SMS to the user
            try {
              await sendSMS(phoneNumber, smsMessage); // Call sendSMS function
            } catch (smsError) {
              console.error('SMS sending failed:', smsError);
            }

            // Send email to the user
            try {
              await sendEmail(user.email, emailSubject, emailMessage); // Call sendEmail function
            } catch (emailError) {
              console.error('Email sending failed:', emailError);
            }

            return res.send(`END ${t('appointment_successful', lang)}`);
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
        db.query('INSERT INTO users (username, email, password, phoneNumber) VALUES (?, ?, ?, ?)',
          [username, email, password, phoneNumber], (err, result) => {
            if (err) {
              console.error(err);
              return res.send('END Registration failed. Please try again later.');
            }
            return res.send(`END ${t('registration_successful', lang)}`);
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
