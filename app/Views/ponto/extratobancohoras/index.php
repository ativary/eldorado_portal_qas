<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card noprint">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Filtro</h4>
                    </div>
                </div>
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <form action="<?= base_url('ponto/extratobancohoras/pdf'); ?>" method="post" name="form_filtro" id="form_filtro" target="_blank">
                        <input type="hidden" name="action" id="action">

                        <div class="row">
                            <label for="mescomp" class="col-sm-2 col-form-label text-right text-left-sm">Mês Comp.:<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="mescomp" id="mescomp" class="form-control form-control-sm col-md-2">
                                    <?php for($i=1;$i<=12;$i++): ?>
                                        <option value="<?= $i; ?>"><?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label for="anocomp" class="col-sm-2 col-form-label text-right text-left-sm">Ano Comp.:<span class="text-danger">*</span></label>
                            <div class="col-sm-10"><input class="form-control form-control-sm col-md-2" name="anocomp" type="number" min="1900" max="2099" step="1"></div>
                        </div>
                        <?php if($resSecaoGestor): ?>
                        <div class="row">
                            <label for="secao" class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
                            <div class="col-sm-10">
                                <select class="select2 custom-select form-control form-control-sm" name="secao" id="secao">
                                    <option value="">-- Todas --</option>
                                    <?php if($resSecaoGestor): ?>
                                        <?php foreach($resSecaoGestor as $key => $SecaoGestor): ?>
                                            <option value="<?= $SecaoGestor['CODIGO']; ?>"><?= $SecaoGestor['CODIGO'].' - '.$SecaoGestor['DESCRICAO']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label for="keyword" class="col-form-label col-sm-2 text-left-sm">Chapa:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" name="keyword" value="" class="form-control form-control-sm col-3" placeholder="chapa / nome">
                                    <div class="input-group-prepend input-group-append bg-success">
                                        <span class="input-group-text pt-0 pb-0"><button onclick="procurarFuncionario()" type="button" class="btn btn-primary btn-xxs"><i class="fas fa-search"></i></button></span>
                                    </div>
                                    <select name="chapa" id="chapa" class="form-control form-control-sm"></select>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                            <input type="hidden" name="chapa" value="<?= util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null; ?>">
                        <?php endif; ?>
                        
                    </form>
                </div>
                <div class="card-footer text-center">
                    <button type="button" class="btn btn-danger btn-xxs" onclick="executar()">Gerar PDF <i class="mdi mdi-file-pdf"></i></button>
                </div>
            </div>
        </div><!-- end main -->


    </div>
</div><!-- container -->
<script>
const executar = () => {

    var dados = {
        'mescomp' : $("[name=mescomp]").val(),
        'anocomp' : $("[name=anocomp]").val(),
    }

    if(dados.mescomp == ''){exibeAlerta('error', '<b>Mês competência</b> não informado'); return false;}
    if(dados.anocomp == ''){exibeAlerta('error', '<b>Ano competência</b> não informado'); return false;}
    
    $("#form_filtro").submit();

}
const procurarFuncionario = () => {
    
    let keyword = $('[name=keyword]').val();
    if(keyword.length < 4){ exibeAlerta('warning', 'Digite pelo menos 4 caracteres para pesquisar.'); return false; }

    openLoading();
    
    $("select[name=chapa]").html('');

    $.ajax({
        url: base_url + '/relatorio/gerar/action/lista_funcionarios',
        type: 'POST',
        data: {
            'keyword'  : keyword
        },
        success: function(result) {

            openLoading(true);

            try {
                var response = JSON.parse(result);

                $("select[name=chapa]").append('<option value="">Selecione o Colaborador ('+response.length+')</option>');
                if(response.length >= 1000){exibeAlerta('info', 'Qtde de registro ultrapassou o limite permitido, exibindo os primeiros 1000 registros.');}
                for(var x = 0; x < response.length; x++){
                    $("select[name=chapa]").append('<option value="'+response[x].CHAPA+'">'+response[x].NOME + ' - ' +response[x].CHAPA+'</option>');
                }

            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }

        },
    });

}
</script>
<?php
loadPlugin(array('select2', 'mask', 'tooltips'))
?>