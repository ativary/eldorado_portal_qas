<?php
namespace App\Controllers\Variaveis;
use App\Controllers\BaseController;
use Ramsey\Uuid\Uuid;
use \Mpdf\Mpdf;

Class Substituicao extends BaseController {

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
        parent::VerificaPerfil('VARIAVEIS_SUBSTITUICAO');
        $dados['_titulo'] = "Salário  Substituição";
        $this->_breadcrumb->add($dados['_titulo'], '');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['data_inicio']         = $this->request->getPost('data_inicio');
        $dados['data_fim']            = $this->request->getPost('data_fim');
        $dados['secao']            = $this->request->getPost('secao');
        $dados['funcionario']         = $this->request->getPost('funcionario');
        $dados['situacao']            = $this->request->getPost('situacao');

        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
        $dados['listaEventos']  = $this->mParam->listaEventos();
      
        $dados['listaReqs']     = $this->mParam->getReq(1, $dados['data_inicio'],$dados['data_fim'],$dados['funcionario'],false, $dados['situacao'] );
       
        $dados['listaSecao']    = $this->mParam->listaSecaoGestor( $dados['rh']);
       
        
        return parent::ViewPortal('variaveis/substituicao/index', $dados);

    }

    public function novo(){
        parent::VerificaPerfil('VARIAVEIS_SUBSTITUICAO');
        $dados['_titulo'] = "Novo Salário Substituição";
     
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
        $dados['chapaFunc'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

        $dados['chapaFunc'] = ''.$dados['chapaFunc'].'';
        $dados['resFuncionarioSub']  = $this->mParam->ListarFuncionariosSecao('all', $dados, false, false,1);

       
        $dados['param6']   = json_encode($this->mParam->getParametros(1));
        $this->_breadcrumb->add('Novo Salário Substituição');

        return parent::ViewPortal('variaveis/substituicao/novo', $dados);
    }
    
    public function substituicaoTermo()
    {
        $id = $this->request->getVar('id'); // Pega o valor de 'id' no GET ou POST

        $meses = [

            'January' => 'janeiro',
            'February' => 'fevereiro',
            'March' => 'março',
            'April' => 'abril',
            'May' => 'maio',
            'June' => 'junho',
            'July' => 'julho',
            'August' => 'agosto',
            'September' => 'setembro',
            'October' => 'outubro',
            'November' => 'novembro',
            'December' => 'dezembro'
        ];

        $mesAtual = date('F');

        $dados['mes'] = $meses[$mesAtual];
        $dados['empresa']    = $this->mParam->DadosEmpresa();
        $dados['req']     = $this->mParam->getReqDados( $id);
        $dados['valores']     = json_decode($dados['req'][0]->valores) ;
        $dados['func_sub']    = $this->mParam->dadosFunc($dados['valores']->funcionario_sub);
        $dados['func']    = $this->mParam->dadosFunc($dados['valores']->funcionario);
        $dados['filial']    = $this->mParam->DadosFilial(   $dados['func'][0]['CODFILIAL'] );
     
        $html = view('variaveis/substituicao/termo',  $dados);
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P'
        ]);

      
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S'); // 'S' faz o Output para string (não download ou inline)

         // Retornar o PDF como resposta em binário
         return $this->response
         ->setHeader('Content-Type', 'application/pdf')
         ->setHeader('Content-Disposition', 'attachment; filename="termo_de_substituicao.pdf"')
         ->setBody($pdfContent)
         ->send(); // Use send() para garantir o envio da resposta binária
    }

    public function editar($id){
        $id = base64_decode($id);
     
        $dados['_titulo'] = "Editar Salário  Substituição";
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        
        $dados['listaSecao']    = $this->mParam->listaSecaoGestor( $dados['rh']);
       
        $dados['resFuncionarioSecao'] = $this->mParam->ListarFuncionariosSecao('all', $dados);
    
        $dados['req']     = $this->mParam->getReqDados($id);
        $dados['chapaFunc'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dados['param6']   = json_encode($this->mParam->getParametros(1));
        $dados['valores']     = json_decode($dados['req'][0]->valores) ;
    
        $this->_breadcrumb->add('Editar Salário  Substituição');

        return parent::ViewPortal('variaveis/substituicao/update', $dados);
    }
    public function update(){
      
        $request    = $this->request->getPost();

        // Definição de variáveis de datas usando strtotime
        $dataInicio = strtotime($request['data_inicio']);
        $dataFim = strtotime($request['data_fim']);

        // Inicializar as variáveis de referência
        $dataInicioReferencia = $dataInicio;
        $dataFimReferencia = $dataFim;

        // Calcular a diferença em dias entre as datas
        $diferencaDias = floor(($dataFim - $dataInicio) / (60 * 60 * 24)) + 1;

        if (date('Y-m', $dataInicio) === date('Y-m', $dataFim)) {
            
            $request['data_inicio_referencia'] = date('Y-m-d', $dataInicio);
            $request['data_fim_referencia'] = date('Y-m-d', $dataFim);
            $request['dias_referencia'] = $diferencaDias;

            $diferencaDiasReferencia = floor(($dataFim - $dataInicio) / (60 * 60 * 24)) + 1;

            $diasNoMes = date('t', $dataInicio);
          
            if ($diferencaDiasReferencia >= $diasNoMes) {
            
                $request['dias_referencia'] = 30;
            } else {
            
                $request['dias_referencia'] = $diferencaDiasReferencia;
            }
        } else {
        
            $dataFimReferencia = mktime(0, 0, 0, date('m', $dataInicio) + 1, 0, date('Y', $dataInicio));

            
            $diferencaDiasReferencia = floor(($dataFimReferencia - $dataInicio) / (60 * 60 * 24)) + 1;

            
            $diasNoMes = date('t', $dataInicio);
            if ($diferencaDiasReferencia >= $diasNoMes) {
            
                $request['dias_referencia'] = 30;
            } else {
            
                $request['dias_referencia'] = $diferencaDiasReferencia;
            }

            $request['data_inicio_referencia'] = date('Y-m-d', $dataInicio);
            $request['data_fim_referencia'] = date('Y-m-d', $dataFimReferencia);
        }
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
      
        // Definição de variáveis de datas usando strtotime
        $dataInicio = strtotime($request['data_inicio']);
        $dataFim = strtotime($request['data_fim']);

        // Inicializar as variáveis de referência
        $dataInicioReferencia = $dataInicio;
        $dataFimReferencia = $dataFim;

        // Calcular a diferença em dias entre as datas
        $diferencaDias = floor(($dataFim - $dataInicio) / (60 * 60 * 24)) + 1;

        if (date('Y-m', $dataInicio) === date('Y-m', $dataFim)) {
            
            $request['data_inicio_referencia'] = date('Y-m-d', $dataInicio);
            $request['data_fim_referencia'] = date('Y-m-d', $dataFim);
            $request['dias_referencia'] = $diferencaDias;

                 
            $diferencaDiasReferencia = floor(($dataFim - $dataInicio) / (60 * 60 * 24)) + 1;

            $diasNoMes = date('t', $dataInicio);
          
            if ($diferencaDiasReferencia >= $diasNoMes) {
            
                $request['dias_referencia'] = 30;
            } else {
            
                $request['dias_referencia'] = $diferencaDiasReferencia;
            }
        } else {
        
            $dataFimReferencia = mktime(0, 0, 0, date('m', $dataInicio) + 1, 0, date('Y', $dataInicio));

            
            $diferencaDiasReferencia = floor(($dataFimReferencia - $dataInicio) / (60 * 60 * 24)) + 1;

            
            $diasNoMes = date('t', $dataInicio);
          
            if ($diferencaDiasReferencia >= $diasNoMes) {
            
                $request['dias_referencia'] = 30;
            } else {
            
                $request['dias_referencia'] = $diferencaDiasReferencia;
            }

            $request['data_inicio_referencia'] = date('Y-m-d', $dataInicio);
            $request['data_fim_referencia'] = date('Y-m-d', $dataFimReferencia);
        }
    
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
        $result = $this->mParam->ListarFuncionariosSecao($request['codigo'], $dados, false, false,1);

       
        if($result){
            echo json_encode($result);
            exit;
        }
        
        exit(responseJson('error', 'Nenhum funcionário localizado para essa seção.'));

    }
   

}