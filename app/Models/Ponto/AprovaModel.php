<?php

namespace App\Models\Ponto;

use SimpleXMLElement;
use DateTime;

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
  public $producao;

  public function __construct()
  {
    $this->dbportal = db_connect('dbportal');
    $this->dbrm     = db_connect('dbrm');
    $this->log_id   = session()->get('log_id');
    $this->coligada = session()->get('func_coligada');
    $this->now      = date('Y-m-d H:i:s');
    $this->mEscala = model('Ponto/EscalaModel');
    $this->producao   = (DBRM_BANCO == 'CorporeRMPRD') ? true : false;

    if (DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
    if (DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
  }

  // #############################################################################
  // APROVA DE ESCALA
  // #############################################################################
  public function aprovaEscala($idEscala, $rh = false)
  {

    $escala = $this->dbportal->query(" SELECT chapa, situacao FROM zcrmportal_escala WHERE id = '{$idEscala}' AND situacao IN (10,2) ");
    $result = ($escala) ? $escala->getResultArray() : null;

    if ($result) {

      $situacao = $result[0]['situacao'];

      $chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
      if ($chapaUser == $result[0]['chapa']) return false;

      if (!$rh) {
        if (!self::isGestorOrLiderAprovador($result[0]['chapa'])) {
          return false;
        }
      }

      switch ($situacao) {
        case 10:
          $query = " UPDATE zcrmportal_escala SET situacao = 2, dtapr = '" . date('Y-m-d H:i:s') . "', usuapr = '{$this->log_id}' WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' AND situacao = 10 ";
          break;
        case 2:
          $query = " UPDATE zcrmportal_escala SET situacao = 3, dtrh = '" . date('Y-m-d H:i:s') . "', usurh = '{$this->log_id}' WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' AND situacao = 2 ";
          break;
        default:
          return false;
          break;
      }

      $this->dbportal->query($query);
      if ($this->dbportal->affectedRows() > 0) {

        $query = " SELECT id FROM zcrmportal_escala WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' and termo_obrigatorio = 1 AND situacao = 1 ";
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
          $this->mEscala->EscalaNotificaSolicitante($idEscala);
        }

        if ($situacao == 2) {
          $result = $this->mEscala->SincronizaRM_Horario($idEscala);

          if ($result === false) {

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
  // APROVA DE ART 61
  // #############################################################################
  public function aprovaArtigo61($idReq, $rh = false)
  {

    $art61 = $this->dbportal->query(" SELECT chapa_requisitor, chapa_gestor, status FROM zcrmportal_art61_requisicao WHERE id = '{$idReq}' AND status IN (2,4) ");
    $result = ($art61) ? $art61->getResultArray() : null;

    if ($result) {

      $status = $result[0]['status'];

      $chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
      if ($chapaUser == $result[0]['chapa_requisitor']) return false;

      if (!$rh) {
        if (!self::isGestorOrLiderAprovador($chapaUser)) {
          return false;
        }
        $query = " UPDATE zcrmportal_art61_requisicao SET status = 3, dt_aprovacao = '" . date('Y-m-d H:i:s') . "', id_aprovador = '{$this->log_id}', chapa_aprov_reprov = '{$chapaUser}' WHERE id = '{$idReq}' AND status = 2 ";

      } else {
        if ($status == 4) {
          $query = " UPDATE zcrmportal_art61_requisicao SET status = 5, dt_rh_aprovacao = '" . date('Y-m-d H:i:s') . "', id_aprovador = '{$this->log_id}', chapa_rh_aprov_reprov = '{$chapaUser}' WHERE id = '{$idReq}' AND status = 4 ";
        } else {
          $query = " UPDATE zcrmportal_art61_requisicao SET status = 3, dt_aprovacao = '" . date('Y-m-d H:i:s') . "', id_aprovador = '{$this->log_id}', chapa_aprov_reprov = '{$chapaUser}' WHERE id = '{$idReq}' AND status = 2 ";
        }
      }

      $this->dbportal->query($query);
      if ($this->dbportal->affectedRows() > 0) {
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
			justificativa_excecao,
			ISNULL((SELECT BASE FROM " . DBRM_BANCO . "..AAFHTFUN WHERE CHAPA = zcrmportal_ponto_horas.chapa COLLATE Latin1_General_CI_AS AND CODCOLIGADA = zcrmportal_ponto_horas.coligada AND DATA = zcrmportal_ponto_horas.dtponto),0) htrab,
			(CASE WHEN abn_dtfim > abn_dtini THEN abn_horafim+1440 - abn_horaini ELSE abn_horafim - abn_horaini END) horas_abono
		FROM 
			zcrmportal_ponto_horas 
		WHERE 
				status IN ('1','2','3', 'A') 
			AND usu_delete IS NULL
			AND id = '" . $idbatida . "' ";
    $qry = $this->dbportal->query($query);
    $res = ($qry) ? $qry->getResultArray() : array();

    try {

      $atualizou = false;

      // $wsTotvs = model('WsrmModel');

      $chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
      if ($chapaUser == $res[0]['chapa']) return false;

      if (!$rh) {
        if (!self::isGestorOrLiderAprovador($res[0]['chapa'])) {
          return false;
        }
      }


      // ***************************************************************
      // APROVA NATUREZA
      // ***************************************************************
      if ($res[0]['movimento'] == '3') {

        $queryRM = " UPDATE ABATFUN SET NATUREZA = '" . $res[0]['natureza'] . "' WHERE IDAAFDT = '" . $res[0]['idbatida'] . "' AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
        $resRM = $this->dbrm->query($queryRM);

        if ($resRM) $atualizou = true;
      }
      // FIM  **********************************************************

      // ***************************************************************
      // APROVA DATA REFERECIA
      // ***************************************************************
      if ($res[0]['movimento'] == '4') {

        $queryRM = " UPDATE ABATFUN SET DATAREFERENCIA = '" . $res[0]['dtref'] . "' WHERE IDAAFDT = '" . $res[0]['idbatida'] . "' AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
        $resRM = $this->dbrm->query($queryRM);

        if ($resRM) $atualizou = true;
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

        if ($resRM) $atualizou = true;
      }
      // FIM  **********************************************************

      // ***************************************************************
      // APROVA INCLUSAO DE BATIDA
      // ***************************************************************
      if ($res[0]['movimento'] == '1') {

        $queryRM = " INSERT INTO ABATFUN 
					(CODCOLIGADA, CHAPA, DATA, DATAREFERENCIA, BATIDA, STATUS, NATUREZA, RECCREATEDBY, RECCREATEDON, DATAINSERCAO) 
						VALUES 
					('" . $_SESSION['func_coligada'] . "', '" . $res[0]['chapa'] . "', '" . $res[0]["dtponto"] . "', '" . $res[0]["dtref"] . "', '" . $res[0]["batida"] . "', 'T', '" . $res[0]["natureza"] . "', 'PORT." . $_SESSION['log_id'] . "', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "') 
				";
        $resRM = $this->dbrm->query($queryRM);

        $queryRM = " INSERT INTO AJUSTBAT 
					(CODCOLIGADA, CHAPA, DATA, BATIDA, JUSTIFICA, RECCREATEDBY, RECCREATEDON) VALUES
					('" . $_SESSION['func_coligada'] . "', '" . $res[0]['chapa'] . "', '" . $res[0]["dtponto"] . "', '" . $res[0]["batida"] . "', '" . trim(addslashes($res[0]["justificativa"])) . "', 'PORT." . $_SESSION['log_id'] . "', '" . date('Y-m-d H:i:s') . "')
				";
        $resRM = $this->dbrm->query($queryRM);

        if ($resRM) $atualizou = true;
      }
      // FIM  **********************************************************

      // ***************************************************************
      // APROVA ABONO
      // ***************************************************************
      if ($res[0]['movimento'] == '5' || $res[0]['movimento'] == '6' || $res[0]['movimento'] == '9') {

        $SOLUCAOCONFLITO = 0;

        $desconsidera = 1;

        if ($res[0]['movimento'] == '9') {
          $desconsidera = 0;
        }

        if ($res[0]['movimento'] == '5') $SOLUCAOCONFLITO = 1;

        if (strlen(trim($res[0]['horas_abono'])) > 0) {
          if ((int)$res[0]['horas_abono'] >= (int)$res[0]['htrab']) {
            $SOLUCAOCONFLITO = 6;
          }
        }

        if ($res[0]['abn_horaini'] > $res[0]['abn_horafim']) {

          $queryRM = " INSERT INTO AABONFUN
						(CODCOLIGADA, CHAPA, DATA, CODABONO, HORAINICIO, HORAFIM, SOLUCAOCONFLITO, ABONOFUTURO, COLIGADARESP, DESCONSIDERA,RESPONSAVEL, RECCREATEDBY, RECCREATEDON)
							VALUES
						('" . $_SESSION['func_coligada'] . "', '" . $res[0]['chapa'] . "', '" . $res[0]["dtponto"] . "', '" . $res[0]["abn_codabono"] . "', '" . $res[0]["abn_horaini"] . "', '1440', '" . $SOLUCAOCONFLITO . "', 0, '" . $_SESSION['func_coligada'] . "', " . $desconsidera . ", '" . $res[0]['chapa'] . "', 'PORT." . $_SESSION['log_id'] . "', '" . date('Y-m-d H:i:s') . "')
					";
          $resRM = $this->dbrm->query($queryRM);

          $queryRM = " INSERT INTO AABONFUN
						(CODCOLIGADA, CHAPA, DATA, CODABONO, HORAINICIO, HORAFIM, SOLUCAOCONFLITO, ABONOFUTURO, COLIGADARESP, DESCONSIDERA,RESPONSAVEL, RECCREATEDBY, RECCREATEDON)
							VALUES
						('" . $_SESSION['func_coligada'] . "', '" . $res[0]['chapa'] . "', '" . $res[0]["abn_dtfim"] . "', '" . $res[0]["abn_codabono"] . "', '0', '" . $res[0]["abn_horafim"] . "', '" . $SOLUCAOCONFLITO . "', 0, '" . $_SESSION['func_coligada'] . "', " . $desconsidera . ", '" . $res[0]['chapa'] . "', 'PORT." . $_SESSION['log_id'] . "', '" . date('Y-m-d H:i:s') . "')
					";
          $resRM = $this->dbrm->query($queryRM);
        } else {

          $queryRM = " INSERT INTO AABONFUN
						(CODCOLIGADA, CHAPA, DATA, CODABONO, HORAINICIO, HORAFIM, SOLUCAOCONFLITO, ABONOFUTURO, COLIGADARESP, DESCONSIDERA,RESPONSAVEL, RECCREATEDBY, RECCREATEDON)
							VALUES
						('" . $_SESSION['func_coligada'] . "', '" . $res[0]['chapa'] . "', '" . $res[0]["abn_dtini"] . "', '" . $res[0]["abn_codabono"] . "', '" . $res[0]["abn_horaini"] . "', '" . $res[0]["abn_horafim"] . "', '" . $SOLUCAOCONFLITO . "', 0, '" . $_SESSION['func_coligada'] . "'," . $desconsidera . ", '" . $res[0]['chapa'] . "', 'PORT." . $_SESSION['log_id'] . "', '" . date('Y-m-d H:i:s') . "')
					";
          $resRM = $this->dbrm->query($queryRM);
        }

        if ($resRM) $atualizou = true;
      }

      // ***************************************************************
      // APROVA INCLUSAO JUSTIFICATIVA DE EXCEÇÕES
      // ***************************************************************
      if ($res[0]['movimento'] == '7') {

        $update = " 
					UPDATE 
						AJUSTFUN 
					SET 
						JUSTIFICATIVA = 'Descontar' ,
						RECMODIFIEDBY = 'USERPORTAL_" . $_SESSION['log_id'] . "',
						RECMODIFIEDON = '" . date('Y-m-d H:i:s') . "'
						
					WHERE 
							DATA = '{$res[0]["atitude_dt"]}' 
						AND CHAPA = '{$res[0]['chapa']}' 
						AND TIPOOCORRENCIA IN ('A','F','AC', 'SA')
						AND CODCOLIGADA = '{$this->coligada}'
						
					";
        $resRM = $this->dbrm->query($update);
        if ($resRM) $atualizou = true;
      }

      if ($res[0]['movimento'] == '8') {

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
        if ($resRM) $atualizou = true;
      }


      if ($atualizou) {
        $update_batida = " UPDATE zcrmportal_ponto_horas SET status = 'S', aprrh_user = '" . $_SESSION['log_id'] . "', aprrh_data = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $idbatida . "' ";
        $res_update = $this->dbportal->query($update_batida);
        return true;
      }
    } catch (\Exception $e) {

      return $e->getMessage();
    }

    return true;
  }

  // #############################################################################
  // REPROVA TROCA DE ESCALA
  // #############################################################################
  public function reprovaEscala($idEscala, $motivo_reprova = "", $rh = false)
  {
    $escala = $this->dbportal->query("
			SELECT e.chapa, e.id, e.situacao, e.tipo, e.dtcad, FORMAT(e.dtcad, 'dd/MM/yyyy') dtcad_br, 
				  u.nome, u.email, e.datamudanca, e.datamudanca_folga, e.codindice_folga, e.codhorario, FORMAT(e.datamudanca, 'dd/MM/yyyy') dtmud_br, FORMAT(e.datamudanca_folga, 'dd/MM/yyyy') dtmud_folga_br, 
					d.DESCRICAO horario, e.codindice, b.nome nome_colab
			FROM zcrmportal_escala e
			LEFT JOIN zcrmportal_usuario u (NOLOCK) ON u.id = e.usucad
			INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = e.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = e.coligada
			LEFT JOIN " . DBRM_BANCO . "..AHORARIO D (NOLOCK) ON D.CODIGO = e.codhorario COLLATE Latin1_General_CI_AS AND D.CODCOLIGADA = e.coligada
			WHERE e.id = '{$idEscala}' 
			AND e.situacao != '3' 
		");
    $result = ($escala) ? $escala->getResultArray() : null;

    if ($result) {

      $chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
      if ($chapaUser == $result[0]['chapa']) return false;

      if (!$rh) {
        if (!self::isGestorOrLiderAprovador($result[0]['chapa'])) {
          notificacao('danger', 'Alguns movimento não podem ser aprovados.');
          return false;
        }
      }

      $situacao = $result[0]['situacao'];
      $tipo_solicitacao = ($result[0]['tipo'] == 1) ? 'Troca de escala' : 'Troca de dia';
      $data_mud = $result[0]['datamudanca'];
      $data_solicitacao = $result[0]['dtcad'];
      $nome_solicitante = $result[0]['nome'];
      $email_solicitante = $result[0]['email'];
      $chapa_colab = $result[0]['chapa'];
      $nome_colab = $result[0]['nome_colab'];
      $reprovador = $_SESSION['log_nome'];
      $codindice = $result[0]['codindice'];
      $codindice_folga = $result[0]['codindice_folga'];
      $dtmud_br = $result[0]['dtmud_br'];
      $dtmud_folga_br = $result[0]['dtmud_folga_br'];
      $horario = $result[0]['horario'];

      $query = " UPDATE zcrmportal_escala SET documento = NULL, dtupload = NULL, usuupload = NULL, situacao = 8, motivocancelado = '(" . ((!$rh) ? 'Gestor' : 'RH') . "){$motivo_reprova}', dtcancelado = '" . date('Y-m-d H:i:s') . "', usucancelado = '{$this->log_id}' WHERE id = '{$idEscala}' AND coligada = '{$this->coligada}' AND situacao NOT IN (9, 3, 8) ";
      $this->dbportal->query($query);
      if ($this->dbportal->affectedRows() > 0) {

        $tipo_solicitacao = ($result[0]['tipo'] == 1) ? 'Troca de escala' : 'Troca de dia';
        $data_solicitacao = $result[0]['dtcad_br'];
        $nome_solicitante = $result[0]['nome'];
        $email_solicitante = $result[0]['email'];

        $mensagem = '
				Prezado(a) ' . $nome_solicitante . ',<br><br>
				Sua solicitação no Portal RH - Módulo de Ponto foi <strong>reprovada</strong>.<br><br>

				<strong><u>Detalhes da Solicitação</u></strong><br>
				<strong>• Tipo: </strong>' . $tipo_solicitacao . '<br>
				<strong>• Data da Solicitação</strong>: ' . $data_solicitacao . '<br>
        <strong>• Colaborador</strong>: ' . $chapa_colab . ' - ' . $nome_colab . '<br>
        <strong>• Descrição do Tipo:</strong><br>
				';

        if ($result[0]['tipo'] == 1) {
          $mensagem = $mensagem . '
          <strong>&nbsp&nbsp&nbsp- Data</strong>: ' . $dtmud_br . '<br>
          <strong>&nbsp&nbsp&nbsp- Índice</strong>: ' . $codindice . '<br>
          <strong>&nbsp&nbsp&nbsp- Horário</strong>: ' . $horario . '<br><br>
          ';
        }

        if ($result[0]['tipo'] != 1) {
          $mensagem = $mensagem . '
          <strong>&nbsp&nbsp&nbsp- Data Útil</strong>: ' . $dtmud_br . '<br>
          <strong>&nbsp&nbsp&nbsp- Índice Útil</strong>: ' . $codindice . '<br>
          <strong>&nbsp&nbsp&nbsp- Data Folga</strong>: ' . $dtmud_folga_br . '<br>
          <strong>&nbsp&nbsp&nbsp- Índice Folga</strong>: ' . $codindice_folga . '<br>
          <strong>&nbsp&nbsp&nbsp- Horário</strong>: ' . $horario . '<br><br>
          ';
        }

        $mensagem = $mensagem . '
				<strong>Motivo da Reprovação</strong>: ' . $motivo_reprova . '<br>
				<strong>Usuário que Reprovou</strong>: ' . $reprovador . '<br><br>
				Caso necessário, você pode realizar um novo envio dentro do período de ponto vigente.<br><br>

				Atenciosamente,<br>
				<strong>Equipe Processos de RH</strong>
				';
        $htmlEmail = templateEmail($mensagem);

        $email_solicitante = 'deivison.batista@eldoradobrasil.com.br';
        //$email_solicitante = 'alvaro.zaragoza@ativary.com';
        enviaEmail($email_solicitante, '[Portal RH] Sua Solicitação Foi Reprovada', $htmlEmail);

        return responseJson('success', 'Escala REPROVADA com sucesso');
      }
    }

    return false;
  }

  // #############################################################################
  // REPROVA REQUISIÇÃO ART.61
  // #############################################################################
  public function reprovaArt61($id_req, $motivo_reprova = "", $rh = false)
  {
    $query = "
      SELECT 
        FORMAT(r.dt_requisicao, 'dd/MM/yyyy') dtreq_br,
        r.status,
        r.chapa_requisitor,
        b.nome nome_requisitor,
        u.email email_requisitor,
        FORMAT(r.dt_ini_ponto, 'dd/MM/yyyy') dtini_br,
        FORMAT(r.dt_fim_ponto, 'dd/MM/yyyy') dtfim_br,
        ( SELECT COUNT(DISTINCT c.chapa_colab) AS qtde 
          FROM zcrmportal_art61_req_chapas c
          WHERE c.id_req = r.id AND c.status <> 'I'
        ) as colaboradores,
        (
          SELECT 
          RIGHT('000' + CAST(CAST(h.total AS INT) / 60 AS VARCHAR), 3) + ':' + 
          RIGHT('00' + CAST(CAST(h.total AS INT) % 60 AS VARCHAR), 2) 
        FROM (	SELECT SUM(c.valor) AS total
            FROM zcrmportal_art61_req_chapas c
            WHERE c.id_req = r.id
              AND c.status <> 'I' ) h
        ) as horas,
          (SELECT SUM(c.valor) AS total
            FROM zcrmportal_art61_req_chapas c
            WHERE c.id_req = r.id
              AND c.status <> 'I' 
        ) as horas_min
			FROM zcrmportal_art61_requisicao r
			LEFT JOIN email_chapa u (NOLOCK) ON u.chapa = r.chapa_requisitor COLLATE Latin1_General_CI_AS and u.codcoligada = r.id_coligada
			INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = r.chapa_requisitor COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = r.id_coligada
			WHERE r.id = '{$id_req}' 
		";
    //echo $query;
    //die();
    $art61 = $this->dbportal->query($query);
    $result = ($art61) ? $art61->getResultArray() : null;

    if ($result) {

      $chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? 'RH';
      if ($chapaUser == $result[0]['chapa_requisitor']) {
        notificacao('danger', 'Requisição não pode ser reprovada pelo requisitor.');
        return false;
      }

      if (!$rh) {
        if (!self::isGestorOrLiderAprovador($result[0]['chapa_requisitor'])) {
          notificacao('danger', 'Requisição não pode ser reprovada.');
          return false;
        }
      }

      if (is_null($result[0]['email_requisitor'])) {
        notificacao('danger', 'Requisição não pode ser reprovada. Email do requisitor não encontrado');
        return false;
      }

      $tipo_solicitacao = 'Artigo 61';
      $status = $result[0]['status'];
      $data_solicitacao = $result[0]['dtreq_br'];
      $nome_solicitante = $result[0]['nome_requisitor'];
      $email_solicitante = $result[0]['email_requisitor'];
      $colaboradores = $result[0]['colaboradores'];
      $horas = $result[0]['horas'];
      $reprovador = $_SESSION['log_nome'];
      
      if ($status == 2) {
        $query = " UPDATE zcrmportal_art61_requisicao SET status = 9, motivo_recusa = '(" . ((!$rh) ? 'Gestor' : 'RH') . "){$motivo_reprova}', dt_aprovacao = '" . date('Y-m-d H:i:s') . "', chapa_aprov_reprov = '{$chapaUser}' WHERE id = '{$id_req}'";

      } else {
        $query = " UPDATE zcrmportal_art61_requisicao SET status = 9, motivo_recusa = '(" . ((!$rh) ? 'Gestor' : 'RH') . "){$motivo_reprova}', dt_rh_aprovacao = '" . date('Y-m-d H:i:s') . "', chapa_rh_aprov_reprov = '{$chapaUser}' WHERE id = '{$id_req}' AND status IN (3, 4, 5) ";
      }
      $this->dbportal->query($query);
      
      if ($this->dbportal->affectedRows() > 0) {

        $mensagem = '
				Prezado(a) ' . $nome_solicitante . ',<br><br>
				Sua solicitação no Portal RH - Módulo de Ponto foi <strong>reprovada</strong>.<br><br>

				<strong><u>Detalhes da Solicitação</u></strong><br>
				<strong>• Tipo: </strong>' . $tipo_solicitacao . '<br>
				<strong>• Data da Solicitação</strong>: ' . $data_solicitacao . '<br>
        <strong>• Descrição do Tipo:</strong><br>
				';

        $mensagem = $mensagem . '
        <strong>&nbsp&nbsp&nbsp- Colaboradores</strong>: ' . $colaboradores . '<br>
        <strong>&nbsp&nbsp&nbsp- Horas</strong>: ' . $horas . '<br><br>
        ';
       
        $mensagem = $mensagem . '
				<strong>Motivo da Reprovação</strong>: ' . $motivo_reprova . '<br>
				<strong>Usuário que Reprovou</strong>: ' . $reprovador . '<br><br>
				Caso necessário, você pode realizar um novo envio dentro do período de ponto vigente.<br><br>

				Atenciosamente,<br>
				<strong>Equipe Processos de RH</strong>
				';
        $htmlEmail = templateEmail($mensagem);

        //$email_solicitante = 'deivison.batista@eldoradobrasil.com.br';
        $email_solicitante = 'alvaro.zaragoza@ativary.com';
        enviaEmail($email_solicitante, '[Portal RH] Sua Solicitação Foi Reprovada', $htmlEmail);

        return responseJson('success', 'Requisição do Artigo 61 REPROVADA com sucesso');
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

    $query = " 
			SELECT 
        h.id, u.nome, u.email, h.movimento,
        (
					SELECT max(CAST(BB.descricao AS VARCHAR)) 
          FROM zcrmportal_ponto_justificativa_func AA  (NOLOCK) 
					INNER JOIN zcrmportal_ponto_motivos BB (NOLOCK) ON 
            AA.justificativa = BB.id AND AA.coligada = BB.codcoligada 
					WHERE AA.coligada = h.coligada AND AA.dtponto = h.dtponto AND AA.chapa = h.chapa
				) justificativa_excecao,
        CAST(COALESCE(h.ent1, h.ent2, h.ent3, h.ent4, h.ent5, h.sai1, h.sai2, h.sai3, h.sai4, h.sai5)  / 60 AS VARCHAR(8)) + ':' + 
		    FORMAT(COALESCE(h.ent1, h.ent2, h.ent3, h.ent4, h.ent5, h.sai1, h.sai2, h.sai3, h.sai4, h.sai5)  % 60, 'D2') batida,
        COALESCE(h.justent1, h.justent2, h.justent3, h.justent4, h.justent5, h.justsai1, h.justsai2, h.justsai3, h.justsai4, h.justsai5) motivo,
		    FORMAT(COALESCE(h.dtrefent1, h.dtrefent2, h.dtrefent3, h.dtrefent4, h.dtrefent5, h.dtrefsai1, h.dtrefsai2, h.dtrefsai3, h.dtrefsai4, h.dtrefsai5), 'dd/MM/yyyy') data_ref_br,
        FORMAT(h.dtponto, 'dd/MM/yyyy') data_br,
        FORMAT(h.dtcadastro, 'dd/MM/yyyy') dtcad_br, 
        h.abn_codabono, d.descricao desc_abono, 
	      FORMAT(h.abn_dtini, 'dd/MM/yyyy') dtini_br, 
        FORMAT(h.abn_dtfim, 'dd/MM/yyyy') dtfim_br, 
	      CAST(h.abn_horaini / 60 AS VARCHAR(8)) + ':' + FORMAT(h.abn_horaini % 60, 'D2') hora_ini,
	      CAST(h.abn_horafim / 60 AS VARCHAR(8)) + ':' + FORMAT(h.abn_horafim % 60, 'D2') hora_fim,
        CASE 
          WHEN h.abn_horafim is NULL THEN NULL
          WHEN h.abn_horafim >= h.abn_horaini THEN
            CAST((h.abn_horafim-h.abn_horaini) / 60 AS VARCHAR(8)) + ':' + FORMAT((h.abn_horafim-h.abn_horaini) % 60, 'D2')
          ELSE
            CAST((1440+h.abn_horafim-h.abn_horaini) / 60 AS VARCHAR(8)) + ':' + FORMAT((1440+h.abn_horafim-h.abn_horaini) % 60, 'D2')
          END tot_horas,
        FORMAT(h.atitude_dt, 'dd/MM/yyyy') dtatitude_br, 
        CAST(h.atitude_fim / 60 AS VARCHAR(8)) + ':' + FORMAT(h.atitude_fim % 60, 'D2') hora_atitude,
        CASE 
              WHEN h.movimento NOT IN (7,8) THEN NULL
          WHEN h.movimento = 7 THEN 'Atraso não remunerado'
          WHEN h.atitude_tipo = 1 THEN 'Compensar (Fica BH)'
          ELSE 'Descontar no pagto'
        END tipo_atitude,
	      h.chapa, b.nome nome_colab
			FROM zcrmportal_ponto_horas h
			LEFT JOIN zcrmportal_usuario u (NOLOCK) ON u.id = h.usucad
      INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = h.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = h.coligada
			LEFT JOIN " . DBRM_BANCO . "..AABONO D (NOLOCK) ON D.CODIGO = h.abn_codabono COLLATE Latin1_General_CI_AS AND D.CODCOLIGADA = h.coligada
			WHERE h.id = '{$dados[2]}' ";

    $result = $this->dbportal->query($query);
    $resbat = $result->getResultArray();

    $chapa = $resbat[0]['chapa'];

    if (!$rh) {
      if (!self::isGestorOrLiderAprovador($chapa)) {
        notificacao('danger', 'Alguns movimento não podem ser aprovados.');
        //notificacao('danger', 'Alguns movimento não podem ser reprovados.');
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
			, apr_user = NULL, apr_data = NULL, aprgestor_data = NULL, aprgestor_user = NULL, motivo_reprova = '" . addslashes($motivo_reprova) . "', usu_reprova = '" . $_SESSION['log_id'] . "', dt_reprova = '" . date('Y-m-d H:i:s') . "' WHERE id = '" . $dados[2] . "'
				AND status <> 'S'
			";
      $result = $this->dbportal->query($query);

      if ($result) {
        $query = "

					INSERT INTO zcrmportal_ponto_horas_reprova
					SELECT * FROM zcrmportal_ponto_horas WHERE id = '" . $dados[2] . "'
				
				";
        $this->dbportal->query($query);

        $tipo_solicitacao = '';
        switch ($resbat[0]['movimento']) {
          case 1:
            $tipo_solicitacao = 'Inclusão de registro';
            break;
          case 2:
            $tipo_solicitacao = 'Exclusão de registro';
            break;
          case 3:
            $tipo_solicitacao = 'Alteração de natureza';
            break;
          case 4:
            $tipo_solicitacao = 'Alteração jornada referência';
            break;
          case 5:
            $tipo_solicitacao = 'Abono de atrasos';
            break;
          case 6:
            $tipo_solicitacao = 'Abono de faltas';
            break;
          case 7:
            $tipo_solicitacao = 'Justificativa de exceção';
            break;
          case 8:
            if (is_null($resbat[0]['justificativa_excecao'])) {
              $tipo_solicitacao = 'Altera atitude';
            } else {
              $tipo_solicitacao = $resbat[0]['justificativa_excecao'];
            }
            break;
          case 9:
            $tipo_solicitacao = 'Falta não remunerada';
            break;
        }
        $data_solicitacao = $resbat[0]['dtcad_br'];
        $nome_solicitante = $resbat[0]['nome'];
        $email_solicitante = $resbat[0]['email'];

        $data_br = $resbat[0]['data_br'];
        $chapa_colab = $resbat[0]['chapa'];
        $nome_colab = $resbat[0]['nome_colab'];
        $reprovador = $_SESSION['log_nome'];
        $abn_codabono = $resbat[0]['abn_codabono'];
        $desc_abono = $resbat[0]['desc_abono'];
        $dtini_br = $resbat[0]['dtini_br'];
        $dtfim_br = $resbat[0]['dtfim_br'];
        $hora_ini = $resbat[0]['hora_ini'];
        $hora_fim = $resbat[0]['hora_fim'];
        $tot_horas = $resbat[0]['tot_horas'];
        $batida = $resbat[0]['batida'];
        $motivo = $resbat[0]['motivo'];
        $data_ref_br = $resbat[0]['data_ref_br'];
        $dtatitude_br = $resbat[0]['dtatitude_br'];
        $hora_atitude = $resbat[0]['hora_atitude'];
        $tipo_atitude = $resbat[0]['tipo_atitude'];

        $desc_abono = ($tipo_solicitacao == 'Falta não remunerada') ? 'FALTA NÃO REMUNERADA' : $desc_abono;

        $mensagem = '
				Prezado(a) ' . $nome_solicitante . ',<br><br>
				Sua solicitação no Portal RH - Módulo de Ponto foi <strong>reprovada</strong>.<br><br>
				<strong><u>Detalhes da Solicitação</u></strong><br>
				<strong>• Tipo: </strong>' . $tipo_solicitacao . '<br>
				<strong>• Data</strong>: ' . $data_br . '<br>
				<strong>• Colaborador</strong>: ' . $chapa_colab . ' - ' . $nome_colab . '<br>
        ';

        $mensagem = $mensagem . '
        <strong>• Descrição do Tipo:</strong><br>
        ';

        if (!is_null($dtini_br)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Data Início</strong>: ' . $dtini_br . ' ' . $hora_ini . '<br>';
        }
        if (!is_null($dtfim_br)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Data Fim</strong>: ' . $dtfim_br . ' ' . $hora_fim . '<br>';
        }
        if (!is_null($tot_horas)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Total de Horas</strong>: ' . $tot_horas . '<br>';
        }
        if (!is_null($abn_codabono)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Tipo de Abono</strong>: ' . $abn_codabono . ' - ' . $desc_abono . '<br>';
        }
        if (!is_null($batida)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Registro</strong>: ' . $batida . '<br>';
        }
        if (!is_null($motivo)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Justificativa</strong>: ' . $motivo . '<br>';
        }
        if (!is_null($data_ref_br)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Data Referência</strong>: ' . $data_ref_br . '<br>';
        }
        if (!is_null($dtatitude_br)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Data</strong>: ' . $dtatitude_br . '<br>';
        }
        if (!is_null($hora_atitude)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Horas</strong>: ' . $hora_atitude . '<br>';
        }
        if (!is_null($tipo_atitude)) {
          $mensagem = $mensagem . '<strong>&nbsp&nbsp&nbsp- Tipo Atitude</strong>: ' . $tipo_atitude . '<br>';
        }

        $mensagem = $mensagem . '
				<strong>• Data da Solicitação</strong>: ' . $data_solicitacao . '<br>
				<strong>• Motivo da Reprovação</strong>: ' . $motivo_reprova . '<br>
				<strong>• Usuário que Reprovou</strong>: ' . $reprovador . '<br><br>
				Caso necessário, você pode realizar um novo envio dentro do período de ponto vigente. <br><br>

				Atenciosamente,<br>
				<strong>Equipe Processos de RH</strong>
				';
        $htmlEmail = templateEmail($mensagem);

        $email_solicitante = 'deivison.batista@eldoradobrasil.com.br';
        //$email_solicitante = 'alvaro.zaragoza@ativary.com';
        enviaEmail($email_solicitante, '[Portal RH] Sua Solicitação Foi Reprovada', $htmlEmail);
      }

      return $result;
    }
  }

  // #############################################################################
  // APROVA BATIDA GESTOR
  // #############################################################################
  public function  aprovaBatidaGestor($idbatida, $tipo)
  {

    $dados = explode('|', $idbatida);
    // var_dump($dados);exit();

    // APROVA GESTOR
    if ($tipo == 'GESTOR') {

      $query = " UPDATE zcrmportal_ponto_horas SET status = '2', aprgestor_user = '" . session()->get('log_id') . "', aprgestor_data = GETDATE() WHERE id = '" . $dados[2] . "'";
      // echo $query;exit();
      return $this->dbportal->query($query);
    }
  }


  // #############################################################################
  // LISTA BATIDA PRO GESTOR
  // #############################################################################
  public function listaBatidaApr($status, $codfilial = false, $codccusto = false, $movimento = false, $tipo_abono = false, $ft_legenda = false, $ft_status = false, $dt_inicio = false, $dt_fim = false, $filtroChapa = '', $periodo = false, $dados = false)
  {

    $periodo          = explode('|', $periodo);
    $perInicio        = $periodo[0];
    $perFim           = $periodo[1];
    $periodo          = " AND A.dtponto BETWEEN '{$periodo[0]}' AND '{$periodo[1]}' ";
    $periodoEscala    = " AND (a.datamudanca BETWEEN '{$perInicio}' AND '{$perFim}' OR a.datamudanca_folga BETWEEN '{$perInicio}' AND '{$perFim}' ) ";
    $periodoArt61     = " AND r.dt_ini_ponto = '".$perInicio."' ";

    $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    
    if ($codfilial) {
      if ($codfilial != 'all') {

        // #################################################################
        // FILTRO POR TODAS SEÇÃO
        // #################################################################				
        $LT_CHAPAS = false;

        $chapasRM = " SELECT CHAPA FROM PFUNC WHERE CODSITUACAO <> 'D' AND CODSECAO = '" . $codfilial . "'";
        // exit($chapasRM);
        $qry = $this->dbrm->query($chapasRM);
        $resChapaRM = ($qry) ? $qry->getResultArray() : false;
        if ($resChapaRM && is_array($resChapaRM)) {
          foreach ($resChapaRM as $idc => $value) {
            $LT_CHAPAS .= "'" . $resChapaRM[$idc]['CHAPA'] . "',";
          }
        }

        if ($resChapaRM && is_array($resChapaRM) && $resChapaRM > 0) {
          $codfilial = " AND A.chapa IN (" . substr($LT_CHAPAS, 0, -1) . ") ";
        } else {
          return false;
        }

        // #################################################################

      } else {
        $codfilial = false;
      }
    }

    if ($codccusto) {
      if ($codccusto != 'all') {

        // #################################################################
        // FILTRO POR CENTRO DE CUSTO
        // #################################################################				
        $LT_CHAPAS = false;

        $chapasRM = " 
            SELECT
                A.CHAPA
            FROM 
                PFUNC A
            LEFT JOIN PSECAO S ON S.CODCOLIGADA = A.CODCOLIGADA AND S.CODIGO = A.CODSECAO
            LEFT JOIN GCCUSTO C ON C.CODCOLIGADA = S.CODCOLIGADA AND C.CODCCUSTO = S.NROCENCUSTOCONT

            WHERE 
                A.CODSITUACAO <> 'D' 
            AND C.CODCCUSTO = '" . $codccusto . "'";
        // exit($chapasRM);
        $qry = $this->dbrm->query($chapasRM);
        $resChapaRM = ($qry) ? $qry->getResultArray() : false;
        if ($resChapaRM && is_array($resChapaRM)) {
          foreach ($resChapaRM as $idc => $value) {
            $LT_CHAPAS .= "'" . $resChapaRM[$idc]['CHAPA'] . "',";
          }
        }

        if ($resChapaRM && is_array($resChapaRM) && $resChapaRM > 0) {
          $codccusto = " AND A.chapa IN (" . substr($LT_CHAPAS, 0, -1) . ") ";
        } else {
          return false;
        }

        // #################################################################

      } else {
        $codccusto = '';
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
    switch (DBRM_TIPO) {
      case 'sqlserver':
        $hoje = " GETDATE() ";
        break;
      case 'oracle':
        $hoje = " SYSDATE ";
        break;
    }

    $filtro_gestor = " AND A.CHAPA IN ('') ";
    $mPortal  = model('PortalModel');

    $secaoGestor = $mPortal->listaFuncionarioSecao();
    if ($secaoGestor) {
      $filtro_gestor = "";
      foreach ($secaoGestor as $key => $DadosFunc) {
        $filtro_gestor .= "'{$DadosFunc['CHAPA']}',";
      }
      $filtro_gestor = " AND A.CHAPA IN (" . rtrim($filtro_gestor, ',') . ")";
    }

    if ($_SESSION['log_id'] == 1) {
      $filtro_gestor = "";
    }

    //======= FILTRO POR ABONO ==========//by Matheus
    $FT_ABONO = false;
    if ($tipo_abono) {

      $FT_ABONO = " AND A.abn_codabono = '" . $tipo_abono . "'";
    }

    //========== FILTRO POR MOVIMENTO ==============//by Matheus
    $FT_MOVIMENTO = false;
    if ($ft_legenda) {

      $FT_MOVIMENTO = " AND A.movimento = '" . $ft_legenda . "'";
    }

    $FT_STATUS = false;
    if ($ft_status) {

      $FT_STATUS = " A.status = '" . $ft_status . "'";
    } else {

      $FT_STATUS = " A.status in ('1','2')";
    }


    //-----------------------------------------
    // filtro das chapas que o lider pode ver
    //-----------------------------------------
    $mHierarquia = Model('HierarquiaModel');
    $objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
    $isLider = $mHierarquia->isLider();

    $filtro_chapa_lider = "";
    $filtro_secao_lider = "";
    if ($isLider) {
      $chapas_lider = "";
      $codsecoes = "";
      foreach ($objFuncLider as $idx => $value) {
        $chapas_lider .= "'" . $objFuncLider[$idx]['chapa'] . "',";
      }
      $filtro_secao_lider = " A.CHAPA IN (" . substr($chapas_lider, 0, -1) . ") OR ";
    }


    //-----------------------------------------
    // filtro das seções que o gestor pode ver
    //-----------------------------------------
    $secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
    $filtro_secao_gestor = "";

    if ($secoes) {
      $codsecoes = "";
      foreach ($secoes as $ids => $Secao) {
        $codsecoes .= "'" . $Secao['codsecao'] . "',";
      }
      $filtro_secao_gestor = " B.CODSECAO IN (" . substr($codsecoes, 0, -1) . ") OR ";
    }
    //-----------------------------------------

    // monta o where das seções
    if ($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
    if ($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    if ($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    $chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

    // Monta filtro de chapa_gestor para artigo61
    if($chapa <> null) {
      $in_art61 = " AND ( r.chapa_gestor = '".$chapa."' OR g.ger_chapa = '".$chapa."' ) ";
    }
    
    if ($this->log_id  != 1) {
      $in_secao = " AND (" . $filtro_secao_lider . " " . $filtro_secao_gestor . ") AND A.CHAPA != '{$chapaFunc}' ";
    } else {
      $in_secao = "";
      $in_art61 = "";
    }

    $filtro_chapa = (strlen(trim($filtroChapa)) <= 0 || $filtroChapa == 'all') ? "" : " AND a.chapa = '{$filtroChapa}' ";

    if ($dados['perfilRH']) {
        $in_secao = "";
        $in_art61 = "";
    }

    $filtro_filial = (strlen(trim($dados['filtro_filial'])) != 0) ? " AND B.CODFILIAL = '{$dados['filtro_filial']}' " : '';
    $filtro_legenda = "";
    $filtro_legenda2 = "";
    $in_leg_art61 = "AND r.status in (2, 3, 4, 5, 6) ";
    if ((strlen(trim($dados['filtro_legenda'])) != 0)) {
      if ($dados['filtro_legenda'] == 10) {
        $filtro_legenda2 = " AND a.situacao = '10' ";
        $in_leg_art61 = " AND r.status = 2 ";
      }
      if ($dados['filtro_legenda'] == 2) {
        $filtro_legenda = " AND 1 = 2 ";
        $filtro_legenda2 = " AND a.situacao = '2' ";
        $in_leg_art61 = " AND r.status = 4 ";
      }

    }


    //(strlen(trim($dados['filtro_legenda'])) != 0) ? " AND a.situacao = '{$dados['filtro_legenda']}' " : '';

    /* DESATIVADO EM 17/05/2025 - Unificação PONTO E TROCAS ESCALA/DIA
    $filtro_1 = "";
    $filtro_2 = "";
    if (strlen(trim($dados['filtro_tipo'])) > 0) {
      if ($dados['filtro_tipo'] == 21 || $dados['filtro_tipo'] == 22) {
        $filtro_1 = " AND 1 = 2 ";
        $filtro_2 = " AND 1 = 1 ";
        $filtro_tipo = ($dados['filtro_tipo'] == 21) ? ' and a.movimento = 1 ' : ' and a.movimento = 2 ';
      } else {
        $filtro_1 = " AND 1 = 1 ";
        $filtro_2 = " AND 1 = 2 ";
      }
    }
    */

    $filtro_tipo_art61 = '';
    if ((strlen(trim($dados['filtro_tipo2'])) != 0)) {
       if ($dados['filtro_tipo2'] != '61') {$filtro_tipo_art61 = ' and r.id = -1 ';}
    }

    $filtro_tipo_ponto = '';
    if ($dados['filtro_tipo2'] == '1') {$filtro_tipo_ponto = ' and a.movimento = 1 ';}
    if ($dados['filtro_tipo2'] == '2') {$filtro_tipo_ponto = ' and a.movimento = 2 ';}
    if ($dados['filtro_tipo2'] == '3') {$filtro_tipo_ponto = ' and a.movimento = 3 ';}
    if ($dados['filtro_tipo2'] == '4') {$filtro_tipo_ponto = ' and a.movimento = 4 ';}
    if ($dados['filtro_tipo2'] == '5') {$filtro_tipo_ponto = ' and a.movimento = 5 ';}
    if ($dados['filtro_tipo2'] == '6') {$filtro_tipo_ponto = ' and a.movimento = 6 ';}
    if ($dados['filtro_tipo2'] == '7') {$filtro_tipo_ponto = ' and a.movimento = 7 ';}
    if ($dados['filtro_tipo2'] == '8') {$filtro_tipo_ponto = ' and a.movimento = 8 ';}
    if ($dados['filtro_tipo2'] == '9') {$filtro_tipo_ponto = ' and a.movimento = 9 ';}
    if ($dados['filtro_tipo2'] == '21' or $dados['filtro_tipo2'] == '22' or $dados['filtro_tipo2'] == '61') {
      $filtro_tipo_ponto = ' and a.movimento = -1 ';
    }

    $filtro_tipo_escala = '';
    if ($dados['filtro_tipo2'] == '21') {$filtro_tipo_escala = ' and a.tipo = 1 ';}
    if ($dados['filtro_tipo2'] == '22') {$filtro_tipo_escala = ' and a.tipo = 2 ';}
    if ($dados['filtro_tipo2'] == '1' or $dados['filtro_tipo2'] == '2' or $dados['filtro_tipo2'] == '3' or  $dados['filtro_tipo2'] == '4' or $dados['filtro_tipo2'] == '5' or $dados['filtro_tipo2'] == '6' or $dados['filtro_tipo2'] == '7' or $dados['filtro_tipo2'] == '8' or $dados['filtro_tipo2'] == '9' or $dados['filtro_tipo2'] == '61') {
      $filtro_tipo_escala = ' and a.tipo = -1 ';
    }

    // if(isset($dados['filtro_tipo'])) { if($dados['filtro_tipo'] == "") { return false; } } else { return false; }
    // UNIFICAÇÃO DE Unificação PONTO E TROCAS ESCALA/DIA
    if($dados['filtro_tipo'] == "ponto") {
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
					0 situacao,
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
							" . DBRM_BANCO . "..PPESSOA AA
							INNER JOIN " . DBRM_BANCO . "..PFUNC BB ON BB.CODPESSOA = AA.CODIGO
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
										" . DBRM_BANCO . "..PFUNC (NOLOCK)
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
						" . DBRM_BANCO . "..PFHSTSIT HA (NOLOCK)
						INNER JOIN " . DBRM_BANCO . "..PCODSITUACAO HB (NOLOCK) ON HB.CODCLIENTE = HA.NOVASITUACAO
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
					) justificativa_excecao,
          NULL art61_colaboradores,
          NULL art61_horas,
	        NULL art61_chapa_gerente,
          A.obs	obs,
          (
              SELECT TOP 1 AA.obs
              FROM zcrmportal_ponto_justificativa_func AA (NOLOCK)
              WHERE AA.coligada = A.coligada
                AND AA.dtponto = A.dtponto
                AND AA.chapa = A.chapa
            ) obs_just
				FROM
					zcrmportal_ponto_horas A (NOLOCK)
					INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
					LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
					INNER JOIN " . DBRM_BANCO . "..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
				WHERE
					" . $FT_STATUS . "
					AND A.coligada = '{$_SESSION['func_coligada']}'
          AND A.motivo_reprova IS NULL
          AND A.usu_delete IS NULL
					{$in_secao}
					" . $FT_ABONO . "
					" . $FT_MOVIMENTO . "
					" . $codfilial . "
					" . $codccusto . "
					{$periodo}
					{$filtro_chapa}
					{$filtro_filial}
					{$filtro_legenda}
					{$filtro_tipo_ponto}
					
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
					a.situacao,
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
							" . DBRM_BANCO . "..PPESSOA AA
							INNER JOIN " . DBRM_BANCO . "..PFUNC BB ON BB.CODPESSOA = AA.CODIGO
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
										" . DBRM_BANCO . "..PFUNC (NOLOCK)
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
						" . DBRM_BANCO . "..PFHSTSIT HA (NOLOCK)
						INNER JOIN " . DBRM_BANCO . "..PCODSITUACAO HB (NOLOCK) ON HB.CODCLIENTE = HA.NOVASITUACAO
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
					NULL justificativa_excecao,
          NULL art61_colaboradores,
          NULL art61_horas,
	        NULL art61_chapa_gerente,
	        NULL obs,
	        NULL just_obs
				FROM
					zcrmportal_escala a (NOLOCK)
					INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
					LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
					LEFT JOIN " . DBRM_BANCO . "..AHORARIO D (NOLOCK) ON D.CODIGO = a.codhorario COLLATE Latin1_General_CI_AS AND D.CODCOLIGADA = a.coligada
					INNER JOIN " . DBRM_BANCO . "..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
				WHERE
					a.coligada = '{$_SESSION['func_coligada']}'
					and a.situacao in (10,2)
					{$in_secao}
					" . $codfilial . "
					" . $codccusto . "
					{$periodoEscala}
					{$filtro_chapa}
					{$filtro_filial}
					{$filtro_legenda2}
					{$filtro_tipo_escala}

        UNION ALL

        SELECT 
          r.id					as id,
          CAST(r.dt_requisicao AS DATETIME) as dtponto,
          null					as dtfolga,
          null					as codindice_folga,
          61						as movimento,
          r.chapa_gestor as chapa,
          null					as batida,
          null					as motivo,
          null					as natureza,
          CAST(r.dt_requisicao AS DATETIME) as data_referencia,
          null					as abn_dtfim,
          null					as abn_horaini,
          null					as abn_horafim,
          null					as abn_codabono,
          null					as abn_totalhoras,
          null					as possui_anexo,
          r.id_coligada			as coligada,
          r.status				as status,
          r.status				as situacao,
          null					as justificativa_abono_tipo,
          null					as atitude_dt,
          null					as atitude_ini,
          null					as atitude_fim,
          null					as atitude_tipo,
          null					as atitude_justificativa,
          f.nome COLLATE Latin1_General_CI_AS				as nome_solicitante,
          r.chapa_requisitor COLLATE Latin1_General_CI_AS	as chapa_solicitante,
          null					as codhorario,
          null					as horario,
          null					as codindice,
          null					as justificativa_escala,
          e.CPF as CPF,
          (
              SELECT TOP 1 HB.DESCRICAO
              FROM " . DBRM_BANCO . "..PFHSTSIT HA (NOLOCK)
                INNER JOIN " . DBRM_BANCO . "..PCODSITUACAO HB (NOLOCK) ON HB.CODCLIENTE = HA.NOVASITUACAO
              WHERE HA.CODCOLIGADA = f.CODCOLIGADA
                AND HA.CHAPA = f.CHAPA 
                AND (HA.DATAMUDANCA <= r.dt_requisicao)
              ORDER BY DATAMUDANCA DESC
            ) as CODSITUACAO,
          u.NOME as NOME,
          CAST(r.dt_requisicao AS DATETIME)	as data_solicitacao,
          null					as justificativa_excecao,
          ( SELECT COUNT(DISTINCT c.chapa_colab) AS qtde 
            FROM zcrmportal_art61_req_chapas c
            WHERE c.id_req = r.id AND c.status <> 'I'
          ) as art61_colaboradores,
          ( SELECT SUM(c.valor) AS total 
            FROM zcrmportal_art61_req_chapas c
            WHERE c.id_req = r.id AND c.status <> 'I'
          ) as art61_horas,
	        g.ger_chapa as art61_chapa_gerente,
	        NULL obs,
	        NULL just_obs
          FROM zcrmportal_art61_requisicao r
            LEFT JOIN " . DBRM_BANCO . "..PFUNC f ON f.CODCOLIGADA = r.id_coligada
            AND f.CHAPA = r.chapa_requisitor COLLATE Latin1_General_CI_AS
            LEFT JOIN " . DBRM_BANCO . "..PFUNC u ON u.CODCOLIGADA = r.id_coligada
            AND u.CHAPA = r.chapa_gestor COLLATE Latin1_General_CI_AS
            INNER JOIN " . DBRM_BANCO . "..PPESSOA e (NOLOCK) ON e.CODIGO = f.CODPESSOA
            LEFT JOIN GESTORES_ABAIXO_GERENTE g ON (g.n1_chapa = r.chapa_gestor or g.n2_chapa = r.chapa_gestor)
          WHERE r.id is not null 
          {$in_art61}
          {$in_leg_art61}
          {$periodoArt61}
          {$filtro_tipo_art61}
 			)X
			ORDER BY
				X.chapa,
				X.dtponto
		  ";
    } else {
      return false;
    }

    //echo $query;
    //die();
    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      $response = array();
      $result = $result->getResultArray();
      foreach ($result as $key => $Dados) {
        $response[$key] = $Dados;

        $response[$key]['batidas_dia'] = self::listaBatidasDoDia($Dados['coligada'], $Dados['chapa'], dtEn($Dados['dtponto'], true));

        $nome_funcionario   = '-demitido-';
        $cpf_functionario   = '';
        $nome_gestor        = '';
        $chapa_gestor       = '';
        $codsituacao        = '';
        $Dados['GESTOR_CHAPA'] = '';
        $Dados['GESTOR_NOME'] = '';
        $response[$key]['nome']           = $Dados['NOME'];
        $response[$key]['cpf']            = $Dados['CPF'];
        $response[$key]['nome_gestor']    = $Dados['GESTOR_NOME'];
        $response[$key]['chapa_gestor']   = $Dados['GESTOR_CHAPA'];
        $response[$key]['codsituacao']    = $Dados['CODSITUACAO'];
        $response[$key]['nomechapa']      = $Dados['chapa'] . ' - ' . $Dados['NOME'] . ' <span style="font-size: 12px; margin-right: 5px;">[Situação: ' . $Dados['CODSITUACAO'] . ']</span> <small><b>=> [GESTOR: ' . $Dados['GESTOR_CHAPA'] . ' - ' . $Dados['GESTOR_NOME'] . ']</b></small>';


        unset($result[$key], $key, $Dados);
      }
      unset($result, $dados_func);
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
    if ($isLider) {
      $chapas_lider = "";
      $codsecoes = "";
      foreach ($objFuncLider as $idx => $value) {
        $chapas_lider .= "'" . $objFuncLider[$idx]['chapa'] . "',";
      }
      $filtro_secao_lider = " A.CHAPA IN (" . substr($chapas_lider, 0, -1) . ") OR ";
    }


    //-----------------------------------------
    // filtro das seções que o gestor pode ver
    //-----------------------------------------
    $secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
    $filtro_secao_gestor = "";

    if ($secoes) {
      $codsecoes = "";
      foreach ($secoes as $ids => $Secao) {
        $codsecoes .= "'" . $Secao['codsecao'] . "',";
      }
      $filtro_secao_gestor = " A.CODSECAO IN (" . substr($codsecoes, 0, -1) . ") OR ";
    }
    //-----------------------------------------

    // monta o where das seções
    if ($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
    if ($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    if ($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    $chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

    if ($this->log_id  != 1) {
      $qr_secao = " AND (" . $filtro_secao_lider . " " . $filtro_secao_gestor . ") AND A.CHAPA != '{$chapaFunc}' ";
    } else {
      $qr_secao = "";
    }

    // lista seções
    $filtro_secao = ($codsecao != null) ? " AND A.CODSECAO = '{$codsecao}' " : "";

    if ($dados) {
      if (($dados['perfilRH'] ?? false) || ($dados['rh'] ?? false)) $qr_secao = "";
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
    if (!$result) return false;
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

  public function ListaDadosAnexos($id_anexo)
  {
    $query = "SELECT abono_atestado arquivo, anexo_batida FROM zcrmportal_ponto_horas WHERE id = '" . $id_anexo . "'";

    $result = $this->dbportal->query($query);
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  public function ListaArt61Anexo($id_req_chapa, $linha)
  {
    $query = "
      with anexos as (
      select ROW_NUMBER() OVER (ORDER BY id) as linha, * 
      from zcrmportal_art61_req_chapa_anexo
      where id_req_chapa = ".$id_req_chapa." 
      )

      select * from anexos
      where linha = ".$linha;

    //echo $query;
    //exit();
    $result = $this->dbportal->query($query);
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  public function RecalculoPonto($array_recalculo)
  {

    $wsTotvs = model('WsrmModel');

    if ($array_recalculo) {
      if (count($array_recalculo) > 0) {
        foreach ($array_recalculo as $key => $dadosRecalculo) {
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
    if (!is_array($codsecao)) {
      if ($codsecao == 'all') $codsecao = null;
    }
    //-----------------------------------------
    // filtro das chapas que o lider pode ver
    //-----------------------------------------
    $mHierarquia = Model('HierarquiaModel');
    $objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
    $isLider = $mHierarquia->isLider();

    $filtro_chapa_lider = "";
    $filtro_secao_lider = "";
    if ($isLider) {
      $chapas_lider = "";
      $codsecoes = "";
      foreach ($objFuncLider as $idx => $value) {
        $chapas_lider .= "'" . $objFuncLider[$idx]['chapa'] . "',";
      }
      $filtro_secao_lider = " A.CHAPA IN (" . substr($chapas_lider, 0, -1) . ") OR ";
    }


    //-----------------------------------------
    // filtro das seções que o gestor pode ver
    //-----------------------------------------
    $secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
    $filtro_secao_gestor = "";

    if ($secoes) {
      $codsecoes = "";
      foreach ($secoes as $ids => $Secao) {
        $codsecoes .= "'" . $Secao['codsecao'] . "',";
      }
      $filtro_secao_gestor = " A.CODSECAO IN (" . substr($codsecoes, 0, -1) . ") OR ";
    }
    //-----------------------------------------

    // monta o where das seções
    if ($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
    if ($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    if ($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");

    $chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    if ($aprovacao) {
      $qr_secao = " AND (" . $filtro_secao_lider . " " . $filtro_secao_gestor . ")  AND A.CHAPA != '{$chapaFunc}' ";
    } else {
      $qr_secao = " AND (" . $filtro_secao_lider . " " . $filtro_secao_gestor . " OR A.CHAPA = '{$chapaFunc}')";
    }


    if ($dados) {
      if ($dados['rh'] ?? false || $dados['perfilRH'] ?? false) $qr_secao = "";
    }

    // lista seções
    if (!is_array($codsecao)) {
      $filtro_secao = ($codsecao != null) ? " AND A.CODSECAO = '{$codsecao}' " : "";
    } else {
      if (is_array($codsecao)) {
        $filtro_secao = "";
        $codsecao_in = "";
        foreach ($codsecao as $codSecao) {
          $codsecao_in .= "'{$codSecao}',";
        }

        $filtro_secao = " AND A.CODSECAO IN (" . rtrim($codsecao_in, ',') . ") ";
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
						" . DBPORTAL_BANCO . "..zcrmportal_ponto_horas p
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
    if (!$result) return false;
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
    if (!$result) return false;
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
    if ($isLider) {
      $chapas_lider = "";
      $codsecoes = "";
      foreach ($objFuncLider as $idx => $value) {
        $chapas_lider .= "'" . $objFuncLider[$idx]['chapa'] . "',";
      }
      $filtro_secao_lider = " A.CHAPA IN (" . substr($chapas_lider, 0, -1) . ") OR ";
    }


    //-----------------------------------------
    // filtro das seções que o gestor pode ver
    //-----------------------------------------
    $secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
    $filtro_secao_gestor = "";

    if ($secoes) {
      $codsecoes = "";
      foreach ($secoes as $ids => $Secao) {
        $codsecoes .= "'" . $Secao['codsecao'] . "',";
      }
      $filtro_secao_gestor = " A.CODSECAO IN (" . substr($codsecoes, 0, -1) . ") OR ";
    }
    //-----------------------------------------

    // monta o where das seções
    if ($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
    if ($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    if ($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    $chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    if ($this->log_id  != 1) {
      $in_secao = " AND (" . $filtro_secao_lider . " " . $filtro_secao_gestor . ") AND A.CHAPA != '{$chapaFunc}' ";
    } else {
      $in_secao = "";
    }





    if ($rh) $in_secao = "";



    $query = " SELECT A.CHAPA, A.NOME, A.CODSECAO FROM PFUNC A WHERE A.CODSITUACAO <> 'D' {$in_secao} ORDER BY A.NOME ASC";

    $result = $this->dbrm->query($query);

    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  public function isGestorOrLiderAprovador($chapaColaborador = false, $funcao = false)
  {
    //Funcao de aprovador do ponto
    $funcao = ($funcao == false) ? 181 : $funcao;

    $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
    if ($chapa == null) return false;

    $query = " SELECT * FROM zcrmportal_hierarquia_chapa WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' ";
    $result = $this->dbportal->query($query);
    if ($result) {
      if ($result->getNumRows() > 0) return true;
    }

    $query = " SELECT * FROM zcrmportal_hierarquia_chapa_sub WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' AND inativo IS NULL ";
    $result = $this->dbportal->query($query);
    if ($result) {
      if ($result->getNumRows() > 0) return true;
    }

    $query = " SELECT * FROM zcrmportal_hierarquia_gestor_substituto A
                  LEFT JOIN zcrmportal_hierarquia_gestor_substituto_modulos B ON a.modulos LIKE '%\"' + CAST(B.id AS VARCHAR) + '\"%'
                  WHERE A.chapa_substituto = '{$chapa}' 
                    AND A.coligada = '{$this->coligada}' 
                    AND A.inativo = 0
                    AND B.funcoes like '%\"{$funcao}\"%'
      ";
    $result = $this->dbportal->query($query);
    if ($result) {
      if ($result->getNumRows() > 0) return true;
    }


    $query = "
        SELECT * FROM zcrmportal_hierarquia_lider_func_ponto WHERE chapa = '{$chapaColaborador}' AND coligada = '{$this->coligada}' AND inativo IS NULL AND id_lider IN (
            SELECT id FROM zcrmportal_hierarquia_lider_ponto WHERE chapa = '{$chapa}' AND coligada = '{$this->coligada}' AND inativo IS NULL AND nivel = 1
        )
       ";
    $result = $this->dbportal->query($query);
    if ($result) {
      if ($result->getNumRows() > 0) return true;
    }

    return false;
  }

  public function listaBatidasDoDia($coligada, $chapa, $data)
  {
    $result       = $this->dbrm->query("SELECT BATIDA, NATUREZA FROM ABATFUN WHERE CHAPA = '{$chapa}' AND COALESCE(DATAREFERENCIA, DATA) = '{$data}' AND CODCOLIGADA = '{$coligada}' ORDER BY DATA ASC, BATIDA ASC ");
    $batidasDia   = '';

    if ($result) {
      $batidas = $result->getResult();
      foreach ($batidas as $batida) {
        switch ($batida->NATUREZA) {
          case 0:
            $batidasDia .= 'Ent: ' . m2h($batida->BATIDA) . ' | ';
            break;
          case 1:
            $batidasDia .= 'Sai: ' . m2h($batida->BATIDA) . ' | ';
            break;
        }
      }
    }

    return rtrim($batidasDia, ' | ');
  }

  // -----------------------------------------------------------------------------
  // Workflow para verificar aprovações pendentes
  // -----------------------------------------------------------------------------
  public function Workflow()
  {

    $query = "
		WITH PER AS (
		SELECT 
			CODCOLIGADA,
			INICIOMENSAL,
			FIMMENSAL
		FROM
			" . DBRM_BANCO . "..APERIODO
		WHERE
			ATIVO = 1
		),

		PRESDIR AS (
		select 
			distinct c.chapa as chapa
		from zcrmportal_hierarquia a
		left join zcrmportal_hierarquia_grupocargo b on b.id = a.id_grupocargo
		left join zcrmportal_hierarquia_chapa c on c.id_hierarquia = a.id
		where 
			(b.descricao = '01 - Diretor' or b.descricao = '00 - Presidente') and
			a.inativo is null and
			c.chapa is not null
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
		WHERE s.modulos like '%\"6\"%' AND s.dtfim >= GETDATE() AND s.inativo = 0
		),

		EML AS (
		SELECT 
			DISTINCT
			A.CHAPA,
			A.NOME,
			A.CODCOLIGADA,
			C.email EMAIL
		FROM
			" . DBRM_BANCO . "..PFUNC A,
			" . DBRM_BANCO . "..PPESSOA B,
			zcrmportal_usuario C
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
			" . DBRM_BANCO . "..PFUNC A,
			zcrmportal_frente_trabalho B,
			zcrmportal_hierarquia_frentetrabalho C,
			zcrmportal_hierarquia_chapa D,
			" . DBRM_BANCO . "..PFUNC E
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
			INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
			AND B.CODCOLIGADA = A.coligada
			LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
			INNER JOIN " . DBRM_BANCO . "..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
			INNER JOIN PER P ON P.CODCOLIGADA = A.coligada
			LEFT JOIN PAR R ON R.COLIGADA = A.coligada
		WHERE 
      A.status in ('1', '2')
			AND A.motivo_reprova IS NULL
			AND A.usu_delete IS NULL
			AND A.dtponto >= P.INICIOMENSAL
			
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
			INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
			AND B.CODCOLIGADA = A.coligada
			LEFT JOIN zcrmportal_usuario C (NOLOCK) ON C.id = A.usucad
			LEFT JOIN " . DBRM_BANCO . "..AHORARIO D (NOLOCK) ON D.CODIGO = a.codhorario COLLATE Latin1_General_CI_AS
			AND D.CODCOLIGADA = a.coligada
			INNER JOIN " . DBRM_BANCO . "..PPESSOA E (NOLOCK) ON E.CODIGO = B.CODPESSOA
			INNER JOIN PER P ON P.CODCOLIGADA = A.coligada
			LEFT JOIN PAR R ON R.COLIGADA = A.coligada
		WHERE 
      a.situacao in (10)
			AND (
				a.datamudanca >= P.INICIOMENSAL
				OR a.datamudanca_folga >= P.INICIOMENSAL 
			)
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
		LEFT JOIN " . DBRM_BANCO . "..CRM_HIERARQUIA3 H ON H.CODCOLIGADA = G.COLIGADA AND H.CHAPA = G.CHAPA COLLATE Latin1_General_CI_AS
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
		LEFT JOIN " . DBRM_BANCO . "..CRM_HIERARQUIA3 H ON H.CODCOLIGADA = G.COLIGADA AND H.CHAPA = G.GESTOR1_ACIMA COLLATE Latin1_General_CI_AS
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
				WHEN TGE.MOVIMENTO = 1 THEN 'Inclusão de registro'
				WHEN TGE.MOVIMENTO = 2 THEN 'Exclusão de registro'
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
		LEFT JOIN " . DBRM_BANCO . "..CRM_HIERARQUIA3 H ON H.CODCOLIGADA = G.COLIGADA AND H.CHAPA = G.GESTOR COLLATE Latin1_General_CI_AS
		LEFT JOIN EML E ON E.CODCOLIGADA = H.CODCOLIGADA AND E.CHAPA = H.CHAPA_GESTOR_IMEDIATO COLLATE Latin1_General_CI_AS
		WHERE G.EMAIL IS NULL
		),

		FI1 AS (
		SELECT * FROM GEN WHERE EMAIL IS NOT NULL
		UNION ALL
		SELECT * FROM GEM
		),

		FIN AS (
		SELECT 
			F.COLIGADA,
			F.GESTOR,
			F.NOME,
			F.EMAIL,
			F.MOVIMENTO,
			F.DESC_MOVIMENTO,
			MAX( F.TOTAL ) TOTAL
		FROM FI1 F
		GROUP BY F.COLIGADA, F.GESTOR, F.NOME, F.EMAIL, F.MOVIMENTO, F.DESC_MOVIMENTO
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
			S.email EMAIL_SUB,
			FORMAT(P.INICIOMENSAL, 'dd/MM/yyyy') DTINI_BR, 
			FORMAT(P.FIMMENSAL, 'dd/MM/yyyy') DTFIM_BR
		FROM FIN F
		LEFT JOIN SUB S ON S.COLIGADA = F.coligada AND S.chapa_gestor = F.GESTOR COLLATE Latin1_General_CI_AS
		LEFT JOIN PER P ON P.CODCOLIGADA = F.coligada
		WHERE F.GESTOR NOT IN (SELECT CHAPA FROM PRESDIR) 
		ORDER BY F.GESTOR
		";

    //echo '<PRE> '.$query;
    //exit();

    $result = $this->dbportal->query($query);
    $gestor_atu = "";
    $itens = "";
    $gestor =  "";
    $nome =  "";
    $email =  "";
    $desc_movimento =  "";
    $total =  "";
    $nome_sub =  "";
    $email_sub =  "";
    $dtinip_br = "";
    $dtfimp_br = "";

    if ($result->getNumRows() > 0) {
      $resFuncs = $result->getResultArray();
      foreach ($resFuncs as $key => $Func):
        //print_r($Func);
        //exit();
        //die();
        if ($gestor_atu == "") {
          $gestor_atu = $Func['GESTOR'];
        }
        $gestor = $Func['GESTOR'];

        if ($gestor == $gestor_atu) {
          $nome = $Func['NOME'];
          $email = $Func['EMAIL'];
          $desc_movimento = $Func['DESC_MOVIMENTO'];
          $total = $Func['TOTAL'];
          $nome_sub = $Func['NOME_SUB'];
          $email_sub = $Func['EMAIL_SUB'];
          $dtinip_br = $Func['DTINI_BR'];
          $dtfimp_br = $Func['DTFIM_BR'];

          $itens = $itens . '<strong>•	' . $desc_movimento . ':</strong> ' . $total . '<br>';
        } else {
          $assunto = '[Portal RH] Você possui solicitações pendentes de aprovação';
          $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
          $mensagem = '
						Este é um lembrete de que você possui solicitações pendentes de aprovação no <strong>Portal RH - Módulo de Ponto</strong> no período de <strong>' . $dtinip_br . ' a ' . $dtfimp_br . '</strong>, ou posterior. Abaixo está um resumo das pendências: <br><br>
						<strong>Resumo de Pendências:</strong><br><br>
						' . $itens . '<br>
						Solicitamos que acesse o Portal RH para revisar as solicitações pendentes.<br><br>
						Segue abaixo link para acesso ao Portal RH <a href="' . base_url() . '" target="_blank">' . base_url() . '</a><br><br>
						Atenciosamente,<br>
						<strong>Equipe Processos de RH</strong><br>
                    ';

          $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');

          $email = 'deivison.batista@eldoradobrasil.com.br';
          //$email = 'alvaro.zaragoza@ativary.com';
          $response = enviaEmail($email, $assunto, $htmlEmail);
          echo 'Enviado email para ' . $nome . ' - ' . $email . '<br>';

          if (!is_null($email_sub)) {
            $msg_nome_sub = 'Prezado(a) ' . $nome_sub . ',<br><br>';
            $htmlEmail = templateEmail($msg_nome_sub . $mensagem, '95%');
            $email_sub = 'deivison.batista@eldoradobrasil.com.br';
            //$email = 'alvaro.zaragoza@ativary.com';
            $response = enviaEmail($email_sub, $assunto, $htmlEmail);
            echo 'Enviado email para ' . $nome_sub . ' - ' . $email_sub . '<br>';
          }

          $gestor_atu = $Func['GESTOR'];
          $gestor = $Func['GESTOR'];
          $itens = '';

          $nome = $Func['NOME'];
          $email = $Func['EMAIL'];
          $desc_movimento = $Func['DESC_MOVIMENTO'];
          $total = $Func['TOTAL'];
          $nome_sub = $Func['NOME_SUB'];
          $email_sub = $Func['EMAIL_SUB'];
          $dtinip_br = $Func['DTINI_BR'];
          $dtfimp_br = $Func['DTFIM_BR'];

          $itens = $itens . '<strong>•	' . $desc_movimento . ':</strong> ' . $total . '<br>';
          /*$query = "UPDATE zcrmportal_premios_emprestimos SET dt_envio_email = '".date('Y-m-d')."' WHERE id = '{$id_acesso}'";

                    $this->dbportal->query($query);
                    if($this->dbportal->affectedRows() <= 0){
                        echo 'Falha ao atualizar data de envio para '.$nome_para_chapa.'<br>';
                    } else {
                        echo 'Enviado email para '.$nome_para_chapa.' - '.$email_para_chapa.'<br>';
                    }*/
        }
      endforeach;
      if ($nome != '') {
        $assunto = '[Portal RH] Você possui solicitações pendentes de aprovação';
        $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
        $mensagem = '
						Este é um lembrete de que você possui solicitações pendentes de aprovação no <strong>Portal RH - Módulo de Ponto</strong> no período de <strong>' . $dtinip_br . ' a ' . $dtfimp_br . '</strong>, ou posterior. Abaixo está um resumo das pendências: <br><br>
						' . $itens . '<br>
						Pedimos que acesse o Portal RH para revisar e aprovar as solicitações pendentes.<br><br>
						Segue abaixo link para acesso ao Portal RH <a href="' . base_url() . '" target="_blank">' . base_url() . '</a><br><br>
						Atenciosamente,<br>
						<strong>Equipe Processos de RH</strong><br>
                    ';

        $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');

        $email = 'deivison.batista@eldoradobrasil.com.br';
        //$email = 'alvaro.zaragoza@ativary.com';
        $response = enviaEmail($email, $assunto, $htmlEmail);

        if (!is_null($email_sub)) {
          $msg_nome_sub = 'Prezado(a) ' . $nome_sub . ',<br><br>';
          $htmlEmail = templateEmail($msg_nome_sub . $mensagem, '95%');
          $email_sub = 'deivison.batista@eldoradobrasil.com.br';
          //$email_sub = 'alvaro.zaragoza@ativary.com';
          $response = enviaEmail($email_sub, $assunto, $htmlEmail);
        }
      }

      $lista = "
			WITH PER AS (
			SELECT 
				CODCOLIGADA,
				INICIOMENSAL,
				FIMMENSAL
			FROM
				" . DBRM_BANCO . "..APERIODO
			WHERE
				ATIVO = 1
			),

			PAR AS (
			SELECT 
				COLIGADA,
				WFLOW_DIAS_NOTIF		D1,
				WFLOW_DIAS_NOTIF_ACIMA	D2
			FROM zcrmportal_espelho_config
			),

			LIS AS (
			SELECT 
				'H' AS TIPO,
				A.id,
				A.dtponto,
				A.movimento,
				A.chapa,
				A.coligada,
				A.status,
				A.dtcadastro data_solicitacao,
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
				INNER JOIN PER P ON P.CODCOLIGADA = A.coligada
				LEFT JOIN PAR R ON R.COLIGADA = A.coligada
			WHERE A.status in ('1', '2')
				AND A.coligada = '1'
				AND A.motivo_reprova IS NULL
				AND A.usu_delete IS NULL
				AND A.dtponto >= P.INICIOMENSAL

			UNION ALL

			SELECT 
				'E' AS TIPO,
				a.id,
				a.datamudanca dtponto,
				CASE
					WHEN a.tipo = 1 THEN 21
					ELSE 22
				END movimento,
				a.chapa,
				a.coligada,
				a.situacao status,
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
				INNER JOIN PER P ON P.CODCOLIGADA = A.coligada
				LEFT JOIN PAR R ON R.COLIGADA = A.coligada
			WHERE a.coligada = '1'
				and a.situacao in (10)
				AND (
					a.datamudanca >= P.INICIOMENSAL
					OR a.datamudanca_folga >= P.INICIOMENSAL 
				)
			)
			
			";

      // atualiza datas de envio
      $atuH2 = $lista . " UPDATE zcrmportal_ponto_horas SET envio_gestor2 = GETDATE() WHERE id IN ( SELECT id from LIS WHERE TIPO = 'H' AND (envia_para_gestor2 = 'S' OR envio_gestor2 IS NULL) ) ";
      // echo $query;exit();
      $this->dbportal->query($atuH2);

      $atuH1 = $lista . " UPDATE zcrmportal_ponto_horas SET envio_gestor1 = GETDATE() WHERE id IN ( SELECT id from LIS WHERE TIPO = 'H' AND envia_para_gestor1 = 'S' ) ";
      $this->dbportal->query($atuH1);

      $atuE2 = $lista . " UPDATE zcrmportal_escala SET envio_gestor2 = GETDATE() WHERE id IN ( SELECT id from LIS WHERE TIPO = 'E' AND (envia_para_gestor2 = 'S' OR envio_gestor2 IS NULL) ) ";
      $this->dbportal->query($atuE2);

      $atuE1 = $lista . " UPDATE zcrmportal_escala SET envio_gestor1 = GETDATE() WHERE id IN ( SELECT id from LIS WHERE TIPO = 'E' AND envia_para_gestor1 = 'S' ) ";
      $this->dbportal->query($atuE1);

      return true;
    } else {
      echo 'Nada a enviar';
      return false;
    }
  }

  // -----------------------------------------------------------------------------
  // Workflow para verificar aprovações pendentes de Artigo 61
  // -----------------------------------------------------------------------------
  public function Workflow_Art61()
  {

    $query = "
      SELECT 
        COUNT( r.chapa_gestor ) as reqs,
        r.chapa_gestor,
        b.nome nome_gestor,
        u.email email_gestor,
        FORMAT(r.dt_ini_ponto, 'dd/MM/yyyy') dtini_br,
        FORMAT(r.dt_fim_ponto, 'dd/MM/yyyy') dtfim_br
      FROM zcrmportal_art61_requisicao r
        LEFT JOIN email_chapa u (NOLOCK) ON u.chapa = r.chapa_gestor COLLATE Latin1_General_CI_AS
        and u.codcoligada = r.id_coligada
        INNER JOIN " . DBRM_BANCO . "..PFUNC B (NOLOCK) ON B.CHAPA = r.chapa_gestor COLLATE Latin1_General_CI_AS
        AND B.CODCOLIGADA = r.id_coligada
      WHERE 
        r.status = 2 AND
        (r.dt_envio_email IS NULL OR CONVERT(VARCHAR, r.dt_envio_email, 23) < CONVERT(VARCHAR, GETDATE(), 23))
      GROUP BY r.chapa_gestor, b.nome, u.email, r.dt_ini_ponto, r.dt_fim_ponto
		";
    //echo $query;
    //die();
    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      $resFuncs = $result->getResultArray();
      foreach ($resFuncs as $key => $Func):
        $nome = $Func['nome_gestor'];
        $email = $Func['email_gestor'];
        $reqs = $Func['reqs'];  
        $dtini_br = $Func['dtini_br'];
        $dtfim_br = $Func['dtfim_br'];

        if($reqs > 1) {
          $itens = '<strong>•	Artigo 61:</strong> ' . $reqs . ' requisições<br>';
        } else {
          $itens = '<strong>•	Artigo 61:</strong> ' . $reqs . ' requisição<br>';
        }

        $assunto = '[Portal RH] Você possui solicitações pendentes de aprovação';
        $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
        $mensagem = '
						Este é um lembrete de que você possui solicitações pendentes de aprovação no <strong>Portal RH - Módulo de Ponto</strong> no período de <strong>' . $dtini_br . ' a ' . $dtfim_br . '</strong>, ou posterior. Abaixo está um resumo das pendências: <br><br>
						<strong>Resumo de Pendências:</strong><br><br>
						' . $itens . '<br>
						Solicitamos que acesse o Portal RH para revisar as solicitações pendentes.<br><br>
						Segue abaixo link para acesso ao Portal RH <a href="' . base_url() . '" target="_blank">' . base_url() . '</a><br><br>
						Atenciosamente,<br>
						<strong>Equipe Processos de RH</strong><br>
        ';

        $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');

        //$email = 'deivison.batista@eldoradobrasil.com.br';
        $email = 'alvaro.zaragoza@ativary.com';
        $response = enviaEmail($email, $assunto, $htmlEmail);
        echo 'Enviado email para ' . $nome . ' - ' . $email . '<br>';

      endforeach;
      
      // atualiza datas de envio
      $atu = "UPDATE zcrmportal_art61_requisicao SET dt_envio_email = GETDATE() WHERE status = 2";
      $this->dbportal->query($atu);

      return true;

    } else {

      echo 'Nada a enviar';
      return false;
    }
  }

  public function listaCCustoUsuario($codsecao = null, $dados = false)
  {

    //-----------------------------------------
    // filtro das chapas que o lider pode ver
    //-----------------------------------------
    $mHierarquia = Model('HierarquiaModel');
    $objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
    $isLider = $mHierarquia->isLider();

    $filtro_chapa_lider = "";
    $filtro_secao_lider = "";
    if ($isLider) {
      $chapas_lider = "";
      $codsecoes = "";
      foreach ($objFuncLider as $idx => $value) {
        $chapas_lider .= "'" . $objFuncLider[$idx]['chapa'] . "',";
      }
      $filtro_secao_lider = " A.CHAPA IN (" . substr($chapas_lider, 0, -1) . ") OR ";
    }


    //-----------------------------------------
    // filtro das seções que o gestor pode ver
    //-----------------------------------------
    $secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
    $filtro_secao_gestor = "";

    if ($secoes) {
      $codsecoes = "";
      foreach ($secoes as $ids => $Secao) {
        $codsecoes .= "'" . $Secao['codsecao'] . "',";
      }
      $filtro_secao_gestor = " A.CODSECAO IN (" . substr($codsecoes, 0, -1) . ") OR ";
    }
    //-----------------------------------------

    // monta o where das seções
    if ($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
    if ($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    if ($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
    $chapaFunc = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;

    if ($this->log_id  != 1) {
      $qr_secao = " AND (" . $filtro_secao_lider . " " . $filtro_secao_gestor . ") AND A.CHAPA != '{$chapaFunc}' ";
    } else {
      $qr_secao = "";
    }

    // lista seções
    $filtro_secao = ($codsecao != null) ? " AND A.CODSECAO = '{$codsecao}' " : "";

    if ($dados) {
      if (($dados['perfilRH'] ?? false) || ($dados['rh'] ?? false)) $qr_secao = "";
    }
    $query = " 
		WITH LISTA AS (
			SELECT 
				A.CODSECAO CODIGO, 
				B.DESCRICAO,
				C.CODCCUSTO CCUSTO,
				C.NOME DESC_CCUSTO
				
			FROM 
				PFUNC A,
				PSECAO B,
				GCCUSTO C

			WHERE 
				    A.CODCOLIGADA = '{$this->coligada}'
				AND A.CODCOLIGADA = B.CODCOLIGADA
				AND B.SECAODESATIVADA = 0
				AND A.CODSECAO = B.CODIGO
				AND A.CODSITUACAO NOT IN ('D')
				AND B.CODCOLIGADA = C.CODCOLIGADA
				AND B.NROCENCUSTOCONT = C.CODCCUSTO
				{$qr_secao}
        {$filtro_secao}

			GROUP BY
				A.CODSECAO, 
				B.DESCRICAO,
				C.CODCCUSTO,
				C.NOME

    )
    SELECT DISTINCT CCUSTO, DESC_CCUSTO FROM LISTA
		";

    // exit('<pre>'.$query);
    $result = $this->dbrm->query($query);
    if (!$result) return false;
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  // -------------------------------------------------------
  // Calcula a solicitacao do Artigo 61
  // -------------------------------------------------------
  public function Calcular_Req($id_req)
  {

    // Query para calcular e atualizar horas
    $query = "
      with calc as (
        select
          c.id,
          c.id_req,
          c.chapa_colab,
          c.valor,
          f.codfilial,
          c.codevento,
          z.HEXTRA_DIARIA as extra_normal,
          e.para_codevento as codevento_art61,
          (c.valor - z.HEXTRA_DIARIA) as extra_art61

        from zcrmportal_art61_req_chapas c
        left join zcrmportal_art61_requisicao r on r.id = c.id_req
        left join " . DBRM_BANCO . "..PFUNC f on f.CODCOLIGADA = r.id_coligada and f.CHAPA = c.chapa_colab COLLATE Latin1_General_CI_AS 
        left join " . DBRM_BANCO . "..Z_OUTSERV_MELHORIAS3 z on z.CODCOLIGADA = f.CODCOLIGADA and z.CODINDICE = c.indice and z.CODHORARIO = c.codhorario COLLATE Latin1_General_CI_AS
        left join zcrmportal_art61_codevento e on e.coligada = r.id_coligada and e.codfilial = f.CODFILIAL and e.de_codevento = c.codevento and e.ativo = 'S'

        where r.id_coligada = " . $_SESSION['func_coligada'] . "
        and   c.status = 'A'
        and   c.id_req = " . $id_req . "
      )

      UPDATE
          r
      SET
          r.extra_normal = c.extra_normal,
          r.codevento_art61 = c.codevento_art61,
          r.extra_art61 = c.extra_art61
      FROM
          zcrmportal_art61_req_chapas as r
          INNER JOIN calc AS c
              ON c.id = r.id
      WHERE
          r.id_req = " . $id_req . "
    ";

    $this->dbportal->query($query);
    /*if ($this->dbportal->affectedRows() == 0) {
      return responseJson('error', 'Não foi possível calcular. Será necessário processar a requisição novamente. Antes, verifique configurações de eventos do Artigo.61. '.$this->dbportal->affectedRows());
    }*/

    // Query para calcular e atualizar horas
    $query = "
      UPDATE zcrmportal_art61_requisicao
      SET status = 4
      WHERE id = " . $id_req . "
    ";
    $this->dbportal->query($query);
    /*
    if ($this->dbportal->affectedRows() == 0) {
      return responseJson('error', 'Cálculo finalizado, mas não foi possível atualizar status da requisiçao.');
    }*/

    $resp = 'Cálculo finalizado com sucesso.';
    return responseJson('success', $resp);
  }

  //---------------------------------------------
    // Pega parametros do (RM)
    //---------------------------------------------
    public function ListarParam(){

        $where = "CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
        $query = "
            SELECT
                ANOCOMP,
                MESCOMP,
                PERIODO
            FROM
                PPARAM
            WHERE
                {$where}
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -----------------------------------------------------------------------
    // Sincronizar Requisição do Artigo 61
    // ------------------------------------------------------------------------

    public function SincArt61RM($dados){
        
        $mescomp = $dados['mescomp'];
        $anocomp = $dados['anocomp'];
        $nroperiodo = $dados['nroperiodo'];
        $coligada = $_SESSION['func_coligada'];
        $id_usuario = $_SESSION['log_id'];

        $ids = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        if(substr($ids, -1)==",") { $ids = substr($ids, 0, -1); }
        
        $query = "
            UPDATE zcrmportal_art61_requisicao
            SET 
              status = 6,
              dt_sincronismo = '".date('Y-m-d')."', 
              periodo_sincronismo = '".$nroperiodo."',
              user_id_sincronismo = ".$id_usuario."
            WHERE status = 5 AND id IN ({$ids})";
        $this->dbportal->query($query);
        
        // Atualiza status da requisição
        $query = "
          UPDATE zcrmportal_art61_req_chapas
          SET 
            anocomp = ".$anocomp.",
            mescomp = ".$mescomp.",
            nroperiodo = ".$nroperiodo." 
          WHERE id_req IN ({$ids})";

        $this->dbportal->query($query);

        if (str_contains($ids, ',')) {
           $msg = 'Requisições sincronizadas com o RM Folha';
        } else {
           $msg = 'Requisição sincronizada com o RM Folha';
        }
        
        return ($this->dbportal->affectedRows() > 0) 
            ? responseJson('success', $msg)
            : responseJson('error', 'Falha ao sincronizar requisição');

    }

    // -----------------------------------------------------------------------
    // Cancelar Sincronizar Requisição do Artigo 61
    // ------------------------------------------------------------------------

    public function CancSincArt61RM($dados){
        
        $coligada = $_SESSION['func_coligada'];
        $id_usuario = $_SESSION['log_id'];

        $ids = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        if(substr($ids, -1)==",") { $ids = substr($ids, 0, -1); }
        
        $query = "
            UPDATE zcrmportal_art61_requisicao
            SET 
              status = 5,
              dt_sincronismo = null, 
              periodo_sincronismo = null,
              user_id_sincronismo = ".$id_usuario."
            WHERE status = 6 AND id IN ({$ids})";
        $this->dbportal->query($query);
        
        // Atualiza status da requisição
        $query = "
          UPDATE zcrmportal_art61_req_chapas
          SET 
            anocomp = null,
            mescomp = null,
            nroperiodo = null 
          WHERE id_req IN ({$ids})";

        $this->dbportal->query($query);

        if (str_contains($ids, ',')) {
           $msg = 'Sincronismos cancelados. Os envelopes devem ser recalculados no RM Folha.';
        } else {
           $msg = 'Sincronismo cancelado. Os envelopes devem ser recalculados no RM Folha.';
        }
        
        return ($this->dbportal->affectedRows() > 0) 
            ? responseJson('success', $msg)
            : responseJson('error', 'Falha ao cancelar sincronismo.');

    }


  // -----------------------------------------------------------------------------
  // Workflow de envio de emails para gestores aprovarem envio de 
  // aviso para colaboradores com mais de 10 faltas consecutivas
  // -----------------------------------------------------------------------------

  public function Workflow_Faltas()
  {
   
    $data = date('Y-m-d');

    // DATA USADA PARA TESTES EM HOMOLOGAÇÃO
    // REMOVER EM PRODUÇÃO
    $data = '2025-05-15';

    $query = "
      DECLARE @DATA_FIM DATE = '".$data."';
      DECLARE @DATA_INI DATE = DATEADD(DAY, -20, @DATA_FIM);

      WITH EVE_FALTAS AS (
        SELECT CODIGO FROM PEVENTO WHERE CODIGOCALCULO = 8 AND VALHORDIAREF = 'D' 
      ),

      PRESDIR AS (
        SELECT 
          DISTINCT C.COLIGADA, C.CHAPA 
        FROM " . DBPORTAL_BANCO . "..ZCRMPORTAL_HIERARQUIA A
        LEFT JOIN " . DBPORTAL_BANCO . "..ZCRMPORTAL_HIERARQUIA_GRUPOCARGO B ON B.ID = A.ID_GRUPOCARGO
        LEFT JOIN " . DBPORTAL_BANCO . "..ZCRMPORTAL_HIERARQUIA_CHAPA C ON C.ID_HIERARQUIA = A.ID
        WHERE 
          (B.DESCRICAO = '01 - DIRETOR' OR B.DESCRICAO = '00 - PRESIDENTE') AND
          A.INATIVO IS NULL AND
          C.CHAPA IS NOT NULL
      ),

      SUB AS (
        SELECT 
          S.COLIGADA,
          S.CHAPA_GESTOR,
          S.CHAPA_SUBSTITUTO,
          U.NOME,
          U.EMAIL
        FROM " . DBPORTAL_BANCO . "..ZCRMPORTAL_HIERARQUIA_GESTOR_SUBSTITUTO S
        INNER JOIN " . DBPORTAL_BANCO . "..ZCRMPORTAL_USUARIO U ON U.ID = S.ID_SUBSTITUTO
        WHERE 
          --S.MODULOS LIKE '%\"6\"%' AND 
          S.DTFIM >= GETDATE() AND S.INATIVO = 0
      ),

      EML AS (
        SELECT 
          DISTINCT
          A.CHAPA,
          A.NOME,
          A.CODCOLIGADA,
          C.EMAIL EMAIL
        FROM
          PFUNC A,
          PPESSOA B,
          " . DBPORTAL_BANCO . "..ZCRMPORTAL_USUARIO C
        WHERE
          A.CODPESSOA = B.CODIGO
          AND C.LOGIN = B.CPF COLLATE LATIN1_GENERAL_CI_AS
          AND A.CODSITUACAO <> 'D'
      ),

      PAR AS (
      SELECT 
        CODCOLIGADA,
        DIAS_FALTAS,
        DIAS_DE_ESPERA,
        DIAS_PARA_ESCALAR
      FROM " . DBPORTAL_BANCO . "..ZCRMPORTAL_WORKFLOW_FALTAS_CONFIG
      ),

      LISTA AS (
        SELECT A.CODCOLIGADA, A.CHAPA, COUNT(CONVERT(DATE,A.DATA)) AS FALTAS, MIN(CONVERT(DATE,A.DATA)) AS PRIM_FALTA, MAX(CONVERT(DATE,A.DATA)) AS ULT_FALTA 
        FROM AMOVFUNDIA A
        LEFT JOIN PAR P ON P.CODCOLIGADA = A.CODCOLIGADA
        WHERE 
          A.CODEVE IN (SELECT CODIGO FROM EVE_FALTAS)
        AND A.DATA >= @DATA_INI AND A.DATA <= @DATA_FIM 

        GROUP BY A.CODCOLIGADA, A.CHAPA, P.DIAS_FALTAS
        HAVING COUNT(A.DATA) >= ISNULL(P.DIAS_FALTAS,10)
      ),

      EVE_OUTROS AS (
        SELECT CODCOLIGADA, CHAPA, MIN(CONVERT(DATE,DATA)) AS PRIM_NAO_FALTA, MAX(CONVERT(DATE,DATA)) AS ULT_NAO_FALTA FROM AMOVFUNDIA
        WHERE 
          CODEVE NOT IN (SELECT CODIGO FROM EVE_FALTAS)
        AND DATA >= @DATA_INI AND DATA <= @DATA_FIM

        GROUP BY CODCOLIGADA, CHAPA
      ),

      LISTA_UNI AS (
        SELECT L.*, O.PRIM_NAO_FALTA, O.ULT_NAO_FALTA
        FROM LISTA L
        LEFT JOIN EVE_OUTROS O ON O.CODCOLIGADA = L.CODCOLIGADA AND O.CHAPA = L.CHAPA 
      ),

      TEM_OUTROS_EVE AS (
        SELECT A.CODCOLIGADA, A.CHAPA, COUNT(CONVERT(DATE,A.DATA)) AS FALTAS 
        FROM AMOVFUNDIA A
        INNER JOIN LISTA_UNI L ON L.CODCOLIGADA = A.CODCOLIGADA AND L.CHAPA = A.CHAPA AND L.ULT_NAO_FALTA IS NOT NULL
        WHERE 
          CODEVE IN (SELECT CODIGO FROM EVE_FALTAS)
        AND DATA >= L.ULT_NAO_FALTA AND DATA <= @DATA_FIM 

        GROUP BY A.CODCOLIGADA, A.CHAPA
      ),

      LISTA_CALC AS (
      SELECT 
        L.*,
        F.CODSITUACAO,
        O.FALTAS  FALTAS_AJUSTADAS,
        CASE 
          WHEN L.ULT_NAO_FALTA > L.ULT_FALTA THEN 0
          ELSE ISNULL(O.FALTAS, L.FALTAS) 
        END FALTAS_FINAIS
      FROM LISTA_UNI L
      LEFT JOIN PFUNC F ON F.CODCOLIGADA = L.CODCOLIGADA AND F.CHAPA = L.CHAPA
      LEFT JOIN TEM_OUTROS_EVE O ON O.CODCOLIGADA = L.CODCOLIGADA AND O.CHAPA = L.CHAPA
      WHERE F.CODSITUACAO IN ('A')
      ),

      LISTA_G1 AS (
      SELECT 
        L.CODCOLIGADA,
        L.CHAPA,
        L.PRIM_FALTA,
        L.FALTAS_FINAIS,
        G.CHAPA_GESTOR_IMEDIATO,
        CASE 
          WHEN P.CHAPA IS NOT NULL THEN 'S'
          ELSE 'N'
        END PRESDIR1
      FROM LISTA_CALC L
      LEFT JOIN PAR R ON R.CODCOLIGADA = L.CODCOLIGADA
      LEFT JOIN CRM_HIERARQUIA3 G ON G.CODCOLIGADA = L.CODCOLIGADA AND G.CHAPA = L.CHAPA
      LEFT JOIN PRESDIR P ON P.COLIGADA = L.CODCOLIGADA AND P.CHAPA = G.CHAPA_GESTOR_IMEDIATO COLLATE LATIN1_GENERAL_CI_AS
      WHERE L.FALTAS_FINAIS >= ISNULL(R.DIAS_FALTAS, 10)
      ),

      LISTA_G2 AS (
      SELECT 
        L.CODCOLIGADA,
        L.CHAPA,
        L.PRIM_FALTA,
        L.FALTAS_FINAIS,
        L.CHAPA_GESTOR_IMEDIATO AS CHAPA_GESTOR1,
        L.PRESDIR1,
        G.CHAPA_GESTOR_IMEDIATO AS CHAPA_GESTOR2,
        CASE 
          WHEN P.CHAPA IS NOT NULL THEN 'S'
          ELSE 'N'
        END PRESDIR2
      FROM LISTA_G1 L
      LEFT JOIN CRM_HIERARQUIA3 G ON G.CODCOLIGADA = L.CODCOLIGADA AND G.CHAPA = L.CHAPA_GESTOR_IMEDIATO COLLATE LATIN1_GENERAL_CI_AS
      LEFT JOIN PRESDIR P ON P.COLIGADA = L.CODCOLIGADA AND P.CHAPA = G.CHAPA_GESTOR_IMEDIATO COLLATE LATIN1_GENERAL_CI_AS
      )

      SELECT 
        L.CODCOLIGADA,
        L.CHAPA				AS CHAPA_COLAB,
        F0.NOME				AS NOME_COLAB,
	      U.NOME				AS FUNCAO_COLAB,
        L.PRIM_FALTA,
        L.FALTAS_FINAIS,
        L.CHAPA_GESTOR1,
        E1.NOME				AS NOME_GESTOR1,
	      E1.EMAIL			AS EMAIL_GESTOR1,
        L.PRESDIR1,
        S1.CHAPA_SUBSTITUTO AS GESTOR_SUB1,
        S1.NOME				AS NOME_SUB1,
        S1.EMAIL			AS EMAIL_SUB1,
        L.CHAPA_GESTOR2,
        E2.NOME				AS NOME_GESTOR2,
	      E2.EMAIL			AS EMAIL_GESTOR2,
        L.PRESDIR2,
        S2.CHAPA_SUBSTITUTO AS GESTOR_SUB2,
        S2.NOME				AS NOME_SUB2,
        S2.EMAIL			AS EMAIL_SUB2
        
      FROM LISTA_G2 L
      LEFT JOIN SUB S1 ON S1.COLIGADA = L.CODCOLIGADA AND S1.CHAPA_GESTOR = L.CHAPA_GESTOR1 COLLATE LATIN1_GENERAL_CI_AS
      LEFT JOIN SUB S2 ON S2.COLIGADA = L.CODCOLIGADA AND S2.CHAPA_GESTOR = L.CHAPA_GESTOR2 COLLATE LATIN1_GENERAL_CI_AS
      LEFT JOIN EML E1 ON E1.CODCOLIGADA = L.CODCOLIGADA AND E1.CHAPA = L.CHAPA_GESTOR1 COLLATE LATIN1_GENERAL_CI_AS
      LEFT JOIN EML E2 ON E2.CODCOLIGADA = L.CODCOLIGADA AND E2.CHAPA = L.CHAPA_GESTOR2 COLLATE LATIN1_GENERAL_CI_AS
      LEFT JOIN PFUNC F0 ON F0.CODCOLIGADA = L.CODCOLIGADA AND F0.CHAPA = L.CHAPA COLLATE LATIN1_GENERAL_CI_AS
      LEFT JOIN PFUNCAO U ON U.CODCOLIGADA = F0.CODCOLIGADA AND U.CODIGO = F0.CODFUNCAO

		";
    //echo $query;
    //die();
    $result = $this->dbrm->query($query);
    if ($result->getNumRows() > 0) {
      $resFuncs = $result->getResultArray();
      foreach ($resFuncs as $key => $Func):
        $codcoligada = $Func['CODCOLIGADA'];
        $chapa_colab = $Func['CHAPA_COLAB'];
        $nome_colab = $Func['NOME_COLAB'];
        $funcao_colab = $Func['FUNCAO_COLAB'];
        $prim_falta = $Func['PRIM_FALTA'];
        $faltas_finais = $Func['FALTAS_FINAIS'];
        $chapa_gestor1 = $Func['CHAPA_GESTOR1'];
        $nome_gestor1 = $Func['NOME_GESTOR1'];
        $email_gestor1 = $Func['EMAIL_GESTOR1'];
        $presdir1 = $Func['PRESDIR1'];
        $gestor_sub1 = $Func['GESTOR_SUB1'];  
        $nome_sub1 = $Func['NOME_SUB1'];  
        $email_sub1 = $Func['EMAIL_SUB1'];  
        $chapa_gestor2 = $Func['CHAPA_GESTOR2'];
        $nome_gestor2 = $Func['NOME_GESTOR2'];
        $email_gestor2 = $Func['EMAIL_GESTOR2'];
        $presdir2 = $Func['PRESDIR2'];
        $gestor_sub2 = $Func['GESTOR_SUB2'];  
        $nome_sub2 = $Func['NOME_SUB2'];  
        $email_sub2 = $Func['EMAIL_SUB2'];

        $enviar = 0; // 0: Não envia email, 1: Envia para gestor 1 e 2: Envia para gestor 2

        $reg_config = $this->reg_faltas_config($codcoligada);  
        $reg = $this->reg_workflow_faltas($codcoligada, $chapa_colab);      

        if(!$reg) {
          // insere registro
          $query = " 
            INSERT INTO zcrmportal_workflow_faltas
              (codcoligada, chapa_colab, nome_colab, prim_falta , faltas_finais, chapa_gestor1, nome_gestor1, email_gestor1, presdir1, gestor_sub1, nome_sub1, email_sub1, chapa_gestor2, nome_gestor2, email_gestor2, presdir2, gestor_sub2, nome_sub2, email_sub2, status, dtenvio1, dtcad, usucad, dtalt, usualt) 
            VALUES 
              (" . $codcoligada . ", '" . $chapa_colab . "', '" . $nome_colab . "', '" . $prim_falta . "', " . $faltas_finais . ", '" . $chapa_gestor1. "', '" . $nome_gestor1. "', '" . $email_gestor1. "', '" . $presdir1. "', '" . $gestor_sub1. "', '" . $nome_sub1. "', '" . $email_sub1. "', '" . $chapa_gestor2. "', '" . $nome_gestor2. "', '" . $email_gestor2. "', '" . $presdir2. "', '" . $gestor_sub2. "', '" . $nome_sub2. "', '" . $email_sub2. "', 'ENVIADO GESTOR 1', '" . $data. "', '" . $data. "', " . $_SESSION['log_id']. ", '" . $data. "', " . $_SESSION['log_id'] . ") ";

          $this->dbportal->query($query);

          if ($this->dbportal->affectedRows() > 0) {
            $id_reg = $this->dbportal->insertId();
            echo 'Registro de abandono de emprego criado com sucesso para coligada-chapa: '.$codcoligada.'-'.$chapa_colab;
          } else {
            echo 'Falha ao criar registro de abandono de emprego para chapa: '.$codcoligada.'-'.$chapa_colab;
          }
          $enviar = 1;

        } else {
          $id_reg = $reg->id;
          $data_atu = DateTime::createFromFormat('Y-m-d', $data);
          $data_env1 = DateTime::createFromFormat('Y-m-d', $reg->dtenvio1 ?? $data);
          $data_recu = DateTime::createFromFormat('Y-m-d', $reg->dtrecusado ?? $data);
          
          if(substr($reg->status, -16) == 'ENVIADO GESTOR 1') {
            if(date_diff($data_env1, $data_atu)->d >= $reg_config->dias_para_escalar) {
              $enviar = 2;
            } else {
              if(date_diff($data_env1, $data_atu)->d >= 1) {
                $enviar = 1;
              }
            }

          } elseif($reg->status == 'ENVIADO GESTOR 2') {
            // nenhuma ação
            $enviar = 4;  

          } elseif($reg->status == 'RECUSADO') {
            if(date_diff($data_recu, $data_atu)->d >= $reg_config->dias_de_espera) {
              $enviar = 3;
            }  
          }

          $query = " 
            UPDATE zcrmportal_workflow_faltas
              set prim_falta = '" . $prim_falta . "', 
              faltas_finais = " . $faltas_finais . ", 
              chapa_gestor1 = '" . $chapa_gestor1. "', 
              nome_gestor1 = '" . $nome_gestor1. "', 
              email_gestor1 = '" . $email_gestor1. "', 
              presdir1 = '" . $presdir1. "', 
              gestor_sub1 = '" . $gestor_sub1. "', 
              nome_sub1 = '" . $nome_sub1. "', 
              email_sub1 = '" . $email_sub1. "', 
              chapa_gestor2 = '" . $chapa_gestor2. "', 
              nome_gestor2 = '" . $nome_gestor2. "', 
              email_gestor2 = '" . $email_gestor2. "', 
              presdir2 = '" . $presdir2. "', 
              gestor_sub2 = '" . $gestor_sub2. "', 
              nome_sub2 = '" . $nome_sub2. "', 
              email_sub2 = '" . $email_sub2. "',";

          if($enviar == 2) {
            $query .= " 
              status = 'ENVIADO GESTOR 2',
              dtenvio2 = '" . $data. "', ";
          } elseif($enviar == 3) {
            $query .= "
              status = 'REENVIADO GESTOR 1',
              dtenvio1 = '" . $data. "', ";
          }

          $query .= " 
              dtalt = '" . $data. "', 
              usualt = " . $_SESSION['log_id']. "
            WHERE 
              id = " . $id_reg;

          //echo $query;
          //exit();
          $this->dbportal->query($query);

          if ($this->dbportal->affectedRows() > 0) {
            echo 'Registro de abandono de emprego atualizado com sucesso para coligada-chapa: '.$codcoligada.'-'.$chapa_colab.'<br>';
          } else {
            echo 'Falha ao atualizar registro de abandono de emprego para chapa: '.$codcoligada.'-'.$chapa_colab.'<br>';
          }

        }
  
        // Apenas para complementar a URL
        $random = rtrim(strtr(base64_encode(random_bytes(12)), '+/', '-_'), '=');

        if($enviar > 0) {
          $assunto = '[Portal RH] Faltas Consecutivas';
          $mensagem = '
						O(a) colaborador(a) <strong>' . $nome_colab . '</strong>, de chapa <strong>' . $chapa_colab . '</strong> e função <strong>' . $funcao_colab . '</strong>, está com ' . $faltas_finais . ' faltas consecutivas desde ' . date( 'd/m/Y' , strtotime( $prim_falta ) ) . '.

						Solicitamos que acesse o Portal RH para confirmar o envio do 1° Telegrama, ou justificar o motivo de aguardar mais alguns dias.<br><br>
						Segue abaixo link para acesso ao Portal RH <a href="' . base_url() . '/ponto/aprova/telegrama/' . $id_reg . '" target="_blank">' . base_url() .'/ponto/aprova/telegrama/' . $id_reg . '/' . $random . '</a><br><br>
						Atenciosamente,<br>
						<strong>Equipe Processos de RH</strong><br>
          ';

          if($enviar > 0 and $presdir1 == 'N') {
            $nome = $nome_gestor1;
            $email = $email_gestor1;
            $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
            $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');
            //$email = 'deivison.batista@eldoradobrasil.com.br';
            $email = 'alvaro.zaragoza@ativary.com';
            $response = enviaEmail($email, $assunto, $htmlEmail);
            echo 'Enviado email para ' . $nome . ' - ' . $email . '<br>';
          }

          if($enviar > 0 and $email_sub1 <> '') {
            $nome = $nome_sub1;
            $email = $email_sub1;
            $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
            $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');
            //$email = 'deivison.batista@eldoradobrasil.com.br';
            $email = 'alvaro.zaragoza@ativary.com';
            $response = enviaEmail($email, $assunto, $htmlEmail);
            echo 'Enviado email para ' . $nome . ' - ' . $email . '<br>';
          }

          if(($enviar == 2 or $enviar == 4) and $presdir2 == 'N') {
            $nome = $nome_gestor2;
            $email = $email_gestor2;
            $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
            $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');
            //$email = 'deivison.batista@eldoradobrasil.com.br';
            $email = 'alvaro.zaragoza@ativary.com';
            $response = enviaEmail($email, $assunto, $htmlEmail);
            echo 'Enviado email para ' . $nome . ' - ' . $email . '<br>';
          }

          if(($enviar == 2 or $enviar == 4) and $email_sub2 <> '') {
            $nome = $nome_sub2;
            $email = $email_sub2;
            $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
            $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');
            //$email = 'deivison.batista@eldoradobrasil.com.br';
            $email = 'alvaro.zaragoza@ativary.com';
            $response = enviaEmail($email, $assunto, $htmlEmail);
            echo 'Enviado email para ' . $nome . ' - ' . $email . '<br>';
          }
        }

      endforeach;
      
      // atualiza datas de envio
      //$atu = "UPDATE zcrmportal_art61_requisicao SET dt_envio_email = GETDATE() WHERE status = 2";
      //$this->dbportal->query($atu);

      return true;

    } else {

      echo 'Nada a enviar';
      return false;
    }
  }

  // -------------------------------------------------------
  // Retorna o registro no workflow de faltas
  // -------------------------------------------------------
  public function reg_workflow_faltas($codcoligada, $chapa, $id = 0)
  {
    if($id == 0) {
      $where = "
      where codcoligada = ".$codcoligada." 
        and chapa_colab = '".$chapa."'
        and status <> 'FINALIZADO'
      ";
    } else {
      $where = "
      where id = ".$id;
    }

    $query = "
      select * 
      from zcrmportal_workflow_faltas 
      ".$where;

    $result = $this->dbportal->query($query);
    $row = $result->getRow();
    if (isset($row)) {
      return $row;
    } else {
      return false;
    }
  }

  // -------------------------------------------------------
  // Retorna o registro de parametros do workflow de faltas
  // -------------------------------------------------------
  public function reg_faltas_config($codcoligada)
  {
    $query = "
      select * 
      from zcrmportal_workflow_faltas_config
      where codcoligada = ".$codcoligada." 
    ";
    $result = $this->dbportal->query($query);
    $row = $result->getRow();
    if (isset($row)) {
      return $row;
    } else {
      return false;
    }
  }

  //---------------------------------------------
    // Pega parametros do (RM)
    //---------------------------------------------
    public function ListarFaltas($id){

        $query = "
          SELECT 
            w.codcoligada,
            w.chapa_colab,
            w.nome_colab,
            w.prim_falta,
            w.faltas_finais, 
            w.status, 
            u.NOME as funcao_colab
          FROM zcrmportal_workflow_faltas w
          LEFT JOIN " . DBRM_BANCO . "..PFUNC f ON f.CODCOLIGADA = w.codcoligada and f.CHAPA = w.chapa_colab COLLATE Latin1_General_CI_AS 
          LEFT JOIN " . DBRM_BANCO . "..PFUNCAO u ON u.CODCOLIGADA = f.CODCOLIGADA and u.CODIGO = f.CODFUNCAO
          WHERE w.id = ".$id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

  // -----------------------------------------------------------------------------
  // Finaliza registro de faltas e envia email para RH MASTER
  // -----------------------------------------------------------------------------

  public function EnviaTelegrama($id)
  {
    $data = date('Y-m-d');
    $reg_config = $this->reg_faltas_config($_SESSION['func_coligada']);  
    $reg = $this->reg_workflow_faltas(0,'0',$id);      
    $chapa_aprovou = util_chapa(session()->get('func_chapa'))['CHAPA'];
    $nome_aprovou = $_SESSION['log_nome'];

    $query = " 
        UPDATE zcrmportal_workflow_faltas
        SET status = 'FINALIZADO',
            dtaprovado = '" . $data. "', 
            chapa_aprovou = '" . $chapa_aprovou. "', 
            dtalt = '" . $data. "', 
            usualt = " . $_SESSION['log_id']. "
        WHERE 
          id = " . $id;
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
    } else {
      return responseJson('error', 'Não foi possivel confirmar o envio do telegrama');
    }

    $assunto = '[Portal RH] Confirmação para envio de Telegrama';
    $mensagem = '
      Confirmo o envio de telegrama de abandono de emprego para o colaborador:<br>
      Nome: <strong>' . $reg->nome_colab . '</strong><br>
      Chapa: <strong>' . $reg->chapa_colab . '</strong><br>
      Que esta com <strong>' . $reg->faltas_finais . '</strong> faltas consecutivas desde <strong>' . date( 'd/m/Y' , strtotime( $reg->prim_falta ) ) . '</strong>.<br><br>
      Atenciosamente,<br>
      <strong>' . $nome_aprovou . ' - ' . $chapa_aprovou . ' </strong><br>
      ' . date( 'd/m/Y' ) . ' <br>
    ';

    $email = $reg_config->email_rh;
    $msg_nome = 'Prezado(a) RH Master,<br><br>';
    $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');
    $response = enviaEmail($email, $assunto, $htmlEmail);
    
    return responseJson('success', 'Envio do telegrama confirmado.');
  }

  // -----------------------------------------------------------------------------
  // Recusa o envio do Telegrama
  // -----------------------------------------------------------------------------

  public function RecusaTelegrama($id, $motivo)
  {
    $data = date('Y-m-d');
    $reg_config = $this->reg_faltas_config($_SESSION['func_coligada']);  
    $reg = $this->reg_workflow_faltas(0,'0',$id);      
    $chapa_reprovou = util_chapa(session()->get('func_chapa'))['CHAPA'];
    $nome_reprovou = $_SESSION['log_nome'];

    $query = " 
        UPDATE zcrmportal_workflow_faltas
        SET status = 'RECUSADO',
            dtrecusado = '" . $data. "', 
            chapa_recusou = '" . $chapa_reprovou. "', 
            motivo_recusa = '" . $motivo. "', 
            dtalt = '" . $data. "', 
            usualt = " . $_SESSION['log_id']. "
        WHERE 
          id = " . $id;
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
    } else {
      return responseJson('error', 'Não foi possivel recusar o envio do telegrama');
    }

    $assunto = '[Portal RH] Recusado o envio de Telegrama';
    $mensagem = '
      O envio de telegrama de abandono de emprego para o colaborador:<br>
      Nome: <strong>' . $reg->nome_colab . '</strong><br>
      Chapa: <strong>' . $reg->chapa_colab . '</strong><br>
      Que esta com <strong>' . $reg->faltas_finais . '</strong> faltas consecutivas desde <strong>' . date( 'd/m/Y' , strtotime( $reg->prim_falta ) ) . '</strong>.<br><br>
      <strong>Foi RECUSADO</strong> pelo motivo: <strong>' . $motivo . '</strong>.<br><br>
      Atenciosamente,<br>
      <strong>' . $nome_reprovou . ' - ' . $chapa_reprovou . ' </strong><br>
      ' . date( 'd/m/Y' ) . ' <br>
    ';

    $email = $reg_config->email_rh;
    $msg_nome = 'Prezado(a) RH Master,<br><br>';
    $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');
    $response = enviaEmail($email, $assunto, $htmlEmail);
    
    return responseJson('success', 'Envio do telegrama recusado.');
  }

}
