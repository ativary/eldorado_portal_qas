<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-9 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <?php if($idGestor != null): ?>
                            <div class="col-3 text-right">
                                <div class="button-items">
                                    <a href="<?= base_url('hierarquia/substituto/novo/'.$idGestor); ?>" class="btn btn-primary btn-xxs mb-0 bteldorado_7"><i class="fas fa-plus-circle"></i> Novo</a>
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
                                <label for="idGestor" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Gestor: <strong class="text-danger">*</strong></label>
                                <div class="col-xl-10 col-10">
                                    <form action="" method="post" name="form_gestor" id="form_gestor">
                                        <select class="select2 form-control form-control-sm mb-1" name="idGestor" id="idGestor" onchange="selecionaGestor(this.value)">
                                            <option value="">...</option>
                                            <?php foreach($resGestores as $key => $Gestor): ?>
                                                <option value="<?= $Gestor['id']; ?>" <?= ($idGestor == $Gestor['id']) ? ' selected ' : ''; ?>><?= $Gestor['NOME'].' - '.$Gestor['CHAPA']; ?></option>
                                                <?php unset($resGestores[$key], $key, $Gestor); ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($idGestor): ?>
                        <?php if($resSubstitutos): ?>
                            <?php foreach($resSubstitutos as $key1 => $Substituto): ?>
                                
                                <div class="card mb-1 ml-1 mr-1">
                                    <div class="card-body bg-secondary">
                                        <div class="row">
                                            <div class="col-7">
                                                <small class="m-0 p-0 text-success"><b>Substituto</b></small>
                                                <h4 class="m-0">
                                                    <?= $Substituto['NOME']; ?> <small>(<?= $Substituto['CHAPA_SUBSTITUTO']; ?>)</small></h4>
                                                    <?= dtBr($Substituto['DTINI']).' até '.(trim($Substituto['DTFIM']) == '2100-01-01 00:00:00.000' ? 'Indeterminado' : dtBr($Substituto['DTFIM'])) ; ?><br>
                                           
                                            </div>
                                            <div class="col-2 text-center">
                                                <small class="m-0 p-0 text-success n-mobile-cell"><b>PERMISSÕES</b></small>
                                                <h4 class="m-0"><span class="badge badge-success"><?= $Substituto['QTDPERMISSOES']; ?></span></h4>
                                            </div>  
                                            <div class="col-3 text-right align-middle outserv-align-r">
                                                <div class="btn-group">
                                                  
                                                    <a href="<?= base_url('/hierarquia/substituto/editar/'. $Substituto['ID'].'/'.$idGestor); ?>" class="btn btn-success btn-xxs bteldorado_7"><i class="mdi mdi-square-edit-outline"></i></a>
                                                    <button type="button" onclick="return inativarGestor('<?= id($Substituto['ID']); ?>')" class="btn btn-danger btn-xxs bteldorado_2"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php unset($resSubstitutos[$key1], $key1, $Substituto); ?>
                            <?php endforeach; ?>
                        <?php else: ?>

                            <div class="alert alert-warning2 border-0" role="alert">
                                <i class="dripicons-information"></i> Nenhum gestor configurado
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
<script src="<?= base_url('public/app/hierarquia/substituto.js').'?v='.VERSION_JS; ?>"></script>
<?php loadPlugin(array('select2', 'duallistbox')); ?>