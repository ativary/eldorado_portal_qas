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
                                <a href="<?= base_url('variaveis/desconto') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                   

                <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_desconto" role="tab"><i class="fa fa-home"></i> Desconto Autorizado</a>
                            </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio desconto -->
                        <div class="tab-pane active p-3" id="auxilio_desconto" role="tabpanel">

                                     <!-- auxilio substituicao -->
                        <div class="tab-pane active p-3" id="auxilio_substituicao" role="tabpanel">

                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Tipo de Desconto:</label>
                                <div class="col-sm-10">
                                    <select  class="select2 custom-select form-control form-control-sm" name="tipoReq" id="tipoReq">
                                        <option value=""> ... </option>
                                        <option value="1"> Desconto Autorizado </option>
                                        <option value="2">  Desconto de EPIs </option>
                                        <option value="3">  Multa de Trânsito </option>
                                    </select>
                                
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Funcionário:</label>
                                <div class="col-sm-10">
                                    <select onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">...</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($funcionario == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] .'-'. $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            

                            <div class="form-group row mb-2">
                                <label for="valor" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Valor total do Desconto:</label>
                                <div class="col-sm-10">
                                    <input class="form-control form-control-sm " data-money  type="text" value="" name="valor" id="valor" required>
                                   
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
     
    $(document).ready(function() {
        $("[data-money]").maskMoney({prefix:'', allowNegative: false, allowZero:false, thousands:'.', decimal:',', affixesStay: false});
        verificaData();
        if (fora_periodo == 1) { 
            exibeAlerta("error", "Atenção! Fora do período de abertura para novas requisições."); 
        }       
    });



const selecionarFuncionarios = (codigo) => {
        let dados = {
            "codigo":codigo,
            
        }
        openLoading();

        $.ajax({
            url: "<?= base_url('variaveis/desconto/selectFunc'); ?>",
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
                        $('#funcionario_sub').append(
                            $('<option>', {
                                value: func.CHAPA,
                                text: func.CHAPA + " - " + func.NOME
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
        const funcionario =  "<?= $chapaFunc > 0 ? $chapaFunc  : '0' ?>"; // Defina o valor da constante funcionario
      
        const hoje = new Date();
        const diaHoje = hoje.getDate(); // Obtém o dia do mês atual

        const periodoInicio = parseInt(regra.periodo_desconto, 10);
        const periodoFim = parseInt(regra.periodo_desconto_fim, 10);
    
       
        hoje.setHours(0, 0, 0, 0);
        console.log(funcionario);
        if (diaHoje >= periodoInicio && diaHoje <= periodoFim) {
            
            fora_periodo=0 ;
         
        } else {
            if (Array.isArray(regra.gestor) && regra.gestor.length > 0) {
               
                $.each(regra.gestor, function(index, func) {
                    console.log(func);
                  
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
    const regra = <?= $param6 ?>;
    let valorCampo = $("#valor").val().replace(/\./g, '').replace(',', '.');

    valorCampo = parseFloat(valorCampo);
   
    let salarioFormatado = salario.toString().replace(',', '.');
 
    salarioFormatado = parseFloat(salarioFormatado);
    let limite = '0.'+regra.desconto_porcentagem;
  
    limite = parseFloat(limite);
 
    let maximoPermitido = salarioFormatado * limite;

    // Verifica se o valor do campo é maior que 30% do salário
   
    
    let formData = new FormData();
    let resultado = 0;
    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("tipoReq", $("#tipoReq").val());
    formData.append("funcao", $("#funcao").val());
    formData.append("valor", $("#valor").val());
    formData.append("quantMes", '-');
    formData.append("salario", salario);
    formData.append("tipo", '5');
    formData.append("id", '');
  
 
  
    // Adiciona múltiplos arquivos ao formData
    let fileInput = $('#anexo')[0].files;
    if (fileInput.length > 0) {
        for (let i = 0; i < fileInput.length; i++) {
            formData.append("anexo[]", fileInput[i]);
        }
    }


    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
   
    if($("#tipoReq").val() == ""){ exibeAlerta("error", "<b>Tipo obrigatório </b> ."); return false; }
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatório."); return false; }
    if($("#tipoReq").val() == "2" && fora_periodo == 2){ 
        fora_periodo = 1;

     }
     if($("#valor").val().trim() == ""){ exibeAlerta("error", "<b>Valor</b> obrigatório."); return false; }
     formData.append("fora_periodo",fora_periodo);

     if (fora_periodo == 1) { 
        exibeAlerta("error", "Fora do Período de abertura."); 
        return false; 
    }

    

    $.ajax({
        url: "<?= base_url('variaveis/desconto/save'); ?>",
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
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/desconto'); ?>');
                
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
    const regra = <?= $param6 ?>;
    let dados = {
        "chapa":chapa,
        
    }
    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/desconto/dadosFunc'); ?>",
        type:'POST',
        data:dados,
        success:function(result){
           

            var response = JSON.parse(result);

            CODIGOGRUPO =  response[0].CARGO;

            $("#valor").val('');
            $("#filial").val(response[0].CODFILIAL)
            $("#funcao").val(response[0].CODFUNCAO +':'+response[0].NOMEFUNCAO)
            $("#grupoCargo").val(response[0].CARGO)
            salario = response[0].SALARIO;
            openLoading(true);
            console.log(response[0].CODFILIAL);
            if (Array.isArray(regra.desconto) && regra.desconto.length > 0) {
             
                var valor = regra.reembolso_desconto_valor_demais_filiais;

                // Itera sobre o array regra.desconto
                regra.desconto.forEach(function(desconto) {
                    $('#quantMes').empty();
                    
                    if (desconto.nome == response[0].CODFILIAL) {
                        valor = desconto.valor; // Pega o campo valor dentro de regra.desconto
                       
                        return false;
                    }
                });
                $('#quantMes').append('<option value="1">1 Mês</option>'); 
                // Adiciona options ao select a partir do valor de desconto.valor
                if(valor >= 2){
                    for (var i = 2; i <= valor; i++) { 
                        $('#quantMes').append('<option value="' + i + '">' + i + ' Meses</option>');
                    }
                }
            }else{
                var valor = regra.reembolso_desconto_valor_demais_filiais;
                $('#quantMes').append('<option value="1">1 Mês</option>'); 
                if(valor >= 2){
                    for (var i = 2; i <= valor; i++) { // Exemplo de range, ajusta conforme sua necessidade
                        $('#quantMes').append('<option value="' + i + '">' + i + ' Meses</option>');
                    }
                }

            }
            

        },
    });


    
}   
</script>
<?php loadPlugin(['select2','maskmoney','datepicker']); ?>