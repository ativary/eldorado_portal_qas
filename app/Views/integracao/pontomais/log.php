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

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#colaborador" role="tab"><i class="fas fa-user"></i> - Log Colaborador</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#horario" role="tab"><i class="fas fa-user-clock"></i> - Log Horário</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#afd" role="tab"><i class="mdi mdi-sync-alert"></i> - Log AFD</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active p-3" id="colaborador" role="tabpanel">
                            
                            <table id="datatable" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Chapa</th>
                                        <th>Nome</th>
                                        <th class="text-center">Centro de Custo</th>
                                        <th class="text-center">Seção</th>
                                        <th class="text-center">Função</th>
                                        <th class="text-center">Data Admissão</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($resColoborador): ?>
                                        <?php foreach($resColoborador as $key => $Colaborador): ?>
                                            <tr>
                                                <td class="text-center"><?= $Colaborador['chapa']; ?></td>
                                                <td><?= $Colaborador['nome']; ?></td>
                                                <td class="text-center"><?= $Colaborador['cc']; ?></td>
                                                <td class="text-center"><?= $Colaborador['codsecao']; ?></td>
                                                <td class="text-center"><?= $Colaborador['codfuncao']; ?></td>
                                                <td class="text-center"><?= dtBr($Colaborador['dataadmissao']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane p-3" id="horario" role="tabpanel">
                            
                            <table id="datatable" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Chapa</th>
                                        <th>Nome</th>
                                        <th class="text-center">Data Mudança</th>
                                        <th class="text-center">Horário</th>
                                        <th class="text-center">Indice Calculo</th>
                                        <th class="text-center">Ação</th>
                                        <th class="text-center">Data Log</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($resHorario): ?>
                                        <?php foreach($resHorario as $key => $Horario): ?>
                                            <tr>
                                                <td class="text-center"><?= $Horario['chapa']; ?></td>
                                                <td><?= $Horario['nome']; ?></td>
                                                <td class="text-center"><?= dtBr($Horario['dtmudanca']); ?></td>
                                                <td class="text-center"><?= $Horario['deschorario']; ?></td>
                                                <td class="text-center"><?= $Horario['indice_calculo']; ?></td>
                                                <td class="text-center"><?= $Horario['tipo']; ?></td>
                                                <td class="text-center"><?= dtBr($Horario['dtcad']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane p-3" id="afd" role="tabpanel">

                            <table id="datatable" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">Data</th>
                                        <th>UUID</th>
                                        <th class="text-center">Processo</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center" width="50"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($resAfd): ?>
                                        <?php foreach($resAfd as $key => $Afd): ?>
                                            <tr>
                                                <td class="text-center"><?= date('d/m/Y H:i:s', strtotime($Afd['created_at'])); ?></td>
                                                <td><?= $Afd['uuid']; ?></td>
                                                <td class="text-center"><?= $Afd['process']; ?></td>
                                                <td class="text-center"><?= ($Afd['status'] == 0) ? '<span class="badge badge-success">Sucesso</span>' : '<span class="badge badge-danger">Erro</span>'; ?></td>
                                                <td class="text-center"><button onclick="abreLogDetalhes('<?= $Afd['uuid']; ?>')" class="btn btn-soft-primary waves-effect waves-light btn-xxs" type="button"><i class="fas fa-search"></i></button></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>


                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<style>
    .tab-pane {
        border-left: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
    }
</style>
<script>
const abreLogDetalhes = (uuid) => {
    
    openLoading();
    $(".modal_logafd tbody").html('');

    $.ajax({
        url: "<?= base_url('integracao/pontomais/action/detalhes_logafd') ?>",
        type: 'POST',
        data: {
            'uuid': uuid
        },
        success: function(result) {

            openLoading(true);
            $(".modal_logafd").modal('show');

            try {
                var response = JSON.parse(result);
                for(var x = 0; x < response.length; x++){

                    switch(response[x].type){
                        case 1 : var status = '<span class="badge badge-success">Sucesso</span>'; break;
                        case 2 : var status = '<span class="badge badge-danger">Erro</span>'; break;
                        case 3 : var status = '<span class="badge badge-warning">Aviso</span>'; break;
                    }


                    var message = (response[x].message.length > 300) ? response[x].message.substring(0, 300)+'...' : response[x].message;

                    $(".modal_logafd tbody").append(`<tr>
                        <td class="align-top">${response[x].created_at}</td>
                        <td class="align-top">${status}</td>
                        <td class="align-top" style="word-break: break-word;">${message}<div style="opacity: 0;  position: absolute;  z-index: -9999;  pointer-events: none;" aria-hidden="true" id="copy_${x}">${response[x].message}</div></td>
                        <td class="align-top"><button class="btn btn-soft-primary waves-effect waves-light btn-xxs" type="button" data-clipboard-action="copy" data-clipboard-target="#copy_${x}"><i class="far fa-copy"></i></button></td>
                    </tr>`);
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });
}
</script>
<!-- modal modal_logafd -->
<div class="modal modal_logafd" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_logafd" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-full">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title mt-0 text-white"><i class="fas fa-info-circle"></i> Detalhes do Log de Integração</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <table class="table table-sm table-bordered table-hover" width="100%" style="table-layout: fixed;">
                            <thead>
                                <tr>
                                    <th width="150">Data</th>
                                    <th width="60">Status</th>
                                    <th style="white-space: pre-wrap !important;">Log</th>
                                    <th width="40"></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_logafd -->
<?php loadPlugin(array('clipboard')); ?>
<script>var clipboard = new ClipboardJS('[data-clipboard-action]');</script>