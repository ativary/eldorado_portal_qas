<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
                
                <div class="card-header mt-0">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1">Selecione um registro</h4>
                    </div>
                </div>

                <div class="card-body">

                    <div class="alert alert-warning2" role="alert">
                    <i class="mdi mdi-information-outline"></i> Selecione o registro que deseja se conectar.
                    </div>

                    <?php if($resChapa): ?>
                        <?php foreach($resChapa as $key => $DadosFuncionario): ?>
                            <button onclick="selecionaRegistro('<?= base64_encode($DadosFuncionario['CHAPA'].':'.$DadosFuncionario['CODCOLIGADA']); ?>')" class="btn btn-success waves-effect waves-light btn-block text-left" type="button"><i class="fas fa-user-alt"></i> <?= $DadosFuncionario['CHAPA'].' - '.$DadosFuncionario['NOME'].' - ['.$DadosFuncionario['CODCOLIGADA'].' - '.$DadosFuncionario['NOMECOLIGADA'].' - '.$DadosFuncionario['NOMEFANTASIACOLIGADA'] ?>]</button>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->