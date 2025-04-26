WITH PER AS (
SELECT 
	CODCOLIGADA,
	INICIOMENSAL,
	FIMMENSAL
FROM
	CorporeRMDEV..APERIODO
WHERE
	ATIVO = 1
),

SUB AS (
SELECT 
	s.coligada,
	s.chapa_gestor,
	s.chapa_substituto,
	u.nome,
	u.email
FROM zcrmportal_hierarquia_gestor_substituto s
INNER JOIN zcrmportal_usuario u ON u.id = s.id_substituto
WHERE s.modulos like '%"6"%' AND s.dtfim >= GETDATE() AND s.inativo = 0
),

EML AS (
SELECT 
	DISTINCT
	A.CHAPA,
	A.NOME,
	A.CODCOLIGADA,
	C.email EMAIL
FROM
	CorporeRMDEV..PFUNC A,
	CorporeRMDEV..PPESSOA B,
	PortalRHDEV..zcrmportal_usuario C
WHERE
	A.CODPESSOA = B.CODIGO
	AND C.login = B.CPF COLLATE Latin1_General_CI_AS
	AND A.CODSITUACAO <> 'D'
),

PAR AS (
SELECT 
	COLIGADA,
	WFLOW_DIAS_NOTIF		D1,
	WFLOW_DIAS_NOTIF_ACIMA	D2
FROM zcrmportal_espelho_config
),

GES AS (
SELECT DISTINCT
	A.CODCOLIGADA,
	A.CHAPA,
	A.NOME,
	A.CODSECAO,
	C.id_hierarquia ID_HIERARQUIA,
	D.chapa GESTOR_CHAPA,
	E.NOME GESTOR_NOME
FROM 
	CorporeRMDEV..PFUNC A,
	zcrmportal_frente_trabalho B,
	zcrmportal_hierarquia_frentetrabalho C,
	zcrmportal_hierarquia_chapa D,
	CorporeRMDEV..PFUNC E
WHERE
	/*A.CODSITUACAO NOT IN ('D')
	AND */B.codsecao = A.CODSECAO COLLATE Latin1_General_CI_AS
	AND B.coligada = A.CODCOLIGADA
	AND B.id = C.id_frentetrabalho
	AND C.inativo IS NULL
	AND D.id_hierarquia = C.id_hierarquia
	AND D.inativo IS NULL
	AND D.chapa = E.CHAPA COLLATE Latin1_General_CI_AS
	AND D.coligada = E.CODCOLIGADA
	AND E.CODSITUACAO NOT IN ('D')
),

APR AS (
SELECT A.id,
    A.dtponto,
    A.movimento,
    A.chapa,
    A.coligada,
    A.status,
    E.CPF,
    B.NOME,
    a.dtcadastro data_solicitacao,
	GETDATE() data_hoje,
	CASE
		WHEN A.envio_gestor1 IS NULL OR DATEADD(DAY, ISNULL(R.D1, 1), A.envio_gestor1) <= GETDATE() THEN 'S'
		ELSE 'N'
	END envia_para_gestor1,
	A.envio_gestor1,
	R.D1,
	DATEADD(DAY, ISNULL(R.D1, 1), A.envio_gestor1) data1_calculada,
	CASE
		WHEN DATEADD(DAY, ISNULL(R.D2, 1), A.envio_gestor2) <= GETDATE() THEN 'S'
		ELSE 'N'
	END envia_para_gestor2,
	A.envio_gestor2,
	R.D2,
	DATEADD(DAY, ISNULL(R.D2, 1), A.envio_gestor2) data2_calculada
FROM zcrmportal_ponto_horas A (NOLOCK)
    INNER JOIN CorporeRMDEV..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
    AND B.CODCOLIGADA = A.coligada
    LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
    INNER JOIN CorporeRMDEV..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
	INNER JOIN PER P ON P.CODCOLIGADA = A.coligada
	LEFT JOIN PAR R ON R.COLIGADA = A.coligada
WHERE A.status in ('1', '2')
    AND A.coligada = '1'
    AND A.motivo_reprova IS NULL
    AND A.usu_delete IS NULL
    AND A.dtponto >= P.INICIOMENSAL
	/*AND (	A.envio_gestor1 IS NULL 
		OR	DATEADD(DAY, ISNULL(R.D1, 1), A.envio_gestor1) <= GETDATE()
		OR	DATEADD(DAY, ISNULL(R.D2, 1), A.envio_gestor2) <= GETDATE()
		)*/
		
	--AND A.dtponto BETWEEN P.INICIOMENSAL AND P.FIMMENSAL
	--and a.id = 1439734

UNION ALL

SELECT a.id,
    a.datamudanca dtponto,
    CASE
        WHEN a.tipo = 1 THEN 21
        ELSE 22
    END movimento,
    a.chapa,
    a.coligada,
    a.situacao status,
    E.CPF,
    B.NOME,
    a.dtcad data_solicitacao,
	GETDATE() data_hoje,
	CASE
		WHEN A.envio_gestor1 IS NULL OR DATEADD(DAY, ISNULL(R.D1, 1), A.envio_gestor1) <= GETDATE() THEN 'S'
		ELSE 'N'
	END envia_para_gestor1,
	A.envio_gestor1,
	R.D1,
	DATEADD(DAY, ISNULL(R.D1, 1), A.envio_gestor1) data1_calculada,
	CASE
		WHEN DATEADD(DAY, ISNULL(R.D2, 1), A.envio_gestor2) <= GETDATE() THEN 'S'
		ELSE 'N'
	END envia_para_gestor2,
	A.envio_gestor2,
	R.D2,
	DATEADD(DAY, ISNULL(R.D2, 1), A.envio_gestor2) data2_calculada
FROM zcrmportal_escala a (NOLOCK)
    INNER JOIN CorporeRMDEV..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
    AND B.CODCOLIGADA = A.coligada
    LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
    LEFT JOIN CorporeRMDEV..AHORARIO D (NOLOCK) ON D.CODIGO = a.codhorario COLLATE Latin1_General_CI_AS
    AND D.CODCOLIGADA = a.coligada
    INNER JOIN CorporeRMDEV..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
	INNER JOIN PER P ON P.CODCOLIGADA = A.coligada
	LEFT JOIN PAR R ON R.COLIGADA = A.coligada
WHERE a.coligada = '1'
    and a.situacao in (10)
	/*and a.id = 1439734
    AND (
        a.datamudanca BETWEEN P.INICIOMENSAL AND P.FIMMENSAL
        OR a.datamudanca_folga BETWEEN P.INICIOMENSAL AND P.FIMMENSAL
    )*/
	AND (
        a.datamudanca >= P.INICIOMENSAL
        OR a.datamudanca_folga >= P.INICIOMENSAL 
    )
	/*AND (	A.envio_gestor1 IS NULL 
		OR	DATEADD(DAY, ISNULL(R.D1, 1), A.envio_gestor1) <= GETDATE()
		OR	DATEADD(DAY, ISNULL(R.D2, 1), A.envio_gestor2) <= GETDATE()
		)*/
),

LID AS (
SELECT DISTINCT
	L.id_lider,
	O.chapa LIDER,
	A.* 
FROM APR A
INNER JOIN PER P ON P.CODCOLIGADA = A.coligada
LEFT JOIN zcrmportal_hierarquia_lider_func_ponto L ON L.coligada = A.coligada AND L.chapa = A.chapa AND L.inativo IS NULL
LEFT JOIN zcrmportal_hierarquia_lider_ponto O ON O.coligada = A.coligada AND O.id = L.id_lider AND O.inativo IS NULL AND O.perfim >= P.INICIOMENSAL
WHERE O.chapa IS NOT NULL AND A.envia_para_gestor1 = 'S'
),

FU1 AS (
SELECT DISTINCT COLIGADA, CHAPA FROM APR WHERE envia_para_gestor1 = 'S' OR envia_para_gestor2 = 'S'
),

FU2 AS (
SELECT DISTINCT COLIGADA, CHAPA FROM APR WHERE envia_para_gestor2 = 'S'
),

GE0 AS (
SELECT 
	F.COLIGADA,
	F.CHAPA,
	G.GESTOR_CHAPA
FROM FU1 F
LEFT JOIN GES G ON G.CODCOLIGADA = F.COLIGADA AND G.CHAPA = F.CHAPA COLLATE Latin1_General_CI_AS
),

GE1 AS (
SELECT 
	G.COLIGADA,
	G.CHAPA,
	ISNULL(G.GESTOR_CHAPA,H.CHAPA_GESTOR_IMEDIATO) GESTOR1_ACIMA,
	NULL GESTOR2_ACIMA
FROM GE0 G
LEFT JOIN CorporeRMDEV..CRM_HIERARQUIA3 H ON H.CODCOLIGADA = G.COLIGADA AND H.CHAPA = G.CHAPA COLLATE Latin1_General_CI_AS
),

GE2 AS (
SELECT 
	G1.COLIGADA,
	G1.CHAPA,
	G1.GESTOR1_ACIMA,
	IIF(G2.GESTOR_CHAPA=G1.GESTOR1_ACIMA,NULL,G2.GESTOR_CHAPA) GESTOR2_ACIMA
FROM GE1 G1
INNER JOIN FU2 F2 ON F2.COLIGADA = G1.COLIGADA AND F2.CHAPA = G1.CHAPA COLLATE Latin1_General_CI_AS
LEFT JOIN GES G2 ON G2.CODCOLIGADA = G1.COLIGADA AND G2.CHAPA = G1.GESTOR1_ACIMA COLLATE Latin1_General_CI_AS
),

GE3 AS (
SELECT 
	G.COLIGADA,
	G.CHAPA,
	G.GESTOR1_ACIMA,
	ISNULL(GESTOR2_ACIMA, H.CHAPA_GESTOR_IMEDIATO) GESTOR2_ACIMA
FROM GE2 G
LEFT JOIN CorporeRMDEV..CRM_HIERARQUIA3 H ON H.CODCOLIGADA = G.COLIGADA AND H.CHAPA = G.GESTOR1_ACIMA COLLATE Latin1_General_CI_AS
),

FUF AS (
SELECT 
	G1.GESTOR1_ACIMA,
	G2.GESTOR2_ACIMA,
	A.*
FROM APR A 
LEFT JOIN GE1 G1 ON G1.COLIGADA = A.COLIGADA AND G1.CHAPA = A.CHAPA COLLATE Latin1_General_CI_AS
LEFT JOIN GE3 G2 ON G2.COLIGADA = A.COLIGADA AND G2.CHAPA = A.CHAPA COLLATE Latin1_General_CI_AS
),

LG1 AS (
SELECT DISTINCT COLIGADA, GESTOR1_ACIMA GESTOR, ID, MOVIMENTO FROM FUF WHERE FUF.envia_para_gestor1 = 'S'
),

LG2 AS (
SELECT DISTINCT COLIGADA,GESTOR2_ACIMA GESTOR, ID, MOVIMENTO FROM FUF WHERE FUF.envia_para_gestor2 = 'S'
),

LG3 AS (
SELECT DISTINCT COLIGADA,LIDER GESTOR, ID, MOVIMENTO FROM LID WHERE LID.envia_para_gestor1 = 'S'
),

LGE AS (
SELECT * FROM LG1
UNION ALL
SELECT * FROM LG2
UNION ALL
SELECT * FROM LG3
),

LGU AS (
SELECT DISTINCT * FROM LGE
),

TGE AS (
SELECT 
	COLIGADA,
	GESTOR,
	MOVIMENTO,
	COUNT (MOVIMENTO) AS TOTAL
FROM LGE
GROUP BY COLIGADA, GESTOR, MOVIMENTO
),

GEN AS (
SELECT 
	TGE.COLIGADA,
	TGE.GESTOR,
	EML.NOME,
	EML.EMAIL,
	TGE.MOVIMENTO,
	CASE
		WHEN TGE.MOVIMENTO = 1 THEN 'Inclusão de batida'
		WHEN TGE.MOVIMENTO = 2 THEN 'Exclusão de batida'
		WHEN TGE.MOVIMENTO = 3 THEN 'Alteração de natureza'
		WHEN TGE.MOVIMENTO = 4 THEN 'Alteração jornada referência'
		WHEN TGE.MOVIMENTO = 5 THEN 'Abono de atrasos'
		WHEN TGE.MOVIMENTO = 6 THEN 'Abono de faltas'
		WHEN TGE.MOVIMENTO = 7 THEN 'Justificativa de exceção'
		WHEN TGE.MOVIMENTO = 8 THEN 'Altera atitude'
		WHEN TGE.MOVIMENTO = 9 THEN 'Falta não remunerada'
		WHEN TGE.MOVIMENTO = 21 THEN 'Troca de escala'
		WHEN TGE.MOVIMENTO = 22 THEN 'Troca de dia'
		ELSE 'Movimento '+CAST(TGE.MOVIMENTO AS VARCHAR)+' não identificado'
	END DESC_MOVIMENTO,
	TGE.TOTAL
FROM TGE
LEFT JOIN EML ON EML.CODCOLIGADA = TGE.coligada AND EML.CHAPA = TGE.GESTOR COLLATE Latin1_General_CI_AS
),

SEM AS(
SELECT * FROM GEN WHERE EMAIL IS NULL
),

GEM AS (
SELECT 
	G.COLIGADA,
	H.CHAPA_GESTOR_IMEDIATO COLLATE Latin1_General_CI_AS GESTOR,
	E.NOME,
	E.EMAIL,
	G.MOVIMENTO,
	G.DESC_MOVIMENTO,
	G.TOTAL
FROM SEM G 
LEFT JOIN CorporeRMDEV..CRM_HIERARQUIA3 H ON H.CODCOLIGADA = G.COLIGADA AND H.CHAPA = G.GESTOR COLLATE Latin1_General_CI_AS
LEFT JOIN EML E ON E.CODCOLIGADA = H.CODCOLIGADA AND E.CHAPA = H.CHAPA_GESTOR_IMEDIATO COLLATE Latin1_General_CI_AS
WHERE G.EMAIL IS NULL
),

FIN AS (
SELECT * FROM GEN WHERE EMAIL IS NOT NULL
UNION ALL
SELECT * FROM GEM
)

SELECT
	F.COLIGADA,
	F.GESTOR,
	F.NOME,
	F.EMAIL,
	F.MOVIMENTO,
	F.DESC_MOVIMENTO,
	F.TOTAL,
	S.chapa_substituto CHAPA_SUB,
	S.nome NOME_SUB,
	S.email EMAIL_SUB
FROM FIN F
LEFT JOIN SUB S ON S.COLIGADA = F.coligada AND S.chapa_gestor = F.GESTOR COLLATE Latin1_General_CI_AS
ORDER BY GESTOR
