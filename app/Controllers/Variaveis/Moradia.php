<?php
namespace App\Controllers\Variaveis;
use App\Controllers\BaseController;
use Ramsey\Uuid\Uuid;

Class Moradia extends BaseController {

    private $mParam;
  
    public function __construct()
    {
        
        parent::__construct('Variáveis');
        $this->_moduloName = '<i class="mdi mdi-account-star-outline"></i> Variáveis';
        $this->mParam = model('Variaveis/VariaveisModel');

    }

    public function index()
    {
       
       
        $dados['funcionario']  = false;
        parent::VerificaPerfil('VARIAVEIS_MORADIA');
        $dados['_titulo'] = "Auxilio Moradia";
        $this->_breadcrumb->add($dados['_titulo'], '');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['data_inicio']         = $this->request->getPost('data_inicio');
        $dados['data_fim']            = $this->request->getPost('data_fim');
        $dados['secao']            = $this->request->getPost('secao');
        $dados['funcionario']         = $this->request->getPost('funcionario');
        $dados['situacao']            = $this->request->getPost('situacao');
      
      
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
        $dados['listaEventos']  = $this->mParam->listaEventos();
      
        $dados['listaReqs']     = $this->mParam->getReq(6, $dados['data_inicio'],$dados['data_fim'],$dados['funcionario'],false, $dados['situacao'] );
       
        $dados['listaSecao']    = $this->mParam->listaSecaoGestor( $dados['rh']);
       
        
        return parent::ViewPortal('variaveis/moradia/index', $dados);

    }

    public function novo(){
        parent::VerificaPerfil('VARIAVEIS_MORADIA');
        $dados['_titulo'] = "Novo Auxilio Moradia";
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
        $dados['chapaFunc'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dados['funcionario'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dados['param6']   = json_encode($this->mParam->getParametros(6));
        $this->_breadcrumb->add('Novo Auxilio Moradia');

        return parent::ViewPortal('variaveis/moradia/novo', $dados);
    }

    public function editar($id){
        $id = base64_decode($id);
     
        $dados['_titulo'] = "Editar Auxilio Moradia";
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
    
        $dados['req']     = $this->mParam->getReqDados($id);
        $dados['chapaFunc'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dados['param6']   = json_encode($this->mParam->getParametros(6));
        $dados['valores']     = json_decode($dados['req'][0]->valores) ;
    
        $this->_breadcrumb->add('Editar Auxilio Moradia');

        return parent::ViewPortal('variaveis/moradia/update', $dados);
    }
    public function update(){
      
        $request    = $this->request->getPost();
        $result     = $this->mParam->saveUpdateRequisicao($request);

        $files = $this->request->getFiles(); // Pega todos os arquivos

        
        if($result){
            if($files){
                foreach ($files['anexo'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $result2 = $this->mParam->saveAnexo($request['id'], $file);
                    }
                }
            

            }
            exit(responseJson('success', 'Requisição atualizada com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao salvar requisição.'));
    }

    public function save()
    {
        $result = false;
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $request    = $this->request->getPost();
        $files = $this->request->getFiles(); // Pega todos os arquivos
      
        $result     = $this->mParam->saveRequisicao($request,$dados['rh']);
       
      
        if($result){

            if($result === 'achou'){
                exit(responseJson('error', 'O colaborador já possui uma requisição desse tipo ativa nesse período.'));
            }else{
                if($files){
                    foreach ($files['anexo'] as $file) {
                        if ($file->isValid() && !$file->hasMoved()) {
                            $result2 = $this->mParam->saveAnexo($result, $file);
                        }
                    }
                

                }
                
                
            }
            exit(responseJson('success', 'Requisição salva com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao salvar requisição.'));

    }
    public function Anexos()
    {
        try {
            $request = $this->request->getPost();
            $Anexos = $this->mParam->getAnexos($request['id']);
            // Converte o array em JSON
            return $this->response->setJSON($Anexos);

        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON vazia
            return $this->response->setJSON([]);
        }
    }
    public function salvarAnexo()
    {
        try {
            $request = $this->request->getPost();
            $files = $this->request->getFiles(); // Pega todos os arquivos

            if($files){
                foreach ($files['anexo'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $result2 = $this->mParam->saveAnexo($request['id'], $file);
                    }
                }
            

            }
            
          
            // Converte o array em JSON
            exit(responseJson('success', 'Anexos salvos com sucesso.'));

        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON vazia
            exit(responseJson('error', 'Falha ao salvar Anexos.'));

        }
    }
    public function delete()
    {

        $request    = $this->request->getPost();
       
        $result     = $this->mParam->DeleteReq($request['id']);
     
        if($result){
            exit(responseJson('success', 'Requisição excluída com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao excluir requisição.'));

    }
    public function deleteAnexo()
    {

        $request    = $this->request->getPost();
       
        $result     = $this->mParam->DeleteReqAnexo($request['id']);
     
        if($result){
            exit(responseJson('success', 'Anexo excluído com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao excluir Anexo.'));

    }


    public function dadosFunc()
    {

        $request    = $this->request->getPost();
        $result     = $this->mParam->dadosFunc($request['chapa']);
       

       
        if($result){
            echo json_encode($result);
            exit;
        }
        
        exit(responseJson('error', 'Falha ao salvar parâmetros.'));

    }

    public function selectFunc()
    {

        $request    = $this->request->getPost();
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $result = $this->mParam->ListarFuncionariosSecao($request['codigo'], $dados);

       
        if($result){
            echo json_encode($result);
            exit;
        }
        
        exit(responseJson('error', 'Nenhum funcionário localizado para essa seção.'));

    }
   

}