<?php
namespace App\Models;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class PortalModel extends Model {

    protected $dbportal;
    protected $dbrm;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm = db_connect('dbrm');
    }

    //---------------------------------------------
    // Lista dados do funcionário
    //---------------------------------------------
    public function ListarDadosFuncionario($cpf = false, $chapa = false, $filtra_situacao = true, $data_referencia = false){

        $where = ($filtra_situacao) ? " A.CODSITUACAO <> 'D' " : " A.CODSITUACAO IS NOT NULL ";
        $where .= ($cpf) ? " AND B.CPF = '{$cpf}' " : " AND A.CHAPA = '{$chapa}' AND A.CODCOLIGADA = '".session()->get('func_coligada')."' ";

        if(!$cpf && !$chapa) return false;
        
       if($data_referencia) {
            $dataFim = dtEn(substr($data_referencia, 10, 10));
            
            $fcoJoin = "
                LEFT JOIN (
                    SELECT 
                        F1.CODCOLIGADA,
                        F1.CHAPA,
                        F1.CODFUNCAO,
                        F1.DTMUDANCA
                    FROM PFHSTFCO F1
                    INNER JOIN (
                        SELECT 
                            CODCOLIGADA, 
                            CHAPA, 
                            MIN(DTMUDANCA) AS DTMUDANCA
                        FROM PFHSTFCO
                        WHERE DTMUDANCA > '{$dataFim}'
                        GROUP BY CODCOLIGADA, CHAPA
                    ) F2 ON F1.CODCOLIGADA = F2.CODCOLIGADA 
                        AND F1.CHAPA = F2.CHAPA 
                        AND F1.DTMUDANCA = F2.DTMUDANCA
                ) FCO ON FCO.CODCOLIGADA = A.CODCOLIGADA 
                    AND FCO.CHAPA = A.CHAPA
                
                LEFT JOIN PFUNCAO J ON J.CODCOLIGADA = A.CODCOLIGADA AND J.CODIGO = FCO.CODFUNCAO
            ";
            
            $funcaoSelect = "
                CASE
                    WHEN FCO.CODFUNCAO IS NOT NULL THEN FCO.CODFUNCAO
                    ELSE A.CODFUNCAO
                END AS CODFUNCAO,
                CASE
                    WHEN FCO.CODFUNCAO IS NOT NULL THEN J.NOME
                    ELSE D.NOME
                END AS NOMEFUNCAO";
        } else {
            $fcoJoin = "";
            $funcaoSelect = "
                A.CODFUNCAO,
                D.NOME AS NOMEFUNCAO";
        }
        $query = "
            SELECT 
                A.CODCOLIGADA,
                A.NOME,
                A.CHAPA,
                B.CPF,
                B.EMAIL,
                A.CODSITUACAO,
                A.DATAADMISSAO,
                A.DATADEMISSAO,
                A.CODSECAO,
                C.DESCRICAO NOMESECAO,
                 {$funcaoSelect},
                E.NOME NOMECOLIGADA,
                E.NOMEFANTASIA NOMEFANTASIACOLIGADA,
                E.CGC CNPJ,
                B.RUA,
                B.BAIRRO,
                B.CEP,
                B.CIDADE,
                B.ESTADO,
                A.PISPASEP PIS,
                B.CARTIDENTIDADE RG,
                F.NOME NOMEBANCO,
                A.CODBANCOPAGTO PAGTO_BANCO,
                A.CODAGENCIAPAGTO PAGTO_AGENCIA,
                A.CONTAPAGAMENTO PAGTO_CONTA,
                A.SALARIO,
                A.CODTIPO,
                B.DTNASCIMENTO,
                CASE 
                    WHEN B.SEXO = 'M' THEN 'Masculino'
                    WHEN B.SEXO = 'F' THEN 'Feminino'
                    ELSE 'Não informado'
                END SEXO,
                B.NUMERO,
                B.COMPLEMENTO,
                B.TELEFONE1,
                B.TELEFONE2,
                A.CODPESSOA,
                G.IMAGEM,
                --NULL IMAGEM,
                B.DTEMISSAOIDENT,
                B.ORGEMISSORIDENT,
                B.CARTEIRATRAB,
                B.SERIECARTTRAB,
                B.DTCARTTRAB,
                B.UFCARTTRAB,
                B.CARTMOTORISTA,
                B.TIPOCARTHABILIT,
                B.TIPOCARTHABILIT,
                B.DTVENCHABILIT,
                A.CODHORARIO,
                H.DESCRICAO NOMEHORARIO,
                I.NOME CARGO,
                (
                    SELECT 
                			MIN(DTMUDANCA) 
                		FROM 
                			PFHSTHOR 
                		WHERE 
                				CHAPA = A.CHAPA
                			AND CODCOLIGADA = A.CODCOLIGADA 
                			AND CODHORARIO = A.CODHORARIO
                			AND DTMUDANCA > CASE WHEN (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CHAPA = A.CHAPA AND CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO <> A.CODHORARIO) IS NOT NULL THEN (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CHAPA = A.CHAPA AND CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO <> A.CODHORARIO) ELSE '1900-01-01' END
                ) DTMUDANCA_HORARIO

            FROM 
                PFUNC A
                    INNER JOIN PPESSOA B ON B.CODIGO = A.CODPESSOA
                    INNER JOIN PSECAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
                    {$fcoJoin}
                    INNER JOIN PFUNCAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODFUNCAO
                    INNER JOIN GCOLIGADA E ON E.CODCOLIGADA = A.CODCOLIGADA
                    LEFT JOIN GBANCO F ON F.NUMBANCO = A.CODBANCOPAGTO
                    LEFT JOIN GIMAGEM G ON G.ID = B.IDIMAGEM
                    LEFT JOIN AHORARIO H ON H.CODCOLIGADA = A.CODCOLIGADA AND H.CODIGO = A.CODHORARIO
                    LEFT JOIN PCARGO I ON I.CODCOLIGADA = A.CODCOLIGADA AND I.CODIGO = D.CARGO
                    
            WHERE 
                --    A.CODSITUACAO <> 'D'
                {$where}
                --AND A.CHAPA IN ('001353','001514','001515')
                --AND A.CHAPA IN ('029900','011586','011541')
        ";
        //echo '<pre>'.$query;exit();
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Listar Coligada
    //---------------------------------------------
    public function ListarColigada($codcoligada = false, $todos = false){
        
        $isGestor = (!$todos) ? session()->get('gestor') : "S";

        $gestor = ($isGestor == 'S') ? " CODCOLIGADA > 0 " : " CODCOLIGADA = '".session()->get('func_coligada')."' ";

        $where = ($codcoligada) ? " CODCOLIGADA = '{$codcoligada}' " : $gestor;

        $query = " SELECT * FROM GCOLIGADA WHERE CODCOLIGADA IN (1,4,5) AND ".$where;
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Listar Seção Coligada
    //---------------------------------------------
    public function ListarSecao($codcoligada = false){

        $where = ($codcoligada) ? " AND CODCOLIGADA = '{$codcoligada}' " : " AND CODCOLIGADA = '".session()->get('func_coligada')."' ";

        // tamanho da seção
        switch(DBRM_TIPO){
            case 'sqlserver': $length = " AND LEN(CODIGO) = ".TAMANHO_SECAO; break;
            case 'oracle': $length = " AND LENGTH(CODIGO) = ".TAMANHO_SECAO; break;
            default: $length = ""; break;
        }

        $query = "
            SELECT 
                CODCOLIGADA,
                CODIGO,
                DESCRICAO
            FROM 
                PSECAO 
            WHERE 
                    SECAODESATIVADA = 0
                    {$where}
                    {$length}
            ORDER BY
                CODIGO
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Listar Funções
    //---------------------------------------------
    public function ListarFuncao($codcoligada = false, $codfuncao = false){

        $where = ($codcoligada) ? " AND CODCOLIGADA = '{$codcoligada}' " : " AND CODCOLIGADA = '".session()->get('func_coligada')."' ";
        $codigo = ($codfuncao) ? " AND CODIGO = '{$codfuncao}' " : "";

        $query = "
            SELECT
                CODCOLIGADA,
                CODIGO,
                NOME
            FROM
                PFUNCAO
            WHERE
                    INATIVA = 0
                {$where}
                {$codigo}
            ORDER BY
                NOME ASC
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Listar Filial
    //---------------------------------------------
    public function ListarFilial($codcoligada = false, $codfilial = false){

        $where = ($codcoligada) ? " AND CODCOLIGADA = '{$codcoligada}' " : " AND CODCOLIGADA = '".session()->get('func_coligada')."' ";
        $codigo = ($codfilial) ? " AND CODFILIAL = '{$codfilial}' " : "";

        $query = "
            SELECT
                CODCOLIGADA,
                CODFILIAL,
                NOMEFANTASIA,
                NOME
            FROM
                GFILIAL
            WHERE
                    ATIVO = 1
                {$where}
                {$codigo}
            ORDER BY
                CODFILIAL ASC
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Altera a senha de acesso
    //---------------------------------------------
    public function AlterarSenha($dados){

        $senha_atual = $dados['senha_atual'];
        $senha_nova = $dados['senha_nova'];
        $senha_nova_confirma = $dados['senha_nova_confirma'];
        $log_id = session()->get('log_id');

        if($senha_atual == "") return responseJson('error', 'Senha atual não informada.');
        if($senha_nova == "") return responseJson('error', 'Nova senha não informada.');
        if($senha_nova != $senha_nova_confirma) return responseJson('error', 'Confirmação da nova senha inválida.');

        $senha_atual = md5($senha_atual);
        $senha_nova = md5($senha_nova);

        // valida a nova senha
        $checkSenhaAtual = $this->dbportal->query(" SELECT * FROM zcrmportal_usuario WHERE id = '{$log_id}' AND senha = '{$senha_atual}' ");
        if($checkSenhaAtual->getNumRows() <= 0) return responseJson('error', 'Senha atual inválida.');

        $query = " UPDATE zcrmportal_usuario SET senha = '{$senha_nova}', senhatmp = NULL, dtexptoken = NULL, token = NULL, primeiro_acesso = 0, dtalt = '".date('Y-m-d H:i:s')."' WHERE id = '{$log_id}' ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0){
            session()->set('primeiro_acesso', 0);
            notificacao('success', 'Senha alterado com sucesso');
            return responseJson('success', 'Senha alterada com sucesso');
        }else{
            return responseJson('error', 'Falha ao alterar senha de acesso');
        }

    }

    //---------------------------------------------
    // Lista Menu Header
    //---------------------------------------------
    public function ListarAtalho(){

        $log_id = session()->get('log_id');

        $query = "
             SELECT 
                DISTINCT
                d.id,
                d.menutit,
                d.nome,
                d.caminho,
                d.icone
            FROM 	
                zcrmportal_usuarioperfil a
                INNER JOIN zcrmportal_perfil b ON b.id = a.id_perfil
                INNER JOIN zcrmportal_perfilfuncao c ON c.id_perfil = b.id
                INNER JOIN zcrmportal_funcoes d ON d.id = c.id_funcao AND d.modo != 'M' AND d.menu = 'X' AND d.menupai IS NOT NULL
                
            WHERE
                d.atalho = 1
                AND a.id_usuario = {$log_id}
                
            ORDER BY
                d.menutit
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function VerificaSecao(){

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        
        return $Secoes;

    }

    //---------------------------------------------
    // Lista funcionarios da seção
    //---------------------------------------------
    public function ListaFuncionarioSecao($dados = false, $podeSeVer = true){
        
        $chapaGestor = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
        
		$isLider = $mHierarquia->isLider();
       
        $filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
            if($objFuncLider){
                $chapas_lider = "";
                $codsecoes = "";
                foreach($objFuncLider as $idx => $value){
                    $chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
                }
                $filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
                // exit($filtro_secao_lider);
            }
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

        if($podeSeVer){
            $chapaFunc = " A.CHAPA = '{$chapaGestor}' ";
            $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor." OR {$chapaFunc}) ";
            if($filtro_secao_lider == "" && $filtro_secao_gestor == "") $in_secao = " AND A.CHAPA = '{$chapaGestor}' ";
        }else{
            $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") AND A.CHAPA != '{$chapaGestor}' ";
            if($filtro_secao_lider == "" && $filtro_secao_gestor == "") $in_secao = " AND 1 = 2 ";
        }


        if($dados){
            if($dados['rh'] ?? false) $in_secao = "";
        }
        
        $query = " SELECT A.CHAPA, A.NOME, A.CODSECAO FROM PFUNC A WHERE /*A.CODSITUACAO <> 'D'*/ 1=1 {$in_secao} AND A.CODCOLIGADA = '{$_SESSION['func_coligada']}' ORDER BY A.NOME ASC";
        // echo  $query;exit();
        $result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0)
                ? $result->getResultArray()
                : false;
    }

    public function CarregaColaboradores($dataInicio, $dados = false, $podeSeVer = true, $motorista = false, $passaPonto = false)
    {

        $chapaGestor = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

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
            // exit($filtro_secao_lider);
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

        if($podeSeVer){
            $chapaFunc = " A.CHAPA = '{$chapaGestor}' ";
            $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor." OR {$chapaFunc}) ";
            if($filtro_secao_lider == "" && $filtro_secao_gestor == "") $in_secao = " AND A.CHAPA = '{$chapaGestor}' ";
        }else{
            $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") AND A.CHAPA != '{$chapaGestor}' ";
            if($filtro_secao_lider == "" && $filtro_secao_gestor == "") $in_secao = " AND 1 = 2 ";
        }

        if($dados){
            if($dados['rh'] ?? false) $in_secao = "";
            if($dados['codsecao'] ?? false){
                if(!is_array($dados['codsecao'])){
                    $dados['codsecao'] = [$dados['codsecao']];
                }

                $in_secao .= " AND '".implode(",", $dados['codsecao'])."'  LIKE CONCAT('%', A.CODSECAO, '%') ";
            }
        }

        $in_ccusto = "";
        if($dados){
          if($dados['codccusto'] ?? false){
              if(!is_array($dados['codccusto'])){
                  if($dados['codccusto']<>'all') {
                    $dados['codccusto'] = [$dados['codccusto']];
                    $in_ccusto .= " AND '".implode(",", $dados['codccusto'])."'  LIKE CONCAT('%', C.CODCCUSTO, '%') ";
                  }
              } else {
                $in_ccusto .= " AND '".implode(",", $dados['codccusto'])."'  LIKE CONCAT('%', C.CODCCUSTO, '%') ";
              }
          }
        } 

        $utilizaPonto = '';
        if($passaPonto){
            $utilizaPonto = '
            AND (
                SELECT 
                    TOP 1 HP.UTILIZA
                FROM 
                    AHSTUTILIZAPONTO HP
                WHERE
                        HP.DATAINICIO <= GETDATE()
                    AND HP.CODCOLIGADA = A.CODCOLIGADA
                    AND HP.CHAPA = A.CHAPA
                ORDER BY
                    HP.DATAINICIO DESC
            ) = 1
            ';
        }
        
        $query = " 
            SELECT 
                A.CHAPA, 
                A.NOME, 
                A.CODSECAO,
                C.CODCCUSTO
            FROM 
                PFUNC A
            LEFT JOIN PSECAO S ON S.CODCOLIGADA = A.CODCOLIGADA AND S.CODIGO = A.CODSECAO
            LEFT JOIN GCCUSTO C ON C.CODCOLIGADA = S.CODCOLIGADA AND C.CODCCUSTO = S.NROCENCUSTOCONT
            WHERE 
                    A.CODSITUACAO IS NOT NULL 
                AND A.CODCOLIGADA = '{$_SESSION['func_coligada']}' 
                AND (
                    SELECT TOP 1 REGISTRO FROM (
                        SELECT
                            CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                            CASE
                                WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                ELSE '2099-12-31'
                            END DATA
                        FROM
                            PFUNC
                        WHERE
                            CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
                            AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                    )X WHERE X.DATA >= '{$dataInicio}'
                    ORDER BY X. DATA ASC
                ) IS NOT NULL
                {$in_secao}
                {$in_ccusto}
                {$utilizaPonto}
            ORDER BY 
                A.NOME 
            ASC
        ";
        $result = $this->dbrm->query($query);
        
        return ($result->getNumRows() > 0)
                ? $result->getResultArray()
                : [];

    }

    public function funcoesPodeVer($dados)
    {

        $chapaGestor = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

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
            // exit($filtro_secao_lider);
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

        $chapaFunc = " A.CHAPA = '{$chapaGestor}' ";
        $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor." OR {$chapaFunc}) ";
        if($filtro_secao_lider == "" && $filtro_secao_gestor == "") $in_secao = " AND A.CHAPA = '{$chapaGestor}' ";

        if($dados){
            if($dados['rh'] ?? false) $in_secao = "";
            if($dados['codsecao'] ?? false){
                if(!is_array($dados['codsecao'])){
                    $dados['codsecao'] = [$dados['codsecao']];
                }

                $in_secao .= " AND '".implode(",", $dados['codsecao'])."'  LIKE CONCAT('%', A.CODSECAO, '%') ";
            }
        }
        
        $utilizaPonto = '
        AND (
            SELECT 
                TOP 1 HP.UTILIZA
            FROM 
                AHSTUTILIZAPONTO HP
            WHERE
                    HP.DATAINICIO <= GETDATE()
                AND HP.CODCOLIGADA = A.CODCOLIGADA
                AND HP.CHAPA = A.CHAPA
            ORDER BY
                HP.DATAINICIO DESC
        ) = 1
        ';
        
        $query = " 
            SELECT 
                B.CODIGO,
                B.NOME 
            FROM 
                PFUNC A 
                INNER JOIN PFUNCAO B ON B.CODIGO = A.CODFUNCAO AND B.CODCOLIGADA = A.CODCOLIGADA AND B.INATIVA = 0
            WHERE 
                    A.CODCOLIGADA = '{$_SESSION['func_coligada']}' 
                AND A.CODSITUACAO <> 'D'
                {$in_secao}
                {$utilizaPonto}
            GROUP BY
                B.CODIGO,
                B.NOME
            ORDER BY 
                B.NOME
            ASC
        ";
       //echo '<pre>'.$query;exit;
        $result = $this->dbrm->query($query);
        
        return ($result->getNumRows() > 0)
                ? $result->getResultArray()
                : [];

    }
}