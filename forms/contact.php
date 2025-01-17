<?php
// Check if the required "PHP Email Form" library exists
$php_email_form_path = '../assets/vendor/php-email-form/php-email-form.php';

// Replace with your real receiving email address
$receiving_email_address = 'hungcuong.fort.it@gmail.com';

// Ensure the email address is set correctly
if (!filter_var($receiving_email_address, FILTER_VALIDATE_EMAIL)) {
    die('Invalid receiving email address.');
}

// Check if the library file exists, and include it if found
if (file_exists($php_email_form_path)) {
    include($php_email_form_path);
} else {
    die('Unable to load the "PHP Email Form" Library!');
}

// Create a new instance of PHP_Email_Form
$contact = new PHP_Email_Form();
$contact->ajax = true;

// Set the recipient's email address
$contact->to = $receiving_email_address;

// Validate and set form inputs
if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['subject']) && !empty($_POST['message'])) {
    // Sanitize form inputs to avoid XSS attacks
    $contact->from_name = htmlspecialchars($_POST['name']);
    $contact->from_email = htmlspecialchars($_POST['email']);
    $contact->subject = htmlspecialchars($_POST['subject']);

    // Validate the sender's email address
    if (!filter_var($contact->from_email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid sender email address.');
    }

    // Add the message fields
    $contact->add_message($_POST['name'], 'From');
    $contact->add_message($_POST['email'], 'Email');
    $contact->add_message($_POST['message'], 'Message', 10);

    // Uncomment the block below to use SMTP (ensure SMTP credentials are correct)
    /*
    $contact->smtp = array(
        'host' => 'smtp.example.com', // Replace with your SMTP server
        'username' => 'your-smtp-username',
        'password' => 'your-smtp-password',
        'port' => '587', // Use 465 for SSL, 587 for TLS
        'encryption' => 'tls' // Use 'ssl' for SSL encryption
    );
    */

    // Send the email and output the result
    if ($contact->send()) {
        echo 'Message sent successfully!';
    } else {
        echo 'Failed to send the message. Please try again.';
    }
} else {
    // Handle the case where any of the form fields are empty
    die('All fields are required.');
}
?>
