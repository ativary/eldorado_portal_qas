<div class="container-fluid"> 
    <div class="row">
        <!-- main -->
        <div class="col-12">
            <div class="card">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1">Filtro</h4>
                        <div class="col-6 text-right">
                            <div class="button-items">
                                <button type="button" onclick="novo()" class="btn btn-success bteldorado_1 btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova requisição</button>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="" method="post" name="form_filtro" id="form_filtro">
                    <div class="card-body">
                        <div class="row">
                            <label for="filtro_tipo_troca" class="col-2 col-form-label text-right">Tipo de troca:</label>
                            <div class="col-sm-10">
                                <select class="form-control form-control-sm" name="filtro_tipo_troca" id="filtro_tipo_troca">
                                    <option value="">Todos</option>
                                    <option <?= ($filtro_tipo_troca == 1) ? 'selected ' : ''; ?> value="1">Troca de escala</option>
                                    <option <?= ($filtro_tipo_troca == 2) ? 'selected ' : ''; ?> value="2">Troca de dia</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="filtro_funcao" class="col-2 col-form-label text-right">Período:</label>
                            <div class="col-sm-6 input-group">
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control form-control-sm" value="<?= $data_inicio; ?>" onblur="carregaColaboradores()">
                                <div class="input-group-prepend input-group-append"><span class="input-group-text form-control-sm">até</span></div>
                                <input type="date" name="data_fim" id="data_fim" class="form-control form-control-sm" value="<?= $data_fim; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <label for="filtro_colaborador" class="col-2 col-form-label text-right">Colaborador:</label>
                            <div class="col-sm-10">
                                <select class="select2 form-control form-control-sm" name="filtro_colaborador" id="funcionario">
                                    <option value="">Todos (<?= count($colaboradores) ?>)</option>
                                    <?php if($colaboradores): ?>
                                        <?php foreach($colaboradores as $colaborador): ?>
                                            <option <?php if($colaborador['CHAPA'] == $filtro_colaborador){ echo 'selected';} ?> value="<?= $colaborador['CHAPA']; ?>"><?= $colaborador['NOME'].' - '.$colaborador['CHAPA'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="filtro_secao" class="col-2 col-form-label text-right">Seção:</label>
                            <div class="col-sm-10">
                                <select class="select2 form-control form-control-sm" name="filtro_secao" id="filtro_secao">
                                    <option value="">Todos</option>
                                    <?php if($resSecao): ?>
                                        <?php foreach($resSecao as $key => $Secao): ?>
                                            <option <?php if($Secao['CODIGO'] == $filtro_secao){ echo 'selected';} ?> value="<?= $Secao['CODIGO']; ?>"><?= $Secao['CODIGO'].' - '.$Secao['DESCRICAO'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="filtro_funcao" class="col-2 col-form-label text-right">Função:</label>
                            <div class="col-sm-10">
                                <select class="select2 form-control form-control-sm" name="filtro_funcao" id="filtro_funcao">
                                    <option value="">Todos</option>
                                    <?php if($resFuncao): ?>
                                        <?php foreach($resFuncao as $key => $Funcao): ?>
                                            <option <?php if($filtro_funcao ==  $Funcao['CODIGO']){ echo 'selected';} ?> value="<?= $Funcao['CODIGO']; ?>"><?= $Funcao['CODIGO'].' - '.$Funcao['NOME'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="perfil_nome" class="col-2 col-form-label text-right">Status:</label>
                            <div class="col-sm-6">
                                <select class="form-control form-control-sm" name="filtro" id="filtro">
                                    <option value="">Todos</option>
                                    <option value="0" <?= ($filtro == 0 && $filtro != null) ? " selected " : ""; ?>>Criada</option>
                                    <option value="10" <?= ($filtro == 10) ? " selected " : ""; ?>>Pend/Ação Gestor</option>
                                    <!-- <option value="1" <?= ($filtro == 1) ? " selected " : ""; ?>>Aguardando upload documento</option> -->
                                    <!-- <option value="4" <?= ($filtro == 4) ? " selected " : ""; ?>>Termo anexado</option> -->
                                    <option value="2" <?= ($filtro == 2) ? " selected " : ""; ?>>Pend/Ação RH</option>
                                    <option value="3" <?= ($filtro == 3) ? " selected " : ""; ?>>Concluído</option>
                                    <option value="9" <?= ($filtro == 9) ? " selected " : ""; ?>>Reprovado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-success bteldorado_1 btn-xxs" id="btnsave" onclick="return filtraDados()"><i class="fas fa-search"></i> Filtrar</button>
                    </div>
                </form>
            </div>

            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>
                        
                        <div class="row mb-3" style="margin-top: -62px;">
                            <div class="col-12 text-right"><button onclick="return excel()" type="button" class="btnpeq btn-sm btn-success bteldorado_1"><i class="mdi mdi-file-excel"></i> Exportar excel</button></div>
                        </div>
                        
                        <form>
                        <table id="datatable" class="table table-sm table-bordered table-responsive_mobile" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60"><strong>ID</strong></th>
                                    <th class="text-center"><strong>Status</strong></th>
                                    <th><strong>Tipo de troca</strong></th>
                                    <th><strong>Colaborador</strong></th>
                                    <th><strong>Data de Solicitação</strong></th>
                                    <th><strong>Usuário Solicitante</strong></th>
                                    <th class="text-center"><strong>Anexo</strong></th>
                                    <th class="text-center no-sort" width="10"><strong>Ações</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resEscala): ?>
                                <?php foreach($resEscala as $key => $Escala): ?>
                                    <tr data-linha="<?= $Escala['id'] ?>">
                                        <td class="text-center"><?= $Escala['id']; ?></td>
                                        <td class="text-center" width="150">
                                        <?php
                                            switch($Escala['situacao']){
                                                case 0: echo '<span class="badge badge-warning">Criada</span>'; break;
                                                case 10: echo '<span class="badge badge-dark">Pend/Ação Gestor</span>'; break;
                                                case 1: echo '<span class="badge badge-success">Pend/Upload Documento</span>'; break;
                                                case 4: echo '<span class="badge badge-success">Termo Anexado</span>'; break;
                                                case 2: echo '<span class="badge badge-info">Pend/Ação RH</span>'; break;
                                                case 3: echo '<span class="badge badge-primary">Concluído</span>'; break;
                                                case 8:
                                                case 9: 
                                                        echo '<span class="badge badge-danger">Reprovado</span>'; break;
                                                case 11: echo '<span class="badge badge-danger">Excluído</span>'; break;
                                                // case 9: echo '<span class="badge badge-danger">Cancelado</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td>
                                            <?php
                                                switch($Escala['tipo']){
                                                    case 1: echo 'Troca de escala'; break;
                                                    case 2: echo 'Troca de dia'; break;
                                                    default: echo '--'; break;
                                                }
                                            ?>
                                        </td>
                                        <td><a href="<?= base_url('ponto/escala/'.(($Escala['tipo'] == 1) ? 'editar' : 'editardia').'/'.id($Escala['id'])).'/'.id($Escala['situacao']); ?>"><?= $Escala['chapa'].' - '.$Escala['nome']; ?></a></td>
                                        <td><?= date('d/m/Y', strtotime($Escala['dtcad'])); ?></td>
                                        <td><?= $Escala['chapa_solicitante'].' - '.$Escala['solicitante']; ?></td>
                                        <td class="text-center"><?= ($Escala['usuupload']) ? '<a href="'.base_url('ponto/escala/download_termo_aditivo/'.id($Escala['id']).'/'.id($Escala['situacao'])).'" target="_blank">Ver documento</a>' : '' ?></td>
                                        <td class="text-center">
                                            <div class="btn-group dropleft mb-2 mb-md-0">
                                                <div class="dropdown">
                                                    <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                    <div class="dropdown-menu" style="margin-left: -131px;">

                                                        <?php if($Escala['situacao'] == 0): ?>
                                                            <a href="<?= base_url('ponto/escala/termo_aditivo/'.id($Escala['id'])); ?>" target="_blank" class="dropdown-item"><i class="mdi mdi-cloud-download-outline"></i> Exportar documento</a>
                                                        <?php endif; ?>

                                                        <?php if(in_array($Escala['situacao'], [0])): ?>
                                                            <button type="button" onclick="abreUpload('<?= id($Escala['id']); ?>')" class="dropdown-item"><i class="mdi mdi-file-upload-outline"></i> <?= ($Escala['situacao'] == 0 && strlen(trim($Escala['usuupload']) <= 0)) ? 'Anexar documento' : 'Alterar documento' ?></button>
                                                        <?php endif; ?>

                                                        <a href="<?= base_url('ponto/escala/'.(($Escala['tipo'] == 1) ? 'editar' : 'editardia').'/'.id($Escala['id'])).'/'.id($Escala['situacao']); ?>" class="dropdown-item">
                                                            <?php if(in_array($Escala['situacao'], [0,8])): ?>
                                                                <i class="mdi mdi-square-edit-outline"></i> Editar requisição
                                                            <?php else: ?>
                                                                <i class="mdi mdi-eye-outline"></i> Ver requisição
                                                            <?php endif; ?>
                                                        </a>


                                                        <?php if(in_array($Escala['situacao'], [0])): ?>
                                                            <button type="button" onclick="enviaParaAprovacao(<?= $Escala['id']; ?>)" class="dropdown-item"><i class="mdi mdi-file-send"></i> Enviar para aprovação</button>
                                                        <?php endif; ?>

                                                       
                                                       
                                                        
                                                       
                                                        
                                                        <button type="button" onclick="justificativas('<?= id($Escala['id']); ?>')" class="dropdown-item"><i class="mdi mdi-comment-eye-outline"></i> Ver justificativa</button>
                                                        <?php if(in_array($Escala['situacao'], [0,8])): ?>
                                                            <div class="dropdown-divider"></div>
                                                           
                                                            <button onclick="excluir('<?= $Escala['id']; ?>')" class="dropdown-item text-danger"><i class="mdi mdi-trash-can-outline"></i> Excluir</button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                        </form>

                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<!-- modal troca escala/dia -->
<div class="modal modal_troca" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_troca" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header text-dark pt-3 pb-2" style="background: #ffffff;">
                <h5 class="modal-title mt-0">Selecione o tipo de troca para prosseguir</h5>
                <button type="button" class="close text-dark" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body" style="background: #ffffff;">

                <div class="row">
                    <label for="tipo" class="col-2 col-form-label text-right">Tipo:</label>
                    <div class="col-10">
                        <select name="tipo_troca" id="tipo_troca" class="form-control form-control-sm">
                            <option value="">...</option>
                            <option value="<?= base_url('ponto/escala/novo') ?>">Troca de Escala</option>
                            <option value="<?= base_url('ponto/escala/novodia') ?>">Troca de Dia</option>
                        </select>
                    </div>
                </div>

            </div>
            <div class="modal-footer text-center justify-content-center">
                <button class="btn btn-success bteldorado_1 btn-xxs" type="button" onclick="prosseguir()">Prosseguir</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_troca -->

<!-- modal upload -->
<div class="modal modal_upload" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_upload" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header text-dark pt-3 pb-2" style="background: #ffffff;">
                <h5 class="modal-title mt-0">Selecione o termo assinado</h5>
                <button type="button" class="close text-dark" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body" style="background: #ffffff;">

                <div class="row">
                    <div class="col-12">
                        <p class="text-dark p-0"><strong>Formatos permitidos</strong>: PDF ou Imagem</p>
                        <form action="" method="post" name="upload_termo_aditivo" id="upload_termo_aditivo" enctype="multipart/form-data">  
                            <input type="file" name="termo_aditivo_assinado" id="termo_aditivo_assinado" class="dropify" onchange="documentoAnexo()" accept="application/pdf,image/jpeg,image/png,image/gif" />
                            <input type="hidden" name="id_escala" id="id_escala">
                        </form>
                    </div>
                </div>

            </div>
            <div class="modal-footer text-center justify-content-center">
                <button class="btn btn-secondary btn-xxs" id="btnupload" disabled type="button" onclick="enviaDocumentoAssinado()">Enviar documento</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal upload -->

<!-- modal justificativa -->
<div class="modal modal_justificativa" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_justificativa" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header text-dark pt-3 pb-2" style="background: #ffffff;">
                <h5 class="modal-title mt-0">Justificativas da requisição</h5>
                <button type="button" class="close text-dark" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body" style="background: #ffffff;" id="justificativas">

                

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal upload -->


<script>
const abreUpload = (idEscala) => {
    $('#id_escala').val(idEscala);
    $(".dropify-clear").click();
    $(".modal_upload").modal('show');
}
$(function () {
    var drEvent = $('.dropify').dropify({
        messages: {
            default: 'Arraste e solte um arquivo aqui ou clique',
            replace: 'Arraste e solte um arquivo ou clique para substituir',
            remove:  'Remover',
            error:   'Desculpe, o arquivo é muito grande'
        }
    });
    
    drEvent.on('dropify.afterClear', function(event, element){
        $("#btnupload").attr('class', 'btn btn-secondary btn-xxs').prop("disabled", true);
    });
});
const documentoAnexo = () => {
    $("#btnupload").attr('class', 'btn btn-success bteldorado_1 btn-xxs').prop("disabled", false);
}
const justificativas = (idEscala) => {

    openLoading();
    $("#justificativas").html('');

    $.ajax({
        url: "<?= base_url('ponto/escala/action/justificativa') ?>",
        type:'POST',
        data: {
            "id": idEscala
        },
        success:function(result){
            $(".modal_justificativa").modal('show');
            openLoading(true);
            if(result == ""){
                $("#justificativas").html('<div class="alert alert-warning2 border-0" role="alert">Nenhuma justificativa encontrada.</div>');
            }else{
                $("#justificativas").html(result);
            }

        },
    });

}
const enviaDocumentoAssinado = () => {

    var idEscala = $('#id_escala').val();

    Swal.fire({
        icon: 'question',
        title: 'Confirma o envio do documento?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim Enviar`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {

            openLoading();
            
            let fd = new FormData();
            let img = $('#termo_aditivo_assinado');
            fd.append('documento', img[0].files[0]);
            fd.append('id', idEscala);

            $.ajax({
                url: "<?= base_url('ponto/escala/action/upload_termo_aditivo') ?>",
                type:'POST',
                processData: false,
                contentType: false,
                data: fd,
                success:function(result){
                    try {
                        var response = JSON.parse(result);
                        
                        if(response.tipo == "success"){
                            exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                        }else{
                            exibeAlerta(response.tipo, response.msg);
                        }
                    }catch (e) {
                        exibeAlerta('error', '<b>Erro interno do sistema:</b><br><code class=" language-markup">'+e+'</code>', 8);
                    }

                },
            });

        }
    });

}
const prosseguir = () => {
    let url = $("#tipo_troca").val();
    if(url == ''){
        exibeAlerta('warning', 'Tipo de troca de escala não foi selecionado.');
        return;
    }
    openLoading();
    $('.modal_troca').modal('hide');
    window.location=url;
}
const novo = () => {
    $('.modal_troca').modal('show');
}
const filtraDados = () => {

    openLoading();
    $("#form_filtro").attr('target', '_top');
    $("#form_filtro").attr('action', '');
    document.getElementById("form_filtro").submit();
    
}
const excel = () => {
    $("#form_filtro").attr('target', '_blank');
    $("#form_filtro").attr('action', '/ponto/escala/excel');
    document.getElementById("form_filtro").submit();
}
const enviaParaAprovacao = (idEscala) => {
    Swal.fire({
        icon              : 'question',
        title             : 'Confirmar envio para aprovação?',
        text              : 'Após envio não será possivel alterar a troca de dia.',
        showDenyButton    : true,
        showCancelButton  : true,
        confirmButtonText : `Sim Confirmar`,
        denyButtonText    : `Cancelar`,
        showCancelButton  : false,
        showCloseButton   : false,
        allowOutsideClick : false,
        width             : 600,
    }).then((result) => {
        if (result.isConfirmed) {
            openLoading();

            $.ajax({
                url: "<?= base_url('ponto/escala/action/envia_aprovacao') ?>",
                type:'POST',
                data: {
                    'id': idEscala
                },
                success:function(result){
                    
                    var response = JSON.parse(result);

                    if(response.tipo != 'success'){
                        exibeAlerta(response.tipo, response.msg);
                    }else{
                        exibeAlerta(response.tipo, response.msg, 3, window.location.href);
                    }
                    
                },
            });
        }
    });
}
const excluir = (idEscala) => {
    event.preventDefault();
    Swal.fire({
        icon              : 'question',
        title             : 'Confirmar a exclusão da requisição de troca de Dia?',
        text              : 'Após exclusão não será possivel recuperar.',
        showDenyButton    : true,
        showCancelButton  : true,
        confirmButtonText : `Sim Excluir`,
        denyButtonText    : `Cancelar`,
        showCancelButton  : false,
        showCloseButton   : false,
        allowOutsideClick : false,
        width             : 600,
    }).then((result) => {
        if (result.isConfirmed) {
            openLoading();

            $.ajax({
                url: "<?= base_url('ponto/escala/action/excluir') ?>",
                type:'POST',
                data: {
                    'id': idEscala
                },
                success:function(result){

                    console.log(result);
                    
                    var response = JSON.parse(result);

                    if(response.tipo != 'success'){
                        exibeAlerta(response.tipo, response.msg);
                    }else{
                        exibeAlerta(response.tipo, response.msg, 3, window.location.href);
                    }
                    
                },
            });
        }
    });
}
const carregaColaboradores = () => {

    var periodo = $("#data_inicio").val();
    if(periodo == ''){
        var dataInicio = '<?= date('d/m/Y'); ?>'
    }else{
        var dataInicio = periodo.substr(8,2)+'/'+periodo.substr(5,2)+'/'+periodo.substr(0,4);
    }

    $("#funcionario").html('<option value="">Todos</option>').trigger('change');

    if(periodo == "") return;

    openLoading();

    $.ajax({
        url: "<?= base_url('ponto/espelho/action/carrega_colaboradores') ?>",
        type: 'POST',
        data: {
            'codsecao'    : null,
            'periodo'     : dataInicio
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
<?php loadPlugin(array('select2', 'datatable', 'dropify')); ?>
<style> 
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0 !important;
    border: none !important;
    background: none !important;
}
.dtfh-floatingparent {
    height: 205px !important;
}
.dtfh-floatingparent .sorting {
    padding-top: 75px !important;
}
body {
    overflow-x: auto !important;
}
.select2-results {
    width: max-content !important;
    background-color: #ffffff;
    border: 1px solid #cccccc;
}
</style>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>"/>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>
<script>
    $(document).ready(function () {
    // Inicialização do DataTable
    var tabelaAprovacao = $('#datatable').DataTable({
        "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength": 25,
        "aaSorting": [[0, "desc"]],
        "fixedHeader": true, // Ativa o fixedHeader
        "language": {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Processando...",
            "sLengthMenu": "Exibir _MENU_ registros",
            "sZeroRecords": "Nenhum registro encontrado",
            "sEmptyTable": "Nenhum dado disponível nesta tabela",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
            "sSearch": "Procurar:",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Próximo",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": Ordenar colunas de forma ascendente",
                "sSortDescending": ": Ordenar colunas de forma descendente"
            }
        },
        initComplete: function () {
            var api = this.api(); // Instância do DataTable
            var p_linha = api.columns()[0].length;

            // Configura filtros personalizados
            api.columns().every(function () {
                var column = this;

                if (column[0][0] == 0 || column[0][0] == 7) return false;

                var select = $('<select class="form-control form-control-sm filtro_table"><option value=""></option></select>')
                    .appendTo($(column.header()))
                    .on('change', function () {
                        var val = $(this).val();
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    var noHTML = removerHTML(d);
                    if(j == 0) select.append('<option value="">- Todos -</option>');
                    if(noHTML != "") select.append('<option value="' + noHTML + '">' + noHTML + '</option>');
                });
            });

            $(".filtro_table").select2({
                width: '100%',
                language: {
                    noResults: function () {
                        return 'Nenhum resultado encontrado';
                    }
                }
            });
            setInterval(function(){
                // tabelaAprovacao.fixedHeader.adjust();
            }, 1000);
        },
    });
    
});
function removerHTML(variavelComHTML) {
    const elemento = document.createElement('div');
    elemento.innerHTML = variavelComHTML;
    return elemento.textContent || elemento.innerText || '';
}
</script>