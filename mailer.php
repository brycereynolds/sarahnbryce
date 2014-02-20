<?php

include("database.php");


///// MINOR VALIDATION - Leaving most of it to front-end
$d = $_POST;

// $d['firstname'] = 'Bryce';
// $d['lastname'] = 'Reynolds';
// $d['address'] = '35 Union Avenue';
// $d['zip_code'] = '95008';
// $d['email'] = 'brycereynoldsdesign@gmail.com';
// $d['phone'] = '2535695159';
// $d['food_allergy'] = 'Gluten-free and vegetarian';
// $d['wedding_rsvp'] = 'accept';
// $d['montana_rsvp'] = 'accept';

// $d['guest_first_name'] = Array('Guest', 'Guest');
// $d['guest_last_name'] = Array('One', 'Two');
// $d['child_first_name'] = Array('Child', 'Child', 'Child');
// $d['child_last_name'] = Array('One', 'Two', 'Three');
// $d['child_age'] = Array('15 months', '11', '15');

// Declaring the variable for submitted inputs

$firstname      = !empty($d['firstname'])    ? $d['firstname']       : 'unknown';
$lastname       = !empty($d['lastname'])     ? $d['lastname']        : 'unknown';
$address        = !empty($d['address'])      ? $d['address']         : 'unknown';
$zip_code       = !empty($d['zip_code'])     ? $d['zip_code']        : 'unknown';
$email          = !empty($d['email'])        ? $d['email']           : 'unknown';
$phone          = !empty($d['phone'])        ? $d['phone']           : 'unknown';
$wedding_rsvp   = !empty($d['wedding_rsvp']) ? $d['wedding_rsvp']    : 'unknown';
$montana_rsvp   = !empty($d['montana_rsvp']) ? $d['montana_rsvp']    : 'unknown';
$food_allergy   = !empty($d['food_allergy']) ? $d['food_allergy']    : 'unknown';
$comments       = !empty($d['comments'])     ? $d['comments']        : 'unknown';
$code           = !empty($d['code'])         ? $d['code']            : 'unknown';

$responseId = !empty($d['response_id']) ? $d['response_id'] : false;

$isSubmission = $d['submission'] == "true";

// Check that all required inputs are not empty.
// if(empty($firstname) || empty($lastname) || empty($wedding_rsvp) || empty($email) ) {
//     $error = 'Please ensure all required inputs are provided.';
// }

// //Validates correct email formatting
// if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//     $error = 'Please use the correct format for your email and try again';
// }

// if(!empty($error)){
//     $return['status'] = 'error';
//     $return['msg'] = $error;
//     echo json_encode($return);
//     die;
// }

if(!isset($d['child_first_name'])) $d['child_first_name'] = array();
if(!isset($d['guest_first_name'])) $d['guest_first_name'] = array();




///// QUICKLY SAVING INFO INTO IN CASE MAIL BOUNCES
// Add response to DB
function res($conn, $string){
    return $conn->real_escape_string($string);
}

if(!$responseId){
    $query = "
    INSERT INTO `responses`
        (
            `first_name`,
            `last_name`,
            `wedding_rsvp`,
            `montana_rsvp`,
            `address`,
            `zip_code`,
            `email`,
            `phone`,
            `food_allergy`,
            `comments`,
            `code`,
            `submitted`,
            `created`
        )
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
            '" . res($conn, $comments) . "',
            '" . res($conn, $code) . "',
            " . ($isSubmission ? 'NOW()' : 'NULL') . ",
            NOW()
        )";

}else{
    $query = "
    UPDATE
        responses 
    SET
        `first_name` = '" . res($conn, $firstname) . "',
        `last_name` = '" . res($conn, $lastname) . "',
        `wedding_rsvp` = '" . res($conn, $wedding_rsvp) . "',
        `montana_rsvp` = '" . res($conn, $montana_rsvp) . "',
        `address` = '" . res($conn, $address) . "',
        `zip_code` = '" . res($conn, $zip_code) . "',
        `email` = '" . res($conn, $email) . "',
        `phone` = '" . res($conn, $phone) . "',
        `food_allergy` = '" . res($conn, $food_allergy) . "',
        `comments` = '" . res($conn, $comments) . "',
        `code` = '" . res($conn, $code) . "',
        `submitted` = " . ($isSubmission ? 'NOW()' : 'NULL') . "

    WHERE
        id = $responseId";
}

if($conn->query($query) === false) {
    trigger_error('Wrong SQL: ' . $query . ' Error: ' . $conn->error, E_USER_ERROR);
} else {
    if(!$responseId) $responseId = $conn->insert_id;
}


// Add in guests and childen then email if this is a submission
if(!$isSubmission){
    $return['status'] = 'ok';
    $return['response_id'] = $responseId;
    echo json_encode($return);
    return;
}


// Add in guests - only on submit
$guests = array();
if(isset($d['guest_first_name']) && !empty($d['guest_first_name'][0]) ){
    for ($i=0; $i < count($d['guest_first_name']); $i++) { 
        $guests[] = $d['guest_first_name'][$i]. ' ' . $d['guest_last_name'][$i];

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


// Add in children - only on submit
$children = array();
if(isset($d['child_first_name']) && !empty($d['child_first_name'][0])){
    for ($i=0; $i < count($d['child_first_name']); $i++) { 

        $children[] = $d['child_first_name'][$i]. ' ' . $d['child_last_name'][$i];

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

$guestCount = count($guests);
$childCount = count($children);

$guestsString = implode(", ", $guests);
$childrenString = implode(", ", $children);

$codeString = ($code == 'none' ? 'No Plus One' : ($code == 'sccc' ? 'Allow Plus One & Kids' : 'Allow Plus One'));

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

Food Allergy: {$food_allergy}
Comments: {$comments}

We allowed: {$codeString}

Additional Guest Count: {$guestCount}
Guests: {$guestsString}

Child Count: {$childCount}
Children: {$childrenString}

TEXT;

// Email to send to
$to = 'sarahnbryce@gmail.com';

// Email Subject
$subject = "Website | $firstname $lastname has submitted their RSVP status.";

// Name to show email from
$from = 'Sarah and Bryce';

// Domain to show the email from
$fromEmail = 'brycereynoldsdesign@gmail.com';

// Construct a header to send who the email is from
$header = 'From: ' . $from . '<' . $fromEmail . '>';

// Try sending the email
if(!mail($to, $subject, $message, $header)) {

    $return['status'] = 'error';

}else{

    $return['status'] = 'ok';

}

echo json_encode($return);
die;