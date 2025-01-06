<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                        <?= exibeMensagem(true); ?>


                        <div class="row">

                            
                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-info text-white mt-0">
                                            <div class="row">
                                            <h4 class="col-12 mb-1 mt-1">Estrutura do Menu</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="custom-dd dd" id="menu_ativo">
                                                
                                                    <?php if($resFuncao): ?>
                                                        <ol class="dd-list" data-root="s">
                                                        <?php foreach($resFuncao as $key => $Menu): ?>
                                                            <?php if($Menu['modo'] == 'M' && $Menu['menu'] == 'X'  && strlen(trim($Menu['menupai'])) > 0): ?>
                                                                <li class="dd-item" data-id="<?= $Menu['id']; ?>" data-type="M" data-group="1">
                                                                    <div class="dd-handle">
                                                                        <i style="font-size: 20px;" class="mdi mdi-folder-open text-success"></i> - <?= $Menu['menutit']; ?> - <?= $Menu['nome']; ?> - <span class="badge badge-success">menu</span>
                                                                    </div>
                                                                    
                                                                        <ol class="dd-list">
                                                                            <?php foreach($resFuncao as $key2 => $ItensMenu): ?>
                                                                                <?php if($ItensMenu['modo'] != 'M' && $ItensMenu['menu'] == 'X'): ?>
                                                                                    <?php if($ItensMenu['menupai'] == $Menu['id']): ?>
                                                                                        <li class="dd-item" data-id="<?= $ItensMenu['id'] ?>" data-type="I">
                                                                                            <div class="dd-handle">
                                                                                                <i style="font-size: 20px;" class="mdi mdi-cube-outline text-primary"></i> - <span class="d-none d-sm-inline"><?= $ItensMenu['menutit']; ?> - </span><?= $ItensMenu['nome']; ?> - <span class="badge badge-primary">page</span>
                                                                                            </div>
                                                                                        </li>
                                                                                    <?php endif; ?>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        </ol>

                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                        </ol>
                                                    <?php endif; ?>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-info text-white mt-0">
                                            <div class="row">
                                            <h4 class="col-12 mb-1 mt-1">Funções</h4>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="custom-dd dd" id="menu_inativo">
                                                <ol class="dd-list2" data-root="s">
                                                    <?php if($resFuncao): ?>
                                                        <?php foreach($resFuncao as $key3 => $Funcao): ?>
                                                            <?php if($Funcao['menu'] == 'X' && strlen(trim($Funcao['menupai'])) <= 0): ?>
                                                                <li class="dd-item" data-id="<?= $Funcao['id']; ?>" data-type="<?= $Funcao['modo']; ?>" data-group="1">
                                                                    <div class="dd-handle">
                                                                        <?= ($Funcao['modo'] == 'M') ? '<i style="font-size: 20px;" class="mdi mdi-folder-open text-success"></i>' : '<i style="font-size: 20px;" class="mdi mdi-cube-outline text-primary"></i>'; ?> - <span class="d-none d-sm-inline"><?= $Funcao['menutit']; ?> - </span><?= $Funcao['nome']; ?> - <?= ($Funcao['modo'] == 'M') ? '<span class="badge badge-success">menu</span>' : '<span class="badge badge-primary">page</span>'; ?></span>
                                                                    </div>
                                                                </li>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </div>
                        

                </div>

                <div class="card-footer text-center">
                    <button class="btn btn-success" id="btnsave" onclick="return salvaMenu()"><i class="fas fa-check"></i> Salvar</button>
                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->

<script>
$(document).ready(function() {

    $('#menu_ativo').nestable({
        'maxDepth': 2,
        'group': 0,
        'collapseBtnHTML': '',
        'beforeDragStop': function(t, e, s){

            var type = e[0].getAttribute('data-type');
            var nivel = s[0].parentNode.className;
            var id = s[0].parentNode.id;

            if(id == 'menu_ativo'){
                if(type == 'M' && nivel != 'custom-dd dd'){
                    event.preventDefault();
                    return false;
                }
                if(type != 'M' && nivel == 'custom-dd dd'){
                    event.preventDefault();
                    return false;
                }
            }
            
        },
        'onDragStart':function(){
            if($("#menu_inativo").find('dd-empty').length > 0){
                $("#menu_inativo").html('<div class="dd-empty"></div>');
            }
        }
    });
    $('#menu_inativo').nestable({
        'maxDepth': 2,
        'group': 0,
        'fixedDepth': 0,
        'collapseBtnHTML': '',
        'beforeDragStop': function(t, e, s){

            var type = e[0].getAttribute('data-type');
            var nivel = s[0].parentNode.className;
            var id = s[0].parentNode.id;            
           
            if(id == 'menu_ativo'){
                if(type == 'M' && nivel != 'custom-dd dd'){
                    event.preventDefault();
                    return false;
                }
                if(type != 'M' && nivel == 'custom-dd dd'){
                    event.preventDefault();
                    return false;
                }
            }
            
            
        },
        'onDragStart':function(){
            if($("#menu_ativo").find('.dd-empty').length > 0){
                $("#menu_ativo").html('<div class="dd-empty"></div>');
            }
        }
    });

});
const salvaMenu = () => {

    

    var menu = $('#menu_ativo').nestable('serialize');
    let dados = {
        'menu': menu,
    }
    console.log(dados);

    //return false;
    openLoading();
    
    $.ajax({
        url: "<?= base_url('manager/menu/action/cadastrar_estrutura_menu'); ?>",
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
</script>
<?php
loadPlugin(array('nestable'));
?>
<style>
    #menu_ativo.dd > ol > li > .dd-handle {
        background-color: #ededed;
        padding: 7px;
        height: 37px
    }
</style>