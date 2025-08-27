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
                            <a href="<?= base_url('premio/requisicao') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> Voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="form-group row">
                        <label for="dt_requisicao" class="col-sm-2 col-form-label text-right">Data da Requisição:</label>
                        <div class="col-sm-2">
                            <input class="form-control datepicker m_data" type="date" name="dt_requisicao" id="dt_requisicao" value="<?php echo date("Y-m-d");?>" disabled>
                        </div>
                        <div class="col-8">
                            &nbsp;
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="chapa_requisitor" class="col-sm-2 col-form-label text-right">Requisitante:</label>
                        <div class="col-4">
                            <?php if($isAdmin) { ?>
                                <select class="select2 text-left form-control form-control-sm mb-1" name="chapa_requisitor" id="chapa_requisitor" onchange="buscaGerente()">
                                    <option value="">...</option>
                                    <?php foreach($resFunc as $key => $Func): ?>
                                        <option value="<?= $Func['CHAPA']; ?>"><?= $Func['NOME'].' - '.$Func['CHAPA']; ?></option>
                                        <?php //unset($resFunc[$key], $key, $Func); ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php } else { ?>
                                <input class="form-control" type="hidden" value="<?= $func_chapa ?>" name="chapa_requisitor" id="chapa_requisitor">
                                <input class="form-control" type="text" value="<?= $func_chapa ?> - <?= $func_nome ?>" name="chapa_requisitor" id="chapa_requisitor" disabled>
                            <?php } ?>
                        </div>
                        <label for="gerente" class="col-sm-2 col-form-label text-right">Gerente:</label>
                        <div class="col-4">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="gerente" id="gerente">
                                <option value="">...</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="id_acesso" class="col-sm-2 col-form-label text-right">Prêmio:</label>
                        <div class="col-sm-4">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="id_acesso" id="id_acesso">
                                <option value="">...</option>
                                <?php foreach($resAcessos as $key => $Acesso): ?>
                                    <option value="<?= $Acesso['id_acesso']; ?>"><?= $Acesso['nome_premio'].' - '.$Acesso['per_ponto_br']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label for="tipo" class="col-sm-2 col-form-label text-right">Tipo:</label>
                        <div class="col-sm-4">
                            <select class="form-control form-control-sm mb-1" name="tipo" id="tipo" require>
                                <option value="M">Mensal</option>
                                <option value="C">Complementar</option>
                            </select>
                        </div>
                        <?php if($resAcessos == []) { ?>
                            <div class="col-sm-2"> </div>
                            <div class="col-sm-10 text-danger"><i>Não foram encontrados prêmios disponíveis.</i></div>
                        <?php } ?>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar Requisição</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
$(document).ready(function() {
  buscaGerente();
});

const salvaDados = () => {
    
    //nome_de_gestor
    let dados = {
        "dt_requisicao": $("#dt_requisicao").val(),
        "chapa_requisitor": $("#chapa_requisitor").val(),
        "chapa_gerente": $("#gerente").val(),
        "tipo": $("#tipo").val(),
        "id_acesso": $("#id_acesso").val(),
    }

    if(dados.id_acesso == ""){ exibeAlerta("error", "<b>Prêmio</b> não informado."); return false; }
    if(dados.chapa_requisitor == ""){ exibeAlerta("error", "<b>Chapa do criador</b> não informada."); return false; }
    if(dados.chapa_requisitor == ""){ exibeAlerta("error", "<b>Gerente</b> não localizado."); return false; }
    if(dados.tipo == ""){ exibeAlerta("error", "<b>Tipo</b> não informado."); return false; }
    
    openLoading();

    $.ajax({
        url: "<?= base_url('premio/requisicao/action/cadastrar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/requisicao/editar'); ?>/'+response.cod);
            }
        },
    });
    
}
const buscaGerente = () => {
    
    //nome_de_gestor
    let dados = {
        "chapa_requisitor": $("#chapa_requisitor").val()
    }

    if(dados.chapa_requisitor == ""){ exibeAlerta("error", "<b>Chapa do requisitante</b> não informada."); return false; }
    
    openLoading();

    $.ajax({
        url: "<?= base_url('premio/requisicao/action/busca_gerente'); ?>",
        type:'POST',
        data:dados,
        success:function(result){
          openLoading(true);

          try {
                    var response = JSON.parse(result);
                    $("#gerente").html('').trigger('change');
                    
                    for (var x = 0; x < response.length; x++) {
                        $("#gerente").append('<option value="' + response[x].CHAPA + '">' + response[x].NOME + ' - ' + response[x].CHAPA + '</option>');
                    }

                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }
        },
    });
    
}

</script>

<?php loadPlugin(['select2']); ?>