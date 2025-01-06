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
					<form action="" method="post" name="form_envio" id="form_envio">
						<div class="form-group row">
							 <label for="horario" class="col-2 col-form-label text-right pr-0 pl-0">Horario:</label>
										<div class="col-10">
											<select name="horario" id="horario" class="form-control form-control-sm">
												<option value="">- Todos -</option>
												<?php if($resHorario): ?>
													<?php foreach($resHorario as $key => $Horario): ?>
														<option value="<?= $Horario['CODIGO']; ?>"><?= $Horario['CODIGO'].' - '.$Horario['DESCRICAO']; ?></option>
													<?php endforeach; ?>
												<?php endif; ?>
											</select>
										</div>
						</div>
					</form>

					<div class="card-footer text-center">
						<button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar</button>
					</div>

				</div>
					</div><!-- end main -->
					   <div class="card">
				 <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="70">ID</th>
                                    <th>Codigo</th>
									
                                    <th>Coligada</th>
									<th>DESCRIÇÂO</th>
                                    <th class="text-center" width="100">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resPortalHorario): ?>
                                <?php foreach($resPortalHorario as $key => $Dados): ?>
                                    <tr data-linha="<?= $Dados['id'] ?>">
                                        <td class="text-center"><?= $Dados['id'] ?></td>
                                        <td class="text-center"><?= $Dados['codigo'] ?></td>
                                       <td class="text-center"><?= $Dados['coligada'] ?></td>
									   <td class="text-center"><?= $Dados['DESCRICAO'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="javascript:void(0);" onclick="return excluir(<?= $Dados['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                           
                                            </div>
                                            
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
			
			
			</div><!-- end main -->
		
		  
          
        
		</div>
		
	 </div>
</div><!-- container -->
<script>
const salvaDados = () => {
	  let dados = {
        "horario": $("#horario").val(), 
    };
 
	openLoading();

	
    if(dados.horario == ""){ exibeAlerta("error", "<b>horario</b> não informada."); return false; }
		
	 $.ajax({
        url: "<?= base_url('ponto/ocorrencia/salvahorario'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

			
			exibeAlerta('3', 'Horario incluido com sucesso', 3, '<?= base_url('ponto/ocorrencia/config'); ?>/');
           

        },
    });

    
}

const excluir = (id) => {
	
   
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

			let dados = {"id":id};

			$.ajax({
				url: "<?= base_url('ponto/ocorrencia/deletaHorario') ?>",
				type:'POST',
				data:dados,
				success:function(result){
					exibeAlerta('3', 'Horario excluido com sucesso', 3, '<?= base_url('ponto/ocorrencia/config'); ?>/');
				},
			});

		}
	});

    
}
</script>