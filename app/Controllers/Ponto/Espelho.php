<?php
namespace App\Controllers\Ponto;

use App\Controllers\BaseController;

class Espelho extends BaseController {

    private $mEspelho;
    private $mPortal;
    private $mOcorrencia;

    public function __construct(){
        parent::__construct('Ponto'); // sempre manter

        $this->_moduloName = '<i class="fas fa-clock"></i> Ponto';
        $this->mEspelho    = model('Ponto/EspelhoModel');
        $this->mOcorrencia = model('Ponto/OcorrenciaModel');
        $this->mPortal     = model('PortalModel');
    }

    //ESPELHO PONTO EDITAR
    public function Editar($chapaFiltro = false, $dataFiltro = false){
        parent::VerificaPerfil('PONTO_ESPELHO');

        $dados['rh']                = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['perfilRH']          = parent::VerificaPerfil('PERFIL_RH', false);
        $dados['perfilRecalculo']   = parent::VerificaPerfil('PONTO_ESPELHO_RECALCULO', false);
        $dados['impressao']         = false;
        
        $dados['_titulo']       = "Espelho de Ponto | Editar";
        $this->_breadcrumb->add($dados['_titulo'], 'ponto/espelho');
        $dados['resPeriodo']    = $this->mEspelho->ListarEspelhoPeriodoRM($dados['rh']);
        $dados['configEspelho'] = $this->mEspelho->ListarEspelhoConfiguracao();
        $dados['periodo']       = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), 0, -1) : null;
        $dados['statusPeriodo'] = ($this->request->getPost('periodo') != null) ? substr($this->request->getPost('periodo'), -1) : 0;
        $dados['secao']         = $this->request->getPost('secao');

        // verifica se é gestor
        $dados['is_Gestor']         = false;
        $dados['isGestorOrLider']   = $this->mEspelho->isGestorOrLider($dados);
        $dados['isGestor']          = $this->mEspelho->isGestor($dados);
        
        // if(session()->get('log_id') == 1 || session()->get('log_id') == 20009  || session()->get('log_id') == 20414  || session()->get('log_id') == 20003){
        if((session()->get('log_id') == 1 || $dados['isGestorOrLider']) && $this->request->getPost('funcionario')){
            $dados['chapa'] = $this->request->getPost('funcionario');
        }else{
            $dados['chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        }

        if($dados['isGestorOrLider'] || $dados['rh'] || session()->get('log_id') == 1){
            if($chapaFiltro && $dataFiltro){
               
                $dados['chapa'] = $chapaFiltro;
                if($dados['resPeriodo']){
                    foreach($dados['resPeriodo'] as $periodoFiltro){
                        if($dataFiltro >= dtEn($periodoFiltro['INICIOMENSAL'], true) && $dataFiltro <= dtEn($periodoFiltro['FIMMENSAL'], true)){

                            $dados['periodo'] = substr(dtBr($periodoFiltro['INICIOMENSAL']) . dtBr($periodoFiltro['FIMMENSAL']). $periodoFiltro['STATUSPERIODO'], 0, -1);
                            $dados['statusPeriodo'] = substr(dtBr($periodoFiltro['INICIOMENSAL']) . dtBr($periodoFiltro['FIMMENSAL']). $periodoFiltro['STATUSPERIODO'], -1);
                            
                            break;
                        }
                    }
                }

            }
        }

        $DadosBancoHoras            = false;
        $HorasPositivas             = 0;
        $HorasNegativas             = 0;
        $h_mes_positivo             = 0;
        $h_mes_negativo             = 0;
        $SaldoTotal                 = 0;
        $colaboradores              = [];
        $dados['resFuncionario']    = $this->mPortal->ListarDadosFuncionario(false, $dados['chapa'], false);
        //$dados['resDiasEspelho']    = $this->mEspelho->ListarEspelhoDias($dados['periodo'], $dados['chapa'], $dados['isMotorista']);
        $dados['resDiasEspelho']    = $this->mEspelho->ListarEspelhoDias($dados['periodo'], $dados['chapa']);
        
        $dados['dataInicio'] = '';
        if($dados['periodo']){
            $dados['saldoAnteriorBancoDeHoras']   = $this->mEspelho->saldoAnteriorBancoDeHoras($dados['chapa'], $dados['periodo']);
            $dados['saldoMesBancoDeHoras']        = $this->mEspelho->saldoMesBancoDeHoras($dados['chapa'], $dados['periodo']);
            $dados['saldoAtualBancoDeHoras']      = $this->mEspelho->saldoAtualBancoDeHoras($dados['chapa'], $dados['periodo']);

            $DadosBancoHoras = $this->mEspelho->SaldoBancoHorasEquipe($dados['periodo'], $dados['chapa']);
            $HorasPositivas  = $DadosBancoHoras[0]['SALDO_ANTERIOR'] ?? 0;
            $h_mes_positivo  = $DadosBancoHoras[0]['MES_POSITIVO'] ?? 0;
            $h_mes_negativo  = $DadosBancoHoras[0]['MES_NEGATIVO'] ?? 0;
            $SaldoTotal      = $DadosBancoHoras[0]['SALDO_TOTAL'] ?? 0;
            $dados['EspelhoConfiguracao'] = $this->mEspelho->ListarEspelhoConfiguracao();
            if(!$dados['EspelhoConfiguracao']){
                notificacao('warning2', 'Configuração de ponto não localizada.');
                redireciona(base_url('ponto/espelho/editar'));
                return false;
            }

            $dadosColaboradores['rh']         = $dados['rh'];
            $dadosColaboradores['codsecao']   = $dados['secao'];
            $dataInicio                       = \DateTime::createFromFormat('d/m/Y', substr($dados['periodo'],0,10))->format('Y-m-d');
            $colaboradores                    = $this->mPortal->CarregaColaboradores($dataInicio, $dadosColaboradores, true);
            $dados['dataFim'] = \DateTime::createFromFormat('d/m/Y', substr($dados['periodo'],10,10))->format('Y-m-d');

            if($dados['resFuncionario'] and $dados['resFuncionario'][0]['DATADEMISSAO']) {
              if(strlen(trim($dados['resFuncionario'][0]['DATADEMISSAO'])) > 0){
                  $fimPeriodo         = \DateTime::createFromFormat('d/m/Y', substr($dados['periodo'],10,10))->format('Y-m-d');
                  $ultimoDia          = ($dados['resDiasEspelho']) ? end($dados['resDiasEspelho']) : false;
                  if(!$ultimoDia) $ultimoDia['DATA'] = $fimPeriodo;
                  if($ultimoDia['DATA'] < $fimPeriodo){
                      $diasDiff       = dataDiff($ultimoDia['DATA'], $fimPeriodo);
                      for($i=1;$i<=$diasDiff;$i++){
                          $data       = \DateTime::createFromFormat('Y-m-d', dtEn($ultimoDia['DATA'], true));
                          $data->add(new \DateInterval('P'.$i.'D')); // 2 dias
                          $dataDia    = $data->format('Y-m-d');
                          $dados['resDiasEspelho'][] = [
                              "DATA" => $dataDia
                          ];
                      }
                  }
              }
            }
            
        }

        $dados['DadosBancoHoras']       = $DadosBancoHoras;
        $dados['HorasPositivas']        = $HorasPositivas;
        $dados['HorasNegativas']        = $HorasNegativas;
        $dados['h_mes_positivo']        = $h_mes_positivo;
        $dados['h_mes_negativo']        = $h_mes_negativo;
        $dados['SaldoTotal']            = $SaldoTotal;
        $dados['isMotorista']           = self::checkMotorista($dados['resFuncionario']);
        $dados['resBatidasEspelho']     = $this->mEspelho->ListarEspelhoBatidas($dados['periodo'], $dados['chapa']);
        $dados['resMovimentos']         = $this->mEspelho->ListarEspelhoMovimento($dados['periodo'], $dados['chapa']);
        $dados['resSolicitacaoAbono']   = $this->mEspelho->ListarSolicitacaoAbono($dados['periodo'], $dados['chapa']);
        $dados['resAbono']              = $this->mEspelho->ListarAbono();
        $dados['resFuncionarioSecao']   = $colaboradores;
        // $dados['resFuncionarioSecao']       = $this->mPortal->ListaFuncionarioSecao($dados);


        $dados['resJustificativaExtra']     = $this->mEspelho->ListarJustificativa(3);
        $dados['resJustificativaAjuste']    = $this->mEspelho->ListarJustificativa(4);
        $dados['gestorPossuiExcecao']       = $this->mEspelho->gestorPossuiExcecao();
        $dados['resAbonoPendenteRH']        = $this->mEspelho->listaAbonoPendenteRH($dados['chapa']);
        //$dados['resJustificativaExtraEspelho'] = $this->mEspelho->ListarJustificativaExtraEspelho($dados['periodo']);
        $mSaldoBanco                        = model('Ponto/SaldobancohorasModel');
        $dados['resSecaoGestor']            = $mSaldoBanco->ListarSecaoUsuario(false, $dados['rh']);

        return parent::ViewPortal('ponto/espelho/editar', $dados);
    }

    private function checkMotorista($dadosFunc)
    {
      if($dadosFunc and $dadosFunc[0]['CODFUNCAO']) {
        $motoristas = $this->mOcorrencia->ListaConfiguracaoMotorita();
        if($motoristas){
            foreach($motoristas as $funcao){
                if($dadosFunc[0]['CODFUNCAO'] == $funcao->codfuncao) return true;
            }
        }
      }

      return false;

    }

    // ESPELHO DE PONTO CONSULTAR
    public function Consultar(){

        parent::VerificaPerfil('CONSULTAR_ESPELHO');

        $dados['isGestor'] = $this->mPortal->VerificaSecao();

        $dados['_titulo']           = "Espelho de Ponto | Consultar";
        $this->_breadcrumb->add($dados['_titulo'], 'ponto/espelho');
        $dados['resPeriodo']        = $this->mEspelho->ListarEspelhoPeriodoRM();
        $dados['ListaFuncionarioSecao'] = $this->mPortal->ListaFuncionarioSecao();
        $dados['periodo']           = $this->request->getPost('periodo');

        if(isset($_POST['funcionario'])){
            $dados['chapa'] = $this->request->getPost("funcionario");            
        }else{
            $dados['chapa'] = util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null;
        }

        $dados['DadosFuncionario'] = $this->mPortal->ListarDadosFuncionario(false,$dados['chapa']);
        $dados['resDiasEspelho']    = $this->mEspelho->ListarEspelhoDias($dados['periodo'], $dados['chapa']);
        $dados['resBatidasEspelho'] = $this->mEspelho->ListarEspelhoBatidas($dados['periodo'], $dados['chapa']);
        $dados['resMovimentos']     = $this->mEspelho->ListarEspelhoMovimento($dados['periodo'], $dados['chapa']);

        $DadosBancoHoras = false;
        $HorasPositivas  = 0;
        $HorasNegativas  = 0;
        $SaldoTotal      = 0;

        if($dados['periodo'] != null){
            $DadosBancoHoras = $this->mEspelho->SaldoBancoHorasEquipe($dados['periodo'], $dados['chapa']);
            $HorasPositivas  = $DadosBancoHoras[0]['SALDO_ANTERIOR'];
            $HorasNegativas  = $DadosBancoHoras[0]['SALDO_PERIODO'];
            $SaldoTotal      = $DadosBancoHoras[0]['SALDO_TOTAL'];
        }

        $dados['DadosBancoHoras'] = $DadosBancoHoras;
        $dados['HorasPositivas']  = $HorasPositivas;
        $dados['HorasNegativas']  = $HorasNegativas;
        $dados['SaldoTotal']      = $SaldoTotal;
        
        
        return parent::ViewPortal('ponto/espelho/consultar', $dados);
    }

    // ------------------------------------------------------------------
    // Configuração
    // ------------------------------------------------------------------
    public function configuracao(){

        parent::VerificaPerfil('GLOBAL_RH');
        parent::VerificaPerfil('PONTO_CONFIG');
        $dados['_titulo'] = "Configuração Espelho de Ponto";
        $this->_breadcrumb->add('Configuração', 'ponto/espelho/configuracao');

        $EspelhoConfiguracao = $this->mEspelho->ListarEspelhoConfiguracao();
        $dados['resGestor'] = $this->mEspelho->listaGestorExcecao();

        $dados['EspelhoConfiguracao'] = $EspelhoConfiguracao;
        
        return parent::ViewPortal('ponto/espelho/configuracao', $dados);
    }

    public function impressao()
    {

        parent::VerificaPerfil('PONTO_ESPELHO_IMPRESSAO');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $dados['_titulo'] = "Impressão Espelho de Ponto";
        $dados['impressao'] = true;
        $this->_breadcrumb->add($dados['_titulo'], 'ponto/espelho/impressao');

        $dados['periodo'] = $this->request->getPost('periodo');
        $dados['secao'] = $this->request->getPost('secao');
        $dados['chapa'] = $this->request->getPost('funcionario');

        if($dados['periodo'] != null){
            self::pdf($dados['periodo'], $dados['secao'], $dados['chapa']);
            exit();
        }

        $dados['isGestorOrLider'] = $this->mEspelho->isGestorOrLider($dados);
        $dados['isGestor'] = $this->mEspelho->isGestor($dados);
        $dados['statusPeriodo'] = 0;

        $dados['resPeriodo'] = $this->mEspelho->ListarEspelhoPeriodoRM();
        $mSaldoBanco = model('Ponto/SaldobancohorasModel');
        $dados['resSecaoGestor'] = $mSaldoBanco->ListarSecaoUsuario(false, $dados['rh']);
        
        $dados['resFuncionarioSecao'] = $this->mPortal->ListaFuncionarioSecao($dados);
        
        
        return parent::ViewPortal('ponto/espelho/impressao', $dados);
    }

    public function pdf($periodo, $codsecao = false, $chapa = false)
    {

        $mSaldoBanco = model('Ponto/SaldobancohorasModel');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
        $resSecaoGestor = $mSaldoBanco->ListarSecaoUsuario(false, $dados['rh']);

        $dadosColaboradores['rh']         = $dados['rh'];
        $dadosColaboradores['codsecao']   = $codsecao;
        $dataInicio                       = \DateTime::createFromFormat('d/m/Y', substr($periodo,0,10))->format('Y-m-d');
        $colaboradores                    = $this->mPortal->CarregaColaboradores($dataInicio, $dadosColaboradores, true);

        $resFuncionarioSecao   = $colaboradores;

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_bottom' => 9,
            'margin_top' => 9,
        ]);

        if($chapa){
            $html = self::dados_html($chapa, $periodo);
            $mpdf->WriteHTML(($html));
        }else
        if($codsecao){
            if($resFuncionarioSecao){
                foreach($resFuncionarioSecao as $key => $Chapa){
                    $html = '';
                    $html = self::dados_html($Chapa['CHAPA'], $periodo);
                    // echo $html;
                    if($html){
                        $mpdf->AddPage();
                        $mpdf->WriteHTML(($html));
                    }
                }
            }
        }else
        if(!$codsecao && !$resSecaoGestor){

            
            $html = self::dados_html(util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null, $periodo);
            $mpdf->WriteHTML(($html));
        }else{
            if($resFuncionarioSecao){
                foreach($resFuncionarioSecao as $key => $Chapa){
                    
                    $html = '';
                    $html = self::dados_html($Chapa['CHAPA'], $periodo);
                    // echo $html;
                    if($html){
                        $mpdf->AddPage();
                        $mpdf->WriteHTML(($html));
                    }
                }
            }
        }
        $mpdf->Output('EspelhoPonto.pdf', 'I');
        exit();

    }

    private function dados_html($chapa, $periodo)
    {

        $EspelhoConfiguracao = $this->mEspelho->ListarEspelhoConfiguracao();
        $resAbono = $this->mEspelho->ListarAbono();
        $resJustificativaExtra = $this->mEspelho->ListarJustificativa(3);
        $resJustificativaAjuste = $this->mEspelho->ListarJustificativa(4);
        $resPeriodo = $this->mEspelho->ListarEspelhoPeriodoRM();
        $configEspelho = $this->mEspelho->ListarEspelhoConfiguracao();
        $dados['resAbonoPendenteRH'] = $this->mEspelho->listaAbonoPendenteRH($chapa);

        $dados['perfilRH']        = false;
        $dados['perfilRecalculo'] = false;
        $dados['impressao'] = true;
        
        $dados['resPeriodo'] = $resPeriodo;
        $dados['configEspelho'] = $configEspelho;
        $dados['periodo']    = $periodo;

        // verifica se é gestor
        $dados['is_Gestor'] = false;
        $dados['temSecao'] = false;
        
        $dados['chapa'] = $chapa;

        $DadosBancoHoras = false;
        $HorasPositivas  = 0;
        $HorasNegativas  = 0;
        $h_mes_positivo  = 0;
        $h_mes_negativo  = 0;
        $SaldoTotal      = 0;
        
        if($dados['periodo']){
            $dados['saldoAnteriorBancoDeHoras'] = $this->mEspelho->saldoAnteriorBancoDeHoras($dados['chapa'], $dados['periodo']);
            $dados['saldoMesBancoDeHoras'] = $this->mEspelho->saldoMesBancoDeHoras($dados['chapa'], $dados['periodo']);
            $dados['saldoAtualBancoDeHoras'] = $this->mEspelho->saldoAtualBancoDeHoras($dados['chapa'], $dados['periodo']);

            $DadosBancoHoras = $this->mEspelho->SaldoBancoHorasEquipe($dados['periodo'], $dados['chapa']);
            $HorasPositivas  = $DadosBancoHoras[0]['SALDO_ANTERIOR'];
            $h_mes_positivo  = $DadosBancoHoras[0]['MES_POSITIVO'];
            $h_mes_negativo  = $DadosBancoHoras[0]['MES_NEGATIVO'];
            $SaldoTotal      = $DadosBancoHoras[0]['SALDO_TOTAL'];
            $dados['EspelhoConfiguracao'] = $EspelhoConfiguracao;
            if(!$dados['EspelhoConfiguracao']){
                notificacao('warning2', 'Configuração de ponto não localizada.');
                redireciona(base_url('ponto/espelho/editar'));
                return false;
            }

        }

        $dados['DadosBancoHoras'] = $DadosBancoHoras;
        $dados['HorasPositivas']  = $HorasPositivas;
        $dados['HorasNegativas']  = $HorasNegativas;
        $dados['h_mes_positivo']  = $h_mes_positivo;
        $dados['h_mes_negativo']  = $h_mes_negativo;
        $dados['SaldoTotal']      = $SaldoTotal;
        
        $dados['resFuncionario'] = $this->mPortal->ListarDadosFuncionario(false, $dados['chapa'], false);
        if(!$dados['resFuncionario']) return false;
        
        $dados['isMotorista'] = self::checkMotorista($dados['resFuncionario']);

        $dados['resDiasEspelho']      = $this->mEspelho->ListarEspelhoDias($dados['periodo'], $dados['chapa'], $dados['isMotorista']);
        $dados['resBatidasEspelho']   = $this->mEspelho->ListarEspelhoBatidas($dados['periodo'], $dados['chapa']);
        $dados['resMovimentos']       = $this->mEspelho->ListarEspelhoMovimento($dados['periodo'], $dados['chapa']);
        $dados['resSolicitacaoAbono'] = $this->mEspelho->ListarSolicitacaoAbono($dados['periodo'], $dados['chapa']);
        $dados['gestorPossuiExcecao'] = $this->mEspelho->gestorPossuiExcecao();
        $dados['isGestorOrLider'] = $this->mEspelho->isGestorOrLider($dados);
        $dados['isGestor'] = $this->mEspelho->isGestor($dados);
        $dados['statusPeriodo'] = 0;
        $dados['resAbono']            = $resAbono;
        $dados['resJustificativaExtra'] = $resJustificativaExtra;
        $dados['resJustificativaAjuste'] = $resJustificativaAjuste;
        if(!$dados['resDiasEspelho']) return false;
        //$dados['resJustificativaExtraEspelho'] = $this->mEspelho->ListarJustificativaExtraEspelho($dados['periodo']);

        return view('ponto/espelho/editar', $dados);

    }

    private function dados_html_motorista($chapa, $periodo)
    {

        $EspelhoConfiguracao = $this->mEspelho->ListarEspelhoConfiguracao();
        $resAbono = $this->mEspelho->ListarAbono();
        $resJustificativaExtra = $this->mEspelho->ListarJustificativa(3);
        $resJustificativaAjuste = $this->mEspelho->ListarJustificativa(4);
        $resPeriodo = $this->mEspelho->ListarEspelhoPeriodoRM();
        $configEspelho = $this->mEspelho->ListarEspelhoConfiguracao();
        $dados['resAbonoPendenteRH'] = $this->mEspelho->listaAbonoPendenteRH($chapa);

        $dados['perfilRH']        = false;
        $dados['perfilRecalculo'] = false;
        $dados['impressao'] = true;
        
        $dados['resPeriodo'] = $resPeriodo;
        $dados['configEspelho'] = $configEspelho;
        $dados['periodo']    = $periodo;

        // verifica se é gestor
        $dados['is_Gestor'] = false;
        $dados['temSecao'] = false;
        
        $dados['chapa'] = $chapa;

        $DadosBancoHoras = false;
        $HorasPositivas  = 0;
        $HorasNegativas  = 0;
        $h_mes_positivo  = 0;
        $h_mes_negativo  = 0;
        $SaldoTotal      = 0;
        
        if($dados['periodo']){
            $dados['saldoAnteriorBancoDeHoras'] = $this->mEspelho->saldoAnteriorBancoDeHoras($dados['chapa'], $dados['periodo']);
            $dados['saldoMesBancoDeHoras'] = $this->mEspelho->saldoMesBancoDeHoras($dados['chapa'], $dados['periodo']);
            $dados['saldoAtualBancoDeHoras'] = $this->mEspelho->saldoAtualBancoDeHoras($dados['chapa'], $dados['periodo']);

            $DadosBancoHoras = $this->mEspelho->SaldoBancoHorasEquipe($dados['periodo'], $dados['chapa']);
            $HorasPositivas  = $DadosBancoHoras[0]['SALDO_ANTERIOR'];
            $h_mes_positivo  = $DadosBancoHoras[0]['MES_POSITIVO'];
            $h_mes_negativo  = $DadosBancoHoras[0]['MES_NEGATIVO'];
            $SaldoTotal      = $DadosBancoHoras[0]['SALDO_TOTAL'];
            $dados['EspelhoConfiguracao'] = $EspelhoConfiguracao;
            if(!$dados['EspelhoConfiguracao']){
                notificacao('warning2', 'Configuração de ponto não localizada.');
                redireciona(base_url('ponto/espelho/editarmotorista'));
                return false;
            }

        }

        $dados['DadosBancoHoras'] = $DadosBancoHoras;
        $dados['HorasPositivas']  = $HorasPositivas;
        $dados['HorasNegativas']  = $HorasNegativas;
        $dados['h_mes_positivo']  = $h_mes_positivo;
        $dados['h_mes_negativo']  = $h_mes_negativo;
        $dados['SaldoTotal']      = $SaldoTotal;
        
        $dados['resFuncionario'] = $this->mPortal->ListarDadosFuncionario(false, $dados['chapa']);
        if(!$dados['resFuncionario']) return false;

        $dados['isMotorista'] = false;

        $dados['resDiasEspelho']      = $this->mEspelho->ListarEspelhoDias($dados['periodo'], $dados['chapa'], true);
        $dados['resBatidasEspelho']   = $this->mEspelho->ListarEspelhoBatidas($dados['periodo'], $dados['chapa']);
        $dados['resMovimentos']       = $this->mEspelho->ListarEspelhoMovimento($dados['periodo'], $dados['chapa']);
        $dados['resSolicitacaoAbono'] = $this->mEspelho->ListarSolicitacaoAbono($dados['periodo'], $dados['chapa']);
        $dados['gestorPossuiExcecao'] = $this->mEspelho->gestorPossuiExcecao();
        $dados['isGestorOrLider'] = $this->mEspelho->isGestorOrLider($dados);
        $dados['isGestor'] = $this->mEspelho->isGestor($dados);
        $dados['statusPeriodo'] = 0;
        $dados['resAbono']            = $resAbono;
        $dados['resJustificativaExtra'] = $resJustificativaExtra;
        $dados['resJustificativaAjuste'] = $resJustificativaAjuste;
        if(!$dados['resDiasEspelho']) return false;
        //$dados['resJustificativaExtraEspelho'] = $this->mEspelho->ListarJustificativaExtraEspelho($dados['periodo']);

        return view('ponto/espelho/editarmotorista', $dados);

    }
    
    //-----------------------------------------------------------
    // Actions
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        $arquivos = $_FILES;
        if(!$dados) return false;
        
        switch($act){

            //-------------------------------------
            case 'espelho_configuracao':
                exit($this->mEspelho->CadastrarEspelhoConfiguracao($dados));
                break;
            
            //-------------------------------------
            case 'cadastrar_batida':
                exit($this->mEspelho->CadastrarBatida($dados));
                break;
            
            //-------------------------------------
            case 'alterar_batida':
             
                exit($this->mEspelho->AlterarBatida($dados, $arquivos));
                break;
            
            //-------------------------------------
            case 'cadastrar_abono':
                exit($this->mEspelho->CadastrarAbono($dados, $arquivos));
                break;
                
            //-------------------------------------
            case 'listar_abonos':
             
                $result = $this->mEspelho->HistoricoSolicitacaoAbonos($dados['dataPonto'], $dados['chapa']);
                return $this->response->setJSON($result);
                break;

            //-------------------------------------
            case 'excluir_abono_rm':
                exit($this->mEspelho->ExcluirAbonoRM($dados));
                break;
            
            //-------------------------------------
            case 'excluir_abono_pt':
                exit($this->mEspelho->ExcluirAbonoPT($dados));
                break;

            //-------------------------------------
            case 'excluir_batida_rm':
                exit($this->mEspelho->ExcluirBatidaRM($dados));
                break;

            //-------------------------------------
            case 'excluir_batida_pt':
                exit($this->mEspelho->ExcluirBatidaPT($dados));
                break;

            //-------------------------------------
            case 'recalcular_espelho':

                $wsTotvs = model('WsrmModel');
                $wsTotvs->ws_recalculo_ponto($dados['chapa'], $dados['data']);
                exit(responseJson('success', '<b>Recalculo</b> processado com sucesso.'));
                break;

            //-------------------------------------
            case 'cadastrar_justificativa_extra':
                exit($this->mEspelho->CadastrarJustificativaExtra($dados));
                break;

            //-------------------------------------
            case 'cadastrar_gestor_excecao':
                exit($this->mEspelho->CadastrarGestorExcecao($dados));
                break;

            //-------------------------------------
            case 'excluir_gestor_excecao':
                exit($this->mEspelho->ExcluirGestorExcecao($dados));
                break;
                
            case 'listar_funcionarios_secao':
                $mAprova = model('Ponto/AprovaModel');
                $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);
                exit(json_encode($mAprova->ListarFuncionariosSecao($dados['codsecao'], $dados, false)));
                break;
            case 'carrega_macro':
                exit(json_encode($this->mEspelho->CarregaMacro($dados)));
            break;
            case 'carrega_colaboradores':
                $dados['rh']    = parent::VerificaPerfil('GLOBAL_RH', false);
                $periodo        = \DateTime::createFromFormat('d/m/Y', substr($dados['periodo'],0,10));
                $dataInicio     = $periodo->format('Y-m-d');
                $result         = $this->mPortal->CarregaColaboradores($dataInicio, $dados, true);
                exit(json_encode($result));
            break;

        }

        

    }

}
