<style>* {text-align: justify !important;}table, span {font-family:Tahoma, Geneva, sans-serif !important;border-collapse: collapse;text-align: justify !important; font-size: 12px;}
div {
  text-align: justify;
  text-justify: inter-word;
}
</style><img src="<?= (('public/assets/images/logo_pdf.jpg')); ?>">
<br>
<br><br>
<table width="700">
    <tr>
        <td align="center"><h4 style="font-size: 16px;">SOLICITAÇÃO TROCA DE DIA</h4></td>
    </tr>
</table>

<br>
<table width="700">
    <tr>
        <td style="text-align: justify !important; font-size: 14px;">
                Eu, <strong><?= $resDadosTermo[0]['NOME']; ?></strong>, Matricula <strong><?= $resDadosTermo[0]['CHAPA']; ?></strong>, colaborador da empresa <?= $resDadosTermo[0]['NOMEFILIAL']; ?>, localizada na <?= $resDadosTermo[0]['RUA'].' - '.$resDadosTermo[0]['COMPLEMENTO']; ?>, na cidade <?= $resDadosTermo[0]['CIDADE']; ?>, Estado <?= $resDadosTermo[0]['ESTADO']; ?>, solicito a troca do dia <strong><?= dtBr($resEscala['datamudanca']); ?></strong> para folgar no dia <strong><?= dtBr($resEscala['datamudanca_folga']); ?></strong>.
                <br>
                <br>
                Garantindo o disposto no art. 611-A, XI, da CLT, que determina que a troca de datas de feriados e dia útil, somente podem ser compensadas mediante previsão em instrumento coletivo, desse modo, as horas extraordinárias.<br>
                <br>
                <br>
                Sem mais, para o momento assino e concordo com o exporto acima.<br>
                <br>
                <br>
                <br>
            
        </td>
    </tr>
</table>
<br>
<br><br>
<table width="700">
    <tr>
        <td align="center"><?= $resDadosTermo[0]['CIDADE'].' - '.$resDadosTermo[0]['ESTADO']; ?>, ______ de ____________________ de __________</td>
    </tr>
    <tr>
        <td align="center">
            <br><br><br>
            _________________________________________________<br>
            <?= $resDadosTermo[0]['NOME']; ?>
        </td>
    </tr>
    <tr>
        <td align="center">
            <br><br><br>
            _________________________________________________<br>
            <?= $resDadosTermo[0]['NOMEFILIAL']; ?>
        </td>
    </tr>
</table>