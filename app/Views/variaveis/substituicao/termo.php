
<?php
// Defina a localidade para exibir o nome do mês em português
setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'Portuguese_Brazil.1252');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termo Aditivo de Contrato de Trabalho</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 40px;
        }
        p {
    font-size: 12px; /* Escolha o tamanho de fonte que desejar */
  }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }
        .content {
            margin-top: 20px;
        }
        .clause {
        margin-bottom: 20px;
        text-align: justify; /* Adicionado para justificar o texto */
    }
        
        .clause-title {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature {
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="title">
        TERMO ADITIVO DE CONTRATO DE TRABALHO
    </div>

    <div class="content">
        <div class="clause">
            Pelo presente termo aditivo ao contrato de trabalho, de um lado <strong><?= $empresa[0]['NOME']; ?></strong>, inscrita no CNPJ n.º <strong><?= $filial[0]['CGC']; ?></strong>, localizada na <strong><?= $empresa[0]['RUA']; ?></strong> em <strong><?= $empresa[0]['CIDADE']; ?></strong>, doravante denominada de <strong>EMPRESA</strong>, e de outro lado <strong><?= $func[0]['NOME']; ?></strong>, inscrito no CPF n.º <strong><?= $func[0]['CPF']; ?></strong>, doravante denominado de <strong>EMPREGADO</strong>, têm entre si justo e acordado o presente <strong>TERMO ADITIVO</strong>, que ficará fazendo parte integrante ao contrato de trabalho anteriormente firmado, pelas cláusulas e condições a seguir descritas:
        </div>

        <div class="clause">
            <span class="clause-title">Cláusula Primeira</span> – O <strong>CONTRATADO</strong> exerce a função de <strong><?= $func[0]["NOMEFUNCAO"]; ?></strong>, todavia em virtude da necessidade operacional a <strong>EMPRESA</strong> e o <strong>EMPREGADO</strong> exercerá em caráter temporário a função de <strong><?= $func_sub[0]['NOMEFUNCAO']; ?></strong> no período de <?= date('d/m/Y',strtotime($valores->data_inicio)); ?> a <?= date('d/m/Y',strtotime($valores->data_fim)); ?>, sendo que durante este período responderá plenamente pelas responsabilidades da função para o qual deverá estar plenamente capacitado.
        </div>

        <div class="clause">
            <span class="clause-title">Cláusula Segunda</span> – Em virtude da presente mudança de função do <strong>EMPREGADO</strong>, o mesmo fará jus ao salário de R$ <strong><?= moeda($func_sub[0]['SALARIO']); ?>/mês</strong>, correspondente ao salário contratual da função exercida, nos termos da SÚMULA 159, do TRIBUNAL SUPERIOR DO TRABALHO, sendo que a diferença de salários existentes será paga na folha de pagamento do <strong>EMPREGADO</strong> com o seguinte título: “Salário Substituição”, durante o período de mudança de função ora previsto.
        </div>

        <div class="clause">
            <span class="clause-title">Cláusula Terceira</span> – Ao término do período, o <strong>EMPREGADO</strong> voltará a exercer a função de <strong><?= $func[0]['NOMEFUNCAO']; ?></strong>, quando não mais lhe será devido o pagamento previsto na Cláusula Segunda deste Aditivo Contratual.
        </div>

        <div class="clause">
            <span class="clause-title">Cláusula Quarta</span> – Todas as demais cláusulas do instrumento particular de contrato de trabalho permanecem inalteradas em todos os seus termos, que deverão ser cumpridas integralmente pelas partes contratantes, ficando o presente termo aditivo fazendo parte integrante do contrato de trabalho, para que surta seus legais e jurídicos efeitos.  <br>
             Por estarem assim justos e contratados, firmam o presente instrumento, em duas vias de igual teor, juntamente com 2 (duas) testemunhas  
        </div>

    

        <p style="text-align: right;">
            <?= $filial[0]['CIDADE']; ?>, <?= date('d'); ?> de <?= $mes; ?> de <?= date('Y'); ?>
        </p>
        <div class="signature">
           
        <div style="border-top: 1px solid black; width: 300px; margin: 0 auto; text-align: center; padding-top: 2px;">
            <p style="margin: 0;"><strong><?= $empresa[0]['NOME']; ?></strong></p>
            <p style="margin: 0;">CNPJ <strong><?= $filial[0]['CGC']; ?></strong></p>
        </div>

            <br>
            <div style="border-top: 1px solid black; width: 300px; margin: 0 auto; text-align: center; padding-top: 2px;">
                 <p><strong><?= $func[0]['NOME']; ?></strong></p>
                <p>CPF <strong><?= $func[0]['CPF']; ?></strong></p>
            </div>
           
        </div>
    </div>
</body>
</html>
