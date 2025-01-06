<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-9 mb-1 mt-1"><?= $_titulo; ?></h4>
                    <div class="col-3 text-right">
                        <div class="button-items">
                            <a href="<?= base_url('ponto/escala') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="row">
                        <label for="perfil_nome" class="col-2 col-form-label text-right">Funcionário:</label>
                        <div class="col-sm-10">
                            <input class="form-control form-control-sm" type="text" name="chapa" disabled value="<?= $DadosFuncionario['NOME'].' - '.$DadosFuncionario['CHAPA']; ?>">
                        </div>

                        <?php if($DadosFuncionario): ?>
                            <label class="col-2 col-form-label text-right">Horário Atual:</label>
                            <div class="col-sm-10">
                                <input class="form-control form-control-sm" type="text" name="horario" disabled value="<?= $DadosFuncionario['NOMEHORARIO'].' - '.$DadosFuncionario['CODHORARIO']; ?>">
                            </div>
                        <?php endif; ?>

                    </div>

                    <?php
                    if($resEscala['justificativa_11_horas']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário):</b><br>
                                '.$resEscala['justificativa_11_horas'].'
                            </div>';
                    }
                    if($resEscala['justificativa_6_dias']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Funcionário possui mais de 6 dias trabalhados consecutivos):</b><br>
                                '.$resEscala['justificativa_6_dias'].'
                            </div>';
                    }
                    if($resEscala['justificativa_6_meses']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Troca de escala não permitida antes de 6 meses do horário atual do funcionário):</b><br>
                                '.$resEscala['justificativa_6_meses'].'
                            </div>';
                    }
                    ?>

                </div>

            </div>

            <?php if($DadosFuncionario): ?>
                <div class="card">
                    
                    <div class="card-header mt-0">
                        <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Dados da nova escala</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <label for="perfil_nome" class="col-sm-2 col-form-label text-right">Data:</label>
                            <div class="col-sm-3">
                                <input disabled class="form-control form-control-sm" type="date" value="<?= date('Y-m-d', strtotime($resEscala['datamudanca'])) ?>" name="data" id="data" require>
                            </div>
                        </div>
                        <div class="row">
                            <label for="codhorario" class="col-sm-2 col-form-label text-right">Horário:</label>
                            <div class="col-sm-10">
                                <select disabled class="select2 custom-select form-control form-control-sm data_disabled" name="codhorario" id="codhorario" onchange="buscaHorarioIndice(this.value)">
                                    <option value="">- selecione um horário -</option>
                                    <?php if($resHorario): ?>
                                        <?php foreach($resHorario as $key2 => $Horario): ?>
                                            <option value="<?= $Horario['CODIGO']; ?>" <?= (($resEscala['codhorario'] == $Horario['CODIGO']) ? " selected " : "") ?>><?= $Horario['DESCRICAO'].' - '.$Horario['CODIGO']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="indice" class="col-sm-2 col-form-label text-right">Índice:</label>
                            <div class="col-sm-3">
                                <select disabled class="form-control form-control-sm data_disabled" name="indice" id="indice">
                                    <option value="<?= $resEscala['codindice']; ?>"><?= $resEscala['codindice']; ?></option>
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <?php if(($resEscala['step'] == 1 && $resEscala['situacao'] == 0) || ($resEscala['step'] == 2 && $resEscala['situacao'] == 2)): ?>
                    <div class="card-footer text-center">
                        <button class="btn btn-success" id="btnsave" onclick="return aprovar('<?= id($resEscala['id']); ?>', '<?= id($resEscala['step']); ?>')"><i class="far fa-thumbs-up"></i> Aprovar Troca de Escala</button>
                        <button class="btn btn-danger" id="btnreprovar" onclick="return reprovar('<?= id($resEscala['id']); ?>', '<?= id($resEscala['step']); ?>')"><i class="far fa-thumbs-down"></i> Reprovar Troca de Escala</button>
                    </div>
                    <?php endif; ?>
                    

                </div>

                <?php if($resEscala['situacao'] == 1 && $resEscala['step'] == 0 && $resEscala['termo_obrigatorio'] == 1): ?>
                    <div class="card">
                    
                        <div class="card-header mt-0">
                            <div class="row">
                                <h4 class="col-12 mb-1 mt-1">Enviar Termo Aditivo Assinado</h4>
                            </div>
                        </div>

                        <div class="card-body">
                            <form action="" method="post" name="upload_termo_aditivo" id="upload_termo_aditivo" enctype="multipart/form-data">  
                                <input type="file" name="termo_aditivo_assinado" id="termo_aditivo_assinado" class="dropify" onchange="documentoAnexo()" />
                            </form>
                        </div>

                        <div class="card-footer text-center">
                            <button disabled class="btn btn-secondary" id="btnsave" onclick="return enviaDocumentoAssinado()"><i class="mdi mdi-cloud-upload"></i> Enviar Termo Assinado</button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(($resEscala['situacao'] == 2 || $resEscala['situacao'] == 3) && $resEscala['termo_obrigatorio'] == 1): ?>
                    <div class="card">
                    
                        <div class="card-header mt-0">
                            <div class="row">
                                <h4 class="col-12 mb-1 mt-1">Termo Aditivo Assinado</h4>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="alert alert-outline-primary mb-0" role="alert">
                            <?php
                            $documento = explode('|', $resEscala['documento']);
                            ?>
                            <h4><i class="dripicons-download"></i> - <a href="<?= base_url('ponto/escala/download_termo_aditivo/'.id($resEscala['id'])); ?>/<?= id($resEscala['situacao']); ?>" target="_blank"><?= @$documento[0].' - ['.formatBytes(@$documento[2]); ?>]</a></h4>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>

                <div class="card" id="box_projecao">
                    
                    <div class="card-header mt-0">
                        <div class="row">
                            <h4 class="col-8 mb-1 mt-1">Projeção da Escala</h4>

                            <?php if($resEscala['situacao'] == 1 && $resEscala['step'] == 0 && $resEscala['termo_obrigatorio'] == 1): ?>
                            <div class="col-4 text-right">
                                <div class="button-items">
                                    <a href="<?= base_url('ponto/escala/termo_aditivo/'.id($resEscala['id'])); ?>" class="btn btn-info btn-xs" target="_blank"><i class="dripicons-download"></i> termo aditivo</a>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body">

                        <span class="badge mb-2"><b>Legenda:</b></span>
                        <span class="badge bg_compensado mb-2">Compensado</span>
                        <span class="badge bg_folga mb-2">Descanso</span>
                        <span class="badge bg_feriado mb-2">Feriado</span>

                        <table id="table_projecao_escala" class="table table-bordered mb-0 table-centered table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">Data</th>
                                    <th class="text-center">Código do Horário</th>
                                    <th class="text-center">Índice</th>
                                    <th class="text-center">Dia</th>
                                    <th class="text-center">Entrada 1</th>
                                    <th class="text-center">Saída 1</th>
                                    <th class="text-center">Entrada 2</th>
                                    <th class="text-center">Saída 2</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>

                    </div>

                </div>
            <?php endif; ?>

        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
var carregaEscala = () => {

    let dados = {
        "chapa": '<?= $resEscala['chapa']; ?>',
        "data": '<?= date('Y-m-d', strtotime($resEscala['datamudanca'])); ?>',
        "codhorario": '<?= $resEscala['codhorario']; ?>',
        "indice": '<?= $resEscala['codindice']; ?>',
    }

    if(dados.chapa == ""){ exibeAlerta("error", "<b>Funcionário</b> não selecionado."); return false; }
    if(dados.data == ""){ exibeAlerta("error", "<b>Data</b> não informada."); return false; }
    if(dados.codhorario == ""){ exibeAlerta("error", "<b>Horário</b> não informado."); return false; }
    if(dados.indice == ""){ exibeAlerta("error", "<b>Índice</b> não selecionado."); return false; }

    openLoading();

    $("#table_projecao_escala tbody").html('');
    $("#box_projecao").fadeIn(100);

    $.ajax({
        url: "<?= base_url('ponto/escala/action/carrega_escala'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            if(result){
                var response = JSON.parse(result);

                if(typeof response.tipo === 'undefined'){
                    
                    for(var x = 0; x < response.length; x++){

                        var indicador = "";
                        var title = "";
                        if(response[x].TIPO == 'DESCANSO') indicador = "bg_folga", title = ' title = "Descanso"';
                        if(response[x].TIPO == 'COMPENSADO') indicador = "bg_compensado", title = ' title = "Compensado"';
                        if(response[x].DIAFERIADO == 1) indicador = "bg_feriado", title = ' title = "Feriado"';

                        var html = "";
                            html += '<tr>';
                            html += '<td class="text-center bg_dados">'+((response[x].DATAEN == dados.data) ? "<b class='text-danger'>"+response[x].DATA+"</b>" : response[x].DATA)+'</td>';
                            html += '<td class="text-center bg_dados">'+((response[x].CODHORARIO == dados.codhorario) ? "<b class='text-primary'>"+response[x].CODHORARIO+"</b>" : response[x].CODHORARIO)+'</td>';
                            html += '<td class="text-center bg_dados">'+response[x].CODINDICE+'</td>';
                            html += '<td class="text-center bg_dados">'+response[x].DIA+'</td>';
                            html += '<td class="text-center '+indicador+'" '+title+'>'+response[x].ENTRADA1+'</td>';
                            html += '<td class="text-center bg_ref">'+response[x].SAIDA1+'</td>';
                            html += '<td class="text-center bg_ref">'+response[x].ENTRADA2+'</td>';
                            html += '<td class="text-center '+indicador+'" '+title+'>'+response[x].SAIDA2+'</td>';
                            html += '</tr>';
                            $("#table_projecao_escala tbody").append(html);
                    }

                }else{
                    exibeAlerta(response.tipo, response.msg, 3);
                }

            }

            openLoading(true);
            
        },
    });

}
window.onload=function(){
    carregaEscala();
}
<?php if(($resEscala['step'] == 1 && $resEscala['situacao'] == 0) || ($resEscala['step'] == 2 && $resEscala['situacao'] == 2)): ?>
const aprovar = (id, step) => {

    Swal.fire({
        icon: 'question',
        title: 'Deseja realmente aprovar este <b>registro</b>?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim Aprovar`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {

            let dados = {"id":"<?= id($id); ?>", "step":step};

            openLoading();

            $.ajax({
                url: "<?= base_url('ponto/escala/action/aprovar') ?>",
                type:'POST',
                data:dados,
                success:function(result){
                    var response = JSON.parse(result);
                    
                    if(response.tipo == "success"){
                        exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/escala') ?>');
                    }else{
                        exibeAlerta(response.tipo, response.msg, 2);
                    }
                },
            });

        }
    });

}
const reprovar = (id, step) => {

Swal.fire({
    icon: 'question',
    title: 'Deseja realmente reprovar este <b>registro</b>?',
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: `Sim Reprovar`,
    denyButtonText: `Cancelar`,
    showCancelButton: false,
    showCloseButton: false,
    allowOutsideClick: false,
    width: 600,
}).then((result) => {
    if (result.isConfirmed) {

        let dados = {"id":"<?= id($id); ?>", "step":step};

        openLoading();

        $.ajax({
            url: "<?= base_url('ponto/escala/action/reprovar') ?>",
            type:'POST',
            data:dados,
            success:function(result){
                var response = JSON.parse(result);
                
                if(response.tipo == "success"){
                    exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/escala') ?>');
                }else{
                    exibeAlerta(response.tipo, response.msg, 2);
                }
            },
        });

    }
});

}
<?php endif; ?>
</script>
<style>
.bg_dados {
    background-color: #eaeaea;
}
.bg_compensado {
    background-color: #aae0ff;
}
.bg_folga {
    background-color: #68c7ff;
}
.bg_feriado {
    background-color: #4997f9;
}
.bg_ref {
    background-color: #d2f1ce;
}
</style>
<?php
loadPlugin(array('select2','dropify'));
?>
<script>
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
        $("#btnsave").attr('class', 'btn btn-secondary').prop("disabled", true);
    });
});
const documentoAnexo = () => {
    $("#btnsave").attr('class', 'btn btn-success').prop("disabled", false);
}
const enviaDocumentoAssinado = () => {

    Swal.fire({
        icon: 'question',
        title: 'Confirma o envio do documento <b>Termo Aditivo</b> assinado?',
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
            fd.append('id', '<?= id($resEscala['id']); ?>');

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
                            exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/escala/editar').'/'.id($resEscala['id']); ?>/'+response.complemento);
                        }else{
                            exibeAlerta(response.tipo, response.msg, 3);
                        }
                    }catch (e) {
                        exibeAlerta('error', '<b>Erro interno do sistema:</b><br><code class=" language-markup">'+e+'</code>', 8);
                    }

                },
            });

        }
    });

}
</script>