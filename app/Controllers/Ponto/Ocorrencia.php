<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use CodeIgniter\Model;
// ini_set('display_errors', true);
// error_reporting(E_ALL);
Class Ocorrencia extends BaseController {
	
	public $mOcorrencia;

    public function __construct()
    {
        
        parent::__construct('Ponto'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-calendar-clock"></i> Ponto';
        $this->_breadcrumb->add('Ocorrência de Ponto', 'ponto/ocorrencia');
        $this->mOcorrencia = model('Ponto/OcorrenciaModel');

    }

    public function index(){

        ini_set("pcre.backtrack_limit", "50000000");
        set_time_limit(60*90);
        ini_set('max_execution_time', 60*90);
        // ini_set('memory_limit', '4096M');
       
        parent::VerificaPerfil('PONTO_OCORRENCIA');
        $dados['arquivo_morto'] = parent::VerificaPerfil('PONTO_OCORRENCIA_EXCEL_AM', false);
        $dados['_titulo'] = "Ocorrência de Ponto";
        
        $periodo = $this->request->getPost('periodo');
        $dados['resOcorrencia'] = false;
        $dados['resOcorrenciaJustificadas'] = false;
        
        $mAcesso = model('AcessoModel');
        $dados['perfil_Config']= $mAcesso->VerificaPerfil('PONTO_OCORRENCIAS_CONFIG');
        $dados['TiposOcorrencia'] = $this->mOcorrencia->ListarOcorrenciaTipoPortal()[0];


        // $dataFimPeriodoAtivo = ultimoDiaDoMes(dtEn($this->mOcorrencia->PeriodoAtivo()[0]->FIMMENSAL, true));
        $dados['bloqueiaAlteracao'] = false;
       
		$congelado = false;
        if($periodo !== null){
            $dados['post'] = $_POST;
            $resOcorrencia = $this->mOcorrencia->ListarOcorrencia($dados['post']);
            $dados['resOcorrenciaLog'] = $resOcorrencia;
            $array_just = $dados['post'];
            $array_just['ja_justificados'] = 1;
            $dados['resOcorrenciaLogTratado'] = $this->mOcorrencia->ListarOcorrencia($array_just);
            
			$periodo_fim = (int)date('Ym', strtotime(mb_substr($periodo, -10)));
		    $hoje = (int)date('Ym');
            if($periodo_fim < $hoje){
                $congelado = true;
            }

            $ultimoDiaPeriodo           = ultimoDiaDoMes(mb_substr($periodo, -10));
            // $dados['bloqueiaAlteracao'] = ($ultimoDiaPeriodo < '2022-01-01') ? true : false;
            $dados['bloqueiaAlteracao'] = ($ultimoDiaPeriodo < date('Y-m-d')) ? true : false;
			
            $dados['resOcorrenciaJustificadas'] = $this->mOcorrencia->ListarOcorrenciaJustificadaTotal($dados['post']);

            $resFuncionario = array();
            $resJustificativas = array();
            $resDadosFunc = array();
            if($resOcorrencia){

                foreach($resOcorrencia as $key => $Ocorrencia){

                    $observacao = "";
                    if(isset($resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['observacao'])){
                        if(strlen(trim($resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['observacao'])) > 0) $observacao = $resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['observacao'];
                    }

                    //$resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['observacao'] = $$observacao;
                    $resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['CODSITUACAO'] = $Ocorrencia['CODSITUACAO'];
                    $resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['justificativa_extra'] = $Ocorrencia['justificativa_extra'];
                    $resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['observacao'] = $Ocorrencia['observacao'];
                    $resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['codmotivo'] = $Ocorrencia['codmotivo'];
                    $resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['id_anexo'] = $Ocorrencia['id_anexo'];
                    $resJustificativas[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']]['file_name'] = $Ocorrencia['file_name'];
                    
                    $resFuncionario[$Ocorrencia['CHAPA']][$Ocorrencia['DATA']][] = $Ocorrencia;

                    if(!isset($resDadosFunc[$Ocorrencia['CHAPA']])){
                        $resDadosFunc[$Ocorrencia['CHAPA']]['DATAS'][] = $Ocorrencia['DATA'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['CODCOLIGADA'] = $Ocorrencia['CODCOLIGADA'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['FERIADO'] = $Ocorrencia['FERIADO'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['HORARIO'] = $Ocorrencia['HORARIO'];
                        
                        $resDadosFunc[$Ocorrencia['CHAPA']]['CHAPA'] = $Ocorrencia['CHAPA'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['NOME'] = $Ocorrencia['NOME'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['CODFUNCAO'] = $Ocorrencia['CODFUNCAO'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['NOMEFUNCAO'] = $Ocorrencia['NOMEFUNCAO'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['CODSECAO'] = $Ocorrencia['CODSECAO'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['NOMESECAO'] = $Ocorrencia['NOMESECAO'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['codmotivo'] = $Ocorrencia['codmotivo'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['descricao_motivo'] = $Ocorrencia['descricao_motivo'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['log_ocorrencia'] = $Ocorrencia['log_ocorrencia'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['observacao'] = $Ocorrencia['observacao'];
                        $resDadosFunc[$Ocorrencia['CHAPA']]['gestor'] = $Ocorrencia['gestor'];
                    }else{
                        if(!in_array($Ocorrencia['DATA'],  $resDadosFunc[$Ocorrencia['CHAPA']]['DATAS'])){
                            $resDadosFunc[$Ocorrencia['CHAPA']]['DATAS'][] = $Ocorrencia['DATA'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['CODCOLIGADA'] = $Ocorrencia['CODCOLIGADA'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['FERIADO'] = $Ocorrencia['FERIADO'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['CHAPA'] = $Ocorrencia['CHAPA'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['HORARIO'] = $Ocorrencia['HORARIO'];
                            
                            $resDadosFunc[$Ocorrencia['CHAPA']]['NOME'] = $Ocorrencia['NOME'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['CODFUNCAO'] = $Ocorrencia['CODFUNCAO'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['NOMEFUNCAO'] = $Ocorrencia['NOMEFUNCAO'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['CODSECAO'] = $Ocorrencia['CODSECAO'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['NOMESECAO'] = $Ocorrencia['NOMESECAO'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['codmotivo'] = $Ocorrencia['codmotivo'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['descricao_motivo'] = $Ocorrencia['descricao_motivo'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['log_ocorrencia'] = $Ocorrencia['log_ocorrencia'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['observacao'] = $Ocorrencia['observacao'];
                            $resDadosFunc[$Ocorrencia['CHAPA']]['gestor'] = $Ocorrencia['gestor'];
                        }
                    }
                }

            }
            
            $dados['resOcorrencia'] = false;
            $dados['resDadosFunc'] = $resDadosFunc;

            /*echo '<pre>';
            print_r($resFuncionario);
            exit();*/
            

        }

        $dados['resFuncionario'] = $resFuncionario ?? false;
        $dados['resJustificativas'] = $resJustificativas ?? false;
        

        $dados['resPeriodo'] = $this->mOcorrencia->ListarPeriodoPonto();
        $dados['resFilial'] = $this->mOcorrencia->ListarOcorrenciaFilial();
	
        $dados['resSecao'] = $this->mOcorrencia->ListarOcorrenciaSecao();
        $dados['resMotivo'] = $this->mOcorrencia->ListarOcorrenciaMotivo();
        $dados['resAbono'] = $this->mOcorrencia->ListarOcorrenciaAbono();
		$dados['congelado'] = $congelado;
        // return parent::ViewPortal('ponto/ocorrencia/configuracao', $dados);
        
        return parent::ViewPortal('ponto/ocorrencia/index', $dados);

    }
	 public function workflow(){
	


        $this->mOcorrencia->Workflow();
        exit();

    }
	
	public function config($tab = '1')
  {		 
		parent::VerificaPerfil('GLOBAL_RH');
    parent::VerificaPerfil('PONTO_CONFIG');

    $dados['_titulo'] = "Configuração Ocorrência de Ponto";

    // config.horario
		$dados['resHorario'] = $this->mOcorrencia->ListarOcorrenciaHorarios();
		$dados['resHorarioConfig'] = $this->mOcorrencia->ListarOcorrenciaHorariosPortal();
    
    //cargos
    $dados['resFuncao'] = model('PortalModel')->ListarFuncao();
    $dados['resMotorista'] = $this->mOcorrencia->ListaConfiguracaoMotorita();

    //tipos
    $dados['resTipos'] = $this->mOcorrencia->ListarOcorrenciaTipoPortal();

		$dados['tab'] = $tab;

		return parent::ViewPortal('ponto/ocorrencia/configuracao', $dados);

  }

  public function configWorkflow($tab = '1')
  {		 
		parent::VerificaPerfil('GLOBAL_RH');
    parent::VerificaPerfil('PONTO_CONFIG');

    $dados['_titulo'] = "Configuração Ocorrência de Ponto";

    // workflow
		$dados['resWorkflow'] = $this->mOcorrencia->ListaConfiguracaoWorkflow();
		$dados['resWorkflowRH'] = $this->mOcorrencia->ListaConfiguracaoWorkflowRH();
		$dados['resWorkflowFaltas'] = $this->mOcorrencia->ListaConfigWorkflowFaltas();
		
    $dados['tab'] = $tab;

		return parent::ViewPortal('ponto/ocorrencia/config_workflow', $dados);

  }

  public function motorista(){
        
        parent::VerificaPerfil('GLOBAL_RH');
        parent::VerificaPerfil('PONTO_CONFIG');
        $dados['_titulo'] = "Configuração de Cargos";
        $this->_breadcrumb->add($dados['_titulo'], '');
        $dados['resFuncao'] = model('PortalModel')->ListarFuncao();
        $dados['resMotorista'] = $this->mOcorrencia->ListaConfiguracaoMotorita();

		return parent::ViewPortal('ponto/ocorrencia/motorista', $dados);
    }

    public function config_ocorrencias(){
		 
    parent::VerificaPerfil('GLOBAL_RH');
    parent::VerificaPerfil('PONTO_CONFIG');
    $dados['_titulo'] = "Configuração Ocorrência de Ponto";
		$dados['resTipos'] = $this->mOcorrencia->ListarOcorrenciaTipoPortal();
		
		return parent::ViewPortal('ponto/ocorrencia/config_ocorrencias', $dados);

    }
	 public function salvahorario(){
		
		
		
        $horario = $_POST;
		
        $this->mOcorrencia->IUDHorario($horario);
		
		
		

    }
	public function deletaHorario(){
	
        $id = $_POST;
		
        $this->mOcorrencia->DELHorario($id);
	
    }
	
	 public function workflow2(){
        

		

        $this->mOcorrencia->Workflow2();
        exit();

  


    }
    
    public function log(){
		 
        $dados['_titulo'] = "Log do Workflow de ocorrência";
        $this->_breadcrumb->add($dados['_titulo'], 'ponto/ocorrencia/log');
		parent::VerificaPerfil('PONTO_OCORRENCIA_WORKFLOW_LOG');
        
        $dados['data_inicio'] = $this->request->getPost('data_inicio');
        $dados['data_fim'] = $this->request->getPost('data_fim');
        $dados['ListarWorkflowLog'] = ($dados['data_inicio'] != null || $dados['data_fim'] != null) ? $this->mOcorrencia->ListarWorkflowLog($dados) : false;
		
		return parent::ViewPortal('ponto/ocorrencia/log', $dados);

    }


    // ------------------------------------------------------------------
    // download ocorrência em excel
    // ------------------------------------------------------------------
    public function excel(){
		ini_set("pcre.backtrack_limit", "50000000");
		set_time_limit(60*90);
		ini_set('max_execution_time', 60*90);

        parent::VerificaPerfil('PONTO_OCORRENCIA');

        $periodo = $this->request->getPost('periodo');
        $resOcorrencia = false;

        if($periodo === null){
            notificacao('danger', 'Parâmetros não enviados.');
            redireciona(base_url('ponto/ocorrencia'));
        }

        $post = $_POST;
        $resOcorrencia = $this->mOcorrencia->ListarOcorrencia($post);
        $resMotivo = $this->mOcorrencia->ListarOcorrenciaMotivo();

        $periodo_fim = (int)date('Ym', strtotime(mb_substr($periodo, -10)));
        $hoje = (int)date('Ym');
        $congelado = ($periodo_fim < $hoje) ? true : false;

        $template_ativo = true;
        if(count(($resOcorrencia ?? array())) > 10000) $template_ativo = false;
        
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
        if($template_ativo){
            
            $spreadsheet->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('A1:O1')->applyFromArray($styleBorda);
            

            // cor do background
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('A1:O1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('006f49');

            // nome da aba da planilha
            $spreadsheet->getActiveSheet()->setTitle('Ocorrências');
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:O1'); // auto filtro no titulo

        }
        $arrayMotivo = false;
        if($resMotivo){
            foreach($resMotivo as $key => $Motivo){
                $arrayMotivo[$Motivo['id']] = $Motivo['descricao'];
            }
            //$arrayMotivo['O'] = 'OUTRO';
        }

        // titulo das colunas
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'COLIGADA');
        $sheet->setCellValue('B1', 'CHAPA');
        $sheet->setCellValue('C1', 'NOME');
        $sheet->setCellValue('D1', 'SITUAÇÃO');
        $sheet->setCellValue('E1', 'VISUALIZADO GESTOR');
        $sheet->setCellValue('F1', 'CENTRO DE CUSTO');
        $sheet->setCellValue('G1', 'DESCRIÇÃO CENTRO DE CUSTO');
        $sheet->setCellValue('H1', 'FUNÇÃO');
        $sheet->setCellValue('I1', 'CÓD. SEÇÃO');
        $sheet->setCellValue('J1', 'SEÇÃO');
        $sheet->setCellValue('K1', 'DATA');
        $sheet->setCellValue('L1', 'OCORRÊNCIA');
        $sheet->setCellValue('M1', 'VALOR');
        $sheet->setCellValue('N1', 'DETALHAMENTO DA OCORRÊNCIA');
        $sheet->setCellValue('O1', 'INFORMAÇÃO COMPLEMENTAR');
        $rows = 2;

        if($resOcorrencia){
            foreach($resOcorrencia as $key => $Ocorrencia){

                if($congelado){

                    $sheet->setCellValue('A' . $rows, $Ocorrencia['CODCOLIGADA']);
                    $sheet->setCellValue('B' . $rows, $Ocorrencia['CHAPA']);
                    $sheet->setCellValue('C' . $rows, $Ocorrencia['NOME']);
                    $sheet->setCellValue('D' . $rows, $Ocorrencia['CODSITUACAO']);
                    $sheet->setCellValue('E' . $rows, ((strlen(trim($Ocorrencia['gestor'])) > 0) ? 'Sim' : "Não"));
                    $sheet->setCellValue('F' . $rows, $Ocorrencia['CODCUSTO']);
                    $sheet->setCellValue('G' . $rows, $Ocorrencia['NOMECUSTO']);
                    $sheet->setCellValue('H' . $rows, $Ocorrencia['NOMEFUNCAO']);
                    $sheet->setCellValue('I' . $rows, $Ocorrencia['CODSECAO']);
                    $sheet->setCellValue('J' . $rows, $Ocorrencia['NOMESECAO']);
                    $sheet->setCellValue('K' . $rows, dtBr($Ocorrencia['DATA']));
                    
                    if($Ocorrencia['SISTEMA'] == "RM"){
                        switch($Ocorrencia['OCORRENCIA']){
                            case 'extra_permitido': $ocorrencia = 'Extra Acima do Permitido'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'extra': $ocorrencia = 'Extra em Escala Especial'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'jornada': $ocorrencia = 'Excesso de jornada'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'trabalho_dsr_folga': $ocorrencia = 'Trabalho em DSR ou Folga'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'trabalho_dsr_folga_descanso': $ocorrencia = 'Excesso de jornada semanal'; $valor_ocorrencia = m2h($Ocorrencia['COMPLEMENTO']); break;
                            case 'trabalho_ferias_afastamento': $ocorrencia = 'Trabalho em Férias ou Afastamentos'; $valor_ocorrencia = ($Ocorrencia['VALOR'] == 1) ? "Férias" : $Ocorrencia['COMPLEMENTO']; break;
                            case 'registro_manual': $ocorrencia = 'Registro Manual'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'trabalho_6dias': $ocorrencia = 'Trabalho superior à 6 (seis) dias consecutivos sem folga'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                            case 'excesso_abono_gestor': $ocorrencia = 'Excesso de Abono Gestor (Superior a 5 dias consecutivos)'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                            case 'interjornada': $ocorrencia = 'Interjornada'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                            case 'intrajornada': $ocorrencia = 'Intrajornada'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                            case 'registro_britanico': $ocorrencia = 'Registro Britânico'; 
                                $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; 
                                $valor_ocorrencia = explode(' ', $valor_ocorrencia);
                                $valor_ocorrencia = $valor_ocorrencia[0].' - '.$valor_ocorrencia[1];
                                break;
                            case 'sobreaviso': $ocorrencia = 'Sobreaviso'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'troca_menor_6_meses': $ocorrencia = 'Troca de escala menor que 6 meses'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO'].' mês(es)'; break;
                            case 'troca_menor_10_dias': $ocorrencia = 'Troca de escala menor que 3 dias'; $valor_ocorrencia = $Ocorrencia['VALOR'].' dia(s)'; break;
                            case 'pendente_termo_aditivo': $ocorrencia = 'Req. troca de escala pendente termo aditivo'; $valor_ocorrencia = 'Nº Req. '.$Ocorrencia['VALOR']; break;
                            default : $ocorrencia = '--'; $valor_ocorrencia = "--";
                        }
                    }else{
                        switch($Ocorrencia['OCORRENCIA']){
                            case 'extra_permitido': $ocorrencia = 'Extra Acima do Permitido'; break;
                            case 'extra': $ocorrencia = 'Extra em Escala Especial';  break;
                            case 'jornada': $ocorrencia = 'Excesso de jornada'; break;
                            case 'trabalho_dsr_folga': $ocorrencia = 'Trabalho em DSR ou Folga'; break;
                            case 'trabalho_dsr_folga_descanso': $ocorrencia = 'Excesso de jornada semanal';  break;
                            case 'trabalho_ferias_afastamento': $ocorrencia = 'Trabalho em Férias ou Afastamentos'; break;
                            case 'registro_manual': $ocorrencia = 'Registro Manual'; break;
                            case 'trabalho_6dias': $ocorrencia = 'Trabalho superior à 6 (seis) dias consecutivos sem folga'; break;
                            case 'excesso_abono_gestor': $ocorrencia = 'Excesso de Abono Gestor (Superior a 5 dias consecutivos)'; break;
                            case 'interjornada': $ocorrencia = 'Interjornada'; break;
                            case 'intrajornada': $ocorrencia = 'Intrajornada'; break;
                            case 'registro_britanico': $ocorrencia = 'Registro Britânico'; break;
                            case 'sobreaviso': $ocorrencia = 'Sobreaviso'; break;
                            case 'troca_menor_6_meses': $ocorrencia = 'Troca de escala menor que 6 meses'; break;
                            case 'troca_menor_10_dias': $ocorrencia = 'Troca de escala menor que 3 dias'; break;
                            case 'pendente_termo_aditivo': $ocorrencia = 'Req. troca de escala pendente termo aditivo'; break;
                            default : $ocorrencia = '--'; $valor_ocorrencia = "--";
                        }
                        $valor_ocorrencia = $Ocorrencia['VALOR'];
                    }

                    $sheet->setCellValue('L' . $rows, $ocorrencia);
                    $sheet->setCellValue('M' . $rows, $valor_ocorrencia);
                    $sheet->setCellValue('N' . $rows, (strlen(trim($Ocorrencia['codmotivo'])) > 0 ? $arrayMotivo[$Ocorrencia['codmotivo']] : ""));
                    //$sheet->setCellValue('K' . $rows, $Ocorrencia['descricao_motivo']);
                    $sheet->setCellValue('O' . $rows, $Ocorrencia['observacao']);

                }else{
                        
                    $sheet->setCellValue('A' . $rows, $Ocorrencia['CODCOLIGADA']);
                    $sheet->setCellValue('B' . $rows, $Ocorrencia['CHAPA']);
                    $sheet->setCellValue('C' . $rows, $Ocorrencia['NOME']);
                    $sheet->setCellValue('D' . $rows, $Ocorrencia['CODSITUACAO']);
                    $sheet->setCellValue('E' . $rows, ((strlen(trim($Ocorrencia['gestor'])) > 0) ? 'Sim' : "Não"));
                    $sheet->setCellValue('F' . $rows, $Ocorrencia['CODCUSTO']);
                    $sheet->setCellValue('G' . $rows, $Ocorrencia['NOMECUSTO']);
                    $sheet->setCellValue('H' . $rows, $Ocorrencia['NOMEFUNCAO']);
                    $sheet->setCellValue('I' . $rows, $Ocorrencia['CODSECAO']);
                    $sheet->setCellValue('J' . $rows, $Ocorrencia['NOMESECAO']);
                    $sheet->setCellValue('K' . $rows, dtBr($Ocorrencia['DATA']));

                    $ocorrencia = "";
                    switch($Ocorrencia['OCORRENCIA']){
                        case 'extra_permitido': $ocorrencia = 'Extra Acima do Permitido'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'extra': $ocorrencia = 'Extra em Escala Especial'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'jornada': $ocorrencia = 'Excesso de jornada'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'trabalho_dsr_folga': $ocorrencia = 'Trabalho em DSR ou Folga'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'trabalho_dsr_folga_descanso': $ocorrencia = 'Excesso de jornada semanal'; $valor_ocorrencia = ($Ocorrencia['COMPLEMENTO']); break;
                        case 'trabalho_ferias_afastamento': $ocorrencia = 'Trabalho em Férias ou Afastamentos'; $valor_ocorrencia = ($Ocorrencia['VALOR'] == 1) ? "Férias" : $Ocorrencia['COMPLEMENTO']; break;
                        case 'registro_manual': $ocorrencia = 'Registro Manual'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'trabalho_6dias': $ocorrencia = 'Trabalho superior à 6 (seis) dias consecutivos sem folga'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                        case 'excesso_abono_gestor': $ocorrencia = 'Excesso de Abono Gestor (Superior a 5 dias consecutivos)'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                        case 'interjornada': $ocorrencia = 'Interjornada'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                        case 'intrajornada': $ocorrencia = 'Intrajornada'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                        case 'registro_britanico': $ocorrencia = 'Registro Britânico'; 
                            $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; 
                            $valor_ocorrencia = explode(' ', $valor_ocorrencia);
                            $valor_ocorrencia = $valor_ocorrencia[0].' - '.$valor_ocorrencia[1];
                            break;
                        case 'sobreaviso': $ocorrencia = 'Sobreaviso'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'troca_menor_6_meses': $ocorrencia = 'Troca de escala menor que 6 meses'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO'].' mês(es)'; break;
                        case 'troca_menor_10_dias': $ocorrencia = 'Troca de escala menor que 3 dias'; $valor_ocorrencia = $Ocorrencia['VALOR'].' dia(s)'; break;
                        case 'pendente_termo_aditivo': $ocorrencia = 'Req. troca de escala pendente termo aditivo'; $valor_ocorrencia = 'Nº Req. '.$Ocorrencia['VALOR']; break;
                        default : $ocorrencia = '--'; $valor_ocorrencia = "--";
                    }

                    $sheet->setCellValue('L' . $rows, $ocorrencia);
                    $sheet->setCellValue('M' . $rows, $valor_ocorrencia);
                    $sheet->setCellValue('N' . $rows, (strlen(trim($Ocorrencia['codmotivo'])) > 0 ? $arrayMotivo[$Ocorrencia['codmotivo']] : ""));
                    //$sheet->setCellValue('K' . $rows, $Ocorrencia['descricao_motivo']);
                    $sheet->setCellValue('O' . $rows, $Ocorrencia['observacao']);
                    

                }
                if($template_ativo) $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':O'.$rows)->applyFromArray($styleBorda);
                $rows++;
            }
        }

        // ajusta as colunas ao tamanho do texto
        if($template_ativo){
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'A' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'B' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'C' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'D' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'E' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'F' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'G' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'H' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'I' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'J' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'K' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'L' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'M' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'N' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'O' )->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Disposition: attachment; filename=Ocorrências.xlsx' );
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer->save("php://output");
        exit();
        
    }

    public function excel_arquivo_morto()
    {

        ini_set("pcre.backtrack_limit", "50000000");
        set_time_limit(60*90);
        ini_set('max_execution_time', 60*90);
        // ini_set('memory_limit', '4096M');

        parent::VerificaPerfil('PONTO_OCORRENCIA');
        parent::VerificaPerfil('PONTO_OCORRENCIA_EXCEL_AM');

        $periodo = $this->request->getPost('periodo');
        $resOcorrencia = false;

        if($periodo === null){
            notificacao('danger', 'Parâmetros não enviados.');
            redireciona(base_url('ponto/ocorrencia'));
        }

        $post = $_POST;
        $resOcorrencia = $this->mOcorrencia->ListarOcorrenciaArquivoMorto($post);
        $resMotivo = $this->mOcorrencia->ListarOcorrenciaMotivo();

        $periodo_fim = (int)date('Ym', strtotime(mb_substr($periodo, -10)));
        $hoje = (int)date('Ym');
        $congelado = ($periodo_fim < $hoje) ? true : false;

        $template_ativo = true;
        if(count($resOcorrencia ?? 0) > 10000) $template_ativo = false;
        
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
        if($template_ativo){
            
            $spreadsheet->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleArray);
            $spreadsheet->getActiveSheet()->getStyle('A1:K1')->applyFromArray($styleBorda);
            

            // cor do background
            $spreadsheet
            ->getActiveSheet()
            ->getStyle('A1:K1')
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('006f49');

            // nome da aba da planilha
            $spreadsheet->getActiveSheet()->setTitle('Ocorrências');
            $spreadsheet->getActiveSheet()->setAutoFilter('A1:K1'); // auto filtro no titulo

        }
        $arrayMotivo = false;
        if($resMotivo){
            foreach($resMotivo as $key => $Motivo){
                $arrayMotivo[$Motivo['id']] = $Motivo['descricao'];
            }
            //$arrayMotivo['O'] = 'OUTRO';
        }

        // titulo das colunas
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'COLIGADA');
        $sheet->setCellValue('B1', 'CHAPA');
        $sheet->setCellValue('C1', 'NOME');
        $sheet->setCellValue('D1', 'VISUALIZADO GESTOR');
        $sheet->setCellValue('E1', 'FUNÇÃO');
        $sheet->setCellValue('F1', 'SEÇÃO');
        $sheet->setCellValue('G1', 'DATA');
        $sheet->setCellValue('H1', 'OCORRÊNCIA');
        $sheet->setCellValue('I1', 'VALOR');
        $sheet->setCellValue('J1', 'DETALHAMENTO DA OCORRÊNCIA');
        $sheet->setCellValue('K1', 'INFORMAÇÃO COMPLEMENTAR');
        $rows = 2;

        if($resOcorrencia){
            foreach($resOcorrencia as $key => $Ocorrencia){

                if($congelado){

                    $sheet->setCellValue('A' . $rows, $Ocorrencia['CODCOLIGADA']);
                    $sheet->setCellValue('B' . $rows, $Ocorrencia['CHAPA']);
                    $sheet->setCellValue('C' . $rows, $Ocorrencia['NOME']);
                    $sheet->setCellValue('D' . $rows, ((strlen(trim($Ocorrencia['gestor'])) > 0) ? 'Sim' : "Não"));
                    $sheet->setCellValue('E' . $rows, $Ocorrencia['NOMEFUNCAO']);
                    $sheet->setCellValue('F' . $rows, $Ocorrencia['NOMESECAO']);
                    $sheet->setCellValue('G' . $rows, dtBr($Ocorrencia['DATA']));

                    $ocorrencia = "";
                    
                    if($Ocorrencia['SISTEMA'] == "RM"){
                        switch($Ocorrencia['OCORRENCIA']){
                            case 'extra_permitido': $ocorrencia = 'Extra Acima do Permitido'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'extra': $ocorrencia = 'Extra em Escala Especial'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'jornada': $ocorrencia = 'Excesso de jornada'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'trabalho_dsr_folga': $ocorrencia = 'Trabalho em DSR ou Folga'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'trabalho_dsr_folga_descanso': $ocorrencia = 'Excesso de jornada semanal'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'trabalho_ferias_afastamento': $ocorrencia = 'Trabalho em Férias ou Afastamentos'; $valor_ocorrencia = ($Ocorrencia['VALOR'] == 1) ? "Férias" : "Afastamento"; break;
                            case 'registro_manual': $ocorrencia = 'Registro Manual'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'trabalho_6dias': $ocorrencia = 'Trabalho superior à 6 (seis) dias consecutivos sem folga'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']." Dias"; break;
                            case 'excesso_abono_gestor': $ocorrencia = 'Excesso de Abono Gestor (Superior a 5 dias consecutivos)'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']." Dias"; break;
                            case 'interjornada': $ocorrencia = 'Interjornada ou Intrajornada'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                            case 'registro_britanico': $ocorrencia = 'Registro Britânico'; 
                                $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; 
                                $valor_ocorrencia = explode(' ', $valor_ocorrencia);
                                $valor_ocorrencia = $valor_ocorrencia[0].' - '.$valor_ocorrencia[1];
                                break;
                            case 'sobreaviso': $ocorrencia = 'Sobreaviso'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                            case 'troca_menor_6_meses': $ocorrencia = 'Troca de escala menor que 6 meses'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO'].' mês(es)'; break;
                            case 'troca_menor_10_dias': $ocorrencia = 'Troca de escala menor que 3 dias'; $valor_ocorrencia = $Ocorrencia['VALOR'].' dia(s)'; break;
                            case 'pendente_termo_aditivo': $ocorrencia = 'Req. troca de escala pendente termo aditivo'; $valor_ocorrencia = 'Nº Req. '.$Ocorrencia['VALOR']; break;
                            default : $ocorrencia = '--'; $valor_ocorrencia = "--";
                        }
                    }else{
                        switch($Ocorrencia['OCORRENCIA']){
                            case 'extra_permitido': $ocorrencia = 'Extra Acima do Permitido'; break;
                            case 'extra': $ocorrencia = 'Extra em Escala Especial';  break;
                            case 'jornada': $ocorrencia = 'Excesso de jornada'; break;
                            case 'trabalho_dsr_folga': $ocorrencia = 'Trabalho em DSR ou Folga'; break;
                            case 'trabalho_dsr_folga_descanso': $ocorrencia = 'Excesso de jornada semanal';  break;
                            case 'trabalho_ferias_afastamento': $ocorrencia = 'Trabalho em Férias ou Afastamentos'; break;
                            case 'registro_manual': $ocorrencia = 'Registro Manual'; break;
                            case 'trabalho_6dias': $ocorrencia = 'Trabalho superior à 6 (seis) dias consecutivos sem folga'; break;
                            case 'excesso_abono_gestor': $ocorrencia = 'Excesso de Abono Gestor (Superior a 5 dias consecutivos)'; break;
                            case 'interjornada': $ocorrencia = 'Interjornada ou Intrajornada'; break;
                            case 'registro_britanico': $ocorrencia = 'Registro Britânico'; break;
                            case 'sobreaviso': $ocorrencia = 'Sobreaviso'; break;
                            case 'troca_menor_6_meses': $ocorrencia = 'Troca de escala menor que 6 meses'; break;
                            case 'troca_menor_10_dias': $ocorrencia = 'Troca de escala menor que 3 dias'; break;
                            case 'pendente_termo_aditivo': $ocorrencia = 'Req. troca de escala pendente termo aditivo'; break;
                            default : $ocorrencia = '--'; $valor_ocorrencia = "--";
                        }
                        $valor_ocorrencia = $Ocorrencia['VALOR'];
                    }

                    $sheet->setCellValue('H' . $rows, $ocorrencia);
                    $sheet->setCellValue('I' . $rows, $valor_ocorrencia);
                    $sheet->setCellValue('J' . $rows, (strlen(trim($Ocorrencia['codmotivo'])) > 0 ? $arrayMotivo[$Ocorrencia['codmotivo']] : ""));
                    //$sheet->setCellValue('K' . $rows, $Ocorrencia['descricao_motivo']);
                    $sheet->setCellValue('K' . $rows, $Ocorrencia['observacao']);

                }else{
                        
                    $sheet->setCellValue('A' . $rows, $Ocorrencia['CODCOLIGADA']);
                    $sheet->setCellValue('B' . $rows, $Ocorrencia['CHAPA']);
                    $sheet->setCellValue('C' . $rows, $Ocorrencia['NOME']);
                    $sheet->setCellValue('D' . $rows, ((strlen(trim($Ocorrencia['gestor'])) > 0) ? 'Sim' : "Não"));
                    $sheet->setCellValue('E' . $rows, $Ocorrencia['NOMEFUNCAO']);
                    $sheet->setCellValue('F' . $rows, $Ocorrencia['NOMESECAO']);
                    $sheet->setCellValue('G' . $rows, dtBr($Ocorrencia['DATA']));

                    $ocorrencia = "";
                    switch($Ocorrencia['OCORRENCIA']){
                        case 'extra_permitido': $ocorrencia = 'Extra Acima do Permitido'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'extra': $ocorrencia = 'Extra em Escala Especial'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'jornada': $ocorrencia = 'Excesso de jornada'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'trabalho_dsr_folga': $ocorrencia = 'Trabalho em DSR ou Folga'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'trabalho_dsr_folga_descanso': $ocorrencia = 'Excesso de jornada semanal'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'trabalho_ferias_afastamento': $ocorrencia = 'Trabalho em Férias ou Afastamentos'; $valor_ocorrencia = ($Ocorrencia['VALOR'] == 1) ? "Férias" : "Afastamento"; break;
                        case 'registro_manual': $ocorrencia = 'Registro Manual'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'trabalho_6dias': $ocorrencia = 'Trabalho superior à 6 (seis) dias consecutivos sem folga'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']." Dias"; break;
                        case 'excesso_abono_gestor': $ocorrencia = 'Excesso de Abono Gestor (Superior a 5 dias consecutivos)'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']." Dias"; break;
                        case 'interjornada': $ocorrencia = 'Interjornada ou Intrajornada'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; break;
                        case 'registro_britanico': $ocorrencia = 'Registro Britânico'; 
                            $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; 
                            $valor_ocorrencia = explode(' ', $valor_ocorrencia);
                            $valor_ocorrencia = $valor_ocorrencia[0].' - '.$valor_ocorrencia[1];
                            break;
                        case 'sobreaviso': $ocorrencia = 'Sobreaviso'; $valor_ocorrencia = m2h($Ocorrencia['VALOR']); break;
                        case 'troca_menor_6_meses': $ocorrencia = 'Troca de escala menor que 6 meses'; $valor_ocorrencia = $Ocorrencia['COMPLEMENTO'].' mês(es)'; break;
                        case 'troca_menor_10_dias': $ocorrencia = 'Troca de escala menor que 3 dias'; $valor_ocorrencia = $Ocorrencia['VALOR'].' dia(s)'; break;
                        case 'pendente_termo_aditivo': $ocorrencia = 'Req. troca de escala pendente termo aditivo'; $valor_ocorrencia = 'Nº Req. '.$Ocorrencia['VALOR']; break;
                        default : $ocorrencia = '--'; $valor_ocorrencia = "--";
                    }

                    $sheet->setCellValue('H' . $rows, $ocorrencia);
                    $sheet->setCellValue('I' . $rows, $valor_ocorrencia);
                    $sheet->setCellValue('J' . $rows, (strlen(trim($Ocorrencia['codmotivo'])) > 0 ? $arrayMotivo[$Ocorrencia['codmotivo']] : ""));
                    //$sheet->setCellValue('K' . $rows, $Ocorrencia['descricao_motivo']);
                    $sheet->setCellValue('K' . $rows, $Ocorrencia['observacao']);
                    

                }
                if($template_ativo) $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray($styleBorda);
                $rows++;
            }
        }

        // ajusta as colunas ao tamanho do texto
        if($template_ativo){
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'A' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'B' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'C' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'D' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'E' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'F' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'G' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'H' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'I' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'J' )->setAutoSize(true);
            $spreadsheet->getActiveSheet ()->getColumnDimension ( 'K' )->setAutoSize(true);
        }
        

        $writer = new Xlsx($spreadsheet);

        header('Content-Disposition: attachment; filename=Ocorrências.xlsx' );
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer->save("php://output");
        exit();

    }

    public function GravaLogOcorrencia(){

        // $this->mOcorrencia->CargaDadosOcorrencia();

        // exit();

        $log = $this->mOcorrencia->GravaLogOcorrenciaColigadas();

    }

    public function importacao(){
		
		// if($_SESSION['log_id'] == 1){
			
			// $this->mOcorrencia->CargaDadosOcorrencia();
			// exit();
		// }

        parent::VerificaPerfil('PONTO_OCORRENCIA_IMPORTACAO');
		$dados['_titulo'] = "Importar Ocorrências";
        $this->_breadcrumb->add($dados['_titulo'], 'ponto/ocorrencia/importacao');
        
		return parent::ViewPortal('ponto/ocorrencia/importacao', $dados);

    }

    public function anexo($id_anexo = false)
    {
        if(!$id_anexo){
            notificacao('danger', 'Arquivo não localizado.');
            redireciona(base_url('/ponto/ocorrencia'));
        }

        $id_anexo = base64_decode($id_anexo);

        $dadosAnexo = $this->mOcorrencia->DadosAnexo($id_anexo);

        if(!$dadosAnexo){
            notificacao('danger', 'Arquivo não localizado.');
            redireciona(base_url('/ponto/ocorrencia'));
        }

        header('Content-Type: application/'. $dadosAnexo[0]->file_type);
        header('Content-Disposition: attachment; filename='. $dadosAnexo[0]->file_name);
        header('Pragma: no-cache');

        echo base64_decode($dadosAnexo[0]->file_data);
        exit();
        
    }

    public function cron_indice_horario()
    {
        ini_set("pcre.backtrack_limit", "50000000");
        set_time_limit(60*90);
        ini_set('max_execution_time', 60*90);
        $this->mOcorrencia->cron_indice_horario();
    }
    

    //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            //-------------------------------------
            // grava justificativa da ocorrência
            case 'grava_justificativa':
                $dados['file'] = $_FILES;
                exit($this->mOcorrencia->CadastrarOcorrenciaJustificativa($dados));
                break;
            //-------------------------------------
            // grava observação da ocorrência
            case 'grava_observacao':
                exit($this->mOcorrencia->CadastrarOcorrenciaObservacao($dados));
                break;
            //-------------------------------------
            // grava justificativa coletiva da ocorrência
            case 'grava_justificativa_coletiva':
                $dados['file'] = $_FILES;
                exit($this->mOcorrencia->CadastrarOcorrenciaJustificativaColetiva($dados));
                break;
            //-------------------------------------
            // grava informação complementar coletiva da ocorrência
            case 'grava_informacao_complementar_coletiva':
                exit($this->mOcorrencia->CadastrarInformacaoComplementarColetiva($dados));
                break;
            //-------------------------------------
            // grava configuração dos tipos de ocorrências
            case 'grava_configuracao_tipo_ocorrencia':
                exit($this->mOcorrencia->CadastrarConfiguracaoTipoOcorrencia($dados));
                break;
            //-------------------------------------
            // importação de ocorrencias
            case 'ocorrencia_importacao':
                $dados['documento'] = $_FILES;
                exit($this->mOcorrencia->ImportacaoOcorrencias($dados));
                break;
            case 'config_horario':
                exit($this->mOcorrencia->ConfiguracaoHorario($dados));
                break;
            case 'excluir_anexo':
                exit($this->mOcorrencia->ExcluirAnexo($dados));
                break;
            case 'carrega_dados_horario':
                exit(json_encode($this->mOcorrencia->ListaDadosHorario($dados)));
                break;
            case 'salva_dados_horario':
                exit($this->mOcorrencia->SalvaDadosHorario($dados['dados']));
                break;
            case 'lista_batidas':
                exit(json_encode($this->mOcorrencia->ListaBatidasDia($dados)));
                break;
            case 'salva_configuracao_workflow':
                exit($this->mOcorrencia->ConfiguracaoWorkflow($dados['dados']));
                break;
            case 'salva_configuracao_workflow_RH':
                exit($this->mOcorrencia->ConfiguracaoWorkflowRH($dados['dados']));
                break;
            case 'salva_config_workflow_faltas':
                exit($this->mOcorrencia->ConfigWorkflowFaltas($dados['dados']));
                break;
            case 'config_motorista':
                exit($this->mOcorrencia->ConfiguracaoMotorista($dados));
                break;
            case 'salvar_param_motorista':
                exit($this->mOcorrencia->SalvarDadosMotorista($dados));
                break;
            case 'config_motorista_check':
                exit($this->mOcorrencia->ConfigMotoristaCheck($dados));
                break;
            case 'excluir_motorista':
                exit($this->mOcorrencia->ExcluirMotorista($dados));
                break;
                
        }

        

    }

}