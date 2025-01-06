<script>
$(document).ready(function(){
    $(".button-menu-mobile").click();
});
</script>
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

                    <?= exibeMensagem(true); ?>

                    <form action="" method="post" name="form_filtro" id="form_filtro">
                        <input type="hidden" name="salario_manual" id="salario_manual" value="<?= $salario_manual; ?>">
                        <input type="hidden" name="he50_manual" id="he50_manual" value="<?= $he50_manual; ?>">
                        <input type="hidden" name="he80_manual" id="he80_manual" value="<?= $he80_manual; ?>">
                        <input type="hidden" name="he100_manual" id="he100_manual" value="<?= $he100_manual; ?>">
                        <input type="hidden" name="adicional_noturno" id="adicional_noturno" value="<?= $adicional_noturno; ?>">
                        <input type="hidden" name="demaiscustos_manual" id="demaiscustos_manual" value="<?= $demaiscustos_manual; ?>">
                        <input type="hidden" name="demaiscustos_manual_atual" id="demaiscustos_manual_atual" value="<?= $demaiscustos_manual_atual; ?>">
                        <input type="hidden" name="he_dsr" id="he_dsr" value="<?= $he_dsr; ?>">
                        <input type="hidden" name="premio_producao" id="premio_producao" value="<?= $premio_producao; ?>">
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <label for="acao" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0">Ação: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8">
                                        <select class="form-control form-control-sm mb-1" name="acao" id="acao" onchange="carregaAcao(this.value);">
                                            <option value="">...</option>
                                            <?php if(!$perfil_recrutamento || $perfil_gestor || $perfil_global_rh): ?><option value="A" <?= ($acao == "A") ? "selected" : ""; ?>>Extrato</option><?php endif; ?>
                                            <?php if(!$perfil_recrutamento || $perfil_gestor || $perfil_global_rh): ?><option value="P" <?= ($acao == "P") ? "selected" : ""; ?>>Promoção</option><?php endif; ?>
                                            <?php if($perfil_recrutamento || $perfil_gestor || $perfil_global_rh): ?><option value="R" <?= ($acao == "R") ? "selected" : ""; ?>>Recrutamento</option><?php endif; ?>
                                            <?php if(!$perfil_recrutamento || $perfil_gestor || $perfil_global_rh): ?><option value="S" <?= ($acao == "S") ? "selected" : ""; ?>>Simulação</option><?php endif; ?>
                                        </select>
                                    </div>

                                    <label for="chapa" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0 data_filtro_1 <?= (($acao == 'A' || $acao == 'P') && $acao != null) ? "" : "hidden"; ?>">Colaborador:</label>
                                    <div class="col-xl-10 col-8 data_filtro_1 <?= (($acao == 'A' || $acao == 'P') && $acao != null) ? "" : "hidden"; ?>">
                                        <form action="" method="post" name="filtro_form" id="filtro_form">
                                            <select class="select2 custom-select form-control form-control-sm" name="chapa" id="chapa" >
                                                <option value="">- selecione o funcionário -</option>
                                                <?php $dadosFunc = false; ?>
                                                <?php if($resFuncionarios): ?>
                                                    <?php foreach($resFuncionarios as $key => $Funcionario): ?>
                                                        <?php if($chapa == $Funcionario['CHAPA']) $dadosFunc = $Funcionario; ?>
                                                        <option value="<?= $Funcionario['CHAPA']; ?>" <?= ($chapa == $Funcionario['CHAPA']) ? " selected " : ""; ?>><?= $Funcionario['NOME'].' - '.$Funcionario['CHAPA']; ?></option>
                                                        <?php unset($Funcionario); unset($resFuncionarios[$key]); ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </form>
                                    </div>

                                    <label for="posicao_destino" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0 data_filtro_2  <?= ($acao == "P") ? "" : "hidden"; ?>">Posição Nova: </label>
                                    <div class="col-xl-10 col-8 data_filtro_2  <?= ($acao == "P") ? "" : "hidden"; ?>">
                                        <select class="select2 mb-3" name="posicao_destino" id="posicao_destino" style="width: 100%" data-placeholder="- Todos -">
                                            <option value="">...</option>
                                            <?php if($resPosicao): ?>
                                                <?php foreach($resPosicao as $key => $Posicao): ?>
                                                    <?php if($posicao_destino == $Posicao['codposicao']) $dadosPosicao = $Posicao; ?>
                                                    <option value="<?= $Posicao['codposicao'] ?>" <?= ($posicao_destino == $Posicao['codposicao']) ? " selected " : ""; ?>><?= $Posicao['codposicao'].' | '.$Posicao['codsecao'].' - '.$Posicao['nomesecao'].' | '.$Posicao['nomefuncao'].(strlen(trim($Posicao['chapa'] ?? '') > 0) ? " - [{$Posicao['chapa']} - {$Posicao['NOMEFUNCIONARIO']}]" : ""); ?></option>
                                                    <?php unset($Posicao); unset($resPosicao[$key]); ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <label for="posicao_recrutamento" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0 data_filtro_4  <?= ($acao == "R") ? "" : "hidden"; ?>">Requisição AQ: </label>
                                    <div class="col-xl-10 col-8 data_filtro_4  <?= ($acao == "R") ? "" : "hidden"; ?>">
                                        <select class="select2 mb-3" name="posicao_recrutamento" id="posicao_recrutamento" style="width: 100%" data-placeholder="- Todos -">
                                            <option value="">...</option>
                                            <?php if($resPosicaoRequisicao): ?>
                                                <?php foreach($resPosicaoRequisicao as $key => $PosicaoReq): ?>
                                                    <?php if($posicao_recrutamento == $PosicaoReq['codposicao']) $dadosPosicao = $PosicaoReq; ?>
                                                    <option value="<?= $PosicaoReq['codposicao'] ?>" <?= ($posicao_recrutamento == $PosicaoReq['codposicao']) ? " selected " : ""; ?>><?= $PosicaoReq['codposicao'].' | '.$PosicaoReq['codsecao'].' - '.$PosicaoReq['nomesecao'].' | '.$PosicaoReq['nomefuncao'].' | Req. Nº: '.$PosicaoReq['id_requisicao']; ?></option>
                                                    <?php unset($PosicaoReq); unset($resPosicaoRequisicao[$key]); ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <label for="filial" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0 data_filtro_3 <?= ($acao == "S" && $acao != null) ? "" : "hidden"; ?>">Filial: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8 data_filtro_3 <?= ($acao == "S" && $acao != null) ? "" : "hidden"; ?>">
                                        <select class="select2 mb-3" name="filial" id="filial" style="width: 100%" data-placeholder="Selecione uma filial" onchange="carregaFilialSecao(this.value)">
                                            <option value="">...</option>
                                            <?php if($resFilial): ?>
                                                <?php foreach($resFilial as $key => $Filial): ?>
                                                    <?php #if($posicao_destino == $Filial['codposicao']) $dadosPosicao = $Filial; ?>
                                                    <option value="<?= $Filial['CODFILIAL']; ?>" <?= ($filial == $Filial['CODFILIAL']) ? " selected " : ""; ?>><?= $Filial['CODFILIAL'].' - '.$Filial['DESCRICAO']; ?></option>
                                                    <?php unset($Filial); unset($resFilial[$key]); ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <label for="secao" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0 data_filtro_3 <?= ($acao == "S" && $acao != null) ? "" : "hidden"; ?>">Seção: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8 data_filtro_3 <?= ($acao == "S" && $acao != null) ? "" : "hidden"; ?>">
                                        <select class="select2 mb-3" name="secao" id="secao" style="width: 100%" data-placeholder="Selecione uma seção">
                                            <option value="">...</option>
                                            <?php if(is_array($resSecao)): ?>
                                                <?php if($resSecao): ?>
                                                    <?php foreach($resSecao as $key => $Secao): ?>
                                                        <?php #if($posicao_destino == $Filial['codposicao']) $dadosPosicao = $Filial; ?>
                                                        <option value="<?= $Secao['CODIGO']; ?>" <?= ($secao == $Secao['CODIGO']) ? " selected " : ""; ?>><?= $Secao['CODIGO'].' - '.$Secao['DESCRICAO']; ?></option>
                                                        <?php unset($Secao); unset($resSecao[$key]); ?>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <label for="funcao" class="col-xl-2 col-4 col-form-label text-right pr-0 pl-0 data_filtro_3 <?= ($acao == "S" && $acao != null) ? "" : "hidden"; ?>">Função: <strong class="text-danger">*</strong></label>
                                    <div class="col-xl-10 col-8 data_filtro_3 <?= ($acao == "S" && $acao != null) ? "" : "hidden"; ?>">
                                        <select class="select2 mb-3" name="funcao" id="funcao" style="width: 100%" data-placeholder="Selecione uma função">
                                            <option value="">...</option>
                                            <?php if($resFuncao): ?>
                                                <?php foreach($resFuncao as $key => $Funcao): ?>
                                                    <?php #if($posicao_destino == $Filial['codposicao']) $dadosPosicao = $Filial; ?>
                                                    <option value="<?= $Funcao['CODIGO']; ?>" <?= ($funcao == $Funcao['CODIGO']) ? " selected " : ""; ?>><?= $Funcao['CODIGO'].' - '.$Funcao['NOME']; ?></option>
                                                    <?php unset($Funcao); unset($resFuncao[$key]); ?>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return executaSimulacao()"><i class="mdi mdi-play-circle-outline"></i> Executar simulação</button>
                </div>
            </div><!-- end card -->

            <?php if($dados['acao'] !== null): ?>
            <div class="card data_resultado">
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1">Resultado da simulação</h4>
                    </div>
                </div>
                <div class="card-body" id="html_pdf">

                    <div class="row mb-4">
                        <?php if($chapa != null): ?>
                        <div class="col-6">
                            <table class="table table-bordered mb-0 table-centered table-sm table-striped" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <tr>
                                    <td colspan="2" class="text-center" style="background: #e0e0e0;"><strong>Dados do Colaborador</strong></td>
                                </tr>
                                <tr>
                                    <td width="100" style="background: #f0f0f0;"><strong>Função:</strong></td>
                                    <td><?= $dadosFunc['NOMEFUNCAO'].' - '.$dadosFunc['CODFUNCAO']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background: #f0f0f0;"><strong>Seção:</strong></td>
                                    <td><?= $dadosFunc['NOMESECAO'].' - '.$dadosFunc['CODSECAO']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background: #f0f0f0;"><strong>Filial:</strong></td>
                                    <td><?= $dadosFunc['NOMEFILIAL'].' - '.$dadosFunc['CODFILIAL']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <?php endif; ?>

                        <?php if($dados['acao'] == 'P' || $dados['acao'] == 'R' || $dados['acao'] == 'S'): ?>
                        <div class="col-6">
                            <table class="table table-bordered mb-0 table-centered table-sm table-striped" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <tr>
                                    <td colspan="2" class="text-center" style="background: #e0e0e0;"><strong>Dados da Posição Nova</strong></td>
                                </tr>
                                <tr>
                                    <td width="100" style="background: #f0f0f0;"><strong>Função:</strong></td>
                                    <td><?= $resPrevisao[0]['NOMEFUNCAO'].' - '.$resPrevisao[0]['CODFUNCAO']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background: #f0f0f0;"><strong>Seção:</strong></td>
                                    <td><?= $resPrevisao[0]['NOMESECAO'].' - '.$resPrevisao[0]['CODSECAO']; ?></td>
                                </tr>
                                <tr>
                                    <td style="background: #f0f0f0;"><strong>Filial:</strong></td>
                                    <td><?= $resPrevisao[0]['NOMEFILIAL'].' - '.$resPrevisao[0]['CODFILIAL']; ?></td>
                                </tr>
                            </table>
                        </div>
                        <?php endif; ?>

                    </div>

                    <?php
                    $remuneracao_total_atual = 0;
                    $remuneracao_total_calc = 0;
                    $individual_total_atual = 0;
                    $individual_total_calc = 0;
                    $linha = 0;
                    ?>
                    <table class="table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="background: #ffffff;border-left: 1px solid #ffffff;border-top: 1px solid #ffffff;"></th>
                                <th class="text-center" >Contas</th>
                                <th class="text-center" width="200">Índice/Valor</th>
                                <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><th class="text-center" width="150">ATUAL</th><?php endif; ?>
                                <?php if($dados['acao'] != 'A'): ?>
                                <th class="text-center" width="150">SIMULADO</th>
                                <?php if($dados['acao'] == 'P'): ?>
                                <th class="text-center" >%</th>
                                <th class="text-center" >R$</th>
                                <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($dados['acao'] != '__A'): ?>

                                <!-- layout padrão -->
                                <tr>
                                    <td class="text-center bg-primary text-white" rowspan="14" width="100"><p class="quebra_letra">REMUNERAÇÃO</p></td>
                                </tr>
                                <tr>
                                    <td class="text-left <?= ($salario_manual != "") ? ' text-danger' : ""; ?>" data-item="REMUNERACAO">Salário - Mensal</td>
                                    <td class="text-center <?= ($salario_manual != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= (($salario_manual ?? '') != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['SALARIO_MENSAL'] ?? '') : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>

                                    <?php if(strlen(trim($resPrevisao[0]['salario_isonomia'] ?? '')) > 0 && ($resPrevisao[0]['isonomia'] ?? 'nao') == 'sim' && $salario_manual == ""): ?>
                                        <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= moeda($resPrevisao[0]['SALARIO_MENSAL']); ?></td>
                                    <?php else: ?>
                                        <td class="text-center <?= ($salario_manual != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><input data-money="<?= $linha; ?>" type="text" class="form-control form-control-xxs text-center" value="<?= moeda($resPrevisao[0]['SALARIO_MENSAL']); ?>"></td>
                                    <?php endif; ?>

                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center <?= ($salario_manual != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['SALARIO_MENSAL'] ?? '', $resPrevisao[0]['SALARIO_MENSAL']) : ""; ?></td>
                                        <td class="text-center <?= ($salario_manual != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['SALARIO_MENSAL'] - $resCalculo[0]['SALARIO_MENSAL'] ?? '') : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['SALARIO_MENSAL'] ?? 0);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['SALARIO_MENSAL']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO">Adicional Tritrem</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['ADIC_TRITREM'] ?? '') : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['ADIC_TRITREM']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['ADIC_TRITREM'], $resPrevisao[0]['ADIC_TRITREM']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['ADIC_TRITREM'] - $resCalculo[0]['ADIC_TRITREM']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['ADIC_TRITREM'] ?? 0);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['ADIC_TRITREM'] ?? 0);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO">Periculosidade</td>
                                    <td class="text-center" style="background: #fffeed;">30% do salário</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['PERICULOSIDADE'] ?? 0) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['PERICULOSIDADE'] ?? 0) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['PERICULOSIDADE'], $resPrevisao[0]['PERICULOSIDADE']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['PERICULOSIDADE'] - $resCalculo[0]['PERICULOSIDADE']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['PERICULOSIDADE'] ?? 0);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['PERICULOSIDADE'] ?? 0);
                                ?>
                                <tr>
                                    <td class="text-left <?= ($premio_producao != "") ? ' text-danger' : ""; ?>" data-item="REMUNERACAO">Prêmio Produção</td>
                                    <td class="text-center <?= ($premio_producao != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= ($premio_producao != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['PREMIO_PRODUCAO'] ?? 0) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center <?= ($premio_producao != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? '<input data-money="'.$linha.'" type="text" class="form-control form-control-xxs text-center" value="'.moeda($resPrevisao[0]['PREMIO_PRODUCAO']).'">' : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center <?= ($premio_producao != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['PREMIO_PRODUCAO'], $resPrevisao[0]['PREMIO_PRODUCAO']) : ""; ?></td>
                                        <td class="text-center <?= ($premio_producao != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['PREMIO_PRODUCAO'] - $resCalculo[0]['PREMIO_PRODUCAO']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['PREMIO_PRODUCAO']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['PREMIO_PRODUCAO']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO">Adicional Assiduidade</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['ADIC_ASSIDUIDADE']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['ADIC_ASSIDUIDADE']): ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['ADIC_ASSIDUIDADE'], $resPrevisao[0]['ADIC_ASSIDUIDADE']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['ADIC_ASSIDUIDADE'] - $resCalculo[0]['ADIC_ASSIDUIDADE']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['ADIC_ASSIDUIDADE']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['ADIC_ASSIDUIDADE']);
                                ?>
                                <tr>
                                    <td class="text-left <?= ($he50_manual != "") ? ' text-danger' : ""; ?>" data-item="REMUNERACAO">Média Horas Extras 50%</td>
                                    <td class="text-center <?= ($he50_manual != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= ($he50_manual != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['MEDIA_HORA_EXTRA_50']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center <?= ($he50_manual != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? '<input data-money="'.$linha.'" type="text" class="form-control form-control-xxs text-center" value="'.moeda($resPrevisao[0]['MEDIA_HORA_EXTRA_50']).'">' : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center <?= ($he50_manual != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['MEDIA_HORA_EXTRA_50'], $resPrevisao[0]['MEDIA_HORA_EXTRA_50']) : ""; ?></td>
                                        <td class="text-center <?= ($he50_manual != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['MEDIA_HORA_EXTRA_50'] - $resCalculo[0]['MEDIA_HORA_EXTRA_50']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['MEDIA_HORA_EXTRA_50']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['MEDIA_HORA_EXTRA_50']);
                                ?>
                                <tr>
                                    <td class="text-left <?= ($he80_manual != "") ? ' text-danger' : ""; ?>" data-item="REMUNERACAO">Média Horas Extras 80%</td>
                                    <td class="text-center <?= ($he80_manual != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= ($he80_manual != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['MEDIA_HORA_EXTRA_80']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center <?= ($he80_manual != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? '<input data-money="'.$linha.'" type="text" class="form-control form-control-xxs text-center" value="'.moeda($resPrevisao[0]['MEDIA_HORA_EXTRA_80']).'">' : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center <?= ($he80_manual != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['MEDIA_HORA_EXTRA_80'], $resPrevisao[0]['MEDIA_HORA_EXTRA_80']) : ""; ?></td>
                                        <td class="text-center <?= ($he80_manual != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['MEDIA_HORA_EXTRA_80'] - $resCalculo[0]['MEDIA_HORA_EXTRA_80']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['MEDIA_HORA_EXTRA_80']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['MEDIA_HORA_EXTRA_80']);
                                ?>
                                <tr>
                                    <td class="text-left <?= ($he100_manual != "") ? ' text-danger' : ""; ?>" data-item="REMUNERACAO">Média Horas Extras 100%</td>
                                    <td class="text-center <?= ($he100_manual != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= ($he100_manual != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['MEDIA_HORA_EXTRA_100']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center <?= ($he100_manual != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? '<input data-money="'.$linha.'" type="text" class="form-control form-control-xxs text-center" value="'.moeda($resPrevisao[0]['MEDIA_HORA_EXTRA_100']).'">' : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center <?= ($he100_manual != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['MEDIA_HORA_EXTRA_100'], $resPrevisao[0]['MEDIA_HORA_EXTRA_100']) : ""; ?></td>
                                        <td class="text-center <?= ($he100_manual != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['MEDIA_HORA_EXTRA_100'] - $resCalculo[0]['MEDIA_HORA_EXTRA_100']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['MEDIA_HORA_EXTRA_100']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['MEDIA_HORA_EXTRA_100']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO">Nona Hora</td>
                                    <td class="text-center" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['MEDIA_NONA_HORA']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['MEDIA_NONA_HORA']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['MEDIA_NONA_HORA'], $resPrevisao[0]['MEDIA_NONA_HORA']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['MEDIA_NONA_HORA'] - $resCalculo[0]['MEDIA_NONA_HORA']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['MEDIA_NONA_HORA']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['MEDIA_NONA_HORA']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO">Hora em Espera</td>
                                    <td class="text-center" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['MEDIA_ESPERA_HORA']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['MEDIA_ESPERA_HORA']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['MEDIA_ESPERA_HORA'], $resPrevisao[0]['MEDIA_ESPERA_HORA']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['MEDIA_ESPERA_HORA'] - $resCalculo[0]['MEDIA_ESPERA_HORA']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['MEDIA_ESPERA_HORA']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['MEDIA_ESPERA_HORA']);
                                ?>
                                <tr>
                                    <td class="text-left <?= ($he_dsr != "") ? ' text-danger' : ""; ?>" data-item="REMUNERACAO">DSR sobre Horas Extras</td>
                                    <td class="text-center <?= ($he_dsr != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= ($he_dsr != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['MEDIA_DSR_HE']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center <?= ($he_dsr != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? '<input data-money="'.$linha.'" type="text" class="form-control form-control-xxs text-center" value="'.moeda($resPrevisao[0]['MEDIA_DSR_HE']).'">' : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center <?= ($he_dsr != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['MEDIA_DSR_HE'], $resPrevisao[0]['MEDIA_DSR_HE']) : ""; ?></td>
                                        <td class="text-center <?= ($he_dsr != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['MEDIA_DSR_HE'] - (float)$resCalculo[0]['MEDIA_DSR_HE']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['MEDIA_DSR_HE']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['MEDIA_DSR_HE']);
                                ?>
                                <tr>
                                    <td class="text-left <?= ($adicional_noturno != "") ? ' text-danger' : ""; ?>" data-item="REMUNERACAO">Adicional Noturno</td>
                                    <td class="text-center <?= ($adicional_noturno != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Pagamento Médio no Cargo</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= ($adicional_noturno != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['MEDIA_ADIC_NOTURNO']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center <?= ($adicional_noturno != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? '<input data-money="'.$linha.'" type="text" class="form-control form-control-xxs text-center" value="'.moeda($resPrevisao[0]['MEDIA_ADIC_NOTURNO']).'">' : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center <?= ($adicional_noturno != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['MEDIA_ADIC_NOTURNO'], $resPrevisao[0]['MEDIA_ADIC_NOTURNO']) : ""; ?></td>
                                        <td class="text-center <?= ($adicional_noturno != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['MEDIA_ADIC_NOTURNO'] - $resCalculo[0]['MEDIA_ADIC_NOTURNO']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $remuneracao_total_atual += ($resCalculo[0]['MEDIA_ADIC_NOTURNO']);
                                if($acao != 'A') $remuneracao_total_calc += ($resPrevisao[0]['MEDIA_ADIC_NOTURNO']);
                                ?>
                                <tr>
                                    <td class="text-center bg-dark text-white" colspan="2">Total Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center bg-dark text-white"><?= moeda($remuneracao_total_atual); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center bg-dark text-white"><?= moeda($remuneracao_total_calc); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($remuneracao_total_atual, $remuneracao_total_calc) : ""; ?></td>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? moeda($remuneracao_total_calc - $remuneracao_total_atual) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center bg-success text-white" rowspan="6" width="100"><p class="quebra_letra">ENCARGOS</p></td>
                                </tr>
                                <?php
                                $encargos_total_atual = 0;
                                $encargos_total_calc = 0;
                                ?>
                                <tr>
                                    <td class="text-left" data-item="ENCARGOS">Férias</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['FERIAS']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['FERIAS']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['FERIAS'], $resPrevisao[0]['FERIAS']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['FERIAS'] - $resCalculo[0]['FERIAS']) : "-"; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $encargos_total_atual += ($resCalculo[0]['FERIAS']);
                                if($acao != 'A') $encargos_total_calc += ($resPrevisao[0]['FERIAS']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="ENCARGOS">13º Salário</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['SALARIO_13']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['SALARIO_13']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['SALARIO_13'], $resPrevisao[0]['SALARIO_13']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['SALARIO_13'] - $resCalculo[0]['SALARIO_13']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $encargos_total_atual += ($resCalculo[0]['SALARIO_13']);
                                if($acao != 'A') $encargos_total_calc += ($resPrevisao[0]['SALARIO_13']);
                                ?>
                                
                                    <!-- oculto, solicitação Edmir -->
                                <tr>
                                    <td class="text-left" data-item="ENCARGOS">FGTS</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? moeda($resCalculo[0]['CODCATEGORIA_CALC']*100) : moeda($resPrevisao[0]['CODCATEGORIA_CALC']*100); ?>%</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['FGTS']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['FGTS']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['FGTS'], $resPrevisao[0]['FGTS']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['FGTS'] - $resCalculo[0]['FGTS']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                    if($chapa) $encargos_total_atual += ($resCalculo[0]['FGTS']);
                                    if($acao != 'A') $encargos_total_calc += ($resPrevisao[0]['FGTS']);
                                
                                    if(1==2):
                                ?>
                                <tr>
                                    <td class="text-left" data-item="ENCARGOS">INSS (Colaborador)</td>
                                    <td class="text-center" style="background: #fffeed;"></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['CALCULO_INSS']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['CALCULO_INSS']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['CALCULO_INSS'], $resPrevisao[0]['CALCULO_INSS']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['CALCULO_INSS'] - $resCalculo[0]['CALCULO_INSS']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $encargos_total_atual += ($resCalculo[0]['CALCULO_INSS']);
                                if($acao != 'A') $encargos_total_calc += ($resPrevisao[0]['CALCULO_INSS']);
                                    endif;
                                ?>

                                <tr>
                                    <td class="text-left" data-item="ENCARGOS">INSS (Empresa)</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? moeda($resCalculo[0]['CALC_INSS']*100) : moeda($resPrevisao[0]['CALC_INSS']*100); ?>%</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['INSS']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['INSS']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['INSS'], $resPrevisao[0]['INSS']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['INSS'] - $resCalculo[0]['INSS']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $encargos_total_atual += ($resCalculo[0]['INSS']);
                                if($acao != 'A') $encargos_total_calc += ($resPrevisao[0]['INSS']);
                                ?>

                                <tr>
                                    <td class="text-center bg-dark text-white" colspan="2">Total Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center bg-dark text-white"><?= moeda($encargos_total_atual); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center bg-dark text-white"><?= moeda($encargos_total_calc); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($encargos_total_atual, $encargos_total_calc) : ""; ?></td>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? moeda($encargos_total_calc - $encargos_total_atual) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center bg-primary text-white" rowspan="7" width="100"><p class="quebra_letra" style="float: left; margin-left: 37px; margin-top: 10px; margin-bottom: 10px;">REMUNERAÇÃO</p> <p class="quebra_letra" style="float: right; margin-right: 41px; margin-top: 10px; margin-bottom: 10px;">VARIÁVEL</p></td>
                                </tr>

                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO_VARIAVEL">Prêmio Produção (Indústria e São Paulo)</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['PP_IND']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['PP_IND']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['PP_IND'], $resPrevisao[0]['PP_IND']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['PP_IND'] - $resCalculo[0]['PP_IND']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <?php $linha++; ?>                            
                                <?php
                                if($chapa) $individual_total_atual += ($resCalculo[0]['PP_IND']);
                                if($acao != 'A') $individual_total_calc += ($resPrevisao[0]['PP_IND']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO_VARIAVEL">RVD - Remuneração Variável Diferida</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? moeda($resCalculo[0]['RVD_CALC']) : moeda($resPrevisao[0]['RVD_CALC']); ?></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['RVD']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['RVD']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['RVD'], $resPrevisao[0]['RVD']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['RVD'] - $resCalculo[0]['RVD']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <?php $linha++; ?>                            
                                <?php
                                if($chapa) $individual_total_atual += ($resCalculo[0]['RVD']);
                                if($acao != 'A') $individual_total_calc += ($resPrevisao[0]['RVD']);
                                ?>
                                
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO_VARIAVEL">PPR - Programa de Participação nos Resultados</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? moeda($resCalculo[0]['PPR_CALC']) : moeda($resPrevisao[0]['PPR_CALC']); ?></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['PPR']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['PPR']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['PPR'], $resPrevisao[0]['PPR']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['PPR'] - $resCalculo[0]['PPR']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <?php $linha++; ?>                            
                                <?php
                                if($chapa) $individual_total_atual += ($resCalculo[0]['PPR']);
                                if($acao != 'A') $individual_total_calc += ($resPrevisao[0]['PPR']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO_VARIAVEL">PRV - Programa de Remuneração Variável</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? moeda($resCalculo[0]['PRV_CALC']) : moeda($resPrevisao[0]['PRV_CALC']); ?></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['PRV']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['PRV']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['PRV'], $resPrevisao[0]['PRV']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['PRV'] - $resCalculo[0]['PRV']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <?php $linha++; ?>                            
                                <?php
                                if($chapa) $individual_total_atual += ($resCalculo[0]['PRV']);
                                if($acao != 'A') $individual_total_calc += ($resPrevisao[0]['PRV']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="REMUNERACAO_VARIAVEL">Bônus Superação</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? moeda($resCalculo[0]['SUPERACAO_CALC']) : moeda($resPrevisao[0]['SUPERACAO_CALC']); ?></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['SUPERACAO']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['SUPERACAO']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['SUPERACAO'], $resPrevisao[0]['SUPERACAO']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['SUPERACAO'] - $resCalculo[0]['SUPERACAO']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <?php $linha++; ?>                            
                                <?php
                                if($chapa) $individual_total_atual += ($resCalculo[0]['SUPERACAO']);
                                if($acao != 'A') $individual_total_calc += ($resPrevisao[0]['SUPERACAO']);
                                ?>

                                <tr>
                                    <td class="text-center bg-dark text-white" colspan="2">Total Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center bg-dark text-white"><?= moeda($individual_total_atual); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center bg-dark text-white"><?= moeda($individual_total_calc); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($individual_total_atual, $individual_total_calc) : ""; ?></td>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? moeda($individual_total_calc - $individual_total_atual) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                
                                <tr>
                                    <td class="text-center bg-info text-white" rowspan="9" width="100"><p class="quebra_letra">BENEFÍCIOS</p></td>
                                </tr>
                                <?php
                                $beneficio_total_atual = 0;
                                $beneficio_total_calc = 0;
                                $beneficio_total_atual_grupo = 0;
                                $beneficio_total_calc_grupo = 0;
                                ?>
                                <tr>
                                    <td class="text-left" data-item="BENEFICIOS">Vale Alimentação</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['VA']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['VA']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['VA'], $resPrevisao[0]['VA']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['VA'] - $resCalculo[0]['VA']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $beneficio_total_atual += ($resCalculo[0]['VA'] / 12);
                                if($acao != 'A') $beneficio_total_calc += ($resPrevisao[0]['VA'] / 12);
                                if($chapa) $beneficio_total_atual_grupo += ($resCalculo[0]['VA']);
                                if($acao != 'A') $beneficio_total_calc_grupo += ($resPrevisao[0]['VA']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="BENEFICIOS">Vale Refeição</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['VR']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['VR']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['VR'], $resPrevisao[0]['VR']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['VR'] - $resCalculo[0]['VR']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $beneficio_total_atual += ($resCalculo[0]['VR']);
                                if($acao != 'A') $beneficio_total_calc += ($resPrevisao[0]['VR']);
                                if($chapa) $beneficio_total_atual_grupo += ($resCalculo[0]['VR']);
                                if($acao != 'A') $beneficio_total_calc_grupo += ($resPrevisao[0]['VR']);
                                ?>

                                <tr>
                                    <td class="text-left" data-item="BENEFICIOS">Vale Alimentação - HI</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['VAHI']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['VAHI']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['VAHI'], $resPrevisao[0]['VAHI']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['VAHI'] - $resCalculo[0]['VAHI']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $beneficio_total_atual += ($resCalculo[0]['VAHI']);
                                if($acao != 'A') $beneficio_total_calc += ($resPrevisao[0]['VAHI']);
                                if($chapa) $beneficio_total_atual_grupo += ($resCalculo[0]['VAHI']);
                                if($acao != 'A') $beneficio_total_calc_grupo += ($resPrevisao[0]['VAHI']);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="BENEFICIOS">Vale Alimentação - Adicional Natal</td>
                                    <td class="text-center" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['VANATAL']/12) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['VANATAL']/12) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem(($resCalculo[0]['VANATAL']/12), ($resPrevisao[0]['VANATAL']/12)) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda(($resPrevisao[0]['VANATAL']/12) - ($resCalculo[0]['VANATAL']/12)) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                               //if($chapa) $beneficio_total_atual += ($resCalculo[0]['VANATAL']/12);
                               //if($acao != 'A') $beneficio_total_calc += ($resPrevisao[0]['VANATAL']/12);
                               if($chapa) $beneficio_total_atual_grupo += ($resCalculo[0]['VANATAL']/12);
                               if($acao != 'A') $beneficio_total_calc_grupo += ($resPrevisao[0]['VANATAL']/12);
                                ?>


                                <tr>
                                    <td class="text-left" data-item="BENEFICIOS">Plano de Saúde</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? ucfirst(strtolower($resCalculo[0]['PLANO_SAUDE_OPERADORA'])) : ucfirst(strtolower($resPrevisao[0]['PLANO_SAUDE_OPERADORA'])); ?></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['PLANO_SAUDE']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['PLANO_SAUDE']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['PLANO_SAUDE'], $resPrevisao[0]['PLANO_SAUDE']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['PLANO_SAUDE'] - $resCalculo[0]['PLANO_SAUDE']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $beneficio_total_atual += ($resCalculo[0]['PLANO_SAUDE']);
                                if($acao != 'A') $beneficio_total_calc += ($resPrevisao[0]['PLANO_SAUDE']);
                                if($chapa) $beneficio_total_atual_grupo += ($resCalculo[0]['PLANO_SAUDE']);
                                if($acao != 'A') $beneficio_total_calc_grupo += ($resPrevisao[0]['PLANO_SAUDE']);

                                $PREVIDENCIA_CALCULO = 0;
                                $PREVIDENCIA_PREVISAO = 0;
                                $PREVIDENCIA_CALCULO_VALOR = 0;
                                $PREVIDENCIA_PREVISAO_VALOR = 0;

                                if($chapa){
                                    if(substr($resCalculo[0]['PREVIDENCIA_PFCOMPL'],0,1) != "N"){
                                        $PREVIDENCIA_CALCULO = (int)substr($resCalculo[0]['PREVIDENCIA_PFCOMPL'],0,2);
                                        $PREVIDENCIA_CALCULO_VALOR = $resCalculo[0]['SALARIO_MENSAL'] * ($PREVIDENCIA_CALCULO / 100);
                                    }
                                    if($acao != 'A'){
                                        $PREVIDENCIA_PREVISAO = $resPrevisao[0]['CALC_PREVIDENCIA_PRIVADA'];
                                        $PREVIDENCIA_PREVISAO_VALOR = $resPrevisao[0]['PREVIDENCIA_PRIVADA'];
                                        if($PREVIDENCIA_CALCULO_VALOR <= 0) $PREVIDENCIA_PREVISAO_VALOR = 0;
                                    }
                                    if($PREVIDENCIA_CALCULO_VALOR <= 0 && $acao == 'A'){
                                        $PREVIDENCIA_PREVISAO = 0;
                                        $PREVIDENCIA_PREVISAO_VALOR = 0;
                                    }
                                }

                                ?>
                                <tr>
                                    <td class="text-left" data-item="BENEFICIOS">Previdência Privada Privada</td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa) ? $PREVIDENCIA_CALCULO : $PREVIDENCIA_PREVISAO; ?>%</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($PREVIDENCIA_CALCULO_VALOR) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($PREVIDENCIA_PREVISAO_VALOR) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($PREVIDENCIA_CALCULO_VALOR, $PREVIDENCIA_PREVISAO_VALOR) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($PREVIDENCIA_PREVISAO_VALOR - $PREVIDENCIA_CALCULO_VALOR) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $beneficio_total_atual += ($PREVIDENCIA_CALCULO_VALOR);
                                if($acao != 'A') $beneficio_total_calc += ($PREVIDENCIA_PREVISAO_VALOR);
                                if($chapa) $beneficio_total_atual_grupo += ($PREVIDENCIA_CALCULO_VALOR);
                                if($acao != 'A') $beneficio_total_calc_grupo += ($PREVIDENCIA_PREVISAO_VALOR);
                                ?>
                                <tr>
                                    <td class="text-left" data-item="BENEFICIOS">Seguro de Vida</td>
                                    <td class="text-center" style="background: #fffeed;">70% do salário</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" data-valoratual="<?= $linha; ?>"><?= ($chapa) ? moeda($resCalculo[0]['SEGURO_VIDA']) : ""; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" data-valorcalculado="<?= $linha; ?>"><?= ($acao != 'A') ? moeda($resPrevisao[0]['SEGURO_VIDA']) : ""; ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                        <td class="text-center" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['SEGURO_VIDA'], $resPrevisao[0]['SEGURO_VIDA']) : ""; ?></td>
                                        <td class="text-center" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['SEGURO_VIDA'] - $resCalculo[0]['SEGURO_VIDA']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr><?php $linha++; ?>
                                <?php
                                if($chapa) $beneficio_total_atual += ($resCalculo[0]['SEGURO_VIDA']);
                                if($acao != 'A') $beneficio_total_calc += ($resPrevisao[0]['SEGURO_VIDA']);
                                if($chapa) $beneficio_total_atual_grupo += ($resCalculo[0]['SEGURO_VIDA']);
                                if($acao != 'A') $beneficio_total_calc_grupo += ($resPrevisao[0]['SEGURO_VIDA']);
                                ?>


                                <tr>
                                    <td class="text-center bg-dark text-white" colspan="2">Total Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center bg-dark text-white"><?= moeda($beneficio_total_atual_grupo); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center bg-dark text-white"><?= moeda($beneficio_total_calc_grupo); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($beneficio_total_atual_grupo, $beneficio_total_calc_grupo) : ""; ?></td>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? moeda($beneficio_total_calc_grupo - $beneficio_total_atual_grupo) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center bg-primary text-white" rowspan="3" width="100"><p class="quebra_letra">OUTROS</p></td>
                                </tr>
                                <?php
                                $outros_total_atual = 0;
                                $outros_total_calc = 0;
                                //if($acao == "A") $resCalculo[0]['DEMAIS_CUSTOS'] = str_replace(',', '.', str_replace('.', '', $demaiscustos_manual_atual));

                                
                                ?>
                                <tr>
                                    <td class="text-left <?= ($demaiscustos_manual != "" || $demaiscustos_manual_atual != "") ? ' text-danger' : ""; ?>" data-item="OUTROS">Demais Custos Gerais</td>
                                    <td class="text-center <?= ($demaiscustos_manual != "" || $demaiscustos_manual_atual != "") ? ' text-danger' : ""; ?>" style="background: #fffeed;">Valor Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center <?= ($demaiscustos_manual != "") ? ' text-danger' : ""; ?>" data-valoratual="<?= $linha; ?>"><input data-money="<?= $linha; ?>" data-demaiscustosatual="<?= $linha; ?>" type="text" class="form-control form-control-xxs text-center" value="<?= moeda($resCalculo[0]['DEMAIS_CUSTOS']); ?>"></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center <?= ($demaiscustos_manual != "" || $demaiscustos_manual_atual != "") ? ' text-danger' : ""; ?>" data-valorcalculado="<?= $linha; ?>"><input data-money="<?= $linha; ?>" data-demaiscustosprevisao="<?= $linha; ?>" type="text" class="form-control form-control-xxs text-center" value="<?= moeda($resPrevisao[0]['DEMAIS_CUSTOS']); ?>"></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center <?= ($demaiscustos_manual != "" || $demaiscustos_manual_atual != "") ? ' text-danger' : ""; ?>" data-diffporcentagem="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['DEMAIS_CUSTOS'], $resPrevisao[0]['DEMAIS_CUSTOS']) : ""; ?></td>
                                    <td class="text-center <?= ($demaiscustos_manual != "" || $demaiscustos_manual_atual != "") ? ' text-danger' : ""; ?>" data-diffvalor="<?= $linha; ?>"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['DEMAIS_CUSTOS'] - $resCalculo[0]['DEMAIS_CUSTOS']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <?php
                                if($acao != 'A') $beneficio_total_calc += ($resPrevisao[0]['DEMAIS_CUSTOS']);
                                ?>
                                <?php $linha++; ?>
                                <tr>
                                    <td class="text-center bg-dark text-white" colspan="2">Total Mensal</td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center bg-dark text-white"><?= moeda($resCalculo[0]['DEMAIS_CUSTOS']); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center bg-dark text-white"><?= moeda($resPrevisao[0]['DEMAIS_CUSTOS']); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['DEMAIS_CUSTOS'], $resPrevisao[0]['DEMAIS_CUSTOS']) : ""; ?></td>
                                    <td class="text-center bg-dark text-white"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['DEMAIS_CUSTOS'] - $resCalculo[0]['DEMAIS_CUSTOS']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>

                                <tr>
                                    <td class="text-center" colspan="<?= (($dados['acao'] != 'A') ? 7 : 4) ?>" style="height: 25px;"></td>
                                </tr>
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center" style="background: #f0f0f0;">Total Mensal</td>
                                    <td class="text-center" style="background: #f0f0f0;"></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" style="background: #f0f0f0;">
                                        <?php
                                        /*var_dump($remuneracao_total_atual);
                                        var_dump($encargos_total_atual);
                                        var_dump($beneficio_total_atual);
                                        var_dump($individual_total_atual);
                                        var_dump($resCalculo[0]['DEMAIS_CUSTOS']);
                                        exit();*/
                                        ?>
                                        
                                        <?= moeda($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + (float)$resCalculo[0]['DEMAIS_CUSTOS']); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" style="background: #f0f0f0;"><?= moeda($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + (float)$resPrevisao[0]['DEMAIS_CUSTOS']); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center" style="background: #f0f0f0;"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + (float)$resCalculo[0]['DEMAIS_CUSTOS'], $remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + (float)$resPrevisao[0]['DEMAIS_CUSTOS']) : ""; ?></td>
                                    <td class="text-center" style="background: #f0f0f0;"><?= ($chapa && $acao != 'A') ? moeda(($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + (float)$resPrevisao[0]['DEMAIS_CUSTOS']) - ($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + (float)$resCalculo[0]['DEMAIS_CUSTOS'])) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center" style="background: #f0f0f0;">Total Anual</td>
                                    <td class="text-center" style="background: #f0f0f0;"></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" style="background: #f0f0f0;"><?= moeda((($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + (float)$resCalculo[0]['DEMAIS_CUSTOS']) * 12)); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" style="background: #f0f0f0;"><?= moeda((($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc + $individual_total_calc + (float)$resPrevisao[0]['DEMAIS_CUSTOS']) * 12)); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center" style="background: #f0f0f0;"><?= ($chapa && $acao != 'A') ? calculaPorcentagem((($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + (float)$resCalculo[0]['DEMAIS_CUSTOS'])*12), (($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + (float)$resPrevisao[0]['DEMAIS_CUSTOS'])*12)) : ""; ?></td>
                                    <td class="text-center" style="background: #f0f0f0;"><?= ($chapa && $acao != 'A') ? moeda((($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + (float)$resPrevisao[0]['DEMAIS_CUSTOS'])*12) - ((($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + (float)$resCalculo[0]['DEMAIS_CUSTOS'])*12))) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center" style="background: #f0f0f0;">FATOR K (salário mensal / total mensal)</td>
                                    <td class="text-center" style="background: #f0f0f0;"></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" style="background: #f0f0f0;"><?= ($chapa) ? moeda(($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual) / $resCalculo[0]['SALARIO_MENSAL']) : "-"; ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" style="background: #f0f0f0;"><?= moeda(($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + $resPrevisao[0]['DEMAIS_CUSTOS']) / (($resPrevisao[0]['SALARIO_MENSAL'] <= 0) ? 1 : $resPrevisao[0]['SALARIO_MENSAL'])); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center" style="background: #f0f0f0;"><?= ($chapa && $acao != 'A') ? calculaPorcentagem((($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + $resCalculo[0]['DEMAIS_CUSTOS']) / (($resCalculo[0]['SALARIO_MENSAL'] <= 0) ? 1 : $resCalculo[0]['SALARIO_MENSAL'])), (($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + $resPrevisao[0]['DEMAIS_CUSTOS']) / (($resPrevisao[0]['SALARIO_MENSAL'] <= 0) ? 1 : $resPrevisao[0]['SALARIO_MENSAL']))) : ""; ?></td>
                                    <td class="text-center" style="background: #f0f0f0;"><?= ($chapa && $acao != 'A') ? moeda((($remuneracao_total_calc + $encargos_total_calc + $beneficio_total_calc_grupo + $individual_total_calc + $resPrevisao[0]['DEMAIS_CUSTOS']) / (($resPrevisao[0]['SALARIO_MENSAL'] <= 0) ? 1 : $resPrevisao[0]['SALARIO_MENSAL'])) - (($remuneracao_total_atual + $encargos_total_atual + $beneficio_total_atual_grupo + $individual_total_atual + $resCalculo[0]['DEMAIS_CUSTOS']) / (($resCalculo[0]['SALARIO_MENSAL'] <= 0) ? 1 : $resCalculo[0]['SALARIO_MENSAL']))) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>

                                <tr>
                                    <td class="text-center" colspan="<?= (($dados['acao'] != 'A') ? 7 : 4) ?>" style="height: 25px;"></td>
                                </tr>
                                
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center" style="background: #fffeed;">Líquido Mês</td>
                                    <td class="text-center" style="background: #fffeed;"></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" style="background: #fffeed;"><?= moeda($resCalculo[0]['LIQUIDO_MENSAL']); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" style="background: #fffeed;"><?= moeda($resPrevisao[0]['LIQUIDO_MENSAL']); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['LIQUIDO_MENSAL'], $resPrevisao[0]['LIQUIDO_MENSAL']) : ""; ?></td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa && $acao != 'A') ? moeda($resPrevisao[0]['LIQUIDO_MENSAL'] - $resCalculo[0]['LIQUIDO_MENSAL']) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center" style="background: #fffeed;">Líquido Ano (sem remuneração variável)</td>
                                    <td class="text-center" style="background: #fffeed;"></td>
                                    <?php if($dados['acao'] == 'A' || $dados['acao'] == 'P'): ?><td class="text-center" style="background: #fffeed;"><?= moeda(($resCalculo[0]['LIQUIDO_MENSAL']) * 12); ?></td><?php endif; ?>
                                    <?php if($dados['acao'] != 'A'): ?>
                                    <td class="text-center" style="background: #fffeed;"><?= moeda(($resPrevisao[0]['LIQUIDO_MENSAL']) * 12); ?></td>
                                    <?php if($dados['acao'] == 'P'): ?>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa && $acao != 'A') ? calculaPorcentagem($resCalculo[0]['LIQUIDO_MENSAL']*12, $resPrevisao[0]['LIQUIDO_MENSAL']*12) : ""; ?></td>
                                    <td class="text-center" style="background: #fffeed;"><?= ($chapa && $acao != 'A') ? moeda(($resPrevisao[0]['LIQUIDO_MENSAL']*12) - ($resCalculo[0]['LIQUIDO_MENSAL']*12)) : ""; ?></td>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </tr>

                            
                            <?php endif; ?>
                        </tbody>
                    </table><spsn style="float: left;"><?= $time_start.' | '.$time_end; ?></spsn>
                    <div class="text-right hidden marca_rodape"><img src="<?= base_url('imgcartas/Marca DGua - Item 7.jpg') ?>"></div>

                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success botao_salvar mr-3" onclick="window.print();"><i class="mdi mdi-printer"></i> Imprimir Simulação</button>
                    <button class="btn btn-primary botao_salvar" onclick="return salvaDados(1)"><i class="mdi mdi-content-save"></i> Salvar Simulação</button>
                    <?php if(($chapa && $acao != 'A') || ($acao == "R")): ?><button class="btn btn-info botao_salvar ml-3" onclick="return salvaDados(2)"><i class="mdi mdi-plus-circle-outline"></i> Salvar e Criar Requisição de <?= ($chapa) ? "Alteração" : "Pessoal"; ?></button><?php endif; ?>
                    <button class="btn btn-info hidden" id="botao_recalcula" onclick="return executaSimulacao()"><i class="mdi mdi-reload"></i> Recalcular</button>
                    
                </div>
            </div>
            <?php endif; ?>

        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
$(document).ready(function(){
    $("#chapa, #posicao_destino, #filial, #secao, #funcao").on('change', function(){
        $("#salario_manual, #he50_manual, #he80_manual, #he100_manual, #demaiscustos_manual, #demaiscustos_manual_atual, #adicional_noturno, #he_dsr, #premio_producao").val('');
        $(".data_resultado").fadeOut(0);
    });
    $("[data-money]").maskMoney({prefix:'', allowNegative: false, allowZero:true, thousands:'.', decimal:',', affixesStay: false});
    $("[data-money=0]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#salario_manual").val($(this).val());
    });
    $("[data-money=5]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#he50_manual").val($(this).val());
    });
    $("[data-money=6]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#he80_manual").val($(this).val());
    });
    $("[data-money=7]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#he100_manual").val($(this).val());
    });
    $("[data-money=11]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#adicional_noturno").val($(this).val());
    });
    /*$("[data-money=28]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#demaiscustos_manual").val($(this).val());
    });*/
    $("[data-demaiscustosatual=28]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#demaiscustos_manual_atual").val($(this).val());
    });
    $("[data-demaiscustosprevisao=28]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#demaiscustos_manual").val($(this).val());
    });
    $("[data-money=10]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#he_dsr").val($(this).val());
    });
    $("[data-money=3]").on('blur', function(){
        $(".botao_salvar").fadeOut(0);
        $("#botao_recalcula").fadeIn(0);
        $("#premio_producao").val($(this).val());
    });
    
    
});
const carregaAcao = (acao) => {
    $("#salario_manual, #he50_manual, #he80_manual, #he100_manual, #demaiscustos_manual, #demaiscustos_manual_atual, #adicional_noturno, #he_dsr, #premio_producao").val('');
    $(".data_filtro_1, .data_filtro_2, .data_filtro_3").find('select').val(null).trigger('change');
    $(".data_resultado").fadeOut(0);
    // alteração
    if(acao == 'A'){
        $(".data_filtro_1").fadeIn(100);
        $(".data_filtro_2, .data_filtro_3, .data_filtro_4").fadeOut(100);
    }else
    // promoção
    if(acao == 'P'){
        $(".data_filtro_1, .data_filtro_2").fadeIn(100);
        $(".data_filtro_3, .data_filtro_4").fadeOut(100);
    }else
    // recrutamento
    if(acao == 'R'){
        $(".data_filtro_1, .data_filtro_2, .data_filtro_3").fadeOut(100);
        $(".data_filtro_4").fadeIn(100);
    }else
    // simulação
    if(acao == 'S'){
        $(".data_filtro_1, .data_filtro_2, .data_filtro_4").fadeOut(100);
        $(".data_filtro_3").fadeIn(100);
    }else{
        $(".data_filtro_1, .data_filtro_2, .data_filtro_3, .data_filtro_4").fadeOut(100);
    }
}
const executaSimulacao = () => {
    let dados = {
        "acao": $("#acao").val(),
        "chapa": $("#chapa").val(),
        "posicao_destino": $("#posicao_destino").val(),
        "posicao_recrutamento": $("#posicao_recrutamento").val(),
        "filial": $("#filial").val(),
        "secao": $("#secao").val(),
        "funcao": $("#funcao").val(),
    }

    if(dados.acao == ""){ exibeAlerta("warning", "Ação não informado."); return false; }
    if((dados.acao == "A" || dados.acao == "P") && dados.chapa == ""){ exibeAlerta("warning", "Colaborador não informado."); return false; }
    if((dados.acao == "P") && dados.posicao_destino == ""){ exibeAlerta("warning", "Posição nova não informado."); return false; }
    if((dados.acao == "R") && dados.posicao_recrutamento == ""){ exibeAlerta("warning", "Requisição AQ não informado."); return false; }
    if(dados.acao == "S" && dados.filial == ""){ exibeAlerta("warning", "Filial não informada."); return false; }
    if(dados.acao == "S" && dados.secao == ""){ exibeAlerta("warning", "Seção não informada."); return false; }
    if(dados.acao == "S" && dados.funcao == ""){ exibeAlerta("warning", "Função não informada."); return false; }

    openLoading();

    $("#form_filtro").submit();
}
const carregaFilialSecao = (codfilial) => {

    $("#secao").html('<option value="">...</option>');
    if(codfilial == '') return false;

    openLoading();

    $.ajax({
        url: "<?= base_url('remuneracao/simulador/action/filial_secao'); ?>",
        type:'POST',
        data:{"codfilial": codfilial},
        success:function(result){

            var response = JSON.parse(result);

            if(response){
                for(var x = 0; x < response.length; x++){
                    $("#secao").append('<option value="'+response[x].CODIGO+'">'+response[x].DESCRICAO+' - '+response[x].CODIGO+'</option>');
                }
            }
            
            openLoading(true);
            
        },
    });

}
const salvaDados = (processo) => {

    let valorAtual = $("[data-valoratual]");
    let valorCalculado = $("[data-valorcalculado]");
    let acao = $("#acao").val();
    let html = $("#html_pdf").html();
    
    let fd = new FormData();

    fd.append('acao', acao);
    fd.append('chapa', $("#chapa").val());
    fd.append('posicao_destino', $("#posicao_destino").val());
    fd.append('filial', $("#filial").val());
    fd.append('secao', $("#secao").val());
    fd.append('funcao', $("#funcao").val());
    fd.append('processo', processo);
    
    
    fd.append('texto_acao', $("#acao option:selected").text());
    fd.append('texto_chapa', $("#chapa option:selected").text());
    fd.append('texto_posicao_destino', $("#posicao_destino option:selected").text());
    fd.append('texto_filial', $("#filial option:selected").text());
    fd.append('texto_secao', $("#secao option:selected").text());
    fd.append('texto_funcao', $("#funcao option:selected").text());
    
    fd.append('html_pdf', html);
    

    $("[data-item]").each(function(i){

        fd.append('tipo[]', $(this).attr('data-item'));
        fd.append('descricao[]', $(this).html());
        
        if(acao == "A" || acao == "P"){
            fd.append('valor_atual[]', ((typeof valorAtual[i]?.innerText != "undefined") ? valorAtual[i].innerText : "0"));
        }

        if(acao == "P" || acao == "R" || acao == "S"){
            if(typeof valorCalculado[i]?.childNodes[0].value != "undefined"){
                fd.append('valor_calculado[]', ((typeof valorCalculado[i]?.childNodes[0].value != "undefined") ? valorCalculado[i]?.childNodes[0].value : "0"));
            }else{
                fd.append('valor_calculado[]', ((typeof valorCalculado[i]?.innerText != "undefined") ? valorCalculado[i].innerText : "0"));
            }
        }

    });

    openLoading();

    $.ajax({
        url: "<?= base_url('remuneracao/simulador/action/cadastrar_simulacao') ?>",
        type:'POST',
        processData: false,
        contentType: false,
        data: fd,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                if(processo == 2){
                    /*
                    if(acao == 'P') exibeAlerta(response.tipo, 'Simulação cadastrada com sucesso, abrindo requisição de alteração.', 4, 'https://portalrhdev.eldoradobrasil.com.br/apl/requisicao/add.requisicao.php?_r='+response.cod+'&_a='+acao);
                    if(acao == 'R') exibeAlerta(response.tipo, 'Simulação cadastrada com sucesso, abrindo requisição de alteração.', 4, 'https://portalrhdev.eldoradobrasil.com.br/apl/requisicao/add.aumentoquadro.php?_r='+response.cod+'&_a='+acao);
                    */

                    exibeAlerta(response.tipo, 'Simulação cadastrada com sucesso, abrindo requisição de alteração.', 4);

                    $("#local").val(acao);
                    $("#cod").val(response.cod);
                    $("#form_login").submit();

                }else{
                    exibeAlerta(response.tipo, response.msg, 3, window.location.href);
                }
            }

        }
    });

}
const calculoGeral = () => {
    //var salario_digitado = $("").val();
}
</script>
<style>
    .quebra_letra {
        word-wrap: break-word;
        font-family: monospace;
        white-space: pre-wrap;
        width: 1px;
        line-height: 14px;
        font-weight: bold;
        font-size: 14px;
        margin: auto;
        display: block;
    }
    .quebra_letra span {
        margin-top: 10px;
        display: block;
    }
    @media print {
        button, a, .card-footer {
            display: none !important;
        }
        table * {
            font-size: 11px !important;
        }
        .page-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card-header {
            padding: 5px !important;
        }
        .marca_rodape {
            display: block;
        }
    }
</style>
<form action="https://portalrh.eldoradobrasil.com.br/login.php" method="post" name="form_login" id="form_login" target="_blank">
<input type="hidden" name="log_login" value="<?= $_SESSION['log_login']; ?>">
<input type="hidden" name="local" id="local" value="">
<input type="hidden" name="cod" id="cod" value="">
<input type="hidden" name="action" value="login_simulador">
<input type="hidden" name="sublogin" value="ENTRAR">
</form>
<?php
loadPlugin(array('select2', 'maskmoney'));
?>