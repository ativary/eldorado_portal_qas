<?php
namespace App\Models\Premio;

use CodeIgniter\Model;
use Mpdf\Tag\S;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CadastroModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $now;
    private $coligada;
    private $logId;
    
    public function __construct()
    {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->now      = date('Y-m-d H:i:s');
        $this->coligada = $_SESSION['func_coligada'];
        $this->logId    = $_SESSION['log_id'];

    }

    // -------------------------------------------------------
    // Listar premios
    // -------------------------------------------------------
    public function ListarPremios($id = false){

        $ft_id = ($id) ? " AND id = '{$id}' " : "";

        $query = " SELECT * FROM zcrmportal_premios WHERE status <> 'I' AND id > 0 AND codcoligada = ".$this->coligada." ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }    

    // -------------------------------------------------------
    // Cadastrar  
    // -------------------------------------------------------
    public function CadastrarPremio($dados){

        $nome = strlen(trim($dados['premio_nome'])) > 0 ? "{$dados['premio_nome']}" : "NULL";
        $descricao = strlen(trim($dados['premio_descricao'])) > 0 ? "{$dados['premio_descricao']}" : " ";
        $status = strlen(trim($dados['premio_status'])) > 0 ? "'{$dados['premio_status']}'" : "NULL";
        $dt_vigencia = strlen(trim($dados['premio_vigencia'])) > 0 ? "'{$dados['premio_vigencia']}'" : "NULL";

        if(str_contains($nome, '"') or str_contains($nome, "'")) {
            return responseJson('error', 'Nome contém caracteres inválidos');
        }
        if(str_contains($descricao, '"') or str_contains($descricao, "'")) {
            return responseJson('error', 'Descrição contém caracteres inválidos');
        }
        
        $query = " INSERT INTO zcrmportal_premios
            (nome, descricao, status, dt_vigencia, codcoligada) 
                VALUES
            ('{$nome}', '{$descricao}', {$status}, {$dt_vigencia}, '".$_SESSION['func_coligada']."')    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Prêmio cadastrado com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Prêmio cadastrado com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar novo prêmio');

    }

    // -------------------------------------------------------
    // Editar
    // -------------------------------------------------------
    public function EditarPremio($dados){

        $nome = strlen(trim($dados['premio_nome'])) > 0 ? "{$dados['premio_nome']}" : "NULL";
        $descricao = strlen(trim($dados['premio_descricao'])) > 0 ? "{$dados['premio_descricao']}" : " ";
        $status = strlen(trim($dados['premio_status'])) > 0 ? "'{$dados['premio_status']}'" : "NULL";
        $vigencia = strlen(trim($dados['premio_vigencia'])) > 0 ? "'{$dados['premio_vigencia']}'" : "NULL";

        if(str_contains($nome, '"') or str_contains($nome, "'")) {
            return responseJson('error', 'Nome contém caracteres inválidos');
        }
        if(str_contains($descricao, '"') or str_contains($descricao, "'")) {
            return responseJson('error', 'Descrição contém caracteres inválidos');
        }
        $query = " 
            UPDATE
                zcrmportal_premios
            SET
                nome = '{$nome}', 
                descricao = '{$descricao}', 
                status = {$status}, 
                dt_vigencia = {$vigencia}
            WHERE
                id = {$dados['id']}
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Prêmio alterado com sucesso')
                : responseJson('error', 'Falha ao alterar prêmio');

    }

    // -------------------------------------------------------
    // Deletar
    // -------------------------------------------------------
    public function DeletarPremio($dados = false){

        $query = "UPDATE zcrmportal_premios SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Prêmio excluído com sucesso')
                : responseJson('error', 'Falha ao excluir prêmio');

    }

    // -------------------------------------------------------
    // Deletar todas as premissas de um prêmio
    // -------------------------------------------------------
    public function DeletarPremissasPremio($dados = false){
        $id_premio = $dados['id'];

        // Apaga todas as premissas do premio
        $query = " DELETE FROM zcrmportal_premios_premissas WHERE id_premio = {$id_premio}";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Premissas excluidas')
                : responseJson('error', 'Nenhum premissa foi excluida');

    }

    // -------------------------------------------------------
    // Listar Apuração
    // -------------------------------------------------------
    public function ListarApuracao($id_premio){

        $query = " SELECT * FROM zcrmportal_premios WHERE id = ".$id_premio;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Editar Apuração
    // -------------------------------------------------------
    public function EditarApuracao($dados){

        $base_salario = strlen(trim($dados['base_salario'])) > 0 ? "'{$dados['base_salario']}'" : "NULL";
        $valor_base = strlen(trim($dados['valor_base'])) > 0 ? "'{$dados['valor_base']}'" : "NULL";
        $codevento = strlen(trim($dados['codevento'])) > 0 ? "'{$dados['codevento']}'" : "NULL";
        
        $query = " 
            UPDATE
                zcrmportal_premios
            SET
                base_salario = {$base_salario}, 
                valor_base = {$valor_base}, 
                codevento = {$codevento}
            WHERE
                id = {$dados['id_premio']}
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Apuração de prêmio alterada com sucesso')
                : responseJson('error', 'Falha ao alterar apuração de prêmio');

    }

    // -------------------------------------------------------
    // Listar Deflatores
    // -------------------------------------------------------
    public function ListarDeflatores($id_premio){

        $query = "SELECT * FROM zcrmportal_premios_deflatores WHERE id_premio = ".$id_premio;
        $result = $this->dbportal->query($query);

        if ($result->getNumRows() <= 0) {
            // Cria deflatores para o prêmio
            $query = "INSERT INTO zcrmportal_premios_deflatores (id_premio) VALUES (".$id_premio.")";
            $result = $this->dbportal->query($query);

            $query = "SELECT * FROM zcrmportal_premios_deflatores WHERE id_premio = ".$id_premio;
            $result = $this->dbportal->query($query);
        }
        return $result->getResultArray();
    }

    // -------------------------------------------------------
    // Editar Deflatores
    // -------------------------------------------------------
    public function EditarDeflatores($dados){

        $deflator = [];
        $apenas_dias = [];
        $dias = [];
        $porcent = [];
        for($i=1; $i<=20; $i++) {
            $deflator[$i] = strlen(trim($dados['deflator_'.$i])) > 0 ? "'{$dados['deflator_'.$i]}'" : "NULL";
            $apenas_dias[$i] = strlen(trim($dados['apenas_dias_'.$i])) > 0 ? "'{$dados['apenas_dias_'.$i]}'" : "NULL";
            $dias[$i] = strlen(trim($dados['dias_'.$i])) > 0 ? "'{$dados['dias_'.$i]}'" : "NULL";
            $porcent[$i] = strlen(trim($dados['porcent_'.$i])) > 0 ? "'{$dados['porcent_'.$i]}'" : 0;
        }

        $query = "UPDATE zcrmportal_premios_deflatores SET ";
        for($i=1; $i<=20; $i++) {
            $query = $query." 
                deflator_".$i." = {$deflator[$i]}, 
                apenas_dias_".$i." = {$apenas_dias[$i]}, 
                dias_".$i." = {$dias[$i]}, 
                porcent_".$i." = {$porcent[$i]}
            ";
            if($i<20) {$query = $query.", ";}
        }
        $query = $query." WHERE id_premio = {$dados['id_premio']}";
        //print_r($query);
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Deflatores alterados com sucesso')
                : responseJson('error', 'Falha ao alterar deflatores');

    }

    // -------------------------------------------------------
    // Listar Premissas
    // -------------------------------------------------------
    public function ListarPremissas($id_premio, $id = false){

        $ft_id = ($id) ? " AND z.id = '{$id}' " : "";

        $query = "
            SELECT 
                z.id
                ,z.id_premio
                ,z.codfilial
                ,z.codcusto
                ,z.codfuncao
                ,f.nome AS nome_funcao
                ,z.grupo
                ,z.target
                ,FORMAT(z.target, 'N', 'pt-BR') AS target_br
                ,z.tipo_target
                ,z.status
            FROM zcrmportal_premios_premissas z
            LEFT JOIN ".DBRM_BANCO."..PFUNCAO f ON 
                f.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
                z.codfuncao = f.CODIGO COLLATE Latin1_General_CI_AS 
            WHERE z.status = 'A' AND z.id_premio = ".$id_premio." ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }  

    // -------------------------------------------------------
    // Cadastrar Premissa
    // -------------------------------------------------------
    public function CadastrarPremissa($dados){

        $codfilial = strlen(trim($dados['premissa_codfilial'])) > 0 ? "'{$dados['premissa_codfilial']}'" : 0;
        $codcusto = strlen(trim($dados['premissa_codcusto'])) > 0 ? "'{$dados['premissa_codcusto']}'" : "NULL";
        $codfuncao = strlen(trim($dados['premissa_codfuncao'])) > 0 ? "'{$dados['premissa_codfuncao']}'" : "NULL";
        $grupo = strlen(trim($dados['premissa_grupo'])) > 0 ? "{$dados['premissa_grupo']}" : "NULL";
        $target = strlen(trim($dados['premissa_target'])) > 0 ? "'{$dados['premissa_target']}'" : 0;
        $tipo_target = strlen(trim($dados['premissa_tipo_target'])) > 0 ? "'{$dados['premissa_tipo_target']}'" : "NULL";
        $id_premio = strlen(trim($dados['id_premio'])) > 0 ? "'{$dados['id_premio']}'" : "NULL";
        
        if(str_contains($grupo, '"') or str_contains($grupo, "'")) {
            return responseJson('error', 'Grupo contém caracteres inválidos');
        }

        // verifica se premissa já existe
        $query = "
            SELECT z.id
            FROM zcrmportal_premios_premissas z
            WHERE z.status = 'A' AND z.id_premio = ".$id_premio." 
            AND z.codfilial = ".$codfilial." 
            AND z.codfuncao = ".$codfuncao." 
            AND z.codcusto = ".$codcusto ;
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            return responseJson('error', 'Já existe uma premissa para essa filial, centro de custo e função.');
        }

        $query = " INSERT INTO zcrmportal_premios_premissas
            (codfilial, codcusto, codfuncao, grupo, target, tipo_target, id_premio) 
                VALUES
            ({$codfilial}, {$codcusto}, {$codfuncao}, '{$grupo}', {$target}, {$tipo_target}, {$id_premio})    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Premissa cadastrada com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Premissa cadastrada com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar nova premissa');

    }

    //---------------------------------------------
    // Listar Filial
    //---------------------------------------------
    public function ListarFilial(){

        $where = " AND CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
        $query = "
            SELECT
                CODFILIAL,
                NOMEFANTASIA,
                NOME
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
    // Listar Funcões (RM)
    //---------------------------------------------
    public function ListarFuncao(){

        $where = "A.CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
        $query = "
            SELECT
                A.CODIGO,
                A.NOME
            FROM
                PFUNCAO A
                INNER JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODFUNCAO = A.CODIGO AND B.CODSITUACAO NOT IN ('D')
            WHERE
                {$where}
            GROUP BY
                A.CODIGO,
                A.NOME
            ORDER BY
                A.NOME
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Listar Funcionários (RM)
    //---------------------------------------------
    public function ListarFunc(){

        $where = "A.CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
        $query = "
            SELECT
                A.CHAPA,
                A.NOME
            FROM
                PFUNC A
            WHERE
                A.CODSITUACAO NOT IN ('D') AND 
                {$where}
            ORDER BY
                A.NOME
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Deletar Premissa
    // -------------------------------------------------------
    public function DeletarPremissa($dados = false){

        $query = "UPDATE zcrmportal_premios_premissas SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Premissa excluída com sucesso')
                : responseJson('error', 'Falha ao excluir premissa');

    }

    // -------------------------------------------------------
    // Editar Premissa
    // -------------------------------------------------------
    public function EditarPremissa($dados){

        $codfilial = strlen(trim($dados['premissa_codfilial'])) > 0 ? "'{$dados['premissa_codfilial']}'" : 0;
        $codcusto = strlen(trim($dados['premissa_codcusto'])) > 0 ? "'{$dados['premissa_codcusto']}'" : "NULL";
        $codfuncao = strlen(trim($dados['premissa_codfuncao'])) > 0 ? "'{$dados['premissa_codfuncao']}'" : "NULL";
        $grupo = strlen(trim($dados['premissa_grupo'])) > 0 ? "{$dados['premissa_grupo']}" : "NULL";
        $target = strlen(trim($dados['premissa_target'])) > 0 ? "'{$dados['premissa_target']}'" : 0;
        $tipo_target = strlen(trim($dados['premissa_tipo_target'])) > 0 ? "'{$dados['premissa_tipo_target']}'" : "NULL";
        $id = strlen(trim($dados['id'])) > 0 ? "'{$dados['id']}'" : "NULL";

        if(str_contains($grupo, '"') or str_contains($grupo, "'")) {
            return responseJson('error', 'Grupo contém caracteres inválidos');
        }

        // verifica se premissa já existe
        $query = "
            SELECT z.id
            FROM zcrmportal_premios_premissas z
            WHERE z.status = 'A' 
            AND z.id <> ".$dados['id']." 
            AND z.id_premio = ".$dados['id_premio']." 
            AND z.codfilial = ".$codfilial." 
            AND z.codfuncao = ".$codfuncao." 
            AND z.codcusto = ".$codcusto;
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            return responseJson('error', 'Já existe uma premissa para essa filial, centro de custo e função.');
        }

        $query = " 
            UPDATE
                zcrmportal_premios_premissas
            SET
                codfilial = {$codfilial}, 
                codcusto = {$codcusto}, 
                codfuncao = {$codfuncao}, 
                grupo = '{$grupo}',
                target = {$target}, 
                tipo_target = {$tipo_target}
            WHERE
                id = {$dados['id']}
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Premissa alterada com sucesso')
                : responseJson('error', 'Falha ao alterar premissa');

    }

    // -------------------------------------------------------
    // Importar Premissas
    // -------------------------------------------------------
	public function ImportarPremissas($dados){

		try {

			$documento = $dados['documento'];
            $id_premio = $dados['id_premio'];
            $falha_insere = 0;
            $falha_altera = 0;
			
			$file_name = $documento['arquivo_importacao']['name'] ?? null;
			$file_type = $documento['arquivo_importacao']['type'] ?? null;
			$file_size = $documento['arquivo_importacao']['size'] ?? null;
			
			if($file_name == null) return responseJson('error', 'Nome do arquivo inválido.');
			if($file_type == null) return responseJson('error', 'Tipo do arquivo inválido.');
			if($file_size == null) return responseJson('error', 'Tamanho do arquivo inválido.');

            $arquivo = $documento['arquivo_importacao']['tmp_name'];
            $spreadsheet = IOFactory::load($arquivo);

            // Pega a primeira planilha
            $worksheet = $spreadsheet->getActiveSheet();

            // Le os dados da planilha como array
            $data = $worksheet->toArray();

            // Define as colunas esperadas
            $colunasEsperadas = ['COD_FILIAL', 'COD_CCUSTO', 'COD_FUNCAO', 'DESC_FUNCAO', 'GRUPO', 'PORC_TARGET', 'TIPO_TARGET'];

            // Valida o nome das colunas
            $colunasLidas = $data[0]; // A primeira linha contém o nome das colunas
            if ($colunasEsperadas !== $colunasLidas) {
				return responseJson('error', 'As colunas do arquivo Excel não correspondem às colunas esperadas.<br><br>Esperado: COD_FILIAL, COD_CCUSTO, COD_FUNCAO, DESC_FUNCAO, GRUPO, PORC_TARGET e TIPO_TARGET.<br><br>Recebido:'.json_encode($colunasLidas));
			}

            // Loop para ler e gravar dados novos
            for ($i = 1; $i < count($data); $i++) { 
                $cod_filial = $data[$i][0];
                $cod_ccusto = $data[$i][1];
                $cod_funcao = $data[$i][2];
                $grupo = $data[$i][4];
                $porc_target = $data[$i][5];
                $tipo_target = $data[$i][6];

                if ($cod_filial != '' and $cod_ccusto != '') {
                    $grupo = mb_substr($grupo,0,100,'UTF-8');
                    if (!is_numeric($porc_target)) {
                        return responseJson('error', 'A importação foi interrompida pois a porcentagem de target não é numerica. Linha: '.$i);
                    }
        
                    // FAZER validar grupo, porc_target e tipo_target

                    // verifica se filial + ccusto + funcao já existem nas premissas do premio
                    $where = "id_premio = {$id_premio} AND codfilial = {$cod_filial} AND codcusto = '{$cod_ccusto}' AND codfuncao = '{$cod_funcao}' AND status = 'A'";
                    $query = "SELECT id FROM zcrmportal_premios_premissas WHERE {$where}";
                    $result = $this->dbportal->query($query);
                    if ($result->getNumRows() > 0) {
                        // Atualiza dados
                        $query = " 
                            UPDATE
                                zcrmportal_premios_premissas
                            SET
                                grupo = '{$grupo}',
                                target = {$porc_target}, 
                                tipo_target = '{$tipo_target}'
                            WHERE
                                {$where}
                        ";
                        //echo '<pre> '.$query;
                        //exit();
                        $this->dbportal->query($query);
                
                        if ($this->dbportal->affectedRows() <= 0) {
                            $falha_altera++;
                        }

                    } else {
                        // Insere dados
                        $query = " 
                            INSERT INTO zcrmportal_premios_premissas 
                                (codfilial, codcusto, codfuncao, grupo, target, tipo_target, id_premio)
                            VALUES 
                                ({$cod_filial}, '{$cod_ccusto}', '{$cod_funcao}', '{$grupo}', {$porc_target}, '{$tipo_target}', {$id_premio})    
                        ";
                        //echo '<pre> '.$query;
                        //exit();
                        $this->dbportal->query($query);

                        if ($this->dbportal->affectedRows() <= 0) {
                            $falha_insere++;
                        }
                    }
                }
            }

			notificacao('success', 'Importação concluída com sucesso');
			return responseJson('success', 'Importação concluída com sucesso.');

        } catch (\Exception | \Error $e) {

			// grava log de erro
			/*try {
				$query = $this->dbportal->query(" INSERT INTO zcrmportal_ocorrencia_importacao_log 
				(
					arquivo,
					datacadastro,
					erro
				) VALUES (
					'".base64_encode(file_get_contents($documento['arquivo_importacao']['tmp_name']))."', 
					'{$this->now}', 
					'".$this->dbportal->escapeString($e->getMessage())."'
					)
				");
			} catch (\Exception | \Error $e) {
				return responseJson('error', '<b>Erro interno:</b><br><code class="language-markup">'.$e->getMessage().'</code>');
			}
			*/
			return responseJson('error', 'Erro interno: '.$e->getMessage());
			
		}

	}

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

    // -------------------------------------------------------
    // Listar Acessos
    // -------------------------------------------------------
    public function ListarAcessos($id_premio, $id = false){

        $ft_id = ($id) ? " AND z.id = '{$id}' " : "";

        $query = "
            SELECT 
                id,
                id_premio,
                dtini_req,
                dtfim_req,
                dtini_ponto,
                dtfim_ponto,
                FORMAT(dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_acessos z WHERE z.status = 'A' AND z.id_premio = ".$id_premio." ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    } 

    // -------------------------------------------------------
    // Listar Acessos de Exceção
    // -------------------------------------------------------
    public function ListarAcessosExcecao($id_premio, $id = false){

        $ft_id = ($id) ? " AND z.id = '{$id}' " : "";

        $query = "
            SELECT 
                id,
                id_premio,
                dtini_req,
                dtfim_req,
                dtini_ponto,
                dtfim_ponto,
                FORMAT(dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_excecao z WHERE z.status = 'A' AND z.id_premio = ".$id_premio." ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    } 

    // -------------------------------------------------------
    // Listar Acesso por ID de Acesso
    // -------------------------------------------------------
    public function ListarAcesso($id){

        $query = "
            SELECT 
                z.id,
                z.id_premio,
                p.nome AS nome_premio,
                z.dtini_req,
                z.dtfim_req,
                z.dtini_ponto,
                z.dtfim_ponto,
                FORMAT(z.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(z.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(z.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(z.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(z.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(z.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(z.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(z.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_acessos z 
            LEFT JOIN zcrmportal_premios p ON p.id = z.id_premio 
            WHERE z.status = 'A' AND z.id = ".$id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    } 

    // -------------------------------------------------------
    // Listar Acesso por ID de Exceção de Acesso
    // -------------------------------------------------------
    public function ListarAcessoExcecao($id){

        $query = "
            SELECT 
                z.id,
                z.id_premio,
                p.nome AS nome_premio,
                z.dtini_req,
                z.dtfim_req,
                z.dtini_ponto,
                z.dtfim_ponto,
                FORMAT(z.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(z.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(z.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(z.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(z.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(z.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(z.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(z.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_excecao z 
            LEFT JOIN zcrmportal_premios p ON p.id = z.id_premio 
            WHERE z.status = 'A' AND z.id = ".$id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    } 

    // -------------------------------------------------------
    // Listar Usuários por ID de Acesso
    // -------------------------------------------------------
    public function ListarAcessoUsuarios($id){

        $query = "
            SELECT 
                z.id,
                z.id_acesso,
                z.chapa,
                f.nome AS nome_func
            FROM zcrmportal_premios_acessos_usuarios z 
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON 
                f.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
                z.chapa = f.CHAPA COLLATE Latin1_General_CI_AS 
            WHERE z.status = 'A' AND z.id_acesso = ".$id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    } 

    // -------------------------------------------------------
    // Listar Usuários por ID de Exceção de Acesso
    // -------------------------------------------------------
    public function ListarAcessoExcecaoUsuarios($id){

        $query = "
            SELECT 
                z.id,
                z.id_acesso,
                z.chapa,
                f.nome AS nome_func
            FROM zcrmportal_premios_excecao_usuarios z 
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON 
                f.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
                z.chapa = f.CHAPA COLLATE Latin1_General_CI_AS 
            WHERE z.status = 'A' AND z.id_acesso = ".$id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    } 

    // -------------------------------------------------------
    // Listar períodos de ponto por ID de prêmio para importar usuarios
    // -------------------------------------------------------
    public function ListarPontoUsuarios($id_premio){

        $query = "
            SELECT distinct 
	            a.id AS id_acesso,
	            FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_acessos_usuarios u 
            left join zcrmportal_premios_acessos a
	            ON u.id_acesso = a.id 
                WHERE u.status = 'A' and a.status = 'A' and a.id_premio = ".$id_premio;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // ---------------------------------------------------------------------------
    // Listar períodos de ponto por ID de prêmio para importar usuarios de exceção
    // ---------------------------------------------------------------------------
    public function ListarPontoExcecaoUsuarios($id_premio){

        $query = "
            SELECT distinct 
	            a.id AS id_acesso,
	            FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_excecao_usuarios u 
            left join zcrmportal_premios_excecao a
	            ON u.id_acesso = a.id 
                WHERE u.status = 'A' and a.status = 'A' and a.id_premio = ".$id_premio;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Deletar Acesso
    // -------------------------------------------------------
    public function DeletarAcesso($dados = false){

        $query = "UPDATE zcrmportal_premios_acessos SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Acesso excluído com sucesso')
                : responseJson('error', 'Falha ao excluir acesso');

    }

    // -------------------------------------------------------
    // Deletar Exceção de Acesso
    // -------------------------------------------------------
    public function DeletarAcessoExcecao($dados = false){

        $query = "UPDATE zcrmportal_premios_excecao SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Exceção de acesso excluída com sucesso')
                : responseJson('error', 'Falha ao excluir exceção de acesso');

    }

    // -------------------------------------------------------
    // Cadastrar Acesso
    // -------------------------------------------------------
    public function CadastrarAcesso($dados){

        $dtini_req = strlen(trim($dados['dtini_req'])) > 0 ? "'{$dados['dtini_req']}'" : "NULL";
        $dtfim_req = strlen(trim($dados['dtfim_req'])) > 0 ? "'{$dados['dtfim_req']}'" : "NULL";
        $dtini_ponto = "NULL";
        $dtfim_ponto = "NULL";
        $id_premio = strlen(trim($dados['id_premio'])) > 0 ? "'{$dados['id_premio']}'" : "NULL";
        
        if(strlen(trim($dados['dtper_ponto'])) > 0) {
            $dtini_ponto = substr(trim($dados['dtper_ponto']),0,10);
            $dtfim_ponto = substr(trim($dados['dtper_ponto']),11,10);
        }
        
        $query = " INSERT INTO zcrmportal_premios_acessos
            (dtini_req, dtfim_req, dtini_ponto, dtfim_ponto, id_premio) 
                VALUES
            ({$dtini_req}, {$dtfim_req}, '{$dtini_ponto}', '{$dtfim_ponto}', {$id_premio})    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Acesso cadastrado com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Acesso cadastrado com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar novo acesso');

    }

    // -------------------------------------------------------
    // Cadastrar Excecao de Acesso
    // -------------------------------------------------------
    public function CadastrarAcessoExcecao($dados){

        $dtini_req = strlen(trim($dados['dtini_req'])) > 0 ? "'{$dados['dtini_req']}'" : "NULL";
        $dtfim_req = strlen(trim($dados['dtfim_req'])) > 0 ? "'{$dados['dtfim_req']}'" : "NULL";
        $dtini_ponto = "NULL";
        $dtfim_ponto = "NULL";
        $id_premio = strlen(trim($dados['id_premio'])) > 0 ? "'{$dados['id_premio']}'" : "NULL";
        
        if(strlen(trim($dados['dtper_ponto'])) > 0) {
            $dtini_ponto = substr(trim($dados['dtper_ponto']),0,10);
            $dtfim_ponto = substr(trim($dados['dtper_ponto']),11,10);
        }
        
        $query = " INSERT INTO zcrmportal_premios_excecao
            (dtini_req, dtfim_req, dtini_ponto, dtfim_ponto, id_premio) 
                VALUES
            ({$dtini_req}, {$dtfim_req}, '{$dtini_ponto}', '{$dtfim_ponto}', {$id_premio})    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Exceção de acesso cadastrada com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Exceção de acesso cadastrada com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar nova exceção de acesso');

    }

    // -------------------------------------------------------
    // Editar Acesso
    // -------------------------------------------------------
    public function EditarAcesso($dados){

        $dtini_req = strlen(trim($dados['dtini_req'])) > 0 ? "'{$dados['dtini_req']}'" : "NULL";
        $dtfim_req = strlen(trim($dados['dtfim_req'])) > 0 ? "'{$dados['dtfim_req']}'" : "NULL";
        $dtini_ponto = "NULL";
        $dtfim_ponto = "NULL";
        if(strlen(trim($dados['dtper_ponto'])) > 0) {
            $dtini_ponto = substr(trim($dados['dtper_ponto']),0,10);
            $dtfim_ponto = substr(trim($dados['dtper_ponto']),11,10);
        }
        $id = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : 0;

        $query = " 
            UPDATE
                zcrmportal_premios_acessos
            SET
                dtini_req = {$dtini_req}, 
                dtfim_req = {$dtfim_req}, 
                dtini_ponto = '{$dtini_ponto}', 
                dtfim_ponto = '{$dtfim_ponto}'
            WHERE
                id = {$id}
        ";
        //return($query);
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Acesso alterado com sucesso')
                : responseJson('error', 'Falha ao alterar acesso');

    }

    // -------------------------------------------------------
    // Editar Exceção de Acesso
    // -------------------------------------------------------
    public function EditarAcessoExcecao($dados){

        $dtini_req = strlen(trim($dados['dtini_req'])) > 0 ? "'{$dados['dtini_req']}'" : "NULL";
        $dtfim_req = strlen(trim($dados['dtfim_req'])) > 0 ? "'{$dados['dtfim_req']}'" : "NULL";
        $dtini_ponto = "NULL";
        $dtfim_ponto = "NULL";
        if(strlen(trim($dados['dtper_ponto'])) > 0) {
            $dtini_ponto = substr(trim($dados['dtper_ponto']),0,10);
            $dtfim_ponto = substr(trim($dados['dtper_ponto']),11,10);
        }
        $id = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : 0;

        $query = " 
            UPDATE
                zcrmportal_premios_excecao
            SET
                dtini_req = {$dtini_req}, 
                dtfim_req = {$dtfim_req}, 
                dtini_ponto = '{$dtini_ponto}', 
                dtfim_ponto = '{$dtfim_ponto}'
            WHERE
                id = {$id}
        ";
        //return($query);
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Exceção de acesso alterada com sucesso')
                : responseJson('error', 'Falha ao alterar acesso');

    }

    // -------------------------------------------------------
    // Adicionar usuário ao Acesso
    // -------------------------------------------------------
    public function AdicionarUsuario($dados){

        $chapa = strlen(trim($dados['chapa'])) > 0 ? "'{$dados['chapa']}'" : "NULL";
        $id_acesso = strlen(trim($dados['id_acesso'])) > 0 ? "{$dados['id_acesso']}" : 0;
        
        $query = " INSERT INTO zcrmportal_premios_acessos_usuarios
            (chapa, id_acesso) 
                VALUES
            ({$chapa}, {$id_acesso})    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Acesso do usuário cadastrado com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Acesso do usuário cadastrado com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar acessos do usuário');

    }

    // -------------------------------------------------------
    // Adicionar usuário ao Acesso Exceção
    // -------------------------------------------------------
    public function AdicionarUsuarioExcecao($dados){

        $chapa = strlen(trim($dados['chapa'])) > 0 ? "'{$dados['chapa']}'" : "NULL";
        $id_acesso = strlen(trim($dados['id_acesso'])) > 0 ? "{$dados['id_acesso']}" : 0;
        
        $query = " INSERT INTO zcrmportal_premios_excecao_usuarios
            (chapa, id_acesso) 
                VALUES
            ({$chapa}, {$id_acesso})    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Exceção de acesso do usuário cadastrada com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Exceção de acesso do usuário cadastrada com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar exceção de acesso do usuário');

    }

    // -------------------------------------------------------
    // Importar usuários de outro ao Acesso
    // -------------------------------------------------------
    public function ImportarUsuarios($dados){

        $id_acesso_imp = strlen(trim($dados['id_acesso_imp'])) > 0 ? "{$dados['id_acesso_imp']}" : 0;
        $id_acesso = strlen(trim($dados['id_acesso'])) > 0 ? "{$dados['id_acesso']}" : 0;
        $sucesso = true;

        $chapas = "
            SELECT 
                a.chapa 
            FROM zcrmportal_premios_acessos_usuarios a 
            WHERE
                chapa NOT IN (
                    SELECT b.chapa FROM zcrmportal_premios_acessos_usuarios b 
                    WHERE b.status = 'A' AND b.id_acesso = ". $id_acesso." 
                ) 
                AND a.status = 'A' AND a.id_acesso = ". $id_acesso_imp;

        $qry = $this->dbportal->query($chapas);
		$resChapa = ($qry) ? $qry->getResultArray() : false;
		if ($resChapa && is_array($resChapa)) {
            foreach ($resChapa as $idc => $value) {
                $chapa = $resChapa[$idc]['chapa'];

                $query = " INSERT INTO zcrmportal_premios_acessos_usuarios
                    (chapa, id_acesso) 
                        VALUES
                    ('{$chapa}', {$id_acesso})    
                ";
                $this->dbportal->query($query);
                if($this->dbportal->affectedRows() <= 0) { $sucesso = false; }
            }
        }

        if($sucesso) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Importação finalizada.')));

        return ($sucesso) 
                ? responseJson('success', 'Importação finalizada', $this->dbportal->insertID())
                : responseJson('error', 'Um ou mais usuários podem não ter sido importados.');

    }

    // -------------------------------------------------------
    // Importar usuários de outra Exceção de Acesso
    // -------------------------------------------------------
    public function ImportarUsuariosExcecao($dados){

        $id_acesso_imp = strlen(trim($dados['id_acesso_imp'])) > 0 ? "{$dados['id_acesso_imp']}" : 0;
        $id_acesso = strlen(trim($dados['id_acesso'])) > 0 ? "{$dados['id_acesso']}" : 0;
        $sucesso = true;

        $chapas = "
            SELECT 
                a.chapa 
            FROM zcrmportal_premios_excecao_usuarios a 
            WHERE
                chapa NOT IN (
                    SELECT b.chapa FROM zcrmportal_premios_excecao_usuarios b 
                    WHERE b.status = 'A' AND b.id_acesso = ". $id_acesso." 
                ) 
                AND a.status = 'A' AND a.id_acesso = ". $id_acesso_imp;

        $qry = $this->dbportal->query($chapas);
		$resChapa = ($qry) ? $qry->getResultArray() : false;
		if ($resChapa && is_array($resChapa)) {
            foreach ($resChapa as $idc => $value) {
                $chapa = $resChapa[$idc]['chapa'];

                $query = " INSERT INTO zcrmportal_premios_excecao_usuarios
                    (chapa, id_acesso) 
                        VALUES
                    ('{$chapa}', {$id_acesso})    
                ";
                $this->dbportal->query($query);
                if($this->dbportal->affectedRows() <= 0) { $sucesso = false; }
            }
        }

        if($sucesso) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Importação finalizada.')));

        return ($sucesso) 
                ? responseJson('success', 'Importação finalizada', $this->dbportal->insertID())
                : responseJson('error', 'Um ou mais usuários podem não ter sido importados.');

    }

    // -------------------------------------------------------
    // Deletar Usuário
    // -------------------------------------------------------
    public function DeletarUsuario($dados = false){

        $query = "UPDATE zcrmportal_premios_acessos_usuarios SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Acesso do colaborador removido com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Acesso do colaborador excluído com sucesso')
                : responseJson('error', 'Falha ao excluir acesso do colaborador');

    }

    // -------------------------------------------------------
    // Deletar Usuário
    // -------------------------------------------------------
    public function DeletarUsuarioExcecao($dados = false){

        $query = "UPDATE zcrmportal_premios_excecao_usuarios SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Exceção de acesso do colaborador excluída com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Exceção de acesso do colaborador excluída com sucesso')
                : responseJson('error', 'Falha ao excluir exceção de acesso do colaborador');

    }

}