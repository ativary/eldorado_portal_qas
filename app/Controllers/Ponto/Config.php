<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;

Class Config extends BaseController {

    public function __construct()
    {
        
        parent::__construct('Ponto');
        $this->_moduloName = '<i class="mdi mdi-account-star-outline"></i> Ponto';

    }

    public function index()
    {
    
        parent::VerificaPerfil('GLOBAL_RH');
        $dados['_titulo'] = "Configurações do Ponto";
        $this->_breadcrumb->add($dados['_titulo'], '');

        return parent::ViewPortal('ponto/config/index', $dados);

    }

}