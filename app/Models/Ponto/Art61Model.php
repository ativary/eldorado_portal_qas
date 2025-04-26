<?php
namespace App\Models\Ponto;
use PhpOffice\PhpSpreadsheet\IOFactory;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class Art61Model extends Model {

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
    // Lista configurações do Art61
    // -------------------------------------------------------
    public function ListarConfigArt61(){

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
            FROM zcrmportal_art61_config WHERE coligada = '".session()->get('func_coligada')."' 
        ";

        $result = $this->dbportal->query($queryConfig);
        if($result->getNumRows() > 0) {
            return $result->getResultArray();

        } else {
            $query = " INSERT INTO zcrmportal_art61_config (coligada) VALUES ('".session()->get('func_coligada')."') ";
            $result = $this->dbportal->query($query);
            $query = $queryConfig;
            $result = $this->dbportal->query($query);
            return $result->getResultArray();
        }
    }

    // -------------------------------------------------------
    // Salva configurações do Art.61
    // -------------------------------------------------------
    public function SalvarConfig($dados){

        $dtini_req = strlen(trim($dados['dtini_req'])) > 0 ? "'{$dados['dtini_req']}'" : "NULL";
        $dtfim_req = strlen(trim($dados['dtfim_req'])) > 0 ? "'{$dados['dtfim_req']}'" : "NULL";
        $codevento = strlen(trim($dados['codevento'])) > 0 ? "'{$dados['codevento']}'" : "NULL";
        $dtini_ponto = "NULL";
        $dtfim_ponto = "NULL";
        
        if(strlen(trim($dados['dtper_ponto'])) > 0) {
            $dtini_ponto = substr(trim($dados['dtper_ponto']),0,10);
            $dtfim_ponto = substr(trim($dados['dtper_ponto']),11,10);
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

        if($this->dbportal->affectedRows() > 0){

            notificacao('success', 'Artigo.61 configurado com sucesso');
            return responseJson('success', 'Artigo.61 configurado com sucesso.');

        }else{
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
                FORMAT(INICIOMENSAL, 'dd/MM/yyyy')+' a '+FORMAT(FIMMENSAL, 'dd/MM/yyyy') AS PERIODO_BR
            FROM APERIODO
            WHERE CODCOLIGADA = ".$this->coligada." 
            ORDER BY INICIOMENSAL DESC 
        ");
		if(!$result) return false;
		return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
	}

    //---------------------------------------------
    // Listar Centro de Custo
    //---------------------------------------------
    public function ListarCentroCusto(){

        $where = " AND CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
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
    public function ListarColab(){

        $where = " AND CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
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
    public function ListarFilial(){

        $where = " AND CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
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
    public function ListarEvento(){

        $where = " AND CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
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
    public function ListarCCustoArea(){

        $queryConfig = " 
            SELECT
                a.id,
                a.coligada,
                a.codcusto,
                c.nome nome_ccusto,
                a.diretoria,
                a.area
            FROM zcrmportal_art61_areas a
            LEFT JOIN ".DBRM_BANCO."..GCCUSTO c ON 
                c.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
                c.CODCCUSTO = a.codcusto COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '".$_SESSION['func_coligada']."' AND
                a.ativo = 'S'
        ";

        $result = $this->dbportal->query($queryConfig);
        if($result->getNumRows() > 0) {
            return $result->getResultArray();
        } else {
            return false;
        }
    }

    // -------------------------------------------------------
    // Lista Prorrogações
    // -------------------------------------------------------
    public function ListarProrroga(){

        $queryConfig = " 
            SELECT
                a.id,
                a.coligada,
                a.chapa,
                f.nome,
                a.dt_extendida,
                FORMAT(a.dt_extendida, 'dd/MM/yyyy') AS dt_extendida_br
            FROM zcrmportal_art61_prorroga a
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON 
                f.codcoligada = '".$_SESSION['func_coligada']."' AND 
                f.chapa = a.chapa COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '".$_SESSION['func_coligada']."' AND
                a.ativo = 'S'
        ";

        $result = $this->dbportal->query($queryConfig);
        if($result->getNumRows() > 0) {
            return $result->getResultArray();
        } else {
            return false;
        }
    }

    // -------------------------------------------------------
    // Lista Exceções
    // -------------------------------------------------------
    public function ListarExcecao(){

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
            LEFT JOIN ".DBRM_BANCO."..PFUNC fp ON 
                fp.codcoligada = '".$_SESSION['func_coligada']."' AND 
                fp.chapa = a.chapa_pai COLLATE Latin1_General_CI_AS 
            LEFT JOIN ".DBRM_BANCO."..PFUNC ff ON 
                ff.codcoligada = '".$_SESSION['func_coligada']."' AND 
                ff.chapa = a.chapa_filho COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '".$_SESSION['func_coligada']."' AND
                a.ativo = 'S'
        ";

        $result = $this->dbportal->query($queryConfig);
        if($result->getNumRows() > 0) {
            return $result->getResultArray();
        } else {
            return false;
        }
    }

    // -------------------------------------------------------
    // Lista Codeventos por Filial
    // -------------------------------------------------------
    public function ListarCodevento(){

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
            LEFT JOIN ".DBRM_BANCO."..GFILIAL l ON 
                l.codcoligada = a.coligada AND 
	            l.codfilial = a.codfilial 
            LEFT JOIN ".DBRM_BANCO."..PEVENTO e1 ON 
                e1.codcoligada = a.coligada AND 
	            e1.codigo = a.de_codevento COLLATE Latin1_General_CI_AS 
            LEFT JOIN ".DBRM_BANCO."..PEVENTO e2 ON 
                e2.codcoligada = a.coligada AND 
	            e2.codigo = a.de_codevento COLLATE Latin1_General_CI_AS 
            WHERE a.coligada = '".$_SESSION['func_coligada']."' AND
                a.ativo = 'S'
        ";

        $result = $this->dbportal->query($queryConfig);
        if($result->getNumRows() > 0) {
            return $result->getResultArray();
        } else {
            return false;
        }
    }

    
    // -------------------------------------------------------
    // Insere ou Altera Centro Custo no Art.61
    // -------------------------------------------------------
    public function Grava_CCusto($dados){
        
        $id_ccusto = $dados['idccusto'];
        $codcusto = strlen(trim($dados['codcusto'])) > 0 ? "'{$dados['codcusto']}'" : "NULL";
        $diretoria = strlen(trim($dados['diretoria'])) > 0 ? "'{$dados['diretoria']}'" : "NULL";
        $area = strlen(trim($dados['area'])) > 0 ? "'{$dados['area']}'" : "NULL";

        // verifica se centro de custo já existe
        $query = "
            SELECT id
            FROM zcrmportal_art61_areas
            WHERE id <> ".$id_ccusto." 
            AND codcusto = ".$codcusto."
            AND coligada = '".session()->get('func_coligada')."'";
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            return responseJson('error', 'Esse centro de custo já está cadastrado.');
        }
        
        if($id_ccusto == 0) {
            $query = "
                INSERT INTO
                    zcrmportal_art61_areas
                    (codcusto, diretoria, area, dtalt, usualt, coligada)
                VALUES
                    ({$codcusto}, {$diretoria}, {$area}, '".date('Y-m-d H:i:s')."', 
                     '".session()->get('log_id')."', '".session()->get('func_coligada')."')
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

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Centro de Custo gravado com sucesso');
            return responseJson('success', 'Centro de Custo gravado com sucesso.');

        }else{
            return responseJson('error', 'Falha ao gravar Centro de Custo.');
        }
            
    }

    // -------------------------------------------------------
    // Deleta Centro Custo no Art.61
    // -------------------------------------------------------
    public function Deleta_CCusto($dados){
        
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

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Centro de Custo excluído com sucesso.');

        }else{
            return responseJson('error', 'Falha ao excluir Centro de Custo.');
        }
            
    }

    // -------------------------------------------------------
    // Insere ou Altera Prorrogação
    // -------------------------------------------------------
    public function Grava_Prorroga($dados){
        
        $id_prorroga = $dados['id_prorroga'];
        $chapa = strlen(trim($dados['chapa'])) > 0 ? "'{$dados['chapa']}'" : "NULL";
        $dt_extendida = strlen(trim($dados['dt_extendida'])) > 0 ? "'{$dados['dt_extendida']}'" : "NULL";
        
        // verifica se chapa já existe
        $query = "
            SELECT id
            FROM zcrmportal_art61_prorroga
            WHERE id <> ".$id_prorroga." 
            AND chapa = ".$chapa."
            AND coligada = '".session()->get('func_coligada')."'";
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            return responseJson('error', 'Essa chapa já está cadastrada.');
        }
        
        if($id_prorroga == 0) {
            $query = "
                INSERT INTO
                    zcrmportal_art61_prorroga
                    (chapa, dt_extendida, dtalt, usualt, coligada)
                VALUES
                    ({$chapa}, {$dt_extendida}, '".date('Y-m-d H:i:s')."', 
                     '".session()->get('log_id')."', '".session()->get('func_coligada')."')
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

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Prorrogação gravada com sucesso');
            return responseJson('success', 'Prorrogação gravada com sucesso.');

        }else{
            return responseJson('error', 'Falha ao gravar Prorrogação.');
        }
            
    }

    // -------------------------------------------------------
    // Deleta Prorrogação
    // -------------------------------------------------------
    public function Deleta_Prorroga($dados){
        
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

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Prorrogação excluída com sucesso.');

        }else{
            return responseJson('error', 'Falha ao excluir Prorrogação.');
        }
            
    }

    // -------------------------------------------------------
    // Insere ou Altera Exceção
    // -------------------------------------------------------
    public function Grava_Excecao($dados){
        
        $id_excecao = $dados['id_excecao'];
        $chapa_pai = strlen(trim($dados['chapa_pai'])) > 0 ? "'{$dados['chapa_pai']}'" : "NULL";
        $chapa_filho = strlen(trim($dados['chapa_pai'])) > 0 ? "'{$dados['chapa_filho']}'" : "NULL";
        $dt_limite = strlen(trim($dados['dt_limite'])) > 0 ? "'{$dados['dt_limite']}'" : "NULL";
        
        // verifica se excecao já existe
        $query = "
            SELECT id
            FROM zcrmportal_art61_excecao
            WHERE id <> ".$id_excecao." 
            AND chapa_pai = ".$chapa_pai."
            AND chapa_filho = ".$chapa_filho."
            AND coligada = '".session()->get('func_coligada')."'";
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            return responseJson('error', 'Essa exceção já está cadastrada.');
        }
        
        if($id_excecao == 0) {
            $query = "
                INSERT INTO
                    zcrmportal_art61_excecao
                    (chapa_pai, chapa_filho, dt_limite, dtalt, usualt, coligada)
                VALUES
                    ({$chapa_pai}, {$chapa_filho}, {$dt_limite}, '".date('Y-m-d H:i:s')."', 
                     '".session()->get('log_id')."', '".session()->get('func_coligada')."')
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

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Exceção gravada com sucesso');
            return responseJson('success', 'Exceção gravada com sucesso.');

        }else{
            return responseJson('error', 'Falha ao gravar Exceção.');
        }
            
    }

    // -------------------------------------------------------
    // Insere ou Altera Codevento
    // -------------------------------------------------------
    public function Grava_Codevento($dados){
        
        $id_codevento = $dados['id_codevento'];
        $codfilial = strlen(trim($dados['codfilial'])) > 0 ? "{$dados['codfilial']}" : "NULL";
        $de_codevento = strlen(trim($dados['de_codevento'])) > 0 ? "'{$dados['de_codevento']}'" : "NULL";
        $para_codevento = strlen(trim($dados['para_codevento'])) > 0 ? "'{$dados['para_codevento']}'" : "NULL";
        
        // verifica se filial já existe
        $query = "
            SELECT id
            FROM zcrmportal_art61_codevento
            WHERE id <> ".$id_codevento." 
            AND codfilial = ".$codfilial."
            AND de_codevento = ".$de_codevento."
            AND coligada = '".session()->get('func_coligada')."'";
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            return responseJson('error', 'Esse evento já está cadastrado nessa filial.');
        }
        
        if($id_codevento == 0) {
            $query = "
                INSERT INTO
                    zcrmportal_art61_codevento
                    (codfilial, de_codevento, para_codevento, dtalt, usualt, coligada)
                VALUES
                    ({$codfilial}, {$de_codevento}, {$para_codevento}, '".date('Y-m-d H:i:s')."', 
                     '".session()->get('log_id')."', '".session()->get('func_coligada')."')
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

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Evento gravado com sucesso na Filial');
            return responseJson('success', 'Evento gravado com sucesso na Filial.');

        }else{
            return responseJson('error', 'Falha ao gravar Evento na Filial.');
        }
            
    }

    // -------------------------------------------------------
    // Deleta Prorrogação
    // -------------------------------------------------------
    public function Deleta_Excecao($dados){
        
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

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Exceção excluída com sucesso.');

        }else{
            return responseJson('error', 'Falha ao excluir Exceção.');
        }
            
    }

    // -------------------------------------------------------
    // Deleta Evento
    // -------------------------------------------------------
    public function Deleta_Codevento($dados){
        
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

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Evento excluído com sucesso.');

        }else{
            return responseJson('error', 'Falha ao excluir Evento.');
        }
            
    }

    // -------------------------------------------------------
    // Importar Centros de Custos
    // -------------------------------------------------------
	public function Importa_Area($dados){
            $documento = $dados['documento'];
            
			$file_name = $documento['arquivo_importacao']['name'] ?? null;
			$file_type = $documento['arquivo_importacao']['type'] ?? null;
			$file_size = $documento['arquivo_importacao']['size'] ?? null;
            $arquivo = $documento['arquivo_importacao']['tmp_name'] ?? null;
			
			if($arquivo == null) return responseJson('error', 'Arquivo inválido.');
            if($file_name == null) return responseJson('error', 'Nome do arquivo inválido.');
			if($file_type == null) return responseJson('error', 'Tipo do arquivo inválido.');
			if($file_size == null) return responseJson('error', 'Tamanho do arquivo inválido.');
            
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
				return responseJson('error', 'As colunas do arquivo Excel não correspondem às colunas esperadas.<br><br>Esperado: COD_COLIGADA, COD_CCUSTO, NOME_CCUSTO, DIRETORIA, AREA.<br><br>Recebido:'.json_encode($colunasLidas));
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
                            ('{$cod_ccusto}', '{$diretoria}', '{$area}', '".date('Y-m-d H:i:s')."', 
                            '".session()->get('log_id')."', '".session()->get('func_coligada')."')   
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
    
}