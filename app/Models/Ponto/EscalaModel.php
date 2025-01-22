<?php
namespace App\Models\Ponto;

use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class EscalaModel extends Model {

    protected $dbportal;
    protected $dbrm;
    public $coligada;
    public $log_id;
    public $now;
    public $producao;
    
    public function __construct() {
        $this->dbportal   = db_connect('dbportal');
        $this->dbrm       = db_connect('dbrm');
        $this->coligada   = session()->get('func_coligada');
        $this->log_id     = session()->get('log_id');
        $this->now        = date('Y-m-d H:i:s');
        $this->producao   = (DBRM_BANCO == 'CorporeRMPRD') ? true : false;
    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- TROCA DE ESCALA -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Lista funcionários que pode ver
    // -------------------------------------------------------
    public function ListarEscalaFuncionarios($perfilRH = false){
    /*
        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        $isLider = $mHierarquia->isLider();

        $in_secao = " 1 = 2 ";
        if($Secoes){
            $in_secao = "";
            if($isLider){
                // lider
                foreach($Secoes as $key =>$Chapa){
                    $in_secao .= "'{$Chapa['chapa']}',";
                }
                $in_secao = " A.CHAPA IN (".rtrim($in_secao, ',').") ";
            }else{
                // gestor
                foreach($Secoes as $key =>$CodSecao){
                    $in_secao .= "'{$CodSecao['codsecao']}',";
                }
                $in_secao = " A.CODSECAO IN (".rtrim($in_secao, ',').") ";
            }
        }**************************************/

        //-----------------------------------------
        // filtro das chapas que o lider pode ver
        //-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
        $objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
        $isLider = $mHierarquia->isLider();
        
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
        $in_secao = " (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

        if($perfilRH) $in_secao = " 1=1 ";

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
                    {$in_secao}
                AND A.CODSITUACAO IN ('A')
                AND A.CODCOLIGADA = {$this->coligada}
                AND (
                    SELECT 
                        TOP 1 HP.UTILIZA
                    FROM 
                        AHSTUTILIZAPONTO HP
                    WHERE
                            HP.DATAINICIO <= GETDATE()
                        AND HP.CODCOLIGADA = A.CODCOLIGADA
                        AND HP.CHAPA = A.CHAPA
                    ORDER BY
                        HP.DATAINICIO DESC
                ) = 1
            ORDER BY
                A.NOME
        ";

        // echo '<pre>'.$query;exit();
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista horários do totvs
    // -------------------------------------------------------
    public function ListarEscalaHorario($chapa = null, $resFuncionario = null){

        $codsecao = null;
        if($resFuncionario !== null){
            foreach($resFuncionario as $key => $Funcionario){
                if($Funcionario['CHAPA'] == $chapa){
                    $codsecao = $Funcionario['CODSECAO'];
                    break;
                }
            }
        }

        $query = " SELECT * FROM AHORARIO WHERE CODCOLIGADA = '{$this->coligada}' AND INATIVO = 0 AND CODIGO IN (SELECT CODHORARIO FROM ZMDHORARIOESCALA WHERE CODCOLIGADA = '{$this->coligada}' AND CODSECAO = '{$codsecao}') ORDER BY DESCRICAO ";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista dados do horario do RM
    // -------------------------------------------------------
    public function ListarHorarioRM($codHorario)
    {

        $query = " SELECT * FROM AHORARIO WHERE CODCOLIGADA = '{$this->coligada}' AND CODIGO = '{$codHorario}' ";
        $result = $this->dbrm->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista indice do horário
    // -------------------------------------------------------
    public function ListarEscalaHorarioIndice($codhorario){

        $query = " /*SELECT INDINICIOHOR INDICE, DESCRICAO FROM AINDHOR WHERE CODCOLIGADA = {$this->coligada} AND CODHORARIO = '{$codhorario}' ORDER BY INDINICIOHOR*/ 

            SELECT
                INDICE,
                DESCRICAO,
                TIPO,
                dbo.MINTOTIME(ENTRADA1) ENTRADA1,
                dbo.MINTOTIME(SAIDA1) SAIDA1,
                dbo.MINTOTIME(ENTRADA2) ENTRADA2,
                dbo.MINTOTIME(SAIDA2) SAIDA2,
                RTRIM(ISNULL(dbo.MINTOTIME(ENTRADA1),'') + ' ' + ISNULL(dbo.MINTOTIME(SAIDA1),'') + ' ' + ISNULL(dbo.MINTOTIME(ENTRADA2),'') + ' ' + ISNULL(dbo.MINTOTIME(SAIDA2),'')) + ' [' + TIPO + ']' HORARIO


            FROM (

                SELECT 
                    A.INDINICIOHOR INDICE, 
                    A.DESCRICAO,
                    CASE
                        WHEN MIN(D.INICIO) IS NOT NULL THEN 'DESCANSO'
                        WHEN MIN(E.INICIO) IS NOT NULL THEN 'COMPENSADO'
                        ELSE 'TRABALHO'
                    END TIPO,
                    CASE
                        WHEN MIN(D.INICIO) IS NOT NULL THEN MIN(D.INICIO)
                        WHEN MIN(E.INICIO) IS NOT NULL THEN MIN(E.INICIO)
                        ELSE MIN(B.BATINICIO)
                    END ENTRADA1,	
                    MIN(CASE
                        WHEN G.INICIO IS NOT NULL THEN G.INICIO
                        ELSE NULL
                    END) SAIDA1,
                    MAX(CASE
                        WHEN G.FIM IS NOT NULL THEN G.FIM
                        ELSE NULL
                    END) ENTRADA2,                    
                    CASE
                        WHEN MAX(D.FIM) IS NOT NULL THEN MAX(D.FIM)
                        WHEN MAX(E.FIM) IS NOT NULL THEN MAX(E.FIM)
                        ELSE MAX(B.BATFIM)
                    END SAIDA2
                    
                FROM 
                    AINDHOR A
                    LEFT JOIN AJORHOR B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODHORARIO = A.CODHORARIO AND B.INDINICIO = A.INDINICIOHOR
                    LEFT JOIN AJORHOR C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODHORARIO = A.CODHORARIO AND C.INDINICIO = A.INDINICIOHOR
                    LEFT JOIN ABATHOR D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODHORARIO = A.CODHORARIO AND D.INDICE = A.INDINICIOHOR AND D.TIPO = 1--DESCANSO
                    LEFT JOIN ABATHOR E ON E.CODCOLIGADA = A.CODCOLIGADA AND E.CODHORARIO = A.CODHORARIO AND E.INDICE = A.INDINICIOHOR AND E.TIPO = 2--COMPENSADO
                    LEFT JOIN ABATHOR F ON F.CODCOLIGADA = A.CODCOLIGADA AND F.CODHORARIO = A.CODHORARIO AND F.INDICE = A.INDINICIOHOR AND F.TIPO = 3
                    LEFT JOIN ABATHOR G ON G.CODCOLIGADA = A.CODCOLIGADA AND G.CODHORARIO = A.CODHORARIO AND G.INDICE = A.INDINICIOHOR AND G.TIPO = 4--REFEIÇÃO
                WHERE 
                        A.CODCOLIGADA = {$this->coligada} 
                    AND A.CODHORARIO = '{$codhorario}' 

                GROUP BY
                    A.INDINICIOHOR,
                    A.DESCRICAO,
                    B.BATINICIO,
                    B.BATFIM
            )X

            ORDER BY INDICE
        
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Calcula Troca de Escala
    // -------------------------------------------------------
    public function CalculaTrocaDeEscala($dados){

        $id           = $dados['id'] ?? null;
        $data         = $dados['data'] ?? null;
        $chapa        = $dados['chapa'] ?? null;
        $codhorario   = $dados['codhorario'] ?? null;
        $indice       = $dados['indice'] ?? null;
        $qtde_dias    = 10;

        if($id != null){
            $query = " select projecao escala from zcrmportal_escala WHERE id = '{$id}' and situacao not in (0,8,9) ";
            $result = $this->dbportal->query($query);
            if($result->getNumRows() > 0){
                $response = $result->getResultArray();
                if(strlen(trim($response[0]['escala'])) > 0) exit($response[0]['escala']);
            }
        }

        //return responseJson('error', '<b>Colaborador</b> não informado.');
        if($data == null) return responseJson('error', '<b>Data</b> não informada.');
        if($chapa == null) return responseJson('error', '<b>Colaborador</b> não informado.');
        if($codhorario == null) return responseJson('error', '<b>Horário</b> não informado.');
        if($indice == null) return responseJson('error', '<b>Índice</b> não informado.');

        $data = dtBr($data);

        $query = "
            SELECT
                X.CODHORARIO, 
                CONVERT(VARCHAR, X.DATA, 103) DATA,
                X.CODINDICE, 
                X.CODCOLIGADA, 
                X.TIPO, 
                X.ENTRADA1, 
                X.SAIDA1,
                X.ENTRADA2,
                X.SAIDA2,
                ISNULL(B.CODSECAO, A.CODSECAO) CODSECAO,
                CASE WHEN D.DIAFERIADO IS NOT NULL THEN 1 ELSE 0 END DIAFERIADO

            FROM (

                SELECT * FROM dbo.CALCULO_HORARIO_PT1('{$data}', '{$chapa}', {$this->coligada})
                    
                UNION ALL
                
                SELECT * FROM dbo.CALCULO_HORARIO_PT2('{$data}', '{$codhorario}', {$indice}, {$this->coligada}, {$qtde_dias}, '{$chapa}') 

            )X
                LEFT JOIN PFUNC A ON A.CHAPA = '{$chapa}' AND A.CODCOLIGADA = {$this->coligada}
                LEFT JOIN PFHSTSEC B ON B.CHAPA = A.CHAPA AND B.CODCOLIGADA = A.CODCOLIGADA AND B.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTSEC WHERE CHAPA = B.CHAPA AND B.CODCOLIGADA = CODCOLIGADA AND DTMUDANCA <= CONVERT(DATETIME, DATA, 103))
                LEFT JOIN PSECAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = ISNULL(B.CODSECAO, A.CODSECAO)
                LEFT JOIN GFERIADO D ON D.CODCALENDARIO = C.CODCALENDARIO AND D.DIAFERIADO = CONVERT(DATETIME, DATA, 103)

            ORDER BY CONVERT(DATETIME, DATA, 103) ASC
        ";
        $result = $this->dbrm->query($query);
                
        if($result->getNumRows() > 0){
            $response = $result->getResultArray();
            $dadosJson = false;
            foreach($response as $key => $dadosResponse){

                $dadosJson[$key] = $dadosResponse;
                $dadosJson[$key]['DIA'] = diaSemana(dtEn($dadosResponse['DATA']), true);
                $dadosJson[$key]['DATAEN'] = dtEn($dadosResponse['DATA']);

                if((int)$dadosResponse['ENTRADA1'] >= 1440) $dadosResponse['ENTRADA1'] = 0;
                $dadosJson[$key]['ENTRADA1'] = m2h((int)$dadosResponse['ENTRADA1']);

                if((int)$dadosResponse['ENTRADA2'] >= 1440) $dadosResponse['ENTRADA2'] = 0;
                $dadosJson[$key]['ENTRADA2'] = m2h((int)$dadosResponse['ENTRADA2']);

                if((int)$dadosResponse['SAIDA1'] >= 1440) $dadosResponse['SAIDA1'] = 0;
                $dadosJson[$key]['SAIDA1'] = m2h((int)$dadosResponse['SAIDA1']);

                if((int)$dadosResponse['SAIDA2'] >= 1440) $dadosResponse['SAIDA2'] = 0;
                $dadosJson[$key]['SAIDA2'] = m2h((int)$dadosResponse['SAIDA2']);

                $dadosJson[$key]['ENTRADA1_MINUTO'] = h2m($dadosJson[$key]['ENTRADA1']);
                $dadosJson[$key]['ENTRADA2_MINUTO'] = h2m($dadosJson[$key]['ENTRADA2']);
                $dadosJson[$key]['SAIDA1_MINUTO'] = h2m($dadosJson[$key]['SAIDA1']);
                $dadosJson[$key]['SAIDA2_MINUTO'] = h2m($dadosJson[$key]['SAIDA2']);

            }

            return json_encode($dadosJson);
        }

        return false;

    }

    // -------------------------------------------------------
    // Calcula Troca de Escala
    // -------------------------------------------------------
    public function CalculaTrocaDeDia($dados){

        $id           = $dados['id'] ?? null;
        $tipo         = $dados['tipo'] ?? null;
        $data         = $dados['data'] ?? null;
        $chapa        = $dados['chapa'] ?? null;
        $codhorario   = $dados['codhorario'] ?? null;
        $indice       = $dados['indice'] ?? null;
        $qtde_dias    = 1;

        //return responseJson('error', '<b>Colaborador</b> não informado.');
        if($data == null) return responseJson('error', '<b>Data</b> não informada.');
        if($chapa == null) return responseJson('error', '<b>Colaborador</b> não informado.');
        if($codhorario == null) return responseJson('error', '<b>Horário</b> não informado.');
        if($indice == null) return responseJson('error', '<b>Índice</b> não informado.');

        if($id != null){

            $query = " select ".(($tipo == 'trabalho') ? 'projecao' : 'projecao_folga')." escala from zcrmportal_escala WHERE id = '{$id}' and situacao not in (0,8,9) ";
            $result = $this->dbportal->query($query);
            if($result->getNumRows() > 0){
                $response = $result->getResultArray();
                if(strlen(trim($response[0]['escala'])) > 0) exit($response[0]['escala']);
            }

        }

        $data = dtBr($data);

        $query = "
            SELECT
                X.CODHORARIO, 
                CONVERT(VARCHAR, X.DATA, 103) DATA,
                X.CODINDICE, 
                X.CODCOLIGADA, 
                X.TIPO, 
                X.ENTRADA1, 
                X.SAIDA1,
                X.ENTRADA2,
                X.SAIDA2,
                ISNULL(B.CODSECAO, A.CODSECAO) CODSECAO,
                CASE WHEN D.DIAFERIADO IS NOT NULL THEN 1 ELSE 0 END DIAFERIADO

            FROM (

                SELECT * FROM dbo.CALCULO_HORARIO_PT1('{$data}', '{$chapa}', {$this->coligada})
                    
                UNION ALL
                
                SELECT * FROM dbo.CALCULO_HORARIO_PT2('{$data}', '{$codhorario}', {$indice}, {$this->coligada}, {$qtde_dias}, '{$chapa}') 

            )X
                LEFT JOIN PFUNC A ON A.CHAPA = '{$chapa}' AND A.CODCOLIGADA = {$this->coligada}
                LEFT JOIN PFHSTSEC B ON B.CHAPA = A.CHAPA AND B.CODCOLIGADA = A.CODCOLIGADA AND B.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTSEC WHERE CHAPA = B.CHAPA AND B.CODCOLIGADA = CODCOLIGADA AND DTMUDANCA <= CONVERT(DATETIME, DATA, 103))
                LEFT JOIN PSECAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = ISNULL(B.CODSECAO, A.CODSECAO)
                LEFT JOIN GFERIADO D ON D.CODCALENDARIO = C.CODCALENDARIO AND D.DIAFERIADO = CONVERT(DATETIME, DATA, 103)

            ORDER BY CONVERT(DATETIME, DATA, 103) ASC
        ";
        $result = $this->dbrm->query($query);
                
        if($result->getNumRows() > 0){
            $response = $result->getResultArray();
            $dadosJson = false;
            foreach($response as $key => $dadosResponse){

                $dadosJson[$key] = $dadosResponse;
                $dadosJson[$key]['DIA'] = diaSemana(dtEn($dadosResponse['DATA']), true);
                $dadosJson[$key]['DATAEN'] = dtEn($dadosResponse['DATA']);

                if((int)$dadosResponse['ENTRADA1'] >= 1440) $dadosResponse['ENTRADA1'] = 0;
                $dadosJson[$key]['ENTRADA1'] = m2h((int)$dadosResponse['ENTRADA1']);

                if((int)$dadosResponse['ENTRADA2'] >= 1440) $dadosResponse['ENTRADA2'] = 0;
                $dadosJson[$key]['ENTRADA2'] = m2h((int)$dadosResponse['ENTRADA2']);

                if((int)$dadosResponse['SAIDA1'] >= 1440) $dadosResponse['SAIDA1'] = 0;
                $dadosJson[$key]['SAIDA1'] = m2h((int)$dadosResponse['SAIDA1']);

                if((int)$dadosResponse['SAIDA2'] >= 1440) $dadosResponse['SAIDA2'] = 0;
                $dadosJson[$key]['SAIDA2'] = m2h((int)$dadosResponse['SAIDA2']);

                $dadosJson[$key]['ENTRADA1_MINUTO'] = h2m($dadosJson[$key]['ENTRADA1']);
                $dadosJson[$key]['ENTRADA2_MINUTO'] = h2m($dadosJson[$key]['ENTRADA2']);
                $dadosJson[$key]['SAIDA1_MINUTO'] = h2m($dadosJson[$key]['SAIDA1']);
                $dadosJson[$key]['SAIDA2_MINUTO'] = h2m($dadosJson[$key]['SAIDA2']);

            }

            return json_encode($dadosJson);
        }

        return false;

    }
    
    // -------------------------------------------------------
    // Cadastrar escala
    // -------------------------------------------------------
    public function CadastrarEscala($dados){

        $id                     = $dados['id'] ?? null;
        $data                   = $dados['data'] ?? null;
        $data_folga             = $dados['data_folga'] ?? null;
        $chapa                  = $dados['chapa'] ?? null;
        $codhorario             = $dados['codhorario'] ?? null;
        $indice                 = $dados['indice'] ?? null;
        $indice_folga           = $dados['indice_folga'] ?? null;
        $dtmudanca_historico    = $dados['dtmudanca_historico'] ?? null;
        $codhorario_historico   = $dados['codhorario_historico'] ?? null;
        $chapa_solicitante      = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $horaEntrada            = $dados['horaEntrada'] ?? null;
        $horaSaida              = $dados['horaSaida'] ?? null;
        $dataDia                = $dados['dataDia'] ?? null;
        $horaEntrada2           = $dados['horaEntrada2'] ?? null;
        $horaSaida2             = $dados['horaSaida2'] ?? null;
        $dataDia2               = $dados['dataDia2'] ?? null;
        $justificativa_11_horas = $dados['justificativa_11_horas'] ?? "";
        $justificativa_6_dias   = $dados['justificativa_6_dias'] ?? "";
        $justificativa_6_meses  = $dados['justificativa_6_meses'] ?? "";
        $justificativa_3_dias   = $dados['justificativa_3_dias'] ?? "";
        $justificativa_periodo  = $dados['justificativa_periodo'] ?? "";
        $tipo                   = $dados['tipo'] ?? 1;

        if($data == null) return responseJson('error', '<b>Data</b> não informada.');
        if($chapa == null) return responseJson('error', '<b>Colaborador</b> não informado.');
        if($codhorario == null) return responseJson('error', '<b>Horário</b> não informado.');
        if($indice == null) return responseJson('error', '<b>Índice</b> não informado.');
        if($chapa_solicitante == null) return responseJson('error', '<b>Solicitante</b> inválido.');
        if($dtmudanca_historico == null) return responseJson('error', '<b>Histórico de horário</b> inválido.');
        if($tipo != 1){
            if($data_folga == null) return responseJson('error', '<b>Data (Dia Folga)</b> não informada.');
            if($indice_folga == null) return responseJson('error', '<b>Índice (Dia Folga)</b> não informada.');

            $diasDiff = (int)dataDiff($data, $data_folga);
            if($diasDiff < 0) $diasDiff = $diasDiff*-1;
            if($diasDiff == 0) return responseJson('error', 'Dia útil não pode ser igual ao Dia folga.');
            if($diasDiff > 90) return responseJson('error', 'Intervalo entre <strong>Dia Útil</strong> e <strong>Dia Folga</strong> não pode ser superior a <strong>90 dias</strong>.');
        }

        $termo_obrigatorio = ($codhorario != $codhorario_historico || $tipo != 1) ? 1 : "NULL";

        $configuracao         = self::Configuracao();
        $escala_per_inicio    = (strlen(trim($configuracao[0]['escala_per_inicio'])) <= 0) ? 'NULL' : "'{$configuracao[0]['escala_per_inicio']}'";
        $escala_per_fim       = (strlen(trim($configuracao[0]['escala_per_fim'])) <= 0) ? 'NULL' : "'{$configuracao[0]['escala_per_fim']}'";
        $dia_per_inicio       = (strlen(trim($configuracao[0]['dia_per_inicio'])) <= 0) ? 'NULL' : "'{$configuracao[0]['dia_per_inicio']}'";
        $dia_per_fim          = (strlen(trim($configuracao[0]['dia_per_fim'])) <= 0) ? 'NULL' : "'{$configuracao[0]['dia_per_fim']}'";
        $bloqueio_aviso       = (int)$configuracao[0]['bloqueio_aviso'];

        // valida troca da troca
        $historico = $this->dbrm->query(" SELECT * FROM PFHSTHOR WHERE CHAPA = '{$chapa}' AND CODCOLIGADA = '{$this->coligada}' AND DTMUDANCA = '{$data}' ");
        if(($historico->getNumRows() ?? 0) > 0){
            return responseJson('error', 'Não é possivel cadastrar está requisição.<br>Colaborador já possui uma troca no dia <b>'.dtBr($data).'</b>.');
        }
            
        
        if($tipo != 1){
            $historico2 = $this->dbrm->query(" SELECT * FROM PFHSTHOR WHERE CHAPA = '{$chapa}' AND CODCOLIGADA = '{$this->coligada}' AND DTMUDANCA = '{$data_folga}' ");
            if(($historico2->getNumRows() ?? 0) > 0){
                return responseJson('error', 'Não é possivel cadastrar está requisição.<br>Colaborador já possui uma troca no dia <b>'.dtBr($data_folga).'</b>.');
            }

            $query = " SELECT * FROM zcrmportal_escala WHERE chapa = '{$chapa}' AND datamudanca = '{$data}' AND tipo != '1' AND situacao not in (3,9) ";
            $result = $this->dbportal->query($query);
            if($result->getNumRows() > 0){
                return responseJson('error', 'Já existe uma solicitação em aberto para esta data.');
            }

        }

        // valida data da mudança
        // $data_min = \DateTime::createFromFormat('d/m/Y', date('d/m/Y'))->add(new \DateInterval('P3D'))->format('Ymd');
        // $data_min2 = \DateTime::createFromFormat('d/m/Y', date('d/m/Y'))->add(new \DateInterval('P3D'))->format('d/m/Y');
        // $data_prog = \DateTime::createFromFormat('Y-m-d', $data)->format('Ymd');

        // if($data_prog < $data_min) return responseJson('error', '<b>Data da nova escala</b> não permitida.<br>Permitido apartir de <b>'.$data_min2.'</b>');
        
        if($codhorario_historico != $codhorario && $justificativa_6_meses == ""){
            // VERIFICA A DATA DA ULTIMA TROCA DE HORÁRIO
            $dias = dataDiff($dtmudanca_historico, $data);
            if($dias < 180) return responseJson('error', 'Troca de escala inferior a 6 meses do horário atual do colaborador. <br>Última alteração em <b>'.dtBr($dtmudanca_historico).'</b>', 1);
        }

        // verifica se o Colaborador já possui uma solicitação em aberto
        $checkRequisicaoPendente = $this->VerificarRequisicaoChapa($chapa, $data, $data_folga, $id);
        if($checkRequisicaoPendente) return responseJson('error', 'Colaborador já possui uma requisição em aberto na data informada.');

        // verifica 35 horas de descanso dos ultimos 6 dias
        ##$result35horas = calcula35Horas($dataDia, $horaEntrada, $horaSaida, $dataDia2, $horaEntrada2, $horaSaida2);
        ##$result35horasEx = explode('|', $result35horas);
        
        ##if($result35horasEx[0] > 35) return responseJson('error', 'Colaborador deve descançar <b>35h</b> nos últimos 6 dias conseguitivos.<br><br>- Saída do último dia '.$result35horasEx[1].'<br>- Entrada do novo horário '.$result35horasEx[2].'<br>Horas '.$result35horasEx[0].'h');

        // verifica DSR 2 dias inicio férias
        $checkDSR = self::verificaFolgaDSR($data, $codhorario, $indice, $chapa);
        if($checkDSR){
            if($checkDSR == 1) return responseJson('error', 'TROCA DE ESCALA <b>NÃO PERMITIDA</b> EM DECORRÊNCIA DE FÉRIAS JÁ PROGRAMADAS.<br>POR GENTILEZA PROCURAR O RH.');
            if($checkDSR == 2) return responseJson('error', 'TROCA DE ESCALA <b>NÃO PERMITIDA</b> EM DECORRÊNCIA DE FÉRIAS INCIAR EM DIA DE DESCANSO.');
        }

        if($id){
            return self::AlterarEscala($id, $codhorario, $data, $indice, $data_folga, $indice_folga, $justificativa_11_horas, $justificativa_6_dias, $justificativa_6_meses, $justificativa_3_dias, $justificativa_periodo, $termo_obrigatorio, $chapa);
        }

        $filtroProjecao = [
            'data' => $data,
            'chapa' => $chapa,
            'codhorario' => $codhorario,
            'indice' => $indice,
        ];

        $projecao = ($tipo == 1) ? self::CalculaTrocaDeEscala($filtroProjecao) : self::CalculaTrocaDeDia($filtroProjecao);
        $projecao_folga = null;
        if($tipo != 1){
            $filtroProjecao['data'] = $data_folga;
            $filtroProjecao['indice'] = $indice_folga;
            $projecao_folga = self::CalculaTrocaDeDia($filtroProjecao);
        }
        
        $query = " INSERT INTO zcrmportal_escala
            (chapa, coligada, datamudanca, codhorario, codindice, usucad, dtcad, chapa_solicitante, justificativa_11_horas, justificativa_6_dias, justificativa_6_meses, justificativa_3_dias, termo_obrigatorio, tipo, datamudanca_folga, codindice_folga, justificativa_periodo, config_escala_per_ini, config_escala_per_fim, config_dia_per_ini, config_dia_per_fim, bloqueio_aviso, projecao, projecao_folga) 
                VALUES
            ('{$chapa}', '{$this->coligada}', '{$data}', '{$codhorario}', '{$indice}', '{$this->log_id}', '".date('Y-m-d H:i:s')."', '{$chapa_solicitante}', ".(($justificativa_11_horas == "") ? "NULL" : "'{$justificativa_11_horas}'").", ".(($justificativa_6_dias == "") ? "NULL" : "'{$justificativa_6_dias}'").", ".(($justificativa_6_meses == "") ? "NULL" : "'{$justificativa_6_meses}'").", ".(($justificativa_3_dias == "") ? "NULL" : "'{$justificativa_3_dias}'").", {$termo_obrigatorio}, {$tipo}, '{$data_folga}', '{$indice_folga}', ".(($justificativa_periodo == "") ? "NULL" : "'{$justificativa_periodo}'").", {$escala_per_inicio}, {$escala_per_fim}, {$dia_per_inicio}, {$dia_per_fim}, {$bloqueio_aviso}, '{$projecao}', ".(($projecao_folga != null) ? "'{$projecao_folga}'" : 'NULL').")
        ";
        $this->dbportal->query($query);
        
        if($this->dbportal->affectedRows() > 0){

            $id_insert = $this->dbportal->insertID();

            // caso seja solicitação do gestor, já aprova alteração de escala
            $mHierarquia = model('HierarquiaModel');
            if($mHierarquia->isGestor()){

                $this->dbportal->query(" UPDATE zcrmportal_escala SET situacao = (CASE WHEN termo_obrigatorio = 1 THEN 0 ELSE 2 END), dtapr = '".date('Y-m-d H:i:s')."', usuapr = '{$this->log_id}' WHERE id = '{$id_insert}' AND coligada = '{$this->coligada}' AND situacao = 0 ");

                if($this->dbportal->affectedRows() > 0){
                    $query = " SELECT id FROM zcrmportal_escala WHERE id = '{$id_insert}' AND coligada = '{$this->coligada}' AND termo_obrigatorio = 1 AND situacao = 0 ";
                    $result = $this->dbportal->query($query);
                    if($result->getNumRows() > 0){
                        $this->EscalaNotificaSolicitante($id_insert);
                    }
                }

                notificacao('success', 'Cadastrado com Sucesso.');
                return responseJson('success', 'Cadastrado com Sucesso', id($this->dbportal->insertID()), id(($termo_obrigatorio == 1) ? 1 : 2));

            }else{
                $this->EscalaNotificaGestor($chapa_solicitante, $chapa);
            }

            notificacao('success', 'Cadastrado com Sucesso.');
            return responseJson('success', 'Cadastrado com Sucesso', id($this->dbportal->insertID()), id(0));
        }

        return responseJson('error', 'Falha ao cadastrar escala');

    }

    public function AlterarEscala($id, $codhorario, $data, $indice, $data_folga, $indice_folga, $justificativa_11_horas, $justificativa_6_dias, $justificativa_6_meses, $justificativa_3_dias, $justificativa_periodo, $termo_obrigatorio, $chapa)
    {

        $filtroProjecao = [
            'data' => $data,
            'chapa' => $chapa,
            'codhorario' => $codhorario,
            'indice' => $indice,
        ];

        $projecao = self::CalculaTrocaDeDia($filtroProjecao);
        $projecao_folga = null;
        if($data_folga != null){
            $filtroProjecao['data'] = $data_folga;
            $filtroProjecao['indice'] = $indice_folga;
            $projecao_folga = self::CalculaTrocaDeDia($filtroProjecao);
        }

        
        $this->dbportal->query("
            update
                zcrmportal_escala
            set
                termo_obrigatorio         = {$termo_obrigatorio},
                codhorario                = '{$codhorario}',
                datamudanca               = '{$data}',
                codindice                 = '{$indice}',
                datamudanca_folga         = '{$data_folga}',
                codindice_folga           = '{$indice_folga}',
                justificativa_11_horas    = ".(($justificativa_11_horas == "") ? "NULL" : "'{$justificativa_11_horas}'").",
                justificativa_6_dias      = ".(($justificativa_6_dias == "") ? "NULL" : "'{$justificativa_6_dias}'").",
                justificativa_6_meses     = ".(($justificativa_6_meses == "") ? "NULL" : "'{$justificativa_6_meses}'").",
                justificativa_3_dias      = ".(($justificativa_3_dias == "") ? "NULL" : "'{$justificativa_3_dias}'").",
                justificativa_periodo     = ".(($justificativa_periodo == "") ? "NULL" : "'{$justificativa_periodo}'").",
                usualt                    = '{$this->log_id}',
                dtalt                     = '{$this->now}',
                situacao                  = 0,
                projecao                  = '{$projecao}',
                projecao_folga            = ".(($projecao_folga != null) ? "'{$projecao_folga}'" : 'NULL')."
            where
                    id                    = {$id}
                and situacao in (0, 8)
        ");
        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Escala salva com sucesso.');
            return responseJson('success', 'Escala salva com sucesso');
        }

        return responseJson('error', 'Falha ao salvar escala');
    }
    
    // -------------------------------------------------------
    // Envia troca de escala e dia para aprovação
    // -------------------------------------------------------
    public function EnviaParaAprovacao($dados){
        $checkStatus = $this->dbportal
                    ->table('zcrmportal_escala')
                    ->where('id', $dados['id'])
                    ->whereIn('situacao', [0])
                    ->get();
                    
        if($checkStatus->getNumRows() <= 0){
            return responseJson('error', 'Erro ao enviar para aprovação.');
        }

        // verifica se anexou o termo
        $dadosEscala = $checkStatus->getResultArray();
        if((int)$dadosEscala[0]['termo_obrigatorio'] == 1){
            if(strlen(trim($dadosEscala[0]['dtupload'])) <= 0){
                return responseJson('error', 'Termo assinado ainda não foi anexado.');
            }
        }

        $this->dbportal
            ->table('zcrmportal_escala')
            ->whereIn('situacao', [0])
            ->where('id', $dados['id'])
            ->update([
                'situacao' => 10,
                'usualt' => $this->log_id,
                'dtalt' => date('Y-m-d H:i:s')
            ]);

        return responseJson('success', 'Requisição enviada para aprovação com sucesso.');
    }
    
    // -------------------------------------------------------
    // exclui troca de escala
    // -------------------------------------------------------
    public function ExcluirEscala($dados)
    {

        $checkStatus = $this->dbportal
                    ->table('zcrmportal_escala')
                    ->where('id', $dados['id'])
                    ->whereIn('situacao', [0,8])
                    ->get();
                    
        if($checkStatus->getNumRows() <= 0){
            return responseJson('error', 'Não é possivel excluir esta escala.');
        }

        $this->dbportal->query("
            UPDATE zcrmportal_escala SET situacao = 11 WHERE id = {$dados['id']} AND situacao = 8
        ");

        $this->dbportal->query("
            DELETE FROM zcrmportal_escala WHERE id = {$dados['id']} AND situacao = 0
        ");

        return responseJson('success', 'Requisição excluída com sucesso.');
        
    }
    
    // -------------------------------------------------------
    // verifica se a chapa possui uma requisição aberta pendente
    // -------------------------------------------------------
    public function VerificarRequisicaoChapa($chapa, $data, $data_folga, $id){

        $filtroDataFolga = ($data_folga) ? " OR datamudanca_folga = '{$data_folga}' " : "";
        $filtroId = ($id) ? " AND id != '{$id}' " : "";

        $query = " SELECT * FROM zcrmportal_escala WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' AND situacao not in (3,9) AND (datamudanca = '{$data}' {$filtroDataFolga})".$filtroId;
        $result = $this->dbportal->query($query);

        return ($result->getNumRows() > 0) ? true : false;

    }
    
    // -------------------------------------------------------
    // configuração da escala
    // -------------------------------------------------------
    public function Configuracao(){

        $query = " SELECT * FROM zcrmportal_escala_config WHERE coligada = '{$this->coligada}' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    
    // -------------------------------------------------------
    // configuração da escala
    // -------------------------------------------------------
    public function CadastrarConfiguracao($dados){

        $bloqueio_aviso = $dados['bloqueio_aviso'];

        if($dados['escala_per_fim'] < $dados['escala_per_inicio']){
            return responseJson('error', '<b>Escala:</b> Data fim não pode ser menor que a data início');
        }
        if($dados['dia_per_fim'] < $dados['dia_per_inicio']){
            return responseJson('error', '<b>Dia:</b> Data fim não pode ser menor que a data início');
        }

        $existe_registro = $this->Configuracao();
        if($existe_registro){

            // update
            $query = "
                UPDATE
                    zcrmportal_escala_config
                SET
                    bloqueio_aviso    = ".(($bloqueio_aviso) ? "1" : "NULL").",
                    escala_per_inicio = '{$dados['escala_per_inicio']}',
                    escala_per_fim    = '{$dados['escala_per_fim']}',
                    dia_per_inicio    = '{$dados['dia_per_inicio']}',
                    dia_per_fim       = '{$dados['dia_per_fim']}',
                    usualt            = '{$this->log_id}',
                    dtalt             = '".date('Y-m-d H:i:s')."'
                WHERE
                    coligada          = '{$this->coligada}'
            ";
            $this->dbportal->query($query);
            if($this->dbportal->affectedRows() > 0){
                return responseJson('success', 'Configuração realizada com sucesso.');
            }

            return responseJson('error', 'Não foi possivel salvar a configuração.');

        }

        // update
        $query = "
            INSERT INTO zcrmportal_escala_config
            (
                coligada,
                bloqueio_aviso,
                escala_per_inicio,
                escala_per_fim,
                dia_per_inicio,
                dia_per_fim,
                usualt,
                dtalt
            ) VALUES (
                '{$this->coligada}',
                ".(($bloqueio_aviso) ? "1" : "NULL").",
                '{$dados['escala_per_inicio']}',
                '{$dados['escala_per_fim']}',
                '{$dados['dia_per_inicio']}',
                '{$dados['dia_per_fim']}',
                '{$this->log_id}',
                '".date('Y-m-d H:i:s')."'
            )
        ";
        $this->dbportal->query($query);
        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Configuração realizada com sucesso.');
        }

        return responseJson('error', 'Não foi possivel salvar a configuração.');

    }

    // -------------------------------------------------------
    // Lista escala cadastradas
    // -------------------------------------------------------
    public function ListarEscala($id = false, $filtro = null){

        //$this->EscalaNotificaSolicitante(9);

        $ft_id = ($id !== false) ? " AND a.id = '{$id}' " : "";

        $table_documento = ($ft_id != "") ? " , a.documento " : "";

        $mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('GLOBAL_RH');

        $situacao_rh = " AND a.situacao = 2 ";
        $situacao_gestor = " AND a.situacao = 0 ";
        $situacao_solicitante = "";
        $distinct = ($id) ? "" : "DISTINCT";
        $filtroGlobal = "";

        // if($filtro['filtro'] ?? null !== null && $filtro['filtro'] ?? "" != ""){
        //     $filtro_in = ($filtro['filtro'] == 9) ? "8,9" : $filtro['filtro'];
        //     $situacao_rh = " AND a.situacao IN ({$filtro_in}) ";
        //     $situacao_gestor = " AND a.situacao IN ({$filtro_in}) ";
        //     $situacao_solicitante = $situacao_gestor;
        // }

        if(($filtro['filtro_tipo_troca'] ?? '') !== '') $filtroGlobal .= " and a.tipo = '{$filtro['filtro_tipo_troca']}' ";
        if(($filtro['filtro_colaborador'] ?? '') !== '') $filtroGlobal .= " and a.chapa = '{$filtro['filtro_colaborador']}' ";
        if(($filtro['data_inicio'] ?? '') !== '') $filtroGlobal .= " and a.datamudanca >= '{$filtro['data_inicio']}' ";
        if(($filtro['data_fim'] ?? '') !== '') $filtroGlobal .= " and a.datamudanca <= '{$filtro['data_fim']}' ";
        if(($filtro['data_inicio'] ?? '') !== '' && ($filtro['filtro_tipo_troca'] ?? null) == 2) $filtroGlobal .= " and a.datamudanca_folga >= '{$filtro['data_inicio']}' ";
        if(($filtro['data_fim'] ?? '') !== '' && ($filtro['filtro_tipo_troca'] ?? null) == 2) $filtroGlobal .= " and a.datamudanca_folga <= '{$filtro['data_fim']}' ";
        if(($filtro['filtro'] ?? '') !== ''){
            if($filtro['filtro'] == '9'){
                $filtroGlobal .= " and a.situacao IN ('8','9') ";
            }else{
                $filtroGlobal .= " and a.situacao = '{$filtro['filtro']}' ";
            }
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
			$filtro_secao_lider = " a.chapa IN (".substr($chapas_lider, 0, -1).") OR ";
            // exit($filtro_secao_lider);
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
			$filtro_secao_gestor = " aa.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
        
        $in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";
        if($perfil_rh) $in_secao = "";

        $usucad = "";

        $sql_rh = "";
        if($perfil_rh && 1==2){
            $sql_rh = "
                UNION ALL

                SELECT 
                    {$distinct} 
                    a.termo_obrigatorio,
                    a.justificativa_11_horas,
                    a.justificativa_6_dias,
                    a.justificativa_6_meses,
                    a.justificativa_3_dias,
                    a.justificativa_periodo,
                    a.motivocancelado,
                    a.id,
                    a.chapa,
                    a.datamudanca,
                    a.datamudanca_folga,
                    a.codhorario,
                    a.codindice,
                    a.codindice_folga,
                    a.situacao,
                    2 step,
                    a.dtcancelado,
                    b.nome solicitante,
                    a.tipo,
                    a.config_escala_per_ini,
                    a.config_escala_per_fim,
                    a.config_dia_per_ini,
                    a.config_dia_per_fim,
                    a.bloqueio_aviso,
                    a.usuupload
                    {$table_documento}
                FROM 
                    zcrmportal_escala a
                    LEFT JOIN zcrmportal_usuario b ON b.id = a.usucad
                WHERE
                        a.coligada = '{$this->coligada}'
                        {$situacao_rh}
                        {$filtroGlobal}
                        {$ft_id}
                        and a.situacao <> 11
            ";
        }

        $query = " 
            SELECT 
            {$distinct} 
            a.termo_obrigatorio,
            a.justificativa_11_horas,
            a.justificativa_6_dias,
            a.justificativa_6_meses, 
            a.justificativa_3_dias, 
            a.justificativa_periodo, 
            a.motivocancelado,
            a.id,
            a.chapa,
            a.datamudanca,
            a.datamudanca_folga,
            a.codhorario,
            a.codindice,
            a.codindice_folga,
            a.situacao,
            0 step,
            a.dtcancelado,
            a.chapa_solicitante,
            b.nome solicitante,
            a.tipo,
            a.config_escala_per_ini,
            a.config_escala_per_fim,
            a.config_dia_per_ini,
            a.config_dia_per_fim,
            a.bloqueio_aviso,
            a.usuupload,
            a.dtcad
             {$table_documento} 
            FROM 
                zcrmportal_escala a 
                LEFT JOIN zcrmportal_usuario b ON b.id = a.usucad
                INNER JOIN ".DBRM_BANCO."..PFUNC aa ON aa.chapa = a.chapa COLLATE Latin1_General_CI_AS AND aa.CODCOLIGADA = a.coligada
            WHERE 
                a.coligada = '{$this->coligada}' {$ft_id} 
                {$usucad} 
                {$situacao_solicitante} 
                {$filtroGlobal}
                {$in_secao}
                and a.situacao <> 11
            
            /***************
            UNION ALL

            SELECT 
                {$distinct}  
                a.termo_obrigatorio,
                a.justificativa_11_horas,
                a.justificativa_6_dias,
                a.justificativa_6_meses,
                a.justificativa_3_dias,
                a.justificativa_periodo,
                a.motivocancelado,
                a.id,
                a.chapa,
                a.datamudanca,
                a.datamudanca_folga,
                a.codhorario,
                a.codindice,
                a.codindice_folga,
                a.situacao,
                1 step,
                a.dtcancelado,
                a2.nome solicitante,
                a.tipo,
                a.config_escala_per_ini,
                a.config_escala_per_fim,
                a.config_dia_per_ini,
                a.config_dia_per_fim,
                a.bloqueio_aviso,
                a.usuupload
                {$table_documento}
            FROM 
                zcrmportal_escala a
                    LEFT JOIN zcrmportal_usuario a2 ON a2.id = a.usucad
                    INNER JOIN ".DBRM_BANCO."..PFUNC aa ON aa.chapa = a.chapa COLLATE Latin1_General_CI_AS AND aa.CODCOLIGADA = a.coligada
                ,
                GESTOR_DO_LIDER b,
                zcrmportal_hierarquia_lider_func c
            WHERE 
                    a.coligada = '{$this->coligada}'
                AND a.chapa_solicitante = b.chapa_funcionario COLLATE Latin1_General_CI_AS
                AND b.id_gestor = '{$this->log_id}'
                {$situacao_gestor}
                AND a.usucad != '{$this->log_id}'
                {$ft_id}
                AND c.id_lider = b.id_funcionario_lider
                AND c.chapa = a.chapa
                AND c.coligada = a.coligada
                {$filtroGlobal}
                {$in_secao}
                and a.situacao <> 11

            UNION ALL

            SELECT 
                {$distinct}  
                a.termo_obrigatorio,
                a.justificativa_11_horas,
                a.justificativa_6_dias,
                a.justificativa_6_meses,
                a.justificativa_3_dias,
                a.justificativa_periodo,
                a.motivocancelado,
                a.id,
                a.chapa,
                a.datamudanca,
                a.datamudanca_folga,
                a.codhorario,
                a.codindice,
                a.codindice_folga,
                a.situacao,
                1 step,
                a.dtcancelado,
                a2.nome solicitante,
                a.tipo,
                a.config_escala_per_ini,
                a.config_escala_per_fim,
                a.config_dia_per_ini,
                a.config_dia_per_fim,
                a.bloqueio_aviso,
                a.usuupload
                {$table_documento}
            FROM 
                zcrmportal_escala a
                    LEFT JOIN zcrmportal_usuario a2 ON a2.id = a.usucad
                ,
                GESTOR_DO_LIDER_SUBSTITUTO b,
                zcrmportal_hierarquia_lider_func c
            WHERE 
                    a.coligada = '{$this->coligada}'
                AND a.chapa_solicitante = b.chapa_funcionario COLLATE Latin1_General_CI_AS
                AND b.id_gestor = '{$this->log_id}'
                {$situacao_gestor}
                AND a.usucad != '{$this->log_id}'
                {$ft_id}
                AND c.id_lider = b.id_funcionario_lider
                AND c.chapa = a.chapa
                AND c.coligada = a.coligada
                {$filtroGlobal}
                and a.situacao <> 11

            {$sql_rh}
        **************/
                
        ";
        
        

        // print_r($filtro);
        // echo '<pre>';echo $query;exit();
        $result = $this->dbportal->query($query);

        if($result->getNumRows() > 0){

            $mPortal = model("PortalModel");
            
            $response = $result->getResultArray();
            $dadosArray = array();
            foreach($response as $key => $dados){

                $dadosArray[$key] = (!$dados) ? array() : $dados;
                $dadosChapa = $mPortal->ListarDadosFuncionario(false, $dados['chapa'], false);
                $dadosArray[$key]['nome'] = $dadosChapa[0]['NOME'] ?? "";
                
                if($filtro['filtro_secao'] ?? null != null){
                    if($filtro['filtro_secao'] ?? null != $dadosChapa[0]['CODSECAO']){
                        unset($dadosArray[$key]);
                        continue;
                    }
                }
                if($filtro['filtro_funcao'] ?? null != null){
                    if($filtro['filtro_funcao'] != $dadosChapa[0]['CODFUNCAO']){
                        unset($dadosArray[$key]);
                        continue;
                    }
                }

            }
            
            return $dadosArray;
        }
        return false;

    }

    public function VerificaData($dados)
    {

        $dataInicio   = somarDias($dados['data'], -1);
        $dataTermino  = somarDias($dados['data'], 1);
        $idRequisicao = strlen(trim($dados['id'] ?? '') > 0) ? " AND id != '{$dados['id']}' " : '';

        $historico = $this->dbrm->query(" SELECT * FROM PFHSTHOR WHERE CHAPA = '{$dados['chapa']}' AND CODCOLIGADA = '{$this->coligada}' AND DTMUDANCA BETWEEN '{$dataInicio}' AND '{$dataTermino}' ");
        if(($historico->getNumRows() ?? 0) > 0){
            return responseJson('error', 'Não é possivel cadastrar está requisição.<br>Colaborador já possui uma troca no dia <b>'.dtBr($dados['data']).'</b>.');
        }

        $checkPortal = $this->dbportal->query(" SELECT * FROM zcrmportal_escala WHERE chapa = '{$dados['chapa']}' AND datamudanca BETWEEN '{$dataInicio}' AND '{$dataTermino}' AND situacao NOT IN (3, 9) {$idRequisicao} ");
        if(($checkPortal->getNumRows() ?? 0) > 0){
            return responseJson('error', 'Não é possivel cadastrar está requisição.<br>Colaborador já possui uma troca no dia <b>'.dtBr($dados['data']).'</b>.');
        }

        $checkPortal = $this->dbportal->query(" SELECT * FROM zcrmportal_escala WHERE chapa = '{$dados['chapa']}' AND datamudanca_folga BETWEEN '{$dataInicio}' AND '{$dataTermino}' AND situacao NOT IN (3, 9) {$idRequisicao} ");
        if(($checkPortal->getNumRows() ?? 0) > 0){
            return responseJson('error', 'Não é possivel cadastrar está requisição.<br>Colaborador já possui uma troca no dia <b>'.dtBr($dados['data']).'</b>.');
        }

        return responseJson('success', 'Data ok');

    }
    
    // -------------------------------------------------------
    // Dados do funcionário e empresa termo aditivo
    // -------------------------------------------------------
    public function ListarEscalaTermoAditivo($dados){
        
        $chapa = $dados['chapa'] ?? null;
        $codhorario = $dados['codhorario'] ?? null;
        
        if($chapa == null) return false;
        if($codhorario == null) return false;
        
        $query = "
            SELECT
                A.CHAPA,
                A.NOME,
                A.CODHORARIO,
                B.DESCRICAO HORARIO_ATUAL,
                C.CODIGO CODHORARIO_NOVO,
                C.DESCRICAO HORARIO_NOVO,
                D.NOME NOMEFILIAL,
                D.CGC CNPJ,
                D.RUA,
                D.COMPLEMENTO,
                D.BAIRRO,
                D.CIDADE,
                D.ESTADO,
                E.CPF,
                E.CARTEIRATRAB CTPS,
                E.SERIECARTTRAB CTPS_SERIE,
                D.CEP,
                D.TELEFONE
            FROM
                PFUNC A
                    INNER JOIN AHORARIO B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODHORARIO
                    INNER JOIN AHORARIO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = '{$codhorario}'
                    INNER JOIN GFILIAL D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODFILIAL = A.CODFILIAL
                    INNER JOIN PPESSOA E ON E.CODIGO = A.CODPESSOA
            WHERE
                    A.CHAPA = '{$chapa}'
                AND A.CODCOLIGADA = '{$this->coligada}'
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Aprova solicitação de escala 
    // -------------------------------------------------------
    public function AprovarEscala($dados){

        $id = cid($dados['id']);

        switch(cid($dados['step'])){
            case 1: $query = " UPDATE zcrmportal_escala SET situacao = 2, dtapr = '".date('Y-m-d H:i:s')."', usuapr = '{$this->log_id}' WHERE id = '{$id}' AND coligada = '{$this->coligada}' AND situacao = 0 "; break;
            case 2: $query = " UPDATE zcrmportal_escala SET situacao = 3, dtrh = '".date('Y-m-d H:i:s')."', usurh = '{$this->log_id}' WHERE id = '{$id}' AND coligada = '{$this->coligada}' AND situacao = 2 "; break;
            default: responseJson('error', 'Falha ao APROVAR escala'); break;
        }
        
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0){

            // verifica se precisa notificar o solicitando, pendente do termo aditivo
            //if(cid($dados['step']) == 1){

                $query = " SELECT id FROM zcrmportal_escala WHERE id = '{$id}' AND coligada = '{$this->coligada}' and termo_obrigatorio = 1 AND situacao = 1 ";
                $result = $this->dbportal->query($query);
                if($result->getNumRows() > 0){
                    $this->EscalaNotificaSolicitante($id);
                }

            //}

            // sincroniza com totvs
            if(cid($dados['step']) == 2){
                // sincroniza com RM
                $result = $this->SincronizaRM_Horario($id);
                //$result = true;
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
                                id = {$id}
                            AND situacao = 3
                    ";
                    $this->dbportal->query($query);

                    return responseJson('error', 'Falha na sincronização com RM.');

                }

                notificacao('success', 'Escala APROVADA e SINCRONIZADA com sucesso.');
                return responseJson('success', 'Escala APROVADA e SINCRONIZADA com sucesso');

            }

            notificacao('success', 'Escala APROVADA com sucesso.');
            return responseJson('success', 'Escala APROVADA com sucesso');
        }

        return responseJson('error', 'Falha ao APROVAR escala');

    }

    // -------------------------------------------------------
    // Reprovar solicitação de escala
    // -------------------------------------------------------
    public function ReprovarEscala($dados){

        $id = cid($dados['id']);

        switch(cid($dados['step'])){
            case 1: $query = " UPDATE zcrmportal_escala SET situacao = 8, motivocancelado = 'Reprovado pelo Gestor', dtcancelado = '".date('Y-m-d H:i:s')."', usucancelado = '{$this->log_id}' WHERE id = '{$id}' AND coligada = '{$this->coligada}' AND situacao NOT IN (9, 3, 8) ";
            case 2: $query = " UPDATE zcrmportal_escala SET situacao = 8, motivocancelado = 'Reprovado pelo RH', dtcancelado = '".date('Y-m-d H:i:s')."', usucancelado = '{$this->log_id}' WHERE id = '{$id}' AND coligada = '{$this->coligada}' AND situacao NOT IN (9, 3, 8) ";
            default: responseJson('error', 'Falha ao REPROVAR escala'); break;
        }
        
        $this->dbportal->query($query);
        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Escala REPROVADA com sucesso');
        }

        return responseJson('error', 'Falha ao REPROVAR escala');

    }
    
    // -------------------------------------------------------
    // Upload do termo aditivo
    // -------------------------------------------------------
    public function UploadTermoAditivo($dados){

        $id = cid($dados['id']);
        $documento = $dados['documento'];
        $formatosPermitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        
        $file_name = $documento['documento']['name'] ?? null;
        $file_type = $documento['documento']['type'] ?? null;
        $file_size = $documento['documento']['size'] ?? null;
        if($file_name !== null) $file_file = base64_encode(file_get_contents($documento['documento']['tmp_name']));
        
        if($file_name == null) return responseJson('error', 'Nome do arquivo inválido.');
        if($file_type == null) return responseJson('error', 'Tipo do arquivo inválido.');
        if($file_size == null) return responseJson('error', 'Tamanho do arquivo inválido.');

        // valida tamanho do arquivo
        $tamanho = $file_size / 1000000;
        if($tamanho > 10) return responseJson('error', 'Arquivo muito grande, tamanho máximo permitido <b>10MB</b>');

        if(!in_array($file_type, $formatosPermitidos)){
            return responseJson('error', 'O formato do arquivo não é permitido, formatos aceitos <b>PDF</b> ou <b>Imagem</b>.');
        }

        $mHierarquia = model('HierarquiaModel');
        $situacao = "0";
        if($mHierarquia->isGestor()){
            $situacao = "2";
        }

        $query = "
            UPDATE 
                zcrmportal_escala
            SET
                documento   = '{$file_name}|{$file_type}|{$file_size}|{$file_file}',
                dtupload    = '".date('Y-m-d H:i:s')."',
                usuupload   = '{$this->log_id}',
                situacao    = {$situacao}
            WHERE
                    id = {$id}
                AND situacao IN (0)
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Documento enviado com sucesso.');
            return responseJson('success', 'Documento enviado com sucesso.', false, id(2));
        }else{
            return responseJson('error', 'Falha ao enviar documento.');
        }

    }
    
    // -------------------------------------------------------
    // Sincroniza novo horário para o totvs
    // -------------------------------------------------------
    public function SincronizaRM_Horario($id_requisicao){

        $query = " SELECT chapa, coligada, codhorario, datamudanca, codindice, tipo, datamudanca_folga, codindice_folga FROM zcrmportal_escala WHERE coligada = {$this->coligada} AND id = {$id_requisicao} ";
        $result = $this->dbportal->query($query);

        if($result->getNumRows() <= 0) return false;

        $DadosEscala = $result->getResultArray()[0];

        if($DadosEscala['tipo'] == 1){

            // atualiza PFUNC
            $PFUNC = " 
                UPDATE 
                    PFUNC 
                SET 
                    CODHORARIO = '{$DadosEscala['codhorario']}', 
                    INDINICIOHOR = '{$DadosEscala['codindice']}', 
                    RECMODIFIEDBY = 'PORTAL.{$this->log_id}', 
                    RECMODIFIEDON = '".date('Y-m-d H:i:s')."'
                WHERE 
                        CODCOLIGADA = {$this->coligada} 
                    AND CHAPA = '{$DadosEscala['chapa']}' 
            ";
            $this->dbrm->query($PFUNC);

            if($this->dbrm->affectedRows() > 0){

                // grava o histórico da alteração PFHSTHOR
                $PFHSTHOR = "
                    INSERT INTO PFHSTHOR
                        (CODCOLIGADA, CHAPA, CODHORARIO, INDINICIOHOR, DTMUDANCA, DATAALTERACAO, RECCREATEDBY, RECCREATEDON, COMPORTAMENTOHORARIOANTERIOR, COMPORTAMENTOHORARIOATUAL)
                            VALUES
                        ({$this->coligada}, '{$DadosEscala['chapa']}', '{$DadosEscala['codhorario']}', dbo.CalculoIndicePontoMais({$DadosEscala['codindice']}, '".date('d/m/Y', strtotime($DadosEscala['datamudanca']))."', '{$DadosEscala['codhorario']}', '{$this->coligada}'), '".date('Y-m-d', strtotime($DadosEscala['datamudanca']))."', '".date('Y-m-d H:i:s')."', 'PORTAL.{$this->log_id}', '".date('Y-m-d H:i:s')."', 1, 1)
                ";
                $this->dbrm->query($PFHSTHOR);

                return true;

            }

        }else{

            $pfunc = $this->dbrm->query(" SELECT TOP 1 CODHORARIO, INDINICIOHOR FROM PFHSTHOR WHERE CHAPA = '{$DadosEscala['chapa']}' AND CODCOLIGADA = '{$DadosEscala['coligada']}' ORDER BY DTMUDANCA DESC ");
            if($pfunc->getNumRows() > 0){

                $query    = " select projecao, projecao_folga from zcrmportal_escala WHERE id = '{$id_requisicao}' ";
                $result   = $this->dbportal->query($query);
                $response = $result->getResultArray()[0];
            
                $dadosFunc                = $pfunc->getResultArray()[0];
                $dataMudanca              = $DadosEscala['datamudanca'];
                $dataMudancaFolga         = $DadosEscala['datamudanca_folga'];
                $dataHorarioNormal        = somarDias(dtEn($dataMudanca, true), 1);
                $dataHorarioNormalFolga   = somarDias(dtEn($dataMudancaFolga, true), 1);

                $indiceTrabalho       = $dadosFunc['INDINICIOHOR'];
                $indiceFolga          = $dadosFunc['INDINICIOHOR'];
                $dadosProjecao        = json_decode($response['projecao'], true);
                $dadosProjecaoFolga   = json_decode($response['projecao_folga'], true);

                if($dadosProjecao){
                    foreach($dadosProjecao as $projecao){
                        if($projecao['DATAEN'] == $dataHorarioNormal){
                            $indiceTrabalho = $projecao['CODINDICE'];
                            break;
                        }
                    }
                }
                if($dadosProjecaoFolga){
                    foreach($dadosProjecaoFolga as $projecaoFolga){
                        if($projecaoFolga['DATAEN'] == $dataHorarioNormalFolga){
                            $indiceFolga = $projecaoFolga['CODINDICE'];
                            break;
                        }
                    }
                }
                
                // dia util
                $PFHSTHOR = "
                    INSERT INTO PFHSTHOR
                        (CODCOLIGADA, CHAPA, CODHORARIO, INDINICIOHOR, DTMUDANCA, DATAALTERACAO, RECCREATEDBY, RECCREATEDON, COMPORTAMENTOHORARIOANTERIOR, COMPORTAMENTOHORARIOATUAL)
                            VALUES
                        ({$this->coligada}, '{$DadosEscala['chapa']}', '{$DadosEscala['codhorario']}', dbo.CalculoIndicePontoMais({$DadosEscala['codindice']}, '".date('d/m/Y', strtotime($DadosEscala['datamudanca']))."', '{$DadosEscala['codhorario']}', '{$this->coligada}'), '".date('Y-m-d', strtotime($DadosEscala['datamudanca']))."', '".date('Y-m-d H:i:s')."', 'PORTAL.{$this->log_id}', '".date('Y-m-d H:i:s')."', 1, 1)
                ";
                $this->dbrm->query($PFHSTHOR);
                if(dtEn($dataMudancaFolga, true) != $dataHorarioNormal){
                    // horario normal
                    $PFHSTHOR = "
                        INSERT INTO PFHSTHOR
                            (CODCOLIGADA, CHAPA, CODHORARIO, INDINICIOHOR, DTMUDANCA, DATAALTERACAO, RECCREATEDBY, RECCREATEDON, COMPORTAMENTOHORARIOANTERIOR, COMPORTAMENTOHORARIOATUAL)
                                VALUES
                            ({$this->coligada}, '{$DadosEscala['chapa']}', '{$dadosFunc['CODHORARIO']}', dbo.CalculoIndicePontoMais({$indiceTrabalho}, '".date('d/m/Y', strtotime($dataHorarioNormal))."', '{$dadosFunc['CODHORARIO']}', '{$this->coligada}'), '".date('Y-m-d', strtotime($dataHorarioNormal))."', '".date('Y-m-d H:i:s')."', 'PORTAL.{$this->log_id}', '".date('Y-m-d H:i:s')."', 1, 1)
                    ";
                    $this->dbrm->query($PFHSTHOR);
                }
                // dia folga
                $PFHSTHOR = "
                    INSERT INTO PFHSTHOR
                        (CODCOLIGADA, CHAPA, CODHORARIO, INDINICIOHOR, DTMUDANCA, DATAALTERACAO, RECCREATEDBY, RECCREATEDON, COMPORTAMENTOHORARIOANTERIOR, COMPORTAMENTOHORARIOATUAL)
                            VALUES
                        ({$this->coligada}, '{$DadosEscala['chapa']}', '{$DadosEscala['codhorario']}', dbo.CalculoIndicePontoMais({$DadosEscala['codindice_folga']}, '".date('d/m/Y', strtotime($DadosEscala['datamudanca_folga']))."', '{$DadosEscala['codhorario']}', '{$this->coligada}'), '".date('Y-m-d', strtotime($DadosEscala['datamudanca_folga']))."', '".date('Y-m-d H:i:s')."', 'PORTAL.{$this->log_id}', '".date('Y-m-d H:i:s')."', 1, 1)
                ";
                $this->dbrm->query($PFHSTHOR);
                // horario normal
                $PFHSTHOR = "
                    INSERT INTO PFHSTHOR
                        (CODCOLIGADA, CHAPA, CODHORARIO, INDINICIOHOR, DTMUDANCA, DATAALTERACAO, RECCREATEDBY, RECCREATEDON, COMPORTAMENTOHORARIOANTERIOR, COMPORTAMENTOHORARIOATUAL)
                            VALUES
                        ({$this->coligada}, '{$DadosEscala['chapa']}', '{$dadosFunc['CODHORARIO']}', dbo.CalculoIndicePontoMais({$indiceFolga}, '".date('d/m/Y', strtotime($dataHorarioNormalFolga))."', '{$dadosFunc['CODHORARIO']}', '{$this->coligada}'), '".date('Y-m-d', strtotime($dataHorarioNormalFolga))."', '".date('Y-m-d H:i:s')."', 'PORTAL.{$this->log_id}', '".date('Y-m-d H:i:s')."', 1, 1)
                ";
                $this->dbrm->query($PFHSTHOR);

                return true;

            }

        }

        return false;

    }
    
    // -------------------------------------------------------
    // Cancela escala sem anexo do documento assinado 10 dias 
    // antes da data de mudança
    // -------------------------------------------------------
    public function CancelarEscala10Dias(){
        // não executar mais esse JOB
        exit();

        $query = "
            UPDATE
                zcrmportal_escala
            SET
                dtcancelado = getdate(),
                usucancelado = 1,
                motivocancelado = 'Fora do prazo 3 dias',
                situacao = 9
            WHERE
                    (DATEDIFF(DAY, getdate(), datamudanca) +1) <= 3
                AND situacao NOT IN (3, 9, 8)
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? true
                : false;

    }

    // -------------------------------------------------------
    // Notifica solicitante pendente de upload termo aditivo
    // -------------------------------------------------------
    public function EscalaNotificaSolicitante($id){

        $query = "
        --//----Solicitante
            SELECT 
                a.chapa_solicitante,
                a.tipo,
                b.nome nome_solicitante,
                b.email email_solicitante,
                c.NOME nome_funcionario,
                c.CHAPA chapa_funcionario
            FROM
                zcrmportal_escala a
                INNER JOIN zcrmportal_usuario b ON b.id = a.usucad
                INNER JOIN ".DBRM_BANCO."..PFUNC c ON c.CODCOLIGADA = A.coligada AND c.CHAPA = a.chapa COLLATE Latin1_General_CI_AS
            WHERE
                    a.id = {$id}
                AND a.situacao = 1
                        
        UNION ALL 
        
        --//----Gestor substituto
            SELECT 
                a.chapa_solicitante,
                a.tipo,
                b.SUBSTITUTO_NOME nome_solicitante,
                b.email email_solicitante,
                c.NOME nome_funcionario,
                c.CHAPA chapa_funcionario
            FROM
                zcrmportal_escala a
                INNER JOIN GESTOR_SUBSTITUTO_CHAPA b ON b.GESTOR_ID = a.usucad
                INNER JOIN ".DBRM_BANCO."..PFUNC c ON c.CODCOLIGADA = A.coligada AND c.CHAPA = a.chapa COLLATE Latin1_General_CI_AS
            WHERE
                    a.id = {$id}
                AND a.situacao = 1
                AND B.FUNCOES LIKE '%\"136\"%'
        ";

        $result = $this->dbportal->query($query);

        if($result->getNumRows() > 0){
            
            $dados = $result->getResultArray()[0];
            $mensagem = 'Olá <strong>'.$dados['nome_solicitante'].'</strong>,<br>
<br>
Requisição de alteração '.(($dados['tipo'] == 1) ? 'de escala' : 'de dia').' do colaborador <strong>'.$dados['nome_funcionario'].' - '.$dados['chapa_funcionario'].',</strong> foi aprovada e aguarda o upload de termo aditivo assinado.';

            $htmlEmail = templateEmail($mensagem);

            if($this->producao){
                enviaEmail($dados['email_solicitante'], 'Solicitação de Troca de '.(($dados['tipo'] == 1) ? 'Escala' : 'Dia').' Aprovada', $htmlEmail);
            }else{
                enviaEmail('jessica.silva@outserv.com.br', 'Solicitação de Troca de '.(($dados['tipo'] == 1) ? 'Escala' : 'Dia').' Aprovada', $htmlEmail);
            }


        }

        return false;

    }

    // -------------------------------------------------------
    // Notifica gestor sobre requisição alteração escala
    // -------------------------------------------------------
    public function EscalaNotificaGestor($chapa_solicitante, $chapa_funcionario){

        $query = "
            SELECT
                D.nome_gestor,
                D.email_gestor,
                E.NOME nome_funcionario
            FROM
                zcrmportal_hierarquia_lider A
                INNER JOIN zcrmportal_hierarquia_lider_func B ON A.id = B.id_lider
                INNER JOIN zcrmportal_hierarquia C ON C.id = A.id_hierarquia AND C.inativo IS NULL
                INNER JOIN GESTOR_DO_LIDER D ON D.chapa_funcionario = A.chapa AND D.coligada = A.coligada AND D.hierarquia_gestor = A.id_hierarquia
                INNER JOIN ".DBRM_BANCO."..PFUNC E ON E.CODCOLIGADA = A.coligada AND E.CHAPA = B.chapa COLLATE Latin1_General_CI_AS
            WHERE
                    A.chapa = '{$chapa_solicitante}' 
                AND A.coligada = {$this->coligada}
                AND B.chapa = '{$chapa_funcionario}' 
                AND A.inativo IS NULL
                AND '".date('Y-m-d')."' BETWEEN D.perini AND (CASE WHEN D.perfim IS NOT NULL THEN D.perfim ELSE '2090-12-31' END)
            
            UNION ALL 
            
            SELECT		
                F.SUBSTITUTO_NOME COLLATE Latin1_General_CI_AS,
                F.email,
                E.NOME nome_funcionario
            FROM
                zcrmportal_hierarquia_lider A
                INNER JOIN zcrmportal_hierarquia_lider_func B ON A.id = B.id_lider
                INNER JOIN zcrmportal_hierarquia C ON C.id = A.id_hierarquia AND C.inativo IS NULL
                INNER JOIN GESTOR_DO_LIDER D ON D.chapa_funcionario = A.chapa AND D.coligada = A.coligada AND D.hierarquia_gestor = A.id_hierarquia
                INNER JOIN ".DBRM_BANCO."..PFUNC E ON E.CODCOLIGADA = A.coligada AND E.CHAPA = B.chapa COLLATE Latin1_General_CI_AS
                INNER JOIN GESTOR_SUBSTITUTO_CHAPA F ON F.GESTOR_CHAPA = D.chapa_gestor AND F.CODCOLIGADA = D.coligada
            WHERE
                    A.chapa = '{$chapa_solicitante}'  -- funcionario solicitante (Email do gestor dele que é notificado)
                AND A.coligada = {$this->coligada}
                AND B.chapa = '{$chapa_funcionario}'  -- funcionario que terá a escala trocada
                AND A.inativo IS NULL
                AND '".date('Y-m-d')."' BETWEEN D.perini AND (CASE WHEN D.perfim IS NOT NULL THEN D.perfim ELSE '2090-12-31' END)
                AND F.FUNCOES LIKE '%\"181\"%'
        ";
        $result = $this->dbportal->query($query);

        if($result->getNumRows() > 0){

            $response = $result->getResultArray();
            foreach($response as $key => $dados){

                $mensagem = 'Prezado Gestor <strong>'.$dados['nome_gestor'].'</strong>,<br>
<br>
Requisição de alteração de escala do colaborador <strong>'.$dados['nome_funcionario'].' - '.$chapa_funcionario.',</strong> aguardando sua aprovação.';

                $htmlEmail = templateEmail($mensagem);

                if($this->producao){
                    enviaEmail($dados['email_gestor'], 'Solicitação de Troca de Escala', $htmlEmail);
                }

            }

        }

    }

    public function verificaFolgaDSR($data, $codhorario, $indice, $chapa)
    {

        $query = " SELECT MAX(DATAINICIO) INICIO_FERIAS FROM PFUFERIASPER WHERE CHAPA = '{$chapa}' AND CODCOLIGADA = {$this->coligada} AND SITUACAOFERIAS NOT IN ('F') ";
        $result = $this->dbrm->query($query);
        if($result){

            $dadosFerias = $result->getResultArray();
            $dataInicio = $dadosFerias[0]['INICIO_FERIAS'];
            if(strlen(trim($dataInicio)) <= 0) return false;
            $inicioFerias = dtEn($dataInicio, true);

            // calcula 2 dias após inicio das ferias
            $inicioFerias1 = \DateTime::createFromFormat('Y-m-d', $inicioFerias)->add(new \DateInterval('P1D'))->format('Y-m-d');
            $inicioFerias2 = \DateTime::createFromFormat('Y-m-d', $inicioFerias)->add(new \DateInterval('P2D'))->format('Y-m-d');

            $query = " SELECT * FROM dbo.CALCULO_HORARIO_FERIAS('".dtBr($data)."', '{$codhorario}', {$indice}, {$this->coligada}, 500, '{$chapa}')  ";
            $result = $this->dbrm->query($query);
            if($result){
                $response = $result->getResultArray();
                if($response){
                    foreach($response as $key => $Escala){

                        if(dtEn($Escala['DATA'], true) == $inicioFerias1 || dtEn($Escala['DATA'], true) == $inicioFerias2){

                            if($Escala['TIPO'] == 'DESCANSO'){
                                return 1;
                                break;
                            }

                        }

                        if(dtEn($Escala['DATA'], true) == $inicioFerias){

                            if($Escala['TIPO'] == 'DESCANSO'){
                                return 2;
                                break;
                            }

                        }
                        
                    }
                }
            }

        }

        return false;

    }

    public function dadosEscala($idEscala)
    {
        
        $query = " SELECT * FROM zcrmportal_escala WHERE id = '{$idEscala}' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function projecaoEscalaChapa($chapa)
    {

        $configuracao = self::Configuracao();
        
        if($chapa == null) return false;

        $query = "
            SELECT
                X.CODHORARIO, 
                CONVERT(VARCHAR, X.DATA, 103) DATA,
                X.CODINDICE, 
                X.CODCOLIGADA, 
                CASE WHEN D.DIAFERIADO IS NOT NULL THEN 'FERIADO' ELSE X.TIPO END TIPO,
                X.ENTRADA1, 
                X.SAIDA1,
                X.ENTRADA2,
                X.SAIDA2,
                ISNULL(B.CODSECAO, A.CODSECAO) CODSECAO,
                CASE WHEN D.DIAFERIADO IS NOT NULL THEN 1 ELSE 0 END DIAFERIADO

            FROM (

                SELECT * FROM DBO.PROJETA_ESCALA('".dtBr($configuracao[0]['escala_per_inicio'])."', '".dtBr($configuracao[0]['dia_per_fim'])."', '{$chapa}', {$this->coligada})

            )X
                LEFT JOIN PFUNC A ON A.CHAPA = '{$chapa}' AND A.CODCOLIGADA = {$this->coligada}
                LEFT JOIN PFHSTSEC B ON B.CHAPA = A.CHAPA AND B.CODCOLIGADA = A.CODCOLIGADA AND B.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTSEC WHERE CHAPA = B.CHAPA AND B.CODCOLIGADA = CODCOLIGADA AND DTMUDANCA <= CONVERT(DATETIME, DATA, 103))
                LEFT JOIN PSECAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = ISNULL(B.CODSECAO, A.CODSECAO)
                LEFT JOIN GFERIADO D ON D.CODCALENDARIO = C.CODCALENDARIO AND D.DIAFERIADO = CONVERT(DATETIME, DATA, 103)

            ORDER BY CONVERT(DATETIME, DATA, 103) ASC
        ";
        $result = $this->dbrm->query($query);
                
        if($result->getNumRows() > 0){
            $response = $result->getResultArray();
            $dadosJson = false;
            foreach($response as $key => $dadosResponse){

                $dadosJson[$key] = $dadosResponse;
                $dadosJson[$key]['DIA'] = diaSemana(dtEn($dadosResponse['DATA']), true);
                $dadosJson[$key]['DATAEN'] = dtEn($dadosResponse['DATA']);

                if((int)$dadosResponse['ENTRADA1'] >= 1440) $dadosResponse['ENTRADA1'] = 0;
                $dadosJson[$key]['ENTRADA1'] = m2h((int)$dadosResponse['ENTRADA1']);

                if((int)$dadosResponse['ENTRADA2'] >= 1440) $dadosResponse['ENTRADA2'] = 0;
                $dadosJson[$key]['ENTRADA2'] = m2h((int)$dadosResponse['ENTRADA2']);

                if((int)$dadosResponse['SAIDA1'] >= 1440) $dadosResponse['SAIDA1'] = 0;
                $dadosJson[$key]['SAIDA1'] = m2h((int)$dadosResponse['SAIDA1']);

                if((int)$dadosResponse['SAIDA2'] >= 1440) $dadosResponse['SAIDA2'] = 0;
                $dadosJson[$key]['SAIDA2'] = m2h((int)$dadosResponse['SAIDA2']);

                $dadosJson[$key]['ENTRADA1_MINUTO'] = h2m($dadosJson[$key]['ENTRADA1']);
                $dadosJson[$key]['ENTRADA2_MINUTO'] = h2m($dadosJson[$key]['ENTRADA2']);
                $dadosJson[$key]['SAIDA1_MINUTO'] = h2m($dadosJson[$key]['SAIDA1']);
                $dadosJson[$key]['SAIDA2_MINUTO'] = h2m($dadosJson[$key]['SAIDA2']);

            }

            return $dadosJson;
        }

        return false;
    }

}
?>