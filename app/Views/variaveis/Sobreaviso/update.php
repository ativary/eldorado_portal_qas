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
                                <a href="<?= ($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/sobreaviso'); ?> " class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
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
                                        <select  class="select2 custom-select form-control form-control-sm"  name="tipoReq" id="tipoReq">
                                            <option disabled value=""> ... </option>
                                            <option value="1" <?= ($req[0]->tiporeq == '1') ? " selected " : ""; ?>> Mensal </option>
                                            <option value="2" <?= ($req[0]->tiporeq == '2') ? " selected " : ""; ?>> Complementar </option>
                                        
                                        </select>
                                    
                                    </div>

                                    <label for="tipoPer" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Período de Ponto:</label>
                                    <div class="col-sm-4">
                                        <select  class="select2 custom-select form-control form-control-sm" name="tipoPer" id="tipoPer">
                                            <option value=""> ... </option>
                                            <option value="1" <?= (isset($valores->tipoPer) and $valores->tipoPer == '1') ? " selected " : ""; ?>> Atual &nbsp;&nbsp;&nbsp; (<?= DateTime::createFromFormat('Y-m-d', $per_ini_atual)->format('d/m/Y'); ?> a <?= DateTime::createFromFormat('Y-m-d', $per_fim_atual)->format('d/m/Y'); ?>)</option>
                                            <option value="2" <?= (isset($valores->tipoPer) and $valores->tipoPer == '2') ? " selected " : ""; ?>> Futuro &nbsp; (<?= DateTime::createFromFormat('Y-m-d', $per_ini_futuro)->format('d/m/Y'); ?> a <?= DateTime::createFromFormat('Y-m-d', $per_fim_futuro)->format('d/m/Y'); ?>)</option>
                                          
                                        </select>
                                      
                                    </div>
                                </div>
                            
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
                                <div class="col-sm-10">
                                    <select disabled onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">- ... -</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($req[0]->chapa == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="datas" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span> Data Inicial:</label>
                                <div class="input-group col-sm-4 ">
                                    <input class="form-control datepicker m_data" type="date"  value="<?= isset($valores->data_inicio) ?  $valores->data_inicio : ''; ?>" name="data_inicio" id="data_inicio" required>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">Data Final</span>
                                    </div>
                                    <input class="form-control datepicker m_data" type="date"  value="<?= isset($valores->data_fim) ? $valores->data_fim : ''; ?>" name="data_fim" id="data_fim" required>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="horas" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span> Hora Inicial:</label>
                                <div class="input-group col-sm-4 ">
                                    <input class="form-control" type="time"  value="<?= isset($valores->hora_inicio) ? $valores->hora_inicio :''; ?>"  name="hora_inicio" id="hora_inicio" required>
                                    <div class="input-group-prepend input-group-append">
                                        <span class="input-group-text">Hora Final</span>
                                    </div>
                                    <input class="form-control" type="time" value="<?= isset($valores->hora_fim) ? $valores->hora_fim : ''; ?>" name="hora_fim" id="hora_fim" required>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="tipoReq" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Quantidade de horas:</label>
                                <div class="col-sm-10">
                                    <input  onchange="verificaValor(this)" class="form-control form-control-sm " type="number" value="<?= $valores->valor; ?>" name="valor" id="valor" required>
                                   
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
     var salario = 0;
    $(document).ready(function(){
        desabilitaInputs();
        verificaData();
        console.log(<?= json_encode($req[0]->valores) ?>);
    });
    
    function verificaValor(input) {
        var valor = input.value;
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

            input.value = ""; // Opcional: Limpa o valor inserido
        }
        
    }

    function desabilitaInputs() {
        // Seleciona todos os elementos de input, textarea e select da página
        const status =  <?= $req[0]->status ?>;
       const RH = <?= $rh ? 'true' : 'false' ?>;

        if(status ==1 || ((status == 3 ) &&  RH )){
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

    const periodoInicio = parseInt(regra.periodo_sobreaviso, 10);
    const periodoFim = parseInt(regra.periodo_sobreaviso_fim, 10);
 
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
    
    let formData = new FormData();
    let resultado = 0;
    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("valor", $("#valor").val());
    formData.append("tipoReq", $("#tipoReq").find("option:selected").val());
    formData.append("tipoPer", $("#tipoPer").val());
    formData.append("data_inicio", $("#data_inicio").val());
    formData.append("data_fim", $("#data_fim").val());
    formData.append("hora_inicio", $("#hora_inicio").val());
    formData.append("hora_fim", $("#hora_fim").val());
    formData.append("funcao", $("#funcao").val());
    formData.append("tipo", '3');
    formData.append("id", ' <?= $req[0]->id; ?>');
    if($("#tipoReq").val() == "2" && fora_periodo == 2){ 
        fora_periodo = 1;
     }
    formData.append("fora_periodo",fora_periodo);

    // Adiciona múltiplos arquivos ao formData
    let fileInput = $('#anexo')[0].files;
    if (fileInput.length > 0) {
        for (let i = 0; i < fileInput.length; i++) {
            formData.append("anexo[]", fileInput[i]);
        }
    }
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
    if($("#valor").val() == ""){ exibeAlerta("error", "<b>Quantidade de horas obrigatória </b> ."); return false; }
    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
    if($("#tipoReq").val() == ""){ exibeAlerta("error", "<b>Tipo obrigatório </b> ."); return false; }
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatório."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/sobreaviso/update'); ?>",
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
                exibeAlerta(response.tipo, response.msg, 3, '<?=($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/sobreaviso');?> ');
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
<?php loadPlugin(['select2','maskmoney']); ?>