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
                                <a href="<?= base_url('premio/cadastro/acessos/'.$id_premio) ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                    <label for="dtini_req" class="col-sm-2 col-form-label text-right">Início das Requisições:</label>
                        <div class="col-sm-4">
                            <input class="form-control datepicker m_data" type="date" name="dtini_req" id="dtini_req" value="<?= date('Y-m-d', strtotime($resAcesso[0]['dtini_req'])) ?>" >
                        </div>
                        <label for="dtfim_req" class="col-sm-2 col-form-label text-right">Final das Requisições:</label>
                        <div class="col-sm-4">
                            <input class="form-control datepicker m_data" type="date" name="dtfim_req" id="dtfim_req" value="<?= date('Y-m-d', strtotime($resAcesso[0]['dtfim_req'])) ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="dtper_ponto" class="col-sm-2 col-form-label text-right">Período de Ponto:</label>
                        <div class="col-sm-4">
                            <select class="select2 form-control form-control-sm mb-1" name="dtper_ponto" id="dtper_ponto">
                                <option value="">...</option>
                                <?php foreach($resPonto as $key => $Ponto): ?>
                                    <option value="<?= $Ponto['PERIODO_SQL']; ?>" <?= ($resAcesso[0]['per_ponto_sql'] == $Ponto['PERIODO_SQL']) ? " selected " : ""; ?>><?= $Ponto['PERIODO_BR']; ?></option>
                                    <?php unset($resPonto[$key], $key, $Ponto); ?>
                                <?php endforeach; ?>
                            </select>
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
const salvaDados = () => {

    if($("#dtini_req").val() == ""){ exibeAlerta("error", "<b>Início das Requisições</b> não informado."); return false; }
    if($("#dtfim_req").val() == ""){ exibeAlerta("error", "<b>Fim das Requisições</b> não informado."); return false; }
    if($("#dtfim_req").val() < $("#dtini_req").val()){ exibeAlerta("error", "<b>Fim das Requisições</b> não pode ser menor que <b>Início das Requisições</b>."); return false; }
    if($("#dtper_ponto").val() == ""){ exibeAlerta("error", "<b>Período de Ponto</b> não informado."); return false; }
    
    // Validação das datas. Para que não existam Acessos em períodos de pontos já selecionados ou datas de requisições sobrepostas
    <?php if ($resAcessos) {?>
        <?php foreach($resAcessos as $key => $Acesso): ?>
            // Não testa existência no proprio ID do prêmio
            <?php if ($Acesso['id'] != $id) {?>
                if($("#dtini_req").val() >= "<?= $Acesso['dtini_req']; ?>" && $("#dtini_req").val() <= "<?= $Acesso['dtfim_req']; ?>"){ exibeAlerta("error", "Essa data de <b>Início das Requisições</b> já existe em outro período de acessos cadastrado."); return false; }
                if($("#dtfim_req").val() >= "<?= $Acesso['dtini_req']; ?>" && $("#dtfim_req").val() <= "<?= $Acesso['dtfim_req']; ?>"){ exibeAlerta("error", "Essa data de <b>Fim das Requisições</b> já existe em outro período de acessos cadastrado."); return false; }
                if($("#dtper_ponto").val().substring(0, 10) == "<?= $Acesso['dtini_ponto']; ?>"){ exibeAlerta("error", "Esse <b>Período de Ponto</b> já existe em outro período de acessos cadastrado."); return false; }
            <?php }?>
        <?php endforeach; ?>
    <?php }?>

    let dados = {
        "dtini_req": $("#dtini_req").val(),
        "dtfim_req": $("#dtfim_req").val(),
        "dtper_ponto": $("#dtper_ponto").val(),
        "id_premio": <?= $id_premio ?>,
        "id": <?= $id ?>,
    }

    $.ajax({
        url: "<?= base_url('premio/cadastro/action_acessos/editar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/cadastro/acessos/'.$id_premio) ?>');
            }

        },
    });
}
</script>

<?php loadPlugin(['select2']); ?>