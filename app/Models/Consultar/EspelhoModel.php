<?php
namespace App\Models\Consultar;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class EspelhoModel extends Model {

    protected $dbportal;
    protected $dbrm;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm = db_connect('dbrm');

        if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
    }

    // -------------------------------------------------------
    // Pega as configuração do espelho de ponto
    // -------------------------------------------------------
    public function ListarEspelhoConfiguracao(){

        $query = " SELECT * FROM zcrmportal_espelho_config WHERE coligada = '".session()->get('func_coligada')."' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Cadastra configuração do holerite
    // -------------------------------------------------------
    public function CadastrarEspelhoConfiguracao($dados){

        // verifica se o período já foi configurado
        $checkExiste = $this->ListarEspelhoConfiguracao();

        if($checkExiste){
            // update

            $query = " 
                UPDATE 
                    zcrmportal_espelho_config 
                SET 
                    dtinicio = '{$dados['data_inicio']}', 
                    dtfim = '{$dados['data_fim']}', 
                    dtcad = '".date('Y-m-d H:i:s')."',
                    usucad = '".session()->get('log_id')."'
                WHERE
                    coligada = '".session()->get('func_coligada')."'
                ";
            $this->dbportal->query($query);

            if($this->dbportal->affectedRows() > 0){

                notificacao('success', 'Espelho de ponto configurado com sucesso');
                return responseJson('success', 'Espelho de ponto configurado com sucesso.');

            }else{
                return responseJson('error', 'Falha ao realizar a configuração do espelho de ponto.');
            }
            

        }else{
            // insert

            $query = " 
                INSERT INTO zcrmportal_espelho_config 
                    (coligada, dtinicio, dtfim, dtcad, usucad)
                        VALUES
                    ('".session()->get('func_coligada')."', '{$dados['data_inicio']}', '{$dados['data_fim']}', '".date('Y-m-d H:i:s')."', '".session()->get('log_id')."')
                ";
            $this->dbportal->query($query);

            if($this->dbportal->affectedRows() > 0){

                notificacao('success', 'Espelho de ponto configurado com sucesso');
                return responseJson('success', 'Espelho de ponto configurado com sucesso.');

            }else{
                return responseJson('error', 'Falha ao realizar a configuração do espelho de ponto.');
            }

        }

    }

    // -------------------------------------------------------
    // Lista periodo do espelho
    // -------------------------------------------------------
    public function ListarEspelhoPeriodoRM(){

        $configuracao = $this->ListarEspelhoConfiguracao();
        if(!$configuracao) return false;
        
        $query = "
            SELECT 
                * 
            FROM 
                APERIODO 
            WHERE 
                    CODCOLIGADA = '".session()->get('func_coligada')."'
                AND INICIOMENSAL >= '{$configuracao[0]['dtinicio']}' AND FIMMENSAL  <= '{$configuracao[0]['dtfim']}'

            ORDER BY 
                ANOCOMP DESC, 
                MESCOMP DESC
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista dias do período
    // -------------------------------------------------------
    public function ListarEspelhoDias($periodo){

        if($periodo === NULL) return false;

        $coligada = session()->get("func_coligada");
        $chapa = session()->get("func_chapa");
        $dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim = dtEn(substr($periodo, 10, 10));

        $query = "
            SELECT 
                CODCOLIGADA, 
                CHAPA, 
                DATA, 
                ATRASO, 
                FALTA, 
                HTRAB, 
                EXTRAEXECUTADO, 
                ADICIONAL, 
                ABONO, 
                BASE, 
                EXTRAAUTORIZADO, 
                TEMPOREF, 
                ATRASONUCL, 
                COMPENSADO, 
                DESCANSO, 
                FERIADO, 
                EXTRACALC, 
                ATRASOCALC, 
                FALTACALC
                ,(SELECT (CASE WHEN A.DATA < B.DATAADMISSAO THEN 'Não Admitido' END) FROM PFUNC B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA) ADMISSAO
                ,(SELECT 'Férias' FROM PFUFERIASPER C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA BETWEEN C.DATAINICIO AND C.DATAFIM) FERIAS
                ,(SELECT E.DESCRICAO FROM PFHSTAFT D, PCODAFAST E WHERE D.TIPO = E.CODCLIENTE AND A.CODCOLIGADA = D.CODCOLIGADA AND A.CHAPA = D.CHAPA AND A.DATA BETWEEN D.DTINICIO AND D.DTFINAL) AFASTAMENTO
                ,(SELECT MAX(G.DESCRICAO) FROM AABONFUN F, AABONO G WHERE F.CODCOLIGADA = G.CODCOLIGADA AND F.CODABONO = G.CODIGO AND A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA AND A.DATA = F.DATA) ABONOS
                ,(SELECT MAX('Suspensão') FROM AJUSTFUN H WHERE A.CODCOLIGADA = H.CODCOLIGADA AND A.CHAPA = H.CHAPA AND A.DATA = H.DATA AND H.APROVADO = '1') SUSPENSAO

            FROM 
                AAFHTFUN A
                
            WHERE 
                    A.CODCOLIGADA = {$coligada}
                AND A.CHAPA = '{$chapa}'
                AND A.DATA >= '{$dataInicio}' 
                AND A.DATA <= '{$dataFim}' 

            UNION ALL 

            SELECT 
                CODCOLIGADA, 
                CHAPA, 
                DATA, 
                ATRASO, 
                FALTA, 
                HTRAB, 
                EXTRAEXECUTADO, 
                ADICIONAL, 
                ABONO, 
                BASE, 
                EXTRAAUTORIZADO, 
                TEMPOREF, 
                ATRASONUCL, 
                COMPENSADO, 
                DESCANSO, 
                FERIADO, 
                EXTRACALC, 
                ATRASOCALC, 
                FALTACALC
                ,(SELECT (CASE WHEN A.DATA < B.DATAADMISSAO THEN 'Não Admitido' END) FROM PFUNC B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA) ADMISSAO
                ,(SELECT 'Férias' FROM PFUFERIASPER C WHERE A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA AND A.DATA BETWEEN C.DATAINICIO AND C.DATAFIM) FERIAS
                ,(SELECT E.DESCRICAO FROM PFHSTAFT D, PCODAFAST E WHERE D.TIPO = E.CODCLIENTE AND A.CODCOLIGADA = D.CODCOLIGADA AND A.CHAPA = D.CHAPA AND A.DATA BETWEEN D.DTINICIO AND D.DTFINAL) AFASTAMENTO
                ,(SELECT MAX(G.DESCRICAO) FROM AABONFUNAM F, AABONO G WHERE F.CODCOLIGADA = G.CODCOLIGADA AND F.CODABONO = G.CODIGO AND A.CODCOLIGADA = F.CODCOLIGADA AND A.CHAPA = F.CHAPA AND A.DATA = F.DATA) ABONOS
                ,(SELECT MAX('Suspensão') FROM AJUSTFUNAM H WHERE A.CODCOLIGADA = H.CODCOLIGADA AND A.CHAPA = H.CHAPA AND A.DATA = H.DATA AND H.APROVADO = '1') SUSPENSAO
                    
            FROM 
                AAFHTFUNAM A
                
            WHERE 
                    A.CODCOLIGADA = {$coligada}
                AND A.CHAPA = '{$chapa}'
                AND A.DATA >= '{$dataInicio}' 
                AND A.DATA <= '{$dataFim}' 
            ORDER BY 
                DATA
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista dias do período
    // -------------------------------------------------------
    public function ListarEspelhoBatidas($periodo){

        if($periodo === NULL) return false;

        $coligada = session()->get("func_coligada");
        $chapa = session()->get("func_chapa");
        $dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim = dtEn(substr($periodo, 10, 10));

        $query = "

        SELECT * FROM (
            SELECT 
                CHAPA,
                DATA, 
                DATAREFERENCIA, 
                BATIDA, 
                STATUS, 
                NATUREZA, 
                DATAINSERCAO, 
                CASE WHEN DATAREFERENCIA IS NULL THEN DATA ELSE DATAREFERENCIA END DATABATIDA, 
                CASE WHEN DATAREFERENCIA < DATA THEN 1 ELSE 0 END BATIDA_NOTURNA 
            FROM 
                ABATFUN 
            WHERE 
                    CODCOLIGADA = {$coligada}
                AND CHAPA = '{$chapa}'
                AND DATA BETWEEN '{$dataInicio}' AND '{$dataFim}'
                
            UNION ALL

            SELECT 
                CHAPA,
                DATA, 
                DATAREFERENCIA, 
                BATIDA, 
                STATUS, 
                NATUREZA, 
                DATAINSERCAO, 
                CASE WHEN DATAREFERENCIA IS NULL THEN DATA ELSE DATAREFERENCIA END DATABATIDA, 
                CASE WHEN DATAREFERENCIA < DATA THEN 1 ELSE 0 END BATIDA_NOTURNA 
            FROM 
                ABATFUNAM
            WHERE 
                    CODCOLIGADA = {$coligada}
                AND CHAPA = '{$chapa}'
                AND DATA BETWEEN '{$dataInicio}' AND '{$dataFim}'
        )X
            ORDER BY CASE WHEN DATAREFERENCIA IS NOT NULL THEN DATAREFERENCIA ELSE DATA END ASC, CASE WHEN DATAREFERENCIA <> DATA THEN BATIDA+1440 ELSE BATIDA END ASC, NATUREZA ASC
            
        ";
        $result = $this->dbrm->query($query);

        if($result->getNumRows() > 0){

            $dados = array();
            $linha = 0;

            $batidas = $result->getResultArray();
            foreach($batidas as $key => $dadosBatidas){
                
                $chapa = $dadosBatidas['CHAPA'];
                $data = ((isset($dadosBatidas['DATAREFERENCIA'])) ? $dadosBatidas['DATAREFERENCIA'] : $dadosBatidas['DATA']);

                if(!isset($dados[$chapa][$data])) $linha = 0;
                    
                $dados[$chapa][$data]['batidas'][$linha]['batida'] = $dadosBatidas['BATIDA'];
                $dados[$chapa][$data]['batidas'][$linha]['data'] = $dadosBatidas['DATA'];
                $dados[$chapa][$data]['batidas'][$linha]['datareferencia'] = $dadosBatidas['DATAREFERENCIA'];
                $dados[$chapa][$data]['batidas'][$linha]['natureza'] = $dadosBatidas['NATUREZA'];
                $dados[$chapa][$data]['batidas'][$linha]['status'] = $dadosBatidas['STATUS'];
                $dados[$chapa][$data]['batidas'][$linha]['forcado'] = 0;
                $linha++;
                

            }

            return $dados;

        }else{
            return false;
        }


    }

    // -------------------------------------------------------
    // Lista movimentos espelhos
    // -------------------------------------------------------
    public function ListarEspelhoMovimento($periodo){

        if($periodo === NULL) return false;

        $coligada = session()->get("func_coligada");
        $chapa = session()->get("func_chapa");
        $dataInicio = dtEn(substr($periodo, 0, 10));
        $dataFim = dtEn(substr($periodo, 10, 10));

        $query = "
            SELECT 
                PEVENTO.CODIGO, 
                PEVENTO.DESCRICAO, 
                AMOVFUN.NUMHORAS 
            FROM 
                AMOVFUN, 
                PEVENTO
            WHERE
                    AMOVFUN.CODCOLIGADA = {$coligada}
                AND PEVENTO.CODCOLIGADA = AMOVFUN.CODCOLIGADA
                AND AMOVFUN.CHAPA = '{$chapa}' 
                AND AMOVFUN.INICIOPER = '{$dataInicio}' 
                AND AMOVFUN.FIMPER = '{$dataFim}'
                AND AMOVFUN.CODCOLIGADA = PEVENTO.CODCOLIGADA 
                AND AMOVFUN.CODEVE = PEVENTO.CODIGO 
                AND PEVENTO.CODIGO NOT IN ('0002','0003','0966') 
                AND AMOVFUN.NUMHORAS > 0
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

}