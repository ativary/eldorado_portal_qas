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
                                <a href="<?= ($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/coparticipacao'); ?> " class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                   

                <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_moradia" role="tab"><i class="fa fa-home"></i>  Coparticipação</a>
                            </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio moradia -->
                        <div class="tab-pane active p-3" id="auxilio_moradia" role="tabpanel">
                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Prestadora:</label>
                                <div class="col-sm-8">
                                    <select disabled  class="select2 custom-select form-control form-control-sm"  name="tipoReq" id="tipoReq">
                                        <option value=""> ... </option>
                                        <option value="1" <?= ($req[0]->tiporeq == '1') ? " selected " : ""; ?>> Bradesco </option>
                                        <option value="2" <?= ($req[0]->tiporeq == '2') ? " selected " : ""; ?>> Unimed </option>
                                       
                                    </select>
                                   
                                </div>
                            </div>
                            

                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"><?= $valores->justificativa; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"> Anexos:</label>
                                <div class="col-sm-8">
                                <input class="form-control " type="file" name="anexo" id="anexo"  required>
                                </div>
                                <div class="col-sm-2">
                                    <button class="btn btn-success" id="btnadd" ><i class="fas fa-plus"></i> Adicionar</button>
                                   
                                </div>
                            </div>
                            <div class="form-group row mb-2" id="dependentesTableContainer" >
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="dependentesTable">
                                        <thead>
                                            <tr>
                                                <th>Chapa</th>
                                                <th>Nome</th>
                                                <th>Valor</th>
                                                <th>Situação</th> <!-- Nova coluna para o valor -->
                                                <th>Função</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php  $dependentes = json_decode($valores->dependentes); 
                                            foreach ($dependentes as $key2 => $dados2) : ?>
                                                <tr>
                                                    <td class="n-mobile-cell"><?= $dados2->chapa ?></td>
                                                    <td class="n-mobile-cell"><?= $dados2->nome ?></td>
                                                    <td class="n-mobile-cell"><input type="text" value="<?= $dados2->valor ?>" data-money class="form-control form-control-sm valorDependente" placeholder="Valor" ></td>
                                                    <td class="n-mobile-cell"><?= $dados2->situacao ?></td>
                                                    <td class="n-mobile-cell"><?= $dados2->funcao ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>


                            
                            

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
<script src="<?= base_url('public/assets/js/xlsx.full.min.js') ?>"></script>

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
        var table = $('#dependentesTable').DataTable({
        "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength": 50,
        "aaSorting": [[1, "desc"]],
        });
  
        desabilitaInputs();
        // Inicializar o plugin maskMoney para os inputs com atributo data-money
        $('[data-money]').maskMoney({
                            prefix: '',
                            allowNegative: false,
                            allowZero: true,
                            thousands: '.',
                            decimal: ',',
                            affixesStay: false
                        });
     
    });
    function desabilitaInputs() {
        // Seleciona todos os elementos de input, textarea e select da página
        const status =  <?= $req[0]->status ?>;
       const RH = <?= $rh ? 'true' : 'false' ?>;

        if(status ==1 || ((status == 3 || status == 7) &&  RH )){
            return false;
        }else
       {
        
            const inputs = document.querySelectorAll(' textarea, button');
            $('#tipoReq').prop('disabled', true);
            $('#justificativa').prop('disabled', true);
            $('#anexo').prop('disabled', true);

            // Desabilitar todos os campos com a classe 'valorDependente'
            $('.valorDependente').prop('disabled', true);

            inputs.forEach(function(input) {
                input.disabled = true;
            });
             // Esconde o botão com id "btnsave"
            const btnSave = document.getElementById('btnsave');
            if (btnSave) {
                btnSave.style.display = 'none';
            }
        }

    }


    
    $('#btnadd').on('click', function () {
    const fileInput = $('#anexo')[0];
    const file = fileInput.files[0];

    if (!file) {
        Swal.fire('Erro', 'Por favor, selecione um arquivo.', 'error');
        return;
    }

    const reader = new FileReader();
    const extension = file.name.split('.').pop().toLowerCase();
 // Reseta a tabela ao carregar um novo arquivo
 const tableBody = $('#dependentesTable tbody');
    $('#dependentesTableContainer').show();
    tableBody.empty();  // Limpa o corpo da tabela

    // Verifica se o DataTables já está inicializado e destrói para evitar duplicação
    if ($.fn.DataTable.isDataTable('#dependentesTable')) {
            $('#dependentesTable').DataTable().clear().destroy();
        }
    reader.onload = function (e) {
        let workbook;

        if (extension === 'xls') {
            workbook = XLSX.read(e.target.result, { type: 'binary' });
        } else if (extension === 'xlsx') {
            const data = new Uint8Array(e.target.result);
            workbook = XLSX.read(data, { type: 'array' });
        } else if (extension === 'csv') {
            const data = e.target.result;
            workbook = XLSX.read(data, { type: 'string' });
        } else {
            Swal.fire('Erro', 'Formato de arquivo não suportado.', 'error');
            return;
        }

        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
        const rows = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
        const chapaValorList = [];

        $.each(rows, function (index, row) {
            if (index === 0) return; // Pular a primeira linha (cabeçalho)

            const chapa = String(row[0]).replace(/\D/g, '').padStart(9, '0'); 
            let valor = row[1];

            // Verificar e tratar o valor
            if (valor === '-' || valor === undefined || valor === null) {
                valor = 0; // Substituir por zero
            }
          
              // Garantir que seja numérico
            valor = parseFloat(valor) || 0;

            if (chapa && valor !== undefined) {
                chapaValorList.push({ chapa: chapa, valor: valor });
            }
        });

        if (chapaValorList.length > 0) {
            Swal.fire({
                title: 'Processando...',
                text: 'Aguarde enquanto os dados são processados.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: "<?= base_url('variaveis/coparticipacao/dadosFunc2'); ?>",
                type: 'POST',
                data: { chapas: chapaValorList },
                success: function (response) {
                    console.log(response);
                    try {
                        const results = JSON.parse(response);
                        const tableBody = $('#dependentesTable tbody');
                        tableBody.empty();

                        $.each(results, function (index, result) {
                            if (result && result.NOME) {
                                let valorFormatado = parseFloat(result.VALOR).toFixed(2).replace('.', ','); // Garantir 2 casas decimais e substituir ponto por vírgula

                                const newRow = $('<tr></tr>').html(`
                                    <td>${result.CHAPA}</td>
                                    <td>${result.NOME}</td>
                                    <td><input type="text" value="${valorFormatado}" data-money class="form-control form-control-sm valorDependente" placeholder="Valor" onchange="verificarLimite(this)"></td>
                                    <td>${result.CODSITUACAO}</td>
                                    <td>${result.CODFUNCAO}:${result.NOMEFUNCAO}</td>
                                `);
                                tableBody.append(newRow);
                            }
                        });

                        $('#dependentesTableContainer').show();

                        // Reinicializar o DataTable se já estiver inicializado
                        if ($.fn.DataTable.isDataTable('#dependentesTable')) {
                            $('#dependentesTable').DataTable().clear().destroy();
                        }

                        // Inicializar o DataTables com as opções personalizadas
                        $('#dependentesTable').DataTable({
                            "aLengthMenu": [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
                            "iDisplayLength": 50,
                            "aaSorting": [[1, "desc"]]
                        });
                           // Inicializar o plugin maskMoney para os inputs com atributo data-money
                           $('[data-money]').maskMoney({
                            prefix: '',
                            allowNegative: false,
                            allowZero: true,
                            thousands: '.',
                            decimal: ',',
                            affixesStay: false
                        });
                        // Aplicar o maskMoney novamente para formatar os valores corretamente
                        $('[data-money]').each(function () {
                            $(this).maskMoney('mask'); // Reaplica a máscara nos valores já presentes
                        });

                        Swal.close();
                    } catch (e) {
                        console.error('Erro ao processar a resposta:', e);
                        Swal.fire('Erro', 'Erro ao processar os dados.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Erro', 'Ocorreu um erro ao processar as chapas.', 'error');
                }
            });
        } else {
            Swal.fire('Erro', 'Nenhum dado encontrado no arquivo.', 'error');
        }
    };

    if (extension === 'xls') {
        reader.readAsBinaryString(file);
    } else if (extension === 'xlsx') {
        reader.readAsArrayBuffer(file);
    } else if (extension === 'csv') {
        reader.readAsText(file);
    }
});
const salvaDados = () => {
    
    let formData = new FormData();
    formData.append("funcionario", '');
    formData.append("Nome", '');
    formData.append("justificativa", $("#justificativa").val());
    formData.append("fora_periodo", '<?= $valores->fora_periodo; ?>');
    formData.append("tipoReq", $("#tipoReq").val());
    formData.append("tipo", '8');
    formData.append("id", '<?= $req[0]->id; ?>');
    
    let dependentes = [];
    let total = 0;
    let hasEmptyValue = false; // Flag para identificar valor vazio

    // Usando a API do DataTables para obter todos os dados, inclusive os não visíveis
    let table = $('#dependentesTable').DataTable();
    let allRows = table.rows().nodes(); // Isso pega todos os nós DOM das linhas

    $(allRows).each(function() {
        let valorDependente = $(this).find("td:eq(2) input").val().trim(); // Obtém o valor do input

        // Verifica se o campo de valor está vazio
        if (!valorDependente || valorDependente == '0' || valorDependente == '0,00') {
            hasEmptyValue = true; // Sinaliza que há campo vazio
            return false; // Interrompe o loop
        }

        let valorDependenteParsed = parseFloat(valorDependente.replace(/\./g, '').replace(',', '.')) || 0;
        total += valorDependenteParsed; // Acumula o valor

        let dependente = {
            "chapa": $(this).find("td:eq(0)").text(),
            "nome": $(this).find("td:eq(1)").text(),
            "valor": valorDependente,
            "situacao": $(this).find("td:eq(3)").text(),
            "funcao": $(this).find("td:eq(4)").text(),
        };

        dependentes.push(dependente); // Adiciona o dependente ao array
    });

    // Serializa o array completo de dependentes e adiciona ao FormData
    formData.append("dependentes", JSON.stringify(dependentes));

    if (hasEmptyValue) {
        exibeAlerta("error", "<b>Todos os campos de valor devem ser preenchidos. Verifique se algum valor não está zerado</b>");
        return false; // Interrompe a execução da função
    }

    // Adiciona múltiplos arquivos ao formData
    let fileInput = $('#anexo')[0].files;
    if (fileInput.length > 0) {
        for (let i = 0; i < fileInput.length; i++) {
            formData.append("anexo[]", fileInput[i]);
        }
    }

    if (total == "" || total == 0) { 
        exibeAlerta("error", "<b>A lista precisa de funcionários com valores válidos. Verifique se algum valor não está zerado</b>.");
        return false; 
    }

    if ($("#justificativa").val().trim() == "") { 
        exibeAlerta("error", "<b>Justificativa</b> obrigatório.");
        return false; 
    }

    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/coparticipacao/update'); ?>",
        type: 'POST',
        data: formData,
        processData: false, // Impede que o jQuery processe os dados
        contentType: false, // Impede que o jQuery defina o tipo de conteúdo
        success: function(result) {
            console.log(result);
            var response = JSON.parse(result);

            if (response.tipo != 'success') {
                exibeAlerta(response.tipo, response.msg, 2);
            } else {
                exibeAlerta(response.tipo, response.msg, 3, '<?= ($req[0]->status == '3' || $req[0]->status == '7') ? base_url('variaveis/sincronizacao') : base_url('variaveis/coparticipacao'); ?>');
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
        url: "<?= base_url('variaveis/coparticipacao/dadosFunc'); ?>",
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
<?php loadPlugin(['select2','maskmoney','datatable']); ?>