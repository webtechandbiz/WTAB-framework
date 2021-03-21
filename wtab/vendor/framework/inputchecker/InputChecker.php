<?php

class InputChecker {
    public function checkParameters($application_configs, $module, $controller, $action, $post){
        if(!in_array($module, $application_configs['OPEN_MODULES'])){
            $position = $module.'/'.$controller.'/'.$action;
            $getParametersWhitelist = $this->getParametersWhitelist($application_configs, $position);
            $post_keys = array_keys($post);

            if($getParametersWhitelist){
                if($this->checkIfThePostKeysAreEqual($post_keys, $getParametersWhitelist)){
                    return true;
                }else{
                    $localization = $this->getLocalization($application_configs, $module, $controller, 'default');
                    die('A-<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
                }
            }else{
                $localization = $this->getLocalization($application_configs, $module, $controller, 'default');
                die($position.'|<a href="'.$application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['error-log-done'].'</a>');
            }
        }else{
            return true;
        }
    }

    private function getParametersWhitelist($application_configs, $position){
        $parameters_whitelist = $application_configs['parameters_whitelist'];
        if(isset($parameters_whitelist[$position])){
            return $parameters_whitelist[$position];
        }else{
            return false;
        }
    }
    
    private function checkIfThePostKeysAreEqual($post_keys, $whitelist_keys){
        if($whitelist_keys === 'no-parameters'){return true;}
        if($post_keys === $whitelist_keys){return true;}else{return false;}
    }

    private function getLocalization($application_configs, $module, $controller, $action){
        $localization = new localization($application_configs);
        return $localization->getLocalization($application_configs['language'], $module, $controller, $action);
    }
}