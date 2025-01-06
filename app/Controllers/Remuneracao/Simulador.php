<?php
namespace App\Controllers\Remuneracao;
use App\Controllers\BaseController;


Class Simulador extends BaseController {

    public $mSimulador;

    public function __construct()
    {
        
        parent::__construct('Remuneração'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-cash-usd"></i> Remuneração';
        $this->_breadcrumb->add('Simulador', 'remuneracao/simulador');
        $this->mSimulador = model('Remuneracao/SimuladorModel');

    }

    public function index(){

        //ini_set('display_errors', true);
        //error_reporting(E_ALL);

        $dados['time_start']                      = date('d/m/Y H:i:s');
        parent::VerificaPerfil('REMUNERACAO_SIMULADOR');
        $dados['perfil_recrutamento']             = parent::VerificaPerfil('REMUNERACAO_SIMULADOR_RECRUTAMENTO', false);
        $dados['perfil_gestor']                   = parent::VerificaPerfil('REMUNERACAO_GESTOR_RS', false);
        $dados['perfil_global_rh']                = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo']                         = "Simulador";
        $dados['resPosicao']                      = $this->mSimulador->ListarPosicao();
        $dados['resPosicaoRequisicao'] = $this->mSimulador->ListarPosicaoRequisicaoAQ();
        $dados['resFuncionarios'] = $this->mSimulador->ListarSimuladorFuncionarios();
        $dados['resFilial'] = $this->mSimulador->ListarFilial($dados['perfil_global_rh']);
        $dados['resFuncao'] = $this->mSimulador->ListarFuncao($dados['perfil_global_rh']);
        $dados['acao'] = $this->request->getPost('acao');
        $dados['chapa'] = $this->request->getPost('chapa');
        $dados['posicao_destino'] = $this->request->getPost('posicao_destino');
        $dados['filial'] = $this->request->getPost('filial');
        $dados['secao'] = $this->request->getPost('secao');
        $dados['funcao'] = $this->request->getPost('funcao');
        $dados['posicao_recrutamento'] = $this->request->getPost('posicao_recrutamento');
        $adicional['salario_manual'] = $this->request->getPost('salario_manual');
        $adicional['he50_manual'] = $this->request->getPost('he50_manual');
        $adicional['he80_manual'] = $this->request->getPost('he80_manual');
        $adicional['he100_manual'] = $this->request->getPost('he100_manual');
        $adicional['demaiscustos_manual'] = $this->request->getPost('demaiscustos_manual');
        $adicional['demaiscustos_manual_atual'] = $this->request->getPost('demaiscustos_manual_atual');
        $adicional['adicional_noturno'] = $this->request->getPost('adicional_noturno');
        $adicional['he_dsr'] = $this->request->getPost('he_dsr');
        $adicional['premio_producao'] = $this->request->getPost('premio_producao');
        $dados['salario_manual'] = $adicional['salario_manual'];
        $dados['he50_manual'] = $adicional['he50_manual'];
        $dados['he80_manual'] = $adicional['he80_manual'];
        $dados['he100_manual'] = $adicional['he100_manual'];
        $dados['demaiscustos_manual'] = $adicional['demaiscustos_manual'];
        $dados['demaiscustos_manual_atual'] = $adicional['demaiscustos_manual_atual'];
        $dados['adicional_noturno'] = $adicional['adicional_noturno'];
        $dados['he_dsr'] = $adicional['he_dsr'];
        $dados['premio_producao'] = $adicional['premio_producao'];

        $dados['resSecao'] = null;
        $dados['resPrevisao'] = null;

        if($dados['acao'] !== null){
            if($dados['chapa'] !== null) $dados['resCalculo'] = $this->mSimulador->ExecutaSimuladorCalculo($dados['acao'], $dados['chapa'], $dados['posicao_destino'], $adicional);
            if($dados['acao'] == 'P' || $dados['acao'] == 'R') $dados['resPrevisao'] = $this->mSimulador->ExecutaSimuladorCalculo($dados['acao'], null, (($dados['acao'] == "P") ? $dados['posicao_destino'] : $dados['posicao_recrutamento']), $adicional);
            if($dados['acao'] == 'S') $dados['resPrevisao'] = $this->mSimulador->ExecutaSimuladorCalculoSimulacao($dados['acao'], $dados['filial'], $dados['secao'], $dados['funcao'], $adicional);
            $dados['resSecao'] = $this->mSimulador->ListarFilialSecao($dados['filial'], $dados['perfil_global_rh']);
        }
        
        $dados['time_end'] = date('d/m/Y H:i:s');
        return parent::ViewPortal('remuneracao/simulador/index', $dados);

    }

    public function GeraPDFSimulacao($html){

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_bottom' => 7,
            'margin_top' => 7,
            'margin_left' => 7,
            'margin_right' => 7,
            'default_font_size' => 8
        ]);

        $html = '<style type="text/css">
            body {font-family: sans-serif;}
            table {border-collapse: collapse; margin-bottom: 10px;} 
            td, th {border: 1px solid #000000;}
            .bg-primary { background-color: #5766da !important;}
            .bg-success {background-color: #1ecab8 !important;}
            .bg-info {background-color: #00bcd4 !important;}
            .text-white {color: #ffffff !important;}
            th {background: #f0f0f0;}
            .bg-dark {background-color: #2f394e !important;}
            .text-center {text-align: center !important;}
            .text-danger {color: #f93b7a !important;}
            .form-control {border: 1px solid #ffffff !important; text-align: center !important;}
            </style>'.$html;

        $mpdf->WriteHTML($html);

        /*
        * F - salva o arquivo NO SERVIDOR
        * I - abre no navegador E NÃO SALVA
        * D - chama o prompt E SALVA NO CLIENTE
        */
        //header("Content-type:application/pdf");
        return $mpdf->Output("Simulador.pdf", "S");
        exit();

    }

    //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        parent::VerificaPerfil('REMUNERACAO_SIMULADOR');

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            //-------------------------------------
            // seleciona coligada
            case 'filial_secao':

                $perfil_global_rh = parent::VerificaPerfil('GLOBAL_RH', false);
        
                exit(json_encode($this->mSimulador->ListarFilialSecao($dados['codfilial'], $perfil_global_rh)));

                break;

            case 'cadastrar_simulacao': 
                
                exit($this->mSimulador->CadastrarSimulador($dados));

                break;
            
        }

        

    }

}