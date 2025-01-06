<?php

namespace App\Controllers\Premio;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controllers\BaseController;

class Emprestimos extends BaseController
{

  private $mEmprestimos;

  public function __construct()
  {
    parent::__construct('Premio'); // sempre manter

    $this->_moduloName = '<i class="fas fa-trophy"></i> Prêmios';
    $this->mEmprestimos = model('Premio/EmprestimosModel');
  }

  // ------------------------------------------------------------------
  // Emprestimos de Colaborador
  // ------------------------------------------------------------------ 
  public function index()
  {
    parent::VerificaPerfil('PREMIO_EMPRESTIMO');

    if($_SESSION['func_coligada'] == ""){
      return redirect()->to(base_url('portal/coligada'));
    }
 
    //$dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

    $dados['_titulo'] = "Cedência de Colaborador";
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $this->_breadcrumb->add($dados['_titulo'], 'premio/emprestimos');
    $dados['resEmprestimos'] = $this->mEmprestimos->ListarEmprestimos();
    $dados['resAprovar'] = $this->mEmprestimos->ListarEmprestimosAprovar();
    
    return parent::ViewPortal('premio/emprestimos/index', $dados);

  }

  // ------------------------------------------------------------------
  // Novo Emprestimo de Colaborador
  // ------------------------------------------------------------------
  public function novo()
  {
    parent::VerificaPerfil('PREMIO_EMPRESTIMO');

    $dados['_titulo'] = "Nova Cedência de Colaborador";
    $this->_breadcrumb->add('Nova Cedência');
    $dados['resFunc'] = $this->mEmprestimos->ListarFunc();
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $dados['func_chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    $dados['func_nome'] = $_SESSION['log_nome'];
    if ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') {
      $dados['resColab'] = $dados['resFunc'];
      //$dados['resGestores'] = $dados['resFunc']; Solicitado ajuste em plano de testes enviado por Gracy em 30/10/2024
      $dados['resGestores'] = $this->mEmprestimos->ListarGestores();
    } else {
      $dados['resColab'] = $this->mEmprestimos->ListarFuncGestor($dados['func_chapa']);
      $dados['resGestores'] = $this->mEmprestimos->ListarGestores();
    }
    $dados['resAcessos'] = $this->mEmprestimos->ListarAcessosPremios();
    
    return parent::ViewPortal('premio/emprestimos/novo', $dados);
  }

  // ------------------------------------------------------------------
  // Aprovação de Emprestimo de Colaborador
  // ------------------------------------------------------------------ 
  public function aprovacao()
  {
    parent::VerificaPerfil('PREMIO_EMPRESTIMO');

    if($_SESSION['func_coligada'] == ""){
      return redirect()->to(base_url('portal/coligada'));
    }
 
    //$dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

    $dados['_titulo'] = "Cedência de Colaborador - Aprovação";
    $dados['isAdmin'] = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? true : false;
    $this->_breadcrumb->add($dados['_titulo'], 'premio/emprestimos/aprovacao');
    $dados['resEmprestimos'] = $this->mEmprestimos->ListarEmprestimosAprovar();
    
    return parent::ViewPortal('premio/emprestimos/aprovacao', $dados);

  }

  //-----------------------------------------------------------
  // Actions Emprestimos
  //-----------------------------------------------------------
  public function action($act)
  {
    parent::VerificaPerfil('PREMIO_EMPRESTIMO');

    $dados = $_POST;
    if(!$dados) return false;
      
      switch($act){
          
        //-------------------------------------
        // delete
        case 'deletar':
            exit($this->mEmprestimos->DeletarEmprestimo($dados));
            break;
        //-------------------------------------
        // novo emprestimo
        case 'cadastrar':
            exit($this->mEmprestimos->CadastrarEmprestimo($dados));
            break;
        //-------------------------------------
        // aprovar emprestimo
        case 'aprovar':
          exit($this->mEmprestimos->AprovarEmprestimo($dados));
          break;
        //-------------------------------------
        // reprovar emprestimo
        case 'reprovar':
          exit($this->mEmprestimos->ReprovarEmprestimo($dados));
          break;
      }

  }

  //-----------------------------------------------------------
  // Workflow de envio de emails pendentes de aprovação
  //-----------------------------------------------------------
  public function workflow(){
    $this->mEmprestimos->Workflow();
    exit();
  }
  
}

