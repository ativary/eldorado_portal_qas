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
            <div class="card">

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
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" name="form_secao" id="form_secao">
                            <div class="form-group row">
                                <label for="secao" class="col-sm-4 col-form-label text-right">Seção:</label>
                                <select name="secao" id="secao" class="form-control col-sm-4">
                                    <option value="all">Todas as seções</option>
                                    <?php
                                    $codsecao = false;
                                    $secao = $listaSecaoUsuarioRM;
                                    if ($objSecaoUsu) {
                                        foreach ($objSecaoUsu as $idc => $value) {

                                            $nomeSecao = false;
                                            if ($secao) {
                                                foreach ($secao as $ids => $value) {
                                                    if ($secao[$ids]['CODIGO'] == $objSecaoUsu[$idc]['secao']) {
                                                        $nomeSecao = $secao[$ids]['DESCRICAO'];
                                                        break;
                                                    }
                                                }
                                            }
                                            echo '<option value="' . $objSecaoUsu[$idc]['secao'] . '"';
                                            if (isset($_POST['secao']) && $_POST['secao'] == $objSecaoUsu[$idc]['secao']) {
                                                echo ' selected="selected"';
                                            }
                                            echo '>' . $objSecaoUsu[$idc]['secao'] . ' - ' . $nomeSecao . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="tipo_abono" class="col-sm-4 col-form-label text-right">Tipo Evento:</label>
                                <select name="tipo_abono" id="tipo_abono" class="form-control col-sm-4">
                                    <option value="" selected>...</option>
                                    <?php if ($resAbonos) {
                                        foreach ($resAbonos as $tipoAbono) {
                                            echo '<option value="' . $tipoAbono['CODIGO'] . '"';
                                            if (isset($_POST['tipo_abono']) && $_POST['tipo_abono'] == $tipoAbono['CODIGO']) {
                                                echo ' selected="selected"';
                                            }
                                            echo '>' . $tipoAbono['DESCRICAO'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <label for="ft_legenda" class="col-sm-4 col-form-label text-right">Legenda:</label>
                                <select name="ft_legenda" id="ft_legenda" class="form-control col-sm-4">
                                    <option value="" selected>...</option>
                                    <option value="1" <?= isset($_POST['ft_legenda']) && $_POST['ft_legenda'] == 1 ? ' selected' : '' ?>>Batida Inserida</option>
                                    <option value="2" <?= isset($_POST['ft_legenda']) && $_POST['ft_legenda'] == 2 ? ' selected' : '' ?>>Batida Excluida</option>
                                    <option value="3" <?= isset($_POST['ft_legenda']) && $_POST['ft_legenda'] == 3 ? ' selected' : '' ?>>Alt. Natureza</option>
                                    <option value="4" <?= isset($_POST['ft_legenda']) && $_POST['ft_legenda'] == 4 ? ' selected' : '' ?>>Alt. Data Referência</option>
                                    <option value="5" <?= isset($_POST['ft_legenda']) && $_POST['ft_legenda'] == 5 ? ' selected' : '' ?>>Abono Atraso</option>
                                    <option value="6" <?= isset($_POST['ft_legenda']) && $_POST['ft_legenda'] == 6 ? ' selected' : '' ?>>Abono Falta</option>
                                    <option value="7" <?= isset($_POST['ft_legenda']) && $_POST['ft_legenda'] == 7 ? ' selected' : '' ?>>Justificativa de Exeção</option>
                                </select>
                            </div>

                            <div class="form-group row">
                                <label for="ft_periodo" class="col-sm-4 col-form-label text-right">Período:</label>
                                <input type="date" name="ft_dtini" id="ft_dtini" class="form-control col-2" value="<?= $ft_dtini; ?>">
                                <input type="date" name="ft_dtfim" id="ft_dtfim" class="form-control col-2" value="<?= $ft_dtfim; ?>">
                            </div>

                            <div class="col-sm-12 text-center">
                                <button style="margin-left: 20px;" class="btnpeq btn-sm btn-success" type="button" onclick="return filtroSecao()"><i class="fa fa-filter"></i> Filtrar</button>
                                <button style="margin-left: 20px;" class="btnpeq btn-sm btn-danger" type="button" onclick="return limpaFiltro()"> Limpar</button>
                            </div>
                            
                        </form>
                        <!-- LEGENDA -->
                        <div class="row">
                            <div class="col-sm-12">
                                <hr>
                                <b>Legenda:</b>
                                <span class="batida">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Batida inserida &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="batida_del_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Batida Excluida &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="batida_nat_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Alt. Natureza &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="batida_data_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Alt. Data Referencia&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="batida_atraso_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Abono atraso&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="batida_falta_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Abono falta&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <span class="batida_just_excecao" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> Justificativa de Exceção
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TABELA COM RESULTADOS -->
                <?php
                if ($nomeFunc) {
                    echo '<form action="" method="post" name="apr_batida" id="form1">';

                    echo '<div class="card">';
                    echo '<div class="card-body" style="display: flex; align-items: center;">';
                    echo '<table class="table table-bordered">';
                    echo '<thead>';
                    // echo '<label for="sel_todos">';
                    // echo '<th><input id="sel_todos" data-check="checado" type="checkbox"> SELECIONAR TODOS</th>';
                    echo '<div class="row d-flex">
                </div>';
                    echo '</label>';
                    echo '</thead>';
                    echo '</table>';
                    echo '</div>';
                    echo '</div>';
                    foreach ($nomeFunc as $chapaFunc) {
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<h4 class="page-title">' . $chapaFunc . '</h4>';

                        echo '<table class="table table-bordered table-responsive_mobile">';
                        echo '<thead>';
                        echo '<th></th>';
                        echo '<th ><b>MOVIMENTO</b></th>';
                        echo '<th width="140"><b></b></th>';
                        echo '<th align="center" width="90"><b>DATA</b></th>';
                        echo '<th align="center" width="60"><b>BATIDA</b></th>';
                        echo '<th align="center" width="120"><b>ABONO DATA INICIO</b></th>';
                        echo '<th align="center" width="120"><b>ABONO DATA FIM</b></th>';
                        echo '<th align="center" width="80"><b>TOTAL HORAS</b></th>';
                        echo '<th width="230"><b>TIPO ABONO</b></th>';
                        echo '<th class="text-center"><b>ANEXO</b></th>';
                        echo '<th class="text-center"><b>STATUS</b></th>';
                        echo '</thead>';

                        if ($objListaBatidaApr) {
                            $aa = 0;
                            foreach ($objListaBatidaApr as $idx => $value) {
                                if ($objListaBatidaApr[$idx]['nomechapa'] == $chapaFunc) {
                                    echo '<tr class="tbadmlistalin">';
                                    echo '<td width="20" align="center"></td>';
                                    echo '<td width="25" align="center">';
                                    switch ($objListaBatidaApr[$idx]['movimento']) {
                                        case 1:
                                            echo '<span class="batida" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
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
                                            echo '<span class="batida_atraso_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 6:
                                            echo '<span class="batida_falta_view" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 7:
                                            echo '<span class="batida_just_excecao" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                        case 8:
                                            echo '<span class="batida_atitude" style="display: inline !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                                            break;
                                    }




                                    echo '</td>';
                                    echo '<td style="font-size: 11px;">';

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
                                    }


                                    // calcula qtde de horas
                                    $data_fim = "";
                                    $total_horas = "";
                                    if (strlen(trim($objListaBatidaApr[$idx]['abn_codabono'])) > 0) {
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

                                    echo '</td>';
                                    echo '<td align="center">' . date('d/m/Y', strtotime($objListaBatidaApr[$idx]['dtponto'])) . '</td>';
                                    echo '<td align="center">' . (($objListaBatidaApr[$idx]['abn_codabono'] == '') ? sprintf("%05s", m2h($objListaBatidaApr[$idx]['batida'])) : '') . '</td>';
                                    echo '<td align="center">' . (($objListaBatidaApr[$idx]['abn_codabono'] <> '') ? date('d/m/Y', strtotime($objListaBatidaApr[$idx]['dtponto'])) . ' ' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['abn_horaini'])) : '') . '</td>';
                                    echo '<td align="center">' . (($objListaBatidaApr[$idx]['abn_codabono'] <> '') ? date('d/m/Y', strtotime($data_fim)) . ' ' . sprintf("%05s", m2h($objListaBatidaApr[$idx]['abn_horafim'])) : '') . '</td>';
                                    echo '<td align="center">' . (($objListaBatidaApr[$idx]['abn_totalhoras'] <> '') ?  m2h($total_horas, 4)  : '') . '</td>';
                                    echo '<td>' . $objListaBatidaApr[$idx]['abn_codabono'] . ' - ' . extrai_valor($resAbonos, $objListaBatidaApr[$idx]['abn_codabono'], 'CODIGO', 'DESCRICAO') . '</td>';
                                    echo '<td class="text-center">';

                                    if (strlen($objListaBatidaApr[$idx]['possui_anexo']) > 0) {
                                        echo '<button type="button" onclick="carregaVisualizador(' . $objListaBatidaApr[$idx]['id'] . ',\'' . substr($chapaFunc, 9) . '\')" title="Visualizar" class="btn btn-sm btn-info"><i class="fa fa-search" aria-hidden="true"></i> </button>';
                                        echo '<a href="' . base_url('ponto/aprova/download_anexo/' . $objListaBatidaApr[$idx]['id']) . '" target="_blank" title="Download" class="btn btn-sm btn-primary"><i class="fa fa-download" aria-hidden="true"></i> </a>';
                                    }
                                    echo '</td>';
                                    echo '<td class="text-center">';
                                    echo '<span class="badge badge-success p-2">Sincronizado</span>';
                                    echo '</td>';
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
        var secao = document.getElementById('secao').value;
        var dtini = document.getElementById('ft_dtini').value;
        var dtfim = document.getElementById('ft_dtfim').value;
        if(dtini == ''){alert('Data início do período não informado.'); return false;}
        if(dtfim == ''){alert('Data término do período não informado.'); return false;}
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
            $('[data-act]').val('aprGestor');
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
        var r = confirm('Deseja realmente REPROVAR as batidas selecionadas.');
        if (r == true) {
            $('[data-act]').val('rep');
            document.getElementById('form1').submit();
        }
        return false;
    }
</script>