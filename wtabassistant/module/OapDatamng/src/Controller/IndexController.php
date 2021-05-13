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
        $_tableconstraints = array();
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

                                    $_tableconstraints[$_columns[$_pos]] = 'ALTER TABLE `'.$_currentFile.'` ADD CONSTRAINT `'.$_columns[$_pos].'` FOREIGN KEY (`'.$_columns[$_pos].'`) REFERENCES `'.strtolower($_columns[$_pos]).'`(`id_'.strtolower($_columns[$_pos]).'`) ON DELETE RESTRICT ON UPDATE RESTRICT;';
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

                foreach ($_tableconstraints as $key => $altertable){
                    $__db_mng->getDataByQuery($altertable, 'db');
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
        $this->_getGeneratedCodeByTable($__db_mng, $this->_get_application_configs()['db_details']['VxMO8N5kX4'], $this->_get_application_configs()['_post']['tablename']);
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
        $_datakeys_tablename_columns = $this->_getFieldListByTable($__db_mng, $dbname, $tablename);

        //# get PRIMARY KEY
        $_primary_key = $this->_getPrimaryKeyByTable($__db_mng, $dbname, $tablename);
        
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
        if(is_array($_foreign_keys)){
            foreach ($_foreign_keys as $_fk_table){
                $_column_name = strtolower($_fk_table['column_name']);
                $_column_name = str_replace('_id', '', $_column_name);
                $_selectjoin .= 'LEFT JOIN '.$_column_name.' ON '.$_column_name.'.id_'.$_column_name.' = '.$tablename.'.'.$_fk_table['column_name'].' '.PHP_EOL;
//                $tables[] = $_column_name;
                $_foreign_keys_ary[] = $_fk_table['column_name'];
                $_foreign_tables[] = array(
                    'table' => $_column_name, 'field' => $_fk_table['column_name'],
                );
            }
        }

        //# get JOINED DATA
        $_select = $_selectjoin;
        $_ = $__db_mng->getDataByQuery($_select, 'db');
        $_datakeys = $_['response_columns'];
        $_data = $_['response'];

        foreach ($_datakeys as $key => $_clm){
            $_columns[] = $key;
        }
        if($_['response'] === 'no-rows'){
            $_columns = $_datakeys_tablename_columns;
        }
        //# get tables and fields
        if(isset($_columns) && is_array($_columns)){
            $_getFieldsByTable[] = $this->_getFieldsByTable($__db_mng, $dbname, $tablename);
        }else{
            die('Put some date into the table "'.$tablename.'" and try again.');
        }

        if(isset($_getFieldsByTable) && is_array($_getFieldsByTable)){
            foreach ($_getFieldsByTable as $_field){
                $_fields = array();
                foreach ($_field as $field){
                    $_table = $field['TABLE_NAME'];
                    $_fields[] = array(
                        'clm' => $field['COLUMN_NAME'], 'type' => $field['COLUMN_TYPE']
                        , 'extra' => $field['EXTRA']);
                    $_tables[$field['TABLE_NAME']] = $_fields;
                }
            }
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
        if($_ !== ''){
            $_html .= '<tr>';
            foreach ($_columns as $column){
                $_html .= '<th>'.$column.'</th>';
            }
            $_html .= '</tr>';
            
            foreach ($_data_values as $row){
                $_html .= '<tr>';

                foreach ($row as $clm){
                    if(isset($_foreign_keys_ary) && is_array($_foreign_keys_ary) && array_search($clm, $_foreign_keys_ary)){
                        $_html .= '<td>'.$clm.'</td>';
                    }else{
                        $_html .= '<td>'.$clm.'</td>';
                    }
                }

                $_html .= '</tr>';

            }
            $_html .= '</table>';

            $tabledata = $_html;
        }


        #GENERATE CODE
        $php_tab = "    ";
        $anchor = 'btn_'.$tablename;
        $_action_get = 'get'.ucwords($tablename);
        $_action_set = 'set'.ucwords($tablename);
        $anchor_view = $anchor; //# todo
        $_action_edit = 'edit'.ucwords($tablename);
        $_module = $tablename;
        $_controller = 'index';
        $_id = 'id_'.$tablename;
        $routing_view = array('module' => $_module, 'controller' => $_controller, 'action' => $_action_get);

        $_menu = '<li class="nav-item active">'.PHP_EOL;
            $_menu .= $php_tab.'<a class="nav-link" href="<?php echo $application_configs[\'APPLICATION_URL\']?>'.$_module.'/'.$_controller.'/index">'.$_module.'</a>'.PHP_EOL;
        $_menu .= '</li>'.PHP_EOL;

        //#whitelist
        $_application_config = '//#'.$tablename.PHP_EOL;
        $_application_config .= '\''.$tablename.'/index/index\' => \'no-parameters\','.PHP_EOL;
        $_application_config .= '\''.$tablename.'/'.$_action_get.'/'.$_action_get.'\' => \'no-parameters\','.PHP_EOL;
        $_application_config .= '\''.$tablename.'/'.$_action_set.'/'.$_action_set.'\' => \'no-parameters\','.PHP_EOL;

        //# Module config
        $_module_config_getdata = ''.
            '\''.$tablename.'__'.$_action_get.'\' => ['.PHP_EOL.
                $php_tab.'\'type\'    => Segment::class,'.PHP_EOL.
                $php_tab.'\'options\' => ['.PHP_EOL.
                    $php_tab.$php_tab.'\'route\'    => \'/'.$tablename.'/index/'.$_action_get.'\','.PHP_EOL.
                    $php_tab.$php_tab.'\'defaults\' => ['.PHP_EOL.
                        $php_tab.$php_tab.$php_tab.'\'controller\' => Controller\IndexController::class,'.PHP_EOL.
                        $php_tab.$php_tab.$php_tab.'\'action\'     => \''.$_action_get.'\','.PHP_EOL.
                    $php_tab.$php_tab.'],'.PHP_EOL.
                $php_tab.'],'.PHP_EOL.
            '],';

        //# JS View
        $_jsgetdata = $this->_getJSview($_action_edit, $anchor_view, $tablename, $routing_view, $_primary_key, $_columns);

        //# HTML view
        $_html_getdata = '';
        if($_ !== ''){
            $_columns_ary_str = '';
            foreach ($_columns as $column){
                $_columns_ary_str .= '\''.$column.'\' => \''.$column.'\', ';
            }
            $_html_getdata .= '<span id="span_'.$tablename.'"></span>';
            $_data[] = array('html_getdata' => $_html_getdata);
        }
        
        $_html_getdata .= '<br><br><button id="insert'.$tablename.'">Inserisci</button>';

        //# PHP get data
        $_php_getdata = 'public function '.$_action_get.'Action(){'.PHP_EOL;
        $_php_getdata .= $php_tab.'global $_pageinterface;'.PHP_EOL;
        $_php_getdata .= $php_tab.'$_module = \'index\';'.PHP_EOL;
        $_php_getdata .= $php_tab.'$_controller = \'index\';'.PHP_EOL;
        $_php_getdata .= $php_tab.'$_action_edit = \''.$_action_edit.'\';'.PHP_EOL;
        
        $_php_getdata .= $php_tab.'$___db_mng = $this->_get_application_configs()[\'db_mng\'];'.PHP_EOL.PHP_EOL;

        $_php_getdata .= $php_tab.'$post = $this->_get_application_configs()[\'_post\'];'.PHP_EOL;
        $_php_getdata .= $php_tab.'if(isset($post[\'where\']) && intval($post[\'where\']) > 0){'.PHP_EOL;
        $_php_getdata .= $php_tab.$php_tab.'$_where = $post[\'where\'];'.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.'$get = \''.$_selectjoin.' WHERE id_'.$tablename.'="\'.$_where.\'" \';'.PHP_EOL; //TODO improve
        $_php_getdata .= $php_tab.'}else{'.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.'$get = \''.$_selectjoin.'\';'.PHP_EOL;
        $_php_getdata .= $php_tab.'}'.PHP_EOL;

        $_php_getdata .= $php_tab.'$result = $___db_mng->getDataByQuery($get, \'db\')[\'response\'];'.PHP_EOL;
        
        $_php_getdata .= $php_tab.'$_columns = array('.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.$_columns_ary_str.PHP_EOL;
        $_php_getdata .= $php_tab.');'.PHP_EOL;

        $_php_getdata .= $php_tab.'if($result !== \'no-rows\' && isset($result[0])){'.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.'$table = $_pageinterface->table(\''.$_id.'\', $result, $_columns, $_module, $_controller, $_action_edit);'.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.'$data = $result[0];'.PHP_EOL;
        $_php_getdata .= $php_tab.'}else{'.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.'$data = $result;'.PHP_EOL;
        $_php_getdata .= $php_tab.'}'.PHP_EOL.PHP_EOL;

        $_php_getdata .= $php_tab.'header("Content-Type: application/json");'.PHP_EOL;
        $_php_getdata .= $php_tab.'echo json_encode('.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.'array('.PHP_EOL;
                $_php_getdata .= $php_tab.$php_tab.$php_tab.'\'table\' => $table,'.PHP_EOL;
                $_php_getdata .= $php_tab.$php_tab.$php_tab.'\'data\' => $data'.PHP_EOL;
            $_php_getdata .= $php_tab.$php_tab.')'.PHP_EOL;
        $_php_getdata .= $php_tab.');'.PHP_EOL;
        
        $_php_getdata .= $php_tab.'die();'.PHP_EOL;
        $_php_getdata .= '}'.PHP_EOL;
        //#--

        $routing_edit = array('module' => $_module, 'controller' => $_controller, 'action' => $_action_set);

        //# Module config
        $_module_config_setdata = ''.
            '\''.$tablename.'__'.$_action_set.'\' => ['.PHP_EOL.
                $php_tab.'\'type\'    => Segment::class,'.PHP_EOL.
                $php_tab.'\'options\' => ['.PHP_EOL.
                    $php_tab.$php_tab.'\'route\'    => \'/'.$tablename.'/index/'.$_action_set.'\','.PHP_EOL.
                    $php_tab.$php_tab.'\'defaults\' => ['.PHP_EOL.
                        $php_tab.$php_tab.$php_tab.'\'controller\' => Controller\IndexController::class,'.PHP_EOL.
                        $php_tab.$php_tab.$php_tab.'\'action\'     => \''.$_action_set.'\','.PHP_EOL.
                    $php_tab.$php_tab.'],'.PHP_EOL.
                $php_tab.'],'.PHP_EOL.
            '],';

        //# Module config
        $_module_config_getdata = ''.
            '\''.$tablename.'__'.$_action_get.'\' => ['.PHP_EOL.
                $php_tab.'\'type\'    => Segment::class,'.PHP_EOL.
                $php_tab.'\'options\' => ['.PHP_EOL.
                    $php_tab.$php_tab.'\'route\'    => \'/'.$tablename.'/index/'.$_action_get.'\','.PHP_EOL.
                    $php_tab.$php_tab.'\'defaults\' => ['.PHP_EOL.
                        $php_tab.$php_tab.$php_tab.'\'controller\' => Controller\IndexController::class,'.PHP_EOL.
                        $php_tab.$php_tab.$php_tab.'\'action\'     => \''.$_action_get.'\','.PHP_EOL.
                    $php_tab.$php_tab.'],'.PHP_EOL.
                $php_tab.'],'.PHP_EOL.
            '],';

        //# JS Edit
        $_jsedit = $this->_getJSedit($tablename, $routing_edit, $_primary_key, $_columns);

        //# HTML Edit
        $_html_edit = '';
        $_html_edit .= '<div class="modal fade" id="mdl_edit_'.$tablename.'" role="dialog">'.PHP_EOL;
            $_html_edit .= '<div class="modal-dialog">'.PHP_EOL;
                $_html_edit .= '<div class="modal-content">'.PHP_EOL;
                    $_html_edit .= '<div class="modal-body">'.PHP_EOL;
                        $_html_edit .= '<form id="frm_'.$tablename.'">'.PHP_EOL;
                            foreach ($_datakeys_tablename_columns as $clm){
                                $_html_edit .= '<div class="row">'.PHP_EOL;
                                    $_html_edit .= $php_tab.'<div class="col-md-4">'.PHP_EOL;
                                        $_html_edit .= $php_tab.$php_tab.$clm.PHP_EOL;
                                    $_html_edit .= $php_tab.'</div>'.PHP_EOL;
                                    $_html_edit .= $php_tab.'<div class="col-md-6">'.PHP_EOL;
                                        $_key = $this->_isKeyByColumn($__db_mng, $dbname, $clm);
                                        $_edit_button_needed = false;
                                        if(isset($_foreign_keys_ary) && is_array($_foreign_keys_ary) &&  array_search($clm, $_foreign_keys_ary) || $_key){
                                            if(strpos($clm, '_id') !== false){
                                                $_ext_table = str_replace('_id', '', $clm);
                                                $_html_edit .= $php_tab.'<select id="'.$clm.'">'.PHP_EOL;
                                                    $_getRowsByTable = $this->_getRowsByTable($__db_mng, $dbname, $_ext_table);
                                                    if(is_array($_getRowsByTable)){
                                                        foreach ($_getRowsByTable as $row){
                                                            $_id_clm = 'id_'.$_ext_table;
                                                            $__clm = $_ext_table;
                                                            $_html_edit .= $php_tab.$php_tab.
                                                                '<option value="'.$row[$_id_clm].'">'.$row[$__clm].'</option>'.PHP_EOL;
                                                        }
                                                    }
                                                $_html_edit .= $php_tab.'</select>'.PHP_EOL;
                                                $_edit_button_needed = true;
                                            } else{
                                                $_html_edit .= $php_tab.$php_tab.'<div id="dv_'.$clm.'"></div>'.PHP_EOL;
                                                $_html_edit .= $php_tab.$php_tab.'<input FK1 type="hidden" id="'.$clm.'" placeholder="'.$clm.'"/>'.PHP_EOL;
                                            }
                                        }else{
                                            if(strpos($clm, '_id') !== false){
                                                $_ext_table = str_replace('_id', '', $clm);
                                                $_html_edit .= $php_tab.'<select id="'.$clm.'">'.PHP_EOL;
                                                    $_getRowsByTable = $this->_getRowsByTable($__db_mng, $dbname, $_ext_table);
                                                    if(is_array($_getRowsByTable)){
                                                        foreach ($_getRowsByTable as $row){
                                                            $_id_clm = 'id_'.$_ext_table;
                                                            $__clm = $_ext_table;
                                                            $_html_edit .= $php_tab.$php_tab.
                                                                '<option value="'.$row[$_id_clm].'">'.$row[$__clm].'</option>'.PHP_EOL;
                                                        }
                                                    }
                                                $_html_edit .= $php_tab.'</select>'.PHP_EOL;
                                                $_edit_button_needed = true;
                                            } else{
                                                $_html_edit .= $php_tab.$php_tab.'<input FK2 type="text" id="'.$clm.'" placeholder="'.$clm.'"/>'.PHP_EOL;
                                            }
                                        }
                                    $_html_edit .= $php_tab.'</div>'.PHP_EOL;
                                    $_html_edit .= $php_tab.'<div class="col-md-2">'.PHP_EOL;
                                        if($_edit_button_needed){
                                            $_html_edit .= $php_tab.$php_tab.
                                                '<button id="edit_'.$clm.'"  data-edit="'.$_ext_table.'" class="editslc">[edit]</button>'.PHP_EOL;
                                        }else{
                                            $_html_edit .= $php_tab.$php_tab.'&nbsp;'.PHP_EOL;
                                        }
                                    $_html_edit .= $php_tab.'</div>'.PHP_EOL;
                                $_html_edit .= '</div>'.PHP_EOL;
                            }
                        $_html_edit .= '</form>'.PHP_EOL;
                    $_html_edit .= '</div>'.PHP_EOL;
                    $_html_edit .= '<div class="modal-footer">'.PHP_EOL;
                        $_html_edit .= $php_tab.'<button id="confirm_edit_'.$tablename.'" type="button" class="btn btn-primary">Confirm</button>'.PHP_EOL;
                    $_html_edit .= '</div>'.PHP_EOL;
                $_html_edit .= '</div>'.PHP_EOL;
            $_html_edit .= '</div>'.PHP_EOL;
        $_html_edit .= '</div>'.PHP_EOL;

        //# PHP Edit
        $_php_edit = '';
        $_php_edit .= 'public function '.$routing_edit['action'].'Action(){'.PHP_EOL;
        $_php_edit .= $php_tab.'$___db_mng = $this->_get_application_configs()[\'db_mng\'];'.PHP_EOL;
        $_php_edit .= $php_tab.'$post = $this->_get_application_configs()[\'_post\'];'.PHP_EOL;
        $_php_edit .= $php_tab.'$_post = $post[\'values\'];'.PHP_EOL.PHP_EOL;

        //# Now save data into tablename
        $_php_edit .= $php_tab.'$_where = $post[\'where\'];'.PHP_EOL;
        $table = $tablename;

        $__getFieldsByPrimaryTable = $this->_getFieldsByTable($__db_mng, $dbname, $tablename);

        $_fields = array();

        foreach ($__getFieldsByPrimaryTable as $field){
            $_table = $field['TABLE_NAME'];
            $_column_type = $field['COLUMN_TYPE'];
            $_column_type = str_replace(')', '', $_column_type);
            $_column_type_ary = explode('(', $_column_type);
            $_column_type = $_column_type_ary[0];
            if(isset($_column_type_ary[1])){
                $_column_length = $_column_type_ary[1];
            }else{
                $_column_length = 1000; //#TODO
            }
            $_fields[] = array(
                'clm' => $field['COLUMN_NAME'], 'type' => $_column_type, 'length' => $_column_length, 
                'COLUMN_DEFAULT' => $field['COLUMN_DEFAULT'],
                'IS_NULLABLE' => $field['IS_NULLABLE'],
                'extra' => $field['EXTRA']
            );
            $_tables[$field['TABLE_NAME']] = $_fields;
        }


        foreach ($_tables as $table => $fields){
            //# Mandatory check
            foreach ($fields as $field){
                if(isset($field['clm']) && $field['clm'] != '' && $field['extra'] != 'auto_increment'){
                    $clm = $field['clm'];
                    if($field['IS_NULLABLE'] === 'NO'){
                        $is_mandatory = true;
                        
                        $_php_edit .= $php_tab.'if('.'$_post[\''.$clm.'\'] == \'\'){'.PHP_EOL;
                            $_php_edit .= $php_tab.$php_tab.'return new \JsonModel(array('.PHP_EOL;
                            $_php_edit .= $php_tab.$php_tab.$php_tab.'\'mnderr\' => \''.$clm.'\''.PHP_EOL;
                            $_php_edit .= $php_tab.$php_tab.'));'.PHP_EOL;
                        $_php_edit .= $php_tab.'}'.PHP_EOL;
                    }else{
                        $is_mandatory = false;
                    }
                }
            }

            //# Formal check
            foreach ($fields as $field){ //# TODO: must be improved
                if(isset($field['clm']) && $field['clm'] != '' && $field['extra'] != 'auto_increment'){
                    $clm = $field['clm'];
//                    length //#TODO length check
                    $_php_edit .= $php_tab.'$_fc = $___db_mng->formalCheck(\''.$field['type'].'\', $_post[\''.$clm.'\']);'.PHP_EOL;
                    $_php_edit .= $php_tab.'if(!$_fc){'.PHP_EOL;
                    $_php_edit .= $php_tab.$php_tab.'return new \JsonModel(array('.PHP_EOL;
                    $_php_edit .= $php_tab.$php_tab.$php_tab.'\'mnderr\' => \''.$clm.'\''.PHP_EOL;
                    $_php_edit .= $php_tab.$php_tab.'));'.PHP_EOL;
                    $_php_edit .= $php_tab.'}'.PHP_EOL;
                }
            }

            //# Save data
            foreach ($fields as $field){
                if(isset($field['clm']) && $field['clm'] != '' && $field['extra'] != 'auto_increment'){
                    $clm = $field['clm'];
                    $_php_edit .= $php_tab.'$save[] = array(\'field\' => \''.$clm.'\', \'typed_value\' => $_post[\''.$clm.'\']);'.PHP_EOL;
                }
            }

        }


        $_php_edit .= $php_tab.'if(isset($_where) && intval($_where) > 0){'.PHP_EOL;
            $_php_edit .= $php_tab.$php_tab.'$insert_update = 1;'.PHP_EOL;
            $_php_edit .= $php_tab.$php_tab.'$save[] = array(\'where_field\' => \''.$_primary_key.'\', \'where_value\' => $_post[\''.$_primary_key.'\']);'.PHP_EOL;
        $_php_edit .= $php_tab.'}else{'.PHP_EOL;
            $_php_edit .= $php_tab.$php_tab.'$insert_update = 0;'.PHP_EOL;
        $_php_edit .= $php_tab.'}'.PHP_EOL;
        $_php_edit .= $php_tab.'$id = $___db_mng->saveDataOnTable(\''.$table.'\', $save, \'db\', $insert_update);'.PHP_EOL;
        
        $_php_edit .= $php_tab.'echo json_encode(array(\'content\' => $id));'.PHP_EOL;
        $_php_edit .= $php_tab.'die();'.PHP_EOL;

        $_php_edit .= '}'.PHP_EOL;

        $_html_HTMLviewfield = $_html_JSviewfield = '';
        if(isset($_foreign_tables) && is_array($_foreign_tables)){
            foreach ($_foreign_tables as $ft){
                $__getFieldsByPrimaryTable = $this->_getFieldsByTable($__db_mng, $dbname, $ft['table']);

                foreach ($__getFieldsByPrimaryTable as $field){
                    $_table = $field['TABLE_NAME'];
                    $_column_type = $field['COLUMN_TYPE'];
                    $_column_type = str_replace(')', '', $_column_type);
                    $_column_type_ary = explode('(', $_column_type);
                    $_column_type = $_column_type_ary[0];
                    if(isset($_column_type_ary[1])){
                        $_column_length = $_column_type_ary[1];
                    }else{
                        $_column_length = 1000; //#TODO
                    }
                    $_fields_ft[] = array(
                        'clm' => $field['COLUMN_NAME'], 'type' => $_column_type, 'length' => $_column_length, 
                        'COLUMN_DEFAULT' => $field['COLUMN_DEFAULT'],
                        'IS_NULLABLE' => $field['IS_NULLABLE'],
                        'extra' => $field['EXTRA']
                    );
                    $_tables[$field['TABLE_NAME']] = $_fields_ft;
                }

                //# HTML View field
                $_html_HTMLviewfield .= $this->_getHTMLViewField($__db_mng, $php_tab, $dbname, $ft['table'], $_fields_ft);

                //# HTML View field
                $_html_JSviewfield .= $this->_getJSViewField($ft['table'], $routing_view, $_primary_key, $_fields_ft);

                $_fields_ft = array();
            }
        }
        
        header("Content-Type: application/json");
        echo json_encode(
            array(
                'tabledata' => $tabledata, 
                'selectjoin' => $_selectjoin,

                'menu' => $_menu,
                'application_config' => $_application_config,
                
                //# Get
                'module_config_get_data' => $_module_config_getdata,
                'js_getdata' => $_jsgetdata,
                'html_getdata' => $_html_getdata,
                'php_getdata' => $_php_getdata,
                'html_HTMLviewfield' => $_html_HTMLviewfield,
                'html_JSviewfield' => $_html_JSviewfield,

                //# Edit
                'module_config_set_data' => $_module_config_setdata,
                'jsedit' => $_jsedit,
                'htmledit' => $_html_edit,
                'php_edit' => $_php_edit
            )
        );
        
        die();
        return new \JsonModel(array(
            'content' => $_content
        ));
    }
    
    private function _getJSview($_action_edit, $anchor_view, $tablename, $routing, $_primary_key, $fields){
        $php_tab = "    ";
        $_ = '';

        $_ .= 'fnc__'.$_action_edit.'();'.PHP_EOL;

        $_ .= '$(\'body\').on(\'click\', \'.'.$_action_edit.'\', function(e) {'.PHP_EOL;
            $_ .= $php_tab.'console.log($(this).data(\'id\'));'.PHP_EOL;
            $_ .= $php_tab.'var _alltds = $(this).parent().parent().find(\'td\');'.PHP_EOL;
            $_ .= $php_tab.'var _th = $(this).parent().parent().parent().find(\'th\');'.PHP_EOL;
            $_ .= $php_tab.'$( _th ).each(function( index, value ) {'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'if(typeof index !== \'undefined\' && typeof value !== \'undefined\') {'.PHP_EOL;
                    $_ .= $php_tab.$php_tab.$php_tab.'$(\'#\' + $(value).data(\'fn\')).val($(_alltds[index]).html());'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'}'.PHP_EOL;
            $_ .= $php_tab.'});'.PHP_EOL;

            $_ .= $php_tab.'$(\'#id_'.$tablename.'\').val($(this).data(\'id\'));'.PHP_EOL;
            $_ .= $php_tab.'$(\'#mdl_edit_'.$tablename.'\').modal(\'show\');'.PHP_EOL;
            $_ .= $php_tab.'return false;'.PHP_EOL;
        $_ .= '});'.PHP_EOL;


        $_ .= 'function fnc__'.$_action_edit.'(){'.PHP_EOL;
            $_ .= $php_tab.'console.log(\''.$anchor_view.'\');'.PHP_EOL;
            $_ .= $php_tab.'$(\'#caricamento\').show();'.PHP_EOL;
            $_ .= $php_tab.'var where = $(\'#'.$_primary_key.'\').val();'.PHP_EOL;

            $_ .= $php_tab.'$.post( APPLICATION_URL + "'.$routing['module'].'/'.$routing['controller'].'/'.$routing['action'].'", { where: where })'.PHP_EOL;
            $_ .= $php_tab.'.done(function(data) {'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log(data);'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'$(\'#span_'.$tablename.'\').replaceWith(data.table);'.PHP_EOL;
                $_ .= $php_tab.'$(\'#caricamento\').hide();'.PHP_EOL;
            $_ .= $php_tab.'})'.PHP_EOL;
            $_ .= $php_tab.'.fail(function(data) {'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log( "error" );'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log(data);'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log(\'span_'.$tablename.'\');'.PHP_EOL;
                
            $_ .= $php_tab.'});'.PHP_EOL;
            $_ .= $php_tab.'return false;'.PHP_EOL;
        $_ .= '}'.PHP_EOL;

//#TODO improve
//        $_ .= '$(\'body\').on(\'click\', \'.editslc\', function(e) {'.PHP_EOL;
//            $_ .= $php_tab.'console.log($(this).data(\'edit\'));'.PHP_EOL;
//            $_ .= $php_tab.'window.open(APPLICATION_URL + $(this).data(\'edit\') + "/'.$routing['controller'].'", \'_blank\');'.PHP_EOL; //#TODO
//        $_ .= '});'.PHP_EOL;

        return $_;
    }
    
    private function _getJSedit($tablename, $routing, $_primary_key, $fields){
        $php_tab = "    ";
        $_ = '';
        $_ .= '$(\'body\').on(\'click\', \'#confirm_edit_'.$tablename.'\', function(e) {'.PHP_EOL;
            $_ .= $php_tab.'console.log(\'#confirm_edit_'.$tablename.'\');'.PHP_EOL;

            $_ .= $php_tab.'$(\'#caricamento\').show();'.PHP_EOL;
            $_ .= $php_tab.'$(\'.modal\').modal(\'hide\');'.PHP_EOL;

            $_ .= $php_tab.'var values = {};'.PHP_EOL;
            $_ .= $php_tab.'var where = $(\'#'.$_primary_key.'\').val();'.PHP_EOL;
            
            foreach ($fields as $fieldname => $fieldvalue){
                if(strpos($fieldvalue, '_id') !== false){
                    $_ .= $php_tab.'values[\''.$fieldvalue.'\'] = $(\'#'.$fieldvalue.' option:selected\').val();'.PHP_EOL;
                } else{
                    $_ .= $php_tab.'values[\''.$fieldvalue.'\'] = $(\'#'.$fieldvalue.'\').val();'.PHP_EOL;
                }
            }
            $_ .= $php_tab.'$.post( APPLICATION_URL + "'.$routing['module'].'/'.$routing['controller'].'/'.$routing['action'].'", { values: values, where: where })'.PHP_EOL;
            $_ .= $php_tab.'.done(function(data) {'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log(data);'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'location.reload();'.PHP_EOL;
            $_ .= $php_tab.'})'.PHP_EOL;
            $_ .= $php_tab.'.fail(function(data) {'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log( "error" );'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log(data);'.PHP_EOL;
            $_ .= $php_tab.'});'.PHP_EOL;
            $_ .= $php_tab.'return false;'.PHP_EOL;
        $_ .= '});'.PHP_EOL.PHP_EOL;

        $_ .= '$(\'body\').on(\'click\', \'#insert'.$tablename.'\', function(e) {'.PHP_EOL;
            $_ .= $php_tab.'console.log(\'#insert'.$tablename.'\');'.PHP_EOL;
            $_ .= $php_tab.'$(\'#id_'.$tablename.'\').val($(this).data(\'id\'));'.PHP_EOL;
            $_ .= $php_tab.'$(\'#mdl_edit_'.$tablename.'\').modal(\'show\');'.PHP_EOL;
            $_ .= $php_tab.'return false;'.PHP_EOL;
        $_ .= '});'.PHP_EOL;

        return $_;
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
    
    private function _getPrimaryKeyByTable($__db_mng, $dbname, $tablename){
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
        return $_primary_key;
    }
    
    private function _isKeyByColumn($__db_mng, $dbname, $column){
        $_select = 
            'SELECT k.column_name
            FROM information_schema.table_constraints t
            JOIN information_schema.key_column_usage k
            USING(constraint_name,table_schema,table_name)
            WHERE t.constraint_type=\'PRIMARY KEY\'
            AND t.table_schema=\''.$dbname.'\'
            AND k.column_name=\''.$column.'\';';
        $_ = $__db_mng->getDataByQuery($_select, 'db');

        if(isset($_) && isset($_['response']) && isset($_['response'][0]) && isset($_['response'][0]['column_name'])
            && isset($_['response'][0]['column_name']) !== ''){
            return true;
        }else{
            return false;
        }
    }
    
    private function _getFieldsByTable($__db_mng, $db_name, $tablename){
        $_select = 
            'SELECT * FROM information_schema.columns
            WHERE `table_name` = "'.$tablename.'"
            AND table_schema = "'.$db_name.'"';

        $_ = $__db_mng->getDataByQuery($_select, 'db');
        if(isset($_) && isset($_['response']) && isset($_['response'][0]) && isset($_['response'][0]['COLUMN_NAME'])
            && isset($_['response'][0]['COLUMN_NAME']) !== ''){
            return $_['response'];
        }else{
            return false;
        }
    }

    private function _getFieldListByTable($__db_mng, $db_name, $tablename){
        $_fields = array();
        $_select = 'SHOW COLUMNS FROM '.$db_name.'.'.$tablename;

        $_ = $__db_mng->getDataByQuery($_select, 'db');
        foreach ($_['response'] as $_response){
            $_fields[] = $_response['Field'];
        }
        return $_fields;
    }

    private function _getRowsByTable($__db_mng, $db_name, $tablename){
        $_select = 
            'SELECT * FROM '.$db_name.'.'.$tablename.'';
        $_ = $__db_mng->getDataByQuery($_select, 'db');
        if(isset($_) && isset($_['response']) && isset($_['response'][0])){
            return $_['response'];
        }else{
            return false;
        }
    }
    
    private function _getHTMLViewField($__db_mng, $php_tab, $dbname, $tablename, $_datakeys_tablename_columns){
        $_html_viewfield = '';
        $_html_viewfield .= '<div class="modal fade" id="mdl_view_'.$tablename.'" role="dialog">'.PHP_EOL;
            $_html_viewfield .= '<div class="modal-dialog">'.PHP_EOL;
                $_html_viewfield .= '<div class="modal-content">'.PHP_EOL;
                    $_html_viewfield .= '<div class="modal-body">'.PHP_EOL;
                        $_html_viewfield .= '<form id="frm_'.$tablename.'">'.PHP_EOL;
                            foreach ($_datakeys_tablename_columns as $_clm){
                                $clm = $_clm['clm'];
                                $_html_viewfield .= '<div class="row">'.PHP_EOL;
                                    $_html_viewfield .= $php_tab.'<div class="col-md-4">'.PHP_EOL;
                                        $_html_viewfield .= $php_tab.$php_tab.$clm.PHP_EOL;
                                    $_html_viewfield .= $php_tab.'</div>'.PHP_EOL;
                                    $_html_viewfield .= $php_tab.'<div class="col-md-8">'.PHP_EOL;
                                        $_key = $this->_isKeyByColumn($__db_mng, $dbname, $clm);
                                        $_edit_button_needed = false;
                                        $_html_viewfield .= $php_tab.$php_tab.'<span id="'.$clm.'">'.$clm.'</span>'.PHP_EOL;
                                    $_html_viewfield .= $php_tab.'</div>'.PHP_EOL;
                                $_html_viewfield .= '</div>'.PHP_EOL;
                            }
                        $_html_viewfield .= '</form>'.PHP_EOL;
                    $_html_viewfield .= '</div>'.PHP_EOL;
                $_html_viewfield .= '</div>'.PHP_EOL;
            $_html_viewfield .= '</div>'.PHP_EOL;
        $_html_viewfield .= '</div>'.PHP_EOL;
        $_html_viewfield .= PHP_EOL.PHP_EOL;
        
        return $_html_viewfield;
    }
    
    private function _getJSViewField($tablename, $routing, $_primary_key, $fields){
        $routing['module'] = $tablename; //#TODO improve
        $routing['controller'] = 'index'; //#TODO improve
        $routing['action'] = 'get'.ucwords($tablename); //#TODO improve

        $php_tab = "    ";
        $_ = ''.PHP_EOL;
        $_ .= '$(\'body\').on(\'click\', \'.open_'.$tablename.'\', function(e) {'.PHP_EOL;
            $_ .= $php_tab.'console.log(\'.open_'.$tablename.'\');'.PHP_EOL;

            $_ .= $php_tab.'$(\'#caricamento\').show();'.PHP_EOL;
            $_ .= $php_tab.'$(\'.modal\').modal(\'hide\');'.PHP_EOL;
            $_ .= $php_tab.'var values = {};'.PHP_EOL;
            $_ .= $php_tab.'var where = $(this).data(\'id\');'.PHP_EOL;

            $_ .= $php_tab.'$.post( APPLICATION_URL + "'.$routing['module'].'/'.$routing['controller'].'/'.$routing['action'].'", { values: values, where: where })'.PHP_EOL;
            $_ .= $php_tab.'.done(function(data) {'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log(data);'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'$(\'#caricamento\').hide();'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'data = data.data;'.PHP_EOL;

                foreach ($fields as $fieldname => $_fieldvalue){
                    $fieldvalue  = $_fieldvalue['clm'];
                    $_ .= $php_tab.$php_tab.'$(\'#'.$fieldvalue.'\').html(data.'.$fieldvalue.');'.PHP_EOL;
                }

                $_ .= $php_tab.$php_tab.'$(\'#mdl_view_'.$tablename.'\').modal(\'show\');'.PHP_EOL;

            $_ .= $php_tab.'})'.PHP_EOL;
            $_ .= $php_tab.'.fail(function(data) {'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log( "error" );'.PHP_EOL;
                $_ .= $php_tab.$php_tab.'console.log(data);'.PHP_EOL;
            $_ .= $php_tab.'});'.PHP_EOL;
            $_ .= $php_tab.'return false;'.PHP_EOL;
        $_ .= '});'.PHP_EOL;
        return $_;
    }
    
}
