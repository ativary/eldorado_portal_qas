<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1">Alterar senha de acesso</h4>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(); ?>

                    <div class="form-group row">
                        <label for="senha_atual" class="col-sm-2 col-form-label text-right">Senha atual:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="password" value="" name="senha_atual" id="senha_atual" require>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="senha_nova" class="col-sm-2 col-form-label text-right">Nova Senha:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="password" value="" name="senha_nova" id="senha_nova" require>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="senha_nova_confirma" class="col-sm-2 col-form-label text-right">Confirma nova senha:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="password" value="" name="senha_nova_confirma" id="senha_nova_confirma" require>
                        </div>
                    </div>
                    

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Alterar senha</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
const salvaDados = () => {
    
    let dados = {
        "senha_atual": $("#senha_atual").val(),
        "senha_nova": $("#senha_nova").val(),
        "senha_nova_confirma": $("#senha_nova_confirma").val(),
    }

    if(dados.senha_atual == ""){ exibeAlerta("error", "<b>Senha atual</b> obrigatória."); return false; }
    if(dados.senha_nova == ""){ exibeAlerta("error", "<b>Nova senha</b> obrigatória."); return false; }
    if(dados.senha_nova_confirma == ""){ exibeAlerta("error", "<b>Confirmação da nova senha</b> obrigatória."); return false; }
    if(dados.senha_nova != dados.senha_nova_confirma){ exibeAlerta("error", "<b>Confirmação de senha</b> inválida."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('portal/action/alterar_senha'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 3, window.location.href);
            }

        },
    });
    
}    
</script>