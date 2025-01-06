<?php
namespace App\Controllers\Hierarquia;
use App\Controllers\BaseController;

Class Lider extends BaseController {

    private $mLider;

    public function __construct()
    {
        parent::__construct('Equipe');
        $this->_moduloName = '<i class="mdi mdi-sitemap"></i> Equipe';
        $this->mLider = model('Hierarquia/LiderModel');
    }

    public function index($chapaGestor = null)
    {
        parent::VerificaPerfil('HIERARQUIA_LIDER');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Equipe Líder";
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/lider');

        if($dados['rh']){
            $chapaGestor = ($this->request->getPost('chapaGestor')) ? $this->request->getPost('chapaGestor') : $chapaGestor;
            $dados['chapaGestor'] = $chapaGestor;
            $resGestores = $this->mLider->listaGestores();
        }else{
            $dados['chapaGestor'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $resGestores = false;
        }

        $dados['resGestores']           = $resGestores;
        $dados['resHierarquiaLider']    = $this->mLider->listaHierarquiaLider($dados['chapaGestor']);

        return parent::ViewPortal('hierarquia/lider/index', $dados);
    }

    public function novo($chapaGestor = null)
    {
        parent::VerificaPerfil('HIERARQUIA_LIDER');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Nova Equipe Líder";
        $this->_breadcrumb->add('Equipe Líder', 'hierarquia/lider');
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/lider');

        if($dados['rh']){
            $dados['chapaGestor']   = ($this->request->getPost('chapaGestor') != null) ? $this->request->getPost('chapaGestor') : $chapaGestor;
            $resGestores            = $this->mLider->listaGestores();
        }else{
            $dados['chapaGestor']   = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $resGestores            = false;
        }
        
        $dados['resGestores']       = $resGestores;
        $dados['resSecaoGestor']    = $this->mLider->listaSecaoGestor($dados['chapaGestor'], $dados['rh']);
        $dados['resFuncaoGestor']   = $this->mLider->listaFuncaoGestor($dados['chapaGestor'], $dados['rh']);
        
        return parent::ViewPortal('hierarquia/lider/novo', $dados);
    }

    public function editar($id)
    {

        $dados['id'] = cid($id);

        // echo date('d/m/Y H:i:s').' - 1<br>';

        parent::VerificaPerfil('HIERARQUIA_LIDER');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Editar Equipe Líder";
        $this->_breadcrumb->add('Equipe Líder', 'hierarquia/lider');
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/lider');

        // echo date('d/m/Y H:i:s').' - 2<br>';
        $dados['resDadosLider']         = $this->mLider->listaDadosHierarquiaLider($dados['id']);
        // echo date('d/m/Y H:i:s').' - 3<br>';
        $dados['resFuncionariosLider']  = $this->mLider->listaFuncionariosLider($dados['id']);

        // echo date('d/m/Y H:i:s').' - <br>';exit();

        // echo date('d/m/Y H:i:s').' - 3<br>';
        if(!$dados['resDadosLider']){
            notificacao('danger', 'Equipe Líder não encontrada');
            redireciona(base_url('hierarquia/lider'));
        }
        $chapaGestor                    = $dados['resDadosLider'][0]['chapa_gestor'];

        if($dados['rh']){
            $dados['chapaGestor']   = ($this->request->getPost('chapaGestor') != null) ? $this->request->getPost('chapaGestor') : $chapaGestor;
            $resGestores            = $this->mLider->listaGestores();
        }else{
            $dados['chapaGestor']   = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $resGestores            = false;
        }
        
        $dados['resGestores']       = $resGestores;
        $dados['resSecaoGestor']    = $this->mLider->listaSecaoGestor($dados['chapaGestor'], $dados['rh']);
        $dados['resFuncaoGestor']   = $this->mLider->listaFuncaoGestor($dados['chapaGestor'], $dados['rh']);
        $dados['resLiderExcecao']   = $this->mLider->listaLiderExcecao($dados['id']);
        
        return parent::ViewPortal('hierarquia/lider/editar', $dados);
    }

    public function inativaLider(){


        $this->mLider->inativaLider();

    }

    public function action($act)
    {

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            case 'lista_secao_gestor':
                exit($this->mLider->ListaSecapGestor($dados));
                break;
            case 'lista_funcionarios_gestor':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit(json_encode($this->mLider->listaFuncionariosGestor($dados)));
                break;
            case 'lista_funcionarios_secoes':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit(json_encode($this->mLider->listaFuncionariosSecao($dados)));
                break;
            case 'cadastrar_lider':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit($this->mLider->cadastrarLider($dados));
                break;
            case 'tipo_aprovador':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit($this->mLider->tipoAprovador($dados));
                break;
            case 'alterar_lider':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit($this->mLider->alterarLider($dados));
                break;
            case 'remover_lider':
                exit($this->mLider->removerLider($dados));
                break;
            case 'cadastrar_lider_excecao':
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit($this->mLider->cadastrarLiderExcecao($dados));
                break;
            case 'remover_lider_excecao':
                exit($this->mLider->removerLiderExcecao($dados));
                break;
                
        }

        

    }

}