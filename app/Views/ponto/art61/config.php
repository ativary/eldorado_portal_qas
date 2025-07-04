<?= menuConfigPonto('Artigo.61'); ?>

<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a data-tab="1" class="nav-link <?php echo ($tab == '1' ? 'active' : ''); ?>" data-toggle="tab" href="#param" role="tab">Parâmetros</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="2" class="nav-link <?php echo ($tab == '2' ? 'active' : ''); ?>" data-toggle="tab" href="#areas" role="tab">Áreas e Diretoria</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="4" class="nav-link <?php echo ($tab == '4' ? 'active' : ''); ?>" data-toggle="tab" href="#colab" role="tab">Exceção Hierarquia</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="5" class="nav-link <?php echo ($tab == '5' ? 'active' : ''); ?>" data-toggle="tab" href="#evento" role="tab">Eventos</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane <?php echo ($tab == '1' ? 'active' : ''); ?> p-3" id="param" role="tabpanel">

                            <div class="container-fluid">
                                <div class="row">

                                    <!-- main -->
                                    <div class="col-12">
                                        <div class="card">

                                            <div class="card-header mt-0">
                                                <div class="row">
                                                    <h5 class="col-6 mb-1 mt-1"> </h5>
                                                    <div class="col-6 text-right">

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <label for="dtper_ponto" class="col-sm-2 col-form-label text-right">Período do Ponto:</label>
                                                    <div class="col-sm-4">
                                                        <select class="select2 form-control form-control-sm mb-1" name="dtper_ponto" id="dtper_ponto">
                                                            <?php foreach ($resPonto as $key => $Ponto): ?>
                                                                <option value="<?= $Ponto['PERIODO_SQL']; ?>" <?= ($resConfig[0]['per_ponto_sql'] == $Ponto['PERIODO_SQL']) ? " selected " : ""; ?>><?= $Ponto['PERIODO_BR']; ?></option>
                                                                <?php unset($resPonto[$key], $key, $Ponto); ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="dtini_req" class="col-sm-2 col-form-label text-right">Início das Requisições:</label>
                                                    <div class="col-sm-4">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= is_null($resConfig[0]['dtini_req']) ? '' : date('Y-m-d', strtotime($resConfig[0]['dtini_req'])) ?>" name="dtini_req" id="dtini_req">
                                                    </div>
                                                    <label for="dtfim_req" class="col-sm-2 col-form-label text-right">Final das Requisições:</label>
                                                    <div class="col-sm-4">
                                                        <input class="form-control datepicker m_data" type="date" value="<?= is_null($resConfig[0]['dtfim_req']) ? '' : date('Y-m-d', strtotime($resConfig[0]['dtfim_req'])) ?>" name="dtfim_req" id="dtfim_req">
                                                    </div>
                                                </div>

                                                <div class="form-group row" style="display: none;">
                                                    <label for="codevento" class="col-sm-2 col-form-label text-right">Código do Evento [RM]:</label>
                                                    <div class="col-sm-2">
                                                        <input class="form-control form-control-sm mb-1" type="text" value="<?= $resConfig[0]['codevento'] ?>" maxlength="4" name="codevento" id="codevento" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" require>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="card-footer text-center">
                                                <button class="btn btn-success bteldorado_1" id="btnsave" onclick="return salvaDados()"><i class="fas fa-save"></i> Salvar</button>
                                            </div>


                                        </div>
                                    </div><!-- end main -->

                                </div>
                                <div class="row">

                                    <!-- main -->
                                    <div class="col-12">
                                        <div class="card">

                                            <div class="card-header mt-0">
                                                <div class="row">
                                                    <h5 class="col-6 mb-1 mt-1"> </h5>
                                                    <div class="col-6 text-right">
                                                        <div class="button-items">
                                                            <a href="javascript:void(0);" onclick="return Prorroga(0, '', '')" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova Prorrogação</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="20">ID</th>
                                                            <th>Colaborador</th>
                                                            <th>Data</th>
                                                            <th class="text-center" width="180">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($resProrroga): ?>
                                                            <?php foreach ($resProrroga as $key => $Prorroga): ?>
                                                                <tr data-linhap="<?= $Prorroga['id'] ?>">
                                                                    <td class="text-left"><?= $Prorroga['id'] ?></td>
                                                                    <td class="text-left"><?= $Prorroga['chapa'] . ' - ' . $Prorroga['nome']; ?></td>
                                                                    <td class="text-left"><?= $Prorroga['dt_extendida_br'] ?></td>
                                                                    <td class="text-center" width="180">
                                                                        <div class="btn-group" aria-label="acao" role="group">
                                                                            <a href="javascript:void(0);" onclick="return DeletaProrroga(<?= $Prorroga['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
                                                                            <a href="javascript:void(0);" onclick="return Prorroga(<?= $Prorroga['id'] ?>, '<?= $Prorroga['chapa'] ?>', '<?= $Prorroga['dt_extendida'] ?>')" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> Editar</a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="card-footer text-center">
                                            </div>


                                        </div>
                                    </div><!-- end main -->

                                </div>
                            </div><!-- container -->


                        </div>

                        <div class="tab-pane <?php echo ($tab == '2' ? 'active' : ''); ?> p-3" id="areas" role="tabpanel">
                            <div class="container-fluid">
                                <div class="row">

                                    <!-- main -->
                                    <div class="col-12">
                                        <div class="card">

                                            <div class="card-header mt-0">
                                                <div class="row">
                                                    <h5 class="col-6 mb-1 mt-1"> </h5>
                                                    <div class="col-6 text-right">
                                                        <div class="button-items">
                                                            <a href="<?= base_url('ponto/art61/importar_areas') ?>" class="btn btn-warning btn-xxs mb-0"><i class="fas fa-file-excel"></i> Importar </a>
                                                            <a href="<?= base_url('ponto/art61/exportar_areas') ?>" class="btn btn-success btn-xxs mb-0"><i class="fas fa-file-excel"></i> Exportar </a>
                                                            <a href="javascript:void(0);" onclick="return LigaCCustoArea(0, 0, '', '')" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo C.Custo</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="20">ID</th>
                                                            <th>Centro de Custo</th>
                                                            <th>Diretoria</th>
                                                            <th>Área</th>
                                                            <th class="text-center" width="180">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($resAreas): ?>
                                                            <?php foreach ($resAreas as $key => $Area): ?>
                                                                <tr data-linha="<?= $Area['id'] ?>">
                                                                    <td class="text-left"><?= $Area['id'] ?></td>
                                                                    <td class="text-left"><?= $Area['codcusto'] . ' - ' . $Area['nome_ccusto']; ?></td>
                                                                    <td class="text-left"><?= $Area['diretoria'] ?></td>
                                                                    <td class="text-left"><?= $Area['area'] ?></td>
                                                                    <td class="text-center" width="180">
                                                                        <div class="btn-group" aria-label="acao" role="group">
                                                                            <a href="javascript:void(0);" onclick="return DeletaCCustoArea(<?= $Area['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
                                                                            <a href="javascript:void(0);" onclick="return LigaCCustoArea(<?= $Area['id'] ?>, <?= $Area['codcusto'] ?>, '<?= $Area['diretoria'] ?>', '<?= $Area['area'] ?>')" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> Editar</a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="card-footer text-center">
                                            </div>


                                        </div>
                                    </div><!-- end main -->

                                </div>
                            </div><!-- container -->

                        </div>

                        <div class="tab-pane <?php echo ($tab == '4' ? 'active' : ''); ?> p-3" id="colab" role="tabpanel">
                            <div class="container-fluid">
                                <div class="row">

                                    <!-- main -->
                                    <div class="col-12">
                                        <div class="card">

                                            <div class="card-header mt-0">
                                                <div class="row">
                                                    <h5 class="col-6 mb-1 mt-1"> </h5>
                                                    <div class="col-6 text-right">
                                                        <div class="button-items">
                                                            <a href="javascript:void(0);" onclick="return Excecao(0, '', '', '')" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Nova Exceção</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="20">ID</th>
                                                            <th>Colaborador Acima</th>
                                                            <th>Colaborador Abaixo</th>
                                                            <th>Data Limite</th>
                                                            <th class="text-center" width="180">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($resExcecao): ?>
                                                            <?php foreach ($resExcecao as $key => $Excecao): ?>
                                                                <tr data-linhae="<?= $Excecao['id'] ?>">
                                                                    <td class="text-left"><?= $Excecao['id'] ?></td>
                                                                    <td class="text-left"><?= $Excecao['chapa_pai'] . ' - ' . $Excecao['nome_pai']; ?></td>
                                                                    <td class="text-left"><?= $Excecao['chapa_filho'] . ' - ' . $Excecao['nome_filho']; ?></td>
                                                                    <td class="text-left"><?= $Excecao['dt_limite_br'] ?></td>
                                                                    <td class="text-center" width="180">
                                                                        <div class="btn-group" aria-label="acao" role="group">
                                                                            <a href="javascript:void(0);" onclick="return DeletaExcecao(<?= $Excecao['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
                                                                            <a href="javascript:void(0);" onclick="return Excecao(<?= $Excecao['id'] ?>, '<?= $Excecao['chapa_pai'] ?>', '<?= $Excecao['chapa_filho'] ?>', '<?= $Excecao['dt_limite'] ?>')" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> Editar</a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="card-footer text-center">
                                            </div>


                                        </div>
                                    </div><!-- end main -->

                                </div>
                            </div><!-- container -->

                        </div>
                        <div class="tab-pane <?php echo ($tab == '5' ? 'active' : ''); ?> p-3" id="evento" role="tabpanel">
                            <div class="container-fluid">
                                <div class="row">

                                    <!-- main -->
                                    <div class="col-12">
                                        <div class="card">

                                            <div class="card-header mt-0">
                                                <div class="row">
                                                    <h5 class="col-6 mb-1 mt-1"> </h5>
                                                    <div class="col-6 text-right">
                                                        <div class="button-items">
                                                            <a href="javascript:void(0);" onclick="return Evento(0, 0, '', '')" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo Evento</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" width="20">ID</th>
                                                            <th>Filial</th>
                                                            <th>Do Evento</th>
                                                            <th>Para Evento</th>
                                                            <th class="text-center" width="180">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($resCodeventos): ?>
                                                            <?php foreach ($resCodeventos as $key => $Codevento): ?>
                                                                <tr data-linhac="<?= $Codevento['id'] ?>">
                                                                    <td class="text-left"><?= $Codevento['id'] ?></td>
                                                                    <td class="text-left"><?= $Codevento['codfilial'] . ' - ' . $Codevento['nome_filial']; ?></td>
                                                                    <td class="text-left"><?= $Codevento['de_codevento'] . ' - ' . $Codevento['de_evento']; ?></td>
                                                                    <td class="text-left"><?= $Codevento['para_codevento'] . ' - ' . $Codevento['para_evento']; ?></td>
                                                                    <td class="text-center" width="180">
                                                                        <div class="btn-group" aria-label="acao" role="group">
                                                                            <a href="javascript:void(0);" onclick="return DeletaEvento(<?= $Codevento['id']; ?>)" class="btn btn-soft-pink waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Excluir</a>
                                                                            <a href="javascript:void(0);" onclick="return Evento(<?= $Codevento['id'] ?>, <?= $Codevento['codfilial'] ?>, '<?= $Codevento['de_codevento'] ?>', '<?= $Codevento['para_codevento'] ?>')" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="mdi mdi-pencil-outline"></i> Editar</a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="card-footer text-center">
                                            </div>


                                        </div>
                                    </div><!-- end main -->

                                </div>
                            </div><!-- container -->

                        </div>

                    </div>
                </div>

            </div>

        </div><!-- end main -->

    </div>
</div><!-- container -->

<!-- modal -->
<div class="modal" id="modalArea" style="width: 100%;" role="dialog" aria-labelledby="modalArea" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="modalArealabel"><span class="oi oi-people"></span> Inserir C.Custo </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="modal-body">

                <div class="input-group mb-3">
                    <label for="chapa">Centro de Custo: </label>
                    <select class="select2 custom-select" id="ccusto" name="ccusto">
                        <option value="">...</option>
                        <?php foreach ($resCentroCusto as $key => $CentroCusto): ?>
                            <option value="<?= $CentroCusto['CODCCUSTO']; ?>"><?= $CentroCusto['NOME'] . ' - ' . $CentroCusto['CODCCUSTO']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;" for="diretoria">Diretoria: </label>
                    </div>
                    <input type="text" id="diretoria" name="diretoria" class="form-control" style="border:1px solid #d6d6d6;" maxlength="100">
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;" for="diretoria">Área: </label>
                    </div>
                    <input type="text" id="area" name="area" class="form-control" style="border:1px solid #d6d6d6;" maxlength="100">
                </div>

                <input type="hidden" id="id_ccusto" name="id_ccusto">

            </div>
            <div class="modal-footer">

                <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
                <button type="button" class="btn btn-success" onclick="return GravaCCustoArea();"> <i class="fa fa-check"></i> Confirmar </button>
            </div>
        </div>
    </div>


</div>
<!-- modal -->

<!-- modal -->
<div class="modal" id="modalProrroga" style="width: 100%;" role="dialog" aria-labelledby="modalProrroga" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="modalProrrogalabel"><span class="oi oi-people"></span> Inserir Prorrogação </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="modal-body">

                <div class="input-group mb-3">
                    <label for="chapa">Colaborador: </label>
                    <select class="select2 custom-select" id="chapa" name="chapa">
                        <option value="">...</option>
                        <?php foreach ($resColab as $key => $Colab): ?>
                            <option value="<?= $Colab['CHAPA']; ?>"><?= $Colab['NOME'] . ' - ' . $Colab['CHAPA']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;" for="dt_extendida">Data Extendida: </label>
                    </div>
                    <input class="form-control datepicker m_data" type="date" name="dt_extendida" id="dt_extendida">
                </div>

                <input type="hidden" id="id_prorroga" name="id_prorroga">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
                <button type="button" class="btn btn-success" onclick="return GravaProrroga();"> <i class="fa fa-check"></i> Confirmar </button>
            </div>
        </div>
    </div>


</div>
<!-- modal -->

<!-- modal -->
<div class="modal" id="modalExcecao" style="width: 100%;" role="dialog" aria-labelledby="modalExcecao" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="modalExcecaolabel"><span class="oi oi-people"></span> Inserir Exceção </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="modal-body">

                <div class="input-group mb-3">
                    <label for="chapa">Colaborador Acima: </label>
                    <select class="select2 custom-select" id="chapa_pai" name="chapa_pai">
                        <option value="">...</option>
                        <?php foreach ($resColab as $key => $Colab): ?>
                            <option value="<?= $Colab['CHAPA']; ?>"><?= $Colab['NOME'] . ' - ' . $Colab['CHAPA']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <label for="chapa">Colaborador Abaixo: </label>
                    <select class="select2 custom-select" id="chapa_filho" name="chapa_filho">
                        <option value="">...</option>
                        <?php foreach ($resColab as $key => $Colab): ?>
                            <option value="<?= $Colab['CHAPA']; ?>"><?= $Colab['NOME'] . ' - ' . $Colab['CHAPA']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" style="width: 150px;" for="dt_limite">Data Limite: </label>
                    </div>
                    <input class="form-control datepicker m_data" type="date" name="dt_limite" id="dt_limite">
                </div>

                <input type="hidden" id="id_excecao" name="id_excecao">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
                <button type="button" class="btn btn-success" onclick="return GravaExcecao();"> <i class="fa fa-check"></i> Confirmar </button>
            </div>
        </div>
    </div>


</div>
<!-- modal -->

<!-- modal -->
<div class="modal" id="modalEvento" style="width: 100%;" role="dialog" aria-labelledby="modalEvento" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="modalEventolabel"><span class="oi oi-people"></span> Inserir Evento</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="modal-body">

                <div class="input-group mb-3">
                    <label for="chapa">Filial: </label>
                    <select class="select2 custom-select" id="codfilial" name="codfilial">
                        <option value="">...</option>
                        <?php foreach ($resFiliais as $key => $Filial): ?>
                            <option value="<?= $Filial['CODFILIAL']; ?>"><?= $Filial['CODFILIAL'] . ' - ' . $Filial['NOMEFANTASIA']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <label for="chapa">Do Evento: </label>
                    <select class="select2 custom-select" id="de_codevento" name="de_codevento">
                        <option value="">...</option>
                        <?php foreach ($resEventos as $key => $Evento): ?>
                            <option value="<?= $Evento['CODIGO']; ?>"><?= $Evento['CODIGO'] . ' - ' . $Evento['DESCRICAO']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="input-group mb-3">
                    <label for="chapa">Para o Evento: </label>
                    <select class="select2 custom-select" id="para_codevento" name="para_codevento">
                        <option value="">...</option>
                        <?php foreach ($resEventos as $key => $Evento): ?>
                            <option value="<?= $Evento['CODIGO']; ?>"><?= $Evento['CODIGO'] . ' - ' . $Evento['DESCRICAO']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" id="id_codevento" name="id_codevento">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
                <button type="button" class="btn btn-success" onclick="return GravaEvento();"> <i class="fa fa-check"></i> Confirmar </button>
            </div>
        </div>
    </div>


</div>
<!-- modal -->

<!-- modal importacao -->
<!-- DESABILITADO EM 15/04/2025 - PORQUE FOI CRIADA TELA ESPECÍFICA PARA PROCESSO-->
<div class="modal" id="modalImportaAreas" tabindex="1" role="dialog" aria-labelledby="modalImportaAreas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="modalArealabel"><span class="oi oi-people"></span> Importar Centros de Custos </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="fa fa-times"></span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning2 border-0" role="alert">
                    <p class="p-0 m-0"><i class="mdi mdi-information"></i> Importação permitida somente de arquivo no formato <b>XLS or XLSX</b>.</p>
                </div>
                <form action="" method="post" name="upload_arquivo_importacao" id="upload_arquivo_importacao" enctype="multipart/form-data">
                    <input type="file" name="arquivo_importacao" id="arquivo_importacao" class="dropify" data-allowed-file-extensions="xls xlsx" data-show-loader="true" />
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
                <button type="button" class="btn btn-success" onclick="return ImportaAreas();"> <i class="ti-arrow-down"></i> Importar Centros de Custo </button>
            </div>
        </div>
    </div>


</div>
<!-- modal importacao -->


<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "aLengthMenu": [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "Todos"]
            ],
            "iDisplayLength": -1,
            "aaSorting": [
                [0, "asc"],
                [1, "asc"],
                [2, "asc"]
            ]
        });

    });

    const salvaDados = () => {

        if ($("#dtini_req").val() == "") {
            exibeAlerta("error", "<b>Início das Requisições</b> não informado.");
            return false;
        }
        if ($("#dtfim_req").val() == "") {
            exibeAlerta("error", "<b>Fim das Requisições</b> não informado.");
            return false;
        }
        if ($("#dtfim_req").val() < $("#dtini_req").val()) {
            exibeAlerta("error", "<b>Fim das Requisições</b> não pode ser menor que <b>Início das Requisições</b>.");
            return false;
        }
        if ($("#dtper_ponto").val() == "") {
            exibeAlerta("error", "<b>Período de Ponto</b> não informado.");
            return false;
        }

        let dados = {
            "dtini_req": $("#dtini_req").val(),
            "dtfim_req": $("#dtfim_req").val(),
            "dtper_ponto": $("#dtper_ponto").val(),
            "codevento": $("#codevento").val(),
        }

        $.ajax({
            url: "<?= base_url('ponto/art61/action/salva_config'); ?>",
            type: 'POST',
            data: dados,
            success: function(result) {
                var response = JSON.parse(result);
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/config/1'); ?>');
                }
            },
        });
    }

    const LigaCCustoArea = (id_ccusto, codcusto, diretoria, area) => {
        //abre modal
        console.log(id_ccusto, codcusto, diretoria, area);
        let label = (id_ccusto == 0) ? 'Inserir Centro de Custo' : 'Editar Centro de Custo';
        $("#modalArealabel").html(label);
        $("#id_ccusto").val(id_ccusto);
        $("#ccusto").val(codcusto);
        $("#diretoria").val(diretoria);
        $("#area").val(area);
        $("#modalArea").modal();
    }

    const Prorroga = (id_prorroga, chapa, dt_extendida) => {
        //abre modal
        console.log(id_prorroga, chapa, dt_extendida);
        let label = (id_prorroga == 0) ? 'Inserir Prorrogação' : 'Editar Prorrogação';
        $("#modalProrrogalabel").html(label);
        $("#id_prorroga").val(id_prorroga);
        $("#chapa").val(chapa);
        $("#dt_extendida").val(dt_extendida);
        $("#modalProrroga").modal();
    }

    const Excecao = (id_excecao, chapa_pai, chapa_filho, dt_limite) => {
        //abre modal
        console.log(id_excecao, chapa_pai, chapa_filho, dt_limite);
        let label = (id_excecao == 0) ? 'Inserir Exceção' : 'Editar Exceção';
        $("#modalExcecaolabel").html(label);
        $("#id_excecao").val(id_excecao);
        $("#chapa_pai").val(chapa_pai);
        $("#chapa_filho").val(chapa_filho);
        $("#dt_limite").val(dt_limite);
        $("#modalExcecao").modal();
    }

    const Evento = (id_codevento, codfilial, de_codevento, para_codevento) => {
        //abre modal
        console.log(id_codevento, codfilial, de_codevento, para_codevento);
        let label = (id_codevento == 0) ? 'Inserir Evento' : 'Editar Evento';
        $("#modalEventolabel").html(label);
        $("#id_codevento").val(id_codevento);
        $("#codfilial").val(codfilial);
        $("#de_codevento").val(de_codevento);
        $("#para_codevento").val(para_codevento);
        $("#modalEvento").modal();
    }

    const GravaCCustoArea = (id_ccusto) => {

        if ($("#ccusto").val() == "") {
            exibeAlerta("error", "<b>Centro de custo</b> não informado.");
            return false;
        }
        if ($("#diretoria").val() == "") {
            exibeAlerta("error", "<b>Diretoria</b> não informada.");
            return false;
        }
        if ($("#area").val() == "") {
            exibeAlerta("error", "<b>Área</b> não informada.");
            return false;
        }

        let dados = {
            "idccusto": $("#id_ccusto").val(),
            "codcusto": $("#ccusto").val(),
            "diretoria": $("#diretoria").val(),
            "area": $("#area").val(),
        }
        console.log(dados);

        $.ajax({
            url: "<?= base_url('ponto/art61/action/grava_ccusto'); ?>",
            type: 'POST',
            data: dados,
            success: function(result) {
                var response = JSON.parse(result);
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/config/2'); ?>');
                }
            },
        });
    }

    const GravaProrroga = (id_prorroga) => {

        if ($("#chapa").val() == "") {
            exibeAlerta("error", "<b>Chapa</b> não informada.");
            return false;
        }
        if ($("#dt_extendida").val() == "") {
            exibeAlerta("error", "<b>Data</b> não informada.");
            return false;
        }

        let dados = {
            "id_prorroga": $("#id_prorroga").val(),
            "chapa": $("#chapa").val(),
            "dt_extendida": $("#dt_extendida").val(),
        }
        console.log(dados);

        $.ajax({
            url: "<?= base_url('ponto/art61/action/grava_prorroga'); ?>",
            type: 'POST',
            data: dados,
            success: function(result) {
                var response = JSON.parse(result);
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/config/1'); ?>');
                }
            },
        });
    }

    const GravaExcecao = (id_excecao) => {

        if ($("#chapa_pai").val() == "") {
            exibeAlerta("error", "<b>Chapa Acima</b> não informada.");
            return false;
        }
        if ($("#chapa_filho").val() == "") {
            exibeAlerta("error", "<b>Chapa Abaixo</b> não informada.");
            return false;
        }
        if ($("#chapa_pai").val() == $("#chapa_filho").val()) {
            exibeAlerta("error", "<b>Chapas</b> devem ser diferentes.");
            return false;
        }
        if ($("#dt_limite").val() == "") {
            exibeAlerta("error", "<b>Data Limite</b> não informada.");
            return false;
        }

        let dados = {
            "id_excecao": $("#id_excecao").val(),
            "chapa_pai": $("#chapa_pai").val(),
            "chapa_filho": $("#chapa_filho").val(),
            "dt_limite": $("#dt_limite").val(),
        }
        console.log(dados);

        $.ajax({
            url: "<?= base_url('ponto/art61/action/grava_excecao'); ?>",
            type: 'POST',
            data: dados,
            success: function(result) {
                var response = JSON.parse(result);
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/config/4'); ?>');
                }
            },
        });
    }

    const GravaEvento = (id_codevento) => {

        if ($("#codfilial").val() == "") {
            exibeAlerta("error", "<b>Filial</b> não informada.");
            return false;
        }
        if ($("#de_codevento").val() == "") {
            exibeAlerta("error", "<b>Evento origem</b> não informado.");
            return false;
        }
        if ($("#para_codevento").val() == "") {
            exibeAlerta("error", "<b>Evento destino</b> não informado.");
            return false;
        }
        if ($("#de_codevento").val() == $("#para_codevento").val()) {
            exibeAlerta("error", "<b>Eventos</b> devem ser diferentes.");
            return false;
        }
        
        let dados = {
            "id_codevento": $("#id_codevento").val(),
            "codfilial": $("#codfilial").val(),
            "de_codevento": $("#de_codevento").val(),
            "para_codevento": $("#para_codevento").val(),
        }
        console.log(dados);

        $.ajax({
            url: "<?= base_url('ponto/art61/action/grava_evento'); ?>",
            type: 'POST',
            data: dados,
            success: function(result) {
                var response = JSON.parse(result);
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/config/5'); ?>');
                }
            },
        });
    }

    const DeletaCCustoArea = (id) => {
        Swal.fire({
            icon: 'question',
            title: 'Deseja realmente excluir este <b>registro</b>?',
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
                    "id": id
                };

                $.ajax({
                    url: "<?= base_url('ponto/art61/action/deleta_ccusto') ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);
                        exibeAlerta(response.tipo, response.msg);
                        if (response.tipo == "success") $("[data-linha='" + id + "']").remove();
                    },
                });

            }
        });
    }

    const DeletaProrroga = (id) => {
        Swal.fire({
            icon: 'question',
            title: 'Deseja realmente excluir este <b>registro</b>?',
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
                    "id": id
                };

                $.ajax({
                    url: "<?= base_url('ponto/art61/action/deleta_prorroga') ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);
                        exibeAlerta(response.tipo, response.msg);
                        if (response.tipo == "success") $("[data-linhap='" + id + "']").remove();
                    },
                });

            }
        });
    }

    const DeletaExcecao = (id) => {
        Swal.fire({
            icon: 'question',
            title: 'Deseja realmente excluir este <b>registro</b>?',
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
                    "id": id
                };

                $.ajax({
                    url: "<?= base_url('ponto/art61/action/deleta_excecao') ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);
                        exibeAlerta(response.tipo, response.msg);
                        if (response.tipo == "success") $("[data-linhae='" + id + "']").remove();
                    },
                });

            }
        });
    }

    const DeletaEvento = (id) => {
        Swal.fire({
            icon: 'question',
            title: 'Deseja realmente excluir este <b>registro</b>?',
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
                    "id": id
                };

                $.ajax({
                    url: "<?= base_url('ponto/art61/action/deleta_evento') ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);
                        exibeAlerta(response.tipo, response.msg);
                        if (response.tipo == "success") $("[data-linhac='" + id + "']").remove();
                    },
                });

            }
        });
    }

    const ImportaAreas = () => {

        Swal.fire({
            icon: 'question',
            title: 'Confirma a importação dos Centros de Custo?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Sim, importar`,
            denyButtonText: `Cancelar`,
            showCancelButton: false,
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
        }).then((result) => {
            if (result.isConfirmed) {
                //openLoading();
                let fd = new FormData();
                let img = $('#arquivo_importacao');
                fd.append('arquivo_importacao', img[0].files[0]);
                console.log(fd);
                $.ajax({
                    url: "<?= base_url('ponto/art61/action/importar') ?>",
                    type: 'POST',
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function(result) {
                        try {
                            var response = JSON.parse(result);

                            if (response.tipo != 'success') {
                                exibeAlerta(response.tipo, response.msg, 6);
                            } else {
                                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/config/2') ?>');
                            }
                        } catch (e) {
                            exibeAlerta('error', '<b>Erro interno do sistema:</b><br><code class="language-markup">' + e + '</code>');
                        }
                    },
                });
            }
        });
    }
</script>

<style>
    .tab-pane {
        border-left: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
    }
</style>
<?php
loadPlugin(array('datatable'));
loadPlugin(['select2']);
loadPlugin(array('dropify'));
?>
<script>
    $(function() {
        var drEvent = $('.dropify').dropify({
            messages: {
                default: 'Arraste e solte um arquivo aqui ou clique',
                replace: 'Arraste e solte um arquivo ou clique para substituir',
                remove: 'Remover',
                error: 'Desculpe, o arquivo é muito grande ou não esta no formato XLS/XLSX',

            },
            error: {
                fileExtension: "Formato do arquivo não permitido (somente {{ value }})."
            }
        });

        $("#arquivo_importacao").on('change', function() {
            if ($(this).val() != '') $("#btn-importar").attr('class', 'btn btn-success').prop("disabled", false);
        });
        drEvent.on('dropify.afterClear', function(event, element) {
            $("#btn-importar").attr('class', 'btn btn-secondary').prop("disabled", true);
        });
        drEvent.on('dropify.errors', function(event, element) {
            $("#btn-importar").attr('class', 'btn btn-secondary').prop("disabled", true);
        });
    });
</script>