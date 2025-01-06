<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
					<div class="card-header mt-0">
						<div class="row">
							<h4 class="col-9 mb-1 mt-1"><?= $_titulo; ?></h4>
							<div class="col-3 text-right">
								<div class="button-items">
									<a href="<?= base_url('ponto/ocorrencia') ?>" class="btn btn-primary btn-xxs mb-0"><i class="far fa-arrow-alt-circle-left"></i> voltar</a>
								</div>
							</div>
						</div>
					</div>
					
					
					
				<div class="card-body">

					<?= exibeMensagem(true); ?>

					<form action="" method="post" name="form_envio" id="form_envio">
						
						<div class="form-group row text-left">
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="excesso_abono_gestor" name="excesso_abono_gestor" <?= ( $resTipos[0]['excesso_gestor'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="excesso_abono_gestor">Excesso de Abono Gestor</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="extra_permitido" name="extra_permitido" <?= ( $resTipos[0]['extra_acima'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="extra_permitido">Extra Acima do Permitido</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="extra" name="extra" <?= ( $resTipos[0]['extra_especial'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="extra">Extra em Escala Especial</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="interjornada" name="interjornada" <?= ( $resTipos[0]['interjornada'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="interjornada">Interjornada ou Intrajornada</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="jornada" name="jornada" <?= ( $resTipos[0]['excesso_jornada'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="jornada">Excesso de jornada	</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="registro_britanico" name="registro_britanico" <?= ( $resTipos[0]['registro_bri'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="registro_britanico">Registro Britânico</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="registro_manual" name="registro_manual" <?= ( $resTipos[0]['registro_manual'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="registro_manual">Registro Manual</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="pendente_termo_aditivo" name="pendente_termo_aditivo" <?= ( $resTipos[0]['req_troca'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="pendente_termo_aditivo">Req. troca de escala pendente termo aditivo	</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="sobreaviso" name="sobreaviso" <?= ( $resTipos[0]['sobreaviso'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="sobreaviso">Sobreaviso</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="trabalho_dsr_folga" name="trabalho_dsr_folga" <?= ( $resTipos[0]['trabalho_dsr'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="trabalho_dsr_folga">Trabalho em DSR ou Folga</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="trabalho_dsr_folga_descanso" name="trabalho_dsr_folga_descanso" <?= ( $resTipos[0]['trabalho_dsr_descanso'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="trabalho_dsr_folga_descanso">Excesso de jornada semanal</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="trabalho_ferias_afastamento" name="trabalho_ferias_afastamento" <?= ( $resTipos[0]['trabalho_AfastFerias'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="trabalho_ferias_afastamento">Trabalho em Férias ou Afastamentos</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="trabalho_6dias" name="trabalho_6dias" data-parsley-multiple="groups" <?= ( $resTipos[0]['trabalho_sup6'] == 1 )  ? "checked" : ""; ?> data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="trabalho_6dias">Trabalho superior à 6 (seis) dias consecutivos sem folga</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="troca_menor_10_dias" name="troca_menor_10_dias" <?= ( $resTipos[0]['troca_menor10'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="troca_menor_10_dias">Troca de escala menor que 3 dias</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="troca_menor_6_meses" name="troca_menor_6_meses" <?= ( $resTipos[0]['troca_menor6'] == 1 )  ? "checked" : ""; ?> data-parsley-multiple="groups" data-parsley-mincheck="2" value="1">
									<label class="custom-control-label" for="troca_menor_6_meses">Troca de escala menor que 6 meses</label>
								</div>
							</div>
                    </div>
					</form>

					

				</div>
					<!-- end main -->

					<div class="card-footer text-center">
						<button class="btn btn-success" id="btnsave" onclick="return salvaDados()"><i class="fas fa-check"></i> Salvar</button>
					</div>
				
		
		  
          
        
		</div>
		
	 </div>
</div><!-- container -->
<script>
const salvaDados = () => {
	  let dados = {
        "excesso_abono_gestor": ($("#excesso_abono_gestor").is(':checked')) ? 1 : 0,
        "extra_permitido": ($("#extra_permitido").is(':checked')) ? 1 : 0,
		"extra": ($("#extra").is(':checked')) ? 1 : 0,
		"interjornada": ($("#interjornada").is(':checked')) ? 1 : 0,
		"jornada": ($("#jornada").is(':checked')) ? 1 : 0,
		"registro_britanico": ($("#registro_britanico").is(':checked')) ? 1 : 0,
		"registro_manual": ($("#registro_manual").is(':checked')) ? 1 : 0,
		"pendente_termo_aditivo": ($("#pendente_termo_aditivo").is(':checked')) ? 1 : 0,
		"sobreaviso": ($("#sobreaviso").is(':checked')) ? 1 : 0,
		"trabalho_dsr_folga": ($("#trabalho_dsr_folga").is(':checked')) ? 1 : 0,
		"trabalho_dsr_folga_descanso": ($("#trabalho_dsr_folga_descanso").is(':checked')) ? 1 : 0,
		"trabalho_ferias_afastamento": ($("#trabalho_ferias_afastamento").is(':checked')) ? 1 : 0,
		"trabalho_6dias": ($("#trabalho_6dias").is(':checked')) ? 1 : 0,
		"troca_menor_10_dias": ($("#troca_menor_10_dias").is(':checked')) ? 1 : 0,
		"troca_menor_6_meses": ($("#troca_menor_6_meses").is(':checked')) ? 1 : 0,
    };
 
	openLoading();

	$.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/grava_configuracao_tipo_ocorrencia'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            var response = JSON.parse(result);

            exibeAlerta(response.tipo, response.msg);
			openLoading(true);
            
        },
    });

    
}
</script>