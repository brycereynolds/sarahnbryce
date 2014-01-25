<?php
require_once("app.php");

if($status == 'live'){

    $DBUser     = 'bryce';
    $DBPass     = 'choenolds';

}else{
    $DBPass     = 'password';
    $DBUser     = 'root';
}

$DBServer   = 'localhost';
$DBName     = 'wedding';


$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);

// check connection
if ($conn->connect_error) {
    trigger_error('Database connection failed: ' . $conn->connect_error, E_USER_ERROR);
}

// $sql='SELECT * FROM responses';
// $rs=$conn->query($sql);

// if($rs === false) {
//     trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $conn->error, E_USER_ERROR);
// } else {
//     $rows_returned = $rs->num_rows;
// }

// echo 'out here';
// echo $rows_returned;
// exit();


/*
BUILD DATABASE

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS responses;
CREATE TABLE `responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` char(255) DEFAULT NULL,
  `last_name` char(255) DEFAULT NULL,
  `wedding_rsvp` enum('accept', 'decline') DEFAULT NULL,
  `montana_rsvp` enum('accept', 'decline') DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `zip_code` char(50) DEFAULT NULL,
  `email` char(255) DEFAULT NULL,
  `phone` char(255) DEFAULT NULL,
  `food_allergy` char(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS guests;
CREATE TABLE `guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `response_id` int(11) NOT NULL,
  `guest` enum('adult', 'child') DEFAULT NULL,
  `first_name` char(255) DEFAULT NULL,
  `last_name` char(255) DEFAULT NULL,
  `age` char(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `responses_FK` (`response_id`),
  CONSTRAINT `responses_FK` FOREIGN KEY (`response_id`) REFERENCES `responses` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
*/