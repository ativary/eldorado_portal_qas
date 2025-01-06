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
                                <a href="<?= base_url('hierarquia/lider/index/'.$chapaGestor); ?>" class="btn btn-primary btn-xxs mb-0 bteldorado_7"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row">
                        <?php if($rh): ?>
                        <div class="col-12">
                            <div class="row">
                                <label for="gestor" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Gestor:<strong class="text-danger">*</strong></label>
                                <div class="col-xl-10 col-10">
                                    <form action="" method="post" name="form_gestor" id="form_gestor">
                                        <select class="select2 form-control form-control-sm mb-1" name="chapaGestor" id="chapaGestor" onchange="seleionaGestor(this.value)">
                                            <option value="">...</option>
                                            <?php foreach($resGestores as $key => $Gestor): ?>
                                                <option value="<?= $Gestor['CHAPA']; ?>" <?= ($chapaGestor == $Gestor['CHAPA']) ? ' selected ' : ''; ?>><?= $Gestor['NOME'].' - '.$Gestor['CHAPA']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($chapaGestor != null): ?>
                        <div class="col-12">
                            <div class="row">
                                <label for="lider" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Líder:<strong class="text-danger">*</strong></label>
                                <div class="col-xl-10 col-10">
                                    <div class="input-group">
                                        <input type="text" name="lider_keyword" class="form-control form-control-sm col-3" placeholder="chapa / nome">
                                        <div class="input-group-prepend input-group-append bg-success">
                                            <span class="input-group-text pt-0 pb-0"><button type="button" class="btn btn-primary btn-xxs bteldorado_7" onclick="return procurarLider()"><i class="fas fa-search"></i></button></span>
                                        </div>
                                        <select name="lider" class="form-control form-control-sm" onchange="selecionaLider(this.value)"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <label for="lider" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Período:<strong class="text-danger">*</strong></label>
                                <div class="col-xl-4 col-4">
                                    <div class="input-group">
                                        <input type="date" name="periodo_inicio" class="form-control form-control-sm col-md-6" min="<?= date('Y-m-d'); ?>">
                                        <div class="input-group-prepend input-group-append bg-success">
                                            <span class="input-group-text pt-0 pb-0">até</span>
                                        </div>
                                        <input type="date" name="periodo_termino" class="form-control form-control-sm col-md-6" min="<?= date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <label for="operacao" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Operação:</label>
                                <div class="col-xl-4 col-4">
                                    <input type="text" name="operacao" class="form-control form-control-sm" value="">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="text-right"><small><strong class="text-danger">*</strong> Campo obrigatório</small></div>
                    
                </div>
            </div>
        </div><!-- end main -->

        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Atribuir Funcionários ao Líder</h4>
                    </div>
                </div>

                <div class="card-body">

                    <div class="row">
                        <div class="col-12">
                            <div class="row">
                                <label for="secao" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Seção:</label>
                                <div class="col-xl-10 col-10">
                                    <select data-secao class="select2 mb-3 select2-multiple form-control-sm" name="secao[]" style="width: 100%" multiple="multiple" data-placeholder="- Todos -">
                                        <option value="">...</option>
                                        <?php if($resSecaoGestor): ?>
                                            <?php foreach($resSecaoGestor as $key => $SecaoGestor): ?>
                                                <option value="<?= $SecaoGestor['codsecao']; ?>"><?= $SecaoGestor['descricao']. ' - '.$SecaoGestor['codsecao']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="row">
                                <label for="funcao" class="col-xl-2 col-2 col-form-label text-right pr-0 pl-0">Função:</label>
                                <div class="col-xl-10 col-10">
                                    <select data-funcao class="select2 mb-3 select2-multiple form-control-sm" name="funcao[]" style="width: 100%" multiple="multiple" data-placeholder="- Todos -">
                                        <option value=" ">- Todos -</option>
                                        <?php if($resFuncaoGestor): ?>
                                            <?php foreach($resFuncaoGestor as $key => $FuncaoGestor): ?>
                                                <option value="<?= $FuncaoGestor['CODIGO']; ?>"><?= $FuncaoGestor['NOME']. ' - '.$FuncaoGestor['CODIGO']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-center mt-1">
                            <button class="btn btn-primary btn-xxs bteldorado_7" onclick="exibeFuncionarios()"><i class="fas fa-search"></i> Exibir Funcionários</button>
                        </div>
                    </div>


                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="row">
                                <div class="col-xl-12 col-12">
                                    <select class="funcionarios" name="funcionarios" multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success bteldorado_7" id="btnsave" onclick="return cadastrarLider()"><i class="mdi mdi-content-save-outline"></i> Cadastrar Líder</button>
                </div>
            </div>
        </div>
        
        
    </div>
</div><!-- container -->

<script>
const chapaGestor = '<?= $chapaGestor; ?>';
</script>
<script src="<?= base_url('public/app/hierarquia/lider.js').'?v='.VERSION_JS.microtime(true); ?>"></script>
<?php
loadPlugin(array('select2', 'duallistbox'));
?>
<style>
    .bootstrap-duallistbox-container label {
        background: #006b44 !important;
        border:1px solid #006b44 !important;
    }
</style>