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
                                <a href="<?= base_url('variaveis/decimoterceiro') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                   

                <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#auxilio_moradia" role="tab"><i class="fa fa-home"></i>  Antecipação 1 parcela do 13ºSalário</a>
                            </li>
                          
                    </ul>
                <div class="tab-content">
                        <!-- auxilio moradia -->
                        
                        <div class="tab-pane active p-3" id="auxilio_moradia" role="tabpanel">

                            <?php if($isGestor or $isLider) {?>
                            <div class="form-group row mb-2">
                                <label class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Aplicar para:</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="para_quem" id="paraMin" value="min" onclick="Funcionario('min')" >
                                        <label class="form-check-label" for="paraMin">Para mim</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="para_quem" id="paraOutro" value="outro" onclick="Funcionario('')">
                                        <label class="form-check-label" for="paraOutro">Para outro</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-2" id="funcionario-div" style="display: none;">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">
                                    <span class="text-danger">*</span>Funcionário:
                                </label>
                                <div class="col-sm-10">
                                    <select onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">...</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($funcionario == $DadosFunc['CHAPA']) ? " selected " : ""; ?>>
                                                    <?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="<?= $chapaFunc ?>"><?= $log_nome ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <?php } else {?>
                            <div class="form-group row mb-2" id="funcionario-div" style="display: none;">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">
                                    <span class="text-danger">*</span>Funcionário:
                                </label>
                                <div class="col-sm-10">
                                    <select onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">...</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($funcionario == $DadosFunc['CHAPA']) ? " selected " : ""; ?>>
                                                    <?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="<?= $chapaFunc ?>"><?= $log_nome ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <?php }?>

                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"> <span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"></textarea>
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
  $(document).ready(function(){
    <?php if(!$isGestor and !$isLider) {?>
      Funcionario('min');
    <?php }?>
  });


const salvaDados = () => {
    
    let formData = new FormData();
    const regra = <?= $param6 ?>;
   
    let periodo;
    const periodoInicio = criarData(regra.periodo_13salario);
    const periodoFim = criarData(regra.periodo_13salario_fim);
    const hoje = new Date();
    hoje.setHours(0, 0, 0, 0); // Ajusta para meia-noite (00:00:00)
    const funcionario =  "<?= $chapaFunc > 0 ? $chapaFunc  : '0' ?>"; // Defina o valor da constante funcionario
      // Verifica se o funcionário está dentro da regra de gestor
      if (Array.isArray(regra.gestor) && regra.gestor.length > 0) {
        $.each(regra.gestor, function(index, func) {
            if (func.chapa == funcionario) {
                // Converter dt_ini e dt_fim para objetos Date
                const dtIni = new Date(`${func.dt_ini}T00:00:00`);
                const dtFim = new Date(`${func.dt_fim}T00:00:00`);
                if (hoje >= dtIni && hoje <= dtFim) {
                    fora_periodo = 2;
                  
                    return false; // Sai do loop
                }
            }
        });
    }

    // Verifica se a data de hoje está dentro do período do 13º salário ou do gestor
    if (fora_periodo != 2 && (hoje < periodoInicio || hoje > periodoFim)) {
        Swal.fire({
            icon: 'warning',
            title: 'Período inválido',
            text: 'A data atual não está dentro do período permitido de ' + formatarData(periodoInicio) + ' até ' + formatarData(periodoFim),
            confirmButtonText: 'OK'
        });
        return false; // Interrompe o envio do formulário se estiver fora do período
    }
    

    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("fora_periodo",  fora_periodo);
    formData.append("tipoReq", '1');
    formData.append("funcao", $("#funcao").val());
    formData.append("tipo", '9');
    formData.append("id", '');
  
    <?php if($isGestor or $isLider) {?>
    if (!$("input[name='para_quem']:checked").val()) {
        // Exibe o alerta se nenhum radio estiver marcado
        Swal.fire({
            icon: 'warning',
            title: 'Atenção',
            text: 'Por favor, selecione uma opção: Para mim ou Para outro.',
            confirmButtonText: 'OK'
        });
        return false; // Impede o envio do formulário ou execução de outra ação
    }
    <?php }?>

    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
    
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatório."); return false; }
  
    formData.append("fora_periodo",fora_periodo);

    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/decimoterceiro/save'); ?>",
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
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('variaveis/decimoterceiro'); ?>');
                
            }

        },
    });
    
}
const criarData = (dataString) => {
        let partes = dataString.split('-'); // Supondo que o formato seja yyyy-mm-dd
        return new Date(partes[0], partes[1] - 1, partes[2]); // O mês em JavaScript começa em 0
    }
const formatarData = (data) => {
        let dia = String(data.getDate()).padStart(2, '0');
        let mes = String(data.getMonth() + 1).padStart(2, '0'); // Meses começam do 0
        let ano = data.getFullYear();
        return `${dia}/${mes}/${ano}`;
    }

    
$("#anexo").fileinput({
    showUpload: false,
    
    dropZoneEnabled: true, // Mantém a dropzone visível
    fileActionSettings: {
        showRemove: true, // Exibe o botão de remover
        
        showZoom: false, // Oculta o botão de zoom
      
    },
    showCaption: true,
    browseClass: "btn btn-primary",
    fileType: "any",
    showClose: false, // Esconde o botão de "x" (fechar) na visualização dos arquivos
    browseLabel: "Selecionar Arquivo", // Texto personalizado do botão de anexar
    dropZoneTitle: "Arraste o(s) arquivo(s) aqui. Para anexar mais de um arquivo arraste todos de uma vez. Os navegadores de internet não permitem arrastar um arquivo por vez. O mesmo vale para a seleção de arquivos, caso queira mais de um arquivo selecione todos de uma vez, usando o SHIFT ou CRTL junto com o clique do mouse.", // Texto personalizado da zona de drop
    dropZoneClickTitle: "ou clique para selecionar os arquivos" // Texto secundário na zona de drop
});


function Funcionario (valor) {
    $('#funcionario-div').show(); // Mostra a div que contém o select
    var funcionario = document.getElementById('funcionario');
 
    console.log(<?= $chapaFunc ?>);
    if (valor === 'min') {
        funcionario.value = '<?= $chapaFunc ?>';
        funcionario.disabled = true; // Desabilita o campo select
    } else {
        funcionario.value = '';
        funcionario.disabled = false; // Habilita o campo select
    }
        
    // Forçar o select2 a reconhecer a mudança
    $('#funcionario').trigger('change');
}


const selecionaFuncionario = (chapa) => {
    let dados = {
        "chapa":chapa,
        
    }
    
    if(!chapa || chapa == ''){
        return false;

    }

    openLoading();
    $.ajax({
        url: "<?= base_url('variaveis/decimoterceiro/dadosFunc'); ?>",
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