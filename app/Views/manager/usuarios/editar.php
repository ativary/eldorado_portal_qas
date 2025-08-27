<div class="container-fluid"> 
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
                            <input class="form-control" type="text" value="<?= $resDados[0]['nome']; ?>" name="usuario_nome" id="usuario_nome" require>
                        </div>
                        <label for="usuario_login" class="col-sm-2 col-form-label text-right">Login:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="<?= $resDados[0]['login']; ?>" name="usuario_login" id="usuario_login" disabled>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="usuario_email" class="col-sm-2 col-form-label text-right">E-mail:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="email" value="<?= $resDados[0]['email']; ?>" name="usuario_email" id="usuario_email" require>
                        </div>
                    </div>

                    <div class="form-group row text-left">
                        <label for="usuario_alt_senha" class="col-sm-2 col-form-label text-right"></label>
                        <div class="col-sm-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="usuario_alterar_senha" name="usuario_alterar_senha" data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
                                <label class="custom-control-label" for="usuario_alterar_senha">Alterar Senha?</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row alteracao_senha hidden">
                    <label for="usuario_senha" class="col-sm-2 col-form-label text-right text-success">Senha de acesso:</label>
                        <div class="col-sm-4">
                            <input class="form-control text-success" type="password" value="" name="usuario_senha" id="usuario_senha">
                        </div>
                        <label for="usuario_senhac" class="col-sm-2 col-form-label text-right text-success">Confirme a senha:</label>
                        <div class="col-sm-4">
                            <input class="form-control text-success" type="password" value="" name="usuario_senhac" id="usuario_senhac">
                        </div>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="mdi mdi-content-save-outline"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
const salvaDados = () => {
    
    let dados = {
        "id": <?= $id; ?>,
        "usuario_nome": $("#usuario_nome").val(),
        "usuario_email": $("#usuario_email").val(),
        "usuario_email": $("#usuario_email").val(),
        "usuario_alterar_senha": $("#usuario_alterar_senha").prop('checked'),
        "usuario_senha": $("#usuario_senha").val(),
        "usuario_senhac": $("#usuario_senhac").val(),
    }

    if(dados.usuario_nome == ""){ exibeAlerta("error", "<b>Nome Completo</b> obrigatório."); return false; }
    if(dados.usuario_email == ""){ exibeAlerta("error", "<b>E-mail</b> obrigatório."); return false; }

    if(dados.usuario_alterar_senha){
        if(dados.usuario_senha == ""){ exibeAlerta("error", "<b>Senha</b> obrigatório."); return false; }
        if(dados.usuario_senhac != dados.usuario_senha){ exibeAlerta("error", "<b>Confirmação de senha</b> inválida."); return false; }
    }
	
	if(!dados.usuario_email.includes("@eldoradobrasil.com.br")){ exibeAlerta("error", "<b>E-mail</b> deve conter <b>@eldoradobrasil.com.br."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('manager/usuario/action/editar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            exibeAlerta(response.tipo, response.msg);

        },
    });
    
}
$(function(e){
    $("#usuario_alterar_senha").on('click', function(e){
        var checked = $(this).prop('checked');
        if(checked){
            $(".alteracao_senha").removeClass('hidden');
        }else{
            $(".alteracao_senha").addClass('hidden');
        }
    })
})
</script>