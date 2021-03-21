<?php

class headLink{
    public function appendStylesheet($_) {
        echo '<link href="'.$_.'" rel="stylesheet">'.PHP_EOL;
    }
    public function prependStylesheet($_) {
        echo '<link href="'.$_.'" rel="stylesheet">'.PHP_EOL;
    }
}