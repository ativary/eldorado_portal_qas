<div class="container-fluid"> 
    <div class="row">


        <div class="col-12" data-div-funcionarios>
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-8 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <div class="col-4 text-right">
                            <div class="button-items">
                                <a href="<?= base_url('hierarquia/substituto/lista'); ?>" class="btn btn-primary btn-xxs mb-0 bteldorado_7"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="col-12">
                        <div class="row">
                            <div class="col-1 mb-1">
                                <label for="nomeModulo" class="col-form-label text-left pr-0">Nome: </label>
                            </div>
                            <div class="col-8 col-xl-8">
                                <input id="nomeModulo" value="<?= $dadosModulo[0]['nome'] ?>" class="form-control form-control-sm " type="text"/>
                            </div>
                            
                            <div class="col-2 col-xl-2">
                                <input id="aprovador" class=" " type="checkbox" <?= $dadosModulo[0]['aprovador'] == 1 ? 'checked' : '' ?>/>
                                <label for="aprovador" class="col-form-label text-left pr-0">Aprovador </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="row">

                            <div class="col-xl-12 col-12">
                                <div class="input-group d-flex align-items-center row">
                                    <div class="col-1">
                                        <label for="perfis" class="col-form-label text-left pr-0">Perfis:</label>
                                    </div>
                                    <div class="col-9">
                                        <select onchange="carregarDualList(this.value)" name="perfis" id="perfis" class="select2 form-control form-control-sm">
                                        <option value="" selected> </option>
                                        <?php foreach($perfis as $key => $perfil): ?>
                                            
                                            <option value="<?=$perfil['id']?>"><?=$perfil['id']?> - <?=$perfil['nome']?></option>
                                                
                                            <?php unset($perfis[$key], $key, $perfil); ?>
                                        <?php endforeach; ?>
                                        </select>
                                        </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-12 mt-2">

                        <select multiple="multiple" size="10" id="duallistbox" class="form-control">
                            
                        <?php foreach($funcoesSelecionadas as $funcao): ?>
                            
                            <option selected value="<?= $funcao['id'] ?>" >
                                <?= $funcao['nome'] ?>
                            </option>
                        <?php endforeach; ?>

                        </select>
                    </div>

                    

                </div>
                <div class="card-footer text-center">
                        <button class="btn btn-success bteldorado_7" id="btnsave" onclick="return editarModulo(<?= $dadosModulo[0]['id'] ?>)"><i class="mdi mdi-content-save-outline"></i> Salvar</button>
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
