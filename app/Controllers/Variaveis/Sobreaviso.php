<?php

namespace App\Controllers\Variaveis;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Controllers\BaseController;
use Ramsey\Uuid\Uuid;
use \Mpdf\Mpdf;

Class Sobreaviso extends BaseController {

    private $mParam;
  
    public function __construct()
    {
        
        parent::__construct('Variáveis');
        $this->_moduloName = '<i class="mdi mdi-account-star-outline"></i> Variáveis';
        $this->mParam = model('Variaveis/VariaveisModel');

    }

    public function index()
    {
      
       
        $dados['funcionario']  = false;
        parent::VerificaPerfil('VARIAVEIS_SOBREAVISO');
        $dados['_titulo'] = "Sobreaviso";
        $this->_breadcrumb->add($dados['_titulo'], '');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['data_inicio']         = $this->request->getPost('data_inicio');
        $dados['data_fim']            = $this->request->getPost('data_fim');
        $dados['secao']            = $this->request->getPost('secao');
        $dados['funcionario']         = $this->request->getPost('funcionario');
        $dados['situacao']            = $this->request->getPost('situacao');

        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
        $dados['listaEventos']  = $this->mParam->listaEventos();
      
        $dados['listaReqs']     = $this->mParam->getReq(3, $dados['data_inicio'],$dados['data_fim'],$dados['funcionario'],false, $dados['situacao'] );
       
        $dados['listaSecao']    = $this->mParam->listaSecaoGestor( $dados['rh']);
       
        
        return parent::ViewPortal('variaveis/sobreaviso/index', $dados);

    }

    public function novo(){

        parent::VerificaPerfil('VARIAVEIS_SOBREAVISO');

        $mesanterior = date('Y-m', strtotime('-1 month'));
        $mesatual = date('Y-m');
        $mesfuturo = date('Y-m', strtotime('+1 month'));
        $param3 = $this->mParam->getParametros(3);

        $dados['per_ini_atual'] = $mesanterior.'-'.$param3->dia_ponto_ini;
        $dados['per_fim_atual'] = $mesatual.'-'.$param3->dia_ponto_fim;
        $dados['per_ini_futuro'] = $mesatual.'-'.$param3->dia_ponto_ini;
        $dados['per_fim_futuro'] = $mesfuturo.'-'.$param3->dia_ponto_fim;

        $dados['_titulo'] = "Novo Sobreaviso";
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
        $dados['chapaFunc'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dados['funcionario'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

        $dados['param6']   = json_encode($this->mParam->getParametros(3));
        
        $this->_breadcrumb->add('Novo Sobreaviso');

        return parent::ViewPortal('variaveis/sobreaviso/novo', $dados);
    }

    public function editar($id){
        $id = base64_decode($id);
     
        $mesanterior = date('Y-m', strtotime('-1 month'));
        $mesatual = date('Y-m');
        $mesfuturo = date('Y-m', strtotime('+1 month'));
        $param3 = $this->mParam->getParametros(3);

        $dados['per_ini_atual'] = $mesanterior.'-'.$param3->dia_ponto_ini;
        $dados['per_fim_atual'] = $mesatual.'-'.$param3->dia_ponto_fim;
        $dados['per_ini_futuro'] = $mesatual.'-'.$param3->dia_ponto_ini;
        $dados['per_fim_futuro'] = $mesfuturo.'-'.$param3->dia_ponto_fim;
        
        $dados['_titulo'] = "Editar Sobreaviso";
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
    
        $dados['req'] = $this->mParam->getReqDados($id);
        $dados['chapaFunc'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dados['param6'] = json_encode($this->mParam->getParametros(3));
        $dados['valores'] = json_decode($dados['req'][0]->valores) ;

        $horarios = json_decode(isset($dados['valores']->horarios) ? $dados['valores']->horarios : '[]');
        $mes1 = '';
        $mes2 = '';
        foreach ($horarios as $key2 => $dados2) :
            if(substr($dados2->data_inicio,-2) > '15') {
              $mes1 = date('Y-m', strtotime($dados2->data_inicio));
              $mes2 = date('Y-m', strtotime('+1 month', strtotime($dados2->data_inicio)));
            } else {
              $mes1 = date('Y-m', strtotime('-1 month', strtotime($dados2->data_inicio)));
              $mes2 = date('Y-m', strtotime($dados2->data_inicio));
            }
            break;
        endforeach;
        if($mes1<>'') {
          $dados['per_ini_atual'] = $mes1.'-'.$param3->dia_ponto_ini;
          $dados['per_fim_atual'] = $mes2.'-'.$param3->dia_ponto_fim;
          $dados['per_ini_futuro'] ='';
          $dados['per_fim_futuro'] = '';
        }
    
        $this->_breadcrumb->add('Editar Sobreaviso');

        return parent::ViewPortal('variaveis/sobreaviso/update', $dados);
    }
    public function update(){
      
        $request    = $this->request->getPost();
        $result     = $this->mParam->saveUpdateRequisicao($request);

        $files = $this->request->getFiles(); // Pega todos os arquivos

        
        if($result){
            if($files){
                foreach ($files['anexo'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $result2 = $this->mParam->saveAnexo($request['id'], $file);
                    }
                }
            

            }
            exit(responseJson('success', 'Requisição atualizada com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao salvar requisição.'));
    }
    public function substituicaoTermo()
    {
        $id = $this->request->getVar('id'); // Pega o valor de 'id' no GET ou POST

        $meses = [

            'January' => 'janeiro',
            'February' => 'fevereiro',
            'March' => 'março',
            'April' => 'abril',
            'May' => 'maio',
            'June' => 'junho',
            'July' => 'julho',
            'August' => 'agosto',
            'September' => 'setembro',
            'October' => 'outubro',
            'November' => 'novembro',
            'December' => 'dezembro'
        ];

        $mesAtual = date('F');

        $dados['mes'] = $meses[$mesAtual];
        $dados['empresa']    = $this->mParam->DadosEmpresa();
        $dados['req']     = $this->mParam->getReqDados($id);
        $dados['valores']     = json_decode($dados['req'][0]->valores) ;
      
        $dados['func']    = $this->mParam->dadosFunc($dados['valores']->funcionario);
        $dados['filial']    = $this->mParam->DadosFilial(   $dados['func'][0]['CODFILIAL'] );
     
        $html = view('variaveis/sobreaviso/termo',  $dados);
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P'
        ]);

      
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S'); // 'S' faz o Output para string (não download ou inline)

         // Retornar o PDF como resposta em binário
         return $this->response
         ->setHeader('Content-Type', 'application/pdf')
         ->setHeader('Content-Disposition', 'attachment; filename="termo_de_sobreaviso.pdf"')
         ->setBody($pdfContent)
         ->send(); // Use send() para garantir o envio da resposta binária
    }

    public function save()
    {
        $result = false;
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $request    = $this->request->getPost();
        $files = $this->request->getFiles(); // Pega todos os arquivos
      
        $result     = $this->mParam->saveRequisicao($request,$dados['rh']);
       
      
        if($result){

            if($result === 'achou'){
                exit(responseJson('error', 'O colaborador já possui uma requisição desse tipo ativa nesse período.'));
            }else{
                if($files){
                    foreach ($files['anexo'] as $file) {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $result2 = $this->mParam->saveAnexo($result, $file);
                        }
                    }
                

                }
                
                
            }
            exit(responseJson('success', 'Requisição salva com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao salvar requisição.'));

    }
    public function Anexos()
    {
        try {
            $request = $this->request->getPost();
            $Anexos = $this->mParam->getAnexos($request['id']);
            // Converte o array em JSON
            return $this->response->setJSON($Anexos);

        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON vazia
            return $this->response->setJSON([]);
        }
    }
    public function salvarAnexo()
    {
        try {
            $request = $this->request->getPost();
            $files = $this->request->getFiles(); // Pega todos os arquivos

            if($files){
                foreach ($files['anexo'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $result2 = $this->mParam->saveAnexo($request['id'], $file);
                    }
                }
            

            }
            
          
            // Converte o array em JSON
            exit(responseJson('success', 'Anexos salvos com sucesso.'));

        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON vazia
            exit(responseJson('error', 'Falha ao salvar Anexos.'));

        }
    }
    public function delete()
    {

        $request    = $this->request->getPost();
       
        $result     = $this->mParam->DeleteReq($request['id']);
     
        if($result){
            exit(responseJson('success', 'Requisição excluída com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao excluir requisição.'));

    }
    public function deleteAnexo()
    {

        $request    = $this->request->getPost();
       
        $result     = $this->mParam->DeleteReqAnexo($request['id']);
     
        if($result){
            exit(responseJson('success', 'Anexo excluído com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao excluir Anexo.'));

    }


    public function dadosFunc()
    {

        $request    = $this->request->getPost();
        $result     = $this->mParam->dadosFunc($request['chapa']);
       

       
        if($result){
            echo json_encode($result);
            exit;
        }
        
        exit(responseJson('error', 'Falha ao salvar parâmetros.'));

    }

    public function selectFunc()
    {

        $request    = $this->request->getPost();
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $result = $this->mParam->ListarFuncionariosSecao($request['codigo'], $dados);

       
        if($result){
            echo json_encode($result);
            exit;
        }
        
        exit(responseJson('error', 'Nenhum funcionário localizado para essa seção.'));

    }

  // ------------------------------------------------------------------
  // exportar horarios para excel
  // ------------------------------------------------------------------
  public function exportar($dados)
  {
    
    $horarios = json_decode(urldecode($dados));
    /*print_r($horarios);
    $valores = json_decode($horarios);
    print_r($valores);
    exit();
    die();*/

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
    $spreadsheet->getActiveSheet()->setTitle('Planilha de Horas de Sobreaviso');
    $spreadsheet->getActiveSheet()->setAutoFilter('A1:E1'); // auto filtro no titulo

    // titulo das colunas
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'DATA_INICIAL');
    $sheet->setCellValue('B1', 'HORA_INICIAL');
    $sheet->setCellValue('C1', 'DATA_FINAL');
    $sheet->setCellValue('D1', 'HORA_FINAL');
    $sheet->setCellValue('E1', 'TOTAL_DE_HORAS');

    $rows = 2;

    //$horarios = json_decode(isset($valores->horarios) ? $valores->horarios : '[]');
    foreach ($horarios as $key2 => $dados2) :
        $sheet->setCellValue('A' . $rows, dtBr($dados2->data_inicio));
        $sheet->setCellValue('B' . $rows, $dados2->hora_inicio);
        $sheet->setCellValue('C' . $rows, dtBr($dados2->data_fim));
        $sheet->setCellValue('D' . $rows, $dados2->hora_fim);
        $sheet->setCellValue('E' . $rows, $dados2->tot_horas);

        $spreadsheet->getActiveSheet()->getStyle('A' . $rows . ':E' . $rows)->applyFromArray($styleBorda);
        $rows++;
    endforeach; 

    for ($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
      $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    $writer = new Xlsx($spreadsheet);

    header('Content-Disposition: attachment; filename=Planilha de Horas de Sobreaviso.xlsx');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $writer->save("php://output");

    exit();
  }


}