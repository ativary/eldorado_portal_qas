<?= menuConfigPonto('Escala'); ?>

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
                        <div class="col-sm-12 ml-auto">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="bloqueio_aviso" name="bloqueio_aviso" <?= ($resConfiguracao[0]['bloqueio_aviso'] ?? null == 1) ? " checked " : ""; ?>>
                                <label class="custom-control-label" for="bloqueio_aviso"> Bloquear requisição com aviso?</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="periodo" class="col-sm-12 col-form-label text-left">Período permitido para troca de escala:</label>
                        <div class="col-md-6 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($resConfiguracao[0]['escala_per_inicio']) ? dtEn($resConfiguracao[0]['escala_per_inicio'], true) : ""; ?>" name="escala_data_inicio" id="escala_data_inicio" required>
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text">até</span>
                                </div>
                                <input class="form-control" type="date" value="<?= isset($resConfiguracao[0]['escala_per_fim']) ? dtEn($resConfiguracao[0]['escala_per_fim'], true) : ""; ?>" name="escala_data_fim" id="escala_data_fim" require>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="periodo" class="col-sm-12 col-form-label text-left">Período permitido para troca de dia:</label>
                        <div class="col-md-6 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($resConfiguracao[0]['dia_per_inicio']) ? dtEn($resConfiguracao[0]['dia_per_inicio'], true) : ""; ?>" name="dia_data_inicio" id="dia_data_inicio" required>
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text">até</span>
                                </div>
                                <input class="form-control" type="date" value="<?= isset($resConfiguracao[0]['dia_per_fim']) ? dtEn($resConfiguracao[0]['dia_per_fim'], true) : ""; ?>" name="dia_data_fim" id="dia_data_fim" require>
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
        "bloqueio_aviso"    : ($("#bloqueio_aviso").prop('checked')) ? 1 : 0,
        "escala_per_inicio" : $("#escala_data_inicio").val(),
        "escala_per_fim"    : $("#escala_data_fim").val(),
        "dia_per_inicio"    : $("#dia_data_inicio").val(),
        "dia_per_fim"       : $("#dia_data_fim").val(),
    }

    if(dados.escala_per_inicio == ""){exibeAlerta('error', '<b>Escala:</b> Data de início não informada.'); return false;}
    if(dados.escala_per_fim == ""){exibeAlerta('error', '<b>Escala:</b> Data de fim não informada.'); return false;}
    if(dados.dia_per_inicio == ""){exibeAlerta('error', '<b>Dia:</b> Data de início não informada.'); return false;}
    if(dados.dia_per_fim == ""){exibeAlerta('error', '<b>Dia:</b> Data de fim não informada.'); return false;}

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