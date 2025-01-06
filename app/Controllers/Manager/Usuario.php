<?php
namespace App\Controllers\Manager;
use App\Controllers\BaseController;


Class Usuario extends BaseController {

    public function __construct()
    {
        parent::__construct('Manager'); // sempre manter
        // permissão de acesso ao modulo MANAGER
        parent::VerificaPerfil('MANAGER_USUARIOS');
        
        $this->_moduloName = '<i class="mdi mdi-settings"></i> Manager';
        $this->_breadcrumb->add('Usuários', 'manager/usuario');

        $this->mManager = model('ManagerModel');

    }

    public function index(){
        $dados['_titulo'] = "Usuários Portal";
        $dados['resDados'] = $this->mManager->ListarUsuario();
        
        return parent::ViewPortal('manager/usuarios/index', $dados);
    }

    // ------------------------------------------------------------------
    // novo usuário
    // ------------------------------------------------------------------
    public function novo(){

        $dados['_titulo'] = "Novo Usuário Portal";
        
        $this->_breadcrumb->add('Novo Usuário Portal');

        return parent::ViewPortal('manager/usuarios/novo', $dados);
    }

    // ------------------------------------------------------------------
    // editar usuário
    // ------------------------------------------------------------------
    public function editar($id){

        $dados['_titulo'] = "Editar Usuário Portal | <span class=\"badge badge-info\">Nº {$id}</span>";
        
        $this->_breadcrumb->add('Editar Usuário Portal');

        $resDados = $this->mManager->ListarUsuario($id);
        if(!$resDados){
            notificacao('danger', 'Usuário não localizado.');
            return redirect()->to(base_url('manager/usuario'));
        }

        $dados['resDados'] = $resDados;
        $dados['id'] = $id;

        return parent::ViewPortal('manager/usuarios/editar', $dados);
    }

    // ------------------------------------------------------------------
    // perfil do usuário
    // ------------------------------------------------------------------
    public function perfil($id){

        $dados['_titulo'] = "Perfil do Portal | <span class=\"badge badge-info\">Nº {$id}</span>";

        $resUsuario = $this->mManager->ListarUsuario($id);
        if(!$resUsuario){
            notificacao('danger', 'Usuário não localizado.');
            return redirect()->to(base_url('manager/usuario'));
        }

        $this->_breadcrumb->add('Perfil do Usuário: '.$resUsuario[0]['nome']);
        
        $dados['resUsuario'] = $resUsuario;
        $dados['resPerfil'] = $this->mManager->ListarPerfil();
        $dados['resUsuarioPerfil'] = $this->mManager->ListarUsuarioPerfil($id);
        $dados['id'] = $id;

        return parent::ViewPortal('manager/usuarios/perfil', $dados);
    }

    // ------------------------------------------------------------------
    // seção do usuário
    // ------------------------------------------------------------------
    public function secao($id){

        $dados['_titulo'] = "Seções do Usuário | <span class=\"badge badge-info\">Nº {$id}</span>";

        $resUsuario = $this->mManager->ListarUsuario($id);
        if(!$resUsuario){
            notificacao('danger', 'Usuário não localizado.');
            return redirect()->to(base_url('manager/usuario'));
        }

        $this->_breadcrumb->add('Seção do Usuário: '.$resUsuario[0]['nome']);

        $mPortal = model('PortalModel');

        $dados['resSecao'] = $mPortal->ListarSecao();
        $dados['resUsuarioSecao'] = $this->mManager->ListarUsuarioSecao($id);
        $dados['id'] = $id;

        return parent::ViewPortal('manager/usuarios/secao', $dados);
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
                exit($this->mManager->DeletarUsuario($dados));
                break;

            //-------------------------------------=
            case 'cadastrar':
                exit($this->mManager->CadastrarUsuario($dados));
                break;

            //-------------------------------------=
            case 'editar':
                exit($this->mManager->EditarUsuario($dados));
                break;

            //-------------------------------------=
            case 'cadastrar_usuario_perfil':
                exit($this->mManager->CadastrarUsuarioPerfil($dados));
                break;

            //-------------------------------------=
            case 'deletar_usuario_perfil':
                exit($this->mManager->DeletarUsuarioPerfil($dados));
                break;

            //-------------------------------------=
            case 'cadastrar_usuario_secao':
                exit($this->mManager->CadastrarUsuarioSecao($dados));
                break;
        }

        

    }

}