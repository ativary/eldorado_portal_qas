<?php
namespace App\Models\Relatorio;
use CodeIgniter\Model;

class RelatorioModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $log_id;
    private $now;
    private $coligada;
    
    public function __construct()
    {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->log_id   = session()->get('log_id');
        $this->now      = date('Y-m-d H:i:s');
        $this->coligada = session()->get('func_coligada');
    }

    public function listaSecao()
    {

        $query = " SELECT CODIGO, DESCRICAO FROM PSECAO WHERE CODCOLIGADA = '{$this->coligada}' AND SECAODESATIVADA = 0 AND LEN(CODIGO) = ".TAMANHO_SECAO." ORDER BY DESCRICAO ";
        $result = $this->dbrm->query($query);

        return ($result)
            ? $result->getResultArray()
            : false;

    }

    public function listaFuncao()
    {

        $query = " SELECT CODIGO, NOME FROM PFUNCAO WHERE CODCOLIGADA = '{$this->coligada}' AND INATIVA = 0 AND LEN(CODIGO) = ".TAMANHO_FUNCAO." ORDER BY NOME ";
        $result = $this->dbrm->query($query);

        return ($result)
            ? $result->getResultArray()
            : false;

    }

    public function listaFuncionario($request)
    {

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

        $filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}

            if($request['rel'] != 2){
			    $filtro_secao_gestor = " CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
            }else{
                $filtro_secao_gestor = " (  ISNULL((SELECT TOP 1 S.CODIGO 
                FROM PFHSTSEC SEC 
                INNER JOIN PSECAO S ON SEC.CODCOLIGADA = S.CODCOLIGADA AND S.CODIGO = SEC.CODSECAO 
                WHERE SEC.CODCOLIGADA = A.CODCOLIGADA
                    AND SEC.CHAPA = A.CHAPA
                    AND SEC.DTMUDANCA <= '{$request['dtfim']}'
                ORDER BY SEC.DTMUDANCA DESC      
                ), CODSECAO) IN (".substr($codsecoes, 0, -1).")) OR ";
            }
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

		$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

        if($request['rh']) $qr_secao = "";

        if($request['rel'] == 2){

           $query =" SELECT ISNULL((SELECT TOP 1 S.CODIGO 
            FROM PFHSTSEC SEC 
            INNER JOIN PSECAO S ON SEC.CODCOLIGADA = S.CODCOLIGADA AND S.CODIGO = SEC.CODSECAO 
            WHERE SEC.CODCOLIGADA = A.CODCOLIGADA
                AND SEC.CHAPA = A.CHAPA
                AND SEC.DTMUDANCA <= '{$request['dtfim']}'
            ORDER BY SEC.DTMUDANCA DESC      
            ), CODSECAO) AS COD_SECAO, A.CHAPA, A.NOME FROM PFUNC A WHERE CODCOLIGADA = '{$this->coligada}' 
            {$qr_secao}
            AND (A.NOME LIKE '%{$request['keyword']}%' OR A.CHAPA LIKE '%{$request['keyword']}%') AND (TIPODEMISSAO IS NULL OR TIPODEMISSAO NOT IN ('5', '6')) ORDER BY NOME ASC ";

        }else{
            $query = " SELECT CHAPA, NOME FROM PFUNC WHERE CODCOLIGADA = '{$this->coligada}' {$qr_secao} AND (NOME LIKE '%{$request['keyword']}%' OR CHAPA LIKE '%{$request['keyword']}%') AND (TIPODEMISSAO IS NULL OR TIPODEMISSAO NOT IN ('5', '6')) ORDER BY NOME ASC ";
        }

        
       // exit('<pre>'.$query);
        $result = $this->dbrm->query($query);

        return ($result)
            ? $result->getResultArray()
            : false;

    }

    public function gerarRelatorio($request)
    {
        switch($request['relatorio']){
            case 1:
                return self::relatorioAbono($request);
                break;
            case 2:
                return self::relatorioColaboradores($request);
                break;
            case 3:
                return self::relatorioAfastamentos($request);
                break;
            case 4:
                return self::relatorioPonto($request);
                break;
            case 5:
                return self::relatorioHorasIninterruptas($request);
                break;
            case 6:
                return self::relatorioInterjornadaMenos11H($request);
                break;
            case 7:
                return self::relatorioMais6DiasSemDescanso($request);
                break;
            case 8:
                return self::relatorioTempoInsuficienteRefeicao($request);
                break;
            case 9:
                return self::relatorioHorarioBritanico($request);
                break;
            case 10:
                return self::relatorioSaldoBancoHoras($request);
                break;
            case 11:
                return self::relatorioExtratoBancoHoras($request);
                break;
            case 12:
                return self::relatorioGeralEquipe($request);
                break;
            case 13:
                return self::relatorioBatidaColetadaDigitada($request);
                break;
            case 14:
                return self::relatorioBatidaDigitadoExcluido($request);
                break;                
            case 15:
                return self::relatorioBatidReprovadas($request);
                break;
            case 16:
                return self::relatorioMacros($request);
                break;
            case 17:
                return self::relatorioVariaveisMoradia($request);
                break;
            case 18:
                return self::relatorioVariaveisPCD($request);
                break;
            case 19:
                return self::relatorioVariaveisCreche($request);
                break;
            case 20:
                return self::relatorioVariaveisAluguel($request);
                break;
            case 21:
                return self::relatorioVariaveissubstituicao($request);
                break;
            case 22:
                return self::relatorioVariaveisSobreaviso($request);
                break;
            case 23:
                return self::relatorioVariaveisDesconto($request);
                break;
            case 24:
                return self::relatorioVariaveis13($request);
                break;
            case 25:
                return self::relatoriosCopart($request);
                break;
            case 26:
                return self::relatorioConferenciaPremios($request);
                break;
            case 27:
                return self::relatorioVariacaoPremios($request);
                break;
            case 28:
                return self::relatorioDeflatoresPremios($request);
                break;
            case 30:
                return self::relatorioEscalaDia($request);
                break;
        }
    }
    private function relatorioVariaveisCreche($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(2);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND E.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
    
        WHERE
        A.Tipo ='2' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
    
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();

            $dadosFinais = []; // Array para armazenar os dados finais com dependentes

            // Itera sobre cada linha para extrair os dependentes do JSON
            foreach ($dados as $linha) {
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
                $linha['EVENTO'] = $param->reembolso_creche_evento;
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Mensal';
                }else{
                    $tipo = 'Complementar';
                }
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                // Verifica se existe o campo 'dependentes' no JSON e se é um array
                if (isset($valoresJson['dependentes'])) {
                    // Para cada dependente, cria uma nova linha com os dados originais e adiciona o nome do dependente
                    $depend = json_decode($valoresJson['dependentes']);
                    
                    foreach ( $depend as $dependente) {
                        $novaLinha = $linha; // Copia a linha original
                        $novaLinha['Nome_dependente'] = $dependente->nome; // Adiciona o nome do dependente
                        $novaLinha['Valor_Dependente'] = $dependente->valor; // Adiciona o nome do dependente
                        $dadosFinais[] = $novaLinha; // Adiciona a nova linha ao array final
                    }
                } else {
                    // Caso não haja dependentes, adiciona a linha original sem modificar
                    $linha['Nome_dependente'] = 'Não informado';
                    $dadosFinais[] = $linha;
                }
            }

            // Retorna os dados finais tratados e a quantidade de colunas
            return array(
                'dados'     => $dadosFinais,
                'colunas'   => $result->getFieldCount() + 3
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }

    private function relatorioVariaveisAluguel($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(7);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME  AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
       
        WHERE
        A.Tipo ='7' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
        ";
    
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();
            
            // Itera sobre cada linha para extrair a 'justificativa' do JSON
            foreach ($dados as &$linha) {
                
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Mensal';
                }else{
                    $tipo = 'Complementar';
                }
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                // Verifica se 'justificativa' existe no JSON e adiciona ao array
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
                   $linha['VALOR'] = isset($valoresJson['valor']) ? $valoresJson['valor'] : 'Não informado';
                $linha['QUANTIDADE_MESES']  = isset($valoresJson['quantMes']) ? $valoresJson['quantMes'] : 'Não informado';
                $linha['EVENTO'] = $param->reembolso_aluguel_evento;
                // Se necessário, adicione mais tratamentos para outros campos do JSON aqui
            }
            
            // Retorna os dados tratados e a quantidade de colunas
            return array(
                'dados'     => $dados,
                'colunas'   => $result->getFieldCount() + 3
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }
    private function relatorioVariaveissubstituicao($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(1);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            B.SALARIO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME  AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
       
        WHERE
        A.Tipo ='1' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
   
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();
            
            // Itera sobre cada linha para extrair a 'justificativa' do JSON
            foreach ($dados as &$linha) {
                
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Mensal';
                }else{
                    $tipo = 'Complementar';
                }


                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                $linha['SALARIO'] =   moeda($linha['SALARIO']);

                $atestado =  $mParam->DeflatoresAtestado($valoresJson['data_inicio'],$valoresJson['data_fim'],$linha['CHAPA']);
                $ferias =  $mParam->DeflatoresFerias($valoresJson['data_inicio'],$valoresJson['data_fim'],$linha['CHAPA']);
                $falta =  $mParam->DeflatoresFaltasPonto($valoresJson['data_inicio'],$valoresJson['data_fim'],$linha['CHAPA']);
                // Verifica se 'justificativa' existe no JSON e adiciona ao array
                $linha['JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
               
                $linha['EVENTO'] = $param->substituicao_evento;
              
                $funcSUb =  $mParam->dadosFuncSUb($valoresJson['funcionario_sub']);

                $linha['CHAPA_SUB'] = $valoresJson['funcionario_sub']; // 
                $linha['NOME_SUB'] =  $funcSUb[0]['NOME']; // 
                $linha['CODSITUACAO_SUB'] =  $funcSUb[0]['CODSITUACAO']; // 
                $linha['CENTROCUSTO_SUB'] =  $funcSUb[0]['CENTROCUSTO']; // 
                $linha['DESC_CUST_SUB'] =  $funcSUb[0]['DESC_CUST']; // 
                $linha['CODSECAO_SUB'] =  $funcSUb[0]['CODSECAO']; // 
                $linha['SECAO_SUB'] =  $funcSUb[0]['SECAO']; // 
                $linha['FUNCAO_SUB'] =  $funcSUb[0]['FUNCAO']; // 
                $linha['SALARIO_SUB'] =  moeda($funcSUb[0]['SALARIO']); // 
                $linha['FERIAS'] =  intval($ferias[0]['QUANTIDADE_DIAS']); 
                $linha['ATESTADO'] =  intval($atestado[0]['QUANTIDADE_DIAS']); 
                $linha['FALTAS'] =  intval($falta[0]['FALTAS']);                ; 
                // Se necessário, adicione mais tratamentos para outros campos do JSON aqui
            }

            // Retorna os dados tratados e a quantidade de colunas
            return array(
                'dados'     => $dados,
                'colunas'   => $result->getFieldCount() + 11
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }
    

    private function relatorioVariaveisSobreaviso($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(3);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            B.SALARIO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME  AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
       
        WHERE
        A.Tipo ='3' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
    
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();
            
            // Itera sobre cada linha para extrair a 'justificativa' do JSON
            foreach ($dados as &$linha) {
                
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Mensal';
                }else{
                    $tipo = 'Complementar';
                }
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                $linha['SALARIO'] = moeda($linha['SALARIO']) ;
                // Verifica se 'justificativa' existe no JSON e adiciona ao array
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
                $linha['QUANTIDADE_HORAS'] = isset($valoresJson['valor']) ? $valoresJson['valor'] : 'Não informado';
                   $linha['VALOR'] = number_format(($linha['SALARIO'] / 200) * $linha['QUANTIDADE_HORAS'], 2, '.', '');
                $linha['EVENTO'] = $param->sobreaviso_evento;
              
                // Se necessário, adicione mais tratamentos para outros campos do JSON aqui
            }

            // Retorna os dados tratados e a quantidade de colunas
            return array(
                'dados'     => $dados,
                'colunas'   => $result->getFieldCount() +3
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }
    private function relatorioVariaveisDesconto($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(5);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            B.SALARIO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME  AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
       
        WHERE
        A.Tipo ='5' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
    
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();
            
            // Itera sobre cada linha para extrair a 'justificativa' do JSON
            foreach ($dados as &$linha) {
                
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Desconto Autorizado';
                    $evento = $param->reembolso_desconto_evento;
                }elseif($linha['TIPO_REQUISIÇÂO'] == 2){
                    $tipo = 'Desconto EPI';
                    $evento = $param->reembolso_desconto_evento2;
                }else{
                    $tipo = 'Multa de transito';
                    $evento = $param->reembolso_desconto_evento3;
                }
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                // Verifica se 'justificativa' existe no JSON e adiciona ao array
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
                $linha['QUANTIDADE_MESES']  = isset($valoresJson['quantMes']) ? $valoresJson['quantMes'] : 'Não informado';
                $linha['VALOR'] = isset($valoresJson['valor']) ? $valoresJson['valor'] : 'Não informado';
               
                if (isset($valoresJson['parcelas'])){
                   
                    $parcelas = json_decode($valoresJson['parcelas']); 
                    foreach ($parcelas as $key2 => $dados2) {
                        $linha['VALOR_PARCELA'] = $dados2->valor;
                    }
                }
               
                $linha['EVENTO'] = $evento;
              
                // Se necessário, adicione mais tratamentos para outros campos do JSON aqui
            }

            // Retorna os dados tratados e a quantidade de colunas
            return array(
                'dados'     => $dados,
                'colunas'   => $result->getFieldCount() +4
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }

    private function relatorioVariaveis13($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(9);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            B.SALARIO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME  AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
       
        WHERE
        A.Tipo ='9' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
   
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();
            
            // Itera sobre cada linha para extrair a 'justificativa' do JSON
            foreach ($dados as &$linha) {
                
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Mensal';
                }else{
                    $tipo = 'Complementar';
                }
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                $linha['VALOR'] = moeda(number_format($linha['SALARIO'] * 0.5, 2, '.', ''));
                // Verifica se 'justificativa' existe no JSON e adiciona ao array
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
               
                $linha['EVENTO'] = $param->auxilio_13salario_evento;
                // Se necessário, adicione mais tratamentos para outros campos do JSON aqui
            }

            // Retorna os dados tratados e a quantidade de colunas
            return array(
                'dados'     => $dados,
                'colunas'   => $result->getFieldCount() + 1
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }

    private function relatoriosCopart($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(8);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
    
        WHERE
        A.Tipo ='8' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
    
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();

            $dadosFinais = []; // Array para armazenar os dados finais com dependentes

            // Itera sobre cada linha para extrair os dependentes do JSON
            foreach ($dados as $linha) {
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
               
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Bradesco';
                    $linha['EVENTO'] = $param->auxilio_coparticipacao_evento;
                }else{
                    $tipo = 'Unimed';
                    $linha['EVENTO'] = $param->auxilio_coparticipacao2_evento;
                }
               
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                // Verifica se existe o campo 'dependentes' no JSON e se é um array
                if (isset($valoresJson['dependentes'])) {
                    // Para cada dependente, cria uma nova linha com os dados originais e adiciona o nome do dependente
                    $depend = json_decode($valoresJson['dependentes']);
                    
                    foreach ( $depend as $dependente) {
                        $novaLinha = $linha; // Copia a linha original

                        $depend =  $mParam->dadosFuncSUb($dependente->chapa);
                        $novaLinha['CHAPA'] = $dependente->chapa; // Adiciona o nome do dependente
                        $novaLinha['NOME'] = $dependente->nome; // Adiciona o nome do dependente
                        $novaLinha['VALOR'] = $dependente->valor; // Adiciona o nome do dependente
                        $novaLinha['CODSITUACAO'] =  $depend[0]['CODSITUACAO']; ; // Adiciona o nome do dependente
                        
                        $novaLinha['CENTROCUSTO'] =  $depend[0]['CENTROCUSTO']; // Adiciona o nome do dependente
                        $novaLinha['DESC_CUST'] =  $depend[0]['DESC_CUST']; // Adiciona o nome do dependente
                        $novaLinha['CODSECAO'] =  $depend[0]['CODSECAO']; // Adiciona o nome do dependente
                        $novaLinha['SECAO'] =  $depend[0]['SECAO']; // Adiciona o nome do dependente
                        $novaLinha['FUNCAO'] =  $depend[0]['FUNCAO']; // Adiciona o nome do dependente
                        
                        $dadosFinais[] = $novaLinha; // Adiciona a nova linha ao array final
                    }
                } else {
                    // Caso não haja dependentes, adiciona a linha original sem modificar
                    $linha['Nome_dependente'] = 'Não informado';
                    $dadosFinais[] = $linha;
                }
            }

            // Retorna os dados finais tratados e a quantidade de colunas
            return array(
                'dados'     => $dadosFinais,
                'colunas'   => $result->getFieldCount() + 8
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }
    private function relatorioVariaveisMoradia($request)
    {
      

         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(6);
     
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME  AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
       
        WHERE
        A.Tipo ='6' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
    
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();
            
            // Itera sobre cada linha para extrair a 'justificativa' do JSON
            foreach ($dados as &$linha) {
                
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Mensal';
                }else{
                    $tipo = 'Complementar';
                }
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                // Verifica se 'justificativa' existe no JSON e adiciona ao array
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
                $linha['EVENTO'] = $param->auxilio_moradia_evento;
                // Se necessário, adicione mais tratamentos para outros campos do JSON aqui
            }

            // Retorna os dados tratados e a quantidade de colunas
            return array(
                'dados'     => $dados,
                'colunas'   => $result->getFieldCount() +1
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }

    private function relatorioVariaveisPCD($request)
    {
         $mParam = Model('Variaveis/VariaveisModel');


        $param =  $mParam->getParametros(4);
        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.chapa = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }
        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }
        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND C.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }
      

        if($request['dataIni'] && $request['dataFim'] ){
            $filtroDatas = "AND A.dtcad BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'";
        }
        
        $query = "
        SELECT 
            A.id as ID_REQUISIÇÂO,
            A.tiporeq AS TIPO_REQUISIÇÂO,
            A.chapa as CHAPA,
            B.NOME as NOME,
            B.CODFUNCAO as FUNCAO,
            B.CODSITUACAO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            B.CODSECAO,
            C.DESCRICAO AS SECAO,
            CONVERT(VARCHAR, A.dtcad, 103) DATA_CADASTRO,
            A.valores,
            D.NOME AS USUARIO_SOLICITANTE,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS DATA_APROVACAO,
            (SELECT  TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_APROVACAO,
            (SELECT observacao FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(5,2) ) AS JUSTIFICATIVA_APROV,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(6,7) ) AS DATA_APROV_RH,
            (SELECT   TOP 1 Z.NOME +' : ' + XX.CHAPA FROM zcrmportal_variaveis_aprovacao INNER JOIN zcrmportal_usuario X ON zcrmportal_variaveis_aprovacao.id_user = X.id INNER JOIN ".DBRM_BANCO."..PPESSOA Z ON X.login = Z.CPF COLLATE Latin1_General_CI_AS INNER JOIN ".DBRM_BANCO."..PFUNC XX ON Z.CODIGO = XX.CODPESSOA WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) AND XX.CODSITUACAO = 'A'  ) AS USUARIO_RH,
            (SELECT TOP 1   CONVERT(VARCHAR, A.dtcad, 103) AS dtcad FROM zcrmportal_variaveis_aprovacao WHERE zcrmportal_variaveis_aprovacao.id_requisicao = A.id AND zcrmportal_variaveis_aprovacao.nivel_apr_area IN(3) ) AS DATA_SINCRONIZAÇÂO,
           CONCAT(SUBSTRING(A.periodo, 6, 2), '/', SUBSTRING(A.periodo, 1, 4)) AS PERIODO_SINC
        FROM zcrmportal_variaveis_req A
        INNER JOIN zcrmportal_usuario ON A.usucad = zcrmportal_usuario.id
        INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS
        INNER JOIN ".DBRM_BANCO."..PSECAO C ON B.CODSECAO = C.codigo
        INNER JOIN  ".DBRM_BANCO."..GCCUSTO on gccusto.codcoligada = C.codcoligada and gccusto.codccusto = C.nrocencustocont
        INNER JOIN ".DBRM_BANCO."..PPESSOA D ON zcrmportal_usuario.login = D.CPF COLLATE Latin1_General_CI_AS
    
        WHERE
        A.Tipo ='4' 
        ".$FiltroChapa."
        ".$FiltroFuncao."
        ".$FiltroSecao."
        ".$filtroDatas."
    ";
    
            
        // Executa a query
        $result = $this->dbportal->query($query);

        // Se houver resultados, vamos tratá-los
        if ($result) {
            // Extrai os resultados como um array associativo
            $dados = $result->getResultArray();

            $dadosFinais = []; // Array para armazenar os dados finais com dependentes

            // Itera sobre cada linha para extrair os dependentes do JSON
            foreach ($dados as $linha) {
                // Decodifica o campo 'valores' para um array PHP
                $valoresJson = json_decode($linha['valores'], true);
               
                $linha[ 'JUSTIFICATIVA'] = isset($valoresJson['justificativa']) ? $valoresJson['justificativa'] : 'Não informado';
                if($linha['TIPO_REQUISIÇÂO'] == 1){
                    $tipo = 'Mensal';
                }else{
                    $tipo = 'Complementar';
                }
                $linha['EVENTO'] = $param->reembolso_cpd_evento;
                $linha['TIPO_REQUISIÇÂO'] =  $tipo ;
                // Verifica se existe o campo 'dependentes' no JSON e se é um array
                if (isset($valoresJson['dependentes'])) {
                    // Para cada dependente, cria uma nova linha com os dados originais e adiciona o nome do dependente
                    $depend = json_decode($valoresJson['dependentes']);
                    
                    foreach ( $depend as $dependente) {
                        $novaLinha = $linha; // Copia a linha original
                        $novaLinha['Nome_dependente'] = $dependente->nome; // Adiciona o nome do dependente
                        $novaLinha['Valor_Dependente'] = $dependente->valor; // Adiciona o nome do dependente
                        $dadosFinais[] = $novaLinha; // Adiciona a nova linha ao array final
                    }
                } else {
                    // Caso não haja dependentes, adiciona a linha original sem modificar
                    $linha['Nome_dependente'] = 'Não informado';
                    $dadosFinais[] = $linha;
                }
            }

            // Retorna os dados finais tratados e a quantidade de colunas
            return array(
                'dados'     => $dadosFinais,
                'colunas'   => $result->getFieldCount() + 3
            );
        } else {
            return false; // Retorna falso se a query não retornar resultados
        }
    }

    private function relatorioAbono($request)
    {


       // print_r($request);
         //exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND E.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND A.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " E.CODIGO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
            WITH 

                PAR AS 
                    (
                        SELECT 
                        COLIGADA = '{$this->coligada}'
                    )

            SELECT ".rtrim($select, ',')." FROM (
                SELECT
               
                    A.CODCOLIGADA AS 'COLIGADA'
                    ,A.CODFILIAL AS 'CODIGO_FILIAL'  
                    ,C.NOMEFANTASIA AS 'NOME_FILIAL'
                    ,A.CHAPA 
                    ,A.NOME
                    ,A.CODFUNCAO AS 'CODIGO_FUNCAO'
                    ,D.NOME AS 'FUNCAO'
                    ,E.NROCENCUSTOCONT AS 'CC'
                    ,F.NOME  AS 'DESCRICAO_CC'
                    ,E.CODIGO AS 'SECAO'
                    ,E.DESCRICAO AS 'DESCRICAO_SECAO'
                    ,G.CODABONO AS 'CODIGO_ABONO'
                    ,H.DESCRICAO AS 'TIPO_ABONO'
                    ,CONVERT(VARCHAR, G.DATA, 103)  AS 'DATA'
                    ,DBO.MINTOTIME(G.HORAINICIO) AS 'HORA_INICIO'
                    ,DBO.MINTOTIME(G.HORAFIM) AS 'HORA_TERMINO'
                    

                FROM 
                    PFUNC A (NOLOCK)
                    INNER JOIN PAR P ON A.CODCOLIGADA = P.COLIGADA
                    INNER JOIN GFILIAL C      (NOLOCK) ON A.CODCOLIGADA = C.CODCOLIGADA AND A.CODFILIAL = C.CODFILIAL
                    INNER JOIN PFUNCAO D      (NOLOCK) ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO    = A.CODFUNCAO
                    INNER JOIN PSECAO  E      (NOLOCK) ON A.CODCOLIGADA = E.CODCOLIGADA AND A.CODSECAO  = E.CODIGO
                    LEFT JOIN GCCUSTO  F      (NOLOCK) ON F.CODCOLIGADA = E.CODCOLIGADA AND F.CODCCUSTO = E.NROCENCUSTOCONT
                    LEFT JOIN AABONFUN G      (NOLOCK) ON G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA
                    LEFT JOIN AABONO   H      (NOLOCK) ON H.CODCOLIGADA = G.CODCOLIGADA AND H.CODIGO = G.CODABONO


                WHERE
                    A.CODCOLIGADA = P.COLIGADA
                    AND G.CODABONO IS NOT NULL
                    AND G.DATA BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'
                    ".$FiltroSecao."
                    ".$FiltroChapa."
                    ".$FiltroFuncao."
                    {$qr_secao}
            )X
            
        ";

        $result = $this->dbrm->query($query);



        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    private function relatorioAfastamentos($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
            
            WITH
                PAR AS (
                    SELECT
                    COLIGADA = '{$this->coligada}'
                )

            SELECT ".rtrim($select, ',')." FROM (
            SELECT 
            A.CODCOLIGADA      AS COLIGADA
            ,B.CODFILIAL        AS CODIGO_FILIAL
            ,A.CHAPA            AS CHAPA
            ,B.NOME             AS NOME
            ,B.CODFUNCAO        AS CODIGO_FUNCAO
            ,D.NOME             AS FUNCAO
            ,C.NROCENCUSTOCONT  AS CC
            ,E.NOME             AS DESCRICAO_CC
            ,B.CODSECAO         AS SECAO
            ,C.DESCRICAO        AS DESCRICAO_SECAO
            ,F.DESCRICAO        AS TIPO_AFASTAMENTO
            ,CONVERT(VARCHAR, A.DTINICIO, 103)         AS INICIO_AFASTAMENTO
            ,CONVERT(VARCHAR, A.DTFINAL, 103)          AS TERMINO_AFASTAMENTO
            ,ISNULL(CONVERT(VARCHAR,DATEDIFF(DAY,A.DTINICIO,A.DTFINAL)+1),'em andamento') QTD_DIAS

            FROM PFHSTAFT (NOLOCK) A
            INNER JOIN PFUNC     (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA 
            INNER JOIN PSECAO    (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODSECAO = C.CODIGO 
            INNER JOIN PFUNCAO   (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODFUNCAO = D.CODIGO
            LEFT  JOIN PCCUSTO   (NOLOCK) E ON C.CODCOLIGADA = E.CODCOLIGADA AND C.NROCENCUSTOCONT = E.CODCCUSTO
            INNER JOIN PCODAFAST (NOLOCK) F ON A.TIPO = F.CODCLIENTE
            INNER JOIN PAR                M ON A.CODCOLIGADA = M.COLIGADA


            WHERE
            B.CODSITUACAO NOT IN ('D')
            --AND A.CHAPA IN ('050000018'/*Diurno*/ ,'050000017'/*Noturno*/ , '050000028'/*Refeição*/)
            ".$FiltroSecao."
            ".$FiltroChapa."
            ".$FiltroFuncao."
            {$qr_secao}

            AND 
            	(
            		'".$request['dataIni']."' BETWEEN A.DTINICIO AND coalesce(A.DTFINAL, '2050-12-31')
            			OR
                    '".$request['dataFim']."' BETWEEN A.DTINICIO AND coalesce(A.DTFINAL, '2050-12-31')
            	)


            )X
        ";


// exit('<pre>'.$query);
        $result = $this->dbrm->query($query);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }


    private function relatorioPonto($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
            
            WITH
            PAR AS (
            SELECT
            COLIGADA = '{$this->coligada}'
            ,INICIO   = '".$request['dataIni']."'
            ,FIM      = '".$request['dataFim']."'
            ),

            FAIXAS AS (

            SELECT 
            P.CODCOLIGADA, CODPARCOL, CODEVEPTO, CODEVEREL, PORCINCID 
            ,(SELECT CODCALC FROM AEVECALC R WHERE P.CODCOLIGADA = R.CODCOLIGADA AND P.CODEVEPTO = R.CODEVENTO) FAIXA
            FROM AEVEPCOL P, PEVENTO Q
            WHERE 
            P.CODCOLIGADA = Q.CODCOLIGADA
            AND P.CODEVEREL = Q.CODIGO
            AND concat(P.CODCOLIGADA,' - ',P.CODEVEPTO) IN (
            --eventos de cálculos
            SELECT concat(codcoligada,' - ',codevento) CHAVE FROM AEVECALC
            WHERE CODCALC BETWEEN '0018' AND '0027'
            )

            )

            --SELECT * FROM FAIXAS WHERE CODPARCOL = '012' AND CODCOLIGADA = 1 AND 

            SELECT ".rtrim($select, ',')." FROM (
                SELECT 
                A.CODCOLIGADA      AS COLIGADA
                ,B.CODFILIAL        AS CODIGO_FILIAL
                ,A.CHAPA            AS CHAPA
                ,B.NOME             AS NOME
                ,B.CODFUNCAO        AS CODIGO_FUNCAO
                ,D.NOME             AS FUNCAO
                ,C.NROCENCUSTOCONT  AS CC
                ,E.NOME             AS DESCRICAO_CC
                ,B.CODSECAO         AS SECAO
                ,C.DESCRICAO        AS DESCRICAO_SECAO
                ,F.CODPARCOL        AS CODIGO_SINDICATO
                ,G.DESCRICAO        AS SINDICATO
                ,CONVERT(varchar, A.DATA, 103)  AS DATA
                ,(SELECT
                    max(CONCAT(bb.id, ' - ', CAST(bb.descricao AS VARCHAR)))
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func aa
                    JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motivos bb ON bb.id = aa.justificativa
                WHERE
                        aa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                    AND aa.coligada = A.CODCOLIGADA
                    AND aa.dtponto = A.DATA
                ) JUSTIFICATIVA_EXTRA
                ,(CASE WHEN DATEPART(WEEKDAY,A.DATA) = 1 THEN 'Domingo'
                WHEN DATEPART(WEEKDAY,A.DATA) = 2 THEN 'Segunda' 
                WHEN DATEPART(WEEKDAY,A.DATA) = 3 THEN 'Terça'
                WHEN DATEPART(WEEKDAY,A.DATA) = 4 THEN 'Quarta'
                WHEN DATEPART(WEEKDAY,A.DATA) = 5 THEN 'Quinta'
                WHEN DATEPART(WEEKDAY,A.DATA) = 6 THEN 'Sexta'
                WHEN DATEPART(WEEKDAY,A.DATA) = 7 THEN 'Sabado'
                END) AS DIA_SEMANA

                ,(SELECT 
                CONCAT('IndiceDia ', CODINDICE, '  --> ') +
                COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                ) ESCALA
                
                ,dbo.MINTOTIME(BASE) AS JORNADA
                ,(SELECT 
                TOP 1 P.CODHORARIO
                FROM PFHSTHOR P
                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                ORDER BY DTMUDANCA DESC) AS HORARIO

                ,(SELECT 
                TOP 1 Q.DESCRICAO
                FROM PFHSTHOR P
                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                ORDER BY DTMUDANCA DESC) AS DESC_HORARIO

                ,(SELECT BATIDAS FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) ENTRADA

                ,(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) IS NULL THEN NULL
                WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) <= 3 THEN dbo.MINTOTIME('0')
                WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 4
                THEN CASE WHEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))
                - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) IS NULL THEN ''
                ELSE CASE WHEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5)) <
                      DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) )
                      THEN  
                  dbo.MINTOTIME((DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))+1440
                - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) )      
                  ELSE     
                dbo.MINTOTIME((DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))
                - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) )
                  END
                END
                END ) HR_REFEICAO

                ,ISNULL((
                    SELECT
						case when ta.horas_de_direcao = 0 then null
						else
							RIGHT('0' + CAST(ta.horas_de_direcao / 60 AS VARCHAR), 2) + ':' +
							RIGHT('0' + CAST(ta.horas_de_direcao % 60 AS VARCHAR), 2) 
						end HHMM
                    FROM
                        ".DBPORTAL_BANCO."..zcrmportal_ponto_ats_totalizador ta
                        INNER JOIN PPESSOA tc ON tc.CODIGO = B.CODPESSOA
                    WHERE
                            tc.CPF = ta.cpf COLLATE Latin1_General_CI_AS
                        and ta.data_gravacao = A.DATA

                ),'') HRS_DIRECAO
                

                ,ISNULL((

                    SELECT
						case when ta.horas_em_espera = 0 then null
						else
							RIGHT('0' + CAST(ta.horas_em_espera / 60 AS VARCHAR), 2) + ':' +
							RIGHT('0' + CAST(ta.horas_em_espera % 60 AS VARCHAR), 2) 
						end HHMM
                    FROM
                        ".DBPORTAL_BANCO."..zcrmportal_ponto_ats_totalizador ta
                        INNER JOIN PPESSOA tc ON tc.CODIGO = B.CODPESSOA
                    WHERE
                            tc.CPF = ta.cpf COLLATE Latin1_General_CI_AS
                        and ta.data_gravacao = A.DATA

                ),'') HRS_EM_ESPERA
                

                ,ISNULL((

                    SELECT
						case when ta.horas_parado = 0 then null
						else
							RIGHT('0' + CAST(ta.horas_parado / 60 AS VARCHAR), 2) + ':' +
							RIGHT('0' + CAST(ta.horas_parado % 60 AS VARCHAR), 2) 
						end HHMM
                    FROM
                        ".DBPORTAL_BANCO."..zcrmportal_ponto_ats_totalizador ta
                        INNER JOIN PPESSOA tc ON tc.CODIGO = B.CODPESSOA
                    WHERE
                            tc.CPF = ta.cpf COLLATE Latin1_General_CI_AS
                        and ta.data_gravacao = A.DATA

                ),'') HRS_PARADO
                

                ,(SELECT TOP 1 R.DESCRICAO  
                FROM AOCORRENCIACALCULADA P, AABONFUN Q, AABONO R 
                WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND A.DATA = P.DATAREFERENCIA AND P.TIPOOCORRENCIA IN ('ABO')
                AND P.CODCOLIGADA = Q.CODCOLIGADA AND P.CHAPA = Q.CHAPA AND FORMAT(P.INICIO, 'yyyy-MM-dd 00:00:00') = Q.DATA 
                AND DATEDIFF(minute, 0, CONVERT(VARCHAR(8),P.INICIO,108)) = Q.HORAINICIO AND DATEDIFF(minute, 0, CONVERT(VARCHAR(8),FIM,108)) = Q.HORAFIM 
                AND Q.CODCOLIGADA = R.CODCOLIGADA AND Q.CODABONO = R.CODIGO) TIPO_ABONO
                ,(CASE WHEN HTRAB = 0 THEN '' ELSE dbo.MINTOTIME(A.HTRAB) END) HRS_NORMAIS
                /* Se no horario estiver que tem q fazer no MINIMo X minutos, vai aparecer mesmo q ele tenha apenas 2 batidas no dia || Se no horario nao for flexivel o horario, não entrará como AREF e sim atraso compensado e extra compensado mesmo ele fazendo 1h e vai aparece apenas o tempo q for dentro do horario de refeicao do horario*/
                ,(CASE WHEN ABONO = 0 THEN '' ELSE dbo.MINTOTIME(A.ABONO) END) HRS_ABONADAS
                ,(CASE WHEN FALTA = 0 THEN '' ELSE dbo.MINTOTIME(A.FALTA) END) HRS_FALTAS
                ,(CASE WHEN ATRASO = 0 THEN '' ELSE dbo.MINTOTIME(A.ATRASO) END) HRS_ATRASOS
                ,(SELECT dbo.MINTOTIME(SUM(HORAFIM-HORAINICIO)) FROM AEESPFUN P WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND A.DATA = P.DATA) HRS_ESPERA

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                /* 1a FAIXA AMOVFUNDIA*/
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 1.5

                UNION ALL

                /* 1a FAIXA BANCO DE HORAS - Tem percentual ao enviar para o Banco de horas, por isso a Qtde Executada nao bate*/
                SELECT 
                SUM(ISNULL(VALOR,0)) HORA
                FROM ABANCOHORFUNDETALHE P, FAIXAS Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVENTO   = Q.CODEVEPTO
                AND F.CODPARCOL   = Q.CODPARCOL
                AND Q.PORCINCID   = 1.5
                )X 
                ) HRS_50

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                /* 1a FAIXA AMOVFUNDIA*/
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 1.6

                UNION ALL

                /* 1a FAIXA BANCO DE HORAS - Tem percentual ao enviar para o Banco de horas, por isso a Qtde Executada nao bate*/
                SELECT 
                SUM(ISNULL(VALOR,0)) HORA
                FROM ABANCOHORFUNDETALHE P, FAIXAS Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVENTO   = Q.CODEVEPTO
                AND F.CODPARCOL   = Q.CODPARCOL
                AND Q.PORCINCID   = 1.6
                )X 
                ) HRS_60

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                /* 1a FAIXA AMOVFUNDIA*/
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 1.8

                UNION ALL

                /* 1a FAIXA BANCO DE HORAS - Tem percentual ao enviar para o Banco de horas, por isso a Qtde Executada nao bate*/
                SELECT 
                SUM(ISNULL(VALOR,0)) HORA
                FROM ABANCOHORFUNDETALHE P, FAIXAS Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVENTO   = Q.CODEVEPTO
                AND F.CODPARCOL   = Q.CODPARCOL
                AND Q.PORCINCID   = 1.8
                )X 
                ) HRS_80

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                /* 1a FAIXA AMOVFUNDIA*/
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 2.0

                UNION ALL

                /* 1a FAIXA BANCO DE HORAS - Tem percentual ao enviar para o Banco de horas, por isso a Qtde Executada nao bate*/
                SELECT 
                SUM(ISNULL(VALOR,0)) HORA
                FROM ABANCOHORFUNDETALHE P, FAIXAS Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVENTO   = Q.CODEVEPTO
                AND F.CODPARCOL   = Q.CODPARCOL
                AND Q.PORCINCID   = 2.0
                )X 
                ) HRS_100

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 0.25
                )X 
                ) AD_NOTURNO_25

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 0.30
                )X 
                ) AD_NOTURNO_30 

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 0.35
                )X 
                ) AD_NOTURNO_35

                ,(SELECT CASE WHEN SUM(ISNULL(HORA,0)) > 0 THEN dbo.mintotime(SUM(ISNULL(HORA,0))) ELSE '' END
                FROM(
                SELECT 
                SUM(ISNULL(NUMHORAS,0)) HORA
                FROM AMOVFUNDIA P, PEVENTO Q
                WHERE 
                A.CODCOLIGADA = P.CODCOLIGADA
                AND A.CHAPA       = P.CHAPA
                AND A.DATA        = P.DATA
                AND P.CODCOLIGADA = Q.CODCOLIGADA
                AND P.CODEVE      = Q.CODIGO
                AND Q.PORCINCID   = 0.40
                )X 
                ) AD_NOTURNO_40
                
                ,(SELECT (CASE WHEN SUM(VALOR) > 0 THEN '-' ELSE '' END) + dbo.mintotime(SUM(VALOR)) 
                FROM ABANCOHORFUNDETALHE P 
                WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND A.DATA = P.DATA AND P.CODEVENTO IN ('001','002')) DEBITO_BH
                
                ,(SELECT CONCAT((CASE WHEN SUM(CASE WHEN CODEVENTO IN ('001','002') THEN VALOR*-1 ELSE VALOR END) < 0 
                    THEN '-'
                            WHEN SUM(CASE WHEN CODEVENTO IN ('001','002') THEN VALOR*-1 ELSE VALOR END) > 0 THEN '+' 
                            ELSE '' END),
                dbo.MINTOTIME(SUM(CASE WHEN (CASE WHEN CODEVENTO IN ('001','002') THEN VALOR*-1 ELSE VALOR END) < 0 
                                    THEN (CASE WHEN CODEVENTO IN ('001','002') THEN VALOR*-1 ELSE VALOR END)*-1 
                                    ELSE (CASE WHEN CODEVENTO IN ('001','002') THEN VALOR*-1 ELSE VALOR END) END)) )


               

                FROM ABANCOHORFUNDETALHE P 
                WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND A.DATA = P.DATA) SALDO_BH_COM_ACRESCIMO

                FROM AAFHTFUN (NOLOCK) A
                INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA 
                INNER JOIN PSECAO  (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODSECAO = C.CODIGO 
                INNER JOIN PFUNCAO (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODFUNCAO = D.CODIGO
                LEFT  JOIN PCCUSTO (NOLOCK) E ON C.CODCOLIGADA = E.CODCOLIGADA AND C.NROCENCUSTOCONT = E.CODCCUSTO
                INNER JOIN APARFUN (NOLOCK) F ON A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA
                INNER JOIN APARCOL (NOLOCK) G ON F.CODCOLIGADA = G.CODCOLIGADA AND F.CODPARCOL = G.CODIGO
                INNER JOIN PAR              M ON A.CODCOLIGADA = M.COLIGADA AND A.DATA BETWEEN M.INICIO AND M.FIM


                WHERE
                B.CODSITUACAO NOT IN ('D')
                AND A.DATA BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'
                ".$FiltroSecao."
                ".$FiltroChapa."
                ".$FiltroFuncao."
                {$qr_secao}

            )X


        ";

//         echo '<pre>';
// echo $query;
//         exit();

        $result = $this->dbrm->query($query, [], false, '', ['Scrollable' => SQLSRV_CURSOR_FORWARD]);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }


    private function relatorioColaboradores($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = " AND C.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "  AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " COD_SECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if(!$request['rh']){
		    $qr_secao = " WHERE (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $filtroPeriodo = "";
        $filtroDataIni = date('Y-m-01');
        $filtroDataFim = date('Y-m-d');

        if(strlen(trim($request['dataFim'])) > 0 && strlen(trim($request['dataFim'])) > 0){

            $filtroDataFim  = $request['dataFim'];
            $filtroDataIni  = $request['dataIni'];

            
        }

        $filtroPeriodo = "
                AND A.DATAADMISSAO <= '{$filtroDataFim}'
                AND (A.TIPODEMISSAO IS NULL OR A.TIPODEMISSAO NOT IN ('5','6'))
                AND (A.DATADEMISSAO IS NULL OR A.DATADEMISSAO >= '{$filtroDataIni}')
            ";

        $query = "
            

        SELECT
        ".rtrim($select, ',')."

        FROM(
            SELECT 
            A.CODCOLIGADA AS 'COD_COLIGADA',
            A.CODFILIAL AS 'COD_FILIAL',
            A.CHAPA,
            A.NOME,
            -- Situação
            ISNULL((SELECT TOP 1 SS.CODCLIENTE 
                    FROM PFHSTSIT SIT
                    INNER JOIN PCODSITUACAO SS ON SS.CODCLIENTE = SIT.NOVASITUACAO
                    WHERE SIT.CODCOLIGADA = A.CODCOLIGADA
                        AND SIT.CHAPA = A.CHAPA
                        AND SIT.DATAMUDANCA <= '{$filtroDataFim}'
                    ORDER BY SIT.DATAMUDANCA DESC
                    ), S.DESCRICAO) AS CODIGO_SITUACAO,
            (CASE WHEN (ISNULL((SELECT TOP 1 SS.DESCRICAO 
                    FROM PFHSTSIT SIT
                    INNER JOIN PCODSITUACAO SS ON SS.CODCLIENTE = SIT.NOVASITUACAO
                    WHERE SIT.CODCOLIGADA = A.CODCOLIGADA
                      AND SIT.CHAPA = A.CHAPA
                      AND SIT.DATAMUDANCA <= '{$filtroDataFim}'
                    ORDER BY SIT.DATAMUDANCA DESC
                   ), S.DESCRICAO)
                           ) = 'Demitido' 
                   AND 
                   (CASE WHEN A.DATADEMISSAO BETWEEN '{$filtroDataIni}' AND '{$filtroDataFim}' THEN A.DATADEMISSAO ELSE NULL END) IS NULL 
                           THEN 'Ativo' 
                   ELSE 
                   (ISNULL((SELECT TOP 1 SS.DESCRICAO 
                    FROM PFHSTSIT SIT
                    INNER JOIN PCODSITUACAO SS ON SS.CODCLIENTE = SIT.NOVASITUACAO
                    WHERE SIT.CODCOLIGADA = A.CODCOLIGADA
                      AND SIT.CHAPA = A.CHAPA
                      AND SIT.DATAMUDANCA <= '{$filtroDataFim}'
                    ORDER BY SIT.DATAMUDANCA DESC
                   ), S.DESCRICAO)
                   ) 
                   END) AS SITUACAO,
            -- Centro de custo
            ISNULL((SELECT TOP 1 S.NROCENCUSTOCONT 
                    FROM PFHSTSEC SEC 
                    INNER JOIN PSECAO S ON SEC.CODCOLIGADA = S.CODCOLIGADA AND S.CODIGO = SEC.CODSECAO 
                    WHERE SEC.CODCOLIGADA = A.CODCOLIGADA
                        AND SEC.CHAPA = A.CHAPA
                        AND SEC.DTMUDANCA <= '{$filtroDataFim}'
                    ORDER BY SEC.DTMUDANCA DESC      
                    ), B.NROCENCUSTOCONT) AS COD_CUSTO,
            ISNULL((SELECT TOP 1 T.NOME
                    FROM PFHSTSEC SEC 
                    INNER JOIN PSECAO S ON SEC.CODCOLIGADA = S.CODCOLIGADA AND S.CODIGO = SEC.CODSECAO 
                    LEFT JOIN PCCUSTO T ON T.CODCOLIGADA = S.CODCOLIGADA AND T.CODCCUSTO = S.NROCENCUSTOCONT
                    WHERE SEC.CODCOLIGADA = A.CODCOLIGADA
                        AND SEC.CHAPA = A.CHAPA
                        AND SEC.DTMUDANCA <= '{$filtroDataFim}'
                    ORDER BY SEC.DTMUDANCA DESC      
                    ), B.NROCENCUSTOCONT) AS DESCRICAO_CUSTO,
                    
            -- seção   
            ISNULL((SELECT TOP 1 S.CODIGO 
                    FROM PFHSTSEC SEC 
                    INNER JOIN PSECAO S ON SEC.CODCOLIGADA = S.CODCOLIGADA AND S.CODIGO = SEC.CODSECAO 
                    WHERE SEC.CODCOLIGADA = A.CODCOLIGADA
                        AND SEC.CHAPA = A.CHAPA
                        AND SEC.DTMUDANCA <= '{$filtroDataFim}'
                    ORDER BY SEC.DTMUDANCA DESC      
                    ), B.CODIGO) AS COD_SECAO,
                    
            ISNULL((SELECT TOP 1 S.DESCRICAO
                    FROM PFHSTSEC SEC 
                    INNER JOIN PSECAO S ON SEC.CODCOLIGADA = S.CODCOLIGADA AND S.CODIGO = SEC.CODSECAO 
                    LEFT JOIN PCCUSTO T ON T.CODCOLIGADA = S.CODCOLIGADA AND T.CODCCUSTO = S.NROCENCUSTOCONT
                    WHERE SEC.CODCOLIGADA = A.CODCOLIGADA
                        AND SEC.CHAPA = A.CHAPA
                        AND SEC.DTMUDANCA <= '{$filtroDataFim}'
                    ORDER BY SEC.DTMUDANCA DESC      
                    ), B.DESCRICAO) AS DESCRICAO_SECAO,             
                    
                    
            -- Função
            ISNULL((SELECT TOP 1 F.CODIGO 
                    FROM PFHSTFCO FC 
                    INNER JOIN PFUNCAO F ON FC.CODCOLIGADA = F.CODCOLIGADA AND F.CODIGO = FC.CODFUNCAO 
                    WHERE FC.CODCOLIGADA = A.CODCOLIGADA
                        AND FC.CHAPA = A.CHAPA
                        AND FC.DTMUDANCA <= '{$filtroDataFim}'
                    ORDER BY FC.DTMUDANCA DESC 
                    ), C.CODIGO) AS COD_FUNCAO,
            ISNULL((SELECT TOP 1 F.NOME 
                    FROM PFHSTFCO FC 
                    INNER JOIN PFUNCAO F ON FC.CODCOLIGADA = F.CODCOLIGADA AND F.CODIGO = FC.CODFUNCAO 
                    WHERE FC.CODCOLIGADA = A.CODCOLIGADA
                        AND FC.CHAPA = A.CHAPA
                        AND FC.DTMUDANCA <= '{$filtroDataFim}'
                    ORDER BY FC.DTMUDANCA DESC 
                    ), C.NOME) AS FUNCAO,
            -- Outras colunas da tabela original
            CONVERT(VARCHAR(10), A.DATAADMISSAO, 103)  AS DATA_ADMISSAO,
            CONVERT(VARCHAR(10), P.DTNASCIMENTO, 103)DATA_NASCIMENTO,
            ( CASE WHEN P.SEXO = 'F' THEN 'Feminino' ELSE 'Masculino' END) GENERO,
            CONVERT(VARCHAR(10), (CASE WHEN A.DATADEMISSAO BETWEEN  '{$filtroDataIni}' AND '{$filtroDataFim}' THEN A.DATADEMISSAO ELSE NULL END), 103) AS DATA_DEMISSAO,
            
            U.CODPARCOL AS COD_SIND_PONTO,
            V.DESCRICAO AS DESCRIÇÃO_SIND_PONTO,
            T.CODIGO AS COD_SIND_FOLHA,
            T.NOME AS DESCRIÇÃO_SIND_FOLHA,
            '{$filtroDataIni}' AS INIC,
            '{$filtroDataFim}' AS FIM,
            A.DATADEMISSAO
            
            ,ISNULL((SELECT TOP 1 H.CODIGO 
            FROM PFHSTHOR HOR, AHORARIO H 

            WHERE 
                HOR.CODCOLIGADA = H.CODCOLIGADA
                AND HOR.CODHORARIO = H.CODIGO 
                AND HOR.CODCOLIGADA = A.CODCOLIGADA
                AND HOR.CHAPA = A.CHAPA
                AND HOR.DTMUDANCA <= '{$filtroDataFim}'
            ORDER BY HOR.DTMUDANCA DESC
            ),A.CODHORARIO)COD_HOR  
            
            ,ISNULL((SELECT TOP 1 H.DESCRICAO 
            FROM PFHSTHOR HOR, AHORARIO H 

            WHERE 
                HOR.CODCOLIGADA = H.CODCOLIGADA
                AND HOR.CODHORARIO = H.CODIGO 
                AND HOR.CODCOLIGADA = A.CODCOLIGADA
                AND HOR.CHAPA = A.CHAPA
                AND HOR.DTMUDANCA <= '{$filtroDataFim}'
            ORDER BY HOR.DTMUDANCA DESC
            ),A.CODHORARIO)HOR          
            
            FROM PFUNC A
            INNER JOIN PSECAO B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CODSECAO = B.CODIGO
            INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
            LEFT JOIN GFILIAL GFIL ON GFIL.CODCOLIGADA = A.CODCOLIGADA AND GFIL.CODFILIAL = A.CODFILIAL
            LEFT JOIN PCODSITUACAO S ON S.CODCLIENTE = A.CODSITUACAO
            LEFT JOIN PSINDIC T ON T.CODCOLIGADA = A.CODCOLIGADA AND T.CODIGO = A.CODSINDICATO
            LEFT JOIN APARFUN U ON U.CODCOLIGADA = A.CODCOLIGADA AND U.CHAPA = A.CHAPA
            LEFT JOIN APARCOL V ON V.CODCOLIGADA = U.CODCOLIGADA AND V.CODIGO = U.CODPARCOL
            LEFT JOIN PPESSOA P ON P.CODIGO = A.CODPESSOA 

            WHERE 
            A.CODCOLIGADA = '{$this->coligada}'
            
            ".$FiltroChapa."
            ".$FiltroFuncao."
            ".$FiltroSecao."
            {$filtroPeriodo}
            )A
            {$qr_secao}
            

            ORDER BY
            A.COD_COLIGADA
            ,A.NOME
            
        ";
        //exit('<pre>'.$query);

        $result = $this->dbrm->query($query);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }


    private function relatorioHorasIninterruptas($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "

            WITH
            PAR AS (
                SELECT
                COLIGADA  = '{$this->coligada}'
                ,INICIO   = '".$request['dataIni']."'
                ,FIM      = '".$request['dataFim']."'
                ,QTDE_HORA = 360
            )
            SELECT ".rtrim($select, ',')." FROM (

            SELECT 
                COLIGADA
                , CODIGO_FILIAL
                , CHAPA
                , NOME
                , CODIGO_FUNCAO
                , FUNCAO
                , CC
                , DESCRICAO_CC
                , SECAO
                , DESCRICAO_SECAO
                , CONVERT(VARCHAR(10),DATA,103) AS DATA
                , DIA_SEMANA
                , ESCALA
                , HORARIO
                , DESC_HORARIO
                , BATIDAS
                ,(CASE WHEN ISNULL(PRIMEIRO_PAR,0) > 0 THEN 'Par1 '   + dbo.mintotime(PRIMEIRO_PAR) ELSE '' END)
                    +(CASE WHEN ISNULL(SEGUNDO_PAR,0)  > 0 THEN '  Par2 ' + dbo.mintotime(SEGUNDO_PAR) ELSE '' END)
                    +(CASE WHEN ISNULL(TERCEIRO_PAR,0) > 0 THEN '  Par3 ' + dbo.mintotime(TERCEIRO_PAR) ELSE '' END)
                    +(CASE WHEN ISNULL(QUARTO_PAR,0)   > 0 THEN '  Par4 ' + dbo.mintotime(QUARTO_PAR) ELSE '' END)
                    +(CASE WHEN ISNULL(QUINTO_PAR,0)   > 0 THEN '  Par5 ' + dbo.mintotime(QUINTO_PAR) ELSE '' END) HORAS_TRABALHADAS
            FROM (
                SELECT 
                    A.CODCOLIGADA      AS COLIGADA
                    ,B.CODFILIAL        AS CODIGO_FILIAL
                    ,A.CHAPA            AS CHAPA
                    ,B.NOME             AS NOME
                    ,B.CODFUNCAO        AS CODIGO_FUNCAO
                    ,D.NOME             AS FUNCAO
                    ,C.NROCENCUSTOCONT  AS CC
                    ,E.NOME             AS DESCRICAO_CC
                    ,B.CODSECAO         AS SECAO
                    ,C.DESCRICAO        AS DESCRICAO_SECAO
                    ,CONVERT(VARCHAR, A.DATA,103)             AS DATA
                    ,(CASE WHEN DATEPART(WEEKDAY,A.DATA) = 1 THEN 'Domingo'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 2 THEN 'Segunda' 
                    WHEN DATEPART(WEEKDAY,A.DATA) = 3 THEN 'Terça'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 4 THEN 'Quarta'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 5 THEN 'Quinta'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 6 THEN 'Sexta'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 7 THEN 'Sabado'
                    END) AS DIA_SEMANA
                    
                    ,(SELECT 
                    CONCAT('IndiceDia ', CODINDICE, '  --> ') +
                    COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                    FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                    ) ESCALA
                    
                    ,(SELECT 
                            TOP 1 P.CODHORARIO
                        FROM PFHSTHOR P
                            INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                        WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                        ORDER BY DTMUDANCA DESC) AS HORARIO
                        
                    ,(SELECT 
                            TOP 1 Q.DESCRICAO
                        FROM PFHSTHOR P
                            INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                        WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                        ORDER BY DTMUDANCA DESC) AS DESC_HORARIO
                    
                    ,(SELECT BATIDAS FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BATIDAS
                    
                    ,(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 2
                        THEN CASE WHEN SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5) 
                                        < SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),1,5)
                                    THEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5))+1440
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),1,5)) )
                                    ELSE (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5))
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),1,5)) )
                                END
                        ELSE NULL
                    END ) PRIMEIRO_PAR
                
                    ,(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 4
                        THEN CASE WHEN SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),16,5) 
                                        < SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5)
                                    THEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),16,5))+1440
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5)) )
                                    ELSE (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),16,5))
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5)) )
                                END
                        ELSE NULL
                    END ) SEGUNDO_PAR
                
                    ,(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 6
                        THEN CASE WHEN SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),26,5) 
                                        < SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),21,5)
                                    THEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),26,5))+1440
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),21,5)) )
                                    ELSE (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),26,5))
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),21,5)) )
                                END
                        ELSE NULL
                    END ) TERCEIRO_PAR
                
                    ,(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 8
                        THEN CASE WHEN SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),36,5) 
                                        < SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),31,5)
                                    THEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),36,5))+1440
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),31,5)) )
                                    ELSE (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),36,5))
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),31,5)) )
                                END
                        ELSE NULL
                    END ) QUARTO_PAR
                
                    ,(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 8
                        THEN CASE WHEN SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),46,5) 
                                        < SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),41,5)
                                    THEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),46,5))+1440
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),41,5)) )
                                    ELSE (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),46,5))
                                            - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),41,5)) )
                                END
                        ELSE NULL
                    END ) QUINTO_PAR

                    ,M.QTDE_HORA	
                    
                FROM AAFHTFUN (NOLOCK) A
                    INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA 
                    INNER JOIN PSECAO  (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODSECAO = C.CODIGO 
                    INNER JOIN PFUNCAO (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODFUNCAO = D.CODIGO
                    LEFT  JOIN PCCUSTO (NOLOCK) E ON C.CODCOLIGADA = E.CODCOLIGADA AND C.NROCENCUSTOCONT = E.CODCCUSTO
                    INNER JOIN APARFUN (NOLOCK) F ON A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA
                    INNER JOIN APARCOL (NOLOCK) G ON F.CODCOLIGADA = G.CODCOLIGADA AND F.CODPARCOL = G.CODIGO
                    INNER JOIN PAR              M ON A.CODCOLIGADA = M.COLIGADA AND A.DATA BETWEEN M.INICIO AND M.FIM
                
                
                WHERE
                B.CODSITUACAO NOT IN ('D')
                ".$FiltroSecao."
                ".$FiltroChapa."
                ".$FiltroFuncao."
                {$qr_secao}
            )X
            WHERE (PRIMEIRO_PAR > QTDE_HORA OR SEGUNDO_PAR > QTDE_HORA OR TERCEIRO_PAR > QTDE_HORA OR QUARTO_PAR > QTDE_HORA OR QUINTO_PAR > QTDE_HORA)

            )Z
            
        ";

        $result = $this->dbrm->query($query);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }


    private function relatorioInterjornadaMenos11H($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
      
            WITH
            PAR AS (
                SELECT
                COLIGADA  = '{$this->coligada}'
                ,INICIO   = '".$request['dataIni']."'
                ,FIM      = '".$request['dataFim']."'
            )
            SELECT ".rtrim($select, ',')." FROM (
            SELECT 
                COLIGADA
                , CODIGO_FILIAL
                , CHAPA
                , NOME
                , CODIGO_FUNCAO
                , FUNCAO
                , CC
                , DESCRICAO_CC
                , SECAO
                , DESCRICAO_SECAO
                , CONVERT(VARCHAR(10),DATA,103) AS DATA
                , DIA_SEMANA
                , ESCALA
                , JORNADA
                , HORARIO
                , HORARIO_NOME
                , BATIDAS
                , ' 11h entre jornadas desrespeitado [' +
                    (CASE WHEN DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) IS NULL or DATEDIFF(minute ,BAT_anterior, BAT_atual) > 660 THEN '[Bat.Inválida]'
                        ELSE DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) END) 
                    + ']' INTERJORNADA

            FROM (
                SELECT 
                    A.CODCOLIGADA      AS COLIGADA
                    ,B.CODFILIAL        AS CODIGO_FILIAL
                    ,A.CHAPA            AS CHAPA
                    ,B.NOME             AS NOME
                    ,B.CODFUNCAO        AS CODIGO_FUNCAO
                    ,D.NOME             AS FUNCAO
                    ,C.NROCENCUSTOCONT  AS CC
                    ,E.NOME             AS DESCRICAO_CC
                    ,B.CODSECAO         AS SECAO
                    ,C.DESCRICAO        AS DESCRICAO_SECAO
                    ,CONVERT(VARCHAR, A.DATA,103)             AS DATA
                    ,(CASE WHEN DATEPART(WEEKDAY,A.DATA) = 1 THEN 'Domingo'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 2 THEN 'Segunda' 
                    WHEN DATEPART(WEEKDAY,A.DATA) = 3 THEN 'Terça'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 4 THEN 'Quarta'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 5 THEN 'Quinta'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 6 THEN 'Sexta'
                    WHEN DATEPART(WEEKDAY,A.DATA) = 7 THEN 'Sabado'
                    END) AS DIA_SEMANA
                    
                    ,(SELECT 
                    CONCAT('IndiceDia ', CODINDICE, '  --> ') +
                    COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                    FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                    ) ESCALA
                    ,dbo.MINTOTIME(BASE) AS JORNADA
                    ,(SELECT 
                            TOP 1 P.CODHORARIO
                        FROM PFHSTHOR P
                            INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                        WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                        ORDER BY DTMUDANCA DESC) AS HORARIO
                    
                    ,(SELECT 
                            TOP 1 Q.DESCRICAO
                        FROM PFHSTHOR P
                            INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                        WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                        ORDER BY DTMUDANCA DESC) AS HORARIO_NOME
                    
                    ,(SELECT BATIDAS FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BATIDAS
                    
                    ,H.DESCRICAO INTERJORNADA
                    
                    ,(SELECT MAX(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATA-1 = M.DATAREFERENCIA AND NATUREZA = 1) BAT_anterior
                    ,(SELECT MIN(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATA = M.DATAREFERENCIA AND NATUREZA = 0) BAT_atual
                    
                FROM AAFHTFUN (NOLOCK) A
                    INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA 
                    INNER JOIN PSECAO  (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODSECAO = C.CODIGO 
                    INNER JOIN PFUNCAO (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODFUNCAO = D.CODIGO
                    LEFT  JOIN PCCUSTO (NOLOCK) E ON C.CODCOLIGADA = E.CODCOLIGADA AND C.NROCENCUSTOCONT = E.CODCCUSTO
                    INNER JOIN APARFUN (NOLOCK) F ON A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA
                    INNER JOIN APARCOL (NOLOCK) G ON F.CODCOLIGADA = G.CODCOLIGADA AND F.CODPARCOL = G.CODIGO
                    INNER JOIN AAVISOCALCULADO  H ON A.CODCOLIGADA = H.CODCOLIGADA AND A.CHAPA = H.CHAPA AND A.DATA = H.DATAREFERENCIA AND H.CODAVISO = 1
                    INNER JOIN PAR              M ON A.CODCOLIGADA = M.COLIGADA AND A.DATA BETWEEN M.INICIO AND M.FIM

                WHERE
                B.CODSITUACAO NOT IN ('D')
                ".$FiltroSecao."
                ".$FiltroChapa."
                ".$FiltroFuncao."
                {$qr_secao}
                
            )X

            )K
            
        ";

        // exit('<pre>'.$query);
        $result = $this->dbrm->query($query);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    private function relatorioMais6DiasSemDescanso($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
      
        WITH
        PAR AS (
            SELECT
            COLIGADA  = '{$this->coligada}'
            ,INICIO   = '".$request['dataIni']."'
            ,FIM      = '".$request['dataFim']."'
        )
        
            SELECT ".rtrim($select, ',')." FROM (

                  SELECT
                       COLIGADA
                      ,CODIGO_FILIAL
                      ,CHAPA
                      ,NOME
                      ,CODIGO_FUNCAO
                      ,FUNCAO
                      ,CC
                      ,DESCRICAO_CC
                      ,SECAO
                      ,DESCRICAO_SECAO
                      ,CONVERT(VARCHAR(10),DATA,103) DATA
                      ,DIA_SEMANA
                      ,ESCALA
                      ,JORNADA
                      ,HORARIO
                      ,HORARIO_NOME
                      ,BATIDAS
                      ,'7 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103) PERIODO
                      ,(UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) VALOR
                
                
                  FROM(
                  
                      SELECT
                         A.CODCOLIGADA      AS COLIGADA
                        ,B.CODFILIAL        AS CODIGO_FILIAL
                        ,A.CHAPA            AS CHAPA
                        ,B.NOME             AS NOME
                        ,B.CODFUNCAO        AS CODIGO_FUNCAO
                        ,D.NOME             AS FUNCAO
                        ,C.NROCENCUSTOCONT  AS CC
                        ,E.NOME             AS DESCRICAO_CC
                        ,B.CODSECAO         AS SECAO
                        ,C.DESCRICAO        AS DESCRICAO_SECAO
                        ,A.DATA             AS DATA
                        ,(CASE WHEN DATEPART(WEEKDAY,A.DATA) = 1 THEN 'Domingo'
                           WHEN DATEPART(WEEKDAY,A.DATA) = 2 THEN 'Segunda' 
                           WHEN DATEPART(WEEKDAY,A.DATA) = 3 THEN 'Terça'
                           WHEN DATEPART(WEEKDAY,A.DATA) = 4 THEN 'Quarta'
                           WHEN DATEPART(WEEKDAY,A.DATA) = 5 THEN 'Quinta'
                           WHEN DATEPART(WEEKDAY,A.DATA) = 6 THEN 'Sexta'
                           WHEN DATEPART(WEEKDAY,A.DATA) = 7 THEN 'Sabado'
                         END) AS DIA_SEMANA
                
                         /* Ficando Lento quando excesso de linha */
                        ,(SELECT 
                                  CONCAT('IndiceDia ', CODINDICE, '  --> ') +
                                  COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                                  COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                                  COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                                  COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                              FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                          ) ESCALA
                        ,dbo.MINTOTIME(BASE) AS JORNADA
                        ,(SELECT 
                                TOP 1 P.CODHORARIO
                              FROM PFHSTHOR P
                                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                            WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                            ORDER BY DTMUDANCA DESC) AS HORARIO
                        
                        ,(SELECT 
                                TOP 1 Q.DESCRICAO
                            FROM PFHSTHOR P
                                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                            WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                            ORDER BY DTMUDANCA DESC) AS HORARIO_NOME
                        
                        ,(SELECT BATIDAS FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BATIDAS
                      
                        ,(SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.HTRAB >0)UM
                        ,(SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.HTRAB >0)DOIS
                        ,(SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.HTRAB >0)TRES
                        ,(SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.HTRAB >0)QUATRO
                        ,(SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.HTRAB >0)CINCO
                        ,(SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-5 = B.DATA AND B.HTRAB >0)SEIS
                        ,(SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)SETE
                          
                        ,(SELECT B.DATA FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)DATAFINAL
                              
                          
                    FROM AAFHTFUN (NOLOCK) A
                        INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA 
                        INNER JOIN PSECAO  (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODSECAO = C.CODIGO 
                        INNER JOIN PFUNCAO (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODFUNCAO = D.CODIGO
                        LEFT  JOIN PCCUSTO (NOLOCK) E ON C.CODCOLIGADA = E.CODCOLIGADA AND C.NROCENCUSTOCONT = E.CODCCUSTO
                        INNER JOIN APARFUN (NOLOCK) F ON A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA
                        INNER JOIN APARCOL (NOLOCK) G ON F.CODCOLIGADA = G.CODCOLIGADA AND F.CODPARCOL = G.CODIGO
                        INNER JOIN PAR              M ON A.CODCOLIGADA = M.COLIGADA AND A.DATA BETWEEN M.INICIO AND M.FIM
                          
                      WHERE
                      B.CODSITUACAO NOT IN ('D')
                      ".$FiltroSecao."
                      ".$FiltroChapa."
                      ".$FiltroFuncao."
                      {$qr_secao}
                                  
                  )X
                    WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1 AND SEIS = 1 AND SETE = 1

            )K
            
        ";

 
        $result = $this->dbrm->query($query);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    private function relatorioTempoInsuficienteRefeicao($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
      
        WITH
            PAR AS 
            (
                SELECT
                    COLIGADA  = '{$this->coligada}'
                    ,INICIO   = '".$request['dataIni']."'
                    ,FIM      = '".$request['dataFim']."'
                    ,QTDE     = '55'
            )


            SELECT ".rtrim($select, ',')." FROM (

                SELECT 
                    COLIGADA,
                    CODIGO_FILIAL
                    ,CHAPA
                    ,NOME
                    ,CODIGO_FUNCAO
                    ,FUNCAO
                    ,CC
                    ,DESCRICAO_CC
                    ,SECAO
                    ,DESCRICAO_SECAO
                    ,CODIGO_SINDICATO
                    ,SINDICATO
                    ,[DATA]
                    ,DIA_SEMANA
                    ,ESCALA
                    ,HORARIO
                    ,HORARIO_NOME
                    ,BATIDAS
                    ,dbo.MINTOTIME(HR_REFEICAO) AS HR_REFEICAO
                FROM (

                    SELECT 
                        A.CODCOLIGADA      AS COLIGADA
                        ,B.CODFILIAL        AS CODIGO_FILIAL
                        ,A.CHAPA            AS CHAPA
                        ,B.NOME             AS NOME
                        ,B.CODFUNCAO        AS CODIGO_FUNCAO
                        ,D.NOME             AS FUNCAO
                        ,C.NROCENCUSTOCONT  AS CC
                        ,E.NOME             AS DESCRICAO_CC
                        ,B.CODSECAO         AS SECAO
                        ,C.DESCRICAO        AS DESCRICAO_SECAO
                        ,F.CODPARCOL        AS CODIGO_SINDICATO
                        ,G.DESCRICAO        AS SINDICATO
                        ,CONVERT(VARCHAR(10),A.DATA,103)            AS DATA
                        ,(CASE WHEN DATEPART(WEEKDAY,A.DATA) = 1 THEN 'Domingo'
                        WHEN DATEPART(WEEKDAY,A.DATA) = 2 THEN 'Segunda' 
                        WHEN DATEPART(WEEKDAY,A.DATA) = 3 THEN 'Terça'
                        WHEN DATEPART(WEEKDAY,A.DATA) = 4 THEN 'Quarta'
                        WHEN DATEPART(WEEKDAY,A.DATA) = 5 THEN 'Quinta'
                        WHEN DATEPART(WEEKDAY,A.DATA) = 6 THEN 'Sexta'
                        WHEN DATEPART(WEEKDAY,A.DATA) = 7 THEN 'Sabado'
                        END) AS DIA_SEMANA
                    
                        ,(SELECT 
                            CONCAT('IndiceDia ', CODINDICE, '  --> ') +
                            COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                            COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                            COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                            COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                        FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                        ) ESCALA
                    
                        ,(SELECT 
                                TOP 1 P.CODHORARIO
                            FROM PFHSTHOR P
                                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                            WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                            ORDER BY DTMUDANCA DESC) AS HORARIO
                    
                        ,(SELECT 
                                TOP 1 Q.DESCRICAO
                            FROM PFHSTHOR P
                                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                            WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                            ORDER BY DTMUDANCA DESC) AS HORARIO_NOME
                    
                        ,(SELECT BATIDAS FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BATIDAS
                    
                        ,(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) IS NULL THEN NULL
                            WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) <= 3 THEN '0'
                            WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 4
                            THEN CASE WHEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))
                                - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) IS NULL THEN ''
                            ELSE ((DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))
                                - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(BATIDAS,' ',''),'+','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) )
                                END
                        END ) HR_REFEICAO
                        ,M.QTDE
                    
                    FROM AAFHTFUN (NOLOCK) A
                        INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA 
                        INNER JOIN PSECAO  (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODSECAO = C.CODIGO 
                        INNER JOIN PFUNCAO (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODFUNCAO = D.CODIGO
                        LEFT  JOIN PCCUSTO (NOLOCK) E ON C.CODCOLIGADA = E.CODCOLIGADA AND C.NROCENCUSTOCONT = E.CODCCUSTO
                        INNER JOIN APARFUN (NOLOCK) F ON A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA
                        INNER JOIN APARCOL (NOLOCK) G ON F.CODCOLIGADA = G.CODCOLIGADA AND F.CODPARCOL = G.CODIGO
                        INNER JOIN PAR              M ON A.CODCOLIGADA = M.COLIGADA AND A.DATA BETWEEN M.INICIO AND M.FIM
                    
                    
                    WHERE
                    B.CODSITUACAO NOT IN ('D')
                    ".$FiltroSecao."
                    ".$FiltroChapa."
                    ".$FiltroFuncao."
                    {$qr_secao}
                )X
                WHERE HR_REFEICAO < QTDE
            )K
            
        ";


        $result = $this->dbrm->query($query);
        
        
            return ($result)
                ? array(
                    'dados'     => $result->getResultArray(),
                    'colunas'   => $result->getFieldCount()
                )
                : false;
        
    }

    private function relatorioHorarioBritanico($request)
    {


        // print_r($request);
        // exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND D.CODIGO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND C.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " D.CODIGO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
      
        WITH
            PAR AS 
            (
                SELECT
                    COLIGADA  = '{$this->coligada}'
                    ,INICIO   = '".$request['dataIni']."'
                    ,FIM      = '".$request['dataFim']."'
                   
            )


            SELECT ".rtrim($select, ',')." FROM (

                SELECT
                    COLIGADA
                    , CODIGO_FILIAL
                    , CHAPA
                    , NOME
                    , CODIGO_FUNCAO
                    , FUNCAO
                    , CC
                    , DESCRICAO_CC
                    , SECAO
                    , DESCRICAO_SECAO
                    , SINDICATO
                    , CONVERT(VARCHAR(10),DATA,103) DATA
                    , DIA_SEMANA
                    , ESCALA
                    , JORNADA
                    , HORARIO
                    , DESCRICAO_HORARIO
                    , BATIDAS
                    
                    , CONCAT(HOR_UM,' ',HOR_DOIS,' ',HOR_TRES,' ',HOR_QUATRO) HORARIO_BRITANICO
                
                
                FROM(   
                    SELECT
                            A.CODCOLIGADA COLIGADA
                            ,C.CODFILIAL CODIGO_FILIAL  
                            ,A.CHAPA
                            ,C.NOME
                            ,C.CODFUNCAO CODIGO_FUNCAO
                            ,F.NOME FUNCAO
                            ,D.NROCENCUSTOCONT CC
                            ,FF.NOME DESCRICAO_CC
                            ,D.CODIGO SECAO
                            ,D.DESCRICAO DESCRICAO_SECAO
                            ,I.DESCRICAO SINDICATO
                            ,A.DATA
                            ,(CASE WHEN DATEPART(WEEKDAY,A.DATA) = 1 THEN 'Domingo'
                                WHEN DATEPART(WEEKDAY,A.DATA) = 2 THEN 'Segunda' 
                                WHEN DATEPART(WEEKDAY,A.DATA) = 3 THEN 'Terça'
                                WHEN DATEPART(WEEKDAY,A.DATA) = 4 THEN 'Quarta'
                                WHEN DATEPART(WEEKDAY,A.DATA) = 5 THEN 'Quinta'
                                WHEN DATEPART(WEEKDAY,A.DATA) = 6 THEN 'Sexta'
                                WHEN DATEPART(WEEKDAY,A.DATA) = 7 THEN 'Sabado'
                            END) AS DIA_SEMANA
                        ,(SELECT 
                            CONCAT('IndiceDia ', CODINDICE, '  --> ') +
                            COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                            COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                            COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                            COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                            FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                        ) ESCALA
                        
                        ,dbo.MINTOTIME(BASE) AS JORNADA
                        ,(SELECT CODHORARIO FROM PFHSTHOR M (NOLOCK) WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
                            (SELECT MAX(DTMUDANCA) FROM PFHSTHOR M (NOLOCK) WHERE M.CODCOLIGADA = C.CODCOLIGADA AND M.CHAPA = C.CHAPA AND DTMUDANCA <= A.DATA)) HORARIO
                        
                        ,(SELECT MM.DESCRICAO FROM PFHSTHOR M (NOLOCK), AHORARIO MM (NOLOCK) WHERE MM.CODCOLIGADA = M.CODCOLIGADA AND MM.CODIGO = M.CODHORARIO AND M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
                            (SELECT MAX(DTMUDANCA) FROM PFHSTHOR M (NOLOCK) WHERE M.CODCOLIGADA = C.CODCOLIGADA AND M.CHAPA = C.CHAPA AND DTMUDANCA <= A.DATA)) DESCRICAO_HORARIO
                        
                        
                    
                        ,(SELECT BATIDAS FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BATIDAS
                        
                
                            ,(SELECT SUBSTRING(BATIDAS,2,5) FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BAT_UM
                            ,(SELECT (dbo.MINTOTIME(ENTRADA1)) ESCALA FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) )HOR_UM
                    
                    
                            ,(SELECT SUBSTRING(BATIDAS,9,5) FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BAT_DOIS
                            ,(SELECT (dbo.MINTOTIME(SAIDA1)) ESCALA FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) )HOR_DOIS
                        
                        
                            ,(SELECT SUBSTRING(BATIDAS,16,5) FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BAT_TRES
                            ,(SELECT (dbo.MINTOTIME(ENTRADA2)) ESCALA FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) )HOR_TRES
                            
                            
                            ,(SELECT SUBSTRING(BATIDAS,23,5) FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) BAT_QUATRO
                            ,(SELECT (dbo.MINTOTIME(SAIDA2)) ESCALA FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) )HOR_QUATRO
                            
                            
                            
                    FROM AAFHTFUN A (NOLOCK)
                        
                        INNER JOIN ABATFUN B (NOLOCK)
                        ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA = (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)
                        AND B.STATUS NOT IN ('T')
                    
                        INNER JOIN PFUNC C (NOLOCK)
                        ON A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA 
                    
                        INNER JOIN PSECAO D (NOLOCK)
                        ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODIGO = C.CODSECAO
                        
                        INNER JOIN PAR                     ON PAR.COLIGADA = A.CODCOLIGADA
                        INNER JOIN GCCUSTO  FF     (NOLOCK) ON FF.CODCOLIGADA = D.CODCOLIGADA AND FF.CODCCUSTO = D.NROCENCUSTOCONT
                        INNER JOIN PFUNCAO F      (NOLOCK) ON F.CODCOLIGADA = C.CODCOLIGADA  AND F.CODIGO = C.CODFUNCAO
                        INNER JOIN APARFUN H       (NOLOCK) ON H.CODCOLIGADA = C.CODCOLIGADA  AND H.CHAPA     = C.CHAPA
                        INNER JOIN APARCOL I       (NOLOCK) ON I.CODCOLIGADA = H.CODCOLIGADA  AND I.CODIGO    = H.CODPARCOL
                    
                    
                    
                    
                    WHERE 
                        A.CODCOLIGADA = PAR.COLIGADA
                        AND A.DATA  BETWEEN PAR.INICIO AND PAR.FIM
                        ".$FiltroSecao."
                        ".$FiltroChapa."
                        ".$FiltroFuncao."
                        {$qr_secao}
                
                )X
                
                WHERE
                    (BAT_UM = HOR_UM OR
                    BAT_DOIS = HOR_DOIS OR
                    BAT_TRES = HOR_TRES OR
                    BAT_QUATRO = HOR_QUATRO)
                    
                    
            )K
            
        ";

 
        $result = $this->dbrm->query($query);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    public function isLiderOrGestor()
    {

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			return 1;
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes && !$isLider){
			return 2;
		}
		//-----------------------------------------

        return false;

    }

    private function relatorioSaldoBancoHoras($request)
    {
        
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND B.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " B.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = " 
            SELECT ".rtrim($select, ',')." FROM (
                SELECT CODCOLIGADA, CHAPA, NOME, CASE WHEN SALDO < 0 THEN '-'+dbo.MINTOTIME(SALDO*-1) ELSE dbo.MINTOTIME(SALDO) END SALDO FROM (
                SELECT 
                    A.CODCOLIGADA,
                    A.CHAPA,
                    B.NOME,
                    SUM((CASE WHEN CODEVENTO IN ('001','002') AND VALOR > 0
                    THEN ((VALOR-(VALORCOMPENSADO+VALORLANCADO))*-1) ELSE VALOR-((VALORCOMPENSADO+VALORLANCADO))END)) SALDO
                FROM 
                    ABANCOHORFUNDETALHE A,
                    PFUNC B
                    JOIN APARFUN C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CHAPA = B.CHAPA
                    LEFT JOIN ALIMBANCOHOR D ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODPARCOL = C.CODPARCOL AND '".$request['dataFim']."' BETWEEN DATAINICIO AND (DATAINICIO + LIMDIASCOMPENSACAO -1)
                WHERE 
                    A.CODCOLIGADA = '{$this->coligada}'
                    AND A.DATA >= '1900-01-01'
                    AND A.DATA <= '".$request['dataFim']."'
                    AND A.CHAPA = B.CHAPA
                    AND A.CODCOLIGADA = B.CODCOLIGADA
                    AND B.CODSITUACAO NOT IN ('D')
                    {$FiltroSecao}
                    {$FiltroChapa}
                    {$FiltroFuncao}
                    {$qr_secao}
                GROUP BY 
                    A.CODCOLIGADA,
                    A.CHAPA,
                    B.NOME,
                    (DATAINICIO + LIMDIASCOMPENSACAO -1)
                )Y
            )X
		";
        $result = $this->dbrm->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    private function relatorioExtratoBancoHoras($request)
    {
        
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND pfunc.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND pfunc.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND pfunc.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " pfunc.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " pfunc.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = " 
        
WITH

PAR AS
(
	SELECT
		 '{$this->coligada}'    COLIGADA
		,'".(int)date('Y', strtotime($request['dataFim']))."' ANO
		,'".(int)date('m', strtotime($request['dataFim']))."'    MES
		,CAST(CONVERT(VARCHAR, '".(int)date('Y', strtotime($request['dataFim']))."') + '-' + CONVERT(VARCHAR, '".date('m', strtotime($request['dataFim']))."') + '-' + CONVERT(VARCHAR, '".date('d', strtotime($request['dataFim']))."') AS DATETIME) ULT_DIAPONTO
)


SELECT 
    ".rtrim($select, ',')."

FROM (

	SELECT
		 CODCOLIGADA
		,CHAPA
		,NOME
		,CODFILIAL
		,NOMESECAO
		,CODCUSTO
		,NOMECUSTO
		,SINDICATO
		,INICIO_PERIODO
		,FIM_PERIODO
		,DATA
		,CASE WHEN SALDO_ANTERIOR < 0 THEN '-'+dbo.MINTOTIME(SALDO_ANTERIOR*-1) ELSE dbo.MINTOTIME(SALDO_ANTERIOR) END SALDO_INICIAL
		,OCORRENCIA
		,CASE WHEN HORAS_POSITIVAS < 0 THEN '-'+dbo.MINTOTIME(HORAS_POSITIVAS*-1) ELSE dbo.MINTOTIME(HORAS_POSITIVAS) END HORAS_POSITIVAS
		,CASE WHEN HORAS_NEGATIVAS < 0 THEN '-'+dbo.MINTOTIME(HORAS_NEGATIVAS*-1) ELSE dbo.MINTOTIME(HORAS_NEGATIVAS) END HORAS_NEGATIVAS 
		,(CASE WHEN (SALDO_ANTERIOR+HORAS_POSITIVAS-HORAS_NEGATIVAS) < 0 THEN '-'+dbo.MINTOTIME((SALDO_ANTERIOR+HORAS_POSITIVAS-HORAS_NEGATIVAS)*-1) ELSE dbo.MINTOTIME((SALDO_ANTERIOR+HORAS_POSITIVAS-HORAS_NEGATIVAS)) END) TOTAL
		
		,SALDO_ANTERIOR SALDO_ANTERIOR_minute
		,HORAS_POSITIVAS HORAS_POSITIVAS_minute
		,HORAS_NEGATIVAS HORAS_NEGATIVAS_minute
		,(SALDO_ANTERIOR+HORAS_POSITIVAS-HORAS_NEGATIVAS) TOTAL__minute
	FROM (
		
		select	 
        	 pfunc.codcoligada																	as [CODCOLIGADA]
            ,pfunc.chapa																		as [CHAPA]
            ,pfunc.nome																			as [NOME]
            ,pfunc.codfilial																	as [CODFILIAL]
            ,psecao.descricao																	as [NOMESECAO]
            ,gccusto.codccusto																	as [CODCUSTO]
            ,gccusto.nome																		as [NOMECUSTO]
            ,aparcol.descricao																	as [SINDICATO]
            ,convert(VARCHAR, aperiodo.iniciomensal,103)										as [INICIO_PERIODO]
            ,convert(VARCHAR, aperiodo.fimmensal,103)											as [FIM_PERIODO]
            ,convert(VARCHAR, abancohorfundetalhe.data,103)										as [DATA]

			,(SELECT 
				SUM((CASE WHEN P.PROVDESCBASE IN ('D') 
						  THEN ((VALOR-((CASE WHEN FIMPERMESALTERADO >= Q.ULT_DIAPONTO THEN 0 ELSE VALORCOMPENSADO END)
						  				  +(CASE WHEN FIMPERMESALTERADO >= Q.ULT_DIAPONTO THEN 0 ELSE VALORLANCADO END)))*-1) 
						  ELSE VALOR-(((CASE WHEN FIMPERMESALTERADO >= Q.ULT_DIAPONTO THEN 0 ELSE VALORCOMPENSADO END)
						  				  +(CASE WHEN FIMPERMESALTERADO >= Q.ULT_DIAPONTO THEN 0 ELSE VALORLANCADO END))) END)) SALDO
			  FROM abancohorfundetalhe M
				join aparfun  N  on N.codcoligada = M.codcoligada and N.chapa = M.chapa
          		join aevepcol O  on O.codcoligada = N.codcoligada and O.codparcol = N.codparcol and O.codevepto = M.codevento
          		join pevento  P  on P.codcoligada = O.codcoligada and P.codigo = O.codevebanco
          		JOIN PAR      Q  ON M.CODCOLIGADA = Q.COLIGADA
			  WHERE 
			 	  abancohorfundetalhe.CODCOLIGADA = M.CODCOLIGADA 
			  AND abancohorfundetalhe.CHAPA = M.CHAPA 
			  AND M.DATA < abancohorfundetalhe.DATA )                                           AS SALDO_ANTERIOR

			,(case when pevento.provdescbase in ('p','b') then 'CRÉDITO'
             	  else 'DÉBITO'
              end)																		     	as [OCORRENCIA]
              
            ,isnull(sum((case when pevento.provdescbase in ('p','b') then abancohorfundetalhe.valor
             	  else null
              end)),0)																			as [HORAS_POSITIVAS]
              
            ,isnull(sum((case when pevento.provdescbase in ('d')	 then abancohorfundetalhe.valor
                  else null
              END)),0)																			as [HORAS_NEGATIVAS]
                
            ,pfuncao.nome																		as [funcao]
            ,pfunc.salario																		as [salario]
                
            ,sum(case when pevento.provdescbase in ('p','b') then (abancohorfundetalhe.valor)
                  else null
             END)																				as [horaspositivas]
               
            ,sum(case when pevento.provdescbase in ('d')	 then (abancohorfundetalhe.valor)
                  else null
             END)																				as [horasnegativas]
                
            ,datefromparts(2019,8,01)    												as [periodo]

		from pfunc
          	JOIN PAR                       ON PFUNC.CODCOLIGADA = PAR.COLIGADA
        	join abancohorfundetalhe       on pfunc.codcoligada = abancohorfundetalhe.codcoligada	and	pfunc.chapa = abancohorfundetalhe.chapa
          	join psecao                    on pfunc.codcoligada = psecao.codcoligada and pfunc.codsecao = psecao.codigo
          	join pfuncao                   on pfunc.codcoligada = pfuncao.codcoligada and pfunc.codfuncao = pfuncao.codigo
          	join aperiodo                  on aperiodo.codcoligada = abancohorfundetalhe.codcoligada
          	join gccusto                   on gccusto.codcoligada = psecao.codcoligada and gccusto.codccusto = psecao.nrocencustocont
          	join aparfun        	       on aparfun.codcoligada = pfunc.codcoligada and	aparfun.chapa = pfunc.chapa
          	join aevepcol                  on aevepcol.codcoligada = aparfun.codcoligada and	aevepcol.codparcol = aparfun.codparcol and aevepcol.codevepto = abancohorfundetalhe.codevento
          	join pevento                   on pevento.codcoligada = aevepcol.codcoligada and pevento.codigo = aevepcol.codevebanco
          	join aperiodo aperiodoant      on aperiodoant.codcoligada = abancohorfundetalhe.codcoligada and aperiodoant.mescomp = case when PAR.MES = 1 then 12 else PAR.MES-1 END and	aperiodoant.anocomp = case when PAR.MES = 1 then PAR.ANO-1 else PAR.ANO end
          	left outer join asaldobancohor on asaldobancohor.codcoligada = pfunc.codcoligada and asaldobancohor.chapa = pfunc.chapa and	asaldobancohor.inicioper >= aperiodoant.iniciomensal and	asaldobancohor.fimper <= aperiodoant.fimmensal
          	join aparcol        	       on aparcol.codcoligada = aparfun.codcoligada and aparcol.codigo = aparfun.codparcol

        where	abancohorfundetalhe.data >= aperiodo.iniciomensal
          and	abancohorfundetalhe.data <= aperiodo.fimmensal
          and	aperiodo.mescomp = PAR.MES
          and	aperiodo.anocomp = PAR.ANO
          {$FiltroSecao}
            {$FiltroChapa}
            {$FiltroFuncao}
            {$qr_secao}
   
        group by pfunc.codcoligada
                ,pfunc.codfilial
                ,psecao.descricao
                ,gccusto.codccusto
                ,gccusto.nome
                ,pfunc.chapa
                ,pfunc.nome
                ,pfuncao.nome
                ,pfunc.salario
                ,abancohorfundetalhe.data
                ,pevento.provdescbase
                ,aparcol.descricao
                ,aperiodo.iniciomensal
                ,aperiodo.fimmensal
                ,abancohorfundetalhe.CODCOLIGADA
                ,abancohorfundetalhe.CHAPA
                ,PAR.ANO, PAR.MES 
                
		)X
      
	)Z








/*







            SELECT ".rtrim($select, ',')." FROM (
                SELECT
                CODCOLIGADA, CHAPA, NOME, CODFILIAL, NOMESECAO, CODCUSTO, NOMECUSTO, SINDICATO, INICIO_PERIODO, FIM_PERIODO, DATA_OCORRENCIA, OCORRENCIA, 
                    CASE WHEN HORAS_POSITIVAS < 0 THEN '-'+dbo.MINTOTIME(HORAS_POSITIVAS*-1) ELSE dbo.MINTOTIME(HORAS_POSITIVAS) END HORAS_POSITIVAS, 
                    CASE WHEN HORAS_NEGATIVAS < 0 THEN '-'+dbo.MINTOTIME(HORAS_NEGATIVAS*-1) ELSE dbo.MINTOTIME(HORAS_NEGATIVAS) END HORAS_NEGATIVAS, 
                    CASE WHEN SALDO < 0 THEN '-'+dbo.MINTOTIME(SALDO*-1) ELSE dbo.MINTOTIME(SALDO) END SALDO_INICIAL
             
            FROM (
            select	 
                 pfunc.codcoligada																	as [CODCOLIGADA]
                 ,pfunc.chapa																		as [CHAPA]
                ,pfunc.nome																			as [NOME]
                ,pfunc.codfilial																	as [CODFILIAL]
                ,psecao.descricao																	as [NOMESECAO]
                ,gccusto.codccusto																	as [CODCUSTO]
                ,gccusto.nome																		as [NOMECUSTO]
                ,aparcol.descricao																	as [SINDICATO]
                ,convert(VARCHAR, aperiodo.iniciomensal,103)										as [INICIO_PERIODO]
                ,convert(VARCHAR, aperiodo.fimmensal,103)											as [FIM_PERIODO]
                ,convert(VARCHAR, abancohorfundetalhe.data,103)										as [DATA_OCORRENCIA]
                ,pevento.descricao																	as [OCORRENCIA]
                ,case when pevento.provdescbase in ('p','b') then abancohorfundetalhe.valor
                      else null
                 end																				as [HORAS_POSITIVAS]
                 ,case when pevento.provdescbase in ('d')	 then abancohorfundetalhe.valor
                      else null
                 end																				as [HORAS_NEGATIVAS]
                 ,(sum(asaldobancohor.extraant)  + sum(asaldobancohor.extraatu)) -
                 (sum(asaldobancohor.atrasoant) + sum(asaldobancohor.atrasoatu)+
                  sum(asaldobancohor.faltaant)  + sum(asaldobancohor.faltaatu))						as [SALDO]
                
                ,pfuncao.nome																		as [funcao]
                ,pfunc.salario																		as [salario]
                
                ,case when pevento.provdescbase in ('p','b') then pevento.descricao
                      else null
                 end																				as [eventopositivo]
                
                ,case when pevento.provdescbase in ('p','b') then dbo.fn_hora(abancohorfundetalhe.valor)
                      else null
                 end																				as [horaspositivas]
                ,case when pevento.provdescbase in ('d')	 then pevento.descricao
                      else null
                 end																				as [eventonegativo]
                
                ,case when pevento.provdescbase in ('d')	 then dbo.fn_hora(abancohorfundetalhe.valor)
                      else null
                 end																				as [horasnegativas]
                
                ,datefromparts(2019,8,01)														as [periodo]
                
                
                
    
    
        from pfunc
          join abancohorfundetalhe
           on	pfunc.codcoligada = abancohorfundetalhe.codcoligada
            and	pfunc.chapa = abancohorfundetalhe.chapa
          join psecao 
           on	pfunc.codcoligada = psecao.codcoligada
            and pfunc.codsecao = psecao.codigo
          join pfuncao 
           on	pfunc.codcoligada = pfuncao.codcoligada
            and pfunc.codfuncao = pfuncao.codigo
          join aperiodo
           on	aperiodo.codcoligada = abancohorfundetalhe.codcoligada
          join gccusto
           on	gccusto.codcoligada = psecao.codcoligada
            and	gccusto.codccusto = psecao.nrocencustocont
          join	aparfun
           on	aparfun.codcoligada = pfunc.codcoligada
            and	aparfun.chapa = pfunc.chapa
          join	aevepcol
           on	aevepcol.codcoligada = aparfun.codcoligada
            and	aevepcol.codparcol = aparfun.codparcol
            and	aevepcol.codevepto = abancohorfundetalhe.codevento
          join	pevento
           on	pevento.codcoligada = aevepcol.codcoligada
            and	pevento.codigo = aevepcol.codevebanco
          join	aperiodo aperiodoant
           on	aperiodoant.codcoligada = abancohorfundetalhe.codcoligada
            and	aperiodoant.mescomp = case when ".(int)date('m', strtotime($request['dataFim']))." = 1 then 12 else ".(int)date('m', strtotime($request['dataFim']))."-1 end
            and	aperiodoant.anocomp = case when ".(int)date('m', strtotime($request['dataFim']))." = 1 then ".(int)date('Y', strtotime($request['dataFim']))."-1 else ".(int)date('Y', strtotime($request['dataFim']))." end
          left outer join asaldobancohor 
           on	asaldobancohor.codcoligada = pfunc.codcoligada
            and	asaldobancohor.chapa = pfunc.chapa
            and	asaldobancohor.inicioper >= aperiodoant.iniciomensal
            and	asaldobancohor.fimper <= aperiodoant.fimmensal
          join	aparcol
           on	aparcol.codcoligada = aparfun.codcoligada
            and aparcol.codigo = aparfun.codparcol
    
    
        where	abancohorfundetalhe.data >= aperiodo.iniciomensal
          and	abancohorfundetalhe.data <= aperiodo.fimmensal
          
          and	aperiodo.mescomp = ".(int)date('m', strtotime($request['dataFim']))."
          and	aperiodo.anocomp = ".(int)date('Y', strtotime($request['dataFim']))."
          
          and	abancohorfundetalhe.codcoligada = '{$this->coligada}'
          {$FiltroSecao}
            {$FiltroChapa}
            {$FiltroFuncao}
            {$qr_secao}
    
        group by pfunc.codcoligada
                ,pfunc.codfilial
                ,psecao.descricao
                ,gccusto.codccusto
                ,gccusto.nome
                ,pfunc.chapa
                ,pfunc.nome
                ,pfuncao.nome
                ,pfunc.salario
                ,abancohorfundetalhe.data
                ,pevento.provdescbase
                ,pevento.descricao
                ,abancohorfundetalhe.valor
                ,aparcol.descricao
                ,aperiodo.iniciomensal
                ,aperiodo.fimmensal
    )X
      
            )Z*/
		";

        // exit('<pre>'.$query);
        $result = $this->dbrm->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    public function relatorioGeralEquipe($request)
    {
        
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND A.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND A.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " A.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
            SELECT ".rtrim($select, ',')." FROM (
                SELECT
                    A.CODCOLIGADA,
                    A.CHAPA,
                    A.NOME,
                    B.NOME NOMEFUNCAO,
                    SUBSTRING(A.CODSECAO, 5, 5) CODCUSTO,
                    F.NOME DESCRICAO_CUSTO,
                    A.CODSECAO,
                    C.DESCRICAO NOMESECAO,
                    (
                        SELECT 
                            MAX(p.NOME)
                        FROM 
                            ".DBPORTAL_BANCO."..zcrmportal_hierarquia_lider_func_ponto lf
                            INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_hierarquia_lider_ponto l ON l.id = lf.id_lider
                            INNER JOIN PFUNC p ON p.CODCOLIGADA = l.coligada AND p.CHAPA = l.chapa COLLATE Latin1_General_CI_AS
                        WHERE 
                                lf.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                            AND lf.coligada = A.CODCOLIGADA
                            AND lf.inativo IS NULL
                    ) LIDER,
                    (
                        SELECT 
                            MAX(l.operacao)
                        FROM 
                            ".DBPORTAL_BANCO."..zcrmportal_hierarquia_lider_func_ponto lf
                            INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_hierarquia_lider_ponto l ON l.id = lf.id_lider
                            INNER JOIN PFUNC p ON p.CODCOLIGADA = l.coligada AND p.CHAPA = l.chapa COLLATE Latin1_General_CI_AS
                        WHERE 
                                lf.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                            AND lf.coligada = A.CODCOLIGADA
                            AND lf.inativo IS NULL
                    ) OPERACAO,
                    D.GESTOR_NOME GESTOR
                FROM
                    PFUNC A
                    INNER JOIN PFUNCAO B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
                    INNER JOIN PSECAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
                    LEFT JOIN GCCUSTO  F      (NOLOCK) ON F.CODCOLIGADA = C.CODCOLIGADA AND F.CODCCUSTO = C.NROCENCUSTOCONT
                    LEFT JOIN ".DBPORTAL_BANCO."..GESTOR_CHAPA D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CHAPA = A.CHAPA
                WHERE
                        A.CODCOLIGADA = '{$this->coligada}'
                    AND A.CODSITUACAO NOT IN ('D')
                    {$FiltroSecao}
                    {$FiltroChapa}
                    {$FiltroFuncao}
                    {$qr_secao}
            )X
        ";
        // echo '<pre>';
        // echo $query;exit();
        $result = $this->dbrm->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    public function relatorioBatidaColetadaDigitada($request)
    {

        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
            SELECT ".rtrim($select, ',')." FROM (

                SELECT
                    CODCOLIGADA, 
                    CODFILIAL, 
                    FILIAL, 
                    CHAPA, 
                    NOME, 
                    FUNCAO, 
                    CODSECAO, 
                    SECAO, 
                    CODCUSTO, 
                    CENTROCUSTO, 
                    DATA, 
                    BATIDA, 
                    STATUS, 
                    (CASE WHEN STATUS <> 'C' THEN JUSTIFICATIVA ELSE '' END) JUSTIFICATIVA,
                    CONVERT(VARCHAR, DATA_REGISTRO, 103) DATA_REGISTRO,
                    CONVERT(VARCHAR, DATA_REGISTRO, 108) HORA_REGISTRO,
                    DATA_APROVADOR,
                    HORA_APROVADOR,
                    CASE
                        WHEN STATUS = 'C' THEN NULL
                        WHEN STATUS = 'D' AND USUARIO IS NULL THEN 'Inserida RM'
                        ELSE USUARIO
                    END USUARIO, 
                    CASE
                        WHEN STATUS IN ('C', 'E') THEN NULL
                        WHEN STATUS = 'D' AND APROVADOR IS NULL THEN 'Inserida RM'
                        ELSE APROVADOR
                    END APROVADOR,
                    CASE WHEN POSSUI_ANEXO = 1 THEN 'SIM' ELSE 
                        (CASE WHEN STATUS = 'C' THEN '' ELSE 'NÃO' END)
                    END POSSUI_ANEXO
                FROM (
                    SELECT
                    A.CODCOLIGADA,
                    C.CODFILIAL,
                    C.NOME FILIAL,
                    B.CHAPA,
                    B.NOME,
                    D.NOME FUNCAO,
                    B.CODSECAO,
                    E.DESCRICAO SECAO,
                    SUBSTRING(B.CODSECAO,5, 5) CODCUSTO,
                    F.NOME CENTROCUSTO,
                    CONVERT(VARCHAR, A.DATA, 103) DATA,
                    DBO.MINTOTIME(A.BATIDA) BATIDA,
                    A.STATUS,
                    (
                        SELECT
                            MAX(COALESCE(pa.justent1, pa.justent2, pa.justent3, pa.justent4, pa.justent5, pa.justsai1, pa.justsai2, pa.justsai3, pa.justsai4, pa.justsai5))
                        FROM
                        ".DBPORTAL_BANCO."..zcrmportal_ponto_horas pa
                    WHERE
                            pa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                        AND pa.coligada = A.CODCOLIGADA
                        AND pa.dtponto = A.DATA
                        AND pa.movimento = 1
                        AND pa.status = 'S'
                        AND COALESCE(pa.ent1, pa.ent2, pa.ent3, pa.ent4, pa.ent5, pa.sai1, pa.sai2, pa.sai3, pa.sai4, pa.sai5) = A.BATIDA
                    ) JUSTIFICATIVA,
                    (
                        SELECT
                            MAX(pa.dtcadastro)
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_horas pa
                        WHERE
                                pa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                            AND pa.coligada = A.CODCOLIGADA
                            AND pa.dtponto = A.DATA
                            AND pa.movimento = 1
                            AND pa.status = 'S'
                            AND COALESCE(pa.ent1, pa.ent2, pa.ent3, pa.ent4, pa.ent5, pa.sai1, pa.sai2, pa.sai3, pa.sai4, pa.sai5) = A.BATIDA
                    ) DATA_REGISTRO,
                    CONVERT(VARCHAR, A.RECCREATEDON, 103) DATA_APROVADOR,
                    CONVERT(VARCHAR, A.RECCREATEDON, 108) HORA_APROVADOR,
                    (
                        SELECT
                            max(pa.possui_anexo)
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_horas pa
                            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_usuario pb ON pb.id = pa.usucad
                            LEFT JOIN PPESSOA pp ON pp.CPF = pb.login COLLATE Latin1_General_CI_AS
                            LEFT JOIN PFUNC pf ON pf.CODPESSOA = pp.CODIGO
                        WHERE
                                pa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                            AND pa.coligada = A.CODCOLIGADA
                            AND pa.dtponto = A.DATA
                            AND pa.movimento = 1
                            AND pa.status = 'S'
                            AND COALESCE(pa.ent1, pa.ent2, pa.ent3, pa.ent4, pa.ent5, pa.sai1, pa.sai2, pa.sai3, pa.sai4, pa.sai5) = A.BATIDA
                    ) POSSUI_ANEXO,
                    (
                        SELECT
                            MAX(CONCAT(pf.CHAPA, ' - ',pf.NOME))
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_horas pa
                            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_usuario pb ON pb.id = pa.usucad
                            LEFT JOIN PPESSOA pp ON pp.CPF = pb.login COLLATE Latin1_General_CI_AS
                            LEFT JOIN PFUNC pf ON pf.CODPESSOA = pp.CODIGO
                        WHERE
                                pa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                            AND pa.coligada = A.CODCOLIGADA
                            AND pa.dtponto = A.DATA
                            AND pa.movimento = 1
                            AND pa.status = 'S'
                            AND COALESCE(pa.ent1, pa.ent2, pa.ent3, pa.ent4, pa.ent5, pa.sai1, pa.sai2, pa.sai3, pa.sai4, pa.sai5) = A.BATIDA
                    ) USUARIO,
                    (
                        SELECT
                            MAX(CONCAT(pf.CHAPA, ' - ',pf.NOME))
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_horas pa
                            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_usuario pb ON pb.id = pa.aprrh_user
                            LEFT JOIN PPESSOA pp ON pp.CPF = pb.login COLLATE Latin1_General_CI_AS
                            LEFT JOIN PFUNC pf ON pf.CODPESSOA = pp.CODIGO
                        WHERE
                                pa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                            AND pa.coligada = A.CODCOLIGADA
                            AND pa.dtponto = A.DATA
                            AND pa.movimento = 1
                            AND pa.status = 'S'
                            AND COALESCE(pa.ent1, pa.ent2, pa.ent3, pa.ent4, pa.ent5, pa.sai1, pa.sai2, pa.sai3, pa.sai4, pa.sai5) = A.BATIDA
                    ) APROVADOR
                    
                    FROM
                        ABATFUN A
                        INNER JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                        INNER JOIN GFILIAL C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODFILIAL = B.CODFILIAL
                        INNER JOIN PFUNCAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
                        INNER JOIN PSECAO E ON E.CODCOLIGADA = A.CODCOLIGADA AND E.CODIGO = B.CODSECAO
                        INNER JOIN PCCUSTO F ON F.CODCOLIGADA = B.CODCOLIGADA AND F.CODCCUSTO = SUBSTRING(B.CODSECAO,5, 5)
                        LEFT JOIN AJUSTBAT G ON G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND G.DATA = A.DATA AND G.BATIDA = A.BATIDA
                        
                    WHERE
                            B.CODCOLIGADA = '{$this->coligada}'
                        AND B.CODSITUACAO NOT IN ('D')
                        AND A.STATUS IN ('D')
                        AND A.DATA BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'
                        {$FiltroSecao}
                        {$FiltroChapa}
                        {$FiltroFuncao}
                        {$qr_secao}
                        AND (
                            SELECT
                                MAX(CONCAT(pf.CHAPA, ' - ',pf.NOME))
                            FROM
                                ".DBPORTAL_BANCO."..zcrmportal_ponto_horas pa
                                LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_usuario pb ON pb.id = pa.usucad
                                LEFT JOIN PPESSOA pp ON pp.CPF = pb.login COLLATE Latin1_General_CI_AS
                                LEFT JOIN PFUNC pf ON pf.CODPESSOA = pp.CODIGO
                            WHERE
                                    pa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                                AND pa.coligada = A.CODCOLIGADA
                                AND pa.dtponto = A.DATA
                                AND pa.movimento = 1
                                AND pa.status = 'S'
                                AND COALESCE(pa.ent1, pa.ent2, pa.ent3, pa.ent4, pa.ent5, pa.sai1, pa.sai2, pa.sai3, pa.sai4, pa.sai5) = A.BATIDA
                        ) IS NOT NULL

                    UNION ALL

                    SELECT
                        A.CODCOLIGADA,
                        C.CODFILIAL,
                        C.NOME FILIAL,
                        B.CHAPA,
                        B.NOME,
                        D.NOME FUNCAO,
                        B.CODSECAO,
                        E.DESCRICAO SECAO,
                        SUBSTRING(B.CODSECAO,5, 5) CODCUSTO,
                        F.NOME CENTROCUSTO,
                        CONVERT(VARCHAR, A.DATA, 103) DATA,
                        DBO.MINTOTIME(A.BATIDA) BATIDA,
                        A.STATUS,
                        G.JUSTIFICA COLLATE Latin1_General_CI_AS JUSTIFICATIVA,
                        A.RECCREATEDON DATA_REGISTRO,
                        NULL DATA_APROVADOR,
                        NULL DATA_APROVADOR,
                        NULL POSSUI_ANEXO,
                        NULL USUARIO,
                        NULL APROVADOR
                    
                    FROM
                        ABATFUN A
                        INNER JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                        INNER JOIN GFILIAL C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODFILIAL = B.CODFILIAL
                        INNER JOIN PFUNCAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
                        INNER JOIN PSECAO E ON E.CODCOLIGADA = A.CODCOLIGADA AND E.CODIGO = B.CODSECAO
                        INNER JOIN PCCUSTO F ON F.CODCOLIGADA = B.CODCOLIGADA AND F.CODCCUSTO = SUBSTRING(B.CODSECAO,5, 5)
                        LEFT JOIN AJUSTBAT G ON G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND G.DATA = A.DATA AND G.BATIDA = A.BATIDA
                        
                    WHERE
                            B.CODCOLIGADA = '{$this->coligada}'
                        AND B.CODSITUACAO NOT IN ('D')
                        AND A.STATUS IN ('C')
                        AND A.DATA BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'
                        {$FiltroSecao}
                        {$FiltroChapa}
                        {$FiltroFuncao}
                        {$qr_secao}
                    
                )X
            )Y
        ";
        $result = $this->dbrm->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    public function relatorioBatidaDigitadoExcluido($request)
    {

        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND D.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND D.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND D.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " D.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " D.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "            
                WITH PAR AS
                (
                SELECT
                    '{$this->coligada}' COLIGADA
                    ,'04/09/2022' INICIO
                    ,'04/10/2022' FIM

                ),

                ZABATFUN AS (

                SELECT * 
                FROM ABATFUN
                UNION  ALL
                SELECT *
                FROM ABATFUNAM
                )

            SELECT ".rtrim($select, ',')." FROM (
                SELECT DISTINCT * 

                FROM(
                    SELECT 
                        B.CODCOLIGADA 
                        ,D.CODFILIAL
                        ,B.CHAPA
                        ,D.NOME
                        ,F.NOME FUNCAO
                        ,E.CODIGO CODSECAO
                        ,E.DESCRICAO SECAO
                        ,E.NROCENCUSTOCONT CCUSTO 
                        ,G.NOME CUSTO 
                        ,CONVERT(VARCHAR, B.DATAHORA, 103)DATA
		                ,SUBSTRING(CONVERT(VARCHAR, B.DATAHORA, 108),0,6) BATIDA
                        ,(CASE 
                            WHEN B.IDIMPORTACAO IS NOT NULL AND PROCESSO = '13'  THEN 'E' 
                            WHEN A.STATUS      = 'D' THEN 'D'
                            WHEN A.STATUS      = 'C' THEN 'C'
                            ELSE 'X' END) STATUS
                        ,B.JUSTIFICATIVA 
                        ,B.CODUSUARIO
                        ,CONVERT(VARCHAR, B.DATAHORAALTERACAO, 103) DATA_ALTERACAO
                        ,CONVERT(VARCHAR, B.DATAHORAALTERACAO, 108) HORA_ALTERACAO
                        ,CASE WHEN B.LINHAORIGINAL <> '' THEN CONCAT(SUBSTRING(B.LINHAORIGINAL,11,2),'/', SUBSTRING(B.LINHAORIGINAL,13,2),'/', SUBSTRING(B.LINHAORIGINAL,15,4)) ELSE '' END LINHAORIGINAL_DATA
                        ,CASE WHEN B.LINHAORIGINAL <> '' THEN CONCAT(SUBSTRING(B.LINHAORIGINAL,19,2), ':', SUBSTRING(B.LINHAORIGINAL,21,2)) ELSE '' END LINHAORIGINAL_HORA
                        ,A.RECCREATEDBY USUARIO_CRIACAO
                        ,CONVERT(VARCHAR, A.RECCREATEDON, 103) DATA_REGISTRO
                        ,CONVERT(VARCHAR, A.RECCREATEDON, 108) HORA_REGISTRO
                    
                    FROM 
                        TOTVSAUDIT.AAFDT B
                        FULL JOIN ZABATFUN  A ON A.CODCOLIGADA = B.CODCOLIGADA AND A.IDAAFDT = B.ID
                        INNER JOIN PAR P     ON B.CODCOLIGADA = P.COLIGADA
                        LEFT JOIN PFUNC  D   ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CHAPA = B.CHAPA
                        INNER JOIN PSECAO E  ON E.CODCOLIGADA = D.CODCOLIGADA AND E.CODIGO = D.CODSECAO
                        INNER JOIN PFUNCAO F ON F.CODCOLIGADA = D.CODCOLIGADA AND F.CODIGO = D.CODFUNCAO 
                        LEFT JOIN PCCUSTO G  ON G.CODCOLIGADA = E.CODCOLIGADA AND G.CODCCUSTO = E.NROCENCUSTOCONT
                        
                    WHERE 
                            B.CODCOLIGADA = P.COLIGADA
                        AND CONVERT(DATE,B.DATAHORA,103) BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'
                        --AND B.LINHAORIGINAL IS NOT null
                        AND (A.RECCREATEDBY IS NULL OR  A.RECCREATEDBY  NOT LIKE '%PORT.%')
                        {$FiltroSecao}
                        {$FiltroChapa}
                        {$FiltroFuncao}
                        {$qr_secao}
                )X

                WHERE STATUS <> 'X' AND DATA_ALTERACAO IS NOT NULL  AND JUSTIFICATIVA NOT IN ('Batida já existe.') AND (
                    DATA <> LINHAORIGINAL_DATA OR BATIDA <> LINHAORIGINAL_HORA OR LINHAORIGINAL_DATA = '' OR STATUS <> 'C'
                )

            )Y
        ";
        $result = $this->dbrm->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    public function relatorioBatidReprovadas($request)
    {

        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND B.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND B.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND A.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
            SELECT ".rtrim($select, ',')." FROM (

                SELECT
                    B.CODCOLIGADA,
                    B.CODFILIAL,
                    B.CHAPA,
                    B.NOME,
                    C.NOME FUNCAO,
                    B.CODSECAO,
                    B.NOME SECAO,
                    SUBSTRING(B.CODSECAO,5, 5) CODCUSTO,
                    F.NOME CENTROCUSTO,
                    CONVERT(VARCHAR, A.dtponto, 103) DATA,
                    DBO.MINTOTIME(COALESCE(A.ent1, A.ent2, A.ent3, A.ent4, A.ent5, A.sai1, A.sai2, A.sai3, A.sai4, A.sai5)) BATIDA,
                    'R' STATUS,
                    COALESCE(A.justent1, A.justent2, A.justent3, A.justent4, A.justent5, A.justsai1, A.justsai2, A.justsai3, A.justsai4, A.justsai5) JUSTIFICATIVA,
                   
                    CONCAT(pf.CHAPA, ' - ',pf.NOME) USUARIO,
                    
                    CONVERT(VARCHAR, A.dtcadastro, 103) DATA_REGISTRO,
                    CONVERT(VARCHAR, A.dtcadastro, 108) HORA_REGISTRO,
                    
                    
                    CONCAT(pf2.CHAPA, ' - ',pf2.NOME) USUARIO_REPROVA,
                    
                    A.motivo_reprova MOTIVO_REPROVA,
                    
                    CONVERT(VARCHAR, A.dt_reprova, 103) DATA_REPROVA,
                    CONVERT(VARCHAR, A.dt_reprova, 108) HORA_REPROVA,
                    
                    CASE WHEN A.possui_anexo = 1 THEN 'SIM' ELSE 'NÃO' END POSSUI_ANEXO
                    
                FROM
                    zcrmportal_ponto_horas_reprova A
                    INNER JOIN ".DBRM_BANCO."..PFUNC B ON B.CODCOLIGADA = A.coligada AND B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
                    INNER JOIN ".DBRM_BANCO."..PFUNCAO C ON C.CODCOLIGADA = B.CODCOLIGADA AND B.CODFUNCAO = C.CODIGO
                    INNER JOIN ".DBRM_BANCO."..PSECAO E ON E.CODCOLIGADA = B.CODCOLIGADA AND E.CODIGO = B.CODSECAO
                    INNER JOIN ".DBRM_BANCO."..PCCUSTO F ON F.CODCOLIGADA = B.CODCOLIGADA AND F.CODCCUSTO = SUBSTRING(B.CODSECAO,5, 5)
                    
                    LEFT JOIN zcrmportal_usuario pb ON pb.id = A.usucad
                    LEFT JOIN ".DBRM_BANCO."..PPESSOA pp ON pp.CPF = pb.login COLLATE Latin1_General_CI_AS
                    LEFT JOIN ".DBRM_BANCO."..PFUNC pf ON pf.CODPESSOA = pp.CODIGO
                    
                    LEFT JOIN zcrmportal_usuario pb2 ON pb2.id = A.usu_reprova
                    LEFT JOIN ".DBRM_BANCO."..PPESSOA pp2 ON pp2.CPF = pb2.login COLLATE Latin1_General_CI_AS
                    LEFT JOIN ".DBRM_BANCO."..PFUNC pf2 ON pf2.CODPESSOA = pp2.CODIGO
                    
                WHERE
                        A.dtponto BETWEEN '".$request['dataIni']."' AND '".$request['dataFim']."'
                    AND A.motivo_reprova IS NOT NULL
                    AND A.coligada = '{$this->coligada}'
                    AND COALESCE(A.ent1, A.ent2, A.ent3, A.ent4, A.ent5, A.sai1, A.sai2, A.sai3, A.sai4, A.sai5) IS NOT NULL
                    {$FiltroSecao}
                    {$FiltroChapa}
                    {$FiltroFuncao}
                    {$qr_secao}
                    
            )X
        ";
        $result = $this->dbportal->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

	private function relatorioMacros($request)
    {

        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND c.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND c.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND c.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " c.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " c.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $query = "
            SELECT ".rtrim($select, ',')." FROM (
                SELECT
                    c.CHAPA,
                    c.NOME,
                    c.CODSECAO,
                    e.DESCRICAO SECAO,
                    d.NOME FUNCAO,
                    a.cpf CPF,
                    CONCAT(CONVERT(VARCHAR, a.data_inicio_status, 103),' ', CONVERT(VARCHAR, a.data_inicio_status, 8)) DATA_INICIO,
                    CONCAT(CONVERT(VARCHAR, a.data_fim_status, 103),' ', CONVERT(VARCHAR, a.data_fim_status, 8)) DATA_FIM,
                    CONCAT(CONVERT(VARCHAR, a.data_gravacao, 103),' ', CONVERT(VARCHAR, a.data_gravacao, 8)) DATA_CADASTRO,
                    a.placa PLACA,
                    a.status STATUS,
                    a.tempo TEMPO,
                    case 
                        when a.tipo_mensagem = 'Mensagem mobile' then 'Mobile'
                        when a.tipo_mensagem = 'Mensagem rastreador' then 'Mensagem'
                        when a.tipo_mensagem = 'Automatizacao rastreador' then 'Automatização'
                        else a.tipo_mensagem
                    end  ORIGEM,
                    (CASE WHEN (c.DATADEMISSAO IS NULL OR c.DATADEMISSAO >= '".$request['dataFim']."') THEN 'Ativo' ELSE 'Demitido' END) CODSITUACAO

                FROM
                    zcrmportal_ponto_ats_macro a
                    LEFT JOIN ".DBRM_BANCO."..PPESSOA b ON b.CPF = a.cpf COLLATE Latin1_General_CI_AS
                    LEFT JOIN ".DBRM_BANCO."..PFUNC c ON c.CODPESSOA = b.CODIGO AND
                        CONCAT(c.CODCOLIGADA,'-',c.CHAPA) = 
                        (
                            SELECT TOP 1 REGISTRO FROM (
                                SELECT
                                    CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                                    CASE
                                        WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                        ELSE GETDATE()
                                    END DATA
                                FROM
                                ".DBRM_BANCO."..PFUNC
                                WHERE
                                    CODPESSOA = c.CODPESSOA
                            )X WHERE CONVERT(VARCHAR, X.DATA, 23) >= CONVERT(VARCHAR, a.data_inicio_status, 23)
                            ORDER BY X. DATA ASC
                        )
                    LEFT JOIN ".DBRM_BANCO."..PFUNCAO d ON d.CODIGO = c.CODFUNCAO AND d.CODCOLIGADA = c.CODCOLIGADA
                    LEFT JOIN ".DBRM_BANCO."..PSECAO e ON e.CODIGO = c.CODSECAO AND e.CODCOLIGADA = c.CODCOLIGADA

                WHERE
                    a.tipo_mensagem != 'Inserção Manual' AND a.data_inicio_status BETWEEN '".$request['dataIni']." 00:00:00' AND '".$request['dataFim']." 23:59:59'
                    {$FiltroSecao}
                    {$FiltroChapa}
                    {$FiltroFuncao}
                    {$qr_secao}
            )Y
        ";
        // echo '<pre>'.$query;exit();
        $result = $this->dbportal->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }


    private function relatorioConferenciaPremios($request)
    {
       // print_r($request);
         //exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND CODSECAO_COLAB IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND CODFUNCAO_COLAB = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND CHAPA_COLAB = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        $query = "
            SELECT 
                NOME_PREMIO,
                CASE 
                    WHEN TIPO_REQUISICAO = 'M' THEN 'MENSAL'
                    ELSE 'COMPLEMENTAR'
                END TIPO_REQUISICAO,
                FORMAT(DT_INICIO_PONTO, 'dd/MM/yyyy') DT_INICIO_PONTO,
                FORMAT(DT_FIM_PONTO, 'dd/MM/yyyy') DT_FIM_PONTO,
                CODFILIAL_COLAB,
                CHAPA_COLAB,
                NOME_COLAB,
                FUNCAO_COLAB,
                FORMAT(DT_ADMISSAO_COLAB, 'dd/MM/yyyy') DT_ADMISSAO_COLAB,
                CCUSTO_COLAB,
                CODSECAO_COLAB,
                SECAO_COLAB,
                CODSITUACAO_COLAB,
                PERCENT_TARGET,
                PERCENT_REALIZADO,
                DIAS_FALTAS,
                DATAS_FALTAS,
                DEFLA_FALTAS,
                DIAS_AFAST,
                DATAS_AFAST,
                DEFLA_AFAST,
                DIAS_ATESTADO,
                DATAS_ATESTADO,
                DEFLA_ATESTADO,
                DIAS_FERIAS,
                DATAS_FERIAS,
                DEFLA_FERIAS,
                DIAS_ADMISSAO,
                DATAS_ADMISSAO,
                DEFLA_ADMISSAO,
                DIAS_DEMISSAO,
                DATAS_DEMISSAO,
                DEFLA_DEMISSAO,
                DIAS_TOTAL          DIAS_PERDIDOS,
                CASE 
                    WHEN (30 - DIAS_TOTAL) < 0 THEN 0
                    ELSE (30 - DIAS_TOTAL)
                END DIAS_DE_DIREITO,
                DEFLA_TOTAL         PERCENT_DEFLATOR,
                PERCENT_FINAL,
                VALOR_BASE,
                VALOR_PREMIO        VALOR_A_RECEBER,
                OBS,
                FORMAT(DATA_CRIACAO, 'dd/MM/yyyy') DATA_CRIACAO,
                CHAPA_REQUISITOR,
                NOME_REQUISITOR,
                DATA_APROV_REPROV,
                CHAPA_APROV_REPROV,
                NOME_APROV_REPROV,
                MOTIVO_REPROV,
                DATA_RH_APROV_REPROV,
                CHAPA_RH_APROV_REPROV,
                NOME_RH_APROV_REPROV,
                DATA_SINCRONISMO,
                EVENTO_SINCRONISMO,
                PERIODO_SINCRONISMO,
                MESCOMP         MES_COMPETENCIA,
                ANOCOMP         ANO_COMPETENCIA,
                ID_REQUISICAO,
                CHAPA_APROVADOR,
                NOME_APROVADOR,
                CHAPA_GESTOR,
                NOME_GESTOR       

            FROM
                REL_CONFERENCIA_PREMIOS 

            WHERE
                COLIGADA_COLAB = ".$this->coligada." 
                AND DT_INICIO_PONTO = '".$request['dataIni']."' 
                AND DT_FIM_PONTO = '".$request['dataFim']."'
                ".$FiltroSecao."
                ".$FiltroChapa."
                ".$FiltroFuncao." 
        ";

        //echo '<pre>';
        //echo $query;exit();
        $result = $this->dbportal->query($query);


        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    private function relatorioVariacaoPremios($request)
    {
       // print_r($request);
         //exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND CODSECAO_COLAB IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND CODFUNCAO_COLAB = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND CHAPA_COLAB = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        $query = "
            SELECT 
                NOME_PREMIO,
                CASE 
                    WHEN TIPO_REQUISICAO = 'M' THEN 'MENSAL'
                    ELSE 'COMPLEMENTAR'
                END TIPO_REQUISICAO,
                FORMAT(DT_INICIO_PONTO, 'dd/MM/yyyy') DT_INICIO_PONTO,
                FORMAT(DT_FIM_PONTO, 'dd/MM/yyyy') DT_FIM_PONTO,
                CODFILIAL_COLAB,
                CHAPA_COLAB,
                NOME_COLAB,
                FUNCAO_COLAB,
                FORMAT(DT_ADMISSAO_COLAB, 'dd/MM/yyyy') DT_ADMISSAO_COLAB,
                CCUSTO_COLAB,
                CODSECAO_COLAB,
                SECAO_COLAB,
                CODSITUACAO_COLAB,
                DIAS_PERDIDOS,
                PERCENT_FINAL,
                VALOR_A_RECEBER,
                DIAS_PERDIDOS_MES_ANT,
                PERCENT_FINAL_MES_ANT,
                VALOR_A_RECEBER_MES_ANT,
                PERCENT_VARIACAO
                
            FROM
                REL_VARIACAO_PREMIOS 

            WHERE
                COLIGADA_COLAB = ".$this->coligada." 
                AND DT_INICIO_PONTO = '".$request['dataIni']."' 
                AND DT_FIM_PONTO = '".$request['dataFim']."'
                ".$FiltroSecao."
                ".$FiltroChapa."
                ".$FiltroFuncao." 
        ";

        //echo '<pre>';
        //echo $query;exit();
        $result = $this->dbportal->query($query);


        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

    private function relatorioDeflatoresPremios($request)
    {
       // print_r($request);
         //exit();
        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $filtroGestor = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? "" : " AND ( CHAPA_GERENTE = '".$chapa."' OR CHAPA_COORDENADOR = '".$chapa."' ) ";

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND CODSECAO_COLAB IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND CODFUNCAO_COLAB = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND CHAPA_COLAB = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        $query = "
            SELECT 
                NOME_PREMIO,
                CASE 
                    WHEN TIPO_REQUISICAO = 'M' THEN 'MENSAL'
                    ELSE 'COMPLEMENTAR'
                END TIPO_REQUISICAO,
                FORMAT(DT_INICIO_PONTO, 'dd/MM/yyyy') DT_INICIO_PONTO,
                FORMAT(DT_FIM_PONTO, 'dd/MM/yyyy') DT_FIM_PONTO,
                CODFILIAL_COLAB,
                CHAPA_COLAB,
                NOME_COLAB,
                FUNCAO_COLAB,
                FORMAT(DT_ADMISSAO_COLAB, 'dd/MM/yyyy') DT_ADMISSAO_COLAB,
                CCUSTO_COLAB,
                CODSECAO_COLAB,
                SECAO_COLAB,
                CODSITUACAO_COLAB,
                DIAS_FALTAS,
                DATAS_FALTAS,
                DIAS_AFAST,
                DATAS_AFAST,
                DIAS_ATESTADO,
                DATAS_ATESTADO,
                DIAS_FERIAS,
                DATAS_FERIAS,
                DIAS_ADMISSAO,
                DATAS_ADMISSAO,
                DIAS_DEMISSAO,
                DATAS_DEMISSAO,
                DIAS_TOTAL          DIAS_DEFLATORES,
                CASE 
                    WHEN (30 - DIAS_TOTAL) < 0 THEN 0
                    ELSE (30 - DIAS_TOTAL)
                END DIAS_DE_DIREITO
                
            FROM
                REL_CONFERENCIA_PREMIOS 

            WHERE
                COLIGADA_COLAB = ".$this->coligada." 
                AND DT_INICIO_PONTO = '".$request['dataIni']."' 
                AND DT_FIM_PONTO = '".$request['dataFim']."' 
                ".$FiltroSecao." 
                ".$FiltroChapa." 
                ".$FiltroFuncao."  
                ".$filtroGestor." 
        ";

        //echo '<pre>';
        //echo $query;exit();
        $result = $this->dbportal->query($query);

        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }
    
    private function relatorioEscalaDia($request)
    {

        $select = "";
        foreach($request['colunas'] as $Select){
            $select .= $Select.',';
        }

        $FiltroSecao = "";
        if(is_array($request['secao'])){
            if(count($request['secao']) > 0){
                $codsecao = "";
                foreach($request['secao'] as $Secao){
                    $codsecao .= "'{$Secao}',";
                }
                $FiltroSecao = " AND b.CODSECAO IN (".rtrim($codsecao,',').") ";
            }
        }

        if($request['funcao'] != ""){
            $FiltroFuncao = "AND b.CODFUNCAO = '".$request['funcao']."'";
        }else{
            $FiltroFuncao = "";
        }

        if($request['chapa']){
            if($request['chapa'] != ""){
                $FiltroChapa = "AND b.CHAPA = '".$request['chapa']."'";
            }else{
                $FiltroChapa = "";
            }
        }else{
            $FiltroChapa = "";
        }

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " b.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
		}
        
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";
			}
			$filtro_secao_gestor = " b.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

        if(!$request['rh']){
		    $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        }else{
            $qr_secao = "";
        }

        $FiltroData = "";
        if($request['dataIni'] != ''){
            $FiltroData .= " AND (a.datamudanca >= '{$request['dataIni']} 00:00:00' OR a.datamudanca_folga >= '{$request['dataIni']} 00:00:00') ";
        }
        if($request['dataFim'] != ''){
            $FiltroData .= " AND (a.datamudanca <= '{$request['dataFim']} 23:59:59' OR a.datamudanca_folga <= '{$request['dataFim']} 23:59:59') ";
        }

        $query = "
            SELECT ".rtrim($select, ',')." FROM (
                SELECT
                    DISTINCT
                    a.id ID,
                    CASE WHEN a.tipo = 1 THEN 'Troca de Escala' ELSE 'Troca de Dia' END [TIPO],
                        CASE WHEN a.situacao = 0 THEN 'Criada'
                        WHEN a.situacao = 10 THEN 'Pend/Ação Gestor'
                        WHEN a.situacao = 1 THEN 'Pend/Upload Documento'
                        WHEN a.situacao = 4 THEN 'Termo Anexado'
                        WHEN a.situacao = 2 THEN 'Pend/Ação RH'
                        WHEN a.situacao = 3 THEN 'Concluído'
                        WHEN a.situacao = 9 THEN 'Reprovado'
                        WHEN a.situacao = 11 THEN 'Excluído'
                        end as STATUS,
                    CONVERT(VARCHAR, a.datamudanca, 103) [DATA],
                    b.CODCOLIGADA [COD.COLIGADA],
                    k.NOME [COLIGADA],
                    b.codfilial [COD.FILIAL],
                    c.nome [FILIAL],
                    a.chapa [CHAPA],
                    b.nome [NOME],
                    b.codfuncao [COD.FUNÇÃO],
                    d.nome [FUNÇÃO],
                    CONVERT(VARCHAR, b.dataadmissao, 103) [DT ADMISSÃO],
                    substring(b.codsecao, 5, 5) [COD.CUSTO],
                    j.NOME [DESC.CUSTO],
                    b.codsecao [COD.SEÇÃO],
                    e.descricao [DESC.SEÇÃO],
                    (
                        SELECT
                            TOP 1 
                            HB.DESCRICAO
                        FROM
                            ".DBRM_BANCO."..PFHSTSIT HA
                            INNER JOIN ".DBRM_BANCO."..PCODSITUACAO HB ON HB.CODCLIENTE = HA.NOVASITUACAO
                        WHERE
                                HA.CODCOLIGADA = a.coligada
                            AND HA.CHAPA = a.chapa COLLATE Latin1_General_CI_AS
                            AND (
                                HA.DATAMUDANCA <= a.datamudanca
                            )
                        ORDER BY
                            DATAMUDANCA DESC
                    ) [SITUAÇÃO],
                    (
                        SELECT
                            TOP 1
                            CONCAT(A2.CODIGO, ' - ', A2.DESCRICAO, ' - ', ZZ.INDICE)
                        FROM
                            ".DBRM_BANCO."..PFHSTHOR A1 (NOLOCK)
                            INNER JOIN ".DBRM_BANCO."..AHORARIO A2 (NOLOCK) ON A2.CODCOLIGADA = A1.CODCOLIGADA AND A2.CODIGO = A1.CODHORARIO
                            LEFT JOIN ".DBRM_BANCO."..Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A1.CODCOLIGADA AND ZZ.CODHORARIO = A1.CODHORARIO AND ZZ.DATA = (DATEADD(day, +(A1.INDINICIOHOR-1), a.datamudanca))
                        WHERE
                            A1.DTMUDANCA <= a.datamudanca
                            AND A1.CHAPA = a.chapa COLLATE Latin1_General_CI_AS
                            AND A1.CODCOLIGADA = a.coligada
                        ORDER BY
                            A1.DTMUDANCA DESC
                    ) [ANTES - TROCA],
                    CONCAT(a.codhorario, ' - ', f.descricao collate Latin1_General_CI_AS,' - ', a.codindice) [APÓS - TROCA],
                    CASE WHEN a.tipo = 2 THEN (
                        SELECT
                            TOP 1
                            CONCAT(A2.CODIGO, ' - ', A2.DESCRICAO, ' - ', ZZ.INDICE)
                        FROM
                            ".DBRM_BANCO."..PFHSTHOR A1 (NOLOCK)
                            INNER JOIN ".DBRM_BANCO."..AHORARIO A2 (NOLOCK) ON A2.CODCOLIGADA = A1.CODCOLIGADA AND A2.CODIGO = A1.CODHORARIO
                            LEFT JOIN ".DBRM_BANCO."..Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A1.CODCOLIGADA AND ZZ.CODHORARIO = A1.CODHORARIO AND ZZ.DATA = (DATEADD(day, +(A1.INDINICIOHOR-1), a.datamudanca_folga))
                        WHERE
                            A1.DTMUDANCA <= a.datamudanca_folga
                            AND A1.CHAPA = a.chapa COLLATE Latin1_General_CI_AS
                            AND A1.CODCOLIGADA = a.coligada
                        ORDER BY
                            A1.DTMUDANCA DESC
                    ) ELSE NULL END [ANTES - FOLGA],
                    CASE WHEN a.tipo = 2 THEN CONCAT(a.codhorario, ' - ', f.descricao collate Latin1_General_CI_AS,' - ', a.codindice_folga) ELSE NULL END [APÓS - FOLGA],
                    
                    (
                    SELECT 
                        TOP 1 CONCAT(BB.CHAPA,'-',BB.NOME)
                    FROM
                        ".DBRM_BANCO."..PPESSOA AA (NOLOCK)
                        INNER JOIN ".DBRM_BANCO."..PFUNC BB (NOLOCK) ON BB.CODPESSOA = AA.CODIGO
                    WHERE
                        AA.CPF = g.login COLLATE Latin1_General_CI_AS
                        AND BB.DATAADMISSAO <= a.datamudanca
                    ) [USUÁRIO - SOLICITANTE],
                    
                    (
                    SELECT 
                        TOP 1 CONCAT(BB.CHAPA,'-',BB.NOME)
                    FROM
                        ".DBRM_BANCO."..PPESSOA AA (NOLOCK)
                        INNER JOIN ".DBRM_BANCO."..PFUNC BB (NOLOCK) ON BB.CODPESSOA = AA.CODIGO
                    WHERE
                        AA.CPF = h.login COLLATE Latin1_General_CI_AS
                        AND BB.DATAADMISSAO <= a.datamudanca
                    ) [USUÁRIO - GESTOR],
                    
                    (
                    SELECT 
                        TOP 1 CONCAT(BB.CHAPA,'-',BB.NOME)
                    FROM
                        ".DBRM_BANCO."..PPESSOA AA (NOLOCK)
                        INNER JOIN ".DBRM_BANCO."..PFUNC BB (NOLOCK) ON BB.CODPESSOA = AA.CODIGO
                    WHERE
                        AA.CPF = i.login COLLATE Latin1_General_CI_AS
                        AND BB.DATAADMISSAO <= a.datamudanca
                    ) [USUÁRIO - RH],
                    CONCAT(
                    	CASE WHEN a.justificativa_11_horas IS NOT NULL THEN CONCAT('Interjornada 11h: ',a.justificativa_11_horas, ', ') ELSE '' END,
                    	CASE WHEN a.justificativa_6_dias IS NOT NULL THEN CONCAT('6 dias consecutivos: ', a.justificativa_11_horas, ', ') ELSE '' END,
                    	CASE WHEN a.justificativa_6_meses IS NOT NULL THEN CONCAT('Troca inf. 6 meses: ', a.justificativa_6_meses, ', ') ELSE '' END,
                    	CASE WHEN a.justificativa_periodo IS NOT NULL THEN CONCAT('Fora período: ', a.justificativa_periodo, ', ') ELSE '' END
                    ) [JUSTIFICATIVA SOLICITAÇÃO],
                    CASE WHEN a.dtapr IS NULL THEN NULL ELSE CONVERT(VARCHAR, a.dtapr, 103) END [DATA APROVAÇÃO],
                    CASE WHEN a.dtcancelado IS NULL THEN NULL ELSE CONVERT(VARCHAR, a.dtcancelado, 103) END [DATA REPROVAÇÃO],
                    a.motivocancelado [JUSTIFICATIVA REPROVAÇÃO],
                    CONVERT(VARCHAR, a.dtcad, 103) [DATA SOLICITAÇÃO]
                    
                FROM
                    zcrmportal_escala a (NOLOCK)
                    INNER JOIN ".DBRM_BANCO."..PFUNC b (NOLOCK) ON b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS AND b.CODCOLIGADA = a.coligada
                    INNER JOIN ".DBRM_BANCO."..GFILIAL c (NOLOCK) ON c.CODFILIAL = b.CODFILIAL AND c.CODCOLIGADA = b.CODCOLIGADA
                    INNER JOIN ".DBRM_BANCO."..PFUNCAO d (NOLOCK) ON d.CODIGO = b.CODFUNCAO AND c.CODCOLIGADA = b.CODCOLIGADA
                    INNER JOIN ".DBRM_BANCO."..PSECAO e (NOLOCK) ON e.CODIGO = b.CODSECAO AND e.CODCOLIGADA = b.CODCOLIGADA
                    INNER JOIN ".DBRM_BANCO."..AHORARIO f (NOLOCK) ON f.CODIGO = a.codhorario COLLATE Latin1_General_CI_AS AND f.CODCOLIGADA = e.CODCOLIGADA
                    LEFT JOIN ".DBRM_BANCO."..GCCUSTO j (NOLOCK) ON j.CODCCUSTO = substring(b.codsecao, 5, 5) AND j.CODCOLIGADA = b.CODCOLIGADA
                    LEFT JOIN ".DBRM_BANCO."..GCOLIGADA k (NOLOCK) ON k.CODCOLIGADA = b.CODCOLIGADA
                    INNER JOIN zcrmportal_usuario g (NOLOCK) ON g.id = a.usucad
                    LEFT JOIN zcrmportal_usuario h (NOLOCK) ON h.id = a.usuapr
                    LEFT JOIN zcrmportal_usuario i (NOLOCK) ON i.id = a.usurh
                WHERE
                        1=1
                    {$FiltroSecao}
                    {$FiltroChapa}
                    {$FiltroFuncao}
                    {$FiltroData}
                    {$qr_secao}

            )Y
        ";
        $result = $this->dbportal->query($query);
        return ($result)
            ? array(
                'dados'     => $result->getResultArray(),
                'colunas'   => $result->getFieldCount()
            )
            : false;

    }

}