<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\Breadcrumb;
use App\Models\AcessoModel;


/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{

    protected $session;
    protected $_breadcrumb;
    protected $_moduloName;
    protected $permissao = false;

    public function __construct($_modulo = false)
    {
        $this->session = \Config\Services::session();
        $this->session->start();
        $this->_breadcrumb = new Breadcrumb();
        $this->_breadcrumb->add($_modulo);
        $this->response = service('response');

        helper('default');
    }

    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['default', 'manager', 'url'];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = \Config\Services::session();

        
    }

    public function ViewPortal($view, $dados = [], $param = []){

        if (! is_file(APPPATH . 'Views/' . $view . '.php')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($view);
        }

        $mPortal = model('PortalModel');

        $dados['_resColigada'] = $mPortal->ListarColigada();
        $dados['_resChapa'] = $mPortal->ListarDadosFuncionario(session()->get('log_login'));

        $dadosColigadaAtiva = "";
        if(session()->get('func_coligada')){
            $dadosColigada = $mPortal->ListarColigada(session()->get('func_coligada'));
            $dadosColigadaAtiva = $dadosColigada[0]['CODCOLIGADA'].' - '.$dadosColigada[0]['NOMEFANTASIA'];
        }
    
        $dados['_dadosAtalho'] = $mPortal->ListarAtalho();
        $dados['_dadosColigadaAtiva'] = $dadosColigadaAtiva;
        $dados['_moduloName'] = $this->_moduloName;
        $dados['_breadcrumb'] = $this->_breadcrumb->render();

        $dados['view'] = $view;
        $dados['dados'] = $dados;
        $dados['param'] = $param;
        
        return view('template/main', $dados, $param);
    
    }

    public function ViewDefault($view, $dados = [], $param = []){

        if (! is_file(APPPATH . 'Views/' . $view . '.php')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException($view);
        }
    
        $dados['view'] = $view;
        $dados['dados'] = $dados;
        $dados['param'] = $param;
        
        return view('template/main_default', $dados, $param);
    
    }

    public function VerificaPerfil($perfil, $redireciona = true){

        $mAcesso = model('AcessoModel');
        $TemPermissao = $mAcesso->VerificaPerfil($perfil);
        if($redireciona && !$TemPermissao){
            notificacao('danger', 'Sem premissão para acessar esta página');
            redireciona();
        }

        return $TemPermissao;

    }

}
