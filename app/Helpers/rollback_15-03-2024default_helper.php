<?php
use Ramsey\Uuid\Uuid;
function isLogged($redirect = true){

    $checkSession = isset($_SESSION['authenticate']) ? true : false;

    if($redirect && !$checkSession){
        redirect(base_url('acesso/login'));
    }

    return $checkSession;

}

function responseJson($tipo, $msg, $cod = false, $complemento = false){

    $response['tipo'] = $tipo;
    $response['msg'] = $msg;
    if($cod) $response['cod'] = $cod;
    if($complemento) $response['complemento'] = $complemento;

    return json_encode($response);

}

function msgAlert(){

    if(session()->get('msg')){
        
    }

}

function enviaEmail($destinatario, $assunto, $mensagem, $anexo = array()){

    $email = \Config\Services::email();
    
    $email->clear(true);

    $config['protocol'] = EMAIL_PROTOCOLO;
    $config['SMTPCrypto'] = EMAIL_CRYPTO;
    $config['SMTPHost'] = EMAIL_SMTP;
    $config['SMTPUser'] = EMAIL_USUARIO;
    $config['SMTPPass'] = EMAIL_SENHA;
    $config['SMTPPort'] = EMAIL_PORTA;
    $config['mailType'] = 'html';
    $config['newline'] = "\\r\\n";
    $config['crlf'] = "\\r\\n";
    $config['wordwrap'] = true;
    $config['charset']    = 'utf-8';
    //'wordwrap' => TRUE

    $email->initialize($config);
    $email->setNewline("\r\n");  

    $email->setFrom(EMAIL_EMAIL_REMETENTE, EMAIL_NOME_REMETENTE);
    $email->setTo($destinatario);

    $email->setSubject($assunto);
    $email->setMessage($mensagem);

    

    if(count($anexo) > 0){
        foreach($anexo as $key => $Anexo){
            $email->attach(base64_decode($Anexo['file_base64']), 'attachment', $Anexo['file_name'], $Anexo['file_mime']);
        }
        
    }

    if($email->send()){
        return true;
    }

    if(EMAIL_DEBUG){
        $debug = $email->printDebugger(['headers']);
        echo '<pre>';
        print_r($debug);
        exit();
    }

    return false;

}

function exibeMensagem($botao_fechar = false){

    /*
     * exemplo de uso:
     * session()->set(array('notificacao' => array('success', 'Teste mensagem')));
     */

    $botao = ($botao_fechar) ? '<button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true"><i class="mdi mdi-close"></i></span></button>' : "";

    $notificacao = session()->get('notificacao');

    if(isset($notificacao)){
        $tipo = $notificacao[0];
        $mensagem = $notificacao[1];

        echo '<div class="alert alert-'.$tipo.' border-0" role="alert">
                '.$botao.'
                '.$mensagem.'
            </div>';
        session()->remove('notificacao');
    }

}

function limitaTexto($texto, $tamanho, $ponto = "..."){
    if(strlen(trim($texto)) > $tamanho){
        return substr($texto, 0, $tamanho).$ponto;
    }
    return $texto;
}

function loadPlugin($array = array()){
    
    // datatables
    if(in_array('datatable', $array)){
        echo '<link href="'.base_url('public/assets/plugins/datatables/dataTables.bootstrap4.min.css').'?v'.VERSION_CSS.'" rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('public/assets/plugins/datatables/jquery.dataTables.min.js').'?v'.VERSION_JS.'"></script>';
        echo '<script src="'.base_url('public/assets/plugins/datatables/dataTables.bootstrap4.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // select2
    if(in_array('select2', $array)){
        echo '<link href="'.base_url('public/assets/plugins/select2/select2.min.css').'?v'.VERSION_CSS.'" rel="stylesheet" type="text/css" />';
        echo '<script src="'.base_url('public/assets/plugins/select2/select2.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // nestable
    if(in_array('nestable', $array)){
        echo '<link href="'.base_url('public/assets/plugins/nestable/jquery.nestable.min.css').'?v'.VERSION_CSS.'" rel="stylesheet" />';
        echo '<script src="'.base_url('public/assets/plugins/nestable/jquery.nestable.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // dropify
    if(in_array('dropify', $array)){
        echo '<link href="'.base_url('public/assets/plugins/dropify/css/dropify.min.css').'?v'.VERSION_CSS.'" rel="stylesheet" />';
        echo '<script src="'.base_url('public/assets/plugins/dropify/js/dropify.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // tinymce
    if(in_array('tinymce', $array)){
        echo '<script src="'.base_url('public/assets/plugins/tinymce/tinymce.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // maxlength
    if(in_array('maxlength', $array)){
        echo '<script src="'.base_url('public/assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // maskmoney
    if(in_array('maskmoney', $array)){
        echo '<script src="'.base_url('public/assets/plugins/maskmoney/jquery.maskMoney.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // inputmask
    if(in_array('inputmask', $array)){
        echo '<script src="'.base_url('public/assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // mask
    if(in_array('mask', $array)){
        echo '<script src="'.base_url('public/assets/js/jquery.mask.min.js').'?v'.VERSION_JS.'"></script>';
    }

    // tooltips
    if(in_array('tooltips', $array)){
        //echo '<link href="'.base_url('public/assets/plugins/multiselect/css/multi-select.css').'?v'.VERSION_CSS.'" rel="stylesheet" />';
        echo '<script src="'.base_url('public/assets/plugins/tippy/tippy.all.min.js').'?v'.VERSION_JS.'"></script>';
        echo '<script src="'.base_url('public/assets/pages/jquery.tooltipster.js').'?v'.VERSION_JS.'"></script>';
    }

    // duallistbox
    if(in_array('duallistbox', $array)){
        echo '<script src="'.base_url('public/assets/plugins/bootstrap-duallistbox-master/src/jquery.bootstrap-duallistbox.js').'?v'.VERSION_JS.'"></script>';
        echo '<link href="'.base_url('public/assets/plugins/bootstrap-duallistbox-master/src/bootstrap-duallistbox.css').'?v'.VERSION_CSS.'" rel="stylesheet" />';
    }

    // duallistbox
    if(in_array('clipboard', $array)){
        echo '<script src="'.base_url('public/assets/plugins/clipboard/clipboard.min.js').'?v'.VERSION_JS.'"></script>';
    }

}
function checkMenuAtivo($modulo_carregado = false, $nivel = false){

    if(!$modulo_carregado) return false;
    
    $urlAtual = explode('/', $_SERVER['REQUEST_URI']);
    $moduloAtual = isset($urlAtual[1]) ? mb_strtoupper($urlAtual[1]) : false;

    $modulo_carregado = explode('_', $modulo_carregado);

    if(mb_strtoupper($modulo_carregado[0]) == $moduloAtual){
        switch($nivel){
            case 1: return ' class="active" '; break;
            case 2: return ' in '; break;
        }
    }

}
function notificacao($type = 'success', $mensagem = ''){

    switch($type){
        case 'success': $icone = '<i class="mdi mdi-check-all"></i> '; break;
        case 'danger': $icone = '<i class="mdi mdi-alert-outline"></i> '; break;
        case 'warning': $icone = '<i class="typcn typcn-warning-outline"></i> '; break;
        default: $icone = '<i class="fas fa-info-circle"></i> '; break;
    }

    session()->set(array('notificacao' => array($type, $icone.$mensagem)));

}
function redireciona($url = false){
    $url = ($url) ? $url : base_url();
    exit("<script>window.location='{$url}';</script>");
}
function templateEmail($mensagem, $size = "540"){

    $html = '<html>
    <head>
      <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
      <style>
          @import url(\'https://fonts.googleapis.com/css?family=Open+Sans|Oswald\');
          body{padding:0px;margin:0px;background:#f0f0f0;}
    
          .f-title-1{font-family:"Oswald", sans-serif;}
          .f-content-1,
          .f-footer-1{font-family:"Open Sans", sans-serif;}
      </style>
    </head>
    <body>
    
        <br>
      <table cellspacing="5" cellpadding="5" border="0" align="center" width="'.$size.'" bgcolor="#FFFFFF">
      
      <tr><td>
      
      '.$mensagem.'
      
      </td></tr>
      <tr><td>&nbsp;</td></tr>
      </table>
    
    <table cellspacing="0" cellpadding="0" border="0" align="center" width="'.$size.'" bgcolor="#CCCCCC">
    <tr><td>&nbsp;</td></tr>
    <tr>
      <td>
          <table cellspacing="5" cellpadding="5" border="0" width="100%">
              <tr>
                  <td width="100" align="center">
                      <a href="'.base_url().'">
                          <img src="'.base_url('public/assets/images/logo-dark.png').'" height="24" border="0" />
                      </a>
                  </td>
                  <td align="right">
                      <font size="1" face="Tahoma, Verdana, Arial, sans-serif" color="#000000">
                          <span class="f-footer-1">Esta mensagem foi enviada em '.date('d/m/Y H:i:s').'.</span>
                      </font>
                  </td>
              </tr>
          </table>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    </table>

    <table cellspacing="0" cellpadding="0" border="0" align="center" width="'.$size.'">
    <tr><td><font size="1" face="Tahoma, Verdana, Arial, sans-serif" color="#000000">
    <span class="f-footer-1">'.NOME_PORTAL.' - '.base_url().'</span>
    </font></td></tr>
    </table>


    </body>
    </html>';
    return $html;

}
function descricaoHolerite($nroPeriodo){
    switch($nroPeriodo){
        case 2: return '<span class="badge badge-warning">Adiantamento</span>'; break;
        case 5: return '<span class="badge badge-primary">Mensal</span>'; break;
        default: return '<span class="badge badge-secondary">Outros</span>'; break;
    }
}

// função para colocar zeros a esquerda
function e0($valor, $qtde_zeros = 2){
    // coloca zero a esquerda
    return str_pad($valor, $qtde_zeros, '0' , STR_PAD_LEFT);
}

function dtBr($data){
    if(strlen(trim($data)) <= 0) return false;
    return date('d/m/Y', strtotime($data));
}
function dtEn($data, $basic = false){

    try{

        if(strlen(trim($data)) <= 0) return false;
        if(!$basic){
            return DateTime::createFromFormat("d/m/Y", $data)->format ( "Y-m-d" );
        }    
        return date('Y-m-d', strtotime($data));

    } catch (\Exception $e) {

        return $data;

    }
}

function moeda($valor, $para = 'BR'){
    //if($valor == null) return false;

    try{

        if($valor == '') $valor = 0;

        switch($para){
            case 'BR' : return number_format($valor, 2, ',', '.'); break;
            case 'EN' : return str_replace(',', '.', str_replace('.', '', $valor)); break;
            default : return $valor;
        }

    } catch (Exception $e) {

        return 0;

    }

    
}

function diaSemana($data, $abreviado = false){

    $descSemana = array(
        0 => "Domingo",
        1 => "Segunda-Feira",
        2 => "Terça-Feira",
        3 => "Quarta-Feira",
        4 => "Quinta-Feira",
        5 => "Sexta-Feira",
        6 => "Sábado"
    );
    $descSemanaA = array(
        0 => "Dom",
        1 => "Seg",
        2 => "Ter",
        3 => "Qua",
        4 => "Qui",
        5 => "Sex",
        6 => "Sáb"
    );

    // YYYY-MM-DD
    $diaSemana = date('w', strtotime($data));
    return ($abreviado) ? $descSemanaA[(int)$diaSemana] : $descSemana[(int)$diaSemana];
}

function m2h($mins, $tamanho = 2) {

    $mins = (int)$mins;

    try{

        if ($mins < 0) {
            $min = Abs($mins);
        } else {
            $min = $mins;
        }
        $H = Floor($min / 60);
        $M = ($min - ($H * 60)) / 100;
        $hours = $H + $M;
        if ($mins < 0) {
            $hours = $hours * (-1);
        }
     
        $expl = explode(".", $hours);
        $H = $expl[0];
        if (empty($expl[1])) {
            $expl[1] = 00;
        }
     
        $M = $expl[1];
        if (strlen($M) < 2) {
            $M = $M . 0;
        }
     
        $hours = $H . ":" . $M;
        
        
        $hours = sprintf("%0".($tamanho +1)."s",$hours);
     
        return $hours;

    } catch (\Exception $e) {

        return $mins;

    }

    
}

function h2m($num){
	$mtz = explode(":", trim($num));
	if(count($mtz) > 0){
		return intval($mtz[0])* 60 + intval($mtz[1]);		
	}
	return false; 
	
}
function util_chapa($string){
    
    if($string != null) $string = explode(':', base64_decode($string));

    return array(
        'CHAPA' => $string[0] ?? '',
        'CODCOLIGADA' => $string[1] ?? '',
    );

}
// função para gerar id com integridade de dados
function id($id, $somente_token = false){

    if(INTEGRIDADE === false) return $id;

    // gera integração de dados
    $modulo = explode('/',$_SERVER['REQUEST_URI']);
    $token = crc32(($modulo[1] ?? "").($modulo[2] ?? "").$id) + $id;

    return (!$somente_token) ? $id.'.'.$token : $token;
}
// função para validar a integridade de dados do id
function cid($id, $redirect = true){

    $string = explode('.', $id);
    $id = $string[0] ?? false;
    $token = $string[1] ?? false;

    if(INTEGRIDADE === false) return $id;

    // validação de integridade de dados
    $modulo = explode('/',$_SERVER['REQUEST_URI']);
    $valid = (crc32(($modulo[1] ?? "").($modulo[2] ?? "").$id) + $id != $token) ? false : true;

    if(($redirect && !$valid) || ($id === false || $token === false)){
        notificacao('danger', 'Erro de integridade de dados');
        redireciona();
        return false;
    }

    return $id;
}
// gerar crc
function crc($dados){
    return crc32($dados);
}
function data_extenso($data){
    $meses = array(1 => "Janeiro", 2 => "Fevereiro", 3 => "Março", 4 => "Abril", 5 => "Maio", 6 => "Junho", 7 => "Julho", 8 => "Agosto", 9 => "Setembro", 10 => "Outubro", 11 => "Novembro", 12 => "Dezembro");
    return date('d', strtotime($data)).' de '.$meses[(int)date('m', strtotime($data))].' de '.date('Y', strtotime($data));
}
function ib64($image, $type = 'image/jpeg'){

    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    ); 
    
    $file = file_get_contents($image, false, stream_context_create($arrContextOptions));

    return "data:{$type};base64,".base64_encode($file);

}
function formatBytes($size, $precision = 2, $legenda = true){
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   

    return round(pow(1024, $base - floor($base)), $precision).(($legenda) ? "".$suffixes[floor($base)] : "");
}
function to_xml(SimpleXMLElement $object, array $data){   
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $new_object = $object->addChild($key);
            to_xml($new_object, $value);
        } else {
            // if the key is an integer, it needs text with it to actually work.
            if ($key == (int) $key) {
                $key = "$key";
            }

            $object->addChild($key, $value);
        }   
    }   
} 
function dataDiff($dataIni, $dataFim, $retorno = 'day'){
    $inicio = new DateTimeImmutable($dataIni);
    $final = new DateTimeImmutable($dataFim);
    $intervalo = $inicio->diff($final);
    return $intervalo->format('%a');
}
function calcula35Horas($dataDia, $horaEntrada, $horaSaida, $dataDia2, $horaEntrada2, $horaSaida2){

    if($horaSaida < $horaEntrada){
        $data = DateTime::createFromFormat('d/m/Y', $dataDia);
        $data->add(new DateInterval('P1D')); // 2 dias
        $dataDia = $data->format('d/m/Y');
    }

    $dataInicio = dtEn($dataDia)." ".m2h($horaSaida, 4).":00";
    $dataTermino = dtEn($dataDia2)." ".m2h($horaEntrada2, 4).":00";

    $unix_data1 = strtotime($dataInicio);
    $unix_data2 = strtotime($dataTermino);

    $nHoras   = ($unix_data2 - $unix_data1) / 3600;
    $nMinutos = (($unix_data2 - $unix_data1) % 3600) / 60;

    return (int)$nHoras.'|'.date('d/m/Y H:i', $unix_data1).'|'.date('d/m/Y H:i', $unix_data2);

}
function dadosOcorrencia($Ocorrencia){

    switch($Ocorrencia['OCORRENCIA']){
        case 'extra_permitido': return 'excedido: '.m2h($Ocorrencia['VALOR']); break;
        case 'extra': return m2h($Ocorrencia['VALOR']); break;
        case 'jornada': return $Ocorrencia['COMPLEMENTO']; break;
        // case 'jornada': return m2h($Ocorrencia['VALOR']); break;
        case 'trabalho_dsr_folga': return m2h($Ocorrencia['VALOR']); break;
        case 'trabalho_dsr_folga_descanso': return $Ocorrencia['COMPLEMENTO']; break;
        case 'trabalho_ferias_afastamento': return ($Ocorrencia['VALOR'] == 1) ? "Férias" : $Ocorrencia['COMPLEMENTO']; break;
        case 'registro_manual':return m2h($Ocorrencia['VALOR'],4); break;
        case 'trabalho_6dias': return $Ocorrencia['COMPLEMENTO']." Dias"; break;
        case 'excesso_abono_gestor': return $Ocorrencia['COMPLEMENTO']." Dias"; break;
        case 'interjornada': return $Ocorrencia['COMPLEMENTO']; break;
        case 'registro_britanico':  
            $valor_ocorrencia = $Ocorrencia['COMPLEMENTO']; 
            $valor_ocorrencia = explode(' ', $valor_ocorrencia);
            return $valor_ocorrencia[0].'-'.$valor_ocorrencia[1];
            break;
        case 'sobreaviso': return m2h($Ocorrencia['VALOR'],4); break;
        case 'troca_menor_6_meses': return $Ocorrencia['COMPLEMENTO'].' dias'; break;
        case 'troca_menor_10_dias':return $Ocorrencia['VALOR'].' dia(s)'; break;
        case 'pendente_termo_aditivo': return 'Nº Req. '.$Ocorrencia['VALOR']; break;
        default : return "--";
    }

}
function calculaPorcentagem($valorA, $valorB){

    if($valorA == 0 || $valorB == 0) return "0.00%";
    $valor = (($valorB * 100) / $valorA) - 100;
    return number_format($valor,2)."%";
}
function dataExtenso($dataString = false){
    $data = date('D');
    $mes = date('M');
    $dia = date('d');
    $ano = date('Y');

    $mes_extenso = array(
        'Jan' => 'Janeiro',
        'Feb' => 'Fevereiro',
        'Mar' => 'Marco',
        'Apr' => 'Abril',
        'May' => 'Maio',
        'Jun' => 'Junho',
        'Jul' => 'Julho',
        'Aug' => 'Agosto',
        'Sep' => 'Setembro',
        'Oct' => 'Outubro',
        'Nov' => 'Novembro',
        'Dec' => 'Dezembro'
    );

    if($dataString){
        $data = explode("-", $dataString);
        $mes = $data[1];
        $dia = $data[2];
        $ano = $data[0];

        $mes_extenso = array(
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Marco',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        );
    }
 
    $semana = array(
        'Sun' => 'Domingo',
        'Mon' => 'Segunda-Feira',
        'Tue' => 'Terca-Feira',
        'Wed' => 'Quarta-Feira',
        'Thu' => 'Quinta-Feira',
        'Fri' => 'Sexta-Feira',
                'Sat' => 'Sábado'
    );
    return "{$dia} de " . $mes_extenso["$mes"] . " de {$ano}";
}
function extrai_valor($array, $valor, $index, $campo_retorno){

    if(is_array($array)){
        foreach($array as $key => $value){
            if($array[$key][$index] == $valor) return $array[$key][$campo_retorno];
            unset($array[$key], $value);
        }
    }
    
    return '';

}
function uuid()
{
    $uuid = Uuid::uuid4()->toString();
    return $uuid;
}
function indexToExcelColumn($index)
{
    $column = '';
    for($i=0;$i<$index;$i++){

        $remainder = ($index - 1) % 26;
        $column = chr(65 + $remainder) . $column;
        $index = ($index - $remainder) / 26;
    }
    return $column;
}
function ultimoDiaDoMes($data)
{
    // formato: yyyy-mm-dd
    $dataObj = new DateTime($data);
    $ultimoDia = $dataObj->format('Y-m-t');
    return $ultimoDia;
}
function somarDias($data, $dias)
{
    $dataObj = new DateTime($data);
    $dataObj->modify($dias . " days");
    return $dataObj->format('Y-m-d');
}
function meses($mes)
{
    $mes_extenso = array(
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Marco',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    );
    return $mes_extenso[$mes];
}