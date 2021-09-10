<?php 
include_once 'sms.php';
class User{
    protected $name;
    protected $phone;
    protected $pin;
    protected $balance;
    //protected $name;
    
    //intializing constructor with phone nom since it will be used to identify user
    function __construct($phone){
    
        $this->phone=$phone;
    }
    
    //setters & getters
    public function setName($name){
       $this->name=$name;
    }

    public function getName(){
        return $this->name;
    }

    public function setPin($pin){
        $this->pin=$pin;
     }
 
     public function getpin(){
         return $this->pin;
     }

     public function setBalance($balance){
        $this->balance=$balance;
     }
 
     public function getBalance(){
         return $this->balance;
     }

    
     public function getPhone(){
         return $this->phone;
     }

     public function register($pdo){
      //Registration Process for user
      try{
        //Hashing pin entered by user
      $hashedPin = password_hash($this->getpin(),PASSWORD_DEFAULT);

      //Inserting users details in mysql DB after registration
      //Prepared statement
      $stmt = $pdo->prepare("INSERT INTO  ussd_user(name,pin,phone,balance) values(?,?,?,?)");
      $stmt->execute([$this->getName(),$hashedPin,$this->getPhone(),$this->getBalance()]);
       
      //sending sms logic
      $sms = new Sms($this->getPhone());
      $msg="Thank you for registering with H&S Group. Account has been created";
      $sms->sendSMS($msg,$this->getPhone());
      }catch(PDOException $e){
          echo $e->getMessage();
      }
     }

     public function isUserRegistered($pdo){
      //Checks if a user is registered
      $stmt = $pdo->prepare("SELECT * FROM ussd_user WHERE phone=?");
      $stmt->execute([$this->getPhone()]);
       //Checking count in the array if  > 0
       //If > 0, User exists.
       if(count($stmt->fetchAll()) > 0){
         return true;
       }else{
         return false;
       }
    }

    public function readName($pdo){
        //Reads the user from db
      $stmt = $pdo->prepare("SELECT * FROM  ussd_user WHERE phone=?");
      $stmt->execute([$this->getPhone()]);


      //Fetching name of already registered user from associative array
      $row = $stmt->fetch();
      return $row['name'];
      }

      public function readUserId($pdo){
        //Fething user Id from the database
        $stmt = $pdo->prepare("SELECT uid from ussd_user WHERE phone=?");
        $stmt->execute([$this->getPhone()]);
        //Getting the user ID FROM THE ASSOCIATIVE ARRAY
        $row = $stmt->fetch();
        return $row['uid'];
      }

      public function correctPin($pdo){
        //Checks if PIN Provided by user is correct or not
        $stmt = $pdo->prepare("SELECT pin FROM ussd_user WHERE phone=?");
        $stmt->execute([$this->getPhone()]);
        $row = $stmt->fetch();
        
        //Checking if PIN exists in DB
        if($row == null){
          return false;
        }

        //checking Pin to that in DB
        //getPin(); getter to get pin entered bu user
        if(password_verify($this->getPin(), $row['pin'])){
          return true;
        }

        return false;
      }

      public function checkBalance($pdo){
        //Checks The users balance from DB
        $stmt = $pdo->execute("SELECT balance FROM ussd_user WHERE phone=?");
        $stmt->execute([$this->getPhone()]);
        $row = $stmt->fetch();
        return $row['balance'];
      }
}

?>