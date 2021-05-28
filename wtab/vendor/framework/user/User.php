<?php

class User {    
    private $application_configs;
    private $db_mng;
    
    public function __construct($application_configs = false, $db_mng = false) {
        $this->application_configs = $application_configs;
        $this->db_mng = $db_mng;
    }

    public function login($username, $password, $_loginType){
        switch ($_loginType) {
            case 'db':
                $db = $this->db_mng->getDB();

                $stmt = $db->prepare(
                    "SELECT * FROM `".$this->application_configs['TBL_PREFIX']."__users` ".
                    "WHERE ".$this->application_configs['TBL_FIELD_PREFIX']."_email_and_user = :email_and_user ". 
                    "AND ".$this->application_configs['TBL_FIELD_PREFIX']."_psw = :password"
                );
                $stmt->execute(array(':email_and_user' => $username, ':password' => $password));
                $row_count = $stmt->rowCount();

                if ($row_count > 0){
                    while ($row = $stmt->fetchObject()) {
                        $userbean = new UserBean($this->application_configs);
                        $userbean->setId($row->id_user);
                        $_email_and_user_field = $this->application_configs['TBL_PREFIX'].'_email_and_user';
                        $_usertype_id = $this->application_configs['TBL_PREFIX'].'_usertype_id';
                        $userbean->setEmailAndUser($row->$_email_and_user_field);
                        $userbean->setIdUserType($row->$_usertype_id);

                        $response = $userbean;
                    }
                }else{
                    $response = 'login-error';
                }

                $db = null;
                return $response;

            case 'file':
                
                break;
            default:
                break;
        }
    }
    
    public function getLoggedUserOrFalse($session){
        if(!unserialize($session['userbean'.$this->application_configs['SESSION_PREFIX']])){
            return $session['userbean'.$this->application_configs['SESSION_PREFIX']];
        }
        if(isset($session['userbean'.$this->application_configs['SESSION_PREFIX']]) && null !== $session['userbean'.$this->application_configs['SESSION_PREFIX']] && null !== unserialize($session['userbean'.$this->application_configs['SESSION_PREFIX']])->getEmailAndUser() && $session['userbean'.$this->application_configs['SESSION_PREFIX']] !== ''){
            return $session['userbean'.$this->application_configs['SESSION_PREFIX']];
        }else{
            return false;
        }
    }
    
    public function ifNotLoggedThenLogin($session, $module){
        if($module !== 'login' && !$this->getLoggedUserOrFalse($session)){
            $localization = $this->getLocalization($this->application_configs, '', '', 'default');
            die('<a href="'.$this->application_configs['APPLICATION_URL_LOGIN'].'">'.$localization['not-logged'].'</a>');
        }
    }

    private function getLocalization($application_configs, $module, $controller, $action){
        $localization = new localization($application_configs);
        return $localization->getLocalization($application_configs['language'], $module, $controller, $action);
    }
}
