<?php


exit('PAUSE');
date_default_timezone_set('America/Sao_Paulo');

ini_set("pcre.backtrack_limit", "50000000");
set_time_limit(60*90);
ini_set('max_execution_time', 60*90);
ini_set('display_errors', true);
error_reporting(-1);

$ini = date('d/m/Y H:i:s');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://portalrh2.eldoradobrasil.com.br/ponto/ocorrencia/cron_indice_horario");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
curl_close($ch);

echo $result;

$fim = date('d/m/Y H:i:s');

$arquivo = "E:/crmserver/htdocs/portal_prd/cron/cron_indice_horario.txt";
$fp = fopen($arquivo, "w+");
fwrite($fp, ' ('.$ini.' - '.$fim.') | ('.$result.') Executado em => '.date('d/m/Y H:i:s'));
fclose($fp);