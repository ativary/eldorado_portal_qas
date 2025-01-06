<?php
namespace App\Controllers\Gupy;
use App\Controllers\BaseController;

Class V1 extends BaseController {

    protected $token;
    public $mGupy;

    public function __construct(){
        $this->token = '1709b210-dc36-456e-987c-e9dc108e0a2c';
        $this->mGupy = model('Gupy/GupyModel');
    }

    // request GET api GUPY
    public function get($api, $param_name, $value, $response_id = true){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.gupy.io/api/v1/{$api}?{$param_name}=".trim($value),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'content-type: application/json',
                "Authorization: Bearer ".$this->token
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return ($response_id) ? $response['results'][0]['id'] ?? 0 : $response;

    }

    // request POST api GUPY
    public function post($api, $data){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.gupy.io/api/v1/{$api}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                'content-type: application/json',
                "Authorization: Bearer ".$this->token
            ),
        ));

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response;

    }

    // envia vaga para o GUPY
    public function createJob($id_req){
        
        $DadosVaga = $this->mGupy->PegaDadosRequisicao($id_req)[0];

        // pega os IDs GUPY
        $id_funcao = self::get('roles', 'name', $DadosVaga['codfuncao']);
        
        // monta dados do request
        $request = array(
            'templateId'      => 3599594, // Modelo externo
            'code'            => $DadosVaga['id'],
            'name'            => "Portal RH | {$DadosVaga['id']} | {$DadosVaga['nomefuncao']}",
            'publicationType' => ($DadosVaga['tipo_recrutamento'] == 'E') ? 'external' : 'internal',
            'reason'          => ($DadosVaga['tipo'] == 'S') ? 'staff_replacement' : 'staff_increase',
            'numVacancies'    => $DadosVaga['qtde_vagas'],
            'departmentId'    => 511718, // fixo banco de talentos
            'roleId'          => $id_funcao,
            'branchId'        => ($DadosVaga['coligada'] == 1) ? 631491 : 631532, // ELDORADO BRASIL
            'recruiterEmail'  => 'gleice.melero@eldoradobrasil.com.br',
            'managerEmail'    => 'gleice.melero@eldoradobrasil.com.br',
            'type' => 'vacancy_type_effective',
            'salary'          => array(
                'currency' => 'R$',
                'startsAt' => (float)$DadosVaga['salario']
            ),
            'customFields' => array(
                array(
                    'id'    => 'cda8ea44-ccf8-418e-9c70-6f99ba57b6c9', // coligada
                    'value' => $DadosVaga['coligada']
                ),
                array(
                    'id'    => 'c7ae4906-fb33-4c1c-af87-ec8db06ed890', // função
                    'value' => $DadosVaga['codfuncao'].' - '.$DadosVaga['nomefuncao']
                ),
                array(
                    'id'    => 'a9fdbfa6-0641-4ecc-9993-25973e0e3454', // seção
                    'value' => $DadosVaga['codsecao'].' - '.$DadosVaga['nomesecao']
                ),
                array(
                    'id'    => '8441f525-a576-4e7f-a5ad-e1bc8e4c38a6', // horário
                    'value' => $DadosVaga['codhorario'].' - '.$DadosVaga['nomehorario']
                ),
                array(
                    'id'    => '355937f2-e31c-4f0d-b9a3-7db8699fb45b', // previsão
                    'value' => dtBr($DadosVaga['previsao'])
                ),
                array(
                    'id'    => 'cc30edd6-e81f-4e48-9e65-2a5b62a5b054', // salário
                    'value' => moeda($DadosVaga['salario'])
                ),
                array(
                    'id'    => 'd2051f46-a341-4ab1-9e66-f214622685d6', // faixa salarial
                    'value' => moeda($DadosVaga['porcentagem']).'%'
                ),
                array(
                    'id'    => 'd3804eb6-662b-4e5a-a428-5696b04fbe31', // gs
                    'value' => $DadosVaga['GS']
                ),
                array(
                    'id'    => 'c3565320-aae0-4fb2-9a0f-da871a1a0838', // Acessará Espaço Confinado?
                    'value' => (boolean)($DadosVaga['espaco_confinado'] == 1) ? true : false
                ),
                array(
                    'id'    => '371fa818-77a5-4255-be31-b362d53e074c', // Trabalhará em Altura?
                    'value' => (boolean)($DadosVaga['altura'] == 1) ? true : false
                ),
                array(
                    'id'    => '9160fd9d-4468-4e46-a17f-8083d6dd49a2', // Conduzirá veículos leves?
                    'value' => (boolean)($DadosVaga['veiculos'] == 1) ? true : false
                ),
                array(
                    'id'    => '228635ee-8bbc-4f25-807f-538e9bc71a93', // (Banco) Gestor Seção
                    'value' => $DadosVaga['nomegestor']
                ),
                array(
                    'id'    => 'a0117250-cf23-45cc-9edb-0a2b9bb95b57', // Numero RP
                    'value' => $DadosVaga['id']
                ),
                array(
                    'id'    => '11781001-e27c-41cd-af12-3cd551ef8f29', // Tipo RP
                    'value' => ($DadosVaga['tipo'] == 'A') ? 'Aumento de Quadro' : 'Substituição'
                ),
                array(
                    'id'    => 'f67b1616-2780-4f1a-8969-5cfc0bcb3859', // Funcionário Substituido
                    'value' => $DadosVaga['nomefunc']
                ),
                array(
                    'id'    => 'c3dadd5b-71b7-486a-98a5-fe88b728063b', // Regime de Contratação
                    'value' => 'Efetivo'
                ),
                array(
                    'id'    => 'bca6eee1-af0f-4032-8c3a-923b603f88d4', // centro de custo
                    'value' => $DadosVaga['centrocusto']
                ),
                array(
                    'id'    => '68fdc1ce-6080-4e15-a64d-8d157a467f0e', // solicitante
                    'value' => $DadosVaga['nomesolicitante']
                ),
                array(
                    'id'    => 'eca2b62c-9c99-497d-b1a5-7881d83d3ab9', // filial
                    'value' => (int)substr($DadosVaga['codsecao'],0,3)
                ),
                array(
                    'id'    => 'd3d71e32-4935-46b1-aa3b-8f35a3bd989f', // Interno / Externo
                    'value' => ($DadosVaga['tipo_recrutamento'] == 'E') ? 'Externo' : 'Interno'
                )
            )
        );

        // https://developers.gupy.io/docs/fluxo-de-cria%C3%A7%C3%A3o-de-vagas
        // https://developers.gupy.io/reference/createjob

        // echo '<pre>';
        // // echo ($request);
        // print_r($request);
        // exit();

        $response = self::post('jobs', $request);

        if(isset($response['id'])){
            echo 'Sucesso';
            $this->mGupy->AtualizaDadosGupy($DadosVaga['id'], $response['id'] ?? null, json_encode($response), 1);
            self::createJobVacancies($response['id'], $DadosVaga['id'], $DadosVaga['codposicao']);
        }else{
            echo 'Erro';
            $this->mGupy->AtualizaDadosGupy($DadosVaga['id'], NULL, json_encode($response), 9);
        }

    }

    // lista requisição para criação da vaga
    public function CronCriaVagaGupy($id_requisicao){
        
        $resJobs = $this->mGupy->ListarRequisicao($id_requisicao);
        
        if($resJobs){
            foreach($resJobs as $key => $Job){

                self::createJob($Job['id']);
                unset($Job, $resJobs[$key]);

            }
        }

    }

    // cria as posição na vaga
    public function createJobVacancies($id_job, $id_requisicao, $codposicao){

        // verifica se é requisição coletiva
        if(strlen(trim($codposicao)) <= 0){

            $resPosicoes = $this->mGupy->ListarPosicaoRequisicao($id_requisicao);
            if($resPosicoes){
                foreach($resPosicoes as $key => $Posicao){

                    $request['code']   = $Posicao['codposicao'];
                    $request['status'] = 'valid';

                    $response = self::post("jobs/{$id_job}/vacancies", $request);

                    if(isset($response['id'])){
                        $dados['id_posicao_gupy'] = $response['id'];
                        $dados['id_posicao']      = $Posicao['id'];
                        $this->mGupy->AtualizaDadosPosicaoGupy('C', $dados);
                    }

                }
            }

        }else{

            $request['code']   = $codposicao;
            $request['status'] = 'valid';

            $response = self::post("jobs/{$id_job}/vacancies", $request);

            if(isset($response['id'])){
                $dados['id_posicao_gupy'] = $response['id'];
                $dados['id_requisicao']   = $id_requisicao;
                $this->mGupy->AtualizaDadosPosicaoGupy('I', $dados);
            }

        }

        return true;
    }

    // verifica o status da vaga
    public function CronVerificaStatusVaga(){

        $requisicoes = $this->mGupy->ListarRequisicaoGupy();
        if($requisicoes){
            foreach($requisicoes as $key => $requisicao){

                // busca dados da vaga
                $response = self::get("jobs/{$requisicao['id_vaga_gupy']}/applications", "fields", "currentStep.name,candidate.name,job.id,id,candidate.id", false);
         

                // verifica se possui retorno
                if(($response['totalResults'] ?? 0) > 0){

                    // verifica o tipo de requisição Individual ou Coletiva
                    if(strlen(trim($requisicao['codposicao'])) > 0){
                        // individual

                        // verifica se a situação da vaga é "Contratação"
                        if($response['results'][0]['currentStep']['name'] ==  "Contratação"){

                            // atualiza status da vaga
                            $dados                  = array();
                            $dados['id_requisicao'] = $requisicao['id'];
                            $this->mGupy->AtualizarStatusVagaGupy('I', $dados);

                        }

                    }else{
                        // coletiva

                        // pega dados das posições
                        $posicoes = $this->mGupy->ListarRequisicaoGupyColetivo($requisicao['id']);
                        if($posicoes){
                            foreach($posicoes as $key => $posicao){

                                $id_candidato_gupy = false;
                                foreach($response['results'] as $key2 => $Results){
                                    if($Results['currentStep']['name'] == "Contratação"){
                                        if($Results['candidate']['id'] == $posicao['id_candidato_gupy']){
                                            unset($response['results'][$key2]);
                                            $id_candidato_gupy = false;
                                            break;
                                        }else{
                                            $id_candidato_gupy = $Results['candidate']['id'];
                                        }
                                    }
                                }

                                if($id_candidato_gupy){
                                    // atualiza a posição com o ID encontrado
                                    $dados                      = array();
                                    $dados['id_requisicao']     = $requisicao['id'];
                                    $dados['id_candidato_gupy'] = $id_candidato_gupy;
                                    $dados['id_posicao']        = $posicao['id'];
                                    $this->mGupy->AtualizarStatusVagaGupy('C', $dados);
                                }

                            }
                        }

                        // atualiza o status da requisição
                        $this->mGupy->AtualizarStatusVagaGupyColetiva($requisicao['id']);

                    }

                }

            }
        }

    }



}