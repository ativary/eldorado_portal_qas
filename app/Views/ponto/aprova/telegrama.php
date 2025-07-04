<div class="container-fluid"> 
    <div class="row">
    
        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                        
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group row">
                      <div class="col-sm-3 text-center">
                        &nbsp;
                      </div>
                      <div class="col-sm-6 text-center">
                        <h3 class="text-center" id="msg">
                          <?= $msg ?>
                        </h3>
                      <div class="col-sm-3 text-center">
                        &nbsp;
                      </div>
                    </div>
                </div>

                <?php if($botoes=='S') { ?>
                    <div class="card-footer text-center">
                          <button class="btn-lg btn-success" id="btnsave" onclick="return enviar(<?= $id; ?>)"><i class="fas fa-check"></i> &nbsp;ENVIAR TELEGRAMA&nbsp;&nbsp;</button>
                          &nbsp;&nbsp;&nbsp;&nbsp;
                          <button class="btn-lg btn-danger" id="btnrecusa" onclick="return recusar(<?= $id; ?>)"><i class="fas fa-times"></i> &nbsp;NÃO ENVIAR TELEGRAMA&nbsp;&nbsp;</button>
                  </div>
                <?php } ?>
                
            </div>
        </div><!-- end main -->
        
    </div>
</div><!-- container -->

<script>

    const enviar = (id) => {

        Swal.fire({
        icon: 'question',
        title: 'Confirma o envio do telegrama pelo RH?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim, Enviar`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
      }).then((result) => {
        if (result.isConfirmed) {

          let dados = {"id":id};

          $.ajax({
            url: "<?= base_url('ponto/aprova/action/enviaTelegrama') ?>",
            type:'POST',
            data:dados,
            success: function(result) {
                console.log(result);
                var response = JSON.parse(result);

                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg, 2);
                } else {
                    exibeAlerta(response.tipo, response.msg, 3, '<?= base_url(); ?>');
                }
            },
            error: function(xhr, status, error) {
                exibeAlerta('error', 'Ocorreu um erro ao tentar confirmar o envio do telegrama.', 2);
            }
          });

        }
      });

    }

    const recusar = (id) => {
        // Perguntar ao usuário se deseja aprovar usando Swal, incluindo um campo de texto para a justificativa
        Swal.fire({
            icon: 'question',
            title: 'Deseja recusar o envio do telegrama?',
            input: 'textarea', // Tipo de input como textarea para a justificativa
            inputLabel: '',
            inputPlaceholder: 'Insira sua justificativa aqui...',
            inputAttributes: {
                'aria-label': 'Insira sua justificativa aqui'
            },
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: 'Sim, recusar',
            denyButtonText: 'Cancelar',
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
            preConfirm: (justificativa) => {
                if (!justificativa) {
                    Swal.showValidationMessage('A justificativa é obrigatória');
                    return false;
                }
                return justificativa;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let dados = {
                    "id": id,
                    "justificativa": result.value // Envia a justificativa junto com o ID
                };
                openLoading();
                $.ajax({
                    url: "<?= base_url('ponto/aprova/action/recusaTelegrama'); ?>",
                    type: 'POST',
                    data: dados,
                    success: function(result) {
                        console.log(result);
                        var response = JSON.parse(result);

                        if (response.tipo != 'success') {
                            exibeAlerta(response.tipo, response.msg, 2);
                        } else {
                            exibeAlerta(response.tipo, response.msg, 3, '<?= base_url(); ?>');
                        }
                    },
                    error: function(xhr, status, error) {
                        exibeAlerta('error', 'Ocorreu um erro ao tentar recusar o envio do telegrama.', 2);
                    }
                });
            }
        });
    };


const salvaDados = () => {

    if($("#dtini_req").val() == ""){ exibeAlerta("error", "<b>Início das Requisições</b> não informado."); return false; }
    if($("#dtfim_req").val() == ""){ exibeAlerta("error", "<b>Fim das Requisições</b> não informado."); return false; }
    if($("#dtfim_req").val() < $("#dtini_req").val()){ exibeAlerta("error", "<b>Fim das Requisições</b> não pode ser menor que <b>Início das Requisições</b>."); return false; }
    if($("#dtper_ponto").val() == ""){ exibeAlerta("error", "<b>Período de Ponto</b> não informado."); return false; }
    

    let dados = {
        "dtini_req": $("#dtini_req").val(),
        "dtfim_req": $("#dtfim_req").val(),
        "dtper_ponto": $("#dtper_ponto").val(),
        "id": <?= $id ?>,
    }

    $.ajax({
        url: "<?= base_url('premio/cadastro/action_acessos/editar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, '<?= base_url('premio/cadastro/acessos/'.$id) ?>');
            }

        },
    });
}
</script>
