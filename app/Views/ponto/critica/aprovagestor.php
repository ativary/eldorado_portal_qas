
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



              </div>
									
               

                <div class="card-footer text-center">
									 <?php echo $dados['ck_ExtraExecutado']; ?>
                    <button class="btn btn-success" id="btnfiltro" onclick="return Filtro()"><i class="fas fa-check"></i> Filtrar</button>
                </div>

            </div>
        </div>

    </div>
    </div>




    <div class="row">
      <div class="col-md-12 col-lg-12">
          <div class="card">
              <div class="card-body">
              
                  <div class="table-responsive">
                    <table cellspacing="0" cellpadding="0" id="tb-ponto-critica" class="table table-striped table-bordered">
                      <thead>
                        <tr class="text-center">
                          <td width="40">Nº</td>
                					<td width="40">Chapa</td>
                					<td width="20">Loja</td>
                					<td>Nome</td>
                					
                					<td width="40">&nbsp;</td>
                					<td>Data</td>
                					<td>Ent1</td>
                					<td>Sai1</td>
                					<td>Ent2</td>
                					<td>Sai2</td>
                					<td>Ent3</td>
                					<td>Sai3</td>
                					<td>Ent4</td>
                					<td>Sai4</td>


                          <?php if($dados['ck_semPar']){ ?><td width="40">S/ Par.<br> Corresp.</td><?php } ?>
                      		<?php if($dados['ck_ExtraExecutado']){ ?><td width="40">Extra <br>Executado</td><?php } ?>
                      		<?php if($dados['ck_Atrasos']){ ?><td width="40">Atrasos</td><?php } ?>
                      		<?php if($dados['ck_Faltas']){ ?><td width="40">Faltas</td><?php } ?>
                      		<?php if($dados['ck_jorMaior10']){ ?><td width="40">Jor. > 10h</td><?php } ?>
                      		<?php if($dados['ck_interjornada']){ ?><td width="40">Interjornada</td><?php } ?>
													
                        </tr>
                      </thead>
                      <tbody>
                        <?php

												
												if($dados['resData']){
                          $html = '';
                          $nLinha = 1;
													
													foreach($dados['resData'] as $idbData => $value ){
                            $i = $idbData;
														$dataModal = $resData[$i]['DATA'];
							
							if($dados['ck_Faltas'] && !$dados['ck_semPar'] ) {
								if(((int)$resData[$i]['FALTA_CASE'] > 0 && (int)$resData[$i]['BATIDAS_PORTAL'] > 0 && (int)$resData[$i]['SEM_PAR_CORRESPONDENTE'] > 0)){
									continue;
								}
							}


                            $b1 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'a'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'0\')" readonly type="text" name="a'.$idbData.'" id="a'.$idbData.'" size="5" maxlength="5">';
                    				$b2 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'b'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="b'.$idbData.'" id="b'.$idbData.'" size="5" maxlength="5">';
                    				$b3 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'c'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'0\')" readonly type="text" name="c'.$idbData.'" id="c'.$idbData.'" size="5" maxlength="5">';
                    				$b4 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'d'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="d'.$idbData.'" id="d'.$idbData.'" size="5" maxlength="5">';
                    				$b5 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'e'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'0\')" readonly type="text" name="e'.$idbData.'" id="e'.$idbData.'" size="5" maxlength="5">';
                    				$b6 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'f'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="f'.$idbData.'" id="f'.$idbData.'" size="5" maxlength="5">';
                    				$b7 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'g'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'0\')" readonly type="text" name="g'.$idbData.'" id="g'.$idbData.'" size="5" maxlength="5">';
                    				$b8 = '<input data-value value="" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'h'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="h'.$idbData.'" id="h'.$idbData.'" size="5" maxlength="5">';


                            $coluna = 1;
                            foreach ( $dados['resBatidas'] as $idb => $value ):
                              $bat_data = $resBatidas[$idb]['DATAREFERENCIA'];
                              $bat_chapa = $resBatidas[$idb]['CHAPA'];
                              $bat_natureza = $resBatidas[$idb]['NATUREZA'];
                              $bat_idaafdt = $resBatidas[$idb]['IDAAFDT'];
                              $bat_batida = $resBatidas[$idb]['BATIDA'];

                              if(strlen(trim($resBatidas[$idb]['DATAREFERENCIA2'])) > 0){
                      					$bat_data2 = date('d/m/Y', strtotime($resBatidas[$idb]['DATAREFERENCIA2']));
                      				}else{
                      					$bat_data2 = "Não pode ficar em branco";
                      				}

                              if($bat_data == $resData[$i]['DATA'] && $bat_chapa == $resData[$i]['CHAPA']){
								  
									
                  							if($coluna > 8) $coluna = 1;

                  							if($bat_natureza == 0){

                  								switch($coluna){
                  									case 1:
                  										$b1 = '<span id="a'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'a'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 2;
                  										break;
                  									case 2:
                  										$b3 = '<span id="c'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'c'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 4;
                  										break;
                  									case 3:
                  										$b3 = '<span id="c'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'c'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										break;
                  									case 4:
                  										$b5 = '<span id="e'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'e'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 6;
                  										break;
                  									case 5:
                  										$b5 = '<span id="e'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'e'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 6;
                  										break;
                  									case 6:
                  										$b7 = '<span id="g'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'g'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 8;
                  										break;
                  									case 7:
                  										$b7 = '<span id="g'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'g'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 8;
                  										break;
                  									case 8:
                  										$b9 = '<span id="i'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'i'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 1;
                  										break;
                  								}

                  							}else{



                  								switch($coluna){
                  									case 1:
                  										$b2 = '<span id="b'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'b'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 2;
                  										break;
                  									case 2:
                  										$b2 = '<span id="b'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'b'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 3;
                  										break;
                  									case 3:
                  										$b4 = '<span id="d'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'d'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 5;
                  										break;
                  									case 4:
                  										$b4 = '<span id="d'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'d'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										break;
                  									case 5:
                  										$b6 = '<span id="f'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'f'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 7;
                  										break;
                  									case 6:
                  										$b6 = '<span id="f'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'f'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										break;
                  									case 7:
                  										$b8 = '<span id="h'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'h'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										$coluna = 8;
                  										break;
                  									case 8:
                  										$b8 = '<span id="h'.$idbData.'" style="cursor: pointer;" ondblclick="abreModalAltera(\'\',\''.sprintf("%05s",m2h($bat_batida)).'\',\''.$resData[$i]['CHAPA'].'\', \''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'h'.$idbData.'\', \''.$bat_idaafdt.'\', \''.$bat_natureza.'\',\''.$bat_data2.'\')">'.sprintf("%05s",m2h($bat_batida)).'</span>';
                  										break;
                  								}

                  							}

                  						}

                            endforeach;
														if($dados['resBatidasApontadas']){
                  					foreach($dados['resBatidasApontadas'] as $idba => $value):
                              $batapt_chapa = $resBatidasApontadas[$idba]['chapa'];
                              $batapt_dtponto = $resBatidasApontadas[$idba]['dtponto'];

                  						if(
                  							$batapt_chapa == $resData[$i]['CHAPA'] &&
                  							date('d/m/Y', strtotime($batapt_dtponto)) == date('d/m/Y', strtotime($resData[$i]['DATA']))
                  							){

                  								#	[MOVIMENTO]
                  								#	1 - Inclusão de batida
                  								#	2 - Exclusão de batida
                  								#	3 - Altera natureza
                  								#	4 - Altera Jornada Referencia
                  								#	5 - Abonos Atrasos
                  								#	6 - Abonos Faltas
                  								#	7 - Altera Atitude

                  								#################### ent1
                  								if($resBatidasApontadas[$idba]['ent1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b1 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['ent1']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'a'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'0\')" readonly type="text" name="a'.$idbData.'" id="a'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b1 = '<span class="batida_del">'.$b1.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b1 = '<span class="batida_nat">'.$b1.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b1 = '<span class="batida_data">'.$b1.'</span>';
                  								}
                  								#################### sai1
                  								if($resBatidasApontadas[$idba]['sai1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b2 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['sai1']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'b'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="b'.$idbData.'" id="b'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b2 = '<span class="batida_del">'.$b2.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b2 = '<span class="batida_nat">'.$b2.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai1'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b2 = '<span class="batida_data">'.$b2.'</span>';
                  								}
                  								#################### ent2
                  								if($resBatidasApontadas[$idba]['ent2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b3 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['ent2']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'c'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'0\')" readonly type="text" name="c'.$idbData.'" id="c'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b3 = '<span class="batida_del">'.$b3.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b3 = '<span class="batida_nat">'.$b3.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b3 = '<span class="batida_data">'.$b3.'</span>';
                  								}
                  								#################### sai2
                  								if($resBatidasApontadas[$idba]['sai2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b4 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['sai2']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'d'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="d'.$idbData.'" id="d'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b4 = '<span class="batida_del">'.$b4.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b4 = '<span class="batida_nat">'.$b4.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai2'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b4 = '<span class="batida_data">'.$b4.'</span>';
                  								}
                  								#################### ent3
                  								if($resBatidasApontadas[$idba]['ent3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b5 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['ent3']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'e'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="d'.$idbData.'" id="d'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b5 = '<span class="batida_del">'.$b5.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b5 = '<span class="batida_nat">'.$b5.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b5 = '<span class="batida_data">'.$b5.'</span>';
                  								}
                  								#################### sai3
                  								if($resBatidasApontadas[$idba]['sai3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b6 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['sai3']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'f'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="f'.$idbData.'" id="f'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b6 = '<span class="batida_del">'.$b6.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b6 = '<span class="batida_nat">'.$b6.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai3'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b6 = '<span class="batida_data">'.$b6.'</span>';
                  								}
                  								#################### ent4
                  								if($resBatidasApontadas[$idba]['ent4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b7 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['ent4']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'g'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'0\')" readonly type="text" name="g'.$idbData.'" id="g'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b7 = '<span class="batida_del">'.$b7.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b7 = '<span class="batida_nat">'.$b7.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['ent4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b7 = '<span class="batida_data">'.$b7.'</span>';
                  								}
                  								#################### sai4
                  								if($resBatidasApontadas[$idba]['sai4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 1){
                  									$b8 = '<input data-value value="'.m2h($resBatidasApontadas[$idba]['sai4']).'" ondblclick="abreModalInsere(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\', \'h'.$idbData.'\', \''.$resData[$i]['CHAPA'].'\', \'1\')" readonly type="text" name="h'.$idbData.'" id="h'.$idbData.'" size="5" maxlength="5">';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 2){
                  									$b8 = '<span class="batida_del">'.$b8.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 3){
                  									$b8 = '<span class="batida_nat">'.$b8.'</span>';
                  								}else
                  								if($resBatidasApontadas[$idba]['sai4'] > 0 && $resBatidasApontadas[$idba]['movimento'] == 4){
                  									$b8 = '<span class="batida_data">'.$b8.'</span>';
                  								}
                  								//break;

                  							}
                    				endforeach;
													}
                            $html .= '<tr class="tbadmlistalin">';

                    				$html .= '<td align="center">'.$nLinha.'</td>';
                    				$html .= '<td align="center">'.$resData[$i]['CHAPA'].'</td>';
                    				$html .= '<td align="center">'.sprintf("%02s",$resData[$i]['CODFILIAL']).'</td>';
                    				$html .= '<td>'.$resData[$i]['NOME'].'</td>';
                    				$html .= '<td align="center">'.$resData[$i]['CODTIPO'].'</td>';
                    				
                    				$html .= '<td>'.date('d/m/Y', strtotime($resData[$i]['DATA'])).'</td>';

                    				$html .= '<td align="center">'.$b1.'</td>';
                    				$html .= '<td align="center">'.$b2.'</td>';
                    				$html .= '<td align="center">'.$b3.'</td>';
                    				$html .= '<td align="center">'.$b4.'</td>';
                    				$html .= '<td align="center">'.$b5.'</td>';
                    				$html .= '<td align="center">'.$b6.'</td>';
                    				$html .= '<td align="center">'.$b7.'</td>';
                    				$html .= '<td align="center">'.$b8.'</td>';
                           /*
                            if ($dados['ck_Faltas']) {
                    					// VERIFICA JUSTIFICATIVA FALTA
                    					$objFalta = verifyAtitudeFalta($resData[$i]['CHAPA'], $resData[$i]['DATA']);
                    					$corFalta = '';
                    					$titleFalta = '';
                    					$txtTitle = '';
                    					if(strlen($objFalta) > 0) {
                    						if($objFalta == '0'){
                    							$txtTitle = 'Descontado';
                    						} elseif ($objFalta == '1') {
                    							$txtTitle = 'Compensado';
                    						} else {
                    							$txtTitle = '';
                    						}
                    						$corFalta = 'style="background-color: green; color: white"';
                    					}

                    					$titleFalta = ' title="'.$txtTitle.'"';
                    				}*/



                    				if($dados['ck_semPar'])
                              $html .= '<td title="'.$resData[$i]['SEM_PAR_CORRESPONDENTE_DESC'].'" align="center">'.( ($resData[$i]['SEM_PAR_CORRESPONDENTE']) ? '<i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '' ).'</td>';
                    				if($dados['ck_ExtraExecutado'])
                              $html .= '<td align="center">'.( ($resData[$i]['EXTRAEXECUTADO_CASE'] ) ? m2h($resData[$i]['EXTRAEXECUTADO_CASE']) : '' ).'</td>';
                    				if($dados['ck_Atrasos'])
                              $html .= '<td align="center">'.( ($resData[$i]['ATRASO_CASE'] ) ? '<span style="cursor: pointer;" ondblclick="InsereAbonoAtraso(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\',\''.$resData[$i]['CHAPA'].'\',\''.m2h($resData[$i]['FALTA_CASE']).'\', \''.$resData[$i]['NOME'].'\')">'.m2h($resData[$i]['ATRASO_CASE']).'</span>' : '' ).'</td>';
                    				if($dados['ck_Faltas'])
                              $html .= '<td align="center">'.( ($resData[$i]['FALTA_CASE'] && $resData[$i]['BATIDAS_PORTAL'] == 0) ? '<span style="cursor: pointer;" ondblclick="InsereAbonoFalta(\''.date('d/m/Y', strtotime($resData[$i]['DATA'])).'\',\''.$resData[$i]['CHAPA'].'\',\''.m2h($resData[$i]['FALTA_CASE']).'\', \''.$resData[$i]['NOME'].'\')">'.m2h($resData[$i]['FALTA_CASE']).'</span>' : '' ).'</td>';
                    				if($dados['ck_jorMaior10'])
                              $html .= '<td title="'.$resData[$i]['JORNADA_MAIOR_10HORAS_DESC'].'" align="center">'.( ($resData[$i]['JORNADA_MAIOR_10HORAS']) ? '<i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '' ).'</td>';
                    				if($dados['ck_interjornada'])
                              $html .= '<td title="'.$resData[$i]['INTERJORNADA_DESC'].'" align="center">'.( ($resData[$i]['INTERJORNADA']) ? '<i class="fas fa-exclamation-triangle" style="color: red;"></i>' : '' ).'</td>';

                    				$html .= '</tr>';

                    				$nLinha++;

													}

                          echo $html;
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




<script>
$(document).ready(function () {

	$('[mask-batida]').mask("99:99");

	$('#tb-ponto-critica').DataTable({
        "aLengthMenu"   : [[25, 50, 100, 200, -1], [25, 50, 100, 200, "Todos"]],
        "iDisplayLength": -1,
        "aaSorting"     : [[0, "desc"]],
        "fixedHeader"   : true
    });
});

//****************************************************
//******Modal
//****************************************************

function InsereAbonoFalta ($data,$chapa,$falta,$nome,$atraso){

	$("#abonoFaltaNome").val($nome);
	$("#abonoFaltaData").val($data);

	$("#modalAbonoFalta").modal();
}

function InsereAbonoAtraso ($data,$chapa,$falta,$nome,$atraso){
	
	$("#abonoAtrasoNome").val($nome);
	$("#abonoAtrasoData").val($data);

	$("#modalAbonoAtraso").modal();
}

function abreModalAltera ($a,$batida,$chapa,$data,$valor,$idaafdt,$natureza,$dataref){
	$("#alteraData").val($data);
	$("#alteraDataReferencia").val($dataref);
	$("#alteraHora").val($batida);

  $("#modalAltera").modal();
}

function abreModalInsere($data, $localizacao, $chapa, $tipo){
	
	//preenche modal
	$("#addNatureza").val($tipo);
	$("#addData").val($data);
	$("#addDataReferencia").val($data);

	//abre modal
	$("#modalAdiciona").modal();
}

//****************************************************
//******Filtro
//****************************************************

const Filtro = () => {
    

    let dados = {
				"periodo"          : $("#periodo").val(),
				"data_inicio"      : $("#data_inicio").val(),
				"data_fim"         : $("#data_fim").val(),
				"ck_ExtraExecutado": $("#ck_ExtraExecutado").val(),
				"ck_semPar"        : $("#ck_semPar").val(),
				"ck_Atrasos"       : $("#ck_Atrasos").val(),
				"ck_Faltas"        : $("#ck_Faltas").val(),
				"ck_jorMaior10"    : $("#ck_jorMaior10").val(),
				"ck_interjornada"  : $("#ck_interjornada").val(),
	
			
    }

    if(dados.periodo == "")			{ exibeAlerta("error", "<b>Período</b> não selecionado."); 		 return false; }
		if(dados.data_inicio == "") { exibeAlerta("error", "<b>Data inicio</b> não selecionada."); return false; }
		if(dados.data_fim == "")		{ exibeAlerta("error", "<b>Data fim</b> não selecionada.");		 return false; }

    openLoading();

    document.getElementById("form_filtro").submit();

}


//****************************************************
//******Passa dados para inserts 
//****************************************************

const AdicionarBatida = () => {

  let dados = {
     "addData"          : $("#addData").val(),
     "addDataReferencia": $("#addDataReferencia").val(),
     "addNatureza"      : $("#addNatureza").val(),
     "addBatida"        : $("#addBatida").val(),
     "addJustificativa" : $("#addJustificativa").val(),
     "textoJustOutros"  : $("#textoJustOutros").val(),
    }

    if(dados.addNatureza == "")				{ exibeAlerta("error", "Natureza não informada."); 			return false; }
    if(dados.addBatida == "")					{ exibeAlerta("error", "Batida não informada."); 				return false; }
    if(dados.addJustificativa == "")	{ exibeAlerta("error", "Justificativa não informada."); return false; }
		if(dados.addJustificativa == "outros" && dados.textoJustOutros == ""){ exibeAlerta("error", "Justificativa (outros) não especificada."); return false; }

    openLoading();

    $.ajax({
        url: "<?= base_url('Ponto/Critica/action/cadastrar_batida'); ?>",
        type:'POST',
        data:dados,
        success:function(result){

            openLoading(true);

            try{

              var response = JSON.parse(result);

              if(response.tipo == "success"){
                  exibeAlerta(response.tipo, response.msg, 3, '<?= base_url('Ponto/Critica'); ?>');
              }else{
                  exibeAlerta(response.tipo, response.msg);
               }

            } catch (e) {
              exibeAlerta('error', 'Erro interno: '+e);
            }
        },
    });

}

//****************************************************
//******Inputs dinâmicos
//****************************************************

$("#addJustificativa").on('change', function () {
	var value = $(this).val();

	if(value == 'Outros') {
		$("#textoJustOutros").show();
	} else {
		$("#textoJustOutros").hide();
	}
})

$("#ck_extraExecutado").on('change', function () {
	var value = $(this).val();

	if(value == 1) {
		$("#vl_extra_executado").show();
	} else {
		$("#vl_extra_executado").hide();
	}
})

</script>


<?php
loadPlugin(array('select2', 'datatable', 'mask'))
?>