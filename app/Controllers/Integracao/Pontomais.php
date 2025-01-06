<?php
namespace App\Controllers\Integracao;
use App\Controllers\BaseController;

Class Pontomais extends BaseController {

    private $mPontomais;

    public function __construct()
    {
        parent::__construct('Integração');
        $this->_moduloName = '<i class="mdi mdi-nfc-tap"></i> Integração';
        $this->_breadcrumb->add('Pontomais', 'integracao/pontomais');
        $this->mPontomais = model('Integracao/PontomaisModel');
    }

    public function index()
    {
        redireciona(base_url('integracao/pontomais/log'));
    }

    public function log()
    {
        parent::VerificaPerfil('INTEGRACAO_PONTOMAIS_LOG');
        $dados['_titulo'] = "Log de Integração";
        $this->_breadcrumb->add($dados['_titulo'], 'integracao/pontomais/log');
        $dados['resColoborador']    = $this->mPontomais->logColaborador();
        $dados['resHorario']        = $this->mPontomais->logHorario();
        $dados['resAfd']            = $this->mPontomais->logAfd();

        return parent::ViewPortal('integracao/pontomais/log', $dados);
    }

    private function get($api)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.pontomaisweb.com.br/{$api}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'content-type: application/json',
                'access-token: $2a$10$Lpc9GYT541nh2eONlmE4ue2mjk4Kj8uAUf1Nn/XUi9bJD3jZcvgqu'
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;

    }
    
    private function post($api, $data)
    {

        set_time_limit(60*60);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.pontomaisweb.com.br/{$api}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'content-type: application/json',
                'access-token: $2a$10$Lpc9GYT541nh2eONlmE4ue2mjk4Kj8uAUf1Nn/XUi9bJD3jZcvgqu'
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;

    }

    public function get_file_afd_units()
    {

        // self::get_file_afd_teams();
        // exit();

        $uuid = uuid();
        self::createLog($uuid, 'Start process', 1);

        try {

            $data = new \DateTime(date('Y-m-d'));
            $data->sub(new \DateInterval('P7D'));
            $data7Dias = $data->format('Y-m-d');

            $data = new \DateTime(date('Y-m-d'));
            $data->sub(new \DateInterval('P1D'));
            $dataOntem = $data->format('Y-m-d');
            // $dataOntem = '2022-09-16';

            self::createLog($uuid, 'RequestAPI getUnits: /external_api/v1/business_units?page=1&per_page=100', 1);
            $getUnits = self::get('external_api/v1/business_units?page=1&per_page=100');
            self::createLog($uuid, 'Response getUnits: '.json_encode($getUnits), 1);
            
            if(file_exists("E:\afd_pontomais\AFD_pontomais.txt")){
                self::createLog($uuid, 'File exists: E:\afd_pontomais\AFD_pontomais.txt', 1);
                rename('E:/afd_pontomais/AFD_pontomais.txt', 'E:/afd_pontomais/importados/AFD_pontomais_'.date('Y-m-d H-i-s').'.txt');
                self::createLog($uuid, 'Move file to: E:/afd_pontomais/importados/AFD_pontomais_'.date('Y-m-d H-i-s').'.txt', 1);
                sleep(2);
                self::createLog($uuid, 'Delete file: E:\afd_pontomais\AFD_pontomais.txt', 1);
                //unlink("E:\afd_pontomais\AFD_pontomais.txt");
            }else{
                self::createLog($uuid, 'File not exists: E:\afd_pontomais\AFD_pontomais.txt', 3);
            }

            if(is_array($getUnits['business_units'])){
                self::createLog($uuid, 'Is Array: $getUnits[\'business_units\']', 1);
                foreach($getUnits['business_units'] as $key => $Units){
                    self::createLog($uuid, 'Start Foreach Id('.$Units['id'].'): $getUnits[\'business_units\']', 1);
                    
                    $request['afd_export'] = [
                        'start_date' => $data7Dias,
                        'end_date' => $dataOntem,
                        'business_unit_id' => $Units['id'],
                        'inmetro' => false,
                        'local_date_time' => true
                    ];
                    self::createLog($uuid, 'Request external_api/v1/afd/export: '.json_encode($request), 1);
                    $fileAFD = self::post('external_api/v1/afd/export', $request);
                    self::createLog($uuid, 'Response external_api/v1/afd/export: '.json_encode($fileAFD), 1);
                    if(isset($fileAFD['content'])){
                        self::createLog($uuid, 'Exists content', 1);
                        $arquivo = "E:\afd_pontomais\AFD_pontomais.txt";
                        $fp = fopen($arquivo, "a+");
                        fwrite($fp, $fileAFD['content']);
                        fclose($fp);
                        self::createLog($uuid, "File AFD {$Units['id']} generated success.", 1);

                        echo date('d/m/Y H:i:s')." - File AFD {$Units['id']} generated success.<br>";

                    }else{
                        echo date('d/m/Y H:i:s')." - No File AFD {$Units['id']}.<br>";
                        self::createLog($uuid, "No File AFD {$Units['id']}.", 3);
                    }
                }
                
            }else{
                self::createLog($uuid, 'File not exists: E:\afd_pontomais\AFD_pontomais.txt', 1);
            }

            self::createLog($uuid, 'End process', 1);

        } catch (\Exception | \Error $e) {
            self::createLog($uuid, 'Internal Error: '.$e->getMessage(), 2);
        }

    }

    public function get_file_afd_teams()
    {

        $uuid = uuid();
        self::createLog($uuid, 'Start process', 1);

        try {

            // $data = new \DateTime(date('Y-m-d'));
            // $data->sub(new \DateInterval('P1D'));
            // $dataOntem = $data->format('Y-m-d');
            $dataOntem = '2022-09-16';
            
            if(file_exists("E:\afd_pontomais\AFD_pontomais.txt")){
                self::createLog($uuid, 'File exists: E:\afd_pontomais\AFD_pontomais.txt', 1);
                rename('E:/afd_pontomais/AFD_pontomais.txt', 'E:/afd_pontomais/importados/AFD_pontomais_'.date('Y-m-d H-i-s').'.txt');
                self::createLog($uuid, 'Move file to: E:/afd_pontomais/importados/AFD_pontomais_'.date('Y-m-d H-i-s').'.txt', 1);
                sleep(2);
                self::createLog($uuid, 'Delete file: E:\afd_pontomais\AFD_pontomais.txt', 1);
                //unlink("E:\afd_pontomais\AFD_pontomais.txt");
            }else{
                self::createLog($uuid, 'File not exists: E:\afd_pontomais\AFD_pontomais.txt', 3);
            }
            
            $request['afd_export'] = [
                'start_date' => $dataOntem,
                'end_date' => '2022-10-15',
                'team_id' => 134566,
                'inmetro' => false,
                'local_date_time' => true
            ];
            self::createLog($uuid, 'Request external_api/v1/afd/export: '.json_encode($request), 1);
            $fileAFD = self::post('external_api/v1/afd/export', $request);
            self::createLog($uuid, 'Response external_api/v1/afd/export: '.json_encode($fileAFD), 1);
            if(isset($fileAFD['content'])){
                self::createLog($uuid, 'Exists content', 1);
                $arquivo = "E:\afd_pontomais\AFD_pontomais.txt";
                $fp = fopen($arquivo, "a+");
                fwrite($fp, $fileAFD['content']);
                fclose($fp);
                self::createLog($uuid, "File AFD {Team 134566} generated success.", 1);

                echo date('d/m/Y H:i:s')." - File AFD {Team 134566} generated success.<br>";

            }else{
                echo date('d/m/Y H:i:s')." - No File AFD {Team 134566}.<br>";
                self::createLog($uuid, "No File AFD {Team 134566}.", 3);
            }
                
          

            self::createLog($uuid, 'End process', 1);

        } catch (\Exception | \Error $e) {
            self::createLog($uuid, 'Internal Error: '.$e->getMessage(), 2);
        }

    }

    private function createLog($uuid, $message, $type)
    {

        $data = [
            'uuid' => $uuid,
            'message' => $message,
            'type' => $type,
            'created_at' => date('Y-m-d H:i:s'),
            'process' => 'afd_import_pontomais_x_rm'
        ];
        $this->mPontomais->createLog($data);

    }

    public function action($act)
    {

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            case 'detalhes_logafd':
                exit(json_encode($this->mPontomais->logAfdDetais($dados), true));
                break;
        }
    }

}