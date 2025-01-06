<?php
/*namespace App\Controllers\Manager;
use App\Controllers\BaseController;


Class Manager extends BaseController {

    public function __construct()
    {
        parent::__construct('Manager'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-settings"></i> Manager';
        $this->mManager = model('ManagerModel');
    }

    public function index(){
        return parent::ViewPortal('teste');
    }

    // ------------------------------------------------------------------
    // funções
    // ------------------------------------------------------------------
    public function funcao($param = false){

        $dados['_titulo'] = "Função Portal";
        $this->_breadcrumb->add('Função');

        $dados['resFuncoes'] = $this->mManager->ListaFuncao();

        return parent::ViewPortal('manager/funcao', $dados);
    }

    // ------------------------------------------------------------------
    // menu
    // ------------------------------------------------------------------
    public function menu(){

        $dados['_titulo'] = "Menu Portal";
        $this->_breadcrumb->add('Menu');

        return parent::ViewPortal('manager/funcao', $dados);
    }

    // ------------------------------------------------------------------
    // perfil
    // ------------------------------------------------------------------
    public function perfil(){
        
        $dados['_titulo'] = "Perfil Portal";
        $this->_breadcrumb->add('Perfil');

        return parent::ViewPortal('manager/perfil', $dados);
    }

    // ------------------------------------------------------------------
    // usuários
    // ------------------------------------------------------------------
    public function usuarios(){

        $dados['_titulo'] = "Usuários Portal";
        $this->_breadcrumb->add('Usuários');

        return parent::ViewPortal('manager/usuario', $dados);
    }

    public function novo(){
        exit('123');
    }

    //-----------------------------------------------------------
    // Action Login
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;
        //sleep(1);
        switch($act){
            
            //-------------------------------------
            // delete
            case 'deleta_funcao':
                exit($this->mManager->DeletaFuncao($dados));
                break;

            //-------------------------------------
            // recuperação de senha
            case 'lembrar_senha':
                exit($this->mAcesso->LembrarSenha($dados['dados']));
                break;
        }

        

    }

}*/