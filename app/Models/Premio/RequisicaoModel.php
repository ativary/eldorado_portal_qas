<?php
namespace App\Models\Premio;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;
use Mpdf\Tag\S;
use PhpOffice\PhpSpreadsheet\IOFactory;
use \DateTime;

class RequisicaoModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $now;
    private $coligada;
    private $chapa;
    private $logId;
    
    public function __construct()
    {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->now      = date('Y-m-d H:i:s');
        $_SESSION['func_coligada'] = isset($_SESSION['func_coligada']) ? $_SESSION['func_coligada'] : NULL;
        $_SESSION['func_chapa'] = isset($_SESSION['func_chapa']) ? $_SESSION['func_chapa'] : '';
        $_SESSION['log_id'] = isset($_SESSION['log_id']) ? $_SESSION['log_id'] : 0;
        $this->coligada = $_SESSION['func_coligada'];
        $this->chapa    = $_SESSION['func_chapa'];
        $this->logId    = $_SESSION['log_id'];
        
    }

    // -------------------------------------------------------
    // Listar Requisicao
    // -------------------------------------------------------
    public function ListarRequisicao($id = false){

        $mHierarquia = Model('HierarquiaModel');

        $ft_id = ($id) ? " AND e.id = '{$id}' " : "";
        $user_id = "";

        // Filtra por chapa ou admin apenas se $id false
        if($ft_id=="") {
            $chapa = "'". util_chapa(session()->get('func_chapa'))['CHAPA'] . "'" ?? null;
            $coligada = $_SESSION['func_coligada'];

            if($chapa){

            
                $chapasGestorSubstituto = $mHierarquia->getChapasGestorSubstituto($chapa);

                if($chapasGestorSubstituto){
                    foreach($chapasGestorSubstituto as $idx  => $value){
                        $chapa .= " , '" . $chapasGestorSubstituto[$idx]['chapa_gestor'] . "' ";
                    }
                }
            }

            /*$user_id = ($_SESSION['log_id']) != 1 ? " AND e.chapa_requisitor = '".$chapa."' AND e.id_coligada = ".$coligada : "";*/
            //$user_id = ($_SESSION['log_id']) != 1 ? " AND e.chapa_requisitor = '".$chapa."'" : "";
            $user_id = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? "" : " AND e.chapa_requisitor in (".$chapa.") AND e.id_coligada = ".$coligada;
            $user_id = $user_id." AND e.id_coligada = ".$coligada;
        }

        

        $query = " 
            WITH ZEROS AS (
                select distinct id_requisicao, 1 as tem_zeros from zcrmportal_premios_requisicao_chapas 
                where status <> 'I' and realizado is null or realizado = 0
            ), 
            APROV AS (
                select id_requisicao, min(status) as status from zcrmportal_premios_requisicao_aprovacao
                where status <> 'I'
                group by id_requisicao
            )
            SELECT 
	            e.id,
	            e.dt_requisicao,
                FORMAT(e.dt_requisicao, 'dd/MM/yyyy') as dt_requisicao_br,
	            e.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
	            e.chapa_requisitor,
	            f.nome AS nome_requisitor,
	            isnull(v.status, e.status) as status,
	            e.id_requisitor,
                a.dtini_ponto,
                a.dtfim_ponto,
                a.dtini_req,
                a.dtfim_req,
                e.tipo,
                e.chapa_gerente,
                g.nome AS nome_gerente,
                CASE WHEN e.tipo = 'M' THEN 'MENSAL'
                     ELSE 'COMPLEMENTAR'
                END tipo_requisicao,
                FORMAT(a.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(a.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(a.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br,
				ISNULL(z.tem_zeros,0) AS tem_zeros
            FROM zcrmportal_premios_requisicao e
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON 
                f.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            e.chapa_requisitor = f.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC g ON 
                g.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            e.chapa_gerente = g.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN zcrmportal_premios_acessos a ON
	            a.id = e.id_acesso
            LEFT JOIN zcrmportal_premios p ON
	            p.id = a.id_premio
            LEFT JOIN ZEROS z ON
	            z.id_requisicao = e.id
            LEFT JOIN APROV v ON
	            v.id_requisicao = e.id
            WHERE e.status <> 'I' AND e.id > 0 ".$ft_id.$user_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    }    

    // -------------------------------------------------------
    // Listar Chapas da Requisicao
    // -------------------------------------------------------
    public function ListarChapasRequisicao($id, $chapa_coordenador = false){

        $ft_id = ($id) ? " AND c.id_requisicao = '{$id}' " : "";
        $ft_coord = ($chapa_coordenador) ? " AND c.chapa_coordenador = '{$chapa_coordenador}' " : "";
        $query = " 
            SELECT 
	            c.id,
	            c.id_requisicao,
                c.tipo,
                c.obs,
                c.chapa AS func_chapa,
                c.chapa_coordenador AS coord_chapa,
                n.nome AS coord_nome,
                c.dias_faltas,
                c.dias_ferias,
                c.dias_afast,
                c.dias_atestado,
                c.dias_admissao,
                c.dias_demissao,
                c.defla_faltas + c.defla_ferias + c.defla_afast + c.defla_atestado + c.defla_admissao + 
                c.defla_demissao AS tot_deflator,
                isnull(c.dias_faltas,0) + isnull(c.dias_ferias,0) + isnull(c.dias_afast,0) + isnull(c.dias_atestado,0) + isnull(c.dias_admissao,0) + isnull(c.dias_demissao,0) AS dias_defla,
                isnull(c.resultado,0) as resultado,
                FORMAT(isnull(c.resultado,0), 'N', 'pt-BR') AS resultado_br,
                c.valor_base,
                isnull(c.valor_premio,0) as valor_premio,
	            FORMAT(isnull(c.valor_premio,0), 'N', 'pt-BR') AS valor_premio_br,
                f.nome AS func_nome,
                u.nome AS funcao,
                f.codsituacao,
                i.descricao AS situacao,
                f.dataadmissao AS dt_admissao,
                FORMAT(f.dataadmissao, 'dd/MM/yyyy') as dt_admissao_br,
                f.codfilial,
                l.nomefantasia AS filial,
                f.codsecao,
                s.descricao AS secao,
                s.nrocencustocont AS codcusto,
	            c.id_coligada,
	            c.target,
                FORMAT(c.target, 'N', 'pt-BR') AS target_br,
                c.realizado,
                FORMAT(c.realizado, 'N', 'pt-BR') AS realizado_br,
                c.id_requisicao,
                r.id_acesso,
                a.id_premio
            FROM zcrmportal_premios_requisicao_chapas c
            LEFT JOIN ".DBRM_BANCO."..PFUNC n ON 
                n.codcoligada = c.id_coligada AND 
	            c.chapa_coordenador = n.chapa COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON 
                f.codcoligada = c.id_coligada AND 
	            c.chapa = f.chapa COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNCAO u ON 
                u.codcoligada = c.id_coligada AND 
	            u.codigo = f.codfuncao COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PCODSITUACAO i ON 
                i.codcliente = f.codsituacao COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PSECAO s ON 
                s.codcoligada = c.id_coligada AND 
	            s.codigo = f.codsecao COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..GFILIAL l ON 
                l.codcoligada = c.id_coligada AND 
	            l.codfilial = f.codfilial 
            LEFT JOIN zcrmportal_premios_requisicao r ON
	            r.id = c.id_requisicao
            LEFT JOIN zcrmportal_premios_acessos a ON
	            a.id = r.id_acesso
            LEFT JOIN zcrmportal_premios p ON
	            p.id = a.id_premio
            WHERE c.status <> 'I' AND c.id > 0 ".$ft_id.$ft_coord;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    }    

    // -------------------------------------------------------
    // Listar uma Chapa da Requisicao
    // -------------------------------------------------------
    public function ListarChapaRequisicao($id){

        $ft_id = ($id) ? " AND c.id = '{$id}' " : "";
        $query = " 
            SELECT 
	            c.id,
	            c.id_requisicao,
                c.tipo,
                c.chapa AS func_chapa,
	            c.chapa_coordenador AS coord_chapa,
                n.nome AS coord_nome,
                f.nome AS func_nome,
                u.nome AS funcao,
                f.codsituacao,
                i.descricao AS situacao,
                f.dataadmissao AS dt_admissao,
                FORMAT(f.dataadmissao, 'dd/MM/yyyy') as dt_admissao_br,
                f.codfilial,
                l.nomefantasia AS filial,
                f.codsecao,
                s.descricao AS secao,
                s.nrocencustocont AS codcusto,
	            c.id_coligada,
	            c.target,
                FORMAT(c.target, 'N', 'pt-BR') AS target_br,
                c.realizado,
                FORMAT(c.realizado, 'N', 'pt-BR') AS realizado_br,
                c.id_requisicao,
                r.id_acesso,
                a.id_premio
            FROM zcrmportal_premios_requisicao_chapas c
            LEFT JOIN ".DBRM_BANCO."..PFUNC n ON 
                n.codcoligada = c.id_coligada AND 
	            c.chapa_coordenador = n.chapa COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON 
                f.codcoligada = c.id_coligada AND 
	            c.chapa = f.chapa COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNCAO u ON 
                u.codcoligada = c.id_coligada AND 
	            u.codigo = f.codfuncao COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PCODSITUACAO i ON 
                i.codcliente = f.codsituacao COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PSECAO s ON 
                s.codcoligada = c.id_coligada AND 
	            s.codigo = f.codsecao COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..GFILIAL l ON 
                l.codcoligada = c.id_coligada AND 
	            l.codfilial = f.codfilial 
            LEFT JOIN zcrmportal_premios_requisicao r ON
	            r.id = c.id_requisicao
            LEFT JOIN zcrmportal_premios_acessos a ON
	            a.id = r.id_acesso
            LEFT JOIN zcrmportal_premios p ON
	            p.id = a.id_premio
            WHERE c.status <> 'I' AND c.id > 0 ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    }    

    // -------------------------------------------------------
    // Cadastrar Requisicao
    // -------------------------------------------------------
    public function CadastrarRequisicao($dados){

        $id_acesso = strlen(trim($dados['id_acesso'])) > 0 ? "{$dados['id_acesso']}" : "NULL";
        $dt_requisicao = strlen(trim($dados['dt_requisicao'])) > 0 ? "{$dados['dt_requisicao']}" : "NULL";
        $tipo = strlen(trim($dados['tipo'])) > 0 ? "{$dados['tipo']}" : "NULL";
        $chapa_requisitor = strlen(trim($dados['chapa_requisitor'])) > 0 ? "{$dados['chapa_requisitor']}" : "NULL";

        // verifica se já existe uma requisição ativa com o mesmos parâmetros
        $where = "id_acesso = {$id_acesso} AND tipo = '{$tipo}' AND chapa_requisitor = '{$chapa_requisitor}' AND status <> 'I'";
        $query = "SELECT id FROM zcrmportal_premios_requisicao WHERE {$where}";
        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            return responseJson('error', 'Já existe requisição com os mesmos parâmetros');
        }

        // busca gerente da chapa
        $chapa_gerente = $this->GerenteChapa($chapa_requisitor);
        if (!$chapa_gerente) {
            return responseJson('error', 'Não foi possível identificar o gestor dessa chapa. Nova requisição não pode ser criada.');
        }

        // insere a requisição
        $query = " INSERT INTO zcrmportal_premios_requisicao
            (id_acesso, dt_requisicao, tipo, chapa_requisitor, chapa_gerente, id_requisitor, id_coligada) 
                VALUES
            ({$id_acesso}, '{$dt_requisicao}', '{$tipo}','{$chapa_requisitor}','{$chapa_gerente}', {$_SESSION['log_id']}, {$_SESSION['func_coligada']})    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) {
            $id_req = $this->dbportal->insertID();
            session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Requisição de prêmio criada com sucesso.')));
            return responseJson('success', 'Requisição de prêmio criada com sucesso', $id_req);
        } else {
            return responseJson('error', 'Falha ao criar requisição de prêmio');
        }
              
    }

    // -------------------------------------------------------
    // Deletar Requisição
    // -------------------------------------------------------
    public function DeletarRequisicao($dados = false){

        $query = "UPDATE zcrmportal_premios_requisicao SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Requisição de prêmio excluída com sucesso')
                : responseJson('error', 'Falha ao excluir requisição de prêmio');

    }

    // -------------------------------------------------------
    // Deletar Chapa da Requisição
    // -------------------------------------------------------
    public function DeletarChapaRequisicao($dados = false){

        $query = "UPDATE zcrmportal_premios_requisicao_chapas SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Chapa da requisição excluída com sucesso')
                : responseJson('error', 'Falha ao excluir chapa da requisição de prêmio');

    }

    // -------------------------------------------------------
    // Editar Chapa da Requisição
    // -------------------------------------------------------
    public function EditarChapaRequisicao($dados = false){

        $query = "
            UPDATE zcrmportal_premios_requisicao_chapas 
            SET 
                chapa_coordenador = '{$dados['chapa_coordenador']}', 
                target = {$dados['target']},
                realizado = {$dados['realizado']} 
            WHERE id = '{$dados['id_req_chapa']}' 
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Chapa da requisição editada com sucesso')
                : responseJson('error', 'Falha ao editar chapa da requisição de prêmio');

    }

    //---------------------------------------------------------------------------------
    // Verifica se funcionário já está em alguma requisição de prêmio/ponto (id_acesso)
    //---------------------------------------------------------------------------------
    public function ExisteFuncAcesso($chapa, $id_coligada, $id_requisicao, $tipo_req){

        $q = $this->dbportal->query("SELECT id_acesso from zcrmportal_premios_requisicao WHERE id = ".$id_requisicao );
        $row = $q->getRow();
        $id_acesso = $row->id_acesso;

        $query = "
            SELECT 
                c.id_requisicao,
                c.chapa,
                c.id_coligada,
                r.id_acesso
            FROM zcrmportal_premios_requisicao_chapas c
            INNER JOIN zcrmportal_premios_requisicao r ON
                r.id = c.id_requisicao
            WHERE 
                c.status <> 'I' AND
                c.chapa = '".$chapa."' AND
                c.id_coligada = ".$id_coligada." AND
                r.id_acesso = ".$id_acesso." AND r.status <> 'I' AND 
                r.tipo = '".$tipo_req."' 
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? true 
                : false;

    }

    // -------------------------------------------------------
    // Nova Chapa da Requisição
    // -------------------------------------------------------
    public function NovaChapaRequisicao($dados = false){

        $chapa = $dados['chapa_colaborador'];
        $chapa_coordenador = $dados['chapa_coordenador'];
        $obs = $dados['obs'];
        $target = $dados['target'];
        $realizado = $dados['realizado'];
        $id_requisicao = $dados['id_requisicao'];
        $tipo_req = $dados['tipo_req'];
        $id_usuario = $_SESSION['log_id'];
        $id_coligada = $_SESSION['func_coligada'];

        if($this->ExisteFuncAcesso($chapa, $id_coligada, $id_requisicao, $tipo_req)) {
            return responseJson('error', 'Chapa já existe em uma requisição de prêmio no mesmo período de ponto e tipo.');
        }

        $query = " INSERT INTO zcrmportal_premios_requisicao_chapas
                    (chapa, chapa_coordenador, tipo, obs, target, realizado, id_coligada, id_requisicao, id_usuario) 
                        VALUES
                    ('{$chapa}', '{$chapa_coordenador}', 'E', '{$obs}', {$target}, {$realizado}, {$id_coligada}, {$id_requisicao}, {$id_usuario})    
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Chapa inserida na requisição com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao inserir chapa na requisição de prêmio');

    }

    // -------------------------------------------------------
    // Aprovar Requisição
    // -------------------------------------------------------
    public function AprovarRequisicao($dados = false){

        $id_aprova = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        if(substr($id_aprova, -1)==",") { $id_aprova = substr($id_aprova, 0, -1); }
        $func_chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? "";
        $func_chapa = trim($func_chapa) == '' ? 'RH' : $func_chapa;
        $log_id = $_SESSION['log_id'];
        $query = "
            UPDATE zcrmportal_premios_requisicao_aprovacao
            SET 
                status = 'A',
                dt_aprovacao = '".date('Y-m-d')."', 
                id_aprovador = ".$log_id.",
                chapa_aprov_reprov = '".$func_chapa."'  
            WHERE id IN ({$id_aprova})";
        $this->dbportal->query($query);

        //echo '<PRE> '.$query;
        //exit();

        if (str_contains($id_aprova, ',')) {
            $msg = 'Requisições de prêmio aprovadas com sucesso';
        } else {
            $msg = 'Requisição de prêmio aprovada com sucesso';
        }
        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', $msg)
                : responseJson('error', 'Falha ao aprovar requisição de prêmio');

    }

    // -------------------------------------------------------
    // Aprovar Requisição como RH Master
    // -------------------------------------------------------
    public function AprovarRHRequisicao($dados = false){

        $ids = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        if(substr($ids, -1)==",") { $ids = substr($ids, 0, -1); }
        
        $func_chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? "";
        $func_chapa = trim($func_chapa) == '' ? 'RH' : $func_chapa;
        $log_id = $_SESSION['log_id'];
        $query = "
            UPDATE zcrmportal_premios_requisicao_aprovacao 
            SET status = 'H'
            WHERE status = 'C' AND id_requisicao IN ({$ids})";
        $this->dbportal->query($query);

        $query = "
            UPDATE zcrmportal_premios_requisicao 
            SET 
                status = 'H',
                dt_rh_aprovacao = '".date('Y-m-d')."', 
                motivo_recusa = '',
                chapa_rh_aprov_reprov = '".$func_chapa."'  
            WHERE id IN ({$ids})";
        $this->dbportal->query($query);

        if (str_contains($ids, ',')) {
            $msg = 'Requisições de prêmio aprovadas pelo RH Master';
        } else {
            $msg = 'Requisição de prêmio aprovada pelo RH Master';
        }
        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', $msg)
                : responseJson('error', 'Falha ao aprovar como RH Master');

    }

    //---------------------------------------------
    // Listar Funcionários (RM)
    //---------------------------------------------
    public function ListarFunc(){

        $where = "A.CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
        //(A.CODSITUACAO NOT IN ('D') OR YEAR(A.DATADEMISSAO) >= 2024) AND 
        //REMOVIDO EM 04/12 - ALVARO
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

    //---------------------------------------------
    // Listar Funcionários Chapa Gestor
    //---------------------------------------------
    public function ListarFuncChapaGestor($chapa){

        $where = "A.CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
        $query = "
            SELECT
                A.CHAPA_COLAB   CHAPA,
                A.NOME_COLAB    NOME
            FROM
                GESTOR_CHAPA_PREMIOS A
            WHERE
                A.GESTOR_CHAPA = '".$chapa."' AND 
                {$where}
            ORDER BY
                A.NOME_COLAB
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //--------------------------------------------------
    // Listar Coordenadores que não aprovaram requisição
    //--------------------------------------------------
    public function ListarGestorNaoAprovou($id_requisicao){

        $id_coligada = $_SESSION['func_coligada'];
        $query = "
            select a.chapa_coordenador, g.nome_colab as nome_coordenador
            from zcrmportal_premios_requisicao_aprovacao a
            join GESTOR_CHAPA_PREMIOS g on 
                g.codcoligada = ".$id_coligada." and
                g.CHAPA_COLAB = a.chapa_coordenador COLLATE Latin1_General_CI_AS
            where 
                a.status = 'E' and
                a.id_requisicao = ".$id_requisicao." 
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //-------------------------------
    // Retorna o gerente de uma chapa
    //-------------------------------
    public function GerenteChapa($chapa){

        $id_coligada = $_SESSION['func_coligada'];
        $sql = "
            SELECT DISTINCT D.chapa GESTOR_CHAPA
            FROM 
	            ".DBRM_BANCO."..PFUNC A,
	            zcrmportal_frente_trabalho B,
	            zcrmportal_hierarquia_frentetrabalho C,
	            zcrmportal_hierarquia_chapa D
            WHERE
                A.CHAPA = '".$chapa."'
	            AND A.CODCOLIGADA = ".$id_coligada." 
	            AND B.codsecao = A.CODSECAO COLLATE Latin1_General_CI_AS
	            AND B.coligada = A.CODCOLIGADA
                AND B.id = C.id_frentetrabalho
	            AND C.inativo IS NULL
                AND D.id_hierarquia = C.id_hierarquia
        ";
        $query_sql = $this->dbportal->query($sql);
        $row = $query_sql->getRow();
                        
        if (isset($row)) {
            $chapa_gestor = $row->GESTOR_CHAPA;
            $sql = "
                SELECT	TOP 1 GER_CHAPA 
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE	COL_ID = ".$id_coligada." 
                AND     ( N1_CHAPA = '".$chapa_gestor."' 
                        OR N2_CHAPA = '".$chapa_gestor."' 
                        OR N3_CHAPA = '".$chapa_gestor."'
                        OR GER_CHAPA = '".$chapa_gestor."' )
            ";
            $query_sql = $this->dbportal->query($sql);
            $rowg = $query_sql->getRow();
                        
            if (isset($rowg)) { 
                return $rowg->GER_CHAPA; 
            } else { 
                $sql = "
                    SELECT	TOP 1 GER_CHAPA 
                    FROM	GESTORES_ABAIXO_GERENTE_GERAL 
                    WHERE	COL_ID = ".$id_coligada." 
                    AND     ( N1_CHAPA = '".$chapa_gestor."' 
                            OR N2_CHAPA = '".$chapa_gestor."' 
                            OR N3_CHAPA = '".$chapa_gestor."'
                            OR N4_CHAPA = '".$chapa_gestor."'
                            OR GER_CHAPA = '".$chapa_gestor."' )
                ";
                $query_sql = $this->dbportal->query($sql);
                $rowg = $query_sql->getRow();
                            
                if (isset($rowg)) { 
                    return $rowg->GER_CHAPA; 
                } else { 
                    $sql = "
                        SELECT	TOP 1 GER_CHAPA 
                        FROM	GESTORES_ABAIXO_DIRETOR
                        WHERE	COL_ID = ".$id_coligada." 
                        AND     ( N1_CHAPA = '".$chapa_gestor."' 
                                OR N2_CHAPA = '".$chapa_gestor."' 
                                OR N3_CHAPA = '".$chapa_gestor."'
                                OR N4_CHAPA = '".$chapa_gestor."'
                                OR GER_CHAPA = '".$chapa_gestor."' )
                    ";
                    $query_sql = $this->dbportal->query($sql);
                    $rowg = $query_sql->getRow();
                                
                    if (isset($rowg)) { 
                        return $rowg->GER_CHAPA; 
                    } else { 
                        return false;  
                    }
                }
            }
        } else {
            return false;
        }
    }

    //-----------------------------------
    // Retorna o coordenador de uma chapa
    //-----------------------------------
    public function CoordenadorChapa($chapa){

        $id_coligada = $_SESSION['func_coligada'];
        $sql = "
            SELECT DISTINCT D.chapa GESTOR_CHAPA
            FROM 
	            ".DBRM_BANCO."..PFUNC A,
	            zcrmportal_frente_trabalho B,
	            zcrmportal_hierarquia_frentetrabalho C,
	            zcrmportal_hierarquia_chapa D
            WHERE
                A.CHAPA = '".$chapa."'
	            AND A.CODCOLIGADA = ".$id_coligada." 
	            AND B.codsecao = A.CODSECAO COLLATE Latin1_General_CI_AS
	            AND B.coligada = A.CODCOLIGADA
                AND B.id = C.id_frentetrabalho
	            AND C.inativo IS NULL
                AND D.id_hierarquia = C.id_hierarquia
        ";
        $query_sql = $this->dbportal->query($sql);
        $row = $query_sql->getRow();
                        
        if (isset($row)) {
            $chapa_gestor = $row->GESTOR_CHAPA;
            $sql = "
                SELECT	TOP 1 COORD_CHAPA 
                FROM	GESTORES_ABAIXO_COORDENADOR
                WHERE	COL_ID = ".$id_coligada." 
                AND     ( N1_CHAPA = '".$chapa_gestor."' 
                        OR N2_CHAPA = '".$chapa_gestor."' 
                        OR N3_CHAPA = '".$chapa_gestor."'
                        OR COORD_CHAPA = '".$chapa_gestor."' )
            ";
            $query_sql = $this->dbportal->query($sql);
            $rowc = $query_sql->getRow();
                        
            if (isset($rowc)) { 
                return $rowc->COORD_CHAPA; 
            } else {
                $sql = "
                    SELECT	TOP 1 GER_CHAPA 
                    FROM	GESTORES_ABAIXO_GERENTE 
                    WHERE	COL_ID = ".$id_coligada." 
                    AND     ( N1_CHAPA = '".$chapa_gestor."' 
                            OR N2_CHAPA = '".$chapa_gestor."' 
                            OR N3_CHAPA = '".$chapa_gestor."'
                            OR GER_CHAPA = '".$chapa_gestor."' )
                ";
                $query_sql = $this->dbportal->query($sql);
                $rowg = $query_sql->getRow();
                            
                if (isset($rowg)) { 
                    return $rowg->GER_CHAPA; 
                } else { 
                    $sql = "
                        SELECT	TOP 1 GER_CHAPA 
                        FROM	GESTORES_ABAIXO_GERENTE_GERAL 
                        WHERE	COL_ID = ".$id_coligada." 
                        AND     ( N1_CHAPA = '".$chapa_gestor."' 
                                OR N2_CHAPA = '".$chapa_gestor."' 
                                OR N3_CHAPA = '".$chapa_gestor."'
                                OR N4_CHAPA = '".$chapa_gestor."'
                                OR GER_CHAPA = '".$chapa_gestor."' )
                    ";
                    $query_sql = $this->dbportal->query($sql);
                    $rowg = $query_sql->getRow();
                                
                    if (isset($rowg)) { 
                        return $rowg->GER_CHAPA; 
                    } else { 
                        $sql = "
                            SELECT	TOP 1 GER_CHAPA 
                            FROM	GESTORES_ABAIXO_DIRETOR
                            WHERE	COL_ID = ".$id_coligada." 
                            AND     ( N1_CHAPA = '".$chapa_gestor."' 
                                    OR N2_CHAPA = '".$chapa_gestor."' 
                                    OR N3_CHAPA = '".$chapa_gestor."'
                                    OR N4_CHAPA = '".$chapa_gestor."'
                                    OR GER_CHAPA = '".$chapa_gestor."' )
                        ";
                        $query_sql = $this->dbportal->query($sql);
                        $rowg = $query_sql->getRow();
                                    
                        if (isset($rowg)) { 
                            return $rowg->GER_CHAPA; 
                        } else { 
                            return '';  
                        }
                    }
                }
            }
        } else {
            return '';
        }
    }

    //---------------------------------------------
    // Listar Gerente e Coordenadores da Requisição
    //---------------------------------------------
    public function ListarGerenteCoordenadores($id_req){

        $id_coligada = $_SESSION['func_coligada'];
        $query = "
            WITH GESTORES AS (
                SELECT chapa_gerente AS GER_CHAPA 
                FROM zcrmportal_premios_requisicao WHERE id = ".$id_req." and id_coligada = ".$id_coligada." 
            ),
            
            CHAPAS AS (
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N1_CHAPA IS NOT NULL
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N2_CHAPA IS NOT NULL
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N3_CHAPA IS NOT NULL
                UNION
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N1_CHAPA IS NOT NULL
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N2_CHAPA IS NOT NULL
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N3_CHAPA IS NOT NULL
                UNION
                SELECT	N4_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N4_CHAPA IS NOT NULL
                UNION
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N1_CHAPA IS NOT NULL
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N2_CHAPA IS NOT NULL
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N3_CHAPA IS NOT NULL
                UNION
                SELECT	N4_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N4_CHAPA IS NOT NULL
            )

            SELECT DISTINCT
                r.id                id_requisicao,
                r.chapa_requisitor  chapa_requisitor,
                r.id_coligada       id_coligada,
                f.nome              nome_requisitor,

				g.ger_chapa         chapa_gerente,
				a.nome				nome_gerente,
				c.chapa				chapa_coordenador,
				b.nome				nome_coordenador

            FROM zcrmportal_premios_requisicao r
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON f.codcoligada = r.id_coligada AND r.chapa_requisitor = f.chapa COLLATE Latin1_General_CI_AS 
            LEFT JOIN GESTORES g ON chapa_gerente IS NOT NULL 
			LEFT JOIN CHAPAS c ON c.chapa IN (SELECT coord_chapa FROM GESTORES_ABAIXO_COORDENADOR WHERE col_id = ".$id_coligada.") 
            LEFT JOIN ".DBRM_BANCO."..PFUNC a ON a.codcoligada = r.id_coligada AND g.ger_chapa = a.chapa COLLATE Latin1_General_CI_AS 
            LEFT JOIN ".DBRM_BANCO."..PFUNC b ON b.codcoligada = r.id_coligada AND c.chapa = b.chapa COLLATE Latin1_General_CI_AS 
            WHERE r.id = ".$id_req." 
            
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Listar Funcionários do Gestor (RM)
    //---------------------------------------------
    public function ListarFuncGestor($chapa){

        $where = "A.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND D.CHAPA = '".$chapa."'";
        $query = "
            SELECT 
                DISTINCT
                A.CODCOLIGADA,
                A.CHAPA,
                A.NOME,
                C.id_hierarquia ID_HIERARQUIA,
                D.chapa GESTOR_CHAPA,
                E.NOME GESTOR_NOME
            FROM 
                ".DBRM_BANCO."..PFUNC A,
                zcrmportal_frente_trabalho B,
                zcrmportal_hierarquia_frentetrabalho C,
                zcrmportal_hierarquia_chapa D,
                ".DBRM_BANCO."..PFUNC E
            WHERE
                A.CODSITUACAO NOT IN ('D')
	
                AND B.codsecao = A.CODSECAO COLLATE Latin1_General_CI_AS
                AND B.coligada = A.CODCOLIGADA
	
                AND B.id = C.id_frentetrabalho
                AND C.inativo IS NULL

	            AND D.id_hierarquia = C.id_hierarquia
	
                AND D.chapa = E.CHAPA COLLATE Latin1_General_CI_AS
                AND D.coligada = E.CODCOLIGADA

                AND  ".$where;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];
    }

    // -------------------------------------------------------
    // Listar Acessos de Premios Ativos
    // -------------------------------------------------------
    public function ListarAcessosPremios(){

        $func_chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? "";
        
        $query = "
            SELECT 
                a.id AS id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
                a.dtini_req,
                a.dtfim_req,
                a.dtini_ponto,
                a.dtfim_ponto,
                FORMAT(a.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(a.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(a.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_acessos a 
            JOIN zcrmportal_premios p ON p.id = a.id_premio 
            LEFT JOIN zcrmportal_premios_excecao e ON 
                e.id_premio = a.id_premio AND
                e.dtini_ponto = a.dtini_ponto AND
                e.dtfim_ponto = a.dtfim_ponto 
            WHERE a.status = 'A'  AND p.status = 'A'  
                AND ( ( a.dtini_req <= Convert(date, getdate()) AND a.dtfim_req >= Convert(date, getdate()) )   
                 OR   ( e.dtini_req <= Convert(date, getdate()) AND e.dtfim_req >= Convert(date, getdate()) ) ) 
                AND ( p.dt_vigencia IS NULL OR Convert(date, p.dt_vigencia) >= Convert(date, getdate()) )  
        ";
        
        if($func_chapa != "") {
            $query = $query."
            AND ( a.id in (SELECT id_acesso FROM zcrmportal_premios_acessos_usuarios 
                            WHERE chapa = '".$func_chapa."' AND status = 'A') OR 
                  e.id in (SELECT id_acesso FROM zcrmportal_premios_excecao_usuarios 
                            WHERE chapa = '".$func_chapa."' AND status = 'A') 
                )
            "; 
        }

        //echo '<PRE> '.$query;
        //exit();

        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    } 

    // -----------------------------------------------------------------------
    // Enviar Requisicao para aprovação
    // ------------------------------------------------------------------------

    public function EnviarRequisicaoAprovacao($dados){
        
        $id_requisicao = $dados['id'];

        // Apaga realizados zerados
        $query = " DELETE FROM zcrmportal_premios_requisicao_chapas WHERE id_requisicao = {$id_requisicao} AND ( realizado IS NULL or realizado <= 0 ) ";
        $this->dbportal->query($query);

        $func_chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? "";

        // Loop para enviar requisição para aprovar
        $id_usuario = $_SESSION['log_id'];
        $sucesso = true;

        $chapas = "
            SELECT 
                 c.id
                ,c.tipo
                ,c.chapa
                ,c.chapa_coordenador
                ,c.id_coligada
                ,c.target
                ,c.realizado
                ,r.id_acesso
                ,r.chapa_requisitor
                ,a.dtini_ponto
                ,a.dtfim_ponto
                ,a.id_premio
            FROM zcrmportal_premios_requisicao_chapas c
            LEFT JOIN zcrmportal_premios_requisicao r ON r.id = c.id_requisicao
            LEFT JOIN zcrmportal_premios_acessos a on a.id = r.id_acesso

            WHERE c.status <> 'I' AND c.id_requisicao = ". $id_requisicao;

        $qry = $this->dbportal->query($chapas);

		$resChapa = ($qry) ? $qry->getResultArray() : false;
		if ($resChapa && is_array($resChapa)) {
            foreach ($resChapa as $idc => $value) {
                $id_chapa = $resChapa[$idc]['id'];
                $codcoligada = $resChapa[$idc]['id_coligada'];
                $chapa = $resChapa[$idc]['chapa'];
                $dtini_ponto = $resChapa[$idc]['dtini_ponto'];
                $dtfim_ponto = $resChapa[$idc]['dtfim_ponto'];
                $id_coligada = $resChapa[$idc]['id_coligada'];
                $id_premio = $resChapa[$idc]['id_premio'];

                $d_faltas = 0;
                $d_ferias = 0;
                $d_admissao = 0;
                $d_demissao = 0;
                $d_afast = 0;
                $d_atestado = 0;
                $d_aa = 0;

                $salario_base = 0;
                $base_calculo = 0;
                $valor_base = 0;

                $df_faltas = 0;
                $df_ferias = 0;
                $df_admissao = 0;
                $df_demissao = 0;
                $df_afast = 0;
                $df_atestado = 0;

                /* ATUALIZAR DEFLATORES
                    VERIFICAR INCONSISTENCIAS */

                if(is_null($resChapa[$idc]['chapa_coordenador'])) {
                    return responseJson('error', 'O coordenador da chapa '.$chapa.' está incorreto. Requisição NÃO enviada para aprovação');
                }
                    
                if(is_null($resChapa[$idc]['target']) or $resChapa[$idc]['target'] <=0) {
                    return responseJson('error', 'O % target da chapa '.$chapa.' está incorreto. Requisição NÃO enviada para aprovação');
                }
                if(is_null($resChapa[$idc]['realizado']) or $resChapa[$idc]['realizado'] <0) {
                    return responseJson('error', 'O % realizado da chapa '.$chapa.' está incorreto. Requisição NÃO enviada para aprovação');
                }

            }
        }

        // Delete aprovações de requisições da req atual caso existam
        $query = "
            UPDATE zcrmportal_premios_requisicao_aprovacao
            SET status = 'I' 
            WHERE id_requisicao = ".$id_requisicao." 
        ";
        $this->dbportal->query($query);

        $query = "
            WITH COORD AS (
                SELECT DISTINCT id_requisicao, chapa_coordenador 
                FROM zcrmportal_premios_requisicao_chapas 
                WHERE chapa_coordenador IS NOT NULL AND id_requisicao = ".$id_requisicao." 
            )
            SELECT 
                r.id,
                r.id_coligada,
                r.dt_requisicao,
                FORMAT(r.dt_requisicao, 'dd/MM/yyyy') as dt_requisicao_br,
                r.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
                r.tipo,
                CASE WHEN r.tipo = 'M' THEN 'MENSAL'
                    ELSE 'COMPLEMENTAR'
                END tipo_requisicao,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br,
                r.chapa_requisitor,
                r.chapa_gerente,
                r.id_coligada,
                g.chapa_colab,
                g.nome_colab,
                c.chapa_coordenador as gestor_chapa,
                e.nome as gestor_nome,
                e.email gestor_email
            FROM zcrmportal_premios_requisicao r
            LEFT JOIN COORD c ON c.id_requisicao = r.id
            LEFT JOIN zcrmportal_premios_acessos a ON a.id = r.id_acesso
            LEFT JOIN zcrmportal_premios p ON p.id = a.id_premio
            LEFT JOIN gestor_chapa_premios g ON g.codcoligada = r.id_coligada AND r.chapa_requisitor = g.chapa_colab COLLATE Latin1_General_CI_AS
            LEFT JOIN email_chapa e ON e.codcoligada = r.id_coligada AND e.chapa = c.chapa_coordenador COLLATE Latin1_General_CI_AS
            WHERE r.id = ".$id_requisicao;

        $result = $this->dbportal->query($query);
        $resReq = $result->getResultArray();
        $falhou = false;
        $status_req = 'E';
        $tem_status_E = false;

        if ($resReq && is_array($resReq)) {
            foreach ($resReq as $idc => $value) {
                $nome_colab = $resReq[$idc]['nome_colab'];
                $gestor_nome = $resReq[$idc]['gestor_nome'];
                $gestor_email = $resReq[$idc]['gestor_email'];
                $nome_premio = $resReq[$idc]['nome_premio'];
                $per_ponto_br = $resReq[$idc]['per_ponto_br'];
                $dt_requisicao_br = $resReq[$idc]['dt_requisicao_br'];
                $tipo_requisicao = $resReq[$idc]['tipo_requisicao'];

                $chapa_requisitor = $resReq[$idc]['chapa_requisitor'];
                $chapa_coordenador = $resReq[$idc]['gestor_chapa'];
                $chapa_gerente = $resReq[$idc]['chapa_gerente'];
                $dt_requisicao = $resReq[$idc]['dt_requisicao'];
                $id_acesso = $resReq[$idc]['id_acesso'];
                $tipo = $resReq[$idc]['tipo'];
                $id_coligada = $resReq[$idc]['id_coligada'];
                $status = ($chapa_requisitor == $chapa_coordenador and $chapa_coordenador == $func_chapa) ? 'A' : 'E';

                if($status_req == 'E' and $status == 'A') {
                    $status_req = 'A';
                }
                if($status == 'E') {
                    $tem_status_E = true;
                }
                
                $nome_colab = ($_SESSION['rh_master'] == 'S') ? 'Processos de RH' : $nome_colab;
                // Cria as requisições de aprovações
                $query = "
                    INSERT INTO zcrmportal_premios_requisicao_aprovacao
                    ( id_requisicao, chapa_coordenador, status)
                    VALUES
                    ( {$id_requisicao}, '{$chapa_coordenador}', '{$status}' )
                ";
                $this->dbportal->query($query);
        
                if ($this->dbportal->affectedRows() > 0) {
                    if ($status == 'E') {
                        // Envio de email solicitando aprovação da requisicao
                        $mensagem = '
                        Prezado <strong>'.$gestor_nome.'</strong>, <br>
                        <br>
                        Venho por meio desta solicitar a aprovação da requisição do prêmio abaixo.<br>
                        <br>
                        Prêmio: '.$nome_premio.'<br>
                        Período: '.$per_ponto_br.'<br>
                        Tipo: '.$tipo_requisicao.'<br>
                        <br>
                        Segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>.<br>
                        <br>
                        Att.,<br>'.$nome_colab.'<br>'.date('d/m/Y').'
                        ';

                        $htmlEmail = templateEmail($mensagem, '95%');

                        $response = enviaEmail($gestor_email, 'Requisição de aprovação de Prêmio', $htmlEmail);
                    }
                } else {
                    $falhou = true;
                    notificacao('danger', 'Envio para aprovação falhou. Erro na criação do registro de aprovação. Aprovador: '.$chapa_coordenador);
                }
            }
        }

        // Atualiza status da requisição
        if($tem_status_E) {
            $status_req = 'E';
        }
        $query = "
            UPDATE zcrmportal_premios_requisicao 
            SET status = '".$status_req."', dt_envio_email = '".date('Y-m-d')."' 
            WHERE id = '{$id_requisicao}'";
        $this->dbportal->query($query);
        
        if ($this->dbportal->affectedRows() > 0) {
            if ($falhou) {
                return responseJson('success', 'Requisição enviada para aprovação com falhas');
            } else {
                return responseJson('success', 'Requisição enviada para aprovação');
            }

        } else {
            return responseJson('error', 'Requisição NÃO enviada para aprovação');
        }

    }

    // -------------------------------------------------------
    // Importar Requisição
    // -------------------------------------------------------
	public function ImportarRequisicao($dados){

		try {

			$documento = $dados['documento'];
            $id_requisicao = $dados['id_requisicao'];
            $falha_insere = 0;
            $falha_altera = 0;
            $rhm = ($_SESSION['rh_master'] == 'S') ? true : false ;
			
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
            $colunasEsperadas = ['TIPO', 'FUNC_CHAPA', 'FUNC_NOME', 'FUNCAO', 'SITUACAO', 'DT_ADMISSAO_BR', 'CODFILIAL', 'CODCUSTO', 'SECAO', 'TARGET', 'REALIZADO'];

            // Valida o nome das colunas
            $colunasLidas = $data[0]; // A primeira linha contém o nome das colunas
            if ($colunasEsperadas !== $colunasLidas) {
				return responseJson('error', 'As colunas do arquivo Excel não correspondem às colunas esperadas.<br><br>As colunas lidas foram: '.json_encode($colunasLidas));
			}

            $erro = "";
            // Loop para ler e gravar dados novos
            for ($i = 1; $i < count($data); $i++) { 
                $chapa = $data[$i][1];
                $tipo = mb_strtoupper($data[$i][0]) == mb_strtoupper("PADRÃO") ? "P" : "E";
                $target = is_null($data[$i][9]) ? 0 : $data[$i][9];
                $realizado = is_null($data[$i][10]) ? 0 : $data[$i][10];
                
                // validar requisicao e chapa
                if($chapa!='' and is_numeric($target) and is_numeric($realizado) and $target > 0 and $target <= 999.99 and $realizado >= 0 and ($realizado <=100 or ($rhm and $realizado <=999.99)) ) {

                    // verifica se chapa já existe
                    $where = "id_requisicao = {$id_requisicao} AND chapa = '{$chapa}' AND status = 'A'";
                    $query = "SELECT id FROM zcrmportal_premios_requisicao_chapas WHERE {$where}";
                    $result = $this->dbportal->query($query);
                    if ($result->getNumRows() > 0) {
                        // Atualiza dados
                        $query = " 
                            UPDATE
                                zcrmportal_premios_requisicao_chapas
                            SET
                                tipo = '{$tipo}', 
                                realizado = {$realizado} 
                            WHERE
                                {$where}
                        ";
                        $this->dbportal->query($query);
                
                        if ($this->dbportal->affectedRows() <= 0) {
                            $falha_altera++;
                        }

                    } else {
                        /* INCLUSÃO NÃO PERMITIDA POR PLANILHA POIS PRECISA DE OBSERVAÇÃO
                        $id_coligada = $_SESSION['func_coligada'];
                        if(($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S')) {
                            $sql = "
                                SELECT chapa FROM pfunc
                                WHERE chapa = '".$chapa."' 
                                AND   codcoligada = ".$id_coligada." 
                            ";
                            $query_sql = $this->dbrm->query($sql);
                            $row = $query_sql->getRow();

                        } else {
                            $sql = "
                                select 
                                    g.chapa_colab	chapa,
                                    g.nome_colab	nom
                                from gestor_chapa_premios g
                                join zcrmportal_premios_requisicao r on r.id_coligada = g.codcoligada and r.chapa_requisitor = g.gestor_chapa 
                                where g.chapa_colab = '".$chapa."'  
                                and   r.id = ".$id_requisicao." 
                            ";
                            $query_sql = $this->dbportal->query($sql);
                            $row = $query_sql->getRow();
                        }
                        
                        if (isset($row)) {
                            // Insere dados
                            $id_usuario = $_SESSION['log_id'];
                            $query = " 
                                INSERT INTO zcrmportal_premios_requisicao_chapas 
                                    (chapa, tipo, target, realizado, id_requisicao, id_coligada, id_usuario)
                                VALUES 
                                    ('{$chapa}', '{$tipo}', {$target}, {$realizado}, {$id_requisicao}, {$id_coligada}, {$id_usuario})    
                            ";
                            $this->dbportal->query($query);

                            if ($this->dbportal->affectedRows() <= 0) {
                                $falha_insere++;
                            }
                        } else {*/
                            $erro = $erro.($erro == "" ? "Inconsistência(s) na(s) linha(s): ".$i : ", ".$i);
                        //}
                    }
                } else {
                    $erro = $erro.($erro == "" ? "Inconsistência(s) na(s) linha(s): ".$i : ", ".$i);
                }
            }

			notificacao($erro == "" ? 'success' : 'danger', 'Importação finalizada. '.$erro);
			return responseJson($erro == "" ? 'success' : 'error', 'Importação finalizada. '.$erro);

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

    // -------------------------------------------------------
    // Apagar Realizados Zerados - Requisição
    // -------------------------------------------------------
    public function ApagarZerados($dados){
        $id_requisicao = $dados['id_requisicao'];

        // Apaga todas chapas da requisição com % realizados = 0
        $query = " DELETE FROM zcrmportal_premios_requisicao_chapas WHERE id_requisicao = {$id_requisicao} AND ( realizado IS NULL or realizado <= 0 ) ";
        $this->dbportal->query($query);

        return responseJson('success', 'Chapas com percentual realizado igual a zero foram apagadas.');
    }

    // -------------------------------------------------------
    // Processar / Recalcular Requisicao
    // -------------------------------------------------------
    public function ProcessarRequisicao($dados){

        $mHierarquia = Model('HierarquiaModel');

        $id_requisicao = $dados['id_requisicao'];
        $tipo_req = $dados['tipo_req'];
        $msgCompl = '';

        // Apaga todas chapas da requisição para processar
        $query = " DELETE FROM zcrmportal_premios_requisicao_chapas WHERE id_requisicao = {$id_requisicao}";
        $this->dbportal->query($query);

        // Loop para incluir chapas
        $id_coligada = $_SESSION['func_coligada'];
        $id_usuario = $_SESSION['log_id'];
        $sucesso = true;

        $chapa = "'". util_chapa(session()->get('func_chapa'))['CHAPA'] ."'" ?? null;
        $chapasGerentes = [];

        if($chapa){
        
            $chapasGestorSubstituto = $mHierarquia->getChapasGestorSubstituto($chapa);

            if($chapasGestorSubstituto){
                foreach($chapasGestorSubstituto as $idx  => $value){
                    $chapa_gerente = $this->GerenteChapa($chapasGestorSubstituto[$idx]['chapa_gestor']);

                    if ($chapa_gerente) {
                        $chapasGerentesArray[] = " SELECT '" . $chapa_gerente . "' AS GER_CHAPA  ";
                    }
                }
            }
        }

        $chapasGerentes = isset($chapasGerentesArray) && is_array($chapasGerentesArray) && count($chapasGerentesArray) > 0
            ? implode(' UNION ALL ', $chapasGerentesArray)
            : '';

        $chapas = "
            WITH GESTORES AS (
                SELECT chapa_gerente AS GER_CHAPA 
                FROM zcrmportal_premios_requisicao WHERE id = ".$id_requisicao." and id_coligada = ".$id_coligada." 
                ". (!empty($chapasGerentes) ? " UNION ALL ".$chapasGerentes : "") ."
            ),
            
            CHAPAS AS (
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N1_CHAPA IS NOT NULL
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N2_CHAPA IS NOT NULL
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N3_CHAPA IS NOT NULL
                UNION
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N1_CHAPA IS NOT NULL
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N2_CHAPA IS NOT NULL
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N3_CHAPA IS NOT NULL
                UNION
                SELECT	N4_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N4_CHAPA IS NOT NULL
                UNION
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N1_CHAPA IS NOT NULL
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N2_CHAPA IS NOT NULL
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N3_CHAPA IS NOT NULL
                UNION
                SELECT	N4_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA IN (SELECT GER_CHAPA FROM GESTORES) AND N4_CHAPA IS NOT NULL
            )

            SELECT DISTINCT
                r.id                id_requisicao,
                r.chapa_requisitor  chapa_requisitor,
                r.id_coligada       id_coligada,
                f.nome              nome_requisitor,
                r.id_acesso         id_acesso,
                a.dtini_ponto       dtini_ponto,
                a.dtfim_ponto       dtfim_ponto,
                p.codfilial			codfilial,
                g.codsecao_colab	codsecao,
                g.chapa_colab       chapa_func,
                g.nome_colab        nome_func,
                g.dt_demissao_colab dt_demissao_func,
                p.codcusto			codcusto_func,
                isnull(p.target,0)  target_premissa,
				case 
					when c.grupocargo in ('04 - Coordenador','03 - Gerente','02 - Gerente Geral', '01 - Diretor') then c.gestor_chapa
					when c.grupocargo_n1 in ('04 - Coordenador','03 - Gerente','02 - Gerente Geral', '01 - Diretor') then c.gestor_n1
					when c.grupocargo_n2 in ('04 - Coordenador','03 - Gerente','02 - Gerente Geral', '01 - Diretor') then c.gestor_n2
					when c.grupocargo_n3 in ('04 - Coordenador','03 - Gerente','02 - Gerente Geral', '01 - Diretor') then c.gestor_n3
					when c.grupocargo_n4 in ('04 - Coordenador','03 - Gerente','02 - Gerente Geral', '01 - Diretor') then c.gestor_n4
				end chapa_coordenador
                            
            FROM zcrmportal_premios_requisicao r
            LEFT JOIN zcrmportal_premios_acessos a ON a.id = r.id_acesso
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON f.codcoligada = r.id_coligada AND r.chapa_requisitor = f.chapa COLLATE Latin1_General_CI_AS 
            LEFT JOIN zcrmportal_premios_premissas p
                ON  p.id_premio = a.id_premio
                AND p.status <> 'I'
            LEFT JOIN GESTOR_CHAPA_PREMIOS g
                ON  g.codcoligada = r.id_coligada
                AND g.codfilial_colab = p.codfilial
                AND p.codfuncao = g.codfuncao_colab COLLATE Latin1_General_CI_AS 
                AND p.codcusto = g.ccusto_colab COLLATE Latin1_General_CI_AS 
                AND g.codsituacao_colab <> 'D' 
				AND g.gestor_chapa IN (SELECT CHAPA FROM CHAPAS)
			LEFT JOIN GESTORES_ACIMA c
				ON  c.codcoligada = r.id_coligada
                AND c.gestor_chapa = g.gestor_chapa 
            WHERE g.chapa_colab IS NOT NULL AND r.id = ".$id_requisicao;

        // AND ( g.dt_demissao_colab IS NULL OR g.dt_demissao_colab >= a.dtini_ponto )
        // CODIGO REMOVIDO DO SELECT ACIMA EM 04/12, substutuido por  AND g.codsituacao_colab <> 'D'

        $qry = $this->dbportal->query($chapas);
		$resChapa = ($qry) ? $qry->getResultArray() : false;
		if ($resChapa && is_array($resChapa)) {
            foreach ($resChapa as $idc => $value) {
                $chapa = $resChapa[$idc]['chapa_func'];
                $chapa_coordenador = $resChapa[$idc]['chapa_coordenador'];
                $target = $resChapa[$idc]['target_premissa'];
                $id_coligada = $resChapa[$idc]['id_coligada'];

                //echo $chapa.' - '.$id_coligada.' - '.$id_requisicao.' - '.$tipo_req;
                //exit();
                //checa se chapa já existe para esse prêmio
                if($this->ExisteFuncAcesso($chapa, $id_coligada, $id_requisicao, $tipo_req)) {
                    $msgCompl = ' Uma ou mais chapas já existem em outra requisição do mesmo prêmio, período de ponto e tipo.';
                } else {

                    if(is_null($chapa_coordenador) or $chapa_coordenador == '') {
                        $chapa_coordenador = $this->CoordenadorChapa($chapa);
                    }
                    $query = " INSERT INTO zcrmportal_premios_requisicao_chapas
                        (chapa, tipo, target, id_coligada, id_requisicao, id_usuario, chapa_coordenador) 
                            VALUES
                        ('{$chapa}', 'P', {$target}, {$id_coligada}, {$id_requisicao}, {$id_usuario}, '{$chapa_coordenador}')    
                    ";
                    $this->dbportal->query($query);
                    if($this->dbportal->affectedRows() <= 0) { $sucesso = false; }
                }
            }
        }

        // Busca Emprestimos
        $q = $this->dbportal->query("SELECT id_acesso, chapa_gerente, chapa_requisitor from zcrmportal_premios_requisicao WHERE id = ".$id_requisicao );
        $row = $q->getRow();
        $id_acesso = $row->id_acesso;
        $chapa_requisitor = $row->chapa_requisitor;
        $chapa_gerente = $row->chapa_gerente;

        $chapas = "
            WITH CHAPAS AS (
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE 
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N4_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_GERENTE_GERAL
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N1_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N2_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N3_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
                UNION
                SELECT	N4_CHAPA AS CHAPA
                FROM	GESTORES_ABAIXO_DIRETOR
                WHERE GER_CHAPA = '".$chapa_gerente."' AND col_id = ".$id_coligada." 
            )
            
            SELECT chapa_colaborador, para_chapa 
            FROM zcrmportal_premios_emprestimos
            WHERE 
                id_acesso = ".$id_acesso." AND 
                para_chapa IN (SELECT CHAPA FROM CHAPAS) AND
                id_coligada = ".$id_coligada." AND
                status = 'E' 
        ";

        $qry = $this->dbportal->query($chapas);
        $resChapa = ($qry) ? $qry->getResultArray() : false;
        if ($resChapa && is_array($resChapa)) {
            foreach ($resChapa as $idc => $value) {
                $chapa = $resChapa[$idc]['chapa_colaborador'];
                $chapa_coordenador = $resChapa[$idc]['para_chapa'];
                //checa se chapa já existe para esse prêmio
                if($this->ExisteFuncAcesso($chapa, $id_coligada, $id_requisicao, $tipo_req)) {
                    $msgCompl = ' Uma ou mais chapas já existem em outra requisição do mesmo prêmio, período de ponto e tipo.';
                } else {
                    $query = " INSERT INTO zcrmportal_premios_requisicao_chapas
                        (chapa, chapa_coordenador, tipo, target, id_coligada, id_requisicao, id_usuario) 
                            VALUES
                        ('{$chapa}', '{$chapa_coordenador}', 'E', 0, {$id_coligada}, {$id_requisicao}, {$id_usuario})    
                    ";
                    $this->dbportal->query($query);
                    if($this->dbportal->affectedRows() <= 0) { $sucesso = false; }
                }
            }
        }

        if($sucesso) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Processamento finalizado. '.$msgCompl)));

        return ($sucesso) 
                ? responseJson('success', 'Processamento finalizado. '.$msgCompl, $this->dbportal->insertID())
                : responseJson('error', 'Um ou mais usuários podem não ter sido processados corretamente.');

    }

    // -------------------------------------------------------
    // Listar Requisições a aprovar
    // -------------------------------------------------------
    public function ListarAprovaRequisicao(){

        $mHierarquia = Model('HierarquiaModel');

        $coligada = $_SESSION['func_coligada'];
        $user_id = " AND e.id_coligada = ".$coligada;

        // Filtra requisições de chapas abaixo do gestor requisitor
        if(!($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S')) {
            
            $chapa = "'". util_chapa(session()->get('func_chapa'))['CHAPA'] ."'" ?? null;

            if($chapa){
            
                $chapasGestorSubstituto = $mHierarquia->getChapasGestorSubstituto($chapa);

                if($chapasGestorSubstituto){
                    foreach($chapasGestorSubstituto as $idx  => $value){
                        $chapa .= " , '" . $chapasGestorSubstituto[$idx]['chapa_gestor'] . "' ";
                    }
                }
            }

            /* desativado em função da nova regra de aprovação
            $q = "
                SELECT STUFF((SELECT ','+ ('''' + gestor_chapa + '''')  
                FROM gestores_acima
                    WHERE codcoligada = ".$coligada." AND (gestor_n1 = '".$chapa."' OR gestor_n2 = '".$chapa."' OR gestor_n3 = '".$chapa."' OR gestor_n4 = '".$chapa."') 
                FOR XML PATH('')), 1, 1, '') AS chapas 
            ";
            $q = $this->dbportal->query($q);
            $row = $q->getRow();
            $chapas = is_null($row->chapas) ? "-1" : $row->chapas;
            */

            $user_id = $user_id." AND (e.chapa_gerente in (".$chapa.") OR r.chapa_coordenador in (".$chapa.")) ";
        }
        $query = " 
            SELECT 
	            e.id,
	            e.dt_requisicao,
                FORMAT(e.dt_requisicao, 'dd/MM/yyyy') as dt_requisicao_br,
	            e.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
                p.codevento,
	            e.chapa_requisitor,
	            f.nome AS nome_requisitor,
	            r.id AS id_aprova,
	            r.chapa_coordenador,
	            g.nome AS nome_coordenador,
	            isnull(r.status, e.status) as status,
	            e.id_requisitor,
                a.dtini_ponto,
                a.dtfim_ponto,
                a.dtini_req,
                a.dtfim_req,
                e.tipo,
                CASE WHEN e.tipo = 'M' THEN 'MENSAL'
                     ELSE 'COMPLEMENTAR'
                END tipo_requisicao,
                FORMAT(a.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(a.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(a.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_requisicao e
            LEFT JOIN ".DBRM_BANCO."..PFUNC f ON 
                f.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            e.chapa_requisitor = f.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN zcrmportal_premios_acessos a ON
	            a.id = e.id_acesso
            LEFT JOIN zcrmportal_premios_requisicao_aprovacao r ON 
                r.status not in ('I') AND r.id_requisicao = e.id 
            LEFT JOIN ".DBRM_BANCO."..PFUNC g ON 
                g.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            r.chapa_coordenador = g.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN zcrmportal_premios p ON
	            p.id = a.id_premio
            WHERE e.status not in ('P','I') AND e.id > 0 AND 
	            r.chapa_coordenador IS NOT NULL ".$user_id;


        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    }   
    
    // -------------------------------------------------------
    // Listar Requisições a aprovar
    // -------------------------------------------------------
    public function ListarTargetsPremissas($chapa_gerente, $codcoligada){

        /*QUERY ANTERIOR AO ADITIVO GERENTE/COORDENADOR
        $query = " 
	            WITH AUX1 AS (
                    SELECT [CODCOLIGADA]
                        ,[GESTOR_N1]
                    FROM [PortalRHQA].[dbo].[GESTORES_ACIMA]
                    WHERE GESTOR_CHAPA = '".$chapa."' AND CODCOLIGADA = ".$codcoligada." 
                ),

                AUX2 AS (
                    SELECT [CODCOLIGADA]
                        ,[GESTOR_CHAPA]
                    FROM [PortalRHQA].[dbo].[GESTORES_ACIMA]
                    WHERE GESTOR_N1 IN (SELECT [GESTOR_N1] FROM AUX1)
                    AND CODCOLIGADA = 1
                )

                SELECT DISTINCT 
                    isnull(p.target,0) AS target_premissa
                    ,FORMAT(isnull(p.target,0), 'N', 'pt-BR') AS target_br
                          
                FROM zcrmportal_premios_requisicao r
                LEFT JOIN zcrmportal_premios_acessos a ON a.id = r.id_acesso
                LEFT JOIN ".DBRM_BANCO."..PFUNC f ON f.codcoligada = r.id_coligada AND r.chapa_requisitor = f.chapa COLLATE Latin1_General_CI_AS 
                LEFT JOIN zcrmportal_premios_premissas p
                    ON  p.id_premio = a.id_premio
                    AND p.status <> 'I'
                LEFT JOIN GESTOR_CHAPA_PREMIOS g
                    ON  g.codcoligada = r.id_coligada
                    AND r.chapa_requisitor IN (SELECT GESTOR_CHAPA FROM AUX2)
                    AND g.codfilial_colab = p.codfilial
                    AND p.codfuncao = g.codfuncao_colab COLLATE Latin1_General_CI_AS 
                    AND p.codcusto = g.ccusto_colab COLLATE Latin1_General_CI_AS 
                    AND ( g.dt_demissao_colab IS NULL OR g.dt_demissao_colab >= a.dtini_ponto )

	    ";*/

        $query = " 
            with h_gerente as (
                select distinct n1_id as h_id from GESTORES_ABAIXO_GERENTE where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n1_id is not null
                union
                select distinct n2_id as h_id from GESTORES_ABAIXO_GERENTE where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n2_id is not null
                union
                select distinct n3_id as h_id from GESTORES_ABAIXO_GERENTE where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n3_id is not null
                union
                select distinct n1_id as h_id from GESTORES_ABAIXO_GERENTE_GERAL where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n1_id is not null
                union
                select distinct n2_id as h_id from GESTORES_ABAIXO_GERENTE_GERAL where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n2_id is not null
                union
                select distinct n3_id as h_id from GESTORES_ABAIXO_GERENTE_GERAL where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n3_id is not null
                union
                select distinct n4_id as h_id from GESTORES_ABAIXO_GERENTE_GERAL where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n4_id is not null
                union
                select distinct n1_id as h_id from GESTORES_ABAIXO_DIRETOR where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n1_id is not null
                union
                select distinct n2_id as h_id from GESTORES_ABAIXO_DIRETOR where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n2_id is not null
                union
                select distinct n3_id as h_id from GESTORES_ABAIXO_DIRETOR where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n3_id is not null
                union
                select distinct n4_id as h_id from GESTORES_ABAIXO_DIRETOR where col_id = ".$codcoligada."  and ger_chapa = '".$chapa_gerente."' and n4_id is not null
            ),

            ccustos as (
                select h.id_hierarquia, h.id_frentetrabalho, f.centrocusto, f.filial from zcrmportal_hierarquia_frentetrabalho h
                left join zcrmportal_frente_trabalho f on f.id = h.id_frentetrabalho
                where h.id_hierarquia in (select h_id from h_gerente)
                and   f.centrocusto is not null
                and   f.coligada = ".$codcoligada." 
            )

            select distinct 
                isnull(p.target,0) AS target_premissa,
                FORMAT(isnull(p.target,0), 'N', 'pt-BR') AS target_br
            from zcrmportal_premios_premissas p
            inner join ccustos c on c.filial = p.codfilial and c.centrocusto = p.codcusto
            inner join zcrmportal_premios r on r.id = p.id_premio
            where p.target is not null and p.target > 0
            and   r.codcoligada = ".$codcoligada." 
	    ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    }   

    // -------------------------------------------------------
    // Reprovar Requisição
    // -------------------------------------------------------
    public function ReprovarRequisicao($dados = false){

        $id_requisicao = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        if(substr($id_requisicao, -1)==",") { $id_requisicao = substr($id_requisicao, 0, -1); }
        $query = "
            SELECT 
                r.id,
                FORMAT(r.dt_requisicao, 'dd/MM/yyyy') as dt_requisicao_br,
                r.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
                CASE WHEN r.tipo = 'M' THEN 'MENSAL'
                    ELSE 'COMPLEMENTAR'
                END tipo_requisicao,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br,
                r.chapa_requisitor,
                r.id_coligada,
                g.chapa_colab,
                g.nome_colab,
                g.gestor_chapa,
                g.gestor_nome,
                e.email email_colab
            FROM zcrmportal_premios_requisicao r
            LEFT JOIN zcrmportal_premios_acessos a ON a.id = r.id_acesso
            LEFT JOIN zcrmportal_premios p ON p.id = a.id_premio
            LEFT JOIN gestor_chapa_premios g ON g.codcoligada = r.id_coligada AND r.chapa_requisitor = g.chapa_colab COLLATE Latin1_General_CI_AS
            LEFT JOIN email_chapa e ON e.codcoligada = r.id_coligada AND e.chapa = g.chapa_colab COLLATE Latin1_General_CI_AS
            WHERE r.id in (".$id_requisicao.")";

        $result = $this->dbportal->query($query);
        $resReq = $result->getResultArray();
        $conta = 0;
        if ($resReq && is_array($resReq)) {
            foreach ($resReq as $idc => $value) {
                $id_req = $resReq[$idc]['id'];
                $gestor_nome = $resReq[$idc]['gestor_nome'];
                $email_colab = $resReq[$idc]['email_colab'];
                $nome_colab = $resReq[$idc]['nome_colab'];
                $nome_premio = $resReq[$idc]['nome_premio'];
                $per_ponto_br = $resReq[$idc]['per_ponto_br'];
                $dt_requisicao_br = $resReq[$idc]['dt_requisicao_br'];
                $tipo_requisicao = $resReq[$idc]['tipo_requisicao'];

                $func_chapa = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? "";
                $func_chapa = trim($func_chapa) == '' ? 'RH' : $func_chapa;
                $log_id = $_SESSION['log_id'];

                $query = "
                    UPDATE zcrmportal_premios_requisicao_aprovacao
                    SET 
                        status = 'R',
                        dt_aprovacao = '".date('Y-m-d')."', 
                        id_aprovador = ".$log_id.",
                        chapa_aprov_reprov = '".$func_chapa."'  
                    WHERE status <> 'I' AND id_requisicao = '{$id_req}'";

                $this->dbportal->query($query);

                $query = "
                    UPDATE zcrmportal_premios_requisicao 
                    SET 
                        status = 'R',
                        dt_aprovacao = '".date('Y-m-d')."', 
                        id_aprovador = ".$log_id.",
                        motivo_recusa = '{$dados['motivo']}',
                        anocomp = NULL,
                        mescomp = NULL,
                        nroperiodo = NULL,
                        chapa_aprov_reprov = '".$func_chapa."'  
                    WHERE id = '{$id_req}'";

                $this->dbportal->query($query);

                if ($this->dbportal->affectedRows() > 0) {
                    if($nome_colab == $gestor_nome) {
                        $gestor_nome = 'Processos do RH';
                    }
                    $gestor_nome = ($_SESSION['rh_master'] == 'S') ? 'Processos de RH' : $gestor_nome;
                    $conta++;
                    // Envio de email informando reprovação da requisição
                    $mensagem = '
                    Prezado <strong>'.$nome_colab.'</strong>, <br>
                    <br>
                    Venho por meio desta informar que sua requisição de prêmio REPROVADA.<br>
                    <br>
                    Prêmio: '.$nome_premio.'<br>
                    Período: '.$per_ponto_br.'<br>
                    Tipo: '.$tipo_requisicao.'<br>
                    <br>
                    Motivo: '.$dados['motivo'].'<br>
                    <br>
                    Segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>.<br>
                    <br>
                    Att.,<br>'.$gestor_nome.'<br>'.date('d/m/Y').'
                    ';

                    $htmlEmail = templateEmail($mensagem, '95%');

                    $response = enviaEmail($email_colab, 'Requisição de prêmio foi REPROVADA', $htmlEmail);

                }
            }
        }

        if($conta==1) {
            return responseJson('success', 'Requisição de prêmio foi reprovada');
        } else {
            if($conta==0) { 
                return responseJson('error', 'Falha ao reprovar requisição de prêmio');
            } else {
                return responseJson('error', 'Requisições de prêmio foram reprovadas');
            }
        }
    }

    // -----------------------------------------------------------------------
    // Calcular Requisição
    // ------------------------------------------------------------------------

    public function CalcularRequisicaoRH($dados){
        
        $ids = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        if(substr($ids, -1)==",") { $ids = substr($ids, 0, -1); }
        
        // Loop para calcular chapas
        $id_usuario = $_SESSION['log_id'];
        $sucesso = true;

        $aprovacoes = "
            SELECT status FROM zcrmportal_premios_requisicao_aprovacao
            WHERE id_requisicao IN (".$ids.") 
            AND status NOT IN ('A','I','C')" ;

        $result = $this->dbportal->query($aprovacoes);
        if ($result->getNumRows() > 0) {
            //notificacao('danger', 'Todas as requisições precisam estar aprovadas pelos coordenadores para poder calcular');
            return responseJson('error', 'Todas as requisições precisam estar aprovadas para poder calcular');
        }

        $chapas = "
            SELECT 
                 c.id
                ,c.tipo
                ,c.chapa
                ,c.id_coligada
                ,c.target
                ,c.realizado
                ,r.id AS id_requisicao
                ,r.id_acesso
                ,r.chapa_requisitor
                ,a.dtini_ponto
                ,a.dtfim_ponto
                ,a.id_premio
            FROM zcrmportal_premios_requisicao_chapas c
            LEFT JOIN zcrmportal_premios_requisicao r ON r.id = c.id_requisicao
            LEFT JOIN zcrmportal_premios_acessos a on a.id = r.id_acesso

            WHERE c.id_requisicao IN (".$ids.")" ;

        $qry = $this->dbportal->query($chapas);

        $id_reqant = "";
		$resChapa = ($qry) ? $qry->getResultArray() : false;
		if ($resChapa && is_array($resChapa)) {
            foreach ($resChapa as $idc => $value) {
                $id_requisicao = $resChapa[$idc]['id_requisicao'];
                $id_chapa = $resChapa[$idc]['id'];
                $codcoligada = $resChapa[$idc]['id_coligada'];
                $chapa = $resChapa[$idc]['chapa'];
                $dtini_ponto = $resChapa[$idc]['dtini_ponto'];
                $dtfim_ponto = $resChapa[$idc]['dtfim_ponto'];
                $id_coligada = $resChapa[$idc]['id_coligada'];
                $id_premio = $resChapa[$idc]['id_premio'];
                $target = $resChapa[$idc]['target'];
                $realizado = $resChapa[$idc]['realizado'];

                $target = is_null($target) ? 0 : $target;
                $realizado = is_null($realizado) ? 0 : $realizado;

                $d_faltas = 0;
                $d_ferias = 0;
                $d_admissao = 0;
                $d_demissao = 0;
                $d_afast = 0;
                $d_atestado = 0;
                $d_aa = 0;

                $salario_base = 0;
                $base_calculo = 0;
                $valor_base = 0;

                $defla_faltas = 0;
                $defla_ferias = 0;
                $defla_admissao = 0;
                $defla_demissao = 0;
                $defla_afast = 0;
                $defla_atestado = 0;

                $datas_faltas = '';
                $datas_ferias = '';
                $datas_admissao = '';
                $datas_demissao = '';
                $datas_afast = '';
                $datas_atestado = '';

                $tem_defla_admissao = false;
                $tem_defla_demissao = false;
                $tem_defla_atestado = false;
                $tem_defla_afast = false;
                $tem_defla_ferias = false;
                $tem_defla_faltas = false;

                /* CALCULANDO DIAS DE FERIAS
                SELECT CODCOLIGADA, CHAPA,DATAINICIO,DATAFIM,NRODIASFERIAS FROM   PFUFERIASPER
                */
                $query = "
                    SELECT 
                        datainicio,
                        datafim 
                    FROM pfuferiasper
                    WHERE 
                        codcoligada = ".$codcoligada." AND
                        ((datainicio BETWEEN '".$dtini_ponto."' AND '".$dtfim_ponto."')  OR
                         (datafim    BETWEEN '".$dtini_ponto."' AND '".$dtfim_ponto."')) AND
                        chapa = '".$chapa."' 
                ";
                $qry = $this->dbrm->query($query);
                $resFerias = ($qry) ? $qry->getResultArray() : false;
                if ($resFerias && is_array($resFerias)) {
                    foreach ($resFerias as $idd => $value) {
                        $d_ini = $resFerias[$idd]['datainicio'] > $dtini_ponto ? $resFerias[$idd]['datainicio'] : $dtini_ponto;
                        $d_fim = $resFerias[$idd]['datafim'] < $dtfim_ponto ? $resFerias[$idd]['datafim'] : $dtfim_ponto;
                        $d_ini = substr($d_ini." ",0,10);
                        $d_fim = substr($d_fim." ",0,10);
                        $d_ini = DateTime::createFromFormat('Y-m-d', $d_ini);
                        $d_fim = DateTime::createFromFormat('Y-m-d', $d_fim);
                        $d_ferias = $d_ferias + date_diff($d_ini, $d_fim)->d + 1;
                        $datas_ferias = $datas_ferias.(($datas_ferias != '') ? ', ' : '');
                        $datas_ferias = $datas_ferias.date_format($d_ini, 'd/m/Y').' a '.date_format($d_fim, 'd/m/Y');
                    }
                }
                
                /* CALCULANDO DIAS DE ADMISSÃO E DEMISSAO NO PERIODO
                SELECT 
                    codcoligada,
                    chapa,
                    CASE WHEN dataadmissao >= '2024-12-16' THEN dataadmissao
                        ELSE null
                    END d_adm,
                    CASE WHEN datademissao BETWEEN '2024-12-16' AND '2025-01-15' THEN datademissao
                        ELSE null
                    END d_dem
                FROM PFUNC
				WHERE
					codcoligada = 1 AND
                    chapa = '050010841' 
                */
                $query = "
                    SELECT 
                        codcoligada,
                        chapa,
                        salario,
                        CASE WHEN dataadmissao >= '".$dtini_ponto."' THEN dataadmissao
                            ELSE null
                        END d_adm,
                        CASE WHEN datademissao BETWEEN '".$dtini_ponto."' AND '".$dtfim_ponto."' THEN datademissao
                            ELSE null
                        END d_dem
                    FROM PFUNC 
                    WHERE
                        codcoligada = ".$codcoligada." AND
                        chapa = '".$chapa."'   
                ";
                $qryAD = $this->dbrm->query($query);
                $resAdmDem = ($qryAD) ? $qryAD->getResultArray() : false;
                if ($resAdmDem && is_array($resAdmDem)) {
                    //admissao
                    $salario_base = $resAdmDem[0]['salario'];
                    $d_adm = $resAdmDem[0]['d_adm'];
                    if(!is_null($d_adm)) {
                        $d_ini = substr($dtini_ponto." ",0,10);
                        $d_fim = substr($d_adm." ",0,10);
                        $d_ini = DateTime::createFromFormat('Y-m-d', $d_ini);
                        $d_fim = DateTime::createFromFormat('Y-m-d', $d_fim);
                        $d_admissao = date_diff($d_ini, $d_fim)->d;
						$d_admissao = ($d_adm > $dtfim_ponto) ? 30 : $d_admissao;
                        $datas_admissao = $datas_admissao.(($datas_admissao != '') ? ', ' : '');
                        $datas_admissao = $datas_admissao.date_format($d_ini, 'd/m/Y').' a '.date_format($d_fim, 'd/m/Y');
                    }

                    // demissao
                    $d_dem = $resAdmDem[0]['d_dem'];
                    if(!is_null($d_dem)) {
                        $d_ini = substr($d_dem." ",0,10);
                        $d_fim = substr($dtfim_ponto." ",0,10);
                        $d_ini = DateTime::createFromFormat('Y-m-d', $d_ini);
                        $d_fim = DateTime::createFromFormat('Y-m-d', $d_fim);
                        $d_demissao = date_diff($d_ini, $d_fim)->d;
                        $datas_demissao = $datas_demissao.(($datas_demissao != '') ? ', ' : '');
                        $datas_demissao = $datas_demissao.date_format($d_ini, 'd/m/Y').' a '.date_format($d_fim, 'd/m/Y');
                    }
                }
                
                /* CALCULANDO DIAS ATESTADO / AFATAMENTO
                SELECT
                    f.codcoligada,
                    f.chapa,
                    f.codpessoa,
                    CASE WHEN dtinicio BETWEEN '2024-12-16' AND '2025-01-15' THEN dtinicio
                        ELSE '2024-12-16'
                    END d_ini,
                    CASE WHEN dtfinal BETWEEN '2024-12-16' AND '2025-01-15' THEN dtfinal
                        ELSE '2025-01-15'
                    END d_fim,
                    a.dtinicio,
                    a.dtfinal
                from pfunc f
                inner JOIN vatestado a on a.codpessoa = f.codpessoa
                WHERE (a.dtinicio BETWEEN '2024-12-16' AND '2025-01-15'
                OR    a.dtfinal BETWEEN '2024-12-16' AND '2025-01-15'
                OR 	(a.dtinicio < '2024-12-16' AND a.dtfinal > '2025-01-15' ) 
                OR 	(a.dtinicio <= '2025-01-15' AND a.dtfinal is NULL )) 
                AND CHAPA = '050007847'
                */
                $query = "
                    SELECT
                        f.codcoligada,
                        f.chapa,
                        f.codpessoa,
                        CASE WHEN a.dtinicio BETWEEN '".$dtini_ponto."' AND '".$dtfim_ponto."' THEN a.dtinicio
                            ELSE '".$dtini_ponto."' 
                        END d_ini,
                        CASE WHEN a.dtfinal BETWEEN '".$dtini_ponto."' AND '".$dtfim_ponto."' THEN a.dtfinal
                            ELSE '".$dtfim_ponto."' 
                        END d_fim,
                        a.dtinicio,
                        CASE WHEN a.dtfinal IS NOT NULL THEN a.dtfinal
                            ELSE '".$dtfim_ponto."' 
                        END dtfinal
                    FROM pfunc f
                    INNER JOIN vatestado a on a.codpessoa = f.codpessoa
                    WHERE 
                        f.codcoligada = ".$codcoligada." AND
                        ((a.dtinicio BETWEEN '".$dtini_ponto."' AND '".$dtfim_ponto."') OR
                         (a.dtfinal  BETWEEN '".$dtini_ponto."' AND '".$dtfim_ponto."') OR 
						 (a.dtinicio < '".$dtini_ponto."' AND a.dtfinal > '".$dtfim_ponto."') OR
                         ( (a.dtinicio <= '".$dtfim_ponto."') AND a.dtfinal is NULL) ) AND
                        f.chapa = '".$chapa."' 
                ";
                /*if($chapa=='050008006') {
                    echo '<pre> '.$query;
                }*/
                $qryAA = $this->dbrm->query($query);
                $resAA = ($qryAA) ? $qryAA->getResultArray() : false;
                $d_aa = 0;
                $is_afast = false;
                if ($resAA && is_array($resAA)) {
                    foreach ($resAA as $idd => $value) {
                        // calcula os dias
                        $d_ini = $resAA[$idd]['d_ini'];
                        $d_fim = $resAA[$idd]['d_fim'];
                        $d_ini = substr($d_ini." ",0,10);
                        $d_fim = substr($d_fim." ",0,10);
                        $d_ini = DateTime::createFromFormat('Y-m-d', $d_ini);
                        $d_fim = DateTime::createFromFormat('Y-m-d', $d_fim);
                        $d_aa = $d_aa + date_diff($d_ini, $d_fim)->d + 1;

                        // verifica se é afastamento ou não
                        $di = $resAA[$idd]['dtinicio'];
                        $df= $resAA[$idd]['dtfinal'];
                        $di= substr($di." ",0,10);
                        $df= substr($df." ",0,10);
                        $di = DateTime::createFromFormat('Y-m-d', $di);
                        $df = DateTime::createFromFormat('Y-m-d', $df);
                        if((date_diff($di, $df)->d + 1) > 15) { 
                            $is_afast = true;
                            $datas_afast = $datas_afast.(($datas_afast != '') ? ', ' : '');
                            $datas_afast = $datas_afast.date_format($d_ini, 'd/m/Y').' a '.date_format($d_fim, 'd/m/Y');
                        } else {
                            $datas_atestado = $datas_atestado.(($datas_atestado != '') ? ', ' : '');
                            $datas_atestado = $datas_atestado.date_format($d_ini, 'd/m/Y').' a '.date_format($d_fim, 'd/m/Y');
                        };
                    }
                }

                if($is_afast) {
                    $d_afast = $d_aa ;
                } else {
                    $d_atestado = $d_aa ;
                }
                
                /* CALCULANDO DIAS DE FALTAS
                SELECT * FROM amovfun 
                WHERE 
                    codeve IN (SELECT codigo FROM pevento WHERE CODIGOCALCULO = 8 AND valhordiaref = 'D' )
                AND inicioper = '2024-07-16' AND fimper = '2024-08-15'
                */
                $query = "
                    SELECT codcoligada, chapa, valor FROM amovfun 
                    WHERE 
                        codeve IN (SELECT codigo FROM pevento WHERE CODIGOCALCULO = 8 AND valhordiaref = 'D' )
                    AND inicioper = '".$dtini_ponto."' AND fimper = '".$dtfim_ponto."' 
                    AND codcoligada = ".$codcoligada."  
                    AND chapa = '".$chapa."'  
                ";
                $qryf = $this->dbrm->query($query);
                $resFaltas = ($qryf) ? $qryf->getResultArray() : false;
                if ($resFaltas && is_array($resFaltas)) {
                    foreach ($resFaltas as $idd => $value) {
                        $d_faltas = $d_faltas + $resFaltas[$idd]['valor'];
                    }
                }
                
                /* VERIFICA OS DIAS DAS FALTAS
                select distinct chapa,data from amovfundia
                WHERE 
                    codeve IN (SELECT codigo FROM pevento WHERE CODIGOCALCULO = 8 AND valhordiaref = 'D' )
                AND data >= '2024-07-16' AND data <= '2024-08-15'
                */
                $query = "
                    select distinct data from amovfundia
                WHERE 
                        codeve IN (SELECT codigo FROM pevento WHERE CODIGOCALCULO = 8 AND valhordiaref = 'D' )
                    AND data >= '".$dtini_ponto."' AND data <= '".$dtfim_ponto."' 
                    AND codcoligada = ".$codcoligada."  
                    AND chapa = '".$chapa."'  
                ";
                $qryf = $this->dbrm->query($query);
                $resFaltas = ($qryf) ? $qryf->getResultArray() : false;
                if ($resFaltas && is_array($resFaltas)) {
                    foreach ($resFaltas as $idd => $value) {
                        $data = $resFaltas[$idd]['data'];
                        $data = substr($data." ",0,10);
                        $data = DateTime::createFromFormat('Y-m-d', $data);
                        $datas_faltas = $datas_faltas.(($datas_faltas != '') ? ', ' : '');
                        $datas_faltas = $datas_faltas.date_format($data, 'd/m/Y');
                    }
                }
                
                // le premio para pegar parametros de calculo
                $query = " 
                    SELECT 
                        base_salario,
                        valor_base,
                        descricao
                    FROM zcrmportal_premios 
                    WHERE id = ".$id_premio." 
                ";
                //echo "<PRE> ".$query;
                $qryp = $this->dbportal->query($query);
                $resPremio = ($qryp) ? $qryp->getResultArray() : false;
                $desc_premio = '';
                $base_dias = 0;
                if ($resPremio && is_array($resPremio)) {
                    //admissao
                    $base_salario = $resPremio[0]['base_salario'];
                    $valor_base = $resPremio[0]['valor_base'];
                    $desc_premio = $resPremio[0]['descricao'];
                }

                // Verifica se tem base de dias na descricao
                if (!is_null($desc_premio)) {
                    $base_dias = substr($desc_premio,0,1) != 'D' ? $base_dias : intval(substr($desc_premio,1,2));
                }
                //echo '<pre> '.$base_dias;
                //exit();
                // le deflatores para calcular porcentagens parametros de calculo
                $defla_admissao = 0;
                $defla_demissao = 0;
                $defla_atestado = 0;
                $defla_afast = 0;
                $defla_ferias = 0;
                $defla_faltas = 0;

                $query = " 
                    SELECT * FROM zcrmportal_premios_deflatores 
                    WHERE id_premio = ".$id_premio." 
                ";
                $qrydf = $this->dbportal->query($query);
                $resDefla = ($qrydf) ? $qrydf->getResultArray() : false;
                if ($resDefla && is_array($resDefla)) {
                    for($i = 20; $i>=1; $i--) {
                        $defla = $resDefla[0]['deflator_'.$i];
                        $apenas_dias = $resDefla[0]['apenas_dias_'.$i];
                        $dias = $resDefla[0]['dias_'.$i];
                        $porcent = $resDefla[0]['porcent_'.$i];
                        if(!is_null($defla)) {
                            // admissão
                            if($defla=='Admissão' and $d_admissao > 0) {
                                $tem_defla_admissao = true;
                                if($apenas_dias=='S') {
                                    $defla_admissao = 0;
                                } else {
                                    if($d_admissao <= $dias) {
                                        $defla_admissao = $porcent;
                                    } else {
                                        if ($defla_admissao == 0) { $defla_admissao = 100; }
                                    }
                                }
                            }
                            // demissão
                            if($defla=='Demissão' and $d_demissao > 0) {
                                $tem_defla_demissao = true;
                                if($apenas_dias=='S') {
                                    $defla_demissao = 0;
                                } else {
                                    if($d_demissao <= $dias) {
                                        $defla_demissao = $porcent;
                                    } else {
                                        if ($defla_demissao == 0) { $defla_demissao = 100;}
                                    }
                                }
                            }
                            // férias
                            if($defla=='Férias' and $d_ferias > 0) {
                                $tem_defla_ferias = true;
                                if($apenas_dias=='S') {
                                    $defla_ferias = 0;
                                } else {
                                    if($d_ferias <= $dias) {
                                        $defla_ferias = $porcent;
                                    } else {
                                        if ($defla_ferias == 0) { $defla_ferias = 100; }
                                    }
                                }
                            }
                            // afastamentos
                            if($defla=='Afastamentos' and $d_afast > 0) {
                                $tem_defla_afast = true;
                                if($apenas_dias=='S') {
                                    $defla_afast = 0;
                                } else {
                                    if($d_afast <= $dias) {
                                        $defla_afast = $porcent;
                                    } else {
                                        if ($defla_afast == 0) { $defla_afast = 100; }
                                    }
                                }
                            }
                            // atestados
                            if($defla=='Atestados' and $d_atestado > 0) {
                                $tem_defla_atestado = true;
                                if($apenas_dias=='S') {
                                    $defla_atestado = 0;
                                } else {
                                    if($d_atestado <= $dias) {
                                        $defla_atestado = $porcent;
                                    } else {
                                        if ($defla_atestado == 0) { $defla_atestado = 100; }
                                    }
                                }
                            }
                            // faltas
                            if($defla=='Faltas' and $d_faltas > 0) {
                                $tem_defla_faltas = true;
                                if($apenas_dias=='S') {
                                    $defla_faltas = 0;
                                } else {
                                    if($d_faltas <= $dias) {
                                        $defla_faltas = $porcent;
                                    } else {
                                        if ($defla_faltas == 0) { $defla_faltas = 100; }
                                    }
                                }
                            }
                        }

                    }
                }

                if (!$tem_defla_admissao) {
                    $d_admissao = 0;
                    $defla_admissao = 0;
                    $datas_admissao = '';
                }

                if (!$tem_defla_demissao) {
                    $d_demissao = 0;
                    $defla_demissao = 0;
                    $datas_demissao = '';
                }

                if (!$tem_defla_ferias) {
                    $d_ferias = 0;
                    $defla_ferias = 0;
                    $datas_ferias = '';
                }

                if (!$tem_defla_afast) {
                    $d_afast = 0;
                    $defla_afast = 0;
                    $datas_afast = '';
                }

                if (!$tem_defla_atestado) {
                    $d_atestado = 0;
                    $defla_atestado = 0;
                    $datas_atestado = '';
                }

                if (!$tem_defla_faltas) {
                    $d_faltas = 0;
                    $defla_faltas = 0;
                    $datas_faltas = '';
                }

                // define se valor base é o salário ou o valor parametrizado no prêmio
                $valor_base = $base_salario == 'S' ? $salario_base : $valor_base;
                $valor_base = is_null($valor_base) ? 0 : $valor_base;
                
                $tot_dias_perdidos = $d_admissao + $d_demissao + $d_ferias + $d_afast + $d_atestado + $d_faltas;
                
                $tot_defla = $defla_admissao + $defla_demissao + $defla_ferias + $defla_afast + $defla_atestado + $defla_faltas;
                $tot_defla = ($tot_defla > 100) ? 100 : $tot_defla;
                $percent_defla = 100 - $tot_defla;

                if ($base_dias == 0) {
                    $resultado = $target * $realizado / 100;
                    $resultado = $resultado * $percent_defla / 100;
                    $tot_dias = 30 - $tot_dias_perdidos;
                    $tot_dias = ($tot_dias < 0) ? 0 : $tot_dias;
                    $resultado = ($tot_dias == 0) ? 0 : $resultado / 30 * $tot_dias;
                    $valor_premio = $valor_base * $resultado / 100;
                } else {
                    // Novo cálculo para transportes implementado em 06/12/2024 com base em dias
                    $valor_base_dias = $valor_base / $base_dias;  // valor em dias
                    //$tot_dias = 30 * $realizado / 100;
                    $tot_dias = $realizado;
                    $tot_dias = $tot_dias - $tot_dias_perdidos;
                    $tot_dias = ($tot_dias < 0) ? 0 : $tot_dias;
                    $valor_premio = $valor_base_dias * $tot_dias;
                    $valor_premio = $valor_premio * $percent_defla / 100;
                    $resultado = $valor_premio / $valor_base * 100;
                }
                // atualiza totais de dias
                $query = " 
                    UPDATE zcrmportal_premios_requisicao_chapas
                    SET
                        dias_faltas = ".$d_faltas.",
                        dias_admissao = ".$d_admissao.",
                        dias_demissao = ".$d_demissao.",
                        dias_ferias = ".$d_ferias.",
                        dias_atestado = ".$d_atestado.",
                        dias_afast = ".$d_afast.",
                        defla_faltas = ".$defla_faltas.",
                        defla_admissao = ".$defla_admissao.",
                        defla_demissao = ".$defla_demissao.",
                        defla_ferias = ".$defla_ferias.",
                        defla_atestado = ".$defla_atestado.",
                        defla_afast = ".$defla_afast.",
                        datas_faltas = '".$datas_faltas."',
                        datas_admissao = '".$datas_admissao."',
                        datas_demissao = '".$datas_demissao."',
                        datas_ferias = '".$datas_ferias."',
                        datas_atestado = '".$datas_atestado."',
                        datas_afast = '".$datas_afast."',
                        valor_base = ".$valor_base.", 
                        resultado = ".$resultado.", 
                        valor_premio = ".$valor_premio."  
                    WHERE ID = ".$id_chapa."    
                ";
                //echo '<PRE>'.$query;
                //exit();
                $this->dbportal->query($query);
                if($this->dbportal->affectedRows() <= 0) { $sucesso = false; }

                if($id_reqant <> $id_requisicao) {
                    $id_reqant = $id_requisicao;
                    // Atualiza status da requisição
                    $query = "UPDATE zcrmportal_premios_requisicao SET status = 'C' WHERE id = '{$id_requisicao}'";
                    $this->dbportal->query($query);
                    if ($this->dbportal->affectedRows() <= 0) { $sucesso = false; }
                    $query = "
                        UPDATE zcrmportal_premios_requisicao_aprovacao SET status = 'C' 
                        WHERE (status = 'A' OR status = 'C') AND id_requisicao = '{$id_requisicao}'";
                    $this->dbportal->query($query);
                    if ($this->dbportal->affectedRows() <= 0) { $sucesso = false; }
                }
            }
        }
      
        if ($sucesso) {
            return responseJson('success', 'Requisições calculadas');
        } else {
            return responseJson('error', 'Falhas ao calcular requisições');
        }

    }

// -----------------------------------------------------------------------
// Sincronizar Requisição aprovada pelo RH com o RM
// ------------------------------------------------------------------------

    public function SincRMrequisicaoRH($dados){
        
        $mescomp = $dados['mescomp'];
        $anocomp = $dados['anocomp'];
        $periodo = $dados['periodo'];
        $codevento = $dados['codevento'];
        $coligada = $_SESSION['func_coligada'];
        $id_usuario = $_SESSION['log_id'];

        $ids = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        if(substr($ids, -1)==",") { $ids = substr($ids, 0, -1); }
        
        // verifica se evento existe
        $qry = "
            SELECT CODIGO FROM PEVENTO
            WHERE CODCOLIGADA = ".$coligada." AND CODIGO = '".$codevento."' ";
        $query = $this->dbrm->query($qry);
        $row = $query->getRow();

        if (!isset($row)) {
            return responseJson('error', 'Esse código de evento não existe no RM. Sincronização não pode ser realizada.');
        }

        $query = "
            UPDATE zcrmportal_premios_requisicao_aprovacao 
            SET status = 'S'
            WHERE status = 'H' AND id_requisicao IN ({$ids})";
        $this->dbportal->query($query);

        /* REMOVIDO EM 08/11/2024 INCLUSÕES DIRETAS NO RM

        // Loop para processar chapas
        $sucesso = true;

        $chapas = "
            SELECT 
                 c.id
                ,c.tipo
                ,c.chapa
                ,c.id_coligada
                ,c.valor_premio
                ,r.id_acesso
                ,r.chapa_requisitor
                ,a.dtini_ponto
                ,a.dtfim_ponto
                ,a.id_premio
            FROM zcrmportal_premios_requisicao_chapas c
            LEFT JOIN zcrmportal_premios_requisicao r ON r.id = c.id_requisicao
            LEFT JOIN zcrmportal_premios_acessos a on a.id = r.id_acesso

            WHERE c.status = 'A' and c.id_requisicao = ".$id_requisicao;

        $qry = $this->dbportal->query($chapas);
		$resChapa = ($qry) ? $qry->getResultArray() : false;

        foreach ($resChapa as $idc => $value) {
            $chapa = $resChapa[$idc]['chapa'];
            $codcoligada = $resChapa[$idc]['id_coligada'];

            $sql = "
                SELECT chapa FROM pfperff
                WHERE chapa = '".$chapa."' 
                AND   codcoligada = ".$codcoligada." 
                AND   nroperiodo = ".$periodo." 
                AND   anocomp = ".$anocomp." 
                AND   mescomp = ".$mescomp." 
            ";
            $query = $this->dbrm->query($sql);
            $row = $query->getRow();

            if (!isset($row)) {
                return responseJson('error', 'Sincronização interrompida porque não existe envelope criado para a chapa '.$chapa.'. Verifique se os envelopes para o ano, mês e período escolhidos já foram inicializados.');
            }

        }

        // Iniciando as transações no RM
        $this->dbrm->transBegin();

        // sicronizar valores de premios no RM
        // inserindo ou alterando registro no PFFINAC
		if ($resChapa && is_array($resChapa)) {
            foreach ($resChapa as $idc => $value) {
                $id_chapa = $resChapa[$idc]['id'];
                $codcoligada = $resChapa[$idc]['id_coligada'];
                $chapa = $resChapa[$idc]['chapa'];
                $valor_premio = $resChapa[$idc]['valor_premio'];

                $sql = "
                    SELECT chapa FROM pffinanc
                    WHERE chapa = '".$chapa."' 
                    AND   codcoligada = ".$codcoligada." 
                    AND   nroperiodo = ".$periodo." 
                    AND   anocomp = ".$anocomp." 
                    AND   mescomp = ".$mescomp." 
                    AND   codevento = '".$codevento."' 
                ";
                $query = $this->dbrm->query($sql);
                $row = $query->getRow();

                if (isset($row)) {
                    // atualiza lançamento no PFFINANC
                    $sql = " 
                        UPDATE pffinanc
                        SET   valor = ".$valor_premio.",
                              valororiginal = ".$valor_premio.",
                              recmodifiedon = '".date('Y-m-d')."' 

                        WHERE chapa = '".$chapa."' 
                        AND   codcoligada = ".$codcoligada." 
                        AND   nroperiodo = ".$periodo." 
                        AND   anocomp = ".$anocomp." 
                        AND   mescomp = ".$mescomp." 
                        AND   codevento = '".$codevento."' 
                    ";
                    //echo '<PRE>'.$sql;
                    //exit();
                    try {
                        $this->dbrm->query($sql);
                    } catch (DatabaseException $e) {
                        $this->dbrm->transRollback();
                        return responseJson('error', 'Sincronização interrompida porque ao processar a chapa '.$chapa.', ocorreu o seguinte erro: '.$e->getMessage());
                    }

                } else {
                
                    // cria lançamento no PFFINANC
                    $sql = " 
                        INSERT INTO PFFINANC
                            (codcoligada, chapa, anocomp, mescomp, nroperiodo, codevento, valor, valororiginal, dtpagto, reccreatedon) 
                        VALUES
                            (".$codcoligada.", '".$chapa."', ".$anocomp.", ".$mescomp.", ".$periodo.", '".$codevento."', ".$valor_premio.", ".$valor_premio.", '".$datapgto."', '".date('Y-m-d')."')
                    ";
                    //echo '<PRE>'.$sql;
                    //exit();
                    try {
                        $this->dbrm->query($sql);
                    } catch (DatabaseException $e) {
                        $this->dbrm->transRollback();
                        return responseJson('error', 'Sincronização interrompida porque ao processar a chapa '.$chapa.', ocorreu o seguinte erro: '.$e->getMessage());
                    }
                }
            }
        }

        if ($this->dbrm->transStatus() === false) {
            $this->dbrm->transRollback();
            return responseJson('error', 'Falha ao sincronizar requisição');

        } else {
            $this->dbrm->transCommit();

        */
        
            // Atualiza status da requisição
            $query = "
                UPDATE zcrmportal_premios_requisicao 
                SET 
                    status = 'S',
                    dt_sincronismo = '".date('Y-m-d')."', 
                    codevento_sincronismo = '".$codevento."',
                    periodo_sincronismo = '".$periodo."',
                    user_id_sincronismo = ".$id_usuario.",
                    anocomp = ".$anocomp.",
                    mescomp = ".$mescomp.",
                    nroperiodo = ".$periodo." 
                WHERE id IN ({$ids})";

            $this->dbportal->query($query);

            if (str_contains($ids, ',')) {
                $msg = 'Requisições sincronizadas com o RM Folha';
            } else {
                $msg = 'Requisição sincronizada com o RM Folha';
            }
            return ($this->dbportal->affectedRows() > 0) 
                    ? responseJson('success', $msg)
                    : responseJson('error', 'Falha ao sincronizar requisições');

        /*}*/

    }

    // -----------------------------------------------------------------------------
    // Workflow para verificar requisições pendentes de aprovação e reenvio de email
    // -----------------------------------------------------------------------------
    public function Workflow(){

        $query = " 
            SELECT 
                r.id,
                FORMAT(r.dt_requisicao, 'dd/MM/yyyy') as dt_requisicao_br,
                r.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
                CASE WHEN r.tipo = 'M' THEN 'MENSAL'
                    ELSE 'COMPLEMENTAR'
                END tipo_requisicao,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br,
                r.chapa_requisitor,
                r.id_coligada,
                g.chapa_colab,
                g.nome_colab,
                o.chapa_coordenador as gestor_chapa,
                e.nome as gestor_nome,
                e.email gestor_email
            FROM zcrmportal_premios_requisicao r
            INNER JOIN zcrmportal_premios_requisicao_aprovacao o ON o.id_requisicao = r.id AND o.status = 'E'
            LEFT JOIN zcrmportal_premios_acessos a ON a.id = r.id_acesso
            LEFT JOIN zcrmportal_premios p ON p.id = a.id_premio
            LEFT JOIN gestor_chapa_premios g ON g.codcoligada = r.id_coligada AND r.chapa_requisitor = g.chapa_colab COLLATE Latin1_General_CI_AS
            LEFT JOIN email_chapa e ON e.codcoligada = r.id_coligada AND e.chapa = o.chapa_coordenador COLLATE Latin1_General_CI_AS
            WHERE r.status = 'E' AND r.id > 0 AND 
                (r.dt_envio_email IS NULL OR r.dt_envio_email < '".date('Y-m-d')."')" ;

        $result = $this->dbportal->query($query);

        //echo '<PRE> '.$query;
        //exit();

        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            $resFuncs = $result->getResultArray();
            foreach($resFuncs as $key => $Func): 
                $chapa_requisitor = $Func['chapa_requisitor'];
                $chapa_coordenador = $Func['gestor_chapa'];
                $nome_colab = $Func['nome_colab'];

                $gestor_nome = $Func['gestor_nome'];
                $gestor_email = $Func['gestor_email'];
                $nome_premio = $Func['nome_premio'];
                $per_ponto_br = $Func['per_ponto_br'];
                $dt_requisicao_br = $Func['dt_requisicao_br'];
                $id_requisicao = $Func['id'];
                $tipo_requisicao = $Func['tipo_requisicao'];

                $nome_colab = ($chapa_requisitor == $chapa_coordenador) ? 'Processos de RH' : $nome_colab;
        
                if($gestor_email != "") {
                    // REEnvio de email para gestor que deve aprovar a requisição
                    // echo $gestor_nome.' - '.$gestor_email.'<br>';
    
                    // Envio de email solicitando aprovação da requisicao
                    $mensagem = '
                    Prezado <strong>'.$gestor_nome.'</strong>, <br>
                    <br>
                    Venho por meio desta solicitar a aprovação da requisição do prêmio abaixo.<br>
                    <br>
                    Prêmio: '.$nome_premio.'<br>
                    Período: '.$per_ponto_br.'<br>
                    Tipo: '.$tipo_requisicao.'<br>
                    <br>
                    Segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>.<br>
                    <br>
                    Att.,<br>'.$nome_colab.'<br>'.date('d/m/Y').'
                    ';
        
                    $htmlEmail = templateEmail($mensagem, '95%');
        
                    $response = enviaEmail($gestor_email, 'REENVIANDO - Requisição de aprovação de Prêmio', $htmlEmail);                

                    $query = "UPDATE zcrmportal_premios_requisicao SET dt_envio_email = '".date('Y-m-d')."' WHERE id = '{$id_requisicao}'";

                    $this->dbportal->query($query);
                    if ($this->dbportal->affectedRows() <= 0) {
                        echo 'Falha ao atualizar data de envio para '.$gestor_nome.'<br>';
                    } else {
                        echo 'Enviado email para '.$gestor_nome.' - '.$gestor_email.'<br>';
                    }
                } else {
                    echo 'Falha no email para '.$gestor_nome.'<br>';
                }  
            endforeach;
            return true;

        } else {
            echo 'Nada a enviar'.'<br>';
            return false;
        }

    }    

}