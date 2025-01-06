<?php
namespace App\Controllers\Acesso;
use App\Controllers\BaseController;

Class Acesso extends BaseController {

    protected $mAcesso;

    public function __construct()
    {
        parent::__construct(); // sempre manter
        $this->mAcesso = model('AcessoModel');
        
    }

    public function index(){
        
        $dados['titulo'] = 'Login';
        echo $this->ViewDefault('acesso/login', $dados);
    }

    //-----------------------------------------------------------
    // Tela de Login
    //-----------------------------------------------------------
    public function login(){

        $dados['titulo'] = 'Login';
        echo $this->ViewDefault('acesso/login', $dados);

    }

    //-----------------------------------------------------------
    // Tela de Login
    //-----------------------------------------------------------
    public function lembrar_senha(){

        $dados['titulo'] = 'Esqueceu a senha?';
        echo $this->ViewDefault('acesso/lembrar_senha', $dados);

    }

    //-----------------------------------------------------------
    // Confirma alteração de senha
    //-----------------------------------------------------------
    public function confirma_novasenha($token = false){

        $this->mAcesso->ConfirmaNovaSenha($token);
        return redirect()->to(base_url('acesso/login'));

    }

    //-----------------------------------------------------------
    // Realiza o logout
    //-----------------------------------------------------------
    public function logout(){
        session()->destroy();
        header('Clear-Site-Data: "cache", "cookies", "storage"');
        return redirect()->to(base_url('acesso/login'));
    }

    //-----------------------------------------------------------
    // Realiza o login vindo do portal antigo
    //-----------------------------------------------------------
    public function login_auto(){
        
        $dados['dados']['u'] = base64_decode($this->request->getPost('p_u'));
        $dados['dados']['link'] = base64_decode($this->request->getPost('p_l'));
        $dados['dados']['login_portal_antigo'] = 1;

        $result = $this->mAcesso->Logar($dados);

        $json = json_decode($result);

        if($json->tipo == 'error'){
            notificacao('danger', 'Falha na autenticação.');
            return $this->login();
        }

        return redirect()->to(base_url($dados['dados']['link']));
    }

    

    //-----------------------------------------------------------
    // Action Login
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            //-------------------------------------
            // logar
            case 'logar':
                exit($this->mAcesso->Logar($dados));
                break;

            //-------------------------------------
            // recuperação de senha
            case 'lembrar_senha':
                exit($this->mAcesso->LembrarSenha($dados['dados']));
                break;
        }

        

    }

}