<?php
namespace App\Models\Ponto;
use SimpleXMLElement;

use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class AprovaModel extends Model
{

    protected $dbportal;
    protected $dbrm;
    protected $mEscala;
    private $log_id;
    private $now;
    private $coligada;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->log_id   = session()->get('log_id');
        $this->coligada = session()->get('func_coligada');
        $this->now      = date('Y-m-d H:i:s');
		$this->mEscala = model('Ponto/EscalaModel');

        if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
    }

	// #############################################################################
	// APROVA DE ESCALA
	// #############################################################################
	public function aprovaEscala($idEscala, $rh = false)
	{

		

		$escala = $this->dbportal->query(" SELECT chapa, situacao FROM zcrmportal_escala WHERE id = '{$idEscala}' AND situacao IN (10,2) ");
		$result = ($escala) ? $escala->getResultArray() : null;
		
		if($result){

			$situacao = $result[0]['situacao'];
			
			$chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
			if($chapaUser == $result[0]['chapa']) return false;

			if(!$rh){
				if(!self::isGestorOrLiderAprovador($result[0]['chapa'])){
					return false;
				}
			}

			switch($situacao){
				case 10: $query = " UPDATE zcrmportal_escala SET situacao = 2, dtapr = '".date('Y-m-d H:i:s')."', usuapr = '{$this->log_id}' WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' AND situacao = 10 "; break;
				case 2: $query = " UPDATE zcrmportal_escala SET situacao = 3, dtrh = '".date('Y-m-d H:i:s')."', usurh = '{$this->log_id}' WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' AND situacao = 2 "; break;
				default: return false; break;
			}

			$this->dbportal->query($query);
        	if($this->dbportal->affectedRows() > 0){

				$query = " SELECT id FROM zcrmportal_escala WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' and termo_obrigatorio = 1 AND situacao = 1 ";
                $result = $this->dbportal->query($query);
                if($result->getNumRows() > 0){
                    $this->mEscala->EscalaNotificaSolicitante($idEscala);
                }

				if($situacao == 2){
					$result = $this->mEscala->SincronizaRM_Horario($idEscala);

					if($result === false){

						// em caso de erro na sincronização com totvs
						// faz o rollback da tabela do portal
						$query = "
							UPDATE 
								zcrmportal_escala
							SET
								usurh = NULL,
								dtrh = NULL,
								situacao = 2
							WHERE
									id = {$idEscala}
								AND situacao = 3
						";
						$this->dbportal->query($query);

						return false;

					}
					
				}

				return true;

			}

		}

		return false;

	}

    // #############################################################################
	// APROVA BATIDA RH
	// #############################################################################
	public function aprovaBatidaRH($idbatida, $rh = false)
	{
		$query = " 
		SELECT
			COALESCE(ent1, ent2, ent3, ent4, ent5, sai1, sai2, sai3, sai4, sai5) AS batida,
			COALESCE(ident1, ident2, ident3, ident4, ident5, idsai1, idsai2, idsai3, idsai4, idsai5) AS idbatida,
			COALESCE(natent1, natent2, natent3, natent4, natent5, natsai1, natsai2, natsai3, natsai4, natsai5) AS natureza,
			COALESCE(justent1, justent2, justent3, justent4, justent5, justsai1, justsai2, justsai3, justsai4, justsai5) AS justificativa,
			COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) AS dtref,	
			dtponto,
			abn_dtini,
			abn_dtfim,
			abn_horaini,
			abn_horafim,
			abn_codabono,
			movimento,
			chapa,
			atitude_ini,
			atitude_fim,
			atitude_dt,
			atitude_tipo,
			justificativa_excecao
		FROM 
			zcrmportal_ponto_horas 
		WHERE 
				status IN ('1','2','3', 'A') 
			AND usu_delete IS NULL
			AND id = '" . $idbatida . "' ";
		$qry = $this->dbportal->query($query);
		$res = ($qry) ? $qry->getResultArray() : array();

		try{

			$atualizou = false;

			// $wsTotvs = model('WsrmModel');

			$chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
			if($chapaUser == $res[0]['chapa']) return false;

			if(!$rh){
				if(!self::isGestorOrLiderAprovador($res[0]['chapa'])){
					return false;
				}
			}


			// ***************************************************************
			// APROVA NATUREZA
			// ***************************************************************
			if ($res[0]['movimento'] == '3') {

				$queryRM = " UPDATE ABATFUN SET NATUREZA = '" . $res[0]['natureza'] . "' WHERE IDAAFDT = '" . $res[0]['idbatida'] . "' AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
				$resRM = $this->dbrm->query($queryRM);

				if($resRM) $atualizou = true;
			}
			// FIM  **********************************************************

			// ***************************************************************
			// APROVA DATA REFERECIA
			// ***************************************************************
			if ($res[0]['movimento'] == '4') {

				$queryRM = " UPDATE ABATFUN SET DATAREFERENCIA = '" . $res[0]['dtref'] . "' WHERE IDAAFDT = '" . $res[0]['idbatida'] . "' AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
				$resRM = $this->dbrm->query($queryRM);

				if($resRM) $atualizou = true;

			}
			// FIM  **********************************************************

			// ***************************************************************
			// APROVA EXCLUSAO DE BATIDA
			// ***************************************************************
			if ($res[0]['movimento'] == '2') {

				$queryRM = " DELETE FROM ABATFUN WHERE DATA = '" . $res[0]["dtponto"] . "' AND CHAPA = '" . $res[0]['chapa'] . "' AND BATIDA = '" . $res[0]["batida"] . "' AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
				$resRM = $this->dbrm->query($queryRM);


				$queryRM = " DELETE FROM AJUSTBAT WHERE DATA = '" . $res[0]["dtponto"] . "' AND CHAPA = '" . $res[0]['chapa'] . "' AND BATIDA = '" . $res[0]["batida"] . "' AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
				$resRM = $this->dbrm->query($queryRM);

				$queryRM = " DELETE FROM AAFDT WHERE ID = '" . $res[0]['idbatida'] . "' AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
				$resRM = $this->dbrm->query($queryRM);

				$minhaquery = " DELETE FROM zcrmportal_ponto_horas  WHERE dtponto = '" . $res[0]["dtponto"] . "' AND chapa = '" . $res[0]['chapa'] . "' AND coligada = '" . $_SESSION['func_coligada'] . "' AND movimento = '2' AND id = '" . $idbatida . "'";
				$res = $this->dbportal->query($minhaquery);

				if($resRM) $atualizou = true;
			}
			// FIM  **********************************************************

			// ***************************************************************
			// APROVA INCLUSAO DE BATIDA
			// ***************************************************************
			if ($res[0]['movimento'] == '1') {
				
				$queryRM = " INSERT INTO ABATFUN 
					(CODCOLIGADA, CHAPA, DATA, DATAREFERENCIA, BATIDA, STATUS, NATUREZA, RECCREATEDBY, RECCREATEDON, DATAINSERCAO) 
						VALUES 
					('".$_SESSION['func_coligada']."', '".$res[0]['chapa']."', '".$res[0]["dtponto"]."', '".$res[0]["dtref"]."', '".$res[0]["batida"]."', 'T', '".$res[0]["natureza"]."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."') 
				";
				$resRM = $this->dbrm->query($queryRM);
				
				$queryRM = " INSERT INTO AJUSTBAT 
					(CODCOLIGADA, CHAPA, DATA, BATIDA, JUSTIFICA, RECCREATEDBY, RECCREATEDON) VALUES
					('".$_SESSION['func_coligada']."', '".$res[0]['chapa']."', '".$res[0]["dtponto"]."', '".$res[0]["batida"]."', '".trim(addslashes($res[0]["justificativa"]))."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
				";
				$resRM = $this->dbrm->query($queryRM);

				if($resRM) $atualizou = true;

			}
			// FIM  **********************************************************

			// ***************************************************************
			// APROVA ABONO
			// ***************************************************************
			if ($res[0]['movimento'] == '5' || $res[0]['movimento'] == '6' || $res[0]['movimento'] == '9') {

				$SOLUCAOCONFLITO = 0;
			
				$desconsidera = 1;
				
				if($res[0]['movimento'] == '9'){
					$desconsidera = 0;
				}
				
				if($res[0]['movimento'] == '5' ) $SOLUCAOCONFLITO = 1;
				
				if($res[0]['abn_horaini'] > $res[0]['abn_horafim']){
					
					$queryRM = " INSERT INTO AABONFUN
						(CODCOLIGADA, CHAPA, DATA, CODABONO, HORAINICIO, HORAFIM, SOLUCAOCONFLITO, ABONOFUTURO, COLIGADARESP, DESCONSIDERA,RESPONSAVEL, RECCREATEDBY, RECCREATEDON)
							VALUES
						('".$_SESSION['func_coligada']."', '".$res[0]['chapa']."', '".$res[0]["dtponto"]."', '".$res[0]["abn_codabono"]."', '".$res[0]["abn_horaini"]."', '1440', '".$SOLUCAOCONFLITO."', 0, '".$_SESSION['func_coligada']."', ".$desconsidera.", '".$res[0]['chapa']."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
					";
					//echo $queryRM;
					$resRM = $this->dbrm->query($queryRM);				
					
					$queryRM = " INSERT INTO AABONFUN
						(CODCOLIGADA, CHAPA, DATA, CODABONO, HORAINICIO, HORAFIM, SOLUCAOCONFLITO, ABONOFUTURO, COLIGADARESP, DESCONSIDERA,RESPONSAVEL, RECCREATEDBY, RECCREATEDON)
							VALUES
						('".$_SESSION['func_coligada']."', '".$res[0]['chapa']."', '".$res[0]["abn_dtfim"]."', '".$res[0]["abn_codabono"]."', '0', '".$res[0]["abn_horafim"]."', '".$SOLUCAOCONFLITO."', 0, '".$_SESSION['func_coligada']."', ".$desconsidera.", '".$res[0]['chapa']."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
					";
					$resRM = $this->dbrm->query($queryRM);
					
				}else{
					
					$queryRM = " INSERT INTO AABONFUN
						(CODCOLIGADA, CHAPA, DATA, CODABONO, HORAINICIO, HORAFIM, ABONOFUTURO, COLIGADARESP, DESCONSIDERA,RESPONSAVEL, RECCREATEDBY, RECCREATEDON)
							VALUES
						('".$_SESSION['func_coligada']."', '".$res[0]['chapa']."', '".$res[0]["abn_dtini"]."', '".$res[0]["abn_codabono"]."', '".$res[0]["abn_horaini"]."', '".$res[0]["abn_horafim"]."', 0, '".$_SESSION['func_coligada']."',".$desconsidera.", '".$res[0]['chapa']."', 'PORT.".$_SESSION['log_id']."', '".date('Y-m-d H:i:s')."')
					";
					$resRM = $this->dbrm->query($queryRM);
					
				}

				if($resRM) $atualizou = true;
				
			}

			// ***************************************************************
			// APROVA INCLUSAO JUSTIFICATIVA DE EXCEÇÕES
			// ***************************************************************
			if($res[0]['movimento'] == '7'){
				
				$update = " 
					UPDATE 
						AJUSTFUN 
					SET 
						JUSTIFICATIVA = 'Descontar' ,
						RECMODIFIEDBY = 'USERPORTAL_".$_SESSION['log_id']."',
						RECMODIFIEDON = '".date('Y-m-d H:i:s')."'
						
					WHERE 
							DATA = '{$res[0]["atitude_dt"]}' 
						AND CHAPA = '{$res[0]['chapa']}' 
						AND TIPOOCORRENCIA IN ('A','F','AC', 'SA')
						AND CODCOLIGADA = '{$this->coligada}'
						
					";
					$resRM = $this->dbrm->query($update);
					if($resRM) $atualizou = true;
			}

			if($res[0]['movimento'] == '8'){

				$update = " 
				  UPDATE 
				    AJUSTFUN 
				  SET 
				    ATITUDE = '{$res[0]["atitude_tipo"]}',  
				    RECMODIFIEDBY = 'PORTAL.{$this->log_id}',
					RECMODIFIEDON = '{$this->now}'

				  WHERE 
				      DATA = '{$res[0]["atitude_dt"]}' 
				    AND CHAPA = '{$res[0]['chapa']}' 
				    AND TIPOOCORRENCIA IN ('A','F','AC', 'SA')
				    AND CODCOLIGADA = '{$this->coligada}' 
				";
				// $update = "
				// 	UPDATE 
				// 		AJUSTFUN 
				// 	SET 
				// 		ATITUDE = '".$res[0]["atitude_tipo"]."',
				// 		RECMODIFIEDBY = 'USERPORTAL_".$_SESSION['log_id']."',
				// 		RECMODIFIEDON = '".date('Y-m-d H:i:s')."'
						
				// 	WHERE 
				// 		CODCOLIGADA = '".$_SESSION['func_coligada']."' 
				// 		AND CHAPA = '".$res[0]['chapa']."'
				// 		AND INICIO = '".$res[0]["atitude_ini"]."'
				// 		AND FIM = '".$res[0]["atitude_fim"]."'
				// 		AND TIPOOCORRENCIA IN ('A','F','AC')
				// 		AND DATA = '".$res[0]["atitude_dt"]."'
				// ";
				$resRM = $this->dbrm->query($update);
				if($resRM) $atualizou = true;
				
			}

			
			if($atualizou){
				$update_batida = " UPDATE zcrmportal_ponto_horas SET status = 'S', aprrh_user = '" . $_SESSION['log_id'] . "', aprrh_data = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $idbatida . "' ";
				$res_update = $this->dbportal->query($update_batida);
				return true;
			}

		}catch(\Exception $e){

			return $e->getMessage();
	
		}

		return true;
	}

    // #############################################################################
	// REPROVA TROCA DE ESCALA
	// #############################################################################
	public function reprovaEscala($idEscala, $motivo_reprova = "", $rh = false)
	{

		$escala = $this->dbportal->query(" SELECT chapa FROM zcrmportal_escala WHERE id = '{$idEscala}' AND situacao != '3' ");
		$result = ($escala) ? $escala->getResultArray() : null;

		if($result){

			$chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
			if($chapaUser == $result[0]['chapa']) return false;

			if(!$rh){
				if(!self::isGestorOrLiderAprovador($result[0]['chapa'])){
					notificacao('danger', 'Alguns movimento não podem ser aprovados.');
					return false;
				}
			}

			$situacao = $result[0]['situacao'];

			$query = " UPDATE zcrmportal_escala SET documento = NULL, dtupload = NULL, usuupload = NULL, situacao = 8, motivocancelado = '(".((!$rh) ? 'Gestor' : 'RH').") {$motivo_reprova}', dtcancelado = '".date('Y-m-d H:i:s')."', usucancelado = '{$this->log_id}' WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' AND situacao NOT IN (9, 3, 8) ";
			$this->dbportal->query($query);
			if($this->dbportal->affectedRows() > 0){
				return responseJson('success', 'Escala REPROVADA com sucesso');
			}

        }

		return false;

	}

    // #############################################################################
	// REPROVA BATIDA RH
	// #############################################################################
	public function reprovaBatidaRH($idbatida, $tipo, $motivo_reprova = "", $rh = false)
	{
		$dados = explode('|', $idbatida);

		$query = " SELECT * FROM zcrmportal_ponto_horas WHERE id = '{$dados[2]}' ";
		$result = $this->dbportal->query($query); 
		$chapa = $result->getResultArray()[0]['chapa'];

		if(!$rh){
			if(!self::isGestorOrLiderAprovador($chapa)){
				notificacao('danger', 'Alguns movimento não podem ser aprovados.');
				return false;
			}
		}

		// REPROVA RH
		if ($tipo == 'RH') {

			$query = " 
			UPDATE zcrmportal_ponto_horas SET status = 
				/*CASE
					WHEN movimento in (1,2) THEN 1
					WHEN abn_codabono in ('0003','0008','0009','0012','0016','0018','0019','0020','0024','0023') THEN 2
					ELSE 3
				END*/
				3
			, apr_user = NULL, apr_data = NULL, aprgestor_data = NULL, aprgestor_user = NULL, motivo_reprova = '".addslashes($motivo_reprova)."', usu_reprova = '".$_SESSION['log_id']."', dt_reprova = '".date('Y-m-d H:i:s')."' WHERE id = '".$dados[2]."'
				AND status <> 'S'
			";
			$result = $this->dbportal->query($query);

			if($result){
				$query = "

					INSERT INTO zcrmportal_ponto_horas_reprova
					SELECT * FROM zcrmportal_ponto_horas WHERE id = '".$dados[2]."'
				
				";
				$this->dbportal->query($query);
			}

			return $result;
        }           
	}

	// #############################################################################
	// APROVA BATIDA GESTOR
	// #############################################################################
	public function	aprovaBatidaGestor($idbatida, $tipo){

		$dados = explode('|', $idbatida);
		// var_dump($dados);exit();

		// APROVA GESTOR
		if ($tipo == 'GESTOR') {

			$query = " UPDATE zcrmportal_ponto_horas SET status = '2', aprgestor_user = '".session()->get('log_id')."', aprgestor_data = GETDATE() WHERE id = '" . $dados[2] . "'";
			// echo $query;exit();
			return $this->dbportal->query($query); 
        }  
	}


    // #############################################################################
	// LISTA BATIDA PRO GESTOR
	// #############################################################################
	public function listaBatidaApr($status, $codfilial = false, $movimento = false, $tipo_abono = false, $ft_legenda = false, $ft_status = false, $dt_inicio = false, $dt_fim = false, $filtroChapa = '', $periodo = false, $dados = false)
	{

		// $periodo = "";
		// if($ft_status == 'S'){
		// 	if(strlen(trim($dt_inicio)) <= 0){
		// 		notificacao('danger', 'Data início do período não informado');
		// 		redireciona(base_url('ponto/aprova/historico'));
		// 	}
		// 	if(strlen(trim($dt_fim)) <= 0){
		// 		notificacao('danger', 'Data término do período não informado');
		// 		redireciona(base_url('ponto/aprova/historico'));
		// 	}

		// 	$dias = dataDiff($dt_inicio, $dt_fim);
		// 	if($dias > 60){
		// 		notificacao('danger', 'Período informado é superior a 60 dias.');
		// 		redireciona(base_url('ponto/aprova/historico'));
		// 	}

		// 	$periodo = " AND A.dtponto BETWEEN '{$dt_inicio}' AND '{$dt_fim}' ";
		// }


		// exit($periodo);
		$periodo    = explode('|', $periodo);
		$perInicio  = $periodo[0];
		$perFim     = $periodo[1];
		$periodo    = " AND A.dtponto BETWEEN '{$periodo[0]}' AND '{$periodo[1]}' ";

        $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
		if ($codfilial) {
			if ($codfilial != 'all') {

				// #################################################################
				// FILTRO POR TODAS SEÇÃO
				// #################################################################				
				$LT_CHAPAS = false;

				$chapasRM = " SELECT CHAPA FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODSECAO = '". $codfilial ."'";
				// exit($chapasRM);
				$qry = $this->dbrm->query($chapasRM);
				$resChapaRM = ($qry) ? $qry->getResultArray() : false;
				if ($resChapaRM && is_array($resChapaRM)) {
					foreach ($resChapaRM as $idc => $value) {
						$LT_CHAPAS .= "'" . $resChapaRM[$idc]['CHAPA'] . "',";
					}
				}

				if($resChapaRM && is_array($resChapaRM) && $resChapaRM > 0){
					$codfilial = " AND A.chapa IN (" . substr($LT_CHAPAS, 0, -1) . ") ";
				} else {
					return false;
				}

				// #################################################################

			} else {
				$codfilial = false;
			}
		}

		if ($movimento == 'X') {
			$movimentoA = " '5','6','7','8' ";
			$movimentoB = " '5','6','7' ";
		} elseif ($movimento == '8') {
			$movimentoA = " '1','2','3','4','5','6','7','8' ";
			$movimentoB = " '8' ";
		} else {
			$movimentoA = " '5','6','7','8' ";
			$movimentoB = " '5','6','7','8' ";
		}

		$hoje = "";
		switch(DBRM_TIPO){
			case 'sqlserver': $hoje = " GETDATE() "; break;
			case 'oracle': $hoje = " SYSDATE "; break;
		}

		

		$QUERY = " SELECT PFUNC.*, PPESSOA.CPF, PSECAO.DESCRICAO SECAO, PFUNCAO.NOME AS FUNCAO 
					FROM PFUNC, PPESSOA, PSECAO, PFUNCAO 
					WHERE PFUNC.CODCOLIGADA = '1' 
					--AND (PFUNC.DATADEMISSAO > ({$hoje} - 30) OR PFUNC.DATADEMISSAO IS NULL) 
					AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2900-12-31'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = PFUNC.CODCOLIGADA AND CHAPA = PFUNC.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= '{$perInicio}'
						ORDER BY X.DATA ASC
					) IS NOT NULL
					AND PFUNC.CODTIPO <> 'A' 
					AND PFUNC.CODPESSOA = PPESSOA.CODIGO 
					AND PFUNC.CODSECAO = PSECAO.CODIGO 
					AND PFUNC.CODFUNCAO = PFUNCAO.CODIGO 
					AND PFUNC.CODCOLIGADA = PSECAO.CODCOLIGADA 
					AND PFUNCAO.CODCOLIGADA = PFUNC.CODCOLIGADA 
					ORDER BY PFUNC.CHAPA ";

		$qry = $this->dbrm->query($QUERY);
		$FUNC = ($qry) ? $qry->getResultArray() : false;

		// #################################################################
		// FILTRO POR ABONO TIPO
		// #################################################################
		$QUERY = " SELECT CODIGO, DESCRICAO FROM AABONO WHERE CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' AND ATIVOPORTAL = 1 ORDER BY DESCRICAO ";
		#$ABONO = $this->execConsultaIntegra($QUERY);
		$qry = $this->dbrm->query($QUERY);
		$ABONO = ($qry) ? $qry->getResultArray() : false;

		// #################################################################
		// FILTRO POR SEÇÃO
		// #################################################################
		$LT_SECAO_FUNCA = false;

		$portal_secao = " SELECT * FROM zcrmportal_usuario_secao WHERE id_usu = '" . $_SESSION['log_id'] . "' ";
		#$resPortalSecao = $this->dbportal->query($portal_secao);
		$qry = $this->dbportal->query($portal_secao);
		$resPortalSecao = ($qry) ? $qry->getResultArray() : false;
		if ($resPortalSecao && is_array($resPortalSecao)) {
			foreach ($resPortalSecao as $idp => $value) {
				$LT_SECAO_FUNCA .= "'" . $resPortalSecao[$idp]['secao'] . "',";
			}
		}
		$FILTRO_SECAO = " --AND CODSECAO IN (" . substr($LT_SECAO_FUNCA, 0, -1) . ") ";

		// #################################################################
		// CHAPA DAS SEÇÕES
		// #################################################################
		$FILTRO_CHAPAS = false;
		if ($_SESSION['log_id'] == '1') {
			$query = " SELECT CHAPA FROM PFUNC WHERE (DATADEMISSAO > ({$hoje} - 30) OR DATADEMISSAO IS NULL) AND CODTIPO <> 'A' " . $FILTRO_SECAO . " ";
			// echo $query;
			$qry = $this->dbrm->query($query);
			$resCHAPAS = ($qry) ? $qry->getResultArray() : false;

			if ($resCHAPAS && is_array($resCHAPAS)) {
				foreach ($resCHAPAS as $idxx => $value) {
					$FILTRO_CHAPAS .= "'" . $resCHAPAS[$idxx]['CHAPA'] . "',";
				}
				$FILTRO_CHAPAS = " AND A.chapa in (" . substr($FILTRO_CHAPAS, 0, -1) . ", '" . $chapa . "') ";
			} else {
				$FILTRO_CHAPAS = " AND A.chapa in (" . $chapa . ") ";
			}
		}
		// #################################################################


		$filtro_gestor = " AND A.CHAPA IN ('') ";
		$mPortal  = model('PortalModel');

		$secaoGestor = $mPortal->listaFuncionarioSecao();
		if($secaoGestor){
			$filtro_gestor = "";
			foreach($secaoGestor as $key => $DadosFunc){
				$filtro_gestor .= "'{$DadosFunc['CHAPA']}',";
			}
			$filtro_gestor = " AND A.CHAPA IN (".rtrim($filtro_gestor, ',').")";
		}

		if($_SESSION['log_id'] == 1){
			$filtro_gestor = "";
		}

		//======= FILTRO POR ABONO ==========//by Matheus
		$FT_ABONO = false;
		if ($tipo_abono){

			$FT_ABONO = " AND A.abn_codabono = '".$tipo_abono."'";
		}

		//========== FILTRO POR MOVIMENTO ==============//by Matheus
		$FT_MOVIMENTO = false;
		if($ft_legenda){

			$FT_MOVIMENTO = " AND A.movimento = '". $ft_legenda . "'";
		}

		$FT_STATUS = false;
		if($ft_status){

			$FT_STATUS = " A.status = '". $ft_status . "'" ;
		} else {

			$FT_STATUS = " A.status in ('1','2')";
		}

		// filtro hierarquia
		/*$mHierarquia = model('HierarquiaModel');
		$Secoes   = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$isLider  = $mHierarquia->isLider();

		$in_secao = " AND 1 = 2 ";
		if($Secoes && !$dados['perfilRH']){
			$in_secao = "";
			if($isLider){
				// lider
				$Secoes = self::listaChapaLider();

				foreach($Secoes as $key =>$Chapa){
					$in_secao .= "'{$Chapa['chapa']}',";
				}
				$in_secao = " AND a.chapa IN (".rtrim($in_secao, ',').") ";
			}else{
				// gestor
				foreach($Secoes as $key =>$CodSecao){
					$in_secao .= "'{$CodSecao['codsecao']}',";
				}
				$in_secao = " AND B.CODSECAO IN (".rtrim($in_secao, ',').") ";
			}
		}*/






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
		$chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
		if($this->log_id  != 1){
			$in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") AND A.CHAPA != '{$chapaFunc}' ";
		}
		else{
			$in_secao = "";
		}

		$filtro_chapa = (strlen(trim($filtroChapa)) <= 0 || $filtroChapa == 'all') ? "" : " AND a.chapa = '{$filtroChapa}' ";

		if($dados['perfilRH']) $in_secao = "";

		// pega nome do funcionário
		$query = " 
			SELECT 
				A.CHAPA, 
				A.NOME,
				B.CPF,
				C.GESTOR_CHAPA,
				C.GESTOR_NOME,
				(CASE WHEN (A.DATADEMISSAO IS NULL OR A.DATADEMISSAO >= '{$perFim}') THEN 'Ativo' ELSE 'Demitido' END) CODSITUACAO
			FROM 
				PFUNC A
				INNER JOIN PPESSOA B ON B.CODIGO = A.CODPESSOA
				LEFT JOIN ".DBPORTAL_BANCO."..GESTOR_CHAPA C ON C.CHAPA = A.CHAPA AND C.CODCOLIGADA = A.CODCOLIGADA
			WHERE 
					A.CODCOLIGADA = '{$_SESSION['func_coligada']}' 
				--AND (A.DATADEMISSAO > ({$hoje} - 30) OR A.DATADEMISSAO IS NULL)
				AND (
                    SELECT TOP 1 REGISTRO FROM (
                        SELECT
                            CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                            CASE
                                WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                ELSE '2900-12-31'
                            END DATA
                        FROM
                            PFUNC
                        WHERE
                            CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
							AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                    )X WHERE X.DATA >= '{$perInicio}'
                    ORDER BY X. DATA ASC
                ) IS NOT NULL
				".(str_replace('B.CODSECAO','A.CODSECAO',$in_secao))."
				{$filtro_chapa}
			";
		// $result = $this->dbrm->query($query);
		// $dados_func = $result->getResultArray();

		$filtro_tipo = (strlen(trim($dados['filtro_tipo'])) != 0) ? " AND a.movimento = '{$dados['filtro_tipo']}' " : '';
		$filtro_filial = (strlen(trim($dados['filtro_filial'])) != 0) ? " AND B.CODFILIAL = '{$dados['filtro_filial']}' " : '';
		$filtro_legenda = "";
		$filtro_legenda2 = "";
		if((strlen(trim($dados['filtro_legenda'])) != 0)){
			if($dados['filtro_legenda'] == 10){
				$filtro_legenda2 = " AND a.situacao = '10' ";
			}
			if($dados['filtro_legenda'] == 2){
				$filtro_legenda = " AND 1 = 2 ";
				$filtro_legenda2 = " AND a.situacao = '2' ";
			}
		}
		
		
		(strlen(trim($dados['filtro_legenda'])) != 0) ? " AND a.situacao = '{$dados['filtro_legenda']}' " : '';
		
		$filtro_1 = "";
		$filtro_2 = "";
		if(strlen(trim($dados['filtro_tipo'])) > 0){
			if($dados['filtro_tipo'] == 21 || $dados['filtro_tipo'] == 22){
				$filtro_1 = " AND 1 = 2 ";
				$filtro_2 = " AND 1 = 1 ";
				$filtro_tipo = ($dados['filtro_tipo'] == 21) ? ' and a.movimento = 1 ' : ' and a.movimento = 2 ';
			}else{
				$filtro_1 = " AND 1 = 1 ";
				$filtro_2 = " AND 1 = 2 ";
			}
		}

		// nova sql Tiago
		$query = "
			SELECT DISTINCT X.* FROM (
				SELECT
					A.id,
					A.dtponto,
					null dtfolga,
					null codindice_folga,
					A.movimento,
					A.chapa,
					COALESCE(A.ent1, A.ent2, A.ent3, A.ent4, A.ent5, A.sai1, A.sai2, A.sai3, A.sai4, A.sai5) batida,
					COALESCE(A.justent1, A.justent2, A.justent3, A.justent4, A.justent5, A.justsai1, A.justsai2, A.justsai3, A.justsai4, A.justsai5) motivo,
					COALESCE(A.natent1, A.natent2, A.natent3, A.natent4, A.natent5, A.natsai1, A.natsai2, A.natsai3, A.natsai4, A.natsai5) natureza,
					COALESCE(A.dtrefent1, A.dtrefent2, A.dtrefent3, A.dtrefent4, A.dtrefent5, A.dtrefsai1, A.dtrefsai2, A.dtrefsai3, A.dtrefsai4, A.dtrefsai5) data_referencia,
					A.abn_dtfim,
					A.abn_horaini,
					A.abn_horafim,
					A.abn_codabono,
					(A.abn_horafim - A.abn_horaini) abn_totalhoras,
					A.possui_anexo,
					A.coligada,
					A.status,
					A.justificativa_abono_tipo,
					A.atitude_dt,
					A.atitude_ini,
					A.atitude_fim,
					A.atitude_tipo,
					A.atitude_justificativa,
					C.nome solicitante,
					(
						SELECT
							MAX(BB.CHAPA)
						FROM
							".DBRM_BANCO."..PPESSOA AA
							INNER JOIN ".DBRM_BANCO."..PFUNC BB ON BB.CODPESSOA = AA.CODIGO
						WHERE
								AA.CPF = C.login COLLATE Latin1_General_CI_AS
							AND BB.CODCOLIGADA = A.coligada
							AND (
								SELECT TOP 1 REGISTRO FROM (
									SELECT
										CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
										CASE
											WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
											ELSE '2900-12-31'
										END DATA
									FROM
										".DBRM_BANCO."..PFUNC
									WHERE
										CODCOLIGADA = BB.CODCOLIGADA AND CHAPA = BB.CHAPA
										AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
										AND DATAADMISSAO <= a.dtponto
								)X WHERE X.DATA >= a.dtponto
								ORDER BY X. DATA ASC
							) IS NOT NULL
					) chapa_solicitante,
					NULL codhorario,
					NULL horario,
					NULL codindice,
					NULL justificativa_escala,
					E.CPF,
					(
					SELECT
						TOP 1 
						HB.DESCRICAO
					FROM
						".DBRM_BANCO."..PFHSTSIT HA
						INNER JOIN ".DBRM_BANCO."..PCODSITUACAO HB ON HB.CODCLIENTE = HA.NOVASITUACAO
					WHERE
							HA.CODCOLIGADA = A.COLIGADA
						AND HA.CHAPA = A.CHAPA COLLATE Latin1_General_CI_AS
						AND (
							HA.DATAMUDANCA <= A.dtponto
						)
					ORDER BY
						DATAMUDANCA DESC
					) CODSITUACAO,
					B.NOME,
					a.dtcadastro data_solicitacao,
					(
						SELECT max(CAST(BB.descricao AS VARCHAR)) FROM zcrmportal_ponto_justificativa_func AA  (NOLOCK) 
						INNER JOIN zcrmportal_ponto_motivos BB (NOLOCK) ON AA.justificativa = BB.id AND AA.coligada = BB.codcoligada WHERE AA.coligada = A.coligada AND AA.dtponto = A.dtponto AND AA.chapa = A.chapa
					) justificativa_excecao
				FROM
					zcrmportal_ponto_horas A
					INNER JOIN ".DBRM_BANCO."..PFUNC B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
					LEFT JOIN zcrmportal_usuario C ON C.id = A.usucad
					INNER JOIN ".DBRM_BANCO."..PPESSOA E ON E.CODIGO = B.CODPESSOA
				WHERE
					" . $FT_STATUS . "
					AND A.coligada = '{$_SESSION['func_coligada']}'
					AND A.motivo_reprova IS NULL
					AND A.usu_delete IS NULL
					{$in_secao}
					" . $FT_ABONO . "
					" . $FT_MOVIMENTO . "
					" . $codfilial . "
					{$periodo}
					{$filtro_chapa}
					".((($dados['filtro_tipo'] ?? 'ponto') == 'ponto') ? '' : $filtro_tipo)."
					{$filtro_filial}
					{$filtro_1}
					{$filtro_legenda}
					
				UNION ALL
				
				SELECT
					a.id,
					a.datamudanca dtponto,
					a.datamudanca_folga dtfolga,
					a.codindice_folga codindice_folga,
					CASE WHEN a.tipo = 1 THEN 21 ELSE 22 END movimento,
					a.chapa,
					NULL batida,
					NULL motivo,
					NULL natureza,
					NULL data_referencia,
					NULL abn_dtfim,
					NULL abn_horaini,
					NULL abn_horafim,
					NULL abn_codabono,
					NULL abn_totalhoras,
					CASE WHEN a.usuupload IS NULL THEN 0 ELSE 1 END possui_anexo,
					a.coligada,
					a.situacao status,
					NULL justificativa_abono_tipo,
					NULL atitude_dt,
					NULL atitude_ini,
					NULL atitude_fim,
					NULL atitude_tipo,
					NULL atitude_justificativa,
					c.nome solicitante,
					(
						SELECT
							MAX(BB.CHAPA)
						FROM
							".DBRM_BANCO."..PPESSOA AA
							INNER JOIN ".DBRM_BANCO."..PFUNC BB ON BB.CODPESSOA = AA.CODIGO
						WHERE
								AA.CPF = C.login COLLATE Latin1_General_CI_AS
							AND BB.CODCOLIGADA = A.coligada
							AND (
								SELECT TOP 1 REGISTRO FROM (
									SELECT
										CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
										CASE
											WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
											ELSE '2900-12-31'
										END DATA
									FROM
										".DBRM_BANCO."..PFUNC
									WHERE
										CODCOLIGADA = BB.CODCOLIGADA AND CHAPA = BB.CHAPA
										AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
										AND DATAADMISSAO <= a.datamudanca
								)X WHERE X.DATA >= a.datamudanca
								ORDER BY X.DATA ASC
							) IS NOT NULL
					) chapa_solicitante,
					a.codhorario,
					d.DESCRICAO horario,
					a.codindice,
					CONCAT(
                    	CASE WHEN justificativa_11_horas IS NOT NULL THEN CONCAT('Interjornada 11h: ',a.justificativa_11_horas, ', ') ELSE '' END,
                    	CASE WHEN justificativa_6_dias IS NOT NULL THEN CONCAT('6 dias consecutivos: ', a.justificativa_11_horas, ', ') ELSE '' END,
                    	CASE WHEN justificativa_6_meses IS NOT NULL THEN CONCAT('Troca inf. 6 meses: ', a.justificativa_6_meses, ', ') ELSE '' END,
                        CASE WHEN justificativa_3_dias IS NOT NULL THEN CONCAT('Troca inf. 72 horas: ', a.justificativa_3_dias, ', ') ELSE '' END,
                    	CASE WHEN justificativa_periodo IS NOT NULL THEN CONCAT('Fora período: ', a.justificativa_periodo, ', ') ELSE '' END
                    ) justificativa_escala,
					E.CPF,
					(
					SELECT
						TOP 1 
						HB.DESCRICAO
					FROM
						".DBRM_BANCO."..PFHSTSIT HA
						INNER JOIN ".DBRM_BANCO."..PCODSITUACAO HB ON HB.CODCLIENTE = HA.NOVASITUACAO
					WHERE
							HA.CODCOLIGADA = A.COLIGADA
						AND HA.CHAPA = A.CHAPA COLLATE Latin1_General_CI_AS
						AND (
							HA.DATAMUDANCA <= A.datamudanca
						)
					ORDER BY
						DATAMUDANCA DESC
					) CODSITUACAO,
					B.NOME,
					a.dtcad data_solicitacao,
					NULL justificativa_excecao
				FROM
					zcrmportal_escala a
					INNER JOIN ".DBRM_BANCO."..PFUNC B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
					LEFT JOIN zcrmportal_usuario C ON C.id = A.usucad
					LEFT JOIN ".DBRM_BANCO."..AHORARIO D ON D.CODIGO = a.codhorario COLLATE Latin1_General_CI_AS AND D.CODCOLIGADA = a.coligada
					INNER JOIN ".DBRM_BANCO."..PPESSOA E ON E.CODIGO = B.CODPESSOA
				WHERE
					a.coligada = '{$_SESSION['func_coligada']}'
					and a.situacao in (10,2)
					{$in_secao}
					" . $codfilial . "
					".(str_replace('A.dtponto','a.datamudanca',$periodo))."
					".(str_replace(['a.movimento','ponto'],['a.tipo','0'],$filtro_tipo))."
					{$filtro_chapa}
					{$filtro_filial}
					{$filtro_2}
					{$filtro_legenda2}
			)X
			ORDER BY
				X.chapa,
				X.dtponto
		";
		$result = $this->dbportal->query($query);
        if($result->getNumRows() > 0){
			$response = array();
			$result = $result->getResultArray();
			foreach($result as $key => $Dados){
				$response[$key] = $Dados;

				$response[$key]['batidas_dia'] = self::listaBatidasDoDia($Dados['coligada'], $Dados['chapa'], dtEn($Dados['dtponto'], true));

				$nome_funcionario   = '-demitido-';
				$cpf_functionario   = '';
				$nome_gestor        = '';
				$chapa_gestor       = '';
				$codsituacao        = '';
				$response[$key]['nome']           = $Dados['NOME'];
				$response[$key]['cpf']            = $Dados['CPF'];
				$response[$key]['nome_gestor']    = $Dados['GESTOR_NOME'];
				$response[$key]['chapa_gestor']   = $Dados['GESTOR_CHAPA'];
				$response[$key]['codsituacao']    = $Dados['CODSITUACAO'];
				$response[$key]['nomechapa']      = $Dados['chapa'].' - '.$Dados['NOME'].' <span style="font-size: 12px; margin-right: 5px;">[Situação: '.$Dados['CODSITUACAO'].']</span> <small><b>=> [GESTOR: '.$Dados['GESTOR_CHAPA'].' - '.$Dados['GESTOR_NOME'].']</b></small>';
				

				unset($result[$key], $key, $Dados);
			}
			unset($dados_func);
			return $response;
		}

		return false;
	}


	

    //##################################################################################
	// Lista Seção
	//##################################################################################

	public function listaSecaoUsuario($codsecao = null, $dados = false)
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
		$chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

		if($this->log_id  != 1){
			$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") AND A.CHAPA != '{$chapaFunc}' ";
		}
		else{
			$qr_secao = "";
		}
		
		// lista seções
		$filtro_secao = ($codsecao != null) ? " AND A.CODSECAO = '{$codsecao}' " : "";

		if($dados){
			if(($dados['perfilRH'] ?? false) || ($dados['rh'] ?? false)) $qr_secao = "";
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
				AND A.CODSITUACAO NOT IN ('D')
				{$qr_secao}
                {$filtro_secao}

			GROUP BY
				A.CODSECAO, 
				B.DESCRICAO
				
			ORDER BY
				B.DESCRICAO
		";

		// exit('<pre>'.$query);
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

	}

    //##################################################################################
	// LISTA SEÇÃO DO USUÁRIO
	//##################################################################################
	public function listaSecaoUsu($idusu = false)
	{

		$query = " SELECT * FROM zcrmportal_usuario_secao WHERE id_usu = '" . $idusu . "' AND LEN(secao) = 13  ORDER BY secao";
		// echo $query;exit();
		$res = $this->dbportal->query($query);
		return $res = ($res) ? $res->getResultArray() : array();
	}

	public function ListaDadosAnexos($id_anexo){
		$query = "SELECT abono_atestado arquivo, anexo_batida FROM zcrmportal_ponto_horas WHERE id = '". $id_anexo . "'";
		
		$result = $this->dbportal->query($query);
		return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;		

	}

	

	public function RecalculoPonto($array_recalculo){
		
		$wsTotvs = model('WsrmModel');
		
		if($array_recalculo){
			if(count($array_recalculo) > 0){
				foreach($array_recalculo as $key => $dadosRecalculo){
					$recalculo_chapa = $dadosRecalculo['chapa'];
					$recalculo_data  = $dadosRecalculo['data'];

					$wsTotvs->ws_recalculo_ponto($recalculo_chapa, $recalculo_data);

				}
			}
		}

		return true;

	}

	public function ListarFuncionariosSecao($codsecao, $dados = false, $aprovacao = true)
	{
		if(!is_array($codsecao)){
			if($codsecao == 'all') $codsecao = null;
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
				AND
				EXISTS(
					SELECT 
						MAX(p.id) 
					FROM 
						".DBPORTAL_BANCO."..zcrmportal_ponto_horas p
					WHERE 
							p.status IN ('1','2','3', 'A')
						AND p.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
						AND p.coligada = A.CODCOLIGADA 
				)

			GROUP BY
				A.CHAPA,
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

	public function listaChapaLider()
	{

		$chapa_lider = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

		$query = "
			SELECT 
				DISTINCT
				b.chapa 
			FROM 
				zcrmportal_hierarquia_lider_ponto a
				JOIN zcrmportal_hierarquia_lider_func_ponto b ON b.id_lider = a.id
			WHERE
					a.inativo IS NULL
				AND b.inativo IS NULL 
				AND a.coligada = 1
				AND a.id_hierarquia IN (
				SELECT id_hierarquia FROM zcrmportal_hierarquia_lider_ponto WHERE chapa = '{$chapa_lider}' AND inativo IS NULL AND nivel = 1 AND coligada = '{$this->coligada}'
			)
		";
		$result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

	}

	public function listaFuncionarioSecao($codsecao = false, $dados = false)
	{
		// $mHierarquia = model('HierarquiaModel');
        // $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		// $isLider = $mHierarquia->isLider();

		$rh = ($dados) ? $dados['perfilRH'] : false;

        $in_secao = " AND 1 = 2 ";
        /*if($Secoes && !$rh){
            $in_secao = "";
            if($isLider){
                // lider
				$Secoes = self::listaChapaLider();
                foreach($Secoes as $key =>$Chapa){
                    $in_secao .= "'{$Chapa['chapa']}',";
                }
                $in_secao = " AND A.CHAPA IN (".rtrim($in_secao, ',').") ";
            }else{
                // gestor
                foreach($Secoes as $key =>$CodSecao){
                    $in_secao .= "'{$CodSecao['codsecao']}',";
                }
                $in_secao = " AND A.CODSECAO IN (".rtrim($in_secao, ',').") ";
            }
        }*/




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
		$chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
		if($this->log_id  != 1){
			$in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") AND A.CHAPA != '{$chapaFunc}' ";
		}
		else{
			$in_secao = "";
		}





		if($rh) $in_secao = "";


        
        $query = " SELECT A.CHAPA, A.NOME, A.CODSECAO FROM PFUNC A WHERE A.CODSITUACAO <> 'D' {$in_secao} ORDER BY A.NOME ASC";
        
        $result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0)
                ? $result->getResultArray()
                : false;
	}

	public function isGestorOrLiderAprovador($chapaColaborador = false)
    {

      $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
      if($chapa == null) return false;

      $query = " SELECT * FROM zcrmportal_hierarquia_chapa WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' ";
      $result = $this->dbportal->query($query);
      if($result){
        if($result->getNumRows() > 0) return true;
      }

      $query = " SELECT * FROM zcrmportal_hierarquia_chapa_sub WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' AND inativo IS NULL ";
      $result = $this->dbportal->query($query);
      if($result){
        if($result->getNumRows() > 0) return true;
      }

	  $query = " SELECT * FROM zcrmportal_hierarquia_gestor_substituto WHERE chapa_substituto = '{$chapa}' AND coligada = '{$this->coligada}' AND inativo = 0";
      $result = $this->dbportal->query($query);
      if($result){
        if($result->getNumRows() > 0) return true;
      }


      $query = "
        SELECT * FROM zcrmportal_hierarquia_lider_func_ponto WHERE chapa = '{$chapaColaborador}' AND coligada = '{$this->coligada}' AND inativo IS NULL AND id_lider IN (
            SELECT id FROM zcrmportal_hierarquia_lider_ponto WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' AND inativo IS NULL AND nivel = 1
        )
       ";
      $result = $this->dbportal->query($query);
      if($result){
        if($result->getNumRows() > 0) return true;
      }

      return false;

    }

	public function listaBatidasDoDia($coligada, $chapa, $data)
	{
		$result       = $this->dbrm->query("SELECT BATIDA, NATUREZA FROM ABATFUN WHERE CHAPA = '{$chapa}' AND COALESCE(DATAREFERENCIA, DATA) = '{$data}' AND CODCOLIGADA = '{$coligada}' ORDER BY DATA ASC, BATIDA ASC ");
		$batidasDia   = '';

		if($result){
			$batidas = $result->getResult();
			foreach($batidas as $batida){
				switch($batida->NATUREZA){
					case 0: $batidasDia .= 'Ent: '.m2h($batida->BATIDA).' | '; break;
					case 1: $batidasDia .= 'Sai: '.m2h($batida->BATIDA).' | '; break;
				}
			}
		}

		return rtrim($batidasDia, ' | ');

	}

}