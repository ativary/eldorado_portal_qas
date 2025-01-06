<?php
namespace App\Models;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class ManagerModel extends Model {

    protected $dbportal;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
    }

    // -------------------------------------------------------
    // Lista menu do portal
    // -------------------------------------------------------
    public function Menu($global = false){

        $log_id = session()->get('log_id');

        $where = (!$global) ? " WHERE a.id_usuario = {$log_id} " : " WHERE d.menupai = '0' ";

        $query = "
            SELECT 
                DISTINCT
                d.id,
                d.menutit,
                d.nome,
                d.caminho,
                d.icone
            FROM 	
                zcrmportal_usuarioperfil a
                INNER JOIN zcrmportal_perfil b ON b.id = a.id_perfil
                INNER JOIN zcrmportal_perfilfuncao c ON c.id_perfil = b.id
                INNER JOIN zcrmportal_funcoes d ON d.id = c.id_funcao AND d.modo = 'M' AND d.menu = 'X' AND d.menupai IS NOT NULL AND d.portal_novo = 1
                
            {$where}
                
            ORDER BY
                d.menutit
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista itens do menu
    // -------------------------------------------------------
    public function ItenMenu($global = false){

        $log_id = session()->get('log_id');

        $where = (!$global) ? " WHERE a.id_usuario = {$log_id} " : "";

        $query = "
            SELECT 
                DISTINCT
                d.id,
                d.menupai idpai,
                d.menutit,
                d.nome,
                d.caminho,
                d.icone
            FROM 	
                zcrmportal_usuarioperfil a
                INNER JOIN zcrmportal_perfil b ON b.id = a.id_perfil
                INNER JOIN zcrmportal_perfilfuncao c ON c.id_perfil = b.id
                INNER JOIN zcrmportal_funcoes d ON d.id = c.id_funcao AND d.modo != 'M' AND d.menu = 'X' AND d.menupai IS NOT NULL AND d.portal_novo = 1
                
            {$where}
                
            ORDER BY
                d.menupai,
                d.menutit
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- FUNÇÃO -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Listar
    // -------------------------------------------------------
    public function ListarFuncao($id = false){

        $ft_id = ($id) ? " AND id = '{$id}' " : "";

        $query = " SELECT * FROM zcrmportal_funcoes WHERE id > 0 AND portal_novo = 1 ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }    

    // -------------------------------------------------------
    // Deletar
    // -------------------------------------------------------
    public function DeletarFuncao($dados = false){

        if((int)$dados['id'] <= 5) return responseJson('error', 'Falha ao excluir função');

        $query = " DELETE FROM zcrmportal_funcoes WHERE id = '{$dados['id']}' AND portal_novo = 1 ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Função excluída com sucesso')
                : responseJson('error', 'Falha ao excluir função');

    }

    // -------------------------------------------------------
    // Cadastrar
    // -------------------------------------------------------
    public function CadastrarFuncao($dados){

        $nome = strlen(trim($dados['funcao_nome'])) > 0 ? "'{$dados['funcao_nome']}'" : "NULL";
        $descricao = strlen(trim($dados['funcao_descricao'])) > 0 ? "'{$dados['funcao_descricao']}'" : "NULL";
        $modo = strlen(trim($dados['funcao_modo'])) > 0 ? "'{$dados['funcao_modo']}'" : "NULL";
        $caminho = strlen(trim($dados['funcao_caminho'])) > 0 ? "'{$dados['funcao_caminho']}'" : "NULL";
        $menu = strlen(trim($dados['funcao_menu'])) > 0 ? "'{$dados['funcao_menu']}'" : "NULL";
        $titulo = strlen(trim($dados['funcao_titulo'])) > 0 ? "'{$dados['funcao_titulo']}'" : "NULL";
        $icone = strlen(trim($dados['funcao_icone'])) > 0 ? "'{$dados['funcao_icone']}'" : "NULL";
        $atalho = (int)$dados['funcao_atalho'];

        $query = " INSERT INTO zcrmportal_funcoes 
            (nome, descr, modo, caminho, menu, menutit, icone, atalho, portal_novo) 
                VALUES
            ({$nome}, {$descricao}, {$modo}, {$caminho}, {$menu}, {$titulo}, {$icone}, {$atalho}, 1)    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Função cadastrada com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Função cadastrada com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar função');

    }

    // -------------------------------------------------------
    // Editar
    // -------------------------------------------------------
    public function EditarFuncao($dados){

        $nome = strlen(trim($dados['funcao_nome'])) > 0 ? "'{$dados['funcao_nome']}'" : "NULL";
        $descricao = strlen(trim($dados['funcao_descricao'])) > 0 ? "'{$dados['funcao_descricao']}'" : "NULL";
        $modo = strlen(trim($dados['funcao_modo'])) > 0 ? "'{$dados['funcao_modo']}'" : "NULL";
        $caminho = strlen(trim($dados['funcao_caminho'])) > 0 ? "'{$dados['funcao_caminho']}'" : "NULL";
        $menu = strlen(trim($dados['funcao_menu'])) > 0 ? "'{$dados['funcao_menu']}'" : "NULL";
        $titulo = strlen(trim($dados['funcao_titulo'])) > 0 ? "'{$dados['funcao_titulo']}'" : "NULL";
        $icone = strlen(trim($dados['funcao_icone'])) > 0 ? "'{$dados['funcao_icone']}'" : "NULL";
        $atalho = (int)$dados['funcao_atalho'];

        $query = " 
            UPDATE
                zcrmportal_funcoes
            SET
                nome = {$nome}, 
                descr = {$descricao}, 
                modo = {$modo}, 
                caminho = {$caminho}, 
                menu = {$menu}, 
                menutit = {$titulo}, 
                icone = {$icone},
                atalho = {$atalho}
            WHERE
                id = {$dados['id']} AND portal_novo = 1
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Função alterada com sucesso')
                : responseJson('error', 'Falha ao alterar função');

    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- PERFIL -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Listar
    // -------------------------------------------------------
    public function ListarPerfil($id = false){

        $ft_id = ($id) ? " AND id = '{$id}' " : "";

        $query = " SELECT * FROM zcrmportal_perfil WHERE id > 0 ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Deletar
    // -------------------------------------------------------
    public function DeletarPerfil($dados = false){

        //if((int)$dados['id'] <= 5) return responseJson('error', 'Falha ao excluir função');

        $query = " DELETE FROM zcrmportal_perfil WHERE id = '{$dados['id']}' ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Perfil excluido com sucesso')
                : responseJson('error', 'Falha ao excluir perfil');

    }

    // -------------------------------------------------------
    // Cadastrar
    // -------------------------------------------------------
    public function CadastrarPerfil($dados){

        $nome = strlen(trim($dados['perfil_nome'])) > 0 ? "'{$dados['perfil_nome']}'" : "NULL";
        $descricao = strlen(trim($dados['perfil_descricao'])) > 0 ? "'{$dados['perfil_descricao']}'" : "NULL";

        $query = " INSERT INTO zcrmportal_perfil
            (nome, descr) 
                VALUES
            ({$nome}, {$descricao})
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Perfil cadastrado com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Perfil cadastrado com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar perfil');

    }

    // -------------------------------------------------------
    // Editar
    // -------------------------------------------------------
    public function EditarPerfil($dados){

        $nome = strlen(trim($dados['perfil_nome'])) > 0 ? "'{$dados['perfil_nome']}'" : "NULL";
        $descricao = strlen(trim($dados['perfil_descricao'])) > 0 ? "'{$dados['perfil_descricao']}'" : "NULL";

        $query = " 
            UPDATE
                zcrmportal_perfil
            SET
                nome = {$nome}, 
                descr = {$descricao}
            WHERE
                id = {$dados['id']} 
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Perfil alterado com sucesso')
                : responseJson('error', 'Falha ao alterar perfil');

    }

    // -------------------------------------------------------
    // Listar Funções do Perfil
    // -------------------------------------------------------
    public function ListarPerfilFuncao($id_perfil){

        $query = "
            SELECT
                b.id,
                b.nome,
                b.descr
            FROM
                zcrmportal_perfilfuncao a,
                zcrmportal_funcoes b
            WHERE
                a.id_funcao = b.id
                AND a.id_perfil = '{$id_perfil}'
                AND b.portal_novo = 1
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
    }

    // -------------------------------------------------------
    // Deletar Funções do Perfil
    // -------------------------------------------------------
    public function DeletarPerfilFuncao($dados){

        $id_perfil = $dados['id_perfil'];
        $id_funcao = $dados['id_funcao'];

        $query = " DELETE FROM zcrmportal_perfilfuncao WHERE id_perfil = '{$id_perfil}' AND id_funcao = '{$id_funcao}' ";
        $result = $this->dbportal->query($query);
        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Função removida com sucesso do perfil')
                : responseJson('error', 'Falha ao remover função do perfil');
    }

    // -------------------------------------------------------
    // Cadastrar Função Perfil
    // -------------------------------------------------------
    public function CadastrarPerfilFuncao($dados){

        $id_perfil = strlen(trim($dados['id_perfil'])) > 0 ? "'{$dados['id_perfil']}'" : "NULL";
        $id_funcao = strlen(trim($dados['id_funcao'])) > 0 ? "'{$dados['id_funcao']}'" : "NULL";

        // verifica se já esta vinculado ao perfil
        $checkPerfil = $this->dbportal->query(" SELECT * FROM zcrmportal_perfilfuncao WHERE id_perfil = {$id_perfil} AND id_funcao = {$id_funcao} ");
        if($checkPerfil->getNumRows() > 0) return responseJson('error', 'Função já esta cadastrada neste perfil');

        $query = " INSERT INTO zcrmportal_perfilfuncao
            (id_perfil, id_funcao) 
                VALUES
            ({$id_perfil}, {$id_funcao})
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Função cadastrada com sucesso ao perfil')
                : responseJson('error', 'Função já esta cadastrada neste perfil');

    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- USUÁRIOS -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Listar
    // -------------------------------------------------------
    public function ListarUsuario($id = false){

        $ft_id = ($id) ? " WHERE id = '{$id}' " : "";

        $query = " SELECT * FROM zcrmportal_usuario ".$ft_id;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    
    // -------------------------------------------------------
    // Listar perfil do usuário
    // -------------------------------------------------------
    public function ListarUsuarioPerfil($id){
        $query = "
            SELECT
                b.id,
                b.nome
            FROM
                zcrmportal_usuarioperfil a
                    JOIN zcrmportal_perfil b ON b.id = a.id_perfil
            WHERE
                a.id_usuario = {$id}
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    
    // -------------------------------------------------------
    // Listar seção do usuário
    // -------------------------------------------------------
    public function ListarUsuarioSecao($id){

        $coligada = session()->get('func_coligada');

        $query = " SELECT * FROM zcrmportal_usuario_secao WHERE id_usu = '{$id}' AND coligada = '{$coligada}' ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Cadastrar perfil no usuário
    // -------------------------------------------------------
    public function CadastrarUsuarioPerfil($dados){

        $id_usuario = $dados['id_usuario'];
        $id_perfil = $dados['id_perfil'];

        // verifica se os dados já existe
        $checkUsuarioPerfil = $this->dbportal->query(" SELECT * FROM zcrmportal_usuarioperfil WHERE id_perfil = '{$id_perfil}' AND id_usuario = '{$id_usuario}' ");
        if($checkUsuarioPerfil->getNumRows() > 0) return responseJson('error', 'Perfil já cadastrado para este usuário.');

        $query = " INSERT INTO zcrmportal_usuarioperfil 
            (id_usuario, id_perfil) 
                VALUES
            ({$id_usuario}, {$id_perfil})    
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Perfil adicionado com sucesso')
                : responseJson('error', 'Falha ao adicionar perfil ao usuário');

    }

    // -------------------------------------------------------
    // Cadastrar seção no usuário
    // -------------------------------------------------------
    public function CadastrarUsuarioSecao($dados){

        $id = $dados['id'];
        $secoes = isset($dados['secao']) ? $dados['secao'] : false;
        $coligada = session()->get('func_coligada');

        // deleta perfis
        $this->dbportal->query(" DELETE FROM zcrmportal_usuario_secao WHERE id_usu = '{$id}' AND coligada = '{$coligada}' ");

        if(is_array($secoes)){
            foreach($secoes as $key => $codSecao){

                $this->dbportal->query("
                    INSERT INTO zcrmportal_usuario_secao
                    (
                        coligada,
                        id_usu,
                        secao
                    ) VALUES (
                        '{$coligada}',
                        '{$id}',
                        '{$codSecao['codsecao']}'
                    )
                ");

            }
        }

        return responseJson('success', 'Dados alterados com sucesso.');

    }

    // -------------------------------------------------------
    // Cadastrar
    // -------------------------------------------------------
    public function CadastrarUsuario($dados){

        $usuario_nome = strlen(trim($dados['usuario_nome'])) > 0 ? "'{$dados['usuario_nome']}'" : "NULL";
        $usuario_login = strlen(trim($dados['usuario_login'])) > 0 ? "'{$dados['usuario_login']}'" : "NULL";
        $usuario_email = strlen(trim($dados['usuario_email'])) > 0 ? "'{$dados['usuario_email']}'" : "NULL";
        $usuario_senha = strlen(trim($dados['usuario_senha'])) > 0 ? "'".md5($dados['usuario_senha'])."'" : "NULL";
        $usuario_senhac = strlen(trim($dados['usuario_senhac'])) > 0 ? "'".md5($dados['usuario_senhac'])."'" : "NULL";
        if($usuario_senha != $usuario_senhac) return responseJson('error', 'Confirmação de senha inválida.');

        // verifica se os dados já existe
        $checkUsuario = $this->dbportal->query(" SELECT * FROM zcrmportal_usuario WHERE (login = {$usuario_login} OR email = {$usuario_email}) ");
        if($checkUsuario->getNumRows() > 0) return responseJson('error', 'Login ou E-mail já cadastrado.');

        $query = " INSERT INTO zcrmportal_usuario 
            (login, nome, email, senha, ativo, dtcad, primeiro_acesso) 
                VALUES
            ({$usuario_login}, {$usuario_nome}, {$usuario_email}, {$usuario_senha}, 'N', '".date('Y-m-d H:i:s')."', 1)    
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Usuário cadastrado com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Usuário cadastrado com sucesso', $this->dbportal->insertID())
                : responseJson('error', 'Falha ao cadastrar usuário');

    }

    // -------------------------------------------------------
    // Deletar usuário
    // -------------------------------------------------------
    public function DeletarUsuario($dados){

        $id = $dados['id'];

        // deleta os perfis do usuário
        $this->dbportal->query(" DELETE FROM zcrmportal_usuarioperfil WHERE id_usuario = '{$id}' ");

        // deleta as seções do usuário
        $this->dbportal->query(" DELETE FROM zcrmportal_usuario_secao WHERE id_usu = '{$id}' ");

        $query = " DELETE FROM zcrmportal_usuario WHERE id = '{$id}' ";
        $this->dbportal->query($query);
        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Usuário excluído com sucesso')
                : responseJson('error', 'Falha ao excluir usuário');
    }

    // -------------------------------------------------------
    // Deletar perfil do usuário
    // -------------------------------------------------------
    public function DeletarUsuarioPerfil($dados){

        $id_usuario = $dados['id_usuario'];
        $id_perfil = $dados['id_perfil'];

        $query = " DELETE FROM zcrmportal_usuarioperfil WHERE id_usuario = '{$id_usuario}' AND id_perfil = '{$id_perfil}' ";
        $this->dbportal->query($query);
        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Perfil excluído com sucesso do usuário')
                : responseJson('error', 'Falha ao excluir perfil do usuário');
    }

    // -------------------------------------------------------
    // Editar
    // -------------------------------------------------------
    public function EditarUsuario($dados){

        $id = $dados['id'];
        $usuario_nome = $dados['usuario_nome'];
        $usuario_email = $dados['usuario_email'];
        $usuario_senha = strlen(trim($dados['usuario_senha'])) > 0 ? "'".md5($dados['usuario_senha'])."'" : "NULL";
        $usuario_senhac = strlen(trim($dados['usuario_senhac'])) > 0 ? "'".md5($dados['usuario_senhac'])."'" : "NULL";
        $usuario_alterar_senha = $dados['usuario_alterar_senha'] ?? false;
        if($usuario_senha != $usuario_senhac) return responseJson('error', 'Confirmação de senha inválida.');

        $nova_senha = ($usuario_alterar_senha) ? " , senha = ".$usuario_senha : "";

        // veirica se o e-mail já existe caso tenha alterado
        $dadosUsuario = $this->ListarUsuario($id);
        if(!$dadosUsuario) responseJson('error', 'Usuário não localizado.');
        
        if($dadosUsuario[0]['email'] != $usuario_email){
            $checkUsuario = $this->dbportal->query(" SELECT * FROM zcrmportal_usuario WHERE email = '{$usuario_email}' ");
            if($checkUsuario->getNumRows() > 0) return responseJson('error', 'E-mail já cadastrado em outro usuário.');
        }

        $query = " 
            UPDATE
                zcrmportal_usuario
            SET
                nome = '{$usuario_nome}', 
                email = '{$usuario_email}',
                dtalt = '".date('Y-m-d H:i:s')."'
                {$nova_senha}
            WHERE
                id = {$dados['id']} 
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Usuário alterado com sucesso')
                : responseJson('error', 'Falha ao alterar usuário');

    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- MENU -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Cadastra estrutura do menu
    // -------------------------------------------------------
    public function CadastrarMenuEstrutura($dados){

        $menu = isset($dados['menu']) ? $dados['menu'] : false;
        // limpa estrutura do menu
        $this->dbportal->query(" UPDATE zcrmportal_funcoes SET menupai = NULL WHERE portal_novo = 1 ");

        // monta nova estrutura do portal
        if($menu){
            foreach($menu as $key => $Menu){
                if($Menu['type'] == 'M'){
                    
                    $this->dbportal->query(" UPDATE zcrmportal_funcoes SET menupai = 0 WHERE id = '{$Menu['id']}' AND portal_novo = 1 ");

                    // itens do menu
                    $itensMenu = isset($Menu['children']) ? $Menu['children'] : false;
                    if($itensMenu){
                        foreach($itensMenu as $key2 => $ItensMenu){
                            if($ItensMenu['type'] != 'M'){
                                $this->dbportal->query(" UPDATE zcrmportal_funcoes SET menupai = {$Menu['id']} WHERE id = '{$ItensMenu['id']}' AND portal_novo = 1 ");
                            }
                        }
                    }

                }
            }
        }

        return responseJson('success', 'Estrutura de menu alterada com sucesso.');

    }
}