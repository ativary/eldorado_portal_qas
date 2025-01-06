<?php
namespace App\Models;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class WsrmModel extends Model {
    
    private $host;
    private $port;
    private $user;
    private $pass;
    
    public function __construct() {
        $this->host = "";
        $this->port = "";
        $this->user = "";
        $this->pass = "";
    }

    public function wsDataServer($nome_do_processo, $xml, $contexto){
		
		/*
		 * Autenticação do usuário do rm totvs
		 */
		$autentication = array(
			'login' => $this->user,
			'password' => $this->pass,
			'trace' => false
		);
		
		/*
		 * inicia o soap
		 */
		$url_wsdl = $this->host.':'.$this->port.'/wsDataServer/MEX?wsdl';
		
		/* 
		 * conecta no client do soap
		 */
		try {
			
			$client = new \SoapClient($url_wsdl, $autentication);
			
		} catch (\Exception $e) {
			
			echo '<h3>Erro WebServer TOTVs</h3><br>'.$e->getMessage()."<br>";
			exit();
			
		}
		
		/*
		 * monta o xml para envio e com o nome do processo
		 */
		$post_string = array(
		  'DataServerName' => $nome_do_processo, 
		  'XML' => $xml,
		  'Contexto' => $contexto
		);
		
		/*
		 * monta o xml para envio e com o nome do processo
		 */
		try {
			
			$result = $client->SaveRecord($post_string);
			$xml = ($result->SaveRecordResult);
			
		} catch (\Exception $e) {
			
			$xml = false;
			echo '<h3>Erro WebServer TOTVs</h3><br>'.$e->getMessage()."<br>";
			exit();
			
		}
		
		/*
		 * retorna o resultado
		 */
		return $xml;

    }

}