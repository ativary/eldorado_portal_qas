USE [CorporeRMDEV]
GO

/****** Object:  UserDefinedFunction [dbo].[AY_CRITICAS_BATIDAS_APONTADAS]    Script Date: 21/05/2025 10:16:17 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE FUNCTION [dbo].[AY_CRITICAS_BATIDAS_APONTADAS](@CODCOLIGADA INT, @DATAINIC DATE, @DATAFIM DATE)
RETURNS TABLE AS

RETURN (
SELECT id,
  coligada,
  chapa,
  dtponto,
  ent1,
  ent2,
  ent3,
  ent4,
  ent5,
  sai1,
  sai2,
  sai3,
  sai4,
  sai5,
  status,
  motivo_reprova,
  COALESCE(
    natent1,
    natent2,
    natent3,
    natent4,
    natent5,
    natsai1,
    natsai2,
    natsai3,
    natsai4,
    natsai5
  ) natureza,
  COALESCE(
    dtrefent1,
    dtrefent2,
    dtrefent3,
    dtrefent4,
    dtrefent5,
    dtrefsai1,
    dtrefsai2,
    dtrefsai3,
    dtrefsai4,
    dtrefsai5
  ) data_referencia
FROM PortalRHDEV..zcrmportal_ponto_horas (NOLOCK)
WHERE coligada = '1'
  AND status IN ('1', '2', '3', 'R')
  and dtponto BETWEEN @DATAINIC AND @DATAFIM 
  AND dt_delete IS NULL
  AND EXISTS (
    SELECT B.CHAPA
    FROM PFUNC B (NOLOCK)
    WHERE
      (
        SELECT TOP 1 REGISTRO
        FROM (
            SELECT CONCAT(CODCOLIGADA, '-', CHAPA) REGISTRO,
              CASE
                WHEN DATADEMISSAO IS NOT NULL
                AND CODSITUACAO = 'D' THEN DATADEMISSAO
                ELSE GETDATE()
              END DATA
            FROM CorporeRMDEV..PFUNC
            WHERE CODCOLIGADA = B.CODCOLIGADA
              AND CHAPA = B.CHAPA
              AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
          ) X
        WHERE X.DATA >= @DATAINIC
        ORDER BY X.DATA ASC
      ) IS NOT NULL
      AND CODCOLIGADA = @CODCOLIGADA
  )
);
GO


