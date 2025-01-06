<?php
namespace App\Controllers\Remuneracao;
use App\Controllers\BaseController;


Class Cartas extends BaseController {

    public $mCartas;

    public function __construct()
    {
        
        parent::__construct('Remuneração'); // sempre manter
        $this->_moduloName = '<i class="mdi mdi-cash-usd"></i> Remuneração';
        $this->_breadcrumb->add('Cartas', 'remuneracao/cartas');
        $this->mCartas = model('Remuneracao/CartasModel');

    }

    public function index(){

        parent::VerificaPerfil('REMUNERACAO_CARTAS');
        $dados['_titulo'] = "Cartas";
        $dados['resCartas'] = $this->mCartas->ListarCartas();

        return parent::ViewPortal('remuneracao/cartas/index', $dados);

    }

    public function novo(){

        parent::VerificaPerfil('REMUNERACAO_CARTAS');
        $this->_breadcrumb->add('Nova Carta', 'remuneracao/cartas');

        $mPortal = model('PortalModel');

        $dados['resSecao'] = $mPortal->ListarSecao();
        $dados['resFuncao'] = $mPortal->ListarFuncao();
        $dados['resFilial'] = $mPortal->ListarFilial();

        $dados['_titulo'] = "Nova Carta";

        return parent::ViewPortal('remuneracao/cartas/carta_novo', $dados);

    }

    public function editar($id = null){

        if(!$id) redireciona(base_url('remuneracao/cartas'));
        $id = cid($id);
        $dados['id'] = $id;

        parent::VerificaPerfil('REMUNERACAO_CARTAS');
        $this->_breadcrumb->add('Editar Carta', 'remuneracao/cartas');

        $resCartas = $this->mCartas->ListarCartas($id);
        if(!$resCartas){
            notificacao('Registro não localizado.');
            redirect(base_url('remuneracao/cartas'));
        }

        $mPortal = model('PortalModel');

        $dados['resSecao'] = $mPortal->ListarSecao();
        $dados['resFuncao'] = $mPortal->ListarFuncao();
        $dados['resFilial'] = $mPortal->ListarFilial();

        $dados['secao'] = unserialize($resCartas[0]['secao']);
        $dados['funcao'] = unserialize($resCartas[0]['funcao']);
        $dados['filial'] = unserialize($resCartas[0]['filial']);

        $dados['Carta'] = $resCartas[0];
        $dados['_titulo'] = "Editar Carta | <span class=\"badge badge-info\">Nº {$id}</span>";

        return parent::ViewPortal('remuneracao/cartas/carta_editar', $dados);

    }

    public function paginas($id_carta = null){

        if(!$id_carta) redireciona(base_url('remuneracao/cartas'));
        $id_carta = cid($id_carta);

        parent::VerificaPerfil('REMUNERACAO_CARTAS');
        $this->_breadcrumb->add('Páginas da Cartas', 'remuneracao/cartas/paginas');

        $resCartas = $this->mCartas->ListarCartas($id_carta);
        $dados['resPaginas'] = $this->mCartas->ListarCartasPaginas($id_carta);
        $dados['id_carta'] = $id_carta;

        $dados['_titulo'] = 'Páginas da Carta - <span class="text-primary">'.$resCartas[0]['descricao'].'</span>';

        return parent::ViewPortal('remuneracao/cartas/paginas', $dados);

    }

    public function pagina_novo($id_carta = null){

        if(!$id_carta) redireciona(base_url('remuneracao/cartas'));

        $id_carta = cid($id_carta);

        parent::VerificaPerfil('REMUNERACAO_CARTAS');
        $this->_breadcrumb->add('Páginas da Cartas', 'remuneracao/cartas/paginas/'.id($id_carta));
        $this->_breadcrumb->add('Nova Página');

        $dados['id_carta'] = $id_carta;

        $dados['_titulo'] = 'Nova Página';

        return parent::ViewPortal('remuneracao/cartas/pagina_novo', $dados);

    }

    public function pagina_editar($id_pagina = null, $id_carta = null){

        if(!$id_pagina || !$id_carta) redireciona(base_url('remuneracao/cartas'));

        $id_pagina = cid($id_pagina);
        $id_carta = cid($id_carta);

        parent::VerificaPerfil('REMUNERACAO_CARTAS');
        $this->_breadcrumb->add('Páginas da Cartas', 'remuneracao/cartas/paginas/'.id($id_carta));
        $this->_breadcrumb->add('Editar Página');

        $Pagina = $this->mCartas->ListarCartasPaginas($id_carta, $id_pagina);
        $dados['Pagina'] = $Pagina[0];
        $dados['id_pagina'] = $id_pagina;
        $dados['id_carta'] = $id_carta;

        $dados['_titulo'] = 'Editar Página: <span class="text-primary">'.$Pagina[0]['descricao'].'</span>';

        return parent::ViewPortal('remuneracao/cartas/pagina_editar', $dados);

    }

    public function gerar_carta($tipo = null, $id_req = null, $output = 'I', $adicioal = array()){

        if($tipo == null || $id_req == null) redireciona(base_url('/'));

        $Funcionario = $this->mCartas->PegaDadosRequisicao($tipo, $id_req);
        $tipo_carta = 1;
        $mSimulador = model('Remuneracao/SimuladorModel');

        if(!isset($_SESSION['func_coligada'])) $_SESSION['func_coligada'] = $Funcionario['CODCOLIGADA'];
        $DadosAtual[0] = array();

        if($Funcionario){
           // echo 'Tipo: '.$tipo.'
            //';
            //print_r($Funcionario);
            /*echo '<pre>';
            print_r($Funcionario);
            exit();*/
            if(($Funcionario['requisicao']['codmotivo'] ?? 0) == 1 || ($Funcionario['requisicao']['codmotivo'] ?? 0) == 12 || ((int)$Funcionario['requisicao']['secao_motivo'] ?? 0) == 2){
                $DadosAtual = $mSimulador->ExecutaSimuladorCalculo(null, $Funcionario['CHAPA'], $Funcionario['requisicao']['posicao_nova']);
                $DadosNovo = $mSimulador->ExecutaSimuladorCalculo(null, null, $Funcionario['requisicao']['posicao_nova']);

                //print_r($DadosAtual);
                //print_r($DadosNovo);

            }

            if($tipo == 2){
                $DadosAtual[0] = array();
                //$DadosNovo[0] = $Funcionario;
                $mSimulador = model('Remuneracao/SimuladorModel');

                $DadosNovo = $mSimulador->ExecutaSimuladorCalculo('R', null, $Funcionario['posicao_nova']);
                /*echo '<pre>';
                print_r($DadosNovo);
                exit();*/
            }

            if(($tipo == 1 || $tipo == 2) && ($Funcionario['requisicao']['codmotivo'] ?? 0) == 1) $tipo_carta = 1; // requisição promoção
            if(($tipo == 1 || $tipo == 2) && ($Funcionario['requisicao']['codmotivo'] ?? 0) == 2) $tipo_carta = 2; // requisição merito
            if(($tipo == 1 || $tipo == 2) && ($Funcionario['requisicao']['codmotivo'] ?? 0) == 3) $tipo_carta = 4; // requisição enquadramento

            // meritocracia
            if(($tipo == 4) && ((int)$Funcionario['requisicao']['secao_motivo'] ?? 0) == 2) $tipo_carta = 1; // requisição promoção
            if(($tipo == 4) && ((int)$Funcionario['requisicao']['secao_motivo'] ?? 0) == 3) $tipo_carta = 2; // requisição merito
            if(($tipo == 4) && ((int)$Funcionario['requisicao']['secao_motivo'] ?? 0) == 7) $tipo_carta = 4; // requisição enquadramento

            if($tipo == 3){
                $tipo_carta = 3; // requisição aumento de quadro
                
                if(($adicioal['requisicao_aumento_quadro_salario'] ?? null) != null){
                    $Funcionario['SALARIO'] = str_replace(',', '.', str_replace('.', '', $adicioal['requisicao_aumento_quadro_salario']));
                }

                $DadosNovo = $mSimulador->ExecutaSimuladorCalculo('R', null, $Funcionario['posicao_nova']);

            }
        }

        $data_extenso = dataExtenso();
        $requisicao_aumento_quadro_nome = ($adicioal['requisicao_aumento_quadro_nome'] ?? null != null) ? $adicioal['requisicao_aumento_quadro_nome'] : "";
        if($adicioal['requisicao_aumento_quadro_data'] ?? null != null) $data_extenso = dataExtenso($adicioal['requisicao_aumento_quadro_data']);


        

       

        // de x para
        $array_de = array(
            // DADOS PFUNC
            '[DBPORTAL]',
            '[DBRM]',
            '[CODCOLIGADA]',
            '[CHAPA]',
            '[NOME]',
            '[IDREQ]',
            '[IDUSUARIO]',
            '[CPF]',
            '[EMAIL]',
            '[CODSITUACAO]',
            '[DATAADMISSAO]',
            '[DATADEMISSAO]',
            '[CODSECAO]',
            '[NOMESECAO]',
            '[CODFUNCAO]',
            '[NOMEFUNCAO]',
            '[NOMECOLIGADA]',
            '[NOMEFANTASIACOLIGADA]',
            '[RUA]',
            '[BAIRRO]',
            '[CEP]',
            '[CIDADE]',
            '[ESTADO]',
            '[PIS]',
            '[RG]',
            '[NOMEBANCO]',
            '[PAGTO_BANCO]',
            '[PAGTO_AGENCIA]',
            '[PAGTO_CONTA]',
            '[SALARIO]',
            '[SALARIO_BR]',
            '[CODTIPO]',
            '[DTNASCIMENTO]',
            '[SEXO]',
            '[NUMERO]',
            '[COMPLEMENTO]',
            '[TELEFONE1]',
            '[TELEFONE2]',
            '[CODPESSOA]',
            '[DTEMISSAOIDENT]',
            '[ORGEMISSORIDENT]',
            '[CARTEIRATRAB]',
            '[SERIECARTTRAB]',
            '[DTCARTTRAB]',
            '[UFCARTTRAB]',
            '[CARTMOTORISTA]',
            '[TIPOCARTHABILIT]',
            '[TIPOCARTHABILIT]',
            '[DTVENCHABILIT]',
            '[CODHORARIO]',
            '[NOMEHORARIO]',
            '[IDMERITOCRACIA]',
            // SIMULAÇÃO DADOS ATUAL
            '[ATU_SALARIO_MENSAL]',
            '[ATU_ADIC_TRITREM]',
            '[ATU_PERICULOSIDADE]',
            '[ATU_PREMIO_PRODUCAO]',
            '[ATU_MEDIA_HORA_EXTRA_50]',
            '[ATU_MEDIA_HORA_EXTRA_80]',
            '[ATU_MEDIA_HORA_EXTRA_100]',
            '[ATU_MEDIA_NONA_HORA]',
            '[ATU_MEDIA_ESPERA_HORA]',
            '[ATU_MEDIA_DSR_HE]',
            '[ATU_MEDIA_ADIC_NOTURNO]',
            '[ATU_ADIC_ASSIDUIDADE]',
            '[ATU_PPR]',
            '[ATU_PPR_CALC]',
            '[ATU_PRV]',
            '[ATU_PRV_CALC]',
            '[ATU_SUPERACAO]',
            '[ATU_SUPERACAO_CALC]',
            '[ATU_RVD_CALC]',
            '[ATU_PP_IND_CALC]',
            '[ATU_PLANO_SAUDE]',
            '[ATU_PLANO_SAUDE_CALC]',
            '[ATU_PLANO_SAUDE_VALOR_UNITARIO]',
            '[ATU_PLANO_SAUDE_OPERADORA]',
            '[ATU_CALC_PREVIDENCIA_PRIVADA]',
            '[ATU_CALC_SEGURO_VIDA]',
            '[ATU_CODCATEGORIA]',
            '[ATU_CALC_INSS]',
            '[ATU_BASE_CALC]',
            '[ATU_SALARIO_13]',
            '[ATU_FERIAS]',
            '[ATU_FGTS]',
            '[ATU_INSS]',
            '[ATU_TRANSPORTE]',
            '[ATU_PREVIDENCIA_PRIVADA]',
            '[ATU_SEGURO_VIDA]',
            '[ATU_PP_IND]',
            '[ATU_RVD]',
            '[ATU_VR]',
            '[ATU_VA]',
            '[ATU_PREMIO_PRODUCAO_12]',
            '[ATU_CODFILIAL]',
            // SIMULAÇÃO DADOS CALCULADOR
            '[CALC_SALARIO_MENSAL]',
            '[CALC_ADIC_TRITREM]',
            '[CALC_PERICULOSIDADE]',
            '[CALC_PREMIO_PRODUCAO]',
            '[CALC_MEDIA_HORA_EXTRA_50]',
            '[CALC_MEDIA_HORA_EXTRA_80]',
            '[CALC_MEDIA_HORA_EXTRA_100]',
            '[CALC_MEDIA_NONA_HORA]',
            '[CALC_MEDIA_ESPERA_HORA]',
            '[CALC_MEDIA_DSR_HE]',
            '[CALC_MEDIA_ADIC_NOTURNO]',
            '[CALC_ADIC_ASSIDUIDADE]',
            '[CALC_PPR]',
            '[CALC_PPR_CALC]',
            '[CALC_PRV]',
            '[CALC_PRV_CALC]',
            '[CALC_SUPERACAO]',
            '[CALC_SUPERACAO_CALC]',
            '[CALC_RVD_CALC]',
            '[CALC_PP_IND_CALC]',
            '[CALC_PLANO_SAUDE]',
            '[CALC_PLANO_SAUDE_CALC]',
            '[CALC_PLANO_SAUDE_VALOR_UNITARIO]',
            '[CALC_PLANO_SAUDE_OPERADORA]',
            '[CALC_CALC_PREVIDENCIA_PRIVADA]',
            '[CALC_CALC_SEGURO_VIDA]',
            '[CALC_CODCATEGORIA]',
            '[CALC_CALC_INSS]',
            '[CALC_BASE_CALC]',
            '[CALC_SALARIO_13]',
            '[CALC_FERIAS]',
            '[CALC_FGTS]',
            '[CALC_INSS]',
            '[CALC_TRANSPORTE]',
            '[CALC_PREVIDENCIA_PRIVADA]',
            '[CALC_SEGURO_VIDA]',
            '[CALC_PP_IND]',
            '[CALC_RVD]',
            '[CALC_VR]',
            '[CALC_VA]',
            '[CALC_PREMIO_PRODUCAO_12]',
            '[DATA_EXTENSO]',
            '[NOME_DIGITADO]',
            '[CALC_CODFILIAL]',
            '[BENEFICIO_MORADIA]',
            '[BENEFICIO_MUDANCA]',
            '[PREMIO_PRODUCAO]'
        );
        $array_para = array(
            DBPORTAL_BANCO,
            DBRM_BANCO,
            $Funcionario['CODCOLIGADA'] ?? session()->get('func_coligada'),
            $Funcionario['CHAPA'] ?? null,
            $Funcionario['NOME'] ?? null,
            $id_req,
            session()->get('log_id'),
            $Funcionario['CPF'] ?? null,
            $Funcionario['EMAIL'] ?? null,
            $Funcionario['CODSITUACAO'] ?? null,
            $Funcionario['DATAADMISSAO'] ?? null,
            $Funcionario['DATADEMISSAO'] ?? null,
            $Funcionario['CODSECAO'] ?? null,
            $Funcionario['NOMESECAO'] ?? null,
            $Funcionario['CODFUNCAO'] ?? null,
            $Funcionario['NOMEFUNCAO'] ?? null,
            $Funcionario['NOMECOLIGADA'] ?? null,
            $Funcionario['NOMEFANTASIACOLIGADA'] ?? null,
            $Funcionario['RUA'] ?? null,
            $Funcionario['BAIRRO'] ?? null,
            $Funcionario['CEP'] ?? null,
            $Funcionario['CIDADE'] ?? null,
            $Funcionario['ESTADO'] ?? null,
            $Funcionario['PIS'] ?? null,
            $Funcionario['RG'] ?? null,
            $Funcionario['NOMEBANCO'] ?? null,
            $Funcionario['PAGTO_BANCO'] ?? null,
            $Funcionario['PAGTO_AGENCIA'] ?? null,
            $Funcionario['PAGTO_CONTA'] ?? null,
            $Funcionario['SALARIO'] ?? null,
            moeda($Funcionario['SALARIO'] ?? null),
            $Funcionario['CODTIPO'] ?? null,
            $Funcionario['DTNASCIMENTO'] ?? null,
            $Funcionario['SEXO'] ?? null,
            $Funcionario['NUMERO'] ?? null,
            $Funcionario['COMPLEMENTO'] ?? null,
            $Funcionario['TELEFONE1'] ?? null,
            $Funcionario['TELEFONE2'] ?? null,
            $Funcionario['CODPESSOA'] ?? null,
            $Funcionario['DTEMISSAOIDENT'] ?? null,
            $Funcionario['ORGEMISSORIDENT'] ?? null,
            $Funcionario['CARTEIRATRAB'] ?? null,
            $Funcionario['SERIECARTTRAB'] ?? null,
            $Funcionario['DTCARTTRAB'] ?? null,
            $Funcionario['UFCARTTRAB'] ?? null,
            $Funcionario['CARTMOTORISTA'] ?? null,
            $Funcionario['TIPOCARTHABILIT'] ?? null,
            $Funcionario['TIPOCARTHABILIT'] ?? null,
            $Funcionario['DTVENCHABILIT'] ?? null,
            $Funcionario['CODHORARIO'] ?? null,
            $Funcionario['NOMEHORARIO'] ?? null,
            $Funcionario['requisicao']['id_meritocracia'] ?? 0,

            moeda($DadosAtual[0]['SALARIO_MENSAL'] ?? null),
            $DadosAtual[0]['ADIC_TRITREM'] ?? null,
            $DadosAtual[0]['PERICULOSIDADE'] ?? null,
            $DadosAtual[0]['PREMIO_PRODUCAO'] ?? null,
            $DadosAtual[0]['MEDIA_HORA_EXTRA_50'] ?? null,
            $DadosAtual[0]['MEDIA_HORA_EXTRA_80'] ?? null,
            $DadosAtual[0]['MEDIA_HORA_EXTRA_100'] ?? null,
            $DadosAtual[0]['MEDIA_NONA_HORA'] ?? null,
            $DadosAtual[0]['MEDIA_ESPERA_HORA'] ?? null,
            $DadosAtual[0]['MEDIA_DSR_HE'] ?? null,
            $DadosAtual[0]['MEDIA_ADIC_NOTURNO'] ?? null,
            $DadosAtual[0]['ADIC_ASSIDUIDADE'] ?? null,
            (moeda($DadosAtual[0]['PPR'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PPR']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['PPR_CALC'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PPR_CALC']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['PRV'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PRV']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['PRV_CALC'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PRV_CALC']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['SUPERACAO'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['SUPERACAO']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['SUPERACAO_CALC'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['SUPERACAO_CALC']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['RVD_CALC'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['RVD_CALC']) : 'Não Elegível'),
            $DadosAtual[0]['PP_IND_CALC'] ?? null,
            (moeda($DadosAtual[0]['PLANO_SAUDE'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PLANO_SAUDE']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['PLANO_SAUDE_CALC'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PLANO_SAUDE_CALC'])."%" : 'Não Elegível'),
            (moeda($DadosAtual[0]['PLANO_SAUDE_VALOR_UNITARIO'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PLANO_SAUDE_VALOR_UNITARIO']) : 'Não Elegível'),
            $DadosAtual[0]['PLANO_SAUDE_OPERADORA'] ?? '',
            moeda($DadosAtual[0]['CALC_PREVIDENCIA_PRIVADA'] ?? 0),
            $DadosAtual[0]['CALC_SEGURO_VIDA'] ?? null,
            $DadosAtual[0]['CODCATEGORIA'] ?? null,
            $DadosAtual[0]['CALC_INSS'] ?? null,
            $DadosAtual[0]['BASE_CALC'] ?? null,
            moeda($DadosAtual[0]['SALARIO_13'] ?? null),
            moeda($DadosAtual[0]['FERIAS'] ?? null),
            moeda($DadosAtual[0]['FGTS'] ?? null),
            moeda($DadosAtual[0]['INSS'] ?? null),
            moeda($DadosAtual[0]['TRANSPORTE'] ?? null),
            moeda($DadosAtual[0]['PREVIDENCIA_PRIVADA'] ?? null),
            moeda($DadosAtual[0]['SEGURO_VIDA'] ?? null),
            moeda($DadosAtual[0]['PP_IND'] ?? null),
            (moeda($DadosAtual[0]['RVD'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['RVD']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['VR'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['VR']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['VA'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['VA']) : 'Não Elegível'),
            (moeda($DadosAtual[0]['PREMIO_PRODUCAO_12'] ?? 0) != "0,00" ? moeda($DadosAtual[0]['PREMIO_PRODUCAO_12']) : 'Não Elegível'),
            (int)($DadosAtual[0]['CODFILIAL'] ?? 0),

            $DadosNovo[0]['SALARIO_MENSAL'] ?? null,
            $DadosNovo[0]['ADIC_TRITREM'] ?? null,
            $DadosNovo[0]['PERICULOSIDADE'] ?? null,
            $DadosNovo[0]['PREMIO_PRODUCAO'] ?? null,
            $DadosNovo[0]['MEDIA_HORA_EXTRA_50'] ?? null,
            $DadosNovo[0]['MEDIA_HORA_EXTRA_80'] ?? null,
            $DadosNovo[0]['MEDIA_HORA_EXTRA_100'] ?? null,
            $DadosNovo[0]['MEDIA_NONA_HORA'] ?? null,
            $DadosNovo[0]['MEDIA_ESPERA_HORA'] ?? null,
            $DadosNovo[0]['MEDIA_DSR_HE'] ?? null,
            $DadosNovo[0]['MEDIA_ADIC_NOTURNO'] ?? null,
            $DadosNovo[0]['ADIC_ASSIDUIDADE'] ?? null,
            (moeda($DadosNovo[0]['PPR'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PPR']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['PPR_CALC'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PPR_CALC']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['PRV'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PRV']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['PRV_CALC'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PRV_CALC']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['SUPERACAO'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['SUPERACAO']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['SUPERACAO_CALC'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['SUPERACAO_CALC']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['RVD_CALC'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['RVD_CALC']) : 'Não Elegível'),
            $DadosNovo[0]['PP_IND_CALC'] ?? null,
            (moeda($DadosNovo[0]['PLANO_SAUDE'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PLANO_SAUDE']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['PLANO_SAUDE_CALC'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PLANO_SAUDE_CALC'])."%" : 'Não Elegível'),
            (moeda($DadosNovo[0]['PLANO_SAUDE_VALOR_UNITARIO'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PLANO_SAUDE_VALOR_UNITARIO']) : 'Não Elegível'),
            $DadosNovo[0]['PLANO_SAUDE_OPERADORA'] ?? '',
            moeda($DadosNovo[0]['CALC_PREVIDENCIA_PRIVADA'] ?? 0),
            $DadosNovo[0]['CALC_SEGURO_VIDA'] ?? null,
            $DadosNovo[0]['CODCATEGORIA'] ?? null,
            $DadosNovo[0]['CALC_INSS'] ?? null,
            $DadosNovo[0]['BASE_CALC'] ?? null,
            moeda($DadosNovo[0]['SALARIO_13'] ?? null),
            moeda($DadosNovo[0]['FERIAS'] ?? null),
            moeda($DadosNovo[0]['FGTS'] ?? null),
            moeda($DadosNovo[0]['INSS'] ?? null),
            moeda($DadosNovo[0]['TRANSPORTE'] ?? null),
            moeda($DadosNovo[0]['PREVIDENCIA_PRIVADA'] ?? null),
            moeda($DadosNovo[0]['SEGURO_VIDA'] ?? null),
            moeda($DadosNovo[0]['PP_IND'] ?? null),
            (moeda($DadosNovo[0]['RVD'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['RVD']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['VR'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['VR']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['VA'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['VA']) : 'Não Elegível'),
            (moeda($DadosNovo[0]['PREMIO_PRODUCAO_12'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PREMIO_PRODUCAO_12']) : 'Não Elegível'),
            $data_extenso,
            $requisicao_aumento_quadro_nome,
            (int)($DadosNovo[0]['CODFILIAL'] ?? 0),
            (($adicioal['requisicao_aumento_quadro_moradia'] ?? 0) == 1) ? '<table class="tabela" width="100%" style="margin-bottom: 30px;"><tr><td align="center" width="80"><img style="margin-bottom: 10px;" src="../../../../imgcartas/Icone Hospedagem - Req. Carta Proposta.jpg" alt="" width="50" height="50" /></td><td><span style="color: #008000;"><strong>HOSPEDAGEM E AUXÍLIO MORADIA</strong></span><br />Hospedagem por até 30 dias, visando a sua adaptação na região, com alimentação e serviços de lavanderia inclusos.<br /><br />Ao término desse prazo a empresa concederá auxílio-aluguel no valor de até R$ 1.500,00 (hum mil e quinhentos reais – se for para cargos de analista, supervisores, operadores de área ou painel) e se for posição de Especialista, Coordenador e Gerente, o valor será de R$3.000,00 (três mil reais) durante o período de 03 (três meses), mediante a apresentação do contrato de aluguel em seu nome pago em folha. Para tanto o benefício deverá ser acionado até o período máximo de 6 meses a contar da data de admissão. Após esse período, caso não acionado o benefício automaticamente será extinto da proposta de trabalho.</td></tr></table>' : '',
            (($adicioal['requisicao_aumento_quadro_mudanca'] ?? 0) == 1) ? '<table class="tabela" width="100%" style="margin-bottom: 30px;"><tr><td align="center" width="80"><img style="margin-bottom: 10px;" src="../../../../imgcartas/Icone Desp. Mudança - Req. Carta Proposta.jpg" alt="" width="50" height="50" /></td><td><span style="color: #008000;"><strong>DESPESAS COM MUDANÇA</strong></span><br />Será subsidiada do local de origem até o local de moradia, com limite de 50m3, incluindo 1 carro e 1 moto. Não contempla transporte de animais de estimação.</td></tr></table>' : '',
            (
                ((int)($DadosNovo[0]['CODFILIAL'] ?? 0) == 1 || (int)($DadosNovo[0]['CODFILIAL'] ?? 0) == 2 || (int)($DadosNovo[0]['CODFILIAL'] ?? 0) == 35) &&
                ($Funcionario['CODCOLIGADA'] ?? session()->get('func_coligada')) == 1
            ) ? '<p style="clear: both; margin-bottom: 35px;"><img style="float: left; margin-right: 20px;" src="../../../../imgcartas/Icone Prêmio Produção - Req. Carta Proposta.jpg" alt="" width="50" height="50" /><span style="color: #008000;"><strong>PRÊMIO DE PRODUTIVIDADE</strong></span><br />O Prêmio de Produtividade tem valor mensal de até '.(moeda($DadosNovo[0]['PP_IND'] ?? 0) != "0,00" ? moeda($DadosNovo[0]['PP_IND']) : 'Não Elegível').' pago de forma acumulada semestralmente e proporcional ao alcance das metas e tempo trabalhado.</p>' : ''
        );

        $resPaginas = $this->mCartas->GerarCartasPagina($tipo_carta);

        /*echo "Tipo Carta: ".$tipo_carta;

        print_r($resPaginas);*/

        if($resPaginas){

            $mpdf = new \Mpdf\Mpdf([
                'format' => 'A4',
                'margin_bottom' => 28,
                'margin_top' => 9,
                'default_font' => 'tahoma',
                'default_font_size' => 10,
            ]);
            $mpdf->showImageErrors = true;

            foreach($resPaginas as $key => $Pagina){

                //echo $Pagina['titulo_pagina'].'<br>';
                $texto_pagina = base64_decode($Pagina['texto']);

                $texto_pagina = str_replace($array_de, $array_para, $texto_pagina);
                $texto_pagina = str_replace('../../../../', $_SERVER['DOCUMENT_ROOT'].'/', $texto_pagina);

                // altera a tabela da carta para meritocracia
                if($tipo == 4){
                    $texto_pagina = str_replace('WHERE id_requisicao', "WHERE id",  $texto_pagina);
                    $texto_pagina = str_replace('DATA_PROCESSAMENTO', 'DATA_PROCESSAMENTO_MERITOCRACIA',  $texto_pagina);
                    $texto_pagina = str_replace('zcrmportal_requisicao ', 'zcrmportal_requisicao_meritocracia ', $texto_pagina);
                }

                preg_match_all("/\{([A-Z]{5}:(.*?))\}/i",$texto_pagina, $return);
                foreach($return[1] as $ids => $value){
                    $texto_pagina = $this->executa_sql($return[0][$ids], $return[1][$ids], $texto_pagina);
                }

                if($key == 0){
                    $texto_pagina .= "<style>
                        td, th {border: 1px solid #000000;} 
                        table {border-collapse: collapse;} 
                        .tabela td {border: none !important;vertical-align: top;} 
                        .remover {height: 0px !important; padding: 0 !important; margin: 0 !important; display: none;} 
                        .remover td {display: none !important; border: none !important; visibility: hidden; height: 0px !important; padding: 0 !important; overflow: hidden; line-height: 0px; margin: 0 !important; font-size: 0px}
                    </style>";
                }


                $texto_pagina = preg_replace('/\[NAOEXIBIR](.*)\[\/FIMREGRA]/', '', $texto_pagina);
                $texto_pagina = str_replace(array('[EXIBIR]', '[/FIMREGRA]'), '', $texto_pagina);
                $texto_pagina = str_replace('[B]', '<strong>', $texto_pagina);
                $texto_pagina = str_replace('[/B]', '</strong>', $texto_pagina);
                
//echo $texto_pagina;
                $mpdf->AddPage();
                $mpdf->WriteHTML($texto_pagina);
                //echo $texto_pagina;
                if($tipo_carta != 1 && $tipo_carta != 2 && $tipo_carta != 4) $mpdf->SetHTMLFooter('<table width="100%"><tr><td align="right" style="border: none !important;"><img src="'.$_SERVER['DOCUMENT_ROOT'].'/imgcartas/Marca DGua - Item 7.jpg"></td></tr></table>');
                //ECHO $texto_pagina;
                //$mpdf->WriteHTML('<img src="'.ib64(base_url('public/assets/images/logo_pdf.jpg')).'"><br><br>'.$texto_pagina);
                

            }
//exit();
            /*
            * F - salva o arquivo NO SERVIDOR
            * I - abre no navegador E NÃO SALVA
            * D - chama o prompt E SALVA NO CLIENTE
            */

            /*echo '<pre>';
            echo 'Tipo: '.$tipo."
            ";
    echo $texto_pagina;
            exit();*/

            

            if($output == "I"){
                header("Content-type:application/pdf");
                return $mpdf->Output("Carta.pdf", $output);
            }
            if($output != "I") return $mpdf->Output("Carta.pdf", $output);
            //return $mpdf->Output("Carta.pdf", $output);
            //exit();
            
        }

    }

    public function gerar_carta_global($carta, $chapa, $ano, $secao){
    

        $DadosFuncionarios = $this->mCartas->ListarFuncionariosCartaGlobal($chapa, $ano, $secao);

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_bottom' => 9,
            'margin_top' => 9,
            'default_font' => 'arial',
            'default_font_size' => 11,
        ]);
        $mpdf->showImageErrors = true;

        $EstruturaPagina = $this->mCartas->GerarCartasPaginaGlobal($carta);

        if($DadosFuncionarios){
            foreach($DadosFuncionarios as $key => $Funcionario){

                // de x para
                $array_de = array(
                    // DADOS PFUNC
                    '[DBPORTAL]',
                    '[DBRM]',
                    '[CODCOLIGADA]',
                    '[CHAPA]',
                    '[NOME]',
                    '[CODSECAO]',
                    '[CODFUNCAO]'
                );
                $array_para = array(
                    DBPORTAL_BANCO,
                    DBRM_BANCO,
                    $Funcionario['CODCOLIGADA'] ?? session()->get('func_coligada'),
                    $Funcionario['CHAPA'] ?? null,
                    $Funcionario['NOME'] ?? null,
                    $Funcionario['CODSECAO'] ?? null,
                    $Funcionario['CODFUNCAO'] ?? null
                );

                $resPaginas = $EstruturaPagina;
                if($resPaginas){

                    

                    foreach($resPaginas as $key => $Pagina){

                        //echo $Pagina['titulo_pagina'].'<br>';
                        $texto_pagina = base64_decode($Pagina['texto']);
                        $texto_pagina = str_replace($array_de, $array_para, $texto_pagina);

                        $texto_pagina = str_replace('../../../../', $_SERVER['DOCUMENT_ROOT'].'/', $texto_pagina);

                        preg_match_all("/\{([A-Z]{5}:(.*?))\}/i",$texto_pagina, $return);
                        foreach($return[1] as $ids => $value){
                            $texto_pagina = $this->executa_sql($return[0][$ids], $return[1][$ids], $texto_pagina);
                        }

                        if($key == 0){
                            $texto_pagina .= "<style>td, th {border: 1px solid #000000;} table {border-collapse: collapse;}</style>";
                        }


                        $texto_pagina = preg_replace('/\[OCULTA](.*)\[\/FIMREGRA]/', '', $texto_pagina);
                        $texto_pagina = str_replace(array('[EXIBE]', '[/FIMREGRA]'), '', $texto_pagina);
                        

                        $mpdf->AddPage();
                        $mpdf->WriteHTML($texto_pagina);
                        //$mpdf->WriteHTML('<img src="'.ib64(base_url('public/assets/images/logo_pdf.jpg')).'"><br><br>'.$texto_pagina);
                        

                    }
                    
                }

            }
        }

        header("Content-type:application/pdf");
        return $mpdf->Output("Carta_Global.pdf", 'I');
        

    }

    public function executa_sql($substituir, $sql, $texto_pagina){


        $banco = substr(trim($sql),0, 5);
        switch($banco){
            case 'SQLRM':

                $response = $this->mCartas->executaSQL(substr($sql, 6), 'RM');
                return str_replace($substituir, $response, $texto_pagina);

                break;
            case 'SQLPT':

                $response = $this->mCartas->executaSQL(substr($sql, 6), 'PT');
                return str_replace($substituir, $response, $texto_pagina);

                break;
            default: return $texto_pagina;
        }


    }

    public function EnviarCartaGestor(){

        set_time_limit(60 * 10);

        $gestores = $this->mCartas->CronEnviaCartaGestorRequisicaoPromocao();
        
        if($gestores){
            foreach($gestores as $key => $Gestor){

                $PDF_CARTA = base64_encode($this->gerar_carta(1, $Gestor['id'], 'S'));


                //echo $PDF_CARTA;
                $anexo = array(); // limpa array       
                $anexo = array(
                    0 => array(
                        'file_base64' => $PDF_CARTA,
                        'file_name' => 'Carta '.$Gestor['nome_funcionario'].'.pdf',
                        'file_mime' => 'application/pdf'
                    )
                );

                $html = 'Olá <b>'.$Gestor['NOME'].',</b><br>
                <br>
Segue em anexo a Carta da requisição de alteração do colaborador <b>'.$Gestor['nome_funcionario'].'</b> Nº '.$Gestor['id'].'';
                $email_enviado = enviaEmail('tiago.moselli@crmservices.com.br', 'Carta da Requisição de Alteração | '.$Gestor['id'], $html, $anexo);
                
                if($email_enviado){
                    $this->mCartas->CadastrarCartasPDFRequisicao($Gestor['id'], $PDF_CARTA);
                }

                unset($Gestor);
            }
        }

        $this->EnviarCartaGestorMeritocracia();

    }

    public function EnviarCartaGestorMeritocracia(){
        
        $gestores = $this->mCartas->CronEnviaCartaGestorRequisicaoMeritocracia();
        
        if($gestores){
            foreach($gestores as $key => $Gestor){

                $PDF_CARTA = base64_encode($this->gerar_carta(4, $Gestor['id'], 'S'));
                
                $anexo = array();                
                $anexo = array(
                    0 => array(
                        'file_base64' => $PDF_CARTA,
                        'file_name' => 'Carta '.$Gestor['nome_funcionario'].'.pdf',
                        'file_mime' => 'application/pdf'
                    )
                );

                $html = 'Olá <b>'.$Gestor['NOME'].',</b><br>
                <br>
Segue em anexo a Carta da requisição de alteração (meritocrácia) do colaborador <b>'.$Gestor['nome_funcionario'].'</b> Nº '.$Gestor['id'].'';


                //$email_enviado = enviaEmail('tiago.moselli@crmservices.com.br', 'Carta da Requisição de Alteração (Meritocrácia) | '.$Gestor['id'], $html, $anexo);
                $email_enviado = enviaEmail('tiago.moselli@crmservices.com.br', 'Carta da Requisição de Alteração (Meritocrácia) | '.$Gestor['id'], $html, $anexo);
                if($email_enviado){
                    $this->mCartas->CadastrarCartasPDFRequisicaoMeritocracia($Gestor['id'], $PDF_CARTA);
                }

                unset($Gestor);

            }
        }

        $this->EnviarCartaGestorAQ();

    }

    private function EnviarCartaGestorAQ(){

        $gestores = $this->mCartas->CronEnviaCartaGestorRequisicaoAQ();
        
        if($gestores){
            foreach($gestores as $key => $Gestor){

                $PDF_CARTA = base64_encode($this->gerar_carta(3, $Gestor['id'], 'S'));
                
                $anexo = array();                
                $anexo = array(
                    0 => array(
                        'file_base64' => $PDF_CARTA,
                        'file_name' => 'Carta AQ '.$Gestor['id'].'.pdf',
                        'file_mime' => 'application/pdf'
                    )
                );

                $html = 'Olá <b>'.$Gestor['nome'].',</b><br>
                <br>
Segue em anexo a Carta da requisição de aumento de quadro <b>Nº</b> '.$Gestor['id'].'';


                $email_enviado = enviaEmail('tiago.moselli@crmservices.com.br', 'Carta da Requisição de Aumento de Quadro | '.$Gestor['id'], $html, $anexo);
                if($email_enviado){
                    $this->mCartas->CadastrarCartasPDFRequisicaoAQ($Gestor['id'], $PDF_CARTA);
                }

                unset($Gestor);

            }
        }

        exit();

    }

    public function relatorio(){

        parent::VerificaPerfil('REMUNERACAO_CARTAS_RELATORIO');
        $this->_breadcrumb->add('GED - Cartas', 'remuneracao/cartas/relatorio');

        $mPortal = model('PortalModel');

        $dados['_titulo']          = "GED - Cartas";
        $dados['perfil_rh']        = parent::VerificaPerfil('REMUNERACAO_CARTAS_RELATORIO_RH', false);
        $dados['perfil_gestor']    = parent::VerificaPerfil('REMUNERACAO_GESTOR_RS', false);
        $dados['perfil_global_rh'] = parent::VerificaPerfil('GLOBAL_RH', false);

        $libera_todas_requisicoes = ($dados['perfil_rh'] || $dados['perfil_global_rh'] || $dados['perfil_gestor']) ? true : false;

        // requisições
        $dados['resReqAlteracao']    = $this->mCartas->ListarRequisicaoAlteracao(false, $dados['perfil_global_rh']);
        $dados['resReqMeritocracia'] = $this->mCartas->ListarRequisicaoMeritocracia(false, $dados['perfil_global_rh']);
        $dados['resCartasGlobal']    = $this->mCartas->ListarCartasGlobal();
        $dados['resSecao']           = $this->mCartas->ListarSecaoRelatorio();
        if($dados['perfil_rh'] || $dados['perfil_global_rh'] || $dados['perfil_gestor']) $dados['resReqAQ'] = $this->mCartas->ListaRequisicaoAQ(false, $libera_todas_requisicoes);

        


        return parent::ViewPortal('remuneracao/cartas/relatorio', $dados);

    }

    public function gerador(){

        //ini_set('display_errors', true);
        //error_reporting(-1);

        $acao = $this->request->getPost('relatorio');
        $requisicao_alteracao = $this->request->getPost('requisicao_alteracao');
        $requisicao_aumento_quadro = $this->request->getPost('requisicao_aumento_quadro');
        $requisicao_aumento_quadro_nome = $this->request->getPost('requisicao_aumento_quadro_nome');
        $requisicao_aumento_quadro_data = $this->request->getPost('requisicao_aumento_quadro_data');
        $requisicao_aumento_quadro_salario = $this->request->getPost('requisicao_aumento_quadro_salario');
        $requisicao_aumento_quadro_moradia = $this->request->getPost('requisicao_aumento_quadro_moradia');
        $requisicao_aumento_quadro_mudanca = $this->request->getPost('requisicao_aumento_quadro_mudanca');
        $requisicao_meritocracia = $this->request->getPost('requisicao_meritocracia');
        $carta = $this->request->getPost('cartas');
        $chapa = $this->request->getPost('chapa');
        $ano = $this->request->getPost('ano');
        $centro_custo = $this->request->getPost('centro_custo');

        switch($acao){
            case "1": // requisição de alteração
                //$this->gerar_carta(1, $requisicao_alteracao, 'I');

                $DadosReq = $this->mCartas->ListarRequisicaoAlteracao($requisicao_alteracao);

                header('Content-Description: File Transfer');
                header("Content-Type: application/pdf"); 
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                echo (base64_decode($DadosReq[0]['pdf_carta']));
                exit();
                /*$this->gerar_carta(1, $requisicao_alteracao, 'I');*/

                break;
            case "2":
                $param = array();
                $param['requisicao_aumento_quadro_data'] = $requisicao_aumento_quadro_data;
                $param['requisicao_aumento_quadro_nome'] = $requisicao_aumento_quadro_nome;
                $param['requisicao_aumento_quadro_salario'] = $requisicao_aumento_quadro_salario;
                $param['requisicao_aumento_quadro_moradia'] = (int)$requisicao_aumento_quadro_moradia;
                $param['requisicao_aumento_quadro_mudanca'] = (int)$requisicao_aumento_quadro_mudanca;
                $this->gerar_carta(3, $requisicao_aumento_quadro, 'I', $param);
                break;
            case "3":
                $DadosReq = $this->mCartas->ListarRequisicaoMeritocracia($requisicao_meritocracia);

                header('Content-Description: File Transfer');
                header("Content-Type: application/pdf"); 
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                echo (base64_decode($DadosReq[0]['pdf_carta']));
                exit();
            case "999":
                $this->gerar_carta_global($carta, $chapa, $ano, $centro_custo);
                break;
        }
        exit();

    }

    //-----------------------------------------------------------
    // Action
    //-----------------------------------------------------------
    public function action($act){

        parent::VerificaPerfil('REMUNERACAO_CARTAS');

        $dados = $_POST;
        if(!$dados) return false;

        switch($act){
            //-------------------------------------
            case 'editar_pagina':
                exit($this->mCartas->EditarCartasPagina($dados));
                break;
            
            //-------------------------------------
            case 'nova_pagina':
                exit($this->mCartas->CadastrarCartasPagina($dados));
                break;
            
            //-------------------------------------
            case 'editar_carta':
                exit($this->mCartas->EditarCartas($dados));
                break;
            
            //-------------------------------------
            case 'cadastrar_carta':
                exit($this->mCartas->CadastrarCartas($dados));
                break;
            
        }

        

    }

}