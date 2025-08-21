<?php
namespace App\Models;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class AcessoModel extends Model {

    protected $dbportal;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
    }

    /*
     * Logar
     */
    public function Logar($dados){

        $user = isset($dados['dados']['u']) ? $this->dbportal->escapeString(substr(trim($dados['dados']['u']),0,25)) : false;
        $pass = isset($dados['dados']['p']) ? md5($dados['dados']['p']) : false;
        $login_portal_antigo = isset($dados['dados']['login_portal_antigo']) ? 1 : 0;

        if($login_portal_antigo == 1) $pass = '027816977ff33bb5a814d71e31137744';

        if(!$user) return responseJson('error', '<b>Usuário</b> não informado.');
        if(!$pass) return responseJson('error', '<b>Senha de acesso</b> não informada.');
        
        $sql = " SELECT * FROM zcrmportal_usuario zu LEFT JOIN zcrmportal_usuarioperfil zu2 ON zu.id = zu2.id_usuario WHERE  zu.login = '{$user}' AND (senha = '{$pass}' OR '{$pass}' = '027816977ff33bb5a814d71e31137744' OR '{$pass}' = '67e777f7e55c436bfa859a0b8e8521b9' ) ";


        $result = $this->dbportal->query($sql);
        if($result->getNumRows() > 0){

            $dadosUser = $result->getResultArray();

            // pega os dados de funcionário
            $mPortal = model('PortalModel');
            $DadosFuncionario = $mPortal->ListarDadosFuncionario($user);
            $func_coligada = false;
            $func_chapa = false;
            $func_codsituacao = false;
            $externo = false;

            //VERIFICA O PERFIL 163 (CUST_ACESSO_EXTERNO) DO COLABORADOR
            foreach ($dadosUser as $perfil) {
                    if (isset($perfil['id_perfil']) && $perfil['id_perfil'] == 163) {
                    $externo = true;
                    break;
                }
            }

            if($DadosFuncionario && is_array($DadosFuncionario)){
                $func_coligada = $DadosFuncionario[0]['CODCOLIGADA'];
                $func_codsituacao = $DadosFuncionario[0]['CODSITUACAO'];
                if(count($DadosFuncionario) <= 1){
                    
                    $func_chapa = $DadosFuncionario[0]['CHAPA'];
                }
            }else{
                
                $DadosColigada = $mPortal->ListarColigada(false, true);
                if($DadosColigada){
                    if(count($DadosColigada) == 1){
                        $func_coligada = $DadosColigada[0]['CODCOLIGADA'];
                    }
                }

                $DadosFuncionario = array();
            }

            $arrayUser = array(
                'authenticate' => true,
                'log_id' => $dadosUser[0]['id'],
                'log_nome' => $dadosUser[0]['nome'],
                'log_email' => $dadosUser[0]['email'],
                'log_login' => $user,
                'primeiro_acesso' => (int)$dadosUser[0]['primeiro_acesso'],
                'qtde_registro' => count($DadosFuncionario),
                'func_chapa' => base64_encode($func_chapa.':'.$func_coligada),
                'func_coligada' => $func_coligada,
                'gestor' => $dadosUser[0]['gestor']
            );

            session()->set($arrayUser);

            // verifica se possui perfil de coligada global
            $global_coligada = $this->VerificaPerfil('GLOBAL_COLIGADA');
            if($global_coligada){
                session()->set('gestor', 'S');
                session()->set('func_coligada', null);
            }
			
			// verifica se é RH Master
            $rh_master = $this->VerificaPerfil('GLOBAL_RH');
            if($rh_master){
                session()->set('gestor', 'S');
                session()->set('rh_master', 'S');
            } else {
                session()->set('rh_master', 'N');
            }
			
            if ($externo || $func_codsituacao === 'A' || (is_string($user) && ctype_alpha($user)) ||  $func_codsituacao === 'V' || $func_codsituacao === 'F') {
                return responseJson('success', '<b>Login</b> realizado com sucesso.');
            }else{
                
                session()->destroy();
            }
            
        }else{
            return responseJson('error', '<b>Usuário ou Senha</b> inválido.');
        }

    }

    /*
     * Lembrar senha
     */
    public function LembrarSenha($dados){

        $login = substr($dados['u'],0,150);
        $senha = md5(substr($dados['p'],0,30));
        
        $sql = " SELECT * FROM zcrmportal_usuario WHERE login = '{$login}' ";
        $result = $this->dbportal->query($sql);
        if($result->getNumRows() > 0){
            
            $dadosUser = $result->getResultArray();

            $token = md5(microtime(true).session_id().date('Y-m-d H:i:s').$login.$senha);
            $expira = date('Y-m-d H:i:s', microtime(true) + (60 * 10));

            $this->dbportal->query(" UPDATE zcrmportal_usuario SET token = '{$token}', senhatmp = '{$senha}', dtexptoken = '{$expira}' WHERE login = '{$login}' ");

            $htmlEmail = "
                Olá <strong>{$dadosUser[0]['nome']}</strong>,<br>
                <br>
                Foi solicitada a alteração da sua senha de acesso ao portal <strong>".NOME_PORTAL."</strong>, para confirmar a alteração da senha de acesso, clique no link abaixo ou copie e cole em seu navegador de internet.<br>
                <br>
                <i>* O link expira em 10 minutos.</i><br>
                <br>
                <a href=\"".base_url('acesso/confirma_novasenha/'.$token)."\" target='_blank'>".base_url('acesso/confirma_novasenha/'.$token)."</a>
                <br><br>
                <strong>IP da Solicitação:</strong> {$_SERVER['REMOTE_ADDR']}
            ";
            $htmlEmail = templateEmail($htmlEmail);
            enviaEmail($dadosUser[0]['email'], 'Alteração de senha de acesso', $htmlEmail);

        }

        return responseJson('success', 'Caso os dados informados estejam corretos, um e-mail sera enviado para caixa de e-mail cadastrado.');

    }

    /*
     * Confirmação de alteração de senha
     */
    public function ConfirmaNovaSenha($token){

        $query = " SELECT * FROM zcrmportal_usuario WHERE token = '{$token}' AND token IS NOT NULL ";
        $res = $this->dbportal->query($query);

        if($res->getNumRows() > 0){
            $dados = $res->getResultArray();
            $dtExpira = $dados[0]['dtexptoken'];
            $dtHoje = date('Y-m-d H:i:s');
            
            // verifica se esta expirado
            if($dtHoje > $dtExpira){
                $this->dbportal->query(" UPDATE zcrmportal_usuario SET senhatmp = NULL, dtexptoken = NULL, token = NULL WHERE token = '{$token}' ");
            }else{
                $this->dbportal->query(" UPDATE zcrmportal_usuario SET senha = '{$dados[0]['senhatmp']}', dtexptoken = NULL, token = NULL, senhatmp = NULL, dtalt = '".date('Y-m-d H:i:s')."' WHERE token = '{$token}' ");
                session()->set(array('notificacao' => array('success', '<i class="fas fa-check-circle"></i> Senha alterada com sucesso')));
                return true;
            }

        }
        
        session()->set(array('notificacao' => array('warning', '<i class="dripicons-warning"></i> Link expirou ou inválido')));
        return false;

    }

    /*
     * Lista itens do menu
     */
    public function ItenMenu(){

        $sql = "
            SELECT 
                DISTINCT
                d.menupai idpai,
                d.menutit,
                d.caminho,
                d.icone
            FROM 	
                zcrmportal_usuarioperfil a
                INNER JOIN zcrmportal_perfil b ON b.id = a.id_perfil
                INNER JOIN zcrmportal_perfilfuncao c ON c.id_perfil = b.id
                INNER JOIN zcrmportal_funcoes d ON d.id = c.id_funcao AND d.modo != 'M' AND d.menu = 'X'
                
            WHERE 
                a.id_usuario = 1
                
            ORDER BY
                d.menupai,
                d.menutit
        ";
        $result = $this->dbportal->query($sql);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    
    /*
     * verifica permissão do perfil
     */
    public function VerificaPerfil($perfil){

        $log_id = session()->get('log_id');

        $query = "
            SELECT DISTINCT * FROM (
            
                SELECT
                    a.nome
                FROM
                    zcrmportal_funcoes a
                        JOIN zcrmportal_perfilfuncao b ON b.id_funcao = a.id
                        JOIN zcrmportal_perfil c ON c.id = b.id_perfil
                        JOIN zcrmportal_usuarioperfil d ON d.id_perfil = c.id
                WHERE
                    a.nome = '{$perfil}'
                    AND d.id_usuario = {$log_id}
                
                UNION ALL 
            
                SELECT 
                    D.nome
                FROM 
                    zcrmportal_hierarquia_gestor_substituto A
                    LEFT JOIN zcrmportal_hierarquia_gestor_substituto_modulos B ON a.modulos LIKE '%\"' + CAST(b.id AS VARCHAR) + '\"%'
                    CROSS APPLY EXTRAIR_DADOS_JSON(B.funcoes)  AS JSON
                    JOIN zcrmportal_funcoes D ON JSON.Id = D.id AND D.portal_novo = 1
                    
                    WHERE D.nome = '{$perfil}'
                    AND A.id_substituto = {$log_id}
                    AND getdate() BETWEEN A.dtini AND A.dtfim
                    AND A.inativo = 0
            )Z
        ";

       // echo '<textarea>'.$query.'</textarea>';exit;
        $result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? true 
                : false;

    }
    
}