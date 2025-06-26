<?php
$is_motorista = $isMotorista;
?>
<?php if($impressao): ?>
<style>
body {
    font-size: 10px !important;
    font-family:Georgia, 'Times New Roman', Times, serif;
}
table {
    border-collapse: collapse;
    border-spacing: 0px;
    margin-top: 20px;
}
table td, table th {
    border:1px dotted #000000;
}
table td, table tr {
    background-color: #ffffff !important;
}
table th {
    background-color: #f0f0f0;
}
.h4 {
    margin-bottom: -20px;
}
.no-border table td{
    border: none !important;
    padding: 10px;
}
.text-center {text-align: center !important;}
.txtpdf {color: #ffffff !important;}
.badge {font-size: 7px !important;}
/* colors eldorado text */
.txteldorado_1 {
    color: #006b44 !important;
}
.bgeldorado_2 {
    color: #f5943f !important;
}
.bgeldorado_3 {
    color: #9de3ad !important;
}
.txteldorado_4 {
    color: #c8c02d !important;
}
.txteldorado_5 {
    color: #feca07 !important;
}
.txteldorado_6 {
    color: #cb7b3a !important;
}
.txteldorado_8 {
    color: #dbdcdd !important;
}
.txteldorado_9 {
    color: #f3dab0 !important;
}
</style>
<div class="no-border">
		
																															
																																					   
		 
		
<table width="100%">
    <tr>
        <td width="100" valign="top"><img width="100" src="<?= $_SERVER['DOCUMENT_ROOT'].'/public/assets/images/logo-dark.png'; ?>"></td>
        <td align="left">
            Cartão de Ponto<br>
            <?= $resFuncionario[0]['NOMECOLIGADA']; ?><br>
            Emissão: <?= meses(date('m')) ?>/<?= date('Y'); ?><br>
            <br>
            <?= $resFuncionario[0]['RUA']; ?>, <?= $resFuncionario[0]['NUMERO']; ?><br>
            CNPJ: <?= $resFuncionario[0]['CNPJ']; ?>
        </td>
        <td align="right">
            <div style="text-align: left;">
                <strong>Seção: </strong> <?php echo $resFuncionario[0]['CODSECAO'] . ' - ' . $resFuncionario[0]['NOMESECAO'] ?>
            </div>
            <br>
            <div style="text-align: rigth;">
                <b>Data:</b> <?= date('d/m/Y'); ?><br>
                <span><b>Período:</b> <?= substr($periodo, 0, 10); ?> à <?= substr($periodo, -10); ?></span>
            </div>
        </td>
    </tr>
    <tr>
        <td align="left" valign="top" colspan="3">
            <hr>
            <table width="100%" style="margin:0;">
                <tr>
                    <td style="padding: 0px;" width="150">Chapa<br><?= $resFuncionario[0]['CHAPA']; ?></td>
                    <td style="padding: 0px;">Nome do Colaborador<br><?= $resFuncionario[0]['NOME']; ?></td>
                    <td style="padding: 0px;" width="150">CPF<br><?= $resFuncionario[0]['CPF']; ?></td>
                    <td style="padding: 0px;" width="150" align="center">Data de Admissão<br><?= dtBr($resFuncionario[0]['DATAADMISSAO']); ?></td>
                </tr>
            </table>
            <br>
            <table width="100%" style="margin:0;">
                <tr>
                    <td style="padding: 0px;" width="150">Carteira de Trabalho<br><?= $resFuncionario[0]['CARTEIRATRAB'].' - '.$resFuncionario[0]['SERIECARTTRAB']; ?></td>
                    <td style="padding: 0px;">Função<br><?= $resFuncionario[0]['NOMEFUNCAO']; ?></td>
                    <td style="padding: 0px;" width="150">Cargo<br><?= $resFuncionario[0]['CARGO']; ?></td>
                    <td style="padding: 0px;" width="150" align="center"></td>
                </tr>
            </table><br>
            <table width="100%" style="margin:0;">
                <tr>
                    <td style="padding: 0px;">Horário:<br><?= $resFuncionario[0]['CODHORARIO'].' - '.$resFuncionario[0]['NOMEHORARIO']; ?></td>
                </tr>
            </table>
            <hr>
        </td>
    </tr>
</table>  
</div>
<?php endif; ?>
<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <?php if(!$impressao): ?>
            <div class="card noprint">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Selecione o período</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="" method="post" name="form_espelho" id="form_espelho">

                        <div class="row">
                            <label for="periodo" class="col-sm-2 col-form-label text-right text-left-sm">Período:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo" <?php if($isGestorOrLider): ?>onchange="carregaColaboradores()"<?php endif; ?>>
                                    <option value="">- selecione um período -</option>
                                    <?php if ($resPeriodo) : ?>
                                        <?php foreach($resPeriodo as $key => $Periodo): ?>
                                        <option value="<?= dtBr($Periodo['INICIOMENSAL']) . dtBr($Periodo['FIMMENSAL']). $Periodo['STATUSPERIODO']; ?>" <?= ($periodo == dtBr($Periodo['INICIOMENSAL']) . dtBr($Periodo['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($Periodo['INICIOMENSAL']) . ' à ' . dtBr($Periodo['FIMMENSAL']); ?></option>                                        
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <?php if ($isGestorOrLider) : ?>
                            <div class="row">
                                <label for="secao" class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
                                <div class="col-sm-10">
                                    <select class="select2 custom-select form-control form-control-sm" name="secao" id="secao" onchange="carregaColaboradores()">
                                        <option value="">-- Todas --</option>
                                        <?php if($resSecaoGestor): ?>
                                            <?php foreach($resSecaoGestor as $key => $SecaoGestor): ?>
                                                <option value="<?= $SecaoGestor['CODIGO']; ?>" <?= ($secao == $SecaoGestor['CODIGO']) ? ' selected ' : '' ?>><?= $SecaoGestor['CODIGO'].' - '.$SecaoGestor['DESCRICAO']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Colaborador:</label>
                                <div class="col-sm-10">
                                    <select class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">- selecione o colaborador -</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($chapa == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] . ' - ' . $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                    </form>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary btn-xxs bteldorado_7" id="btnsave" onclick="return consultarEspelho()"><i class="fas fa-search"></i> Consultar</button>
                </div>
            </div>
            <?php endif; ?>
            <div class="card">

                <?php if(!$impressao): ?>
                <div class="card-header mt-0 noprint">
                    <div class="row">
                        <h4 class="col-7 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <?php if($perfilRecalculo && $resDiasEspelho && $chapa): ?>
                        <div class="col-5 text-right">
                            <div class="button-items">
                                <button type="button" class="btn btn-info btn-xxs mb-0" onclick="return recalcularPonto();"><i class="ti-reload"></i> Recalcular Ponto</button>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <?php if ($resDiasEspelho && $chapa) : ?>

                        <?php
                        // $periodo_bloqueado = (dtEn($EspelhoConfiguracao[0]['limite_funcionario'], true) < date('Y-m-d')) ? true : false;
                        $periodo_bloqueado = false;
                        ?>

                        <?php if(!$impressao): ?>
                        <ul class="list-group" style="font-size: 12px !important;">
                            <li class="list-group-item border-0 m-0 mb-1 p-0"><strong>Função: </strong><?php echo $resFuncionario[0]['NOMEFUNCAO'] ?></li>
                            <li class="list-group-item border-0 m-0 mb-1 p-0"><strong>Seção: </strong><?php echo $resFuncionario[0]['CODSECAO'] . ' - ' . $resFuncionario[0]['NOMESECAO'] ?></li>
                            <li class="list-group-item border-0 m-0 p-0"><strong>Horário: </strong><?php echo $resFuncionario[0]['CODHORARIO'].' - '.$resFuncionario[0]['NOMEHORARIO'] ?></li>
                            <li class="list-group-item border-0 m-0 p-0"><strong>Situação: </strong><?php echo (strlen(trim($resFuncionario[0]['DATADEMISSAO']) <= 0 || $resFuncionario[0]['DATADEMISSAO'] >= $dataFim)) ? 'Ativo' : 'Demitido'; ?></li>
                        </ul>
                        <?php endif; ?>

                        <?php if(!$impressao): ?>
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header">
                                    <h4 class="mb-1 mt-1">Banco de Horas</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-around text-center">
                                        <div>
                                            <p class="<?= ($HorasPositivas > 0) ? 'txteldorado_1' : 'bgeldorado_2'; ?> mb-0" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_anterior">  </p>
                                            <p class="<?= ($HorasPositivas > 0) ? 'txteldorado_1' : 'bgeldorado_2'; ?> mb-0">Saldo Anterior</p>
                                        </div>
                                        <div>
                                            <p class="txteldorado_1 mb-0" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_mes_positivo"> </p>
                                            <p class="txteldorado_1 mb-0">Saldo Mês Positivo</p>
                                        </div>
                                        <div>
                                            <p class="bgeldorado_2 mb-0" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_mes_negativo"> </p>
                                            <p class="bgeldorado_2 mb-0">Saldo Mês Negativo</p>
                                        </div>
                                        <div>
                                            <p class="<?= m2h($SaldoTotal) > 0 ? 'txteldorado_1 mb-0' : 'bgeldorado_2 mb-0' ?>" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_total"> </p>
                                            <p class="<?= m2h($SaldoTotal) > 0 ? 'txteldorado_1 mb-0' : 'bgeldorado_2 mb-0' ?>">Saldo total</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="m-0">
                            <b>Legenda:</b> 
                            <i title="Necessária Ação" class="mdi mdi-square ml-3 txteldorado_9" style="font-size:20px; color: #f3dab0;"></i> Necessária Ação
                            <i title="Aguardando Ação Gestor" class="mdi mdi-square ml-3 txteldorado_5" style="font-size:20px; color: #feca07;"></i> Pend/Ação Gestor
                            <i title="Aprovado Gestor" class="mdi mdi-square ml-3 txteldorado_8" style="font-size:20px; color: #dbdcdd;"></i> Pend/Ação RH
                            <i title="Ação Reprovada" class="mdi mdi-square ml-3 bgeldorado_2" style="font-size:20px; color: #f5943f;"></i> Ação Reprovada
                            <i title="Ação Aprovada" class="mdi mdi-square ml-3 bgeldorado_3" style="font-size:20px; color: #9de3ad;"></i> Ação Aprovada
                            <i title="Justificar extra" class="mdi mdi-square ml-3 txteldorado_4" style="font-size:20px; color: #c8c02d;"></i> Justificar extra
                            <i title="Registro Coletado" class="mdi mdi-alpha-c-box-outline ml-3" style="font-size:20px; color:rgb(43, 41, 41);"></i> Coletado
                            <i title="Registro Digitado" class="mdi mdi-alpha-d-box-outline ml-3" style="font-size:20px; color:rgb(43, 41, 41);"></i> Digitado
                        </p>
                        <?php endif; ?>
                        <div class="table-responsive">
                            <table id="tabela_ponto" class="table table-bordered mb-0 table-centered table-sm ">
                                <thead style="position: sticky; z-index: 1;">
                                    <tr>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '60' : '100'; ?>">Data</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '40' : '90'; ?>">Dia. Sem.</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Ent</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Sai</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Ent</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Sai</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Ent</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Sai</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Ent</th>
                                        <th class="text-center n-mobile-cell" width="<?= ($impressao) ? '50' : ''; ?>">Sai</th>
                                        <th class="text-center n-mobile-cell">Hr. Trab.</th>
                                        <th class="text-center n-mobile-cell">Extra 1° faixa</th>
                                        <th class="text-center n-mobile-cell">Extra 2° faixa</th>
                                        <th class="text-center n-mobile-cell">Extra 100%</th>
                                        <th class="text-center n-mobile-cell">Total Extra</th>
                                        <th class="text-center n-mobile-cell">Adicional Noturno</th>
                                        <th class="text-center n-mobile-cell">Atraso</th>
                                        <th class="text-center n-mobile-cell">Falta</th>

                                        <?php if($is_motorista): ?>
                                        <th class="text-center n-mobile-cell">Total Hrs Refeição</th>
                                        <th class="text-center n-mobile-cell">Total Hrs Direção</th>
                                        <th class="text-center n-mobile-cell">Total Hrs Espera</th>
										<th class="text-center n-mobile-cell">Total Hrs Parado</th>
																									
                                        <?php endif; ?>

                                        <?php if(!$impressao): ?>
                                        <th class="text-center n-mobile-cell">Abono</th>
                                        <th class="text-center d-none y-mobile-cell">Data</th>
                                        <th class="text-center d-none y-mobile-cell">Entrada</th>
                                        <th class="text-center d-none y-mobile-cell">Saida</th>
                                        <th class="text-center y-mobile-cell" width="20">Ação</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resDiasEspelho as $key => $DiasEspelho) : ?>
                                        <?php

                                        $indice_ent = 1;
                                        $indice_sai = 1;

                                        $ent[1] = '';
                                        $sai[1] = '';
                                        $ent[2] = '';
                                        $sai[2] = '';
                                        $ent[3] = '';
                                        $sai[3] = '';
                                        $ent[4] = '';
                                        $sai[4] = '';

                                        $ent_portal[1] = '';
                                        $sai_portal[1] = '';
                                        $ent_portal[2] = '';
                                        $sai_portal[2] = '';
                                        $ent_portal[3] = '';
                                        $sai_portal[3] = '';
                                        $ent_portal[4] = '';
                                        $sai_portal[4] = '';

                                        $ent_motivo_reprova[1] = '';
                                        $sai_motivo_reprova[1] = '';
                                        $ent_motivo_reprova[2] = '';
                                        $sai_motivo_reprova[2] = '';
                                        $ent_motivo_reprova[3] = '';
                                        $sai_motivo_reprova[3] = '';
                                        $ent_motivo_reprova[4] = '';
                                        $sai_motivo_reprova[4] = '';

                                        $status_ent[1] = false;
                                        $status_sai[1] = false;
                                        $status_ent[2] = false;
                                        $status_sai[2] = false;
                                        $status_ent[3] = false;
                                        $status_sai[3] = false;
                                        $status_ent[4] = false;
                                        $status_sai[4] = false;

                                        $dataref_forcado_ent[1] = false;
                                        $dataref_forcado_sai[1] = false;
                                        $dataref_forcado_ent[2] = false;
                                        $dataref_forcado_sai[2] = false;
                                        $dataref_forcado_ent[3] = false;
                                        $dataref_forcado_sai[3] = false;
                                        $dataref_forcado_ent[4] = false;
                                        $dataref_forcado_sai[4] = false;

                                        $dataref_ent[1] = false;
                                        $dataref_sai[1] = false;
                                        $dataref_ent[2] = false;
                                        $dataref_sai[2] = false;
                                        $dataref_ent[3] = false;
                                        $dataref_sai[3] = false;
                                        $dataref_ent[4] = false;
                                        $dataref_sai[4] = false;

                                        $data_ent[1] = false;
                                        $data_sai[1] = false;
                                        $data_ent[2] = false;
                                        $data_sai[2] = false;
                                        $data_ent[3] = false;
                                        $data_sai[3] = false;
                                        $data_ent[4] = false;
                                        $data_sai[4] = false;

                                        $idaafdt_ent[1] = false;
                                        $idaafdt_sai[1] = false;
                                        $idaafdt_ent[2] = false;
                                        $idaafdt_sai[2] = false;
                                        $idaafdt_ent[3] = false;
                                        $idaafdt_sai[3] = false;
                                        $idaafdt_ent[4] = false;
                                        $idaafdt_sai[4] = false;

                                        // ent 1
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['natureza'] == 0) {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['idaafdt']);
                                                $indice_sai++;
                                            }
                                        }

                                        // sai 1
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['natureza'] == 1) {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['idaafdt']);
                                                $indice_ent++;
                                            }
                                        }

                                        // ent 2
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['natureza'] == 0) {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['idaafdt']);
                                                $indice_sai++;
                                            }
                                        }

                                        // sai 2
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['natureza'] == 1) {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['idaafdt']);
                                                $indice_ent++;
                                            }
                                        }

                                        // ent 3
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['natureza'] == 0) {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['idaafdt']);
                                                $indice_sai++;
                                            }
                                        }

                                        // sai 3
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['natureza'] == 1) {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['idaafdt']);
                                                $indice_ent++;
                                            }
                                        }

                                        // ent 4
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['natureza'] == 0) {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['idaafdt']);
                                                $indice_sai++;
                                            }
                                        }

                                        // sai 4
                                        if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'])) {
                                            $concat = false;
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']) $concat = '-';
                                            if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']) $concat = '+';
                                            if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['natureza'] == 1) {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['motivo_reprova'];
                                                $sai_justificativa_batida[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['justificativa_batida'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #feca07;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #dbdcdd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['motivo_reprova'];
                                                $ent_justificativa_batida[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['justificativa_batida'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['idaafdt']);
                                                $indice_ent++;
                                            }
                                        }

                                        $bglinha = "";
                                        if (diaSemana($DiasEspelho['DATA'], true) == "Dom" || diaSemana($DiasEspelho['DATA'], true) == "Sáb") $bglinha = ' style="background-color: #f9f9f9;" ';

                                        $array_batidas = array();
                                        $array_caracteres = array("+", "-");
                                        if (strlen(trim($ent[1])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $ent[1]),
                                                'natureza'        => 0,
                                                'pendente'        => (strlen(trim($ent_portal[1])) > 0 ? 1 : 0),
                                                'data_ponto'      => dtEn($data_ent[1], true),
                                                'data_referencia' => dtEn($dataref_ent[1], true),
                                                'status'          => $status_ent[1],
                                                'reprova'         => $ent_motivo_reprova[1],
                                                'justificativa_batida'         => $ent_justificativa_batida[1],
                                                'campo'            => 'ent1',
                                                'idaafdt'         => $idaafdt_ent[1]
                                            );
                                        }
                                        if (strlen(trim($sai[1])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $sai[1]),
                                                'natureza'        => 1,
                                                'pendente'        => (strlen(trim($sai_portal[1])) > 0 ? 1 : 0),
                                                'data_ponto'      => dtEn($data_sai[1], true),
                                                'data_referencia' => dtEn($dataref_sai[1], true),
                                                'status'          => $status_sai[1],
                                                'reprova'         => $sai_motivo_reprova[1],
                                                'justificativa_batida'         => $sai_justificativa_batida[1],
                                                'campo'            => 'sai1',
                                                'idaafdt'         => $idaafdt_sai[1]
                                            );
                                        }
                                        if (strlen(trim($ent[2])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $ent[2]),
                                                'natureza'        => 0,
                                                'pendente'        => (strlen(trim($ent_portal[2])) > 0 ? 1 : 0),
                                                'data_ponto'      => dtEn($data_ent[2], true),
                                                'data_referencia' => dtEn($dataref_ent[2], true),
                                                'status'          => $status_ent[2],
                                                'reprova'         => $ent_motivo_reprova[2],
                                                'justificativa_batida'         => $ent_justificativa_batida[2],
                                                'campo'            => 'ent2',
                                                'idaafdt'         => $idaafdt_ent[2]
                                            );
                                        }
                                        if (strlen(trim($sai[2])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $sai[2]),
                                                'natureza'        => 1,
                                                'pendente'        => (strlen(trim($sai_portal[2])) > 0 ? 1 : 0),
                                                'data_ponto'      => dtEn($data_sai[2], true),
                                                'data_referencia' => dtEn($dataref_sai[2], true),
                                                'status'          => $status_sai[2],
                                                'reprova'         => $sai_motivo_reprova[2],
                                                'justificativa_batida'         => $sai_justificativa_batida[2],
                                                'campo'            => 'sai2',
                                                'idaafdt'         => $idaafdt_sai[2]
                                            );
                                        }
                                        if (strlen(trim($ent[3])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $ent[3]),
                                                'natureza'        => 0,
                                                'pendente'        => (strlen(trim($ent_portal[3])) > 0 ? 1 : 0),
                                                'data_ponto'      => dtEn($data_ent[3], true),
                                                'data_referencia' => dtEn($dataref_ent[3], true),
                                                'status'          => $status_ent[3],
                                                'reprova'         => $ent_motivo_reprova[3],
                                                'justificativa_batida'         => $ent_justificativa_batida[3],
                                                'campo'            => 'ent3',
                                                'idaafdt'         => $idaafdt_ent[3]
                                            );
                                        }
                                        if (strlen(trim($sai[3])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $sai[3]),
                                                'natureza'        => 1,
                                                'pendente'        => (strlen(trim($sai_portal[3])) > 0 ? 1 : 0),
                                                'data_ponto'      => dtEn($data_sai[3], true),
                                                'data_referencia' => dtEn($dataref_sai[3], true),
                                                'status'          => $status_sai[3],
                                                'reprova'         => $sai_motivo_reprova[3],
                                                'justificativa_batida'         => $sai_justificativa_batida[3],
                                                'campo'            => 'sai3',
                                                'idaafdt'         => $idaafdt_sai[3]
                                            );
                                        }
                                        if (strlen(trim($ent[4])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $ent[4]),
                                                'natureza'        => 0,
                                                'pendente'        => (strlen(trim($ent_portal[4])) > 0 ? 1 : 0),
                                                'data_referencia' => dtEn($dataref_ent[4], true),
                                                'status'          => $status_ent[4],
                                                'reprova'         => $ent_motivo_reprova[4],
                                                'justificativa_batida'         => $ent_justificativa_batida[4],
                                                'campo'            => 'ent4',
                                                'idaafdt'         => $idaafdt_ent[4]
                                            );
                                        }
                                        if (strlen(trim($sai[4])) > 0) {
                                            $array_batidas[] = array(
                                                'batida'          => str_replace($array_caracteres, '', $sai[4]),
                                                'natureza'        => 1,
                                                'pendente'        => (strlen(trim($sai_portal[4])) > 0 ? 1 : 0),
                                                'data_ponto'      => dtEn($data_sai[4], true),
                                                'data_referencia' => dtEn($dataref_sai[4], true),
                                                'status'          => $status_sai[4],
                                                'reprova'         => $sai_motivo_reprova[4],
                                                'justificativa_batida'         => $sai_justificativa_batida[4],
                                                'campo'            => 'sai4',
                                                'idaafdt'         => $idaafdt_sai[4]
                                            );
                                        }

                                        // monta array de abonos solicitados pendentes de aprovação
                                        $array_abonos = array();
                                        $abono_pendente = "";
                                        $abono_pendente_mobile = "";
                                        $abono_reprovado = "";
                                        if ($resSolicitacaoAbono) {
                                            foreach ($resSolicitacaoAbono as $keyb => $SolicitacaoAbono) {
                                                if (dtBr($SolicitacaoAbono['dtponto']) == dtBr($DiasEspelho['DATA'])) {

                                                    $nome_anexo = "";
                                                    if(strlen(trim($SolicitacaoAbono['abono_atestado'])) > 0){
                                                        $file = explode('|', $SolicitacaoAbono['abono_atestado']);
                                                        $nome_anexo = $file[0];
                                                        unset($file);
                                                    }

                                                    $array_abonos[] = array(
                                                        'id'                => $SolicitacaoAbono['id'],
                                                        'data'              => dtEn($SolicitacaoAbono['dtponto'], true),
                                                        'inicio'            => m2h($SolicitacaoAbono['abn_horaini'], 4),
                                                        'termino'           => m2h($SolicitacaoAbono['abn_horafim'], 4),
                                                        'codabono'          => $SolicitacaoAbono['abn_codabono'],
                                                        'status'            => $SolicitacaoAbono['status'],
                                                        'just_abono_tipo'   => $SolicitacaoAbono['justificativa_abono_tipo'],
                                                        'nome_anexo'        => $nome_anexo
                                                    );
                                                    
                                                    if($SolicitacaoAbono['status'] == 3){
                                                        $abono_reprovado = 1;
                                                        $abono_pendente = '<i title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-square bgeldorado_2" style="font-size:20px;"></i>';
                                                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-timer-off tippy-btn bgeldorado_2" style="font-size:20px;"></i>';
                                                    }else{
                                                        $abono_pendente = '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square txteldorado_5" style="font-size:20px;"></i>';
                                                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Abono Pendente de Aprovação" class="mdi mdi-timer-off tippy-btn txteldorado_5" style="font-size:20px; color: #feca07 ;"></i>';
                                                    }
                                                    unset($resSolicitacaoAbono[$keyb]);
                                                }
                                            }
                                        }

                                        // abono pendente de aprovação RH
                                        if($resAbonoPendenteRH){
                                            foreach($resAbonoPendenteRH as $keya => $AbonoPendente){
                                                if (dtBr($AbonoPendente['DATA']) == dtBr($DiasEspelho['DATA'])) {
                                                    $abono_pendente = '<i title="Aguardando Aprovação RH" class="mdi mdi-square txteldorado_8" style="font-size:20px;"></i>';
                                                    $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Aguardando Aprovação RH" class="mdi mdi-timer-off tippy-btn txteldorado_8" style="font-size:20px;"></i>';
                                                    break;
                                                }
                                            }
                                        }

                                        // verifica se justificou extra
                                        $justificativa_extra = "";
                                        $codigo_justificativa_extra = "";
                                        if(strlen(trim($DiasEspelho['justificativa_extra'])) > 0){
                                            $dados_justificados = explode(' - ', $DiasEspelho['justificativa_extra']);
                                            $codigo_justificativa_extra = $dados_justificados[0];
                                            $justificativa_extra = ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.$dados_justificados[1].'" class="mdi mdi-comment-check-outline tippy-btn" style="font-size:20px; color: #60b95b;"></i>';
                                        }

                                        $possui_extra = ((int)$DiasEspelho['EXTRAEXECUTADO'] > 0) ? true : false;
                                        if($possui_extra and $justificativa_extra == "" ){
                                            $bglinha = ' style="background-color: #c8c02d;" ';
                                        }

                                        $possui_falta_atraso = ((int)$DiasEspelho['FALTA'] > 0 || (int)$DiasEspelho['ATRASO'] > 0) ? true : false;
                                        if($possui_falta_atraso){
                                            $bglinha = ' style="background-color: #f3dab0;" ';
                                        }

                                        $tipo_ocorrencia = 0;
                                        if((int)$DiasEspelho['FALTA']) $tipo_ocorrencia = 6;
                                        if((int)$DiasEspelho['ATRASO']) $tipo_ocorrencia = 5;


                                        //-----------------------------------------------
                                        // regra para bloqueio do ponto
                                        //-----------------------------------------------
                                        if($statusPeriodo == 1){
                                            if($isGestor){

                                                $periodo_bloqueado = (dtEn($EspelhoConfiguracao[0]['limite_gestor'], true) < date('Y-m-d')) ? true : false;
                                              
                                                if($gestorPossuiExcecao && $periodo_bloqueado){
                                                    $periodo_bloqueado = (dtEn($gestorPossuiExcecao, true) < date('Y-m-d')) ? true : false;
                                                }
                                            }else{
                                                $periodo_bloqueado = (dtEn($EspelhoConfiguracao[0]['limite_funcionario'], true) < date('Y-m-d')) ? true : false;
                                                if($gestorPossuiExcecao && $periodo_bloqueado){
                                                    $periodo_bloqueado = (dtEn($gestorPossuiExcecao, true) < date('Y-m-d')) ? true : false;
                                                }
                                            }
                                        }
                                        if($statusPeriodo == 0) $periodo_bloqueado = true;
                                        //-----------------------------------------------
                                        ?>
                                        <tr>
                                            <td class="n-mobile-cell text-center <?= ($bglinha != "" && 1==2) ? "text-danger" : ""; ?>" style="background-color: #f9f9f9;"><?= dtBr($DiasEspelho['DATA']); ?></td>
                                            <td class="n-mobile-cell text-center <?= ($bglinha != "" && 1==2) ? "text-danger" : ""; ?>" style="background-color: #f9f9f9;"><?= diaSemana($DiasEspelho['DATA'], true); ?></td>
                                            
																																																			   
																					
																																												   
														 
                                                <td <?php if($idaafdt_ent[1] > 0 && $status_ent[1] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($ent_motivo_reprova[1])) > 0){echo ' style="background:#f5943f;" title="'.$ent_motivo_reprova[1].'"';}elseif(strlen(trim($ent_portal[1])) > 0 && $status_ent[1] != "T"){echo ' style="background:#feca07;" ';}elseif($status_ent[1] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?php if(strlen(trim($ent[1])) > 0): ?><?= $ent[1].' <span class="badge badge-light">'.(($status_ent[1] != 'C') ? 'D' : 'C').'</span>'; ?><?php endif; ?></td>
                                                <td <?php if($idaafdt_sai[1] > 0 && $status_sai[1] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($sai_motivo_reprova[1])) > 0){echo ' style="background:#f5943f;" title="'.$sai_motivo_reprova[1].'"';}elseif(strlen(trim($sai_portal[1])) > 0 && $status_sai[1] != "T"){echo ' style="background:#feca07;" ';}elseif($status_sai[1] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?php if(strlen(trim($sai[1])) > 0): ?><?= $sai[1].' <span class="badge badge-light">'.(($status_sai[1] != 'C') ? 'D' : 'C').'</span>'; ?><?php endif; ?></td>
                                                <td <?php if($idaafdt_ent[2] > 0 && $status_ent[2] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($ent_motivo_reprova[2])) > 0){echo ' style="background:#f5943f;" title="'.$ent_motivo_reprova[2].'"';}elseif(strlen(trim($ent_portal[2])) > 0 && $status_ent[2] != "T"){echo ' style="background:#feca07;" ';}elseif($status_ent[2] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?php if(strlen(trim($ent[2])) > 0): ?><?= $ent[2].' <span class="badge badge-light">'.(($status_ent[2] != 'C') ? 'D' : 'C').'</span>'; ?><?php endif; ?></td>
                                                <td <?php if($idaafdt_sai[2] > 0 && $status_sai[2] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($sai_motivo_reprova[2])) > 0){echo ' style="background:#f5943f;" title="'.$sai_motivo_reprova[2].'"';}elseif(strlen(trim($sai_portal[2])) > 0 && $status_sai[2] != "T"){echo ' style="background:#feca07;" ';}elseif($status_sai[2] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?php if(strlen(trim($sai[2])) > 0): ?><?= $sai[2].' <span class="badge badge-light">'.(($status_sai[2] != 'C') ? 'D' : 'C').'</span>'; ?><?php endif; ?></td>
                                                <td <?php if($idaafdt_ent[3] > 0 && $status_ent[3] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($ent_motivo_reprova[3])) > 0){echo ' style="background:#f5943f;" title="'.$ent_motivo_reprova[3].'"';}elseif(strlen(trim($ent_portal[3])) > 0 && $status_ent[3] != "T"){echo ' style="background:#feca07;" ';}elseif($status_ent[3] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?php if(strlen(trim($ent[3])) > 0): ?><?= $ent[3].' <span class="badge badge-light">'.(($status_ent[3] != 'C') ? 'D' : 'C').'</span>'; ?><?php endif; ?></td>
                                                <td <?php if($idaafdt_sai[3] > 0 && $status_sai[1] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($sai_motivo_reprova[3])) > 0){echo ' style="background:#f5943f;" title="'.$sai_motivo_reprova[3].'"';}elseif(strlen(trim($sai_portal[3])) > 0 && $status_sai[3] != "T"){echo ' style="background:#feca07;" ';}elseif($status_sai[3] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?php if(strlen(trim($sai[3])) > 0): ?><?= $sai[3].' <span class="badge badge-light">'.(($status_sai[3] != 'C') ? 'D' : 'C').'</span>'; ?><?php endif; ?></td>
                                                <?php if(strlen(trim($resFuncionario[0]['DATADEMISSAO'])) > 0 && $DiasEspelho['DATA'] >= $resFuncionario[0]['DATADEMISSAO']): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-dark  txtpdf" style="background: #004d95 !important;">Demitido - Não Utiliza Ponto</span></td>
                                                <?php elseif($DiasEspelho['AFASTAMENTO'] > 0): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-purple  txtpdf" style="background: #004d95 !important;"><?= $DiasEspelho['AFASTAMENTO']; ?></span></td>
                                                <?php elseif($DiasEspelho['FERIADO'] > 0): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-success  txtpdf" style="background: #04609d !important;">FERIADO</span></td>
                                                <?php elseif($DiasEspelho['COMPENSADO'] > 0): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-purple  txtpdf" style="background: #004d95 !important;">COMPENSADO</span></td>
                                                <?php elseif($DiasEspelho['DESCANSO'] > 0): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-purple  txtpdf" style="background: #004d95 !important;">DESCANSO</span></td>
                                                <?php elseif($DiasEspelho['FERIAS'] > 0): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-purple  txtpdf" style="background: #004d95 !important;">FÉRIAS</span></td>
                                                <?php else: ?>
                                                    <td <?php if($idaafdt_ent[4] > 0 && $status_ent[4] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($ent_motivo_reprova[4])) > 0){echo ' style="background:#f5943f;" title="'.$ent_motivo_reprova[4].'"';}elseif(strlen(trim($ent_portal[4])) > 0 && $status_ent[4] != "T"){echo ' style="background:#feca07;" ';}elseif($status_ent[4] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $ent[4]; ?></td>
                                                    <td <?php if($idaafdt_sai[4] > 0 && $status_sai[4] == "D") { echo ' style="background:#9de3ad;" ';} elseif(strlen(trim($sai_motivo_reprova[4])) > 0){echo ' style="background:#f5943f;" title="'.$sai_motivo_reprova[4].'"';}elseif(strlen(trim($sai_portal[4])) > 0 && $status_sai[4] != "T"){echo ' style="background:#feca07;" ';}elseif($status_sai[4] == "T"){echo ' style="background:#dbdcdd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $sai[4]; ?></td>
                                                <?php endif; ?>
														   
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-primary"><?= ((int)$DiasEspelho['HTRAB'] > 0) ? m2h($DiasEspelho['HTRAB']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['EXTRA_1AFAIXA'] > 0) ? m2h($DiasEspelho['EXTRA_1AFAIXA']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['EXTRA_2AFAIXA'] > 0) ? m2h($DiasEspelho['EXTRA_2AFAIXA']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['EXTRA_100'] > 0) ? m2h($DiasEspelho['EXTRA_100']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['EXTRAAUTORIZADO'] > 0) ? m2h($DiasEspelho['EXTRAAUTORIZADO']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-info"><?= ((int)$DiasEspelho['ADICIONAL'] > 0) ? m2h($DiasEspelho['ADICIONAL']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['ATRASO'] > 0) ? m2h($DiasEspelho['ATRASO']) : ""; ?></td>
                                            <?php $suspensao = ($DiasEspelho['SUSPENSAO'] > 0) ? '*' : ''; ?>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['FALTA'] > 0) ? m2h($DiasEspelho['FALTA']).$suspensao : ""; ?></td>

                                            <?php if($is_motorista): ?>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-info"><?= ((int)$DiasEspelho['total_refeicao'] > 0) ? m2h($DiasEspelho['total_refeicao']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-info"><?= ((int)$DiasEspelho['total_direcao'] > 0) ? m2h($DiasEspelho['total_direcao']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-info"><?= ((int)$DiasEspelho['total_espera'] > 0) ? m2h($DiasEspelho['total_espera']) : ""; ?></td>
											<td <?= $bglinha; ?> class="n-mobile-cell text-center text-info"><?= ((int)$DiasEspelho['total_parado'] > 0) ? m2h($DiasEspelho['total_parado']) : ""; ?></td>
																																																					
                                            <?php endif; ?>

                                            <?php if(!$impressao): ?>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-success"><?= ((int)$DiasEspelho['ABONO'] > 0) ? m2h($DiasEspelho['ABONO']) : $abono_pendente; ?></td>
                                            
                                            <td <?= $bglinha; ?> class="y-mobile-cell d-none text-center">
                                                <h3 style="font-size: 20px;" class="m-0 p-0"><?= date('d/m', strtotime($DiasEspelho['DATA'])); ?></h3><small class="text-gray d-block" style="color: #999999;"><?= diaSemana($DiasEspelho['DATA']); ?></small><?= $abono_pendente_mobile.$justificativa_extra; ?>

                                                <?php if($DiasEspelho['AFASTAMENTO'] > 0): ?>
                                                    <span class="badge badge-purple txtpdf" style="background: #004d95 !important;"><?= $DiasEspelho['AFASTAMENTO']; ?></span>
                                                <?php elseif($DiasEspelho['FERIADO'] > 0): ?>
                                                    <span class="badge badge-success txtpdf" style="background: #04609d !important;">FERIADO</span>
                                                <?php elseif($DiasEspelho['COMPENSADO'] > 0): ?>
                                                    <span class="badge badge-purple txtpdf" style="background: #004d95 !important;">COMPENSADO</span>
                                                <?php elseif($DiasEspelho['DESCANSO'] > 0): ?>
                                                    <span class="badge badge-purple txtpdf" style="background: #004d95 !important;">DESCANSO</span>
                                                <?php elseif($DiasEspelho['FERIAS'] > 0): ?>
                                                    <span class="badge badge-purple txtpdf" style="background: #004d95 !important;">FERIAS</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td <?= $bglinha; ?> class="y-mobile-cell d-none text-center align-top">

                                                <?php $numero_batida = 0; ?>
                                                <?php $proxima_batida = 1; ?>
                                                <?php if (strlen(trim($ent[1])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[1])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[1]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' : $ent_portal[1]) . $ent[1].(($status_ent[1] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">E1</small></h3><?php $numero_batida++;                                                                                                                                                                                                                            $proxima_batida = 1; ?><?php endif; ?>
                                                <?php if (strlen(trim($ent[2])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[2])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[2]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' : $ent_portal[2]) . $ent[2].(($status_ent[2] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">E2</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                $proxima_batida = 2; ?><?php endif; ?>
                                                <?php if (strlen(trim($ent[3])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[3])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[3]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' : $ent_portal[3]) . $ent[3].(($status_ent[3] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">E3</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                    $proxima_batida = 3; ?><?php endif; ?>
                                                <?php if (strlen(trim($ent[4])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[4])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[4]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' : $ent_portal[4]) . $ent[4].(($status_ent[4] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">E4</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                                    $proxima_batida = 4; ?><?php endif; ?>

                                            </td>
                                            <td <?= $bglinha; ?> class="y-mobile-cell d-none text-center align-top">

                                                <?php if (strlen(trim($sai[1])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[1])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[1]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' :  $sai_portal[1]) . $sai[1].(($status_sai[1] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">S1</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                        $proxima_batida = 2; ?><?php endif; ?>
                                                <?php if (strlen(trim($sai[2])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[2])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[2]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' :  $sai_portal[2]) . $sai[2].(($status_sai[2] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">S2</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                            $proxima_batida = 3; ?><?php endif; ?>
                                                <?php if (strlen(trim($sai[3])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[3])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[3]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' :  $sai_portal[3]) . $sai[3].(($status_sai[3] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">S3</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                                $proxima_batida = 4; ?><?php endif; ?>
                                                <?php if (strlen(trim($sai[4])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[4])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[4]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #f5943f;"></i>' :  $sai_portal[4]) . $sai[4].(($status_sai[4] != 'C') ? 'D' : 'C'); ?></span> <small style="color: #999999;">S4</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                                    $proxima_batida = 5; ?><?php endif; ?>

                                            </td>
                                            <td <?= $bglinha; ?> class="text-center">
                                                    <?php if(!$periodo_bloqueado || $rh || 1==1): ?>
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <?php if($DiasEspelho['SUSPENSAO'] <= 0): ?>
                                                                <?php 
                                                                if (!(($DiasEspelho['AFASTAMENTO'] > 0) || ($DiasEspelho['FERIAS'] > 0) || (strlen(trim($resFuncionario[0]['DATADEMISSAO'])) > 0 && $DiasEspelho['DATA'] >= $resFuncionario[0]['DATADEMISSAO']) || ($DiasEspelho['DATA'] < $resFuncionario[0]['DATAADMISSAO']))) : ?>
                                                                    <button type="button" class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="mdi mdi-dots-vertical"></i></button>
                                                                    <div class="dropdown-menu">
                                                                        <!--<button <?= ($numero_batida == 8) ? ' disabled ' : ''; ?> onclick="abrirInclusaoBatida('<?= dtEn($DiasEspelho['DATA'], true); ?>', <?= $numero_batida; ?>, <?= $proxima_batida; ?>);" type="button" class="dropdown-item <?= ($numero_batida == 8) ? ' text-secondary ' : ''; ?>" title="Incluir batida"><i class="fas fa-plus-circle"></i> Incluir batida</button>-->
                                                                        <?php
                                                                        $escala         = explode(' ', $DiasEspelho['ESCALA']);
                                                                        $inicioEscala   = $escala[0];
                                                                        $terminoEscala  = end($escala);
                                                                        ?>
                                                                        <button onclick="abrirAlteracaoBatida('<?= dtEn($DiasEspelho['DATA'], true); ?>', '<?= urlencode(json_encode($array_batidas)); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= $DiasEspelho['ESCALA']; ?>', '<?= dtBr($DiasEspelho['DATA']); ?>', '<?= ($periodo_bloqueado) ? 1 : 0; ?>', '<?= $inicioEscala; ?>', '<?= $terminoEscala; ?>')" type="button" class="dropdown-item" title="Alterar registro"><i class="mdi mdi-pencil-outline"></i> Incluir/Alterar registro</button>
                                                                        <button onclick="abrirResumoDiario('<?= dtBr($DiasEspelho['DATA']); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= ((int)$DiasEspelho['HTRAB'] > 0) ? m2h($DiasEspelho['HTRAB']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['ATRASO'] > 0) ? m2h($DiasEspelho['ATRASO']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['FALTA'] > 0) ? m2h($DiasEspelho['FALTA']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['EXTRAAUTORIZADO'] > 0) ? m2h($DiasEspelho['EXTRAAUTORIZADO']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['ABONO'] > 0) ? m2h($DiasEspelho['ABONO']) : '0:00'; ?>', '<?= urlencode(json_encode($array_batidas)); ?>', '<?= $DiasEspelho['ESCALA']; ?>', '<?= ((int)$DiasEspelho['total_refeicao'] > 0) ? m2h($DiasEspelho['total_refeicao']) : "0:00"; ?>', '<?= ((int)$DiasEspelho['total_direcao'] > 0) ? ($DiasEspelho['total_direcao'].':00') : "0:00"; ?>', '<?= ((int)$DiasEspelho['total_espera'] > 0) ? m2h($DiasEspelho['total_espera']) : "0:00"; ?>', '<?= ((int)$DiasEspelho['total_parado'] > 0) ? m2h($DiasEspelho['total_parado']) : "0:00"; ?>', '<?= m2h($DiasEspelho['ADICIONAL']); ?>', '0:00')" type="button" class="dropdown-item" title="Resumo diário"><i class="mdi mdi-file-document-box-outline"></i> Resumo diário</button>
                                                                        
                                                                        
                                                                        <?php if((int)$DiasEspelho['ATRASO'] > 0 || (int)$DiasEspelho['FALTA'] > 0): ?><button onclick="abrirSolicitacaoAbono('<?= dtEn($DiasEspelho['DATA'], true); ?>', '<?= dtBr($DiasEspelho['DATA']); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= urlencode(json_encode($array_abonos)); ?>', <?= $tipo_ocorrencia; ?>, '<?= urlencode(json_encode($array_batidas)); ?>', '<?= $DiasEspelho['ESCALA']; ?>', '<?= ($periodo_bloqueado) ? 1 : 0; ?>', '<?= $inicioEscala; ?>', '<?= $terminoEscala; ?>', '<?= ((int)$DiasEspelho['FALTA'] > 0) ? m2h($DiasEspelho['FALTA']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['ATRASO'] > 0) ? m2h($DiasEspelho['ATRASO']) : '0:00'; ?>')" type="button" class="dropdown-item" title="Solicitar abono"><i class="mdi mdi-timer-off"></i> Solicitar abono</button><?php endif; ?>
                                                                        <?php if((int)$DiasEspelho['EXTRAAUTORIZADO'] > 0): ?><button onclick="abrirJustificativaExtra('<?= dtEn($DiasEspelho['DATA'], true); ?>', '<?= dtBr($DiasEspelho['DATA']); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= $codigo_justificativa_extra; ?>', '<?= urlencode(json_encode($array_batidas)); ?>', '<?= $DiasEspelho['ESCALA']; ?>', '<?= ($periodo_bloqueado) ? 1 : 0; ?>')" type="button" class="dropdown-item" title="Justificativa de Extra"><i class="mdi mdi-comment-check-outline"></i> Justificativa de Extra</button><?php endif; ?>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <i title="Periodo bloqueado" class="mdi mdi-lock-alert" disabled style="font-size:20px;color:#999999;"></i>
                                                                <?php endif;?>
                                                            <?php else: ?>
                                                                <button type="button" class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="mdi mdi-dots-vertical"></i></button>
                                                                    <div class="dropdown-menu">
                                                                        <button onclick="abrirResumoDiario('<?= dtBr($DiasEspelho['DATA']); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= ((int)$DiasEspelho['HTRAB'] > 0) ? m2h($DiasEspelho['HTRAB']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['ATRASO'] > 0) ? m2h($DiasEspelho['ATRASO']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['FALTA'] > 0) ? m2h($DiasEspelho['FALTA']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['EXTRAAUTORIZADO'] > 0) ? m2h($DiasEspelho['EXTRAAUTORIZADO']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['ABONO'] > 0) ? m2h($DiasEspelho['ABONO']) : '0:00'; ?>', '<?= urlencode(json_encode($array_batidas)); ?>', '<?= $DiasEspelho['ESCALA']; ?>', '<?= ((int)$DiasEspelho['total_refeicao'] > 0) ? m2h($DiasEspelho['total_refeicao']) : "0:00"; ?>', '<?= ((int)$DiasEspelho['total_direcao'] > 0) ? ($DiasEspelho['total_direcao'].':00') : "0:00"; ?>', '<?= ((int)$DiasEspelho['total_espera'] > 0) ? m2h($DiasEspelho['total_espera']) : "0:00"; ?>', '<?= ((int)$DiasEspelho['total_parado'] > 0) ? m2h($DiasEspelho['total_parado']) : "0:00"; ?>', '<?= m2h($DiasEspelho['ADICIONAL']); ?>', '<?= m2h($DiasEspelho['SUSPENSAO']) ?>')" type="button" class="dropdown-item" title="Resumo diário"><i class="mdi mdi-file-document-box-outline"></i> Resumo diário</button>
                                                                    </div>
                                                            <?php endif;?>
                                                    <?php else: ?>
                                                        <i title="Periodo bloqueado" class="mdi mdi-lock-alert" disabled style="font-size:20px;color:#999999;"></i>
                                                    <?php endif;?>

                                                </div>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php unset($array_batidas); ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="text-align: left; font-size:10px; color:rgb(75, 74, 74);">
                            Legenda: &nbsp;&nbsp; ( c ) Coletado &nbsp;&nbsp; ( d ) Digitado
                        </div>
                        <?php if($impressao): ?><br>
                            <div style="text-align: right; ">
                            Total Hrs. Trab. <?= m2h($resDiasEspelho[0]['QTDE_HORAS']); ?>
                            </div>
                            <table>
                                <tr>
                                    <td align="center" class="text-align:center;" colspan="4" bgcolor="#f0f0f0"><b>Saldo de Banco de Horas</b></td>
                                </tr>
                                <tr>
                                    <td align="center" class="text-align:center;"><b>Saldo Anterior</b></td>
                                    <td align="center" class="text-align:center;"><b>Saldo Mês Positivo</b></td>
                                    <td align="center" class="text-align:center;"><b>Saldo Mês Negativo</b></td>
                                    <td align="center" class="text-align:center;"><b>Saldo total</b></td>
                                </tr>
                                <tr>
                                    <td align="center" class="text-align:center;"><?= m2h($HorasPositivas) > 0 ? '+' : '' ?><?= m2h($HorasPositivas) ?></td>
                                    <td align="center" class="text-align:center;">+<?= m2h($h_mes_positivo) ?></td>
                                    <td align="center" class="text-align:center;"><?= m2h($h_mes_negativo) ?></td>
                                    <td align="center" class="text-align:center;"><?= m2h($SaldoTotal) > 0 ? '+' : '' ?><?= m2h($SaldoTotal) ?></td>
                                </tr>
                            </table>
                        <?php endif; ?>

                        <h4 class="mt-4 mb-0 h4">Eventos do movimento no período:</h4>
                        <?php if ($resMovimentos) : ?>
                            <table class="table mb-0 table-centered table-sm ">
                                <tbody>
                                    <?php foreach ($resMovimentos as $key => $Movimentos) : ?>
                                        <tr>
                                            <td class="text-center"><?= $Movimentos['CODIGO']; ?></td>
                                            <td class="text-left pl-3"><?= $Movimentos['DESCRICAO']; ?></td>
                                            <td class="text-right pl-3"><?= m2h($Movimentos['NUMHORAS']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <br><br>Sem movimentos
                        <?php endif; ?>

                        <script>
                            $(document).ready(function(e){
                                $("#h_anterior").html('<?= m2h($HorasPositivas) > 0 ? '+' : '' ?><?= m2h($HorasPositivas) ?>');
                                $("#h_mes_positivo").html('+<?= m2h($h_mes_positivo) ?>');
                                $("#h_mes_negativo").html('<?= m2h($h_mes_negativo) ?>');
                                $("#h_total").html('<?= m2h($SaldoTotal) > 0 ? '+' : '' ?><?= m2h($SaldoTotal) ?>');
                            });
                        </script>
                    <?php endif; // $DiasEspelho 
                    ?>



                </div>
            </div>
        </div><!-- end main -->


    </div>
</div><!-- container -->
<?php if(!$impressao): ?>
<script>
    // variaveis global
    quantidade_batidas = 1;
    data_nova_batida = '';
    tipo_do_abono = '';
    lista_abonos = JSON.parse('<?= json_encode($resAbono); ?>');
    abono_noturno = false;
    msg_noturno = "";
    msg_aviso_batida = "";
    msg_aviso = "";
    inicio_escala = "";
    termino_escala = "";
    qtde_falta = "";
    <?php
    $select_justificativa_ajuste = "";
    if($resJustificativaAjuste){
        foreach($resJustificativaAjuste as $key => $JustificativaAjuste){
            $select_justificativa_ajuste .= '<option value="'.$JustificativaAjuste['descricao'].'">'.$JustificativaAjuste['descricao'].'</option>';
        }
    }
    echo "var select_justificativa_ajuste = '{$select_justificativa_ajuste}';
    ";
    ?>

    const consultarEspelho = () => {

        let dados = {
            "periodo": $("#periodo").val(),
        }

        if (dados.periodo == "") {
            exibeAlerta("error", "<b>Período</b> não selecionado.");
            return false;
        }

        openLoading();

        document.getElementById("form_espelho").submit();

    }
    <?php if ($resDiasEspelho && $chapa) : ?>

        <?php if($perfilRecalculo): ?>
        //------------------------------------------------------
        // reclculo do ponto
        //------------------------------------------------------
        const recalcularPonto = () => {

            Swal.fire({
                icon             : 'question',
                title            : 'Confirma recalculo do espelho de ponto?',
                showDenyButton   : true,
                showCancelButton : true,
                confirmButtonText: `Sim Recalcular`,
                denyButtonText   : `Cancelar`,
                showCancelButton : false,
                showCloseButton  : false,
                allowOutsideClick: false,
                width: 600,
            }).then((result) => {
                if (result.isConfirmed) {

                    try {

                        openLoading();

                        $.ajax({
                            url: "<?= base_url('ponto/espelho/action/recalcular_espelho') ?>",
                            type: 'POST',
                            data: {
                                'chapa': '<?= $chapa; ?>',
                                'data' : '<?= $resDiasEspelho[0]['DATA']; ?>'
                            },
                            success: function(result) {
                                openLoading(true);

                                var response = JSON.parse(result);

                                exibeAlerta(response.tipo, response.msg, 4, window.location.href);

                                var myTimeout = setTimeout(function() {
                                    consultarEspelho();
                                    clearTimeout(myTimeout);
                                }, 2000);
                            },
                        });

                    } catch (e) {
                        exibeAlerta('error', 'Erro interno: ' + e);
                    }
                }
            });

        }
        <?php endif; ?>

        <?php if(!$periodo_bloqueado || $rh || 1==1): ?>
        //------------------------------------------------------
        // inclusão de nova batida
        //------------------------------------------------------
        const abrirInclusaoBatida = (data, numero_batida, proxima_batida) => {

            try {

                $(".modal_incluir_batida").modal('show');
                $("[name=data_batida]").val(data);
                $("[name=referencia_batida]").val(data);
                $("[name=natureza_batida]").val((numero_batida % 2 == 0) ? 0 : 1);
                $("[name=horario_batida]").val('').mask('99:99');
                $("[name=justificativa_batida]").val('');
                quantidade_batidas = proxima_batida;

                $("[name=horario_batida]").on('keyup', function(e) {
                    var valor = $(this).val();
                    var valido = validaHorario(valor);

                    valor = parseInt(valor.replace(':', ''));
                    if (valor > 2359 || !valido) {
                        $(this).addClass('parsley-error text-danger');
                    } else {
                        $(this).removeClass('parsley-error text-danger');
                    }
                });

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        }
        // cadastra nova batida
        const cadastraBatida = () => {

            try {

                let dados = {
                    //"chapa"               : '',
                    "data_batida": $("[name=data_batida]").val(),
                    "referencia_batida": $("[name=referencia_batida]").val(),
                    "natureza_batida": $("[name=natureza_batida]").val(),
                    "horario_batida": $("[name=horario_batida]").val(),
                    "justificativa_batida": $("[name=justificativa_batida]").val().trim(),
                    "numero_batida": quantidade_batidas,
                    "codfilial": '<?= ($resFuncionario[0]['CODFILIAL'] ?? 1) ?>',
                    "chapa": '<?= $chapa ?>'
                }

                if (dados.data_batida == "") {
                    exibeAlerta('error', '<b>Data</b> não informada.');
                    return false;
                }
                if (dados.referencia_batida == "") {
                    exibeAlerta('error', '<b>Data referencia</b> informada.');
                    return false;
                }
                if (dados.natureza_batida == "") {
                    exibeAlerta('error', '<b>Natureza</b> não informada.');
                    return false;
                }
                if (dados.horario_batida == "") {
                    exibeAlerta('error', '<b>Registro</b> não informado.');
                    return false;
                }
                if (!validaHorario(dados.horario_batida)) {
                    exibeAlerta('error', '<b>Registro</b> inválido.');
                    return false;
                }
                var batida = parseInt(dados.horario_batida.replace(':', ''));
                if (batida > 2359) {
                    exibeAlerta('error', '<b>Registro</b> não pode ser superior a <b>23:59</b>.');
                    return false;
                }
                if (dados.justificativa_batida == "") {
                    exibeAlerta('error', '<b>Justificativa</b> não informada.');
                    return false;
                }
                if (dados.justificativa_batida.length <= 4) {
                    exibeAlerta('error', '<b>Justificativa</b> deve conter mais de 4 caracteres.');
                    return false;
                }

                // openLoading();

                // Pergunta se quer adicionar observação
                Swal.fire({
                    title: 'Deseja incluir informações adicionais?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                    customClass: {
                        title: 'swal2-title'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        abrirModalObservacao(dados, enviarBatidaComObservacao);
                    } else {
                        enviarBatidaComObservacao(dados);
                    }
                });

                function enviarBatidaComObservacao(dados) {
                    openLoading();
                    $.ajax({
                        url: "<?= base_url('ponto/espelho/action/cadastrar_batida') ?>",
                        type: 'POST',
                        data: dados,
                        success: function(result) {
                            openLoading(true);
                            try {
                                var response = JSON.parse(result);
                                if (response.tipo != 'success') {
                                    exibeAlerta(response.tipo, response.msg);
                                } else {
                                    exibeAlerta(response.tipo, response.msg, 3);
                                    setTimeout(function() {
                                        consultarEspelho();
                                    }, 2000);
                                }
                            } catch (e) {
                                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                            }
                        },
                    });
                }
            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        }

        //------------------------------------------------------
        // alteração de batidas
        //------------------------------------------------------
        const abrirAlteracaoBatida = (data, batidas, diaSemana, escala, data_br, periodo_bloqueado, inicioEscala, terminoEscala) => {
            $(".modal_alterar_batida").modal('show');

            inicio_escala = inicioEscala;
            termino_escala = terminoEscala;

            msg_aviso_batida = "";
            
            $(".modal_alterar_batida [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diaSemana + '</small>');
            $(".modal_alterar_batida [data-h-escala], .modal_macro [data-h-escala]").html('Escala: '+escala);
            $(".modal_alterar_batida [data-h-batidas]").html('Registros existentes: ');

            var batidas = JSON.parse(decodeURIComponent(batidas));
            
            $('.modal_alterar_batida tbody').html('');

            data_nova_batida = data;
            natureza_proxima_batida = 0;

            for (var x = 0; x < batidas.length; x++) {

                var liberado = (batidas[x].pendente != 1 || batidas[x].status == 'T') ? ' disabled ' : '';
                var tipo = (batidas[x].pendente != 1 || batidas[x].status == 'T') ? ' data-p="RM" data-idaafdt="' + batidas[x].idaafdt + '" ' : ' data-p="U" ';
                if(batidas[x].reprova != '' && batidas[x].reprova != null) var tipo = ' data-p= "X" ';

                var id_tr = Math.random();
                var html = '<tr data-info="' + id_tr + '" ' + tipo + '>' +
                    '<td><input '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' type="date" data-batida-default="'+batidas[x].data_ponto+'" ' + liberado + ' class="form-control form-control-sm  p-0" value="' + batidas[x].data_ponto + '"></td>' +
                    '<td><input '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' type="date" data-referencia-default="'+batidas[x].data_referencia+'" class="form-control form-control-sm  p-0" value="' + batidas[x].data_referencia + '"></td>' +
                    '<td><div class="input-group"><input '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' data-default="' + batidas[x].batida + '" ' + liberado + ' type="time" class="form-control form-control-sm  p-0" value="' + batidas[x].batida + '"><div class="input-group-append"><span class="input-group-text pt-0 pb-0">'+batidas[x].status+'</span></div></div></td>' +
                    '<td><select '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' class="form-control form-control-sm  p-0" style="min-width:56px;"><option value="0" ' + (batidas[x].natureza != 1 ? 'selected' : '') + '>Entrada</option><option value="1" ' + (batidas[x].natureza == 1 ? 'selected' : '') + '>Saida</option></select></td>' +
                    '<td width="1" class="p-0">';

                    $(".modal_alterar_batida [data-h-batidas]").append(batidas[x].batida + ' | ');

                if(batidas[x].reprova != '' && batidas[x].reprova != null){
                    html += '<button onclick="return excluirBatidaPT(\'' + batidas[x].data_ponto + '\', \'' + batidas[x].batida + '\')" class="btn btn-danger btn-xxs btn-excluir pr-1 pl-1">X</button>';
                }
                else if (batidas[x].status == 'D' && batidas[x].pendente == 0) {
                    //html += '<button onclick="return excluirBatidaRM(\'' + batidas[x].idaafdt + '\')" class="btn btn-danger btn-excluir">X</button>';
                } else if (batidas[x].status == 'D' && batidas[x].pendente == 1) {
                    html += '<button onclick="return excluirBatidaPT(\'' + batidas[x].data_ponto + '\', \'' + batidas[x].batida + '\')" class="btn btn-danger btn-xxs btn-excluir pr-1 pl-1">X</button>';
                }

                html += '</td></tr>';

                var exibe = <?= ($rh) ? 1 : 0; ?>;
                if(batidas[x].justificativa_batida != null && (periodo_bloqueado != 1 || exibe == 1)) html += '<tr data-justificativa="'+id_tr+'" data-just="" style="'+((periodo_bloqueado == 1) ? '' : 'display: none;')+'"><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Motivo do ajuste:</label> <span class="badge badge-primary">'+batidas[x].justificativa_batida.replaceAll('+', ' ')+'</span></td></tr>';
		        if(batidas[x].obs != null && batidas[x].obs != '' && (exibe == 1)) html += '<tr data-observacao="'+id_tr+'" data-obs=""><td><label>Deseja incluir informações adicionais?</label><textarea class="form-control" value="'+batidas[x].obs+'"></textarea></td></tr>';

                $('.modal_alterar_batida tbody').append(html);
		        if(batidas[x].obs != null && batidas[x].obs != '') $("[data-observacao='"+id_tr+"'] textarea").val(batidas[x].obs).change().attr('readonly', true);

                natureza_proxima_batida = (batidas[x].natureza != 1) ? 1 : 0;

            }

            // abre a justificativa da batida ao clicar na linha
            $("[data-info]").on('click', function(e) {
                $("[data-just]").fadeOut(0);
                $("[data-justificativa='" + $(this).attr('data-info') + "']").fadeIn(0);
		        $("[data-obs]").fadeOut(0);
		        $("[data-observacao='" + $(this).attr('data-info') + "']").fadeIn(0)
            });

            verificaLimite();

            if(periodo_bloqueado == 1){
                $(".modal_alterar_batida .modal-body *").prop('disabled', true);
                $(".modal_alterar_batida .modal-footer *").prop('disabled', true);
            }else{
                $(".modal_alterar_batida .modal-body button").prop('disabled', false);
                $(".modal_alterar_batida .modal-body [type=file]").prop('disabled', false);
                $(".modal_alterar_batida .modal-footer *").prop('disabled', false);
            }
            $('#btnConsultaMacro').prop('disabled', false);
        }

        //excluir batida RM tabela ABATFUN
        const excluirBatidaPT = (data_ref, batida) => {

            Swal.fire({
                icon: 'question',
                title: 'Deseja excluir esse registro?',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: `Sim, confirmar`,
                denyButtonText: `Cancelar`,
                showCancelButton: false,
                showCloseButton: false,
                allowOutsideClick: false,
                width: 600,
            }).then((result) => {
                if (result.isConfirmed) {

                    try {
                        let dados = {
                            "data_ref"      : data_ref,
                            "horario_batida": batida,
                            "chapa"         : '<?= $chapa ?>',
                            "coligada"      : '<?= ($resFuncionario[0]['CODFILIAL'] ?? 1) ?>'
                        }

                        openLoading();

                        $.ajax({
                            url: "<?= base_url('ponto/espelho/action/excluir_batida_pt') ?>",
                            type: 'POST',
                            data: dados,
                            success: function(result) {

                                openLoading(true);

                                try {
                                    var response = JSON.parse(result);

                                    if (response.tipo != 'success') {
                                        exibeAlerta(response.tipo, response.msg);
                                    } else {
                                        exibeAlerta(response.tipo, response.msg, 5);
                                        var myTimeout = setTimeout(function() {
                                            consultarEspelho();
                                            clearTimeout(myTimeout);
                                        }, 2000);
                                    }

                                } catch (e) {
                                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                                }

                            },
                        });

                    } catch (e) {
                        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                    }
                } else {
                    return false;
                }
            })
        }

        //excluir batida RM tabela ABATFUN
        const excluirBatidaRM = (idaafdt) => {

            $(".modal_alterar_batida").modal('hide');

            Swal.fire({
                icon: 'info',
                title: 'Digite uma justificativa para excluir esse registro:',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: `Confirmar e excluir`,
                denyButtonText: `Cancelar`,
                showCancelButton: false,
                showCloseButton: false,
                allowOutsideClick: false,
                width: 600,
                input: 'text',
                inputAttributes: {
                    class: 'form-control'
                },
                preConfirm: (justificativa) => {
                    if (justificativa == '') {
                        Swal.showValidationMessage(
                            `Justificativa obrigatória!`
                        );
                    } else {
                        return justificativa;
                    }
                },

            }).then((result) => {
                if (result.isConfirmed) {

                    try {

                        let dados = {
                            "idaafdt": idaafdt,
                            "justificativa": result.value
                        }

                        openLoading();

                        $.ajax({
                            url: "<?= base_url('ponto/espelho/action/excluir_batida_rm') ?>",
                            type: 'POST',
                            data: dados,
                            success: function(result) {

                                openLoading(true);

                                try {
                                    var response = JSON.parse(result);

                                    if (response.tipo != 'success') {
                                        exibeAlerta(response.tipo, response.msg);
                                    } else {
                                        exibeAlerta(response.tipo, response.msg, 5);
                                        var myTimeout = setTimeout(function() {
                                            consultarEspelho();
                                            clearTimeout(myTimeout);
                                        }, 2000);
                                    }

                                } catch (e) {
                                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                                }

                            },
                        });

                    } catch (e) {
                        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                    }
                } else {
                    $(".modal_alterar_batida").modal('show');
                    return false;
                }
            })
        }

        // salva alteração de batida
        const alterarBatida = () => {

            if (!validaDados()) return;

            var dados = [];
            var fd = new FormData();

            $("[data-p]").each(function(e) {
                let tipo = $(this).attr('data-p');
                let idaafdt = (tipo == "RM") ? $(this).attr('data-idaafdt') : 0;
                let id = $(this).attr('data-info');

                if (id != undefined) {
                    if (tipo == "I") {
                        var just = $("[data-justificativa='" + id + "'] select").val().trim();
                    }
			        var obs = $("[data-observacao='" + id + "' ] textarea").val();
                    let data = $("[data-info='" + id + "']").find('input,select')[0].value;
                    let data_default = $("[data-info='" + id + "']").find('input,select')[0].getAttribute('data-batida-default');
                    let data_ref = $("[data-info='" + id + "']").find('input,select')[1].value;
                    let data_ref_default = $("[data-info='" + id + "']").find('input,select')[1].getAttribute('data-referencia-default');
                    let batida = $("[data-info='" + id + "']").find('input,select')[2].value;
                    let batida_default = $("[data-info='" + id + "']").find('input,select')[2].getAttribute('data-default');
                    let natureza = $("[data-info='" + id + "']").find('input,select')[3].value;

                    if (data_ref == "") {
                        $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    } else {
                        $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm');
                    }

                    if (batida == "") {
                        $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    } else {
                        $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm');
                    }

                    if (natureza == "") {
                        $("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    } else {
                        $("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm');
                    }

                    if (tipo != "RM") {
                        if (just != undefined) {
                            if (just == "" || just.length < 4) {
                                $("[data-justificativa='" + id + "'] select").addClass("parsley-error text-danger");
                            } else {
                                $("[data-justificativa='" + id + "'] select").removeClass("parsley-error text-danger");
                            }
                        }
                    }

                    if(tipo != 'X'){

                        // dados.push({
                        //     'data': data_nova_batida,
                        //     'tipo': tipo,
                        //     'id': id,
                        //     'justificativa': (just != undefined) ? just : '',
                        //     'data_ref': data_ref,
                        //     'batida': batida,
                        //     'batida_default': batida_default,
                        //     'natureza': natureza,
                        //     "codfilial": '<?= ($resFuncionario[0]['CODFILIAL'] ?? 1) ?>',
                        //     "chapa": '<?= $chapa ?>',
                        //     "idaafdt": idaafdt
                        // });


                        //-----------------------------------
                        var doc = $("[name=anexo_batida]");

                        var tem_anexo = 0;
                        if (tipo == "I") {
                            if (doc[0].files[0] != undefined) {
                                var tem_anexo = 1;
                            }
                        }
                        
                        var dados = [];

                        dados.push({
                            'data_default'        : data_default,
                            'data'                : data,
                            'tipo'                : tipo,
                            'id'                  : id,
                            'justificativa'       : (just != undefined) ? just : '',
				            'obs'				  : (obs != undefined) ? obs : '',
                            'data_ref'            : data_ref,
                            'data_ref_default'    : data_ref_default,
                            'batida'              : batida,
                            'batida_default'      : batida_default,
                            'natureza'            : natureza,
                            "codfilial"           : '<?= ($resFuncionario[0]['CODFILIAL'] ?? 1) ?>',
                            "chapa"               : '<?= $chapa ?>',
                            "idaafdt"             : idaafdt,
                            "tem_anexo"           : tem_anexo
                        });

                        fd.append('dados[]', JSON.stringify(dados));
                        if (tipo == "I") {
                            if (doc[0].files[0] != undefined) fd.append(doc[0].files[0].name, doc[0].files[0]);
                        }
                        //-----------------------------------



                    }
                }

            });

            if(msg_aviso_batida != ''){

                Swal.fire({
                    icon: 'question',
                    title: msg_aviso_batida,
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: `Sim, confirmar`,
                    denyButtonText: `Cancelar`,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowOutsideClick: false,
                    width: 600,
                }).then((result) => {
                    if (result.isConfirmed) {

                        openLoading();

                        $.ajax({
                            url: "<?= base_url('ponto/espelho/action/alterar_batida') ?>",
                            type: 'POST',
                            processData: false,
                            contentType: false,
                            data: fd,
                            success: function(result) {
                                openLoading(true);
																	 
								
																		
																   
												   
														
									 
						 

                                try {
                                    var response = JSON.parse(result);

                                    if (response.tipo != 'success') {
                                        exibeAlerta(response.tipo, response.msg);
                                    } else {
                                        exibeAlerta(response.tipo, response.msg, 3);
                                        var myTimeout = setTimeout(function() {
                                            consultarEspelho();
                                            clearTimeout(myTimeout);
                                        }, 2000);
                                    }

                                } catch (e) {
                                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                                }


                            },
                        });

                    }
                });

            }else{

                openLoading();
			   

                $.ajax({
                    url: "<?= base_url('ponto/espelho/action/alterar_batida') ?>",
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function(result) {

                        openLoading(true);

                        try {
                            var response = JSON.parse(result);

                            if (response.tipo != 'success') {
                                exibeAlerta(response.tipo, response.msg);
                            } else {
                                exibeAlerta(response.tipo, response.msg, 3);
                                var myTimeout = setTimeout(function() {
                                    consultarEspelho();
                                    clearTimeout(myTimeout);
                                }, 2000);
                            }

                        } catch (e) {
                            exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                        }


                    },
                });

            }

        }
        // cria nova linha para inclusão de nova batida
        const incluirNovaBatida = () => {
            
            if (!validaDados()) return;

            $("[data-just]").fadeOut(0);
            var id = Math.random();

            var html = '<tr data-p="I" data-info="' + id + '">' +
                '<td><input type="date" class="form-control form-control-sm" value="' + data_nova_batida + '"></td>' +
                '<td><input type="date" class="form-control form-control-sm" value="' + data_nova_batida + '"></td>' +
                '<td><input type="time" class="form-control form-control-sm" value=""></td>' +
                '<td><select class="form-control form-control-sm"><option value="0" ' + (natureza_proxima_batida != 1 ? 'selected' : '') + '>Entrada</option><option value="1" ' + (natureza_proxima_batida == 1 ? 'selected' : '') + '>Saida</option></select></td>' +
                '<td width="18" class="p-0"><button class="btn btn-danger btn-xxs pr-1 pl-1" data-remove-batida="' + id + '">X</button></td>' +
                '</tr>' +
                '<tr data-justificativa="' + id + '" data-just><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Motivo do ajuste:</label><select class="form-control form-control-sm"><option value="">...</option>'+select_justificativa_ajuste+'</td></tr>' + 
                '<tr data-observacao="' + id + '" data-obs><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Deseja incluir informações adicionais? </label><textarea class="form-control"></textarea></td></tr>';

                //'<tr data-justificativa="' + id + '" data-just><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Justificativa:</label><input type="text" maxlength="50" class="form-control mt-0"></td></tr>';

            $('.modal_alterar_batida tbody').append(html);

            // abre a justificativa da batida ao clicar na linha
            $("[data-info]").on('click', function(e) {
                $("[data-just]").fadeOut(0);
                $("[data-justificativa='" + $(this).attr('data-info') + "']").fadeIn(0);
                $("[data-obs]").fadeOut(0);
                $("[data-observacao='" + $(this).attr('data-info') + "']").fadeIn(0);
            });

            // remove a nova batida
            $("[data-remove-batida]").on('click', function(e) {
                $("[data-info='" + $(this).attr('data-remove-batida') + "']").remove();
                $("[data-justificativa='" + $(this).attr('data-remove-batida') + "']").remove();
                $("[data-observacao='" + $(this).attr('data-remove-batida') + "']").remove();
                verificaLimite();
                natureza_proxima_batida = (natureza_proxima_batida != 1) ? 1 : 0;
            });
            verificaLimite();

            natureza_proxima_batida = (natureza_proxima_batida != 1) ? 1 : 0;

        }
        //-----------------------------------------------------------
        // valida se todos os dados da batida nova foram preenchidos
        //-----------------------------------------------------------
        const validaDados = () => {

            msg_aviso_batida = '';
            msg_aviso_datas = '';

            $("[data-p]").each(function(e) {
                let tipo = $(this).attr('data-p');
                let id = $(this).attr('data-info');

                if (id != undefined) {
                    if (tipo == "I") {
                        var just = $("[data-justificativa='" + id + "'] select").val().trim();
                    }
                    let data = $("[data-info='" + id + "']").find('input,select')[0].value;
                    let data_ref = $("[data-info='" + id + "']").find('input,select')[1].value;
                    let batida = $("[data-info='" + id + "']").find('input,select')[2].value;
                    let batida_default = $("[data-info='" + id + "']").find('input,select')[2].getAttribute('data-default');
                    let natureza = $("[data-info='" + id + "']").find('input,select')[3].value;

                    $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm');
                    $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm');
                    $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm');
                    $("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm');

                    if (data == "") $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    if (data_ref == "") $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    if (batida == "") $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    if (natureza == "") $("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');

                    var a = new Date(data),
                        b = new Date(data_ref),
                        difference = dateDiffInDays(a, b);

                    if(difference < 0) difference = difference*-1;
                    if(difference > 2){
                        msg_aviso_datas = 'Diferença entre a data e data referência é superior a 2 dias.';
                        $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    }

                    if (tipo == "I") {

                        let Vinicio_escala  = parseInt(inicio_escala.replace(':', ''));
                        let Vtermino_escala = parseInt(termino_escala.replace(':', ''));

                        if(Vinicio_escala > Vtermino_escala) Vtermino_escala = Vtermino_escala + 2400;

                        let batidaInformada = parseInt(batida.replace(':', ''));

                        let batidaInformadaCheck = batidaInformada;

                        if(data_ref > data_nova_batida) batidaInformadaCheck = batidaInformadaCheck +2400;

                        if(batidaInformadaCheck < Vinicio_escala){
                            msg_aviso_batida = '<b>Registro</b> está fora de sua jornada.';
                        }
                        if(batidaInformadaCheck > Vtermino_escala){
                            msg_aviso_batida = '<b>Registro</b> está fora de sua jornada.';
                        }

                        if (just != undefined) {
                            if (just == "") {
                                $("[data-justificativa='" + id + "'] select").addClass("parsley-error text-danger form-control-sm");
                            } else {
                                $("[data-justificativa='" + id + "'] select").removeClass("parsley-error text-danger form-control-sm");
                            }
                        }
                    }
                }

            });

            if(msg_aviso_datas != ''){
                exibeAlerta('error', msg_aviso_datas);
                return false;
            }

            return ($(".modal_alterar_batida .parsley-error").length > 0) ? false : true;
        }
        
        // valida o horario preenchido
        const validaHorario = (batida) => {
            var horario = batida.split(':');
            var horas = parseInt(horario[0]);
            var minutos = parseInt(horario[1]);

            if (horas > 23) return false;
            if (minutos > 59) return false;

            return true;
        }
        // verifica a qtde maxima de batida permitida
        const verificaLimite = () => {
            let qtde = $(".modal_alterar_batida [type=time]").length;
            if (qtde >= 6) {
                $('[data-btn-add]').fadeOut(0);
            } else {
                $('[data-btn-add]').fadeIn(0);
            }
        }
        const dateDiffInDays = (a, b) => {
            const _MS_PER_DAY = 1000 * 60 * 60 * 24;
            // Discard the time and time-zone information.
            const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
            const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

            return Math.floor((utc2 - utc1) / _MS_PER_DAY);
        }
        //-----------------------------------------------------------
        // solicitar abono
        //-----------------------------------------------------------
        const abrirSolicitacaoAbono = (data, data_br, diasemana, abonos, tipo_ocorrencia, batidas, escala, periodo_bloqueado, inicioEscala, terminoEscala, faltas, atrasos) => {
            $(".modal_abono").modal('show');

            inicio_escala = inicioEscala;
            termino_escala = terminoEscala;
            qtde_falta = (faltas != '0:00') ? faltas : atrasos;
            
            // $(".modal_abono h3").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_abono [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_abono [data-h-escala]").html('Escala: '+escala);
            $(".modal_abono [data-h-batidas]").html('Registros existentes: ');
            var batidas = JSON.parse(decodeURIComponent(batidas));

            for (var x = 0; x < batidas.length; x++) {
                $(".modal_abono [data-h-batidas]").append(batidas[x].batida + ' | ');
            }
            
            
            $("[name=data_abono]").val(data);
            data_nova_batida = data;
            tipo_do_abono = parseInt(tipo_ocorrencia);

            $('.modal_abono tbody').html('');

            openLoading();

            $.ajax({
                url: "<?= base_url('ponto/espelho/action/listar_abonos') ?>",
                type: 'POST',
                data: {
                    dataPonto: data,
                    chapa: '<?= $chapa ?>'
                },
                success: function(response) {

                    openLoading(true);

                    
                    for (var x = 0; x < response.length; x++) {

                        var titulo_abono = '';

                        var select_abono = '<select class="form-control form-control-sm p-0 m-0" disabled>';
                        select_abono += '<option value="">...</option>';

                        for (var y = 0; y < lista_abonos.length; y++) {
                            select_abono += '<option data-atestado="' + lista_abonos[y].ATESTADOOBRIGATORIO + '" value="' + lista_abonos[y].CODIGO + '" ' + ((response[x].abn_codabono == lista_abonos[y].CODIGO) ? ' selected ' : '') + '>' + lista_abonos[y].DESCRICAO + ' - ' + lista_abonos[y].CODIGO + '</option>';

                            if(response[x].abn_codabono == lista_abonos[y].CODIGO) titulo_abono = ' data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'+lista_abonos[y].DESCRICAO+'" class="tippy-btn" ';

                        }

                        select_abono += '</select>';

                        var horaini = Math.floor(response[x].abn_horaini / 60);
                        var minini = response[x].abn_horaini % 60;
                        var horafim = Math.floor(response[x].abn_horafim / 60);
                        var minfim = response[x].abn_horafim % 60;

                        // Formatação das horas com dois dígitos
                        var horaInicioFormatada = horaini.toString().padStart(2, '0');
                        var minutoInicioFormatado = minini.toString().padStart(2, '0');
                        var horaFimFormatada = horafim.toString().padStart(2, '0');
                        var minutoFimFormatado = minfim.toString().padStart(2, '0');
                        
                        if (response[x].status == 'S') {
                            var html = '<tr data-abono="S" data-id-abono="' + Math.random() + '">' +
                                '<td '+titulo_abono+'>' + select_abono + '</td>' +
                                '<td><input disabled type="checkbox" data-parcial value="1"></td>' +
                                '<td><input disabled type="text" class="form-control form-control-sm" value=""></td>' +
                                '<td><input disabled data-inicio-default="' + response[x].abn_horaini + '" type="time" class="form-control form-control-sm p-0" value="' + horaInicioFormatada + ':' + minutoInicioFormatado + '"></td>' +
                                '<td><input disabled data-termino-default="' + response[x].abn_horafim + '" type="time" class="form-control form-control-sm p-0" value="' + horaFimFormatada + ':' + minutoFimFormatado + '"></td>';
                                //'<td><input disabled data-justificativa-default="' + (response[x].justificativa_abono_tipo || '') + '" type="text" maxlength="50" class="form-control form-control-sm" value="' + (response[x].justificativa_abono_tipo || '') + '"></td>';
                       
                            html += '<td width="1" class="p-0" '+titulo_abono+'><span class="badge badge-success" style="background: #04609d !important;">Sinc.</span></td>' +
                                '<td></td>'
                            '</tr>';
                            var exibe = <?= ($rh) ? 1 : 0; ?>;
                            if(response[x].nome_anexo != '' && response[x].nome_anexo != null && (periodo_bloqueado != 1 || exibe == 1)){
                                html += '<tr>';
                                html += '<td colspan="4"><label><i class="mdi mdi-arrow-up-bold"></i> Anexo:</label> <a href="<?= base_url('/ponto/preview/index'); ?>/'+response[x].id+'" target="_blank">'+response[x].nome_anexo+'</a></td>';
                                html += '</tr>';
                            }
                        }

                        $('.modal_abono tbody').append(html);
                        validaTotalHorasAbono(false);
                    }

                    if(periodo_bloqueado == 1){
                        $(".modal_abono .modal-body *").prop('disabled', true);
                        $(".modal_abono .modal-footer *").prop('disabled', true);
                    }else{
                        $(".modal_abono .modal-body button").prop('disabled', false);
                        $(".modal_abono .modal-body [type=file]").prop('disabled', false);
                        $(".modal_abono .modal-footer *").prop('disabled', false);
                    }
                },
                error: function(error) {
                    // Tratar erros, se necessário
                    console.log(error);
                }
            });

            var abonos = JSON.parse(decodeURIComponent(abonos));

            for (var x = 0; x < abonos.length; x++) {

                
                var titulo_abono = '';
                var select_abono = '<select class="form-control form-control-sm p-0 m-0" disabled>';
                select_abono += '<option value="">...</option>';
                for (var y = 0; y < lista_abonos.length; y++) {
                    
                    select_abono += '<option data-atestado="' + lista_abonos[y].ATESTADOOBRIGATORIO + '" value="' + lista_abonos[y].CODIGO + '" ' + ((abonos[x].codabono == lista_abonos[y].CODIGO) ? ' selected ' : '') + '>' + lista_abonos[y].DESCRICAO + ' - ' + lista_abonos[y].CODIGO + '</option>';

                    if(abonos[x].codabono == lista_abonos[y].CODIGO) titulo_abono = ' data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" aria-describedby="tippy-4" title="'+lista_abonos[y].DESCRICAO+'" ';

                }
                select_abono += '</select>';

                var html = '<tr data-abono="U" data-id-abono="' + Math.random() + '">' +
                    '<td '+titulo_abono+'>' + select_abono + '</td>' +
                    '<td><input disabled type="checkbox" data-parcial value="1"></td>' +
                    '<td><input disabled type="text" class="form-control form-control-sm" value=""></td>' +
                    '<td><input data-inicio-default="' + abonos[x].inicio + '" type="time" class="form-control p-0" value="' + abonos[x].inicio + '"></td>' +
                    '<td><input data-termino-default="' + abonos[x].termino + '" type="time" class="form-control p-0" value="' + abonos[x].termino + '"></td>';
                    //'<td><input data-justificativa-default="' + abonos[x].just_abono_tipo.replace(/\+/g, ' ') + '" type="text" maxlength="50" class="form-control" value="' + abonos[x].just_abono_tipo.replace(/\+/g, ' ') + '"></td>';
                if(parseInt(abonos[x].status) == 3){
                    html += '<td width="1" class="p-0"><span class="badge badge-danger" tippy-btn '+titulo_abono+'>Rep.</span></td>';
                    html += '<td><button class="btn btn-sm btn-danger btn-xxs pr-1 pl-1" onclick="return ExcluirAbonoPT(' + abonos[x].id + ')"><i class="mdi mdi-trash-can-outline"></i></button></td>';
                }else{
                    html += '<td width="1" class="p-0"><span class="badge badge-warning tippy-btn" '+titulo_abono+'>Sol.</span></td>';
                    html += '<td><button class="btn btn-sm btn-danger btn-xxs pr-1 pl-1" onclick="return ExcluirAbonoPT(' + abonos[x].id + ')"><i class="mdi mdi-trash-can-outline"></i></button></td>';
                }
                html += '</tr>';
                
                var exibe = <?= ($rh) ? 1 : 0; ?>;
                if(abonos[x].nome_anexo != '' && abonos[x].nome_anexo != null && (periodo_bloqueado != 1 || exibe == 1)){
                    html += '<tr>';
                    html += '<td colspan="4"><label><i class="mdi mdi-arrow-up-bold"></i> Anexo:</label> <a href="<?= base_url('/ponto/preview/index'); ?>/'+abonos[x].id+'" target="_blank">'+abonos[x].nome_anexo+'</a></td>';
                    html += '</tr>';
                }

                $('.modal_abono tbody').append(html);
                validaTotalHorasAbono(false);

                $("[data-abono] [type=time]").on('keyup', function(e) {
                    validaTotalHorasAbono(false);
                });

            }

        }

        const ExcluirAbonoPT = (id) => {
            Swal.fire({
                icon: 'question',
                title: 'Deseja realmente excluir o abono do Portal?',
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

                    try {

                        openLoading();

                        $.ajax({
                            url: "<?= base_url('ponto/espelho/action/excluir_abono_pt') ?>",
                            type: 'POST',
                            data: {
                                'id': id
                            },
                            success: function(result) {
                                openLoading(true);

                                var response = JSON.parse(result);

                                if (response.tipo != 'success') {
                                    exibeAlerta(response.tipo, response.msg);
                                } else {
                                    exibeAlerta(response.tipo, response.msg, 5);
                                    var myTimeout = setTimeout(function() {
                                        consultarEspelho();
                                        clearTimeout(myTimeout);
                                    }, 2000);
                                }

                            },
                        });

                    } catch (e) {
                        exibeAlerta('error', 'Erro interno: ' + e);

                    }
                }
            });

        }

        const ExcluirAbonoRM = (id) => {
            Swal.fire({
                icon: 'question',
                title: 'Deseja realmente excluir o abono do RM?',
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

                    try {

                        openLoading();

                        $.ajax({
                            url: "<?= base_url('ponto/espelho/action/excluir_abono_rm') ?>",
                            type: 'POST',
                            data: {
                                'id': id
                            },
                            success: function(result) {
                                openLoading(true);

                                var response = JSON.parse(result);

                                if (response.tipo != 'success') {
                                    exibeAlerta(response.tipo, response.msg);
                                } else {
                                    exibeAlerta(response.tipo, response.msg, 5);
                                    var myTimeout = setTimeout(function() {
                                        consultarEspelho();
                                        clearTimeout(myTimeout);
                                    }, 2000);
                                }

                            },
                        });

                    } catch (e) {
                        exibeAlerta('error', 'Erro interno: ' + e);

                    }
                }
            });

        }
        const incluirNovoAbono = () => {

            if (!validaDadosAbono()) return;

            var id = Math.random();

            var select_abono = '<select class="form-control form-control-sm p-0 m-0" data-cod-abono="' + id + '">';
            select_abono += '<option value="">...</option>';
            for (var x = 0; x < lista_abonos.length; x++) {
                select_abono += '<option data-atestado="' + lista_abonos[x].ATESTADOOBRIGATORIO + '" value="' + lista_abonos[x].CODIGO + '">' + lista_abonos[x].DESCRICAO + ' - ' + lista_abonos[x].CODIGO + '</option>';
            }
            select_abono += '</select>';

            var html = '<tr data-abono="I" data-id-abono="' + id + '">' +
                '<td>' + select_abono + '</td>' +
                '<td><input type="checkbox" data-parcial value="1"></td>' +
                '<td><input disabled type="text" class="form-control form-control-sm" value=""></td>' +
                '<td><input type="time" class="form-control form-control-sm" value=""></td>' +
                '<td><input type="time" class="form-control form-control-sm" value=""></td>' +
                //'<td><input type="text" maxlength="50" class="form-control form-control-sm" value=""></td>' +
                '<td width="18" class="p-0"><button class="btn btn-danger btn-xxs pr-1 pl-1" data-remove-abono="' + id + '">X</button></td>' +
                '</tr>' +
                '<tr data-anexo-atestado="' + id + '" class="hidden">' +
                '<td colspan="4" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Atestado:</label><input type="file" class="form-control" accept="image/png, image/jpeg, .pdf"></td>' +
                '</tr>';

            $('.modal_abono tbody').append(html);

            // remove a nova batida
            $("[data-remove-abono]").on('click', function(e) {
                $("[data-anexo-atestado='" + $(this).attr('data-remove-abono') + "']").remove();
                $(this).parent().parent().remove();
            });


            // horasInicio = parseInt(inicio_escala.replace(':', ''));
            // horasFalta = parseInt(qtde_falta.replace(':', ''));
            
            //------- correção horas
            var min_horasInicio = h2m(inicio_escala);
            var min_horasFalta  = h2m(qtde_falta);
            horasFinal = min_horasInicio + min_horasFalta;
            if(horasFinal > 1440) horasFinal = horasFinal - 1440;
            horasFinal = m2h(horasFinal);
            //------- correção horas
            
            $("[data-id-abono]:last").find('[type="time"]')[0].value=inicio_escala;
            $("[data-id-abono]:last").find('[type="time"]')[1].value=horasFinal;
            $("[data-id-abono]:last").find('[type="time"]').prop('disabled', true);
            $("[data-id-abono]:last").find('[type="time"]').css('visibility', 'hidden');

            $("[data-parcial]").on('click', function(){
                if($(this).prop('checked')){
                    $(this).parent().parent().find('[type=text]')[0].value='';
                    $(this).parent().parent().find('[type=time]')[0].value='';
                    $(this).parent().parent().find('[type=time]')[1].value='';
                    $(this).parent().parent().find('[type=time]').prop('disabled', false);
                    $(this).parent().parent().find('[type=time]').css('visibility', 'visible');
                }else{
                    // horasInicio = parseInt(inicio_escala.replace(':', ''));
                    // horasFalta = parseInt(qtde_falta.replace(':', ''));
                    // horasFinal = horasInicio + horasFalta;
                    // if(horasFinal > 2400) horasFinal = horasFinal-2400;
                    // horasFinal = horasFinal.toString().padStart(4, '0');
                    // horasFinal = horasFinal.toString().substr(0,2)+ ':' + horasFinal.toString().substr(-2);

                    //------- correção horas
                    var min_horasInicio = h2m(inicio_escala);
                    var min_horasFalta  = h2m(qtde_falta);
                    horasFinal = min_horasInicio + min_horasFalta;
                    if(horasFinal > 1440) horasFinal = horasFinal - 1440;
                    horasFinal = m2h(horasFinal);
                    //------- correção horas
                    
                    $(this).parent().parent().find('[type=time]')[0].value=inicio_escala;
                    $(this).parent().parent().find('[type=time]')[1].value=horasFinal;
                    $(this).parent().parent().find('[type=time]').css('visibility', 'hidden');
                    $(this).parent().parent().find('[type=time]').prop('disabled', true);
                    validaTotalHorasAbono(false);
                }
            });

            validaTotalHorasAbono(false);

            $("[data-cod-abono]").on('change', function(e) {

                var atestado = $(this).find(':selected').attr('data-atestado');
                var id_linha_abono = $(this).attr('data-cod-abono');

                $("[data-anexo-atestado='" + id_linha_abono + "'] input").val('');
                if (atestado == 1) {
                    $("[data-anexo-atestado='" + id_linha_abono + "']").fadeIn(0);
                } else {
                    $("[data-anexo-atestado='" + id_linha_abono + "']").fadeOut(0);
                }

            });

            $("[data-abono] [type=time]").on('keyup', function(e) {
                validaTotalHorasAbono(false);
            });

        }
        const solicitarAbono = () => {

            abono_noturno = false;

            if (!validaDadosAbono()) return;

            var fd = new FormData();
            var formatos = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];

            if ($("[data-abono]").find('input').length <= 0) {
                exibeAlerta('error', 'Nenhum abono informado.');
                return;
            }

            var msg_erro = "";
            $("[data-abono]").each(function(e) {

                let tipo = $(this).attr('data-abono');

                if (tipo == "I" || tipo == "U") {

                    var doc = $("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input');
                    var tem_anexo = 0;
                    if (tipo == "I") {
                        if (doc[0].files[0] != undefined) {
                            if (doc[0].files[0].size > 5242880) {
                                msg_erro = "<b>" + doc[0].files[0].name + "</b> Tamanho superior a 5MB.";
                            } else
                            if (!formatos.includes(doc[0].files[0].type)) {
                                msg_erro = "<b>" + doc[0].files[0].name + "</b> Formato do arquivo não permitido.";
                            } else {
                                var tem_anexo = 1;
                            }
                        }
                    }

                    var dados = [];

                    dados.push({
                        'data'           : data_nova_batida,
                        'tipo'           : tipo,
                        'codabono'       : $(this).find('select')[0].value,
                        'inicio'         : $(this).find('input')[2].value,
                        'termino'        : $(this).find('input')[3].value,
                        'just_abono_tipo': '',
                        //'just_abono_tipo': $(this).find('input')[2].value,
                        'inicio_default' : $(this).find('input')[2].getAttribute('data-inicio-default'),
                        'termino_default': $(this).find('input')[3].getAttribute('data-termino-default'),
                        'tem_anexo'      : tem_anexo,
                        "codfilial"      : '<?= ($resFuncionario[0]['CODFILIAL'] ?? 1) ?>',
                        "chapa"          : '<?= $chapa ?>',
                        "tipo_ocorrencia": tipo_do_abono
                    });

                    fd.append('dados[]', JSON.stringify(dados));
                    if (tipo == "I") {
                        if (doc[0].files[0] != undefined) fd.append(doc[0].files[0].name + '_' + e, doc[0].files[0]);
                    }
                }

            });

            if (msg_erro != "") {
                exibeAlerta('error', msg_erro);
                return;
            }
            
            openLoading();

            if (abono_noturno || msg_aviso) {
                
                Swal.fire({
                    icon: 'question',
                    title: ((msg_aviso == '') ? msg_noturno : msg_aviso),
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: `Sim, confirmar`,
                    denyButtonText: `Cancelar`,
                    showCancelButton: false,
                    showCloseButton: false,
                    allowOutsideClick: false,
                    width: 600,
                }).then((result) => {
                    if (result.isConfirmed) {

                        confirmaEnvioAbono(fd);

                    } else {
                        openLoading(true);
                    }
                });
            } else {
                confirmaEnvioAbono(fd);
            }

        }
        const calcularDiferencaHoras = (hora1, hora2) => {
            var minutos1 = horaParaMinutos(hora1);
            var minutos2 = horaParaMinutos(hora2);
            
            var diferencaMinutos = minutos2 - minutos1;
            
            var horas = Math.floor(diferencaMinutos / 60);
            var minutos = diferencaMinutos % 60;
            
            return pad(horas, 2) + ":" + pad(minutos, 2);
        }
        const horaParaMinutos = (hora) => {
            var partes = hora.split(":");
            return parseInt(partes[0]) * 60 + parseInt(partes[1]);
        }
        const confirmaEnvioAbono = (fd) => {

            $.ajax({
                url: "<?= base_url('ponto/espelho/action/cadastrar_abono') ?>",
                type: 'POST',
                processData: false,
                contentType: false,
                data: fd,
                success: function(result) {

                    openLoading(true);

                    try {
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3);
                            var myTimeout = setTimeout(function() {
                                consultarEspelho();
                                clearTimeout(myTimeout);
                            }, 2000);
                        }

                    } catch (e) {
                        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                    }


                },
            });

        }
        const pad = (str, length) => {
            const resto = length - String(str).length;
            return '0'.repeat(resto > 0 ? resto : '0') + str;
        }
        const validaDadosAbono = () => {

            msg_aviso = '';

            let qtde_abonos = $("[data-abono]").length;
            if (qtde_abonos > 1) {
                var novo_inicio = $("[data-abono]:last").find('input')[2].value;
                var novo_termino = $("[data-abono]:last").find('input')[3].value;
            }

            if(!validaTotalHorasAbono(true)){
                return false;
            }

            var msg_erro = "";

            $("[data-abono]").each(function(e) {

                let codabono = $(this).find('select')[0].value;
                let inicio = $(this).find('input')[2].value;
                let termino = $(this).find('input')[3].value;
                let inicioH = $(this).find('input')[2].value;
                let terminoH = $(this).find('input')[3].value;
                var atestado = $(this).find(':selected').attr('data-atestado');
                var tipo = $(this).attr('data-abono');

                if (inicio != '' && termino != '') {

                    let Vinicio_escala  = parseInt(inicio_escala.replace(':', ''));
                    let Vtermino_escala = parseInt(termino_escala.replace(':', ''));
                    if(Vinicio_escala > Vtermino_escala) Vtermino_escala = Vtermino_escala + 2400;
                    
                    inicio = parseInt(inicio.replace(':', ''));
                    termino = parseInt(termino.replace(':', ''));

                    if (inicio > 2359) {
                        msg_erro = '<b>Registro</b> não pode ser superior a <b>23:59</b>.';
                        return false;
                    }
                    if (termino > 2359) {
                        msg_erro = '<b>Registro</b> não pode ser superior a <b>23:59</b>.';
                        return false;
                    }
                    
                    if(inicio < Vinicio_escala){
                        // msg_erro = '<b>Hora de início</b> esta fora de sua jornada.';
                        msg_aviso = '<b>Hora de início do abono</b> esta fora de sua jornada.';
                        // exibeAlerta('warning', '<b>Hora de início do abono</b> esta fora de sua jornada.');
                        // return false;
                    }
                    if(inicio > Vtermino_escala){
                        // msg_erro = '<b>Hora de início</b> esta fora de sua jornada.';
                        // exibeAlerta('warning', '<b>Hora de início do abono</b> esta fora de sua jornada.');
                        msg_aviso = '<b>Hora de início do abono</b> esta fora de sua jornada. 2';
                        // return false;
                    }
                    if (termino < inicio) {
                        if (data_nova_batida != "") {
                            var dataIni = new Date(parseInt(data_nova_batida.slice(0, 4)), parseInt(data_nova_batida.slice(5, 7)) - 1, parseInt(data_nova_batida.slice(8)));
                            var dataFim = new Date(parseInt(data_nova_batida.slice(0, 4)), parseInt(data_nova_batida.slice(5, 7)) - 1, parseInt(data_nova_batida.slice(8)));
                            dataFim.setDate(dataFim.getDate() + 1);
                            msg_noturno = "<b>Hora término " + terminoH + "</b> esta menor que a <b>hora inicio " + inicioH + "</b>, sera gerado um abono do dia <b>" + pad(dataIni.getDate(), 2) + "/" + pad(dataIni.getMonth() + 1, 2) + "</b> até <b>" + pad(dataFim.getDate(), 2) + "/" + pad(dataFim.getMonth() + 1, 2) + "</b>, confirma solicitação?";
                        }
                        abono_noturno = true;

                        if(termino +2400 > Vtermino_escala){
                            // msg_erro = '<b>Hora de término do abono não pode ser maior que o término da escala.';
                            // exibeAlerta('warning', '<b>Hora de término do abono</b> esta fora de sua jornada.');
                            msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
                            // return false;
                        }
                        if(termino +2400 < Vinicio_escala){
                            // exibeAlerta('warning', '<b>Hora de término do abono</b> esta fora de sua jornada.');
                            msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
                            // return false;
                        }
                    }else{

                        if(termino > Vtermino_escala){
                            // msg_erro = '<b>Hora de término do abono não pode ser maior que o término da escala.';
                            // exibeAlerta('warning', '<b>Hora de término do abono</b> esta fora de sua jornada.');
                            msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
                            // return false;
                        }
                        if(termino < Vinicio_escala){
                            // exibeAlerta('warning', '<b>Hora de término do abono</b> esta fora de sua jornada.');
                            msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
                            // return false;
                        }

                    }
                    if (termino == inicio) {
                        msg_erro = '<b>Inicio</b> não pode ser igual ao <b>Término</b>.';
                        return false;
                    }
                }

                if (qtde_abonos > 1 && qtde_abonos != (e + 1)) {
                    if (novo_inicio != '' && novo_termino != '') {
                        novo_inicio_minuto = parseInt(novo_inicio.replace(':', ''));
                        novo_termino_minuto = parseInt(novo_termino.replace(':', ''));

                        if (novo_inicio_minuto >= inicio && novo_inicio_minuto <= termino) {
                            $("[data-abono]:last").find('[type=time]')[0].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            msg_erro = '<b>Inicio</b> informado esta em interseção com outro horário de abono.';
                            return false;
                        }
                        if (novo_termino_minuto >= inicio && novo_termino_minuto <= termino) {
                            $("[data-abono]:last").find('[type=time]')[0].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            msg_erro = '<b>Término</b> informado esta em interseção com outro horário de abono.';
                            return false;
                        }
                        if (novo_inicio_minuto <= inicio && novo_termino_minuto >= termino) {
                            $("[data-abono]:last").find('[type=time]')[1].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            $("[data-abono]:last").find('[type=time]')[1].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            msg_erro = '<b>Inicio</b> e <b>Término</b> informado esta em interseção com outro horário de abono.';
                            return false;
                        }
                    }
                }

                $(this).find('select')[0].setAttribute('class', (codabono != '') ? 'form-control form-control-sm p-0 m-0' : 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                $(this).find('[type=time]')[0].setAttribute('class', (typeof inicio != "string") ? 'form-control form-control-sm p-0 m-0' : 'form-control parsley-error text-danger');
                $(this).find('[type=time]')[1].setAttribute('class', (typeof termino != "string") ? 'form-control form-control-sm p-0 m-0' : 'form-control parsley-error text-danger');
                if (atestado == 1 && tipo == "I") {
                    if ($("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input')[0].value == "") {
                        $("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input')[0].setAttribute('class', 'form-control parsley-error text-danger');
                    } else {
                        $("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input')[0].setAttribute('class', 'form-control');
                    }
                }

            });

            if (msg_erro != "") {
                exibeAlerta('error', msg_erro);
                return false;
            }

            return ($(".modal_abono .parsley-error").length > 0) ? false : true;

        }

        const validaTotalHorasAbono = (alerta = true) => {

            var horasTotal  = 0;
            var qtdeHoras = h2m(qtde_falta);
            var horasAbono  = 0;
            $("[data-abono]").each(function(e) {

                let inicio = $(this).find('input')[2].value;
                let termino = $(this).find('input')[3].value;
                var tipo = $(this).attr('data-abono');
                

                if (inicio != '' && termino != '') {

                    // novo_inicio  = parseInt(inicio.replace(':', ''));
                    // novo_termino = parseInt(termino.replace(':', ''));

                    // if(novo_inicio > novo_termino) novo_termino = novo_termino+2400;

                    // let horaInicio = (novo_inicio);
                    // horaInicio = horaInicio.toString().padStart(4, '0');
                    // horaInicio = horaInicio.toString().substr(0,2)+ ':' + horaInicio.toString().substr(-2);

                    // let horaFim = (novo_termino);
                    // horaFim = horaFim.toString().padStart(4, '0');
                    // horaFim = horaFim.toString().substr(0,2)+ ':' + horaFim.toString().substr(-2);
                    // // $(this).find('input')[1].value = horasFinal;
                    // var horasDiff = calcularDiferencaHoras(horaInicio, horaFim);
                    // $(this).find('input')[1].value = horasDiff;
                    
                    // if(qtde_falta.toString().padStart(5, '0') == horasDiff.toString().padStart(5, '0')){
                    //     $(this).find('input')[2].setAttribute('style', 'visibility:hidden');
                    //     $(this).find('input')[3].setAttribute('style', 'visibility:hidden');
                    // }

                    novo_inicio = h2m(inicio);
                    novo_termino = h2m(termino);

                    if(novo_inicio > novo_termino) novo_termino = novo_termino+1440;
                    var horasDiff = novo_termino - novo_inicio;
                    
                    $(this).find('input')[1].value = m2h(horasDiff);

                    if(qtdeHoras == horasDiff){
                        $(this).find('input')[2].setAttribute('style', 'visibility:hidden');
                        $(this).find('input')[3].setAttribute('style', 'visibility:hidden');
                    }

                    horasTotal += (novo_termino - novo_inicio);
                }

            });

            horasAbono = horasTotal;
            if(horasTotal > qtdeHoras){
                if(alerta) exibeAlerta('error', '<b>Horas totais de abono</b> não pode ser superior ao limite de '+qtde_falta);
                return false;
            }

            return true;

        }

        //-----------------------------------------------------------
        // resumo diario
        //-----------------------------------------------------------
        const abrirResumoDiario = (data, diasemana, horas_trabalhadas, atrasos, faltas, extras, abono, batidas, escala, total_refeicao, total_direcao, total_espera, total_parado, adicional_noturno, suspensao) => {

            $(".modal_resumo_diario").modal('show');
            $(".modal_resumo_diario [data-h-data]").html(data + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_resumo_diario [data-h-escala]").html('Escala: '+escala);
            $(".modal_resumo_diario [data-h-batidas]").html('Registros existentes: ');
            var batidas = JSON.parse(decodeURIComponent(batidas));

            for (var x = 0; x < batidas.length; x++) {
                $(".modal_resumo_diario [data-h-batidas]").append(batidas[x].batida + ' | ');
            }
            $('.modal_resumo_diario tbody').html('');

            if (horas_trabalhadas != '') $('.modal_resumo_diario tbody').append('<tr><td>Horas trabalhadas</td><td class="text-rigth text-primary">' + horas_trabalhadas + '</td></tr>');
            $('.modal_resumo_diario tbody').append('<tr><td>Atraso</td><td class="text-rigth text-danger">' + atrasos + '</td></tr>');
            $('.modal_resumo_diario tbody').append('<tr><td>Falta</td><td class="text-rigth text-danger">' + faltas + '</td></tr>');
            $('.modal_resumo_diario tbody').append('<tr><td>Total Extra</td><td class="text-rigth text-success">' + extras + '</td></tr>');
            $('.modal_resumo_diario tbody').append('<tr><td>Abono</td><td class="text-rigth text-success">' + abono + '</td></tr>');
            $('.modal_resumo_diario tbody').append('<tr><td>Adicional Noturno</td><td class="text-rigth text-info">' + adicional_noturno + '</td></tr>');
            if(suspensao != '0:00') $('.modal_resumo_diario tbody').append('<tr><td>Horas Suspensão</td><td class="text-rigth text-danger">' + suspensao + '</td></tr>');
            
            <?php if($is_motorista): ?>
            $('.modal_resumo_diario tbody').append('<tr><td>Total Hrs Refeição</td><td class="text-rigth text-info">' + total_refeicao + '</td></tr>');
            $('.modal_resumo_diario tbody').append('<tr><td>Total Hrs Direção</td><td class="text-rigth text-info">' + total_direcao + '</td></tr>');
            $('.modal_resumo_diario tbody').append('<tr><td>Total Hrs Espera</td><td class="text-rigth text-info">' + total_espera + '</td></tr>');
			$('.modal_resumo_diario tbody').append('<tr><td>Total Hrs Parado</td><td class="text-rigth text-info">' + total_parado + '</td></tr>');
																																						 
            <?php endif; ?>

        }
        //-----------------------------------------------------------
        // justificativa de extra
        //-----------------------------------------------------------
        const abrirJustificativaExtra = (data, data_br, diasemana, codigoJustificativa, batidas, escala, periodo_bloqueado) => {
            $(".modal_justificativa_extra").modal('show');
            
            $(".modal_justificativa_extra [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_justificativa_extra [data-h-escala]").html('Escala: '+escala);
            $(".modal_justificativa_extra [data-h-batidas]").html('Registros existentes: ');
            var batidas = JSON.parse(decodeURIComponent(batidas));

            for (var x = 0; x < batidas.length; x++) {
                $(".modal_justificativa_extra [data-h-batidas]").append(batidas[x].batida + ' | ');
            }
            data_nova_batida = data;
            $("#justificativa_extra").val(codigoJustificativa);

            if(periodo_bloqueado == 1){
                $(".modal_justificativa_extra .modal-body *").prop('disabled', true);
                $(".modal_justificativa_extra .modal-footer *").prop('disabled', true);
            }else{
                $(".modal_justificativa_extra .modal-body *").prop('disabled', false);
                $(".modal_justificativa_extra .modal-footer *").prop('disabled', false);
            }

        }
        const justificarExtra = () => {

            try {

                let dados = {
                    "justificativa" : $("#justificativa_extra").val(),
                    "chapa"         : '<?= $chapa ?>',
                    "data"          : data_nova_batida
                }

                if(dados.justificativa == ""){exibeAlerta('error', '<b>Justificativa de Extra</b> não informado.'); return false; }

                // openLoading();

                // Pergunta se quer adicionar observação
                Swal.fire({
                    title: 'Deseja incluir informações adicionais?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                    customClass: {
                        title: 'swal2-title'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        abrirModalObservacao(dados, enviarJustificativaExtraComObservacao);
                    } else {
                        enviarJustificativaExtraComObservacao(dados);
                    }
                });

                function enviarJustificativaExtraComObservacao(dados) {
                    openLoading();
                    $.ajax({
                        url: "<?= base_url('ponto/espelho/action/cadastrar_justificativa_extra') ?>",
                        type: 'POST',
                        data: dados,
                        success: function(result) {
                            openLoading(true);
                            try {
                                var response = JSON.parse(result);
                                if (response.tipo != 'success') {
                                    exibeAlerta(response.tipo, response.msg);
                                } else {
                                    exibeAlerta(response.tipo, response.msg, 3);
                                    setTimeout(function() {
                                        consultarEspelho();
                                    }, 2000);
                                }
                            } catch (e) {
                                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                            }
                        },
                    });
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        }

        let dadosObservacao = null;
        let callbackObservacao = null;

        function abrirModalObservacao(dados, callback) {
            dadosObservacao = dados;
            callbackObservacao = callback;
            $("#campoObservacao").val('');
            $("#modalObservacao").modal('show');
        }

        // Ao clicar em salvar no modal
        $("#btnSalvarObservacao").off('click').on('click', function() {
            if (callbackObservacao) {
                dadosObservacao.obs = $("#campoObservacao").val();
                $("#modalObservacao").modal('hide');
                callbackObservacao(dadosObservacao);
            }
        });

        <?php endif; // if(!$periodo_bloqueado || $rh || 1==1): ?>

    <?php endif; //if($resDiasEspelho && $chapa): 
    ?>

function h2m(hora) {
    // Divide a string de hora em horas e minutos
    var partes = hora.split(':');
    // Converte as partes para números inteiros
    var horas = parseInt(partes[0]);
    var minutos = parseInt(partes[1]);
    // Calcula o total de minutos
    var totalMinutos = horas * 60 + minutos;
    return totalMinutos;
}
function m2h(minutos) {
    // Calcula as horas e os minutos
    var horas = Math.floor(minutos / 60);
    var minutosRestantes = minutos % 60;
    
    // Formata as horas e os minutos para terem dois dígitos
    var horasFormatadas = horas < 10 ? '0' + horas : horas;
    var minutosFormatados = minutosRestantes < 10 ? '0' + minutosRestantes : minutosRestantes;
    
    // Retorna a hora formatada
    return horasFormatadas + ':' + minutosFormatados;
}
</script>
<style>
    .ht {
        margin-top: -1px !important;
    }

    .modal_incluir_batida,
    .modal_alterar_batida,
    .modal_abono {
        padding: 0 !important;
    }

    .modal-dialog-full {
        width: 100%;
        height: 100%;
        max-width: 900px;
        margin: auto;
        padding: 0;
    }

    .modal-content-full {
        height: auto;
        min-height: 100%;
        border-radius: 0;
    }

	.swal2-popup .swal2-title {
		background: #1ecab8; /* ou a cor do seu header, ex: var(--success) */
		color: #fff;
		padding: 12px 24px;
		border-top-left-radius: 2px;
		border-top-right-radius: 2px;
		font-size: 16px;
		text-align: left;
	}
    
	#modalObservacao .modal-title {
		color: #fff;
	}
</style>

<!-- modal modal_incluir_batida -->
<div class="modal modal_incluir_batida" tabindex="-1" role="dialog" aria-labelledby="modal_incluir_batida" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-primary">
                <h5 class="modal-title mt-0 text-white"><i class="fas fa-plus-circle"></i> Inclusão de Registro</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-6">
                        <div class="form-group row mb-1">
                            <label for="data_batida" class="col-sm-12 col-form-label text-left pb-1">Data registro:</label>
                            <div class="col-sm-12">
                                <input type="date" name="data_batida" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group row mb-1">
                            <label for="referencia_batida" class="col-sm-12 col-form-label text-left pb-1">Data referencia:</label>
                            <div class="col-sm-12">
                                <input type="date" name="referencia_batida" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group row mb-1">
                            <label for="horario_batida" class="col-sm-12 col-form-label text-left pb-1">Registro:</label>
                            <div class="col-sm-12">
                                <input type="time" name="horario_batida" class="form-control" placeholder="__:__">
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group row mb-1">
                            <label for="horario_batida" class="col-sm-12 col-form-label text-left pb-1">Natureza:</label>
                            <div class="col-sm-12">
                                <select name="natureza_batida" class="form-control">
                                    <option value="0">Entrada</option>
                                    <option value="1">Saida</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row mb-1">
                            <label for="data_batida" class="col-sm-12 col-form-label text-left pb-1">Motivo do ajuste:</label>
                            <div class="col-sm-12">
                                <input type="text" name="justificativa_batida" class="form-control" maxlength="50">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="return cadastraBatida()">Incluir Registro <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_incluir_batida -->

<!-- modal modal_alterar_batida -->
<div class="modal modal_alterar_batida" tabindex="-1" role="dialog" aria-labelledby="modal_alterar_batida" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-info">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-pencil-outline"></i> Incluir/Alterar Registro</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <?php if($is_motorista): ?><button class="btn btn-primary btn-sm float-right" type="button" onclick="carregaMacro()" id="btnConsultaMacro"><i class="fa fa-cogs"></i> Consulta Macros</button><?php endif; ?>
                        <h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						            <h6 data-h-batidas class="h7 m-0"></h6>
                        <p class="m-0">
					                <b>Legenda:</b> 
                          <i title="Registro Coletado" class="mdi mdi-alpha-c-box-outline ml-3" style="font-size:20px; color:rgb(43, 41, 41);"></i> Coletado
                          <i title="Registro Digitado" class="mdi mdi-alpha-d-box-outline ml-3" style="font-size:20px; color:rgb(43, 41, 41);"></i> Digitado 
                        </p>
                        <table class="table table-sm tablebatida">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Data referencia</th>
                                    <th class="colbatida">Registro</th>
                                    <th>Natureza</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row mb-2">
                            <div class="col-12">
                                <label><i class="fa fa-paperclip" aria-hidden="true"></i> Anexo:</label>
                                <input type="file" name="anexo_batida" class="form-control">
                            </div>
                        </div>

                        <button data-btn-add class="btn btn-outline-primary waves-effect waves-light btn-block" type="button" onclick="incluirNovaBatida()"><i class="mdi mdi-plus-circle-outline"></i> Adicionar registro</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="return alterarBatida()">Salvar Registro <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_incluir_batida -->

<!-- modal modal_resumo_diario -->
<div class="modal modal_resumo_diario" tabindex="-1" role="dialog" aria-labelledby="modal_resumo_diario" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Resumo Diário</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <form action="" method="post" enctype="multipart/form-data">
                            <table class="table" style="font-size: 16px;">
                                <thead>
                                    <tr>
                                        <th>Ocorrências</th>
                                        <th width="60" class="text-rigth">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_incluir_batida -->

<!-- modal modal_abono -->
<div class="modal modal_abono" tabindex="-1" role="dialog" aria-labelledby="modal_abono" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-timer-off"></i> Solicitar Abono</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body pl-0 pr-0 ml-0 mr-0">

                <div class="row p-0 m-0">
                    <div class="col-12">
                        
                        <h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tipo de Abono</th>
                                    <th>Parcial</th>
                                    <th width="80">Horas</th>
                                    <th>Inicio Abono</th>
                                    <th>Término Abono</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <button data-btn-add class="btn btn-outline-success waves-effect waves-light btn-block" type="button" onclick="incluirNovoAbono()"><i class="mdi mdi-plus-circle-outline"></i> Adicionar abono</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="return solicitarAbono()">Enviar Solicitação de Abono <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_abono -->

<!-- modal modal_justificativa_extra -->
<div class="modal modal_justificativa_extra" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_justificativa_extra" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-comment-check-outline"></i> Justificativa de Extra</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <hr>
                        <div class="form-group row mb-1">
                            <label for="data_batida" class="col-sm-3 col-form-label text-left-sm text-right pb-1">Justificativa:</label>
                            <div class="col-sm-9">
                                <select id="justificativa_extra" name="justificativa_extra" class="form-control">
                                    <option value="">...</option>
                                    <?php if($resJustificativaExtra): ?>
                                        <?php foreach($resJustificativaExtra as $key => $Justificativa): ?>
                                            <option value="<?= $Justificativa['id']; ?>"><?= $Justificativa['descricao']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="return justificarExtra()">Salvar<i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_justificativa_extra -->

<!-- modal modal_macro -->
<div class="modal modal_macro" tabindex="-1" role="dialog" aria-labelledby="modal_macro" aria-hidden="true" data-keyboard="false" data-backdrop="static" style="width: auto !important;">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="fa fa-cogs"></i> Consulta Macro</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        <h3 class="m-1 text-right" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <span><button type="button" id="dia_anterior" class="text-primary" style="border: none; background: none;"><i class="fa fa-arrow-left"></i></button></span>
                            <span><div id="data_macro">-</div></span>
                            <span><button id="dia_posterior" type="button" class="text-primary" style="border: none; background: none;"><i class="fa fa-arrow-right"></i></button></span>
                        </h3>
                        <?php if( $resFuncionario and $resFuncionario[0]['CHAPA'] ) { ?>
                          <h3 class="m-0"><?= $resFuncionario[0]['CHAPA'].' - '.$resFuncionario[0]['NOME']; ?></h3>
                        <?php } else { ?>
                          <h3 class="m-0"> </h3>
                        <?php } ?>
                        <h5 data-h-escala class="h7 mt-0"></h5>

                        <table class="table table-sm" id="table_macro">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Placa</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Tempo</th>
                                    <th>Origem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
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
<!-- modal modal_macro -->
<!-- Modal Observação -->
<div class="modal fade" id="modalObservacao" tabindex="-1" role="dialog" aria-labelledby="modalObservacaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalObservacaoLabel">Adicionar Observação</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <textarea id="campoObservacao" class="form-control" rows="4" placeholder="Digite sua observação aqui..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="btnSalvarObservacao">Salvar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Observação -->
<script>
    $(function() {
        $(".modal").draggable();
    });

const carregaMacro = (data_macro = data_nova_batida ) => {

console.log(data_macro);
const dateString = data_macro;
const { nextDate, previousDate } = getNextAndPreviousDates(dateString);
console.log(previousDate, nextDate);

const [year, month, day] = dateString.split("-");
$('#data_macro').html(`${day}/${month}/${year}`);

$('#table_macro tbody').html('');
$('#dia_anterior').attr("onClick","carregaMacro('"+previousDate+"')");
$('#dia_posterior').attr("onClick","carregaMacro('"+nextDate+"')");

$.ajax({
    url: "<?= base_url('ponto/espelho/action/carrega_macro') ?>",
    type: 'POST',
    data: {
        "chapa"           : '<?= $chapa ?>',
        "cpf"             : '<?= $resFuncionario[0]['CPF'] ?? ''; ?>',
        "data"            : data_macro,
        "inicio_escala"   : inicio_escala,
        "termino_escala"  : termino_escala
    },
    success: function(result) {

        openLoading(true);

        try {
            var response = JSON.parse(result);
            response.map(function(ponto, i){
                $('#table_macro tbody').append(`
                    <tr>
                        <td>${ponto.status}</td>
                        <td>${ponto.placa}</td>
                        <td>${ponto.data_inicio_status}</td>
                        <td>${ponto.data_fim_status}</td>
                        <td>${ponto.tempo}</td>
                        <td>${ponto.tipo_mensagem}</td>
                    </tr>
                `);
            });
            

        } catch (e) {
            exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

    },
});

$('.modal_macro').modal('show');
}
const carregaFuncionariosSecao = (codSecao) => {

    openLoading();

    $("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');
    if (codSecao == '') {
        // openLoading(true);
        // return false;
    }

    $.ajax({
        url: "<?= base_url('ponto/espelho/action/listar_funcionarios_secao') ?>",
        type: 'POST',
        data: {
            'codsecao': codSecao
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);
                $("#funcionario").html('<option value="">-- Todos (' + response.length + ') --</option>').trigger('change');

                for (var x = 0; x < response.length; x++) {
                    $("#funcionario").append('<option value="' + response[x].CHAPA + '">' + response[x].NOME + ' - ' + response[x].CHAPA + '</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const carregaColaboradores = () => {

    openLoading();

    var periodo = $("#periodo").val();
    if(periodo == ''){
        exibeAlerta('warning', 'Período não selecionado.');
        return;
    }

    $("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');

    $.ajax({
        url: "<?= base_url('ponto/espelho/action/carrega_colaboradores') ?>",
        type: 'POST',
        data: {
            'codsecao'    : $("#secao").val() ?? null,
            'periodo'     : periodo
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);
                $("#funcionario").html('<option value="">-- Todos (' + response.length + ') --</option>').trigger('change');

                for (var x = 0; x < response.length; x++) {
                    $("#funcionario").append('<option value="' + response[x].CHAPA + '">' + response[x].NOME + ' - ' + response[x].CHAPA + '</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}

$(document).ready(function() {
    var header = $("#tabela_ponto thead");
    var sticky = header.offset().top;

    $(window).scroll(function() {
        if ($(window).scrollTop() > sticky) {
            header.css('top', $(window).scrollTop()-sticky +70);
            header.addClass("sticky-header");
        } else {
            header.css('top', 0);
            header.removeClass("sticky-header");
        }
    });
    $(window).resize(function(){
        header = $("#tabela_ponto thead");
        sticky = header.offset().top;
    });
});

function getNextAndPreviousDates(dateString) {
    // Parse the input date string
    const date = new Date(dateString);

    // Ensure the date is valid
    if (isNaN(date.getTime())) {
        throw new Error("Invalid date format. Please use 'yyyy-mm-dd'.");
    }

    // Get the next date
    const nextDate = new Date(date);
    nextDate.setDate(date.getDate() + 1);

    // Get the previous date
    const previousDate = new Date(date);
    previousDate.setDate(date.getDate() - 1);

    // Format the dates back to 'yyyy-mm-dd'
    const formatDate = (d) => d.toISOString().split('T')[0];

    return {
        nextDate: formatDate(nextDate),
        previousDate: formatDate(previousDate)
    };
}

</script>
<script src="<?= base_url('public/assets/js/jquery-3.6.0.min.js'); ?>"></script>
<script src="<?= base_url('public/assets/js/jquery-ui.js'); ?>"></script>

<?php
loadPlugin(array('select2', 'mask', 'tooltips'))
?>
<?php endif; ?>
<?php if($impressao): ?>
   <br>
   <br>
   <br>
<br>
<b>Data:</b> ______ / ______ / ____________ 
<div style="padding-left: 50px; text-align: center;display:inline-block;">__________________________________________________________________<br><?= $resFuncionario[0]['NOME']; ?></div>
<?php endif; ?>

<style>
    @media (max-width: 500px){
        .modal_alterar_batida [data-info] [type="date"] {
            width: 90px !important;
            font-size: 12px !important;
            padding-left:0 !important;
            padding-right: 0 !important;
        }
        .modal_alterar_batida [data-info] [type="time"],
        .modal_alterar_batida [data-info] select {
            font-size: 12px !important;
            padding-left:0 !important;
            padding-right: 0 !important
        }
        .tablebatida td {
            padding: 1px !important;
        }
        .colbatida {
            width: 98px;
        }
    }
</style>