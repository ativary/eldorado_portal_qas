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
                        <label for="periodo" class="col-sm-12 col-form-label text-left">Período do Holerite:</label>
                        <div class="col-md-4 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($HoleriteConfiguracao[0]['dtinicio']) ? $HoleriteConfiguracao[0]['dtinicio'] : ""; ?>" name="data_inicio" id="data_inicio" require>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">até</span>
                                    </div>
                                <input class="form-control" type="date" value="<?= isset($HoleriteConfiguracao[0]['dtfim']) ? $HoleriteConfiguracao[0]['dtfim'] : ""; ?>" name="data_fim" id="data_fim" require>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="responsavel" class="col-sm-12 col-form-label text-left">Responsável pelo informe de rendimento:</label>
                        <div class="col-sm-12">
                            <input class="form-control" type="text" value="<?= isset($HoleriteConfiguracao[0]['responsavel_informe_rendimento']) ? $HoleriteConfiguracao[0]['responsavel_informe_rendimento'] : ""; ?>" name="responsavel" id="responsavel" require>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="periodorm" class="col-sm-12 col-form-label text-left">Período para liberação do holerite:</label>
                        <div class="col-sm-12">
                            
                            <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="60"></th>
                                        <th class="text-center" width="80">Cód.</th>
                                        <th class="text-center" width="100">Ano/Mês</th>
                                        <th class="text-left">Descrição</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($resHoleritePeriodoRM): ?>
                                        <?php foreach($resHoleritePeriodoRM as $key => $HoleritePeriodoRM): ?>
                                            <?php
                                                $checkedPeriodo = "";
                                                if($resHoleriteConfiguracaoPeriodo){
                                                    foreach($resHoleriteConfiguracaoPeriodo as $key2 => $HoleriteConfiguracaoPeriodo){
                                                        if($HoleriteConfiguracaoPeriodo['periodo'].'-'.$HoleriteConfiguracaoPeriodo['mescomp'].'-'.$HoleriteConfiguracaoPeriodo['anocomp'] == $HoleritePeriodoRM['NROPERIODO'].'-'.$HoleritePeriodoRM['MESCOMP'].'-'.$HoleritePeriodoRM['ANOCOMP']){
                                                            $checkedPeriodo = "checked";
                                                            break;
                                                        }
                                                    }
                                                }
                                            ?>
                                            <tr>
                                                <td class="text-center"><input data-periodo type="checkbox" <?= $checkedPeriodo; ?> value="<?= $HoleritePeriodoRM['NROPERIODO'].'-'.$HoleritePeriodoRM['MESCOMP'].'-'.$HoleritePeriodoRM['ANOCOMP']; ?>"></td>
                                                <td class="text-center"><?= $HoleritePeriodoRM['NROPERIODO']; ?></td>
                                                <td class="text-center"><?= $HoleritePeriodoRM['MESCOMP'].'/'.$HoleritePeriodoRM['ANOCOMP']; ?></td>
                                                <td class="text-left"><?= descricaoHolerite($HoleritePeriodoRM['NROPERIODO']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

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
    
    let periodos = [];

    $("[data-periodo]:checked").each(function(e){
        periodos.push($(this).val());
    });

    let dados = {
        "data_inicio": $("#data_inicio").val(),
        "data_fim": $("#data_fim").val(),
        "responsavel": $("#responsavel").val(),
        "periodos": periodos,
    }
    
    if(dados.data_inicio == ""){ exibeAlerta("error", "<b>Data de início</b> não informada."); return false; }
    if(dados.data_fim == ""){ exibeAlerta("error", "<b>Data fim</b> não informada."); return false; }
    if(dados.responsavel == ""){ exibeAlerta("error", "<b>Responsável pelo informe de rendimento</b> não informado."); return false; }
    //if(dados.periodos.length <= 0){ exibeAlerta("error", "Nenhum período para liberação do holerite selecionado."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('consultar/holerite/action/holerite_configuracao'); ?>",
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