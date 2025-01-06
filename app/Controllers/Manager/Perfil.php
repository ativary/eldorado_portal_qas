<?php
namespace App\Controllers\Manager;
use App\Controllers\BaseController;


Class Perfil extends BaseController {

    public function __construct()
    {
        parent::__construct('Manager'); // sempre manter
        // permissão de acesso ao modulo MANAGER
        parent::VerificaPerfil('MANAGER_PERFIL');
        
        $this->_moduloName = '<i class="mdi mdi-settings"></i> Manager';
        $this->_breadcrumb->add('Perfil', 'manager/perfil');

        $this->mManager = model('ManagerModel');

    }

    public function index(){
        $dados['_titulo'] = "Perfil Portal";
        $dados['resDados'] = $this->mManager->ListarPerfil();
        
        return parent::ViewPortal('manager/perfil/index', $dados);
    }

    // ------------------------------------------------------------------
    // nova perfil
    // ------------------------------------------------------------------
    public function novo(){

        $dados['_titulo'] = "Novo Perfil Portal";
        
        $this->_breadcrumb->add('Novo Perfil Portal');

        return parent::ViewPortal('manager/perfil/novo', $dados);
    }

    // ------------------------------------------------------------------
    // editar função
    // ------------------------------------------------------------------
    public function editar($id){

        $dados['_titulo'] = "Editar Perfil Portal | <span class=\"badge badge-info\">Nº {$id}</span>";
        
        $this->_breadcrumb->add('Editar Perfil Portal');

        $resDados = $this->mManager->ListarPerfil($id);
        if(!$resDados){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Perfil não localizado.')));
            return redirect()->to(base_url('manager/perfil'));
        }

        $dados['resDados'] = $resDados;
        $dados['id'] = $id;

        return parent::ViewPortal('manager/perfil/editar', $dados);
    }
    

    // ------------------------------------------------------------------
    // editar função
    // ------------------------------------------------------------------
    public function funcoes($id){

        $dados['_titulo'] = "Funções do Perfil | <span class=\"badge badge-info\">Nº: {$id}</span>";
        
        $this->_breadcrumb->add('Funções do Perfil');

        $resDados = $this->mManager->ListarPerfil($id);
        if(!$resDados){
            session()->set(array('notificacao' => array('danger', '<i class="mdi mdi-alert-outline"></i> Perfil não localizado.')));
            return redirect()->to(base_url('manager/perfil'));
        }

        $dados['resDados'] = $resDados;
        $dados['resFuncoes'] = $this->mManager->ListarFuncao();
        $dados['resFuncaoPerfil'] = $this->mManager->ListarPerfilFuncao($resDados[0]['id']);
        $dados['id'] = $id;

        return parent::ViewPortal('manager/perfil/funcoes', $dados);
    }

    //-----------------------------------------------------------
    // Action Função
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;
        
        switch($act){
            
            //-------------------------------------=
            case 'deletar':
                exit($this->mManager->DeletarPerfil($dados));
                break;

            //-------------------------------------=
            case 'cadastrar':
                exit($this->mManager->CadastrarPerfil($dados));
                break;

            //-------------------------------------=
            case 'editar':
                exit($this->mManager->EditarPerfil($dados));
                break;

            //-------------------------------------=
            case 'deletar_funcao_perfil':
                exit($this->mManager->DeletarPerfilFuncao($dados));
                break;

            //-------------------------------------=
            case 'cadastrar_funcao_perfil':
                exit($this->mManager->CadastrarPerfilFuncao($dados));
                break;
        }

        

    }

}