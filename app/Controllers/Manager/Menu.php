<?php
namespace App\Controllers\Manager;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;


Class Menu extends BaseController {

    public function __construct()
    {
        
        parent::__construct('Manager'); // sempre manter
        // permissão de acesso ao modulo MANAGER
        parent::VerificaPerfil('MANAGER_MENUS');
        
        $this->_moduloName = '<i class="mdi mdi-settings"></i> Manager';
        $this->_breadcrumb->add('Menus Portal', 'manager/menu');
        $this->mManager = model('ManagerModel');

    }

    public function index(){

        $dados['_titulo'] = "Menus Portal";
        $dados['resMenu'] = $this->mManager->Menu(true);
        $dados['resItensMenu'] = $this->mManager->ItenMenu(true);
        $dados['resFuncao'] = $this->mManager->ListarFuncao();
        
        return parent::ViewPortal('manager/menu/index', $dados);
    }
    //-----------------------------------------------------------
    // Action Função
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return responseJson('error', 'Estrutura do menu não pode ficar em branco.');
        
        switch($act){
            
            //-------------------------------------
            // cadastra estrutura do menu
            case 'cadastrar_estrutura_menu':
                exit($this->mManager->CadastrarMenuEstrutura($dados));
                break;
                
        }

        

    }

}