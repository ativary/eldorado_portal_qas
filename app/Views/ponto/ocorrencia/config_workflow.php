<?php $isAdmin = ($_SESSION['log_id'] == 1) ? true : false; ?>
<?= menuConfigPonto('Workflow'); ?>

<div class="container-fluid">
  <div class="row">

    <!-- main -->
    <div class="col-12">
      <div class="card">

        <div class="card-body">

          <?= exibeMensagem(true); ?>

          <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a data-tab="1" class="nav-link <?php echo ($tab == '1' ? 'active' : ''); ?>" data-toggle="tab" href="#cargos" role="tab">Abandono</a>
            </li>
            <li class="nav-item">
              <a data-tab="2" class="nav-link <?php echo ($tab == '2' ? 'active' : ''); ?>" data-toggle="tab" href="#tipos" role="tab">Aprovação</a>
            </li>
            <li class="nav-item">
              <a data-tab="3" class="nav-link <?php echo ($tab == '3' ? 'active' : ''); ?>" data-toggle="tab" href="#horarios" role="tab">Ocorrência</a>
            </li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane <?php echo ($tab == '1' ? 'active' : ''); ?> p-3" id="cargos" role="tabpanel">
              <div class="container-fluid">
                <div class="row">

                  <!-- main -->
                  <div class="col-12">
                    <div class="card">

                      <div class="card-header mt-0">
                        <div class="row">
                          <h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-email-check"></i> Configuração do Workflow de Abandono de Emprego</h4>
                        </div>
                      </div>

                      <div class="card-body">
                        <div class="form-group row p-0 m-0">
                          <label for="dias_faltas" class="col-sm-4 col-form-label text-right">Faltas Consecutivas para iniciar o Workflow:</label>
                          <div class="col-sm-2">
                            <div class="input-group">
                              <input class="form-control form-control" type="number" value="<?= $resWorkflowFaltas[0]->dias_faltas ?? ''; ?>" name="dias_faltas" id="dias_faltas" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Dia(s)</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row p-0 m-0">
                          <label for="dias_para_escalar" class="col-sm-4 col-form-label text-right">Em caso de não resposta escalar em:</label>
                          <div class="col-sm-2">
                            <div class="input-group">
                              <input class="form-control form-control" type="number" value="<?= $resWorkflowFaltas[0]->dias_para_escalar ?? ''; ?>" name="dias_para_escalar" id="dias_para_escalar" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Dia(s)</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row p-0 m-0">
                          <label for="dias_de_espera" class="col-sm-4 col-form-label text-right">Reiniciar workflow após esperar:</label>
                          <div class="col-sm-2">
                            <div class="input-group">
                              <input class="form-control form-control" type="number" value="<?= $resWorkflowFaltas[0]->dias_de_espera ?? ''; ?>" name="dias_de_espera" id="dias_de_espera" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Dia(s)</span>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="form-group row p-0 m-0">
                          <label for="email_rh" class="col-sm-4 col-form-label text-right">Email do usuário RH Master:</label>
                          <div class="col-sm-8">
                            <div class="input-group">
                              <input class="form-control form-control" type="email" maxlength="100" value="<?= $resWorkflowFaltas[0]->email_rh ?? ''; ?>" name="email_rh" id="email_rh" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Receberá alerta de confirmação de envio de telegrama.</span>
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                      <div class="card-footer text-center">
                        <button type="button" class="btn btn-xxs btn-primary" onclick="return salvaConfigWorkflowFaltas()">Salvar <i class="fas fa-check"></i></button>
                      </div>


                    </div>
                  </div><!-- end main -->

                </div>
              </div><!-- container -->

            </div>

            <div class="tab-pane <?php echo ($tab == '2' ? 'active' : ''); ?> p-3" id="tipos" role="tabpanel">
              <div class="container-fluid">
                <div class="row">

                  <!-- main -->
                  <div class="col-12">
                    <div class="card">

                      <div class="card-header mt-0">
                        <div class="row">
                          <h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-email-check"></i> Configuração do Workflow de Aprovação</h4>
                        </div>
                      </div>

                      <div class="card-body">
                        <div class="form-group row p-0 m-0">
                          <label for="ciclo1" class="col-sm-4 col-form-label text-right">Lembrete para Gestor aprovar a cada:</label>
                          <div class="col-sm-2">
                            <div class="input-group">
                              <input class="form-control form-control" type="number" value="<?= $resWorkflowRH[0]->wflow_dias_notif ?? ''; ?>" name="dgestor1" id="dgestor1" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Dia(s)</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="form-group row p-0 m-0">
                          <label for="ciclo1" class="col-sm-4 col-form-label text-right">Lembrete p/Gestor acima caso pendência persista em:</label>
                          <div class="col-sm-2">
                            <div class="input-group">
                              <input class="form-control form-control" type="number" value="<?= $resWorkflowRH[0]->wflow_dias_notif_acima ?? ''; ?>" name="dgestor2" id="dgestor2" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Dia(s)</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="card-footer text-center">
                        <button type="button" class="btn btn-xxs btn-primary" onclick="return salvaConfigWorkflowRH()">Salvar <i class="fas fa-check"></i></button>
                      </div>


                    </div>
                  </div><!-- end main -->

                </div>
              </div><!-- container -->

            </div>

            <div class="tab-pane <?php echo ($tab == '3' ? 'active' : ''); ?> p-3" id="horarios" role="tabpanel">

              <div class="container-fluid">
                <div class="row">

                  <!-- main -->
                  <div class="col-12">
                    <div class="card">

                      <div class="card-header mt-0">
                        <div class="row">
                          <h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-email-check"></i> Configuração de envio do workflow</h4>
                        </div>
                      </div>

                      <div class="card-body">
                        <div class="form-group row p-0 m-0">
                          <label for="data1" class="col-sm-2 col-form-label text-right">Data Envio W1:</label>
                          <div class="col-sm-2">
                            <input class="form-control form-control" type="date" value="<?= ($resWorkflow[0]->data1 ?? false) ? dtEn($resWorkflow[0]->data1, true) : ''; ?>" name="data1" id="data1" require>
                          </div>
                          <label for="ciclo1" class="col-sm-2 col-form-label text-right">Ciclo de notificação W1:</label>
                          <div class="col-sm-2">
                            <div class="input-group">
                              <input class="form-control form-control" type="number" value="<?= $resWorkflow[0]->ciclo1 ?? ''; ?>" name="ciclo1" id="ciclo1" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Dia(s)</span>
                              </div>
                            </div>
                          </div>
                          <label for="horario1" class="col-sm-2 col-form-label text-right">Horário 1:</label>
                          <div class="col-sm-2">
                            <input class="form-control form-control" type="time" value="<?= ($resWorkflow[0]->horario1 ?? false) ? m2h($resWorkflow[0]->horario1, 4) : ''; ?>" name="horario1" id="horario1" require>
                          </div>
                        </div>
                        <div class="form-group row p-0 m-0">
                          <label for="data2" class="col-sm-2 col-form-label text-right">Data Envio W2:</label>
                          <div class="col-sm-2">
                            <input class="form-control form-control" type="date" value="<?= ($resWorkflow[0]->data2 ?? false) ? dtEn($resWorkflow[0]->data2, true) : ''; ?>" name="data2" id="data2" require>
                          </div>
                          <label for="ciclo2" class="col-sm-2 col-form-label text-right">Ciclo de notificação W2:</label>
                          <div class="col-sm-2">
                            <div class="input-group">
                              <input class="form-control form-control" type="number" value="<?= $resWorkflow[0]->ciclo2 ?? ''; ?>" name="ciclo2" id="ciclo2" require>
                              <div class="input-group-append">
                                <span class="input-group-text">Dia(s)</span>
                              </div>
                            </div>
                          </div>
                          <label for="horario2" class="col-sm-2 col-form-label text-right">Horário 2:</label>
                          <div class="col-sm-2">
                            <input class="form-control form-control" type="time" value="<?= ($resWorkflow[0]->horario2 ?? false) ? m2h($resWorkflow[0]->horario2, 4) : ''; ?>" name="horario2" id="horario2" require>
                          </div>
                        </div>
                      </div>
                      <div class="card-footer text-center">
                        <button type="button" class="btn btn-xxs btn-primary" onclick="return salvaConfigWorkflow()">Salvar <i class="fas fa-check"></i></button>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div><!-- container -->

<script>
  const salvaConfigCiclo = () => {

    openLoading();

    var inputs = $("[data-detalhes-horario] tbody").find('input');

    var dados = [];
    $("[data-detalhes-horario] tbody tr").each(function(e) {

      var input = $(this).find('input');

      dados.push({
        'codhorario': input[0].dataset.codhorario,
        'codindice': input[0].dataset.indice,
        'tipo': input[0].dataset.tipo,
        'tolerancia_inicio': input[0].value,
        'tolerancia_fim': input[1].value,
        'ciclo': input[2].value,
        'extra': input[3].value,
        'excesso': input[4].value,
      });

    });


    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_dados_horario'); ?>",
      type: 'POST',
      data: {
        'dados': dados
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);
          $(".modal_configurar_ciclo").modal('hide');

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }
  const salvaConfigWorkflow = () => {

    var data = {
      'data1': $("#data1").val(),
      'data2': $("#data2").val(),
      'horario1': $("#horario1").val(),
      'horario2': $("#horario2").val(),
      'ciclo1': $("#ciclo1").val(),
      'ciclo2': $("#ciclo2").val(),
    };

    if (data.data1 == '') {
      alert('error', 'Data Envio W1 não informado.');
      return;
    }
    if (data.data2 == '') {
      alert('error', 'Data Envio W2 não informado.');
      return;
    }
    if (data.ciclo1 == '') {
      alert('error', 'Ciclo de notificação W1 não informado.');
      return;
    }
    if (data.horario1 == '') {
      alert('error', 'Horário do W1 não informado.');
      return;
    }
    if (data.ciclo2 == '') {
      alert('error', 'Ciclo de notificação W2 não informado.');
      return;
    }
    if (data.horario2 == '') {
      alert('error', 'Horário do W2 não informado.');
      return;
    }

    openLoading();

    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_configuracao_workflow'); ?>",
      type: 'POST',
      data: {
        'dados': data
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }

  const salvaConfigWorkflowRH = () => {

    var data = {
      'dgestor1': $("#dgestor1").val(),
      'dgestor2': $("#dgestor2").val(),
    };

    if (data.dgestor1 == '') {
      alert('Dias para lembrete gestor não informado.');
      return;
    }
    if (data.dgestor2 == '') {
      alert('Dias para aviso ao gestor acima.');
      return;
    }

    openLoading();

    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_configuracao_workflow_RH'); ?>",
      type: 'POST',
      data: {
        'dados': data
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }

  const salvaConfigWorkflowFaltas = () => {

    var data = {
      'dias_faltas': $("#dias_faltas").val(),
      'dias_de_espera': $("#dias_de_espera").val(),
      'dias_para_escalar': $("#dias_para_escalar").val(),
      'email_rh': $("#email_rh").val(),
    };

    if (data.dias_faltas == '') {
      alert('Dias de faltas consecutivas não informado.');
      return;
    }
    if (data.dias_de_espera == '') {
      alert('Dias de espera não informado.');
      return;
    }
    if (data.dias_para_escalar == '') {
      alert('Dias para escalar não informado.');
      return;
    }
    if (data.email_rh == '') {
      alert('É necessário informar o email do RH Master que receberá o alerta de envio de telegrama.');
      return;
    }

    openLoading();

    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_config_workflow_faltas'); ?>",
      type: 'POST',
      data: {
        'dados': data
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }
</script>
<?php loadPlugin(array('select2', 'mask')); ?>