<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Header;

class Logged implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {

        $urlRequest = explode('/', $request->uri);
        
        if(in_array('acesso', $urlRequest)) return false;
        if(in_array('CronCancelarEscala10Dias', $urlRequest)) return false;
        if(in_array('workflow', $urlRequest)) return false;
        if(in_array('workflow2', $urlRequest)) return false;
        if(in_array('EnviarCartaGestor', $urlRequest)) return false;
        if(in_array('GravaLogOcorrencia', $urlRequest)) return false;
        if(in_array('CronCriaVagaGupy', $urlRequest)) return false;
        if(in_array('CronVerificaStatusVaga', $urlRequest)) return false;
        if(in_array('get_file_afd_units', $urlRequest)) return false;
        if(in_array('download_anexo', $urlRequest)) return false;
        if(in_array('cron_indice_horario', $urlRequest)) return false;
        if(in_array('cronATS', $urlRequest)) return false;
        if(in_array('cronATSMacro', $urlRequest)) return false;
        if(in_array('inativaLider', $urlRequest)) return false;
        if(!session()->get('authenticate')){
            return redirect()->to(base_url('acesso/login'));
        }
        
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {

        $urlRequest = explode('/', $request->uri);
        /*
        if($response->getHeaders()['tem_permissao'] !== null){

            $temPermissao = trim(explode(':', $response->getHeaders()['tem_permissao'])[1]);

            if($temPermissao != "S"){
                return redirect()->to(base_url('acesso/login'));
            }
        }
        */
        if(session()->get('authenticate')){

            // FORÇA ALTERAÇÃO DE SENHA
            if(session()->get('primeiro_acesso') == 1){
                if(!in_array('portal', $urlRequest) && !in_array('alterasenha', $urlRequest)){
                    return redirect()->to(base_url('portal/alterasenha'));
                }
            }
        
            // VERIFICA SE SELECIONOU UMA COLIGADA
            if(!in_array('portal', $urlRequest)){
                if(!session()->get('func_coligada')){
                    return redirect()->to(base_url('portal/coligada'));
                }
            }

            // VERIFICA SE POSSUI MAIS DE UM REGISTRO ATIVO NO TOTVS
            if(!in_array('portal', $urlRequest)){
                if(!session()->get('func_chapa') && (int)session()->get('qtde_registro') > 1){
                    return redirect()->to(base_url('portal/chapa'));
                }
            }

        }

    }
}