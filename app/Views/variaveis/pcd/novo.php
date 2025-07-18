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
                                <a href="<?= base_url('variaveis/pcd') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                   

                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#reembolso_cpd" role="tab"><i class="fa fa-wheelchair"></i> Auxilio Excepcional</a>
                        </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio moradia -->
                        <div class="tab-pane active p-3" id="reembolso_cpd" role="tabpanel">
                        
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Funcionário:</label>
                                <div class="col-sm-10">
                                    <select onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value=""> ... </option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" ><?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Tipo de Requisição:</label>
                                <div class="col-sm-10">
                                    <select  class="select2 custom-select form-control form-control-sm" name="tipoReq" id="tipoReq">
                                        <option value=""> ... </option>
                                        <option value="1"> Mensal </option>
                                        <option value="2"> Complementar </option>
                                       
                                    </select>
                                   
                                </div>
                            </div>

                           
                          

                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Anexar:</label>
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
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Dependente:</label>
                                <div class="col-sm-8">
                                    <select  class="select2 custom-select form-control form-control-sm" name="dependente" id="dependente">
                                        <option value=""> ... </option>
                                       
                                    </select>
                                   
                                </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnadd" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                               
                            </div>
                            <!-- Tabela de Dependentes -->
                            <div class="form-group row mb-2" id="dependentesTableContainer" style="display:none;">
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="dependentesTable">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Data de Nascimento</th>
                                                <th>Parentesco</th>
                                                <th>Valor</th> <!-- Nova coluna para o valor -->
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- As linhas dos dependentes serão adicionadas aqui -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                         
                            <input class="form-control datepicker m_data" hidden type="text" value="" name="filial" id="filial" required>
                            <input class="form-control datepicker m_data" hidden type="text" value="" name="funcao" id="funcao" required>
							

                            
                            

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
     let fora_periodo = '0';
    $(document).ready(function(){
            $("[data-money]").maskMoney({prefix:'', allowNegative: false, allowZero:false, thousands:'.', decimal:',', affixesStay: false});
           
            verificaData();
            if (fora_periodo == 1) { 
                exibeAlerta("error", "Atenção! Fora do período de abertura para novas requisições."); 
            }
    });
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

    
    function verificaData() {
        const regra = <?= $param4 ?>; // Certifique-se de que $param6 seja um JSON válido.
        const funcionario =  "<?= $chapaFunc > 0 ? $chapaFunc  : '0' ?>"; // Defina o valor da constante funcionario
        console.log(funcionario);
        const hoje = new Date();
        const diaHoje = hoje.getDate(); // Obtém o dia do mês atual

        const periodoInicio = parseInt(regra.periodo_pcd, 10);
        const periodoFim = parseInt(regra.periodo_pcd_fim, 10);
    
        const $selectTipoReq = $('#tipoReq');
        const $opcaoMensal = $selectTipoReq.find('option[value="1"]');
        hoje.setHours(0, 0, 0, 0);

        // opção "Complementar"
        const dia_lim_compl = parseInt(regra.dia_limite_compl4, 10);
        const $opcaoCompl = $selectTipoReq.find('option[value="2"]');
        if (diaHoje <= dia_lim_compl) {
            $opcaoCompl.prop('disabled', false); // Habilita a opção "Complementar"
        } else {
            $opcaoCompl.prop('disabled', true); // Desabilita a opção "Complementar"
        }

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
                            console.log('entrou2');
                            fora_periodo = '2';
                            $opcaoMensal.prop('disabled', false); // Habilita a opção "Mensal"
                        } else {
                            fora_periodo = '1';
                            $opcaoMensal.prop('disabled', true); // Desabilita a opção "Mensal"
                        }
                        return false;
                    }else{
                        fora_periodo='1';
                        $opcaoMensal.prop('disabled', true); // Desabilita a opção "Mensal"
                    }
                });
            }else{
                fora_periodo='1';
                $opcaoMensal.prop('disabled', true); // Desabilita a opção "Mensal"
            }
            
            
        }
    }

    function salvaDados()  {
    let formData = new FormData();
    
    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("funcao", $("#funcao").val());
    formData.append("tipoReq", $("#tipoReq").val());
    formData.append("tipo", '4');
    formData.append("id", '');
   
    let total = 0;
    let hasEmptyValue = false; // Flag para identificar valor vazio

    let dependentes = [];
    
    $("#dependentesTable tbody tr").each(function() {
        let valorDependente = $(this).find("td:eq(3) input").val().trim(); // Obtém o valor do input
        
         // Verifica se o campo de valor está vazio
         if (!valorDependente || valorDependente == '0') {
            hasEmptyValue = true; // Sinaliza que há campo vazio
            return false; // Interrompe o loop
        }

        let valorDependenteParsed = parseFloat(valorDependente.replace(/\./g, '').replace(',', '.')) || 0;
        total += valorDependenteParsed; // Acumula o valor


      
        let dependente = {
            "nome": $(this).find("td:eq(0)").text(),
            "data_nascimento": $(this).find("td:eq(1)").text(),
            "parentesco": $(this).find("td:eq(2)").text(),
            "valor": $(this).find("td:eq(3) input").val() // Captura o valor do input
        };

        dependentes.push(dependente); // Adiciona o dependente ao array
    });

    // Serializa o array completo de dependentes e adiciona ao FormData
    formData.append("dependentes", JSON.stringify(dependentes));
    formData.append("valor_total", total.toFixed(2)); // Atribui o valor total ao campo valor_total formatado
    if($("#tipoReq").val() == "2" && fora_periodo == 2){ 
        fora_periodo = 1;

     }
     formData.append("fora_periodo",fora_periodo);
    // Adiciona os arquivos ao FormData
    let files = $('#anexo')[0].files;
    for (let i = 0; i < files.length; i++) {
        formData.append("anexo[]", files[i]);
    }
    if (hasEmptyValue) {
        exibeAlerta("error", "<b>Todos os campos de valor de dependentes devem ser preenchidos.</b>");
        return false; // Interrompe a execução da função
    }


    if ($("#funcionario").val() == "") { 
        exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); 
        return false; 
    }
    if (total == "" || total == 0) { 
        exibeAlerta("error", "<b>Todos os campos de valor de dependentes devem ser preenchidos </b> ."); 
        return false; 
    }
    if($("#justificativa").val().trim() == ""){
        exibeAlerta("error", "<b>Justificativa</b> obrigatório."); 
        return false; 
    }
    if ($("#tipoReq").val() == "") { 
        exibeAlerta("error", "<b>Tipo obrigatório </b> ."); 
        return false; 
    }

    if (fora_periodo == 1) { 
        exibeAlerta("error", "Fora do Período de abertura."); 
        return false; 
    }

    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/pcd/save'); ?>",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(result) {
            var response = JSON.parse(result);

            if (response.tipo != 'success') {
                exibeAlerta(response.tipo, response.msg, 2);
                openLoading(true);
            } else {
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/pcd'); ?>');
            }
        },
    });
}


document.getElementById('btnadd').addEventListener('click', function(e) {
    e.preventDefault();
    
    var dependenteSelect = document.getElementById('dependente');
    var dependenteValue = dependenteSelect.value;
   
    var dependenteText = dependenteSelect.options[dependenteSelect.selectedIndex].text;
   
    // Verifica se um dependente foi selecionado
    if (dependenteValue !== "") {
        dependenteValue = JSON.parse(dependenteValue);
        // Exibe a tabela se estiver oculta
        var tableContainer = document.getElementById('dependentesTableContainer');

 
        // Verifica se o dependente já foi adicionado
        var tableBody = document.getElementById('dependentesTable').getElementsByTagName('tbody')[0];
        var alreadyAdded = false;
        
         
        for (var i = 0; i < tableBody.rows.length; i++) {
            var row = tableBody.rows[i];
            var cellText = row.cells[0].innerText;
            
            if (cellText === dependenteText) {
                alreadyAdded = true;
                break;
            }
        }

        if (alreadyAdded) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Dependente já existe',
                });
                return false;
        }

        tableContainer.style.display = "block";
        
        // Adiciona uma nova linha na tabela
       
        var newRow = tableBody.insertRow();

        // Adicione os dados nas células
        var cell1 = newRow.insertCell(0);
        var cell2 = newRow.insertCell(1);
        var cell3 = newRow.insertCell(2);
        var cell4 = newRow.insertCell(3);
        var cell5 = newRow.insertCell(4);
        if(dependenteValue.DTNASCIMENTO){
            
            const dataNascimento = dependenteValue.DTNASCIMENTO;

            // Converter a string em um objeto Date
            const date = new Date(dataNascimento);

            // Formatar a data para o padrão d/m/Y
            var formattedDate = date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });

           
        }else{
            
            var formattedDate = dependenteValue.DTNASCIMENTO;

        }

        switch(dependenteValue.GRAUPARENTESCO) {
            case '1':
                descricao = "Filho(a) sem deficiência";
                break;
            case '3':
                descricao = "Filho(a) com deficiência";
                break;
            case '5':
                descricao = "Cônjuge";
                break;
            case '6':
                descricao = "Pai";
                break;
            case '7':
                descricao = "Mãe";
                break;
            case '8':
                descricao = "Sogro(a)";
                break;
            case '9':
                descricao = "Outros";
                break;
            case 'A':
                descricao = "Avô(ó)";
                break;
            case 'B':
                descricao = "Incapaz";
                break;
            case 'C':
                descricao = "Companheiro(a)";
                break;
            case 'D':
                descricao = "Enteado(a)";
                break;
            case 'E':
                descricao = "Excluído";
                break;
            case 'G':
                descricao = "Ex-cônjuge";
                break;
            case 'I':
                descricao = "Irmã(o) Válido";
                break;
            case 'M':
                descricao = "Menor pobre";
                break;
            case 'N':
                descricao = "Irmã(o) Inválido";
                break;
            case 'P':
                descricao = "Ex-companheiro(a)";
                break;
            case 'S':
                descricao = "Ex-sogro(a)";
                break;
            case 'T':
                descricao = "Neto(a)";
                break;
            case 'X':
                descricao = "Ex-enteado(a)";
                break;
            default:
                descricao = "Código não reconhecido";
                break;
        }

        // Dados de exemplo (você pode substituir pelos dados reais)
        cell1.innerHTML = dependenteText;  // Nome do dependente
        cell2.innerHTML = formattedDate;    // Data de nascimento
        cell3.innerHTML = descricao; ;      // Parentesco
        cell4.innerHTML = '<input type="text" data-money class="form-control form-control-sm valorDependente" placeholder="Valor" onchange="verificarLimite(this)">';
        cell5.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remover</button>';
        $(cell4).find('input[data-money]').maskMoney({
                prefix: '',
                allowNegative: false,
                allowZero: true,
                thousands: '.',
                decimal: ',',
                affixesStay: false
        });
    }
});

function verificarLimite(input) {
    var valor = parseFloat(input.value.replace(/[^0-9,-]+/g,"").replace(",","."));
    var filial = document.getElementById('filial').value;
    const regra = <?= $param4 ?>; // Certifique-se de que $param4 esteja em um formato adequado para ser interpretado como JSON.

    var limitePermitido = null;
    var valorLimite = null;
    
    valorLimite = regra.reembolso_cpd_valor_demais_filiais;
    limitePermitido = parseFloat(regra.reembolso_cpd_valor_demais_filiais);
  
    $.each(regra.dependentes, function(index, func) {
        if (func.nome == filial) {
            valorLimite = func.valor;
            limitePermitido = parseFloat(func.valor.replace(/[^0-9,-]+/g,"").replace(",","."));
            return false; // Encerra o loop assim que encontrar a correspondência
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
}

// Função para remover a linha da tabela
function removeRow(btn) {
    var row = btn.parentNode.parentNode;
    row.parentNode.removeChild(row);
    
    // Verifica se a tabela está vazia e oculta se necessário
    var tableBody = document.getElementById('dependentesTable').getElementsByTagName('tbody')[0];
    if (tableBody.rows.length === 0) {
        document.getElementById('dependentesTableContainer').style.display = "none";
    }
}


const selecionaFuncionario = (chapa) => {
    let dados = {
        "chapa":chapa,
        
    }
    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/pcd/dadosFunc'); ?>",
        type:'POST',
        data:dados,
        success:function(result){
          

            var response = JSON.parse(result);
            
            $('#dependentesTable tbody').empty();
            $("#filial").val(response['result1'][0].CODFILIAL)
            $("#funcao").val(response['result1'][0].CODFUNCAO +':'+response['result1'][0].NOMEFUNCAO)
            openLoading(true);
            $('#dependente').empty();

            $.each(response['result2'], function(index, func) {
                $('#dependente').append(
                    $('<option>', {
                        value: JSON.stringify(func),
                        text: func.NRODEPEND + " - " + func.NOMEDEPEND
                    })
                );
            });
            

        },
    });
    
}    
</script>
<?php loadPlugin(['select2','maskmoney']); ?>