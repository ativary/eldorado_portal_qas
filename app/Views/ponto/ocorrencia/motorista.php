<?= menuConfigPonto('Cargos'); ?>

<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
				<div class="card-header mt-0">
					<div class="row">
                        <div class="col-sm-10">
						    <h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-briefcase-edit-outline"></i> <?= $_titulo; ?></h4>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" onclick="$('#modalCarga').modal();" class="btn btn-primary btn-xs btn-block bteldorado_7"><i class="fa fa-recycle"></i> Cargas ATS</button>
                        </div>
					</div>
				</div>
				
				<div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="form-group row p-0 mb-2">
						<label for="add_funcao" class="col-sm-2 col-form-label text-right">Adicionar Função:</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-sm select2" name="codfuncao" id="codfuncao">
                                <option value="">...</option>
                                <?php if($resFuncao): ?>
                                    <?php foreach($resFuncao as $Funcao): ?>
                                        <option value="<?= $Funcao['CODIGO']; ?>"><?= $Funcao['NOME'].' - '.$Funcao['CODIGO']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" onclick="return addfuncao()" class="btn btn-primary btn-xxs btn-block bteldorado_7"><i class="mdi mdi-subdirectory-arrow-right"></i> adicionar</button>
                        </div>
                    </div>

					<table id="datatable" class="table table-sm table-bordered table-hover dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr>
								<th width="250">Função</th>
                                
								<!-- <th class="text-center">Intervalo Obrigatório</th>
								<th class="text-center">Intervalo Fracionado</th>
								<th class="text-center">Intervalo planejado (Início)</th>
								<th class="text-center">Intervalo planejado (Término)</th>
                                <th class="text-center">Intervalo (Total)</th> -->
								
								<th class="text-center">Horas previstas</th>
								<th class="text-center">Limite extra</th>
								<th class="text-center">Limite jornada dia</th>
								<th class="text-center">Excesso semanal</th>
								<th class="text-center">Ação</th>
							</tr>
						</thead>
						<tbody>
                            <?php if($resMotorista): ?>
                                <?php foreach($resMotorista as $Motorista): ?>
                                    <?php $nomeFuncao = extrai_valor($resFuncao, $Motorista->codfuncao, 'CODIGO', 'NOME'); ?>
                                    <tr>
                                        <td><?= $nomeFuncao.' - '.$Motorista->codfuncao; ?></td>
                                        <!-- <td>
                                            <div class="checkbox checkbox-primary checkbox-single text-center">
                                                <input <?= ($Motorista->intervalo_obrigatorio == 1) ? ' checked ' : ''; ?> type="checkbox" data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field-<?= $Motorista->codfuncao; ?>-intervalo_obrigatorio onclick="marcaCheckbox('<?=$Motorista->codfuncao; ?>')" aria-label="Intervalo obrigatório" title="Intervalo obrigatório">
                                               <label></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox checkbox-primary checkbox-single text-center">
                                            <input <?= ($Motorista->intervalo_fracionado == 1) ? ' checked ' : ''; ?> type="checkbox" data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field-<?= $Motorista->codfuncao; ?>-intervalo_fracionado onclick="marcaCheckbox('<?=$Motorista->codfuncao; ?>')" aria-label="Intervalo fracionado" title="Intervalo fracionado">
                                            <label></label>
                                            </div>
                                        </td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="intervalo_planejado_ini" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->intervalo_planejado_ini,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="intervalo_planejado_fim" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->intervalo_planejado_fim,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="intervalo_total" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->intervalo_total,4); ?>"></td> -->
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="horas_prevista" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->horas_prevista,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="limite_extra" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->limite_extra,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="limite_jornada" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->limite_jornada,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="excesso_semanal" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->excesso_semanal,4); ?>"></td>
                                        <td class="text-center"><button type="button" onclick="return delfuncao('<?= $Motorista->codfuncao; ?>', '<?= $nomeFuncao; ?>')" class="btn btn-danger btn-xxs"><i class="fa fa-times"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
						</tbody>
					</table>

                </div>
			</div><!-- end card -->
		</div><!-- end col-12 -->
		
	</div><!-- end row -->
</div><!-- end container -->

<!-- modal -->
<div class="modal" id="modalCarga" tabindex="1" role="dialog" aria-labelledby="modalCarga" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="modalCargalabel"><span class="oi oi-people"></span> Carga retroativa de dados via API do ATS </h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body">

                <div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addData" style="width: 128px;">Data Inicial: </label>
					</div>
					<input class="form-control datepicker m_data" type="date" name="data_inicial" id="data_inicial">
				</div>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addData" style="width: 128px;">Data Final: </label>
					</div>
					<input class="form-control datepicker m_data" type="date" name="data_final" id="data_final">
				</div>

                <div class="form-control mb-3">
                    <div class="checkbox checkbox-primary">
                        <input id="proc_macros" type="checkbox" checked >
                        <label for="proc_macros">Processar Macros</label>
                    </div>
                </div>

                <div class="form-control mb-3">
                    <div class="checkbox checkbox-primary">
                        <input id="proc_tots" type="checkbox" checked >
                        <label for="proc_tots">Processar Totalizadores</label>
                    </div>
                </div>

				<div class="form-control mb-3">
                    <div class="checkbox checkbox-primary">
                        <input id="apagar_dados" type="checkbox" checked >
                        <label for="apagar_dados">Apagar dados do período antes de processar</label>
                    </div>
                </div>

                <div id="proc_box" class="form-control mb-3" style="display: none;">
                    <div class="text-center">
                        <label id="proc_dia" style="color:midnightblue">Processando dia ...</label>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="ProcessarCargas();"> <i class="fa fa-check"></i> Processar </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
			</div>
		</div>
	</div>


</div>

<!-- modal -->


<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 25,
        "aaSorting"         : [[0, "desc"]],
    });
});
$('.horas').on('focus', function(){
    $(this).mask('99:99');
});
$('.horas').on('blur', function(){

    openLoading();

    var codfuncao = $(this).attr('data-codfuncao');
    var valor = $(this).val();
    var datafield =  $(this).attr('data-field');

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/salvar_param_motorista'); ?>",
        type: 'POST',
        data: {
            'codfuncao':codfuncao,
            'valor':valor,
            'datafield':datafield,
        },
        success:function(result){
            
			openLoading(true);
            var response = JSON.parse(result);
            if(response.tipo == 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg);
            }
            
        },
    });
});

const marcaCheckbox = (codFuncao) => {

openLoading();

console.log(codFuncao);

var obrigatorio = ($("[data-field-"+codFuncao+"-intervalo_obrigatorio]").prop('checked')) ? 1 : 0;
var fracionado = ($("[data-field-"+codFuncao+"-intervalo_fracionado]").prop('checked')) ? 1 : 0;

console.log(obrigatorio);
console.log(fracionado);

$.ajax({
    url: "<?= base_url('ponto/ocorrencia/action/config_motorista_check'); ?>",
    type: 'POST',
    data: {
        'codfuncao'     : codFuncao,
        'obrigatorio'   : obrigatorio,
        'fracionado'    : fracionado
     
    },
    success:function(result){
        console.log(result);
        
        openLoading(true);
        var response = JSON.parse(result);
        //exibeAlerta(response.tipo, response.msg, 3);
        
    },
});

}


const addfuncao = () => {
    var codFuncao = $("#codfuncao").val();
    if(codFuncao == ''){exibeAlerta('error', '<b>Função</b> não selecionada.'); return false;}
    
    openLoading();

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/config_motorista'); ?>",
        type: 'POST',
        data: {'codfuncao':codFuncao},
        success:function(result){
            
			openLoading(true);
            var response = JSON.parse(result);
            if(response.tipo == 'success'){
                exibeAlerta(response.tipo, response.msg, 2, window.location.href);
            }else{
                exibeAlerta(response.tipo, response.msg);
            }
            
        },
    });
}

const delfuncao = (codfuncao, nomefuncao) => {

    Swal.fire({
        icon: 'question',
        title: 'Confirma a exclusão da função: <br>'+nomefuncao+' - '+codfuncao+'?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim, excluir`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {

            openLoading();
            
            $.ajax({
                url: "<?= base_url('ponto/ocorrencia/action/excluir_motorista'); ?>",
                type: 'POST',
                data: {'codfuncao':codfuncao},
                success:function(result){
                    
                    openLoading(true);
                    var response = JSON.parse(result);
                    if(response.tipo == 'success'){
                        exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    }else{
                        exibeAlerta(response.tipo, response.msg);
                    }
                    
                },
            });

        }
    });

}

const ProcessarCargas = () => {

    let dados = {
        "data_inicial": $("#data_inicial").val(),
        "data_final": $("#data_final").val(),
        "proc_macros": $("#proc_macros").is(':checked') ? 'S' : 'N',
        "proc_tots": $("#proc_tots").is(':checked') ? 'S' : 'N',
        "apagar_dados": $("#apagar_dados").is(':checked') ? 'S' : 'N',
    }

    console.log(dados.data_inicial);
    console.log(dados.data_final);
    console.log(dados.proc_macros);
    console.log(dados.proc_tots);
    console.log(dados.apagar_dados);
    
    if(dados.data_inicial == ""){ exibeAlerta("error", "<b>Data Inicial</b> não informado."); return false; }
    if(dados.data_final == ""){ exibeAlerta("error", "<b>Data Final</b> não informado."); return false; }
    if(dados.data_final < dados.data_inicial){ exibeAlerta("error", "<b>Período inválido</b>."); return false; }
    if(dados.proc_macros == "" && dados.proc_tots == ""){ exibeAlerta("error", "Selecione um grupo para processar, macros, totalizadores ou ambos."); return false; }
    
    const d1 = new Date(dados.data_inicial);
    const d2 = new Date(dados.data_final);
    let dif = (d2 - d1) / (1000 * 60 * 60 * 24); 
    console.log(dif);
    if(dif > 6){ exibeAlerta("error", "Período não pode ser superior a 7 dias."); return false; }
    if(dif < 1){ exibeAlerta("error", "Período não pode ser inferior a 2 dias."); return false; }
    console.log(dif);

    Swal.fire({
        icon: 'question',
        title: 'Confirma o processamento das cargas ATS desse período?',
        showDenyButton: true,
        showCancelButton: true,
        denyButtonText: `Cancelar`,
        confirmButtonText: `Sim, processar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {

            let currentDate = new Date(d1);
            let avisos = '';
            $("#proc_box").css({ display: "block" });
            $("#proc_dia").html('Processando cargas ATS...');
              
            openLoading();
            $.ajax({
                url: "<?= base_url('ponto/ats/processa_carga/'); ?>",
                type: 'POST',
                data: dados,
                success:function(result){
                    var response = JSON.parse(result);
                    if(response.tipo == 'success'){
                        $("#proc_box").hide();
                        exibeAlerta(response.tipo, response.msg);
                        return (true);
                    }else{
                        $("#proc_box").hide();
                        exibeAlerta(response.tipo, response.msg);
                        return (false);
                    }     
                },
            });

        }
    });

}

</script>
<?php loadPlugin(array('select2', 'datatable', 'mask')); ?>