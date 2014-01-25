<?php

include("database.php");

$d = $_POST;

$d['firstname'] = 'Bryce';
$d['lastname'] = 'Reynolds';
$d['address'] = '35 Union Avenue';
$d['zip_code'] = '95008';
$d['email'] = 'brycereynoldsdesign@gmail.com';
$d['phone'] = '2535695159';
$d['food_allergy'] = 'Gluten-free and vegetarian';
$d['wedding_rsvp'] = 'accept';
$d['montana_rsvp'] = 'accept';

$d['guest_first_name'] = Array('Guest', 'Guest');
$d['guest_last_name'] = Array('One', 'Two');
$d['child_first_name'] = Array('Child', 'Child', 'Child');
$d['child_last_name'] = Array('One', 'Two', 'Three');
$d['child_age'] = Array('15 months', '11', '15');


// Declaring the variable for submitted inputs

$firstname      = $d['firstname'];
$lastname       = $d['lastname'];
$address        = $d['address'];
$zip_code       = $d['zip_code'];
$email          = $d['email'];
$phone          = $d['phone'];
$wedding_rsvp   = $d['wedding_rsvp'];
$montana_rsvp   = $d['montana_rsvp'];
$food_allergy   = $d['food_allergy'];

// Check that all required inputs are not empty.
if(empty($firstname) || empty($lastname) || empty($wedding_rsvp) || empty($email) ) {
    $error = 'Please ensure all required inputs are provided.';
}

//Validates correct email formatting
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please use the correct format for your email and try again';
}

if(!empty($error)){
    $return['status'] = 'error';
    $return['msg'] = $error;
    echo json_encode($return);
    die;
}

function res($conn, $string){
    return $conn->real_escape_string($string);
}

// Add response to DB
$query = "
INSERT INTO `responses` (`first_name`, `last_name`, `wedding_rsvp`, `montana_rsvp`, `address`, `zip_code`, `email`, `phone`, `food_allergy`, `created`)
VALUES
    (
        '" . res($conn, $firstname) . "',
        '" . res($conn, $lastname) . "',
        '" . res($conn, $wedding_rsvp) . "',
        '" . res($conn, $montana_rsvp) . "',
        '" . res($conn, $address) . "',
        '" . res($conn, $zip_code) . "',
        '" . res($conn, $email) . "',
        '" . res($conn, $phone) . "',
        '" . res($conn, $food_allergy) . "',
        NOW()
    )";

if($conn->query($query) === false) {
    trigger_error('Wrong SQL: ' . $query . ' Error: ' . $conn->error, E_USER_ERROR);
} else {
    $responseId = $conn->insert_id;
}

// Add in guests
if(isset($d['guest_first_name'])){
    for ($i=0; $i < count($d['guest_first_name']); $i++) { 
        $query = "
        INSERT INTO `guests` (`response_id`, `guest`, `first_name`, `last_name`, `age`, `created`)
        VALUES
            (
                '" . $responseId . "',
                'adult',
                '" . res($conn, isset($d['guest_first_name'][$i]) ? $d['guest_first_name'][$i] : 'unknown' ) . "',
                '" . res($conn, isset($d['guest_last_name'][$i]) ? $d['guest_last_name'][$i] : 'unknown' ) . "',
                NULL,
                NOW()
            )";

        if($conn->query($query) === false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $conn->error, E_USER_ERROR);
        }
    }
}


// Add in children
if(isset($d['child_first_name'])){
    for ($i=0; $i < count($d['child_first_name']); $i++) { 
        $query = "
        INSERT INTO `guests` (`response_id`, `guest`, `first_name`, `last_name`, `age`, `created`)
        VALUES
            (
                '" . $responseId . "',
                'child',
                '" . res($conn, isset($d['child_first_name'][$i]) ? $d['child_first_name'][$i] : 'unknown' ) . "',
                '" . res($conn, isset($d['child_last_name'][$i]) ? $d['child_last_name'][$i] : 'unknown' ) . "',
                '" . res($conn, isset($d['child_age'][$i]) ? $d['child_age'][$i] : 'unknown' ) . "',
                NOW()
            )";

        if($conn->query($query) === false) {
            trigger_error('Wrong SQL: ' . $query . ' Error: ' . $conn->error, E_USER_ERROR);
        }
    }
}




// Send an email for us as a notice

$guestCount = count($d['guest_first_name']);
$childCount = count($d['child_first_name']);

// Construct the message
$message = '';
$message .= <<<TEXT
You have received an RSVP submission.

Guest Details
=================================
Name: {$firstname} {$lastname}
Email: {$email}
Phone: {$phone}
Attendance: {$wedding_rsvp}
MT RSVP: {$montana_rsvp}

Guests Count: {$guestCount}
Child Count: {$childCount}

TEXT;

// Email to send to
$to = 'sarahnbryce@gmail.com';

// Email Subject
$subject = 'Website | {$firstname} {$lastname} has submitted their RSVP status.';

// Name to show email from
$from = 'Sarah and Bryce';

// Domain to show the email from
$fromEmail = 'sarahnbryce@gmail.com';

// Construct a header to send who the email is from
$header = 'From: ' . $from . '<' . $fromEmail . '>';

// Try sending the email
if(!mail($to, $subject, $message, $header)) {

    $return['status'] = 'error';
    $return['msg'] = 'We were not able to send an email at this time. Please try again later or let us know of this error. Thanks!';

}else{

    $return['status'] = 'ok';

}

echo json_encode($return);
die;