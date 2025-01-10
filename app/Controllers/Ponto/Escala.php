<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;
use App\Libraries\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $dados['_titulo'] = "Histórico de solicitação";

        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

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
        $dados['resEscala'] = $this->mEscala->ListarEscala(false, $dados);
        
        return parent::ViewPortal('ponto/escala/index', $dados);

    }

    public function excel()
    {

        parent::VerificaPerfil('PONTO_TROCADEESCALA');
        $dados['_titulo'] = "Histórico de solicitação";

        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

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
        $resEscala = $this->mEscala->ListarEscala(false, $dados);

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

        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle('A1:G1')->applyFromArray($styleBorda);

        $spreadsheet
        ->getActiveSheet()
        ->getStyle('A1:G1')
        ->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('006f49');

        // nome da aba da planilha
        $spreadsheet->getActiveSheet()->setTitle('Aprovação de Ponto');
        $spreadsheet->getActiveSheet()->setAutoFilter('A1:G1'); // auto filtro no titulo

        // titulo das colunas
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Status');
        $sheet->setCellValue('C1', 'Tipo de troca');
        $sheet->setCellValue('D1', 'Colaborador');
        $sheet->setCellValue('E1', 'Data de Solicitação');
        $sheet->setCellValue('F1', 'Usuário Solicitante');
        $sheet->setCellValue('G1', 'Anexo');

        $rows = 2;

        if($resEscala){
            foreach($resEscala as $Escala){

                $sheet->setCellValue('A' . $rows, $Escala['id']);

                $status = '';
                switch($Escala['situacao']){
                    case 0: $status = 'Criada'; break;
                    case 10: $status = 'Pend/Ação Gestor'; break;
                    case 1: $status = 'Pend/Upload Documento'; break;
                    case 4: $status = 'Termo Anexado'; break;
                    case 2: $status = 'Pend/Ação RH'; break;
                    case 3: $status = 'Concluído'; break;
                    case 8:
                    case 9: 
                        $status = 'Reprovado'; break;
                    // case 9: echo '<span class="badge badge-danger">Cancelado</span>'; break;
                    default: $status = '';
                }

                $sheet->setCellValue('B' . $rows, $status);
                $sheet->setCellValue('C' . $rows, ($Escala['tipo'] == 1) ? 'Troca de escala' : 'Troca de dia');
                $sheet->setCellValue('D' . $rows, $Escala['chapa'].' - '.$Escala['nome']);
                $sheet->setCellValue('E' . $rows, date('d/m/Y', strtotime($Escala['dtcad'])));
                $sheet->setCellValue('F' . $rows, $Escala['chapa_solicitante'].' - '.$Escala['solicitante']);
                $sheet->setCellValue('G' . $rows, ($Escala['usuupload']) ? 'Sim' : 'Não');

                $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->applyFromArray($styleBorda);
                $rows++;

            }
        }

        for($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Disposition: attachment; filename=Troca de Escala.xlsx' );
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $writer->save("php://output");

        exit();

    }

    // ------------------------------------------------------------------
    // nova escala
    // ------------------------------------------------------------------
    public function novo(){

        parent::VerificaPerfil('PONTO_TROCADEESCALA');
        $dados['_titulo'] = "Nova Troca de Escala";
        $this->_breadcrumb->add('Nova Troca de Escala', 'ponto/escala/nova');

        $perfilRH = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['resFuncionarios'] = $this->mEscala->ListarEscalaFuncionarios($perfilRH);
        $dados['resConfiguracao'] = $this->mEscala->Configuracao();
        $chapa = $this->request->getPost('chapa');
        $dados['chapa'] = $chapa;
        if($chapa){
            
            $dados['resHorario']    = $this->mEscala->ListarEscalaHorario($chapa, $dados['resFuncionarios']);

        }
        
        return parent::ViewPortal('ponto/escala/novo', $dados);
        
    }

    // ------------------------------------------------------------------
    // nova escala dia
    // ------------------------------------------------------------------
    public function novodia(){

        parent::VerificaPerfil('PONTO_TROCADEESCALA');
        $dados['_titulo'] = "Nova Troca de Dia";
        $this->_breadcrumb->add('Nova Troca de Dia', 'ponto/escala/nova');

        $perfilRH = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['resFuncionarios'] = $this->mEscala->ListarEscalaFuncionarios($perfilRH);
        $dados['resConfiguracao'] = $this->mEscala->Configuracao();
        $chapa = $this->request->getPost('chapa');
        $dados['chapa'] = $chapa;
        if($chapa){
            
            $dados['resHorario']    = $this->mEscala->ListarEscalaHorario($chapa, $dados['resFuncionarios']);
            $dados['resProjecao']   = $this->mEscala->projecaoEscalaChapa($chapa);

        }
        
        return parent::ViewPortal('ponto/dia/novo', $dados);
        
    }

    // ------------------------------------------------------------------
    // editar escala
    // ------------------------------------------------------------------
    public function editar($id, $situacao = false){

        parent::VerificaPerfil('PONTO_TROCADEESCALA');

        // valida integridade de dados
        $id = cid($id, disabled: true);
        $situacao = (!$situacao) ? 0 : cid($situacao, disabled: true);

        $dados['_titulo'] = "Visualizar Solicitação | <span class=\"badge badge-info\">Nº {$id}</span>";
        
        $this->_breadcrumb->add('Visualizar Solicitação');

        // verifica se o registro existe
        $filtro['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $filtro['filtro'] = $situacao;
        $resEscala = $this->mEscala->ListarEscala($id, $filtro);
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
        $dados['resIndice']           = $this->mEscala->ListarEscalaHorarioIndice($resEscala[0]['codhorario']);
        $dados['resConfiguracao']     = [
            'escala_per_inicio' => $resEscala[0]['config_escala_per_ini'],
            'escala_per_fim' => $resEscala[0]['config_escala_per_fim'],
            'dia_per_inicio' => $resEscala[0]['config_dia_per_ini'],
            'dia_per_fim' => $resEscala[0]['config_dia_per_fim'],
            'bloqueio_aviso' => $resEscala[0]['bloqueio_aviso'],
        ];

        $dados['resEscala'] = $resEscala[0];
        $dados['id'] = $id;

        switch($resEscala[0]['situacao']){
            case 0  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-warning">Criada</span>'; break;
            case 10 : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-warning">Pend/Ação Gestor</span>'; break;
            case 1  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-success">Pend/Upload Documento</span>'; break;
            case 4  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-success">Termo anexado</span>'; break;
            case 2  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-info">Pend/Ação RH</span>'; break;
            case 3  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-primary">Concluído</span>'; break;
            case 8  :
            case 9  : 
                      $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-danger">Reprovado</span>'; break;
            // case 9  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-danger">Cancelado</span>'; break;
            default : $dados['_titulo'] = $dados['_titulo'];
        }

        if($resEscala[0]['situacao'] == 9) notificacao('danger', 'Requisição cancelada em <b>'.dtBr($resEscala[0]['dtcancelado']).(strlen(trim($resEscala[0]['motivocancelado'])) > 0 ? "<br>Motivo: ".$resEscala[0]['motivocancelado'] : "" ).'</b>');
        if($resEscala[0]['situacao'] == 8) notificacao('danger', 'Requisição reprovada em <b>'.dtBr($resEscala[0]['dtcancelado']).(strlen(trim($resEscala[0]['motivocancelado'])) > 0 ? "<br>Motivo: ".$resEscala[0]['motivocancelado'] : "" ).'</b>');
        
        return parent::ViewPortal('ponto/escala/editar', $dados);
    }

    // ------------------------------------------------------------------
    // editar escala
    // ------------------------------------------------------------------
    public function editardia($id, $situacao = false){

        parent::VerificaPerfil('PONTO_TROCADEESCALA');

        // valida integridade de dados
        $id = cid($id, disabled: true);
        $situacao = (!$situacao) ? 0 : cid($situacao, disabled: true);

        $dados['_titulo'] = "Visualizar Solicitação | <span class=\"badge badge-info\">Nº {$id}</span>";
        
        $this->_breadcrumb->add('Visualizar Solicitação');

        // verifica se o registro existe
        $filtro['filtro'] = $situacao;
        $filtro['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $resEscala = $this->mEscala->ListarEscala($id, $filtro);
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

        $dados['DadosFuncionario']    = $DadosFuncionario[0];
        $dados['resHorario']          = $this->mEscala->ListarEscalaHorario($resEscala[0]['chapa'], $DadosFuncionario);
        $dados['resIndice']   = $this->mEscala->ListarEscalaHorarioIndice($resEscala[0]['codhorario']);
        $dados['resConfiguracao']     = [
            'escala_per_inicio' => $resEscala[0]['config_escala_per_ini'],
            'escala_per_fim' => $resEscala[0]['config_escala_per_fim'],
            'dia_per_inicio' => $resEscala[0]['config_dia_per_ini'],
            'dia_per_fim' => $resEscala[0]['config_dia_per_fim'],
            'bloqueio_aviso' => $resEscala[0]['bloqueio_aviso'],
        ];

        $dados['resEscala'] = $resEscala[0];
        $dados['id'] = $id;

        switch($resEscala[0]['situacao']){
            case 0  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-warning">Criada</span>'; break;
            case 10 : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-warning">Pend/Ação Gestor</span>'; break;
            case 1  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-success">Pend/Upload Documento</span>'; break;
            case 4  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-success">Termo anexado</span>'; break;
            case 2  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-info">Pend/Ação RH</span>'; break;
            case 3  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-primary">Concluído</span>'; break;
            case 8  :
            case 9  : 
                      $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-danger">Reprovado</span>'; break;
            // case 9  : $dados['_titulo'] = $dados['_titulo']. ' | <span class="badge badge-danger">Cancelado</span>'; break;
            default : $dados['_titulo'] = $dados['_titulo'];
        }

        $dados['resProjecao']   = $this->mEscala->projecaoEscalaChapa($resEscala[0]['chapa']);

        if($resEscala[0]['situacao'] == 9) notificacao('danger', 'Requisição cancelada em <b>'.dtBr($resEscala[0]['dtcancelado']).(strlen(trim($resEscala[0]['motivocancelado'])) > 0 ? "<br>Motivo: ".$resEscala[0]['motivocancelado'] : "" ).'</b>');
        if($resEscala[0]['situacao'] == 8) notificacao('danger', 'Requisição reprovada em <b>'.dtBr($resEscala[0]['dtcancelado']).(strlen(trim($resEscala[0]['motivocancelado'])) > 0 ? "<br>Motivo: ".$resEscala[0]['motivocancelado'] : "" ).'</b>');
        
        return parent::ViewPortal('ponto/dia/editar', $dados);
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
        $filtro['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $resEscala = $this->mEscala->ListarEscala($id, $filtro);
        if(!$resEscala){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Escala não localizada.')));
            return redirect()->to(base_url('ponto/escala'));
        }

        // verifica status da solicitação
        if($resEscala[0]['situacao'] != 0){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Solicitação inválida.')));
            return redirect()->to(base_url('ponto/escala'));
        }


        $dados['chapa'] = $resEscala[0]['chapa'];
        $dados['codhorario'] = $resEscala[0]['codhorario'];
        $dados['datamudanca'] = $resEscala[0]['datamudanca'];
        $dados['resDadosTermo'] = $this->mEscala->ListarEscalaTermoAditivo($dados);
        $dados['resEscala'] = $resEscala[0];

        $mpdf = new Mpdf();

        $mpdf->SetDefaultBodyCSS('text-align', 'justify');

        // echo '<pre>';

        // print_r($dados);
        // exit();


        $view = ($resEscala[0]['tipo'] == 1) ? 'ponto/escala/pdf_termo_aditivo' : 'ponto/escala/pdf_termo_aditivo_dia';
        $WriteHTML = view($view, $dados, ['saveData' => false]);

        // $WriteHTML = '
        //     <p>
        //         Este texto será justificado mesmo que o CSS não funcione diretamente. Este método garante que o mPDF aplique o alinhamento. Este texto será justificado mesmo que o CSS não funcione diretamente. Este método garante que o mPDF aplique o alinhamento. Este texto será justificado mesmo que o CSS não funcione diretamente. Este método garante que o mPDF aplique o alinhamento. Este texto será justificado mesmo que o CSS não funcione diretamente. Este método garante que o mPDF aplique o alinhamento. Este texto será justificado mesmo que o CSS não funcione diretamente. Este método garante que o mPDF aplique o alinhamento.
        //     </p>
        // ';
        
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
        $filtro['filtro'] = $situacao;
        $filtro['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $resEscala = $this->mEscala->ListarEscala($id, $filtro);
        if(!$resEscala){
            notificacao('danger', 'Escala não localizada.');
            return redirect()->to(base_url('ponto/escala'));
        }

        // verifica situacao
        // if($resEscala[0]['situacao'] < 2){
        //     notificacao('danger', 'Escala não localizada.');
        //     return redirect()->to(base_url('ponto/escala'));
        // }

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
            case 'horario_indice':
                
                $resIndice = $this->mEscala->ListarEscalaHorarioIndice($dados['codhorario']);

                return json_encode($resIndice);
                break;
            case 'carrega_escala':

                $resEscala = $this->mEscala->CalculaTrocaDeEscala($dados);

                return $resEscala;
                break;
            case 'carrega_escala_dia':

                $resEscala = $this->mEscala->CalculaTrocaDeDia($dados);

                return $resEscala;
                break;
            case 'cadastrar':
                exit($this->mEscala->CadastrarEscala($dados));
                break;
            case 'verifica_data':
                exit($this->mEscala->VerificaData($dados));
                break;
            case 'aprovar':
                exit($this->mEscala->AprovarEscala($dados));
                break;
            case 'reprovar':
                exit($this->mEscala->ReprovarEscala($dados));
                break;
            case 'upload_termo_aditivo':
                $dados['documento'] = $_FILES;
                
                exit($this->mEscala->UploadTermoAditivo($dados));
                break;
            case 'configuracao':
                parent::VerificaPerfil('PONTO_TROCADEESCALA_CONFIGURACAO');
                $dados['documento'] = $_FILES;
                
                exit($this->mEscala->CadastrarConfiguracao($dados));
                break;
            case 'envia_aprovacao':
                exit($this->mEscala->EnviaParaAprovacao($dados));
                break;
            case 'excluir':
                exit($this->mEscala->ExcluirEscala($dados));
                break;
            case 'justificativa':
                $filtro['rh'] = true;
                $escala = $this->mEscala->ListarEscala(cid($dados['id'], disabled: true), $filtro);
                if($escala[0]['justificativa_11_horas'] != ''){
                    echo '<b>Justificativa (Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário):</b><br>';
                    echo '<div style="padding: 8px; background: #f0f0f0; border-radius:6px;">'.nl2br($escala[0]['justificativa_11_horas']).'</div>';
                    echo '<hr>';
                }
                if($escala[0]['justificativa_6_dias'] != ''){
                    echo '<b>Justificativa (Colaborador possui mais de 6 dias trabalhados consecutivos):</b><br>';
                    echo '<div style="padding: 8px; background: #f0f0f0; border-radius:6px;">'.nl2br($escala[0]['justificativa_6_dias']).'</div>';
                    echo '<hr>';
                }
                if($escala[0]['justificativa_6_meses'] != ''){
                    echo '<b>Justificativa (Troca de escala inferior a 6 meses do horário atual do colaborador):</b><br>';
                    echo '<div style="padding: 8px; background: #f0f0f0; border-radius:6px;">'.nl2br($escala[0]['justificativa_6_meses']).'</div>';
                    echo '<hr>';
                }
                if($escala[0]['justificativa_periodo'] != ''){
                    echo '<b>Justificativa (Fora do período permitido):</b><br>';
                    echo '<div style="padding: 8px; background: #f0f0f0; border-radius:6px;">'.nl2br($escala[0]['justificativa_periodo']).'</div>';
                    echo '<hr>';
                }
                break;

                
        }

        

    }

}