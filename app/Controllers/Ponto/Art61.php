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
    // Actions Prêmios
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


}