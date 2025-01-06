<?php

namespace App\Controllers\Premio;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controllers\BaseController;

class Cadastro extends BaseController
{

  private $mCadastro;

  public function __construct()
  {
    parent::__construct('Premio'); // sempre manter

    $this->_moduloName = '<i class="fas fa-trophy"></i> Prêmios';
    $this->mCadastro = model('Premio/CadastroModel');
  }

  public function index()
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    if($_SESSION['func_coligada'] == ""){
      return redirect()->to(base_url('portal/coligada'));
    }
 
    //$dados['perfilRH'] = parent::VerificaPerfil('GLOBAL_RH', false);

    $dados['_titulo'] = "Cadastro de Prêmios";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro');
    $dados['resPremios'] = $this->mCadastro->ListarPremios();
    
    return parent::ViewPortal('premio/cadastro/index', $dados);

  }

  // ------------------------------------------------------------------
  // Novo Prêmio
  // ------------------------------------------------------------------
  public function novo()
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Novo Prêmio";
    $this->_breadcrumb->add('Novo Prêmio');

    return parent::ViewPortal('premio/cadastro/novo', $dados);
  }

  // ------------------------------------------------------------------
  // editar premio
  // ------------------------------------------------------------------
  public function editar($id)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Editar Prêmio | <span class=\"badge badge-info\">Nº {$id}</span>";
      
    $this->_breadcrumb->add('Editar Prêmio');

    $resPremios = $this->mCadastro->ListarPremios($id);
    if(!$resPremios){
      session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Premio não localizado.')));
      return redirect()->to(base_url('premio/cadastro'));
    }

    $dados['resPremios'] = $resPremios;
    $dados['id'] = $id;

    return parent::ViewPortal('premio/cadastro/editar', $dados);
  }

  //-----------------------------------------------------------
  // Actions Prêmios
  //-----------------------------------------------------------
  public function action($act)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados = $_POST;
    if(!$dados) return false;
      
      switch($act){
          
        //-------------------------------------
        // delete
        case 'deletar':
            exit($this->mCadastro->DeletarPremio($dados));
            break;
        //-------------------------------------
        // novo premio
        case 'cadastrar':
            exit($this->mCadastro->CadastrarPremio($dados));
            break;
        //-------------------------------------
        // edita premio
        case 'editar':
            exit($this->mCadastro->EditarPremio($dados));
            break;
        //-------------------------------------
        // edita deflatores
        case 'editar_deflatores':
          exit($this->mCadastro->EditarDeflatores($dados));
          break;
        //-------------------------------------
        // edita apuracao
        case 'editar_apuracao':
          exit($this->mCadastro->EditarApuracao($dados));
          break;
      }

  }

  // ------------------------------------------------------------------
  // Premissas do Prêmio
  // ------------------------------------------------------------------
  public function premissas($id)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Premissas do Prêmio | <span class=\"badge badge-info\">Nº {$id}</span>";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/premissas');
    $dados['resPremissas'] = $this->mCadastro->ListarPremissas($id);
    $dados['id_premio'] = $id;
    
    return parent::ViewPortal('premio/cadastro/premissas', $dados);

  }

  // ------------------------------------------------------------------
  // Acessos do Prêmio
  // ------------------------------------------------------------------
  public function acessos($id)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Acessos do Prêmio | <span class=\"badge badge-info\">Nº {$id}</span>";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/acessos');
    $dados['resAcessos'] = $this->mCadastro->ListarAcessos($id);
    $dados['id_premio'] = $id;
    
    return parent::ViewPortal('premio/cadastro/acessos', $dados);


  }

  // ------------------------------------------------------------------
  // Exceção de Acessos do Prêmio
  // ------------------------------------------------------------------
  public function acessos_excecao($id)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Exceção de Acessos do Prêmio | <span class=\"badge badge-info\">Nº {$id}</span>";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/acessos_excecao');
    $dados['resAcessos'] = $this->mCadastro->ListarAcessosExcecao($id);
    $dados['id_premio'] = $id;
    
    return parent::ViewPortal('premio/cadastro/acessos_excecao', $dados);


  }

  // ------------------------------------------------------------------
  // Usuários do acesso
  // ------------------------------------------------------------------
  public function acessos_usuarios($id, $id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Usuários do Acesso | <span class=\"badge badge-info\">Nº {$id}</span>";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/acessos_usuarios');
    $dados['resFunc'] = $this->mCadastro->ListarFunc();
    $dados['resFunc'] = $dados['resFunc'] ? $dados['resFunc'] : [];
    $dados['resAcesso'] = $this->mCadastro->ListarAcesso($id);
    $dados['resPonto'] = $this->mCadastro->ListarPontoUsuarios($id_premio);
    $dados['resPonto'] = $dados['resPonto'] ? $dados['resPonto'] : [];
    $dados['resAcessoUsuarios'] = $this->mCadastro->ListarAcessoUsuarios($id);
    $dados['id_acesso'] = $id;
    
    return parent::ViewPortal('premio/cadastro/acessos_usuarios', $dados);

  }

  // ------------------------------------------------------------------
  // Usuários do acesso de exceção
  // ------------------------------------------------------------------
  public function acessos_excecao_usuarios($id, $id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Usuários do Acesso de Exceção | <span class=\"badge badge-info\">Nº {$id}</span>";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/acessos_excecao_usuarios');
    $dados['resFunc'] = $this->mCadastro->ListarFunc();
    $dados['resFunc'] = $dados['resFunc'] ? $dados['resFunc'] : [];
    $dados['resAcesso'] = $this->mCadastro->ListarAcessoExcecao($id);
    $dados['resPonto'] = $this->mCadastro->ListarPontoExcecaoUsuarios($id_premio);
    $dados['resPonto'] = $dados['resPonto'] ? $dados['resPonto'] : [];
    $dados['resAcessoUsuarios'] = $this->mCadastro->ListarAcessoExcecaoUsuarios($id);
    $dados['id_acesso'] = $id;
    
    return parent::ViewPortal('premio/cadastro/acessos_excecao_usuarios', $dados);

  }

  // ------------------------------------------------------------------
  // Nova Premissa
  // ------------------------------------------------------------------
  public function nova_premissa($id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Nova Premissa | Prêmio <span class=\"badge badge-info\">Nº {$id_premio}</span>";
    $this->_breadcrumb->add('Nova Premissa', 'premio/cadastro/premissas');
    $dados['resFilial'] = $this->mCadastro->ListarFilial();
    $dados['resCentroCusto'] = $this->mCadastro->ListarCentroCusto();
    $dados['resFuncao'] = $this->mCadastro->ListarFuncao();
    $dados['id_premio'] = $id_premio;
    
    return parent::ViewPortal('premio/cadastro/nova_premissa', $dados);
  }

  //-----------------------------------------------------------
  // Actions Premissas
  //-----------------------------------------------------------
  public function action_premissas($act)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados = $_POST;
    if(!$dados) return false;
      
      switch($act){
          
        //-------------------------------------
        // delete
        case 'deletar':
          exit($this->mCadastro->DeletarPremissa($dados));
          break;
        //-------------------------------------
        // delete todas as premissas
        case 'deletar_todas':
          exit($this->mCadastro->DeletarPremissasPremio($dados));
          break;
        //-------------------------------------
        // nova premissa
        case 'cadastrar':
          exit($this->mCadastro->CadastrarPremissa($dados));
          break;
        //-------------------------------------
        // editar premissa
        case 'editar':
          exit($this->mCadastro->EditarPremissa($dados));
          break;
        //-------------------------------------
        // importação de ocorrencias
        case 'importacao':
            $dados['documento'] = $_FILES;
            exit($this->mCadastro->ImportarPremissas($dados));
            break;  
      }

  }

  // ------------------------------------------------------------------
  // editar premissa
  // ------------------------------------------------------------------
  public function editar_premissa($id, $id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Editar Premissa | <span class=\"badge badge-info\">Nº {$id}</span>";
      
    $this->_breadcrumb->add('Editar Premissa', 'premio/cadastro/premissas');

    $resPremissa = $this->mCadastro->ListarPremissas($id_premio, $id);
    if(!$resPremissa){
      session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Premissa não localizada.')));
      return redirect()->to(base_url('premio/cadastro/premissas/'.$id_premio));
    }

    $dados['resPremissas'] = $resPremissa;
    $dados['resFilial'] = $this->mCadastro->ListarFilial();
    $dados['resCentroCusto'] = $this->mCadastro->ListarCentroCusto();
    $dados['resFuncao'] = $this->mCadastro->ListarFuncao();
    $dados['id'] = $id;
    $dados['id_premio'] = $id_premio;

    return parent::ViewPortal('premio/cadastro/editar_premissa', $dados);
  }  

  // ------------------------------------------------------------------
  // exportar premissas para excel
  // ------------------------------------------------------------------
  public function exportar_premissas($id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $resPremissa = $this->mCadastro->ListarPremissas($id_premio);

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
    $spreadsheet->getActiveSheet()->setTitle('Premissas do Prêmio '.$id_premio);
    $spreadsheet->getActiveSheet()->setAutoFilter('A1:G1'); // auto filtro no titulo

    // titulo das colunas
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'COD_FILIAL');
    $sheet->setCellValue('B1', 'COD_CCUSTO');
    $sheet->setCellValue('C1', 'COD_FUNCAO');
    $sheet->setCellValue('D1', 'DESC_FUNCAO');
    $sheet->setCellValue('E1', 'GRUPO');
    $sheet->setCellValue('F1', 'PORC_TARGET');
    $sheet->setCellValue('G1', 'TIPO_TARGET');

    $rows = 2;

    if($resPremissa){
        foreach($resPremissa as $key => $Premissa){
          $sheet->setCellValue('A' . $rows, $Premissa['codfilial']);
          $sheet->setCellValue('B' . $rows, $Premissa['codcusto']);
          $sheet->setCellValue('C' . $rows, $Premissa['codfuncao']);
          $sheet->setCellValue('D' . $rows, $Premissa['nome_funcao']);
          $sheet->setCellValue('E' . $rows, $Premissa['grupo']);
          $sheet->setCellValue('F' . $rows, $Premissa['target']);
          $sheet->setCellValue('G' . $rows, $Premissa['tipo_target']);
        
            $spreadsheet->getActiveSheet()->getStyle('A'.$rows.':G'.$rows)->applyFromArray($styleBorda);
            $rows++;
        }
    }

    for($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
        $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
    }

    $writer = new Xlsx($spreadsheet);

    header('Content-Disposition: attachment; filename=Premissas do Premio '.$id_premio.'.xlsx' );
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    $writer->save("php://output");

    exit();

  }  

  public function importar_premissas($id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

		$dados['_titulo'] = "Importar Premissas | Prêmio <span class=\"badge badge-info\">Nº {$id_premio}</span>";
    $dados['id_premio'] = $id_premio;
    
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/premissas');
        
		return parent::ViewPortal('premio/cadastro/importar_premissas', $dados);

  }

  // ------------------------------------------------------------------
  // Deflatores do Prêmio
  // ------------------------------------------------------------------
  public function deflatores($id)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Deflatores do Prêmio | <span class=\"badge badge-info\">Nº {$id}</span>";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/deflatores');
    $dados['resDeflatores'] = $this->mCadastro->ListarDeflatores($id);
    $dados['id_premio'] = $id;
    
    return parent::ViewPortal('premio/cadastro/deflatores', $dados);

  }

  // ------------------------------------------------------------------
  // Apuração do Prêmio
  // ------------------------------------------------------------------
  public function apuracao($id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Apuração do Prêmio | <span class=\"badge badge-info\">Nº {$id_premio}</span>";
    $this->_breadcrumb->add($dados['_titulo'], 'premio/cadastro/apuracao');
    $dados['resApuracao'] = $this->mCadastro->ListarApuracao($id_premio);
    $dados['id_premio'] = $id_premio;
    
    return parent::ViewPortal('premio/cadastro/apuracao', $dados);

  }

  // ------------------------------------------------------------------
  // Novo Acesso
  // ------------------------------------------------------------------
  public function novo_acesso($id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Novo Acesso | Prêmio <span class=\"badge badge-info\">Nº {$id_premio}</span>";
    $this->_breadcrumb->add('Novo Acesso', 'premio/cadastro/acessos');
    $dados['resAcessos'] = $this->mCadastro->ListarAcessos($id_premio);
    $dados['resPonto'] = $this->mCadastro->ListarPeriodoPonto();
    $dados['id_premio'] = $id_premio;
    
    return parent::ViewPortal('premio/cadastro/novo_acesso', $dados);
  }

  // ------------------------------------------------------------------
  // Nova Excecao de Acesso
  // ------------------------------------------------------------------
  public function novo_acesso_excecao($id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Nova Exceção de Acesso | Prêmio <span class=\"badge badge-info\">Nº {$id_premio}</span>";
    $this->_breadcrumb->add('Nova Exceção de Acesso', 'premio/cadastro/acessos');
    $dados['resAcessos'] = $this->mCadastro->ListarAcessosExcecao($id_premio);
    $dados['resPonto'] = $this->mCadastro->ListarPeriodoPonto();
    $dados['id_premio'] = $id_premio;
    
    return parent::ViewPortal('premio/cadastro/novo_acesso_excecao', $dados);
  }

  //-----------------------------------------------------------
  // Actions Acessos
  //-----------------------------------------------------------
  public function action_acessos($act)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados = $_POST;
    if(!$dados) return false;
      
      switch($act){
          
        //-------------------------------------
        // deletar acesso
        case 'deletar':
            exit($this->mCadastro->DeletarAcesso($dados));
            break;
        //-------------------------------------
        // novo acesso
        case 'cadastrar':
          exit($this->mCadastro->CadastrarAcesso($dados));
          break;
        //-------------------------------------
        // editar acesso
        case 'editar':
          exit($this->mCadastro->EditarAcesso($dados));
          break;
        //-------------------------------------
        // adicionar colaborador
        case 'adicionar_usuario':
          exit($this->mCadastro->AdicionarUsuario($dados));
          break;
        //-------------------------------------
        // importar usuários
        case 'importar_usuarios':
          exit($this->mCadastro->ImportarUsuarios($dados));
          break;
        //-------------------------------------
        // remover colaborador
        case 'deletar_usuario':
          exit($this->mCadastro->DeletarUsuario($dados));
          break;

      }

  }

  //-----------------------------------------------------------
  // Actions Exceção de Acessos
  //-----------------------------------------------------------
  public function action_acessos_excecao($act)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados = $_POST;
    if(!$dados) return false;
      
      switch($act){
          
        //-------------------------------------
        // deletar acesso
        case 'deletar':
            exit($this->mCadastro->DeletarAcessoExcecao($dados));
            break;
        //-------------------------------------
        // novo acesso
        case 'cadastrar':
          exit($this->mCadastro->CadastrarAcessoExcecao($dados));
          break;
        //-------------------------------------
        // editar acesso
        case 'editar':
          exit($this->mCadastro->EditarAcessoExcecao($dados));
          break;
        //-------------------------------------
        // adicionar colaborador
        case 'adicionar_usuario':
          exit($this->mCadastro->AdicionarUsuarioExcecao($dados));
          break;
        //-------------------------------------
        // importar usuários
        case 'importar_usuarios':
          exit($this->mCadastro->ImportarUsuariosExcecao($dados));
          break;
        //-------------------------------------
        // remover colaborador
        case 'deletar_usuario':
          exit($this->mCadastro->DeletarUsuarioExcecao($dados));
          break;

      }

  }

  // ------------------------------------------------------------------
  // editar acesso
  // ------------------------------------------------------------------
  public function editar_acesso($id, $id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Editar Acesso | <span class=\"badge badge-info\">Nº {$id}</span>";
      
    $this->_breadcrumb->add('Editar Acesso', 'premio/cadastro/acessos');

    $resAcesso = $this->mCadastro->ListarAcessos($id_premio, $id);
    if(!$resAcesso){
      session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Acesso não localizado.')));
      return redirect()->to(base_url('premio/cadastro/acessos/'.$id_premio));
    }

    $dados['resAcesso'] = $resAcesso;
    $dados['resPonto'] = $this->mCadastro->ListarPeriodoPonto();
    $dados['resAcessos'] = $this->mCadastro->ListarAcessos($id_premio);
    $dados['id'] = $id;
    $dados['id_premio'] = $id_premio;

    return parent::ViewPortal('premio/cadastro/editar_acesso', $dados);
  } 
  
  // ------------------------------------------------------------------
  // editar exceção de acesso
  // ------------------------------------------------------------------
  public function editar_acesso_excecao($id, $id_premio)
  {
    parent::VerificaPerfil('PREMIO_CADASTRO');

    $dados['_titulo'] = "Editar Exceção de Acesso | <span class=\"badge badge-info\">Nº {$id}</span>";
      
    $this->_breadcrumb->add('Editar Exceção de Acesso', 'premio/cadastro/acessos');

    $resAcesso = $this->mCadastro->ListarAcessosExcecao($id_premio, $id);
    if(!$resAcesso){
      session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Exceção de Acesso não localizada.')));
      return redirect()->to(base_url('premio/cadastro/acessos_excecao/'.$id_premio));
    }

    $dados['resAcesso'] = $resAcesso;
    $dados['resPonto'] = $this->mCadastro->ListarPeriodoPonto();
    $dados['resAcessos'] = $this->mCadastro->ListarAcessosExcecao($id_premio);
    $dados['id'] = $id;
    $dados['id_premio'] = $id_premio;

    return parent::ViewPortal('premio/cadastro/editar_acesso_excecao', $dados);
  } 
  
}

