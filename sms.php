<?php 
require 'vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;
include_once 'util.php';
include_once 'db.php';

class Sms{
    protected $phone;
    protected $AT;

    function __construct($phone){
        //Setting the phone
        $this->phone = $phone;
        $this->AT = new AfricasTalking(Util::$API_USERNAME, Util::$API_KEY);
    }

    public function getPhone(){
        return $this->phone;
    }

    public function sendSMS($message,$recepients){
       //Accessing the sms function
       $sms = $this->AT->sms();

       //Using the sms

       $result = $sms->send([
           //Sending SMS to single recepient
        //'to'      => $this->getPhone(),
        'to' => $recepients,
        'message' => $message,
        //Branded / Shortcode SMS
       // 'from' => Util::$COMPANY_NAME,
        'from' => Util::$SMS_SHORTCODE,
       ]);

       return $result;
    }

    //Fething al, users phone numbers from MYSQL Database
    public function fetchRecepients(){
       $db = new DBConnector();
       $pdo = $db->connectToDB();
       $stmt = $pdo->preare("SELECT phone from ussd_user");
       $stmt->execute();

       $result = $stmt->fetchAll();

       //Creating a recepients array to hold all data from database
       $recepients = array();

     //Looping through the records and pushing each phone to recepients array
     foreach($result as $row){
        array_push($recepients,$row['phone']);
     }

     //Joining
     join(",",$recepients);
    }
}
?>