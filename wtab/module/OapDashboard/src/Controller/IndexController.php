<?php

namespace OapDashboard\Controller;

class IndexController extends \page{

    public function indexAction() {
        $_data = array('test' => 'erewrewr');

        return new \ViewModel(array(
            'data' => $_data
        ));
    }
}