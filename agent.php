<?php 

class Agent{
protected $name;
protected $number;

function __construct($number){
    $this->number = $number;
}

public function setName($name){
    $this->name = $name;
}

public function getName(){
    return $this->name;
}

public function getNumber(){
    return $this->number;
}

public function readNameByNumber($pdo){
    //return agent name if found, if not returns false
    $stmt = $pdo->prepare("SELECT name from agent WHERE agentNumber=?");
    $stmt->execute([$this->getNumber()]);
    $row = $stmt->fetch();

    if($row != null) {
        return  $row['name'];
    }else{
        return false;
    }
    
}


public function readIdByNumber($pdo){
    //return agent name if found, if not returns false
    $stmt = $pdo->prepare("SELECT aid from agent WHERE agentNumber=?");
    $stmt->execute([$this->getNumber()]);
    $row = $stmt->fetch();

    if($row != null) {
        return  $row['aid'];
    }else{
        return false;
    }
    
}
}

?>