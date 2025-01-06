<?php $isAdmin = ($_SESSION['log_id'] == 1) ? true : false; ?>
<div class="container-fluid"> 
    <div class="row">

		<!-- main -->
        <div class="col-12">
            <div class="card">
					
				<div class="card-header mt-0">
					<div class="row">
					<h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-email-check"></i> Configuração de envio do workflow</h4>
					</div>
				</div>
				
				<div class="card-body">

					
					<div class="form-group row p-0 m-0">
						<label for="data1" class="col-sm-2 col-form-label text-right">Data Envio W1:</label>
                        <div class="col-sm-2">
                            <input class="form-control form-control-sm" type="date" value="<?= ($resWorkflow[0]->data1 ?? false) ? dtEn($resWorkflow[0]->data1,true) : ''; ?>" name="data1" id="data1" require>
                        </div>
                        <label for="ciclo1" class="col-sm-2 col-form-label text-right">Ciclo de notificação W1:</label>
                        <div class="col-sm-2">
							<div class="input-group">
								<input class="form-control form-control-sm" type="number" value="<?= $resWorkflow[0]->ciclo1 ?? ''; ?>" name="ciclo1" id="ciclo1" require>
								<div class="input-group-append">
									<span class="input-group-text">Dia(s)</span>
								</div>
							</div>
                        </div>
						<label for="horario1" class="col-sm-2 col-form-label text-right">Horário 1:</label>
                        <div class="col-sm-2">
                            <input class="form-control form-control-sm" type="time" value="<?= ($resWorkflow[0]->horario1 ?? false) ? m2h($resWorkflow[0]->horario1,4) : ''; ?>" name="horario1" id="horario1" require>
                        </div>
                    </div>
                    <div class="form-group row p-0 m-0">
						<label for="data2" class="col-sm-2 col-form-label text-right">Data Envio W2:</label>
                        <div class="col-sm-2">
                            <input class="form-control form-control-sm" type="date" value="<?= ($resWorkflow[0]->data2 ?? false) ? dtEn($resWorkflow[0]->data2,true) : ''; ?>" name="data2" id="data2" require>
                        </div>
                        <label for="ciclo2" class="col-sm-2 col-form-label text-right">Ciclo de notificação W2:</label>
						<div class="col-sm-2">
                            <div class="input-group">
								<input class="form-control form-control-sm" type="number" value="<?= $resWorkflow[0]->ciclo2 ?? ''; ?>" name="ciclo2" id="ciclo2" require>
								<div class="input-group-append">
									<span class="input-group-text">Dia(s)</span>
								</div>
							</div>
                        </div>
                        <label for="horario2" class="col-sm-2 col-form-label text-right">Horário 2:</label>
                        <div class="col-sm-2">
                            <input class="form-control form-control-sm" type="time" value="<?= ($resWorkflow[0]->horario2 ?? false) ? m2h($resWorkflow[0]->horario2,4) : ''; ?>" name="horario2" id="horario2" require>
                        </div>
                    </div>
				</div>
				<div class="card-footer text-center">
					<button type="button" class="btn btn-xxs btn-primary" onclick="return salvaConfigWorkflow()">Salvar <i class="fas fa-check"></i></button>
				</div>
			</div>
		</div>

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
				<div class="card-header mt-0">
					<div class="row">
						<h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-clock-alert-outline"></i> <?= $_titulo; ?></h4>
					</div>
				</div>
				
				<div class="card-body">

                    <?= exibeMensagem(true); ?>

					<table id="datatable" class="table table-sm table-bordered table-hover dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr>
								<th class="text-center" width="70">Cód. Horário</th>
								<th>Horário</th>
								<th class="text-center">Intervalo Obrigatório</th>
								<th class="text-center">Intervalo Fracionado</th>
								<th class="text-center">Intervalo planejado (Início)</th>
								<th class="text-center">Intervalo planejado (Término)</th>
								<th class="text-center">Horas previstas feriado (integral)</th>
								<th class="text-center">Horas previstas feriado (parcial)</th>
								<th class="text-center">Considera escala especial</th>
								<th class="text-center">Tipo Horário</th>
								<th class="text-center">Ação</th>
							</tr>
						</thead>
						<tbody>
							<?php if($resHorario): ?>
							<?php foreach($resHorario as $Horario): ?>
								<tr>
									<td class="text-center"><?= $Horario['CODIGO']; ?></td>
									<td><?= $Horario['DESCRICAO']; ?></td>
									<td class="text-center">
										<div class="checkbox checkbox-primary checkbox-single">
											<input <?= ($Horario['intervalo_obrigatorio'] == 1) ? ' checked ' : ''; ?> type="checkbox" data-intervalo_obrigatorio="<?= $Horario['CODIGO']; ?>" onclick="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'intervalo_obrigatorio')" aria-label="Intervalo obrigatório" title="Intervalo obrigatório">
											<label></label>
										</div>
									</td>
									<td class="text-center">
										<div class="checkbox checkbox-primary checkbox-single">
											<input <?= ($Horario['intervalo_fracionado'] == 1) ? ' checked ' : ''; ?> type="checkbox" data-intervalo_fracionado="<?= $Horario['CODIGO']; ?>" onclick="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'intervalo_fracionado')" aria-label="Intervalo Fracionado" title="Intervalo Fracionado">
											<label></label>
										</div>
									</td>
									<td class="text-center">
										<input type="time" <?= ($Horario['intervalo_obrigatorio'] == 1) ?  '' : 'readonly'; ?> <?= ($Horario['tipo_horario'] == 'C') ?  '' : 'readonly'; ?> name="planejado_inicio" data-planejado_inicio="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['planejado_inicio'],4); ?>" class="form-control form-control-sm" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'planejado_inicio')">
									</td>
									<td class="text-center">
										<input type="time" <?= ($Horario['intervalo_obrigatorio'] == 1) ?  '' : 'readonly'; ?> <?= ($Horario['tipo_horario'] == 'C') ?  '' : 'readonly'; ?> name="planejado_termino" data-planejado_termino="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['planejado_termino'],4); ?>" class="form-control form-control-sm" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'planejado_termino')">
									</td>
									<td class="text-center">
										<input type="time" name="extra_feriado" data-extra_feriado="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['extra_feriado'],4); ?>" class="form-control form-control-sm" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'extra_feriado')">
									</td>
									<td class="text-center">
										<input type="time" name="extra_feriado_parcial" data-extra_feriado_parcial="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['extra_feriado_parcial'],4); ?>" class="form-control form-control-sm" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'extra_feriado_parcial')">
									</td>
									<td class="text-center">
										<div class="checkbox checkbox-primary checkbox-single">
											<input <?= ($Horario['escala_especial'] == 1) ? ' checked ' : ''; ?> type="checkbox" data-escala_especial="<?= $Horario['CODIGO']; ?>" onclick="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'escala_especial')" aria-label="Considera escala especial" title="Considera escala especial">
											<label></label>
										</div>
									</td>
									<td class="text-center">
										<select class="form-control" data-tipo_horario="<?= $Horario['CODIGO']; ?>" name="tipo_horario" id="tipo_horario" onchange="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'tipo_horario')">
											<option value="">-nenhum-</option>
											<option value="C" <?= ($Horario['tipo_horario'] == 'C') ? ' selected ' : ''; ?>>Ciclo</option>
											<option value="S" <?= ($Horario['tipo_horario'] == 'S') ? ' selected ' : ''; ?>>Semanal</option>
											<option value="I" <?= ($Horario['tipo_horario'] == 'I') ? ' selected ' : ''; ?>>Indice</option>
										</select>
									</td>
									<td class="text-center">
										<div class="btn-group dropleft mb-2 mb-md-0">
											<button type="button" class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="mdi mdi-dots-vertical"></i></button>
											<div class="dropdown-menu">
											<button type="button" class="dropdown-item" title="Configurar Horário2" onclick="return configurarCiclo('<?= $Horario['CODIGO']; ?>', '<?= addslashes(trim(preg_replace('/\s+/', ' ', $Horario['DESCRICAO']))); ?>')">
    <i class="mdi mdi-reload"></i> | Configurar Horário
</button>
											</div>
                                    	</div>
									</td>
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

<!-- modal modal_configurar_ciclo -->
<div class="modal modal_configurar_ciclo" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_configurar_ciclo" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white" id="titulo_configurar_ciclo"></h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body" >

                <div class="row">
                    <div class="col-12">

						<table class="table table-sm text-xs table-bordered" data-detalhes-horario>
							<thead class="bg-info">
								<tr>
									<th class="text-white text-center" width="65">Entrada1</th>
									<th class="text-white text-center" width="65">Saida1</th>
									<th class="text-white text-center" width="65">Entrada2</th>
									<th class="text-white text-center" width="65">Saida2</th>
									<th class="text-white text-center" style="background:#7b8aff;">Tolerância Entrada</th>
									<th class="text-white text-center" style="background:#7b8aff;">Tolerância Saída</th>
									<th class="text-white text-center">Tipo</th>
									<th class="text-white text-center">Jornada Diaria</th>
									<th class="text-white text-center">Intervalo</th>
									<th class="text-white text-center">Indice</th>
									<th class="text-white text-center" width="70">Ciclo</th>
									<th class="text-white text-center" width="120">Extra Limite Diário</th>
									<th class="text-white text-center" width="120">Excesso de Jornada Semanal</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
						
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="return salvaConfigCiclo()">Salvar <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_configurar_ciclo -->

<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 25,
        "aaSorting"         : [[0, "desc"]],
    });

	
});


const marcaCheckbox = (codHorario, nameField) => {

	openLoading();
	
	if(nameField != 'escala_especial' && nameField != 'intervalo_obrigatorio' && nameField != 'intervalo_fracionado'){
		var marcado 	= $("[data-"+nameField+"='"+codHorario+"']").val();
	}else{
		var marcado 	= ($("[data-"+nameField+"='"+codHorario+"']").prop('checked')) ? 1 : 0;
	}

	if (marcado == 0) {
		$("[data-planejado_inicio='"+codHorario+"']").prop('readonly', true);
		$("[data-planejado_termino='"+codHorario+"']").prop('readonly', true);
		
	} else{
				
		$("[data-planejado_inicio='"+codHorario+"']").prop('readonly', false);
		$("[data-planejado_termino='"+codHorario+"']").prop('readonly', false);
		
	}


	$.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/config_horario'); ?>",
        type: 'POST',
        data: {
			'codhorario': codHorario,
			'marcado'	: marcado,
			'field'		: nameField
		},
        success:function(result){
            
			openLoading(true);
            var response = JSON.parse(result);
			//exibeAlerta(response.tipo, response.msg, 3);
            
        },
    });

}
const configurarCiclo = (codHorario, descricao) => {

	$(".modal_configurar_ciclo").modal('show');
	$("[data-detalhes-horario] tbody").html('');
	$("#titulo_configurar_ciclo").html('<i class="mdi mdi-reload"></i> Configurar Horário | '+codHorario+' - '+descricao);

	$.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/carrega_dados_horario'); ?>",
        type: 'POST',
        data: {
			'codhorario': codHorario
		},
        success:function(result){
			console.log(result);
			try {
            
				openLoading(true);
				var response = JSON.parse(result);
				
				for(var x=0; x<response.length; x++){
					$("[data-detalhes-horario] tbody").append(`
						<tr>
							<td class="text-center">${response[x].ENTRADA1}</td>
							<td class="text-center">${response[x].SAIDA1}</td>
							<td class="text-center">${response[x].ENTRADA2}</td>
							<td class="text-center">${response[x].SAIDA2}</td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="text" class="form-control form-control-sm text-center horas" value="${response[x].TOLERANCIA_INICIO}"></td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="text" class="form-control form-control-sm text-center horas" value="${response[x].TOLERANCIA_FIM}"></td>
							<td class="text-center">${response[x].TIPO}</td>
							<td class="text-center">${response[x].QTD2}</td>
							<td class="text-center">${response[x].INTERVALO}</td>
							<td class="text-center">${response[x].CODINDICE}</td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="" class="form-control form-control-sm text-center" value="${response[x].CICLO}"></td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="time" class="form-control form-control-sm text-center" value="${response[x].HEXTRA_DIARIA}"></td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="text" class="form-control form-control-sm text-center horas" value="${response[x].EXCESSO_JORNADA_SEMANAL}"></td>
						</tr>
					`);
				}

				$(".horas").mask('99:99');

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}
            
        },
    });

}
const salvaConfigCiclo = () => {

	openLoading();

	var inputs = $("[data-detalhes-horario] tbody").find('input');

	var dados = [];
	$("[data-detalhes-horario] tbody tr").each(function(e){

		var input = $(this).find('input');

		dados.push({
			'codhorario'		: input[0].dataset.codhorario,
			'codindice'			: input[0].dataset.indice,
			'tipo'				: input[0].dataset.tipo,
			'tolerancia_inicio'	: input[0].value,
			'tolerancia_fim'	: input[1].value,
			'ciclo'				: input[2].value,
			'extra'				: input[3].value,
			'excesso'			: input[4].value,
		});

	});


	$.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/salva_dados_horario'); ?>",
        type: 'POST',
        data: {
			'dados': dados
		},
        success:function(result){

			try {
            
				openLoading(true);
				var response = JSON.parse(result);
				exibeAlerta(response.tipo, response.msg);
				$(".modal_configurar_ciclo").modal('hide');

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}
            
        },
    });

}
const salvaConfigWorkflow = () => {

	var data = {
		'data1': $("#data1").val(),
		'data2': $("#data2").val(),
		'horario1': $("#horario1").val(),
		'horario2': $("#horario2").val(),
		'ciclo1': $("#ciclo1").val(),
		'ciclo2': $("#ciclo2").val(),
	};

	if(data.data1 == ''){alert('error', 'Data Envio W1 não informado.'); return;}
	if(data.data2 == ''){alert('error', 'Data Envio W2 não informado.'); return;}
	if(data.ciclo1 == ''){alert('error', 'Ciclo de notificação W1 não informado.'); return;}
	if(data.horario1 == ''){alert('error', 'Horário do W1 não informado.'); return;}
	if(data.ciclo2 == ''){alert('error', 'Ciclo de notificação W2 não informado.'); return;}
	if(data.horario2 == ''){alert('error', 'Horário do W2 não informado.'); return;}

	openLoading();

	$.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/salva_configuracao_workflow'); ?>",
        type: 'POST',
        data: {
			'dados': data
		},
        success:function(result){

			try {
            
				openLoading(true);
				var response = JSON.parse(result);
				exibeAlerta(response.tipo, response.msg);

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}
            
        },
    });

}
</script>
<?php loadPlugin(array('datatable', 'mask')); ?>