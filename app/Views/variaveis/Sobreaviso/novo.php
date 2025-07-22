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
                                <a href="<?= base_url('variaveis/sobreaviso') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                   

                <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_sobreaviso" role="tab"><i class="fa fa-home"></i> Auxílio sobreaviso</a>
                            </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio sobreaviso -->
                        <div class="tab-pane active p-3" id="auxilio_sobreaviso" role="tabpanel">

                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Tipo de Requisição:</label>
                                <div class="col-sm-4">
                                    <select  class="select2 custom-select form-control form-control-sm" name="tipoReq" id="tipoReq">
                                        <option value=""> ... </option>
                                        <option value="1"> Mensal </option>
                                        <option value="2"> Complementar </option>
                                       
                                    </select>
                                   
                                </div>

                                <label for="tipoPer" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Período de Ponto:</label>
                                <div class="col-sm-4">
                                    <select  class="select2 custom-select form-control form-control-sm" name="tipoPer" id="tipoPer">
                                        <option value=""> ... </option>
                                        <option value="1"> Atual &nbsp;&nbsp;&nbsp; (<?= DateTime::createFromFormat('Y-m-d', $per_ini_atual)->format('d/m/Y'); ?> a <?= DateTime::createFromFormat('Y-m-d', $per_fim_atual)->format('d/m/Y'); ?>)</option>
                                        <option value="2"> Futuro &nbsp; (<?= DateTime::createFromFormat('Y-m-d', $per_ini_futuro)->format('d/m/Y'); ?> a <?= DateTime::createFromFormat('Y-m-d', $per_fim_futuro)->format('d/m/Y'); ?>)</option>
                                       
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
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm">Anexar:</label>
                                <div class="col-sm-10">
                                <input class="form-control filepond" type="file" name="anexo[]" id="anexo" multiple  required
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

                            <hr>
                            <div class="form-group row mb-2">
                                <label for="datas" class="col-sm-1 col-form-label text-right text-left-sm"> <span class="text-danger">*</span> Data Inicial:</label>
                                <div class="input-group col-sm-3 ">
                                    <input class="form-control datepicker m_data" type="date"  value="" name="data_inicio" id="data_inicio" required onchange="calcularDiferenca()">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">Hora inicial</span>
                                    </div>
                                    <input class="form-control" type="time"  value="" name="hora_inicio" id="hora_inicio" required onchange="calcularDiferenca()">
                                    
                                </div>
                          
                                <label for="horas" class="col-sm-1 col-form-label text-right text-left-sm"> <span class="text-danger">*</span> Data Final:</label>
                                <div class="input-group col-sm-3 ">
                                    <input class="form-control datepicker m_data" type="date"  value="" name="data_fim" id="data_fim" required onchange="calcularDiferenca()">
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">Hora Final</span>
                                    </div>
                                    <input class="form-control" type="time"  value="" name="hora_fim" id="hora_fim" required onchange="calcularDiferenca()"> 
                                </div>
                                <label for="tipoReq" class="col-sm-1 col-form-label text-sm-right text-left"><span class="text-danger"></span>Total Horas:</label>
                                <div class="col-sm-1">
                                    <input class="form-control form-control-sm" type="text" name="valor_h" id="valor_h" disabled>
                                </div>
                                <div class="col-sm-2 text-right">
                                    <button class="btn btn-success" id="btnadd" ><i class="fas fa-plus"></i> Adicionar Horas</button>
                                </div>
                            </div>

                            <div class="form-group row mt-0">
                                <div class="col-sm-12">
                                    <i><h6 style="color:gray; text-align:center;" name="resultado" id="resultado"></h6></i>
                                </div>
                                <input class="form-control form-control-sm" type="hidden" name="valor" id="valor" disabled>
                            </div>
                            
                            <input class="form-control datepicker m_data" hidden type="text" value="" name="filial" id="filial" required>
                            <input class="form-control datepicker m_data" hidden type="text" value="" name="funcao" id="funcao" required>
                            
                            <hr>

                            </div>
                            <!-- Tabela de Horas -->
                            <div class="form-group row mb-2" id="horasTableContainer" style="display:none;">
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="horasTable">
                                        <thead>
                                            <tr>
                                                <th>Data Inicial</th>
                                                <th>Hora Inicial</th>
                                                <th>Data Final</th>
                                                <th>Hora Final</th> 
                                                <th>Total de Horas</th> 
                                                <th>Ação</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- As linhas dos horarios serão adicionadas aqui -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

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

$(document).ready(function(){
    verificaData();
    if (fora_periodo == 1) { 
        exibeAlerta("error", "Atenção! Fora do período de abertura para novas requisições."); 
    }
});


document.getElementById('btnadd').addEventListener('click', function(e) {
    e.preventDefault();

    if($("#tipoPer").val()==1) {
      if($("#data_inicio").val() < '<?= $per_ini_atual ?>'){ exibeAlerta("error", "<b>Data Inicial deve estar dentro do período de ponto selecionado </b> ."); return false; }
      if($("#data_inicio").val() > '<?= $per_fim_atual ?>'){ exibeAlerta("error", "<b>Data Inicial deve estar dentro do período de ponto selecionado </b> ."); return false; }
      if($("#data_fim").val() < '<?= $per_ini_atual ?>'){ exibeAlerta("error", "<b>Data Final deve estar dentro do período de ponto selecionado </b> ."); return false; }
      if($("#data_fim").val() > '<?= $per_fim_atual ?>'){ exibeAlerta("error", "<b>Data Final deve estar dentro do período de ponto selecionado </b> ."); return false; }
    } else {
      if($("#data_inicio").val() < '<?= $per_ini_futuro ?>'){ exibeAlerta("error", "<b>Data Inicial deve estar dentro do período de ponto selecionado </b> ."); return false; }
      if($("#data_inicio").val() > '<?= $per_fim_futuro ?>'){ exibeAlerta("error", "<b>Data Inicial deve estar dentro do período de ponto selecionado </b> ."); return false; }
      if($("#data_fim").val() < '<?= $per_ini_futuro ?>'){ exibeAlerta("error", "<b>Data Final deve estar dentro do período de ponto selecionado </b> ."); return false; }
      if($("#data_fim").val() > '<?= $per_fim_futuro ?>'){ exibeAlerta("error", "<b>Data Final deve estar dentro do período de ponto selecionado </b> ."); return false; }
    }
    if($("#data_fim").val() < $("#data_inicio").val()){ exibeAlerta("error", "<b>Data Final deve ser maior ou igual à Data Inicial</b> ."); return false; }
    if($("#hora_inicio").val() == ""){ exibeAlerta("error", "<b>Hora Inicial</b> é obrigatória."); return false; }
    if($("#hora_fim").val() == ""){ exibeAlerta("error", "<b>Hora Final</b> é obrigatória."); return false; }
    
    // Verifica se tem horas
    if ($("#valor_h").val() != "") {
        // Exibe a tabela se estiver oculta
        var tableContainer = document.getElementById('horasTableContainer');

        // Verifica se o horas já foi adicionado
        var tableBody = document.getElementById('horasTable').getElementsByTagName('tbody')[0];
        var alreadyAdded = false;
        
        for (var i = 0; i < tableBody.rows.length; i++) {
            var row = tableBody.rows[i];
            var ini = formatarData(row.cells[0].innerText) + row.cells[1].innerText;
            var fim = formatarData(row.cells[2].innerText) + row.cells[3].innerText;
            var dini = $("#data_inicio").val()+$("#hora_inicio").val();
            var dfim = $("#data_fim").val()+$("#hora_fim").val();

            if (dini >= ini && dfim <= fim)  {
                alreadyAdded = true;
                break;
            }
        }

        if (alreadyAdded) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Esse intervalo de horas já está na lista',
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
        var cell6 = newRow.insertCell(5);

        // Dados de exemplo (você pode substituir pelos dados reais)
        cell1.innerHTML = formatarDataParaBR($("#data_inicio").val());    // data inicial
        cell2.innerHTML = $("#hora_inicio").val();                        // hora inicial
        cell3.innerHTML = formatarDataParaBR($("#data_fim").val());       // data final
        cell4.innerHTML = $("#hora_fim").val();                           // hora final
        cell5.innerHTML = $("#valor_h").val();                            // total de horas
        cell6.innerHTML = '<button class="btn btn-danger btn-sm" onclick="removeRow(this)">Remover</button>';
        sortTable();
    } else {
      exibeAlerta("error", "<b>Preencha todos os dados para adicionar.")
    }
});

// Função para remover a linha da tabela
function removeRow(btn) {
    var row = btn.parentNode.parentNode;
    row.parentNode.removeChild(row);
    
    // Verifica se a tabela está vazia e oculta se necessário
    var tableBody = document.getElementById('horasTable').getElementsByTagName('tbody')[0];
    if (tableBody.rows.length === 0) {
        document.getElementById('horasTableContainer').style.display = "none";
    }
}

// função para ordenar a table
function sortTable() {
  const table = document.getElementById("horasTable");
  const tbody = table.tBodies[0];
  const rows = Array.from(tbody.rows);

  rows.sort((a, b) => {
    const col0A = formatarData(a.cells[0].textContent.trim());
    const col0B = formatarData(b.cells[0].textContent.trim());

    if (col0A === col0B) {
      const col1A = a.cells[1].textContent.trim();
      const col1B = b.cells[1].textContent.trim();

      // Sort as numbers if possible, else strings
      return isNaN(col1A - col1B)
        ? col1A.localeCompare(col1B)
        : col1A - col1B;
    }

    return col0A.localeCompare(col0B);
  });

  rows.forEach(row => tbody.appendChild(row));
}

function formatarDataParaBR(data) {
  let [ano, mes, dia] = data.split("-");
  return `${dia}/${mes}/${ano}`;
}

function formatarData(data) {
  let [dia, mes, ano] = data.split("/");
  return `${ano}-${mes}-${dia}`;
}

function horasParaMinutos(horaString) {
  const [horas, minutos] = horaString.split(":").map(Number);
  return (horas * 60) + minutos;
}

function minutosParaHora(minutosTotais) {
  const horas = Math.floor(minutosTotais / 60);
  const minutos = minutosTotais % 60;

  const hh = String(horas).padStart(2, '0');
  const mm = String(minutos).padStart(2, '0');

  return `${hh}:${mm}`;
}

function verificaData() {
    const regra = <?= $param6 ?>; // Certifique-se de que $param6 seja um JSON válido.
    const funcionario =  "<?= $chapaFunc > 0 ? $chapaFunc  : '0' ?>"; // Defina o valor da constante funcionario
    console.log(funcionario);
    const hoje = new Date();
    const diaHoje = hoje.getDate(); // Obtém o dia do mês atual

    const periodoInicio = parseInt(regra.periodo_sobreaviso, 10);
    const periodoFim = parseInt(regra.periodo_sobreaviso_fim, 10);
 
    const $selectTipoReq = $('#tipoReq');
    const $opcaoMensal = $selectTipoReq.find('option[value="1"]');

    // opção "Complementar"
    const dia_lim_compl = parseInt(regra.dia_limite_compl3, 10);
    const $opcaoCompl = $selectTipoReq.find('option[value="2"]');
    
    if (diaHoje <= dia_lim_compl) {
        $opcaoCompl.prop('disabled', false); // Habilita a opção "Complementar"
    } else {
        $opcaoCompl.prop('disabled', true); // Desabilita a opção "Complementar"
    }

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


function verificaValor(valor) {
    const regra = <?= $param6 ?>;
    
    if (parseInt(regra.limite_sobreaviso) < parseInt(valor)) {
        Swal.fire({
            icon: 'error', // Ícone de erro para indicar que algo está errado
            title: 'Limite de Horas Excedido!',
            text: 'O valor inserido é Maior que o limite permitido.  '+regra.limite_sobreaviso,
            confirmButtonText: 'Ok',
            showCloseButton: true,
            allowOutsideClick: false,
            width: 600,
        });

        return false;
    }

    return true;
}

const salvaDados = () => {
    
    let formData = new FormData();
    let resultado = 0;

    const regra = <?= $param6 ?>;
    
    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("tipoReq", $("#tipoReq").val());
    formData.append("tipoPer", $("#tipoPer").val());
    formData.append("funcao", $("#funcao").val());
    formData.append("tipo", '3');
    formData.append("id", '');
        
    // Adiciona múltiplos arquivos ao formData
    let fileInput = $('#anexo')[0].files;
    if (fileInput.length > 0) {
        for (let i = 0; i < fileInput.length; i++) {
            formData.append("anexo[]", fileInput[i]);
        }
    }

    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
    if($("#valor").val() == ""){ exibeAlerta("error", "<b>Quantidade de horas obrigatória </b> ."); return false; }
    if($("#tipoReq").val() == ""){ exibeAlerta("error", "<b>Tipo obrigatório </b> ."); return false; }
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatória."); return false; }

    if (fora_periodo == 1) { 
        exibeAlerta("error", "Fora do Período de abertura."); 
        return false; 
    }
    
    if($("#tipoReq").val() == "2" && fora_periodo == 2){ 
        fora_periodo = 1;
    }
    formData.append("fora_periodo",fora_periodo);

    let total = 0;
    let hasEmptyValue = false; // Flag para identificar valor vazio

    let horas = [];

    // Verifica se o horas já foi adicionado
    var tableBody = document.getElementById('horasTable').getElementsByTagName('tbody')[0];
    var alreadyAdded = false;
    
    for (var i = 0; i < tableBody.rows.length; i++) {
        var row = tableBody.rows[i];
        var dini = formatarData(row.cells[0].innerText);
        var dfim = formatarData(row.cells[2].innerText);
        var hini = row.cells[1].innerText;
        var hfim = row.cells[3].innerText;
        var tot_horas = row.cells[4].innerText;
        var tot_minutos = horasParaMinutos(row.cells[4].innerText);

        let horario = {
            "data_inicio": dini,
            "hora_inicio": hini,
            "data_fim": dfim,
            "hora_fim": hfim,
            "tot_horas": tot_horas,
            "tot_minutos": tot_minutos
        };
        total = total + tot_minutos;
        horas.push(horario);
    }

    formData.append("horarios", JSON.stringify(horas));
    formData.append("valor", total);

    if (parseInt(regra.limite_sobreaviso*60) < parseInt(total)) {
      exibeAlerta("error", "<b>Total de horas ("+minutosParaHora(total)+"h)</b> maior que o permitido ("+regra.limite_sobreaviso+":00h)."); return false;
    }

    //console.log(JSON.stringify(Object.fromEntries(formData.entries())));
    //return false;

    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/sobreaviso/save'); ?>",
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
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/sobreaviso'); ?>');
                
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

function calcularDiferenca() {
  const dataInicio = document.getElementById('data_inicio').value;
  const horaInicio = document.getElementById('hora_inicio').value;
  const dataFim = document.getElementById('data_fim').value;
  const horaFim = document.getElementById('hora_fim').value;

  $("#valor").val(0);
  $("#valor_h").val('');

  if (!dataInicio || !horaInicio || !dataFim || !horaFim) {
    document.getElementById('resultado').textContent = 'Preencha todos os campos para calcular o total';
    return;
  }

  let dini = `${dataInicio}T${horaInicio}`;
  let dfim = `${dataFim}T${horaFim}`;
  //console.log(dini);
  //console.log(dfim);
  // Combina data e hora em uma string compatível com o Date
  const inicio = new Date(dini);
  const fim = new Date(dfim);

  // Calcula a diferença em milissegundos
  const diffMs = fim - inicio;

  if (diffMs < 0) {
    document.getElementById('resultado').textContent = 'Data/hora final é anterior à inicial';
    return;
  }

  // Converte milissegundos para horas
  const diffHoras = diffMs / (1000 * 60 * 60);

  //document.getElementById('resultado').textContent = `Diferença: ${diffHoras.toFixed(2)} horas`;
  document.getElementById('resultado').textContent = '';
  $("#valor").val(diffHoras.toFixed(2));
  $("#valor_h").val(convertDecimalHoursToHHMM(diffHoras.toFixed(2)));
}

function convertDecimalHoursToHHMM(decimalHours) {
    const hours = Math.floor(decimalHours); // Get full hours
    const minutes = Math.round((decimalHours - hours) * 60); // Convert decimal to minutes
    return `${hours}:${minutes.toString().padStart(2, '0')}`;
}

const selecionaFuncionario = (chapa) => {
    let dados = {
        "chapa":chapa,
        
    }
    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/sobreaviso/dadosFunc'); ?>",
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
</script>
<?php loadPlugin(['select2','maskmoney','datepicker']); ?>