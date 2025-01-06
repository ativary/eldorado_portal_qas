<?php
namespace App\Controllers\Portal;
use App\Controllers\BaseController;


Class Portal extends BaseController {

    private $mPortal;

    public function __construct()
    {
        
        parent::__construct('Portal'); // sempre manter
        
        
        
        $this->mPortal = model('PortalModel');

    }

    public function index(){

        return $this->coligada();

    }

    public function coligada(){

        $this->_moduloName = '<i class="mdi mdi-factory"></i> Portal';
        $this->_breadcrumb->add('Coligadas', 'portal/index');

        $dados['resColigada'] = $this->mPortal->ListarColigada();
        
        return parent::ViewPortal('portal/coligada', $dados);

    }

    public function chapa(){

        $this->_moduloName = '<i class="mdi mdi-factory"></i> Portal';
        $this->_breadcrumb->add('Registros', 'portal/index');

        $log_login = session()->get('log_login');
        $cpf = ($log_login) ? $log_login : false;
        $dados['resChapa'] = $this->mPortal->ListarDadosFuncionario($cpf);
        
        return parent::ViewPortal('portal/chapa', $dados);

    }

    public function alterasenha(){

        $this->_moduloName = '<i class="mdi mdi-factory"></i> Portal';
        $this->_breadcrumb->add('Alterar senha', 'portal/index');
        
        return parent::ViewPortal('portal/alterasenha');

    }

    //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            //-------------------------------------
            // seleciona coligada
            case 'seleciona_coligada':
                session()->set('func_coligada', $dados['codcoligada']);

                if(session()->get('func_chapa')){
                    if(util_chapa(session()->get('func_chapa'))['CODCOLIGADA'] != $dados['codcoligada']){
                        session()->set('func_chapa', null);
                    }
                }

                return responseJson('success', '<b>Empresa</b> selecionada com sucesso.');
                break;
            //-------------------------------------
            // seleciona chapa
            case 'seleciona_chapa':

                // valida chapas do cpf
                $chapas = $this->mPortal->ListarDadosFuncionario(session()->get('log_login'));
                if(!$chapas) return responseJson('error', 'Chapa não existe.');
                $validado = false;
                if(is_array($chapas)){
                    foreach($chapas as $key => $Chapa){

                        $chapa_info = explode(':', base64_decode($dados['registro']));
                        
                        // verifica se o registro é a mesma cóligada ativa
                        if($chapa_info[1] != session()->get('func_coligada')) return responseJson('warning', 'É necessário alterar a empresa para acessar os dados deste registro.');

                        if($Chapa['CHAPA'] == $chapa_info[0]){
                            $validado = true;
                            break;
                        }
                    }
                }

                if(!$validado) return responseJson('error', 'Chapa não existe.');

                session()->set('func_chapa', $dados['registro']);
                return responseJson('success', '<b>Registro</b> selecionado com sucesso.');
                break;
            //-------------------------------------
            // altera senha de acesso
            case 'alterar_senha':
                exit($this->mPortal->AlterarSenha($dados));
                break;
        }

        

    }

}