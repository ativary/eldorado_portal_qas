
<?php
// Defina a localidade para exibir o nome do mês em português
setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorização para Desconto em Folha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
       
        .center {
            text-align: center;
        }
        .signature {
            margin-top: 50px;
        }
        .signature div {
            display: inline-block;
            margin-right: 50px;
        }
        .line {
            width: 300px;
            border-top: 1px solid #000;
            margin: 0 auto;
            text-align: center;
        }
        .text-justify {
            text-align: justify;
        }
        .primeira-linha {
    text-indent: 50vw; /* Recuo da primeira linha, ajustado para 50% da largura da tela */
}

    </style>
</head>
<body>
   
        <h1 class="center">AUTORIZAÇÃO PARA DESCONTO EM FOLHA</h1>
        <p class="text-justify primeira-linha">
            Eu, <b><?= $func[0]['NOME']; ?></b> Matrícula <b><?= $func[0]['CHAPA']; ?></b>, inscrito no CPF <b><?= $func[0]['CPF']; ?></b>, atualmente exercendo a função de <b><?= $func[0]['NOMEFUNCAO']; ?></b>, em conformidade com o previsto no artigo 462, § 1º, da CLT, sob a prestação de contas, <b>AUTORIZO</b> a empresa <b><?= $empresa[0]['NOME']; ?></b> a descontar em minha folha de pagamento o valor total de R$ <b><?= $valores->valor_desconto; ?></b>, descontado.
        </p>

       
        <?php if (isset($valores->parcelas)) : ?>
            <?php  $parcelas = json_decode($valores->parcelas); 
            foreach ($parcelas as $key2 => $dados2) : ?>
            <p class="text-justify">
            <?= $dados2->parcela; ?> <?= $dados2->valor; ?>
            </p>
             <?php endforeach; ?>
            <?php endif; ?>
       
        <p class="text-justify">
            Tal desconto refere-se a: <b><?= $valores->justificativa; ?></b>
        </p>
        <br>
        <p><?= $filial[0]['CIDADE']; ?>, <b><?= strftime('%d de %B de %Y'); ?></b></p>
     
       <div class="signature">
            <p>________________________________________________________________________________                  ___/__/____ </p>
            <div><?= $func[0]['NOME']; ?> </div>
            <div>CPF : <?= $func[0]['CPF']; ?> </div>
        </div>
       
        <div class="signature">
        <p>________________________________________________________________________________  </p>
            <div><?= $empresa[0]['NOME']; ?></div>
            <div><?= $filial[0]['CGC']; ?></div>  
        </div>
  
</body>
</html>
