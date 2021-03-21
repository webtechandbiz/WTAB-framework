<?php

class headScript{
    public function appendFile($_) {
        echo '<script src="'.$_.'"></script>'.PHP_EOL;
    }
}