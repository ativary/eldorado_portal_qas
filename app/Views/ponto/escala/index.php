<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1">Filtro</h4>
                        <div class="col-6 text-right">
                            <div class="button-items">
                                <a href="<?= base_url('ponto/escala/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo</a>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="" method="post" name="form_filtro" id="form_filtro">
                    <div class="card-body">
                        <div class="row">
                            <label for="perfil_nome" class="col-2 col-form-label text-right">Situação:</label>
                            <div class="col-sm-10">
                                <select class="form-control form-control-sm" name="filtro" id="filtro">
                                    <option value="">...</option>
                                    <option value="0" <?= ($filtro == 0 && $filtro != null) ? " selected " : ""; ?>>Aguardando aprovação gestor</option>
                                    <option value="1" <?= ($filtro == 1) ? " selected " : ""; ?>>Aguardando upload documento</option>
                                    <option value="2" <?= ($filtro == 2) ? " selected " : ""; ?>>Aguardando aprovação RH</option>
                                    <option value="3" <?= ($filtro == 3) ? " selected " : ""; ?>>Concluído</option>
                                    <option value="9" <?= ($filtro == 9) ? " selected " : ""; ?>>Cancelado / Reprovado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return filtraDados()"><i class="fas fa-search"></i> Exibir</button>
                    </div>
                </form>
            </div>

            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60">ID</th>
                                    <th class="text-center">Situação</th>
                                    <th>Colaborador</th>
                                    <th class="text-center" width="100">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resEscala): ?>
                                <?php foreach($resEscala as $key => $Escala): ?>
                                    <tr data-linha="<?= $Escala['id'] ?>">
                                        <td class="text-center"><?= $Escala['id']; ?></td>
                                        <td class="text-center" width="150">
                                        <?php
                                            switch($Escala['situacao']){
                                                case 0: echo '<span class="badge badge-warning">Aguardando aprovação gestor</span>'; break;
                                                case 1: echo '<span class="badge badge-success">Aguardando upload documento</span>'; break;
                                                case 2: echo '<span class="badge badge-info">Aguardando aprovação RH</span>'; break;
                                                case 3: echo '<span class="badge badge-primary">Concluído</span>'; break;
                                                case 8: echo '<span class="badge badge-danger">Reprovado</span>'; break;
                                                case 9: echo '<span class="badge badge-danger">Cancelado</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td><a href="<?= base_url('ponto/escala/editar/'.id($Escala['id'])).'/'.id($Escala['situacao']); ?>"><?= $Escala['nome'].' - '.$Escala['chapa']; ?></a></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="<?= base_url('ponto/escala/editar/'.id($Escala['id'])).'/'.id($Escala['situacao']); ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs">visualizar</a>
                                                <?php if(($Escala['step'] == 1 && $Escala['situacao'] == 0) || ($Escala['step'] == 2 && $Escala['situacao'] == 2)): ?>
                                                    <button type="button" class="btn btn-soft-success waves-effect waves-light btn-xxs" onclick="return aprovar('<?= id($Escala['id']); ?>', '<?= id($Escala['step']); ?>')"><i class="far fa-thumbs-up"></i> aprovar</button>
                                                    <button type="button" class="btn btn-soft-danger waves-effect waves-light btn-xxs" onclick="return reprovar('<?= id($Escala['id']); ?>', '<?= id($Escala['step']); ?>')"><i class="far fa-thumbs-down"></i> reprovar</button>
                                                <?php endif; ?>
                                                <?php if($Escala['situacao'] == 1 && $filtro == null): ?>
                                                    <a href="<?= base_url('ponto/escala/termo_aditivo/'.id($Escala['id'])); ?>" class="btn btn-soft-info waves-effect waves-light btn-xxs" target="_blank"><i class="dripicons-download"></i> termo aditivo</a>
                                                <?php endif; ?>
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
        "iDisplayLength"    : 25,
        "aaSorting"         : [[0, "desc"]]
    });
});
const filtraDados = () => {

    openLoading();
    document.getElementById("form_filtro").submit();
    
}
const aprovar = (id, step) => {

    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente aprovar este <b>registro</b>?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim Aprovar`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if (result.isConfirmed) {

			let dados = {"id":id, "step": step};

            openLoading();

			$.ajax({
				url: "<?= base_url('ponto/escala/action/aprovar') ?>",
				type:'POST',
				data:dados,
				success:function(result){
					var response = JSON.parse(result);
					
					if(response.tipo == "success"){
                        exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    }else{
                        exibeAlerta(response.tipo, response.msg, 2);
                    }
				},
			});

		}
	});

}
const reprovar = (id, step) => {

Swal.fire({
    icon: 'question',
    title: 'Deseja realmente reprovar este <b>registro</b>?',
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: `Sim Reprovar`,
    denyButtonText: `Cancelar`,
    showCancelButton: false,
    showCloseButton: false,
    allowOutsideClick: false,
    width: 600,
}).then((result) => {
    if (result.isConfirmed) {

        let dados = {"id":id, "step": step};

        openLoading();

        $.ajax({
            url: "<?= base_url('ponto/escala/action/reprovar') ?>",
            type:'POST',
            data:dados,
            success:function(result){
                var response = JSON.parse(result);
                
                exibeAlerta(response.tipo, response.msg, 3, window.location.href);

            },
        });

    }
});

}
</script>
<?php
loadPlugin(array('datatable'));
?>