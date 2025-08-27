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
                    
                    <form action="" method="post" id="form_filtro">
                        <div class="form-group row mb-0">
                            <label for="keywords" class="col-form-label col-sm-2 text-left-sm">Colaborador:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" name="keywords" id="keywords" value="" class="form-control form-control-sm col-3" placeholder="Chapa ou Nome">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label for="dataIni" class="col-sm-2 col-form-label text-right text-left-sm">Data Início: <span class="text-danger">*</span></label>
                            <div class="col-sm-4"><input class="form-control form-control-sm" type="date" name="dataIni" id="dataIni" value="<?= somarDias(date('Y-m-d'), -30) ?>"></div>
                            <label for="dataFim" class="col-sm-2 col-form-label text-right text-left-sm">Data Fim: <span class="text-danger">*</span></label>
                            <div class="col-sm-4"><input class="form-control form-control-sm" type="date" name="dataFim" id="dataFim" value="<?= date('Y-m-d') ?>"></div>
                        </div>
                        <div class="form-group row mb-0">
                            <label for="situacao" class="col-form-label col-sm-2 text-left-sm">Situação:</label>
                            <div class="col-sm-10">
                                <select name="situacao" id="situacao" class="form-control form-control-sm">
                                    <option value="">-Todos-</option>
                                    <option value="0">Pendente envio PontoMais</option>
                                    <option value="1">Integrado com Sucesso</option>
                                    <option value="9">Erro</option>
                                    <option value="8">Cancelado</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class = "card-footer text-center">
					<button class="btn btn-primary btn-sm btn-xxs bteldorado_7" id="btnfiltro" type="button" onclick="return Filtro()"><i class="fas fa-search"></i> Filtrar</button>
				</div>
            </div>

            <div class="card">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col mb-1 mt-1">Resultado Logs</h4>
                        <div class="col text-right">
                            <div class="btn-group dropleft mb-2 mb-md-0">
                                <button type="button" class="btn btn-primary dropdown-toggle pl-1 pr-1 pt-0 pb-0 bteldorado_7" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Ações Globais <i class="mdi mdi-chevron-down"></i></button>
                                <div class="dropdown-menu">
                                    <button onclick="reprocessar()" type="button" class="dropdown-item text-success" title="Reprocessar"><i class="mdi mdi-refresh"></i> Reprocessar selecionados</button>
                                    <button onclick="cancelar()" type="button" class="dropdown-item text-danger" title="Cancelar"><i class="mdi mdi-trash-can-outline"></i> Cancelar selecionados</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    

                    <table id="datatable" class="table table-sm table-bordered dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" width="30px"><input type="checkbox" class="selectall"></th>
                                <th class="text-center" width="70px">Id</th>
                                <th class="text-center">Situação</th>
                                <th class="text-center" width="70px">Chapa</th>
                                <th>Nome</th>
                                <th class="text-center" width="70px">Processo</th>
                                <th class="text-center">Mensagem</th>
                                <th class="text-center">Data</th>
                                <th class="text-center" width="70px">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
const Filtro = () => {
    
    if($("#dataIni").val() == ''){exibeAlerta('warning', '<b>Data início</b> não informado.'); return;}
    if($("#dataFim").val() == ''){exibeAlerta('warning', '<b>Data fim</b> não informado.'); return;}

    openLoading();

    $.ajax({
        url: "<?= base_url('logs/pontomais/action/busca_log') ?>",
        type: 'POST',
        data: {
            keywords    : $("#keywords").val(),
            data_inicio : $("#dataIni").val(),
            data_termino: $("#dataFim").val(),
            situacao    : $("#situacao").val()

        },
        success: function(result) {
           openLoading(true);
           $('#datatable').DataTable().destroy();
           $("#datatable tbody").html('');
           
           var json = JSON.parse(result);
           
           json.map(dados => {
                var situacao = '<span class="badge badge-danger">Erro</span>';
                var checkbox = `<input type="checkbox" class="select-item" value="${dados.ID}">`;
                switch(dados.SITUACAO){
                    case 0: var situacao = '<span class="badge badge-warning">Pendente envio PontoMais</span>'; break;
                    case 1: var situacao = '<span class="badge badge-success">Integrado com Sucesso</span>'; checkbox = ''; break;
                    case 8: var situacao = '<span class="badge badge-purple">Cancelado</span>'; checkbox = ''; break;
                }

                $("#datatable tbody").append(`
                    <tr>
                        <td class="text-center">${checkbox}</td>
                        <td class="text-center">${dados.ID}</td>
                        <td class="text-center">${situacao}</td>
                        <td class="text-center">${dados.CHAPA}</td>
                        <td>${dados.NOME}</td>
                        <td class="text-center">${(dados.ACAO == 'INSERT') ? 'Admissão' : 'Demissão'}</td>
                        <td>${(dados.MESSAGE == null) ? '' : dados.MESSAGE}</td>
                        <td class="text-center">${dados.DATALOG}</td>
                        <td class="text-center">
                            <div class="btn-group dropleft mb-2 mb-md-0">
                                <button type="button" class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="mdi mdi-dots-vertical"></i></button>
                                <div class="dropdown-menu">
                                    <button ${(dados.SITUACAO == 1 || dados.SITUACAO == 8) ? 'disabled' : ''} onclick="reprocessar('${dados.ID}')" type="button" class="dropdown-item" title="Reprocessar"><i class="mdi mdi-refresh"></i> Reprocessar</button>
                                    <button ${(dados.SITUACAO == 1 || dados.SITUACAO == 8) ? 'disabled' : ''} onclick="cancelar('${dados.ID}')" type="button" class="dropdown-item" title="Cancelar"><i class="mdi mdi-trash-can-outline"></i> Cancelar</button>
                                </div>
                            </div>
                        </td>
                        
                    </tr>
                `);
           });

           var table = $('#datatable').DataTable({
                "aLengthMenu": [
                    [25, 50, 100, 200, -1],
                    [25, 50, 100, 200, "Todos"]
                ],
                "iDisplayLength": 25,
                "aaSorting": [
                    [1, "desc"]
                ],
                "fixedHeader": true,
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]
            });
            
            $(document).on('change', '.selectall', function () {
                var isChecked = $(this).prop("checked");
                $('input[type="checkbox"].select-item', table.rows().nodes()).prop('checked', isChecked);
            });
            
            $(document).on('change', '.select-item', function () {
                var allChecked = $('.select-item:checked', table.rows().nodes()).length === $('.select-item', table.rows().nodes()).length;
                $('.selectall').prop('checked', allChecked);
            });
            
        }
    });

}
const cancelar = (id = null) => {

    var check = [];
    if(id == null){
        var marcados = $(".select-item:checked");
        let qtde = marcados.length;
        if(qtde <= 0){
            exibeAlerta('warning', 'Nenhuma linha selecionada!');
            return;
        }

        marcados.each(function(e){
            check.push($(this).val());
        });

    }else{
        check.push(id);
    }

    var ids = JSON.stringify(check);

    Swal.fire({
        icon              : 'question',
        title             : (id != null) ? 'Confirmar cancelamento deste registro?' : 'Confirmar cancelamento dos registros selecionados?',
        showDenyButton    : true,
        showCancelButton  : true,
        confirmButtonText : `Sim`,
        denyButtonText    : `Fechar`,
        showCancelButton  : false,
        showCloseButton   : false,
        allowOutsideClick : false,
        width             : 600,
        customClass      : {
            confirmButton: 'btn btn-primary bteldorado_1',
            denyButton   : 'btn btn-danger',
        }
    }).then((result) => {
        if (result.isConfirmed) {

            openLoading();

            $.ajax({
                url: "<?= base_url('logs/pontomais/action/cancela_log') ?>",
                type: 'POST',
                data: {
                    id: ids
                },
                success: function(result) {

                    openLoading(true);

                    try{

                        var response = JSON.parse(result);
                        exibeAlerta(response.tipo, response.msg);
                        Filtro();

                    } catch (e) {
                        exibeAlerta('error', 'Erro interno(JS): ' + e);
                    }

                }
            });
            
        }
    });

}
const reprocessar = (id = null) => {

    var check = [];
    if(id == null){
        var marcados = $(".select-item:checked");
        let qtde = marcados.length;
        if(qtde <= 0){
            exibeAlerta('warning', 'Nenhuma linha selecionada!');
            return;
        }

        marcados.each(function(e){
            check.push($(this).val());
        });

    }else{
        check.push(id);
    }

    var ids = JSON.stringify(check);

    Swal.fire({
        icon             : 'question',
        title            : (id != null) ? 'Confirmar reprocessamento deste registro?': 'Confirmar reprocessamento dos registros selecionados?',
        showDenyButton   : true,
        showCancelButton : true,
        confirmButtonText: `Sim`,
        denyButtonText   : `Fechar`,
        showCancelButton : false,
        showCloseButton  : false,
        allowOutsideClick: false,
        width            : 600,
        customClass      : {
            confirmButton: 'btn btn-primary bteldorado_1',
            denyButton   : 'btn btn-danger',
        }
    }).then((result) => {
        if (result.isConfirmed) {

            openLoading();

            $.ajax({
                url: "<?= base_url('logs/pontomais/action/reprocessar_log') ?>",
                type: 'POST',
                data: {
                    id: ids
                },
                success: function(result) {

                    openLoading(true);

                    try{

                        var response = JSON.parse(result);
                        exibeAlerta(response.tipo, response.msg);
                        Filtro();

                    } catch (e) {
                        exibeAlerta('error', 'Erro interno(JS): ' + e);
                    }

                }
            });
            
        }
    });
}
</script>
<?php loadPlugin(array('datatable')) ?>