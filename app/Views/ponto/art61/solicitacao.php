<script>
  $(document).ready(function() {
    alteraPeriodo();
  });
</script>
<style>
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
    $("#checkall").on('click', function(e) {
      if ($(this).prop('checked')) {
        $("[type=checkbox]").prop('checked', true);
      } else {
        $("[type=checkbox]").prop('checked', false);
      }
      VerificaCheck();
    });
    VerificaCheck();
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

                <div class="col-sm-1 text-right"><label for="opt_periodo" class=" col-form-label text-left text-left-sm">Período Ponto:</label></div>

                <div class="col-sm-2">
                  <select class="select2  form-control form-control-sm " name="periodo" id="periodo" onchange="return alteraPeriodo()">
                    <?php if ($resPeriodo) : ?>
                      <?php foreach ($resPeriodo as $key => $DadosPeriodo) : ?>
                        <option value="<?= $DadosPeriodo['PERIODO_SQL']; ?>" <?= ($DadosPeriodo['PERIODO_SQL'] == $periodo) ? 'selected' : ''; ?>><?= $DadosPeriodo['PERIODO_BR']; ?></option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>

                <div class="col-sm-2 text-left">
                  <button style="margin-left: 20px;" class="btnpeq btn-sm btn-success bteldorado_1" type="button" onclick="return filtrar()"><i class="fa fa-filter"></i> Filtrar</button>
                </div>

                <div class="col-sm-7 text-right">
                  <button style="margin-left: 20px;" id="btnEnvia" name="btnEnvia" class="btnpeq btn-sm btn-success" type="button" onclick="return enviarLoteAprovacao()"><i class="far fa-paper-plane"></i> Enviar para aprovação</button>

                  <button style="margin-left: 20px; display: none;" id="btnNova" name="btnNova" class="btnpeq btn-sm btn-primary" type="button" onclick="return novaSolicitacao()"><i class="fa fa-plus"></i> Nova Solicitação</button>
                </div>
              </div>
            </form>

            <form action="" method="post" id="form1">
              <table id="datatableAprovacao" class="table table-sm table-bordered table-responsive_mobile" style="width: 100%;">
                <thead>
                  <tr>
                    <th><input type="checkbox" id="checkall"></th>
                    <th class="n-mobile-cell"><strong>ID</strong></th>
                    <th class="n-mobile-cell"><strong>Status</strong></th>
                    <th class="n-mobile-cell"><strong>Data Solicitação</strong></th>
                    <th class="n-mobile-cell" style="min-width: 290px;"><strong>Solicitante</strong></th>
                    <th class="n-mobile-cell" style="min-width: 290px;"><strong>Gestor</strong></th>
                    <th class="n-mobile-cell"><strong>Justificados</strong></th>
                    <th class="n-mobile-cell"><strong>Competência</strong></th>
                    <th class="n-mobile-cell"><strong>Período do Ponto</strong></th>
                    <th>Ação</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($resListaArt61): ?>
                    <?php foreach ($resListaArt61 as $key => $registro):
                      if($registro['registros'] != 0):  ?>
                        <tr data-linha="<?= $registro['id'] ?>">
                          <td width="20" class="text-center">
                            <input type="checkbox" onclick="VerificaCheck()" name="idart61[]" data-checkbox="<?= $registro['id']; ?>" value="<?= $registro['id']; ?>">
                          </td>
                          <td class="n-mobile-cell"><?= $registro['id']; ?></td>
                          <td class="n-mobile-cell text-center">
                            <?php
                            switch ($registro['status']) {
                              case 1:
                                echo '<span class="badge badge-dark">Criada</span>';
                                break;
                              case 2:
                                echo '<span class="badge badge-warning">Pend/Ação Gestor</span>';
                                break;
                              case 3:
                                echo '<span class="badge badge-info">Pend/Calc.RH</span>';
                                break;
                              case 4:
                                echo '<span class="badge badge-info">Pend/Ação RH</span>';
                                break;
                              case 5:
                                echo '<span class="badge badge-primary">Pend/Sincronização</span>';
                                break;
                              case 6:
                                echo '<span class="badge badge-success">Sincronizada</span>';
                                break;
                              case 9:
                                echo '<span class="badge badge-danger">Reprovada</span>';
                                break;
                              default:
                                echo '';
                            }
                            ?>
                          </td>
                          <td class="n-mobile-cell"><?= $registro['dt_req_br']; ?></td>
                          <td class="n-mobile-cell"><?= $registro['chapa_requisitor'] . ' - ' . $registro['nome_requisitor']; ?></td>
                          <td class="n-mobile-cell"><?= $registro['chapa_gestor'] . ' - ' . $registro['nome_gestor']; ?></td>
                          <td class="n-mobile-cell"><?= $registro['justificados'] . ' / ' . $registro['registros']; ?></td>
                          <td class="n-mobile-cell"><?= $registro['mescomp'] . '/' . $registro['anocomp']; ?></td>
                          <td class="n-mobile-cell"><?= $registro['per_ponto_br']; ?></td>
                          <td>
                            <div class="dropdown">
                              <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                              <div class="dropdown-menu" style="margin-left: -131px;">

                                <?php if ($registro['status'] == 1 || $registro['status'] == 9):  ?>
                                  <a href="<?= base_url('ponto/art61/solicitacao_chapas/' . $registro['id']); ?>" class="dropdown-item"><i class="mdi mdi-pencil"></i> Editar requisição</a>
                                  <button type="button" id="btnEnvia1" name="btnEnvia1" onclick="enviarAprovacao('<?= $registro['id']; ?>', <?= $registro['status']; ?>, <?= $registro['qtde_gestores']; ?>, <?= $registro['justificados']; ?>, <?= $registro['registros']; ?>)" class="dropdown-item text-success"><i class="far fa-paper-plane"></i> Enviar para Aprovação</button>
                                  <button type="button" onclick="apagarRequisicao('<?= $registro['id']; ?>')" class="dropdown-item text-danger"><i class="mdi mdi-trash-can-outline"></i> Apagar Solicitação</button>

                                <?php else:  ?>
                                  <a href="/ponto/art61/solicitacao_chapas/<?= $registro['id']; ?>" class="dropdown-item"><i class="mdi mdi mdi-eye-outline"></i> Ver Solicitação</a>
                                  <?php if ($rh):  ?>
                                    <button type="button" onclick="apagarRequisicao('<?= $registro['id']; ?>')" class="dropdown-item text-danger"><i class="mdi mdi-trash-can-outline"></i> Apagar Solicitação</button>
                                  <?php endif;  ?>
                                <?php endif;  ?>
                                
                              </div>
                            </div>
                          </td>
                        </tr>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
              <input type="hidden" name="act" data-act>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- modal -->
<div class="modal" id="modalNova" style="width:100%;" role="dialog" aria-labelledby="modalNova" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="modalNovalabel"><span class="oi oi-people"></span> Inserir Nova Solicitação </h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-times"></span>
        </button>
      </div>
      <div class="modal-body">

        <div class="input-group mb-3">
          <label for="chapa">Solicitante: </label>
          <select class="select2 custom-select" id="chapa" name="chapa">
            <?php if($_SESSION['rh_master']=='S') {?>
              <option value="">...</option>
            <?php } ?>
            <?php foreach ($resSolicitante as $key => $Colab): ?>
              <option value="<?= $Colab['CHAPA']; ?>"><?= $Colab['NOME'] . ' - ' . $Colab['CHAPA']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <label class="input-group-text" style="width: 150px;" for="data_requisicao">Data Requisição: </label>
          </div>
          <input class="form-control datepicker m_data" type="date" name="data_requisicao" id="data_requisicao" value="<?php echo date("Y-m-d"); ?>" disabled>
        </div>

      </div>
      <div class="modal-footer">

        <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
        <button type="button" class="btn btn-success" onclick="return GravaSolicitacao();"> <i class="fa fa-check"></i> Confirmar </button>
      </div>
    </div>
  </div>
</div>
<!-- modal -->


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
  function enviarLoteAprovacao() {
    if ($("[data-checkbox]:checked").length <= 0) {
      exibeAlerta('warning', 'Nenhum registro selecionado.')
      return;
    }
    enviarAprovacao(-1);
  }

  function VerificaCheck() {
    console.log($("[data-checkbox]:checked").length);
    if ($("[data-checkbox]:checked").length <= 0) {
      //$("#btnEnvia").hide();
    } else {
      //$("#btnEnvia").show();
    }
  }

  const enviarAprovacao = (id_req, status = -1, qtde_gestores = 0, justificados = 0, registros = 0) => {
    //abre modal
    let validas = true;
    let todosJust = true;
    let isGestor = "<?php echo $isGestor; ?>";
    console.log(id_req, status, validas);
    console.log(isGestor);

    // Get all checkboxes with name="idart61[]"
    const checkboxes = document.querySelectorAll('input[name="idart61[]"]');

    if (id_req == -1) {
      checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
          // Find the parent <tr> of the checkbox
          const row = checkbox.closest('tr');
          // Get all <td> elements in the row
          const cells = row.querySelectorAll('td');
          // Check if the third <td> exists and log its text
          if (cells.length >= 3) {
            //console.log(cells[2].innerText.trim());
            if (cells[2].innerText.trim() != 'Criada' && cells[2].innerText.trim() != 'Reprovada') {
              validas = false;
            }
            let justReg = cells[6].innerText.trim().split(" / ");
            //console.log(justReg[0]);
            //console.log(justReg[1]);
            if (justReg[0] != justReg[1] || justReg[1] == 0 ) {
              todosJust = false;
            }
          }
        }
      });
    } else {
      if (status != 1 && status != 9) {
        validas = false;
      }
      if (justificados != registros || registros == 0 ) {
        todosJust = false;
      }
    };

    if (!validas) {
      exibeAlerta('warning', 'Somente solicitações criadas ou reprovadas podem ser enviadas para aprovação.')
      return;
    }
    if (!todosJust) {
      exibeAlerta('warning', 'Somente solicitações com todos os registros justificados podem ser enviadas para aprovação.')
      return;
    }
    var selecionados = $('input[name="idart61[]"]:checked')
      .map(function() {
        return this.value;
      }).get().join(', ');

    let dados = {
      "id": id_req,
      "sel_ids": selecionados,
      "isGestor": isGestor
    }
    console.log(dados);

    let ini_msg = '';
    if(qtde_gestores > 1) {
      ini_msg = '<h5>Requisições que tem mais de um gestor serão desmembradas, ficando uma requisição para cada gestor.<br></h5>'
    }
    if(id_req == -1) {
      ini_msg = '<h5>Requisições vazias ou não processadas, não serão enviadas para aprovação.<br></h5><h5>Requisições que tem mais de um gestor serão desmembradas, ficando uma requisição para cada gestor.<br></h5>'
    }
    let msg = (selecionados.includes(',')) ? 'Confirma o envio para aprovação das solicitações selecionadas?' : 'Confirma o envio para aprovação das solicitação selecionada?';

    msg = ini_msg + msg;

    Swal.fire({
      icon: 'question',
      title: msg,
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: `Sim Enviar`,
      denyButtonText: `Cancelar`,
      showCancelButton: false,
      showCloseButton: false,
      allowOutsideClick: false,
      width: 600,
    }).then((result) => {
      if (result.isConfirmed) {

        $.ajax({
          url: "<?= base_url('ponto/art61/action/envia_aprovacao'); ?>",
          type: 'POST',
          data: dados,
          success: function(result) {
            var response = JSON.parse(result);
            if (response.tipo != 'success') {
              exibeAlerta(response.tipo, response.msg);
            } else {
              exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/solicitacao'); ?>');
            }
          },
        });

      }
    });

  }

  function limpaFiltro() {
    <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
    $('#secao').val('all');
    $('#tipo_abono').val('');
  }

  function filtrar() {
    <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
    var periodo = document.getElementById("periodo").value;
    if (periodo == "") {
      exibeAlerta('error', 'Período não informado.');
      return false;
    }
    openLoading();
    $("#form_secao").attr('target', '_top');
    document.getElementById('form_secao').action = "";
    document.getElementById('form_secao').submit();
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

  const alteraPeriodo = () => {
    //abre modal
    console.log($("#periodo").val());
    console.log('<?= $resConfig[0]['per_ponto_sql']; ?>');
    if ($("#periodo").val() == '<?= $resConfig[0]['per_ponto_sql']; ?>') {
      $("#btnNova").show();
      $("#btnEnvia").show();
      $("#btnEnvia1").show();
    } else {
      $("#btnNova").hide();
      $("#btnEnvia").hide();
      $("#btnEnvia1").hide();
    }
    return true;
  }

  const novaSolicitacao = () => {
    //abre modal
    console.log($("#periodo").val());
    $("#modalNova").modal();
  }

  const GravaSolicitacao = () => {

    if ($("#periodo").val() == "") {
      exibeAlerta("error", "<b>Período</b> não informado.");
      return false;
    }
    if ($("#chapa").val() == "") {
      exibeAlerta("error", "<b>Solicitante</b> não informado.");
      return false;
    }
    if ($("#data_requisicao").val() == "") {
      exibeAlerta("error", "<b>Data da requisição</b> não informada.");
      return false;
    }

    let dados = {
      "periodo": $("#periodo").val(),
      "chapa_solicitante": $("#chapa").val(),
      "data_requisicao": $("#data_requisicao").val(),
    }
    console.log(dados);

    $.ajax({
      url: "<?= base_url('ponto/art61/action/nova_solicitacao'); ?>",
      type: 'POST',
      data: dados,
      success: function(result) {
        var response = JSON.parse(result);
        if (response.tipo != 'success') {
          exibeAlerta(response.tipo, response.msg);
        } else {
          exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/solicitacao_chapas'); ?>' + '/' + response.cod);
        }
      },
    });
  }

  const apagarRequisicao = (id) => {

    Swal.fire({
      icon: 'question',
      title: 'Deseja realmente excluir esta <b>Requisição</b>?',
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
          url: "<?= base_url('ponto/art61/action/apaga_requisicao') ?>",
          type: 'POST',
          data: dados,
          success: function(result) {
            var response = JSON.parse(result);
            exibeAlerta(response.tipo, response.msg);
            if (response.tipo == "success") $("[data-linha='" + id + "']").remove();
          },
        });

      }
    });

  }

  function aprovaBatida() {
    <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
    if ($("[data-checkbox]:checked").length <= 0) {
      exibeAlerta('warning', 'Nenhum registro selecionado.')
      return;
    }
    var r = confirm('Deseja realmente APROVAR os registros selecionados.');
    if (r == true) {
      openLoading();
      $('[data-act]').val('apr');
      $("#form1").attr('target', '_top');
      document.getElementById('form1').action = "";
      document.getElementById('form1').submit();
    }
    return false;
  }

  function aprovaBatidaTot() {
    <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
    if ($("[data-checkbox]:checked").length <= 0) {
      exibeAlerta('warning', 'Nenhum registro selecionado.')
      return;
    }
    var r = confirm('Deseja realmente aprovar os registros selecionados.');
    if (r == true) {
      openLoading();
      $('[data-act]').val('apr');
      $("#form1").attr('target', '_top');
      document.getElementById('form1').action = "";
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

  const justificativas = (idEscala) => {
    <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
    openLoading();
    $("#justificativas").html('');

    $.ajax({
      url: "<?= base_url('ponto/escala/action/justificativa') ?>",
      type: 'POST',
      data: {
        "id": idEscala
      },
      success: function(result) {
        $(".modal_justificativa").modal('show');
        openLoading(true);
        if (result == "") {
          $("#justificativas").html('<div class="alert alert-warning2 border-0" role="alert">Nenhuma justificativa encontrada.</div>');
        } else {
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
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>" />
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>
<script>
  $(document).ready(function() {
    <?= (!$acessoPermitido) ? 'return false;' : ''; ?>
    // Inicialização do DataTable
    var tabelaAprovacao = $('#datatableAprovacao').DataTable({
      "aLengthMenu": [
        [25, 50, 100, 200, -1],
        [25, 50, 100, 200, "Todos"]
      ],
      "iDisplayLength": 25,
      "aaSorting": [
        [0, "desc"]
      ],
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
      initComplete: function() {
        var api = this.api(); // Instância do DataTable
        var p_linha = api.columns()[0].length;

        // Configura filtros personalizados
        api.columns().every(function() {
          var column = this;

          if (column[0][0] <= 1 || column[0][0] >= (p_linha - 4)) return false;

          var select = $('<select class="form-control form-control-sm filtro_table"><option value="">Todos</option></select>')
            .appendTo($(column.header()))
            .on('change', function() {
              var val = $(this).val();
              column.search(val ? '^' + val + '$' : '', true, false).draw();
            });

          column.data().unique().sort().each(function(d, j) {
            var noHTML = removerHTML(d);
            select.append('<option value="' + noHTML + '">' + noHTML + '</option>');
          });
        });

        $(".filtro_table").select2({
          width: '100%',
          language: {
            noResults: function() {
              return 'Nenhum resultado encontrado';
            }
          }
        });
        setInterval(function() {
          tabelaAprovacao.fixedHeader.adjust();
        }, 1000);
      },
    });

  });
</script>
<?php loadPlugin(array('select2')); ?>