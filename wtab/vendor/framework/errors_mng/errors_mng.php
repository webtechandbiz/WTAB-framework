<?php

class errors_mng extends page{
    public function getFilesToInclude($application_configs){
        $files_to_include = 
            array(
                
            )
        ;
        return $this->_getFilesToInclude($files_to_include);
    }

    public function getCss($application_configs){
        $css = array();
        return $this->_getCss($css);
    }
    
    public function getJs($application_configs){
        $js = array();
        return $this->_getJs($js);
    }

    public function getTitle(){
        return $this->_getTitle('editor');
    }
}

set_error_handler('myErrorHandler');

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {return false;}

    global $application_configs;
    $application_configs['LOGMNG']->_log($errno.'|'.$errstr.'|'.$errline.'|'.$errfile, 'exception');

    return true;
}