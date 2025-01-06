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
                            <a href="<?= base_url('manager/funcao') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                        </div>
                    </div>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="form-group row">
                        <label for="funcao_nome" class="col-sm-2 col-form-label text-right">Nome Função:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="<?= $resFuncoes[0]['nome'] ?>" name="funcao_nome" id="funcao_nome" require>
                        </div>
                        <label for="funcao_titulo" class="col-sm-2 col-form-label text-right">Título do Menu:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="<?= $resFuncoes[0]['menutit'] ?>" name="funcao_titulo" id="funcao_titulo">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="funcao_descricao" class="col-sm-2 col-form-label text-right">Descrição:</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="funcao_descricao" id="funcao_descricao" cols="30" rows="3" require><?= $resFuncoes[0]['descr'] ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="funcao_nome" class="col-sm-2 col-form-label text-right">Modo:</label>
                        <div class="col-sm-4">
                            <select class="form-control" name="funcao_modo" id="funcao_modo" require>
                                <option value="">...</option>
                                <option value="L" <?= ($resFuncoes[0]['modo'] == 'L') ? " selected " : ""; ?>>Leitura</option>
                                <option value="E" <?= ($resFuncoes[0]['modo'] == 'E') ? " selected " : ""; ?>>Escrita</option>
                                <option value="M" <?= ($resFuncoes[0]['modo'] == 'M') ? " selected " : ""; ?>>Item de Menu</option>
                            </select>
                        </div>
                        <label for="funcao_caminho" class="col-sm-2 col-form-label text-right">Caminho:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value="<?= $resFuncoes[0]['caminho'] ?>" name="funcao_caminho" id="funcao_caminho">
                        </div>
                    </div>

                    <div class="form-group row text-left">
                        <label for="funcao_icone" class="col-sm-2 col-form-label text-right">Ícone Menu:</label>
                        <div class="col-sm-4">
                            <input class="form-control" type="text" value='<?= $resFuncoes[0]['icone'] ?>' name="funcao_icone" id="funcao_icone">
                        </div>
                        <div class="col-sm-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="funcao_menu" name="funcao_menu" data-parsley-multiple="groups" data-parsley-mincheck="2" value="1"  <?= ($resFuncoes[0]['menu'] == 'X') ? " checked " : ""; ?>>
                                <label class="custom-control-label" for="funcao_menu">Função Menu</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="funcao_atalho" name="funcao_atalho" data-parsley-multiple="groups" data-parsley-mincheck="2" value="1"  <?= ($resFuncoes[0]['atalho'] == 1) ? " checked " : ""; ?>>
                                <label class="custom-control-label" for="funcao_atalho">Fixar no Atalho</label>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="mdi mdi-content-save-outline"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
const salvaDados = () => {
    
    let dados = {
        "id": <?= $id; ?>,
        "funcao_nome": $("#funcao_nome").val(),
        "funcao_titulo": $("#funcao_titulo").val(),
        "funcao_descricao": $("#funcao_descricao").val(),
        "funcao_modo": $("#funcao_modo").val(),
        "funcao_caminho": $("#funcao_caminho").val(),
        "funcao_icone": $("#funcao_icone").val(),
        "funcao_menu": ($("#funcao_menu").is(':checked')) ? 'X' : '',
        "funcao_atalho": ($("#funcao_atalho").is(':checked')) ? 1 : 0,
    }

    if(dados.funcao_nome == ""){ exibeAlerta("error", "<b>Nome Função</b> não informada."); return false; }
    if(dados.funcao_descricao == ""){ exibeAlerta("error", "<b>Descrição</b> não informada."); return false; }
    if(dados.funcao_modo == ""){ exibeAlerta("error", "<b>Modo</b> não informado."); return false; }
    if(dados.funcao_modo == "M" && dados.funcao_icone == ""){ exibeAlerta("error", "<b>Ícone</b> obrigatório para função de menu."); return false; }

    $.ajax({
        url: "<?= base_url('manager/funcao/action/editar'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);
            openLoading(true);
            exibeAlerta(response.tipo, response.msg, 2);
            

        },
    });
    
}    
</script>