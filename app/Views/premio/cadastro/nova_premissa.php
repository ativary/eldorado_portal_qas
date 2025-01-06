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
                                <a href="<?= base_url('premio/cadastro/premissas/'.$id_premio) ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="premissa_codfilial" class="col-sm-2 col-form-label text-right">Filial:</label>
                        <div class="col-sm-4">
                            <select class="select2 form-control form-control-sm mb-1" name="premissa_codfilial" id="premissa_codfilial">
                                <option value="">...</option>
                                <?php foreach($resFilial as $key => $Filial): ?>
                                    <option value="<?= $Filial['CODFILIAL']; ?>"><?= $Filial['NOMEFANTASIA'].' - '.$Filial['CODFILIAL']; ?></option>
                                    <?php unset($resFilial[$key], $key, $Filial); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label for="premissa_codcusto" class="col-sm-2 col-form-label text-right">Centro de Custo:</label>
                        <div class="col-sm-4">
                            <select class="select2 form-control form-control-sm mb-1" name="premissa_codcusto" id="premissa_codcusto">
                                <option value="">...</option>
                                <?php foreach($resCentroCusto as $key => $CentroCusto): ?>
                                    <option value="<?= $CentroCusto['CODCCUSTO']; ?>"><?= $CentroCusto['NOME'].' - '.$CentroCusto['CODCCUSTO']; ?></option>
                                    <?php unset($resCentroCusto[$key], $key, $CentroCusto); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="premissa_codfuncao" class="col-sm-2 col-form-label text-right">Função:</label>
                        <div class="col-sm-4">
                            <select class="select2 form-control form-control-sm mb-1" name="premissa_codfuncao" id="premissa_codfuncao">
                                <option value="">...</option>
                                <?php foreach($resFuncao as $key => $Funcao): ?>
                                    <option value="<?= $Funcao['CODIGO']; ?>"><?= $Funcao['NOME'].' - '.$Funcao['CODIGO']; ?></option>
                                    <?php unset($resFuncao[$key], $key, $Funcao); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label for="premissa_tipo_target" class="col-sm-2 col-form-label text-right">Tipo Target:</label>
                        <div class="col-sm-4">
                            <select class="select2 form-control form-control-sm mb-1" name="premissa_tipo_target" id="premissa_tipo_target" require>
                                <option value="F">Fixo</option>
                                <option value="M">Múltiplo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="premissa_target" class="col-sm-2 col-form-label text-right">% Target:</label>
                        <div class="col-sm-4">
                            <input data-money type="text" id="premissa_target" name="premissa_target" class="form-control" placeholder="0,00" value="0" require>
                        </div>
                        <label for="premissa_grupo" class="col-sm-2 col-form-label text-right">Grupo:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="" maxlength="100" name="premissa_grupo" id="premissa_grupo" require>
                        </div>
            
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success bteldorado_1" id="btnsave" onclick="return salvaDados()"><i class="fas fa-save"></i> Cadastrar Premissa</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
const salvaDados = () => {
    
    if($("#premissa_target").val() == ""){ exibeAlerta("error", "<b>% Target</b> não informado."); return false; }
    
    let dados = {
        "premissa_codfilial": $("#premissa_codfilial").val(),
        "premissa_codcusto": $("#premissa_codcusto").val(),
        "premissa_codfuncao": $("#premissa_codfuncao").val(),
        "premissa_grupo": $("#premissa_grupo").val(),
        "premissa_target": parseFloat($("#premissa_target").val().replaceAll(".", "").replace(",", ".")),
        "premissa_tipo_target": $("#premissa_tipo_target").val(),
        "id_premio": <?= $id_premio ?>,
    }

    if(dados.premissa_codfilial == ""){ exibeAlerta("error", "<b>Filial</b> não informada."); return false; }
    if(dados.premissa_codcusto == ""){ exibeAlerta("error", "<b>Centro de Custo</b> não informado."); return false; }
    if(dados.premissa_codfuncao == ""){ exibeAlerta("error", "<b>Função</b> não informada."); return false; }
    if(dados.premissa_grupo == ""){ exibeAlerta("error", "<b>Grupo</b> não informado."); return false; }
    if(dados.premissa_tipo_target == ""){ exibeAlerta("error", "<b>Tipo do Target</b> não informado."); return false; }
    if(dados.premissa_target > 999.99){ exibeAlerta("error", "<b>% Target</b> não pode ser maior que 999,99."); return false; }
    if(dados.premissa_target < 0){ exibeAlerta("error", "<b>% Target</b> não pode ser menor que 0."); return false; }
    
    $.ajax({
        url: "<?= base_url('premio/cadastro/action_premissas/cadastrar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 4);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/cadastro/premissas/'.$id_premio) ?>');
            }

        },
    });
}
</script>
<script>
    $(document).ready(function(e){
        $("[data-money]").maskMoney({
            prefix: '',
            allowNegative: false,
            allowZero: true,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });
    });
</script>

<?php loadPlugin(['select2','maskmoney']); ?>