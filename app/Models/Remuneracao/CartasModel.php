<?php
namespace App\Models\Remuneracao;

use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class CartasModel extends Model {

    protected $dbportal;
    protected $dbrm;
    public $coligada;
    public $log_id;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm = db_connect('dbrm');
        $this->coligada = session()->get('func_coligada');
        $this->log_id = session()->get('log_id');
    }

    // lista cadastro de cartas
    public function ListarCartas($id = false){

        $where = ($id) ? " AND A.id = '{$id}' " : "";

        $query = "
            SELECT
                A.*
            FROM
                zcrmportal_cartas A
            WHERE
                    A.coligada = '{$this->coligada}'
                AND A.inativo IS NULL
                {$where}
                
            ORDER BY
                A.id DESC
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // lista paginas da carta
    public function ListarCartasPaginas($id_carta, $id_pagina = null){

        $where = ($id_pagina) ? " AND id = '{$id_pagina}' " : "";

        $query = " SELECT * FROM zcrmportal_cartas_pagina WHERE id_carta = '{$id_carta}' {$where} ORDER BY id DESC ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // cadastra carta
    public function CadastrarCartas($dados){

        $descricao = $dados['descricao'] ?? null;
        $secao = $dados['secao'] ?? null;
        $funcao = $dados['funcao'] ?? null;
        $filial = $dados['filial'] ?? null;
        $tipo = $dados['tipo'] ?? null;
        
        if($descricao == null){ return responseJson('error', 'Descrição não informado.'); }
        if($tipo == null){ return responseJson('error', 'Tipo não informado.'); }

        $query = "
            INSERT INTO zcrmportal_cartas
            (
                coligada,
                descricao,
                tipo,
                secao,
                funcao,
                filial,
                dtcad,
                usucad
            ) VALUES (
                '{$this->coligada}',
                '{$descricao}',
                {$tipo},
                ".(($secao != null) ? "'".serialize($secao)."'" : "NULL").",
                ".(($funcao != null) ? "'".serialize($funcao)."'" : "NULL").",
                ".(($filial != null) ? "'".serialize($filial)."'" : "NULL").",
                '".date('Y-m-d H:i:s')."',
                '{$this->log_id}'
            )
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Nova carta cadastrada com sucesso');
            return responseJson('success', 'Nova carta cadastrada com sucesso', id($this->dbportal->insertID()));
        }

        return responseJson('error', 'Falha ao cadastrar nova Carta');

    }

    // editar carta
    public function EditarCartas($dados){

        $id_carta = $dados['id_carta'] ?? null;
        $descricao = $dados['descricao'] ?? null;
        $secao = $dados['secao'] ?? null;
        $funcao = $dados['funcao'] ?? null;
        $filial = $dados['filial'] ?? null;
        $tipo = $dados['tipo'] ?? null;
        
        if($id_carta == null){ return responseJson('error', 'ID Carta não encontrado.'); }
        if($descricao == null){ return responseJson('error', 'Descrição não informado.'); }
        if($tipo == null){ return responseJson('error', 'Tipo não informado.'); }

        $query = " 
            UPDATE
                zcrmportal_cartas
            SET
                descricao = '{$descricao}',
                tipo = {$tipo},
                secao = ".(($secao != null) ? "'".serialize($secao)."'" : "NULL").",
                funcao = ".(($funcao != null) ? "'".serialize($funcao)."'" : "NULL").",
                filial = ".(($filial != null) ? "'".serialize($filial)."'" : "NULL").",
                usualt = '{$this->log_id}',
                dtalt = '".date('Y-m-d H:i:s')."'
            WHERE
                id = '{$id_carta}'
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0){
            return responseJson('success', 'Carta atualizada com sucesso');
        }

        return responseJson('error', 'Falha ao atualizar dados da Carta');

    }

    // cadastra página da carta
    public function CadastrarCartasPagina($dados){

        $id_carta = $dados['id_carta'] ?? null;
        $descricao = $dados['descricao'] ?? null;
        $texto_pagina = base64_encode($dados['texto_pagina']);

        if($id_carta == null){ return responseJson('error', 'ID Carta não encontrado.'); }
        if($descricao == null){ return responseJson('error', 'Descrição não informado.'); }

        $query = "
            INSERT INTO zcrmportal_cartas_pagina
            (descricao, texto, id_carta, usucad, dtcad)
                VALUES
            ('{$descricao}', '{$texto_pagina}', '{$id_carta}', '{$this->log_id}', '".date('Y-m-d H:i:s')."')
        ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Texto da página cadastrada com sucesso');
            return responseJson('success', 'Texto da página cadastrada com sucesso', id($this->dbportal->insertID()));
        }

        return responseJson('error', 'Falha ao cadastrar texto da página');

    }

    // editar pagina da carta
    public function EditarCartasPagina($dados){

        $id_carta = $dados['id_carta'] ?? null;
        $id_pagina = $dados['id_pagina'] ?? null;
        $descricao = $dados['descricao'] ?? 'Página';
        $texto_pagina = base64_encode($dados['texto_pagina']);

        if($id_carta == null){ return responseJson('error', 'ID Carta não encontrado.'); }
        if($id_pagina == null){ return responseJson('error', 'ID Página não encontrado.'); }

        $query = " UPDATE zcrmportal_cartas_pagina SET texto = '{$texto_pagina}', descricao = '{$descricao}', dtalt = '".date('Y-m-d H:i:s')."', usualt = '{$this->log_id}' WHERE id = '{$id_pagina}' AND id_carta = '{$id_carta}' ";
        $this->dbportal->query($query);

        if($this->dbportal->affectedRows() > 0){
            notificacao('success', 'Texto da página alterada com sucesso');
            return responseJson('success', 'Texto da página alterada com sucesso');
        }

        return responseJson('error', 'Falha ao alterar texto da página');

    }

    // gerar pagina
    public function GerarCartasPagina($tipo){

        $query = "
            SELECT
                b.id id_pagina,
                a.descricao titulo_carta,
                b.texto,
                b.descricao titulo_pagina
            FROM
                zcrmportal_cartas a
                INNER JOIN zcrmportal_cartas_pagina b ON b.id_carta = a.id
            WHERE
                    a.coligada = '".session()->get('func_coligada')."'
                AND a.inativo IS NULL
                --AND a.id = 5
                AND a.tipo = '{$tipo}'
            ORDER BY
                b.id ASC
        ";
        //echo $query;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // gerar pagina global
    public function GerarCartasPaginaGlobal($id_carta){

        $query = "
            SELECT
                b.id id_pagina,
                a.descricao titulo_carta,
                b.texto,
                b.descricao titulo_pagina
            FROM
                zcrmportal_cartas a
                INNER JOIN zcrmportal_cartas_pagina b ON b.id_carta = a.id
            WHERE
                    a.coligada = '{$this->coligada}'
                AND a.inativo IS NULL
                AND a.id = '{$id_carta}'
            ORDER BY
                b.id ASC
        ";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // grava o pdf da carta
    public function CadastrarCartasPDFRequisicao($id_requisicao, $pdf_carta){

        $query = " UPDATE zcrmportal_requisicao SET carta_enviada = 1, pdf_carta = '{$pdf_carta}' WHERE id = '{$id_requisicao}' ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) ? true: false;

    }

    // grava o pdf da carta aumento de quadro
    public function CadastrarCartasPDFRequisicaoAQ($id_requisicao, $pdf_carta){

        $query = " UPDATE zcrmportal_requisicao_aq SET carta_enviada = 1, pdf_carta = '{$pdf_carta}' WHERE id = '{$id_requisicao}' ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) ? true: false;

    }

    // grava o pdf da carta
    public function CadastrarCartasPDFRequisicaoMeritocracia($id_requisicao, $pdf_carta){

        $query = " UPDATE zcrmportal_requisicao_meritocracia SET carta_enviada = 1, pdf_carta = '{$pdf_carta}' WHERE id = '{$id_requisicao}' ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) ? true: false;

    }

    // pega chapa do funcionário da requisição
    public function PegaDadosRequisicao($tipo, $id_req){

        switch($tipo){
            case 1: $query = " SELECT chapa, posicao_nova, posicao_atual, codmotivo, coligada FROM zcrmportal_requisicao WHERE id = '{$id_req}' "; break;
            case 2: $query = " SELECT chapa, posicao_nova, posicao_atual, codmotivo, coligada FROM zcrmportal_requisicao WHERE id = '{$id_req}' "; break;
            case 3: $query = " 
                SELECT 
                    DISTINCT
                    RQ.coligada CODCOLIGADA,
                    DP.CODIGO CODSECAO,
                    DP.DESCRICAO NOMESECAO,
                    EP.CODIGO CODFUNCAO,
                    EP.NOME NOMEFUNCAO,
                    GC.NOME NOMECOLIGADA,
                    GC.NOMEFANTASIA NOMEFANTASIACOLIGADA,
                    RQ.salario SALARIO,
                    AH.DESCRICAO NOMEHORARIO,
                    RQ.codposicao posicao_nova

                FROM
                    zcrmportal_requisicao_aq RQ
                    INNER JOIN zcrmportal_posicao_salario AP ON AP.id IS NOT NULL 
                    INNER JOIN zcrmportal_posicao BP ON AP.id_posicao = BP.id 
                    INNER JOIN zcrmportal_posicao_funcao CP ON CP.id_posicao = BP.id AND CP.id = AP.id_posicao_funcao 
                    INNER JOIN ".DBRM_BANCO."..PSECAO DP ON DP.CODCOLIGADA = BP.coligada AND DP.CODIGO COLLATE Latin1_General_CI_AS = BP.codsecao
                    INNER JOIN ".DBRM_BANCO."..PFUNCAO EP ON EP.CODCOLIGADA = BP.coligada AND EP.CODIGO COLLATE Latin1_General_CI_AS = CP.codfuncao
                    INNER JOIN ".DBRM_BANCO."..GFILIAL FP ON FP.CODCOLIGADA = BP.coligada
                    INNER JOIN ".DBRM_BANCO."..GCOLIGADA GC ON GC.CODCOLIGADA = RQ.coligada
                    INNER JOIN ".DBRM_BANCO."..AHORARIO AH ON AH.CODIGO = RQ.codhorario COLLATE Latin1_General_CI_AS AND AH.CODCOLIGADA = RQ.coligada

                WHERE
                        RQ.id = '{$id_req}'
                    AND FP.CODFILIAL = CAST(SUBSTRING(BP.codsecao,1,3) AS INT)
                    AND CONCAT(BP.id, '.', AP.id) = RQ.codposicao
                    AND AP.id_posicao = BP.id
            "; break;
            case 4: $query = " SELECT chapa, posicao_nova, posicao_atual, codmotivo, coligada, secao_motivo, id_meritocracia FROM zcrmportal_requisicao_meritocracia WHERE id = '{$id_req}' "; break;
                break;
        }

        $result = $this->dbportal->query($query);

        if($result->getNumRows() > 0){

            $DADOS = $result->getResultArray();
            if($tipo == 3){
                return $DADOS[0];
            }


            $chapa = $DADOS[0]['chapa'];
            
            $_SESSION['func_coligada'] = $DADOS[0]['coligada'];

            $mPortal = model('PortalModel');

            $dadosFunc = $mPortal->ListarDadosFuncionario(false, $chapa);
            
            
            if($dadosFunc){
                $dadosFunc[0]['requisicao'] = $result->getResultArray()[0];
            }

            if($dadosFunc) return $dadosFunc[0];

        }

        return false;

    }

    // executa sql do texto da carta
    public function ExecutaSQL($sql, $banco){
        
        $query = str_replace('<br />', ' ', $sql);
        $query = strip_tags($query, ' ');

        switch($banco){
            case 'RM': $result = $this->dbrm->query($query); break;
            case 'PT': $result = $this->dbportal->query($query); break;
            default: return false;
        }

        if($result->getNumRows() > 0){


            $dados = $result->getFirstRow('array');
            if(count($dados) == 1){
                
                $campo = array_key_first($dados);

                return $dados[$campo];

            }else{
                
                $table = "";
                $table .= "<table align=\"center\" width=\"100%\">";
                $table .= "<tr>";

                // titulo da tabela
                $campo = array_keys($dados);
                foreach($campo as $NomeCampo){
                    $table .= "<th style=\"background: #159d99; color: #ffffff;\" align=\"center\" valign=\"middle\"><b>{$NomeCampo}</b></th>";
                }
                $table .= "</tr>";

                // valores da tabela
                $valores = $result->getResultArray();
                foreach($valores as $key2 => $Dados){
                    $table .= "<tr>";
                    foreach($Dados as $Valores){
                        $table .= "<td align=\"center\" valign=\"middle\">{$Valores}</td>";
                    }
                    $table .= "</tr>";
                }

                $table .= "</table>";

                return $table;
                
            }

        }

        return false;

    }

    // envia carta para o gestor
    public function CronEnviaCartaGestorRequisicaoPromocao(){

        $query = "
            SELECT 
                a.id, 
                a.coligada, 
                a.chapa,
                c.NOME,
                c.EMAIL,
                a.usufim_apr,
                d.NOME nome_funcionario
                
            FROM 
                zcrmportal_requisicao a
                    INNER JOIN zcrmportal_hierarquia_chapa b ON b.id_hierarquia = a.id_hierarquia_aprovador
                    INNER JOIN EMAIL_CHAPA c ON c.CHAPA = b.chapa COLLATE Latin1_General_CI_AS AND c.CODCOLIGADA = b.coligada
                    LEFT JOIN ".DBRM_BANCO."..PFUNC d ON d.CHAPA = a.chapa COLLATE Latin1_General_CI_AS AND d.CODCOLIGADA = a.coligada COLLATE Latin1_General_CI_AS
                
            WHERE 
                    a.situacao IN (2) 
                AND a.carta_enviada IS NULL
                --AND a.id > 7581
                --AND a.id IN (7524, 7523)
                AND a.id >= 8643
                AND a.codmotivo IN (SELECT id FROM zcrmportal_requisicao_motivo WHERE descricao IN ('Promoção','Mérito','Enquadramento','Alteração Cargo') AND coligada = a.coligada)
            ORDER BY a.id DESC
        ";

        //echo '<pre>'.$query;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
                

    }

    // envia carta para o gestor
    public function CronEnviaCartaGestorRequisicaoMeritocracia(){

        $query = "
            SELECT 
                a.id, 
                a.coligada, 
                a.chapa,
                c.NOME,
                c.EMAIL,
                a.usufim_apr,
                d.NOME nome_funcionario
                
            FROM 
                zcrmportal_requisicao_meritocracia a
                    LEFT JOIN GESTOR_CHAPA b ON b.CHAPA = a.chapa COLLATE Latin1_General_CI_AS AND b.CODCOLIGADA = a.coligada
                    LEFT JOIN EMAIL_CHAPA c ON c.CHAPA COLLATE Latin1_General_CI_AS = b.GESTOR_CHAPA AND c.CODCOLIGADA = b.CODCOLIGADA
                    LEFT JOIN ".DBRM_BANCO."..PFUNC d ON d.CHAPA = a.chapa COLLATE Latin1_General_CI_AS AND d.CODCOLIGADA = a.coligada COLLATE Latin1_General_CI_AS
                
            WHERE 
                    a.situacao IN (2) 
                AND a.carta_enviada IS NULL
                AND a.secao_motivo IN ('07','02','03')
                AND a.id > 22
            ORDER BY a.id DESC
        ";

        //echo '<pre>'.$query;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
                

    }

    // envia carta para o gestor aumento de quadro
    public function CronEnviaCartaGestorRequisicaoAQ(){

        $query = "
            SELECT 
                a.id,
                a.coligada,
                b.email,
                b.nome
                
            FROM
                zcrmportal_requisicao_aq a
                INNER JOIN zcrmportal_usuario b ON b.id = a.usucad
                
            WHERE
                a.codposicao IS NOT NULL
                AND a.tipo = 'A'
                AND a.carta_enviada IS NULL
                AND a.id > 9365
                
            ORDER BY
                a.id DESC
        ";

        //echo '<pre>'.$query;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;
                

    }

    public function ListarRequisicaoAlteracao($id_requisicao = false, $perfil_global = false){

        $select_arquivo = ($id_requisicao) ? " , a.pdf_carta " : "";
		$where_arquivo = ($id_requisicao) ? " AND a.id ='{$id_requisicao}' " : "";

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        $isLider = $mHierarquia->isLider(util_chapa(session()->get('func_chapa'))['CHAPA'], session()->get('func_coligada'));
        $isBP = $mHierarquia->getSecaoPodeVerBP();

        $in_secao = " AND 1 = 1 ";
        if($Secoes && !$perfil_global){
            $in_secao = "";
            if($isLider && !$isBP){
                // lider
                foreach($Secoes as $key =>$Chapa){
                    $in_secao .= "'{$Chapa['chapa']}',";
                }
                $in_secao = " AND a.chapa IN (".rtrim($in_secao, ',').") ";
            }else{
                // gestor
                foreach($Secoes as $key =>$CodSecao){
                    $in_secao .= "'{$CodSecao['codsecao']}',";
                }
                $in_secao = " AND a.secao_atual IN (".rtrim($in_secao, ',').") ";
            }
        }
		
		$query = "
			SELECT 
				a.id,
				b.NOME nome,
				b.CHAPA chapa
				{$select_arquivo}
			FROM 
				zcrmportal_requisicao a
				INNER JOIN ".DBRM_BANCO."..PFUNC b ON b.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND b.CODCOLIGADA = a.coligada
			WHERE 
					a.coligada = '{$this->coligada}'
                AND a.carta_enviada = 1 
				{$where_arquivo}
                {$in_secao}
			ORDER BY 
				b.NOME ASC,
                a.id ASC
		";
		$result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ListarRequisicaoMeritocracia($id_requisicao = false, $perfil_global = false){

        $select_arquivo = ($id_requisicao) ? " , a.pdf_carta " : "";
		$where_arquivo = ($id_requisicao) ? " AND a.id ='{$id_requisicao}' " : "";

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        $isLider = $mHierarquia->isLider(util_chapa(session()->get('func_chapa'))['CHAPA'], session()->get('func_coligada'));
        $isBP = $mHierarquia->getSecaoPodeVerBP();

        $in_secao = " AND 1 = 1 ";
        if($Secoes && !$perfil_global){
            $in_secao = "";
            if($isLider && !$isBP){
                // lider
                foreach($Secoes as $key =>$Chapa){
                    $in_secao .= "'{$Chapa['chapa']}',";
                }
                $in_secao = " AND a.chapa IN (".rtrim($in_secao, ',').") ";
            }else{
                // gestor
                foreach($Secoes as $key =>$CodSecao){
                    $in_secao .= "'{$CodSecao['codsecao']}',";
                }
                $in_secao = " AND a.secao_atual IN (".rtrim($in_secao, ',').") ";
            }
        }
		
		$query = "
			SELECT 
				a.id,
				b.NOME nome,
				b.CHAPA chapa
				{$select_arquivo}
			FROM 
				zcrmportal_requisicao_meritocracia a
				INNER JOIN ".DBRM_BANCO."..PFUNC b ON b.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND b.CODCOLIGADA = a.coligada
			WHERE 
					a.coligada = '{$this->coligada}'
                AND a.carta_enviada = 1 
				{$where_arquivo}
                {$in_secao}
			ORDER BY 
				b.NOME ASC,
                a.id ASC
		";
		$result = $this->dbportal->query($query);
        if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ListaRequisicaoAQ($id_requisicao = false, $perfil_global = false){

        $select_arquivo = ($id_requisicao) ? " , a.pdf_carta " : "";
		$where_arquivo = ($id_requisicao) ? " AND a.id ='{$id_requisicao}' " : "";
		
		$select_arquivo = ($id_requisicao) ? " , R.pdf_carta " : "";
		$where_arquivo = ($id_requisicao) ? " AND R.id ='{$id_requisicao}' " : "";

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        $isLider = $mHierarquia->isLider(util_chapa(session()->get('func_chapa'))['CHAPA'], session()->get('func_coligada'));
        $isBP = $mHierarquia->getSecaoPodeVerBP();

        $in_secao = " AND 1 = 2 ";
        if($Secoes && !$perfil_global){
            $in_secao = "";
            if($isLider && !$isBP){
                // lider
                foreach($Secoes as $key =>$Chapa){
                    $in_secao .= "'{$Chapa['chapa']}',";
                }
                $in_secao = " AND AP.chapa IN (".rtrim($in_secao, ',').") ";
            }else{
                // gestor
                foreach($Secoes as $key =>$CodSecao){
                    $in_secao .= "'{$CodSecao['codsecao']}',";
                }
                $in_secao = " AND BP.codsecao IN (".rtrim($in_secao, ',').") ";
            }
        }

        if($perfil_global){
            $in_secao = "";
        }
		
		$query = "
			SELECT 
                R.id id,
                CONCAT(BP.id, '.', AP.id) codposicao,
                AP.salario,
                AP.range_salario,
                AP.salario_min,
                AP.salario_max,
                AP.excecao,
                BP.codsecao,
                CAST(SUBSTRING(BP.codsecao,1,3) AS INT) codfilial,
                CP.codfuncao,
                DP.DESCRICAO nomesecao,
                EP.NOME nomefuncao,
                AP.nao_orcada,
                AP.chapa,
                R.codposicao
				{$select_arquivo}
            FROM
                zcrmportal_posicao_salario AP
                INNER JOIN zcrmportal_posicao BP ON AP.id_posicao = BP.id 
                INNER JOIN zcrmportal_posicao_funcao CP ON CP.id_posicao = BP.id AND CP.id = AP.id_posicao_funcao 
                INNER JOIN ".DBRM_BANCO."..PSECAO DP ON DP.CODCOLIGADA = BP.coligada AND DP.CODIGO COLLATE Latin1_General_CI_AS = BP.codsecao
                INNER JOIN ".DBRM_BANCO."..PFUNCAO EP ON EP.CODCOLIGADA = BP.coligada AND EP.CODIGO COLLATE Latin1_General_CI_AS = CP.codfuncao
                INNER JOIN 
                    (
                        SELECT
                            R1.id,
                            CASE
                                WHEN R1.codposicao IS NOT NULL THEN R1.codposicao
                                ELSE (SELECT MAX(codposicao) FROM zcrmportal_requisicao_aq_salario WHERE id_req_aq = R1.id)
                            END codposicao
                        FROM
                            zcrmportal_requisicao_aq R1
                    ) R ON R.codposicao = CONCAT(BP.id, '.', AP.id)
            WHERE
				AP.coligada = '{$_SESSION['func_coligada']}'
				--AND R.carta_enviada = 1
				{$where_arquivo}
                {$in_secao}
		";
        // echo '<pre>';
        // echo $query;
        // exit();
		$result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ListarCartasGlobal(){

        $query = "SELECT id, descricao FROM zcrmportal_cartas WHERE tipo = 999 ORDER BY descricao ASC";
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ListarSecaoRelatorio(){

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
        $isLider = $mHierarquia->isLider(util_chapa(session()->get('func_chapa'))['CHAPA'], session()->get('func_coligada'));
        $isBP = $mHierarquia->getSecaoPodeVerBP();

        $in_secao = " AND 1 = 1 ";
        if($Secoes){
            $in_secao = "";
            if($isLider && !$isBP){
                // lider
                foreach($Secoes as $key =>$Chapa){
                    $in_secao .= "'{$Chapa['chapa']}',";
                }
                $in_secao = " AND A.CHAPA IN (".rtrim($in_secao, ',').") ";
            }else{
                // gestor
                foreach($Secoes as $key =>$CodSecao){
                    $in_secao .= "'{$CodSecao['codsecao']}',";
                }
                $in_secao = " AND A.CODSECAO IN (".rtrim($in_secao, ',').") ";
            }
        }

        $query = "
            SELECT
                DISTINCT
                A.CODSECAO,
                B.DESCRICAO
            FROM
                PFUNC A
                INNER JOIN PSECAO B ON B.CODIGO = A.CODSECAO AND B.CODCOLIGADA = A.CODCOLIGADA
            WHERE
                A.CODSITUACAO NOT IN ('D')
                {$in_secao}
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    public function ListarFuncionariosCartaGlobal($chapa = false, $ano = false, $secao = false){

        $where = "";
        if($chapa) $where .= " AND A.CHAPA = '{$chapa}' ";
        //if($ano) $where .= " AND A.CHAPA = '{$chapa}' ";
        if($secao) $where .= " AND A.CODSECAO = '{$secao}' ";

        $query = "
            SELECT
                DISTINCT
                A.CHAPA,
                A.NOME,
                A.CODSECAO,
                A.CODFUNCAO
            FROM
                PFUNC A
                INNER JOIN PSECAO B ON B.CODIGO = A.CODSECAO AND B.CODCOLIGADA = A.CODCOLIGADA
            WHERE
                A.CODSITUACAO NOT IN ('D')
                {$where}
        ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
    

}
?>