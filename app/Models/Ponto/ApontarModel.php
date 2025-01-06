<?php

namespace App\Models\Ponto;

use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class ApontarModel extends Model
{

    protected $dbportal;
    protected $dbrm;

    public function __construct()
    {
        $this->dbportal   = db_connect('dbportal');
        $this->dbrm       = db_connect('dbrm');
    }

    // -------------------------------------------------------
    // 
    // -------------------------------------------------------
    public function ListarEspelhoConfiguracao()
    {

        $query = " SELECT * FROM zcrmportal_espelho_config WHERE coligada = '" . session()->get('func_coligada') . "' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0)
            ? $result->getResultArray()
            : false;
    }

    // -------------------------------------------------------
    // Lista periodo
    // -------------------------------------------------------
    public function ListarPeriodoRM()
    {

        $configuracao = $this->ListarEspelhoConfiguracao();
        if (!$configuracao) return false;

        $query = "
            SELECT 
                * 
            FROM 
                APERIODO 
            WHERE 
                    CODCOLIGADA = '" . session()->get('func_coligada') . "'
                AND INICIOMENSAL >= '{$configuracao[0]['dtinicio']}' AND FIMMENSAL  <= '{$configuracao[0]['dtfim']}'

            ORDER BY 
                ANOCOMP DESC, 
                MESCOMP DESC
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0)
            ? $result->getResultArray()
            : false;
    }

    // #############################################################################
    // LISTA BATIDAS
    // #############################################################################
    public function listaBatidas($periodo)
	{
		$codCol = session()->get("func_coligada");
        $idfunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dtini = dtEn(substr($periodo, 0, 10));
        $dtfim = dtEn(substr($periodo, 10, 10));

		$query = "SELECT * FROM ABATFUN WHERE ABATFUN.CODCOLIGADA = " . $codCol . " AND ABATFUN.CHAPA = " . $idfunc . " ";
		if ($dtini != '') {
			
			if (DBRM_TIPO == 'sqlserver')
				$query .= " AND ABATFUN.DATA >= CONVERT(DATETIME, '" . $dtini . "', 102)";
			else if (DBRM_TIPO == 'oracle')
				$query .= " AND ABATFUN.DATA >= TO_DATE('" . $dtini . "', 'yyyy-mm-dd') ";
			else
				$query .= " AND ABATFUN.DATA >= TO_DATE('" . $dtini . "', 'yyyy-mm-dd') ";
		}
		if ($dtfim != '') {

            if (DBRM_TIPO == 'sqlserver')
				$query .= " AND ABATFUN.DATA <= CONVERT(DATETIME, '" . $dtfim . "', 102)";
			else if (DBRM_TIPO == 'oracle')
				$query .= " AND ABATFUN.DATA <= TO_DATE('" . $dtfim . "', 'yyyy-mm-dd') ";
			else
				$query .= " AND ABATFUN.DATA <= TO_DATE('" . $dtfim . "', 'yyyy-mm-dd') ";
		}
		$query .= " ORDER BY DATA, BATIDA";

        // echo $query;
        // exit();
		$res = $this->dbrm->query($query);
        return ($res->getNumRows() > 0)
            ? $res->getResultArray()
            : false;

		// echo $query;
		// exit();
		return $res;
	}

    // #############################################################################
    // LISTA MOVIMENTOS
    // #############################################################################
    public function listaMovimentos($periodo)
    {
        $codCol = session()->get("func_coligada");
        $idfunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dtini = dtEn(substr($periodo, 0, 10));
        $dtfim = dtEn(substr($periodo, 10, 10));
        
        $query = "SELECT PEVENTO.CODIGO, PEVENTO.DESCRICAO, AMOVFUN.NUMHORAS FROM AMOVFUN, PEVENTO";
        $query .= " WHERE AMOVFUN.CODCOLIGADA = {$codCol} AND PEVENTO.CODCOLIGADA = {$codCol} AND AMOVFUN.CHAPA = '{$idfunc}' AND AMOVFUN.INICIOPER = '{$dtini}' AND FIMPER = '{$dtfim}' ";
        $query .= " AND AMOVFUN.CODCOLIGADA = PEVENTO.CODCOLIGADA AND AMOVFUN.CODEVE = PEVENTO.CODIGO AND PEVENTO.CODIGO NOT IN ('0') AND AMOVFUN.NUMHORAS > 0 ";

        $res = $this->dbrm->query($query);
        return ($res->getNumRows() > 0)
            ? $res->getResultArray()
            : false;

        $qtd_ret = is_array($res) ? count($res) : 0;

        $retorno = $res;

        if ($qtd_ret > 0) {
            if (DBRM_TIPO == 'sqlserver')
                $sqldata = " AND PFHSTHOR.DTMUDANCA < CONVERT(DATETIME, '{dtini}', 102)";
            else if (DBRM_TIPO == 'oracle')
                $sqldata = " AND PFHSTHOR.DTMUDANCA < TO_DATE('{dtini}','yyyy-mm-dd')";

            $query = "SELECT PFHSTHOR.CODHORARIO, AHORARIO.DESCRICAO FROM PFHSTHOR, AHORARIO  WHERE PFHSTHOR.CODCOLIGADA = AHORARIO.CODCOLIGADA AND PFHSTHOR.CODCOLIGADA = {$codCol} AND PFHSTHOR.CODHORARIO = AHORARIO.CODIGO
                        AND PFHSTHOR.CHAPA = '{$idfunc}' {$sqldata}
                        ORDER BY PFHSTHOR.DTMUDANCA DESC";

            $res = $this->dbrm->query($query);
            return ($res->getNumRows() > 0)
            ? $res->getResultArray()
            : false;

            $qtd = is_array($res) ? count($res) : 0;
            $indice = $qtd_ret;
            for ($i = 0; $i < $qtd; $i++) :
                $retorno[$indice]['CODIGO'] = $res[$i]["CODHORARIO"];
                $retorno[$indice]['DESCRICAO'] = $res[$i]["DESCRICAO"];
                $retorno[$indice]['NUMHORAS'] = '';
                $retorno[$indice]['JORNADA'] = "X";

                break;
            endfor;
        }
        // echo $res;
        // exit();
        return $retorno;
    }
    // #############################################################################
    // VERIFICA SE O DIA É FERIADO
    // #############################################################################
    public function diaFeriado($data)
    {
        $dtapt = dtEn(substr($data, 0, 10));
        $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $minhaquery = "
				SELECT D.CHAPA, A.DIAFERIADO
					FROM GFERIADO A, PFUNC D, PSECAO E
					 WHERE 
						   D.CODCOLIGADA=E.CODCOLIGADA AND
						   D.CODCOLIGADA=E.CODCOLIGADA AND
						   D.CODSECAO=E.CODIGO AND
						   A.CODCALENDARIO=E.CODCALENDARIO AND
						   D.CODSITUACAO NOT IN ('D') AND
						   D.CODCOLIGADA = 1 AND
						   D.CHAPA = '" . $chapa . "' AND
						   A.DIAFERIADO = '" . $dtapt . "'
				
		";

        // echo $minhaquery;
        $result = $this->dbrm->query($minhaquery);
        return ($result->getNumRows() > 0)
            ? $result->getResultArray()
            : false;
    }

    // #############################################################################
    // VERIFICA SE A DATA DO APONTAMENTO O FUNCIONÁRIO ESTAVA EM FÉRIAS
    // #############################################################################
    public function funcEmFerias($data)
    {
        $coligada = session()->get("func_coligada");
        $dtapt = dtEn(substr($data, 0, 10));
        $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

        $minhaquery = " SELECT (DATAINICIO), (DATAFIM) FROM PFUFERIASPER WHERE CHAPA = '" . $chapa . "' AND CODCOLIGADA = '" . $coligada . "' AND
				'" . $dtapt . "' BETWEEN DATAINICIO  AND DATAFIM ";

        // echo $minhaquery;
        $result = $this->dbrm->query($minhaquery);
        return ($result->getNumRows() > 0)
            ? $result->getResultArray()
            : false;
    }

    // #############################################################################
    // VERIFICA SE A DATA DO APONTAMENTO O FUNCIONÁRIO ESTAVA AFASTADO
    // #############################################################################
    public function funcAfastado($data)
    {
        $coligada = session()->get("func_coligada");
        $dtapt = dtEn(substr($data, 0, 10));
        $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

        $minhaquery = " SELECT (DTINICIO), (DTFINAL) FROM PFHSTAFT WHERE CHAPA = '" . $chapa . "' AND CODCOLIGADA = '" . $coligada . "' AND	'" . $dtapt . "' BETWEEN DTINICIO  AND DTFINAL";
        // echo $minhaquery;
        $result = $this->dbrm->query($minhaquery);
        return ($result->getNumRows() > 0)
            ? $result->getResultArray()
            : false;
    }

    public function verificaAlteracao($idbatida)
    {
        $query = "SELECT JUSTIFICATIVA FROM AAFDT WHERE justificativa LIKE 'Ajuste Ponto Portal' AND CODCOLIGADA = 3 AND ID = '" . $idbatida . "'";
        // echo $query;
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0)
            ? $result->getResultArray()
            : false;
    }


    // #############################################################################
    // LISTA AS HORAS DO PONTO
    // #############################################################################
    public function listaHorasPonto($chapa, $dtapt = false, $status = "A", $idbatida = false)
    {
        $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

        if (!$chapa && !$dtapt) {
            return false;
        }

        $filtro = false;

        if ($chapa)
            $filtro = " AND chapa = '" . $chapa . "' ";

        if ($dtapt)
            $filtro = $filtro . " AND dtponto = '" . $dtapt . "' ";

        if ($idbatida)
            $filtro = $filtro . " AND id = '" . $idbatida . "' ";


        $minhaquery = " SELECT * FROM zcrmportal_ponto_horas WHERE coligada = '" . $_SESSION['func_coligada'] . "' " . $filtro . " AND status  = '" . $status . "'";
        // echo $minhaquery;
        // exit();
        $result = $this->dbportal->query($minhaquery);
        return ($result->getNumRows() > 0)
            ? $result->getResultArray()
            : false;
    }

    public function listaPeriodosCMB()
    {
        $codCol = session()->get("func_coligada");
        $res = $this->ListarEspelhoConfiguracao();

        //$this->__setConexaoBancoIntegra();

        $dtini = isset($res[0]['dtinicio']) ? $res[0]['dtinicio'] : '';
        $dtfim = isset($res[0]['dtfim']) ? $res[0]['dtfim'] : '';

        $query = '';
        if (($dtini != '') && ($dtfim != '')) {
            $pini = date("Y-m-d", strtotime($dtini));
            // var_dump($pini);
            $pfim = date("Y-m-d", strtotime($dtfim));
            // var_dump($pfim);

            $query .= "SELECT * FROM APERIODO WHERE CODCOLIGADA = {$codCol} AND ";
            $query .= " INICIOMENSAL >= CONVERT(DATETIME, '{$pini}', 102) AND FIMMENSAL <= CONVERT(DATETIME, '{$pfim}', 102) ";
            $query .= " ORDER BY ANOCOMP DESC, MESCOMP DESC";
            // echo $query;
            // exit();

            $res = $this->dbrm->query($query);
            return ($res->getNumRows() > 0)
                ? $res->getResultArray()
                : false;
        } else {
            $res = array();
        }
        // echo $query;
        // exit();
        return $res;
    }

    public function listaDiasBatidas($periodo)
	{
        $codCol = session()->get("func_coligada");
        $idfunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $dtini = dtEn(substr($periodo, 0, 10));
        $dtfim = dtEn(substr($periodo, 10, 10));

		$query = "SELECT * FROM AAFHTFUN WHERE CODCOLIGADA = {$codCol} AND CHAPA = {$idfunc}";

		if ($dtini) {
			if ($dtfim) {
				$query .= " AND AAFHTFUN.DATA >= '" . $dtini . "' ";
				$query .= " AND AAFHTFUN.DATA <= '" . $dtfim . "' ";
			} else {
				$query .= " AND AAFHTFUN.DATA = '" . $dtini . "' ";
			}
		}

		$query .= " ORDER BY DATA";
		// echo $query;
		// exit();
		$res = $this->dbrm->query($query);
            return ($res->getNumRows() > 0)
                ? $res->getResultArray()
                : false;
	}

    // #############################################################################
    // ATUALIZA O PONTO
    // #############################################################################
    public function iudPonto($objDados, $acao)
    {
        if ($acao == 'D') {
            // pega a linha para fazer o UPDATE
            switch (substr($objDados['input'], 0, 2)) {
                case 'e1': 
                    $campo   = " ent1 ";
                    $campoid = " ident1 ";
                    $nat     = " natent1 ";
                    $just    = " justent1 ";
                    $dtref   = " dtrefent1 ";
                    break;
                case 's1': 
                    $campo   = " sai1 ";
                    $campoid = " idsai1 ";
                    $nat     = " natsai1 ";
                    $just    = " justsai1 ";
                    $dtref   = " dtrefsai1 ";
                    break;
                case 'e2': 
                    $campo   = " ent2 ";
                    $campoid = " ident2 ";
                    $nat     = " natent2 ";
                    $just    = " justent2 ";
                    $dtref   = " dtrefent2 ";
                    break;
                case 's2': 
                    $campo   = " sai2 ";
                    $campoid = " idsai2 ";
                    $nat     = " natsai2 ";
                    $just    = " justsai2 ";
                    $dtref   = " dtrefsai2 ";
                    break;
                case 'e3': 
                    $campo   = " ent3 ";
                    $campoid = " ident3 ";
                    $nat     = " natent3 ";
                    $just    = " justent3 ";
                    $dtref   = " dtrefent3 ";
                    break;
                case 's3': 
                    $campo   = " sai3 ";
                    $campoid = " idsai3 ";
                    $nat     = " natsai3 ";
                    $just    = " justsai3 ";
                    $dtref   = " dtrefsai3 ";
                    break;
                case 'e4': 
                    $campo   = " ent4 ";
                    $campoid = " ident4 ";
                    $nat     = " natent4 ";
                    $just    = " justent4 ";
                    $dtref   = " dtrefent4 ";
                    break;
                case 's4': 
                    $campo   = " sai4 ";
                    $campoid = " idsai4 ";
                    $nat     = " natsai4 ";
                    $just    = " justsai4 ";
                    $dtref   = " dtrefsai4 ";
                    break;
                case 'e5': 
                    $campo   = " ent5 ";
                    $campoid = " ident5 ";
                    $nat     = " natent5 ";
                    $just    = " justent5 ";
                    $dtref   = " dtrefent5 ";
                    break;
                case 's5': 
                    $campo   = " sai5 ";
                    $campoid = " idsai5 ";
                    $nat     = " natsai5 ";
                    $just    = " justsai5 ";
                    $dtref   = " dtrefsai5 ";
                    break;
                default: 
                    return "Erro: Valor de entrada não reconhecido";
            }

            $query = " DELETE FROM zcrmportal_ponto_horas WHERE chapa = '" . $objDados['chapa'] . "' AND dtponto = '" . $objDados['dtponto'] . "' AND coligada = '" . $_SESSION['func_coligada'] . "' AND status <> 'S' ";
            // echo $query;
            // exit();
            return $this->dbportal->query($query);

            
        }

        // pega a linha para fazer o UPDATE        
        switch (substr($objDados['input'], 0, 2)) {
            case 'e1': 
                $campo   = " ent1 ";
                $campoid = " ident1 ";
                $nat     = " natent1 ";
                $just    = " justent1 ";
                $dtref   = " dtrefent1 ";
                break;
            case 's1': 
                $campo   = " sai1 ";
                $campoid = " idsai1 ";
                $nat     = " natsai1 ";
                $just    = " justsai1 ";
                $dtref   = " dtrefsai1 ";
                break;
            case 'e2': 
                $campo   = " ent2 ";
                $campoid = " ident2 ";
                $nat     = " natent2 ";
                $just    = " justent2 ";
                $dtref   = " dtrefent2 ";
                break;
            case 's2': 
                $campo   = " sai2 ";
                $campoid = " idsai2 ";
                $nat     = " natsai2 ";
                $just    = " justsai2 ";
                $dtref   = " dtrefsai2 ";
                break;
            case 'e3': 
                $campo   = " ent3 ";
                $campoid = " ident3 ";
                $nat     = " natent3 ";
                $just    = " justent3 ";
                $dtref   = " dtrefent3 ";
                break;
            case 's3': 
                $campo   = " sai3 ";
                $campoid = " idsai3 ";
                $nat     = " natsai3 ";
                $just    = " justsai3 ";
                $dtref   = " dtrefsai3 ";
                break;
            case 'e4': 
                $campo   = " ent4 ";
                $campoid = " ident4 ";
                $nat     = " natent4 ";
                $just    = " justent4 ";
                $dtref   = " dtrefent4 ";
                break;
            case 's4': 
                $campo   = " sai4 ";
                $campoid = " idsai4 ";
                $nat     = " natsai4 ";
                $just    = " justsai4 ";
                $dtref   = " dtrefsai4 ";
                break;
            case 'e5': 
                $campo   = " ent5 ";
                $campoid = " ident5 ";
                $nat     = " natent5 ";
                $just    = " justent5 ";
                $dtref   = " dtrefent5 ";
                break;
            case 's5': 
                $campo   = " sai5 ";
                $campoid = " idsai5 ";
                $nat     = " natsai5 ";
                $just    = " justsai5 ";
                $dtref   = " dtrefsai5 ";
                break;
            default: 
                return "Erro: Valor de entrada não reconhecido";
        }
        
        $minhaquery = " SELECT * FROM zcrmportal_ponto_horas WHERE dtponto = '" . $objDados['dtponto'] . "' AND chapa = '" . $objDados['chapa'] . "' AND " . $campo . " <> '' AND coligada = '" . $_SESSION['func_coligada'] . "' AND status = '1' ";
        echo $minhaquery;
        // exit();
        $qry = $this->dbportal->query($minhaquery);
        $result = ($qry) ? $qry->getResultArray() : false;        

        // se já possui apontamento atualiza / senão faz o insert
        if ($result) {

            // pega a linha para fazer o UPDATE
            switch (substr($objDados['input'], 0, 2)) {
                case 'e1': 
                    $campo   = " ent1 ";
                    $campoid = " ident1 ";
                    $nat     = " natent1 ";
                    $just    = " justent1 ";
                    $dtref   = " dtrefent1 ";
                    break;
                case 's1': 
                    $campo   = " sai1 ";
                    $campoid = " idsai1 ";
                    $nat     = " natsai1 ";
                    $just    = " justsai1 ";
                    $dtref   = " dtrefsai1 ";
                    break;
                case 'e2': 
                    $campo   = " ent2 ";
                    $campoid = " ident2 ";
                    $nat     = " natent2 ";
                    $just    = " justent2 ";
                    $dtref   = " dtrefent2 ";
                    break;
                case 's2': 
                    $campo   = " sai2 ";
                    $campoid = " idsai2 ";
                    $nat     = " natsai2 ";
                    $just    = " justsai2 ";
                    $dtref   = " dtrefsai2 ";
                    break;
                case 'e3': 
                    $campo   = " ent3 ";
                    $campoid = " ident3 ";
                    $nat     = " natent3 ";
                    $just    = " justent3 ";
                    $dtref   = " dtrefent3 ";
                    break;
                case 's3': 
                    $campo   = " sai3 ";
                    $campoid = " idsai3 ";
                    $nat     = " natsai3 ";
                    $just    = " justsai3 ";
                    $dtref   = " dtrefsai3 ";
                    break;
                case 'e4': 
                    $campo   = " ent4 ";
                    $campoid = " ident4 ";
                    $nat     = " natent4 ";
                    $just    = " justent4 ";
                    $dtref   = " dtrefent4 ";
                    break;
                case 's4': 
                    $campo   = " sai4 ";
                    $campoid = " idsai4 ";
                    $nat     = " natsai4 ";
                    $just    = " justsai4 ";
                    $dtref   = " dtrefsai4 ";
                    break;
                case 'e5': 
                    $campo   = " ent5 ";
                    $campoid = " ident5 ";
                    $nat     = " natent5 ";
                    $just    = " justent5 ";
                    $dtref   = " dtrefent5 ";
                    break;
                case 's5': 
                    $campo   = " sai5 ";
                    $campoid = " idsai5 ";
                    $nat     = " natsai5 ";
                    $just    = " justsai5 ";
                    $dtref   = " dtrefsai5 ";
                    break;
            }

            // ID BATIDA
            $idBATIDA = 'NULL';
            if (strlen(trim($objDados['idbatida'])) > 0) $idBATIDA = $objDados['idbatida'];
		
            if (strlen(trim($objDados['hora_nova'])) <= 0) {
                $HORA_HOVA = 'NULL';
            } else {
                $HORA_HOVA = "'" . $objDados['hora_nova'] . "'";
            }

            $query_update = " UPDATE zcrmportal_ponto_horas SET " . $campo . " = " . $HORA_HOVA . ", " . $campoid . " = '" . $idBATIDA . "', usualt = '" . $_SESSION['log_id'] . "', dtalt = '" . date('Y-m-d H:i:s') . "', ipalteracao = '" . $_SERVER['REMOTE_ADDR'] . "' WHERE chapa = '" . $objDados['chapa'] . "' AND dtponto = '" . $objDados['dtponto'] . "' AND coligada = '" . $_SESSION['func_coligada'] . "' AND status = '1' ";

            echo $query_update;
            // exit();
            $this->dbportal->query($query_update);

            return true;
        } else {
            // pega a linha para fazer o UPDATE
            switch (substr($objDados['input'], 0, 2)) {
                case 'e1': 
                    $campo   = " ent1 ";
                    $campoid = " ident1 ";
                    $nat     = " natent1 ";
                    $just    = " justent1 ";
                    $dtref   = " dtrefent1 ";
                    break;
                case 's1': 
                    $campo   = " sai1 ";
                    $campoid = " idsai1 ";
                    $nat     = " natsai1 ";
                    $just    = " justsai1 ";
                    $dtref   = " dtrefsai1 ";
                    break;
                case 'e2': 
                    $campo   = " ent2 ";
                    $campoid = " ident2 ";
                    $nat     = " natent2 ";
                    $just    = " justent2 ";
                    $dtref   = " dtrefent2 ";
                    break;
                case 's2': 
                    $campo   = " sai2 ";
                    $campoid = " idsai2 ";
                    $nat     = " natsai2 ";
                    $just    = " justsai2 ";
                    $dtref   = " dtrefsai2 ";
                    break;
                case 'e3': 
                    $campo   = " ent3 ";
                    $campoid = " ident3 ";
                    $nat     = " natent3 ";
                    $just    = " justent3 ";
                    $dtref   = " dtrefent3 ";
                    break;
                case 's3': 
                    $campo   = " sai3 ";
                    $campoid = " idsai3 ";
                    $nat     = " natsai3 ";
                    $just    = " justsai3 ";
                    $dtref   = " dtrefsai3 ";
                    break;
                case 'e4': 
                    $campo   = " ent4 ";
                    $campoid = " ident4 ";
                    $nat     = " natent4 ";
                    $just    = " justent4 ";
                    $dtref   = " dtrefent4 ";
                    break;
                case 's4': 
                    $campo   = " sai4 ";
                    $campoid = " idsai4 ";
                    $nat     = " natsai4 ";
                    $just    = " justsai4 ";
                    $dtref   = " dtrefsai4 ";
                    break;
                case 'e5': 
                    $campo   = " ent5 ";
                    $campoid = " ident5 ";
                    $nat     = " natent5 ";
                    $just    = " justent5 ";
                    $dtref   = " dtrefent5 ";
                    break;
                case 's5': 
                    $campo   = " sai5 ";
                    $campoid = " idsai5 ";
                    $nat     = " natsai5 ";
                    $just    = " justsai5 ";
                    $dtref   = " dtrefsai5 ";
                    break;
            }

            // ID BATIDA
            $idBATIDA = 'NULL';
            if (strlen(trim($objDados['idbatida'])) > 0) $idBATIDA = $objDados['idbatida'];


            // executa a query	
            $query_insert = " INSERT INTO zcrmportal_ponto_horas (dtponto," . $campo . ",dtcadastro,chapa,coligada,status,usucad,ipcadastro," . $campoid . ", movimento) 
			VALUES ('" . $objDados['dtponto'] . "','" . $objDados['hora_nova'] . "','" . date('Y-m-d H:i:s') . "','" . $objDados['chapa'] . "','" . $_SESSION['func_coligada'] . "','1','" . $_SESSION['log_id'] . "' , '" . $_SERVER['REMOTE_ADDR'] . "', '" . $idBATIDA . "', '" . '1' . "')";

            echo $query_insert;
            // exit();
            $this->dbportal->query($query_insert);
        }
        return true;
    }
}
