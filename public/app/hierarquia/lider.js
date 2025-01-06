"use strict";
const procurarLider = () => {
    
    let keywordLider = $('[name=lider_keyword]').val();
    if(keywordLider.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }

    openLoading();

    $('[data-div-funcionarios]').addClass('d-none');
    $("select[name=lider]").html('');

    $.ajax({
        url: base_url + '/hierarquia/lider/action/lista_funcionarios_gestor',
        type: 'POST',
        data: {
            'keywordLider'  : keywordLider,
            'chapaGestor'   : chapaGestor,
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);

                $("select[name=lider]").append('<option value="">Selecione o Líder ('+response.length+')</option>');
                if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                for(var x = 0; x < response.length; x++){
                    $("select[name=lider]").append('<option value="'+response[x].CHAPA+'">'+response[x].NOME + ' - ' +response[x].CHAPA+'</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const procurarColaborador = () => {

    let keywordLider = $('[name=colaborador_keyword]').val();
    if(keywordLider.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }

    openLoading();
    $("select[name=colaborador]").html('');

    $.ajax({
        url: base_url + '/hierarquia/lider/action/lista_funcionarios_gestor',
        type: 'POST',
        data: {
            'keywordLider'  : keywordLider,
            'chapaGestor'   : chapaGestor,
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);

                $("select[name=colaborador]").append('<option value="">Selecione o Colaborador ('+response.length+')</option>');
                if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                for(var x = 0; x < response.length; x++){
                    $("select[name=colaborador]").append('<option value="'+response[x].CHAPA+'">'+response[x].CHAPA + ' - ' +response[x].NOME+' - ' +response[x].NOMEFUNCAO+'</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const cadastrarLiderExcecao = () => {

    try {

        let dados = {
            'chapaColaborador'  : $("[name=colaborador]").val(),
            'chapaGestor'       : chapaGestor,
            'periodoInicio'     : $("[name=colaborador_periodo_inicio]").val(),
            'periodoTermino'    : $("[name=colaborador_periodo_termino]").val(),
            'idLider'           : id
        }

        if(dados.chapaColaborador == null || dados.chapaColaborador == ''){exibeAlerta('error', '<b>Colaborador</b> não selecionado.'); return false;}
        if(dados.chapaGestor == null || dados.chapaGestor == ''){exibeAlerta('error', '<b>Gestor</b> não identificado.'); return false;}
        if(dados.idLider == null || dados.idLider == ''){exibeAlerta('error', '<b>Líder</b> não identificado.'); return false;}
        if(dados.periodoInicio == ''){exibeAlerta('error', '<b>Período de início</b> não informado.'); return false;}
        if(dados.periodoTermino != ''){
            if(dados.periodoTermino < dados.periodoInicio){exibeAlerta('error', '<b>Período de término</b> não poder ser menor que o <b>período de início.</b>'); return false;}
        }

        openLoading();

        $.ajax({
            url: base_url + '/hierarquia/lider/action/cadastrar_lider_excecao',
            type: 'POST',
            data: dados,
            success: function(result) {

                openLoading(true);

                try {
                    
                    var response = JSON.parse(result);
                    if(response.tipo == "success") {
						exibeAlerta(response.tipo, response.msg, 3, window.location.href);
					} else {
						exibeAlerta(response.tipo, response.msg);
					}

                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }

            },
        });

    } catch (e) {
        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
    }
    
}
const selecionaLider = (chapaLider) => {
    if(chapaLider != ''){
        $('[data-div-funcionarios]').removeClass('d-none');
    }else{
        $('[data-div-funcionarios]').addClass('d-none');
    }
}
const exibeFuncionarios = () => {

    openLoading();

    $(".funcionarios").html('');
    $('.funcionarios').bootstrapDualListbox('refresh', true);

    $.ajax({
        url: base_url + '/hierarquia/lider/action/lista_funcionarios_secoes',
        type: 'POST',
        data: {
            'codsecao'      : $("[data-secao]").val(),
            'codfuncao'     : $("[data-funcao]").val(),
            'action'        : 'ok',
            'chapaGestor'   : chapaGestor
        },
        success: function(result) {

            openLoading(true);

            try {
                
                var response = JSON.parse(result);
                if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                
                if(response.length > 0){
                    for(var x = 0; x < response.length; x++){
                        $(".funcionarios").append('<option value="'+response[x].CHAPA+'">'+response[x].CHAPA + ' - ' +response[x].NOME+' - ' +response[x].NOMEFUNCAO+'</option>');
                    }
                }

                if(typeof resFuncionariosLider !== 'undefined'){
                    var Funcionario = JSON.parse(resFuncionariosLider);
                    for(var x = 0; x < Funcionario.length; x++){
                        $(".funcionarios").append('<option value="'+Funcionario[x].CHAPA+'" selected>'+Funcionario[x].CHAPA + ' - ' +Funcionario[x].NOME+' - ' +Funcionario[x].NOMEFUNCAO+'</option>');
                    }
                }

                $('.funcionarios').bootstrapDualListbox('refresh', true);

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const cadastrarLider = () => {

    try {

        let dados = {
            'chapaLider'        : $("[name=lider]").val(),
            'chapaFuncionarios' : $(".funcionarios").val(),
            'periodoInicio'     : $("[name=periodo_inicio]").val(),
            'periodoTermino'    : $("[name=periodo_termino]").val(),
            'operacao'          : $("[name=operacao]").val(),
            'chapaGestor'       : chapaGestor,
        }

        if(dados.chapaLider == null || dados.chapaLider == ''){exibeAlerta('error', '<b>Líder</b> não selecionado.'); return false;}
        if(dados.periodoInicio == ''){exibeAlerta('error', '<b>Período de início</b> não informado.'); return false;}
        if(dados.periodoTermino != ''){
            if(dados.periodoTermino < dados.periodoInicio){exibeAlerta('error', '<b>Período de término</b> não poder ser menor que o <b>período de início.</b>'); return false;}
        }
        if(dados.chapaFuncionarios.length <= 0){exibeAlerta('error', 'Nenhum funcionário atribuido ao <b>Líder</b>.'); return false;}
        if(dados.chapaGestor == null || dados.chapaGestor == ''){exibeAlerta('error', '<b>Gestor</b> não identificado.'); return false;}

        openLoading();

        $.ajax({
            url: base_url + '/hierarquia/lider/action/cadastrar_lider',
            type: 'POST',
            data: dados,
            success: function(result) {

                openLoading(true);

                try {
                    
                    var response = JSON.parse(result);
                    if(response.tipo == "success") {
						exibeAlerta(response.tipo, response.msg, 3, base_url+'/hierarquia/lider/editar/'+response.cod);
					} else {
						exibeAlerta(response.tipo, response.msg);
					}

                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }

            },
        });

    } catch (e) {
        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
    }

}
const alterarLider = () => {

    try {

        let dados = {
            'chapaLider'        : $("[name=lider]").val(),
            'chapaFuncionarios' : $(".funcionarios").val(),
            'periodoInicio'     : $("[name=periodo_inicio]").val(),
            'periodoTermino'    : $("[name=periodo_termino]").val(),
            'operacao'          : $("[name=operacao]").val(),
            'id'                : id,
            'chapaGestor'       : chapaGestor,
        }

        if(dados.chapaLider == null || dados.chapaLider == ''){exibeAlerta('error', '<b>Líder</b> não selecionado.'); return false;}
        if(dados.periodoInicio == ''){exibeAlerta('error', '<b>Período de início</b> não informado.'); return false;}
        if(dados.periodoTermino != ''){
            if(dados.periodoTermino < dados.periodoInicio){exibeAlerta('error', '<b>Período de término</b> não poder ser menor que o <b>período de início.</b>'); return false;}
        }
        if(dados.chapaFuncionarios.length <= 0){exibeAlerta('error', 'Nenhum funcionário atribuido ao <b>Líder</b>.'); return false;}
        if(dados.chapaGestor == null || dados.chapaGestor == ''){exibeAlerta('error', '<b>Gestor</b> não identificado.'); return false;}

        openLoading();

        $.ajax({
            url: base_url + '/hierarquia/lider/action/alterar_lider',
            type: 'POST',
            data: dados,
            success: function(result) {

                openLoading(true);

                try {
                    
                    var response = JSON.parse(result);
                    if(response.tipo == "success") {
						exibeAlerta(response.tipo, response.msg, 3, base_url + '/hierarquia/lider');
					} else {
						exibeAlerta(response.tipo, response.msg);
					}

                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }

            },
        });

    } catch (e) {
        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
    }

}
const selecionaGestor = (chapaGestor) => {
    openLoading();
    document.getElementById('form_gestor').action=base_url+'/hierarquia/lider/index/'+chapaGestor;
    document.getElementById('form_gestor').submit();
}
const tipoLider = (idLider) => {

    openLoading();
    
    $.ajax({
        url: base_url + '/hierarquia/lider/action/tipo_aprovador',
        type: 'POST',
        data: {
            'idLider'       : idLider,
            'tipoAprovador' : ($("#aprovador").prop('checked')) ? 'S' : 'N' 
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);
                exibeAlerta(response.tipo, response.msg);
            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
const removerLider = (idLider) => {

    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente remover este Líder?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim remover`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if (result.isConfirmed) {

			$.ajax({
				url: base_url + '/hierarquia/lider/action/remover_lider',
				type:'POST',
				data:{
                    'idLider': idLider
                },
				success:function(result){
					var response = JSON.parse(result);
					
					if(response.tipo == "success"){
                        exibeAlerta(response.tipo, response.msg, 5, window.location.href);
                    }else{
                        exibeAlerta(response.tipo, response.msg);
                    }
				},
			});

		}
	});

}
const removerLiderExcecao = (idLiderExcecao) => {

    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente remover este Líder Exceção?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim remover`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if (result.isConfirmed) {

			$.ajax({
				url: base_url + '/hierarquia/lider/action/remover_lider_excecao',
				type:'POST',
				data:{
                    'idLider': id,
                    'idLiderExcecao': idLiderExcecao
                },
				success:function(result){
					var response = JSON.parse(result);
					
					if(response.tipo == "success"){
                        exibeAlerta(response.tipo, response.msg, 5, window.location.href);
                    }else{
                        exibeAlerta(response.tipo, response.msg);
                    }
				},
			});

		}
	});

}
$(document).ready(function(){
    if($('.funcionarios').length > 0){
        $('.funcionarios').bootstrapDualListbox({
            nonSelectedListLabel    : 'Lista de funcionários',
            selectedListLabel       : 'Funcionários Atribuidos',
            preserveSelectionOnMove : 'moved',
            moveOnSelect            : true
        });
    }
});