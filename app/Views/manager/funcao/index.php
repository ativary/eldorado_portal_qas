<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                    <div class="col-6 text-right">
                        <div class="button-items">
                            <a href="<?= base_url('manager/funcao/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th class="text-center">Modo</th>
                                    <th>Caminho</th>
                                    <th class="text-center" width="100">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resFuncoes): ?>
                                <?php foreach($resFuncoes as $key => $Funcao): ?>
                                    <tr data-linha="<?= $Funcao['id'] ?>">
                                        <td class="text-center"><?= $Funcao['id'] ?></td>
                                        <td><a href="<?= base_url('manager/funcao/editar/'.$Funcao['id']); ?>"><?= $Funcao['nome'] ?></a></td>
                                        <td><?= $Funcao['descr'] ?></td>
                                        <td class="text-center">
                                        <?php
                                            switch($Funcao['modo']){
                                                case 'L': echo '<span class="badge badge-info">L</span>'; break;
                                                case 'E': echo '<span class="badge badge-primary">E</span>'; break;
                                                case 'M': echo '<span class="badge badge-warning">M</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td><?= $Funcao['caminho'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="javascript:void(0);" onclick="return excluir(<?= $Funcao['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
                                                <a href="<?= base_url('manager/funcao/editar/'.$Funcao['id']); ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> editar</a>
                                            </div>
                                            
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : -1,
        "aaSorting"         : [[0, "desc"]]
    });
});
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
				url: "<?= base_url('manager/funcao/action/deletar') ?>",
				type:'POST',
				data:dados,
				success:function(result){
					var response = JSON.parse(result);
					exibeAlerta(response.tipo, response.msg);
					if(response.tipo == "success") $("[data-linha='"+id+"']").remove();
				},
			});

		}
	});

}
</script>
<?php
loadPlugin(array('datatable'));
?>