<?= menuConfigPonto('Justificativas'); ?>

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

                    <?= exibeMensagem(true); ?>

                    <ul class="nav nav-tabs" role="tablist">
                        <!-- <li class="nav-item">
                            <a data-tab="1" class="nav-link" data-toggle="tab" href="#falta" role="tab"><i class="mdi mdi-account-off"></i> - Falta</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="2" class="nav-link" data-toggle="tab" href="#atraso" role="tab"><i class="mdi mdi-account-alert"></i> - Atraso</a>
                        </li> -->
                        <li class="nav-item">
                            <a data-tab="3" class="nav-link active" data-toggle="tab" href="#hora_extra" role="tab"><i class="mdi mdi-account-star"></i> - H.Extra</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="4" class="nav-link" data-toggle="tab" href="#ajuste" role="tab"><i class="mdi mdi-account-edit"></i> - Ajuste</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="5" class="nav-link" data-toggle="tab" href="#reprova" role="tab"><i class="mdi mdi-account-minus"></i> - Reprova</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="6" class="nav-link" data-toggle="tab" href="#artigo61" role="tab"><i class="mdi mdi-account-alert"></i> - Artigo 61</a>
                        </li>
                        
                        <li class="nav-item">
                            <a data-tab="51" class="nav-link" data-toggle="tab" href="#ocorrencia" role="tab"><i class="mdi mdi-account-clock"></i> - Ocorrência</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane p-3" id="falta" role="tabpanel">

                            <div class="col-12 text-right mb-2">
                                <button type="button" onclick="return editar(0, '', 1)" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova justificativa de falta</button>
                            </div>

                            <table data-table id="datatable_falta" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Motivo</th>
                                        <th class="text-center" width="155">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($motivos['faltas']): ?>
                                        <?php foreach($motivos['faltas'] as $falta): ?>
                                            <tr>
                                                <td class="text-center"><?= $falta['id']; ?></td>
                                                <td><?= $falta['descricao']; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" aria-label="acao" role="group">
                                                        <a href="javascript:void(0);" onclick="return excluir(<?= $falta['id']; ?>, 1)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                        <button onclick="return editar(<?= $falta['id']; ?>, '<?= $falta['descricao']; ?>', 1)" type="button" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane p-3" id="atraso" role="tabpanel">

                            <div class="col-12 text-right mb-2">
                                <button type="button" onclick="return editar(0, '', 2)" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova justificativa de atraso</button>
                            </div>

                            <table data-table id="datatable_atraso" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Motivo</th>
                                        <th class="text-center" width="155">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($motivos['atrasos']): ?>
                                        <?php foreach($motivos['atrasos'] as $atraso): ?>
                                            <tr>
                                                <td class="text-center"><?= $atraso['id']; ?></td>
                                                <td><?= $atraso['descricao']; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" aria-label="acao" role="group">
                                                        <a href="javascript:void(0);" onclick="return excluir(<?= $atraso['id']; ?>, 2)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                        <button onclick="return editar(<?= $atraso['id']; ?>, '<?= $atraso['descricao']; ?>', 2)" type="button" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane active p-3" id="hora_extra" role="tabpanel">

                            <div class="col-12 text-right mb-2">
                                <button type="button" onclick="return editar(0, '', 3)" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova justificativa de h.extra</button>
                            </div>

                            <table data-table id="datatable_extra" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Motivo</th>
                                        <th class="text-center" width="155">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($motivos['extras']): ?>
                                        <?php foreach($motivos['extras'] as $extras): ?>
                                            <tr>
                                                <td class="text-center"><?= $extras['id']; ?></td>
                                                <td><?= $extras['descricao']; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" aria-label="acao" role="group">
                                                        <a href="javascript:void(0);" onclick="return excluir(<?= $extras['id']; ?>, 3)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                        <button onclick="return editar(<?= $extras['id']; ?>, '<?= $extras['descricao']; ?>', 3)" type="button" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane p-3" id="ajuste" role="tabpanel">

                            <div class="col-12 text-right mb-2">
                                <button type="button" onclick="return editar(0, '', 4)" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova justificativa de ajuste</button>
                            </div>

                            <table data-table id="datatable_ajuste" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Motivo</th>
                                        <th class="text-center" width="155">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($motivos['ajustes']): ?>
                                        <?php foreach($motivos['ajustes'] as $ajuste): ?>
                                            <tr>
                                                <td class="text-center"><?= $ajuste['id']; ?></td>
                                                <td><?= $ajuste['descricao']; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" aria-label="acao" role="group">
                                                        <a href="javascript:void(0);" onclick="return excluir(<?= $ajuste['id']; ?>, 4)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                        <button onclick="return editar(<?= $ajuste['id']; ?>, '<?= $ajuste['descricao']; ?>', 4)" type="button" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane p-3" id="reprova" role="tabpanel">

                            <div class="col-12 text-right mb-2">
                                <button type="button" onclick="return editar(0, '', 5)" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova justificativa de reprova</button>
                            </div>

                            <table data-table id="datatable_reprova" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Motivo</th>
                                        <th class="text-center" width="155">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($motivos['reprovas']): ?>
                                        <?php foreach($motivos['reprovas'] as $reprova): ?>
                                            <tr>
                                                <td class="text-center"><?= $reprova['id']; ?></td>
                                                <td><?= $reprova['descricao']; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" aria-label="acao" role="group">
                                                        <a href="javascript:void(0);" onclick="return excluir(<?= $reprova['id']; ?>, 5)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                        <button onclick="return editar(<?= $reprova['id']; ?>, '<?= $reprova['descricao']; ?>', 5)" type="button" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>

                        <div class="tab-pane p-3" id="artigo61" role="tabpanel">

                            <div class="col-12 text-right mb-2">
                                <button type="button" onclick="return editar(0, '', 6)" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova justificativa de Artigo 61</button>
                            </div>

                            <table data-table id="datatable_artigo61" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Motivo</th>
                                        <th class="text-center" width="155">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($motivos['artigos61']): ?>
                                        <?php foreach($motivos['artigos61'] as $artigo61): ?>
                                            <tr>
                                                <td class="text-center"><?= $artigo61['id']; ?></td>
                                                <td><?= $artigo61['descricao']; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" aria-label="acao" role="group">
                                                        <a href="javascript:void(0);" onclick="return excluir(<?= $artigo61['id']; ?>, 5)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                        <button onclick="return editar(<?= $artigo61['id']; ?>, '<?= $artigo61['descricao']; ?>', 5)" type="button" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                        </div>

                        <div class="tab-pane p-3" id="ocorrencia" role="tabpanel">

                            <div class="col-12 text-right mb-2">
                                <button type="button" onclick="return editar(0, '', 51)" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo detalhamento de ocorrência</button>
                            </div>

                            <table data-table id="datatable_extra" class="table table-sm table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="80">ID</th>
                                        <th>Motivo</th>
                                        <th class="text-center" width="155">Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($motivos['ocorrencia']): ?>
                                        <?php foreach($motivos['ocorrencia'] as $ocorrencia): ?>
                                            <tr>
                                                <td class="text-center"><?= $ocorrencia['id']; ?></td>
                                                <td><?= $ocorrencia['descricao']; ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" aria-label="acao" role="group">
                                                        <a href="javascript:void(0);" onclick="return excluir(<?= $ocorrencia['id']; ?>, 51)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                        <button onclick="return editar(<?= $ocorrencia['id']; ?>, '<?= $ocorrencia['descricao']; ?>', 51)" type="button" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</button>
                                                    </div>
                                                </td>
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

<!-- modal modal_justificativa -->
<div class="modal modal_justificativa" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_justificativa" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-comment-check-outline"></i> Descrição da Justificativa</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        <div class="form-group row mb-1">
                            <label for="data_batida" class="col-sm-3 col-form-label text-left-sm text-right pb-1">Descrição:</label>
                            <div class="col-sm-9">
                                <input type="text" name="descricao" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="return salvar()">Salvar <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_justificativa -->

<script>
    id_justificativa = 0;
    id_tipo = 0;
    const excluir = (id, tipo) => {

        Swal.fire({
            icon: 'question',
            title: 'Deseja realmente excluir este <b>registro</b>?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Sim Excluir`,
            denyButtonText: `Cancelar`,
            showCancelButton: false,
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
        }).then((result) => {
            if (result.isConfirmed) {

                let dados = {"id":id, "tipo": tipo};

                $.ajax({
                    url: "<?= base_url('ponto/justificativa/action/excluir_config_justificativa') ?>",
                    type:'POST',
                    data:dados,
                    success:function(result){
                        var response = JSON.parse(result);

                        if(response.tipo=='success'){
                            exibeAlerta(response.tipo, response.msg, 3, window.location.href);
                        }else{
                            exibeAlerta(response.tipo, response.msg);
                        }
                        
                    },
                });

            }
        });

    }
    const editar = (id, descricao, tipo) => {

        id_justificativa = id;
        id_tipo = tipo;
        $("[name=descricao]").val(descricao);
        $(".modal_justificativa").modal('show');

    }
    const salvar = () => {

        var dados = {
            'id'        : id_justificativa,
            'tipo'      : id_tipo,
            'descricao' : $("[name=descricao]").val()
        }
        
        if(dados.descricao == ''){exibeAlerta('error', '<b>Descrição</b> obrigatória.'); return;}

        openLoading();

        $.ajax({
            url: "<?= base_url('ponto/justificativa/action/alterar_config_justificativa') ?>",
            type: 'POST',
            data: dados,
            success: function(result) {
                openLoading(true);

                var response = JSON.parse(result);

                if (response.tipo == 'success') {
                    exibeAlerta(response.tipo, response.msg, 3, window.location.href);
                } else {
                    exibeAlerta(response.tipo, response.msg);
                }

            },
        });

    }

    $(document).ready(function() {
        $('[data-table]').DataTable({
            "aLengthMenu"       : [[50, 100, 200, -1], [50, 100, 200, "Todos"]],
            "iDisplayLength"    : 50,
            "aaSorting"         : [[0, "desc"]]
        });
    });
    <?php if(isset($_SESSION['tab_open'])): ?>
        setTimeout(() => {
            $("[data-tab=<?= $_SESSION['tab_open']; ?>]").click();
        }, 1200);
    <?php unset($_SESSION['tab_open']); endif; ?>
</script>
<style>
    .tab-pane {
        border-left: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
    }
</style>
<?php
loadPlugin(array('datatable'));
?>