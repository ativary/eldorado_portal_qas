<div class="container-fluid">
  <div class="row">

    <!-- main -->
    <div class="col-12">
      <div class="card m-0">

        <div class="card-header mt-0">
          <div class="row">
            <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
            <div class="col-6 text-right">
              <div class="button-items">
                <?php if ($pode_editar) { ?>
                  <a href="javascript:void(0);" onclick="return processar(<?= $id_requisicao; ?> )" class="btn btn-purple btn-xxs mb-0 <?= $em_analise; ?>"><i class="fas fa-table"></i> Processar</a>
                <?php } ?>
                <a href="<?= base_url('ponto/art61/exportar_req/' . $id_requisicao) ?>" class="btn btn-success btn-xxs mb-0"><i class="fas fa-file-excel"></i> Exportar Excel</a>
                <?php if ($pode_editar) { ?>
                  <a href="<?= base_url('ponto/art61/importar_justificativas/' . $id_requisicao) ?>" class="btn btn-warning btn-xxs mb-0 <?= $em_analise; ?>"><i class="fas fa-file-excel"></i> Importar Excel</a>
                <?php } ?>
                <a href="<?= base_url('ponto/art61/solicitacao') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
              </div>
            </div>
          </div>
        </div>
        <div class="card-header mt-0">
          <div class="row">
            <h6 class="col-2 text-right mb-1 mt-1">Solicitante:</h6>
            <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resListaArt61[0]['nome_requisitor'] . ' (' . $resListaArt61[0]['chapa_requisitor'] . ')'; ?></h5>
            <h6 class="col-2 text-right mb-1 mt-1">Data Requisição:</h6>
            <h5 class="col-3 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resListaArt61[0]['dt_req_br']; ?></h5>
          </div>
          <div class="row">
            <h6 class="col-2 text-right mb-1 mt-1">Período de Ponto:</h6>
            <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resListaArt61[0]['per_ponto_br']; ?>&nbsp;&nbsp;&nbsp;
                      <?php
                          switch ($resListaArt61[0]['status']) {
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
            </h5>
            <div class="col-5 mb-1 mt-1 text-right">
              <?php if ($pode_editar) { ?>
                <button style="margin-left: 20px;" id="btnJust" name="btnJust" class="btnpeq btn-sm btn-success" type="button" onclick="return Justificar()"><i class="fa fa-plus"></i> Justificar Selecionados</button>
              <?php } ?>
              <?php if ($rh and $pode_editar) { ?>
                <button style="margin-left: 20px;" id="btnNovo" name="btnNovo" class="btnpeq btn-sm btn-primary" type="button" onclick="return novoColaborador()"><i class="fa fa-plus"></i> Novo Colaborador</button>
              <?php } ?>
            </div>
          </div>
        </div>

        <div class="card-body">

          <?= exibeMensagem(true); ?>

          <form action="" method="post" id="form1">
            <table id="datatableAprovacao" class="table table-sm table-bordered table-responsive_mobile" style="width: 100%;">
              <thead>
                <tr>
                  <th><input type="checkbox" id="checkall"></th>
                  <th class="n-mobile-cell"><strong>ID</strong></th>
                  <th class="n-mobile-cell"><strong>Filial</strong></th>
                  <th class="n-mobile-cell"><strong>Data</strong></th>
                  <th class="n-mobile-cell"><strong>Colaborador</strong></th>
                  <th class="n-mobile-cell"><strong>Função</strong></th>
                  <th class="n-mobile-cell"><strong>Centro de Custo</strong></th>
                  <th class="n-mobile-cell"><strong>Seção</strong></th>
                  <th class="n-mobile-cell"><strong>Gestor</strong></th>
                  <th class="n-mobile-cell"><strong>Área</strong></th>
                  <th class="n-mobile-cell"><strong>Justificativa</strong></th>
                  <th class="n-mobile-cell"><strong>Obs</strong></th>
                  <th class="n-mobile-cell"><strong>Quantidade</strong></th>
                  <th class="n-mobile-cell"><strong>Evento</strong></th>
                  <?php if ($rh and $calculado): ?>
                    <th class="n-mobile-cell"><strong>H.E.Normais</strong></th>
                    <th class="n-mobile-cell"><strong>Evento Art.61</strong></th>
                    <th class="n-mobile-cell"><strong>H.E.Art.61</strong></th>
                  <?php endif; ?>
                  <th>Ação</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($resReqChapas): ?>
                  <?php foreach ($resReqChapas as $key => $registro): ?>
                    <tr data-linha="<?= $registro['id'] ?>">
                      <td width="20" class="text-center">
                        <input type="checkbox" onclick="VerificaCheck()" name="idart61[]" data-checkbox="<?= $registro['id']; ?>" value="<?= $registro['id']; ?>">
                      </td>
                      <td class="n-mobile-cell"><?= $registro['id']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['codfilial']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['dt_ponto_br'] ?></td>
                      <td class="n-mobile-cell"><?= $registro['chapa_colab'] . ' - ' . $registro['nome_colab']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['desc_funcao'] . ' - ' . $registro['codfuncao']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['desc_ccusto'] . ' - ' . $registro['cod_ccusto']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['desc_secao'] . ' - ' . $registro['codsecao']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['chapa_gestor'] . ' - ' . $registro['nome_gestor']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['area']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['id_justificativa'] . ' - ' . $registro['desc_justificativa']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['obs']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['valor']; ?></td>                
                      <td class="n-mobile-cell"><?= $registro['codevento'] . ' - ' . $registro['desc_evento']; ?></td>
                      <?php if ($rh and $calculado): ?>
                        <?php if (strlen(trim($registro['horas_extras_normais'])) == 0) { ?>
                          <td class="n-mobile-cell">00:00</td>
                        <?php } else { ?>
                          <td class="n-mobile-cell"><?= $registro['horas_extras_normais']; ?></td>
                        <?php } ?>
                        <td class="n-mobile-cell"><?= $registro['codevento_art61']. ' - ' . $registro['desc_evento_art61']; ?></td>
                        <td class="n-mobile-cell"><?= $registro['horas_extras_art61']; ?></td>
                      <?php endif; ?>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                          <div class="dropdown-menu" style="margin-left: -131px;">
                            <button type="button" onclick="atuJustificativa('<?= $registro['id']; ?>')" class="dropdown-item" <?= $pode_editar ? '' : 'disabled' ; ?>><i class="mdi mdi-comment-eye-outline"></i> Justificativa</button>
                            <button type="button" onclick="verAnexos('<?= $registro['id']; ?>',<?= $pode_editar ? 1 : 0 ; ?>)" class="dropdown-item"><i class="mdi mdi mdi-eye-outline"></i> Anexos</button>
                            <button type="button" onclick="apagarColaborador('<?= $registro['id']; ?>')" class="dropdown-item text-danger" <?= $pode_editar ? '' : 'disabled' ; ?>><i class="mdi mdi-trash-can-outline"></i> Remover Colaborador/Evento</button>
                          </div>
                        </div>
                      </td>
                    </tr>
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

<!-- modal -->
<div class="modal" id="modalAnexos" tabindex="-1" role="dialog" aria-labelledby="modalAnexos" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog" style="max-width: 50%; width: 50%;">
    <div class="modal-content modal-content-full">
      <div class="modal-header bg-dark">
        <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-file-document-box-outline"></i> Anexos</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-12">
            <!-- Removed the <form> tag -->
            <table class="table" style="font-size: 16px;">
              <thead>
                <tr>
                  <th width="60" class="text-right">Anexo</th>
                  <th width="60" class="text-right">Ação</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="form-group row mb-2">
          <label for="anexo" class="col-sm-2 col-form-label text-right text-left-sm">Anexar:</label>
          <div class="col-sm-10">
            <input class="form-control filepond" type="file" name="anexo[]" id="anexo" multiple required accept="application/pdf, image/jpeg">
            <input class="form-control" hidden type="text" value="" name="id" id="id" required>
          </div>
        </div>

        <div class="card-footer text-center">
          <button type="button" class="btn btn-success bteldorado_1" id="btnsave" onclick="salvaAnexo()">
            <i class="fas fa-check"></i> Salvar
          </button>
        </div>

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- modal -->

<!-- modal -->
<div class="modal" id="modalNovo" tabindex="1" role="dialog" aria-labelledby="modalNovo" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="modalNovolabel"><span class="oi oi-people"></span> Inserir Novo Colaborador </h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-times"></span>
        </button>
      </div>
      <div class="modal-body">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <label class="input-group-text" for="chapa" style="width: 150px;">Colaborador: </label>
          </div>
          <select class="custom-select" id="chapa" name="chapa">
            <option value="">...</option>
            <?php foreach ($resColab as $key => $Colab): ?>
              <option value="<?= $Colab['CHAPA']; ?>"><?= $Colab['NOME'] . ' - ' . $Colab['CHAPA']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
        <button type="button" class="btn btn-success" onclick="return GravaColaborador();"> <i class="fa fa-check"></i> Confirmar </button>
      </div>
    </div>
  </div>
</div>
<!-- modal -->


<!-- modal -->
<div class="modal" id="modalJust" tabindex="1" role="dialog" aria-labelledby="modalJust" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-center" id="modalJustlabel"><span class="oi oi-people"></span> Atualizar Justificativa(s) </h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-times"></span>
        </button>
      </div>
      <div class="modal-body">

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <label class="input-group-text" for="chapa" style="width: 150px;">Justificativa: </label>
          </div>
          <select class="custom-select" id="justificativa" name="justificativa">
            <option value="">...</option>
            <?php foreach ($resJustificativaArt61 as $key => $Just): ?>
              <option value="<?= $Just['id']; ?>"><?= $Just['id'] . ' - ' . $Just['descricao']; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <label class="input-group-text" style="width: 150px;" for="obs">Observação: </label>
          </div>
          <input class="form-control" type="text" maxlength="1000" name="obs" id="obs" value="<?php echo ''; ?>">
        </div>
        <input type="hidden" name="id_req_chapa" id="id_req_chapa">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
        <button type="button" class="btn btn-success" onclick="return GravaJust();"> <i class="fa fa-check"></i> Confirmar </button>
      </div>
    </div>
  </div>
</div>
<!-- modal -->

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

  .disabled {
    pointer-events: none;
    color: grey;
    /* Estilização para parecer desabilitado */
  }

  .anexo-claro {
    background-color: #f0f0f0;
    /* Cor mais clara */
  }

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
  const novoColaborador = () => {
    //abre modal
    $("#modalNovo").modal();
  }

  const verAnexos = (id, status=1) => {
    $("#modalAnexos").modal();
    const novoTitulo = `Anexos - ${id}`; // Exemplo de como construir o novo título
    $("#modalAnexos .modal-title").text(novoTitulo);
    $('#modalAnexos tbody').html('');
    $("#id").val(id);
    $('#anexo').val(''); // Limpa o campo de anexo
    if (status != 1) {
      $('#anexo').prop('disabled', true);
    } else {
      $('#anexo').prop('disabled', false);
    }
    $.ajax({
      url: "<?= base_url('ponto/art61/Anexos'); ?>",
      type: "POST",
      data: {
        "id": id
      },
      success: function(response) {
        swal.close();
        if (Array.isArray(response) && response.length > 0) {
          let anexoCounter = 0;
          response.forEach(anexo => {
            anexoCounter++;
            let disableDelete = status != 1 ? 'disabled' : '';
            let rowClass = anexoCounter != 1 ? 'anexo-claro' : '';

            let row = `
                            <tr id="anexo_${anexo.id}" class="${rowClass}">
                                <td style="width: 80%;"><a href="data:${anexo.file_type};base64,${anexo.file_data}" download="${anexo.file_name}">
                                ${anexoCounter}: ${anexo.file_name}
                                </a></td>
                                <td><button type="button" class="btn btn-danger btn-sm" ${disableDelete} onclick="excluirAnexo(${anexo.id})"><i class="mdi mdi-delete"></i> Excluir</button></td>
                            </tr>
                        `;
            console.log(row);
            $('#modalAnexos tbody').append(row);
          });
        }

        $("#modalAnexos").modal("show");
      }
      ,
            beforeSend: function() {
              Swal.fire({
                title: 'Carregando Anexos...',
                html: 'Por favor, aguarde...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                  Swal.showLoading();
                }
              });
            },
            error: function(response) {
              Swal.fire(
                response.responseJSON.title,
                response.responseJSON.message,
                response.responseJSON.status
              );
            },
    });
  }

  const salvaAnexo = () => {
    // Obtenha os arquivos selecionados
    let arquivos = $('#anexo')[0].files;

    if (arquivos.length === 0) {
      Swal.fire('Erro', 'Nenhum arquivo selecionado.', 'error');
      return false;
    }

    // Definir extensões permitidas
    const extensoesPermitidas = ['jpeg', 'pdf'];
    let extensaoInvalida = false;

    // Validar extensões dos arquivos
    $.each(arquivos, function(i, file) {
      const extensaoArquivo = file.name.split('.').pop().toLowerCase();
      if (!extensoesPermitidas.includes(extensaoArquivo)) {
        extensaoInvalida = true;
        return false; // Interromper o loop ao encontrar uma extensão inválida
      }
    });

    if (extensaoInvalida) {
      Swal.fire('Erro', 'Apenas arquivos com as extensões .jpeg, ou .pdf são permitidos.', 'error');
      return false;
    }

    // Cria um objeto FormData para enviar os arquivos
    let formData = new FormData();
    $.each(arquivos, function(i, file) {
      formData.append('anexo[]', file);
    });

    // Adicione outros dados que você queira enviar, se necessário
    formData.append("id", $("#id").val());

    // Enviar requisição AJAX com os anexos validados
    $.ajax({
      url: "<?= base_url('ponto/art61/salvarAnexo'); ?>", // Altere para a URL do seu endpoint de upload
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(result) {
        var response = JSON.parse(result);

        if (response.tipo != 'success') {
          exibeAlerta(response.tipo, response.msg, 2);
        } else {
          $("#modalAnexos").modal("hide");
          exibeAlerta(response.tipo, response.msg, 3);
        }
      },
      error: function() {
        Swal.fire('Erro', 'Ocorreu um erro ao salvar os anexos.', 'error');
      }
    });

    return false; // Prevenir o recarregamento da página
  }

  const excluirAnexo = (id) => {
    let totalLinhas = $('#modalAnexos tbody tr').length;

    if (totalLinhas <= 1) {
      Swal.fire('Erro', 'Você não pode excluir o único anexo restante.', 'error');
      return false;
    }

    // Perguntar ao usuário se deseja excluir usando Swal
    Swal.fire({
      icon: 'question',
      title: 'Deseja excluír esse Anexo?',
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: 'Sim, confirmar',
      denyButtonText: 'Cancelar',
      showCloseButton: false,
      allowOutsideClick: false,
      width: 600,
    }).then((result) => {
      if (result.isConfirmed) {
        let dados = {
          "id": id,
        }

        $.ajax({
          url: "<?= base_url('ponto/art61/deleteAnexo'); ?>",
          type: 'POST',
          data: dados, // Enviar o ID para exclusão
          success: function(result) {
            console.log(result);
            var response = JSON.parse(result);

            if (response.tipo != 'success') {
              exibeAlerta(response.tipo, response.msg, 2);
            } else {
              $(`#anexo_${id}`).remove();
              exibeAlerta(response.tipo, response.msg, 3);
            }
          },
          error: function(xhr, status, error) {
            exibeAlerta('error', 'Ocorreu um erro ao tentar excluir o anexo.', 2);
          }
        });
      }
    });
  };

  const atuJustificativa = (id_req_chapa) => {
    //abre modal
    console.log(id_req_chapa);
    if (id_req_chapa != -1) {
      $("[type=checkbox]").prop('checked', false)
    };
    $("#justificativa").val('');
    $("#obs").val('');
    $("#id_req_chapa").val(id_req_chapa);
    $("#modalJust").modal();
  }

  const GravaJust = () => {
    if ($("#justificativa").val() == "") {
      exibeAlerta("error", "<b>Justificativa</b> não informada.");
      return false;
    }

    var selecionados = $('input[name="idart61[]"]:checked')
      .map(function() {
        return this.value;
      }).get().join(', ');

    let dados = {
      "id": $("#id_req_chapa").val(),
      "id_just": $("#justificativa").val(),
      "obs": $("#obs").val(),
      "sel_ids": selecionados,
    }
    console.log(dados);

    let msg = (selecionados.includes(',')) ? 'Confirma a atualização da justificativa e observação para os registros selecionados?' : 'Confirma a atualização da justificativa e observação para o registro selecionado?';

    Swal.fire({
      icon: 'question',
      title: msg,
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: `Sim Atualizar`,
      denyButtonText: `Cancelar`,
      showCancelButton: false,
      showCloseButton: false,
      allowOutsideClick: false,
      width: 600,
    }).then((result) => {
      if (result.isConfirmed) {

        $.ajax({
          url: "<?= base_url('ponto/art61/action/grava_just_req_chapa'); ?>",
          type: 'POST',
          data: dados,
          success: function(result) {
            var response = JSON.parse(result);
            if (response.tipo != 'success') {
              exibeAlerta(response.tipo, response.msg);
            } else {
              exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/solicitacao_chapas'); ?>' + '/' + <?= $id_requisicao; ?>);
            }
          },
        });

      }
    });

  }

  function Justificar() {
    if ($("[data-checkbox]:checked").length <= 0) {
      exibeAlerta('warning', 'Nenhum registro selecionado.')
      return;
    }
    atuJustificativa(-1);
  }

  function VerificaCheck() {
    console.log($("[data-checkbox]:checked").length);
    if ($("[data-checkbox]:checked").length <= 0) {
      $("#btnJust").hide();
    } else {
      $("#btnJust").show();
    }
  }

  const GravaColaborador = () => {
    if ($("#chapa").val() == "") {
      exibeAlerta("error", "<b>Colaborador</b> não informado.");
      return false;
    }
    let dados = {
      "id_req": <?= $id_requisicao; ?>,
      "periodo": '<?= $resListaArt61[0]['per_ponto_sql']; ?>',
      "chapa": $("#chapa").val(),
    }
    console.log(dados);

    $.ajax({
      url: "<?= base_url('ponto/art61/action/novo_colab_req'); ?>",
      type: 'POST',
      data: dados,
      success: function(result) {
        var response = JSON.parse(result);
        if (response.tipo != 'success') {
          exibeAlerta(response.tipo, response.msg);
        } else {
          exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/solicitacao_chapas'); ?>' + '/' + <?= $id_requisicao; ?>);
        }
      },
    });
  }

  const apagarColaborador = (id) => {

    Swal.fire({
      icon: 'question',
      title: 'Deseja realmente excluir este <b>Colaborador/Evento</b>?',
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
          url: "<?= base_url('ponto/art61/action/apaga_colaborador') ?>",
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

  const processar = (id) => {
    let dados = {
      "id": id,
      "rh_master": <?php if ($rh) { ?> 'S'
    <?php } else { ?> 'N'
    <?php } ?>,
    "per_ponto": '<?= $resListaArt61[0]['per_ponto_sql']; ?>',
    "chapa_requisitor": '<?= $resListaArt61[0]['chapa_requisitor']; ?>'
    };
    console.log(dados);

    Swal.fire({
      icon: 'question',
      title: 'Este processamento recriará todos os eventos de colaboradores com base no RM Folha. Deseja realmente processar esta <b>Solicitação</b>?',
      showDenyButton: true,
      showCancelButton: true,
      confirmButtonText: `Sim, Processar`,
      denyButtonText: `Cancelar`,
      showCancelButton: false,
      showCloseButton: false,
      allowOutsideClick: false,
      width: 600,
    }).then((result) => {
      if (result.isConfirmed) {
        openLoading();
        $.ajax({
          url: "<?= base_url('ponto/art61/action/processar_req'); ?>",
          type: 'POST',
          data: dados,
          success: function(result) {
            openLoading(true);
            var response = JSON.parse(result);
            if (response.tipo != 'success') {
              exibeAlerta(response.tipo, response.msg);
            } else {
              exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('ponto/art61/solicitacao_chapas'); ?>' + '/' + <?= $id_requisicao; ?>);
            }
          },
        });

      }
    });

  }

  function removerHTML(variavelComHTML) {
    const elemento = document.createElement('div');
    elemento.innerHTML = variavelComHTML;
    return elemento.textContent || elemento.innerText || '';
  }

  $(document).ready(function() {
    $("#checkall").on('click', function(e) {
      if ($(this).prop('checked')) {
        $("[type=checkbox]").prop('checked', true);
      } else {
        $("[type=checkbox]").prop('checked', false);
      }
      VerificaCheck();
    });
    $(".button-menu-mobile").click();
    VerificaCheck();
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

          if (column[0][0] == 0 || column[0][0] >= (p_linha - 1)) return false;

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
<?php loadPlugin(['select2','maskmoney','datatable']); ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>" />
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>" />
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>
