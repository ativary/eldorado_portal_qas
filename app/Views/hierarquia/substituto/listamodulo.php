<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-9 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <div class="col-3 text-right">
                                <div class="button-items">
                                    <a href="<?= base_url('hierarquia/substituto/novomodulo'); ?>" class="btn btn-primary btn-xxs mb-0 bteldorado_7"><i class="fas fa-plus-circle"></i> Novo</a>
                                </div>
                            </div>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="col-xl-12 col-12">
                        <table class="table table-sm table-bordered mt-3">
                            <tr>
                                <th style="width: 60%; background-color: #F3F2F7"> Perfis </th>
                                <th class="text-center" style="width: 15%; background-color: #F3F2F7"> Quantidade de Funções </th>
                                <th class="text-center" style="width: 15%; background-color: #F3F2F7"> Aprovador? </th>
                                <th class="text-center" style="background-color: #F3F2F7"> Ação </th>

                            </tr>

                            <?php foreach($modulosExistentes as $modulo): ?>
                                <tr>
                                    <td><?= $modulo['id'] ?> - <?= $modulo['nome'] ?></td>
                                    <td class="text-center"><?= $modulo['qtd_funcoes'] ?> </td>
                                    <td class="text-center"><?= ($modulo['aprovador'] == 1) ? 'Sim' : 'Não' ?> </td>
                                    <td class="text-center"> 
                                    <a href="<?= base_url('/hierarquia/substituto/editarmodulo/'. $modulo['id']); ?>" class="btn btn-success btn-xxs bteldorado_7"><i class="mdi mdi-square-edit-outline"></i></a>
                                        <button onclick="excluirModulo(<?= $modulo['id'] ?> )" class="btn btn-xxs btn-danger "><span><i class="fas fa-trash"></i></span></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        </table>
                    </div>


                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<style>
.outserv-align-r {
    display: inline-flex;
    flex-wrap: nowrap;
    align-content: space-around;
    align-items: center;
    justify-content: flex-end;
}
</style>
<script src="<?= base_url('public/app/hierarquia/substituto.js').'?v='.VERSION_JS; ?>"></script>
<?php loadPlugin(array('select2', 'duallistbox')); ?>