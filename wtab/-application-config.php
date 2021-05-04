<?php
ini_set('memory_limit', '-1');
ini_set("error_reporting", E_ALL);

include($application_configs['ROOT_PATH'].'-change-here.php');

$application_configs['APPLICATION_PROTOCOL'] = 'https://';
$application_configs['APPLICATION_DOMAIN_PROTOCOL'] = $application_configs['APPLICATION_PROTOCOL'].$application_configs['APPLICATION_DOMAIN'].'/';
$application_configs['ENABLE_HTTP'] = false;

$application_configs['APPLICATION_ROOT'] = $application_configs['ROOT_PATH'];

$application_configs['LIB'] = 'libs/';
$application_configs['FRAMEWORK_FOLDER'] = $application_configs['APPLICATION_ROOT'].'vendor/framework/';
$application_configs['LIBS_FOLDER'] = $application_configs['APPLICATION_ROOT'].'vendor/'.$application_configs['LIB'];

$application_configs['PUBLIC_FOLDER'] = $application_configs['APPLICATION_ROOT'].'public/';
$application_configs['PRIVATE_FOLDER_MODULE'] = $application_configs['APPLICATION_ROOT'].'module/';

$application_configs['APPLICATION_URL'] = $application_configs['APPLICATION_DOMAIN_PROTOCOL'].$application_configs['APPLICATION_SLUG'].'/';
$application_configs['APPLICATION_HOME'] = $application_configs['APPLICATION_URL'].'dashboard/index/index';
$application_configs['PUBLIC_FOLDER_MEDIA_MODULES'] = $application_configs['APPLICATION_DOMAIN_PROTOCOL'].$application_configs['APPLICATION_SLUG'].'/'.$application_configs['PUBLIC_FOLDER'].'media/modules/';
$application_configs['APPLICATION_URL_LOGIN'] = $application_configs['APPLICATION_URL'].'login/login/index';

$application_configs['PUBLIC_FOLDER_MODULES'] = 'framework/';
$application_configs['language'] = 'IT';
$application_configs['date_default_timezone_set'] = 'Europe/Rome';

$application_configs['localization'] = array(
    'IT' => array(
        'login' => 
            array(
                'empty' => 'Devi indicare nome utente e password',
                'email_or_password_error' => 'Credenziali errate'
            ),
        'default' => 
            array(
                'error-log-done' => 'Problema improvviso. Riprova più avanti e segnala all amministratore.',
                'error-log-fail' => 'Problema improvviso. Riprova più avanti e segnala all amministratore.',
                'not-logged' => 'clicca qui per accedere'
            )
    ),
    'EN' => array(
        'login' => 
            array(
                'empty' => 'Something is wrong',
                'email_or_password_error' => 'Something is wrong',
                'not-logged' => 'Click here to login'
            ),
        'default' => 
            array(
                'error-log-done' => 'Something is wrong',
                'error-log-fail' => 'Something is wrong'
            )
    )
);

//# Logs
$application_configs['WORDPRESS_URL__website'] = $application_configs['APPLICATION_ROOT'].'website.com/';
$application_configs['APPLICATION_LOGS_FOLDER'] = $application_configs['APPLICATION_ROOT'].'logs-'.$application_configs['APPLICATION_LOGS_FOLDER_PREFIX'].'/';
$application_configs['APPLICATION_LOGS_DB'] = $application_configs['APPLICATION_LOGS_FOLDER'].'db/';
$application_configs['APPLICATION_LOGS_EXCEPTIONS'] = $application_configs['APPLICATION_LOGS_FOLDER'].'exceptions/';
$application_configs['APPLICATION_LOGS_MESSAGES'] = $application_configs['APPLICATION_LOGS_FOLDER'].'messages/';
$application_configs['APPLICATION_LOGS_OTHER'] = $application_configs['APPLICATION_LOGS_FOLDER'].'other/';

//# Data
$application_configs['APPLICATION_DATA_FOLDER'] = $application_configs['APPLICATION_ROOT'].'data/';

//#TODO
$application_configs['OPEN_MODULES'] = array(
    'public', 'login', 'logout'
);
