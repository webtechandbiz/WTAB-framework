<?php
class DbMng {

    private $db_details;
    private $file_details;
    
    public function __construct($db_details = false, $file_details = false) {
        $this->db_details = $db_details;
        $this->file_details = $file_details;
    }

    public function saveDataOnTable($selectedTable, $inputValues, $_dbType, $_insert_update = 0, $_encryption = null){
        global $_app_path;
        switch ($_dbType) {
            case 'db':
                if(!$this->db_details){return false;}
                try {
                    $newId = 0;
                    if(!is_array($inputValues)){return 0;}

                    $db = $this->getDB();
                    $db->exec("SET NAMES 'utf8';");
                    if(intval($_insert_update) === 0){
                        $_insert_into__ary = $this->_insert_into($selectedTable, $inputValues, $_encryption);
                        $query = $_insert_into__ary['query'];
                        $execute_ary = $_insert_into__ary['execute_ary'];
                    }else{
                        $_update__ary = $this->_update($selectedTable, $inputValues);
                        $query = $_update__ary['query'];
                        $execute_ary = $_update__ary['execute_ary'];
                    }

                    $stmt = $db->prepare($query);
                    $stmt->execute($execute_ary);

                    if($_insert_update === 0){
                        $newId = $db->lastInsertId();
                    }else{
                        $newId = $stmt->rowCount();
                    }
                    $db = null;
                } catch(PDOException $pdoE) {
                    error_log('saveDataOnTable-ExceptionPDOE:'.print_r($pdoE->getMessage(), true).PHP_EOL, 3, $_app_path."/logs/db-exception.log");
                    echo $pdoE->getMessage();
                } catch (Exception $e) {
                    error_log('saveDataOnTable-ExceptionPDOE:'.print_r($e->getMessage(), true).PHP_EOL, 3, $_app_path."/logs/db-exception.log");
                    echo $e->getMessage();
                }
                return $newId;

            case 'file':
                
                break;
            default:
                break;
        }
    }

    public function getDataByWhere($selectedTable, $selectValues, $whereValues){
        $selectValuesLenght = sizeof($selectValues);
        $execute_ary = array();
        $i = 1;

        if(!is_array($selectValues)){return 0;}
        if(!$this->db_details){return false;}

        $response_columns = null;

        $db = $this->getDB();

        $select = "SELECT ";

        foreach ($selectValues as $v){
            if($v !== NULL){
                $field = $v;
                if($i < $selectValuesLenght){
                    $select .= '`'.$field.'`, ';
                }else{
                    $select .= '`'.$field.'`';
                }
            }
            $i++;
        }
        $select .= ' FROM `'.$selectedTable.'` ';

        if(count($whereValues) === 1){
            foreach ($whereValues as $v){
                if($v['where_value'] !== NULL){
                    $where_field = $v['where_field'];
                    $select .= ' WHERE `'.$where_field.'` = :'.$where_field;
                }
                if($v['where_value'] !== NULL){
                    $execute_ary[':'.$v['where_field']] = $v['where_value'];
                }
            }
        }

        if(count($whereValues) > 1){
            if($whereValues[0]['where_value'] !== NULL){
                $select .= ' WHERE 1= 1 ';
            }
            foreach ($whereValues as $v){
                if($v['where_value'] !== NULL){
                    $where_field = $v['where_field'];
                    $select .= ' AND `'.$where_field.'` = :'.$where_field;
                }
                if($v['where_value'] !== NULL){
                    $execute_ary[':'.$v['where_field']] = $v['where_value'];
                }
            }
        }

        $query = $select;
        $stmt = $db->prepare($query);
        $stmt->execute($execute_ary);

        $row_count = $stmt->rowCount();
        if ($row_count > 0){
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($data as $row){
                $response[] = $row;
            }

            foreach($data as $row){
                $response_columns[] = $row;
            }
        }else{
            $response = 'no-rows';
        }

        $db = null;

        return array('response_columns' => $response_columns, 'response'=>$response);
    }

    public function getDataByQuery($query, $_dbType){
        global $_app_path;
        switch ($_dbType) {
            case 'db':
                if(!$this->db_details){return false;}

                try {
                    $db = $this->getDB();
                    $db->exec("SET NAMES 'utf8';");
                    $stmt = $db->prepare($query);

                    $stmt->execute();
            
                    $row_count = $stmt->rowCount();
                    if ($row_count > 0){
                        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach($data as $row){
                            $response[] = $row;
                        }

                        foreach($data as $row){
                            $response_columns = $row;
                        }
                    }else{
                        $response = 'no-rows';
                    }
                    $db = null;

                } catch(PDOException $pdoE) {
                    error_log('saveDataOnTable-ExceptionPDOE:'.print_r($pdoE->getMessage(), true).'|', 3, $_app_path."/logs/db-exception.log");
                }
                if(!isset($response_columns)){
                    $response_columns = array();
                }
                if(!isset($response)){
                    $response = array();
                }
                return array('response_columns' => $response_columns, 'response'=>$response);

            case 'file':
                
                break;
            default:
                break;
        }
    }
    
    public function delete($selectedTable, $id_name, $id_value){
        $db = $this->getDB();
        $db->exec("SET NAMES 'utf8';");
        
        $query = 'DELETE FROM `'.$selectedTable.'` WHERE `'.$selectedTable.'`.`'.$id_name.'` = '.$id_value;
        $stmt = $db->prepare($query);
        return $stmt->execute();
    }
    
    private function _insert_into($selectedTable, $inputValues, $_encryption){
        $insert_into = "INSERT INTO `".$selectedTable."` ";
        $insert_into .= '(';
        $inputValuesLenght = sizeof($inputValues);
        $execute_ary = array();
        $i = 1;
        foreach ($inputValues as $v){
            if(!isset($v['typed_value'])){
//                var_dump($v);
            }
            if($v['typed_value'] === NULL){
                $inputValuesLenght--;
            }
        }
        foreach ($inputValues as $v){
            $field = $v['field'];
            if($v['typed_value'] !== NULL){
                if($i < $inputValuesLenght){
                    $insert_into .= '`'.$field.'`, ';
                }else{
                    $insert_into .= '`'.$field.'`';
                }
            }
            $i++;
        }
        $insert_into .= ') VALUES';

        $insert_into .= '(';
        $i = 1;
        foreach ($inputValues as $v){
            $field = $v['field'];
            if($v['typed_value'] !== NULL){
                //#- encryption Begin
                if($_encryption && isset($_encryption['object']) && is_array($_encryption['fields_to_encrypt'])){
                    $__encryption = $_encryption['object'];
                    if(in_array($field, $_encryption['fields_to_encrypt'])){
                        $execute_ary[$field] = $__encryption->encrypt($v['typed_value']);
                    }else{
                        $execute_ary[$field] = $v['typed_value'];
                    }
                }else{
                    $execute_ary[$field] = $v['typed_value'];
                }
                //#- encryption End
                if($i < $inputValuesLenght){
                    $insert_into .= ':'.$field.', ';
                }else{
                    $insert_into .= ':'.$field;
                }
            }
            $i++;
        }
        $insert_into .= ') ';

        return array('query' => $insert_into, 'execute_ary' => $execute_ary);
    }

    private function _update($selectedTable, $inputValues){
        $update = "UPDATE `".$selectedTable."` ";
        $update .= 'SET ';
        $inputValuesLenght = sizeof($inputValues);
        $execute_ary = array();
        $i = 1;
        foreach ($inputValues as $v){
            if(!isset($v['typed_value'])){
                $inputValuesLenght--;
            }
        }
        foreach ($inputValues as $v){
            if(isset($v['field'])){
                $field = $v['field'];
                if($v['typed_value'] !== NULL){
                    if($i < $inputValuesLenght){
                        $update .= '`'.$field.'` = :'.$field.', ';
                    }else{
                        $update .= '`'.$field.'` = :'.$field;
                    }
                }
            }
            if(isset($v['where_value'])){
                $where_field = $v['where_field'];
                $update .= ' WHERE `'.$where_field.'` = :'.$where_field;
            }
            $i++;
        }
        $i = 1;
        foreach ($inputValues as $v){
            if(isset($v['field'])){
                $field = $v['field'];
                if(isset($v['typed_value'])){
                    //#- encryption Begin
                    if($_encryption && isset($_encryption['object']) && is_array($_encryption['fields_to_encrypt'])){
                        $__encryption = $_encryption['object'];
                        if(in_array($field, $_encryption['fields_to_encrypt'])){
                            $execute_ary[$field] = $__encryption->encrypt($v['typed_value']);
                        }else{
                            $execute_ary[$field] = $v['typed_value'];
                        }
                    }else{
                        $execute_ary[$field] = $v['typed_value'];
                    }
                    //#- encryption End
                }
            }
            if(isset($v['where_value'])){
                if($v['where_value'] !== NULL && $v['where_value'] !== ''){
                    $execute_ary[$v['where_field']] = $v['where_value'];
                }else{
                    return false;
                }
            }
            $i++;
        }

        return array('query' => $update, 'execute_ary' => $execute_ary);
    }
    
    public function getDB(){
        $db_host = $this->db_details['Nrqtx0HHsX'];
        $db_name = $this->db_details['VxMO8N5kX4'];
        $db_user = $this->db_details['qsPV6EwtzA'];
        $db_psw = $this->db_details['AQowahicz5'];
        $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_psw);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}