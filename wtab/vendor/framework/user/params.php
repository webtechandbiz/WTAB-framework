<?php

class params{
    public function fromRoute(){
        global $optional_parameters_values;
        return $optional_parameters_values;
    }    
}