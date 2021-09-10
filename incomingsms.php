<?php 
// http://ca6984fa9bb7.ngrok.io/ussdsms/incomingsms.php
include_once 'db.php';
include_once 'util.php';
include_once 'user.php';


//SMS LOGIC
//For users registering account via sending text to registered shortcode
//i.e name pin e.g ian 1234
$phoneNumber = $_POST['from'];
$text = $_POST['text'];

$user = new User($phoneNumber);
$db = new DBConnector();
$pdo = $db->connectToDB();

//Obtaining text to array
$text = explode(" ",$text);

//Setting Name and PIN to the setters in user class
$user->setName($text[0]);
$user->setPin($text[1]);
$user->setBalance(Util::$USER_BALANCE);

$user->register($pdo);
?>