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
							<label for="periodo" class="col-2 col-form-label text-right pr-0 pl-0">Período:</label>
                            <div class="col-5">
                                <div class="input-group">
                                    <input type="date" id="data_inicio" name="data_inicio" class="form-control" placeholder="Data inicio" value="<?= $data_inicio; ?>">
                                    <div class="input-group-append input-group-prepend">
                                        <span class="input-group-text"><i class="mdi mdi-calendar-range"></i></span>
                                    </div>
                                    <input type="date" id="data_fim" name="data_fim" class="form-control" placeholder="Data Fim" value="<?= $data_fim; ?>">
                                </div>
                            </div>
						</div>
					</form>

				</div>

                <div class="card-footer text-center">
                    <button class="btn btn-success btn-xxs" onclick="return filtaDados()"><i class="fas fa-search"></i> Filtrar</button>
                </div>
                
			</div><!-- end card -->

            <!-- card -->
            <?php $teste = array(); ?>
            <?php if($ListarWorkflowLog): ?>
			<div class="card">
				<div class="card-body">

                    <table id="datatable" class="table table-bordered dt-responsive nowrap table-sm table-striped" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" width="90">ID</th>
                                <th class="text-center">Nome</th>
                                <th class="text-center">E-mail</th>
                                <th class="text-center" width="100">Data</th>
                                <th class="text-center">Detalhes</th>
                                <th class="text-center">Enviado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($ListarWorkflowLog): ?>
                                <?php foreach($ListarWorkflowLog as $key => $Log): ?>
                                    <tr>
                                        <td class="text-center"><?= $Log['id']; ?></td>
                                        <td><?= $Log['nome']; ?></td>
                                        <td class="text-center"><?= $Log['email']; ?></td>
                                        <td class="text-center"><?= dtBr($Log['dtcad']); ?></td>
                                        <td><pre><?php print_r(unserialize($Log['descricao'])); ?></pre></td>
                                        <td class="text-center"><?= ($Log['enviado'] == 1) ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-danger">Não</span>'; ?></td>
                                    </tr>
                                    <?php $teste[$Log['nome'].'_'.$key] = $Log['id']; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
			</div><!-- end card -->
            <?php endif; ?>
		
		  
          
        
		</div><!-- end main -->
		
	</div><!-- end row -->
</div><!-- container -->
<script>
const filtaDados = () => {
    var dados = {
        "data_inicio": $("#data_inicio").val(),
        "data_fim": $("#data_fim").val(),
    }

    if(dados.data_inicio == "" && dados.data_fim == ""){
        exibeAlerta('error', '<b>Data ínicio</b> ou <b>Data fim</b> não informada.');
        return false;
    }

    openLoading();
    $("#form_envio").submit();
}
</script>