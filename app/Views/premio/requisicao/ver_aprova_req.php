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
                                <a href="<?= base_url('premio/requisicao/aprova_requisicao') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
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
                        <div class="col-5 mb-1 mt-1 text-right">&nbsp;</div>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%; font-size: 12px;">                            
                            <thead>
                                <tr>
                                    <th>Filial</th>
                                    <th>Chapa</th>
                                    <th>Nome</th>
                                    <th>Admissão</th>
                                    <th>Situação</th>
                                    <th>C.Custo</th>
                                    <th>Tipo</th>
                                    <th class="text-right">% Target</th>
                                    <th class="text-right">% Realiz.</th>
                                    <?php if($isAdmin) {?>
                                        <th class="text-right">Dias Defl.</th>
                                        <th class="text-right">% Final</th>
                                        <th class="text-right">Valor Prêmio</th>
                                    <?php }?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resReqChapas): ?>
                                <?php foreach($resReqChapas as $key => $ReqChapa): ?>
                                    <tr data-linha="<?= $ReqChapa['id'] ?>">
                                        <td class="text-left"><?= $ReqChapa['codfilial'] ?></td>
                                        <td class="text-left"><?= $ReqChapa['func_chapa'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['func_nome'] ?></td>
                                        <td class="text-left"><?= $ReqChapa['dt_admissao_br'] ?></td>
                                        <td class="text-left" style="max-width: 70px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $ReqChapa['situacao'] ?></td>
                                        <td class="text-left"><?= $ReqChapa['codcusto'] ?></td>
                                        <td class="text-center">
                                        <?php
                                            switch($ReqChapa['tipo']){
                                                case 'P': echo '<span class="badge badge-primary">&nbspPADRÃO&nbsp</span>'; break;
                                                case 'E': echo '<span class="badge badge-warning">EXCEÇÃO</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-right"><?= $ReqChapa['target_br'] ?></td>
                                        <td class="text-right"><?= $ReqChapa['realizado_br'] ?></td>
                                        <?php if($isAdmin) {?>
                                            <td class="text-right"><?= $ReqChapa['dias_defla'] ?></td>
                                            <td class="text-right"><?= $ReqChapa['resultado_br'] ?></td>
                                            <td class="text-right"><?= $ReqChapa['valor_premio_br'] ?></td>
                                        <?php } ?>
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
        "aLengthMenu"       : [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 10,
        "aaSorting"         : [[0, "asc"],[1,"asc"],[2,"asc"]],
        "scrollX"           : true
    });
});
</script>
<?php
loadPlugin(array('datatable'));
?>