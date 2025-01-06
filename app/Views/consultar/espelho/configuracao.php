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
                        <label for="periodo" class="col-sm-12 col-form-label text-left">Período para consulta do Espelho de Ponto:</label>
                        <div class="col-md-4 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['dtinicio']) ? $EspelhoConfiguracao[0]['dtinicio'] : ""; ?>" name="data_inicio" id="data_inicio" require>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">até</span>
                                    </div>
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['dtfim']) ? $EspelhoConfiguracao[0]['dtfim'] : ""; ?>" name="data_fim" id="data_fim" require>
                            </div>
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
        "data_inicio": $("#data_inicio").val(),
        "data_fim": $("#data_fim").val(),
    }
    
    if(dados.data_inicio == ""){ exibeAlerta("error", "<b>Data de início</b> não informada."); return false; }
    if(dados.data_fim == ""){ exibeAlerta("error", "<b>Data fim</b> não informada."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('consultar/espelho/action/espelho_configuracao'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, window.location.href);
            }

        },
    });
    
}
</script>