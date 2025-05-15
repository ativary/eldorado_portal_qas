<?php
namespace App\Controllers\Hierarquia;
use App\Controllers\BaseController;

Class Substituto extends BaseController {

    private $mSubstituto;
    private $mManager;

    public function __construct()
    {
        parent::__construct('Equipe');
        $this->_moduloName = '<i class="mdi mdi-sitemap"></i> Equipe';
        $this->mSubstituto = model('Hierarquia/SubstitutoModel');
        $this->mManager = model('ManagerModel');
    }

    public function index($idGestor = null)
    {
        parent::VerificaPerfil('HIERARQUIA_GESTOR_SUBSTITUTO');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Equipe - Gestor Substituto";
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/substituto');

        if($dados['rh']){
            $idGestor = ($this->request->getPost('idGestor')) ? $this->request->getPost('idGestor') : $idGestor;
            $dados['idGestor'] = $idGestor;
            $resGestores = $this->mSubstituto->listaGestores();
        }else{
            $dados['idGestor'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $resGestores = false;
        }

        $dados['resGestores']  = $resGestores;

        $dados['resSubstitutos'] = ($dados['idGestor']) ? $this->mSubstituto->listaSubstitutosGestor($dados['idGestor']) : null;
 
        return parent::ViewPortal('hierarquia/substituto/index', $dados);
    }

    public function novo($idGestor = null)
    {
        parent::VerificaPerfil('HIERARQUIA_GESTOR_SUBSTITUTO');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Novo gestor substituto";
        $this->_breadcrumb->add('Gestor substituto', 'hierarquia/substituto');
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/substituto');
        $dados['chapaGestor'] = '';

        if($dados['rh']){
            $dados['idGestor']   = ($this->request->getPost('idGestor') != null) ? $this->request->getPost('idGestor') : $idGestor;
            $resGestores            = $this->mSubstituto->listaGestores();
        }else{
            $dados['idGestor']   = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $resGestores            = false;
        }

        $dados['modulos'] = $this->mSubstituto->getModulosGestor($dados['idGestor']);
        
        $dados['resGestores']       = $resGestores;
        return parent::ViewPortal('hierarquia/substituto/novo', $dados);
    }

    public function editar($idReq, $idGestor)
    {   
        parent::VerificaPerfil('HIERARQUIA_GESTOR_SUBSTITUTO');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Editar gestor substituto";
        $this->_breadcrumb->add('Gestor substituto', 'hierarquia/substituto');
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/substituto');

        if($dados['rh']){
            $idGestor = ($this->request->getPost('idGestor')) ? $this->request->getPost('idGestor') : $idGestor;
            $dados['idGestor']   = ($this->request->getPost('idGestor') != null) ? $this->request->getPost('idGestor') : $idGestor;
        }else{
            $dados['idGestor']   = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        }

        $dados['dadosSubstituicao'] = $this->mSubstituto->getDadosSubstituicao($idReq);

        $dados['modulosSelecionados'] = $this->mSubstituto->getModulosSelecionados($dados['dadosSubstituicao'][0]['modulos']);
        
        $dados['modulos'] = $this->mSubstituto->getModulosGestor($dados['dadosSubstituicao'][0]['id_gestor']);
        
        return parent::ViewPortal('hierarquia/substituto/editar', $dados);
    }

    public function lista(){

        parent::VerificaPerfil('HIERARQUIA_GESTOR_SUBSTITUTO');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Lista de Perfis";
        $this->_breadcrumb->add('Perfis gestor substituto', 'hierarquia/substituto');
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/substituto');

        $dados['modulosExistentes'] = $this->mSubstituto->getModulos();

        return parent::ViewPortal('hierarquia/substituto/listamodulo', $dados);

    }

    public function novomodulo(){

        parent::VerificaPerfil('HIERARQUIA_GESTOR_SUBSTITUTO');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Novo Perfil";
        $this->_breadcrumb->add('Perfis gestor substituto', 'hierarquia/substituto');
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/substituto');

        $dados['perfis'] = $this->mManager->ListarPerfil();
        $dados['funcoesSelecionadas'] = [];
        $dados['chapaGestor'] = '';

        return parent::ViewPortal('hierarquia/substituto/novomodulo', $dados);

    }

    public function editarmodulo($id)
    {   
        parent::VerificaPerfil('HIERARQUIA_GESTOR_SUBSTITUTO');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Editar Perfil";
        $this->_breadcrumb->add('Gestor substituto', 'hierarquia/substituto');
        $this->_breadcrumb->add($dados['_titulo'], 'hierarquia/substituto');

        $dados['dadosModulo'] = $this->mSubstituto->getDadosModulo($id);

        $dados['funcoesSelecionadas'] = $this->mSubstituto->getFuncoesSelecionadas($dados['dadosModulo'][0]['funcoes']);
        
        $dados['perfis'] = $this->mManager->ListarPerfil();
        
        return parent::ViewPortal('hierarquia/substituto/editarmodulo', $dados);
    }

    public function action($act)
    {

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            case 'cadastrar_gestor':
                exit($this->mSubstituto->CadastrarGestorSubstituto($dados));
                break;
            case 'lista_funcionarios_gestor':
                exit(json_encode($this->mSubstituto->listaFuncionariosGestor($dados)));
                break;
            case 'inativar_substituto':
                exit(json_encode($this->mSubstituto->inativarSubstituto($dados)));
                break;
            case 'atualizar_gestor':
                exit(json_encode($this->mSubstituto->atualizarGestor($dados)));
                break;
            case 'listar_funcoes_perfil':
                exit(json_encode($this->mManager->ListarPerfilFuncao($dados['idPerfil'], 0)));
                break;
            case 'excluir_modulo':
                exit(json_encode($this->mSubstituto->excluirModulo($dados['id'])));
                break;
            case 'cadastrar_modulo':
                exit(json_encode($this->mSubstituto->cadastrarModulo($dados)));
                break;
            case 'editar_modulo':
                exit(json_encode($this->mSubstituto->editarModulo($dados)));
                break;
                
                
        }

        

    }

}