<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card noprint">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Selecione o Ano</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label for="perfil_nome" class="col-2 col-form-label text-right">Ano:</label>
                        <div class="col-sm-10">

                            <form action="" method="post" name="form_dados" id="form_dados">
                                <select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo">
                                    <option value="">- selecione um período -</option>
                                    <?php if($resPeriodo): ?>
                                        <?php foreach($resPeriodo as $key => $DadosPeriodo): ?>
                                            <option value="<?= $DadosPeriodo['ANO']; ?>" <?= ($periodo == $DadosPeriodo['ANO']) ? " selected " : ""; ?>><?= $DadosPeriodo['ANO']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return consultaDados()"><i class="fas fa-search"></i> Consultar</button>
                </div>
            </div>

            <div class="card">
                
                <div class="card-header mt-0 noprint">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>
                    
                    <?php if($periodo): ?>
                        <iframe src="<?= base_url('consultar/informerendimentos/informerendimentopdf/'.$periodo); ?>" width="100%" frameborder="1" height="500"></iframe>
                    <?php endif; ?>
                    

                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
const consultaDados = () => {
    
    let dados = {
        "periodo": $("#periodo").val(),
    }

    if(dados.periodo == ""){ exibeAlerta("error", "<b>Ano</b> não selecionado."); return false; }

    openLoading();

    document.getElementById("form_dados").submit();

}

</script>
<style>
.ht {
    margin-top: -1px !important;
}
.holerite {
    width: 749px;
    text-align: center;
    margin: auto;
}
.holerite td {
    border: 1px solid #000000;
}
</style>
<?php
loadPlugin(array('select2'))
?>