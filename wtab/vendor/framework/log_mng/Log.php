<?php

class Log{
    private $application_configs;
    
    public function __construct($application_configs) {
        $this->application_configs = $application_configs;
    }

    public function _log($data = false, $whichlog = false){
        switch ($whichlog) {
            case 'message':
                $logfilename = 'message';
                $dir = $this->application_configs['APPLICATION_LOGS_MESSAGES'];
                break;
            case 'exception':
                $logfilename = 'exception';
                $dir = $this->application_configs['APPLICATION_LOGS_EXCEPTIONS'];
                break;

            default:
                $logfilename = 'other';
                $dir = $this->application_configs['APPLICATION_LOGS_OTHER'];
                break;
        }

        $_date = new \DateTime();
        if(file_exists($dir)){
            file_put_contents($dir.$_date->format('Ymd-His').'-'.$logfilename, $data);
        }else{
            die(print_r($data, true));
        }
        
    }
}