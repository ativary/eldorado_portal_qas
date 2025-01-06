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

                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <div class="form-group row">
                        <label for="base_salario" class="col-sm-3 col-form-label text-right">Utilizar o Salário Base do RM:</label>
                        <div class="col-sm-1">
                            <select class="form-control form-control-sm mb-1" onchange="checaDados()" name="base_salario" id="base_salario">
                                <option value="">...</option>
                                <option value="S" <?= ($resApuracao[0]['base_salario'] == 'S') ? " selected " : ""; ?>>Sim</option>
                                <option value="N" <?= ($resApuracao[0]['base_salario'] == 'N') ? " selected " : ""; ?>>Não</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="valor_base" class="col-sm-3 col-form-label text-right">Valor Base para o Prêmio:</label>
                        <div class="col-sm-2">
                            <input data-money type="text" onkeyup="checaDados()" id="valor_base" name="valor_base" class="form-control form-control-sm mb-1" placeholder="0,00" value="" require>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="codevento" class="col-sm-3 col-form-label text-right">Código do Evento [RM]:</label>
                        <div class="col-sm-2">
                        <input class="form-control form-control-sm mb-1" type="text" value="<?= $resApuracao[0]['codevento'] ?>" maxlength="4" name="codevento" id="codevento" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" require>
                        </div>
                    </div>
                                        
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success bteldorado_1" id="btnsave" onclick="return salvaDados()"><i class="fas fa-save"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>

const checaDados = (n) => {

    if($("#base_salario").val() == "S") {
        $("#valor_base").val("");
    }
}

const salvaDados = () => {
    let dados = {
        "base_salario": $("#base_salario").val(),
        "valor_base": $("#valor_base").val() == "" ? 0 : parseFloat($("#valor_base").val().replaceAll(".", "").replace(",", ".")),
        "codevento": $("#codevento").val(),
        "id_premio": <?= $id_premio ?>,
    }

    if(dados.valor_base <= 0 && dados.base_salario != "S"){ exibeAlerta("error", "<b>% Valor Base para o Prêmio</b> não pode ser menor ou igual a 0."); return false; }

    $.ajax({
        url: "<?= base_url('premio/cadastro/action/editar_apuracao'); ?>",
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
        $("[data-money]").maskMoney({
            prefix: '',
            allowNegative: false,
            allowZero: true,
            thousands: '.',
            decimal: ',',
            affixesStay: true
        }).maskMoney('mask');
        $("#valor_base").val('<?= $resApuracao[0]['valor_base'] ?>'.replaceAll(".", ","));
    });
</script>

<?php loadPlugin(['select2','maskmoney']); ?>