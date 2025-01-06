<?php
namespace App\Controllers\Integracao;
use App\Controllers\BaseController;


Class Unimed extends BaseController {

    public $mUnimed;
    protected $token;
    protected $usermov;
   
  
    public function __construct()
    {
        
        parent::__construct('Integração'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-cash-usd"></i> Integracao';
        $this->_breadcrumb->add('Unimed', 'integracao/unimed');
        $this->token = 'ZWxkb3JhZG86S0k1MDRLRVNOS1FFVVUySg==';
        $this->usermov = 'RWxkb3JhZG8=';
        $this->mUnimed = model('Integracao/UnimedModal');

    }

    public function index(){
      
        parent::VerificaPerfil('INTEGRACAO_UNIMED_LOG');
        $dados['_titulo'] = "Unimed";
     //   $this->PegaXML();
       // exit;

         
       $dados['data_inicio'] = $this->request->getPost('data_inicio');
       $dados['data_fim'] = $this->request->getPost('data_fim');
       $dados['tp_operação'] = $this->request->getPost('tp_operação');
       $dados['nome'] = $this->request->getPost('nome');
       $dados['cpf'] = $this->request->getPost('CPF');

        $dados['resColoborador']    = $this->mUnimed->Pegalog($dados['data_inicio'], $dados['data_fim'],$dados['tp_operação'], $dados['nome'], $dados['cpf']);
        return parent::ViewPortal('integracao/unimed/log', $dados);

    }
    public function envio(){
       
        parent::VerificaPerfil('INTEGRACAO_UNIMED_ENVIO');
        $dados['_titulo'] = "Unimed | Envio";
        //   $this->PegaXML();
       // exit;
      
      
       $dados['data_inicio'] = $this->request->getPost('data_inicio');
       $dados['data_inicio2'] = $this->request->getPost('data_inicio2');
       $dados['data_fim'] = $this->request->getPost('data_fim');
       $dados['data_fim2'] = $this->request->getPost('data_fim2');
       
       $dados['tp_operação'] = $this->request->getPost('tp_operação');

       ini_set('display_errors', 1);
       error_reporting(E_ALL);

      
       $dados['DadosVaga'] = false;
        if( $dados['data_fim'] &&  $dados['data_inicio'] ){
            $dados['DadosVaga'] = $this->mUnimed->PegaDadosIntegracao( $dados['data_inicio'], $dados['data_fim'], $dados['tp_operação'] );
            $dados['post'] = $this->request->getPost();
        }
        if( $dados['data_fim2'] &&  $dados['data_inicio2'] ){
            $post = $this->request->getPost();
            $this->apr($post);
        }
        return parent::ViewPortal('integracao/unimed/envio', $dados);

    }
    public function apr($dados){
       
   
        $DadosVaga = $this->mUnimed->PegaDadosIntegracao($dados['data_inicio2'], $dados['data_fim2'],$dados['tp_operacao2']);
       
        $this->PegaXML( $DadosVaga,  $dados );

         return;
        

    }

    public function jason($id){
       
      
        $dados   = $this->mUnimed->PegaJson($id);
        echo '<pre>'.$dados[0]['jason'];
      
        exit;

    }

    public function PegaXML($DadosVaga = false, $dados = false){
      
        $key = false;
      
       
      

        foreach ( $dados['idbatida'] as $key => $value) {
            if($DadosVaga[$value]['Cod_Tipo_Operacao'] == '1'){
                $xml= $this->MontaXML($DadosVaga[$value]);
                $retorno = $this->post($xml);
               
                 $this->mUnimed->SalvaLog($retorno, $xml,$DadosVaga[$value]);
                 sleep(1);
                 
            }

            if($DadosVaga[$value]['Cod_Tipo_Operacao'] == '2'){
                $xml= $this->MontaXML2($DadosVaga[$value]);
                $retorno = $this->post($xml);
                $this->mUnimed->SalvaLog($retorno, $xml,$DadosVaga[$value]);
                sleep(1);
            }

            if($DadosVaga[$value]['Cod_Tipo_Operacao'] == '3'){
                $xml= $this->MontaXML3($DadosVaga[$value]);
                $retorno = $this->post($xml);

             
                $this->mUnimed->SalvaLog($retorno, $xml,$DadosVaga[$value]);
                sleep(1);
            }

            if($DadosVaga[$value]['Cod_Tipo_Operacao'] == '5'){
                $xml= $this->MontaXML5($DadosVaga[$value]);
                $retorno = $this->post($xml);
                $this->mUnimed->SalvaLog($retorno, $xml,$DadosVaga[$value]);
                sleep(1);
            }

            if($DadosVaga[$value]['Cod_Tipo_Operacao'] == '6'){
                $xml= $this->MontaXML6($DadosVaga[$value]);
                $retorno = $this->post($xml);
                $this->mUnimed->SalvaLog($retorno, $xml,$DadosVaga[$value]);
                sleep(1);
            }

            if($DadosVaga[$value]['Cod_Tipo_Operacao'] == '7'){
                $xml= $this->MontaXML7($DadosVaga[$value]);
                $retorno = $this->post($xml);
                $this->mUnimed->SalvaLog($retorno, $xml,$DadosVaga[$value]);
                sleep(1);
            }


        }

    }
    public function MontaXML7($Dados){
        $xml ='{
            "movimentacoes": [
            {
            "codTipoRegistro": "2",
            "codContrato": "809",
            "codEmpresa": "41",
            "codTipoOperacao": "'.$Dados["Cod_Tipo_Operacao"].'",
            "codDependencia": "00",
            "dataReativacao": "08/06/2020",
            "numMatriculaEmpresa": "98985585",
            "numCPF": "28639210888"
            }
            ]

           }';
        return $xml;
    }
    public function MontaXML6($Dados){
        $xml ='{
            "movimentacoes": [
            {
            "codTipoRegistro":"'.$Dados["Cod_Tipo_Registro"].'",
            "codContrato": "182947",
            "codEmpresa":"'.$Dados["Cod_Empresa"].'",
            "codTipoOperacao":"'.$Dados["Cod_Tipo_Operacao"].'",
            "codDependencia":"'.$Dados["Data_Reativacao"].'",
            "dataReativacao": "'.$Dados["Data_Reativacao"].'",
            "numMatriculaEmpresa":"'.$Dados["Num_Matricula_Empresa"].'",
            "numCPF": "'.$Dados["Num_CPF"].'"
            }
            ]

           }';
        return $xml;
    }
    public function MontaXML5($Dados){
        $xml ='
        {
            "movimentacoes": [
            {
            "codContrato":"182947",
            "codEmpresa":"'.$Dados["Cod_Empresa"].'",
            "codTipoOperacao":"'.$Dados["Cod_Tipo_Operacao"].'",
            "indLayoutOrigem":"'.$Dados["Ind_Layout_Origem"].'",
            "codFamilia":"'.$Dados["Cod_Familia"].'",
            "codDependencia":"'.$Dados["Cod_Dependencia"].'",
            "nomeCompleto":"'.$Dados["Nome_Completo_Beneficiario"].'",
            "indSexo": "'.$Dados["Ind_Sexo"].'",
            "indEstadoCivil": "'.$Dados["Ind_Estado_Civil"].'",
            "dataNascimento":"'.(($Dados["Data_Nascimento"]) ? date('d/m/Y',strtotime($Dados["Data_Nascimento"])) : false ).'",
            "dataInclusao":"'.(($Dados["Data_Inclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Inclusão"])) : false ).'",
            "dataCasamento": "'.(($Dados["Data_Casamento"]) ? date('d/m/Y',strtotime($Dados["Data_Casamento"])) : false ).'",
            "nomeCompletoMae":"'.$Dados["Nome_Completo_Mae"].'",
            "indCondicaoDependente":"",
            "nomeCidadeResidencia":"",
            "ufResidencia":"",
            "codPaisNascimento":"",
            "codMunicipioNascimentoCorreio":"'.$Dados["Cod_Municipio_Nascimento_(Correio)"].'",
            "codMunicipioNascimentoIbge":"'.$Dados["Cod_Municipio_Nascimento_(IBGE)"].'",
            "numCPF":"'.$Dados["Num_CPF"].'",
            "numPIS":"'.$Dados["Num_PIS"].'",
            "numUnicoSaude": "'.$Dados["Num_Unico_Saude"].'",
            "numIdentidade":"'.$Dados["Num_Identidade"].'",
            "codOrgaoEmissor":"'.$Dados["Cod_Orgao_Emissor"].'",
            "codPaisEmissor":"032",
            "numDeclaracaoNascidoVivo":"",
            "codBancoReembolso": "'.$Dados["Cod_Banco_Reembolso"].'",
            "codAgenciaReembolso":"'.$Dados["Cod_Agencia_Reembolso"].'",
            "numDvAgenciaReembolso":"'.$Dados["Num_DV_Agencia_Reembolso"].'",
            "numContaCorrenteReembolso": "'.$Dados["Num_Conta_Corrente_Reembolso"].'",
            "numDvCcReembolso":"'.$Dados["Num_DV_CC_Reembolso"].'",
            "nomeLogradouro": "'.$Dados["Nome_Logradouro"].'",
            "numEndereco":"'.$Dados["Num_Endereco"].'",
            "complemento":"'.$Dados["TXT_Complemento"].'",
            "nomeBairro":"'.$Dados["Nome_Bairro"].'",
            "codMunicipio":"",
            "numCep":"'.$Dados["Num_CEP"].'",
            "regiao":"sul",
            "ramal":"",
            "endEmail1": "'.$Dados["End_e-Mail"].'",
            "numDddTelefone1":"'.$Dados["Num_DDD_Telefone"].'",
            "numTelefone1":"'.$Dados["Num_Telefone"].'",
            "dddCelular1":"'.$Dados["DDD_Celular"].'",
            "numCelular1": "'.$Dados["Num_Celular"].'",
            "codPlano":"'.$Dados["Cod_Plano"].'",
            "dataTrocaPlano":"",
            "codLotacao":"'.$Dados["Cod_Lotacao"].'",
            "nomeLotacao":"",
            "dataExclusao":"'.(($Dados["Data_Exclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Exclusão"])) : false ).'",
            "dataReativacao":"",
            "dataLotacao":"15/04/2014",
            "dataAdmissao": "'.(($Dados["Data_Admissao"]) ? date('d/m/Y',strtotime($Dados["Data_Admissao"])) : false ).'",
            "codMotivoExclusao":"'.$Dados["Cod_Motivo_Exclusão"].'",
            "codSetor":"4123300",
            "indSituacaoEmpresa":"A",
            "numMatriculaEmpresa":"'.$Dados["Num_Matricula_Empresa"].'",
            "numSequenciaMatriculaEmpresa":"",
            "numMatriculaEmpresaNova":"",
            "numSequencialMatriculaEmpresaNova":"",
            "dataInicioContribuicao":"01/04/2019",
            "codFuncao":"522",
            "codCentroDeCusto":"",
            "nomeCentroDeCusto":"",
            "indSequencialDependenciaInformada":""
         
           }
            ]
           }
        ';
        return $xml;
    }
    public function MontaXML3($Dados){
        $xml ='
        {
            "movimentacoes": [
            
            {
            "identificador": "123456", 
            "dataOperacao": "'.date('d/m/Y H:i:s').'",
            "codContrato": "182947",
            "codEmpresa": "'.$Dados["Cod_Empresa"].'",
            "codTipoOperacao":"'.$Dados["Cod_Tipo_Operacao"].'",
            "codDependencia": "'.$Dados["Cod_Dependencia"].'",
            "numCPF":"'.$Dados["Num_CPF"].'",
            "numMatriculaEmpresa":"'.$Dados["Num_Matricula_Empresa"].'",
            "numSequenciaMatriculaEmpresa": "",
            "numMatriculaEmpresaNova": "",
            "numSequencialMatriculaEmpresaNova": "",
            "nomeCompleto":"'.$Dados["Nome_Completo_Beneficiario"].'",
            "indSexo": "'.$Dados["Ind_Sexo"].'",
            "indEstadoCivil": "'.$Dados["Ind_Estado_Civil"].'",
            "dataNascimento": "'.(($Dados["Data_Nascimento"]) ? date('d/m/Y',strtotime($Dados["Data_Nascimento"])) : false ).'",
            "dataInclusao": "'.(($Dados["Data_Inclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Inclusão"])) : false ).'",
            "dataCasamento":  "'.(($Dados["Data_Casamento"]) ? date('d/m/Y',strtotime($Dados["Data_Casamento"])) : false ).'",
            "nomeCompletoMae": "'.$Dados["Nome_Completo_Mae"].'",
            "indCondicaoDependente": "",
            "nomeCidadeResidencia": "",
            "ufResidencia": "",
            "codPaisNascimento": "",
            "codMunicipioNascimentoCorreio": "'.$Dados["Cod_Municipio_Nascimento_(Correio)"].'",
            "numPIS": "'.$Dados["Num_PIS"].'",
            "numUnicoSaude": "'.$Dados["Num_Unico_Saude"].'",
            "numIdentidade": "'.$Dados["Num_Identidade"].'",
            "codOrgaoEmissor":"'.$Dados["Cod_Orgao_Emissor"].'",
            "codPaisEmissor": "032",
            "numDeclaracaoNascidoVivo": "",
            "codBancoReembolso": "'.$Dados["Cod_Banco_Reembolso"].'",
            "codAgenciaReembolso": "'.$Dados["Cod_Agencia_Reembolso"].'",
            "numDvAgenciaReembolso":"'.$Dados["Num_DV_Agencia_Reembolso"].'",
            "numContaCorrenteReembolso": "'.$Dados["Num_Conta_Corrente_Reembolso"].'",
            "numDvCcReembolso":"'.$Dados["Num_DV_CC_Reembolso"].'",
            "nomeLogradouro":  "'.$Dados["Nome_Logradouro"].'",
            "numEndereco":"'.$Dados["Num_Endereco"].'",
            "complemento":  "'.$Dados["TXT_Complemento"].'",
            "nomeBairro": "'.$Dados["Nome_Bairro"].'",
            "codMunicipio": "",
            "numCep": "'.$Dados["Num_CEP"].'",
            "endEmail1": "'.$Dados["End_e-Mail"].'",
            "numDddTelefone1": "'.$Dados["Num_DDD_Telefone"].'",
            "numTelefone1": "'.$Dados["Num_Telefone"].'",
            "dddCelular1": "'.$Dados["DDD_Celular"].'",
            "numCelular1": "'.$Dados["Num_Celular"].'",
            "codPlano": "'.$Dados["Cod_Plano"].'",
            "dataTrocaPlano": "",
            "codLotacao":"'.$Dados["Cod_Lotacao"].'",
            "nomeLotacao": "",
            "dataExclusao":"'.(($Dados["Data_Exclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Exclusão"])) : false ).'",
            "dataReativacao": "",
            "dataLotacao":"15/04/2014",
            "dataAdmissao": "'.(($Dados["Data_Admissao"]) ? date('d/m/Y',strtotime($Dados["Data_Admissao"])) : false ).'",
            "codMotivoExclusao": "'.$Dados["Cod_Motivo_Exclusão"].'",
            "codSetor": "4123300",
            "indSituacaoEmpresa": "A",
            "dataInicioContribuicao": "01/04/2019",
            "codFuncao": "522",
            "codCentroDeCusto": "",
            "nomeCentroDeCusto": "",
            "indSequencialDependenciaInformada": "",
            "anexos":[
            {
            "nomeArquivo": "teste.pdf",
            "arquivo":""
            
            }]
          
            }
            ]
           }
        
        
        ';
        return $xml;
    }
    public function MontaXML2($Dados){
        $xml ='
        {
            "movimentacoes":[
            {
            "codTipoRegistro":"'.$Dados["Cod_Tipo_Registro"].'",
            "codContrato":"182947",
            "codEmpresa":"'.$Dados["Cod_Empresa"].'",
            "codTipoOperacao":"'.$Dados["Cod_Tipo_Operacao"].'",
            "codFamilia":"'.$Dados["Cod_Familia"].'",
            "codDependencia":"'.$Dados["Cod_Dependencia"].'",
            "nomeCompleto":"'.$Dados["Nome_Completo_Beneficiario"].'",
            "indSexo":"'.$Dados["Ind_Sexo"].'",
            "indEstadoCivil":"'.$Dados["Ind_Estado_Civil"].'",
            "dataNascimento":"'.(($Dados["Data_Nascimento"]) ? date('d/m/Y',strtotime($Dados["Data_Nascimento"])) : false ).'",
            "dataInclusao":"'.(($Dados["Data_Inclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Inclusão"])) : false ).'",
            "dataCasamento": "'.(($Dados["Data_Casamento"]) ? date('d/m/Y',strtotime($Dados["Data_Casamento"])) : false ).'",
            "nomeCompletoMae":"'.$Dados["Nome_Completo_Mae"].'",
            "codPaisNascimento":"",
            "indLayoutOrigem":"'.$Dados["Ind_Layout_Origem"].'",
            "indCartaoResidencia":"",
            "indExtratoResidencia":"",
            "indCondicaoDependente":"",
            "nomeCidadeResidencia":"",
            "ufResidencia":"",
            "tipoAssociadoEspecifico":"",
            "codMunicipioNascimentoCorreio":"'.$Dados["Cod_Municipio_Nascimento_(Correio)"].'",
            "codMunicipioNascimentoIbge": "'.$Dados["Cod_Municipio_Nascimento_(IBGE)"].'",
            "codPlano": "'.$Dados["Cod_Plano"].'",
            "dataTrocaPlano":"",
            "codUnimedLcat":"",
            "codLotacao":"'.$Dados["Cod_Lotacao"].'",
            "nomeLotacao":"",
            "dataExclusao":"'.(($Dados["Data_Exclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Exclusão"])) : false ).'",
            "dataReativacao":"",
            "dataLotacao":"15/04/2014",
            "dataAdmissao": "'.(($Dados["Data_Admissao"]) ? date('d/m/Y',strtotime($Dados["Data_Admissao"])) : false ).'",
            "codMotivoExclusao":"'.$Dados["Cod_Motivo_Exclusão"].'",
            "codSetor":"4123300",
            "indSituacaoEmpresa":"A",
            "indCategoria":"",
            "numMatriculaEmpresa":"'.$Dados["Num_Matricula_Empresa"].'",
            "numSequenciaMatriculaEmpresa":"'.$Dados["Num_Sequencia_Matricula_Empresa"].'",
            "dataInicioContribuicao":"01/04/2020",
            "codFuncao":"522",
            "codCentroDeCusto":"",
            "nomeCentroDeCusto":"",
            "indSequencialDependenciaInformada":"",
            "numMatriculaEmpresaNova":"",
            "numSequencialMatriculaEmpresaNova":"",
            "numCPF":"'.$Dados["Num_CPF"].'",
            "numPIS":"'.$Dados["Num_PIS"].'",
            "numUnicoSaude":"'.$Dados["Num_Unico_Saude"].'",
            "numIdentidade":"'.$Dados["Num_Identidade"].'",
            "codOrgaoEmissor":"'.$Dados["Cod_Orgao_Emissor"].'",
            "codPaisEmissor":"032",
            "numDeclaracaoNascidoVivo":"",
            "codBancoReembolso":"'.$Dados["Cod_Banco_Reembolso"].'",
            "codAgenciaReembolso":"'.$Dados["Cod_Agencia_Reembolso"].'",
            "numDvAgenciaReembolso":"'.$Dados["Num_DV_Agencia_Reembolso"].'",
            "numContaCorrenteReembolso": "'.$Dados["Num_Conta_Corrente_Reembolso"].'",
            "numDvCcReembolso": "'.$Dados["Num_DV_CC_Reembolso"].'",
            "nomeLogradouro": "'.$Dados["Nome_Logradouro"].'",
            "numEndereco":"'.$Dados["Num_Endereco"].'",
            "complemento": "'.$Dados["TXT_Complemento"].'",
            "nomeBairro": "'.$Dados["Nome_Bairro"].'",
            "codMunicipio":"",
            "numCep": "'.$Dados["Num_CEP"].'",
            "numTelefone1":"'.$Dados["Num_Telefone"].'",
            "ramal":"",
            "endEmail1":"'.$Dados["End_e-Mail"].'",
            "numDddTelefone1": "'.$Dados["Num_DDD_Telefone"].'",
            "dddCelular1":"'.$Dados["DDD_Celular"].'",
            "numCelular1": "'.$Dados["Num_Celular"].'"
          
            }]
           }
        ';
        return $xml;
    }
    public function MontaXML($Dados){
       $XML ='
       {
        "movimentacoes": [
        {
        "codContrato": "182947",
        "codEmpresa": "'.$Dados["Cod_Empresa"].'",
        "codTipoOperacao": "'.$Dados["Cod_Tipo_Operacao"].'",
        "codFamilia": "",
        "codDependencia": "00",
        "nomeCompleto": "'.$Dados["Nome_Completo_Beneficiario"].'",
        "indSexo": "'.$Dados["Ind_Sexo"].'",
        "indEstadoCivil": "'.$Dados["Ind_Estado_Civil"].'",
        "dataNascimento": "'.(($Dados["Data_Nascimento"]) ? date('d/m/Y',strtotime($Dados["Data_Nascimento"])) : false ).'",
        "dataInclusao":"'.(($Dados["Data_Inclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Inclusão"])) : false ).'",
        "nomeCompletoMae": "'.$Dados["Nome_Completo_Mae"].'",
        "indCondicaoDependente": "",
        "nomeCidadeResidencia":  "'.$Dados["Nome_Cidade_Residencia"].'",
        "ufResidencia":  "'.$Dados["UF_Residencia"].'",
        "codPaisNascimento": "'.$Dados["Cod_Pais_Nascimento"].'",
        "codMunicipioNascimentoCorreio": "'.$Dados["Cod_Municipio_Nascimento_(Correio)"].'",
        "codMunicipioNascimentoIbge": "'.$Dados["Cod_Municipio_Nascimento_(IBGE)"].'",
        "numCPF": "'.$Dados["Num_CPF"].'",
        "numPIS": "'.$Dados["Num_PIS"].'",
        "numUnicoSaude": "'.$Dados["Num_Unico_Saude"].'",
        "numIdentidade": "'.$Dados["Num_Identidade"].'",
        "codOrgaoEmissor":"'.$Dados["Cod_Orgao_Emissor"].'",
        "codPaisEmissor": "032",
        "numDeclaracaoNascidoVivo": "'.$Dados["Num_Declaracao_Nascido_Vivo"].'",
        "codBancoReembolso":"'.$Dados["Cod_Banco_Reembolso"].'",
        "codAgenciaReembolso": "'.$Dados["Cod_Agencia_Reembolso"].'",
        "numDvAgenciaReembolso": "'.$Dados["Num_DV_Agencia_Reembolso"].'",
        "numContaCorrenteReembolso": "'.$Dados["Num_Conta_Corrente_Reembolso"].'",
        "numDvCcReembolso": "'.$Dados["Num_DV_CC_Reembolso"].'",
       "nomeLogradouro":  "'.$Dados["Nome_Logradouro"].'",
        "numEndereco":  "'.$Dados["Num_Endereco"].'",
        "complemento": "'.$Dados["TXT_Complemento"].'",
        "nomeBairro":  "'.$Dados["Nome_Bairro"].'",
        "codMunicipio": "",
        "numCep": "'.$Dados["Num_CEP"].'",
        "regiao": "sul",
        "ramal": "",
        "endEmail": "teste@hotmail.com",
        "numDddTelefone":  "'.$Dados["Num_DDD_Telefone"].'",
        "numTelefone":  "'.$Dados["Num_Telefone"].'",
        "dddCelular":  "'.$Dados["DDD_Celular"].'",
        "numCelular": "'.$Dados["Num_Celular"].'",
        "codPlano":  "'.$Dados["Cod_Plano"].'",
        "dataTrocaPlano": "",
        "codLotacao": "'.$Dados["Cod_Lotacao"].'",
        "nomeLotacao": "",
        "dataExclusao": "'.(($Dados["Data_Exclusão"]) ? date('d/m/Y',strtotime($Dados["Data_Exclusão"])) : false ).'",
        "dataReativacao": "",
        "dataLotacao":"15/04/2014",
        "dataAdmissao":  "'.(($Dados["Data_Admissao"]) ? date('d/m/Y',strtotime($Dados["Data_Admissao"])) : false ).'",
        "codMotivoExclusao": "'.$Dados["Cod_Motivo_Exclusão"].'",
        "codSetor": "",
        "indSituacaoEmpresa": "A",
        "numMatriculaEmpresa": "'.$Dados["Num_Matricula_Empresa"].'",
        "numSequenciaMatriculaEmpresa": "'.$Dados["Num_Sequencia_Matricula_Empresa"].'",
        "numMatriculaEmpresaNova": "",
        "numSequencialMatriculaEmpresaNova": "",
        "dataInicioContribuicao": "30/06/2020",
        "codFuncao": "",
        "codCentroDeCusto": "",
        "nomeCentroDeCusto": "",
        "indSequencialDependenciaInformada": ""
        }
        ]
       }
       ';
       return $XML;
       

    }



    public function post($data){

        $curl = curl_init();
   
        curl_setopt_array($curl, array( 
            CURLOPT_URL => "https://api.centralnacionalunimed.com.br/its/v1/movimentacao/beneficiario",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                'content-type: application/json',
                "Authorization: Basic ".$this->token."",
                "user-mov: ".$this->usermov
               
            ),
        ));
      
        $response = json_decode(curl_exec($curl), true);
        
        if (curl_errno($curl)) { 
            print curl_error($curl); 
        } 
        curl_close($curl);
        
      
        return $response;

    }

   

}