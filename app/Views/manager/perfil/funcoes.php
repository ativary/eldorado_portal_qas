<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1">Adicionar função ao perfil</h4>
                        <div class="col-6 text-right">
                            <div class="button-items">
                                <a href="<?= base_url('manager/perfil') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label for="perfil_nome" class="col-2 col-form-label text-right">Perfil:</label>
                        <div class="col-sm-10">
                            <input class="form-control form-control-sm" type="text" value="<?= $resDados[0]['id'].' - '.$resDados[0]['nome']; ?>" name="perfil_nome" id="perfil_nome" require disabled>
                        </div>
                    </div>
                    <div class="row">
                        <label for="perfil_nome" class="col-2 col-form-label text-right">Função:</label>
                        <div class="col-sm-10">
                            <select class="select2 custom-select form-control form-control-sm" name="id_funcao" id="id_funcao">
                                <option value="">- selecione uma função -</option>
                                <?php if($resFuncoes): ?>
                                    <?php foreach($resFuncoes as $key => $Funcoes): ?>
                                        <option value="<?= $Funcoes['id']; ?>"><?= $Funcoes['id'].' - '.$Funcoes['nome']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return salvaDados()"><i class="mdi mdi-subdirectory-arrow-right"></i> Adicionar</button>
                </div>
            </div>


            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-6 mb-1 mt-1">Funções do Perfil</h4>
                    <div class="col-6 text-right">
                        <div class="button-items">
                            <a href="<?= base_url('manager/perfil') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">                    

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="70">ID</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th class="text-center" width="100">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resFuncaoPerfil): ?>
                                <?php foreach($resFuncaoPerfil as $key => $FuncaoPerfil): ?>
                                    <tr data-linha="<?= $FuncaoPerfil['id'] ?>">
                                        <td class="text-center"><?= $FuncaoPerfil['id'] ?></td>
                                        <td><a href="<?= base_url('manager/funcao/editar/'.$FuncaoPerfil['id']); ?>" target="_blank"><?= $FuncaoPerfil['nome'] ?></a></td>
                                        <td><?= $FuncaoPerfil['descr'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="javascript:void(0);" onclick="return deletar_funcao_perfil(<?= $FuncaoPerfil['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> excluir</a>
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
const salvaDados = () => {
    
    let dados = {
        "id_perfil": <?= $id; ?>,
        "id_funcao": $("#id_funcao").val(),
    }

    if(dados.id_perfil == ""){ exibeAlerta("error", "<b>Id Perfil</b> não informado."); return false; }
    if(dados.id_funcao == ""){ exibeAlerta("error", "<b>Função</b> não informada."); return false; }

    $.ajax({
        url: "<?= base_url('manager/perfil/action/cadastrar_funcao_perfil'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);
            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, window.location.href);
            }
            
        },
    });
    
}
const deletar_funcao_perfil = (id_funcao) => {

    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente excluir esta função do perfil?',
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

            let dados = {
                "id_perfil": <?= $id; ?>,
                "id_funcao": id_funcao,
            }

            if(dados.id_perfil == ""){ exibeAlerta("error", "<b>Id Perfil</b> não informado."); return false; }
            if(dados.id_funcao == ""){ exibeAlerta("error", "<b>Id Função</b> não informado."); return false; }

            $.ajax({
                url: "<?= base_url('manager/perfil/action/deletar_funcao_perfil'); ?>",
                type:'POST',
                data:dados,
                success:function(result){

                    var response = JSON.parse(result);
                    exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    

                },
            });

        }
	});

}
</script>
<?php
loadPlugin(array('datatable', 'select2'));
?>