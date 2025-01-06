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
                                           
                                                <option value="1-Inclusão Titular" <?= (($post['tp_operação'] ?? "") == '1') ? " selected " : ""; ?>> 1 - Inclusão Titular  </option>
                                                <option value="2" <?= (($post['tp_operação'] ?? "") == '2') ? " selected " : ""; ?>> 2 - Inclusão de Dependente  </option>
                                                <option value="3-Alteração Cadastral" <?= (($post['tp_operação'] ?? "") == '3') ? " selected " : ""; ?>> 3 - Alteração Cadastral </option>

                                                <option value="5" <?= (($post['tp_operação'] ?? "") == '5') ? " selected " : ""; ?>> 5 -Troca de plano  </option>
                                                <option value="6" <?= (($post['tp_operação'] ?? "") == '6') ? " selected " : ""; ?>> 6 - Reativação </option>
                                                <option value="7" <?= (($post['tp_operação'] ?? "") == '7') ? " selected " : ""; ?>> 7 - Exclusão </option>
                                              
                                        </select>
                                    </div>
                                    <label for="tp_operação" class="col-2 col-form-label text-right pr-0 pl-0">Nome:</label>
                                    <div class="col-10">
                                    <input type="text" name="nome" id="nome" class="form-control form-control-sm" value="<?= $post['nome'] ?? ""; ?>">

                                    </div>
                                    <label for="tp_operação" class="col-2 col-form-label text-right pr-0 pl-0">CPF:</label>
                                    <div class="col-10">
                                    <input type="text" name="CPF" id="CPF" class="form-control form-control-sm" value="<?= $post['cpf'] ?? ""; ?>">

                                    </div>
                                   
                                
                                 
                                   

                                </div>
                            </div>
                            

                            
                        </div>
                    </form>
                        
                    <div class=" text-center">
                        <button class="btn btn-primary btn-xxs" type="button" onclick="return buscaUnimed()"><i class="fas fa-search"></i> Buscar</button>
                               
				    </div>

                    </div>
                </div>
              
                    <div class="tab-content">
                        <div class="tab-pane active p-3" id="colaborador" role="tabpanel">
                      
                        <table  id="datatable" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nome</th>
                                        <th>cpf</th>
                                        <th class="text-center">operacao</th>
                                        <th class="text-center">Data</th>
                                        <th class="text-center">Numero associado</th>
                                        <th class="text-center">tipo mensagem</th>
                                        <th class="text-center">resultado</th>
                                        <th class="text-center">Json</th>
                                    </tr>
                                </thead>
                                <tbody>
                               
                                    <?php if($resColoborador): ?>
                                        <?php foreach($resColoborador as $key => $Colaborador): ?>
                                            <tr class="tbadmlistalin">
                                                <td class="text-center"><?= $Colaborador['Nome']; ?></td>
                                                <td class="text-center"><?= $Colaborador['cpf']; ?></td>
                                                <td class="text-center"><?= $Colaborador['cod_operacao']; ?></td>
                                                <td class="text-center"><?= dtBr($Colaborador['dtcad']); ?></td>
                                                <td class="text-center"><?= $Colaborador['indice']; ?></td>
                                                <td class="text-center"><?= $Colaborador['tipo']; ?></td>
                                                <td class="text-center"><?= $Colaborador['erro_unimed']; ?></td>
                                                <td class="text-center"><a href="<?= base_url('integracao/unimed/jason/'.$Colaborador['id_json']); ?>" target="_blank" class="btn btn-soft-primary waves-effect waves-light btn-xxs"><i class="far fa-edit"></i> Ver</a><td>
                                            
                                                
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                        </tbody>
                            </table>

                        </div>
                       
                    </div>


                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<style>
    .tab-pane {
        border-left: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
    }
</style>
<script>
  $(document).ready(function() {
      
    $('#datatable').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 50,
        "aaSorting"         : [[0, "desc"]],
        "fixedHeader": true,
       
    });
}); 
    const buscaUnimed = () => {

            
      


        openLoading();

        $("#form_filtro").attr('action','<?= base_url('integracao/unimed'); ?>').attr('target', '_self');
        $("#form_filtro").submit();

    }
    
    const buscaOcorrencia = () => {
        
    openLoading();

        setTimeout(() => {
            window.location='<?= base_url('integracao/unimed'); ?>';
        }, 4000);

        $("#form_filtro").attr('action','<?= base_url('integracao/unimed/PegaXML'); ?>').attr('target', '_blank');
        $("#form_filtro").submit();

    }
</script>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>"/>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>

<?php
loadPlugin(array('select2', 'datatable', 'dropify'));
?>
<?php gc_collect_cycles(); ?>