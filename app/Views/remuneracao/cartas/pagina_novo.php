<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-8 mb-1 mt-1"><?= $_titulo; ?></h4>
                    <div class="col-4 text-right">
                            <div class="button-items">
                               <a href="<?= base_url('remuneracao/cartas/paginas').'/'.id($id_carta); ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="form-group">
                        <label for="descricao">Descrição da Página:</label>
                        <input type="text" name="descricao" id="descricao" class="form-control" value="">
                    </div>

                    <div class="form-group">
                        <textarea id="texto_pagina" name="texto_pagina"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="descricao">Parâmetros Pré-definidos:</label>
                        <select id="parametro_carta" class="form-control">
                            <option>...</option>
                            <optgroup label="===[ Outros Parâmetros ]===">
                                <option>{SQLPT:SELECT NOME_CAMPO FROM TABELA} (executa SQL no banco de dados do PORTAL)</option>
                                <option>{SQLRM:SELECT NOME_CAMPO FROM TABELA} (executa SQL no banco de dados do RM)</option>
                                <option>[DATA_EXTENSO]</option>
                                <option>[DBPORTAL]</option>
                                <option>[DBRM]</option>
                            </optgroup>
                            <optgroup label="===[ Dados do Funcionário ]===">
                                <option>[CODCOLIGADA]</option>
                                <option>[CHAPA]</option>
                                <option>[NOME]</option>
                                <option>[IDREQ]</option>
                                <option>[IDUSUARIO]</option>
                                <option>[CPF]</option>
                                <option>[EMAIL]</option>
                                <option>[CODSITUACAO]</option>
                                <option>[DATAADMISSAO]</option>
                                <option>[DATADEMISSAO]</option>
                                <option>[CODSECAO]</option>
                                <option>[NOMESECAO]</option>
                                <option>[CODFUNCAO]</option>
                                <option>[NOMEFUNCAO]</option>
                                <option>[NOMECOLIGADA]</option>
                                <option>[NOMEFANTASIACOLIGADA]</option>
                                <option>[RUA]</option>
                                <option>[BAIRRO]</option>
                                <option>[CEP]</option>
                                <option>[CIDADE]</option>
                                <option>[ESTADO]</option>
                                <option>[PIS]</option>
                                <option>[RG]</option>
                                <option>[NOMEBANCO]</option>
                                <option>[PAGTO_BANCO]</option>
                                <option>[PAGTO_AGENCIA]</option>
                                <option>[PAGTO_CONTA]</option>
                                <option>[SALARIO]</option>
                                <option>[SALARIO_BR]</option>
                                <option>[CODTIPO]</option>
                                <option>[DTNASCIMENTO]</option>
                                <option>[SEXO]</option>
                                <option>[NUMERO]</option>
                                <option>[COMPLEMENTO]</option>
                                <option>[TELEFONE1]</option>
                                <option>[TELEFONE2]</option>
                                <option>[CODPESSOA]</option>
                                <option>[DTEMISSAOIDENT]</option>
                                <option>[ORGEMISSORIDENT]</option>
                                <option>[CARTEIRATRAB]</option>
                                <option>[SERIECARTTRAB]</option>
                                <option>[DTCARTTRAB]</option>
                                <option>[UFCARTTRAB]</option>
                                <option>[CARTMOTORISTA]</option>
                                <option>[TIPOCARTHABILIT]</option>
                                <option>[TIPOCARTHABILIT]</option>
                                <option>[DTVENCHABILIT]</option>
                                <option>[CODHORARIO]</option>
                                <option>[NOMEHORARIO]</option>
                            </optgroup>
                            <optgroup label="===[ Dados Simulador (Atual) ]===">
                                <option>[ATU_SALARIO_MENSAL]</option>
                                <option>[ATU_ADIC_TRITREM]</option>
                                <option>[ATU_PERICULOSIDADE]</option>
                                <option>[ATU_PREMIO_PRODUCAO]</option>
                                <option>[ATU_MEDIA_HORA_EXTRA_50]</option>
                                <option>[ATU_MEDIA_HORA_EXTRA_80]</option>
                                <option>[ATU_MEDIA_HORA_EXTRA_100]</option>
                                <option>[ATU_MEDIA_NONA_HORA]</option>
                                <option>[ATU_MEDIA_ESPERA_HORA]</option>
                                <option>[ATU_MEDIA_DSR_HE]</option>
                                <option>[ATU_MEDIA_ADIC_NOTURNO]</option>
                                <option>[ATU_ADIC_ASSIDUIDADE]</option>
                                <option>[ATU_PPR]</option>
                                <option>[ATU_PRV]</option>
                                <option>[ATU_SUPERACAO]</option>
                                <option>[ATU_RVD_CAL]</option>
                                <option>[ATU_PP_IND_CALC]</option>
                                <option>[ATU_PLANO_SAUDE]</option>
                                <option>[ATU_CALC_PREVIDENCIA_PRIVADA]</option>
                                <option>[ATU_CALC_SEGURO_VIDA]</option>
                                <option>[ATU_CODCATEGORIA]</option>
                                <option>[ATU_CALC_INSS]</option>
                                <option>[ATU_BASE_CALC]</option>
                                <option>[ATU_SALARIO_13]</option>
                                <option>[ATU_FERIAS]</option>
                                <option>[ATU_FGTS]</option>
                                <option>[ATU_INSS]</option>
                                <option>[ATU_TRANSPORTE]</option>
                                <option>[ATU_PREVIDENCIA_PRIVADA]</option>
                                <option>[ATU_SEGURO_VIDA]</option>
                                <option>[ATU_PP_IND]</option>
                                <option>[ATU_RVD]</option>
                            </optgroup>
                            <optgroup label="===[ Dados Simulador (Calculado) ]===">
                                <option>[CALC_SALARIO_MENSAL]</option>
                                <option>[CALC_ADIC_TRITREM]</option>
                                <option>[CALC_PERICULOSIDADE]</option>
                                <option>[CALC_PREMIO_PRODUCAO]</option>
                                <option>[CALC_MEDIA_HORA_EXTRA_50]</option>
                                <option>[CALC_MEDIA_HORA_EXTRA_80]</option>
                                <option>[CALC_MEDIA_HORA_EXTRA_100]</option>
                                <option>[CALC_MEDIA_NONA_HORA]</option>
                                <option>[CALC_MEDIA_ESPERA_HORA]</option>
                                <option>[CALC_MEDIA_DSR_HE]</option>
                                <option>[CALC_MEDIA_ADIC_NOTURNO]</option>
                                <option>[CALC_ADIC_ASSIDUIDADE]</option>
                                <option>[CALC_PPR]</option>
                                <option>[CALC_PRV]</option>
                                <option>[CALC_SUPERACAO]</option>
                                <option>[CALC_RVD_CAL]</option>
                                <option>[CALC_PP_IND_CALC]</option>
                                <option>[CALC_PLANO_SAUDE]</option>
                                <option>[CALC_CALC_PREVIDENCIA_PRIVADA]</option>
                                <option>[CALC_CALC_SEGURO_VIDA]</option>
                                <option>[CALC_CODCATEGORIA]</option>
                                <option>[CALC_CALC_INSS]</option>
                                <option>[CALC_BASE_CALC]</option>
                                <option>[CALC_SALARIO_13]</option>
                                <option>[CALC_FERIAS]</option>
                                <option>[CALC_FGTS]</option>
                                <option>[CALC_INSS]</option>
                                <option>[CALC_TRANSPORTE]</option>
                                <option>[CALC_PREVIDENCIA_PRIVADA]</option>
                                <option>[CALC_SEGURO_VIDA]</option>
                                <option>[CALC_PP_IND]</option>
                                <option>[CALC_RVD]</option>
                            </optgroup>
                        
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="text" id="print_parametro" class="form-control" readonly>
                    </div>

                    
                </div>
                
                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Cadastrar</button>
                </div>
                
            </div><!-- end card -->


        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
$(document).ready(function () {
    if($("#texto_pagina").length > 0){
        tinymce.init({
            selector: "textarea#texto_pagina",
            theme: "modern",
            height:300,
            entity_encoding : "raw",
            plugins: [
                "advlist autolink link lists charmap image",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "table contextmenu directionality paste textcolor"
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image | print preview media fullpage | forecolor backcolor emoticons",
            style_formats: [
                {title: 'Bold text', inline: 'b'},
                {title: 'Red text', inline: 'span'},
                {title: 'H1', block: 'h1'},
                {title: 'H2', block: 'h2'},
                {title: 'H3', block: 'h3'},
                {title: 'H4', block: 'h4'},
                {title: 'Example 1', inline: 'span', classes: 'example1'},
                {title: 'Example 2', inline: 'span', classes: 'example2'},
                {title: 'Table styles'},
                {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
            ]
        });
    }
    $("#parametro_carta").on('change', function(i){
        var selected_option = $('#parametro_carta option:selected');
        $("#print_parametro").val(selected_option[0].innerText.substr(0, 37));
    });
});
const salvaDados = () => {
    
    let dados = {
        "id_carta": <?= $id_carta ?? 0; ?>,
        "descricao": $("#descricao").val(),
        "texto_pagina": tinymce.get("texto_pagina").getContent(),
    }
    
    if(dados.id_carta == 0){ exibeAlerta("error", "<b>ID Carta</b> não encontrado."); return false; }
    if(dados.descricao == ""){ exibeAlerta("error", "<b>Descrição da Página</b> não informado."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('remuneracao/cartas/action/nova_pagina'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            console.log(result);

            var response = JSON.parse(result);

            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('remuneracao/cartas/pagina_editar'); ?>/'+response.cod+'/<?= id($id_carta) ?>');
            }

            openLoading(true);
            
        },
    });

}
</script>
<?php
loadPlugin(array('tinymce'));
?>