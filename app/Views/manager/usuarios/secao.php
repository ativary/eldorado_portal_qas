<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-6 mb-1 mt-1">Seção do Usuário</h4>
                    <div class="col-6 text-right">
                            <div class="button-items">
                                <a href="<?= base_url('manager/usuario') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" width="30"><input type="checkbox" data-checkall></th>
                                    <th class="text-center" width="70">CÓDIGO</th>
                                    <th>DESCRIÇÃO DA SEÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if($resSecao): ?>
                                <?php foreach($resSecao as $key => $Secao): ?>

                                    <?php
                                    $checked = "";
                                    if($resUsuarioSecao){
                                        foreach($resUsuarioSecao as $key2 => $UsuarioSecao){
                                            if($UsuarioSecao['secao'] == $Secao['CODIGO']){
                                                $checked = " checked ";
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    
                                    <tr>
                                        <td class="text-center"><input data-check type="checkbox" value="<?= $Secao['CODIGO']; ?>" <?= $checked; ?>></td>
                                        <td class="text-center"><?= $Secao['CODIGO']; ?></td>
                                        <td><?= $Secao['DESCRICAO']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>  
const salvaDados = () => {

    let secaoMarcada = []
    $('[data-check]').each(function(e){

        if($(this).prop('checked')){
            secaoMarcada.push({
                codsecao: $(this).val()
            });
        }

    });

    let dados = {
        "id": <?= $id; ?>,
        "secao": secaoMarcada,
    }

    if(dados.id == ""){ exibeAlerta("error", "<b>Id Usuário</b> não informado."); return false; }

    $.ajax({
        url: "<?= base_url('manager/usuario/action/cadastrar_usuario_secao'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);
            if(response.tipo != 'success'){
                exibeAlerta(response.tipo, response.msg);
            }else{
                exibeAlerta(response.tipo, response.msg, 2, window.location.href);
            }
            
        },
    });
    
}
$(function(i){
    $("[data-checkall]").on('click', function(e){
        var Check = $(this).prop('checked');
        $('[data-check]').prop('checked', (Check) ? true : false);
    });
});
</script>