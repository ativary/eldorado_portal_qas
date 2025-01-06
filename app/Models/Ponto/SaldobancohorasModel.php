<?php
namespace App\Models\Ponto;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class SaldobancohorasModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $log_id;
    private $now;
    private $coligada;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->log_id   = session()->get('log_id');
        $this->coligada = session()->get('func_coligada');
        $this->now      = date('Y-m-d H:i:s');

        if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
    }
    
    public function ListarSecaoUsuario($codsecao = null, $rh = false)
    {

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		$isGestorLider = false;
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
			$isGestorLider = true;
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
			$isGestorLider = true;
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		$chapaGestor = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $chapaFunc = " A.CHAPA = '{$chapaGestor}' ";
            $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor." OR {$chapaFunc}) ";
		if(!$isGestorLider){
			$qr_secao = " AND 1 = 2 ";
			// $qr_secao = " AND A.CHAPA = '".(util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null)."' ";
		}
		if($rh) $qr_secao = "";
		
		// lista seções
		$filtro_secao = ($codsecao != null) ? " AND A.CODSECAO = '{$codsecao}' " : "";

		switch(DBRM_TIPO){
            case 'sqlserver': $length = " AND LEN(A.CODSECAO) = ".TAMANHO_SECAO; break;
            case 'oracle': $length = " AND LENGTH(A.CODSECAO) = ".TAMANHO_SECAO; break;
            default: $length = ""; break;
        }

		$query = " 
			SELECT 
				A.CODSECAO CODIGO, 
				B.DESCRICAO
				
			FROM 
				PFUNC A,
				PSECAO B
				
			WHERE 
				    A.CODCOLIGADA = '{$this->coligada}'
				AND A.CODCOLIGADA = B.CODCOLIGADA
				AND B.SECAODESATIVADA = 0
				AND A.CODSECAO = B.CODIGO
				{$qr_secao}
                {$filtro_secao}
				{$length}
			GROUP BY
				A.CODSECAO, 
				B.DESCRICAO
				
			ORDER BY
				B.DESCRICAO
		";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
    }
    
    public function ListarFuncionariosSecao($codsecao = null, $dados = false)
    {

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		$isGestorLider = false;
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
			$isGestorLider = true;
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
			$isGestorLider = true;
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		$chapaGestor = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
            $chapaFunc = " A.CHAPA = '{$chapaGestor}' ";
            $qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor." OR {$chapaFunc}) ";
		if(!$isGestorLider){
			$qr_secao = " AND A.CHAPA = '".(util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null)."' ";
		}
		
		// lista seções
		$filtro_secao = ($codsecao != null) ? " AND A.CODSECAO = '{$codsecao}' " : "";

		if($dados){
			if($dados['rh']) $qr_secao = "";
		}

		$query = " 
			SELECT 
				A.CHAPA,
                A.NOME
				
			FROM 
				PFUNC A,
				PSECAO B
				
			WHERE 
				    A.CODCOLIGADA = '{$this->coligada}'
				AND A.CODCOLIGADA = B.CODCOLIGADA
				AND B.SECAODESATIVADA = 0
				AND A.CODSECAO = B.CODIGO
				AND A.CODSITUACAO NOT IN ('D')
				{$qr_secao}
                {$filtro_secao}

			GROUP BY
				A.CHAPA,
                A.NOME
				
			ORDER BY
				A.NOME
		";
		// exit('<pre>'.$query);
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
    }

    public function SaldoBancoHoras($codsecao = null, $chapa = null, $periodo = null, $dados = false)
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
		$chapaGestor = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
		$chapaFunc = " A.CHAPA = '{$chapaGestor}' ";
		$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor." OR {$chapaFunc}) ";
		
		// lista seções
		$filtro_secao = ($codsecao != null) ? " AND B.CODSECAO = '{$codsecao}' " : "";
		$filtro_chapa = ($chapa != null) ? " AND B.CHAPA = '{$chapa}' " : "";
		if($chapa != null) $qr_secao = "";

		if($dados && $chapa == null){
			if($dados['rh']) $qr_secao = "";
		}

		$query = " 
            SELECT 
                A.CHAPA,
                B.NOME,
                SUM((CASE WHEN CODEVENTO IN ('001','002') AND VALOR > 0
                THEN ((VALOR-(VALORCOMPENSADO+VALORLANCADO))*-1) ELSE VALOR-((VALORCOMPENSADO+VALORLANCADO))END)) SALDO,
				(DATAINICIO + LIMDIASCOMPENSACAO -1) DATAFIMLIMITEBH
            FROM 
                ABANCOHORFUNDETALHE A,
                PFUNC B
				JOIN APARFUN C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CHAPA = B.CHAPA
				LEFT JOIN ALIMBANCOHOR D ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODPARCOL = C.CODPARCOL AND '{$periodo}' BETWEEN DATAINICIO AND (DATAINICIO + LIMDIASCOMPENSACAO -1)
            WHERE 
                A.CODCOLIGADA = '{$this->coligada}'
                AND A.DATA >= '1900-01-01'
                AND A.DATA <= '{$periodo}'
                AND A.CHAPA = B.CHAPA
                AND A.CODCOLIGADA = B.CODCOLIGADA
				/*AND B.CODSITUACAO NOT IN ('D')*/
				AND (
                    SELECT TOP 1 REGISTRO FROM (
                        SELECT
                            CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                            CASE
                                WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                ELSE GETDATE()
                            END DATA
                        FROM
                            PFUNC
                        WHERE
                            CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
                    )X WHERE X.DATA >= '{$dados['dataInicio']}'
                    ORDER BY X. DATA ASC
                ) IS NOT NULL
                {$qr_secao}
                {$filtro_secao}
                {$filtro_chapa}
            GROUP BY 
                A.CHAPA,
                B.NOME,
				(DATAINICIO + LIMDIASCOMPENSACAO -1)
            ORDER BY 
                B.NOME
		";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

	public function ListarPeriodoPonto()
	{
		$result = $this->dbrm->query(" SELECT TOP 12 CODCOLIGADA, INICIOMENSAL, FIMMENSAL FROM APERIODO WHERE CODCOLIGADA = 1 ORDER BY INICIOMENSAL DESC ");
		if(!$result) return false;
		return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
	}

}