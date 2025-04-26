<?= menuConfigPonto('Espelho'); ?>

<div class="container-fluid">
    <div class="row">
        
        <!-- main -->
        <div class="col-12">
            <div class="card">

                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <!-- Define o periodo que será mostrado no select -->
                    <div class="form-group row">
                        <label for="periodo" class="col-sm-12 col-form-label text-left">Período para consulta do Espelho de Ponto:</label>
                        <div class="col-md-6 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['dtinicio']) ? $EspelhoConfiguracao[0]['dtinicio'] : ""; ?>" name="data_inicio" id="data_inicio" require>
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text">até</span>
                                </div>
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['dtfim']) ? $EspelhoConfiguracao[0]['dtfim'] : ""; ?>" name="data_fim" id="data_fim" require>
                            </div>
                        </div>
                    </div>

                    <!--
                    <div class="form-group row">
                        <label class="col-sm-12 col-form-label text-left">Período bloqueado do Espelho de Ponto:</label>
                        <div class="col-md-6 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['dt_ini_bloqueio']) ? $EspelhoConfiguracao[0]['dt_ini_bloqueio'] : ""; ?>" name="data_inicio_bloqueio" id="data_inicio_bloqueio" require>
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text">até</span>
                                </div>
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['dt_fim_bloqueio']) ? $EspelhoConfiguracao[0]['dt_fim_bloqueio'] : ""; ?>" name="data_fim_bloqueio" id="data_fim_bloqueio" require>
                            </div>
                        </div>
                    </div>

                    
                    <div class="form-group row">
                        <label for="data_atual_bloqueio" class="col-sm-12 col-form-label text-left">Data Período atual à bloquear:</label>
                        <div class="col-md-6 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['dt_atual_bloqueio']) ? $EspelhoConfiguracao[0]['dt_atual_bloqueio'] : ""; ?>" name="data_atual_bloqueio" id="data_atual_bloqueio" require>                                
                            </div>
                        </div>
                    </div>
                    -->

                    <div class="form-group row">
                        <label for="limite_funcionario" class="col-sm-12 col-form-label text-left">Limite bloqueio Funcionário/Lider:</label>
                        <div class="col-md-6 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['limite_funcionario']) ? dtEn($EspelhoConfiguracao[0]['limite_funcionario'],true) : ""; ?>" name="limite_funcionario" id="limite_funcionario" require>                                
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="limite_gestor" class="col-sm-12 col-form-label text-left">Limite bloqueio Gestor:</label>
                        <div class="col-md-6 col-sm-10">
                            <div class="input-group">
                                <input class="form-control" type="date" value="<?= isset($EspelhoConfiguracao[0]['limite_gestor']) ? dtEn($EspelhoConfiguracao[0]['limite_gestor'],true) : ""; ?>" name="limite_gestor" id="limite_gestor" require>                                
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar</button>
                </div>
            </div><!-- end main -->

            <div class="card">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Exceção Gestor</h4>
                    </div>
                </div>
                <div class="card-body">
                    

                    <div class="form-group row mb-2">
                        <label for="keyword" class="col-sm-12 col-form-label text-left">Procurar Gestor:</label>
                        <div class="col-10">
                            <div class="input-group">
                                <input type="text" name="keyword" value="" class="form-control form-control-sm col-3" placeholder="chapa / nome">
                                <div class="input-group-prepend input-group-append bg-success">
                                    <span class="input-group-text pt-0 pb-0"><button onclick="procurarFuncionario()" type="button" class="btn btn-primary btn-xxs"><i class="fas fa-search"></i></button></span>
                                </div>
                                <select name="chapa" id="chapa" class="form-control form-control-sm">
                                </select>
                                <input type="date" class="form-control form-control-sm col-3" name="data_limite">
                            </div>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-info btn-xxs btn-block" type="button" onclick="adicionaGestor()"><i class="mdi mdi-subdirectory-arrow-right"></i> adicionar</button>
                        </div>
                    </div>

                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center"><b>Chapa</b></th>
                                <th><b>Nome</b></th>
                                <th class="text-center"><b>Data Fim</b></th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($resGestor): ?>
                                <?php foreach($resGestor as $key => $Gestor): ?>
                                    <tr>
                                        <td width="100" class="text-center"><?= $Gestor['chapa']; ?></td>
                                        <td><?= $Gestor['nome']; ?></td>
                                        <td width="100" class="text-center"><?= dtBr($Gestor['data_limite']); ?></td>
                                        <td width="50" class="text-center"><button class="btn btn-xxs btn-danger" type="button" onclick="return removerGestor('<?= $Gestor['id']; ?>')">remover</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">
                                        <div class="alert alert-warning2 border-0 m-0" role="alert">
                                            <i class="fas fa-info-circle"></i> Nenhum registro encontrado.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>


        </div>
    </div><!-- container -->
    <script>
        const salvaDados = () => {

            let dados = {
                "data_inicio": $("#data_inicio").val(),
                "data_fim": $("#data_fim").val(),
                "data_inicio_bloqueio": $("#data_inicio_bloqueio").val(),
                "data_fim_bloqueio": $("#data_fim_bloqueio").val(),
                "data_atual_bloqueio": $("#data_atual_bloqueio").val(),
                "limite_funcionario": $("#limite_funcionario").val(),
                "limite_gestor": $("#limite_gestor").val(),
            }

            if (dados.data_inicio == "") {
                exibeAlerta("error", "<b>Data de início</b> não informada.");
                return false;
            }
            if (dados.data_fim == "") {
                exibeAlerta("error", "<b>Data fim</b> não informada.");
                return false;
            }

            openLoading();

            $.ajax({
                url: "<?= base_url('ponto/espelho/action/espelho_configuracao'); ?>",
                type: 'POST',
                data: dados,
                success: function(result) {

                    var response = JSON.parse(result);

                    if (response.tipo != 'success') {
                        exibeAlerta(response.tipo, response.msg, 2);
                    } else {
                        exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    }

                },
            });

        }
        const adicionaGestor = () => {
            let dados = {
                'chapa'         : $("#chapa").val(),
                'data_limite'   : $("[name=data_limite]").val(),
            }
            console.log(dados);
            if(dados.chapa == '' || dados.chapa == null){exibeAlerta('error', '<b>Gestor</b> não selecionado.'); return false;}
            if(dados.data_limite == ''){exibeAlerta('error', '<b>Data limite</b> não informada.'); return false;}

            openLoading();

            $.ajax({
                url: "<?= base_url('ponto/espelho/action/cadastrar_gestor_excecao'); ?>",
                type: 'POST',
                data: dados,
                success: function(result) {

                    var response = JSON.parse(result);

                    if (response.tipo != 'success') {
                        exibeAlerta(response.tipo, response.msg, 2);
                    } else {
                        exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    }

                },
            });

        }
        const procurarFuncionario = () => {
    
            let keyword = $('[name=keyword]').val();
            if(keyword.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }

            openLoading();
            
            $("select[name=chapa]").html('');

            $.ajax({
                url: base_url + '/relatorio/gerar/action/lista_funcionarios',
                type: 'POST',
                data: {
                    'keyword'  : keyword
                },
                success: function(result) {

                    openLoading(true);

                    try {
                        var response = JSON.parse(result);

                        $("select[name=chapa]").append('<option value="">Selecione o Funcionário ('+response.length+')</option>');
                        if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                        for(var x = 0; x < response.length; x++){
                            $("select[name=chapa]").append('<option value="'+response[x].CHAPA+'">'+response[x].NOME + ' - ' +response[x].CHAPA+'</option>');
                        }

                    } catch (e) {
                        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                    }

                },
            });

        }
        const removerGestor = (idGestor) => {

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

                    let dados = {"id":idGestor};

                    $.ajax({
                        url: "<?= base_url('ponto/espelho/action/excluir_gestor_excecao') ?>",
                        type:'POST',
                        data:dados,
                        success:function(result){
                            var response = JSON.parse(result);

                            if (response.tipo != 'success') {
                                exibeAlerta(response.tipo, response.msg, 2);
                            } else {
                                exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                            }

                        },
                    });

                }
            });

        }
    </script>