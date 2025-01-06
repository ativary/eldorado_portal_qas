<?php
namespace App\Controllers;

class Home extends BaseController {

    public function __construct() {
        parent::__construct(); // sempre manter
        $this->ManagerModel = model('ManagerModel');
    }

    public function index(){
        return parent::ViewPortal('dashboard');
        
    }
}