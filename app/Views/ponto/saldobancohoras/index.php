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
                <form action="" method="post" name="form_filtro" id="form_filtro">
                        <input type="hidden" name="action" id="action">

                        <div class="row">
                            <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Período:</label>
                            <div class="col-sm-10">
                                <select name="periodo" id="periodo" class="form-control form-control-sm" onchange="return carregaColaboradores()">
                                <option value="">:: Selecione um período ::</option>
                                    <?php if($resPeriodo): ?>
                                        <?php foreach($resPeriodo as $key => $Periodo): ?>
                                            <option data-inicioperiodo="<?= dtBr($Periodo['INICIOMENSAL']) ?>" value="<?= $Periodo['FIMMENSAL'] ?>" <?php if($periodo == $Periodo['FIMMENSAL']){echo ' selected ';} ?>><?= dtBr($Periodo['INICIOMENSAL']). ' à '.dtBr($Periodo['FIMMENSAL']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <?php if($resSecaoGestor): ?>
                        <div class="row">
                            <label for="secao" class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="secao" id="secao" onchange="return carregaColaboradores()">
                                    <option value="">-- Todas --</option>
                                    <?php if($resSecaoGestor): ?>
                                        <?php foreach($resSecaoGestor as $key => $SecaoGestor): ?>
                                            <option value="<?= $SecaoGestor['CODIGO']; ?>" <?php if($codsecao == $SecaoGestor['CODIGO']){echo ' selected ';} ?>><?= $SecaoGestor['CODIGO'].' - '.$SecaoGestor['DESCRICAO']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="chapa" class="col-sm-2 col-form-label text-right text-left-sm">Colaborador:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="chapa" id="chapa">
                                    <option value="">-- selecione um colaborador --</option>
                                    <?php if($resFuncionarioSecao && $action == 'filtro'): ?>
                                        <?php foreach($resFuncionarioSecao as $key => $FuncionarioSecao): ?>
                                            <option value="<?= $FuncionarioSecao['CHAPA']; ?>" <?php if($chapa == $FuncionarioSecao['CHAPA']){echo ' selected ';} ?>><?= $FuncionarioSecao['NOME'].' - '.$FuncionarioSecao['CHAPA']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="chapa" value="<?= util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null; ?>">
                        <?php endif; ?>
                        
                    </form>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return executar()"><i class="fas fa-search"></i> Consultar</button>
                </div>
            </div>

            <?php if($action == 'filtro'): ?>
                <div class="card">

                    <div class="card-header mt-0 noprint">
                        <div class="row">
                            <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <?= exibeMensagem(true); ?>

                        <div class="row mb-2">
                            <div class="col-12 text-right">
                                <form action="<?= base_url('ponto/saldobancohoras/excel') ?>" method="post" name="form_excel" target="_blank">
                                    <button class="btn btn-success btn-xxs" type="submit" onclick="exportaExcel()"><i class="fas fa-file-excel"></i> Exportar Excel</button>
                                    <input type="hidden" name="codsecao" value="<?= $codsecao; ?>">
                                    <input type="hidden" name="chapa" value="<?= $chapa; ?>">
                                    <input type="hidden" name="periodo" value="<?= $periodo; ?>">
                                </form>
                            </div>
                        </div>

                        <table id="datatable" class="table table-sm table-bordered n-mobile-cell">
                            <thead>
                                <tr>
                                    <th class="text-center">CHAPA</th>
                                    <th>NOME</th>
                                    <th class="text-center">SALDO POSITIVO</th>
                                    <th class="text-center">SALDO NEGATIVO</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $total_positovo = 0; ?>
                            <?php $total_negativo = 0; ?>
                            <?php if($resSaldoBancoHoras): ?>
                                <?php foreach($resSaldoBancoHoras as $key => $SaldoBancoHoras): ?>
                                    <tr>
                                        <td width="90" class="text-center"><?= $SaldoBancoHoras['CHAPA']; ?></td>
                                        <td><?= $SaldoBancoHoras['NOME']; ?></td>
                                        <td width="90" class="text-right text-success"><b><?= ($SaldoBancoHoras['SALDO'] >= 0) ? m2h($SaldoBancoHoras['SALDO']) : ''; ?></b></td>
                                        <td width="90" class="text-right text-danger"><b><?= ($SaldoBancoHoras['SALDO'] < 0) ? m2h($SaldoBancoHoras['SALDO']) : ''; ?></b></td>
                                    </tr>
                                    <?php $total_positovo += (($SaldoBancoHoras['SALDO'] >= 0) ? $SaldoBancoHoras['SALDO'] : 0); ?>
                                    <?php $total_negativo += (($SaldoBancoHoras['SALDO'] < 0) ? $SaldoBancoHoras['SALDO'] : 0); ?>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-right" bgcolor="#f0f0f0"><b>Total:</b></td>
                                    <td class="text-right text-success" bgcolor="#f0f0f0"><b><?= m2h($total_positovo); ?></b></td>
                                    <td class="text-right text-danger" bgcolor="#f0f0f0"><b><?= m2h($total_negativo); ?></b></td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>

                        <?php if($resSaldoBancoHoras): ?>
                            <?php foreach($resSaldoBancoHoras as $key => $SaldoBancoHoras): ?>
                                <div class="card mb-1 ml-1 mr-1 y-mobile-block d-none">
                                    <div class="card-body bg-secondary p-1">
                                        <div class="row">
                                            <div class="col-9">
                                                <small class="m-0 p-0 text-primary"><b>Funcionário</b></small>
                                                <h5 class="mt-0"><?= $SaldoBancoHoras['NOME']; ?><br><small><?= $SaldoBancoHoras['CHAPA']; ?></small></h5>
                                            </div>
                                            <div class="col-3 text-right"><small class="m-0 p-0 text-primary"><b>Saldo</b></small><h4 class="mt-0 <?= ($SaldoBancoHoras['SALDO'] < 0) ? ' text-danger ' : ' text-success '; ?>"><?= m2h($SaldoBancoHoras['SALDO']); ?></h4></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="card mb-1 ml-1 mr-1 y-mobile-block d-none">
                            <div class="card-body btn-soft-dark p-1">
                                <div class="row">
                                    <div class="col-4">
                                        <h5 class="text-right">TOTAL:</h5>
                                    </div>
                                    <div class="col-4 text-right"><h4 class="text-success"><small class="m-0 p-0 text-primary"><b>Total Positivo</b></small><br><?= m2h($total_positovo); ?></h4></div>
                                    <div class="col-4 text-right"><h4 class="text-danger"><small class="m-0 p-0 text-primary"><b>Total Negativo</b></small><br><?= m2h($total_negativo); ?></h4></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endif; ?>
        </div><!-- end main -->


    </div>
</div><!-- container -->
<script>
const executar = () => {
    openLoading();

    $("#action").val('filtro');
    $("#form_filtro").submit();

}
const carregaFuncionariosSecao = (codSecao) => {

    openLoading();
    
    $("#chapa").html('<option value="">-- selecione um funcionário --</option>').trigger('change');
    if(codSecao == ''){
        openLoading(true);
        return false;
    }

    $.ajax({
        url: "<?= base_url('ponto/saldobancohoras/action/listar_funcionarios_secao') ?>",
        type: 'POST',
        data: {
            'codsecao': codSecao
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);
                $("#chapa").html('<option value="">-- Todos ('+response.length+') --</option>').trigger('change');

                for(var x=0; x < response.length; x++){
                    $("#chapa").append('<option value="'+response[x].CHAPA+'">'+response[x].NOME+' - '+response[x].CHAPA+'</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const exportaExcel = () => {

}
const carregaColaboradores = () => {

openLoading();

var selPeriodo = $("#periodo").val();
if(selPeriodo == ''){
    exibeAlerta('warning', 'Período não selecionado.');
    return;
}


var periodo = $("select option:selected").attr('data-inicioperiodo');

$("#chapa").html('<option value="">-- selecione um colaborador --</option>').trigger('change');

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
            $("#chapa").html('<option value="">-- Todos (' + response.length + ') --</option>').trigger('change');

            for (var x = 0; x < response.length; x++) {
                $("#chapa").append('<option value="' + response[x].CHAPA + '">' + response[x].NOME + ' - ' + response[x].CHAPA + '</option>');
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