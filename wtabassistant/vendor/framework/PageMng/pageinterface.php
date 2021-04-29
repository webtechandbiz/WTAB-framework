<?php
class pageinterface {

    private $application_config = null;

    public function table($id, $data, $columns){
        $_html = '<table>';
        $_html .= '<tr>';
        foreach ($columns as $clm){
            $_html .= '<th>'.$clm.'</th>';
        }
        $_html .= '</tr>';

        foreach ($data as $row){
            $_html .= '<tr>';
            foreach ($columns as $clm){
                $_html .= '<td>'.$row[$clm].'</td>';
            }
            $_html .= '<td><a target="_blank" href="edit/'.$id.'">[Edit]</a></td>';
            $_html .= '</tr>';
        }
        $_html .= '</table>';
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
