<?php
namespace App\Models\Gupy;

use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class GupyModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $now;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->now      = date('Y-m-d H:i:s');
    }

    public function ListarRequisicao($id_requisicao){

        $query = "
            SELECT 
                a.coligada,
                a.id_vaga_kenoby, 
                a.id,
                CASE
                    WHEN a.codposicao IS NOT NULL THEN 1
                    ELSE (SELECT COUNT(*) FROM zcrmportal_requisicao_aq_salario b WHERE b.id_req_aq = a.id)
                END qtde_vagas
            FROM 
                zcrmportal_requisicao_aq a
            WHERE 
                    a.situacao = 2 
                AND a.id = '{$id_requisicao}'
                /*AND a.dt_cancelado IS NULL 
                AND a.id_vaga_kenoby IS NOT NULL 
                AND a.id_vaga_kenoby > 0 
                AND a.xerpa_guid IS NULL 
                AND a.status_vaga_kenoby not in (2, 3) */
                /*AND a.id = 8579*/
        ";
        $result = $this->dbportal->query($query);

        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function PegaDadosRequisicao($id_requisicao){

        $query = "
            SELECT 
                A.*,
                B.NOME nomefunc,
                substring(C.codsecao,5,5) centrocusto,
                C.codsecao,
                CASE WHEN A.salario IS NOT NULL THEN A.salario ELSE (SELECT MAX(salario) FROM zcrmportal_requisicao_aq_salario WHERE id_req_aq = A.id) END salario,
                CB.codfuncao,
                D.DESCRICAO nomesecao,
                E.NOME nomefuncao,
                
                GE.NOME nomegestor,
                GE.EMAIL emailgestor,
                
                F.DESCRICAO nomehorario,
                A2.nome nomesolicitante,
                A2.email emailsolicitante,
                CA.tipo GS
            FROM 
                zcrmportal_requisicao_aq A
                    LEFT JOIN ".DBRM_BANCO."..PFUNC B ON 
                        B.CHAPA COLLATE Latin1_General_CI_AS = A.chapa_sub
                        AND A.coligada = B.CODCOLIGADA
                    
                    LEFT JOIN 
                        zcrmportal_posicao C
                    ON
                        C.id = (CASE WHEN A.codposicao IS NOT NULL THEN DBO.getExplode(A.codposicao,'.',1) ELSE DBO.getExplode((SELECT MAX(codposicao) FROM zcrmportal_requisicao_aq_salario WHERE id_req_aq = A.id),'.',1) END)
                        
                    LEFT JOIN 
                        zcrmportal_posicao_salario CA
                    ON
                        C.id = CA.id_posicao
                        AND CA.id = (CASE WHEN A.codposicao IS NOT NULL THEN DBO.getExplode(A.codposicao,'.',2) ELSE DBO.getExplode((SELECT MAX(codposicao) FROM zcrmportal_requisicao_aq_salario WHERE id_req_aq = A.id),'.',2) END)
                    
                    LEFT JOIN 
                        zcrmportal_posicao_funcao CB
                    ON
                        CB.id = CA.id_posicao_funcao
                    
                    LEFT JOIN ".DBRM_BANCO."..PSECAO D ON
                        D.CODIGO COLLATE Latin1_General_CI_AS = C.codsecao
                        AND D.CODCOLIGADA = A.coligada
                        
                    LEFT JOIN ".DBRM_BANCO."..PFUNCAO E ON
                        E.CODCOLIGADA = A.coligada
                        AND E.CODIGO COLLATE Latin1_General_CI_AS = CB.codfuncao
                        
                    LEFT JOIN ".DBRM_BANCO."..AHORARIO F ON
                        F.CODCOLIGADA = A.coligada
                        AND F.CODIGO COLLATE Latin1_General_CI_AS = A.codhorario
                ,zcrmportal_usuario A2
                ,EMAIL_CHAPA GE
            WHERE 
                    A.id = '{$id_requisicao}'
                AND GE.id_hierarquia = A.id_hierarquia_aprovador
                AND GE.CODCOLIGADA = A.coligada
                AND A2.id = A.usucad
            ORDER BY 
                A.id 
            DESC
        ";
        $result = $this->dbportal->query($query);

        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function AtualizaDadosGupy($id_requisicao, $id_vaga_gupy, $response_gupy, $status_vaga_gupy){

        $id_vaga_gupy =  strlen(trim($id_vaga_gupy)) <= 0 ? "NULL" : $id_vaga_gupy;

        $query = "
            UPDATE 
                zcrmportal_requisicao_aq
            SET
                id_vaga_gupy     = {$id_vaga_gupy},
                dt_id_vaga_gupy  = '{$this->now}',
                status_vaga_gupy = {$status_vaga_gupy},
                response_gupy    = '{$response_gupy}'
            WHERE
                id = {$id_requisicao}
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) ? true : false;


    }

    public function ListarPosicaoRequisicao($id_requisicao){

        $query = " SELECT * FROM zcrmportal_requisicao_aq_salario WHERE id_req_aq = '{$id_requisicao}' ";
        $result = $this->dbportal->query($query);

        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function AtualizaDadosPosicaoGupy($tipo, $dados){

        switch($tipo){
            case 'C':
                $query = " UPDATE zcrmportal_requisicao_aq_salario SET id_posicao_gupy = '{$dados['id_posicao_gupy']}' WHERE id = '{$dados['id_posicao']}' ";
            break;
            case 'I':
                $query = " UPDATE zcrmportal_requisicao_aq SET id_posicao_gupy = '{$dados['id_posicao_gupy']}' WHERE id = '{$dados['id_requisicao']}' ";
            break;
        }
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) ? true : false;

    }

    public function ListarRequisicaoGupy(){

        $query = " SELECT id, id_posicao_gupy, id_vaga_gupy, status_vaga_gupy, codposicao  FROM zcrmportal_requisicao_aq WHERE status_vaga_gupy = 1 AND id_vaga_gupy IS NOT NULL ORDER BY id DESC ";
        $result = $this->dbportal->query($query);

        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ListarRequisicaoGupyColetivo($id_requisicao){

        $query = " SELECT id, id_req_aq, id_posicao_gupy, status_vaga_gupy, id_candidato_gupy FROM zcrmportal_requisicao_aq_salario WHERE id_posicao_gupy IS NOT NULL AND id_req_aq = '{$id_requisicao}' ORDER BY id_candidato_gupy ASC ";
        $result = $this->dbportal->query($query);

        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function AtualizarStatusVagaGupy($tipo, $dados){

        switch($tipo){
            case 'C':
                $query = " UPDATE zcrmportal_requisicao_aq_salario SET status_vaga_gupy = '2', id_candidato_gupy = '{$dados['id_candidato_gupy']}' WHERE id = '{$dados['id_posicao']}' ";
            break;
            case 'I':
                $query = " UPDATE zcrmportal_requisicao_aq SET status_vaga_gupy = '2' WHERE id = '{$dados['id_requisicao']}' AND status_vaga_gupy = '1' ";
            break;
        }
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) ? true : false;

    }

    public function AtualizarStatusVagaGupyColetiva($id_requisicao){

        $query = "
            UPDATE
                zcrmportal_requisicao_aq
            SET
                status_vaga_gupy = '2'
            WHERE
                    id = '{$id_requisicao}'
                AND (SELECT COUNT(*) FROM zcrmportal_requisicao_aq_salario WHERE id_req_aq = '{$id_requisicao}' AND id_candidato_gupy IS NULL) = 0
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) ? true : false;

    }

}