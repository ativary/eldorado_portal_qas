<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Saldobancohoras extends BaseController {

    private $mEspelho;
    private $mPortal;
    private $mSaldoBanco;

    public function __construct(){
        parent::__construct('Ponto'); // sempre manter

        $this->_moduloName = '<i class="fas fa-clock"></i> Ponto';
        $this->mEspelho    = model('Ponto/EspelhoModel');
        $this->mSaldoBanco    = model('Ponto/SaldobancohorasModel');
        $this->mPortal    = model('PortalModel');
    }
    
    public function index()
    {
        parent::VerificaPerfil('PONTO_SALDOBANCOHORAS');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

        $dados['_titulo']    = "Consultar Saldo Banco de Horas";
        $this->_breadcrumb->add($dados['_titulo'], 'ponto/espelho');
        
        $dados['codsecao']              = $this->request->getPost('secao');
        $dados['chapa']                 = $this->request->getPost('chapa');
        $dados['periodo']               = $this->request->getPost('periodo');
        $dados['action']                = $this->request->getPost('action');
        // $dados['resSecaoGestor']        = $this->mSaldoBanco->ListarSecaoUsuario();

        $mAprova = model('Ponto/AprovaModel');
        $dados['resSecaoGestor'] = $mAprova->listaSecaoUsuario(false, $dados);

        $dados['resSecao']              = $this->mPortal->ListarSecao();
        $dados['resFuncionarioSecao']   = $this->mSaldoBanco->ListarFuncionariosSecao($dados['codsecao'], $dados);
        $dados['resSaldoBancoHoras']    = ($dados['action'] == 'filtro') ? $this->mSaldoBanco->SaldoBancoHoras($dados['codsecao'], $dados['chapa'], $dados['periodo'], $dados) : false;
        $mCritica = model('Ponto/CriticaModel');
        $dados['resPeriodo']          = $mCritica->ListarCriticaPeriodoRM();
        
        return parent::ViewPortal('ponto/saldobancohoras/index', $dados);
    }

    public function excel()
    {
        parent::VerificaPerfil('PONTO_SALDOBANCOHORAS');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

        $codsecao               = $this->request->getPost('codsecao');
        $chapa                  = $this->request->getPost('chapa');
        $periodo                = $this->request->getPost('periodo');
        $resSecao               = $this->mPortal->ListarSecao();
        $resPeriodo             = $this->mSaldoBanco->ListarPeriodoPonto();
        $resSaldoBancoHoras     = $this->mSaldoBanco->SaldoBancoHoras($codsecao, $chapa, $periodo, $dados);

        $spreadsheet = new Spreadsheet();

        // cor do texto
        $styleRed = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => 'ff0000')
            )
        );
        $styleGreen = array(
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '3ebb17')
            )
        );
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

        $spreadsheet->getActiveSheet()->mergeCells('B1:E1');
        $spreadsheet->getActiveSheet()->mergeCells('B2:E2');
        $spreadsheet->getActiveSheet()->mergeCells('B3:E3');
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A4:E4')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A1:E1')->applyFromArray($styleBorda);
        $spreadsheet->getActiveSheet()->getStyle('A2:E2')->applyFromArray($styleBorda);
        $spreadsheet->getActiveSheet()->getStyle('A3:E3')->applyFromArray($styleBorda);

        // cor do background
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A1')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A2')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A3')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');
        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A4:E4')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');

        // nome da aba da planilha
        $spreadsheet->getActiveSheet()->setTitle('Saldo Banco de Horas');
        $spreadsheet->getActiveSheet()->setAutoFilter('A4:E4'); // auto filtro no titulo

        // titulo das colunas
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'SEÇÃO');
        $sheet->setCellValue('A2', 'FUNCIONÁRIO');
        $sheet->setCellValue('A3', 'PERÍODO');
        $sheet->setCellValue('A4', 'CHAPA');
        $sheet->setCellValue('B4', 'NOME');
        $sheet->setCellValue('C4', 'DATA VENCIMENTO');
        $sheet->setCellValue('D4', 'SALDO POSITIVO');
        $sheet->setCellValue('E4', 'SALDO NEGATIVO');

        $nomeSecao          = "- Todos -";
        $nomeFuncionario    = "- Todos -";
        $nomePeriodo        = "";

        if($codsecao != null){
            $nomeSecao = extrai_valor($resSecao, $codsecao, 'CODIGO', 'DESCRICAO').' - '.$codsecao;
            unset($resSecao);
        }
        if($chapa != null){
            $nomeFuncionario = extrai_valor($resSaldoBancoHoras, $chapa, 'CHAPA', 'NOME').' - '.$chapa;
        }
        if($periodo != null){
            foreach($resPeriodo as $key => $Periodo){
                if($Periodo['FIMMENSAL'] == $periodo){
                    $nomePeriodo = dtBr($Periodo['INICIOMENSAL']). ' à '.dtBr($Periodo['FIMMENSAL']);
                    break;
                }
                unset($resPeriodo[$key], $Periodo, $key);
            }
        }

        $sheet->setCellValue('B1', $nomeSecao);
        $sheet->setCellValue('B2', $nomeFuncionario);
        $sheet->setCellValue('B3', $nomePeriodo);

        $rows = 5;
        
        $total_positivo = 0;
        $total_negativo = 0;
        if($resSaldoBancoHoras){
            foreach($resSaldoBancoHoras as $key => $SaldoBancoHoras){
                $sheet->setCellValue('A' . $rows, $SaldoBancoHoras['CHAPA']);
                $sheet->setCellValue('B' . $rows, $SaldoBancoHoras['NOME']);
                $sheet->setCellValue('C' . $rows, (strlen(trim($SaldoBancoHoras['DATAFIMLIMITEBH'])) > 0) ? dtBr($SaldoBancoHoras['DATAFIMLIMITEBH']) : "");
                $sheet->setCellValue('D' . $rows, (($SaldoBancoHoras['SALDO'] >= 0) ? m2h($SaldoBancoHoras['SALDO']) : 0));
                $sheet->setCellValue('E' . $rows, (($SaldoBancoHoras['SALDO'] < 0) ? m2h($SaldoBancoHoras['SALDO']) : 0));
                
                $spreadsheet->getActiveSheet()->getStyle('D'.$rows)->applyFromArray($styleGreen);
                $spreadsheet->getActiveSheet()->getStyle('E'.$rows)->applyFromArray($styleRed);
                $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':E'.$rows)->applyFromArray($styleBorda);
                $rows++;

                $total_positivo += (($SaldoBancoHoras['SALDO'] >= 0) ? $SaldoBancoHoras['SALDO'] : 0);
                $total_negativo += (($SaldoBancoHoras['SALDO'] < 0) ? $SaldoBancoHoras['SALDO'] : 0);

            }
        }

        $sheet->setCellValue('C' . $rows, "Total");
        $sheet->setCellValue('D' . $rows, m2h($total_positivo));
        $sheet->setCellValue('E' . $rows, m2h($total_negativo));
        $spreadsheet->getActiveSheet()->getStyle('D'.$rows)->applyFromArray($styleGreen);
        $spreadsheet->getActiveSheet()->getStyle('E'.$rows)->applyFromArray($styleRed);


        for($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
        }
        
        $writer = new Xlsx($spreadsheet);

        header('Content-Disposition: attachment; filename=SaldoBancoHoras.xlsx' );
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer->save("php://output");
        exit();

    }
    
    //-----------------------------------------------------------
    // Actions
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        $arquivos = $_FILES;
        if(!$dados) return false;
        
        switch($act){

            //-------------------------------------
            case 'listar_funcionarios_secao':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit(json_encode($this->mSaldoBanco->ListarFuncionariosSecao($dados['codsecao'], $dados)));
                break;
            
            //-------------------------------------
            case 'cadastrar_batida':
                exit($this->mEspelho->CadastrarBatida($dados));
                break;
            
            //-------------------------------------
            case 'alterar_batida':
                exit($this->mEspelho->AlterarBatida($dados));
                break;
            
            //-------------------------------------
            case 'cadastrar_abono':
                exit($this->mEspelho->CadastrarAbono($dados, $arquivos));
                break;
                
            //-------------------------------------
            case 'listar_abonos':
              
                $result = $this->mEspelho->HistoricoSolicitacaoAbonos($dados['dataPonto'], $dados['chapa']);
                return $this->response->setJSON($result);
                break;

            //-------------------------------------
            case 'excluir_abono_rm':
                exit($this->mEspelho->ExcluirAbonoRM($dados));
                break;
            
            //-------------------------------------
            case 'excluir_abono_pt':
                exit($this->mEspelho->ExcluirAbonoPT($dados));
                break;

            //-------------------------------------
            case 'excluir_batida_rm':
                exit($this->mEspelho->ExcluirBatidaRM($dados));
                break;

            //-------------------------------------
            case 'excluir_batida_pt':
                exit($this->mEspelho->ExcluirBatidaPT($dados));
                break;

            //-------------------------------------
            case 'recalcular_espelho':

                $wsTotvs = model('WsrmModel');
                $wsTotvs->ws_recalculo_ponto($dados['chapa'], $dados['data']);
                exit(responseJson('success', '<b>Recalculo</b> processado com sucesso.'));
                break;

            //-------------------------------------
            case 'cadastrar_justificativa_extra':
                exit($this->mEspelho->CadastrarJustificativaExtra($dados));
                break;

        }

        

    }

}
