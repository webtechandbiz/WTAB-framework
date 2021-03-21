<?php

class ImageProcess {
    public function getExtension($finfo){
        switch ($finfo) {
            case 'image/png':
                return 'png';

                break;

            default:
                break;
        }

        return '';
    }
    
}