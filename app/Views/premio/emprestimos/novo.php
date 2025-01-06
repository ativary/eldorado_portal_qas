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
                            <a href="<?= base_url('premio/emprestimos') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> Voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="form-group row">
                        <label for="dt_solicitacao" class="col-sm-2 col-form-label text-right">Data da Solicitação:</label>
                        <div class="col-sm-4">
                            <input class="form-control datepicker m_data" type="date" name="dt_solicitacao" id="dt_solicitacao" value="<?php echo date("Y-m-d");?>" disabled>
                        </div>
                        <label for="de_chapa" class="col-sm-2 col-form-label text-right">De Gestor:</label>
                        <div class="col-4">
                            <?php if($isAdmin) { ?>
                                <select class="select2 text-left form-control form-control-sm mb-1" name="de_chapa" id="de_chapa">
                                    <option value="">...</option>
                                    <?php foreach($resFunc as $key => $Func): ?>
                                        <option value="<?= $Func['CHAPA']; ?>"><?= $Func['NOME'].' - '.$Func['CHAPA']; ?></option>
                                        <?php //unset($resFunc[$key], $key, $Func); ?>
                                    <?php endforeach; ?>
                                </select>
                            <?php } else { ?>
                                <input class="form-control" type="hidden" value="<?= $func_chapa ?>" name="de_chapa" id="de_chapa">
                                <input class="form-control" type="text" value="<?= $func_chapa ?> - <?= $func_nome ?>" name="de_chapa_nome" id="de_chapa_nome" disabled>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="para_chapa" class="col-sm-2 col-form-label text-right">Para Gestor:</label>
                        <div class="col-4">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="para_chapa" id="para_chapa">
                                <option value="">...</option>
                                <?php foreach($resGestores as $key => $Func): ?>
                                    <option value="<?= $Func['CHAPA']; ?>"><?= $Func['NOME'].' - '.$Func['CHAPA']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <label for="chapa_colaborador" class="col-sm-2 col-form-label text-right">Colaborador:</label>
                        <div class="col-4">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="chapa_colaborador" id="chapa_colaborador">
                                <option value="">...</option>
                                <?php foreach($resColab as $key => $Func): ?>
                                    <option value="<?= $Func['CHAPA']; ?>"><?= $Func['NOME'].' - '.$Func['CHAPA']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="id_acesso" class="col-sm-2 col-form-label text-right">Prêmio:</label>
                        <div class="col-sm-10">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="id_acesso" id="id_acesso">
                                <option value="">...</option>
                                <?php foreach($resAcessos as $key => $Acesso): ?>
                                    <option value="<?= $Acesso['id_acesso']; ?>"><?= $Acesso['nome_premio'].' - '.$Acesso['per_ponto_br']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php if($resAcessos == []) { ?>
                            <div class="col-sm-2"> </div>
                            <div class="col-sm-10 text-danger"><i>Não foram encontrados prêmios disponíveis para a data acima.</i></div>
                        <?php } ?>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Gravar e Enviar Solicitação</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
const salvaDados = () => {
    
    //nome_de_gestor
    let dados = {
        "dt_solicitacao": $("#dt_solicitacao").val(),
        "de_chapa": $("#de_chapa").val(),
        "para_chapa": $("#para_chapa").val(),
        "chapa_colaborador": $("#chapa_colaborador").val(),
        "id_acesso": $("#id_acesso").val(),
    }

    if(dados.id_acesso == ""){ exibeAlerta("error", "<b>Prêmio</b> não informado."); return false; }
    if(dados.de_chapa == ""){ exibeAlerta("error", "<b>Gestor origem</b> não informado."); return false; }
    if(dados.para_chapa == ""){ exibeAlerta("error", "<b>Gestor destino</b> não informado."); return false; }
    if(dados.chapa_colaborador == ""){ exibeAlerta("error", "<b>Colaborador</b> não informado."); return false; }
    if(dados.de_chapa == dados.para_chapa){ exibeAlerta("error", "<b>Gestor de origem e de destino</b> não podem ser o mesmo."); return false; }
    if(dados.de_chapa == dados.chapa_colaborador){ exibeAlerta("error", "<b>Gestor de origem e Colaborador</b> não podem ser o mesmo."); return false; }
    if(dados.para_chapa == dados.chapa_colaborador){ exibeAlerta("error", "<b>Gestor de destino e Colaborador</b> não podem ser o mesmo."); return false; }
    
    openLoading();

    $.ajax({
        url: "<?= base_url('premio/emprestimos/action/cadastrar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/emprestimos'); ?>');
            }

        },
    });
    
}
</script>

<?php loadPlugin(['select2']); ?>