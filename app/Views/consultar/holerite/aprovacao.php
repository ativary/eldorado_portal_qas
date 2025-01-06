<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive mb-0" data-pattern="priority-columns">
                    <table id="datatable" class="table table-bordered  dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="70">ID MOVIMENTO</th>
                                    <th class="text-center">Nº MOVIMENTO</th>
                                    <th class="text-center">VALOR BRUTO</th>
                                    <th>CENTRO DE CUSTO</th>
                                    <th class="text-center">DATA EMISSÃO</th>
                                    <th class="text-center" width="100">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $linha = 12354; ?>
                                <?php if($centro_custo): ?>
                                    <?php foreach($centro_custo as $key => $CentroCusto): ?>
                                        <?php
                                        $valores = array(
                                            0 => "1.458,134,00",
                                            1 => "4.782,00",
                                            2 => "145.476,31",
                                            3 => "77.997,00",
                                            4 => "1.357,00",
                                            5 => "3.478.001,11"
                                        );
                                        ?>
                                        <tr>
                                            <td data-priority="1" class="text-center"><?= $linha; ?></td>
                                            <td data-priority="6" class="text-center">1.1.10</td>
                                            <td data-priority="1" class="text-center"><?= $valores[rand(0, 5)]; ?></td>
                                            <td data-priority="6"><?= $CentroCusto['NOME']; ?></td>
                                            <td class="text-center">27/05/2022</td>
                                            <td data-priority="1" class="text-center">
                                                <div class="btn-group" aria-label="acao" role="group">
                                                    <a title="Excluir" href="javascript:void(0);" class="btn btn-soft-danger waves-effect waves-light btn-xxs"><i class="fas fa-thumbs-down"></i> Reprovar</a>
                                                    <a title="Seção" href="javascript:void(0);" class="btn btn-soft-success waves-effect waves-light btn-xxs"><i class="fas fa-thumbs-up"></i> Aprovar</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $linha++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                        </tbody>
                    </table>
                    </div>

                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 50,
        "aaSorting"         : [[0, "desc"]]
    });
    
});
</script>
<?php
loadPlugin(array('datatable'));
?>