<?php
namespace App\Models\Hierarquia;
use CodeIgniter\Model;

class SubstitutoModel extends Model {

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
        
    }


    public function listaSubstitutosGestor($idGestor)
    {

        $query = "
            SELECT 
                A.ID 
                , A.CHAPA_GESTOR
                , A.CHAPA_SUBSTITUTO
                , B.NOME
                , A.DTINI
                , A.DTFIM
                , CASE WHEN (
                    LEN(A.modulos) - LEN(REPLACE(A.modulos, ',', ''))
                ) = 0 THEN 1 
                    ELSE
                    (LEN(A.modulos) - LEN(REPLACE(A.modulos, ',', '')) + 1) 
                    END AS QTDPERMISSOES
                , CASE WHEN EXISTS (
                    SELECT 1
                    FROM zcrmportal_hierarquia_gestor_substituto_modulos f
                    WHERE f.id IN (SELECT Id FROM dbo.EXTRAIR_DADOS_JSON(A.modulos))
                    AND f.aprovador = 1
                ) THEN 1 ELSE 0 END AS APROVADOR
            FROM zcrmportal_hierarquia_gestor_substituto A
                LEFT JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa_substituto COLLATE Latin1_General_CI_AS = B.CHAPA
            WHERE A.id_gestor = '{$idGestor}'
                AND getdate() <= A.dtfim+1
                AND A.inativo = 0
                AND A.coligada = '{$this->coligada}'
            GROUP BY A.chapa_substituto, B.NOME, A.dtini, A.dtfim, A.CHAPA_GESTOR, A.id, A.modulos
        ";


        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;

    }

    public function getModulosSelecionados($valores){

        $array = json_decode($valores, true);
        $val = implode(',', $array);

        $query = "
            SELECT 
                * 
            FROM zcrmportal_hierarquia_gestor_substituto_modulos
            WHERE ID IN (".$val.")
            AND inativo = 0
        ";


        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function getDadosSubstituicao($idSub){
        $query = "
        SELECT 
            A.*, B.NOME AS nome_sub, C.NOME AS nome_gestor
        FROM zcrmportal_hierarquia_gestor_substituto A
            LEFT JOIN 
                ".DBRM_BANCO."..PFUNC B ON A.chapa_substituto COLLATE Latin1_General_CI_AS = B.CHAPA
            LEFT JOIN 
			    CorporeRMQA2..PFUNC C ON A.chapa_gestor COLLATE Latin1_General_CI_AS = C.CHAPA
        WHERE A.id = ".$idSub."
            AND A.inativo = 0
            AND A.coligada = '{$this->coligada}'
        ";

        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function inativarSubstituto($dados){

        $id = (int)cid($dados['idSub']);

        $delete = [
            'inativo'       => 1,
            'usu_inativou'  => $this->log_id,
            'dt_inativou'   => $this->now
        ];

        $delete = $this->dbportal
            ->table('zcrmportal_hierarquia_gestor_substituto')
            ->set($delete)
            ->where('id', $id)
            ->update();

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Substituto removido com sucesso.');
                
        }

        return responseJson('error', 'Falha ao remover Substituto.');
    }

    public function atualizarGestor($dados){

        $update = [
            'modulos' => $dados['funcoes'],
            'dtini'   => $dados['dataini'],
            'dtfim'   => $dados['datafim'],
            'usualt'  => $this->log_id,
            'dtalt'   => $this->now
        ];

        $update = $this->dbportal
            ->table('zcrmportal_hierarquia_gestor_substituto')
            ->set($update)
            ->where('id', $dados['idReq'])
            ->update();
 

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Substituto atualizado com sucesso.');
                
        }

        return responseJson('error', 'Falha ao atualizar Substituto.');

    }


    public function listaGestores()
    {
        
       $query = "
        SELECT 
            b.CHAPA,
            b.NOME,
            a.id_hierarquia,
            C.CPF,
            D.id
        FROM 
            zcrmportal_hierarquia_chapa a
            JOIN ".DBRM_BANCO."..PFUNC b ON b.CODCOLIGADA = a.coligada AND b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS	
            LEFT JOIN ".DBRM_BANCO."..PPESSOA C ON C.CODIGO = B.CODPESSOA
            LEFT JOIN zcrmportal_usuario D ON D.LOGIN = C.CPF COLLATE Latin1_General_CI_AS	
        WHERE 
            a.inativo IS NULL
            AND a.coligada = '{$this->coligada}'
        ORDER BY
            b.NOME

            ";

       /* $query ="
            SELECT 
        	A.id,
        	C.CHAPA,
        	A.NOME,
        	B.CPF	
         FROM 
        	zcrmportal_usuario A
        	LEFT JOIN CorporeRMQA2..PPESSOA B ON B.CPF COLLATE Latin1_General_CI_AS	 = A.login
        	LEFT JOIN CorporeRMQA2..PFUNC C ON C.CODPESSOA = B.CODIGO
		WHERE 
            a.ativo <> 'e'
            AND C.CODCOLIGADA = '{$this->coligada}'
            AND C.CODSITUACAO <> 'D'
        ORDER BY
            b.NOME

        ";*/
        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function getDadosGestor($chapa){

        $query = "
            SELECT 
                NOME, CHAPA FROM PFUNC
            WHERE CHAPA = '".$chapa."'
            AND CODCOLIGADA = '{$this->coligada}'

        ";
        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }
 
    public function CadastrarGestorSubstituto($request){

        $insert = [
            'coligada'         => $this->coligada,
            'chapa_gestor'     => $request['chapa_gestor'],
            'chapa_substituto' => $request['chapa_substituto'],
            'id_gestor'        => $request['id_gestor'],
            'id_substituto'    => $request['id_substituto'],
            'dtini'            => $request['dataini'],
            'dtfim'            => $request['datafim'],
            'dtcad'            => $this->now,
            'inativo'          => 0,
            'modulos'          => $request['funcoes'],
        ];

        $response = $this->dbportal
            ->table('zcrmportal_hierarquia_gestor_substituto')
            ->insert($insert);

        if($this->dbportal->affectedRows() > 0){
        
            return responseJson('success', 'Substituto cadastrado com sucesso.');
        }

    }

    public function getModulos(){

        $query = "

            SELECT A.id, A.nome, A.coligada, A.inativo, COUNT(JSON.Id) AS qtd_funcoes, A.aprovador
                FROM zcrmportal_hierarquia_gestor_substituto_modulos A
            CROSS APPLY 
                dbo.EXTRAIR_DADOS_JSON(A.funcoes) AS JSON
            WHERE 
                COLIGADA = '{$this->coligada}'
                AND INATIVO = 0
                
            GROUP BY A.id, A.nome, A.coligada, A.inativo, A.aprovador
       
        ";

        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function cadastrarModulo($request){


        $insert = [
            'nome'             => $request['nome'],
            'coligada'         => $this->coligada,
            'funcoes'          => $request['funcoes'],
            'dtcad'            => $this->now,
            'inativo'          => 0,
            'aprovador'        => $request['aprovador']
        ];

        $response = $this->dbportal
            ->table('zcrmportal_hierarquia_gestor_substituto_modulos')
            ->insert($insert);

        if($this->dbportal->affectedRows() > 0){
        
            return responseJson('success', 'Módulo cadastrado com sucesso.');
        }
    }

    public function getDadosModulo($id){

        $query = "
            SELECT *
                FROM zcrmportal_hierarquia_gestor_substituto_modulos
            WHERE id = {$id}
        ";

        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function excluirModulo($id){

        $update = [
            'inativo'       => 1,
            'usu_inativou'  => $this->log_id,
            'dt_inativou'   => $this->now
        ];

        $update = $this->dbportal
            ->table('zcrmportal_hierarquia_gestor_substituto_modulos')
            ->set($update)
            ->where('id', $id)
            ->update();
 

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Módulo excluído com sucesso.');
                
        }

        return responseJson('error', 'Falha ao excluir Módulo.');
    }

    public function getFuncoesSelecionadas($funcoes){
        
        $query = "
            SELECT A.id, A.nome 
                FROM zcrmportal_funcoes A
            INNER JOIN dbo.EXTRAIR_DADOS_JSON('{$funcoes}') JSON ON JSON.id = A.id
        ";

        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function getModulosGestor($idGestor){

        $query = "
        SELECT 
            m.id,
            m.nome,
            m.inativo,
            m.funcoes AS funcoes_modulo
        FROM zcrmportal_hierarquia_gestor_substituto_modulos M
            LEFT JOIN zcrmportal_funcoes A 
                ON M.funcoes LIKE CONCAT('%\"', A.id, '\"%')
            LEFT JOIN zcrmportal_perfilfuncao B 
                ON B.id_funcao = A.id
            LEFT JOIN zcrmportal_usuarioperfil C 
                ON C.id_perfil = B.id_perfil
        WHERE C.id_usuario = ".$idGestor."
        AND M.inativo = 0
        GROUP BY m.id, m.inativo, m.funcoes, m.nome
        ";

        $result = $this->dbportal->query($query);
        return  ($result) 
                ? $result->getResultArray() 
                : false;
    }

    public function listaFuncionariosGestor($request)
    {

        $query = " 
            SELECT 
                TOP 1000 
                A.CHAPA
                , A.NOME
                , B.NOME NOMEFUNCAO 
                , C.CPF
                , D.ID
            FROM PFUNC A 
                INNER JOIN PFUNCAO B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO 
                INNER JOIN PPESSOA C ON C.CODIGO = A.CODPESSOA 
                INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_usuario D ON D.LOGIN = C.CPF COLLATE Latin1_General_CI_AS	
            WHERE A.CODCOLIGADA = '1' 
                AND A.CODSITUACAO NOT IN ('D') 
                AND (A.NOME LIKE ? OR A.CHAPA LIKE ?) 
            ORDER BY A.NOME ASC";
        $result = $this->dbrm->query($query, array("%".$request['colab']."%","%".$request['colab']."%"));

        return  ($result) 
                ? $result->getResultArray() 
                : false;
        

    }

    public function editarModulo($request){

        $update = [
            'nome'             => $request['nome'],
            'funcoes'          => $request['funcoes'],
            'usualt'           => $this->log_id,
            'dtalt'            => $this->now,
            'aprovador'        => $request['aprovador']
            
        ];

        $update = $this->dbportal
            ->table('zcrmportal_hierarquia_gestor_substituto_modulos')
            ->set($update)
            ->where('id', $request['id'])
            ->update();
 
        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Módulo atualizado com sucesso.');
                
        }

        return responseJson('error', 'Falha ao atualizar Módulo.');

    }



}