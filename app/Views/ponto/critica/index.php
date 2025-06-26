<script>
$(document).ready(function(){
    // $(".button-menu-mobile").click();
});
</script>
<div class="popup_bg"></div>
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



					<form action="" method="post" name="form_filtro" id="form_filtro">

						<div class="form-group row mb-0">
							<label for="opt_periodo" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span> Período</label>
							<div class="col-sm-10">
								<select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo" onchange="return carregaColaboradores()">
									<option value="">- selecione um período -</option>
									<?php if ($resPeriodo) : ?>
										<?php foreach ($resPeriodo as $key => $DadosPeriodo) : ?>
											<option data-inicio="<?= date('Y-m-d', strtotime($DadosPeriodo['INICIOMENSAL'])) ?>" data-fim="<?= date('Y-m-d', strtotime($DadosPeriodo['FIMMENSAL'])) ?>" value="<?= dtBr($DadosPeriodo['INICIOMENSAL']) . dtBr($DadosPeriodo['FIMMENSAL']).$DadosPeriodo['STATUSPERIODO']; ?>" <?= ($periodo == dtBr($DadosPeriodo['INICIOMENSAL']) . dtBr($DadosPeriodo['FIMMENSAL'])) ? " selected " : ""; ?>><?= dtBr($DadosPeriodo['INICIOMENSAL']) . ' à ' . dtBr($DadosPeriodo['FIMMENSAL']); ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
						</div>

						<div class="form-group row mb-0">
							<label for="secao" class="col-sm-2 col-form-label text-right text-left-sm">Seção:</label>
							<div class="col-sm-10">
								<select data-secao class="select2 mb-3 select2-multiple form-control-sm" name="secao[]" data-secao style="width: 100%" multiple="multiple" data-placeholder="- Todos -"  onchange="return carregaColaboradores()">
									<option value="">- Todos -</option>
									<?php
                                    if($listaSecaoUsuarioRM){
                                        foreach($listaSecaoUsuarioRM as $key => $Secao){
                                            echo '<option value="'.$Secao['CODIGO'].'" '.((in_array($Secao['CODIGO'], $secao ?? array())) ? 'selected' : "").'>'.$Secao['DESCRICAO'].' - '.$Secao['CODIGO'].'</option>';
                                        }
                                    }
                                    ?>
								</select>
							</div>
						</div>
						<div class="form-group row mb-0">
							<label for="funcionario" class="col-sm-2 col-form-label text-right text-left-sm">Colaborador:</label>
							<div class="col-sm-10">
								<select class="select2 custom-select form-control form-control-sm" name="funcionario" id="funcionario">
									<option value="">- Todos -</option>
									<?php if ($resFuncionarioSecao) : ?>
										<?php foreach ($resFuncionarioSecao as $key => $DadosFunc) : ?>
											<option value="<?= $DadosFunc['CHAPA'] ?>" <?= ($funcionario == $DadosFunc['CHAPA']) ? " selected " : ""; ?>><?= $DadosFunc['CHAPA'] . ' - ' . $DadosFunc['NOME'] ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
						</div>
						
						<div class="form-group row mb-2">
							<label for="data" class="col-sm-2 col-form-label text-right text-left-sm"><span class="text-danger">*</span> Data:</label>
							<div class="input-group col-md-4">
								<input class="form-control datepicker m_data" type="date" value="<?php echo isset($_POST['data_inicio']) ? $_POST['data_inicio'] : (isset($data_inicio) ? $data_inicio : ''); ?>" name="data_inicio" id="data_inicio" required>
								<div class="input-group-prepend input-group-append">
									<span class="input-group-text">até</span>
								</div>
								<input class="form-control datepicker m_data" type="date" value="<?php echo isset($_POST['data_fim']) ? $_POST['data_fim'] : (isset($data_fim) ? $data_fim : ''); ?>" name="data_fim" id="data_fim" require>
							</div>
						</div>

						<div class="form-group row mb-0">

							<div class="col-sm-3 offset-sm-2">
								<div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ck_ExtraExecutado" id="ck_ExtraExecutado" value="1" <?php if (isset($_POST['ck_ExtraExecutado']) && $_POST['ck_ExtraExecutado'] == '1') {
																																							echo 'checked="checked"';
																																						} ?> />
										<label class="custom-control-label" for="ck_ExtraExecutado">Extra executado</label>
										<input data-time size="5" mask-batida maxlength="5" type="text" value="<?php echo isset($_POST['vl_extra_executado']) ? $_POST['vl_extra_executado'] : $vl_extra_executado; ?>" name="vl_extra_executado" id="vl_extra_executado">
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ck_jorMaior12" id="ck_jorMaior12" value="1" <?php if (isset($_POST['ck_jorMaior12']) && $_POST['ck_jorMaior12'] == '1') {
																																					echo 'checked="checked"';
																																				} else if ($ck_jorMaior12 == '1') {
																																					echo 'checked="checked"';
																																				} ?> />
										<label class="custom-control-label" for="ck_jorMaior12">Jorn. maior 12h</label>
									</div>
								</div>
							</div>

							<div class="col-sm-3">
                  <div class="checkbox">
                    <div class="custom-control custom-checkbox">
                      <input type="checkbox" class="custom-control-input" name="ck_semPar" id="ck_semPar" value="1" <?php if (isset($_POST['ck_semPar']) && $_POST['ck_semPar'] == '1') {
                                                                        echo 'checked="checked"';
                                                                      } else if ($ck_semPar == '1') {
                                                                        echo 'checked="checked"';
                                                                      } ?> />
                      <label class="custom-control-label" for="ck_semPar">Sem par correspondente</label>
                    </div>
                  </div>                                        
								</div>

						</div>

						<div class="form-group row mb-0">
							<div class="col-sm-3 offset-sm-2">
								<div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ck_interjornada" id="ck_interjornada" value="1" <?php if (isset($_POST['ck_interjornada']) && $_POST['ck_interjornada'] == '1') {
																																						echo 'checked="checked"';
																																					} else if ($ck_interjornada == '1') {
																																						echo 'checked="checked"';
																																					} ?> />
										<label class="custom-control-label" for="ck_interjornada">Interjornada</label>
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ck_Atrasos" id="ck_Atrasos" value="1" <?php if (isset($_POST['ck_Atrasos']) && $_POST['ck_Atrasos'] == '1') {
																																			echo 'checked="checked"';
																																		} else if ($ck_Atrasos == '1') {
																																			echo 'checked="checked"';
																																		} ?> />
										<label class="custom-control-label" for="ck_Atrasos">Atrasos</label>
										<input data-time size="5" mask-batida maxlength="5" type="text" value="<?php echo $vl_atrasos ?>" name="vl_atrasos" id="vl_atrasos">
									</div>
								</div>
								
							</div>
              <div class="col-sm-3">
                  <div class="checkbox">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="ck_todos" id="ck_todos" value="1" <?php if (isset($_POST['ck_todos']) && $_POST['ck_todos'] == '1') {
																																				echo 'checked="checked"';
																																			} else if ($ck_todos == '1') {
																																				echo 'checked="checked"';
																																			} ?> />
											<label class="custom-control-label" for="ck_todos">Todos</label>
										</div>
									</div>
              </div>
						</div>
						<div class="form-group row mb-0">
							<div class="col-sm-3 offset-sm-2">
							  <div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ck_jorMaior10" id="ck_jorMaior10" value="1" <?php if (isset($_POST['ck_jorMaior10']) && $_POST['ck_jorMaior10'] == '1') {
																																					echo 'checked="checked"';
																																				} else if ($ck_jorMaior10 == '1') {
																																					echo 'checked="checked"';
																																				} ?> />
										<label class="custom-control-label" for="ck_jorMaior10">Jorn. maior 10h</label>
									</div>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="checkbox">
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" name="ck_Faltas" id="ck_Faltas" value="1" <?php if (isset($_POST['ck_Faltas']) && $_POST['ck_Faltas'] == '1') {
																																			echo 'checked="checked"';
																																		} else if ($ck_Faltas == '1') {
																																			echo 'checked="checked"';
																																		} ?> />
										<label class="custom-control-label" for="ck_Faltas">Faltas</label>
									</div>
								</div>
								
							</div>
						</div>
					</form>
				</div>




				



				<div class="card-footer text-center">
					<button class="btn btn-primary btn-sm bteldorado_7" id="btnfiltro" type="button" onclick="return Filtro()">Exibir <i class="fas fa-check"></i></button>
					<button class="btn btn-success btn-sm bteldorado_1" id="btExcel" type="button" onclick="return Excel()">Gerar Excel <i class="mdi mdi-file-excel"></i></button>
				</div>

			</div>
		</div>

	</div>


<div class="row">
	<div class="col-md-12 col-lg-12">
		<div class="card">
			<div class="card-body">

				<p class="m-0">
					<b>Legenda:</b> 
					<i title="Aguardando Aprovação Gestor" class="mdi mdi-square ml-3 txteldorado_5" style="font-size:20px; color: #feca07;"></i> Pendente Aprovação
					<i title="Pendente Aprovação RH" class="mdi mdi-square ml-3 txteldorado_8" style="font-size:20px; color: #37b550;"></i> Pendente Aprovação RH
					<i title="Ação Reprovada" class="mdi mdi-square ml-3 txteldorado_2" style="font-size:20px; color: #f4811f;"></i> Ação Reprovada
          <i title="Registro Coletado" class="mdi mdi-alpha-c-box-outline ml-3" style="font-size:20px; color:rgb(43, 41, 41);"></i> Coletado
          <i title="Registro Digitado" class="mdi mdi-alpha-d-box-outline ml-3" style="font-size:20px; color:rgb(43, 41, 41);"></i> Digitado
				</p>

				<div class="table-responsive">
					<table width="100%" cellspacing="0" cellpadding="0" id="tb-ponto-critica" class="table table-sm table-striped table-bordered">
						<thead>
							<tr class="text-center">
								<td class="y-mobile-cell d-none">Data</td>
								<td class="y-mobile-cell d-none" width="60">Entrada</td>
								<td class="y-mobile-cell d-none" width="60">Saída</td>
								<td class="y-mobile-cell d-none">Ocorrência</td>


								<td class="n-mobile-cell" width="40" data-orderable="false">Nº</td>
								<td class="n-mobile-cell" width="40">Chapa</td>
								<td class="n-mobile-cell">Nome</td>

								<td class="n-mobile-cell" width="40">Situação</td>
								<td class="n-mobile-cell" width="40">Dia</td>
								<td class="n-mobile-cell">Data</td>
								<td class="n-mobile-cell" data-orderable="false">Ent1</td>
								<td class="n-mobile-cell" data-orderable="false">Sai1</td>
								<td class="n-mobile-cell" data-orderable="false">Ent2</td>
								<td class="n-mobile-cell" data-orderable="false">Sai2</td>
								<td class="n-mobile-cell" data-orderable="false">Ent3</td>
								<td class="n-mobile-cell" data-orderable="false">Sai3</td>


								<?php if ($ck_semPar) { ?><td class="n-mobile-cell" data-orderable="false">S/ Par.<br> Corresp.</td><?php } ?>
								<?php if ($ck_ExtraExecutado) { ?><td class="n-mobile-cell" data-orderable="false">Extra <br>Executado</td><?php } ?>
								<?php if ($ck_Atrasos) { ?><td class="n-mobile-cell" data-orderable="false">Atrasos</td><?php } ?>
								<?php if ($ck_Faltas) { ?><td class="n-mobile-cell" data-orderable="false">Faltas</td><?php } ?>
								<?php if ($ck_jorMaior10) { ?><td class="n-mobile-cell" data-orderable="false">Jor. > 10h</td><?php } ?>
								<?php if ($ck_jorMaior12) { ?><td class="n-mobile-cell" data-orderable="false">Jor. > 12h</td><?php } ?>
								<?php if ($ck_interjornada) { ?><td class="n-mobile-cell" data-orderable="false">Interjornada</td><?php } ?>
								<td class="n-mobile-cell" data-orderable="false">Justificativa (Atraso/Falta/Extra)</td>

								<td width="10" data-orderable="false">Ação</td>

							</tr>
						</thead>
						<tbody>
							<?php

							$periodo_bloqueado = false;
							if ($resData) {
								$html = '';
								$nLinha = 1;


								if(count($resData ?? array()) >= 1000){
									echo '<div class="alert alert-warning2 border-0" role="alert">
										<i class="fas fa-info-circle"></i> <b>Atenção!</b> Limite de registros atingido, exibindo os primeiros 1000, por favor ajuste o filtro.
									</div>';
								}

								
								// if($periodo_ativo == 1){
								// 	$periodo_bloqueado = ($EspelhoConfiguracao[0]['limite_gestor'] < date('Y-m-d')) ? true : false;
								// }
								// if($periodo_ativo == 0){
								// 	$periodo_bloqueado = ($EspelhoConfiguracao[0]['limite_gestor'] > dtEn($DadosPeriodo['FIMMENSAL'], true)) ? true : false;
								// }
								// $periodo_bloqueado = false;

								//-----------------------------------------------
								// regra para bloqueio do ponto
								//-----------------------------------------------
								if($statusPeriodo == 1){
									if($isGestor){
										$periodo_bloqueado = (dtEn($EspelhoConfiguracao[0]['limite_gestor'], true) < date('Y-m-d')) ? true : false;
										if($gestorPossuiExcecao && $periodo_bloqueado){
											$periodo_bloqueado = (dtEn($gestorPossuiExcecao, true) < date('Y-m-d')) ? true : false;
										}
									}else{
										$periodo_bloqueado = (dtEn($EspelhoConfiguracao[0]['limite_funcionario'], true) < date('Y-m-d')) ? true : false;
										if($gestorPossuiExcecao && $periodo_bloqueado){
											$periodo_bloqueado = (dtEn($gestorPossuiExcecao, true) < date('Y-m-d')) ? true : false;
										}
									}
								}
								//if($statusPeriodo == 0) $periodo_bloqueado = true;
								//-----------------------------------------------




								foreach ($resData as $idbData => $value) {
									$i = $idbData;
									// $dataModal = $resData[$i]['DATA'];

									if ($ck_Faltas && !$ck_semPar) {
										if (((int)$resData[$i]['FALTA_CASE'] > 0 && (int)$resData[$i]['BATIDAS_PORTAL'] > 0 && (int)$resData[$i]['SEM_PAR_CORRESPONDENTE'] > 0)) {
											// continue;
										}
									}

									// SOLICITAÇÕES ABONO FALTA
									$array_abonos_falta = array();
									$abono_pendente = "";
									$abono_pendente_mobile = "";
									$abono_reprovado_falta = "";
									$abono_pendente_falta = "";
									if ($resSolicitacaoAbonoFalta) {
										foreach ($resSolicitacaoAbonoFalta as $keyF => $SolicitacaoAbono) {
											if ($SolicitacaoAbono['chapa'] == $resData[$i]['CHAPA'] && dtEn($SolicitacaoAbono['dtponto'], true) == dtEn($resData[$i]['DATA'], true)) {
												$nome_anexo = "";
												if(strlen(trim($SolicitacaoAbono['abono_atestado'])) > 0){
													$file = explode('|', $SolicitacaoAbono['abono_atestado']);
													$nome_anexo = $file[0];
													unset($file);
												}
												$array_abonos_falta[] = array(
													'id'              => $SolicitacaoAbono['id'],
													'data'            => dtEn($SolicitacaoAbono['dtponto'], true),
													'inicio'          => m2h($SolicitacaoAbono['abn_horaini'], 4),
													'termino'         => m2h($SolicitacaoAbono['abn_horafim'], 4),
													'codabono'        => $SolicitacaoAbono['abn_codabono'],
													'status'          => $SolicitacaoAbono['status'],
													'just_abono_tipo' => $SolicitacaoAbono['justificativa_abono_tipo'],
													'nome_anexo'	=> $nome_anexo
												);

												if($SolicitacaoAbono['status'] == 3){
													$abono_reprovado_falta = $SolicitacaoAbono['motivo_reprova'];
													$abono_pendente = '<i title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-square" style="font-size:20px; color: red;"></i>';
													$abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-timer-off tippy-btn text-danger" style="font-size:20px;"></i>';
												}else{
													$abono_pendente_falta = 1;
													$abono_pendente = '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square txteldorado_5" style="font-size:20px; color: #feca07;"></i>';
													$abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Abono Pendente de Aprovação" class="mdi mdi-timer-off tippy-btn txteldorado_5" style="font-size:20px; color: #feca07;"></i>';
												}
											}
										}
									}
									// SOLICITAÇÕES ABONO ATRASO
									$array_abonos_atraso = array();
									$abono_pendente = "";
									$abono_pendente_mobile = "";
									$abono_reprovado_atraso = "";
									$abono_pendente_atraso = "";
									if ($resSolicitacaoAbonoAtraso) {
										foreach ($resSolicitacaoAbonoAtraso as $keyA => $SolicitacaoAbono) {
											if (dtBr($SolicitacaoAbono['dtponto']) == dtBr($resData[$i]['DATA']) && $SolicitacaoAbono['chapa'] == $resData[$i]['CHAPA']) {

												$nome_anexo = "";
												if(strlen(trim($SolicitacaoAbono['abono_atestado'])) > 0){
													$file = explode('|', $SolicitacaoAbono['abono_atestado']);
													$nome_anexo = $file[0];
													unset($file);
												}

												$array_abonos_atraso[] = array(
													'id'              => $SolicitacaoAbono['id'],
													'data'            => dtEn($SolicitacaoAbono['dtponto'], true),
													'inicio'          => m2h($SolicitacaoAbono['abn_horaini'], 4),
													'termino'         => m2h($SolicitacaoAbono['abn_horafim'], 4),
													'codabono'        => $SolicitacaoAbono['abn_codabono'],
													'status'          => $SolicitacaoAbono['status'],
													'nome_anexo'      => $nome_anexo,
													'just_abono_tipo' => $SolicitacaoAbono['justificativa_abono_tipo']
												);
												
												if($SolicitacaoAbono['status'] == 3){
													$$abono_reprovado_atraso = $SolicitacaoAbono['motivo_reprova'];
													$abono_pendente = '<i title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-square" style="font-size:20px; color: red;"></i>';
													$abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.$SolicitacaoAbono['motivo_reprova'].'" class="mdi mdi-timer-off tippy-btn text-danger" style="font-size:20px;"></i>';
												}else{
													$abono_pendente_atraso = 1;
													$abono_pendente = '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square txteldorado_5" style="font-size:20px; color: #feca07;"></i>';
													$abono_pendente_mobile = '<i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="Abono Pendente de Aprovação" class="mdi mdi-timer-off tippy-btn txteldorado_5" style="font-size:20px; color: #feca07;"></i>';
												}
											}
										}
									}
									// SOLICITAÇÕES ALTERA ATITUDE
									if ($resSolicitacaoAbonoAtitude) {
										foreach ($resSolicitacaoAbonoAtitude as $keyA => $SolicitacaoAbono) {
											if (dtBr($SolicitacaoAbono['dtponto']) == dtBr($resData[$i]['DATA']) && $SolicitacaoAbono['chapa'] == $resData[$i]['CHAPA']) {

												if(substr($SolicitacaoAbono['atitude_justificativa'],0,1) == 'F'){

													if($SolicitacaoAbono['status'] == 3){
														$abono_reprovado_falta = $SolicitacaoAbono['motivo_reprova'];
													}else{
														if($abono_pendente_falta == ""){
															$abono_pendente_falta = 1;
														}
													}

												}
												
												if(substr($SolicitacaoAbono['atitude_justificativa'],0,1) == 'A'){

													if($SolicitacaoAbono['status'] == 3){
														$abono_reprovado_atraso = $SolicitacaoAbono['motivo_reprova'];
													}else{
														if($abono_pendente_atraso == ""){
															$abono_pendente_atraso = 1;
														}
													}
													
												}
												
											}
										}
									}


									// $b1 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'a' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'0\')" readonly type="text" name="a' . $idbData . '" id="a' . $idbData . '" size="5" maxlength="5">';
									// $b2 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'b' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'1\')" readonly type="text" name="b' . $idbData . '" id="b' . $idbData . '" size="5" maxlength="5">';
									// $b3 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'c' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'0\')" readonly type="text" name="c' . $idbData . '" id="c' . $idbData . '" size="5" maxlength="5">';
									// $b4 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'d' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'1\')" readonly type="text" name="d' . $idbData . '" id="d' . $idbData . '" size="5" maxlength="5">';
									// $b5 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'e' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'0\')" readonly type="text" name="e' . $idbData . '" id="e' . $idbData . '" size="5" maxlength="5">';
									// $b6 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'f' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'1\')" readonly type="text" name="f' . $idbData . '" id="f' . $idbData . '" size="5" maxlength="5">';
									// $b7 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'g' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'0\')" readonly type="text" name="g' . $idbData . '" id="g' . $idbData . '" size="5" maxlength="5">';
									// $b8 = '<input data-value value="" ondblclick="abreModalInsere(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'h' . $idbData . '\', \'' . $resData[$i]['CHAPA'] . '\', \'1\')" readonly type="text" name="h' . $idbData . '" id="h' . $idbData . '" size="5" maxlength="5">';

									$b1 = '';
									$b2 = '';
									$b3 = '';
									$b4 = '';
									$b5 = '';
									$b6 = '';
									$b7 = '';
									$b8 = '';

									$status_1 = '';
									$status_2 = '';
									$status_3 = '';
									$status_4 = '';
									$status_5 = '';
									$status_6 = '';
									$status_7 = '';
									$status_8 = '';

									$motivo_reprova_1 = '';
									$motivo_reprova_2 = '';
									$motivo_reprova_3 = '';
									$motivo_reprova_4 = '';
									$motivo_reprova_5 = '';
									$motivo_reprova_6 = '';
									$motivo_reprova_7 = '';
									$motivo_reprova_8 = '';

									$array_batidas = array();
									$array_caracteres = array("+", "-");

									$coluna = 1;
									$qtde = 1;
									if (isset($resBatidas) && is_array($resBatidas) > 0) :


										if($_SESSION['log_id'] == 1){
											// echo '<pre>';
											// print_r($resBatidas);
											// echo '</pre>';
											// exit();
										}
										foreach ($resBatidas as $idb => $value) :
											
											$bat_data_ponto = $resBatidas[$idb]['DATA'];
											$bat_data = $resBatidas[$idb]['DATAREFERENCIA'];
											$bat_chapa = $resBatidas[$idb]['CHAPA'];
											$bat_natureza = $resBatidas[$idb]['NATUREZA'];
											$bat_idaafdt = $resBatidas[$idb]['IDAAFDT'];
											$bat_batida = $resBatidas[$idb]['BATIDA'];

											if($_SESSION['log_id'] == 1){
												// echo $qtde.')'.m2h($bat_batida).' - '.$bat_natureza.'<br>';
											}


											if (strlen(trim($resBatidas[$idb]['DATAREFERENCIA2'])) > 0) {
												$bat_data2 = date('d/m/Y', strtotime($resBatidas[$idb]['DATAREFERENCIA2']));
											} else {
												$bat_data2 = "Não pode ficar em branco";
											}

											if (dtEn($bat_data, true) == dtEn($resData[$i]['DATA'], true) && $bat_chapa == $resData[$i]['CHAPA']) {
												

												if ($coluna > 8) $coluna = 1;

												if ($bat_natureza == 0) {
													

													switch ($qtde) {
														case 1:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'ent1',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b1 = '<span id="a' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'a' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b1 = sprintf("%05s", m2h($bat_batida));
															$status_1 = $resBatidas[$idb]['STATUS'];
															$coluna = 2;
															break;
														case 2:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'ent2',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b3 = '<span id="c' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'c' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b3 = sprintf("%05s", m2h($bat_batida));
															$status_3 = $resBatidas[$idb]['STATUS'];
															$coluna = 3;
															break;
														case 3:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'ent2',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b3 = '<span id="c' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'c' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b5 = sprintf("%05s", m2h($bat_batida));
															$status_5 = $resBatidas[$idb]['STATUS'];
															$coluna = 4;
															break;
														case 4:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'ent3',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b5 = '<span id="e' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'e' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b5 = sprintf("%05s", m2h($bat_batida));
															$status_5 = $resBatidas[$idb]['STATUS'];
															$coluna = 5;
															break;
														case 5:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'ent3',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b5 = '<span id="e' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'e' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b5 = sprintf("%05s", m2h($bat_batida));
															$status_5 = $resBatidas[$idb]['STATUS'];
															$coluna = 6;
															break;
														case 6:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'ent4',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b7 = '<span id="g' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'g' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b7 = sprintf("%05s", m2h($bat_batida));
															$status_7 = $resBatidas[$idb]['STATUS'];
															$coluna = 8;
															break;
														case 7:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'ent4',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b7 = '<span id="g' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'g' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b7 = sprintf("%05s", m2h($bat_batida));
															$status_7 = $resBatidas[$idb]['STATUS'];
															$coluna = 8;
															break;
														case 8:
															// $b9 = '<span id="i' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'i' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b9 = sprintf("%05s", m2h($bat_batida));
															$coluna = 1;
															break;
													}
												} else {



													switch ($qtde) {
														case 1:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai1',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b2 = '<span id="b' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'b' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b2 = sprintf("%05s", m2h($bat_batida));
															$status_2 = $resBatidas[$idb]['STATUS'];
															$coluna = 2;
															break;
														case 2:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai2',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b2 = '<span id="b' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'b' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b4 = sprintf("%05s", m2h($bat_batida));
															$status_4 = $resBatidas[$idb]['STATUS'];
															$coluna = 3;
															break;
														case 3:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai2',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b4 = '<span id="d' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'d' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b6 = sprintf("%05s", m2h($bat_batida));
															$status_6 = $resBatidas[$idb]['STATUS'];
															$coluna = 5;
															break;
														case 4:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai2',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b4 = '<span id="d' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'d' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b4 = sprintf("%05s", m2h($bat_batida));
															$status_4 = $resBatidas[$idb]['STATUS'];
															break;
														case 5:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai3',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b6 = '<span id="f' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'f' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b6 = sprintf("%05s", m2h($bat_batida));
															$status_6 = $resBatidas[$idb]['STATUS'];
															$coluna = 7;
															break;
														case 6:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai3',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b6 = '<span id="f' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'f' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b6 = sprintf("%05s", m2h($bat_batida));
															$status_6 = $resBatidas[$idb]['STATUS'];
															break;
														case 7:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai4',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b8 = '<span id="h' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'h' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b8 = sprintf("%05s", m2h($bat_batida));
															$status_8 = $resBatidas[$idb]['STATUS'];
															$coluna = 8;
															break;
														case 8:
															$array_batidas[] = array(
																'batida'          => sprintf("%05s", m2h($bat_batida)),
																'batida_min'          => $bat_batida,
																'natureza'        => $bat_natureza,
																'pendente'        => 0,
																'data_ponto'		=> dtEn($bat_data_ponto, true),
																'data_referencia' => dtEn($bat_data, true),
																'status'          => $resBatidas[$idb]['STATUS'],
																'status_bat'          => $resBatidas[$idb]['STATUS'],
																'justificativa_batida'	=> $resBatidas[$idb]['JUSTIFICATIVA_BATIDA'],
																'motivo_reprova'          => '',
																'campo'            => 'sai4',
																'idaafdt'         => (int)$bat_idaafdt
															);
															// $b8 = '<span id="h' . $idbData . '" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\'' . sprintf("%05s", m2h($bat_batida)) . '\',\'' . $resData[$i]['CHAPA'] . '\', \'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\', \'h' . $idbData . '\', \'' . $bat_idaafdt . '\', \'' . $bat_natureza . '\',\'' . $bat_data2 . '\')">' . sprintf("%05s", m2h($bat_batida)) . '</span>';
															$b8 = sprintf("%05s", m2h($bat_batida));
															$status_8 = $resBatidas[$idb]['STATUS'];
															break;
													}
												}

												// unset($resBatidas[$idb]);
												$qtde++;
											}
											// unset($resBatidas[$idb]);
										endforeach;
									endif;

									if (isset($resBatidasApontadas) && is_array($resBatidasApontadas) > 0) {
										foreach ($resBatidasApontadas as $idba => $value) :
											$batapt_chapa = $resBatidasApontadas[$idba]['chapa'];
											$batapt_dtponto2 = $resBatidasApontadas[$idba]['dtponto'];
											$batapt_dtponto = $resBatidasApontadas[$idba]['data_referencia'];

											if (
												$batapt_chapa == $resData[$i]['CHAPA'] &&
												(date('d/m/Y', strtotime($batapt_dtponto)) == date('d/m/Y', strtotime($resData[$i]['DATA'])) || date('d/m/Y', strtotime($batapt_dtponto2)) == date('d/m/Y', strtotime($resData[$i]['DATA'])))
											) {
												#	[MOVIMENTO]
												#	1 - Inclusão de batida
												#	2 - Exclusão de batida
												#	3 - Altera natureza
												#	4 - Altera Jornada Referencia
												#	5 - Abonos Atrasos
												#	6 - Abonos Faltas
												#	7 - Altera Atitude

												if (strlen(trim($resBatidasApontadas[$idba]['ent1'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['ent1'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['ent1'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'ent1',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b1 = m2h($resBatidasApontadas[$idba]['ent1'],4);
													$status_1 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_1 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												if (strlen(trim($resBatidasApontadas[$idba]['ent2'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['ent2'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['ent2'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'ent2',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b3 = m2h($resBatidasApontadas[$idba]['ent2'],4);
													$status_3 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_3 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												if (strlen(trim($resBatidasApontadas[$idba]['ent3'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['ent3'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['ent3'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'ent3',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b5 = m2h($resBatidasApontadas[$idba]['ent3'],4);
													$status_5 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_5 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												if (strlen(trim($resBatidasApontadas[$idba]['ent4'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['ent4'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['ent4'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'ent4',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b7 = m2h($resBatidasApontadas[$idba]['ent4'],4);
													$status_7 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_7 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												if (strlen(trim($resBatidasApontadas[$idba]['sai1'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['sai1'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['sai1'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'sai1',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b2 = m2h($resBatidasApontadas[$idba]['sai1'],4);
													$status_2 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_2 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												if (strlen(trim($resBatidasApontadas[$idba]['sai2'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['sai2'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['sai2'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'sai2',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b4 = m2h($resBatidasApontadas[$idba]['sai2'],4);
													$status_4 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_4 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												if (strlen(trim($resBatidasApontadas[$idba]['sai3'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['sai3'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['sai3'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'sai3',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b6 = m2h($resBatidasApontadas[$idba]['sai3'],4);
													$status_6 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_6 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												if (strlen(trim($resBatidasApontadas[$idba]['sai4'])) > 0){
													$array_batidas[] = array(
														'batida'          => m2h($resBatidasApontadas[$idba]['sai4'],4),
														'batida_min'          => $resBatidasApontadas[$idba]['sai4'],
														'natureza'        => $resBatidasApontadas[$idba]['natureza'],
														'pendente'        => 1,
														'data_ponto'		=> dtEn($batapt_dtponto2, true),
														'data_referencia' => dtEn($batapt_dtponto, true),
														'status'          => 'D',
														'campo'            => 'sai4',
														'idaafdt'         => $resBatidasApontadas[$idba]['id'],
														'status_bat'         => $resBatidasApontadas[$idba]['status'],
														'motivo_reprova'         => $resBatidasApontadas[$idba]['motivo_reprova'],
														'obs'				=> $resBatidasApontadas[$idba]['obs'],
														'justificativa_batida'	=> '' // não existe na SQL $resBatidasApontadas[$idba]['JUSTIFICATIVA_BATIDA']
													);
													$b8 = m2h($resBatidasApontadas[$idba]['sai4'],4);
													$status_8 = $resBatidasApontadas[$idba]['status'];
													$motivo_reprova_8 = $resBatidasApontadas[$idba]['motivo_reprova'];
												}
												// unset($resBatidasApontadas[$idba]);
											}
										endforeach;
									}

									$b1 = '';
									$b2 = '';
									$b3 = '';
									$b4 = '';
									$b5 = '';
									$b6 = '';

									$status_1 = '';
									$status_2 = '';
									$status_3 = '';
									$status_4 = '';
									$status_5 = '';
									$status_6 = '';

									$motivo_reprova_1 = '';
									$motivo_reprova_2 = '';
									$motivo_reprova_3 = '';
									$motivo_reprova_4 = '';
									$motivo_reprova_5 = '';
									$motivo_reprova_6 = '';

									// if($_SESSION['log_id'] == 1){
									// 	echo '<pre>';
									// 	print_r($array_batidas);

									// 	asort($array_batidas);
									// 	print_r($array_batidas);
									// 	echo '</pre>';
									// }
									
										
									$coluna = 1;
									if($array_batidas){
										foreach($array_batidas as $key => $NovaBatida){

											if($_SESSION['log_id'] == 1){
												// echo 'Coluna:'.$coluna.'<br>';
											}
											
											if($NovaBatida['natureza'] == 0){

												if($b6 == '' && $b3 != '' && $b1 != ''){$b3 = $NovaBatida['batida']; $status_6 = $NovaBatida['status_bat']; $motivo_reprova_6 = $NovaBatida['motivo_reprova'];}
												if($b3 == '' && $b1 != ''){$b3 = $NovaBatida['batida']; $status_3 = $NovaBatida['status_bat']; $motivo_reprova_3 = $NovaBatida['motivo_reprova'];}
												if($b1 == ''){$b1 = $NovaBatida['batida']; $status_1 = $NovaBatida['status_bat']; $motivo_reprova_1 = $NovaBatida['motivo_reprova'];}
			
											}else{

												if($b6 == '' && $b4 != '' && $b2 != ''){$b6 = $NovaBatida['batida']; $status_6 = $NovaBatida['status_bat']; $motivo_reprova_6 = $NovaBatida['motivo_reprova'];}
												if($b4 == '' && $b2 != ''){$b4 = $NovaBatida['batida']; $status_4 = $NovaBatida['status_bat']; $motivo_reprova_4 = $NovaBatida['motivo_reprova'];}
												if($b2 == ''){$b2 = $NovaBatida['batida']; $status_2 = $NovaBatida['status_bat']; $motivo_reprova_2 = $NovaBatida['motivo_reprova'];}
							
											}
										}
									}


									$abono_pendente_rh = $resData[$i]['ABONO_PENDENTE_RH'];

									$motivo_justificativa = (strlen(trim($resData[$i]['JUSTIFICATIVA'])) > 0) ? $resData[$i]['JUSTIFICATIVA'] : 'Não justificado';

									$status_atitude = '';
									if(strlen(trim($resData[$i]['JUSTIFICATIVA_ATITUDE'])) > 0){
										$status_atitude = substr($resData[$i]['JUSTIFICATIVA_ATITUDE'],-1);
										$motivo_justificativa = substr($resData[$i]['JUSTIFICATIVA_ATITUDE'],0, -1);
									}




									$html = '<tr class="tbadmlistalin">';

									$html .= '<td class="y-mobile-cell d-none" align="center" width="100">
										<small class="text-primary d-block"><b>['.$resData[$i]['CHAPA'].'] '.$resData[$i]['NOME'].'</b></small>
										<h3 style="font-size: 20px;" class="m-0 p-0">'.date('d/m', strtotime($resData[$i]['DATA'])).'</h3><small class="text-gray d-block" style="color: #999999;">'.diaSemana($resData[$i]['DATA']).'</small>
									</td>';
									$html .= '<td class="y-mobile-cell d-none text-center align-top pl-0 pr-0 m-0" align="center">';
										if(strlen(trim($b1)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_1 == 1 || $status_1 == 2 || $status_1 == 3 || $status_1 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_1 != '') ? $motivo_reprova_1 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_1 == 'T') ? '#acffcd;' : ($status_1 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b1.'</span> <small style="color: #999999;">E1</small></h5></div>';
										if(strlen(trim($b3)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_2 == 1 || $status_2 == 2 || $status_2 == 3 || $status_2 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_2 != '') ? $motivo_reprova_2 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_2 == 'T') ? '#acffcd;' : ($status_2 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b3.'</span> <small style="color: #999999;">E2</small></h5></div>';
										if(strlen(trim($b5)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_3 == 1 || $status_3 == 2 || $status_3 == 3 || $status_3 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_3 != '') ? $motivo_reprova_3 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_3 == 'T') ? '#acffcd;' : ($status_3 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b5.'</span> <small style="color: #999999;">E3</small></h5></div>';
										if(strlen(trim($b7)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_4 == 1 || $status_4 == 2 || $status_4 == 3 || $status_4 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_4 != '') ? $motivo_reprova_4 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_4 == 'T') ? '#acffcd;' : ($status_4 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b7.'</span> <small style="color: #999999;">E4</small></h5></div>';
									$html .= '</td>';
									$html .= '<td class="y-mobile-cell d-none align-top pl-0 pr-0 m-0" align="center">';
										if(strlen(trim($b2)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_5 == 1 || $status_5 == 2 || $status_5 == 3 || $status_5 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_5 != '') ? $motivo_reprova_5 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_5 == 'T') ? '#acffcd;' : ($status_5 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b2.'</span> <small style="color: #999999;">S1</small></h5></div>';
										if(strlen(trim($b4)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_6 == 1 || $status_6 == 2 || $status_6 == 3 || $status_6 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_6 != '') ? $motivo_reprova_6 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_6 == 'T') ? '#acffcd;' : ($status_6 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b4.'</span> <small style="color: #999999;">S2</small></h5></div>';
										if(strlen(trim($b6)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_7 == 1 || $status_7 == 2 || $status_7 == 3 || $status_7 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_7 != '') ? $motivo_reprova_7 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_7 == 'T') ? '#acffcd;' : ($status_7 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b6.'</span> <small style="color: #999999;">S3</small></h5></div>';
										if(strlen(trim($b8)) > 0) $html .= '<div class="info_bat mb-1"><h5 class="p-0 m-0"><span class="text-primary" style="font-size: 16px;letter-spacing: -1px;">'.(($status_8 == 1 || $status_8 == 2 || $status_8 == 3 || $status_8 == 'T') ? ' <i data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'.(($motivo_reprova_8 != '') ? $motivo_reprova_8 : 'Pendente Aprovação').'" class="mdi mdi-square tippy-btn" style="font-size:14px; color: '.(($status_8 == 'T') ? '#acffcd;' : ($status_8 == 3 ? '#fe0200;' : '#feca07;')).'"></i> ' : '').''.$b8.'</span> <small style="color: #999999;">S4</small></h5></div>';
									$html .= '</td>';

									$html .= '<td class="y-mobile-cell d-none text-center" align="center">';
										if ($ck_semPar && ($resData[$i]['SEM_PAR_CORRESPONDENTE'])) $html .= '<button data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" type="button" class="btn pl-0 pr-0 btn-warning btn-block btn-xxs m-0 tippy-btn" title="' . $resData[$i]['SEM_PAR_CORRESPONDENTE_DESC'] . '" align="center">' . (($resData[$i]['SEM_PAR_CORRESPONDENTE']) ? 'Sem Par <i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</button>';
										if ($ck_ExtraExecutado && $vl_extra_executado > 0 && ($resData[$i]['EXTRAEXECUTADO_CASE'])) $html .= '<button type="button" class="btn pl-0 pr-0 btn-primary btn-block btn-xxs m-0" align="center">' . (($resData[$i]['EXTRAEXECUTADO_CASE']) ? 'Ex. Exec. '.m2h($resData[$i]['EXTRAEXECUTADO_CASE']) : '') . '</button>';
										if ($ck_Atrasos && ($resData[$i]['ATRASO_CASE'])) $html .= '<button type="button" class="btn pl-0 pr-0 btn-info btn-block btn-xxs m-0" align="center">' . (($resData[$i]['ATRASO_CASE']) ? '<span style="cursor: pointer;" >' . 'Atraso '.m2h($resData[$i]['ATRASO_CASE']) . '</span>' : '') . '</button>';
										if ($ck_Faltas && ($resData[$i]['FALTA_CASE'] && $resData[$i]['BATIDAS_PORTAL'] == 0)) $html .= '<button type="button" class="btn pl-0 pr-0 btn-danger btn-block btn-xxs m-0" align="center">' . (($resData[$i]['FALTA_CASE'] && $resData[$i]['BATIDAS_PORTAL'] == 0) ? '<span style="cursor: pointer;" >Falta ' . m2h($resData[$i]['FALTA_CASE']) . '</span>' : '') . '</button>';
										if ($ck_jorMaior10 && ($resData[$i]['JORNADA_MAIOR_10HORAS'])) $html .= '<button type="button" class="btn pl-0 pr-0 btn-success btn-block btn-xxs m-0" title="' . $resData[$i]['JORNADA_MAIOR_10HORAS_DESC'] . '" align="center">' . (($resData[$i]['JORNADA_MAIOR_10HORAS']) ? 'Jorn. > 10h <i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</button>';
										if ($ck_jorMaior12 && ($resData[$i]['JORNADA_MAIOR_12HORAS'])) $html .= '<button type="button" class="btn pl-0 pr-0 btn-success btn-block btn-xxs m-0" title="' . $resData[$i]['JORNADA_MAIOR_12HORAS_DESC'] . '" align="center">' . (($resData[$i]['JORNADA_MAIOR_12HORAS']) ? 'Jorn. > 12h <i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</button>';
										if ($ck_interjornada && ($resData[$i]['INTERJORNADA'])) $html .= '<button type="button" class="btn pl-0 pr-0 btn-dark btn-block btn-xxs m-0" title="' . $resData[$i]['INTERJORNADA_DESC'] . '" align="center">' . (($resData[$i]['INTERJORNADA']) ? 'Interjornada <i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</button>';
									$html .= '</td>';
									
									$html .= '<td class="n-mobile-cell" align="center">' . $nLinha . '</td>';
									$html .= '<td class="n-mobile-cell" align="center">' . $resData[$i]['CHAPA'] . '</td>';
									// $html .= '<td class="n-mobile-cell" align="center">' . sprintf("%02s", $resData[$i]['CODFILIAL']) . '</td>';
									$html .= '<td class="n-mobile-cell">' . $resData[$i]['NOME'] . '</td>';
									$html .= '<td class="n-mobile-cell">' . $resData[$i]['CODSITUACAO'] . '</td>';
									$html .= '<td class="n-mobile-cell" align="center">' . diaSemana($resData[$i]['DATA'], true) . '</td>';

									$html .= '<td class="n-mobile-cell" align="center">' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '</td>';

									$b1_tipo = (strlen(trim($b1)) > 0) ? 'D' : '';
									$b2_tipo = (strlen(trim($b2)) > 0) ? 'D' : '';
									$b3_tipo = (strlen(trim($b3)) > 0) ? 'D' : '';
									$b4_tipo = (strlen(trim($b4)) > 0) ? 'D' : '';
									$b5_tipo = (strlen(trim($b5)) > 0) ? 'D' : '';
									$b6_tipo = (strlen(trim($b6)) > 0) ? 'D' : '';

									$html .= '<td '.(($status_1 == 1 || $status_1 == 2) ? ' style="background: #feca07;" ' : '').' '.(($status_1 == 3) ? ' style="background: #f4811f;" title="'.$motivo_reprova_1.'" ' : '').' '.(($status_1 == 'T') ? ' style="background: #dbdcdd;" title="Aguardando Aprovação RH" ' : '').' class="n-mobile-cell" align="center">' . $b1 . ' <span class="badge badge-light">'.(($status_1 != 'C') ? $b1_tipo : 'C').'</span></td>';
									$html .= '<td '.(($status_2 == 1 || $status_2 == 2) ? ' style="background: #feca07;" ' : '').' '.(($status_2 == 3) ? ' style="background: #f4811f;" title="'.$motivo_reprova_2.'" ' : '').' '.(($status_2 == 'T') ? ' style="background: #dbdcdd;" title="Aguardando Aprovação RH" ' : '').' class="n-mobile-cell" align="center">' . $b2 . ' <span class="badge badge-light">'.(($status_2 != 'C') ? $b2_tipo : 'C').'</span></td>';
									$html .= '<td '.(($status_3 == 1 || $status_3 == 2) ? ' style="background: #feca07;" ' : '').' '.(($status_3 == 3) ? ' style="background: #f4811f;" title="'.$motivo_reprova_3.'" ' : '').' '.(($status_3 == 'T') ? ' style="background: #dbdcdd;" title="Aguardando Aprovação RH" ' : '').' class="n-mobile-cell" align="center">' . $b3 . ' <span class="badge badge-light">'.(($status_3 != 'C') ? $b3_tipo : 'C').'</span></td>';
									$html .= '<td '.(($status_4 == 1 || $status_4 == 2) ? ' style="background: #feca07;" ' : '').' '.(($status_4 == 3) ? ' style="background: #f4811f;" title="'.$motivo_reprova_4.'" ' : '').' '.(($status_4 == 'T') ? ' style="background: #dbdcdd;" title="Aguardando Aprovação RH" ' : '').' class="n-mobile-cell" align="center">' . $b4 . ' <span class="badge badge-light">'.(($status_4 != 'C') ? $b4_tipo : 'C').'</span></td>';
									$html .= '<td '.(($status_5 == 1 || $status_5 == 2) ? ' style="background: #feca07;" ' : '').' '.(($status_5 == 3) ? ' style="background: #f4811f;" title="'.$motivo_reprova_5.'" ' : '').' '.(($status_5 == 'T') ? ' style="background: #dbdcdd;" title="Aguardando Aprovação RH" ' : '').' class="n-mobile-cell" align="center">' . $b5 . ' <span class="badge badge-light">'.(($status_5 != 'C') ? $b5_tipo : 'C').'</span></td>';
									$html .= '<td '.(($status_6 == 1 || $status_6 == 2) ? ' style="background: #feca07;" ' : '').' '.(($status_6 == 3) ? ' style="background: #f4811f;" title="'.$motivo_reprova_6.'" ' : '').' '.(($status_6 == 'T') ? ' style="background: #dbdcdd;" title="Aguardando Aprovação RH" ' : '').' class="n-mobile-cell" align="center">' . $b6 . ' <span class="badge badge-light">'.(($status_6 != 'C') ? $b6_tipo : 'C').'</span></td>';
									
									
									unset($b1,$b2,$b3,$b4,$b5,$b6,$b7,$b8);
									

									if ($ck_semPar)
										$html .= '<td class="n-mobile-cell" title="' . $resData[$i]['SEM_PAR_CORRESPONDENTE_DESC'] . '" align="center">' . (($resData[$i]['SEM_PAR_CORRESPONDENTE']) ? '<i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</td>';
									if ($ck_ExtraExecutado && $vl_extra_executado > 0)
										$html .= '<td class="n-mobile-cell" align="center">' . (($resData[$i]['EXTRAEXECUTADO_CASE']) ? m2h($resData[$i]['EXTRAEXECUTADO_CASE']) : '') . '</td>';
									if ($ck_Atrasos) {
                    if ($ck_todos and $resData[$i]['SEM_PAR_CORRESPONDENTE'])  { 
                      $html .= '<td class="n-mobile-cell" align="center"> </td>';
                    } else {
                      $html .= '<td class="n-mobile-cell" align="center" '.(($abono_pendente_rh > 0 && $resData[$i]['ATRASO_CASE']) ? ' title="Aguardando Aprovação RH" style="background:#dbdcdd;" ':'').' '.(($abono_pendente_atraso == 1) ? ' title="Aguardando Aprovação" style="background:#feca07;"  ' : '').' '.(($abono_reprovado_atraso != '') ? ' title="'.$abono_reprovado_atraso.'" style="background:#f4811f;" class="txteldorado_2" ' : '').'>' . (($resData[$i]['ATRASO_CASE']) ? m2h($resData[$i]['ATRASO_CASE']) : '') . '</td>';
                    }
                  }
									if ($ck_Faltas) {
                    if ($ck_todos and $resData[$i]['SEM_PAR_CORRESPONDENTE'])  { 
                      $html .= '<td class="n-mobile-cell" align="center"> </td>';
                    } else {
										  $html .= '<td class="n-mobile-cell" align="center" '.(($abono_pendente_rh > 0 && $resData[$i]['FALTA_CASE']) ? ' title="Aguardando Aprovação RH" style="background:#dbdcdd;" ':'').' '.(($abono_pendente_falta == 1 || $status_atitude == 1) ? ' title="Aguardando Aprovação" style="background:#feca07;"  ' : '').' '.(($abono_reprovado_falta != '' || $status_atitude == 3) ? ' title="'.$abono_reprovado_falta.'" style="background:#f4811f;"  class="txteldorado_2"' : '').'>' . (($resData[$i]['FALTA_CASE']) ? m2h($resData[$i]['FALTA_CASE']) : '') . '</td>';
                    }
                  }
                  if ($ck_jorMaior10)
										$html .= '<td class="n-mobile-cell" title="' . $resData[$i]['JORNADA_MAIOR_10HORAS_DESC'] . '" align="center">' . (($resData[$i]['JORNADA_MAIOR_10HORAS']) ? '<i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</td>';
                  if ($ck_jorMaior12)
										$html .= '<td class="n-mobile-cell" title="' . $resData[$i]['JORNADA_MAIOR_12HORAS_DESC'] . '" align="center">' . (($resData[$i]['JORNADA_MAIOR_12HORAS']) ? '<i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</td>';
									if ($ck_interjornada)
										$html .= '<td class="n-mobile-cell" title="' . $resData[$i]['INTERJORNADA_DESC'] . '" align="center">' . (($resData[$i]['INTERJORNADA']) ? '<i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '') . '</td>';

									
									
									if(count($array_abonos_falta) > 0 || $resData[$i]['QTDE_ABONO'] > 0 || count($array_abonos_atraso) > 0 ){
										$motivo_justificativa = "ABONO SOLICITADO";
									}

									
									
									$html .= '<td class="text-center n-mobile-cell">'.$motivo_justificativa.'</td>';

									$html .= '<td width="10" align="center" class="text-center">';
										if(!$periodo_bloqueado){
											$html .= '<div class="btn-group dropleft mb-2 mb-md-0">';
												// if (!(($DiasEspelho['AFASTAMENTO'] > 0) || ($DiasEspelho['FERIAS'] > 0))){
													$html .= '<button type="button" class="btn btn-soft-primary dropdown-toggle pl-1 pr-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="mdi mdi-dots-vertical"></i></button>';
													$html .= '<div class="dropdown-menu">';
														
														
														$escala         = explode(' ', $resData[$i]['ESCALA']);
														$inicioEscala   = $escala[0];
														$terminoEscala  = end($escala);

                            if ($motivo_justificativa == 'Não justificado') {
														  $html .= '<button onclick="abrirAlteracaoBatida(\''.dtEn($resData[$i]['DATA'], true).'\', \''.dtBr($resData[$i]['DATA']).'\', \''.diaSemana($resData[$i]['DATA']).'\', \''.urlencode(json_encode($array_batidas)).'\', \''.$resData[$i]['CHAPA'].'\', \''.$resData[$i]['ESCALA'].'\', \''.$inicioEscala.'\', \''.$terminoEscala.'\', \''.$resData[$i]['CPF'].'\', \''.(int)$resData[$i]['IS_MOTORISTA'].'\')" type="button" class="dropdown-item" title="Alterar registro"><i class="mdi mdi-pencil-outline"></i> Incluir/Alterar registro</button>';
                            } else {
                              $html .= '<button type="button" class="dropdown-item disabled" title="Incluir/Alterar registro" disabled><i class="mdi mdi-lock-alert" disabled ></i> Incluir/Alterar registro</button>';
                            }

														if ($ck_Atrasos && ($resData[$i]['ATRASO_CASE'])){

                              if ($motivo_justificativa == 'Não justificado') {
															  $html .= '<button onclick="abrirSolicitacaoAbono(\''.dtEn($resData[$i]['DATA'], true).'\', \''.dtBr($resData[$i]['DATA']).'\', \''.diaSemana($resData[$i]['DATA']).'\', \''.urlencode(json_encode($array_abonos_atraso)).'\', 5, \''.$resData[$i]['CHAPA'].'\', \''.$resData[$i]['ESCALA'].'\', \''.urlencode(json_encode($array_batidas)).'\', \''.(($periodo_bloqueado) ? 1 : 0).'\', \''.$inicioEscala.'\', \''.$terminoEscala.'\', \'0:00\', \''.m2h($resData[$i]['ATRASO_CASE']).'\')" type="button" class="dropdown-item" title="Solicitar Abono Atraso"><i class="mdi mdi-av-timer"></i> Solicitar Abono Atraso</button>';
                              } else {
                                $html .= '<button type="button" class="dropdown-item disabled" title="Solicitar Abono Atraso" disabled><i class="mdi mdi-lock-alert" disabled ></i> Solicitar Abono Atraso</button>';
                              }

															$html .= '<button onclick="abrirAlteraAtitude(\''.dtEn($resData[$i]['DATA'], true).'\', \''.dtBr($resData[$i]['DATA']).'\', \''.diaSemana($resData[$i]['DATA']).'\', \''.$resData[$i]['CHAPA'].'\', \''.$resData[$i]['ESCALA'].'\', \''.urlencode(json_encode($array_batidas)).'\', \'A\', \''.$resData[$i]['USABANCOHORAS'].'\')" type="button" class="dropdown-item" title="Altera Atitude Atraso"><i class="mdi mdi-calendar-question"></i> Altera Atitude Atraso</button>';
															// $html .= '<button onclick="InsereAbonoAtraso(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\',\'' . $resData[$i]['CHAPA'] . '\',\'' . m2h($resData[$i]['FALTA_CASE']) . '\', \'' . $resData[$i]['NOME'] . '\', \'\', \'' . $resData[$i]['CODFILIAL'] . '\')" type="button" class="dropdown-item" title="Justificativa de exceção"><i class="mdi mdi-calendar-star"></i> Justificativa de exceção</button>';
														}

														if ($ck_Faltas && ($resData[$i]['FALTA_CASE'] && $resData[$i]['BATIDAS_PORTAL'] == 0)){
                              if ($motivo_justificativa == 'Não justificado') {
                                $html .= '<button onclick="abrirSolicitacaoAbono(\''.dtEn($resData[$i]['DATA'], true).'\', \''.dtBr($resData[$i]['DATA']).'\', \''.diaSemana($resData[$i]['DATA']).'\', \''.urlencode(json_encode($array_abonos_falta)).'\', 6, \''.$resData[$i]['CHAPA'].'\', \''.$resData[$i]['ESCALA'].'\', \''.urlencode(json_encode($array_batidas)).'\', \''.(($periodo_bloqueado) ? 1 : 0).'\', \''.$inicioEscala.'\', \''.$terminoEscala.'\', \''.m2h($resData[$i]['FALTA_CASE']).'\', \'0:00\')" type="button" class="dropdown-item" title="Solicitar Abono Falta"><i class="mdi mdi-timer-off"></i> Solicitar Abono Falta</button>';
                              } else {
                                $html .= '<button type="button" class="dropdown-item disabled" title="Solicitar Abono Falta" disabled><i class="mdi mdi-lock-alert" disabled ></i> Solicitar Abono Falta</button>';
                              }

															if($status_atitude != 'S') $html .= '<button onclick="abrirAlteraAtitude(\''.dtEn($resData[$i]['DATA'], true).'\', \''.dtBr($resData[$i]['DATA']).'\', \''.diaSemana($resData[$i]['DATA']).'\', \''.$resData[$i]['CHAPA'].'\', \''.$resData[$i]['ESCALA'].'\', \''.urlencode(json_encode($array_batidas)).'\', \'F\', \''.$resData[$i]['USABANCOHORAS'].'\')" type="button" class="dropdown-item" title="Altera Atitude Falta"><i class="mdi mdi-calendar-question"></i> Altera Atitude Falta</button>';
															// $html .= '<button onclick="InsereAbonoFalta(\'' . date('d/m/Y', strtotime($resData[$i]['DATA'])) . '\',\'' . $resData[$i]['CHAPA'] . '\',\'' . m2h($resData[$i]['FALTA_CASE']) . '\', \'' . $resData[$i]['NOME'] . '\', \'\', \'' . $resData[$i]['CODFILIAL'] . '\')" type="button" class="dropdown-item" title="Falta"><i class="mdi mdi-calendar-question"></i> Altera Atitude/Justificativa</button>';
														}

														if ($ck_ExtraExecutado && (int)$resData[$i]['EXTRAEXECUTADO_CASE'] > 0){
															$html .= '<button onclick="abrirJustificativaExtra(\''.dtEn($resData[$i]['DATA'], true).'\', \''.dtBr($resData[$i]['DATA']).'\', \''.diaSemana($resData[$i]['DATA']).'\', \''.$resData[$i]['JUSTIFICATIVA_CODIGO'].'\', \''.$resData[$i]['CHAPA'].'\', \''.$resData[$i]['ESCALA'].'\', \''.urlencode(json_encode($array_batidas)).'\')" type="button" class="dropdown-item" title="Justificativa de Extra"><i class="mdi mdi-comment-check-outline"></i> Justificativa de Extra</button>';
														}
														
													$html .= '</div>';
												// }
											$html .= '</div>';
										}else{
											$html .= '<i title="Periodo bloqueado" class="mdi mdi-lock-alert" disabled style="font-size:20px;color:#999999;"></i>';
										}
									$html .= '</td>';

									$html .= '</tr>
									';

									$nLinha++;
									echo $html;
									unset($html);
									unset($resData[$i], $value);
								}

								//cho $html;
								unset($resDados);
							}
							?>
						</tbody>
					</table>
				</div>


			</div>
		</div>
	</div>
</div>

</div>




<!-- modal -->
<div class="modal" id="modalAdiciona" tabindex="1" role="dialog" aria-labelledby="modalAdiciona" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="modalAdicionalabel"><span class="oi oi-people"></span> Inserir Registro </h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body">


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addData" style="width: 128px;">Data: </label>
					</div>
					<input type="text" id="addData" name="addData" value="" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
				</div>


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addDataReferencia">Data Referencia: </label>
					</div>
					<input type="text" id="addDataReferencia" name="addDataReferencia" value="" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
				</div>


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addNatureza" style="width: 128px;">Natureza: </label>
					</div>
					<select class="custom-select" id="addNatureza" name="addNatureza">
						<option selected value=""></option>
						<option value="0">Entrada</option>
						<option value="1">Saída</option>
					</select>
				</div>


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" style="width: 128px;" for="addBatida">Registro: </label>
					</div>
					<input type="text" id="addBatida" name="addBatida" value="" mask-batida style="border:1px solid #d6d6d6;" maxlength="4">
				</div>


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="addJustificativa" style="width: 128px;">Justificativa: </label>
					</div>
					<select class="custom-select" id="addJustificativa" name="addJustificativa">
						<option selected value=""></option>
						<option value="Servico externo">Serviço externo</option>
						<option value="Esquecimento">Esquecimento</option>
						<option value="Problema no ponto">Problema no ponto</option>
						<option value="Outros">Outros</option>
					</select>


				</div>
				<div class="input-group mb-3">
					<input type="text" class="form-control" name="textoJustOutros" id="textoJustOutros" style="display: none;" placeholder="Digite a justificativa..." />
				</div>

				<input type="hidden" id="addLocalizacao" name="addLocalizacao" value="">
				<input type="hidden" id="addChapa" name="addChapa" value="">



			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
				<button type="button" class="btn btn-success" onclick="return AdicionarBatida();"> <i class="fa fa-check"></i> Confirmar </button>
			</div>
		</div>
	</div>


</div>

<!-- modal -->


<!-- modal -->
<div class="modal" id="modalAltera" tabindex="1" role="dialog" aria-labelledby="modalAltera" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="modalAlteralabel"><span class="oi oi-people"></span> Alterar Registro </h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body">


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" style="width: 128px;">Data: </label>
					</div>
					<input type="text" id="alteraData" name="alteraData" value="" style="background-color: #e0e0e0;  border:1px solid #d6d6d6;" readonly>
				</div>


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" style="width: 128px;">Data Referencia: </label>
					</div>
					<input type="text" id="alteraDataReferencia" name="alteraDataReferencia" value="" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
				</div>


				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" style="width: 128px;">Hora: </label>
					</div>
					<input type="text" id="alteraBatida" name="alteraBatida" value="" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
				</div>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text" for="alteraAcao" style="width: 128px;">Ação: </label>
					</div>
					<select class="custom-select" id="alteraAcao" name="alteraAcao">
						<option selected value=""></option>
						<option value="AN">Alterar natureza</option>
						<option value="EB">Excluir registro</option>
						<option value="AR">Alternar Jornada de Referência</option>
					</select>
				</div>

				<div class="input-group mb-3">
					<input type="text" class="form-control" name="textoJustExcluir" id="textoJustExcluir" style="display: none;" placeholder="Digite a justificativa..." />
					<input type="text" class="form-control" name="textoJustAlteraJornada" id="textoJustAlteraJornada" mask-data style="display: none;" placeholder="Digite a data referência..." />
				</div>

				<div class="input-group mb-3">
					<input hidden type="text" class="form-control" id="alteraNatureza" name="alteraNatureza" value="">
				</div>


				<input hidden type="text" id="alteraIDAAFDT" name="alteraIDAAFDT" value="">
				<input hidden type="text" id="alteraLocalizacao" name="alteraLocalizacao" value="">
				<input hidden type="text" id="alteraChapa" name="alteraChapa" value="">
			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
				<button type="button" class="btn btn-success" onclick="return EditarBatida();"><i class="fa fa-check"></i>Confirmar </button>
			</div>
		</div>
	</div>


</div>

<!-- modal -->

<!-- modal -->
<div class="modal" id="modalAbonoFalta" tabindex="1" role="dialog" aria-labelledby="modalAbonoFalta" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="modalAbonoFaltaLabel"><span class="oi oi-people"></span> Abono de falta </h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body">

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<label class="input-group-text">Colaborador: </label>
					</div>
					<input type="text" id="abonoFaltaNome" name="abonoFaltaNome" value="" class="form-control" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
				</div>

				<form method="POST" id="formulario_abono" enctype="multipart/form-data">

					<input type="hidden" name="codfilial_lancamento" id="codfilial_lancamento">

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" style="width: 103px;">Data: </label>
						</div>
						<input type="text" id="abonoFaltaData" name="abonoFaltaData" value="" class="form-control" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" for="abonoFaltaAcao" style="width: 103px;">Ação: </label>
						</div>
						<select class="custom-select" id="abonoFaltaAcao" name="abonoFaltaAcao" data-chapa="" data-date="" data-nome="" data-falta="" data-atraso="" data-codfilial="">
							<option selected value=""></option>
							<!--<option value="1">Lançar Abonos</option>-->
							<option value="2">Justificativa</option>
							<option value="3">Altera Atitude</option>
						</select>
					</div>




					<div class="row linha_escolha_justificativa" style="display: none;">
						<div class="col-12">
							<div>
								<table width="100%" class="table table-striped table-sm table-bordered">
									<tr>
										<td bgcolor="#F0F0F0" width="150"><b>Data</b></td>
										<td bgcolor="#F0F0F0"><b>Horário</b></td>
										<td bgcolor="#F0F0F0"><b>Horas</b></td>
									</tr>
									<tbody class="dados_abn_falta">



									</tbody>
								</table>
							</div>


						</div>


					</div>


					<div class="row linha_escolha_justificativa" style="display: none;">
						<div class="col-12">

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<label class="input-group-text"><b>Anexar Atestado:</b> </label>
								</div>
								<input type="file" name="arquivo" id="arquivo" class="form-control">
							</div>
						</div>


					</div>

				</form>

				<div class="input-group mb-3">
					<input type="text" class="form-control" name="textoJustOutros" id="textoJustOutros" style="display: none;" placeholder="Digite a justificativa..." />
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
				<button type="button" class="btn btn-success" onclick="return ConfirmaAbonoFalta();"> <i class="fa fa-check"></i> Confirmar </button>
			</div>
		</div>
	</div>


</div>
<!-- modal -->


<!-- modal -->
<div class="modal" id="modalAbonoAtraso" tabindex="1" role="dialog" aria-labelledby="modalAbonoAtraso" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title text-center" id="modalAbonoAtrasoLabel"><span class="oi oi-people"></span> Abono de atraso </h5>

				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span class="fa fa-times"></span>
				</button>
			</div>
			<div class="modal-body">

				<form method="POST" id="formulario_atraso" enctype="multipart/form-data">

					<input type="hidden" name="codfilial_lancamento" id="codfilial_atraso">

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text">Colaborador: </label>
						</div>
						<input type="text" id="abonoAtrasoNome" name="abonoAtrasoNome" value="" class="form-control" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" style="width: 103px;">Data: </label>
						</div>
						<input type="text" id="abonoAtrasoData" name="abonoAtrasoData" class="form-control" value="" style="background-color: #e0e0e0; border:1px solid #d6d6d6;" readonly>
					</div>

					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" for="abonoAtrasoAcao" style="width: 103px;">Ação: </label>
						</div>
						<select class="custom-select" id="abonoAtrasoAcao" name="abonoAtrasoAcao" data-chapa="" data-date="" data-nome="" data-falta="" data-atraso="" data-codfilial="">
							<option selected value=""></option>
							<!--<option value="1">Lançar Abonos</option>-->
							<option value="2">Justificativa de exceção</option>
						</select>
					</div>

					<div class="row linha_escolha_justificativa" style="display: none;">
						<div class="col-12">
							<div class="table-responsive">
								<table width="100%" class="table table-striped table-bordered">
								<tr>
										<td bgcolor="#F0F0F0" width="150"><b>Data</b></td>
										<td bgcolor="#F0F0F0"><b>Horário</b></td>
										<td bgcolor="#F0F0F0"><b>Horas</b></td>
									</tr>
									<tbody class="dados_abn_atraso">



									</tbody>
								</table>
							</div>


						</div>


					</div>


					<div class="row linha_escolha_justificativa" style="display: none;">
						<div class="col-12">

							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<label class="input-group-text"><b>Anexar Atestado:</b> </label>
								</div>
								<input type="file" name="arquivo" id="arquivo_atraso" class="form-control">
							</div>
						</div>


					</div>

					<div class="input-group mb-3">

						<input type="text" class="form-control" name="textoJustOutros" id="textoJustOutros" style="display: none;" placeholder="Digite a justificativa..." />
					</div>

				</form>


			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal"> Cancelar </button>
				<button type="button" class="btn btn-success" onclick="return ConfirmaAbonoAtraso();"> <i class="fa fa-check"></i> Confirmar </button>
			</div>
		</div>
	</div>


</div>
<!-- modal -->


<script>
	$(document).ready(function() {

		$('[mask-batida]').mask("99:99");
		$('[mask-data]').mask("99/99/9999");

		$('#tb-ponto-critica').DataTable({
			"aLengthMenu": [
				[25, 50, 100, 200, -1],
				[25, 50, 100, 200, "Todos"]
			],
			"iDisplayLength": 25,
			"aaSorting": [
				[4, "desc"]
			],
			"fixedHeader": true
		});
	});

	$('#abonoAtrasoAcao').on('change', function() {

		const valor = $(this).val();

		const chapa = $(this).attr('data-chapa');
		const data = $(this).attr('data-date');
		const nome = $(this).attr('data-nome');
		const falta = $(this).attr('data-falta');
		const atraso = $(this).attr('data-atraso');
		const codfilial = $(this).attr('data-codfilial');

		$('#codfilial_atraso').val(codfilial);
		$('.linha_escolha_justificativa').hide();


		//ABONO
		if (valor == '1') {
			$('.descricao_tipo_lancamento').html('Tipo abono');
		} //JUSTIFICATIVA
		else if (valor == '2') {
			$('.descricao_tipo_lancamento').html('Tipo Justificativa');
		}

		if (valor == '1' || valor == '2') {
			$.ajax({
				url: "<?= base_url('ponto/critica/BuscarAbonos') ?>",
				type: 'POST',
				data: {
					chapa: chapa,
					data: data,
					dataref: data,
					dtapt: data,
					tipo: "'A','SA','SI'",
					ft_tipo: 'ATRASO'

				},
				success: function(result) {
					var RETORNO = result.split('@');

					if (valor == '1') $(".dados_abn_atraso").html(RETORNO[0] + '' + RETORNO[3]);
					if (valor == '2') $(".dados_abn_atraso").html(RETORNO[1] + '' + RETORNO[3]);
					$("[data-hora]").mask('99:99');

					$('.linha_escolha_justificativa').show();
				}
			});

		}



	});

	$('#abonoFaltaAcao').on('change', function() {
		const valor = $(this).val();

		const chapa = $(this).attr('data-chapa');
		const data = $(this).attr('data-date');
		const nome = $(this).attr('data-nome');
		const falta = $(this).attr('data-falta');
		const atraso = $(this).attr('data-atraso');
		const codfilial = $(this).attr('data-codfilial');

		$('#codfilial_lancamento').val(codfilial);
		$('.linha_escolha_justificativa').hide();


		//ABONO
		if (valor == '1') {
			$('.descricao_tipo_lancamento').html('Tipo abono');
		} //JUSTIFICATIVA
		else if (valor == '2') {
			$('.descricao_tipo_lancamento').html('Tipo Justificativa');
		} 
		else if (valor == '3') {
			$('.descricao_tipo_lancamento').html('Altera Atitude');
		}

		if (valor == '1' || valor == '2' || valor == '3') {



			$.ajax({
				url: "<?= base_url('ponto/critica/BuscarAbonos') ?>",
				type: 'POST',
				data: {
					chapa: chapa,
					data: data,
					dataref: data,
					dtapt: data,
					tipo: "'F'",
					ft_tipo: 'FALTA'

				},
				success: function(result) {
					var RETORNO = result.split('@');

					console.log(RETORNO);
					if (valor == '1') $(".dados_abn_falta").html(RETORNO[0] + '' + RETORNO[3]);
					if (valor == '2') $(".dados_abn_falta").html(RETORNO[1] + '' + RETORNO[3]);
					if (valor == '3') $(".dados_abn_falta").html(RETORNO[2] + '' + RETORNO[3]);
					$("[data-hora]").mask('99:99');

					$('.linha_escolha_justificativa').show();
				}
			});

		}
	})

	function h2m(hora) {
		var horas = hora.split(':');
		var m = parseFloat(horas[1]) / parseFloat(60);
		var m = parseFloat(horas[0]) + parseFloat(m);
		return (m * 60);
	}

	function m2h(hora) {
		return transforma_magicamente(hora * 60);
	}

	function duas_casas(numero) {
		if (numero <= 9) {
			numero = "0" + numero;
		}
		return numero;
	}

	function transforma_magicamente(s) {

		if (s < 3600) {
			hora = duas_casas(0);
		} else {
			hora = duas_casas(Math.floor(s / 3600, 1));
		}
		minuto = duas_casas(Math.round((s % 3600) / 60));
		formatado = hora + ":" + minuto;
		return formatado;
	}




	function calcularNovoHorario(elemento) {
		
		var COD = $(elemento).attr('data-cod');
		
		var INICIO 	= $("input[data-inicio='" + COD + "']").val();
		var INICIOH = $("input[data-inicioh='" + COD + "']").val();
		var FIM 	= $("input[data-fim='" + COD + "']").val();
		var FIMH 	= $("input[data-fimh='" + COD + "']").val();
		var LIMITE 	= $("input[data-limite='" + COD + "']").val();

		var INICIO = h2m(INICIO);
		var FIM = h2m(FIM);

		// ********************************************************
		// CONTA QTDE DE CARACTERES				
		// ********************************************************
		if ($("input[data-inicio='" + COD + "']").val().length != 5) {
			alert('Hora início não pode ficar em branco.');
			$("input[data-inicio='" + COD + "']").val(INICIOH);
			return false;
		}
		if ($("input[data-fim='" + COD + "']").val().length != 5) {
			alert('Hora fim não pode ficar em branco.');
			$("input[data-fim='" + COD + "']").val(FIMH);
			return false;
		}
		// ********************************************************

		var TOTAL = parseFloat(FIM) - parseFloat(INICIO);
		if (TOTAL > LIMITE || TOTAL <= 0 || INICIO == '' || FIM == '') {
			alert('Quantidade de horas digita é maior doque limite encontrado.');
			$("input[data-inicio='" + COD + "']").val(INICIOH);
			$("input[data-fim='" + COD + "']").val(FIMH);
		} else {

			$("[data-resultado='" + COD + "']").html(m2h(TOTAL));

		}


	}

	function removeAbono(id) {

		<?php if($periodo_bloqueado ?? true):?>return false;<?php endif; ?>

			openLoading();

		try {

			$.ajax({
				url: "<?= base_url('ponto/critica/action/excluir_lancamento') ?>",
				type: 'POST',
				data: {
					id: id
				},
				success: function(result) {
					openLoading(true);
					try {

						var response = JSON.parse(result);

						if (response.tipo == "success") {
							exibeAlerta(response.tipo, response.msg, 6);
							Filtro();
						} else {
							exibeAlerta(response.tipo, response.msg);
						}

					} catch (e) {
						exibeAlerta('error', 'Erro interno: ' + e);
					}
				}
			})

		} catch (e) {
			exibeAlerta('error', 'Erro interno: ' + e);
		}
	}

	//****************************************************
	//******Modal
	//****************************************************

	function InsereAbonoFalta(data, chapa, falta, nome, atraso, codfilial) {
		<?php if($periodo_bloqueado):?>return false;<?php endif; ?>

		$(".dados_abn_falta").html('');
		$('.linha_escolha_justificativa').hide();
		$('#abonoFaltaAcao').val('');

		$("#abonoFaltaNome").val(nome);
		$("#abonoFaltaData").val(data);

		$('#abonoFaltaAcao').attr('data-chapa', chapa);
		$('#abonoFaltaAcao').attr('data-date', data);
		$('#abonoFaltaAcao').attr('data-falta', falta);
		$('#abonoFaltaAcao').attr('data-nome', nome);
		$('#abonoFaltaAcao').attr('data-atraso', atraso);
		$('#abonoFaltaAcao').attr('data-codfilial', codfilial);



		$("#modalAbonoFalta").modal();
	}

	function InsereAbonoAtraso(data, chapa, falta, nome, atraso, codfilial) {
		<?php if($periodo_bloqueado):?>return false;<?php endif; ?>

		$(".dados_abn_atraso").html('');
		$('.linha_escolha_justificativa').hide();
		$('#abonoAtrasoAcao').val('');

		$("#abonoAtrasoNome").val(nome);
		$("#abonoAtrasoData").val(data);
		$('#abonoAtrasoAcao').attr('data-chapa', chapa);
		$('#abonoAtrasoAcao').attr('data-date', data);
		$('#abonoAtrasoAcao').attr('data-falta', falta);
		$('#abonoAtrasoAcao').attr('data-nome', nome);
		$('#abonoAtrasoAcao').attr('data-atraso', atraso);
		$('#abonoAtrasoAcao').attr('data-codfilial', codfilial);

		$("#modalAbonoAtraso").modal();
	}

	function abreModalAltera($a, $batida, $chapa, $data, $localizacao, $idaafdt, $natureza, $dataref) {
		<?php if($periodo_bloqueado):?>return false;<?php endif; ?>
		$("#alteraData").val($data);
		$("#alteraDataReferencia").val($dataref);
		$("#alteraBatida").val($batida);
		$("#alteraLocalizacao").val($localizacao);
		$("#alteraIDAAFDT").val($idaafdt);
		$("#alteraNatureza").val($natureza);
		$("#alteraChapa").val($chapa);


		$("#modalAltera").modal();
	}

	function abreModalInsere(data, localizacao, chapa, tipo) {
		<?php if($periodo_bloqueado):?>return false;<?php endif; ?>

		//preenche modal
		$("#addNatureza").val(tipo);
		$("#addData").val(data);
		$("#addDataReferencia").val(data);
		$("#addLocalizacao").val(localizacao);
		$("#addChapa").val(chapa);

		console.log(data);
		console.log(localizacao);
		console.log(chapa);
		console.log(tipo);


		//abre modal
		$("#modalAdiciona").modal();
	}

	function ConfirmaAbonoFalta() {
		<?php if($periodo_bloqueado):?>return false;<?php endif; ?>

		var Formulario = new FormData($('#formulario_abono')[0]);
		var anexoObrigatorio = 0;

		$('[name="codabono[]"]').each(function() {
			if ($(this).find('option:selected').attr('data-obrigatorio') == 1)
				anexoObrigatorio = 1;
		})

		var arquivo = $('#arquivo').val();

		if (arquivo == '' && anexoObrigatorio == 1) {
			exibeAlerta("error", "É obrigatório um anexo.");
			return false;
		}

		openLoading();

		$.ajax({
			url: "<?= base_url('ponto/critica/action/lancar_abono_falta') ?>",
			type: 'POST',
			processData: false,
			contentType: false,
			data: Formulario,

			success: function(result) {

				try {

					var response = JSON.parse(result);

					if (response.tipo == "success") {
						exibeAlerta(response.tipo, response.msg, 6);
						Filtro();
					} else {
						exibeAlerta(response.tipo, response.msg);
					}

				} catch (e) {
					exibeAlerta('error', 'Erro interno: ' + e);
				}

			}

		})



	}

	function ConfirmaAbonoAtraso() {
		<?php if($periodo_bloqueado):?>return false;<?php endif; ?>

		var Formulario = new FormData($('#formulario_atraso')[0]);
		var anexoObrigatorio = 0;

		$('[name="codabono[]"]').each(function() {
			if ($(this).find('option:selected').attr('data-obrigatorio') == 1)
				anexoObrigatorio = 1;
		})

		var arquivo = $('#arquivo').val();

		if (arquivo == '' && anexoObrigatorio == 1) {
			exibeAlerta("error", "É obrigatório um anexo.");
			return false;
		}

		openLoading();

		$.ajax({
			url: "<?= base_url('ponto/critica/action/lancar_abono_atraso') ?>",
			type: 'POST',
			processData: false,
			contentType: false,
			data: Formulario,

			success: function(result) {

				try {

					var response = JSON.parse(result);

					if (response.tipo == "success") {
						exibeAlerta(response.tipo, response.msg, 6);
						Filtro();
					} else {
						exibeAlerta(response.tipo, response.msg);
					}

				} catch (e) {
					exibeAlerta('error', 'Erro interno: ' + e);
				}

			}

		})



	}

	//****************************************************
	//******Filtro
	//****************************************************

	const Filtro = () => {
		
		let dados = {
			"periodo"          : $("#periodo").val(),
			"data_inicio"      : $("#data_inicio").val(),
			"data_fim"         : $("#data_fim").val(),
			"ck_ExtraExecutado": $("#ck_ExtraExecutado").is(':checked'),
			"ck_semPar"        : $("#ck_semPar").is(':checked'),
			"ck_Atrasos"       : $("#ck_Atrasos").is(':checked'),
			"ck_Faltas"        : $("#ck_Faltas").is(':checked'),
			"ck_jorMaior10"    : $("#ck_jorMaior10").is(':checked'),
			"ck_jorMaior12"    : $("#ck_jorMaior12").is(':checked'),
			"ck_interjornada"  : $("#ck_interjornada").is(':checked'),
			"ck_todos"         : $("#ck_todos").is(':checked'),
			"funcionario"      : $('#funcionario').val(),
		}
		
		if (dados.periodo == "") {
			exibeAlerta("error", "<b>Período</b> não selecionado.");
			return false;
		}
		if (dados.data_inicio == "") {
			exibeAlerta("error", "<b>Data inicio</b> não selecionada.");
			return false;
		}
		if (dados.data_fim == "") {
			exibeAlerta("error", "<b>Data fim</b> não selecionada.");
			return false;
		}

		if(!dados.ck_ExtraExecutado && !dados.ck_semPar && !dados.ck_Atrasos && !dados.ck_Faltas && !dados.ck_jorMaior10 && !dados.ck_jorMaior12 && !dados.ck_interjornada && !dados.ck_todos){
			exibeAlerta("error", "Selecione ao menos 1 ocorrência.");
			return false;
		}

		if(dados.ck_ExtraExecutado){
			var vl_extra_executado = $("#vl_extra_executado").val();
			if(vl_extra_executado == '' || vl_extra_executado == '__:__'){
				exibeAlerta("error", "Informe o valor para <b>Extra executado</b>");
				return false;
			}
		}

		if(dados.ck_Atrasos){
			var vl_atrasos = $("#vl_atrasos").val();
			if(vl_atrasos == '' || vl_atrasos == '__:__'){
				exibeAlerta("error", "Informe o valor para <b>Atrasos</b>");
				return false;
			}
		}

		const data_inicio_periodo = new Date($("#periodo option:selected").attr('data-inicio'));
		const data_fim_periodo    = new Date($("#periodo option:selected").attr('data-fim'));
		const data_inicio         = new Date($("#data_inicio").val());
		const data_fim            = new Date($("#data_fim").val());

		if (data_inicio < data_inicio_periodo || data_inicio > data_fim_periodo) {
			exibeAlerta("error", "<b>Data inicio</b> não está dentro do periodo selecionado.");
			return false;
		}
		if (data_fim > data_fim_periodo || data_fim < data_inicio_periodo) {
			exibeAlerta("error", "<b>Data fim</b> não está dentro do periodo selecionado.");
			return false;
		}
		
		openLoading();
		$("#form_filtro").attr('action', base_url + '/ponto/critica').attr('target', '_self');
		document.getElementById("form_filtro").submit();

	}

	const Excel = () => {
		
		let dados = {
			"periodo"          : $("#periodo").val(),
			"data_inicio"      : $("#data_inicio").val(),
			"data_fim"         : $("#data_fim").val(),
			"ck_ExtraExecutado": $("#ck_ExtraExecutado").is(':checked'),
			"ck_semPar"        : $("#ck_semPar").is(':checked'),
			"ck_Atrasos"       : $("#ck_Atrasos").is(':checked'),
			"ck_Faltas"        : $("#ck_Faltas").is(':checked'),
			"ck_jorMaior10"    : $("#ck_jorMaior10").is(':checked'),
			"ck_jorMaior12"    : $("#ck_jorMaior12").is(':checked'),
			"ck_interjornada"  : $("#ck_interjornada").is(':checked'),
			"ck_todos"         : $("#ck_todos").is(':checked'),
			"funcionario"      : $('#funcionario').val(),
		}
		
		if (dados.periodo == "") {
			exibeAlerta("error", "<b>Período</b> não selecionado.");
			return false;
		}
		if (dados.data_inicio == "") {
			exibeAlerta("error", "<b>Data inicio</b> não selecionada.");
			return false;
		}
		if (dados.data_fim == "") {
			exibeAlerta("error", "<b>Data fim</b> não selecionada.");
			return false;
		}

		if(!dados.ck_ExtraExecutado && !dados.ck_semPar && !dados.ck_Atrasos && !dados.ck_Faltas && !dados.ck_jorMaior10 && !dados.ck_jorMaior12 && !dados.ck_interjornada && !dados.ck_todos){
			exibeAlerta("error", "Selecione ao menos 1 ocorrência.");
			return false;
		}

		if(dados.ck_ExtraExecutado){
			var vl_extra_executado = $("#vl_extra_executado").val();
			if(vl_extra_executado == '' || vl_extra_executado == '__:__'){
				exibeAlerta("error", "Informe o valor para <b>Extra executado</b>");
				return false;
			}
		}

		if(dados.ck_Atrasos){
			var vl_atrasos = $("#vl_atrasos").val();
			if(vl_atrasos == '' || vl_atrasos == '__:__'){
				exibeAlerta("error", "Informe o valor para <b>Atrasos</b>");
				return false;
			}
		}

		const data_inicio_periodo = new Date($("#periodo option:selected").attr('data-inicio'));
		const data_fim_periodo    = new Date($("#periodo option:selected").attr('data-fim'));
		const data_inicio         = new Date($("#data_inicio").val());
		const data_fim            = new Date($("#data_fim").val());

		if (data_inicio < data_inicio_periodo || data_inicio > data_fim_periodo) {
			exibeAlerta("error", "<b>Data inicio</b> não está dentro do periodo selecionado.");
			return false;
		}
		if (data_fim > data_fim_periodo || data_fim < data_inicio_periodo) {
			exibeAlerta("error", "<b>Data fim</b> não está dentro do periodo selecionado.");
			return false;
		}
		
		// openLoading();
		$("#form_filtro").attr('action', base_url + '/ponto/critica/excel').attr('target', '_blank');
		document.getElementById("form_filtro").submit();

	}


	//****************************************************
	//******Passa dados para IUDs 
	//****************************************************

	const AdicionarBatida = () => {
		<?php if($periodo_bloqueado):?>return false;<?php endif; ?>

		let dados = {
			"addData"          : $("#addData").val(),
			"addDataReferencia": $("#addDataReferencia").val(),
			"addNatureza"      : $("#addNatureza").val(),
			"addBatida"        : $("#addBatida").val(),
			"addJustificativa" : $("#addJustificativa").val(),
			"textoJustOutros"  : $("#textoJustOutros").val(),
			"addLocalizacao"   : $("#addLocalizacao").val(),
			"addChapa"         : $("#addChapa").val(),
			"movimento"        : 1,
		}

		if (dados.addNatureza == "") {
			exibeAlerta("error", "Natureza não informada.");
			return false;
		}
		if (dados.addBatida == "") {
			exibeAlerta("error", "Registro não informado.");
			return false;
		}
		if (dados.addJustificativa == "") {
			exibeAlerta("error", "Justificativa não informada.");
			return false;
		}
		if (dados.addJustificativa == "Outros" && dados.textoJustOutros.trim() == "") {
			exibeAlerta("error", "Justificativa (outros) não especificada.");
			return false;
		}

		openLoading();

		$.ajax({
			url: "<?= base_url('Ponto/Critica/action/cadastrar_batida'); ?>",
			type: 'POST',
			data: dados,
			success: function(result) {


				openLoading(true);

				try {
					var response = JSON.parse(result);

					if (response.tipo == "success") {
						exibeAlerta(response.tipo, response.msg, 2);

						console.log(dados);

						id = $("#" + dados.addLocalizacao)
						$(id).val(dados.addBatida);
						$(id).css({
							'background-color': '#50fc3a'
						});


						$("#addNatureza").val(""),
						$("#addBatida").val(""),
						$("#addJustificativa").val(""),
						$("#textoJustOutros").val(""),
						$("#modalAdiciona").modal("hide");

						Filtro();
					}
				} catch (e) {
					exibeAlerta(
						"error",
						"Não foi possível alterar registro."
					);
				}
			},
			error: function() {
				exibeAlerta(
					"error",
					"Ocorreu um erro ao comunicar com o servidor."
				);
			},
		});

	}



	const EditarBatida = () => {

		<?php if($periodo_bloqueado ?? true):?>return false;<?php endif; ?>


		let dados = {
			"alteraData"            : $("#alteraData").val(),
			"alteraDataReferencia"  : $("#alteraDataReferencia").val(),
			"movimento"             : $("#alteraAcao").val(),
			"textoJustExcluir"      : $("#textoJustExcluir").val(),
			"textoJustAlteraJornada": $("#textoJustAlteraJornada").val(),
			"alteraIDAAFDT"         : $("#alteraIDAAFDT").val(),
			"alteraNatureza"        : $("#alteraNatureza").val(),
			"alteraChapa"           : $("#alteraChapa").val(),
			"alteraBatida"          : $("#alteraBatida").val(),
			"alteraLocalizacao"     : $("#alteraLocalizacao").val(),
		}
		
		if (dados.movimento == "") {
			exibeAlerta("error", "Ação não informada.");
			return false;
		}
		if (dados.movimento == "EB" && dados.textoJustExcluir == "") {
			exibeAlerta("error", "Justificativa não especificada.");
			return false;
		}
		if (dados.movimento == "AR" && dados.textoJustAlteraJornada == "") {
			exibeAlerta("error", "Jornada não especificada.");
			return false;
		}

		openLoading();

		$.ajax({
			url: "<?= base_url('Ponto/Critica/action/editar_batida'); ?>",
			type: 'POST',
			data: dados,
			success: function(result) {
				console.log(result);
				openLoading(true);

				try {

					var response = JSON.parse(result);

					if (response.tipo == "success") {
						exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('Ponto/Critica/index'); ?>');
					} else {
						exibeAlerta(response.tipo, response.msg);
					}

				} catch (e) {
					exibeAlerta('error', 'Erro interno: ' + e);
				}
			},
		});

	}

	//****************************************************
	//******Inputs dinâmicos
	//****************************************************

	$("#addJustificativa").on('change', function() {
		var value = $(this).val();

		if (value == 'Outros') {
			$("#textoJustOutros").show();
		} else {
			$("#textoJustOutros").hide();
		}
	})

	$("#alteraAcao").on('change', function() {
		var value = $(this).val();

		if (value == 'EB') {
			$("#textoJustExcluir").show();
		} else {
			$("#textoJustExcluir").hide();
		}

		if (value == 'AR') {
			$("#textoJustAlteraJornada").show();
		} else {
			$("#textoJustAlteraJornada").hide();
		}
	})


	$('#periodo').on('change', function() {

		const data_inicio = $("#periodo option:selected").attr('data-inicio');
		const data_fim = $("#periodo option:selected").attr('data-fim');

		$('#data_inicio').val(data_inicio);
		$('#data_fim').val(data_fim);


	});
</script>

<style>
.info_bat input {
    width: 46px;
    padding: 0;
    margin: 0;
    border: 1px solid #cccccc;
}
</style>

<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.css'); ?>"/>
<link rel="stylesheet" type="text/css" href="<?= base_url('public/assets/plugins/datatables/fixedHeader/fixedHeader.dataTables.css'); ?>"/>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/jquery.dataTables.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('public/assets/plugins/datatables/fixedHeader/dataTables.fixedHeader.js'); ?>"></script>

<?php
loadPlugin(array('select2', 'datatable', 'mask', 'tooltips'))
?>
<style>
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0 !important;
    border: none !important;
    background: none !important;
}
.dtfh-floatingparent {
    height: 121px !important;
	top: 63px !important;
}
.dtfh-floatingparent .sorting {
    padding-top: 80px !important;
}
.dtfh-floatingparent td {
	vertical-align: bottom;
}
</style>
<script>
// variaveis global
quantidade_batidas = 1;
data_nova_batida = '';
tipo_atitude = '';
tipo_do_abono = '';
chapaFunc = '';
cpfFunc = '';
abono_noturno = false;
msg_noturno = "";
msg_aviso = "";
msg_aviso_batida = "";
inicio_escala = "";
termino_escala = "";
qtde_falta = "";
lista_abonos = JSON.parse('<?= json_encode($resAbono); ?>');
<?php
$select_justificativa_ajuste = "";
if($resJustificativaAjuste){
	foreach($resJustificativaAjuste as $key => $JustificativaAjuste){
		$select_justificativa_ajuste .= '<option value="'.$JustificativaAjuste['descricao'].'">'.$JustificativaAjuste['descricao'].'</option>';
	}
}
echo "var select_justificativa_ajuste = '{$select_justificativa_ajuste}';
";
?>
</script>
<script>
// cria nova linha para inclusão de nova batida
const incluirNovaBatida = () => {

	if (!validaDados()) return;

	$("[data-just]").fadeOut(0);
	$("[data-obs]").fadeOut(0);
	var id = Math.random();

	var html = '<tr data-p="I" data-info="' + id + '">' +
		'<td><input type="date" class="form-control form-control-sm" value="' + data_nova_batida + '"></td>' +
		'<td><input type="date" class="form-control form-control-sm" value="' + data_nova_batida + '"></td>' +
		'<td><input type="time" class="form-control form-control-sm" value=""></td>' +
		'<td><select class="form-control form-control-sm"><option value="0" ' + (natureza_proxima_batida != 1 ? 'selected' : '') + '>Entrada</option><option value="1" ' + (natureza_proxima_batida == 1 ? 'selected' : '') + '>Saida</option></select></td>' +
		'<td width="18" class="p-0"><button class="btn btn-danger btn-xxs pr-1 pl-1" data-remove-batida="' + id + '">X</button></td>' +
		'</tr>' +
		'<tr data-justificativa="' + id + '" data-just><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Motivo do ajuste:</label><select class="form-control form-control-sm mt-"><option value="">...</option>'+select_justificativa_ajuste+'</td></tr>'+
		'<tr data-observacao="' + id + '" data-obs><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Deseja incluir informações adicionais? </label><textarea class="form-control"></textarea></td></tr>';

	$('.modal_alterar_batida tbody').append(html);

	// abre a justificativa da batida ao clicar na linha
	$("[data-info]").on('click', function(e) {
		$("[data-just]").fadeOut(0);
		$("[data-obs]").fadeOut(0);
		$("[data-justificativa='" + $(this).attr('data-info') + "']").fadeIn(0);
		$("[data-observacao='" + $(this).attr('data-info') + "']").fadeIn(0);
	});

	// remove a nova batida
	$("[data-remove-batida]").on('click', function(e) {
		$("[data-info='" + $(this).attr('data-remove-batida') + "']").remove();
		$("[data-justificativa='" + $(this).attr('data-remove-batida') + "']").remove();
		$("[data-observacao='" + $(this).attr('data-remove-batida') + "']").remove();
		verificaLimite();
		natureza_proxima_batida = (natureza_proxima_batida != 1) ? 1 : 0;
	});
	verificaLimite();

	natureza_proxima_batida = (natureza_proxima_batida != 1) ? 1 : 0;

}
//-----------------------------------------------------------
// valida se todos os dados da batida nova foram preenchidos
//-----------------------------------------------------------
const validaDados = () => {

	msg_aviso_datas = '';

	$("[data-p]").each(function(e) {
		let tipo = $(this).attr('data-p');
		let id = $(this).attr('data-info');

		if (id != undefined) {
			if (tipo == "I") {
				var just = $("[data-justificativa='" + id + "'] select").val().trim();
			}
			let data = $("[data-info='" + id + "']").find('input,select')[0].value;
			let data_ref = $("[data-info='" + id + "']").find('input,select')[1].value;
			let batida = $("[data-info='" + id + "']").find('input,select')[2].value;
			let batida_default = $("[data-info='" + id + "']").find('input,select')[2].getAttribute('data-default');
			let natureza = $("[data-info='" + id + "']").find('input,select')[3].value;

			$("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm');
			$("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm');
			$("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm');
			$("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm');

			if (data == "") $("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
			if (data_ref == "") $("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
			if (batida == "") $("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
			if (natureza == "") $("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');

			var a = new Date(data),
				b = new Date(data_ref),
				difference = dateDiffInDays(a, b);

			if(difference < 0) difference = difference*-1;
			if(difference > 2){
				msg_aviso_datas = 'Diferença entre a data e data referência é superior a 2 dias.';
				$("[data-info='" + id + "']").find('input,select')[0].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
			}

			if (tipo == "I") {

				let Vinicio_escala  = parseInt(inicio_escala.replace(':', ''));
				let Vtermino_escala = parseInt(termino_escala.replace(':', ''));
				if(Vinicio_escala > Vtermino_escala) Vtermino_escala = Vtermino_escala + 2400;

				let batidaInformada = parseInt(batida.replace(':', ''));

				if(batidaInformada < Vinicio_escala){
					msg_aviso_batida = '<b>Registro</b> está fora de sua jornada.';
				}
				if(batidaInformada > Vtermino_escala){
					msg_aviso_batida = '<b>Registro</b> está fora de sua jornada.';
				}

				if (just != undefined) {
					if (just == "") {
						$("[data-justificativa='" + id + "'] select").addClass("parsley-error text-danger form-control-sm");
					} else {
						$("[data-justificativa='" + id + "'] select").removeClass("parsley-error text-danger form-control-sm");
					}
				}
			}
		}

	});

	if(msg_aviso_datas != ''){
		exibeAlerta('error', msg_aviso_datas);
		return false;
	}

	return ($(".modal_alterar_batida .parsley-error").length > 0) ? false : true;
}
// verifica a qtde maxima de batida permitida
const verificaLimite = () => {
	let qtde = $(".modal_alterar_batida [type=time]").length;
	if (qtde >= 6) {
		$('[data-btn-add]').fadeOut(0);
	} else {
		$('[data-btn-add]').fadeIn(0);
	}
}
//------------------------------------------------------
// alteração de batidas
//------------------------------------------------------
const abrirAlteracaoBatida = (data, data_br, diasemana, batidas, chapa, escala, inicioEscala, terminoEscala, cpf, is_motorista) => {

	chapaFunc = chapa;
	cpfFunc = cpf;
	msg_aviso_batida = "";
	msg_aviso_datas = "";

	inicio_escala = inicioEscala;
    termino_escala = terminoEscala;

	$("#btnConsultaMacro").fadeOut(0);
	if(is_motorista > 0) $("#btnConsultaMacro").fadeIn(0);
	
	$(".modal_alterar_batida").modal('show');
	$(".modal_alterar_batida [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
	$(".modal_alterar_batida [data-h-escala], .modal_macro [data-h-escala]").html('Escala: '+escala);
	$(".modal_alterar_batida [data-h-batidas]").html('Registros existentes: ');

	var batidas = JSON.parse(decodeURIComponent(batidas));
	
	$('.modal_alterar_batida tbody').html('');

	data_nova_batida = data;
	natureza_proxima_batida = 0;

	for (var x = 0; x < batidas.length; x++) {

		var liberado = (batidas[x].pendente != 1) ? ' disabled ' : '';
		var tipo = (batidas[x].pendente != 1) ? ' data-p="RM" data-idaafdt="' + batidas[x].idaafdt + '" ' : ' data-p="U" ';

		var id_tr = Math.random();
		var html = '<tr data-info="' + id_tr + '" ' + tipo + '>' +
			'<td><input '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' type="date" data-batida-default="'+batidas[x].data_ponto+'" ' + liberado + ' class="form-control form-control-sm  p-0" value="' + batidas[x].data_ponto + '"></td>' +
			'<td><input '+((batidas[x].motivo_reprova != '' && batidas[x].motivo_reprova != null) ? ' disabled ' : '')+' data-referencia-default="'+batidas[x].data_referencia+'" type="date" class="form-control form-control-sm" value="' + batidas[x].data_referencia + '"></td>' +
			
			'<td><div class="input-group"><input '+((batidas[x].reprova != '' && batidas[x].reprova != null) ? ' disabled ' : '')+' data-default="' + batidas[x].batida + '" ' + liberado + ' type="time" class="form-control form-control-sm  p-0" value="' + batidas[x].batida + '"><div class="input-group-append"><span class="input-group-text pt-0 pb-0">'+batidas[x].status+'</span></div></div></td>' +
			'<td><select '+((batidas[x].motivo_reprova != '' && batidas[x].motivo_reprova != null) ? ' disabled ' : '')+' class="form-control form-control-sm" style="min-width:56px;"><option value="0" ' + (batidas[x].natureza != 1 ? 'selected' : '') + '>Entrada</option><option value="1" ' + (batidas[x].natureza == 1 ? 'selected' : '') + '>Saida</option></select></td>' +
			'<td width="1" class="p-0">';

			$(".modal_alterar_batida [data-h-batidas]").append(batidas[x].batida + ' | ');

		if (batidas[x].status == 'D' && batidas[x].pendente == 0) {
		} else if (batidas[x].status == 'D' && batidas[x].pendente == 1) {
			html += '<button onclick="return excluirBatidaPT(\'' + batidas[x].data_ponto + '\', \'' + batidas[x].batida + '\')" class="btn btn-danger btn-xxs btn-excluir pr-1 pl-1">X</button>';
		}

		html += '</td></tr>';

		var exibe = <?= ($rh) ? 1 : 0; ?>;
		
        if(batidas[x].justificativa_batida != null && (exibe == 1)) html += '<tr data-justificativa="'+id_tr+'" data-just="" style="display: none;"><td colspan="3" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Motivo do ajuste:</label> <span class="badge badge-primary">'+batidas[x].justificativa_batida.replaceAll('+', ' ')+'</span></td></tr>';
		if(batidas[x].obs != null && batidas[x].obs != '' && (exibe == 1)) html += '<tr data-observacao="'+id_tr+'" data-obs=""><td><label>Deseja incluir informações adicionais?</label><textarea class="form-control" value="'+batidas[x].obs+'"></textarea></td></tr>';
		$('.modal_alterar_batida tbody').append(html);
		if(batidas[x].obs != null && batidas[x].obs != '') $("[data-observacao='"+id_tr+"'] textarea").val(batidas[x].obs).change().attr('readonly', true);

		natureza_proxima_batida = (batidas[x].natureza != 1) ? 1 : 0;

	}

	// abre a justificativa da batida ao clicar na linha
	$("[data-info]").on('click', function(e) {
		$("[data-just]").fadeOut(0);
		$("[data-justificativa='" + $(this).attr('data-info') + "']").fadeIn(0);
		$("[data-obs]").fadeOut(0);
		$("[data-observacao='" + $(this).attr('data-info') + "']").fadeIn(0)
	});

	verificaLimite();
}
// salva alteração de batida
const alterarBatida = () => {

	if (!validaDados()) return;

	var dados = [];
	var fd = new FormData();

	$("[data-p]").each(function(e) {
		let tipo = $(this).attr('data-p');
		let idaafdt = (tipo == "RM") ? $(this).attr('data-idaafdt') : 0;
		let id = $(this).attr('data-info');

		if (id != undefined) {
			if (tipo == "I") {
				var just = $("[data-justificativa='" + id + "'] select").val().trim();
			}
			var obs = $("[data-observacao='" + id + "' ] textarea").val();
			let data = $("[data-info='" + id + "']").find('input,select')[0].value;
			let data_default = $("[data-info='" + id + "']").find('input,select')[0].getAttribute('data-batida-default');
			let data_ref = $("[data-info='" + id + "']").find('input,select')[1].value;
			let data_ref_default = $("[data-info='" + id + "']").find('input,select')[1].getAttribute('data-referencia-default');
			let batida = $("[data-info='" + id + "']").find('input,select')[2].value;
			let batida_default = $("[data-info='" + id + "']").find('input,select')[2].getAttribute('data-default');
			let natureza = $("[data-info='" + id + "']").find('input,select')[3].value;

			if (data_ref == "") {
				$("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
			} else {
				$("[data-info='" + id + "']").find('input,select')[1].setAttribute('class', 'form-control form-control-sm');
			}

			if (batida == "") {
				$("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
			} else {
				$("[data-info='" + id + "']").find('input,select')[2].setAttribute('class', 'form-control form-control-sm');
			}

			if (natureza == "") {
				$("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm parsley-error text-danger');
			} else {
				$("[data-info='" + id + "']").find('input,select')[3].setAttribute('class', 'form-control form-control-sm');
			}

			if (tipo != "RM") {
				if (just != undefined) {
					if (just == "" || just.length < 4) {
						$("[data-justificativa='" + id + "'] select").addClass("parsley-error form-control-sm text-danger");
					} else {
						$("[data-justificativa='" + id + "'] select").removeClass("parsley-error form-control-sm text-danger");
					}
				}
			}

			// dados.push({
			// 	'data': data_nova_batida,
			// 	'tipo': tipo,
			// 	'id': id,
			// 	'justificativa': (just != undefined) ? just : '',
			// 	'data_ref': data_ref,
			// 	'batida': batida,
			// 	'batida_default': batida_default,
			// 	'natureza': natureza,
			// 	"codfilial": '1',
			// 	"chapa": chapaFunc,
			// 	"idaafdt": parseInt(idaafdt)
			// });

			var doc = $("[name=anexo_batida]");
			var tem_anexo = 0;
			if (tipo == "I") {
				if (doc[0].files[0] != undefined) {
					var tem_anexo = 1;
				}
			}
			
			var dados = [];

			dados.push({
				'data_default'        : data_default,
				'data'                : data,
				'tipo'                : tipo,
				'id'                  : id,
				'justificativa'       : (just != undefined) ? just : '',
				'obs'				  : (obs != undefined) ? obs : '',
				'data_ref'            : data_ref,
				'data_ref_default'    : data_ref_default,
				'batida'              : batida,
				'batida_default'      : batida_default,
				'natureza'            : natureza,
				"codfilial"           : '1',
				"chapa"               : chapaFunc,
				"idaafdt"             : parseInt(idaafdt),
				"tem_anexo"           : tem_anexo
			});

			fd.append('dados[]', JSON.stringify(dados));
			if (tipo == "I") {
				if (doc[0].files[0] != undefined) fd.append(doc[0].files[0].name, doc[0].files[0]);
			}
			
		}

	});

	if(msg_aviso_batida != ''){

		Swal.fire({
			icon: 'question',
			title: msg_aviso_batida,
			showDenyButton: true,
			showCancelButton: true,
			confirmButtonText: `Sim, confirmar`,
			denyButtonText: `Cancelar`,
			showCancelButton: false,
			showCloseButton: false,
			allowOutsideClick: false,
			width: 600,
		}).then((result) => {
			if (result.isConfirmed) {

				openLoading();

				$.ajax({
					url: "<?= base_url('ponto/espelho/action/alterar_batida') ?>",
					type: 'POST',
					processData: false,
					contentType: false,
					data: fd,
					success: function(result) {

						openLoading(true);

						try {
							var response = JSON.parse(result);

							if (response.tipo != 'success') {
								exibeAlerta(response.tipo, response.msg);
							} else {
								exibeAlerta(response.tipo, response.msg, 3);
								var myTimeout = setTimeout(function() {
									Filtro();
									clearTimeout(myTimeout);
								}, 2000);
							}

						} catch (e) {
							exibeAlerta('error', '<b>Erro interno:</b> ' + e);
						}


					},
				});

			}
		});

	}else{

		openLoading();

		$.ajax({
			url: "<?= base_url('ponto/espelho/action/alterar_batida') ?>",
			type: 'POST',
			processData: false,
			contentType: false,
			data: fd,
			success: function(result) {

				openLoading(true);

				try {
					var response = JSON.parse(result);

					if (response.tipo != 'success') {
						exibeAlerta(response.tipo, response.msg);
					} else {
						exibeAlerta(response.tipo, response.msg, 3);
						var myTimeout = setTimeout(function() {
							Filtro();
							clearTimeout(myTimeout);
						}, 2000);
					}

				} catch (e) {
					exibeAlerta('error', '<b>Erro interno:</b> ' + e);
				}


			},
		});

	}

	

}
//excluir batida RM tabela ABATFUN
const excluirBatidaPT = (data_ref, batida) => {

	Swal.fire({
		icon: 'question',
		title: 'Deseja excluir esse registro?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim, confirmar`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if (result.isConfirmed) {

			try {
				let dados = {
					"data_ref"      : data_ref,
					"horario_batida": batida,
					"chapa"         : chapaFunc,
					"coligada"      : '<?= ($_SESSION['func_coligada']) ?>'
				}

				openLoading();

				$.ajax({
					url: "<?= base_url('ponto/espelho/action/excluir_batida_pt') ?>",
					type: 'POST',
					data: dados,
					success: function(result) {

						openLoading(true);

						try {
							var response = JSON.parse(result);

							if (response.tipo != 'success') {
								exibeAlerta(response.tipo, response.msg);
							} else {
								exibeAlerta(response.tipo, response.msg, 5);
								var myTimeout = setTimeout(function() {
									Filtro();
									clearTimeout(myTimeout);
								}, 2000);
							}

						} catch (e) {
							exibeAlerta('error', '<b>Erro interno:</b> ' + e);
						}

					},
				});

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}
		} else {
			return false;
		}
	})
}
const dateDiffInDays = (a, b) => {
	const _MS_PER_DAY = 1000 * 60 * 60 * 24;
	// Discard the time and time-zone information.
	const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
	const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

	return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}
//-----------------------------------------------------------
// solicitar abono
//-----------------------------------------------------------
const abrirSolicitacaoAbono = (data, data_br, diasemana, abonos, tipo_ocorrencia, chapa, escala, batidas, periodo_bloqueado, inicioEscala, terminoEscala, faltas, atrasos) => {
	$(".modal_abono").modal('show');

	inicio_escala = inicioEscala;
	termino_escala = terminoEscala;
	qtde_falta = (faltas != '0:00') ? faltas : atrasos;
	chapaFunc = chapa;
	
	// $(".modal_abono h3").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
	$(".modal_abono [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
	$(".modal_abono [data-h-escala]").html('Escala: '+escala);
	$(".modal_abono [data-h-batidas]").html('Registros existentes: ');
	try{
		var batidas = JSON.parse(decodeURIComponent(batidas));
	} catch (e) {	
		var batidas = [];
	}

	for (var x = 0; x < batidas.length; x++) {
		$(".modal_abono [data-h-batidas]").append(batidas[x].batida + ' | ');
	}
	
	
	$("[name=data_abono]").val(data);
	data_nova_batida = data;
	tipo_do_abono = parseInt(tipo_ocorrencia);

	$('.modal_abono tbody').html('');

	openLoading();

	$.ajax({
		url: "<?= base_url('ponto/espelho/action/listar_abonos') ?>",
		type: 'POST',
		data: {
			dataPonto: data,
			chapa: chapaFunc
		},
		success: function(response) {

			openLoading(true);

			
			for (var x = 0; x < response.length; x++) {

				var titulo_abono = '';

				var select_abono = '<select class="form-control form-control-sm p-0 m-0" disabled>';
				select_abono += '<option value="">...</option>';

				for (var y = 0; y < lista_abonos.length; y++) {
					select_abono += '<option data-atestado="' + lista_abonos[y].ATESTADOOBRIGATORIO + '" value="' + lista_abonos[y].CODIGO + '" ' + ((response[x].abn_codabono == lista_abonos[y].CODIGO) ? ' selected ' : '') + '>' + lista_abonos[y].DESCRICAO + ' - ' + lista_abonos[y].CODIGO + '</option>';

					if(response[x].abn_codabono == lista_abonos[y].CODIGO) titulo_abono = ' data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" title="'+lista_abonos[y].DESCRICAO+'" class="tippy-btn" ';

				}

				select_abono += '</select>';

				var horaini = Math.floor(response[x].abn_horaini / 60);
				var minini = response[x].abn_horaini % 60;
				var horafim = Math.floor(response[x].abn_horafim / 60);
				var minfim = response[x].abn_horafim % 60;

				// Formatação das horas com dois dígitos
				var horaInicioFormatada = horaini.toString().padStart(2, '0');
				var minutoInicioFormatado = minini.toString().padStart(2, '0');
				var horaFimFormatada = horafim.toString().padStart(2, '0');
				var minutoFimFormatado = minfim.toString().padStart(2, '0');
				
				if (response[x].status == 'S') {
					var html = '<tr data-abono="S" data-id-abono="' + Math.random() + '">' +
						'<td '+titulo_abono+'>' + select_abono + '</td>' +
						'<td><input disabled type="checkbox" data-parcial value="1"></td>' +
						'<td><input disabled type="text" class="form-control form-control-sm" value=""></td>' +
						'<td><input disabled data-inicio-default="' + response[x].abn_horaini + '" type="time" class="form-control form-control-sm p-0" value="' + horaInicioFormatada + ':' + minutoInicioFormatado + '"></td>' +
						'<td><input disabled data-termino-default="' + response[x].abn_horafim + '" type="time" class="form-control form-control-sm p-0" value="' + horaFimFormatada + ':' + minutoFimFormatado + '"></td>';
						//'<td><input disabled data-justificativa-default="' + (response[x].justificativa_abono_tipo || '') + '" type="text" maxlength="50" class="form-control form-control-sm" value="' + (response[x].justificativa_abono_tipo || '') + '"></td>';
				
					html += '<td width="1" class="p-0" '+titulo_abono+'><span class="badge badge-success">Sinc.</span></td>' +
						'<td></td>'
					'</tr>';
					if(response[x].nome_anexo != '' && response[x].nome_anexo != null && response[x].nome_anexo != undefined){
						html += '<tr>';
						html += '<td colspan="4"><label><i class="mdi mdi-arrow-up-bold"></i> Anexo:</label> <a href="<?= base_url('/ponto/preview/index'); ?>/'+response[x].id+'" target="_blank">'+response[x].nome_anexo+'</a></td>';
						html += '</tr>';
					}
				}

				$('.modal_abono tbody').append(html);
				validaTotalHorasAbono(false);
			}

			if(periodo_bloqueado == 1){
				$(".modal_abono .modal-body *").prop('disabled', true);
				$(".modal_abono .modal-footer *").prop('disabled', true);
			}else{
				$(".modal_abono .modal-body button").prop('disabled', false);
				$(".modal_abono .modal-body [type=file]").prop('disabled', false);
				$(".modal_abono .modal-footer *").prop('disabled', false);
			}
		},
		error: function(error) {
			// Tratar erros, se necessário
			console.log(error);
		}
	});

	var abonos = JSON.parse(decodeURIComponent(abonos));
	console.log(abonos);

	for (var x = 0; x < abonos.length; x++) {

		
		var titulo_abono = '';
		var select_abono = '<select class="form-control form-control-sm p-0 m-0" disabled>';
		select_abono += '<option value="">...</option>';
		for (var y = 0; y < lista_abonos.length; y++) {
			
			select_abono += '<option data-atestado="' + lista_abonos[y].ATESTADOOBRIGATORIO + '" value="' + lista_abonos[y].CODIGO + '" ' + ((abonos[x].codabono == lista_abonos[y].CODIGO) ? ' selected ' : '') + '>' + lista_abonos[y].DESCRICAO + ' - ' + lista_abonos[y].CODIGO + '</option>';

			if(abonos[x].codabono == lista_abonos[y].CODIGO) titulo_abono = ' data-tippy-followCursor="true" data-tippy-arrow="true" data-tippy-animation="fade" aria-describedby="tippy-4" title="'+lista_abonos[y].DESCRICAO+'" ';

		}
		select_abono += '</select>';

		var html = '<tr data-abono="U" data-id-abono="' + Math.random() + '">' +
			'<td '+titulo_abono+'>' + select_abono + '</td>' +
			'<td><input disabled type="checkbox" data-parcial value="1"></td>' +
			'<td><input disabled type="text" class="form-control form-control-sm" value=""></td>' +
			'<td><input data-inicio-default="' + abonos[x].inicio + '" type="time" class="form-control p-0" value="' + abonos[x].inicio + '"></td>' +
			'<td><input data-termino-default="' + abonos[x].termino + '" type="time" class="form-control p-0" value="' + abonos[x].termino + '"></td>';
			//'<td><input data-justificativa-default="' + abonos[x].just_abono_tipo.replace(/\+/g, ' ') + '" type="text" maxlength="50" class="form-control" value="' + abonos[x].just_abono_tipo.replace(/\+/g, ' ') + '"></td>';
		if(parseInt(abonos[x].status) == 3){
			html += '<td width="1" class="p-0"><span class="badge badge-danger" tippy-btn '+titulo_abono+'>Rep.</span></td>';
			html += '<td><button class="btn btn-sm btn-danger btn-xxs" onclick="return ExcluirAbonoPT(' + abonos[x].id + ')"><i class="mdi mdi-trash-can-outline"></i></button></td>';
		}else{
			html += '<td width="1" class="p-0"><span class="badge badge-warning tippy-btn" '+titulo_abono+'>Sol.</span></td>';
			html += '<td><button class="btn btn-sm btn-danger btn-xxs" onclick="return ExcluirAbonoPT(' + abonos[x].id + ')"><i class="mdi mdi-trash-can-outline"></i></button></td>';
		}
		html += '</tr>';
		if(abonos[x].nome_anexo != '' && abonos[x].nome_anexo != null && abonos[x].nome_anexo != undefined){
			html += '<tr>';
			html += '<td colspan="4"><label><i class="mdi mdi-arrow-up-bold"></i> Anexo:</label> <a href="<?= base_url('/ponto/preview/index'); ?>/'+abonos[x].id+'" target="_blank">'+abonos[x].nome_anexo+'</a></td>';
			html += '</tr>';
		}

		$('.modal_abono tbody').append(html);
		validaTotalHorasAbono(false);

		$("[data-abono] [type=time]").on('keyup', function(e) {
			validaTotalHorasAbono(false);
		});

	}

}
const validaTotalHorasAbono = (alerta = true) => {

	var horasTotal  = 0;
	var qtdeHoras = h2m(qtde_falta);
	var horasAbono  = 0;
	$("[data-abono]").each(function(e) {

		let inicio = $(this).find('input')[2].value;
		let termino = $(this).find('input')[3].value;
		var tipo = $(this).attr('data-abono');
		

		if (inicio != '' && termino != '') {

			// novo_inicio  = parseInt(inicio.replace(':', ''));
			// novo_termino = parseInt(termino.replace(':', ''));

			// if(novo_inicio > novo_termino) novo_termino = novo_termino+2400;

			// let horaInicio = (novo_inicio);
			// horaInicio = horaInicio.toString().padStart(4, '0');
			// horaInicio = horaInicio.toString().substr(0,2)+ ':' + horaInicio.toString().substr(-2);

			// let horaFim = (novo_termino);
			// horaFim = horaFim.toString().padStart(4, '0');
			// horaFim = horaFim.toString().substr(0,2)+ ':' + horaFim.toString().substr(-2);
			// // $(this).find('input')[1].value = horasFinal;
			// var horasDiff = calcularDiferencaHoras(horaInicio, horaFim);
			// $(this).find('input')[1].value = horasDiff;
			
			// if(qtde_falta.toString().padStart(5, '0') == horasDiff.toString().padStart(5, '0')){
			// 	$(this).find('input')[2].setAttribute('style', 'visibility:hidden');
			// 	$(this).find('input')[3].setAttribute('style', 'visibility:hidden');
			// }

			novo_inicio = h2m(inicio);
			novo_termino = h2m(termino);

			if(novo_inicio > novo_termino) novo_termino = novo_termino+1440;
			var horasDiff = novo_termino - novo_inicio;
			
			$(this).find('input')[1].value = m2h(horasDiff);

			if(qtdeHoras == horasDiff){
				$(this).find('input')[2].setAttribute('style', 'visibility:hidden');
				$(this).find('input')[3].setAttribute('style', 'visibility:hidden');
			}

			horasTotal += (novo_termino - novo_inicio);
		}

	});

	horasAbono = horasTotal;
	if(horasTotal > qtdeHoras){
		if(alerta) exibeAlerta('error', '<b>Horas totais de abono</b> não pode ser superior ao limite de '+qtde_falta);
		return false;
	}

	return true;

}
const solicitarAbono = () => {

	abono_noturno = false;

	if (!validaDadosAbono()) return;

	var fd = new FormData();
	var formatos = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword'];

	if ($("[data-abono]").find('input').length <= 0) {
		exibeAlerta('error', 'Nenhum abono informado.');
		return;
	}

	var msg_erro = "";
	$("[data-abono]").each(function(e) {

		let tipo = $(this).attr('data-abono');

		if (tipo == "I" || tipo == "U") {

			var doc = $("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input');
			var tem_anexo = 0;
			if (tipo == "I") {
				if (doc[0].files[0] != undefined) {
					if (doc[0].files[0].size > 5242880) {
						msg_erro = "<b>" + doc[0].files[0].name + "</b> Tamanho superior a 5MB.";
					} else
					if (!formatos.includes(doc[0].files[0].type)) {
						msg_erro = "<b>" + doc[0].files[0].name + "</b> Formato do arquivo não permitido.";
					} else {
						var tem_anexo = 1;
					}
				}
			}

			var dados = [];

			dados.push({
				'data'           : data_nova_batida,
				'tipo'           : tipo,
				'codabono'       : $(this).find('select')[0].value,
				'inicio'         : $(this).find('input')[2].value,
				'termino'        : $(this).find('input')[3].value,
				'just_abono_tipo': '',
				//'just_abono_tipo': $(this).find('input')[2].value,
				'inicio_default' : $(this).find('input')[2].getAttribute('data-inicio-default'),
				'termino_default': $(this).find('input')[3].getAttribute('data-termino-default'),
				'tem_anexo'      : tem_anexo,
				"codfilial"      : '1',
				"chapa"          : chapaFunc,
				"tipo_ocorrencia": tipo_do_abono
			});

			fd.append('dados[]', JSON.stringify(dados));
			if (tipo == "I") {
				if (doc[0].files[0] != undefined) fd.append(doc[0].files[0].name + '_' + e, doc[0].files[0]);
			}
		}

	});

	if (msg_erro != "") {
		exibeAlerta('error', msg_erro);
		return;
	}

	openLoading();

	if (abono_noturno || msg_aviso) {
		
		Swal.fire({
			icon: 'question',
			title: ((msg_aviso == '') ? msg_noturno : msg_aviso),
			showDenyButton: true,
			showCancelButton: true,
			confirmButtonText: `Sim, confirmar`,
			denyButtonText: `Cancelar`,
			showCancelButton: false,
			showCloseButton: false,
			allowOutsideClick: false,
			width: 600,
		}).then((result) => {
			if (result.isConfirmed) {

				confirmaEnvioAbono(fd);

			} else {
				openLoading(true);
			}
		});
	} else {
		confirmaEnvioAbono(fd);
	}
	
}
const calcularDiferencaHoras = (hora1, hora2) => {
	var minutos1 = horaParaMinutos(hora1);
	var minutos2 = horaParaMinutos(hora2);
	
	var diferencaMinutos = minutos2 - minutos1;
	
	var horas = Math.floor(diferencaMinutos / 60);
	var minutos = diferencaMinutos % 60;
	
	return pad(horas, 2) + ":" + pad(minutos, 2);
}
const horaParaMinutos = (hora) => {
	var partes = hora.split(":");
	return parseInt(partes[0]) * 60 + parseInt(partes[1]);
}
const confirmaEnvioAbono = (fd) => {

	$.ajax({
		url: "<?= base_url('ponto/espelho/action/cadastrar_abono') ?>",
		type: 'POST',
		processData: false,
		contentType: false,
		data: fd,
		success: function(result) {

			openLoading(true);

			try {
				var response = JSON.parse(result);

				if (response.tipo != 'success') {
					exibeAlerta(response.tipo, response.msg);
				} else {
					exibeAlerta(response.tipo, response.msg, 3);
					var myTimeout = setTimeout(function() {
						Filtro();
						clearTimeout(myTimeout);
					}, 2000);
				}

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}


		},
	});

}
const validaDadosAbono = () => {

	msg_aviso = '';

	let qtde_abonos = $("[data-abono]").length;
	if (qtde_abonos > 1) {
		var novo_inicio = $("[data-abono]:last").find('input')[2].value;
		var novo_termino = $("[data-abono]:last").find('input')[3].value;
	}

	if(!validaTotalHorasAbono(true)){
		return false;
	}

	var msg_erro = "";

	$("[data-abono]").each(function(e) {

		let codabono = $(this).find('select')[0].value;
		let inicio = $(this).find('input')[2].value;
		let termino = $(this).find('input')[3].value;
		let inicioH = $(this).find('input')[2].value;
		let terminoH = $(this).find('input')[3].value;
		var atestado = $(this).find(':selected').attr('data-atestado');
		var tipo = $(this).attr('data-abono');

		if (inicio != '' && termino != '') {

			let Vinicio_escala  = parseInt(inicio_escala.replace(':', ''));
			let Vtermino_escala = parseInt(termino_escala.replace(':', ''));
			if(Vinicio_escala > Vtermino_escala) Vtermino_escala = Vtermino_escala + 2400;
			
			inicio = parseInt(inicio.replace(':', ''));
			termino = parseInt(termino.replace(':', ''));

			if (inicio > 2359) {
				msg_erro = '<b>Registro</b> não pode ser superior a <b>23:59</b>.';
				return false;
			}
			if (termino > 2359) {
				msg_erro = '<b>Registro</b> não pode ser superior a <b>23:59</b>.';
				return false;
			}
			
			if(inicio < Vinicio_escala){
				// msg_erro = '<b>Hora de início</b> esta fora de sua jornada.';
				msg_aviso = '<b>Hora de início do abono</b> esta fora de sua jornada.';
				// return false;
			}
			if(inicio > Vtermino_escala){
				// msg_erro = '<b>Hora de início</b> esta fora de sua jornada.';
				msg_aviso = '<b>Hora de início do abono</b> esta fora de sua jornada. 2';
				// return false;
			}
			if (termino < inicio) {
				if (data_nova_batida != "") {
					var dataIni = new Date(parseInt(data_nova_batida.slice(0, 4)), parseInt(data_nova_batida.slice(5, 7)) - 1, parseInt(data_nova_batida.slice(8)));
					var dataFim = new Date(parseInt(data_nova_batida.slice(0, 4)), parseInt(data_nova_batida.slice(5, 7)) - 1, parseInt(data_nova_batida.slice(8)));
					dataFim.setDate(dataFim.getDate() + 1);
					msg_noturno = "<b>Hora término " + terminoH + "</b> esta menor que a <b>hora inicio " + inicioH + "</b>, sera gerado um abono do dia <b>" + pad(dataIni.getDate(), 2) + "/" + pad(dataIni.getMonth() + 1, 2) + "</b> até <b>" + pad(dataFim.getDate(), 2) + "/" + pad(dataFim.getMonth() + 1, 2) + "</b>, confirma solicitação?";
				}
				abono_noturno = true;

				if(termino +2400 > Vtermino_escala){
					// msg_erro = '<b>Hora de término do abono não pode ser maior que o término da escala.';
					msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
					// return false;
				}
				if(termino +2400 < Vinicio_escala){
					msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
					// return false;
				}
			}else{

				if(termino > Vtermino_escala){
					// msg_erro = '<b>Hora de término do abono não pode ser maior que o término da escala.';
					// exibeAlerta('warning', '<b>Hora de término do abono</b> esta fora de sua jornada.');
					msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
					// return false;
				}
				if(termino < Vinicio_escala){
					// exibeAlerta('warning', '<b>Hora de término do abono</b> esta fora de sua jornada.');
					msg_aviso = '<b>Hora de término do abono</b> esta fora de sua jornada.';
					// return false;
				}

			}
			if (termino == inicio) {
				msg_erro = '<b>Inicio</b> não pode ser igual ao <b>Término</b>.';
				return false;
			}
		}

		if (qtde_abonos > 1 && qtde_abonos != (e + 1)) {
			if (novo_inicio != '' && novo_termino != '') {
				novo_inicio_minuto = parseInt(novo_inicio.replace(':', ''));
				novo_termino_minuto = parseInt(novo_termino.replace(':', ''));

				if (novo_inicio_minuto >= inicio && novo_inicio_minuto <= termino) {
					$("[data-abono]:last").find('[type=time]')[0].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
					msg_erro = '<b>Inicio</b> informado esta em interseção com outro horário de abono.';
					return false;
				}
				if (novo_termino_minuto >= inicio && novo_termino_minuto <= termino) {
					$("[data-abono]:last").find('[type=time]')[0].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
					msg_erro = '<b>Término</b> informado esta em interseção com outro horário de abono.';
					return false;
				}
				if (novo_inicio_minuto <= inicio && novo_termino_minuto >= termino) {
					$("[data-abono]:last").find('[type=time]')[1].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
					$("[data-abono]:last").find('[type=time]')[1].setAttribute('class', 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
					msg_erro = '<b>Inicio</b> e <b>Término</b> informado esta em interseção com outro horário de abono.';
					return false;
				}
			}
		}

		$(this).find('select')[0].setAttribute('class', (codabono != '') ? 'form-control form-control-sm p-0 m-0' : 'form-control form-control-sm p-0 m-0 parsley-error text-danger');
		$(this).find('[type=time]')[0].setAttribute('class', (typeof inicio != "string") ? 'form-control form-control-sm p-0 m-0' : 'form-control parsley-error text-danger');
		$(this).find('[type=time]')[1].setAttribute('class', (typeof termino != "string") ? 'form-control form-control-sm p-0 m-0' : 'form-control parsley-error text-danger');
		if (atestado == 1 && tipo == "I") {
			if ($("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input')[0].value == "") {
				$("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input')[0].setAttribute('class', 'form-control parsley-error text-danger');
			} else {
				$("[data-anexo-atestado='" + $(this).attr('data-id-abono') + "']").find('input')[0].setAttribute('class', 'form-control');
			}
		}

	});

	if (msg_erro != "") {
		exibeAlerta('error', msg_erro);
		return false;
	}

	return ($(".modal_abono .parsley-error").length > 0) ? false : true;

}
const incluirNovoAbono = () => {

	if (!validaDadosAbono()) return;

	var id = Math.random();

	var select_abono = '<select class="form-control form-control-sm p-0 m-0" data-cod-abono="' + id + '">';
	select_abono += '<option value="">...</option>';
	for (var x = 0; x < lista_abonos.length; x++) {
		select_abono += '<option data-atestado="' + lista_abonos[x].ATESTADOOBRIGATORIO + '" value="' + lista_abonos[x].CODIGO + '">' + lista_abonos[x].DESCRICAO + ' - ' + lista_abonos[x].CODIGO + '</option>';
	}
	select_abono += '</select>';

	var html = '<tr data-abono="I" data-id-abono="' + id + '">' +
		'<td>' + select_abono + '</td>' +
		'<td><input type="checkbox" data-parcial value="1"></td>' +
		'<td><input disabled type="text" class="form-control form-control-sm" value=""></td>' +
		'<td><input type="time" class="form-control form-control-sm" value=""></td>' +
		'<td><input type="time" class="form-control form-control-sm" value=""></td>' +
		//'<td><input type="text" maxlength="50" class="form-control form-control-sm" value=""></td>' +
		'<td width="18" class="p-0"><button class="btn btn-danger btn-xxs pr-1 pl-1" data-remove-abono="' + id + '">X</button></td>' +
		'</tr>' +
		'<tr data-anexo-atestado="' + id + '" class="hidden">' +
		'<td colspan="4" style="border-top: none;" class="pb-3"><label><i class="mdi mdi-arrow-up-bold"></i> Atestado:</label><input type="file" class="form-control" accept="image/png, image/jpeg, .pdf"></td>' +
		'</tr>';

	$('.modal_abono tbody').append(html);

	// remove a nova batida
	$("[data-remove-abono]").on('click', function(e) {
		$("[data-anexo-atestado='" + $(this).attr('data-remove-abono') + "']").remove();
		$(this).parent().parent().remove();
	});

	// horasInicio = parseInt(inicio_escala.replace(':', ''));
	// horasFalta = parseInt(qtde_falta.replace(':', ''));

	//------- correção horas
	var min_horasInicio = h2m(inicio_escala);
	var min_horasFalta  = h2m(qtde_falta);
	horasFinal = min_horasInicio + min_horasFalta;
	if(horasFinal > 1440) horasFinal = horasFinal - 1440;
	horasFinal = m2h(horasFinal);
	//------- correção horas

	// console.log(horasInicio);
	// console.log(horasFinal);

	$("[data-id-abono]:last").find('[type="time"]')[0].value=inicio_escala;
	$("[data-id-abono]:last").find('[type="time"]')[1].value=horasFinal;
	$("[data-id-abono]:last").find('[type="time"]').prop('disabled', true);
	$("[data-id-abono]:last").find('[type="time"]').css('visibility', 'hidden');

	$("[data-parcial]").on('click', function(){
		if($(this).prop('checked')){
			$(this).parent().parent().find('[type=text]')[0].value='';
			$(this).parent().parent().find('[type=time]')[0].value='';
			$(this).parent().parent().find('[type=time]')[1].value='';
			$(this).parent().parent().find('[type=time]').prop('disabled', false);
			$(this).parent().parent().find('[type=time]').css('visibility', 'visible');
		}else{
			// horasInicio = parseInt(inicio_escala.replace(':', ''));
			// horasFalta = parseInt(qtde_falta.replace(':', ''));
			// horasFinal = horasInicio + horasFalta;
			// if(horasFinal > 2400) horasFinal = horasFinal-2400;
			// horasFinal = horasFinal.toString().padStart(4, '0');
			// horasFinal = horasFinal.toString().substr(0,2)+ ':' + horasFinal.toString().substr(-2);
			//------- correção horas
			var min_horasInicio = h2m(inicio_escala);
			var min_horasFalta  = h2m(qtde_falta);
			horasFinal = min_horasInicio + min_horasFalta;
			if(horasFinal > 1440) horasFinal = horasFinal - 1440;
			horasFinal = m2h(horasFinal);
			//------- correção horas
			
			$(this).parent().parent().find('[type=time]')[0].value=inicio_escala;
			$(this).parent().parent().find('[type=time]')[1].value=horasFinal;
			$(this).parent().parent().find('[type=time]').css('visibility', 'hidden');
			$(this).parent().parent().find('[type=time]').prop('disabled', true);
			validaTotalHorasAbono(false);
		}
	});

	validaTotalHorasAbono(false);

	$("[data-cod-abono]").on('change', function(e) {

		var atestado = $(this).find(':selected').attr('data-atestado');
		var id_linha_abono = $(this).attr('data-cod-abono');

		$("[data-anexo-atestado='" + id_linha_abono + "'] input").val('');
		if (atestado == 1) {
			$("[data-anexo-atestado='" + id_linha_abono + "']").fadeIn(0);
		} else {
			$("[data-anexo-atestado='" + id_linha_abono + "']").fadeOut(0);
		}

	});

	$("[data-abono] [type=time]").on('keyup', function(e) {
		validaTotalHorasAbono(false);
	});

}
const pad = (str, length) => {
	const resto = length - String(str).length;
	return '0'.repeat(resto > 0 ? resto : '0') + str;
}
const ExcluirAbonoPT = (id) => {
	Swal.fire({
		icon: 'question',
		title: 'Deseja realmente excluir o abono do Portal?',
		showDenyButton: true,
		showCancelButton: true,
		confirmButtonText: `Sim Excluir`,
		denyButtonText: `Cancelar`,
		showCancelButton: false,
		showCloseButton: false,
		allowOutsideClick: false,
		width: 600,
	}).then((result) => {
		if(result.isConfirmed){

			try {

				openLoading();

				$.ajax({
					url: "<?= base_url('ponto/espelho/action/excluir_abono_pt'); ?>",
					type: 'POST',
					data: {
						'id': id
					},
					success: function(result) {
						openLoading(true);

						var response = JSON.parse(result);

						if(response.tipo != 'success'){
							exibeAlerta(response.tipo, response.msg);
						}else{
							exibeAlerta(response.tipo, response.msg, 5);
							var myTimeout = setTimeout(function(){
								Filtro();
								clearTimeout(myTimeout);
							}, 2000);
						}

					},
				});

			}catch (e){
				exibeAlerta('error', 'Erro interno: ' + e);
			}
		}
	});
}
//-----------------------------------------------------------
// justificativa de extra
//-----------------------------------------------------------
const abrirJustificativaExtra = (data, data_br, diasemana, codigoJustificativa, chapa, escala, batidas) => {
	$(".modal_justificativa_extra").modal('show');
	chapaFunc = chapa;
	$(".modal_justificativa_extra [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
	$(".modal_justificativa_extra [data-h-escala]").html('Escala: '+escala);
	$(".modal_justificativa_extra [data-h-batidas]").html('Registros existentes: ');
	var batidas = JSON.parse(decodeURIComponent(batidas));

	for (var x = 0; x < batidas.length; x++) {
		$(".modal_justificativa_extra [data-h-batidas]").append(batidas[x].batida + ' | ');
	}
	data_nova_batida = data;
	$("#justificativa_extra").val(codigoJustificativa);

}
function justificarExtra() {
    try {
        let dados = {
            "justificativa" : $("#justificativa_extra").val(),
            "chapa"         : chapaFunc,
            "data"          : data_nova_batida
        }

        if(dados.justificativa == ""){exibeAlerta('error', '<b>Justificativa de Extra</b> não informado.'); return false; }

        // Pergunta se quer adicionar observação
        Swal.fire({
            title: 'Deseja incluir informações adicionais?',
            showDenyButton: true,
            confirmButtonText: 'Sim',
            denyButtonText: 'Não',
			customClass: {
				title: 'swal2-title'
			}
        }).then((result) => {
            if (result.isConfirmed) {
                // Limpa o campo antes de mostrar
                $("#campoObservacaoExtra").val('');
                // Abre o modal de observação
                $("#modalObservacaoExtra").modal('show');

                // Ao clicar em salvar no modal de observação
                $("#btnSalvarObservacaoExtra").off('click').on('click', function() {
                    dados.obs = $("#campoObservacaoExtra").val();
                    $("#modalObservacaoExtra").modal('hide');
                    enviarJustificativaExtra(dados);
                });
            } else if (result.isDenied) {
                // Se não, segue fluxo normal
                enviarJustificativaExtra(dados);
            }
        });

    } catch (e) {
        exibeAlerta('error', '<b>Erro interno:</b> ' + e);
    }
}

// NOVA função auxiliar para envio AJAX
function enviarJustificativaExtra(dados) {
    openLoading();
    $.ajax({
        url: "<?= base_url('ponto/espelho/action/cadastrar_justificativa_extra') ?>",
        type: 'POST',
        data: dados,
        success: function(result) {
            openLoading(true);
            try {
                var response = JSON.parse(result);
                if (response.tipo != 'success') {
                    exibeAlerta(response.tipo, response.msg);
                } else {
                    exibeAlerta(response.tipo, response.msg, 3);
                    var myTimeout = setTimeout(function() {
                        Filtro();
                        clearTimeout(myTimeout);
                    }, 2000);
                }
            } catch (e) {
                exibeAlerta('error', '<b>Erro interno:</b> ' + e);
            }
        },
    });
}
//-----------------------------------------------------------
// altera atitude
//-----------------------------------------------------------
const abrirAlteraAtitude = (data, data_br, diasemana, chapa, escala, batidas, tipo, usabanco) => {
	$(".modal_altera_atitude").modal('show');
	chapaFunc = chapa;
	$(".modal_altera_atitude [data-h-data]").html(data_br + ' <small style="color: #999999;">' + diasemana + '</small>');
	$(".modal_altera_atitude [data-h-escala]").html('Escala: '+escala);
	$(".modal_altera_atitude [data-h-batidas]").html('Registros existentes: ');
	var batidas = JSON.parse(decodeURIComponent(batidas));

	for (var x = 0; x < batidas.length; x++) {
		$(".modal_altera_atitude [data-h-batidas]").append(batidas[x].batida + ' | ');
	}
	data_nova_batida = data;
	tipo_atitude = tipo;

	$('[data-atitude-data]').val('');
	$('[data-atitude-horas]').val('');
	$('[data-atitude-atitude]').val('');

	//var bancodehoras = (usabanco == 1) ? '<option value="1">Banco de Horas</option>' : '';
  var bancodehoras =  ''; //DESTIVADO POR ALVARO ZARAGOZA EM 16/06/2025

	if(tipo == 'F'){
		$('[data-atitude-atitude]').html(`
			<option value="">...</option>
				${bancodehoras}
				<option value="0">Falta confirmada</option>
				<option value="2">Falta não remunerada</option>
			</select>`);
	}else{
		$('[data-atitude-atitude]').html(`
			<option value="">...</option>
				${bancodehoras}
				<option value="0">Atraso confirmado</option>
				<!--option value="3">Atraso não remunerado</option-->
			</select>`);
	}
	
	openLoading();

	$.ajax({
		url: "<?= base_url('ponto/critica/action/lista_atitude') ?>",
		type: 'POST',
		data: {
			'chapa': chapaFunc,
			'data': data_nova_batida,
			'tipo': tipo_atitude
		},
		success: function(result) {

			openLoading(true);

			try {
				var response = JSON.parse(result);

				$('[data-atitude-data]').val(data);
				$('[data-atitude-horas]').val(response[0].TOTAL_HORAS);
				$('[data-atitude-atitude]').val('');

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}


		},
	});

}
const salvarAtitude = () => {

	var fd = new FormData();
	var dados = [];

	if(chapaFunc == ''){exibeAlerta('error', '<b>CHAPA</b> não informada.'); return false;}
	if(data_nova_batida == ''){exibeAlerta('error', '<b>DATA</b> não informada.'); return false;}
	if($('[data-atitude-atitude]').val() == ''){exibeAlerta('error', '<b>ATITUDE</b> não informada.'); return false;}
	if(tipo_atitude == ''){exibeAlerta('error', '<b>TIPO</b> não informada.'); return false;}

	var doc = $("[name=anexo_atitude]");
	var tem_anexo = 0;
	if (doc[0].files[0] != undefined) {
		var tem_anexo = 1;
	}

	dados.push({
		'chapa'		: chapaFunc,
		'data'		: data_nova_batida,
		'atitude'	: $('[data-atitude-atitude]').val(),
		'tipo'		: tipo_atitude,
		'horas'		: $('[data-atitude-horas]').val(),
		'tem_anexo'	: tem_anexo
	});

	fd.append('dados[]', JSON.stringify(dados));
	if (doc[0].files[0] != undefined) fd.append('anexo', doc[0].files[0]);

	openLoading();

	$.ajax({
		url: "<?= base_url('ponto/critica/action/altera_atitude') ?>",
		type: 'POST',
		processData: false,
		contentType: false,
		data: fd,
		success: function(result) {

			openLoading(true);

			try {
				var response = JSON.parse(result);

				if (response.tipo != 'success') {
					exibeAlerta(response.tipo, response.msg);
				} else {
					exibeAlerta(response.tipo, response.msg, 3);
					var myTimeout = setTimeout(function() {
						Filtro();
						clearTimeout(myTimeout);
					}, 2000);
				}

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}


		},
	});

}

$(document).ready(function(){
	$("#ck_todos").on('click', function(e){
		if($(this).prop('checked')){
			$("#ck_ExtraExecutado").prop('checked', true);
			$("#ck_semPar").prop('checked', true);
			$("#ck_Atrasos").prop('checked', true);
			$("#ck_Faltas").prop('checked', true);
			$("#ck_jorMaior10").prop('checked', true);
			$("#ck_jorMaior12").prop('checked', true);
			$("#ck_interjornada").prop('checked', true);
      $("#vl_extra_executado").val('00:01');
			$("#vl_atrasos").val('00:01');
		}else{
			$("#ck_ExtraExecutado").prop('checked', false);
			$("#ck_semPar").prop('checked', false);
			$("#ck_Atrasos").prop('checked', false);
			$("#ck_Faltas").prop('checked', false);
			$("#ck_jorMaior10").prop('checked', false);
			$("#ck_jorMaior12").prop('checked', false);
			$("#ck_interjornada").prop('checked', false);
      $("#vl_extra_executado").val('');
			$("#vl_atrasos").val('');
		}
	});
	$("[data-atitude-atitude]").on('change', function(){
		if($(this).val() == 2 || $(this).val() == 0){
			$('[data-exibe-anexo-atitude]').fadeIn(0);
		}else{
			$('[data-exibe-anexo-atitude]').fadeOut(0);
			$('[name=anexo_atitude]').val('');
		}
	});
});

function h2m(hora) {
    // Divide a string de hora em horas e minutos
    var partes = hora.split(':');
    // Converte as partes para números inteiros
    var horas = parseInt(partes[0]);
    var minutos = parseInt(partes[1]);
    // Calcula o total de minutos
    var totalMinutos = horas * 60 + minutos;
    return totalMinutos;
}
function m2h(minutos) {
    // Calcula as horas e os minutos
    var horas = Math.floor(minutos / 60);
    var minutosRestantes = minutos % 60;
    
    // Formata as horas e os minutos para terem dois dígitos
    var horasFormatadas = horas < 10 ? '0' + horas : horas;
    var minutosFormatados = minutosRestantes < 10 ? '0' + minutosRestantes : minutosRestantes;
    
    // Retorna a hora formatada
    return horasFormatadas + ':' + minutosFormatados;
}
const carregaColaboradores = () => {

openLoading();

var periodo = $("#periodo").val();
if(periodo == ''){
	exibeAlerta('warning', 'Período não selecionado.');
	return;
}

$("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');

$.ajax({
	url: "<?= base_url('ponto/espelho/action/carrega_colaboradores') ?>",
	type: 'POST',
	data: {
		'codsecao'    : $("[data-secao]").val() ?? null,
		'periodo'     : periodo
	},
	success: function(result) {

		openLoading(true);

		try {
			var response = JSON.parse(result);
			$("#funcionario").html('<option value="">-- Todos (' + response.length + ') --</option>').trigger('change');

			for (var x = 0; x < response.length; x++) {
				$("#funcionario").append('<option value="' + response[x].CHAPA + '">' + response[x].NOME + ' - ' + response[x].CHAPA + '</option>');
			}

		} catch (e) {
			exibeAlerta('error', '<b>Erro interno:</b> ' + e);
		}

	},
});

}
</script>
<!-- modal modal_alterar_batida -->
<div class="modal modal_alterar_batida" tabindex="-1" role="dialog" aria-labelledby="modal_alterar_batida" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-info">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-pencil-outline"></i> Incluir/Alterar Registro</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
						<button class="btn btn-primary btn-sm float-right" type="button" onclick="carregaMacro()" id="btnConsultaMacro"><i class="fa fa-cogs"></i> Consulta Macros</button>
						<h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <table class="table table-sm tablebatida">
                            <thead>
                                <tr>
									<th>Data</th>
                                    <th>Data referencia</th>
                                    <th class="colbatida">Registro</th>
                                    <th>Natureza</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
						<div class="row mb-2">
                            <div class="col-12">
                                <label><i class="fa fa-paperclip" aria-hidden="true"></i> Anexo:</label>
                                <input type="file" name="anexo_batida" class="form-control">
                            </div>
                        </div>
                        <button data-btn-add class="btn btn-outline-primary waves-effect waves-light btn-block" type="button" onclick="incluirNovaBatida()"><i class="mdi mdi-plus-circle-outline"></i> Adicionar registro</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" onclick="return alterarBatida()">Alterar Registro <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_incluir_batida -->
<!-- modal modal_abono -->
<div class="modal modal_abono" tabindex="-1" role="dialog" aria-labelledby="modal_abono" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content modal-dialog-full">
            <div class="modal-header bg-success">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-timer-off"></i> Solicitar Abono</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body pl-0 pr-0 ml-0 mr-0">

                <div class="row p-0 m-0">
                    <div class="col-12">
						<h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tipo de Abono</th>
                                    <th>Parcial</th>
                                    <th width="80">Horas</th>
                                    <th>Inicio Abono</th>
                                    <th>Término Abono</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <button data-btn-add class="btn btn-outline-success waves-effect waves-light btn-block" type="button" onclick="incluirNovoAbono()"><i class="mdi mdi-plus-circle-outline"></i> Adicionar abono</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="return solicitarAbono()">Enviar Solicitação de Abono <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_abono -->
<!-- modal modal_justificativa_extra -->
<div class="modal modal_justificativa_extra" tabindex="-1" role="dialog" aria-labelledby="modal_justificativa_extra" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-comment-check-outline"></i> Justificativa de Extra</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <hr>
                        <div class="form-group row mb-1">
                            <label for="data_batida" class="col-sm-3 col-form-label text-left-sm text-right pb-1">Justificativa:</label>
                            <div class="col-sm-9">
                                <select id="justificativa_extra" name="justificativa_extra" class="form-control">
                                    <option value="">...</option>
                                    <?php if($resJustificativaExtra ?? false): ?>
                                        <?php foreach($resJustificativaExtra as $key => $Justificativa): ?>
                                            <option value="<?= $Justificativa['id']; ?>"><?= $Justificativa['descricao']; ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="return justificarExtra()">Salvar<i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_justificativa_extra -->
<!-- modal modal_altera_atitude -->
<div class="modal modal_altera_atitude" tabindex="-1" role="dialog" aria-labelledby="modal_altera_atitude" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-dialog-full">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title mt-0 text-white"><i class="mdi mdi-calendar-question"></i> Altera Atitude</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">
                        
                        <h3 data-h-data class="m-0"></h3>
                        <h5 data-h-escala class="h7 m-0"></h5>
						<h6 data-h-batidas class="h7 m-0"></h6>
                        <hr>
                        <div class="form-group row mb-1">
                            <table class="table table-sm">
								<thead>
									<tr>
										<th>Data</th>
										<th>Total de Horas</th>
										<th>Atitude</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><input data-atitude-data type="date" disabled class="form-control form-control-sm"></td>
										<td><input data-atitude-horas type="time" disabled class="form-control form-control-sm"></td>
										<td>
											<select data-atitude-atitude class="form-control form-control-sm">
												<option value="">...</option>
												<option value="1">Banco de Horas</option>
												<option value="0">Falta confirmada</option>
											</select>
										</td>
									</tr>
								</tbody>
							</table>
                        </div>
						<div class="row mb-2" data-exibe-anexo-atitude>
							<div class="col-12">
								<label><i class="fa fa-paperclip" aria-hidden="true"></i> Anexo:</label>
								<input type="file" name="anexo_atitude" class="form-control">
							</div>
						</div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="return salvarAtitude()">Salvar Atitude <i class="fas fa-check"></i></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_altera_atitude -->

<!-- modal modal_macro -->
<div class="modal modal_macro" tabindex="-1" role="dialog" aria-labelledby="modal_macro" aria-hidden="true" data-keyboard="false" data-backdrop="static" style="width: auto !important;">
    <div class="modal-dialog modal-dialog-full">
        <div class="modal-content modal-content-full">
            <div class="modal-header bg-dark">
                <h5 class="modal-title mt-0 text-white"><i class="fa fa-cogs"></i> Consulta Macro</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><i class="mdi mdi-close-circle-outline"></i></button>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-12">

						<h3 class="m-1 text-right" style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <span><button type="button" id="dia_anterior" class="text-primary" style="border: none; background: none;"><i class="fa fa-arrow-left"></i></button></span>
                            <span><div id="data_macro">-</div></span>
                            <span><button id="dia_posterior" type="button" class="text-primary" style="border: none; background: none;"><i class="fa fa-arrow-right"></i></button></span>
                        </h3>
                        <h3 class="m-0"><?= $resFuncionario[0]['CHAPA'].' - '.$resFuncionario[0]['NOME']; ?></h3>
                        <h5 data-h-escala class="h7 mt-0"></h5>

                        <table class="table table-sm" id="table_macro">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Placa</th>
                                    <th>Início</th>
                                    <th>Fim</th>
                                    <th>Tempo</th>
                                    <th>Origem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        
                    </div>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- modal modal_macro -->
 <!-- Modal Observação -->
<div class="modal" id="modalObservacaoExtra" tabindex="-1" role="dialog" aria-labelledby="modalObservacaoExtraLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <h5 class="modal-title" id="modalObservacaoExtraLabel">Observação</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span class="fa fa-times"></span>
        </button>
      </div>
      <div class="modal-body">
        <textarea id="campoObservacaoExtra" class="form-control" rows="4" placeholder="Digite sua observação aqui..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" id="btnSalvarObservacaoExtra">Salvar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Observação -->
<script>
$(function() {
	$(".modal").draggable();
});


const carregaMacro = (data_macro = data_nova_batida ) => {

	console.log(data_macro);
	const dateString = data_macro;
	const { nextDate, previousDate } = getNextAndPreviousDates(dateString);
	console.log(previousDate, nextDate);

	const [year, month, day] = dateString.split("-");
	$('#data_macro').html(`${day}/${month}/${year}`);

	$('#table_macro tbody').html('');
	$('#dia_anterior').attr("onClick","carregaMacro('"+previousDate+"')");
	$('#dia_posterior').attr("onClick","carregaMacro('"+nextDate+"')");

	$.ajax({
		url: "<?= base_url('ponto/espelho/action/carrega_macro') ?>",
		type: 'POST',
		data: {
			"chapa"            : chapaFunc,
			"cpf"              : cpfFunc,
			"data"             : data_macro,
			"inicio_escala"    : inicio_escala,
			"termino_escala"   : termino_escala
		},
		success: function(result) {

			openLoading(true);

			try {
				var response = JSON.parse(result);
				response.map(function(ponto, i){
					console.log(ponto);
					$('#table_macro tbody').append(`
						<tr>
							<td>${ponto.status}</td>
							<td>${ponto.placa}</td>
							<td>${ponto.data_inicio_status}</td>
							<td>${ponto.data_fim_status}</td>
							<td>${ponto.tempo}</td>
							<td>${ponto.tipo_mensagem}</td>
						</tr>
					`);
				});
				

			} catch (e) {
				exibeAlerta('error', '<b>Erro interno:</b> ' + e);
			}

		},
	});

	$('.modal_macro').modal('show');
}
const carregaFuncionario = () => {

openLoading();
var codSecao = $("[data-secao]").val();
$("#funcionario").html('<option value="">-- selecione um colaborador --</option>').trigger('change');
if(codSecao == ''){
	codSecao = 'all';
}

$.ajax({
	url: "<?= base_url('ponto/aprova/action/listar_funcionarios_secao') ?>",
	type: 'POST',
	data: {
		'codsecao': codSecao
	},
	success: function(result) {

		openLoading(true);

		try {
			var response = JSON.parse(result);
			$("#funcionario").html('<option value="">-- Todos ('+response.length+') --</option>').trigger('change');

			for(var x=0; x < response.length; x++){
				$("#funcionario").append('<option value="'+response[x].CHAPA+'">'+response[x].NOME+' - '+response[x].CHAPA+'</option>');
			}

		} catch (e) {
			exibeAlerta('error', '<b>Erro interno:</b> ' + e);
		}

	},
});

}

function getNextAndPreviousDates(dateString) {
    // Parse the input date string
    const date = new Date(dateString);

    // Ensure the date is valid
    if (isNaN(date.getTime())) {
        throw new Error("Invalid date format. Please use 'yyyy-mm-dd'.");
    }

    // Get the next date
    const nextDate = new Date(date);
    nextDate.setDate(date.getDate() + 1);

    // Get the previous date
    const previousDate = new Date(date);
    previousDate.setDate(date.getDate() - 1);

    // Format the dates back to 'yyyy-mm-dd'
    const formatDate = (d) => d.toISOString().split('T')[0];

    return {
        nextDate: formatDate(nextDate),
        previousDate: formatDate(previousDate)
    };
}

</script>
<script src="<?= base_url('public/assets/js/jquery-ui.js'); ?>"></script>
<style>
    .ht {
        margin-top: -1px !important;
    }

    .modal_incluir_batida,
    .modal_alterar_batida,
    .modal_abono {
        padding: 0 !important;
    }

    .modal-dialog-full {
        width: 100%;
        height: 100%;
        max-width: 900px;
        margin: auto;
        padding: 0;
    }

    .modal-content-full {
        height: auto;
        min-height: 100%;
        border-radius: 0;
    }

	.swal2-popup .swal2-title {
		background: #1ecab8; /* ou a cor do seu header, ex: var(--success) */
		color: #fff;
		padding: 12px 24px;
		border-top-left-radius: 2px;
		border-top-right-radius: 2px;
		font-size: 16px;
		text-align: left;
	}
</style>
<style>
    @media (max-width: 500px){
        .tablebatida [type="date"] {
            width: 90px !important;
            font-size: 12px !important;
            padding-left:0 !important;
            padding-right: 0 !important;
        }
        .tablebatida [type="time"],
        .tablebatida select {
            font-size: 12px !important;
            padding-left:0 !important;
            padding-right: 0 !important
        }
        .tablebatida td {
            padding: 1px !important;
        }
        .colbatida {
            width: 98px;
        }
    }
</style>