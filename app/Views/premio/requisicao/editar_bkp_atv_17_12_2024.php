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
                                <?php if($pode_editar) {?>
                                    <a href="javascript:void(0);" onclick="return processar(<?= $id_requisicao; ?>)" class="btn btn-purple btn-xxs mb-0 <?= $em_analise; ?>"><i class="fas fa-table"></i> Processar</a>
                                <?php }?>
                                <a href="<?= base_url('premio/requisicao/exportar_requisicao/'.$id_requisicao) ?>" class="btn btn-success btn-xxs mb-0"><i class="fas fa-file-excel"></i> Exportar Excel</a>
                                <?php if($pode_editar) {?>
                                    <a href="<?= base_url('premio/requisicao/importar_requisicao/'.$id_requisicao) ?>" class="btn btn-warning btn-xxs mb-0 <?= $em_analise; ?>"><i class="fas fa-file-excel"></i> Importar Excel</a>
                                <?php }?>
                                <a href="<?= base_url('premio/requisicao') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header mt-0">
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Requisitante:</h6>
                        <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['nome_requisitor'].' ('.$resRequisicao[0]['chapa_requisitor'].')'; ?></h5>
                        <h6 class="col-2 text-right mb-1 mt-1">Data Requisição:</h6>
                        <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['dt_requisicao_br']; ?></h5>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Nome do Prêmio:</h6>
                        <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['nome_premio']; ?></h5>
                        <h6 class="col-2 text-right mb-1 mt-1">Tipo Requisição:</h6>
                        <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['tipo_requisicao']; ?></h5>
                    </div>
                    <div class="row">
                        <h6 class="col-2 text-right mb-1 mt-1">Período de Ponto:</h6>
                        <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resRequisicao[0]['per_ponto_br']; ?></h5>
                        <div class="col-5 mb-1 mt-1 text-right">
                            <?php if($isAdmin and $pode_editar) {?>
                                <a href="<?= base_url('premio/requisicao/nova_chapa/'.$id_requisicao) ?>" class="btn btn-primary btn-xxs mb-0 <?= $em_analise; ?>"><i class="fas fa-plus-circle"></i> Incluir novo colaborador</a>
                            <?php }?>
                        </div>
                    </div>
                    <?php if($resGestorNaoAprovou) {?>
                        <div class="row" style="color:red;">
                        <h6 class="col-2 text-right mb-1 mt-1">Aguardando aprovação de:</h6>
                        <h5 class="col-10 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php 
                                $conta = 0;
                                foreach($resGestorNaoAprovou as $key => $gestor):
                                    if($conta > 0) {
                                        if ($conta == count($resGestorNaoAprovou) - 1) {
                                            echo ' e ';
                                        } else {
                                            echo ', ';
                                        }
                                    }
                                    echo $gestor['nome_coordenador'];
                                    $conta++;
                                endforeach; 
                            ?>
                        </h5>
                    <?php }?>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%; font-size: 12px;">                            
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Aprovador</th>
                                    <th>Chapa</th>
                                    <th>Nome</th>
                                    <th>Função</th>
                                    <th>Situação</th>
                                    <th>Admissão</th>
                                    <th>Filial</th>
                                    <th>C.Custo</th>
                                    <th>Seção</th>
                                    <th class="text-right">% Target</th>
                                    <th class="text-right">% Realiz.</th>
                                    <th class="text-center" width="50">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resReqChapas): ?>
                                <?php foreach($resReqChapas as $key => $ReqChapa): ?>
                                    <tr data-linha="<?= $ReqChapa['id'] ?>">
                                        <td class="text-center">
                                        <?php
                                            switch($ReqChapa['tipo']){
                                                case 'P': echo '<span class="badge badge-primary">&nbspPADRÃO&nbsp</span>'; break;
                                                case 'E': echo '<span class="badge badge-warning">EXCEÇÃO</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-left" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['coord_nome'] ?></td>
                                        <td class="text-left"><?= $ReqChapa['func_chapa'] ?></td>
                                        <td class="text-left" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['func_nome'] ?></td>
                                        <td class="text-left" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['funcao'] ?></td>
                                        <td class="text-left" style="max-width: 70px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['situacao'] ?></td>
                                        <td class="text-left"><?= $ReqChapa['dt_admissao_br'] ?></td>
                                        <td class="text-left"><?= $ReqChapa['codfilial'] ?></td>
                                        <td class="text-left"><?= $ReqChapa['codcusto'] ?></td>
                                        <td class="text-left" style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['secao'] ?></td>
                                        <td class="text-right"><?= $ReqChapa['target_br'] ?></td>
                                        <td class="text-right"><?= $ReqChapa['realizado_br'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="javascript:void(0);" onclick="return ver('<?= $ReqChapa['func_chapa']; ?>')" class="btn btn-soft-success waves-effect waves-light btn-xxs"><i class="fa fa-eye"></i></a>
                                                <?php if($pode_editar) {?>
                                                    <a href="<?= base_url('premio/requisicao/editar_chapa/'.$ReqChapa['id']) ?>/<?= $id_requisicao; ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline <?= $em_analise; ?>"></i></a>
                                                <?php }?>
                                                <?php if($pode_editar) {?>
                                                    <a href="javascript:void(0);" onclick="return excluir(<?= $ReqChapa['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs <?= $em_analise; ?>"><i class="fa fa-times"></i></a>
                                                <?php }?>
                                            </div>
                                        </td>
                                        <td class="text-left" style="max-width: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['obs'] ?></td>
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
        "aaSorting"         : [[0, "asc"],[1,"asc"],[2,"asc"]],
        "columnDefs": [{ 'visible': false, 'targets': [13] }]
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
				url: "<?= base_url('premio/requisicao/action/deletar_chapa') ?>",
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

const ver = (chapa) => {
    const result = searchAndGetValues('datatable', chapa);
    if (result) {
        let obs = "";
        if(result.col12 != "") {
            obs = `<br><b>Observação:</b> ${result.col12}<br>`;
        }
        
        Swal.fire({
            title: "Dados do Colaborador",
            html: `
                <b>Chapa:</b> ${result.col1} <br>
                <b>Nome:</b> ${result.col2} <br>
                <b>Tipo:</b> ${result.col3}<br><br>
                <b>Função:</b> ${result.col4}<br>
                <b>Situação / Admissão:</b> ${result.col5} / ${result.col6}<br>
                <b>Filial / C.Custo:</b> ${result.col7} / ${result.col8} <br>
                <b>Seção:</b> ${result.col9} <br><br>
                <b>% Target:</b> ${result.col10} <br>
                <b>% Realizado:</b> ${result.col11}<br>${obs}<br>
            `,
            confirmButtonText: `
                <i class="far fa-arrow-alt-circle-left"></i> Voltar
            `
        });
    }
}

// Funcão para buscar linha do datatable por ID e retornar dados das colunas 1 a 12
function searchAndGetValues(dataTableId, chapa) {
    // Initialize the DataTable instance
    var table = $(`#${dataTableId}`).DataTable();
    
    // Find the row where the first column matches the searchId
    var rowData = table.rows().data().filter(function(row) {
        return row[2] == chapa; // Check if the second column matches
    });

    if (rowData.length > 0) {
        // Extract values from the second and third columns
        var col1Value = rowData[0][2];
        var col2Value = rowData[0][3];
        var col3Value = rowData[0][0];
        var col4Value = rowData[0][4];
        var col5Value = rowData[0][5];
        var col6Value = rowData[0][6];
        var col7Value = rowData[0][7];
        var col8Value = rowData[0][8];
        var col9Value = rowData[0][9];
        var col10Value = rowData[0][10];
        var col11Value = rowData[0][11];
        var col12Value = rowData[0][13];
        
        // Return the values
        return {
            col1: col1Value,
            col2: col2Value,
            col3: col3Value,
            col4: col4Value,
            col5: col5Value,
            col6: col6Value,
            col7: col7Value,
            col8: col8Value,
            col9: col9Value,
            col10: col10Value,
            col11: col11Value,
            col12: col12Value
        };
    }

    // If the ID is not found, return null
    return null;
}

const processar = (id_requisicao) => {

    Swal.fire({
        icon: 'question',
        title: 'Esta açao limpa a requisição antes do processamento. Deseja realmente processar esta <b>requisição</b>?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,

    }).then((result) => {
        if (result.isConfirmed) {

            let dados = {"id_requisicao":id_requisicao};
            openLoading();
            
            $.ajax({
                url: "<?= base_url('premio/requisicao/action/processar') ?>",
                type:'POST',
                data:dados,
                success:function(result){
                    var response = JSON.parse(result);

                    if(response.tipo != 'success'){
                        exibeAlerta(response.tipo, response.msg, 2);
                    }else{
                        exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/requisicao/editar'); ?>/'+id_requisicao);
                    }
                },
            });

        }
    });

}

</script>
<?php
loadPlugin(array('datatable'));
?>