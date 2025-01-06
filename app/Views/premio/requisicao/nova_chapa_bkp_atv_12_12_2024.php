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
                            <a href="<?= base_url('premio/requisicao/editar/') ?>/<?= $id_requisicao; ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> Voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-header mt-0">
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Requisitante:</h6>
                        <h5 class="col-10 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['nome_requisitor'].' ('.$resRequisicao[0]['chapa_requisitor'].')'; ?></h5>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Nome do Prêmio:</h6>
                        <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['nome_premio']; ?></h5>
                        <h6 class="col-2 text-right mb-1 mt-1">Data Requisição:</h6>
                        <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['dt_requisicao_br']; ?></h5>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Período de Ponto:</h6>
                        <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['per_ponto_br']; ?></h5>
                        <h6 class="col-2 text-right mb-1 mt-1">Tipo Requisição:</h6>
                        <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['tipo_requisicao']; ?></h5>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="chapa_colaborador" class="col-sm-2 col-form-label text-right">Colaborador:</label>
                        <div class="col-sm-10">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="chapa_colaborador" id="chapa_colaborador">
                                <option value="">...</option>
                                <?php foreach($resFunc as $key => $Func): ?>
                                    <option value="<?= $Func['CHAPA']; ?>"><?= $Func['NOME'].' - '.$Func['CHAPA']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="chapa_coordenador" class="col-sm-2 col-form-label text-right">Aprovador:</label>
                        <div class="col-sm-10">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="chapa_coordenador" id="chapa_coordenador">
                                <option value="">...</option>
                                <?php foreach($resCoordenadores as $key => $Func): ?>
                                    <option value="<?= $Func['chapa_coordenador']; ?>"><?= $Func['nome_coordenador'].' - '.$Func['chapa_coordenador']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="obs" class="col-sm-2 col-form-label text-right">Observação:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" maxlength="1000" name="obs" id="obs" cols="30" rows="3" class="form-control-sm mb-1" require></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="target" class="col-sm-2 col-form-label text-right">% Target:</label>
                        <div class="col-sm-4">
                            <?php // INPUT INICIAL <input data-money type="text" id="target" name="target" class="form-control" placeholder="0,00" value="" require>?>
                            <select class="select2 text-right form-control form-control-sm mb-1" name="target" id="target">
                            <option value="">...</option>
                                <?php foreach($resTargets as $key => $Target): ?>
                                    <option value="<?= $Target['target_premissa']; ?>"><?= $Target['target_br']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label for="realizado" class="col-sm-2 col-form-label text-right">% Realizado:</label>
                        <div class="col-sm-4">
                            <input data-money type="text" id="realizado" name="realizado" class="form-control-sm mb-1" placeholder="0,00" value="" require>
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
    if($("#target").val() == ""){ exibeAlerta("error", "<b>% Target</b> não informado."); return false; }
    if($("#realizado").val() == ""){ exibeAlerta("error", "<b>% Realizado</b> não informado."); return false; }
    
    let dados = {
        "target": parseFloat($("#target").val()),
        "realizado": parseFloat($("#realizado").val().replaceAll(".", "").replace(",", ".")),
        "chapa_colaborador": $("#chapa_colaborador").val(),
        "chapa_coordenador": $("#chapa_coordenador").val(),
        "obs": $("#obs").val(),
        "id_requisicao": <?= $id_requisicao ?>,
        "tipo_req": "<?= $tipo_req ?>"
    }

    if(dados.chapa_colaborador == ""){ exibeAlerta("error", "<b>Colaborador</b> não informado."); return false; }
    if(dados.chapa_coordenador == ""){ exibeAlerta("error", "<b>Aprovador</b> não informado."); return false; }
    if(dados.obs == ""){ exibeAlerta("error", "<b>Observação</b> não informada."); return false; }
    if(dados.target > 999.99){ exibeAlerta("error", "<b>% Target</b> não pode ser maior que 999,99."); return false; }
    if(dados.target <= 0){ exibeAlerta("error", "<b>% Target</b> não pode ser menor ou igual a 0."); return false; }
    if(dados.realizado > 999.99){ exibeAlerta("error", "<b>% Realizado</b> não pode ser maior que 999,99."); return false; }
    if(dados.realizado <= 0){ exibeAlerta("error", "<b>% Realizado</b> não pode ser menor ou igual a 0."); return false; }
    
    openLoading();

    $.ajax({
        url: "<?= base_url('premio/requisicao/action/nova_chapa'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/requisicao/editar/'); ?>/<?= $id_requisicao; ?>');
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