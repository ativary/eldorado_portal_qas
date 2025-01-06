<?php
namespace App\Controllers\Ponto;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Controllers\BaseController;

class Extratobancohoras extends BaseController
{

    private $mPortal;

    public function __construct()
    {
        parent::__construct('Ponto'); // sempre manter

        $this->_moduloName = '<i class="fas fa-clock"></i> Ponto';
        $this->mPortal    = model('PortalModel');
    }

    public function index()
    {
        parent::VerificaPerfil('PONTO_EXTRATOBANCOHORAS');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

        $dados['_titulo']    = "Extrato de banco de horas";
        $this->_breadcrumb->add($dados['_titulo'], 'ponto/extratobancohoras');

        $mSaldoBanco = model('Ponto/SaldobancohorasModel');
        $dados['resSecaoGestor'] = $mSaldoBanco->ListarSecaoUsuario(false, $dados['rh']);
        
        return parent::ViewPortal('ponto/extratobancohoras/index', $dados);
    }

    public function pdf()
    {

        set_time_limit(60*30);

        parent::VerificaPerfil('PONTO_EXTRATOBANCOHORAS');
        $dados['rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

        $mExtrato = model('Ponto/ExtratobancohorasModel');

        $dados['mescomp']   = $this->request->getPost('mescomp');
        $dados['anocomp']   = $this->request->getPost('anocomp');
        $dados['codsecao']  = $this->request->getPost('secao');
        $dados['chapa']     = $this->request->getPost('chapa');

        $msg = '';
        if(($dados['mescomp'] ?? null) == null){
            $msg .= '<br>- <b>Mês comperência</b> não informado';
        }
        if(($dados['anocomp'] ?? null) == null){
            $msg .= '<br>- <b>Ano comperência</b> não informado';
        }
        if($msg != ''){
            notificacao('danger', $msg);
            redireciona(base_url('ponto/extratobancohoras'));
        }

        $objFuncBH      = $mExtrato->geraRelatorioBHFunc($dados);
        $objDadosBH     = $mExtrato->geraRelatorioBH($dados);

        if($objFuncBH){

            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4-L',
                'margin_bottom' => 9,
                'margin_top' => 9,
            ]);

            $mpdf->showImageErrors = true;
	
	
            foreach($objFuncBH as $idx => $value){
                
                
                $html = '
                    <table width="100%" style="font-size: 10px !important;">
                        <tr>
                            <td>Período: '.$dados['mescomp'].'/'.$dados['anocomp'].'</td>
                            <td rowspan="4"><img width="100" src="'.$_SERVER['DOCUMENT_ROOT'].'/public/assets/images/logo-dark.png'.'"></td>
                        </tr>
                        <tr>
                            <td>'.$objFuncBH[$idx]->sindicato.'</td>
                        </tr>
                        <tr>
                            <td>Dia a Dia por Funcionário de '.date('d/m/Y', strtotime($objFuncBH[$idx]->inicio)).' até '.date('d/m/Y', strtotime($objFuncBH[$idx]->fim)).'</td>
                        </tr>
                        <tr>
                            <td>'.$objFuncBH[$idx]->codcusto.' - '.$objFuncBH[$idx]->centrodecusto.'</td>
                        </tr>
                    </table>
                    
                    
                    <table bgcolor="#cccccc" width="100%" cellspacing="2" border="0" style="font-size: 10px !important;">
                        <tr>
                            <td colspan="5">Funcionário: <strong>'.$objFuncBH[$idx]->chapa.' - '.$objFuncBH[$idx]->funcionario.'</strong></td>
                        </tr>
                        <tr>
                            <td align="center" colspan="2">Dia</td>
                            <td align="center">Ocorrência</td>
                            <td align="center" width="70">Horas Positivas</td>
                            <td align="center" width="70">Horas Negativas</td>
                            <td align="center" width="70">Saldo</td>
                        </tr>
                        <tr>
                            <td align="left" bgcolor="#ffffff" colspan="5">Saldo Inicial:</td>
                            <td align="center" bgcolor="#ffffff">'.m2h($objFuncBH[$idx]->saldo).'</td>
                        </tr>
                ';
                
                $total_positivo = 0;
                $total_negativo = 0;
                $saldo = $objFuncBH[$idx]->saldo;
                
                
                if($objDadosBH){
                    foreach($objDadosBH as $idb => $value){
                        if($objDadosBH[$idb]->chapa == $objFuncBH[$idx]->chapa){
                            
                            $total_positivo += ($objDadosBH[$idb]->horaspositivasminutos);
                            $total_negativo += ($objDadosBH[$idb]->horasnegativasminutos);
                            
                            $saldo = $saldo + $objDadosBH[$idb]->horaspositivasminutos;
                            $saldo = $saldo - $objDadosBH[$idb]->horasnegativasminutos;
                            
        
                            
                            $html .= '
                                <tr>
                                    <td bgcolor="#ffffff" align="center" width="60">'.date('d/m/Y', strtotime($objDadosBH[$idb]->data)).'</td> 
                                    <td bgcolor="#ffffff" align="center" width="30">'.diaSemana(date('Y-m-d', strtotime($objDadosBH[$idb]->data))).'</td>
                                    <td bgcolor="#ffffff" align="center">'.( strlen(trim($objDadosBH[$idb]->eventopositivo)) > 0 ? $objDadosBH[$idb]->eventopositivo : $objDadosBH[$idb]->eventonegativo ).'</td>
                                    <td bgcolor="#ffffff" align="center">'.( strlen(trim($objDadosBH[$idb]->horaspositivas)) > 0 ? m2h($objDadosBH[$idb]->horaspositivasminutos) : '' ).'</td>
                                    <td bgcolor="#ffffff" align="center">'.( strlen(trim($objDadosBH[$idb]->horasnegativas)) > 0 ? m2h($objDadosBH[$idb]->horasnegativasminutos) : '' ).'</td>
                                    <td bgcolor="#ffffff" align="center">'.m2h($saldo).'</td>
                                </tr>
                            ';
                            
                        }
                    }
                }
                
                
                
                $html .= '
                    <tr>
                        <td align="left" colspan="3" width="100"><strong>Total</strong></td>
                        <td align="center" width="70"><strong>'.m2h($total_positivo).'</strong></td>
                        <td align="center" width="70"><strong>'.m2h($total_negativo).'</strong></td>
                        <td align="center" width="70"><strong>'.m2h($saldo).'</strong></td>
                    </tr>
                ';
                
                
                $html .= '</table>';
                
                if($idx == 0){
                    $mpdf->WriteHTML(($html));
                }else{
                    $mpdf->AddPage();
                    $mpdf->WriteHTML(($html));
                }
                
            }

            $mpdf->Output('ExtratoBancoDeHoras.pdf', 'I');
            exit();
        }

    }

    //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        $dados = $_POST;
        if(!$dados) return false;
        
        switch($act){
            
        }        

    }

}