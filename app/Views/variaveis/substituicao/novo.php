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
                                <a href="<?= base_url('variaveis/substituicao') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_substituicao" role="tab"><i class="fa fa-home"></i>Salário Substituição</a>
                            </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio substituicao -->
                        <div class="tab-pane active p-3" id="auxilio_substituicao" role="tabpanel">
                        <!-- 
                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Tipo de Requisição:</label>
                                <div class="col-sm-10">
                                    <select  class="select2 custom-select form-control form-control-sm" name="tipoReq" id="tipoReq">
                                        <option value=""> ... </option>
                                        <option value="1"> Evento folha </option>
                                        <option value="2">  EPI’S: Evento folha </option>
                                        <option value="3">  Multa de Trânsito </option>
                                    </select>
                                   
                                </div>
                            </div> -->
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Funcionário Substituído:</label>
                                <div class="col-sm-10">
                                    <select onchange="selecionaFuncionarioSub(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario_sub" id="funcionario_sub">
                                        <option value="">...</option>
                                        <?php if ($resFuncionarioSub) : ?>
                                            <?php foreach ($resFuncionarioSub as $key => $DadosFunc) : ?>
                                                <?php 
                                                        switch ($DadosFunc['CODSITUACAO']) {
                                                            case 'E':
                                                                $descricao = 'Licença Mater.';
                                                                break;
                                                            case 'F':
                                                                $descricao = 'Férias.';
                                                                break;
                                                            case 'P':
                                                                $descricao = 'Af.Previdência';
                                                                break;
                                                            case 'Q':
                                                                $descricao = 'Prisão / Cárcere';
                                                                break;
                                                            case 'S':
                                                                $descricao = 'Mandato Sindical Ônus do Sindicato';
                                                                break;
                                                            case 'T':
                                                                $descricao = 'Af.Ac.Trabalho';
                                                                break;
                                                            case 'V':
                                                                $descricao = 'Aviso Prévio';
                                                                break;
                                                            case 'I':
                                                                $descricao = 'Apos. por Incapacidade Permanente';
                                                                break;
                                                            case 'A':
                                                                $descricao = 'Ativo';
                                                                break;
                                                            default:
                                                                $descricao = 'Situação desconhecida';
                                                                break;
                                                        }
                                                    
                                                    
                                                    
                                                ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($funcionario == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?> - <?=  $descricao ?> </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Funcionário Substituto:</label>
                                <div class="col-sm-10">
                                    <select onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">...</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($funcionario == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            

                            <div class="form-group row mb-2">
                                <label for="data" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span> Período:</label>
                                <div class="input-group col-sm-10 ">
                                    <input class="form-control datepicker m_data" type="date"  value="" name="data_inicio" id="data_inicio" required>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">até</span>
                                    </div>
                                    <input class="form-control datepicker m_data" type="date"  value="" name="data_fim" id="data_fim" require>
                                </div>
                            </div>
                         


                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm">Anexar:</label>
                                <div class="col-sm-10">
                                <input class="form-control filepond" type="file" name="anexo[]" id="anexo" multiple required
                                  accept="
                                    application/pdf,
                                    application/msword,
                                    application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                                    image/jpeg,
                                    image/jpg,
                                    image/png,
                                    image/gif,
                                    image/tiff,
                                    image/webp,
                                    image/bmp">
                                </div>
                            </div>

                            <input class="form-control " hidden type="text" value="" name="filial" id="filial" required>
                            <input class="form-control  " hidden type="text" value="" name="funcao" id="funcao" required>
							<input class="form-control  " hidden type="text" value="" name="funcao_sub" id="funcao_sub" required>
							

                            
                            

                        </div>
                    </div>
                    

                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success bteldorado_1" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<style>
.tab-pane {
    border-left: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #d8d8d8 !important;
    padding: 5px 0;
    height: 39px !important;
}
div:where(.swal2-icon).swal2-error {
    color: #fd7e14 !important;
    border-color: #fd7e14 !important;
}
div:where(.swal2-icon).swal2-error [class^=swal2-x-mark-line] {
    background-color: #fd7e14 !important;
}
.swal2-popup .swal2-styled.swal2-confirm {
    background-color: #225c30 !important;
    padding: 6px 20px;
}
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-fileinput/css/fileinput.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-fileinput/js/fileinput.min.js"></script>


<script>
       var fora_periodo=0 ;
       var salario = 0;
       var salario_sub = 0;
       $(document).ready(function() {
        
            verificaData();
           
            if (fora_periodo == 1) { 
                exibeAlerta("error", "Atenção! Fora do período de abertura para novas requisições."); 
            }
            // Definir a data mínima para o campo "data_inicio" como a data atual
            const today = new Date();
          
            $('#data_inicio').on('change', function() {
    const regra = <?= $param6 ?>; // Certifique-se de que $param6 seja um JSON válido.

    // Pegar a data selecionada no campo de início
    let dataInicio = new Date($(this).val());
    // Ajustar para meia-noite no horário UTC
    dataInicio.setUTCHours(0, 0, 0, 0);

    // Definir a data mínima para o campo "data_fim" como dataInicio + minimo dias
    let minimo = parseInt(regra.subistiuicao_min_dias, 10);
    minimo = minimo -1;
    let minDate = new Date(dataInicio);
    minDate.setUTCDate(dataInicio.getUTCDate() + minimo); // Adicionar os dias no horário UTC
    minDate.setUTCHours(0, 0, 0, 0); // Garantir que a data mínima está em meia-noite no UTC

    // Definir a data máxima para o campo "data_fim" como dataInicio + maximo dias
    const maximo = parseInt(regra.subistiuicao_max_dias, 10);
    let maxDate = new Date(dataInicio);
    maxDate.setUTCDate(dataInicio.getUTCDate() + maximo); // Adicionar os dias no horário UTC
    maxDate.setUTCHours(0, 0, 0, 0); // Garantir que a data máxima está em meia-noite no UTC

    // Atualizar os atributos min e max do campo "data_fim"
    $('#data_fim').attr('min', formatDate(minDate));
    $('#data_fim').attr('max', formatDate(maxDate));

    // Se o valor atual de "data_fim" for inválido, resetar o campo
    const dataFimAtual = new Date($('#data_fim').val());
    dataFimAtual.setUTCHours(0, 0, 0, 0); // Ajustar para meia-noite no horário UTC
    if (dataFimAtual < minDate || dataFimAtual > maxDate) {
        $('#data_fim').val('');
    }
});

// Evento ao sair do campo "data_fim" (blur)
$('#data_fim').on('blur', function() {
    const minDate = new Date($(this).attr('min'));
    minDate.setUTCHours(0, 0, 0, 0); // Ajustar para meia-noite no horário UTC

    const maxDate = new Date($(this).attr('max'));
    maxDate.setUTCHours(0, 0, 0, 0); // Ajustar para meia-noite no horário UTC

    const dataFimAtual = new Date($(this).val());
    dataFimAtual.setUTCHours(0, 0, 0, 0); // Ajustar para meia-noite no horário UTC

    // Verificar se a data atual está dentro do intervalo permitido
    if (dataFimAtual < minDate || dataFimAtual > maxDate || isNaN(dataFimAtual)) {
        // Mostrar o alerta do SweetAlert2 com a data mínima e máxima formatadas
        Swal.fire({
            icon: 'error',
            title: 'Data Inválida',
            text: `A data fim está fora do intervalo permitido. O intervalo permitido é de ${formatDateBR(minDate)} a ${formatDateBR(maxDate)}.`,
            confirmButtonText: 'Entendi'
        });

        // Limpar o valor do campo
        $(this).val('');
    }
});

// Função para formatar a data no formato YYYY-MM-DD para setar como min e max
function formatDate(date) {
    const year = date.getUTCFullYear();
    const month = ('0' + (date.getUTCMonth() + 1)).slice(-2);
    const day = ('0' + date.getUTCDate()).slice(-2);
    return `${year}-${month}-${day}`;
}

// Função para formatar a data no formato d/m/Y para exibir no alerta
function formatDateBR(date) {
    const day = ('0' + date.getUTCDate()).slice(-2);
    const month = ('0' + (date.getUTCMonth() + 1)).slice(-2);
    const year = date.getUTCFullYear();
    return `${day}/${month}/${year}`;
}


           
        });



const selecionarFuncionarios = (codigo) => {
        let dados = {
            "codigo":codigo,
            
        }
        const descricaoSituacao = {
            'E': 'Licença Mater.',
            'F': 'Férias.',
            'P': 'Af.Previdência',
            'Q': 'Prisão / Cárcere',
            'S': 'Mandato Sindical Ônus do Sindicato',
            'T': 'Af.Ac.Trabalho',
            'V': 'Aviso Prévio',
            'I': 'Apos. por Incapacidade Permanente',
            'A': 'Ativo'
        };
        openLoading();

        $.ajax({
            url: "<?= base_url('variaveis/substituicao/selectFunc'); ?>",
            type:'POST',
            data:dados,
            success:function(result){
               
                var response = JSON.parse(result);

               
                 // Limpa todas as opções do select com id "funcionario"
                $('#funcionario_sub').empty();
                if (response.tipo === 'error') {
                    $('#funcionario_sub').append(
                        $('<option>', {
                            value: '',
                            text: 'Nenhum funcionário encontrado'
                        })
                    );
                } else {
                    $('#funcionario_sub').append(
                            $('<option>', {
                                value: '',
                                text: '-...-'
                            })
                        );
                    // Cria uma option para cada item no response se não for um erro
                    $.each(response, function(index, func) {
                          // Busca a descrição correspondente à situação atual
                    let descricao = descricaoSituacao[func.CODSITUACAO] || 'Situação Desconhecida';
                    
                        $('#funcionario_sub').append(
                            $('<option>', {
                                value: func.CHAPA,
                                text: func.CHAPA + " - " + func.NOME + " : " + descricao
                            })
                        );
                    });
                }

                openLoading(true);
                

            },
        });
        
    }
    function verificaData() {
        const regra = <?= $param6 ?>; // Certifique-se de que $param6 seja um JSON válido.
        const funcionario =  "<?= $chapaFunc > 0 ? $chapaFunc : '0' ?>"; // Defina o valor da constante funcionario
        console.log(funcionario);
        
        const hoje = new Date();
        const diaHoje = hoje.getDate(); // Obtém o dia do mês atual

        const periodoInicio = parseInt(regra.periodo_substituicao, 10);
        const periodoFim = parseInt(regra.periodo_substituicao_fim, 10);
    
       
        hoje.setHours(0, 0, 0, 0);
       
        if (diaHoje >= periodoInicio && diaHoje <= periodoFim) {
            
            fora_periodo=0 ;
         
        } else {
            if (Array.isArray(regra.gestor) && regra.gestor.length > 0) {
               
                $.each(regra.gestor, function(index, func) {
                   
                  
                    if (func.chapa == funcionario) {
                       
                        // Converter dt_ini e dt_fim para objetos Date
                        const dtIni = new Date(`${func.dt_ini}T00:00:00`);
                        const dtFim = new Date(`${func.dt_fim}T00:00:00`);
                        if (hoje >= dtIni && hoje <= dtFim) {
                            console.log('entrou2');
                            fora_periodo = '2';
                           
                        } else {
                            console.log('entrou1');
                            fora_periodo = '1';
                           
                        }
                        return false;
                    }else{
                        console.log('entrou1');
                        fora_periodo='1';
                       
                    }
                });
            }else{
                console.log('entrou1');
                fora_periodo='1';
               
            }
            
            
        }
    }





const salvaDados = () => {
    
    let formData = new FormData();
    let resultado = 0;
    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("funcionario_sub", $("#funcionario_sub").val());
    formData.append("Nome_sub", $("#funcionario_sub option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("salario", salario);
    formData.append("salario_sub", salario_sub);
    formData.append("tipoReq", '1');
    formData.append("funcao", $("#funcao").val());
    formData.append("funcao_sub", $("#funcao_sub").val());
    formData.append("data_fim", $("#data_fim").val());
    formData.append("data_inicio", $("#data_inicio").val());
    formData.append("tipo", '1');
    formData.append("id", '');
  
 
  
    // Adiciona múltiplos arquivos ao formData
    let fileInput = $('#anexo')[0].files;
    if (fileInput.length > 0) {
        for (let i = 0; i < fileInput.length; i++) {
            formData.append("anexo[]", fileInput[i]);
        }
    }

   if( $("#funcionario_sub").val() == $("#funcionario").val() ){

        exibeAlerta("error", "<b>Funcionário Substituido e Substituto não podem ser iguais </b> ."); return false;
   }
    

    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
    if($("#funcionario_sub").val() == ""){ exibeAlerta("error", "<b>Funcionário Substituido obrigatório </b> ."); return false; }
   
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatório."); return false; }
    if ($("#data_inicio").val().trim() == "") {
        exibeAlerta("error", "<b>Data Início</b> é obrigatória.");
        return false;
    }

    if ($("#data_fim").val().trim() == "") {
        exibeAlerta("error", "<b>Data Fim</b> é obrigatória.");
        return false;
    }
     formData.append("fora_periodo",fora_periodo);
    console.log(fora_periodo);
     if (fora_periodo == 1) { 
        exibeAlerta("error", "Fora do Período de abertura."); 
        return false; 
    }


    $.ajax({
        url: "<?= base_url('variaveis/substituicao/save'); ?>",
        type:'POST',
        data:formData,
        processData: false, // impede que o jQuery processe os dados
        contentType: false, // impede que o jQuery defina o tipo de conteúdo
        success:function(result){
            console.log(result);

            var response = JSON.parse(result);
            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
                openLoading(true);
            }else{
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/substituicao'); ?>');
                
            }

        },
    });
    
}

$("#anexo").fileinput({
    showUpload: false,
    showCaption: true,
    dropZoneEnabled: true, // Mantém a dropzone visível
    fileActionSettings: {
        showRemove: true, // Exibe o botão de remover
        showZoom: false, // Oculta o botão de zoom
    },
    browseClass: "btn btn-primary",
    fileType: "any",
    showClose: false, // Esconde o botão de "x" (fechar) na visualização dos arquivos
    browseLabel: "Selecionar Arquivo", // Texto personalizado do botão de anexar
    dropZoneTitle: "Arraste o(s) arquivo(s) aqui. Para anexar mais de um arquivo arraste todos de uma vez. Os navegadores de internet não permitem arrastar um arquivo por vez. O mesmo vale para a seleção de arquivos, caso queira mais de um arquivo selecione todos de uma vez, usando o SHIFT ou CRTL junto com o clique do mouse.", // Texto personalizado da zona de drop
    dropZoneClickTitle: "ou clique para selecionar os arquivos", // Texto secundário na zona de drop
    allowedFileExtensions: ['pdf', 'jpeg', 'jpg', 'doc', 'doc', 'docx', 'png', 'gif', 'tiff', 'webp', 'bmp'], // Permite apenas arquivos PDF e JPEG
    msgInvalidFileExtension: "Tipo de arquivo não suportado. Apenas arquivos PDF, DOC, DOCx e imagens são permitidos." // Mensagem personalizada
});


const selecionaFuncionario = (chapa) => {
    let dados = {
        "chapa":chapa,
        
    }
    
    if(!chapa || chapa == ''){
        openLoading(true);
        return false;
       
    }
    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/substituicao/dadosFunc'); ?>",
        type:'POST',
        data:dados,
        success:function(result){
           

            var response = JSON.parse(result);
            salario = response[0].SALARIO;

            $("#filial").val(response[0].CODFILIAL)
            $("#funcao").val(response[0].CODFUNCAO +':'+response[0].NOMEFUNCAO)
            openLoading(true);
            

        },
    });

    
}    
const selecionaFuncionarioSub = (chapa) => {
    let dados = {
        "chapa":chapa,
        
    }
    openLoading();
   
    if(!chapa || chapa == ''){
        openLoading(true);
        return false;
       
    }
    $.ajax({
        url: "<?= base_url('variaveis/substituicao/dadosFunc'); ?>",
        type:'POST',
        data:dados,
        success:function(result){
           

            var response = JSON.parse(result);
            salario_sub = response[0].SALARIO;
            $("#funcao_sub").val(response[0].CODFUNCAO +':'+response[0].NOMEFUNCAO);
            openLoading(true);
            

        },
    });

    
}   
</script>
<?php loadPlugin(['select2','maskmoney','datepicker']); ?>