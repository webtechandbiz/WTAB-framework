<?php

class ViewModel {
    private $_;

    public function get() {
        return $this->_;
    }
    public function __construct($_) {
        $this->_ = $_;
    }
}