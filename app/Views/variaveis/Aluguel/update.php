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
                                <a href="<?= ($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/aluguel'); ?> " class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                   

                <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_moradia" role="tab"><i class="fa fa-home"></i> Auxílio Aluguel</a>
                            </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio moradia -->
                        <div class="tab-pane active p-3" id="auxilio_moradia" role="tabpanel">
                          
                              
                                <!-- <div class="form-group row mb-2">
                                    <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Tipo de Requisição:</label>
                                    <div class="col-sm-10">
                                        <select  class="select2 custom-select form-control form-control-sm"  name="tipoReq" id="tipoReq">
                                            <option disabled value=""> ... </option>
                                            <option value="1" <?= ($req[0]->tiporeq == '1') ? " selected " : ""; ?>> Mensal </option>
                                           
                                        
                                        </select>
                                    
                                    </div>
                                </div> -->
                            
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
                                <div class="col-sm-10">
                                    <select disabled onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">- ... -</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($req[0]->chapa == $DadosFunc['CHAPA']) ? " selected " : ""; ?>> <?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Quantidade de Meses:</label>
                                <div class="col-sm-10">
                                    <select  class="select2 custom-select form-control form-control-sm" name="quantMes" id="quantMes">
                                    
                                    </select>
                                   
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Valor:</label>
                                <div class="col-sm-10">
                                    <input onchange="verificaValor(this)"  class="form-control form-control-sm " data-money  type="text" value="<?= $valores->valor; ?>" name="valor" id="valor" required>
                                   
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"><?= $valores->justificativa; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm">Adicionar novos Anexos:</label>
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

                            <input class="form-control datepicker m_data" hidden type="text" value="<?= $valores->filial; ?>" name="filial" id="filial" required>
                            <input class="form-control datepicker m_data" hidden type="text" value="<?= $valores->funcao; ?>" name="funcao" id="funcao" required>
							<input class="form-control datepicker m_data" hidden type="text" value="<?= $valores->grupoCargo; ?>"name="grupoCargo" id="grupoCargo" required>
							

                            
                            

                        </div>
                    </div>
                    

                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave"  onclick="return salvaDados()"><i class="fas fa-check"></i> Atualizar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-fileinput/css/fileinput.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap-fileinput/js/fileinput.min.js"></script>

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
<script>
     var fora_periodo=0 ;
    $(document).ready(function(){
        $("[data-money]").maskMoney({prefix:'', allowNegative: false, allowZero:false, thousands:'.', decimal:',', affixesStay: false});
        verificaData();
        quantMeses();
        desabilitaInputs();
    });
    
    function desabilitaInputs() {
        // Seleciona todos os elementos de input, textarea e select da página
        const status =  <?= $req[0]->status ?>;
       const RH = <?= $rh ? 'true' : 'false' ?>;

        if(status ==1 || ((status == 3 || status == 7) &&  RH )){
            return false;
        }else
       {

        
            const inputs = document.querySelectorAll('input, textarea, select, button');
            
            // Itera sobre cada elemento e define o atributo disabled para true


            inputs.forEach(function(input) {
                input.disabled = true;
            });
            const btnSave = document.getElementById('btnsave');
            if (btnSave) {
                btnSave.style.display = 'none';
            }
        }

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

function verificaValor(input) {
    var valor = parseFloat(input.value.replace(/[^0-9,-]+/g,"").replace(",","."));
    const regra = <?= $param6 ?>;
    const cargo = $("#filial").val();
    console.log(cargo);
    var limiteminimo = null;
    var limitePermitido = null;
    $.each(regra.dependentes, function(index, func) {
        if (func.codigo == cargo) {
            valorLimite = func.valor_max;
            valorminimo = func.valor_min;
            limitePermitido = parseFloat(func.valor_max.replace(/[^0-9,-]+/g,"").replace(",","."));
            limiteminimo = parseFloat(func.valor_min.replace(/[^0-9,-]+/g,"").replace(",","."));
            return false; // Encerra o loop assim que encontrar a correspondência
        }else{

        }

    });

    if (limitePermitido !== null && valor > limitePermitido) {
        Swal.fire({
            icon: 'error', // Ícone de erro para indicar que algo está errado
            title: 'Valor Excedido!',
            text: 'O valor inserido é maior que o limite permitido.  R$'+valorLimite,
            confirmButtonText: 'Ok',
            showCloseButton: true,
            allowOutsideClick: false,
            width: 600,
        });

        input.value = ""; // Opcional: Limpa o valor inserido
    }

    
    if (limitePermitido !== null && valorminimo < limiteminimo) {
        Swal.fire({
            icon: 'error', // Ícone de erro para indicar que algo está errado
            title: 'Valor Excedido!',
            text: 'O valor inserido é menor que o limite permitido.  R$'+valorminimo,
            confirmButtonText: 'Ok',
            showCloseButton: true,
            allowOutsideClick: false,
            width: 600,
        });

        input.value = ""; // Opcional: Limpa o valor inserido
    }
    
}
function quantMeses() {
    const regra = <?= $param6 ?>; 
    const quantMeses = <?= $valores->quantMes ?>; 
    const filial = $("#filial").val();
   
    console.log(regra);
    console.log(quantMeses);
    console.log(filial);
    regra.dependentes.forEach(function(aluguel) {
        $('#quantMes').empty(); // Limpa as opções anteriores
        // Verifica se o nome no array é igual ao CODFILIAL da pessoa
        if (aluguel.nome == filial) {
            var valor = aluguel.valor;                  
            $('#quantMes').append('<option value="1">1 Mês</option>'); 

            if (valor >= 2) {
               
                for (var i = 2; i <= valor; i++) { // Exemplo de range, ajusta conforme sua necessidade
                    $('#quantMes').append('<option value="' + i + '">' + i + ' Meses</option>');
                }
                // Define a opção selecionada com base no valor da constante quantMeses
                $('#quantMes').val(quantMeses);
            }
        }else{
            var valor = regra.reembolso_aluguel_valor_demais_filiais;
            $('#quantMes').append('<option value="1">1 Mês</option>'); 
            if(valor >= 2){
                for (var i = 2; i <= valor; i++) { // Exemplo de range, ajusta conforme sua necessidade
                    $('#quantMes').append('<option value="' + i + '">' + i + ' Meses</option>');
                }
                $('#quantMes').val(quantMeses);
            }
           
        }
    });
}

    
function verificaData() {
    const regra = <?= $param6 ?>; 
    const funcionario =  "<?= $chapaFunc > 0 ? $chapaFunc  : '0' ?>"; 
    
    const hoje = new Date();
    const diaHoje = hoje.getDate();

    const periodoInicio = parseInt(regra.periodo_aluguel, 10);
    const periodoFim = parseInt(regra.periodo_aluguel_fim, 10);
 
    const $selectTipoReq = $('#tipoReq');
    const $opcaoMensal = $selectTipoReq.find('option[value="1"]');
    hoje.setHours(0, 0, 0, 0);
    if (diaHoje >= periodoInicio && diaHoje <= periodoFim) {
         fora_periodo=0 ;
        $opcaoMensal.prop('disabled', false); // Habilita a opção "Mensal"
    } else {
        if (Array.isArray(regra.gestor) && regra.gestor.length > 0) {
            $.each(regra.gestor, function(index, func) {
                if (func.chapa == funcionario) {
                
                    // Converter dt_ini e dt_fim para objetos Date
                    const dtIni = new Date(`${func.dt_ini}T00:00:00`);
                    const dtFim = new Date(`${func.dt_fim}T00:00:00`);
                    if (hoje >= dtIni && hoje <= dtFim) {
                       
                        fora_periodo = 2;
                        $opcaoMensal.prop('disabled', false); // Habilita a opção "Mensal"
                    } else {
                        fora_periodo = 1;
                        $opcaoMensal.prop('disabled', true); // Desabilita a opção "Mensal"
                    }
                    return false;
                }else{
                    fora_periodo= 1;
                    $opcaoMensal.prop('disabled', true); // Desabilita a opção "Mensal"
                }
            });
        }else{
                fora_periodo='1';
                $opcaoMensal.prop('disabled', true); // Desabilita a opção "Mensal"
            }
        
    }
}
const salvaDados = () => {
    
    let formData = new FormData();

    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("tipoReq", '1');
    formData.append("quantMes", $("#quantMes").val());
    formData.append("valor", $("#valor").val());
    formData.append("funcao", $("#funcao").val());
    formData.append("grupoCargo", $("#grupoCargo").val());
    formData.append("tipo", '7');
    formData.append("id", ' <?= $req[0]->id; ?>');
    if($("#tipoReq").val() == "2" && fora_periodo == 2){ 
        fora_periodo = 1;
    }
    formData.append("fora_periodo",fora_periodo);
    
    let extensoesPermitidas = ['pdf', 'jpeg', 'jpg', 'doc', 'doc', 'docx', 'png', 'gif', 'tiff', 'webp', 'bmp'];
    // Adiciona múltiplos arquivos ao formData
    let fileInput = $('#anexo')[0].files;
    let extensaoInvalida = false;
    if (fileInput.length > 0) {
        for (let i = 0; i < fileInput.length; i++) {
            let file = fileInput[i];
            let extension = file.name.split('.').pop().toLowerCase();
            console.log('extensao ',extension);
            if (extensoesPermitidas.includes(extension)) {
                formData.append("anexo[]", file);
            } else {
                extensaoInvalida = true;
            }
        }
    }

    if(extensaoInvalida){ exibeAlerta("error", "Apenas arquivos PDF, DOC, DOCx e imagens são permitidos."); return false; }
    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
    if($("#tipoReq").val() == ""){ exibeAlerta("error", "<b>Tipo obrigatório </b> ."); return false; }
    if($("#valor").val() == ""){ exibeAlerta("error", "<b>Valor obrigatório </b> ."); return false; }
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatório."); return false; }
    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/aluguel/update'); ?>",
        type:'POST',
        data:formData,
        processData: false, // impede que o jQuery processe os dados
        contentType: false, // impede que o jQuery defina o tipo de conteúdo
        success:function(result){
            console.log(result);

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg, 3, '<?=($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/aluguel');?> ');
            }

        },
    });
    
}

const selecionaFuncionario = (chapa) => {
    let dados = {
        "chapa":chapa,
        
    }
    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/aluguel/dadosFunc'); ?>",
        type:'POST',
        data:dados,
        success:function(result){
           

            var response = JSON.parse(result);
           

            $("#filial").val(response[0].CODFILIAL)
            $("#funcao").val(response[0].CODFUNCAO +':'+response[0].NOMEFUNCAO)
            openLoading(true);
            

        },
    });
    
}    
</script>
<?php loadPlugin(['select2','maskmoney']); ?>