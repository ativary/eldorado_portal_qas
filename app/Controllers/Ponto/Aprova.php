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

  public function __construct()
  {
    parent::__construct('Ponto'); // sempre manter

    $this->_moduloName = '<i class="fas fa-clock"></i> Ponto';
    $this->mAprova = model('Ponto/AprovaModel');
    $this->mCritica = model('Ponto/CriticaModel');
    $this->mPortal = model('PortalModel');
  }

  public function index()
  {

    parent::VerificaPerfil('PONTO_APROVA');
    
    $dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

    $dados['_titulo'] = "Aprovar ponto";
    $this->_breadcrumb->add($dados['_titulo'], 'ponto/aprova');
    
    $dados['chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

    $mEspelho = model('Ponto/EspelhoModel');
    $dados['resAbonos'] = $mEspelho->ListarAbono();

    $dados['EspelhoConfiguracao'] = $mEspelho->ListarEspelhoConfiguracao();
    $dados['gestorPossuiExcecao'] = $mEspelho->gestorPossuiExcecao();

    if(!$dados['EspelhoConfiguracao']){
        notificacao('warning2', 'Configuração de ponto não localizada.');
        redireciona(base_url('ponto/espelho/editar'));
        return false;
    }

        $idbatida   = $this->request->getPost('idbatida');
        $act        = $this->request->getPost('act');
        $codsecao   = $this->request->getPost('secao');
        $tipo_abono = $this->request->getPost('tipo_abono');
        $ft_legenda = $this->request->getPost('ft_legenda');
        $ft_status  = $this->request->getPost('ft_status');
        $chapa      = $this->request->getPost('funcionario');
        $motivo_reprova = $this->request->getPost('motivo_reprova');

        if($motivo_reprova == null){
            $dados['periodo']             = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), 0, -1) : null;
            $dados['statusPeriodo']       = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), -1) : 0;
        }else{
            $dados['periodo']             = $this->request->getPost('periodo');
            $dados['statusPeriodo']       = ($this->request->getPost('statusPeriodo') != null) ? $this->request->getPost('statusPeriodo') : 0;
        }

        $dados['codsecao'] = $codsecao;
        $objListaBatidaApr = false;

        $colaboradores = [];

        if($dados['periodo']){
            $dadosColaboradores['rh']         = $dados['perfilRH'];
            $dadosColaboradores['codsecao']   = ($dados['codsecao'] == 'all') ? null : $dados['codsecao'];
            $dataInicio                       = substr($dados['periodo'],0,10);
            $colaboradores                    = $this->mPortal->CarregaColaboradores($dataInicio, $dadosColaboradores, false);
        }

        $resFuncionarioSecao = $colaboradores;
        
        // $resFuncionarioSecao = $this->mAprova->ListarFuncionariosSecao('all', $dados);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
        
            // APROVA BATIDA RH COM RM
            if ($act == 'apr') {

                    $sucesso = 0;
                    $erro = '';

                    $array_recalculo = array();
                    foreach ($idbatida as $id_batida) {

                        $dados_post = explode('|', $id_batida);

                        $index = $dados_post[0].'_'.$dados_post[1];
                        
                        if(!isset($array_recalculo[$index])){
                            $array_recalculo[$index] = array(
                                'chapa' => $dados_post[0],
                                'data' => $dados_post[1]
                            );
                        }

                        $RESULT = $this->mAprova->aprovaBatidaRH($dados_post[2], $dados['perfilRH']);

                        if($RESULT === true){
                            $sucesso++;
                        }else{
                            $erro .= $RESULT . '<br>';
                        }
                    }

                    if($sucesso > 0 && strlen($erro) > 0){
                        notificacao('warning', 'Batida(s) aprovada(s) com sucesso! Porém, ocorreram falha parcial na aprovação.'.$erro);
                    }

                    if($sucesso == 0 && strlen($erro) > 0){
                        notificacao('danger', 'Erro ao aprovar a(s) batida(s) | Erro: '. $erro);
                    }

                    if(strlen($erro) == 0 && $sucesso > 0){
                        notificacao('success', 'Batida(s) aprovada(s) com sucesso!');
                    }

                    // processa o recalculo
                    //$this->mAprova->RecalculoPonto($array_recalculo);
                    redireciona(base_url('ponto/aprova'));
                    exit();

                    //return responseJson('success', 'Batida(s) aprovada(s) com sucesso!');
                
            }

            // APROVA BATIDA GESTOR
            if ($act == 'aprGestor') {
                foreach ($idbatida as $id_batida) {
                    $this->mAprova->aprovaBatidaGestor($id_batida, 'GESTOR');
                }
                notificacao('success', 'Batida aprovada com sucesso.');
            }

            // REPROVA BATIDA
            if ($act == 'rep') {
                $resp = 0;
                foreach ($idbatida as $id_batida) {
                    $result = $this->mAprova->reprovaBatidaRH($id_batida, 'RH', $motivo_reprova, $dados['perfilRH']);
                    if(!$result) $resp = 1;
                }

                if($resp > 0){
                    notificacao('warning', 'Movimento reprovado com sucesso. Porém, ocorreram falha parcial na reprovação.');
                }else{
                    notificacao('success', 'Movimento reprovado com sucesso.');
                }
                
                //return responseJson('success', 'Batida reprovada com sucesso.');
            }
            $objListaBatidaApr = $this->mAprova->listaBatidaApr(3, $codsecao, false, $tipo_abono, $ft_legenda, $ft_status, false, false, $chapa, $dados['periodo'], $dados);
            if($codsecao != null) $resFuncionarioSecao = $this->mAprova->listaFuncionarioSecao($codsecao, $dados);
        }

       //$objListaBatidaApr = $this->mAprova->listaBatidaApr(3, $codsecao, false, $tipo_abono, $ft_legenda, $ft_status);

        $nomeFunc = array();
        if ($objListaBatidaApr) {
            foreach ($objListaBatidaApr as $idxx => $value) {
                $nomeFunc[] = $objListaBatidaApr[$idxx]['nomechapa'];
            }
        }

        $nomeFunc = array_unique($nomeFunc);

        $listaSecaoUsuarioRM = $this->mAprova->listaSecaoUsuario(false, $dados);
        $objSecaoUsu         = $this->mAprova->listaSecaoUsu($_SESSION["log_id"]);

        $dados['objListaBatidaApr']   = $objListaBatidaApr;
        $dados['nomeFunc']            = $nomeFunc;
        $dados['listaSecaoUsuarioRM'] = $listaSecaoUsuarioRM;
        $dados['objSecaoUsu']         = $objSecaoUsu;
        $dados['resFuncionarioSecao'] = $resFuncionarioSecao;
        $dados['chapa']               = $chapa;
        $dados['resPeriodo']          = $this->mCritica->ListarCriticaPeriodoRM();

        $mEspelho = model('Ponto/EspelhoModel');
        $dados['resMotivoReprova']   = $mEspelho->ListarJustificativa(5);
        $dados['isGestorOrLider']   = $mEspelho->isGestorOrLider($dados);
        $dados['isGestor']          = $mEspelho->isGestor($dados);

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
        if (isset($_POST['tipo_abono']))    $tipo_abono = $_POST['tipo_abono'];
        if (isset($_POST['ft_legenda']))    $ft_legenda = $_POST['ft_legenda'];
        if (isset($_POST['ft_status']))      $ft_status = $_POST['ft_status'];
        if (isset($_POST['ft_dtini'])) $ft_dtini = $_POST['ft_dtini'];
        if (isset($_POST['ft_dtfim'])) $ft_dtfim = $_POST['ft_dtfim'];

        $objListaBatidaApr = false;
        if($codsecao){
            $objListaBatidaApr = $this->mAprova->listaBatidaApr('S', $codsecao, false, $tipo_abono, $ft_legenda, 'S', $ft_dtini, $ft_dtfim);
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

  public function excel()
    {
        parent::VerificaPerfil('PONTO_APROVA');
        $dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

        // $codsecao               = $this->request->getPost('codsecao');
        // $chapa                  = $this->request->getPost('funcionario');
        // $periodo                  = $this->request->getPost('periodo');
        // $objListaBatidaApr      = $this->mAprova->listaBatidaApr(3, $codsecao, false, false, false, false, false, false, $chapa, $periodo, $dados);

        $codsecao   = $this->request->getPost('secao');
        $tipo_abono = $this->request->getPost('tipo_abono');
        $ft_legenda = $this->request->getPost('ft_legenda');
        $ft_status  = $this->request->getPost('ft_status');
        $chapa      = $this->request->getPost('funcionario');
        $motivo_reprova = $this->request->getPost('motivo_reprova');

        if($motivo_reprova == null){
            $dados['periodo']             = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), 0, -1) : null;
            $dados['statusPeriodo']       = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), -1) : 0;
        }else{
            $dados['periodo']             = $this->request->getPost('periodo');
            $dados['statusPeriodo']       = ($this->request->getPost('statusPeriodo') != null) ? $this->request->getPost('statusPeriodo') : 0;
        }

        $objListaBatidaApr      = $this->mAprova->listaBatidaApr(3, $codsecao, false, $tipo_abono, $ft_legenda, $ft_status, false, false, $chapa, $dados['periodo'], $dados);
// echo '<pre>';
// print_r($objListaBatidaApr);
//         exit();
        // $objListaBatidaApr      = $this->mAprova->listaBatidaApr(3, $codsecao, false, false, false, false);
        $resFuncionarioSecao    = $this->mAprova->ListarFuncionariosSecao($codsecao);

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

        $spreadsheet->getActiveSheet()->getStyle('A1:N1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A1:N1')->applyFromArray($styleBorda);

        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A1:N1')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');

        // nome da aba da planilha
        $spreadsheet->getActiveSheet()->setTitle('Aprovação de Ponto');
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:N1'); // auto filtro no titulo

        // titulo das colunas
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'CHAPA');
        $sheet->setCellValue('B1', 'COLABORADOR');
        $sheet->setCellValue('C1', 'CHAPA GESTOR');
        $sheet->setCellValue('D1', 'NOME GESTOR');
        $sheet->setCellValue('E1', 'MOVIMENTO');
        $sheet->setCellValue('F1', 'DATA');
        $sheet->setCellValue('G1', 'BATIDA');
        $sheet->setCellValue('H1', 'ABONO DATA INICIO');
        $sheet->setCellValue('I1', 'ABONO DATA FIM');
        $sheet->setCellValue('J1', 'TOTAL HORAS');
        $sheet->setCellValue('K1', 'TIPO ABONO');
        $sheet->setCellValue('L1', 'JUSTIFICATIVA');
        $sheet->setCellValue('M1', 'ANEXO');
        $sheet->setCellValue('N1', 'SOLICITANTE');

        $rows = 2;


        $mEspelho = model('Ponto/EspelhoModel');
        $resAbonos = $mEspelho->ListarAbono();

        if($objListaBatidaApr){
            foreach($objListaBatidaApr as $key => $Batidas){

                $sheet->setCellValue('A' . $rows, $Batidas['chapa']);
                $sheet->setCellValue('B' . $rows, $Batidas['nome']);
                $sheet->setCellValue('C' . $rows, $Batidas['chapa_gestor']);
                $sheet->setCellValue('D' . $rows, $Batidas['nome_gestor']);

                

                $movimento = "";
                switch ($Batidas['movimento']) {
                    case 1:
                        $movimento ='Inclusão de batida';
                        break;
                    case 2:
                        $movimento ='Exclusão de batida';
                        break;
                    case 3:
                        $movimento= 'Alteração de natureza';
                        break;
                    case 4:
                        $movimento ='Alteração jornada referência';
                        break;
                    case 5:
                        $movimento = 'Abono de atrasos';
                        break;
                    case 6:
                        $movimento= 'Abono de faltas';
                        break;
                    case 7:
                        $movimento = 'Justificativa de Exceção';
                        break;
                    case 8:
                        $movimento = 'Altera Atitude';
                        break;
                    case 9:
                        $movimento = 'Falta não remunerada';
                        break;
                }


                $tipo_abono = $Batidas['abn_codabono'] . ' - ' . (($Batidas['movimento'] == 9) ? 'FALTA NÃO REMUNERADA' : extrai_valor($resAbonos, $Batidas['abn_codabono'], 'CODIGO', 'DESCRICAO'));


                $total_horas = "";
                if (strlen(trim($Batidas['abn_codabono'] ?? '')) > 0) {
                    if ($Batidas['abn_horafim'] > $Batidas['abn_horaini']) {
                
                        $data_fim    = dtEn($Batidas['dtponto'], true) . 'T00:00:00';
                        $total_horas = ($Batidas['abn_horafim'] - $Batidas['abn_horaini']);
                        $total_horas = m2h($total_horas, 4);
                    } else {
                
                        $dataTermino = new \DateTime(dtEn($Batidas['dtponto'], true));
                        $dataTermino->add(new \DateInterval('P1D'));
                        $data_fim    = $dataTermino->format('Y-m-d');
                        $total_horas = (($Batidas['abn_horafim'] + 1440) - $Batidas['abn_horaini']);
                        $total_horas = m2h($total_horas, 4);
                    }
                }

                $justificativa = "";
                if ($Batidas['movimento'] != 8 && $Batidas['movimento'] != 7) {
                    $justificativa = (strlen(trim($Batidas['justificativa_abono_tipo'])) > 0 ? $Batidas['justificativa_abono_tipo'] : $Batidas['motivo']);
                } else {
                    if ($Batidas['movimento'] == 8) {
                        $justificativa = $Batidas['atitude_justificativa'];
                    } else {
                        $justificativa = 'Atraso Não Remunerado';
                    }
                }


                $sheet->setCellValue('E' . $rows, $movimento);
                $sheet->setCellValue('F' . $rows, dtBr($Batidas['dtponto']));
                $sheet->setCellValue('G' . $rows, (($Batidas['movimento'] == 5 || $Batidas['movimento'] == 6 || $Batidas['movimento'] == 9)) ? '' : m2h($Batidas['batida']));
                $sheet->setCellValue('H' . $rows, (($Batidas['movimento'] == 5 || $Batidas['movimento'] == 6 || $Batidas['movimento'] == 9)) ? date('d/m/Y', strtotime($Batidas['dtponto'])).' '.sprintf("%05s", m2h($Batidas['abn_horaini'])) : '');
                $sheet->setCellValue('I' . $rows, (($Batidas['movimento'] == 5 || $Batidas['movimento'] == 6 || $Batidas['movimento'] == 9)) ? date('d/m/Y', strtotime($Batidas['dtponto'])).' '.sprintf("%05s", m2h($Batidas['abn_horafim'])) : '');
                $sheet->setCellValue('J' . $rows, $total_horas);
                $sheet->setCellValue('K' . $rows, (($Batidas['movimento'] == 5 || $Batidas['movimento'] == 6 || $Batidas['movimento'] == 9)) ? $tipo_abono : '');
                $sheet->setCellValue('L' . $rows, $justificativa);
                $sheet->setCellValue('M' . $rows, (strlen($Batidas['possui_anexo'] ?? '') > 0) ? 'Sim' : 'Não');
                $sheet->setCellValue('N' . $rows, $Batidas['solicitante']);
                
                // $spreadsheet->getActiveSheet()->getStyle('C'.$rows)->applyFromArray(($SaldoBancoHoras['SALDO'] < 0) ? $styleRed : $styleGreen);
                $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':N'.$rows)->applyFromArray($styleBorda);
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
        }        

    }

}