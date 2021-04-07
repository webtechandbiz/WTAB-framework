<?php

namespace OapDatamng\Controller;

class IndexController extends \page{

    public function indexAction() {
        $__db_mng = $this->_get_application_configs()['db_mng'];
        $_data = array('test' => $this->_get_application_configs()['APPLICATION_ROOT']);

        return new \ViewModel(array(
            'data' => $_data,
        ));
    }
    
    public function uploadAction(){
        if(isset($_FILES['file']['name']) && $_FILES['file']['name'] !== ''){
            $_path = $this->_get_application_configs()['APPLICATION_DATA_FOLDER'].'/datamng/uploaded/';
            if(is_writable($_path)){
                if ( 0 < $_FILES['file']['error'] ) {
                    die('upload-error');
                }else {
                    move_uploaded_file($_FILES['file']['tmp_name'], $_path.$this->_getRandomCode());
                }
            }
        }

        return new \JsonModel(array(
            'test' => array('type' => 'ws', 'response' => true)
        ));
    }
    
    public function getUploadedFileAction(){
        $_path = $this->_get_application_configs()['APPLICATION_DATA_FOLDER'].'/datamng/uploaded/';
        $_content = file_get_contents($_path.$this->_get_application_configs()['_post']['filename']);
        
        $_row_count = 0;
        $clmposition = 0;

        $_html = 'Add autoincrement? <input id="addautoincrement" type="checkbox" value="1"/>';
        
        $_html .= '<table>';
        $_ = explode(PHP_EOL, $_content);
        header("Content-Type: application/json");
        if($_ !== ''){
            foreach ($_ as $row){
                $clm = explode(',', $row);
                $_html .= '<tr>';
                if($_row_count === 0){
                    foreach ($clm as $clm){
                        $_html .= '<th data-clmposition="'.$clmposition.'">';
                            $_html .= '<span data-clm="'.$clm.'" data-clmposition="'.$clmposition.'" class="slc">select<br><select class="clmtype"><option value="1">varchar</option><option value="2">int</option></select><br><input type="text"/></span><br>';
                            $_html .= '<span data-clm="'.$clm.'" data-clmposition="'.$clmposition.'" class="key">key</span><br>';
                            $_html .= '<br>';
                        $_html .= ''.$clm.'</th>';
                        $clmposition++;
                    }
                }else{
                    foreach ($clm as $clm){
                        $_html .= '<td>'.$clm.'</td>';
                    }
                }
                $_html .= '</tr>';

                $_row_count++;
            }
            $_html .= '</table>';
            echo json_encode(array('content' => $_html));
        }
        die();
        return new \JsonModel(array(
            'content' => $_content
        ));
    }
    
    public function confirmuploadAction(){
        $__db_mng = $this->_get_application_configs()['db_mng'];
        $_currentFile = $this->_get_application_configs()['_post']['currentFile'];
        $_addautoincrement = $this->_get_application_configs()['_post']['addautoincrement'];
        if(isset($_addautoincrement) && intval($_addautoincrement) > 0){
            $_id_autoincrement = '`id` int(11) NOT NULL, ';
        }else{
            $_id_autoincrement = '';
        }
        $_selectedkey = $this->_get_application_configs()['_post']['selectedkey'];
        $_path = $this->_get_application_configs()['APPLICATION_DATA_FOLDER'].'/datamng/uploaded/'.$_currentFile;
        $_content = file_get_contents($_path);
        $_external_tables_values = $this->_createTable($_content, $_selectedkey, $__db_mng);
        
        $_selectedclm = $this->_get_application_configs()['_post']['selectedclm'];
        $_clmposition = $this->_get_application_configs()['_post']['clmposition'];
        $_clmlength = $this->_get_application_configs()['_post']['clmlength'];
        $_clmtype = $this->_get_application_configs()['_post']['clmtype'];

        //# TODO: create table in a method
        $_querycreatetable = 'CREATE TABLE '.$_currentFile.'( '.$_id_autoincrement;
        $i = 0;
        foreach ($_selectedclm as $_clm){
            if($i < sizeof($_selectedclm) -1){
                $_querycreatetable .= $_clm.' '.$this->_clmtype($_clmtype[$i]).'('.$_clmlength[$i].') NULL, ';
            }else{
                $_querycreatetable .= $_clm.' '.$this->_clmtype($_clmtype[$i]).'('.$_clmlength[$i].') NULL ';
            }
            $i++;
        }
        $_querycreatetable .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
        if(isset($_addautoincrement) && intval($_addautoincrement) > 0){
            $_querycreatetable .= 'ALTER TABLE `'.$_currentFile.'` ADD PRIMARY KEY (`id`);';

            $_querycreatetable .= 'ALTER TABLE `'.$_currentFile.'` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;';
        }

        $_createtable = $__db_mng->getDataByQuery($_querycreatetable, 'db');
        if($_createtable){
            $_row_count = 0;
            $_ = explode(PHP_EOL, $_content);
            header("Content-Type: application/json");
            if($_ !== '' && is_array($_)){
                foreach ($_ as $row){
                    $clm = explode(',', $row);
                    if($_row_count === 0){
                        foreach ($clm as $_clm){
                            $_columns[] = $_clm;
                        }
                    }else{
                        foreach ($_clmposition as $_pos){
                            if(isset($_columns[$_pos]) && isset($clm[$_pos])){
                                if(isset($_external_tables_values[strtolower($_columns[$_pos])])){

                                    foreach ($_external_tables_values[strtolower($_columns[$_pos])] as $_id => $_){
                                        if($_ == $clm[$_pos]){
                                            $_values[] = array('field' => $_columns[$_pos], 'typed_value' => $_id);
                                        }
                                    }
                                }else{
                                    $_values[] = array('field' => $_columns[$_pos], 'typed_value' => $clm[$_pos]);
                                }
                            }
                        }
                    }

                    if(isset($_values) && is_array($_values)){
                        $id = $__db_mng->saveDataOnTable($_currentFile, $_values, 'db');
                        $_values = null;
                    }
                    $_row_count++;
                }
            }


        }else{ //#"Error creating table: " . $__db_mng->getDB()->error;
            var_dump($__db_mng->getDB()->error);
        }


        //# TODO - solve the JsonModel() bug
        header("Content-Type: application/json");
        echo json_encode(array('querycreatetable' => $_querycreatetable));
        die();
        //#--

        return new \JsonModel(array(
//            'content' => $_content
        ));
    }


    public function getGeneratedCodeByTableAction(){
        $__db_mng = $this->_get_application_configs()['db_mng'];
        $__getGeneratedCodeByTable = $this->_getGeneratedCodeByTable($__db_mng, 'oxzwzmci_wtab', $this->_get_application_configs()['_post']['tablename']);
        var_dump($__getGeneratedCodeByTable);
        
//        var_dump($this->_get_application_configs()['_post']);
        die();
        $_content = file_get_contents($_path.$this->_get_application_configs()['_post']['filename']);
        
        $_row_count = 0;
        $clmposition = 0;

        $_html = 'Add autoincrement? <input id="addautoincrement" type="checkbox" value="1"/>';
        
        $_html .= '<table>';
        $_ = explode(PHP_EOL, $_content);
        header("Content-Type: application/json");
        if($_ !== ''){
            foreach ($_ as $row){
                $clm = explode(',', $row);
                $_html .= '<tr>';
                if($_row_count === 0){
                    foreach ($clm as $clm){
                        $_html .= '<th data-clmposition="'.$clmposition.'">';
                            $_html .= '<span data-clm="'.$clm.'" data-clmposition="'.$clmposition.'" class="slc">select<br><select class="clmtype"><option value="1">varchar</option><option value="2">int</option></select><br><input type="text"/></span><br>';
                            $_html .= '<span data-clm="'.$clm.'" data-clmposition="'.$clmposition.'" class="key">key</span><br>';
                            $_html .= '<br>';
                        $_html .= ''.$clm.'</th>';
                        $clmposition++;
                    }
                }else{
                    foreach ($clm as $clm){
                        $_html .= '<td>'.$clm.'</td>';
                    }
                }
                $_html .= '</tr>';

                $_row_count++;
            }
            $_html .= '</table>';
            echo json_encode(array('content' => $_html));
        }
        die();
        return new \JsonModel(array(
            'content' => $_content
        ));
    }

    
    private function _getRandomCode() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i=0;$i<10;$i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    private function _clmtype($_clmtype){
        //# TODO: add all types
        switch ($_clmtype) {
            case 1:
            case '1':
                return 'varchar';
            case 2:
            case '2':
                return 'int';

            default:
                break;
        }
    }
    private function _getGeneratedCodeByTable($__db_mng, $dbname, $tablename){
        //# get PRIMARY KEY
        $_select = 
        'SELECT k.column_name
        FROM information_schema.table_constraints t
        JOIN information_schema.key_column_usage k
        USING(constraint_name,table_schema,table_name)
        WHERE t.constraint_type=\'PRIMARY KEY\'
        AND t.table_schema=\''.$dbname.'\'
        AND t.table_name=\''.$tablename.'\';';
        $_ = $__db_mng->getDataByQuery($_select, 'db');
        $_primary_key = $_['response_columns']['column_name'];
        
        //# get FOREIGN KEYS
        $_select = 
        'SELECT k.column_name
        FROM information_schema.table_constraints t
        JOIN information_schema.key_column_usage k
        USING(constraint_name,table_schema,table_name)
        WHERE t.constraint_type=\'FOREIGN KEY\'
        AND t.table_schema=\''.$dbname.'\'
        AND t.table_name=\''.$tablename.'\';';
        $_ = $__db_mng->getDataByQuery($_select, 'db');
        $_foreign_keys = $_['response'];
        $_selectjoin = 'SELECT * FROM '.$tablename.' '.PHP_EOL;
        foreach ($_foreign_keys as $_fk_table){
            $_selectjoin .= 'LEFT JOIN '.strtolower($_fk_table['column_name']).' ON '.strtolower($_fk_table['column_name']).'.id_'.strtolower($_fk_table['column_name']).' = '.$tablename.'.'.$_fk_table['column_name'].' '.PHP_EOL;
        }
        
        //# get JOINED DATA
        $_select = $_selectjoin;
        $_ = $__db_mng->getDataByQuery($_select, 'db');
        $_datakeys = $_['response_columns'];
        $_data = $_['response'];
        foreach ($_datakeys as $key => $_clm){
            $_columns[] = $key;
        }
        $_data_values = array();
        
        $i = 0;
        foreach ($_data as $_value){
            foreach ($_columns as $_clm){
                $_data_values[$i][] = $_value[$_clm];
            }
            $i++;
        }

        //# Show data
        $_html = '<table>';
        
        header("Content-Type: application/json");
        if($_ !== ''){
            $_html .= '<tr>';
            foreach ($_columns as $column){
                $_html .= '<th>'.$column.'</th>';
            }
            $_html .= '</tr>';
            
            foreach ($_data_values as $row){
                $_html .= '<tr>';

                foreach ($row as $clm){
                    $_html .= '<td>'.$clm.'</td>';
                }

                $_html .= '</tr>';

            }
            $_html .= '</table>';
            echo json_encode(array('content' => $_html));
        }
        die();
        return new \JsonModel(array(
            'content' => $_content
        ));
    }
    private function _createTable($_content, $_selectedkey, $__db_mng){
        foreach ($_selectedkey as $_table_fieldname){
            $_table = strtolower($_table_fieldname['col']);
            $_field = $_table_fieldname['col'];
            $column_position = $_table_fieldname['pos'];
            
            $_id_autoincrement = '`id_'.$_table.'` int(11) NOT NULL, ';
            $_querycreatetable = 'CREATE TABLE '.$_table.'( '.$_id_autoincrement;

            $_querycreatetable .= $_field.' varchar(200) NULL ';
            $_querycreatetable .= ') ENGINE=InnoDB DEFAULT CHARSET=utf8;';
            $_querycreatetable .= 'ALTER TABLE `'.$_table.'` ADD PRIMARY KEY (`id_'.$_table.'`);';
            $_querycreatetable .= 'ALTER TABLE `'.$_table.'` MODIFY `id_'.$_table.'` int(11) NOT NULL AUTO_INCREMENT;';
            $__db_mng->getDataByQuery($_querycreatetable, 'db');

            $_getDistinctValuesFromTable = $this->_getDistinctValuesFromTable($_content, $column_position);

            $_values_in_the_table = null;
            foreach ($_getDistinctValuesFromTable as $value){
                $_values_in_the_table[] = $value;
                if(isset($value) && $value != null && $value != ''){
                    $_values[] = array('field' => $_field, 'typed_value' => $value);

                    $id = $__db_mng->saveDataOnTable($_table, $_values, 'db');
                    $_values = null;
                }
            }
            $_values_in_the_table_[$_table] = $_values_in_the_table;
        }
        return $_values_in_the_table_;
    }
    public function _getDistinctValuesFromTable($_content, $column_position){
        $_ = explode(PHP_EOL, $_content);
        if($_ !== ''){
            foreach ($_ as $row){
                $clm = explode(',', $row);

                if(intval($column_position) > 0 && isset($clm[$column_position])){
                    $_columns_values[] = $clm[$column_position];
                }
            }
        }
        return array_unique($_columns_values);
    }
}
