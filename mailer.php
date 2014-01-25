<?php


pr($_POST);
exit();

// Blank message to start with so we can append to it.
$message = '';

// Declaring the variable for submitted inputs

$firstname  = $_POST['firstname'];
$lastname   = $_POST['lastname'];
$email      = $_POST['email'];
$phone      = $_POST['phone'];
$maincourse = $_POST['main-course']; 
$dessert    = $_POST['dessert'];
$attendance = $_POST['radio'];

// Check that all required inputs are not empty.
if(empty($firstname) || empty($lastname) || empty($attendance) || empty($email) ) {
    die('Please ensure all required inputs are provided.');
}

//Validates correct email formatting
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Please use the correct format for your email and try again');
}

// Construct the message
$message .= <<<TEXT
You have received an RSVP submission.

Guest Details
=================================
Name: {$firstname} {$lastname}
Email: {$email}
Phone: {$phone}
Attendance: {$attendance}

Meal Selection
=================================
Main: {$maincourse} 
Dessert: {$dessert}

TEXT;

// Email to send to
$to = 'sarahnbryce@gmail.com';

// Email Subject
$subject = 'Website | {$firstname} {$lastname} has submitted their RSVP status.';

// Name to show email from
$from = 'Sarah and Bryce';

// Domain to show the email from
$fromEmail = 'hello@brahah.com';

// Construct a header to send who the email is from
$header = 'From: ' . $from . '<' . $fromEmail . '>';

// Try sending the email
if(!mail($to, $subject, $message, $header)) {
    die('Error sending email.');
} else {
    die('Thanks for submitting your RSVP.');
}