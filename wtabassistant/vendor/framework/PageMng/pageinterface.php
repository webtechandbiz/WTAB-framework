<?php
class pageinterface {

    private $application_config = null;

    public function table($id, $data, $columns, $_module, $_controller, $_action){
        $php_tab = '    ';
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
                    $_html .= '<td>'.$row[$clm].'</td>'.PHP_EOL;
                }else{
                    $_html .= '<td>&nbsp;</td>'.PHP_EOL;
                }
            }
            $_html .= '<td><div data-id="'.$row[$id].'" class="'.$_action.'">[Edit]</div></td>'.PHP_EOL; //# todo controller
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
