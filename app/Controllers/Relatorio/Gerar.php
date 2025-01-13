<?php
namespace App\Controllers\Relatorio;
use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

set_time_limit(60*60);
// ini_set('memory_limit', '2048M');

Class Gerar extends BaseController {

    private $mRel;
    private $mPortal;

    public function __construct()
    {
        
        parent::__construct('Relatório'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-cash-usd"></i> Relatório';
        $this->_breadcrumb->add('Gerar Relatório', 'relatorio/gerar');
        $this->mRel = model('Relatorio/RelatorioModel');
        $this->mPortal = model('PortalModel');

    }

    public function index()
    {

        parent::VerificaPerfil('RELATORIO_GERAR');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo']   = "Gerar Relatório";

        $mAprova = model('Ponto/AprovaModel');
        $dados['resSecao'] = $mAprova->listaSecaoUsuario(false, $dados);
        $dados['resFuncao'] = $this->mRel->listaFuncao();
        $dados['resFuncionarioSecao'] = $this->mPortal->ListaFuncionarioSecao($dados);

        $dados['colunas']   = $this->request->getPost('colunas');
        $dados['relatorio'] = $this->request->getPost('relatorio');
        $dados['dataIni']   = $this->request->getPost('dataIni');
        $dados['dataFim']   = $this->request->getPost('dataFim');
        $dados['secao']     = $this->request->getPost('secao');
        $dados['funcao']    = $this->request->getPost('funcao');
        $dados['keyword']   = $this->request->getPost('keyword');
        $dados['colunas_filtro']   = $this->request->getPost('colunas_filtro');
        $dados['chapa']   = $this->request->getPost('chapa');

        if($dados['relatorio'] != null && $dados['colunas'] != null){
            $response               = $this->mRel->gerarRelatorio($dados);
            $dados['resDados']      = ($response) ? $response['dados'] : false;
            $dados['qtdeColunas']   = ($response) ? $response['colunas'] : false;
        }else{
            $dados['resDados']      = false;
            $dados['qtdeColunas']   = false;
        }

        return parent::ViewPortal('relatorio/index', $dados);

    }

    public function pdf(){

        ini_set("pcre.backtrack_limit", "50000000");

        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['colunas']   = $this->request->getPost('colunas');
        $dados['relatorio'] = $this->request->getPost('relatorio');
        $dados['dataIni']   = $this->request->getPost('dataIni');
        $dados['dataFim']   = $this->request->getPost('dataFim');
        $dados['secao']     = $this->request->getPost('secao');
        $dados['funcao']    = $this->request->getPost('funcao');
        $dados['keyword']   = $this->request->getPost('keyword');
        $dados['colunas_filtro']   = $this->request->getPost('colunas_filtro');
        $dados['chapa']   = $this->request->getPost('chapa');


        $response = $this->mRel->gerarRelatorio($dados);


        if($response){

            
            $resDados       = $response['dados'];
            $qtdeColunas    = $response['colunas'];
            $colunas        = $_POST['colunas'];
            
            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4-L',
                'margin_bottom' => 9,
                'margin_top' => 9,
            ]);
            
            $tabela = '

                <style>
                table, th, td{
                    border: 1px solid black;
                    border-collapse: collapse;
                    text-align: center;
                }
                .tableheader{
                    margin-bottom: 50px;
                    background-color: #006f49;
                    color: #FFFFFF;

                }

                .tablebody{
                    background-color: #ffffff;
                
                }
                </style>
            
                <table> 
                <thead>    
                <tr >';

          
                foreach($colunas as $Select => $value){
                    $tabela .= '<th class="tableheader" height="50">'.$colunas[$Select].'</th>';
                }

            $tabela .= '</tr> </thead> <tbody>';

               

                foreach ($resDados as $idx => $Dados) {
                    $tabela .= '<tr>';
                
                    // Para cada coluna em $colunas, acessa o valor correspondente em $resDados
                    foreach ($colunas as $colunaNome) {
                        $valorCelula = isset($Dados[$colunaNome]) ? $Dados[$colunaNome] : ''; // Usa valor vazio se não existir
                        $tabela .= '<td class="tablebody">' . $valorCelula . '</td>';
                    }
                
                    $tabela .= '</tr>';
                }
                

            $tabela .= '</tbody></table>';

            
            $mpdf->WriteHTML($tabela);

            /*
            * F - salva o arquivo NO SERVIDOR
            * I - abre no navegador E NÃO SALVA
            * D - chama o prompt E SALVA NO CLIENTE
            */
            
            $mpdf->Output("Relatório.pdf", "I");
            exit();
        }
    }

    public function excel()
    {

        
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['colunas']   = $this->request->getPost('colunas');
        $dados['relatorio'] = $this->request->getPost('relatorio');
        $dados['dataIni']   = $this->request->getPost('dataIni');
        $dados['dataFim']   = $this->request->getPost('dataFim');
        $dados['secao']     = $this->request->getPost('secao');
        $dados['funcao']    = $this->request->getPost('funcao');
        $dados['keyword']   = $this->request->getPost('keyword');
        $dados['colunas_filtro']   = $this->request->getPost('colunas_filtro');

        $dados['chapa']   = $this->request->getPost('chapa');
        $response = $this->mRel->gerarRelatorio($dados);

        if($response){

            $resDados       = $response['dados'];
            $qtdeColunas    = $response['colunas'];
            $colunas        = $_POST['colunas'];

            $template_ativo = true;
            if(count($resDados) > 10000) $template_ativo = false;

            $qtdLinhas = count($resDados)+1;
            $letra_coluna = indexToExcelColumn($qtdeColunas);

            $spreadsheet = new Spreadsheet();

            // cor do texto
            $styleArray = array(
                'font'  => array(
                    'bold'  => true,
                    'color' => array('rgb' => 'FFFFFF')
                )
            );
            $styleBordaDados = array(
                'borders' => array(
                    'outline' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '5B9BD5'),
                    ),
                ),
            );
            if($template_ativo){
            
                // $spreadsheet->getActiveSheet()->getStyle('A1:'.$letra_coluna.'1')->applyFromArray($styleArray);
                // $spreadsheet->getActiveSheet()->getStyle('A2:'.$letra_coluna.$qtdLinhas)->applyFromArray($styleBordaDados);
                
    
                // cor do background
                // $spreadsheet
                // ->getActiveSheet()
                // ->getStyle('A1:'.$letra_coluna.'1')
                // ->getFill()
                // ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                // ->getStartColor()
                // ->setARGB('5B9BD5');

                // $spreadsheet
                // ->getActiveSheet()
                // ->getStyle('A2:'.$letra_coluna.$qtdLinhas)
                // ->getFill()
                // ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                // ->getStartColor()
                // ->setARGB('ddebf7');
                 
    
                // nome da aba da planilha
                $spreadsheet->getActiveSheet()->setTitle('Relatório');
                // $spreadsheet->getActiveSheet()->setAutoFilter('A1:'.$letra_coluna.'1'); // auto filtro no titulo
                
   
            }

          
            // titulo das colunas
            $sheet = $spreadsheet->getActiveSheet();
            $i=1;
            foreach ($colunas as $colunaNome) {
                $sheet->setCellValue(indexToExcelColumn($i) . '1', str_replace(['[', ']'], '', $colunaNome));
                $i++;
            }
            
            // Preenche os dados de acordo com as colunas especificadas em $colunas
            foreach ($resDados as $idx => $Dados) {
                $linha = $idx + 2; // Ajusta para pular a linha 1 (título)
            
                $valorColuna = 1;
                foreach ($colunas as $colunaNome) {
                    $valorCelula = isset($Dados[str_replace(['[', ']'], '', $colunaNome)]) ? $Dados[str_replace(['[', ']'], '', $colunaNome)] : ''; // Deixa em branco se o valor não existir
                    $sheet->setCellValue(indexToExcelColumn($valorColuna) . $linha, $valorCelula);
                    $valorColuna++;
                }
            }


            $writer = new Xlsx($spreadsheet);

            header('Content-Disposition: attachment; filename=Relatório.xlsx' );
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');

            $writer->save("php://output");

            exit();

            $rows = 2;

        }

    }



    public function action($act)
    {

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            case 'lista_funcionarios':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit(json_encode($this->mRel->listaFuncionario($dados)));
                break;
        }
    }

}