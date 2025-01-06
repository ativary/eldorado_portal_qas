<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-9 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <?php if($chapaGestor != null): ?>
                            <div class="col-3 text-right">
                                <div class="button-items">
                                    <a href="<?= base_url('hierarquia/lider/novo/'.$chapaGestor); ?>" class="btn btn-primary btn-xxs mb-0 bteldorado_7"><i class="fas fa-plus-circle"></i> Novo</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <?php if($rh): ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="row">
                                <label for="chapaGestor" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Gestor: <strong class="text-danger">*</strong></label>
                                <div class="col-xl-10 col-10">
                                    <form action="" method="post" name="form_gestor" id="form_gestor">
                                        <select class="select2 form-control form-control-sm mb-1" name="chapaGestor" id="chapaGestor" onchange="selecionaGestor(this.value)">
                                            <option value="">...</option>
                                            <?php foreach($resGestores as $key => $Gestor): ?>
                                                <option value="<?= $Gestor['CHAPA']; ?>" <?= ($chapaGestor == $Gestor['CHAPA']) ? ' selected ' : ''; ?>><?= $Gestor['NOME'].' - '.$Gestor['CHAPA']; ?></option>
                                                <?php unset($resGestores[$key], $key, $Gestor); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($chapaGestor): ?>
                        <?php if($resHierarquiaLider): ?>
                            <?php foreach($resHierarquiaLider as $key1 => $HierarquiaLider): ?>
                                
                                <div class="card mb-1 ml-1 mr-1">
                                    <div class="card-body bg-secondary">
                                        <div class="row">
                                            <div class="col-7">
                                                <small class="m-0 p-0 text-primary"><b>LÍDER</b></small>
                                                <h4 class="m-0">
                                                    <?= $HierarquiaLider['NOME']; ?> <small>(<?= $HierarquiaLider['CHAPA']; ?>)</small></h4>
                                                    <?= dtBr($HierarquiaLider['PERINI']).' até '.(strlen(trim($HierarquiaLider['PERFIM'])) > 0 ? dtBr($HierarquiaLider['PERFIM']) : 'Indeterminado'); ?><br>
                                                    <?= (($HierarquiaLider['APROVADOR'] == 'S') ? ' <span class="badge badge-info"><i class="mdi mdi-check-decagram"></i> aprovador</span>' : ''); ?>
                                                    <span class="badge badge-dark mt-0 "><?= ($HierarquiaLider['OPERACAO']) ? '<i class="fas fa-user-tag"></i> '.$HierarquiaLider['OPERACAO'].'<br>' : ''; ?></span>
                                            </div>
                                            <div class="col-2 text-center">
                                                <small class="m-0 p-0 text-primary n-mobile-cell"><b>FUNCIONÁRIOS</b></small>
                                                <small class=" text-center m-0 p-0 text-primary d-none y-mobile-cell"><b>FUNC.</b></small>
                                                <h4 class="m-0"><span class="badge badge-success"><?= $HierarquiaLider['QTDE_FUNCIONARIOS']; ?></span></h4>
                                            </div>
                                            <div class="col-3 text-right align-middle outserv-align-r">
                                                <div class="btn-group">
                                                    <a href="<?= base_url('/hierarquia/lider/editar/'.id($HierarquiaLider['id'])); ?>" class="btn btn-primary btn-xxs bteldorado_7"><i class="mdi mdi-square-edit-outline"></i></a>
                                                    <button type="button" onclick="return removerLider('<?= id($HierarquiaLider['id']); ?>')" class="btn btn-danger btn-xxs bteldorado_2"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php unset($resHierarquiaLider[$key1], $key1, $HierarquiaLider); ?>
                            <?php endforeach; ?>
                        <?php else: ?>

                            <div class="alert alert-warning2 border-0" role="alert">
                                <i class="dripicons-information"></i> Nenhuma equipe configurada
                            </div>

                        <?php endif; ?>
                    <?php endif; ?>


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
<script src="<?= base_url('public/app/hierarquia/lider.js').'?v='.VERSION_JS; ?>"></script>
<?php loadPlugin(array('select2')); ?>