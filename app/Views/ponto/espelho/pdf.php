<?php if($objFuncionarios && $periodo): ?>
	<?php foreach($objFuncionarios as $idf => $value): ?>
	
		<div style="page-break-after: always;">
		<?php
		$dtini = date('d/m/Y', strtotime(substr($periodo,0,10)));
		$dtfim = date('d/m/Y', strtotime(substr($periodo,11,10)));
		
		$p_ini = strtotime(substr($periodo,0,10));
		$p_fim = strtotime(substr($periodo,11,10));
		
		$listBatidas = $objPonto->listaBatidas ( $objFuncionarios[$idf]->chapa, $dtini, $dtfim, $_SESSION["func_coligada"] );
		$listaDias = $objPonto->listaDiasBatidas ( $objFuncionarios[$idf]->chapa, $dtini, $dtfim, $_SESSION["func_coligada"] );
 		$listaMovimentos = $objPonto->listaMovimentos($objFuncionarios[$idf]->chapa, $dtini, $dtfim, $_SESSION["func_coligada"]);
		
		$objSaldoBancoHorasPer = $objPonto->getSaldoBancoHorasPer($objFuncionarios[$idf]->chapa, date('Y-m-d', ($p_ini)), date('Y-m-d', ($p_fim)));
		$objSaldoBancoHorasAnt = $objPonto->getSaldoBancoHorasPer($objFuncionarios[$idf]->chapa, date('Y-m-d', strtotime('-1 month', ($p_ini))), date('Y-m-d', strtotime('-1 month', ($p_fim))));
		
		
		$objSaldoBancoMesAnt = $objPonto->getSaldoBancoAnt($objFuncionarios[$idf]->chapa, $dtini);
		
		?>
		
		<table cellspacing="1" cellpadding="2" bgcolor="#000000" width="100%" style="margin-bottom: 3px;">
			<tr>
				<td bgcolor="#ffffff" width="120"><img width="120" border="0" src="http://www.eldoradobrasil.com.br/Content/img/logo-eldorado.png" alt="ELDORADO"></td>
				<td bgcolor="#ffffff" align="center">
					<b style="font-size: 20px;">Espelho Ponto</b><br>
					<b>Período:</b> <?= $dtini.' à '.$dtfim; ?>
				</td>
			</tr>
		</table>
		
		<table cellspacing="1" cellpadding="2" bgcolor="#000000" width="100%" style="margin-bottom: 5px;">
			<tr>
				<td bgcolor="#ffffff"><b style="display: block; font-size: 10px !important; padding-bottom: 3px;">Nome:</b><?= $objFuncionarios[$idf]->nome.' - '.$objFuncionarios[$idf]->chapa; ?></td>
				<td bgcolor="#ffffff"><b style="display: block; font-size: 10px !important; padding-bottom: 3px;">Função:</b><?= $objFuncionarios[$idf]->nomefuncao.' - '.$objFuncionarios[$idf]->codfuncao; ?></td>
			</tr>
			<tr>
				<td bgcolor="#ffffff"><b style="display: block; font-size: 10px !important; padding-bottom: 3px;">Seção:</b><?= $objFuncionarios[$idf]->nomesecao.' - '.$objFuncionarios[$idf]->codsecao; ?></td>
				<td bgcolor="#ffffff"><b style="display: block; font-size: 10px !important; padding-bottom: 3px;">Horário:</b><?= $objFuncionarios[$idf]->nomehorario; ?></td>
			</tr>
		</table>
		
		<table cellspacing="1" cellpadding="3" width="100%" style="margin-bottom: 15px;">
			<tr class='tbadmlistacab'>
				<td width="80">Data</td>
				<td width="100">Dia. Sem.</td>
				<td width="40">Ent</td>
				<td width="40">Sai</td>
				<td width="40">Ent</td>
				<td width="40">Sai</td>
				
				<td width="40">Ent</td>
				<td width="40">Sai</td>
				<!--
				<td width="40">Ent</td>
				<td width="40">Sai</td>
				
				
				<td width="40">Ent</td>
				<td width="40">Sai</td>
				<td width="40">Ent</td>
				<td width="40">Sai</td>
				-->
				
				<td width="40">Hr. Trab.</td>
				<td width="40">Atraso</td>
				<td width="40">Falta</td>
				<td width="40">Extra</td>
			</tr>
			
			<?php
	if ($listaDias) {	
		
		$geraID = 1;
		foreach ( $listaDias as $d => $value ) {
			$dtapont = date ( 'd/m/Y', strtotime ( $listaDias [$d]->data ) );
			$dtapontF = date ( 'd/m/Y', strtotime ( $listaDias [$d]->data ) );
			
			// VERIFICA SE O DIA É FERIADO
			/*$objDiaFeriado = $objPonto->diaFeriado($funcLido->cod, $_SESSION['func_coligada'], $dtapont);
			if($objDiaFeriado) $dtapontF = '<b style="color:red">'.$dtapontF.'<br>Feriado</b>';*/
			
			$ferias = $objPonto->verificarFerias($listaDias [$d]->data, $listaDias[$d]->funcid);
			$abono_verify = $objPonto->verificarAbono($listaDias [$d]->data, $listaDias[$d]->funcid);
			$verifica_afastamento = $objPonto->verificaAfastamento($listaDias[$d]->funcid, $listaDias [$d]->data);
			
			echo "<tr class='tbadmlistalin'>";
			echo "<td align='center'>" . $dtapont . "</td>";
			echo "<td>" . $diasem [date ( 'w', strtotime ( $listaDias [$d]->data ) )] . "</td>";
			$b0 = "";
			$b1 = "";
			$b2 = "";
			$b3 = "";
			$b4 = "";
			$b5 = "";
			$b6 = "";
			$b7 = "";
			$b8 = "";
			$b9 = "";
			$b10 = "";
			$b11 = "";
			
			
			if ($listBatidas) {
				
				$coluna = -1;
				foreach ( $listBatidas as $b => $value ) {
					$dtbat = date ( 'd/m/Y', strtotime ( $listBatidas [$b]->data2 ) );
					
					if ($dtbat == $dtapont) {
						
						if($listBatidas [$b]->natureza == 0){
							$coluna++;
							
							switch ($coluna) {
							case 0 :
								$b0 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat0 = $listBatidas [$b]->id;
								break;
							case 1 :
								$b1 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat1 = $listBatidas [$b]->id;
								break;
							case 2 :
								$b2 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat2 = $listBatidas [$b]->id;
								break;
							case 3 :
								$b3 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat3 = $listBatidas [$b]->id;
								break;
							case 4 :
								$b4 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat4 = $listBatidas [$b]->id;
								break;
							case 5 :
								$b5 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat5 = $listBatidas [$b]->id;
								break;
							case 6 :
								$b6 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat6 = $listBatidas [$b]->id;
								break;
							case 7 :
								$b7 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat7 = $listBatidas [$b]->id;
								break;
							case 8 :
								$b8 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat8 = $listBatidas [$b]->id;
								break;
							case 9 :
								$b9 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat9 = $listBatidas [$b]->id;
								break;
							case 10 :
								$b10 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat10 = $listBatidas [$b]->id;
								break;
							case 11 :
								$b11 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat11 = $listBatidas [$b]->id;
								break;
							}
							
							
						}else{
							$coluna++;
							
							if($coluna == 0){
								$coluna = 1;
							}
							
							switch ($coluna) {
							case 0 :
								$b0 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat0 = $listBatidas [$b]->id;
								break;
							case 1 :
								$b1 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat1 = $listBatidas [$b]->id;
								break;
							case 2 :
								$b2 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat2 = $listBatidas [$b]->id;
								break;
							case 3 :
								$b3 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat3 = $listBatidas [$b]->id;
								break;
							case 4 :
								$b4 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat4 = $listBatidas [$b]->id;
								break;
							case 5 :
								$b5 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat5 = $listBatidas [$b]->id;
								break;
							case 6 :
								$b6 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat6 = $listBatidas [$b]->id;
								break;
							case 7 :
								$b7 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat7 = $listBatidas [$b]->id;
								break;
							case 8 :
								$b8 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat8 = $listBatidas [$b]->id;
								break;
							case 9 :
								$b9 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat9 = $listBatidas [$b]->id;
								break;
							case 10 :
								$b10 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat10 = $listBatidas [$b]->id;
								break;
							case 11 :
								$b11 = sprintf("%05s",m2h ( $listBatidas [$b]->batida ));
								$idbat11 = $listBatidas [$b]->id;
								break;
							}
							
						}
						
					}
					
				}
			}
			
			$id0 = false;
			$id1 = false;
			$id2 = false;
			$id3 = false;
			$id4 = false;
			$id5 = false;
			$id6 = false;
			$id7 = false;
			$id8 = false;
			$id9 = false;
			$id10 = false;
			$id11 = false;
			
			if($ferias || $abono_verify || $verifica_afastamento || (($listaDias [$d]->compensado > 0 || $listaDias [$d]->descanso > 0) && $listaDias[$d]->htrab == 0 )){
				if($listaDias [$d]->compensado > 0){
					echo '<td colspan="4" align="center">  </td>';
					echo '<td colspan="2" align="center"> COMPENSADO </td>';
					
		
					
					
				}else if($listaDias [$d]->descanso > 0) {
					
					echo '<td colspan="4" align="center">  </td>';
					echo '<td colspan="2" align="center"> DESCANSO </td>';
					

					
				}else if($ferias){
					echo '<td colspan="4" align="center">  </td>';
					echo '<td colspan="2" align="center" style="color: red;"> FÉRIAS </td>';
				}else if($abono_verify){
					
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_1' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b0)) > 0 ? '1' : '')."' data-id='".$id0."' ".(($id0) ? ' class="batida_inserida" ' : '')." >" . $b0 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_2' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b1)) > 0 ? '1' : '')."' data-id='".$id1."' ".(($id1) ? ' class="batida_inserida" ' : '')."  >" . $b1 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_3' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b2)) > 0 ? '1' : '')."' data-id='".$id2."' ".(($id2) ? ' class="batida_inserida" ' : '')."  >" . $b2 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_4' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b3)) > 0 ? '1' : '')."' data-id='".$id3."' ".(($id3) ? ' class="batida_inserida" ' : '')."  >" . $b3 . "</td>";
				
					echo '<td colspan="2" align="center"> '. $abono_verify .' </td>';
				}else if($verifica_afastamento){
					echo '<td colspan="6" align="center" style="color: red;"> '. $verifica_afastamento .' </td>';
				}
				
				
			}else{
			
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_1' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b0)) > 0 ? '1' : '')."' data-id='".$id0."' ".(($id0) ? ' class="batida_inserida" ' : '')." >" . $b0 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_2' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b1)) > 0 ? '1' : '')."' data-id='".$id1."' ".(($id1) ? ' class="batida_inserida" ' : '')."  >" . $b1 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_3' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b2)) > 0 ? '1' : '')."' data-id='".$id2."' ".(($id2) ? ' class="batida_inserida" ' : '')."  >" . $b2 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_4' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b3)) > 0 ? '1' : '')."' data-id='".$id3."' ".(($id3) ? ' class="batida_inserida" ' : '')."  >" . $b3 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_5' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b4)) > 0 ? '1' : '')."' data-id='".$id4."' ".(($id4) ? ' class="batida_inserida" ' : '')."  >" . $b4 . "</td>";
				echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_6' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b5)) > 0 ? '1' : '')."' data-id='".$id5."' ".(($id5) ? ' class="batida_inserida" ' : '')."  >" . $b5 . "</td>";
				#echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_7' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b6)) > 0 ? '1' : '')."' data-id='".$id6."' ".(($id6) ? ' class="batida_inserida" ' : '')."  >" . $b6 . "</td>";
				#echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_8' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b7)) > 0 ? '1' : '')."' data-id='".$id7."' ".(($id7) ? ' class="batida_inserida" ' : '')."  >" . $b7 . "</td>";
				#echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_9' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b8)) > 0 ? '1' : '')."' data-id='".$id8."' ".(($id8) ? ' class="batida_inserida" ' : '')."  >" . $b8 . "</td>";
				#echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_10' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b9)) > 0 ? '1' : '')."' data-id='".$id9."' ".(($id9) ? ' class="batida_inserida" ' : '')." >" . $b9 . "</td>";
				#echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_11' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b10)) > 0 ? '1' : '')."' data-id='".$id10."' ".(($id10) ? ' class="batida_inserida" ' : '')." >" . $b10 . "</td>";
				#echo "<td data-bat='".date('Ymd', strtotime($listaDias [$d]->data))."_12' data-data='".date('d/m/Y', strtotime($listaDias [$d]->data))."' data-rm='".(strlen(trim($b11)) > 0 ? '1' : '')."' data-id='".$id11."' ".(($id11) ? ' class="batida_inserida" ' : '')." >" . $b11 . "</td>";
				
			}
			echo "<td teste>" . m2h ( $listaDias [$d]->htrab ) . "</td>";
			echo "<td>" . m2h ( $listaDias [$d]->atraso ) . "</td>";
			echo "<td>" . m2h ( $listaDias [$d]->falta ) . "</td>";
			echo "<td>" . m2h ( $listaDias [$d]->extraautorizado ) . "</td>";
			
			echo "</tr>";
			
		}
		
		
	}
	?>
</table>

<?php

$saldo_liquido = ($objSaldoBancoMesAnt) + ($objSaldoBancoHorasPer[0]->saldo);

?>

<table>
	<tr>
		<td align="center"><b>Saldo de Banco Período Anterior</b></td>
		<td align="center" style="padding-left: 25px;"><b>Saldo de Banco Atual</b></td>
	</tr>
	<tr>
		<td align="center"><b><?= ($objSaldoBancoMesAnt >= 0) ? '<span style="color: green;">+'.m2h($objSaldoBancoMesAnt).'</span>' : '<span style="color: red;">-'.m2h($objSaldoBancoMesAnt).'</span>'; ?></b></td>
		<td align="center" style="padding-left: 25px;"><b><?= ($saldo_liquido >= 0) ? '<span style="color: green;">+'.m2h($saldo_liquido).'</span>' : '<span style="color: red;">'.m2h($saldo_liquido).'</span>'; ?></b></td>
	</tr>
</table>

<strong>Eventos do Movimento no período:</strong>
<br><br>
<?php if($listaMovimentos){
	foreach($listaMovimentos as $idx => $value){
		if(trim($listaMovimentos[$idx]->jornada) == "X") echo "<br><strong>Horário do Funcionário</strong><br><br>";
		echo "<div style='float: left; width: 60px;border-bottom: 1px dotted #000000;'>" . $listaMovimentos[$idx]->codigo . "</div>";
		if(trim($listaMovimentos[$idx]->jornada) == "X")
			echo "<div style='float: left; width: 500px; margin-left: 10px;border-bottom: 1px dotted #000000;'>";
		else 
			echo "<div style='float: left; width: 300px; margin-left: 10px;border-bottom: 1px dotted #000000;'>";
		echo $listaMovimentos[$idx]->descricao . "</div>";
		echo "<div style='float: left; width: 100px; margin-left: 10px;border-bottom: 1px dotted #000000;'>";
		if(!$listaMovimentos[$idx]->jornada && trim($listaMovimentos[$idx]->jornada) != "X") echo m2h($listaMovimentos[$idx]->minutos);
		echo "</div>";
		echo "<br style='clear: both;' />";
	}
}else{
	echo "<div style='float: left; width: 300px;'>Sem eventos.</div>";
	}
?>

<br>
<br>
<br>

<b>Data:</b> ____/____/________ ____________________________________________________________<br>
<span style="padding-left: 140px;"><?= $objFuncionarios[$idf]->nome; ?></span>
		
		
</div>
	<?php endforeach; ?>
<script>window.print();</script>

<?php endif; ?>