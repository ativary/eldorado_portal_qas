<?php
namespace App\Controllers\Variaveis;
use App\Controllers\BaseController;


Class Sincronizacao extends BaseController {

    private $mParam;
  
    public function __construct()
    {
        
        parent::__construct('Variáveis');
        $this->_moduloName = '<i class="mdi mdi-account-star-outline"></i> Variáveis';
        $this->mParam = model('Variaveis/VariaveisModel');

    }

    public function index($tipo = false)
    {

        parent::VerificaPerfil('VARIAVEIS_APROVACAO');
        $dados['_titulo'] = "Aprovação/Sincronização de requisição";
        $this->_breadcrumb->add($dados['_titulo'], '');

        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

       
        $dados['data_inicio']         = $this->request->getPost('data_inicio');
        $dados['data_fim']            = $this->request->getPost('data_fim');
       
        $dados['situacao']            = $this->request->getPost('situacao');
        if($dados['rh']){
            $dados['historico']  = $this->mParam->historico2($dados['situacao'] );
            $aprovacao ='2';
        }else{
            $dados['historico']  = $this->mParam->historicoGestor($dados['situacao'] );
            $aprovacao ='1';
        }

        $dados['listaSecao']    = $this->mParam->listaSecao();
        $dados['listaFiliais']    = $this->mParam->listaFiliais();
        $dados['listaReqs1']     = $this->mParam->getReq(1, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
       
        $dados['listaReqs2']     = $this->mParam->getReq(2, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
        $dados['listaReqs3']     = $this->mParam->getReq(3, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
       
        $dados['listaReqs4']     = $this->mParam->getReq(4,  $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
        $dados['listaReqs5']     = $this->mParam->getReq(5, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
       
        $dados['listaReqs6']     = $this->mParam->getReq(6, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
        $dados['listaReqs7']     = $this->mParam->getReq(7, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
        $dados['listaReqs8']     = $this->mParam->getReq(8, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
        $dados['listaReqs9']     = $this->mParam->getReq(9, $dados['data_inicio'],   $dados['data_fim'], false,$aprovacao, $dados['situacao'] );
       
         
        $dados['activeTab'] = $tipo;

        return parent::ViewPortal('variaveis/sincronizacao/index', $dados);

    }

    public function save()
    {

        $request    = $this->request->getPost();
        $result     = $this->mParam->saveParametros($request);
        
        if($result){
            exit(responseJson('success', 'Parâmetros salvo com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao salvar parâmetros.'));

    }

  

    public function aprovarGestor()
    {
        $req= false;
        $request = $this->request->getPost();
    
        if (isset($request['idStatus']) && is_array($request['idStatus'])) {
            foreach ($request['idStatus'] as $idStatus) {
                $id = $idStatus['id'];
                $status = $idStatus['status'];
    
                // Verifica se o status é 3 ou 7
                if ( $status == 7) {
                   $req = $id;
                   break;
                    
                } else if ( $status == 2) {
                    // Se necessário, adicione a lógica para outros status aqui
                    $result = $this->mParam->aprovarReqGestor($id,$request['tipo'], $request['justificativa']);
                }else if( $status == 3) {
                    $result = $this->mParam->aprovaRH($id, $request['tipo'], $request['justificativa']);
                }else{
                    $result = $this->mParam->aprovaRHGestor($id, $request['tipo'], $request['justificativa']);
                }
    
               
            }
        } else {
            exit(responseJson('error', 'Erro ao Aprovar requsições.'));
        }
        if($req){
            exit(responseJson('error', 'A requisição.'.$req.' precisa ser sincronizada'));
        }
        exit(responseJson('success', 'Requisição Aprovada com sucesso.'));
    }

    
    public function sincronizaGestor()
    {
        $request = $this->request->getPost();
        $naoSincronizados = [];

        if (isset($request['idStatus']) && is_array($request['idStatus'])) {
            foreach ($request['idStatus'] as $idStatus) {
                $id = $idStatus['id'];
                $status = $idStatus['status'];

              
                if ($status == 7) {
                   
                    $result = $this->mParam->sincReq($id, $request['tipo'], $request['justificativa']);
                } else {
                    
                    $naoSincronizados[] = $id;
                }
            }
        } else {
            exit(responseJson('error', 'Erro ao sincronizar requisições. Verifique se o funcionário não está demitido ou recebendo o benefício.'));
        }

        if (!empty($naoSincronizados)) {
            $idsNaoSincronizados = implode(', ', $naoSincronizados);
            $mensagem = "Os seguintes IDs não foram sincronizados devido ao status: $idsNaoSincronizados.";
            exit(responseJson('warning', $mensagem));
        } else {
            if($result){
                if($request['tipo'] == '1'){
                 if($result == 'Atualizado'){
                    exit(responseJson('success', 'Requisição Sincronizada com sucesso, verifique os dias sincronizados no Log de calculo.'));
                 }
                    exit(responseJson('success', 'Requisição Sincronizada com sucesso.'));
                }
                exit(responseJson('success', 'Requisição Sincronizada com sucesso.'));
            }else{
                exit(responseJson('error', 'Erro ao sincronizar requisições. Verifique se o funcionário não está demitido ou recebendo o benefício'));
            }
           
        }
    }

    

    public function reprovaRH()
    {

        $request    = $this->request->getPost();
       
        if (isset($request['idStatus']) && is_array($request['idStatus'])) {
            foreach ($request['idStatus'] as $idStatus) {
                $id = $idStatus['id'];
                $status = $idStatus['status'];
    
                // Verifica se o status é 3 ou 7
                if ( $status == 7) {
                    // Adicione a lógica que você quer executar quando o status for 3 ou 7
                    $result = $this->mParam->reprovaRH($id, $request['tipo'], $request['justificativa']);
                } else if ( $status == 2) {
                    // Se necessário, adicione a lógica para outros status aqui
                    $result = $this->mParam->reprovarReqGestor($id,$request['tipo'], $request['justificativa']);
 
                }else {
                    $result = $this->mParam->reprovaRH($id, $request['tipo'], $request['justificativa']);
                }
    
               
            }
        } else {
            exit(responseJson('error', 'Erro ao Reprovar requsições.'));
        }
       
     
        if($result){
            exit(responseJson('success', 'Requisição reprovada com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao reprovar requisição.'));

    }
   

}