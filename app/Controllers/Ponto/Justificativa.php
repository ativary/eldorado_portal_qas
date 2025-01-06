<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\Model;
// ini_set('display_errors', true);
// error_reporting(E_ALL);
Class Justificativa extends BaseController {
	
	public $mOcorrencia;
    private $mEspelho;

    public function __construct()
    {
        
        parent::__construct('Ponto'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-calendar-clock"></i> Ponto';
        $this->_breadcrumb->add('Justificativas de Ponto', 'ponto/justificativa');
        $this->mEspelho    = model('Ponto/EspelhoModel');
        $this->mOcorrencia = model('Ponto/OcorrenciaModel');

    }

    public function index(){
       
        parent::VerificaPerfil('PONTO_CONFIG_JUSTIFICATIVA');
        $dados['_titulo'] = "Justificativas de Ponto";

        $dados['motivos'] = [
            'faltas'        => $this->mEspelho->ListarJustificativa(1),
            'atrasos'       => $this->mEspelho->ListarJustificativa(2),
            'extras'        => $this->mEspelho->ListarJustificativa(3),
            'ajustes'       => $this->mEspelho->ListarJustificativa(4),
            'reprovas'      => $this->mEspelho->ListarJustificativa(5),
            'ocorrencia'    => $this->mOcorrencia->ListarOcorrenciaMotivo()
        ];
        
        return parent::ViewPortal('ponto/justificativa/index', $dados);

    }
    

    //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        if(!parent::VerificaPerfil('PONTO_CONFIG_JUSTIFICATIVA', false)){
            return responseJson('error', 'Sem premissão de acesso');
        }

        $dados = $_POST;
        if(!$dados) return false;

        $_SESSION['tab_open'] = $dados['tipo'];

        switch($act){
            case 'excluir_config_justificativa':
                exit($this->mEspelho->ExcluirConfigJustificativa($dados));
                break;
            case 'alterar_config_justificativa':
                exit($this->mEspelho->AlterarConfigJustificativa($dados));
                break;
        }

    }

}