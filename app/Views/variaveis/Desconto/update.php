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
                                <a href="<?= ($req[0]->status == '3' || $req[0]->status == '4' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/desconto'); ?> " class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                   

                <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_desconto" role="tab"><i class="fa fa-home"></i> Auxílio desconto</a>
                            </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio desconto -->
                        <div class="tab-pane active p-3" id="auxilio_desconto" role="tabpanel">
                          
                              
                                <div class="form-group row mb-2">
                                    <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Tipo de Requisição:</label>
                                    <div class="col-sm-10">
                                        <select  class="select2 custom-select form-control form-control-sm" disabled  name="tipoReq" id="tipoReq">
                                            <option disabled value=""> ... </option>
                                            <option value="1" <?= ($req[0]->tiporeq == '1') ? " selected " : ""; ?>> Desconto Autorizado </option>
                                            <option value="2" <?= ($req[0]->tiporeq == '2') ? " selected " : ""; ?>> Desconto de EPIs </option>
                                            <option value="3" <?= ($req[0]->tiporeq == '3') ? " selected " : ""; ?>> Multa de Trânsito </option>
                                        </select>
                                    
                                    </div>
                                </div>
                            
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Funcionário:</label>
                                <div class="col-sm-10">
                                    <select disabled  class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">- ... -</option>
                                            <option selected value="<?= $valores->funcionario; ?>" > <?=  $valores->Nome ?></option>
                                          
                                    </select>
                                </div>
                            </div>
 
                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Valor Total do Desconto:</label>
                                <div class="col-sm-10">
                                    <input class="form-control form-control-sm " data-money  type="text" value="<?= $valores->valor; ?>" name="valor" id="valor" required>
                                   
                                </div>
                            </div>
                            <?php if ( $rh && ($req[0]->status == '8' ||$req[0]->status == '3' || $req[0]->status == '7' || $req[0]->status == '4')) : ?>
                                <div class="form-group row mb-2">
                                    <label for="valor_desconto" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Valor a Descontar:</label>
                                    <div class="col-sm-10">
                                        <input class="form-control form-control-sm " data-money  type="text" value="<?= $valores->valor_desconto; ?>"  name="valor_desconto" id="valor_desconto" required>
                                    
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Quantidade de Meses:</label>
                                    <div class="col-sm-10">
                                        <select  class="select2 custom-select form-control form-control-sm" name="quantMes" onchange="atualizarTabelaParcelas()"  id="quantMes">
                                            
                                        </select>
                                    
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="salario" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger"></span>salário:</label>
                                    <div class="col-sm-2">
                                        <input  disabled class="form-control form-control-sm " data-money  type="text" value="<?= $valores->salario_30; ?>" name="salario" id="salario" required>
                                    
                                    </div>
                                    <label for="salario_30" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger"></span>30% do salário:</label>
                                    <div class="col-sm-2">
                                        <input  disabled class="form-control form-control-sm " data-money  type="text" value="<?= $valores->salario_30; ?>" name="salario_30" id="salario_30" required>
                                    
                                    </div>
                                    <label for="salario_10" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger"></span>10% do salário:</label>
                                    <div class="col-sm-2">
                                        <input disabled class="form-control form-control-sm " data-money  type="text" value="<?= $valores->salario_10; ?>" name="salario_10" id="salario_10" required>
                                    
                                    </div>
                                </div>


                                <!-- Tabela de parcelas -->
                                <div class="form-group row mb-2">
                                    <div class="col-sm-10 offset-sm-2">
                                        <table class="table table-bordered" id="tabelaParcelas">
                                            <thead>
                                                <tr>
                                                    <th>Parcela</th>
                                                    <th>Valor</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php if (isset($valores->parcelas)) : ?>
                                            <?php  $parcelas = json_decode($valores->parcelas); 
                                                 foreach ($parcelas as $key2 => $dados2) : ?>
                                                <tr>
                                                 <td><?= $dados2->parcela; ?></td>
                                                 <td><?= $dados2->valor; ?></td>
                                                 <tr>
                                                <?php endforeach; ?>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                                
                            <?php endif; ?>

                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"><?= $valores->justificativa; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm">Adicionar novos Anexos:</label>
                                <div class="col-sm-10">
                                <input class="form-control filepond" type="file" name="anexo[]" id="anexo" multiple required accept="application/pdf, image/jpeg">
                                </div>
                            </div>

                            <input class="form-control datepicker m_data" hidden type="text" value="<?= $valores->filial; ?>" name="filial" id="filial" required>
                            <input class="form-control datepicker m_data" hidden type="text" value="<?= $valores->funcao; ?>" name="funcao" id="funcao" required>
							

                            
                            

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
     var salario = <?= $valores->salario ?>;
     const limite10 = (salario * 0.1).toFixed(2);
     const limite30 = (salario * 0.3).toFixed(2);
    $(document).ready(function(){
      
        quantMeses();
     
        $("#salario").val(salario).trigger('maskMoney.mask');
        $("#salario_10").val(limite10).trigger('maskMoney.mask');
        $("#salario_30").val(limite30).trigger('maskMoney.mask');
        // Inicializando a máscara nos campos, para garantir a formatação desejada
        desabilitaInputs();
        $("[data-money]").maskMoney({
            prefix: '',
            allowNegative: false,
            allowZero: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });
        
    });
    function calcularMeses() {
        console.log('entrou');
        const valorDesconto = parseFloat(document.getElementById('valor_desconto').value.replace(/\./g, '').replace(',', '.'));
        console.log(valorDesconto);
        if (!isNaN(valorDesconto) && valorDesconto > 0) {
            const mesesNecessarios = Math.ceil(valorDesconto / limite10);
            console.log(mesesNecessarios);
            $('#quantMes').val(mesesNecessarios).trigger('change'); // Atualiza o select com jQuery e dispara o evento change
          
        }
    }
      // Adicione o evento onchange ao campo de valor_desconto
      $('#valor_desconto').on('change', function() {
        calcularMeses();
        atualizarTabelaParcelas();
    });

function quantMeses() {
    const regra = <?= $param6 ?>; 
    const quantMeses = isNaN('<?= $valores->quantMes ?>') ? 0 : '<?= $valores->quantMes ?>';
  
    const filial = $("#filial").val();
   
    if (Array.isArray(regra.desconto) && regra.desconto.length > 0) {
        regra.desconto.forEach(function(aluguel) {
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
                    $('#quantMes').val(quantMeses).trigger('change');
                }
            }else{
                var valor = regra.reembolso_desconto_valor_demais_filiais;
                $('#quantMes').append('<option value="1">1 Mês</option>'); 
                if(valor >= 2){
                    for (var i = 2; i <= valor; i++) { // Exemplo de range, ajusta conforme sua necessidade
                        $('#quantMes').append('<option value="' + i + '">' + i + ' Meses</option>');
                    }
                    $('#quantMes').val(quantMeses).trigger('change');
                }
            }
        });
    }else{
        var valor = regra.reembolso_desconto_valor_demais_filiais;
                $('#quantMes').append('<option value="1">1 Mês</option>'); 
                if(valor >= 2){
                    for (var i = 2; i <= valor; i++) { // Exemplo de range, ajusta conforme sua necessidade
                        $('#quantMes').append('<option value="' + i + '">' + i + ' Meses</option>');
                    }
                    $('#quantMes').val(quantMeses).trigger('change');
                }

    }
}
function atualizarTabelaParcelas() {
        const valorDesconto = parseFloat($('#valor_desconto').val().replace(/\./g, '').replace(',', '.'));
        const quantidadeMeses = parseInt($('#quantMes').val());

        if (isNaN(valorDesconto) || valorDesconto <= 0 || isNaN(quantidadeMeses) || quantidadeMeses <= 0) {
            $('#tabelaParcelas tbody').empty(); // Limpa a tabela se os valores forem inválidos
            return;
        }

        const valorParcela = Math.floor((valorDesconto / quantidadeMeses) * 100) / 100; // Valor arredondado para 2 casas decimais
        const valorUltimaParcela = valorDesconto - (valorParcela * (quantidadeMeses - 1)); // Calcula o valor da última parcela
        const $tbody = $('#tabelaParcelas tbody');
        
        $tbody.empty(); // Limpa as linhas anteriores

        for (let i = 1; i <= quantidadeMeses; i++) {
            const valorAtual = (i === quantidadeMeses) ? valorUltimaParcela : valorParcela; // Última parcela recebe o valor restante
            $tbody.append(`
                <tr>
                    <td>${i}ª Parcela</td>
                    <td>R$ ${valorAtual.toFixed(2).replace('.', ',')}</td>
                </tr>
            `);
        }
    }

    function desabilitaInputs() {
        // Seleciona todos os elementos de input, textarea e select da página
        const status =  <?= $req[0]->status ?>;
       const RH = <?= $rh ? 'true' : 'false' ?>;

        if(status ==1 || ((status == 8 || status == 3 || status == 7) &&  RH )){
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
    dropZoneTitle: "Arraste os arquivos aqui", // Texto personalizado da zona de drop
    dropZoneClickTitle: "ou clique para selecionar os arquivos", // Texto secundário na zona de drop
    allowedFileExtensions: ['pdf', 'jpeg', 'jpg'], // Permite apenas arquivos PDF e JPEG
    msgInvalidFileExtension: 'Tipo de arquivo não suportado. Apenas arquivos PDF e JPEG são permitidos.' // Mensagem personalizada
});


    
function verificaData() {
    const regra = <?= $param6 ?>; 
    const funcionario =  "<?= $chapaFunc > 0 ? $chapaFunc  : '0' ?>"; 
    console.log(funcionario);
    const hoje = new Date();
    const diaHoje = hoje.getDate();

    const periodoInicio = parseInt(regra.periodo_desconto, 10);
    const periodoFim = parseInt(regra.periodo_desconto_fim, 10);
 
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
                        console.log('entrou2');
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
    const RH = <?= $rh ? 'true' : 'false' ?>;
    const status =  <?= $req[0]->status ?>;
    let formData = new FormData();
    let resultado = 0;
    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("funcionario_sub", $("#funcionario_sub").val());
    formData.append("Nome_sub", $("#funcionario_sub option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("valor", $("#valor").val());
    formData.append("valor_desconto", $("#valor_desconto").val());
    formData.append("quantMes", $("#quantMes").val());
    formData.append("tipoReq",  $("#tipoReq").val());
    formData.append("funcao", $("#funcao").val());
    formData.append("data_fim", $("#data_fim").val());
    formData.append("data_inicio", $("#data_inicio").val());
    formData.append("tipo", '5');
    formData.append("id", ' <?= $req[0]->id; ?>');
    formData.append("salario", salario);
    formData.append("fora_periodo",fora_periodo);

    // Obtenha e converta os valores diretamente
    let valor = parseFloat($("#valor").val().replace(/\./g, '').replace(',', '.'));
    if(RH && ( status== '8' || status == '7' || status == '3' )){
        let valorDesconto = parseFloat($("#valor_desconto").val().replace(/\./g, '').replace(',', '.'));
        // Verifique se o valorDesconto é maior que o valor
        if (valorDesconto > valor) {

            $("#valor_desconto").val('');
            exibeAlerta("error", "<b>Valor a Descontar não pode ser maior do que Valor Total do Desconto!  </b> "); return false;
        }
        if($("#valor_desconto").val().trim() == ""){ exibeAlerta("error", "<b>Valor do desconto</b> obrigatório."); return false; }
        if($("#quantMes").val().trim() == ""){ exibeAlerta("error", "<b>Quantidade de Meses</b> obrigatório."); return false; }

    }
   
    formData.append("resultado", $("#resultado").val());
    // Adiciona múltiplos arquivos ao formData
    let fileInput = $('#anexo')[0].files;
    if (fileInput.length > 0) {
        for (let i = 0; i < fileInput.length; i++) {
            formData.append("anexo[]", fileInput[i]);
        }
    }

    let parcelas = [];
    $("#tabelaParcelas tbody tr").each(function() {
        const parcela = $(this).find("td:nth-child(1)").text(); // Texto da coluna "Parcela"
        const valor = $(this).find("td:nth-child(2)").text();   // Texto da coluna "Valor"
        
        // Adiciona cada parcela como um objeto no array de parcelas
        parcelas.push({ parcela, valor });
    });

    // Adiciona o array de parcelas ao FormData como JSON stringificado
    formData.append("parcelas", JSON.stringify(parcelas));

    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
    if($("#tipoReq").val() == ""){ exibeAlerta("error", "<b>Tipo obrigatório </b> ."); return false; }
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatório."); return false; }
    if($("#valor").val().trim() == ""){ exibeAlerta("error", "<b>Valor</b> obrigatório."); return false; }



    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/desconto/update'); ?>",
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
                exibeAlerta(response.tipo, response.msg, 3, '<?=($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/desconto');?> ');
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
        url: "<?= base_url('variaveis/desconto/dadosFunc'); ?>",
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
                                text: '-Todos-'
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
</script>
<?php loadPlugin(['select2','maskmoney']); ?>