<?php

if(!$application_configs['ENABLE_HTTP'] && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off")){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}