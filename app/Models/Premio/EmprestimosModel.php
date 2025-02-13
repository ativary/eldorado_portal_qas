<?php
namespace App\Models\Premio;

use CodeIgniter\Model;
use Mpdf\Tag\S;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmprestimosModel extends Model {

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
    // Listar emprestimos
    // -------------------------------------------------------
    public function ListarEmprestimos($id = false){

        $mHierarquia = Model('HierarquiaModel');

        $ft_id = ($id) ? " AND e.id = '{$id}' " : "";
        $user_id = "";

        // Filtra por chapa ou admin apenas se $id false
        if($ft_id=="") {
            $coligada = $_SESSION['func_coligada'];

            $chapa = "'". util_chapa(session()->get('func_chapa'))['CHAPA'] ."'" ?? null;

            if($chapa){
            
                $chapasGestorSubstituto = $mHierarquia->getChapasGestorSubstituto($chapa);

                if($chapasGestorSubstituto){
                    foreach($chapasGestorSubstituto as $idx  => $value){
                        $chapa .= " , '" . $chapasGestorSubstituto[$idx]['chapa_gestor'] . "' ";
                    }
                }
            }
            //$user_id = ($_SESSION['log_id']) != 1 ? " AND e.de_chapa = '".$chapa."' AND e.id_coligada = ".$coligada : "";
            $user_id = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? "" : " AND e.de_chapa in (".$chapa.") ";
            $user_id = $user_id." AND e.id_coligada = ".$coligada;
        }

        $query = " 
            SELECT 
	            e.id,
	            e.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
	            e.de_chapa,
	            f1.nome AS de_nome_func,
	            e.para_chapa,
	            f2.nome AS para_nome_func,
	            e.dt_solicitacao,
	            e.chapa_colaborador,
	            f3.nome AS colaborador_nome_func,
	            e.dt_resposta,
	            e.motivo_recusa,
	            e.status,
	            e.id_usuario,
                a.dtini_ponto,
                a.dtfim_ponto,
                a.dtini_req,
                a.dtfim_req,
                FORMAT(a.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(a.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(a.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_emprestimos e
            LEFT JOIN ".DBRM_BANCO."..PFUNC f1 ON 
                f1.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            e.de_chapa = f1.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f2 ON 
                f2.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            e.para_chapa = f2.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f3 ON 
                f3.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND  
                e.chapa_colaborador = f3.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN zcrmportal_premios_acessos a ON
	            a.id = e.id_acesso
            LEFT JOIN zcrmportal_premios p ON
	            p.id = a.id_premio
            WHERE e.status <> 'I' AND e.id > 0 ".$ft_id.$user_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }    

    // -------------------------------------------------------
    // Pegar email por chapa + coligada
    // -------------------------------------------------------
    public function ListarEmailChapa($chapa, $codcoligada){

        $query = "
            SELECT EMAIL
            FROM EMAIL_CHAPA 
            WHERE 
                CODCOLIGADA = ".$codcoligada." AND 
                CHAPA = '".$chapa."' 
        ";

        //echo '<pre>'.$query;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray()[0]['EMAIL']
                : "";
                

    }

    // -------------------------------------------------------
    // Cadastrar Emprestimo
    // -------------------------------------------------------
    public function CadastrarEmprestimo($dados){

        $id_acesso = strlen(trim($dados['id_acesso'])) > 0 ? "{$dados['id_acesso']}" : "NULL";
        $de_chapa = strlen(trim($dados['de_chapa'])) > 0 ? "{$dados['de_chapa']}" : "NULL";
        $para_chapa = strlen(trim($dados['para_chapa'])) > 0 ? "{$dados['para_chapa']}" : "NULL";
        $chapa_colaborador = strlen(trim($dados['chapa_colaborador'])) > 0 ? "{$dados['chapa_colaborador']}" : "NULL";
        $dt_solicitacao = strlen(trim($dados['dt_solicitacao'])) > 0 ? "{$dados['dt_solicitacao']}" : "NULL";

        $nome_de_chapa = '';
        $nome_para_chapa = '';
        $nome_chapa_colaborador = '';
        
        $resFuncs = $this->ListarFunc();
        foreach($resFuncs as $key => $Func): 
            if($Func['CHAPA'] == $de_chapa) { $nome_de_chapa = $Func['NOME']; }
            if($Func['CHAPA'] == $para_chapa) { $nome_para_chapa = $Func['NOME']; }
            if($Func['CHAPA'] == $chapa_colaborador) { $nome_chapa_colaborador = $Func['NOME']; }
        endforeach;

        $resAcesso = $this->ListarAcessosPremios($id_acesso);
        $email_para_chapa = $this->ListarEmailChapa($para_chapa, $_SESSION['func_coligada']);
        $nome_premio = $resAcesso[0]['nome_premio'];
        $per_ponto_br = $resAcesso[0]['per_ponto_br'];

        if($email_para_chapa == "") {
            return responseJson('error', 'Falha ao criar solicitação de cedência. Email do destinatário não encontrado.');
        }
        
        $query = " INSERT INTO zcrmportal_premios_emprestimos
            (id_acesso, de_chapa, para_chapa, chapa_colaborador, dt_solicitacao, id_usuario, id_coligada) 
                VALUES
            ({$id_acesso}, '{$de_chapa}', '{$para_chapa}', '{$chapa_colaborador}', '{$dt_solicitacao}', {$_SESSION['log_id']}, {$_SESSION['func_coligada']})    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) {

            // Envio de email para gestor que deve aprovar o emprestimo
            $mensagem = '
                Prezado Gestor <strong>'.$nome_para_chapa.'</strong>, <br>
				<br>
				Venho por meio desta solicitar sua aprovação para a cedência de um colaborador de minha equipe para sua equipe de trabalho, durante o período especificado abaixo:<br>
                <br>
                Colaborador: '.$nome_chapa_colaborador.' ('.$chapa_colaborador.')<br>
                Prêmio: '.$nome_premio.'<br>
                Período: '.$per_ponto_br.'<br>
                <br>
                Segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>, onde você poderá responder à esta solicitação.<br>
				<br>
                Att.,<br>'.$nome_de_chapa.'<br>'.date('d/m/Y', strtotime($dt_solicitacao)).'
                ';
            
            $htmlEmail = templateEmail($mensagem, '95%');
            
            //$response = enviaEmail('alvaro.zaragoza@ativary.com', 'Solicitação de Cedência de Colaborador', $htmlEmail);
            $response = enviaEmail($email_para_chapa, 'Solicitação de Cedência de Colaborador', $htmlEmail);
            
            session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Solicitação de cedência criada com sucesso.')));
            return responseJson('success', 'Solicitação de cedência criada com sucesso', $this->dbportal->insertID());
    
        } else {

            return responseJson('error', 'Falha ao criar solicitação de cedência');

        }
              
    }

    // -------------------------------------------------------
    // Deletar
    // -------------------------------------------------------
    public function DeletarEmprestimo($dados = false){

        $query = "UPDATE zcrmportal_premios_emprestimos SET status = 'I' WHERE id = '{$dados['id']}'";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Solicitação de cedência excluída com sucesso')
                : responseJson('error', 'Falha ao excluir solicitação de cedência');

    }

    // -------------------------------------------------------
    // Aprovar cedência
    // -------------------------------------------------------
    public function AprovarEmprestimo($dados = false){

        $id = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        $resEmp = $this->ListarEmprestimos($id);
        $de_chapa = $resEmp[0]['de_chapa'];
        $de_nome_func = $resEmp[0]['de_nome_func'];
        $para_nome_func = $resEmp[0]['para_nome_func'];
        $chapa_colaborador = $resEmp[0]['chapa_colaborador'];
        $colaborador_nome_func = $resEmp[0]['colaborador_nome_func'];
        $nome_premio = $resEmp[0]['nome_premio'];
        $per_ponto_br = $resEmp[0]['per_ponto_br'];

        $email_de_chapa = $this->ListarEmailChapa($de_chapa, $_SESSION['func_coligada']);
        
        if($email_de_chapa == "") {
            return responseJson('error', 'Falha ao aprovar cedência. Email do destinatário não encontrado.');
        }
        
        $query = "UPDATE zcrmportal_premios_emprestimos SET status = 'E' WHERE id = '{$id}'";
        $this->dbportal->query($query);
        
        if ($this->dbportal->affectedRows() > 0) {
            // Envio de email informando aprovação do emprestimo
            $mensagem = '
            Prezado <strong>'.$de_nome_func.'</strong>, <br>
            <br>
            Venho por meio desta informar que sua solicitação de cedência de colaborador foi APROVADA.<br>
            <br>
            Colaborador: '.$colaborador_nome_func.' ('.$chapa_colaborador.')<br>
            Prêmio: '.$nome_premio.'<br>
            Período: '.$per_ponto_br.'<br>
            <br>
            Segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>.<br>
            <br>
            Att.,<br>'.$para_nome_func.'<br>'.date('d/m/Y').'
            ';

            $htmlEmail = templateEmail($mensagem, '95%');

            //$response = enviaEmail('alvaro.zaragoza@ativary.com', 'Solicitação de cedência de Colaborador', $htmlEmail);
            $response = enviaEmail($email_de_chapa, 'Solicitação de Cedência de Colaborador - APROVADA', $htmlEmail);

            return responseJson('success', 'Solicitação de cedência aprovada com sucesso');

        } else {
            return responseJson('error', 'Falha ao aprovar solicitação de cedência');
        }

    }

    // -------------------------------------------------------
    // Reprovar cedência
    // -------------------------------------------------------
    public function ReprovarEmprestimo($dados = false){

        $id = strlen(trim($dados['id'])) > 0 ? "{$dados['id']}" : "NULL";
        $resEmp = $this->ListarEmprestimos($id);
        $de_chapa = $resEmp[0]['de_chapa'];
        $de_nome_func = $resEmp[0]['de_nome_func'];
        $para_nome_func = $resEmp[0]['para_nome_func'];
        $chapa_colaborador = $resEmp[0]['chapa_colaborador'];
        $colaborador_nome_func = $resEmp[0]['colaborador_nome_func'];
        $nome_premio = $resEmp[0]['nome_premio'];
        $per_ponto_br = $resEmp[0]['per_ponto_br'];

        $email_de_chapa = $this->ListarEmailChapa($de_chapa, $_SESSION['func_coligada']);
        
        if($email_de_chapa == "") {
            return responseJson('error', 'Falha ao reprovar cedência. Email do destinatário não encontrado.');
        }
        
        $query = "
            UPDATE zcrmportal_premios_emprestimos 
            SET 
                status = 'R',
                motivo_recusa = '{$dados['motivo']}'

            WHERE id = '{$dados['id']}'
            ";
        $this->dbportal->query($query);

        if ($this->dbportal->affectedRows() > 0) {
            // Envio de email informando reprovação do emprestimo
            $mensagem = '
            Prezado <strong>'.$de_nome_func.'</strong>, <br>
            <br>
            Venho por meio desta informar que sua solicitação de cedência de colaborador foi REPROVADA.<br>
            <br>
            Colaborador: '.$colaborador_nome_func.' ('.$chapa_colaborador.')<br>
            Prêmio: '.$nome_premio.'<br>
            Período: '.$per_ponto_br.'<br>
            <br>
            Motivo: '.$dados['motivo'].'<br>
            <br>
            Segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>.<br>
            <br>
            Att.,<br>'.$para_nome_func.'<br>'.date('d/m/Y').'
            ';

            $htmlEmail = templateEmail($mensagem, '95%');

            $response = enviaEmail($email_de_chapa, 'Solicitação de Cedência de Colaborador - REPROVADA', $htmlEmail);

            return responseJson('success', 'Solicitação de cedência foi reprovada');

        } else {
            return responseJson('error', 'Falha ao reprovar solicitação de cedência');
        }

    }

    //---------------------------------------------
    // Listar Funcionários 
    //---------------------------------------------
    public function ListarFunc(){

        $where = "A.CODCOLIGADA = '".$_SESSION['func_coligada']."' ";
        $query = "
            SELECT
                A.CHAPA,
                A.NOME
            FROM
                ".DBRM_BANCO."..PFUNC A
            WHERE
                A.CODSITUACAO NOT IN ('D') AND 
                {$where}
            ORDER BY
                A.NOME
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    }

    //---------------------------------------------
    // Listar Funcionários de um gestor (por chapa)
    //---------------------------------------------
    public function ListarFuncGestor($chapa_gestor){

        $where = "CODCOLIGADA = '".$_SESSION['func_coligada']."' AND CODSITUACAO_COLAB <> 'D'";
        $query = "
            SELECT
                CHAPA_COLAB CHAPA,
                NOME_COLAB  NOME
            FROM
                GESTOR_CHAPA_PREMIOS
            WHERE
                GESTOR_CHAPA = '".$chapa_gestor."' AND 
                {$where}
            ORDER BY
                NOME_COLAB 
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    //---------------------------------------------
    // Listar Gestores
    //---------------------------------------------
    public function ListarGestores(){

        $query = "
            SELECT
                DISTINCT
                GESTOR_CHAPA CHAPA,
                GESTOR_NOME  NOME
            FROM
                GESTOR_CHAPA_PREMIOS
            WHERE 
                CODCOLIGADA = ".$_SESSION['func_coligada']." AND 
				CODSITUACAO_GESTOR <> 'D' AND 
				GESTOR_CHAPA IN (SELECT COORD_CHAPA FROM GESTORES_ABAIXO_COORDENADOR WHERE COL_ID = ".$_SESSION['func_coligada'].")
            ORDER BY
                GESTOR_NOME
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Listar Acessos de Premios Ativos
    // -------------------------------------------------------
    public function ListarAcessosPremios($id = false){

        $ft_id = ($id) ? " AND a.id = '{$id}' " : "";

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
            
            WHERE a.status = 'A' 
              AND a.dtfim_req >= Convert(date, getdate()) 
              AND a.dtini_req <= Convert(date, getdate()) 
              AND (p.dt_vigencia IS NULL OR Convert(date, p.dt_vigencia) >= Convert(date, getdate())) 
              AND p.codcoligada = '".$_SESSION['func_coligada']."' ".$ft_id;
    
        //echo '<PRE> '.$query;
        //exit();

        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    } 

    // -----------------------------------------------------------------------------
    // Workflow para verificar emprestimos pendentes de aprovação e reenvio de email
    // -----------------------------------------------------------------------------
    public function Workflow(){

        $query = " 
            SELECT 
	            e.id,
	            e.id_coligada,
	            e.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
	            e.de_chapa,
	            f1.nome AS de_nome_func,
	            e.para_chapa,
	            f2.nome AS para_nome_func,
	            e.dt_solicitacao,
	            e.chapa_colaborador,
	            f3.nome AS colaborador_nome_func,
	            e.dt_resposta,
	            e.motivo_recusa,
	            e.status,
	            e.id_usuario,
                a.dtini_ponto,
                a.dtfim_ponto,
                a.dtini_req,
                a.dtfim_req,
                FORMAT(a.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(a.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(a.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_emprestimos e
            LEFT JOIN ".DBRM_BANCO."..PFUNC f1 ON 
                f1.CODCOLIGADA = e.id_coligada AND 
	            e.de_chapa = f1.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f2 ON 
                f2.CODCOLIGADA = e.id_coligada AND 
	            e.para_chapa = f2.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f3 ON 
                f3.CODCOLIGADA = e.id_coligada AND  
                e.chapa_colaborador = f3.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN zcrmportal_premios_acessos a ON
	            a.id = e.id_acesso
            LEFT JOIN zcrmportal_premios p ON
	            p.id = a.id_premio
            WHERE e.status = 'A' AND e.id > 0 AND 
                  (e.dt_envio_email IS NULL OR e.dt_envio_email < '".date('Y-m-d')."')" ;

        //echo '<PRE> '.$query;
        //exit();

        $result = $this->dbportal->query($query);
        if ($result->getNumRows() > 0) {
            $resFuncs = $result->getResultArray();
            foreach($resFuncs as $key => $Func): 
                $id_acesso = $Func['id'];
                $codcoligada = $Func['id_coligada'];
                $para_chapa = $Func['para_chapa'];
                $nome_de_chapa = $Func['de_nome_func'];
                $nome_para_chapa = $Func['para_nome_func'];
                $nome_chapa_colaborador = $Func['colaborador_nome_func'];
                $nome_premio = $Func['nome_premio'];
                $per_ponto_br = $Func['per_ponto_br'];
                $chapa_colaborador = $Func['chapa_colaborador'];
                $dt_solicitacao = "{$Func['dt_solicitacao']}";
                $email_para_chapa = $this->ListarEmailChapa($para_chapa, $codcoligada);
                
                if($email_para_chapa != "") {
                    // REEnvio de email para gestor que deve aprovar o emprestimo
                    // echo $nome_para_chapa.' - '.$email_para_chapa.'<br>';

                    $mensagem = '
                        Prezado Gestor <strong>'.$nome_para_chapa.'</strong>, <br>
                        <br>
                        Venho por meio desta solicitar a cedência de um colaborador de minha equipe para sua equipe de trabalho, durante o período especificado abaixo:<br>
                        <br>
                        Colaborador: '.$nome_chapa_colaborador.' ('.$chapa_colaborador.')<br>
                        Prêmio: '.$nome_premio.'<br>
                        Período: '.$per_ponto_br.'<br>
                        <br>
                        Segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>, onde você poderá responder à esta solicitação.<br>
                        <br>
                        Att.,<br>'.$nome_de_chapa.'<br>'.date('d/m/Y', strtotime($dt_solicitacao)).'
                    ';
                    
                    $htmlEmail = templateEmail($mensagem, '95%');
                        
                    //$response = enviaEmail('alvaro.zaragoza@ativary.com', 'Solicitação de cedência de Colaborador', $htmlEmail);
                    $response = enviaEmail($email_para_chapa, 'REENVIANDO - Solicitação de Cedência de Colaborador', $htmlEmail);

                    $query = "UPDATE zcrmportal_premios_emprestimos SET dt_envio_email = '".date('Y-m-d')."' WHERE id = '{$id_acesso}'";

                    $this->dbportal->query($query);
                    if ($this->dbportal->affectedRows() <= 0) {
                        echo 'Falha ao atualizar data de envio para '.$nome_para_chapa.'<br>';
                    } else {
                        echo 'Enviado email para '.$nome_para_chapa.' - '.$email_para_chapa.'<br>';
                    }   

                } else {
                    echo 'Falha no envio de email para '.$nome_para_chapa.'<br>';
                    $query = "UPDATE zcrmportal_premios_emprestimos SET dt_envio_email = '".date('Y-m-d')."' WHERE id = '{$id_acesso}'";

                    $this->dbportal->query($query);
                    if ($this->dbportal->affectedRows() <= 0) {
                        echo 'Falha ao atualizar data de envio para '.$nome_para_chapa.'<br>';
                    }   
                }  
            endforeach;
            return true;

        } else {
            echo 'Nada a enviar';
            return false;
        }


    }    

    // ------------------------------------------------------------------------
    // Listar emprestimos para aprovação do usuário logado ou de todos se admin
    // ------------------------------------------------------------------------
    public function ListarEmprestimosAprovar(){

        $mHierarquia = Model('HierarquiaModel');
        
        $coligada = $_SESSION['func_coligada'];

        $chapa = "'". util_chapa(session()->get('func_chapa'))['CHAPA'] ."'" ?? null;

        if($chapa){
        
            $chapasGestorSubstituto = $mHierarquia->getChapasGestorSubstituto($chapa);

            if($chapasGestorSubstituto){
                foreach($chapasGestorSubstituto as $idx  => $value){
                    $chapa .= " , '" . $chapasGestorSubstituto[$idx]['chapa_gestor'] . "' ";
                }
            }
        }
        
        //$user_id = ($_SESSION['log_id']) != 1 ? " AND e.para_chapa = '".$chapa."' AND e.id_coligada = ".$coligada : "";
        $user_id = ($_SESSION['log_id'] == 1 or $_SESSION['rh_master'] == 'S') ? "" : " AND e.para_chapa in (".$chapa.") ";
        $user_id = $user_id." AND e.id_coligada = ".$coligada;

        $query = " 
            SELECT 
	            e.id,
	            e.id_acesso,
                a.id_premio,
                p.nome AS nome_premio,
	            e.de_chapa,
	            f1.nome AS de_nome_func,
	            e.para_chapa,
	            f2.nome AS para_nome_func,
	            e.dt_solicitacao,
	            e.chapa_colaborador,
	            f3.nome AS colaborador_nome_func,
	            e.dt_resposta,
	            e.motivo_recusa,
	            e.status,
	            e.id_usuario,
                a.dtini_ponto,
                a.dtfim_ponto,
                a.dtini_req,
                a.dtfim_req,
                FORMAT(a.dtini_req, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_req , 'yyyy-MM-dd') AS per_req_sql,
                FORMAT(a.dtini_req, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_req, 'dd/MM/yyyy') AS per_req_br,
                FORMAT(a.dtini_ponto, 'yyyy-MM-dd')+'-'+FORMAT(a.dtfim_ponto , 'yyyy-MM-dd') AS per_ponto_sql,
                FORMAT(a.dtini_ponto, 'dd/MM/yyyy')+' a '+FORMAT(a.dtfim_ponto, 'dd/MM/yyyy') AS per_ponto_br
            FROM zcrmportal_premios_emprestimos e
            LEFT JOIN ".DBRM_BANCO."..PFUNC f1 ON 
                f1.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            e.de_chapa = f1.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f2 ON 
                f2.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND 
	            e.para_chapa = f2.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN ".DBRM_BANCO."..PFUNC f3 ON 
                f3.CODCOLIGADA = '".$_SESSION['func_coligada']."' AND  
                e.chapa_colaborador = f3.CHAPA COLLATE Latin1_General_CI_AS
            LEFT JOIN zcrmportal_premios_acessos a ON
	            a.id = e.id_acesso
            LEFT JOIN zcrmportal_premios p ON
	            p.id = a.id_premio
            WHERE e.status = 'A' AND e.id > 0 ".$user_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : [];

    }    


}