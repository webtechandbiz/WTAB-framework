<?php

class token{
    public function getToken(){
        global $application_configs;
        if(isset($application_configs) && isset($application_configs['token'])){
            $token = $application_configs['token'];
        }else{
            $token = md5(uniqid(rand(), TRUE));
            $application_configs['token'] = $token;
            if(!isset($token) && $token === ''){die('');}
        }
        return $token;
    }

    public function checkToken(){
        global $application_configs;
        if(isset($application_configs) && isset($application_configs['token'])){
            $token = $application_configs['token'];
        }else{
            $token = md5(uniqid(rand(), TRUE));
            $application_configs['token'] = $token;
            if(!isset($token) && $token === ''){die('');}
        }
        return $token;
    }
}