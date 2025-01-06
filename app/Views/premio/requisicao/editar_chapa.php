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

                <div class="card-header mt-0">
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Colaborador:</h6>
                        <h5 class="col-10 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resReqChapa[0]['func_nome'].' ('.$resReqChapa[0]['func_chapa'].')'; ?> &nbsp&nbsp&nbsp&nbsp
                        <?php
                            switch($resReqChapa[0]['tipo']){
                                case 'P': echo '<span class="badge badge-primary">&nbspPADRÃO&nbsp</span>'; break;
                                case 'E': echo '<span class="badge badge-warning">EXCEÇÃO</span>'; break;
                                default: echo '';
                            }
                        ?>
                        </h5>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Função:</h6>
                        <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resReqChapa[0]['funcao']; ?></h5>
                        <h6 class="col-2 text-right mb-1 mt-1">Filial:</h6>
                        <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resReqChapa[0]['codfilial']; ?></h5>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Seção:</h6>
                        <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resReqChapa[0]['secao']; ?></h5>
                        <h6 class="col-2 text-right mb-1 mt-1">Centro de Custo:</h6>
                        <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resReqChapa[0]['codcusto']; ?></h5>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label for="chapa_coordenador" class="col-sm-2 col-form-label text-right">Aprovador:</label>
                        <div class="col-sm-10">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="chapa_coordenador" id="chapa_coordenador" <?= $em_analise; ?> value="<?= $resReqChapa[0]['coord_chapa'] ?>" <?php if($resReqChapa[0]['tipo'] == 'P') { echo " disabled ";}?>>
                                <?php foreach($resCoordenadores as $key => $Func): ?>
                                    <option value="<?= $Func['chapa_coordenador']; ?>" <?= ($resReqChapa[0]['coord_chapa'] == $Func['chapa_coordenador']) ? " selected " : ""; ?>><?= $Func['nome_coordenador'].' - '.$Func['chapa_coordenador']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="target" class="col-sm-2 col-form-label text-right">% Target:</label>
                        <div class="col-sm-4">
                            <select class="select2 text-right form-control form-control-sm mb-1" name="target" id="target" <?= $em_analise; ?> value="<?= $resReqChapa[0]['target'] ?>" <?php if($resReqChapa[0]['tipo'] == 'P') { echo " disabled ";}?>>
                                <?php foreach($resTargets as $key => $Target): ?>
                                    <option value="<?= $Target['target_premissa']; ?>" <?= ($resReqChapa[0]['target'] == $Target['target_premissa']) ? " selected " : ""; ?>><?= $Target['target_br']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label for="realizado" class="col-sm-2 col-form-label text-right">% Realizado:</label>
                        <div class="col-sm-4">
                            <input data-money type="text" id="realizado" name="realizado" class="form-control-sm mb-1" placeholder="0,00" value="" require  <?= $em_analise; ?>>
                        </div>
                    </div>
                </div>   
                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"  <?= $em_analise; ?>><i class="fas fa-check"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
    </div>
</div><!-- container -->

<script>
const salvaDados = () => {
    
    if($("#target").val() == ""){ exibeAlerta("error", "<b>% Target</b> não informado."); return false; }
    if($("#realizado").val() == ""){ exibeAlerta("error", "<b>% Realizado</b> não informado."); return false; }
    
    console.log($("#target").val());

    let dados = {
        "target": parseFloat($("#target").val()),
        "realizado": parseFloat($("#realizado").val().replaceAll(".", "").replace(",", ".")),
        "chapa_coordenador": $("#chapa_coordenador").val(),
        "id_req_chapa": <?= $id_req_chapa ?>,
    }

    if(dados.chapa_coordenador == ""){ exibeAlerta("error", "<b>Aprovador</b> não informado."); return false; }
    if(dados.target > 999.99){ exibeAlerta("error", "<b>% Target</b> não pode ser maior que 999,99."); return false; }
    if(dados.target <= 0){ exibeAlerta("error", "<b>% Target</b> não pode ser menor ou igual a 0."); return false; }
    <?php if($isAdmin) {?>
        if(dados.realizado > 999.99){ exibeAlerta("error", "<b>% Realizado</b> não pode ser maior que 999,99."); return false; }
    <?php } else { ?>
        if(dados.realizado > 100){ exibeAlerta("error", "<b>% Realizado</b> não pode ser maior que 100."); return false; }
    <?php } ?>
    if(dados.realizado < 0){ exibeAlerta("error", "<b>% Realizado</b> não pode ser menor que 0."); return false; }
    
    openLoading();
    console.log(dados.target);

    $.ajax({
        url: "<?= base_url('premio/requisicao/action/editar_chapa'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/requisicao/editar/'); ?>/<?= $id_requisicao; ?>');
            }
        },
    });
    
}
</script>
<script>
    $(document).ready(function(e){
        $("#realizado").val('<?= $resReqChapa[0]['realizado'] ?>'.replaceAll(".", ","));
        $("[data-money]").maskMoney({
            prefix: '',
            allowNegative: false,
            allowZero: true,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });
        $("#realizado").focus();
    });
</script>

<?php loadPlugin(['select2','maskmoney']); ?>