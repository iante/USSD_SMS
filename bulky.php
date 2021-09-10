<?php 
//Sending Bulky SMS
include_once 'sms.php';

$sms = new Sms("+25487");
$msg = "Dear Customer, We have reduced Our transaction costs by 50%";
$recepients = $sms->fetchRecepients();
$response = $sms->sendSMS($msg,$recepients);
//Coverting data to json format
echo json_encode($response);

?>