<style>
    .batida {
        background: #ace4ff;
        border: 1px solid #ace4ff;
    }

    .batida_del span {
        background: #ff5454;
        display: block;
        width: 51px;
        padding: 2px 0;
    }

    .batida_del_view {
        background: #ff5454 !important;
        display: block !important;
        width: 51px !important;
        padding: 2px 0 !important;
    }

    .batida_data span {
        background: #c5be97;
        display: block;
        width: 51px;
        padding: 2px 0;
    }

    .batida_data_view {
        background: #c5be97 !important;
        display: block !important;
        width: 51px !important;
        padding: 2px 0 !important;
    }

    .batida_nat span {
        background: #95ffa2;
        display: block;
        width: 51px;
        padding: 2px 0;
    }

    .batida_nat_view {
        background: #95ffa2 !important;
        display: block !important;
        width: 51px !important;
        padding: 2px 0 !important;
    }

    .batida_atraso_view {
        background: #ffc319 !important;
        display: block !important;
        width: 51px !important;
        padding: 2px 0 !important;
    }

    .batida_falta_view {
        background: #ffe8a6 !important;
        display: block !important;
        width: 51px !important;
        padding: 2px 0 !important;
    }

    .batida_atitude {
        background: #fff176;
        display: block;
        width: 51px;
        padding: 2px 0;
    }

    .batida_just_excecao {
        background: #9c27b0;
        display: block;
        width: 51px;
        padding: 2px 0;
    }

    #selecctall {
        cursor: pointer;
        float: left;
        margin-top: -3px;
        margin-left: 12px;
        font-weight: bold;
        padding: 5px 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        display: flex;
        align-items: center;
        min-width: 185px;
    }
</style>
<script>
    $(document).ready(function() {
        $("#selecctall, #checkall").click(function() {
            if ($("input:checkbox").prop('checked')) {
                $("input:checkbox").prop('checked', false);
            } else {
                $("input:checkbox").prop('checked', true);
            }
        });
    });
</script>

<div class="container-fluid">

    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card m-0">

                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?= exibeMensagem(); ?>
                        </div>
                    </div>
                </div>

                <!-- FILTRO -->
                <div class="card m-0">
                    <div class="card-body">
                        <form action="" method="POST" name="form_secao" id="form_secao">
                            <div class="form-group row">

                                <div class="col-sm-2 text-right"><label for="opt_periodo" class=" col-form-label text-right text-left-sm"><span class="text-danger">*</span> Período</label></div>

                                <div class="col-sm-10">
                                <select class="select2  form-control form-control-sm " name="periodo" id="periodo" onchange="return carregaColaboradores()">
                                    <option value="">- selecione um período -</option>
                                    <?php if ($resPeriodo) : ?>
                                        <?php foreach ($resPeriodo as $key => $DadosPeriodo) : ?>
                                            <option data-iniciomensal="<?= dtBr($DadosPeriodo['INICIOMENSAL']); ?>" data-inicio="<?= date('Y-m-d', strtotime($DadosPeriodo['INICIOMENSAL'])) ?>" data-fim="<?= date('Y-m-d', strtotime($DadosPeriodo['FIMMENSAL'])) ?>" value="<?= dtEn($DadosPeriodo['INICIOMENSAL'], true) . '|' . dtEn($DadosPeriodo['FIMMENSAL'], true) . $DadosPeriodo['STATUSPERIODO']; ?>" <?= ($periodo == dtEn($DadosPeriodo['INICIOMENSAL'], true) . '|' . dtEn($DadosPeriodo['FIMMENSAL'], true)) ? " selected " : ""; ?>><?= dtBr($DadosPeriodo['INICIOMENSAL']) . ' à ' . dtBr($DadosPeriodo['FIMMENSAL']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                </div>

                                <div class="col-sm-2 text-right"><label for="secao" class="col-form-label text-right text-left-sm">Seção:</label></div>
                                <div class="col-sm-10 "><select name="secao" id="secao" class="form-control select2  form-control-sm " onchange="carregaColaboradores();">
                                    <option value="all">Todas as seções</option>
                                    <?php
                                    if ($listaSecaoUsuarioRM) {
                                        foreach ($listaSecaoUsuarioRM as $key => $Secao) {
                                            echo '<option value="' . $Secao['CODIGO'] . '" ' . (($codsecao == $Secao['CODIGO']) ? ' selected ' : '') . '>' . $Secao['DESCRICAO'] . ' - ' . $Secao['CODIGO'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select></div>
                                <div class="col-sm-2 text-right"><label for="funcionario" class=" col-form-label text-right text-left-sm">Colaborador:</label></div>
                                <div class="col-sm-10 "><select name="funcionario" id="funcionario" class="form-control select2 form-control-sm ">
                                    <option value="all">Todos</option>
                                    <?php if ($resFuncionarioSecao) : ?>
                                        <?php foreach ($resFuncionarioSecao as $key => $FuncionarioSecao) : ?>
                                            <option value="<?= $FuncionarioSecao['CHAPA']; ?>" <?php if ($chapa == $FuncionarioSecao['CHAPA']) {
                                                                                                    echo ' selected ';
                                                                                                } ?>><?= $FuncionarioSecao['NOME'] . ' - ' . $FuncionarioSecao['CHAPA']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select></div>

                                <div class="col-sm-12 text-center">
                                    <button style="margin-left: 20px;" class="btnpeq btn-sm btn-success bteldorado_1" type="button" onclick="return filtroSecao()"><i class="fa fa-filter"></i> Filtrar</button>
                                    <button style="margin-left: 20px;" class="btnpeq btn-sm btn-danger bteldorado_2" type="button" onclick="return limpaFiltro()"> Limpar</button>
                                </div>
                            </div>
                        </form>
                        <!-- LEGENDA -->
                        <div class="col-12">
                            <hr>
                            <b>Legenda:</b>
                            <span style="display: inline-block;" class="mb-1"><span class="batida bteldorado_3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Batida inserida &nbsp;&nbsp;&nbsp;</span>
                            <span style="display: inline-block;" class="mb-1"><span class="batida_atraso_view bteldorado_4" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Abono atraso &nbsp;&nbsp;&nbsp;</span>
                            <span style="display: inline-block;" class="mb-1"><span class="batida_falta_view bteldorado_5" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Abono falta &nbsp;&nbsp;&nbsp;</span>
                            <span style="display: inline-block;" class="mb-1"><span class="batida_atitude bteldorado_6" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Altera atitude &nbsp;&nbsp;&nbsp;</span>
                            <!--<span style="display: inline-block;" class="mb-1"><span class="batida_just_excecao" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Justificativa de Exceção &nbsp;&nbsp;&nbsp;</span>-->
                        </div>
                    </div>
                </div>

                <!-- TABELA COM RESULTADOS -->
                <?php
                $chapaUser = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
                if ($nomeFunc) {

                    //-----------------------------------------------
                    // regra para bloqueio do ponto
                    //-----------------------------------------------
                    $periodo_bloqueado = false;
                    // if ($statusPeriodo == 1) {
                    //     if ($isGestor) {
                    //         $periodo_bloqueado = (dtEn($EspelhoConfiguracao[0]['limite_gestor'], true) < date('Y-m-d')) ? true : false;
                    //         if ($gestorPossuiExcecao && $periodo_bloqueado) {
                    //             $periodo_bloqueado = (dtEn($gestorPossuiExcecao, true) < date('Y-m-d')) ? true : false;
                    //         }
                    //     } else {
                    //         $periodo_bloqueado = (dtEn($EspelhoConfiguracao[0]['limite_funcionario'], true) < date('Y-m-d')) ? true : false;
                    //     }
                    // }
                    if ($statusPeriodo == 0) $periodo_bloqueado = true;
                    //-----------------------------------------------

                    echo '
                    <div class="row mr-2">
                        <div class="col-12 text-right">
                            <form action="' . base_url('ponto/aprova/excel') . '" method="post" name="form_excel" target="_blank">
                                <button class="btn btn-success btn-xxs bteldorado_1" type="submit"><i class="fas fa-file-excel"></i> Exportar Excel</button>
                                <input type="hidden" name="secao" value="' . $_POST['secao'] . '">
                                <input type="hidden" name="tipo_abono" value="' . $_POST['tipo_abono'] . '">
                                <input type="hidden" name="ft_legenda" value="' . $_POST['ft_legenda'] . '">
                                <input type="hidden" name="ft_status" value="' . $_POST['ft_status'] . '">
                                <input type="hidden" name="funcionario" value="' . $_POST['funcionario'] . '">
                                <input type="hidden" name="motivo_reprova" value="' . $_POST['motivo_reprova'] . '">
                                <input type="hidden" name="periodo" value="' . $_POST['periodo'] . '">
                                <input type="hidden" name="statusPeriodo" value="' . $_POST['statusPeriodo'] . '">
                            </form>
                        </div>
                    </div>';

                    echo '<form action="" method="post" name="apr_batida" id="form1">';

                    echo '<div class="card m-0">';
                    if (!$periodo_bloqueado) {
                        echo '<div class="row mt-3">';
                        echo '<div class="col-6 text-center"><button onclick="return reprovaBatida()" type="button" class="btnpeq btn-sm btn-danger bteldorado_2"><i class="far fa-thumbs-down"></i><br> Reprova Selecionados</button></div>';
                        echo '<div class="col-6 text-center"><button onclick="return aprovaBatida()" type="button" class="btnpeq btn-sm btn-success bteldorado_1"><i class="far fa-thumbs-up"></i><br> Aprova Selecionados</button></div>';
                        echo '</div>';
                    }
                    echo '<div class="card-body" style="display: flex; align-items: center;">';



                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    // echo '<label for="sel_todos">';
                    // echo '<th><input id="sel_todos" data-check="checado" type="checkbox"> SELECIONAR TODOS</th>';
                    echo '<div class="row d-flex">
                        <span id="selecctall"><input type="checkbox" id="checkall"><span style="margin-left: 5px;">Selecionar Todos</span> </span>
                    </div>';
                    echo '</label>';
                    echo '</thead>';
                    echo '</table>';
                    echo '</div>';
                    echo '</div>';

                    foreach ($nomeFunc as $chapaFunc) {


                        // exit(substr(trim($chapaFunc), 0, 9));
                        if(substr(trim($chapaFunc), 0, 9) == $chapaUser) continue;

                        echo '<div class="card m-0">';
                        echo '<div class="card-body">';
                        echo '<h4 class="page-title">' . $chapaFunc . '</h4>';

                        echo '<table class="table table-sm table-bordered table-responsive_mobile">';
                        echo '<thead>';
                        if (!$periodo_bloqueado) {
                            echo '<th><input data-check class="data_check_func" type="checkbox" data-chapa="' . substr($chapaFunc, 0, 9) . '"></th>';
                        } else {
                            echo '<th></th>';
                        }
                        // mobile
                        echo '<th><b></b></th>';
                        echo '<th class="y-mobile-cell d-none"><b>DADOS</b></th>';



                        echo '<th class="n-mobile-cell" width="140"><b>MOVIMENTO</b></th>';
                        echo '<th class="n-mobile-cell" align="center" width="90"><b>DATA</b></th>';
                        echo '<th class="n-mobile-cell" align="center" width="130"><b>BATIDA</b></th>';
                        echo '<th class="n-mobile-cell" align="center" width="120"><b>ABONO DATA INICIO</b></th>';
                        echo '<th class="n-mobile-cell" align="center" width="120"><b>ABONO DATA FIM</b></th>';
                        echo '<th class="n-mobile-cell" align="center" width="80"><b>TOTAL HORAS</b></th>';
                        echo '<th class="n-mobile-cell" width="230"><b>TIPO</b></th>';
                        echo '<th class="n-mobile-cell" width="230"><b>JUSTIFICATIVA</b></th>';
                        echo '<th class="n-mobile-cell" class="text-center"><b>ANEXO</b></th>';
                        echo '<th class="n-mobile-cell" class="text-center"><b>SOLICITANTE</b></th>';
                        echo '</thead>';

                        if ($objListaBatidaApr) {
                            $aa = 0;
                            foreach ($objListaBatidaApr as $idx => $value) {
                                if ($objListaBatidaApr[$idx]['nomechapa'] == $chapaFunc) {


                                    // calcula qtde de horas
                                    $data_fim = "";
                                    $total_horas = "";
                                    if (strlen(trim($objListaBatidaApr[$idx]['abn_codabono'] ?? '')) > 0) {
                                        if ($objListaBatidaApr[$idx]['abn_horafim'] > $objListaBatidaApr[$idx]['abn_horaini']) {

                                            $data_fim    = dtEn($objListaBatidaApr[$idx]['dtponto'], true) . 'T00:00:00';
                                            $total_horas = ($objListaBatidaApr[$idx]['abn_horafim'] - $objListaBatidaApr[$idx]['abn_horaini']);
                                        } else {

                                            $dataTermino = new \DateTime(dtEn($objListaBatidaApr[$idx]['dtponto'], true));
                                            $dataTermino->add(new \DateInterval('P1D'));
                                            $data_fim    = $dataTermino->format('Y-m-d');
                                            $total_horas = (($objListaBatidaApr[$idx]['abn_horafim'] + 1440) - $objListaBatidaApr[$idx]['abn_horaini']);
                                        }
                                    }
                                    echo '<tr class="tbadmlistalin">';

                                    if (!$periodo_bloqueado) {
                                        echo '<td width="20" align="center">
                                        <input type="checkbox" name="idbatida[]"  data-chapa="' . substr($chapaFunc, 0, 9) . '" value="' . $objListaBatidaApr[$idx]['chapa'] . '|' . dtEn($objListaBatidaApr[$idx]['dtponto'], true) . '|' . $objListaBatidaApr[$idx]['id'] . '">
                                        </td>';
                                    } else {
                                        echo '<td width="20" align="center"></td>';
                                    }
                                    echo '<td class="y-mobile-cell d-none" style="font-size:11px;" width="40">';
                                    switch ($objListaBatidaApr[$idx]['movimento']) {
                                        case 1:
                                            echo '<span class="batida bteldorado_3 text-center" style="display:block !important; width: 100% !important;">Inclusão de batida</span>';
                                            break;
                                        case 2:
                                            echo '<span class="batida_del_view text-center" style="display:block !important; width: 100% !important;">Exclusão de batida</span>';
                                            break;
                                        case 3:
                                            echo '<span class="batida_nat_view text-center" style="display:block !important; width: 100% !important;">Alteração de natureza</span>';
                                            break;
                                        case 4:
                                            echo '<span class="batida_data_view text-center" style="display:block !important; width: 100% !important;">Alteração jornada referência</span>';
                                            break;
                                        case 5:
                                            echo '<span class="batida_atraso_view bteldorado_4 text-center" style="display:block !important; width: 100% !important;">Abono de atrasos</span>';
                                            break;
                                        case 6:
                                            echo '<span class="batida_falta_view bteldorado_5 text-center" style="display:block !important; width: 100% !important;">Abono de faltas</span>';
                                            break;
                                        case 7:
                                            echo '<span class="batida_just_excecao text-center" style="display:block !important; width: 100% !important;">Justificativa de Exceção</span>';
                                            break;
                                        case 8:
                                            echo '<span class="batida_atitude bteldorado_6 text-center" style="display:block !important; width: 100% !important;">Altera Atitude</span>';
                                            break;
                                        case 9:
                                            echo '<span class="batida_falta_view text-center" style="display:block !important; width: 100% !important;">Falta não remunerada</span>';
                                            break;
                                    }
                                    echo '</td>';

                                    echo '<td class="y-mobile-cell d-none">';

                                    // inclusão de batida
                                    echo '<div class="row">';
                                    if ($objListaBatidaApr[$idx]['movimento'] != 5 && $objListaBatidaApr[$idx]['movimento'] != 6 && $objListaBatidaApr[$idx]['movimento'] != 8 && $objListaBatidaApr[$idx]['movimento'] != 7 && $objListaBatidaApr[$idx]['movimento'] != 9) {
                                        echo '<div class="col-6 text-center"><strong>Data</strong><br>' . date('d/m/Y', strtotime($objListaBatidaApr[$idx]['dtponto'])) . '</div>';
                                        echo '<div class="col-6 text-center"><strong>Batida</strong><br>' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['batida'])) . '</div>';
                                        echo '<div class="col-12 text-center"><strong>Justificativa</strong><br>' . $objListaBatidaApr[$idx]['motivo'] . '</div>';
                                        echo '<div class="col-12 text-center"><strong>Solicitante</strong><br>' . $objListaBatidaApr[$idx]['solicitante'] . '</div>';
                                        if (strlen(trim($objListaBatidaApr[$idx]['justificativa_abono_tipo'] ?? '')) > 0) echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong><br>' . $objListaBatidaApr[$idx]['justificativa_abono_tipo'] . '</div>';

                                        if (strlen($objListaBatidaApr[$idx]['possui_anexo'] ?? '') > 0) {
                                            echo '<div class="col-12 text-center pt-2"><strong>Anexo</strong><br>';
                                            echo '<button type="button" onclick="carregaVisualizador(' . $objListaBatidaApr[$idx]['id'] . ',\'' . substr($chapaFunc, 9) . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" aria-hidden="true"></i> </button>';
                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $objListaBatidaApr[$idx]['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                            echo '</div>';
                                        }
                                    }
                                    // abono
                                    if ($objListaBatidaApr[$idx]['movimento'] == 5 || $objListaBatidaApr[$idx]['movimento'] == 6 || $objListaBatidaApr[$idx]['movimento'] == 9) {
                                        echo '<div class="col-6 text-center"><strong>Data Inicio</strong><br>' . date('d/m/Y', strtotime($objListaBatidaApr[$idx]['dtponto'])) . ' ' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['abn_horaini'])) . '</div>';
                                        echo '<div class="col-6 text-center"><strong>Data Fim</strong><br>' . date('d/m/Y', strtotime($data_fim)) . ' ' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['abn_horafim'])) . '</div>';
                                        echo '<div class="col-6 text-center pt-2"><strong>Total de Horas</strong><br>' . m2h($total_horas, 4) . '</div>';
                                        echo '<div class="col-6 text-center pt-2"><strong>Tipo de Abono</strong><br>' . $objListaBatidaApr[$idx]['abn_codabono'] . ' - ' . (($objListaBatidaApr[$idx]['movimento'] == 9) ? 'FALTA NÃO REMUNERADA' : extrai_valor($resAbonos, $objListaBatidaApr[$idx]['abn_codabono'], 'CODIGO', 'DESCRICAO')) . '</div>';
                                        echo '<div class="col-12 text-center"><strong>Solicitante</strong><br>' . $objListaBatidaApr[$idx]['solicitante'] . '</div>';
                                        if (strlen(trim($objListaBatidaApr[$idx]['justificativa_abono_tipo'] ?? '')) > 0) echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong> ' . $objListaBatidaApr[$idx]['justificativa_abono_tipo'] . '</div>';
                                        if (strlen($objListaBatidaApr[$idx]['possui_anexo'] ?? '') > 0) {
                                            echo '<div class="col-12 text-center pt-2"><strong>Anexo</strong><br>';
                                            echo '<button type="button" onclick="carregaVisualizador(' . $objListaBatidaApr[$idx]['id'] . ',\'' . substr($chapaFunc, 9) . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" aria-hidden="true"></i> </button>';
                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $objListaBatidaApr[$idx]['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                            echo '</div>';
                                        }
                                    }
                                    // altera atitude
                                    if ($objListaBatidaApr[$idx]['movimento'] == 8 || $objListaBatidaApr[$idx]['movimento'] == 7) {
                                        echo '<div class="col-6 text-center"><strong>Data</strong><br>' . date('d/m/Y', strtotime($objListaBatidaApr[$idx]['atitude_dt'])) . '</div>';
                                        echo '<div class="col-6 text-center"><strong>Horas</strong><br>' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['atitude_fim'])) . '</div>';
                                        if ($objListaBatidaApr[$idx]['movimento'] == 8) {
                                            echo '<div class="col-12 text-center pt-2"><strong>Tipo Atitude</strong><br>' . ($objListaBatidaApr[$idx]['atitude_tipo'] == 1 ? 'Compensar (Fica BH)' : 'Descontar no pagto.') . '</div>';
                                        } else {
                                            echo '<div class="col-12 text-center pt-2"><strong>Tipo Atitude</strong><br>Atraso Não Remunerado</div>';
                                        }
                                        echo '<div class="col-12 text-center"><strong>Solicitante</strong><br>' . $objListaBatidaApr[$idx]['solicitante'] . '</div>';
                                        if ($objListaBatidaApr[$idx]['movimento'] == 8) {
                                            if (strlen(trim($objListaBatidaApr[$idx]['justificativa_abono_tipo'] ?? '')) > 0) echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong> ' . $objListaBatidaApr[$idx]['atitude_justificativa'] . '</div>';
                                        } else {
                                            echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong> Atraso Não Remunerado</div>';
                                        }
                                        if (strlen($objListaBatidaApr[$idx]['possui_anexo'] ?? '') > 0) {
                                            echo '<div class="col-12 text-center pt-2"><strong>Anexo</strong><br>';
                                            echo '<button type="button" onclick="carregaVisualizador(' . $objListaBatidaApr[$idx]['id'] . ',\'' . substr($chapaFunc, 9) . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" aria-hidden="true"></i> </button>';
                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $objListaBatidaApr[$idx]['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                            echo '</div>';
                                        }
                                    }
                                    echo '</div>';

                                    echo '</td>';




                                    echo '<td class="n-mobile-cell" width="25" align="center">';
                                    switch ($objListaBatidaApr[$idx]['movimento']) {
                                        case 1:
                                            echo '<span class="batida bteldorado_3" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 2:
                                            echo '<span class="batida_del_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 3:
                                            echo '<span class="batida_nat_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 4:
                                            echo '<span class="batida_data_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 5:
                                            echo '<span class="batida_atraso_view bteldorado_4" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 6:
                                            echo '<span class="batida_falta_view bteldorado_5" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 7:
                                            echo '<span class="batida_just_excecao" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 8:
                                            echo '<span class="batida_atitude bteldorado_6" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 9:
                                            echo '<span class="batida_falta_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                    }



                                    if ($aa == 0) {
                                        echo '<input type="hidden" name="func_chapa" value="' . $objListaBatidaApr[$idx]['chapa'] . '"><input type="hidden" name="func_cpf" value="' . $objListaBatidaApr[$idx]['cpf'] . '"><input type="hidden" name="func_password" id="func_password_' . $objListaBatidaApr[$idx]['chapa'] . '" value="">';
                                    }

                                    echo '</td>';
                                    echo '<td class="n-mobile-cell" style="font-size: 11px;">';

                                    switch ($objListaBatidaApr[$idx]['movimento']) {
                                        case 1:
                                            echo 'Inclusão de batida';
                                            break;
                                        case 2:
                                            echo 'Exclusão de batida';
                                            break;
                                        case 3:
                                            echo 'Alteração de natureza';
                                            break;
                                        case 4:
                                            echo 'Alteração jornada referência';
                                            break;
                                        case 5:
                                            echo 'Abono de atrasos';
                                            break;
                                        case 6:
                                            echo 'Abono de faltas';
                                            break;
                                        case 7:
                                            echo 'Justificativa de Exceção';
                                            break;
                                        case 8:
                                            echo 'Altera Atitude';
                                            break;
                                        case 9:
                                            echo 'Falta não remunerada';
                                            break;
                                    }

                                    echo '</td>';
                                    echo '<td class="n-mobile-cell" align="center">' . date('d/m/Y', strtotime($objListaBatidaApr[$idx]['dtponto'])) . '</td>';

                                    if ($objListaBatidaApr[$idx]['movimento'] != 8 && $objListaBatidaApr[$idx]['movimento'] != 7) {
                                        echo '<td class="n-mobile-cell" align="center">' . (($objListaBatidaApr[$idx]['abn_codabono'] == '') ? sprintf("%05s", m2h($objListaBatidaApr[$idx]['batida'])) : '') . '</td>';
                                        echo '<td class="n-mobile-cell" align="center">' . (($objListaBatidaApr[$idx]['abn_codabono'] <> '') ? date('d/m/Y', strtotime($objListaBatidaApr[$idx]['dtponto'])) . ' ' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['abn_horaini'])) : '') . '</td>';
                                        echo '<td class="n-mobile-cell" align="center">' . (($objListaBatidaApr[$idx]['abn_codabono'] <> '') ? date('d/m/Y', strtotime($data_fim)) . ' ' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['abn_horafim'])) : '') . '</td>';
                                        echo '<td class="n-mobile-cell" align="center">' . (($objListaBatidaApr[$idx]['abn_totalhoras'] <> '') ?  m2h($total_horas, 4)  : '') . '</td>';
                                        echo '<td class="n-mobile-cell">';
                                        if (strlen(trim($objListaBatidaApr[$idx]['abn_codabono'] ?? '')) > 0) {
                                            echo $objListaBatidaApr[$idx]['abn_codabono'] . ' - ' . (($objListaBatidaApr[$idx]['movimento'] == 9) ? 'FALTA NÃO REMUNERADA' : extrai_valor($resAbonos, $objListaBatidaApr[$idx]['abn_codabono'], 'CODIGO', 'DESCRICAO'));
                                        }
                                        echo '</td>';
                                        echo '<td class="n-mobile-cell">' . (strlen(trim($objListaBatidaApr[$idx]['justificativa_abono_tipo'])) > 0 ? $objListaBatidaApr[$idx]['justificativa_abono_tipo'] : $objListaBatidaApr[$idx]['motivo']) . '</td>';
                                    } else {

                                        echo '<td class="n-mobile-cell" align="left"></td>';
                                        echo '<td class="n-mobile-cell" align="left"></td>';
                                        echo '<td class="n-mobile-cell" align="left"></td>';

                                        echo '<td class="n-mobile-cell" align="center">' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['atitude_fim'])) . '</td>';
                                        if ($objListaBatidaApr[$idx]['movimento'] == 8) {
                                            echo '<td class="n-mobile-cell" align="left">' . ($objListaBatidaApr[$idx]['atitude_tipo'] == 1 ? 'Compensar (Fica BH)' : 'Descontar no pagto.') . '</td>';
                                            echo '<td class="n-mobile-cell" align="left">' . $objListaBatidaApr[$idx]['atitude_justificativa'] . '</td>';
                                        } else {
                                            echo '<td class="n-mobile-cell" align="left">Atraso Não Remunerado</td>';
                                            echo '<td class="n-mobile-cell" align="left">Atraso Não Remunerado</td>';
                                        }
                                    }
                                    echo '<td class="text-center n-mobile-cell">';

                                    if (strlen($objListaBatidaApr[$idx]['possui_anexo'] ?? '') > 0) {
                                        echo '<button type="button" onclick="carregaVisualizador(' . $objListaBatidaApr[$idx]['id'] . ',\'' . substr($chapaFunc, 9) . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" aria-hidden="true"></i> </button>';
                                        echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $objListaBatidaApr[$idx]['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                    }
                                    echo '</td>';
                                    echo '<td class="n-mobile-cell" align="left" style="font-size:11px;">' . $objListaBatidaApr[$idx]['solicitante'] . '</td>';
                                    echo '</tr>';
                                    $aa++;
                                }
                            }
                        }

                        echo '</table>';
                        echo '</div>';
                        echo '</div>';

                        echo '</fieldset>';
                        echo '<input type="hidden" name="act" data-act>';
                    }
                    echo '</br>';

                    echo '<div class="row">';
                    echo '<input type="hidden" name="motivo_reprova" id="motivo_reprova">';
                    echo '<input type="hidden" name="periodo" value="' . $periodo . '">';
                    echo '<input type="hidden" name="statusPeriodo" value="' . $statusPeriodo . '">';
                    echo '</div></form>';
                    echo '</br>';
                    echo '</br>';
                } else {
                    echo '<div class="card-body border-bottom">
                                                    <div class="alert alert-warning2 border-0" role="alert">
                                                        <i class="fas fa-info-circle"></i> Nenhum registro encontrado.
                                                    </div>
                                                </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    const carregaVisualizador = (id, nome) => {

        $("#titulo_modal").html('Anexo enviado por | ' + nome);
        $("#conteudo_modal").html('<div class="text-center"><div class="spinner-border thumb-md text-primary" role="status"></div></div>');
        $(".modal_visualizador").modal('show');
        openLoading();
        var hFrame = $(".modal-body").height();

        $("#iframe_preview").html('<iframe id="iframe" src="/ponto/preview/index/' + id + '" frameborder="0" width="100%" height="' + (hFrame - 50) + 'px" allowfullscreen></iframe>');

        var myTimeout = setTimeout(function() {
            openLoading(true);
            var wFrame = $("#iframe").width();
            $('#iframe').contents().find("html").find('img').attr('style', 'max-width:' + wFrame + 'px;');
            clearTimeout(myTimeout);
        }, 4000);

    }
</script>

<!-- modal visualizador -->
<div class="modal modal_visualizador" tabindex="-1" role="dialog" aria-labelledby="modal_visualizador" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="titulo_modal"></h5>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <h4>Preview Do Anexo</h4>
                        <div id="iframe_preview"></div>
                    </div>
                </div>

            </div>
            <div class="modal-footer"><button type="button" class="btn btn-danger btn-xxs" data-dismiss="modal">Fechar</button></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal visualizador -->

<style>
    .modal_visualizador {
        padding: 10px !important;
    }

    .modal-dialog-full {
        width: 100%;
        height: 100%;
        max-width: 1600px;
        margin: auto;
        padding: 0;
    }

    .modal-content-full {
        height: auto;
        min-height: 100%;
        border-radius: 0;
    }
</style>

<script>
    function filtroSecao() {
        var periodo = document.getElementById("periodo").value;
        if (periodo == "") {
            exibeAlerta('error', 'Período não informado.');
            return false;
        }
        var secao = document.getElementById('secao').value;
        if (secao == '') {
            alert('Selecione uma seção');
            return false;
        }
        document.getElementById('form_secao').submit();
    }

    function limpaFiltro() {
        $('#secao').val('all');
        $('#tipo_abono').val('');
    }


    $('.data_check_func').on('click', function() {
        var chapa = $(this).attr('data-chapa');
        if ($(this).prop('checked') == true) {
            console.log('TRUE');
            $("[data-chapa=" + chapa + "]").prop('checked', true);
        } else {
            console.log('FALSE');
            $("[data-chapa=" + chapa + "]").prop('checked', false);
        }
    })

    function aprovaBatida() {
        var r = confirm('Deseja realmente APROVAR as batidas selecionadas.');
        if (r == true) {
            openLoading();
            $('[data-act]').val('apr');
            document.getElementById('form1').submit();
        }
        return false;
    }

    function aprovaBatidaTot() {
        var r = confirm('Deseja realmente aprovar as batidas selecionadas.');
        if (r == true) {
            openLoading();
            $('[data-act]').val('apr');
            document.getElementById('form1').submit();
        }
        return false;
    }

    function cancelaApr() {
        $(".popup_bg").fadeOut(100);
        $(".popup_content").fadeOut(100);
        $("#nome_func").html('');
        $("#func_chapa").val('');
    }

    function autenticaFunc() {
        var CHAPA = $("#func_chapa").val();
        var SENHA = $("#func_senha").val();
        if (CHAPA == '') {
            alert('Erro: Problema com a CHAPA.');
            return false;
        }
        if (SENHA == '') {
            alert('Erro: Senha não informada.');
            return false;
        }
        $("#func_password_" + CHAPA).val(SENHA);
        document.getElementById('chapa_' + CHAPA).submit();
    }

    function reprovaBatida() {

        if ($("[data-chapa]:checked").length <= 0) {
            exibeAlerta('warning', 'Nenhuma registro selecionada.');
            return false;
        }


        Swal.fire({
            icon: 'info',
            title: 'Selecione o motivo para reprovar esse registro:',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Confirmar e reprovar`,
            denyButtonText: `Cancelar`,
            showCancelButton: false,
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            input: 'select',
            inputOptions: {
                <?php if ($resMotivoReprova) : ?>
                    <?php foreach ($resMotivoReprova as $key => $MotivoReprova) : ?> '<?= $MotivoReprova['descricao'] ?>': '<?= $MotivoReprova['descricao'] ?>',
                    <?php endforeach; ?>
                <?php endif; ?>
            },
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
                    openLoading();
                    $('[data-act]').val('rep');
                    $('#motivo_reprova').val(result.value);
                    document.getElementById('form1').submit();

                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }
            }
        });

    }
    const carregaFuncionariosSecao = (codSecao) => {

        openLoading();

        $("#funcionario").html('<option value="">-- selecione um funcionário --</option>').trigger('change');
        if (codSecao == '') {
            openLoading(true);
            return false;
        }

        $.ajax({
            url: "<?= base_url('ponto/aprova/action/listar_funcionarios_secao') ?>",
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

        var selPeriodo = $("#periodo").val();
        if(selPeriodo == ''){
            exibeAlerta('warning', 'Período não selecionado.');
            return;
        }

        var periodo = $("#periodo option:selected").attr('data-iniciomensal');
        var codsecao = $("#secao").val() ?? null;
        if(codsecao == 'all') codsecao = null;

        $("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');

        $.ajax({
            url: "<?= base_url('ponto/espelho/action/carrega_colaboradores') ?>",
            type: 'POST',
            data: {
                'codsecao'    : codsecao,
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
</script>
<?php loadPlugin(array('select2')); ?>