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
                            <a href="<?= base_url('premio/requisicao/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova Requisição</a>
                        </div>
                    </div>
                    </div>
                </div>
                
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%; font-size: 12px;">
                        <thead>
                            <tr>
                                <th class="text-center" width="30">ID</th>
                                <th width="70">Tipo</th>
                                <th class="text-center" width="50">Status</th>
                                <th width="70">Data</th>
                                <th width="150">Requisitante</th>
                                <th width="150">Gestor</th>
                                <th width="150">Prêmio</th>
                                <th width="150">Período de Ponto</th>
                                <th class="text-center" width="50">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($resRequisicao): ?>
                                <?php foreach($resRequisicao as $key => $Requisicao): ?>
                                    <tr data-linha="<?= $Requisicao['id'] ?>">
                                        <td class="text-left"><?= $Requisicao['id'] ?></td>
                                        <td class="text-left">
                                        <?php
                                            switch($Requisicao['tipo']){
                                                case 'M': echo 'Mensal'; break;
                                                case 'C': echo 'Complementar'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-center">
                                        <?php
                                            switch($Requisicao['status']){
                                                case 'S': echo '<span class="badge badge-purple">&nbsp&nbsp&nbsp&nbspSincronizada&nbsp&nbsp&nbsp&nbsp</span>'; break;
                                                case 'H': echo '<span class="badge badge-primary">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspAprovada&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>'; break;
                                                case 'A': echo '<span class="badge badge-warning">&nbspPend/RH Calcular&nbsp</span>'; break;
                                                case 'C': echo '<span class="badge badge-success">&nbsp&nbsp&nbspPend/Ação RH&nbsp&nbsp&nbsp</span>'; break;
                                                case 'E': echo '<span class="badge badge-info">Pend/Ação Gestor</span>'; break;
                                                case 'R': echo '<span class="badge badge-danger">&nbsp&nbsp&nbsp&nbsp&nbsp&nbspReprovada&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>'; break;
                                                case 'P': echo '<span class="badge badge-dark">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspCriada&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-left"><?= date('d/m/Y', strtotime($Requisicao['dt_requisicao'])) ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['nome_requisitor'].' - '.$Requisicao['chapa_requisitor'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['nome_gerente'].' - '.$Requisicao['chapa_gerente'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['nome_premio'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['per_ponto_br'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <button id="btn_enviar" name="btn_enviar" onclick="return requisitar(<?= $Requisicao['id']; ?>,<?= $Requisicao['tem_zeros']; ?>)" class="btn btn-soft-success waves-effect waves-light btn-xxs" <?php if(!($Requisicao['status'] == 'P' or $Requisicao['status'] == 'R')) { echo 'disabled';}?>><i class="fa fa-paper-plane"></i></button>
                                                <button id="btn_excluir" name="btn_excluir" onclick="return excluir(<?= $Requisicao['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs" <?php if(!($Requisicao['status'] == 'P') and !($Requisicao['status'] == 'R')) { echo 'disabled';}?>><i class="fa fa-times"></i></button>
                                                <?php if(!($Requisicao['status'] == 'P' or $Requisicao['status'] == 'R')) { ?>
                                                    <a id="btn_ver" name="btn_ver" href="<?= base_url('premio/requisicao/editar/'.$Requisicao['id']) ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="fa fa-eye"></i></a>
                                                <?php } else { ?>
                                                    <a id="btn_editar" name="btn_editar" href="<?= base_url('premio/requisicao/editar/'.$Requisicao['id']) ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i></a>
                                                <?php } ?>
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
<style> 
td {
    padding: 0.5rem !important;
} 
</style>
<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : -1,
        "aaSorting"         : [[0, "desc"]]
    });
    $("#btn_enviar").hover(function() {
        $(this).css('cursor','pointer').attr('title', 'Enviar para aprovação');
    }, function() {
        $(this).css('cursor','auto');
    });
    $("#btn_excluir").hover(function() {
        $(this).css('cursor','pointer').attr('title', 'Excluir requisição');
    }, function() {
        $(this).css('cursor','auto');
    });
    $("#btn_editar").hover(function() {
        $(this).css('cursor','pointer').attr('title', 'Editar requisição');
    }, function() {
        $(this).css('cursor','auto');
    });
    $("#btn_ver").hover(function() {
        $(this).css('cursor','pointer').attr('title', 'Visualizar requisição');
    }, function() {
        $(this).css('cursor','auto');
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
				url: "<?= base_url('premio/requisicao/action/deletar') ?>",
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

const requisitar = (id,tem_zeros) => {

let msg = 'Deseja enviar esta requisição para <b>aprovação</b>?';
if (tem_zeros==1) {
    msg = 'Atenção! Está requisição tem percentuais de realizado zerados. Para enviar para aprovação esses registros precisam ser apagados. Deseja excluir esses registros e enviar esta requisição para <b>aprovação</b>?';
}

Swal.fire({
    icon: 'question',
    title: msg,
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: `Sim Enviar`,
    denyButtonText: `Cancelar`,
    showCancelButton: false,
    showCloseButton: false,
    allowOutsideClick: false,
    width: 600,
}).then((result) => {
    if (result.isConfirmed) {
        openLoading();
        let dados = {"id":id};

        $.ajax({
            url: "<?= base_url('premio/requisicao/action/requisitar') ?>",
            type:'POST',
            data:dados,
            success:function(result){
                try {
                    var response = JSON.parse(result);
                    
                    if(response.tipo != 'success'){
                        exibeAlerta(response.tipo, response.msg);
                    }else{
                        exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('premio/requisicao') ?>');
                    }
                }catch (e) {
                    exibeAlerta('error', '<b>Erro interno do sistema:</b><br><code class="language-markup">'+e+'</code>');
                }
            },
        });

    }
});

}

</script>
<?php
loadPlugin(array('datatable','select2'));
?>