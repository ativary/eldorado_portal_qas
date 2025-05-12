<?php

namespace App\Models\Ponto;

use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class Art61Model extends Model
{

  protected $dbportal;
  protected $dbrm;
  private $log_id;
  private $now;
  private $coligada;

  public function __construct()
  {
    $this->dbportal = db_connect('dbportal');
    $this->dbrm     = db_connect('dbrm');
    $this->log_id   = session()->get('log_id');
    $this->coligada = session()->get('func_coligada');
    $this->now      = date('Y-m-d H:i:s');

    if (DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
    if (DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
  }

  // -------------------------------------------------------
  // Lista configurações do Art61
  // -------------------------------------------------------
  public function ListarConfigArt61()
  {

    $queryConfig = " 
            SELECT
                dtini_req,
                dtfim_req,
                dtini_ponto,
                dtfim_ponto,
                codevento,
                FORMAT(dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_art61_config WHERE coligada = '" . session()->get('func_coligada') . "' 
        ";

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      $query = " INSERT INTO zcrmportal_art61_config (coligada) VALUES ('" . session()->get('func_coligada') . "') ";
      $result = $this->dbportal->query($query);
      $query = $queryConfig;
      $result = $this->dbportal->query($query);
      return $result->getResultArray();
    }
  }

  // -------------------------------------------------------
  // Salva configurações do Art.61
  // -------------------------------------------------------
  public function SalvarConfig($dados)
  {

    $dtini_req = strlen(trim($dados['dtini_req'])) > 0 ? "'{$dados['dtini_req']}'" : "NULL";
    $dtfim_req = strlen(trim($dados['dtfim_req'])) > 0 ? "'{$dados['dtfim_req']}'" : "NULL";
    $codevento = strlen(trim($dados['codevento'])) > 0 ? "'{$dados['codevento']}'" : "NULL";
    $dtini_ponto = "NULL";
    $dtfim_ponto = "NULL";

    if (strlen(trim($dados['dtper_ponto'])) > 0) {
      $dtini_ponto = substr(trim($dados['dtper_ponto']), 0, 10);
      $dtfim_ponto = substr(trim($dados['dtper_ponto']), 11, 10);
    }

    $query = "
                UPDATE
                    zcrmportal_art61_config
                SET
                    dtini_req = {$dtini_req},
                    dtfim_req = {$dtfim_req},
                    codevento = {$codevento},
                    dtini_ponto = '{$dtini_ponto}',
                    dtfim_ponto = '{$dtfim_ponto}',
                    dtalt = '" . date('Y-m-d H:i:s') . "',
                    usualt = '" . session()->get('log_id') . "'
                WHERE
                    coligada = '" . session()->get('func_coligada') . "'
            ";
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {

      notificacao('success', 'Artigo.61 configurado com sucesso');
      return responseJson('success', 'Artigo.61 configurado com sucesso.');
    } else {
      return responseJson('error', 'Falha ao realizar a configuração do Artigo.61.');
    }
  }

  // -------------------------------------------------------
  // Lista Periodo do Ponto RM (12 ultimos)
  // -------------------------------------------------------

  public function ListarPeriodoPonto()
  {
    $result = $this->dbrm->query(" 
            SELECT TOP 12 
                INICIOMENSAL, 
                FIMMENSAL,
                FORMAT(INICIOMENSAL, 'yyyy-MM-dd')+'-'+FORMAT(FIMMENSAL , 'yyyy-MM-dd') AS PERIODO_SQL,
                FORMAT(INICIOMENSAL, 'dd/MM/yyyy')+' a '+FORMAT(FIMMENSAL, 'dd/MM/yyyy') AS PERIODO_BR,
                ATIVO
            FROM APERIODO
            WHERE CODCOLIGADA = " . $this->coligada . " 
            ORDER BY INICIOMENSAL DESC 
        ");
    if (!$result) return false;
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  //---------------------------------------------
  // Listar Centro de Custo
  //---------------------------------------------
  public function ListarCentroCusto()
  {

    $where = " AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
    $query = "
            SELECT
                CODCCUSTO,
                NOME
            FROM
                GCCUSTO
            WHERE
                ATIVO = 'T'
                {$where}
            ORDER BY
                NOME ASC
        ";
    $result = $this->dbrm->query($query);
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  //---------------------------------------------
  // Lista Funcionarios
  //---------------------------------------------
  public function ListarColab()
  {

    $where = " AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
    $query = "
            SELECT
                CHAPA,
                NOME
            FROM
                PFUNC
            WHERE
                CODSITUACAO <> 'D'
                {$where}
            ORDER BY
                NOME ASC
        ";
    $result = $this->dbrm->query($query);
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  //---------------------------------------------
  // Listar Filiais
  //---------------------------------------------
  public function ListarFilial()
  {

    $where = " AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
    $query = "
            SELECT
                CODFILIAL,
                NOMEFANTASIA
            FROM
                GFILIAL
            WHERE
                ATIVO = 1 
                {$where}
            ORDER BY
                CODFILIAL ASC
        ";
    $result = $this->dbrm->query($query);
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  //---------------------------------------------
  // Listar Eventos
  //---------------------------------------------
  public function ListarEvento()
  {

    $where = " AND CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' ";
    $query = "
            SELECT
                CODIGO,
                DESCRICAO
            FROM
                PEVENTO
            WHERE
                ( inativo <> 1 or inativo is null ) 
                {$where}
            ORDER BY
                DESCRICAO ASC
        ";
    $result = $this->dbrm->query($query);
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  // -------------------------------------------------------
  // Lista Diretoria/Area p/ C.Custo 
  // -------------------------------------------------------
  public function ListarCCustoArea()
  {

    $queryConfig = " 
            SELECT
                a.id,
                a.coligada,
                a.codcusto,
                c.nome nome_ccusto,
                a.diretoria,
                a.area
            FROM zcrmportal_art61_areas a
            LEFT JOIN " . DBRM_BANCO . "..GCCUSTO c ON 
                c.CODCOLIGADA = '" . $_SESSION['func_coligada'] . "' AND 
                c.CODCCUSTO = a.codcusto COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '" . $_SESSION['func_coligada'] . "' AND
                a.ativo = 'S'
        ";

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      return false;
    }
  }

  // -------------------------------------------------------
  // Lista Prorrogações
  // -------------------------------------------------------
  public function ListarProrroga()
  {

    $queryConfig = " 
            SELECT
                a.id,
                a.coligada,
                a.chapa,
                f.nome,
                a.dt_extendida,
                FORMAT(a.dt_extendida, 'dd/MM/yyyy') AS dt_extendida_br
            FROM zcrmportal_art61_prorroga a
            LEFT JOIN " . DBRM_BANCO . "..PFUNC f ON 
                f.codcoligada = '" . $_SESSION['func_coligada'] . "' AND 
                f.chapa = a.chapa COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '" . $_SESSION['func_coligada'] . "' AND
                a.ativo = 'S'
        ";

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      return false;
    }
  }

  // -------------------------------------------------------
  // Lista Exceções
  // -------------------------------------------------------
  public function ListarExcecao()
  {

    $queryConfig = " 
            SELECT
                a.id,
                a.coligada,
                a.chapa_pai,
                fp.nome nome_pai,
                a.chapa_filho,
                ff.nome nome_filho,
                a.dt_limite,
                FORMAT(a.dt_limite, 'dd/MM/yyyy') AS dt_limite_br
            FROM zcrmportal_art61_excecao a
            LEFT JOIN " . DBRM_BANCO . "..PFUNC fp ON 
                fp.codcoligada = '" . $_SESSION['func_coligada'] . "' AND 
                fp.chapa = a.chapa_pai COLLATE Latin1_General_CI_AS 
            LEFT JOIN " . DBRM_BANCO . "..PFUNC ff ON 
                ff.codcoligada = '" . $_SESSION['func_coligada'] . "' AND 
                ff.chapa = a.chapa_filho COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '" . $_SESSION['func_coligada'] . "' AND
                a.ativo = 'S'
        ";

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      return false;
    }
  }

  // -------------------------------------------------------
  // Lista Codeventos por Filial
  // -------------------------------------------------------
  public function ListarCodevento()
  {

    $queryConfig = " 
            SELECT
                a.id,
                a.coligada,
                a.codfilial,
                l.nomefantasia as nome_filial,
                a.de_codevento,
				e1.descricao as de_evento,
				a.para_codevento,
				e2.descricao as para_evento
            FROM zcrmportal_art61_codevento a
            LEFT JOIN " . DBRM_BANCO . "..GFILIAL l ON 
                l.codcoligada = a.coligada AND 
	            l.codfilial = a.codfilial 
            LEFT JOIN " . DBRM_BANCO . "..PEVENTO e1 ON 
                e1.codcoligada = a.coligada AND 
	            e1.codigo = a.de_codevento COLLATE Latin1_General_CI_AS 
            LEFT JOIN " . DBRM_BANCO . "..PEVENTO e2 ON 
                e2.codcoligada = a.coligada AND 
	            e2.codigo = a.de_codevento COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '" . $_SESSION['func_coligada'] . "' AND
                a.ativo = 'S'
        ";

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      return false;
    }
  }


  // -------------------------------------------------------
  // Insere ou Altera Centro Custo no Art.61
  // -------------------------------------------------------
  public function Grava_CCusto($dados)
  {

    $id_ccusto = $dados['idccusto'];
    $codcusto = strlen(trim($dados['codcusto'])) > 0 ? "'{$dados['codcusto']}'" : "NULL";
    $diretoria = strlen(trim($dados['diretoria'])) > 0 ? "'{$dados['diretoria']}'" : "NULL";
    $area = strlen(trim($dados['area'])) > 0 ? "'{$dados['area']}'" : "NULL";

    // verifica se centro de custo já existe
    $query = "
            SELECT id
            FROM zcrmportal_art61_areas
            WHERE id <> " . $id_ccusto . " 
            AND codcusto = " . $codcusto . "
            AND coligada = '" . session()->get('func_coligada') . "'";
    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      return responseJson('error', 'Esse centro de custo já está cadastrado.');
    }

    if ($id_ccusto == 0) {
      $query = "
                INSERT INTO
                    zcrmportal_art61_areas
                    (codcusto, diretoria, area, dtalt, usualt, coligada)
                VALUES
                    ({$codcusto}, {$diretoria}, {$area}, '" . date('Y-m-d H:i:s') . "', 
                     '" . session()->get('log_id') . "', '" . session()->get('func_coligada') . "')
            ";
    } else {
      $query = "
                UPDATE
                    zcrmportal_art61_areas
                SET
                    codcusto = {$codcusto},
                    diretoria = {$diretoria},
                    area = {$area},
                    dtalt = '" . date('Y-m-d H:i:s') . "',
                    usualt = '" . session()->get('log_id') . "'
                WHERE
                    id = " . $id_ccusto . "
            ";
    }
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      notificacao('success', 'Centro de Custo gravado com sucesso');
      return responseJson('success', 'Centro de Custo gravado com sucesso.');
    } else {
      return responseJson('error', 'Falha ao gravar Centro de Custo.');
    }
  }

  // -------------------------------------------------------
  // Deleta Centro Custo no Art.61
  // -------------------------------------------------------
  public function Deleta_CCusto($dados)
  {

    $id_ccusto = $dados['id'];

    $query = "
            UPDATE
               zcrmportal_art61_areas
            SET
               ativo = 'N',
               dtalt = '" . date('Y-m-d H:i:s') . "',
               usualt = '" . session()->get('log_id') . "'
            WHERE
               id = " . $id_ccusto . "
        ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Centro de Custo excluído com sucesso.');
    } else {
      return responseJson('error', 'Falha ao excluir Centro de Custo.');
    }
  }

  // -------------------------------------------------------
  // Insere ou Altera Prorrogação
  // -------------------------------------------------------
  public function Grava_Prorroga($dados)
  {

    $id_prorroga = $dados['id_prorroga'];
    $chapa = strlen(trim($dados['chapa'])) > 0 ? "'{$dados['chapa']}'" : "NULL";
    $dt_extendida = strlen(trim($dados['dt_extendida'])) > 0 ? "'{$dados['dt_extendida']}'" : "NULL";

    // verifica se chapa já existe
    $query = "
            SELECT id
            FROM zcrmportal_art61_prorroga
            WHERE id <> " . $id_prorroga . " 
            AND chapa = " . $chapa . "
            AND coligada = '" . session()->get('func_coligada') . "'";
    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      return responseJson('error', 'Essa chapa já está cadastrada.');
    }

    if ($id_prorroga == 0) {
      $query = "
                INSERT INTO
                    zcrmportal_art61_prorroga
                    (chapa, dt_extendida, dtalt, usualt, coligada)
                VALUES
                    ({$chapa}, {$dt_extendida}, '" . date('Y-m-d H:i:s') . "', 
                     '" . session()->get('log_id') . "', '" . session()->get('func_coligada') . "')
            ";
    } else {
      $query = "
                UPDATE
                    zcrmportal_art61_prorroga
                SET
                    chapa = {$chapa},
                    dt_extendida = {$dt_extendida},
                    dtalt = '" . date('Y-m-d H:i:s') . "',
                    usualt = '" . session()->get('log_id') . "'
                WHERE
                    id = " . $id_prorroga . "
            ";
    }
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      notificacao('success', 'Prorrogação gravada com sucesso');
      return responseJson('success', 'Prorrogação gravada com sucesso.');
    } else {
      return responseJson('error', 'Falha ao gravar Prorrogação.');
    }
  }

  // -------------------------------------------------------
  // Deleta Prorrogação
  // -------------------------------------------------------
  public function Deleta_Prorroga($dados)
  {

    $id_prorroga = $dados['id'];

    $query = "
            UPDATE
               zcrmportal_art61_prorroga
            SET
               ativo = 'N',
               dtalt = '" . date('Y-m-d H:i:s') . "',
               usualt = '" . session()->get('log_id') . "'
            WHERE
               id = " . $id_prorroga . "
        ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Prorrogação excluída com sucesso.');
    } else {
      return responseJson('error', 'Falha ao excluir Prorrogação.');
    }
  }

  // -------------------------------------------------------
  // Insere ou Altera Exceção
  // -------------------------------------------------------
  public function Grava_Excecao($dados)
  {

    $id_excecao = $dados['id_excecao'];
    $chapa_pai = strlen(trim($dados['chapa_pai'])) > 0 ? "'{$dados['chapa_pai']}'" : "NULL";
    $chapa_filho = strlen(trim($dados['chapa_pai'])) > 0 ? "'{$dados['chapa_filho']}'" : "NULL";
    $dt_limite = strlen(trim($dados['dt_limite'])) > 0 ? "'{$dados['dt_limite']}'" : "NULL";

    // verifica se excecao já existe
    $query = "
            SELECT id
            FROM zcrmportal_art61_excecao
            WHERE id <> " . $id_excecao . " 
            AND chapa_pai = " . $chapa_pai . "
            AND chapa_filho = " . $chapa_filho . "
            AND coligada = '" . session()->get('func_coligada') . "'";
    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      return responseJson('error', 'Essa exceção já está cadastrada.');
    }

    if ($id_excecao == 0) {
      $query = "
                INSERT INTO
                    zcrmportal_art61_excecao
                    (chapa_pai, chapa_filho, dt_limite, dtalt, usualt, coligada)
                VALUES
                    ({$chapa_pai}, {$chapa_filho}, {$dt_limite}, '" . date('Y-m-d H:i:s') . "', 
                     '" . session()->get('log_id') . "', '" . session()->get('func_coligada') . "')
            ";
    } else {
      $query = "
                UPDATE
                    zcrmportal_art61_excecao
                SET
                    chapa_pai = {$chapa_pai},
                    chapa_filho = {$chapa_filho},
                    dt_limite = {$dt_limite},
                    dtalt = '" . date('Y-m-d H:i:s') . "',
                    usualt = '" . session()->get('log_id') . "'
                WHERE
                    id = " . $id_excecao . "
            ";
    }
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      notificacao('success', 'Exceção gravada com sucesso');
      return responseJson('success', 'Exceção gravada com sucesso.');
    } else {
      return responseJson('error', 'Falha ao gravar Exceção.');
    }
  }

  // -------------------------------------------------------
  // Insere ou Altera Codevento
  // -------------------------------------------------------
  public function Grava_Codevento($dados)
  {

    $id_codevento = $dados['id_codevento'];
    $codfilial = strlen(trim($dados['codfilial'])) > 0 ? "{$dados['codfilial']}" : "NULL";
    $de_codevento = strlen(trim($dados['de_codevento'])) > 0 ? "'{$dados['de_codevento']}'" : "NULL";
    $para_codevento = strlen(trim($dados['para_codevento'])) > 0 ? "'{$dados['para_codevento']}'" : "NULL";

    // verifica se filial já existe
    $query = "
            SELECT id
            FROM zcrmportal_art61_codevento
            WHERE id <> " . $id_codevento . " 
            AND codfilial = " . $codfilial . "
            AND de_codevento = " . $de_codevento . "
            AND coligada = '" . session()->get('func_coligada') . "'";
    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      return responseJson('error', 'Esse evento já está cadastrado nessa filial.');
    }

    if ($id_codevento == 0) {
      $query = "
                INSERT INTO
                    zcrmportal_art61_codevento
                    (codfilial, de_codevento, para_codevento, dtalt, usualt, coligada)
                VALUES
                    ({$codfilial}, {$de_codevento}, {$para_codevento}, '" . date('Y-m-d H:i:s') . "', 
                     '" . session()->get('log_id') . "', '" . session()->get('func_coligada') . "')
            ";
    } else {
      $query = "
                UPDATE
                    zcrmportal_art61_codevento
                SET
                    codfilial = {$codfilial},
                    de_codevento = {$de_codevento},
                    para_codevento = {$para_codevento},
                    dtalt = '" . date('Y-m-d H:i:s') . "',
                    usualt = '" . session()->get('log_id') . "'
                WHERE
                    id = " . $id_codevento . "
            ";
    }
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      notificacao('success', 'Evento gravado com sucesso na Filial');
      return responseJson('success', 'Evento gravado com sucesso na Filial.');
    } else {
      return responseJson('error', 'Falha ao gravar Evento na Filial.');
    }
  }

  // -------------------------------------------------------
  // Deleta Exceção
  // -------------------------------------------------------
  public function Deleta_Excecao($dados)
  {

    $id_excecao = $dados['id'];

    $query = "
            UPDATE
               zcrmportal_art61_excecao
            SET
               ativo = 'N',
               dtalt = '" . date('Y-m-d H:i:s') . "',
               usualt = '" . session()->get('log_id') . "'
            WHERE
               id = " . $id_excecao . "
        ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Exceção excluída com sucesso.');
    } else {
      return responseJson('error', 'Falha ao excluir Exceção.');
    }
  }

  // -------------------------------------------------------
  // Deleta Evento
  // -------------------------------------------------------
  public function Deleta_Codevento($dados)
  {

    $id_codevento = $dados['id'];

    $query = "
            UPDATE
               zcrmportal_art61_codevento
            SET
               ativo = 'N',
               dtalt = '" . date('Y-m-d H:i:s') . "',
               usualt = '" . session()->get('log_id') . "'
            WHERE
               id = " . $id_codevento . "
        ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Evento excluído com sucesso.');
    } else {
      return responseJson('error', 'Falha ao excluir Evento.');
    }
  }

  // -------------------------------------------------------
  // Importar Centros de Custos
  // -------------------------------------------------------
  public function Importa_Area($dados)
  {
    $documento = $dados['documento'];

    $file_name = $documento['arquivo_importacao']['name'] ?? null;
    $file_type = $documento['arquivo_importacao']['type'] ?? null;
    $file_size = $documento['arquivo_importacao']['size'] ?? null;
    $arquivo = $documento['arquivo_importacao']['tmp_name'] ?? null;

    if ($arquivo == null) return responseJson('error', 'Arquivo inválido.');
    if ($file_name == null) return responseJson('error', 'Nome do arquivo inválido.');
    if ($file_type == null) return responseJson('error', 'Tipo do arquivo inválido.');
    if ($file_size == null) return responseJson('error', 'Tamanho do arquivo inválido.');

    $spreadsheet = IOFactory::load($arquivo);
    // Pega a primeira planilha
    $worksheet = $spreadsheet->getActiveSheet();

    // Le os dados da planilha como array
    $data = $worksheet->toArray();

    // Define as colunas esperadas
    $colunasEsperadas = ['COD_COLIGADA', 'COD_CCUSTO', 'NOME_CCUSTO', 'DIRETORIA', 'AREA'];

    // Valida o nome das colunas
    $colunasLidas = $data[0]; // A primeira linha contém o nome das colunas
    if ($colunasEsperadas !== $colunasLidas) {
      return responseJson('error', 'As colunas do arquivo Excel não correspondem às colunas esperadas.<br><br>Esperado: COD_COLIGADA, COD_CCUSTO, NOME_CCUSTO, DIRETORIA, AREA.<br><br>Recebido:' . json_encode($colunasLidas));
    }

    // Loop para ler e gravar dados novos
    for ($i = 1; $i < count($data); $i++) {
      $cod_coligada = $data[$i][0];
      $cod_ccusto = $data[$i][1];
      $diretoria = $data[$i][3];
      $area = $data[$i][4];

      if ($cod_ccusto != '') {
        // verifica ccusto já existe
        $where = "codcusto = '{$cod_ccusto}' AND coligada = '{$cod_coligada}' AND ativo = 'S'";
        $query = "SELECT id FROM zcrmportal_art61_areas WHERE {$where}";
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
          // Atualiza dados
          $query = " 
                            UPDATE
                                zcrmportal_art61_areas
                            SET
                                diretoria = '{$diretoria}',
                                area = '{$area}',
                                dtalt = '" . date('Y-m-d H:i:s') . "',
                                usualt = '" . session()->get('log_id') . "'
                            WHERE
                                {$where}
                        ";
          //echo '<pre> '.$query;
          //exit();
          $this->dbportal->query($query);
        } else {
          // Insere dados
          $query = " 
                        INSERT INTO
                            zcrmportal_art61_areas
                            (codcusto, diretoria, area, dtalt, usualt, coligada)
                        VALUES
                            ('{$cod_ccusto}', '{$diretoria}', '{$area}', '" . date('Y-m-d H:i:s') . "', 
                            '" . session()->get('log_id') . "', '" . session()->get('func_coligada') . "')   
                        ";
          //echo '<pre> '.$query;
          //exit();
          $this->dbportal->query($query);
        }
      }
    }

    notificacao('success', 'Importação concluída com sucesso');
    return responseJson('success', 'Importação concluída com sucesso.');
  }

  // -------------------------------------------------------
  // Importar Justificativas
  // -------------------------------------------------------
  public function Importa_Justificativas($dados)
  {
    $documento = $dados['documento'];
    $id_requisicao = $dados['id_requisicao'];

    $file_name = $documento['arquivo_importacao']['name'] ?? null;
    $file_type = $documento['arquivo_importacao']['type'] ?? null;
    $file_size = $documento['arquivo_importacao']['size'] ?? null;
    $arquivo = $documento['arquivo_importacao']['tmp_name'] ?? null;

    if ($arquivo == null) return responseJson('error', 'Arquivo inválido.');
    if ($file_name == null) return responseJson('error', 'Nome do arquivo inválido.');
    if ($file_type == null) return responseJson('error', 'Tipo do arquivo inválido.');
    if ($file_size == null) return responseJson('error', 'Tamanho do arquivo inválido.');

    $spreadsheet = IOFactory::load($arquivo);
    // Pega a primeira planilha
    $worksheet = $spreadsheet->getActiveSheet();

    // Le os dados da planilha como array
    $data = $worksheet->toArray();

    // Define as colunas esperadas
    $colunasEsperadas = ['ID', 'ID_REQ', 'DATA', 'FILIAL', 'CHAPA', 'NOME', 'COD_JUSTIFICATIVA', 'DESC_JUSTIFICATIVA', 'OBS'];

    // Valida o nome das colunas
    $colunasLidas = $data[0]; // A primeira linha contém o nome das colunas
    if ($colunasEsperadas !== $colunasLidas) {
      return responseJson('error', 'As colunas do arquivo Excel não correspondem às colunas esperadas.<br><br>Esperado: ID, ID_REQ, DATA, FILIAL, CHAPA, NOME, COD_JUSTIFICATIVA, DESC_JUSTIFICATIVA, OBS.<br><br>Recebido:' . json_encode($colunasLidas));
    }

    // Loop para ler e gravar dados novos
    for ($i = 1; $i < count($data); $i++) {
      $id = $data[$i][0];
      $id_req = $data[$i][1];
      $id_just = $data[$i][6];
      $desc_just = $data[$i][7];
      $obs = $data[$i][8];

      if (is_numeric($id) and is_numeric($id_req) and (is_numeric($id_just) or $desc_just != '' or $obs != '')) {
        if ($desc_just != '') {
          $query = "select id from zcrmportal_ponto_motivos where lower(cast(descricao AS nvarchar(MAX))) = lower('" . $desc_just . "') and tipo = 6";
          $result = $this->dbportal->query($query);
          $row = $result->getRow();
          if (isset($row)) {
            $id_just = $row->id;
          }
        }
        if (is_numeric($id_just)) {
          $query = "select id from zcrmportal_ponto_motivos where id = " . $id_just . " and tipo = 6";
          $result = $this->dbportal->query($query);
          $row = $result->getRow();
          if (isset($row)) {
            $id_just = '' . $row->id;
          } else {
            $id_just = 'NULL';
          }
        } else {
          $id_just = 'NULL';
        }

        // verifica ccusto já existe
        $where = "id = '{$id}' AND id_req = '{$id_req}'";
        $query = " 
            UPDATE
              zcrmportal_art61_req_chapas
            SET
              id_justificativa = {$id_just},
              obs = '{$obs}',
              dtalt = '" . date('Y-m-d H:i:s') . "',
              usualt = '" . session()->get('log_id') . "'
            WHERE
              {$where}
        ";
        //echo '<pre> '.$query;
        //exit();
        $this->dbportal->query($query);
      }
    }

    notificacao('success', 'Importação concluída com sucesso');
    return responseJson('success', 'Importação concluída com sucesso.');
  }

  // -------------------------------------------------------
  // Lista solicitacoes do Art61
  // -------------------------------------------------------
  public function ListarArt61($periodo = '', $id = 0)
  {

    $filtro = ($periodo == '') ? '' : " AND a.dt_ini_ponto = '" . substr($periodo, 0, 10) . "'";
    $filtro .= ($id == 0) ? '' : " AND a.id = " . $id;
    $filtro = ($id == 0 and $periodo == '') ? ' AND a.id < 0' : $filtro;  // criado para não listar nada

    $queryConfig = " 
          SELECT
              a.id,
              a.dt_requisicao,
              FORMAT(a.dt_requisicao, 'dd/MM/yyyy') AS dt_req_br,
              a.status,
              a.chapa_requisitor,
              f.nome AS nome_requisitor,
              a.dt_ini_ponto, 
              a.dt_fim_ponto,
              FORMAT(a.dt_ini_ponto, 'yyyy-MM-dd')+' and '+FORMAT(a.dt_fim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
              FORMAT(a.dt_ini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dt_fim_ponto, 'dd/MM/yyyy') AS per_ponto_br,
              a.anocomp,
              a.mescomp
          FROM zcrmportal_art61_requisicao a 
          LEFT JOIN " . DBRM_BANCO . "..PFUNC f ON 
              f.codcoligada = '" . $_SESSION['func_coligada'] . "' AND 
              f.chapa = a.chapa_requisitor COLLATE Latin1_General_CI_AS 
          WHERE 
              a.id_coligada = '" . $_SESSION['func_coligada'] . "'
          AND a.status > 0
          " . $filtro . " 
      ";

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      return [];
    }
  }

  // -------------------------------------------------------
  // Lista apenas chapas de solicitação do Art61
  // -------------------------------------------------------
  public function ListarReqApenasChapas($id = 0)
  {

    $filtro = ($id == 0) ? '' : " AND id_req = " . $id;

    $queryConfig = " 
          SELECT
              a.chapa_colab chapa,
              f.codfilial
          FROM zcrmportal_art61_req_chapas a 
          LEFT JOIN zcrmportal_art61_requisicao r ON 
                r.id = a.id_req
          LEFT JOIN CorporeRMDEV..PFUNC f ON 
                f.codcoligada = r.id_coligada AND 
                f.chapa = a.chapa_colab COLLATE Latin1_General_CI_AS   
          WHERE a.status <> 'I' 
          " . $filtro;

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      return [];
    }
  }

  // -------------------------------------------------------
  // Lista chapas de solicitação do Art61
  // -------------------------------------------------------
  public function ListarReqChapas($id = 0)
  {

    $filtro = ($id == 0) ? '' : " AND id_req = " . $id;

    $queryConfig = " 
          SELECT
              a.id,
              a.id_req,
              a.dt_ponto,
              FORMAT(a.dt_ponto, 'dd/MM/yyyy') AS dt_ponto_br,
              f.codfilial,
			        a.chapa_colab,
              f.nome AS nome_colab,
              a.chapa_gestor,
              g.nome AS nome_gestor,
              dbo.[MINTOTIME](valor) AS valor, 
              a.id_justificativa,
              j.descricao AS desc_justificativa,
              a.obs,
              FORMAT(r.dt_ini_ponto, 'yyyy-MM-dd')+' and '+FORMAT(r.dt_fim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
              FORMAT(r.dt_ini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(r.dt_fim_ponto, 'dd/MM/yyyy') AS per_ponto_br,
              r.dt_requisicao,
              FORMAT(r.dt_requisicao, 'dd/MM/yyyy') AS dt_requisicao_br,
              f.codsecao,
              s.descricao as desc_secao,
              f.codfuncao,
              u.nome as desc_funcao,
              s.nrocencustocont as cod_ccusto,
              c.nome as desc_ccusto,
              e.area as area,
              a.codevento,
              p.descricao as desc_evento
              
          FROM zcrmportal_art61_req_chapas a 
          LEFT JOIN zcrmportal_art61_requisicao r ON 
                r.id = a.id_req
          LEFT JOIN " . DBRM_BANCO . "..PFUNC f ON 
                f.codcoligada = r.id_coligada AND 
                f.chapa = a.chapa_colab COLLATE Latin1_General_CI_AS 
          LEFT JOIN " . DBRM_BANCO . "..PFUNC g ON 
                g.codcoligada = r.id_coligada AND 
                g.chapa = a.chapa_gestor COLLATE Latin1_General_CI_AS 
          LEFT JOIN " . DBRM_BANCO . "..PFUNCAO u ON 
                u.codcoligada = f.codcoligada AND 
                u.codigo = f.codfuncao 
          LEFT JOIN " . DBRM_BANCO . "..PSECAO s ON 
                s.codcoligada = f.codcoligada AND 
                s.codigo = f.codsecao
          LEFT JOIN " . DBRM_BANCO . "..GCCUSTO c ON 
                c.codcoligada = s.codcoligada AND 
                c.codccusto = s.nrocencustocont
          LEFT JOIN " . DBRM_BANCO . "..PEVENTO p ON 
                p.codcoligada = f.codcoligada AND 
                p.codigo = a.codevento COLLATE Latin1_General_CI_AS
          LEFT JOIN zcrmportal_ponto_motivos j ON 
                j.id = a.id_justificativa
          LEFT JOIN zcrmportal_art61_areas e ON 
                e.coligada = r.id_coligada AND 
                e.codcusto = c.codccusto COLLATE Latin1_General_CI_AS
          WHERE a.status <> 'I' 
          " . $filtro . " 
      ";

    //echo '<PRE> '.$queryConfig;
    //die();
    //exit();

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() > 0) {
      return $result->getResultArray();
    } else {
      return [];
    }
  }

  public function saveAnexo($id, $dados)
  {
    $file_name = $dados->getName();
    $file_type = $dados->getMimeType();
    $file_size = $dados->getSize();
    $file_data = base64_encode(file_get_contents($dados->getTempName()));

    $anexo = $this->dbportal->query(
      "INSERT INTO zcrmportal_art61_req_chapa_anexo (id_req_chapa, coligada, usucad, dtcad, file_type, file_size, file_name, file_data) 
                VALUES 
            ('{$id}','{$this->coligada}', {$_SESSION['log_id']}, '{$this->now}', '{$file_type}', '{$file_size}', '{$file_name}', '{$file_data}')"
    );

    return $anexo;
  }

  public function getAnexos($id)
  {

    $query = " SELECT * FROM zcrmportal_art61_req_chapa_anexo WHERE id_req_chapa = '" . $id . "' order by id";
    //echo $query;
    //die();

    $result = $this->dbportal->query($query);
    if (!$result) return false;
    return ($result->getNumRows() > 0)
      ? $result->getResult()
      : false;
  }

  public function DeleteReqAnexo($id)
  {
    $query = " 
        DELETE FROM zcrmportal_art61_req_chapa_anexo
            WHERE id = '" . $id . "'
        ";
    // exit('<pre>'.print_r($query,1));
    return $this->dbportal->query($query);
  }

  public function ListarColabSolicitacao($rh_master = false, $chapa = false)
  {
    //-----------------------------------------
    // filtro das chapas que o lider pode ver
    //-----------------------------------------
    $mHierarquia = Model('HierarquiaModel');
    $objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer($chapa, false, true);
    $isLider = $mHierarquia->isLider($chapa);

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
    $secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer($chapa);
    $filtro_secao_gestor = "";

    if ($secoes) {
      $codsecoes = "";
      foreach ($secoes as $ids => $Secao) {
        $codsecoes .= "'" . $Secao['codsecao'] . "',";
      }
      $filtro_secao_gestor = " A.CODSECAO IN (" . substr($codsecoes, 0, -1) . ") OR ";
    }
    //-----------------------------------------

    $chapaFunc = ($chapa) ? $chapa : util_chapa(session()->get('func_chapa'))['CHAPA'];
    $qr_secao = " AND (" . $filtro_secao_lider . " " . $filtro_secao_gestor . " A.CHAPA = '{$chapaFunc}')";

    if ($rh_master) $qr_secao = "";

    $query = " 
			SELECT 
				A.CHAPA,
				A.NOME,
        A.CODFILIAL
				
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
        
			GROUP BY
				A.CHAPA,
				A.NOME,
        A.CODFILIAL
				
			ORDER BY
				A.NOME
		";
    //exit('<pre>'.print_r($query,1));
    $result = $this->dbrm->query($query);
    if (!$result) return false;
    return ($result->getNumRows() > 0)
      ? $result->getResultArray()
      : false;
  }

  // -------------------------------------------------------
  // Cria nova solicitacao do Art61
  // -------------------------------------------------------
  public function Nova_Solicitacao($dados)
  {
    $chapa_solicitante = strlen(trim($dados['chapa_solicitante'])) > 0 ? "{$dados['chapa_solicitante']}" : "NULL";
    $data_requisicao = strlen(trim($dados['data_requisicao'])) > 0 ? "{$dados['data_requisicao']}" : "NULL";
    $per_ponto_sql = strlen(trim($dados['periodo'])) > 0 ? "{$dados['periodo']}" : "NULL";
    $dtini_ponto = "NULL";
    $dtfim_ponto = "NULL";
    $mescomp = "NULL";
    $anocomp = "NULL";

    if (strlen(trim($dados['periodo'])) > 0) {
      $dtini_ponto = substr(trim($dados['periodo']), 0, 10);
      $dtfim_ponto = substr(trim($dados['periodo']), 11, 10);
      $mescomp = substr($dtfim_ponto, 5, 2);
      $anocomp = substr($dtfim_ponto, 0, 4);
    }

    $queryConfig = "
        SELECT
            a.dtini_req,
            a.dtfim_req,
            a.dtini_ponto,
            a.dtfim_ponto,
            a.codevento,
            FORMAT(a.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
            FORMAT(a.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
            FORMAT(a.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
            FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br,
			      p.dt_extendida,
			      r.id
        FROM zcrmportal_art61_config a 
		    LEFT JOIN zcrmportal_art61_prorroga p ON 
              p.coligada = '1' 
          AND p.ativo = 'S' 
          AND p.chapa = '" . $chapa_solicitante . "'
        LEFT JOIN zcrmportal_art61_requisicao r ON 
              r.chapa_requisitor = '" . $chapa_solicitante . "' 
          AND r.dt_ini_ponto = a.dtini_ponto 
          AND r.status > 0
        WHERE a.coligada = '" . session()->get('func_coligada') . "' 
    ";

    $result = $this->dbportal->query($queryConfig);
    if ($result->getNumRows() <= 0) {
      return responseJson('error', 'Falha ao ler configuração do Artigo.61.');
    }

    $resConfig = $result->getResultArray();
    $dtExtendida = is_null($resConfig[0]['dt_extendida']) ? $resConfig[0]['dtfim_req'] : $resConfig[0]['dt_extendida'];
    $dtExtendida = ($dtExtendida < $resConfig[0]['dtfim_req']) ? $resConfig[0]['dtfim_req'] : $dtExtendida;

    $chapaExiste = is_null($resConfig[0]['id']) ? false : true;

    if ($chapaExiste) {
      return responseJson('error', 'Já existe solicitação para colaborador nesse período.');
    }

    if ($dtExtendida < $data_requisicao) {
      return responseJson('error', 'Data fora do limite permitido para criar solicitações.');
    }

    if ($per_ponto_sql != $resConfig[0]['per_ponto_sql']) {
      return responseJson('error', 'Esse período não está liberado para criar solicitações.');
    }

    $query = " 
      INSERT INTO zcrmportal_art61_requisicao 
        (dt_requisicao, chapa_requisitor, dt_ini_ponto, dt_fim_ponto, mescomp, anocomp, id_coligada, id_criador) 
      VALUES 
        ('" . $data_requisicao . "', '" . $chapa_solicitante . "', '" . $dtini_ponto . "', '" . $dtfim_ponto . "', " . $mescomp . ", " . $anocomp . ", " . session()->get('func_coligada') . ", " . $_SESSION['log_id'] . ") ";

    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      $lastID = $this->dbportal->insertId();
      return responseJson('success', 'Solicitação criada com sucesso.', $lastID);
    } else {
      return responseJson('error', 'Falha ao criar a solicitação.');
    }
  }

  // -------------------------------------------------------
  // Deleta Requisicao do Art.61
  // -------------------------------------------------------
  public function Apaga_Requisicao($dados)
  {

    $id_req = $dados['id'];

    $query = "
            UPDATE
               zcrmportal_art61_req_chapas
            SET
               status = 'I'
            WHERE
               id_req = " . $id_req . "
        ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    $query = "
            UPDATE
               zcrmportal_art61_requisicao
            SET
               status = '0'
            WHERE
               id = " . $id_req . "
        ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Requisição excluída com sucesso.');
    } else {
      return responseJson('error', 'Falha ao excluir Requisicao.');
    }
  }

  // -------------------------------------------------------
  // Apaga Colaborador
  // -------------------------------------------------------
  public function Apaga_Colaborador($dados)
  {

    $id = $dados['id'];

    $query = "
            UPDATE
               zcrmportal_art61_req_chapas
            SET
               status = 'I'
            WHERE
               id = " . $id . "
        ";

    $this->dbportal->query($query);
    //echo $query;
    //die();

    if ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Colaborador/Evento excluído com sucesso.');
    } else {
      return responseJson('error', 'Falha ao excluir Colaborador/Evento.');
    }
  }

  // -------------------------------------------------------
  // Cria novo colaborador na solicitacao
  // -------------------------------------------------------
  public function Novo_Colaborador($dados)
  {
    $id_req = strlen(trim($dados['id_req'])) > 0 ? "{$dados['id_req']}" : "NULL";
    $chapa = strlen(trim($dados['chapa'])) > 0 ? "{$dados['chapa']}" : "NULL";
    $per_ponto_sql = strlen(trim($dados['periodo'])) > 0 ? "{$dados['periodo']}" : "NULL";
    $dtini_ponto = "NULL";
    $dtfim_ponto = "NULL";

    if (strlen(trim($dados['periodo'])) > 0) {
      $dtini_ponto = substr(trim($dados['periodo']), 0, 10);
      $dtfim_ponto = substr(trim($dados['periodo']), 15, 10);
    }

    $queryChapa = "
        SELECT
            chapa_colab
        FROM zcrmportal_art61_req_chapas
		    WHERE 
              chapa_colab = '" . $chapa . "'
          AND status = 'A'
          AND FORMAT(dt_ini_ponto, 'yyyy-MM-dd')+' and '+FORMAT(dt_fim_ponto , 'yyyy-MM-dd') = '" . $per_ponto_sql . "'
    ";

    $result = $this->dbportal->query($queryChapa);
    if ($result->getNumRows() > 0) {
      return responseJson('error', 'Já existe solicitação para colaborador nesse período.');
    }

    $query = " 
      INSERT INTO zcrmportal_art61_req_chapas
        (id_req, chapa_colab, dt_ini_ponto, dt_fim_ponto) 
      VALUES 
        (" . $id_req . ", '" . $chapa . "', '" . $dtini_ponto . "', '" . $dtfim_ponto . "') ";

    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 0) {
      $lastID = $this->dbportal->insertId();
      return responseJson('success', 'Colaborador registrado com sucesso.', $lastID);
    } else {
      return responseJson('error', 'Falha ao registrar colaborador.');
    }
  }

  // -------------------------------------------------------
  // Processar solcitacao
  // -------------------------------------------------------
  public function Processar_Req($dados)
  {

    $id_req = $dados['id'];
    $chapa_requisitor = $dados['chapa_requisitor'];
    $dt_ini_per = substr($dados['per_ponto'], 0, 10);
    $dt_fim_per = substr($dados['per_ponto'], -10);
    $rh_master = ($dados['rh_master'] == 'S') ? true : false;

    $chapasReq = $this->ListarReqApenasChapas($id_req);
    $eventos = $this->ListarCodevento();

    $chapas = $this->ListarColabSolicitacao(false, $chapa_requisitor);

    $lista = "";
    foreach ($chapas as $chapa) {
      $lista .= "'" . $chapa['CHAPA'] . "',";
    }
    foreach ($chapasReq as $chapa) {
      $lista .= "'" . $chapa['chapa'] . "',";
    }
    $lista = substr($lista, 0, -1);

    // só vai ter processamento se existirem eventos
    if ($eventos) {
      $codeventos = "";
      foreach ($eventos as $ids => $evento) {
        $codeventos .= "'" . $evento['codfilial'] . $evento['de_codevento'] . "',";
      }
      $codeventos = substr($codeventos, 0, -1);
    }
    /*   
    echo $codeventos;
    echo '<br>';
    print_r($dados);
    echo '<br>';
    print_r($lista);
    die();
    exit();
*/

    // Desabilita todos os colaboradores
    $query = "
    UPDATE
      zcrmportal_art61_req_chapas
    SET
      status = 'I',
      obs = 'REPROCESSAMENTO EM " . $this->now . "'
    WHERE
      status = 'A' AND
      id_req = " . $id_req . "
    ";
    $this->dbportal->query($query);

    // prepara a lista para inserção
    $query = "
        with lista as (
        select
          a.CHAPA,
          a.CODCOLIGADA,
          f.CODFILIAL,
          a.CODEVE,
          a.DATA,
          a.NUMHORAS,
          a.VALOR,
	        ISNULL(g.GESTOR_CHAPA,'') as GESTOR_CHAPA,
          (SELECT 
            CODINDICE
                FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
            ) INDICE,
          (SELECT 
            CONCAT('IndiceDia ', CODINDICE, '  --> ') +
                COALESCE(dbo.MINTOTIME(ENTRADA1),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA1) IS NULL THEN '' ELSE '  ' END) +
                COALESCE(dbo.MINTOTIME(SAIDA1),'')   + (CASE WHEN dbo.MINTOTIME(SAIDA1)   IS NULL THEN '' ELSE '  ' END) +
                COALESCE(dbo.MINTOTIME(ENTRADA2),'') + (CASE WHEN dbo.MINTOTIME(ENTRADA2) IS NULL THEN '' ELSE '  ' END) +
                COALESCE(dbo.MINTOTIME(SAIDA2),'') ESCALA
                FROM dbo.CALCULO_HORARIO_PT4 (CONVERT(VARCHAR(10),A.DATA,103), A.CHAPA, A.CODCOLIGADA, 0) 
            ) ESCALA,
          (SELECT 
                TOP 1 CODHORARIO
                FROM PFHSTHOR P
                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                ORDER BY DTMUDANCA DESC
          ) HORARIO,
          (SELECT 
                TOP 1 Q.DESCRICAO
                FROM PFHSTHOR P
                INNER JOIN AHORARIO Q ON P.CODCOLIGADA = Q.CODCOLIGADA AND P.CODHORARIO = Q.CODIGO 
                WHERE A.CODCOLIGADA = P.CODCOLIGADA AND A.CHAPA = P.CHAPA AND P.DTMUDANCA <= A.DATA
                ORDER BY DTMUDANCA DESC
          ) DESC_HORARIO
        from AMOVFUNDIA a
        left join PFUNC f on f.CODCOLIGADA = a.CODCOLIGADA and f.CHAPA = a.CHAPA
        left join " . DBPORTAL_BANCO . "..GESTOR_CHAPA g on g.CODCOLIGADA = a.CODCOLIGADA and g.CHAPA = a.CHAPA COLLATE Latin1_General_CI_AS 
        where 
          a.DATA >= '" . $dt_ini_per . "' AND a.DATA <= '" . $dt_fim_per . "'
        and	cast(f.CODFILIAL AS VARCHAR)+a.CODEVE in (" . $codeventos . ")
        and a.CHAPA in (" . $lista . ")
        and a.CODCOLIGADA = '" . session()->get('func_coligada') . "' 
        )

        select l.*, z.HEXTRA_DIARIA 
        from lista l
        left join Z_OUTSERV_MELHORIAS3 z on z.CODCOLIGADA = l.CODCOLIGADA and z.CODHORARIO = l.HORARIO and z.CODINDICE = l.INDICE
    ";

    $result = $this->dbrm->query($query);
    if ($result->getNumRows() <= 0) {
      return responseJson('error', 'Não foram encontradas horas extras para esta solicitação.');
    }

    $resp = 'Processamento finalizado com sucesso.';
    if ($result) {
      $batidas = $result->getResult();
      foreach ($batidas as $batida) {
        if ($batida->NUMHORAS > $batida->HEXTRA_DIARIA) { // só gera se hora extra do dia maior que o permitido
          $query = " 
            INSERT INTO zcrmportal_art61_req_chapas
              (id_req, chapa_colab, dt_ponto, codevento, dt_ini_ponto, dt_fim_ponto, valor, chapa_gestor) 
            VALUES 
              (" . $id_req . ", '" . $batida->CHAPA . "', '" . $batida->DATA . "', '" . $batida->CODEVE . "', '" . $dt_ini_per . "', '" . $dt_fim_per . "', " . $batida->NUMHORAS . ", '" . $batida->GESTOR_CHAPA . "') ";

          $this->dbportal->query($query);

          if ($this->dbportal->affectedRows() <= 0) {
            $resp = 'Processamento finalizado. Alguns registros não foram gravados.';
          }
        }
      }
    }
    return responseJson('success', $resp);
  }

  // -------------------------------------------------------  
  // Grava Justificativa na Chapa da Requisição
  // -------------------------------------------------------
  public function Grava_Just_Req_Chapa($dados)
  {
    $ids = ($dados['id'] == -1) ? $dados['sel_ids'] : $dados['id'];
    $id_justificativa = $dados['id_just'];
    $obs = $dados['obs'];

    $query = "
            UPDATE
               zcrmportal_art61_req_chapas
            SET
               id_justificativa = " . $id_justificativa . ",
               obs = '" . $obs . "',
               dtalt = '" . date('Y-m-d H:i:s') . "',
               usualt = '" . session()->get('log_id') . "'
            WHERE
               id in ( " . $ids . " )
        ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 1) {
      return responseJson('success', 'Justificativas atualizadas com sucesso.');
    } elseif ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Justificativa atualizada com sucesso.');
    } else {
      return responseJson('error', 'Falha ao atualizar Justificativa.');
    }
  }

  // -------------------------------------------------------  
  // Envia para aprovação
  // -------------------------------------------------------
  public function Envia_Aprovacao($dados)
  {
    $ids = ($dados['id'] == -1) ? $dados['sel_ids'] : $dados['id'];

    // VALIDA SE REQ TEM CHAPAS
    $query = "
      WITH REQS AS (
        	SELECT 
          r.id,
          (SELECT COUNT(c.id_req) FROM zcrmportal_art61_req_chapas c 
          WHERE 
            c.valor > 0 AND
            c.status = 'A' AND 
            c.id_req = r.id
          ) as REGS
        FROM zcrmportal_art61_requisicao r
        WHERE r.id IN (" . $ids . ")
      )

      SELECT TOP 1 ID FROM REQS WHERE REGS = 0
    ";

    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      $regs = $result->getResultArray();
      return responseJson('error', 'A solicitação número '.$regs[0]['ID'].' não possui registros. Processo de envio interrompido.');
    }

    // ROTINA DE ENVIO DE EMAIL
    $query = "
      SELECT DISTINCT
        r.chapa_requisitor,
        e.NOME,
        e.EMAIL,
        FORMAT(r.dt_ini_ponto, 'dd/MM/yyyy') DTINI_BR, 
        FORMAT(r.dt_fim_ponto, 'dd/MM/yyyy') DTFIM_BR
      
      FROM zcrmportal_art61_requisicao r
      LEFT JOIN EMAIL_CHAPA e ON e.CODCOLIGADA = r.id_coligada AND e.CHAPA = r.chapa_requisitor COLLATE Latin1_General_CI_AS
      WHERE e.EMAIL IS NOT NULL AND r.id IN  (" . $ids . ")
    ";

    //echo '<PRE> '.$query;
    //exit();

    $result = $this->dbportal->query($query);
    if ($result->getNumRows() > 0) {
      $resFuncs = $result->getResultArray();
      foreach ($resFuncs as $key => $Func):
        $nome = $Func['NOME'];
        $email = $Func['EMAIL'];
        $dtinip_br = $Func['DTINI_BR'];
        $dtfimp_br = $Func['DTFIM_BR'];

        $assunto = '[Portal RH] Você possui solicitações pendentes de aprovação';
        $msg_nome = 'Prezado(a) ' . $nome . ',<br><br>';
        $mensagem = '
          Este é um lembrete de que você possui solicitações pendentes de aprovação no <strong>Portal RH - Módulo de Ponto</strong> no período de <strong>' . $dtinip_br . ' a ' . $dtfimp_br . '</strong>, ou posterior. Abaixo está um resumo das pendências: <br><br>
          <strong>Pendências de Artigo.61</strong><br><br>
          Solicitamos que acesse o Portal RH para revisar as solicitações pendentes.<br><br>
          Segue abaixo link para acesso ao Portal RH <a href="' . base_url() . '" target="_blank">' . base_url() . '</a><br><br>
          Atenciosamente,<br>
          <strong>Equipe Processos de RH</strong><br>
        ';

        $htmlEmail = templateEmail($msg_nome . $mensagem, '95%');

        //$email = 'deivison.batista@eldoradobrasil.com.br';
        $email = 'alvaro.zaragoza@ativary.com';
        $response = enviaEmail($email, $assunto, $htmlEmail);

      /* EMAIL PARA SUBSTITUTO
          if (!is_null($email_sub)) {
            $msg_nome_sub = 'Prezado(a) ' . $nome_sub . ',<br><br>';
            $htmlEmail = templateEmail($msg_nome_sub . $mensagem, '95%');
            $email_sub = 'deivison.batista@eldoradobrasil.com.br';
            //$email = 'alvaro.zaragoza@ativary.com';
            $response = enviaEmail($email_sub, $assunto, $htmlEmail);
            echo 'Enviado email para ' . $nome_sub . ' - ' . $email_sub . '<br>';
          }
          */
      endforeach;
    }

    $query = "
      UPDATE
          zcrmportal_art61_requisicao
      SET
          dt_envio_email = '" . date('Y-m-d H:i:s') . "',
          status = '2'
      WHERE
          id in ( " . $ids . " )
      AND status in ('1','4')
    ";
    //echo $query;
    //die();
    $this->dbportal->query($query);

    if ($this->dbportal->affectedRows() > 1) {
      return responseJson('success', 'Requisições enviadas para aprovação com sucesso.');
    } elseif ($this->dbportal->affectedRows() > 0) {
      return responseJson('success', 'Requisição enviada para aprovação com sucesso.');
    } else {
      return responseJson('error', 'Falha ao enviar para aprovação.');
    }
  }
}
