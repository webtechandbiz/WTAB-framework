<?php

class talkWithWP{
    public function get_option($_){
        switch ($_) {
            case 'choosenfile':
                return 'error';

                break;

            default:
                break;
        }
        return '';
    }
}