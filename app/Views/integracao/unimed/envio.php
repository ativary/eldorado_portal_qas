<script>
$(document).ready(function(){
    $(".button-menu-mobile").click();
});
</script>
<div class="container-fluid"> 
    <div class="row">
        <style>
            [data-ocorrencia] {
                pointer-events: none;
            }
        </style>

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                 <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-6 mb-1 mt-1"><?= $_titulo; ?></h4>
                       
                      
                    </div>
                </div>

				
                <div class="card-body">
					
                    <?= exibeMensagem(true); ?>

                    <form action="" method="post" name="form_filtro" id="form_filtro">
                        <div class="row">
                            

                            <div class="col-8">
                                <div class="row">

                                   
                                  
                                    <label for="data_ini_fim" class="col-2 col-form-label text-right pr-0 pl-0">Data:</label>
                                    <div class="col-10 input-group">
                                        <input type="date" name="data_inicio" id="data_inicio" class="form-control form-control-sm" value="<?= $post['data_inicio'] ?? ""; ?>">
                                        <div class="input-group-prepend input-group-append"><span class="input-group-text form-control-sm">até</span></div>
                                        <input type="date" name="data_fim" id="data_fim" class="form-control form-control-sm" value="<?= $post['data_fim'] ?? ""; ?>">

                                    </div>
                                    <label for="tp_operação" class="col-2 col-form-label text-right pr-0 pl-0">Tipo Operação:</label>
                                    <div class="col-10">
                                        <select name="tp_operação" id="tp_operação" class="form-control form-control-sm">
                                            <option value="">- .... -</option>
                                           
                                                <option value="1" <?= (($post['tp_operação'] ?? "") == '1') ? " selected " : ""; ?>> 1 - Inclusão Titular  </option>
                                                <option value="2" <?= (($post['tp_operação'] ?? "") == '2') ? " selected " : ""; ?>> 2 - Inclusão de Dependente  </option>
                                                <option value="3" <?= (($post['tp_operação'] ?? "") == '3') ? " selected " : ""; ?>> 3 - Alteração Cadastral </option>

                                                <option value="5" <?= (($post['tp_operação'] ?? "") == '5') ? " selected " : ""; ?>> 5 -Troca de plano  </option>
                                                <option value="6" <?= (($post['tp_operação'] ?? "") == '6') ? " selected " : ""; ?>> 6 - Reativação </option>
                                                <option value="7" <?= (($post['tp_operação'] ?? "") == '7') ? " selected " : ""; ?>> 7 - Exclusão </option>
                                              
                                        </select>
                                    </div>
                                   
                                
                                 
                                   

                                </div>
                            </div>
                            

                            
                        </div>
                    </form>
                        
                    <div class=" text-center">
                        <button class="btn btn-primary btn-xxs" type="button" onclick="return buscaUnimed()"><i class="fas fa-search"></i> Gerar Unimed</button>
                               
				    </div>

                    </div>
                </div>

               
                 

                <!-- TABELA COM RESULTADOS -->
                <?php
                if ($DadosVaga) {
                    echo '<form action="" method="post" name="apr_batida" id="form1">';

                 
                  
                        echo '<div class="card">';
                        echo '<div class="card-body">';
                        echo '<div class="row d-flex">
                            <span id="selecctall"><input type="checkbox" id="checkall"><span style="margin-left: 10px;">Selecionar Todos</span> </span>
                        </div>';

                        echo '<table  id="datatable" class="table table-bordered">';
                        echo '<thead>';
                        echo '<th></th>';
                        echo '<th align="center" width="90"><b>Cod_Empresa</b></th>';
                        echo '<th align="center" width="60"><b>cod_Operacao</b></th>';
                        echo '<th align="center" width="120"><b>Nome Beneficiario</b></th>';
                        echo '<th align="center" width="120"><b>CPF</b></th>';
                        echo '<th align="center" width="120"><b>Matricula Empresa</b></th>';
                        echo '<th align="center" width="120"><b>Codigo Plano</b></th>';
                        echo '<th align="center" width="120"><b>Cod_Dependencia</b></th>';
                       
                        
                        echo '</thead>';

                     
                            foreach ( $DadosVaga as $key => $value) {
                              
                                    echo '<tr class="tbadmlistalin">';
                                    echo '<td width="20" align="center">
                                    <input type="checkbox" name="idbatida[]"   value="' . $key  . '">
                                    </td>';
                                    echo '<td align="center">' . (($DadosVaga[$key]["Cod_Empresa"]) ? $DadosVaga[$key]["Cod_Empresa"] : false ) . '</td>';
                                   
                                    echo '<td align="center">' .(($DadosVaga[$key]["Cod_Tipo_Operacao"]) ? $DadosVaga[$key]["Cod_Tipo_Operacao"] : false ) . '</td>';
                                  
                                    echo '<td align="center">' .(($DadosVaga[$key]["Nome_Completo_Beneficiario"]) ? $DadosVaga[$key]["Nome_Completo_Beneficiario"] :'-' ) . '</td>';
                                    echo '<td align="center">' .(($DadosVaga[$key]["Num_CPF"]) ? $DadosVaga[$key]["Num_CPF"] : false ) . '</td>';
                                    echo '<td align="center">' .(($DadosVaga[$key]["Num_Matricula_Empresa"]) ? $DadosVaga[$key]["Num_Matricula_Empresa"] : false ) . '</td>';
                                    echo '<td align="center">' .(($DadosVaga[$key]["Cod_Plano"]) ? $DadosVaga[$key]["Cod_Plano"] : false ) . '</td>';
                                     echo '<td align="center">' .(($DadosVaga[$key]["Cod_Dependencia"]) ? $DadosVaga[$key]["Cod_Dependencia"] : '-' ) . '</td>';
                                    echo '</tr>';
                                   
                                
                            }
                        

                        echo '</table>';
                        echo '</div>';
                        echo '</div>';

                        echo '</fieldset>';
                        echo '<input type="hidden" name="act" data-act>';
                        echo '<input type="hidden" value="'.$post['data_inicio'].'"  name="data_inicio2" >';
                        echo '<input type="hidden" value="'.$post['data_fim'].'" name="data_fim2" >';
                        echo '<input type="hidden" value="'.$post['tp_operação'].'" name="tp_operacao2" >';
                    
                    echo '</br>';
                    echo '<button onclick="return aprovaBatidaTot()" type="button" class="btnpeq btn-sm btn-success" style="float: right; margin: -13px 0 2px 0;">Aprovação Selecionados <i class="fa fa-check"></i></button></form>';
                    echo '</br>';
                    echo '</br>';
                } else {
                    // $status = 'info';
                    //             $msg = 'Nenhum registro encontrado.';
                    // echo monta_box_mensagem_aviso( $msg, $status );
                }
                ?>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $("#selecctall, #checkall").click(function() {
            if ($("input:checkbox").prop('checked')) {
                $("input:checkbox").prop('checked', false);
            } else {
                $("input:checkbox").prop('checked', true);
            }
        });
        $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 50,
        "aaSorting"         : [[0, "desc"]],
        "fixedHeader": true,
       
    });
}); 
    function filtroSecao() {
        var secao = document.getElementById('secao').value;
        if (secao == '') {
            alert('Selecione uma seção');
            return false;
        }
        document.getElementById('form_secao').submit();
    }

    const buscaUnimed = () => {

       
        var data_inicio = $("#data_inicio").val();
        var data_fim = $("#data_fim").val();
       
        if(data_inicio == ""){ exibeAlerta("error", "<b>Data de Inicio</b> não informada."); return false; }
        if(data_fim == ""){ exibeAlerta("error", "<b>Data Final</b> não informada."); return false; }


        if(data_inicio > data_fim){ exibeAlerta("error", "<b>Data de Inicio</b> não pode ser maior que a <b>Data Fim</b>."); return false; }
      

    
        openLoading();

        $("#form_filtro").attr('action','<?= base_url('integracao/unimed/envio'); ?>').attr('target', '_self');
        $("#form_filtro").submit();

    }


    $(function() {
        $("#ft_dtini,#ft_dtfim").datepicker({
            constrainInput: true,
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'],
            dayNamesMin: ['D', 'S', 'T', 'Q', 'Q', 'S', 'S', 'D'],
            dayNamesShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            monthNamesShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
        });


    });

    $('.data_check_func').on('click', function (){
        var chapa = $(this).attr('data-chapa');
        if ($(this).prop('checked') == true) {
                console.log('TRUE');
                $("[data-chapa=" + chapa + "]").prop('checked', true);
        } else {
                console.log('FALSE');
                $("[data-chapa=" + chapa + "]").prop('checked', false);
        }
    })

    function aprovaBatida(chapa) {
        var r = confirm('Deseja realmente aprovar as batidas selecionadas.');
        if (r == true) {
            $('[data-act]').val('apr');
            document.getElementById('chapa_' + chapa).submit();
        }
        return false;
    }

    function aprovaBatidaTot() {
        var r = confirm('Deseja realmente aprovar as batidas selecionadas.');
        if (r == true) {
            $('[data-act]').val('apr');
         
            document.getElementById('form1').submit();
        }
        return false;
    }
    

    function cancelaApr() {
        $(".popup_bg").fadeOut(100);
        $(".popup_content").fadeOut(100);
        $("#nome_func").html('');
        $("#func_chapa").val('');
    }

    function autenticaFunc() {
        var CHAPA = $("#func_chapa").val();
        var SENHA = $("#func_senha").val();
        if (CHAPA == '') {
            alert('Erro: Problema com a CHAPA.');
            return false;
        }
        if (SENHA == '') {
            alert('Erro: Senha não informada.');
            return false;
        }
        $("#func_password_" + CHAPA).val(SENHA);
        document.getElementById('chapa_' + CHAPA).submit();
    }

    function reprovaBatida() {
        var r = confirm('Deseja realmente REPROVAR as batidas selecionadas.');
        if (r == true) {
            $('[data-act]').val('rep');
            document.getElementById('form1').submit();
        }
        return false;
    }
</script>
         
        

<!--
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.3.1/css/fixedHeader.dataTables.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.js"></script>
-->
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>"/>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>

<?php
loadPlugin(array('select2', 'datatable', 'dropify'));
?>
<style>
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0 !important;
    border: none !important;
    background: none !important;
}
.dtfh-floatingparent {
    height: 205px !important;
}
.dtfh-floatingparent .sorting {
    padding-top: 75px !important;
}
body {
    overflow-x: auto !important;
}
.select2-results {
    width: max-content !important;
    background-color: #ffffff;
    border: 1px solid #cccccc;
    
}
#selecctall {
        cursor: pointer;
        float: left;
        margin-top: -3px;
        margin-left: 12px;
        font-weight: bold;
        padding: 5px 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        display: flex;
        align-items: center;
        min-width: 185px;
    }
.dropify-wrapper {
    height: 53px !important;
    width: 132px;
    line-height: 0;
}
.dropify-wrapper .dropify-message span.file-icon {
    font-size: 28px;
}
.dropify-wrapper .dropify-message p {
    margin: 5px 0 0;
    margin: 0;
    padding: 0;
    font-size: 10px;
    line-height: 1;
}
.dropify-wrapper .dropify-errors-container ul li {
    font-size: 10px;
    list-style: none;
    margin: 0;
    padding: 0;
    line-height: 1;
}
</style>
<?php gc_collect_cycles(); ?>