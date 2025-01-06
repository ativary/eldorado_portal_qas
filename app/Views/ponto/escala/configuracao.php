<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="form-group row">
                        <div class="col-sm-11 ml-auto">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="bloqueio_aviso" name="bloqueio_aviso" <?= ($resConfiguracao[0]['bloqueio_aviso'] ?? null == 1) ? " checked " : ""; ?>>
                                <label class="custom-control-label" for="bloqueio_aviso"> Bloquear requisição com aviso?</label>
                            </div>
                        </div>
                    </div>
                    

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar Configuração</button>
                </div>

            </div>

            

        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
const salvaDados = () => {
    let dados = {
        "bloqueio_aviso": ($("#bloqueio_aviso").prop('checked')) ? 1 : 0
    }

    openLoading();

    $.ajax({
        url: "<?= base_url('ponto/escala/action/configuracao') ?>",
        type:'POST',
        data:dados,
        success:function(result){
            
            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 3);
            }
            
            openLoading(true);
            
        },
    });

}
</script>