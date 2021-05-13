<?php
class pageinterface {

    private $application_config = null;

    public function table($id, $data, $columns, $_module, $_controller, $_action){
        $php_tab = '    ';
        $tablename = strtolower(str_replace('edit', '', $_action));
        $_html = '<table>'.PHP_EOL;
        $_html .= '<tr>'.PHP_EOL;
        foreach ($columns as $field_name => $clm){
            $_html .= '<th data-fn="'.$field_name.'">'.$clm.'</th>'.PHP_EOL;
        }
        $_html .= '</tr>'.PHP_EOL;

        foreach ($data as $row){
            $_html .= '<tr>'.PHP_EOL;
            foreach ($columns as $field_name => $clm){
                if(isset($row[$clm])){
                    if(strpos($clm, '_id') !== false || strpos($clm, 'id_') !== false){ //# TODO improve
                        $_html .= '<td>'.$row[$clm].'</td>'.PHP_EOL;
                    }else{
                        $_html .= '<td class="open_'.$clm.'" data-id="'.$row['id_'.$tablename].'">'.$row[$clm].'</td>'.PHP_EOL;
                    }
                }else{
                    $_html .= '<td>&nbsp;</td>'.PHP_EOL;
                }
            }
            $_html .= '<td><div data-id="'.$row[$id].'" class="edit '.$_action.'">[Edit]</div></td>'.PHP_EOL; //# todo controller
            $_html .= '</tr>'.PHP_EOL;
        }
        $_html .= '</table>'.PHP_EOL;
        return $_html;
    }
    public function __construct($application_config) {
        if(isset($application_config)){
            $this->application_config = $application_config;
        }else{
            die('no $application_config');
        }
    }
    public function _get_application_configs(){
        if(isset($this->application_config)){
            return $this->application_config;
        }
    }
}
