<div class="container-fluid"> 
    <div class="row">

        <!-- main -->
        <div class="col-12">
            <div class="card noprint">
                <div class="card-header mt-0">
                    <div class="row">
                        <h4 class="col-12 mb-1 mt-1">Selecione o período</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <label for="perfil_nome" class="col-2 col-form-label text-right">Período:</label>
                        <div class="col-sm-10">

                            <form action="" method="post" name="form_holerite" id="form_holerite">
                                <select class="select2 custom-select form-control form-control-sm" name="periodo" id="periodo">
                                    <option value="">- selecione um período -</option>
                                    <?php if($resPeriodo): ?>
                                        <?php foreach($resPeriodo as $key => $DadosPeriodo): ?>
                                            <option value="<?= $DadosPeriodo['periodo'].'-'.$DadosPeriodo['mescomp'].'-'.$DadosPeriodo['anocomp']; ?>" <?= ($periodo == $DadosPeriodo['periodo'].'-'.$DadosPeriodo['mescomp'].'-'.$DadosPeriodo['anocomp']) ? " selected " : ""; ?>><?= $DadosPeriodo['mescomp'].'/'.$DadosPeriodo['anocomp'].' - '.descricaoHolerite($DadosPeriodo['periodo']); ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </form>

                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary btn-xxs" id="btnsave" onclick="return consultaHolerite()"><i class="fas fa-search"></i> Consultar</button>
                </div>
            </div>

            <div class="card">
                
                <div class="card-header mt-0 noprint">
                    <div class="row">
                    <h4 class="col-12 mb-1 mt-1"><?= $_titulo; ?></h4>
                    </div>
                </div>

                <div class="card-body">

                    <?= exibeMensagem(true); ?>

                    <?php if($HoleriteDadosEmpresa): ?>

                        <p class="text-center noprint">
                            <a href="javascript:window.print();" class="btn btn-primary"><i class="fas fa-print mr-2"></i> Imprimir</a>
                        </p>
                    
                    <table class="holerite" cellpadding="2">
                        <tr>
                            <td style="text-align: center;" width="148"><img src="<?= base_url("public/assets/images/logo-dark.png"); ?>" style="max-width: 148px; max-height: 40px;"></td>
                            <td style="text-align: center;">
                                <span class="font-11">Demostrativo de Pagamento</span>
                                <h3 class="mt-0 mb-0"><?= $HoleriteDadosEmpresa[0]['NOME']; ?></h3>
                                <footer class="blockquote-footer font-12"><?= $HoleriteDadosEmpresa[0]['CNPJ']; ?></footer>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center;">
                                <span class="font-11">R.E.:</span>
                                <h4 class="mt-0 mb-0"><?= $DadosFuncionario[0]['CHAPA']; ?></h4>
                            </td>
                            <td>
                                <span class="font-11">NOME:</span>
                                <h5 class="mt-0 mb-0"><?= $DadosFuncionario[0]['NOME']; ?></h5>
                            </td>
                        </tr>
                    </table>

                    <table class="holerite ht" cellpadding="2">
                        <tr>
                            <td style="text-align: center;">
                                <span class="font-11">FUNÇÃO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['NOMEFUNCAO']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">DATA ADMISSÃO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= dtBr($DadosFuncionario[0]['DATAADMISSAO']); ?></span>
                            </td>
                            <td>
                                <span class="font-11">ENDEREÇO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['RUA']; ?></span>
                            </td>
                        </tr>
                    </table>

                    <table class="holerite ht" cellpadding="2">
                        <tr>
                            <td style="text-align: center;">
                                <span class="font-11">BAIRRO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['BAIRRO']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">CEP:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['CEP']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">CIDADE:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['CIDADE']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">ESTADO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['ESTADO']; ?></span>
                            </td>
                        </tr>
                    </table>

                    <table class="holerite ht" cellpadding="2">
                        <tr>
                            <td style="text-align: center;">
                                <span class="font-11">PIS:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['PIS']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">CPF:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['CPF']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">IDENTIDADE:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['RG']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">DATA CRÉDITO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= dtBr($Holerite[0]['DTPAGTO']); ?></span>
                            </td>
                            <td>
                                <span class="font-11">REFERÊNCIA:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $referencia[1].'/'.$referencia[2]; ?></span>
                            </td>
                            <td>
                                <span class="font-11">SAL. FAM.</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= (int)$Holerite[0]['NRODEPENDSALFAMILIA']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">IR</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= (int)$Holerite[0]['NRODEPENDIRRF']; ?></span>
                            </td>
                        </tr>
                    </table>

                    <table class="holerite ht" cellpadding="2">
                        <tr>
                            <td colspan="2" rowspan="2" style="text-align: center;" width="50%">
                                <span class="font-11">SALÁRIO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($DadosFuncionario[0]['SALARIO']); ?></span>
                            </td>
                            <td colspan="3" style="background-color: #f0f0f0;">
                                <b class="font-14">LOCAL DO PAGAMENTO:</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="font-11">BANCO:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['PAGTO_BANCO']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">AGÊNCIA:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['PAGTO_AGENCIA']; ?></span>
                            </td>
                            <td>
                                <span class="font-11">CONTA:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= $DadosFuncionario[0]['PAGTO_CONTA']; ?></span>
                            </td>
                        </tr>
                    </table>

                    <table class="holerite ht" cellpadding="1">
                        <tr>
                            <td colspan="6" style="text-align: center; background-color: #f0f0f0;">
                                <b class="font-12">DISCRIMINAÇÃO DAS PARCELAS</b>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; background-color: #f0f0f0;"><b>Mês/Ano</b></td>
                            <td style="text-align: center; background-color: #f0f0f0;"><b>Evento</b></td>
                            <td style="text-align: center; background-color: #f0f0f0;"><b>Discriminação</b></td>
                            <td style="text-align: center; background-color: #f0f0f0;"><b>Ref.</b></td>
                            <td style="text-align: center; background-color: #f0f0f0;"><b>Proventos</b></td>
                            <td style="text-align: center; background-color: #f0f0f0;"><b>Descontos</b></td>
                        </tr>
                        <?php 
                            $totalProventos = 0;
                            $totalDescontos = 0; 
                        ?>
                        <?php if($HoleriteDescritivo): ?>
                            <?php foreach($HoleriteDescritivo as $key => $DadosDescritivo): ?>
                                <tr>
                                    <td style="text-align: center; font-size: 12px;"><?= $referencia[1].'/'.$referencia[2]; ?></td>
                                    <td style="text-align: center; font-size: 12px;"><?= $DadosDescritivo['CODEVENTO']; ?></td>
                                    <td style="text-align: center; font-size: 12px;"><?= $DadosDescritivo['DESCRICAO']; ?></td>
                                    <td style="text-align: center; font-size: 12px;"><?= moeda($DadosDescritivo['REF']); ?></td>
                                    <td style="text-align: right; font-size: 12px;"><?= ($DadosDescritivo['PROVDESCBASE'] == "P") ? '<span class="text-primary">'.moeda($DadosDescritivo['VALOR']).'</span>' : ""; ?></td>
                                    <td style="text-align: right; font-size: 12px;"><?= ($DadosDescritivo['PROVDESCBASE'] == "D") ? '<span class="text-danger">'.moeda($DadosDescritivo['VALOR']).'</span>' : ""; ?></td>
                                </tr>
                                <?php 
                                    $totalProventos += (($DadosDescritivo['PROVDESCBASE'] == "P") ? $DadosDescritivo['VALOR'] : 0);
                                    $totalDescontos += (($DadosDescritivo['PROVDESCBASE'] == "D") ? $DadosDescritivo['VALOR'] : 0);
                                ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr>
                            <td colspan="6" style="text-align: center; height: 20px;">
                            </td>
                        </tr>
                    </table>

                    <table class="holerite ht" cellpadding="2">
                        <tr>
                            <td>
                                <span class="font-11">BASE FGTS 13º:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($Holerite[0]['BASEFGTS13']); ?></span>
                            </td>
                            <td>
                                <span class="font-11">BASE FGTS:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($Holerite[0]['BASEFGTS']); ?></span>
                            </td>
                            <td>
                                <span class="font-11">FGTS DO MÊS:</span>
                                <span class="mt-0 mb-0 font-14 d-block">
                                <?php
                                    switch($DadosFuncionario[0]['CODTIPO']){
                                        case 'N': echo moeda(($Holerite[0]['BASEFGTS'] + $Holerite[0]['BASEFGTS13']) * 0.08); break;
                                        case 'Z': echo moeda(($Holerite[0]['BASEFGTS'] + $Holerite[0]['BASEFGTS13']) * 0.02); break;
                                        default : echo ""; break;
                                    }
                                ?>
                                </span>
                            </td>
                            <td>
                                <span class="font-11">TOTAL DE PROVENTOS:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($totalProventos); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="font-11">BASE CÁLC. IRRF 13º:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($Holerite[0]['BASEIRRF13']); ?></span>
                            </td>
                            <td>
                                <span class="font-11">BASE CÁLC. IRRF:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($Holerite[0]['BASEIRRF']); ?></span>
                            </td>
                            <td>
                                <span class="font-11">PENSÃO ALIM. EXTRA:</span>
                                <span class="mt-0 mb-0 font-14 d-block">-</span>
                            </td>
                            <td>
                                <span class="font-11">TOTAL DE DESCONTOS:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($totalDescontos); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="font-11">SAL. CONT. INSS 13º:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($Holerite[0]['BASEINSS13']); ?></span>
                            </td>
                            <td>
                                <span class="font-11">SAL. CONT. INSS:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($Holerite[0]['BASEINSS']); ?></span>
                            </td>
                            <td>
                            </td>
                            <td>
                                <span class="font-11">LÍQUIDO A RECEBER:</span>
                                <span class="mt-0 mb-0 font-14 d-block"><?= moeda($totalProventos - $totalDescontos); ?></span>
                            </td>
                        </tr>
                    </table>

                    <table class="holerite ht" cellpadding="2">
                        <tr>
                            <td colspan="4" style="text-align: left;">
                                <br>
                                <br>
                                <br>
                                Assinatura: _____________________________________________________________________________
                            </td>
                        </tr>
                    </table>

                    <?php endif; ?>

                </div>
            </div>
        </div><!-- end main -->
        
        
    </div>
</div><!-- container -->
<script>
const consultaHolerite = () => {
    
    let dados = {
        "periodo": $("#periodo").val(),
    }

    if(dados.periodo == ""){ exibeAlerta("error", "<b>Período</b> não selecionado."); return false; }

    openLoading();

    document.getElementById("form_holerite").submit();

}

</script>
<style>
.ht {
    margin-top: -1px !important;
}
.holerite {
    width: 749px;
    text-align: center;
    margin: auto;
}
.holerite td {
    border: 1px solid #000000;
}
</style>
<?php
loadPlugin(array('select2'))
?>