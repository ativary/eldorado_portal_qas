<?php
namespace App\Models\Consultar;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class InformeRendimentosModel extends Model {

    protected $dbportal;
    protected $dbrm;
    protected $coligada;
    protected $chapa;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm = db_connect('dbrm');
        $this->coligada = session()->get("func_coligada");
        $this->chapa = session()->get('func_chapa');
    }

    // -------------------------------------------------------
    // Lista os períodos de informe do funcionário
    // -------------------------------------------------------
    public function ListarInformePeriodoFuncionario($chapa = false){
        
        $chapa = ($chapa) ? $chapa : $this->chapa;

        $query = " SELECT DISTINCT ANO FROM PDIRF WHERE CODCOLIGADA = '{$this->coligada}' AND CHAPA = '{$chapa}' ORDER BY ANO DESC ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista dados do informe de rendimento
    // -------------------------------------------------------
    public function ListarInformeDados($ano, $chapa = false){
        
        $chapa = ($chapa) ? $chapa : $this->chapa;

        $query = "
            SELECT 
				C.*,
				(SELECT SUM(VALOR) FROM PFFINANC AS A, PEVENTO AS B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CODCOLIGADA = C.CODCOLIGADA AND C.CHAPA = A.CHAPA AND A.CODEVENTO = B.CODIGO AND YEAR (A.DTPAGTO) = '{$ano}' AND B.CODIGOCALCULO IN ('49')) AS IRRF13_PFFINANC
			FROM 
				PDIRF AS C
					INNER JOIN PFUNC D ON C.CHAPA = D.CHAPA AND C.CODCOLIGADA = D.CODCOLIGADA
					INNER JOIN PPESSOA E ON D.CODPESSOA = E.CODIGO
			WHERE C.ANO = '{$ano}' AND D.CHAPA = '{$chapa}'
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }


}