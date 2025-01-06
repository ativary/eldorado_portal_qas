<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

set_time_limit(60*60);

Class Critica extends BaseController {

    private $mCritica;
    private $mPortal;

    public function __construct()
    {
      parent::__construct('Ponto'); // sempre manter
        
      $this->_moduloName = '<i class="fas fa-clock"></i> Crítica';
      $this->mCritica = model('Ponto/CriticaModel');
      $this->mPortal    = model('PortalModel');

    }

    public function index(){

      parent::VerificaPerfil('PONTO_CRITICA');
      $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

      // if($periodo == 1) return true;

      $dados['_titulo'] = "Crítica do ponto";
      $this->_breadcrumb->add($dados['_titulo'], 'ponto/critica');

      $dados['is_Gestor'] = false;
      
      $dados['chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

      $dados['resFuncionario']      = $this->mCritica->listaFuncionarios($dados);
      $colaboradores = [];
      
      $dados['resPeriodo']          = $this->mCritica->ListarCriticaPeriodoRM();
      $dados['periodo']             = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), 0, -1) : null;
      $dados['statusPeriodo']       = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), -1) : 0;
      $dados['data_inicio']         = $this->request->getPost('data_inicio');
      $dados['data_fim']            = $this->request->getPost('data_fim');
      $dados['ck_ExtraExecutado']   = $this->request->getPost('ck_ExtraExecutado');
      $dados['ck_semPar']           = $this->request->getPost('ck_semPar');
      $dados['ck_Atrasos']          = $this->request->getPost('ck_Atrasos');
      $dados['ck_Faltas']           = $this->request->getPost('ck_Faltas');
      $dados['ck_jorMaior10']       = $this->request->getPost('ck_jorMaior10');
      $dados['ck_interjornada']     = $this->request->getPost('ck_interjornada');
      $dados['vl_extra_executado']  = $this->request->getPost('vl_extra_executado');
      $dados['vl_atrasos']          = $this->request->getPost('vl_atrasos');
      $dados['funcionario']         = $this->request->getPost('funcionario');
      $dados['ck_bancohoras']       = $this->request->getPost('ck_bancohoras');
      $dados['secao']               = $this->request->getPost('secao');

      $mAprova = model('Ponto/AprovaModel');
      $dados['listaSecaoUsuarioRM'] = $mAprova->listaSecaoUsuario(false, $dados);

      $periodo_ativo = false;
      $dados['resJustificativaAjuste'] = false;
      $dados['resAbono'] = false;
      $dados['EspelhoConfiguracao'] = false;
      if($dados['periodo'] !== NULL && $dados['data_inicio']  !==  NULL && $dados['data_fim'] !== NULL){

        if((int)substr($dados['periodo'],-1) == 1) $periodo_ativo = true;
        $dados['periodo_ativo']             = $periodo_ativo;
        $dados['resBatidas']                = $this->mCritica->listaBatidasCritica($dados);
        $dados['resData']                   = $this->mCritica->listaDataCritica($dados);
        $dados['resBatidasApontadas']       = $this->mCritica->listaBatidasApontadaCritica($dados);
        $dados['resSolicitacaoAbonoFalta']  = $this->mCritica->listaAbonoCritica(dtEn($dados['data_inicio'], true), dtEn($dados['data_fim'], true), 6);
        $dados['resSolicitacaoAbonoAtraso'] = $this->mCritica->listaAbonoCritica(dtEn($dados['data_inicio'], true), dtEn($dados['data_fim'], true), 5);
        $dados['resSolicitacaoAbonoAtitude'] = $this->mCritica->listaAbonoCritica(dtEn($dados['data_inicio'], true), dtEn($dados['data_fim'], true), 8);
        
        $mEspelho = model('Ponto/EspelhoModel');
        $dados['EspelhoConfiguracao']     = $mEspelho->ListarEspelhoConfiguracao();
        $dados['resJustificativaExtra']   = $mEspelho->ListarJustificativa(3);
        $dados['resJustificativaAjuste']  = $mEspelho->ListarJustificativa(4);
        $dados['resAbono']                = $mEspelho->ListarAbono();
        $dados['gestorPossuiExcecao']     = $mEspelho->gestorPossuiExcecao();
        $dados['isGestorOrLider']         = $mEspelho->isGestorOrLider($dados);
        $dados['isGestor']                = $mEspelho->isGestor($dados);
        if(!$dados['EspelhoConfiguracao']){
            notificacao('warning2', 'Configuração de ponto não localizada.');
            redireciona(base_url('ponto/espelho/editar'));
            return false;
        }

        $dadosColaboradores['rh']         = $dados['rh'];
        $dadosColaboradores['codsecao']   = $dados['secao'];
        $dataInicio                       = \DateTime::createFromFormat('d/m/Y', substr($dados['periodo'],0,10))->format('Y-m-d');
        $colaboradores                    = $this->mPortal->CarregaColaboradores($dataInicio, $dadosColaboradores);

      }
      else{
        $dados['resBatidas']          = false;
        $dados['resData']             = false;
        $dados['resBatidasApontadas'] = false;
      }

      $dados['resFuncionarioSecao'] = $colaboradores;
      
      return parent::ViewPortal('ponto/critica/index', $dados);
  }

  public function excel()
  {

    parent::VerificaPerfil('PONTO_CRITICA');
      $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

      // if($periodo == 1) return true;

      $dados['_titulo'] = "Crítica do ponto";
      $this->_breadcrumb->add($dados['_titulo'], 'ponto/critica');

      $dados['is_Gestor'] = false;
      
      $dados['chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

      $dados['resFuncionario']      = $this->mCritica->listaFuncionarios($dados);
      $dados['resFuncionarioSecao'] = $this->mPortal->ListaFuncionarioSecao($dados);
      $dados['resPeriodo']          = $this->mCritica->ListarCriticaPeriodoRM();
      $dados['periodo']             = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), 0, -1) : null;
      $dados['statusPeriodo']       = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), -1) : 0;
      $dados['data_inicio']         = $this->request->getPost('data_inicio');
      $dados['data_fim']            = $this->request->getPost('data_fim');
      $dados['ck_ExtraExecutado']   = $this->request->getPost('ck_ExtraExecutado');
      $dados['ck_semPar']           = $this->request->getPost('ck_semPar');
      $dados['ck_Atrasos']          = $this->request->getPost('ck_Atrasos');
      $dados['ck_Faltas']           = $this->request->getPost('ck_Faltas');
      $dados['ck_jorMaior10']       = $this->request->getPost('ck_jorMaior10');
      $dados['ck_interjornada']     = $this->request->getPost('ck_interjornada');
      $dados['vl_extra_executado']  = $this->request->getPost('vl_extra_executado');
      $dados['vl_atrasos']          = $this->request->getPost('vl_atrasos');
      $dados['funcionario']         = $this->request->getPost('funcionario');
      $dados['ck_bancohoras']       = $this->request->getPost('ck_bancohoras');
      $dados['secao']               = $this->request->getPost('secao');

      $mAprova = model('Ponto/AprovaModel');
      $dados['listaSecaoUsuarioRM'] = $mAprova->listaSecaoUsuario(false, $dados);

      $periodo_ativo = false;
      $dados['resJustificativaAjuste'] = false;
      $dados['resAbono'] = false;
      $dados['EspelhoConfiguracao'] = false;
      if($dados['periodo'] !== NULL && $dados['data_inicio']  !==  NULL && $dados['data_fim'] !== NULL){

        if((int)substr($dados['periodo'],-1) == 1) $periodo_ativo = true;
        $dados['periodo_ativo']             = $periodo_ativo;
        $dados['resBatidas']                = $this->mCritica->listaBatidasCritica($dados);
        $dados['resData']                   = $this->mCritica->listaDataCritica($dados);
        $resBatidasApontadas       = $this->mCritica->listaBatidasApontadaCritica($dados);

        $resSolicitacaoAbonoFalta  = $this->mCritica->listaAbonoCritica(dtEn($dados['data_inicio'], true), dtEn($dados['data_fim'], true), 6);
        $resSolicitacaoAbonoAtraso = $this->mCritica->listaAbonoCritica(dtEn($dados['data_inicio'], true), dtEn($dados['data_fim'], true), 5);
        $resSolicitacaoAbonoAtitude = $this->mCritica->listaAbonoCritica(dtEn($dados['data_inicio'], true), dtEn($dados['data_fim'], true), 8);
        
        $mEspelho = model('Ponto/EspelhoModel');
        $dados['EspelhoConfiguracao']     = $mEspelho->ListarEspelhoConfiguracao();
        $dados['resJustificativaExtra']   = $mEspelho->ListarJustificativa(3);
        $dados['resJustificativaAjuste']  = $mEspelho->ListarJustificativa(4);
        $dados['resAbono']                = $mEspelho->ListarAbono();
        $dados['gestorPossuiExcecao']     = $mEspelho->gestorPossuiExcecao();
        $dados['isGestorOrLider']         = $mEspelho->isGestorOrLider($dados);
        $dados['isGestor']                = $mEspelho->isGestor($dados);
        if(!$dados['EspelhoConfiguracao']){
            notificacao('warning2', 'Configuração de ponto não localizada.');
            redireciona(base_url('ponto/espelho/editar'));
            return false;
        }
      }
      else{
        $dados['resBatidas']          = false;
        $dados['resData']             = false;
        $dados['resBatidasApontadas'] = false;
      }

      $spreadsheet = new Spreadsheet();

      // titulo das colunas 17 colunas
      $colunas = array(
        1 => 'M',
        2 => 'N',
        3 => 'O',
        4 => 'P',
        5 => 'Q',
        6 => 'R',
        7 => 'S'
      );

      $sheet = $spreadsheet->getActiveSheet();
      //legenda
      $sheet->setCellValue('B1', 'Pendente Aprovação');
      $sheet->setCellValue('B2', 'Pendente Aprovação RH');
      $sheet->setCellValue('B3', 'Ação Reprovada');

      $spreadsheet
      ->getActiveSheet()
      ->getStyle('A1')
      ->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()
      ->setARGB('feca07');
      $spreadsheet
      ->getActiveSheet()
      ->getStyle('A2')
      ->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()
      ->setARGB('dbdcdd');
      $spreadsheet
      ->getActiveSheet()
      ->getStyle('A3')
      ->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()
      ->setARGB('f4811f');


      $sheet->setCellValue('A4', 'CHAPA');
      $sheet->setCellValue('B4', 'NOME');
      $sheet->setCellValue('C4', 'SITUAÇÃO');
      $sheet->setCellValue('D4', 'DIA');
      $sheet->setCellValue('E4', 'DATA');
      $sheet->setCellValue('F4', 'ENT1');
      $sheet->setCellValue('G4', 'SAI1');
      $sheet->setCellValue('H4', 'ENT2');
      $sheet->setCellValue('I4', 'SAI2');
      $sheet->setCellValue('J4', 'ENT3');
      $sheet->setCellValue('K4', 'SAI3');
      $sheet->setCellValue('L4', 'JUSTIFICATIVA');

      $n = 0;
      if(strlen(trim($dados['ck_ExtraExecutado'])) > 0){$n++; $sheet->setCellValue($colunas[$n].'4', 'EXTRA EXECUTADO');}
      if(strlen(trim($dados['ck_semPar'])) > 0){$n++; $sheet->setCellValue($colunas[$n].'4', 'SEM PAR CORRÊSPONDENTE');}
      if(strlen(trim($dados['ck_Atrasos'])) > 0){$n++; $sheet->setCellValue($colunas[$n].'4', 'ATRASO');}
      if(strlen(trim($dados['ck_Faltas'])) > 0){$n++; $sheet->setCellValue($colunas[$n].'4', 'FALTA');}
      if(strlen(trim($dados['ck_jorMaior10'])) > 0){$n++; $sheet->setCellValue($colunas[$n].'4', 'JOR. MAIOR QUE 10h');}
      if(strlen(trim($dados['ck_interjornada'])) > 0){$n++; $sheet->setCellValue($colunas[$n].'4', 'INTERJORNADA');}

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

    $spreadsheet->getActiveSheet()->getStyle('A4:'.$colunas[$n].'4')->applyFromArray($styleArray);
    $spreadsheet->getActiveSheet()->getStyle('A4:'.$colunas[$n].'4')->applyFromArray($styleBorda);

    $spreadsheet
    ->getActiveSheet()
    ->getStyle('A4:'.$colunas[$n].'4')
    ->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()
    ->setARGB('006f49');

    // nome da aba da planilha
    $spreadsheet->getActiveSheet()->setTitle('Crítica de Ponto');
    $spreadsheet->getActiveSheet()->setAutoFilter('A4:'.$colunas[$n].'4'); // auto filtro no titulo

    $rows = 2;

      if ($dados['resData']) {
        $nLinha = 1;
        $resData = $dados['resData'];
        // echo '<pre>';
        // print_r($resData);
        // exit();
        $resBatidas = $dados['resBatidas'];
        unset($dados['resData']);
        unset($dados['resBatidas']);
        $rows = 5;
        foreach ($resData as $idbData => $value) {
          $i = $idbData;

          $b1 = '';
          $b2 = '';
          $b3 = '';
          $b4 = '';
          $b5 = '';
          $b6 = '';

          $status_1 = '';
          $status_2 = '';
          $status_3 = '';
          $status_4 = '';
          $status_5 = '';
          $status_6 = '';

          $status_portal_1 = '';
          $status_portal_2 = '';
          $status_portal_3 = '';
          $status_portal_4 = '';
          $status_portal_5 = '';
          $status_portal_6 = '';

          $array_batidas = array();

          $coluna = 1;
          
          if (isset($resBatidas) && is_array($resBatidas) > 0) :

            
            foreach ($resBatidas as $idb => $value) :
              $bat_data = $resBatidas[$idb]['DATAREFERENCIA'];
              $bat_chapa = $resBatidas[$idb]['CHAPA'];
              $bat_natureza = $resBatidas[$idb]['NATUREZA'];
              $bat_idaafdt = $resBatidas[$idb]['IDAAFDT'];
              $bat_batida = $resBatidas[$idb]['BATIDA'];
              $bat_status = $resBatidas[$idb]['STATUS'];


              if (strlen(trim($resBatidas[$idb]['DATAREFERENCIA2'])) > 0) {
                $bat_data2 = date('d/m/Y', strtotime($resBatidas[$idb]['DATAREFERENCIA2']));
              } else {
                $bat_data2 = "Não pode ficar em branco";
              }

              if (dtEn($bat_data, true) == dtEn($resData[$i]['DATA'], true) && $bat_chapa == $resData[$i]['CHAPA']) {
                

                if ($coluna > 8) $coluna = 1;

                if ($bat_natureza == 0) {
                  

                  switch ($coluna) {
                    case 1:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'ent1',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b1 = '<span id="a' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'a' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b1 = sprintf("%05s", m2h($bat_batida));
                      $status_1 = $bat_status;
                      $coluna = 2;
                      break;
                    case 2:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'ent2',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b3 = '<span id="c' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'c' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b3 = sprintf("%05s", m2h($bat_batida));
                      $status_3 = $bat_status;
                      $coluna = 4;
                      break;
                    case 3:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'ent2',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b3 = '<span id="c' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'c' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b3 = sprintf("%05s", m2h($bat_batida));
                      $status_3 = $bat_status;
                      break;
                    case 4:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'ent3',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b5 = '<span id="e' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'e' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b5 = sprintf("%05s", m2h($bat_batida));
                      $status_5 = $bat_status;
                      $coluna = 6;
                      break;
                    case 5:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'ent3',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b5 = '<span id="e' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'e' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b5 = sprintf("%05s", m2h($bat_batida));
                      $status_5 = $bat_status;
                      $coluna = 6;
                      break;
                    case 6:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'ent4',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b7 = '<span id="g' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'g' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b7 = sprintf("%05s", m2h($bat_batida));
                      $coluna = 8;
                      $status_7 = $bat_status;
                      break;
                    case 7:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'ent4',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b7 = '<span id="g' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'g' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b7 = sprintf("%05s", m2h($bat_batida));
                      $status_7 = $bat_status;
                      $coluna = 8;
                      break;
                    case 8:
                      // $b9 = '<span id="i' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'i' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b9 = sprintf("%05s", m2h($bat_batida));
                      $coluna = 1;
                      break;
                  }
                } else {



                  switch ($coluna) {
                    case 1:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai1',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b2 = '<span id="b' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'b' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b2 = sprintf("%05s", m2h($bat_batida));
                      $coluna = 2;
                      $status_2 = $bat_status;
                      break;
                    case 2:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai1',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b2 = '<span id="b' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'b' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b2 = sprintf("%05s", m2h($bat_batida));
                      $status_2 = $bat_status;
                      $coluna = 3;
                      break;
                    case 3:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai2',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b4 = '<span id="d' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'d' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b4 = sprintf("%05s", m2h($bat_batida));
                      $status_4 = $bat_status;
                      $coluna = 5;
                      break;
                    case 4:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai2',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b4 = '<span id="d' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'d' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b4 = sprintf("%05s", m2h($bat_batida));
                      $status_4 = $bat_status;
                      break;
                    case 5:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai3',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b6 = '<span id="f' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'f' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b6 = sprintf("%05s", m2h($bat_batida));
                      $status_6 = $bat_status;
                      $coluna = 7;
                      break;
                    case 6:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai3',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b6 = '<span id="f' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'f' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b6 = sprintf("%05s", m2h($bat_batida));
                      $status_6 = $bat_status;
                      break;
                    case 7:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai4',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b8 = '<span id="h' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'h' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b8 = sprintf("%05s", m2h($bat_batida));
                      $coluna = 8;
                      $status_8 = $bat_status;
                      break;
                    case 8:
                      $array_batidas[] = array(
                        'batida'          => sprintf("%05s", m2h($bat_batida)),
                        'natureza'        => $bat_natureza,
                        'pendente'        => 0,
                        'data_referencia' => dtEn($bat_data, true),
                        'status'          => 'C',
                        'campo'            => 'sai4',
                        'idaafdt'         => (int)$bat_idaafdt
                      );
                      // $b8 = '<span id="h' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'h' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
                      $b8 = sprintf("%05s", m2h($bat_batida));
                      $status_8 = $bat_status;
                      break;
                  }
                }

                // unset($resBatidas[$idb]);
              }
              // unset($resBatidas[$idb]);
            endforeach;
          endif;

          if (isset($resBatidasApontadas) && is_array($resBatidasApontadas) > 0) {
            foreach ($resBatidasApontadas as $idba => $value) :
              $batapt_chapa = $resBatidasApontadas[$idba]['chapa'];
              $batapt_dtponto = $resBatidasApontadas[$idba]['dtponto'];

              if (
                $batapt_chapa == $resData[$i]['CHAPA'] &&
                date('d/m/Y', strtotime($batapt_dtponto)) == date('d/m/Y', strtotime($resData[$i]['DATA']))
              ) {

                #	[MOVIMENTO]
                #	1 - Inclusão de batida
                #	2 - Exclusão de batida
                #	3 - Altera natureza
                #	4 - Altera Jornada Referencia
                #	5 - Abonos Atrasos
                #	6 - Abonos Faltas
                #	7 - Altera Atitude

                if (strlen(trim($resBatidasApontadas[$idba]['ent1'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['ent1'],4),
                    'natureza'        => 0,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'ent1',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b1 = m2h($resBatidasApontadas[$idba]['ent1'],4);
                  $status_1 = 'D';
                  $motivo_reprova_1 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_1 = $resBatidasApontadas[$idba]['status'];
                }
                if (strlen(trim($resBatidasApontadas[$idba]['ent2'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['ent2'],4),
                    'natureza'        => 0,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'ent2',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b3 = m2h($resBatidasApontadas[$idba]['ent2'],4);
                  $status_3 = 'D';
                  $motivo_reprova_3 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_3 = $resBatidasApontadas[$idba]['status'];
                }
                if (strlen(trim($resBatidasApontadas[$idba]['ent3'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['ent3'],4),
                    'natureza'        => 0,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'ent3',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b5 = m2h($resBatidasApontadas[$idba]['ent3'],4);
                  $status_5 = 'D';
                  $motivo_reprova_5 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_5 = $resBatidasApontadas[$idba]['status'];
                }
                if (strlen(trim($resBatidasApontadas[$idba]['ent4'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['ent4'],4),
                    'natureza'        => 0,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'ent4',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b7 = m2h($resBatidasApontadas[$idba]['ent4'],4);
                  $status_7 = 'D';
                  $motivo_reprova_7 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_7 = $resBatidasApontadas[$idba]['status'];
                }
                if (strlen(trim($resBatidasApontadas[$idba]['sai1'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['sai1'],4),
                    'natureza'        => 1,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'sai1',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b2 = m2h($resBatidasApontadas[$idba]['sai1'],4);
                  $status_2 = 'D';
                  $motivo_reprova_2 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_2 = $resBatidasApontadas[$idba]['status'];
                }
                if (strlen(trim($resBatidasApontadas[$idba]['sai2'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['sai2'],4),
                    'natureza'        => 1,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'sai2',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b4 = m2h($resBatidasApontadas[$idba]['sai2'],4);
                  $status_4 = 'D';
                  $motivo_reprova_4 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_4 = $resBatidasApontadas[$idba]['status'];
                }
                if (strlen(trim($resBatidasApontadas[$idba]['sai3'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['sai3'],4),
                    'natureza'        => 1,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'sai3',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b6 = m2h($resBatidasApontadas[$idba]['sai3'],4);
                  $status_6 = 'D';
                  $motivo_reprova_6 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_6 = $resBatidasApontadas[$idba]['status'];
                }
                if (strlen(trim($resBatidasApontadas[$idba]['sai4'])) > 0){
                  $array_batidas[] = array(
                    'batida'          => m2h($resBatidasApontadas[$idba]['sai4'],4),
                    'natureza'        => 1,
                    'pendente'        => 1,
                    'data_referencia' => dtEn($batapt_dtponto, true),
                    'status'          => 'D',
                    'campo'            => 'sai4',
                    'idaafdt'         => $resBatidasApontadas[$idba]['id']
                  );
                  $b8 = m2h($resBatidasApontadas[$idba]['sai4'],4);
                  $status_8 = 'D';
                  $motivo_reprova_8 = $resBatidasApontadas[$idba]['motivo_reprova'];
                  $status_portal_8 = $resBatidasApontadas[$idba]['status'];
                }
                // unset($resBatidasApontadas[$idba]);
              }
            endforeach;
          }

          // SOLICITAÇÕES ABONO FALTA
                $array_abonos_falta = array();
                $abono_pendente = "";
                $abono_pendente_mobile = "";
                $abono_reprovado_falta = "";
                $abono_pendente_falta = "";
                if ($resSolicitacaoAbonoFalta) {
                  foreach ($resSolicitacaoAbonoFalta as $keyF => $SolicitacaoAbono) {
                    if ($SolicitacaoAbono['chapa'] == $resData[$i]['CHAPA'] && dtEn($SolicitacaoAbono['dtponto'], true) == dtEn($resData[$i]['DATA'], true)) {
                      $array_abonos_falta[] = array(
                        'id'              => $SolicitacaoAbono['id'],
                        'data'            => dtEn($SolicitacaoAbono['dtponto'], true),
                        'inicio'          => m2h($SolicitacaoAbono['abn_horaini'], 4),
                        'termino'         => m2h($SolicitacaoAbono['abn_horafim'], 4),
                        'codabono'        => $SolicitacaoAbono['abn_codabono'],
                        'status'          => $SolicitacaoAbono['status'],
                        'just_abono_tipo' => $SolicitacaoAbono['justificativa_abono_tipo']
                      );

                      if($SolicitacaoAbono['status'] == 3){
                        $abono_reprovado_falta = $SolicitacaoAbono['motivo_reprova'];
                        $abono_pendente = '<i title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-square" style="font-size:20px; color: red;"></i>';
                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-timer-off tippy-btn text-danger" style="font-size:20px;"></i>';
                      }else{
                        $abono_pendente_falta = 1;
                        $abono_pendente = '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>';
                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Abono Pendente de Aprovação" class="mdi mdi-timer-off tippy-btn" style="font-size:20px; color: #ace5ff;"></i>';
                      }
                    }
                  }
                }
                // SOLICITAÇÕES ABONO ATRASO
                $array_abonos_atraso = array();
                $abono_pendente = "";
                $abono_pendente_mobile = "";
                $abono_reprovado_atraso = "";
                $abono_pendente_atraso = "";
                if ($resSolicitacaoAbonoAtraso) {
                  foreach ($resSolicitacaoAbonoAtraso as $keyA => $SolicitacaoAbono) {
                    if (dtBr($SolicitacaoAbono['dtponto']) == dtBr($resData[$i]['DATA']) && $SolicitacaoAbono['chapa'] == $resData[$i]['CHAPA']) {
                      $array_abonos_atraso[] = array(
                        'id'              => $SolicitacaoAbono['id'],
                        'data'            => dtEn($SolicitacaoAbono['dtponto'], true),
                        'inicio'          => m2h($SolicitacaoAbono['abn_horaini'], 4),
                        'termino'         => m2h($SolicitacaoAbono['abn_horafim'], 4),
                        'codabono'        => $SolicitacaoAbono['abn_codabono'],
                        'status'          => $SolicitacaoAbono['status'],
                        'just_abono_tipo' => $SolicitacaoAbono['justificativa_abono_tipo']
                      );
                      
                      if($SolicitacaoAbono['status'] == 3){
                        $$abono_reprovado_atraso = $SolicitacaoAbono['motivo_reprova'];
                        $abono_pendente = '<i title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-square" style="font-size:20px; color: red;"></i>';
                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-timer-off tippy-btn text-danger" style="font-size:20px;"></i>';
                      }else{
                        $abono_pendente_atraso = 1;
                        $abono_pendente = '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>';
                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Abono Pendente de Aprovação" class="mdi mdi-timer-off tippy-btn" style="font-size:20px; color: #ace5ff;"></i>';
                      }
                    }
                  }
                }
                // SOLICITAÇÕES ALTERA ATITUDE
                if ($resSolicitacaoAbonoAtitude) {
                  foreach ($resSolicitacaoAbonoAtitude as $keyA => $SolicitacaoAbono) {
                    if (dtBr($SolicitacaoAbono['dtponto']) == dtBr($resData[$i]['DATA']) && $SolicitacaoAbono['chapa'] == $resData[$i]['CHAPA']) {

                      if(substr($SolicitacaoAbono['atitude_justificativa'],0,1) == 'F'){

                        if($SolicitacaoAbono['status'] == 3){
                          $abono_reprovado_falta = $SolicitacaoAbono['motivo_reprova'];
                        }else{
                          if($abono_pendente_falta == ""){
                            $abono_pendente_falta = 1;
                          }
                        }

                      }
                      
                      if(substr($SolicitacaoAbono['atitude_justificativa'],0,1) == 'A'){

                        if($SolicitacaoAbono['status'] == 3){
                          $abono_reprovado_atraso = $SolicitacaoAbono['motivo_reprova'];
                        }else{
                          if($abono_pendente_atraso == ""){
                            $abono_pendente_atraso = 1;
                          }
                        }
                        
                      }
                      
                    }
                  }
                }

          $motivo_justificativa = (strlen(trim($resData[$i]['JUSTIFICATIVA'])) > 0) ? $resData[$i]['JUSTIFICATIVA'] : 'Não justificado';

          $status_atitude = '';
          if(strlen(trim($resData[$i]['JUSTIFICATIVA_ATITUDE'])) > 0){
            $status_atitude = substr($resData[$i]['JUSTIFICATIVA_ATITUDE'],-1);
            $motivo_justificativa = substr($resData[$i]['JUSTIFICATIVA_ATITUDE'],0, -1);
          }

          if(count($array_abonos_falta) > 0 || $resData[$i]['QTDE_ABONO'] > 0 || count($array_abonos_atraso) > 0 ){
            $motivo_justificativa = "ABONO SOLICITADO";
          }

          $b1_tipo = (strlen(trim($b1)) > 0) ? 'D' : '';
          $b2_tipo = (strlen(trim($b2)) > 0) ? 'D' : '';
          $b3_tipo = (strlen(trim($b3)) > 0) ? 'D' : '';
          $b4_tipo = (strlen(trim($b4)) > 0) ? 'D' : '';
          $b5_tipo = (strlen(trim($b5)) > 0) ? 'D' : '';
          $b6_tipo = (strlen(trim($b6)) > 0) ? 'D' : '';


          // echo $resData[$i]['CHAPA'].' ('.$i.')  ('.$rows.') ('.$nLinha.')<br>';
          // echo $resData[$i]['CHAPA'].' ('.$rows.')<br>';
          $sheet->setCellValue('A' . $rows, $resData[$i]['CHAPA']);
          $sheet->setCellValue('B' . $rows, $resData[$i]['NOME']);
          $sheet->setCellValue('C' . $rows, $resData[$i]['CODSITUACAO']);
          $sheet->setCellValue('D' . $rows, diaSemana($resData[$i]['DATA']));
          $sheet->setCellValue('E' . $rows, date('d/m/Y', strtotime($resData[$i]['DATA'])));
          $sheet->setCellValue('F' . $rows, $b1.(($status_1 != 'C') ? $b1_tipo : 'C'));

          

          if((($status_portal_1 ?? '') != '') || $status_1 != ''){
            $color='ffffff';
            if($status_portal_1 == 1 || $status_portal_1 == 2) $color = 'feca07';
            if($status_portal_1 == 3) $color = 'f4811f';
            if($status_1 == 'T') $color = 'dbdcdd';
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('E' . $rows)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
          }
          
          $sheet->setCellValue('G' . $rows, $b2.(($status_2 != 'C') ? $b2_tipo : 'C'));
          if((($status_portal_2 ?? '') != '') || $status_2 != ''){
            if($status_portal_2 == 1 || $status_portal_2 == 2) $color = 'feca07';
            if($status_portal_2 == 3) $color = 'f4811f';
            if($status_2 == 'T') $color = 'dbdcdd';
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('F' . $rows)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
          }

          $sheet->setCellValue('H' . $rows, $b3.(($status_3 != 'C') ? $b3_tipo : 'C'));
          if((($status_portal_3 ?? '') != '') || $status_3 != ''){
            if($status_portal_3 == 1 || $status_portal_3 == 2) $color = 'feca07';
            if($status_portal_3 == 3) $color = 'f4811f';
            if($status_3 == 'T') $color = 'dbdcdd';
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('G' . $rows)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
          }

          $sheet->setCellValue('I' . $rows, $b4.(($status_4 != 'C') ? $b4_tipo : 'C'));
          if((($status_portal_4 ?? '') != '') || $status_4 != ''){
            if($status_portal_4 == 1 || $status_portal_4 == 2) $color = 'feca07';
            if($status_portal_4 == 3) $color = 'f4811f';
            if($status_4 == 'T') $color = 'dbdcdd';
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('H' . $rows)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
          }

          $sheet->setCellValue('J' . $rows, $b5.(($status_5 != 'C') ? $b5_tipo : 'C'));
          if((($status_portal_5 ?? '') != '') || $status_5 != ''){
            if($status_portal_5 == 1 || $status_portal_5 == 2) $color = 'feca07';
            if($status_portal_5 == 3) $color = 'f4811f';
            if($status_5 == 'T') $color = 'dbdcdd';
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('I' . $rows)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
          }

          $sheet->setCellValue('K' . $rows, $b6.(($status_6 != 'C') ? $b6_tipo : 'C'));
          if((($status_portal_6 ?? '') != '') || $status_6 != ''){
            if($status_portal_6 == 1 || $status_portal_6 == 2) $color = 'feca07';
            if($status_portal_6 == 3) $color = 'f4811f';
            if($status_6 == 'T') $color = 'dbdcdd';
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('J' . $rows)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($color);
          }

          $sheet->setCellValue('L' . $rows, $motivo_justificativa);

          $n = 0;
          if($dados['ck_ExtraExecutado'] == 1){$n++; $sheet->setCellValue($colunas[$n].$rows, ($resData[$i]['EXTRAEXECUTADO_CASE']) ? m2h($resData[$i]['EXTRAEXECUTADO_CASE']) : '');}
          if($dados['ck_semPar'] == 1){$n++; $sheet->setCellValue($colunas[$n].$rows, ($resData[$i]['SEM_PAR_CORRESPONDENTE']) ? $resData[$i]['SEM_PAR_CORRESPONDENTE_DESC'] : '');}
          if($dados['ck_Atrasos'] == 1){
            $n++; $sheet->setCellValue($colunas[$n].$rows, ($resData[$i]['ATRASO_CASE']) ? m2h($resData[$i]['ATRASO_CASE']) : '');

            if($abono_pendente_atraso == 1 || $abono_reprovado_atraso != '' || $resData[$i]['ABONO_PENDENTE_RH'] > 0){
              $color = ($abono_reprovado_atraso != '') ? 'f4811f' : 'feca07';
              if($resData[$i]['ABONO_PENDENTE_RH'] > 0) $color = 'dbdcdd';
              $spreadsheet
              ->getActiveSheet()
              ->getStyle($colunas[$n].$rows)
              ->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()
              ->setARGB($color);
            }
          }
          if($dados['ck_Faltas'] == 1){
            $n++; $sheet->setCellValue($colunas[$n].$rows, ($resData[$i]['FALTA_CASE']) ? m2h($resData[$i]['FALTA_CASE']) : '');
            if($abono_pendente_falta == 1 || $abono_reprovado_falta != '' || $resData[$i]['ABONO_PENDENTE_RH'] > 0){
              $color = ($abono_reprovado_falta != '') ? 'f4811f' : 'feca07';

              if($resData[$i]['ABONO_PENDENTE_RH'] > 0) $color = 'dbdcdd';

              $spreadsheet
              ->getActiveSheet()
              ->getStyle($colunas[$n].$rows)
              ->getFill()
              ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
              ->getStartColor()
              ->setARGB($color);
            }
          }
          if($dados['ck_jorMaior10'] == 1){$n++; $sheet->setCellValue($colunas[$n].$rows, ($resData[$i]['JORNADA_MAIOR_10HORAS']) ? 'JOR. MAIOR QUE 10h' : '');}
          if($dados['ck_interjornada'] == 1){$n++; $sheet->setCellValue($colunas[$n].$rows, ($resData[$i]['INTERJORNADA']) ? 'INTERJORNADA' : '');}
          
          $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':'.$colunas[$n].$rows)->applyFromArray($styleBorda);
          
          $rows++;


          $nLinha++;
          // unset($resData[$i]);
        }
      }
      
      for($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
          $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
      }

      $writer = new Xlsx($spreadsheet);

      header('Content-Disposition: attachment; filename=Crítica de Ponto.xlsx' );
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Transfer-Encoding: binary');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');

      $writer->save("php://output");

      exit();

  }

    public function action($act){

        $dados = $_POST;
        $arquivos = $_FILES;

        
        if(!$dados && !$arquivos) return false;
        
        switch($act){
            
            //-------------------------------------
            // cadastra batida
            case 'cadastrar_batida':
                exit($this->mCritica->CadastrarBatida($dados));
                break;

            //-------------------------------------
            // cadastra batida
            case 'editar_batida':
                exit($this->mCritica->CadastrarBatida($dados));
                break;
            
            //LANCA ABONO E JUSTIFICATIVA
            case 'lancar_abono_falta':
                exit($this->mCritica->CadastrarAbonoFalta($dados, $arquivos));
                break;

            case 'lancar_abono_atraso':
                exit($this->mCritica->CadastrarAbonoAtraso($dados, $arquivos));
                break;
            
            //EXCLUIR LANCAMENTO
            case 'excluir_lancamento':
                  exit($this->mCritica->DeletarAbonoLancado($dados));
                  break;
            case 'lista_atitude':
                  exit(json_encode($this->mCritica->listaAtitudeFalta($dados)));
                  break;
            case 'altera_atitude':
                  exit($this->mCritica->alteraAtitude($dados, $arquivos));
                  break;

        } 

    }

    public function BuscarAbonos(){
      $dados = $_POST;
      if(!$dados) return false;

        $html_retorno = '';

        $chapa = ($dados['chapa']) ?? $dados['chapa'];
        $dataref = ($dados['dataref']) ?? $dados['dataref'];
        $tipo = ($dados['tipo']) ?? $dados['tipo'];
        $ft_tipo = ($dados['ft_tipo']) ?? $dados['ft_tipo'];
        $dtapt = ($dados['dtapt']) ?? $dados['dtapt'];

        $objTipoAbono = $this->mCritica->ListaTipoAbono($ft_tipo);

        $dtapt = dtEn($dtapt );
        
        $objDados = false;
        $objDados['chapa'] = $chapa;
        $objDados['dataref'] = dten($dataref);
        $objDados['tipo'] = $tipo;
        
        //BUSCA A JUSTIFICATIVA
        $objJustFunc = $this->mCritica->BuscaJustificativa($chapa, $dtapt);
        $objOcorrencias = $this->mCritica->ListaOcorrenciaAbono($objDados);
        $linha = 1;
        
        if($objOcorrencias){
          

          foreach($objOcorrencias as $ido => $value){
            
            
            $datatime1 = new DateTime($objOcorrencias[$ido]['INICIO']);
            $datatime2 = new DateTime($objOcorrencias[$ido]['FIM']);
    
            $data1  = $datatime1->format('Y-m-d H:i:s');
            $data2  = $datatime2->format('Y-m-d H:i:s');
    
            $diff = $datatime1->diff($datatime2);
            $horas = sprintf("%02s",$diff->h).':'.sprintf("%02s",$diff->i);
            

            // mobile
            $html_retorno .= '
            <tr>
              <td bgcolor="#ffffff">
                <b>Ini:</b> '.date('d/m/Y', strtotime($objOcorrencias[$ido]['INICIO'])).'<br>
                <b>Fim:</b> '.date('d/m/Y', strtotime($objOcorrencias[$ido]['FIM'])).'
              </td>
              <td width="40" bgcolor="#ffffff">
                <input type="hidden" name="chapa[]" value="'. $objOcorrencias[$ido]['CHAPA'] .'">
                <input data-calculo onblur="calcularNovoHorario(this)" name="inicio[]" data-inicio="'.$linha.'" data-cod="'.$linha.'" data-hora type="time" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                <input data-calculo onblur="calcularNovoHorario(this)" name="inicio_h[]" data-inicioh="'.$linha.'" data-cod="'.$linha.'" data-hora type="hidden" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                <input data-calculo onblur="calcularNovoHorario(this)" name="data_inicio[]" data-datainicio="'.$linha.'" data-cod="'.$linha.'" type="hidden" value="'.date('Y-m-d', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                
                <input data-calculo onblur="calcularNovoHorario(this)" name="fim[]" data-fim="'.$linha.'"  data-cod="'.$linha.'" data-hora type="time" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['FIM'])).'">
                <input data-calculo onblur="calcularNovoHorario(this)" name="fim_h[]" data-fimh="'.$linha.'" data-cod="'.$linha.'" data-hora type="hidden" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['FIM'])).'">
                <input data-calculo onblur="calcularNovoHorario(this)" name="datafim[]" data-datafim="'.$linha.'" data-cod="'.$linha.'" type="hidden" value="'.date('Y-m-d', strtotime($objOcorrencias[$ido]['FIM'])).'">
              </td>
              <td bgcolor="#ffffff"><span data-resultado="'.$linha.'">'.($horas).'</span><input data-calculo onblur="calcularNovoHorario(this)" data-limite="'.$linha.'"  data-cod="'.$linha.'" type="hidden" value="'.h2m($horas).'"></td>
            </tr>
            <tr style="border-bottom: 4px solid #cccccc;" width="40">
              <td bgcolor="#F0F0F0"><b>Abono:</b></td>
              <td colspan="2" bgcolor="#ffffff">
              <select data-cod-abono="'.$linha.'" name="codabono[]" data-cod="'.$linha.'" style="width: 100%;">
              <option value="" selected>...</option>
              ';
              if($objTipoAbono){
                foreach($objTipoAbono as $idtb => $value){
                  $html_retorno .= '<option data-obrigatorio="'.$objTipoAbono[$idtb]['ATESTADOOBRIGATORIO'].'" data-portal="'.$objTipoAbono[$idtb]['ATIVOPORTAL'].'" value="'.$objTipoAbono[$idtb]['CODIGO'].'">'.($objTipoAbono[$idtb]['CODIGO'].' - '.$objTipoAbono[$idtb]['DESCRICAO']).'</option>';
                }
              }
              $html_retorno .= '</select></td>';
              
              
              $html_retorno .= '</tr>
            ';

            $linha++;
            
          }
          $html_retorno .= '@';
          #############################################################################################################
          # (B)
          #############################################################################################################	
          $linha = 1;
          foreach($objOcorrencias as $ido => $value){
            
            
            $datatime1 = new DateTime(date('Y/m/d H:i:s', strtotime($objOcorrencias[$ido]['INICIO'])));
            $datatime2 = new DateTime(date('Y/m/d H:i:s', strtotime($objOcorrencias[$ido]['FIM'])));
    
            $data1  = $datatime1->format('Y-m-d H:i:s');
            $data2  = $datatime2->format('Y-m-d H:i:s');
    
            $diff = $datatime1->diff($datatime2);
            $horas = sprintf("%02s",$diff->h).':'.sprintf("%02s",$diff->i);
            
            
            $html_retorno .= '
            <tr>
              <td bgcolor="#ffffff">
                <b>Ini:</b> '.date('d/m/Y', strtotime($objOcorrencias[$ido]['INICIO'])).'<br>
                <b>Fim:</b> '.date('d/m/Y', strtotime($objOcorrencias[$ido]['FIM'])).'
              </td>
              <td width="40" bgcolor="#ffffff">
                <input type="hidden" name="chapa[]" value="'. $objOcorrencias[$ido]['CHAPA'] .'">
                <input readonly data-inicio2="'.$linha.'" data-cod2="'.$linha.'" name="inicio[]" data-hora type="text" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                <input readonly data-inicioh2="'.$linha.'" data-cod2="'.$linha.'" name="inicio_h[]" data-hora type="hidden" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                <input readonly data-datainicio2="'.$linha.'" data-cod2="'.$linha.'" name="data_inicio[]" type="hidden" value="'.date('Y-m-d', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                
                <input readonly data-fim2="'.$linha.'"  data-cod2="'.$linha.'" name="fim[]" data-hora2 type="text" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['FIM'])).'">
                <input readonly data-fimh2="'.$linha.'" data-cod2="'.$linha.'" name="fim_h[]" data-hora2 type="hidden" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['FIM'])).'">
                <input readonly data-datafim2="'.$linha.'" data-cod2="'.$linha.'" name="datafim[]" type="hidden" value="'.date('Y-m-d', strtotime($objOcorrencias[$ido]['FIM'])).'">
              </td>
              <td width="100"><span data-resultado="'.$linha.'">'.($horas).'</span><input data-calculo2 data-limite="'.$linha.'"  data-cod="'.$linha.'" type="hidden" value="'.h2m($horas).'"></td>


            </td>
            <tr style="border-bottom: 4px solid #cccccc;" width="40">
              <td bgcolor="#F0F0F0"><b>Justificativa:</b></td>
              <td width="40" bgcolor="#ffffff" colspan="2">
                <select name="codabono[]" data-cod-abono2="'.$linha.'" name="cod_abono_justificativa[]" data-cod="'.$linha.'">
                  <option value="">...</option>
                  <option value="Descontar">Falta Confirmada</option>
                    <option value="031">Falta Não Remunerada</option>
                </select>
              </td>
            </tr>

              ';
            $linha++;
          }

          $html_retorno .= '@';
          #############################################################################################################
          # (B)
          #############################################################################################################	
          $linha = 1;
          foreach($objOcorrencias as $ido => $value){
            
            
            $datatime1 = new DateTime(date('Y/m/d H:i:s', strtotime($objOcorrencias[$ido]['INICIO'])));
            $datatime2 = new DateTime(date('Y/m/d H:i:s', strtotime($objOcorrencias[$ido]['FIM'])));
    
            $data1  = $datatime1->format('Y-m-d H:i:s');
            $data2  = $datatime2->format('Y-m-d H:i:s');
    
            $diff = $datatime1->diff($datatime2);
            $horas = sprintf("%02s",$diff->h).':'.sprintf("%02s",$diff->i);
            
            
            $html_retorno .= '
            <tr>
              <td bgcolor="#ffffff">
                <b>Ini:</b> '.date('d/m/Y', strtotime($objOcorrencias[$ido]['INICIO'])).'<br>
                <b>Fim:</b> '.date('d/m/Y', strtotime($objOcorrencias[$ido]['FIM'])).'
              </td>
              <td width="40" bgcolor="#ffffff">
                <input type="hidden" name="chapa[]" value="'. $objOcorrencias[$ido]['CHAPA'] .'">
                <input readonly data-inicio2="'.$linha.'" data-cod2="'.$linha.'" name="inicio[]" data-hora type="text" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                <input readonly data-inicioh2="'.$linha.'" data-cod2="'.$linha.'" name="inicio_h[]" data-hora type="hidden" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                <input readonly data-datainicio2="'.$linha.'" data-cod2="'.$linha.'" name="data_inicio[]" type="hidden" value="'.date('Y-m-d', strtotime($objOcorrencias[$ido]['INICIO'])).'">
                
                <input readonly data-fim2="'.$linha.'"  data-cod2="'.$linha.'" name="fim[]" data-hora2 type="text" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['FIM'])).'">
                <input readonly data-fimh2="'.$linha.'" data-cod2="'.$linha.'" name="fim_h[]" data-hora2 type="hidden" size="5" value="'.date('H:i', strtotime($objOcorrencias[$ido]['FIM'])).'">
                <input readonly data-datafim2="'.$linha.'" data-cod2="'.$linha.'" name="datafim[]" type="hidden" value="'.date('Y-m-d', strtotime($objOcorrencias[$ido]['FIM'])).'">
              </td>
              <td width="100"><span data-resultado="'.$linha.'">'.($horas).'</span><input data-calculo2 data-limite="'.$linha.'"  data-cod="'.$linha.'" type="hidden" value="'.h2m($horas).'"></td>


            </td>
            <tr style="border-bottom: 4px solid #cccccc;" width="40">
              <td bgcolor="#F0F0F0"><b>Justificativa:</b></td>
              <td width="40" bgcolor="#ffffff" colspan="2">
                <select name="codabono[]" data-cod-abono2="'.$linha.'" name="cod_abono_justificativa[]" data-cod="'.$linha.'">
                  <option value="0">.</option>
                    <option value="1">Falta confirmada</option>
                </select>
              </td>
            </tr>

              ';
            $linha++;
          }
          
          $html_retorno .= '@';
          
          
          $objDados = false;
          $objDados['chapa'] = $chapa;
          $objDados['datainicio'] = $dtapt;
          $objDados['tipo'] = $ft_tipo;
          $objListaAbonoInseridos = $this->mCritica->ListaAbonoInseridos($objDados);
          ######################################################################################
          # AA )
          ######################################################################################
          if($objListaAbonoInseridos){
            
            $html_retorno .= '
            
            <tr bgcolor="#ffffff">
              <td colspan="8"><br><b>'.('Lançamento realizados:').'</b></td>
            </tr>
            
              
              <tr class="tbadmlistacab" style="background: #67c750;">
                <td><b>Data</b></td>
                <td><b>Horário</b></td>
                <td><b>Fim</b></td>
                <td></td>
              </tr>
            ';
            
            
            $linha = 1;
            foreach($objListaAbonoInseridos as $ido2 => $Abono){

              if($Abono['movimento'] == 8){

                $data = new DateTime(date('Y/m/d H:i:s', strtotime($objListaAbonoInseridos[$ido2]['atitude_dt'])));

                $html_retorno .= '
                    <tr class="tbadmlistacab" style="background: #ffffff;">
                      <td><b>Data:</b> '.date('d/m/Y', strtotime($objListaAbonoInseridos[$ido2]['atitude_dt'])).'</td>
                      <td><b>Início:</b> '.m2h($objListaAbonoInseridos[$ido2]['atitude_ini']).'</td>
                      <td><b>Fim:</b> '.m2h($objListaAbonoInseridos[$ido2]['atitude_fim']).'</td>
                      <td>'.(($objListaAbonoInseridos[$ido2]['atitude_tipo'] == 1) ? 'Falta Confirmada' : '.').'</td><td>';

                      if(($objListaAbonoInseridos[$ido2]['status'] == 2) || ($objListaAbonoInseridos[$ido2]['status'] == 3 && strlen(trim($objListaAbonoInseridos[$ido2]['aprgestor_user'])) <= 0) || strlen(trim($objListaAbonoInseridos[$ido2]['motivo_reprova'])) > 0){
                        $html_retorno .= '<a href="javascript:void(0);" onclick="return removeAbono(\''.$objListaAbonoInseridos[$ido2]['id'].'\');" style="color: red !important;"><b class="fa fa-times"></b></a>';
                      }else{
                        $html_retorno .= utf8_decode('<a href="javascript:void(0);" onclick="alert(\'Abono não pode ser excluido porque já foi aprovado pelo Funcionário ou Gestor.\')" style="color: red !important;"><b class="fa fa-times"></b></a>');
                      }
                    
                      $html_retorno .= '</td></tr>
                  ';

              }else{
              
              
              $datatime1 = new DateTime(date('Y/m/d H:i:s', strtotime($objListaAbonoInseridos[$ido2]['datainicio'])));
              $datatime2 = new DateTime(date('Y/m/d H:i:s', strtotime($objListaAbonoInseridos[$ido2]['datafim'])));
    
              $data1  = $datatime1->format('Y-m-d H:i:s');
              $data2  = $datatime2->format('Y-m-d H:i:s');
    
              $diff = $datatime1->diff($datatime2);
              $horas = sprintf("%02s",$diff->h).':'.sprintf("%02s",$diff->i);
              
              
              $html_retorno .= '
              <tr class="cod_lan_'.$objListaAbonoInseridos[$ido2]['id'].'" '.( (strlen(trim($objListaAbonoInseridos[$ido2]['justificativa_excecao'])) <= 0) ? 'style="color: #ffffff; background: #7986CB !important;"' : 'style="color: #ffffff; background: #67c750;"' ).' >
                
                <td>
                  <b>Ini:</b>'.date('d/m/Y', strtotime($objListaAbonoInseridos[$ido2]['datainicio'])).'<br>
                  <b>Fim:</b>'.date('d/m/Y', strtotime($objListaAbonoInseridos[$ido2]['datafim'])).'
                </td>
                <td>
                  '.date('H:i', strtotime($objListaAbonoInseridos[$ido2]['datainicio'])).'<br>
                  '.date('H:i', strtotime($objListaAbonoInseridos[$ido2]['datafim'])).'
                </td>
                <td>'.($horas).'
                ';

                $html_retorno .= '
                </td><td align="center">';
                  
                  if(($objListaAbonoInseridos[$ido2]['status'] == 2) || ($objListaAbonoInseridos[$ido2]['status'] == 3 && strlen(trim($objListaAbonoInseridos[$ido2]['aprgestor_user'])) <= 0) || strlen(trim($objListaAbonoInseridos[$ido2]['motivo_reprova'])) > 0){
                    $html_retorno .= '<a href="javascript:void(0);" onclick="return removeAbono(\''.$objListaAbonoInseridos[$ido2]['id'].'\');" style="color: red !important;"><b class="fa fa-times"></b></a>';
                  }else{
                    $html_retorno .= utf8_decode('<a href="javascript:void(0);" onclick="alert(\'Abono não pode ser excluido porque já foi aprovado pelo Funcionário ou Gestor.\')" style="color: red !important;"><b class="fa fa-times"></b></a>');
                  }
                  
                  $html_retorno .= '</td>';
                
                
                  $html_retorno .= '</tr>
              ';
                
                $html_retorno .= '
                <tr style="border-bottom:6px solid #cccccc;">
                <td bgcolor="#b3b3b3"><b>Tipo Abono:</b></td>
                <td colspan="3"'.( (strlen(trim($objListaAbonoInseridos[$ido2]['justificativa_excecao'])) <= 0) ? 'style="color: #ffffff; background: #7986CB !important;"' : 'style="color: #ffffff; background: #67c750;"' ).'>
                ';
                
                if(strlen(trim($objListaAbonoInseridos[$ido2]['justificativa_excecao'])) <= 0){
                  if($objTipoAbono){
                    foreach($objTipoAbono as $idtb => $value){
                      
                      if($objTipoAbono[$idtb]['CODIGO'] == $objListaAbonoInseridos[$ido2]['codabono']){
                        $html_retorno .= ($objTipoAbono[$idtb]['DESCRICAO']);
                        break;
                      }
                      
                    }
                  }
                }else{
                  $html_retorno .= 'JUST -'.$objListaAbonoInseridos[$ido2]['justificativa_excecao'];
                }
                
                
                
                $html_retorno .= '
                </td>';
                
                  $html_retorno .= '</tr>
              ';
              
              $linha++;
              }
            }
          }
          $html_retorno .= '@';
          ######################################################################################
          # BB )
          ######################################################################################
          if($objListaAbonoInseridos && 1== 2){
            
            $html_retorno .= '
            
            <tr bgcolor="#ffffff">
              <td colspan="8"><br><b>'.('Lançamento realizados: ').'</b></td>
            </tr>
            
              
              <tr class="tbadmlistacab" style="background: #b3b3b3;">
                <td><b>Data</b></td>
                <td><b>Horário</b></td>
                <td><b>Horas</b></td>
                <td></td>
              </tr>
            ';
            
            
            foreach($objListaAbonoInseridos as $ido2 => $Abono){

                $datatime1 = new DateTime(date('Y/m/d H:i:s', strtotime($objListaAbonoInseridos[$ido2]['datainicio'])));
                $datatime2 = new DateTime(date('Y/m/d H:i:s', strtotime($objListaAbonoInseridos[$ido2]['datafim'])));
      
                $data1  = $datatime1->format('Y-m-d H:i:s');
                $data2  = $datatime2->format('Y-m-d H:i:s');
      
                $diff = $datatime1->diff($datatime2);
                $horas = sprintf("%02s",$diff->h).':'.sprintf("%02s",$diff->i);
                
                
                $html_retorno .= '
                <tr class="cod_lan_'.$objListaAbonoInseridos[$ido2]['id'].'" '.( (strlen(trim($objListaAbonoInseridos[$ido2]['justificativa_excecao'])) <= 0) ? 'style="color: #ffffff; background: #7986CB !important;"' : 'style="color: #ffffff; background: #67c750;"' ).' >
                  
                  <td>
                    <b>Ini:</b>'.date('d/m/Y', strtotime($objListaAbonoInseridos[$ido2]['datainicio'])).'<br>
                    <b>Fim:</b>'.date('d/m/Y', strtotime($objListaAbonoInseridos[$ido2]['datafim'])).'
                  </td>
                  <td>
                    '.date('H:i', strtotime($objListaAbonoInseridos[$ido2]['datainicio'])).'<br>
                    '.date('H:i', strtotime($objListaAbonoInseridos[$ido2]['datafim'])).'
                  </td>
                  <td>'.($horas).'
                  ';

                  $html_retorno .= '
                  </td><td align="center">';
                    
                    if(($objListaAbonoInseridos[$ido2]['status'] == 2) || ($objListaAbonoInseridos[$ido2]['status'] == 3 && strlen(trim($objListaAbonoInseridos[$ido2]['aprgestor_user'])) <= 0) || strlen(trim($objListaAbonoInseridos[$ido2]['motivo_reprova'])) > 0){
                      $html_retorno .= '<a href="javascript:void(0);" onclick="return removeAbono(\''.$objListaAbonoInseridos[$ido2]['id'].'\');" style="color: red !important;"><b class="fa fa-times"></b></a>';
                    }else{
                      $html_retorno .= utf8_decode('<a href="javascript:void(0);" onclick="alert(\'Abono não pode ser excluido porque já foi aprovado pelo Funcionário ou Gestor.\')" style="color: red !important;"><b class="fa fa-times"></b></a>');
                    }
                    
                    $html_retorno .= '</td>';
                  
                  
                    // $html_retorno .= '</tr>';
                  
                  $html_retorno .= '
                  <tr style="border-bottom:6px solid #cccccc;">
                  <td bgcolor="#b3b3b3"><b>Tipo Abono:</b></td>
                  <td colspan="3"'.( (strlen(trim($objListaAbonoInseridos[$ido2]['justificativa_excecao'])) <= 0) ? 'style="color: #ffffff; background: #7986CB !important;"' : 'style="color: #ffffff; background: #67c750;"' ).'>
                  ';
                  
                  if(strlen(trim($objListaAbonoInseridos[$ido2]['justificativa_excecao'])) <= 0){
                    if($objTipoAbono){
                      foreach($objTipoAbono as $idtb => $value){
                        
                        if($objTipoAbono[$idtb]['CODIGO'] == $objListaAbonoInseridos[$ido2]['codabono']){
                          $html_retorno .= ($objTipoAbono[$idtb]['DESCRICAO']);
                          break;
                        }
                        
                      }
                    }
                  }else{
                    $html_retorno .= 'JUST -'.$objListaAbonoInseridos[$ido2]['justificativa_excecao'];
                  }
                  
                  
                  
                  $html_retorno .= '
                  </td>';
                  
                    $html_retorno .= '</tr>
                ';
            }
          }
          
          
          
        }else{
          
          
          
          $html_retorno .= '
          <tr class="tbadmlistalin">
            <td colspan="7" align="center" height="40" valign="middle">Nenhuma ocorrencia encontrada.</td>
          </tr>
          ';
        }

        echo $html_retorno;
        
        exit();

    }
}