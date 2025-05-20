<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card noprint">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Filtro</h4>
                    </div>
                </div>
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <form action="" method="post" name="form_filtro" id="form_filtro" target="_blank">
                        <input type="hidden" name="action" id="action">

                        <div class="row">
                            <label for="periodo" class="col-sm-2 col-form-label text-right text-left-sm">Período:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo" <?php if($isGestorOrLider): ?>onchange="carregaColaboradores()"<?php endif; ?>>
                                    <option value="">- selecione um período -</option>
                                    <?php if ($resPeriodo) : ?>
                                        <?php foreach($resPeriodo as $key => $Periodo): ?>
                                            <option value="<?= dtBr($Periodo['INICIOMENSAL']) . dtBr($Periodo['FIMMENSAL']); ?>" <?= ($periodo == dtBr($Periodo['INICIOMENSAL']) . dtBr($Periodo['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($Periodo['INICIOMENSAL']) . ' à ' . dtBr($Periodo['FIMMENSAL']); ?></option>                                        
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <?php if ($isGestorOrLider) : ?>
                        <div class="row">
                            <label for="secao" class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="secao" id="secao" onchange="carregaColaboradores()">
                                    <option value="">-- Todas --</option>
                                    <?php if($resSecaoGestor): ?>
                                        <?php foreach($resSecaoGestor as $key => $SecaoGestor): ?>
                                            <option value="<?= $SecaoGestor['CODIGO']; ?>"><?= $SecaoGestor['CODIGO'].' - '.$SecaoGestor['DESCRICAO']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                            <div class="row">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Colaborador:</label>
                                <div class="col-sm-10">
                                    <select class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">- selecione o colaborador -</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>"><?= $DadosFunc['CHAPA'] . ' - ' . $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </form>
                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-danger btn-xxs bteldorado_2" onclick="executar()">Gerar PDF <i class="mdi mdi-file-pdf"></i></button>
                </div>
            </div>
        </div><!-- end main -->


    </div>
</div><!-- container -->
<script>
const executar = () => {

    var dados = {
        'periodo' : $("[name=periodo]").val(),
    }

    if(dados.periodo == ''){exibeAlerta('error', '<b>Período</b> não informado'); return false;}
    
    $("#form_filtro").submit();

}
const procurarFuncionario = () => {
    
    let keyword = $('[name=keyword]').val();
    if(keyword.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }

    openLoading();
    
    $("select[name=chapa]").html('');

    $.ajax({
        url: base_url + '/relatorio/gerar/action/lista_funcionarios',
        type: 'POST',
        data: {
            'keyword'  : keyword
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);

                $("select[name=chapa]").append('<option value="">Selecione o Colaborador ('+response.length+')</option>');
                if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                for(var x = 0; x < response.length; x++){
                    $("select[name=chapa]").append('<option value="'+response[x].CHAPA+'">'+response[x].NOME + ' - ' +response[x].CHAPA+'</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const carregaFuncionariosSecao = (codSecao) => {

    openLoading();

    $("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');
    if (codSecao == '') {
        // openLoading(true);
        // return false;
    }

    $.ajax({
        url: "<?= base_url('ponto/espelho/action/listar_funcionarios_secao') ?>",
        type: 'POST',
        data: {
            'codsecao': codSecao
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);
                $("#funcionario").html('<option value="">-- Todos (' + response.length + ') --</option>').trigger('change');

                for (var x = 0; x < response.length; x++) {
                    $("#funcionario").append('<option value="' + response[x].CHAPA + '">' + response[x].NOME + ' - ' + response[x].CHAPA + '</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const carregaColaboradores = () => {

    openLoading();

    var periodo = $("#periodo").val();
    if(periodo == ''){
        exibeAlerta('warning', 'Período não selecionado.');
        return;
    }

    $("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');

    $.ajax({
        url: "<?= base_url('ponto/espelho/action/carrega_colaboradores') ?>",
        type: 'POST',
        data: {
            'codsecao'    : $("#secao").val() ?? null,
            'periodo'     : periodo
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);
                $("#funcionario").html('<option value="">-- Todos (' + response.length + ') --</option>').trigger('change');

                for (var x = 0; x < response.length; x++) {
                    $("#funcionario").append('<option value="' + response[x].CHAPA + '">' + response[x].NOME + ' - ' + response[x].CHAPA + '</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
</script>
<?php
loadPlugin(array('select2', 'mask', 'tooltips'))
?>