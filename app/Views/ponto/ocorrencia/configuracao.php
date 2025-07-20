<?php $isAdmin = ($_SESSION['log_id'] == 1) ? true : false; ?>
<?= menuConfigPonto('Ocorrências'); ?>

<div class="container-fluid">
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a data-tab="1" class="nav-link <?php echo ($tab == '1' ? 'active' : ''); ?>" data-toggle="tab" href="#cargos" role="tab">Cargos</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="2" class="nav-link <?php echo ($tab == '2' ? 'active' : ''); ?>" data-toggle="tab" href="#tipos" role="tab">Tipos</a>
                        </li>
                        <li class="nav-item">
                            <a data-tab="3" class="nav-link <?php echo ($tab == '3' ? 'active' : ''); ?>" data-toggle="tab" href="#horarios" role="tab">Config. Horários</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane <?php echo ($tab == '1' ? 'active' : ''); ?> p-3" id="cargos" role="tabpanel">
                            <div class="container-fluid">
                                <div class="row">

                                    <!-- main -->
                                    <div class="col-12">
                                        <div class="card">

<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card">
					
				<div class="card-header mt-0">
					<div class="row">
                        <div class="col-sm-10">
						    <h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-briefcase-edit-outline"></i> <?= $_titulo; ?></h4>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" onclick="$('#modalCarga').modal();" class="btn btn-primary btn-xs btn-block bteldorado_7"><i class="fa fa-recycle"></i> Cargas ATS</button>
                        </div>
					</div>
				</div>
				
				<div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <div class="form-group row p-0 mb-2">
						<label for="add_funcao" class="col-sm-2 col-form-label text-right">Adicionar Função:</label>
                        <div class="col-sm-8">
                            <select class="form-control form-control-sm select2" name="codfuncao" id="codfuncao">
                                <option value="">...</option>
                                <?php if($resFuncao): ?>
                                    <?php foreach($resFuncao as $Funcao): ?>
                                        <option value="<?= $Funcao['CODIGO']; ?>"><?= $Funcao['NOME'].' - '.$Funcao['CODIGO']; ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" onclick="return addfuncao()" class="btn btn-primary btn-xxs btn-block bteldorado_7"><i class="mdi mdi-subdirectory-arrow-right"></i> adicionar</button>
                        </div>
                    </div>

					<table id="datatableCargos" class="table table-sm table-bordered table-hover dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr>
								<th width="250">Função</th>
                                
								<!-- <th class="text-center">Intervalo Obrigatório</th>
								<th class="text-center">Intervalo Fracionado</th>
								<th class="text-center">Intervalo planejado (Início)</th>
								<th class="text-center">Intervalo planejado (Término)</th>
                                <th class="text-center">Intervalo (Total)</th> -->
								
								<th class="text-center">Horas previstas</th>
								<th class="text-center">Limite extra</th>
								<th class="text-center">Limite jornada dia</th>
								<th class="text-center">Excesso semanal</th>
								<th class="text-center">Ação</th>
							</tr>
						</thead>
						<tbody>
                            <?php if($resMotorista): ?>
                                <?php foreach($resMotorista as $Motorista): ?>
                                    <?php $nomeFuncao = extrai_valor($resFuncao, $Motorista->codfuncao, 'CODIGO', 'NOME'); ?>
                                    <tr>
                                        <td><?= $nomeFuncao.' - '.$Motorista->codfuncao; ?></td>
                                        <!-- <td>
                                            <div class="checkbox checkbox-primary checkbox-single text-center">
                                                <input <?= ($Motorista->intervalo_obrigatorio == 1) ? ' checked ' : ''; ?> type="checkbox" data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field-<?= $Motorista->codfuncao; ?>-intervalo_obrigatorio onclick="marcaCheckbox('<?=$Motorista->codfuncao; ?>')" aria-label="Intervalo obrigatório" title="Intervalo obrigatório">
                                               <label></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="checkbox checkbox-primary checkbox-single text-center">
                                            <input <?= ($Motorista->intervalo_fracionado == 1) ? ' checked ' : ''; ?> type="checkbox" data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field-<?= $Motorista->codfuncao; ?>-intervalo_fracionado onclick="marcaCheckbox('<?=$Motorista->codfuncao; ?>')" aria-label="Intervalo fracionado" title="Intervalo fracionado">
                                            <label></label>
                                            </div>
                                        </td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="intervalo_planejado_ini" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->intervalo_planejado_ini,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="intervalo_planejado_fim" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->intervalo_planejado_fim,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="intervalo_total" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->intervalo_total,4); ?>"></td> -->
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="horas_prevista" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->horas_prevista,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="limite_extra" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->limite_extra,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="limite_jornada" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->limite_jornada,4); ?>"></td>
                                        <td class="text-center"><input data-codfuncao="<?= $Motorista->codfuncao; ?>" data-field="excesso_semanal" type="text" class="form-control form-control-sm text-center horas" value="<?= m2h($Motorista->excesso_semanal,4); ?>"></td>
                                        <td class="text-center"><button type="button" onclick="return delfuncao('<?= $Motorista->codfuncao; ?>', '<?= $nomeFuncao; ?>')" class="btn btn-danger btn-xxs"><i class="fa fa-times"></i></button></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
						</tbody>
					</table>

                </div>
			</div><!-- end card -->
		</div><!-- end col-12 -->
		
	</div><!-- end row -->
</div><!-- end container -->

                                        </div>
                                    </div><!-- end main -->

                                </div>
                            </div><!-- container -->

                        </div>

                        <div class="tab-pane <?php echo ($tab == '2' ? 'active' : ''); ?> p-3" id="tipos" role="tabpanel">
                            <div class="container-fluid">
                                <div class="row">

                                    <!-- main -->
                                    <div class="col-12">
                                        <div class="card">
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
		
	 </div>
</div><!-- container -->


                                        </div>
                                    </div><!-- end main -->

                                </div>
                            </div><!-- container -->

                        </div>

                        <div class="tab-pane <?php echo ($tab == '3' ? 'active' : ''); ?> p-3" id="horarios" role="tabpanel">

<div class="container-fluid">
  <div class="row">

    <!-- main -->
    <div class="col-12">
      <div class="card">

        <div class="card-header mt-0">
          <div class="row">
            <h4 class="col-12 mb-1 mt-1"><i class="mdi mdi-clock-alert-outline"></i> <?= $_titulo; ?></h4>
          </div>
        </div>

        <div class="card-body">

          <?= exibeMensagem(true); ?>

          <table id="datatable" class="table table-sm table-bordered table-hover dt-responsive" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr>
                <th class="text-center" width="70">Cód. Horário</th>
                <th>Horário</th>
                <th class="text-center">Intervalo Obrigatório</th>
                <th class="text-center">Intervalo Fracionado</th>
                <th class="text-center">Intervalo planejado (Início)</th>
                <th class="text-center">Intervalo planejado (Término)</th>
                <th class="text-center">Horas previstas feriado (integral)</th>
                <th class="text-center">Horas previstas feriado (parcial)</th>
                <th class="text-center">Considera escala especial</th>
                <th class="text-center">Tipo Horário</th>
                <th class="text-center">Ação</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($resHorario): ?>
                <?php foreach ($resHorario as $Horario): ?>
                  <tr>
                    <td class="text-center"><?= $Horario['CODIGO']; ?></td>
                    <td><?= $Horario['DESCRICAO']; ?></td>
                    <td class="text-center">
                      <div class="checkbox checkbox-primary checkbox-single">
                        <input <?= ($Horario['intervalo_obrigatorio'] == 1) ? ' checked ' : ''; ?> type="checkbox" data-intervalo_obrigatorio="<?= $Horario['CODIGO']; ?>" onclick="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'intervalo_obrigatorio')" aria-label="Intervalo obrigatório" title="Intervalo obrigatório">
                        <label></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <div class="checkbox checkbox-primary checkbox-single">
                        <input <?= ($Horario['intervalo_fracionado'] == 1) ? ' checked ' : ''; ?> type="checkbox" data-intervalo_fracionado="<?= $Horario['CODIGO']; ?>" onclick="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'intervalo_fracionado')" aria-label="Intervalo Fracionado" title="Intervalo Fracionado">
                        <label></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <input type="time" <?= ($Horario['intervalo_obrigatorio'] == 1) ?  '' : 'readonly'; ?> <?= ($Horario['tipo_horario'] == 'C') ?  '' : 'readonly'; ?> name="planejado_inicio" data-planejado_inicio="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['planejado_inicio'], 4); ?>" class="form-control form-control" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'planejado_inicio')">
                    </td>
                    <td class="text-center">
                      <input type="time" <?= ($Horario['intervalo_obrigatorio'] == 1) ?  '' : 'readonly'; ?> <?= ($Horario['tipo_horario'] == 'C') ?  '' : 'readonly'; ?> name="planejado_termino" data-planejado_termino="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['planejado_termino'], 4); ?>" class="form-control form-control" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'planejado_termino')">
                    </td>
                    <td class="text-center">
                      <input type="time" name="extra_feriado" data-extra_feriado="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['extra_feriado'], 4); ?>" class="form-control form-control" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'extra_feriado')">
                    </td>
                    <td class="text-center">
                      <input type="time" name="extra_feriado_parcial" data-extra_feriado_parcial="<?= $Horario['CODIGO']; ?>" value="<?= m2h($Horario['extra_feriado_parcial'], 4); ?>" class="form-control form-control" onblur="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'extra_feriado_parcial')">
                    </td>
                    <td class="text-center">
                      <div class="checkbox checkbox-primary checkbox-single">
                        <input <?= ($Horario['escala_especial'] == 1) ? ' checked ' : ''; ?> type="checkbox" data-escala_especial="<?= $Horario['CODIGO']; ?>" onclick="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'escala_especial')" aria-label="Considera escala especial" title="Considera escala especial">
                        <label></label>
                      </div>
                    </td>
                    <td class="text-center">
                      <select class="form-control" data-tipo_horario="<?= $Horario['CODIGO']; ?>" name="tipo_horario" id="tipo_horario" onchange="marcaCheckbox('<?= $Horario['CODIGO']; ?>', 'tipo_horario')">
                        <option value="">-nenhum-</option>
                        <option value="C" <?= ($Horario['tipo_horario'] == 'C') ? ' selected ' : ''; ?>>Ciclo</option>
                        <option value="S" <?= ($Horario['tipo_horario'] == 'S') ? ' selected ' : ''; ?>>Semanal</option>
                        <option value="I" <?= ($Horario['tipo_horario'] == 'I') ? ' selected ' : ''; ?>>Indice</option>
                      </select>
                    </td>
                    <td class="text-center">
                      <div class="btn-group dropleft mb-2 mb-md-0">
                        <button type="button" class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="mdi mdi-dots-vertical"></i></button>
                        <div class="dropdown-menu">
                          <button type="button" class="dropdown-item" title="Configurar Horário2" onclick="return configurarCiclo('<?= $Horario['CODIGO']; ?>', '<?= addslashes(trim(preg_replace('/\s+/', ' ', $Horario['DESCRICAO']))); ?>')">
                            <i class="mdi mdi-reload"></i> | Configurar Horário
                          </button>
                        </div>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>

        </div>
      </div><!-- end card -->
    </div><!-- end col-12 -->

  </div><!-- end row -->
</div><!-- end container -->

                        </div>

                    </div>
                </div>

            </div>

        </div><!-- end main -->

    </div>
</div><!-- container -->

<!-- modal modal_configurar_ciclo -->
<div class="modal modal_configurar_ciclo" tabindex="-1" role="dialog" data-animation="blur" aria-labelledby="modal_configurar_ciclo" aria-hidden="true" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-dark">
        <h5 class="modal-title mt-0 text-white" id="titulo_configurar_ciclo"></h5>
        <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-12">

            <table class="table table-sm text-xs table-bordered" data-detalhes-horario>
              <thead class="bg-info">
                <tr>
                  <th class="text-white text-center" width="65">Entrada1</th>
                  <th class="text-white text-center" width="65">Saida1</th>
                  <th class="text-white text-center" width="65">Entrada2</th>
                  <th class="text-white text-center" width="65">Saida2</th>
                  <th class="text-white text-center" style="background:#7b8aff;">Tolerância Entrada</th>
                  <th class="text-white text-center" style="background:#7b8aff;">Tolerância Saída</th>
                  <th class="text-white text-center">Tipo</th>
                  <th class="text-white text-center">Jornada Diaria</th>
                  <th class="text-white text-center">Intervalo</th>
                  <th class="text-white text-center">Indice</th>
                  <th class="text-white text-center" width="70">Ciclo</th>
                  <th class="text-white text-center" width="120">Extra Limite Diário</th>
                  <th class="text-white text-center" width="120">Excesso de Jornada Semanal</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table>

          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" onclick="return salvaConfigCiclo()">Salvar <i class="fas fa-check"></i></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_configurar_ciclo -->

<!-- modal -->
<div class="modal" id="modalCarga" tabindex="1" role="dialog" aria-labelledby="modalCarga" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="modalCargalabel"><span class="oi oi-people"></span> Carga retroativa de dados via API do ATS </h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body">

                <div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addData" style="width: 128px;">Data Inicial: </label>
					</div>
					<input class="form-control datepicker m_data" type="date" name="data_inicial" id="data_inicial">
				</div>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addData" style="width: 128px;">Data Final: </label>
					</div>
					<input class="form-control datepicker m_data" type="date" name="data_final" id="data_final">
				</div>

                <div class="form-control mb-3">
                    <div class="checkbox checkbox-primary">
                        <input id="proc_macros" type="checkbox" checked >
                        <label for="proc_macros">Processar Macros</label>
                    </div>
                </div>

                <div class="form-control mb-3">
                    <div class="checkbox checkbox-primary">
                        <input id="proc_tots" type="checkbox" checked >
                        <label for="proc_tots">Processar Totalizadores</label>
                    </div>
                </div>

				<div class="form-control mb-3">
                    <div class="checkbox checkbox-primary">
                        <input id="apagar_dados" type="checkbox" checked >
                        <label for="apagar_dados">Apagar dados do período antes de processar</label>
                    </div>
                </div>

                <div id="proc_box" class="form-control mb-3" style="display: none;">
                    <div class="text-center">
                        <label id="proc_dia" style="color:midnightblue">Processando dia ...</label>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="ProcessarCargas();"> <i class="fa fa-check"></i> Processar </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
			</div>
		</div>
	</div>

</div>

<!-- modal -->

<script>
  $(document).ready(function() {
    $('#datatable').DataTable({
      "aLengthMenu": [
        [25, 50, 100, 200, -1],
        [25, 50, 100, 200, "Todos"]
      ],
      "iDisplayLength": 25,
      "aaSorting": [
        [0, "desc"]
      ],
    });


  });

  $(document).ready(function() {
    $('#datatableCargos').DataTable({
        "aLengthMenu"       : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength"    : 25,
        "aaSorting"         : [[0, "desc"]],
    });
  });
  $('.horas').on('focus', function(){
      $(this).mask('99:99');
  });
  $('.horas').on('blur', function(){

    openLoading();

    var codfuncao = $(this).attr('data-codfuncao');
    var valor = $(this).val();
    var datafield =  $(this).attr('data-field');

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/salvar_param_motorista'); ?>",
        type: 'POST',
        data: {
            'codfuncao':codfuncao,
            'valor':valor,
            'datafield':datafield,
        },
        success:function(result){
            
			openLoading(true);
            var response = JSON.parse(result);
            if(response.tipo == 'success'){
                exibeAlerta(response.tipo, response.msg, 2);
            }else{
                exibeAlerta(response.tipo, response.msg);
            }
            
        },
    });
});

const addfuncao = () => {
    var codFuncao = $("#codfuncao").val();
    if(codFuncao == ''){exibeAlerta('error', '<b>Função</b> não selecionada.'); return false;}
    
    openLoading();

    $.ajax({
        url: "<?= base_url('ponto/ocorrencia/action/config_motorista'); ?>",
        type: 'POST',
        data: {'codfuncao':codFuncao},
        success:function(result){
            
			openLoading(true);
            var response = JSON.parse(result);
            if(response.tipo == 'success'){
                exibeAlerta(response.tipo, response.msg, 2, window.location.href);
            }else{
                exibeAlerta(response.tipo, response.msg);
            }
            
        },
    });
}

const delfuncao = (codfuncao, nomefuncao) => {

    Swal.fire({
        icon: 'question',
        title: 'Confirma a exclusão da função: <br>'+nomefuncao+' - '+codfuncao+'?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Sim, excluir`,
        denyButtonText: `Cancelar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {

            openLoading();
            
            $.ajax({
                url: "<?= base_url('ponto/ocorrencia/action/excluir_motorista'); ?>",
                type: 'POST',
                data: {'codfuncao':codfuncao},
                success:function(result){
                    
                    openLoading(true);
                    var response = JSON.parse(result);
                    if(response.tipo == 'success'){
                        exibeAlerta(response.tipo, response.msg, 2, window.location.href);
                    }else{
                        exibeAlerta(response.tipo, response.msg);
                    }
                    
                },
            });

        }
    });

}

const ProcessarCargas = () => {

    let dados = {
        "data_inicial": $("#data_inicial").val(),
        "data_final": $("#data_final").val(),
        "proc_macros": $("#proc_macros").is(':checked') ? 'S' : 'N',
        "proc_tots": $("#proc_tots").is(':checked') ? 'S' : 'N',
        "apagar_dados": $("#apagar_dados").is(':checked') ? 'S' : 'N',
    }

    console.log(dados.data_inicial);
    console.log(dados.data_final);
    console.log(dados.proc_macros);
    console.log(dados.proc_tots);
    console.log(dados.apagar_dados);
    
    if(dados.data_inicial == ""){ exibeAlerta("error", "<b>Data Inicial</b> não informado."); return false; }
    if(dados.data_final == ""){ exibeAlerta("error", "<b>Data Final</b> não informado."); return false; }
    if(dados.data_final < dados.data_inicial){ exibeAlerta("error", "<b>Período inválido</b>."); return false; }
    if(dados.proc_macros == "" && dados.proc_tots == ""){ exibeAlerta("error", "Selecione um grupo para processar, macros, totalizadores ou ambos."); return false; }
    
    const d1 = new Date(dados.data_inicial);
    const d2 = new Date(dados.data_final);
    let dif = (d2 - d1) / (1000 * 60 * 60 * 24); 
    console.log(dif);
    if(dif > 6){ exibeAlerta("error", "Período não pode ser superior a 7 dias."); return false; }
    if(dif < 1){ exibeAlerta("error", "Período não pode ser inferior a 2 dias."); return false; }
    console.log(dif);

    Swal.fire({
        icon: 'question',
        title: 'Confirma o processamento das cargas ATS desse período?',
        showDenyButton: true,
        showCancelButton: true,
        denyButtonText: `Cancelar`,
        confirmButtonText: `Sim, processar`,
        showCancelButton: false,
        showCloseButton: false,
        allowOutsideClick: false,
        width: 600,
    }).then((result) => {
        if (result.isConfirmed) {

            let currentDate = new Date(d1);
            let avisos = '';
            $("#proc_box").css({ display: "block" });
            $("#proc_dia").html('Processando cargas ATS...');
              
            openLoading();
            $.ajax({
                url: "<?= base_url('ponto/ats/processa_carga/'); ?>",
                type: 'POST',
                data: dados,
                success:function(result){
                    var response = JSON.parse(result);
                    if(response.tipo == 'success'){
                        $("#proc_box").hide();
                        exibeAlerta(response.tipo, response.msg);
                        return (true);
                    }else{
                        $("#proc_box").hide();
                        exibeAlerta(response.tipo, response.msg);
                        return (false);
                    }     
                },
            });

        }
    });

}

  const marcaCheckbox = (codHorario, nameField) => {

    openLoading();

    if (nameField != 'escala_especial' && nameField != 'intervalo_obrigatorio' && nameField != 'intervalo_fracionado') {
      var marcado = $("[data-" + nameField + "='" + codHorario + "']").val();
    } else {
      var marcado = ($("[data-" + nameField + "='" + codHorario + "']").prop('checked')) ? 1 : 0;
    }

    if (marcado == 0) {
      $("[data-planejado_inicio='" + codHorario + "']").prop('readonly', true);
      $("[data-planejado_termino='" + codHorario + "']").prop('readonly', true);

    } else {

      $("[data-planejado_inicio='" + codHorario + "']").prop('readonly', false);
      $("[data-planejado_termino='" + codHorario + "']").prop('readonly', false);

    }


    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/config_horario'); ?>",
      type: 'POST',
      data: {
        'codhorario': codHorario,
        'marcado': marcado,
        'field': nameField
      },
      success: function(result) {

        openLoading(true);
        var response = JSON.parse(result);
        //exibeAlerta(response.tipo, response.msg, 3);

      },
    });

  }
  const configurarCiclo = (codHorario, descricao) => {

    $(".modal_configurar_ciclo").modal('show');
    $("[data-detalhes-horario] tbody").html('');
    $("#titulo_configurar_ciclo").html('<i class="mdi mdi-reload"></i> Configurar Horário | ' + codHorario + ' - ' + descricao);

    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/carrega_dados_horario'); ?>",
      type: 'POST',
      data: {
        'codhorario': codHorario
      },
      success: function(result) {
        console.log(result);
        try {

          openLoading(true);
          var response = JSON.parse(result);

          for (var x = 0; x < response.length; x++) {
            $("[data-detalhes-horario] tbody").append(`
						<tr>
							<td class="text-center">${response[x].ENTRADA1}</td>
							<td class="text-center">${response[x].SAIDA1}</td>
							<td class="text-center">${response[x].ENTRADA2}</td>
							<td class="text-center">${response[x].SAIDA2}</td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="text" class="form-control form-control text-center horas" value="${response[x].TOLERANCIA_INICIO}"></td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="text" class="form-control form-control text-center horas" value="${response[x].TOLERANCIA_FIM}"></td>
							<td class="text-center">${response[x].TIPO}</td>
							<td class="text-center">${response[x].QTD2}</td>
							<td class="text-center">${response[x].INTERVALO}</td>
							<td class="text-center">${response[x].CODINDICE}</td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="" class="form-control form-control text-center" value="${response[x].CICLO}"></td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="time" class="form-control form-control text-center" value="${response[x].HEXTRA_DIARIA}"></td>
							<td><input data-codhorario="${response[x].CODHORARIO}" data-indice="${response[x].CODINDICE}" type="text" class="form-control form-control text-center horas" value="${response[x].EXCESSO_JORNADA_SEMANAL}"></td>
						</tr>
					`);
          }

          $(".horas").mask('99:99');

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }
  const salvaConfigCiclo = () => {

    openLoading();

    var inputs = $("[data-detalhes-horario] tbody").find('input');

    var dados = [];
    $("[data-detalhes-horario] tbody tr").each(function(e) {

      var input = $(this).find('input');

      dados.push({
        'codhorario': input[0].dataset.codhorario,
        'codindice': input[0].dataset.indice,
        'tipo': input[0].dataset.tipo,
        'tolerancia_inicio': input[0].value,
        'tolerancia_fim': input[1].value,
        'ciclo': input[2].value,
        'extra': input[3].value,
        'excesso': input[4].value,
      });

    });


    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_dados_horario'); ?>",
      type: 'POST',
      data: {
        'dados': dados
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);
          $(".modal_configurar_ciclo").modal('hide');

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }
  const salvaConfigWorkflow = () => {

    var data = {
      'data1': $("#data1").val(),
      'data2': $("#data2").val(),
      'horario1': $("#horario1").val(),
      'horario2': $("#horario2").val(),
      'ciclo1': $("#ciclo1").val(),
      'ciclo2': $("#ciclo2").val(),
    };

    if (data.data1 == '') {
      alert('error', 'Data Envio W1 não informado.');
      return;
    }
    if (data.data2 == '') {
      alert('error', 'Data Envio W2 não informado.');
      return;
    }
    if (data.ciclo1 == '') {
      alert('error', 'Ciclo de notificação W1 não informado.');
      return;
    }
    if (data.horario1 == '') {
      alert('error', 'Horário do W1 não informado.');
      return;
    }
    if (data.ciclo2 == '') {
      alert('error', 'Ciclo de notificação W2 não informado.');
      return;
    }
    if (data.horario2 == '') {
      alert('error', 'Horário do W2 não informado.');
      return;
    }

    openLoading();

    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_configuracao_workflow'); ?>",
      type: 'POST',
      data: {
        'dados': data
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }

  const salvaConfigWorkflowRH = () => {

    var data = {
      'dgestor1': $("#dgestor1").val(),
      'dgestor2': $("#dgestor2").val(),
    };

    if (data.dgestor1 == '') {
      alert('Dias para lembrete gestor não informado.');
      return;
    }
    if (data.dgestor2 == '') {
      alert('Dias para aviso ao gestor acima.');
      return;
    }

    openLoading();

    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_configuracao_workflow_RH'); ?>",
      type: 'POST',
      data: {
        'dados': data
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }

  const salvaConfigWorkflowFaltas = () => {

    var data = {
      'dias_faltas': $("#dias_faltas").val(),
      'dias_de_espera': $("#dias_de_espera").val(),
      'dias_para_escalar': $("#dias_para_escalar").val(),
      'email_rh': $("#email_rh").val(),
    };

    if (data.dias_faltas == '') {
      alert('Dias de faltas consecutivas não informado.');
      return;
    }
    if (data.dias_de_espera == '') {
      alert('Dias de espera não informado.');
      return;
    }
    if (data.dias_para_escalar == '') {
      alert('Dias para escalar não informado.');
      return;
    }
    if (data.email_rh == '') {
      alert('É necessário informar o email do RH Master que receberá o alerta de envio de telegrama.');
      return;
    }
    
    openLoading();

    $.ajax({
      url: "<?= base_url('ponto/ocorrencia/action/salva_config_workflow_faltas'); ?>",
      type: 'POST',
      data: {
        'dados': data
      },
      success: function(result) {

        try {

          openLoading(true);
          var response = JSON.parse(result);
          exibeAlerta(response.tipo, response.msg);

        } catch (e) {
          exibeAlerta('error', '<b>Erro interno:</b> ' + e);
        }

      },
    });

  }

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
<?php loadPlugin(array('select2', 'datatable', 'mask')); ?>