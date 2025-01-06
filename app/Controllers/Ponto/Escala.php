<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;

Class Escala extends BaseController {

    private $mEscala;

    public function __construct()
    {
        
        parent::__construct('Ponto'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-calendar-clock"></i> Ponto';
        $this->_breadcrumb->add('Escala', 'ponto/escala');
        $this->mEscala = model('Ponto/EscalaModel');

    }

    public function teste(){

        $dados['_titulo'] = "Troca de Escalas";

        
        return parent::ViewPortal('ponto/escala/teste', $dados);

    }

    public function index(){

        parent::VerificaPerfil('PONTO_TROCADEESCALA');
        $dados['_titulo'] = "Troca de Escalas";

        $dados['filtro'] = $this->request->getPost('filtro');
        $dados['resEscala'] = $this->mEscala->ListarEscala(false, $dados['filtro']);
        
        return parent::ViewPortal('ponto/escala/index', $dados);

    }

    // ------------------------------------------------------------------
    // nova escala
    // ------------------------------------------------------------------
    public function novo(){

        parent::VerificaPerfil('PONTO_TROCADEESCALA');
        $dados['_titulo'] = "Nova Troca de Escala";
        $this->_breadcrumb->add('Nova Troca de Escala', 'ponto/escala/nova');

        $dados['rh']                = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['resFuncionarios'] = $this->mEscala->ListarEscalaFuncionarios($dados);
        $dados['resConfiguracao'] = $this->mEscala->Configuracao();
        $chapa = $this->request->getPost('chapa');
        $dados['chapa'] = $chapa;
        if($chapa){
            
            $dados['resHorario'] = $this->mEscala->ListarEscalaHorario($chapa, $dados['resFuncionarios']);

        }
        
        return parent::ViewPortal('ponto/escala/novo', $dados);
        
    }

    // ------------------------------------------------------------------
    // editar escala
    // ------------------------------------------------------------------
    public function editar($id, $situacao = false){

        parent::VerificaPerfil('PONTO_TROCADEESCALA');

        // valida integridade de dados
        $id = cid($id);
        $situacao = (!$situacao) ? 0 : cid($situacao);

        $dados['_titulo'] = "Visualizar solicitação de escala | <span class=\"badge badge-info\">Nº {$id}</span>";
        
        $this->_breadcrumb->add('Visualizar solicitação de escala');

        // verifica se o registro existe
        $resEscala = $this->mEscala->ListarEscala($id, $situacao);
        if(!$resEscala){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Escala não localizada.')));
            return redirect()->to(base_url('ponto/escala'));
        }

        // pega os dados do funcionário
        $mPortal = model("PortalModel");
        $DadosFuncionario = $mPortal->ListarDadosFuncionario(false, $resEscala[0]['chapa'], false);

        // verifica se o funcionário existe no totvs
        if(!$DadosFuncionario){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Colaborador não localizado.')));
            return redirect()->to(base_url('ponto/escala'));
        }

        // verifica se o funcionário esta ativo no totvs
        if($DadosFuncionario[0]['CODSITUACAO'] == "D"){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Colaborador não esta ativo no RM.')));
            return redirect()->to(base_url('ponto/escala'));
        }

        $dados['DadosFuncionario'] = $DadosFuncionario[0];
        $dados['resHorario'] = $this->mEscala->ListarEscalaHorario($resEscala[0]['chapa'], $DadosFuncionario);

        $dados['resEscala'] = $resEscala[0];
        $dados['id'] = $id;

        switch($resEscala[0]['situacao']){
            case 0: $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-warning">Aguardando aprovação gestor</span>'; break;
            case 1: $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-success">Aguardando upload documento</span>'; break;
            case 2: $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-info">Aguardando aprovação RH</span>'; break;
            case 3: $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-primary">Concluído</span>'; break;
            case 8: $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-danger">Reprovado</span>'; break;
            case 9: $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-danger">Cancelado</span>'; break;
            default: $dados['_titulo'] = $dados['_titulo'];
        }

        if($resEscala[0]['situacao'] == 9) notificacao('danger', 'Requisição cancelada em <b>'.dtBr($resEscala[0]['dtcancelado']).(strlen(trim($resEscala[0]['motivocancelado'])) > 0 ? "<br>Motivo: ".$resEscala[0]['motivocancelado'] : "" ).'</b>');
        if($resEscala[0]['situacao'] == 8) notificacao('danger', 'Requisição reprovada em <b>'.dtBr($resEscala[0]['dtcancelado']).(strlen(trim($resEscala[0]['motivocancelado'])) > 0 ? "<br>Motivo: ".$resEscala[0]['motivocancelado'] : "" ).'</b>');
        
        return parent::ViewPortal('ponto/escala/editar', $dados);
    }

    // ------------------------------------------------------------------
    // configuração da troca de escala
    // ------------------------------------------------------------------
    public function configuracao(){

        parent::VerificaPerfil('PONTO_TROCADEESCALA_CONFIGURACAO');
        $dados['_titulo'] = "Configuração";
        $dados['resConfiguracao'] = $this->mEscala->Configuracao();
        
        $this->_breadcrumb->add('Configuração');

        return parent::ViewPortal('ponto/escala/configuracao', $dados);

    }

    // ------------------------------------------------------------------
    // gera o termo aditivo do contrato
    // ------------------------------------------------------------------
    public function termo_aditivo($id){

        ini_set('display_errors', true);
        error_reporting(-1);

        parent::VerificaPerfil('PONTO_TROCADEESCALA');

        // valida integridade de dados
        $id = cid($id);

        // verifica se o registro existe
        $resEscala = $this->mEscala->ListarEscala($id);
        if(!$resEscala){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Escala não localizada.')));
            return redirect()->to(base_url('ponto/escala'));
        }

        // verifica status da solicitação
        if($resEscala[0]['situacao'] != 1){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Solicitação inválida.')));
            return redirect()->to(base_url('ponto/escala'));
        }


        $dados['chapa'] = $resEscala[0]['chapa'];
        $dados['codhorario'] = $resEscala[0]['codhorario'];
        $dados['datamudanca'] = $resEscala[0]['datamudanca'];
        $dados['resDadosTermo'] = $this->mEscala->ListarEscalaTermoAditivo($dados);

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_bottom' => 9,
            'margin_top' => 9,
        ]);


        
        $WriteHTML = view('ponto/escala/pdf_termo_aditivo', $dados, ['saveData' => false]);
        
        $mpdf->WriteHTML($WriteHTML);

        /*
        * F - salva o arquivo NO SERVIDOR
        * I - abre no navegador E NÃO SALVA
        * D - chama o prompt E SALVA NO CLIENTE
        */
        //header("Content-type:application/pdf");
        $mpdf->Output("TermoAditivo_".$dados['chapa'].".pdf", "I");
        exit();

    }

    // ------------------------------------------------------------------
    // faz o download do termo aditivo do contrato
    // ------------------------------------------------------------------
    public function download_termo_aditivo($id, $situacao = false){


        $situacao = (!$situacao) ? 0 : cid($situacao);

        parent::VerificaPerfil('PONTO_TROCADEESCALA');

        $id = cid($id);
                
        // verifica se o registro existe
        $resEscala = $this->mEscala->ListarEscala($id, $situacao);
        if(!$resEscala){
            notificacao('danger', 'Escala não localizada.');
            return redirect()->to(base_url('ponto/escala'));
        }

        // verifica situacao
        if($resEscala[0]['situacao'] < 2){
            notificacao('danger', 'Escala não localizada.');
            return redirect()->to(base_url('ponto/escala'));
        }

        $documento = explode('|', $resEscala[0]['documento']);

        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename="'.$documento[0].'"');
        header("Content-Type: ".$documento[1]); 
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        echo (base64_decode($documento[3]));
        exit();

    }

    // ------------------------------------------------------------------
    // cancela escala sem anexo 10 dias antes
    // ------------------------------------------------------------------
    public function CronCancelarEscala10Dias(){

        $result = $this->mEscala->CancelarEscala10Dias();

        return ($result) ? "Processado" : "Sem dados ";

    }
    

    //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            //-------------------------------------
            // busca indices do horário
            case 'horario_indice':
                
                $resIndice = $this->mEscala->ListarEscalaHorarioIndice($dados['codhorario']);

                return json_encode($resIndice);
                break;

            //-------------------------------------
            // seleciona chapa
            case 'carrega_escala':

                $resEscala = $this->mEscala->CalculaTrocaDeEscala($dados);

                return $resEscala;
                break;

            //-------------------------------------
            // nova escala
            case 'cadastrar':
                exit($this->mEscala->CadastrarEscala($dados));
                break;

            //-------------------------------------
            // aprova escala
            case 'aprovar':
                exit($this->mEscala->AprovarEscala($dados));
                break;

            //-------------------------------------
            // reprovar escala
            case 'reprovar':
                exit($this->mEscala->ReprovarEscala($dados));
                break;

            //-------------------------------------
            // upload do termo aditivo
            case 'upload_termo_aditivo':
                $dados['documento'] = $_FILES;
                
                exit($this->mEscala->UploadTermoAditivo($dados));
                break;

            //-------------------------------------
            // configuração
            case 'configuracao':
                parent::VerificaPerfil('PONTO_TROCADEESCALA_CONFIGURACAO');
                $dados['documento'] = $_FILES;
                
                exit($this->mEscala->CadastrarConfiguracao($dados));
                break;

                
        }

        

    }

}