<script>
  $(document).ready(function() {
    $(".button-menu-mobile").click();
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
            <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
            <div class="col-6 text-right">
              <div class="button-items">
                <a href="javascript:void(0);" onclick="return apagar_zerados(<?= $id_requisicao; ?>)" class="btn btn-danger btn-xxs mb-0"><i class="fa fa-times"></i> Apagar Zerados</a>
                <?php if ($pode_editar) { ?>
                  <a href="javascript:void(0);" onclick="return processar(<?= $id_requisicao; ?> )" class="btn btn-purple btn-xxs mb-0 <?= $em_analise; ?>"><i class="fas fa-table"></i> Processar</a>
                <?php } ?>
                <a href="<?= base_url('premio/requisicao/exportar_requisicao/' . $id_requisicao) ?>" class="btn btn-success btn-xxs mb-0"><i class="fas fa-file-excel"></i> Exportar Excel</a>
                <?php if ($pode_editar) { ?>
                  <a href="<?= base_url('premio/requisicao/importar_requisicao/' . $id_requisicao) ?>" class="btn btn-warning btn-xxs mb-0 <?= $em_analise; ?>"><i class="fas fa-file-excel"></i> Importar Excel</a>
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
            <h5 class="col-5 mb-1 mt-1" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $resListaArt61[0]['per_ponto_br']; ?></h5>
            <div class="col-5 mb-1 mt-1 text-right">
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
                  <th class="n-mobile-cell"><strong>Evento</strong></th>
                  <th class="n-mobile-cell"><strong>Função</strong></th>
                  <th class="n-mobile-cell"><strong>Centro de Custo</strong></th>
                  <th class="n-mobile-cell"><strong>Seção</strong></th>
                  <th class="n-mobile-cell"><strong>Gestor</strong></th>
                  <th class="n-mobile-cell"><strong>Área</strong></th>
                  <th class="n-mobile-cell"><strong>Quantidade</strong></th>
                  <th class="n-mobile-cell"><strong>Obs</strong></th>
                  <th>Ação</th> 
                </tr>
              </thead>
              <tbody>
                <?php if ($resReqChapas): ?>
                  <?php foreach ($resReqChapas as $key => $registro): ?>
                    <tr data-linha="<?= $registro['id'] ?>">
                      <td width="20" class="text-center">
                        <input type="checkbox" name="idart61[]" data-checkbox="<?= $registro['id']; ?>" value="<?= $registro['id']; ?>">
                      </td>
                      <td class="n-mobile-cell"><?= $registro['id']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['codfilial']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['dt_ponto_br'] ?></td>
                      <td class="n-mobile-cell"><?= $registro['chapa_colab'] . ' - ' . $registro['nome_colab']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['codevento'] . ' - ' . $registro['desc_evento']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['desc_funcao'] . ' - ' . $registro['codfuncao']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['desc_ccusto'] . ' - ' . $registro['cod_ccusto']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['desc_secao'] . ' - ' . $registro['codsecao']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['chapa_gestor'] . ' - ' . $registro['nome_gestor']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['area']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['valor']; ?></td>
                      <td class="n-mobile-cell"><?= $registro['obs']; ?></td>
                      <td>
                        <div class="dropdown">
                          <button class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"> <i class="mdi mdi-dots-vertical"></i></button>
                          <div class="dropdown-menu" style="margin-left: -131px;">
                              <button type="button" onclick="justificativas('<?= id($registro['id']); ?>')" class="dropdown-item"><i class="mdi mdi-comment-eye-outline"></i> Ver justificativa</button>
                              <a href="/ponto/art61/solicitacao_chapas/<?= $registro['id']; ?>" target="_blank" class="dropdown-item"><i class="mdi mdi mdi-eye-outline"></i> Ver Anexos</a>
                              <button type="button" onclick="apagarColaborador('<?= $registro['id']; ?>')" class="dropdown-item text-danger"><i class="mdi mdi-trash-can-outline"></i> Remover Colaborador/Evento</button>
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
            <label class="input-group-text" for="chapa" style="width: 150px;">Solicitante: </label>
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
  const novoColaborador = () => {
    //abre modal
    $("#modalNovo").modal();
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
      "rh_master": <?php if ($rh) { ?> 'S' <?php } else { ?> 'N' <?php }?>,
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

        $.ajax({
          url: "<?= base_url('ponto/art61/action/processar_req'); ?>",
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

</script>

<script>
  function removerHTML(variavelComHTML) {
    const elemento = document.createElement('div');
    elemento.innerHTML = variavelComHTML;
    return elemento.textContent || elemento.innerText || '';
  }

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

          if (column[0][0] == 0 || column[0][0] >= (p_linha - 3)) return false;

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