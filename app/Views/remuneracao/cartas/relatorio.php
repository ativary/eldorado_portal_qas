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
                                <a href="<?= base_url('remuneracao/cartas/novo') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <form action="<?= base_url('remuneracao/cartas/gerador') ?>" method="post" name="form_filtro" id="form_filtro" target="_blank">
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <label for="relatorio" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Ação: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8">
                                        <select class="form-control form-control-sm mb-1" name="relatorio" id="relatorio" onchange="carregaAcao(this.value);">
                                        <option value="">...</option>
                                            <?php if($perfil_rh || $perfil_global_rh || $perfil_gestor): ?>
                                                <option value="2">Requisição Carta Proposta</option>
                                            <?php endif; ?>
                                            <?php if(!$perfil_rh || $perfil_global_rh || $perfil_gestor): ?>
                                            <option value="1">Requisição Alteração</option>
                                            <option value="3">Requisição Meritocrácia</option>
                                            <!--<option value="999">Carta Global</option>-->
                                            <?php endif; ?>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php if(!$perfil_rh || $perfil_global_rh || $perfil_gestor): ?>
                            <div class="col-12 hidden rel_acao" id="rel_1">
                                <div class="row">
                                    <label for="requisicao_alteracao" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Requisição Alteração: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8">
                                        <select class="form-control form-control-sm mb-1 select2" name="requisicao_alteracao" id="requisicao_alteracao" onchange="carregaRequisicao(1);">
                                            <option value="">...</option>
                                            <?php if($resReqAlteracao): ?>
                                                <?php foreach($resReqAlteracao as $key => $ReqAlteracao): ?>
                                                    <option value="<?= $ReqAlteracao['id']; ?>"><?= $ReqAlteracao['id'].' - '.$ReqAlteracao['nome'].' - '.$ReqAlteracao['chapa']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 hidden rel_acao" id="rel_3">
                                <div class="row">
                                    <label for="requisicao_meritocracia" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Requisição Meritocrácia: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8">
                                        <select class="form-control form-control-sm mb-1" name="requisicao_meritocracia" id="requisicao_meritocracia">
                                            <option value="">...</option>
                                            <?php if($resReqMeritocracia): ?>
                                                <?php foreach($resReqMeritocracia as $key => $ReqMeritocracia): ?>
                                                    <option value="<?= $ReqMeritocracia['id']; ?>"><?= $ReqMeritocracia['id'].' - '.$ReqMeritocracia['nome'].' - '.$ReqMeritocracia['chapa']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 hidden rel_acao" id="rel_999">

                                <div class="card border mt-3">
                                    <h5 class="card-header mt-0">Parâmetros da Carta</h5>
                                
                                    <div class="card-body">
                                        <div class="row">
                                            <label for="cartas" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Cartas: <strong class="text-danger">*</strong></label>
                                            <div class="col-xl-10 col-8">
                                                <select class="form-control form-control-sm mb-1 select2" name="cartas" id="cartas" onchange="carregaRequisicao(999);">
                                                    <option value="">...</option>
                                                    <?php if($resCartasGlobal): ?>
                                                        <?php foreach($resCartasGlobal as $key => $Cartas): ?>
                                                            <option value="<?= $Cartas['id']; ?>"><?= $Cartas['id'].' - '.$Cartas['descricao']; ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="chapa" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Chapa:</label>
                                            <div class="col-xl-4 col-4">
                                                <input type="text" class="form-control" name="chapa" id="chapa" placeholder="00000000">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="ano" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Ano:</label>
                                            <div class="col-xl-4 col-4">
                                                <input type="text" class="form-control" name="ano" id="ano" placeholder="AAAA">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label for="centro_custo" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Centro de Custo:</label>
                                            <div class="col-xl-10 col-8">
                                                <select name="centro_custo" id="centro_custo" class="form-control form-control-sm mb-1 select2">
                                                    <option value="">...</option>
                                                    <?php if($resSecao): ?>
                                                        <?php foreach($resSecao as $key => $Secao): ?>
                                                            <option value="<?= $Secao['CODSECAO']; ?>"><?= $Secao['CODSECAO'].' - '.$Secao['DESCRICAO']; ?></option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <?php endif; ?>
                            <?php if($perfil_rh || $perfil_global_rh || $perfil_gestor): ?>
                            <div class="col-12 hidden rel_acao" id="rel_2">
                                <div class="row">
                                    <label for="requisicao_aumento_quadro" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Requisição Aumento de Quadro: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8">
                                        <select class="form-control form-control-sm mb-1 select2" name="requisicao_aumento_quadro" id="requisicao_aumento_quadro" onchange="carregaRequisicao(2);">
                                            <option value="">...</option>
                                            <?php if($resReqAQ): ?>
                                                <?php foreach($resReqAQ as $key => $ReqAQ): ?>
                                                    <option value="<?= $ReqAQ['id']; ?>"><?= $ReqAQ['id'].' | '.$ReqAQ['nomesecao'].' | '.$ReqAQ['nomefuncao']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="card border mt-3">
                                    <h5 class="card-header mt-0">Parâmetros da Carta</h5>
                                
                                    <div class="card-body">
                                        <div class="row">
                                            <label for="requisicao_aumento_quadro_salario" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Salário:</label>
                                            <div class="col-xl-4 col-4"><input data-money class="form-control" type="text" name="requisicao_aumento_quadro_salario" id="requisicao_aumento_quadro_salario"></div>
                                        </div>
                                        <div class="row">
                                            <label for="requisicao_aumento_quadro_data" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Data:</label>
                                            <div class="col-xl-4 col-4"><input class="form-control" type="date" name="requisicao_aumento_quadro_data" id="requisicao_aumento_quadro_data"></div>
                                        </div>
                                        <div class="row">
                                            <label for="requisicao_aumento_quadro_nome" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Nome Funcionário:</label>
                                            <div class="col-xl-10 col-8"><input class="form-control" type="text" name="requisicao_aumento_quadro_nome" id="requisicao_aumento_quadro_nome"></div>
                                        </div>
                                        <div class="row">
                                            <label for="" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0"></label>
                                            <div class="col-xl-10 col-8 mt-1"><input class="form-control-checkbox" type="checkbox" name="requisicao_aumento_quadro_moradia" id="requisicao_aumento_quadro_moradia" value="1"> <label for="requisicao_aumento_quadro_moradia">Inclusão Benefício - Hospedagem e Auxilio Moradia</label></div>
                                        </div>
                                        <div class="row">
                                            <label for="" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0"></label>
                                            <div class="col-xl-10 col-8 mt-1"><input class="form-control-checkbox" type="checkbox" name="requisicao_aumento_quadro_mudanca" id="requisicao_aumento_quadro_mudanca" value="1"> <label for="requisicao_aumento_quadro_mudanca">Inclusão Benefício - Despesas com Mudança</label></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                    </form>

                    
                    
                </div>

                <div class="card-footer text-center hidden botao_gerar">
                    <button class="btn btn-success btn-sm" id="btnsave" onclick="return gerarCarta()"><i class="mdi mdi-settings"></i> Gerar Carta</button>
                </div>

            </div><!-- end card -->


        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
    const carregaAcao = (acao) => {

        $(".rel_acao, .botao_gerar").fadeOut(0);
        $("#requisicao_alteracao, #requisicao_aumento_quadro, #requisicao_meritocracia").val(null).trigger('change');
        $("#rel_"+acao).fadeIn(100);

    }

    const carregaRequisicao = (tipo_requisicao) => {

        $(".botao_gerar").fadeIn(100);

    }


    const gerarCarta = () => {
        
        let dados = {
            "acao": $("#relatorio").val(),
            "requisicao_alteracao": $("#requisicao_alteracao").val(),
            "requisicao_aumento_quadro": $("#requisicao_aumento_quadro").val(),
            "requisicao_aumento_quadro_data": $("#requisicao_aumento_quadro_data").val(),
            "requisicao_aumento_quadro_nome": $("#requisicao_aumento_quadro_nome").val(),
            "requisicao_aumento_quadro_salario": $("#requisicao_aumento_quadro_salario").val(),
            "requisicao_meritocracia": $("#requisicao_meritocracia").val(),
            "chapa": $("#chapa").val(),
            "ano": $("#ano").val(),
            "centro_custo": $("#centro_custo").val(),
            "cartas": $("#cartas").val(),
        }

        switch(dados.acao){
            case '1': if(dados.requisicao_alteracao == ''){exibeAlerta('warning', 'Requisição não selecionada.'); return false;} break;
            case '2': 
                if(dados.requisicao_aumento_quadro == ''){exibeAlerta('warning', 'Requisição não selecionada.'); return false;} 
                //if(dados.requisicao_aumento_quadro_data == ''){exibeAlerta('warning', 'Data não informada.'); return false;} 
                //if(dados.requisicao_aumento_quadro_salario == '' || dados.requisicao_aumento_quadro_salario == '0,00'){exibeAlerta('warning', 'Salário não informado.'); return false;} 
                break;
            case '3': if(dados.requisicao_meritocracia == ''){exibeAlerta('warning', 'Requisição não selecionada.'); return false;} break;
            case '999': if(dados.chapa == '' && dados.ano == '' && dados.centro_custo == '' && dados.cartas == ''){exibeAlerta('warning', 'Nenhum parâmetro informado.'); return false;} break;
            default: exibeAlerta('warning', 'Ação não selecionada.'); return false; break;
        }

        $("#form_filtro").submit();

    }
    $(document).ready(function(){
        $("[data-money]").maskMoney({prefix:'', allowNegative: false, allowZero:false, thousands:'.', decimal:',', affixesStay: false});
    });
</script>
<?php
loadPlugin(array('select2', 'maskmoney'));
?>