<?php
date_default_timezone_set('America/Sao_Paulo');
$ini = date('d/m/Y H:i:s');
set_time_limit(60*300);
ini_set('display_errors', true);
error_reporting(-1);
// exit();
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://portalrh2.eldoradobrasil.com.br/ponto/ats/cronATSMacro");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_TIMEOUT, 60*50);

$result = trim(curl_exec($ch));
curl_close($ch);

echo $result;
$fim = date('d/m/Y H:i:s');
$arquivo = "E:/crmserver/htdocs/portal_prd/cron/log_atsmacro.txt";
$fp = fopen($arquivo, "w+");
fwrite($fp, '('.$result.') Executado em => '.date('d/m/Y H:i:s').' | Ini: '.$ini.' Fim: '.$fim);
fclose($fp);