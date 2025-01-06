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
                                <a href="javascript:void(0);" onclick="return apagar(<?= $id_premio; ?>)" class="btn btn-danger btn-xxs mb-0"><i class="fas fa-times"></i> Apagar Premissas</a>
                                <a href="<?= base_url('premio/cadastro/importar_premissas/'.$id_premio) ?>" class="btn btn-warning btn-xxs mb-0"><i class="fas fa-file-excel"></i> Importar </a>
                                <a href="<?= base_url('premio/cadastro/exportar_premissas/'.$id_premio) ?>" class="btn btn-success btn-xxs mb-0"><i class="fas fa-file-excel"></i> Exportar </a>
                                <a href="<?= base_url('premio/cadastro/nova_premissa/'.$id_premio) ?>" class="btn btn-purple btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova Premissa</a>
                                <a href="<?= base_url('premio/cadastro') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="20">ID</th>
                                    <th>Filial</th>
                                    <th>C.Custo</th>
                                    <th>Função</th>
                                    <th width="200">Nome da Função</th>
                                    <th>Grupo</th>
                                    <th>% Target</th>
                                    <th class="text-center" width="50">Tipo Target</th>
                                    <th class="text-center" width="100">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resPremissas): ?>
                                <?php foreach($resPremissas as $key => $Premissa): ?>
                                    <tr data-linha="<?= $Premissa['id'] ?>">
                                        <td class="text-left"><a href="<?= base_url('premio/cadastro/editar_premissa/'.$Premissa['id'].'/'.$id_premio); ?>"><?= $Premissa['id'] ?></a></td>
                                        <td class="text-left"><?= $Premissa['codfilial'] ?></td>
                                        <td class="text-left"><?= $Premissa['codcusto'] ?></td>
                                        <td class="text-left"><?= $Premissa['codfuncao'] ?></td>
                                        <td class="text-left" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Premissa['nome_funcao'] ?></td>
                                        <td class="text-left"><?= $Premissa['grupo'] ?></td>
                                        <td class="text-left"><?= $Premissa['target_br'] ?></td>
                                        <td class="text-center">
                                        <?php
                                            switch($Premissa['tipo_target']){
                                                case 'F': echo '<span class="badge badge-primary">&nbsp&nbsp&nbspFIXO&nbsp&nbsp&nbsp</span>'; break;
                                                case 'M': echo '<span class="badge badge-warning">MÚLTIPLO</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="javascript:void(0);" onclick="return excluir(<?= $Premissa['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
                                                <a href="<?= base_url('premio/cadastro/editar_premissa/'.$Premissa['id'].'/'.$id_premio); ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> Editar</a>
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
        "aaSorting"         : [[0, "asc"],[1,"asc"],[2,"asc"]]
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
				url: "<?= base_url('premio/cadastro/action_premissas/deletar') ?>",
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

const apagar = (id) => {
    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente excluir <b>todas as premissas</b> deste prêmio?',
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
				url: "<?= base_url('premio/cadastro/action_premissas/deletar_todas') ?>",
				type:'POST',
				data:dados,
				success:function(result){
					var response = JSON.parse(result);
					exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/cadastro/premissas/'); ?>'+'/'+id);
				},
			});

		}
	});

}

</script>
<?php
loadPlugin(array('datatable'));
?>