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
                            <a href="<?= base_url('manager/usuario/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="70">ID</th>
                                    <th>Login</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th class="text-center" width="100">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resDados): ?>
                                <?php foreach($resDados as $key => $Dados): ?>
                                    <tr data-linha="<?= $Dados['id'] ?>">
                                        <td class="text-center"><?= $Dados['id'] ?></td>
                                        <td><a href="<?= base_url('manager/usuario/editar/'.$Dados['id']); ?>"><?= $Dados['login'] ?></a></td>
                                        <td><?= $Dados['nome'] ?></td>
                                        <td><?= $Dados['email'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a title="Excluir" href="javascript:void(0);" onclick="return excluir(<?= $Dados['id']; ?>)" class="btn btn-soft-danger waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> </a>
                                                <a title="Editar" href="<?= base_url('manager/usuario/editar/'.$Dados['id']); ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> </a>
                                                <a title="Perfil" href="<?= base_url('manager/usuario/perfil/'.$Dados['id']); ?>" class="btn btn-soft-dark waves-effect waves-light btn-xxs"><i class="fas fa-user-tag"></i> </a>
                                                <a title="Seção" href="<?= base_url('manager/usuario/secao/'.$Dados['id']); ?>" class="btn btn-soft-info waves-effect waves-light btn-xxs"><i class="fas fa-users"></i> </a>
                                            </div>
                                            
                                        </td>
                                    </tr>
                                    <?php unset($Dados); ?>
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
        "iDisplayLength"    : 50,
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
				url: "<?= base_url('manager/usuario/action/deletar') ?>",
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