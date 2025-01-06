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
                                <a href="<?= base_url('remuneracao/cartas'); ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                                <a href="<?= base_url('remuneracao/cartas/pagina_novo').'/'.id($id_carta); ?>" class="btn btn-primary btn-xxs mb-0"><i class="fas fa-plus-circle"></i> Novo</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">ID</th>
                                <th class="text-center">Descrição</th>
                                <th class="text-center" width="180">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($resPaginas): ?>
                                <?php foreach($resPaginas as $key => $Paginas): ?>
                                    <tr data-linha="<?= $Paginas['id'] ?>">
                                        <td class="text-center"><?= $Paginas['id']; ?></td>
                                        <td><a href="<?= base_url('remuneracao/cartas/pagina_editar/'.id($Paginas['id'])).'/'.id($id_carta); ?>"><?= $Paginas['descricao']; ?></a></td>
                                        <td class="text-center">
                                            <div class="btn-group" aria-label="acao" role="group">
                                                <a href="<?= base_url('remuneracao/cartas/pagina_editar/'.id($Paginas['id'])).'/'.id($id_carta); ?>" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="far fa-edit"></i> editar</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    
                </div>
            </div><!-- end card -->


        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->