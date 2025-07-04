<?php
date_default_timezone_set('America/Sao_Paulo');

set_time_limit(60*60);
ini_set('display_errors', true);
error_reporting(-1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://portalrh2.eldoradobrasil.com.br/ponto/aprova/workflow_faltas");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = trim(substr(curl_exec($ch),0,10));
curl_close($ch);

echo $result;
	
$arquivo = "E:/crmserver/htdocs/portal_prd/cron/log_ponto_aprova_telegrama.txt";
$fp = fopen($arquivo, "w+");
fwrite($fp, '('.$result.') Executado em => '.date('d/m/Y H:i:s'));
fclose($fp);