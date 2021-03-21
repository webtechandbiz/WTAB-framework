<?php
$_now = new \DateTime();
$filename = __DIR__.'/logs/js-errors/js-error-'.$_now->format('YmdHis');

if(isset($_REQUEST)){
    file_put_contents($filename, $_REQUEST);
}