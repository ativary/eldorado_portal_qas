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
    border:1px solid #000000;
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
</style>
<table width="100%">
    <tr>
        <td width="150"><img width="150" src="<?= $_SERVER['DOCUMENT_ROOT'].'/public/assets/images/logo-dark.png'; ?>"></td>
        <td align="center"><h3>Espelho de Ponto</h3><span><b>Período:</b> <?= substr($periodo, 0, 10); ?> à <?= substr($periodo, -10); ?></span></td>
    </tr>
</table>
<table width="100%">
    <tr>
        <td width="50%"><strong>Nome:</strong> <?= $resFuncionario[0]['NOME']; ?></td>
        <td width="50%"><strong>Função: </strong> <?php echo $resFuncionario[0]['NOMEFUNCAO'] ?></td>
    </tr>
    <tr>
        <td width="50%"><strong>Seção: </strong> <?php echo $resFuncionario[0]['CODSECAO'] . ' - ' . $resFuncionario[0]['NOMESECAO'] ?></td>
        <td width="50%"><strong>Horário:</strong> <?php echo $resFuncionario[0]['NOMEHORARIO'] ?></td>
    </tr>
</table>  
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

                        <?php if ($isGestorOrLider) : ?>
                            <div class="row">
                                <label for="secao" class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
                                <div class="col-sm-10">
                                    <select class="select2 custom-select form-control form-control-sm" name="secao" id="secao" onchange="carregaFuncionariosSecao(this.value)">
                                        <option value="">-- Todas --</option>
                                        <?php if($resSecaoGestor): ?>
                                            <?php foreach($resSecaoGestor as $key => $SecaoGestor): ?>
                                                <option value="<?= $SecaoGestor['CODIGO']; ?>"><?= $SecaoGestor['CODIGO'].' - '.$SecaoGestor['DESCRICAO']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
                                <div class="col-sm-10">
                                    <select class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">- selecione o funcionário -</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($chapa == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] . ' - ' . $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <label for="periodo" class="col-sm-2 col-form-label text-right text-left-sm">Período:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo">
                                    <option value="">- selecione um período -</option>
                                    <?php if ($resPeriodo) : ?>
                                        <?php foreach($resPeriodo as $key => $Periodo): ?>
                                        <option value="<?= dtBr($Periodo['INICIOMENSAL']) . dtBr($Periodo['FIMMENSAL']). $Periodo['STATUSPERIODO']; ?>" <?= ($periodo == dtBr($Periodo['INICIOMENSAL']) . dtBr($Periodo['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($Periodo['INICIOMENSAL']) . ' à ' . dtBr($Periodo['FIMMENSAL']); ?></option>                                        
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return consultarEspelho()"><i class="fas fa-search"></i> Consultar</button>
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
                            <li class="list-group-item border-0 m-0 p-0"><strong>Horário: </strong><?php echo $resFuncionario[0]['NOMEHORARIO'] ?></li>
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
                                            <p class="<?= ($HorasPositivas > 0) ? 'text-success' : 'text-danger'; ?> mb-0" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_anterior">  </p>
                                            <p class="<?= ($HorasPositivas > 0) ? 'text-success' : 'text-danger'; ?> mb-0">Saldo Anterior</p>
                                        </div>
                                        <div>
                                            <p class="text-success mb-0" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_mes_positivo"> </p>
                                            <p class="text-success mb-0">Saldo Mês Positivo</p>
                                        </div>
                                        <div>
                                            <p class="text-danger mb-0" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_mes_negativo"> </p>
                                            <p class="text-danger mb-0">Saldo Mês Negativo</p>
                                        </div>
                                        <div>
                                            <p class="<?= m2h($SaldoTotal) > 0 ? 'text-success mb-0' : 'text-danger mb-0' ?>" style="font-weight: 700;font-size: 170%;text-align: center;" id="h_total"> </p>
                                            <p class="<?= m2h($SaldoTotal) > 0 ? 'text-success mb-0' : 'text-danger mb-0' ?>">Saldo total</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="m-0">
                            <b>Legenda:</b> 
                            <i title="Aguardando Aprovação Gestor" class="mdi mdi-square ml-3" style="font-size:20px; color: #ace5ff;"></i> Pendente Aprovação
                            <i title="Aprovado Gestor" class="mdi mdi-square ml-3" style="font-size:20px; color: #acffcd;"></i> Pendente Aprovação RH
                            <i title="Ação Reprovada" class="mdi mdi-square ml-3" style="font-size:20px; color: #fe0200;"></i> Ação Reprovada
                            <i title="Necessária Ação" class="mdi mdi-square ml-3" style="font-size:20px; color: #fff08f;"></i> Necessária Ação
                            <i title="Justificar extra" class="mdi mdi-square ml-3" style="font-size:20px; color: #f9d399;"></i> Justificar extra
                        </p>
                        <?php endif; ?>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0 table-centered table-sm ">
                                <thead>
                                    <tr>
                                        <th class="text-center n-mobile-cell" width="100">Data</th>
                                        <th class="text-center n-mobile-cell" width="90">Dia. Sem.</th>
                                        <th class="text-center n-mobile-cell">Ent</th>
                                        <th class="text-center n-mobile-cell">Sai</th>
                                        <th class="text-center n-mobile-cell">Ent</th>
                                        <th class="text-center n-mobile-cell">Sai</th>
                                        <th class="text-center n-mobile-cell">Ent</th>
                                        <th class="text-center n-mobile-cell">Sai</th>
                                        <th class="text-center n-mobile-cell">Ent</th>
                                        <th class="text-center n-mobile-cell">Sai</th>
                                        <th class="text-center n-mobile-cell">Hr. Trab.</th>
                                        <th class="text-center n-mobile-cell">Extra 1° faixa</th>
                                        <th class="text-center n-mobile-cell">Extra 2° faixa</th>
                                        <th class="text-center n-mobile-cell">Extra 100%</th>
                                        <th class="text-center n-mobile-cell">Total Extra</th>
                                        <th class="text-center n-mobile-cell">Atraso</th>
                                        <th class="text-center n-mobile-cell">Falta</th>
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
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['motivo_reprova'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['motivo_reprova'];
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
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['motivo_reprova'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['motivo_reprova'];
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
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['motivo_reprova'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['motivo_reprova'];
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
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['motivo_reprova'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['motivo_reprova'];
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
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['motivo_reprova'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['motivo_reprova'];
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
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['motivo_reprova'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['motivo_reprova'];
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
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['motivo_reprova'];
                                                $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] : 'D';
                                                $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['forcado'] == 1) ? 1 : 0;
                                                $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia']);
                                                $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']);
                                                $idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['idaafdt']);
                                                $indice_ent++;
                                            } else {
                                                $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'], 4);
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['motivo_reprova'];
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
                                                $sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['motivo_reprova'];
                                                $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] : 'D';
                                                $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['forcado'] == 1) ? 1 : 0;
                                                $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia']);
                                                $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']);
                                                $idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['idaafdt']);
                                                $indice_sai++;
                                            } else {
                                                $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'], 4);
                                                $ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

                                                if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

                                                $ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['motivo_reprova'];
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
                                                        $abono_pendente = '<i title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-square" style="font-size:20px; color: red;"></i>';
                                                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-timer-off tippy-btn text-danger" style="font-size:20px;"></i>';
                                                    }else{
                                                        $abono_pendente = '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>';
                                                        $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Abono Pendente de Aprovação" class="mdi mdi-timer-off tippy-btn" style="font-size:20px; color: #ace5ff;"></i>';
                                                    }
                                                    unset($resSolicitacaoAbono[$keyb]);
                                                }
                                            }
                                        }

                                        // abono pendente de aprovação RH
                                        if($resAbonoPendenteRH){
                                            foreach($resAbonoPendenteRH as $keya => $AbonoPendente){
                                                if (dtBr($AbonoPendente['DATA']) == dtBr($DiasEspelho['DATA'])) {
                                                    $abono_pendente = '<i title="Aguardando Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';
                                                    $abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Aguardando Aprovação RH" class="mdi mdi-timer-off tippy-btn" style="font-size:20px; color: #acffcd;"></i>';
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
                                        if($possui_extra){
                                            $bglinha = ' style="background-color: #f9d399;" ';
                                        }

                                        $possui_falta_atraso = ((int)$DiasEspelho['FALTA'] > 0 || (int)$DiasEspelho['ATRASO'] > 0) ? true : false;
                                        if($possui_falta_atraso){
                                            $bglinha = ' style="background-color: #fff08f;" ';
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
                                            }
                                        }
                                        if($statusPeriodo == 0) $periodo_bloqueado = true;
                                        //-----------------------------------------------
                                        ?>
                                        <tr>
                                            <td class="n-mobile-cell text-center <?= ($bglinha != "" && 1==2) ? "text-danger" : ""; ?>" style="background-color: #f9f9f9;"><?= dtBr($DiasEspelho['DATA']); ?></td>
                                            <td class="n-mobile-cell text-center <?= ($bglinha != "" && 1==2) ? "text-danger" : ""; ?>" style="background-color: #f9f9f9;"><?= diaSemana($DiasEspelho['DATA'], true); ?></td>
                                            <?php if($DiasEspelho['AFASTAMENTO']):?>
                                                <td <?= $bglinha; ?> colspan="8" class="n-mobile-cell text-center text-danger"><span class="badge badge-purple"><?= $DiasEspelho['AFASTAMENTO']; ?></span></td>
                                            <?php elseif($DiasEspelho['FERIAS']) :?>
                                                <td <?= $bglinha; ?> colspan="8" class="n-mobile-cell text-center text-danger"><span class="badge badge-purple">Férias</span></td>
                                            <?php else:?>
                                                <td <?php if(strlen(trim($ent_motivo_reprova[1])) > 0){echo ' style="background:#fe0200;" title="'.$ent_motivo_reprova[1].'"';}elseif(strlen(trim($ent_portal[1])) > 0 && $status_ent[1] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_ent[1] == "T"){echo ' style="background:#acffcd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $ent[1]; ?></td>
                                                <td <?php if(strlen(trim($sai_motivo_reprova[1])) > 0){echo ' style="background:#fe0200;" title="'.$sai_motivo_reprova[1].'"';}elseif(strlen(trim($sai_portal[1])) > 0 && $status_sai[1] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_sai[1] == "T"){echo ' style="background:#acffcd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $sai[1]; ?></td>
                                                <td <?php if(strlen(trim($ent_motivo_reprova[2])) > 0){echo ' style="background:#fe0200;" title="'.$ent_motivo_reprova[2].'"';}elseif(strlen(trim($ent_portal[2])) > 0 && $status_ent[2] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_ent[2] == "T"){echo ' style="background:#acffcd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $ent[2]; ?></td>
                                                <td <?php if(strlen(trim($sai_motivo_reprova[2])) > 0){echo ' style="background:#fe0200;" title="'.$sai_motivo_reprova[2].'"';}elseif(strlen(trim($sai_portal[2])) > 0 && $status_sai[2] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_sai[2] == "T"){echo ' style="background:#acffcd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $sai[2]; ?></td>
                                                <td <?php if(strlen(trim($ent_motivo_reprova[3])) > 0){echo ' style="background:#fe0200;" title="'.$ent_motivo_reprova[3].'"';}elseif(strlen(trim($ent_portal[3])) > 0 && $status_ent[3] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_ent[3] == "T"){echo ' style="background:#acffcd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $ent[3]; ?></td>
                                                <td <?php if(strlen(trim($sai_motivo_reprova[3])) > 0){echo ' style="background:#fe0200;" title="'.$sai_motivo_reprova[3].'"';}elseif(strlen(trim($sai_portal[3])) > 0 && $status_sai[3] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_sai[3] == "T"){echo ' style="background:#acffcd;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $sai[3]; ?></td>
                                                <?php if($DiasEspelho['COMPENSADO'] > 0): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-purple">COMPENSADO</span></td>
                                                <?php elseif($DiasEspelho['DESCANSO'] > 0): ?>
                                                    <td <?= $bglinha; ?> class="n-mobile-cell text-center" colspan="2"><span class="badge badge-purple">DESCANSO</span></td>
                                                <?php else: ?>
                                                    <td <?php if(strlen(trim($ent_motivo_reprova[4])) > 0){echo ' style="background:#fe0200;" title="'.$ent_motivo_reprova[4].'"';}elseif(strlen(trim($ent_portal[4])) > 0 && $status_ent[4] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_ent[4] == "T"){echo ' style="background:#ace5ff;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $ent[4]; ?></td>
                                                    <td <?php if(strlen(trim($sai_motivo_reprova[4])) > 0){echo ' style="background:#fe0200;" title="'.$sai_motivo_reprova[4].'"';}elseif(strlen(trim($sai_portal[4])) > 0 && $status_sai[4] != "T"){echo ' style="background:#ace5ff;" ';}elseif($status_sai[4] == "T"){echo ' style="background:#ace5ff;" ';} ?> <?= $bglinha; ?> class="n-mobile-cell text-center"><?= $sai[4]; ?></td>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-primary"><?= ((int)$DiasEspelho['HTRAB'] > 0) ? m2h($DiasEspelho['HTRAB']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-success"><?= ((int)$DiasEspelho['EXTRA_1AFAIXA'] > 0) ? m2h($DiasEspelho['EXTRA_1AFAIXA']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-success"><?= ((int)$DiasEspelho['EXTRA_2AFAIXA'] > 0) ? m2h($DiasEspelho['EXTRA_2AFAIXA']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-success"><?= ((int)$DiasEspelho['EXTRA_100'] > 0) ? m2h($DiasEspelho['EXTRA_100']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-success"><?= ((int)$DiasEspelho['EXTRAAUTORIZADO'] > 0) ? m2h($DiasEspelho['EXTRAAUTORIZADO']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['ATRASO'] > 0) ? m2h($DiasEspelho['ATRASO']) : ""; ?></td>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-danger"><?= ((int)$DiasEspelho['FALTA'] > 0) ? m2h($DiasEspelho['FALTA']) : ""; ?></td>
                                            <?php if(!$impressao): ?>
                                            <td <?= $bglinha; ?> class="n-mobile-cell text-center text-success"><?= ((int)$DiasEspelho['ABONO'] > 0) ? m2h($DiasEspelho['ABONO']) : $abono_pendente; ?></td>
                                            
                                            <td <?= $bglinha; ?> class="y-mobile-cell d-none text-center">
                                                <h3 style="font-size: 20px;" class="m-0 p-0"><?= date('d/m', strtotime($DiasEspelho['DATA'])); ?></h3><small class="text-gray d-block" style="color: #999999;"><?= diaSemana($DiasEspelho['DATA']); ?></small><?= $abono_pendente_mobile.$justificativa_extra; ?>

                                                <?php if($DiasEspelho['COMPENSADO'] > 0): ?>
                                                    <span class="badge badge-purple">COMPENSADO</span>
                                                <?php elseif($DiasEspelho['DESCANSO'] > 0): ?>
                                                    <span class="badge badge-purple">DESCANSO</span>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td <?= $bglinha; ?> class="y-mobile-cell d-none text-center align-top">

                                                <?php $numero_batida = 0; ?>
                                                <?php $proxima_batida = 1; ?>
                                                <?php if (strlen(trim($ent[1])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[1])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[1]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' : $ent_portal[1]) . $ent[1]; ?></span> <small style="color: #999999;">E1</small></h3><?php $numero_batida++;                                                                                                                                                                                                                            $proxima_batida = 1; ?><?php endif; ?>
                                                <?php if (strlen(trim($ent[2])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[2])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[2]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' : $ent_portal[2]) . $ent[2]; ?></span> <small style="color: #999999;">E2</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                $proxima_batida = 2; ?><?php endif; ?>
                                                <?php if (strlen(trim($ent[3])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[3])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[3]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' : $ent_portal[3]) . $ent[3]; ?></span> <small style="color: #999999;">E3</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                    $proxima_batida = 3; ?><?php endif; ?>
                                                <?php if (strlen(trim($ent[4])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($ent_motivo_reprova[4])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $ent_motivo_reprova[4]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' : $ent_portal[4]) . $ent[4]; ?></span> <small style="color: #999999;">E4</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                                    $proxima_batida = 4; ?><?php endif; ?>

                                            </td>
                                            <td <?= $bglinha; ?> class="y-mobile-cell d-none text-center align-top">

                                                <?php if (strlen(trim($sai[1])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[1])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[1]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' :  $sai_portal[1]) . $sai[1]; ?></span> <small style="color: #999999;">S1</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                        $proxima_batida = 2; ?><?php endif; ?>
                                                <?php if (strlen(trim($sai[2])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[2])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[2]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' :  $sai_portal[2]) . $sai[2]; ?></span> <small style="color: #999999;">S2</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                            $proxima_batida = 3; ?><?php endif; ?>
                                                <?php if (strlen(trim($sai[3])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[3])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[3]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' :  $sai_portal[3]) . $sai[3]; ?></span> <small style="color: #999999;">S3</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                                $proxima_batida = 4; ?><?php endif; ?>
                                                <?php if (strlen(trim($sai[4])) > 0) : ?><h3><span class="text-primary" style="font-size: 20px;display: inline-block;"><?= ((strlen(trim($sai_motivo_reprova[4])) > 0) ? '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="' . $sai_motivo_reprova[4]. '" class="mdi mdi-square tippy-btn" style="font-size:20px; color: #fe0200;"></i>' :  $sai_portal[4]) . $sai[4]; ?></span> <small style="color: #999999;">S4</small></h3><?php $numero_batida++;                                                                                                                                                                                                                                                    $proxima_batida = 5; ?><?php endif; ?>

                                            </td>
                                            <td <?= $bglinha; ?> class="text-center">
                                                    <?php if(!$periodo_bloqueado): ?>
                                                        <div class="btn-group dropleft mb-2 mb-md-0">
                                                            <?php
                                                            if (!(($DiasEspelho['AFASTAMENTO'] > 0) || ($DiasEspelho['FERIAS'] > 0))) : ?>
                                                                <button type="button" class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="mdi mdi-dots-vertical"></i></button>
                                                                <div class="dropdown-menu">
                                                                    <!--<button <?= ($numero_batida == 8) ? ' disabled ' : ''; ?> onclick="abrirInclusaoBatida('<?= dtEn($DiasEspelho['DATA'], true); ?>', <?= $numero_batida; ?>, <?= $proxima_batida; ?>);" type="button" class="dropdown-item <?= ($numero_batida == 8) ? ' text-secondary ' : ''; ?>" title="Incluir batida"><i class="fas fa-plus-circle"></i> Incluir batida</button>-->
                                                                    <button onclick="abrirAlteracaoBatida('<?= dtEn($DiasEspelho['DATA'], true); ?>', '<?= urlencode(json_encode($array_batidas)); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= $DiasEspelho['ESCALA']; ?>', '<?= dtBr($DiasEspelho['DATA']); ?>')" type="button" class="dropdown-item" title="Alterar batida"><i class="mdi mdi-pencil-outline"></i> Incluir/Alterar batida</button>
                                                                    <button onclick="abrirResumoDiario('<?= dtBr($DiasEspelho['DATA']); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= ((int)$DiasEspelho['HTRAB'] > 0) ? m2h($DiasEspelho['HTRAB']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['ATRASO'] > 0) ? m2h($DiasEspelho['ATRASO']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['FALTA'] > 0) ? m2h($DiasEspelho['FALTA']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['EXTRAAUTORIZADO'] > 0) ? m2h($DiasEspelho['EXTRAAUTORIZADO']) : '0:00'; ?>', '<?= ((int)$DiasEspelho['ABONO'] > 0) ? m2h($DiasEspelho['ABONO']) : '0:00'; ?>', '<?= urlencode(json_encode($array_batidas)); ?>', '<?= $DiasEspelho['ESCALA']; ?>')" type="button" class="dropdown-item" title="Resumo diário"><i class="mdi mdi-file-document-box-outline"></i> Resumo diário</button>
                                                                    <?php if((int)$DiasEspelho['ATRASO'] > 0 || (int)$DiasEspelho['FALTA'] > 0): ?><button onclick="abrirSolicitacaoAbono('<?= dtEn($DiasEspelho['DATA'], true); ?>', '<?= dtBr($DiasEspelho['DATA']); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= urlencode(json_encode($array_abonos)); ?>', <?= $tipo_ocorrencia; ?>, '<?= urlencode(json_encode($array_batidas)); ?>', '<?= $DiasEspelho['ESCALA']; ?>')" type="button" class="dropdown-item" title="Solicitar abono"><i class="mdi mdi-timer-off"></i> Solicitar abono</button><?php endif; ?>
                                                                    <?php if((int)$DiasEspelho['EXTRAAUTORIZADO'] > 0): ?><button onclick="abrirJustificativaExtra('<?= dtEn($DiasEspelho['DATA'], true); ?>', '<?= dtBr($DiasEspelho['DATA']); ?>', '<?= diaSemana($DiasEspelho['DATA']); ?>', '<?= $codigo_justificativa_extra; ?>', '<?= urlencode(json_encode($array_batidas)); ?>', '<?= $DiasEspelho['ESCALA']; ?>')" type="button" class="dropdown-item" title="Justificativa de Extra"><i class="mdi mdi-comment-check-outline"></i> Justificativa de Extra</button><?php endif; ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <i title="Periodo bloqueado" class="mdi mdi-lock-alert" disabled style="font-size:20px;color:#999999;"></i>
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

                        <?php if($impressao): ?>
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

        <?php if(!$periodo_bloqueado): ?>
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
                    exibeAlerta('error', '<b>Batida</b> não informada.');
                    return false;
                }
                if (!validaHorario(dados.horario_batida)) {
                    exibeAlerta('error', '<b>Batida</b> inválida.');
                    return false;
                }
                var batida = parseInt(dados.horario_batida.replace(':', ''));
                if (batida > 2359) {
                    exibeAlerta('error', '<b>Batida</b> não pode ser superior a <b>23:59</b>.');
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

        }

        //------------------------------------------------------
        // alteração de batidas
        //------------------------------------------------------
        const abrirAlteracaoBatida = (data, batidas, diaSemana, escala, data_br) => {
            $(".modal_alterar_batida").modal('show');
            
            $(".modal_alterar_batida [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diaSemana + '</small>');
            $(".modal_alterar_batida [data-h-escala]").html('Escala: '+escala);
            $(".modal_alterar_batida [data-h-batidas]").html('Batidas existentes: ');

            var batidas = JSON.parse(decodeURIComponent(batidas));
            console.log(batidas);
            $('.modal_alterar_batida tbody').html('');

            data_nova_batida = data;
            natureza_proxima_batida = 0;

            for (var x = 0; x < batidas.length; x++) {

                var liberado = (batidas[x].pendente != 1 || batidas[x].status == 'T') ? ' disabled ' : '';
                var tipo = (batidas[x].pendente != 1 || batidas[x].status == 'T') ? ' data-p="RM" data-idaafdt="' + batidas[x].idaafdt + '" ' : ' data-p="U" ';
                if(batidas[x].reprova != '' && batidas[x].reprova != null) var tipo = ' data-p= "X" ';

                var html = '<tr data-info="' + Math.random() + '" ' + tipo + '>' +
                    '<td><input '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' type="date" class="form-control form-control-sm  p-0" value="' + batidas[x].data_ponto + '"></td>' +
                    '<td><input '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' data-default="' + batidas[x].batida + '" ' + liberado + ' type="time" class="form-control form-control-sm  p-0" value="' + batidas[x].batida + '"></td>' +
                    '<td><select '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' class="form-control form-control-sm  p-0" style="min-width:56px;"><option value="0" ' + (batidas[x].natureza != 1 ? 'selected' : '') + '>Entrada</option><option value="1" ' + (batidas[x].natureza == 1 ? 'selected' : '') + '>Saida</option></select></td>' +
                    '<td width="1" class="p-0">';

                    $(".modal_alterar_batida [data-h-batidas]").append(batidas[x].batida + ' | ');

                if(batidas[x].reprova != '' && batidas[x].reprova != null){
                    html += '<button onclick="return excluirBatidaPT(\'' + batidas[x].data_ponto + '\', \'' + batidas[x].batida + '\')" class="btn btn-danger btn-xxs btn-excluir">X</button>';
                }
                else if (batidas[x].status == 'D' && batidas[x].pendente == 0) {
                    //html += '<button onclick="return excluirBatidaRM(\'' + batidas[x].idaafdt + '\')" class="btn btn-danger btn-excluir">X</button>';
                } else if (batidas[x].status == 'D' && batidas[x].pendente == 1) {
                    html += '<button onclick="return excluirBatidaPT(\'' + batidas[x].data_ponto + '\', \'' + batidas[x].batida + '\')" class="btn btn-danger btn-xxs btn-excluir">X</button>';
                }

                html += '</td></tr>';

                $('.modal_alterar_batida tbody').append(html);

                natureza_proxima_batida = (batidas[x].natureza != 1) ? 1 : 0;

            }

            verificaLimite();
        }

        //excluir batida RM tabela ABATFUN
        const excluirBatidaPT = (data_ref, batida) => {

            Swal.fire({
                icon: 'question',
                title: 'Deseja excluir essa batida?',
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
                title: 'Digite uma justificativa para excluir essa batida:',
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
                    let data_ref = $("[data-info='" + id + "']").find('input,select')[0].value;
                    let batida = $("[data-info='" + id + "']").find('input,select')[1].value;
                    let batida_default = $("[data-info='" + id + "']").find('input,select')[1].getAttribute('data-default');
                    let natureza = $("[data-info='" + id + "']").find('input,select')[2].value;

                    if (data_ref == "") {
                        $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    } else {
                        $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm');
                    }

                    if (batida == "") {
                        $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    } else {
                        $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm');
                    }

                    if (natureza == "") {
                        $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    } else {
                        $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm');
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
                            'data': data_nova_batida,
                            'tipo': tipo,
                            'id': id,
                            'justificativa': (just != undefined) ? just : '',
                            'data_ref': data_ref,
                            'batida': batida,
                            'batida_default': batida_default,
                            'natureza': natureza,
                            "codfilial": '<?= ($resFuncionario[0]['CODFILIAL'] ?? 1) ?>',
                            "chapa": '<?= $chapa ?>',
                            "idaafdt": idaafdt,
                            "tem_anexo": tem_anexo
                        });

                        fd.append('dados[]', JSON.stringify(dados));
                        if (tipo == "I") {
                            if (doc[0].files[0] != undefined) fd.append(doc[0].files[0].name, doc[0].files[0]);
                        }
                        //-----------------------------------



                    }
                }

            });

            openLoading();

            $.ajax({
                url: "<?= base_url('ponto/espelho/action/alterar_batida') ?>",
                // type: 'POST',
                // data: {
                //     "dados": dados
                // },
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
        // cria nova linha para inclusão de nova batida
        const incluirNovaBatida = () => {

            if (!validaDados()) return;

            $("[data-just]").fadeOut(0);
            var id = Math.random();

            var html = '<tr data-p="I" data-info="' + id + '">' +
                '<td><input type="date" class="form-control form-control-sm" value="' + data_nova_batida + '"></td>' +
                '<td><input type="time" class="form-control form-control-sm" value=""></td>' +
                '<td><select class="form-control form-control-sm"><option value="0" ' + (natureza_proxima_batida != 1 ? 'selected' : '') + '>Entrada</option><option value="1" ' + (natureza_proxima_batida == 1 ? 'selected' : '') + '>Saida</option></select></td>' +
                '<td width="18" class="p-0"><button class="btn btn-danger btn-xxs pr-1 pl-1" data-remove-batida="' + id + '">X</button></td>' +
                '</tr>' +
                '<tr data-justificativa="' + id + '" data-just><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Motivo do ajuste:</label><select class="form-control form-control-sm"><option value="">...</option>'+select_justificativa_ajuste+'</td></tr>';
                //'<tr data-justificativa="' + id + '" data-just><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Justificativa:</label><input type="text" maxlength="50" class="form-control mt-0"></td></tr>';

            $('.modal_alterar_batida tbody').append(html);

            // abre a justificativa da batida ao clicar na linha
            $("[data-info]").on('click', function(e) {
                $("[data-just]").fadeOut(0);
                $("[data-justificativa='" + $(this).attr('data-info') + "']").fadeIn(0);
            });

            // remove a nova batida
            $("[data-remove-batida]").on('click', function(e) {
                $("[data-info='" + $(this).attr('data-remove-batida') + "']").remove();
                $("[data-justificativa='" + $(this).attr('data-remove-batida') + "']").remove();
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

            $("[data-p]").each(function(e) {
                let tipo = $(this).attr('data-p');
                let id = $(this).attr('data-info');

                if (id != undefined) {
                    if (tipo == "I") {
                        var just = $("[data-justificativa='" + id + "'] select").val().trim();
                    }
                    let data_ref = $("[data-info='" + id + "']").find('input,select')[0].value;
                    let batida = $("[data-info='" + id + "']").find('input,select')[1].value;
                    let batida_default = $("[data-info='" + id + "']").find('input,select')[1].getAttribute('data-default');
                    let natureza = $("[data-info='" + id + "']").find('input,select')[2].value;

                    $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm');
                    $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm');
                    $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm');

                    if (data_ref == "") $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    if (batida == "") $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
                    if (natureza == "") $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');

                    if (tipo == "I") {
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
            let qtde = $(".modal_alterar_batida [type=date]").length;
            if (qtde >= 8) {
                $('[data-btn-add]').fadeOut(0);
            } else {
                $('[data-btn-add]').fadeIn(0);
            }
        }
        //-----------------------------------------------------------
        // solicitar abono
        //-----------------------------------------------------------
        const abrirSolicitacaoAbono = (data, data_br, diasemana, abonos, tipo_ocorrencia, batidas, escala) => {
            $(".modal_abono").modal('show');
            
            // $(".modal_abono h3").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_abono [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_abono [data-h-escala]").html('Escala: '+escala);
            $(".modal_abono [data-h-batidas]").html('Batidas existentes: ');
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
                                '<td><input disabled data-inicio-default="' + response[x].abn_horaini + '" type="time" class="form-control form-control-sm p-0" value="' + horaInicioFormatada + ':' + minutoInicioFormatado + '"></td>' +
                                '<td><input disabled data-termino-default="' + response[x].abn_horafim + '" type="time" class="form-control form-control-sm p-0" value="' + horaFimFormatada + ':' + minutoFimFormatado + '"></td>';
                                //'<td><input disabled data-justificativa-default="' + (response[x].justificativa_abono_tipo || '') + '" type="text" maxlength="50" class="form-control form-control-sm" value="' + (response[x].justificativa_abono_tipo || '') + '"></td>';
                       
                            html += '<td width="1" class="p-0" '+titulo_abono+'><span class="badge badge-success">Sinc.</span></td>' +
                                '<td></td>'
                            '</tr>';
                            if(response[x].nome_anexo != '' && response[x].nome_anexo != null){
                                html += '<tr>';
                                html += '<td colspan="4"><label><i class="mdi mdi-arrow-up-bold"></i> Anexo:</label> <a href="<?= base_url('/ponto/preview/index'); ?>/'+response[x].id+'" target="_blank">'+response[x].nome_anexo+'</a></td>';
                                html += '</tr>';
                            }
                        }

                        $('.modal_abono tbody').append(html);
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
                    '<td><input data-inicio-default="' + abonos[x].inicio + '" type="time" class="form-control p-0" value="' + abonos[x].inicio + '"></td>' +
                    '<td><input data-termino-default="' + abonos[x].termino + '" type="time" class="form-control p-0" value="' + abonos[x].termino + '"></td>';
                    //'<td><input data-justificativa-default="' + abonos[x].just_abono_tipo.replace(/\+/g, ' ') + '" type="text" maxlength="50" class="form-control" value="' + abonos[x].just_abono_tipo.replace(/\+/g, ' ') + '"></td>';
                if(parseInt(abonos[x].status) == 3){
                    html += '<td width="1" class="p-0"><span class="badge badge-danger" tippy-btn '+titulo_abono+'>Rep.</span></td>';
                    html += '<td><button class="btn btn-sm btn-danger btn-xxs" onclick="return ExcluirAbonoPT(' + abonos[x].id + ')"><i class="mdi mdi-trash-can-outline"></i></button></td>';
                }else{
                    html += '<td width="1" class="p-0"><span class="badge badge-warning tippy-btn" '+titulo_abono+'>Sol.</span></td>';
                    html += '<td><button class="btn btn-sm btn-danger btn-xxs" onclick="return ExcluirAbonoPT(' + abonos[x].id + ')"><i class="mdi mdi-trash-can-outline"></i></button></td>';
                }
                html += '</tr>';
                if(abonos[x].nome_anexo != ''){
                    html += '<tr>';
                    html += '<td colspan="4"><label><i class="mdi mdi-arrow-up-bold"></i> Anexo:</label> <a href="<?= base_url('/ponto/preview/index'); ?>/'+abonos[x].id+'" target="_blank">'+abonos[x].nome_anexo+'</a></td>';
                    html += '</tr>';
                }

                $('.modal_abono tbody').append(html);

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
                        'inicio'         : $(this).find('input')[0].value,
                        'termino'        : $(this).find('input')[1].value,
                        'just_abono_tipo': '',
                        //'just_abono_tipo': $(this).find('input')[2].value,
                        'inicio_default' : $(this).find('input')[0].getAttribute('data-inicio-default'),
                        'termino_default': $(this).find('input')[1].getAttribute('data-termino-default'),
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

            if (abono_noturno) {
                
                Swal.fire({
                    icon: 'question',
                    title: msg_noturno,
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

            let qtde_abonos = $("[data-abono]").length;
            if (qtde_abonos > 1) {
                var novo_inicio = $("[data-abono]:last").find('input')[0].value;
                var novo_termino = $("[data-abono]:last").find('input')[1].value;
            }

            var msg_erro = "";

            $("[data-abono]").each(function(e) {

                let codabono = $(this).find('select')[0].value;
                let inicio = $(this).find('input')[0].value;
                let termino = $(this).find('input')[1].value;
                let inicioH = $(this).find('input')[0].value;
                let terminoH = $(this).find('input')[1].value;
                var atestado = $(this).find(':selected').attr('data-atestado');
                var tipo = $(this).attr('data-abono');

                if (inicio != '' && termino != '') {
                    inicio = parseInt(inicio.replace(':', ''));
                    termino = parseInt(termino.replace(':', ''));
                    if (inicio > 2359) {
                        msg_erro = '<b>Batida</b> não pode ser superior a <b>23:59</b>.';
                        return false;
                    }
                    if (termino > 2359) {
                        msg_erro = '<b>Batida</b> não pode ser superior a <b>23:59</b>.';
                        return false;
                    }
                    if (termino < inicio) {
                        if (data_nova_batida != "") {
                            var dataIni = new Date(parseInt(data_nova_batida.slice(0, 4)), parseInt(data_nova_batida.slice(5, 7)) - 1, parseInt(data_nova_batida.slice(8)));
                            var dataFim = new Date(parseInt(data_nova_batida.slice(0, 4)), parseInt(data_nova_batida.slice(5, 7)) - 1, parseInt(data_nova_batida.slice(8)));
                            dataFim.setDate(dataFim.getDate() + 1);
                            msg_noturno = "<b>Hora término " + terminoH + "</b> esta menor que a <b>hora inicio " + inicioH + "</b>, sera gerado um abono do dia <b>" + pad(dataIni.getDate(), 2) + "/" + pad(dataIni.getMonth() + 1, 2) + "</b> até <b>" + pad(dataFim.getDate(), 2) + "/" + pad(dataFim.getMonth() + 1, 2) + "</b>, confirma solicitação?";
                        }
                        abono_noturno = true;
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
                            $("[data-abono]:last").find('input')[1].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            msg_erro = '<b>Inicio</b> informado esta em interseção com outro horário de abono.';
                            return false;
                        }
                        if (novo_termino_minuto >= inicio && novo_termino_minuto <= termino) {
                            $("[data-abono]:last").find('input')[1].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            msg_erro = '<b>Término</b> informado esta em interseção com outro horário de abono.';
                            return false;
                        }
                        if (novo_inicio_minuto <= inicio && novo_termino_minuto >= termino) {
                            $("[data-abono]:last").find('input')[0].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            $("[data-abono]:last").find('input')[1].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                            msg_erro = '<b>Inicio</b> e <b>Término</b> informado esta em interseção com outro horário de abono.';
                            return false;
                        }
                    }
                }

                $(this).find('select')[0].setAttribute('class', (codabono != '') ? 'form-control form-control-sm p-0 m-0' : 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
                $(this).find('input')[0].setAttribute('class', (typeof inicio != "string") ? 'form-control form-control-sm p-0 m-0' : 'form-control parsley-error text-danger');
                $(this).find('input')[1].setAttribute('class', (typeof termino != "string") ? 'form-control form-control-sm p-0 m-0' : 'form-control parsley-error text-danger');
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

        //-----------------------------------------------------------
        // resumo diario
        //-----------------------------------------------------------
        const abrirResumoDiario = (data, diasemana, horas_trabalhadas, atrasos, faltas, extras, abono, batidas, escala) => {

            $(".modal_resumo_diario").modal('show');
            $(".modal_resumo_diario [data-h-data]").html(data + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_resumo_diario [data-h-escala]").html('Escala: '+escala);
            $(".modal_resumo_diario [data-h-batidas]").html('Batidas existentes: ');
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

        }
        //-----------------------------------------------------------
        // justificativa de extra
        //-----------------------------------------------------------
        const abrirJustificativaExtra = (data, data_br, diasemana, codigoJustificativa, batidas, escala) => {
            $(".modal_justificativa_extra").modal('show');
            
            $(".modal_justificativa_extra [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
            $(".modal_justificativa_extra [data-h-escala]").html('Escala: '+escala);
            $(".modal_justificativa_extra [data-h-batidas]").html('Batidas existentes: ');
            var batidas = JSON.parse(decodeURIComponent(batidas));

            for (var x = 0; x < batidas.length; x++) {
                $(".modal_justificativa_extra [data-h-batidas]").append(batidas[x].batida + ' | ');
            }
            data_nova_batida = data;
            $("#justificativa_extra").val(codigoJustificativa);

        }
        const justificarExtra = () => {

            try {

                let dados = {
                    "justificativa" : $("#justificativa_extra").val(),
                    "chapa"         : '<?= $chapa ?>',
                    "data"          : data_nova_batida
                }

                if(dados.justificativa == ""){exibeAlerta('error', '<b>Justificativa de Extra</b> não informado.'); return false; }

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

        }
        <?php endif; // if(!$periodo_bloqueado): ?>

    <?php endif; //if($resDiasEspelho && $chapa): 
    ?>
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
</style>

<!-- modal modal_incluir_batida -->
<div class="modal modal_incluir_batida" tabindex="-1" role="dialog" aria-labelledby="modal_incluir_batida" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-primary">
                <h5 class="modal-title mt-0 text-white"><i class="fas fa-plus-circle"></i> Inclusão de Batida</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-6">
                        <div class="form-group row mb-1">
                            <label for="data_batida" class="col-sm-12 col-form-label text-left pb-1">Data batida:</label>
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
                            <label for="horario_batida" class="col-sm-12 col-form-label text-left pb-1">Batida:</label>
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
                <button type="button" class="btn btn-primary" onclick="return cadastraBatida()">Incluir Batida <i class="fas fa-check"></i></button>
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
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-pencil-outline"></i> Incluir/Alterar Batida</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Data referencia</th>
                                    <th>Batida</th>
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

                        <button data-btn-add class="btn btn-outline-primary waves-effect waves-light btn-block" type="button" onclick="incluirNovaBatida()"><i class="mdi mdi-plus-circle-outline"></i> Adicionar batida</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="return alterarBatida()">Salvar Batida <i class="fas fa-check"></i></button>
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
                <button type="button" class="btn btn-success" onclick="return justificarExtra()">Salvar Justificativa <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_justificativa_extra -->

<script>
    $(function() {
        $(".modal").draggable();
    });
const carregaFuncionariosSecao = (codSecao) => {

    openLoading();

    $("#funcionario").html('<option value="">-- selecione um funcionário --</option>').trigger('change');
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
</script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
