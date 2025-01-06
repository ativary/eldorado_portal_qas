<?php
namespace App\Models\Ponto;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class EspelhoModel extends Model {

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

    // -------------------------------------------------------
    // Pega as configuração do espelho de ponto
    // -------------------------------------------------------
    public function ListarEspelhoConfiguracao(){

        $query = " SELECT * FROM zcrmportal_espelho_config WHERE coligada = '".session()->get('func_coligada')."' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Cadastra configuração do holerite
    // -------------------------------------------------------
    public function CadastrarEspelhoConfiguracao($dados){

        // verifica se o período já foi configurado
        $checkExiste = $this->ListarEspelhoConfiguracao();

        if($checkExiste){
            // update
            $dados['data_inicio'] = ($dados['data_inicio'] == '') ? "NULL" : "'{$dados['data_inicio']}'";
            $dados['data_fim'] = ($dados['data_fim'] == '') ? "NULL" : "'{$dados['data_fim']}'";
            // $dados['data_inicio_bloqueio'] = ($dados['data_inicio_bloqueio'] == '') ? "NULL" : "'{$dados['data_inicio_bloqueio']}'";
            // $dados['data_fim_bloqueio'] = ($dados['data_fim_bloqueio'] == '') ? "NULL" : "'{$dados['data_fim_bloqueio']}'";
            $dados['limite_funcionario'] = ($dados['limite_funcionario'] == '') ? "NULL" : "'{$dados['limite_funcionario']}'";
            $dados['limite_gestor'] = ($dados['limite_gestor'] == '') ? "NULL" : "'{$dados['limite_gestor']}'";
            
            $query = "
                UPDATE
                    zcrmportal_espelho_config
                SET
                    dtinicio = {$dados['data_inicio']},
                    dtfim = {$dados['data_fim']},
                    limite_funcionario = {$dados['limite_funcionario']},
                    limite_gestor = {$dados['limite_gestor']},
                    dtcad = '" . date('Y-m-d H:i:s') . "',
                    usucad = '" . session()->get('log_id') . "'
                WHERE
                    coligada = '" . session()->get('func_coligada') . "'
            ";
            $this->dbportal->query($query);

            if($this->dbportal->affectedRows() > 0){

                notificacao('success', 'Espelho de ponto configurado com sucesso');
                return responseJson('success', 'Espelho de ponto configurado com sucesso.');

            }else{
                return responseJson('error', 'Falha ao realizar a configuração do espelho de ponto.');
            }
            

        }else{
            // insert

            // $dados['data_inicio_bloqueio'] = ($dados['data_inicio_bloqueio'] == '') ? "NULL" : "'{$dados['data_inicio_bloqueio']}'";
            // $dados['data_fim_bloqueio'] = ($dados['data_fim_bloqueio'] == '') ? "NULL" : "'{$dados['data_fim_bloqueio']}'";
            // $dados['data_atual_bloqueio'] = ($dados['data_atual_bloqueio'] == '') ? "NULL" : "'{$dados['data_atual_bloqueio']}'";

            $dados['data_inicio'] = ($dados['data_inicio'] == '') ? "NULL" : "'{$dados['data_inicio']}'";
            $dados['data_fim'] = ($dados['data_fim'] == '') ? "NULL" : "'{$dados['data_fim']}'";
            $dados['limite_funcionario'] = ($dados['limite_funcionario'] == '') ? "NULL" : "'{$dados['limite_funcionario']}'";
            $dados['limite_gestor'] = ($dados['limite_gestor'] == '') ? "NULL" : "'{$dados['limite_gestor']}'";

            $query = "
                INSERT INTO zcrmportal_espelho_config
                    (coligada, dtinicio, dtfim, limite_funcionario, limite_gestor, dtcad, usucad)
                VALUES
                    ('" . session()->get('func_coligada') . "', {$dados['data_inicio']}, {$dados['data_fim']}, {$dados['limite_funcionario']}, {$dados['limite_gestor']}, '" . date('Y-m-d H:i:s') . "', '" . session()->get('log_id') . "')
            ";

            $this->dbportal->query($query);

            if($this->dbportal->affectedRows() > 0){

                notificacao('success', 'Espelho de ponto configurado com sucesso');
                return responseJson('success', 'Espelho de ponto configurado com sucesso.');

            }else{
                return responseJson('error', 'Falha ao realizar a configuração do espelho de ponto.');
            }

        }

    }


    // -------------------------------------------------------
    // Lista periodo do espelho
    // -------------------------------------------------------
    public function ListarEspelhoPeriodoRM($rh = false){

        $configuracao = $this->ListarEspelhoConfiguracao();
        if(!$configuracao) return false;

        if($rh) $configuracao[0]['dtinicio'] = '1900-01-01';
        
        $query = "
            SELECT 
                * 
            FROM 
                APERIODO 
            WHERE 
                    CODCOLIGADA = '".session()->get('func_coligada')."'
                AND INICIOMENSAL >= '{$configuracao[0]['dtinicio']}' AND FIMMENSAL  <= '{$configuracao[0]['dtfim']}'

            ORDER BY 
                ANOCOMP DESC, 
                MESCOMP DESC
        ";
        // exit('<pre>'.$query);
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista dias do período
    // -------------------------------------------------------
    public function ListarEspelhoDias($periodo, $chapa, $motorista = false){

        if($periodo === NULL) return false;

        $coligada   = session()->get("func_coligada");
        $chapa      = $chapa;
        $dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim    = dtEn(substr($periodo, 10, 10));

        $horas_refeicao = ($motorista) ?
        "(CASE WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) IS NULL THEN NULL
        WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) <= 3 THEN ('0')
        WHEN (SELECT QTDE FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)) >= 4
        THEN CASE WHEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))
        - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) IS NULL THEN ''
        ELSE CASE WHEN (DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5)) <
              DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) )
              THEN  
          ((DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))+1440
        - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) )      
          ELSE     
        ((DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),11,5))
        - DATEDIFF(minute, 0, SUBSTRING((SELECT replace(replace(replace(BATIDAS,' ',''),'+',''),'-','') FROM BATIDAS(A.CODCOLIGADA,A.CHAPA,A.DATA,A.DATA)),6,5)) ) )
          END
        END
        END )":
        'NULL';

        $query = "

        WITH 
            REFEICAO AS(
            SELECT
                COL
                ,VCHAPA CHAPA
                ,VDATA  DATA
                ,BAT

                ,(CASE 
                    WHEN BAT = 4 THEN ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)
                    WHEN BAT = 6 THEN ((((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+((CASE WHEN ENT3 < SAI2 THEN ENT3+1440 ELSE ENT3 END)-SAI2)))
                    WHEN BAT = 8 THEN ((((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+((CASE WHEN ENT3 < SAI2 THEN ENT3+1440 ELSE ENT3 END)-SAI2)+((CASE WHEN ENT4 < SAI3 THEN ENT4+1440 ELSE ENT4 END)-SAI3)))
                END)REALIZADO
                
                
            FROM(
            SELECT 
                COL
                ,VCHAPA
                ,VDATA
                ,HOR
                ,DESC_HOR
                ,'|'T
                ,BAT
            ,(SELECT ISNULL((A.INTERVALO),0)
                    FROM 
                    Z_OUTSERV_MELHORIAS3 A
                    WHERE 
                        A.CODCOLIGADA = COL
                        AND A.CODHORARIO = HOR
                        AND A.CODINDICE = IND_HOR) INTERVALO 
                , ENT1
                , SAI1
                , ENT2
                , SAI2
                , ENT3
                , SAI3
                , ENT4
                , SAI4 	      

            FROM(
            SELECT COL
                ,VCHAPA
                ,NOME
                , DATA VDATA            
                ,(SELECT CODHORARIO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))HOR         
                ,(SELECT DESCRICAO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))DESC_HOR
                ,(SELECT IND_CALC FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))IND_HOR           
                
                , [1] AS ENT1, [2] AS SAI1
                , [3] AS ENT2, [4] AS SAI2
                , [5] AS ENT3, [6] AS SAI3
                , [7] AS ENT4, [8] AS SAI4
                
                ,((CASE WHEN [1] IS NOT NULL THEN 1 ELSE 0 END)+
                (CASE WHEN [2] IS NOT NULL THEN 1 ELSE 0 END)+
                (CASE WHEN [3] IS NOT NULL THEN 1 ELSE 0 END)+
                (CASE WHEN [4] IS NOT NULL THEN 1 ELSE 0 END)+
                (CASE WHEN [5] IS NOT NULL THEN 1 ELSE 0 END)+
                (CASE WHEN [6] IS NOT NULL THEN 1 ELSE 0 END)+
                (CASE WHEN [7] IS NOT NULL THEN 1 ELSE 0 END)+
                (CASE WHEN [8] IS NOT NULL THEN 1 ELSE 0 END)
                )BAT
            FROM (
                SELECT 
                    A.CODCOLIGADA COL
                    ,A.CHAPA VCHAPA
                    ,B.NOME
                    ,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
                    ,ISNULL(A.DATAREFERENCIA,A.DATA)VDATA
                    ,ROW_NUMBER() OVER (PARTITION BY ISNULL(A.DATAREFERENCIA, A.DATA) ORDER BY A.CHAPA, A.DATA, ISNULL(A.DATAREFERENCIA, A.DATA)) AS LIN
                /*,(CASE 
                        WHEN ISNULL(DATAREFERENCIA, DATA) > A.DATA THEN CONVERT(VARCHAR, DATEADD(MINUTE, BATIDA, ISNULL(DATAREFERENCIA, DATA)), 108) + '-'
                        WHEN ISNULL(DATAREFERENCIA, DATA) < A.DATA THEN CONVERT(VARCHAR, DATEADD(MINUTE, BATIDA, ISNULL(DATAREFERENCIA, DATA)), 108) + '+'
                        ELSE CONVERT(VARCHAR, DATEADD(MINUTE, BATIDA, ISNULL(DATAREFERENCIA, DATA)), 108) + ' '
                    END) AS BATIDA*/
                    
                    ,(CASE 
                        WHEN ISNULL(DATAREFERENCIA, DATA) > A.DATA THEN BATIDA
                        WHEN ISNULL(DATAREFERENCIA, DATA) < A.DATA THEN BATIDA
                        ELSE BATIDA
                    END) AS BATIDA     
                    
                    
                    
                FROM ABATFUN A
                    LEFT JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA 
                WHERE 
                A.STATUS <> 'T'
                AND A.CHAPA ='{$chapa}'
                AND A.CODCOLIGADA ='{$coligada}'
                AND ISNULL(A.DATAREFERENCIA, A.DATA) BETWEEN '{$dataInicio}' AND '{$dataFim}'
            ) AS PivotSource
            PIVOT (
                MAX(BATIDA) FOR LIN IN ([1], [2], [3], [4], [5],[6],[7],[8])) AS PivotTable

            )TBL

            )XX

            WHERE (BAT = 4 OR BAT = 6 OR BAT = 8 )
            )




            SELECT 
                A.CODCOLIGADA, 
                A.CHAPA, 
                A.DATA, 
                A.ATRASO, 
                A.FALTA, 
                A.HTRAB, 
                A.EXTRAEXECUTADO, 
                A.ADICIONAL, 
                A.ABONO, 
                A.BASE, 
                A.EXTRAAUTORIZADO, 
                A.TEMPOREF, 
                A.ATRASONUCL, 
                A.COMPENSADO, 
                A.DESCANSO, 
                A.FERIADO, 
                A.EXTRACALC, 
                A.ATRASOCALC, 
                A.FALTACALC
                ,(SELECT (CASE WHEN A.DATA < B.DATAADMISSAO THEN 'Não Admitido' END) FROM PFUNC B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA) ADMISSAO
                ,(SELECT 'Férias' FROM PFUFERIASPER C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA BETWEEN C.DATAINICIO AND C.DATAFIM) FERIAS
                ,(SELECT E.DESCRICAO FROM PFHSTAFT D, PCODAFAST E WHERE D.TIPO = E.CODCLIENTE AND A.CODCOLIGADA = D.CODCOLIGADA AND A.CHAPA = D.CHAPA AND A.DATA BETWEEN D.DTINICIO AND coalesce(D.DTFINAL, '2050-12-31')) AFASTAMENTO
                ,(SELECT MAX(G.DESCRICAO) FROM AABONFUN F, AABONO G WHERE F.CODCOLIGADA = G.CODCOLIGADA AND F.CODABONO = G.CODIGO AND A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA AND A.DATA = F.DATA) ABONOS
                ,(SELECT ISNULL(SUM(FIM - INICIO), 0) FROM AJUSTFUN WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATAREFERENCIA = A.DATA AND APROVADO = '1') SUSPENSAO,
                (SELECT SUM(HTRAB) FROM AAFHTFUN WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATA >= '{$dataInicio}' AND DATA <= '{$dataFim}') QTDE_HORAS,


                (SELECT 
                    COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                    FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                ) ESCALA,
                
                (SELECT SUM(ISNULL(HORA,0))
					   FROM(

							/* 1a FAIXA AMOVFUNDIA*/
							SELECT 
								SUM(ISNULL(NUMHORAS,0)) HORA
							FROM AMOVFUNDIA M 
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND M.CODEVE IN (SELECT CODEVEREL FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0018','0026') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							
							UNION ALL

							/* 1a FAIXA BANCO DE HORAS*/
							SELECT 
								SUM(ISNULL(VALOR,0)) HORA
							FROM ABANCOHORFUNDETALHE M
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0018','0026') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							)X 
					 ) EXTRA_1AFAIXA,
					 
					 (SELECT SUM(ISNULL(HORA,0))
					   FROM(

							/* 2a FAIXA AMOVFUNDIA*/
							SELECT 
								SUM(ISNULL(NUMHORAS,0)) HORA
							FROM AMOVFUNDIA M 
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND M.CODEVE IN (SELECT CODEVEREL FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0019', '0020', '0021', '0027', '0036') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							
							UNION ALL

							/* 2a FAIXA BANCO DE HORAS*/
							SELECT 
								SUM(ISNULL(VALOR,0)) HORA
							FROM ABANCOHORFUNDETALHE M
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0019', '0020', '0021', '0027', '0036') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							)X 
					 ) EXTRA_2AFAIXA,	
					 
					 (SELECT SUM(ISNULL(HORA,0))
					   FROM(

					   		/* 2a FAIXA AMOVFUNDIA*/
							SELECT 
								SUM(ISNULL(NUMHORAS,0)) HORA
							FROM AMOVFUNDIA M 
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND M.CODEVE IN (SELECT CODEVEREL FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0022','0023','0024','0025') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							
							UNION ALL

							/* 2a FAIXA BANCO DE HORAS*/
							SELECT 
								SUM(ISNULL(VALOR,0)) HORA
							FROM ABANCOHORFUNDETALHE M
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0035', '0490') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
					 		)X 
					 ) EXTRA_100,
                    (SELECT
                        max(CONCAT(bb.id, ' - ', CAST(bb.descricao AS VARCHAR)))
                    FROM
                        ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func aa
                        JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motivos bb ON bb.id = aa.justificativa
                    WHERE
                            aa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                         AND aa.coligada = A.CODCOLIGADA
                         AND aa.dtponto = A.DATA
                    ) justificativa_extra,



                    ISNULL((

                        SELECT
                            ta.horas_de_direcao
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_ats_totalizador ta
                            INNER JOIN PFUNC tb ON tb.CHAPA = A.CHAPA AND tb.CODCOLIGADA = A.CODCOLIGADA
                            INNER JOIN PPESSOA tc ON tc.CODIGO = tb.CODPESSOA
                        WHERE
                                tc.CPF = ta.cpf COLLATE Latin1_General_CI_AS
                            and ta.data_gravacao = A.DATA

                    ),0) total_direcao,

                    ISNULL((

                        SELECT
                            ta.horas_em_espera
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_ats_totalizador ta
                            INNER JOIN PFUNC tb ON tb.CHAPA = A.CHAPA AND tb.CODCOLIGADA = A.CODCOLIGADA
                            INNER JOIN PPESSOA tc ON tc.CODIGO = tb.CODPESSOA
                        WHERE
                                tc.CPF = ta.cpf COLLATE Latin1_General_CI_AS
                            and ta.data_gravacao = A.DATA

                    ),0) total_espera,



                    (SELECT R.REALIZADO FROM REFEICAO R WHERE R.COL = A.CODCOLIGADA AND R.CHAPA = A.CHAPA AND R.DATA = A.DATA) total_refeicao, 
                    --ISNULL(total_direcao,0) total_direcao, 
                    --ISNULL(total_espera,0) total_espera, 
                    ISNULL(adicional_noturno,0) adicional_noturno
            FROM 
                AAFHTFUN A
                LEFT JOIN APARFUN AS B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motorista C ON C.coligada = A.CODCOLIGADA AND C.chapa = A.CHAPA COLLATE Latin1_General_CI_AS AND C.dtponto = A.DATA
                
            WHERE 
                    A.CODCOLIGADA = {$coligada}
                AND A.CHAPA = '{$chapa}'
                AND A.DATA >= '{$dataInicio}' 
                AND A.DATA <= '{$dataFim}' 

            UNION ALL 

            SELECT 
                A.CODCOLIGADA, 
                A.CHAPA, 
                A.DATA, 
                A.ATRASO, 
                A.FALTA, 
                A.HTRAB, 
                A.EXTRAEXECUTADO, 
                A.ADICIONAL, 
                A.ABONO, 
                A.BASE, 
                A.EXTRAAUTORIZADO, 
                A.TEMPOREF, 
                A.ATRASONUCL, 
                A.COMPENSADO, 
                A.DESCANSO, 
                A.FERIADO, 
                A.EXTRACALC, 
                A.ATRASOCALC, 
                A.FALTACALC
                ,(SELECT (CASE WHEN A.DATA < B.DATAADMISSAO THEN 'Não Admitido' END) FROM PFUNC B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA) ADMISSAO
                ,(SELECT 'Férias' FROM PFUFERIASPER C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA BETWEEN C.DATAINICIO AND C.DATAFIM) FERIAS
                ,(SELECT E.DESCRICAO FROM PFHSTAFT D, PCODAFAST E WHERE D.TIPO = E.CODCLIENTE AND A.CODCOLIGADA = D.CODCOLIGADA AND A.CHAPA = D.CHAPA AND A.DATA BETWEEN D.DTINICIO AND coalesce(D.DTFINAL, '2050-12-31')) AFASTAMENTO
                ,(SELECT MAX(G.DESCRICAO) FROM AABONFUNAM F, AABONO G WHERE F.CODCOLIGADA = G.CODCOLIGADA AND F.CODABONO = G.CODIGO AND A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA AND A.DATA = F.DATA) ABONOS
                ,(SELECT ISNULL(SUM(FIM - INICIO), 0) FROM AJUSTFUN WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATAREFERENCIA = A.DATA AND APROVADO = '1') SUSPENSAO,
                (SELECT SUM(HTRAB) FROM AAFHTFUNAM WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DATA >= '{$dataInicio}' AND DATA <= '{$dataFim}') QTDE_HORAS,
                
                (SELECT 
                    COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                    COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                    FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
                ) ESCALA,
                
                (SELECT SUM(ISNULL(HORA,0))
					   FROM(

							/* 1a FAIXA AMOVFUNDIA*/
							SELECT 
								SUM(ISNULL(NUMHORAS,0)) HORA
							FROM AMOVFUNDIA M 
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND M.CODEVE IN (SELECT CODEVEREL FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0018','0026') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							
							UNION ALL

							/* 1a FAIXA BANCO DE HORAS*/
							SELECT 
								SUM(ISNULL(VALOR,0)) HORA
							FROM ABANCOHORFUNDETALHE M
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0018','0026') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							)X 
					 ) EXTRA_1AFAIXA,
					 
					 (SELECT SUM(ISNULL(HORA,0))
					   FROM(

							/* 2a FAIXA AMOVFUNDIA*/
							SELECT 
								SUM(ISNULL(NUMHORAS,0)) HORA
							FROM AMOVFUNDIA M 
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND M.CODEVE IN (SELECT CODEVEREL FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0019', '0020', '0021', '0027', '0036') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							
							UNION ALL

							/* 2a FAIXA BANCO DE HORAS*/
							SELECT 
								SUM(ISNULL(VALOR,0)) HORA
							FROM ABANCOHORFUNDETALHE M
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0019', '0020', '0021', '0027', '0036') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							)X 
					 ) EXTRA_2AFAIXA,	
					 
					 (SELECT SUM(ISNULL(HORA,0))
					   FROM(

					   		/* 2a FAIXA AMOVFUNDIA*/
							SELECT 
								SUM(ISNULL(NUMHORAS,0)) HORA
							FROM AMOVFUNDIA M 
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND M.CODEVE IN (SELECT CODEVEREL FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0022','0023','0024','0025') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
							
							UNION ALL

							/* 2a FAIXA BANCO DE HORAS*/
							SELECT 
								SUM(ISNULL(VALOR,0)) HORA
							FROM ABANCOHORFUNDETALHE M
							WHERE 
								A.CODCOLIGADA = M.CODCOLIGADA
							AND A.CHAPA       = M.CHAPA
							AND A.DATA        = M.DATA
							AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL N, AEVECALC O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODEVEPTO = O.CODEVENTO AND O.CODCALC IN ('0035', '0490') AND B.CODCOLIGADA = N.CODCOLIGADA AND B.CODPARCOL = N.CODPARCOL)
					 		)X 
					 ) EXTRA_100,
                     (SELECT
                         max(CONCAT(bb.id, ' - ', CAST(bb.descricao AS VARCHAR)))
                     FROM
                         ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func aa
                         JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motivos bb ON bb.id = aa.justificativa
                     WHERE
                             aa.chapa = A.CHAPA COLLATE Latin1_General_CI_AS
                         AND aa.coligada = A.CODCOLIGADA
                         AND aa.dtponto = A.DATA
                     ) justificativa_extra,

                     ISNULL((

                        SELECT
                            ta.horas_de_direcao
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_ats_totalizador ta
                            INNER JOIN PFUNC tb ON tb.CHAPA = A.CHAPA AND tb.CODCOLIGADA = A.CODCOLIGADA
                            INNER JOIN PPESSOA tc ON tc.CODIGO = tb.CODPESSOA
                        WHERE
                                tc.CPF = ta.cpf COLLATE Latin1_General_CI_AS
                            and ta.data_gravacao = A.DATA

                     ),0) total_direcao,

                    ISNULL((

                        SELECT
                            ta.horas_em_espera
                        FROM
                            ".DBPORTAL_BANCO."..zcrmportal_ponto_ats_totalizador ta
                            INNER JOIN PFUNC tb ON tb.CHAPA = A.CHAPA AND tb.CODCOLIGADA = A.CODCOLIGADA
                            INNER JOIN PPESSOA tc ON tc.CODIGO = tb.CODPESSOA
                        WHERE
                                tc.CPF = ta.cpf COLLATE Latin1_General_CI_AS
                            and ta.data_gravacao = A.DATA

                    ),0) total_espera,
                    
                    (SELECT R.REALIZADO FROM REFEICAO R WHERE R.COL = A.CODCOLIGADA AND R.CHAPA = A.CHAPA AND R.DATA = A.DATA) total_refeicao, 
                     --ISNULL(total_direcao,0) total_direcao, 
                     --ISNULL(total_espera,0) total_espera, 
                     ISNULL(adicional_noturno,0) adicional_noturno
                    
            FROM 
                AAFHTFUNAM A
                LEFT JOIN APARFUN AS B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motorista C ON C.coligada = A.CODCOLIGADA AND C.chapa = A.CHAPA COLLATE Latin1_General_CI_AS AND C.dtponto = A.DATA
                
            WHERE 
                    A.CODCOLIGADA = {$coligada}
                AND A.CHAPA = '{$chapa}'
                AND A.DATA >= '{$dataInicio}' 
                AND A.DATA <= '{$dataFim}' 
            ORDER BY 
                DATA
        ";
        if($_SESSION['log_id'] == 1){
        // echo '<pre>';
        // echo $query;
        // exit();
        }
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function getDadosHorario($periodo, $chapa){

        $coligada = session()->get("func_coligada");

        $query = "
           SELECT * FROM PFUNC WHERE CHAPA = '{$chapa}' AND CODCOLIGADA = {$coligada}
        ";
        $result = $this->dbrm->query($query);

        $dados = $result->getResultArray();

        $query2 = "SELECT * FROM AHORARIO WHERE CODIGO = '{$dados[0]['CODHORARIO']}'";

        $result2 = $this->dbrm->query($query2);

        return ($result2->getNumRows() > 0) 
                ? $result2->getResultArray() 
                : false;


    }

    public function getIndice($chapa, $data){

        $coligada = session()->get("func_coligada");

        $query = "
            SELECT * FROM dbo.OUTSERV_HORARIO_HIST('{$coligada}','{$chapa}','{$data}')
        ";
        $result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;


    }

    public function getEscala($horario){

        $query = "SELECT * FROM AHORARIO WHERE CODIGO = '{$horario}' AND CODCOLIGADA = '{$_SESSION['func_coligada']}' ";

        $result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
    }

    // -------------------------------------------------------
    // Lista dias do período
    // -------------------------------------------------------
    public function ListarEspelhoBatidas($periodo, $chapa, $ocorrencia = false, $inclusão = false){

        if($periodo === NULL) return false;

        $coligada   = session()->get("func_coligada");
        $chapa      = $chapa;
        $dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim    = dtEn(substr($periodo, 10, 10));

        // monta uma SQL com os dados do portal
        $batidasPortal = self::ListarEspelhoBatidasPortal($chapa, $dataInicio, $dataFim, $inclusão);
        $BATIDAS_PORTAL = "";

        $FILTRODATA = ($ocorrencia) ? " AND COALESCE(DATAREFERENCIA, DATA) BETWEEN '{$dataInicio}' AND '{$dataFim}' " : " AND COALESCE(DATAREFERENCIA, DATA) BETWEEN '{$dataInicio}' AND '{$dataFim}' ";
        $FILTRODATA = ($inclusão) ? " AND DATAREFERENCIA BETWEEN '{$dataInicio}' AND '{$dataFim}' " : $FILTRODATA;

        $from_dual = (DBRM_TIPO == 'oracle') ? " FROM DUAL " : "";

        if($batidasPortal){
            foreach($batidasPortal as $key => $BatidaPortal){
                $BATIDAS_PORTAL .= " UNION ALL SELECT '{$chapa}' CHAPA, '{$BatidaPortal['DATA']}' DATA, '{$BatidaPortal['DATAREFERENCIA']}' DATAREFERENCIA, {$BatidaPortal['BATIDA']} BATIDA, 'D' STATUS, {$BatidaPortal['NATUREZA']} NATUREZA, '{$BatidaPortal['DATAINSERCAO']}' DATAINSERCAO, '{$BatidaPortal['DATABATIDA']}' DATABATIDA, {$BatidaPortal['BATIDA_NOTURNA']} BATIDA_NOTURNA, 1 PORTAL, 0 IDAAFDT, '{$BatidaPortal['MOTIVO_REPROVA']}' MOTIVO_REPROVA, '{$BatidaPortal['JUSTIFICATIVA_BATIDA']}' JUSTIFICATIVA_BATIDA {$from_dual} ";
                unset($BatidaPortal, $batidasPortal[$key], $key);
            }
        }

        $query = "

        SELECT * FROM (
            SELECT 
                CHAPA,
                DATA, 
                DATAREFERENCIA, 
                BATIDA, 
                STATUS, 
                NATUREZA, 
                DATAINSERCAO, 
                CASE WHEN DATAREFERENCIA IS NULL THEN DATA ELSE DATAREFERENCIA END DATABATIDA, 
                CASE WHEN DATAREFERENCIA < DATA THEN 1 ELSE 0 END BATIDA_NOTURNA,
                0 PORTAL,
                IDAAFDT,
                NULL MOTIVO_REPROVA,
                (
                    SELECT 
                        MAX(COALESCE(A.justent1,A.justent2,A.justent3,A.justent4,A.justsai1,A.justsai2,A.justsai3,A.justsai4)) 
                    FROM 
                        ".DBPORTAL_BANCO."..zcrmportal_ponto_horas A
                    WHERE 
                            A.dtponto = DATA
                        AND A.chapa = '{$chapa}' COLLATE Latin1_General_CI_AS
                        AND A.movimento = 1 
                        AND A.coligada = CODCOLIGADA
                        AND COALESCE(A.ent1,A.ent2,A.ent3,A.ent4,A.sai1,A.sai2,A.sai3,A.sai4) = BATIDA
                
                ) JUSTIFICATIVA_BATIDA

                
            FROM 
                ABATFUN 
            WHERE 
                    CODCOLIGADA = {$coligada}
                AND CHAPA = '{$chapa}'
                {$FILTRODATA}
                
            UNION ALL

            SELECT 
                CHAPA,
                DATA, 
                DATAREFERENCIA, 
                BATIDA, 
                STATUS, 
                NATUREZA, 
                DATAINSERCAO, 
                CASE WHEN DATAREFERENCIA IS NULL THEN DATA ELSE DATAREFERENCIA END DATABATIDA, 
                CASE WHEN DATAREFERENCIA < DATA THEN 1 ELSE 0 END BATIDA_NOTURNA,
                0 PORTAL,
                IDAAFDT,
                NULL MOTIVO_REPROVA,
                (
                    SELECT 
                        MAX(COALESCE(A.justent1,A.justent2,A.justent3,A.justent4,A.justsai1,A.justsai2,A.justsai3,A.justsai4)) 
                    FROM 
                        ".DBPORTAL_BANCO."..zcrmportal_ponto_horas A
                    WHERE 
                            A.dtponto = DATA
                        AND A.chapa = '{$chapa}' COLLATE Latin1_General_CI_AS
                        AND A.movimento = 1 
                        AND A.coligada = CODCOLIGADA
                        AND COALESCE(A.ent1,A.ent2,A.ent3,A.ent4,A.sai1,A.sai2,A.sai3,A.sai4) = BATIDA
                
                ) JUSTIFICATIVA_BATIDA
            FROM 
                ABATFUNAM
            WHERE 
                    CODCOLIGADA = {$coligada}
                AND CHAPA = '{$chapa}'
                {$FILTRODATA}

            {$BATIDAS_PORTAL}
        )X
            --ORDER BY DATA ASC, BATIDA ASC
            --ORDER BY CASE WHEN DATAREFERENCIA IS NOT NULL THEN DATAREFERENCIA ELSE DATA END ASC, CASE WHEN DATAREFERENCIA <> DATA THEN BATIDA+1440 ELSE BATIDA END ASC, NATUREZA ASC
            ORDER BY CASE WHEN DATAREFERENCIA IS NOT NULL THEN CASE WHEN DATAREFERENCIA > DATA THEN DATA ELSE DATAREFERENCIA END ELSE DATA END ASC, CASE WHEN DATAREFERENCIA <> DATA THEN BATIDA+1440 ELSE BATIDA END ASC, NATUREZA ASC
        ";
        //  if($_SESSION['log_id'] == 1) echo '<pre>'.$query.'</pre>';exit();
        $result = $this->dbrm->query($query);
        

        if($result->getNumRows() > 0){

            unset($BATIDAS_PORTAL);

            $dados = array();
            $linha = 0;

            $batidas = $result->getResultArray();
            foreach($batidas as $key => $dadosBatidas){
                
                $chapa = $dadosBatidas['CHAPA'];
                // $data = $dadosBatidas['DATA'];
                $data = ((isset($dadosBatidas['DATAREFERENCIA'])) ? $dadosBatidas['DATAREFERENCIA'] : $dadosBatidas['DATA']);

                if(!isset($dados[$chapa][$data])) $linha = 0;
                    
                $dados[$chapa][$data]['batidas'][$linha]['batida']                  = $dadosBatidas['BATIDA'];
                $dados[$chapa][$data]['batidas'][$linha]['data']                    = $dadosBatidas['DATA'];
                $dados[$chapa][$data]['batidas'][$linha]['datareferencia']          = $dadosBatidas['DATAREFERENCIA'];
                $dados[$chapa][$data]['batidas'][$linha]['natureza']                = $dadosBatidas['NATUREZA'];
                $dados[$chapa][$data]['batidas'][$linha]['status']                  = $dadosBatidas['STATUS'];
                $dados[$chapa][$data]['batidas'][$linha]['forcado']                 = 0;
                $dados[$chapa][$data]['batidas'][$linha]['portal']                  = $dadosBatidas['PORTAL'];
                $dados[$chapa][$data]['batidas'][$linha]['idaafdt']                 = $dadosBatidas['IDAAFDT'];
                $dados[$chapa][$data]['batidas'][$linha]['motivo_reprova']          = $dadosBatidas['MOTIVO_REPROVA'];
                $dados[$chapa][$data]['batidas'][$linha]['justificativa_batida']    = $dadosBatidas['JUSTIFICATIVA_BATIDA'];
                $linha++;

                unset($dadosBatidas, $batidas[$key], $key);
            }
            
            return $dados;

        }else{
            return false;
        }


    }

    public function ListarEspelhoBatidasPortal($chapa, $dataInicio, $dataFim, $insert = false){

        $coligada = session()->get("func_coligada");

        switch(DBPORTAL_TIPO){
            case    'mysql': $dtponto   = " STR_TO_DATE(dtponto, '%Y-%m-%d') "; break;
            case    'oracle': $dtponto  = " TO_DATE(dtponto, 'YYYY-MM-DD') "; break;
            case    'postgre': $dtponto = " CAST(dtponto AS DATE) "; break;
            default: $dtponto           = " CAST(dtponto AS DATETIME) "; break;
        }

        $filtroData = "
            AND (
                {$dtponto} BETWEEN '{$dataInicio}' AND '{$dataFim}'
                    OR
                COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) BETWEEN '{$dataInicio}' AND '{$dataFim}'
            )
        ";
        if($insert){
            $filtroData = "
                AND (
                        COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) BETWEEN '{$dataInicio}' AND '{$dataFim}'
                    )
            ";
        }

        $query = "
            SELECT
                chapa CHAPA,
                {$dtponto} DATA,
                COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) DATAREFERENCIA,
                COALESCE(ent1, ent2, ent3, ent4, ent5, sai1, sai2, sai3, sai4, sai5) BATIDA,
                'D' STATUS,
                COALESCE(natent1, natent2, natent3, natent4, natent5, natsai1, natsai2, natsai3, natsai4, natsai5) NATUREZA,
                dtcadastro DATAINSERCAO,
                CASE
                    WHEN 
                        COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) IS NULL
                    THEN {$dtponto}
                    ELSE
                        COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5)
                END DATABATIDA,
                CASE
                    WHEN
                        COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) < {$dtponto}
                    THEN 1
                    ELSE 0
                END BATIDA_NOTURNA,
                motivo_reprova MOTIVO_REPROVA,
                COALESCE(justent1,justent2,justent3,justent4,justsai1,justsai2,justsai3,justsai4) JUSTIFICATIVA_BATIDA
            FROM
                zcrmportal_ponto_horas
            WHERE
                    chapa = '{$chapa}'
                AND coligada = '{$coligada}'
                AND status IN ('1', '3')
                AND movimento = '1'
                {$filtroData}
        ";
        // echo '<pre>';
        // echo $query;exit();
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista movimentos espelhos
    // -------------------------------------------------------
    public function ListarEspelhoMovimento($periodo, $chapa){

        if($periodo === NULL) return false;

        $coligada   = session()->get("func_coligada");
        $chapa      = $chapa;
        $dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim    = dtEn(substr($periodo, 10, 10));

        $query = "
            SELECT 
                PEVENTO.CODIGO, 
                PEVENTO.DESCRICAO, 
                AMOVFUN.NUMHORAS 
            FROM 
                AMOVFUN, 
                PEVENTO
            WHERE
                    AMOVFUN.CODCOLIGADA = {$coligada}
                AND PEVENTO.CODCOLIGADA = AMOVFUN.CODCOLIGADA
                AND AMOVFUN.CHAPA = '{$chapa}' 
                AND AMOVFUN.INICIOPER = '{$dataInicio}' 
                AND AMOVFUN.FIMPER = '{$dataFim}'
                AND AMOVFUN.CODCOLIGADA = PEVENTO.CODCOLIGADA 
                AND AMOVFUN.CODEVE = PEVENTO.CODIGO 
                AND PEVENTO.CODIGO NOT IN ('0002','0003','0966') 
                AND AMOVFUN.NUMHORAS > 0
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function CadastrarBatida($dados, $arquivos = null, $tem_anexo = false){
       
        try {
           

            // verifica natureza
            $campo_batida        = ($dados['natureza_batida'] != 1) ? 'ent'.$dados['numero_batida'] : 'sai'.$dados['numero_batida'];
            $campo_natureza      = ($dados['natureza_batida'] != 1) ? 'natent'.$dados['numero_batida'] : 'natsai'.$dados['numero_batida'];
            $campo_justificativa = ($dados['natureza_batida'] != 1) ? 'justent'.$dados['numero_batida'] : 'justsai'.$dados['numero_batida'];
            $campo_referencia    = ($dados['natureza_batida'] != 1) ? 'dtrefent'.$dados['numero_batida'] : 'dtrefsai'.$dados['numero_batida'];
            $coligada            = session()->get("func_coligada");
            $chapa               = $dados['chapa'];
            $codfilial           = $dados['codfilial'];

            // verifica se já existe essa batida pendente de aprovação
            $batida_inserida    = (int)h2m($dados['horario_batida']);
            $data_batida        = dtBr($dados['data_batida']);
        
            
            $batidasRegistradas = self::ListarEspelhoBatidas($data_batida.$data_batida, $chapa, false, true);
            // print_r($batidasRegistradas);
            // exit();
            if($batidasRegistradas){
                foreach($batidasRegistradas[$chapa] as $key => $Batidas){
                    foreach($Batidas['batidas'] as $key2 => $batida){
                        if($batida['batida'] == $batida_inserida){
                           
                            return responseJson('error', '<b>Batida</b> já foi registrada.');
                            break;
                        }
                        // unset($Batidas[$key2], $batida);
                    }
                    // unset($batidasRegistradas[$key], $key, $Batidas);
                }
                unset($batidasRegistradas);
            }
            
            // exit('fim');
            // valida data referencia
            $ini = date('Ymd', strtotime($dados['data_batida']));
            $fim = date('Ymd', strtotime($dados['referencia_batida']));
            $diff = $ini-$fim;
            
            // if($diff != 0 && $diff != 1) return responseJson('error', '<b>Data referencia</b> inválida.');

            // verifica se digitou a justificativa
            if(strlen(trim($dados['justificativa_batida'])) <= 0){
                return responseJson('error', '<b>Justificativa da batida</b> não informada.');
            }

            $possui_anexo = "NULL";
            $anexo_batida = "NULL";
            if($tem_anexo == 1){

                foreach($arquivos as $key2 => $Atestado){

                    $file_nome = $Atestado['name'];
                    $file_size = $Atestado['size'];
                    $file_type = $Atestado['type'];
                    $file_file = file_get_contents($Atestado['tmp_name']);

                    $anexo_batida = "'{$file_nome}|{$file_size}|{$file_type}|".base64_encode($file_file)."'";
                    $possui_anexo = 1;
                    
                    unset($arquivos[$key2], $Atestado);
                    break;
                }
            }
            
            $query = " INSERT INTO zcrmportal_ponto_horas 
                (
                    dtponto,
                    movimento,
                    chapa,
                    coligada,
                    {$campo_batida},
                    {$campo_natureza},
                    {$campo_justificativa},
                    {$campo_referencia},
                    usucad,
                    dtcadastro,
                    status,
                    codfilial,
                    anexo_batida,
                    possui_anexo
                ) VALUES (
                    '{$dados['referencia_batida']}',
                    '1',
                    '{$chapa}',
                    '{$coligada}',
                    '".h2m($dados['horario_batida'])."',
                    '{$dados['natureza_batida']}',
                    '{$dados['justificativa_batida']}',
                    '{$dados['data_batida']}',
                    '{$this->log_id}',
                    '{$this->now}',
                    '1',
                    {$codfilial},
                    {$anexo_batida},
                    {$possui_anexo}
                )
            ";
            // echo $query;exit();
            $this->dbportal->query($query);

            if($this->dbportal->affectedRows() > 0){
                
                $chapa_session  = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
                $insertID       = $this->dbportal->insertID();

                if(self::isGestorOrLiderAprovador($chapa) && $chapa_session != null && $chapa_session != $chapa){
                    $mAprova = model('Ponto/AprovaModel');
                    $mAprova->aprovaBatidaRH($insertID);
                }
                return responseJson('success', 'Batida incluida com sucesso.');
            }

            return responseJson('error', 'Falha ao incluir batida.');

        } catch (\Exception $e) {
            return responseJson('error', 'Erro interno: ' . $e);
        }

    }

    public function AlterarBatida($dados, $arquivos){

        $coligada = session()->get("func_coligada");

        try {

            foreach($dados['dados'] as $key => $Dados){
                $Dados = json_decode($Dados, true);
                $Dados = $Dados[0];
               
                if($Dados['tipo'] == "U"){

                    $query = " 
                        SELECT 
                            max(id) id 
                        FROM 
                            zcrmportal_ponto_horas  
                        WHERE 
                            ".h2m($Dados['batida_default'])." = COALESCE(ent1, ent2, ent3, ent4, ent5, sai1, sai2, sai3, sai4, sai5)
                        AND COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) = '".dtEn($Dados['data'], true)."'
                        AND movimento = '1'
                        AND status not in ('S')
                        AND coligada = '{$coligada}'
                        AND chapa = '{$Dados['chapa']}'
                    ";
                    $result = $this->dbportal->query($query);
                    
                    $query = "
                        UPDATE 
                            zcrmportal_ponto_horas
                        SET 
                            ent1 = CASE WHEN ent1 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            ent2 = CASE WHEN ent2 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            ent3 = CASE WHEN ent3 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            ent4 = CASE WHEN ent4 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            sai1 = CASE WHEN sai1 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            sai2 = CASE WHEN sai2 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            sai3 = CASE WHEN sai3 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            sai4 = CASE WHEN sai4 IS NOT NULL THEN ".h2m($Dados['batida'])." ELSE NULL END,
                            motivo_reprova = NULL,
                            natent1 = CASE WHEN natent1 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            natent2 = CASE WHEN natent2 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            natent3 = CASE WHEN natent3 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            natent4 = CASE WHEN natent4 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            natsai1 = CASE WHEN natsai1 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            natsai2 = CASE WHEN natsai2 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            natsai3 = CASE WHEN natsai3 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            natsai4 = CASE WHEN natsai4 IS NOT NULL THEN ".(int)$Dados['natureza']." ELSE NULL END,
                            dtponto = '".dtEn($Dados['data_ref'], true)."',
                            dtrefent1 = CASE WHEN dtrefent1 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END,
                            dtrefent2 = CASE WHEN dtrefent2 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END,
                            dtrefent3 = CASE WHEN dtrefent3 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END,
                            dtrefent4 = CASE WHEN dtrefent4 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END,
                            dtrefsai1 = CASE WHEN dtrefsai1 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END,
                            dtrefsai2 = CASE WHEN dtrefsai2 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END,
                            dtrefsai3 = CASE WHEN dtrefsai3 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END,
                            dtrefsai4 = CASE WHEN dtrefsai4 IS NOT NULL THEN '".dtEn($Dados['data'], true)."' ELSE NULL END
                        WHERE 
                                ".h2m($Dados['batida_default'])." = COALESCE(ent1, ent2, ent3, ent4, ent5, sai1, sai2, sai3, sai4, sai5)
                            AND COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) = '".dtEn($Dados['data'], true)."'
                            AND movimento = '1'
                            AND status not in ('S')
                            AND coligada = '{$coligada}'
                            AND chapa = '{$Dados['chapa']}'
                    ";
                 
                    $this->dbportal->query($query);

                    if($result){
                        $insertID       = $result->getResultArray()[0]['id'];
                        $chapa_session  = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

                        if(self::isGestorOrLiderAprovador($Dados['chapa']) && $chapa_session != null && $chapa_session != $Dados['chapa']){
                            $mAprova = model('Ponto/AprovaModel');
                            $mAprova->aprovaBatidaRH($insertID);
                        }
                    }
                    // echo $query;

                }else
                if($Dados['tipo'] == "I"){
                    
                    // pega o numero da proxima batida
                    $query = "
                        SELECT 
                            COUNT(*) / 2 + 1 numero_batida
                        FROM 
                            zcrmportal_ponto_horas
                        WHERE 
                                chapa = '{$Dados['chapa']}'
                            AND COALESCE(ent1, ent2, ent3, ent4, ent5, sai1, sai2, sai3, sai4, sai5) IS NOT NULL
                            AND movimento ='1'
                            AND status NOT IN ('S')
                            AND COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) = '".dtEn($Dados['data'], true)."'
                    ";
                    $result = $this->dbportal->query($query);
                    if($result->getNumRows() > 0){
                      
                        $numero_batida = $result->getResultArray()[0]['numero_batida'];

                        $dados_nova_batida                         = array();
                        $dados_nova_batida['numero_batida']        = $numero_batida;
                        $dados_nova_batida['natureza_batida']      = $Dados['natureza'];
                        $dados_nova_batida['justificativa_batida'] = $Dados['justificativa'];
                        $dados_nova_batida['referencia_batida']    = $Dados['data_ref'];
                        $dados_nova_batida['data_batida']          = $Dados['data'];
                        $dados_nova_batida['horario_batida']       = $Dados['batida'];
                        $dados_nova_batida['codfilial']            = $Dados['codfilial'];
                        $dados_nova_batida['chapa']                = $Dados['chapa'];
                       
                        self::CadastrarBatida($dados_nova_batida, $arquivos, $Dados['tem_anexo']);
                        
                    }

                    

                }else
                if($Dados['tipo'] == "RM"){
                    
                    // altera data referencia e natureza da batida no RM
                    if(strlen(trim($Dados['data_ref'])) <= 0) continue;
                    $ini  = date('Ymd', strtotime($Dados['data']));
                    $fim  = date('Ymd', strtotime($Dados['data_ref']));
                    $diff = $ini-$fim;
                    
                    //if($diff != 0 && $diff != 1) continue;

                    $query = "
                        UPDATE 
                            ABATFUN 
                        SET 
                            DATAREFERENCIA = '{$Dados['data_ref']}',
                            NATUREZA       = '{$Dados['natureza']}',
                            RECMODIFIEDBY  = 'portal.{$_SESSION['log_id']}',
                            RECMODIFIEDON  = '".date('Y-m-d H:i:s')."'
                        WHERE
                                CODCOLIGADA = '{$coligada}'
                            AND CHAPA       = '{$Dados['chapa']}'
                            AND IDAAFDT     = '{$Dados['idaafdt']}'
                            AND BATIDA      = '".h2m($Dados['batida'])."'
                    ";
                   
                    if($Dados['idaafdt'] != 'null') $this->dbrm->query($query);

                }
            }

            return responseJson('success', 'Alteração realizada com sucesso.');

        } catch (\Exception $e) {
            return responseJson('error', 'Erro interno: ' . $e);
        }

    }

    public function CadastrarAbono($dados, $arquivos){

        $coligada = session()->get("func_coligada");
        $chapa    = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

        try {

            $sucesso = false;
            foreach($dados['dados'] as $key => $Dados){
                $Dados = json_decode($Dados, true);
                $Dados = $Dados[0];
                if($Dados['tipo'] == "U"){

                    if(strlen(trim($Dados['inicio_default'])) > 0 && strlen(trim($Dados['termino_default'])) > 0){

                        $query = " 
                            SELECT 
                                max(id) id 
                            FROM 
                                zcrmportal_ponto_horas
                            WHERE
                                abn_horaini = '".h2m($Dados['inicio_default'])."'
                            AND abn_horafim = '".h2m($Dados['termino_default'])."'
                            AND dtponto = '".dtEn($Dados['data'], true)."'
                            AND movimento = '{$Dados['tipo_ocorrencia']}'
                            AND status = '1'
                            AND coligada = '{$coligada}'
                            AND chapa = '{$Dados['chapa']}'
                        ";
                        $result = $this->dbportal->query($query);

                        $query = " 
                            UPDATE 
                                zcrmportal_ponto_horas
                            SET
                                abn_horaini = '".h2m($Dados['inicio'])."',
                                abn_horafim = '".h2m($Dados['termino'])."',
                                justificativa_abono_tipo = '".$Dados['just_abono_tipo']."',
                                motivo_reprova = NULL
                            WHERE
                                    abn_horaini = '".h2m($Dados['inicio_default'])."'
                                AND abn_horafim = '".h2m($Dados['termino_default'])."'
                                AND dtponto = '".dtEn($Dados['data'], true)."'
                                AND movimento = '{$Dados['tipo_ocorrencia']}'
                                AND status = '1'
                                AND coligada = '{$coligada}'
                                AND chapa = '{$Dados['chapa']}'
                        ";
                        $this->dbportal->query($query);

                        if($this->dbportal->affectedRows() > 0) $sucesso = true;

                        if($result){
                            $insertID       = $result->getResultArray()[0]['id'];
                            $chapa_session  = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

                            if(self::isGestorOrLiderAprovador($Dados['chapa']) && $chapa_session != null && $chapa_session != $Dados['chapa']){
                                $mAprova = model('Ponto/AprovaModel');
                                $mAprova->aprovaBatidaRH($insertID);
                            }
                        }
                    }

                }else
                if($Dados['tipo'] == "I"){

                    $atestado = "NULL";
                    $possui_atestado = "NULL";
                    if($Dados['tem_anexo'] == 1){
                        foreach($arquivos as $key2 => $Atestado){

                            $file_nome = $Atestado['name'];
                            $file_size = $Atestado['size'];
                            $file_type = $Atestado['type'];
                            $file_file = file_get_contents($Atestado['tmp_name']);

                            $atestado = "'{$file_nome}|{$file_size}|{$file_type}|".base64_encode($file_file)."'";
                            $possui_atestado = 1;

                            unset($arquivos[$key2], $Atestado);
                            break;
                        }
                    }

                    $query = " INSERT INTO zcrmportal_ponto_horas
                        (
                            dtponto,
                            movimento,
                            chapa,
                            coligada,
                            usucad,
                            dtcadastro,
                            status,
                            justificativa_abono_tipo,
                            abn_dtini,
                            abn_dtfim,
                            abn_horaini,
                            abn_horafim,
                            abn_codabono,
                            abono_atestado,
                            possui_anexo,
                            codfilial
                        ) VALUES (
                            '{$Dados['data']}',
                            '{$Dados['tipo_ocorrencia']}',
                            '{$Dados['chapa']}',
                            '{$coligada}',
                            '{$this->log_id}',
                            '{$this->now}',
                            '1',
                            '{$Dados['just_abono_tipo']}',
                            '{$Dados['data']}',
                            '{$Dados['data']}',
                            '".h2m($Dados['inicio'])."',
                            '".h2m($Dados['termino'])."',
                            '{$Dados['codabono']}',
                            {$atestado},
                            {$possui_atestado},
                            '{$Dados['codfilial']}'
                        )
                    ";
                    $this->dbportal->query($query);

                    if($this->dbportal->affectedRows() > 0){
                       $sucesso = true;
                        $chapa_session  = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
                        $insertID       = $this->dbportal->insertID();

                        if(self::isGestorOrLiderAprovador($Dados['chapa']) && $chapa_session != null && $chapa_session != $Dados['chapa']){
                            $mAprova = model('Ponto/AprovaModel');
                            $mAprova->aprovaBatidaRH($insertID);
                        }
                    }

                }
            }

            return ($sucesso)
                ? responseJson('success', 'Abono solicitado com sucesso.')
                : responseJson('error', 'Falha ao solicitar abono.');

        } catch (\Exception $e) {
            return responseJson('error', 'Erro interno: ' . $e);
        }

    }

    public function ListarSolicitacaoAbono($periodo, $chapa, $tipo = false){

        if($periodo === NULL) return false;

        $coligada   = session()->get("func_coligada");
        $chapa      = $chapa;
        $dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim    = dtEn(substr($periodo, 10, 10));
        $ftTipo     = ($tipo) ? " AND movimento = '{$tipo}' " : " AND movimento IN ('5','6') ";

        $query = " SELECT * FROM zcrmportal_ponto_horas WHERE chapa = '{$chapa}' AND coligada = '{$coligada}' {$ftTipo} AND dtponto BETWEEN '{$dataInicio}' AND '{$dataFim}' AND status NOT IN ('S') AND usu_delete IS NULL ";
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
    }

    public function HistoricoSolicitacaoAbonos($dataPonto, $chapa){

        $coligada   = session()->get("func_coligada");

        $query = " SELECT id, abn_horaini, abn_horafim, status, justificativa_abono_tipo, abn_codabono, SUBSTRING(abono_atestado, 0, CHARINDEX('|', abono_atestado)) nome_anexo FROM zcrmportal_ponto_horas WHERE chapa = '".$chapa."' AND coligada = '{$coligada}' AND movimento in ('5', '6') AND dtponto = '".$dataPonto."' AND usu_delete IS NULL";
        
        $result = $this->dbportal->query($query);
        return $result->getResultArray();
    }

    public function ExcluirAbonoRM($dados){

        try{           
            $query = "SELECT * FROM zcrmportal_ponto_horas WHERE id = '".$dados['id']."'";
            $result = $this->dbportal->query($query);

            if ($result && $result->getNumRows() > 0) {
                $row = $result->getRow();
                $query = "DELETE FROM AABONFUN WHERE CHAPA = '".$row->chapa."' AND CODCOLIGADA = '".session()->get("func_coligada")."' 
                AND DATA = '".$row->dtponto."' AND CODABONO = '".$row->abn_codabono."'";

                $qry = $this->dbrm->query($query);

                $query = "UPDATE zcrmportal_ponto_horas SET usu_delete = '".session()->get('log_id')."', dt_delete = '".date('d-m-Y H:i:s')."' WHERE id = '".$dados['id']."'";
                
                $qry = $this->dbportal->query($query);

                if($qry){
                    return responseJson('success', 'Abono excluído com sucesso!');
                } else {
                    return responseJson('error', 'Não foi possível excluir abono!');                    
                }
            }

        }catch(\Exception $e){

            return $e->getMessage();
        }
    }

    public function ExcluirAbonoPT($dados){

        try{           
            
            $query = "UPDATE zcrmportal_ponto_horas SET usu_delete = '".session()->get('log_id')."', dt_delete = GETDATE() WHERE id = '".$dados['id']."'";
            
            $qry = $this->dbportal->query($query);

            if($qry){
                return responseJson('success', 'Abono excluído com sucesso!');
            } else {
                return responseJson('error', 'Não foi possível excluir abono!');                    
            }

        }catch(\Exception $e){

            return $e->getMessage();
        }
    }

    public function ListarAbono(){

        $coligada = session()->get("func_coligada");

        $query = " SELECT CODIGO, DESCRICAO, ATESTADOOBRIGATORIO FROM AABONO WHERE CODCOLIGADA = '{$coligada}' AND ATIVOPORTAL = 1 ORDER BY DESCRICAO ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function saldoAnteriorBancoDeHoras($chapa, $periodo){
        
        $periodo = substr($periodo, 0, 10);
        $periodoFormatado = \DateTime::createFromFormat('d/m/Y', $periodo)->format('Y-m-d');
        $data = new \DateTime($periodoFormatado);
        $data->sub(new \DateInterval('P1D'));
        $dataFinal = $data->format('Y-m-d');

        switch(DBPORTAL_TIPO){
            case 'sqlserver':
            $query = "
                    SELECT 
                        SUM(SALDO) SALDO 
                    FROM (
                    
                        SELECT
                            C.DATA,
                            CODEVENTO
                                                    
                            ,(CASE WHEN CODEVENTO IN ('001','002') THEN 'Débito' 
                                WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                                WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') THEN 'Crédito Dobro'
                            ELSE 'Crédito Normal' END)TIPO,
                            C.VALOR,
                            (CASE WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                        THEN C.VALOR*
                                        (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                                WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1')
                                WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO NOT IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                        THEN C.VALOR		 
                            END) CREDITO,                        

                            (CASE WHEN CODEVENTO     IN ('001','002') THEN C.VALOR END) DEBITO,
                        
                            (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                                (CASE WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                    AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                    AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                                            THEN C.VALORCOMPENSADO * 
                                                            (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                    AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                    AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') ELSE C.VALORCOMPENSADO END) 
                            END) COMPENSADO,
                        
                            (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                            C.VALORLANCADO 
                            END) PAGO,
                        
                            (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN '' ELSE
                                (CONVERT(VARCHAR,INICIOPERMESALTERADO,103) + ' - ' + CONVERT(VARCHAR, FIMPERMESALTERADO,103))
                            END) MOVIMENTO,
                        
                            CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN
                                CASE WHEN C.CODEVENTO IN ('001','002') THEN
                                    ((C.VALOR*
                                        (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                            - (0+0) )*-1
                                    ELSE
                                (  (C.VALOR - (0+0)) *
                                        (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                            END 
                            
                            ELSE 
                            
                            CASE WHEN C.CODEVENTO IN ('001','002') THEN
                                ((C.VALOR*
                                    (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                        - (VALORCOMPENSADO+VALORLANCADO) )*-1
                                ELSE
                            (  (C.VALOR - (VALORCOMPENSADO+VALORLANCADO)) *
                                    (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                        END 
                            END SALDO									 
                            
                        FROM 
                            ABANCOHORFUNDETALHE C,
                            PFUNC A, 
                            PFUNCAO D, 
                            PSECAO B, 
                            APARFUN E
                        WHERE 
                                A.CODCOLIGADA = C.CODCOLIGADA
                            AND A.CHAPA       = C.CHAPA
                            AND A.CODCOLIGADA = D.CODCOLIGADA
                            AND A.CODFUNCAO   = D.CODIGO
                            AND A.CODCOLIGADA = B.CODCOLIGADA
                            AND A.CODSECAO    = B.CODIGO
                            AND C.CODCOLIGADA = E.CODCOLIGADA
                            AND C.CHAPA       = E.CHAPA
                            AND A.CODCOLIGADA = '{$this->coligada}'
                            AND C.DATA       >= '2000-01-01'
                            AND C.DATA       <= '".$dataFinal."'
                            AND C.CHAPA       = '{$chapa}'                                        		
                    )X"; 
                    break;

                    case 'oracle':

                    $query = "
                        SELECT 
                        SUM(SALDO) AS SALDO 
                    FROM (
                        SELECT
                            C.DATA,
                            CODEVENTO,
                            CASE
                                WHEN CODEVENTO IN ('0001', '0002') THEN 'Débito'
                                WHEN CODEVENTO IN (
                                    SELECT CODEVEPTO
                                    FROM AEVEPCOL M, PEVENTO N
                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                        AND M.CODEVEREL = N.CODIGO
                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                        AND C.CODEVENTO = M.CODEVEPTO
                                        AND M.CODPARCOL = E.CODPARCOL
                                        AND M.CONSPERCENT = '1'
                                ) THEN 'Crédito Dobro'
                                ELSE 'Crédito Normal'
                            END AS TIPO,
                            C.VALOR,
                            CASE
                                WHEN CODEVENTO NOT IN ('0001', '0002')
                                    AND CODEVENTO IN (
                                        SELECT CODEVEPTO
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    ) THEN C.VALOR * (
                                        SELECT N.PORCINCID
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    )
                                WHEN CODEVENTO NOT IN ('0001', '0002')
                                    AND CODEVENTO NOT IN (
                                        SELECT CODEVEPTO
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    ) THEN C.VALOR		 
                            END AS CREDITO,
                            CASE WHEN CODEVENTO IN ('0001', '0002') THEN C.VALOR END AS DEBITO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN 0
                                ELSE
                                    CASE
                                        WHEN CODEVENTO IN (
                                            SELECT CODEVEPTO
                                            FROM AEVEPCOL M, PEVENTO N
                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                                AND M.CODEVEREL = N.CODIGO
                                                AND C.CODCOLIGADA = M.CODCOLIGADA
                                                AND C.CODEVENTO = M.CODEVEPTO
                                                AND M.CODPARCOL = E.CODPARCOL
                                                AND M.CONSPERCENT = '1'
                                        ) THEN C.VALORCOMPENSADO * (
                                            SELECT N.PORCINCID
                                            FROM AEVEPCOL M, PEVENTO N
                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                                AND M.CODEVEREL = N.CODIGO
                                                AND C.CODCOLIGADA = M.CODCOLIGADA
                                                AND C.CODEVENTO = M.CODEVEPTO
                                                AND M.CODPARCOL = E.CODPARCOL
                                                AND M.CONSPERCENT = '1'
                                        )
                                        ELSE C.VALORCOMPENSADO
                                    END
                            END AS COMPENSADO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN 0
                                ELSE C.VALORLANCADO
                            END AS PAGO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN ''
                                ELSE TO_CHAR(INICIOPERMESALTERADO, 'DD/MM/YYYY') || ' - ' || TO_CHAR(FIMPERMESALTERADO, 'DD/MM/YYYY')
                            END AS MOVIMENTO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN
                                    CASE
                                        WHEN C.CODEVENTO IN ('0001', '0002') THEN
                                            ((C.VALOR *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL)) - (0 + 0)) * -1
                                        ELSE
                                            ((C.VALOR - (0 + 0)) *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL))
                                    END
                                ELSE
                                    CASE
                                        WHEN C.CODEVENTO IN ('0001', '0002') THEN
                                            ((C.VALOR *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL)) - (VALORCOMPENSADO + VALORLANCADO)) * -1
                                        ELSE
                                            ((C.VALOR - (VALORCOMPENSADO + VALORLANCADO)) *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL))
                                    END
                            END AS SALDO
                        FROM
                            ABANCOHORFUNDETALHE C,
                            PFUNC A,
                            PFUNCAO D,
                            PSECAO B,
                            APARFUN E
                        WHERE
                            A.CODCOLIGADA = C.CODCOLIGADA
                                AND A.CHAPA = C.CHAPA
                                AND A.CODCOLIGADA = D.CODCOLIGADA
                                AND A.CODFUNCAO = D.CODIGO
                                AND A.CODCOLIGADA = B.CODCOLIGADA
                                AND A.CODSECAO = B.CODIGO
                                AND C.CODCOLIGADA = E.CODCOLIGADA
                                AND C.CHAPA = E.CHAPA
                                AND A.CODCOLIGADA = '{$this->coligada}'
                                AND C.DATA >= TO_DATE('2000-01-01', 'YYYY-MM-DD')
                                AND C.DATA <= TO_DATE('" . $dataFinal . "', 'YYYY-MM-DD')
                                AND C.CHAPA = '{$chapa}'
                    ) X";
                    break;
        }
        // echo '<pre>'.$query.'</pre>';exit();
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function saldoMesBancoDeHoras($chapa, $periodo){
        
         $periodoIni = substr($periodo, 0, 10);
        
        $periodoIniFormatado = \DateTime::createFromFormat('d/m/Y', $periodoIni)->format('Y-m-d');
        $data = new \DateTime($periodoIniFormatado);
        $dataIni = $data->format('Y-m-d');

        $periodoFim = substr($periodo, 10);

        $periodoFimFormatado = \DateTime::createFromFormat('d/m/Y', $periodoFim)->format('Y-m-d');
        $data = new \DateTime($periodoFimFormatado);
        $dataFim = $data->format('Y-m-d');

        switch(DBPORTAL_TIPO){
            case 'sqlserver':
                $query = "
                        SELECT 
                            SUM(SALDO) SALDO 
                        FROM (
                        
                            SELECT
                                C.DATA,
                                CODEVENTO
                                                        
                                ,(CASE WHEN CODEVENTO IN ('001','002') THEN 'Débito' 
                                    WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                    AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                    AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') THEN 'Crédito Dobro'
                                ELSE 'Crédito Normal' END)TIPO,
                                C.VALOR,
                                (CASE WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                            THEN C.VALOR*
                                            (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                    AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                    AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1')
                                    WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO NOT IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                            THEN C.VALOR		 
                                END) CREDITO,                        

                                (CASE WHEN CODEVENTO     IN ('001','002') THEN C.VALOR END) DEBITO,
                            
                                (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                                    (CASE WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                                                THEN C.VALORCOMPENSADO * 
                                                                (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') ELSE C.VALORCOMPENSADO END) 
                                END) COMPENSADO,
                            
                                (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                                C.VALORLANCADO 
                                END) PAGO,
                            
                                (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN '' ELSE
                                    (CONVERT(VARCHAR,INICIOPERMESALTERADO,103) + ' - ' + CONVERT(VARCHAR, FIMPERMESALTERADO,103))
                                END) MOVIMENTO,
                            
                                CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN
                                    CASE WHEN C.CODEVENTO IN ('001','002') THEN
                                        ((C.VALOR*
                                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                                - (0+0) )*-1
                                        ELSE
                                    (  (C.VALOR - (0+0)) *
                                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                                END 
                                
                                ELSE 
                                
                                CASE WHEN C.CODEVENTO IN ('001','002') THEN
                                    ((C.VALOR*
                                        (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                            - (VALORCOMPENSADO+VALORLANCADO) )*-1
                                    ELSE
                                (  (C.VALOR - (VALORCOMPENSADO+VALORLANCADO)) *
                                        (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                            END 
                                END SALDO									 
                                
                            FROM 
                                ABANCOHORFUNDETALHE C,
                                PFUNC A, 
                                PFUNCAO D, 
                                PSECAO B, 
                                APARFUN E
                            WHERE 
                                    A.CODCOLIGADA = C.CODCOLIGADA
                                AND A.CHAPA       = C.CHAPA
                                AND A.CODCOLIGADA = D.CODCOLIGADA
                                AND A.CODFUNCAO   = D.CODIGO
                                AND A.CODCOLIGADA = B.CODCOLIGADA
                                AND A.CODSECAO    = B.CODIGO
                                AND C.CODCOLIGADA = E.CODCOLIGADA
                                AND C.CHAPA       = E.CHAPA
                                AND A.CODCOLIGADA = '{$this->coligada}'
                                AND C.DATA       >= '{$dataIni}'
                                AND C.DATA       <= '{$dataFim}'
                                AND C.CHAPA       = '{$chapa}'        		
                            )X";
                            break;
            case 'oracle':
                $query = "
                        SELECT 
                        SUM(SALDO) AS SALDO 
                    FROM (
                        SELECT
                            C.DATA,
                            CODEVENTO,
                            CASE
                                WHEN CODEVENTO IN ('0001', '0002') THEN 'Débito'
                                WHEN CODEVENTO IN (
                                    SELECT CODEVEPTO
                                    FROM AEVEPCOL M, PEVENTO N
                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                        AND M.CODEVEREL = N.CODIGO
                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                        AND C.CODEVENTO = M.CODEVEPTO
                                        AND M.CODPARCOL = E.CODPARCOL
                                        AND M.CONSPERCENT = '1'
                                ) THEN 'Crédito Dobro'
                                ELSE 'Crédito Normal'
                            END AS TIPO,
                            C.VALOR,
                            CASE
                                WHEN CODEVENTO NOT IN ('0001', '0002')
                                    AND CODEVENTO IN (
                                        SELECT CODEVEPTO
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    ) THEN C.VALOR * (
                                        SELECT N.PORCINCID
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    )
                                WHEN CODEVENTO NOT IN ('0001', '0002')
                                    AND CODEVENTO NOT IN (
                                        SELECT CODEVEPTO
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    ) THEN C.VALOR		 
                            END AS CREDITO,
                            CASE WHEN CODEVENTO IN ('0001', '0002') THEN C.VALOR END AS DEBITO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN 0
                                ELSE
                                    CASE
                                        WHEN CODEVENTO IN (
                                            SELECT CODEVEPTO
                                            FROM AEVEPCOL M, PEVENTO N
                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                                AND M.CODEVEREL = N.CODIGO
                                                AND C.CODCOLIGADA = M.CODCOLIGADA
                                                AND C.CODEVENTO = M.CODEVEPTO
                                                AND M.CODPARCOL = E.CODPARCOL
                                                AND M.CONSPERCENT = '1'
                                        ) THEN C.VALORCOMPENSADO * (
                                            SELECT N.PORCINCID
                                            FROM AEVEPCOL M, PEVENTO N
                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                                AND M.CODEVEREL = N.CODIGO
                                                AND C.CODCOLIGADA = M.CODCOLIGADA
                                                AND C.CODEVENTO = M.CODEVEPTO
                                                AND M.CODPARCOL = E.CODPARCOL
                                                AND M.CONSPERCENT = '1'
                                        )
                                        ELSE C.VALORCOMPENSADO
                                    END
                            END AS COMPENSADO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN 0
                                ELSE C.VALORLANCADO
                            END AS PAGO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN ''
                                ELSE TO_CHAR(INICIOPERMESALTERADO, 'DD/MM/YYYY') || ' - ' || TO_CHAR(FIMPERMESALTERADO, 'DD/MM/YYYY')
                            END AS MOVIMENTO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN
                                    CASE
                                        WHEN C.CODEVENTO IN ('0001', '0002') THEN
                                            ((C.VALOR *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL)) - (0 + 0)) * -1
                                        ELSE
                                            ((C.VALOR - (0 + 0)) *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL))
                                    END
                                ELSE
                                    CASE
                                        WHEN C.CODEVENTO IN ('0001', '0002') THEN
                                            ((C.VALOR *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL)) - (VALORCOMPENSADO + VALORLANCADO)) * -1
                                        ELSE
                                            ((C.VALOR - (VALORCOMPENSADO + VALORLANCADO)) *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL))
                                    END
                            END AS SALDO
                        FROM
                            ABANCOHORFUNDETALHE C,
                            PFUNC A,
                            PFUNCAO D,
                            PSECAO B,
                            APARFUN E
                        WHERE
                            A.CODCOLIGADA = C.CODCOLIGADA
                                AND A.CHAPA = C.CHAPA
                                AND A.CODCOLIGADA = D.CODCOLIGADA
                                AND A.CODFUNCAO = D.CODIGO
                                AND A.CODCOLIGADA = B.CODCOLIGADA
                                AND A.CODSECAO = B.CODIGO
                                AND C.CODCOLIGADA = E.CODCOLIGADA
                                AND C.CHAPA = E.CHAPA
                                AND A.CODCOLIGADA = '{$this->coligada}'
                                AND C.DATA >= TO_DATE('" . $dataIni ."' , 'YYYY-MM-DD')
                                AND C.DATA <= TO_DATE('" . $dataFim . "', 'YYYY-MM-DD') 
                                AND C.CHAPA = '{$chapa}'
                    ) X";
                    break;
        }

        // echo '<pre>'.$query.'</pre>';exit();
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function saldoAtualBancoDeHoras($chapa, $periodo){
        
        $periodo = substr($periodo, 10);
        $periodoFormatado = \DateTime::createFromFormat('d/m/Y', $periodo)->format('Y-m-d');
        $data = new \DateTime($periodoFormatado);
        $dataFinal = $data->format('Y-m-d');
        
        switch(DBPORTAL_TIPO){
            case 'sqlserver':
                $query = "
                        SELECT 
                            SUM(SALDO) SALDO 
                        FROM (
                        
                            SELECT
                                C.DATA,
                                CODEVENTO
                                                        
                                ,(CASE WHEN CODEVENTO IN ('001','002') THEN 'Débito' 
                                    WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                    AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                    AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') THEN 'Crédito Dobro'
                                ELSE 'Crédito Normal' END)TIPO,
                                C.VALOR,
                                (CASE WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                            THEN C.VALOR*
                                            (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                    AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                    AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1')
                                    WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO NOT IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                            THEN C.VALOR		 
                                END) CREDITO,                        

                                (CASE WHEN CODEVENTO     IN ('001','002') THEN C.VALOR END) DEBITO,
                            
                                (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                                    (CASE WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                                                THEN C.VALORCOMPENSADO * 
                                                                (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') ELSE C.VALORCOMPENSADO END) 
                                END) COMPENSADO,
                            
                                (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                                C.VALORLANCADO 
                                END) PAGO,
                            
                                (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN '' ELSE
                                    (CONVERT(VARCHAR,INICIOPERMESALTERADO,103) + ' - ' + CONVERT(VARCHAR, FIMPERMESALTERADO,103))
                                END) MOVIMENTO,
                            
                                CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN
                                    CASE WHEN C.CODEVENTO IN ('001','002') THEN
                                        ((C.VALOR*
                                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                                - (0+0) )*-1
                                        ELSE
                                    (  (C.VALOR - (0+0)) *
                                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                                END 
                                
                                ELSE 
                                
                                CASE WHEN C.CODEVENTO IN ('001','002') THEN
                                    ((C.VALOR*
                                        (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                            - (VALORCOMPENSADO+VALORLANCADO) )*-1
                                    ELSE
                                (  (C.VALOR - (VALORCOMPENSADO+VALORLANCADO)) *
                                        (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                            END 
                                END SALDO									 
                                
                            FROM 
                                ABANCOHORFUNDETALHE C,
                                PFUNC A, 
                                PFUNCAO D, 
                                PSECAO B, 
                                APARFUN E
                            WHERE 
                                    A.CODCOLIGADA = C.CODCOLIGADA
                                AND A.CHAPA       = C.CHAPA
                                AND A.CODCOLIGADA = D.CODCOLIGADA
                                AND A.CODFUNCAO   = D.CODIGO
                                AND A.CODCOLIGADA = B.CODCOLIGADA
                                AND A.CODSECAO    = B.CODIGO
                                AND C.CODCOLIGADA = E.CODCOLIGADA
                                AND C.CHAPA       = E.CHAPA
                                AND A.CODCOLIGADA = '{$this->coligada}'
                                AND C.DATA       >= '2000-01-01'
                                AND C.DATA       <= '{$dataFinal}'
                                AND C.CHAPA       = '{$chapa}'
                                            
                                    /* Para Saldo Anterior [C.DATA >= '2000-01-01'(chumbado) AND C.DATA <= perini(menos um dia)] */
                                    /* Para Saldo do Mês [C.DATA >= perini AND C.DATA <= perfim]*/
                                    /*Para Saldo Atual [C.DATA >= '2000-01-01'(chumbado) AND C.DATA <= perfim]*/		
                            )X";
                            break;

            case 'oracle': 
                $query = "
                        SELECT 
                        SUM(SALDO) AS SALDO 
                    FROM (
                        SELECT
                            C.DATA,
                            CODEVENTO,
                            CASE
                                WHEN CODEVENTO IN ('0001', '0002') THEN 'Débito'
                                WHEN CODEVENTO IN (
                                    SELECT CODEVEPTO
                                    FROM AEVEPCOL M, PEVENTO N
                                    WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                        AND M.CODEVEREL = N.CODIGO
                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                        AND C.CODEVENTO = M.CODEVEPTO
                                        AND M.CODPARCOL = E.CODPARCOL
                                        AND M.CONSPERCENT = '1'
                                ) THEN 'Crédito Dobro'
                                ELSE 'Crédito Normal'
                            END AS TIPO,
                            C.VALOR,
                            CASE
                                WHEN CODEVENTO NOT IN ('0001', '0002')
                                    AND CODEVENTO IN (
                                        SELECT CODEVEPTO
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    ) THEN C.VALOR * (
                                        SELECT N.PORCINCID
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    )
                                WHEN CODEVENTO NOT IN ('0001', '0002')
                                    AND CODEVENTO NOT IN (
                                        SELECT CODEVEPTO
                                        FROM AEVEPCOL M, PEVENTO N
                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                            AND M.CODEVEREL = N.CODIGO
                                            AND C.CODCOLIGADA = M.CODCOLIGADA
                                            AND C.CODEVENTO = M.CODEVEPTO
                                            AND M.CODPARCOL = E.CODPARCOL
                                            AND M.CONSPERCENT = '1'
                                    ) THEN C.VALOR		 
                            END AS CREDITO,
                            CASE WHEN CODEVENTO IN ('0001', '0002') THEN C.VALOR END AS DEBITO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN 0
                                ELSE
                                    CASE
                                        WHEN CODEVENTO IN (
                                            SELECT CODEVEPTO
                                            FROM AEVEPCOL M, PEVENTO N
                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                                AND M.CODEVEREL = N.CODIGO
                                                AND C.CODCOLIGADA = M.CODCOLIGADA
                                                AND C.CODEVENTO = M.CODEVEPTO
                                                AND M.CODPARCOL = E.CODPARCOL
                                                AND M.CONSPERCENT = '1'
                                        ) THEN C.VALORCOMPENSADO * (
                                            SELECT N.PORCINCID
                                            FROM AEVEPCOL M, PEVENTO N
                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA
                                                AND M.CODEVEREL = N.CODIGO
                                                AND C.CODCOLIGADA = M.CODCOLIGADA
                                                AND C.CODEVENTO = M.CODEVEPTO
                                                AND M.CODPARCOL = E.CODPARCOL
                                                AND M.CONSPERCENT = '1'
                                        )
                                        ELSE C.VALORCOMPENSADO
                                    END
                            END AS COMPENSADO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN 0
                                ELSE C.VALORLANCADO
                            END AS PAGO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN ''
                                ELSE TO_CHAR(INICIOPERMESALTERADO, 'DD/MM/YYYY') || ' - ' || TO_CHAR(FIMPERMESALTERADO, 'DD/MM/YYYY')
                            END AS MOVIMENTO,
                            CASE
                                WHEN FIMPERMESALTERADO = TO_DATE('2050-01-01', 'YYYY-MM-DD') THEN
                                    CASE
                                        WHEN C.CODEVENTO IN ('0001', '0002') THEN
                                            ((C.VALOR *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL)) - (0 + 0)) * -1
                                        ELSE
                                            ((C.VALOR - (0 + 0)) *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL))
                                    END
                                ELSE
                                    CASE
                                        WHEN C.CODEVENTO IN ('0001', '0002') THEN
                                            ((C.VALOR *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL)) - (VALORCOMPENSADO + VALORLANCADO)) * -1
                                        ELSE
                                            ((C.VALOR - (VALORCOMPENSADO + VALORLANCADO)) *
                                                (SELECT
                                                    CASE
                                                        WHEN CONSPERCENT = '1' THEN N.PORCINCID
                                                        ELSE 1
                                                    END
                                                FROM
                                                    AEVEPCOL M, PEVENTO N
                                                WHERE
                                                    M.CODCOLIGADA = N.CODCOLIGADA
                                                        AND M.CODEVEREL = N.CODIGO
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA
                                                        AND C.CODEVENTO = M.CODEVEPTO
                                                        AND M.CODPARCOL = E.CODPARCOL))
                                    END
                            END AS SALDO
                        FROM
                            ABANCOHORFUNDETALHE C,
                            PFUNC A,
                            PFUNCAO D,
                            PSECAO B,
                            APARFUN E
                        WHERE
                            A.CODCOLIGADA = C.CODCOLIGADA
                                AND A.CHAPA = C.CHAPA
                                AND A.CODCOLIGADA = D.CODCOLIGADA
                                AND A.CODFUNCAO = D.CODIGO
                                AND A.CODCOLIGADA = B.CODCOLIGADA
                                AND A.CODSECAO = B.CODIGO
                                AND C.CODCOLIGADA = E.CODCOLIGADA
                                AND C.CHAPA = E.CHAPA
                                AND A.CODCOLIGADA = '{$this->coligada}'
                                AND C.DATA >= TO_DATE('2000-01-01', 'YYYY-MM-DD')
                                AND C.DATA <= TO_DATE('" . $dataFinal . "', 'YYYY-MM-DD')
                                AND C.CHAPA = '{$chapa}'
                    ) X";
                    break;         
        }
        // echo '<pre>'.$query.'</pre>';exit();
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //exclui batida do RM
    public function ExcluirBatidaRM($dados){

        try{
            if(strlen(trim($dados['justificativa'])) <= 0 ) return responseJson('error', 'Justificativa não informada.');

            $query = " SELECT
                            *
                            FROM 
                                ABATFUN
                            WHERE 
                                IDAAFDT = '".$dados['idaafdt']."'";

            $qry = $this->dbrm->query($query);
            $res = ($qry) ? $qry->getResultArray() : array();
        
            $atualizou = false;
            
            $wsTotvs = model('WsrmModel');

            if ($res) {

                // xml para delete de batida
                $xml_abatfun['CODCOLIGADA']    = $res[0]['CODCOLIGADA'];
                $xml_abatfun['CHAPA']          = $res[0]['CHAPA'];
                $xml_abatfun['DATA']           = dtEn($res[0]['DATA'], true).'T00:00:00';
                $xml_abatfun['BATIDA']         = $res[0]['BATIDA'];
                $xml_abatfun['NATUREZA']       = $res[0]['NATUREZA'];
                $xml_abatfun['IDAAFDT']        = $res[0]['IDAAFDT'];
                $xml_abatfun['JUSTIFICA']      = trim(addslashes($dados["justificativa"]));
                $xml_abatfun['NOME']           = $_SESSION['log_nome'];
                $xml_abatfun['RECCREATEDBY']   = 'portal.'.$_SESSION['log_id'];
                $xml_abatfun['RECCREATEDON']   = date('Y-m-d\TH:i:s');
                $xml_abatfun['RECMODIFIEDBY']  = 'portal.'.$_SESSION['log_id'];
                $xml_abatfun['RECMODIFIEDON']  = date('Y-m-d\TH:i:s');

                // converte para XML
                $xml_html = new \SimpleXMLElement('<ABatFun/>');
                to_xml($xml_html, $xml_abatfun);
                $xml_batida = $xml_html->asXML();

                $contexto = "CODSISTEMA=A;CODCOLIGADA={$_SESSION['func_coligada']};CODUSUARIO=IntegraPortal1";
            
                $result = $wsTotvs->wsDataServerDelete('PtoBatidaData', $xml_batida, $contexto);
                
                // -----response esperado----
                // 4;000237;18/03/2023 00:00:00;4804;000237;18/03/2023 00:00:00;480
                
                $response = explode(';', $result);
                
                if($result == 'Exclusão de registro(s) realizado com sucesso'){
                    return responseJson('error', 'Não foi possivel excluir batida!');
                }else{
                    $wsTotvs->ws_recalculo_ponto($res[0]['CHAPA'], dtEn($res[0]['DATA'], true));
                    return responseJson('success', 'Batida excluida com sucesso!');
                }

                $atualizou = ($response[0] == $_SESSION['func_coligada'] && ($response[1] ?? '') == $res[0]['chapa']) ? true : false;

                

            }
           
        }catch(\Exception $e){

        return $e->getMessage();
        }
    }

    //exclui batida do Portal
    public function ExcluirBatidaPT($dados){

        $query = "
        DELETE FROM zcrmportal_ponto_horas 
            WHERE '".h2m($dados['horario_batida'])."' = COALESCE(ent1, ent2, ent3, ent4, ent5, sai1, sai2, sai3, sai4, sai5) 
            AND dtponto = '".$dados['data_ref']."' 
            --AND COALESCE(dtrefent1, dtrefent2, dtrefent3, dtrefent4, dtrefent5, dtrefsai1, dtrefsai2, dtrefsai3, dtrefsai4, dtrefsai5) = '".$dados['data_ref']."' 
            AND status NOT IN ('S') 
            AND coligada = '{$this->coligada}' 
            AND chapa = '".$dados['chapa']."'";
            
        $res = $this->dbportal->query($query);
        //echo $query;

        return ($res)
                ? responseJson('success', 'Batida excluida com sucesso.')
                : responseJson('error', 'Falha ao excluir batida.');
    }

    public function SaldoBancoHorasEquipe($periodo, $chapa){
        
        $periodoIni = substr($periodo, 0, 10);
       
        $periodoIniFormatado = \DateTime::createFromFormat('d/m/Y', $periodoIni)->format('Y-m-d');
        $data = new \DateTime($periodoIniFormatado);
        $dataIni = $data->format('Y-m-d');

        $periodoFim = substr($periodo, 10);

        $periodoFimFormatado = \DateTime::createFromFormat('d/m/Y', $periodoFim)->format('Y-m-d');
        $data = new \DateTime($periodoFimFormatado);
        $dataFim = $data->format('Y-m-d');
        $dataFimAnterior = date('Y-m-d', strtotime('-1 days', strtotime($dataIni)));

            // SALDO DO PERIODO
           
       
    $query=" 
    with
        FUNCIONARIOS AS (        


        SELECT CHAPA, CODFILIAL, NOME, CODSECAO, CODCOLIGADA, CODFUNCAO COD_FCO FROM PFUNC WHERE CHAPA = '{$chapa}' AND CODCOLIGADA = '{$this->coligada}'       


    ),
        
    SALDO_ANTERIOR AS (
        
        
        
        
                        
        
        
        SELECT 
                CHAPA, NOME, SUM(SALDO) SALDO, 0 MES_POSITIVO, 0 MES_NEGATIVO,  CODCOLIGADA 
            FROM (
            
                SELECT
                	
                    A.CODCOLIGADA,
                        A.CHAPA,
                        A.NOME,
                    C.DATA,
                    CODEVENTO
                                            
                    ,(CASE WHEN CODEVENTO IN ('001','002') THEN 'Débito' 
                        WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') THEN 'Crédito Dobro'
                    ELSE 'Crédito Normal' END)TIPO,
                    C.VALOR,
                    (CASE WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                THEN C.VALOR*
                                (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N  
                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1')
                        WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO NOT IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                THEN C.VALOR		 
                    END) CREDITO,                        
        
                    (CASE WHEN CODEVENTO     IN ('001','002') THEN C.VALOR END) DEBITO,
                
                    (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                        (CASE WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                            AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                            AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                                    THEN C.VALORCOMPENSADO * 
                                                    (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                            AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                            AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') ELSE C.VALORCOMPENSADO END) 
                    END) COMPENSADO,
                
                    (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                    C.VALORLANCADO 
                    END) PAGO,
                
                    (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN '' ELSE
                        (CONVERT(VARCHAR,INICIOPERMESALTERADO,103) + ' - ' + CONVERT(VARCHAR, FIMPERMESALTERADO,103))
                    END) MOVIMENTO,
                
                    CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN
                        CASE WHEN C.CODEVENTO IN ('001','002') THEN
                            ((C.VALOR*
                                (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                    - (0+0) )*-1
                            ELSE
                        (  (C.VALOR - (0+0)) *
                                (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                    END 
                    
                    ELSE 
                    
                    CASE WHEN C.CODEVENTO IN ('001','002') THEN
                        ((C.VALOR*
                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                - (VALORCOMPENSADO+VALORLANCADO) )*-1
                        ELSE
                    (  (C.VALOR - (VALORCOMPENSADO+VALORLANCADO)) *
                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                END 
                    END SALDO									 
                    
                FROM 
                    ABANCOHORFUNDETALHE C,
                    PFUNC A, 
                    PFUNCAO D, 
                    PSECAO B, 
                    APARFUN E,
                    FUNCIONARIOS F
                WHERE 
                        A.CODCOLIGADA = C.CODCOLIGADA
                    AND A.CHAPA       = C.CHAPA
                    AND A.CODCOLIGADA = D.CODCOLIGADA
                    AND A.CODFUNCAO   = D.CODIGO
                    AND A.CODCOLIGADA = B.CODCOLIGADA
                    AND A.CODSECAO    = B.CODIGO
                    AND C.CODCOLIGADA = E.CODCOLIGADA
                    AND C.CHAPA       = E.CHAPA
                    AND A.CODCOLIGADA = F.CODCOLIGADA
                    AND A.CHAPA = F.CHAPA
                    AND C.DATA       >= '1900-01-01'
                    AND C.DATA       <= '{$dataFimAnterior}' 
                    
                    
                )X
                
                GROUP BY CHAPA, NOME, CODCOLIGADA
        
        
        
        ),
        
        SALDO_PERIODO AS (
        
        SELECT 
                CHAPA, NOME, 0 SALDO, SUM(CREDITO) MES_POSITIVO, SUM(DEBITO) MES_NEGATIVO,  CODCOLIGADA 
            FROM (
            
                SELECT
                    A.CODCOLIGADA,
                        A.CHAPA,
                        A.NOME,
                    C.DATA,
                    CODEVENTO
                                            
                    ,(CASE WHEN CODEVENTO IN ('001','002') THEN 'Débito' 
                        WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') THEN 'Crédito Dobro'
                    ELSE 'Crédito Normal' END)TIPO,
                    C.VALOR,
                    (CASE WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                THEN C.VALOR*
                                (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N  
                                                        WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                        AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                        AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1')
                        WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO NOT IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                THEN C.VALOR		 
                    END) CREDITO,                        
        
                    (CASE WHEN CODEVENTO     IN ('001','002') THEN C.VALOR END) DEBITO,
                
                    (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                        (CASE WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                            AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                            AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                                    THEN C.VALORCOMPENSADO * 
                                                    (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                            AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                            AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') ELSE C.VALORCOMPENSADO END) 
                    END) COMPENSADO,
                
                    (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                    C.VALORLANCADO 
                    END) PAGO,
                
                    (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN '' ELSE
                        (CONVERT(VARCHAR,INICIOPERMESALTERADO,103) + ' - ' + CONVERT(VARCHAR, FIMPERMESALTERADO,103))
                    END) MOVIMENTO,
                
                    CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN
                        CASE WHEN C.CODEVENTO IN ('001','002') THEN
                            ((C.VALOR*
                                (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                    - (0+0) )*-1
                            ELSE
                        (  (C.VALOR - (0+0)) *
                                (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                    END 
                    
                    ELSE 
                    
                    CASE WHEN C.CODEVENTO IN ('001','002') THEN
                        ((C.VALOR*
                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                - (VALORCOMPENSADO+VALORLANCADO) )*-1
                        ELSE
                    (  (C.VALOR - (VALORCOMPENSADO+VALORLANCADO)) *
                            (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                END 
                    END SALDO									 
                    
                FROM 
                    ABANCOHORFUNDETALHE C,
                    PFUNC A, 
                    PFUNCAO D, 
                    PSECAO B, 
                    APARFUN E,
                    FUNCIONARIOS F
                WHERE 
                        A.CODCOLIGADA = C.CODCOLIGADA
                    AND A.CHAPA       = C.CHAPA
                    AND A.CODCOLIGADA = D.CODCOLIGADA
                    AND A.CODFUNCAO   = D.CODIGO
                    AND A.CODCOLIGADA = B.CODCOLIGADA
                    AND A.CODSECAO    = B.CODIGO
                    AND C.CODCOLIGADA = E.CODCOLIGADA
                    AND C.CHAPA       = E.CHAPA
                    AND A.CODCOLIGADA = F.CODCOLIGADA
                    AND A.CHAPA = F.CHAPA
                    AND C.DATA       >= '{$dataIni}'
                    AND C.DATA       <= '".$dataFim."' 
                    
                    
                )X
                
                GROUP BY CHAPA, NOME, CODCOLIGADA
                
        
        ), 
        SALDO_TOTAL AS (
        
        
        
        
        SELECT 
                    CHAPA, NOME, SUM(SALDO) SALDO, 0 MES_POSITIVO, 0 MES_NEGATIVO,  CODCOLIGADA 
                FROM (
                
                    SELECT
                        A.CODCOLIGADA,
                        A.CHAPA,
                        A.NOME,
                        C.DATA,
                        CODEVENTO
                                                
                        ,(CASE WHEN CODEVENTO IN ('001','002') THEN 'Débito' 
                            WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                            AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                            AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') THEN 'Crédito Dobro'
                        ELSE 'Crédito Normal' END)TIPO,
                        C.VALOR,
                        (CASE WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                    THEN C.VALOR*
                                    (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N  
                                                            WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                            AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                            AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1')
                            WHEN CODEVENTO NOT IN ('001','002') AND CODEVENTO NOT IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                    THEN C.VALOR		 
                        END) CREDITO,                        
            
                        (CASE WHEN CODEVENTO     IN ('001','002') THEN C.VALOR END) DEBITO,
                    
                        (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                            (CASE WHEN CODEVENTO IN (SELECT CODEVEPTO FROM AEVEPCOL M, PEVENTO N 
                                                                WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') 
                                                        THEN C.VALORCOMPENSADO * 
                                                        (SELECT N.PORCINCID FROM AEVEPCOL M, PEVENTO N 
                                                                WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO 
                                                                AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO 
                                                                AND M.CODPARCOL = E.CODPARCOL AND M.CONSPERCENT = '1') ELSE C.VALORCOMPENSADO END) 
                        END) COMPENSADO,
                    
                        (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN 0 ELSE
                        C.VALORLANCADO 
                        END) PAGO,
                    
                        (CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN '' ELSE
                            (CONVERT(VARCHAR,INICIOPERMESALTERADO,103) + ' - ' + CONVERT(VARCHAR, FIMPERMESALTERADO,103))
                        END) MOVIMENTO,
                    
                        CASE WHEN FIMPERMESALTERADO = '2050-01-01' THEN
                            CASE WHEN C.CODEVENTO IN ('001','002') THEN
                                ((C.VALOR*
                                    (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                        - (0+0) )*-1
                                ELSE
                            (  (C.VALOR - (0+0)) *
                                    (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                        END 
                        
                        ELSE 
                        
                        CASE WHEN C.CODEVENTO IN ('001','002') THEN
                            ((C.VALOR*
                                (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                    - (VALORCOMPENSADO+VALORLANCADO) )*-1
                            ELSE
                        (  (C.VALOR - (VALORCOMPENSADO+VALORLANCADO)) *
                                (SELECT (CASE WHEN CONSPERCENT = '1' THEN N.PORCINCID ELSE 1 END) FROM AEVEPCOL M, PEVENTO N WHERE M.CODCOLIGADA = N.CODCOLIGADA AND M.CODEVEREL = N.CODIGO AND C.CODCOLIGADA = M.CODCOLIGADA AND C.CODEVENTO = M.CODEVEPTO AND M.CODPARCOL = E.CODPARCOL) )
                                    END 
                        END SALDO									 
                        
                    FROM 
                        ABANCOHORFUNDETALHE C,
                        PFUNC A, 
                        PFUNCAO D, 
                        PSECAO B, 
                        APARFUN E,
                        FUNCIONARIOS F
                    WHERE 
                            A.CODCOLIGADA = C.CODCOLIGADA
                        AND A.CHAPA       = C.CHAPA
                        AND A.CODCOLIGADA = D.CODCOLIGADA
                        AND A.CODFUNCAO   = D.CODIGO
                        AND A.CODCOLIGADA = B.CODCOLIGADA
                        AND A.CODSECAO    = B.CODIGO
                        AND C.CODCOLIGADA = E.CODCOLIGADA
                        AND C.CHAPA       = E.CHAPA
                        AND A.CODCOLIGADA = F.CODCOLIGADA
                        AND A.CHAPA = F.CHAPA
                        AND C.DATA       >= '1900-01-01'
                        AND C.DATA       <= '".$dataFim."' 
                        
                        
                    )X
                    
                    GROUP BY CHAPA, NOME, CODCOLIGADA
        
        
        
        
        
        )
        
        
        
        SELECT A.CHAPA, A.NOME, ISNULL(B.SALDO,0) AS SALDO_ANTERIOR, ISNULL(C.SALDO,0) AS SALDO_PERIODO, ISNULL(D.SALDO,0) AS SALDO_TOTAL, ISNULL(C.MES_POSITIVO,0) MES_POSITIVO, ISNULL(C.MES_NEGATIVO,0)*-1 MES_NEGATIVO
        FROM FUNCIONARIOS A 
        LEFT JOIN SALDO_ANTERIOR B ON A.CODCOLIGADA= B.CODCOLIGADA AND A.chapa = B.CHAPA
        LEFT JOIN SALDO_PERIODO C ON A.CODCOLIGADA= C.CODCOLIGADA AND A.chapa = C.CHAPA
        LEFT JOIN SALDO_TOTAL D ON A.CODCOLIGADA= D.CODCOLIGADA AND A.chapa = D.CHAPA";

        // exit('<pre>'.$query);
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;


    }

    public function ListarJustificativa($tipo)
    {
        // 1 = falta | 2 = Atraso | 3 = Extra | 4 = Ajuste
        $query = " SELECT * FROM zcrmportal_ponto_motivos WHERE tipo = '{$tipo}' AND codcoligada = '{$this->coligada}' ORDER BY CAST(descricao AS VARCHAR) ";
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function CadastrarJustificativaExtra($dados)
    {

        try{

            $this->dbportal->query(" DELETE FROM zcrmportal_ponto_justificativa_func WHERE dtponto = '".dtEn($dados['data'], true)."' AND chapa = '{$dados['chapa']}' AND coligada = '{$this->coligada}' ");

            $query = " INSERT INTO zcrmportal_ponto_justificativa_func
                (
                    dtponto,
                    chapa,
                    coligada,
                    justificativa,
                    usucad,
                    tipo
                ) VALUES (
                    '".dtEn($dados['data'], true)."',
                    '{$dados['chapa']}',
                    '{$this->coligada}',
                    '{$dados['justificativa']}',
                    '{$this->log_id}',
                    3
                )
            ";
            $this->dbportal->query($query);
            if($this->dbportal->affectedRows() > 0){

                notificacao('success', '<b>Justificativa de Extra</b> cadastrada com sucesso.');
                return responseJson('success', '<b>Justificativa de Extra</b> cadastrada com sucesso.');

            }else{
                return responseJson('error', 'Falha ao cadastrar justificativa de extra.');
            }

        } catch (\Exception $e) {
            return responseJson('error', '<b>Erro interno:</b> ' . $e);
        }

    }

    public function ListarJustificativaExtraEspelho($periodo, $chapa)
    {

        /*$dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim    = dtEn(substr($periodo, 10, 10));

        $query = "";*/

    }

    public function isGestor($dados = false)
    {
        try {

            if($dados){
                if(($dados['rh'] ?? false) || ($dados['perfilRH'] ?? false)) return true;
            }

            $mHierarquia = model('HierarquiaModel');
            $Secoes   = $mHierarquia->ListarHierarquiaSecaoPodeVer();
            $isLider  = $mHierarquia->isLider();
           
            if($Secoes){
                if($isLider){
                 
                    return false;
                }else{
                
                    return true;
                }
            }
          
            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function isGestorOrLider($dados = false)
    {
        try {


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
                $filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
            }
            //-----------------------------------------
            
            // monta o where das seções
            if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
            if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
            if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

            if(($dados['rh'] ?? false) || ($dados['perfilRH'] ?? false)) return true;
            if($filtro_secao_lider != "" || $filtro_secao_gestor != "") return true;

            return false;

        } catch (\Exception $e) {
            return false;
        }
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

    public function CadastrarGestorExcecao($request)
    {

        $chapa          = $request['chapa'] ?? null;
        $data_limite    = $request['data_limite'] ?? null;

        if($chapa == null || strlen(trim($chapa)) <= 0) return responseJson('error', '<b>Gestor</b> não selecionado');
        if($data_limite == null || strlen(trim($data_limite)) <= 0) return responseJson('error', '<b>Data Limite</b> não informada');

        $check = $this->dbportal->query("select count(chapa) qtde from zcrmportal_ponto_excecao_gestor where chapa = '{$chapa}' AND codcoligada = '{$this->coligada}'");
        if($check->getResultArray()[0]['qtde'] > 0) return responseJson('error', '<b>Gestor</b> já cadastrado na exceção');

        $insert = [
            'chapa'         => $chapa,
            'codcoligada'   => $this->coligada,
            'data_limite'   => $data_limite,
            'usucad'        => $this->log_id,
            'dtcad'         => $this->now
        ];

        $query = $this->dbportal
            ->table('zcrmportal_ponto_excecao_gestor')
            ->insert($insert);

        return  ($query) 
                ? responseJson('success', 'Gestor cadastrado com sucesso')
                : responseJson('error', 'Falha ao cadastrar gestor');

    }

    public function listaGestorExcecao()
    {
        $result = $this->dbportal->query("
            SELECT 
                a.id,
                a.chapa,
                a.data_limite,
                b.NOME nome
            FROM 
                zcrmportal_ponto_excecao_gestor a
                LEFT JOIN ".DBRM_BANCO."..PFUNC b ON b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS AND b.CODCOLIGADA = a.codcoligada
            WHERE
                a.codcoligada = '{$this->coligada}'
            ORDER BY
                b.NOME
        ");
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
    }

    public function ExcluirGestorExcecao($request)
    {

        $id = $request['id'] ?? null;
        if($id == null || strlen(trim($id)) <= 0) return responseJson('error', '<b>ID</b> não enviado');

        $this->dbportal->query(" DELETE FROM zcrmportal_ponto_excecao_gestor WHERE id = '{$id}' ");

        return  ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Gestor excluído com sucesso.')
                : responseJson('error', 'Falha ao excluir Gestor');

    }

    public function gestorPossuiExcecao()
    {

        $chapa  = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        if($chapa != null){

            $result = $this->dbportal->query("SELECT * FROM zcrmportal_ponto_excecao_gestor WHERE codcoligada = '{$this->coligada}' AND chapa = '{$chapa}' AND data_limite >= '".date('Y-m-d')."'");
            if(!$result) return false;
            return ($result->getNumRows() > 0) 
                    ? $result->getResultArray()[0]['data_limite']
                    : false;

        }

        return false;

    }

    public function listaAbonoPendenteRH($chapa = null)
    {

        if($chapa == null) return false;

        $result = $this->dbrm->query(" SELECT * FROM AABONFUN WHERE CHAPA = '{$chapa}' AND CODCOLIGADA = '{$this->coligada}' AND DESCONSIDERA = 1 ");
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ExcluirConfigJustificativa($request)
    {

        $id     = $request['id'] ?? null;
        $tipo   = trim($request['tipo']) ?? null;
        if($id == null || strlen(trim($id)) <= 0) return responseJson('error', '<b>ID</b> não enviado');

        if($tipo == 51){
            return self::ExcluirMotivoOcorrencia($request);
        }
        
        $this->dbportal->query(" DELETE FROM zcrmportal_ponto_motivos WHERE id = '{$request['id']}' ");

        return  ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Justificativa excluída com sucesso.')
                : responseJson('error', 'Falha ao excluir justificativa');

    }

    public function AlterarConfigJustificativa($request)
    {

        $id         = $request['id'] ?? null;
        $descricao  = trim($request['descricao']) ?? null;
        $tipo       = trim($request['tipo']) ?? null;
        if($id == null || strlen(trim($id)) <= 0) return responseJson('error', '<b>ID</b> não enviado');
        if($descricao == null || strlen(trim($descricao)) <= 0) return responseJson('error', '<b>Descrição</b> não enviado');

        if($tipo == 51){
            return self::AlteraMotivoOcorrencia($request);
        }

        if($id == 0){
            $this->dbportal->query(" INSERT INTO zcrmportal_ponto_motivos (descricao, codcoligada, tipo) VALUES ('{$descricao}', '{$this->coligada}', '{$tipo}') ");
        }else{
            $this->dbportal->query(" UPDATE zcrmportal_ponto_motivos SET descricao = '{$descricao}' WHERE id = '{$id}' ");
        }

        return  ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Justificativa salva com sucesso.')
                : responseJson('error', 'Falha ao salvar justificativa');

    }

    public function ExcluirMotivoOcorrencia($request)
    {

        $id = $request['id'] ?? null;
        if($id == null || strlen(trim($id)) <= 0) return responseJson('error', '<b>ID</b> não enviado');
        
        $this->dbportal->query(" DELETE FROM zcrmportal_ocorrencia_motivo WHERE id = '{$request['id']}' ");

        return  ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Justificativa excluída com sucesso.')
                : responseJson('error', 'Falha ao excluir justificativa');

    }

    public function AlteraMotivoOcorrencia($request)
    {

        $id         = $request['id'] ?? null;
        $descricao  = trim($request['descricao']) ?? null;
        if($id == null || strlen(trim($id)) <= 0) return responseJson('error', '<b>ID</b> não enviado');
        if($descricao == null || strlen(trim($descricao)) <= 0) return responseJson('error', '<b>Descrição</b> não enviado');

        if($id == 0){
            $this->dbportal->query(" INSERT INTO zcrmportal_ocorrencia_motivo (descricao) VALUES ('{$descricao}') ");
        }else{
            $this->dbportal->query(" UPDATE zcrmportal_ocorrencia_motivo SET descricao = '{$descricao}' WHERE id = '{$id}' ");
        }

        return  ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Justificativa salva com sucesso.')
                : responseJson('error', 'Falha ao salvar justificativa');

    }

    public function saveAtsMacro($dados)
    {

        if($dados){
            foreach($dados as $key => $Macro){
                
                $existe = $this->dbportal
                    ->table('zcrmportal_ponto_ats_macro')
                    ->where('cpf', $Macro->cpf)
                    ->where('data_inicio_status', str_replace(['T', 'Z'], [' ', ''], $Macro->data_inicio_status))
                    ->where('status', $Macro->status)
                    ->get();

                if($existe->getNumRows() <= 0 && strlen(trim($Macro->cpf)) > 0){
                    $this->dbportal
                        ->table('zcrmportal_ponto_ats_macro')
                        ->insert([
                            'cpf' => $Macro->cpf,
                            'status' => $Macro->status,
                            'mensagem' => $Macro->mensagem,
                            'placa' => $Macro->placa,
                            'data_inicio_status' => str_replace(['T', 'Z'], [' ', ''], $Macro->data_inicio_status),
                            'data_fim_status' => str_replace(['T', 'Z'], [' ', ''], $Macro->data_fim_status),
                            'tempo' => $Macro->tempo,
                            'data_gravacao' => str_replace(['T', 'Z'], [' ', ''], $Macro->data_gravacao),
                            'data_importacao' => date('Y-m-d H:i:s')
                        ]);
                }


            }
        }

    }

    public function saveAtsTotalizador($dados)
    {

        if($dados){
            foreach($dados as $key => $Totalizador){
                
                $this->dbportal
                ->table('zcrmportal_ponto_ats_totalizador')
                ->insert([
                    'cpf' => $Totalizador->cpf,
                    'data_gravacao' => $Totalizador->data_gravacao,
                    'horas_em_espera' => $Totalizador->horas_em_espera,
                    'horas_de_direcao' => $Totalizador->horas_de_direcao,
                    'data_importacao' => date('Y-m-d H:i:s')
                ]);

            }
        }

    }

    public function CarregaMacro($request)
    {
        $perIni = $request['data'].' 00:00:00';
        $perFim = $request['data'].' 23:59:59';

        $result = $this->dbportal
            ->table('zcrmportal_ponto_ats_macro')
            ->select("status, CONCAT(CONVERT(VARCHAR, data_inicio_status, 103),' ', CONVERT(VARCHAR, data_inicio_status, 8)) data_inicio_status, CONCAT(CONVERT(VARCHAR, data_fim_status, 103),' ', CONVERT(VARCHAR, data_fim_status, 8)) data_fim_status, tempo, placa, CONCAT(CONVERT(VARCHAR, data_gravacao, 103),' ', CONVERT(VARCHAR, data_gravacao, 8)) data_gravacao")
            ->where("data_inicio_status BETWEEN '{$perIni}' AND '{$perFim}'")
            ->where('cpf', $request['cpf'])
            ->orderBy('data_inicio_status', 'ASC')
            ->orderBy('data_fim_status', 'ASC')
            ->get();
            
        return $result->getResultArray();

    }
    
}