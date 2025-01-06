<style>table, span {border-collapse: collapse;text-align: justify !important;}</style><img src="<?= (('public/assets/images/logo_pdf.jpg')); ?>">
<br>
<br><br>
<table width="700">
    <tr>
        <td align="center"><h4 style="font-size: 16px;">TERMO ADITIVO AO CONTRATO DE TRABALHO</h4></td>
    </tr>
</table>

<br>
<p>
Pelo presente instrumento particular de Aditivo Contratual, a empresa <?= $resDadosTermo[0]['NOMEFILIAL']; ?>, com sede à <?= $resDadosTermo[0]['RUA'].' - '.$resDadosTermo[0]['COMPLEMENTO']; ?>, Cidade <?= $resDadosTermo[0]['CIDADE']; ?>, Estado <?= $resDadosTermo[0]['ESTADO']; ?>, inscrita no CNPJ <?= $resDadosTermo[0]['CNPJ']; ?> denominada EMPREGADORA e do outro lado o Sr, (a) <strong><?= $resDadosTermo[0]['NOME']; ?></strong>, inscrito (a) no CPF sob o nº <?= $resDadosTermo[0]['CPF']; ?>, portador (a) da Carteira Profissional nº <?= $resDadosTermo[0]['CTPS']; ?>, série <?= $resDadosTermo[0]['CTPS_SERIE']; ?>, doravante denominado EMPREGADO (A), têm como justo e acertado o presente termo aditivo ao contrato de trabalho:
<br>
<strong>Cláusula Primeira:</strong> Objeto<br>
Alteração escala de trabalho.<br>
<br>
<strong>Cláusula Segunda:</strong> Escala atual<br>
A escala de trabalho atual do EMPREGADO é a escala <?= $resDadosTermo[0]['HORARIO_ATUAL']; ?>.<br>
<br>
<strong>Cláusula Terceira:</strong> Alteração escala de trabalho<br>
Fica acordado entre as partes, que a escala de trabalho do empregado acima qualificado, a partir de <?= data_extenso($datamudanca); ?> será alterada para escala de <?= $resDadosTermo[0]['HORARIO_NOVO']; ?>
</p>
<br>
<br>
<table width="700">
    <tr>
        <td align="left"><?= $resDadosTermo[0]['CIDADE'].' - '.$resDadosTermo[0]['ESTADO']; ?>, ______ de ____________________ de __________</td>
    </tr>
    <tr>
        <td align="left">
            <br><br>
            _____________________________________<br>
            <?= $resDadosTermo[0]['NOMEFILIAL']; ?>
        </td>
    </tr>
    <tr>
        <td align="left">
            <br><br>
            _____________________________________<br>
            <?= $resDadosTermo[0]['NOME']; ?>
        </td>
    </tr>
    <tr>
        <td align="left">
        <br><br>Testemunhas<br><br>
           1) _____________________________________<br><br>
           2) _____________________________________<br>
        </td>
    </tr>
</table>