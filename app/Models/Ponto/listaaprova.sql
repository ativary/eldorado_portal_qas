SELECT DISTINCT X.*
FROM (
        SELECT A.id,
            A.dtponto,
            null dtfolga,
            null codindice_folga,
            A.movimento,
            A.chapa,
            COALESCE(
                A.ent1,
                A.ent2,
                A.ent3,
                A.ent4,
                A.ent5,
                A.sai1,
                A.sai2,
                A.sai3,
                A.sai4,
                A.sai5
            ) batida,
            COALESCE(
                A.justent1,
                A.justent2,
                A.justent3,
                A.justent4,
                A.justent5,
                A.justsai1,
                A.justsai2,
                A.justsai3,
                A.justsai4,
                A.justsai5
            ) motivo,
            COALESCE(
                A.natent1,
                A.natent2,
                A.natent3,
                A.natent4,
                A.natent5,
                A.natsai1,
                A.natsai2,
                A.natsai3,
                A.natsai4,
                A.natsai5
            ) natureza,
            COALESCE(
                A.dtrefent1,
                A.dtrefent2,
                A.dtrefent3,
                A.dtrefent4,
                A.dtrefent5,
                A.dtrefsai1,
                A.dtrefsai2,
                A.dtrefsai3,
                A.dtrefsai4,
                A.dtrefsai5
            ) data_referencia,
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
                SELECT MAX(BB.CHAPA)
                FROM CorporeRMDEV..PPESSOA AA
                    INNER JOIN CorporeRMDEV..PFUNC BB ON BB.CODPESSOA = AA.CODIGO
                WHERE AA.CPF = C.login COLLATE Latin1_General_CI_AS
                    AND BB.CODCOLIGADA = A.coligada
                    AND (
                        SELECT TOP 1 REGISTRO
                        FROM (
                                SELECT CONCAT(CODCOLIGADA, '-', CHAPA) REGISTRO,
                                    CASE
                                        WHEN DATADEMISSAO IS NOT NULL
                                        AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                        ELSE '2900-12-31'
                                    END DATA
                                FROM CorporeRMDEV..PFUNC (NOLOCK)
                                WHERE CODCOLIGADA = BB.CODCOLIGADA
                                    AND CHAPA = BB.CHAPA
                                    AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                                    AND DATAADMISSAO <= a.dtponto
                            ) X
                        WHERE X.DATA >= a.dtponto
                        ORDER BY X.DATA ASC
                    ) IS NOT NULL
            ) chapa_solicitante,
            NULL codhorario,
            NULL horario,
            NULL codindice,
            NULL justificativa_escala,
            E.CPF,
            (
                SELECT TOP 1 HB.DESCRICAO
                FROM CorporeRMDEV..PFHSTSIT HA (NOLOCK)
                    INNER JOIN CorporeRMDEV..PCODSITUACAO HB (NOLOCK) ON HB.CODCLIENTE = HA.NOVASITUACAO
                WHERE HA.CODCOLIGADA = A.COLIGADA
                    AND HA.CHAPA = A.CHAPA COLLATE Latin1_General_CI_AS
                    AND (HA.DATAMUDANCA <= A.dtponto)
                ORDER BY DATAMUDANCA DESC
            ) CODSITUACAO,
            B.NOME,
            a.dtcadastro data_solicitacao,
            (
                SELECT max(CAST(BB.descricao AS VARCHAR))
                FROM zcrmportal_ponto_justificativa_func AA (NOLOCK)
                    INNER JOIN zcrmportal_ponto_motivos BB (NOLOCK) ON AA.justificativa = BB.id
                    AND AA.coligada = BB.codcoligada
                WHERE AA.coligada = A.coligada
                    AND AA.dtponto = A.dtponto
                    AND AA.chapa = A.chapa
            ) justificativa_excecao
        FROM zcrmportal_ponto_horas A (NOLOCK)
            INNER JOIN CorporeRMDEV..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
            AND B.CODCOLIGADA = A.coligada
            LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
            INNER JOIN CorporeRMDEV..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
        WHERE A.status in ('1', '2')
            AND A.coligada = '1'
            AND A.motivo_reprova IS NULL
            AND A.usu_delete IS NULL
            AND A.dtponto BETWEEN '2025-03-16' AND '2025-04-15'
        UNION ALL
        SELECT a.id,
            a.datamudanca dtponto,
            a.datamudanca_folga dtfolga,
            a.codindice_folga codindice_folga,
            CASE
                WHEN a.tipo = 1 THEN 21
                ELSE 22
            END movimento,
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
            CASE
                WHEN a.usuupload IS NULL THEN 0
                ELSE 1
            END possui_anexo,
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
                SELECT MAX(BB.CHAPA)
                FROM CorporeRMDEV..PPESSOA AA
                    INNER JOIN CorporeRMDEV..PFUNC BB ON BB.CODPESSOA = AA.CODIGO
                WHERE AA.CPF = C.login COLLATE Latin1_General_CI_AS
                    AND BB.CODCOLIGADA = A.coligada
                    AND (
                        SELECT TOP 1 REGISTRO
                        FROM (
                                SELECT CONCAT(CODCOLIGADA, '-', CHAPA) REGISTRO,
                                    CASE
                                        WHEN DATADEMISSAO IS NOT NULL
                                        AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                        ELSE '2900-12-31'
                                    END DATA
                                FROM CorporeRMDEV..PFUNC (NOLOCK)
                                WHERE CODCOLIGADA = BB.CODCOLIGADA
                                    AND CHAPA = BB.CHAPA
                                    AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                                    AND DATAADMISSAO <= a.datamudanca
                            ) X
                        WHERE X.DATA >= a.datamudanca
                        ORDER BY X.DATA ASC
                    ) IS NOT NULL
            ) chapa_solicitante,
            a.codhorario,
            d.DESCRICAO horario,
            a.codindice,
            CONCAT(
                CASE
                    WHEN justificativa_11_horas IS NOT NULL THEN CONCAT(
                        'Interjornada 11h: ',
                        a.justificativa_11_horas,
                        ', '
                    )
                    ELSE ''
                END,
                CASE
                    WHEN justificativa_6_dias IS NOT NULL THEN CONCAT(
                        '6 dias consecutivos: ',
                        a.justificativa_11_horas,
                        ', '
                    )
                    ELSE ''
                END,
                CASE
                    WHEN justificativa_6_meses IS NOT NULL THEN CONCAT(
                        'Troca inf. 6 meses: ',
                        a.justificativa_6_meses,
                        ', '
                    )
                    ELSE ''
                END,
                CASE
                    WHEN justificativa_3_dias IS NOT NULL THEN CONCAT(
                        'Troca inf. 72 horas: ',
                        a.justificativa_3_dias,
                        ', '
                    )
                    ELSE ''
                END,
                CASE
                    WHEN justificativa_periodo IS NOT NULL THEN CONCAT('Fora período: ', a.justificativa_periodo, ', ')
                    ELSE ''
                END
            ) justificativa_escala,
            E.CPF,
            (
                SELECT TOP 1 HB.DESCRICAO
                FROM CorporeRMDEV..PFHSTSIT HA (NOLOCK)
                    INNER JOIN CorporeRMDEV..PCODSITUACAO HB (NOLOCK) ON HB.CODCLIENTE = HA.NOVASITUACAO
                WHERE HA.CODCOLIGADA = A.COLIGADA
                    AND HA.CHAPA = A.CHAPA COLLATE Latin1_General_CI_AS
                    AND (HA.DATAMUDANCA <= A.datamudanca)
                ORDER BY DATAMUDANCA DESC
            ) CODSITUACAO,
            B.NOME,
            a.dtcad data_solicitacao,
            NULL justificativa_excecao
        FROM zcrmportal_escala a (NOLOCK)
            INNER JOIN CorporeRMDEV..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
            AND B.CODCOLIGADA = A.coligada
            LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
            LEFT JOIN CorporeRMDEV..AHORARIO D (NOLOCK) ON D.CODIGO = a.codhorario COLLATE Latin1_General_CI_AS
            AND D.CODCOLIGADA = a.coligada
            INNER JOIN CorporeRMDEV..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
        WHERE a.coligada = '1'
            and a.situacao in (10, 2)
            AND (
                a.datamudanca BETWEEN '2025-03-16' AND '2025-04-15'
                OR a.datamudanca_folga BETWEEN '2025-03-16' AND '2025-04-15'
            )
    ) X
ORDER BY X.chapa,
    X.dtponto