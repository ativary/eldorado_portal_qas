<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-10">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                    <form action="" method="post" id="form_filtro">
                        <div class="form-group row mb-0" >
                            <label for="relatorio" class="col-sm-2 col-form-label text-right text-left-sm">Relatório:</label>
                            <div class="col-sm-10">
                                <select onchange="selecionaRelatorio(this.value)" class="select2 custom-select form-control form-control-sm" name="relatorio" id="relatorio">
                                    <option value="" >- selecione o relatório -</option>
                                    <option value="1" <?php if($relatorio == 1){ echo 'selected';}?>>Abonos</option>
                                    <option value="2" <?php if($relatorio == 2){ echo 'selected';}?>>Colaboradores</option>
                                    <option value="3" <?php if($relatorio == 3){ echo 'selected';}?>>Afastamentos</option>
                                    <option value="4" <?php if($relatorio == 4){ echo 'selected';}?>>Ponto</option>
                                    <option value="5" <?php if($relatorio == 5){ echo 'selected';}?>>+6 Hrs. Ininterruptas de trabalho</option>
                                    <option value="6" <?php if($relatorio == 6){ echo 'selected';}?>>Menos de 11 hrs. Interjornada</option>
                                    <option value="7" <?php if($relatorio == 7){ echo 'selected';}?>>+6 Dias de trabalho sem descanso</option>
                                    <option value="8" <?php if($relatorio == 8){ echo 'selected';}?>>Tempo insuficiente de refeição</option>
                                    <option value="9" <?php if($relatorio == 9){ echo 'selected';}?>>Horário britânico</option>
                                    <option value="10" <?php if($relatorio == 10){ echo 'selected';}?>>Saldo banco de horas</option>
                                    <option value="11" <?php if($relatorio == 11){ echo 'selected';}?>>Extrato banco de horas</option>
                                    <option value="12" <?php if($relatorio == 12){ echo 'selected';}?>>Relação Geral-Equipe</option>
                                    <option value="13" <?php if($relatorio == 13){ echo 'selected';}?>>Ponto Digitado x Coletado</option>
                                    <?php if($rh): ?><option value="14" <?php if($relatorio == 14){ echo 'selected';}?>>Ponto Digitado x Excluídos</option><?php endif; ?>
                                    <?php if($rh): ?><option value="15" <?php if($relatorio == 15){ echo 'selected';}?>>Ponto Batidas Reprovadas</option><?php endif; ?>
                                    <option value="16" <?php if($relatorio == 16){ echo 'selected';}?>>Relatório (Macros)</option>
                                    <?php if($rh): ?><option value="17" <?php if($relatorio == 17){ echo 'selected';}?>>Variaveis- Auxilio Moradia</option><?php endif; ?>
                                    <?php if($rh): ?><option value="18" <?php if($relatorio == 18){ echo 'selected';}?>>Variaveis- Auxilio Excepcional</option><?php endif; ?>
                                    <?php if($rh): ?><option value="19" <?php if($relatorio == 19){ echo 'selected';}?>>Variaveis- Auxilio Creche</option><?php endif; ?>
                                    <?php if($rh): ?><option value="20" <?php if($relatorio == 20){ echo 'selected';}?>>Variaveis- Auxilio Aluguel</option><?php endif; ?>
                                    <?php if($rh): ?><option value="21" <?php if($relatorio == 21){ echo 'selected';}?>>Variaveis- Salário Substituição</option><?php endif; ?>
                                    <?php if($rh): ?><option value="22" <?php if($relatorio == 22){ echo 'selected';}?>>Variaveis- Sobreaviso</option><?php endif; ?>
                                    <?php if($rh): ?><option value="23" <?php if($relatorio == 23){ echo 'selected';}?>>Variaveis- Desconto Autorizado</option><?php endif; ?>
                                    <?php if($rh): ?><option value="24" <?php if($relatorio == 24){ echo 'selected';}?>>Variaveis- Antecipação 13 salário</option><?php endif; ?>
                                    <?php if($rh): ?><option value="25" <?php if($relatorio == 25){ echo 'selected';}?>>Variaveis- Coparticipação</option><?php endif; ?> 
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-0" >
                            <label for="secao" class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="secao[]" multiple="multiple" data-placeholder="- Todos -">
                                    <?php if($resSecao): ?>
                                        <?php foreach($resSecao as $key => $Secao): ?>
                                            <option <?php if(in_array($Secao['CODIGO'], $secao ?? array())){ echo 'selected';} ?> value="<?= $Secao['CODIGO']; ?>"><?= $Secao['CODIGO'].' - '.$Secao['DESCRICAO'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <!--
                        <div class="form-group row mb-0" >
                            <label for="funcao" class="col-sm-2 col-form-label text-right text-left-sm">Função:</label>
                            <div class="col-sm-10">
                                <select class="select2 select2-multiple custom-select form-control form-control-sm" name="funcao" id="funcao">
                                    <option value="">- Todos -</option>
                                    <?php if($resFuncao && 1==2): ?>
                                        <?php foreach($resFuncao as $key => $Funcao): ?>
                                            <option <?php if($funcao ==  $Funcao['CODIGO']){ echo 'selected';} ?> value="<?= $Funcao['CODIGO']; ?>"><?= $Funcao['CODIGO'].' - '.$Funcao['NOME'] ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        -->
                        <div class="form-group row mb-0">
                            <label for="keyword" class="col-form-label col-sm-2 text-left-sm">Chapa:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="<?php if($keyword){ echo $keyword;}?>" class="form-control form-control-sm col-3" placeholder="chapa / nome">
                                    <div class="input-group-prepend input-group-append bg-success">
                                        <span class="input-group-text pt-0 pb-0"><button onclick="procurarFuncionario()" type="button" class="btn btn-primary btn-xxs"><i class="fas fa-search"></i></button></span>
                                    </div>
                                    <select name="chapa" id="chapa" class="form-control form-control-sm">
                                        <?php if($chapa ?? null != null): ?>
                                            <option value="<?= $chapa; ?>" selected><?= extrai_valor($resFuncionarioSecao, $chapa, 'CHAPA', 'NOME').' - '.$chapa; ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label for="dataIni" class="col-sm-2 col-form-label text-right text-left-sm">Data Início:</label>
                            <div class="col-sm-4"><input class="form-control form-control-sm" type="date" name="dataIni" id="dataIni" value="<?php if($dataIni){ echo $dataIni;}?>"></div>
                            <label for="dataFim" class="col-sm-2 col-form-label text-right text-left-sm">Data Fim:</label>
                            <div class="col-sm-4"><input class="form-control form-control-sm" type="date" name="dataFim" id="dataFim" value="<?php if($dataFim){ echo $dataFim;}?>"></div>
                        </div>

                        <div class="form-group row mb-0" >
                            <label for="colunas" class="col-sm-2 col-form-label text-right text-left-sm">Colunas:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="colunas[]" id="colunas" multiple="multiple"></select>
                            </div>
                        </div>
                        <input type="hidden" name="colunas_filtro" value="<?= $colunas_filtro; ?>">
                    </form>

                </div>

                <div class="card-footer text-center">
                    <button type="button" class="btn btn-primary btn-sm bteldorado_7" onclick="gerarRelatorioTabela()">Gerar Relatório <i class="typcn typcn-cog"></i></button>
                    <button type="button" class="btn btn-success btn-sm bteldorado_1" onclick="gerarExcel()">Gerar Excel <i class="mdi mdi-file-excel"></i></button>
                    <button type="button" class="btn btn-danger btn-sm bteldorado_2" onclick="gerarPDF()">Gerar PDF <i class="mdi mdi-file-pdf"></i></button>
                    <!--<button type="button" class="btn btn-info btn-sm" onclick="gerarCSV()">Gerar CSV <i class="mdi mdi-file-table-outline"></i></button>-->
                </div>

            </div>
        </div><!-- end main -->
    </div>
    

        <?php if($resDados): ?>
        <div class="row">
            <div class="col-sm-10">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table_relatorios" class="mt-3 table table-sm table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 300px;">
                                <thead>
                                    <tr>
                                        <?php 
                                            if($colunas){ 
                                                foreach($colunas as $key => $value){
                                                    echo '<th>' . $colunas[$key] . '</th>';
                                                }
                                            }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($resDados as $idx => $Dados): ?>
                                        <tr>
                                            <?php foreach($colunas as $colunaNome): ?>
                                                <td><?= $Dados[$colunaNome]; ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php unset($resDados[$idx], $idx, $Dados); ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

</div><!-- container -->
<script src="<?= base_url('public/app/relatorio/gerar.js').'?v='.VERSION_JS.microtime(); ?>"></script>
<?php loadPlugin(array('select2', 'datatable')); ?>