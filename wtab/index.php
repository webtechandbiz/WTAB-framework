<?php 
session_start();
$get = $_GET;
$post = $_POST;

global $application_configs;
global $optional_parameters_values;

global $mail;
$application_configs = array();
$application_configs['ROOT_PATH'] = __DIR__.'/';

include(__DIR__.'/-application-config.php');
$application_configs['session'] = $_SESSION;
date_default_timezone_set($application_configs['date_default_timezone_set']);

//# FRAMEWORK_FOLDER
include($application_configs['FRAMEWORK_FOLDER'].'DbMng/_______db_mng.php');
include($application_configs['FRAMEWORK_FOLDER'].'navigation/Navigation.php');
include($application_configs['FRAMEWORK_FOLDER'].'PageMng/page.php');
include($application_configs['FRAMEWORK_FOLDER'].'PageMng/ViewModel.php');
include($application_configs['FRAMEWORK_FOLDER'].'PageMng/JsonModel.php');
include($application_configs['FRAMEWORK_FOLDER'].'user/currentuser.php');
include($application_configs['FRAMEWORK_FOLDER'].'user/params.php');
include($application_configs['FRAMEWORK_FOLDER'].'user/headscript.php');
include($application_configs['FRAMEWORK_FOLDER'].'user/headlink.php');
include($application_configs['FRAMEWORK_FOLDER'].'Encryption/encryption.php');
include($application_configs['FRAMEWORK_FOLDER'].'errors_mng/errors_mng.php');
include($application_configs['FRAMEWORK_FOLDER'].'user/User.php');
include($application_configs['FRAMEWORK_FOLDER'].'user/UserBean.php');
include($application_configs['FRAMEWORK_FOLDER'].'user/login.php');
include($application_configs['FRAMEWORK_FOLDER'].'token/token.php');
include($application_configs['FRAMEWORK_FOLDER'].'https_redirect/https_redirect.php');
include($application_configs['FRAMEWORK_FOLDER'].'localization/localization.php');
include($application_configs['FRAMEWORK_FOLDER'].'inputchecker/InputChecker.php');
include($application_configs['FRAMEWORK_FOLDER'].'imageprocess/ImageProcess.php');
include($application_configs['FRAMEWORK_FOLDER'].'convert/Convert.php');
include($application_configs['FRAMEWORK_FOLDER'].'log_mng/Log.php');

$application_configs['LOGMNG'] = new Log($application_configs);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
if($application_configs['ENABLE_EMAIL_SERVICE']){
    include($application_configs['LIBS_FOLDER'].'/PHPMailer/src/PHPMailer.php');
    include($application_configs['LIBS_FOLDER'].'/PHPMailer/src/SMTP.php');
    $mail = new PHPMailer();
}

$application_configs['db_mng'] = new DbMng($application_configs['db_details']);
$navigation = new Navigation();

if(isset($get['q'])){
    $parameters = explode('/', $get['q']);
    $optional_parameters = $navigation->_get_optional_parameters($parameters);
 
    $optional_parameters_count = sizeof($optional_parameters);
    $action = '-';
    if($optional_parameters_count == 0){
        $module = $navigation->_is_not_set_return_index($parameters[$optional_parameters_count - 0 + $optional_parameters_count]);
        if(isset($parameters[$optional_parameters_count - 1 + $optional_parameters_count])){
            $controller = $navigation->_is_not_set_return_index($parameters[$optional_parameters_count - 1 + $optional_parameters_count]);
            $action = $navigation->_is_not_set_return_index($parameters[$optional_parameters_count - 1 + $optional_parameters_count]);
        }else{
            $controller = 'index';
            $action = 'index';
        }
    }
    if($optional_parameters_count > 0){
        $module = $parameters[0];
        if(isset($parameters[$optional_parameters_count - 0 + $optional_parameters_count])){
            $controller = $navigation->_is_not_set_return_index($parameters[$optional_parameters_count - 0 + $optional_parameters_count]);
            $action = $navigation->_is_not_set_return_index($parameters[$optional_parameters_count - 0 + $optional_parameters_count]);
        }else{
            $controller = 'index';
            $action = 'index';
        }
    }

    if($action !== '-'){
        $user = new User($application_configs);
        $user->ifNotLoggedThenLogin($application_configs['session'], $module);

        $inputchecker = new InputChecker();
        $checkParameters = $inputchecker->checkParameters($application_configs, $module, $controller, $action, $post);
        if(!$checkParameters){die();}

        $class_name = &$controller;

        $_modules = scandir($application_configs['PRIVATE_FOLDER_MODULE']);

        foreach ($_modules as $modulename){
            if($modulename != '.' && $modulename != '..'){
                require_once ($application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/src/Module.php');

                $getConfig = $navigation->getRoutes($application_configs, $modulename);
                $_routes = $getConfig['router']['routes'];
                if(isset($_routes)){
                    $result = preg_replace('/\B([A-Z])/', '-$1', $modulename);
                    $_view_folder = strtolower($result);

                    foreach ($_routes as $route => $values){
                        if($route == $module){
                            $__routes = explode('[/', $route);
                            $_parameters_count = sizeof($__routes);

                            if($_parameters_count > 0){
                                $action = 'index';
                                $_route = $values['options']['route'];
                                $__routes = explode('[/', $_route);

                                array_shift($__routes);
                                array_shift($__routes);
                                $_i_optional_param = 0;
                                foreach ($__routes as $_param){
                                    $_param = str_replace(':', '', $_param);
                                    $_param = str_replace(']', '', $_param);

                                    $optional_parameters_values[$_param] = $optional_parameters[$_i_optional_param];
                                    $_i_optional_param++;
                                }

                            }

                            $_controller = $values['options']['defaults']['controller'];
                            if(file_exists($application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/src/Controller/IndexController.php')){
                                include($application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/src/Controller/IndexController.php');

                                if(class_exists($_controller)){
                                    $page = $_page = new $_controller($application_configs);
                                    $_action = $values['options']['defaults']['action'];
                                    $__action = $_action.'Action';
                                    if($action == $_action && $modulename !== 'FrameworkLogin'){
                                        $_page->_include($modulename, $_view_folder, $_action, $__action);
                                        $_page->$__action();
                                    }
                                }else{
                                    echo '********** '.$_controller.' ************* ';
                                }
                            }else{
                                echo 'Not exists:'.$application_configs['PRIVATE_FOLDER_MODULE'].$modulename.'/src/Controller/IndexController.php<br>';
                            }
                        }else{
    //                        echo $route.'|'.$module.'<br>';
                        }
                    }
                }
            }
        }

    //    echo '$module:'.$module.'|$controller:'.$controller.'|$action:'.$action.'['.$class_name.']';

        if($module === 'login' && $controller === 'index'){
            if($action == 'index'){
                include($application_configs['PRIVATE_FOLDER_MODULE'].'/FrameworkLogin/src/Controller/IndexController.php');
                if(file_exists($application_configs['PRIVATE_FOLDER_MODULE'].'/FrameworkLogin/src/Controller/IndexController.php')){
                    $_IndexController = $page = $_this = new FrameworkLogin\Controller\IndexController($application_configs);
                    $page_response = $_IndexController->indexAction();
                }
            }
        }
        if($module === 'login' && $controller === 'checklogin'){
            if($action == 'checklogin'){
                include($application_configs['PRIVATE_FOLDER_MODULE'].'/FrameworkLogin/src/Controller/IndexController.php');
                $_IndexController = $page = $_this = new FrameworkLogin\Controller\IndexController($application_configs);
                $page_response = $_IndexController->checkloginAction($post);
                $navigation->response($page_response->get()[$action]['response']);
            }
        }
        if($module === 'logout' && $controller === 'index'){
            if($action == 'index'){
                include($application_configs['PRIVATE_FOLDER_MODULE'].'/FrameworkLogin/src/Controller/IndexController.php');
                if(file_exists($application_configs['PRIVATE_FOLDER_MODULE'].'/FrameworkLogin/src/Controller/IndexController.php')){
                    $_IndexController = $page = $_this = new FrameworkLogin\Controller\IndexController($application_configs);
                    $page_response = $_IndexController->logoutAction();
                }
            }
        }
    }
}