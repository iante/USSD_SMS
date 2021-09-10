<?php
//Africas Talking API KEY
//09a16dd0e834f3b385889fd86319a29379e1c28420e50e1775a287917ca45092
include_once 'db.php';
$date = $_POST['date'];
$sessionId = $_POST['sessionId'];
$serviceCode = $_POST['serviceCode'];
$networkCode = $_POST['networkCode'];
$phoneNumber = $_POST['phoneNumber'];
$status = $_POST['status'];
$cost = $_POST['cost'];
$durationInMillis = $_POST['durationInMillis'];
$input = $_POST['input'];
$lastAppResponse = $_POST['lastAppResponse'];
$errorMessage = $_POST['errorMessage'];

$db = new DBConnector();

$pdo = $db->connectToDB();

function saveUssdNotification($pdo,$date,$sessionId,$serviceCode,
$networkCode,$phoneNumber,$status,$cost,$durationInMillis,$input,
$lastAppResponse,$errorMessage){
    $stmt = $pdo->prepare('INSERT INTO ussdnotifications(date_,sessionId,
    serviceCode,networkCode,phoneNumber,status,cost,durationInMillis,input,
    lastAppResponse,errorMessage) values(?,?,?,?,?,?,?,?,?,?,?)');
    $stmt->execute([]);
    //Ending Connection
    $stmt = null;
}

?>