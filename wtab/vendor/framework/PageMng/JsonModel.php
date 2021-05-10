<?php

class JsonModel {
    private $_;

    public function get() {
        return $this->_;
    }
    public function __construct($_) {
        $this->_ = $_;

        header("Content-Type: application/json");
        echo json_encode(
            $_
        );
        die();
    }
}
