<?php

class Convert {
    public function _getENValue($value){
        return str_replace(',', '.', $value);
    }
    
    public function _getEuro($number, $_simbolo = false){
        if($_simbolo){
            return '€ '.number_format($number, 2, ',', '.');
        }else{
            return number_format($number, 2, ',', '.');
        }
        
    }

    public function _getDataIT($date){
        if(isset($date) && $date !== '0000-00-00'){
            $_datetime = new Datetime($date);
            return $_datetime->format('d/m/Y');
        }else{
            return '';
        }
    }
    
    public function _getDateTimeIT($date, $_mode = 1){
        if(isset($date) && $date !== ''){
            $_datetime = new Datetime($date);
            if($_mode === 1){
                return $_datetime->format('d/m/Y H:m:i');
            }
            if($_mode === 2){ //#Scrivi "Oggi" al posto della data
                if($_datetime->format('d/m/Y') === date('d/m/Y')){
                    return 'Oggi '.$_datetime->format('H:m:i');
                }else if($_datetime->format('d/m/Y') === date('d/m/Y', time() - 60 * 60 * 24)){
                    return 'Ieri '.$_datetime->format('H:m:i');
                }else{
                    return $_datetime->format('d/m/Y H:m:i');
                }
            }
            
        }else{
            return '';
        }
    }
    
    public function _getDateEN($date){
        if(isset($date) && $date !== ''){
            $_date = explode('/', $date);
            $date = $_date[2].'-'.$_date[1].'-'.$_date[0];
            $_datetime = new Datetime($date);
            return $_datetime->format('Y-m-d');
        }else{
            return '';
        }
    }

    public function _getValueIT($value){
        return number_format($value, 1, ',', ' ');
    }

    public function _getValueNoDecimal($value){
        return number_format($value, 0, ',', ' ');
    }

    public function _getYear($date){
        if(isset($date) && $date !== '0000-00-00'){
            $_datetime = new Datetime($date);
            return $_datetime->format('Y');
        }else{
            return '';
        }
    }
}
