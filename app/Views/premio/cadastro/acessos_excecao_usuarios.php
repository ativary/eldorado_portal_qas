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
                                <a href="<?= base_url('premio/cadastro/acessos_excecao/'.$resAcesso[0]['id_premio']) ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> Voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header mt-0">
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Nome do Prêmio:</h6>
                        <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resAcesso[0]['nome_premio']; ?></h5>
                        <h6 class="col-4 text-left mb-1 mt-1">Selecione um colaborador para adicionar:</h6>
                        <h6 class="col-3 text-left mb-1 mt-1">Selecione um período importar:</h6>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Período das Requisições:</h6>
                        <h5 class="col-3 mb-1 mt-1"><?= $resAcesso[0]['per_req_br']; ?></h5>
                        <div class="col-4">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="chapa" id="chapa">
                                <option value="">...</option>
                                <?php foreach($resFunc as $key => $Func): ?>
                                    <option value="<?= $Func['CHAPA']; ?>"><?= $Func['NOME'].' - '.$Func['CHAPA']; ?></option>
                                    <?php //unset($resFunc[$key], $key, $Func); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-3">
                            <select class="select2 text-left form-control form-control-sm mb-1" name="id_acesso_imp" id="id_acesso_imp">
                                <option value="">...</option>
                                <?php foreach($resPonto as $key => $Ponto): ?>
                                    <?php if($Ponto['per_ponto_br'] != $resAcesso[0]['per_ponto_br']) { ?>
                                        <option value="<?= $Ponto['id_acesso']; ?>"><?= $Ponto['per_ponto_br']; ?></option>
                                    <?php } ?>
                                    <?php unset($resPonto[$key], $key, $Ponto); ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Período do Ponto:</h6>
                        <h5 class="col-3 mb-1 mt-1"><?= $resAcesso[0]['per_ponto_br']; ?></h5>
                        <div class="col-4 text-center">
                            <div class="btn" aria-label="acao" role="group">
                                <a href="javascript:void(0);" onclick="return adicionar()" class="btn btn-primary btn-xxs mb-1"><i class="fa fa-plus"></i> Adicionar Usuário</a>
                            </div>
                        </div>
                        <div class="col-3 text-center">
                            <div class="btn" aria-label="acao" role="group">
                                <a href="javascript:void(0);" onclick="return importar()" class="btn btn-success btn-xxs mb-1"><i class="fa fa-user-plus"></i> Importar Usuários</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th width="100">Chapa</th>
                                    <th>Nome do Colaborador</th>
                                    <th class="text-center"  width="100">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resAcessoUsuarios): ?>
                                <?php foreach($resAcessoUsuarios as $key => $Acesso): ?>
                                    <tr data-linha="<?= $Acesso['id'] ?>">
                                        <td class="text-left"><?= $Acesso['chapa'] ?></td>
                                        <td class="text-left"><?= $Acesso['nome_func'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="javascript:void(0);" onclick="return excluir(<?= $Acesso['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
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
				url: "<?= base_url('premio/cadastro/action_acessos_excecao/deletar_usuario') ?>",
				type:'POST',
				data:dados,
				success:function(result){
					var response = JSON.parse(result);
                    if(response.tipo == "success") $("[data-linha='"+id+"']").remove();
					//exibeAlerta(response.tipo, response.msg, window.location.href);
                    window.location.reload();
				},
			});

		}
	});

}

const adicionar = () => {

    if($("#chapa").val() == ""){ exibeAlerta("error", "<b>Colaborador</b> não informado."); return false; }

    // Validação das datas. Para que não existam Acessos em períodos de pontos já selecionados ou datas de requisições sobrepostas
    <?php if ($resAcessoUsuarios) {?>
        <?php foreach($resAcessoUsuarios as $key => $Usuario): ?>
            if($("#chapa").val() == "<?= $Usuario['chapa']; ?>"){ exibeAlerta("error", "Esse <b>Colaborador</b> já está na lista de acesso."); return false; }
        <?php endforeach; ?>
    <?php }?>

    let dados = {
        "chapa": $("#chapa").val(),
        "id_acesso": <?= $id_acesso; ?>,
    }

    $.ajax({
        url: "<?= base_url('premio/cadastro/action_acessos_excecao/adicionar_usuario'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                window.location.reload();
                //exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/cadastro/acessos_excecao_usuarios/'.$id_acesso) ?>');
            }

        },
    });
}

const importar = () => {

    if($("#id_acesso_imp").val() == ""){ exibeAlerta("error", "<b>Período de ponto</b> não informado."); return false; }

    let dados = {
        "id_acesso_imp": $("#id_acesso_imp").val(),
        "id_acesso": <?= $id_acesso; ?>,
    }

    $.ajax({
        url: "<?= base_url('premio/cadastro/action_acessos_excecao/importar_usuarios'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                window.location.reload();
                //exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/cadastro/acessos_excecao_usuarios/'.$id_acesso) ?>');
            }

        },
    });
}

</script>

<?php
    loadPlugin(['select2','datatable']);
?>