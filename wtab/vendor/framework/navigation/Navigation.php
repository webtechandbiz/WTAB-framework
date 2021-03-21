<?php

class Navigation{
    function getRoutes($application_configs, $modulename){
        if(file_exists($application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/config/module.config.php')){
            return include $application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/config/module.config.php';
        }else{
            return null;
        }
    }
    function _is_not_set_return_index($parameter){
        if(!isset($parameter)){
            return 'index';
        }else{
            return $parameter;
        }
    }
    function _get_optional_parameters($parameters){
        $optional_parameters = array_slice($parameters,2);
        if(is_array($optional_parameters)){
            return $optional_parameters;
        }else{
            return false;
        }
    }
    function response($response){
        header("Content-Type: application/json");
        if($response !== ''){
            echo json_encode($response);
        }
        die();
    }
}