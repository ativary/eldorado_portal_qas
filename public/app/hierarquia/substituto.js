const selectedFunctions = new Set();

$(document).ready(function () {
    const duallistbox = $('#duallistbox').bootstrapDualListbox({
        nonSelectedListLabel: 'Funções disponíveis',
        selectedListLabel: 'Funções atribuídas',
        preserveSelectionOnMove: 'moved',
        moveOnSelect: false
    });
    
});

const carregarDualList = (idPerfil) =>{

    $.ajax({
        url: base_url + '/hierarquia/substituto/action/listar_funcoes_perfil',
        type: 'POST',
        data: {
            'idPerfil'  : idPerfil,
        },
        success: function(result) {

            var response = JSON.parse(result);  

            try {

                $('#duallistbox option').not(':selected').remove();
                const funcoes = response.map(func => ({
                    nome: func.nome,
                    id: func.id
                }));
                const novasFuncoes = funcoes.filter(func => !selectedFunctions.has(func.nome));
               
                novasFuncoes.forEach(func => {
                    $('#duallistbox').append(new Option(func.nome, func.id));
                });
            
                $('#duallistbox').bootstrapDualListbox('refresh');
            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}

const adicionarPermissao = ()  =>{

    let modulo = $("#modulo").val();

    if (modulo.trim() === "") {
        exibeAlerta("error", "Selecione um módulo!");
        return;
    }

    let existe = false;

    $("#tableModulos tbody tr").each(function () {
        let valorCelula = $(this).find("td:first").text().trim();
        if (valorCelula === modulo) {
            existe = true;
            return false;
        }
    });

    if(existe){
        exibeAlerta("error", "Esse módulo já foi adicionado!");
        return;
    }

    let novaLinha = `
        <tr>
            <td>${modulo}</td>
            <td class="text-center">
                <button class="btn btn-xxs btn-soft-danger" onclick="removerLinha(this)">
                    <span><i class="fas fa-trash"></i></span> Remover
                </button>
            </td>
        </tr>
    `;

    $("#tableModulos tbody").append(novaLinha);
}

const removerLinha = (botao) => {
    $(botao).closest("tr").remove();
}


const selecionaGestor = (chapaGestor) => {
    openLoading();
    document.getElementById('form_gestor').action=base_url+'/hierarquia/substituto/index/'+chapaGestor;
    document.getElementById('form_gestor').submit();
}

const cadastrarModulo = () =>{

    let nome = $('#nomeModulo').val();
    let funcoesSelecionadas = $('#duallistbox').val();
    let aprovador = $('#aprovador').is(':checked') ? 1 : 0;

    if(nome.length == 0){
        exibeAlerta("error", "O campo nome não pode ficar vazio.");
        return false;
    }

    const funcoes = JSON.stringify(funcoesSelecionadas);
    
    $.ajax({
        url: base_url + '/hierarquia/substituto/action/cadastrar_modulo',
        type: 'POST',
        data:{
            'funcoes': funcoes,
            'nome':  nome,
            'aprovador': aprovador
        },
        success: function(result) {

            console.log(result);
            try {
                const response = JSON.parse(JSON.parse(result));  
                if (response.tipo === 'success') {
                    exibeAlerta(response.tipo, response.msg, 2, '/hierarquia/substituto/lista');
                }
            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }
        },
        error: function(result) {
            console.log(result);
            exibeAlerta('error', 'Falha ao salvar as alterações.');
        }
    });

}

const editarModulo = (id) =>{

    let nome = $('#nomeModulo').val();
    let funcoesSelecionadas = $('#duallistbox').val();
    let aprovador = $('#aprovador').is(':checked') ? 1 : 0;

    if(nome.length == 0){
        exibeAlerta("error", "O campo nome não pode ficar vazio.");
        return false;
    }

    const funcoes = JSON.stringify(funcoesSelecionadas);
    
    $.ajax({
        url: base_url + '/hierarquia/substituto/action/editar_modulo',
        type: 'POST',
        data:{
            'id': id,
            'funcoes': funcoes,
            'nome':  nome,
            'aprovador': aprovador
        },
        success: function(result) {

            console.log(result);
            try {
                const response = JSON.parse(JSON.parse(result));  
                if (response.tipo === 'success') {
                    exibeAlerta(response.tipo, response.msg, 2, '/hierarquia/substituto/lista');
                }
            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }
        },
        error: function(result) {
            console.log(result);
            exibeAlerta('error', 'Falha ao salvar as alterações.');
        }
    });
}

const excluirModulo = (id) =>{

    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente excluir esse Módulo? Isso não pode ser desfeito.',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim, excluir`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if (result.isConfirmed) {

			$.ajax({
				url: base_url + '/hierarquia/substituto/action/excluir_modulo',
				type:'POST',
				data:{
                    'id': id
                },
				success:function(result){

                    console.log(result);

					var response = JSON.parse(JSON.parse(result)); 

					if(response.tipo == "success"){
                        exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    }else{
                        exibeAlerta(response.tipo, response.msg);
                    }
				},
			});

		}
	});
}

const procurarSubstituto = () =>  {
    

        let keywordSub = $('[name=substituto_keyword]').val();
        if(keywordSub.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }
    
        openLoading();
        $("select[name=substituto]").html('');
    
        $.ajax({
            url: base_url + '/hierarquia/substituto/action/lista_funcionarios_gestor',
            type: 'POST',
            data: {
                'colab'  : keywordSub,
            },
            success: function(result) {

                openLoading(true);
    
                try {
                    var response = JSON.parse(result);  

                    $("select[name=substituto]").append('<option value="">Selecione o Colaborador ('+response.length+')</option>');
                    if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                    for(var x = 0; x < response.length; x++){
                        $("select[name=substituto]").append('<option value="'+response[x].ID+'">'+response[x].CHAPA + ' - ' +response[x].NOME+' - ' +response[x].NOMEFUNCAO+'</option>');
                    }
    
                } catch (e) {
                    exibeAlerta('error', '<b>Erro interno:</b> ' + e);
                }
    
            },
        });
    
    
}

const inativarGestor = (idSub) =>  {

    Swal.fire({
		icon: 'question',
		title: 'Deseja realmente inativar este Substituto?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim, inativar`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if (result.isConfirmed) {

			$.ajax({
				url: base_url + '/hierarquia/substituto/action/inativar_substituto',
				type:'POST',
				data:{
                    'idSub': idSub
                },
				success:function(result){

					var response = JSON.parse(JSON.parse(result)); 

					if(response.tipo == "success"){
                        exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    }else{
                        exibeAlerta(response.tipo, response.msg);
                    }
				},
			});

		}
	});

}

let estadoInicial = [];

document.addEventListener('DOMContentLoaded', function() {
    const duallistboxSelecionados = document.querySelectorAll('#duallistbox option:checked');
    estadoInicial = Array.from(duallistboxSelecionados).map(option => option.value);
});

const atualizarGestorSubstituto = (idReq, idGestor) => {

    let funcoes = []; 
    $("#tableModulos tbody tr").each(function () {
        let modulo = $(this).find("td:first").text().split(" ")[0].trim();
        if (modulo) {
            funcoes.push(modulo);
        }
    });

    if (funcoes.length === 0) {
        exibeAlerta('warning', 'Nenhum módulo foi selecionado.');
        return;
    }

    funcoes = JSON.stringify(funcoes);

    const dataini = $('#data_inicio').val();
    const datafim = $('#data_fim').val();

    $.ajax({
        url: base_url + '/hierarquia/substituto/action/atualizar_gestor',
        type: 'POST',
        data: {
            funcoes: funcoes,
            idReq: idReq,
            dataini: dataini,
            datafim: datafim,
        },
        success: function(result) {
            try {
                const response = JSON.parse(JSON.parse(result));  
                if (response.tipo === 'success') {
                    exibeAlerta(response.tipo, response.msg, 2, '/hierarquia/substituto/index/'+ idGestor);
                }
            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }
        },
        error: function() {
            exibeAlerta('error', 'Falha ao salvar as alterações.');
        }
    });
 }

const cadastrarGestorSubstituto = () => {

    const gestor = $('select[name="gestor"] option:selected').val();
    const substituto = $('select[name="substituto"] option:selected').val();
    const chapa_gestor = $('select[name="gestor"] option:selected').text().split(' -')[0];
    const chapa_substituto = $('select[name="substituto"] option:selected').text().split(' -')[0];
    const dataini = $('#data_inicio').val();
    let datafim = '2100-01-01';

    if($('#data_fim').val()){
        datafim = $('#data_fim').val();
    }

    let funcoes = []; 
    $("#tableModulos tbody tr").each(function () {
        let modulo = $(this).find("td:first").text().split(" ")[0].trim();
        if (modulo) {
            funcoes.push(modulo);
        }
    });

    if (funcoes.length === 0) {
        exibeAlerta('warning', 'Nenhum módulo foi selecionado.');
        return;
    }

    funcoes = JSON.stringify(funcoes);

    if(chapa_substituto.length == 0 || substituto.length == 0){
        exibeAlerta('error', 'Insira um gestor substituto');
        return false;
    }

    if(dataini.length == 0){
        exibeAlerta('error', 'Insira uma data de início');
        return false;
    }

    $.ajax({
        url: base_url + '/hierarquia/substituto/action/cadastrar_gestor',
        type: 'POST',
        data: {
            'id_gestor'        : gestor,
            'id_substituto'    : substituto,
            'chapa_gestor'     : chapa_gestor,
            'chapa_substituto' : chapa_substituto,
            'dataini'          : dataini,
            'datafim'          : datafim,
            'funcoes'          : funcoes,
        },
        success: function(result) {

            try {
                console.log(result);
                var response = JSON.parse(result);  

                if(response.tipo == 'success'){
                    exibeAlerta(response.tipo, response.msg, 2, '/hierarquia/substituto/index/' + gestor);
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

   
}
