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
                        <label for="perfil_nome" class="col-2 col-form-label text-right">Colaborador:</label>
                        <div class="col-sm-10">
                            <input class="form-control form-control-sm" type="text" name="chapa" disabled value="<?= $DadosFuncionario['NOME'].' - '.$DadosFuncionario['CHAPA']; ?>">
                        </div>

                        <?php if($DadosFuncionario): ?>
                            <label class="col-2 col-form-label text-right">Horário Atual:</label>
                            <div class="col-sm-10">
                                <input class="form-control form-control-sm" type="text" name="horario" disabled value="<?= $DadosFuncionario['NOMEHORARIO'].' - '.$DadosFuncionario['CODHORARIO']; ?>">
                            </div>
                        <?php endif; ?>

                        <label for="solicitante" class="col-2 col-form-label text-right">Solicitante:</label>
                        <div class="col-sm-10">
                            <input class="form-control form-control-sm" type="text" disabled value="<?= $resEscala['solicitante']; ?>">
                        </div>

                    </div>

                    <?php
                    if($resEscala['justificativa_11_horas']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário):</b><br>
                                '.nl2br($resEscala['justificativa_11_horas']).'
                            </div>';
                    }
                    if($resEscala['justificativa_6_dias']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Colaborador possui mais de 6 dias trabalhados consecutivos):</b><br>
                                '.nl2br($resEscala['justificativa_6_dias']).'
                            </div>';
                    }
                    if($resEscala['justificativa_6_meses']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Troca de escala inferior a 6 meses do horário atual do colaborador):</b><br>
                                '.nl2br($resEscala['justificativa_6_meses']).'
                            </div>';
                    }
                    if($resEscala['justificativa_3_dias']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Alteração dentro de 72 horas):</b><br>
                                '.nl2br($resEscala['justificativa_3_dias']).'
                            </div>';
                    }
                    if($resEscala['justificativa_periodo']){
                        echo '<div class="alert alert-warning2 border-0 m-0 mt-2" role="alert">
                                <b>Justificativa (Fora do período permitido):</b><br>
                                '.nl2br($resEscala['justificativa_periodo']).'
                            </div>';
                    }
                    ?>

                </div>

            </div>

            <?php if($DadosFuncionario): ?>
                <div class="card">
                    
                    <div class="card-header mt-0">
                        <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Dados da nova troca</h4>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <label for="perfil_nome" class="col-sm-2 col-form-label text-right">Data (Dia Trabalho):</label>
                            <div class="col-sm-3">
                                <input min="<?= dtEn($resEscala['config_dia_per_ini'], true) ?>" max="<?= dtEn($resEscala['config_dia_per_fim'], true) ?>" <?= (($resEscala['situacao'] != 0 && $resEscala['situacao'] != 8) ? 'disabled' : ''); ?> class="form-control form-control-sm" type="text" value="<?= date('d/m/Y', strtotime($resEscala['datamudanca'])) ?>" name="data" id="data" required readonly onkeyup="selecionaData(this.value);" onchange="selecionaData(this.value);">
                            </div>
                        </div>
                        <div class="row">
                            <label for="indice" class="col-sm-2 col-form-label text-right">Índice:</label>
                            <div class="col-sm-3"><?php ?>
                                <select <?= (($resEscala['situacao'] != 0 && $resEscala['situacao'] != 8) ? 'disabled' : ''); ?> class="form-control form-control-sm data_disabled" name="indice" id="indice">
                                    <?php if($resIndice): ?>
                                        <?php foreach($resIndice as $indice): ?>
                                            <option <?= (str_contains($indice['HORARIO'], 'TRABALHO') ? '' : 'disabled') ?> value="<?= $indice['INDICE']; ?>" <?= (($resEscala['codindice'] == $indice['INDICE']) ? " selected " : "") ?>><?= $indice['INDICE'].' | '.$indice['HORARIO']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <label for="data_folga" class="col-sm-2 col-form-label text-right">Data (Dia Folga):</label>
                            <div class="col-sm-3">
                            <input min="<?= dtEn($resEscala['config_dia_per_ini'], true) ?>" <?= (($resEscala['situacao'] != 0 && $resEscala['situacao'] != 8) ? 'disabled' : ''); ?> class="form-control form-control-sm" type="text" value="<?= date('d/m/Y', strtotime($resEscala['datamudanca_folga'])) ?>" name="data" id="data_folga" required readonly onkeyup="selecionaDataFolga(this.value);" onchange="selecionaDataFolga(this.value);">
                            </div>
                        </div>
                        <div class="row">
                            <label for="indice_folga" class="col-sm-2 col-form-label text-right">Índice:</label>
                            <div class="col-sm-3">
                                <select <?= (($resEscala['situacao'] != 0 && $resEscala['situacao'] != 8) ? 'disabled' : ''); ?> class="form-control form-control-sm data_disabled" name="indice_folga" id="indice_folga">
                                    <?php if($resIndice): ?>
                                        <?php foreach($resIndice as $indice): ?>
                                            <option <?= (str_contains($indice['HORARIO'], 'TRABALHO') ? 'disabled' : '') ?> value="<?= $indice['INDICE']; ?>" <?= (($resEscala['codindice_folga'] == $indice['INDICE']) ? " selected " : "") ?>><?= $indice['INDICE'].' | '.$indice['HORARIO']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        
                        <?php if($resEscala['situacao'] == 0 || $resEscala['situacao'] == 8): ?>
                        <div class="card border mt-4 hidden box_justificativa_11_horas" style="background: #fffbec; border-color: #ebd9a6 !important;">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="justificativa_11_horas" class="col-form-label text-left">Justificativa (Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário):</label>
                                        <textarea class="form-control" name="justificativa_11_horas" id="justificativa_11_horas" maxlength="220" cols="30" rows="2"><?= $resEscala['justificativa_11_horas']; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border mt-4 hidden box_justificativa_3_dias" style="background: #fffbec; border-color: #ebd9a6 !important;">
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <label for="justificativa_3_dias" class="col-form-label text-left">Justificativa (Alteração dentro de 72 horas):</label>
                                    <textarea class="form-control" name="justificativa_3_dias" id="justificativa_3_dias" maxlength="220" cols="30" rows="2"><?= $resEscala['justificativa_3_dias']; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border mt-4 hidden box_justificativa_6_dias" style="background: #fffbec; border-color: #ebd9a6 !important;">
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <label for="justificativa_6_dias" class="col-form-label text-left">Justificativa (Colaborador possui mais de 6 dias trabalhados consecutivos):</label>
                                    <textarea class="form-control" name="justificativa_6_dias" id="justificativa_6_dias" maxlength="220" cols="30" rows="2"><?= $resEscala['justificativa_6_dias']; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border mt-4 hidden box_justificativa_6_meses" style="background: #fffbec; border-color: #ebd9a6 !important;">
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <label for="justificativa_6_meses" class="col-form-label text-left">Justificativa (Troca de escala inferior a 6 meses do horário atual do colaborador):</label>
                                    <textarea class="form-control" name="justificativa_6_meses" id="justificativa_6_meses" maxlength="220" cols="30" rows="2"><?= $resEscala['justificativa_6_meses']; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card border mt-4 hidden box_justificativa_periodo" style="background: #fffbec; border-color: #ebd9a6 !important;">
                            <div class="card-body">
                                <div class="col-sm-12">
                                    <label for="justificativa_periodo" class="col-form-label text-left">Justificativa (Fora do período permitido <b class="text-primary"><?= dtBr($resEscala['config_dia_per_ini']) ?></b> à <b class="text-primary"><?= dtBr($resEscala['config_dia_per_fim']) ?></b>):</label>
                                    <textarea class="form-control" name="justificativa_periodo" id="justificativa_periodo" maxlength="220" cols="30" rows="2"><?= $resEscala['justificativa_periodo']; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    <?php if($resEscala['situacao'] == 0 || $resEscala['situacao'] == 8): ?>
                    <div class="card-footer text-center">
                        <button type="button" class="btn btn-xxs btn-primary bteldorado_7" onclick="carregaEscala()">Visualizar Projeção</button>
                        <!-- <button type="button" class="btn btn-xxs btn-success bteldorado_2" onclick="return enviaParaAprovacao()">Enviar para aprovação</button> -->
                    </div>
                    <?php endif; ?>
                    

                </div>

                <?php if(strlen(trim($resEscala['documento'])) > 0): ?>
                    <div class="card">
                    
                        <div class="card-header mt-0">
                            <div class="row">
                                <h4 class="col-12 mb-1 mt-1">Termo Assinado</h4>
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
                <?php if($resEscala['situacao'] == 0): ?>
                    <div class="card">
                    
                        <div class="card-header mt-0">
                            <div class="row">
                                <h4 class="col-12 mb-1 mt-1"><?= ($resEscala['situacao'] == 0 && strlen(trim($resEscala['documento'])) <= 0) ? 'Enviar Termo Aditivo Assinado' : 'Alterar Termo Assinado' ?></h4>
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

                <div class="card" id="box_projecao">
                    
                    <div class="card-header mt-0">
                        <div class="row">
                            <h4 class="col-8 mb-1 mt-1">Projeção Dia (Trabalho)</h4>

                            <?php if($resEscala['situacao'] == 0): ?>
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

                <div class="card" id="box_projecao_folga">
                    <div class="card-header mt-0">
                        <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Projeção Dia (Folga)</h4>
                        </div>
                    </div>
                    <div class="card-body">

                        <span class="badge mb-2"><b>Legenda:</b></span>
                        <span class="badge bg_compensado mb-2">Compensado</span>
                        <span class="badge bg_folga mb-2">Descanso</span>
                        <span class="badge bg_feriado mb-2">Feriado</span>

                        <table id="table_projecao_escala_folga" class="table table-bordered mb-0 table-centered table-sm">
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

                    <?php if($resEscala['situacao'] == 0 || $resEscala['situacao'] == 8): ?>
                    <div class="card-footer text-center">
                        <button type="button" class="btn btn-xxs btn-success bteldorado_1" onclick="return salvaDados()" id="botao_salvar">Salvar</button>
                    </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
const selecionaData = (data) => {
    var [dia, mes, ano] = data.split('/');
    const dataFormatada = `${ano}-${mes}-${dia}`;

    const inputData = new Date(dataFormatada);
    const dataAtual = new Date();
    const tresDiasDepois = new Date();
    tresDiasDepois.setDate(dataAtual.getDate() + 2);
    
    if (inputData < tresDiasDepois && (inputData > dataAtual || inputData == dataAtual) ) {
        
        Swal.fire({
		icon: 'question',
		title: 'A data escolhida é em menos de 72 horas. Deseja continuar?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim Confirmar`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
        }).then((result) => {
            if (result.isConfirmed) {
                $(".data_disabled").prop("disabled", ((data == "") ? true : false));
                $("#indice").val('');
                
                if(data == ""){
                    $("#box_projecao").fadeOut(100);
                }else{
                    
                    buscaHorarioIndice('<?= $DadosFuncionario['CODHORARIO']; ?>');
                }
                
                verificaData();
            
            }else{
                $("#data").val("");
                $(".data_disabled").prop("disabled", true);
                $("#indice").val('');

            }
        });
    }else{
        $(".data_disabled").prop("disabled", ((data == "") ? true : false));
        $("#indice").val('');
        console.log(data);
        if(data == ""){
            $("#box_projecao").fadeOut(100);
        }else{
            console.log('<?= $DadosFuncionario['CODHORARIO']; ?>');
            buscaHorarioIndice('<?= $DadosFuncionario['CODHORARIO']; ?>');
        }
        
        verificaData();
    } 

}
const selecionaDataFolga = (data) => {
    var [dia, mes, ano] = $("#data_folga").val().split('/');
    const dataFormatada = `${ano}-${mes}-${dia}`;

    const inputData = new Date(dataFormatada);
    const dataAtual = new Date();
    const tresDiasDepois = new Date();
    tresDiasDepois.setDate(dataAtual.getDate() + 2);

    console.log(inputData);
    console.log(dataAtual);
    console.log(tresDiasDepois);
    
    if (inputData < tresDiasDepois) {
        
        Swal.fire({
		icon: 'question',
		title: 'Fora do prazo mínimo de 72 horas. Deseja continuar?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim Confirmar`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
        }).then((result) => {
            if (result.isConfirmed) {
                verificaDataFolga();
            
            }else{
                $("#data_folga").val("");
                $(".data_disabled").prop("disabled", true);
                $("#indice_folga").val('');

            }
        });
    }else{
        verificaDataFolga();
    } 
}
const buscaHorarioIndice = (codhorario) => {

    console.log(codhorario);
    
    let dados = {
        "codhorario": codhorario,
    }

    $("#box_projecao, #box_projecao_folga").fadeOut(100);

    // $("#codhorario_folga").val(codhorario).change();
    $("#indice, #indice_folga").html('<option value="">...</option>');
    
    $("#table_projecao_escala tbody").html('');

    $.ajax({
        url: "<?= base_url('ponto/escala/action/horario_indice'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            for(var x = 0; x < response.length; x++){
                var horario = response[x].HORARIO;
                $("#indice").append('<option '+((horario.includes('TRABALHO') ? '' : 'disabled'))+' value="'+response[x].INDICE+'">'+response[x].INDICE+' | '+horario.replace(/\s{2,}/g, ' ').replaceAll(' ', ' - ')+'</option>');
                $("#indice_folga").append('<option '+((horario.includes('TRABALHO') ? 'disabled' : ''))+' value="'+response[x].INDICE+'">'+response[x].INDICE+' | '+horario.replace(/\s{2,}/g, ' ').replaceAll(' ', ' - ')+'</option>');
            }
            
        },
    });

}
const carregaEscala = () => {

    let dados = {
        "chapa"         : "<?= $resEscala['chapa']; ?>",
        "data"          : fixData($("#data").val()),
        "codhorario"    : '<?= $resEscala['codhorario']; ?>',
        "indice"        : $("#indice").val()
    }
    let dadosFolga = {
        "chapa"         : "<?= $resEscala['chapa']; ?>",
        "data"          : fixData($("#data_folga").val()),
        "codhorario"    : '<?= $resEscala['codhorario']; ?>',
        "indice"        : $("#indice_folga").val()
    }

    if(dados.chapa == ""){ exibeAlerta("error", "<b>Colaborador</b> não selecionado."); return false; }
    if(dados.data == ""){ exibeAlerta("error", "<b>Data (Dia Trabalho)</b> não informada."); return false; }
    if(dados.codhorario == ""){ exibeAlerta("error", "<b>Horário</b> dia trabalho não informado."); return false; }
    if(dados.indice == ""){ exibeAlerta("error", "<b>Índice</b> dia trabalho não selecionado."); return false; }
    if(dadosFolga.data == ""){ exibeAlerta("error", "<b>Data (Dia Folga)</b> não informada."); return false; }
    if(dadosFolga.codhorario == ""){ exibeAlerta("error", "<b>Horário</b> dia folga não informado."); return false; }
    if(dadosFolga.indice == ""){ exibeAlerta("error", "<b>Índice</b> dia folga não selecionado."); return false; }
    if(dados.codhorario != dadosFolga.codhorario){ exibeAlerta("error", "<b>Horário</b> dia trabalho não pode ser diferente do horário dia folga."); return false; }

    <?php if($resEscala['situacao'] == 0 || $resEscala['situacao'] == 8): ?>
    if(
        (dados.data < '<?= dtEn($resEscala['config_dia_per_ini'], true) ?>' || dados.data > '<?= dtEn($resEscala['config_dia_per_fim'], true) ?>') || 
        (dadosFolga.data < '<?= dtEn($resEscala['config_dia_per_ini'], true) ?>' || dadosFolga.data > '<?= dtEn($resEscala['config_dia_per_fim'], true) ?>')){
        $(".box_justificativa_periodo").fadeIn(0);
    }else{
        $(".box_justificativa_periodo").fadeOut(0);
        $("#justificativa_periodo").val('');
    }
    <?php endif; ?>

    if(!validaData()){
        return;
    }

    openLoading();

    projetado = true;
    $("#botao_salvar").prop('disabled', false);

    projecaoDiaUtil(dados);
    projecaoDiaFolga(dadosFolga);

}
const validaData = () => {
    let diaUtil   = fixData($("#data").val());
    let diaFolga  = fixData($("#data_folga").val());

    if(diaUtil == diaFolga){exibeAlerta('warning', 'Dia trabalho não pode ser igual ao Dia folga.'); return false;}

    const diffInMs    = new Date(diaFolga) - new Date(diaUtil)
    var diffInDays    = diffInMs / (1000 * 60 * 60 * 24);
    if(diffInDays < 0) diffInDays = diffInDays*-1;

    if(diffInDays > 90){exibeAlerta('warning', 'Intervalo entre <strong>Dia Trabalho</strong> e <strong>Dia Folga</strong> não pode ser superior a <strong>90 dias</strong>.'); return false;}
    
    return true;
}
const projecaoDiaUtil = (dados) => {

    $("#table_projecao_escala tbody").html('');
    $("#box_projecao").fadeIn(100);

    $.ajax({
        url: "<?= base_url('ponto/escala/action/carrega_escala_dia'); ?>",
        type:'POST',
        data:{
            'id'            : <?= $resEscala['id'] ?>,
            'tipo'          : 'trabalho',
            'data'          : dados.data,
            'chapa'         : dados.chapa,
            'codhorario'    : dados.codhorario,
            'indice'        : dados.indice,
        },
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

                        if(response[x].SAIDA1_MINUTO < response[x].ENTRADA1_MINUTO){response[x].SAIDA1_MINUTO = response[x].SAIDA1_MINUTO + 1440; response[x].SAIDA1 = '+'+response[x].SAIDA1;}
                        if(response[x].ENTRADA2_MINUTO < response[x].SAIDA1_MINUTO){response[x].ENTRADA2_MINUTO = response[x].ENTRADA2_MINUTO + 1440; response[x].ENTRADA2 = '+'+response[x].ENTRADA2;}
                        if(response[x].SAIDA2_MINUTO < response[x].ENTRADA2_MINUTO){response[x].SAIDA2_MINUTO = response[x].SAIDA2_MINUTO + 1440; response[x].SAIDA2 = '+'+response[x].SAIDA2;}

                        var html = "";
                            html += '<tr>';
                            html += '<td class="text-center bg_dados" data-data="'+x+'" data-date="'+response[x].DATA+'">'+((response[x].DATAEN == dados.data) ? "<b class='text-danger'>"+response[x].DATA+"</b>" : response[x].DATA)+'</td>';
                            html += '<td class="text-center bg_dados">'+((response[x].CODHORARIO == dados.codhorario) ? "<b class='text-primary'>"+response[x].CODHORARIO+"</b>" : response[x].CODHORARIO)+'</td>';
                            html += '<td class="text-center bg_dados">'+response[x].CODINDICE+'</td>';
                            html += '<td class="text-center bg_dados">'+response[x].DIA+'</td>';
                            html += '<td class="text-center '+indicador+'" '+title+' data-entrada="'+x+'" data-minutos="'+response[x].ENTRADA1_MINUTO+'">'+response[x].ENTRADA1+'</td>';
                            html += '<td class="text-center bg_ref">'+response[x].SAIDA1+'</td>';
                            html += '<td class="text-center bg_ref">'+response[x].ENTRADA2+'</td>';
                            html += '<td class="text-center '+indicador+'" '+title+' data-saida="'+x+'" data-minutos="'+response[x].SAIDA2_MINUTO+'">'+response[x].SAIDA2+'</td>';
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
const projecaoDiaFolga = (dados) => {

    $("#table_projecao_escala_folga tbody").html('');
    $("#box_projecao_folga").fadeIn(100);

    $.ajax({
        url: "<?= base_url('ponto/escala/action/carrega_escala_dia'); ?>",
        type:'POST',
        data:{
            'id'            : <?= $resEscala['id'] ?>,
            'tipo'          : 'folga',
            'data'          : dados.data,
            'chapa'         : dados.chapa,
            'codhorario'    : dados.codhorario,
            'indice'        : dados.indice,
        },
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

                        if(response[x].SAIDA1_MINUTO < response[x].ENTRADA1_MINUTO){response[x].SAIDA1_MINUTO = response[x].SAIDA1_MINUTO + 1440; response[x].SAIDA1 = '+'+response[x].SAIDA1;}
                        if(response[x].ENTRADA2_MINUTO < response[x].SAIDA1_MINUTO){response[x].ENTRADA2_MINUTO = response[x].ENTRADA2_MINUTO + 1440; response[x].ENTRADA2 = '+'+response[x].ENTRADA2;}
                        if(response[x].SAIDA2_MINUTO < response[x].ENTRADA2_MINUTO){response[x].SAIDA2_MINUTO = response[x].SAIDA2_MINUTO + 1440; response[x].SAIDA2 = '+'+response[x].SAIDA2;}

                        var html = "";
                            html += '<tr>';
                            html += '<td class="text-center bg_dados" data-data="'+x+'" data-date="'+response[x].DATA+'">'+((response[x].DATAEN == dados.data) ? "<b class='text-danger'>"+response[x].DATA+"</b>" : response[x].DATA)+'</td>';
                            html += '<td class="text-center bg_dados">'+((response[x].CODHORARIO == dados.codhorario) ? "<b class='text-primary'>"+response[x].CODHORARIO+"</b>" : response[x].CODHORARIO)+'</td>';
                            html += '<td class="text-center bg_dados">'+response[x].CODINDICE+'</td>';
                            html += '<td class="text-center bg_dados">'+response[x].DIA+'</td>';
                            html += '<td class="text-center '+indicador+'" '+title+' data-entrada="'+x+'" data-minutos="'+response[x].ENTRADA1_MINUTO+'">'+response[x].ENTRADA1+'</td>';
                            html += '<td class="text-center bg_ref">'+response[x].SAIDA1+'</td>';
                            html += '<td class="text-center bg_ref">'+response[x].ENTRADA2+'</td>';
                            html += '<td class="text-center '+indicador+'" '+title+' data-saida="'+x+'" data-minutos="'+response[x].SAIDA2_MINUTO+'">'+response[x].SAIDA2+'</td>';
                            html += '</tr>';
                            $("#table_projecao_escala_folga tbody").append(html);
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
<?php if($resEscala['situacao'] == 0 || $resEscala['situacao'] == 8): ?>
const enviaParaAprovacao = () => {
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
                    'id': '<?= $resEscala['id']; ?>'
                },
                success:function(result){
                    
                    var response = JSON.parse(result);

                    if(response.tipo != 'success'){
                        exibeAlerta(response.tipo, response.msg);
                    }else{
                        exibeAlerta(response.tipo, response.msg, 3, '/ponto/escala');
                    }
                    
                },
            });
        }
    });
}
precisa_justificar_11_horas = false;
precisa_justificar_6_dias = false;
precisa_justificar_6_meses = false;
precisa_justificar_3_dias = false;
projetado = false;
$('#data, #data_folga, #indice, #indice_folga').on('change', function(){
    projetado = false;
    $("#botao_salvar").prop('disabled', true);
    $("#box_projecao, #box_projecao_folga").fadeOut(100);
});
const salvaDados = () => {

    if(projetado == false){
        exibeAlerta('error', 'É necessário projetar a escala para salvar.');
        return;
    }

    let dados = {
        "chapa"               : '<?= $resEscala['chapa']; ?>',
        "data"                : fixData($("#data").val()),
        "codhorario"          : '<?= $resEscala['codhorario']; ?>',
        "indice"              : $("#indice").val()
    }
    let dadosFolga = {
        "chapa"         : '<?= $resEscala['chapa']; ?>',
        "data"          : fixData($("#data_folga").val()),
        "codhorario"    : '<?= $resEscala['codhorario']; ?>',
        "indice"        : $("#indice_folga").val()
    }

    if(dados.chapa == ""){ exibeAlerta("error", "<b>Colaborador</b> não selecionado."); return false; }
    if(dados.data == ""){ exibeAlerta("error", "<b>Data (Dia Trabalho)</b> não informada."); return false; }
    if(dados.codhorario == ""){ exibeAlerta("error", "<b>Horário</b> dia trabalho não informado."); return false; }
    if(dados.indice == ""){ exibeAlerta("error", "<b>Índice</b> dia trabalho não selecionado."); return false; }
    if(dadosFolga.data == ""){ exibeAlerta("error", "<b>Data (Dia Folga)</b> não informada."); return false; }
    if(dadosFolga.codhorario == ""){ exibeAlerta("error", "<b>Horário</b> dia folga não informado."); return false; }
    if(dadosFolga.indice == ""){ exibeAlerta("error", "<b>Índice</b> dia folga não selecionado."); return false; }
    if(dados.codhorario != dadosFolga.codhorario){ exibeAlerta("error", "<b>Horário</b> dia trabalho não pode ser diferente do horário dia folga."); return false; }

    if(
        (dados.data < '<?= dtEn($resEscala['config_dia_per_ini'], true) ?>' || dados.data > '<?= dtEn($resEscala['config_dia_per_fim'], true) ?>') || 
        (dadosFolga.data < '<?= dtEn($resEscala['config_dia_per_ini'], true) ?>' || dadosFolga.data > '<?= dtEn($resEscala['config_dia_per_fim'], true) ?>')){
        $(".box_justificativa_periodo").fadeIn(0);
        let justificativa_periodo = $("#justificativa_periodo").val();
        if(justificativa_periodo == ""){ exibeAlerta("error", "Justificativa não informada."); return false; }
    }else{
        $(".box_justificativa_periodo").fadeOut(0);
        $("#justificativa_periodo").val('');
    }

    if(!validaData()){
        return;
    }

    

    //----------------------------------------- (Dia Útil) ------
    var saida2 = parseInt($("#box_projecao [data-saida='9']").attr("data-minutos"));
    var saida = parseInt($("#box_projecao [data-saida='9']").attr("data-minutos")) + 660;
    var entrada = parseInt($("#box_projecao [data-entrada='10']").attr("data-minutos"));
    var tipo = $("#box_projecao [data-entrada='10']").attr("title");
    var tipo2 = $("#box_projecao [data-entrada='9']").attr("title");
    var justificativa_11_horas = $("#justificativa_11_horas").val();
    var justificativa_6_dias = $("#justificativa_6_dias").val();
    var justificativa_6_meses = $("#justificativa_6_meses").val();
    var justificativa_3_dias = $("#justificativa_3_dias").val();
    if(saida2 <= 0) saida2 = 1440;

    if(saida > 1440) saida = saida - 1440;

    var horas_saida = 1440 - saida2;
    var horas_entrada = entrada;
    var diff_total = horas_saida + horas_entrada;

    var [dia, mes, ano] = $("#data").val().split('/');
    var dataFormatada_util = `${ano}-${mes}-${dia}`;
    var inputData_util = new Date(dataFormatada_util);
    var dataAtual_util = new Date();
    var tresDiasDepois_util = new Date();
    tresDiasDepois_util.setDate(dataAtual_util.getDate() + 2);

    if(tipo === undefined && tipo2 === undefined && !precisa_justificar_11_horas){
        // if(saida > entrada){exibeAlerta('error', 'Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário.');return false;}
        if(diff_total < 660){
            <?php if(($resConfiguracao['bloqueio_aviso'] ?? null) != 1): ?>
            $(".box_justificativa_11_horas").fadeIn(100);
            precisa_justificar_11_horas = true;
            <?php endif; ?>
            exibeAlerta('warning', '<b>Dia Trabalho:</b> Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário.');
            return false;
        }
    }

    if(!justificativa_3_dias && inputData_util < tresDiasDepois_util){
        <?php if(($resConfiguracao[0]['bloqueio_aviso'] ?? null) != 1): ?>
            $(".box_justificativa_3_dias").fadeIn(100);
            precisa_justificar_3_dias = true;
            <?php endif; ?>
            exibeAlerta('warning', 'Troca de escala dentro de 3 dias.');
        return false;
    }


    var qtde_dias_trab = 0;
    // verifica 35 horas de descanço nos ultimos 6 dias
    for(var ini = 9; ini >= 0; ini--){

        var isTrabalhado = true;
        var isDescanso = $("#box_projecao [data-entrada='"+ini+"']").attr("title");

        if(isDescanso === undefined){
            qtde_dias_trab++;
            var horaEntrada = parseInt($("#box_projecao [data-entrada='"+ini+"']").attr("data-minutos"));
            var horaSaida = parseInt($("#box_projecao [data-saida='"+ini+"']").attr("data-minutos"));
            var dataDia = $("#box_projecao [data-data='"+ini+"']").attr("data-date");
        }else{
            break;
        }
        
    }
    // pega a entrada do novo horário
    for(var ini = 10; ini <= 19; ini++){

        var isTrabalhado = true;
        var isDescanso = $("#box_projecao [data-entrada='"+ini+"']").attr("title");

        if(isDescanso === undefined){
            qtde_dias_trab++;
            var horaEntrada2 = parseInt($("#box_projecao [data-entrada='"+ini+"']").attr("data-minutos"));
            var horaSaida2 = parseInt($("#box_projecao [data-saida='"+ini+"']").attr("data-minutos"));
            var dataDia2 = $("#box_projecao [data-data='"+ini+"']").attr("data-date");
        }else{
            break;
        }
    }

    if(qtde_dias_trab > 6 && !precisa_justificar_6_dias){
        <?php if(($resConfiguracao['bloqueio_aviso'] ?? null) != 1): ?>
        $(".box_justificativa_6_dias").fadeIn(100);
        precisa_justificar_6_dias = true;
        <?php endif; ?>
        exibeAlerta('warning', '<b>Dia Trabalho:</b> Colaborador possui mais de 6 dias trabalhados consecutivos.');
        return false;
    }

    //----------------------------------------- (Dia Folga) ------
    var saida2 = parseInt($("#box_projecao_folga [data-saida='9']").attr("data-minutos"));
    var saida = parseInt($("#box_projecao_folga [data-saida='9']").attr("data-minutos")) + 660;
    var entrada = parseInt($("#box_projecao_folga [data-entrada='10']").attr("data-minutos"));
    var tipo = $("#box_projecao_folga [data-entrada='10']").attr("title");
    var tipo2 = $("#box_projecao_folga [data-entrada='9']").attr("title");
    if(saida2 <= 0) saida2 = 1440;

    if(saida > 1440) saida = saida - 1440;

    var horas_saida = 1440 - saida2;
    var horas_entrada = entrada;
    var diff_total = horas_saida + horas_entrada;

    var [dia, mes, ano] = $("#data_folga").val().split('/');
    var dataFormatada_folga = `${ano}-${mes}-${dia}`;

    //Verificação troca dentro de tres dias
    var inputData = new Date(dataFormatada_folga);
    var dataAtual = new Date();
    var tresDiasDepois = new Date();
    tresDiasDepois.setDate(dataAtual.getDate() + 2);

    if(tipo === undefined && tipo2 === undefined && !precisa_justificar_11_horas){
        // if(saida > entrada){exibeAlerta('error', 'Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário.');return false;}
        if(diff_total < 660){
            <?php if(($resConfiguracao['bloqueio_aviso'] ?? null) != 1): ?>
            $(".box_justificativa_11_horas").fadeIn(100);
            precisa_justificar_11_horas = true;
            <?php endif; ?>
            exibeAlerta('warning', '<b>Dia Folga:</b> Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário.');
            return false;
        }
    }

    if(!precisa_justificar_3_dias && inputData < tresDiasDepois){
        <?php if(($resConfiguracao[0]['bloqueio_aviso'] ?? null) != 1): ?>
            $(".box_justificativa_3_dias").fadeIn(100);
            precisa_justificar_3_dias = true;
            <?php endif; ?>
            exibeAlerta('warning', 'Troca de escala dentro de 3 dias.');
        return false;
    }


    var qtde_dias_trab = 0;
    // verifica 35 horas de descanço nos ultimos 6 dias
    for(var ini = 9; ini >= 0; ini--){

        var isTrabalhado = true;
        var isDescanso = $("#box_projecao_folga [data-entrada='"+ini+"']").attr("title");

        if(isDescanso === undefined){
            qtde_dias_trab++;
            var horaEntrada = parseInt($("#box_projecao_folga [data-entrada='"+ini+"']").attr("data-minutos"));
            var horaSaida = parseInt($("#box_projecao_folga [data-saida='"+ini+"']").attr("data-minutos"));
            var dataDia = $("#box_projecao_folga [data-data='"+ini+"']").attr("data-date");
        }else{
            break;
        }
        
    }
    // pega a entrada do novo horário
    for(var ini = 10; ini <= 19; ini++){

        var isTrabalhado = true;
        var isDescanso = $("#box_projecao_folga [data-entrada='"+ini+"']").attr("title");

        if(isDescanso === undefined){
            qtde_dias_trab++;
            var horaEntrada2 = parseInt($("#box_projecao_folga [data-entrada='"+ini+"']").attr("data-minutos"));
            var horaSaida2 = parseInt($("#box_projecao_folga [data-saida='"+ini+"']").attr("data-minutos"));
            var dataDia2 = $("#box_projecao_folga [data-data='"+ini+"']").attr("data-date");
        }else{
            break;
        }
    }

    if(qtde_dias_trab > 6 && !precisa_justificar_6_dias){
        <?php if(($resConfiguracao['bloqueio_aviso'] ?? null) != 1): ?>
        $(".box_justificativa_6_dias").fadeIn(100);
        precisa_justificar_6_dias = true;
        <?php endif; ?>
        exibeAlerta('warning', '<b>Dia Folga:</b> Colaborador possui mais de 6 dias trabalhados consecutivos.');
        return false;
    }

    <?php if(($resConfiguracao['bloqueio_aviso'] ?? null) != 1): ?>
    if(justificativa_11_horas == "" && precisa_justificar_11_horas){
        exibeAlerta('warning', 'Justificativa (Descanso de interjornada mínima de 11h não respeitada entre a saída do horário anterior X entrada do novo horário) não informada.');
        return false;
    }

    if(justificativa_6_dias == "" && precisa_justificar_6_dias){
        exibeAlerta('warning', 'Justificativa (Colaborador possui mais de 6 dias trabalhados consecutivos.');
        return false;
    }

    if(justificativa_3_dias == "" && precisa_justificar_3_dias){
        exibeAlerta('warning', 'Justificativa (Alteração dentro de 72 horas.)');
        return false;
    }

    if(justificativa_6_meses == "" && precisa_justificar_6_meses){
        exibeAlerta('warning', 'Justificativa (Troca de escala inferior a 6 meses do horário atual do colaborador) não informada.');
        return false;
    }
    <?php endif; ?>

    Swal.fire({
        icon              : 'question',
        title             : 'Confirmar alteração da troca de dia?',
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

            let dados = {
                "chapa"                     : '<?= $resEscala['chapa']; ?>',
                "data"                      : fixData($("#data").val()),
                "data_folga"                : fixData($("#data_folga").val()),
                "codhorario"                : '<?= $resEscala['codhorario']; ?>',
                "indice"                    : $("#indice").val(),
                "indice_folga"              : $("#indice_folga").val(),
                "dtmudanca_historico"       : "<?= date('Y-m-d', strtotime($dadosFunc['DTMUDANCA_HORARIO'] ?? null)); ?>",
                "codhorario_historico"      : "<?= $dadosFunc['CODHORARIO'] ?? null; ?>",
                "horaEntrada"               : horaEntrada,
                "horaSaida"                 : horaSaida,
                "dataDia"                   : dataDia,
                "horaEntrada2"              : horaEntrada2,
                "horaSaida2"                : horaSaida2,
                "dataDia2"                  : dataDia2,
                "justificativa_11_horas"    : justificativa_11_horas,
                "justificativa_6_dias"      : justificativa_6_dias,
                "justificativa_6_meses"     : justificativa_6_meses,
                "justificativa_3_dias"      : justificativa_3_dias,
                "tipo"                      : 2,
                "id"                        : "<?= $resEscala['id']; ?>",
            }

            $.ajax({
                url: "<?= base_url('ponto/escala/action/cadastrar') ?>",
                type:'POST',
                data:dados,
                success:function(result){
                    
                    var response = JSON.parse(result);

                    if(response.tipo != 'success'){
                        exibeAlerta(response.tipo, response.msg);
                        <?php if(($resConfiguracao['bloqueio_aviso'] ?? null) != 1): ?>
                        if(response.cod == 1){
                            precisa_justificar_6_meses = true;
                            $(".box_justificativa_6_meses").fadeIn(100);
                        }
                        <?php endif; ?>

                    }else{
                        exibeAlerta(response.tipo, response.msg, 3, window.location.href);
                    }
                    
                },
            });

        }
    });

}
<?php endif; ?>
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
    background-color: #497444;
    color: #ffffff;
}
.bg_folga {
    background-color: #f9cd25;
}
.bg_feriado {
    background-color: #58595b;
    color: #ffffff;
}
.bg_ref {
    background-color: #dcddde;
}
.highlight {
    background: #ededed;
    color: #038f5c !important;
}
.datepicker table tr td.day.focused, .datepicker table tr td.day:hover:not(.disabled), .active.day.highlight {
    background: #038f5c !important;
    color: #ffffff !important;
}
.day.disabled {
    background: #ffffff !important;
    color: #cccccc !important;
}
</style>
<?php
loadPlugin(array('select2','dropify', 'datepicker', 'maxlength'));
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
                            exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/escala'); ?>');
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

const verificaData = () => {
    openLoading();
    $.ajax({
        url: "<?= base_url('ponto/escala/action/verifica_data') ?>",
        type:'POST',
        data:{
            'chapa'   : '<?= $resEscala['chapa']; ?>',
            'data'    : fixData($("#data").val()),
            'id'      : '<?= $resEscala['id']; ?>'
        },
        success:function(result){
            openLoading(true);
            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta('error', response.msg);
                erro_data = true;
                $("#data").val('');
            }else{
                erro_data = false;
            }
            
        },
    });
}
const verificaDataFolga = () => {
    openLoading();
    $.ajax({
        url: "<?= base_url('ponto/escala/action/verifica_data') ?>",
        type:'POST',
        data:{
            'chapa'   : '<?= $resEscala['chapa']; ?>',
            'data'    : fixData($("#data_folga").val()),
            'id'      : '<?= $resEscala['id']; ?>'
        },
        success:function(result){
            openLoading(true);
            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta('error', response.msg);
                erro_data = true;
                $("#data_folga").val('');
            }else{
                erro_data = false;
            }
            
        },
    });
}
const fixData = (data) => {
    try{
    
        data = data.split('/');
        data = data[2]+'-'+data[1]+'-'+data[0];

        return data;

    }catch(e){
        return '';
    }
}

$(document).ready(function(){
    <?php
    $datasTrabalho    = [];
    $datasFolga       = [];

    if($resProjecao){
        foreach($resProjecao as $projecao){
            switch($projecao['TIPO']){
                case 'TRABALHO'   : $datasTrabalho[] = '"'.$projecao['DATA'].'"'; break;
                default           : $datasFolga[] = '"'.$projecao['DATA'].'"'; break;
            };
        }
    }
    ?>
    const datasPermitidasTrabalho   = [<?= (count($datasTrabalho) > 0) ? implode(',', $datasTrabalho) : ''; ?>];
    const datasPermitidasFolga      = [<?= (count($datasFolga) > 0) ? implode(',', $datasFolga) : ''; ?>];

    $('textarea').maxlength({
        alwaysShow          : true,
        warningClass        : "badge badge-success",
        limitReachedClass   : "badge badge-warning"
    });

    $('#data').datepicker({
        format    : "dd/mm/yyyy",
        autoclose : true,
        beforeShowDay: function (date) {
            
            var dia = 
                ("0" + date.getDate()).slice(-2) + "/" +
                ("0" + (date.getMonth() + 1)).slice(-2) + "/" +
                date.getFullYear();
                
            if (datasPermitidasFolga.includes(dia)) {
                return { enabled: true, classes: 'highlight' };
            } else {
                return false;
            }
        }

    });
    
    $('#data_folga').datepicker({
        format    : "dd/mm/yyyy",
        autoclose : true,
        beforeShowDay: function (date) {
            
            var dia = 
                ("0" + date.getDate()).slice(-2) + "/" +
                ("0" + (date.getMonth() + 1)).slice(-2) + "/" +
                date.getFullYear();
                
            if (datasPermitidasTrabalho.includes(dia)) {
                return { enabled: true, classes: 'highlight' };
            } else {
                return false;
            }
        }

    });
});
</script>