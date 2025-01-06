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
                                <a href="<?= base_url('premio/requisicao/editar/'.$id_requisicao) ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
                            </div>
                        </div>
                    </div>
                </div>
                
				<div class="card-body">
					<?= exibeMensagem(true); ?>

                    <div class="alert alert-warning2 border-0" role="alert">
                        <p class="p-0 m-0"><i class="mdi mdi-information"></i> Importação permitida somente de arquivo no formato <b>XLS or XLSX</b>.</p>
                    </div>
					
                    <form action="" method="post" name="upload_arquivo_importacao" id="upload_arquivo_importacao" enctype="multipart/form-data">
                        <input type="file" name="arquivo_importacao" id="arquivo_importacao" class="dropify" data-allowed-file-extensions="xls xlsx" data-show-loader="true" />
                    </form>

				</div>

                <div class="card-footer text-center">
                    <button class="btn btn-secondary" id="btn-importar" disabled onclick="return importarRequisicao()"><i class="ti-arrow-down"></i> Importar Requisição</button>
                </div>
                
			</div><!-- end card -->          
        
		</div><!-- end main -->
		
	</div><!-- end row -->
</div><!-- container -->
<script>
const importarRequisicao = () => {

    Swal.fire({
        icon: 'question',
        title: 'Confirma a importação da Requisicao?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim, importar`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) { 
            openLoading();
            
            let fd = new FormData();
            let img = $('#arquivo_importacao');
            fd.append('id_requisicao', '<?= $id_requisicao; ?>');
            fd.append('arquivo_importacao', img[0].files[0]);

            $.ajax({
                url: "<?= base_url('premio/requisicao/action/importar') ?>",
                type:'POST',
                processData: false,
                contentType: false,
                data: fd,
                success:function(result){
                    //try {
                        var response = JSON.parse(result);
                        
                        exibeAlerta(response.tipo, response.msg, 6, '<?= base_url('premio/requisicao/editar/'.$id_requisicao) ?>');
                        
                    //}catch (e) {
                    //    exibeAlerta('error', '<b>Erro interno do sistema:</b><br><code //class="language-markup">'+e+'</code>');
                   // }

                },
            });
        }
    });

}
</script>
<?php
loadPlugin(array('dropify'));
?>
<script>
$(function () {
    var drEvent = $('.dropify').dropify({
        messages: {
            default: 'Arraste e solte um arquivo aqui ou clique',
            replace: 'Arraste e solte um arquivo ou clique para substituir',
            remove:  'Remover',
            error:   'Desculpe, o arquivo é muito grande ou não esta no formato XLS/XLSX',
            
        },
        error: {
            fileExtension: "Formato do arquivo não permitido (somente {{ value }})."
        }
    });
    
    $("#arquivo_importacao").on('change', function(){
        if($(this).val() != '') $("#btn-importar").attr('class', 'btn btn-success').prop("disabled", false);
    });
    drEvent.on('dropify.afterClear', function(event, element){
        $("#btn-importar").attr('class', 'btn btn-secondary').prop("disabled", true);
    });
    drEvent.on('dropify.errors', function(event, element){
        $("#btn-importar").attr('class', 'btn btn-secondary').prop("disabled", true);
    });
});
</script>