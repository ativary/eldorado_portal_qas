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
                            <a href="<?= base_url('manager/perfil') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="form-group row">
                        <label for="perfil_nome" class="col-sm-2 col-form-label text-right">Nome Perfil:</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="<?= $resDados[0]['nome']; ?>" name="perfil_nome" id="perfil_nome" require>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="perfil_descricao" class="col-sm-2 col-form-label text-right">Descrição:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="perfil_descricao" id="perfil_descricao" cols="30" rows="3"><?= $resDados[0]['descr']; ?></textarea>
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
        "perfil_nome": $("#perfil_nome").val(),
        "perfil_descricao": $("#perfil_descricao").val(),
    }

    if(dados.perfil_nome == ""){ exibeAlerta("error", "<b>Nome Perfil</b> não informado."); return false; }
    if(dados.perfil_descricao == ""){ exibeAlerta("error", "<b>Descrição</b> não informada."); return false; }

    $.ajax({
        url: "<?= base_url('manager/perfil/action/editar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            exibeAlerta(response.tipo, response.msg, 2);

        },
    });
    
}    
</script>