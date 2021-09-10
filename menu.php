<?php
include_once 'util.php';
include_once 'user.php';
include_once 'transactions.php';
include_once 'agent.php';
include_once 'sms.php';

  class Menu{
    protected $text;
    protected $sessionId;

    function __construct(){
         
    }

    public function mainMenuRegistered($name){
        $response = "Welcome " . $name ."Reply with \n";
        $response .= "1. Send Money \n";
        $response .= "2. Withdraw Money \n";
        $response .= "3. Check Balance \n";
        //returning a string as value
        return $response;

  }

  public function mainMenuUnRegistered(){
    $response = "CON  Welcome to H&S Group Microfinance. Reply with \n";
    $response .= "1. Register \n";
    echo $response;
  }

public function registerMenu($textArray,$phoneNumber,$pdo){
        //Counting the number of elements in the exploded array
        $level = count($textArray);
        if($level == 1){
          echo "CON Please Enter Full Names";
        }else if($level == 2){
          echo "CON Set Your Pin";
        }else if($level == 3){
          echo "CON Confirm Your Pin";
        }else if($level == 4){
          //IF user has entered all requested details
          //Getting the specific details & storing in variables
          $name = $textArray[1];
          $pin = $textArray[2];
          $confirmedPin = $textArray[3];
          //Checking if set & confirmed pins match
          if($pin != $confirmedPin){
            echo "END PIN does not match. Please try again";
          }
        }else{
              
          //logic for registering user
          $user = new User($phoneNumber);
          //Setting the name, pin and balance values in the setters
          $user->setName($name);
          $user->setPin($pin);
          $user->setBalance(Util::$USER_BALANCE);

          //Calling the register function in user class to handle connection to db
          $user->register($pdo);
          //logic for sending sms to user after registration
          echo"END You have been registered successfully";

        }
}

public function sendMoneyMenu($textArray,$sender,$pdo,$sessionId){

$receiver = null;
$nameOfReceiver = null;
$response = "";
        
  $level = count($textArray);
  
      if($level == 1){
     echo "CON Enter Mobile Number";
    
  }else if($level == 2){
    echo "CON Enter Amount";
  }

  else if($level == 3){
    echo "CON Enter PIN";
  }
  else if($level == 4){

    $receiverMobile = $textArray[1];
    $receiverMobileWithCountryCode = addCountryCodeToPhoneNumber($receiverMobile);
    $receiver = new User($receiverMobileWithCountryCode);
    $nameOfReceiver = $receiver->readName($pdo);
    $response .= "Confirm send amount".$textArray[2] ."to" . $nameOfReceiver . "-". $receiverMobile."\n";
    $response.="1. Confirm\n";
    $response .= "2. Cancel\n";

    //Calling a static variable belonging to the Util Class in util.php
    $response .= Util ::$GO_BACK . "Back\n";
    $response .= Util :: $GO_TO_MAIN_MENU."Back to main menu\n";
    echo "CON".$response;
  }else if($level == 5 && $textArray[4] == 1){
    //User is Confirming sending money transaction
    //Have to check if there are sufficient funds including for transaction costs
    //Check if PIN is correct
    //Sending Money implementation logic

    $pin = $textArray[3];
    $amount = $textArray[2];
    $ttype = "send";

    //setting the pin
    $sender->setPin($pin);

    //getting balance of sender
   $newSenderBalance = $sender->checkBalance($pdo) - $amount - Util::$TRANSACTION_COST;
   $receiver = new User($this->addCountryCodeToPhoneNumber($textArray[1]));
   

   // Confirming if pin entered is the same with that in db
   if($sender->correctPin($pdo) == false){
      echo "END The PIN Entered is incorrect";

      //Logic for sending SMS If PIN is Incorrect
   }else{

    //If Pin is correct
    $txn = new Transaction($amount,$ttype);
    $result = $txn->sendMoney($pdo,$sender->readUserId($pdo),$receiver->readUserId($pdo),$newSenderBalance,$newReceiverBalance);
    if($result == true){
      echo "END Your request is being processed. You will receive an SMS Shortly";
    }else{
      "CON".$result;
    }
  
  }
    $newReceiverBalance = $receiver->checkBalance($pdo) + $amount;
    echo "END Your request is being processed";
  }else if($level == 5 && $textArray[4]==2){
    //Cancelling Transaction
    echo "END Thank You for using our services";

  }else if($level == 5 && $textArray[4]== Util :: $GO_BACK ){
    //Going back one step
    echo "END Going back one step";
    
  }

else if($level == 5 && $textArray[4]== Util :: $GO_TO_MAIN_MENU){
  //Going back to the menu menu
  echo "END Going back to the menu menu";
  
}else{
  echo "END Invalid Entry";
}
  

}

public function withdrawMoneyMenu($textArray,$pdo,$user){
   $level = count($textArray);
  if($level == 1){
    echo "CON Enter agent Number\n";
  }else if($level == 2){
    echo "CON Enter amount\n";
  }
  else if($level == 3){
    echo "CON Enter Your PIN\n";
   
  }else if($level == 4 ){

    //Checking if agent exists based on the agent number
    $agent = new Agent($textArray[1]);
    $agentName = $agent->readNameByNumber($pdo);
//User is Confirming withdrawal
$response= "CON Confirm Withdrawal of".$textArray[2]."from agent Number". $agentName."\n";
$response.="1. Accept"."";
$response.="2. Cancel";
echo $response;
    
    
  }else if($level==5 && $textArray[4] ==1){

    //confirming pin
    $pin = $textArray[3];
    $user-> setPin($pin);
    if($user->correctPin($pdo) == false){
      echo "END Invalid PIN";
      //send SMS Logic
      return;
    }

    //Checking if user has sufficient balance
    if($user->checkBalance($pdo) < $textArray[2] + Util :: $TRANSACTION_COST){
      echo "END Insufficient Funds";
      return;
    }

    //Processing the withdrawal 
    $agent = new Agent($textArray[1]);
    $agentName = $agent->readNameByNumber($pdo);
    $newBalance = $user->checkBalance($pdo) - $textArray[2] - Util :: $TRANSACTION_COST;
    $ttype = "Withdraw";
    $txn = new Transaction($textArray[2], $ttype);
    $result = $txn->withdrawCash($pdo,$user->readUserId($pdo),$agent->readIdByNumber($pdo),$newBalance);
    
    if($result == true){
      echo"END Your request is being processed. Thank you";
      //SMS LOGIC
    }else{
      "END".$result;
    }
    
  }
  else if($level==5 && $textArray[4] ==2){
    echo"END You have Cancelled the transaction";
  }else{
    echo"END Invalid entry. Try again";
  }
        
}

public function checkBalanceMenu($textArray,$user,$pdo){

        $level = count($testArray);
        if($level == 1){
          echo "CON Enter Pin";
        }
        else if($level == 2){
          //Logic
          //Confirming Pin details
          $pin = $testArray[1];
         $user->setPin($pin);
         if($user->correctPin($pdo) == true){
          $balance = $user->checkBalance($pdo);
         
          $msg =  "Your balance is" .$balance ."Thank You for choosing H&S Group Microfinance";
          $sms = new Sms($user->getPhone());
         $result =  $sms->sendSMS($msg);

         //Checking status of message
         if($result['status'] == "Success"){
          echo "END You will receive an SMS Shortly.";
         }else{
           echo "END aN ERROR Occurred. Please try again";
         }
         }else{
           
          echo "END Invalid PIN";
           //Sending SMS Logic
         }
          echo "END Thank you. You will receive an SMS shortly.";
        }else{
          echo"END Invalid entry";
        }
}

public function middleware($text,$user,$sessionId,$pdo){
  //Calls the goBack & goToMain function
  return $this->invalidEntry($this->goBack($this->goToMain($text)),$user,$sessionId,$pdo);
}

//Function for calling the go back and goback menu

public function goBack($text){
//Logic
  //goes back one step
  $explodedText = explode("*",$text);
  while(array_search(Util::$GO_BACK,$explodedText)!= false){
    $firstIndex = array_search(Util::$GO_BACK,$explodedText);
    array_splice($explodedText,$firstIndex-1,2);
  }
  return join("*",$explodedText);

}

public function goToMain($text){
  //Logic. Back to main menu
  $explodedText = explode("*",$text);
  while(array_search(Util :: $GO_TO_MAIN_MENU,$explodedText) != false){
    $firstIndex = array_search(Util :: $GO_TO_MAIN_MENU,$explodedText);
     //new exploded text will be
     $explodedText = array_slice($explodedText,$firstIndex +1);

     //Putting the array back to string
  }
  return join("*",$explodedText);
}

public function persistInvalidEntry($sessionId,$ussdLevel,$pdo,$user){
  //Creating a connection to db
  $stmt = $pdo->prepare("INSERT into ussdsession(sessionId,ussdLevel) value(?,?)");
  $stmt->execute([$sessionId,$ussdLevel]);
  //End connection
  $stmt = null;
}

//ussdStr contains all options entered by user
//User can enter an invalid option more than twice
public function invalidEntry($ussdStr,$sessionId,$user,$pdo){
  $stmt = $pdo->prepare("SELECT ussdLevel from ussdsession where sessionId=?");
  $stmt->execute([$sessionId]);

  //Check if Ussd Str is 0
  //If 0, there is no invalid option
   //Return String

   //returns an associative array with all records of ussdlevel
   $result = $stmt->fetchAll();
   if(count($result)==0){
     return $ussdStr;
   }

   //Exploding the ussdStr and returning array

   $strArray = explode("*", $ussdStr);

   //Looping through the returned results from database
   //And removing every instance of invalid option entered by user
   //User can have more than one invalid option

   foreach($result as $value){
     //removing
     unset($strArray[$value['ussdLevel']]);

     //To array
     $strArray = array_values($strArray);
      
     //returning array back as string
     return join("*",$strArray);
   }
}

//Function for appending a country code to phone number
//Using substr in built function
//Ignoring character at index 0 of the array i.e the 0 in 0759569980
public function addCountryCodeToPhoneNumber($phone){
  return Util::$COUNTRY_CODE . substr($phone,1);
}
  }

?>


