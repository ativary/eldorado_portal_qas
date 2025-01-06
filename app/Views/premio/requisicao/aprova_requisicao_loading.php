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
                                <th width="70">Data</th>
                                <th width="150">Requisitante</th>
                                <th width="150">Prêmio</th>
                                <th width="150">Período de Ponto</th>
                                <th width="70">Tipo</th>
                                <th class="text-center" width="50">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            
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
    openLoading();
    window.location.replace("<?= base_url('premio/requisicao/aprova_requisicao_main') ?>");
});

</script>
<?php
loadPlugin(array('datatable','select2'));
?>