<script>
$(document).ready(function(){
    $(".button-menu-mobile").click();
});
</script>
<div class="container-fluid"> 
    <div class="row">
        <style>
            [data-ocorrencia] {
                pointer-events: none;
            }
        </style>

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <div class="col-6 text-right">					  
                        <?php if($perfil_Config): ?>
                            <div class="button-items">
                                <a href="<?= base_url('ponto/ocorrencia/config') ?>" class="btn btn-primary btn-xxs mb-0"><i class="mdi mdi-clock-alert-outline"></i> Config. Horarios</a>
                                <a href="<?= base_url('ponto/ocorrencia/motorista') ?>" class="btn btn-primary btn-xxs mb-0"><i class="mdi mdi-briefcase-edit-outline"></i> Config. Cargos</a>
                                <a href="<?= base_url('ponto/ocorrencia/config_ocorrencias') ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Config. Tipos</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

				
                <div class="card-body">
					
                    <?= exibeMensagem(true); ?>

                    <form action="" method="post" name="form_filtro" id="form_filtro">
                        <div class="row">
                            

                            <div class="col-8">
                                <div class="row">

                                    <label for="periodo" class="col-2 col-form-label text-right pr-0 pl-0">Período:</label>
                                    <div class="col-10">
                                        <select name="periodo" id="periodo" class="form-control form-control-sm">
                                            <option value="">...</option>
                                            <?php if($resPeriodo): ?>
                                                <?php foreach($resPeriodo as $key => $Periodo): ?>
                                                    <option value="<?= date('Y-m-d', strtotime($Periodo['INICIOMENSAL'])).date('Y-m-d', strtotime($Periodo['FIMMENSAL'])); ?>"  <?= (($post['periodo'] ?? "") == date('Y-m-d', strtotime($Periodo['INICIOMENSAL'])).date('Y-m-d', strtotime($Periodo['FIMMENSAL']))) ? " selected " : ""; ?>>Período de <?= dtBr($Periodo['INICIOMENSAL']).' à '.dtBr($Periodo['FIMMENSAL']); ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <label for="data_ini_fim" class="col-2 col-form-label text-right pr-0 pl-0">Data:</label>
                                    <div class="col-10 input-group">
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control form-control-sm" value="<?= $post['data_inicio'] ?? ""; ?>">
                                        <div class="input-group-prepend input-group-append"><span class="input-group-text form-control-sm">até</span></div>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control form-control-sm" value="<?= $post['data_fim'] ?? ""; ?>">
                                    </div>
                                    <label for="filial" class="col-2 col-form-label text-right pr-0 pl-0">Filiais:</label>
                                    <div class="col-10">
                                        <select name="filial" id="filial" class="form-control form-control-sm">
                                            <option value="">- Todos -</option>
                                            <?php if($resFilial): ?>
                                                <?php foreach($resFilial as $key => $Filial): ?>
                                                    <option value="<?= $Filial['CODFILIAL']; ?>" <?= (($post['filial'] ?? "") == $Filial['CODFILIAL']) ? " selected " : ""; ?>><?= $Filial['CODFILIAL'].' - '.$Filial['NOMEFILIAL']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <label for="secao" class="col-2 col-form-label text-right pr-0 pl-0">Seção:</label>
                                    <div class="col-10">
                                        <select class="select2 mb-3 select2-multiple" name="secao[]" style="width: 100%" multiple="multiple" data-placeholder="- Todos -">
                                            <?php if($resSecao): ?>
                                                <?php foreach($resSecao as $key => $Secao): ?>
                                                    <?php
                                                    // filtro por seção
                                                    $secao_selecionada = "";
                                                    if(is_array($post['secao'] ?? "")){
                                                        if(in_array($Secao['CODSECAO'], $post['secao'])) $secao_selecionada = " selected ";
                                                    }
                                                    ?>
                                                    <option value="<?= $Secao['CODSECAO']; ?>" <?= $secao_selecionada; ?>><?= $Secao['CODSECAO'].' - '.$Secao['NOMESECAO']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <!--
                                    <label for="abono" class="col-2 col-form-label text-right pr-0 pl-0">Tipo Abono:</label>
                                    <div class="col-10">
                                        <select name="abono" id="abono" class="form-control form-control-sm">
                                            <option value="">...</option>
                                            <?php if($resAbono): ?>
                                                <?php foreach($resAbono as $key => $Abono): ?>
                                                    <option value="<?= $Abono['CODIGO']; ?>" <?= (($post['abono'] ?? "") == $Abono['CODIGO']) ? " selected " : ""; ?>><?= $Abono['DESCRICAO'].' - '.$Abono['CODIGO']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    -->
                                    <div class="col-2 text-right pr-0">
                                        
                                    </div>
                                    <div class="col-10">
                                        <input id="ja_justificados" type="checkbox" name="ja_justificados" class="form-check-label mt-1" value="1" <?= (($post['ja_justificados'] ?? 0) == 1) ? " checked " : ""; ?>>
                                        <label for="ja_justificados" class="col-11 col-form-label text-left pt-0 pb-0">Exibir ocorrência justificadas</label>
                                    </div>

                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row">

                                    <table class="table table-bordered mb-0 table-centered table-sm table-striped" style="border-collapse: collapse; border-spacing: 0; width: 100%; font-size: 11px;">
                                        <thead>
                                            <tr>
                                                <th>OCORRÊNCIA</th>
                                                <th class="text-center">TRATADO</th>
                                                <th class="text-center">NÃO TRATADO</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="excesso_abono_gestor" class="form-check-label mt-1 hidden" value="1" checked> <label for="excesso_abono_gestor" class="col-form-label text-left pt-0 pb-0">Excesso de Abono Gestor (Superior a 5 dias consecutivos)</label></td>
                                                <td data-excesso_abono_gestor_tratado class="text-center">0</td>
                                                <td data-excesso_abono_gestor_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['extra_acima'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="extra_permitido" class="form-check-label mt-1 hidden" value="1" checked> <label for="extra_permitido" class="col-form-label text-left pt-0 pb-0">Extra Acima do Permitido</label></td>
                                                <td data-extra_permitido_tratado class="text-center">0</td>
                                                <td data-extra_permitido_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['extra_especial'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="extra" class="form-check-label mt-1 hidden" value="1" checked> <label for="extra" class="col-form-label text-left pt-0 pb-0">Extra em Escala Especial</label></td>
                                                <td data-extra_tratado class="text-center">0</td>
                                                <td data-extra_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['interjornada'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="interjornada" class="form-check-label mt-1 hidden" value="1" checked> <label for="interjornada" class="col-form-label text-left pt-0 pb-0">Interjornada</label></td>
                                                <td data-interjornada_tratado class="text-center">0</td>
                                                <td data-interjornada_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
											<?php if($TiposOcorrencia['intrajornada'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="intrajornada" class="form-check-label mt-1 hidden" value="1" checked> <label for="intrajornada" class="col-form-label text-left pt-0 pb-0">Intrajornada</label></td>
                                                <td data-intrajornada_tratado class="text-center">0</td>
                                                <td data-intrajornada_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>		   
                                            <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="jornada" class="form-check-label mt-1 hidden" value="1" checked> <label for="jornada" class="col-form-label text-left pt-0 pb-0">Excesso de jornada</label></td>
                                                <td data-jornada_tratado class="text-center">0</td>
                                                <td data-jornada_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['registro_bri'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="registro_britanico" class="form-check-label mt-1 hidden" value="1" checked> <label for="registro_britanico" class="col-form-label text-left pt-0 pb-0">Registro Britânico</label></td>
                                                <td data-registro_britanico_tratado class="text-center">0</td>
                                                <td data-registro_britanico_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['registro_manual'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="registro_manual" class="form-check-label mt-1 hidden" value="1" checked> <label for="registro_manual" class="col-form-label text-left pt-0 pb-0">Registro Manual</label></td>
                                                <td data-registro_manual_tratado class="text-center">0</td>
                                                <td data-registro_manual_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['req_troca'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="pendente_termo_aditivo" class="form-check-label mt-1 hidden" value="1" checked> <label for="pendente_termo_aditivo" class="col-form-label text-left pt-0 pb-0">Req. troca de escala pendente termo aditivo</label></td>
                                                <td data-pendente_termo_aditivo_tratado class="text-center">0</td>
                                                <td data-pendente_termo_aditivo_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['sobreaviso'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="sobreaviso" class="form-check-label mt-1 hidden" value="1" checked> <label for="sobreaviso" class="col-form-label text-left pt-0 pb-0">Sobreaviso</label></td>
                                                <td data-sobreaviso_tratado class="text-center">0</td>
                                                <td data-sobreaviso_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="trabalho_dsr_folga" class="form-check-label mt-1 hidden" value="1" checked> <label for="trabalho_dsr_folga" class="col-form-label text-left pt-0 pb-0">Trabalho em DSR ou Folga</label></td>
                                                <td data-trabalho_dsr_folga_tratado class="text-center">0</td>
                                                <td data-trabalho_dsr_folga_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="trabalho_dsr_folga_descanso" class="form-check-label mt-1 hidden" value="1" checked> <label for="trabalho_dsr_folga_descanso" class="col-form-label text-left pt-0 pb-0">Excesso de jornada semanal</label></td>
                                                <td data-trabalho_dsr_folga_descanso_tratado class="text-center">0</td>
                                                <td data-trabalho_dsr_folga_descanso_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="trabalho_ferias_afastamento" class="form-check-label mt-1 hidden" value="1" checked> <label for="trabalho_ferias_afastamento" class="col-form-label text-left pt-0 pb-0">Trabalho em Férias ou Afastamentos</label></td>
                                                <td data-trabalho_ferias_afastamento_tratado class="text-center">0</td>
                                                <td data-trabalho_ferias_afastamento_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="trabalho_6dias" class="form-check-label mt-1 hidden" value="1" checked> <label for="trabalho_6dias" class="col-form-label text-left pt-0 pb-0">Trabalho superior à 6 (seis) dias consecutivos sem folga</label></td>
                                                <td data-trabalho_6dias_tratado class="text-center">0</td>
                                                <td data-trabalho_6dias_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['troca_menor10'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="troca_menor_10_dias" class="form-check-label mt-1 hidden" value="1" checked> <label for="troca_menor_10_dias" class="col-form-label text-left pt-0 pb-0">Troca de escala menor que 3 dias</label></td>
                                                <td data-troca_menor_10_dias_tratado class="text-center">0</td>
                                                <td data-troca_menor_10_dias_nao_tratado class="text-center">0</td>
                                            <?php endif; ?>
                                            <?php if($TiposOcorrencia['troca_menor6'] == 1): ?>
                                            <tr>
                                                <td><input data-ocorrencia type="checkbox" name="troca_menor_6_meses" class="form-check-label mt-1 hidden" value="1" checked> <label for="troca_menor_6_meses" class="col-form-label text-left pt-0 pb-0">Troca de escala menor que 6 meses</label></td>
                                                <td data-troca_menor_6_meses_tratado class="text-center">0</td>
                                                <td data-troca_menor_6_meses_nao_tratado class="text-center">0</td>
                                            </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td style="background-color: #dee2e6;"><label class="col-form-label text-left pt-0 pb-0">TOTAIS</label></td>
                                                <td data-total-tratado class="text-center" style="background-color: #dee2e6; font-weight: bold;"><label class="col-form-label text-left pt-0 pb-0">0</label></td>
                                                <td data-total class="text-center" style="background-color: #dee2e6; font-weight: bold;">0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                </div>
                            </div>

                            
                        </div>
                    </form>
                        

                </div>

                <div class="card-footer text-center">
					<button class="btn btn-primary btn-xxs" type="button" onclick="return buscaOcorrencia()"><i class="fas fa-search"></i> Exibir Ocorrência</button>
                    <button class="btn btn-success btn-xxs" type="button" onclick="return excelOcorrencia()"><i class="fas fa-file-excel"></i> Gerar Excel</button>
                    <?php if($arquivo_morto): ?><button class="btn btn-success btn-xxs" type="button" onclick="return excelOcorrenciaArquivoMorto()"><i class="fas fa-file-excel"></i> Gerar Excel Arquivo Morto</button><?php endif; ?>
				</div>

            </div>

            <?php
            $qtde_ocorrencia = false;
            $qtde_excesso_abono_gestor = 0;
            $qtde_extra_permitido = 0;
            $qtde_extra = 0;
            $qtde_interjornada = 0;
            $qtde_intrajornada = 0;
            $qtde_jornada = 0;
            $qtde_registro_britanico = 0;
            $qtde_registro_manual = 0;
            $qtde_pendente_termo_aditivo = 0;
            $qtde_sobreaviso = 0;
            $qtde_trabalho_dsr_folga = 0;
            $qtde_trabalho_dsr_folga_descanso = 0;
            $qtde_trabalho_ferias_afastamento = 0;
            $qtde_trabalho_6dias = 0;
            $qtde_troca_menor_10_dias = 0;
            $qtde_troca_menor_6_meses = 0;
            ?>
            <?php if($resFuncionario): ?>
            <div class="card">
                    
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-header mt-0">
                            <div class="row">
                            <h4 class="col-12 mb-1 mt-1">Ocorrências</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <button class="btn btn-info btn-xxs mb-3 botao_coletivo" type="button" id="botao_coletivo"><i class="mdi mdi-check-box-multiple-outline"></i> Detalhamento da Ocorrência - Coletiva</button>
                    <!--<button class="btn btn-primary btn-xxs mb-3 botao_informacao_coletivo" type="button" id="botao_informacao_coletivo"><i class="mdi mdi-check-box-multiple-outline"></i> Informação Complementar - Coletiva</button>-->

                    <div class="card hidden card_coletiva">
                        <div class="card-header mt-0 bg-success pr-5 pt-1 pb-1">
                            <div class="row">
                                <b class="col-12 mb-1 mt-1 text-white">Detalhamento da Ocorrência Coletiva <button style="float: right;" type="button" class="btn btn-danger btn-xxs botao_coletivo" aria-label="Close"><i class="mdi mdi-close"></i></button></b>
                            </div>
                        </div>
                        <div class="card-body" style="border: 1px solid #dddddd;">
                            <div class="row mb-2">
                                <label for="all_motivo" class="col-2 col-form-label text-right pr-0 pl-0">DETALHAMENTO DA OCORRÊNCIA:</label>
                                <div class="col-10">
                                    <select name="all_motivo" id="all_motivo" class="form-control form-control-sm">
                                        <?php if($resMotivo): ?>
                                            <option value="">...</option>
                                            <?php foreach($resMotivo as $key2 => $Motivo): ?>
                                                <option value="<?= $Motivo['id']; ?>"><?= $Motivo['descricao']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <label for="all_outro_motivo" class="col-2 col-form-label text-right pr-0 pl-0 coletiva_outro_motivo hidden">DESCRIÇÃO OUTRO MOTIVO:</label>
                                <div class="col-10 coletiva_outro_motivo hidden">
                                    <textarea name="all_outro_motivo" id="all_outro_motivo" cols="30" rows="2" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="all_informacao_complementar" class="col-2 col-form-label text-right pr-0 pl-0">INFORMAÇÃO COMPLEMENTAR:</label>
                                <div class="col-10">
                                    <textarea name="all_informacao_complementar" id="all_informacao_complementar" cols="30" rows="2" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label for="all_anexo" class="col-2 col-form-label text-right pr-0 pl-0">ANEXO:</label>
                                <div class="col-10">
                                    <input type="file" name="all_anexo" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-success btn-xxs" type="button" onclick="return aplicaOcorrenciaColetiva()"><i class="fas fa-check"></i> Aplicar Detalhamento da Ocorrência</button>
                        </div>
                    </div>

                    <div class="card hidden card_informacao_coletiva">
                        <div class="card-header mt-0 bg-primary pr-5 pt-1 pb-1">
                            <div class="row">
                                <b class="col-12 mb-1 mt-1 text-white">Informação Complementar Coletiva <button style="float: right;" type="button" class="btn btn-danger btn-xxs botao_informacao_coletivo" aria-label="Close"><i class="mdi mdi-close"></i></button></b>
                            </div>
                        </div>
                        <div class="card-body" style="border: 1px solid #dddddd;">
                            <div class="row mb-2">
                                <label for="all_informacao_complementar" class="col-2 col-form-label text-right pr-0 pl-0">INFORMAÇÃO COMPLEMENTAR:</label>
                                <div class="col-10">
                                    <textarea name="all_informacao_complementar" id="all_informacao_complementar" cols="30" rows="2" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn btn-success btn-xxs" type="button" onclick="return aplicaInformacaoComplementarColetiva()"><i class="fas fa-check"></i> Aplicar Informação Complementar</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">                
                    <div class="">
                    <?php ob_start(); ?>
                    <table id="datatableOcorrencia" class="table table-bordered" style="border-collapse: collapse; border-spacing: 0; width: 100%; font-size: 11px; background: #ffffff;">
                        <thead>
                            <tr>
                                <th style="font-size: 11px;" class="text-center"><input type="checkbox" data-checkall></th>
                                <th style="font-size: 11px;" class="text-center">CHAPA</th>
                                <th style="font-size: 11px;">NOME</th>
                                <th style="font-size: 11px;">FUNÇÃO</th>
                                <th style="font-size: 11px;">SEÇÃO</th>
                                <th style="font-size: 11px;">SITUAÇÃO</th>
                                <th class="text-center" style="font-size: 11px;">DATA</th>
                                
                                <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Excesso de Abono Gestor (Superior a 5 dias consecutivos)</th><?php endif; ?>
                                <?php if($TiposOcorrencia['extra_acima'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Extra Acima do Permitido</th><?php endif; ?>
                                <?php if($TiposOcorrencia['extra_especial'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Extra em Escala Especial</th><?php endif; ?>
                                <?php if($TiposOcorrencia['interjornada'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Interjornada</th><?php endif; ?>
								<?php if($TiposOcorrencia['intrajornada'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Intrajornada</th><?php endif; ?>
                                <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Excesso de jornada</th><?php endif; ?>
                                <?php if($TiposOcorrencia['registro_bri'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Registro Britânico</th><?php endif; ?>
                                <?php if($TiposOcorrencia['registro_manual'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Registro Manual</th><?php endif; ?>
                                <?php if($TiposOcorrencia['req_troca'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Req. troca de escala pendente termo aditivo</th><?php endif; ?>
                                <?php if($TiposOcorrencia['sobreaviso'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Sobreaviso</th><?php endif; ?>
                                <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Trabalho em DSR ou Folga</th><?php endif; ?>
                                <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Excesso de jornada semanal</th><?php endif; ?>
                                <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Trabalho em Férias ou Afastamentos</th><?php endif; ?>
                                <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Trabalho superior à 6 (seis) dias consecutivos sem folga</th><?php endif; ?>
                                <?php if($TiposOcorrencia['troca_menor10'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Troca de escala menor que 3 dias</th><?php endif; ?>
                                <?php if($TiposOcorrencia['troca_menor6'] == 1): ?><th class="text-center" width="150" style="white-space: normal !important; font-size: 11px;">Troca de escala menor que 6 meses</th><?php endif; ?>

                                <th style="font-size: 11px;">DETALHAMENTO DA OCORRÊNCIA</th>
                                <th style="font-size: 11px;">INFORMAÇÃO COMPLEMENTAR</th>
								<th style="font-size: 11px;">ANEXO</th>
                                <th style="font-size: 11px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // regra para permitir justificar
                            //$data_fim_periodo_fitro = date('Ym');
                            //$periodo_limite = 202209;
                            $periodo_limite = date('Ym');
                            $periodo_fim_periodo = date('Ym', strtotime(substr($post['periodo'], 10)));
                            ?>
                            <?php $linha = 0; ?>
                            <?php foreach($resDadosFunc as $key => $DadosFunc): ?>
                                
                                <?php foreach($DadosFunc['DATAS'] as $key2 => $Data): ?>
                                    <?php $observacao = ''; ?>
                                    <?php
                                    $codmotivo = (isset($resJustificativas[$DadosFunc['CHAPA']][$Data]['codmotivo'])) ? $resJustificativas[$DadosFunc['CHAPA']][$Data]['codmotivo'] : "";
                                    $justificativa_extra = (isset($resJustificativas[$DadosFunc['CHAPA']][$Data]['justificativa_extra'])) ? $resJustificativas[$DadosFunc['CHAPA']][$Data]['justificativa_extra'] : "";
                                    $codsituacao = (isset($resJustificativas[$DadosFunc['CHAPA']][$Data]['CODSITUACAO'])) ? $resJustificativas[$DadosFunc['CHAPA']][$Data]['CODSITUACAO'] : "";
                                    $observacao = (isset($resJustificativas[$DadosFunc['CHAPA']][$Data]['observacao'])) ? $resJustificativas[$DadosFunc['CHAPA']][$Data]['observacao'] : "";
                                    $id_anexo = (isset($resJustificativas[$DadosFunc['CHAPA']][$Data]['id_anexo'])) ? $resJustificativas[$DadosFunc['CHAPA']][$Data]['id_anexo'] : "";
                                    $file_name = (isset($resJustificativas[$DadosFunc['CHAPA']][$Data]['file_name'])) ? $resJustificativas[$DadosFunc['CHAPA']][$Data]['file_name'] : "";

                                    $data_da_ocorrencia = date('Ym', strtotime($Data));
                                    $permite_alteracao = (($periodo_fim_periodo < $periodo_limite) ? false : true);
                                    if($congelado) $permite_alteracao = false;
                                    ?>
                                    <tr data-linha="<?= $linha; ?>">
                                        <td class="text-center"><?php if(!$bloqueiaAlteracao): ?><input type="checkbox" data-checkbox="<?= $linha; ?>" data-registro="<?= dtBr($Data).'|'.$DadosFunc['CHAPA'].'|'; ?>"><?php endif; ?></td>
                                        <td class="text-center"><?= $DadosFunc['CHAPA']; ?></td>
                                        <td><?= $DadosFunc['NOME']; ?></td>
                                        <td><?= $DadosFunc['NOMEFUNCAO']; ?></td>
                                        <td><?= $DadosFunc['NOMESECAO']; ?></td>
                                        <td><?= $codsituacao; ?></td>
                                        <td class="text-center"><a href="javascript:void(0);" onclick="abreBatidasDia('<?= $DadosFunc['CHAPA']; ?>', '<?= dtBr($Data); ?>', '<?= $DadosFunc['NOME']; ?>', '<?= $DadosFunc['HORARIO']; ?>')"><?= dtBr($Data); ?></a><br><b><?= diaSemana($Data, true); ?></b>
                                            
                                        </td>
                                        <?php $ocorrencias = $resFuncionario[$DadosFunc['CHAPA']][$Data]; ?>
                                        <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "excesso_abono_gestor"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    

                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        $qtde_excesso_abono_gestor += (1);
                                                        if(isset($qtde_ocorrencia['excesso_abono_gestor'])){
                                                            $qtde_ocorrencia['excesso_abono_gestor']['qtde'] = $qtde_ocorrencia['excesso_abono_gestor']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['excesso_abono_gestor']['qtde'] = 1;
                                                            $qtde_ocorrencia['excesso_abono_gestor']['ocorrencia'] = 'excesso_abono_gestor';
                                                        }
                                                    }

                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['extra_acima'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "extra_permitido"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_extra_permitido += (1);
                                                        if(isset($qtde_ocorrencia['extra_permitido'])){
                                                            $qtde_ocorrencia['extra_permitido']['qtde'] = $qtde_ocorrencia['extra_permitido']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['extra_permitido']['qtde'] = 1;
                                                            $qtde_ocorrencia['extra_permitido']['ocorrencia'] = 'extra_permitido';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['extra_especial'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "extra"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_extra += (1);
                                                        if(isset($qtde_ocorrencia['extra'])){
                                                            $qtde_ocorrencia['extra']['qtde'] = $qtde_ocorrencia['extra']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['extra']['qtde'] = 1;
                                                            $qtde_ocorrencia['extra']['ocorrencia'] = 'extra';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['interjornada'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "interjornada"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['COMPLEMENTO']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['COMPLEMENTO']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_interjornada += (1);
                                                        if(isset($qtde_ocorrencia['interjornada'])){
                                                            $qtde_ocorrencia['interjornada']['qtde'] = $qtde_ocorrencia['interjornada']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['interjornada']['qtde'] = 1;
                                                            $qtde_ocorrencia['interjornada']['ocorrencia'] = 'interjornada';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
										<?php if($TiposOcorrencia['intrajornada'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "intrajornada"){
													$valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['COMPLEMENTO']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['COMPLEMENTO']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
														if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_intrajornada += (1);
                                                        if(isset($qtde_ocorrencia['intrajornada'])){
                                                            $qtde_ocorrencia['intrajornada']['qtde'] = $qtde_ocorrencia['intrajornada']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['intrajornada']['qtde'] = 1;
                                                            $qtde_ocorrencia['intrajornada']['ocorrencia'] = 'intrajornada';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "jornada"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_jornada += (1);
                                                        if(isset($qtde_ocorrencia['jornada'])){
                                                            $qtde_ocorrencia['jornada']['qtde'] = $qtde_ocorrencia['jornada']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['jornada']['qtde'] = 1;
                                                            $qtde_ocorrencia['jornada']['ocorrencia'] = 'jornada';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['registro_bri'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "registro_britanico"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_registro_britanico += (1);
                                                        if(isset($qtde_ocorrencia['registro_britanico'])){
                                                            $qtde_ocorrencia['registro_britanico']['qtde'] = $qtde_ocorrencia['registro_britanico']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['registro_britanico']['qtde'] = 1;
                                                            $qtde_ocorrencia['registro_britanico']['ocorrencia'] = 'registro_britanico';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['registro_manual'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "registro_manual"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_registro_manual += (1);
                                                        if(isset($qtde_ocorrencia['registro_manual'])){
                                                            $qtde_ocorrencia['registro_manual']['qtde'] = $qtde_ocorrencia['registro_manual']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['registro_manual']['qtde'] = 1;
                                                            $qtde_ocorrencia['registro_manual']['ocorrencia'] = 'registro_manual';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['req_troca'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "pendente_termo_aditivo"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_pendente_termo_aditivo += (1);
                                                        if(isset($qtde_ocorrencia['pendente_termo_aditivo'])){
                                                            $qtde_ocorrencia['pendente_termo_aditivo']['qtde'] = $qtde_ocorrencia['pendente_termo_aditivo']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['pendente_termo_aditivo']['qtde'] = 1;
                                                            $qtde_ocorrencia['pendente_termo_aditivo']['ocorrencia'] = 'pendente_termo_aditivo';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['sobreaviso'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "sobreaviso"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_sobreaviso += (1);
                                                        if(isset($qtde_ocorrencia['sobreaviso'])){
                                                            $qtde_ocorrencia['sobreaviso']['qtde'] = $qtde_ocorrencia['sobreaviso']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['sobreaviso']['qtde'] = 1;
                                                            $qtde_ocorrencia['sobreaviso']['ocorrencia'] = 'sobreaviso';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "trabalho_dsr_folga"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_trabalho_dsr_folga += (1);
                                                        if(isset($qtde_ocorrencia['trabalho_dsr_folga'])){
                                                            $qtde_ocorrencia['trabalho_dsr_folga']['qtde'] = $qtde_ocorrencia['trabalho_dsr_folga']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['trabalho_dsr_folga']['qtde'] = 1;
                                                            $qtde_ocorrencia['trabalho_dsr_folga']['ocorrencia'] = 'trabalho_dsr_folga';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "trabalho_dsr_folga_descanso"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_trabalho_dsr_folga_descanso += (1);
                                                        if(isset($qtde_ocorrencia['trabalho_dsr_folga_descanso'])){
                                                            $qtde_ocorrencia['trabalho_dsr_folga_descanso']['qtde'] = $qtde_ocorrencia['trabalho_dsr_folga_descanso']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['trabalho_dsr_folga_descanso']['qtde'] = 1;
                                                            $qtde_ocorrencia['trabalho_dsr_folga_descanso']['ocorrencia'] = 'trabalho_dsr_folga_descanso';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "trabalho_ferias_afastamento"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_trabalho_ferias_afastamento += (1);
                                                        if(isset($qtde_ocorrencia['trabalho_ferias_afastamento'])){
                                                            $qtde_ocorrencia['trabalho_ferias_afastamento']['qtde'] = $qtde_ocorrencia['trabalho_ferias_afastamento']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['trabalho_ferias_afastamento']['qtde'] = 1;
                                                            $qtde_ocorrencia['trabalho_ferias_afastamento']['ocorrencia'] = 'trabalho_ferias_afastamento';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "trabalho_6dias"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_trabalho_6dias += (1);
                                                        if(isset($qtde_ocorrencia['trabalho_6dias'])){
                                                            $qtde_ocorrencia['trabalho_6dias']['qtde'] = $qtde_ocorrencia['trabalho_6dias']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['trabalho_6dias']['qtde'] = 1;
                                                            $qtde_ocorrencia['trabalho_6dias']['ocorrencia'] = 'trabalho_6dias';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['troca_menor10'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "troca_menor_10_dias"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        if(strlen(trim(rtrim($valor, " | "))) > 0) $qtde_troca_menor_10_dias += (1);
                                                        if(isset($qtde_ocorrencia['troca_menor_10_dias'])){
                                                            $qtde_ocorrencia['troca_menor_10_dias']['qtde'] = $qtde_ocorrencia['troca_menor_10_dias']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['troca_menor_10_dias']['qtde'] = 1;
                                                            $qtde_ocorrencia['troca_menor_10_dias']['ocorrencia'] = 'troca_menor_10_dias';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <?php if($TiposOcorrencia['troca_menor6'] == 1): ?>
                                        <td>
                                            <?php
                                            $valor = "";
                                            foreach($ocorrencias as $key3 => $Ocorrencias){
                                                if($Ocorrencias['OCORRENCIA'] == "troca_menor_6_meses"){
                                                    $valor = "";
                                                    if(!$congelado){
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }else{
                                                        if($Ocorrencias['SISTEMA'] == "RM"){
                                                            $valor .= dadosOcorrencia($Ocorrencias)." | ";
                                                        }else{
                                                            $valor .= $Ocorrencias['VALOR']." | ";
                                                        }
                                                    }
                                                    if(strlen(trim($codmotivo)) <= 0){
                                                        //if(strlen(trim(rtrim($valor, " | "))) > 0){$valor .= '('.$qtde_troca_menor_6_meses.')'; $qtde_troca_menor_6_meses += (1);}
                                                        if(isset($qtde_ocorrencia['troca_menor_6_meses'])){
                                                            $qtde_ocorrencia['troca_mtroca_menor_6_mesesenor_10_dias']['qtde'] = $qtde_ocorrencia['troca_menor_6_meses']['qtde'] + 1;
                                                        }else{
                                                            $qtde_ocorrencia['troca_menor_6_meses']['qtde'] = 1;
                                                            $qtde_ocorrencia['troca_menor_6_meses']['ocorrencia'] = 'troca_menor_6_meses';
                                                        }
                                                    }
                                                }
                                            }
                                            echo rtrim($valor, " | ");
                                            ?>
                                        </td>
                                        <?php endif; ?>
                                        <td>
                                            <select <?= ($bloqueiaAlteracao) ? " disabled " : ""; ?> data-motivo="<?= $linha; ?>" <?= strlen(trim(($codmotivo))) > 0 ? " disabled " : ""; ?> name="motivo[]" class="form-control form-control-sm">
                                                <?php if($resMotivo): ?>
                                                    <option value="">...</option>
                                                    <?php foreach($resMotivo as $key3 => $Motivo): ?>
                                                        <option value="<?= $Motivo['id']; ?>" <?= ((int)$codmotivo == $Motivo['id']) ? " selected " : ""; ?>><?= $Motivo['descricao']; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                        <td><textarea <?= ($bloqueiaAlteracao) ? " disabled " : ""; ?> name="observacao[]" data-observacao="<?= $linha; ?>" class="form-control" cols="30" rows="1"><?= (strlen(trim($observacao)) <=0) ? $justificativa_extra : $observacao; ?></textarea></td>
                                        <td data-print-anexo="<?= $linha; ?>" class="text-center">
                                            <?php if(strlen(trim($file_name)) > 0): ?>
                                                <div class="btn-group" aria-label="acao" role="group">
                                                    <a href="<?= base_url('ponto/ocorrencia/anexo/'.base64_encode($id_anexo)); ?>" class="btn btn-xxs btn-primary" title="<?= $file_name; ?>" target="_blank"><i class="mdi mdi-cloud-download"></i> download</a> 
                                                    <?php if(!$bloqueiaAlteracao): ?><button onclick="return excluirAnexo('<?= dtEn($Data, true); ?>', '<?= $DadosFunc['CHAPA']; ?>')" type="button" class="btn btn-xxs btn-danger bg-danger"><i class="fa fa-times"></i></button><?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <?php if(!$bloqueiaAlteracao): ?><input type="file" name="anexo[]" data-anexo="<?= $linha; ?>" class="dropify" data-allowed-file-extensions="jpg jpeg png pdf" data-show-loader="true" /><?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(!$bloqueiaAlteracao): ?><button data-save="<?= $linha; ?>" class="btn btn-xxs btn-success" onclick="salvaMotivo(<?= $linha; ?>, '<?= dtBr($Data); ?>', '<?= $DadosFunc['CHAPA']; ?>')">Salvar</button><?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php $linha++; ?>
                                    <?php unset($Data); ?>
                                <?php endforeach; ?>

                                <?php unset($DadosFunc); ?>
                            <?php endforeach; ?>
                                
                        
                            
                        </tbody>
                    </table>
                    <?php
                    $html = ob_get_clean();
                    $html = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $html);
                    echo $html;
                    ?>
                    </div></div>   </div>   

                </div>
                <?php endif; ?>

            </div>

        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 25,
        "aaSorting"         : [[0, "desc"]],
        "fixedHeader": true,
        initComplete: function () {
            var p_linha = this.api().columns()[0].length;
            this.api()
                .columns()
                .every(function () {
                    var column = this;
                    
                    if(column[0][0] == 0 || column[0][0] >= (p_linha-3)) return false;
                    var select = $('<select class="form-control form-control-sm filtro_table"><option value=""></option></select>')
                        .appendTo($(column.header()))
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
 
                    column
                        .data()
                        .unique()
                        .sort()
                        .each(function (d, j) {
                            if(j == 0) select.append('<option value="">- Todos -</option>');
                            if(d != "") select.append('<option value="' + d + '">' + d + '</option>');
                        });
                        
                });

                $(".filtro_table").select2({
                    width: '100%',
                    language: {
                        noResults: function(){
                            return 'Nenhum resultado encontrado';
                        }
                    }
                });
        },
    });
});
const buscaOcorrencia = () => {

    var periodo = $("#periodo").val();
    var data_inicio = $("#data_inicio").val();
    var data_fim = $("#data_fim").val();
    var filial = $("#filial").val();
    var abono = $("#abono").val();

    if(periodo == ""){ exibeAlerta("error", "<b>Período</b> não selecionado."); return false; }
    if(data_inicio == ""){ exibeAlerta("error", "<b>Data de Inicio</b> não informada."); return false; }
    if(data_fim == ""){ exibeAlerta("error", "<b>Data Final</b> não informada."); return false; }

    var periodo_inicio = periodo.substr(0, 10);
    var periodo_fim = periodo.substr(10);
    
    if(data_inicio > data_fim){ exibeAlerta("error", "<b>Data de Inicio</b> não pode ser maior que a <b>Data Fim</b>."); return false; }
    if(data_inicio < periodo_inicio || data_inicio > periodo_fim){ exibeAlerta("error", "<b>Data de Inicio</b> fora do período informado."); return false; }
    if(data_fim < periodo_inicio || data_fim > periodo_fim){ exibeAlerta("error", "<b>Data Fim</b> fora do período informado."); return false; }

    if($("[data-ocorrencia]:checked").length <= 0){ exibeAlerta("error", "Tipo de ocorrência não informado."); return false; }

    openLoading();

    $("#form_filtro").attr('action','<?= base_url('ponto/ocorrencia'); ?>').attr('target', '_self');
    $("#form_filtro").submit();

}
const excelOcorrencia = () => {

    var periodo = $("#periodo").val();
    var data_inicio = $("#data_inicio").val();
    var data_fim = $("#data_fim").val();
    var filial = $("#filial").val();
    var abono = $("#abono").val();

    if(periodo == ""){ exibeAlerta("error", "<b>Período</b> não selecionado."); return false; }
    if(data_inicio == ""){ exibeAlerta("error", "<b>Data de Inicio</b> não informada."); return false; }
    if(data_fim == ""){ exibeAlerta("error", "<b>Data Final</b> não informada."); return false; }

    var periodo_inicio = periodo.substr(0, 10);
    var periodo_fim = periodo.substr(10);

    if(data_inicio > data_fim){ exibeAlerta("error", "<b>Data de Inicio</b> não pode ser maior que a <b>Data Fim</b>."); return false; }
    if(data_inicio < periodo_inicio || data_inicio > periodo_fim){ exibeAlerta("error", "<b>Data de Inicio</b> fora do período informado."); return false; }
    if(data_fim < periodo_inicio || data_fim > periodo_fim){ exibeAlerta("error", "<b>Data Fim</b> fora do período informado."); return false; }

    if($("[data-ocorrencia]:checked").length <= 0){ exibeAlerta("error", "Tipo de ocorrência não informado."); return false; }

    openLoading();

    setTimeout(() => {
        window.location='<?= base_url('ponto/ocorrencia'); ?>';
    }, 4000);

    $("#form_filtro").attr('action','<?= base_url('ponto/ocorrencia/excel'); ?>').attr('target', '_blank');
    $("#form_filtro").submit();

}



const excelOcorrenciaArquivoMorto = () => {

    var periodo = $("#periodo").val();
    var data_inicio = $("#data_inicio").val();
    var data_fim = $("#data_fim").val();
    var filial = $("#filial").val();
    var abono = $("#abono").val();

    if(periodo == ""){ exibeAlerta("error", "<b>Período</b> não selecionado."); return false; }
    if(data_inicio == ""){ exibeAlerta("error", "<b>Data de Inicio</b> não informada."); return false; }
    if(data_fim == ""){ exibeAlerta("error", "<b>Data Final</b> não informada."); return false; }

    var periodo_inicio = periodo.substr(0, 10);
    var periodo_fim = periodo.substr(10);

    if(data_inicio > data_fim){ exibeAlerta("error", "<b>Data de Inicio</b> não pode ser maior que a <b>Data Fim</b>."); return false; }
    if(data_inicio < periodo_inicio || data_inicio > periodo_fim){ exibeAlerta("error", "<b>Data de Inicio</b> fora do período informado."); return false; }
    if(data_fim < periodo_inicio || data_fim > periodo_fim){ exibeAlerta("error", "<b>Data Fim</b> fora do período informado."); return false; }

    if($("[data-ocorrencia]:checked").length <= 0){ exibeAlerta("error", "Tipo de ocorrência não informado."); return false; }

    openLoading();

    setTimeout(() => {
        window.location='<?= base_url('ponto/ocorrencia'); ?>';
    }, 4000);

    $("#form_filtro").attr('action','<?= base_url('ponto/ocorrencia/excel_arquivo_morto'); ?>').attr('target', '_blank');
    $("#form_filtro").submit();

}
<?php if(!$bloqueiaAlteracao): ?>
const excluirAnexo = (data, chapa) => {

    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente excluir este <b>anexo</b>?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim Excluir`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if (result.isConfirmed) {

			let dados = {
                "data"  : data,
                "chapa" : chapa
            };

            openLoading();

			$.ajax({
				url: "<?= base_url('ponto/ocorrencia/action/excluir_anexo') ?>",
				type:'POST',
				data:dados,
				success:function(result){
					var response = JSON.parse(result);
					exibeAlerta(response.tipo, response.msg);
                    buscaOcorrencia();
				},
			});

		}
	});

}
<?php endif; ?>	   
const workflowOcorrencias = () => {

   

    $("#form_filtro").attr('action','<?= base_url('ponto/ocorrencia/workflow'); ?>').attr('target', '_blank');
    $("#form_filtro").submit();

}

const workflow2Ocorrencias = () => {

   

    $("#form_filtro").attr('action','<?= base_url('ponto/ocorrencia/workflow2'); ?>').attr('target', '_blank');
    $("#form_filtro").submit();

}
const selecionaMotivo = (codmotivo, linha, data, chapa) => {

    var coluna = 7;
    <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'excesso_abono_gestor', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['extra_acima'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'extra_permitido', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['extra_especial'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'extra', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['interjornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'interjornada', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['intrajornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'intrajornada', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'jornada', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['registro_bri'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'registro_britanico', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['registro_manual'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'registro_manual', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['req_troca'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'pendente_termo_aditivo', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['sobreaviso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'sobreaviso', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'trabalho_dsr_folga', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'trabalho_dsr_folga_descanso', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'trabalho_ferias_afastamento', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'trabalho_6dias', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['troca_menor10'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'troca_menor_10_dias', '', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['troca_menor6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaJustificativa(codmotivo, data, chapa, 'troca_menor_6_meses', '', linha);} coluna++;<?php endif; ?>

    $("[data-motivo="+linha+"]").prop("disabled", true);
    $("[data-save="+linha+"]").remove();

}
const gravaDescricaoJustificativa = (descricao_motivo, data, chapa, ocorrencia, linha) => {
    if(descricao_motivo != ""){
        gravaJustificativa('O', data, chapa, ocorrencia, descricao_motivo, linha);
    }else{
        alert('Descrição da justificativa não informada.');
    }
    
}
const salvaMotivo = (linha, data, chapa) => {

    var dados_array = "";
    var dados = "";

    var coluna = 7;
    <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "excesso_abono_gestor;"; } coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['extra_acima'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "extra_permitido;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['extra_especial'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "extra;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['interjornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "interjornada;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['intrajornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "intrajornada;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "jornada;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['registro_bri'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "registro_britanico;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['registro_manual'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "registro_manual;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['req_troca'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "pendente_termo_aditivo;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['sobreaviso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "sobreaviso;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_dsr_folga;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_dsr_folga_descanso;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_ferias_afastamento;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_6dias;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['troca_menor10'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "troca_menor_10_dias;";} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['troca_menor6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "troca_menor_6_meses;";} coluna++;<?php endif; ?>

    let fd = new FormData();
    let anexo = $("[data-anexo="+linha+"]");
					 
    let codmotivo = $("[data-motivo="+linha+"]").val();
    let observacao = $("[data-observacao="+linha+"]").val();
								 
	 

    if(codmotivo == ""){exibeAlerta('error', 'Motivo não informado.'); return;}
    if(observacao == ""){exibeAlerta('error', 'Informação complementar não informado.'); return;}

    fd.append('chapa', chapa);
    fd.append('data', data);
    fd.append('codmotivo', codmotivo);
    fd.append('observacao', observacao);
    fd.append('ocorrencia', dados_array);
    if($("[data-anexo="+linha+"]") != '' && anexo.length > 0) fd.append('anexo', anexo[0].files[0]);

    if(anexo.length > 0){
        let name_file = (typeof anexo[0].files[0] === "undefined") ? null : anexo[0].files[0].name;
    }else{
        let name_file = null;
    }
    
    openLoading();

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/grava_justificativa'); ?>",
        type:'POST',
        processData: false,
        contentType: false,
        data: fd,
        success:function(result){

            var response = JSON.parse(result);

										   
            exibeAlerta(response.tipo, response.msg);
				  
            if(response.tipo == 'success'){
                if(name_file != null){
                    $("[data-print-anexo="+linha+"]").html('<i class="mdi mdi-cloud-download text-success"></i> - '+name_file);
                }
            }
            
        },
    });
    

}
const habilitaBotao = (linha) => {

    return false;

    var dados = {
        "motivo": $("[data-motivo="+linha+"]").val(),
        "observacao": $("[data-observacao="+linha+"]").val(),
    }

    if(dados.motivo == "" || dados.observacao == ""){
        $("[data-save="+linha+"]").prop('disabled', true).attr('class', 'btn btn-xxs btn-secondary');
    }else{
        $("[data-save="+linha+"]").prop('disabled', false).attr('class', 'btn btn-xxs btn-success');
    }

}
const gravaDescricaoObservacaoAll = (observacao, data, chapa, linha) => {

    var coluna = 7;
    <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  gravaDescricaoObservacao(observacao, data, chapa, 'excesso_abono_gestor', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['extra_acima'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  gravaDescricaoObservacao(observacao, data, chapa, 'extra_permitido', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['extra_especial'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  gravaDescricaoObservacao(observacao, data, chapa, 'extra', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['interjornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  gravaDescricaoObservacao(observacao, data, chapa, 'interjornada', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['intrajornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  gravaDescricaoObservacao(observacao, data, chapa, 'intrajornada', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'jornada', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['registro_bri'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'registro_britanico', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['registro_manual'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'registro_manual', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['req_troca'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'pendente_termo_aditivo', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['sobreaviso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'sobreaviso', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'trabalho_dsr_folga', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'trabalho_dsr_folga_descanso', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'trabalho_ferias_afastamento', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'trabalho_6dias', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['troca_menor10'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'troca_menor_10_dias', linha);} coluna++;<?php endif; ?>
    <?php if($TiposOcorrencia['troca_menor6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ gravaDescricaoObservacao(observacao, data, chapa, 'troca_menor_6_meses', linha);} coluna++;<?php endif; ?>

}
const gravaDescricaoObservacao = (observacao, data, chapa, ocorrencia, linha) => {

    let dados = {
        "chapa": chapa,
        "data": data,
        "ocorrencia": ocorrencia,
        "observacao": observacao,
    }

    openLoading();

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/grava_observacao'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);
            exibeAlerta(response.tipo, response.msg);
            
        },
    });

}

const aplicaOcorrenciaColetiva = () => {

    if($("[data-checkbox]:checked").length <= 0){
        exibeAlerta('error', 'Nenhum registro selecionado.');
        return false;
    }

    var dados_array = "";

    $('[data-checkbox]:checked').each(function(e){

        var linha = $(this).attr('data-checkbox');
        var dados = $(this).attr('data-registro');

        var coluna = 7;
        <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "excesso_abono_gestor;"; } coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['extra_acima'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "extra_permitido;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['extra_especial'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "extra;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['interjornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "interjornada;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['intrajornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "intrajornada;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "jornada;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['registro_bri'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "registro_britanico;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['registro_manual'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "registro_manual;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['req_troca'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "pendente_termo_aditivo;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['sobreaviso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "sobreaviso;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_dsr_folga;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_dsr_folga_descanso;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_ferias_afastamento;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_6dias;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['troca_menor10'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "troca_menor_10_dias;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['troca_menor6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "troca_menor_6_meses;";} coluna++;<?php endif; ?>
        
    });

    var dados = {
        "codmotivo": $("#all_motivo").val(),
        "outro_motivo": $("#all_outro_motivo").val(),
        "informacao_complementar": $("#all_informacao_complementar").val(),
        "dados": dados_array
    }

    openLoading();

    if(dados.codmotivo == ""){ exibeAlerta('error', 'Detalhamento da ocorrência não selecionada.'); return false; }
    if(dados.informacao_complementar == ""){ exibeAlerta('error', 'Informação complementar não informada.'); return false; }
    /*if(dados.codmotivo == "O" && dados.outro_motivo == ""){ exibeAlerta('error', 'Descrição da justificativa não informada.'); return false; }*/

    $('[data-checkbox]:checked').each(function(e){
        var linha = $(this).attr('data-checkbox');
        $("[data-save="+linha+"]").remove();
    });

    let fd = new FormData();

    let anexo = $("[name=all_anexo]");
    if(anexo != '' && anexo.length > 0) fd.append('anexo', anexo[0].files[0]);

    
    fd.append('codmotivo', dados.codmotivo);
    fd.append('outro_motivo', dados.outro_motivo);
    fd.append('informacao_complementar', dados.informacao_complementar);
    fd.append('dados', dados.dados);
    fd.append('anexo', anexo);

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/grava_justificativa_coletiva'); ?>",
        type:'POST',
        processData: false,
        contentType: false,
        data: fd,
        success:function(result){
            
            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 3);
                setTimeout( () => buscaOcorrencia(), 3000);
            }
            
        },
    });

}
const aplicaInformacaoComplementarColetiva = () => {

    if($("[data-checkbox]:checked").length <= 0){
        exibeAlerta('error', 'Nenhum registro selecionado.');
        return false;
    }

    var dados_array = "";

    $('[data-checkbox]:checked').each(function(e){
        
        var linha = $(this).attr('data-checkbox');
        var dados = $(this).attr('data-registro');

        var coluna = 7;
        <?php if($TiposOcorrencia['excesso_gestor'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "excesso_abono_gestor;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['extra_acima'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "extra_permitido;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['extra_especial'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "extra;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['interjornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "interjornada;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['intrajornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){  dados_array += dados + "intrajornada;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['excesso_jornada'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "jornada;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['registro_bri'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "registro_britanico;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['registro_manual'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "registro_manual;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['req_troca'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "pendente_termo_aditivo;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['sobreaviso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "sobreaviso;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_dsr'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_dsr_folga;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_dsr_folga_descanso;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_ferias_afastamento;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['trabalho_sup6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "trabalho_6dias;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['troca_menor10'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "troca_menor_10_dias;";} coluna++;<?php endif; ?>
        <?php if($TiposOcorrencia['troca_menor6'] == 1): ?>if($("[data-linha="+linha+"]").find('td')[coluna].innerText != ''){ dados_array += dados + "troca_menor_6_meses;";} coluna++;<?php endif; ?>

    });

    var dados = {
        "informacao_complementar": $("#all_informacao_complementar").val(),
        "dados": dados_array
    }

    openLoading();

    if(dados.informacao_complementar == ""){ exibeAlerta('error', 'Informação Complementar não informada.'); return false; }

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/grava_informacao_complementar_coletiva'); ?>",
        type: 'POST',
        data: dados,
        success:function(result){
            
            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 3);
                setTimeout( () => buscaOcorrencia(), 3000);
            }
            
        },
    });

}
const gravaJustificativa = (codmotivo, data, chapa, ocorrencia, descricao_motivo = '', linha) => {

    let dados = {
        "chapa": chapa,
        "data": data,
        "codmotivo": codmotivo,
        "ocorrencia": ocorrencia,
        "descricao_motivo": descricao_motivo,
    }

    openLoading();

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/grava_justificativa'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                /*$('[data-linha="'+linha+'"] td select, [data-linha="'+linha+'"] td textarea').prop('disabled', true);*/
                exibeAlerta(response.tipo, response.msg);
            }
            
        },
    });

}
$(() => {
    $("#periodo").on('change', function(e){
        var periodo = $(this).val();
        $("#data_inicio").val(periodo.substr(0,10));
        $("#data_fim").val(periodo.substr(10));
    });
    /*abre card para preenchimento da justificativa coletivo*/
    $(".botao_coletivo").on('click', function(){
        $("#botao_coletivo").toggle(200);
        $(".card_coletiva").toggle(200);
        $(".checkboxall").toggle(100);
    });
    /* oculta botão para informação complementar coletivo*/
    $("#botao_coletivo").on('click', function(){
        $(".botao_informacao_coletivo").fadeOut(0);
    });
    $(".btn-xxs.botao_coletivo[aria-label='Close']").on('click', function(){
        $(".botao_informacao_coletivo").fadeIn(0);
    });
    /*abre card para preenchimento da informaçaõ complementar coletiva*/
    $(".botao_informacao_coletivo").on('click', function(){
        $("#botao_informacao_coletivo").toggle(200);
        $(".card_informacao_coletiva").toggle(200);
        $(".checkboxall").toggle(100);
    });
    /* oculta botão de justificativa coletivo*/
    $("#botao_informacao_coletivo").on('click', function(){
        $(".botao_coletivo").fadeOut(0);
    });
    $(".btn-xxs.botao_informacao_coletivo[aria-label='Close']").on('click', function(){
        $(".botao_coletivo").fadeIn(0);
    });
    /* seleciona todos os checkbox*/
    $("[data-checkall]").on('click', function(){
        if($(this).prop('checked') === true){
            $("[data-registro]").prop('checked', true);
        }else{
            $("[data-registro]").prop('checked', false);
        }
    });
});

<?php
$total_tratado = 0;
$total_nao_tratado = 0;
/*
if($qtde_ocorrencia && is_array($qtde_ocorrencia)){
    foreach($qtde_ocorrencia as $key => $QtdeOcorrencia){
        if(isset($QtdeOcorrencia['ocorrencia'])){
            echo "$('[data-{$QtdeOcorrencia['ocorrencia']}_nao_tratado]').html('{$QtdeOcorrencia['qtde']}');";
            $total_nao_tratado += ($QtdeOcorrencia['qtde']);
        }
    }
    echo "$('[data-total]').html('<label class=\"col-form-label text-left pt-0 pb-0\">{$total_nao_tratado}</label>');";
}*/

$qtde_ocorrencia = false;
$qtde_excesso_abono_gestor = 0;
$qtde_extra_permitido = 0;
$qtde_extra = 0;
$qtde_interjornada = 0;
$qtde_intrajornada = 0;
$qtde_jornada = 0;
$qtde_registro_britanico = 0;
$qtde_registro_manual = 0;
$qtde_pendente_termo_aditivo = 0;
$qtde_sobreaviso = 0;
$qtde_trabalho_dsr_folga = 0;
$qtde_trabalho_dsr_folga_descanso = 0;
$qtde_trabalho_ferias_afastamento = 0;
$qtde_trabalho_6dias = 0;
$qtde_troca_menor_10_dias = 0;
$qtde_troca_menor_6_meses = 0;

$tratado_qtde_excesso_abono_gestor = 0;
$tratado_qtde_extra_permitido = 0;
$tratado_qtde_extra = 0;
$tratado_qtde_interjornada = 0;
$tratado_qtde_intrajornada = 0;
$tratado_qtde_jornada = 0;
$tratado_qtde_registro_britanico = 0;
$tratado_qtde_registro_manual = 0;
$tratado_qtde_pendente_termo_aditivo = 0;
$tratado_qtde_sobreaviso = 0;
$tratado_qtde_trabalho_dsr_folga = 0;
$tratado_qtde_trabalho_dsr_folga_descanso = 0;
$tratado_qtde_trabalho_ferias_afastamento = 0;
$tratado_qtde_trabalho_6dias = 0;
$tratado_qtde_troca_menor_10_dias = 0;
$tratado_qtde_troca_menor_6_meses = 0;

if(isset($resOcorrenciaLog)){
    foreach($resOcorrenciaLog as $key => $Total){
        if(strlen(trim($Total['codmotivo'])) <= 0){
            switch($Total['OCORRENCIA']){
                case 'excesso_abono_gestor': $qtde_excesso_abono_gestor += (1); break;
                case 'extra_permitido': $qtde_extra_permitido += (1); break;
                case 'extra': $qtde_extra += (1); break;
                case 'interjornada': $qtde_interjornada += (1); break;
                case 'intrajornada': $qtde_intrajornada += (1); break;
                case 'jornada': $qtde_jornada += (1); break;
                case 'registro_britanico': $qtde_registro_britanico += (1); break;
                case 'registro_manual': $qtde_registro_manual += (1); break;
                case 'pendente_termo_aditivo': $qtde_pendente_termo_aditivo += (1); break;
                case 'sobreaviso': $qtde_sobreaviso += (1); break;
                case 'trabalho_dsr_folga': $qtde_trabalho_dsr_folga += (1); break;
                case 'trabalho_dsr_folga_descanso': $qtde_trabalho_dsr_folga_descanso += (1); break;
                case 'trabalho_ferias_afastamento': $qtde_trabalho_ferias_afastamento += (1); break;
                case 'trabalho_6dias': $qtde_trabalho_6dias += (1); break;
                case 'troca_menor_10_dias': $qtde_troca_menor_10_dias += (1); break;
                case 'troca_menor_6_meses': $qtde_troca_menor_6_meses += (1); break;
            }
        }
        unset($resOcorrenciaLog[$key]);
    }
}
if(isset($resOcorrenciaLogTratado)){
    foreach($resOcorrenciaLogTratado as $key => $Total){
        if(strlen(trim($Total['codmotivo'])) > 0){
            switch($Total['OCORRENCIA']){
                case 'excesso_abono_gestor': $tratado_qtde_excesso_abono_gestor += (1); break;
                case 'extra_permitido': $tratado_qtde_extra_permitido += (1); break;
                case 'extra': $tratado_qtde_extra += (1); break;
                case 'interjornada': $tratado_qtde_interjornada += (1); break;
                case 'intrajornada': $tratado_qtde_intrajornada += (1); break;
                case 'jornada': $tratado_qtde_jornada += (1); break;
                case 'registro_britanico': $tratado_qtde_registro_britanico += (1); break;
                case 'registro_manual': $tratado_qtde_registro_manual += (1); break;
                case 'pendente_termo_aditivo': $tratado_qtde_pendente_termo_aditivo += (1); break;
                case 'sobreaviso': $tratado_qtde_sobreaviso += (1); break;
                case 'trabalho_dsr_folga': $tratado_qtde_trabalho_dsr_folga += (1); break;
                case 'trabalho_dsr_folga_descanso': $tratado_qtde_trabalho_dsr_folga_descanso += (1); break;
                case 'trabalho_ferias_afastamento': $tratado_qtde_trabalho_ferias_afastamento += (1); break;
                case 'trabalho_6dias': $tratado_qtde_trabalho_6dias += (1); break;
                case 'troca_menor_10_dias': $tratado_qtde_troca_menor_10_dias += (1); break;
                case 'troca_menor_6_meses': $tratado_qtde_troca_menor_6_meses += (1); break;
            }
        }
        unset($resOcorrenciaLogTratado[$key]);
    }
}
?>

<?php if($TiposOcorrencia['excesso_gestor'] == 1): $total_nao_tratado += ($qtde_excesso_abono_gestor); ?>$('[data-excesso_abono_gestor_nao_tratado]').html(<?= $qtde_excesso_abono_gestor; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['extra_acima'] == 1): $total_nao_tratado += ($qtde_extra_permitido); ?>$('[data-extra_permitido_nao_tratado]').html(<?= $qtde_extra_permitido; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['extra_especial'] == 1): $total_nao_tratado += ($qtde_extra); ?>$('[data-extra_nao_tratado]').html(<?= $qtde_extra; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['interjornada'] == 1): $total_nao_tratado += ($qtde_interjornada); ?>$('[data-interjornada_nao_tratado]').html(<?= $qtde_interjornada; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['intrajornada'] == 1): $total_nao_tratado += ($qtde_intrajornada); ?>$('[data-intrajornada_nao_tratado]').html(<?= $qtde_intrajornada; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['excesso_jornada'] == 1): $total_nao_tratado += ($qtde_jornada); ?>$('[data-jornada_nao_tratado]').html(<?= $qtde_jornada; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['registro_bri'] == 1): $total_nao_tratado += ($qtde_registro_britanico); ?>$('[data-registro_britanico_nao_tratado]').html(<?= $qtde_registro_britanico; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['registro_manual'] == 1): $total_nao_tratado += ($qtde_registro_manual); ?>$('[data-registro_manual_nao_tratado]').html(<?= $qtde_registro_manual; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['req_troca'] == 1): $total_nao_tratado += ($qtde_pendente_termo_aditivo); ?>$('[data-pendente_termo_aditivo_nao_tratado]').html(<?= $qtde_pendente_termo_aditivo; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['sobreaviso'] == 1): $total_nao_tratado += ($qtde_sobreaviso); ?>$('[data-sobreaviso_nao_tratado]').html(<?= $qtde_sobreaviso; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_dsr'] == 1): $total_nao_tratado += ($qtde_trabalho_dsr_folga); ?>$('[data-trabalho_dsr_folga_nao_tratado]').html(<?= $qtde_trabalho_dsr_folga; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): $total_nao_tratado += ($qtde_trabalho_dsr_folga_descanso); ?>$('[data-trabalho_dsr_folga_descanso_nao_tratado]').html(<?= $qtde_trabalho_dsr_folga_descanso; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): $total_nao_tratado += ($qtde_trabalho_ferias_afastamento); ?>$('[data-trabalho_ferias_afastamento_nao_tratado]').html(<?= $qtde_trabalho_ferias_afastamento; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_sup6'] == 1): $total_nao_tratado += ($qtde_trabalho_6dias); ?>$('[data-trabalho_6dias_nao_tratado]').html(<?= $qtde_trabalho_6dias; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['troca_menor10'] == 1): $total_nao_tratado += ($qtde_troca_menor_10_dias); ?>$('[data-troca_menor_10_dias_nao_tratado]').html(<?= $qtde_troca_menor_10_dias; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['troca_menor6'] == 1): $total_nao_tratado += ($qtde_troca_menor_6_meses); ?>$('[data-troca_menor_6_meses_nao_tratado]').html(<?= $qtde_troca_menor_6_meses; ?>);<?php endif; ?>
$('[data-total]').html('<label class="col-form-label text-left pt-0 pb-0"><?= $total_nao_tratado; ?></label>');

<?php if($TiposOcorrencia['excesso_gestor'] == 1): $total_tratado += ($tratado_qtde_excesso_abono_gestor); ?>$('[data-excesso_abono_gestor_tratado]').html(<?= $tratado_qtde_excesso_abono_gestor; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['extra_acima'] == 1): $total_tratado += ($tratado_qtde_extra_permitido); ?>$('[data-extra_permitido_tratado]').html(<?= $tratado_qtde_extra_permitido; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['extra_especial'] == 1): $total_tratado += ($tratado_qtde_extra); ?>$('[data-extra_tratado]').html(<?= $tratado_qtde_extra; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['interjornada'] == 1): $total_tratado += ($tratado_qtde_interjornada); ?>$('[data-interjornada_tratado]').html(<?= $tratado_qtde_interjornada; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['intrajornada'] == 1): $total_tratado += ($tratado_qtde_intrajornada); ?>$('[data-intrajornada_tratado]').html(<?= $tratado_qtde_intrajornada; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['excesso_jornada'] == 1): $total_tratado += ($tratado_qtde_jornada); ?>$('[data-jornada_tratado]').html(<?= $tratado_qtde_jornada; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['registro_bri'] == 1): $total_tratado += ($tratado_qtde_registro_britanico); ?>$('[data-registro_britanico_tratado]').html(<?= $tratado_qtde_registro_britanico; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['registro_manual'] == 1): $total_tratado += ($tratado_qtde_registro_manual); ?>$('[data-registro_manual_tratado]').html(<?= $tratado_qtde_registro_manual; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['req_troca'] == 1): $total_tratado += ($tratado_qtde_pendente_termo_aditivo); ?>$('[data-pendente_termo_aditivo_tratado]').html(<?= $tratado_qtde_pendente_termo_aditivo; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['sobreaviso'] == 1): $total_tratado += ($tratado_qtde_sobreaviso); ?>$('[data-sobreaviso_tratado]').html(<?= $tratado_qtde_sobreaviso; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_dsr'] == 1): $total_tratado += ($tratado_qtde_trabalho_dsr_folga); ?>$('[data-trabalho_dsr_folga_tratado]').html(<?= $tratado_qtde_trabalho_dsr_folga; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_dsr_descanso'] == 1): $total_tratado += ($tratado_qtde_trabalho_dsr_folga_descanso); ?>$('[data-trabalho_dsr_folga_descanso_tratado]').html(<?= $tratado_qtde_trabalho_dsr_folga_descanso; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_AfastFerias'] == 1): $total_tratado += ($tratado_qtde_trabalho_ferias_afastamento); ?>$('[data-trabalho_ferias_afastamento_tratado]').html(<?= $tratado_qtde_trabalho_ferias_afastamento; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['trabalho_sup6'] == 1): $total_tratado += ($tratado_qtde_trabalho_6dias); ?>$('[data-trabalho_6dias_tratado]').html(<?= $tratado_qtde_trabalho_6dias; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['troca_menor10'] == 1): $total_tratado += ($tratado_qtde_troca_menor_10_dias); ?>$('[data-troca_menor_10_dias_tratado]').html(<?= $tratado_qtde_troca_menor_10_dias; ?>);<?php endif; ?>
<?php if($TiposOcorrencia['troca_menor6'] == 1): $total_tratado += ($tratado_qtde_troca_menor_6_meses); ?>$('[data-troca_menor_6_meses_tratado]').html(<?= $tratado_qtde_troca_menor_6_meses; ?>);<?php endif; ?>
$('[data-total-tratado]').html('<label class="col-form-label text-left pt-0 pb-0"><?= $total_tratado; ?></label>');
</script>
<!--
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.js"></script>
-->
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>"/>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>
<script>
$(function () {
    var drEvent = $('.dropify').dropify({
        messages: {
            default: 'Clique para anexar (JPG, PNG ou PDF)',
            replace: 'Clique para anexar (JPG, PNG ou PDF)',
            remove:  'Remover',
            error:   'Desculpe, o arquivo é muito grande ou não esta no formato JPG, PNG ou PDF',
            
        },
        error: {
            fileExtension: "Arquivo inválido (permitido {{ value }})."
        }
    });
});
</script>
<?php
loadPlugin(array('select2', 'datatable', 'dropify'));
?>
<style> 
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0 !important;
    border: none !important;
    background: none !important;
}
.dtfh-floatingparent {
    height: 205px !important;
}
.dtfh-floatingparent .sorting {
    padding-top: 75px !important;
}
body {
    overflow-x: auto !important;
}
.select2-results {
    width: max-content !important;
    background-color: #ffffff;
    border: 1px solid #cccccc;
    
}
.dropify-wrapper {
    height: 53px !important;
    width: 132px;
    line-height: 0;
}
.dropify-wrapper .dropify-message span.file-icon {
    font-size: 28px;
}
.dropify-wrapper .dropify-message p {
    margin: 5px 0 0;
    margin: 0;
    padding: 0;
    font-size: 10px;
    line-height: 1;
}
.dropify-wrapper .dropify-errors-container ul li {
    font-size: 10px;
    list-style: none;
    margin: 0;
    padding: 0;
    line-height: 1;
}
</style>
<script>
    $(document).ready(function() {
        $('#datatableOcorrencia').DataTable({
            "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
            "iDisplayLength"    : 25,
            "aaSorting"         : [[0, "desc"]],
            "fixedHeader": true,
            initComplete: function () {
                var p_linha = this.api().columns()[0].length;
                this.api()
                    .columns()
                    .every(function () {
                        var column = this;
                        
                        if(column[0][0] == 0 || column[0][0] >= (p_linha-4)) return false;
                        var select = $('<select class="form-control form-control-sm filtro_table"><option value=""></option></select>')
                            .appendTo($(column.header()))
                            .on('change', function () {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false).draw();
                            });
    
                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function (d, j) {
                                if(j == 0) select.append('<option value="">- Todos -</option>');
                                //Data tem formato dinamico, necessitando assim corrigí-lo para que o filtro n quebre
                                
                                //data no formato dd/mm/yyyy
                                let array = d.split(">");

                                //dia da semana
                                let arrayT;

                                //array[4] é o dia da semana
                                if(array[4]) arrayT = array[4].split("<");
                                
                                if(array[1]) array = array[1].split("<");
                                if(arrayT) array[0] = array[0].concat(arrayT[0]);
                                
                                let dataPronta = array[0];

                                if(d != "") select.append('<option value="' + dataPronta + '">' + dataPronta + '</option>');
                            });
                            
                    });

                    $(".filtro_table").select2({
                        width: '100%',
                        language: {
                            noResults: function(){
                                return 'Nenhum resultado encontrado';
                            }
                        }
                    });
            },
        });
    });
    const abreBatidasDia = (chapa, data, nome, horario) => {
        $(".modal_batidas").modal('show');
        $("#exibe_batidas, [data-ponto]").html('');
       

        let dados = {
            "data"  : data,
            "chapa" : chapa,
            "horario" : horario
        };

        openLoading();

        

        $.ajax({
            url: "<?= base_url('ponto/ocorrencia/action/lista_batidas') ?>",
            type:'POST',
            data:dados,
            success:function(result){

                try {
                    console.log(result);
                    openLoading(true);

                    var response = JSON.parse(result);

                    

                    var aviso = (response.AVISO != '') ? response.AVISO : response.BATIDA_8;
                    
                    if(response){
                        $("[data-ponto]").html(`
                            <tr>
                                <td bgcolor="#ffffff" class="text-center">${response.DATA}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.SEMANA}</td>
                                <td bgcolor="${response.BATIDA_1_PORTAL}" class="text-center">${response.BATIDA_1}</td>
                                <td bgcolor="${response.BATIDA_2_PORTAL}" class="text-center">${response.BATIDA_2}</td>
                                <td bgcolor="${response.BATIDA_3_PORTAL}" class="text-center">${response.BATIDA_3}</td>
                                <td bgcolor="${response.BATIDA_4_PORTAL}" class="text-center">${response.BATIDA_4}</td>
                                <td bgcolor="${response.BATIDA_5_PORTAL}" class="text-center">${response.BATIDA_5}</td>
                                <td bgcolor="${response.BATIDA_6_PORTAL}" class="text-center">${response.BATIDA_6}</td>
                                <td bgcolor="${response.BATIDA_7_PORTAL}" class="text-center">${response.BATIDA_7}</td>
                                <td bgcolor="${response.BATIDA_8_PORTAL}" class="text-center">${aviso}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.HTRAB}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.EXTRA_1AFAIXA}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.EXTRA_2AFAIXA}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.EXTRA_100}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.EXTRAAUTORIZADO}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.ATRASO}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.FALTA}</td>
                                <td bgcolor="#ffffff" class="text-center">${response.ABONO}</td>
                            </tr>
                        `);

                        $("[data-colaborador]").html(chapa + ' - ' +nome);
                        $("[data-colaborador2]").html('Escala: ' + `${response.HORARIOESCALA}` + ' - ' + `${response.DESCHORARIOESCALA}` + '<br> Índice: ' + `${response.INDICE}`);
                    }

                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }
                
            },
        });


        
    }
</script>
<!-- modal modal_batidas -->
<div class="modal modal_batidas" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_batidas" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-clock-outline"></i> Registros do Dia</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <h4 style="color: #000000" data-colaborador class="mb-0"></h4>
                        <p data-colaborador2 class="mt-0" style="font-size: 13px;"></p>
                        <hr>
                        <p class="m-0">
                            <b>Legenda:</b> 
                            <i title="Aguardando Aprovação Gestor" class="mdi mdi-square ml-3" style="font-size:20px; color: #ace5ff;"></i> Pendente Aprovação
                            <i title="Aprovado Gestor" class="mdi mdi-square ml-3" style="font-size:20px; color: #acffcd;"></i> Pendente Aprovação RH
                            <i title="Ação Reprovada" class="mdi mdi-square ml-3" style="font-size:20px; color: #fe0200;"></i> Ação Reprovada
                        </p>
                        <table class="table table-bordered mb-0 table-centered table-sm ">
                            <thead>
                                <tr class="bg-primary">
                                    <th class="text-white">Data</th>
                                    <th class="text-white">Dia. Sem.</th>
                                    <th class="text-white bg-info">Ent</th>
                                    <th class="text-white bg-info">Sai</th>
                                    <th class="text-white bg-info">Ent</th>
                                    <th class="text-white bg-info">Sai</th>
                                    <th class="text-white bg-info">Ent</th>
                                    <th class="text-white bg-info">Sai</th>
                                    <th class="text-white bg-info">Ent</th>
                                    <th class="text-white bg-info">Sai</th>
                                    <th class="text-white">Hr. Trab.</th>
                                    <th class="text-white">Extra 1° faixa</th>
                                    <th class="text-white">Extra 2° faixa</th>
                                    <th class="text-white">Extra 100%</th>
                                    <th class="text-white">Total Extra</th>
                                    <th class="text-white">Atraso</th>
                                    <th class="text-white">Falta</th>
                                    <th class="text-white">Abono</th>
                                </tr>
                            </thead>
                            <tbody data-ponto>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
							
<!-- modal modal_batidas -->
<?php gc_collect_cycles(); ?>