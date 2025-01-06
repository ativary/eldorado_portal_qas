<?php
namespace App\Controllers\Manager;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;


Class Funcao extends BaseController {

    public function __construct()
    {
        
        parent::__construct('Manager'); // sempre manter
        // permissão de acesso ao modulo MANAGER
        parent::VerificaPerfil('MANAGER_FUNCAO');
        
        $this->_moduloName = '<i class="mdi mdi-settings"></i> Manager';
        $this->_breadcrumb->add('Função', 'manager/funcao');
        $this->mManager = model('ManagerModel');

    }

    public function index(){

        $dados['_titulo'] = "Função Portal";
        $dados['resFuncoes'] = $this->mManager->ListarFuncao();
        
        return parent::ViewPortal('manager/funcao/index', $dados);
    }

    // ------------------------------------------------------------------
    // nova função
    // ------------------------------------------------------------------
    public function novo(){

        $dados['_titulo'] = "Nova Função Portal";
        $this->_breadcrumb->add('Nova Função Portal');

        return parent::ViewPortal('manager/funcao/novo', $dados);
    }

    // ------------------------------------------------------------------
    // editar função
    // ------------------------------------------------------------------
    public function editar($id){

        $dados['_titulo'] = "Editar Função Portal | <span class=\"badge badge-info\">Nº {$id}</span>";
        
        $this->_breadcrumb->add('Editar Função Portal');

        $resFuncoes = $this->mManager->ListarFuncao($id);
        if(!$resFuncoes){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Função não localizada.')));
            return redirect()->to(base_url('manager/funcao'));
        }

        $dados['resFuncoes'] = $resFuncoes;
        $dados['id'] = $id;

        return parent::ViewPortal('manager/funcao/editar', $dados);
    }

    //-----------------------------------------------------------
    // Action Função
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;
        
        switch($act){
            
            //-------------------------------------
            // delete
            case 'deletar':
                exit($this->mManager->DeletarFuncao($dados));
                break;

            //-------------------------------------
            // nova função
            case 'cadastrar':
                exit($this->mManager->CadastrarFuncao($dados));
                break;

            //-------------------------------------
            // edita função
            case 'editar':
                exit($this->mManager->EditarFuncao($dados));
                break;
        }

        

    }

}