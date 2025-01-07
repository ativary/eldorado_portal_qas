<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-8 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <div class="col-4 text-right">
                            <div class="button-items">
                                <a href="<?= base_url('hierarquia/substituto/index/'.$idGestor); ?>" class="btn btn-primary btn-xxs mb-0 bteldorado_7"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row">
                    <?php if($rh): ?>
                        <div class="col-12">
                            <div class="row">
                                <label for="gestor" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Gestor: <strong class="text-danger">*</strong></label>
                                <div class="col-xl-10 col-10">
                                        <select name="gestor" disabled class="form-control form-control-sm mb-1">
                                            <option value="">...</option>
                                            <?php foreach($resGestores as $key => $Gestor): ?>
                                                <option value="<?= $Gestor['id']; ?>" <?= ($idGestor == $Gestor['id']) ? ' selected ' : ''; ?>><?= $Gestor['CHAPA'] .' - '. $Gestor['NOME']; ?></option>
                                                <?php unset($resGestores[$key], $key, $Gestor); ?>
                                            <?php endforeach; ?>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($idGestor != null): ?>
                        <div class="col-12">
                            <div class="row">
                                <label for="substituto" class=" col-2 col-lg-2 col-form-label text-right pr-0 pl-0">Substituto:<strong class="text-danger">*</strong></label>
                                <div class="col-xl-10 col-10">
                                    <div class="input-group">
                                        <input type="text" name="substituto_keyword" class="form-control form-control-sm col-3" placeholder="chapa / nome">
                                        <div class="input-group-prepend input-group-append bg-success">
                                            <span class="input-group-text pt-0 pb-0"><button type="button" class="btn btn-primary btn-xxs bteldorado_7" onclick="return procurarSubstituto()"><i class="fas fa-search"></i></button></span>
                                        </div>
                                        <select name="substituto" class="form-control form-control-sm"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <label for="substituto" class="col-lg-2 col-2 col-form-label text-right pr-0 pl-0">Período:<strong class="text-danger">*</strong></label>
                                <div class="col-xl-4 col-4">
                                    <div class="input-group">
                                        <input type="date" name="data_inicio"  id="data_inicio" class="form-control form-control-sm col-md-6" min="<?= date('Y-m-d'); ?>">
                                        <div class="input-group-prepend input-group-append bg-success">
                                            <span class="input-group-text pt-0 pb-0">até</span>
                                        </div>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control form-control-sm col-md-6" min="<?= date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                        
                        <?php endif; ?>
                    </div>

                    <div class="text-right"><small><strong class="text-danger">*</strong> Campo obrigatório</small></div>
                    
                </div>
            </div>
        </div><!-- end main -->

        <div class="col-12" data-div-funcionarios>
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Perfis atribuidos</h4>
                    </div>
                </div>

                <div class="card-body">

                    <div class="col-12">
                        <div class="row">

                            
                            <div class="col-xl-12 col-12">
                                <div class="input-group d-flex align-items-center row">
                                    <div class="col-1">
                                        <label for="modulo" class="col-form-label text-left pr-0">Módulo:</label>
                                    </div>
                                    <div class="col-9">
                                        <select name="modulo" id="modulo" class="select2 form-control form-control-sm">
                                        <option value="" selected> </option>
                                        <?php foreach($modulos as $key => $modulo): ?>
                                            
                                            <option value="<?=$modulo['id']?> - <?=$modulo['nome']?>"><?=$modulo['id']?> - <?=$modulo['nome']?></option>
                                                
                                            <?php unset($modulos[$key], $key, $modulo); ?>
                                        <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-2 text-center"><button onclick="adicionarPermissao()" class="btn btn-xxs bteldorado_7 btn-primary"><span><i class="fas fa-level-down-alt"></i></span>  Adicionar</button></div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="d-flex justify-content-center align-items-center">
                        <div class="col-xl-10 col-10">
                            <table id="tableModulos" class="table table-sm table-bordered mt-3">
                                <tr>
                                    <th style="width: 80%; background-color: #F3F2F7;">Perfis</th>
                                    <th class="text-center" style="background-color: #F3F2F7;">Ação</th>
                                </tr>
                             
                            </table>
                        </div>
                    </div>
                    

                </div>
                <div class="card-footer text-center">
                        <button class="btn btn-success bteldorado_7" id="btnsave" onclick="return cadastrarGestorSubstituto()"><i class="mdi mdi-content-save-outline"></i>Salvar</button>
                </div>
            </div>
        </div>
        
    </div>
</div><!-- container -->

<script>
const chapaGestor = '<?= $chapaGestor; ?>';
</script>
<script src="<?= base_url('public/app/hierarquia/substituto.js').'?v='.VERSION_JS.microtime(true); ?>"></script>
<?php
loadPlugin(array('select2', 'duallistbox'));
?>
<style>
    .bootstrap-duallistbox-container label {
        background: #006b44 !important;
        border:1px solid #006b44 !important;
    }
</style>