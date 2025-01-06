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
                            <a href="<?= base_url('premio/emprestimos/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova</a>
                        </div>
                    </div>
                    </div>
                </div>
                
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <?php if($resAprovar != []): ?>
                        <a href="<?= base_url('premio/emprestimos/aprovacao') ?>" class="col-12 btn btn-soft-danger waves-effect waves-light">Existem cedências para serem aprovados. <i class="fa fa-eye"></i> Clique aqui para visualizar.</a>
                        <div class="form-group row"> </div>
                    <?php endif; ?>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%; font-size: 12px;">
                        <thead>
                            <tr>
                                <th class="text-center" width="30">ID</th>
                                <th width="70">Data</th>
                                <th width="150">De Gestor</th>
                                <th width="150">Para Gestor</th>
                                <th width="150">Prêmio</th>
                                <th width="150">Período de Ponto</th>
                                <th width="150">Chapa - Colaborador</th>
                                <th class="text-center" width="50">Status</th>
                                <th class="text-center" width="50">Ação</th>
                                <th class="text-center" width="0"> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($resEmprestimos): ?>
                                <?php foreach($resEmprestimos as $key => $Emprestimo): ?>
                                    <tr data-linha="<?= $Emprestimo['id'] ?>">
                                        <td class="text-left"><?= $Emprestimo['id'] ?></td>
                                        <td class="text-left"><?= date('d/m/Y', strtotime($Emprestimo['dt_solicitacao'])) ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Emprestimo['de_nome_func'].' - '.$Emprestimo['de_chapa'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Emprestimo['para_nome_func'].' - '.$Emprestimo['para_chapa'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Emprestimo['nome_premio'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Emprestimo['per_ponto_br'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Emprestimo['colaborador_nome_func'].' - '.$Emprestimo['chapa_colaborador'] ?></td>
                                        <td class="text-center">
                                        <?php
                                            switch($Emprestimo['status']){
                                                case 'A': echo '<span class="badge badge-warning">Aguardando</span>'; break;
                                                case 'R': echo '<span class="badge badge-danger">Reprovado</span>'; break;
                                                case 'E': echo '<span class="badge badge-primary">Aprovado</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <button onclick="return ver(<?= $Emprestimo['id']; ?>)" class="btn btn-soft-success waves-effect waves-light btn-xxs"><i class="fa fa-eye"></i> Ver</button>
                                                <button onclick="return excluir(<?= $Emprestimo['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs" <?php if($Emprestimo['status'] != 'A') { echo 'disabled';}?>><i class="fa fa-times"></i> Excluir</button>
                                            </div>
                                        </td>
                                        <td class="text-left" style="max-width: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Emprestimo['motivo_recusa'] ?></td>
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
        "aaSorting"         : [[0, "desc"]],
        "columnDefs": [{ 'visible': false, 'targets': [9] }]
    });
});

const ver = (id) => {
    const result = searchAndGetValues('datatable', id);
    if (result) {
        let reprovacao = "";
        if(result.col10 != "") {
            reprovacao = `<br><b>Motivo da reprovação:</b> ${result.col10}<br>`;
        }
        Swal.fire({
            title: "Dados da solicitação da cedência",
            html: `
                <b>Data:</b> ${result.col2} <br><br>
                <b>De:</b> ${result.col3} <br>
                <b>Para:</b> ${result.col4}<br><br>
                <b>Prémio:</b> ${result.col5}<br>
                <b>Perído de Ponto:</b> ${result.col6}<br><br>
                <b>Colaborador:</b> ${result.col7}<br>${reprovacao}
            `,
            confirmButtonText: `
                <i class="far fa-arrow-alt-circle-left"></i> Voltar
            `
        });
    }
}

// Funcão para buscar linha do datatable por ID e retornar dados das colunas 2 a 7
function searchAndGetValues(dataTableId, searchId) {
    // Initialize the DataTable instance
    var table = $(`#${dataTableId}`).DataTable();
    
    // Find the row where the first column matches the searchId
    var rowData = table.rows().data().filter(function(row) {
        return row[0] == searchId; // Check if the first column matches
    });

    if (rowData.length > 0) {
        // Extract values from the second and third columns
        var col2Value = rowData[0][1];
        var col3Value = rowData[0][2];
        var col4Value = rowData[0][3];
        var col5Value = rowData[0][4];
        var col6Value = rowData[0][5];
        var col7Value = rowData[0][6];
        var col10Value = rowData[0][9];
        
        // Return the values
        return {
            col2: col2Value,
            col3: col3Value,
            col4: col4Value,
            col5: col5Value,
            col6: col6Value,
            col7: col7Value,
            col10: col10Value
        };
    }

    // If the ID is not found, return null
    return null;
}

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
				url: "<?= base_url('premio/emprestimos/action/deletar') ?>",
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
loadPlugin(array('datatable','select2'));
?>