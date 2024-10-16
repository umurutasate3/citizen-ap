const nodemailer = require('nodemailer');

// Create a transporter for sending emails
const transporter = nodemailer.createTransport({
    service: 'gmail', // Use your email service provider
    auth: {
        user: 'paccyhabi@gmail.com', // Your email address
        pass: 'xzec mayt wrwu ewch' // Your email password or app-specific password
    }
});

// Function to send an email
module.exports = function sendEmail(to, subject, text) {
    const mailOptions = {
        from: 'paccyhabi@gmail.com', // Sender's email address
        to: to, // Recipient's email address
        subject: subject, // Subject line
        text: text // Email body
    };

    return transporter.sendMail(mailOptions)
        .then(info => {
            console.log('Email sent: ' + info.response);
        })
        .catch(error => {
            console.error('Error sending email: ' + error);
            throw error;
        });
};
