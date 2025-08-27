<?php
namespace App\Models\Logs;
use CodeIgniter\Model;
use App\Libraries\PontoMais;

class PontomaisModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $log_id;
    private $now;
    private $mPortal;
    
    public function __construct()
    {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->log_id   = session()->get('log_id');
        $this->now      = date('Y-m-d H:i:s');
    }

    public function BuscaLogs($dados)
    {

        $filtroSituacao = "";
        if($dados['situacao'] >= 9 && $dados['situacao'] != '') $filtroSituacao = " AND A.situacao >= '{$dados['situacao']}' ";
        if($dados['situacao'] < 9 && $dados['situacao'] != '') $filtroSituacao = " AND A.situacao = '{$dados['situacao']}' ";
        $filtroChapaNome = ($dados['keywords']) ? " AND (A.chapa LIKE '%{$dados['keywords']}%' OR B.NOME like '%{$dados['keywords']}%') " : "";
        $filtroData      = ($dados['data_inicio'] && $dados['data_termino']) ? " AND A.dtcad BETWEEN '{$dados['data_inicio']} 00:00:00' AND '{$dados['data_termino']} 23:59:59' " : "";

        $logs = $this->dbrm->query("
            SELECT
                TOP 1000
                A.id ID,
                B.CODCOLIGADA,
                B.CHAPA,
                B.NOME,
                C.EMAIL,
                B.CODHORARIO,
                C.CPF,
                B.PISPASEP PIS,
                B.DATAADMISSAO,
                B.DATADEMISSAO,
                B.CODFUNCAO,
                D.NOME FUNCAO,
                SUBSTRING(B.CODSECAO,5,5) CODCUSTO,
                E.CGC CNPJ,
                A.acao ACAO,
                A.message MESSAGE,
                A.situacao SITUACAO,
                CONVERT(VARCHAR, A.dtcad, 103) + ' ' + CONVERT(VARCHAR, A.dtcad, 108) DATALOG
            FROM
                ".DBPORTAL_BANCO."..zcrmportal_api_pontomais A (NOLOCK)
                INNER JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA   = A.codcoligada AND B.CHAPA  = A.chapa COLLATE Latin1_General_CI_AS
                INNER JOIN PPESSOA C (NOLOCK) ON C.CODIGO      = B.CODPESSOA
                INNER JOIN PFUNCAO D (NOLOCK) ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
                INNER JOIN GFILIAL E (NOLOCK) ON E.CODFILIAL = B.CODFILIAL AND E.CODCOLIGADA = B.CODCOLIGADA
            WHERE
                1 = 1
                {$filtroSituacao}
                {$filtroChapaNome}
                {$filtroData}
            ORDER BY
                A.dtcad DESC
        ");

        return exit(json_encode((!$logs) ? ['erro'] : $logs->getResult()));

    }

    public function CancelaLog($dados)
    {

        try{

            $id   = json_decode($dados['id']);
            $inID = implode(',', $id);

            $this->dbportal->query(" UPDATE zcrmportal_api_pontomais SET situacao = 8, usualt = '{$this->log_id}', dtalt = '{$this->now}' WHERE id IN ({$inID}) AND situacao NOT IN (8, 1) ");
            return (($this->dbportal->affectedRows() ?? 0) > 0)
                    ? responseJson('success', 'Registro cancelado com sucesso.')
                    : responseJson('error', 'Erro ao cancelar registro');

        } catch (\Exception | \Error $e) {
            return responseJson('error', '<b>Erro interno:</b> '.$e->getMessage());
        }

    }

    public function ReprocessarLog($dados)
    {

        try{

            $id   = json_decode($dados['id']);
            $inID = implode(',', $id);

            // return responseJson('warning', 'Reprocessamento pausado, API em Produção!');
            $PontoMais = new PontoMais();
            $result = $PontoMais->schedule($inID);

            return (($result ?? false))
                    ? responseJson('success', 'Registro reprocessado com sucesso.')
                    : responseJson('error', 'Erro no reprocessamento');

        } catch (\Exception | \Error $e) {
            return responseJson('error', '<b>Erro interno:</b> '.$e->getMessage());
        }

    }

    public function CronApiPontoMais()
    {
        $PontoMais = new PontoMais();
        $result = $PontoMais->schedule();
        return $result;
    }


}