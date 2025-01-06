<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;

Class Preview extends BaseController {

    public $mAprova;

    public function __construct()
    {
        
        parent::__construct('Ponto');
        $this->mAprova = model('Ponto/AprovaModel');

    }

    public function index($id_documento){

        $documento = $this->mAprova->ListaDadosAnexos($id_documento);

        if(strlen(trim($documento[0]['arquivo'])) > 0){
            $documento = explode('|', $documento[0]['arquivo']);
        }else{
            $documento = explode('|', $documento[0]['anexo_batida']);
        }
        $doc_type = $documento[2];
        $doc_file = $documento[3];

        if($documento){
            header("Content-type: {$doc_type}");
            echo base64_decode($doc_file);
            exit();
        }else{

            exit('Documento não localizado.');

        }

    }

}