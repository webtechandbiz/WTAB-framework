<?php
class login extends page{

    public function checklogin($post){
        global $application_configs;
        if(isset($post) && is_array($post)){
            try {
                if(isset($post['username']) && isset($post['password'])){
                    $username = $post['username'];
                    $password = md5($post['password']);

                    $user = new User($application_configs, $application_configs['db_mng']);
                    $login_response = $user->login($username, $password, 'db', $application_configs);
                    if($login_response !== 'login-error'){
                        if($login_response->getEmailAndUser() === $username){
                            $_SESSION['userbean-obj'] = $login_response;
                            $_SESSION['userbean'.$application_configs['SESSION_PREFIX']] = serialize($login_response);
                            $response = unserialize($_SESSION['userbean'.$application_configs['SESSION_PREFIX']])->getEmailAndUser();
                        }else{
                            $_SESSION['userbean'.$application_configs['SESSION_PREFIX']] = '';
                            $response = 'login-error';
                        }
                    }else{
                        $_SESSION['userbean'.$application_configs['SESSION_PREFIX']] = '';
                        $response = 'login-error';
                    }
                }else{
                    $response = 'empty';
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
                $response = 'getMessage';
            }
        }else{
            $response = 'no-token';
        }
        return array('type' => 'ws', 'response' => $response);
    }
}