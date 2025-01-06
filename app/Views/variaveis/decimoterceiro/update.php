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
                                <a href="<?= ($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/decimoterceiro'); ?> " class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
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
                          
                              
                            
                            <div class="form-group row mb-2">
                                <label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Funcionário:</label>
                                <div class="col-sm-10">
                                    <select disabled onchange="selecionaFuncionario(this.value)" class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
                                        <option value="">- ... -</option>
                                        <?php if ($resFuncionarioSecao) : ?>
                                            <?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
                                                <option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($req[0]->chapa == $DadosFunc['CHAPA']) ? " selected " : ""; ?>> <?= $DadosFunc['CHAPA'] ?> - <?= $DadosFunc['NOME'] ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option selected value="<?= $chapaFunc ?>" ><?= $log_nome ?></option>
                                        <?php endif; ?>
                
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="justificativa" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span>Justificativa:</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="justificativa" id="justificativa" cols="30" rows="3"><?= $valores->justificativa; ?></textarea>
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
    $(document).ready(function(){
       
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
    
const salvaDados = () => {
    
    let formData = new FormData();

    formData.append("funcionario", $("#funcionario").val());
    formData.append("Nome", $("#funcionario option:selected").text());
    formData.append("justificativa", $("#justificativa").val());
    formData.append("filial", $("#filial").val());
    formData.append("tipoReq", '1');
    formData.append("funcao", $("#funcao").val());
    formData.append("tipo", '9');
    formData.append("id", ' <?= $req[0]->id; ?>');
   
  
    if($("#funcionario").val() == ""){ exibeAlerta("error", "<b>Funcionário obrigatório </b> ."); return false; }
 
    if($("#justificativa").val().trim() == ""){ exibeAlerta("error", "<b>Justificativa</b> obrigatório."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('variaveis/decimoterceiro/update'); ?>",
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
                exibeAlerta(response.tipo, response.msg, 3, '<?=($req[0]->status == '3' || $req[0]->status == '7'  ) ? base_url('variaveis/sincronizacao') : base_url('variaveis/decimoterceiro');?> ');
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