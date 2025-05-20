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
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm = db_connect('dbrm');
        $this->coligada = session()->get('func_coligada');
        $this->log_id = session()->get('log_id');
    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- TROCA DE ESCALA -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Lista funcionários que pode ver
    // -------------------------------------------------------
    public function ListarEscalaFuncionarios(){

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
		if($_SESSION['log_id']  != 1){
			$in_secao = " (".$filtro_secao_lider." ".$filtro_secao_gestor.") AND A.CHAPA != '{$chapaFunc}' ";
		}
		else{
			$in_secao = "";
		}

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
            ORDER BY
                A.NOME
        ";

        // echo '<pre>'.$query;exit();
        $result = $this->dbrm->query($query);
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
        // echo $query;exit();
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

        $data = $dados['data'] ?? null;
        $chapa = $dados['chapa'] ?? null;
        $codhorario = $dados['codhorario'] ?? null;
        $indice = $dados['indice'] ?? null;
        $qtde_dias = 10;

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
    // Cadastrar escala
    // -------------------------------------------------------
    public function CadastrarEscala($dados){

        $data = $dados['data'] ?? null;
        $chapa = $dados['chapa'] ?? null;
        $codhorario = $dados['codhorario'] ?? null;
        $indice = $dados['indice'] ?? null;
        $dtmudanca_historico = $dados['dtmudanca_historico'] ?? null;
        $codhorario_historico = $dados['codhorario_historico'] ?? null;        
        $chapa_solicitante = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        $horaEntrada = $dados['horaEntrada'] ?? null; 
        $horaSaida = $dados['horaSaida'] ?? null; 
        $dataDia = $dados['dataDia'] ?? null; 
        $horaEntrada2 = $dados['horaEntrada2'] ?? null; 
        $horaSaida2 = $dados['horaSaida2'] ?? null; 
        $dataDia2 = $dados['dataDia2'] ?? null;
        $justificativa_11_horas = $dados['justificativa_11_horas'] ?? "";
        $justificativa_6_dias = $dados['justificativa_6_dias'] ?? "";
        $justificativa_6_meses = $dados['justificativa_6_meses'] ?? "";

        if($data == null) return responseJson('error', '<b>Data</b> não informada.');
        if($chapa == null) return responseJson('error', '<b>Funcionário</b> não informado.');
        if($codhorario == null) return responseJson('error', '<b>Horário</b> não informado.');
        if($indice == null) return responseJson('error', '<b>Índice</b> não informado.');
        if($chapa_solicitante == null) return responseJson('error', '<b>Solicitante</b> inválido.');
        if($dtmudanca_historico == null) return responseJson('error', '<b>Histórico de horário</b> inválido.');

        $termo_obrigatorio = ($codhorario != $codhorario_historico) ? 1 : "NULL";

        // valida data da mudança
        $data_min = \DateTime::createFromFormat('d/m/Y', date('d/m/Y'))->add(new \DateInterval('P3D'))->format('Ymd');
        $data_min2 = \DateTime::createFromFormat('d/m/Y', date('d/m/Y'))->add(new \DateInterval('P3D'))->format('d/m/Y');
        $data_prog = \DateTime::createFromFormat('Y-m-d', $data)->format('Ymd');

        if($data_prog < $data_min) return responseJson('error', '<b>Data da nova escala</b> não permitida.<br>Permitido apartir de <b>'.$data_min2.'</b>');
        
        if($codhorario_historico != $codhorario && $justificativa_6_meses == ""){
            // VERIFICA A DATA DA ULTIMA TROCA DE HORÁRIO
            $dias = dataDiff($dtmudanca_historico, $data);
            if($dias < 180) return responseJson('error', 'Troca de escala não permitida antes de 6 meses do horário atual do funcionário. <br>Última alteração em <b>'.dtBr($dtmudanca_historico).'</b>', 1);
        }

        // verifica se o funcionário já possui uma solicitação em aberto
        $checkRequisicaoPendente = $this->VerificarRequisicaoChapa($chapa);
        if($checkRequisicaoPendente) return responseJson('error', 'Funcionário já possui uma requisição de troca de escala em aberto.');

        // verifica 35 horas de descanso dos ultimos 6 dias
        ##$result35horas = calcula35Horas($dataDia, $horaEntrada, $horaSaida, $dataDia2, $horaEntrada2, $horaSaida2);
        ##$result35horasEx = explode('|', $result35horas);
        
        ##if($result35horasEx[0] > 35) return responseJson('error', 'Funcionário deve descançar <b>35h</b> nos últimos 6 dias conseguitivos.<br><br>- Saída do último dia '.$result35horasEx[1].'<br>- Entrada do novo horário '.$result35horasEx[2].'<br>Horas '.$result35horasEx[0].'h');

        $query = " INSERT INTO zcrmportal_escala
            (chapa, coligada, datamudanca, codhorario, codindice, usucad, dtcad, chapa_solicitante, justificativa_11_horas, justificativa_6_dias, justificativa_6_meses, termo_obrigatorio) 
                VALUES
            ('{$chapa}', '{$this->coligada}', '{$data}', '{$codhorario}', '{$indice}', '{$this->log_id}', '".date('Y-m-d H:i:s')."', '{$chapa_solicitante}', ".(($justificativa_11_horas == "") ? "NULL" : "'{$justificativa_11_horas}'").", ".(($justificativa_6_dias == "") ? "NULL" : "'{$justificativa_6_dias}'").", ".(($justificativa_6_meses == "") ? "NULL" : "'{$justificativa_6_meses}'").", {$termo_obrigatorio})
        ";
        $this->dbportal->query($query);
        
        if($this->dbportal->affectedRows() > 0){

            $id_insert = $this->dbportal->insertID();

            // caso seja solicitação do gestor, já aprova alteração de escala
            $mHierarquia = model('HierarquiaModel');
            if($mHierarquia->isGestor()){

                $this->dbportal->query(" UPDATE zcrmportal_escala SET situacao = (CASE WHEN termo_obrigatorio = 1 THEN 1 ELSE 2 END), dtapr = '".date('Y-m-d H:i:s')."', usuapr = '{$this->log_id}' WHERE id = '{$id_insert}' AND coligada = '{$this->coligada}' AND situacao = 0 ");

                if($this->dbportal->affectedRows() > 0){
                    $query = " SELECT id FROM zcrmportal_escala WHERE id = '{$id_insert}' AND coligada = '{$this->coligada}' AND termo_obrigatorio = 1 AND situacao = 1 ";
                    $result = $this->dbportal->query($query);
                    if($result->getNumRows() > 0){
                        $this->EscalaNotificaSolicitante($id_insert);
                    }
                }

                notificacao('success', 'Escala Cadastrada e Aprovada com sucesso.');
                return responseJson('success', 'Escala Cadastrada e Aprovada com sucesso', id($this->dbportal->insertID()), id(($termo_obrigatorio == 1) ? 1 : 2));

            }else{
                $this->EscalaNotificaGestor($chapa_solicitante, $chapa);
            }

            notificacao('success', 'Escala cadastrada com sucesso.');
            return responseJson('success', 'Escala cadastrada com sucesso', id($this->dbportal->insertID()), id(0));
        }

        return responseJson('error', 'Falha ao cadastrar escala');

    }
    
    // -------------------------------------------------------
    // verifica se a chapa possui uma requisição aberta pendente
    // -------------------------------------------------------
    public function VerificarRequisicaoChapa($chapa){

        $query = " SELECT * FROM zcrmportal_escala WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' AND situacao not in (3, 9, 8) ";
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

        $existe_registro = $this->Configuracao();
        if($existe_registro){

            // update
            $query = "
                UPDATE
                    zcrmportal_escala_config
                SET
                    bloqueio_aviso = ".(($bloqueio_aviso) ? "1" : "NULL").",
                    usualt = '{$this->log_id}',
                    dtalt = '".date('Y-m-d H:i:s')."'
                WHERE
                    coligada = '{$this->coligada}'
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
                usualt,
                dtalt
            ) VALUES (
                '{$this->coligada}',
                ".(($bloqueio_aviso) ? "1" : "NULL").",
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
        $perfil_rh = $mAcesso->VerificaPerfil('PONTO_TROCADEESCALA_RH');

        $situacao_rh = " AND a.situacao = 2 ";
        $situacao_gestor = " AND a.situacao = 0 ";
        $situacao_solicitante = "";
        $distinct = ($id) ? "" : "DISTINCT";

        if($filtro !== null && $filtro != ""){
            $filtro_in = ($filtro == 9) ? "8,9" : $filtro;
            $situacao_rh = " AND a.situacao IN ({$filtro_in}) ";
            $situacao_gestor = " AND a.situacao IN ({$filtro_in}) ";
            $situacao_solicitante = $situacao_gestor;
        }

        $sql_rh = "";
        if($perfil_rh){
            $sql_rh = "
                UNION ALL

                SELECT 
                    {$distinct} 
                    a.termo_obrigatorio,
                    a.justificativa_11_horas,
                    a.justificativa_6_dias,
                    a.justificativa_6_meses,
                    a.motivocancelado,
                    a.id,
                    a.chapa,
                    a.datamudanca,
                    a.codhorario,
                    a.codindice,
                    a.situacao,
                    2 step,
                    a.dtcancelado
                    {$table_documento}
                FROM 
                    zcrmportal_escala a
                WHERE
                        a.coligada = '{$this->coligada}'
                        {$situacao_rh}
                    {$ft_id}
            ";
        }

        $query = " 
            SELECT 
            {$distinct} 
            a.termo_obrigatorio,
            a.justificativa_11_horas,
            a.justificativa_6_dias,
            a.justificativa_6_meses, 
            a.motivocancelado,
            a.id,
            a.chapa,
            a.datamudanca,
            a.codhorario,
            a.codindice,
            a.situacao,
            0 step,
            a.dtcancelado
             {$table_documento} FROM zcrmportal_escala a WHERE a.coligada = '{$this->coligada}' {$ft_id} AND a.usucad = '{$this->log_id}' {$situacao_solicitante} 
            

            UNION ALL

            SELECT 
                {$distinct}  
                a.termo_obrigatorio,
                a.justificativa_11_horas,
                a.justificativa_6_dias,
                a.justificativa_6_meses,
                a.motivocancelado,
                a.id,
                a.chapa,
                a.datamudanca,
                a.codhorario,
                a.codindice,
                a.situacao,
                1 step,
                a.dtcancelado
                {$table_documento}
            FROM 
                zcrmportal_escala a,
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

            UNION ALL

            SELECT 
                {$distinct}  
                a.termo_obrigatorio,
                a.justificativa_11_horas,
                a.justificativa_6_dias,
                a.justificativa_6_meses,
                a.motivocancelado,
                a.id,
                a.chapa,
                a.datamudanca,
                a.codhorario,
                a.codindice,
                a.situacao,
                1 step,
                a.dtcancelado
                {$table_documento}
            FROM 
                zcrmportal_escala a,
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

            {$sql_rh}

                
        ";
        
        //echo '<pre>'.$query;exit();
        $result = $this->dbportal->query($query);

        if($result->getNumRows() > 0){

            $mPortal = model("PortalModel");
            
            $response = $result->getResultArray();
            $dadosArray = array();
            foreach($response as $key => $dados){

                $dadosArray[$key] = (!$dados) ? array() : $dados;
                $dadosChapa = $mPortal->ListarDadosFuncionario(false, $dados['chapa'], false);
                $dadosArray[$key]['nome'] = $dadosChapa[0]['NOME'] ?? "";

            }
            
            return $dadosArray;
        }
        return false;

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
                E.SERIECARTTRAB CTPS_SERIE
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
            case 1: $query = " UPDATE zcrmportal_escala SET situacao = (CASE WHEN termo_obrigatorio = 1 THEN 1 ELSE 2 END), dtapr = '".date('Y-m-d H:i:s')."', usuapr = '{$this->log_id}' WHERE id = '{$id}' AND coligada = '{$this->coligada}' AND situacao = 0 "; break;
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

        $query = "
            UPDATE 
                zcrmportal_escala
            SET
                documento = '{$file_name}|{$file_type}|{$file_size}|{$file_file}',
                dtupload = '".date('Y-m-d H:i:s')."',
                usuupload = '{$this->log_id}',
                situacao = 2
            WHERE
                    id = {$id}
                AND situacao = 1
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

        $query = " SELECT chapa, codhorario, datamudanca, codindice FROM zcrmportal_escala WHERE coligada = {$this->coligada} AND id = {$id_requisicao} ";
        $result = $this->dbportal->query($query);

        if($result->getNumRows() <= 0) return false;

        $DadosEscala = $result->getResultArray()[0];

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
                    ({$this->coligada}, '{$DadosEscala['chapa']}', '{$DadosEscala['codhorario']}', dbo.CalculoIndicePontoMais('{$DadosEscala['codindice']}', '".date('d/m/Y', strtotime($DadosEscala['datamudanca']))."', '{$DadosEscala['codhorario']}', {$this->coligada}), '".date('Y-m-d', strtotime($DadosEscala['datamudanca']))."', '".date('Y-m-d H:i:s')."', 'PORTAL.{$this->log_id}', '".date('Y-m-d H:i:s')."', 1, 1)
            ";
            $this->dbrm->query($PFHSTHOR);

            return true;

        }

        return false;

    }
    
    // -------------------------------------------------------
    // Cancela escala sem anexo do documento assinado 10 dias 
    // antes da data de mudança
    // -------------------------------------------------------
    public function CancelarEscala10Dias(){

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
            SELECT 
                a.chapa_solicitante,
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
        ";
        $result = $this->dbportal->query($query);

        if($result->getNumRows() > 0){
            
            $dados = $result->getResultArray()[0];
            $mensagem = 'Olá <strong>'.$dados['nome_solicitante'].'</strong>,<br>
<br>
Requisição de alteração de escala do funcionário <strong>'.$dados['nome_funcionario'].' - '.$dados['chapa_funcionario'].',</strong> foi aprovada e aguarda o upload de termo aditivo assinado.';

            $htmlEmail = templateEmail($mensagem);

            enviaEmail($dados['email_solicitante'], 'Solicitação de Troca de Escala Aprovada', $htmlEmail);
            //enviaEmail('tiago.moselli@crmservices.com.br', 'Solicitação de Troca de Escala Aprovada', $htmlEmail);
            //enviaEmail('tiago.moselli@crmservices.com.br', 'Solicitação de Troca de Escala Aprovada', $htmlEmail);
            //enviaEmail('samuel.santana@crmservices.com.br', 'Solicitação de Troca de Escala Aprovada', $htmlEmail);
            //enviaEmail('edmir.santos@eldoradobrasil.com.br', 'Solicitação de Troca de Escala Aprovada', $htmlEmail);


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
        ";
        $result = $this->dbportal->query($query);

        if($result->getNumRows() > 0){

            $response = $result->getResultArray();
            foreach($response as $key => $dados){

                $mensagem = 'Prezado Gestor <strong>'.$dados['nome_gestor'].'</strong>,<br>
<br>
Requisição de alteração de escala do funcionário <strong>'.$dados['nome_funcionario'].' - '.$chapa_funcionario.',</strong> aguardando sua aprovação.';

                $htmlEmail = templateEmail($mensagem);

                enviaEmail($dados['email_gestor'], 'Solicitação de Troca de Escala', $htmlEmail);
                //enviaEmail('edmir.santos@eldoradobrasil.com.br', 'Solicitação de Troca de Escala', $htmlEmail);
                //enviaEmail('samuel.santana@crmservices.com.br', 'Solicitação de Troca de Escala', $htmlEmail);
                //enviaEmail('tiago.moselli@crmservices.com.br', 'Solicitação de Troca de Escala', $htmlEmail);

            }

        }

    }

}
?>