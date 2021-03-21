<?php

class localization{
    private $application_config = null;

    public function getLocalization($language, $module, $controller, $action){
        $_language = array($this->application_config['localization']);
        return $_language[0][$language][$action];
    }
    public function __construct($application_config) {
        $this->application_config = $application_config;
    }
}