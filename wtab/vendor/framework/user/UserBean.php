<?php

class UserBean {
    private $email_and_user = null;
    private $firstName = null;
    private $lastName = null;
    private $id_usertype = null;
    private $id = 0;

    public function getEmailAndUser(){
        return $this->email_and_user;
    }
    public function getFirstName(){
       return $this->firstName;
    }
    public function getLastName(){
       return $this->lastName;
    }
    public function getIdUserType(){
       return $this->id_usertype;
    }
    public function getId(){
       return $this->id;
    }
    
    public function setEmailAndUser($email_and_user){
        $this->email_and_user = $email_and_user;
    }
    public function setFirstName($firstName){
       $this->firstName = $firstName;
    }
    public function setLastName($lastName){
       $this->lastName = $lastName;
    }
    public function setIdUserType($id_usertype){
       $this->id_usertype = $id_usertype;
    }
    public function setId($id){
       $this->id = $id;
    }
}
