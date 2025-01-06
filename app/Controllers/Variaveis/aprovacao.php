<?php
namespace App\Controllers\Variaveis;
use App\Controllers\BaseController;


Class Aprovacao extends BaseController {

    private $mParam;
  
    public function __construct()
    {
        
        parent::__construct('Variáveis');
        $this->_moduloName = '<i class="mdi mdi-account-star-outline"></i> Variáveis';
        $this->mParam = model('Variaveis/VariaveisModel');

    }

    public function index()
    {

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
      
        parent::VerificaPerfil('VARIAVEIS_APROVACAO');
        $dados['_titulo'] = "Aprovação de requisição";
        $this->_breadcrumb->add($dados['_titulo'], '');

        
        $dados['listaSecao']    = $this->mParam->listaSecao();
        $dados['listaFiliais']    = $this->mParam->listaFiliais();

        $dados['listaReqs4']     = $this->mParam->getReq(4, false, false, false,1);
       
        $dados['listaReqs6']     = $this->mParam->getReq(6,false, false, false,1);
       
        
        return parent::ViewPortal('variaveis/aprovacao/index', $dados);

    }

    public function historico()
    {
        try {
            $request = $this->request->getPost();
            $historico = $this->mParam->historico($request['id']);
            
            $json = [];
            if ($historico) {
                foreach ($historico as $Historico) {
                    $status = '';
                    switch ($Historico->nivel_apr_area) {
                        case '0':
                            $status = '<span class="badge badge-info ">Enviado para Aprovação</span>'; 
                            $gestor = '';
                            $solicitante = $Historico->nome;
                        break;
                        case '2': $status = '<span class="badge badge-success">Aprovado Gestor</span>';
                            $gestor =  $Historico->nome;
                            $solicitante = ''; 
                        break;
                        case '8': $status = '<span class="badge badge-success">Preenchido RH</span>';
                            $gestor =  $Historico->nome;
                            $solicitante = ''; 
                        break;
                        case '9': $status = '<span class="badge badge-success">Documento Anexado</span>';
                            $gestor =  $Historico->nome;
                            $solicitante = ''; 
                        break;
                        case '7': $status = '<span class="badge badge-success">Aprovado RH</span>';
                            $gestor =  $Historico->nome;
                            $solicitante = ''; 
                        break;
                        case '3': $status = '<span class="badge badge-primary">Sincronizado</span>'; 
                            $gestor =  $Historico->nome;
                            $solicitante = ''; 
                        break;
                        case '5': $status = '<span class="badge badge-danger">Reprovado Gestor</span>';
                            $gestor =  $Historico->nome;
                            $solicitante = '';  
                        break;
                        case '6': $status = '<span class="badge badge-danger">Reprovado RH</span>';
                            $gestor =  $Historico->nome;
                            $solicitante = '';  
                        break;
                        default: $status = '<span class="badge badge-info">--</span>'; 
                        break;
                    }

                    $json[] = [
                        'data'       => date('d/m/Y', strtotime($Historico->dtcad)),
                        'dataEnvio'  => $Historico->dtenvio ? date('d/m/Y H:i:s', strtotime($Historico->dtenvio)) : '-', 
                        'acao'       => $status,
                        'req'     => $Historico->id_requisicao, 
                        'gestor'     => $gestor,
                        'solicitante' => $solicitante,
                        'comentario' => $Historico->observacao,
                    ];
                }
            }
            
            // Converte o array em JSON
            return $this->response->setJSON($json);

        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON vazia
            return $this->response->setJSON([]);
        }
    }
    public function logCalculo()
    {
        try {
            $request = $this->request->getPost();
            
            $historico = $this->mParam->logCalculo($request['id']);
           
            $json = [];
            if ($historico) {
                if($historico[0]->tipo == '5'){
                    foreach ($historico as $Historico) {
                        $status = '';
                        $valor = json_decode($Historico->valores);
                        
                        $json[] = [
                            'data'    => date('d/m/Y', strtotime($Historico->dtcad)),
                            'req'     => $Historico->id_req, 
                            'usuário'     => $Historico->nome,
                            'mes'     => $valor->mes,
                            'ano'     =>  $valor->ano,
                            'novoMes'     =>  $valor->novoMes,
                            'novoAno'     =>  $valor->novoAno,
                            'tipo'     =>  $Historico->tipo,
                            'periodo'     =>  $Historico->periodo,
                            'val'     =>  number_format($valor->val, 2, '.', '')
                        ];
                    }
                }else{
                    foreach ($historico as $Historico) {
                        $status = '';
                        $valor = json_decode($Historico->valores);
                        
                        $json[] = [
                            'data'       => date('d/m/Y', strtotime($Historico->dtcad)),
                            'req'     => $Historico->id_req, 
                            'usuário'     => $Historico->nome,
                            'dias_referencia'     => $valor->dias_referencia,
                            'data_inicio_referencia'     =>  date('d/m/Y',strtotime($valor->data_inicio_referencia)),
                            'data_fim_referencia'     =>  date('d/m/Y',strtotime($valor->data_fim_referencia)),
                            'tipo'     =>  $Historico->tipo,
                            'val'     =>  number_format($valor->val, 2, '.', '')
                        ];
                    }

                }
            }
            
            // Converte o array em JSON
            return $this->response->setJSON($json);

        } catch (\Exception $e) {
            // Em caso de erro, retorne uma resposta JSON vazia
            return $this->response->setJSON([]);
        }
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

    public function aprovar()
    {

        $request    = $this->request->getPost();
      
        if (isset($request['tipo'])  && $request['tipo'] == '5') {
            $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
            $request['situacao'] = '8';
           
        }else{
            $dados['rh'] = false;
            $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados, false,false,'3');
            
            if($dados['resFuncionarioSecao']){
                $request['situacao'] = '3';
            }
          

        }

     
      
        $result     = $this->mParam->aprovarReq($request['id'], $request['situacao']);
     
        if($result){
            exit(responseJson('success', 'Requisição enviada para aprovação.'));
        }
        
        exit(responseJson('error', 'Falha ao Aprovada requisição.'));

    }

    public function aprovar_em_lote()
    {

        $request    = $this->request->getPost();

        if (isset($request['tipo'])  && $request['tipo'] == '5') {
            $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
            $request['situacao'] = '8';
        }else{
            $dados['rh'] = false;
            $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados, false,false,'3');
            
            if($dados['resFuncionarioSecao']){
                $request['situacao'] = '3';
            }
           

        }
        $result     = $this->mParam->aprovarReq($request['ids'], $request['situacao']);
     
        if($result){
            exit(responseJson('success', 'Requisição enviada para aprovação.'));
        }
        
        exit(responseJson('error', 'Falha ao enviar a requisição.'));

    }

    public function reprovaGestor()
    {

        $request    = $this->request->getPost();
       
        if(is_array($request['id'])){
            foreach($request['id'] as $id){
                $result     = $this->mParam->reprovarReqGestor($id,$request['tipo'], $request['justificativa']);
            }
        }else{
            
            $result     = $this->mParam->reprovarReqGestor($request['id'],$request['tipo'], $request['justificativa']);
        }
       
       
     
        if($result){
            exit(responseJson('success', 'Requisição reprovada com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao aprovar requisição.'));

    }

    public function aprovarGestor()
    {

        $request    = $this->request->getPost();
       
        if(is_array($request['id'])){
            foreach($request['id'] as $id){
                $result     = $this->mParam->aprovarReqGestor($id,$request['tipo'], $request['justificativa']);
            }
        }else{
            
            $result     = $this->mParam->aprovarReqGestor($request['id'],$request['tipo'], $request['justificativa']);
        }
       
       
     
        if($result){
            exit(responseJson('success', 'Requisição aprovada com sucesso.'));
        }
        
        exit(responseJson('error', 'Falha ao aprovar requisição.'));

    }
   

}