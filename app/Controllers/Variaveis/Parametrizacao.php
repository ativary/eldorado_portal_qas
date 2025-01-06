<?php
namespace App\Controllers\Variaveis;
use App\Controllers\BaseController;


Class Parametrizacao extends BaseController {

    private $mParam;
  
    public function __construct()
    {
        
        parent::__construct('Variáveis');
        $this->_moduloName = '<i class="mdi mdi-account-star-outline"></i> Variáveis';
        $this->mParam = model('Variaveis/VariaveisModel');

    }

    public function index()
    {
        
       
      
        parent::VerificaPerfil('VARIAVEIS_PARAMETRIZACAO');
        $dados['_titulo'] = "Parametrização";
        $this->_breadcrumb->add($dados['_titulo'], '');

        $dados['listaEventos']  = $this->mParam->listaEventos();
        $dados['listaSecao']    = $this->mParam->listaSecao();
        $dados['listaParentesco']    = $this->mParam->listaParentesco();
        $dados['listaFiliais']    = $this->mParam->listaFiliais();
        $dados['listaCargos']    = $this->mParam->listaCargos();
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
        $dados['param1']        = $this->mParam->getParametros(1);
        $dados['param2']        = $this->mParam->getParametros(2);
        $dados['param3']        = $this->mParam->getParametros(3);
        $dados['param4']        = $this->mParam->getParametros(4);
        $dados['param5']        = $this->mParam->getParametros(5);
        $dados['param6']        = $this->mParam->getParametros(6);
        $dados['param7']        = $this->mParam->getParametros(7);
        $dados['param8']        = $this->mParam->getParametros(8);
        $dados['param9']        = $this->mParam->getParametros(9);
        
        return parent::ViewPortal('variaveis/parametrizacao/index', $dados);

    }

    public function save()
    {

        $request    = $this->request->getPost();
        $result     = $this->mParam->saveParametros($request);
        
        if($result){
            exit(responseJson('success', 'Parâmetros salvos com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao salvar parâmetros.'));

    }
   

}