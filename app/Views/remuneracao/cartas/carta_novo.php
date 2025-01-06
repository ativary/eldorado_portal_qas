<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-8 mb-1 mt-1"><?= $_titulo; ?></h4>
                    <div class="col-4 text-right">
                            <div class="button-items">
                               <a href="<?= base_url('remuneracao/cartas'); ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="form-group row">
                        <label for="descricao" class="col-sm-2 col-form-label text-right">Descrição:</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" value="" name="descricao" id="descricao" require>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="tipo" class="col-sm-2 col-form-label text-right">Tipo de Carta:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="tipo" id="tipo">
                                <option value="">...</option>
                                <option value="1">Promoção</option>
                                <option value="2">Mérito / Enquadramento</option>
                                <option value="4">Enquadramento</option>
                                <option value="3">Remuneração e Benefícios</option>
                                <option value="999">Global</option>
                            </select>
                        </div>
                    </div>

                    
                </div>
                
                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Cadastrar</button>
                </div>
                
            </div><!-- end card -->


        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
const salvaDados = () => {

    var secao = [];
    $("[data-secao]").find('option:selected').each(function(e){
        secao.push($(this).val());
    });

    var funcao = [];
    $("[data-funcao]").find('option:selected').each(function(e){
        funcao.push($(this).val());
    });

    var filial = [];
    $("[data-filial]").find('option:selected').each(function(e){
        filial.push($(this).val());
    });

    let dados = {
        "descricao": $("#descricao").val(),
        "secao": secao,
        "funcao": funcao,
        "filial": filial,
        "tipo": $("#tipo").val()
    }
    
    if(dados.descricao == ""){ exibeAlerta("error", "<b>Descrição</b> não informada."); return false; }
    if(dados.tipo == ""){ exibeAlerta("error", "<b>Tipo</b> não informada."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('remuneracao/cartas/action/cadastrar_carta'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('remuneracao/cartas/editar'); ?>/'+response.cod);
            }

            openLoading(true);
            
        },
    });

}
</script>
<?php
loadPlugin(array('tinymce','select2'));
?>