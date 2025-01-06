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
                            <a href="<?= base_url('premio/cadastro/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo</a>
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
                                    <th>Nome</th>
                                    <th width="200">Descrição</th>
                                    <th width="70">Vigência</th>
                                    <th class="text-center" width="50">Status</th>
                                    <th class="text-center" width="500">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resPremios): ?>
                                <?php foreach($resPremios as $key => $Premio): ?>
                                    <tr data-linha="<?= $Premio['id'] ?>">
                                        <td class="text-left"><?= $Premio['id'] ?></td>
                                        <td class="text-left" style="max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><a href="<?= base_url('premio/cadastro/editar/'.$Premio['id']); ?>"><?= $Premio['nome'] ?></a></td>
                                        <td class="text-left" style="max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Premio['descricao'] ?></td>
                                        <td class="text-left"><?= is_null($Premio['dt_vigencia']) ? 'indefinida' : date('d/m/Y', strtotime($Premio['dt_vigencia'])) ?></td>
                                        <td class="text-center">
                                        <?php
                                            switch($Premio['status']){
                                                case 'A': echo '<span class="badge badge-primary">&nbsp&nbsp&nbspAtivo&nbsp&nbsp&nbsp</span>'; break;
                                                case 'B': echo '<span class="badge badge-warning">Bloqueado</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="<?= base_url('premio/cadastro/premissas/'.$Premio['id']); ?>" class="btn btn-soft-dark waves-effect waves-light btn-xxs">Premissas</a>
                                                <a href="<?= base_url('premio/cadastro/deflatores/'.$Premio['id']); ?>" class="btn btn-soft-dark waves-effect waves-light btn-xxs">Deflatores</a>
                                                <a href="<?= base_url('premio/cadastro/apuracao/'.$Premio['id']); ?>" class="btn btn-soft-dark waves-effect waves-light btn-xxs">Apuração</a>
                                                <a href="<?= base_url('premio/cadastro/acessos/'.$Premio['id']); ?>" class="btn btn-soft-success waves-effect waves-light btn-xxs">Acessos</a>
                                                <a href="<?= base_url('premio/cadastro/acessos_excecao/'.$Premio['id']); ?>" class="btn btn-soft-warning waves-effect waves-light btn-xxs">Exceção</a>
                                                <a href="javascript:void(0);" onclick="return excluir(<?= $Premio['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
                                                <a href="<?= base_url('premio/cadastro/editar/'.$Premio['id']); ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> Editar</a>
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
				url: "<?= base_url('premio/cadastro/action/deletar') ?>",
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