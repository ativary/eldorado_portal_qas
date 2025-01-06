<?php

namespace App\Controllers\Premio;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controllers\BaseController;

class Requisicao extends BaseController
{

  private $mRequisicao;

  public function __construct()
  {
    parent::__construct('Premio'); // sempre manter

    $this->_moduloName = '<i class="fas fa-trophy"></i> Prêmios';
    $this->mRequisicao = model('Premio/RequisicaoModel');
  }

  // ------------------------------------------------------------------
  // Requisicao de Colaborador
  // ------------------------------------------------------------------ 
  public function index()
  {
    parent::VerificaPerfil('PREMIO_REQUISICAO');

    if($_SESSION['func_coligada'] == ""){
      return redirect()->to(base_url('portal/coligada'));
    }
 
    //$dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

    $dados['_titulo'] = "Requisição de Prêmio";
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $this->_breadcrumb->add($dados['_titulo'], 'premio/requisicao');
    $dados['resRequisicao'] = $this->mRequisicao->ListarRequisicao();
    
    // Analisar se o padrão será o mesmo que cedências
    //$dados['resAprovar'] = $this->mRequisicao->ListarRequisicaoAprovar();
    
    return parent::ViewPortal('premio/requisicao/index', $dados);

  }

  // ------------------------------------------------------------------
  // Nova Requisicao 
  // ------------------------------------------------------------------
  public function novo()
  {
    parent::VerificaPerfil('PREMIO_REQUISICAO');

    $dados['_titulo'] = "Nova Requisição de Prêmio";
    $this->_breadcrumb->add('Nova Requisição de Prêmio');
    $dados['resFunc'] = $this->mRequisicao->ListarFunc();
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $dados['func_chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    $dados['func_nome'] = $_SESSION['log_nome'];
    $dados['resAcessos'] = $this->mRequisicao->ListarAcessosPremios();
    
    return parent::ViewPortal('premio/requisicao/novo', $dados);
  }

  // ------------------------------------------------------------------
  // Editar Requisicao
  // ------------------------------------------------------------------
  public function editar($id)
  {
    parent::VerificaPerfil('PREMIO_REQUISICAO');

    $this->_breadcrumb->add('Editar Requisição');
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $dados['func_chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    $dados['func_nome'] = $_SESSION['log_nome'];
    $dados['id_requisicao'] = $id;
    $dados['id'] = $id;
    $dados['resRequisicao'] = $this->mRequisicao->ListarRequisicao($id);
    $dados['resGestorNaoAprovou'] = $this->mRequisicao->ListarGestorNaoAprovou($id);
    $dados['resReqChapas'] = $this->mRequisicao->ListarChapasRequisicao($id);
    $status = $dados['resRequisicao'][0]['status'];
    $dados['em_analise'] = !($status == 'P' or $status == 'R') ? " disabled " : "";
    $dados['pode_editar'] = !($status == 'P' or $status == 'R') ? false : true;
    $dados['_titulo'] = !($status == 'P' or $status == 'R') ? "Requisição enviada para Aprovação (Edição bloqueada)" : "Editar Requisição";

    if($dados['resRequisicao'] <> []) {
      if($dados['resRequisicao'][0]['chapa_requisitor'] != $dados['func_chapa'] && !$dados['isAdmin']){
        notificacao('danger', 'Sem permissão para acessar esta página');
        redireciona('premio/requisicao');
      } 
    }
    
    return parent::ViewPortal('premio/requisicao/editar', $dados);
  }

  // ------------------------------------------------------------------
  // Ver aprovação de Requisicao
  // ------------------------------------------------------------------
  public function ver_aprova_req($id, $chapa_coordenador)
  {
    parent::VerificaPerfil('PREMIO_APROVA_REQUISICAO');

    $this->_breadcrumb->add('Detalhes da Requisição a Aprovar');
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $dados['func_chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    $dados['func_nome'] = $_SESSION['log_nome'];
    $dados['id_requisicao'] = $id;
    $dados['id'] = $id;
    $dados['resRequisicao'] = $this->mRequisicao->ListarRequisicao($id);
    $dados['resReqChapas'] = $this->mRequisicao->ListarChapasRequisicao($id, $chapa_coordenador);
    $dados['_titulo'] = "Detalhes da Requisição a Aprovar";
        
    return parent::ViewPortal('premio/requisicao/ver_aprova_req', $dados);
  }

  
  // ------------------------------------------------------------------
  // Editar Chapa da Requisicao
  // ------------------------------------------------------------------
  public function editar_chapa($id_req_chapa, $id_requisicao)
  {
    parent::VerificaPerfil('PREMIO_REQUISICAO');

    $this->_breadcrumb->add('Editar Chapa da Requisição');
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $dados['func_chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    $dados['func_nome'] = $_SESSION['log_nome'];
    $dados['id_req_chapa'] = $id_req_chapa;
    $dados['id_requisicao'] = $id_requisicao;
    $dados['resRequisicao'] = $this->mRequisicao->ListarRequisicao($id_requisicao);
    $dados['resReqChapa'] = $this->mRequisicao->ListarChapaRequisicao($id_req_chapa);
    $dados['resCoordenadores'] = $this->mRequisicao->ListarGerenteCoordenadores($id_requisicao);
    $dados['resTargets'] = $this->mRequisicao->ListarTargetsPremissas($dados['resRequisicao'][0]['chapa_gerente'],$_SESSION['func_coligada']);
    $dados['em_analise'] = $dados['resRequisicao'][0]['status'] == 'E' ? " disabled " : "";
    $dados['_titulo'] = $dados['resRequisicao'][0]['status'] == 'E' ? "Requisição enviada para Aprovação (Edição bloqueada)" : "Editar Chapa da Requisição";

    if($dados['resRequisicao'] <> []) {
      if($dados['resRequisicao'][0]['chapa_requisitor'] != $dados['func_chapa'] && !$dados['isAdmin']){
        notificacao('danger', 'Sem permissão para acessar esta página');
        redireciona('premio/requisicao');
      } 
    }
    
    return parent::ViewPortal('premio/requisicao/editar_chapa', $dados);
  }

  // ------------------------------------------------------------------
  // Editar Chapa da Requisicao
  // ------------------------------------------------------------------
  public function nova_chapa($id_requisicao)
  {
    parent::VerificaPerfil('PREMIO_REQUISICAO');

    $dados['_titulo'] = "Novo Colaborador na Requisição";
    $this->_breadcrumb->add('Novo Colaborador na Requisição');
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $dados['func_chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    $dados['func_nome'] = $_SESSION['log_nome'];
    $dados['id_requisicao'] = $id_requisicao;
    $dados['resRequisicao'] = $this->mRequisicao->ListarRequisicao($id_requisicao);
    $dados['resCoordenadores'] = $this->mRequisicao->ListarGerenteCoordenadores($id_requisicao);
    if($dados['isAdmin']) {
      $dados['resFunc'] = $this->mRequisicao->ListarFunc();
    } else {
      $dados['resFunc'] = $this->mRequisicao->ListarFuncChapaGestor($dados['func_chapa']);
    }
    $dados['resTargets'] = $this->mRequisicao->ListarTargetsPremissas($dados['resRequisicao'][0]['chapa_gerente'],$_SESSION['func_coligada']);
    $dados['tipo_req'] = $dados['resRequisicao'][0]['tipo'];

    if($dados['resRequisicao'] <> []) {
      if($dados['resRequisicao'][0]['chapa_requisitor'] != $dados['func_chapa'] && !$dados['isAdmin']){
        notificacao('danger', 'Sem permissão para acessar esta página');
        redireciona('premio/requisicao');
      } 
    }
    
    return parent::ViewPortal('premio/requisicao/nova_chapa', $dados);
  }

  // ------------------------------------------------------------------
  // Aprovação de Requisicao de Colaborador LOADING
  // ------------------------------------------------------------------ 
  public function aprova_requisicao()
  {
    parent::VerificaPerfil('PREMIO_APROVA_REQUISICAO');

    if($_SESSION['func_coligada'] == ""){
      return redirect()->to(base_url('portal/coligada'));
    }
 
    //$dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

    $dados['_titulo'] = "Aprovação de Requisição de Prêmios";
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $this->_breadcrumb->add($dados['_titulo'], 'premio/requisicao/aprovacao');
    
    return parent::ViewPortal('premio/requisicao/aprova_requisicao_loading', $dados);
  }

  // ------------------------------------------------------------------
  // Aprovação de Requisicao de Colaborador
  // ------------------------------------------------------------------ 
  public function aprova_requisicao_main()
  {
    parent::VerificaPerfil('PREMIO_APROVA_REQUISICAO');

    if($_SESSION['func_coligada'] == ""){
      return redirect()->to(base_url('portal/coligada'));
    }
 
    //$dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

    $dados['_titulo'] = "Aprovação de Requisição de Prêmios";
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $this->_breadcrumb->add($dados['_titulo'], 'premio/requisicao/aprovacao');
    $dados['resRequisicao'] = $this->mRequisicao->ListarAprovaRequisicao();
    $dados['resParam'] = $this->mRequisicao->ListarParam();
    
    return parent::ViewPortal('premio/requisicao/aprova_requisicao', $dados);
  }

  //-----------------------------------------------------------
  // Actions Requisicao
  //-----------------------------------------------------------
  public function action($act)
  {
    $dados = $_POST;
    if(!$dados) return false;
      switch($act){
        //-------------------------------------
        // deletar requisição
        case 'deletar':
            exit($this->mRequisicao->DeletarRequisicao($dados));
            break;
        //-------------------------------------
        // novo Requisicao
        case 'cadastrar':
            exit($this->mRequisicao->CadastrarRequisicao($dados));
            break;
        //-------------------------------------
        // processar Requisicao
        case 'processar':
          exit($this->mRequisicao->ProcessarRequisicao($dados));
          break;
        //-------------------------------------
        // aprovar Requisicao
        case 'aprovar':
          exit($this->mRequisicao->AprovarRequisicao($dados));
          break;
        //-------------------------------------
        // aprovar Requisicao como RH Master
        case 'aprovarRH':
          exit($this->mRequisicao->AprovarRHRequisicao($dados));
          break;
        //-------------------------------------
        // recusar Requisicao
        case 'reprovar':
          exit($this->mRequisicao->ReprovarRequisicao($dados));
          break;
        //-------------------------------------
        // importar Requisicao
        case 'importar':
          $dados['documento'] = $_FILES;
          exit($this->mRequisicao->ImportarRequisicao($dados));
          break;
        // deletar chapa da requisição
        case 'deletar_chapa':
          exit($this->mRequisicao->DeletarChapaRequisicao($dados));
          break;
        // editar chapa da requisição
        case 'editar_chapa':
          exit($this->mRequisicao->EditarChapaRequisicao($dados));
          break;
        // nova chapa na requisição
        case 'nova_chapa':
          exit($this->mRequisicao->NovaChapaRequisicao($dados));
          break;
        // enviar requisição para aprovação
        case 'requisitar':
          exit($this->mRequisicao->EnviarRequisicaoAprovacao($dados));
          break;
        // enviar requisição para aprovação
        case 'calcularRH':
          exit($this->mRequisicao->CalcularRequisicaoRH($dados));
          break;
        // sincronizar com RM
        case 'sincRM':
          exit($this->mRequisicao->SincRMrequisicaoRH($dados));
          break;
      }

  }

  // ------------------------------------------------------------------
  // importar requisicao do excel
  // ------------------------------------------------------------------
  public function importar_requisicao($id_requisicao)
  {
    parent::VerificaPerfil('PREMIO_REQUISICAO');

		$dados['_titulo'] = "Importar Requisição | Prêmio <span class=\"badge badge-info\">Nº {$id_requisicao}</span>";
    $dados['id_requisicao'] = $id_requisicao;
    
    $this->_breadcrumb->add($dados['_titulo'], 'premio/requisicao/editar');
        
		return parent::ViewPortal('premio/requisicao/importar_requisicao', $dados);

  }


  // ------------------------------------------------------------------
  // exportar requisicao para excel
  // ------------------------------------------------------------------
  public function exportar_requisicao($id_requisicao)
  {
    parent::VerificaPerfil('PREMIO_REQUISICAO');

    $resRequisicao = $this->mRequisicao->ListarChapasRequisicao($id_requisicao);

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
    $spreadsheet->getActiveSheet()->setTitle('Requisição número '.$id_requisicao);
    $spreadsheet->getActiveSheet()->setAutoFilter('A1:K1'); // auto filtro no titulo

    // titulo das colunas
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'TIPO');
    $sheet->setCellValue('B1', 'FUNC_CHAPA');
    $sheet->setCellValue('C1', 'FUNC_NOME');
    $sheet->setCellValue('D1', 'FUNCAO');
    $sheet->setCellValue('E1', 'SITUACAO');
    $sheet->setCellValue('F1', 'DT_ADMISSAO_BR');
    $sheet->setCellValue('G1', 'CODFILIAL');
    $sheet->setCellValue('H1', 'CODCUSTO');
    $sheet->setCellValue('I1', 'SECAO');
    $sheet->setCellValue('J1', 'TARGET');
    $sheet->setCellValue('K1', 'REALIZADO');

    $rows = 2;

    if($resRequisicao){
        foreach($resRequisicao as $key => $Requisicao){
          $sheet->setCellValue('A' . $rows, $Requisicao['tipo'] == 'P' ? 'Padrão' : 'Exceção');
          $sheet->setCellValue('B' . $rows, $Requisicao['func_chapa']);
          $sheet->setCellValue('C' . $rows, $Requisicao['func_nome']);
          $sheet->setCellValue('D' . $rows, $Requisicao['funcao']);
          $sheet->setCellValue('E' . $rows, $Requisicao['situacao']);
          $sheet->setCellValue('F' . $rows, $Requisicao['dt_admissao_br']);
          $sheet->setCellValue('G' . $rows, $Requisicao['codfilial']);
          $sheet->setCellValue('H' . $rows, $Requisicao['codcusto']);
          $sheet->setCellValue('I' . $rows, $Requisicao['secao']);
          $sheet->setCellValue('J' . $rows, $Requisicao['target']);
          $sheet->setCellValue('K' . $rows, $Requisicao['realizado']);
        
          $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':K'.$rows)->applyFromArray($styleBorda);
          $rows++;
        }
    }

    for($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
        $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    $writer = new Xlsx($spreadsheet);

    header('Content-Disposition: attachment; filename=Requisição número '.$id_requisicao.'.xlsx' );
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $writer->save("php://output");

    exit();

  }  

  //-----------------------------------------------------------
  // Workflow de envio de emails pendentes de aprovação
  //-----------------------------------------------------------
  public function workflow(){
    $this->mRequisicao->Workflow();
    exit();
  }
  
}

