<?php

include_once 'menu.php';
//Callback URL:  http://3bac4a8c2f47.ngrok.io/ussdsms/index.php
include_once 'db.php';
include_once 'user.php';


// Read the variables sent via POST from our API
$sessionId   = $_POST["sessionId"];
$serviceCode = $_POST["serviceCode"];
$phoneNumber = $_POST["phoneNumber"];
$text        = $_POST["text"];


//Creating object of user class
$user = new User($phoneNumber);
$sender = new User($phoneNumber);

//Creating object for connection handle
$db = new DBConnector();
$pdo = $db->connectToDB();

//Calling the goback and gotomainmenu functions via middleware
//Creating an object menu from the menu class
$menu =new Menu();

$text = $menu->middleware($text,$user,$sessionId,$pdo); 


if ($text == "" && $user->isUserRegistered($pdo)) {
   echo "CON" . $menu->mainMenuRegistered($user->readName($pdo));
    //Text is empty and user is registered
} else if ($text == "" && !$user->isUserRegistered($pdo)) {
    //Text is empty and user is not registered
    $menu->mainMenuUnRegistered();

} else if (!$user->isUserRegistered($pdo)) {
   //Text is not empty and user is unregistered
   //Separating the string divided by * in order to know option selected by user

   $textArray = explode("*",$text);

   //Getting the first element inthe array to konw users option
   switch($textArray[0]){
       case 1: $menu->registerMenu($textArray,$phoneNumber,$pdo);
       break;
       default:
       $ussdLevel = count($textArray) - 1;
       $menu->persistInvalidEntry($sessionId,$ussdLevel,$user,$pdo);
       echo "END Invalid Option. Please Try Again".$menu->registerMenu($textArray,$phoneNumber,$pdo);
   }


} else{ 
    //Text is not empty and user is registered
    $textArray = explode("*",$text);
    switch($textArray[0]){
        case 1:$menu->sendMoneyMenu($textArray,$user,$pdo,$sessionId);
        break;
        case 2:$menu->withdrawMoneyMenu($textArray,$pdo,$user);
        break;
        case 3:$menu->checkBalanceMenu($textArray,$user,$pdo);
        break;
        default:

        //Getting array index of the invalid option selected by user
        //And removing it from responses

        $ussdLevel = count($textArray) - 1;
        //calling function persistInvalidEntry
        $menu->persistInvalidEntry($sessionId,$ussdLevel,$pdo,$user);
        echo "CON Invalid Option. Please Try Again\n" . $menu->mainMenuRegistered($user->readName($pdo));
    }

}

// Echo the response back to the API

?>