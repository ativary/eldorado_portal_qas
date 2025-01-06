<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
				<div class="card-header mt-0">
					<div class="row">
						<h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-briefcase-edit-outline"></i> <?= $_titulo; ?></h4>
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
</script>
<?php loadPlugin(array('select2', 'datatable', 'mask')); ?>