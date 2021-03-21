<?php

namespace FrameworkLogin\Controller;

class IndexController extends \page{

    public function indexAction() {
        global $application_configs;

        $_page_path = $application_configs['ROOT_PATH'].'/module/FrameworkLogin/view/framework-login/index/index.phtml';
        include($application_configs['ROOT_PATH'].'/module/FrameworkLogin/view/framework-login/index/login.phtml');

        return new \ViewModel(array(

        ));
    }
    
    public function checkloginAction($_post) {
        global $application_configs;
        $login = new \login($application_configs);
        $_checklogin = $login->checklogin($_post);

        return new \JsonModel(array(
            'checklogin' => $_checklogin
        ));
    }
    
    public function logoutAction() {
        global $application_configs;

        $_page_path = $application_configs['ROOT_PATH'].'/module/FrameworkLogin/view/framework-login/index/logout.phtml';
        include($application_configs['ROOT_PATH'].'/module/FrameworkLogin/view/framework-login/index/logout.phtml');

        return new \ViewModel(array(

        ));
    }
    

}