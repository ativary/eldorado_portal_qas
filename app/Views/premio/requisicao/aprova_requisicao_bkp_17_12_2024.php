<div class="container-fluid"> 
    <div class="row">
    
        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                    <div class="col-6 text-right">
                        <div class="button-items">
                        </div>
                    </div>
                    </div>
                </div>
                
                <div class="card-body">

                    <?= exibeMensagem(true); ?>
                    <div class="row">
                        <div class="col-12 mb-1 mt-1 text-center">
                            <a href="javascript:void(0);" onclick="return reprovar_lista()" class="btn btn-danger waves-effect waves-light btn-xxs"><i class="fa fa-times"></i> Reprovar Selecionados</a>&nbsp&nbsp&nbsp
                            <a href="javascript:void(0);" onclick="return aprovar_lista()" class="btn btn-primary waves-effect waves-light btn-xxs"><i class="fa fa-check"></i> Aprovar Selecionados</a>
                            <?php if($isAdmin) {?>
                                &nbsp&nbsp&nbsp<a href="javascript:void(0);" onclick="return calcularRH_lista()" class="btn btn-warning waves-effect waves-light btn-xxs"><i class="fa fa-calculator"></i> Calcular (RH Master)</a>
                            <?php } ?>
                            <?php if($isAdmin) {?>
                                &nbsp&nbsp&nbsp<a href="javascript:void(0);" onclick="return aprovarRH_lista()" class="btn btn-success waves-effect waves-light btn-xxs"><i class="fa fa-check-double"></i> Aprovar (RH Master)</a>
                            <?php } ?>
                            <?php if($isAdmin) {?>
                                &nbsp&nbsp&nbsp<a href="javascript:void(0);" onclick="return sincRM_lista(<?= $resParam[0]['ANOCOMP']; ?>, <?= $resParam[0]['MESCOMP']; ?>,<?= $resParam[0]['PERIODO']; ?>)" class="btn btn-purple waves-effect waves-light btn-xxs"><i class="fa fa-recycle"></i> Sincronizar com RM Folha</a>
                            <?php } ?>
                        </div>
                    </div>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%; font-size: 12px;">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkall"></th>
                                <th class="text-center" width="30">ID</th>
                                <th width="70">Tipo</th>
                                <th width="70">Status</th>
                                <th width="70">Data</th>
                                <th width="150">Requisitante</th>
                                <th width="150">Aprovador</th>
                                <th width="150">Prêmio</th>
                                <th width="150">Período de Ponto</th>
                                <th class="text-center" width="50">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($resRequisicao): ?>
                                <?php foreach($resRequisicao as $key => $Requisicao): ?>
                                    <tr data-linha="<?= $Requisicao['id'] ?>">
                                        <td><input data-check class="data_check_req" type="checkbox" data-req="<?= $Requisicao['id'] ?>" data-aprova="<?= $Requisicao['id_aprova'] ?>" data-status="<?= $Requisicao['status'] ?>" data-codevento="<?= $Requisicao['codevento'] ?>"></td>
                                        <td class="text-left"><?= $Requisicao['id'] ?></td>
                                        <td class="text-left">
                                        <?php
                                            switch($Requisicao['tipo']){
                                                case 'M': echo 'Mensal'; break;
                                                case 'C': echo 'Complementar'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-center">
                                        <?php
                                            switch($Requisicao['status']){
                                                case 'A': echo '<span class="badge badge-warning">Aguardando RH Calcular</span>'; break;
                                                case 'C': echo '<span class="badge badge-success">Aguardando RH Aprovar</span>'; break;
                                                case 'H': echo '<span class="badge badge-purple">Aguardando Sincronização</span>'; break;
                                                case 'E': echo '<span class="badge badge-info">&nbsp&nbspAguardando Aprovação&nbsp&nbsp</span>'; break;
                                                case 'S': echo '<span class="badge badge-primary">Requisição Sincronizada</span>'; break;
                                                case 'R': echo '<span class="badge badge-danger">&nbsp&nbsp&nbsp&nbspReprovado&nbsp&nbsp&nbsp&nbsp</span>'; break;
                                                default: echo '';
                                            }
                                        ?>
                                        </td>
                                        <td class="text-left"><?= date('d/m/Y', strtotime($Requisicao['dt_requisicao'])) ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['nome_requisitor'].' - '.$Requisicao['chapa_requisitor'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['nome_coordenador'].' - '.$Requisicao['chapa_coordenador'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['nome_premio'] ?></td>
                                        <td class="text-left" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $Requisicao['per_ponto_br'] ?></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a id="btn_ver" name="btn_ver" href="<?= base_url('premio/requisicao/ver_aprova_req/'.$Requisicao['id'].'/'.$Requisicao['chapa_coordenador']) ?>" class="btn btn-soft-success waves-effect waves-light btn-xxs"><i class="fa fa-eye"></i></a>
                                                <?php if(0>1) {  // Desativado em 26/11?>
                                                    <a href="javascript:void(0);" onclick="return reprovar('<?= $Requisicao['id']; ?>')" class="btn btn-soft-pink waves-effect waves-light btn-xxs <?php if($Requisicao['status'] == 'R' or $Requisicao['status'] == 'S' or ($Requisicao['status'] != 'E' and !$isAdmin)) {echo 'disabled';} ?>"><i class="fa fa-times"></i> Reprovar</a>
                                                    <a href="javascript:void(0);" onclick="return aprovar('<?= $Requisicao['id_aprova']; ?>')" class="btn btn-soft-primary waves-effect waves-light btn-xxs <?php if($Requisicao['status'] != 'E') {echo 'disabled';} ?>"><i class="fa fa-check"></i> Aprovar</a>
                                                <?php } ?>
                                                <?php if(0>1 and $isAdmin and $Requisicao['status'] != 'C' and $Requisicao['status'] != 'H') {?>
                                                    <a href="javascript:void(0);" onclick="return calcularRH(<?= $Requisicao['id']; ?>)" class="btn btn-soft-warning waves-effect waves-light btn-xxs  <?php if($Requisicao['status'] != 'A') {echo 'disabled';}?>"><i class="fa fa-calculator"></i> &nbsp&nbsp&nbspRH Calcular&nbsp&nbsp&nbsp</a>
                                                <?php } ?>
                                                <?php if(0>1 and $isAdmin and $Requisicao['status'] == 'C') {?>
                                                    <a href="javascript:void(0);" onclick="return aprovarRH(<?= $Requisicao['id']; ?>)" class="btn btn-soft-success waves-effect waves-light btn-xxs"><i class="fa fa-check-double"></i> &nbsp&nbsp&nbsp&nbspRH Aprovar&nbsp&nbsp&nbsp&nbsp</a>
                                                <?php } ?>
                                                <?php if(0>1 and $isAdmin and $Requisicao['status'] == 'H') {?>
                                                    <a href="javascript:void(0);" onclick="return sincRM(<?= $Requisicao['id']; ?>, '<?= $Requisicao['codevento']; ?>', <?= $resParam[0]['ANOCOMP']; ?>, <?= $resParam[0]['MESCOMP']; ?>,<?= $resParam[0]['PERIODO']; ?>)" class="btn btn-soft-purple waves-effect waves-light btn-xxs"><i class="fa fa-recycle"></i> Sincronizar RM</a>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<!-- modal -->
<div class="modal" id="modalSincRM" tabindex="1" role="dialog" aria-labelledby="modalSincRM" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="modalAdicionalabel"><span class="oi oi-people"></span> Sincronizar com o RM Folha</h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body">

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="mescomp" style="width: 150px;">Mês Competência: </label>
					</div>
					<input type="number" id="mescomp" name="mescomp" value="" style="border:1px solid #d6d6d6;" min="1" max="12">
				</div>

                <div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="anocomp" style="width: 150px;">Ano Competência: </label>
					</div>
					<input type="number" id="anocomp" name="anocomp" value="" style="border:1px solid #d6d6d6;" min="2024">
				</div>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="periodo" style="width: 150px;">Período: </label>
					</div>
					<select class="custom-select" id="periodo" name="periodo">
						<option selected value=""></option>
						<option value="5">Período 5</option>
						<option value="6">Período 6</option>
						<option value="9">Período 9</option>
						<option value="10">Período 10</option>
					</select>
				</div>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" style="width: 150px;" for="codEvento">Cod. Evento: </label>
					</div>
					<input type="text" id="codevento" name="codevento" style="border:1px solid #d6d6d6;" maxlength="4">
				</div>

				<input type="hidden" id="id_req" name="id_req">

			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
				<button type="button" class="btn btn-success" onclick="return SincRMProc();"> <i class="fa fa-check"></i> Confirmar </button>
			</div>
		</div>
	</div>


</div>

<!-- modal -->

<style> 
td {
    padding: 0.5rem !important;
} 
</style>
<script>
$(document).ready(function() {
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : -1,
        "aaSorting"         : [[1, "desc"]]
    });
    $("#checkall").click(function() {
        console.log(document.getElementById("checkall").checked);
        if (document.getElementById("checkall").checked) {
            $(".data_check_req").prop('checked', true);
        } else {
            $(".data_check_req").prop('checked', false);
        }
    });
    $("#btn_ver").hover(function() {
        $(this).css('cursor','pointer').attr('title', 'Visualizar requisição');
    }, function() {
        $(this).css('cursor','auto');
    });
});

const reprovar_lista = () => {
    var valido = true;
    var achou = false;
    var ids = '';
    $(".data_check_req").each(function () {   
        if ($(this).is(':checked')) {
            achou = true;
            var req = $(this).attr('data-req');
            var sts = $(this).attr('data-status');
            ids = ids.concat(req, ',');
            if(sts == 'R') {
                console.log(sts);
                valido = false;
            }
            console.log(ids);
        }
    });
    if (!achou || !valido){
        exibeAlerta('error', 'Seleção inválida para reprovar');
    } else {
        reprovar(ids);
    }
}

const aprovar_lista = () => {
    var valido = true;
    var achou = false;
    var ids = '';
    $(".data_check_req").each(function () {   
        if ($(this).is(':checked')) {
            achou = true;
            var req = $(this).attr('data-aprova');
            var sts = $(this).attr('data-status');
            ids = ids.concat(req, ',');
            if(sts != 'E') {
                console.log(sts);
                valido = false;
            }
            console.log(ids);
        }
    });
    if (!achou || !valido){
        exibeAlerta('error', 'Seleção inválida para aprovar');
    } else {
        aprovar(ids);
    }
}

const calcularRH_lista = () => {
    var valido = true;
    var achou = false;
    var ids = '';
    $(".data_check_req").each(function () {   
        if ($(this).is(':checked')) {
            achou = true;
            var req = $(this).attr('data-req');
            var sts = $(this).attr('data-status');
            ids = ids.concat(req, ',');
            if(sts != 'A' && sts != 'C') {
                console.log(sts);
                valido = false;
            }
            console.log(ids);
        }
    });
    if (!achou || !valido){
        exibeAlerta('error', 'Seleção inválida para RH Master Calcular');
    } else {
        calcularRH(ids);
    }
}

const aprovarRH_lista = () => {
    var valido = true;
    var achou = false;
    var ids = '';
    $(".data_check_req").each(function () {   
        if ($(this).is(':checked')) {
            achou = true;
            var req = $(this).attr('data-req');
            var sts = $(this).attr('data-status');
            ids = ids.concat(req, ',');
            if(sts != 'C') {
                console.log(sts);
                valido = false;
            }
            console.log(ids);
        }
    });
    if (!achou || !valido){
        exibeAlerta('error', 'Seleção inválida para RH Master Aprovar');
    } else {
        aprovarRH(ids);
    }
}

const reprovar = async (id) => {
    var msg = 'Deseja reprovar esta <b>requisição</b>?';
    var msgMotivo = 'Digite o motivo de reprovação da requisição...';
    if(id.slice(0,-1).includes(',')) {
        msg = 'Deseja reprovar estas <b>requisições</b>?';
        msgMotivo = 'Digite o motivo de reprovação das requisições...';
    }
    const { value: motivo } = await Swal.fire({
        input: "textarea",
        inputLabel: "Motivo de reprovação",
        inputPlaceholder: msgMotivo,
        inputAttributes: {
            "aria-label": "Digite o motivo de reprovação aqui"
        },
        inputValidator: (value) => {
            if (!value) {
                return "É necessário informar o motivo de reprovação!";
            }
        },
        showCancelButton: true,
        confirmButtonText: `Avançar`,
        cancelButtonText: `Cancelar`
    });
    if (motivo) {
        console.log(`${motivo}`);
        Swal.fire({
            icon: 'question',
            title: msg,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Sim Reprovar`,
            denyButtonText: `Cancelar`,
            showCancelButton: false,
            showCloseButton: false,
            allowOutsideClick: false,
            width: 600,
        }).then((result) => {
            if (result.isConfirmed) {
                openLoading();
                let dados = {"id":id, "motivo":`${motivo}`};

                $.ajax({
                    url: "<?= base_url('premio/requisicao/action/reprovar') ?>",
                    type:'POST',
                    data:dados,
                    success:function(result){
                        var response = JSON.parse(result);
                        exibeAlerta(response.tipo, response.msg, 3, "<?= base_url('premio/requisicao/aprova_requisicao') ?>");
                    },
                });

            }
        });
    }

}

const aprovar = (id) => {
    var msg = 'Deseja aprovar esta <b>requisição</b>?';
    if(id.slice(0,-1).includes(',')) {msg = 'Deseja aprovar estas <b>requisições</b>?';}
    Swal.fire({
        icon: 'question',
        title: msg,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim Aprovar`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {
            openLoading();
            let dados = {"id":id};

            $.ajax({
                url: "<?= base_url('premio/requisicao/action/aprovar') ?>",
                type:'POST',
                data:dados,
                success:function(result){
                    var response = JSON.parse(result);
                    exibeAlerta(response.tipo, response.msg, 3, "<?= base_url('premio/requisicao/aprova_requisicao') ?>");
                },
            });

        }
    });

}

const aprovarRH = (id) => {
    var msg = 'Deseja aprovar esta <b>requisição</b> como RH Master?';
    if(id.slice(0,-1).includes(',')) {msg = 'Deseja aprovar estas <b>requisições</b> como RH Master?';}
    
    Swal.fire({
        icon: 'question',
        title: msg,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim Aprovar`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {
            openLoading();
            let dados = {"id":id};

            $.ajax({
                url: "<?= base_url('premio/requisicao/action/aprovarRH') ?>",
                type:'POST',
                data:dados,
                success:function(result){
                    var response = JSON.parse(result);
                    exibeAlerta(response.tipo, response.msg, 3, "<?= base_url('premio/requisicao/aprova_requisicao') ?>");
                },
            });

        }
    });

}

const calcularRH = (id) => {
    var msg = 'Deseja calcular esta <b>requisição</b>?';
    if(id.slice(0,-1).includes(',')) {msg = 'Deseja calcular estas <b>requisições</b>?';}
    Swal.fire({
        icon: 'question',
        title: msg,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim Calcular`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {
            openLoading();
            let dados = {"id":id};

            $.ajax({
                url: "<?= base_url('premio/requisicao/action/calcularRH') ?>",
                type:'POST',
                data:dados,
                success:function(result){
                    var response = JSON.parse(result);
                    exibeAlerta(response.tipo, response.msg, 5, "<?= base_url('premio/requisicao/aprova_requisicao') ?>");
                },
            });

        }
    });
}

const sincRM_lista = (ano, mes, per) => {
    var valido = true;
    var achou = false;
    var codevento = '0089';
    var ids = '';
    $(".data_check_req").each(function () {   
        if ($(this).is(':checked')) {
            achou = true;
            var req = $(this).attr('data-req');
            var sts = $(this).attr('data-status');
            codevento = $(this).attr('data-codevento');
            ids = ids.concat(req, ',');
            if(sts != 'H') {
                console.log(sts);
                valido = false;
            }
            console.log(ids);
        }
    });
    if (!achou || !valido){
        exibeAlerta('error', 'Seleção inválida para Sincronizar com o RM');
    } else {
        sincRM(ids, codevento, ano, mes, per);
    }
}

const sincRM = (id, codevento, ano, mes, per) => {
	//abre modal
    console.log(id, codevento);
    console.log(ano, mes, per);
    $("#id_req").val(id);
    $("#codevento").val(codevento);
    $("#anocomp").val(ano);
    $("#mescomp").val(mes);
    $("#periodo").val(per);
    $("#modalSincRM").modal();
}

const SincRMProc = () => {

    if($("#anocomp").val() == ""){ exibeAlerta("error", "<b>Ano de competência</b> não informado."); return false; }
    if($("#mescomp").val() == ""){ exibeAlerta("error", "<b>Mês de competência</b> não informado."); return false; }
    
    let dados = {
        "id": $("#id_req").val(),
        "periodo": $("#periodo").val(),
        "codevento": $("#codevento").val(),
        "mescomp": parseInt($("#mescomp").val()),
        "anocomp": parseInt($("#anocomp").val()),
    };

    if(dados.id == ""){ exibeAlerta("error", "<b>Requisição</b> não informada."); return false; }
    if(dados.periodo == ""){ exibeAlerta("error", "<b>Período</b> não informado."); return false; }
    if(dados.codevento == ""){ exibeAlerta("error", "<b>Código do Evento</b> não informado."); return false; }
    if(dados.mescomp < 1 || dados.mescomp > 12){ exibeAlerta("error", "<b>Mês de competência</b> inválido."); return false; }
    if(dados.anocomp < 2024 || dados.anocomp > 3000){ exibeAlerta("error", "<b>Ano de competência</b> inválido."); return false; }

    Swal.fire({
        icon: 'question',
        title: 'Confirma a sincronização desta <b>requisição com o RM Folha</b>?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim Sincronizar`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {
            openLoading();

            //console.log(dados);

            $.ajax({
                url: "<?= base_url('premio/requisicao/action/sincRM') ?>",
                type:'POST',
                data:dados,
                success:function(result){
                    var response = JSON.parse(result);
                    exibeAlerta(response.tipo, response.msg, 5, "<?= base_url('premio/requisicao/aprova_requisicao') ?>");
                },
            });
        }
    });

}

</script>

<?php
loadPlugin(array('datatable','select2'));
?>