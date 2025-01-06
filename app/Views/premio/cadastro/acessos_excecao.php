<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0" style="background-color: #ffe69c;">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <div class="col-6 text-right">
                            <div class="button-items">
                               <a href="<?= base_url('premio/cadastro/novo_acesso_excecao/'.$id_premio) ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova Exceção de Acesso</a>
                                <a href="<?= base_url('premio/cadastro') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> Voltar</a>
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
                                    <th width="140">Início das Requisições</th>
                                    <th width="140">Final das Requisições</th>
                                    <th width="140">Início do Ponto</th>
                                    <th width="140">Final do Ponto</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resAcessos): ?>
                                <?php foreach($resAcessos as $key => $Acesso): ?>
                                    <tr data-linha="<?= $Acesso['id'] ?>">
                                        <td class="text-left"><a href="<?= base_url('premio/cadastro/editar_acesso_excecao/'.$Acesso['id'].'/'.$id_premio); ?>"><?= $Acesso['id'] ?></a></td>
                                        <td class="text-left"><?= date('d/m/Y', strtotime($Acesso['dtini_req'])) ?></td>
                                        <td class="text-left"><?= date('d/m/Y', strtotime($Acesso['dtfim_req'])) ?></td>
                                        <td class="text-left"><?= date('d/m/Y', strtotime($Acesso['dtini_ponto'])) ?></td>
                                        <td class="text-left"><?= date('d/m/Y', strtotime($Acesso['dtfim_ponto'])) ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="<?= base_url('premio/cadastro/acessos_excecao_usuarios/'.$Acesso['id'].'/'.$id_premio); ?>" class="btn btn-soft-success waves-effect waves-light btn-xxs"><i class="fa fa-users"></i> Usuários</a>
                                                <a href="javascript:void(0);" onclick="return excluir(<?= $Acesso['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
                                                <a href="<?= base_url('premio/cadastro/editar_acesso_excecao/'.$Acesso['id'].'/'.$id_premio); ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> Editar</a>
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
				url: "<?= base_url('premio/cadastro/action_acessos_excecao/deletar') ?>",
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