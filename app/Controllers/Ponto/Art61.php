<?php
namespace App\Controllers\Ponto;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controllers\BaseController;

Class Art61 extends BaseController {

    private $mArt61;
    
    public function __construct()
    {
        parent::__construct('Ponto');
        $this->_moduloName = '<i class="mdi mdi-account-star-outline"></i> Ponto';
        $this->mArt61 = model('Ponto/Art61Model');
        
    }

    public function index()
    {
        redirect('ponto/art61/config');
    }
    
    public function config($tab = '1')
    {
    
        parent::VerificaPerfil('GLOBAL_RH');
        $dados['_titulo'] = "Artigo.61";
        $this->_breadcrumb->add($dados['_titulo'], '');
        $dados['resConfig'] = $this->mArt61->ListarConfigArt61();
        $dados['resPonto'] = $this->mArt61->ListarPeriodoPonto();
        $dados['resCentroCusto'] = $this->mArt61->ListarCentroCusto();
        $dados['resAreas'] = $this->mArt61->ListarCCustoArea();
        $dados['resProrroga'] = $this->mArt61->ListarProrroga();
        $dados['resExcecao'] = $this->mArt61->ListarExcecao();
        $dados['resEventos'] = $this->mArt61->ListarEvento();
        $dados['resFiliais'] = $this->mArt61->ListarFilial();
        $dados['resColab'] = $this->mArt61->ListarColab();
        $dados['resCodeventos'] = $this->mArt61->ListarCodevento();
        $dados['tab'] = $tab;
        
        return parent::ViewPortal('ponto/art61/config', $dados);

    }

    //-----------------------------------------------------------
    // Actions Art61
    //-----------------------------------------------------------
    public function action($act)
    {
        //parent::VerificaPerfil('GLOBAL_RH');
        
        $dados = $_POST;
        if(!$dados) return $act;
        
        switch($act){
            
            //-------------------------------------
            // salvar configurações
            case 'salva_config':
                exit($this->mArt61->SalvarConfig($dados));
                break;
            //-------------------------------------
            
            //-------------------------------------
            // gravar centro de custo
            case 'grava_ccusto':
                exit($this->mArt61->Grava_CCusto($dados));
                break;
            //-------------------------------------
            
            //-------------------------------------
            // deleta centro de custo
            case 'deleta_ccusto':
                exit($this->mArt61->Deleta_CCusto($dados));
                break;
            //-------------------------------------

            //-------------------------------------
            // gravar prorrogação
            case 'grava_prorroga':
                exit($this->mArt61->Grava_Prorroga($dados));
                break;
            //-------------------------------------
            
            //-------------------------------------
            // deleta prorrogação
            case 'deleta_prorroga':
                exit($this->mArt61->Deleta_Prorroga($dados));
                break;
            //-------------------------------------

            //-------------------------------------
            // gravar excecao
            case 'grava_excecao':
                exit($this->mArt61->Grava_Excecao($dados));
                break;
            //-------------------------------------
            
            //-------------------------------------
            // deleta excecao
            case 'deleta_excecao':
                exit($this->mArt61->Deleta_Excecao($dados));
                break;
            //-------------------------------------

            //-------------------------------------
            // gravar codevento
            case 'grava_evento':
                exit($this->mArt61->Grava_Codevento($dados));
                break;
            //-------------------------------------
            
            //-------------------------------------
            // deleta excecao
            case 'deleta_evento':
              exit($this->mArt61->Deleta_Codevento($dados));
              break;
          //-------------------------------------

          //-------------------------------------
            // grava nova solicitacao
            case 'nova_solicitacao':
              exit($this->mArt61->Nova_Solicitacao($dados));
              break;
          //-------------------------------------

          //-------------------------------------
            // apaga solicitacao
            case 'apaga_requisicao':
              exit($this->mArt61->Apaga_Requisicao($dados));
              break;
          //-------------------------------------

          //-------------------------------------
            // grava novo colaborador
            case 'novo_colab_req':
              exit($this->mArt61->Novo_Colaborador($dados));
              break;
          //-------------------------------------

          //-------------------------------------
            // apaga colaborador
            case 'apaga_colaborador':
              exit($this->mArt61->Apaga_Colaborador($dados));
              break;
          //-------------------------------------

          //-------------------------------------
            // grava novo colaborador
            case 'processar_req':
              exit($this->mArt61->Processar_Req($dados));
              break;
          //-------------------------------------

          //-------------------------------------
            // importa centro de custo
            case 'importar':
                $dados['documento'] = $_FILES;
                exit($this->mArt61->Importa_Area($dados));
                break;
            //-------------------------------------
            
        }

    }

  // ------------------------------------------------------------------
  // importar aread do excel
  // ------------------------------------------------------------------
  public function importar_areas()
  {
    parent::VerificaPerfil('GLOBAL_RH');

	  $dados['_titulo'] = "Importar Centros de Custos - Artigo 61";
    $this->_breadcrumb->add($dados['_titulo'], 'ponto/art61/config');
        
	return parent::ViewPortal('ponto/art61/importa_areas', $dados);

  }


    // ------------------------------------------------------------------
    // exportar premissas para excel
    // ------------------------------------------------------------------
    public function exportar_areas()
    {
        parent::VerificaPerfil('GLOBAL_RH');

        $resCCusto = $this->mArt61->ListarCCustoArea();

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

        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleBorda);

        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A1:E1')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');

        // nome da aba da planilha
        $spreadsheet->getActiveSheet()->setTitle('Centros de Custo - Artigo 61');
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:E1'); // auto filtro no titulo

        // titulo das colunas
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'COD_COLIGADA');
        $sheet->setCellValue('B1', 'COD_CCUSTO');
        $sheet->setCellValue('C1', 'NOME_CCUSTO');
        $sheet->setCellValue('D1', 'DIRETORIA');
        $sheet->setCellValue('E1', 'AREA');

        $rows = 2;

        if($resCCusto){
            foreach($resCCusto as $key => $CCusto){
                $sheet->setCellValue('A' . $rows, $CCusto['coligada']);
                $sheet->setCellValue('B' . $rows, $CCusto['codcusto']);
                $sheet->setCellValue('C' . $rows, $CCusto['nome_ccusto']);
                $sheet->setCellValue('D' . $rows, $CCusto['diretoria']);
                $sheet->setCellValue('E' . $rows, $CCusto['area']);
                
                $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->applyFromArray($styleBorda);
                $rows++;
            }
        }

        for($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Disposition: attachment; filename=Centros de Custo - Artigo 61.xlsx' ); 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer->save("php://output");

        exit();

    }

    public function solicitacao(){

      $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
      if(!$dados['rh']) { parent::VerificaPerfil('ART61_SOLICITACAO'); }

      $dados['_titulo'] = "Histórico de solicitação";
      $dados['periodo'] = $this->request->getPost('periodo');
      //$dados['acessoPermitido']       = ($dados['isGestorHierarquia'] || $dados['perfilRH'] || $dados['isLiderAprovador'] || $dados['isGestorSubstituto']) ? true : false;
      $dados['acessoPermitido']  = true;
        
      $periodo = ($dados['periodo'] != null) ? $dados['periodo'] : '';
      
      $dados['resColab'] = $this->mArt61->ListarColabSolicitacao($dados['rh']); 
      $dados['resListaArt61'] = $this->mArt61->ListarArt61($periodo); 
      $dados['resPeriodo'] = $this->mArt61->ListarPeriodoPonto();
      $dados['resConfig'] = $this->mArt61->ListarConfigArt61();
      $dados['resProroga'] = $this->mArt61->ListarProrroga();
      
      return parent::ViewPortal('ponto/art61/solicitacao', $dados);

  }

  public function solicitacao_chapas($id){

    $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
    if(!$dados['rh']) { parent::VerificaPerfil('ART61_SOLICITACAO'); }

    $dados['_titulo'] = "Detalhes da solicitação";
    $dados['id_requisicao'] = $id;
    $dados['pode_editar'] = true;
    $dados['acessoPermitido'] = true;
    $dados['em_analise'] = ''; //pode ser 'disabled' para evitar o botão processar;

    /*
    $mAprova = model('Ponto/AprovaModel');
    $mRel = model('Relatorio/RelatorioModel');
    $mPortal = model('PortalModel');
    
    $dados['resSecao'] = $mAprova->listaSecaoUsuario(false, $dados);
    $dados['resFuncao'] = $mPortal->funcoesPodeVer($dados);

    $dados['filtro'] = $this->request->getPost('filtro');
    $dados['filtro_tipo_troca'] = $this->request->getPost('filtro_tipo_troca');
    $dados['filtro_colaborador'] = $this->request->getPost('filtro_colaborador');
    $dados['filtro_secao'] = $this->request->getPost('filtro_secao');
    $dados['filtro_funcao'] = $this->request->getPost('filtro_funcao');
    $dados['data_inicio'] = $this->request->getPost('data_inicio');
    $dados['data_fim'] = $this->request->getPost('data_fim');
    
    $dataInicio = ($dados['data_inicio'] != null) ? $dados['data_inicio'] : date('Y-m-d');
    
    $dados['colaboradores'] = $mPortal->CarregaColaboradores($dataInicio, $dados, true);
    */
    $dados['resColab'] = $this->mArt61->ListarColabSolicitacao($dados['rh']); 
    $dados['resListaArt61'] = $this->mArt61->ListarArt61('',$id); 
    $dados['resReqChapas'] = $this->mArt61->ListarReqChapas($id);
    
    return parent::ViewPortal('ponto/art61/solicitacao_chapas', $dados);

  }

}