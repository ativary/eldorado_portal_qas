<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <?php if ($isGestor <= 0) : ?>
                <!-- FORMULARIO SEM SEÇÃO -->
                <div class="card noprint">
                    <div class="card-header mt-0">
                        <div class="row">
                            <h4 class="col-12 mb-1 mt-1">Selecione o período</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <label for="perfil_nome" class="col-2 col-form-label text-right">Período:</label>
                            <div class="col-sm-10">

                                <form action="" method="post" name="form_holerite" id="form_holerite">
                                    <select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo">
                                        <option value="">- selecione um período -</option>
                                        <?php if ($resPeriodo) : ?>
                                            <option value="<?= dtBr($resPeriodo[1]['INICIOMENSAL']) . dtBr($resPeriodo[1]['FIMMENSAL']); ?>" <?= ($periodo == dtBr($resPeriodo[1]['INICIOMENSAL']) . dtBr($resPeriodo[1]['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($resPeriodo[1]['INICIOMENSAL']) . ' à ' . dtBr($resPeriodo[1]['FIMMENSAL']); ?></option>
                                            <option value="<?= dtBr($resPeriodo[2]['INICIOMENSAL']) . dtBr($resPeriodo[2]['FIMMENSAL']); ?>" <?= ($periodo == dtBr($resPeriodo[2]['INICIOMENSAL']) . dtBr($resPeriodo[2]['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($resPeriodo[2]['INICIOMENSAL']) . ' à ' . dtBr($resPeriodo[2]['FIMMENSAL']); ?></option>
                                        <?php endif; ?>
                                    </select>
                                </form>

                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return consultaHolerite()"><i class="fas fa-search"></i> Consultar</button>
                    </div>
                </div>

            <?php else : ?>
                <!-- FORMULARIO COM SEÇÃO -->
                <div class="card noprint">
                    <div class="card-header mt-0">
                        <div class="row">
                            <h4 class="col-12 mb-1 mt-1">Selecione o colaborador e o período</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form action="" method="post" name="form_holerite" id="form_holerite">

                                <div class="col-sm-12 d-flex">
                                    <label for="funcionario" class="col-6 col-form-label text-right">Colaborador:</label>
                                    <div class="col-sm-12">
                                        <select class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                            <option value="">- selecione um colaborador -</option>
                                            <?php if ($ListaFuncionarioSecao) : ?>
                                                <?php foreach ($ListaFuncionarioSecao as $key => $DadosFunc) : ?>
                                                    <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($chapa == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] . ' - ' . $DadosFunc['NOME'] ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-12 d-flex">
                                    <label for="periodo" class="col-6 col-form-label text-right">Período:</label>
                                    <div class="col-sm-12">
                                        <select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo">
                                            <option value="">- selecione um período -</option>
                                            <?php if ($resPeriodo) : ?>
                                                <option value="<?= dtBr($resPeriodo[1]['INICIOMENSAL']) . dtBr($resPeriodo[1]['FIMMENSAL']); ?>" <?= ($periodo == dtBr($resPeriodo[1]['INICIOMENSAL']) . dtBr($resPeriodo[1]['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($resPeriodo[1]['INICIOMENSAL']) . ' à ' . dtBr($resPeriodo[1]['FIMMENSAL']); ?></option>
                                                <option value="<?= dtBr($resPeriodo[2]['INICIOMENSAL']) . dtBr($resPeriodo[2]['FIMMENSAL']); ?>" <?= ($periodo == dtBr($resPeriodo[2]['INICIOMENSAL']) . dtBr($resPeriodo[2]['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($resPeriodo[2]['INICIOMENSAL']) . ' à ' . dtBr($resPeriodo[2]['FIMMENSAL']); ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return consultaHoleriteFuncionario()"><i class="fas fa-search"></i> Consultar</button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="card">

                <div class="card-header mt-0 noprint">
                    <div class="row d-flex justify-content-between">
                        <h4 class="col-8 mb-1 mt-1"><?= $_titulo; ?></h4>
                        <?php if ($resDiasEspelho) : ?>
                            <a class="btn btn-xxs btn-primary" href="javascript:exibirDivImprimir();"><i class="fas fa-print"></i> Imprimir</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <?php if ($resDiasEspelho) : ?>

                        <div class="imprimir" style="display: none;">
                            <div class="text-center">
                                <h3><strong>Espelho de Ponto</strong></h3>
                            </div>

                            <ul class="list-group ml-3 mb-2">
                                <li class="list-group-item border-0 m-0 mb-1 p-0">
                                    <strong>Nome: </strong><?= $DadosFuncionario[0]['NOME'] ?>
                                </li>
                                <li class="list-group-item border-0 m-0 mb-1 p-0">
                                    <strong>Seção: </strong><?= $DadosFuncionario[0]['CODSECAO'] . " - " . $DadosFuncionario[0]['NOMESECAO'] ?>
                                </li>
                                <li class="list-group-item border-0 m-0 mb-1 p-0">
                                    <strong>Função: </strong><?= $DadosFuncionario[0]['CODFUNCAO'] . " - " . $DadosFuncionario[0]['NOMEFUNCAO'] ?>
                                </li>
                                <li class="list-group-item border-0 m-0 mb-1 p-0">
                                    <strong>Periodo: </strong>
                                    <?php
                                        $data1 = substr($_POST['periodo'],0,10); // A primeira data "21/04/2023"
                                        $data2 = substr($_POST['periodo'],10,20); 
                                        echo $data1 . ' à ' . $data2;
                                    ?>
                                </li>
                            </ul>
                        </div>


                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header">
                                    <h4 class="mb-1 mt-1">Banco de Horas</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-around text-center">
                                        <div>
                                            <p class="<?= ($HorasPositivas > 0) ? 'text-success' : 'text-danger'; ?> mb-0" style="font-weight: 700;font-size: 170%;text-align: center;"> <?= m2h($HorasPositivas) > 0 ? '+' : '' ?><?= m2h($HorasPositivas) ?> </p>
                                            <p class="<?= ($HorasPositivas > 0) ? 'text-success' : 'text-danger'; ?> mb-0">Saldo Anterior</p>
                                        </div>
                                        <div>
                                            <p class="<?= ($HorasNegativas > 0) ? 'text-success' : 'text-danger'; ?> mb-0" style="font-weight: 700;font-size: 170%;text-align: center;"> <?= m2h($HorasNegativas) > 0 ? '+' : '' ?><?= m2h($HorasNegativas) ?></p>
                                            <p class="<?= ($HorasNegativas > 0) ? 'text-success' : 'text-danger'; ?> mb-0">Saldo Mês</p>
                                        </div>
                                        <div>
                                            <p class="<?= m2h($SaldoTotal) > 0 ? 'text-success mb-0' : 'text-danger mb-0' ?>" style="font-weight: 700;font-size: 170%;text-align: center;"> <?= m2h($SaldoTotal) > 0 ? '+' : '' ?><?= m2h($SaldoTotal) ?></p>
                                            <p class="<?= m2h($SaldoTotal) > 0 ? 'text-success mb-0' : 'text-danger mb-0' ?>">Saldo total</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered mb-0 table-centered table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center" width="100">Data</th>
                                    <th class="text-center" width="90">Dia. Sem.</th>
                                    <th class="text-center">Ent</th>
                                    <th class="text-center">Sai</th>
                                    <th class="text-center">Ent</th>
                                    <th class="text-center">Sai</th>
                                    <th class="text-center">Ent</th>
                                    <th class="text-center">Sai</th>
                                    <th class="text-center">Ent</th>
                                    <th class="text-center">Sai</th>
                                    <th class="text-center">Hr. Trab.</th>
                                    <th class="text-center">Atraso</th>
                                    <th class="text-center">Débito</th>
                                    <th class="text-center">Crédito</th>
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

                                    // ent 1
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['natureza'] == 0) {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']);
                                            $indice_ent++;
                                        } else {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']);
                                            $indice_sai++;
                                        }
                                    }

                                    // sai 1
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['natureza'] == 1) {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']);
                                            $indice_sai++;
                                        } else {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']);
                                            $indice_ent++;
                                        }
                                    }

                                    // ent 2
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['natureza'] == 0) {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']);
                                            $indice_ent++;
                                        } else {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']);
                                            $indice_sai++;
                                        }
                                    }

                                    // sai 2
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['natureza'] == 1) {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']);
                                            $indice_sai++;
                                        } else {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']);
                                            $indice_ent++;
                                        }
                                    }

                                    // ent 3
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['natureza'] == 0) {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']);
                                            $indice_ent++;
                                        } else {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']);
                                            $indice_sai++;
                                        }
                                    }

                                    // sai 3
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['natureza'] == 1) {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']);
                                            $indice_sai++;
                                        } else {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']);
                                            $indice_ent++;
                                        }
                                    }

                                    // ent 4
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['natureza'] == 0) {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']);
                                            $indice_ent++;
                                        } else {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']);
                                            $indice_sai++;
                                        }
                                    }

                                    // sai 4
                                    if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'])) {
                                        $concat = false;
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']) $concat = '-';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']) $concat = '+';
                                        if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['natureza'] == 1) {
                                            $sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida']);
                                            $status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] : 'D';
                                            $dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['forcado'] == 1) ? 1 : 0;
                                            $dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia']);
                                            $data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']);
                                            $indice_sai++;
                                        } else {
                                            $ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida']);
                                            $status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] : 'D';
                                            $dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['forcado'] == 1) ? 1 : 0;
                                            $dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia']);
                                            $data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']);
                                            $indice_ent++;
                                        }
                                    }

                                    $bglinha = "";
                                    if (diaSemana($DiasEspelho['DATA'], true) == "Dom" || diaSemana($DiasEspelho['DATA'], true) == "Sáb") $bglinha = ' style="background-color: #f9f9f9;" ';



                                    ?>
                                    <tr>
                                        <td class="text-center <?= ($bglinha != "") ? "text-danger" : ""; ?>" style="background-color: #f9f9f9;"><?= dtBr($DiasEspelho['DATA']); ?></td>
                                        <td class="text-center <?= ($bglinha != "") ? "text-danger" : ""; ?>" style="background-color: #f9f9f9;"><?= diaSemana($DiasEspelho['DATA'], true); ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $ent[1]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $sai[1]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $ent[2]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $sai[2]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $ent[3]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $sai[3]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $ent[4]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center"><?= $sai[4]; ?></td>
                                        <td <?= $bglinha; ?> class="text-center text-primary"><?= ((int)$DiasEspelho['HTRAB'] > 0) ? m2h($DiasEspelho['HTRAB']) : ""; ?></td>
                                        <td <?= $bglinha; ?> class="text-center text-danger"><?= ((int)$DiasEspelho['ATRASO'] > 0) ? m2h($DiasEspelho['ATRASO']) : ""; ?></td>
                                        <td <?= $bglinha; ?> class="text-center text-danger"><?= ((int)$DiasEspelho['FALTA'] > 0) ? m2h($DiasEspelho['FALTA']) : ""; ?></td>
                                        <td <?= $bglinha; ?> class="text-center text-success"><?= ((int)$DiasEspelho['EXTRAEXECUTADO'] > 0) ? m2h($DiasEspelho['EXTRAEXECUTADO']) : ""; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <h5 class="mt-4">Eventos do movimento no período:</h5>
                        <?php if ($resMovimentos) : ?>
                            <table class="table mb-0 table-centered table-sm " style="width: auto;">
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
                        <?php endif; ?>

                    <?php endif; // $DiasEspelho 
                    ?>



                </div>
            </div>
        </div><!-- end main -->


    </div>
</div><!-- container -->
<script>
    const consultaHolerite = () => {

        let dados = {
            "periodo": $("#periodo").val(),
        }

        if (dados.periodo == "") {
            exibeAlerta("error", "<b>Período</b> não selecionado.");
            return false;
        }

        openLoading();

        document.getElementById("form_holerite").submit();

    }
</script>
<script>
    const consultaHoleriteFuncionario = () => {

        let dados = {
            "funcionario": $("#funcionario").val(),
            "periodo": $("#periodo").val(),
        }

        if (dados.funcionario == "") {
            exibeAlerta("error", "<b>Colaborador</b> não selecionado.");
            return false;
        }
        if (dados.periodo == "") {
            exibeAlerta("error", "<b>Período</b> não selecionado.");
            return false;
        }

        openLoading();

        document.getElementById("form_holerite").submit();

    }

    function exibirDivImprimir() {
        var divImprimir = document.querySelector('.imprimir');
        divImprimir.style.display = 'block';
        window.print();
    }

    window.addEventListener('afterprint', function() {
        var divImprimir = document.querySelector('.imprimir');
        divImprimir.style.display = 'none';
    });
</script>
<style>
    .ht {
        margin-top: -1px !important;
    }

    .holerite {
        width: 749px;
        text-align: center;
        margin: auto;
    }

    .holerite td {
        border: 1px solid #000000;
    }
</style>
<?php
loadPlugin(array('select2'))
?>