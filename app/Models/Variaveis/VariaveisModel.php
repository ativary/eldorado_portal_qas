<?php
namespace App\Models\Variaveis;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Controllers\BaseController;
use CodeIgniter\Model;

class VariaveisModel extends Model {

    /**
     * Lista de tipos de parametrização
     * 
     * 1 - Salário Substituição
     * 2 - Sobreaviso
     * 3 - Auxílio Crechê
     * 4 - Reembolso PCD: Pessoa com deficiênci
     * 5 - Desconto Autoriad
     * 6 - Auxílio Moradia
     * 7 - Ajuda de custo Aluguel
     * 8 - Coparticipação
     * 9 - Antecipação primeira parcela 13º Salario
     * 10 - Aprovação
     */

    protected $dbportal;
    protected $dbrm;
    private $now;
    private $coligada;
    private $logId;
    
    public function __construct()
    {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->now      = date('Y-m-d H:i:s');
        $this->coligada = $_SESSION['func_coligada'];
        $this->logId    = $_SESSION['log_id'];
    }

    public function ListarFuncionariosSecao($codsecao, $dados = false, $aprovacao = true, $chapa = false, $tipo = false)
	{
		if(!is_array($codsecao)){
			if($codsecao == 'all') $codsecao = null;
		}
		//-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
        $isLider = false;
        if($tipo != '3'){
            $isLider = $mHierarquia->isLider();
        }
		

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
        if (is_array($chapa)) {
            foreach ($chapa as $key => $value) {
                $resultado[] = $value['CHAPA'];
            }
            $chapa = "'" . implode("','", $resultado) . "'";
        }
        
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer($chapa);
        
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

		$chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
		if($aprovacao){
			$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.")  AND A.CHAPA != '{$chapaFunc}' ";
		}else{
			$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor." OR A.CHAPA = '{$chapaFunc}')";
		}
		

		if($dados){
			if($dados['rh'] ?? false || $dados['perfilRH'] ?? false) $qr_secao = "";
		}
		
		// lista seções
		if(!is_array($codsecao)){
			$filtro_secao = ($codsecao != null) ? " AND A.CODSECAO = '{$codsecao}' " : "";
		}else{
			if(is_array($codsecao)){
				$filtro_secao = "";
				$codsecao_in = "";
				foreach($codsecao as $codSecao){
				  $codsecao_in .= "'{$codSecao}',";
				}
		
				$filtro_secao = " AND A.CODSECAO IN (".rtrim($codsecao_in,',').") ";
			  }
		}
       
        $qr_func = "AND A.CODSITUACAO NOT IN ('D')";
        
		$query = " 
			SELECT 
				A.CHAPA,
                A.CODSITUACAO,
				A.NOME
				
			FROM 
				PFUNC A,
				PSECAO B
				
			WHERE 
				    A.CODCOLIGADA = '{$this->coligada}'
				AND A.CODCOLIGADA = B.CODCOLIGADA
				AND B.SECAODESATIVADA = 0
				AND A.CODSECAO = B.CODIGO
				{$qr_func}
				{$qr_secao}
                {$filtro_secao}
				

			GROUP BY
				A.CHAPA,
                A.CODSITUACAO,
				A.NOME
				
			ORDER BY
				A.NOME
		";
		// exit('<pre>'.print_r($query,1));
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
	}

    
    public function dadosFunc($chapa)
    {
        $query = " 
        SELECT
            B.NOME AS NOMEFUNCAO,
            B.CARGO,
            C.CPF,
            D.DESCRICAO AS NOMESECAO,
            A.*

        FROM 
            PFUNC A
            INNER JOIN PFUNCAO B ON  A.CODFUNCAO = B.CODIGO
            INNER JOIN PPESSOA C ON  A.CODPESSOA = C.CODIGO
            INNER JOIN PSECAO D ON  A.CODSECAO = D.CODIGO
            
        WHERE 
                A.CODCOLIGADA = '{$this->coligada}'
                AND
                B.CODCOLIGADA = '{$this->coligada}'
            AND A.CHAPA =  '{$chapa}'
            

    ";
   
    // exit('<pre>'.print_r($query,1));
    $result = $this->dbrm->query($query);
    if(!$result) return false;
    return ($result->getNumRows() > 0) 
            ? $result->getResultArray() 
            : false;

    }

    public function dadosFuncSUb($chapa)
    {
        $query = " 
        SELECT
            A.NOME as NOME,
            A.SALARIO,
            A.CODFUNCAO as FUNCAO,
            A.CODSITUACAO,
            GCCUSTO.CODCCUSTO AS CENTROCUSTO,
            GCCUSTO.NOME AS DESC_CUST,
            A.CODSECAO,
            D.DESCRICAO AS SECAO

        FROM 
            PFUNC A
            INNER JOIN PFUNCAO B ON  A.CODFUNCAO = B.CODIGO
            INNER JOIN PPESSOA C ON  A.CODPESSOA = C.CODIGO
            INNER JOIN PSECAO D ON  A.CODSECAO = D.CODIGO
            INNER JOIN  GCCUSTO on gccusto.codcoligada = D.codcoligada and gccusto.codccusto = D.nrocencustocont
     
            
        WHERE 
                A.CODCOLIGADA = '{$this->coligada}'
                AND
                B.CODCOLIGADA = '{$this->coligada}'
            AND A.CHAPA =  '{$chapa}'
            

    ";
   
    // exit('<pre>'.print_r($query,1));
    $result = $this->dbrm->query($query);
    if(!$result) return false;
    return ($result->getNumRows() > 0) 
            ? $result->getResultArray() 
            : false;

    }

     
    public function DadosEmpresa()
    {
        $query = " 
        SELECT
           *
        FROM 
         GCOLIGADA
            
        WHERE 
            CODCOLIGADA = '{$this->coligada}'
               
            

        ";
    
        // exit('<pre>'.print_r($query,1));
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function DeflatoresFerias($dt_ini, $dt_fim, $chapa)
    {
        $query = " 
        SELECT *,
        CASE
            WHEN '".$dt_fim."'  < DATAFIM THEN DATEDIFF(DAY, DATAINICIO, '".$dt_fim."' ) + 1
            ELSE DATEDIFF(DAY, DATAINICIO, DATAFIM) + 1
        END AS QUANTIDADE_DIAS
        FROM PFUFERIASPER
        WHERE DATAINICIO BETWEEN  '".$dt_ini."' AND '".$dt_fim."'
        AND CHAPA = '".$chapa."' 
        
               
            

        ";
    
        // exit('<pre>'.print_r($query,1));
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function DeflatoresAtestado($dt_ini, $dt_fim, $chapa)
    {
        $query = " 
        SELECT *,
        CASE
            WHEN '".$dt_fim."'  < DTFINAL THEN DATEDIFF(DAY, DTINICIO, '".$dt_fim."' ) + 1
            ELSE DATEDIFF(DAY, DTINICIO, DTFINAL) + 1
        END AS QUANTIDADE_DIAS
        FROM PFHSTAFT
        WHERE DTINICIO BETWEEN  '".$dt_ini."' AND '".$dt_fim."'
        AND CHAPA = '".$chapa."'
        AND tipo = 'P' 
        
               
            

        ";
    
       
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function DeflatoresFaltas($dt_ini, $dt_fim, $chapa)
    {
        $query = " 
        SELECT *
       
        FROM AAFHTFUN
        WHERE DATA BETWEEN '".$dt_ini."' AND '".$dt_fim."'
        AND CHAPA = '".$chapa."'
        AND FALTA > 0
        AND CODCOLIGADA ='{$this->coligada}'
    

        ";
        
        $result = $this->dbrm->query($query);
        if (!$result) return 0;
    
        // Retorna o número de linhas da consulta
        return $result->getNumRows();

    }

    public function DeflatoresFaltasPonto($dt_ini, $dt_fim, $chapa)
    {
        $query = " 
        SELECT
            TOP 1
            ISNULL((CASE WHEN (SELECT VALORINT FROM PPARAMADICIONAIS WHERE NOMECOLUNA = 'USACONTROLEFALTASCALCAVOS' AND CODCOLIGADA = '{$this->coligada}') = 1 
                THEN
            (SELECT COUNT(CHAPA) FROM PFHSTFALTA D WHERE D.CODCOLIGADA ='{$this->coligada}' AND D.CHAPA =  '".$chapa."' AND D.DATAINICIO >='".$dt_ini."' AND D.DATAINICIO <= '".$dt_fim."') 
            ELSE
            (SELECT SUM(E.REF) FROM PFFINANC E, PEVENTO F WHERE E.CODCOLIGADA = F.CODCOLIGADA  AND E.CODEVENTO = F.CODIGO AND '{$this->coligada}' = E.CODCOLIGADA AND  '".$chapa."' = E.CHAPA AND F.CODIGOCALCULO IN ('8') AND F.VALHORDIAREF IN ('D') AND CAST(DATEFROMPARTS(ANOCOMP, MESCOMP, 01) AS DATETIME) BETWEEN '".$dt_ini."' AND '".$dt_fim."')
            END),0) FALTAS

        FROM PFFINANC
    

        ";
       // exit('<pre>'.print_r($query,1));
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    
    public function DadosFilial($codfilial)
    {
        $query = " 
        SELECT
           *
        FROM 
         GFILIAL
            
        WHERE 
            CODCOLIGADA = '{$this->coligada}'
            AND CODFILIAL ='{$codfilial}'
               
            

    ";
   
    // exit('<pre>'.print_r($query,1));
    $result = $this->dbrm->query($query);
    if(!$result) return false;
    return ($result->getNumRows() > 0) 
            ? $result->getResultArray() 
            : false;

    }

    public function dadosFunc2($chapa)
    {
        $query = " 
        SELECT
            B.NOME AS NOMEFUNCAO,
            B.CARGO, 
            A.CODSITUACAO,
            A.CODFUNCAO,
            A.NOME

        FROM 
            PFUNC A
            INNER JOIN PFUNCAO B ON  A.CODFUNCAO = B.CODIGO
          
            
        WHERE 
                A.CODCOLIGADA = '{$this->coligada}'
                AND
                B.CODCOLIGADA = '{$this->coligada}'
            AND A.CHAPA =  '{$chapa}'
            

    ";
   
    // exit('<pre>'.print_r($query,1));
    $result = $this->dbrm->query($query);
    if(!$result) return false;
    return ($result->getNumRows() > 0) 
            ? $result->getResultArray() 
            : false;

    }


    public function funcGestor($chapa)
    {
        $query = " 
        SELECT CPF_GESTOR_IMEDIATO, B.NOME, E.CHAPA 
            FROM CRM_HIERARQUIA3 A 
            INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_usuario B 
                ON A.CPF_GESTOR_IMEDIATO = B.login COLLATE Latin1_General_CI_AS 
            INNER JOIN PPESSOA D 
                ON A.CPF_GESTOR_IMEDIATO = D.CPF COLLATE Latin1_General_CI_AS 
            INNER JOIN PFUNC E 
                ON D.CODIGO = E.CODPESSOA  
            WHERE A.chapa = '".$chapa."' 
            AND E.CODSITUACAO <> 'D' 

        UNION ALL 

        SELECT CPF_GESTOR_IMEDIATO, B.NOME, E.CHAPA
            FROM CRM_HIERARQUIA3 A 
            INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_usuario B 
                ON A.CPF_GESTOR_IMEDIATO = B.login COLLATE Latin1_General_CI_AS
            left JOIN ".DBPORTAL_BANCO."..zcrmportal_hierarquia_gestor_substituto C ON C.chapa_gestor COLLATE Latin1_General_CI_AS = A.chapa 
            INNER JOIN PPESSOA D 
                ON A.CPF_GESTOR_IMEDIATO = D.CPF COLLATE Latin1_General_CI_AS 
            INNER JOIN PFUNC E 
                ON D.CODIGO = E.CODPESSOA  
            
            WHERE A.chapa = C.chapa_gestor COLLATE Latin1_General_CI_AS
            AND C.chapa_substituto = '".$chapa."'
            AND C.inativo = 0
            AND getdate() BETWEEN C.dtini AND C.dtfim 
            AND E.CODSITUACAO <> 'D' 
    
    ";
    
        
       // exit('<pre>'.print_r($query,1));
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    

    public function emailGestor($aprovador)
    {

       
        $query2 = " 
        SELECT nome, email FROM zcrmportal_usuario WHERE login = '".$aprovador."'
        UNION ALL
        SELECT B.SUBSTITUTO_NOME, B.email FROM zcrmportal_usuario A
                INNER JOIN GESTOR_SUBSTITUTO_CHAPA B ON B.GESTOR_ID = A.ID
            WHERE login = '".$aprovador."'
            AND B.FUNCOES LIKE '%\"219\"%'";
         //exit('<pre>'.print_r($query2,1));
        $result2 = $this->dbportal->query($query2);
       
        return ($result2->getNumRows() > 0) 
                ? $result2->getResultArray() 
                : false;

    }
    public function emailSolicitande($aprovador)
    {

       
        $query2 = " 
        SELECT * FROM zcrmportal_usuario WHERE id ='".$aprovador."'
        UNION ALL
        SELECT B.SUBSTITUTO_NOME, B.email FROM zcrmportal_usuario A
                INNER JOIN GESTOR_SUBSTITUTO_CHAPA B ON B.GESTOR_ID = A.ID
            WHERE login = '".$aprovador."'";
         //exit('<pre>'.print_r($query2,1));
        $result2 = $this->dbportal->query($query2);
       
        return ($result2->getNumRows() > 0) 
                ? $result2->getResultArray() 
                : false;

    }

    public function dadosFuncDepend($chapa, $tipo = 4, $filial = false)
    {

        $codparentesco =  self::getParametros($tipo);
        $regraIdade = '';
        if($tipo == 4){
            $reembolsoCpdParente = isset($codparentesco->reembolso_cpd_parente) ? $codparentesco->reembolso_cpd_parente : [];
        }else if($tipo == 2){

            $reembolsoCpdParente = isset($codparentesco->reembolso_creche_parente) ? $codparentesco->reembolso_creche_parente : [];
            $idade = $codparentesco->idade_creche; // Valor padrão caso não encontre correspondência

            foreach ($codparentesco->dependentes as $dependente) {
                if ($dependente->nome == $filial) {
                    $idade = $dependente->idade; 
                    break; 
                }
            }
            $regraIdade ='AND DATEDIFF(YEAR, B.DTNASCIMENTO, GETDATE()) - 
            CASE 
                WHEN DATEADD(YEAR, DATEDIFF(YEAR, B.DTNASCIMENTO, GETDATE()), B.DTNASCIMENTO) > GETDATE() 
                THEN 1 
                ELSE 0 
            END <= '.$idade.'';
        }
     

        // Converte o array para uma string com os valores separados por vírgula
        $codParente = implode(',', array_map(function($value) {
            return "'$value'";
        }, $reembolsoCpdParente));
      
        $query = " 
        SELECT
            B.NRODEPEND,
            B.NOME AS NOMEDEPEND, 
            B.CPF, 
            B.DTNASCIMENTO,
            B.GRAUPARENTESCO  
          

        FROM 
            PFUNC A,
            PFDEPEND B
          
            
        WHERE 
            A.CODCOLIGADA = '{$this->coligada}'
            AND A.CHAPA =  '{$chapa}'
            AND A.CHAPA = B.CHAPA
            AND B.GRAUPARENTESCO IN ({$codParente})
            ".$regraIdade."
          

    ";
   
  
    $result = $this->dbrm->query($query);
    if(!$result) return false;
    return ($result->getNumRows() > 0) 
            ? $result->getResultArray() 
            : false;

    }

    public function DeleteReq($id)
    {
        $query = " 
        DELETE FROM dbo.zcrmportal_variaveis_req
            WHERE id = '".$id."'
           
        ";
        // exit('<pre>'.print_r($query,1));
        return $this->dbportal->query($query);

    }

    public function DeleteReqAnexo($id)
    {
        $query = " 
        DELETE FROM dbo.zcrmportal_variaveis_req_anexo
            WHERE id = '".$id."'
           
        ";
        // exit('<pre>'.print_r($query,1));
        return $this->dbportal->query($query);

    }

    public function aprovarReq($ids, $situacao)
    {
        
        if (!is_array($ids)) {
            $ids = [$ids];
        }
    
        foreach ($ids as $idx => $id) {
            $req = self::getReqDados($id);
            $email = self::emailGestor($req[0]->aprovador);
          
            $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
            $data = [
                'id_user' => $this->logId,
                'id_hierarquia' => '', 
                'dtcad' => $this->now, 
                'tipo' => '',
                'nivel_apr_area' => '0',
                'observacao' => '',
                'id_requisicao' => $id
            ];
    
            $table->insert($data);

            if($req[0]->tipo == 6 || $req[0]->tipo == 7 || $req[0]->tipo == 1 || $req[0]->tipo == 3 || $req[0]->tipo == 5){
            switch ($req[0]->tipo) {
                case 6:
                    $tipo = "Auxilio Moradia";
                   
                    break;
                case 4:
                    $tipo = "Auxilio Excepcional";
                  
                    break;
                case 9:
                    $tipo = "13º Salário";
                      
                    break;
                case 8:
                    $tipo = "Coparticipação";
                          
                    break;
                case 7:
                    $tipo = "Auxilio Aluguel";
                              
                    break;
                case 2:
                    $tipo = "Auxilio Creche";
                                  
                    break;
                case 1:
                    $tipo = "Substituição";
                                  
                    break;
                case 3:
                    $tipo = "Sobreaviso";
                                  
                    break;
                case 5:
                    $tipo = "Desconto Autorizado";
                                  
                    break;
            }
            $htmlEmail = "
                Olá <strong>{$email[0]['nome']}</strong>,<br>
                <br>
                Foi Solicitado uma Nova Requisição de variaveis ID ".$id." para sua aprovação.<br>
                <br>
                <i>* Requisição de ".$tipo.".</i><br>
                <br>
                <a href=\"".base_url('variaveis/sincronizacao')."\" target='_blank'>".base_url('variaveis/sincronizacao')."</a>
                <br><br>
              
            ";
            $htmlEmail = templateEmail($htmlEmail);
            if($situacao != '3') enviaEmail(  $email[0]['email'], 'Requisição de Variáveis Pendente', $htmlEmail);
            
            }
        }
        
        $ids = implode(',', array_map('intval', (array) $ids));
       
        $query = " 
        UPDATE dbo.zcrmportal_variaveis_req
        SET status = $situacao
        , dtenvio = '".date('Y-m-d H:i:s')."'
        WHERE id IN ($ids) AND status ='1'
        ";

        // Executa a query
        return $this->dbportal->query($query);
    }

    public function aprovarReqGestor($id, $tipo, $justificativa)
    {
         
       
        
        $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
        $data = [
            'id_user' => $this->logId,
            'id_hierarquia' => '', 
            'dtcad' => $this->now, 
            'tipo' => $tipo,
            'nivel_apr_area' => '2',
            'observacao' => $justificativa,
            'id_requisicao' => $id
            
        ];

        $table->insert($data);

        $query = " 
            UPDATE dbo.zcrmportal_variaveis_req
            SET status = 3
            WHERE id = '".$id."'
        ";
        // exit('<pre>'.print_r($query,1));
        return $this->dbportal->query($query);

    }

    public function reprovarReqGestor($id, $tipo, $justificativa)
    {
         
       
        
        $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
        $data = [
            'id_user' => $this->logId,
            'id_hierarquia' => '', 
            'dtcad' => $this->now, 
            'tipo' => $tipo,
            'nivel_apr_area' => '5',
            'observacao' => $justificativa,
            'id_requisicao' => $id
            
        ];

        $table->insert($data);

        $query = " 
            UPDATE dbo.zcrmportal_variaveis_req
            SET status = 5
            WHERE id = '".$id."'
        ";
        // exit('<pre>'.print_r($query,1));
        return $this->dbportal->query($query);

    }

    public function aprovaRH($id, $tipo, $justificativa)
    {
        $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
        $data = [
            'id_user' => $this->logId,
            'id_hierarquia' => '', 
            'dtcad' => $this->now, 
            'tipo' => $tipo,
            'nivel_apr_area' => '7',
            'observacao' => $justificativa,
            'id_requisicao' => $id
            
        ];

        $table->insert($data);

        $query = " 
            UPDATE dbo.zcrmportal_variaveis_req
            SET status = 7
            WHERE id = '".$id."'
        ";
       
        return $this->dbportal->query($query);

    }

    public function aprovaRHGestor($id, $tipo, $justificativa)
    {
        $req = self::getReqDados($id);
        $email = self::emailSolicitande($req[0]->usucad);

        $htmlEmail = "
                Olá <strong>{$email[0]['nome']}</strong>,<br>
                <br>
                O Termo de Desconto Autorizado está liberado para a requisição ID ".$id." .<br>
                <br>
                <i>* Requisição de Desconto autorizado.</i><br>
                <br>
                <a href=\"".base_url('variaveis/desconto')."\" target='_blank'>".base_url('variaveis/desconto')."</a>
                <br><br>
              
            ";
        $htmlEmail = templateEmail($htmlEmail);
        enviaEmail(  $email[0]['email'], 'Requisição de Variáveis Pendente', $htmlEmail);
            
        $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
        $data = [
            'id_user' => $this->logId,
            'id_hierarquia' => '', 
            'dtcad' => $this->now, 
            'tipo' => $tipo,
            'nivel_apr_area' => '8',
            'observacao' => $justificativa,
            'id_requisicao' => $id
            
        ];

        $table->insert($data);

        $query = " 
            UPDATE dbo.zcrmportal_variaveis_req
            SET status = 9
            WHERE id = '".$id."'
        ";
       
        return $this->dbportal->query($query);

    }

    public function VoltaRH($id)
    {
        $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
        $data = [
            'id_user' => $this->logId,
            'id_hierarquia' => '', 
            'dtcad' => $this->now, 
            'tipo' => '5',
            'nivel_apr_area' => '9',
            'observacao' => '',
            'id_requisicao' => $id
            
        ];

        $table->insert($data);

        $query = " 
            UPDATE dbo.zcrmportal_variaveis_req
            SET status = 3
            WHERE id = '".$id."'
        ";
       
        return $this->dbportal->query($query);

    }

    public function reprovaRH($id, $tipo, $justificativa)
    {
         
       
        
        $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
        $data = [
            'id_user' => $this->logId,
            'id_hierarquia' => '', 
            'dtcad' => $this->now, 
            'tipo' => $tipo,
            'nivel_apr_area' => '6',
            'observacao' => $justificativa,
            'id_requisicao' => $id
            
        ];

        $table->insert($data);

        $query = " 
            UPDATE dbo.zcrmportal_variaveis_req
            SET status = 6
            WHERE id = '".$id."'
        ";
        // exit('<pre>'.print_r($query,1));
        return $this->dbportal->query($query);

    }

    public function sincReq($id, $tipo, $justificativa)
    {
         
     
        
       

        $dadosReq        = self::getReqDados($id);
        $valores = json_decode($dadosReq[0]->valores) ;

        if($tipo == '4'){
            $lancamento ='32';
            $param4        = self::getParametros(4);

            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);

            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){

            $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$param4->reembolso_cpd_evento."' AND CODCOLIGADA ='". $this->coligada."'";
            
            $result = $this->dbrm->query($query);

            $result = ($result->getNumRows() > 0) 
						? $result->getResultArray() 
						: false;
                      
            if($result[0]['VALHORDIAREF'] == 'V'){
                $val = $valores->valor_total;
                $ref = 0;
            }else{
                $ref = $valores->valor_total;
                $val = 0;
            }
            if($dadosReq[0]->tiporeq == '2'){
                $lancamento ='34';
             }

             if ($valores->fora_periodo == '1') {
                // $novoMes = date('m', strtotime('+1 month', strtotime($dadosReq[0]->dtcad)));
                // $novoAno = date('Y', strtotime('+1 month', strtotime($dadosReq[0]->dtcad)));


                $novoMes = date('m');
                $novoAno = date('Y');
            } else {
                $novoMes = date('m');
                $novoAno = date('Y');
            }
            
            $insert_0434 = "
                INSERT INTO PFMOVTEMP (TIPOLANCAMENTO, CODCOLIGADA, CHAPA, ANOCOMP, MESCOMP, CODEVENTO, IDMOVTEMP, HORA, REF, VALOR, VALORFORCADO, ORIGEMEVENTO, 
                DATAINCLUSAO, CODUSUARIO, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
                VALUES (
                    '".$lancamento."',
                    '".$_SESSION['func_coligada']."',
                    '".$dadosReq[0]->chapa."',
                    '".$novoAno."',  -- Use o novo ano calculado
                    '".$novoMes."',  -- Use o novo mês calculado
                    '".$param4->reembolso_cpd_evento."',
                    (SELECT MAX(IDMOVTEMP)+1 FROM PFMOVTEMP),
                    0,
                    '".$ref."',
                    '".$val."',
                    0,
                    4,
                    '".date('Y-m-d H:i:s')."', 'PortalPontoATU', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
            ";
            
           
            $result = $this->dbrm->query($insert_0434);
            }
        }elseif($tipo == '6'){
            $lancamento ='31';
            $param6        = self::getParametros(6);
            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);
            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){
            // Substituir o ponto de milhar por nada e a vírgula decimal por um ponto
           

            // Converter para float
            $valor_numerico = (float)$dadosFunc[0]['SALARIO'];
           
            $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$param6->auxilio_moradia_evento."' AND CODCOLIGADA ='". $this->coligada."'";
            
            $result = $this->dbrm->query($query);

            $result = ($result->getNumRows() > 0) 
						? $result->getResultArray() 
						: false;
                       
            if($result[0]['VALHORDIAREF'] == 'V'){
                $val = $param6->auxilio_moradia_porcentagem;
                $ref = 0;
            }else{
                $ref = $param6->auxilio_moradia_porcentagem;
                $val = 0;
            }

            if($dadosReq[0]->tiporeq == '2'){
               $lancamento ='33';
            }
           
            if ($valores->fora_periodo == '1') {
                // $novoMes = date('m', strtotime('+1 month'));
                // $novoAno = date('Y', strtotime('+1 month'));


                $novoMes = date('m');
                $novoAno = date('Y');
            } else {
                $novoMes = date('m');
                $novoAno = date('Y');
            }
            
            $insert_0434 = "
					INSERT INTO PFMOVTEMP (TIPOLANCAMENTO, CODCOLIGADA, CHAPA, ANOCOMP, MESCOMP, CODEVENTO, IDMOVTEMP, HORA, REF, VALOR, VALORFORCADO, ORIGEMEVENTO, 
					DATAINCLUSAO, CODUSUARIO, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
					VALUES (
                            '".$lancamento."',
							'".$_SESSION['func_coligada']."',
							'".$dadosReq[0]->chapa."',
							'".$novoAno."',
							'".$novoMes."',
							'".$param6->auxilio_moradia_evento."',
							(SELECT MAX(IDMOVTEMP)+1 FROM PFMOVTEMP),
							0,
							'".$ref."',
							'".$val."',
							0,
							4,
							'".date('Y-m-d H:i:s')."', 'PortalPontoATU', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
            ";
         
                   
            $result = $this->dbrm->query($insert_0434);
            }

        }elseif($tipo == '2'){
            $lancamento ='35';
            $param2        = self::getParametros(2);

            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);
            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){
            $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$param2->reembolso_creche_evento."' AND CODCOLIGADA ='". $this->coligada."'";
            
            $result = $this->dbrm->query($query);

            $result = ($result->getNumRows() > 0) 
						? $result->getResultArray() 
						: false;
                      
            if($result[0]['VALHORDIAREF'] == 'V'){
                $val = $valores->valor_total;
                $ref = 0;
            }else{
                $ref = $valores->valor_total;
                $val = 0;
            }
            if($dadosReq[0]->tiporeq == '2'){
                $lancamento ='36';
             }

             if ($valores->fora_periodo == '1') {
                // $novoMes = date('m', strtotime('+1 month'));
                // $novoAno = date('Y', strtotime('+1 month'));


                $novoMes = date('m');
                $novoAno = date('Y');
            } else {
                $novoMes = date('m');
                $novoAno = date('Y');
            }
            
            $insert_0434 = "
                INSERT INTO PFMOVTEMP (TIPOLANCAMENTO, CODCOLIGADA, CHAPA, ANOCOMP, MESCOMP, CODEVENTO, IDMOVTEMP, HORA, REF, VALOR, VALORFORCADO, ORIGEMEVENTO, 
                DATAINCLUSAO, CODUSUARIO, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
                VALUES (
                    '".$lancamento."',
                    '".$_SESSION['func_coligada']."',
                    '".$dadosReq[0]->chapa."',
                    '".$novoAno."',  -- Use o novo ano calculado
                    '".$novoMes."',  -- Use o novo mês calculado
                    '".$param2->reembolso_creche_evento."',
                    (SELECT MAX(IDMOVTEMP)+1 FROM PFMOVTEMP),
                    0,
                    '".$ref."',
                    '".$val."',
                    0,
                    4,
                    '".date('Y-m-d H:i:s')."', 'PortalPontoATU', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
            ";
            
           
            $result = $this->dbrm->query($insert_0434);
            }
        }elseif($tipo == '7'){
            $lancamento ='37';
            $param7        = self::getParametros(7);


            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);
            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){
                $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$param7->reembolso_aluguel_evento."' AND CODCOLIGADA ='". $this->coligada."'";
                
                $result = $this->dbrm->query($query);

                $result = ($result->getNumRows() > 0) 
                            ? $result->getResultArray() 
                            : false;
                                 
                $val = str_replace('.', '', $valores->valor); // Remove o ponto
                $val = str_replace(',', '.', $val); // Substitui a vírgula por ponto
                
               

                
                $mes = date('m');
                $ano = date('Y');
                
                
                if($valores->quantMes > 1){
                    $meses = $valores->quantMes - 1;
                    $novoMes = date('m', strtotime('+'.$meses.' month'));
                    $novoAno = date('Y', strtotime('+'.$meses.' month'));
                    
                }else{
                    $novoMes =  $mes;
                    $novoAno = $ano;
                    
                }
                
            
                
                $insert_0434 = "
                INSERT INTO PFEVENTOSPROG (CODCOLIGADA, CHAPA, CODEVENTO, ID, MESINIC, ANOINIC, MESFIM, ANOFIM, SEMPREVALIDO, TIPO, COMPLEMENTO1, COMPLEMENTO2, CODCCUSTO, VALOR, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
                VALUES ('". $this->coligada."', '".$dadosReq[0]->chapa."', '".$param7->reembolso_aluguel_evento."', 1, '".$mes."',  '".$ano."', '".$novoMes."',  '".$novoAno."', 0, 3, '', '', NULL, '". $val."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
             
                ";
             
              
                $result = $this->dbrm->query($insert_0434);
            }

        }elseif($tipo == '8'){
            $lancamento ='39';
            $param8        = self::getParametros(8);
            if($dadosReq[0]->tiporeq == '2'){
                        
                $evento = $param8->auxilio_coparticipacao_evento;
            }else{
                $evento = $param8->auxilio_coparticipacao2_evento;
            }
            $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$evento."' AND CODCOLIGADA ='". $this->coligada."'";
            $result = $this->dbrm->query($query);
            $result = ($result->getNumRows() > 0) 
            ? $result->getResultArray() 
            : false;
           
            if (isset($valores->dependentes) ){ 
                $dependentes = json_decode($valores->dependentes);
            
                foreach($dependentes as $key2 => $dados2){

                    $dadosFunc     = self::dadosFunc($dados2->chapa);
                    if($dadosFunc[0]['CODSITUACAO'] != 'D'  && $dadosFunc[0]['CODTIPO'] != 'T' ){
                        $val = str_replace('.', '', $dados2->valor);

                        $val =  str_replace(',', '.',   $val);
                        $ref = 0;
                        if ($valores->fora_periodo == '1') {
                            // $novoMes = date('m', strtotime('+1 month', strtotime($dadosReq[0]->dtcad)));
                            // $novoAno = date('Y', strtotime('+1 month', strtotime($dadosReq[0]->dtcad)));


                            $novoMes = date('m');
                            $novoAno = date('Y');
                        } else {
                            $novoMes = date('m');
                            $novoAno = date('Y');
                        }
                        
                        $insert_0434 = "
                            INSERT INTO PFMOVTEMP (TIPOLANCAMENTO, CODCOLIGADA, CHAPA, ANOCOMP, MESCOMP, CODEVENTO, IDMOVTEMP, HORA, REF, VALOR, VALORFORCADO, ORIGEMEVENTO, 
                            DATAINCLUSAO, CODUSUARIO, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
                            VALUES (
                                '".$lancamento."',
                                '".$_SESSION['func_coligada']."',
                                '".$dados2->chapa."',
                                '".$novoAno."',
                                '".$novoMes."',  
                                '".$evento."',
                                (SELECT MAX(IDMOVTEMP)+1 FROM PFMOVTEMP),
                                0,
                                '".$ref."',
                                '".$val."',
                                0,
                                4,
                                '".date('Y-m-d H:i:s')."', 'PortalPontoATU', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
                        ";
                        
                               
                        $result = $this->dbrm->query($insert_0434);
                    }
                }
            }

        }
        elseif($tipo == '9'){
            $lancamento ='29';
            $param9        = self::getParametros(9);
            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);
            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){
            // Substituir o ponto de milhar por nada e a vírgula decimal por um ponto
           

            // Converter para float
            $valor_numerico = (float)$dadosFunc[0]['SALARIO'];
           
            $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$param9->auxilio_13salario_evento."' AND CODCOLIGADA ='". $this->coligada."'";
            
            $result = $this->dbrm->query($query);

            $result = ($result->getNumRows() > 0) 
						? $result->getResultArray() 
						: false;
                       
            if($result[0]['VALHORDIAREF'] == 'V'){
                $val = $param9->auxilio_13salario_porcentagem;
                $ref = 0;
            }else{
                $ref = $param9->auxilio_13salario_porcentagem;
                $val = 0;
            }

          
           
            if ($valores->fora_periodo == '1') {
                // $novoMes = date('m', strtotime('+1 month'));
                // $novoAno = date('Y', strtotime('+1 month'));


                $novoMes = date('m');
                $novoAno = date('Y');
            } else {
                $novoMes = date('m');
                $novoAno = date('Y');
            }
            
            $insert_0434 = "
					INSERT INTO PFMOVTEMP (TIPOLANCAMENTO, CODCOLIGADA, CHAPA, ANOCOMP, MESCOMP, CODEVENTO, IDMOVTEMP, HORA, REF, VALOR, VALORFORCADO, ORIGEMEVENTO, 
					DATAINCLUSAO, CODUSUARIO, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
					VALUES (
                            '".$lancamento."',
							'".$_SESSION['func_coligada']."',
							'".$dadosReq[0]->chapa."',
							'".$novoAno."',
							'".$novoMes."',
							'".$param9->auxilio_13salario_evento."',
							(SELECT MAX(IDMOVTEMP)+1 FROM PFMOVTEMP),
							0,
							0,
							0,
							0,
							4,
							'".date('Y-m-d H:i:s')."', 'PortalPontoATU', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
            ";
         
                   
            $result = $this->dbrm->query($insert_0434);
            }
        }elseif($tipo == '5'){
            $lancamento ='37';
            $param5        = self::getParametros(5);


            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);
            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){

                if($dadosReq[0]->tiporeq == '1'){
                        
                    $evento = $param5->reembolso_desconto_evento;
                }elseif($dadosReq[0]->tiporeq == '2'){
                    $evento = $param5->reembolso_desconto_evento2;
                }else{
                    $evento = $param5->reembolso_desconto_evento3;
                }
                $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$evento."' AND CODCOLIGADA ='". $this->coligada."'";
                
                $result = $this->dbrm->query($query);

                $result = ($result->getNumRows() > 0) 
                            ? $result->getResultArray() 
                            : false;
                                 
                $val = str_replace('.', '', $valores->valor_desconto); // Remove o ponto
                $val = str_replace(',', '.', $val); // Substitui a vírgula por ponto
                
                $val =  $val/$valores->quantMes;

                $mes = date('m');
                $ano = date('Y');
                 

                 
                if($valores->quantMes > 1){
                    $meses = $valores->quantMes - 1;
                    $novoMes = date('m', strtotime('+'. $meses.' month'));
                    $novoAno = date('Y', strtotime('+'. $meses.' month'));

                }else{
                    $novoMes =  $mes;
                    $novoAno = $ano;
                    
                }
               
               
                
                $insert_0434 = "
                INSERT INTO PFEVENTOSPROG (CODCOLIGADA, CHAPA, CODEVENTO, ID, MESINIC, ANOINIC, MESFIM, ANOFIM, SEMPREVALIDO, TIPO, COMPLEMENTO1, COMPLEMENTO2, CODCCUSTO, VALOR, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
                VALUES ('". $this->coligada."', '".$dadosReq[0]->chapa."', '".$evento."', 1, '".$mes."',  '".$ano."', '".$novoMes."',  '".$novoAno."', 0, 3, '', '', NULL, '". $val."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
             
                ";
               
              
                $result = $this->dbrm->query($insert_0434);
                if($result){
                    $log['mes'] = $mes;
                    $log['ano'] = $ano;
                    $log['novoMes'] = $novoMes;
                    $log['novoAno'] = $novoAno;
                    $log['val'] = $val;

                   $queryLog= "  INSERT INTO dbo.zcrmportal_variaveis_log_valores (id_req, valores, dtcad, status, periodo, id_user)
                    VALUES ('".$id."', '".json_encode($log)."', '".date('Y-m-d H:i:s')."', '7', '".date('Y-m')."', '".$_SESSION['log_id']."')";
                    $log= $this->dbportal->query($queryLog);
                }
            }

        }elseif($tipo == '3'){
            $lancamento ='38';
            $param3        = self::getParametros(3);
            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);
            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){
            // Substituir o ponto de milhar por nada e a vírgula decimal por um ponto
           

            // Converter para float
            if($dadosReq[0]->tiporeq == '2'){
                $lancamento ='28';
            }
            $valor_numerico = (float)$dadosFunc[0]['SALARIO'];
           
            $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$param3->sobreaviso_evento."' AND CODCOLIGADA ='". $this->coligada."'";
            
            $result = $this->dbrm->query($query);

            $result = ($result->getNumRows() > 0) 
						? $result->getResultArray() 
						: false;
                       
            if($result[0]['VALHORDIAREF'] == 'V'){
                $val =  0;
                $ref = $valores->valor;
                
            }else{
               // $val = ($valor_numerico/200) * $valores->valor ;
                $val =  0;
                $ref =  $valores->valor;
            }

          
           
            if ($valores->fora_periodo == '1') {
                // $novoMes = date('m', strtotime('+1 month'));
                // $novoAno = date('Y', strtotime('+1 month'));
                $novoMes = date('m');
                $novoAno = date('Y');
            } else {
                $novoMes = date('m');
                $novoAno = date('Y');
            }
            
            $insert_0434 = "
					INSERT INTO PFMOVTEMP (TIPOLANCAMENTO, CODCOLIGADA, CHAPA, ANOCOMP, MESCOMP, CODEVENTO, IDMOVTEMP, HORA, REF, VALOR, VALORFORCADO, ORIGEMEVENTO, 
					DATAINCLUSAO, CODUSUARIO, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
					VALUES (
                            '".$lancamento."',
							'".$_SESSION['func_coligada']."',
							'".$dadosReq[0]->chapa."',
							'".$novoAno."',
							'".$novoMes."',
							'".$param3->sobreaviso_evento."',
							(SELECT MAX(IDMOVTEMP)+1 FROM PFMOVTEMP),
							0,
                            '".$ref."',
                            '".$val."',
							0,
							4,
							'".date('Y-m-d H:i:s')."', 'PortalPontoATU', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
            ";
         
           
          
                   
            $result = $this->dbrm->query($insert_0434);
            }
        }elseif($tipo == '1'){
            $lancamento ='37';
            $param1        = self::getParametros(1);
            $dadosFunc     = self::dadosFunc($dadosReq[0]->chapa);
            $dadosFuncSub     = self::dadosFunc($valores->funcionario_sub);
            if($dadosFunc[0]['CODSITUACAO'] != 'D' ){
                // Substituir o ponto de milhar por nada e a vírgula decimal por um ponto
            

                // Converter para float
                $SalarioFunc = $dadosFunc[0]['SALARIO'];
                $SalarioFuncSub = $dadosFuncSub[0]['SALARIO'];
            
                $query = "SELECT VALHORDIAREF FROM PEVENTO WHERE codigo ='".$param1->substituicao_evento."' AND CODCOLIGADA ='". $this->coligada."'";
                
                $result = $this->dbrm->query($query);

                $result = ($result->getNumRows() > 0) 
                            ? $result->getResultArray() 
                            : false;

                // pega deflatores
                $deflatores = 0;

                $faltasFolha     = self::DeflatoresFaltasPonto($valores->data_inicio_referencia, $valores->data_fim_referencia, $dadosReq[0]->chapa);
               
                $ferias     = self::DeflatoresFerias($valores->data_inicio_referencia, $valores->data_fim_referencia, $dadosReq[0]->chapa);
                if( $ferias){
                    $deflatores = $deflatores + $ferias[0]['QUANTIDADE_DIAS'];
                }
                if( $faltasFolha){
                    $deflatores = $deflatores + $faltasFolha[0]['FALTAS'];
                }

                $atestado     = self::DeflatoresAtestado($valores->data_inicio_referencia, $valores->data_fim_referencia, $dadosReq[0]->chapa);
                if( $atestado){
                    $deflatores = $deflatores + $atestado[0]['QUANTIDADE_DIAS'];
                }
                

                if($SalarioFuncSub == $SalarioFunc){
                    $val =  ($SalarioFunc) / 30 * ($valores->dias_referencia - $deflatores) ;
                    $val = abs($val);
                    $val = number_format($val, 2, '.', '');

                }else{
                    $val =  ($SalarioFuncSub - $SalarioFunc) / 30 * ($valores->dias_referencia - $deflatores) ;
                    $val = abs($val);
                    $val = number_format($val, 2, '.', '');
                }
              
                
            
            
            
                $novoMes = date('m');
                $novoAno = date('Y');
                
                
                $insert_0434 = "
                        INSERT INTO PFMOVTEMP (TIPOLANCAMENTO, CODCOLIGADA, CHAPA, ANOCOMP, MESCOMP, CODEVENTO, IDMOVTEMP, HORA, REF, VALOR, VALORFORCADO, ORIGEMEVENTO, 
                        DATAINCLUSAO, CODUSUARIO, RECCREATEDBY, RECCREATEDON, RECMODIFIEDBY, RECMODIFIEDON)
                        VALUES (
                                '".$lancamento."',
                                '".$_SESSION['func_coligada']."',
                                '".$dadosReq[0]->chapa."',
                                '".$novoAno."',
                                '".$novoMes."',
                                '".$param1->substituicao_evento."',
                                (SELECT MAX(IDMOVTEMP)+1 FROM PFMOVTEMP),
                                0,
                                0,
                                '".$val."',
                                0,
                                4,
                                '".date('Y-m-d H:i:s')."', 'PortalPontoATU', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
                ";
                            
                 
                $result = $this->dbrm->query($insert_0434);
                
                if($result){
                    $log['dias_referencia'] = $valores->dias_referencia;
                    $log['data_inicio_referencia'] = $valores->data_inicio_referencia;
                    $log['data_fim_referencia'] = $valores->data_fim_referencia;
                    $log['val'] = $val;

                   $queryLog= "  INSERT INTO dbo.zcrmportal_variaveis_log_valores (id_req, valores, dtcad, status, periodo,id_user)
                    VALUES ('".$id."', '".json_encode($log)."', '".date('Y-m-d H:i:s')."', '7', '".date('Y-m')."', '".$_SESSION['log_id']."')";
                     $log= $this->dbportal->query($queryLog);

                     $data_inicio_referencia = strtotime($valores->data_inicio_referencia);
                     $data_fim = strtotime($valores->data_fim);
                     

                    if (date('Y-m', $data_inicio_referencia) !== date('Y-m', $data_fim)) {
                        $liberado = false;
                        // Ajusta a data de início para o primeiro dia do próximo mês
                        $valores->data_inicio_referencia = date('Y-m-01', strtotime('+1 month', $data_inicio_referencia));
                       
                        if (date('Y-m', strtotime($valores->data_inicio_referencia)) !== date('Y-m', $data_fim)) {
                           
                            $valores->dias_referencia = 30;
                            // Ajusta a data de fim para o último dia do mesmo mês da nova data de início
                            $dataInicioTimestamp = strtotime($valores->data_inicio_referencia);
                            $valores->data_fim_referencia = date('Y-m-t', $dataInicioTimestamp);
                        }else{
                            
                            // Converte as datas para timestamp
                           
                            $inicio = strtotime($valores->data_inicio_referencia);
                            $fim = strtotime( $valores->data_fim);
                            
                            // Calcula a diferença em dias
                            $diferenca = ($fim - $inicio) / (60 * 60 * 24) + 1;
                            
                            // Verifica se $data_fim está no último dia do mês
                            $ultimo_dia_mes = date('t', $fim); // Retorna o número total de dias do mês da data fim
                            $dias_fim = date('d', $fim); // Retorna o dia da data_fim

                            // Se a data_fim estiver no último dia do mês, define $dias_referencia como 30
                          
                            if ($dias_fim == $ultimo_dia_mes) {
                                $valores->dias_referencia = 30;
                            } else {
                                // Caso contrário, usa a diferença normal
                                $valores->dias_referencia = $diferenca;
                            }
                            $valores->data_fim_referencia = $valores->data_fim;
                        }
                       
                        $query = " 
                            UPDATE dbo.zcrmportal_variaveis_req
                            SET valores = '".json_encode($valores)."'
                            WHERE id = '".$id."'
                        ";
                       $teste= $this->dbportal->query($query);
                       if($teste){
                        return 'Atualizado';
                       }
                       
                        
                    }else{
                        $liberado = true;


                    }
                    if($liberado){
                        $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
                        $data = [
                            'id_user' => $this->logId,
                            'id_hierarquia' => '', 
                            'dtcad' => $this->now, 
                            'tipo' => $tipo,
                            'nivel_apr_area' => '3',
                            'observacao' => $justificativa,
                            'id_requisicao' => $id
                            
                        ];
                
                        $table->insert($data);

                        $query = " 
                            UPDATE dbo.zcrmportal_variaveis_req
                            SET status = 4
                            WHERE id = '".$id."'
                        ";
                        // exit('<pre>'.print_r($query,1));
                        return $this->dbportal->query($query);
                    }
                }
            }
        }

        if($result){

            $table = $this->dbportal->table('zcrmportal_variaveis_aprovacao');
            $data = [
                'id_user' => $this->logId,
                'id_hierarquia' => '', 
                'dtcad' => $this->now, 
                'tipo' => $tipo,
                'nivel_apr_area' => '3',
                'observacao' => $justificativa,
                'id_requisicao' => $id
                
            ];
    
            $table->insert($data);

            $query = " 
                UPDATE dbo.zcrmportal_variaveis_req
                SET status = 4
                WHERE id = '".$id."'
            ";
            // exit('<pre>'.print_r($query,1));
            return $this->dbportal->query($query);
        }
        return false;

    }


    public function logCalculo($id)
    {

     
        $result = $this->dbportal
            ->table("zcrmportal_variaveis_log_valores")
            ->select('zcrmportal_usuario.nome,zcrmportal_variaveis_req.dtenvio, zcrmportal_variaveis_req.tipo, zcrmportal_variaveis_log_valores.*')
            ->join('zcrmportal_usuario', 'zcrmportal_usuario.id = zcrmportal_variaveis_log_valores.id_user')
            ->join('zcrmportal_variaveis_req', 'zcrmportal_variaveis_log_valores.id_req = zcrmportal_variaveis_req.id')
            ->where('id_req', $id)
          
            ->get();
 
            return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : [];

    }
    public function historico($id)
    {

        
     
        $result = $this->dbportal
            ->table("zcrmportal_variaveis_aprovacao")
            ->select('zcrmportal_usuario.nome,zcrmportal_variaveis_req.dtenvio, zcrmportal_variaveis_aprovacao.*')
            ->join('zcrmportal_usuario', 'zcrmportal_usuario.id = zcrmportal_variaveis_aprovacao.id_user')
            ->join('zcrmportal_variaveis_req', 'zcrmportal_variaveis_aprovacao.id_requisicao = zcrmportal_variaveis_req.id')
            ->where('id_requisicao', $id)
          
            ->get();

            return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : [];

    }

    public function historico2($situacao)
    {
        $builder = $this->dbportal
        ->table("zcrmportal_variaveis_req")
        ->select('zcrmportal_usuario.nome, zcrmportal_variaveis_req.valores,zcrmportal_variaveis_req.tiporeq, zcrmportal_variaveis_req.status, zcrmportal_variaveis_aprovacao.*')
        ->join('zcrmportal_variaveis_aprovacao', 'zcrmportal_variaveis_aprovacao.id_requisicao = zcrmportal_variaveis_req.id')
        ->join('zcrmportal_usuario', 'zcrmportal_usuario.id = zcrmportal_variaveis_aprovacao.id_user');
        $builder->whereIn('nivel_apr_area', [6, 3, 5]);
        $builder->where('zcrmportal_variaveis_req.coligada', $this->coligada);
        // Verifica se foi passada uma situação
        if ($situacao) {
           $builder->where('zcrmportal_variaveis_req.status', $situacao);
 
        }

        $result = $builder->get();
       
        return ($result->getNumRows() > 0) 
            ? $result->getResult() 
            : [];

    }
    public function historicoGestor($situacao)
    {
        $builder = $this->dbportal
        ->table("zcrmportal_variaveis_req")
        ->select('zcrmportal_usuario.nome, zcrmportal_variaveis_req.valores, zcrmportal_variaveis_req.status, zcrmportal_variaveis_aprovacao.*')
        ->join('zcrmportal_variaveis_aprovacao', 'zcrmportal_variaveis_aprovacao.id_requisicao = zcrmportal_variaveis_req.id')
        ->join('zcrmportal_usuario', 'zcrmportal_usuario.id = zcrmportal_variaveis_aprovacao.id_user');
        $builder->where('zcrmportal_variaveis_req.coligada', $this->coligada);
      
        $builder->where('zcrmportal_variaveis_aprovacao.id_user',  $this->logId);
        // Verifica se foi passada uma situação
        if ($situacao) {
            $builder->where('zcrmportal_variaveis_req.status', $situacao);
        }
        
        $builder->whereIn('nivel_apr_area', [2, 5]);
        $result = $builder->get();
      
        return ($result->getNumRows() > 0) 
            ? $result->getResult() 
            : [];

    }

    public function listaEventos()
    {
        $result = $this->dbrm
            ->table("PEVENTO")
            ->select('CODIGO, DESCRICAO')
            ->where('CODCOLIGADA', $this->coligada)
            ->where('ISNULL(INATIVO,0)', 0)
            ->get();
        
            return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : [];

    }

    public function listaSecao()
    {
        $result = $this->dbrm
            ->table("PSECAO")
            ->select('CODIGO, DESCRICAO')
            ->where('CODCOLIGADA', $this->coligada)
            ->where('ISNULL(SECAODESATIVADA,0)', 0)
            ->where('LEN(CODIGO)', TAMANHO_SECAO)
            ->get();
        
            return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : [];
    }
    public function listaParentesco()
    {

     
        $result = $this->dbrm
            ->table("PCODPARENT")
            ->select('CODCLIENTE, DESCRICAO')
            ->get();
        
            return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : [];
    }
    public function listaSecaoGestor($rh = false, $chapa = false)
    {
        $mHierarquia = Model('HierarquiaModel');
        $objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
        $isLider = $mHierarquia->isLider();
       
        $chapas_lider = [];
        if ($isLider) {
            foreach ($objFuncLider as $value) {
                $chapas_lider[] = $value['chapa'];
            }
        }
        
        $secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer($chapa);
        $codsecoes = [];
        if ($secoes) {
            foreach ($secoes as $Secao) {
                $codsecoes[] = $Secao['codsecao'];
            }
        }
      
        $result = $this->dbrm
            ->table("PSECAO")
            ->select('CODIGO, DESCRICAO')
            ->where('CODCOLIGADA', $this->coligada)
            ->where('ISNULL(SECAODESATIVADA,0)', 0)
            ->where('LEN(CODIGO)', TAMANHO_SECAO);
        
        if (!$rh && (!empty($chapas_lider) || !empty($codsecoes))) {
            $result->groupStart(); // Inicia um grupo de condições
            
            if (!empty($chapas_lider)) {
                
            }
            
            if (!empty($codsecoes)) {
                $result->orWhereIn('PSECAO.CODIGO', $codsecoes);
            }
            
            $result->groupEnd(); // Fecha o grupo de condições
        }
        
        $result = $result->get();
       
        return ($result->getNumRows() > 0) 
                ? $result->getResult() 
                : [];
    }
    
    public function listaFiliais()
    {
        $result = $this->dbrm
            ->table("GFILIAL")
            ->select('CODFILIAL, NOME')
            ->where('CODCOLIGADA', $this->coligada)
            ->where('ATIVO', 1)
            ->get();
        
            return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : [];
    }

    public function listaCargos()
    {
        $result = $this->dbrm
            ->table("PCARGO")
            ->select('CODIGO, NOME')
            ->where('CODCOLIGADA', $this->coligada)
            ->where('INATIVO', 0)
            ->get();
        
            return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : [];
    }

    public function saveParametros($request)
    {

        $data = [
            'coligada'      => $this->coligada,
            'tipo'          => $request['tipo'],
            'parametros'    => json_encode($request)
        ];
        
        return self::saveData($data);

    }

    public function saveRequisicao($request, $rh = false)
    {   
        $valida_req = false;
        if($rh){
            $aprovador = $request['funcionario'];
        }else{
            $aprovador = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        }

        $gestor = self::funcGestor($aprovador);

        if($request['funcionario']){
            $chapa =$request['funcionario'];
            $valida_req = self::validaReq($request['funcionario'],  $request['tipo'],  $request['tipoReq']);
        }else{
            $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $valida_req = self::validaReq($request['funcionario'],  $request['tipo'],  $request['tipoReq']);
        }
        
        if($valida_req){
           
            return 'achou';
        }
        $data = [
            'coligada'  => $this->coligada,
            'tipo'      => $request['tipo'],
            'valores'   => json_encode($request),
            'status'    => '1',
            'chapa'     =>  $chapa,
            'tiporeq'     => $request['tipoReq'],
            'aprovador' => $gestor[0]['CPF_GESTOR_IMEDIATO'],
            'periodo'     => date('Y-m'),
            'nome_aprovador' => $gestor[0]['NOME']
            
        ];
        
        return self::saveReq($data);

    }

    public function saveAnexo($id, $dados)
    {
       
        $file_name = $dados->getName();
        $file_type = $dados->getMimeType();
        $file_size = $dados->getSize();
        $file_data = base64_encode(file_get_contents($dados->getTempName()));
		
       $anexo = $this->dbportal->query(
            "INSERT INTO zcrmportal_variaveis_req_anexo (id_req, coligada, usucad, dtcad, file_type, file_size, file_name, file_data) 
                VALUES 
            ('{$id}','{$this->coligada}', {$_SESSION['log_id']}, '{$this->now}', '{$file_type}', '{$file_size}', '{$file_name}', '{$file_data}')"
        );
        
        return $anexo;

    }
    public function getAnexos($id)
    {
        
        $query = " SELECT * FROM zcrmportal_variaveis_req_anexo WHERE id_req= '".$id."' order by id";
       
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                    ? $result->getResult() 
                    : false;

    }

    public function valida13($request)
    {
        
        $query = " 
        SELECT * FROM zcrmportal_variaveis_req WHERE status IN('1','2','3','4','7') AND tipo = '".$request['tipo']."' AND chapa ='".$request['funcionario']."'  AND periodo = '".date('Y-m')."' ";
        //exit('<pre>'.print_r($query,1));
        $result = $this->dbportal->query($query);

        if($result){
            if($result->getNumRows() > 0){
                return $result->getResultArray() ;
            }
        }

        $query = " 
            SELECT *
            FROM dbo.zcrmportal_ferias
            WHERE chapa = '".$request['funcionario']."'
            AND YEAR(prog_ini) = YEAR(GETDATE())
            AND YEAR(prog_fim) = YEAR(GETDATE())
            AND salario13 = 1
            AND situacao <> 9";
        

        $result = $this->dbportal->query($query);
        if($result){
            if($result->getNumRows() > 0){
                return $result->getResultArray() ;
            }
        }

        $query = " 
            SELECT 
        
                * 
            
            FROM PFFINANC A 
            
                INNER JOIN PEVENTO B ON A.CODCOLIGADA = B.CODCOLIGADA AND B.CODIGO = A.CODEVENTO
            
            WHERE 
            
                B.CODIGOCALCULO IN (9)
            
            AND	A.CODCOLIGADA    = '{$this->coligada}' /* Cód Colgada do Funcionário*/
            
            AND ANOCOMP          = '".date('Y')."' /* Ano da data de início das Férias */
            
            AND CHAPA            = '".$request['funcionario']."' /* Chapa do Funcionário */
        ";
        $result = $this->dbrm->query($query);
        if($result){
            if($result->getNumRows() > 0){
                return $result->getResultArray() ;
            }
        }

        return false;

    }
    
    public function validaReq($chapa, $tipo, $tipoReq)
    {
        if($chapa){
            $ft_chapa =" AND chapa ='".$chapa."'";
        }
        $query = " 
        SELECT * FROM zcrmportal_variaveis_req WHERE status IN('1','2','3','4','7') AND tipo = '".$tipo."' AND coligada = '".$this->coligada."' AND tiporeq = '".$tipoReq."' AND periodo = '".date('Y-m')."' ".$ft_chapa;
        //  exit('<pre>'.print_r($query,1));
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    public function saveUpdateRequisicao($request)
    {
      
        $data = [
            'coligada'      => $this->coligada,
            'tipo'          => $request['tipo'],
            'valores'    => json_encode($request),
            'chapa'    => $request['funcionario'],
            'tiporeq'     => $request['tipoReq'],
        ];
     
       
        return self::saveReq($data,$request['id']);

    }

    
    
    public function getReq(int $tipo, $dt_ini = false, $dt_fim = false, $chapa = false, $aprovacao = false, $situacao = false)
    {
        try {
            $builder = $this->dbportal
                ->table('zcrmportal_variaveis_req')
                ->select('zcrmportal_usuario.nome, zcrmportal_variaveis_req.*')
                ->join('zcrmportal_usuario', 'zcrmportal_usuario.id = zcrmportal_variaveis_req.usucad')
                ->where('coligada', $this->coligada)
                ->where('tipo', $tipo);
                
    
                if ($dt_ini && $dt_fim) {
                    $dt_fim = date('Y-m-d 23:59:59', strtotime($dt_fim));
                    $builder->where("zcrmportal_variaveis_req.dtcad BETWEEN '$dt_ini' AND '$dt_fim'");
                }
                
    
            if ($chapa){
                $builder->where('chapa', $chapa);
            }
            if ($situacao){
                
                $builder->where('status', $situacao);
            }

            
            if ($aprovacao) {
                $builder->groupStart()
                ->whereIn('status', [2, 9])
                ->where('aprovador', $_SESSION['log_login']);
                if($aprovacao == '2'){
                
                    $builder->orWhereIn('status', [3, 7,8]); 
                }
                $builder->groupEnd(); 
            }else{
                $builder->where('usucad', $this->logId);
            }
    
            $builder->orderBy('zcrmportal_variaveis_req.id', 'ASC');
            $result = $builder->get();
           
            if ($result === false) {
                throw new \Exception("Erro na execução da query: " . $this->dbportal->getLastQuery());
            }
    
            return ($result->getNumRows() > 0) 
                    ? $result->getResult()
                    : [];
                    
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            log_message('error', $e->getMessage());
            return [];
        }
    }
    public function getReqDados($id)
    {
        try {
            $result = $this->dbportal
                ->table('zcrmportal_variaveis_req')
                ->where('id', $id)
                ->get();
    
    
            return ($result->getNumRows() > 0) 
                    ? $result->getResult()
                    : [];
                    
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            log_message('error', $e->getMessage());
            return [];
        }
    }
    
    
    

    public function getParametros(int $tipo)
    {
      
        $result = $this->dbportal
            ->table('zcrmportal_variaveis_parametros')
            ->where('coligada', $this->coligada)
            ->where('tipo', $tipo)
            ->get();

            return ($result->getNumRows() > 0) 
                    ? json_decode($result->getResult()[0]->parametros)
                    : [];
    }

    private function pInsert($table, $data)
    {
        try {
            $data['usucad'] = $this->logId;
            $data['dtcad']  = $this->now;
            return $table->insert($data);
        }catch(\Exception $e){
            return false;
        }
    }

    private function mUpdate($table, $data)
    {
        try {
            $data['usualt'] = $this->logId;
            $data['dtalt']  = $this->now;
            return $table->where('tipo', $data['tipo'])->where('coligada', $this->coligada)->update($data);
        }catch(\Exception $e){
            return false;
        }
    }

    private function saveData($data)
    {
        try {
            $isUpdate = $this->dbportal->table('zcrmportal_variaveis_parametros')->where('coligada', $this->coligada)->where('tipo', $data['tipo'])->get()->getNumRows();
            $table = $this->dbportal->table('zcrmportal_variaveis_parametros');
            return ($isUpdate > 0) ? self::mUpdate($table, $data) : self::pInsert($table, $data);
        }catch(\Exception $e){
            return false;
        }
    }

    private function pInsertReq($table, $data)
    {
        try {
            $data['usucad'] = $this->logId;
            $data['dtcad']  = $this->now;
              // Inserir os dados na tabela
            $table->insert($data);
           
            // Captura o ID da última inserção
            $insertId = $this->dbportal->insertID();
            
            return $insertId;
          
        }catch(\Exception $e){
            return false;
        }
    }

    private function mUpdateReq($table, $data, $id)
    {
        try {
            $data['usualt'] = $this->logId;
            $data['dtalt']  = $this->now;
            return $table->where('id', $id)->update($data);
        }catch(\Exception $e){
            return false;
        }
    }

    private function saveReq($data, $id = false)
    {
        try {
            $table = $this->dbportal->table('zcrmportal_variaveis_req');
          
            

            
            if($id > 0){
                return  self::mUpdateReq($table, $data, $id);
            }else{
                return  self::pInsertReq($table, $data);
            }
           
        }catch(\Exception $e){
            return false;
        }
    }

}