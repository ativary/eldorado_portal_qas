<?php
namespace App\Controllers\Logs;
use App\Controllers\BaseController;

Class Pontomais extends BaseController {

    private $mPontomais;

    public function __construct()
    {
        parent::__construct('Logs');
        $this->_moduloName = '<i class="mdi mdi-progress-check"></i> Logs';
        $this->mPontomais = model('Logs/PontomaisModel');
    }

    public function index()
    {
        parent::VerificaPerfil('LOGS_PONTOMAIS');
        $dados['_titulo'] = "PontoMais";
        $this->_breadcrumb->add($dados['_titulo'], 'logs/pontomais');
        
        return parent::ViewPortal('logs/pontomais/index', $dados);
    }

    public function cronApiPontoMais()
    {
        return $this->mPontomais->CronApiPontoMais();
    }

    //-----------------------------------------------------------
    // Actions
    //-----------------------------------------------------------
    public function action($act)
    {

        $dados = $_POST;
        if(!$dados) return false;
        
        switch($act){
            case 'busca_log'      : exit($this->mPontomais->BuscaLogs($dados)); break;
            case 'cancela_log'    : exit($this->mPontomais->CancelaLog($dados)); break;
            case 'reprocessar_log': exit($this->mPontomais->ReprocessarLog($dados)); break;
        }

        

    }

}