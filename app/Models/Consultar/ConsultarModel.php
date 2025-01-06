<?php
namespace App\Models\Consultar;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class ConsultarModel extends Model {

    protected $dbportal;
    protected $dbrm;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm = db_connect('dbrm');
    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- HOLERITE -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Configuração do Holerite
    // -------------------------------------------------------
    public function ListarHoleriteConfiguracao(){

        $query = " SELECT * FROM zcrmportal_holerite_config WHERE coligada = '".session()->get('func_coligada')."' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista períodos configurados
    // -------------------------------------------------------
    public function ListarHoleriteConfiguracaoPeriodo(){

        $query = " SELECT * FROM zcrmportal_holerite_config_per WHERE coligada = '".session()->get('func_coligada')."' ORDER BY anocomp desc, mescomp desc, periodo asc ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Cadastra configuração do holerite
    // -------------------------------------------------------
    public function CadastrarHoleriteConfiguracao($dados){

        // verifica se o período já foi configurado
        $checkExiste = $this->ListarHoleriteConfiguracao();

        if($checkExiste){
            // update

            $query = " 
                UPDATE 
                    zcrmportal_holerite_config 
                SET 
                    dtinicio = '{$dados['data_inicio']}', 
                    dtfim = '{$dados['data_fim']}', 
                    responsavel_informe_rendimento = '".addslashes($dados['responsavel'])."' ,
                    dtalt = '".date('Y-m-d H:i:s')."',
                    usualt = '".session()->get('log_id')."'
                WHERE
                    coligada = '".session()->get('func_coligada')."'
                ";
            $this->dbportal->query($query);

            if($this->dbportal->affectedRows() > 0){
            
                // cadastra os períodos para holerite
                if(isset($dados['periodos'])) $this->CadastrarHoleriteConfiguracaoPeriodo($dados['periodos']);

                notificacao('success', 'Holerite configurado com sucesso');
                return responseJson('success', 'Holerite configurado com sucesso.');

            }else{
                return responseJson('error', 'Falha ao realizar a configuração do holerite.');
            }
            

        }else{
            // insert

            $query = " 
                INSERT INTO zcrmportal_holerite_config 
                    (coligada, dtinicio, dtfim, responsavel_informe_rendimento, dtalt, usualt)
                        VALUES
                    ('".session()->get('func_coligada')."', '{$dados['data_inicio']}', '{$dados['data_fim']}', '".addslashes($dados['responsavel'])."', '".date('Y-m-d H:i:s')."', '".session()->get('log_id')."')
                ";
            $this->dbportal->query($query);

            if($this->dbportal->affectedRows() > 0){
            
                // cadastra os períodos para holerite
                if(isset($dados['periodos'])) $this->CadastrarHoleriteConfiguracaoPeriodo($dados['periodos']);

                notificacao('success', 'Holerite configurado com sucesso');
                return responseJson('success', 'Holerite configurado com sucesso.');

            }else{
                return responseJson('error', 'Falha ao realizar a configuração do holerite.');
            }

        }

    }

    // -------------------------------------------------------
    // Cadastra períodos do holerite
    // -------------------------------------------------------
    public function CadastrarHoleriteConfiguracaoPeriodo($periodos){

        // remove períodos cadastrados
        $this->dbportal->query(" DELETE FROM zcrmportal_holerite_config_per WHERE coligada = '".session()->get('func_coligada')."' ");

        // cadastra os novos períodos
        foreach($periodos as $dadosPeriodo){

            $dadosPer = explode('-', $dadosPeriodo);
            $this->dbportal->query("
                INSERT INTO zcrmportal_holerite_config_per
                    (coligada, periodo, mescomp, anocomp, usucad, dtcad)
                        VALUES
                    ('".session()->get('func_coligada')."', '{$dadosPer[0]}', '{$dadosPer[1]}', '{$dadosPer[2]}', '".session()->get('log_id')."', '".date('Y-m-d H:i:s')."')
            ");

        }

        return true;

    }

    // -------------------------------------------------------
    // Lista período totvs
    // -------------------------------------------------------
    public function ListarHoleritePeriodoRM(){

        $configuracao = $this->ListarHoleriteConfiguracao();
        
        $dtInicio = isset($configuracao[0]['dtinicio']) ? date('Ym', strtotime($configuracao[0]['dtinicio'])) : false;
        $dtFim = isset($configuracao[0]['dtfim']) ? date('Ym', strtotime($configuracao[0]['dtfim'])) : false;

        $query = "
            SELECT 
                DISTINCT 
                NROPERIODO, 
                MESCOMP, 
                ANOCOMP 
                
            FROM 
                PFPERFF 
                
            WHERE 
                    CODCOLIGADA = '".session()->get('func_coligada')."'
                AND CONCAT(ANOCOMP, RIGHT(REPLICATE('0',2) + CONVERT(VARCHAR,MESCOMP),2)) >= '{$dtInicio}'
	            AND CONCAT(ANOCOMP, RIGHT(REPLICATE('0',2) + CONVERT(VARCHAR,MESCOMP),2)) <= '{$dtFim}'
                
            ORDER BY 
                ANOCOMP DESC, 
                MESCOMP DESC, 
                NROPERIODO
        ";
        $result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0)
                ? $result->getResultArray()
                : false;

    }

    // -------------------------------------------------------
    // Lista dados do holerite
    // -------------------------------------------------------
    public function ListarHolerite($dados){

        $coligada = session()->get('func_coligada');
        $dadosPeriodo = explode('-', $dados['periodo']);
        $periodo = $dadosPeriodo[0];
        $mescomp = $dadosPeriodo[1];
        $anocomp = $dadosPeriodo[2];
        $chapa = util_chapa(session()->get('func_chapa'))['CHAPA'];

        $query = "
            SELECT 
                PFPERFF.SALFAMILIA, 
                PFPERFF.SALARIODECALCULO, 
                PFPERFF.BASEFGTS ,
                PFPERFF.BASEIRRF, 
                PFPERFF.BASEFGTS13 ,
                PFPERFF.BASEIRRF13 ,
                PFPERFF.BASEINSS, 
                PFPERFF.BASEINSS13, 
                PFPERFF.NRODEPENDIRRF , 
                PFPERFF.LIQUIDO, 
                PFPERFF.NRODEPENDSALFAMILIA , 
                PFHSTFCO.DTMUDANCA, 
                PFUNCAO.NOME,
                (SELECT MAX(PFF.DTPAGTO) FROM PFFINANC PFF WHERE PFF.MESCOMP = PFPERFF.MESCOMP AND PFF.ANOCOMP = PFPERFF.ANOCOMP AND PFF.CODCOLIGADA = PFPERFF.CODCOLIGADA AND PFF.NROPERIODO = PFPERFF.NROPERIODO) AS DTPAGTO
            
            FROM 
                PFPERFF 
                    LEFT  JOIN PFHSTFCO ON PFHSTFCO.CHAPA = PFPERFF.CHAPA AND PFHSTFCO.CODCOLIGADA = PFPERFF.CODCOLIGADA AND PFHSTFCO.DTMUDANCA = (SELECT MAX(PFHSTFCO.DTMUDANCA)  FROM PFHSTFCO WHERE PFHSTFCO.CODCOLIGADA = PFPERFF.CODCOLIGADA AND PFHSTFCO.CHAPA = PFPERFF.CHAPA AND PFHSTFCO.DTMUDANCA <=  '" . $anocomp.  "-" . e0($mescomp) . "-28'  )
                    INNER JOIN PFUNCAO ON PFUNCAO.CODIGO = PFHSTFCO.CODFUNCAO AND PFUNCAO.CODCOLIGADA = PFHSTFCO.CODCOLIGADA
            WHERE 
                    PFPERFF.CODCOLIGADA = " . $coligada . "  
                AND PFPERFF.ANOCOMP = '" .  $anocomp . "' 
                AND PFPERFF.MESCOMP = '" . $mescomp . "' 
                AND PFPERFF.NROPERIODO = " . $periodo . " 
                AND PFHSTFCO.DTMUDANCA <= '" . $anocomp.  "-" . e0($mescomp) . "-28' 
                AND PFPERFF.CHAPA = '" . $chapa . "' 
                AND PFHSTFCO.CODCOLIGADA = " . $coligada . " 
                AND PFUNCAO.CODCOLIGADA = " . $coligada . " 
                AND PFPERFF.CODCOLIGADA = " . $coligada .  " 
            
            UNION ALL 
            
            SELECT 
                PFPERFFCOMPL.SALFAMILIA, 
                PFPERFFCOMPL.SALARIODECALCULO, 
                PFPERFFCOMPL.BASEFGTS ,
                PFPERFFCOMPL.BASEIRRF, 
                PFPERFFCOMPL.BASEFGTS13 ,
                PFPERFFCOMPL.BASEIRRF13 ,
                PFPERFFCOMPL.BASEINSS, 
                PFPERFFCOMPL.BASEINSS13, 
                PFPERFFCOMPL.NRODEPENDIRRF , 
                PFPERFFCOMPL.LIQUIDO, 
                PFPERFFCOMPL.NRODEPENDSALFAMILIA , 
                PFHSTFCO.DTMUDANCA, 
                PFUNCAO.NOME,
                (SELECT MAX(PFF.DTPAGTO) FROM PFFINANCCOMPL PFF WHERE PFF.MESCOMP = PFPERFFCOMPL.MESCOMP AND PFF.ANOCOMP = PFPERFFCOMPL.ANOCOMP AND PFF.CODCOLIGADA = PFPERFFCOMPL.CODCOLIGADA AND PFF.NROPERIODO = PFPERFFCOMPL.NROPERIODO) AS DTPAGTO
            
            FROM 
                PFPERFFCOMPL
                    LEFT  JOIN PFHSTFCO ON PFHSTFCO.CHAPA = PFPERFFCOMPL.CHAPA AND PFHSTFCO.CODCOLIGADA = PFPERFFCOMPL.CODCOLIGADA AND PFHSTFCO.DTMUDANCA = (SELECT MAX(PFHSTFCO.DTMUDANCA)  FROM PFHSTFCO WHERE PFHSTFCO.CODCOLIGADA = PFPERFFCOMPL.CODCOLIGADA AND PFHSTFCO.CHAPA = PFPERFFCOMPL.CHAPA AND PFHSTFCO.DTMUDANCA <=  '" . $anocomp.  "-" . e0($mescomp) . "-28'  )
                    INNER JOIN PFUNCAO ON PFUNCAO.CODIGO = PFHSTFCO.CODFUNCAO AND PFUNCAO.CODCOLIGADA = PFHSTFCO.CODCOLIGADA
            WHERE 
                    PFPERFFCOMPL.CODCOLIGADA = " . $coligada . "  
                AND PFPERFFCOMPL.ANOCOMP = '" .  $anocomp . "' 
                AND PFPERFFCOMPL.MESCOMP = '" . $mescomp . "' 
                AND PFPERFFCOMPL.NROPERIODO = " . $periodo . " 
                AND PFHSTFCO.DTMUDANCA <= '" . $anocomp.  "-" . e0($mescomp) . "-28' 
                AND PFPERFFCOMPL.CHAPA = '" . $chapa . "' 
                AND PFHSTFCO.CODCOLIGADA = " . $coligada . " 
                AND PFUNCAO.CODCOLIGADA = " . $coligada . " 
                AND PFPERFFCOMPL.CODCOLIGADA = " . $coligada .  " 
        ";
        $result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0)
                ? $result->getResultArray()
                : false;

    }



    public function centro_custo(){

        $query = "
            SELECT TOP 9 * FROM GCCUSTO WHERE CODCOLIGADA = 1 AND LEN(CODCCUSTO) = 15 AND ATIVO = 'T' ORDER BY NOME
        ";
        $result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0)
                ? $result->getResultArray()
                : false;

    }


}