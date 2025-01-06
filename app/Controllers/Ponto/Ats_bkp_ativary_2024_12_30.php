<?php
namespace App\Controllers\Ponto;
use App\Controllers\BaseController;

Class Ats extends BaseController {

    /**
     * model ponto
     */
    public $mPonto;
    
    /**
     * token
     */
    private $token;

    /**
     * caminho do token
     */
    private $fileToken;

    /**
     * usuário da api
     */
    private $user;

    /**
     * senha da api
     */
    private $pass;

    /**
     * data atual
     */
    private $now;

    /**
     * url api
     */
    private $url;

    public function __construct()
    {
        $this->url          = 'http://hs101.scp.tec.br:9085';
        $this->fileToken    = $_SERVER['DOCUMENT_ROOT'].'\\writable\\cache\\ats.key';
        $this->user         = 'wsintegerp';
        $this->pass         = 'wsintegerp@@20';
        $this->now          = date('Y-m-d H:i:s');
        $this->mPonto       = model('Ponto/EspelhoModel');
    }

    /**
     * executa a api ATS
     */
    public function cronATS(){

        $hoje       = date('Y-m-d');
        $dataInicio = date('Y-m-d', strtotime('-1 days', strtotime($hoje)));
        $dataFim    = $hoje;

        // $hoje       = '2024-10-05';
        // $dataInicio = $hoje;
        // $dataFim    = '2024-10-15';
        // $dataFim    = date('Y-m-d', strtotime('+10 days', strtotime($hoje)));

        echo $dataInicio.'|'.$dataFim;
        
        $this->totalizador($dataInicio, $dataInicio);

    }

    /**
     * executa a api ATS Macro
     */
    public function cronATSMacro(){

        $hoje       = date('Y-m-d');
        // $hoje       = "2024-09-16";
        $dataInicio = date('Y-m-d', strtotime('-1 days', strtotime($hoje)));
        $dataFim    = date('Y-m-d', strtotime('+1 days', strtotime($hoje)));
        // $dataFim    = $hoje;

        // $hoje       = '2024-12-04';
        // $dataInicio = $hoje;
        // $dataFim    = '2024-10-15';
        // $dataFim    = date('Y-m-d', strtotime('+10 days', strtotime($hoje)));

        // $dataInicio = '2024-12-01';
        // $dataFim = '2024-12-19';

        echo $dataInicio.'|'.$dataFim;

        $this->macro($dataInicio, $dataFim);

    }

    private function auth()
    {
        if(file_exists($this->fileToken)){
            $arquivoToken = json_decode(file_get_contents($this->fileToken));
            if($arquivoToken->exp <= $this->now){
                return $this->newToken();
            }else{
                $this->token = $arquivoToken->token;
                return true;
            }
        }else{
            return $this->newToken();
        }

    }

    /**
     * gera novo token
     * 
     * @return object
     */
    private function newToken()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.'/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => '{
                "user": "'.$this->user.'",
                "pass": "'.$this->pass.'"
              }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $result = curl_exec($curl);
        $response = json_decode($result);
        curl_close($curl);

        $file = fopen($this->fileToken, 'w');
        if($file){
            fwrite($file, $result);
            fclose($file);
        }

        if(!isset($response->token)) return false;

        $this->token = $response->token;

        return $response;

    }

    /**
     * executa a api
     * 
     * @param string $api
     * @param date $dataInicio
     * @param date $dataFim
     * @param array $cpf
     * 
     * @return object
     */
    private function post($api, $dataInicio, $dataFim, $cpf = [])
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url.$api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POSTFIELDS => json_encode([
                'datainicio' => $dataInicio,
                'datafim' => $dataFim,
                'cpfs' => $cpf,
                // 'cpfs' => ["22002718830"],
            ]),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'content-type: application/json',
                "Authorization: Bearer ".$this->token
            ),
        ));

        $result = curl_exec($curl);
        // echo $result;exit();
        $response = json_decode($result);
        curl_close($curl);
        
        return $response;
    }

    private function macro($dataInicio, $dataFim)
    {

        if(!$this->auth()) exit('Falha na autenticação');

        $result = self::post('/v1/Buscar_dadosstatusmacros', $dataInicio, $dataFim);
        // echo '<pre>';
        // print_r($result);
        // exit();
        if($result){
            $this->mPonto->saveAtsMacro($result);
        }


    }

    private function totalizador($dataInicio, $dataFim)
    {

        if(!$this->auth()) exit('Falha na autenticação');

        $result = self::post('/v1/buscar_dadostotalizadordia', $dataInicio, $dataFim);
        if($result){
            $this->mPonto->saveAtsTotalizador($result);
        }


    }

}