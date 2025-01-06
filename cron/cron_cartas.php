<?php
date_default_timezone_set('America/Sao_Paulo');

set_time_limit(60*5);
ini_set('display_erros', true);
error_reporting(-1);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://portalrh2.eldoradobrasil.com.br/remuneracao/cartas/EnviarCartaGestor");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
curl_close($ch);

echo $result;
	
$arquivo = "E:/crmserver/htdocs/portal_prd/cron/log_cartas.txt";
$fp = fopen($arquivo, "w+");
fwrite($fp, '('.$result.') Executado em => '.date('d/m/Y H:i:s'));
fclose($fp);