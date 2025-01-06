<?php

namespace App\Libraries;
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
//$mpdf = new \Mpdf\Mpdf();



class Mpdf extends \Mpdf\Mpdf {
    
    public function __construct(){
        parent::__construct();
    }

}

