
<?php
// Defina a localidade para exibir o nome do mês em português
setlocale(LC_TIME, 'pt_BR.utf8', 'pt_BR', 'Portuguese_Brazil.1252');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunicado de Sobreaviso</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }
        .signature-table td {
            padding: 10px;
            vertical-align: top;
        }
        h1 {
            text-align: center; /* Centraliza o título */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>COMUNICADO DE SOBREAVISO</h1>
        
        <p>Prezado(a) <strong><?= $func[0]['NOME']; ?></strong>,</p>
        
        <p>Informamos que, em conformidade com a legislação vigente e com as normas internas da empresa, você estará em regime de sobreaviso. Durante este período, você deverá permanecer disponível para eventuais chamados relacionados às suas atividades profissionais.</p>
        
        <h2>Detalhes do sobreaviso:</h2>
        <ul>
            <li><strong>Chapa:</strong> <?= $func[0]['CHAPA']; ?></li>
            <li><strong>Colaborador:</strong> <?= $func[0]['NOME']; ?></li>
            <li><strong>Quantidade de Horas:</strong> <?= $valores->valor; ?> Horas</li>
            <li><strong>Área/Departamento:</strong> <?= $func[0]['NOMESECAO']; ?></li>
        </ul>
        
        <p>Conforme o artigo 244, parágrafo 2º da Consolidação das Leis do Trabalho (CLT), o tempo em que o colaborador estiver de sobreaviso será remunerado em 1/3 (um terço) do valor da hora normal de trabalho.</p>
        
        <p> 
            <?= $filial[0]['CIDADE']; ?>, <?= date('d'); ?> de <?= $mes; ?> de <?= date('Y'); ?>
        </p>
        
        
            <table class="signature-table">
                <tr>
                    <td>_________________________________<br><?= $func[0]['NOME']; ?></td>
                    <td>_________________________________<br> <?= $empresa[0]['NOME']; ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

