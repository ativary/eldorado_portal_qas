<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;

Class Preview extends BaseController {

    public $mAprova;
    public $mEscala;

    public function __construct()
    {
        
        parent::__construct('Ponto');
        $this->mAprova = model('Ponto/AprovaModel');
        $this->mEscala = model('Ponto/EscalaModel');

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

    public function escala($id_documento){
        
        $documento = $this->mEscala->dadosEscala($id_documento);

        if(strlen(trim($documento[0]['documento'])) > 0){
            $documento = explode('|', $documento[0]['documento']);
        }
        
        $doc_type = $documento[1];
        $doc_file = $documento[3];

        if($documento){
            header("Content-type: {$doc_type}");
            echo base64_decode($doc_file);
            exit();
        }else{

            exit('Documento não localizado.');

        }

    }

    public function art61($id_req_chapa){
        
        $documento = $this->mAprova->ListaArt61Anexo($id_req_chapa, 1);

        $doc_type = $documento[0]['file_type'];
        $doc_file = $documento[0]['file_data'];

        if($documento){
            header("Content-type: {$doc_type}");
            echo base64_decode($doc_file);
            exit();
        }else{

            exit('Documento não localizado.');

        }

  }

}