<?php
namespace App\Models\Ponto;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class CriticaModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $coligada;
    private $log_id;
    private $now;
    
    public function __construct() {
        $this->dbportal   = db_connect('dbportal');
        $this->dbrm       = db_connect('dbrm');
        $this->coligada   = session()->get('func_coligada');
        $this->log_id     = session()->get('log_id');
        $this->now        = date('Y-m-d H:i:s');

        if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		    if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
    }

   //---------------------------------- critica
    public function listaBatidasCritica($dados, $codloja = false) {
   
    $s_par_corresp      = isset($dados['ck_semPar']) ? $dados['ck_semPar'] : false;
    $extra_executado    = isset($dados['ck_ExtraExecutado']) ? $dados['ck_ExtraExecutado'] : false;
    $vl_extra_executado = isset($dados['vl_extra_executado']) ? $dados['vl_extra_executado'] : false;
    $atrasos            = isset($dados['ck_Atrasos']) ? $dados['ck_Atrasos'] : false;
    $vl_atrasos         = isset($dados['vl_atrasos']) ? $dados['vl_atrasos'] : false;
    $faltas             = isset($dados['ck_Faltas']) ? $dados['ck_Faltas'] : false;
    $jor_acima_10       = isset($dados['ck_jorMaior10']) ? $dados['ck_jorMaior10'] : false;
    $jor_acima_12       = isset($dados['ck_jorMaior12']) ? $dados['ck_jorMaior12'] : false;
    $interjornada       = isset($dados['ck_interjornada']) ? $dados['ck_interjornada'] : false;
    $periodo            = isset($dados['periodo']) ? $dados['periodo'] : false;
    $dtini              = isset($dados['data_inicio']) ? $dados['data_inicio'] : false;
    $dtfim              = isset($dados['data_fim']) ? $dados['data_fim'] : false;
    $funcionario        = isset($dados['funcionario']) ? $dados['funcionario'] : false;
    $bancohoras         = isset($dados['ck_bancohoras']) ? $dados['ck_bancohoras'] : false;
    $secao              = isset($dados['secao']) ? $dados['secao'] : false;

    $FT_GLOBAL = false;

    $FT_BANCOHORA = "  ";
    if($bancohoras){
      $FT_BANCOHORA = " AND X.BANCO_HORA IS NOT NULL ";
    }

		if($s_par_corresp){
			$FT_GLOBAL .= " OR SEM_PAR_CORRESPONDENTE IS NOT NULL ";
		}

		if($extra_executado && $vl_extra_executado){
			$FT_GLOBAL .= " OR EXTRAEXECUTADO_CASE >= '".h2m($vl_extra_executado)."' ";
		}

		if($atrasos && $vl_atrasos){
			$FT_GLOBAL .= " OR
		(CASE WHEN ATRASO_JUST = 0 THEN 0
		  WHEN ATRASO_JUST > 0 AND ATRASO_JUST < ATRASO_CASE THEN ATRASO_JUST
		  WHEN ATRASO_JUST > 0 AND ATRASO_JUST > ATRASO_CASE THEN ATRASO_CASE ELSE ATRASO_CASE END )
			>= '".h2m($vl_atrasos)."' ";
		}

		if($faltas){
			$FT_GLOBAL .= " OR
      ISNULL(CASE WHEN FALTA_JUST > 0 THEN 1 ELSE FALTA_CASE END,0)
			> 0 ";
		}

		if($jor_acima_10){
			$FT_GLOBAL .= " OR JORNADA_MAIOR_10HORAS IS NOT NULL ";
		}
    
		if($jor_acima_12){
			$FT_GLOBAL .= " OR JORNADA_MAIOR_12HORAS IS NOT NULL ";
		}
    
		if($interjornada){
			$FT_GLOBAL .= " OR INTERJORNADA IS NOT NULL ";
		}

    if( $FT_GLOBAL ) {
  		$FT_GLOBAL = 'AND ( '.substr($FT_GLOBAL,3,9999999).' )';
    }

		$FT_CODLOJA = false;
		if($codloja){
			$FT_CODLOJA = " AND B.CODSECAO IN (".$codloja.") ";
		}

    $FT_FUNCIONARIO = false;
    if($funcionario){
      $FT_FUNCIONARIO = " AND A.CHAPA = '" . $funcionario . "'";
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
      $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

    if($dados['rh']) $in_secao = "";
    $qr_secao = $in_secao;

    if(!is_array($secao)){
      $filtro_secao = ($secao != '') ? " AND B.CODSECAO = '{$secao}' " : "";
    }

    if(is_array($secao)){
      $filtro_secao = "";
      $codsecao_in = "";
      foreach($secao as $codSecao){
        $codsecao_in .= "'{$codSecao}',";
      }

      $filtro_secao = " AND B.CODSECAO IN (".rtrim($codsecao_in,',').") ";
    }


    $query = "
    SELECT DISTINCT
    
    CHAPA, NOME, DATA, DATAREFERENCIA, DATAREFERENCIA2, IDAAFDT, BATIDA, NATUREZA,

		(CASE WHEN ATRASO_JUST = 0 THEN 0
		  WHEN ATRASO_JUST > 0 AND ATRASO_JUST < ATRASO_CASE THEN ATRASO_JUST
		  WHEN ATRASO_JUST > 0 AND ATRASO_JUST > ATRASO_CASE THEN ATRASO_CASE ELSE ATRASO_CASE END )
		ATRASO_CASE

		,

    ISNULL(FALTA_CASE,0) FALTA_CASE
		/*(CASE WHEN FALTA_CASE = 0 THEN 0
		  WHEN FALTA_JUST > 0 AND FALTA_JUST < FALTA_CASE THEN FALTA_JUST
		  WHEN FALTA_JUST > 0 AND FALTA_JUST > FALTA_CASE THEN FALTA_CASE ELSE FALTA_CASE END )
		FALTA_CASE*/

		, EXTRAEXECUTADO_CASE, SEM_PAR_CORRESPONDENTE, INTERJORNADA, JORNADA_MAIOR_10HORAS, JORNADA_MAIOR_12HORAS, SEM_PAR_CORRESPONDENTE_DESC, INTERJORNADA_DESC, JORNADA_MAIOR_10HORAS_DESC, JORNADA_MAIOR_12HORAS_DESC, BANCO_HORA,STATUS, JUSTIFICATIVA_BATIDA, OBS
			FROM (

				SELECT
					A.CHAPA,
					B.NOME,
					Z.DATA,
					ATRASO,
					FALTA,
					EXTRAEXECUTADO,
					(CASE WHEN Z.DATAREFERENCIA IS NOT NULL THEN Z.DATAREFERENCIA ELSE Z.DATA END) DATAREFERENCIA,
					Z.DATAREFERENCIA DATAREFERENCIA2,
					Z.IDAAFDT,
					Z.BATIDA,
					Z.NATUREZA,
					Z.STATUS,

					(CASE 
          WHEN (SELECT ISNULL(SUM(FIM - INICIO), 0) FROM AJUSTFUN WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATAREFERENCIA = A.DATA AND APROVADO = '1') > 0 
          THEN 0 
          WHEN ATRASO >= '0' 
          THEN ATRASO ELSE NULL END) ATRASO_CASE,
					ISNULL((SELECT SUM(FIM-INICIO) FROM AJUSTFUN D WHERE A.CODCOLIGADA = D.CODCOLIGADA AND A.CHAPA = D.CHAPA AND A.DATA = D.DATAREFERENCIA AND TIPOOCORRENCIA IN ('A','AC') AND JUSTIFICATIVA IS NULL),0) ATRASO_JUST,

					(CASE WHEN FALTA > '0' THEN FALTA ELSE NULL END) FALTA_CASE,
					--ISNULL((SELECT SUM(FIM-INICIO) FROM AJUSTFUN D WHERE A.CODCOLIGADA = D.CODCOLIGADA AND A.CHAPA = D.CHAPA AND A.DATA = D.DATA AND TIPOOCORRENCIA IN ('F') AND JUSTIFICATIVA IS NULL),0) FALTA_JUST,
          ISNULL((SELECT COUNT(CHAPA) FROM AABONFUN WHERE CHAPA = A.CHAPA AND CODCOLIGADA = A.CODCOLIGADA AND DATA = A.DATA AND CODABONO = '".((DBRM_BANCO == 'CorporeRMPRD') ? '031' : '030')."'),0) FALTA_JUST,

					(CASE WHEN EXTRAEXECUTADO >= '0' THEN EXTRAEXECUTADO ELSE NULL END) EXTRAEXECUTADO_CASE,
					--(SELECT CODAVISO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '5') SEM_PAR_CORRESPONDENTE,
					(SELECT CODAVISO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '1') INTERJORNADA,
					(SELECT CODAVISO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '2') JORNADA_MAIOR_10HORAS,
          (SELECT CODAVISO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '11') JORNADA_MAIOR_12HORAS,
					--(SELECT DESCRICAO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '5') SEM_PAR_CORRESPONDENTE_DESC,

          'Registro sem par correspondente' SEM_PAR_CORRESPONDENTE_DESC,

          CASE (
            SELECT
              COUNT(BAT.BATIDA)
            FROM
              ABATFUN BAT (NOLOCK)
            WHERE
                BAT.CODCOLIGADA = A.CODCOLIGADA
              AND BAT.CHAPA = A.CHAPA
              AND COALESCE(BAT.DATAREFERENCIA, BAT.DATA) = A.DATA
          ) % 2 WHEN 0 THEN NULL ELSE '5' END SEM_PAR_CORRESPONDENTE,

					(SELECT DESCRICAO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '1') INTERJORNADA_DESC,
					(SELECT DESCRICAO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '2') JORNADA_MAIOR_10HORAS_DESC,
          (SELECT DESCRICAO FROM AAVISOCALCULADO C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '11') JORNADA_MAIOR_12HORAS_DESC,
          (CASE WHEN D.justificativa IN (1,21) THEN 'X' ELSE NULL END) BANCO_HORA,
              (
                  SELECT 
                      MAX(COALESCE(A.justent1,A.justent2,A.justent3,A.justent4,A.justsai1,A.justsai2,A.justsai3,A.justsai4)) 
                  FROM 
                      ".DBPORTAL_BANCO."..zcrmportal_ponto_horas A
                  WHERE 
                          A.dtponto = Z.DATA
                      AND A.chapa = Z.CHAPA COLLATE Latin1_General_CI_AS
                      AND A.movimento = 1 
                      AND A.coligada = Z.CODCOLIGADA
                      AND COALESCE(A.ent1,A.ent2,A.ent3,A.ent4,A.sai1,A.sai2,A.sai3,A.sai4) = Z.BATIDA
              
              ) JUSTIFICATIVA_BATIDA,
          D.OBS OBS
				FROM
					AAFHTFUN A
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func D ON  A.DATA  = D.dtponto AND A.CHAPA = D.chapa Collate Database_Default AND A.CODCOLIGADA = D.coligada AND D.justificativa IN (1,21)
          ,
					PFUNC B,
					ABATFUN Z
          
				WHERE
					A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND
					Z.CODCOLIGADA = A.CODCOLIGADA AND
					Z.CHAPA = A.CHAPA AND
					(CASE WHEN Z.DATAREFERENCIA IS NOT NULL THEN Z.DATAREFERENCIA ELSE Z.DATA END) = A.DATA AND
					A.CODCOLIGADA = '{$this->coligada}' AND
					(CASE WHEN Z.DATAREFERENCIA IS NOT NULL THEN Z.DATAREFERENCIA ELSE Z.DATA END) BETWEEN '{$dtini}' AND '{$dtfim}'
					/*AND (B.DATADEMISSAO > (GETDATE() - 30) OR B.DATADEMISSAO IS NULL)*/
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
                    CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
                    AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
            )X WHERE X.DATA >= '{$dtini}'
            ORDER BY X.DATA ASC
        ) IS NOT NULL

					{$FT_FUNCIONARIO}
					{$FT_CODLOJA}
          {$qr_secao}
          {$filtro_secao}

			) X
				WHERE
					(ATRASO_CASE IS NOT NULL OR FALTA_CASE IS NOT NULL OR EXTRAEXECUTADO_CASE  IS NOT NULL OR SEM_PAR_CORRESPONDENTE  IS NOT NULL OR INTERJORNADA  IS NOT NULL OR JORNADA_MAIOR_10HORAS  IS NOT NULL OR JORNADA_MAIOR_12HORAS  IS NOT NULL)
				{$FT_GLOBAL}
				{$FT_BANCOHORA}
				ORDER BY DATA, BATIDA ASC
		";

    //if($_SESSION['log_id'] == 1) echo '<pre>'.$query.'</pre>';exit();
    $result = $this->dbrm->query($query);
    // if(!$result) return false;
    return ($result->getNumRows() > 0) 
         ? $result->getResultArray() 
         : false;        
  }


  public function listaDataCritica($dados = false, $codloja = false, $excel = false) {


    $filtro_top1000 = ((!$excel) ? " /*TOP 1000*/ " : "");
    $select_escala = ((!$excel) ? " ESCALA, " : "");
    $sql_escala = ((!$excel) ? "
        (SELECT 
        COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
        COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
        COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
        COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
        FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
    ) ESCALA,
    " : "");

    $s_par_corresp      = isset($dados['ck_semPar']) ? $dados['ck_semPar'] : false;
    $extra_executado    = isset($dados['ck_ExtraExecutado']) ? $dados['ck_ExtraExecutado'] : false;
    $vl_extra_executado = isset($dados['vl_extra_executado']) ? $dados['vl_extra_executado'] : false;
    $atrasos            = isset($dados['ck_Atrasos']) ? $dados['ck_Atrasos'] : false;
    $vl_atrasos         = isset($dados['vl_atrasos']) ? $dados['vl_atrasos'] : false;
    $faltas             = isset($dados['ck_Faltas']) ? $dados['ck_Faltas'] : false;
    $jor_acima_10       = isset($dados['ck_jorMaior10']) ? $dados['ck_jorMaior10'] : false;
    $jor_acima_12       = isset($dados['ck_jorMaior12']) ? $dados['ck_jorMaior12'] : false;
    $interjornada       = isset($dados['ck_interjornada']) ? $dados['ck_interjornada'] : false;
    $periodo            = isset($dados['periodo']) ? $dados['periodo'] : false;
    $dtini              = isset($dados['data_inicio']) ? $dados['data_inicio'] : false;
    $dtfim              = isset($dados['data_fim']) ? $dados['data_fim'] : false;
    $funcionario        = isset($dados['funcionario']) ? $dados['funcionario'] : false;
    $bancohoras         = isset($dados['ck_bancohoras']) ? $dados['ck_bancohoras'] : false;
    $secao              = isset($dados['secao']) ? $dados['secao'] : false;

    $FT_BANCOHORA = "  ";
    if($bancohoras){
      $FT_BANCOHORA = " AND X.BANCO_HORA IS NOT NULL ";
    }

    $FT_GLOBAL = false;

		if($s_par_corresp){
			$FT_GLOBAL .= " OR SEM_PAR_CORRESPONDENTE IS NOT NULL ";
		}

		if($extra_executado && $vl_extra_executado){
			$FT_GLOBAL .= " OR EXTRAEXECUTADO_CASE >= '".h2m($vl_extra_executado)."' ";
		}

		if($atrasos && $vl_atrasos){
			$FT_GLOBAL .= " OR
		(CASE WHEN ATRASO_JUST = 0 THEN 0
		  WHEN ATRASO_JUST > 0 AND ATRASO_JUST < ATRASO_CASE THEN ATRASO_JUST
		  WHEN ATRASO_JUST > 0 AND ATRASO_JUST > ATRASO_CASE THEN ATRASO_CASE ELSE ATRASO_CASE END )
			>= '".h2m($vl_atrasos)."' ";
		}

		if($faltas){
	  
			$FT_GLOBAL .= " OR 
      ISNULL(CASE WHEN FALTA_JUST > 0 THEN 1 ELSE FALTA_CASE END,0) > 0 ";
		}

		if($jor_acima_10){
			$FT_GLOBAL .= " OR JORNADA_MAIOR_10HORAS IS NOT NULL ";
		}

		if($jor_acima_12){
			$FT_GLOBAL .= " OR JORNADA_MAIOR_12HORAS IS NOT NULL ";
		}

		if($interjornada){
			$FT_GLOBAL .= " OR INTERJORNADA IS NOT NULL ";
		}

    if( $FT_GLOBAL ) {
  		$FT_GLOBAL = 'AND ( '.substr($FT_GLOBAL,3,9999999).' )';
    }

    $FT_FUNCIONARIO = false;
    if($funcionario){
      $FT_FUNCIONARIO = " AND A.CHAPA = '" . $funcionario . "'";
    }

    try{

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
      $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

      if($dados['rh']) $in_secao = "";
      $qr_secao = $in_secao;

      if(!is_array($secao)){
        $filtro_secao = ($secao != '') ? " AND B.CODSECAO = '{$secao}' " : "";
      }
      

      if(is_array($secao)){
        $filtro_secao = "";
        $codsecao_in = "";
        foreach($secao as $codSecao){
          $codsecao_in .= "'{$codSecao}',";
        }

        $filtro_secao = " AND B.CODSECAO IN (".rtrim($codsecao_in,',').") ";
      }


      // $filtro_secao .= "
      
      // AND A.CHAPA NOT IN ('050000334', '050006632')
      
      // ";

      $query = "
      SELECT DISTINCT {$filtro_top1000} CPF, CHAPA, NOME, CODTIPO, CODFILIAL, DATA, BATIDAS_PORTAL, {$select_escala}

      (CASE WHEN ATRASO_JUST = 0 THEN 0
        WHEN ATRASO_JUST > 0 AND ATRASO_JUST < ATRASO_CASE THEN ATRASO_JUST
        WHEN ATRASO_JUST > 0 AND ATRASO_JUST > ATRASO_CASE THEN ATRASO_CASE ELSE ATRASO_CASE END )
      ATRASO_CASE
      ,
      /*(CASE WHEN FALTA_CASE = 0 THEN 0
        WHEN FALTA_JUST > 0 AND FALTA_JUST < FALTA_CASE THEN FALTA_JUST
        WHEN FALTA_JUST > 0 AND FALTA_JUST > FALTA_CASE THEN FALTA_CASE ELSE FALTA_CASE END )
      FALTA_CASE*/
      ISNULL(FALTA_CASE,0) FALTA_CASE
      , EXTRAEXECUTADO_CASE, SEM_PAR_CORRESPONDENTE, INTERJORNADA, JORNADA_MAIOR_10HORAS, JORNADA_MAIOR_12HORAS, SEM_PAR_CORRESPONDENTE_DESC, INTERJORNADA_DESC, JORNADA_MAIOR_10HORAS_DESC, JORNADA_MAIOR_12HORAS_DESC, BANCO_HORA, JUSTIFICATIVA, OBS, JUSTIFICATIVA_CODIGO, QTDE_ABONO, ABONO_PENDENTE_RH, JUSTIFICATIVA_ATITUDE, USABANCOHORAS, CPF, CODSITUACAO, IS_MOTORISTA
        FROM (

          SELECT
            P.CPF,
            A.CHAPA,
            B.NOME,
            B.CODTIPO,
            B.CODFILIAL,
            A.DATA,
            ATRASO,
            FALTA,
            EXTRAEXECUTADO,

            {$sql_escala}

            (CASE WHEN ATRASO >= '0' THEN ATRASO ELSE NULL END) ATRASO_CASE,
            ISNULL((SELECT SUM(FIM-INICIO) FROM AJUSTFUN D (NOLOCK) WHERE A.CODCOLIGADA = D.CODCOLIGADA AND A.CHAPA = D.CHAPA AND A.DATA = D.DATAREFERENCIA AND TIPOOCORRENCIA IN ('A','AC') AND JUSTIFICATIVA IS NULL),0) ATRASO_JUST,

            (CASE WHEN FALTA > '0' THEN FALTA ELSE NULL END) FALTA_CASE,
            ISNULL((SELECT COUNT(CHAPA) FROM AABONFUN WHERE CHAPA = A.CHAPA AND CODCOLIGADA = A.CODCOLIGADA AND DATA = A.DATA AND CODABONO = '".((DBRM_BANCO == 'CorporeRMPRD') ? '031' : '030')."'),0) FALTA_JUST,

            (CASE WHEN EXTRAEXECUTADO >= '0' THEN EXTRAEXECUTADO ELSE NULL END) EXTRAEXECUTADO_CASE,
            --(SELECT CODAVISO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '5') SEM_PAR_CORRESPONDENTE,
            (SELECT CODAVISO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '1') INTERJORNADA,
            (SELECT CODAVISO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '2') JORNADA_MAIOR_10HORAS,
            (SELECT CODAVISO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '11') JORNADA_MAIOR_12HORAS,
            --(SELECT DESCRICAO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '5') SEM_PAR_CORRESPONDENTE_DESC,

            'Registro sem par correspondente' SEM_PAR_CORRESPONDENTE_DESC,

            CASE (
            	SELECT
            		COUNT(BAT.BATIDA)
            	FROM
            		ABATFUN BAT (NOLOCK)
            	WHERE
            			BAT.CODCOLIGADA = A.CODCOLIGADA
            		AND BAT.CHAPA = A.CHAPA
            		AND COALESCE(BAT.DATAREFERENCIA, BAT.DATA) = A.DATA
            ) % 2 WHEN 0 THEN NULL ELSE '5' END SEM_PAR_CORRESPONDENTE,

            (SELECT DESCRICAO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '1') INTERJORNADA_DESC,
            (SELECT DESCRICAO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '2') JORNADA_MAIOR_10HORAS_DESC,
            (SELECT DESCRICAO FROM AAVISOCALCULADO C (NOLOCK) WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA = C.DATAREFERENCIA AND CODAVISO = '11') JORNADA_MAIOR_12HORAS_DESC,
            (SELECT COUNT(*) FROM ABATFUN AS hor (NOLOCK) WHERE hor.DATAREFERENCIA = A.DATA AND hor.CODCOLIGADA = A.CODCOLIGADA AND hor.CHAPA = A.CHAPA) AS BATIDAS_PORTAL,
            (CASE WHEN D.justificativa IN (1,21) THEN 'X' ELSE NULL END) BANCO_HORA,

            (
              SELECT max(CAST(BB.descricao AS VARCHAR)) FROM ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func AA  (NOLOCK) 
		        INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motivos BB (NOLOCK) ON AA.justificativa = BB.id AND AA.coligada = BB.codcoligada WHERE AA.coligada = A.CODCOLIGADA AND AA.dtponto = A.DATA AND AA.chapa = A.CHAPA Collate Database_Default
            ) JUSTIFICATIVA,

            (
              SELECT top 1 AA.obs FROM PortalRHDEV..zcrmportal_ponto_justificativa_func AA  (NOLOCK) 
		        WHERE AA.coligada = A.CODCOLIGADA AND AA.dtponto = A.DATA AND AA.chapa = A.CHAPA Collate Database_Default
            ) OBS,

            (
              SELECT max(CAST(BB.id AS VARCHAR)) FROM ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func AA (NOLOCK) 
		        INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motivos BB (NOLOCK) ON AA.justificativa = BB.id AND AA.coligada = BB.codcoligada WHERE AA.coligada = A.CODCOLIGADA AND AA.dtponto = A.DATA AND AA.chapa = A.CHAPA Collate Database_Default
            ) JUSTIFICATIVA_CODIGO,

            (
              select max(CONCAT(justificativa_excecao,status)) FROM ".DBPORTAL_BANCO."..zcrmportal_ponto_horas AA WHERE AA.chapa = A.CHAPA Collate Database_Default AND AA.coligada = A.CODCOLIGADA AND AA.dtponto = A.DATA AND AA.movimento in (9) AND dt_delete IS NULL
            ) JUSTIFICATIVA_ATITUDE,
            ISNULL(AC.USABANCOHORAS,0) USABANCOHORAS,
            (SELECT COUNT(CHAPA) FROM AABONFUN (NOLOCK) WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATA = A.DATA) QTDE_ABONO,
            (SELECT COUNT(CHAPA) FROM AABONFUN (NOLOCK) WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATA = A.DATA AND DESCONSIDERA = 1) ABONO_PENDENTE_RH
            ,
            
            (
              SELECT TOP 1 ISNULL(HH.DESCRICAO, HH3.DESCRICAO) FROM (
              SELECT
                  TOP 1 
                  HB.DESCRICAO
              FROM
                  PFHSTSIT HA (NOLOCK)
                  INNER JOIN PCODSITUACAO HB (NOLOCK) ON HB.CODCLIENTE = HA.NOVASITUACAO
              WHERE
                      HA.CODCOLIGADA = B.CODCOLIGADA
                  AND HA.CHAPA = B.CHAPA
                  AND (
                      HA.DATAMUDANCA <= A.DATA
                  )
              ORDER BY
                  DATAMUDANCA DESC
              )HH
              RIGHT JOIN PFUNC HH2 (NOLOCK) ON HH2.CODCOLIGADA = B.CODCOLIGADA 
              INNER JOIN PCODSITUACAO HH3 (NOLOCK) ON HH3.CODINTERNO = HH2.CODSITUACAO
              WHERE 
                HH2.CHAPA = B.CHAPA
              AND HH2.CODCOLIGADA = B.CODCOLIGADA
            )
            
            CODSITUACAO
            ,(SELECT COUNT(id) FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista m WHERE m.codfuncao = B.CODFUNCAO COLLATE Latin1_General_CI_AS AND b.codcoligada = B.CODCOLIGADA AND m.usudel IS NULL) IS_MOTORISTA


          FROM
            AAFHTFUN A (NOLOCK)
              LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func D (NOLOCK) ON  A.DATA  = D.dtponto AND A.CHAPA = D.chapa Collate Database_Default AND A.CODCOLIGADA = D.coligada AND D.justificativa IN (1,21)
              INNER JOIN APARFUN AB ON A.CODCOLIGADA = AB.CODCOLIGADA AND A.CHAPA = AB.CHAPA
              INNER JOIN APARCOL AC ON AB.CODCOLIGADA = AC.CODCOLIGADA AND AB.CODPARCOL = AC.CODIGO
            ,
            PFUNC B (NOLOCK)
            ,PPESSOA P (NOLOCK)
          WHERE
            A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND
            B.CODPESSOA = P.CODIGO AND
            A.CODCOLIGADA = '{$this->coligada}' AND
            -- A.DATA BETWEEN {$periodo}
            A.DATA BETWEEN '{$dtini}' AND '{$dtfim}'
            {$FT_FUNCIONARIO}
            {$qr_secao}
            {$filtro_secao}
          /*AND B.CODSITUACAO <> 'D'*/
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
                    CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
                    AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
            )X WHERE X.DATA >= '{$dtini}'
            ORDER BY X. DATA ASC
        ) IS NOT NULL

          AND (SELECT ISNULL(SUM(FIM - INICIO), 0) FROM AJUSTFUN WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATAREFERENCIA = A.DATA AND APROVADO = '1') <= 0

           -- AND (B.DATADEMISSAO > (GETDATE() - 30) OR B.DATADEMISSAO IS NULL)

        ) X
          WHERE
            (ATRASO_CASE IS NOT NULL OR FALTA_CASE IS NOT NULL OR EXTRAEXECUTADO_CASE  IS NOT NULL OR SEM_PAR_CORRESPONDENTE  IS NOT NULL OR INTERJORNADA  IS NOT NULL OR JORNADA_MAIOR_10HORAS  IS NOT NULL OR JORNADA_MAIOR_12HORAS  IS NOT NULL)
          {$FT_GLOBAL}
            /*AND (CASE WHEN FALTA_CASE > 0 AND BATIDAS_PORTAL > 0 THEN 0 ELSE 1 END) > 0*/
            {$FT_BANCOHORA}
      
        ORDER BY NOME, DATA
      ";

// if($_SESSION['log_id'] == 3021){
//   echo '<pre>'.$query.'</pre>';exit();
// }
      // exit();
      $result = $this->dbrm->query($query);
      if(!$result) return false;
      return ($result->getNumRows() > 0) 
          ? $result->getResultArray() 
          : false;
          

    }catch(\Exception $e){

        return responseJson('error', 'Erro interno: '.$e);

    }

    
  }

    public function listaBatidasApontadaCritica($dados) {

      $dtini  = isset($dados['data_inicio']) ? $dados['data_inicio'] : false;
      $dtfim  = isset($dados['data_fim']) ? $dados['data_fim'] : false;
      $chapa  = (strlen(trim($dados['funcionario'] ?? '')) > 0) ? " AND chapa = '{$dados['funcionario']}' " : '';

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
      $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

      if($dados['rh']) $in_secao = "";
      $qr_secao = $in_secao;

    
    
      $query = " SELECT id, chapa, dtponto, ent1, ent2, ent3, ent4, ent5, sai1, sai2, sai3, sai4, sai5, status, motivo_reprova, obs, COALESCE(natent1, natent2, natent3, natent4, natent5, natsai1, natsai2, natsai3, natsai4, natsai5) natureza, COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) data_referencia FROM zcrmportal_ponto_horas (NOLOCK) WHERE coligada = '{$this->coligada}' AND status IN ('1','2','3','R') 
        and dtponto BETWEEN '{$dtini}' AND '{$dtfim}' AND dt_delete IS NULL {$chapa}
        AND EXISTS (SELECT B.CHAPA FROM ".DBRM_BANCO."..PFUNC B (NOLOCK) WHERE /*B.CODSITUACAO NOT IN ('D')*/
        
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
                  CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
                  AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
          )X WHERE X.DATA >= '{$dtini}'
          ORDER BY X.DATA ASC
      ) IS NOT NULL
        
        AND CODCOLIGADA = '{$this->coligada}' {$qr_secao})
       ";

      //if($_SESSION['log_id'] == 1) echo '<pre>'.$query.'</pre>';exit();
      $result = $this->dbportal->query($query);
      if(!$result) return false;
      return ($result->getNumRows() > 0) 
          ? $result->getResultArray() 
          : false;
    }

    public function getDadosCritica($idbatida){
        
        
        $id_batidas = "";
        
        // pega os dados da batida
        foreach($idbatida as $idBatida){
          $id_batidas .= "'".$idBatida."',";
        }
        
        $query = " SELECT *,
          
        CASE 
          WHEN ent1 IS NOT NULL THEN ent1
          WHEN ent2 IS NOT NULL THEN ent2
          WHEN ent3 IS NOT NULL THEN ent3
          WHEN ent4 IS NOT NULL THEN ent4
          WHEN sai1 IS NOT NULL THEN sai1
          WHEN sai2 IS NOT NULL THEN sai2
          WHEN sai3 IS NOT NULL THEN sai3
          WHEN sai4 IS NOT NULL THEN sai4
        END batida,
        CASE 
          WHEN ent1 IS NOT NULL THEN 'Entrada'
          WHEN ent2 IS NOT NULL THEN 'Entrada'
          WHEN ent3 IS NOT NULL THEN 'Entrada'
          WHEN ent4 IS NOT NULL THEN 'Entrada'
          WHEN sai1 IS NOT NULL THEN 'Saida'
          WHEN sai2 IS NOT NULL THEN 'Saida'
          WHEN sai3 IS NOT NULL THEN 'Saida'
          WHEN sai4 IS NOT NULL THEN 'Saida'
        END natureza,
        CASE 
          WHEN justent1 IS NOT NULL THEN justent1
          WHEN justent2 IS NOT NULL THEN justent2
          WHEN justent3 IS NOT NULL THEN justent3
          WHEN justent4 IS NOT NULL THEN justent4
          WHEN justsai1 IS NOT NULL THEN justsai1
          WHEN justsai2 IS NOT NULL THEN justsai2
          WHEN justsai3 IS NOT NULL THEN justsai3
          WHEN justsai4 IS NOT NULL THEN justsai4
        END justificativa,
        CASE 
          WHEN ident1 IS NOT NULL THEN ident1
          WHEN ident2 IS NOT NULL THEN ident2
          WHEN ident3 IS NOT NULL THEN ident3
          WHEN ident4 IS NOT NULL THEN ident4
          WHEN idsai1 IS NOT NULL THEN idsai1
          WHEN idsai2 IS NOT NULL THEN idsai2
          WHEN idsai3 IS NOT NULL THEN idsai3
          WHEN idsai4 IS NOT NULL THEN idsai4
        END idbatida,
        CASE 
          WHEN dtrefent1 IS NOT NULL THEN dtrefent1
          WHEN dtrefent2 IS NOT NULL THEN dtrefent2
          WHEN dtrefent3 IS NOT NULL THEN dtrefent3
          WHEN dtrefent4 IS NOT NULL THEN dtrefent4
          WHEN dtrefsai1 IS NOT NULL THEN dtrefsai1
          WHEN dtrefsai2 IS NOT NULL THEN dtrefsai2
          WHEN dtrefsai3 IS NOT NULL THEN dtrefsai3
          WHEN dtrefsai4 IS NOT NULL THEN dtrefsai4
        END datareferencia
        ,atitude_dt
        ,atitude_ini
        ,atitude_fim
        ,atitude_justificativa
        ,atitude_tipo
        ,atestado
        

        FROM zcrmportal_ponto_horas WHERE id IN (".substr($id_batidas, 0, -1).") AND coligada = '{$this->coligada}' AND status <> 'S' ";


        //if($_SESSION['log_id'] == 1) echo '<pre>'.$query.'</pre>';exit();
        $result = $this->dbportal->query($query);

         if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
        
 
        
      }
      
    public function getDadosCriticaChapa($idbatida){
        
  
        $id_batidas = "";
        
        // pega os dados da batida
        foreach($idbatida as $idBatida){
          $id_batidas .= "'".$idBatida."',";
        }
        
        $query = " SELECT chapa FROM zcrmportal_ponto_horas WHERE id IN (".substr($id_batidas, 0, -1).") AND status <> 'S' AND coligada = '{$this->coligada}' GROUP BY chapa ";
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
 
        
    }
      
    public function postAprovaBatidaCritica($chapa, $idbatida, $movimento){
        
        $id_batidas = "";
        
        // pega os dados da batida
        foreach($idbatida as $idBatida){
          $id_batidas .= "'".$idBatida."',";
        }
        
        $query = " UPDATE zcrmportal_ponto_horas SET status = 'S', aprrh_user = '". $this->log_id ."', aprrh_data = '".date('Y-m-d H:i:s')."' WHERE chapa = '{$chapa}' AND id IN (".substr($id_batidas, 0, -1).") AND coligada = '{$this->coligada}' AND status <> 'S' AND movimento = '{$movimento}' ";
        $this->dbportal->query($query);


        return ($this->dbportal->affectedRows() > 0) 
        ? responseJson('success', 'Registro aprovado com sucesso.')
        : responseJson('error', 'Falha ao aprovar registro.');
    }
    public function listarCriticaPeriodoPonto(){

        $query = " SELECT * FROM zcrmportal_espelho_config WHERE coligada = '{$this->coligada}' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
	

    public function ListarCriticaPeriodoRM($isRH = false){

        $configuracao = $this->listarCriticaPeriodoPonto();
        if(!$configuracao) return false;
        
        $filtro = ($isRH) ? " " : " AND INICIOMENSAL >= '{$configuracao[0]['dtinicio']}' AND FIMMENSAL  <= '{$configuracao[0]['dtfim']}' ";
        $query = "
            SELECT 
                * 
            FROM 
                APERIODO 
            WHERE 
                    CODCOLIGADA = '{$this->coligada}'
                ".$filtro." 

            ORDER BY 
                ANOCOMP DESC, 
                MESCOMP DESC
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function CadastrarBatida($dados){

      try{

        //************************************************ */
        //**** Inclusão de batida */
        //************************************************ */

        if($dados['movimento'] == 1){

        switch(substr($dados['addLocalizacao'],0,1)){

          case 'a' : $campo = " ent1 "; $campoid = " ident1 "; $nat = " natent1 "; $just = " justent1 "; $dtref = " dtrefent1 "; break;
          case 'b' : $campo = " sai1 "; $campoid = " idsai1 "; $nat = " natsai1 "; $just = " justsai1 "; $dtref = " dtrefsai1 "; break;
          case 'c' : $campo = " ent2 "; $campoid = " ident2 "; $nat = " natent2 "; $just = " justent2 "; $dtref = " dtrefent2 "; break;
          case 'd' : $campo = " sai2 "; $campoid = " idsai2 "; $nat = " natsai2 "; $just = " justsai2 "; $dtref = " dtrefsai2 "; break;
          case 'e' : $campo = " ent3 "; $campoid = " ident3 "; $nat = " natent3 "; $just = " justent3 "; $dtref = " dtrefent3 "; break;
          case 'f' : $campo = " sai3 "; $campoid = " idsai3 "; $nat = " natsai3 "; $just = " justsai3 "; $dtref = " dtrefsai3 "; break;
          case 'g' : $campo = " ent4 "; $campoid = " ident4 "; $nat = " natent4 "; $just = " justent4 "; $dtref = " dtrefent4 "; break;
          case 'h' : $campo = " sai4 "; $campoid = " idsai4 "; $nat = " natsai4 "; $just = " justsai4 "; $dtref = " dtrefsai4 "; break;
          case 'i' : $campo = " ent5 "; $campoid = " ident5 "; $nat = " natent5 "; $just = " justent5 "; $dtref = " dtrefent5 "; break;
          case 'j' : $campo = " sai5 "; $campoid = " idsai5 "; $nat = " natsai5 "; $just = " justsai5 "; $dtref = " dtrefsai5 "; break;
        }

        $justificativaOutros = $dados['textoJustOutros'];

        if($dados['addJustificativa'] == "Outros"){
        $justificativa     = $dados['textoJustOutros'];
        }else{
        $justificativa       = $dados['addJustificativa'];
        }
        
        $batida              = h2m($dados['addBatida']);
        $natureza            = $dados['addNatureza'];
        $chapa               = $dados['addChapa'];
        
        $dataReferencia           = explode('/',$dados['addDataReferencia']);
        $dataReferenciaConvertida = $dataReferencia[2] . '/' . $dataReferencia[1] . '/' . $dataReferencia[0];
        $dataReferenciaRM         = ($dataReferenciaConvertida . '00:00:00');

        $data           = explode('/',$dados['addData']);
        $dataconvertida = $data[2] . '/' . $data[1] . '/' . $data[0];
        $dataRM         = ($dataconvertida . ' 00:00:00');

        $query = "

            INSERT INTO zcrmportal_ponto_horas
            (
            coligada
            ,dtcadastro
            ,usucad
            ,ipcadastro
            ,dtponto
            ,chapa
            ,status
            ,movimento
            ,{$campo}
            ,{$campoid}
            ,{$nat}
            ,{$just}
            ,{$dtref}
            ) 
            VALUES( 
            '{$this->coligada}'
            ,'".date('Y/m/d H:i:s')."'
            ,'{$this->log_id}'
            ,'".$_SERVER['REMOTE_ADDR']."'
            ,'{$dataconvertida}'
            ,'{$chapa}'
            ,2
            ,1
            ,{$batida}
            ,0
            ,{$natureza}
            ,'{$justificativa}'
            ,'{$dataReferenciaConvertida}'
            )
          ";

          $this->dbportal->query($query);

          if($this->dbportal->affectedRows() > 0){
            $insertID = $this->dbportal->insertID();
                if(self::isGestorOrLiderAprovador($chapa)){
                  $mAprova = model('Ponto/AprovaModel');
                  $mAprova->aprovaBatidaRH($insertID);
                }
          }

        return ($this->dbportal->affectedRows() > 0) 
            ? responseJson('success', 'Cadastro de registro realizado com sucesso')
            : responseJson('error', 'Falha cadastrar registro.');

        }


        //************************************************ */
        //**** Exclusão de batida */
        //************************************************ */
        elseif($dados['movimento'] == 'EB'){

            switch(substr($dados['alteraLocalizacao'],0,1)){

          case 'a' : $campo = " ent1 "; $campoid = " ident1 "; $nat = " natent1 "; $just = " justent1 "; $dtref = " dtrefent1 "; break;
          case 'b' : $campo = " sai1 "; $campoid = " idsai1 "; $nat = " natsai1 "; $just = " justsai1 "; $dtref = " dtrefsai1 "; break;
          case 'c' : $campo = " ent2 "; $campoid = " ident2 "; $nat = " natent2 "; $just = " justent2 "; $dtref = " dtrefent2 "; break;
          case 'd' : $campo = " sai2 "; $campoid = " idsai2 "; $nat = " natsai2 "; $just = " justsai2 "; $dtref = " dtrefsai2 "; break;
          case 'e' : $campo = " ent3 "; $campoid = " ident3 "; $nat = " natent3 "; $just = " justent3 "; $dtref = " dtrefent3 "; break;
          case 'f' : $campo = " sai3 "; $campoid = " idsai3 "; $nat = " natsai3 "; $just = " justsai3 "; $dtref = " dtrefsai3 "; break;
          case 'g' : $campo = " ent4 "; $campoid = " ident4 "; $nat = " natent4 "; $just = " justent4 "; $dtref = " dtrefent4 "; break;
          case 'h' : $campo = " sai4 "; $campoid = " idsai4 "; $nat = " natsai4 "; $just = " justsai4 "; $dtref = " dtrefsai4 "; break;
          case 'i' : $campo = " ent5 "; $campoid = " ident5 "; $nat = " natent5 "; $just = " justent5 "; $dtref = " dtrefent5 "; break;
          case 'j' : $campo = " sai5 "; $campoid = " idsai5 "; $nat = " natsai5 "; $just = " justsai5 "; $dtref = " dtrefsai5 "; break;
        }

            $justificativa = $dados['textoJustExcluir'];
            $batida        = h2m($dados['alteraBatida']);
            $natureza      = $dados['alteraNatureza'];
            $idaafdt        = $dados['alteraIDAAFDT'];
            $chapa         = $dados['alteraChapa'];

            $data           = explode('/',$dados['alteraData']);
            $dataconvertida = $data[2] . '/' . $data[1] . '/' . $data[0];
            $dataRM         = ($dataconvertida . ' 00:00:00');

            $query = "

            INSERT INTO zcrmportal_ponto_horas
            (
            coligada
            ,dtcadastro
            ,usucad
            ,ipcadastro
            ,dtponto
            ,chapa
            ,status
            ,movimento
            ,{$campo}
            ,{$campoid}
            ,{$nat}
            ,{$just}
            ,{$dtref}
            ) 
            VALUES( 
            '{$this->coligada}'
            ,'".date('Y/m/d H:i:s')."'
            ,'{$this->log_id}'
            ,'".$_SERVER['REMOTE_ADDR']."'
            ,'{$dataconvertida}'
            ,'{$chapa}'
            ,2
            ,2
            ,{$batida}
            ,{$idaafdt}
            ,{$natureza}
            ,'{$justificativa}'
            ,0
            )
          ";

          $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
            ? responseJson('success', 'Exclusão enviada para aprovação')
            : responseJson('error', 'Falha ao excluir.');


        }

        
        //************************************************ */
        //**** Altera natureza */
        //************************************************ */
        elseif($dados['movimento'] == 'AN'){

            switch(substr($dados['alteraLocalizacao'],0,1)){

          case 'a' : $campo = " ent1 "; $campoid = " ident1 "; $nat = " natent1 "; $just = " justent1 "; $dtref = " dtrefent1 "; break;
          case 'b' : $campo = " sai1 "; $campoid = " idsai1 "; $nat = " natsai1 "; $just = " justsai1 "; $dtref = " dtrefsai1 "; break;
          case 'c' : $campo = " ent2 "; $campoid = " ident2 "; $nat = " natent2 "; $just = " justent2 "; $dtref = " dtrefent2 "; break;
          case 'd' : $campo = " sai2 "; $campoid = " idsai2 "; $nat = " natsai2 "; $just = " justsai2 "; $dtref = " dtrefsai2 "; break;
          case 'e' : $campo = " ent3 "; $campoid = " ident3 "; $nat = " natent3 "; $just = " justent3 "; $dtref = " dtrefent3 "; break;
          case 'f' : $campo = " sai3 "; $campoid = " idsai3 "; $nat = " natsai3 "; $just = " justsai3 "; $dtref = " dtrefsai3 "; break;
          case 'g' : $campo = " ent4 "; $campoid = " ident4 "; $nat = " natent4 "; $just = " justent4 "; $dtref = " dtrefent4 "; break;
          case 'h' : $campo = " sai4 "; $campoid = " idsai4 "; $nat = " natsai4 "; $just = " justsai4 "; $dtref = " dtrefsai4 "; break;
          case 'i' : $campo = " ent5 "; $campoid = " ident5 "; $nat = " natent5 "; $just = " justent5 "; $dtref = " dtrefent5 "; break;
          case 'j' : $campo = " sai5 "; $campoid = " idsai5 "; $nat = " natsai5 "; $just = " justsai5 "; $dtref = " dtrefsai5 "; break;
        }

            $justificativa = $dados['textoJustExcluir'];
            $batida        = h2m($dados['alteraBatida']);

            if($dados['alteraNatureza'] == 1){
              $natureza  = 0;
            }
            else{
              $natureza  = 1;
            }
            $chapa          = $dados['alteraChapa'];
            $idaafdt        = $dados['alteraIDAAFDT'];
            $data           = explode('/',$dados['alteraData']);
            $dataconvertida = $data[2] . '/' . $data[1] . '/' . $data[0];
            $dataRM         = ($dataconvertida . ' 00:00:00');

            $query = "

            INSERT INTO zcrmportal_ponto_horas
            (
            coligada
            ,dtcadastro
            ,usucad
            ,ipcadastro
            ,dtponto
            ,chapa
            ,status
            ,movimento
            ,{$campo}
            ,{$campoid}
            ,{$nat}
            ,{$dtref}
            ) 
            VALUES( 
            '{$this->coligada}'
            ,'".date('Y/m/d H:i:s')."'
            ,'{$this->log_id}'
            ,'".$_SERVER['REMOTE_ADDR']."'
            ,'{$dataconvertida}'
            ,'{$chapa}'
            ,2
            ,3
            ,{$batida}
            ,{$idaafdt}
            ,{$natureza}
            ,0
            )
          ";
          
          $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
            ? responseJson('success', 'Alteração enviada para aprovação')
            : responseJson('error', 'Falha cadastrar alteração.');


        }

        //************************************************ */
        //**** Altera data referencia */
        //************************************************ */

        elseif($dados['movimento'] == 'AR'){

            switch(substr($dados['alteraLocalizacao'],0,1)){

          case 'a' : $campo = " ent1 "; $campoid = " ident1 "; $nat = " natent1 "; $just = " justent1 "; $dtref = " dtrefent1 "; break;
          case 'b' : $campo = " sai1 "; $campoid = " idsai1 "; $nat = " natsai1 "; $just = " justsai1 "; $dtref = " dtrefsai1 "; break;
          case 'c' : $campo = " ent2 "; $campoid = " ident2 "; $nat = " natent2 "; $just = " justent2 "; $dtref = " dtrefent2 "; break;
          case 'd' : $campo = " sai2 "; $campoid = " idsai2 "; $nat = " natsai2 "; $just = " justsai2 "; $dtref = " dtrefsai2 "; break;
          case 'e' : $campo = " ent3 "; $campoid = " ident3 "; $nat = " natent3 "; $just = " justent3 "; $dtref = " dtrefent3 "; break;
          case 'f' : $campo = " sai3 "; $campoid = " idsai3 "; $nat = " natsai3 "; $just = " justsai3 "; $dtref = " dtrefsai3 "; break;
          case 'g' : $campo = " ent4 "; $campoid = " ident4 "; $nat = " natent4 "; $just = " justent4 "; $dtref = " dtrefent4 "; break;
          case 'h' : $campo = " sai4 "; $campoid = " idsai4 "; $nat = " natsai4 "; $just = " justsai4 "; $dtref = " dtrefsai4 "; break;
          case 'i' : $campo = " ent5 "; $campoid = " ident5 "; $nat = " natent5 "; $just = " justent5 "; $dtref = " dtrefent5 "; break;
          case 'j' : $campo = " sai5 "; $campoid = " idsai5 "; $nat = " natsai5 "; $just = " justsai5 "; $dtref = " dtrefsai5 "; break;
        }

            $justificativa = $dados['textoJustExcluir'];
            $batida        = h2m($dados['alteraBatida']);
            $natureza      = $dados['alteraNatureza'];
            $chapa         = $dados['alteraChapa'];
            $idaafdt        = $dados['alteraIDAAFDT'];
      
            $dataReferencia           = explode('/',$dados['textoJustAlteraJornada']);
            $dataReferenciaConvertida = $dataReferencia[2] . '/' . $dataReferencia[1] . '/' . $dataReferencia[0];
            $dataReferenciaRM         = ($dataReferenciaConvertida . '00:00:00');

            $data           = explode('/',$dados['alteraData']);
            $dataconvertida = $data[2] . '/' . $data[1] . '/' . $data[0];
            $dataRM         = ($dataconvertida . ' 00:00:00');

            $query = "

            INSERT INTO zcrmportal_ponto_horas
            (
            coligada
            ,dtcadastro
            ,usucad
            ,ipcadastro
            ,dtponto
            ,chapa
            ,status
            ,movimento
            ,{$campo}
            ,{$campoid}
            ,{$nat}
            ,{$dtref}
            ) 
            VALUES( 
            '{$this->coligada}'
            ,'".date('Y/m/d H:i:s')."'
            ,'{$this->log_id}'
            ,'".$_SERVER['REMOTE_ADDR']."'
            ,'{$dataconvertida}'
            ,'{$chapa}'
            ,2
            ,4
            ,{$batida}
            ,{$idaafdt}
            ,{$natureza}
            ,'{$dataReferenciaConvertida}'
            )
          ";

          $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
            ? responseJson('success', 'Alteração enviada para aprovação')
            : responseJson('error', 'Falha cadastrar alteração.');


        }

      }catch(\Exception $e){

        return responseJson('error', 'Erro interno: '.$e);

      }


    }
   

    public function ListaTipoAbono($tipo = false){

      $query = "
            SELECT 
                * 
            FROM 
              AABONO 
            WHERE 
              CODCOLIGADA = '{$this->coligada}'";

        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function BuscaJustificativa($chapa = false, $dtapt = false, $tipo = false, $excecao = false){

      $ft_chapa = false;
      $ft_dtapt = false;
      $ft_tipo = false;
      $ft_excecao = false;
      
      if($chapa) $ft_chapa = " AND A.chapa = '". $chapa . "'";
      if($dtapt) $ft_dtapt = " AND A.dtponto = '". $dtapt . "'";
      if($tipo) $ft_tipo = " AND B.tipo = '". $tipo . "'";
      if($excecao) $ft_excecao = " AND B.tipo NOT IN (". $excecao . ")";

      $query = "SELECT A.*, B.descricao, B.tipo, B.id as ID_JUST 
      FROM zcrmportal_ponto_justificativa_func A 
      INNER JOIN zcrmportal_ponto_motivos B ON A.justificativa = B.id AND A.coligada = B.codcoligada 
      WHERE A.coligada = '{$this->coligada}' ". $ft_chapa.$ft_dtapt.$ft_tipo.$ft_excecao;
      
      $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ListaOcorrenciaAbono($objDados){

        $query = " 
        SELECT 
          A.CHAPA, A.INICIO, A.FIM, A.TIPOOCORRENCIA, A.ATITUDE, B.JUSTIFICATIVA
        FROM 
          AOCORRENCIACALCULADA A, AJUSTFUN B
        WHERE 
          A.CODCOLIGADA = B.CODCOLIGADA
          AND A.CHAPA = B.CHAPA 
          --AND A.TIPOOCORRENCIA = B.TIPOOCORRENCIA
          AND CONVERT(DATETIME, A.INICIO, 113) = CONVERT(DATETIME, CAST(B.DATA AS VARCHAR (12)) + ' ' + CAST(B.INICIO / 60 AS VARCHAR(8)) + ':' + FORMAT(B.INICIO % 60, 'D2'), 113)
          AND A.CODCOLIGADA = '". $this->coligada ."' 
          AND A.CHAPA = '".$objDados['chapa']."' 
          AND A.DATAREFERENCIA = '".$objDados['dataref']."' 
          AND A.TIPOOCORRENCIA IN (".$objDados['tipo'].")
          AND B.JUSTIFICATIVA IS NULL
      ";

      //echo '<textarea>'. $query . '</textarea>';

      $result = $this->dbrm->query($query);
      return ($result->getNumRows() > 0) 
              ? $result->getResultArray() 
              : false;

    }

    public function ListaAbonoInseridos($objDados){
      $MOVIMENTO = "(5,7,8)";
      if($objDados['tipo'] == 'FALTA'){
        $MOVIMENTO = "(6,7,8)";
      }
      
      $query = " SELECT 
                    id, 
                    CONVERT(DATETIME, CAST(abn_dtini AS VARCHAR (12)) + ' ' + CAST(abn_horaini / 60 AS VARCHAR(8)) + ':' + FORMAT(abn_horaini % 60, 'D2'), 113) AS datainicio,
                    CONVERT(DATETIME, CAST(abn_dtfim AS VARCHAR (12)) + ' ' + CAST(abn_horafim / 60 AS VARCHAR(8)) + ':' + FORMAT(abn_horafim % 60, 'D2'), 113) AS datafim, 
                    aprgestor_user, 
                    justificativa_excecao, 
                    abn_horaini as horainicio, 
                    abn_horafim as horafim, 
                    abn_codabono as codabono, 
                    status, 
                    motivo_reprova,
                    movimento,
                    atitude_dt, atitude_ini, atitude_fim, atitude_justificativa, atitude_tipo
                FROM 
                    zcrmportal_ponto_horas
                WHERE 
                  coligada = '".$this->coligada."' 
                  AND chapa = '".$objDados['chapa']."' 
                  AND dtponto = '".$objDados['datainicio']."' 
                  AND movimento IN ".$MOVIMENTO." ";
      
   

      $result = $this->dbportal->query($query);
      return ($result->getNumRows() > 0) 
              ? $result->getResultArray() 
              : false;

    }



    public function DeletarAbonoLancado($Dados){

      try{

        $query = "DELETE FROM zcrmportal_ponto_horas WHERE id = '". $Dados['id'] . "'";
        $res = $this->dbportal->query($query);

        return ($res) 
          ? responseJson('success', 'Deletado com sucesso!')
          : responseJson('error', 'Erro ao deletar lançamento!');


      }catch(\Exception $e){
        return responseJson('error', 'Erro interno: '. $e);
      }
    }

    public function CadastrarAbonoAtraso($Dados, $Arquivos  = false){

      try{

        $sucesso = false;

       

        $DATA_PONTO = dtEn($Dados['abonoAtrasoData']);
        $TIPO = $Dados['abonoAtrasoAcao'];
        $CODFILIAL = $Dados['codfilial_lancamento'];
       

        if(!is_array($Dados)) responseJson('error', 'Arquivo enviado não está no formato de Array!');

        $CONTADOR = 0;
        $TOTAL_BATIDAS = count($Dados['chapa']);
        $UPLOAD_FILE = '';

        if (isset($Arquivos['arquivo']) && file_exists($Arquivos['arquivo']["tmp_name"])) {

          $arquivo = file_get_contents($Arquivos['arquivo']["tmp_name"]);
          $ARQUIVO = base64_encode($arquivo);
          $TIPO_ARQUIVO =  $Arquivos['arquivo']['type'];
          $NOME = $Arquivos['arquivo']['name'];
          
          $UPLOAD_FILE = $ARQUIVO . '|'. $TIPO_ARQUIVO . '|'. $NOME;


        }
        
        for($CONTADOR = 0; $CONTADOR < $TOTAL_BATIDAS; $CONTADOR++){

          $CHAPA = $Dados['chapa'][$CONTADOR];
          $DATA_INICIO = $Dados['data_inicio'][$CONTADOR];
          $DATA_FIM   = $Dados['datafim'][$CONTADOR];
          $INICIO = $Dados['inicio'][$CONTADOR];
          $FIM  = $Dados['fim'][$CONTADOR];
          $CODABONO = $Dados['codabono'][$CONTADOR];
          $JUSTIFICATIVA_FALTA = 'NULL';

          //LANÇAR ABONO
          if($TIPO == '1'){

            $MOVIMENTO = 5;

          //JUSTIFICATIVA
          }else if($TIPO == '2'){
            $JUSTIFICATIVA_FALTA = "'".$CODABONO."'";
            $CODABONO = 'JUST';
            $MOVIMENTO = 7;

          }

          $query = " INSERT INTO zcrmportal_ponto_horas 
          (dtponto,abn_dtini,abn_dtfim,abn_horaini,abn_horafim,abn_codabono, dtcadastro, chapa, coligada, status, movimento, usucad, justificativa_excecao, codfilial, id_hierarquia_gestor, aprgestor_data, aprgestor_user, gestor_dtapr, gestor_usuapr) VALUES 
          ('". $DATA_INICIO  ."','".$DATA_INICIO ."','".$DATA_FIM."','".h2m($INICIO)."','".h2m($FIM)."','".$CODABONO."', '".date('Y-m-d H:i:s')."','".$CHAPA."','".$this->coligada."', '2', ".$MOVIMENTO.", '".$this->log_id."', ".$JUSTIFICATIVA_FALTA.", '".$CODFILIAL."', NULL, NULL, NULL, NULL, NULL)
          ";

            $res = $this->dbportal->query($query);
            if($this->dbportal->affectedRows() > 0) $sucesso = true;

            if($this->dbportal->affectedRows() > 0){
              $insertID = $this->dbportal->insertID();
                if(self::isGestorOrLiderAprovador($CHAPA)){
                  $mAprova = model('Ponto/AprovaModel');
                  $mAprova->aprovaBatidaRH($insertID);
                }
            }

        }

        if(strlen(trim($CODABONO)) > 0 && strlen($UPLOAD_FILE) > 0 )
            
        $this->dbportal->query(" INSERT INTO zcrmportal_ponto_abono_atestado 
          (dtponto, arquivo, dtcad, usucad, chapa, coligada) VALUES
          ('".$DATA_INICIO."', '". $UPLOAD_FILE ."', '".date('Y-m-d H:i:s')."', '".$this->log_id."', '".$CHAPA."', '".$this->coligada."')
        ");

        return ($sucesso) 
              ? responseJson('success', 'Ação efetuada com sucesso!')
              : responseJson('error', 'Erro ao salvar os dados!');




      }catch(\Exception $e){

        return responseJson('error', 'Erro interno: '.$e);

      }


    }

    public function CadastrarAbonoFalta($Dados, $Arquivos = false){

      
      

    
      try{

        $sucesso = false;

       

        $DATA_PONTO = dtEn($Dados['abonoFaltaData']);
        $TIPO = $Dados['abonoFaltaAcao'];
        $CODFILIAL = $Dados['codfilial_lancamento'];
       

        if(!is_array($Dados)) responseJson('error', 'Arquivo enviado não está no formato de Array!');

        $CONTADOR = 0;
        $TOTAL_BATIDAS = count($Dados['chapa']);
        $UPLOAD_FILE = '';

        if (isset($Arquivos['arquivo']) && file_exists($Arquivos['arquivo']["tmp_name"])) {

          $arquivo = file_get_contents($Arquivos['arquivo']["tmp_name"]);
          $ARQUIVO = base64_encode($arquivo);
          $TIPO_ARQUIVO =  $Arquivos['arquivo']['type'];
          $NOME = $Arquivos['arquivo']['name'];
          
          $UPLOAD_FILE = $ARQUIVO . '|'. $TIPO_ARQUIVO . '|'. $NOME;


        }
        
        for($CONTADOR = 0; $CONTADOR < $TOTAL_BATIDAS; $CONTADOR++){

          $CHAPA = $Dados['chapa'][$CONTADOR];
          $DATA_INICIO = $Dados['data_inicio'][$CONTADOR];
          $DATA_FIM   = $Dados['datafim'][$CONTADOR];
          $INICIO = $Dados['inicio'][$CONTADOR];
          $FIM  = $Dados['fim'][$CONTADOR];
          $CODABONO = $Dados['codabono'][$CONTADOR];
          $JUSTIFICATIVA_FALTA = 'NULL';

          //LANÇAR ABONO
          if($TIPO == '1'){

            $MOVIMENTO = 6;

            $query = " INSERT INTO zcrmportal_ponto_horas 
            (dtponto,abn_dtini,abn_dtfim,abn_horaini,abn_horafim,abn_codabono, dtcadastro, chapa, coligada, status, movimento, usucad, justificativa_excecao, codfilial, id_hierarquia_gestor, aprgestor_data, aprgestor_user, gestor_dtapr, gestor_usuapr) VALUES 
            ('". $DATA_INICIO  ."','".$DATA_INICIO ."','".$DATA_FIM."','".h2m($INICIO)."','".h2m($FIM)."','".$CODABONO."', '".date('Y-m-d H:i:s')."','".$CHAPA."','".$this->coligada."', '1', ".$MOVIMENTO.", '".$this->log_id."', ".$JUSTIFICATIVA_FALTA.", '".$CODFILIAL."', NULL, NULL, NULL, NULL, NULL)
            ";

              // echo $query; exit();
              $res = $this->dbportal->query($query);
              if($this->dbportal->affectedRows() > 0) $sucesso = true;

              if($this->dbportal->affectedRows() > 0){
                $insertID = $this->dbportal->insertID();
                if(self::isGestorOrLiderAprovador($CHAPA)){
                  $mAprova = model('Ponto/AprovaModel');
                  $mAprova->aprovaBatidaRH($insertID);
                }
              }

          //JUSTIFICATIVA
          }else if($TIPO == '2'){
            $JUSTIFICATIVA_FALTA = "'".$CODABONO."'";
            $CODABONO = 'JUST';
            $MOVIMENTO = 7;

            $query = " INSERT INTO zcrmportal_ponto_horas 
            (dtponto,abn_dtini,abn_dtfim,abn_horaini,abn_horafim,abn_codabono, dtcadastro, chapa, coligada, status, movimento, usucad, justificativa_excecao, codfilial, id_hierarquia_gestor, aprgestor_data, aprgestor_user, gestor_dtapr, gestor_usuapr) VALUES 
            ('". $DATA_INICIO  ."','".$DATA_INICIO ."','".$DATA_FIM."','".h2m($INICIO)."','".h2m($FIM)."','".$CODABONO."', '".date('Y-m-d H:i:s')."','".$CHAPA."','".$this->coligada."', '1', ".$MOVIMENTO.", '".$this->log_id."', ".$JUSTIFICATIVA_FALTA.", '".$CODFILIAL."', NULL, NULL, NULL, NULL, NULL)
            ";

              // echo $query; exit();
              $res = $this->dbportal->query($query);
              if($this->dbportal->affectedRows() > 0) $sucesso = true;

              if($this->dbportal->affectedRows() > 0){
                $insertID = $this->dbportal->insertID();
                if(self::isGestorOrLiderAprovador($CHAPA)){
                  $mAprova = model('Ponto/AprovaModel');
                  $mAprova->aprovaBatidaRH($insertID);
                }
              }

          }else if($TIPO == '3'){
            // if((int)$CODABONO == 0) continue;
            $JUSTIFICATIVA_FALTA = "'".$CODABONO."'";
            //$CODABONO = 'JUST';
            $MOVIMENTO = 8;

            $this->dbportal->query(" DELETE FROM zcrmportal_ponto_horas WHERE movimento = '8' AND chapa = '".$CHAPA."' AND coligada = '".$this->coligada."' AND dtponto = '". $DATA_INICIO  ."' AND status NOT IN ('S')");

            $query = " INSERT INTO zcrmportal_ponto_horas 
            (dtponto,atitude_dt,atitude_ini,atitude_fim, dtcadastro, chapa, coligada, status, movimento, usucad, atitude_justificativa, atitude_tipo) VALUES 
            ('". $DATA_INICIO  ."','".$DATA_INICIO ."','".h2m($INICIO)."','".h2m($FIM)."', '".date('Y-m-d H:i:s')."','".$CHAPA."','".$this->coligada."', '1', ".$MOVIMENTO.", '".$this->log_id."', 'Altera Atitude', '{$CODABONO}')";

              // echo $query; exit();
              $res = $this->dbportal->query($query);
              if($this->dbportal->affectedRows() > 0) $sucesso = true;

              if($this->dbportal->affectedRows() > 0){
                $insertID = $this->dbportal->insertID();
                if(self::isGestorOrLiderAprovador($CHAPA)){
                  $mAprova = model('Ponto/AprovaModel');
                  $mAprova->aprovaBatidaRH($insertID);
                }
              }

          }

        }

        if(strlen(trim($CODABONO)) > 0 && strlen($UPLOAD_FILE) > 0 ){
            
        $query = " INSERT INTO zcrmportal_ponto_abono_atestado 
        (dtponto, arquivo, dtcad, usucad, chapa, coligada) VALUES
        ('".$DATA_INICIO."', '". $UPLOAD_FILE ."', '".date('Y-m-d H:i:s')."', '".$this->log_id."', '".$CHAPA."', '".$this->coligada."')
      ";
          // echo $query; exit();
            $this->dbportal->query($query);
        }

        return ($sucesso) 
              ? responseJson('success', 'Ação efetuada com sucesso!')
              : responseJson('error', 'Erro ao salvar os dados!');




      }catch(\Exception $e){

        return responseJson('error', 'Erro interno: '.$e);

      }
    }

    public function listaFuncionarios($request){

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
      $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

      if($request['rh']) $in_secao = "";

      $query = "SELECT A.CHAPA, A.NOME FROM PFUNC A WHERE A.CODCOLIGADA = '{$this->coligada}' {$in_secao} AND (A.DATADEMISSAO > (GETDATE() - 10) OR A.DATADEMISSAO IS NULL) ORDER BY A.NOME ";
      $result = $this->dbrm->query($query);

      return ($result)
        ? $result->getResultArray()
        : false;

    }

    public function isGestorOrLiderAprovador($chapaColaborador = false, $funcao = false)
    {
      //Funcao de aprovador do ponto
      $funcao = ($funcao == false) ? 181 : $funcao;

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
      $query = " SELECT * FROM zcrmportal_hierarquia_gestor_substituto A
                  LEFT JOIN zcrmportal_hierarquia_gestor_substituto_modulos B ON a.modulos LIKE '%\"' + CAST(B.id AS VARCHAR) + '\"%'
                  WHERE A.chapa_substituto = '{$chapa}' 
                    AND A.coligada = '{$this->coligada}' 
                    AND A.inativo = 0
                    AND B.funcoes like '%\"{$funcao}\"%'
      ";
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

    public function listaAbonoCritica($dataInicio, $dataTermino, $tipo)
    {

      $ft_tipo = ($tipo == 8) ? "8,7" : $tipo;

      if($dataInicio === NULL || $dataTermino === NULL) return false;

        $coligada   = session()->get("func_coligada");

        $query = " SELECT id, dtponto, abn_horaini, abn_horafim, abn_codabono, status, justificativa_abono_tipo, motivo_reprova, chapa, atitude_justificativa, abono_atestado FROM zcrmportal_ponto_horas WHERE coligada = '{$coligada}' AND movimento IN ({$ft_tipo}) AND dtponto BETWEEN '{$dataInicio}' AND '{$dataTermino}' AND status NOT IN ('S') AND usu_delete IS NULL ";
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function listaAtitudeFalta($request)
    {

      $in_tipo = ($request['tipo'] == 'F') ? "'F'" : "'A', 'SA'";

      $query = "
        SELECT 
          A.ATITUDE, 
          CONVERT(VARCHAR, A.DATAREFERENCIA, 103) DATA,
          dbo.MINTOTIME(SUM(DATEDIFF(MINUTE, A.INICIO, A.FIM))) TOTAL_HORAS
        FROM 
          AOCORRENCIACALCULADA A, 
          AJUSTFUN B
        WHERE 
            A.CODCOLIGADA = B.CODCOLIGADA
          AND A.CHAPA = B.CHAPA 
          AND CONVERT(DATETIME, A.INICIO, 113) = CONVERT(DATETIME, CAST(B.DATA AS VARCHAR (12)) + ' ' + CAST(B.INICIO / 60 AS VARCHAR(8)) + ':' + FORMAT(B.INICIO % 60, 'D2'), 113)
          AND A.CODCOLIGADA = '{$this->coligada}' 
          AND A.CHAPA = '{$request['chapa']}' 
          AND A.DATAREFERENCIA = '{$request['data']}' 
          AND A.TIPOOCORRENCIA IN ({$in_tipo})
          AND B.JUSTIFICATIVA IS NULL
        GROUP BY
          A.ATITUDE,
          A.DATAREFERENCIA
      ";
      $result = $this->dbrm->query($query);
      if(!$result) return false;
      return ($result->getNumRows() > 0) 
              ? $result->getResultArray() 
              : false;

    }

    public function alteraAtitude($request, $arquivos)
    {

        $request = json_decode($request['dados'][0], true);
        $request = $request[0];

        $possui_anexo = "NULL";
        $anexo_atitude = "NULL";
        if($request['tem_anexo'] == 1){

          if(strlen(trim($arquivos['anexo']['tmp_name'])) > 0){
            $file_nome = $arquivos['anexo']['name'];
            $file_size = $arquivos['anexo']['size'];
            $file_type = $arquivos['anexo']['type'];
            $file_file = file_get_contents($arquivos['anexo']['tmp_name']);

            $anexo_atitude = "'{$file_nome}|{$file_size}|{$file_type}|".base64_encode($file_file)."'";
            $possui_anexo = 1;
          }
        }

        $movimento = ($request['atitude'] == 2) ? 9 : 8;
        if($request['atitude'] == 3) $movimento = 7;

        if($movimento == 8 || $movimento == 7){

            $this->dbportal->query(" DELETE FROM zcrmportal_ponto_horas WHERE coligada = '{$this->coligada}' AND chapa = '{$request['chapa']}' AND dtponto = '{$request['data']}' AND movimento = '{$movimento}' AND status NOT IN ('S') ");

            $query = $this->dbportal->query(" INSERT INTO zcrmportal_ponto_horas 
                  (dtponto,atitude_dt,atitude_ini,atitude_fim, dtcadastro, chapa, coligada, status, movimento, usucad, atitude_justificativa, atitude_tipo, anexo_batida, possui_anexo) VALUES 
                  ('". $request['data']  ."','".$request['data'] ."','0','".h2m($request['horas'])."', '".date('Y-m-d H:i:s')."','".$request['chapa']."','".$this->coligada."', '1', '{$movimento}', '".$this->log_id."', '{$request['tipo']} - Altera Atitude', '{$request['atitude']}', {$anexo_atitude}, {$possui_anexo})");

            if($this->dbportal->affectedRows() > 0){

              $insertID = $this->dbportal->insertID();

              if($movimento != 7){

                  $tipo = ($request['tipo'] == 'F') ? 1 : 2;
                  $descricao = ($tipo == 1) ? 'FALTA CONFIRMADA' : 'ATRASO CONFIRMADO';

                  if($request['atitude'] == 0){
                    // falta confirmada
                    $this->dbportal->query(" DELETE FROM zcrmportal_ponto_justificativa_func WHERE chapa = '{$request['chapa']}' AND dtponto = '{$request['data']}' AND coligada = '{$this->coligada}' AND justificativa = (SELECT id FROM zcrmportal_ponto_motivos WHERE CAST(descricao AS VARCHAR) = 'BANCO DE HORAS' AND codcoligada = '{$this->coligada}' AND tipo = '{$tipo}') ");

                    $this->dbportal->query(" INSERT INTO zcrmportal_ponto_justificativa_func 
                      SELECT '{$request['data']}', '{$request['chapa']}', codcoligada, null, null, id, '{$this->log_id}', tipo FROM zcrmportal_ponto_motivos WHERE CAST(descricao AS VARCHAR) = '{$descricao}' AND codcoligada = '{$this->coligada}' AND tipo = '{$tipo}'
                    ");

                  }

                  if($request['atitude'] == 1){
                    // banco de horas
                    $this->dbportal->query(" DELETE FROM zcrmportal_ponto_justificativa_func WHERE chapa = '{$request['chapa']}' AND dtponto = '{$request['data']}' AND coligada = '{$this->coligada}' AND justificativa = (SELECT id FROM zcrmportal_ponto_motivos WHERE CAST(descricao AS VARCHAR) = '{$descricao}' AND codcoligada = '{$this->coligada}' AND tipo = '{$tipo}') ");
                    $this->dbportal->query(" INSERT INTO zcrmportal_ponto_justificativa_func 
                      SELECT '{$request['data']}', '{$request['chapa']}', codcoligada, null, null, id, '{$this->log_id}', tipo FROM zcrmportal_ponto_motivos WHERE CAST(descricao AS VARCHAR) = 'BANCO DE HORAS' AND codcoligada = '{$this->coligada}' AND tipo = '{$tipo}'
                    ");

                  }

              }
              
                if(self::isGestorOrLiderAprovador($request['chapa'])){
                  $mAprova = model('Ponto/AprovaModel');
                  $mAprova->aprovaBatidaRH($insertID);
                }
              
              return responseJson('success', 'Atitude alterada com sucesso.');
            }

            return responseJson('error', 'Falha ao alterar atitude.');
      }else{
        

        $codabono = (DBRM_BANCO == 'CorporeRMPRD') ? '031' : '030';

        $this->dbportal->query(" DELETE FROM zcrmportal_ponto_horas WHERE movimento = '9' AND chapa = '".$request['chapa']."' AND coligada = '".$this->coligada."' AND dtponto = '". $request['data']  ."' AND status NOT IN ('S')");

        // 031 codabono em produção
        $query = $this->dbportal->query(" INSERT INTO zcrmportal_ponto_horas 
          (dtponto,abn_dtini,abn_dtfim,abn_horaini,abn_horafim,abn_codabono, dtcadastro, chapa, coligada, status, movimento, usucad, justificativa_excecao, codfilial, id_hierarquia_gestor, aprgestor_data, aprgestor_user, gestor_dtapr, gestor_usuapr, abono_atestado, possui_anexo) VALUES 
          ('". $request['data']  ."','". $request['data']  ."','". $request['data']  ."',0,'".h2m($request['horas'])."','{$codabono}', '".date('Y-m-d H:i:s')."','".$request['chapa']."','".$this->coligada."', '1', '{$movimento}', '".$this->log_id."', 'Falta Não Remunerada', '1', NULL, NULL, NULL, NULL, NULL, {$anexo_atitude}, {$possui_anexo})
          ");

          if($this->dbportal->affectedRows() > 0){

            $insertID = $this->dbportal->insertID();

            if(self::isGestorOrLiderAprovador($request['chapa'])){
              $mAprova = model('Ponto/AprovaModel');
              $mAprova->aprovaBatidaRH($insertID);
            }

            return responseJson('success', 'Atitude alterada com sucesso.');

          }

          return responseJson('error', 'Falha ao alterar atitude.');

      }

    }
  
    
}

