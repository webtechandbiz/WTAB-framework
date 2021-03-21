<?php

class Encryption {
    private $encryption_details;
    
    public function __construct($encryption_details = false) {
        $this->encryption_details = $encryption_details;
    }

    public function encrypt($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $this->encryption_details['secret_key']);
        $iv = substr(hash('sha256', $this->encryption_details['secret_iv']), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
        return $output;
    }

    public function decrypt($string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $this->encryption_details['secret_key']);
        $iv = substr(hash('sha256', $this->encryption_details['secret_iv']), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
}