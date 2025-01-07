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
                                                <option selected value="<?= $dadosSubstituicao[0]['chapa_gestor']; ?>"><?= $dadosSubstituicao[0]['nome_gestor'].' - '.$dadosSubstituicao[0]['chapa_gestor']; ?></option>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($dadosSubstituicao[0]['chapa_substituto'] != null): ?>
                        <div class="col-12">
                            <div class="row">
                                <label for="substituto" readonly class=" col-2 col-lg-2 col-form-label text-right pr-0 pl-0">Gestor substituto:<strong class="text-danger">*</strong></label>
                                <div class="col-xl-10 col-10">
                                    <div class="input-group">
                                        <input readonly type="text" value="<?= $dadosSubstituicao[0]['chapa_substituto'] ?>" name="substituto_keyword" class="form-control form-control-sm col-3" placeholder="chapa / nome">
                                        <div class="input-group-prepend input-group-append bg-success">
                                            <span class="input-group-text pt-0 pb-0"><button type="button" class="btn btn-primary btn-xxs bteldorado_7" onclick="return procurarSubstituto()"><i class="fas fa-search"></i></button></span>
                                        </div>
                                        <select disabled name="substituto" class="form-control form-control-sm">
                                            <option value="<?= $dadosSubstituicao[0]['id_substituto'] ?>"> <?= $dadosSubstituicao[0]['nome_sub'].' - '.$dadosSubstituicao[0]['chapa_substituto']; ?> </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <label for="substituto" class="col-lg-2 col-2 col-form-label text-right pr-0 pl-0">Período:<strong class="text-danger">*</strong></label>
                                <div class="col-xl-4 col-4">
                                    <div class="input-group">
                                        <input type="date" name="data_inicio" value="<?= date('Y-m-d', strtotime($dadosSubstituicao[0]['dtini'])) ?>" id="data_inicio" class="form-control form-control-sm col-md-6" min="<?= date('Y-m-d', strtotime($dadosSubstituicao[0]['dtini'])); ?>">
                                        <div class="input-group-prepend input-group-append bg-success">
                                            <span class="input-group-text pt-0 pb-0">até</span>
                                        </div>
                                        <input type="date" name="data_fim" value="<?=  date('Y-m-d', strtotime($dadosSubstituicao[0]['dtfim'])) ?>" id="data_fim" class="form-control form-control-sm col-md-6" min="<?= date('Y-m-d', strtotime($dadosSubstituicao[0]['dtini'])); ?>">
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
                        <h4 class="col-12 mb-1 mt-1">Módulos atribuidos</h4>
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
                                <?php foreach($modulosSelecionados as $moduloSel): ?>
                                    <tr>
                                        <td><?= $moduloSel['id'] ?> - <?= $moduloSel['nome'] ?></td>
                                        <td class="text-center"> <button onclick="removerLinha(this)" class="btn btn-xxs btn-soft-danger "><span><i class="fas fa-trash"></i></span> Remover</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>

                    <!--<div class="col-12 mt-2">
                        <select multiple="multiple" size="10" id="duallistbox" class="form-control">
                            
                        <?php foreach($funcoesSelecionadas as $funcao): ?>
                            <option selected value="<?= $funcao['id'] ?>" >
                                <?= $funcao['nome'] ?>
                            </option>
                        <?php endforeach; ?>

                        </select>
                    </div>-->

                    

                </div>
                <div class="card-footer text-center">
                        <button class="btn btn-success bteldorado_7" id="btnsave" onclick="return atualizarGestorSubstituto(<?= $dadosSubstituicao[0]['id']; ?>, <?= $idGestor; ?>)"><i class="mdi mdi-content-save-outline"></i> Salvar</button>
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
