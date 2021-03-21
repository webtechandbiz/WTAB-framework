<?php

class currentuser{
    private $application_configs;

    public function getEmail() {
        if(isset($_SESSION['userbean'.$this->application_configs['SESSION_PREFIX']])){
            return unserialize($_SESSION['userbean'.$this->application_configs['SESSION_PREFIX']])->getEmailAndUser();
        }else{
            return '';
        }
    }

    public function __construct($application_configs) {
        $this->application_configs = $application_configs;
    }
}