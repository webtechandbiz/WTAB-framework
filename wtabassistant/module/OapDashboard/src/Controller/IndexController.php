<?php

namespace OapDashboard\Controller;

class IndexController extends \page{

    public function indexAction() {
        $__db_mng = $this->_get_application_configs()['db_mng'];
        $_data = array('test' => $this->_get_application_configs()['APPLICATION_ROOT']);

        return new \ViewModel(array(
            'data' => $_data,
//            '_get_error_log_files_from_website' => $this->get_error_log_files_from_website($this->_get_application_configs()['APPLICATION_ROOT'])
            '_get_choosen_error_log_files' => $this->get_choosen_error_log_files($this->_get_application_configs()['APPLICATION_ROOT']),
            '_get_log_files' => $this->get_choosen_error_log_files($this->_get_application_configs()['APPLICATION_ROOT'])
        ));
    }
    
    private function get_choosen_error_log_files($dir, &$results = array()) {
        global $talkwithwp;
        $files = scandir($dir);
        foreach ($files as $k => $v) {
            $path = realpath($dir . '/' . $v);
            if ( is_dir($path) && $v != '.' && $v != '..'){
                $this->get_choosen_error_log_files($path, $results);
            }

            $_field_ErrorFileName_slugs = explode('|', 'error_log');//$talkwithwp->get_option('choosenfile'));
//            var_dump($_field_ErrorFileName_slugs);
            foreach ($_field_ErrorFileName_slugs as $__field_ErrorFileName_slug){
                $_field_ErrorFileName_slugs = explode(',', 'error_log');
                foreach ($_field_ErrorFileName_slugs as $__field_ErrorFileName_slug){
                    $__field_ErrorFileName_slug = trim($__field_ErrorFileName_slug);
                    $pos = strpos($v, $__field_ErrorFileName_slug);
                    if ($pos === 0) {
                        $results[] = $path;
                    }
                }
                foreach ($results as $result){
                    if ($__field_ErrorFileName_slug === $path) {
                        $results[] = $path;
                    }
                }
            }
        }

        return $results;
    }
    private function _get_log_files($dir, &$results = array()) {
        global $talkwithwp;
        $files = scandir($dir);
        foreach ($files as $k => $v) {
            $path = realpath($dir . '/' . $v);
            if ( is_dir($path) && $v != '.' && $v != '..'){
                $this->get_choosen_error_log_files($path, $results);
            }

            $_field_ErrorFileName_slugs = explode('|', 'error_log');//$talkwithwp->get_option('choosenfile'));
//            var_dump($_field_ErrorFileName_slugs);
            foreach ($_field_ErrorFileName_slugs as $__field_ErrorFileName_slug){
                $_field_ErrorFileName_slugs = explode(',', 'error_log');
                foreach ($_field_ErrorFileName_slugs as $__field_ErrorFileName_slug){
                    $__field_ErrorFileName_slug = trim($__field_ErrorFileName_slug);
                    $pos = strpos($v, $__field_ErrorFileName_slug);
                    if ($pos === 0) {
                        $results[] = $path;
                    }
                }
                foreach ($results as $result){
                    if ($__field_ErrorFileName_slug === $path) {
                        $results[] = $path;
                    }
                }
            }
        }

        return $results;
    }
//    private function get_error_log_files_from_website($dir, &$results = array()) {
//        $files = scandir($dir);
//        $_field_ErrorFileName_slug = 'error';
////        var_dump($files);
//        foreach ($files as $k => $v) {
//            $path = realpath($dir . '/' . $v);
//            if ( is_dir($path) && $v != '.' && $v != '..'){
//                $this->get_error_log_files_from_website($path, $results);
//            }
//
//            $_field_ErrorFileName_slugs = explode(',', $_field_ErrorFileName_slug);
//            foreach ($_field_ErrorFileName_slugs as $__field_ErrorFileName_slug){
//                $__field_ErrorFileName_slug = trim($__field_ErrorFileName_slug);
//                $pos = strpos($v, $__field_ErrorFileName_slug);
//                if ($pos === 0) {
//                    $results[] = $path;
//                }
//            }
//        }
//
//        return $results;
//    }
}