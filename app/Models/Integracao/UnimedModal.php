<?php
namespace App\Models\Integracao;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class UnimedModal extends Model {

    protected $dbportal;
   
    protected $dbrm;
    public $coligada;
    public $log_id;
    public $now;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
      
        $this->dbrm     = db_connect('dbrm');
        $this->coligada = session()->get('func_coligada');
        $this->log_id = session()->get('log_id');
		$this->now = date('Y-m-d H:i:s');
    }
    public function SalvaLog($retorno, $xml,$Dados){
        $nAssociado='';
        $mensagem ='';
        $tipo ='';
        if($retorno['statusCode']== '500'){
            echo $retorno['message'] ." - ".$retorno['activityId'] ;
            exit;
        }
        $sucesso = array_key_exists("numAssociado", $retorno['movimentacoes'][0]);
        if($sucesso){
          $nAssociado = $retorno['movimentacoes'][0]['numAssociado'];
        }
        if(isset($retorno['movimentacoes'][0]['numSeqAssociadoMov'])){
            $nAssociado = $retorno['movimentacoes'][0]['numSeqAssociadoMov'];
        }
       
        $sucesso = array_key_exists("listMensagens",  $retorno['movimentacoes'][0]);
       
        if($sucesso){
           
            if(isset($retorno['movimentacoes'][0]['listMensagens'][0]['txtComplementoMsg'])){
                $mensagem =  $retorno['movimentacoes'][0]['listMensagens'][0]['txtComplementoMsg'];
            }else{
                $mensagem =  $retorno['movimentacoes'][0]['listMensagens'][0]['txtMensagem'];
            }
            
            if(isset($retorno['movimentacoes'][0]['listMensagens'][0]['tipoMensagem'])){
                $tipo = $retorno['movimentacoes'][0]['listMensagens'][0]['tipoMensagem'];
            }
           
          }

          if($Dados["Cod_Tipo_Operacao"]){
            
            switch ($Dados["Cod_Tipo_Operacao"]) {
                case '1':
                    $operacao = '1-Inclusão Titular';
                    break;
                // Você pode adicionar mais casos aqui para outras situações
                case '2':
                    $operacao = '2-Inclusão de Dependente';
                    break;
                case '3':
                    $operacao = '3-Alteração Cadastral';
                    break;
                case '5':
                    $operacao = '5-Troca de plano';
                    break;
                case '6':
                    $operacao = '6-reativacao';
                    break;
                case '7':
                    $operacao = '7-Exclusão';
                    break;
               
            }

          }
          

        $query_insert = " INSERT INTO dbo.ZCRMPORTAL_UNIMED (codcoligada,Nome, chapa, pis, cpf, dtcad, dtmudanca, indice, tipo, cod_operacao, sincronizado, dtsincronizado, erro_unimed)
        VALUES ('".$Dados["Cod_Empresa"]."','".$Dados["Nome_Completo_Beneficiario"]."' ,NULL, '".$Dados["Num_PIS"]."','".$Dados["Num_CPF"]."', '".date('Y-m-d H:i:s')."',Null, '".$nAssociado."','".$tipo."', '". $operacao."',Null, Null,'". $mensagem."' )";

     
        $this->dbportal->query($query_insert);

        $select_max = "SELECT MAX(id) id from ZCRMPORTAL_UNIMED";

        $result = $this->dbportal->query(  $select_max);

        $max = $result->getResultArray() ;
      
        $query_json = " INSERT INTO dbo.ZCRMPORTAL_UNIMED_XML (id_log, jason)
        VALUES ('"  .$max[0]['id'] ."','" . $xml."')";
     
        $this->dbportal->query($query_json);

        return true;
    }

    public function Pegalog($dtini = false, $dtfim = false, $tipo=false, $nome = false, $cpf = false){

        $filtro_cpf = false;
        $filtro_data= false;
        $filtro_nome = false;
        $filtro_tipo = false;

        if($dtini && $dtfim ){
            $filtro_data = " AND A.dtcad BETWEEN '".$dtini."' AND '".$dtfim."'";
        }
        if($nome){
            $filtro_nome = "AND CONVERT(VARCHAR, A.Nome)  ='".$nome."'";

        }
        if($tipo){
            $filtro_tipo = "AND A.cod_operacao ='".$tipo."'";

        }
        if($cpf){
            $filtro_cpf = "AND A.cpf ='".$cpf."'";

        }

        $query = "SELECT A.*, B.jason, B.id id_json FROM ZCRMPORTAL_UNIMED A, ZCRMPORTAL_UNIMED_XML B WHERE A.id = B.id_log ".$filtro_cpf. $filtro_tipo. $filtro_nome. $filtro_data."  order by A.id  ";
       // echo  $query;
       //  exit();
        $result = $this->dbportal->query($query);

        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    public function PegaJson($id){

        $query = "SELECT * FROM  ZCRMPORTAL_UNIMED_XML  WHERE id = '".$id."'   ";
        //echo $query_insert;
        // exit();
        $result = $this->dbportal->query($query);

        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function PegaDadosIntegracao($dtini= false, $dtfim = false, $codOpe = false){

        if(!$dtini){
            $dtini =strtotime(date("Y-m-d")."-1 day");
        }
        if(!$dtfim){
            $dtfim = date('Y-m-d');
        }
        $filtro_op = false;
        if($codOpe){
            $filtro_op = "WHERE cod_tipo_operacao ='".$codOpe."' " ;
        }

        $query = "
        declare	 @DataIni datetime
		        ,@DataFim datetime

        set @DataIni = '".date("Y-m-d",strtotime($dtini))."'
        set @DataFim = '".date("Y-m-d",strtotime($dtfim))."'
        select * from (
        /*				---01o - ADMISSÃO - INCLUSÃO DE TITULAR---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= '00'
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= '00'
                ,[Data_Inclusão]							= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Data_Admissao]							= fun.dataadmissao
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= fun.nome
                ,[Data_Nascimento]							= pes.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= pes.sexo
                ,[Ind_Estado_Civil]							= case when pes.estadocivil = 'C' then 'C'
                                                                when pes.estadocivil = 'D' then 'D'
                                                                when pes.estadocivil = 'I' then 'I'
                                                                when pes.estadocivil = 'A' then 'S'
                                                                when pes.estadocivil = 'O' then 'S'
                                                                when pes.estadocivil = 'P' then 'S'
                                                                when pes.estadocivil = 'S' then 'S'
                                                                when pes.estadocivil = 'E' then 'U'
                                                                when pes.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= mae.nome
                ,[Num_CPF]									= replace(replace(replace(replace(cast(pes.cpf as varchar(20)),'.',''),'-',''),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= pes.rua
                ,[Num_Endereco]								= pes.numero
                ,[TXT_Complemento]							= pes.complemento
                ,[Nome_Bairro]								= pes.bairro
                ,[Num_CEP]									= replace(pes.cep,'-','')
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= case when len(pes.telefone1) > 8 then left(pes.telefone1,2)
                                                                else null
                                                            end
                ,[Num_Telefone]								= case when len(pes.telefone1) > 8 then substring(pes.telefone1,3,20)
                                                                else null
                                                            end
                ,[Ramal]									= null
                ,[DDD_Celular]								= case when len(pes.telefone2) > 8 then left(pes.telefone2,2)
                                                                else null
                                                            end
                ,[Num_Celular]								= case when len(pes.telefone2) > 8 then substring(pes.telefone2,3,20)
                                                                else null
                                                            end
                ,[End_e-Mail]								= pes.email
                ,[Cod_Banco_Reembolso]						= fun.codbancopagto
                ,[Cod_Agencia_Reembolso]					= fun.codagenciapagto
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= substring(replace(replace(fun.contapagamento,'-',''),'.',''),1,(len(fun.contapagamento)-2))
                ,[Num_DV_CC_Reembolso]						= right(fun.contapagamento,1)
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.dataadmissao between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	fun.tipoadmissao not in ('E','T')
        and	fun.codsituacao not in ('D')

        union all

        /*				---02o - ADMISSÃO - INCLUSÃO DE CONJUGE---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Data_Inclusão]							= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('5','C')
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.dataadmissao between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	dep.incassistmedica = 1
        and	fun.tipoadmissao not in ('E','T')
        and	fun.codsituacao not in ('D')

        union all

        /*				---03o - ADMISSÃO - INCLUSÃO DE DEPENDENTES---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= case when dep.grauparentesco = '3' then 'I'
                                                                else null
                                                            end
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Data_Inclusão]							= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('1','D','T','3')
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.dataadmissao between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	dep.incassistmedica = 1
        and  ((datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 21   
        and   (dep.universitario = 0
        or	dep.universitario is null)
        or   (datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 24
        and	dep.universitario = 1)))
        and	fun.tipoadmissao not in ('E','T')
        and	fun.codsituacao not in ('D')

        union all

        /*				---04o - POSTERIOR - INCLUSÃO DE TITULAR---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= '00'
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= '00'
                ,[Data_Inclusão]							= getdate()
                ,[Data_Admissao]							= fun.dataadmissao
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= fun.nome
                ,[Data_Nascimento]							= pes.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= pes.sexo
                ,[Ind_Estado_Civil]							= case when pes.estadocivil = 'C' then 'C'
                                                                when pes.estadocivil = 'D' then 'D'
                                                                when pes.estadocivil = 'I' then 'I'
                                                                when pes.estadocivil = 'A' then 'S'
                                                                when pes.estadocivil = 'O' then 'S'
                                                                when pes.estadocivil = 'P' then 'S'
                                                                when pes.estadocivil = 'S' then 'S'
                                                                when pes.estadocivil = 'E' then 'U'
                                                                when pes.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= mae.nome
                ,[Num_CPF]									= replace(replace(cast(pes.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= pes.rua
                ,[Num_Endereco]								= pes.numero
                ,[TXT_Complemento]							= pes.complemento
                ,[Nome_Bairro]								= pes.bairro
                ,[Num_CEP]									= replace(pes.cep,'-','')
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= case when len(pes.telefone1) > 8 then left(pes.telefone1,2)
                                                                else null
                                                            end
                ,[Num_Telefone]								= case when len(pes.telefone1) > 8 then substring(pes.telefone1,3,20)
                                                                else null
                                                            end
                ,[Ramal]									= null
                ,[DDD_Celular]								= case when len(pes.telefone2) > 8 then left(pes.telefone2,2)
                                                                else null
                                                            end
                ,[Num_Celular]								= case when len(pes.telefone2) > 8 then substring(pes.telefone2,3,20)
                                                                else null
                                                            end
                ,[End_e-Mail]								= pes.email
                ,[Cod_Banco_Reembolso]						= fun.codbancopagto
                ,[Cod_Agencia_Reembolso]					= fun.codagenciapagto
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= substring(replace(replace(fun.contapagamento,'-',''),'.',''),1,(len(fun.contapagamento)-2))
                ,[Num_DV_CC_Reembolso]						= right(fun.contapagamento,1)
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	dbo.pfunc fun
        join	dbo.ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join dbo.pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	totvsaudit.pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	totvsaudit.zauditchanges data
        on	data.auditid = cpl.auditid
        join	totvsaudit.pfcompl cpl_org
        on	cpl_org.auditid = cpl.auditid
            and	cpl_org.parentlogid = cpl.logid
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	data.auditdate between @DataIni and @DataFim
        and	eomonth(fun.dataadmissao) < eomonth(@DataIni)
        and   (cpl.medica_cnu is not null
        and	cpl_org.medica_cnu is null)
        and	fun.tipoadmissao not in ('E','T')
        and	fun.codsituacao not in ('D')

        union all

        /*				---05o - POSTERIOR - INCLUSÃO DE CONJUGE---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Data_Inclusão]							= getdate()
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	totvsaudit.pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	totvsaudit.zauditchanges data
        on	data.auditid = cpl.auditid
        join	totvsaudit.pfcompl cpl_org
        on	cpl_org.auditid = cpl.auditid
            and	cpl_org.parentlogid = cpl.logid
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('5','C')
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	data.auditdate between @DataIni and @DataFim
        and	eomonth(fun.dataadmissao) < eomonth(@DataIni)
        and   (cpl.medica_cnu is not null
        and	cpl_org.medica_cnu is null)
        and	dep.incassistmedica = 1
        and	fun.tipoadmissao not in ('E','T')
        and	fun.codsituacao not in ('D')

        union all

        /*				---06o - POSTERIOR - INCLUSÃO DE DEPENDENTES---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= case when dep.grauparentesco = '3' then 'I'
                                                                else null
                                                            end
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Data_Inclusão]							= getdate()
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	totvsaudit.pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	totvsaudit.zauditchanges data
        on	data.auditid = cpl.auditid
        join	totvsaudit.pfcompl cpl_org
        on	cpl_org.auditid = cpl.auditid
            and	cpl_org.parentlogid = cpl.logid
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('1','D','T','3')
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	data.auditdate between @DataIni and @DataFim
        and	eomonth(fun.dataadmissao) < eomonth(@DataIni)
        and	fun.datademissao is null
        and   (cpl.medica_cnu is not null
        and	cpl_org.medica_cnu is null)
        and	dep.incassistmedica = 1
        and  ((datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 21   
        and   (dep.universitario = 0
        or	dep.universitario is null)
        or   (datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 24
        and	dep.universitario = 1)))
        and	fun.tipoadmissao not in ('E','T')
        and	fun.codsituacao not in ('D')
        
        union all

        /*				---07o - POSTERIOR - INCLUSÃO DE CONJUGE EXCLUSIVO---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco in ('5') and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco in ('5') and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco in ('5') and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco in ('5') and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Data_Inclusão]							= getdate()
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	totvsaudit.pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('5','C')
        join	totvsaudit.pfdepend dep_org
        on	dep_org.auditid = dep.auditid
            and	dep_org.parentlogid = dep.logid
        join	totvsaudit.zauditchanges dep_data
        on	dep_data.auditid = dep.auditid
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend

        where	dep_data.auditdate between @DataIni and @DataFim
        and	fun.dataadmissao < @DataIni
        and  ((dep_org.incassistmedica is null
        or	dep_org.incassistmedica = '0')
        and	dep.incassistmedica = '1')
        and	dep.incassistmedica = 1
        and	cpl.medica_cnu is not null
        and	fun.tipoadmissao not in ('E','T')
        and not exists (select	*
                        from	totvsaudit.pfcompl taudit
                        join	totvsaudit.pfcompl taudit_org
                        on	taudit_org.auditid = taudit.auditid
                            and	taudit_org.parentlogid = taudit.logid
                        join	totvsaudit.zauditchanges taudit_data
                        on	taudit_data.auditid = taudit.auditid
                        where	taudit.codcoligada = fun.codcoligada
                        and	taudit.chapa = fun.chapa
                        and	taudit_org.medica_cnu <> taudit.medica_cnu
                        and	taudit_data.auditdate between  @DataIni and @DataFim)
        and	fun.codsituacao not in ('D')

        union all

        /*				---08o - POSTERIOR - INCLUSÃO DE CONJUGE NOVO EXCLUSIVO---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Data_Inclusão]							= getdate()
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	totvsaudit.pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('5','C')
        join	totvsaudit.zauditchanges dep_data
        on	dep_data.auditid = dep.auditid
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend

        where	dep_data.auditdate between @DataIni and @DataFim
        and	fun.dataadmissao < @DataIni
        and	fun.tipoadmissao not in ('E','T')
        and	dep.auditaction = 'I'
        and	dep.incassistmedica = '1'
        and	cpl.medica_cnu is not null
        and not exists (select	*
                        from	totvsaudit.pfcompl taudit
                        join	totvsaudit.pfcompl taudit_org
                        on	taudit_org.auditid = taudit.auditid
                            and	taudit_org.parentlogid = taudit.logid
                        join	totvsaudit.zauditchanges taudit_data
                        on	taudit_data.auditid = taudit.auditid
                        where	taudit.codcoligada = fun.codcoligada
                        and	taudit.chapa = fun.chapa
                        and	taudit_org.medica_cnu <> taudit.medica_cnu
                        and	taudit_data.auditdate between  @DataIni and @DataFim)
        and	fun.codsituacao not in ('D')

        union all

        /*				---09o - POSTERIOR - INCLUSÃO DE DEPENDENTES EXCLUSIVO---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= case when dep.grauparentesco = '3' then 'I'
                                                                else null
                                                            end
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Data_Inclusão]							= getdate()
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	totvsaudit.pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('1','D','T','3')
        join	totvsaudit.pfdepend dep_org
        on	dep_org.auditid = dep.auditid
            and	dep_org.parentlogid = dep.logid
        join	totvsaudit.zauditchanges dep_data
        on	dep_data.auditid = dep.auditid
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	dep_data.auditdate between @DataIni and @DataFim
        and	fun.dataadmissao < @DataIni
        and	fun.tipoadmissao not in ('E','T')
        and ((dep_org.incassistmedica is null
        or	dep_org.incassistmedica = '0')
        and	dep.incassistmedica = '1')
        and	cpl.medica_cnu is not null
        and  ((datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 21   
        and   (dep.universitario = 0
        or	dep.universitario is null)
        or   (datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 24
        and	dep.universitario = 1)))
        and	fun.tipoadmissao not in ('E','T')
        and not exists (select	*
                        from	totvsaudit.pfcompl taudit
                        join	totvsaudit.pfcompl taudit_org
                        on	taudit_org.auditid = taudit.auditid
                            and	taudit_org.parentlogid = taudit.logid
                        join	totvsaudit.zauditchanges taudit_data
                        on	taudit_data.auditid = taudit.auditid
                        where	taudit.codcoligada = fun.codcoligada
                        and	taudit.chapa = fun.chapa
                        and	taudit_org.medica_cnu <> taudit.medica_cnu
                        and	taudit_data.auditdate between  @DataIni and @DataFim)

        union all

        /*				---10o - POSTERIOR - INCLUSÃO DE DEPENDENTES NOVO EXCLUSIVO---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 1
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= fun.codcoligada
                ,[Data_Lotacao]								= case when fun.dataadmissao >= @DataFim then fun.dataadmissao
                                                                else getdate()
                                                            end
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= case when dep.grauparentesco = '3' then 'I'
                                                                else null
                                                            end
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Data_Inclusão]							= getdate()
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= plano.descricao
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= dep.nome
                ,[Data_Nascimento]							= dep.dtnascimento
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= dep.sexo
                ,[Ind_Estado_Civil]							= case when dep.estadocivil = 'C' then 'C'
                                                                when dep.estadocivil = 'D' then 'D'
                                                                when dep.estadocivil = 'I' then 'I'
                                                                when dep.estadocivil = 'A' then 'S'
                                                                when dep.estadocivil = 'O' then 'S'
                                                                when dep.estadocivil = 'P' then 'S'
                                                                when dep.estadocivil = 'S' then 'S'
                                                                when dep.estadocivil = 'E' then 'U'
                                                                when dep.estadocivil = 'V' then 'V'
                                                                else 'S'
                                                            end
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= isnull(mae.maedepend,'Não consta no registro civil')
                ,[Num_CPF]									= replace(replace(cast(dep.cpf as varchar(20)),'.',''),'-','')
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= upper(pes.cidade) collate SQL_Latin1_General_CP1251_CS_AS
                ,[UF_Residencia]							= pes.estado
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	totvsaudit.pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('1','D','T','3')
        join	totvsaudit.zauditchanges dep_data
        on	dep_data.auditid = dep.auditid
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	dep_data.auditdate between @DataIni and @DataFim
        and	fun.dataadmissao < @DataIni
        and	fun.tipoadmissao not in ('E','T')
        and	dep.auditaction = 'I'
        and	dep.incassistmedica = '1'
        and	cpl.medica_cnu is not null
        and  ((datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 21   
        and   (dep.universitario = 0
        or	dep.universitario is null)
        or   (datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 24
        and	dep.universitario = 1)))
        and	fun.tipoadmissao not in ('E','T')
        and	fun.codsituacao not in ('D')
        and	not exists (select	*
                            from	totvsaudit.pfcompl taudit
                            join	totvsaudit.pfcompl taudit_org
                            on	taudit_org.auditid = taudit.auditid
                                and	taudit_org.parentlogid = taudit.logid
                            join	totvsaudit.zauditchanges taudit_data
                            on	taudit_data.auditid = taudit.auditid
                            where	taudit.codcoligada = fun.codcoligada
                            and	taudit.chapa = fun.chapa
                            and	taudit_org.medica_cnu <> taudit.medica_cnu
                            and	taudit_data.auditdate between  @DataIni and @DataFim)
            
        union all

        /*				---11o - ALTERAÇÃO CADASTRAL - TITULAR---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 2
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= '00'
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapaanteriortransf
                ,[Num_Sequencia_Matricula_Empresa]			= '00'
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= fun.chapa
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.dttransferencia between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	fun.tipoadmissao in ('E','T')
        and	fun.codsituacao not in ('D')
        and	fun.codcoligada <> fun.coligadaanteriortransf

        union all

        /*				---12o - ALTERAÇÃO CADASTRAL - CONJUGE---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 2
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapaanteriortransf
                ,[Num_Sequencia_Matricula_Empresa]			= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= fun.chapa
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('5','C')
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.dttransferencia between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	dep.incassistmedica = 1
        and	fun.tipoadmissao in ('E','T')
        and	fun.codsituacao not in ('D')
        and	fun.codcoligada <> fun.coligadaanteriortransf

        union all

        /*				---13o - ALTERAÇÃO CADASTRAL - DEPENDENTES---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 2
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= case when dep.grauparentesco = '3' then 'I'
                                                                else null
                                                            end
                ,[Num_Matricula_Empresa]					= fun.chapaanteriortransf
                ,[Num_Sequencia_Matricula_Empresa]			= row_number() over(partition by dep.chapa,dep.sexo order by dep.chapa)+case when dep.sexo = 'M' then 9 else 29 end
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= null
                ,[Cod_Motivo_Exclusão]						= null
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= fun.chapa
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('1','D','T','3')
        left join pfdependcompl mae (nolock)
        on	mae.codcoligada = dep.codcoligada
            and	mae.chapa = dep.chapa
            and	mae.nrodepend = dep.nrodepend
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.dttransferencia between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	dep.incassistmedica = 1
        and  ((datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 21   
        and   (dep.universitario = 0
        or	dep.universitario is null)
        or   (datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 24
        and	dep.universitario = 1)))
        and	fun.tipoadmissao in ('E','T')
        and	fun.codsituacao not in ('D')
        and	fun.codcoligada <> fun.coligadaanteriortransf

        union all

        /*				---14o - DEMISSÃO - EXCLUSÃO DE TITULAR---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 3
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= '00'
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= '00'
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= eomonth(fun.datademissao)
                ,[Cod_Motivo_Exclusão]						= '3'
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.datademissão between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	fun.tipodemissao not in ('5','6')

        /*
        union all

                        -15o - DEMISSÃO - EXCLUSÃO DE CONJUGE---
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 3
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= null
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= eomonth(fun.datademissao)
                ,[Cod_Motivo_Exclusão]						= '13'
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('5','C')
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.datademissão between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	dep.incassistmedica = 1
        and	fun.tipodemissao not in ('5','6')

        union all

                        -16o - DEMISSAO - EXCLUSÃO DE DEPENDENTES---				
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 3
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.sexo = 'F' then cast(dep.nrodepend+30 as varchar(10))
                                                                else cast(dep.nrodepend+10 as varchar(10))
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= null
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= eomonth(fun.datademissao)
                ,[Cod_Motivo_Exclusão]						= '13'
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        join	pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('1','D','T','3')
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	fun.datademissão between @DataIni and @DataFim
        and	cpl.medica_cnu is not null
        and	dep.incassistmedica = 1
        and	fun.tipodemissao not in ('5','6')
        */
        
        union all

        /*				---17o - POSTERIOR - EXCLUSÃO DE TITULAR---				*/
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 3
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= '00'
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= '00'
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= eomonth(data.auditdate)
                ,[Cod_Motivo_Exclusão]						= '13'
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	dbo.pfunc fun
        join	dbo.ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join dbo.pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	totvsaudit.pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	totvsaudit.zauditchanges data
        on	data.auditid = cpl.auditid
        join	totvsaudit.pfcompl cpl_org
        on	cpl_org.auditid = cpl.auditid
            and	cpl_org.parentlogid = cpl.logid
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	data.auditdate between @DataIni and @DataFim
        and	fun.datademissao is null
        and   (cpl.medica_cnu is null
        and	cpl_org.medica_cnu is not null)

        /*
        union all

                        -18o - POSTERIOR - EXCLUSÃO DE CONJUGE---				
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 3
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                                when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                                else 'Vericar cadastro do dependente'
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= null
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= eomonth(data.auditdate)
                ,[Cod_Motivo_Exclusão]						= '13'
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
                ,[Cod_Orgao_Emissor]						= null
                ,[Cod_Pais_Emissor]							= null
                ,[Num_PIS]									= null
                ,[Num_Unico_Saude]							= null
                ,[Num_Declaracao_Nascido_Vivo]				= null
                ,[Nome_Logradouro]							= null
                ,[Num_Endereco]								= null
                ,[TXT_Complemento]							= null
                ,[Nome_Bairro]								= null
                ,[Num_CEP]									= null
                ,[Cod_UNIMED_LCAT]							= null
                ,[Num_DDD_Telefone]							= null
                ,[Num_Telefone]								= null
                ,[Ramal]									= null
                ,[DDD_Celular]								= null
                ,[Num_Celular]								= null
                ,[End_e-Mail]								= null
                ,[Cod_Banco_Reembolso]						= null
                ,[Cod_Agencia_Reembolso]					= null
                ,[Num_DV_Agencia_Reembolso]					= null
                ,[Num_Conta_Corrente_Reembolso]				= null
                ,[Num_DV_CC_Reembolso]						= null
                ,[Ind_Cartao_Residencia]					= null
                ,[Nome_Cidade_Residencia]					= null
                ,[UF_Residencia]							= null
                ,[Cod_Grupo_Carencia]						= null
                ,[Setor_(Especifico_S.O.)]					= null
                ,[Num_Matricula_Empresa_Nova]				= null
                ,[Num_Sequencial_Marticula_Nova]			= null
                ,[Cod_Municipio_Nascimento_(Correio)]		= null
                ,[Cod_Municipio_Nascimento_(IBGE)]			= null
                ,[Num_Associado_Origem]						= null
                ,[CID_01]									= null
                ,[CID_02]									= null
                ,[CID_03]									= null
                ,[CID_04]									= null
                ,[CID_05]									= null
                ,[CID_06]									= null
                ,[CID_07]									= null
                ,[CID_08]									= null
                ,[CID_09]									= null
                ,[CID_10]									= null
                ,[CID_11]									= null
                ,[CID_12]									= null
                ,[CID_13]									= null
                ,[CID_14]									= null
                ,[CID_15]									= null
                ,[CID_16]									= null
                ,[CID_17]									= null
                ,[CID_18]									= null
                ,[CID_19]									= null
                ,[CID_20]									= null
                ,[Ind_tipo_Carencia]						= null
                ,[Nome_Social]								= null
                ,[Genero_Social]							= null
                ,[Documento_Civil]							= null
                ,[Tipo_Documento]							= null
                ,[Protocolo_RN412]							= null

        from	pfunc fun
        join	ppessoa pes (nolock)
        on	pes.codigo = fun.codpessoa
        left join pfdepend mae (nolock)
        on	mae.codcoligada = fun.codcoligada
            and	mae.chapa = fun.chapa
            and mae.grauparentesco = '7'
        join	totvsaudit.pfcompl cpl
        on	cpl.codcoligada = fun.codcoligada
            and	cpl.chapa = fun.chapa
        join	totvsaudit.zauditchanges data
        on	data.auditid = cpl.auditid
        join	totvsaudit.pfcompl cpl_org
        on	cpl_org.auditid = cpl.auditid
            and	cpl_org.parentlogid = cpl.logid
        join	pfdepend dep
        on	dep.codcoligada = fun.codcoligada
            and	dep.chapa = fun.chapa
            and	dep.grauparentesco in ('5','C')
        left join	gconsist convenio
        on	convenio.codcliente = fun.codcoligada
            and	convenio.codtabela = 'CNU_CODIGO'
        left join	gconsist plano
        on	plano.codcliente = cpl.medica_cnu
            and	plano.codtabela = 'CNU_PLANO'

        where	data.auditdate between @DataIni and @DataFim
        and	fun.datademissao is null
        and   (cpl.medica_cnu is null
        and	cpl_org.medica_cnu is not null)
        and	dep.incassistmedica = 1

        union all

                        -19o - POSTERIOR - EXCLUSÃO DE DEPENDENTES---				
        select	 [Cod_Tipo_Registro]						= '1'
                ,[Ind_Layout_Origem]						= 'U'
                ,[Cod_Tipo_Operacao]						= 3
                ,[Cod_Empresa]								= convenio.descricao
                ,[Cod_Lotacao]								= null
                ,[Data_Lotacao]								= null
                ,[Centro_de_Custo]							= null
                ,[Cod_Familia]								= null
                ,[Cod_Dependencia]							= case when dep.sexo = 'F' then cast(dep.nrodepend+30 as varchar(10))
                                                                else cast(dep.nrodepend+10 as varchar(10))
                                                            end
                ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
                ,[Ind_Condicao_Dependente]					= null
                ,[Num_Matricula_Empresa]					= fun.chapa
                ,[Num_Sequencia_Matricula_Empresa]			= null
                ,[Data_Inclusão]							= null
                ,[Data_Admissao]							= null
                ,[Cod_Plano]								= null
                ,[Data_Troca_Plano]							= null
                ,[Data_Exclusão]							= eomonth(data.auditdate)
                ,[Cod_Motivo_Exclusão]						= '13'
                ,[Data_Reativacao]							= null
                ,[Nome_Completo_Beneficiario]				= null
                ,[Data_Nascimento]							= null
                ,[Cod_Pais_Nascimento]						= null
                ,[Ind_Sexo]									= null
                ,[Ind_Estado_Civil]							= null
                ,[Data_Casamento]							= null
                ,[Nome_Completo_Mae]						= null
                ,[Num_CPF]									= null
                ,[Num_Identidade]							= null
            ,[Cod_Orgao_Emissor]						= null
            ,[Cod_Pais_Emissor]							= null
            ,[Num_PIS]									= null
            ,[Num_Unico_Saude]							= null
            ,[Num_Declaracao_Nascido_Vivo]				= null
            ,[Nome_Logradouro]							= null
            ,[Num_Endereco]								= null
            ,[TXT_Complemento]							= null
            ,[Nome_Bairro]								= null
            ,[Num_CEP]									= null
            ,[Cod_UNIMED_LCAT]							= null
            ,[Num_DDD_Telefone]							= null
            ,[Num_Telefone]								= null
            ,[Ramal]									= null
            ,[DDD_Celular]								= null
            ,[Num_Celular]								= null
            ,[End_e-Mail]								= null
            ,[Cod_Banco_Reembolso]						= null
            ,[Cod_Agencia_Reembolso]					= null
            ,[Num_DV_Agencia_Reembolso]					= null
            ,[Num_Conta_Corrente_Reembolso]				= null
            ,[Num_DV_CC_Reembolso]						= null
            ,[Ind_Cartao_Residencia]					= null
            ,[Nome_Cidade_Residencia]					= null
            ,[UF_Residencia]							= null
            ,[Cod_Grupo_Carencia]						= null
            ,[Setor_(Especifico_S.O.)]					= null
            ,[Num_Matricula_Empresa_Nova]				= null
            ,[Num_Sequencial_Marticula_Nova]			= null
            ,[Cod_Municipio_Nascimento_(Correio)]		= null
            ,[Cod_Municipio_Nascimento_(IBGE)]			= null
            ,[Num_Associado_Origem]						= null
            ,[CID_01]									= null
            ,[CID_02]									= null
            ,[CID_03]									= null
            ,[CID_04]									= null
            ,[CID_05]									= null
            ,[CID_06]									= null
            ,[CID_07]									= null
            ,[CID_08]									= null
            ,[CID_09]									= null
            ,[CID_10]									= null
            ,[CID_11]									= null
            ,[CID_12]									= null
            ,[CID_13]									= null
            ,[CID_14]									= null
            ,[CID_15]									= null
            ,[CID_16]									= null
            ,[CID_17]									= null
            ,[CID_18]									= null
            ,[CID_19]									= null
            ,[CID_20]									= null
            ,[Ind_tipo_Carencia]						= null
            ,[Nome_Social]								= null
            ,[Genero_Social]							= null
            ,[Documento_Civil]							= null
            ,[Tipo_Documento]							= null
            ,[Protocolo_RN412]							= null

    from	pfunc fun
    join	ppessoa pes (nolock)
    on	pes.codigo = fun.codpessoa
    join	totvsaudit.pfcompl cpl
    on	cpl.codcoligada = fun.codcoligada
        and	cpl.chapa = fun.chapa
    join	totvsaudit.zauditchanges data
    on	data.auditid = cpl.auditid
    join	totvsaudit.pfcompl cpl_org
    on	cpl_org.auditid = cpl.auditid
        and	cpl_org.parentlogid = cpl.logid
    join	pfdepend dep
    on	dep.codcoligada = fun.codcoligada
        and	dep.chapa = fun.chapa
        and	dep.grauparentesco in ('1','D','T','3')
    left join pfdependcompl mae (nolock)
    on	mae.codcoligada = dep.codcoligada
        and	mae.chapa = dep.chapa
        and	mae.nrodepend = dep.nrodepend
    left join	gconsist convenio
    on	convenio.codcliente = fun.codcoligada
        and	convenio.codtabela = 'CNU_CODIGO'
    left join	gconsist plano
    on	plano.codcliente = cpl.medica_cnu
        and	plano.codtabela = 'CNU_PLANO'

    where	data.auditdate between @DataIni and @DataFim
    and	fun.datademissao is null
    and   (cpl.medica_cnu is null
    and	cpl_org.medica_cnu is not null)
    and	dep.incassistmedica = 1

    union all

    /*				---20o - POSTERIOR - EXCLUSÃO DE CONJUGE EXCLUSIVO---				*/
    select	 [Cod_Tipo_Registro]						= '1'
            ,[Ind_Layout_Origem]						= 'U'
            ,[Cod_Tipo_Operacao]						= 3
            ,[Cod_Empresa]								= convenio.descricao
            ,[Cod_Lotacao]								= null
            ,[Data_Lotacao]								= null
            ,[Centro_de_Custo]							= null
            ,[Cod_Familia]								= null
            ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                            when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                            when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                            else 'Vericar cadastro do dependente'
                                                        end
            ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
            ,[Ind_Condicao_Dependente]					= null
            ,[Num_Matricula_Empresa]					= fun.chapa
            ,[Num_Sequencia_Matricula_Empresa]			= case when dep.grauparentesco = 'C' then '02'
                                                            when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                            when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                            else 'Vericar cadastro do dependente'
                                                        end
            ,[Data_Inclusão]							= null
            ,[Data_Admissao]							= null
            ,[Cod_Plano]								= null
            ,[Data_Troca_Plano]							= null
            ,[Data_Exclusão]							= eomonth(dep_data.auditdate)
            ,[Cod_Motivo_Exclusão]						= '13'
            ,[Data_Reativacao]							= null
            ,[Nome_Completo_Beneficiario]				= null
            ,[Data_Nascimento]							= null
            ,[Cod_Pais_Nascimento]						= null
            ,[Ind_Sexo]									= null
            ,[Ind_Estado_Civil]							= null
            ,[Data_Casamento]							= null
            ,[Nome_Completo_Mae]						= null
            ,[Num_CPF]									= null
            ,[Num_Identidade]							= null
            ,[Cod_Orgao_Emissor]						= null
            ,[Cod_Pais_Emissor]							= null
            ,[Num_PIS]									= null
            ,[Num_Unico_Saude]							= null
            ,[Num_Declaracao_Nascido_Vivo]				= null
            ,[Nome_Logradouro]							= null
            ,[Num_Endereco]								= null
            ,[TXT_Complemento]							= null
            ,[Nome_Bairro]								= null
            ,[Num_CEP]									= null
            ,[Cod_UNIMED_LCAT]							= null
            ,[Num_DDD_Telefone]							= null
            ,[Num_Telefone]								= null
            ,[Ramal]									= null
            ,[DDD_Celular]								= null
            ,[Num_Celular]								= null
            ,[End_e-Mail]								= null
            ,[Cod_Banco_Reembolso]						= null
            ,[Cod_Agencia_Reembolso]					= null
            ,[Num_DV_Agencia_Reembolso]					= null
            ,[Num_Conta_Corrente_Reembolso]				= null
            ,[Num_DV_CC_Reembolso]						= null
            ,[Ind_Cartao_Residencia]					= null
            ,[Nome_Cidade_Residencia]					= null
            ,[UF_Residencia]							= null
            ,[Cod_Grupo_Carencia]						= null
            ,[Setor_(Especifico_S.O.)]					= null
            ,[Num_Matricula_Empresa_Nova]				= null
            ,[Num_Sequencial_Marticula_Nova]			= null
            ,[Cod_Municipio_Nascimento_(Correio)]		= null
            ,[Cod_Municipio_Nascimento_(IBGE)]			= null
            ,[Num_Associado_Origem]						= null
            ,[CID_01]									= null
            ,[CID_02]									= null
            ,[CID_03]									= null
            ,[CID_04]									= null
            ,[CID_05]									= null
            ,[CID_06]									= null
            ,[CID_07]									= null
            ,[CID_08]									= null
            ,[CID_09]									= null
            ,[CID_10]									= null
            ,[CID_11]									= null
            ,[CID_12]									= null
            ,[CID_13]									= null
            ,[CID_14]									= null
            ,[CID_15]									= null
            ,[CID_16]									= null
            ,[CID_17]									= null
            ,[CID_18]									= null
            ,[CID_19]									= null
            ,[CID_20]									= null
            ,[Ind_tipo_Carencia]						= null
            ,[Nome_Social]								= null
            ,[Genero_Social]							= null
            ,[Documento_Civil]							= null
            ,[Tipo_Documento]							= null
            ,[Protocolo_RN412]							= null

    from	pfunc fun
    join	ppessoa pes (nolock)
    on	pes.codigo = fun.codpessoa
    left join pfdepend mae (nolock)
    on	mae.codcoligada = fun.codcoligada
        and	mae.chapa = fun.chapa
        and mae.grauparentesco = '7'
    join	totvsaudit.pfdepend dep
    on	dep.codcoligada = fun.codcoligada
        and	dep.chapa = fun.chapa
        and	dep.grauparentesco in ('5','C')
    join	totvsaudit.pfdepend dep_org
    on	dep_org.auditid = dep.auditid
        and	dep_org.parentlogid = dep.logid
    join	totvsaudit.zauditchanges dep_data
    on	dep_data.auditid = dep.auditid
    join	pfcompl cpl
    on	cpl.codcoligada = fun.codcoligada
        and	cpl.chapa = fun.chapa
    left join	gconsist convenio
    on	convenio.codcliente = fun.codcoligada
        and	convenio.codtabela = 'CNU_CODIGO'
    left join	gconsist plano
    on	plano.codcliente = cpl.medica_cnu
        and	plano.codtabela = 'CNU_PLANO'

    where	dep_data.auditdate between @DataIni and @DataFim
    and	fun.datademissao is null
    and  ((dep_org.incassistmedica is not null
    or	dep_org.incassistmedica = '1')
    and	dep.incassistmedica = '0')
    and	cpl.medica_cnu is not null
    and not exists (select	*
                    from	totvsaudit.pfcompl taudit
                    join	totvsaudit.pfcompl taudit_org
                    on	taudit_org.auditid = taudit.auditid
                        and	taudit_org.parentlogid = taudit.logid
                    join	totvsaudit.zauditchanges taudit_data
                    on	taudit_data.auditid = taudit.auditid
                    where	taudit.codcoligada = fun.codcoligada
                    and	taudit.chapa = fun.chapa
                    and	taudit_org.medica_cnu <> taudit.medica_cnu
                    and	taudit_data.auditdate between  @DataIni and @DataFim)

    union all

    /*				---21o - POSTERIOR - EXCLUSÃO DE DEPENDENTES EXCLUSIVO---				*/
    select	 [Cod_Tipo_Registro]						= '1'
            ,[Ind_Layout_Origem]						= 'U'
            ,[Cod_Tipo_Operacao]						= 3
            ,[Cod_Empresa]								= convenio.descricao
            ,[Cod_Lotacao]								= null
            ,[Data_Lotacao]								= null
            ,[Centro_de_Custo]							= null
            ,[Cod_Familia]								= null
            ,[Cod_Dependencia]							= case when dep.sexo = 'F' then cast(dep.nrodepend+30 as varchar(10))
                                                            else cast(dep.nrodepend+10 as varchar(10))
                                                        end
            ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
            ,[Ind_Condicao_Dependente]					= null
            ,[Num_Matricula_Empresa]					= fun.chapa
            ,[Num_Sequencia_Matricula_Empresa]			= case when dep.sexo = 'F' then cast(dep.nrodepend+30 as varchar(10))
                                                            else cast(dep.nrodepend+10 as varchar(10))
                                                        end
            ,[Data_Inclusão]							= null
            ,[Data_Admissao]							= null
            ,[Cod_Plano]								= null
            ,[Data_Troca_Plano]							= null
            ,[Data_Exclusão]							= eomonth(dep_data.auditdate)
            ,[Cod_Motivo_Exclusão]						= '13'
            ,[Data_Reativacao]							= null
            ,[Nome_Completo_Beneficiario]				= null
            ,[Data_Nascimento]							= null
            ,[Cod_Pais_Nascimento]						= null
            ,[Ind_Sexo]									= null
            ,[Ind_Estado_Civil]							= null
            ,[Data_Casamento]							= null
            ,[Nome_Completo_Mae]						= null
            ,[Num_CPF]									= null
            ,[Num_Identidade]							= null
            ,[Cod_Orgao_Emissor]						= null
            ,[Cod_Pais_Emissor]							= null
            ,[Num_PIS]									= null
            ,[Num_Unico_Saude]							= null
            ,[Num_Declaracao_Nascido_Vivo]				= null
            ,[Nome_Logradouro]							= null
            ,[Num_Endereco]								= null
            ,[TXT_Complemento]							= null
            ,[Nome_Bairro]								= null
            ,[Num_CEP]									= null
            ,[Cod_UNIMED_LCAT]							= null
            ,[Num_DDD_Telefone]							= null
            ,[Num_Telefone]								= null
            ,[Ramal]									= null
            ,[DDD_Celular]								= null
            ,[Num_Celular]								= null
            ,[End_e-Mail]								= null
            ,[Cod_Banco_Reembolso]						= null
            ,[Cod_Agencia_Reembolso]					= null
            ,[Num_DV_Agencia_Reembolso]					= null
            ,[Num_Conta_Corrente_Reembolso]				= null
            ,[Num_DV_CC_Reembolso]						= null
            ,[Ind_Cartao_Residencia]					= null
            ,[Nome_Cidade_Residencia]					= null
            ,[UF_Residencia]							= null
            ,[Cod_Grupo_Carencia]						= null
            ,[Setor_(Especifico_S.O.)]					= null
            ,[Num_Matricula_Empresa_Nova]				= null
            ,[Num_Sequencial_Marticula_Nova]			= null
            ,[Cod_Municipio_Nascimento_(Correio)]		= null
            ,[Cod_Municipio_Nascimento_(IBGE)]			= null
            ,[Num_Associado_Origem]						= null
            ,[CID_01]									= null
            ,[CID_02]									= null
            ,[CID_03]									= null
            ,[CID_04]									= null
            ,[CID_05]									= null
            ,[CID_06]									= null
            ,[CID_07]									= null
            ,[CID_08]									= null
            ,[CID_09]									= null
            ,[CID_10]									= null
            ,[CID_11]									= null
            ,[CID_12]									= null
            ,[CID_13]									= null
            ,[CID_14]									= null
            ,[CID_15]									= null
            ,[CID_16]									= null
            ,[CID_17]									= null
            ,[CID_18]									= null
            ,[CID_19]									= null
            ,[CID_20]									= null
            ,[Ind_tipo_Carencia]						= null
            ,[Nome_Social]								= null
            ,[Genero_Social]							= null
            ,[Documento_Civil]							= null
            ,[Tipo_Documento]							= null
            ,[Protocolo_RN412]							= null

    from	pfunc fun
    join	ppessoa pes (nolock)
    on	pes.codigo = fun.codpessoa
    join	totvsaudit.pfdepend dep
    on	dep.codcoligada = fun.codcoligada
        and	dep.chapa = fun.chapa
        and	dep.grauparentesco in ('1','D','T','3')
    join	totvsaudit.pfdepend dep_org
    on	dep_org.auditid = dep.auditid
        and	dep_org.parentlogid = dep.logid
    join	totvsaudit.zauditchanges dep_data
    on	dep_data.auditid = dep.auditid
    join	pfcompl cpl
    on	cpl.codcoligada = fun.codcoligada
        and	cpl.chapa = fun.chapa
    left join	gconsist convenio
    on	convenio.codcliente = fun.codcoligada
        and	convenio.codtabela = 'CNU_CODIGO'
    left join	gconsist plano
    on	plano.codcliente = cpl.medica_cnu
        and	plano.codtabela = 'CNU_PLANO'
    left join pfdependcompl mae (nolock)
    on	mae.codcoligada = dep.codcoligada
        and	mae.chapa = dep.chapa
        and	mae.nrodepend = dep.nrodepend

    where	dep_data.auditdate between @DataIni and @DataFim
    and	fun.datademissao is null
    and  ((dep_org.incassistmedica is not null
    or	dep_org.incassistmedica = '1')
    and	dep.incassistmedica = '0')
    and	cpl.medica_cnu is not null
    and not exists (select	*
                    from	totvsaudit.pfcompl taudit
                    join	totvsaudit.pfcompl taudit_org
                    on	taudit_org.auditid = taudit.auditid
                        and	taudit_org.parentlogid = taudit.logid
                    join	totvsaudit.zauditchanges taudit_data
                    on	taudit_data.auditid = taudit.auditid
                    where	taudit.codcoligada = fun.codcoligada
                    and	taudit.chapa = fun.chapa
                    and	taudit_org.medica_cnu <> taudit.medica_cnu
                    and	taudit_data.auditdate between  @DataIni and @DataFim)
    */

    union all

    /*				---22o - POSTERIOR - TROCA DE PLANO DE TITULAR---				*/
    select	 [Cod_Tipo_Registro]						= '1'
            ,[Ind_Layout_Origem]						= 'U'
            ,[Cod_Tipo_Operacao]						= 5
            ,[Cod_Empresa]								= convenio.descricao
            ,[Cod_Lotacao]								= null
            ,[Data_Lotacao]								= null
            ,[Centro_de_Custo]							= null
            ,[Cod_Familia]								= null
            ,[Cod_Dependencia]							= '00'
            ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
            ,[Ind_Condicao_Dependente]					= null
            ,[Num_Matricula_Empresa]					= fun.chapa
            ,[Num_Sequencia_Matricula_Empresa]			= '00'
            ,[Data_Inclusão]							= null
            ,[Data_Admissao]							= null
            ,[Cod_Plano]								= plano.descricao
            ,[Data_Troca_Plano]							= dateadd(month,1,datefromparts(year(getdate()),month(getdate()),1))
            ,[Data_Exclusão]							= null
            ,[Cod_Motivo_Exclusão]						= null
            ,[Data_Reativacao]							= null
            ,[Nome_Completo_Beneficiario]				= null
            ,[Data_Nascimento]							= null
            ,[Cod_Pais_Nascimento]						= null
            ,[Ind_Sexo]									= null
            ,[Ind_Estado_Civil]							= null
            ,[Data_Casamento]							= null
            ,[Nome_Completo_Mae]						= null
            ,[Num_CPF]									= null
            ,[Num_Identidade]							= null
            ,[Cod_Orgao_Emissor]						= null
            ,[Cod_Pais_Emissor]							= null
            ,[Num_PIS]									= null
            ,[Num_Unico_Saude]							= null
            ,[Num_Declaracao_Nascido_Vivo]				= null
            ,[Nome_Logradouro]							= null
            ,[Num_Endereco]								= null
            ,[TXT_Complemento]							= null
            ,[Nome_Bairro]								= null
            ,[Num_CEP]									= null
            ,[Cod_UNIMED_LCAT]							= null
            ,[Num_DDD_Telefone]							= null
            ,[Num_Telefone]								= null
            ,[Ramal]									= null
            ,[DDD_Celular]								= null
            ,[Num_Celular]								= null
            ,[End_e-Mail]								= null
            ,[Cod_Banco_Reembolso]						= null
            ,[Cod_Agencia_Reembolso]					= null
            ,[Num_DV_Agencia_Reembolso]					= null
            ,[Num_Conta_Corrente_Reembolso]				= null
            ,[Num_DV_CC_Reembolso]						= null
            ,[Ind_Cartao_Residencia]					= null
            ,[Nome_Cidade_Residencia]					= null
            ,[UF_Residencia]							= null
            ,[Cod_Grupo_Carencia]						= null
            ,[Setor_(Especifico_S.O.)]					= null
            ,[Num_Matricula_Empresa_Nova]				= null
            ,[Num_Sequencial_Marticula_Nova]			= null
            ,[Cod_Municipio_Nascimento_(Correio)]		= null
            ,[Cod_Municipio_Nascimento_(IBGE)]			= null
            ,[Num_Associado_Origem]						= null
            ,[CID_01]									= null
            ,[CID_02]									= null
            ,[CID_03]									= null
            ,[CID_04]									= null
            ,[CID_05]									= null
            ,[CID_06]									= null
            ,[CID_07]									= null
            ,[CID_08]									= null
            ,[CID_09]									= null
            ,[CID_10]									= null
            ,[CID_11]									= null
            ,[CID_12]									= null
            ,[CID_13]									= null
            ,[CID_14]									= null
            ,[CID_15]									= null
            ,[CID_16]									= null
            ,[CID_17]									= null
            ,[CID_18]									= null
            ,[CID_19]									= null
            ,[CID_20]									= null
            ,[Ind_tipo_Carencia]						= null
            ,[Nome_Social]								= null
            ,[Genero_Social]							= null
            ,[Documento_Civil]							= null
            ,[Tipo_Documento]							= null
            ,[Protocolo_RN412]							= null

    from	dbo.pfunc fun
    join	dbo.ppessoa pes (nolock)
    on	pes.codigo = fun.codpessoa
    left join dbo.pfdepend mae (nolock)
    on	mae.codcoligada = fun.codcoligada
        and	mae.chapa = fun.chapa
        and mae.grauparentesco = '7'
    join	totvsaudit.pfcompl cpl
    on	cpl.codcoligada = fun.codcoligada
        and	cpl.chapa = fun.chapa
    join	totvsaudit.zauditchanges data
    on	data.auditid = cpl.auditid
    join	totvsaudit.pfcompl cpl_org
    on	cpl_org.auditid = cpl.auditid
        and	cpl_org.parentlogid = cpl.logid
    left join	gconsist convenio
    on	convenio.codcliente = fun.codcoligada
        and	convenio.codtabela = 'CNU_CODIGO'
    left join	gconsist plano
    on	plano.codcliente = cpl.medica_cnu
        and	plano.codtabela = 'CNU_PLANO'

    where	data.auditdate between @DataIni and @DataFim
    and	fun.datademissao is null
    and	cpl.medica_cnu <> cpl_org.medica_cnu
    and	cpl_org.medica_cnu is not null

    /*
    union all

                    -23o - POSTERIOR - TROCA DE PLANO DE CONJUGE---				
    select	 [Cod_Tipo_Registro]						= '1'
            ,[Ind_Layout_Origem]						= 'U'
            ,[Cod_Tipo_Operacao]						= 5
            ,[Cod_Empresa]								= convenio.descricao
            ,[Cod_Lotacao]								= null
            ,[Data_Lotacao]								= null
            ,[Centro_de_Custo]							= null
            ,[Cod_Familia]								= null
            ,[Cod_Dependencia]							= case when dep.grauparentesco = 'C' then '02'
                                                            when dep.grauparentesco = '5' and dep.sexo = 'M' then '09'
                                                            when dep.grauparentesco = '5' and dep.sexo = 'F' then '01'
                                                            else 'Vericar cadastro do dependente'
                                                        end
            ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
            ,[Ind_Condicao_Dependente]					= null
            ,[Num_Matricula_Empresa]					= fun.chapa
            ,[Num_Sequencia_Matricula_Empresa]			= null
            ,[Data_Inclusão]							= null
            ,[Data_Admissao]							= null
            ,[Cod_Plano]								= plano.descricao
            ,[Data_Troca_Plano]							= dateadd(month,1,datefromparts(year(getdate()),month(getdate()),1))
            ,[Data_Exclusão]							= null
            ,[Cod_Motivo_Exclusão]						= null
            ,[Data_Reativacao]							= null
            ,[Nome_Completo_Beneficiario]				= null
            ,[Data_Nascimento]							= null
            ,[Cod_Pais_Nascimento]						= null
            ,[Ind_Sexo]									= null
            ,[Ind_Estado_Civil]							= null
            ,[Data_Casamento]							= null
            ,[Nome_Completo_Mae]						= null
            ,[Num_CPF]									= null
            ,[Num_Identidade]							= null
            ,[Cod_Orgao_Emissor]						= null
            ,[Cod_Pais_Emissor]							= null
            ,[Num_PIS]									= null
            ,[Num_Unico_Saude]							= null
            ,[Num_Declaracao_Nascido_Vivo]				= null
            ,[Nome_Logradouro]							= null
            ,[Num_Endereco]								= null
            ,[TXT_Complemento]							= null
            ,[Nome_Bairro]								= null
            ,[Num_CEP]									= null
            ,[Cod_UNIMED_LCAT]							= null
            ,[Num_DDD_Telefone]							= null
            ,[Num_Telefone]								= null
            ,[Ramal]									= null
            ,[DDD_Celular]								= null
            ,[Num_Celular]								= null
            ,[End_e-Mail]								= null
            ,[Cod_Banco_Reembolso]						= null
            ,[Cod_Agencia_Reembolso]					= null
            ,[Num_DV_Agencia_Reembolso]					= null
            ,[Num_Conta_Corrente_Reembolso]				= null
            ,[Num_DV_CC_Reembolso]						= null
            ,[Ind_Cartao_Residencia]					= null
            ,[Nome_Cidade_Residencia]					= null
            ,[UF_Residencia]							= null
            ,[Cod_Grupo_Carencia]						= null
            ,[Setor_(Especifico_S.O.)]					= null
            ,[Num_Matricula_Empresa_Nova]				= null
            ,[Num_Sequencial_Marticula_Nova]			= null
            ,[Cod_Municipio_Nascimento_(Correio)]		= null
            ,[Cod_Municipio_Nascimento_(IBGE)]			= null
            ,[Num_Associado_Origem]						= null
            ,[CID_01]									= null
            ,[CID_02]									= null
            ,[CID_03]									= null
            ,[CID_04]									= null
            ,[CID_05]									= null
            ,[CID_06]									= null
            ,[CID_07]									= null
            ,[CID_08]									= null
            ,[CID_09]									= null
            ,[CID_10]									= null
            ,[CID_11]									= null
            ,[CID_12]									= null
            ,[CID_13]									= null
            ,[CID_14]									= null
            ,[CID_15]									= null
            ,[CID_16]									= null
            ,[CID_17]									= null
            ,[CID_18]									= null
            ,[CID_19]									= null
            ,[CID_20]									= null
            ,[Ind_tipo_Carencia]						= null
            ,[Nome_Social]								= null
            ,[Genero_Social]							= null
            ,[Documento_Civil]							= null
            ,[Tipo_Documento]							= null
            ,[Protocolo_RN412]							= null

    from	pfunc fun
    join	ppessoa pes (nolock)
    on	pes.codigo = fun.codpessoa
    left join pfdepend mae (nolock)
    on	mae.codcoligada = fun.codcoligada
        and	mae.chapa = fun.chapa
        and mae.grauparentesco = '7'
    join	totvsaudit.pfcompl cpl
    on	cpl.codcoligada = fun.codcoligada
        and	cpl.chapa = fun.chapa
    join	totvsaudit.zauditchanges data
    on	data.auditid = cpl.auditid
    join	totvsaudit.pfcompl cpl_org
    on	cpl_org.auditid = cpl.auditid
        and	cpl_org.parentlogid = cpl.logid
    join	pfdepend dep
    on	dep.codcoligada = fun.codcoligada
        and	dep.chapa = fun.chapa
        and	dep.grauparentesco in ('5','C')
    left join	gconsist convenio
    on	convenio.codcliente = fun.codcoligada
        and	convenio.codtabela = 'CNU_CODIGO'
    left join	gconsist plano
    on	plano.codcliente = cpl.medica_cnu
        and	plano.codtabela = 'CNU_PLANO'

    where	data.auditdate between @DataIni and @DataFim
    and	fun.datademissao is null
    and	cpl.medica_cnu <> cpl_org.medica_cnu
    and	cpl_org.medica_cnu is not null
    and	dep.incassistmedica = 1

    union all

    /*				---24o - POSTERIOR - TROCA DE PLANO DE DEPENDENTES---				*/
    select	 [Cod_Tipo_Registro]						= '1'
            ,[Ind_Layout_Origem]						= 'U'
            ,[Cod_Tipo_Operacao]						= 5
            ,[Cod_Empresa]								= convenio.descricao
            ,[Cod_Lotacao]								= null
            ,[Data_Lotacao]								= null
            ,[Centro_de_Custo]							= null
            ,[Cod_Familia]								= null
            ,[Cod_Dependencia]							= case when dep.sexo = 'F' then cast(dep.nrodepend+30 as varchar(10))
                                                            else cast(dep.nrodepend+10 as varchar(10))
                                                        end
            ,[Ind_Sequencial_Dependencia_Informada]		= 'N'
            ,[Ind_Condicao_Dependente]					= null
            ,[Num_Matricula_Empresa]					= fun.chapa
            ,[Num_Sequencia_Matricula_Empresa]			= case when dep.sexo = 'F' then cast(dep.nrodepend+30 as varchar(10))
                                                            else cast(dep.nrodepend+10 as varchar(10))
                                                        end
            ,[Data_Inclusão]							= null
            ,[Data_Admissao]							= null
            ,[Cod_Plano]								= plano.descricao
            ,[Data_Troca_Plano]							= dateadd(month,1,datefromparts(year(getdate()),month(getdate()),1))
            ,[Data_Exclusão]							= null
            ,[Cod_Motivo_Exclusão]						= null
            ,[Data_Reativacao]							= null
            ,[Nome_Completo_Beneficiario]				= null
            ,[Data_Nascimento]							= null
            ,[Cod_Pais_Nascimento]						= null
            ,[Ind_Sexo]									= null
            ,[Ind_Estado_Civil]							= null
            ,[Data_Casamento]							= null
            ,[Nome_Completo_Mae]						= null
            ,[Num_CPF]									= null
            ,[Num_Identidade]							= null
            ,[Cod_Orgao_Emissor]						= null
            ,[Cod_Pais_Emissor]							= null
            ,[Num_PIS]									= null
            ,[Num_Unico_Saude]							= null
            ,[Num_Declaracao_Nascido_Vivo]				= null
            ,[Nome_Logradouro]							= null
            ,[Num_Endereco]								= null
            ,[TXT_Complemento]							= null
            ,[Nome_Bairro]								= null
            ,[Num_CEP]									= null
            ,[Cod_UNIMED_LCAT]							= null
            ,[Num_DDD_Telefone]							= null
            ,[Num_Telefone]								= null
            ,[Ramal]									= null
            ,[DDD_Celular]								= null
            ,[Num_Celular]								= null
            ,[End_e-Mail]								= null
            ,[Cod_Banco_Reembolso]						= null
            ,[Cod_Agencia_Reembolso]					= null
            ,[Num_DV_Agencia_Reembolso]					= null
            ,[Num_Conta_Corrente_Reembolso]				= null
            ,[Num_DV_CC_Reembolso]						= null
            ,[Ind_Cartao_Residencia]					= null
            ,[Nome_Cidade_Residencia]					= null
            ,[UF_Residencia]							= null
            ,[Cod_Grupo_Carencia]						= null
            ,[Setor_(Especifico_S.O.)]					= null
            ,[Num_Matricula_Empresa_Nova]				= null
            ,[Num_Sequencial_Marticula_Nova]			= null
            ,[Cod_Municipio_Nascimento_(Correio)]		= null
            ,[Cod_Municipio_Nascimento_(IBGE)]			= null
            ,[Num_Associado_Origem]						= null
            ,[CID_01]									= null
            ,[CID_02]									= null
            ,[CID_03]									= null
            ,[CID_04]									= null
            ,[CID_05]									= null
            ,[CID_06]									= null
            ,[CID_07]									= null
            ,[CID_08]									= null
            ,[CID_09]									= null
            ,[CID_10]									= null
            ,[CID_11]									= null
            ,[CID_12]									= null
            ,[CID_13]									= null
            ,[CID_14]									= null
            ,[CID_15]									= null
            ,[CID_16]									= null
            ,[CID_17]									= null
            ,[CID_18]									= null
            ,[CID_19]									= null
            ,[CID_20]									= null
            ,[Ind_tipo_Carencia]						= null
            ,[Nome_Social]								= null
            ,[Genero_Social]							= null
            ,[Documento_Civil]							= null
            ,[Tipo_Documento]							= null
            ,[Protocolo_RN412]							= null

    from	pfunc fun
    join	ppessoa pes (nolock)
    on	pes.codigo = fun.codpessoa
    join	totvsaudit.pfcompl cpl
    on	cpl.codcoligada = fun.codcoligada
        and	cpl.chapa = fun.chapa
    join	totvsaudit.zauditchanges data
    on	data.auditid = cpl.auditid
    join	totvsaudit.pfcompl cpl_org
    on	cpl_org.auditid = cpl.auditid
        and	cpl_org.parentlogid = cpl.logid
    join	pfdepend dep
    on	dep.codcoligada = fun.codcoligada
        and	dep.chapa = fun.chapa
        and	dep.grauparentesco in ('1','D','T','3')
    left join pfdependcompl mae (nolock)
    on	mae.codcoligada = dep.codcoligada
        and	mae.chapa = dep.chapa
        and	mae.nrodepend = dep.nrodepend
    left join	gconsist convenio
    on	convenio.codcliente = fun.codcoligada
        and	convenio.codtabela = 'CNU_CODIGO'
    left join	gconsist plano
    on	plano.codcliente = cpl.medica_cnu
        and	plano.codtabela = 'CNU_PLANO'

    where	data.auditdate between @DataIni and @DataFim
    and	fun.datademissao is null
    and	cpl.medica_cnu <> cpl_org.medica_cnu
    and	cpl_org.medica_cnu is not null
    and	dep.incassistmedica = 1
    and  ((datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 21   
    and   (dep.universitario = 0
    or	dep.universitario is null)
    or   (datediff(month,dep.dtnascimento,datefromparts(year(@DataFim),month(@DataFim),1))/12.00 <= 24
    and	dep.universitario = 1)))
    */
    ) x ". $filtro_op."

                ";
        //echo '<textarea>'. $query.'</textarea>';
     //   exit;
        $result = $this->dbrm->query($query);
      
       
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }


}
?>