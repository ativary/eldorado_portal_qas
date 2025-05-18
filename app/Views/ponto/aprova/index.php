<script>
$(document).ready(function(){
    $(".button-menu-mobile").click();
});
</script>
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
        $("#checkall").on('click', function(e){
            if($(this).prop('checked')){
                $("[type=checkbox]").prop('checked', true);
            }else{
                $("[type=checkbox]").prop('checked', false);
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
                                <select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> class="select2  form-control form-control-sm " name="periodo" id="periodo" onchange="return carregaColaboradores()">
                                    <option value="">- selecione um período -</option>
                                    <?php if ($resPeriodo) : ?>
                                        <?php foreach ($resPeriodo as $key => $DadosPeriodo) : ?>
                                            <option data-iniciomensal="<?= dtBr($DadosPeriodo['INICIOMENSAL']); ?>" data-inicio="<?= date('Y-m-d', strtotime($DadosPeriodo['INICIOMENSAL'])) ?>" data-fim="<?= date('Y-m-d', strtotime($DadosPeriodo['FIMMENSAL'])) ?>" value="<?= dtEn($DadosPeriodo['INICIOMENSAL'], true) . '|' . dtEn($DadosPeriodo['FIMMENSAL'], true) . $DadosPeriodo['STATUSPERIODO']; ?>" <?= ($periodo == dtEn($DadosPeriodo['INICIOMENSAL'], true) . '|' . dtEn($DadosPeriodo['FIMMENSAL'], true)) ? " selected " : ""; ?>><?= dtBr($DadosPeriodo['INICIOMENSAL']) . ' à ' . dtBr($DadosPeriodo['FIMMENSAL']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                </div>

                                <div class="col-sm-2 text-right"><label for="opt_tipo" class=" col-form-label text-right text-left-sm"><span class="text-danger">*</span> Categoria</label></div>
                                <div class="col-sm-10">
                                    <select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> name="filtro_tipo" id="filtro_tipo" class="form-control select2 form-control-sm">
                                      <option value="">- selecione uma categoria -</option>
                                      <option <?= ($filtro_tipo == "ponto") ? 'selected' : ''; ?> value="ponto">&bull; Ponto</option>
                                      <option <?= ($filtro_tipo == "art61") ? 'selected' : ''; ?> value="art61">&bull; Artigo.61</option>
                                    </select>
                                </div>

                                <div class="col-sm-2 text-right"><label for="filtro_tipo2" class=" col-form-label text-right text-left-sm">Tipo</label></div>
                                <div class="col-sm-10">
                                    <select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> name="filtro_tipo2" id="filtro_tipo2" class="form-control select2 form-control-sm">
                                        <option value="">Todos os tipos</option>
                                        <option <?= ($filtro_tipo2 == "1") ? 'selected' : ''; ?> value="1">Inclusão de batida</option>
                                        <option <?= ($filtro_tipo2 == "2") ? 'selected' : ''; ?> value="2">Exclusão de batida</option>
                                        <option <?= ($filtro_tipo2 == "3") ? 'selected' : ''; ?> value="3">Alteração de natureza</option>
                                        <option <?= ($filtro_tipo2 == "4") ? 'selected' : ''; ?> value="4">Alteração jornada referência</option>
                                        <option <?= ($filtro_tipo2 == "5") ? 'selected' : ''; ?> value="5">Abono de atrasos'</option>
                                        <option <?= ($filtro_tipo2 == "6") ? 'selected' : ''; ?> value="6">Abono de faltas</option>
                                        <option <?= ($filtro_tipo2 == "7") ? 'selected' : ''; ?> value="7">Justificativa de exceção</option>
                                        <option <?= ($filtro_tipo2 == "8") ? 'selected' : ''; ?> value="8">Altera atitude</option>
                                        <option <?= ($filtro_tipo2 == "9") ? 'selected' : ''; ?> value="9">Falta não remunerada</option>
                                        <option <?= ($filtro_tipo2 == "21") ? 'selected' : ''; ?> value="21">Troca de escala</option>
                                        <option <?= ($filtro_tipo2 == "22") ? 'selected' : ''; ?> value="22">Troca de dia</option>
                                    </select>
                                </div>

                                <div class="col-sm-2 text-right"><label for="opt_tipo" class=" col-form-label text-right text-left-sm">Filial</label></div>
                                <div class="col-sm-10">
                                    <select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> name="filtro_filial" id="filtro_filial" class="form-control select2 form-control-sm">
                                        <option value="">Todas as filiais</option>
                                        <?php if($resFilial): ?>
                                                <?php foreach($resFilial as $key => $Filial): ?>
                                                    <option value="<?= $Filial['CODFILIAL']; ?>" <?= (($filtro_filial ?? "") == $Filial['CODFILIAL']) ? " selected " : ""; ?>><?= $Filial['CODFILIAL'].' - '.$Filial['NOMEFILIAL']; ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-sm-2 text-right"><label for="secao" class="col-form-label text-right text-left-sm">Seção:</label></div>
                                <div class="col-sm-10 "><select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> name="secao" id="secao" class="form-control select2  form-control-sm " onchange="return carregaColaboradores()">
                                    <option value="all">Todas as seções</option>
                                    <?php
                                    if ($listaSecaoUsuarioRM) {
                                        foreach ($listaSecaoUsuarioRM as $key => $Secao) {
                                            echo '<option value="' . $Secao['CODIGO'] . '" ' . (($codsecao == $Secao['CODIGO']) ? ' selected ' : '') . '>' . $Secao['DESCRICAO'] . ' - ' . $Secao['CODIGO'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select></div>

                                <div class="col-sm-2 text-right"><label for="ccusto" class="col-form-label text-right text-left-sm">Centro de Custo:</label></div>
                                <div class="col-sm-10 "><select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> name="ccusto" id="ccusto" class="form-control select2  form-control-sm " onchange="return carregaColaboradores()">
                                    <option value="all">Todos os centros de custo</option>
                                    <?php
                                    if ($listaCCustoUsuarioRM) {
                                        foreach ($listaCCustoUsuarioRM as $key => $CCusto) {
                                            echo '<option value="' . $CCusto['CCUSTO'] . '" ' . (($codccusto == $CCusto['CCUSTO']) ? ' selected ' : '') . '>' . $CCusto['DESC_CCUSTO'] . ' - ' . $CCusto['CCUSTO'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select></div>

                                <div class="col-sm-2 text-right"><label for="funcionario" class=" col-form-label text-right text-left-sm">Colaborador:</label></div>
                                <div class="col-sm-10 "><select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> name="funcionario" id="funcionario" class="form-control select2 form-control-sm ">
                                    <option value="all">Todos</option>
                                    <?php if ($resFuncionarioSecao) : ?>
                                        <?php foreach ($resFuncionarioSecao as $key => $FuncionarioSecao) : ?>
                                            <option value="<?= $FuncionarioSecao['CHAPA']; ?>" <?php if ($chapa == $FuncionarioSecao['CHAPA']) {
                                                                                                    echo ' selected ';
                                                                                                } ?>><?= $FuncionarioSecao['NOME'] . ' - ' . $FuncionarioSecao['CHAPA']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select></div>

                                <div class="col-sm-2 text-right"><label for="filtro_legenda" class=" col-form-label text-right text-left-sm">Legenda</label></div>
                                <div class="col-sm-10">
                                    <select <?= (!$acessoPermitido) ? 'disabled' : ''; ?> name="filtro_legenda" id="filtro_legenda" class="form-control select2 form-control-sm">
                                        <option value="">Todas as legendas</option>
                                        <option <?= ($filtro_legenda == "10") ? 'selected' : ''; ?> value="10">Pend/Ação Gestor</option>
                                        <option <?= ($filtro_legenda == "2") ? 'selected' : ''; ?> value="2">Pend/Ação RH</option>
                                    </select>
                                </div>

                                <div class="col-sm-12 text-center">
                                    <button <?= (!$acessoPermitido) ? 'disabled' : ''; ?> style="margin-left: 20px;" class="btnpeq btn-sm btn-success bteldorado_1" type="button" onclick="return filtroSecao()"><i class="fa fa-filter"></i> Filtrar</button>
                                    <button <?= (!$acessoPermitido) ? 'disabled' : ''; ?> style="margin-left: 20px;" class="btnpeq btn-sm btn-danger bteldorado_2" type="button" onclick="return limpaFiltro()"> Limpar</button>
                                </div>
                            </div>
                        </form>
                        <!-- LEGENDA -->
                        <div class="col-12">
                            <hr>
                            <b>Legenda:</b>
                            <span class="badge badge-warning">Pend/Ação Gestor</span>
                            <span class="badge badge-info">Pend/Ação RH</span>
                            <!-- <span style="display: inline-block;" class="mb-1"><span class="batida bteldorado_3">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Batida inserida &nbsp;&nbsp;&nbsp;</span>
                            <span style="display: inline-block;" class="mb-1"><span class="batida_atraso_view bteldorado_4" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Abono atraso &nbsp;&nbsp;&nbsp;</span>
                            <span style="display: inline-block;" class="mb-1"><span class="batida_falta_view bteldorado_5" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Abono falta &nbsp;&nbsp;&nbsp;</span>
                            <span style="display: inline-block;" class="mb-1"><span class="batida_atitude bteldorado_6" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Altera atitude &nbsp;&nbsp;&nbsp;</span> -->
                            <!--<span style="display: inline-block;" class="mb-1"><span class="batida_just_excecao" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Justificativa de Exceção &nbsp;&nbsp;&nbsp;</span>-->
                        </div>
                    </div>
                    
                    <div class="card-body">
                    <div class="row mb-3" style="margin-top: -62px;">
                        <div class="col-12 text-right"><button onclick="return excel()" type="button" class="btnpeq btn-sm btn-success bteldorado_1"><i class="mdi mdi-file-excel"></i> Exportar excel</button></div>
                    </div>
                    <?php
                    if ($acessoPermitido) {
                        echo '<div class="row mt-3 mb-3">';
                        echo '<div class="col-6 text-left"><button onclick="return reprovaBatida()" type="button" class="btnpeq btn-sm btn-danger bteldorado_2"><i class="far fa-thumbs-down"></i> Reprova Selecionados</button></div>';
                        echo '<div class="col-6 text-right"><button onclick="return aprovaBatida()" type="button" class="btnpeq btn-sm btn-success bteldorado_1"><i class="far fa-thumbs-up"></i> Aprova Selecionados</button></div>';
                        echo '</div>';
                    }
                    ?>

                        <form action="" method="post" id="form1">
                            <table id="datatableAprovacao" class="table table-sm table-bordered table-responsive_mobile" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkall"></th>
                                        <th class="n-mobile-cell"><strong>ID</strong></th>
                                        <th class="n-mobile-cell"><strong>Status</strong></th>
                                        <th><strong>Tipo</strong></th>
                                        <th class="n-mobile-cell"><strong>Data</strong></th>
                                        <th class="n-mobile-cell"><strong>Colaborador</strong></th>
                                        <th class="n-mobile-cell" style="min-width: 290px;"><strong>Descrição Tipo</strong></th>
                                        <th class="n-mobile-cell"><strong>Justificativa</strong></th>
                                        <th class="n-mobile-cell"><strong>Anexo</strong></th>
                                        <th class="n-mobile-cell"><strong>Batidas do Dia</strong></th>
                                        <th class="n-mobile-cell"><strong>Data de Referência</strong></th>
                                        <th class="n-mobile-cell"><strong>Data solicitação</strong></th>
                                        <th class="n-mobile-cell"><strong>Solicitante</strong></th>
                                        <!-- <th class="n-mobile-cell"><strong>Aprovador</strong></th> -->
                                        <th class="y-mobile-cell d-none"><b>DADOS</b></th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($objListaBatidaApr): ?>
                                        <?php foreach($objListaBatidaApr as $key => $registro): ?>
                                            <tr>
                                            <td width="20" class="text-center">
                                                    <?php
                                                    if(
                                                        ($perfilRH && $registro['status'] == 2 && ($registro['movimento'] == 21 || $registro['movimento'] == 22)) ||
                                                        (($perfilRH) || (($registro['status'] == 10) && ($registro['movimento'] == 21 || $registro['movimento'] == 22))) ||
                                                        ($registro['status'] == 1)
                                                    ):
                                                    ?>
                                                    <input type="checkbox" name="idbatida[]" data-checkbox="<?= $registro['id'].'|'.$registro['movimento']; ?>" data-chapa="<?= $registro['chapa']; ?>" value="<?= $registro['chapa'] . '|' . dtEn($registro['dtponto'], true) . '|' . $registro['id'].'|'.$registro['movimento']; ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td width="20" class="text-right">
                                                    <?= $registro['id'] ?>
                                                </td>
                                                <td class="n-mobile-cell">
                                                    <?php
                                                        if ($registro['movimento'] == 21 || $registro['movimento'] == 22) {
                                                            switch($registro['status']){
                                                                case 10: echo '<span class="badge badge-warning">Pend/Ação Gestor</span>'; break;
                                                                case 2: echo '<span class="badge badge-info">Pend/Ação RH</span>'; break;
                                                                default: echo '';
                                                            }
                                                        }else{
                                                            echo '<span class="badge badge-warning">Pend/Ação Gestor</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                <?php
                                                    $tipoRequisicao = "";
                                                    switch($registro['movimento']){
                                                        case 1: $tipoRequisicao = 'Inclusão de batida'; break;
                                                        case 2: $tipoRequisicao = 'Exclusão de batida'; break;
                                                        case 3: $tipoRequisicao = 'Alteração de natureza'; break;
                                                        case 4: $tipoRequisicao = 'Alteração jornada referência'; break;
                                                        case 5: $tipoRequisicao = 'Abono de atrasos'; break;
                                                        case 6: $tipoRequisicao = 'Abono de faltas'; break;
                                                        case 7: $tipoRequisicao = 'Justificativa de exceção'; break;
                                                        case 8: $tipoRequisicao = 'Altera atitude'; break;
                                                        case 9: $tipoRequisicao = 'Falta não remunerada'; break;
                                                        case 21: $tipoRequisicao = 'Troca de escala'; break;
                                                        case 22: $tipoRequisicao = 'Troca de dia'; break;
                                                    }

                                                    if($tipoRequisicao == 'Altera atitude' && $registro['justificativa_excecao'] != ''){
                                                        $tipoRequisicao = ucfirst(strtolower($registro['justificativa_excecao']));
                                                    }

                                                    echo $tipoRequisicao;
                                                ?>
                                                </td>
                                                <td class="n-mobile-cell"><?= dtBr($registro['dtponto']); ?></td>
                                                <td class="n-mobile-cell"><?= $registro['chapa'].' - '.$registro['nome']; ?></td>
                                                <td class="n-mobile-cell">
                                                    <?php
                                                    // calcula qtde de horas
                                                    $data_fim = "";
                                                    $total_horas = "";
                                                    if (strlen(trim($registro['abn_codabono'] ?? '')) > 0) {
                                                        if ($registro['abn_horafim'] > $registro['abn_horaini']) {

                                                            $data_fim    = dtEn($registro['dtponto'], true) . 'T00:00:00';
                                                            $total_horas = ($registro['abn_horafim'] - $registro['abn_horaini']);
                                                        } else {

                                                            $dataTermino = new \DateTime(dtEn($registro['dtponto'], true));
                                                            $dataTermino->add(new \DateInterval('P1D'));
                                                            $data_fim    = $dataTermino->format('Y-m-d');
                                                            $total_horas = (($registro['abn_horafim'] + 1440) - $registro['abn_horaini']);
                                                        }
                                                    }
                                                    
                                                    // inclusão de batida
                                                    echo '<div class="row" style="min-width: 290px;">';
                                                    if ($registro['movimento'] == 21) {
                                                        echo '<div class="col-4 text-center"> <strong>Indice</strong><br> ' . $registro['codindice'] . '</div>';
                                                        echo '<div class="col-8 text-center"> <strong>Horário</strong><br> ' . $registro['horario']. '</div>';
                                                    }
                                                    if ($registro['movimento'] == 22) {
                                                        echo '<div class="col-6 text-center"> <strong>Data Útil</strong><br> ' . dtBr($registro['dtponto']) . '</div>';
                                                        echo '<div class="col-6 text-center"> <strong>Índice Útil</strong><br> ' . ($registro['codindice']) . '</div>';
                                                        echo '<div class="col-6 text-center"> <strong>Data Folga</strong><br> ' . dtBr($registro['dtfolga']) . '</div>';
                                                        echo '<div class="col-6 text-center"> <strong>Índice Folga</strong><br> ' . ($registro['codindice_folga']) . '</div>';
                                                        echo '<div class="col-12 text-center"> <strong>Horário</strong><br> ' . $registro['horario']. '</div>';
                                                    }
                                                    if ($registro['movimento'] != 5 && $registro['movimento'] != 6 && $registro['movimento'] != 8 && $registro['movimento'] != 7 && $registro['movimento'] != 9 && $registro['movimento'] != 21 && $registro['movimento'] != 22) {
                                                        echo '<div class="col-12 text-center"><strong>Batida</strong><br> ' . sprintf("%05s", m2h($registro['batida'])) . '</div>';
                                                    }
                                                    // abono
                                                    if ($registro['movimento'] == 5 || $registro['movimento'] == 6 || $registro['movimento'] == 9) {
                                                        echo '<div class="col-6 text-center">  <strong>Data Inicio</strong><br> ' . date('d/m/Y', strtotime($registro['dtponto'])) . ' ' . sprintf("%05s", m2h($registro['abn_horaini'])) . '</div>';
                                                        echo '<div class="col-6 text-center"> <strong>Data Fim</strong><br> ' . date('d/m/Y', strtotime($data_fim)) . ' ' . sprintf("%05s", m2h($registro['abn_horafim'])) . '</div>';
                                                        echo '<div class="col-6 text-center pt-2"> <strong>Total de Horas</strong><br> ' . m2h($total_horas, 4) . '</div>';
                                                        echo '<div class="col-6 text-center pt-2"> <strong>Tipo de Abono</strong><br> ' . $registro['abn_codabono'] . ' - ' . (($registro['movimento'] == 9) ? 'FALTA NÃO REMUNERADA' : extrai_valor($resAbonos, $registro['abn_codabono'], 'CODIGO', 'DESCRICAO')) . '</div>';
                                                    }
                                                    // altera atitude
                                                    if ($registro['movimento'] == 8 || $registro['movimento'] == 7) {
                                                        echo '<div class="col-6 text-center"> <strong>Data</strong><br> ' . date('d/m/Y', strtotime($registro['atitude_dt'])) . '</div>';
                                                        echo '<div class="col-6 text-center"> <strong>Horas</strong><br> ' . sprintf("%05s", m2h($registro['atitude_fim'])) . '</div>';
                                                        if ($registro['movimento'] == 8) {
                                                            echo '<div class="col-12 text-center pt-2"> <strong>Tipo Atitude</strong><br> ' . ($registro['atitude_tipo'] == 1 ? 'Compensar (Fica BH)' : 'Descontar no pagto.') . '</div>';
                                                        } else {
                                                            echo '<div class="col-12 text-center pt-2"> <strong>Tipo Atitude</strong><br> Atraso Não Remunerado</div>';
                                                        }
                                                    }
                                                    echo '</div>';
                                                    ?>
                                                </td>
                                                <td class="n-mobile-cell">
                                                <?php
                                                    if ($registro['movimento'] == 21 || $registro['movimento'] == 22) {
                                                        echo $registro['justificativa_escala'];
                                                    }
                                                    // inclusão de batida
                                                    if ($registro['movimento'] != 5 && $registro['movimento'] != 6 && $registro['movimento'] != 8 && $registro['movimento'] != 7 && $registro['movimento'] != 9 && $registro['movimento'] != 21 && $registro['movimento'] != 22) {
                                                        if (strlen(trim($registro['motivo'] ?? '')) > 0) echo $registro['motivo'];
                                                    }
                                                    // abono
                                                    if ($registro['movimento'] == 5 || $registro['movimento'] == 6 || $registro['movimento'] == 9) {
                                                        if (strlen(trim($registro['justificativa_abono_tipo'] ?? '')) > 0) echo $registro['justificativa_abono_tipo'];
                                                    }
                                                    // altera atitude
                                                    if ($registro['movimento'] == 8 || $registro['movimento'] == 7) {
                                                        if ($registro['movimento'] == 8) {
                                                            if (strlen(trim($registro['justificativa_abono_tipo'] ?? '')) > 0) echo $registro['atitude_justificativa'];
                                                        } else {
                                                            echo 'Atraso Não Remunerado';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td class="n-mobile-cell">
                                                <?php
                                                    // inclusão de batida
                                                    echo '<div class="row">';
                                                    if ($registro['movimento'] != 5 && $registro['movimento'] != 6 && $registro['movimento'] != 8 && $registro['movimento'] != 7 && $registro['movimento'] != 9 && $registro['movimento'] != 21 && $registro['movimento'] != 22) {
                                                        if (strlen($registro['possui_anexo'] ?? '') > 0) {
                                                            echo '<div class="col-12 text-center">';
                                                            echo '<button type="button" onclick="carregaVisualizador(' . $registro['id'] . ',\'' . $registro['chapa'] . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" data-anexo="Sim" aria-hidden="true"></i> </button>';
                                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $registro['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    // abono
                                                    if ($registro['movimento'] == 5 || $registro['movimento'] == 6 || $registro['movimento'] == 9) {
                                                        if (strlen($registro['possui_anexo'] ?? '') > 0) {
                                                            echo '<div class="col-12 text-center">';
                                                            echo '<button type="button" onclick="carregaVisualizador(' . $registro['id'] . ',\'' . $registro['chapa'] . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" data-anexo="Sim" aria-hidden="true"></i> </button>';
                                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $registro['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    // altera atitude
                                                    if ($registro['movimento'] == 8 || $registro['movimento'] == 7) {
                                                        if (strlen($registro['possui_anexo'] ?? '') > 0) {
                                                            echo '<div class="col-12 text-center">';
                                                            echo '<button type="button" onclick="carregaVisualizador(' . $registro['id'] . ',\'' . $registro['chapa'] . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" data-anexo="Sim" aria-hidden="true"></i> </button>';
                                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $registro['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    // escala
                                                    if ($registro['movimento'] == 21 || $registro['movimento'] == 22) {
                                                        if (($registro['possui_anexo'] ?? 0) == 1) {
                                                            echo '<div class="col-12 text-center">';
                                                            echo '<button type="button" onclick="carregaVisualizadorEscala(' . $registro['id'] . ',\'' . $registro['solicitante'] . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" data-anexo="Sim" aria-hidden="true"></i> </button>';
                                                            echo '<a href="' . base_url('ponto/aprova/download_anexo_escala/' . $registro['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    echo '</div>';
                                                    ?>
                                                </td>
                                                <td class="n-mobile-cell"><?= $registro['batidas_dia']; ?>
                                                <td class="n-mobile-cell"><?= (strlen(trim($registro['data_referencia'])) > 0 ? dtBr($registro['data_referencia']) : ''); ?></td>
                                                <td class="n-mobile-cell"><?= dtBr($registro['data_solicitacao']); ?></td>
                                                <td class="n-mobile-cell"><?= $registro['chapa_solicitante'].' - '.$registro['solicitante']; ?></td>
                                                <!-- <td class="n-mobile-cell"><?= $registro['chapa_gestor'].' - '.$registro['nome_gestor']; ?></td> -->
                                                <td class="y-mobile-cell d-none">
                                                    <?php
                                                    // inclusão de batida
                                                    echo '<div class="row">';
                                                    if ($registro['movimento'] == 21) {
                                                        echo '<div class="col-4 text-center"><strong>Indice</strong><br>' . $registro['codindice'] . '</div>';
                                                        echo '<div class="col-8 text-center"><strong>Horário</strong><br>' . $registro['horario']. '</div>';
                                                    }
                                                    if ($registro['movimento'] == 22) {
                                                        echo '<div class="col-6 text-center"><strong>Data Útil</strong><br>' . dtBr($registro['dtponto']) . '</div>';
                                                        echo '<div class="col-6 text-center"><strong>Índice Útil</strong><br>' . ($registro['codindice']) . '</div>';
                                                        echo '<div class="col-6 text-center"><strong>Data Folga</strong><br>' . dtBr($registro['dtfolga']) . '</div>';
                                                        echo '<div class="col-6 text-center"><strong>Índice Folga</strong><br>' . ($registro['codindice_folga']) . '</div>';
                                                        echo '<div class="col-12 text-center"><strong>Horário</strong><br>' . $registro['horario']. '</div>';
                                                    }
                                                    if ($registro['movimento'] != 5 && $registro['movimento'] != 6 && $registro['movimento'] != 8 && $registro['movimento'] != 7 && $registro['movimento'] != 9 && $registro['movimento'] != 21 && $registro['movimento'] != 22) {
                                                        echo '<div class="col-6 text-center"><strong>Data</strong><br>' . date('d/m/Y', strtotime($registro['dtponto'])) . '</div>';
                                                        echo '<div class="col-6 text-center"><strong>Batida</strong><br>' . sprintf("%05s", m2h($registro['batida'])) . '</div>';
                                                        echo '<div class="col-12 text-center"><strong>Justificativa</strong><br>' . $registro['motivo'] . '</div>';
                                                        echo '<div class="col-12 text-center"><strong>Solicitante</strong><br>' . $registro['solicitante'] . '</div>';
                                                        if (strlen(trim($registro['justificativa_abono_tipo'] ?? '')) > 0) echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong><br>' . $registro['justificativa_abono_tipo'] . '</div>';

                                                        if (strlen($registro['possui_anexo'] ?? '') > 0) {
                                                            echo '<div class="col-12 text-center pt-2"><strong>Anexo</strong><br>';
                                                            echo '<button type="button" onclick="carregaVisualizador(' . $registro['id'] . ',\'' . $registro['chapa'] . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" data-anexo="Sim" aria-hidden="true"></i> </button>';
                                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $registro['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    // abono
                                                    if ($registro['movimento'] == 5 || $registro['movimento'] == 6 || $registro['movimento'] == 9) {
                                                        echo '<div class="col-6 text-center"><strong>Data Inicio</strong><br>' . date('d/m/Y', strtotime($registro['dtponto'])) . ' ' . sprintf("%05s", m2h($registro['abn_horaini'])) . '</div>';
                                                        echo '<div class="col-6 text-center"><strong>Data Fim</strong><br>' . date('d/m/Y', strtotime($data_fim)) . ' ' . sprintf("%05s", m2h($registro['abn_horafim'])) . '</div>';
                                                        echo '<div class="col-6 text-center pt-2"><strong>Total de Horas</strong><br>' . m2h($total_horas, 4) . '</div>';
                                                        echo '<div class="col-6 text-center pt-2"><strong>Tipo de Abono</strong><br>' . $registro['abn_codabono'] . ' - ' . (($registro['movimento'] == 9) ? 'FALTA NÃO REMUNERADA' : extrai_valor($resAbonos, $registro['abn_codabono'], 'CODIGO', 'DESCRICAO')) . '</div>';
                                                        echo '<div class="col-12 text-center"><strong>Solicitante</strong><br>' . $registro['solicitante'] . '</div>';
                                                        if (strlen(trim($registro['justificativa_abono_tipo'] ?? '')) > 0) echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong> ' . $registro['justificativa_abono_tipo'] . '</div>';
                                                        if (strlen($registro['possui_anexo'] ?? '') > 0) {
                                                            echo '<div class="col-12 text-center pt-2"><strong>Anexo</strong><br>';
                                                            echo '<button type="button" onclick="carregaVisualizador(' . $registro['id'] . ',\'' . $registro['chapa'] . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" data-anexo="Sim" aria-hidden="true"></i> </button>';
                                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $registro['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    // altera atitude
                                                    if ($registro['movimento'] == 8 || $registro['movimento'] == 7) {
                                                        echo '<div class="col-6 text-center"><strong>Data</strong><br>' . date('d/m/Y', strtotime($registro['atitude_dt'])) . '</div>';
                                                        echo '<div class="col-6 text-center"><strong>Horas</strong><br>' . sprintf("%05s", m2h($registro['atitude_fim'])) . '</div>';
                                                        if ($registro['movimento'] == 8) {
                                                            echo '<div class="col-12 text-center pt-2"><strong>Tipo Atitude</strong><br>' . ($registro['atitude_tipo'] == 1 ? 'Compensar (Fica BH)' : 'Descontar no pagto.') . '</div>';
                                                        } else {
                                                            echo '<div class="col-12 text-center pt-2"><strong>Tipo Atitude</strong><br>Atraso Não Remunerado</div>';
                                                        }
                                                        echo '<div class="col-12 text-center"><strong>Solicitante</strong><br>' . $registro['solicitante'] . '</div>';
                                                        if ($registro['movimento'] == 8) {
                                                            if (strlen(trim($registro['justificativa_abono_tipo'] ?? '')) > 0) echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong> ' . $registro['atitude_justificativa'] . '</div>';
                                                        } else {
                                                            echo '<div class="col-12 text-center pt-2"><strong>Justificativa</strong> Atraso Não Remunerado</div>';
                                                        }
                                                        if (strlen($registro['possui_anexo'] ?? '') > 0) {
                                                            echo '<div class="col-12 text-center pt-2"><strong>Anexo</strong><br>';
                                                            echo '<button type="button" onclick="carregaVisualizador(' . $registro['id'] . ',\'' . $registro['chapa'] . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search"  data-anexo="Sim"aria-hidden="true"></i> </button>';
                                                            echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $registro['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                                            echo '</div>';
                                                        }
                                                    }
                                                    echo '</div>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if(
                                                        ($perfilRH && $registro['status'] == 2 && ($registro['movimento'] == 21 || $registro['movimento'] == 22)) ||
                                                        (($perfilRH) || (($registro['status'] == 10) && ($registro['movimento'] == 21 || $registro['movimento'] == 22))) ||
                                                        ($registro['status'] == 1)
                                                    ):
                                                    ?>
                                                    <div class="dropdown">
                                                        <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                                                        <div class="dropdown-menu" style="margin-left: -131px;">

                                                            <?php if($registro['movimento'] == 21 || $registro['movimento'] == 22): ?>
                                                                <a target="_blank" href="<?= base_url('ponto/escala/'.(($registro['movimento'] == 21) ? 'editar' : 'editardia').'/'.id($registro['id'])).'/'.id($registro['situacao']); ?>" class="dropdown-item"><i class="mdi mdi-eye-outline"></i> Ver requisição</a>
                                                                <button type="button" onclick="justificativas('<?= id($registro['id']); ?>')" class="dropdown-item"><i class="mdi mdi-comment-eye-outline"></i> Ver justificativa</button>
                                                                
                                                            <?php else: ?>
                                                                <a href="/ponto/espelho/editar/<?= $registro['chapa']; ?>/<?= dtEn($registro['dtponto'], true); ?>" target="_blank" class="dropdown-item"><i class="mdi mdi mdi-account-clock"></i> Ver Espelho</a>
                                                            <?php endif; ?>

                                                            <button type="button" onclick="aprovarIndividual('<?= $registro['id'].'|'.$registro['movimento']; ?>')" class="dropdown-item text-success"><i class="far fa-thumbs-up"></i> Aprovar</button>
                                                            <button type="button" onclick="reprovarIndividual('<?= $registro['id'].'|'.$registro['movimento']; ?>')" class="dropdown-item text-danger"><i class="far fa-thumbs-down"></i> Reprovar</button>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>
                                                </td>
                                            </tr>
                                            <?php unset($objListaBatidaApr[$key],$registro); ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <input type="hidden" name="act" data-act>
                            <input type="hidden" name="motivo_reprova" id="motivo_reprova">
                            <input type="hidden" name="periodo" value="<?= $periodo; ?>">
                            <input type="hidden" name="statusPeriodo" value="<?= $statusPeriodo; ?>">
                            <input type="hidden" name="filtro_tipo" value="<?= $filtro_tipo; ?>">
                            <input type="hidden" name="filtro_tipo2" value="<?= $filtro_tipo2; ?>">
                            <input type="hidden" name="codsecao" value="<?= $codsecao; ?>">
                            <input type="hidden" name="codccusto" value="<?= $codccusto; ?>">
                            <input type="hidden" name="funcionario" value="<?= $chapa; ?>">
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const aprovarIndividual = (idRegistro) => {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        Swal.fire({
            icon              : 'question',
            title             : 'Confirmar aprovação deste registro?',
            showDenyButton    : true,
            showCancelButton  : true,
            confirmButtonText : `Sim aprovar`,
            denyButtonText    : `Cancelar`,
            showCancelButton  : false,
            showCloseButton   : false,
            allowOutsideClick : false,
            width             : 600,
        }).then((result) => {
            if (result.isConfirmed) {
                $("[data-checkbox]").prop('checked', false);
                openLoading();
                $("[data-checkbox='"+idRegistro+"']").prop('checked', true);
                $('[data-act]').val('apr');
                $("#form1").attr('target', '_top');
                document.getElementById('form1').action="";
                document.getElementById('form1').submit();
            }
        });
    }
    const reprovarIndividual = (idRegistro) => {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        $("[data-checkbox]").prop('checked', false);
        $("[data-checkbox='"+idRegistro+"']").prop('checked', true);
        reprovaBatida();
    }
    const carregaVisualizador = (id, nome) => {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
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
    const carregaVisualizadorEscala = (id, nome) => {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        $("#titulo_modal").html('Anexo enviado por | ' + nome);
        $("#conteudo_modal").html('<div class="text-center"><div class="spinner-border thumb-md text-primary" role="status"></div></div>');
        $(".modal_visualizador").modal('show');
        openLoading();
        var hFrame = $(".modal-body").height();

        $("#iframe_preview").html('<iframe id="iframe" src="/ponto/preview/escala/' + id + '" frameborder="0" width="100%" height="' + (hFrame - 50) + 'px" allowfullscreen></iframe>');

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

<!-- modal justificativa -->
<div class="modal modal_justificativa" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_justificativa" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-full">
            <div class="modal-header text-dark pt-3 pb-2" style="background: #ffffff;">
                <h5 class="modal-title mt-0">Justificativas da requisição</h5>
                <button type="button" class="close text-dark" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body" style="background: #ffffff;" id="justificativas">

                

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal upload -->

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
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        var periodo = document.getElementById("periodo").value;
        if (periodo == "") {
            exibeAlerta('error', 'Período não informado.');
            return false;
        }
        var filtro_tipo = document.getElementById('filtro_tipo').value;
        if (filtro_tipo == '') {
          exibeAlerta('error', 'Categoria não informada.');
          return false;
        }
        openLoading();
        $("#form_secao").attr('target', '_top');
        document.getElementById('form_secao').action="";
        document.getElementById('form_secao').submit();
    }

    function limpaFiltro() {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
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
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        if( $("[data-checkbox]:checked").length <= 0){
            exibeAlerta('warning', 'Nenhum registro selecionado.')
            return;
        }
        var r = confirm('Deseja realmente APROVAR os registros selecionados.');
        if (r == true) {
            openLoading();
            $('[data-act]').val('apr');
            $("#form1").attr('target', '_top');
            document.getElementById('form1').action="";
            document.getElementById('form1').submit();
        }
        return false;
    }

    function aprovaBatidaTot() {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        if( $("[data-checkbox]:checked").length <= 0){
            exibeAlerta('warning', 'Nenhum registro selecionado.')
            return;
        }
        var r = confirm('Deseja realmente aprovar os registros selecionados.');
        if (r == true) {
            openLoading();
            $('[data-act]').val('apr');
            $("#form1").attr('target', '_top');
            document.getElementById('form1').action="";
            document.getElementById('form1').submit();
        }
        return false;
    }

    function cancelaApr() {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        $(".popup_bg").fadeOut(100);
        $(".popup_content").fadeOut(100);
        $("#nome_func").html('');
        $("#func_chapa").val('');
    }

    function autenticaFunc() {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
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
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        if ($("[data-checkbox]:checked").length <= 0) {
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
                    $("#form1").attr('target', '_top');
                    document.getElementById('form1').action="";
                    document.getElementById('form1').submit();

                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }
            }
        });

    }
    const carregaFuncionariosSecao = (codSecao) => {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
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
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        openLoading();

        var selPeriodo = $("#periodo").val();
        if(selPeriodo == ''){
            exibeAlerta('warning', 'Período não selecionado.');
            return;
        }

        var periodo = $("#periodo option:selected").attr('data-iniciomensal');
        var codsecao = $("#secao").val() ?? null;
        var codccusto = $("#ccusto").val() ?? null;
        if(codsecao == 'all') codsecao = null;
        if(codccusto == 'all') codcusto = null;

        $("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');

        $.ajax({
            url: "<?= base_url('ponto/espelho/action/carrega_colaboradores') ?>",
            type: 'POST',
            data: {
                'codccusto'   : codccusto,
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
    const excel = () => {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
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

        $("#form1").attr('target', '_blank');
        document.getElementById('form1').action="<?= base_url('ponto/aprova/excel'); ?>";
        document.getElementById('form1').submit();
    }
    const justificativas = (idEscala) => {
        <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
        openLoading();
        $("#justificativas").html('');

        $.ajax({
            url: "<?= base_url('ponto/escala/action/justificativa') ?>",
            type:'POST',
            data: {
                "id": idEscala
            },
            success:function(result){
                $(".modal_justificativa").modal('show');
                openLoading(true);
                if(result == ""){
                    $("#justificativas").html('<div class="alert alert-warning2 border-0" role="alert">Nenhuma justificativa encontrada.</div>');
                }else{
                    $("#justificativas").html(result);
                }

            },
        });

    }
    function removerHTML(variavelComHTML) {
        const elemento = document.createElement('div');
        elemento.innerHTML = variavelComHTML;
        return elemento.textContent || elemento.innerText || '';
    }
</script>
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
<?php loadPlugin(array('datatable')); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>"/>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>
<script>
    $(document).ready(function () {
      if($("#periodo").val() != ''){carregaColaboradores();}
 
      <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
      // Inicialização do DataTable
      var tabelaAprovacao = $('#datatableAprovacao').DataTable({
        "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength": 25,
        "aaSorting": [[0, "desc"]],
        "fixedHeader": true, // Ativa o fixedHeader
        "language": {
            "decimal": ",",
            "thousands": ".",
            "sProcessing": "Processando...",
            "sLengthMenu": "Exibir _MENU_ registros",
            "sZeroRecords": "Nenhum registro encontrado",
            "sEmptyTable": "Nenhum dado disponível nesta tabela",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
            "sSearch": "Procurar:",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Próximo",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": Ordenar colunas de forma ascendente",
                "sSortDescending": ": Ordenar colunas de forma descendente"
            }
        },
        initComplete: function () {
            var api = this.api(); // Instância do DataTable
            var p_linha = api.columns()[0].length;

            // Configura filtros personalizados
            api.columns().every(function () {
                var column = this;

                if (column[0][0] == 0 || column[0][0] == 1 || column[0][0] >= (p_linha - 2) || column[0][0] == 8 || column[0][0] == 6) return false;

                var select = $('<select class="form-control form-control-sm filtro_table"><option value="">Todos</option></select>')
                    .appendTo($(column.header()))
                    .on('change', function () {
                        var val = $(this).val();
                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                    });

                column.data().unique().sort().each(function (d, j) {
                    var noHTML = removerHTML(d);
                    select.append('<option value="' + noHTML + '">' + noHTML + '</option>');
                });
            });

            $(".filtro_table").select2({
                width: '100%',
                language: {
                    noResults: function () {
                        return 'Nenhum resultado encontrado';
                    }
                }
            });
            setInterval(function(){
                tabelaAprovacao.fixedHeader.adjust();
            }, 1000);
        },
    });
    
});
</script>
<?php loadPlugin(array('select2')); ?>