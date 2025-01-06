<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1">Seleciona uma empresa</h4>
                    </div>
                </div>

                <div class="card-body">

                    <div class="alert alert-warning2" role="alert">
                    <i class="mdi mdi-information-outline"></i> Selecione uma empresa para continuar
                    </div>

                    <?php if($resColigada): ?>
                        <?php foreach($resColigada as $key => $Coligada): ?>
                            <button onclick="selecionaColigada(<?= $Coligada['CODCOLIGADA']; ?>)" class="btn btn-primary waves-effect waves-light btn-block text-left" type="button"><i class="mdi mdi-factory"></i> <?= $Coligada['CODCOLIGADA'].' - '.$Coligada['NOME'].' - '.$Coligada['NOMEFANTASIA'] ?></button>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->