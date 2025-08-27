s<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <div class="col-6 text-right">
                            <div class="button-items">
                                <a href="<?= base_url('manager/usuario') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="form-group row">
                        <label for="usuario_nome" class="col-sm-2 col-form-label text-right">Nome Completo:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" name="usuario_nome" id="usuario_nome" require>
                        </div>
                        <label for="usuario_login" class="col-sm-2 col-form-label text-right">Login:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" name="usuario_login" id="usuario_login">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="usuario_email" class="col-sm-2 col-form-label text-right">E-mail:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="email" value="" name="usuario_email" id="usuario_email" require>
                        </div>
                    </div>

                    <div class="form-group row">
                    <label for="usuario_senha" class="col-sm-2 col-form-label text-right text-primary">Senha de acesso:</label>
                        <div class="col-sm-4">
                            <input class="form-control text-primary" type="password" value="" name="usuario_senha" id="usuario_senha">
                        </div>
                        <label for="usuario_senhac" class="col-sm-2 col-form-label text-right text-primary">Confirme a senha:</label>
                        <div class="col-sm-4">
                            <input class="form-control text-primary" type="password" value="" name="usuario_senhac" id="usuario_senhac">
                        </div>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Cadastrar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
const salvaDados = () => {
    
    let dados = {
        "usuario_nome": $("#usuario_nome").val(),
        "usuario_login": $("#usuario_login").val(),
        "usuario_email": $("#usuario_email").val(),
        "usuario_senha": $("#usuario_senha").val(),
        "usuario_senhac": $("#usuario_senhac").val(),
    }

    if(dados.usuario_nome == ""){ exibeAlerta("error", "<b>Nome Completo</b> obrigatório."); return false; }
    if(dados.usuario_login == ""){ exibeAlerta("error", "<b>Login</b> obrigatório."); return false; }
    if(dados.usuario_email == ""){ exibeAlerta("error", "<b>E-mail</b> obrigatório."); return false; }
    if(dados.usuario_senha == ""){ exibeAlerta("error", "<b>Senha</b> obrigatório."); return false; }
    if(dados.usuario_senhac != dados.usuario_senha){ exibeAlerta("error", "<b>Confirmação de senha</b> inválida."); return false; }

	if(!dados.usuario_email.includes("@eldoradobrasil.com.br")){ exibeAlerta("error", "<b>E-mail</b> deve conter <b>@eldoradobrasil.com.br."); return false; }
    openLoading();

    $.ajax({
        url: "<?= base_url('manager/usuario/action/cadastrar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('manager/usuario/editar'); ?>/'+response.cod);
            }

        },
    });
    
}    
</script>