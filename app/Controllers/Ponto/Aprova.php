<?php

namespace App\Controllers\Ponto;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controllers\BaseController;

class Aprova extends BaseController
{

  private $mAprova;
  private $mCritica;
  private $mPortal;
  public $mOcorrencia;
  public $mHierarquia;

  public function __construct()
  {
    parent::__construct('Ponto'); // sempre manter

    $this->_moduloName    = '<i class="fas fa-clock"></i> Ponto';
    $this->mAprova        = model('Ponto/AprovaModel');
    $this->mCritica       = model('Ponto/CriticaModel');
    $this->mPortal        = model('PortalModel');
    $this->mOcorrencia    = model('Ponto/OcorrenciaModel');
    $this->mHierarquia    = model('HierarquiaModel');
  }

  public function index()
  {

    //set_time_limit(60*2);

    parent::VerificaPerfil('PONTO_APROVA');
    
    $dados['perfilRH']              = parent::VerificaPerfil('GLOBAL_RH', false);
    $dados['isGestorHierarquia']    = $this->mHierarquia->isGestor();
    $dados['isLiderAprovador']      = $this->mHierarquia->isLiderAprovador();
    $dados['isGestorSubstituto']    = $this->mHierarquia->isGestorSubstituto();
    $dados['acessoPermitido']       = ($dados['isGestorHierarquia'] || $dados['perfilRH'] || $dados['isLiderAprovador'] || $dados['isGestorSubstituto']) ? true : false;

    if(!$dados['acessoPermitido']) notificacao('warning2', 'Você não possui permissão para aprovação de ponto.');

    $dados['_titulo'] = "Aprovar ponto";
    $this->_breadcrumb->add($dados['_titulo'], 'ponto/aprova');
    
    $dados['chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

    $mEspelho = model('Ponto/EspelhoModel');
    $dados['resAbonos'] = $mEspelho->ListarAbono();

    $dados['EspelhoConfiguracao'] = $mEspelho->ListarEspelhoConfiguracao();
    $dados['gestorPossuiExcecao'] = $mEspelho->gestorPossuiExcecao();
    $dados['resFilial'] = $this->mOcorrencia->ListarOcorrenciaFilial();

    if(!$dados['EspelhoConfiguracao']){
        notificacao('warning2', 'Configuração de ponto não localizada.');
        redireciona(base_url('ponto/espelho/editar'));
        return false;
    }

        $idbatida                   = $this->request->getPost('idbatida');
        $act                        = $this->request->getPost('act');
        $codsecao                   = $this->request->getPost('secao');
        $codccusto                  = $this->request->getPost('ccusto');
        $tipo_abono                 = $this->request->getPost('tipo_abono');
        $ft_legenda                 = $this->request->getPost('ft_legenda');
        $ft_status                  = $this->request->getPost('ft_status');
        $chapa                      = $this->request->getPost('funcionario');
        $motivo_reprova             = $this->request->getPost('motivo_reprova');
        $dados['filtro_tipo']       = $this->request->getPost('filtro_tipo');
        $dados['filtro_filial']     = $this->request->getPost('filtro_filial');
        $dados['filtro_legenda']    = $this->request->getPost('filtro_legenda');
        $dados['filtro_tipo2']      = $this->request->getPost('filtro_tipo2');
        $filtroPeriodo              = $this->request->getPost('periodo');
        $statusPeriodo              = $this->request->getPost('statusPeriodo');

        if(isset($_SESSION['filtro_tipo'])) $dados['filtro_tipo'] = $_SESSION['filtro_tipo'];
        if(isset($_SESSION['periodo'])) $filtroPeriodo = $_SESSION['periodo'];
        if(isset($_SESSION['statusPeriodo'])) $statusPeriodo = $_SESSION['statusPeriodo'];
        if(isset($_SESSION['filtro_filial'])) $dados['filtro_filial'] = $_SESSION['filtro_filial'];
        if(isset($_SESSION['secao'])) $codsecao = $_SESSION['secao'];
        if(isset($_SESSION['ccusto'])) $codccusto = $_SESSION['ccusto'];
        if(isset($_SESSION['funcionario'])) $chapa = $_SESSION['funcionario'];
        if(isset($_SESSION['filtro_legenda'])) $dados['filtro_legenda'] = $_SESSION['filtro_legenda'];
        if(isset($_SESSION['filtro_tipo2'])) $dados['filtro_tipo2'] = $_SESSION['filtro_tipo2'];

        if(isset($_SESSION['filtro_tipo'])){
            unset($_SESSION['filtro_tipo'], $_SESSION['periodo'], $_SESSION['statusPeriodo'], $_SESSION['filtro_filial'], $_SESSION['secao'], $_SESSION['funcionario'], $_SESSION['filtro_tipo2']);
        }

        if($motivo_reprova == null){
            $dados['periodo']             = ($filtroPeriodo != null) ? substr($filtroPeriodo, 0, -1) : null;
            $dados['statusPeriodo']       = ($filtroPeriodo != null) ? substr($filtroPeriodo, -1) : 0;
        }else{
            $dados['periodo']             = $filtroPeriodo;
            $dados['statusPeriodo']       = ($statusPeriodo != null) ? $statusPeriodo : 0;
        }

        $dados['codsecao'] = $codsecao;
        $dados['codccusto'] = $codccusto;
        $objListaBatidaApr = false;


        $colaboradores = [];

        if($dados['periodo']){
            $dadosColaboradores['rh']         = $dados['perfilRH'];
            $dadosColaboradores['codsecao']   = ($dados['codsecao'] == 'all') ? null : $dados['codsecao'];
            $dadosColaboradores['codccusto']  = ($dados['codccusto'] == 'all') ? null : $dados['codccusto'];
            $dataInicio                       = substr($dados['periodo'],0,10);
            $colaboradores                    = $this->mPortal->CarregaColaboradores($dataInicio, $dadosColaboradores, false);
        }

        $resFuncionarioSecao = $colaboradores;
        unset($colaboradores);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST' || isset($dados['periodo'])){

            if(!$dados['acessoPermitido']){
                notificacao('warning2', 'Você não possui permissão para aprovações de ponto.');
                redireciona('/ponto/aprova');
                exit();
            }
        
            // APROVA REGISTRO RH COM RM
            if ($act == 'apr') {

                
                    $_SESSION['filtro_tipo']    = $dados['filtro_tipo'];
                    $_SESSION['periodo']        = $filtroPeriodo.$statusPeriodo;
                    $_SESSION['statusPeriodo']  = $statusPeriodo;
                    $_SESSION['filtro_filial']  = $dados['filtro_filial'];
                    $_SESSION['secao']          = $codsecao;
                    $_SESSION['ccusto']         = $codccusto;
                    $_SESSION['funcionario']    = $chapa;
                    $_SESSION['filtro_legenda'] = $dados['filtro_legenda'];
                    $_SESSION['filtro_tipo2']   = $dados['filtro_tipo2'];

                    $sucesso = 0;
                    $erro = '';

                    $array_recalculo = array();
                    foreach ($idbatida as $id_batida) {

                        $dados_post = explode('|', $id_batida);

                        $index = $dados_post[0].'_'.$dados_post[1];
                        $tipo = $dados_post[3] ?? 0;
                        
                        if(!isset($array_recalculo[$index]) && ($tipo != 21 && $tipo != 22)){
                            $array_recalculo[$index] = array(
                                'chapa' => $dados_post[0],
                                'data' => $dados_post[1]
                            );
                        }

                        if($tipo == 61){
                            $RESULT = $this->mAprova->aprovaArtigo61($dados_post[2], $dados['perfilRH']);
                        }elseif($tipo == 21 or $tipo == 22){
                            $RESULT = $this->mAprova->aprovaEscala($dados_post[2], $dados['perfilRH']);
                        }else{
                            $RESULT = $this->mAprova->aprovaBatidaRH($dados_post[2], $dados['perfilRH']);
                        }
                        

                        if($RESULT === true){
                            $sucesso++;
                        }else{
                            $erro .= $RESULT . '<br>';
                        }
                    }

                    if($sucesso > 0 && strlen($erro) > 0){
                        notificacao('warning', 'Registro(s) aprovada(s) com sucesso! Porém, ocorreu falha parcial na aprovação.'.$erro);
                    }

                    if($sucesso == 0 && strlen($erro) > 0){
                        notificacao('danger', 'Erro ao aprovar a(s) registro(s) | Erro: '. $erro);
                    }

                    if(strlen($erro) == 0 && $sucesso > 0){
                        notificacao('success', 'Registro(s) aprovada(s) com sucesso!');
                    }
                    
                    notificacao('success', 'Movimento aprovado com sucesso.');

                    redireciona('/ponto/aprova');
                    exit();
                    
                    // $objListaBatidaApr = $this->mAprova->listaBatidaApr(3, $codsecao, false, $tipo_abono, $ft_legenda, $ft_status, false, false, $chapa, $dados['periodo'], $dados);
                
            }

            // APROVA REGISTRO GESTOR
            if ($act == 'aprGestor') {
                foreach ($idbatida as $id_batida) {
                    $this->mAprova->aprovaBatidaGestor($id_batida, 'GESTOR');
                }
                notificacao('success', 'Registro aprovado com sucesso.');
            }

            // REPROVA REGISTRO
            if ($act == 'rep') {
                $resp = 0;
                foreach ($idbatida as $id_batida) {

                    $dadosRep = explode('|', $id_batida);
                    $tipo = $dadosRep[3] ?? 0;

                    if($tipo == 61){
                        $result = $this->mAprova->reprovaArt61($dadosRep[2], $motivo_reprova, $dados['perfilRH']);
                    }elseif($tipo == 21 or $tipo == 22){
                        $result = $this->mAprova->reprovaEscala($dadosRep[2], $motivo_reprova, $dados['perfilRH']);
                    }else{
                        $result = $this->mAprova->reprovaBatidaRH($id_batida, 'RH', $motivo_reprova, $dados['perfilRH']);
                    }

                    if(!$result) $resp = 1;
                }

                if($resp > 0){
                    notificacao('warning', 'Movimento reprovado com sucesso. Porém, ocorreu falha parcial na reprovação.');
                }else{
                    notificacao('success', 'Movimento reprovado com sucesso.');
                }
                
            }

             $objListaBatidaApr = $this->mAprova->listaBatidaApr(3, $codsecao, $codccusto, false, $tipo_abono, $ft_legenda, $ft_status, false, false, $chapa, $dados['periodo'], $dados);
            
        }

        $listaSecaoUsuarioRM = $this->mAprova->listaSecaoUsuario(false, $dados);
        $listaCCustoUsuarioRM = $this->mAprova->listaCCustoUsuario(false, $dados);
        $objSecaoUsu         = $this->mAprova->listaSecaoUsu($_SESSION["log_id"]);

        $dados['objListaBatidaApr']   = $objListaBatidaApr;
        $dados['listaSecaoUsuarioRM'] = $listaSecaoUsuarioRM;
        $dados['listaCCustoUsuarioRM'] = $listaCCustoUsuarioRM;
        $dados['objSecaoUsu']         = $objSecaoUsu;
        $dados['resFuncionarioSecao'] = $resFuncionarioSecao;
        $dados['chapa']               = $chapa;
        $dados['resPeriodo']          = $this->mCritica->ListarCriticaPeriodoRM();

        $mEspelho = model('Ponto/EspelhoModel');
        $dados['resMotivoReprova']   = $mEspelho->ListarJustificativa(5);
        $dados['isGestorOrLider']   = $mEspelho->isGestorOrLider($dados);
        $dados['isGestor']          = $mEspelho->isGestor($dados);
        $dados['resParam'] = $this->mAprova->ListarParam();

        unset($objListaBatidaApr,$listaSecaoUsuarioRM,$objSecaoUsu,$resFuncionarioSecao,$chapa);
        
    return parent::ViewPortal('ponto/aprova/index', $dados);
  }

  public function historico()
  {

    parent::VerificaPerfil('PONTO_APROVA_HISTORICO');

    $dados['_titulo'] = "Histórico de Aprovação";
    $this->_breadcrumb->add($dados['_titulo'], 'ponto/aprova/historico');
    
    $dados['chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

    $mEspelho = model('Ponto/EspelhoModel');
    $dados['resAbonos'] = $mEspelho->ListarAbono();

        $idbatida   = false;
        $act        = false;
        $codsecao   = false;
        $codcusto   = false;
        $tipo_abono = false;
        $ft_legenda = false;
        $ft_status  = false;
        $ft_dtfim = date('Y-m-d');
        $data = new \DateTime($ft_dtfim);
        $data->sub(new \DateInterval("P10D"));
        $ft_dtini = $data->format('Y-m-d');

        if (isset($_POST['idbatida'])) $idbatida        = $_POST['idbatida'];
        if (isset($_POST['act']))      $act             = $_POST['act'];
        if (isset($_POST['secao']))    $codsecao        = $_POST['secao'];
        if (isset($_POST['ccusto']))    $codccusto        = $_POST['codccusto'];
        if (isset($_POST['tipo_abono']))    $tipo_abono = $_POST['tipo_abono'];
        if (isset($_POST['ft_legenda']))    $ft_legenda = $_POST['ft_legenda'];
        if (isset($_POST['ft_status']))      $ft_status = $_POST['ft_status'];
        if (isset($_POST['ft_dtini'])) $ft_dtini = $_POST['ft_dtini'];
        if (isset($_POST['ft_dtfim'])) $ft_dtfim = $_POST['ft_dtfim'];

        $objListaBatidaApr = false;
        if($codsecao){
            $objListaBatidaApr = $this->mAprova->listaBatidaApr('S', $codsecao, $codccusto, false, $tipo_abono, $ft_legenda, 'S', $ft_dtini, $ft_dtfim);
        }

        $nomeFunc = array();
        if ($objListaBatidaApr) {
            foreach ($objListaBatidaApr as $idxx => $value) {
                $nomeFunc[] = $objListaBatidaApr[$idxx]['nomechapa'];
            }
        }

        $nomeFunc = array_unique($nomeFunc);

        $listaSecaoUsuarioRM = $this->mAprova->listaSecaoUsuario();
        $objSecaoUsu         = $this->mAprova->listaSecaoUsu($_SESSION["log_id"]);

        $dados['objListaBatidaApr']   = $objListaBatidaApr;
        $dados['nomeFunc']            = $nomeFunc;
        $dados['listaSecaoUsuarioRM'] = $listaSecaoUsuarioRM;
        $dados['objSecaoUsu']         = $objSecaoUsu;
        $dados['ft_dtini']         = $ft_dtini;
        $dados['ft_dtfim']         = $ft_dtfim;

    return parent::ViewPortal('ponto/aprova/historico', $dados);
  }

  public function download_anexo($id_anexo){
    if(!$id_anexo) exit('Parametros invalidos!');

    $DadosAnexos = $this->mAprova->ListaDadosAnexos($id_anexo);
    
    if(count($DadosAnexos) > 0){

        if(strlen(trim($DadosAnexos[0]['arquivo'])) > 0){
            $DADOS_ARQUIVO = explode ("|", $DadosAnexos[0]['arquivo']);
        }else{
            $DADOS_ARQUIVO = explode ("|", $DadosAnexos[0]['anexo_batida']);
        }

        $ARQUIVO = base64_decode($DADOS_ARQUIVO[3]);
        $TIPO = $DADOS_ARQUIVO[2];
        $NOME = $DADOS_ARQUIVO[0];

        header('Content-Type: application/'. $TIPO);
        header('Content-Disposition: attachment; filename='. $NOME);
        header('Pragma: no-cache');

        echo $ARQUIVO;
        exit();

    }

  }

  public function download_anexo_escala($id_anexo){
    if(!$id_anexo) exit('Parametros invalidos!');

    $mEscala = model('Ponto/EscalaModel');
    $DadosAnexos = $mEscala->dadosEscala($id_anexo);
    
    if(count($DadosAnexos) > 0){

        if(strlen(trim($DadosAnexos[0]['documento'])) > 0){
            $DADOS_ARQUIVO = explode ("|", $DadosAnexos[0]['documento']);
        }

        $ARQUIVO = base64_decode($DADOS_ARQUIVO[3]);
        $TIPO = $DADOS_ARQUIVO[1];
        $NOME = $DADOS_ARQUIVO[0];

        header('Content-Type: '.$TIPO);
        header('Content-Disposition: attachment; filename='. $NOME);
        header('Pragma: no-cache');

        echo $ARQUIVO;
        exit();

    }

  }
  
  public function download_anexo_art61($id_req_chapa, $linha=1){
    if(!$id_req_chapa) exit('Parametros invalidos!');

    $documento = $this->mAprova->ListaArt61Anexo($id_req_chapa, 1);

    $doc_name = $documento[0]['file_name'];
    $doc_type = $documento[0]['file_type'];
    $doc_file = $documento[0]['file_data'];

    if($documento){
        $ARQUIVO = base64_decode($doc_file);
        $TIPO = $doc_type;
        $NOME = $doc_name;

        header('Content-Type: application/'. $TIPO);
        header('Content-Disposition: attachment; filename='. $NOME);
        header('Pragma: no-cache');

        echo $ARQUIVO;
        exit();

    }

  }

  public function excel()
    {
        parent::VerificaPerfil('PONTO_APROVA');
        $dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);
        
        $codsecao                   = $this->request->getPost('secao');
        $codccusto                   = $this->request->getPost('ccusto');
        $tipo_abono                 = $this->request->getPost('tipo_abono');
        $ft_legenda                 = $this->request->getPost('ft_legenda');
        $ft_status                  = $this->request->getPost('ft_status');
        $chapa                      = $this->request->getPost('funcionario');
        $dados['filtro_tipo']       = $this->request->getPost('filtro_tipo');
        $dados['filtro_filial']     = $this->request->getPost('filtro_filial');
        $dados['filtro_legenda']    = $this->request->getPost('filtro_legenda');
        $dados['filtro_tipo2']      = $this->request->getPost('filtro_tipo2');
        $motivo_reprova             = $this->request->getPost('motivo_reprova');

       
        $dados['periodo']             = ($this->request->getPost('periodo') != null) ? ($this->request->getPost('periodo')) : null;
        $dados['statusPeriodo']       = ($this->request->getPost('periodo') != null) ? ($this->request->getPost('periodo')) : 0;

        
        // $objListaBatidaApr      = $this->mAprova->listaBatidaApr(3, $codsecao, false, false, false, false, false, false, $chapa, $periodo, $dados);

        $objListaBatidaApr = $this->mAprova->listaBatidaApr(3, $codsecao, $codccusto, false, $tipo_abono, $ft_legenda, $ft_status, false, false, $chapa, $dados['periodo'], $dados);

        $spreadsheet = new Spreadsheet();

        // cor do texto
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF')
            )
        );
        $styleBorda = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ),
            ),
        );

        $spreadsheet->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleBorda);

        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A1:K1')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');

        // nome da aba da planilha
        $spreadsheet->getActiveSheet()->setTitle('Aprovação de Ponto');
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:K1'); // auto filtro no titulo

        // titulo das colunas
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'STATUS');
        $sheet->setCellValue('B1', 'TIPO');
        $sheet->setCellValue('C1', 'DATA');
        $sheet->setCellValue('D1', 'COLABORADOR');
        $sheet->setCellValue('E1', 'DESCRIÇÃO TIPO');
        $sheet->setCellValue('F1', 'JUSTIFICATIVA');
        $sheet->setCellValue('G1', 'ANEXO');
        $sheet->setCellValue('H1', 'REGISTROS DO DIA');
        $sheet->setCellValue('I1', 'DATA REFERÊNCIA');
        $sheet->setCellValue('J1', 'DATA SOLICITAÇÃO');
        $sheet->setCellValue('K1', 'SOLICITANTE');
        // $sheet->setCellValue('L1', 'APROVADOR');

        $rows = 2;


        $mEspelho = model('Ponto/EspelhoModel');
        $resAbonos = $mEspelho->ListarAbono();

        if($objListaBatidaApr){
            foreach($objListaBatidaApr as $key => $registro){

                if ($registro['movimento'] == 21 || $registro['movimento'] == 22) {
                    switch($registro['status']){
                        case 10: $status = 'Pend/Ação Gestor'; break;
                        case 2: $status = 'Pend/Ação RH'; break;
                        default: $status = '';
                    }
                }else{
                    $status = 'Pend/Ação Gestor';
                }
                $sheet->setCellValue('A' . $rows, $status);

                $tipo = "";
                switch($registro['movimento']){
                    case 1: $tipo = 'Inclusão de registro'; break;
                    case 2: $tipo = 'Exclusão de registro'; break;
                    case 3: $tipo = 'Alteração de natureza'; break;
                    case 4: $tipo = 'Alteração jornada referência'; break;
                    case 5: $tipo = 'Abono de atrasos'; break;
                    case 6: $tipo = 'Abono de faltas'; break;
                    case 7: $tipo = 'Justificativa de exceção'; break;
                    case 8: $tipo = 'Altera atitude'; break;
                    case 9: $tipo = 'Falta não remunerada'; break;
                    case 21:$tipo =  'Troca de escala'; break;
                    case 22: $tipo =  'Troca de dia'; break;
                    case 61: $tipo =  'Artigo.61'; break;
                }
                $sheet->setCellValue('B' . $rows, $tipo);

                $sheet->setCellValue('C' . $rows, dtBr($registro['dtponto']));
                $sheet->setCellValue('D' . $rows, $registro['chapa'].' - '.$registro['nome']);

                

                /**** descrição do tipo */
                // calcula qtde de horas
                $data_fim = "";
                $total_horas = "";
                if (strlen(trim($registro['abn_codabono'] ?? '')) > 0) {
                    if ($registro['abn_horafim'] > $registro['abn_horaini']) {

                        $data_fim    = dtEn($registro['dtponto'], true) . 'T00:00:00';
                        $total_horas = ($registro['abn_horafim'] - $registro['abn_horaini']);
                    } else {

                        $dataTermino = new \DateTime(dtEn($registro['dtponto'], true));
                        $dataTermino->add(new \DateInterval('P1D'));
                        $data_fim    = $dataTermino->format('Y-m-d');
                        $total_horas = (($registro['abn_horafim'] + 1440) - $registro['abn_horaini']);
                    }
                }
                
                // inclusão de registro
                $descricao_tipo = "";
                if ($registro['movimento'] == 21) {
                    $descricao_tipo .= 'Indice: [' . $registro['codindice'] . '] ';
                    $descricao_tipo .= 'Horário: [' . $registro['horario']. '] ';
                }
                if ($registro['movimento'] == 22) {
                    $descricao_tipo .= 'Data Útil: [' . dtBr($registro['dtponto']) . '] ';
                    $descricao_tipo .= 'Índice Útil: [' . ($registro['codindice']) . '] ';
                    $descricao_tipo .= 'Data Folga: [' . dtBr($registro['dtfolga']) . '] ';
                    $descricao_tipo .= 'Índice Folga: [' . ($registro['codindice_folga']) . '] ';
                    $descricao_tipo .= 'Horário: [' . $registro['horario']. '] ';
                }
                if ($registro['movimento'] != 5 && $registro['movimento'] != 6 && $registro['movimento'] != 8 && $registro['movimento'] != 7 && $registro['movimento'] != 9 && $registro['movimento'] != 21 && $registro['movimento'] != 22) {
                    $descricao_tipo .= 'Registro: [' . sprintf("%05s", m2h($registro['batida'])) . '] ';
                }
                // abono
                if ($registro['movimento'] == 5 || $registro['movimento'] == 6 || $registro['movimento'] == 9) {
                    $descricao_tipo .= 'Data Inicio: [' . date('d/m/Y', strtotime($registro['dtponto'])) . ' ' . sprintf("%05s", m2h($registro['abn_horaini'])) . '] ';
                    $descricao_tipo .= 'Data Fim: [' . date('d/m/Y', strtotime($data_fim)) . ' ' . sprintf("%05s", m2h($registro['abn_horafim'])) . '] ';
                    $descricao_tipo .= 'Total de Horas: [' . m2h($total_horas, 4) . '] ';
                    $descricao_tipo .= 'Tipo de Abono: [' . $registro['abn_codabono'] . ' - ' . (($registro['movimento'] == 9) ? 'FALTA NÃO REMUNERADA' : extrai_valor($resAbonos, $registro['abn_codabono'], 'CODIGO', 'DESCRICAO')) . '] ';
                }
                // altera atitude
                if ($registro['movimento'] == 8 || $registro['movimento'] == 7) {
                    $descricao_tipo .= 'Data: ' . date('d/m/Y', strtotime($registro['atitude_dt'])) . '] ';
                    $descricao_tipo .= 'Horas: ' . sprintf("%05s", m2h($registro['atitude_fim'])) . '] ';
                    if ($registro['movimento'] == 8) {
                        $descricao_tipo .= 'Tipo Atitude: [' . ($registro['atitude_tipo'] == 1 ? 'Compensar (Fica BH)' : 'Descontar no pagto.') . '] ';
                    } else {
                        $descricao_tipo .= 'Tipo Atitude: [Atraso Não Remunerado]';
                    }
                }

                $sheet->setCellValue('E' . $rows, $descricao_tipo);


                $justificativa = "";
                if ($registro['movimento'] == 21 || $registro['movimento'] == 22) {
                    $justificativa = $registro['justificativa_escala'];
                }
                // inclusão de registro
                if ($registro['movimento'] != 5 && $registro['movimento'] != 6 && $registro['movimento'] != 8 && $registro['movimento'] != 7 && $registro['movimento'] != 9 && $registro['movimento'] != 21 && $registro['movimento'] != 22) {
                    if (strlen(trim($registro['motivo'] ?? '')) > 0) $justificativa = $registro['motivo'];
                }
                // abono
                if ($registro['movimento'] == 5 || $registro['movimento'] == 6 || $registro['movimento'] == 9) {
                    if (strlen(trim($registro['justificativa_abono_tipo'] ?? '')) > 0) $justificativa = $registro['justificativa_abono_tipo'];
                }
                // altera atitude
                if ($registro['movimento'] == 8 || $registro['movimento'] == 7) {
                    if ($registro['movimento'] == 8) {
                        if (strlen(trim($registro['justificativa_abono_tipo'] ?? '')) > 0) $justificativa = $registro['atitude_justificativa'];
                    } else {
                        $justificativa = 'Atraso Não Remunerado';
                    }
                }
                $sheet->setCellValue('F' . $rows, $justificativa);


                $possuiAnexo = "Não";
                if ($registro['movimento'] != 5 && $registro['movimento'] != 6 && $registro['movimento'] != 8 && $registro['movimento'] != 7 && $registro['movimento'] != 9 && $registro['movimento'] != 21 && $registro['movimento'] != 22) {
                    if (strlen($registro['possui_anexo'] ?? '') > 0) {
                        $possuiAnexo = 'Sim';
                    }
                }
                // abono
                if ($registro['movimento'] == 5 || $registro['movimento'] == 6 || $registro['movimento'] == 9) {
                    if (strlen($registro['possui_anexo'] ?? '') > 0) {
                        $possuiAnexo = 'Sim';
                    }
                }
                // altera atitude
                if ($registro['movimento'] == 8 || $registro['movimento'] == 7) {
                    if (strlen($registro['possui_anexo'] ?? '') > 0) {
                        $possuiAnexo = 'Sim';
                    }
                }
                // escala
                if ($registro['movimento'] == 21 || $registro['movimento'] == 22) {
                    if (($registro['possui_anexo'] ?? 0) == 1) {
                        $possuiAnexo = 'Sim';
                    }
                }
                $sheet->setCellValue('G' . $rows, $possuiAnexo);


                $sheet->setCellValue('H' . $rows, $registro['batidas_dia']);
                $sheet->setCellValue('I' . $rows, (strlen(trim($registro['data_referencia'])) > 0 ? dtBr($registro['data_referencia']) : ''));
                $sheet->setCellValue('J' . $rows, dtBr($registro['data_solicitacao']));
                $sheet->setCellValue('K' . $rows, $registro['chapa_solicitante'].' - '.$registro['solicitante']);
                // $sheet->setCellValue('L' . $rows, $registro['chapa_gestor'].' - '.$registro['nome_gestor']);

                // $spreadsheet->getActiveSheet()->getStyle('C'.$rows)->applyFromArray(($SaldoBancoHoras['SALDO'] < 0) ? $styleRed : $styleGreen);
                $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray($styleBorda);
                $rows++;
            }
        }

        for($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Disposition: attachment; filename=Aprovação de Ponto.xlsx' );
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer->save("php://output");

        exit();

    }

  //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;
        
        switch($act){
            
            //-------------------------------------
            case 'visualiza_anexo':

                exit(json_encode($this->mAprova->ListaDadosAnexos($dados)));
                break;
            //-------------------------------------
            case 'listar_funcionarios_secao':

                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit(json_encode($this->mAprova->ListarFuncionariosSecao($dados['codsecao'], $dados)));
                break;

            //-------------------------------------
            // calcula requisição de Art.61
            case 'calcula_req':
              exit($this->mAprova->Calcular_Req($dados['id']));
              break;
            //-------------------------------------

            //-------------------------------------
            // sincroniza requisição de Art.61
            case 'sincArt61RM':
              exit($this->mAprova->SincArt61RM($dados));
              break;
            //-
            
            //-------------------------------------
            // cancela sincronismo requisição de Art.61
            case 'cancSincArt61RM':
              exit($this->mAprova->CancSincArt61RM($dados));
              break;
            //-
   
            //-------------------------------------
            // envia telegrama de abandono de emprgo
            case 'enviaTelegrama':
              exit($this->mAprova->EnviaTelegrama($dados['id']));
              break;
            //-

            //-------------------------------------
            // recusa envio de telegrama de abandono de emprgo
            case 'recusaTelegrama':
              exit($this->mAprova->RecusaTelegrama($dados['id'], $dados['justificativa']));
              break;
            //-
   
        }        

    }

    //-----------------------------------------------------------
    // Workflow de envio de emails pendentes de aprovação
    //-----------------------------------------------------------
    public function workflow(){
        $this->mAprova->Workflow();
        exit();
    }

    //-----------------------------------------------------------
    // Workflow de envio de emails pendentes de aprovação do Artigo 61
    //-----------------------------------------------------------
    public function workflow_art61(){
        $this->mAprova->Workflow_Art61();
        exit();
    }

    //-----------------------------------------------------------
    // Workflow de envio de emails para gestores aprovar envio de 
    // aviso para colaboradores com mais de 10 faltas consecutivas
    //-----------------------------------------------------------
    public function workflow_faltas(){
        $this->mAprova->Workflow_Faltas();
        exit();
    }

    //-----------------------------------------------------------
    // Tela para gestor responder se RH deve enviar telegrama de
    // abandono de emprego
    //-----------------------------------------------------------
    public function telegrama($id, $random = ''){

      $dados['_titulo'] = "Faltas Consecutivas - Abandono de Emprego";
      $dados['resFaltas'] = $this->mAprova->ListarFaltas($id);
      $dados['botoes'] = "N";

      if($dados['resFaltas'] and strlen($random)>10) {
        if($dados['resFaltas'][0]['status'] == 'FINALIZADO') {
          $dados['msg'] = "<br>Este registro de Faltas Consecutivas já foi finalizado.";
        } else {
          $dados['botoes'] = "S";
          $dados['msg'] = "<br>O(a) colaborador(a) <strong>".$dados['resFaltas'][0]['nome_colab']."</strong>, de chapa <strong>".$dados['resFaltas'][0]['chapa_colab']."</strong> e função <strong>".$dados['resFaltas'][0]['funcao_colab']."</strong>, está com <strong>".$dados['resFaltas'][0]['faltas_finais']."</strong> faltas consecutivas desde <strong>". date( 'd/m/Y' , strtotime( $dados['resFaltas'][0]['prim_falta'] ))."</strong>.<br><br><strong>Confirma o envio de telegrama de abandono de emprego?</strong><br>";
        }
      } else {
        $dados['msg'] = "<br>Registro de Faltas Consecutivas não encontrado.";
      }

      $dados['id'] = $id;
      
      return parent::ViewPortal('ponto/aprova/telegrama', $dados);
    }

}