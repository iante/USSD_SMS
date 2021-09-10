<?php
class Transaction{
   
    protected $amount;
    protected $ttype;
    function __construct($amount,$ttype){
      
        $this->amount = $amount;
        $this->ttype = $ttype;
    }

    public function getAmount(){
        $this->amount;
    }

    public function getTType(){
        $this->ttype;
    }

    public function sendMoney($pdo,$uid,$ruid,$newSenderBalance,$newReceiverBalance){
       //setting Autocommit to false
       //Have to commit changes manually once executed
       $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,FALSE);
       try{
        
        $pdo->beginTransaction();
        $stmtT = $pdo->prepare("INSERT INTO transaction(amount,uid,ruid,ttype) values(?,?,?,?)");

        //Query for updating user balance when they perform a transaction
        $stmtU = $pdo->prepare("UPDATE user set balance=? WHERE uid=?");

        $stmtT->execute([$this->getAmount(),$uid,$ruid,$this->getTType()]);
        //You can execute a statement more than once
        $stmtU->execute([$newSenderBalance,$uid]);
        $stmtU->execute([$newSenderReceiver,$uid]);
        
        
        //Once queries have been executed
        //commit changes
        $pdo->commit();
        return true;

       }catch(Exception $e){
           //If there is an error, rollback
           $pdo->rollback();
           return "An error occured";
       }
    }

    public function withdrawCash($pdo,$uid,$aid,$newBalance){
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,FALSE);
       try{
        
        $pdo->beginTransaction();
        $stmtT = $pdo->prepare("INSERT INTO transaction(amount,uid,aid,ttype) values(?,?,?,?)");

        //Query for updating user balance when they perform a transaction
        $stmtU = $pdo->prepare("UPDATE user set balance=? WHERE uid=?");

        $stmtT->execute([$this->getAmount(),$uid,$aid,$this->getTType()]);
        $stmtU->execute([$newBalance,$uid]);
        
        
        
        //Once queries have been executed
        //commit changes
        $pdo->commit();
        return true;

       }catch(Exception $e){
           //If there is an error, rollback
           $pdo->rollback();
           return "An error occured";
       }
    }
}


?>

