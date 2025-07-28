
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

        .table_hor {
          border-collapse: collapse;
          width: 70%;
        }
        .th_hor, .td_hor {
          border: 1px solid black;
          text-align: center;        /* horizontal centering */
          vertical-align: middle;    /* vertical centering */
          padding: 5px 10px;
          font-size: small;
        }
</style>

<table>
  <tr>
    <th>Nome</th>
    <th>Idade</th>
  </tr>
  <tr>
    <td>João</td>
    <td>30</td>
  </tr>
</table>
 
    </style>
</head>
<body>
    <div class="container">
        <h1>COMUNICADO DE SOBREAVISO</h1>
        <br>
        <p>Prezado(a) <strong><?= $func[0]['NOME']; ?></strong>,</p>
        
        <p>Informamos que, em conformidade com a legislação vigente e com as normas internas da empresa, você estará em regime de sobreaviso. Durante este período, você deverá permanecer disponível para eventuais chamados relacionados às suas atividades profissionais.</p>
        
        <h2>Detalhes do sobreaviso:</h2>
        <ul>
            <li><strong>Chapa:</strong> <?= $func[0]['CHAPA']; ?></li>
            <li><strong>Colaborador:</strong> <?= $func[0]['NOME']; ?></li>
            <li><strong>Quantidade de Horas: <?= sprintf('%d:%02d', $h = floor($valores->valor), round(($valores->valor - $h) * 60)); ?> Horas </strong></li>
            <li><strong>Área/Departamento:</strong> <?= $func[0]['NOMESECAO']; ?></li>
        </ul>

        <?php if(isset($valores->horarios)) { ?>
          <h3>Horários do sobreaviso:</h3>
          <table class="table_hor">
            <thead>
              <tr>
                  <th class="th_hor">&nbsp;&nbsp;Data Inicial&nbsp;&nbsp;</th>
                  <th class="th_hor">Hora Inicial</th>
                  <th class="th_hor">&nbsp;&nbsp;Data Final&nbsp;&nbsp;</th>
                  <th class="th_hor">Hora Final</th> 
                  <th class="th_hor">&nbsp;&nbsp;Horas&nbsp;&nbsp;</th> 
              </tr>
          </thead>
          <tbody>
        <?php } ?>

        <?php  $horarios = json_decode(isset($valores->horarios) ? $valores->horarios : '[]');
        foreach ($horarios as $key2 => $dados2) : ?>
            <tr>
                <td class="td_hor"><?= dtBr($dados2->data_inicio) ?></td>
                <td class="td_hor"><?= trim($dados2->hora_inicio) ?></td>
                <td class="td_hor"><?= dtBr($dados2->data_fim) ?></td>
                <td class="td_hor"><?= trim($dados2->hora_fim) ?></td>
                <td class="td_hor"><?= $dados2->tot_horas ?></td>
            </tr>
        <?php endforeach; ?>

        <?php if(isset($valores->horarios)) { ?>
          </tbody>
          </table>
        <?php } ?>

        <p>Conforme o artigo 244, parágrafo 2º da Consolidação das Leis do Trabalho (CLT), o tempo em que o colaborador estiver de sobreaviso será remunerado em 1/3 (um terço) do valor da hora normal de trabalho.</p>
        
        <p> 
            <?= $filial[0]['CIDADE']; ?>, <?= date('d'); ?> de <?= $mes; ?> de <?= date('Y'); ?>
        </p>
        
        
            <table class="signature-table">
                <tr>
                    <td>______________________________________<br><?= $func[0]['NOME']; ?></td>
                    <td>______________________________________<br> <?= $empresa[0]['NOME']; ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

