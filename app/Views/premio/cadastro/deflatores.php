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
                                <button class="btn btn-success btn-xxs mb-0" onclick="reorganizar()"><i class="fas fa-recycle"></i> Eliminar linhas vazias</button>
                                <a href="<?= base_url('premio/cadastro') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>                

                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label text-center"><b>Deflatores</b></label>
                        <label class="col-sm-2 col-form-label text-center"><b>Considerar apenas Dias</b></label>
                        <label class="col-sm-2 col-form-label text-center"><b>Até # Dias</b></label>
                        <label class="col-sm-2 col-form-label text-center"><b>% Deflator</b></label>
                    </div>
                    <?php for($i=1; $i<=20; $i++) { ?>
                    <div class="form-group row">
                        <label for="deflator_<?= $i ?>" class="col-sm-2 col-form-label text-right">Deflator <?= $i ?>:</label>
                        <div class="col-sm-2">
                            <select class="form-control form-control-sm mb-1" onchange="checaTudo()" name="deflator_<?= $i ?>" id="deflator_<?= $i ?>">
                                <option value="">...</option>
                                <option value="Faltas" <?= ($resDeflatores[0]['deflator_'.$i] == 'Faltas') ? " selected " : ""; ?>>Faltas</option>
                                <option value="Afastamentos" <?= ($resDeflatores[0]['deflator_'.$i] == 'Afastamentos') ? " selected " : ""; ?>>Afastamentos</option>
                                <option value="Atestados" <?= ($resDeflatores[0]['deflator_'.$i] == 'Atestados') ? " selected " : ""; ?>>Atestados</option>
                                <option value="Férias" <?= ($resDeflatores[0]['deflator_'.$i] == 'Férias') ? " selected " : ""; ?>>Férias</option>
                                <option value="Demissão" <?= ($resDeflatores[0]['deflator_'.$i] == 'Demissão') ? " selected " : ""; ?>>Demissão</option>
                                <option value="Admissão" <?= ($resDeflatores[0]['deflator_'.$i] == 'Admissão') ? " selected " : ""; ?>>Admissão</option>
                            </select>
                        </div>
                        
                        <div class="col-sm-2">
                            <select class="form-control form-control-sm mb-1" onchange="checaDias('<?= $i ?>')" name="apenas_dias_<?= $i ?>" id="apenas_dias_<?= $i ?>">
                                <option value="">...</option>
                                <option value="S" <?= ($resDeflatores[0]['apenas_dias_'.$i] == 'S') ? " selected " : ""; ?>>Sim</option>
                                <option value="N" <?= ($resDeflatores[0]['apenas_dias_'.$i] == 'N') ? " selected " : ""; ?>>Não</option>
                            </select>
                        </div>
                    
                        <div class="col-sm-2">
                            <select class="form-control form-control-sm mb-1" onchange="checaDias('<?= $i ?>')" name="dias_<?= $i ?>" id="dias_<?= $i ?>">
                                <option value="">...</option>
                                <?php for($d=1; $d<=31; $d++) { ?>
                                    <option value="<?= $d ?>" <?= ($resDeflatores[0]['dias_'.$i] == $d) ? " selected " : ""; ?>><?= $d; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        
                        <div class="col-sm-2">
                            <input data-money type="text" onkeyup="checaDias('<?= $i ?>')" id="porcent_<?= $i ?>" name="porcent_<?= $i ?>" class="form-control form-control-sm mb-1" placeholder="0,00" value="" require>
                        </div>
            
                    </div>
                    <?php } ?>                           
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success bteldorado_1" id="btnsave" onclick="return salvaDados()"><i class="fas fa-save"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>

const checaDias = (n) => {

    el_apenas_dias = "#apenas_dias_"+n;
    el_dias = "#dias_"+n;
    el_porcent = "#porcent_"+n;
    if($(el_apenas_dias).val() == "S") {
        $(el_dias).val("");
        $(el_porcent).val("");
    }
}

const checaTudo = () => {

    for(n=1; n<=20; n++) {
        el_deflator = "#deflator_"+n;
        el_apenas_dias = "#apenas_dias_"+n;
        el_dias = "#dias_"+n;
        el_porcent = "#porcent_"+n;
        if($(el_deflator).val() == "") {
            $(el_apenas_dias).val("");
            $(el_dias).val("");
            $(el_porcent).val("");
        }
        if($(el_apenas_dias).val() == "S") {
            $(el_dias).val("");
            $(el_porcent).val("");
        }
    }
}

const reorganizar = () => {

    for(v=1; v<=20; v++) {
        for(n=2; n<=20; n++) {
            el_deflator = "#deflator_"+n;
            el_apenas_dias = "#apenas_dias_"+n;
            el_dias = "#dias_"+n;
            el_porcent = "#porcent_"+n;
            el_deflator_ant = "#deflator_"+(n-1);
            el_apenas_dias_ant = "#apenas_dias_"+(n-1);
            el_dias_ant = "#dias_"+(n-1);
            el_porcent_ant = "#porcent_"+(n-1);
            
            if($(el_deflator_ant).val() == "" && $(el_deflator).val() != "") {
                console.log(el_deflator,$(el_deflator).val(),el_deflator_ant,$(el_deflator_ant).val());
                $(el_deflator_ant).val($(el_deflator).val());
                $(el_apenas_dias_ant).val($(el_apenas_dias).val());
                $(el_dias_ant).val($(el_dias).val());
                $(el_porcent_ant).val($(el_porcent).val());
                $(el_deflator).val("");
                $(el_apenas_dias).val("");
                $(el_dias).val("");
                $(el_porcent).val("");
            }
        }
    }
}


const salvaDados = () => {
    reorganizar();
    for(n=1; n<=20; n++) {
        el_deflator = "#deflator_"+n;
        el_apenas_dias = "#apenas_dias_"+n;
        el_dias = "#dias_"+n;
        el_porcent = "#porcent_"+n;
        if($(el_dias).val() == "" && $(el_porcent).val() > "0,00"){ exibeAlerta("error", "<b>% Até N dias do Deflator "+n+"</b> não informado."); return false; }
        if($(el_apenas_dias).val() == "N" && $(el_dias).val() == ""){ exibeAlerta("error", "<b>% Até N dias do Deflator "+n+"</b> não informado."); return false; }
    }
    
    let dados = {
        <?php for($i=1; $i<=20; $i++) { ?>
            "deflator_<?= $i ?>": $("#deflator_<?= $i ?>").val(),
            "apenas_dias_<?= $i ?>": $("#apenas_dias_<?= $i ?>").val(),
            "dias_<?= $i ?>": $("#dias_<?= $i ?>").val(),
            "porcent_<?= $i ?>": $("#porcent_<?= $i ?>").val() == "" ? 0 : parseFloat($("#porcent_<?= $i ?>").val().replaceAll(".", "").replace(",", ".")),
        <?php } ?>
        "id_premio": <?= $id_premio ?>,
    }

    <?php for($i=1; $i<=20; $i++) { ?>
        if(dados.porcent_<?= $i ?> > 100){ exibeAlerta("error", "<b>% Deflator <?= $i ?></b> não pode ser maior que 100."); return false; }
        if(dados.porcent_<?= $i ?> < 0 && dados.dias_<?= $i ?> != ""){ exibeAlerta("error", "<b>% Deflator <?= $i ?></b> não pode ser menor que 0."); return false; }
    <?php } ?>

    $.ajax({
        url: "<?= base_url('premio/cadastro/action/editar_deflatores'); ?>",
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
<script>
    $(document).ready(function(e){
        <?php for($i=1; $i<=20; $i++) { ?>
            $("#porcent_<?= $i ?>").val('<?= $resDeflatores[0]['porcent_'.$i] ?>'.replaceAll(".", ","));
        <?php } ?>
        $("[data-money]").maskMoney({
            prefix: '',
            allowNegative: false,
            allowZero: true,
            thousands: '.',
            decimal: ',',
            affixesStay: true
        }).maskMoney('mask');
    });
</script>

<?php loadPlugin(['select2','maskmoney']); ?>