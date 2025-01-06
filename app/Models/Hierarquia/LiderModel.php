<?php
namespace App\Models\Hierarquia;
use CodeIgniter\Model;

class LiderModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $log_id;
    private $now;
    private $coligada;
    private $chapaGestor;
    
    public function __construct()
    {
        $this->dbportal     = db_connect('dbportal');
        $this->dbrm         = db_connect('dbrm');
        $this->log_id       = session()->get('log_id');
        $this->coligada     = session()->get('func_coligada');
        $this->now          = date('Y-m-d H:i:s');
        $this->chapaGestor  = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        // self::inativaLider();
    }

    public function listaHierarquiaLider($chapaGestor)
    {

        $id_hierarquia = self::pegaIdHierarquiaGestor($chapaGestor);

        $query = "
            SELECT 
                b.CHAPA,
                b.NOME,
                a.id,
                CASE WHEN a.nivel = 1 THEN 'S' ELSE 'N' END APROVADOR,
                COUNT(c.chapa) QTDE_FUNCIONARIOS,
                a.perini PERINI,
                a.perfim PERFIM,
                a.operacao OPERACAO
                
            FROM
                zcrmportal_hierarquia_lider_ponto a
                LEFT JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS
                LEFT JOIN zcrmportal_hierarquia_lider_func_ponto c ON c.id_lider = a.id AND c.inativo IS NULL
                
            WHERE
                    a.inativo IS NULL
                AND (
                    '".date('Y-m-d')."' BETWEEN a.perini AND COALESCE(a.perfim, '2090-12-31')
                    OR
                    a.perini > '".date('Y-m-d')."'
                )
                AND a.id_hierarquia = '{$id_hierarquia}'
                
            GROUP BY
                b.CHAPA,
                b.NOME,
                a.id,
                a.perini,
                a.perfim,
                a.operacao,
                CASE WHEN a.nivel = 1 THEN 'S' ELSE 'N' END
        ";
        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;

    }

    public function listaSecaoGestor($chapaGestor = null, $rh = false)
    {

        $chapaGestor =  ($chapaGestor == null) 
                        ? $this->chapaGestor 
                        : $chapaGestor;

        $response = $this->dbportal
            ->table('zcrmportal_hierarquia_chapa hg')
            ->select('f.codsecao, f.descricao')
            ->join('zcrmportal_hierarquia_frentetrabalho hf', 'hf.id_hierarquia = hg.id_hierarquia AND hf.inativo IS NULL')
            ->join('zcrmportal_frente_trabalho f', 'f.id = hf.id_frentetrabalho')
            ->where('hg.coligada', $this->coligada);
            
        if(!$rh) $response = $response->where('hg.chapa', $chapaGestor);
        
        $response = $response->where('hg.inativo IS NULL')
            ->orderBy('f.descricao ASC')
            ->get();
        
        return  ($response) 
                ? $response->getResultArray() 
                : false;

    }

    public function listaFuncaoGestor($chapaGestor = null, $rh = false)
    {

        $secaoGestor = self::listaSecaoGestor($chapaGestor, $rh);

        $arraySecao = "AND 1 = 2";
        if($secaoGestor){
            $arraySecao = '';
            foreach($secaoGestor as $Secao){
                $arraySecao .= "'{$Secao['codsecao']}',";
            }
            $arraySecao = " AND B.CODSECAO IN (".rtrim($arraySecao,',').") ";
        }

        if($rh) $arraySecao = "";

        $query = "
            SELECT
                A.CODIGO,
                A.NOME
            FROM
                PFUNCAO A
                INNER JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODFUNCAO = A.CODIGO AND B.CODSITUACAO NOT IN ('D')
            WHERE
                A.CODCOLIGADA = {$this->coligada}
                {$arraySecao}
            GROUP BY
                A.CODIGO,
                A.NOME
            ORDER BY
                A.NOME
        ";
        // exit('<pre>'.$query);
        $response = $this->dbrm->query($query);
        return  ($response) 
                ? $response->getResultArray() 
                : false;

    }

    public function listaFuncionariosGestor($request)
    {

        $chapaGestor =  (($request['chapaGestor'] ?? null) == null) 
                        ? $this->chapaGestor 
                        : $request['chapaGestor'];
        
        if(!$request['rh']){
            $not_exists = " AND NOT EXISTS(SELECT chapa FROM ".DBPORTAL_BANCO."..zcrmportal_hierarquia_lider_ponto WHERE chapa = A.CHAPA COLLATE Latin1_General_CI_AS AND coligada = A.CODCOLIGADA AND inativo IS NULL) ";
            $secaoGestor = self::listaSecaoGestor($chapaGestor);
            $in_secao = "";
            if($secaoGestor){
                foreach($secaoGestor as $key => $SecaoGestor){
                    $in_secao .= "'{$SecaoGestor['codsecao']}',";
                }
                $in_secao = " AND A.CODSECAO IN (".rtrim($in_secao,",").") ";
                $in_secao .= " AND A.CHAPA != '{$chapaGestor}' ";
            }

            if($in_secao == "") return false;
        }else{
            $in_secao = "";
            $not_exists = "";
        }
        
        $query = " SELECT TOP 1000 A.CHAPA, A.NOME, B.NOME NOMEFUNCAO FROM PFUNC A INNER JOIN PFUNCAO B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO WHERE A.CODCOLIGADA = '{$this->coligada}' AND A.CODSITUACAO NOT IN ('D') {$in_secao} AND (A.NOME LIKE ? OR A.CHAPA LIKE ?) {$not_exists} ORDER BY A.NOME ASC ";
        $result = $this->dbrm->query($query, array("%".$request['keywordLider']."%","%".$request['keywordLider']."%"));
        
        return  ($result) 
                ? $result->getResultArray() 
                : false;
        

    }

    public function listaFuncionariosSecao($request)
    {

        $chapaGestor =  (($request['chapaGestor'] ?? null) == null) 
                        ? $this->chapaGestor 
                        : $request['chapaGestor'];

        $in_secao = " AND 1 = 2 ";

        if(!$request['rh']){
            if(($request['codsecao'] ?? null) == null){
                $secaoGestor = self::listaSecaoGestor($chapaGestor);
                if($secaoGestor){
                    $in_secao = "";
                    foreach($secaoGestor as $key => $SecaoGestor){
                        $in_secao .= "'{$SecaoGestor['codsecao']}',";
                    }
                    $in_secao = " AND CODSECAO IN (".rtrim($in_secao,",").")  ";
                }
            }else{
                $in_secao = "";
                foreach($request['codsecao'] as $codSecao){
                    $in_secao .= "'{$codSecao}',";
                }
                $in_secao = " AND CODSECAO IN (".rtrim($in_secao,",").")  ";
            }
            $in_secao .= " AND CHAPA != '{$chapaGestor}' ";

            if($in_secao == "") return false;
        }else{
            $in_secao = "";
            if(($request['codsecao'] ?? null) != null){
                foreach($request['codsecao'] as $codSecao){
                    $in_secao .= "'{$codSecao}',";
                }
                $in_secao = " AND CODSECAO IN (".rtrim($in_secao,",").") ";
            }
        }

        $in_funcao = "  ";

        if(!$request['rh']){
            if(($request['codfuncao'] ?? null) != null){
                $in_funcao = "";
                foreach($request['codfuncao'] as $codFuncao){
                    $in_funcao .= "'{$codFuncao}',";
                }
                $in_funcao = " AND CODFUNCAO IN (".rtrim($in_funcao,",").")  ";
            }
        }else{
            $in_funcao = "";
            if(($request['codfuncao'] ?? null) != null){
                foreach($request['codfuncao'] as $codFuncao){
                    $in_funcao .= "'{$codFuncao}',";
                }
                $in_funcao = " AND CODFUNCAO IN (".rtrim($in_funcao,",").") ";
            }
        }

        $query = " 
            SELECT 
                TOP 1000 
                A.CHAPA, 
                A.NOME,
                B.NOME NOMEFUNCAO
            FROM 
                PFUNC A
                INNER JOIN PFUNCAO B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
            WHERE 
                    A.CODCOLIGADA = '{$this->coligada}' {$in_secao} {$in_funcao}
                AND A.CODSITUACAO NOT IN ('D') 
                AND NOT EXISTS(SELECT chapa FROM ".DBPORTAL_BANCO."..zcrmportal_hierarquia_lider_func_ponto WHERE chapa = A.CHAPA COLLATE Latin1_General_CI_AS AND coligada = A.CODCOLIGADA AND inativo IS NULL)
            ORDER BY NOME
        ";
        $result = $this->dbrm->query($query);

        return  ($result) 
                ? $result->getResultArray() 
                : false;

    }

    public function cadastrarLider($request)
    {

        $chapaLider         = $request['chapaLider'] ?? '';
        $chapaFuncionarios  = $request['chapaFuncionarios'] ?? array();
        $chapaGestor        = $request['chapaGestor'] ?? '';
        $periodoInicio      = $request['periodoInicio'] ?? '';
        $periodoTermino     = $request['periodoTermino'] ?? '';
        $rh                 = $request['rh'];
        $operacao           = $request['operacao'] ?? 'NULL';

        if($chapaLider == ''){return responseJson('error', '<b>Líder</b> não selecionado.');}
        if(count($chapaFuncionarios) <= 0){return responseJson('error', 'Nenhuma funcionário atribuido ao <b>Líder</b>.');}
        if($chapaGestor == ''){return responseJson('error', '<b>Gestor</b> não identificado.');}
        if($periodoInicio == ''){return responseJson('error', '<b>Período de início</b> não informado.');}
        if($periodoTermino != ''){
            if($periodoTermino < $periodoInicio){return responseJson('error', '<b>Período de término</b> não poder ser menor que o <b>período de início.</b>');}
        }

        $id_hierarquia = self::pegaIdHierarquiaGestor($chapaGestor);
        if(!$id_hierarquia){return responseJson('error', '<b>Gestor</b> não localizado em nenhuma Hierarquia.');}

        $checkLiderJaCadastrado = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_ponto')
            ->select('chapa')
            ->where('chapa', $chapaLider)
            ->where('coligada', $this->coligada)
            ->where('inativo IS NULL')
            ->get();
        if($checkLiderJaCadastrado->getNumRows() > 0 && !$rh) return responseJson('error', '<b>Líder</b> já cadastrado.');

        $insert = [
            'id_hierarquia' => $id_hierarquia,
            'chapa'         => $chapaLider,
            'coligada'      => $this->coligada,
            'nivel'         => 2,
            'perini'        => $periodoInicio,
            'operacao'      => $operacao,
            'perfim'        => ($periodoTermino != '') ? $periodoTermino : null,
            'usucad'        => $this->log_id,
            'dtcad'         => $this->now
        ];

        $response = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_ponto')
            ->insert($insert);

        if($this->dbportal->affectedRows() > 0){
            
            $id_lider = $this->dbportal->insertID();

            foreach($chapaFuncionarios as $chapaFuncionario){

                $insertFuncionario = [
                    'chapa'     => $chapaFuncionario,
                    'coligada'  => $this->coligada,
                    'id_lider'  => $id_lider,
                    'usucad'    => $this->log_id,
                    'dtcad'     => $this->now
                ];

                self::cadastrarFuncionarioLider($insertFuncionario);

            }

            return responseJson('success', 'Líder cadastrado com sucesso.', id($id_lider));
        }

        return responseJson('error', 'Falha ao cadastrar Líder');

    }

    public function cadastrarLiderExcecao($request)
    {

        $chapaColaborador   = $request['chapaColaborador'] ?? '';
        $chapaGestor        = $request['chapaGestor'] ?? '';
        $idLider            = $request['idLider'] ?? '';
        $periodoInicio      = $request['periodoInicio'] ?? '';
        $periodoTermino     = $request['periodoTermino'] ?? '';
        $rh                 = $request['rh'];

        if($chapaColaborador == ''){return responseJson('error', '<b>Colaborador</b> não selecionado.');}
        if($chapaGestor == ''){return responseJson('error', '<b>Gestor</b> não identificado.');}
        if($idLider == ''){return responseJson('error', '<b>Líder</b> não identificado.');}
        if($periodoInicio == ''){return responseJson('error', '<b>Período de início</b> não informado.');}
        if($periodoTermino != ''){
            if($periodoTermino < $periodoInicio){return responseJson('error', '<b>Período de término</b> não poder ser menor que o <b>período de início.</b>');}
        }

        $id_hierarquia = self::pegaIdHierarquiaGestor($chapaGestor);
        if(!$id_hierarquia){return responseJson('error', '<b>Gestor</b> não localizado em nenhuma Hierarquia.');}

        $checkLiderJaCadastrado = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_excecao_ponto')
            ->select('chapa')
            ->where('chapa', $chapaColaborador)
            ->where('coligada', $this->coligada)
            ->where('id_lider', $idLider)
            ->where('inativo IS NULL')
            ->get();
        if($checkLiderJaCadastrado->getNumRows() > 0) return responseJson('error', '<b>Colaborador</b> já cadastrado neste Líder.');

        $insert = [
            'id_lider'      => $idLider,
            'id_hierarquia' => $id_hierarquia,
            'chapa'         => $chapaColaborador,
            'perini'        => $periodoInicio,
            'perfim'        => ($periodoTermino != '') ? $periodoTermino : null,
            'coligada'      => $this->coligada,
            'usucad'        => $this->log_id,
            'dtcad'         => $this->now
        ];

        $response = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_excecao_ponto')
            ->insert($insert);

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Líder Exceção cadastrado com sucesso.');
        }

        return responseJson('error', 'Falha ao cadastrar Líder Exceção');

    }

    private function cadastrarFuncionarioLider($request)
    {

        $checkChapaOutroLider = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_func_ponto')
            ->select('chapa')
            ->where('chapa', $request['chapa'])
            ->where('coligada', $this->coligada)
            ->where('inativo IS NULL')
            ->get();
        if($checkChapaOutroLider->getNumRows() > 0) return false;

        $response = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_func_ponto')
            ->insert($request);

        return  ($this->dbportal->affectedRows() > 0)
                ? true
                : false;

    }

    private function pegaIdHierarquiaGestor($chapaGestor)
    {

        $response = $this->dbportal
            ->table('zcrmportal_hierarquia_chapa hg')
            ->selectMax('hg.id_hierarquia')
            ->join('zcrmportal_hierarquia h', 'h.id = hg.id_hierarquia AND h.inativo IS NULL')
            ->where('hg.chapa', $chapaGestor)
            ->where('hg.inativo IS NULL')
            ->get();
        
        return  ($response) 
                ? $response->getResultArray()[0]['id_hierarquia']
                : false;

    }

    public function listaGestores()
    {
        
        $query = "
            SELECT 
                b.CHAPA,
                b.NOME,
                a.id_hierarquia
            FROM 
                zcrmportal_hierarquia_chapa a
                JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS	
            WHERE 
                    a.inativo IS NULL
                and a.coligada = '{$this->coligada}'
            ORDER BY
                b.NOME
        ";
        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function listaDadosHierarquiaLider($id)
    {

        $id = (int)$id;

        $query = "
            SELECT
                a.*,
                b.NOME nome_lider,
                d.CHAPA chapa_gestor,
                d.NOME nome_gestor
            FROM
                zcrmportal_hierarquia_lider_ponto a
                LEFT JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS
                LEFT JOIN zcrmportal_hierarquia_chapa c ON a.id_hierarquia = c.id_hierarquia AND c.inativo IS NULL 
                LEFT JOIN ".DBRM_BANCO."..PFUNC d ON d.CODCOLIGADA = c.coligada AND d.CHAPA = c.chapa COLLATE Latin1_General_CI_AS
                
            WHERE
                    a.id = {$id}
                AND a.inativo IS NULL
                AND a.coligada = '{$this->coligada}'
        ";
        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;

    }

    public function listaFuncionariosLider($id_lider)
    {

        $id = (int)$id_lider;

        $query = "
            SELECT
                b.CHAPA,
                b.NOME,
                c.NOME NOMEFUNCAO
            FROM
                zcrmportal_hierarquia_lider_func_ponto a (NOLOCK)
                LEFT JOIN ".DBRM_BANCO."..PFUNC b (NOLOCK) ON b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS AND b.CODCOLIGADA = a.coligada
                LEFT JOIN ".DBRM_BANCO."..PFUNCAO c ON c.CODCOLIGADA = b.CODCOLIGADA AND c.CODIGO =  b.CODFUNCAO
            WHERE
                    a.id_lider = {$id}
                AND a.inativo IS NULL
                AND a.coligada = '{$this->coligada}'
            ORDER BY 
                b.NOME
        ";
// echo '<pre>';echo $query;
//         exit();
        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;

    }

    public function listaLiderExcecao($id_lider)
    {

        $id = (int)$id_lider;

        $query = "
            SELECT
                a.id,
                a.perini,
                a.perfim,
                b.CHAPA,
                b.NOME
            FROM
                zcrmportal_hierarquia_lider_excecao_ponto a
                LEFT JOIN ".DBRM_BANCO."..PFUNC b ON b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS AND b.CODCOLIGADA = a.coligada
            WHERE
                    a.id_lider = {$id}
                AND a.inativo IS NULL
            ORDER BY 
                b.NOME
        ";
        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;

    }

    public function tipoAprovador($request)
    {
        if(!$request['rh']) responseJson('error', 'Sem permissão de.');
        
        $update = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_ponto')
            ->set('nivel', ($request['tipoAprovador'] == 'S') ? 1 : 2)
            ->set('usualt', $this->log_id)
            ->set('dtalt', $this->now)
            ->where('id', $request['idLider'])
            ->update();
            
        if($this->dbportal->affectedRows() > 0){
            
            if($request['tipoAprovador'] == 'S'){

                $this->dbportal->query("
                    INSERT INTO zcrmportal_usuarioperfil
                        SELECT 
                            D.id,
                            E.id
                        FROM 
                            zcrmportal_hierarquia_lider_ponto A
                            INNER JOIN ".DBRM_BANCO."..PFUNC B ON B.CODCOLIGADA = A.coligada AND B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
                            INNER JOIN ".DBRM_BANCO."..PPESSOA C ON C.CODIGO = B.CODPESSOA
                            INNER JOIN zcrmportal_usuario D ON D.login = C.CPF COLLATE Latin1_General_CI_AS
                            LEFT JOIN zcrmportal_perfil E ON E.nome = 'PONTO_APROVA'
                        WHERE 
                            A.id = {$request['idLider']}
                            AND NOT EXISTS(SELECT * FROM zcrmportal_usuarioperfil WHERE id_usuario = D.id AND id_perfil = E.id)
                ");

            }else{
                $this->dbportal->query("
                    DELETE FROM zcrmportal_usuarioperfil WHERE

                    id_usuario = (
                        SELECT 
                            max(D.id)
                        FROM 
                            zcrmportal_hierarquia_lider_ponto A
                            INNER JOIN ".DBRM_BANCO."..PFUNC B ON B.CODCOLIGADA = A.coligada AND B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
                            INNER JOIN ".DBRM_BANCO."..PPESSOA C ON C.CODIGO = B.CODPESSOA
                            INNER JOIN zcrmportal_usuario D ON D.login = C.CPF COLLATE Latin1_General_CI_AS
                            LEFT JOIN zcrmportal_perfil E ON E.nome = 'PONTO_APROVA'
                        WHERE 
                            A.id = {$request['idLider']}
                    )

                    AND 

                    id_perfil = (
                        SELECT 
                            max(E.id)
                        FROM 
                            zcrmportal_hierarquia_lider_ponto A
                            INNER JOIN ".DBRM_BANCO."..PFUNC B ON B.CODCOLIGADA = A.coligada AND B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS
                            INNER JOIN ".DBRM_BANCO."..PPESSOA C ON C.CODIGO = B.CODPESSOA
                            INNER JOIN zcrmportal_usuario D ON D.login = C.CPF COLLATE Latin1_General_CI_AS
                            LEFT JOIN zcrmportal_perfil E ON E.nome = 'PONTO_APROVA'
                        WHERE 
                            A.id = {$request['idLider']}
                    )
                ");
            }

            return responseJson('success', 'Aprovação do Líder alterado com sucesso.');
        }

        return responseJson('error', 'Falha ao alterar aprovação do Líder.');

    }

    public function alterarLider($request)
    {

        try {

            $chapaLider         = $request['chapaLider'] ?? '';
            $chapaFuncionarios  = $request['chapaFuncionarios'] ?? array();
            $chapaGestor        = $request['chapaGestor'] ?? '';
            $periodoInicio      = $request['periodoInicio'] ?? '';
            $periodoTermino     = $request['periodoTermino'] ?? '';
            $operacao           = $request['operacao'] ?? '';
            $rh                 = $request['rh'];

            if($chapaLider == ''){return responseJson('error', '<b>Líder</b> não selecionado.');}
            if(count($chapaFuncionarios) <= 0){return responseJson('error', 'Nenhuma funcionário atribuido ao <b>Líder</b>.');}
            if($chapaGestor == ''){return responseJson('error', '<b>Gestor</b> não identificado.');}
            if($periodoInicio == ''){return responseJson('error', '<b>Período de início</b> não informado.');}
            if($periodoTermino != ''){
                if($periodoTermino < $periodoInicio){return responseJson('error', '<b>Período de término</b> não poder ser menor que o <b>período de início.</b>');}
            }

            $id_hierarquia = self::pegaIdHierarquiaGestor($chapaGestor);
            if(!$id_hierarquia){return responseJson('error', '<b>Gestor</b> não localizado em nenhuma Hierarquia.');}

            $dadosAtual = self::listaDadosHierarquiaLider(id($request['id']));

            if($dadosAtual[0]['chapa'] != $chapaLider){
                $checkLiderJaCadastrado = $this->dbportal
                    ->table('zcrmportal_hierarquia_lider_ponto')
                    ->select('chapa')
                    ->where('chapa', $chapaLider)
                    ->where('coligada', $this->coligada)
                    ->where('inativo IS NULL')
                    ->get();
                if($checkLiderJaCadastrado->getNumRows() > 0 && !$rh){
                    return responseJson('error', '<b>Líder</b> já cadastrado em outra hierarquia.');
                }

                $this->dbportal
                    ->table('zcrmportal_hierarquia_lider_ponto')
                    ->set('nivel', 2)
                    ->where('id', $request['id'])
                    ->update();
            }

            $update = [
                'chapa'     => $chapaLider,
                'coligada'  => $this->coligada,
                'perini'    => $periodoInicio,
                'perfim'    => ($periodoTermino != '') ? $periodoTermino : null,
                'usualt'    => $this->log_id,
                'dtalt'     => $this->now,
                'operacao'  => $operacao
            ];

            $update = $this->dbportal
                ->table('zcrmportal_hierarquia_lider_ponto')
                ->set($update)
                ->where('id', $request['id'])
                ->update();
                
            if($this->dbportal->affectedRows() > 0){

                $this->dbportal
                    ->table('zcrmportal_hierarquia_lider_func_ponto')
                    ->set('inativo', 1)
                    ->set('usu_inativou', $this->log_id)
                    ->set('dt_inativou', $this->now)
                    ->where('id_lider', $request['id'])
                    ->update();

                foreach($chapaFuncionarios as $chapaFuncionario){

                    $insertFuncionario = [
                        'chapa'     => $chapaFuncionario,
                        'coligada'  => $this->coligada,
                        'id_lider'  => $request['id'],
                        'usucad'    => $this->log_id,
                        'dtcad'     => $this->now
                    ];

                    self::cadastrarFuncionarioLider($insertFuncionario);

                }

                return responseJson('success', 'Alteração realizada com sucesso.');

            }
            return responseJson('error', 'Falha ao alterar Líder.');

        } catch (\Exception | \Error $e) {
            return responseJson('error', 'Internal Error: '.$e->getMessage());
        }
    }

    public function removerLider($request)
    {

        $id = (int)cid($request['idLider']);

        $delete = [
            'inativo'       => 1,
            'usu_inativou'  => $this->log_id,
            'dt_inativou'   => $this->now
        ];

        $delete = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_ponto')
            ->set($delete)
            ->where('id', $id)
            ->update();

        if($this->dbportal->affectedRows() > 0){

            $this->dbportal
                ->table('zcrmportal_hierarquia_lider_func_ponto')
                ->set('inativo', 1)
                ->set('usu_inativou', $this->log_id)
                ->set('dt_inativou', $this->now)
                ->where('id_lider', $id)
                ->update();

            $this->dbportal
                ->table('zcrmportal_hierarquia_lider_excecao_ponto')
                ->set('inativo', 1)
                ->set('usu_inativou', $this->log_id)
                ->set('dt_inativou', $this->now)
                ->where('id_lider', $id)
                ->update();

            return responseJson('success', 'Líder removido com sucesso.');
                
        }

        return responseJson('error', 'Falha ao remover Líder.');

    }

    public function removerLiderExcecao($request)
    {

        $idLider        = (int)$request['idLider'];
        $idLiderExcecao = (int)cid($request['idLiderExcecao']);

        $delete = [
            'inativo'       => 1,
            'usu_inativou'  => $this->log_id,
            'dt_inativou'   => $this->now
        ];

        $delete = $this->dbportal
            ->table('zcrmportal_hierarquia_lider_excecao_ponto')
            ->set($delete)
            ->where('id_lider', $idLider)
            ->where('id', $idLiderExcecao)
            ->update();

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Líder Exceção removido com sucesso.');
        }

        return responseJson('error', 'Falha ao remover Líder Exceção.');

    }

    public function inativaLider()
    {

        $query = $this->dbportal->query(" 
            UPDATE 
                zcrmportal_hierarquia_lider_func_ponto 
            SET 
                inativo = 1, 
                usu_inativou = 0, 
                dt_inativou = getdate() 
            WHERE 
                id_lider IN (SELECT id FROM zcrmportal_hierarquia_lider_ponto WHERE perfim < '".date('Y-m-d')."' AND perfim IS NOT NULL)
                AND inativo IS NULL
        ");
        $query2 = $this->dbportal->query(" 
            UPDATE 
                zcrmportal_hierarquia_lider_excecao_ponto 
            SET 
                inativo = 1, 
                usu_inativou = 0, 
                dt_inativou = getdate() 
            WHERE 
                id_lider IN (SELECT id FROM zcrmportal_hierarquia_lider_ponto WHERE perfim < '".date('Y-m-d')."' AND perfim IS NOT NULL)
                AND inativo IS NULL
        ");
        
        
        $this->dbportal->query(" UPDATE zcrmportal_hierarquia_lider_ponto SET inativo = 1, usu_inativou = 0, dt_inativou = getdate() WHERE perfim < '".date('Y-m-d')."' AND perfim IS NOT NULL AND inativo IS NULL ");

        $query = $this->dbportal->query("
            UPDATE 
                zcrmportal_hierarquia_lider_func_ponto 
            SET 
                inativo = 1, 
                usu_inativou = 0, 
                dt_inativou = getdate() 
            WHERE 
                id_lider IN (
                    SELECT
                    DISTINCT
                    A.id
                FROM
                    zcrmportal_hierarquia_lider_ponto a
                    INNER JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.chapa = a.chapa COLLATE Latin1_General_CI_AS
                WHERE
                    a.inativo IS NULL 
                    AND b.CODSITUACAO = 'D'
                )
        ");
       
            $this->dbportal->query("
                UPDATE 
                    zcrmportal_hierarquia_lider_ponto 
                SET 
                    inativo = 1, 
                    usu_inativou = 0, 
                    dt_inativou = getdate() 
                WHERE 
                    id IN (
                        SELECT
                        DISTINCT
                        A.id
                    FROM
                        zcrmportal_hierarquia_lider_ponto a
                        INNER JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.chapa = a.chapa COLLATE Latin1_General_CI_AS
                    WHERE
                        a.inativo IS NULL 
                        AND b.CODSITUACAO = 'D'
                    )
            ");
        
        $query = $this->dbportal->query("
            UPDATE 
                zcrmportal_hierarquia_lider_func_ponto 
            SET 
                inativo = 1, 
                usu_inativou = 0, 
                dt_inativou = getdate() 
            WHERE 
                CONCAT(coligada, '-', chapa) IN (
                    SELECT
                    DISTINCT
                    concat(A.coligada, '-', A.chapa)
                FROM
                    zcrmportal_hierarquia_lider_func_ponto a
                    INNER JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.chapa = a.chapa COLLATE Latin1_General_CI_AS
                WHERE
                        a.inativo IS NULL 
                    AND b.CODSITUACAO = 'D'
                )
        ");
        $query = $this->dbportal->query("
            UPDATE 
                zcrmportal_hierarquia_lider_excecao_ponto 
            SET 
                inativo = 1, 
                usu_inativou = 0, 
                dt_inativou = getdate() 
            WHERE 
                CONCAT(coligada, '-', chapa) IN (
                    SELECT
                    DISTINCT
                    concat(A.coligada, '-', A.chapa)
                FROM
                zcrmportal_hierarquia_lider_excecao_ponto a
                    INNER JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.chapa = a.chapa COLLATE Latin1_General_CI_AS
                WHERE
                        a.inativo IS NULL 
                    AND b.CODSITUACAO = 'D'
                )
        ");

    }
    

}