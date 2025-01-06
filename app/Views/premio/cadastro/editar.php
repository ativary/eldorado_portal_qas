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
                            <a href="<?= base_url('premio/cadastro') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">
                    
                    <?= exibeMensagem(true); ?>

                    <div class="form-group row">
                        <label for="premio_nome" class="col-sm-2 col-form-label text-right">Nome do Prêmio:</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="<?= $resPremios[0]['nome'] ?>" name="premio_nome" id="premio_nome" require>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="premio_descricao" class="col-sm-2 col-form-label text-right">Descrição:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="premio_descricao" id="premio_descricao" cols="30" rows="3" require><?= $resPremios[0]['descricao'] ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="premio_status" class="col-sm-2 col-form-label text-right">Status:</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="premio_status" id="premio_status" require>
                                <option value="A" <?= ($resPremios[0]['status'] == 'A') ? " selected " : ""; ?>>Ativo</option>
                                <option value="B" <?= ($resPremios[0]['status'] == 'B') ? " selected " : ""; ?>>Bloqueado</option>
                            </select>
                        </div>
                        <label for="premio_vigencia" class="col-sm-2 col-form-label text-right">Vigência:</label>
                        <div class="col-sm-4">
                        <input class="form-control datepicker m_data" type="date" value="<?= is_null($resPremios[0]['dt_vigencia']) ? '' : date('Y-m-d', strtotime($resPremios[0]['dt_vigencia'])) ?>" name="premio_vigencia" id="premio_vigencia">
                        </div>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
const salvaDados = () => {
    
    let dados = {
        "id": <?= $resPremios[0]['id'] ?>,
        "premio_nome": $("#premio_nome").val(),
        "premio_descricao": $("#premio_descricao").val(),
        "premio_status": $("#premio_status").val(),
        "premio_vigencia": $("#premio_vigencia").val(),
    }

    if(dados.premio_nome == ""){ exibeAlerta("error", "<b>Nome do prêmio</b> não informado."); return false; }
    //if(dados.premio_descricao == ""){ exibeAlerta("error", "<b>Descrição</b> não informada."); return false; }
    if(dados.premio_descricao == ""){ dados.premio_descricao = " "; }
    if(dados.premio_status == ""){ exibeAlerta("error", "<b>Status</b> não informado."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('premio/cadastro/action/editar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/cadastro'); ?>');
            }

        },
    });
    
}
</script>