<?php
namespace App\Models\Remuneracao;

use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class SimuladorModel extends Model {

    protected $dbportal;
    protected $dbrm;
    public $coligada;
    public $log_id;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->coligada = session()->get('func_coligada');
        $this->log_id   = session()->get('log_id');
    }

    // lista funcionário que pode visualizar
    public function ListarSimuladorFuncionarios(){

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();

        $isLider = $mHierarquia->isLider(util_chapa(session()->get('func_chapa'))['CHAPA'], session()->get('func_coligada'));
        $isBP = $mHierarquia->getSecaoPodeVerBP();

        $in_secao = " AND 1 = 2 ";
        if($Secoes){
            $in_secao = "";
            if($isLider && !$isBP){
                // lider
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
                //echo $in_secao;exit();
            }
        }

        $mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('REMUNERACAO_SIMULADOR_RH');
        if($perfil_rh) $in_secao = "";

        $query = "
            SELECT
                A.CODCOLIGADA,
                A.CHAPA,
                A.NOME,
                A.CODSECAO,
                B.DESCRICAO NOMESECAO,
                A.CODFUNCAO,
                C.NOME NOMEFUNCAO,
                A.CODHORARIO,
                D.DESCRICAO NOMEHORARIO,
                --(SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CHAPA = A.CHAPA AND CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = A.CODHORARIO) DTMUDANCA_HORARIO,
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
                ) DTMUDANCA_HORARIO,
                E.NOMEFANTASIA NOMEFILIAL,
                E.CODFILIAL
                
            FROM
                PFUNC A
                    INNER JOIN PSECAO B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODSECAO
                    INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
                    INNER JOIN AHORARIO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODHORARIO
                    INNER JOIN GFILIAL E ON E.CODCOLIGADA = A.CODCOLIGADA AND E.CODFILIAL = B.CODFILIAL
            WHERE
                A.CODSITUACAO IN ('A', 'F', 'E', 'V') 
                {$in_secao}
                AND A.CODCOLIGADA = {$this->coligada}
            ORDER BY
                A.NOME
        ";

        //echo '<pre>'.$query;exit();
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // lista posição que o gestor pode ver
    public function ListarPosicao(){

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        
        if($Secoes){
            $in_secao = "";
            foreach($Secoes as $key =>$CodSecao){
                $in_secao .= "'{$CodSecao['codsecao']}',";
            }
            $in_secao = " AND BP.codsecao IN (".rtrim($in_secao, ',').")";
        }else{
            $in_secao = " AND 1 = 2 ";
        }

        $mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('REMUNERACAO_SIMULADOR_RH');
        if($perfil_rh) $in_secao = "";

        $query = "
            SELECT
                CONCAT(BP.id, '.', AP.id) codposicao,
                AP.salario,
                BP.codsecao,
                FP.CODFILIAL codfilial,
                CP.codfuncao,
                DP.DESCRICAO nomesecao,
                EP.NOME nomefuncao,
                FP.NOME nomefilial,
                AP.nao_orcada,
                AP.chapa,
                DP.CODCOLIGADA,
                (
                    SELECT NOME FROM PFUNC (NOLOCK) WHERE CHAPA = AP.chapa COLLATE Latin1_General_CI_AS AND CODCOLIGADA = DP.CODCOLIGADA AND CODSECAO = DP.CODIGO
                ) NOMEFUNCIONARIO
            FROM
                ".DBPORTAL_BANCO."..zcrmportal_posicao_salario (NOLOCK) AP,
                ".DBPORTAL_BANCO."..zcrmportal_posicao (NOLOCK) BP,
                ".DBPORTAL_BANCO."..zcrmportal_posicao_funcao (NOLOCK) CP,
                PSECAO (NOLOCK) DP,
                PFUNCAO (NOLOCK) EP,
                GFILIAL (NOLOCK) FP
            WHERE
                    AP.coligada = {$this->coligada}
                AND AP.inativo = 0
                
                AND AP.id_posicao = BP.id
                
                AND CP.id_posicao = BP.id 
                AND CP.id = AP.id_posicao_funcao 
                AND CP.inativo = 0
                
                AND DP.CODCOLIGADA = BP.coligada 
                AND DP.CODIGO COLLATE Latin1_General_CI_AS = BP.codsecao 
                AND DP.SECAODESATIVADA = 0
                
                AND EP.CODCOLIGADA = BP.coligada 
                AND EP.CODIGO COLLATE Latin1_General_CI_AS = CP.codfuncao
                
                AND FP.CODCOLIGADA = EP.CODCOLIGADA
                
                AND FP.CODFILIAL = DP.CODFILIAL

                {$in_secao}
        ";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // lista posição que o gestor pode ver
    public function ListarPosicaoRequisicaoAQ(){

        $mAcesso = model('AcessoModel');
        $where_perfil = " AND R.usucad = '{$this->log_id}' ";
        $perfil_recrutamento = $mAcesso->VerificaPerfil('REMUNERACAO_SIMULADOR_RECRUTAMENTO');
        $perfil_global = $mAcesso->VerificaPerfil('GLOBAL_RH');
        if($perfil_recrutamento || $perfil_global) $where_perfil = "";
        

        $query = "
            SELECT 
                R.id id_requisicao,
                CONCAT(BP.id, '.', AP.id) codposicao,
                AP.salario,
                AP.range_salario,
                AP.salario_min,
                AP.salario_max,
                AP.excecao,
                BP.codsecao,
                CAST(SUBSTRING(BP.codsecao,1,3) AS INT) codfilial,
                CP.codfuncao,
                DP.DESCRICAO nomesecao,
                EP.NOME nomefuncao,
                AP.nao_orcada,
                AP.chapa
            FROM
                ".DBPORTAL_BANCO."..zcrmportal_posicao_salario AP
                INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao BP ON AP.id_posicao = BP.id 
                INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao_funcao CP ON CP.id_posicao = BP.id AND CP.id = AP.id_posicao_funcao 
                INNER JOIN PSECAO DP ON DP.CODCOLIGADA = BP.coligada AND DP.CODIGO COLLATE Latin1_General_CI_AS = BP.codsecao
                INNER JOIN PFUNCAO EP ON EP.CODCOLIGADA = BP.coligada AND EP.CODIGO COLLATE Latin1_General_CI_AS = CP.codfuncao
                INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_requisicao_aq R ON R.codposicao = CONCAT(BP.id, '.', AP.id) AND R.codposicao IS NOT NULL {$where_perfil} --AND R.usucad = '{$this->log_id}'
            WHERE
                    AP.coligada = '{$this->coligada}'
                    AND AP.inativo = 0

        ";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // executa simulador de calculo
    public function ExecutaSimuladorCalculo($acao, $chapa = null, $posicao_destino = null, $adicional = array()){

        $where_zmd = ($chapa == null) ? " CODSECAO = D2.codsecao COLLATE Latin1_General_CI_AS " : " CHAPA = '{$chapa}' ";
        $DEMAIS_CUSTOS = (strlen(trim($adicional['demaiscustos_manual'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['demaiscustos_manual'])) : 0.00);
        if($acao == "A"){
            $DEMAIS_CUSTOS = (strlen(trim($adicional['demaiscustos_manual_atual']) > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['demaiscustos_manual_atual'])) : 0.00);
        }
        $SALARIO = (strlen(trim($adicional['salario_manual'] ?? '') > 0 && $chapa == null) ? str_replace(',', '.', str_replace('.', '', $adicional['salario_manual'])) : " (CASE WHEN A.SALARIO IS NOT NULL THEN A.SALARIO ELSE C.salario END) ");
        $ADICIONAL_NOTURNO = (strlen(trim($adicional['adicional_noturno'] ?? '') > 0 && $chapa == null) ? str_replace(',', '.', str_replace('.', '', $adicional['adicional_noturno'])) : "
            ISNULL((
                SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '008'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");
        $HE50 = (strlen(trim($adicional['he50_manual'] ?? '') > 0 && $chapa == null) ? str_replace(',', '.', str_replace('.', '', $adicional['he50_manual'])) : "
            ISNULL((
                SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '002'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");
        $HE80 = (strlen(trim($adicional['he80_manual'] ?? '') > 0 && $chapa == null) ? str_replace(',', '.', str_replace('.', '', $adicional['he80_manual'])) : "
                ISNULL((
                    SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '003'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");
        $HE100 = (strlen(trim($adicional['he100_manual'] ?? '') > 0 && $chapa == null) ? str_replace(',', '.', str_replace('.', '', $adicional['he100_manual'])) : "
                ISNULL((
                    SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '004'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");

        // regra para zerar o DSR
        $MEDIA_DSR_HE = "
            ISNULL((
                SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '007'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ";
        if(strlen(trim($adicional['he50_manual'] ?? '') > 0) && strlen(trim($adicional['he80_manual'] ?? '') > 0) && strlen(trim($adicional['he100_manual'] ?? '') > 0) && $chapa == null){
            $_h50 = str_replace(',', '.', str_replace('.', '', $adicional['he50_manual']));
            $_h80 = str_replace(',', '.', str_replace('.', '', $adicional['he80_manual']));
            $_h100 = str_replace(',', '.', str_replace('.', '', $adicional['he100_manual']));

            if($_h50 <= 0 && $_h80 <= 0 && $_h100 <= 0){
                $MEDIA_DSR_HE = 0;
            }

        }

        $PREMIO_PRODUCAO = "
        ISNULL((
            SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '001'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
              ),0)
        ";

        if($chapa == null){
            if(strlen(trim($adicional['he_dsr'] ?? '')) > 0){
                $MEDIA_DSR_HE = (strlen(trim($adicional['he_dsr'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['he_dsr'])) : 0);
            }
            if(strlen(trim($adicional['premio_producao'] ?? '')) > 0){
                $PREMIO_PRODUCAO = str_replace(',', '.', str_replace('.', '', $adicional['premio_producao']));
            }
        }
        

        $query = "
        WITH PARAM AS (
            SELECT
            '{$this->coligada}'     CODCOLIGADA,
            '{$chapa}' 	            CHAPA,
            '{$posicao_destino}'	POSICAO_NOVA
        )

        SELECT 
			*,
            (TOTAL_REMUNERACAO * 1.3334) / 12 FERIAS,
			DBO.CALCULOIRRF((SELECT CODCOLIGADA FROM PARAM), EOMONTH (GETDATE()), TOTAL_REMUNERACAO - CALCULO_INSS, 0) CALCULO_IRRF,
			(TOTAL_REMUNERACAO - DBO.CALCULOIRRF((SELECT CODCOLIGADA FROM PARAM), EOMONTH (GETDATE()), TOTAL_REMUNERACAO - CALCULO_INSS, 0)) - CALCULO_INSS LIQUIDO_MENSAL,
            (PREMIO_PRODUCAO / 12) PREMIO_PRODUCAO_12
		FROM 
	    (

        SELECT 
            *,
            CASE WHEN CODCATEGORIA = 7 THEN 0.02 ELSE 0.08 END CODCATEGORIA_CALC,
            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) BASE_CALC,
            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 12 SALARIO_13,
            ((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12 FERIAS,
            CASE 
                WHEN CODCATEGORIA = 7 THEN 
                    (0.02 * 
                        (
                            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) * 1.3334) / 12)--salario_13
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12)--ferias
                        )
                    )
                    
                ELSE
                    (0.08 * 
                        (
                            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) * 1.3334) / 12)--salario_13
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12)--ferias
                        )
                    )
            END FGTS,
            CALC_INSS * 
                (
                    (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)
                    +
                    (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) * 1.3334) / 12)--salario_13
                    +
                    (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12)--ferias
                ) INSS,
            (SALARIO_MENSAL * 0.06) TRANSPORTE,
            (SALARIO_MENSAL * (CALC_PREVIDENCIA_PRIVADA / 100)) PREVIDENCIA_PRIVADA,
            --(SALARIO_MENSAL * CALC_SEGURO_VIDA) SEGURO_VIDA,
            CALC_SEGURO_VIDA SEGURO_VIDA,
            PP_IND_CALC / 12 PP_IND,
            --((SALARIO_MENSAL) * (PP_IND_CALC)) / 12 PP_IND,
            ((SALARIO_MENSAL) * (RVD_CALC)) / 12 RVD,
            ((SALARIO_MENSAL) * (PPR_CALC)) / 12 PPR,
            ((SALARIO_MENSAL) * (PRV_CALC)) / 12 PRV,
            ((SALARIO_MENSAL) * (SUPERACAO_CALC)) / 12 SUPERACAO,

            

            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + ADIC_ASSIDUIDADE + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) TOTAL_REMUNERACAO,
            dbo.CALCULOINSS((SELECT CODCOLIGADA FROM PARAM), EOMONTH (GETDATE()), (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + ADIC_ASSIDUIDADE + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)) CALCULO_INSS
            
        FROM (
        
        SELECT
            ".$DEMAIS_CUSTOS." DEMAIS_CUSTOS,

            ".$SALARIO." SALARIO_MENSAL,
            ISNULL(E.VALORFIXO,0) ADIC_TRITREM,
            
            -- periculosidade
            (
            SELECT 
                CASE
                    WHEN ISNULL(COUNT(*),0) <> 0 THEN ".$SALARIO." * 0.30
                    ELSE 0 
                END
            FROM 
                ".DBPORTAL_BANCO."..ZMDPERICULOSIDADE
            WHERE
                    coligada = CAST(P.CODCOLIGADA AS INT)
                AND codsecao = D2.codsecao
            ) PERICULOSIDADE,
             
            --  Prêmio Produção (R$) - Mensal | 3671,700188
            ".$PREMIO_PRODUCAO." PREMIO_PRODUCAO,
                
                
            --  Média Horas Extras 50% - Horas
            ".$HE50." MEDIA_HORA_EXTRA_50,
              
             --  Média Horas Extras 80% - Horas
            ".$HE80." MEDIA_HORA_EXTRA_80,
              
             --  Média Horas Extras 100% - Horas
            ".$HE100." MEDIA_HORA_EXTRA_100,
              
             --  Nona Hora - Horas
            ISNULL((
                SELECT 
                CASE 
                    WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                    ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                END FROM (
            SELECT 
                SUM(PF.VALOR) VALOR,
                COUNT(DISTINCT PF.CHAPA) QTDE,
                ZM1.TIPO 
            FROM 
                PFFINANC (NOLOCK) PF
                LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '005'
                LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
            WHERE 
                PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                AND PF.CODEVENTO = ZM2.CODEVENTO
                AND EXISTS(
                        SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                    )
                AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                AND PF.NROPERIODO IN (4,5,6,2)
            GROUP BY ZM1.TIPO)X
              ),0) MEDIA_NONA_HORA,
              
             --  Hora em Espera - Horas
            ISNULL((
                SELECT 
                CASE 
                    WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                    ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                END FROM (
            SELECT 
                SUM(PF.VALOR) VALOR,
                COUNT(DISTINCT PF.CHAPA) QTDE,
                ZM1.TIPO 
            FROM 
                PFFINANC (NOLOCK) PF
                LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '006'
                LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
            WHERE 
                PF.CHAPA IN (SELECT CHAPA FROM PFUNC (NOLOCK) WHERE {$where_zmd} AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                AND PF.CODEVENTO = ZM2.CODEVENTO
                AND EXISTS(
                        SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                    )
                AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                AND PF.NROPERIODO IN (4,5,6,2)
            GROUP BY ZM1.TIPO)X
              ),0) MEDIA_ESPERA_HORA,
              
             --  DSR sobre HE - % Médio
            ".$MEDIA_DSR_HE." MEDIA_DSR_HE,
              
             --  Adicional Noturno - Horas
            ".$ADICIONAL_NOTURNO." MEDIA_ADIC_NOTURNO,
                
            ISNULL(F.VALORFIXO,0) ADIC_ASSIDUIDADE,
            
            -- Férias (1/3) - 30 Dias Fixos por Ano
            --(CASE WHEN A.SALARIO IS NOT NULL THEN A.SALARIO ELSE C.salario END / 3) FERIAS_OLD,
            
            -- 13º Salário
            --(CASE WHEN A.SALARIO IS NOT NULL THEN A.SALARIO ELSE C.salario END) SALARIO_13,
            
            -- PPR
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '1'
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) PPR_CALC,

            -- PRV
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '2'
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) PRV_CALC,

            -- SUPERAÇÃO
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '4'
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) SUPERACAO_CALC,

            -- RVD
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '3'
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) RVD_CALC,

            -- VA
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = P.CODCOLIGADA AND GG.BENEFICIO = 'VA' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL
                    
                WHERE
                        AA.coligada = 1
                    AND AA.descricao = 'Nível'
            ),0) VA,

            -- VR
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = P.CODCOLIGADA AND GG.BENEFICIO = 'VR' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL
                    
                WHERE
                        AA.coligada = 1
                    AND AA.descricao = 'Nível'
            ),0) VR,

            -- VAHI
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = P.CODCOLIGADA AND GG.BENEFICIO = 'VA HI' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL AND SUBSTRING(FF.CODIGO,5,5) = GG.CCUSTO
                    
                WHERE
                        AA.coligada = 1
                    AND AA.descricao = 'Nível'
            ),0) VAHI,

            -- VANATAL
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = P.CODCOLIGADA AND GG.BENEFICIO = 'VA ADC NATAL' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL
                    
                WHERE
                        AA.coligada = 1
                    AND AA.descricao = 'Nível'
            ),0) VANATAL,

            -- PP IND
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '5'
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) PP_IND_CALC,

            -- ASSISTENCIA MÉDICA
            ISNULL((
            	SELECT
                    GG.VLEMPRESA * 
                    ISNULL((SELECT SUM(1) FROM PFDEPEND WHERE INCASSISTMEDICA = 1 AND CHAPA = P.CHAPA AND CODCOLIGADA = P.CODCOLIGADA),1)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDASSISMEDICA GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.FUNCAO = D.codfuncao COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) PLANO_SAUDE,
            ISNULL((
            	SELECT
					SUM(GG.VLCOLABORADOR)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDASSISMEDICA GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.FUNCAO = D.codfuncao COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) PLANO_SAUDE_VALOR_UNITARIO,
            ISNULL((
            	SELECT
					MAX(GG.OPERADORA)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = P.CODCOLIGADA AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDASSISMEDICA GG ON GG.COLIGADA = P.CODCOLIGADA AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.FUNCAO = D.codfuncao COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),'SEM OPERADORA') PLANO_SAUDE_OPERADORA,

            -- PREVIDENCIA PRIVADA
            ISNULL((
            	SELECT
					CAST(REPLACE(FF.CONTRMIN, '%', '') AS NUMERIC)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
					INNER JOIN ZMDPREVIDENCIAPRIVADA FF ON FF.COLIGADA = P.CODCOLIGADA AND SUBSTRING(FF.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) CALC_PREVIDENCIA_PRIVADA,

            -- SEGURO DE VIDA
            ISNULL((
            	SELECT
                (((".$SALARIO." * FF.CAPITALSEGURADO) * (CAST(REPLACE((REPLACE(FF.COEFICIENTE, '%', '')), ',', '.') AS NUMERIC(15,10))) / 100)) * (CAST(CAST(REPLACE((REPLACE(FF.CUSTOEMPRESA, '%', '')), ',', '.') AS NUMERIC(15,10)) AS NUMERIC) / 100)


				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = 1
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
					INNER JOIN ZMDSEGUROVIDA FF ON SUBSTRING(FF.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS
					
				WHERE
						AA.coligada = 1
					AND AA.descricao = 'Nível'
            ),0) CALC_SEGURO_VIDA,

            D3.CODIGO CODSECAO,
            D3.DESCRICAO NOMESECAO,
            D4.CODIGO CODFUNCAO,
            D4.NOME NOMEFUNCAO,
            D5.NOMEFANTASIA NOMEFILIAL,
            D5.CODFILIAL,

            -- FGTS
            A.CODCATEGORIA,
            
            -- INSS
            ISNULL((((CAST(REPLACE(D3.SAT, ',', '.') AS NUMERIC(15,10)) * D6.VALOR) + D3.PERCENTTERCEIROS + 20) / 100),0) CALC_INSS,

            D7.DESCRICAO PREVIDENCIA_PFCOMPL
            
            
        FROM
            PARAM P
            
            -- SALARIO MENSAL
            LEFT JOIN PFUNC A ON A.CHAPA = P.CHAPA AND A.CODCOLIGADA = P.CODCOLIGADA
            LEFT JOIN PFCOMPL B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao_salario C ON CONCAT(C.id_posicao, '.', C.id) = CASE WHEN B.POSICAO IS NOT NULL THEN B.POSICAO ELSE P.POSICAO_NOVA END AND C.inativo = 0
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao_funcao D ON D.id_posicao = C.id_posicao AND D.id = C.id_posicao_funcao AND D.inativo = 0
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao D2 ON D2.coligada = P.CODCOLIGADA AND D2.id = C.id_posicao
            LEFT JOIN PSECAO D3 ON D3.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS AND D3.CODCOLIGADA = P.CODCOLIGADA AND D3.SECAODESATIVADA = 0 
            LEFT JOIN PFUNCAO D4 ON D4.CODCOLIGADA = P.CODCOLIGADA AND D4.CODIGO = D.codfuncao COLLATE Latin1_General_CI_AS AND D4.INATIVA = 0
            LEFT JOIN GFILIAL D5 ON D5.CODCOLIGADA = P.CODCOLIGADA AND D5.CODFILIAL = D3.CODFILIAL
            LEFT JOIN PSECAOFAPALIQUOTA D6 ON D6.CODSECAO = SUBSTRING(D3.CODIGO, 1, 3) AND D6.CODCOLIGADA = P.CODCOLIGADA AND YEAR(D6.FINALVIGENCIAALIQUOTA) = YEAR(GETDATE())
            LEFT JOIN GCONSIST D7 ON D7.CODTABELA = 'PERPREV' AND D7.CODCLIENTE = B.PERCENTUAL
            
            -- ADICIONAL TRITREM
            LEFT JOIN ZMDADICIONALSALARIAL E ON E.COLIGADA = P.CODCOLIGADA AND SUBSTRING(E.FUNCAO, 1, 5) = D.codfuncao COLLATE Latin1_General_CI_AS AND E.PROGRAMA = 'TRITREM'
            
            -- PERICULOSIDADE
            
            -- ADICIONAL ASSIDUIDADE
            LEFT JOIN ZMDADICIONALSALARIAL F ON F.COLIGADA = P.CODCOLIGADA AND SUBSTRING(F.FUNCAO, 1, 5) = D.codfuncao COLLATE Latin1_General_CI_AS AND F.PROGRAMA = 'ASSIDUIDADE'

        )X
        )Y
        ";
        ///if($_SESSION['log_id'] == 67) echo '<br><br><br><textarea>'.$query.'</textarea><br>';exit();
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
        
    }

    // executa simulador de calculo
    public function ExecutaSimuladorCalculoSimulacao($acao, $filial, $secao, $funcao, $adicional = array()){

        $DEMAIS_CUSTOS = (strlen(trim($adicional['demaiscustos_manual'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['demaiscustos_manual'])) : 0.00);
        $SALARIO = (strlen(trim($adicional['salario_manual'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['salario_manual'])) : " 
            CASE 
            
            WHEN (
                SELECT DISTINCT isonomia FROM ".DBPORTAL_BANCO."..zcrmportal_salario_funcao AS SP WHERE SP.codigo = '{$funcao}' AND SP.coligada = {$this->coligada} AND (SP.inativo IS NULL OR SP.inativo = 0)
            ) = 'nao' THEN 0.00
            WHEN (CASE 
                        WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODSECAO = '{$secao}' GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN 
                            (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODSECAO = '{$secao}' GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                        
                        
                        WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE  CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND SUBSTRING(CODSECAO, 5, 5) = SUBSTRING('{$secao}', 5, 5) GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN
                            (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND SUBSTRING(CODSECAO, 5, 5) = SUBSTRING('{$secao}', 5, 5) GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                            
                        WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODFILIAL = D5.CODFILIAL GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN
                            (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODFILIAL = D5.CODFILIAL GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                    END) IS NULL THEN (CASE WHEN A.SALARIO IS NOT NULL THEN A.SALARIO ELSE C.salario END) ELSE
                        
                    (CASE 
                    WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODSECAO = '{$secao}' GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN 
                        (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODSECAO = '{$secao}' GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                    
                    
                    WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE  CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND SUBSTRING(CODSECAO, 5, 5) = SUBSTRING('{$secao}', 5, 5) GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN
                        (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND SUBSTRING(CODSECAO, 5, 5) = SUBSTRING('{$secao}', 5, 5) GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                        
                    WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODFILIAL = D5.CODFILIAL GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN
                        (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODFILIAL = D5.CODFILIAL GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                END)

            END
        ");
        $ADICIONAL_NOTURNO = (strlen(trim($adicional['adicional_noturno'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['adicional_noturno'])) : "
            ISNULL((
                SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '008'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");
        $HE50 = (strlen(trim($adicional['he50_manual'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['he50_manual'])) : "
            ISNULL((
                SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '002'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");
        $HE80 = (strlen(trim($adicional['he80_manual'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['he80_manual'])) : "
                ISNULL((
                    SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '003'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");
        $HE100 = (strlen(trim($adicional['he100_manual'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['he100_manual'])) : "
                ISNULL((
                    SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '004'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ");

        // regra para zerar o DSR
        $MEDIA_DSR_HE = "
            ISNULL((
                SELECT 
                    CASE 
                        WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                        ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                    END FROM (
                SELECT 
                    SUM(PF.VALOR) VALOR,
                    COUNT(DISTINCT PF.CHAPA) QTDE,
                    ZM1.TIPO 
                FROM 
                    PFFINANC (NOLOCK) PF
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '007'
                    LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                WHERE 
                    PF.CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                    AND PF.CODEVENTO = ZM2.CODEVENTO
                    AND EXISTS(
                            SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                        )
                    AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                    AND PF.NROPERIODO IN (4,5,6,2)
                GROUP BY ZM1.TIPO)X
                ),0)
        ";
        if(strlen(trim($adicional['he50_manual'] ?? '') > 0) && strlen(trim($adicional['he80_manual'] ?? '') > 0) && strlen(trim($adicional['he100_manual'] ?? '') > 0)){
            $_h50 = str_replace(',', '.', str_replace('.', '', $adicional['he50_manual']));
            $_h80 = str_replace(',', '.', str_replace('.', '', $adicional['he80_manual']));
            $_h100 = str_replace(',', '.', str_replace('.', '', $adicional['he100_manual']));

            if($_h50 <= 0 && $_h80 <= 0 && $_h100 <= 0){
                $MEDIA_DSR_HE = 0;
            }

        }

        $PREMIO_PRODUCAO = "
        ISNULL((
            SELECT 
            	CASE 
            		WHEN TIPO = '02' THEN VALOR / QTDE
            		WHEN TIPO = '03' THEN VALOR / QTDE
            		ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
            	END FROM (
            SELECT
                SUM(PF.VALOR) VALOR,
                COUNT(PF.CHAPA) QTDE,
                ZM1.TIPO
                
            FROM 
                PFFINANC PF (NOLOCK)
                LEFT JOIN ZMDCONFSIMULACAOPORTAL ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '001'
    			LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
                
            WHERE
                    CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                AND PF.CODEVENTO = ZM2.CODEVENTO
                AND PF.NROPERIODO IN (4,5,6,2)
                --THEN 30 ELSE PF.REF END
                AND (SELECT REF FROM PFFINANC M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002') = 30
              GROUP BY ZM1.TIPO)X
              ),0)
        ";

        if(strlen(trim($adicional['he_dsr'] ?? '')) > 0){
            $MEDIA_DSR_HE = (strlen(trim($adicional['he_dsr'] ?? '') > 0) ? str_replace(',', '.', str_replace('.', '', $adicional['he_dsr'])) : 0);
        }
        if(strlen(trim($adicional['premio_producao'] ?? '')) > 0){
            $PREMIO_PRODUCAO = str_replace(',', '.', str_replace('.', '', $adicional['premio_producao']));
        }

        $query = "
        WITH PARAM AS (
            SELECT
            '{$this->coligada}'     CODCOLIGADA,
            '' 	            CHAPA,
            ''	POSICAO_NOVA
        )

        SELECT 
            DISTINCT
			*,
            (TOTAL_REMUNERACAO * 1.3334) / 12 FERIAS,
			DBO.CALCULOIRRF((SELECT CODCOLIGADA FROM PARAM), EOMONTH (GETDATE()), TOTAL_REMUNERACAO - CALCULO_INSS, 0) CALCULO_IRRF,
			(TOTAL_REMUNERACAO - DBO.CALCULOIRRF((SELECT CODCOLIGADA FROM PARAM), EOMONTH (GETDATE()), TOTAL_REMUNERACAO - CALCULO_INSS, 0)) - CALCULO_INSS LIQUIDO_MENSAL,
            (PREMIO_PRODUCAO / 12) PREMIO_PRODUCAO_12
		FROM 
	    (

        SELECT 
            *,
            CASE WHEN CODCATEGORIA = 7 THEN 0.02 ELSE 0.08 END CODCATEGORIA_CALC,
            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) BASE_CALC,
            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 12 SALARIO_13,
            ((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12 FERIAS,
            CASE 
                WHEN CODCATEGORIA = 7 THEN 
                    (0.02 * 
                        (
                            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)  * 1.3334) / 12)--salario_13
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12)--ferias
                        )
                    )
                    
                ELSE
                    (0.08 * 
                        (
                            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) * 1.3334) / 12)--salario_13
                            +
                            (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12)--ferias
                        )
                    )
            END FGTS,
            CALC_INSS * 
                (
                    (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)
                    +
                    (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) * 1.3334) / 12)--salario_13
                    +
                    (((SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) / 3) / 12)--ferias
                ) INSS,
            (SALARIO_MENSAL * 0.06) TRANSPORTE,
            (SALARIO_MENSAL * (CALC_PREVIDENCIA_PRIVADA / 100)) PREVIDENCIA_PRIVADA,
            --(SALARIO_MENSAL * CALC_SEGURO_VIDA) SEGURO_VIDA,
            CALC_SEGURO_VIDA SEGURO_VIDA,
            PP_IND_CALC / 12 PP_IND,
            --((SALARIO_MENSAL) * (PP_IND_CALC)) / 12 PP_IND,
            ((SALARIO_MENSAL) * (RVD_CALC)) / 12 RVD,
            ((SALARIO_MENSAL) * (PPR_CALC)) / 12 PPR,
            ((SALARIO_MENSAL) * (PRV_CALC)) / 12 PRV,
            ((SALARIO_MENSAL) * (SUPERACAO_CALC)) / 12 SUPERACAO,

            

            (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + ADIC_ASSIDUIDADE + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO) TOTAL_REMUNERACAO,
            dbo.CALCULOINSS((SELECT CODCOLIGADA FROM PARAM), EOMONTH (GETDATE()), (SALARIO_MENSAL + ADIC_TRITREM + PERICULOSIDADE + PREMIO_PRODUCAO + ADIC_ASSIDUIDADE + MEDIA_HORA_EXTRA_50 + MEDIA_HORA_EXTRA_80 + MEDIA_HORA_EXTRA_100 + MEDIA_NONA_HORA + MEDIA_ESPERA_HORA + MEDIA_DSR_HE + MEDIA_ADIC_NOTURNO)) CALCULO_INSS
        FROM (
        
        SELECT
            ".$DEMAIS_CUSTOS." DEMAIS_CUSTOS,
            
            
            ".$SALARIO." SALARIO_MENSAL,
            ISNULL(E.VALORFIXO,0) ADIC_TRITREM,
            -- periculosidade
            (
            SELECT 
                CASE
                    WHEN ISNULL(COUNT(*),0) <> 0 THEN ".$SALARIO." * 0.30
                    ELSE 0 
                END
            FROM 
                ".DBPORTAL_BANCO."..ZMDPERICULOSIDADE
            WHERE
                    coligada = CAST(P.CODCOLIGADA AS INT)
                AND codsecao = D2.codsecao
            ) PERICULOSIDADE,
             
             --  Prêmio Produção (R$) - Mensal | 3671,700188
            ".$PREMIO_PRODUCAO." PREMIO_PRODUCAO,
                
                
              --  Média Horas Extras 50% - Horas
              ".$HE50." MEDIA_HORA_EXTRA_50,
                
               --  Média Horas Extras 80% - Horas
              ".$HE80." MEDIA_HORA_EXTRA_80,
                
               --  Média Horas Extras 100% - Horas
              ".$HE100." MEDIA_HORA_EXTRA_100,
              
             --  Nona Hora - Horas
            ISNULL((
                SELECT 
                CASE 
                    WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                    ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                END FROM (
            SELECT 
                SUM(PF.VALOR) VALOR,
                COUNT(DISTINCT PF.CHAPA) QTDE,
                ZM1.TIPO 
            FROM 
                PFFINANC (NOLOCK) PF
                LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '005'
                LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
            WHERE 
                PF.CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                AND PF.CODEVENTO = ZM2.CODEVENTO
                AND EXISTS(
                        SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                    )
                AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                AND PF.NROPERIODO IN (4,5,6,2)
            GROUP BY ZM1.TIPO)X
              ),0) MEDIA_NONA_HORA,
              
             --  Hora em Espera - Horas
            ISNULL((
                SELECT 
                CASE 
                    WHEN TIPO = '02' OR TIPO = '03' THEN VALOR / QTDE
                    ELSE VALOR / (CASE WHEN P.CHAPA = '' THEN (6*QTDE) ELSE (6*QTDE) END)
                END FROM (
            SELECT 
                SUM(PF.VALOR) VALOR,
                COUNT(DISTINCT PF.CHAPA) QTDE,
                ZM1.TIPO 
            FROM 
                PFFINANC (NOLOCK) PF
                LEFT JOIN ZMDCONFSIMULACAOPORTAL (NOLOCK) ZM1 ON ZM1.CODCOLIGADA = PF.CODCOLIGADA AND ZM1.CODIGO = '006'
                LEFT JOIN ZMDCONFSIMULACAOPORTAL_EV (NOLOCK) ZM2 ON ZM2.CODCOLIGADA = ZM1.CODCOLIGADA AND ZM2.CODIGO = ZM1.CODIGO
            WHERE 
                PF.CHAPA IN (SELECT CHAPA FROM PFUNC WHERE CODSECAO = '{$secao}' AND CODCOLIGADA = P.CODCOLIGADA AND CODSITUACAO <> 'D')
                AND PF.CODEVENTO = ZM2.CODEVENTO
                AND EXISTS(
                        SELECT REF FROM PFFINANC (NOLOCK) M WHERE PF.CODCOLIGADA = M.CODCOLIGADA AND PF.CHAPA = M.CHAPA AND PF.ANOCOMP = M.ANOCOMP AND PF.MESCOMP = M.MESCOMP AND M.CODEVENTO = '0002' AND M.REF = 30
                    )
                AND CONCAT(PF.ANOCOMP, REPLICATE('0', 2 - LEN(PF.MESCOMP)) + RTRIM(PF.MESCOMP)) >= SUBSTRING(CONVERT(VARCHAR, DATEADD(MONTH, -ZM1.QTDMESES, GETDATE()), 112),1,6)
                AND PF.NROPERIODO IN (4,5,6,2)
            GROUP BY ZM1.TIPO)X
              ),0) MEDIA_ESPERA_HORA,
              
             --  DSR sobre HE - % Médio
            ".$MEDIA_DSR_HE." MEDIA_DSR_HE,
              
             --  Adicional Noturno - Horas
            ".$ADICIONAL_NOTURNO." MEDIA_ADIC_NOTURNO,
                
            ISNULL(F.VALORFIXO,0) ADIC_ASSIDUIDADE,
            
            -- Férias (1/3) - 30 Dias Fixos por Ano
            --(CASE WHEN A.SALARIO IS NOT NULL THEN A.SALARIO ELSE C.salario END / 3) FERIAS_OLD,
            
            -- 13º Salário
            --(CASE WHEN A.SALARIO IS NOT NULL THEN A.SALARIO ELSE C.salario END) SALARIO_13,
            
            -- PPR
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '1'
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) PPR_CALC,

            -- PRV
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '2'
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) PRV_CALC,

            -- SUPERAÇÃO
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '4'
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) SUPERACAO_CALC,

            -- RVD
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '3'
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) RVD_CALC,

            -- VA
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = AA.coligada AND GG.BENEFICIO = 'VA' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL
                    
                WHERE
                        AA.coligada = CAST(P.CODCOLIGADA AS INT)
                    AND AA.descricao = 'Nível'
            ),0) VA,

            -- VR
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = AA.coligada AND GG.BENEFICIO = 'VR' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL
                    
                WHERE
                        AA.coligada = CAST(P.CODCOLIGADA AS INT)
                    AND AA.descricao = 'Nível'
            ),0) VR,

            -- VAHI
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = AA.coligada AND GG.BENEFICIO = 'VA HI' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL AND SUBSTRING(FF.CODIGO,5,5) = GG.CCUSTO
                    
                WHERE
                        AA.coligada = CAST(P.CODCOLIGADA AS INT)
                    AND AA.descricao = 'Nível'
            ),0) VAHI,

            -- VANATAL
            ISNULL((
                SELECT
                    ISNULL(MAX(GG.VALOR),0)
                FROM
                    ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
                    INNER JOIN ZMDBENEFICIOS GG ON GG.COLIGADA = AA.coligada AND GG.BENEFICIO = 'VA ADC NATAL' AND (GG.CODFUNCAO = BB.codigo OR GG.CODFUNCAO IS NULL) AND GG.FILIAL = FF.CODFILIAL
                    
                WHERE
                        AA.coligada = CAST(P.CODCOLIGADA AS INT)
                    AND AA.descricao = 'Nível'
            ),0) VANATAL,

            -- PP IND
            ISNULL((
            	SELECT
					MAX(GG.MULTIPLO)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDPROGRAMASREMUNERACAO GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL AND GG.PROGRAMA = '5'
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) PP_IND_CALC,

            -- ASSISTENCIA MÉDICA
            ISNULL((
            	SELECT
                    (SUM(GG.VLEMPRESA)*1)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDASSISMEDICA GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.FUNCAO = D.codfuncao COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) PLANO_SAUDE,
            ISNULL((
            	SELECT
				SUM(GG.VLCOLABORADOR)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = '{$secao}' COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDASSISMEDICA GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.FUNCAO = D.codfuncao COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) PLANO_SAUDE_VALOR_UNITARIO,
            ISNULL((
            	SELECT
					MAX(GG.OPERADORA)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = D.codfuncao AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
                    INNER JOIN PSECAO FF ON FF.CODCOLIGADA = AA.coligada AND FF.CODIGO = D2.codsecao COLLATE Latin1_General_CI_AS
					INNER JOIN ZMDASSISMEDICA GG ON GG.COLIGADA = AA.coligada AND SUBSTRING(GG.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS AND GG.FUNCAO = D.codfuncao COLLATE Latin1_General_CI_AS AND GG.CODFILIAL = FF.CODFILIAL
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),'SEM OPERADORA') PLANO_SAUDE_OPERADORA,

            -- PREVIDENCIA PRIVADA
            ISNULL((
            	SELECT
					CAST(REPLACE(FF.CONTRMIN, '%', '') AS NUMERIC)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
					INNER JOIN ZMDPREVIDENCIAPRIVADA FF ON FF.COLIGADA = AA.coligada AND SUBSTRING(FF.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) CALC_PREVIDENCIA_PRIVADA,

            -- SEGURO DE VIDA
            ISNULL((
            	SELECT
                --CAST(REPLACE((REPLACE(FF.COEFICIENTE, '%', '')), ',', '.') AS NUMERIC(15,10))
                (((".$SALARIO." * FF.CAPITALSEGURADO) * (CAST(REPLACE((REPLACE(FF.COEFICIENTE, '%', '')), ',', '.') AS NUMERIC(15,10))) / 100)) * (CAST(CAST(REPLACE((REPLACE(FF.CUSTOEMPRESA, '%', '')), ',', '.') AS NUMERIC(15,10)) AS NUMERIC) / 100)
				FROM
					".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = '{$funcao}' AND BB.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
					INNER JOIN ZMDSEGUROVIDA FF ON SUBSTRING(FF.GRUPOFUNCAO,1,1) = SUBSTRING(EE.descricao,1,1) COLLATE Latin1_General_CI_AS
					
				WHERE
						AA.coligada = CAST(P.CODCOLIGADA AS INT)
					AND AA.descricao = 'Nível'
            ),0) CALC_SEGURO_VIDA,

            D3.CODIGO CODSECAO,
            D3.DESCRICAO NOMESECAO,
            D4.CODIGO CODFUNCAO,
            D4.NOME NOMEFUNCAO,
            D5.NOMEFANTASIA NOMEFILIAL,
            D5.CODFILIAL,

            -- FGTS
            A.CODCATEGORIA,
            
            -- INSS
            ISNULL((((CAST(REPLACE(D3.SAT, ',', '.') AS NUMERIC(15,10)) * D6.VALOR) + D3.PERCENTTERCEIROS + 20) / 100),0) CALC_INSS,

            D7.DESCRICAO PREVIDENCIA_PFCOMPL,

            (CASE 
                WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODSECAO = '{$secao}' GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN 
                    (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODSECAO = '{$secao}' GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                
                
                WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE  CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND SUBSTRING(CODSECAO, 5, 5) = SUBSTRING('{$secao}', 5, 5) GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN
                    (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND SUBSTRING(CODSECAO, 5, 5) = SUBSTRING('{$secao}', 5, 5) GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
                    
                WHEN (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODFILIAL = D5.CODFILIAL GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC) IS NOT NULL THEN
                    (SELECT TOP 1 SALARIO FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODCOLIGADA = P.CODCOLIGADA AND CODFUNCAO = '{$funcao}' AND CODFILIAL = D5.CODFILIAL GROUP BY SALARIO ORDER BY COUNT(SALARIO) DESC)
            END) AS salario_isonomia,
            
            (
                SELECT DISTINCT isonomia FROM ".DBPORTAL_BANCO."..zcrmportal_salario_funcao AS SP WHERE SP.codigo = '{$funcao}' AND SP.coligada = {$this->coligada} AND (SP.inativo IS NULL OR SP.inativo = 0)
            ) isonomia
            
            
        FROM
            PARAM P
            
            -- SALARIO MENSAL
            LEFT JOIN PFUNC A ON A.CHAPA = P.CHAPA AND A.CODCOLIGADA = P.CODCOLIGADA
            LEFT JOIN PFCOMPL B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
            
            /*
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao_salario C ON CONCAT(C.id_posicao, '.', C.id) = CASE WHEN B.POSICAO IS NOT NULL THEN B.POSICAO ELSE P.POSICAO_NOVA END
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao_funcao D ON D.id_posicao = C.id_posicao AND D.id = C.id_posicao_funcao
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao D2 ON D2.coligada = P.CODCOLIGADA AND D2.id = C.id_posicao
            */

            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao D2 ON D2.coligada = P.CODCOLIGADA AND D2.codsecao = '{$secao}'
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao_funcao D ON D.codfuncao = '{$funcao}' AND D.id_posicao = D2.id
            LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_posicao_salario C ON C.id_posicao = D2.id AND C.id_posicao_funcao = D.id

            LEFT JOIN PSECAO D3 ON D3.CODIGO = '{$secao}' AND D3.CODCOLIGADA = P.CODCOLIGADA
            LEFT JOIN PFUNCAO D4 ON D4.CODCOLIGADA = P.CODCOLIGADA AND D4.CODIGO = '{$funcao}'
            LEFT JOIN GFILIAL D5 ON D5.CODCOLIGADA = P.CODCOLIGADA AND D5.CODFILIAL = D3.CODFILIAL
            LEFT JOIN PSECAOFAPALIQUOTA D6 ON D6.CODSECAO = SUBSTRING(D3.CODIGO, 1, 3) AND D6.CODCOLIGADA = P.CODCOLIGADA AND YEAR(D6.FINALVIGENCIAALIQUOTA) = YEAR(GETDATE())
            LEFT JOIN GCONSIST D7 ON D7.CODTABELA = 'PERPREV' AND D7.CODCLIENTE = B.PERCENTUAL

            
            
            -- ADICIONAL TRITREM
            LEFT JOIN ZMDADICIONALSALARIAL E ON E.COLIGADA = P.CODCOLIGADA AND SUBSTRING(E.FUNCAO, 1, 5) = '{$funcao}' AND E.PROGRAMA = 'TRITREM'
            
            -- PERICULOSIDADE
            
            -- ADICIONAL ASSIDUIDADE
            LEFT JOIN ZMDADICIONALSALARIAL F ON F.COLIGADA = P.CODCOLIGADA AND SUBSTRING(F.FUNCAO, 1, 5) = '{$funcao}' AND F.PROGRAMA = 'ASSIDUIDADE'
        )X
        )Y
        ";
  
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
        
    }
    
    // -------------------------------------------------------
    // Lista filial
    // -------------------------------------------------------
    public function ListarFilial($global_rh = false){

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        
        if($Secoes){
            $in_secao = "";
            foreach($Secoes as $key =>$CodSecao){
                $in_secao .= "'{$CodSecao['codsecao']}',";
            }
            $in_secao = " AND CODIGO IN (".rtrim($in_secao, ',').")";
        }else{
            $in_secao = " AND 1 = 2 ";
        }

        if($global_rh) $in_secao = "";
        
        $query = " SELECT CODFILIAL, NOMEFANTASIA DESCRICAO FROM GFILIAL WHERE CODCOLIGADA = '{$this->coligada}' AND CODFILIAL IN (SELECT CODFILIAL FROM PSECAO WHERE CODCOLIGADA = {$this->coligada} {$in_secao}) ORDER BY CODFILIAL ";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    
    // -------------------------------------------------------
    // Lista seção que o gestor pode ver
    // -------------------------------------------------------
    public function ListarFilialSecao($codfilial, $global_rh = false){

        if($codfilial === null) return false;

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        
        if($Secoes){
            $in_secao = "";
            foreach($Secoes as $key =>$CodSecao){
                $in_secao .= "'{$CodSecao['codsecao']}',";
            }
            $in_secao = " AND A.CODIGO IN (".rtrim($in_secao, ',').")";
        }else{
            $in_secao = " AND 1 = 2 ";
        }

        if($global_rh) $in_secao = "";

        $query = "
            SELECT
                A.CODIGO,
                A.DESCRICAO
            FROM
                PSECAO A
            WHERE
                    A.CODCOLIGADA = {$this->coligada}
                AND A.CODFILIAL = '{$codfilial}'
                AND LEN(A.CODIGO) = ".TAMANHO_SECAO."
                AND A.SECAODESATIVADA = 0
                {$in_secao}

            ORDER BY
                A.DESCRICAO
        ";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    
    // -------------------------------------------------------
    // Lista funções
    // -------------------------------------------------------
    public function ListarFuncao($global_rh = false){

        // dados do funcionário
        $mPortal            = model('PortalModel');
        $mHierarquia        = model('HierarquiaModel');
        $DadosFuncionario   = $mPortal->ListarDadosFuncionario(false, util_chapa(session()->get('func_chapa'))['CHAPA']);
        $codnivel           = $this->PegaNivelFuncao($DadosFuncionario[0]['CODFUNCAO'] ?? '000');
        if($global_rh) $codnivel = '000';
        if(!$codnivel) $codnivel = '999';

        $isLiderBP          = $mHierarquia->getSecaoPodeVerLiderBP();
        $isBP               = $mHierarquia->getSecaoPodeVerBP();

        if($isLiderBP || $isBP) $codnivel = '000';

        $query = "
            SELECT
                A.CODIGO,
                A.NOME
            FROM
                PFUNCAO A
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_subgrupo AA ON AA.id = 3
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_funcao BB ON BB.codigo = A.CODIGO COLLATE Latin1_General_CI_AS AND BB.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                    INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
            WHERE
                    A.CODCOLIGADA = {$this->coligada}
                AND A.INATIVA = 0
                AND TRY_CAST(SUBSTRING(EE.descricao, 1, 1) AS INT) >= ".$codnivel."
            ORDER BY
                A.NOME
        ";
        /*echo '<pre>';
echo $query;
        exit();*/
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    
    // -------------------------------------------------------
    // Cadastrar simulação
    // -------------------------------------------------------
    public function CadastrarSimulador($dados){

        $acao = $dados['acao'];
        $chapa = $dados['chapa'] ?? "";
        $posicao_nova = $dados['posicao_destino'] ?? "";
        $filial = $dados['filial'] ?? "";
        $secao = $dados['secao'] ?? "";
        $funcao = $dados['funcao'] ?? "";
        $tipo = $dados['tipo'];
        $descricao = $dados['descricao'];
        $valor_atual = $dados['valor_atual'] ?? "";
        $valor_calculado = $dados['valor_calculado'] ?? "";
        $html_pdf = $dados['html_pdf'] ?? "";
        $processo = $dados['processo'] ?? 1;

        $dados_filtro = "";
        if($acao == 'A' || $acao == 'P') $dados_filtro .= "<tr><td bgcolor='#f0f0f0'><b>Colaborador:</b></td><td>{$dados['texto_chapa']}</td></tr>";
        if($acao == "P" || $acao == "R") $dados_filtro .= "<tr><td bgcolor='#f0f0f0'><b>Posição Nova:</b></td><td>{$dados['texto_posicao_destino']}</td></tr>";
        if($acao == "S"){
            $dados_filtro .= "
            <tr><td bgcolor='#f0f0f0'><b>Filial:</b></td><td>{$dados['texto_filial']}</td></tr>
            <tr><td bgcolor='#f0f0f0'><b>Seção:</b></td><td>{$dados['texto_secao']}</td></tr>
            <tr><td bgcolor='#f0f0f0'><b>Função:</b></td><td>{$dados['texto_funcao']}</td></tr>
            ";
        }
        

        $filtro = "
            <table width='100%'>
                <tr>
                    <td align='center' bgcolor='#e0e0e0' colspan='2'><b>Filtro</b></td>
                </tr>
                <tr>
                    <td bgcolor='#f0f0f0'><b>Ação:</b></td>
                    <td>{$dados['texto_acao']}</td>
                </td>
                {$dados_filtro}
            </table>
        ";

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_bottom' => 7,
            'margin_top' => 7,
            'margin_left' => 7,
            'margin_right' => 7,
            'default_font_size' => 8
        ]);

        $html = '<style type="text/css">
            body {font-family: sans-serif;}
            table {border-collapse: collapse; margin-bottom: 10px;} 
            td, th {border: 1px solid #000000;}
            .bg-primary { background-color: #5766da !important;}
            .bg-success {background-color: #1ecab8 !important;}
            .bg-info {background-color: #00bcd4 !important;}
            .text-white {color: #ffffff !important;}
            th {background: #f0f0f0;}
            .bg-dark {background-color: #2f394e !important;}
            .text-center {text-align: center !important;}
            .text-danger {color: #f93b7a !important;}
            .form-control {border: 1px solid #ffffff !important; text-align: center !important;}
            </style>'.$filtro.$html_pdf;

        $mpdf->WriteHTML($html);

        /*
        * F - salva o arquivo NO SERVIDOR
        * I - abre no navegador E NÃO SALVA
        * D - chama o prompt E SALVA NO CLIENTE
        */
        //header("Content-type:application/pdf");
        $pdf = base64_encode($mpdf->Output("Simulador.pdf", "S"));

        // cadastra
        $query = "
            INSERT INTO zcrmportal_simulador
            (
                coligada,
                acao,
                chapa,
                posicao_nova,
                filial,
                secao,
                funcao,
                usucad,
                dtcad,
                html_pdf
            ) VALUES (
                '{$this->coligada}',
                '{$acao}',
                ".((strlen(trim($chapa)) > 0) ? "'{$chapa}'" : "NULL").",
                ".((strlen(trim($posicao_nova)) > 0) ? "'{$posicao_nova}'" : "NULL").",
                ".((strlen(trim($filial)) > 0) ? "'{$filial}'" : "NULL").",
                ".((strlen(trim($secao)) > 0) ? "'{$secao}'" : "NULL").",
                ".((strlen(trim($funcao)) > 0) ? "'{$funcao}'" : "NULL").",
                '{$this->log_id}',
                '".date('Y-m-d H:i:s')."',
                '{$pdf}'
            )
        ";
        $this->dbportal->query($query);
        if($this->dbportal->affectedRows() > 0){

            $id_simulador = $this->dbportal->insertID();

            for($i=0; $i < count($tipo); $i++){

                $reg_tipo = $tipo[$i];
                $reg_descricao = $descricao[$i];
                $reg_valor_atual = ($acao == "A" || $acao == "P") ? "'".moeda($valor_atual[$i], 'EN')."'" : "NULL";
                $reg_valor_calculado = ($acao == "P" || $acao == "R" || $acao == "S") ? "'".moeda($valor_calculado[$i], 'EN')."'" : "NULL";

                /*$this->dbportal->query("
                    INSERT INTO zcrmportal_simulador_dados
                    (
                        id_simulador,
                        tipo,
                        descricao,
                        valor_atual,
                        valor_simulado
                    ) VALUES (
                        {$id_simulador},
                        '{$reg_tipo}',
                        '{$reg_descricao}',
                        {$reg_valor_atual},
                        {$reg_valor_calculado}
                    )
                ");*/

            }
            
            return responseJson('success', 'Simulação cadastrada com sucesso.', base64_encode($id_simulador));

        }

        return responseJson('error', 'Falha ao cadastrar simulação');
        

    }

    public function PegaNivelFuncao($codfuncao){

        if($this->log_id == 1) return 0;

        $query = "
            SELECT 
                ISNULL(CAST(SUBSTRING(EE.descricao, 1, 1)  AS INT),0) NIVEL
            FROM
                zcrmportal_salario_subgrupo AA
                INNER JOIN zcrmportal_salario_funcao BB ON BB.codigo = '{$codfuncao}' AND BB.coligada = AA.coligada
                INNER JOIN zcrmportal_salario_itens_subgrupo CC ON CC.id_subgrupo = AA.id AND CC.descricao = 'Nível' AND CC.coligada = AA.coligada
                INNER JOIN zcrmportal_salario_dados DD ON DD.id_grupo = AA.id_grupo AND DD.id_subgrupo = AA.id AND DD.id_funcao = BB.id AND DD.id_item = CC.id
                INNER JOIN zcrmportal_salario_alternativas_itens EE ON EE.id = TRY_CAST(DD.valor_texto AS INT)
        ";
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray()[0]['NIVEL'] 
                : false;

    }
    

}
?>