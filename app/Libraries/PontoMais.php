<?php

namespace App\Libraries;

class PontoMais {
    
    protected $dbportal;
    protected $dbrm;
    protected $token;
    protected $url;
    public $production = true;
    public $id;
    public $idUser;

    public function __construct()
    {

        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->token    = ($this->production) ? '$2a$10$Lpc9GYT541nh2eONlmE4ue2mjk4Kj8uAUf1Nn/XUi9bJD3jZcvgqu' : '';
        $this->url      = ($this->production) ? 'https://api.pontomaisweb.com.br' : '';
        $this->id       = null;
        $this->idUser   = 0;
        
    }

    /**
     * requisições GET
     * 
     * @param string $path
     * @return object
     */
    private function get($path)
    {

        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.pontomaisweb.com.br/{$path}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',
                    'access-token: '.$this->token
                ),
            ));

            $response = json_decode(curl_exec($curl));
            curl_close($curl);

            return $response;

        } catch (\Exception | \Error $e) {
            $this->saveLog($this->id, 'GET:'.$e->getMessage(), 11);
        }

    }

    /**
     * requisições POST
     * 
     * @param string $path
     * @param array $data
     * @return object
     */
    private function post($path, $data)
    {

        try {

            set_time_limit(60*60);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.pontomaisweb.com.br/{$path}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'content-type: application/json',
                    'access-token: '.$this->token
                ),
            ));

            $response = json_decode(curl_exec($curl));
            curl_close($curl);

            log_message('error', 'post('.$path.').'.print_r($response,1));

            return $response;

        } catch (\Exception | \Error $e) {
            $this->saveLog($this->id, 'POST:'.$e->getMessage(), 11);
        }

    }

    /**
     * requisições PUT
     * 
     * @param string $path
     * @param array $data
     * @return object
     */
    private function put($path, $data)
    {
        try {
            set_time_limit(60 * 60);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.pontomaisweb.com.br/{$path}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Access-Token: ' . $this->token
                ],
            ]);

            $response = json_decode(curl_exec($curl));
            curl_close($curl);

            log_message('error', 'put('.$path.').'.print_r($response,1));

            return $response;
        } catch (\Exception | \Error $e) {
            $this->saveLog($this->id, 'PUT:'.$e->getMessage(), 11);
        }
    }


    /**
     * Função para processar a fila de integração com a ponto mais
     */
    public function schedule($id = false)
    {

        if($id) $this->idUser = $_SESSION['log_id'] ?? 0;
        $sucesso = false;

        $schedule = $this->dbrm->query("
            SELECT
                TOP 100
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
                A.acao ACAO
            FROM
                ".DBPORTAL_BANCO."..zcrmportal_api_pontomais A (NOLOCK)
                INNER JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA   = A.codcoligada AND B.CHAPA  = A.chapa COLLATE Latin1_General_CI_AS
                INNER JOIN PPESSOA C (NOLOCK) ON C.CODIGO      = B.CODPESSOA
                INNER JOIN PFUNCAO D (NOLOCK) ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
                INNER JOIN GFILIAL E (NOLOCK) ON E.CODFILIAL = B.CODFILIAL AND E.CODCOLIGADA = B.CODCOLIGADA
            WHERE
                A.situacao NOT IN (1,8)
                ".(($id) ? " AND A.id IN ({$id}) " : "")."
            ORDER BY
                A.dtcad ASC
        ");
        if($schedule){
            foreach($schedule->getResult() as $dados){
                $this->id = $dados->ID;

                if($dados->ACAO == 'INSERT'){
                    $sucesso = $this->admissao($dados);
                }

                if($dados->ACAO == 'DELETE'){
                    $sucesso = $this->demissao($dados);
                }
            }
        }

        return $sucesso;

    }

    /**
     * Função para processar admissão
     */
    private function admissao($dados)
    {

        try {

            // $arrayTurnos      = $this->getIdTurno();
            $arrayCargos      = $this->getIdCargo();
            $arrayCentrocusto = $this->getIdCentroCusto();
            // $arrayEquipes     = $this->getIdEquipe(); // fixo ELDORADO BRASIL - 1519804

            
            $nomeGestor       = 'ELDORADO BRASIL';
            // $team_id        = ($arrayEquipes[$nomeGestor] ?? false) ? ($arrayEquipes[$nomeGestor] ?? false) : false;
            $team_id        = 1519804;
            // $shift_id       = ($arrayTurnos[$dados->CODHORARIO] ?? false) ? ($arrayTurnos[$dados->CODHORARIO] ?? false) : false;
            $shift_id       = 1839950; // fixo 00008 - 5x2 - 08:00 12:00 13:00 17:00 SEG Á SEX
            $cost_center_id = ($arrayCentrocusto[$dados->CODCUSTO] ?? false) ? ($arrayCentrocusto[$dados->CODCUSTO] ?? false) : false;
            $job_title_id   = ($arrayCargos[$dados->CODFUNCAO] ?? false) ? ($arrayCargos[$dados->CODFUNCAO] ?? false) : false;

            if(!$team_id){$this->saveLog($dados->ID, 'Equipe não localizado na PontoMais ('.$nomeGestor.')', 9); return false;}
            if(!$shift_id){$this->saveLog($dados->ID, 'Horário não localizado na PontoMais ('.$dados->CODHORARIO.')', 9); return false;}
            if(!$cost_center_id){$this->saveLog($dados->ID, 'Centro de Custo não localizado na PontoMais ('.$dados->CODCUSTO.')', 9); return false;}
            if(!$job_title_id){$this->saveLog($dados->ID, 'Função não localizado na PontoMais ('.$dados->CODFUNCAO.')', 9); return false;}

            $arrayPontoMais = [
                'registration_number'    => $dados->CHAPA,
                'name'                   => $dados->NOME,
                'cpf'                    => mask("###.###.###-##", $dados->CPF),
                'initial_date'           => dtBr($dados->DATAADMISSAO),
                'is_clt'                 => true,
                'live_abroad'            => false,
                'nis'                    => mask("###.#####.##-#", $dados->PIS),
                'team_id'                => $team_id,
                'cost_center_id'         => $cost_center_id,
                'job_title_id'           => $job_title_id,
                'shift_id'               => $shift_id,
                'email'                  => $dados->EMAIL,
                'has_time_cards'         => true,
                'time_card_source'       => 0,
                'admission_date'         => dtBr($dados->DATAADMISSAO),
                // 'picture'                => '',
                'group_id'               => 72663,
                'password'               => '12345678',
                // 'whats_app_number'       => '',
                'business_unit_cnpj_cpf' => [$dados->CNPJ],
                'pin'                    => substr($dados->CPF,-4)
            ];

            $jsonPontoMais = ["employee" => $arrayPontoMais];

            $json['cpf'] = mask("###.###.###-##", $dados->CPF);
            $json['nis'] = mask("###.#####.##-#", $dados->PIS);

            // verifica se o funcionário já existe no ponto mais
            $check                  = $this->getFuncionarioPontoMais($json);
            $idFuncionarioPontoMais = $check->id ?? false;

            log_message('error', 'check().'.print_r($check,1));


            if($idFuncionarioPontoMais){
                
                $jsonPontoMais['employee']['time_card_source'] = 3;
                log_message('error', 'checkDADOS().'.print_r($jsonPontoMais,1));
                $response = $this->put('external_api/v1/employees/'.$idFuncionarioPontoMais, $jsonPontoMais);

                if(($response->id ?? false)){
                    $this->saveLog($dados->ID, 'Integrado com sucesso', 1, $response);
                    return true;
                }

            }else{

                $response = $this->post('external_api/v1/employees', $jsonPontoMais);

                if(!($response->id ?? false)){
                    $this->saveLog($dados->ID, 'Falha ao cadastrar funcionário na Ponto Mais.', 9, $response);
                    return false;
                }

                // cria o usuário
                $user['send_confirmation_email'] = false;
                $user['group_id']                = 72663;
                $user['password']                = '12345678';
                $user['password_confirmation']   = '12345678';
                $dadosUser['user']               = $user;
                $responseUser                    = $this->post('external_api/v1/employees/'.$response->id.'/user', $dadosUser);

                if(!isset($responseUser->success)){
                    $this->saveLog($dados->ID, 'Falha ao criar usuário na Ponto Mais.', 9, $responseUser);
                    return false;
                }

                $jsonUp['time_card_source'] = '3';
                $dadosEmp['employee']          = $jsonUp;
                $responseUpdate             = $this->put('external_api/v1/employees/'.$response->id, $dadosEmp);

                if($responseUpdate->success ?? false){
                    $this->saveLog($dados->ID, 'Integrado com sucesso', 1, $responseUpdate);
                    return true;
                }

                $this->saveLog($dados->ID, 'Falha na integração[1]', 9, $responseUpdate);
                return false;

            }

            $this->saveLog($dados->ID, 'Erro de integração[2]', 10, $response);

            return false;

        } catch (\Exception | \Error $e) {
            $this->saveLog($this->id, 'admissao():'.$e->getMessage(), 12);
            return false;
        }

    }

    /**
     * Função para processar demissão
     */
    private function demissao($dados)
    {

        try{

            $json['cpf'] = mask("###.###.###-##", $dados->CPF);
            $json['nis'] = mask("###.#####.##-#", $dados->PIS);

            // verifica se o funcionário já existe no ponto mais
            $check                  = $this->getFuncionarioPontoMais($json);
            $idFuncionarioPontoMais = $check->id ?? false;
            if($idFuncionarioPontoMais){

                $dataDemissao = (strlen(trim($dados->DATADEMISSAO)) > 0) ? $dados->DATADEMISSAO : date('Y-m-d');
                
                $data = [
                    'date'           => dtEn($dataDemissao, true),
                    'effective_date' => dtEn($dataDemissao, true),
                    'motive'         => 2
                ];
                $jsonPontoMais = ["employee" => $data];
                $response      = $this->put('external_api/v1/employees/'.$idFuncionarioPontoMais.'/dismiss', $jsonPontoMais);

                log_message('error', 'responseDismiss().'.print_r($response,1));

                if(!($response->success ?? false)){
                    $this->saveLog($dados->ID, 'Erro ao demitir colaborador na PontoMais', 10, $response);
                    return false;
                }

                $jsonUp['time_card_source'] = '0';
                $dadosEmp['employee']       = $jsonUp;
                $responseUpdate             = $this->put('external_api/v1/employees/'.$idFuncionarioPontoMais, $dadosEmp);

                $user['active']    = false;
                $dadosUser['user'] = $user;
                $responseUser      = $this->put('external_api/v1/employees/'.$idFuncionarioPontoMais.'/user', $dadosUser);

                log_message('error', 'responseUserDemiss().'.print_r($responseUser,1));

                if(!isset($responseUser->success)){
                    $this->saveLog($dados->ID, 'Falha ao inativar usuário na PontoMais.', 9, $responseUser);
                    return false;
                }

                $this->saveLog($dados->ID, 'Integrado com sucesso', 1, $response);

                return true;

            }

            $this->saveLog($dados->ID, 'Não localizado cadastro do colaborador na PontoMais', 9);

            return false;

        } catch (\Exception | \Error $e) {
            $this->saveLog($this->id, 'demissao():'.$e->getMessage(), 12);
            return false;
        }

    }

    /**
     * Função para listar os turnos cadastrados na pontomais
     */
    private function getIdTurno()
    {
        
        $path     = 'external_api/v1/shifts?attributes=id,code,name,shift_type,advanced,flexible,flexible_interval,auto_interval,without_holidays,holiday&count=true&page=1&per_page=999999';
        $response = $this->get($path);

        $array_response = [];
        if (!empty($response->shifts)) {
            foreach ($response->shifts as $shift) {
                $array_response[$shift->code] = $shift->id;
            }
        }

        return $array_response;
	}
    
    /**
     * Função para lista os cargos cadastrados no ponto mais
     */
    private function getIdCargo()
    {
        
        $path     = 'external_api/v1/job_titles?attributes=id,code,name,female_name,cbo&count=true&page=1&per_page=999999';
        $response = $this->get($path);

        $array_response = [];
        if (!empty($response->job_titles)) {
            foreach ($response->job_titles as $job) {
                $array_response[$job->code] = $job->id;
            }
        }

        return $array_response;

		
	}

    /**
     * Função para lista os centros de custo cadastrados no ponto mais
     */
    private function getIdCentroCusto()
    {
        
        $path     = 'external_api/v1/cost_centers';
        $response = $this->get($path);

        // log_message('error', 'arrayCentrocusto().'.print_r($response,1));

        $array_response = [];
        if (!empty($response->cost_centers)) {
            foreach ($response->cost_centers as $center) {
                $array_response[$center->code] = $center->id;
                $array_response[$center->name] = $center->id;
            }
        }

        return $array_response;
		
	}

    /**
     * Função para listar as equipes da pontomais
     */
    private function getIdEquipe()
    {
        
        $path     = 'external_api/v1/teams?attributes=id,code,name,department,leaders&count=true&page=1&per_page=999999';
        $response = $this->get($path);

        $array_response = [];
        if (!empty($response->teams)) {
            foreach ($response->teams as $team) {
                if (!empty($team->name)) {
                    $array_response[$team->name] = $team->id;
                }
            }
        }

        return $array_response;
		
	}

    /**
     * Retorna o gestor do funcionário
     */
    private function getNomeGestorEquipeFuncionario($chapa, $coligada)
    {
        $query = "SELECT * FROM CRM_HIERARQUIA3 WHERE CHAPA = ? AND CODCOLIGADA = ?";
        $res   = $this->dbrm->query($query, [$chapa, $coligada]);

        if($res){
            $res = $res->getResultArray();
			$nivel = false;
			if(strlen(trim($res[0]['NIVEL_01'])) > 0) $nivel = $res[0]['NIVEL_01'];
			if(strlen(trim($res[0]['NIVEL_02'])) > 0) $nivel = $res[0]['NIVEL_02'];
			if(strlen(trim($res[0]['NIVEL_03'])) > 0) $nivel = $res[0]['NIVEL_03'];
			if(strlen(trim($res[0]['NIVEL_04'])) > 0) $nivel = $res[0]['NIVEL_04'];
			if(strlen(trim($res[0]['NIVEL_05'])) > 0) $nivel = $res[0]['NIVEL_05'];
			if(strlen(trim($res[0]['NIVEL_06'])) > 0) $nivel = $res[0]['NIVEL_06'];
			
			if($nivel){
				
				$nivel_exp = explode("||", $nivel);
				$nivel_func = trim($nivel_exp[1]);
				return $nivel_func;
				
			}
		}

        return false;
    }

    /**
     * Função para pegar o ID do funcionário da pontomais
     */
    private function getFuncionarioPontoMais($dados)
    {
        
        $json['cpf'] = $dados['cpf'];
        $json['nis'] = $dados['nis'];
        $check       = $this->post('external_api/v1/employees/exists', ['employee' => $json]);
        $cadastroAtivo = false;
        
		if(!isset($check->errors)){
			$employees       = $check->employees;
			$business_active = false;
			foreach($employees as $key => $value){
				if($employees[$key]->active == 1){
                    $cadastroAtivo = true;
					$business_active['indice']             = $key;
					$business_active['business_unit_id']   = $employees[$key]->business_unit_id;
					$business_active['business_unit_name'] = $employees[$key]->business_unit_name;
					break;
				}
			}
            if(!$cadastroAtivo) return false;
			$check->id                 = $check->meta->ids[$business_active['indice']];
			$check->success            = 'success';
			$check->business_unit_id   = $business_active['business_unit_id'];
			$check->business_unit_name = $business_active['business_unit_name'];
		}
		
        if(isset($check->success) && $cadastroAtivo){
            return $check;
        }

        return false;

    }

    /**
     * Função para gravar log
     */
    private function saveLog($id, $message, $situacao, $response = [])
    {

        $this->dbportal->query(" UPDATE zcrmportal_api_pontomais SET message = '{$message}', response = '".json_encode($response)."', situacao = '{$situacao}', usualt = {$this->idUser}, dtalt = getdate() WHERE id = '{$id}' ");

    }


}

