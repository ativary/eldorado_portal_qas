<?php
namespace App\Models\Ponto;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class OcorrenciaModel extends Model {

    protected $dbportal;
    protected $dbrm;
    public $coligada;
    public $log_id;
    public $now;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm = db_connect('dbrm');
        $this->coligada = session()->get('func_coligada');
        $this->log_id = session()->get('log_id');
		$this->now = date('Y-m-d H:i:s');
		ini_set("pcre.backtrack_limit", "50000000");
		set_time_limit(60*90);
		ini_set('max_execution_time', 60*90);
    }


    //--------------------------------------------------------------------------------------------------------------------------------------------
    //----------------------------------------------------------------- TROCA DE ESCALA -------------------------------------------------------------------
    //--------------------------------------------------------------------------------------------------------------------------------------------

    // -------------------------------------------------------
    // Lista configuração de ponto
    // -------------------------------------------------------
    public function ListarPeriodoPonto(){

		try{
        
			// configuração de periodo do ponto
			$query = " SELECT * FROM zcrmportal_espelho_config WHERE coligada = '{$this->coligada}' ";
			$result = $this->dbportal->query($query);
			if(($result->getNumRows() <= 0)) return false;
			$ConfigPonto = $result->getResultArray()[0];

			$periodo_ini = date('Y-m-d', strtotime($ConfigPonto['dtinicio']));
			$periodo_fim = date('Y-m-d', strtotime($ConfigPonto['dtfim']));

			$query = "
			SELECT 
					INICIOMENSAL, 
					FIMMENSAL 
				FROM 
					APERIODO 
				WHERE 
					CODCOLIGADA = {$this->coligada} 
					AND INICIOMENSAL >= '{$periodo_ini}'
					AND FIMMENSAL <= '{$periodo_fim}'
				ORDER BY INICIOMENSAL DESC
			";

			$result = $this->dbrm->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }

	public function ListaGestores5(){

		try{
        
			// configuração de periodo do ponto
			$query = "  SELECT
					e.email,
					h.CHAPA,
					h.CODCOLIGADA AS COLIGADA,
					h.nome
					
					FROM
							zcrmportal_funcoes a
							JOIN zcrmportal_perfilfuncao b ON b.id_funcao = a.id
							JOIN zcrmportal_perfil c ON c.id = b.id_perfil
							JOIN zcrmportal_usuarioperfil d ON d.id_perfil = c.id
							JOIN zcrmportal_usuario e ON d.id_usuario = e.id
							INNER JOIN ".DBRM_BANCO."..PPESSOA f ON e.login = f.cpf COLLATE Latin1_General_CI_AS
							INNER JOIN ".DBRM_BANCO."..PFUNC h ON f.codigo = h.codpessoa 
							
					WHERE
						a.nome = 'PONTO_OCORRENCIA_WOKRFLOW1'

						AND h.CODSITUACAO <>'D'

					UNION ALL
				
					SELECT
						I.email,
						I.SUBSTITUTO_CHAPA CHAPA,
						I.CODCOLIGADA AS COLIGADA,
						I.SUBSTITUTO_NOME COLLATE Latin1_General_CI_AS nome 
					
						FROM
								zcrmportal_funcoes a
								JOIN zcrmportal_perfilfuncao b ON b.id_funcao = a.id
								JOIN zcrmportal_perfil c ON c.id = b.id_perfil
								JOIN zcrmportal_usuarioperfil d ON d.id_perfil = c.id
								JOIN zcrmportal_usuario e ON d.id_usuario = e.id
								INNER JOIN ".DBRM_BANCO."..PPESSOA f ON e.login = f.cpf COLLATE Latin1_General_CI_AS
								INNER JOIN ".DBRM_BANCO."..PFUNC h ON f.codigo = h.codpessoa 
								INNER JOIN GESTOR_SUBSTITUTO_CHAPA I ON I.GESTOR_CHAPA = H.CHAPA COLLATE Latin1_General_CI_AS
								
						WHERE
							a.nome = 'PONTO_OCORRENCIA_WOKRFLOW1'
							AND h.CODSITUACAO <>'D'
							AND I.FUNCOES LIKE '%\"150\"%'


					";
					
				$result = $this->dbportal->query($query);
				return ($result->getNumRows() > 0) 
						? $result->getResultArray() 
						: false;

		} catch (\Exception | \Error $e) {
			return false;
		}
    }

	public function ListaGestores4(){

		try {
        
			// configuração de periodo do ponto
			$query = "  
			SELECT
				e.email,
				h.CHAPA,
				h.CODCOLIGADA AS COLIGADA,
				h.nome
				FROM
						zcrmportal_funcoes a
						JOIN zcrmportal_perfilfuncao b ON b.id_funcao = a.id
						JOIN zcrmportal_perfil c ON c.id = b.id_perfil
						JOIN zcrmportal_usuarioperfil d ON d.id_perfil = c.id
						JOIN zcrmportal_usuario e ON d.id_usuario = e.id
						INNER JOIN ".DBRM_BANCO."..PPESSOA f ON e.login = f.cpf COLLATE Latin1_General_CI_AS
						INNER JOIN ".DBRM_BANCO."..PFUNC h ON f.codigo = h.codpessoa 
						
				WHERE
					a.nome = 'PONTO_OCORRENCIA_WOKRFLOW2'
					AND h.CODSITUACAO <>'D'
			
			UNION ALL
			
			SELECT
				I.email,
				I.SUBSTITUTO_CHAPA CHAPA,
				I.CODCOLIGADA AS COLIGADA,
				I.SUBSTITUTO_NOME COLLATE Latin1_General_CI_AS nome 
			
				FROM
						zcrmportal_funcoes a
						JOIN zcrmportal_perfilfuncao b ON b.id_funcao = a.id
						JOIN zcrmportal_perfil c ON c.id = b.id_perfil
						JOIN zcrmportal_usuarioperfil d ON d.id_perfil = c.id
						JOIN zcrmportal_usuario e ON d.id_usuario = e.id
						INNER JOIN ".DBRM_BANCO."..PPESSOA f ON e.login = f.cpf COLLATE Latin1_General_CI_AS
						INNER JOIN ".DBRM_BANCO."..PFUNC h ON f.codigo = h.codpessoa 
						INNER JOIN GESTOR_SUBSTITUTO_CHAPA I ON I.GESTOR_CHAPA = H.CHAPA COLLATE Latin1_General_CI_AS
						
				WHERE
					a.nome = 'PONTO_OCORRENCIA_WOKRFLOW2'
					AND h.CODSITUACAO <>'D'
					AND I.FUNCOES LIKE '%\"149\"%'


				";
			$result = $this->dbportal->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }
	
	public function ListarPeriodoPontoworflow(){

		try{
        
        	// configuração de periodo do ponto para o workflow
			$query = "
			
				SELECT 
				* 
				FROM 
					APERIODO 
				WHERE 
					CODCOLIGADA = 1
					--AND GETDATE() BETWEEN iniciomensal AND dbo.ZFimdoMes(mescoMP,aNocomP)
					AND '2022-09-18' BETWEEN iniciomensal AND FIMMENSAL --dbo.ZFimdoMes(mescoMP,aNocomP)
				ORDER BY INICIOMENSAL DESC


			";
			
			$result = $this->dbrm->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }


    // -------------------------------------------------------
    // Lista as filiais que pode ver
    // -------------------------------------------------------
    public function ListarOcorrenciaFilial(){

		try {

			//-----------------------------------------
			// filtro das chapas que o lider pode ver
			//-----------------------------------------
			$mHierarquia = Model('HierarquiaModel');
			$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
			$isLider = $mHierarquia->isLider();

			$filtro_chapa_lider = "";
			$filtro_secao_lider = "";
			if($isLider){
				$chapas_lider = "";
				$codsecoes = "";
				// foreach($objFuncLider as $idx => $value){
				// 	$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
				// }
				// $filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";

				//------------------------------------------------
				$inGrupo 	= [];
				$idGrupo 	= 1;
				$linha 		= 1;
				$filtro_secao_lider = "";
				foreach($objFuncLider as $ChapaLider){
					$grupo[$idGrupo][] = $ChapaLider['chapa'];
					if($linha == 800){
						$idGrupo++;
						$linha=0;
					}
					$linha++;
				}
				
				foreach($grupo as $in){
					$filtro_secao_lider .= " A.CHAPA IN ('".implode("','", $in)."') OR ";
				}

				$filtro_secao_lider = "(".rtrim($filtro_secao_lider, " OR ").") OR ";
				//------------------------------------------------
			}
			
			//-----------------------------------------
			// filtro das seções que o gestor pode ver
			//-----------------------------------------
			$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
			$filtro_secao_gestor = "";
			
			if($secoes){
				$codsecoes = "";
				foreach($secoes as $ids => $Secao){
					$codsecoes .= "'".$Secao['codsecao']."',";									   
				}
				$filtro_secao_gestor = " A.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
			}
			//-----------------------------------------
			
			// monta o where das seções
			if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
			if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
			if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
			$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

			$mAcesso = model('AcessoModel');
			$perfil_rh = $mAcesso->VerificaPerfil('PONTO_OCORRENCIA_RH');
			if($mAcesso->VerificaPerfil('GLOBAL_RH')) $perfil_rh = true;
			if($perfil_rh) $qr_secao = "";

			$query = "
				SELECT
					B.CODFILIAL,
					B.NOME NOMEFILIAL
					
				FROM
					PFUNC A
						INNER JOIN GFILIAL B ON B.CODFILIAL = A.CODFILIAL AND B.CODCOLIGADA = A.CODCOLIGADA
				WHERE
						A.CODSITUACAO <> 'D'
					AND A.CODCOLIGADA = {$this->coligada}
					{$qr_secao}
				GROUP BY
					B.CODFILIAL,
					B.NOME
			";
			//echo '<textarea>'.$query.'</textarea>';exit;
			$result = $this->dbrm->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }

	// cadastra configuração de tipo de ocorrencia
	public function CadastrarConfiguracaoTipoOcorrencia($dados){

		try {
        
			$query = "
				UPDATE 
					zcrmportal_ocorrencia_tipo
				SET 
					excesso_gestor =  {$dados['excesso_abono_gestor']},
					excesso_jornada = {$dados['jornada']},
					sobreaviso = {$dados['sobreaviso']},
					troca_menor10 = {$dados['troca_menor_10_dias']},
					extra_acima = {$dados['extra_permitido']},
					registro_bri = {$dados['registro_britanico']},
					trabalho_dsr = {$dados['trabalho_dsr_folga']},
					trabalho_dsr_descanso = {$dados['trabalho_dsr_folga_descanso']},
					troca_menor6 ={$dados['troca_menor_6_meses']},
					extra_especial ={$dados['extra']},
					registro_manual = {$dados['registro_manual']},
					trabalho_AfastFerias = {$dados['trabalho_ferias_afastamento']},
					interjornada = {$dados['interjornada']},
					req_troca = {$dados['pendente_termo_aditivo']},
					trabalho_sup6 = {$dados['trabalho_6dias']}
				WHERE 
					coligada = '{$this->coligada}'
			";
			
			$this->dbportal->query($query);

			return ($this->dbportal->affectedRows() > 0) 
					? responseJson('success', 'Configuração realizada com sucesso.')
					: responseJson('error', 'Falha ao cadastrar configuração');

		} catch (\Exception | \Error $e) {
			return false;
		}

    }

	
	public function ListarOcorrenciaHorarios(){

		try {

			$query = "
				SELECT
				A.CODIGO,
				A.DESCRICAO,
				ISNULL(extra_limite_semanal,0) extra_limite_semanal,
				ISNULL(extra_limite_diario,0) extra_limite_diario,
				ISNULL(extra_feriado,0) extra_feriado,
				ISNULL(extra_feriado_parcial,0) extra_feriado_parcial,
				ISNULL(escala_especial,0) escala_especial,
				tipo_horario,
				ISNULL(intervalo_obrigatorio,0) intervalo_obrigatorio,
				ISNULL(intervalo_fracionado,0) intervalo_fracionado,
				ISNULL(planejado_inicio,0) planejado_inicio,
				ISNULL(planejado_termino,0) planejado_termino
					
				FROM
					AHORARIO A
						LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario B ON B.coligada = A.CODCOLIGADA AND B.codigo = A.CODIGO COLLATE Latin1_General_CI_AS
					
				WHERE
						A.CODCOLIGADA = {$this->coligada}
					AND A.INATIVO = 0
				
				ORDER BY A.CODIGO
				
			";

			$result = $this->dbrm->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }
	
	
	public function IUDHorario($dados){

		try {
		
			$horario = strlen(trim($dados['horario'])) > 0 ? "'{$dados['horario']}'" : "NULL";

			$query = "
			INSERT INTO zcrmportal_ocorrencia_horario (codigo, coligada) VALUES ({$horario}, '{$this->coligada}')
			";
			
			$this->dbportal->query($query);
			
			if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Perfil cadastrado com sucesso.')));

			return ($this->dbportal->affectedRows() > 0) 
					? responseJson('success', 'Horario cadastrado com sucesso', $this->dbportal->insertID())
					: responseJson('error', 'Falha ao cadastrar perfil');
					
		} catch (\Exception | \Error $e) {
			return false;
		}

    }
	
	public function DELHorario($dados){
		
		
        $query = "
           DELETE FROM zcrmportal_ocorrencia_horario where id = '{$dados['id']}'
      	";
		
		$this->dbportal->query($query);
		
		if($this->dbportal->affectedRows() > 0) session()->set(array('notificacao' => array('success', '<i class="mdi mdi-check-all alert-icon"></i> Horário removido com sucesso.')));

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Hora excluido com sucesso')
                : responseJson('error', 'Falha ao excluir Horarios');

    }
	
	public function ListarOcorrenciaHorariosPortal(){

		try {

			$query = "
			SELECT A.*, B.DESCRICAO FROM  zcrmportal_ocorrencia_horario AS A, ".DBRM_BANCO."..AHORARIO AS B WHERE coligada = {$this->coligada} AND a.codigo = B.CODIGO COLLATE Latin1_General_CI_AS
			";
			
			$result = $this->dbportal->query($query);
		
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}
    }
	public function ListarOcorrenciaTipoPortal(){

		try {
		
			$query = "
			SELECT * FROM  zcrmportal_ocorrencia_tipo WHERE coligada = '1'
			";
			
			$result = $this->dbportal->query($query);
		
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }
    // -------------------------------------------------------
    // Lista as seções que pode ver
    // -------------------------------------------------------
    public function ListarOcorrenciaSecao(){

		try {

			//-----------------------------------------
			// filtro das chapas que o lider pode ver
			//-----------------------------------------
			$mHierarquia = Model('HierarquiaModel');
			$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
			$isLider = $mHierarquia->isLider();

			$filtro_chapa_lider = "";
			$filtro_secao_lider = "";
			if($isLider){
				$chapas_lider = "";
				$codsecoes = "";
				// foreach($objFuncLider as $idx => $value){
				// 	$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
				// }
				// $filtro_secao_lider = " A.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";

				//------------------------------------------------
				$inGrupo 	= [];
				$idGrupo 	= 1;
				$linha 		= 1;
				$filtro_secao_lider = "";
				foreach($objFuncLider as $ChapaLider){
					$grupo[$idGrupo][] = $ChapaLider['chapa'];
					if($linha == 800){
						$idGrupo++;
						$linha=0;
					}
					$linha++;
				}
				
				foreach($grupo as $in){
					$filtro_secao_lider .= " A.CHAPA IN ('".implode("','", $in)."') OR ";
				}

				$filtro_secao_lider = "(".rtrim($filtro_secao_lider, " OR ").") OR ";
				//------------------------------------------------
			}
			
			//-----------------------------------------
			// filtro das seções que o gestor pode ver
			//-----------------------------------------
			$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
			$filtro_secao_gestor = "";
			
			if($secoes){
				$codsecoes = "";
				foreach($secoes as $ids => $Secao){
					$codsecoes .= "'".$Secao['codsecao']."',";									   
				}
				$filtro_secao_gestor = " A.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
			}
			//-----------------------------------------
			
			// monta o where das seções
			if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
			if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
			if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
			$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

			$mAcesso = model('AcessoModel');
			$perfil_rh = $mAcesso->VerificaPerfil('PONTO_OCORRENCIA_RH');
			if($mAcesso->VerificaPerfil('GLOBAL_RH')) $perfil_rh = true;
			if($perfil_rh) $qr_secao = "";

			$query = "
				SELECT
					A.CODSECAO,
					B.DESCRICAO NOMESECAO
					
				FROM
					PFUNC A
						INNER JOIN PSECAO B ON B.CODIGO = A.CODSECAO AND B.CODCOLIGADA = A.CODCOLIGADA AND B.SECAODESATIVADA = 0
				WHERE
						A.CODSITUACAO <> 'D'
					AND A.CODCOLIGADA = {$this->coligada}
					{$qr_secao}
				GROUP BY
					A.CODSECAO,
					B.DESCRICAO
			";

			$result = $this->dbrm->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }

    // -------------------------------------------------------
    // Lista ocorrências
    // -------------------------------------------------------
    public function ListarOcorrencia($dados){

		try {
		
        $data_inicio = $dados['data_inicio'] ?? null;
        $data_fim = $dados['data_fim'] ?? null;
        $filial = $dados['filial'];
        $ja_justificados = $dados['ja_justificados'] ?? null;
        $abono = '029';
		$data_inicio_ciclo = somarDias($data_inicio, '-7');

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

        $filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		$withLider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= " SELECT '".$objFuncLider[$idx]['chapa']."' CHAPA UNION ALL ";
			}

			$withLider .= " CHAPALIDER AS (".substr($chapas_lider,0, -10)."),  ";

			$filtro_secao_lider = " EXISTS (SELECT CHAPA FROM CHAPALIDER WHERE CHAPA = A.CHAPA) OR ";

			//------------------------------------------------
			// $inGrupo 	= [];
			// $idGrupo 	= 1;
			// $linha 		= 1;
			// $filtro_secao_lider = "";
			// foreach($objFuncLider as $ChapaLider){
			// 	$grupo[$idGrupo][] = $ChapaLider['chapa'];
			// 	if($linha == 800){
			// 		$idGrupo++;
			// 		$linha=0;
			// 	}
			// 	$linha++;
			// }
			
			// foreach($grupo as $in){
			// 	$filtro_secao_lider .= " A.CHAPA IN ('".implode("','", $in)."') OR ";
			// }

			// $filtro_secao_lider = "(".rtrim($filtro_secao_lider, " OR ").") OR ";
			//------------------------------------------------
			
		}
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
        
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				// $codsecoes .= "'".$Secao['codsecao']."',";	
				$codsecoes .= " SELECT '".$Secao['codsecao']."' CODSECAO UNION ALL ";								   
			}

			$withLider .= " SECAOGESTOR AS (".substr($codsecoes,0, -10)."),  ";

			// $filtro_secao_gestor = " A.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
			$filtro_secao_gestor = " EXISTS (SELECT CODSECAO FROM SECAOGESTOR WHERE CODSECAO = A.CODSECAO) OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

        $mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('PONTO_OCORRENCIA_RH');
		if($mAcesso->VerificaPerfil('GLOBAL_RH')) $perfil_rh = true;
		
        if($perfil_rh) $qr_secao = "";

        $where_filial = (strlen(trim($filial)) > 0) ? " AND A.CODFILIAL = '{$filial}' " : "";

		// $where_filial = " AND A.CHAPA = '050004377' ";

        // tipos de ocorrencias
        $where_extra_permitido = (($dados['extra_permitido'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_extra = (($dados['extra'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_jornada = (($dados['jornada'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_trabalho_dsr_folga = (($dados['trabalho_dsr_folga'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_trabalho_ferias_afastamento = (($dados['trabalho_ferias_afastamento'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_registro_manual = (($dados['registro_manual'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_trabalho_6dias = (($dados['trabalho_6dias'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_excesso_abono_gestor = (($dados['excesso_abono_gestor'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_interjornada = (($dados['interjornada'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_registro_britanico = (($dados['registro_britanico'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_ja_justificados = ($ja_justificados == NULL) ? " WHERE (SELECT Z.codmotivo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) IS NULL " : "";
		// $where_ja_justificados = ($ja_justificados == NULL) ? " WHERE Z.codmotivo IS NULL " : "";
        $where_sobreaviso = (($dados['sobreaviso'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_troca_menor_6_meses = (($dados['troca_menor_6_meses'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_troca_menor_10_dias = (($dados['troca_menor_10_dias'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_pendente_termo_aditivo = (($dados['pendente_termo_aditivo'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        
        // filtro por seção
        if(is_array($dados['secao'] ?? "")){
            $qr_secao = "";
            foreach($dados['secao'] as $key => $CodSecao){
                $qr_secao .= "'{$CodSecao}',";
            }
            $qr_secao = " AND A.CODSECAO IN (".rtrim($qr_secao, ',').") ";
        }


		/*
		$dados['periodo'] = "2023-03-012023-04-05";
		$data_inicio = "2023-03-01";
		$data_fim = "2023-04-05";
		*/

		// verifica se o periodo esta congelado
		$periodo_fim = (int)date('Ym', strtotime(mb_substr($dados['periodo'], -10)));
		$hoje = (int)date('Ym');

		if($periodo_fim < $hoje && 1==2){
			// congelado
			$query = "
				SELECT 
					a.codcoligada CODCOLIGADA,
					a.chapa CHAPA,
					b.NOME,
					b.CODFUNCAO,
					c.NOME NOMEFUNCAO,
					b.CODSECAO,
					d.DESCRICAO NOMESECAO,
					a.data DATA,
					a.ocorrencia OCORRENCIA,
					a.resultado VALOR,
					a.complemento COMPLEMENTO,
					a.codmotivo,
					a.descricao_motivo,
					a.ocorrencia log_ocorrencia,
					a.observacao,
					a.gestor,
					a.sistema SISTEMA,

					(
					  SELECT max(CAST(BB.descricao AS VARCHAR)) FROM zcrmportal_ponto_justificativa_func AA 
						INNER JOIN zcrmportal_ponto_motivos BB ON AA.justificativa = BB.id AND AA.coligada = BB.codcoligada WHERE AA.coligada = a.codcoligada AND AA.dtponto = a.data AND AA.chapa = a.chapa Collate Database_Default
					) justificativa_extra

				FROM 
					zcrmportal_ocorrencia a (NOLOCK)
					INNER JOIN ".DBRM_BANCO."..PFUNC b (NOLOCK) ON b.CHAPA COLLATE Latin1_General_CI_AS = a.chapa AND b.CODCOLIGADA = a.codcoligada
					INNER JOIN ".DBRM_BANCO."..PFUNCAO c (NOLOCK) ON c.CODIGO = b.CODFUNCAO AND c.CODCOLIGADA = b.CODCOLIGADA
					INNER JOIN ".DBRM_BANCO."..PSECAO d (NOLOCK) ON d.CODIGO = b.CODSECAO AND d.CODCOLIGADA = b.CODCOLIGADA
					LEFT JOIN zcrmportal_ocorrencia_tipo e (NOLOCK) ON e.coligada = a.codcoligada
				WHERE 
						a.codcoligada = {$this->coligada} 
					AND a.data BETWEEN '{$data_inicio}' AND '{$data_fim}'
					AND a.resultado IS NOT NULL 
					".str_replace('A.CODSECAO', 'b.CODSECAO', $qr_secao)."
					".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
					".(($ja_justificados == NULL) ? ' AND a.codmotivo IS NULL ' : ' AND a.codmotivo IS NOT NULL ')."
					AND 1 = 
						CASE
							WHEN a.ocorrencia = 'excesso_abono_gestor' THEN e.excesso_gestor
							WHEN a.ocorrencia = 'extra_permitido' THEN e.extra_acima
							WHEN a.ocorrencia = 'extra' THEN 1
							WHEN a.ocorrencia = 'jornada' THEN 1
							WHEN a.ocorrencia = 'trabalho_dsr_folga' THEN 1
							WHEN a.ocorrencia = 'trabalho_dsr_folga_descanso' THEN e.trabalho_dsr_descanso
							WHEN a.ocorrencia = 'trabalho_ferias_afastamento' THEN 1
							WHEN a.ocorrencia = 'registro_manual' THEN e.registro_manual
							WHEN a.ocorrencia = 'trabalho_6dias' THEN e.trabalho_sup6
							WHEN a.ocorrencia = 'interjornada' THEN e.interjornada
							WHEN a.ocorrencia = 'registro_britanico' THEN e.registro_bri
							WHEN a.ocorrencia = 'sobreaviso' THEN e.sobreaviso
							WHEN a.ocorrencia = 'troca_menor_6_meses' THEN e.troca_menor6
							WHEN a.ocorrencia = 'troca_menor_10_dias' THEN e.troca_menor10
							WHEN a.ocorrencia = 'pendente_termo_aditivo' THEN e.req_troca
							ELSE 1
						END
			";
			$result = $this->dbportal->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

		}

		$query = "
            
		WITH 

		PAR AS 
			(
			SELECT 
				COLIGADA = '{$this->coligada}'
				, INICIO   =  '{$data_inicio}'
				, FIM      = '{$data_fim}'
				, HORARIO  = '020,	0012,	0013,	0005,	0033,	Cons.010,	0004,	0055,	006,	0017,	0011,	007,	07,	0202,	0203,	015,	TESTE,	019,	Cons.009,	0007,	0208,	0027,	0023,	108,	110,	092,	104,	084,	118,	0207,	0204,	113,	095,	0032,	03,	100,	0048,	0041,	0042,	0043,	0044,	0026,	0040'        
				, ABONO    = '{$abono}'
			),

			".$withLider."

			QTD_BATIDAS AS
(

SELECT 
A.CODCOLIGADA
,A.CHAPA
,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
,COUNT(A.BATIDA)QTD
/*DENIS 17/04/2024*/,ISNULL((SELECT P.intervalo_obrigatorio
FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
WHERE P.coligada = A.CODCOLIGADA
AND P.codigo =  

(SELECT  TOP 1 CODHORARIO
FROM PFHSTHOR X
 WHERE    
	   X.CODCOLIGADA = A.CODCOLIGADA 
	   AND X.CHAPA = A.CHAPA
	   AND X.DTMUDANCA <= ISNULL(A.DATAREFERENCIA,A.DATA) ORDER BY X.DTMUDANCA DESC ) COLLATE Latin1_General_CI_AS

),0)INTER_OBR	 

/*DENIS 17/04/2024*/,ISNULL((SELECT  P.intervalo_fracionado
FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
WHERE P.coligada = A.CODCOLIGADA
AND P.codigo =  

(SELECT  TOP 1 CODHORARIO
FROM PFHSTHOR X
 WHERE    
	   X.CODCOLIGADA = A.CODCOLIGADA 
	   AND X.CHAPA = A.CHAPA
	   AND X.DTMUDANCA <= ISNULL(A.DATAREFERENCIA,A.DATA) ORDER BY X.DTMUDANCA DESC ) COLLATE Latin1_General_CI_AS

),0)INTER_FRAC   

FROM 	
ABATFUN A 
LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
WHERE 
A.CODCOLIGADA = P.COLIGADA
AND A.DATA BETWEEN P.INICIO AND P.FIM
AND A.STATUS NOT IN ('T')
GROUP BY
A.CODCOLIGADA
,A.CHAPA
,ISNULL(A.DATAREFERENCIA,A.DATA)
),


PREVISTO AS (
SELECT
A.CODCOLIGADA	
,A.CHAPA
,A.DATAREFERENCIA DATA
,SUM(((CASE WHEN B.TIPOOCORRENCIA IN ('AREF','ERA','ERT') THEN (DATEDIFF(MINUTE,A.INICIO,A.FIM)) ELSE NULL END)))PREVISTO


FROM 
AOCORRENCIACALCULADA A (NOLOCK)
INNER JOIN ATIPOOCORRENCIA B (NOLOCK) ON A.TIPOOCORRENCIA = B.TIPOOCORRENCIA
LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA 

WHERE A.CODCOLIGADA = P.COLIGADA
AND A.DATAREFERENCIA BETWEEN P.INICIO AND P.FIM  
AND B.TIPOOCORRENCIA IN ('AREF','ERA','ERT')

GROUP BY
A.CODCOLIGADA
,A.CHAPA
,A.DATAREFERENCIA
)


		/*SELECT
			X.CODCOLIGADA,
			X.CHAPA,
			X.NOME,
			X.CODFUNCAO,
			X.NOMEFUNCAO,
			X.CODSECAO,
			X.NOMESECAO,
			X.DATA,
			X.OCORRENCIA,
			X.VALOR,
			X.COMPLEMENTO,
		  Z.codmotivo,
		  Z.descricao_motivo,
		  Z.ocorrencia log_ocorrencia,
		  Z.observacao,
		  Z.gestor*/

		  [SELECT]


		FROM (





		/***************************************************************************************************************/
		/* Horas Extras acima do permitido  NÃO MOTORISTA                                                                           */
		/***************************************************************************************************************/
			SELECT 
				DISTINCT
				* ,
				'RM' SISTEMA
			FROM (
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'extra_permitido' OCORRENCIA
					 
					
					
					, CASE 
						--WHEN B.FERIADO = 0 THEN CONCAT(dbo.MINTOTIME(ISNULL(Z.HEXTRA_DIARIA,0)),'') ELSE CONCAT(dbo.MINTOTIME(ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0)),'') END
						WHEN B.FERIADO = 0 THEN '' ELSE ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN '' ELSE '' END) ,0) END

					
					COMPLEMENTO
/**/							, (CASE WHEN B.FERIADO = 0 AND EXTRAEXECUTADO > ISNULL(Z.HEXTRA_DIARIA,0) THEN EXTRAEXECUTADO-ISNULL(Z.HEXTRA_DIARIA,0)
/**/									WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
/**/									WHEN 1=2 AND B.FERIADO > 0 AND HTRAB > 0
/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
/**/										AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) = 0 THEN HTRAB - ISNULL(Z.HEXTRA_DIARIA,0)
/**/										
/**/									WHEN B.FERIADO > 0 AND HTRAB > 0 /*Analisando se trabalhou em feriado se a jornada prevista e menor que a jornada trabalhada*/
/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
/**/										--AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) > 0 AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN (HTRAB - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
								/*TIAGO*/AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN ((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN HTRAB ELSE EXTRAEXECUTADO END) - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
/**/									ELSE 0 END) 
/*lucas- ISNULL((SELECT SUM(VALOR) FROM ABANCOHORFUNDETALHE BB WHERE B.CODCOLIGADA = BB.CODCOLIGADA AND B.CHAPA = BB.CHAPA AND B.DATA = BB.DATA AND BB.CODEVENTO NOT IN ('001', '002')),0)*/

- ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0)

-- ISNULL((CASE WHEN B.FERIADO = 0 THEN ISNULL(Z.HEXTRA_DIARIA,0) ELSE ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN 0 ELSE extra_feriado_parcial END) ,0) END) ,0))
VALOR
				
					, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, B.FERIADO
				FROM 
					PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						/*Denis*/LEFT JOIN GFERIADO CAL ON CAL.CODCALENDARIO = D.CODCALENDARIO AND CAL.DIAFERIADO = B.DATA
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
/**/								LEFT  JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario (NOLOCK) P ON P.COLIGADA = A.CODCOLIGADA 
/**/										AND P.CODIGO COLLATE Latin1_General_CI_AS = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
/**/																						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))

						LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))

						LEFT JOIN Z_OUTSERV_MELHORIAS3 Z ON Z.CODCOLIGADA = A.CODCOLIGADA AND Z.CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
				
						LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = B.DATA	
						
						INNER JOIN (
							SELECT MAX(CODINDICE) CODINDICE, CODHORARIO, CODCOLIGADA FROM Z_OUTSERV_MELHORIAS3 GROUP BY CODHORARIO, CODCOLIGADA
						) E2 ON E2.CODHORARIO = AH.CODIGO AND E2.CODCOLIGADA = AH.CODCOLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM

						AND (
							SELECT TOP 1 REGISTRO FROM (
								SELECT
									CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
									CASE
										WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
										ELSE '2199-12-31 23:59:59'
									END DATA
								FROM
									PFUNC
								WHERE
									CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
									AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
							)X WHERE X.DATA >= M.INICIO
							ORDER BY X.DATA ASC
						) IS NOT NULL

						/**/--AND Z.CODINDICE = (CASE WHEN INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) THEN (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) ELSE (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))) END) -1
						AND Z.CODINDICE = dbo.CalculoIndiceHorarioV2(DATEDIFF(DAY, AH.DATABASEHOR, B.DATA), (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)), E2.CODINDICE) 


					{$qr_secao} 
					{$where_filial}
					
					AND (CASE WHEN B.FERIADO = 0 AND EXTRAEXECUTADO > ISNULL(Z.HEXTRA_DIARIA,0) THEN EXTRAEXECUTADO-ISNULL(Z.HEXTRA_DIARIA,0)
/**/									WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
/**/									WHEN 1=2 AND B.FERIADO > 0 AND HTRAB > 0
/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
/**/										AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) = 0 THEN HTRAB - ISNULL(Z.HEXTRA_DIARIA,0)
/**/										
/**/									WHEN B.FERIADO > 0 AND HTRAB > 0 /*Analisando se trabalhou em feriado se a jornada prevista e menor que a jornada trabalhada*/
/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
/**/										--AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) > 0 AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN (HTRAB - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
								/*TIAGO*/AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN ((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN HTRAB ELSE EXTRAEXECUTADO END) - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
/**/									ELSE 0 END) 
/*lucas- ISNULL((SELECT SUM(VALOR) FROM ABANCOHORFUNDETALHE BB WHERE B.CODCOLIGADA = BB.CODCOLIGADA AND B.CHAPA = BB.CHAPA AND B.DATA = BB.DATA AND BB.CODEVENTO NOT IN ('001', '002')),0)*/

- ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0)

-- ISNULL((CASE WHEN B.FERIADO = 0 THEN ISNULL(Z.HEXTRA_DIARIA,0) ELSE ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN 0 ELSE extra_feriado_parcial END) ,0) END) ,0))
> 0


						   AND (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
			)XX
				WHERE XX.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = XX.CODCOLIGADA AND dtdel IS NULL)
			 /*WHERE XX.HORARIO COLLATE Latin1_General_CI_AS NOT IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(escala_especial,0) = 1  )*/

		UNION ALL

		/***************************************************************************************************************/
		/* Horas Extras acima do permitido  MOTORISTA                                                                           */
		/***************************************************************************************************************/
		SELECT 
		DISTINCT
		* ,
		'RM' SISTEMA
	FROM (
		SELECT
			  A.CODCOLIGADA
			, A.CHAPA
			, A.NOME
			, A.CODFUNCAO
			, C.NOME NOMEFUNCAO
			, A.CODSECAO
			, D.DESCRICAO NOMESECAO
			, B.DATA
			, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
				(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
			, 'extra_permitido' OCORRENCIA

			, 'D' COMPLEMENTO

/*							, (EXTRAEXECUTADO - ISNULL(MT.limite_extra,0)) VALOR*/
			, (CASE WHEN B.FERIADO = 0 THEN (EXTRAEXECUTADO - ISNULL(MT.limite_extra,0)) ELSE (EXTRAEXECUTADO - (ISNULL(MT.limite_extra,0)+ ISNULL(MT.horas_prevista,0))) END) VALOR
		
			, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, B.FERIADO
		FROM 
			PFUNC (NOLOCK) A
				LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
				INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
				INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
				/*Denis*/LEFT JOIN GFERIADO CAL ON CAL.CODCALENDARIO = D.CODCALENDARIO AND CAL.DIAFERIADO = B.DATA
				INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
/**/								LEFT  JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario (NOLOCK) P ON P.COLIGADA = A.CODCOLIGADA 
/**/										AND P.CODIGO COLLATE Latin1_General_CI_AS = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
/**/																						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))

				LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))

				LEFT JOIN Z_OUTSERV_MELHORIAS3 Z ON Z.CODCOLIGADA = A.CODCOLIGADA AND Z.CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
		
				LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = B.DATA
				INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista MT ON MT.codcoligada = A.CODCOLIGADA AND MT.dtdel IS NULL AND MT.codfuncao = A.CODFUNCAO COLLATE Latin1_General_CI_AS
		
		WHERE
				B.DATA BETWEEN M.INICIO AND M.FIM
				AND (
					SELECT TOP 1 REGISTRO FROM (
						SELECT
							CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
							CASE
								WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
								ELSE '2199-12-31 23:59:59'
							END DATA
						FROM
							PFUNC
						WHERE
							CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
							AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
					)X WHERE X.DATA >= M.INICIO
					ORDER BY X.DATA ASC
				) IS NOT NULL
			{$qr_secao} 
			{$where_filial}

			/*AND EXTRAEXECUTADO > ISNULL(MT.limite_extra,0)*/
			AND (CASE WHEN B.FERIADO = 0 THEN (EXTRAEXECUTADO - ISNULL(MT.limite_extra,0)) ELSE (EXTRAEXECUTADO - (ISNULL(MT.limite_extra,0)+ ISNULL(MT.horas_prevista,0))) END) > 0
			AND ISNULL(EXTRAEXECUTADO,0) > 0


				   AND (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
	)XX
			
		UNION ALL

		/***************************************************************************************************************/
		/* Horas Extras em escalas específicas (Zero HE)                                                               */
		/***************************************************************************************************************/
			/* Horários 12x36 */
			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, A.NOME
				, A.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, A.CODSECAO
				, D.DESCRICAO NOMESECAO
				, B.DATA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
				, 'extra' OCORRENCIA
				, NULL COMPLEMENTO
				, (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
					   ELSE 0 END) - ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0) VALOR
				
				, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO ,
				'RM' SISTEMA
			FROM 
				PFUNC (NOLOCK) A
					LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
					INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
					INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
					INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
			
			WHERE
					B.DATA BETWEEN M.INICIO AND M.FIM
					AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= M.INICIO
						ORDER BY X.DATA ASC
					) IS NOT NULL
				 {$qr_secao} 
				 {$where_filial}
				AND (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
					   ELSE 0 END)  - ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0)> 0
				AND (SELECT extra_especial FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
			
			UNION ALL
			
			/* Horários de Revezamento */
			SELECT 
				* ,
				'RM' SISTEMA
			FROM (
			  
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'extra' OCORRENCIA
					, NULL COMPLEMENTO
					, (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												 THEN HTRAB
							ELSE 0 END) -ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0) VALOR
					
					, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
				FROM 
					PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
						AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= M.INICIO
						ORDER BY X.DATA ASC
					) IS NOT NULL
					{$qr_secao}
					{$where_filial}
					AND (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												 THEN HTRAB
							ELSE 0 END) -ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0) > 0
					AND (SELECT extra_especial FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
			)XX
			 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(escala_especial,0) = 1 )

		UNION ALL
		/***************************************************************************************************************/
/* Jornada Diária acima de 11 e 10 horas NÃO MOTORISTA                                                         */
/***************************************************************************************************************/
SELECT
CODCOLIGADA
, CHAPA
, NOME
, CODFUNCAO
, NOMEFUNCAO
, CODSECAO
, NOMESECAO
, DATA
, HORARIO
, OCORRENCIA

,CASE 
	WHEN COMPLEMENTO2 = 'FORA' THEN 
	DBO.MINTOTIME((ISNULL(HTRAB,0)-ISNULL(BASE,0)) - ISNULL(HEXTRA_DIARIA,0) - ISNULL(BH,0))
	ELSE COMPLEMENTO
END COMPLEMENTO
,CASE 
	WHEN COMPLEMENTO2 = 'FORA' THEN (ISNULL(HTRAB,0)-ISNULL(BASE,0)) - ISNULL(HEXTRA_DIARIA,0) - ISNULL(BH,0)
	ELSE VALOR
END VALOR

, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO
, SISTEMA

FROM( 
SELECT
COL CODCOLIGADA
,VCHAPA CHAPA
,NOME
,COD_FCO CODFUNCAO
,DESC_FUNCAO NOMEFUNCAO
,COD_SEC CODSECAO
,DESC_SEC NOMESECAO 
,VDATA DATA
,HOR HORARIO
,'jornada' OCORRENCIA

/*02/05/2024*/
,(
(CASE
  WHEN HEXTRA_DIARIA <= 15 AND ENT1 < (ENTRADA1 - TOLERANCIA_INICIO) THEN 'FORA'
  WHEN HEXTRA_DIARIA <= 15 AND SAI > ((CASE WHEN SAIDA2 < ENTRADA1 THEN SAIDA2+1440 ELSE SAIDA2 END) + TOLERANCIA_FIM)      THEN  'FORA'
  WHEN HEXTRA_DIARIA <= 15 AND SAI IS NULL THEN 'N/A'
  WHEN HEXTRA_DIARIA > 15 THEN 'FORA'
ELSE 'N/A' END))COMPLEMENTO2




,'Fora da tolerância - '  + dbo.MINTOTIME((((CASE
  WHEN HEXTRA_DIARIA <= 15 AND ENT1 < (ENTRADA1 - TOLERANCIA_INICIO) THEN (ENTRADA1 - ENT1)
  ELSE 0 END) + 
(CASE
  WHEN HEXTRA_DIARIA <= 15 AND (CASE WHEN SAI < ENTRADA1 THEN SAI+1440 ELSE SAI END)  > ((CASE WHEN SAIDA2 < ENTRADA1 THEN SAIDA2+1440 ELSE SAIDA2 END) + TOLERANCIA_FIM) THEN  ((CASE WHEN SAI < ENTRADA1 THEN SAI+1440 ELSE SAI END) - (CASE WHEN SAIDA2 < ENTRADA1 THEN SAIDA2+1440 ELSE SAIDA2 END) )
ELSE 0 END))))COMPLEMENTO

,((CASE
  WHEN HEXTRA_DIARIA <= 15 AND ENT1 < (ENTRADA1 - TOLERANCIA_INICIO) THEN (ENTRADA1 - ENT1)
  ELSE 0 END) + 
(CASE
  WHEN HEXTRA_DIARIA <= 15 AND (CASE WHEN SAI < ENTRADA1 THEN SAI+1440 ELSE SAI END)  > ((CASE WHEN SAIDA2 < ENTRADA1 THEN SAIDA2+1440 ELSE SAIDA2 END) + TOLERANCIA_FIM) THEN  ((CASE WHEN SAI < ENTRADA1 THEN SAI+1440 ELSE SAI END) - (CASE WHEN SAIDA2 < ENTRADA1 THEN SAIDA2+1440 ELSE SAIDA2 END) )
ELSE 0 END))VALOR


/* ,(ISNULL(HTRAB,0) - (ISNULL(BASE,0) +ISNULL(HEXTRA_DIARIA,0) + ISNULL(BH,0)))VALOR */					
/*02/05/2024*/





, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO
,'RM' SISTEMA  
, HEXTRA_DIARIA
, QTD2
, BH
,EXTRAEXECUTADO 




FROM(
SELECT 
COL
,VCHAPA
,NOME
,COD_FCO
,DESC_FUNCAO
,COD_SEC
,DESC_SEC 
,VDATA
,HOR
,DESC_HOR
, BAT
, ENT1
, SAI1
, ENT2
, SAI2
, ENT3
, SAI3
, ENT4
, SAI4 

,(CASE 
WHEN SAI4 IS NULL  AND SAI3 IS NULL AND SAI2 IS NULL AND SAI1 IS NOT NULL THEN SAI1 
WHEN SAI4 IS NULL  AND SAI3 IS NULL AND SAI2 IS NOT NULL                  THEN SAI2
WHEN SAI4 IS NULL  AND SAI3 IS NOT NULL                                   THEN SAI3
WHEN SAI4 IS NOT NULL                                                     THEN SAI4 
ELSE NULL END)SAI


, TOLERANCIA_INICIO
, TOLERANCIA_FIM
, ENTRADA1
, SAIDA1
, ENTRADA2
, SAIDA2

, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO
, HEXTRA_DIARIA  
, QTD2  
,EXTRAEXECUTADO 

,ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH 
				WHERE OBH.TIPOOCORRENCIA = 'ECA' 
					   AND OBH.CODCOLIGADA = COL
					   AND OBH.CHAPA = VCHAPA 
					   AND OBH.DATAREFERENCIA = VDATA
					   AND OBH.ATITUDE = 1),0)BH	  

FROM(
SELECT COL
,VCHAPA
,NOME
,COD_FCO
,DESC_FUNCAO
,COD_SEC
,DESC_SEC 
,DATA VDATA   

, DESC_HOR
, HOR         

, [1] AS ENT1, [2] AS SAI1
, [3] AS ENT2, [4] AS SAI2
, [5] AS ENT3, [6] AS SAI3
, [7] AS ENT4, [8] AS SAI4

,((CASE WHEN [1] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [2] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [3] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [4] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [5] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [6] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [7] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [8] IS NOT NULL THEN 1 ELSE 0 END)
)BAT

, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO
, DATA_INDICE  
,EXTRAEXECUTADO 



FROM (
SELECT 
A.CODCOLIGADA COL
,A.CHAPA VCHAPA
,B.NOME
,C.CODIGO COD_FCO
,C.NOME DESC_FUNCAO
,D.CODIGO COD_SEC
,D.DESCRICAO DESC_SEC
,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
,ISNULL(A.DATAREFERENCIA,A.DATA)VDATA
,ROW_NUMBER() OVER (PARTITION BY A.CHAPA,ISNULL(A.DATAREFERENCIA, A.DATA) ORDER BY A.CHAPA, A.DATA, ISNULL(A.DATAREFERENCIA, A.DATA)) AS LIN

,(CASE 
WHEN ISNULL(A.DATAREFERENCIA, A.DATA) > A.DATA THEN BATIDA 
WHEN ISNULL(A.DATAREFERENCIA, A.DATA) < A.DATA THEN BATIDA 
ELSE BATIDA
END) AS BATIDA     

,M.EXTRAEXECUTADO
,M.BASE BASE
,M.HTRAB HTRAB
,M.EXTRAAUTORIZADO EXTRAAUTORIZADO
,M.COMPENSADO COMPENSADO
,M.DESCANSO DESCANSO
,M.FERIADO FERIADO
,AH.DESCRICAO DESC_HOR
,AH.CODIGO HOR
,DATEADD(day, +((SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= ISNULL(A.DATAREFERENCIA, A.DATA)))-1), ISNULL(A.DATAREFERENCIA, A.DATA)) DATA_INDICE


FROM ABATFUN A
LEFT JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA 
LEFT JOIN PFUNCAO C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO 
LEFT JOIN PSECAO D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
INNER JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
LEFT JOIN	AAFHTFUN M ON M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND M.DATA = ISNULL(A.DATAREFERENCIA, A.DATA)
LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))

WHERE 
A.STATUS <> 'T'
AND B.CODFUNCAO NOT IN (SELECT MT.codfuncao COLLATE Latin1_General_CI_AS FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista MT WHERE MT.codcoligada = B.CODCOLIGADA) 
AND ISNULL(A.DATAREFERENCIA, A.DATA) BETWEEN P.INICIO AND P.FIM
AND (
	SELECT TOP 1 REGISTRO FROM (
		SELECT
			CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
			CASE
				WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
				ELSE '2199-12-31 23:59:59'
			END DATA
		FROM
			PFUNC
		WHERE
			CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
			AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
	)X WHERE X.DATA >= P.INICIO
	ORDER BY X.DATA ASC
) IS NOT NULL
".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
--AND A.CHAPA = '050002721'


) AS PivotSource
PIVOT (
	MAX(BATIDA) FOR LIN IN ([1], [2], [3], [4], [5],[6],[7],[8])) AS PivotTable

)TBL
LEFT JOIN Z_OUTSERV_MELHORIAS4 ZZ ON ZZ.DATA = DATA_INDICE AND ZZ.CODHORARIO = HOR AND ZZ.CODCOLIGADA = COL
LEFT JOIN Z_OUTSERV_MELHORIAS3 E ON E.CODCOLIGADA = TBL.COL AND E.CODHORARIO = TBL.HOR AND E.CODINDICE =  ZZ.INDICE
)XX

WHERE BAT % 2 = 0 /*SOMENTE BATIDAS PAR???*/

)ABC
WHERE  
 (ISNULL(HTRAB,0) > (ISNULL(BASE,0)+ (ISNULL(HEXTRA_DIARIA,0) + ISNULL(BH,0))))
AND  (ISNULL(HTRAB,0) - ISNULL(BH,0)) >  (ISNULL(BASE,0) + ISNULL(HEXTRA_DIARIA,0))
AND  (ISNULL(HTRAB,0) > (ISNULL(QTD2,0) + ISNULL(HEXTRA_DIARIA,0)))
AND  ((ISNULL(HTRAB,0)-ISNULL(BASE,0)) - ISNULL(HEXTRA_DIARIA,0) - ISNULL(BH,0)) > 0
AND  (SELECT excesso_jornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
AND ISNULL(BASE,0) > 0
-------AND  ISNULL(BASE,0) BETWEEN 1 AND 540
--AND HEXTRA_DIARIA <= 15
/*02/05/2024*/ AND COMPLEMENTO2 <> 'N/A'
AND COMPLEMENTO2 = 'FORA'


/***************************************************************************************************************/
/* Jornada Diária acima de 11 e 10 horas MOTORISTA                                                             */
/***************************************************************************************************************/
				
UNION ALL


SELECT
CODCOLIGADA
, CHAPA
, NOME
, CODFUNCAO
, NOMEFUNCAO
, CODSECAO
, NOMESECAO
, DATA
, HORARIO
, OCORRENCIA
, COMPLEMENTO
, VALOR
, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO
, SISTEMA
FROM( 
SELECT
COL CODCOLIGADA
,VCHAPA CHAPA
,NOME
,COD_FCO CODFUNCAO
,DESC_FUNCAO NOMEFUNCAO
,COD_SEC CODSECAO
,DESC_SEC NOMESECAO 
,VDATA DATA
,HOR HORARIO
,'jornada' OCORRENCIA


,NULL COMPLEMENTO

,(ISNULL(HTRAB,0) - ISNULL(limite_jornada, 0)) VALOR				

, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO
,'RM' SISTEMA  
, HEXTRA_DIARIA
, QTD2
, BH
,limite_jornada

FROM(
SELECT 
COL
,VCHAPA
,NOME
,COD_FCO
,DESC_FUNCAO
,COD_SEC
,DESC_SEC 
,VDATA
,HOR
,DESC_HOR
, BAT
, ENT1
, SAI1
, ENT2
, SAI2
, ENT3
, SAI3
, ENT4
, SAI4 

,(CASE 
WHEN SAI4 IS NULL  AND SAI3 IS NULL AND SAI2 IS NULL AND SAI1 IS NOT NULL THEN SAI1 
WHEN SAI4 IS NULL  AND SAI3 IS NULL AND SAI2 IS NOT NULL                  THEN SAI2
WHEN SAI4 IS NULL  AND SAI3 IS NOT NULL                                   THEN SAI3
WHEN SAI4 IS NOT NULL                                                     THEN SAI4 
ELSE NULL END)SAI


, TOLERANCIA_INICIO
, TOLERANCIA_FIM
, ENTRADA1
, SAIDA1
, ENTRADA2
, SAIDA2

, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO
, ISNULL(MT.limite_jornada,0) limite_jornada  
, QTD2  
,E.HEXTRA_DIARIA


/* ,ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH 
				WHERE OBH.TIPOOCORRENCIA = 'ECA' 
					   AND OBH.CODCOLIGADA = COL
					   AND OBH.CHAPA = VCHAPA 
					   AND OBH.DATAREFERENCIA = VDATA
					   AND OBH.ATITUDE = 1),0) */ ,0 BH	  

FROM(
SELECT COL
,VCHAPA
,NOME
,COD_FCO
,DESC_FUNCAO
,COD_SEC
,DESC_SEC 
,DATA VDATA   
	 

, [1] AS ENT1, [2] AS SAI1
, [3] AS ENT2, [4] AS SAI2
, [5] AS ENT3, [6] AS SAI3
, [7] AS ENT4, [8] AS SAI4

,((CASE WHEN [1] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [2] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [3] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [4] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [5] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [6] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [7] IS NOT NULL THEN 1 ELSE 0 END)+
(CASE WHEN [8] IS NOT NULL THEN 1 ELSE 0 END)
)BAT

, BASE
, HTRAB
, EXTRAAUTORIZADO
, COMPENSADO
, DESCANSO
, FERIADO    
, DATA_INDICE
, DESC_HOR
, HOR


FROM (
SELECT 
A.CODCOLIGADA COL
,A.CHAPA VCHAPA
,B.NOME
,C.CODIGO COD_FCO
,C.NOME DESC_FUNCAO
,D.CODIGO COD_SEC
,D.DESCRICAO DESC_SEC
,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
,ISNULL(A.DATAREFERENCIA,A.DATA)VDATA
,ROW_NUMBER() OVER (PARTITION BY A.CHAPA,ISNULL(A.DATAREFERENCIA, A.DATA) ORDER BY A.CHAPA, A.DATA, ISNULL(A.DATAREFERENCIA, A.DATA)) AS LIN

,(CASE 
WHEN ISNULL(A.DATAREFERENCIA, A.DATA) > A.DATA THEN BATIDA 
WHEN ISNULL(A.DATAREFERENCIA, A.DATA) < A.DATA THEN BATIDA 
ELSE BATIDA
END) AS BATIDA     


,M.BASE BASE
,M.HTRAB HTRAB
,M.EXTRAAUTORIZADO EXTRAAUTORIZADO
,M.COMPENSADO COMPENSADO
,M.DESCANSO DESCANSO
,M.FERIADO FERIADO
,AH.DESCRICAO DESC_HOR
,AH.CODIGO HOR
,DATEADD(day, +((SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= ISNULL(A.DATAREFERENCIA, A.DATA)))-1), ISNULL(A.DATAREFERENCIA, A.DATA)) DATA_INDICE

FROM ABATFUN A
LEFT JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA 
LEFT JOIN PFUNCAO C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO 
LEFT JOIN PSECAO D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
INNER JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
LEFT JOIN	AAFHTFUN M ON M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND M.DATA = ISNULL(A.DATAREFERENCIA, A.DATA)
LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))
WHERE 
A.STATUS <> 'T'
AND ISNULL(A.DATAREFERENCIA, A.DATA) BETWEEN P.INICIO AND P.FIM
AND (
	SELECT TOP 1 REGISTRO FROM (
		SELECT
			CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
			CASE
				WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
				ELSE '2199-12-31 23:59:59'
			END DATA
		FROM
			PFUNC
		WHERE
			CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
			AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
	)X WHERE X.DATA >= P.INICIO
	ORDER BY X.DATA ASC
) IS NOT NULL
".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."


) AS PivotSource
PIVOT (
	MAX(BATIDA) FOR LIN IN ([1], [2], [3], [4], [5],[6],[7],[8])) AS PivotTable

)TBL
LEFT JOIN Z_OUTSERV_MELHORIAS4 ZZ ON ZZ.DATA = DATA_INDICE AND ZZ.CODHORARIO = HOR AND ZZ.CODCOLIGADA = COL
LEFT JOIN Z_OUTSERV_MELHORIAS3 E ON E.CODCOLIGADA = TBL.COL AND E.CODHORARIO = TBL.HOR AND E.CODINDICE =  ZZ.INDICE
INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista MT ON MT.codcoligada = TBL.COL AND MT.dtdel IS NULL AND MT.codfuncao = TBL.COD_FCO COLLATE Latin1_General_CI_AS
)XX

WHERE BAT % 2 = 0 /*SOMENTE BATIDAS PAR???*/

)ABC
WHERE  
ISNULL(HTRAB,0) > ISNULL(limite_jornada,0)
AND (SELECT excesso_jornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
-----AND isnull(BASE,0) BETWEEN 1 AND 540
-- AND HEXTRA_DIARIA <= 15
-- AND COMPLEMENTO <> 'N/A'
AND VALOR > 0

		UNION ALL
		/***************************************************************************************************************/
		/* Falta de registro adequado de jornada de trabalho (Registro Manual)                                         */
		/***************************************************************************************************************/
			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, B.NOME
				, B.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, B.CODSECAO
				, D.DESCRICAO NOMESECAO
				, A.DATA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO	    
				, 'registro_manual' OCORRENCIA
				, NULL COMPLEMENTO
				, A.BATIDA VALOR
				
				, NULL BASE, NULL HTRAB, NULL EXTRAAUTORIZADO, NULL COMPENSADO, NULL DESCANSO, NULL FERIADO,
				'RM' SISTEMA
			FROM 
				ABATFUN (NOLOCK) A
				INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				INNER JOIN PAR              M ON M.COLIGADA = A.CODCOLIGADA
			
			WHERE
					A.DATA BETWEEN M.INICIO AND M.FIM
					AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= A.DATA
						ORDER BY X.DATA ASC
					) IS NOT NULL
				AND A.STATUS = 'D'
				 ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
				 ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
				 AND (SELECT registro_manual FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
						
				 UNION ALL
				 /***************************************************************************************************************/
				 /* Trabalho em DSR ou Folga [COMPENSADO]        JORNADA SEMANAL                                                                  */
				 /***************************************************************************************************************/

				 /*CICLO NÃO MOTORISTA*/
				 SELECT
				 CODCOLIGADA,
				 CHAPA,
				 NOME,
				 CODFUNCAO,
				 NOMEFUNCAO,
				 CODSECAO,
				 NOMESECAO,
				 MAX(DATA) DATA,
				 HOR HORARIO,
				 'trabalho_dsr_folga_descanso' OCORRENCIA,
				 /*CONCAT(
					 dbo.MINTOTIME(SUM(QTD2)),
					 ' previsto, ',
					 dbo.MINTOTIME(SUM(HTRAB)),
					 ' realizado, limite de ',
					 dbo.MINTOTIME(MAX(EXCESSO_JORNADA_SEMANAL)),
					 ', Ciclo: ',
					 SUBSTRING(LINHA,1,1),
					 ', excedido:  ',
					 dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
				 ) COMPLEMENTO,*/
				
					 dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
				  COMPLEMENTO,
				  SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL) VALOR,
				 NULL BASE,
				 NULL HTRAB,
				 NULL EXTRAAUTORIZADO,
				 NULL COMPENSADO,
				 NULL DESCANSO,
				 NULL FERIADO,
				 'RM' SISTEMA
				 
			 FROM (
			 
			 SELECT 
				 X.CODCOLIGADA
				 ,X.CHAPA
				 ,X.NOME
				 ,DATEPART(WK,X.DATA)SEM
				 ,X.DATA
				 ,X.HOR
				 ,X.DESC_HOR
				 ,SUM(X.HTRAB - X.TOTALBH)HTRAB
				 ,SUM(D.QTD2)QTD2
				 ,SUM(X.EXTRAEXECUTADO)EXTRAEXECUTADO
				 ,SUM(D.HEXTRA_DIARIA)HEXTRA_DIARIA
			 
				 ,CONCAT(D.CICLO,'-', ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA) - ROW_NUMBER() OVER (PARTITION BY D.CICLO ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA))LINHA
				 ,E.TIPO_HORARIO
				 --,SUM(ISNULL(EXCESSO_JORNADA_SEMANAL,0))EXCESSO_JORNADA_SEMANAL
				 ,ISNULL((SELECT MAX(EXCESSO_JORNADA_SEMANAL) FROM Z_OUTSERV_MELHORIAS3 WHERE CODHORARIO = X.HOR AND CODCOLIGADA = X.CODCOLIGADA AND CICLO = D.CICLO),0) EXCESSO_JORNADA_SEMANAL
				 ,X.CODFUNCAO
				 ,X.NOMEFUNCAO
				 ,X.CODSECAO
				 ,X.NOMESECAO
				 
				 , X.BASE
			 FROM(   
			 
			 
			 
			 SELECT
				DISTINCT
				 Y.*,
				 ZZ.INDICE IND
				 
				 ,ROW_NUMBER() OVER (PARTITION BY 
				 
					 ZZ.INDICE 


				 
				 ORDER BY CHAPA, Y.DATA) AS LINHA
			 
			 
			 
			 FROM (
				SELECT
					B.CODCOLIGADA
					,B.CHAPA
					,B.NOME
					,A.DATA
					,A.BASE
					,A.HTRAB
					,A.EXTRAEXECUTADO
					,A.ATRASO
					,A.ABONO
					,(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC) HOR
					,(SELECT TOP 1 AA.DESCRICAO FROM PFHSTHOR C, AHORARIO AA (NOLOCK) WHERE AA.CODCOLIGADA = C.CODCOLIGADA AND AA.CODIGO = C.CODHORARIO AND C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)DESC_HOR
					,
					
					B.CODFUNCAO,
					C.NOME NOMEFUNCAO,
					B.CODSECAO,
					D.DESCRICAO NOMESECAO,
					ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = A.DATA AND OBH.ATITUDE = 1),0) TOTALBH,
					DATEADD(day, +((SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))-1), A.DATA) DATA_INDICE
				
				FROM
					AAFHTFUN A (NOLOCK)
					LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					LEFT JOIN PFUNCAO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					LEFT JOIN PSECAO D (NOLOCK) ON D.CODIGO = B.CODSECAO AND D.CODCOLIGADA = B.CODCOLIGADA
					INNER JOIN PAR P ON 1=1
					
				WHERE
					A.CODCOLIGADA = P.COLIGADA
					AND A.DATA BETWEEN '{$data_inicio_ciclo}' AND P.FIM
					/*AND B.CODSITUACAO NOT IN ('D')*/
					AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= A.DATA
						ORDER BY X.DATA ASC
					) IS NOT NULL
					".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
					".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."

					AND B.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = B.CODCOLIGADA AND dtdel IS NULL)
					AND (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
					)Y
					LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = Y.CODCOLIGADA AND ZZ.CODHORARIO = Y.HOR AND ZZ.DATA = Y.DATA_INDICE
					
				)X 
				
						LEFT JOIN Z_OUTSERV_MELHORIAS3 (NOLOCK) D 
										ON D.CODCOLIGADA = X.CODCOLIGADA 
											AND D.CODHORARIO = X.HOR
											AND D.CODINDICE = X.IND
					LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario E (NOLOCK) ON E.COLIGADA COLLATE Latin1_General_CI_AS = D.CODCOLIGADA AND E.CODIGO COLLATE Latin1_General_CI_AS = D.CODHORARIO
					WHERE TIPO_HORARIO IN ('C')

				GROUP BY
					X.CODCOLIGADA
					,X.CHAPA
					,X.NOME
					,X.DATA
					,X.HOR
					,X.DESC_HOR
					,DATEPART(WK,X.DATA)
					,D.CICLO
					,TIPO_HORARIO
					, X.NOMEFUNCAO
					, X.CODFUNCAO
					, X.CODSECAO
					, X.NOMESECAO
					, X.BASE

				)z
				GROUP BY
					CODCOLIGADA
					, CHAPA
					, NOME
					, HOR
					, DESC_HOR
					, LINHA
					, TIPO_HORARIO
					, NOMEFUNCAO
					, CODFUNCAO
					, CODSECAO
					, NOMESECAO
				having SUM(HTRAB) > MAX(EXCESSO_JORNADA_SEMANAL)

		UNION ALL

		/*INDICE NÃO MOTORISTA*/
		SELECT
			CODCOLIGADA,
			CHAPA,
			NOME,
			CODFUNCAO,
			NOMEFUNCAO,
			CODSECAO,
			NOMESECAO,
			MAX(DATA) DATA,
			HOR HORARIO,
			'trabalho_dsr_folga_descanso' OCORRENCIA,
			/*CONCAT(
				dbo.MINTOTIME(SUM(QTD2)),
				' previsto, ',
				dbo.MINTOTIME(SUM(HTRAB)),
				' realizado, limite de ',
				dbo.MINTOTIME(MAX(EXCESSO_JORNADA_SEMANAL)),
				', Ciclo: ',
				SUBSTRING(LINHA,1,1),
				', excedido:  ',
				dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
			) COMPLEMENTO,*/
			
				dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
			 COMPLEMENTO,
			 SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL) VALOR,
			NULL BASE,
			NULL HTRAB,
			NULL EXTRAAUTORIZADO,
			NULL COMPENSADO,
			NULL DESCANSO,
			NULL FERIADO,
			'RM' SISTEMA
		FROM(
		
		SELECT 
		DISTINCT
			X.CODCOLIGADA
			,X.CHAPA
			,X.NOME
			,DATEPART(WK,X.DATA)SEM
			,X.DATA DATA
			,X.HOR
			,X.DESC_HOR
			,SUM(X.HTRAB - X.TOTALBH)HTRAB
			,SUM(D.QTD2)QTD2
			,SUM(X.EXTRAEXECUTADO)EXTRAEXECUTADO
			,SUM(D.HEXTRA_DIARIA)HEXTRA_DIARIA
		
			--,CONCAT(D.CICLO,'-', ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA) - ROW_NUMBER() OVER (PARTITION BY D.CICLO ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA))LINHA
			,ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA, ZZ.INDICE) - ZZ.INDICE LINHA
			,E.TIPO_HORARIO
			--,SUM(ISNULL(EXCESSO_JORNADA_SEMANAL,0))EXCESSO_JORNADA_SEMANAL
			,ISNULL((SELECT MAX(EXCESSO_JORNADA_SEMANAL) FROM Z_OUTSERV_MELHORIAS3 WHERE CODHORARIO = X.HOR AND CODCOLIGADA = X.CODCOLIGADA AND CICLO = D.CICLO),0) EXCESSO_JORNADA_SEMANAL
			,X.CODFUNCAO
			,X.NOMEFUNCAO
			,X.CODSECAO
			,X.NOMESECAO
			
			, X.BASE
		FROM(   
			SELECT
				B.CODCOLIGADA
				,B.CHAPA
				,B.NOME
				,A.DATA
				,A.BASE
				,A.HTRAB
				,A.EXTRAEXECUTADO
				,A.ATRASO
				,A.ABONO
				,(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC) HOR
				,(SELECT TOP 1 AA.DESCRICAO FROM PFHSTHOR C, AHORARIO AA (NOLOCK) WHERE AA.CODCOLIGADA = C.CODCOLIGADA AND AA.CODIGO = C.CODHORARIO AND C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)DESC_HOR
				,
				B.CODFUNCAO,
				C.NOME NOMEFUNCAO,
				B.CODSECAO,
				D.DESCRICAO NOMESECAO,
				ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = A.DATA AND OBH.ATITUDE = 1),0) TOTALBH,
				DATEADD(day, +((SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))-1), A.DATA) DATA_INDICE
			
			FROM
				AAFHTFUN A (NOLOCK)
				LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				LEFT JOIN PFUNCAO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				LEFT JOIN PSECAO D (NOLOCK) ON D.CODIGO = B.CODSECAO AND D.CODCOLIGADA = B.CODCOLIGADA
				INNER JOIN PAR P ON 1=1							
				
				
			WHERE
				A.CODCOLIGADA = P.COLIGADA
				AND A.DATA BETWEEN '{$data_inicio_ciclo}' AND P.FIM
				/*AND B.CODSITUACAO NOT IN ('D')*/
				AND (
                    SELECT TOP 1 REGISTRO FROM (
                        SELECT
                            CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                            CASE
                                WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                ELSE '2199-12-31 23:59:59'
                            END DATA
                        FROM
                            PFUNC
                        WHERE
                            CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
							AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                    )X WHERE X.DATA >= A.DATA
                    ORDER BY X.DATA ASC
                ) IS NOT NULL
				".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
				".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."

				AND B.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = B.CODCOLIGADA AND dtdel IS NULL)
				AND (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
		)X 
				LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = X.CODCOLIGADA AND ZZ.CODHORARIO = X.HOR AND ZZ.DATA = X.DATA_INDICE
				LEFT JOIN Z_OUTSERV_MELHORIAS3 (NOLOCK) D  
								ON D.CODCOLIGADA = X.CODCOLIGADA 
									AND D.CODHORARIO = X.HOR
									AND D.CODINDICE = ZZ.INDICE
			LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario E (NOLOCK) ON E.COLIGADA COLLATE Latin1_General_CI_AS = D.CODCOLIGADA AND E.CODIGO COLLATE Latin1_General_CI_AS = D.CODHORARIO
			WHERE TIPO_HORARIO IN ('I')

		GROUP BY
			X.CODCOLIGADA
			,X.CHAPA
			,X.NOME
			,X.DATA
			,X.HOR
			,X.DESC_HOR
			,DATEPART(WK,X.DATA)
			,CICLO
			,TIPO_HORARIO
			, X.NOMEFUNCAO
			, X.CODFUNCAO
			, X.CODSECAO
			, X.NOMESECAO
			, X.BASE
			,ZZ.INDICE

		)z
		GROUP BY
			CODCOLIGADA
			, CHAPA
			, NOME
			, HOR
			, DESC_HOR
			, LINHA
			, TIPO_HORARIO
			, NOMEFUNCAO
			, CODFUNCAO
			, CODSECAO
			, NOMESECAO
		having SUM(HTRAB) > MAX(EXCESSO_JORNADA_SEMANAL)


		UNION ALL
		
		/*SEMANA NÃO MOTORISTA*/
		
		SELECT
			
			  CODCOLIGADA
			, CHAPA
			, NOME
			, CODFUNCAO
			, NOMEFUNCAO
			, CODSECAO
			, NOMESECAO
			, DATA
			, HORARIO
			, OCORRENCIA
			
			, dbo.MINTOTIME(HTRAB - EXCESSO_JORNADA_SEMANAL) COMPLEMENTO
			, (HTRAB - EXCESSO_JORNADA_SEMANAL) VALOR
			
			, BASE
			, HTRAB
			, EXTRAAUTORIZADO
			, COMPENSADO
			, DESCANSO
			, FERIADO
			, SISTEMA
		FROM
		(
		
		SELECT
		
			(
				SELECT EXCESSO_JORNADA_SEMANAL FROM dbo.fn_semana(CODCOLIGADA, CHAPA, MIN(DATA), MAX(DATA), (CASE WHEN DATEPART(dw,DATA) = 1 THEN DATEPART(WK,DATA)-1 ELSE DATEPART(WK,DATA) END))
			) EXCESSO_JORNADA_SEMANAL,
		
			MIN(DATA) DATA_MIN,
			MAX(DATA) DATA_MAX,
			
			CODCOLIGADA,
			CHAPA,
			NOME,
			CODFUNCAO,
			NOMEFUNCAO,
			CODSECAO,
			NOMESECAO,
			MAX(DATA) DATA,
			--HOR HORARIO,
			(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = z.CODCOLIGADA AND C.CHAPA = z.CHAPA AND C.DTMUDANCA <= MAX(DATA) ORDER BY C.DTMUDANCA DESC) HORARIO,
			'trabalho_dsr_folga_descanso' OCORRENCIA,

		
			
			 NULL COMPLEMENTO,


			NULL VALOR,
			NULL BASE,
			SUM(HTRAB) HTRAB,
			NULL EXTRAAUTORIZADO,
			NULL COMPENSADO,
			NULL DESCANSO,
			NULL FERIADO,
			'RM' SISTEMA
			
		FROM(
		
		SELECT 
			X.CODCOLIGADA
			,X.CHAPA
			,X.NOME
			,(CASE WHEN DATEPART(dw,X.DATA) = 1 THEN DATEPART(WK,X.DATA)-1 ELSE DATEPART(WK,X.DATA) END)SEM
			,X.DATA DATA
			,X.HOR
			,X.DESC_HOR
			,SUM(X.HTRAB - X.TOTALBH)HTRAB
			,SUM(D.QTD2)QTD2
			,SUM(X.EXTRAEXECUTADO)EXTRAEXECUTADO
			,SUM(D.HEXTRA_DIARIA)HEXTRA_DIARIA
		
			,CONCAT(D.CICLO,'-', ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA) - ROW_NUMBER() OVER (PARTITION BY D.CICLO ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA))LINHA
			,E.TIPO_HORARIO
			,ISNULL((SELECT MAX(EXCESSO_JORNADA_SEMANAL) FROM Z_OUTSERV_MELHORIAS3 WHERE CODHORARIO = X.HOR AND CODCOLIGADA = X.CODCOLIGADA),0) EXCESSO_JORNADA_SEMANAL
			,X.CODFUNCAO
			,X.NOMEFUNCAO
			,X.CODSECAO
			,X.NOMESECAO
			
			
		FROM(  
		
		
		SELECT 
		DISTINCT
			Y.*
			,ZZ.INDICE IND 
			,ROW_NUMBER() OVER (PARTITION BY  ZZ.INDICE ORDER BY Y.CHAPA, Y.DATA) AS LINHA
		
		FROM (
			SELECT
				B.CODCOLIGADA
				,B.CHAPA
				,B.NOME
				,A.DATA
				,A.BASE
				,A.HTRAB
				,A.EXTRAEXECUTADO
				,A.ATRASO
				,A.ABONO
				,(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)HOR
				,(SELECT TOP 1 AA.DESCRICAO FROM PFHSTHOR C, AHORARIO AA (NOLOCK) WHERE AA.CODCOLIGADA = C.CODCOLIGADA AND AA.CODIGO = C.CODHORARIO AND C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)DESC_HOR
				,B.CODFUNCAO,
				C.NOME NOMEFUNCAO,
				B.CODSECAO,
				D.DESCRICAO NOMESECAO,
				ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = A.DATA AND OBH.ATITUDE = 1),0) TOTALBH,
				DATEADD(day, +((SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))-1), A.DATA) DATA_INDICE
			FROM
				AAFHTFUN A (NOLOCK)
				LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				LEFT JOIN PFUNCAO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				LEFT JOIN PSECAO D (NOLOCK) ON D.CODIGO = B.CODSECAO AND D.CODCOLIGADA = B.CODCOLIGADA
				INNER JOIN PAR P ON 1=1
				
			WHERE
				A.CODCOLIGADA = P.COLIGADA
				AND A.DATA BETWEEN '{$data_inicio_ciclo}' AND P.FIM
				/*AND B.CODSITUACAO NOT IN ('D')*/
				AND (
                    SELECT TOP 1 REGISTRO FROM (
                        SELECT
                            CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                            CASE
                                WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                ELSE '2199-12-31 23:59:59'
                            END DATA
                        FROM
                            PFUNC
                        WHERE
                            CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
							AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                    )X WHERE X.DATA >= A.DATA
                    ORDER BY X.DATA ASC
                ) IS NOT NULL
				".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
				".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
				AND B.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = B.CODCOLIGADA AND dtdel IS NULL)
				AND (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1

		
				)Y
				LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = Y.CODCOLIGADA AND ZZ.CODHORARIO = Y.HOR AND ZZ.DATA = Y.DATA_INDICE

			
			)X
			
					LEFT JOIN Z_OUTSERV_MELHORIAS3 (NOLOCK) D  
									ON D.CODCOLIGADA = X.CODCOLIGADA 
										AND D.CODHORARIO = X.HOR
										AND D.CODINDICE = X.IND
					LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario E (NOLOCK) ON E.COLIGADA COLLATE Latin1_General_CI_AS = D.CODCOLIGADA AND E.CODIGO COLLATE Latin1_General_CI_AS = D.CODHORARIO

						WHERE TIPO_HORARIO = 'S'
			
			GROUP BY
				X.CODCOLIGADA
				,X.CHAPA
				,X.NOME
				,X.DATA
				,X.HOR
				,X.DESC_HOR
				,(CASE WHEN DATEPART(dw,X.DATA) = 1 THEN DATEPART(WK,X.DATA)-1 ELSE DATEPART(WK,X.DATA) END)
				,CICLO
				,E.TIPO_HORARIO
				,X.CODFUNCAO
				,X.NOMEFUNCAO
				,X.CODSECAO
				,X.NOMESECAO

			)z   
			GROUP BY
				CODCOLIGADA
				, CHAPA
				, NOME
				--, HOR
				--, DESC_HOR
				, LINHA
				, TIPO_HORARIO
				, NOMEFUNCAO
				, CODFUNCAO
				, CODSECAO
				, NOMESECAO
				,SEM
				,(CASE WHEN DATEPART(dw,DATA) = 1 THEN DATEPART(WK,DATA)-1 ELSE DATEPART(WK,DATA) END)
				

			--having SUM(HTRAB) > MAX(EXCESSO_JORNADA_SEMANAL)
)Y
	   
	WHERE
		HTRAB > EXCESSO_JORNADA_SEMANAL

		UNION ALL
		
		/*INDICE MOTORISTA*/
		SELECT
			CODCOLIGADA,
			CHAPA,
			NOME,
			CODFUNCAO,
			NOMEFUNCAO,
			CODSECAO,
			NOMESECAO,
			MAX(DATA) DATA,
			HOR HORARIO,
			'trabalho_dsr_folga_descanso' OCORRENCIA,
			/*CONCAT(
				dbo.MINTOTIME(SUM(QTD2)),
				' previsto, ',
				dbo.MINTOTIME(SUM(HTRAB)),
				' realizado, limite de ',
				dbo.MINTOTIME(MAX(EXCESSO_JORNADA_SEMANAL)),
				', Ciclo: ',
				SUBSTRING(LINHA,1,1),
				', excedido:  ',
				dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
			) COMPLEMENTO,*/
			
				dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
			 COMPLEMENTO,
			SUM(QTD2) VALOR,
			NULL BASE,
			NULL HTRAB,
			NULL EXTRAAUTORIZADO,
			NULL COMPENSADO,
			NULL DESCANSO,
			NULL FERIADO,
			'RM' SISTEMA
		FROM(
		
		SELECT 
			X.CODCOLIGADA
			,X.CHAPA
			,X.NOME
			,DATEPART(WK,X.DATA)SEM
			,X.DATA DATA
			,X.HOR
			,X.DESC_HOR
			,SUM(X.HTRAB - X.TOTALBH)HTRAB
			,SUM(D.QTD2)QTD2
			,SUM(X.EXTRAEXECUTADO)EXTRAEXECUTADO
			,SUM(D.HEXTRA_DIARIA)HEXTRA_DIARIA
		
			--,CONCAT(D.CICLO,'-', ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA) - ROW_NUMBER() OVER (PARTITION BY D.CICLO ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA))LINHA
			,ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA, X.IND) - X.IND LINHA
			,E.TIPO_HORARIO
			--,SUM(ISNULL(EXCESSO_JORNADA_SEMANAL,0))EXCESSO_JORNADA_SEMANAL
			--,ISNULL((SELECT MAX(EXCESSO_JORNADA_SEMANAL) FROM Z_OUTSERV_MELHORIAS3 WHERE CODHORARIO = X.HOR AND CODCOLIGADA = X.CODCOLIGADA AND CICLO = D.CICLO),0) EXCESSO_JORNADA_SEMANAL
			,X.CODFUNCAO
			,X.NOMEFUNCAO
			,X.CODSECAO
			,X.NOMESECAO
			
			, X.BASE
			, MAX(X.excesso_semanal) EXCESSO_JORNADA_SEMANAL
		FROM( 
		  
			
			SELECT
				Y.*,
				ZZ.INDICE IND
			FROM (
			
			
			SELECT
				B.CODCOLIGADA
				,B.CHAPA
				,B.NOME
				,A.DATA
				,A.BASE
				,A.HTRAB
				,A.EXTRAEXECUTADO
				,A.ATRASO
				,A.ABONO
				,(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC) HOR
				,(SELECT TOP 1 AA.DESCRICAO FROM PFHSTHOR C, AHORARIO AA (NOLOCK) WHERE AA.CODCOLIGADA = C.CODCOLIGADA AND AA.CODIGO = C.CODHORARIO AND C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)DESC_HOR
				,
				B.CODFUNCAO,
				C.NOME NOMEFUNCAO,
				B.CODSECAO,
				D.DESCRICAO NOMESECAO,
				MT.excesso_semanal,
				ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = A.DATA AND OBH.ATITUDE = 1),0) TOTALBH
				,DATEADD(day, +((SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))-1), A.DATA) DATA_INDICE
			
			FROM
				AAFHTFUN A (NOLOCK)
				LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				LEFT JOIN PFUNCAO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				LEFT JOIN PSECAO D (NOLOCK) ON D.CODIGO = B.CODSECAO AND D.CODCOLIGADA = B.CODCOLIGADA
				INNER JOIN PAR P ON 1=1
				INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista MT ON MT.codcoligada = B.CODCOLIGADA AND MT.dtdel IS NULL AND MT.codfuncao = B.CODFUNCAO COLLATE Latin1_General_CI_AS
				
				
			WHERE
				A.CODCOLIGADA = P.COLIGADA
				AND A.DATA BETWEEN '{$data_inicio_ciclo}' AND P.FIM
				/*AND B.CODSITUACAO NOT IN ('D')*/
				AND (
                    SELECT TOP 1 REGISTRO FROM (
                        SELECT
                            CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                            CASE
                                WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                ELSE '2199-12-31 23:59:59'
                            END DATA
                        FROM
                            PFUNC
                        WHERE
                            CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
							AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                    )X WHERE X.DATA >= A.DATA
                    ORDER BY X.DATA ASC
                ) IS NOT NULL
				".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
				".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."

				AND B.CODFUNCAO COLLATE Latin1_General_CI_AS IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = B.CODCOLIGADA AND dtdel IS NULL)
				AND (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
				)Y
			
				LEFT JOIN Z_OUTSERV_MELHORIAS4 ZZ ON ZZ.DATA = Y.DATA_INDICE AND ZZ.CODHORARIO = Y.HOR AND ZZ.CODCOLIGADA = Y.CODCOLIGADA
				
			)X 
			
			
					LEFT JOIN Z_OUTSERV_MELHORIAS3 (NOLOCK) D  
									ON D.CODCOLIGADA = X.CODCOLIGADA 
										AND D.CODHORARIO = X.HOR
										AND D.CODINDICE = X.IND
				LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario E (NOLOCK) ON E.COLIGADA COLLATE Latin1_General_CI_AS = D.CODCOLIGADA AND E.CODIGO COLLATE Latin1_General_CI_AS = D.CODHORARIO
				WHERE TIPO_HORARIO IN ('I')

			GROUP BY
				X.CODCOLIGADA
				,X.CHAPA
				,X.NOME
				,X.DATA
				,X.HOR
				,X.DESC_HOR
				,DATEPART(WK,X.DATA)
				,CICLO
				,TIPO_HORARIO
				, X.NOMEFUNCAO
				, X.CODFUNCAO
				, X.CODSECAO
				, X.NOMESECAO
				, X.BASE
				,X.IND

			)z
			GROUP BY
				CODCOLIGADA
				, CHAPA
				, NOME
				, HOR
				, DESC_HOR
				, LINHA
				, TIPO_HORARIO
				, NOMEFUNCAO
				, CODFUNCAO
				, CODSECAO
				, NOMESECAO
			having SUM(HTRAB) > MAX(EXCESSO_JORNADA_SEMANAL)
				 
				 
				 UNION ALL
				 
				 /***************************************************************************************************************/
				 /* Trabalho em DSR ou Folga [DESCANSO]                                                                            */
				 /***************************************************************************************************************/
				SELECT * FROM (
				SELECT 
/**/							CODCOLIGADA, CHAPA, NOME, CODFUNCAO, NOMEFUNCAO, CODSECAO, NOMESECAO, DATA, HORARIO, OCORRENCIA, COMPLEMENTO, 
/*
(
CASE 
WHEN ISNULL(


(CASE WHEN CAL_HORAINICIO =0 AND CAL_HORAFINAL >= 1439 THEN N.extra_feriado ELSE N.extra_feriado_parcial END) ,0) = 0 THEN 

(CASE WHEN CAL_HORAINICIO =0 AND CAL_HORAFINAL >= 1439 THEN VALOR ELSE EXTRAAUTORIZADO END) ELSE 

(CASE WHEN CAL_HORAINICIO =0 AND CAL_HORAFINAL >= 1439 THEN VALOR ELSE EXTRAAUTORIZADO END) END)*/



VALOR, BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO, SISTEMA
				FROM ( 
					 SELECT
						   A.CODCOLIGADA
						 , A.CHAPA
						 , A.NOME
						 , A.CODFUNCAO
						 , C.NOME NOMEFUNCAO
						 , A.CODSECAO
						 , D.DESCRICAO NOMESECAO
						 , B.DATA
						 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						 , 'trabalho_dsr_folga' OCORRENCIA 
						 , NULL COMPLEMENTO
						 , (CASE WHEN (DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
				
						WHEN B.FERIADO > 0 AND HTRAB > 0
									AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													THEN 0
													

								ELSE 0 END) VALOR
					 
						 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, B.FERIADO,
						'RM' SISTEMA,

						CAL.HORAINICIO CAL_HORAINICIO,
						CAL.HORAFINAL CAL_HORAFINAL
						 
					 FROM 
						 PFUNC (NOLOCK) A
							 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
							 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
							 /*Denis*/LEFT JOIN GFERIADO CAL ON CAL.CODCALENDARIO = D.CODCALENDARIO AND CAL.DIAFERIADO = B.DATA
							 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							 LEFT  JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario (NOLOCK) P ON P.COLIGADA = A.CODCOLIGADA 
/**/										AND P.CODIGO COLLATE Latin1_General_CI_AS = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
/**/																						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))

							LEFT JOIN Z_OUTSERV_MELHORIAS3 Z ON Z.CODCOLIGADA = A.CODCOLIGADA AND Z.CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))

					 WHERE
							 B.DATA BETWEEN M.INICIO AND M.FIM
							 AND (
								SELECT TOP 1 REGISTRO FROM (
									SELECT
										CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
										CASE
											WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
											ELSE '2199-12-31 23:59:59'
										END DATA
									FROM
										PFUNC
									WHERE
										CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
										AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
								)X WHERE X.DATA >= M.INICIO
								ORDER BY X.DATA ASC
							) IS NOT NULL
						{$qr_secao}
						{$where_filial}
						 AND (CASE WHEN (DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN B.FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0)
								 ELSE 0 END) > 0
						AND (SELECT trabalho_dsr FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1               
						)XX
						LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario (NOLOCK) N ON XX.CODCOLIGADA = N.COLIGADA AND XX.HORARIO COLLATE Latin1_General_CI_AS = N.CODIGO
/**/								WHERE (XX.VALOR > ISNULL(N.extra_feriado,0) or XX.VALOR > ISNULL(N.extra_feriado_parcial,0)) AND XX.VALOR > 0
						/*WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(extra_feriado,0) = 1 )*/
				)YY WHERE VALOR > 0

		UNION ALL
		/***************************************************************************************************************/
		/* Trabalho em Férias ou Afastamentos                                                                             */
		/***************************************************************************************************************/

			SELECT
				  CODCOLIGADA
				, CHAPA
				, NOME
				, CODFUNCAO
				, NOMEFUNCAO
				, CODSECAO
				, NOMESECAO
				, DATA
				, HORARIO
				, OCORRENCIA
				, CASE 
					WHEN FERIAS IS NOT NULL THEN 'Férias'
					WHEN AFAST IS NOT NULL THEN CONCAT('Afastamento/Atestado', 

					CASE WHEN (SELECT COUNT(AB.BATIDA) FROM ABATFUN AB WHERE AB.CHAPA = CHAPA AND AB.CODCOLIGADA = CODCOLIGADA AND COALESCE(AB.DATAREFERENCIA, AB.DATA) = DATA) > 0 THEN ' Existe registro de ponto' ELSE '' END)

					ELSE NULL
				  END COMPLEMENTO
				, CASE 
					WHEN FERIAS IS NOT NULL THEN 1
					WHEN AFAST IS NOT NULL THEN 2
					ELSE NULL
				  END VALOR
				, NULL BASE
				, NULL HTRAB
				, NULL EXTRAAUTORIZADO
				, NULL COMPENSADO
				, NULL DESCANSO
				, NULL FERIADO,
				'RM' SISTEMA
				
			FROM (
			
				SELECT 
					AX.* 
				FROM(
				
					SELECT
						  A.CODCOLIGADA
						, A.CHAPA
						, A.NOME
						, A.CODFUNCAO
						, C.NOME NOMEFUNCAO
						, A.CODSECAO
						, D.DESCRICAO NOMESECAO
						, B.DATA
						, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						, 'trabalho_ferias_afastamento' OCORRENCIA
						, (SELECT 'Férias' FROM PFUFERIASPER (NOLOCK) F  WHERE F.CODCOLIGADA = A.CODCOLIGADA AND F.CHAPA = A.CHAPA AND B.DATA BETWEEN F.DATAINICIO AND F.DATAFIM) FERIAS
						, (SELECT CASE WHEN (SELECT COUNT(BATIDA) FROM ABATFUN WHERE CHAPA = A.CHAPA AND CODCOLIGADA = A.CODCOLIGADA AND COALESCE(DATAREFERENCIA, DATA) = H.DTINICIO) <= 0 OR H.DTINICIO <> B.DATA THEN 'Afastamento' ELSE NULL END FROM PFHSTAFT (NOLOCK) H WHERE H.CODCOLIGADA = A.CODCOLIGADA AND H.CHAPA = A.CHAPA AND B.DATA BETWEEN H.DTINICIO AND  ISNULL(H.DTFINAL, '2050-12-01') ) AFAST
						, (SELECT COUNT(*) FROM ABATFUN (NOLOCK) G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) BAT
						, B.ABONO
					
					FROM 
						PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
						
					
					WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
						AND (
							SELECT TOP 1 REGISTRO FROM (
								SELECT
									CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
									CASE
										WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
										ELSE '2199-12-31 23:59:59'
									END DATA
								FROM
									PFUNC
								WHERE
									CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
									AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
							)X WHERE X.DATA >= M.INICIO
							ORDER BY X.DATA ASC
						) IS NOT NULL
						{$qr_secao}
						 {$where_filial}
					AND (SELECT trabalho_AfastFerias FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
										
				
				)AX
				WHERE	
					(AX.FERIAS IS NOT NULL OR AX.AFAST IS NOT NULL) AND AX.BAT > 0 AND ( ABONO = 0 OR ABONO >= 240) /* A pedido ignorado se o abono foi menor que 4horas*/
									
			)BX
					   


		UNION ALL
		/***************************************************************************************************************/
		/* InterJornada                                                                                                */
		/***************************************************************************************************************/

		/**SELECT 
			CODCOLIGADA
			, CHAPA
			, NOME
			, COD_FUNCAO CODFUNCAO
			, FUNCAO NOMEFUNCAO
			, SECAO_COD CODSECAO
			, DESC_SEC NOMESECAO
			, DATA DATAREFERENCIA
			, HOR_HIST HORARIO
			, OCORRENCIA
			, COMPLEMENTO
			, NULL VALOR

			, NULL BASE
			, NULL HTRAB
			, NULL EXTRAAUTORIZADO
			, NULL COMPENSADO
			, NULL DESCANSO
			, NULL FERIADO,
			'RM' SISTEMA

		FROM(
			SELECT 
				CODCOLIGADA,
				CHAPA, 
				NOME,
				COD_FUNCAO,
				FUNCAO,
				SECAO_COD,
				DESC_SEC,
				DATA, 
				dbo.mintotime(isnull(UM,'')) + '   ' + dbo.mintotime(isnull(DOIS,'')) + '   ' + dbo.mintotime(isnull(TRES,'')) + '   ' + dbo.mintotime(isnull(QUATRO,'')) + '   ' + dbo.mintotime(isnull(CINCO,'')) + '   ' + dbo.mintotime(isnull(SEIS,'')) + '   ' + dbo.mintotime(isnull(SETE,'')) + '   ' + dbo.mintotime(isnull(OITO,'')) BATIDAS,
				'interjornada' OCORRENCIA,
				
				(
					CASE WHEN QTD = 999 
					THEN 'Não realizou intervalo de Refeição ' ELSE 
					
					CONCAT(
						'Previsto ', dbo.mintotime(PREVISTO),
						', realizado ', ISNULL(dbo.mintotime((CASE WHEN (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END)< 0 
					THEN TRES + (1440 - DOIS)
					ELSE (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END) END)), '00:00'),' ,suprimido ', dbo.mintotime((PREVISTO - (CASE WHEN (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END)< 0 
					THEN TRES + (1440 - DOIS)
					ELSE (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE 0 END) END))
				)
							
				)END) COMPLEMENTO
				, '' VALOR
				, NULL BASE
				, NULL HTRAB
				, NULL EXTRAAUTORIZADO
				, NULL COMPENSADO
				, NULL DESCANSO
				, NULL FERIADO
				,'RM' SISTEMA
				, HOR_HIST
			FROM (
				
				SELECT 
					INICIO
					,FIM
					,CODCOLIGADA
					,CHAPA
					,NOME
					,COD_FUNCAO
					,FUNCAO
					,SECAO_COD
					,DESC_SEC
					,DATA
				
					,[1] BATIDA_ENT_REALIZADA
					,(SELECT MIN(BATINICIO) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_ENT_DEVERIA
					,(SELECT MAX(BATFIM) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_SAI_DEVERIA
			
					,CASE WHEN [8] IS NULL AND [7] IS NOT NULL THEN [7]
							WHEN [7] IS NULL AND [6] IS NOT NULL THEN [6]
								WHEN [6] IS NULL AND [5] IS NOT NULL THEN [5]
									WHEN [5] IS NULL AND [4] IS NOT NULL THEN [4]
										WHEN [4] IS NULL AND [3] IS NOT NULL THEN [3]
											WHEN [3] IS NULL AND [2] IS NOT NULL THEN [2]
												WHEN [2] IS NULL THEN [1] ELSE ''
													END BATIDA_SAI_REALIZADA
											
						
					,[1] UM
					,[2] DOIS
					,[3] TRES
					,[4] QUATRO
					,[5] CINCO
					,[6] SEIS
					,[7] SETE
					,[8] OITO
					,HTRAB
					,CODRECEBIMENTO
					,CODTIPO
					,QTD
					,PREVISTO
					,HOR_HIST
				FROM (	
					
					SELECT 
						ROW_NUMBER() OVER(PARTITION BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END), B.CHAPA ORDER BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)) LINHA,
						INICIO,
						FIM,
						A.CODCOLIGADA, 
						A.CHAPA, 
						C.NOME,
						C.CODSECAO SECAO_COD,
						D.DESCRICAO SECAO_NOME,
						D.DESCRICAO DESC_SEC,
						FU.CODIGO COD_FUNCAO,
						FU.NOME FUNCAO,
						A.DATA,
						B.BATIDA,
						(SELECT CODHORARIO FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
							(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA)) HOR_HIST,
							
			
					C.CODRECEBIMENTO, 
					C.CODTIPO,
					A.HTRAB,
					Q.QTD,
					P.PREVISTO
					
						
					
					FROM AAFHTFUN A
						
						LEFT JOIN ABATFUN B
							ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA = (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)
								AND B.STATUS NOT IN ('T')
					
						LEFT JOIN PFUNC C
							ON A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA 
					
						INNER JOIN PSECAO D
							ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODIGO = C.CODSECAO
						
						INNER JOIN PAR ON PAR.COLIGADA = A.CODCOLIGADA
						
						LEFT JOIN PSECAOCOMPL DC (NOLOCK) ON DC.CODCOLIGADA = D.CODCOLIGADA AND DC.CODIGO = D.CODIGO
					
						LEFT JOIN PFUNCAO FU (NOLOCK) ON FU.CODCOLIGADA = C.CODCOLIGADA AND FU.CODIGO = C.CODFUNCAO
					
					LEFT JOIN QTD_BATIDAS Q ON Q.CODCOLIGADA = A.CODCOLIGADA AND Q.CHAPA = A.CHAPA AND Q.DATA = A.DATA
					
					LEFT JOIN PREVISTO P ON P.CODCOLIGADA = A.CODCOLIGADA AND P.CHAPA = A.CHAPA AND P.DATA = A.DATA
					
					
					
					WHERE 
						A.CODCOLIGADA = PAR.COLIGADA
					AND A.DATA  BETWEEN PAR.INICIO AND PAR.FIM
					".str_replace('A.CODSECAO', 'C.CODSECAO', $qr_secao)."
					".str_replace('A.CODFILIAL', 'C.CODFILIAL', $where_filial)."
					AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
				
				
				)TABELACONSULTA
				
				PIVOT
				
				(
					MAX(BATIDA)
					FOR [LINHA] IN ([1],[2],[3],[4],[5],[6],[7],[8])
				) 
				AS TABELAPIVOT
				
				WHERE [1] IS NOT NULL
			
			)X
			
			WHERE 
				(ISNULL((CASE WHEN (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END)< 0  
						THEN TRES + (1440 - DOIS)
						ELSE (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END) END), 0) < PREVISTO
			
				OR (QTD =999))
		)Y******/

		SELECT 
			  CODCOLIGADA
			, CHAPA
			, NOME
			, CODFUNCAO
			, NOMEFUNCAO
			, CODSECAO
			, NOMESECAO
			, DATAREFERENCIA
			, HORARIO
			, OCORRENCIA
			, 
				(CASE WHEN DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) IS NULL or DATEDIFF(minute ,BAT_anterior, BAT_atual) > 660 THEN 'Bat.Inválida'
					  ELSE DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) END) 
				COMPLEMENTO
			, 1 VALOR

			, NULL BASE
			, NULL HTRAB
			, NULL EXTRAAUTORIZADO
			, NULL COMPENSADO
			, NULL DESCANSO
			, NULL FERIADO,
			'RM' SISTEMA

		FROM(

			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, B.NOME
				, B.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, B.CODSECAO
				, D.DESCRICAO NOMESECAO
				, A.DATAREFERENCIA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATAREFERENCIA)) HORARIO
				, 'interjornada' OCORRENCIA
				, 1 VALOR
				, AAVISO.DESCRICAO AVISO 
				,(SELECT MAX(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA-1 = M.DATAREFERENCIA AND NATUREZA = 1) BAT_anterior
				,(SELECT MIN(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA = M.DATAREFERENCIA AND NATUREZA = 0) BAT_atual
			FROM 
				AAVISOCALCULADO A (NOLOCK)
					INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				, 
				AAVISO,
				PAR
			WHERE 
					A.CODCOLIGADA = PAR.COLIGADA
					AND A.CODAVISO = AAVISO.CODAVISO 
					AND A.CODCOLIGADA = PAR.COLIGADA
					AND A.DATAREFERENCIA BETWEEN PAR.INICIO AND PAR.FIM
					AND A.CODAVISO = '1'
					AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= PAR.INICIO
						ORDER BY X.DATA ASC
					) IS NOT NULL
					".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
					".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
					AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 


				)X

				WHERE (CASE WHEN DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) IS NULL or DATEDIFF(minute ,BAT_anterior, BAT_atual) > 660 THEN 'Bat.Inválida'
					  ELSE DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) END) NOT IN ('Bat.Inválida')

			UNION ALL
			/***************************************************************************************************************/
			/* IntraJornada    [tipo de retorno COMPLEMENTO]                                                               */
			/***************************************************************************************************************/
			  
			/*
			  SELECT 
				  CODCOLIGADA
				, CHAPA
				, NOME
				, CODFUNCAO
				, NOMEFUNCAO
				, CODSECAO
				, NOMESECAO
				, DATA
				, HORARIO
				, OCORRENCIA
				, ' Tempo Mínimo de refeição (IntraJornada) ' + dbo.mintotime(REF_OBRIGATORIO) + ' ' + 'não realizado [' + dbo.mintotime(REF_REALIZADO) + ']' COMPLEMENTO
				, VALOR
				
				, BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO,
				'RM' SISTEMA
			  FROM (
			  
				SELECT 
						 ISNULL((SELECT (FIM-INICIO) TEMPO
						FROM ABATHOR M
						WHERE 
						M.CODCOLIGADA = XX.CODCOLIGADA
						AND M.CODHORARIO  = XX.HORARIO
						AND TIPO = 4 
						AND INDICE = 1
						),0) REF_OBRIGATORIO
				  , *
					
				FROM (
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , A.NOME
					  , A.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , A.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , B.DATA
					  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					  , 'interjornada' OCORRENCIA
					  , NULL VALOR
					  
					  ,ISNULL((SELECT SUM(DATEDIFF(MINUTE,INICIO,FIM)) FROM AOCORRENCIACALCULADA P
					  WHERE 
						A.CODCOLIGADA = P.CODCOLIGADA 
					  AND A.CHAPA = P.CHAPA 
					  AND B.DATA = P.DATAREFERENCIA
					  AND TIPOOCORRENCIA IN ('AREF')),0) REF_REALIZADO
				  
					  , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
				  FROM 
					  PFUNC (NOLOCK) A
						  LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						  INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						  INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						  INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				  
				  WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					  AND B.HTRAB > 0
					  AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
					  {$qr_secao} 
					   {$where_filial}
			  
				)XX
			  )XY
			  WHERE REF_OBRIGATORIO-REF_REALIZADO >= 1 AND REF_OBRIGATORIO > 60
			  */

							
	
			  /*
			  SELECT 
				  CODCOLIGADA
				, CHAPA
				, NOME
				, CODFUNCAO
				, NOMEFUNCAO
				, CODSECAO
				, NOMESECAO
				, DATA
				, HORARIO
				, OCORRENCIA
				, ' Tempo Mínimo de refeição (IntraJornada) ' + dbo.mintotime(REF_OBRIGATORIO) + ' ' + 'não realizado [' + dbo.mintotime(REF_REALIZADO) + ']' COMPLEMENTO
				, VALOR
				
				, BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO,
				'RM' SISTEMA
			  FROM (
			  
				SELECT 
						 ISNULL((SELECT (FIM-INICIO) TEMPO
						FROM ABATHOR M
						WHERE 
						M.CODCOLIGADA = XX.CODCOLIGADA
						AND M.CODHORARIO  = XX.HORARIO
						AND TIPO = 4 
						AND INDICE = 1
						),0) REF_OBRIGATORIO
				  , *
					
				FROM (
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , A.NOME
					  , A.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , A.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , B.DATA
					  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					  , 'interjornada' OCORRENCIA
					  , NULL VALOR
					  
					  ,ISNULL((SELECT SUM(DATEDIFF(MINUTE,INICIO,FIM)) FROM AOCORRENCIACALCULADA P
					  WHERE 
						A.CODCOLIGADA = P.CODCOLIGADA 
					  AND A.CHAPA = P.CHAPA 
					  AND B.DATA = P.DATAREFERENCIA
					  AND TIPOOCORRENCIA IN ('AREF')),0) REF_REALIZADO
				  
					  , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
				  FROM 
					  PFUNC (NOLOCK) A
						  LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						  INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						  INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						  INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				  
				  WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					  AND B.HTRAB > 0
					  AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
					   AND A.CODSECAO IN ('010.34146.003')  
					   
			  
				)XX
			  )XY
			  WHERE REF_OBRIGATORIO-REF_REALIZADO >= 1 AND REF_OBRIGATORIO > 60
			  */

							
	
			  SELECT * FROM( 
				SELECT
					 COL CODCOLIGADA
					,VCHAPA CHAPA
					,NOME
					,COD_FCO CODFUNCAO
					,DESC_FUNCAO NOMEFUNCAO
					,COD_SEC CODSECAO
					,DESC_SEC NOMESECAO 
					,VDATA DATA
					,HOR HORARIO
					,'intrajornada' OCORRENCIA
					,(CASE 
					
				/*30/04/2024*/ /*MOTORISTA
					   WHEN MOTORISTA = 'SIM' AND  BAT = 4 AND PLAN_INIC = PLAN_FIM AND ENT2-SAI1 < INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - (ENT2-SAI1))
					   WHEN MOTORISTA = 'SIM' AND  BAT = 4 AND SAI1 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT2 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT2-SAI1 < INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - (ENT2-SAI1))
					   WHEN MOTORISTA = 'SIM' AND  BAT = 4 AND  SAI1 < PLAN_INIC AND ENT2-SAI1 < INTERVALO  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - (ENT2-SAI1))  + ' antes do planiejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 4 AND ENT2 > PLAN_FIM AND ENT2-SAI1 < INTERVALO   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - (ENT2-SAI1))  + ' depois do planiejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 4 AND SAI1 < PLAN_INIC AND ENT2-SAI1 >= INTERVALO THEN 'Intervalo correto antes do planijejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 4 AND ENT2 > PLAN_FIM AND ENT2-SAI1 = INTERVALO  THEN 'Intervalo correto depois do planijejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 6 AND PLAN_INIC = PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)) < INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)))
					   WHEN MOTORISTA = 'SIM' AND  BAT = 6 AND SAI1 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT3 BETWEEN PLAN_INIC AND PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)) < INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)))
					   WHEN MOTORISTA = 'SIM' AND  BAT = 6 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)) < INTERVALO  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)))  + ' antes do planiejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 6 AND ENT3 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)) < INTERVALO   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)))  + ' depois do planiejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 6 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)) >= INTERVALO THEN 'Intervalo correto antes do planijejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 6 AND ENT3 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)) = INTERVALO  THEN 'Intervalo correto depois do planijejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 8 AND PLAN_INIC = PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))
					   WHEN MOTORISTA = 'SIM' AND  BAT = 8 AND SAI1 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT4 BETWEEN PLAN_INIC AND PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))
					   WHEN MOTORISTA = 'SIM' AND  BAT = 8 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))  + ' antes do planiejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 8 AND ENT4 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))  + ' depois do planiejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 8 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) >= INTERVALO THEN 'Intervalo correto antes do planijejado'
					   WHEN MOTORISTA = 'SIM' AND  BAT = 8 AND ENT4 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) = INTERVALO  THEN 'Intervalo correto depois do planijejado'  
			   /*30/04/2024*/   MOTORISTA*/
				
				
				
				
				 
			  /*30/04/2024*/  /*4 BAT FRACIONADO*/
					   WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 4 AND PLAN_INIC_HOR = PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 4 AND SAI1 BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END) BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 4 AND SAI1 < PLAN_INIC_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))  + ' antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 4 AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END) > PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))  + ' depois do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 4 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND SAI1 < PLAN_INIC_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 = INTERVALO_HOR THEN 'Intervalo correto antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 4 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END) > PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 = INTERVALO_HOR  THEN 'Intervalo correto depois do planejado'
						
					   
						  /*6 BAT FRACIONADO*/
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 6 AND PLAN_INIC_HOR = PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 6 AND SAI1 BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND ENT3 BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)) < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 6 AND SAI1 < PLAN_INIC_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)) < INTERVALO_HOR  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)))  + ' antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 6 AND ENT3 > PLAN_FIM_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)) < INTERVALO_HOR   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)))  + ' depois do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 6 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND SAI1 < PLAN_INIC_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)) > INTERVALO_HOR THEN 'Intervalo correto antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 6 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND ENT3 > PLAN_FIM_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)) = INTERVALO_HOR  THEN 'Intervalo correto depois do planejado'
						
						   
						  /*8 BAT FRACIONADO*/
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 8 AND PLAN_INIC_HOR = PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 8 AND SAI1 BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND ENT4 BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 8 AND SAI1 < PLAN_INIC_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO_HOR  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))  + ' antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 8 AND ENT4 > PLAN_FIM_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO_HOR   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))  + ' depois do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 8 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND SAI1 < PLAN_INIC_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) > INTERVALO_HOR THEN 'Intervalo correto antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FRACIONADO' AND BAT = 8 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND ENT4 > PLAN_FIM_HOR AND (((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) = INTERVALO_HOR  THEN 'Intervalo correto depois do planejado'  
						
						  /*4 BAT FIXO*/
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FIXO' AND BAT = 4 AND PLAN_INIC_HOR = PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FIXO' AND BAT = 4 AND SAI1 BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END) BETWEEN PLAN_INIC_HOR AND PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FIXO' AND BAT = 4 AND SAI1 < PLAN_INIC_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))  + ' antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FIXO' AND BAT = 4 AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END) > PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 < INTERVALO_HOR   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO_HOR - ((CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1))  + ' depois do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FIXO' AND BAT = 4 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND SAI1 < PLAN_INIC_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 = INTERVALO_HOR THEN 'Intervalo correto antes do planejado'
						  WHEN /*MOTORISTA = 'NAO' AND*/ FRACIONADO = 'FIXO' AND BAT = 4 AND PLAN_INIC_HOR <> PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END) > PLAN_FIM_HOR AND (CASE WHEN ENT2 < SAI1 THEN ENT2+1440 ELSE ENT2 END)-SAI1 = INTERVALO_HOR  THEN 'Intervalo correto depois do planejado'
					 
			   /*30/04/2024*/ ELSE 'CORRETO' END)COMPLEMENTO   
					 
					 
					  ,NULL VALOR
					  ,NULL BASE
					  ,NULL HTRAB
					  ,NULL EXTRAAUTORIZADO
					  ,NULL COMPENSADO
					  ,NULL DESCANSO
					  ,NULL FERIADO
					  ,'RM' SISTEMA
					
					
					/*
					  , dbo.MINTOTIME(ENT1)ENT1
					  
					  , dbo.MINTOTIME(SAI1)SAI1
					  , dbo.MINTOTIME(ENT2)ENT2
					  , dbo.MINTOTIME(SAI2)SAI2
					  , dbo.MINTOTIME(ENT3)ENT3
					  , dbo.MINTOTIME(SAI3)SAI3
					  , dbo.MINTOTIME(ENT4)ENT4
					  , dbo.MINTOTIME(SAI4)SAI4
					  
					  ,dbo.MINTOTIME(PLAN_INIC_hor)PLAN_INIC_hor
					  ,dbo.MINTOTIME(PLAN_FIM_hor)PLAN_FIM_hor
					  ,dbo.MINTOTIME(PLAN_INIC)PLAN_INIC
					  ,dbo.MINTOTIME(PLAN_FIM)PLAN_FIM 
					  
					  ,dbo.MINTOTIME(INTERVALO_hor)INTERVALO_hor
					  ,dbo.MINTOTIME(INTERVALO)INTERVALO
					  ,FRACIONADO
					  ,MOTORISTA
					   */
					
					 
				FROM(
				SELECT 
					   COL
					  ,VCHAPA
					  ,NOME
					  ,COD_FCO
					  ,DESC_FUNCAO
					  ,COD_SEC
					  ,DESC_SEC 
					  ,VDATA
					  ,HOR
					  ,DESC_HOR
					  ,'|'T
					  ,BAT
					  
					 
					  
						
					  
		   /*30/04/2024*/	 /*MOTORISTA
				 
				  ,(CASE WHEN (SELECT COUNT(P.codfuncao) FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P WHERE P.CODCOLIGADA = COL AND P.CODFUNCAO = COD_FCO COLLATE Latin1_General_CI_AS AND ISNULL(P.USUDEL,0) <> 1) >=1 THEN 'SIM'ELSE 'NAO' END)MOTORISTA 
				  
				
					  ,(SELECT P.intervalo_fracionado
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
					WHERE P.codcoligada = COL  
						  AND ISNULL(P.USUDEL,0) <> 1 
						  AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)INTER_FRAC
						  
						  
					,(SELECT P.intervalo_obrigatorio
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
					WHERE P.codcoligada = COL
						  AND ISNULL(P.USUDEL,0) <> 1 
						  AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)INTER_OBR	      
				
				
					,(SELECT P.intervalo_planejado_ini
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
					WHERE P.codcoligada = COL
						  AND ISNULL(P.USUDEL,0) <> 1 
						  AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)PLAN_INIC	  
				
					,(SELECT P.intervalo_planejado_fim
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
					WHERE P.codcoligada = COL  
						  AND ISNULL(P.USUDEL,0) <> 1 
						  AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)PLAN_FIM	  
						  
						   
				   
				   
					,isnull((SELECT P.intervalo_total
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
					WHERE P.codcoligada = COL 
						  AND ISNULL(P.USUDEL,0) <> 1 
						  AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS),0)INTERVALO*/
		 /*30/04/2024*/	 /*MOTORISTA*/     

				 /*HORARIO*/     
					  
						 ,(SELECT P.intervalo_fracionado
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
					WHERE P.coligada = COL  
						  AND P.codigo = HOR COLLATE Latin1_General_CI_AS)INTER_FRAC_HOR
						  
						  
					,(SELECT P.intervalo_obrigatorio
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
					WHERE P.coligada = COL  
						  AND P.codigo = HOR COLLATE Latin1_General_CI_AS)INTER_OBR_HOR	      
				
				
					,(SELECT P.planejado_inicio
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
					WHERE P.coligada = COL  
						  AND P.codigo = HOR COLLATE Latin1_General_CI_AS)PLAN_INIC_HOR	  
				
					,(SELECT P.planejado_termino
					FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
					WHERE P.coligada = COL  
						  AND P.codigo = HOR COLLATE Latin1_General_CI_AS)PLAN_FIM_HOR	     
					  
					  
						,(SELECT ISNULL((A.INTERVALO),0)
						 FROM 
						  Z_OUTSERV_MELHORIAS3 A
						  
						 WHERE 
							A.CODCOLIGADA = COL
							AND A.CODHORARIO = HOR
							AND A.CODINDICE = (SELECT IND_CALC FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,VDATA))) INTERVALO_HOR  
									
					  
				 /*HORARIO*/ 
					  
					  
							  
							  
					  , ENT1
					  
					  , SAI1
					  , ENT2
					  , SAI2
					  , ENT3
					  , SAI3
					  , ENT4
					  
					  , SAI4 
							  
								
								
					  ,(SELECT (CASE WHEN ISNULL(P.intervalo_fracionado,0) = 1 AND ISNULL(P.intervalo_obrigatorio,0) = 1 THEN 'FRACIONADO' ELSE 'FIXO' END)
						  FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
						   WHERE P.coligada = COL  
								AND P.codigo = HOR COLLATE Latin1_General_CI_AS)FRACIONADO   	      
								
								
								
								
								
				
				FROM(
				SELECT COL
					  ,VCHAPA
					  ,NOME
					  ,COD_FCO
					  ,DESC_FUNCAO
					  ,COD_SEC
					  ,DESC_SEC 
					  ,DATA VDATA   
						
					  ,(SELECT CODHORARIO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))HOR         
					  ,(SELECT DESCRICAO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))DESC_HOR
					  ,(SELECT IND_CALC FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))IND_HOR           
					   
					  , [1] AS ENT1, [2] AS SAI1
					  , [3] AS ENT2, [4] AS SAI2
					  , [5] AS ENT3, [6] AS SAI3
					  , [7] AS ENT4, [8] AS SAI4
					
					,((CASE WHEN [1] IS NOT NULL THEN 1 ELSE 0 END)+
					  (CASE WHEN [2] IS NOT NULL THEN 1 ELSE 0 END)+
					  (CASE WHEN [3] IS NOT NULL THEN 1 ELSE 0 END)+
					  (CASE WHEN [4] IS NOT NULL THEN 1 ELSE 0 END)+
					  (CASE WHEN [5] IS NOT NULL THEN 1 ELSE 0 END)+
					  (CASE WHEN [6] IS NOT NULL THEN 1 ELSE 0 END)+
					  (CASE WHEN [7] IS NOT NULL THEN 1 ELSE 0 END)+
					  (CASE WHEN [8] IS NOT NULL THEN 1 ELSE 0 END)
					 )BAT
				FROM (
					SELECT 
						A.CODCOLIGADA COL
						,A.CHAPA VCHAPA
						,B.NOME
						,C.CODIGO COD_FCO
						,C.NOME DESC_FUNCAO
						,D.CODIGO COD_SEC
						,D.DESCRICAO DESC_SEC
						,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
						,ISNULL(A.DATAREFERENCIA,A.DATA)VDATA
						,ROW_NUMBER() OVER (PARTITION BY A.CHAPA,ISNULL(A.DATAREFERENCIA, A.DATA) ORDER BY A.CHAPA, A.DATA, ISNULL(A.DATAREFERENCIA, A.DATA)) AS LIN
						
						,(CASE 
							WHEN ISNULL(DATAREFERENCIA, DATA) > A.DATA THEN BATIDA 
							WHEN ISNULL(DATAREFERENCIA, DATA) < A.DATA THEN BATIDA 
							ELSE BATIDA
						END) AS BATIDA     
						
						
						
					FROM ABATFUN A
						 LEFT JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA 
						 LEFT JOIN PFUNCAO C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO 
						 LEFT JOIN PSECAO D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
						 INNER JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
					WHERE 
							A.STATUS <> 'T'
						AND ISNULL(A.DATAREFERENCIA, A.DATA) BETWEEN P.INICIO AND P.FIM
						AND (
							SELECT TOP 1 REGISTRO FROM (
								SELECT
									CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
									CASE
										WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
										ELSE '2199-12-31 23:59:59'
									END DATA
								FROM
									PFUNC
								WHERE
									CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
									AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
							)X WHERE X.DATA >= P.INICIO
							ORDER BY X.DATA ASC
						) IS NOT NULL
						".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
						".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							
				
						
				) AS PivotSource
				PIVOT (
					MAX(BATIDA) FOR LIN IN ([1], [2], [3], [4], [5],[6],[7],[8])) AS PivotTable
				
				)TBL
				
				)XX
				
				WHERE (BAT = 4 OR BAT = 6 OR BAT = 8 )   AND INTER_OBR_HOR = '1'
						
				)ABC
				WHERE (COMPLEMENTO <> 'CORRETO')



		UNION ALL


			/***************************************************************************************************************/
			/*DENIS 18/04/2024*/					/* IntraJornada    [tipo de retorno COMPLEMENTO]  2 BATIDAS OU IMPAR      INTERVALO NÃO IDENTIFICADO                         */
			/***************************************************************************************************************/ 



			SELECT 
			CODCOLIGADA,
			CHAPA, 
			NOME,
			COD_FUNCAO,
			FUNCAO,
			SECAO_COD,
			DESC_SEC,
			DATA,
			HOR_HIST,
			'intrajornada' OCORRENCIA,

			'Intervalo não identificado'COMPLEMENTO



			, '' VALOR
			, NULL BASE
			, NULL HTRAB
			, NULL EXTRAAUTORIZADO
			, NULL COMPENSADO
			, NULL DESCANSO
			, NULL FERIADO,
			'RM' SISTEMA

			FROM (

			SELECT 
			INICIO
			,FIM
			,CODCOLIGADA
			,CHAPA
			,NOME
			,COD_FUNCAO
			,FUNCAO
			,SECAO_COD
			,DESC_SEC
			,DATA

			,[1] BATIDA_ENT_REALIZADA
			,(SELECT MIN(BATINICIO) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_ENT_DEVERIA
			,(SELECT MAX(BATFIM) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_SAI_DEVERIA

			,CASE WHEN [8] IS NULL AND [7] IS NOT NULL THEN [7]
				WHEN [7] IS NULL AND [6] IS NOT NULL THEN [6]
					WHEN [6] IS NULL AND [5] IS NOT NULL THEN [5]
						WHEN [5] IS NULL AND [4] IS NOT NULL THEN [4]
							WHEN [4] IS NULL AND [3] IS NOT NULL THEN [3]
								WHEN [3] IS NULL AND [2] IS NOT NULL THEN [2]
									WHEN [2] IS NULL THEN [1] ELSE ''
										END BATIDA_SAI_REALIZADA
								

			,[1] UM
			,[2] DOIS
			,[3] TRES
			,[4] QUATRO
			,[5] CINCO
			,[6] SEIS
			,[7] SETE
			,[8] OITO
			,HTRAB
			,CODRECEBIMENTO
			,CODTIPO
			,QTD
			,INTER_OBR
			,INTER_FRAC
			,PREVISTO
			,HOR_HIST
			FROM (	

			SELECT 
			ROW_NUMBER() OVER(PARTITION BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END), B.CHAPA ORDER BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)) LINHA,
			INICIO,
			FIM,
			A.CODCOLIGADA, 
			A.CHAPA, 
			C.NOME,
			C.CODSECAO SECAO_COD,
			D.DESCRICAO SECAO_NOME,
			D.DESCRICAO DESC_SEC,
			FU.CODIGO COD_FUNCAO,
			FU.NOME FUNCAO,
			A.DATA,
			B.BATIDA,
			(SELECT CODHORARIO FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
				(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA)) HOR_HIST,
				

			C.CODRECEBIMENTO, 
			C.CODTIPO,
			A.HTRAB,
			Q.QTD,
			Q.INTER_OBR,
			Q.INTER_FRAC,  

			(SELECT ML.INTERVALO 
					FROM 
						Z_OUTSERV_MELHORIAS3 ML 
						INNER JOIN (SELECT * FROM dbo.OUTSERV_HORARIO_HIST(A.CODCOLIGADA,A.CHAPA,A.DATA)) Z ON ML.CODHORARIO = Z.CODHORARIO AND ML.CODINDICE = IND_CALC
			)PREVISTO


			FROM AAFHTFUN A

			LEFT JOIN ABATFUN B
				ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA = (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)
					AND B.STATUS NOT IN ('T')

			LEFT JOIN PFUNC C
				ON A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA 

			INNER JOIN PSECAO D
				ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODIGO = C.CODSECAO

			INNER JOIN PAR ON PAR.COLIGADA = A.CODCOLIGADA

			LEFT JOIN PSECAOCOMPL DC (NOLOCK) ON DC.CODCOLIGADA = D.CODCOLIGADA AND DC.CODIGO = D.CODIGO

			LEFT JOIN PFUNCAO FU (NOLOCK) ON FU.CODCOLIGADA = C.CODCOLIGADA AND FU.CODIGO = C.CODFUNCAO

			LEFT JOIN QTD_BATIDAS Q ON Q.CODCOLIGADA = A.CODCOLIGADA AND Q.CHAPA = A.CHAPA AND Q.DATA = A.DATA


			WHERE 
			A.CODCOLIGADA = PAR.COLIGADA
			AND A.DATA  BETWEEN PAR.INICIO AND PAR.FIM
			AND (
				SELECT TOP 1 REGISTRO FROM (
					SELECT
						CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
						CASE
							WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
							ELSE '2199-12-31 23:59:59'
						END DATA
					FROM
						PFUNC
					WHERE
						CODCOLIGADA = C.CODCOLIGADA AND CHAPA = C.CHAPA
						AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
				)X WHERE X.DATA >= A.DATA
				ORDER BY X.DATA ASC
			) IS NOT NULL
			AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
					".str_replace('A.CODSECAO', 'C.CODSECAO', $qr_secao)."
					".str_replace('A.CODFILIAL', 'C.CODFILIAL', $where_filial)."

					AND C.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT P.codfuncao
			FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P WHERE intervalo_obrigatorio NOT IN (1))

			AND (SELECT CODHORARIO FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
			(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA)) IN (

			SELECT DISTINCT CODHORARIO
			FROM ABATHOR
			WHERE
			CODCOLIGADA = '{$_SESSION['func_coligada']}'
			AND TIPO = '4'
			AND (FIM-INICIO) <> 0
			)



			)TABELACONSULTA

			PIVOT

			(
			MAX(BATIDA)
			FOR [LINHA] IN ([1],[2],[3],[4],[5],[6],[7],[8])
			) 
			AS TABELAPIVOT

			WHERE [1] IS NOT NULL

			)X

			WHERE 
			
			/*DENIS 18/04/2024*/ (QTD = 2 OR QTD % 2 = 1)  AND INTER_OBR = '1'



			UNION ALL
			/***************************************************************************************************************/
			/* Registros britânicos (Apontamentos Manuais)    [tipo de retorno COMPLEMENTO]                                */
			/***************************************************************************************************************/ 
			 
			  SELECT
					A.CODCOLIGADA
				  , A.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , D.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , C.DESCRICAO NOMESECAO
				  , A.DATA
				  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
				  , 'registro_britanico' OCORRENCIA
				  , CONCAT(dbo.mintotime(MIN(B1.BATIDA)), ' ', dbo.mintotime(MAX(B1.BATIDA))) COMPLEMENTO
				  , 1 VALOR
					
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO,
					'RM' SISTEMA
				  
				  
			  FROM AAFHTFUN (NOLOCK) A
				  INNER JOIN PFUNC   (NOLOCK) B  ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				  INNER JOIN PSECAO  (NOLOCK) C  ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODSECAO
				  INNER JOIN PFUNCAO (NOLOCK) D  ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
				  INNER JOIN ABATFUN (NOLOCK) B1 ON B1.CODCOLIGADA = A.CODCOLIGADA AND B1.CHAPA = A.CHAPA AND ISNULL(B1.DATAREFERENCIA, B1.DATA) = A.DATA AND B1.STATUS NOT IN ('T')
				  INNER JOIN AJORHOR (NOLOCK) J1 ON J1.CODCOLIGADA = A.CODCOLIGADA AND J1.CODHORARIO = (SELECT CODHORARIO FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
							  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA))
				  LEFT  JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
			  
				  
			  WHERE
					  A.CODCOLIGADA = P.COLIGADA
				  AND A.DATA BETWEEN P.INICIO AND P.FIM
				  AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= A.DATA
						ORDER BY X.DATA ASC
					) IS NOT NULL
				  ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
				   ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
				   AND (SELECT registro_bri FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
				  
			  GROUP BY
				  A.CODCOLIGADA,
				  A.CHAPA,
				  A.DATA,
				  B.NOME,
				  B.CODSECAO,
				  C.DESCRICAO,
				  B.CODFUNCAO,
				  D.NOME
			  
			  HAVING
					  MIN(B1.BATIDA) = MIN(J1.BATINICIO)
				  AND MAX(B1.BATIDA) = MAX(J1.BATFIM)
							

			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA MENOS DE 6 MESES    [tipo de retorno COMPLEMENTO]                                           */
			/***************************************************************************************************************/ 
			 
			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , ULT_MUD_DATA DATA
				  , NULL HORARIO
				  , 'troca_menor_6_meses' OCORRENCIA
				  , CONVERT(VARCHAR(10),ULT_MUD_DATA,103) + ' ['+ULT_MUD_CODHOR+']' + ' - ' + CONVERT(VARCHAR(10),PEN_MUD_DATA,103) + ' [' + PEN_MUD_CODHOR + '] ' + CONVERT(VARCHAR(2),DATEDIFF(DAY, PEN_MUD_DATA, ULT_MUD_DATA))  COMPLEMENTO
				  , DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) VALOR
				  
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO,
				'RM' SISTEMA
			  FROM (
			  
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, B.NOME NOMEFUNCAO
					, A.CODSECAO
					, C.DESCRICAO NOMESECAO
					, (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ULT_MUD_DATA
					
					, (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA  AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ) ULT_MUD_CODHOR
				  
					  ,(SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
						  AND DTMUDANCA >
										  (SELECT 
											  CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
															ELSE MAX(DTMUDANCA) END
										   FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
											  AND DTMUDANCA <=
												  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
														  AND DTMUDANCA <> 
															  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
														   AND CODHORARIO <> 
															  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
											  AND CODHORARIO <> 
													(SELECT 
															CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
															ELSE CODHORARIO END
														FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
														(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
															  AND DTMUDANCA <> 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
															   AND CODHORARIO <> 
																  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
												 )
										  )	
						  ) PEN_MUD_DATA
	  
					  ,(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
						   (SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
							  AND DTMUDANCA >
											  (SELECT 
												  CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
																ELSE MAX(DTMUDANCA) END
											   FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
												  AND DTMUDANCA <=
													  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
															  AND DTMUDANCA <> 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
															   AND CODHORARIO <> 
																  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
												  AND CODHORARIO <> 
														(SELECT 
																CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
																ELSE CODHORARIO END
															FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																  AND DTMUDANCA <> 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																   AND CODHORARIO <> 
																	  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																		  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
													 )
											  )	
							 )
						 ) PEN_MUD_CODHOR

				FROM
					PFUNC A
					INNER JOIN PFUNCAO (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
					INNER JOIN PSECAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
					INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
			  
				WHERE
						/*A.CODSITUACAO NOT IN ('D')*/
						(
							SELECT TOP 1 REGISTRO FROM (
								SELECT
									CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
									CASE
										WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
										ELSE '2199-12-31 23:59:59'
									END DATA
								FROM
									PFUNC
								WHERE
									CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA
									AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
							)X WHERE X.DATA >= P.INICIO
							ORDER BY X.DATA ASC
						) IS NOT NULL
					AND (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) BETWEEN P.INICIO AND P.FIM
					AND (SELECT troca_menor6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
					{$qr_secao} 
					{$where_filial}
				  
			  )X
				 WHERE 
					DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) < 6 
					AND DATEDIFF(DAY, PEN_MUD_DATA, ULT_MUD_DATA) > 3
			  
			  
			  
			  
			  
			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA MENOR DE 10 DIAS    [tipo de retorno VALOR]                                                 */
			/***************************************************************************************************************/					  
			  /*******SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , DTMUDANCA DATA
				  , NULL HORARIO
				  , 'troca_menor_10_dias' OCORRENCIA
				  , NULL COMPLEMENTO
				  , (DATEDIFF(DAY, DATAALTERACAO, DTMUDANCA)) VALOR


				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO,
				'RM' SISTEMA
			
			  FROM(
				  SELECT 
						A.CODCOLIGADA 
					  , A.CHAPA
					  , B.NOME
					  , B.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , B.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , A.DTMUDANCA
					  , A.CODHORARIO
					  , A.DATAALTERACAO 
					  , (SELECT M.DTMUDANCA FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA = 
							  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA < A.DTMUDANCA) ) PEN_DTMUDANCA
					, ISNULL((SELECT M.CODHORARIO FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA = 
								  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA < A.DTMUDANCA) ),A.CODHORARIO) PEN_CODHORARIO
					  


				  FROM PFHSTHOR A
					  INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
					  LEFT  JOIN PFUNCAO (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODFUNCAO = C.CODIGO
					  LEFT  JOIN PSECAO  (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODSECAO = D.CODIGO
					  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
				  WHERE 
					  /*B.CODSITUACAO NOT IN ('D')*/
					  (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= P.INICIO
						ORDER BY X.DATA ASC
					) IS NOT NULL
				  AND A.DTMUDANCA BETWEEN P.INICIO AND P.FIM
					AND (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
					".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
					   ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
			  )X 
				WHERE (DATEDIFF(DAY, DATAALTERACAO, DTMUDANCA)) = 3
				AND CODHORARIO <> PEN_CODHORARIO
			****/
			SELECT
				CODCOLIGADA
				, CHAPA
				, NOME
				, COD_FCO CODFUNCAO
				, FUNCAO NOMEFUNCAO
				, COD_SEC CODSECAO
				, SEC NOMESECAO
				, DTMUDANCA
				, DESCRICAO
				,'troca_menor_10_dias' OCORRENCIA
				, NULL COMPLEMENTO
				, DATEDIFF(DAY,DTMUDANCA_ANTERIOR,DTMUDANCA) VALOR
				, NULL BASE
				, NULL HTRAB
				, NULL EXTRAAUTORIZADO
				, NULL COMPENSADO
				, NULL DESCANSO
				, NULL FERIADO
				,'RM' SISTEMA  
			FROM(
			SELECT
				B.CODCOLIGADA,
				B.CHAPA,
				B.NOME,
				D.CODIGO COD_SEC,
				D.DESCRICAO SEC,
				E.CODIGO COD_FCO,
				E.NOME FUNCAO,
				A.DTMUDANCA,
				ISNULL(LAG(A.DTMUDANCA) OVER (PARTITION BY A.CODCOLIGADA, A.CHAPA ORDER BY A.CODCOLIGADA, A.CHAPA, A.DTMUDANCA),A.DTMUDANCA) AS DTMUDANCA_ANTERIOR,
				ISNULL(LAG(C.DESCRICAO) OVER (PARTITION BY A.CODCOLIGADA, A.CHAPA ORDER BY A.CODCOLIGADA, A.CHAPA, A.DTMUDANCA),A.DTMUDANCA) AS HORARIO_ANTERIOR,
				A.CODHORARIO,
				C.DESCRICAO,
				ROW_NUMBER() OVER (PARTITION BY A.CODCOLIGADA, A.CHAPA ORDER BY A.CODCOLIGADA, A.CHAPA, A.DTMUDANCA) AS LINHA
				,P.INICIO
				,P.FIM
			FROM 
				PFHSTHOR A (NOLOCK)
				LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				LEFT JOIN AHORARIO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODHORARIO
				LEFT JOIN PSECAO D (NOLOCK) ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				INNER JOIN PAR P ON 1=1
				LEFT  JOIN PFUNCAO (NOLOCK) E ON B.CODCOLIGADA = E.CODCOLIGADA AND B.CODFUNCAO = E.CODIGO
				
			WHERE
				A.CODCOLIGADA = P.COLIGADA
				/*AND B.CODSITUACAO NOT IN ('D')*/
				AND (
                    SELECT TOP 1 REGISTRO FROM (
                        SELECT
                            CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
                            CASE
                                WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
                                ELSE '2199-12-31 23:59:59'
                            END DATA
                        FROM
                            PFUNC
                        WHERE
                            CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
							AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
                    )X WHERE X.DATA >= P.INICIO
                    ORDER BY X.DATA ASC
                ) IS NOT NULL
				AND A.DTMUDANCA BETWEEN P.INICIO AND P.FIM
				AND (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
				".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
				".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."

			)X

			WHERE
					DATEDIFF(DAY,DTMUDANCA_ANTERIOR,DTMUDANCA) = 3
				AND DTMUDANCA BETWEEN INICIO AND FIM
				AND HORARIO_ANTERIOR <> CODHORARIO
			  
			  
			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA SEM ANEXO DO TERMO ADITIVO    [tipo de retorno VALOR]                                       */
			/***************************************************************************************************************/
						
			  SELECT 
					B.CODCOLIGADA
				  , B.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , C.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , D.DESCRICAO NOMESECAO
				  , A.datamudanca DATA
				  , NULL HORARIO
				  , 'pendente_termo_aditivo' OCORRENCIA
				  , NULL COMPLEMENTO
				  , A.id VALOR
				  
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO,
				'RM' SISTEMA
			  FROM 
				  ".DBPORTAL_BANCO."..zcrmportal_escala A
				  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
				  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				  INNER JOIN PAR              P ON P.COLIGADA = A.coligada
			  WHERE
					  CAST(A.documento AS VARCHAR(10)) IS NULL
				  AND A.datamudanca BETWEEN P.INICIO AND P.FIM
				  AND (
					SELECT TOP 1 REGISTRO FROM (
						SELECT
							CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
							CASE
								WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
								ELSE '2199-12-31 23:59:59'
							END DATA
						FROM
							PFUNC
						WHERE
							CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
							AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
					)X WHERE X.DATA >= P.INICIO
					ORDER BY X.DATA ASC
				) IS NOT NULL
				  AND (SELECT req_troca FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
				   ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)." 
				 
				 ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
				 
				  
			UNION ALL
			/***************************************************************************************************************/
			/* SOBREAVISO     [tipo de retorno VALOR]                                                                      */
			/***************************************************************************************************************/
			  
			  SELECT 
					B.CODCOLIGADA
				  , B.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , C.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , D.DESCRICAO NOMESECAO
				  , CONVERT(DATE, A.dtcad, 3) DATA
				  , NULL HORARIO
				  , 'sobreaviso' OCORRENCIA
				, NULL COMPLEMENTO
				  , SUM(A.HORAS) VALOR

				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO,
				'RM' SISTEMA
				  
				  --,DATEADD(DAY,6,P.INICIO) INICIO
				  --,DATEADD(DAY,6,P.FIM) FIM
				  
			  FROM 
				  ".DBPORTAL_BANCO."..zcrmportal_substituicao_sobreaviso (NOLOCK) A
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  INNER JOIN PAR              P ON A.COLIGADA = P.COLIGADA
				  
			  WHERE 
					A.dtcad BETWEEN DATEADD(DAY,6,P.INICIO) AND DATEADD(DAY,6,P.FIM)
					AND (
						SELECT TOP 1 REGISTRO FROM (
							SELECT
								CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
								CASE
									WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
									ELSE '2199-12-31 23:59:59'
								END DATA
							FROM
								PFUNC
							WHERE
								CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
								AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
						)X WHERE X.DATA >= P.INICIO
						ORDER BY X.DATA ASC
					) IS NOT NULL
				  AND A.situacao = 2
				  AND (SELECT sobreaviso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
				   ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
					".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
				  
			  GROUP BY 
				  B.CODCOLIGADA,
				  B.CHAPA,
				  B.NOME,
				  B.CODFUNCAO,
				  C.NOME,
				  B.CODSECAO,
				  D.DESCRICAO,
				  CONVERT(DATE, A.dtcad, 3)
				
			  HAVING 
				  SUM(A.horas) > 5760
			  
			  
							
			UNION ALL
			/***************************************************************************************************************/
			/* Excesso de Abono Gestor (Superior a 5 dias consecutivos)    [tipo de retorno COMPLEMENTO]                   */
			/***************************************************************************************************************/ 

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , DATA
				  , NULL HORARIO
				  , 'excesso_abono_gestor' OCORRENCIA
				  , CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)   COMPLEMENTO
				  , (UM + DOIS + TRES + QUATRO + CINCO) VALOR
				  
				  , BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO,
				'RM' SISTEMA
			  FROM(
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , B.NOME
					  , B.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , B.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , A.DATA
					  , P.ABONO
					  , A.BASE
					, A.HTRAB
					, A.EXTRAAUTORIZADO
					, A.COMPENSADO
					, A.DESCANSO
					, A.FERIADO
			  
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.CODABONO = P.ABONO AND A.BASE > 0)>0 THEN 1 ELSE 0 END)UM
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 1) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) DOIS
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 2) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) TRES
																								
				  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 3) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) QUATRO                                                                            
				  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 4) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) CINCO                                                                           
				  , (SELECT DATA 
					FROM (
					  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
					  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
					  )X WHERE seq = 4) DATAFINAL
					  
				  FROM AAFHTFUN (NOLOCK) A
					  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  
				  WHERE
					  A.DATA BETWEEN P.INICIO AND P.FIM
					  AND (
							SELECT TOP 1 REGISTRO FROM (
								SELECT
									CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
									CASE
										WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
										ELSE '2199-12-31 23:59:59'
									END DATA
								FROM
									PFUNC
								WHERE
									CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
									AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
							)X WHERE X.DATA >= P.INICIO
							ORDER BY X.DATA ASC
						) IS NOT NULL
					  AND (SELECT excesso_gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
					  ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
					  ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
					  
			  
			  )X
			   WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1


			  
			UNION ALL
			/***************************************************************************************************************/
			/* Trabalho superior à 6 (seis) dias consecutivos sem folga  [tipo de retorno COMPLEMENTO]                     */
			/***************************************************************************************************************/

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , DATA
				  , HORARIO
				  , 'trabalho_6dias' OCORRENCIA
				  , CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)  COMPLEMENTO
				  , (UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) VALOR
				
					, BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO,
				'RM' SISTEMA
			  FROM(
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , B.NOME
					  , B.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , B.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , A.DATA
					  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.HTRAB >0)UM
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.HTRAB >0)DOIS
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.HTRAB >0)TRES
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.HTRAB >0)QUATRO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.HTRAB >0)CINCO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-5 = B.DATA AND B.HTRAB >0)SEIS
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)SETE
					  
					  ,(SELECT B.DATA FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)DATAFINAL
					  
					  , A.BASE
					, A.HTRAB
					, A.EXTRAAUTORIZADO
					, A.COMPENSADO
					, A.DESCANSO
					, A.FERIADO
					  
				  FROM AAFHTFUN (NOLOCK) A
					  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  
				  WHERE
					  A.DATA BETWEEN P.INICIO AND P.FIM
					  AND (
							SELECT TOP 1 REGISTRO FROM (
								SELECT
									CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
									CASE
										WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
										ELSE '2199-12-31 23:59:59'
									END DATA
								FROM
									PFUNC
								WHERE
									CODCOLIGADA = B.CODCOLIGADA AND CHAPA = B.CHAPA
									AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
							)X WHERE X.DATA >= P.INICIO
							ORDER BY X.DATA ASC
						) IS NOT NULL
					  AND (SELECT trabalho_sup6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
					   ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
						".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
					  
					  
			  )X
				WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1 AND SEIS = 1 AND SETE = 1


/*************************************************************************************
				 * ocorrencias de outros sistema
				 ************************************************************************************/		
					UNION ALL

					SELECT 
						CAST(a.codcoligada AS INT) CODCOLIGADA,
						a.chapa COLLATE Latin1_General_CI_AS CHAPA,
						b.NOME,
						b.CODFUNCAO,
						c.NOME NOMEFUNCAO,
						b.CODSECAO,
						d.DESCRICAO NOMESECAO,
						a.data DATA,
						NULL HORARIO,
						a.ocorrencia OCORRENCIA,
						a.resultado COLLATE Latin1_General_CI_AS COMPLEMENTO,
						NULL VALOR
						, NULL BASE
						, NULL HTRAB
						, NULL EXTRAAUTORIZADO
						, NULL COMPENSADO
						, NULL DESCANSO
						, NULL FERIADO
						, a.sistema SISTEMA
					FROM 
						".DBPORTAL_BANCO."..zcrmportal_ocorrencia a (NOLOCK)
						INNER JOIN PFUNC b (NOLOCK) ON b.CHAPA COLLATE Latin1_General_CI_AS = a.chapa AND b.CODCOLIGADA = a.codcoligada COLLATE Latin1_General_CI_AS
						INNER JOIN PFUNCAO c (NOLOCK) ON c.CODIGO = b.CODFUNCAO AND c.CODCOLIGADA = b.CODCOLIGADA
						INNER JOIN PSECAO d (NOLOCK) ON d.CODIGO = b.CODSECAO AND d.CODCOLIGADA = b.CODCOLIGADA
						INNER JOIN PAR p ON p.COLIGADA = CAST(a.codcoligada AS INT) 
					WHERE 
							a.codcoligada = {$this->coligada} 
						AND a.sistema NOT IN ('RM')
						AND a.data BETWEEN p.INICIO AND p.FIM
						AND (
							SELECT TOP 1 REGISTRO FROM (
								SELECT
									CONCAT(CODCOLIGADA,'-',CHAPA) REGISTRO,
									CASE
										WHEN DATADEMISSAO IS NOT NULL AND CODSITUACAO = 'D' THEN DATADEMISSAO
										ELSE '2199-12-31 23:59:59'
									END DATA
								FROM
									PFUNC
								WHERE
									CODCOLIGADA = b.CODCOLIGADA AND CHAPA = b.CHAPA
									AND ISNULL(TIPODEMISSAO, '0') NOT IN ('5', '6')
							)X WHERE X.DATA >= p.INICIO
							ORDER BY X.DATA ASC
						) IS NOT NULL
						".str_replace('A.CODSECAO', 'b.CODSECAO', $qr_secao)."
						".(($ja_justificados == NULL) ? ' AND a.codmotivo IS NULL ' : '  ')."
						AND 1 = 
							CASE
								WHEN a.ocorrencia = 'excesso_abono_gestor' THEN (SELECT excesso_gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'extra_permitido' THEN (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'extra' THEN 1
								WHEN a.ocorrencia = 'jornada' THEN 1
								WHEN a.ocorrencia = 'trabalho_dsr_folga' THEN 1
								WHEN a.ocorrencia = 'trabalho_dsr_folga_descanso' THEN (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'trabalho_ferias_afastamento' THEN 1
								WHEN a.ocorrencia = 'registro_manual' THEN (SELECT registro_manual FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'trabalho_6dias' THEN (SELECT trabalho_sup6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'interjornada' THEN (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'registro_britanico' THEN (SELECT registro_bri FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'sobreaviso' THEN (SELECT sobreaviso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'troca_menor_6_meses' THEN (SELECT troca_menor6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'troca_menor_10_dias' THEN (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								WHEN a.ocorrencia = 'pendente_termo_aditivo' THEN (SELECT req_troca FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
								ELSE 1
							END

		)X	

		

		[WHERE]
		 

		ORDER BY
		X.NOME,
		X.DATA,
		X.OCORRENCIA
";


// MONTA SQL PARA GRAVAR LOG DE VISUALIZAÇÃO
$SELECT = "

INSERT INTO ".DBPORTAL_BANCO."..zcrmportal_ocorrencia 

SELECT
DISTINCT
X.CODCOLIGADA,
X.CHAPA,
X.DATA,
NULL,
NULL,
1,
GETDATE(),
X.OCORRENCIA,
NULL,
NULL,
NULL,
".((!$perfil_rh) ? "1" : "NULL")."
X.SISTEMA,
X.VALOR,
NULL,
X.COMPLEMENTO,
X.FERIADO

";
$WHERE = " WHERE (SELECT COUNT(*) FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia  WHERE codcoligada = X.CODCOLIGADA AND chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND data = X.DATA AND ocorrencia = X.OCORRENCIA) <= 0

GROUP BY
	   X.NOME,
	   X.CODCOLIGADA,
	X.CHAPA,
	X.DATA,
	X.OCORRENCIA,
	X.SISTEMA,
	X.VALOR,
	X.COMPLEMENTO,
	X.FERIADO
";

$array_de = array('[SELECT]', '[WHERE]');
$array_para = array($SELECT, $WHERE);
$query_log = str_replace($array_de, $array_para, $query);
// grava log somente se não for perfil de RH
//echo "<textarea>".$query_log."</textarea>";
//exit;

//echo '<pre>';
//echo $query_log;
//exit();
if(!$perfil_rh) $this->dbrm->query($query_log);

// MONTA SQL PARA VISUALIZAÇÃO DAS OCORRÊNCIAS
$SELECT = "
SELECT
	DISTINCT
	X.*,
	(SELECT Z.codmotivo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) codmotivo,
	(SELECT Z.descricao_motivo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) descricao_motivo,
	(SELECT Z.ocorrencia FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) log_ocorrencia,
	(SELECT Z.observacao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) observacao,
	(SELECT Z.gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) gestor,
	(SELECT Z.id_anexo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) id_anexo,
	(SELECT Y.file_name FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_anexo Y (NOLOCK) WHERE Y.id = (SELECT Z.id_anexo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA)) file_name,

	(
	  SELECT max(CAST(BB.descricao AS VARCHAR)) FROM ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func AA 
		INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motivos BB ON AA.justificativa = BB.id AND AA.coligada = BB.codcoligada WHERE AA.coligada = X.codcoligada AND AA.dtponto = X.data AND AA.chapa = X.chapa
	) justificativa_extra,
	(
		SELECT TOP 1 ISNULL(HH.DESCRICAO, HH3.DESCRICAO) FROM (
		SELECT
		    TOP 1 
		    HB.DESCRICAO
		FROM
		    PFHSTSIT HA (NOLOCK)
		    INNER JOIN PCODSITUACAO HB (NOLOCK) ON HB.CODCLIENTE = HA.NOVASITUACAO
		WHERE
		        HA.CODCOLIGADA = X.CODCOLIGADA
		    AND HA.CHAPA = X.CHAPA
		    AND (
		        HA.DATAMUDANCA <= X.DATA
		    )
		ORDER BY
		    DATAMUDANCA DESC
		)HH
		RIGHT JOIN PFUNC HH2 (NOLOCK) ON HH2.CODCOLIGADA = X.CODCOLIGADA 
		INNER JOIN PCODSITUACAO HH3 (NOLOCK) ON HH3.CODINTERNO = HH2.CODSITUACAO
		WHERE 
			HH2.CHAPA = X.CHAPA
		AND HH2.CODCOLIGADA = X.CODCOLIGADA
	) CODSITUACAO,
	(
		SELECT 
			MAX(G.NOME)
		FROM
			GCCUSTO G
		WHERE
				G.CODCCUSTO = SUBSTRING(X.CODSECAO,5,5)
			AND G.CODCOLIGADA = X.CODCOLIGADA
	) NOMECUSTO,
	SUBSTRING(X.CODSECAO,5,5) CODCUSTO
	
";
$WHERE = " {$where_ja_justificados} ";

$array_de = array('[SELECT]', '[WHERE]');
$array_para = array($SELECT, $WHERE);
$query = str_replace($array_de, $array_para, $query);

// if($_SESSION['log_id'] == 1){
// echo '<pre>';
// echo $query;
// exit();
// }
		
        $result = $this->dbrm->query($query);

	// 	echo '<pre>';
	// print_r($result->getResultArray());
	// 	exit();
		if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

		} catch (\Exception | \Error $e) {
			return false;
		}

    }
	public function ListarOcorrenciaWorflow($dados){
		
        $data_inicio = $dados['data_inicio'] ?? null;
        $data_fim = $dados['data_fim'] ?? null;
        $coligada = $dados['coligada'];
    	$abono = '029';
		$ja_justificados = 1;
		$data_inicio_ciclo = somarDias($data_inicio, '-7');

		$where_ja_justificados = ($ja_justificados == NULL) ? " WHERE (SELECT Z.codmotivo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) IS NULL " : "";
        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer($dados['chapa'], $coligada);

		$where_filial = "";

        $qr_secao = " AND 1 = 2 ";
        if($Secoes){
            $qr_secao = "";
            foreach($Secoes as $key =>$CodSecao){
                $qr_secao .= "'{$CodSecao['codsecao']}',";
            }
            $qr_secao = " AND A.CODSECAO IN (".rtrim($qr_secao, ',').") ";
        }

     
	
        // filtro por seção
        if(is_array($dados['secao'] ?? "")){
            $qr_secao = "";
            foreach($dados['secao'] as $key => $CodSecao){
                $qr_secao .= "'{$CodSecao}',";
            }
            $qr_secao = " AND A.CODSECAO IN (".rtrim($qr_secao, ',').") ";
        }

       
		$query = "
            
					WITH 

					PAR AS 
						(
						SELECT 
							COLIGADA = '{$this->coligada}'
							, INICIO   =  '{$data_inicio}'
							, FIM      = '{$data_fim}'
							, HORARIO  = '020,	0012,	0013,	0005,	0033,	Cons.010,	0004,	0055,	006,	0017,	0011,	007,	07,	0202,	0203,	015,	TESTE,	019,	Cons.009,	0007,	0208,	0027,	0023,	108,	110,	092,	104,	084,	118,	0207,	0204,	113,	095,	0032,	03,	100,	0048,	0041,	0042,	0043,	0044,	0026,	0040'        
							, ABONO    = '{$abono}'
						),

						QTD_BATIDAS AS
(

SELECT 
	A.CODCOLIGADA
	,A.CHAPA
	,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
	,COUNT(A.BATIDA)QTD
	/*DENIS 17/04/2024*/,ISNULL((SELECT P.intervalo_obrigatorio
	FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
	WHERE P.coligada = A.CODCOLIGADA
	      AND P.codigo =  
	      
	      (SELECT  TOP 1 CODHORARIO
           FROM PFHSTHOR X
             WHERE    
                   X.CODCOLIGADA = A.CODCOLIGADA 
                   AND X.CHAPA = A.CHAPA
                   AND X.DTMUDANCA <= ISNULL(A.DATAREFERENCIA,A.DATA) ORDER BY X.DTMUDANCA DESC ) COLLATE Latin1_General_CI_AS
	      
	      ),0)INTER_OBR	 
	      
/*DENIS 17/04/2024*/,ISNULL((SELECT  P.intervalo_fracionado
	FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
	WHERE P.coligada = A.CODCOLIGADA
	      AND P.codigo =  
	      
	      (SELECT  TOP 1 CODHORARIO
           FROM PFHSTHOR X
             WHERE    
                   X.CODCOLIGADA = A.CODCOLIGADA 
                   AND X.CHAPA = A.CHAPA
                   AND X.DTMUDANCA <= ISNULL(A.DATAREFERENCIA,A.DATA) ORDER BY X.DTMUDANCA DESC ) COLLATE Latin1_General_CI_AS
	      
	      ),0)INTER_FRAC   
	
FROM 	
	ABATFUN A 
	LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
WHERE 
	A.CODCOLIGADA = P.COLIGADA
	AND A.DATA BETWEEN P.INICIO AND P.FIM
	AND A.STATUS NOT IN ('T')
 GROUP BY
 	 A.CODCOLIGADA
 	,A.CHAPA
	,ISNULL(A.DATAREFERENCIA,A.DATA)
),


PREVISTO AS (
SELECT
		A.CODCOLIGADA	
	,A.CHAPA
	,A.DATAREFERENCIA DATA
	,SUM(((CASE WHEN B.TIPOOCORRENCIA IN ('AREF','ERA','ERT') THEN (DATEDIFF(MINUTE,A.INICIO,A.FIM)) ELSE NULL END)))PREVISTO
	
	
FROM 
	AOCORRENCIACALCULADA A (NOLOCK)
	INNER JOIN ATIPOOCORRENCIA B (NOLOCK) ON A.TIPOOCORRENCIA = B.TIPOOCORRENCIA
	LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA 
	
WHERE A.CODCOLIGADA = P.COLIGADA
		AND A.DATAREFERENCIA BETWEEN P.INICIO AND P.FIM  
		AND B.TIPOOCORRENCIA IN ('AREF','ERA','ERT')

GROUP BY
		A.CODCOLIGADA
	,A.CHAPA
	,A.DATAREFERENCIA
)


					/*SELECT
						X.CODCOLIGADA,
						X.CHAPA,
						X.NOME,
						X.CODFUNCAO,
						X.NOMEFUNCAO,
						X.CODSECAO,
						X.NOMESECAO,
						X.DATA,
						X.OCORRENCIA,
						X.VALOR,
						X.COMPLEMENTO,
					  Z.codmotivo,
					  Z.descricao_motivo,
					  Z.ocorrencia log_ocorrencia,
					  Z.observacao,
					  Z.gestor*/

					  [SELECT]


					FROM (





						/***************************************************************************************************************/
						/* Horas Extras acima do permitido  NÃO MOTORISTA                                                                           */
						/***************************************************************************************************************/
							SELECT 
								DISTINCT
								* ,
								'RM' SISTEMA
							FROM (
								SELECT
									  A.CODCOLIGADA
									, A.CHAPA
									, A.NOME
									, A.CODFUNCAO
									, C.NOME NOMEFUNCAO
									, A.CODSECAO
									, D.DESCRICAO NOMESECAO
									, B.DATA
									, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
									, 'extra_permitido' OCORRENCIA
									 
									
									
									, CASE 
										--WHEN B.FERIADO = 0 THEN CONCAT(dbo.MINTOTIME(ISNULL(Z.HEXTRA_DIARIA,0)),'D') ELSE CONCAT(dbo.MINTOTIME(ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0)),'F') END
										WHEN B.FERIADO = 0 THEN 'D' ELSE ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN 'F' ELSE 'FP' END) ,0) END
	
	
									
									
									
									
									COMPLEMENTO
	/**/							, (CASE WHEN B.FERIADO = 0 AND EXTRAEXECUTADO > ISNULL(Z.HEXTRA_DIARIA,0) THEN EXTRAEXECUTADO
	/**/									WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
	/**/									WHEN B.FERIADO > 0 AND HTRAB > 0
	/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
	/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
	/**/										AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) = 0 THEN HTRAB - ISNULL(Z.HEXTRA_DIARIA,0)
	/**/										
	/**/									WHEN B.FERIADO > 0 AND HTRAB > 0 /*Analisando se trabalhou em feriado se a jornada prevista e menor que a jornada trabalhada*/
	/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
	/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
	/**/										--AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) > 0 AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN (HTRAB - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
												/*TIAGO*/AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) > 0 AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN ((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN HTRAB ELSE EXTRAEXECUTADO END) - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
	/**/									ELSE 0 END) 
	- ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0)

- ISNULL((CASE WHEN B.FERIADO = 0 THEN ISNULL(Z.HEXTRA_DIARIA,0) ELSE ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN 0 ELSE extra_feriado_parcial END) ,0) END) ,0)
VALOR
								
									, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, B.FERIADO
								FROM 
									PFUNC (NOLOCK) A
										LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
										INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
										INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
										/*Denis*/LEFT JOIN GFERIADO CAL ON CAL.CODCALENDARIO = D.CODCALENDARIO AND CAL.DIAFERIADO = B.DATA
										INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
	/**/								LEFT  JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario (NOLOCK) P ON P.COLIGADA = A.CODCOLIGADA 
	/**/										AND P.CODIGO COLLATE Latin1_General_CI_AS = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
	/**/																						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
	
										LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
	
										LEFT JOIN Z_OUTSERV_MELHORIAS3 Z ON Z.CODCOLIGADA = A.CODCOLIGADA AND Z.CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
								
										LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = B.DATA	

										INNER JOIN (
											SELECT MAX(CODINDICE) CODINDICE, CODHORARIO, CODCOLIGADA FROM Z_OUTSERV_MELHORIAS3 GROUP BY CODHORARIO, CODCOLIGADA
										) E2 ON E2.CODHORARIO = AH.CODIGO AND E2.CODCOLIGADA = AH.CODCOLIGADA
								
								WHERE
										B.DATA BETWEEN M.INICIO AND M.FIM
	
										/**/--AND Z.CODINDICE = (CASE WHEN INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) THEN (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) ELSE (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))) END) -1
										AND Z.CODINDICE = dbo.CalculoIndiceHorarioV2(DATEDIFF(DAY, AH.DATABASEHOR, B.DATA), (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)), E2.CODINDICE) 
		
									{$qr_secao} 
									{$where_filial}
									
	/**/							AND (CASE WHEN B.FERIADO = 0 AND EXTRAEXECUTADO > ISNULL(Z.HEXTRA_DIARIA,0) THEN EXTRAEXECUTADO
	/**/									WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
	/**/									WHEN B.FERIADO > 0 AND HTRAB > 0
	/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
	/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
	/**/										AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) = 0 THEN HTRAB - ISNULL(Z.HEXTRA_DIARIA,0)
	/**/										
	/**/									WHEN B.FERIADO > 0 AND HTRAB > 0 /*Analisando se trabalhou em feriado se a jornada prevista e menor que a jornada trabalhada*/
	/**/										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
	/**/															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
	/**/										--AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) > 0 AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN (HTRAB - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
									/*TIAGO*/	AND ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) > 0 AND HTRAB > ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0) THEN ((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN HTRAB ELSE EXTRAEXECUTADO END) - ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN P.extra_feriado ELSE extra_feriado_parcial END) ,0))
	/**/									ELSE 0 END) 
	- ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND ISNULL(OBH.ATITUDE,0) = 1),0)
- ISNULL((CASE WHEN B.FERIADO = 0 THEN ISNULL(Z.HEXTRA_DIARIA,0) ELSE ISNULL((CASE WHEN CAL.HORAINICIO =0 AND CAL.HORAFINAL >= 1439 THEN 0 ELSE extra_feriado_parcial END) ,0) END),0)
> 0

	
										   AND (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
							)XX
								WHERE XX.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = XX.CODCOLIGADA AND dtdel IS NULL)
							 /*WHERE XX.HORARIO COLLATE Latin1_General_CI_AS NOT IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(escala_especial,0) = 1  )*/
	
						UNION ALL
	
						/***************************************************************************************************************/
						/* Horas Extras acima do permitido  MOTORISTA                                                                           */
						/***************************************************************************************************************/
						SELECT 
						DISTINCT
						* ,
						'RM' SISTEMA
					FROM (
						SELECT
							  A.CODCOLIGADA
							, A.CHAPA
							, A.NOME
							, A.CODFUNCAO
							, C.NOME NOMEFUNCAO
							, A.CODSECAO
							, D.DESCRICAO NOMESECAO
							, B.DATA
							, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
							, 'extra_permitido' OCORRENCIA
	
							, 'D' COMPLEMENTO
	
	/**/							, (EXTRAEXECUTADO - ISNULL(MT.limite_extra,0)) VALOR
						
							, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, B.FERIADO
						FROM 
							PFUNC (NOLOCK) A
								LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
								INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
								INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
								/*Denis*/LEFT JOIN GFERIADO CAL ON CAL.CODCALENDARIO = D.CODCALENDARIO AND CAL.DIAFERIADO = B.DATA
								INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
	/**/								LEFT  JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario (NOLOCK) P ON P.COLIGADA = A.CODCOLIGADA 
	/**/										AND P.CODIGO COLLATE Latin1_General_CI_AS = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
	/**/																						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
	
								LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
	
								LEFT JOIN Z_OUTSERV_MELHORIAS3 Z ON Z.CODCOLIGADA = A.CODCOLIGADA AND Z.CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
						
								LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = B.DATA
								INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista MT ON MT.codcoligada = A.CODCOLIGADA AND MT.dtdel IS NULL AND MT.codfuncao = A.CODFUNCAO COLLATE Latin1_General_CI_AS
						
						WHERE
								B.DATA BETWEEN M.INICIO AND M.FIM
							{$qr_secao} 
							{$where_filial}
	
							AND EXTRAEXECUTADO > ISNULL(MT.limite_extra,0)
							AND ISNULL(EXTRAEXECUTADO,0) > 0
	
	
								   AND (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
					)XX

					UNION ALL

					/***************************************************************************************************************/
					/* Horas Extras em escalas específicas (Zero HE)                                                               */
					/***************************************************************************************************************/
						/* Horários 12x36 */
						SELECT
							  A.CODCOLIGADA
							, A.CHAPA
							, A.NOME
							, A.CODFUNCAO
							, C.NOME NOMEFUNCAO
							, A.CODSECAO
							, D.DESCRICAO NOMESECAO
							, B.DATA
							, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
							, 'extra' OCORRENCIA
							, NULL COMPLEMENTO
							, (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
								   ELSE 0 END) VALOR
							
							, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO ,
							'RM' SISTEMA
						FROM 
							PFUNC (NOLOCK) A
								LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
								INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
								INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
								INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
						
						WHERE
								B.DATA BETWEEN M.INICIO AND M.FIM
							 {$qr_secao} 
							 {$where_filial}
							AND (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
								   ELSE 0 END) > 0
							AND (SELECT extra_especial FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
						
						UNION ALL
						
						/* Horários de Revezamento */
						SELECT 
							* ,
							'RM' SISTEMA
						FROM (
						  
							SELECT
								  A.CODCOLIGADA
								, A.CHAPA
								, A.NOME
								, A.CODFUNCAO
								, C.NOME NOMEFUNCAO
								, A.CODSECAO
								, D.DESCRICAO NOMESECAO
								, B.DATA
								, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
								, 'extra' OCORRENCIA
								, NULL COMPLEMENTO
								, (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
										WHEN FERIADO > 0 AND HTRAB > 0
										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
															 THEN HTRAB
										ELSE 0 END) - (SELECT SUM(VALOR) FROM ABANCOHORFUNDETALHE BB WHERE B.CODCOLIGADA = BB.CODCOLIGADA AND B.CHAPA = BB.CHAPA AND B.DATA = BB.DATA AND BB.CODEVENTO NOT IN ('001', '002')) VALOR
								
								, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
							FROM 
								PFUNC (NOLOCK) A
									LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
									INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
									INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
									INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							
							WHERE
									B.DATA BETWEEN M.INICIO AND M.FIM
								{$qr_secao}
								{$where_filial}
								AND (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
										WHEN FERIADO > 0 AND HTRAB > 0
										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
															 THEN HTRAB
										ELSE 0 END) - (SELECT SUM(VALOR) FROM ABANCOHORFUNDETALHE BB WHERE B.CODCOLIGADA = BB.CODCOLIGADA AND B.CHAPA = BB.CHAPA AND B.DATA = BB.DATA AND BB.CODEVENTO NOT IN ('001', '002')) > 0
								AND (SELECT extra_especial FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
						)XX
						 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(escala_especial,0) = 1 )

						 UNION ALL
						 /***************************************************************************************************************/
						 /* Jornada Diária acima de 11 e 10 horas NÃO MOTORISTA                                                                  */
						 /***************************************************************************************************************/
						 
						 
							 SELECT 
								 DISTINCT
								 *,
								 'RM' SISTEMA 
							 FROM(
								 SELECT
	 
									   A.CODCOLIGADA
									 , A.CHAPA
									 , A.NOME
									 , A.CODFUNCAO
									 , C.NOME NOMEFUNCAO
									 , A.CODSECAO
									 , D.DESCRICAO NOMESECAO
									 , B.DATA
									 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
											 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
									 , 'jornada' OCORRENCIA
									 
									 ,
	 
									 /*
									 CONCAT(
										 'Indice ',
										 Z.CODINDICE,
										 ', previsto ',
										 dbo.mintotime(BASE),
										 ' + limite extra ',
										 dbo.mintotime(HEXTRA_DIARIA),
										 ' = ',
										 dbo.mintotime(BASE + HEXTRA_DIARIA),
										 ', realizado ',
										 dbo.mintotime(HTRAB),
										 ', excedido ',
										 dbo.mintotime(HTRAB - (BASE + HEXTRA_DIARIA))
									 )
									 */
									 CONCAT(
										 'excedido ',
										 dbo.mintotime(HTRAB - (BASE + HEXTRA_DIARIA))
									 )
	 
									 
									 COMPLEMENTO
									 
									 , B.HTRAB - ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0) VALOR
									 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
								 FROM 
									 PFUNC (NOLOCK) A 
										 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
										 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
										 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
										 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
										 LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
										 LEFT JOIN Z_OUTSERV_MELHORIAS3 Z ON Z.CODCOLIGADA = A.CODCOLIGADA AND Z.CODHORARIO = AH.CODIGO
										 LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = B.DATA
								 
								 WHERE
										 B.DATA BETWEEN M.INICIO AND M.FIM
	 
										 /**/--AND Z.CODINDICE = (CASE WHEN INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) THEN (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) ELSE (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))) END) -1
										 AND Z.CODINDICE = dbo.CalculoIndiceHorarioV2(DATEDIFF(DAY, AH.DATABASEHOR, B.DATA), (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)), E2.CODINDICE) 
		 
									 --AND B.HTRAB > 600
									 AND HTRAB > (BASE + HEXTRA_DIARIA + ISNULL(TOLERANCIADIA,0))- ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0)
									 AND (HTRAB - ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = B.DATA AND OBH.ATITUDE = 1),0)) > BASE + HEXTRA_DIARIA
									 AND B.HTRAB > (Z.QTD2 + Z.HEXTRA_DIARIA)
									 AND (SELECT excesso_jornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
									 AND B.BASE <= 540
									  {$qr_secao}
									  {$where_filial}
								 )x 
								 
								 WHERE x.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = x.CODCOLIGADA AND dtdel IS NULL)
								 
								 
								 
								 /*where  
									 (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA <= x.DATA)) = 0*/
						 
								 UNION ALL
								 /***************************************************************************************************************/
								 /* Jornada Diária acima de 11 e 10 horas MOTORISTA                                                                  */
								 /***************************************************************************************************************/
								 
								 
									 SELECT 
										 DISTINCT
										 *,
										 'RM' SISTEMA 
									 FROM(
										 SELECT
			 
												 A.CODCOLIGADA
											 , A.CHAPA
											 , A.NOME
											 , A.CODFUNCAO
											 , C.NOME NOMEFUNCAO
											 , A.CODSECAO
											 , D.DESCRICAO NOMESECAO
											 , B.DATA
											 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
											 , 'jornada' OCORRENCIA
											 
											 ,
											 CONCAT(
												 'excedido ',
												 dbo.mintotime(HTRAB - ISNULL(MT.limite_jornada,0))
											 )
			 
											 
											 COMPLEMENTO
											 , B.HTRAB VALOR
											 
											 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
										 FROM 
											 PFUNC (NOLOCK) A 
												 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
												 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
												 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
												 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
												 LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA))
												 LEFT JOIN Z_OUTSERV_MELHORIAS3 Z ON Z.CODCOLIGADA = A.CODCOLIGADA AND Z.CODHORARIO = AH.CODIGO
												 LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = B.DATA
												 INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista MT ON MT.codcoligada = A.CODCOLIGADA AND MT.dtdel IS NULL AND MT.codfuncao = A.CODFUNCAO COLLATE Latin1_General_CI_AS
										 
										 WHERE
												 B.DATA BETWEEN M.INICIO AND M.FIM
												 
											 AND ISNULL(B.HTRAB,0) > ISNULL(MT.limite_jornada,0)
	 
											 AND (SELECT excesso_jornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
											 AND B.BASE <= 540
												 {$qr_secao}
												 {$where_filial}
										 )x 
	 
						 UNION ALL
					/***************************************************************************************************************/
					/* Falta de registro adequado de jornada de trabalho (Registro Manual)                                         */
					/***************************************************************************************************************/
						SELECT
							  A.CODCOLIGADA
							, A.CHAPA
							, B.NOME
							, B.CODFUNCAO
							, C.NOME NOMEFUNCAO
							, B.CODSECAO
							, D.DESCRICAO NOMESECAO
							, A.DATA
							, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO	    
							, 'registro_manual' OCORRENCIA
							, NULL COMPLEMENTO
							, A.BATIDA VALOR
							
							, NULL BASE, NULL HTRAB, NULL EXTRAAUTORIZADO, NULL COMPENSADO, NULL DESCANSO, NULL FERIADO,
							'RM' SISTEMA
						FROM 
							ABATFUN (NOLOCK) A
							INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
							INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
							INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
							INNER JOIN PAR              M ON M.COLIGADA = A.CODCOLIGADA
						
						WHERE
								A.DATA BETWEEN M.INICIO AND M.FIM
							AND A.STATUS = 'D'
							 ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
							 ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							 AND (SELECT registro_manual FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
									
							 UNION ALL
							 /***************************************************************************************************************/
							 /* Trabalho em DSR ou Folga [COMPENSADO]        JORNADA SEMANAL                                                                  */
							 /***************************************************************************************************************/

							 /*CICLO NÃO MOTORISTA*/
					SELECT
						CODCOLIGADA,
						CHAPA,
						NOME,
						CODFUNCAO,
						NOMEFUNCAO,
						CODSECAO,
						NOMESECAO,
						MAX(DATA) DATA,
						HOR HORARIO,
						'trabalho_dsr_folga_descanso' OCORRENCIA,
						/*CONCAT(
							dbo.MINTOTIME(SUM(QTD2)),
							' previsto, ',
							dbo.MINTOTIME(SUM(HTRAB)),
							' realizado, limite de ',
							dbo.MINTOTIME(MAX(EXCESSO_JORNADA_SEMANAL)),
							', Ciclo: ',
							SUBSTRING(LINHA,1,1),
							', excedido:  ',
							dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
						) COMPLEMENTO,*/
						CONCAT(
							dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
						) COMPLEMENTO,
						SUM(QTD2) VALOR,
						NULL BASE,
						NULL HTRAB,
						NULL EXTRAAUTORIZADO,
						NULL COMPENSADO,
						NULL DESCANSO,
						NULL FERIADO,
						'RM' SISTEMA
					FROM(
					
					SELECT 
						X.CODCOLIGADA
						,X.CHAPA
						,X.NOME
						,(CASE WHEN DATEPART(dw,X.DATA) = 1 THEN DATEPART(WK,X.DATA)-1 ELSE DATEPART(WK,X.DATA) END)SEM
						,X.DATA DATA
						,X.HOR
						,X.DESC_HOR
						,SUM(X.HTRAB - X.TOTALBH)HTRAB
						,SUM(D.QTD2)QTD2
						,SUM(X.EXTRAEXECUTADO)EXTRAEXECUTADO
						,SUM(D.HEXTRA_DIARIA)HEXTRA_DIARIA
					
						,CONCAT(D.CICLO,'-', ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA) - ROW_NUMBER() OVER (PARTITION BY D.CICLO ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA))LINHA
						,E.TIPO_HORARIO
						--,SUM(ISNULL(EXCESSO_JORNADA_SEMANAL,0))EXCESSO_JORNADA_SEMANAL
						,ISNULL((SELECT MAX(EXCESSO_JORNADA_SEMANAL) FROM Z_OUTSERV_MELHORIAS3 WHERE CODHORARIO = X.HOR AND CODCOLIGADA = X.CODCOLIGADA AND CICLO = D.CICLO),0) EXCESSO_JORNADA_SEMANAL
						,X.CODFUNCAO
						,X.NOMEFUNCAO
						,X.CODSECAO
						,X.NOMESECAO
						
						, X.BASE
					FROM(   
						SELECT
							B.CODCOLIGADA
							,B.CHAPA
							,B.NOME
							,A.DATA
							,A.BASE
							,A.HTRAB
							,A.EXTRAEXECUTADO
							,A.ATRASO
							,A.ABONO
							,(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC) HOR
							,(SELECT TOP 1 AA.DESCRICAO FROM PFHSTHOR C, AHORARIO AA (NOLOCK) WHERE AA.CODCOLIGADA = C.CODCOLIGADA AND AA.CODIGO = C.CODHORARIO AND C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)DESC_HOR
							,
							
							--(CASE WHEN INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) THEN (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) ELSE (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) END)
							(CASE WHEN INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) THEN (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) ELSE (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) END) -1
							IND

					
								,ROW_NUMBER() OVER (PARTITION BY 
								
									--(CASE WHEN INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) THEN (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) ELSE (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) END)
									(CASE WHEN INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) THEN (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) ELSE (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) END) -1
								
								ORDER BY A.CHAPA,A.DATA) AS LINHA,
							B.CODFUNCAO,
							C.NOME NOMEFUNCAO,
							B.CODSECAO,
							D.DESCRICAO NOMESECAO,
							ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = A.DATA AND OBH.ATITUDE = 1),0) TOTALBH
						
						FROM
							AAFHTFUN A (NOLOCK)
							LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
							LEFT JOIN PFUNCAO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
							LEFT JOIN PSECAO D (NOLOCK) ON D.CODIGO = B.CODSECAO AND D.CODCOLIGADA = B.CODCOLIGADA
							INNER JOIN PAR P ON 1=1
							
							LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))
							LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = A.DATA								
							
							
						WHERE
							A.CODCOLIGADA = P.COLIGADA
							AND A.DATA BETWEEN '{$data_inicio_ciclo}' AND P.FIM
							AND B.CODSITUACAO NOT IN ('D')
							".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
							".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."

							AND B.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = B.CODCOLIGADA AND dtdel IS NULL)
					)X 
					
							LEFT JOIN Z_OUTSERV_MELHORIAS3 (NOLOCK) D  
											ON D.CODCOLIGADA = X.CODCOLIGADA 
												AND D.CODHORARIO = X.HOR
												AND D.CODINDICE = X.IND
						LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario E (NOLOCK) ON E.COLIGADA COLLATE Latin1_General_CI_AS = D.CODCOLIGADA AND E.CODIGO COLLATE Latin1_General_CI_AS = D.CODHORARIO
						WHERE TIPO_HORARIO = 'C'

					GROUP BY
						X.CODCOLIGADA
						,X.CHAPA
						,X.NOME
						,X.DATA
						,X.HOR
						,X.DESC_HOR
						,(CASE WHEN DATEPART(dw,X.DATA) = 1 THEN DATEPART(WK,X.DATA)-1 ELSE DATEPART(WK,X.DATA) END)
						,CICLO
						,TIPO_HORARIO
						, X.NOMEFUNCAO
						, X.CODFUNCAO
						, X.CODSECAO
						, X.NOMESECAO
						, X.BASE

					)z
					GROUP BY
						CODCOLIGADA
						, CHAPA
						, NOME
						, HOR
						, DESC_HOR
						, LINHA
						, TIPO_HORARIO
						, NOMEFUNCAO
						, CODFUNCAO
						, CODSECAO
						, NOMESECAO
					having SUM(HTRAB) > MAX(EXCESSO_JORNADA_SEMANAL)


					UNION ALL
					
					/*SEMANA NÃO MOTORISTA*/
					SELECT
						
						CODCOLIGADA,
						CHAPA,
						NOME,
						CODFUNCAO,
						NOMEFUNCAO,
						CODSECAO,
						NOMESECAO,
						MAX(DATA) DATA,
						HOR HORARIO,
						'trabalho_dsr_folga_descanso' OCORRENCIA,

						/*CONCAT(
							dbo.MINTOTIME(SUM(QTD2)),
							' previsto, ',
							dbo.MINTOTIME(SUM(HTRAB)),
							' realizado, limite de ',
							dbo.MINTOTIME(MAX(EXCESSO_JORNADA_SEMANAL)),
							/*', Semana: ',
							SEM,*/
							', excedido:  ',
							dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
						) COMPLEMENTO,*/

						CONCAT(
							dbo.MINTOTIME(SUM(HTRAB) - MAX(EXCESSO_JORNADA_SEMANAL))
						) COMPLEMENTO,


						SUM(QTD2) VALOR,
						NULL BASE,
						NULL HTRAB,
						NULL EXTRAAUTORIZADO,
						NULL COMPENSADO,
						NULL DESCANSO,
						NULL FERIADO,
						'RM' SISTEMA
						
					FROM(
					
					SELECT 
						X.CODCOLIGADA
						,X.CHAPA
						,X.NOME
						,(CASE WHEN DATEPART(dw,X.DATA) = 1 THEN DATEPART(WK,X.DATA)-1 ELSE DATEPART(WK,X.DATA) END)SEM
						,X.DATA DATA
						,X.HOR
						,X.DESC_HOR
						,SUM(X.HTRAB - X.TOTALBH)HTRAB
						,SUM(D.QTD2)QTD2
						,SUM(X.EXTRAEXECUTADO)EXTRAEXECUTADO
						,SUM(D.HEXTRA_DIARIA)HEXTRA_DIARIA
					
						,CONCAT(D.CICLO,'-', ROW_NUMBER() OVER (ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA) - ROW_NUMBER() OVER (PARTITION BY D.CICLO ORDER BY X.CODCOLIGADA, X.CHAPA, X.DATA))LINHA
						,E.TIPO_HORARIO
						,ISNULL((SELECT MAX(EXCESSO_JORNADA_SEMANAL) FROM Z_OUTSERV_MELHORIAS3 WHERE CODHORARIO = X.HOR AND CODCOLIGADA = X.CODCOLIGADA),0) EXCESSO_JORNADA_SEMANAL
						,X.CODFUNCAO
						,X.NOMEFUNCAO
						,X.CODSECAO
						,X.NOMESECAO
						
					FROM(   
						SELECT
							B.CODCOLIGADA
							,B.CHAPA
							,B.NOME
							,A.DATA
							,A.BASE
							,A.HTRAB
							,A.EXTRAEXECUTADO
							,A.ATRASO
							,A.ABONO
							,(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)HOR
							,(SELECT TOP 1 AA.DESCRICAO FROM PFHSTHOR C, AHORARIO AA (NOLOCK) WHERE AA.CODCOLIGADA = C.CODCOLIGADA AND AA.CODIGO = C.CODHORARIO AND C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)DESC_HOR
							--,(CASE WHEN INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) THEN (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) ELSE (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) END) IND
							,(CASE WHEN INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) THEN (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) ELSE (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) END) -1 IND

					
								,ROW_NUMBER() OVER (PARTITION BY 
									
									--(CASE WHEN INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) THEN (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) ELSE (INDICE + ((SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)))-1)) END)
									(CASE WHEN INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) > (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) THEN (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) - (SELECT MAX(INDICE) FROM ABATHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CODHORARIO = AH.CODIGO) ELSE (INDICE + (SELECT N.INDINICIOHOR FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))) END) -1
								
								ORDER BY A.CHAPA,A.DATA) AS LINHA,
							B.CODFUNCAO,
							C.NOME NOMEFUNCAO,
							B.CODSECAO,
							D.DESCRICAO NOMESECAO,
							ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = A.DATA AND OBH.ATITUDE = 1),0) TOTALBH
						FROM
							AAFHTFUN A (NOLOCK)
							LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
							LEFT JOIN PFUNCAO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
							LEFT JOIN PSECAO D (NOLOCK) ON D.CODIGO = B.CODSECAO AND D.CODCOLIGADA = B.CODCOLIGADA
							INNER JOIN PAR P ON 1=1
							
							LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))
							LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = A.DATA	
						WHERE
							A.CODCOLIGADA = P.COLIGADA
							AND A.DATA BETWEEN P.INICIO AND P.FIM
							AND B.CODSITUACAO NOT IN ('D')
							".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
							".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							AND B.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT codfuncao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista WHERE codcoligada = B.CODCOLIGADA AND dtdel IS NULL)

					
					)X
					
							LEFT JOIN Z_OUTSERV_MELHORIAS3 (NOLOCK) D  
											ON D.CODCOLIGADA = X.CODCOLIGADA 
												AND D.CODHORARIO = X.HOR
												AND D.CODINDICE = X.IND
							LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario E (NOLOCK) ON E.COLIGADA COLLATE Latin1_General_CI_AS = D.CODCOLIGADA AND E.CODIGO COLLATE Latin1_General_CI_AS = D.CODHORARIO

								WHERE TIPO_HORARIO = 'S'
					
					GROUP BY
						X.CODCOLIGADA
						,X.CHAPA
						,X.NOME
						,X.DATA
						,X.HOR
						,X.DESC_HOR
						,(CASE WHEN DATEPART(dw,X.DATA) = 1 THEN DATEPART(WK,X.DATA)-1 ELSE DATEPART(WK,X.DATA) END)
						,CICLO
						,E.TIPO_HORARIO
						,X.CODFUNCAO
						,X.NOMEFUNCAO
						,X.CODSECAO
						,X.NOMESECAO

					)z   
					GROUP BY
						CODCOLIGADA
						, CHAPA
						, NOME
						, HOR
						, DESC_HOR
						, LINHA
						, TIPO_HORARIO
						, NOMEFUNCAO
						, CODFUNCAO
						, CODSECAO
						, NOMESECAO
						,SEM

					having SUM(HTRAB) > MAX(EXCESSO_JORNADA_SEMANAL)

					UNION ALL
					
					/*SEMANA MOTORISTA*/
					SELECT CODCOLIGADA, CHAPA, NOME, CODFUNCAO, NOMEFUNCAO, CODSECAO, NOMESECAO, DATA, HORARIO, OCORRENCIA, COMPLEMENTO, VALOR, BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO, SISTEMA FROM (			  
						SELECT
							   
							   Y.CODCOLIGADA,
							   Y.CHAPA,
							   Y.NOME,
							   
							   Y.CODFUNCAO,
							   Y.NOMEFUNCAO,
							   Y.CODSECAO,
							   Y.NOMESECAO,
							   MAX(Y.DATA) DATA,
							   Y.HOR HORARIO,
							   'trabalho_dsr_folga_descanso' OCORRENCIA,
							   CONCAT(
								dbo.MINTOTIME(SUM(Y.HTRAB - Y.TOTALBH) - MAX(Y.excesso_semanal))
							) COMPLEMENTO,
							SUM(Y.excesso_semanal) VALOR,
							NULL BASE,
							NULL HTRAB,
							NULL EXTRAAUTORIZADO,
							NULL COMPENSADO,
							NULL DESCANSO,
							NULL FERIADO,
							'RM' SISTEMA
							, MAX(Y.excesso_semanal) excesso_semanal
							   
						FROM (
								
								SELECT
									*
								FROM (
			
			
			   
									SELECT
										B.CODCOLIGADA
										,B.CHAPA
										,B.NOME
										,A.DATA
										,A.BASE
										,A.HTRAB
										,A.EXTRAEXECUTADO
										,A.ATRASO
										,A.ABONO
										,(SELECT TOP 1 C.CODHORARIO FROM PFHSTHOR C (NOLOCK) WHERE C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)HOR
										,(SELECT TOP 1 AA.DESCRICAO FROM PFHSTHOR C, AHORARIO AA (NOLOCK) WHERE AA.CODCOLIGADA = C.CODCOLIGADA AND AA.CODIGO = C.CODHORARIO AND C.CODCOLIGADA = A.CODCOLIGADA AND C.CHAPA = A.CHAPA AND C.DTMUDANCA <= A.DATA ORDER BY C.DTMUDANCA DESC)DESC_HOR
										  ,(CASE WHEN DATEPART(dw,A.DATA) = 1 THEN DATEPART(WK,A.DATA)-1 ELSE DATEPART(WK,A.DATA) END) SEMANA
										,B.CODFUNCAO,
										C.NOME NOMEFUNCAO,
										B.CODSECAO,
										D.DESCRICAO NOMESECAO,
										MT.excesso_semanal,
										ISNULL((SELECT SUM(DATEDIFF(MINUTE, INICIO, FIM)) BH FROM AOCORRENCIACALCULADA OBH WHERE OBH.TIPOOCORRENCIA = 'ECA' AND OBH.CODCOLIGADA = B.CODCOLIGADA AND OBH.CHAPA = B.CHAPA AND OBH.DATAREFERENCIA = A.DATA AND OBH.ATITUDE = 1),0) TOTALBH
									FROM
										AAFHTFUN A (NOLOCK)
										LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
										LEFT JOIN PFUNCAO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
										LEFT JOIN PSECAO D (NOLOCK) ON D.CODIGO = B.CODSECAO AND D.CODCOLIGADA = B.CODCOLIGADA
										INNER JOIN PAR P ON 1=1
										
										LEFT JOIN AHORARIO (NOLOCK) AH ON AH.CODCOLIGADA = A.CODCOLIGADA AND AH.CODIGO = (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA))
										LEFT JOIN Z_OUTSERV_MELHORIAS4 (NOLOCK) ZZ ON ZZ.CODCOLIGADA = A.CODCOLIGADA AND ZZ.CODHORARIO = AH.CODIGO AND ZZ.DATA = A.DATA	
										INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista MT ON MT.codcoligada = B.CODCOLIGADA AND MT.dtdel IS NULL AND MT.codfuncao = B.CODFUNCAO COLLATE Latin1_General_CI_AS
									WHERE
										A.CODCOLIGADA = P.COLIGADA
										AND A.DATA BETWEEN P.INICIO AND P.FIM
										AND B.CODSITUACAO NOT IN ('D')
										".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
										".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)." 
			
								
								)X
								
							)Y
				GROUP BY
					Y.CODCOLIGADA,
					Y.CHAPA,
					Y.NOME,
					Y.CODFUNCAO,
					Y.NOMEFUNCAO,
					Y.CODSECAO,
					Y.NOMESECAO,
					Y.HOR,
					Y.SEMANA
				having SUM(Y.HTRAB) > MAX(excesso_semanal)
							 
							 
							 UNION ALL
							 
							 /***************************************************************************************************************/
							 /* Trabalho em DSR ou Folga [DESCANSO]                                                                            */
							 /***************************************************************************************************************/
							SELECT 
/**/							CODCOLIGADA, CHAPA, NOME, CODFUNCAO, NOMEFUNCAO, CODSECAO, NOMESECAO, DATA, HORARIO, OCORRENCIA, COMPLEMENTO, (CASE WHEN ISNULL((CASE WHEN CAL_HORAINICIO =0 AND CAL_HORAFINAL >= 1439 THEN N.extra_feriado ELSE N.extra_feriado_parcial END) ,0) = 0 THEN (CASE WHEN CAL_HORAINICIO =0 AND CAL_HORAFINAL >= 1439 THEN VALOR ELSE EXTRAAUTORIZADO END) ELSE (CASE WHEN CAL_HORAINICIO =0 AND CAL_HORAFINAL >= 1439 THEN VALOR ELSE EXTRAAUTORIZADO END)-ISNULL((CASE WHEN CAL_HORAINICIO =0 AND CAL_HORAFINAL >= 1439 THEN N.extra_feriado ELSE N.extra_feriado_parcial END) ,0) END) VALOR, BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO, SISTEMA
							FROM ( 
								 SELECT
									   A.CODCOLIGADA
									 , A.CHAPA
									 , A.NOME
									 , A.CODFUNCAO
									 , C.NOME NOMEFUNCAO
									 , A.CODSECAO
									 , D.DESCRICAO NOMESECAO
									 , B.DATA
									 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
									 , 'trabalho_dsr_folga' OCORRENCIA 
									 , NULL COMPLEMENTO
									 , (CASE WHEN (DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
											 WHEN B.FERIADO > 0 AND HTRAB > 0
												 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																		 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
																  THEN HTRAB
											 ELSE 0 END) VALOR
								 
									 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, B.FERIADO,
									'RM' SISTEMA,

									CAL.HORAINICIO CAL_HORAINICIO,
									CAL.HORAFINAL CAL_HORAFINAL
									 
								 FROM 
									 PFUNC (NOLOCK) A
										 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
										 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
										 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
										 /*Denis*/LEFT JOIN GFERIADO CAL ON CAL.CODCALENDARIO = D.CODCALENDARIO AND CAL.DIAFERIADO = B.DATA
										 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA

								 WHERE
										 B.DATA BETWEEN M.INICIO AND M.FIM
									{$qr_secao}
									{$where_filial}
									 AND (CASE WHEN (DESCANSO > 0) AND B.FERIADO = 0 AND HTRAB > 0 THEN HTRAB
											 WHEN B.FERIADO > 0 AND HTRAB > 0
												 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																		 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
																  THEN HTRAB
											 ELSE 0 END) > 0
									AND (SELECT trabalho_dsr FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1               
									)XX
								    LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario (NOLOCK) N ON XX.CODCOLIGADA = N.COLIGADA AND XX.HORARIO COLLATE Latin1_General_CI_AS = N.CODIGO
/**/								WHERE XX.VALOR > ISNULL(N.extra_feriado,0)
									/*WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(extra_feriado,0) = 1 )*/
								
					UNION ALL
					/***************************************************************************************************************/
					/* Trabalho em Férias ou Afastamentos                                                                             */
					/***************************************************************************************************************/

					SELECT
						CODCOLIGADA
					, CHAPA
					, NOME
					, CODFUNCAO
					, NOMEFUNCAO
					, CODSECAO
					, NOMESECAO
					, DATA
					, HORARIO
					, OCORRENCIA
					, CASE 
						WHEN FERIAS IS NOT NULL THEN 'Férias'
						WHEN AFAST IS NOT NULL THEN CONCAT('Afastamento/Atestado', 

						CASE WHEN (SELECT COUNT(AB.BATIDA) FROM ABATFUN AB WHERE AB.CHAPA = CHAPA AND AB.CODCOLIGADA = CODCOLIGADA AND COALESCE(AB.DATAREFERENCIA, AB.DATA) = DATA) > 0 THEN ' Existe registro de ponto' ELSE '' END)

						ELSE NULL
						END COMPLEMENTO
					, CASE 
						WHEN FERIAS IS NOT NULL THEN 1
						WHEN AFAST IS NOT NULL THEN 2
						ELSE NULL
						END VALOR
					, NULL BASE
					, NULL HTRAB
					, NULL EXTRAAUTORIZADO
					, NULL COMPENSADO
					, NULL DESCANSO
					, NULL FERIADO,
					'RM' SISTEMA
					
				FROM (
				
					SELECT 
						AX.* 
					FROM(
					
						SELECT
								A.CODCOLIGADA
							, A.CHAPA
							, A.NOME
							, A.CODFUNCAO
							, C.NOME NOMEFUNCAO
							, A.CODSECAO
							, D.DESCRICAO NOMESECAO
							, B.DATA
							, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
							, 'trabalho_ferias_afastamento' OCORRENCIA
							, (SELECT 'Férias' FROM PFUFERIASPER (NOLOCK) F  WHERE F.CODCOLIGADA = A.CODCOLIGADA AND F.CHAPA = A.CHAPA AND B.DATA BETWEEN F.DATAINICIO AND F.DATAFIM) FERIAS
							, (SELECT CASE WHEN (SELECT COUNT(BATIDA) FROM ABATFUN WHERE CHAPA = A.CHAPA AND CODCOLIGADA = A.CODCOLIGADA AND COALESCE(DATAREFERENCIA, DATA) = H.DTINICIO) <= 0 OR H.DTINICIO <> B.DATA THEN 'Afastamento' ELSE NULL END FROM PFHSTAFT (NOLOCK) H WHERE H.CODCOLIGADA = A.CODCOLIGADA AND H.CHAPA = A.CHAPA AND B.DATA BETWEEN H.DTINICIO AND  ISNULL(H.DTFINAL, '2050-12-01') ) AFAST
							, (SELECT COUNT(*) FROM ABATFUN (NOLOCK) G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) BAT
							, B.ABONO
						
						FROM 
							PFUNC (NOLOCK) A
							LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
							INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
							INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							
						
						WHERE
							B.DATA BETWEEN M.INICIO AND M.FIM
							{$qr_secao}
							{$where_filial}
						AND (SELECT trabalho_AfastFerias FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
											
					
					)AX
					WHERE	
						(AX.FERIAS IS NOT NULL OR AX.AFAST IS NOT NULL) AND AX.BAT > 0 AND ( ABONO = 0 OR ABONO >= 240) /* A pedido ignorado se o abono foi menor que 4horas*/
										
				)BX
								   


					UNION ALL
					/***************************************************************************************************************/
					/* InterJornada                                                                                                */
					/***************************************************************************************************************/

					SELECT 
						CODCOLIGADA
						, CHAPA
						, NOME
						, COD_FUNCAO CODFUNCAO
						, FUNCAO NOMEFUNCAO
						, SECAO_COD CODSECAO
						, DESC_SEC NOMESECAO
						, DATA DATAREFERENCIA
						, HOR_HIST HORARIO
						, OCORRENCIA
						, COMPLEMENTO
						, NULL VALOR

						, NULL BASE
						, NULL HTRAB
						, NULL EXTRAAUTORIZADO
						, NULL COMPENSADO
						, NULL DESCANSO
						, NULL FERIADO,
						'RM' SISTEMA

					FROM(
						SELECT 
							CODCOLIGADA,
							CHAPA, 
							NOME,
							COD_FUNCAO,
							FUNCAO,
							SECAO_COD,
							DESC_SEC,
							DATA, 
							dbo.mintotime(isnull(UM,'')) + '   ' + dbo.mintotime(isnull(DOIS,'')) + '   ' + dbo.mintotime(isnull(TRES,'')) + '   ' + dbo.mintotime(isnull(QUATRO,'')) + '   ' + dbo.mintotime(isnull(CINCO,'')) + '   ' + dbo.mintotime(isnull(SEIS,'')) + '   ' + dbo.mintotime(isnull(SETE,'')) + '   ' + dbo.mintotime(isnull(OITO,'')) BATIDAS,
							'interjornada' OCORRENCIA,
							
							(CASE WHEN QTD = 999 THEN 'Não realizou intervalo de Refeição ' ELSE CONCAT('Previsto ',dbo.mintotime(PREVISTO),', realizado ',
							ISNULL(dbo.mintotime((CASE WHEN (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END)< 0 
									THEN TRES + (1440 - DOIS)
									ELSE (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END) END)), '00:00'),' ,suprimido ', dbo.mintotime((PREVISTO - (CASE WHEN (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END)< 0 
										THEN TRES + (1440 - DOIS)
										ELSE (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE 0 END) END)))
										
							)END) COMPLEMENTO
							, '' VALOR
							, NULL BASE
							, NULL HTRAB
							, NULL EXTRAAUTORIZADO
							, NULL COMPENSADO
							, NULL DESCANSO
							, NULL FERIADO
							,'RM' SISTEMA
							, HOR_HIST
						FROM (
							
							SELECT 
								INICIO
								,FIM
								,CODCOLIGADA
								,CHAPA
								,NOME
								,COD_FUNCAO
								,FUNCAO
								,SECAO_COD
								,DESC_SEC
								,DATA
							
								,[1] BATIDA_ENT_REALIZADA
								,(SELECT MIN(BATINICIO) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_ENT_DEVERIA
								,(SELECT MAX(BATFIM) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_SAI_DEVERIA
						
								,CASE WHEN [8] IS NULL AND [7] IS NOT NULL THEN [7]
										WHEN [7] IS NULL AND [6] IS NOT NULL THEN [6]
											WHEN [6] IS NULL AND [5] IS NOT NULL THEN [5]
												WHEN [5] IS NULL AND [4] IS NOT NULL THEN [4]
													WHEN [4] IS NULL AND [3] IS NOT NULL THEN [3]
														WHEN [3] IS NULL AND [2] IS NOT NULL THEN [2]
															WHEN [2] IS NULL THEN [1] ELSE ''
																END BATIDA_SAI_REALIZADA
														
									
								,[1] UM
								,[2] DOIS
								,[3] TRES
								,[4] QUATRO
								,[5] CINCO
								,[6] SEIS
								,[7] SETE
								,[8] OITO
								,HTRAB
								,CODRECEBIMENTO
								,CODTIPO
								,QTD
								,PREVISTO
								,HOR_HIST
							FROM (	
								
								SELECT 
									ROW_NUMBER() OVER(PARTITION BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END), B.CHAPA ORDER BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)) LINHA,
									INICIO,
									FIM,
									A.CODCOLIGADA, 
									A.CHAPA, 
									C.NOME,
									C.CODSECAO SECAO_COD,
									D.DESCRICAO SECAO_NOME,
									D.DESCRICAO DESC_SEC,
									FU.CODIGO COD_FUNCAO,
									FU.NOME FUNCAO,
									A.DATA,
									B.BATIDA,
									(SELECT CODHORARIO FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
										(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA)) HOR_HIST,
										
						
								C.CODRECEBIMENTO, 
								C.CODTIPO,
								A.HTRAB,
								Q.QTD,
								P.PREVISTO
								
									
								
								FROM AAFHTFUN A
									
									LEFT JOIN ABATFUN B
										ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA = (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)
											AND B.STATUS NOT IN ('T')
								
									LEFT JOIN PFUNC C
										ON A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA 
								
									INNER JOIN PSECAO D
										ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODIGO = C.CODSECAO
									
									INNER JOIN PAR ON PAR.COLIGADA = A.CODCOLIGADA
									
									LEFT JOIN PSECAOCOMPL DC (NOLOCK) ON DC.CODCOLIGADA = D.CODCOLIGADA AND DC.CODIGO = D.CODIGO
								
									LEFT JOIN PFUNCAO FU (NOLOCK) ON FU.CODCOLIGADA = C.CODCOLIGADA AND FU.CODIGO = C.CODFUNCAO
								
								LEFT JOIN QTD_BATIDAS Q ON Q.CODCOLIGADA = A.CODCOLIGADA AND Q.CHAPA = A.CHAPA AND Q.DATA = A.DATA
								
								LEFT JOIN PREVISTO P ON P.CODCOLIGADA = A.CODCOLIGADA AND P.CHAPA = A.CHAPA AND P.DATA = A.DATA
								
								
								
								WHERE 
									A.CODCOLIGADA = PAR.COLIGADA
								AND A.DATA  BETWEEN PAR.INICIO AND PAR.FIM
								".str_replace('A.CODSECAO', 'C.CODSECAO', $qr_secao)."
								".str_replace('A.CODFILIAL', 'C.CODFILIAL', $where_filial)."
								AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
							
							
							)TABELACONSULTA
							
							PIVOT
							
							(
								MAX(BATIDA)
								FOR [LINHA] IN ([1],[2],[3],[4],[5],[6],[7],[8])
							) 
							AS TABELAPIVOT
							
							WHERE [1] IS NOT NULL
						
						)X
						
						WHERE 
							(ISNULL((CASE WHEN (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END)< 0  
									THEN TRES + (1440 - DOIS)
									ELSE (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END) END), 0) < PREVISTO
						
							OR (QTD =999))
					)Y
						UNION ALL
						/***************************************************************************************************************/
						/* IntraJornada    [tipo de retorno COMPLEMENTO]                                                               */
						/***************************************************************************************************************/
						  
						/*
						  SELECT 
							  CODCOLIGADA
							, CHAPA
							, NOME
							, CODFUNCAO
							, NOMEFUNCAO
							, CODSECAO
							, NOMESECAO
							, DATA
							, HORARIO
							, OCORRENCIA
							, ' Tempo Mínimo de refeição (IntraJornada) ' + dbo.mintotime(REF_OBRIGATORIO) + ' ' + 'não realizado [' + dbo.mintotime(REF_REALIZADO) + ']' COMPLEMENTO
							, VALOR
							
							, BASE
							  , HTRAB
							  , EXTRAAUTORIZADO
							  , COMPENSADO
							  , DESCANSO
							  , FERIADO,
							'RM' SISTEMA
						  FROM (
						  
							SELECT 
							 		ISNULL((SELECT (FIM-INICIO) TEMPO
									FROM ABATHOR M
									WHERE 
									M.CODCOLIGADA = XX.CODCOLIGADA
									AND M.CODHORARIO  = XX.HORARIO
									AND TIPO = 4 
									AND INDICE = 1
									),0) REF_OBRIGATORIO
							  , *
								
							FROM (
						  
							  SELECT
									A.CODCOLIGADA
								  , A.CHAPA
								  , A.NOME
								  , A.CODFUNCAO
								  , C.NOME NOMEFUNCAO
								  , A.CODSECAO
								  , D.DESCRICAO NOMESECAO
								  , B.DATA
								  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
								  , 'interjornada' OCORRENCIA
								  , NULL VALOR
								  
								  ,ISNULL((SELECT SUM(DATEDIFF(MINUTE,INICIO,FIM)) FROM AOCORRENCIACALCULADA P
								  WHERE 
									A.CODCOLIGADA = P.CODCOLIGADA 
								  AND A.CHAPA = P.CHAPA 
								  AND B.DATA = P.DATAREFERENCIA
								  AND TIPOOCORRENCIA IN ('AREF')),0) REF_REALIZADO
							  
								  , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
							  FROM 
								  PFUNC (NOLOCK) A
									  LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
									  INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
									  INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
									  INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							  
							  WHERE
									B.DATA BETWEEN M.INICIO AND M.FIM
								  AND B.HTRAB > 0
								  AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								  {$qr_secao} 
								   {$where_filial}
						  
							)XX
						  )XY
						  WHERE REF_OBRIGATORIO-REF_REALIZADO >= 1 AND REF_OBRIGATORIO > 60
						  */

						  
 
SELECT 
	CODCOLIGADA,
	CHAPA, 
	NOME,
	COD_FUNCAO,
	FUNCAO,
	SECAO_COD,
	DESC_SEC,
	DATA,
	HOR_HIST,
    'intrajornada' OCORRENCIA,
    /*DENIS 18/04/2024	CONCAT('(IntraJornada) previsto ',dbo.mintotime(PREVISTO),', realizado ',
           ISNULL(dbo.mintotime((CASE WHEN (CASE WHEN isnull((CASE WHEN TRES = 0 THEN 1440 ELSE TRES END),'')> 0 THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) - DOIS ELSE NULL END)< 0 
   	        THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) + (1440 - DOIS)
          ELSE (CASE WHEN isnull((CASE WHEN TRES = 0 THEN 1440 ELSE TRES END),'')> 0 THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) - DOIS ELSE NULL END) END)), '00:00'),', suprimido ', dbo.mintotime((PREVISTO - (CASE WHEN (CASE WHEN isnull((CASE WHEN TRES = 0 THEN 1440 ELSE TRES END),'')> 0 THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) - DOIS ELSE NULL END)< 0 
          THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) + (1440 - DOIS)
          ELSE (CASE WHEN isnull((CASE WHEN TRES = 0 THEN 1440 ELSE TRES END),'')> 0 THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) - DOIS ELSE NULL END) END))))
			COMPLEMENTO
   /DENIS 18/04/2024*/ 
  
			
    /*DENIS 18/04/2024*/ CONCAT('suprimido ', dbo.mintotime((PREVISTO - (CASE WHEN (CASE WHEN isnull((CASE WHEN TRES = 0 THEN 1440 ELSE TRES END),'')> 0 THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) - DOIS ELSE NULL END)< 0 
	/*DENIS 18/04/2024*/ THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) + (1440 - DOIS)
	/*DENIS 18/04/2024*/ ELSE (CASE WHEN isnull((CASE WHEN TRES = 0 THEN 1440 ELSE TRES END),'')> 0 THEN (CASE WHEN TRES = 0 THEN 1440 ELSE TRES END) - DOIS ELSE NULL END) END))))
	/*DENIS 18/04/2024*/ 	COMPLEMENTO
    , '' VALOR
	, NULL BASE
	, NULL HTRAB
	, NULL EXTRAAUTORIZADO
	, NULL COMPENSADO
	, NULL DESCANSO
	, NULL FERIADO,
	'RM' SISTEMA
	
FROM (
	
	SELECT 
		 INICIO
		,FIM
		,CODCOLIGADA
		,CHAPA
		,NOME
		,COD_FUNCAO
		,FUNCAO
		,SECAO_COD
		,DESC_SEC
		,DATA
	   
		,[1] BATIDA_ENT_REALIZADA
		,(SELECT MIN(BATINICIO) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_ENT_DEVERIA
		,(SELECT MAX(BATFIM) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_SAI_DEVERIA

		,CASE WHEN [8] IS NULL AND [7] IS NOT NULL THEN [7]
			     WHEN [7] IS NULL AND [6] IS NOT NULL THEN [6]
					 WHEN [6] IS NULL AND [5] IS NOT NULL THEN [5]
						 WHEN [5] IS NULL AND [4] IS NOT NULL THEN [4]
							 WHEN [4] IS NULL AND [3] IS NOT NULL THEN [3]
								 WHEN [3] IS NULL AND [2] IS NOT NULL THEN [2]
									 WHEN [2] IS NULL THEN [1] ELSE ''
										END BATIDA_SAI_REALIZADA
								
			
		,[1] UM
		,[2] DOIS
		,[3] TRES
		,[4] QUATRO
		,[5] CINCO
		,[6] SEIS
		,[7] SETE
		,[8] OITO
		,HTRAB
		,CODRECEBIMENTO
		,CODTIPO
		,QTD
		/*DENIS 17/04/2024*/,INTER_OBR
		/*DENIS 17/04/2024*/,INTER_FRAC
		,PREVISTO
		,HOR_HIST
	FROM (	
		
		SELECT 
		    ROW_NUMBER() OVER(PARTITION BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END), B.CHAPA ORDER BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)) LINHA,
			INICIO,
			FIM,
			A.CODCOLIGADA, 
			A.CHAPA, 
			C.NOME,
		    C.CODSECAO SECAO_COD,
		    D.DESCRICAO SECAO_NOME,
		    D.DESCRICAO DESC_SEC,
		    FU.CODIGO COD_FUNCAO,
		    FU.NOME FUNCAO,
		    A.DATA,
		    B.BATIDA,
		    (SELECT CODHORARIO FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
				(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA)) HOR_HIST,
				

	       C.CODRECEBIMENTO, 
		   C.CODTIPO,
		   A.HTRAB,
		   Q.QTD,
		   /*DENIS 17/04/2024*/ Q.INTER_OBR,
			/*DENIS 17/04/2024*/ Q.INTER_FRAC,  
			/*DENIS 17/04/2024*//*P.PREVISTO*/
			/*DENIS 17/04/2024*/  (SELECT ML.INTERVALO 
			/*DENIS 17/04/2024*/              FROM 
			/*DENIS 17/04/2024*/                 Z_OUTSERV_MELHORIAS3 ML 
			/*DENIS 17/04/2024*/                  INNER JOIN (SELECT * FROM dbo.OUTSERV_HORARIO_HIST(A.CODCOLIGADA,A.CHAPA,A.DATA)) Z ON ML.CODHORARIO = Z.CODHORARIO AND ML.CODINDICE = IND_CALC --AND ML.DATA = Z.DATA_REF 
			/*DENIS 17/04/2024*/  )PREVISTO
		  
		    
		
		FROM AAFHTFUN A
			
			LEFT JOIN ABATFUN B
				ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA = (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)
					AND B.STATUS NOT IN ('T')
		
		    LEFT JOIN PFUNC C
		    	ON A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA 
		
		    INNER JOIN PSECAO D
		    	ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODIGO = C.CODSECAO
		    
		    INNER JOIN PAR ON PAR.COLIGADA = A.CODCOLIGADA
		    
		    LEFT JOIN PSECAOCOMPL DC (NOLOCK) ON DC.CODCOLIGADA = D.CODCOLIGADA AND DC.CODIGO = D.CODIGO
		   
		    LEFT JOIN PFUNCAO FU (NOLOCK) ON FU.CODCOLIGADA = C.CODCOLIGADA AND FU.CODIGO = C.CODFUNCAO
		   
		   LEFT JOIN QTD_BATIDAS Q ON Q.CODCOLIGADA = A.CODCOLIGADA AND Q.CHAPA = A.CHAPA AND Q.DATA = A.DATA
		   
		   LEFT JOIN PREVISTO P ON P.CODCOLIGADA = A.CODCOLIGADA AND P.CHAPA = A.CHAPA AND P.DATA = A.DATA
		   
		   
		   
		
		WHERE 
			A.CODCOLIGADA = PAR.COLIGADA
		AND A.DATA  BETWEEN PAR.INICIO AND PAR.FIM
	    AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
		".str_replace('A.CODSECAO', 'C.CODSECAO', $qr_secao)."
		".str_replace('A.CODFILIAL', 'C.CODFILIAL', $where_filial)."

		AND B.CODFUNCAO COLLATE Latin1_General_CI_AS IN (SELECT P.codfuncao
						FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P)
           
	
	
	)TABELACONSULTA
	
	PIVOT
	
	(
	    MAX(BATIDA)
	    FOR [LINHA] IN ([1],[2],[3],[4],[5],[6],[7],[8])
	) 
	AS TABELAPIVOT
	
	 WHERE [1] IS NOT NULL

)X

WHERE 
     ISNULL((CASE WHEN (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END)< 0  
	        THEN TRES + (1440 - DOIS)
	        ELSE (CASE WHEN isnull(TRES,'')> 0 THEN TRES - DOIS ELSE NULL END) END), 0) < PREVISTO

			/*DENIS 18/04/2024*/ AND QTD >=4 AND PREVISTO > 60 AND INTER_OBR = '1' AND INTER_FRAC = '0'

			UNION ALL 

			
SELECT
COL CODCOLIGADA
,VCHAPA CHAPA
,NOME
,COF_FCO CODFUNCAO
,DESC_FUNCAO NOMEFUNCAO
,COD_SEC CODSECAO
,DESC_SEC NOMESECAO 
,VDATA DATA
,HOR HORARIO
/*,IND_HOR*/
,'intrajornada' OCORRENCIA



,(CASE 
  WHEN SAI1 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT2 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT2-SAI1 < INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END))
  
  WHEN SAI1 < PLAN_INIC AND (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END) < INTERVALO  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END))  + ' antes do planejado'
  WHEN ENT2 > PLAN_FIM AND (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END) < INTERVALO   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END))  + ' depois do planejado'
 
  WHEN SAI1 < PLAN_INIC AND (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END) >= INTERVALO THEN 'Intervalo correto antes do planejado'
  WHEN ENT2 > PLAN_FIM AND (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END) = INTERVALO  THEN 'Intervalo correto depois do planejado'
  
  

WHEN (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END) < INTERVALO AND SAI1 > PLAN_INIC THEN 'Suprimido ' +  DBO.MINTOTIME(INTERVALO-(CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END))+ ' antes do planejado'
WHEN (CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END) < INTERVALO AND ENT2 < PLAN_FIM THEN 'Suprimido ' +  DBO.MINTOTIME(INTERVALO-(CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END))+ ' depois do planejado'

END)COMPLEMENTO 




 ,NULL VALOR
 ,NULL BASE
 ,NULL HTRAB
 ,NULL EXTRAAUTORIZADO
 ,NULL COMPENSADO
 ,NULL DESCANSO
 ,NULL FERIADO,
'RM' SISTEMA


/*
,dbo.MINTOTIME(SAI1)SAI1
,dbo.MINTOTIME(ENT2)ENT2 
,BAT
,ISNULL(INTER_FRAC,0)INTER_FRAC
,ISNULL(INTER_OBR,0)INTER_OBR
,dbo.MINTOTIME(PLAN_INIC)PLAN_INIC
,dbo.MINTOTIME(PLAN_FIM)PLAN_FIM
,dbo.MINTOTIME(INTERVALO)INTERVALO
,dbo.MINTOTIME((CASE WHEN SAI1 > ENT2 THEN (ENT2 + 1440) - SAI1 ELSE ENT2 - SAI1 END))REALIZADO
*/




FROM(
SELECT 
  COL
 ,VCHAPA
 ,NOME
 ,COF_FCO
 ,DESC_FUNCAO
 ,COD_SEC
 ,DESC_SEC 
 ,VDATA
 ,HOR
 ,DESC_HOR
 ,'|'T
 ,SAI1, ENT2 
 
 ,BAT
 ,IND_HOR
 
 
 ,(SELECT P.intervalo_fracionado
FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
WHERE P.coligada = COL  
	 AND P.codigo = HOR COLLATE Latin1_General_CI_AS)INTER_FRAC
	 
	 
,(SELECT P.intervalo_obrigatorio
FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
WHERE P.coligada = COL  
	 AND P.codigo = HOR COLLATE Latin1_General_CI_AS)INTER_OBR	      


,(SELECT P.planejado_inicio
FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
WHERE P.coligada = COL  
	 AND P.codigo = HOR COLLATE Latin1_General_CI_AS)PLAN_INIC	  

,(SELECT P.planejado_termino
FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario P
WHERE P.coligada = COL  
	 AND P.codigo = HOR COLLATE Latin1_General_CI_AS)PLAN_FIM	  
	 
	  


,(SELECT ISNULL((A.INTERVALO),0)
	FROM 
	 Z_OUTSERV_MELHORIAS3 A (nolock)
	 
	WHERE 
	   A.CODCOLIGADA = COL
	   AND A.CODHORARIO = HOR
	   AND A.CODINDICE = IND_HOR) INTERVALO
		 
		 
		 
		 
		 
		   

FROM(
SELECT COL
 ,VCHAPA
 ,NOME
 ,COF_FCO
 ,DESC_FUNCAO
 ,COD_SEC
 ,DESC_SEC 
 ,DATA VDATA   
   
 ,(SELECT CODHORARIO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))HOR         
 ,(SELECT DESCRICAO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))DESC_HOR
 ,(SELECT IND_CALC FROM DBO.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))IND_HOR
		
  
 , [1] AS ENT1, [2] AS SAI1
 , [3] AS ENT2, [4] AS SAI2
 , [5] AS ENT3, [6] AS SAI3
 , [7] AS ENT4, [8] AS SAI4

,((CASE WHEN [1] IS NOT NULL THEN 1 ELSE 0 END)+
 (CASE WHEN [2] IS NOT NULL THEN 1 ELSE 0 END)+
 (CASE WHEN [3] IS NOT NULL THEN 1 ELSE 0 END)+
 (CASE WHEN [4] IS NOT NULL THEN 1 ELSE 0 END)+
 (CASE WHEN [5] IS NOT NULL THEN 1 ELSE 0 END)+
 (CASE WHEN [6] IS NOT NULL THEN 1 ELSE 0 END)+
 (CASE WHEN [7] IS NOT NULL THEN 1 ELSE 0 END)+
 (CASE WHEN [8] IS NOT NULL THEN 1 ELSE 0 END)
)BAT
FROM (
SELECT 
   A.CODCOLIGADA COL
   ,A.CHAPA VCHAPA
   ,B.NOME
   ,C.CODIGO COF_FCO
   ,C.NOME DESC_FUNCAO
   ,D.CODIGO COD_SEC
   ,D.DESCRICAO DESC_SEC
   ,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
   ,ISNULL(A.DATAREFERENCIA,A.DATA)VDATA
   ,ROW_NUMBER() OVER (PARTITION BY A.CHAPA, ISNULL(A.DATAREFERENCIA, A.DATA) ORDER BY A.CHAPA, A.DATA, ISNULL(A.DATAREFERENCIA, A.DATA)) AS LIN
 
   
   ,(CASE 
	   WHEN ISNULL(DATAREFERENCIA, DATA) > A.DATA THEN BATIDA
	   WHEN ISNULL(DATAREFERENCIA, DATA) < A.DATA THEN BATIDA
	   ELSE BATIDA
   END) AS BATIDA     
   
   
   
FROM ABATFUN A (nolock)
	LEFT JOIN PFUNC B (nolock) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA 
	LEFT JOIN PFUNCAO (nolock) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO 
	LEFT JOIN PSECAO (nolock) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
	INNER JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
WHERE 
	   A.STATUS <> 'T'
   AND ISNULL(A.DATAREFERENCIA, A.DATA) BETWEEN P.INICIO AND P.FIM
   ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
		".str_replace('A.CODFILIAL', 'C.CODFILIAL', $where_filial)."

		AND B.CODFUNCAO COLLATE Latin1_General_CI_AS IN (SELECT P.codfuncao
						FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P)
  
) AS PivotSource
PIVOT (
MAX(BATIDA) FOR LIN IN ([1], [2], [3], [4], [5],[6],[7],[8])) AS PivotTable

)TBL

)X

WHERE BAT = 4 AND ISNULL(INTER_FRAC,0) = 0 AND ISNULL(INTER_OBR,0) = 1

UNION ALL

/*****************************************************************************************/
/*									INTRAJORNADA MOTORISTA								 */
/*****************************************************************************************/

SELECT
     COL CODCOLIGADA
    ,VCHAPA CHAPA
    ,NOME
    ,COD_FCO CODFUNCAO
    ,DESC_FUNCAO NOMEFUNCAO
    ,COD_SEC CODSECAO
    ,DESC_SEC NOMESECAO 
    ,VDATA DATA
    ,HOR HORARIO
    ,'intrajornada' OCORRENCIA
    ,(CASE 
       /*4 BAT*/
       WHEN BAT = 4 AND SAI1 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT2 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT2-SAI1 <= INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - (ENT2-SAI1))
       WHEN BAT = 4 AND SAI1 < PLAN_INIC AND ENT2-SAI1 < INTERVALO  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - (ENT2-SAI1))  + ' antes do planiejado'
       WHEN BAT = 4 AND ENT2 > PLAN_FIM AND ENT2-SAI1 < INTERVALO   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - (ENT2-SAI1))  + ' depois do planiejado'
       WHEN BAT = 4 AND SAI1 < PLAN_INIC AND ENT2-SAI1 >= INTERVALO THEN 'Intervalo correto antes do planijejado'
       WHEN BAT = 4 AND ENT2 > PLAN_FIM AND ENT2-SAI1 = INTERVALO  THEN 'Intervalo correto depois do planijejado'
     
    
       /*6 BAT*/
       WHEN BAT = 6 AND SAI1 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT3 BETWEEN PLAN_INIC AND PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)) <= INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)))
       WHEN BAT = 6 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)) < INTERVALO  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)))  + ' antes do planiejado'
       WHEN BAT = 6 AND ENT3 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)) < INTERVALO   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)))  + ' depois do planiejado'
       WHEN BAT = 6 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)) >= INTERVALO THEN 'Intervalo correto antes do planijejado'
       WHEN BAT = 6 AND ENT3 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)) = INTERVALO  THEN 'Intervalo correto depois do planijejado'
     
         
       /*8 BAT*/
       WHEN BAT = 8 AND SAI1 BETWEEN PLAN_INIC AND PLAN_FIM AND ENT4 BETWEEN PLAN_INIC AND PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) <= INTERVALO THEN 'Suprimido ' + dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))
       WHEN BAT = 8 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO  THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))  + ' antes do planiejado'
       WHEN BAT = 8 AND ENT4 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) < INTERVALO   THEN 'Suprimido '+ dbo.MINTOTIME(INTERVALO - ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)))  + ' depois do planiejado'
       WHEN BAT = 8 AND SAI1 < PLAN_INIC AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) >= INTERVALO THEN 'Intervalo correto antes do planijejado'
       WHEN BAT = 8 AND ENT4 > PLAN_FIM AND ((ENT2-SAI1)+(ENT3-SAI2)+(ENT4-SAI3)) = INTERVALO  THEN 'Intervalo correto depois do planijejado'  
     
     ELSE 'n/a'
     END)COMPLEMENTO
     
     
      ,NULL VALOR
      ,NULL BASE
	  ,NULL HTRAB
	  ,NULL EXTRAAUTORIZADO
	  ,NULL COMPENSADO
	  ,NULL DESCANSO
	  ,NULL FERIADO,
	'RM' SISTEMA

     
FROM(
SELECT 
       COL
      ,VCHAPA
      ,NOME
      ,COD_FCO
      ,DESC_FUNCAO
      ,COD_SEC
      ,DESC_SEC 
      ,VDATA
      ,HOR
      ,DESC_HOR
      ,'|'T
      ,BAT
      
      
      ,(SELECT P.intervalo_fracionado
	FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
	WHERE P.codcoligada = COL 
	AND ISNULL(p.usudel,0) <> 1
	      AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)INTER_FRAC
	      
	      
	,(SELECT P.intervalo_obrigatorio
	FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
	WHERE P.codcoligada = COL  
	AND ISNULL(p.usudel,0) <> 1
	      AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)INTER_OBR	      


	,(SELECT P.intervalo_planejado_ini
	FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
	WHERE P.codcoligada = COL  
	AND ISNULL(p.usudel,0) <> 1
	      AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)PLAN_INIC	  

	,(SELECT P.intervalo_planejado_fim
	FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
	WHERE P.codcoligada = COL  
	AND ISNULL(p.usudel,0) <> 1
	      AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS)PLAN_FIM	  
	      
	       
   
   
	,isnull((SELECT P.intervalo_total
	FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P
	WHERE P.codcoligada = COL  
	AND ISNULL(p.usudel,0) <> 1
	      AND P.codfuncao = COD_FCO COLLATE Latin1_General_CI_AS),0)INTERVALO
      	    
      	    
      	    
      , ENT1
      
      , SAI1
      , ENT2
      , SAI2
      , ENT3
      , SAI3
      , ENT4
      
      , SAI4 
      	    
      	      

FROM(
SELECT COL
      ,VCHAPA
      ,NOME
      ,COD_FCO
      ,DESC_FUNCAO
      ,COD_SEC
      ,DESC_SEC 
      ,DATA VDATA   
        
      ,(SELECT CODHORARIO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))HOR         
      ,(SELECT DESCRICAO FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))DESC_HOR
      ,(SELECT IND_CALC FROM dbo.OUTSERV_HORARIO_HIST(COL,VCHAPA,DATA))IND_HOR           
       
      , [1] AS ENT1, [2] AS SAI1
      , [3] AS ENT2, [4] AS SAI2
      , [5] AS ENT3, [6] AS SAI3
      , [7] AS ENT4, [8] AS SAI4
    
    ,((CASE WHEN [1] IS NOT NULL THEN 1 ELSE 0 END)+
      (CASE WHEN [2] IS NOT NULL THEN 1 ELSE 0 END)+
      (CASE WHEN [3] IS NOT NULL THEN 1 ELSE 0 END)+
      (CASE WHEN [4] IS NOT NULL THEN 1 ELSE 0 END)+
      (CASE WHEN [5] IS NOT NULL THEN 1 ELSE 0 END)+
      (CASE WHEN [6] IS NOT NULL THEN 1 ELSE 0 END)+
      (CASE WHEN [7] IS NOT NULL THEN 1 ELSE 0 END)+
      (CASE WHEN [8] IS NOT NULL THEN 1 ELSE 0 END)
     )BAT
FROM (
    SELECT 
    	A.CODCOLIGADA COL
    	,A.CHAPA VCHAPA
    	,B.NOME
    	,C.CODIGO COD_FCO
    	,C.NOME DESC_FUNCAO
    	,D.CODIGO COD_SEC
    	,D.DESCRICAO DESC_SEC
    	,ISNULL(A.DATAREFERENCIA,A.DATA)DATA
    	,ISNULL(A.DATAREFERENCIA,A.DATA)VDATA
    	,ROW_NUMBER() OVER (PARTITION BY A.CHAPA, ISNULL(A.DATAREFERENCIA, A.DATA) ORDER BY A.CHAPA, A.DATA, ISNULL(A.DATAREFERENCIA, A.DATA)) AS LIN
        
        ,(CASE 
            WHEN ISNULL(DATAREFERENCIA, DATA) > A.DATA THEN BATIDA 
            WHEN ISNULL(DATAREFERENCIA, DATA) < A.DATA THEN BATIDA 
            ELSE BATIDA
        END) AS BATIDA     
        
        
        
    FROM ABATFUN A
         LEFT JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA 
         LEFT JOIN PFUNCAO C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO 
         LEFT JOIN PSECAO D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
         INNER JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
    WHERE 
            A.STATUS <> 'T'
        AND ISNULL(A.DATAREFERENCIA, A.DATA) BETWEEN P.INICIO AND P.FIM
        ".str_replace('A.CODSECAO', 'C.CODSECAO', $qr_secao)."
			".str_replace('B.CODFILIAL', 'C.CODFILIAL', $where_filial)."
		
		
	   AND B.CODFUNCAO COLLATE Latin1_General_CI_AS IN (SELECT P.codfuncao
	      FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P)
		
		
		
		
		
		
		
		
) AS PivotSource
PIVOT (
    MAX(BATIDA) FOR LIN IN ([1], [2], [3], [4], [5],[6],[7],[8])) AS PivotTable

)TBL

)XX

WHERE (BAT = 4 OR BAT = 6 OR BAT = 8 ) AND ISNULL(INTER_FRAC,0) = 1 AND ISNULL(INTER_OBR,0) = 1

			UNION ALL
			/***************************************************************************************************************/
/*DENIS 18/04/2024*/					/* IntraJornada    [tipo de retorno COMPLEMENTO]  2 BATIDAS OU IMPAR        INTERVALO NÃO IDENTIFICADO          */
			/***************************************************************************************************************/ 



SELECT 
CODCOLIGADA,
CHAPA, 
NOME,
COD_FUNCAO,
FUNCAO,
SECAO_COD,
DESC_SEC,
DATA,
HOR_HIST,
'intrajornada' OCORRENCIA,

'Intervalo não identificado'COMPLEMENTO



, '' VALOR
, NULL BASE
, NULL HTRAB
, NULL EXTRAAUTORIZADO
, NULL COMPENSADO
, NULL DESCANSO
, NULL FERIADO,
'RM' SISTEMA

FROM (

SELECT 
INICIO
,FIM
,CODCOLIGADA
,CHAPA
,NOME
,COD_FUNCAO
,FUNCAO
,SECAO_COD
,DESC_SEC
,DATA

,[1] BATIDA_ENT_REALIZADA
,(SELECT MIN(BATINICIO) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_ENT_DEVERIA
,(SELECT MAX(BATFIM) FROM AJORHOR N WHERE TABELAPIVOT.CODCOLIGADA = N.CODCOLIGADA AND TABELAPIVOT.HOR_HIST = N.CODHORARIO) BATIDA_SAI_DEVERIA

,CASE WHEN [8] IS NULL AND [7] IS NOT NULL THEN [7]
	 WHEN [7] IS NULL AND [6] IS NOT NULL THEN [6]
		 WHEN [6] IS NULL AND [5] IS NOT NULL THEN [5]
			 WHEN [5] IS NULL AND [4] IS NOT NULL THEN [4]
				 WHEN [4] IS NULL AND [3] IS NOT NULL THEN [3]
					 WHEN [3] IS NULL AND [2] IS NOT NULL THEN [2]
						 WHEN [2] IS NULL THEN [1] ELSE ''
							END BATIDA_SAI_REALIZADA
					

,[1] UM
,[2] DOIS
,[3] TRES
,[4] QUATRO
,[5] CINCO
,[6] SEIS
,[7] SETE
,[8] OITO
,HTRAB
,CODRECEBIMENTO
,CODTIPO
,QTD
,INTER_OBR
,INTER_FRAC
,PREVISTO
,HOR_HIST
FROM (	

SELECT 
ROW_NUMBER() OVER(PARTITION BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END), B.CHAPA ORDER BY (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)) LINHA,
INICIO,
FIM,
A.CODCOLIGADA, 
A.CHAPA, 
C.NOME,
C.CODSECAO SECAO_COD,
D.DESCRICAO SECAO_NOME,
D.DESCRICAO DESC_SEC,
FU.CODIGO COD_FUNCAO,
FU.NOME FUNCAO,
A.DATA,
B.BATIDA,
(SELECT CODHORARIO FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
	(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA)) HOR_HIST,
	

C.CODRECEBIMENTO, 
C.CODTIPO,
A.HTRAB,
Q.QTD,
Q.INTER_OBR,
Q.INTER_FRAC,  

(SELECT ML.INTERVALO 
		 FROM 
			Z_OUTSERV_MELHORIAS3 ML 
			INNER JOIN (SELECT * FROM dbo.OUTSERV_HORARIO_HIST(A.CODCOLIGADA,A.CHAPA,A.DATA)) Z ON ML.CODHORARIO = Z.CODHORARIO AND ML.CODINDICE = IND_CALC
)PREVISTO


FROM AAFHTFUN A

LEFT JOIN ABATFUN B
	ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA = (CASE WHEN B.DATAREFERENCIA IS NULL THEN B.DATA ELSE B.DATAREFERENCIA END)
		AND B.STATUS NOT IN ('T')

LEFT JOIN PFUNC C
	ON A.CODCOLIGADA = C.CODCOLIGADA AND A.CHAPA = C.CHAPA 

INNER JOIN PSECAO D
	ON D.CODCOLIGADA = C.CODCOLIGADA AND D.CODIGO = C.CODSECAO

INNER JOIN PAR ON PAR.COLIGADA = A.CODCOLIGADA

LEFT JOIN PSECAOCOMPL DC (NOLOCK) ON DC.CODCOLIGADA = D.CODCOLIGADA AND DC.CODIGO = D.CODIGO

LEFT JOIN PFUNCAO FU (NOLOCK) ON FU.CODCOLIGADA = C.CODCOLIGADA AND FU.CODIGO = C.CODFUNCAO

LEFT JOIN QTD_BATIDAS Q ON Q.CODCOLIGADA = A.CODCOLIGADA AND Q.CHAPA = A.CHAPA AND Q.DATA = A.DATA


WHERE 
A.CODCOLIGADA = PAR.COLIGADA
AND A.DATA  BETWEEN PAR.INICIO AND PAR.FIM
AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
		".str_replace('A.CODSECAO', 'C.CODSECAO', $qr_secao)."
		".str_replace('A.CODFILIAL', 'C.CODFILIAL', $where_filial)."

		AND B.CODFUNCAO COLLATE Latin1_General_CI_AS NOT IN (SELECT P.codfuncao
						FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_motorista P)




AND (SELECT CODHORARIO FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA)) IN (

SELECT DISTINCT CODHORARIO
FROM ABATHOR
WHERE
CODCOLIGADA = '{$_SESSION['func_coligada']}'
AND TIPO = '4'
AND (FIM-INICIO) <> 0
)



)TABELACONSULTA

PIVOT

(
MAX(BATIDA)
FOR [LINHA] IN ([1],[2],[3],[4],[5],[6],[7],[8])
) 
AS TABELAPIVOT

WHERE [1] IS NOT NULL

)X

WHERE 


/*DENIS 18/04/2024*/ (QTD = 2 OR QTD % 2 = 1) AND INTER_OBR = '1'


						UNION ALL
						/***************************************************************************************************************/
						/* Registros britânicos (Apontamentos Manuais)    [tipo de retorno COMPLEMENTO]                                */
						/***************************************************************************************************************/ 
						 
						  SELECT
								A.CODCOLIGADA
							  , A.CHAPA
							  , B.NOME
							  , B.CODFUNCAO
							  , D.NOME NOMEFUNCAO
							  , B.CODSECAO
							  , C.DESCRICAO NOMESECAO
							  , A.DATA
							  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
							  , 'registro_britanico' OCORRENCIA
							  , CONCAT(dbo.mintotime(MIN(B1.BATIDA)), ' ', dbo.mintotime(MAX(B1.BATIDA))) COMPLEMENTO
							  , 1 VALOR
								
							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
								'RM' SISTEMA
							  
							  
						  FROM AAFHTFUN (NOLOCK) A
							  INNER JOIN PFUNC   (NOLOCK) B  ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
							  INNER JOIN PSECAO  (NOLOCK) C  ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODSECAO
							  INNER JOIN PFUNCAO (NOLOCK) D  ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
							  INNER JOIN ABATFUN (NOLOCK) B1 ON B1.CODCOLIGADA = A.CODCOLIGADA AND B1.CHAPA = A.CHAPA AND ISNULL(B1.DATAREFERENCIA, B1.DATA) = A.DATA AND B1.STATUS NOT IN ('T')
							  INNER JOIN AJORHOR (NOLOCK) J1 ON J1.CODCOLIGADA = A.CODCOLIGADA AND J1.CODHORARIO = (SELECT CODHORARIO FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
										  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA))
							  LEFT  JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
						  
							  
						  WHERE
								  A.CODCOLIGADA = P.COLIGADA
							  AND A.DATA BETWEEN P.INICIO AND P.FIM
							  ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
							   ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							   AND (SELECT registro_bri FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
							  
						  GROUP BY
							  A.CODCOLIGADA,
							  A.CHAPA,
							  A.DATA,
							  B.NOME,
							  B.CODSECAO,
							  C.DESCRICAO,
							  B.CODFUNCAO,
							  D.NOME
						  
						  HAVING
								  MIN(B1.BATIDA) = MIN(J1.BATINICIO)
							  AND MAX(B1.BATIDA) = MAX(J1.BATFIM)
										

						UNION ALL
						/***************************************************************************************************************/
						/* TROCA DE ESCALA MENOS DE 6 MESES    [tipo de retorno COMPLEMENTO]                                           */
						/***************************************************************************************************************/ 
						 
						  SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , ULT_MUD_DATA DATA
							  , NULL HORARIO
							  , 'troca_menor_6_meses' OCORRENCIA
							  , CONVERT(VARCHAR(10),ULT_MUD_DATA,103) + ' ['+ULT_MUD_CODHOR+']' + ' - ' + CONVERT(VARCHAR(10),PEN_MUD_DATA,103) + ' [' + PEN_MUD_CODHOR + '] ' + CONVERT(VARCHAR(2),DATEDIFF(DAY, PEN_MUD_DATA, ULT_MUD_DATA))  COMPLEMENTO
							  , DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) VALOR
							  
							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
						  FROM (
						  
							SELECT
								  A.CODCOLIGADA
								, A.CHAPA
								, A.NOME
								, A.CODFUNCAO
								, B.NOME NOMEFUNCAO
								, A.CODSECAO
								, C.DESCRICAO NOMESECAO
								, (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ULT_MUD_DATA
								
								, (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA  AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ) ULT_MUD_CODHOR
							  
						  		,(SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
						  			AND DTMUDANCA >
										  			(SELECT 
										  				CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
										  					  		ELSE MAX(DTMUDANCA) END
										  			 FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
										  				AND DTMUDANCA <=
															  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																  	AND DTMUDANCA <> 
																  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																   	AND CODHORARIO <> 
																	  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
										  				AND CODHORARIO <> 
										  					  (SELECT 
										  					  		CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
										  					  		ELSE CODHORARIO END
										  					  	FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
											  					  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																	  	AND DTMUDANCA <> 
																	  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																	   	AND CODHORARIO <> 
																		  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																		  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
															 )
										  			)	
						  	   	 ) PEN_MUD_DATA
			  	
			  					,(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
				   				    (SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
							  			AND DTMUDANCA >
											  			(SELECT 
											  				CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
											  					  		ELSE MAX(DTMUDANCA) END
											  			 FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
											  				AND DTMUDANCA <=
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																	  	AND DTMUDANCA <> 
																	  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																	   	AND CODHORARIO <> 
																		  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																		  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
											  				AND CODHORARIO <> 
											  					  (SELECT 
											  					  		CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
											  					  		ELSE CODHORARIO END
											  					  	FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
												  					  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																		  	AND DTMUDANCA <> 
																		  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																		   	AND CODHORARIO <> 
																			  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																			  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
																 )
											  			)	
							  	   	)
						  	   	) PEN_MUD_CODHOR

							FROM
								PFUNC A
								INNER JOIN PFUNCAO (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
								INNER JOIN PSECAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
								INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
						  
							WHERE
									A.CODSITUACAO NOT IN ('D')
								AND (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) BETWEEN P.INICIO AND P.FIM
								AND (SELECT troca_menor6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								{$qr_secao} 
								{$where_filial}
							  
						  )X
							 WHERE 
								DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) < 6 
								AND DATEDIFF(DAY, PEN_MUD_DATA, ULT_MUD_DATA) > 3
						  
						  
						  
						  
						  
						UNION ALL
						/***************************************************************************************************************/
						/* TROCA DE ESCALA MENOR DE 10 DIAS    [tipo de retorno VALOR]                                                 */
						/***************************************************************************************************************/					  
						  /*******SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , DTMUDANCA DATA
							  , NULL HORARIO
							  , 'troca_menor_10_dias' OCORRENCIA
							  , NULL COMPLEMENTO
							  , (DATEDIFF(DAY, DATAALTERACAO, DTMUDANCA)) VALOR


							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
						
						  FROM(
							  SELECT 
							  	  A.CODCOLIGADA 
							  	, A.CHAPA
							  	, B.NOME
							  	, B.CODFUNCAO
							  	, C.NOME NOMEFUNCAO
							  	, B.CODSECAO
							  	, D.DESCRICAO NOMESECAO
							  	, A.DTMUDANCA
							  	, A.CODHORARIO
							  	, A.DATAALTERACAO 
							  	, (SELECT M.DTMUDANCA FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA = 
							  			(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA < A.DTMUDANCA) ) PEN_DTMUDANCA
							    , ISNULL((SELECT M.CODHORARIO FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA = 
							  				(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA < A.DTMUDANCA) ),A.CODHORARIO) PEN_CODHORARIO
							  	
	
	
							  FROM PFHSTHOR A
							  	INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							  	LEFT  JOIN PFUNCAO (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODFUNCAO = C.CODIGO
							  	LEFT  JOIN PSECAO  (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODSECAO = D.CODIGO
							  	INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
							  WHERE 
							  	B.CODSITUACAO NOT IN ('D')
							  AND A.DTMUDANCA BETWEEN P.INICIO AND P.FIM
								AND (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
								".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
							   	".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
						  )X 
							WHERE (DATEDIFF(DAY, DATAALTERACAO, DTMUDANCA)) = 3
							AND CODHORARIO <> PEN_CODHORARIO
						****/
						SELECT
							CODCOLIGADA
							, CHAPA
							, NOME
							, COD_FCO CODFUNCAO
							, FUNCAO NOMEFUNCAO
							, COD_SEC CODSECAO
							, SEC NOMESECAO
							, DTMUDANCA
							, DESCRICAO
							,'troca_menor_10_dias' OCORRENCIA
							, NULL COMPLEMENTO
							, DATEDIFF(DAY,DTMUDANCA_ANTERIOR,DTMUDANCA) VALOR
							, NULL BASE
							, NULL HTRAB
							, NULL EXTRAAUTORIZADO
							, NULL COMPENSADO
							, NULL DESCANSO
							, NULL FERIADO
							,'RM' SISTEMA  
						FROM(
						SELECT
							B.CODCOLIGADA,
							B.CHAPA,
							B.NOME,
							D.CODIGO COD_SEC,
							D.DESCRICAO SEC,
							E.CODIGO COD_FCO,
							E.NOME FUNCAO,
							A.DTMUDANCA,
							ISNULL(LAG(A.DTMUDANCA) OVER (PARTITION BY A.CODCOLIGADA, A.CHAPA ORDER BY A.CODCOLIGADA, A.CHAPA, A.DTMUDANCA),A.DTMUDANCA) AS DTMUDANCA_ANTERIOR,
							ISNULL(LAG(C.DESCRICAO) OVER (PARTITION BY A.CODCOLIGADA, A.CHAPA ORDER BY A.CODCOLIGADA, A.CHAPA, A.DTMUDANCA),A.DTMUDANCA) AS HORARIO_ANTERIOR,
							A.CODHORARIO,
							C.DESCRICAO,
							ROW_NUMBER() OVER (PARTITION BY A.CODCOLIGADA, A.CHAPA ORDER BY A.CODCOLIGADA, A.CHAPA, A.DTMUDANCA) AS LINHA
							,P.INICIO
							,P.FIM
						FROM 
							PFHSTHOR A (NOLOCK)
							LEFT JOIN PFUNC B (NOLOCK) ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
							LEFT JOIN AHORARIO C (NOLOCK) ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODHORARIO
							LEFT JOIN PSECAO D (NOLOCK) ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
							INNER JOIN PAR P ON 1=1
							LEFT  JOIN PFUNCAO (NOLOCK) E ON B.CODCOLIGADA = E.CODCOLIGADA AND B.CODFUNCAO = E.CODIGO
							
						WHERE
							A.CODCOLIGADA = P.COLIGADA
							AND B.CODSITUACAO NOT IN ('D')
							AND A.DTMUDANCA BETWEEN P.INICIO AND P.FIM
							AND (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
							".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
							".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."

						)X

						WHERE
								DATEDIFF(DAY,DTMUDANCA_ANTERIOR,DTMUDANCA) = 3
							AND DTMUDANCA BETWEEN INICIO AND FIM
							AND HORARIO_ANTERIOR <> CODHORARIO
						  
						  
						UNION ALL
						/***************************************************************************************************************/
						/* TROCA DE ESCALA SEM ANEXO DO TERMO ADITIVO    [tipo de retorno VALOR]                                       */
						/***************************************************************************************************************/
									
						  SELECT 
								B.CODCOLIGADA
							  , B.CHAPA
							  , B.NOME
							  , B.CODFUNCAO
							  , C.NOME NOMEFUNCAO
							  , B.CODSECAO
							  , D.DESCRICAO NOMESECAO
							  , A.datamudanca DATA
							  , NULL HORARIO
							  , 'pendente_termo_aditivo' OCORRENCIA
							  , NULL COMPLEMENTO
							  , A.id VALOR
							  
							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
						  FROM 
							  ".DBPORTAL_BANCO."..zcrmportal_escala A
							  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
							  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
							  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
							  INNER JOIN PAR              P ON P.COLIGADA = A.coligada
						  WHERE
								  CAST(A.documento AS VARCHAR(10)) IS NULL
							  AND A.datamudanca BETWEEN P.INICIO AND P.FIM
							  AND (SELECT req_troca FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
							   ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)." 
							 
							 ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							 
							  
						UNION ALL
						/***************************************************************************************************************/
						/* SOBREAVISO     [tipo de retorno VALOR]                                                                      */
						/***************************************************************************************************************/
						  
						  SELECT 
								B.CODCOLIGADA
							  , B.CHAPA
							  , B.NOME
							  , B.CODFUNCAO
							  , C.NOME NOMEFUNCAO
							  , B.CODSECAO
							  , D.DESCRICAO NOMESECAO
							  , CONVERT(DATE, A.dtcad, 3) DATA
							  , NULL HORARIO
							  , 'sobreaviso' OCORRENCIA
							, NULL COMPLEMENTO
							  , SUM(A.HORAS) VALOR

							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
							  
							  --,DATEADD(DAY,6,P.INICIO) INICIO
							  --,DATEADD(DAY,6,P.FIM) FIM
							  
						  FROM 
							  ".DBPORTAL_BANCO."..zcrmportal_substituicao_sobreaviso (NOLOCK) A
								  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
								  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
								  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
								  INNER JOIN PAR              P ON A.COLIGADA = P.COLIGADA
							  
						  WHERE 
								A.dtcad BETWEEN DATEADD(DAY,6,P.INICIO) AND DATEADD(DAY,6,P.FIM)
							  AND A.situacao = 2
							  AND (SELECT sobreaviso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
							   ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
							    ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							  
						  GROUP BY 
							  B.CODCOLIGADA,
							  B.CHAPA,
							  B.NOME,
							  B.CODFUNCAO,
							  C.NOME,
							  B.CODSECAO,
							  D.DESCRICAO,
							  CONVERT(DATE, A.dtcad, 3)
							
						  HAVING 
							  SUM(A.horas) > 5760
						  
						  
										
						UNION ALL
						/***************************************************************************************************************/
						/* Excesso de Abono Gestor (Superior a 5 dias consecutivos)    [tipo de retorno COMPLEMENTO]                   */
						/***************************************************************************************************************/ 

						  SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , DATA
							  , NULL HORARIO
							  , 'excesso_abono_gestor' OCORRENCIA
							  , '5 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)   COMPLEMENTO
							  , (UM + DOIS + TRES + QUATRO + CINCO) VALOR
							  
							  , BASE
							  , HTRAB
							  , EXTRAAUTORIZADO
							  , COMPENSADO
							  , DESCANSO
							  , FERIADO,
							'RM' SISTEMA
						  FROM(
						  
							  SELECT
									A.CODCOLIGADA
								  , A.CHAPA
								  , B.NOME
								  , B.CODFUNCAO
								  , C.NOME NOMEFUNCAO
								  , B.CODSECAO
								  , D.DESCRICAO NOMESECAO
								  , A.DATA
								  , P.ABONO
								  , A.BASE
								, A.HTRAB
								, A.EXTRAAUTORIZADO
								, A.COMPENSADO
								, A.DESCANSO
								, A.FERIADO
						  
								  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.CODABONO = P.ABONO AND A.BASE > 0)>0 THEN 1 ELSE 0 END)UM
								  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 1) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) DOIS
								  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 2) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) TRES
																											
							  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 3) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) QUATRO                                                                            
							  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 4) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) CINCO                                                                           
							  , (SELECT DATA 
								FROM (
								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
								  )X WHERE seq = 4) DATAFINAL
								  
							  FROM AAFHTFUN (NOLOCK) A
								  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
								  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
								  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
								  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
								  
							  WHERE
								  A.DATA BETWEEN P.INICIO AND P.FIM
								  AND (SELECT excesso_gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								  ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
								  ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
								  
						  
						  )X
						   WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1


						  
						UNION ALL
						/***************************************************************************************************************/
						/* Trabalho superior à 6 (seis) dias consecutivos sem folga  [tipo de retorno COMPLEMENTO]                     */
						/***************************************************************************************************************/

						  SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , DATA
							  , HORARIO
							  , 'trabalho_6dias' OCORRENCIA
							  , CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)  COMPLEMENTO
							  , (UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) VALOR
							
								, BASE
							  , HTRAB
							  , EXTRAAUTORIZADO
							  , COMPENSADO
							  , DESCANSO
							  , FERIADO,
							'RM' SISTEMA
						  FROM(
						  
							  SELECT
									A.CODCOLIGADA
								  , A.CHAPA
								  , B.NOME
								  , B.CODFUNCAO
								  , C.NOME NOMEFUNCAO
								  , B.CODSECAO
								  , D.DESCRICAO NOMESECAO
								  , A.DATA
								  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.HTRAB >0)UM
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.HTRAB >0)DOIS
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.HTRAB >0)TRES
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.HTRAB >0)QUATRO
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.HTRAB >0)CINCO
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-5 = B.DATA AND B.HTRAB >0)SEIS
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)SETE
								  
								  ,(SELECT B.DATA FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)DATAFINAL
								  
								  , A.BASE
								, A.HTRAB
								, A.EXTRAAUTORIZADO
								, A.COMPENSADO
								, A.DESCANSO
								, A.FERIADO
								  
							  FROM AAFHTFUN (NOLOCK) A
								  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
								  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
								  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
								  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
								  
							  WHERE
								  A.DATA BETWEEN P.INICIO AND P.FIM
								  AND (SELECT trabalho_sup6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								   ".str_replace('A.CODSECAO', 'B.CODSECAO', $qr_secao)."
									".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
								  
								  
						  )X
							WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1 AND SEIS = 1 AND SETE = 1


/*************************************************************************************
							 * ocorrencias de outros sistema
							 ************************************************************************************/		
								UNION ALL

								SELECT 
									CAST(a.codcoligada AS INT) CODCOLIGADA,
									a.chapa COLLATE Latin1_General_CI_AS CHAPA,
									b.NOME,
									b.CODFUNCAO,
									c.NOME NOMEFUNCAO,
									b.CODSECAO,
									d.DESCRICAO NOMESECAO,
									a.data DATA,
									NULL HORARIO,
									a.ocorrencia OCORRENCIA,
									a.resultado COLLATE Latin1_General_CI_AS COMPLEMENTO,
									NULL VALOR
									, NULL BASE
									, NULL HTRAB
									, NULL EXTRAAUTORIZADO
									, NULL COMPENSADO
									, NULL DESCANSO
									, NULL FERIADO
									, a.sistema SISTEMA
								FROM 
									".DBPORTAL_BANCO."..zcrmportal_ocorrencia a (NOLOCK)
									INNER JOIN PFUNC b (NOLOCK) ON b.CHAPA COLLATE Latin1_General_CI_AS = a.chapa AND b.CODCOLIGADA = a.codcoligada COLLATE Latin1_General_CI_AS
									INNER JOIN PFUNCAO c (NOLOCK) ON c.CODIGO = b.CODFUNCAO AND c.CODCOLIGADA = b.CODCOLIGADA
									INNER JOIN PSECAO d (NOLOCK) ON d.CODIGO = b.CODSECAO AND d.CODCOLIGADA = b.CODCOLIGADA
									INNER JOIN PAR p ON p.COLIGADA = CAST(a.codcoligada AS INT) 
								WHERE 
										a.codcoligada = {$this->coligada} 
									AND a.sistema NOT IN ('RM')
									AND a.data BETWEEN p.INICIO AND p.FIM
									".str_replace('A.CODSECAO', 'b.CODSECAO', $qr_secao)."
									".(($ja_justificados == NULL) ? ' AND a.codmotivo IS NULL ' : ' AND a.codmotivo IS NOT NULL ')."
									AND 1 = 
										CASE
											WHEN a.ocorrencia = 'excesso_abono_gestor' THEN (SELECT excesso_gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'extra_permitido' THEN (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'extra' THEN 1
											WHEN a.ocorrencia = 'jornada' THEN 1
											WHEN a.ocorrencia = 'trabalho_dsr_folga' THEN 1
											WHEN a.ocorrencia = 'trabalho_dsr_folga_descanso' THEN (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'trabalho_ferias_afastamento' THEN 1
											WHEN a.ocorrencia = 'registro_manual' THEN (SELECT registro_manual FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'trabalho_6dias' THEN (SELECT trabalho_sup6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'interjornada' THEN (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'registro_britanico' THEN (SELECT registro_bri FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'sobreaviso' THEN (SELECT sobreaviso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'troca_menor_6_meses' THEN (SELECT troca_menor6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'troca_menor_10_dias' THEN (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'pendente_termo_aditivo' THEN (SELECT req_troca FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											ELSE 1
										END

					)X

					[WHERE]
					 

					ORDER BY
					X.NOME,
					X.DATA,
					X.OCORRENCIA
        ";
		
		//echo '<textarea>'.$query. '</textarea>';
		//exit;
        // MONTA SQL PARA VISUALIZAÇÃO DAS OCORRÊNCIAS
        $SELECT = "
            SELECT
                X.*,
                (SELECT Z.codmotivo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) codmotivo,
                (SELECT Z.descricao_motivo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) descricao_motivo,
                (SELECT Z.ocorrencia FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) log_ocorrencia,
                (SELECT Z.observacao FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) observacao,
                (SELECT Z.gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) gestor,
                (SELECT Z.id_anexo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA) id_anexo,
                (SELECT Y.file_name FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_anexo Y (NOLOCK) WHERE Y.id = (SELECT Z.id_anexo FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z (NOLOCK) WHERE Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA)) file_name,

				(
				  SELECT max(CAST(BB.descricao AS VARCHAR)) FROM ".DBPORTAL_BANCO."..zcrmportal_ponto_justificativa_func AA 
					INNER JOIN ".DBPORTAL_BANCO."..zcrmportal_ponto_motivos BB ON AA.justificativa = BB.id AND AA.coligada = BB.codcoligada WHERE AA.coligada = X.codcoligada AND AA.dtponto = X.data AND AA.chapa = X.chapa
				) justificativa_extra
				
        ";
        $WHERE = " {$where_ja_justificados} ";

        $array_de = array('[SELECT]', '[WHERE]');
        $array_para = array($SELECT, $WHERE);
        $query = str_replace($array_de, $array_para, $query);
// echo '<pre>';
// echo $query;
// exit();

        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }


    // -------------------------------------------------------
    // Lista total de ocorrencias
    // -------------------------------------------------------
    public function ListarOcorrenciaJustificadaTotal($dados){

        $data_inicio = $dados['data_inicio'] ?? null;
        $data_fim = $dados['data_fim'] ?? null;
        $filial = $dados['filial'];

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

        $filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			// foreach($objFuncLider as $idx => $value){
			// 	$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			// }
			// $filtro_secao_lider = " B.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";

			//------------------------------------------------
			$inGrupo 	= [];
			$idGrupo 	= 1;
			$linha 		= 1;
			$filtro_secao_lider = "";
			foreach($objFuncLider as $ChapaLider){
				$grupo[$idGrupo][] = $ChapaLider['chapa'];
				if($linha == 800){
					$idGrupo++;
					$linha=0;
				}
				$linha++;
			}
			
			foreach($grupo as $in){
				$filtro_secao_lider .= " B.CHAPA IN ('".implode("','", $in)."') OR ";
			}

			$filtro_secao_lider = "(".rtrim($filtro_secao_lider, " OR ").") OR ";
			//------------------------------------------------
		}
		
		//-----------------------------------------
		// filtro das seções que o gestor pode ver
		//-----------------------------------------
		$secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer();
		$filtro_secao_gestor = "";
		
		if($secoes){
			$codsecoes = "";
			foreach($secoes as $ids => $Secao){
				$codsecoes .= "'".$Secao['codsecao']."',";									   
			}
			$filtro_secao_gestor = " B.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		$in_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

		$mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('PONTO_OCORRENCIA_RH');
		if($mAcesso->VerificaPerfil('GLOBAL_RH')) $perfil_rh = true;
        if($perfil_rh) $in_secao = "";

        $where_filial = (strlen(trim($filial)) > 0) ? " AND B.CODFILIAL = '{$filial}' " : "";
        
        // filtro por seção
        if(is_array($dados['secao'] ?? "")){
            $in_secao = "";
            foreach($dados['secao'] as $key => $CodSecao){
                $in_secao .= "'{$CodSecao}',";
            }
            $in_secao = " AND B.CODSECAO IN (".rtrim($in_secao, ',').") ";
        }

        $query = "
            SELECT 
                SUM(CASE WHEN A.ocorrencia = 'extra_permitido' THEN 1 ELSE 0 END) extra_permitido,
                SUM(CASE WHEN A.ocorrencia = 'extra' THEN 1 ELSE 0 END) extra,
                SUM(CASE WHEN A.ocorrencia = 'jornada' THEN 1 ELSE 0 END) jornada,
                SUM(CASE WHEN A.ocorrencia = 'trabalho_dsr_folga' THEN 1 ELSE 0 END) trabalho_dsr_folga,
				SUM(CASE WHEN A.ocorrencia = 'trabalho_dsr_folga_descanso' THEN 1 ELSE 0 END) trabalho_dsr_folga_descanso,
                SUM(CASE WHEN A.ocorrencia = 'trabalho_ferias_afastamento' THEN 1 ELSE 0 END) trabalho_ferias_afastamento,
                SUM(CASE WHEN A.ocorrencia = 'registro_manual' THEN 1 ELSE 0 END) registro_manual,
                SUM(CASE WHEN A.ocorrencia = 'trabalho_6dias' THEN 1 ELSE 0 END) trabalho_6dias,
                SUM(CASE WHEN A.ocorrencia = 'excesso_abono_gestor' THEN 1 ELSE 0 END) excesso_abono_gestor,
                SUM(CASE WHEN A.ocorrencia = 'interjornada' THEN 1 ELSE 0 END) interjornada,
				SUM(CASE WHEN A.ocorrencia = 'intrajornada' THEN 1 ELSE 0 END) intrajornada,
                SUM(CASE WHEN A.ocorrencia = 'registro_britanico' THEN 1 ELSE 0 END) registro_britanico,
                SUM(CASE WHEN A.ocorrencia = 'sobreaviso' THEN 1 ELSE 0 END) sobreaviso,
                SUM(CASE WHEN A.ocorrencia = 'troca_menor_6_meses' THEN 1 ELSE 0 END) troca_menor_6_meses,
                SUM(CASE WHEN A.ocorrencia = 'troca_menor_10_dias' THEN 1 ELSE 0 END) troca_menor_10_dias,
                SUM(CASE WHEN A.ocorrencia = 'pendente_termo_aditivo' THEN 1 ELSE 0 END) pendente_termo_aditivo,
                SUM(1) total
            FROM 
                zcrmportal_ocorrencia A
                INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS AND A.codcoligada = B.CODCOLIGADA
            WHERE
                A.codcoligada = '{$this->coligada}'
                AND A.data BETWEEN '{$data_inicio}' AND '{$data_fim}'
                AND A.codmotivo IS NOT NULL
                {$in_secao}
                {$where_filial}
        ";
		//echo '<textarea>'. $query.'</textarea>';
		//exit;
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista abonos totvs
    // -------------------------------------------------------
    public function ListarOcorrenciaAbono(){

        $query = " SELECT CODIGO, DESCRICAO FROM AABONO WHERE CODCOLIGADA = '{$this->coligada}' ORDER BY DESCRICAO ASC ";
        $result = $this->dbrm->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

    // -------------------------------------------------------
    // Lista motivo ocorrências
    // -------------------------------------------------------
    public function ListarOcorrenciaMotivo(){

        $query = " SELECT * FROM zcrmportal_ocorrencia_motivo ORDER BY DESCRICAO ASC ";

        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;


    }

    // -------------------------------------------------------
    // Cadastrar ocorrência justificada
    // -------------------------------------------------------
    public function CadastrarOcorrenciaJustificativa($dados){

        $coligada = $this->coligada ?? null;
        $chapa = $dados['chapa'] ?? null;
        $data = $dados['data'] ?? null;
        $codmotivo = $dados['codmotivo'] ?? null;
        $ocorrencia = $dados['ocorrencia'] ?? null;
		$observacao = $dados['observacao'] ?? null;
        $descricao_motivo = $dados['descricao_motivo'] ?? '';

		$file_name = "NULL";
		$file_type = "NULL";
		$file_size = "NULL";
		$file_data = "NULL";
		$id_anexo  = "NULL";

        if($coligada == null) return responseJson('error', '<b>Coligada</b> não informada.');
        if($chapa == null) return responseJson('error', '<b>Chapa</b> não informada.');
        if($data == null) return responseJson('error', '<b>Data</b> não informada.');
        if($codmotivo == null) return responseJson('error', '<b>Detalhamento da ocorrência</b> não informada.');
        if($codmotivo == 'O' && $descricao_motivo == '') return responseJson('error', '<b>Descrição da Justificativa</b> não informada.');
        if($ocorrencia == null) return responseJson('error', '<b>Tipo de Ocorrência</b> não informada.');

        $mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('PONTO_OCORRENCIA_RH');
		if($mAcesso->VerificaPerfil('GLOBAL_RH')) $perfil_rh = true;

		

		if(is_array($dados['file'])){
			if(is_array($dados['file']['anexo'] ?? '')){
				$file_name = $dados['file']['anexo']['name'];
				$file_type = $dados['file']['anexo']['type'];
				$file_size = $dados['file']['anexo']['size'];
				$file_data = base64_encode(file_get_contents($dados['file']['anexo']['tmp_name']));

				$this->dbportal->query(
					"INSERT INTO zcrmportal_ocorrencia_anexo (coligada, usucad, dtcad, file_type, file_size, file_name, file_data) 
						VALUES 
					('{$this->coligada}', {$this->log_id}, '{$this->now}', '{$file_type}', '{$file_size}', '{$file_name}', '{$file_data}')"
				);

				$id_anexo = $this->dbportal->insertID();

			}
		}

		$ArrayOcorrencias = explode(";", $ocorrencia);

		for($i=0; $i<count($ArrayOcorrencias);$i++){
			$ocorrencia = $ArrayOcorrencias[$i];
			if(strlen(trim($ocorrencia)) <= 0) continue;
			// verifica se já existe a justificativa
			$check = $this->dbportal->query(" SELECT * FROM zcrmportal_ocorrencia WHERE codcoligada = '{$coligada}' AND chapa = '{$chapa}' AND data = '".dtEn($data)."' AND ocorrencia = '{$ocorrencia}' ");
			if($check->getNumRows() > 0){

				$anexo = ($id_anexo != 'NULL') ? " , id_anexo = '{$id_anexo}' " : "";

				// grava log de alteração
				$this->dbportal->query("
					INSERT INTO zcrmportal_ocorrencia_log_alteracao 
					SELECT *, '{$this->log_id}', '{$this->now}' FROM zcrmportal_ocorrencia WHERE codcoligada = '{$coligada}' AND chapa = '{$chapa}' AND data = '".dtEn($data)."' AND ocorrencia = '{$ocorrencia}'
				");

				// atualiza
				$query = "
					UPDATE
						zcrmportal_ocorrencia
						
					SET
						codmotivo = '{$codmotivo}',
						descricao_motivo = ".((strlen(trim($descricao_motivo)) <= 0) ? "NULL" : "'{$descricao_motivo}'").",
						usualt = '{$this->log_id}',
						dtalt = '".date('Y-m-d H:i:s')."'
						".((!$perfil_rh) ? " , gestor = 1 " : "")."
						".(($observacao) ? " , observacao = '".$observacao."' " : "")."
						{$anexo}
					WHERE
							codcoligada = '{$coligada}' 
						AND chapa = '{$chapa}' 
						AND data = '".dtEn($data)."' 
						AND ocorrencia = '{$ocorrencia}'
						--AND codmotivo IS NULL
				";
				$this->dbportal->query($query);

				/*return ($this->dbportal->affectedRows() > 0) 
					? responseJson('success', 'Detalhamento da ocorrência cadastrada com sucesso.')
					: responseJson('error', 'Falha ao cadastrar Detalhamento da ocorrência.');*/
			}else{

				$query = " INSERT INTO zcrmportal_ocorrencia 
					(id_anexo, codcoligada, chapa, data, codmotivo, descricao_motivo, usucad, dtcad, ocorrencia, gestor ".(($observacao) ? " , observacao " : "").")
						VALUES
					({$id_anexo}, '{$coligada}', '{$chapa}', '".dtEn($data)."', '{$codmotivo}', ".((strlen(trim($descricao_motivo)) <= 0) ? "NULL" : "'{$descricao_motivo}'").", '{$this->log_id}', '".date('Y-m-d H:i:s')."', '{$ocorrencia}', ".((!$perfil_rh) ? 1 : "NULL")." ".(($observacao) ? " , '".$observacao."' " : "").")
				";
				$this->dbportal->query($query);
			}
		}

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Detalhamento da ocorrência cadastrada com sucesso.')
                : responseJson('error', 'Falha ao cadastrar Detalhamento da ocorrência.');

    }

    // -------------------------------------------------------
    // Cadastrar observacao justificada
    // -------------------------------------------------------
    public function CadastrarOcorrenciaObservacao($dados){

        $coligada = $this->coligada ?? null;
        $chapa = $dados['chapa'] ?? null;
        $data = $dados['data'] ?? null;
        $ocorrencia = $dados['ocorrencia'] ?? null;
        $observacao = $dados['observacao'] ?? '';

        if($coligada == null) return responseJson('error', '<b>Coligada</b> não informada.');
        if($chapa == null) return responseJson('error', '<b>Chapa</b> não informada.');
        if($data == null) return responseJson('error', '<b>Data</b> não informada.');
        if($ocorrencia == null) return responseJson('error', '<b>Tipo de Ocorrência</b> não informada.');

        $mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('PONTO_OCORRENCIA_RH');
		if($mAcesso->VerificaPerfil('GLOBAL_RH')) $perfil_rh = true;

        // verifica se já existe a justificativa
        $check = $this->dbportal->query(" SELECT * FROM zcrmportal_ocorrencia WHERE codcoligada = '{$coligada}' AND chapa = '{$chapa}' AND data = '".dtEn($data)."' AND ocorrencia = '{$ocorrencia}' ");
        if($check->getNumRows() > 0){

            // atualiza
            $query = "
                UPDATE
                    zcrmportal_ocorrencia
                    
                SET
                    observacao = ".((strlen(trim($observacao)) <= 0) ? "NULL" : "'{$observacao}'").",
                    usualt = '{$this->log_id}',
                    dtalt = '".date('Y-m-d H:i:s')."'
                    ".((!$perfil_rh) ? " , gestor = 1 " : "")."

                WHERE
                        codcoligada = '{$coligada}' 
                    AND chapa = '{$chapa}' 
                    AND data = '".dtEn($data)."' 
                    AND ocorrencia = '{$ocorrencia}'
            ";
            $this->dbportal->query($query);

            return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Observação cadastrada com sucesso.')
                : responseJson('error', 'Falha ao cadastrar observação.');
        }

        $query = " INSERT INTO zcrmportal_ocorrencia 
            (codcoligada, chapa, data, observacao, usucad, dtcad, ocorrencia, gestor)
                VALUES
            ('{$coligada}', '{$chapa}', '".dtEn($data)."', ".((strlen(trim($observacao)) <= 0) ? "NULL" : "'{$observacao}'").", '{$this->log_id}', '".date('Y-m-d H:i:s')."', '{$ocorrencia}', ".((!$perfil_rh) ? 1 : "NULL").")
        ";
        $this->dbportal->query($query);

        return ($this->dbportal->affectedRows() > 0) 
                ? responseJson('success', 'Observação cadastrada com sucesso.')
                : responseJson('error', 'Falha ao cadastrar observação.');

    }

    // -------------------------------------------------------
    // Cadastrar ocorrência justificada
    // -------------------------------------------------------
    public function CadastrarOcorrenciaJustificativaColetiva($dados){

        $data['codmotivo'] = $dados['codmotivo'];
        $data['descricao_motivo'] = $dados['outro_motivo'];
		$data['observacao'] = $dados['informacao_complementar'];
		$data['file'] = $dados['file'];
		
        $array = explode(';', rtrim($dados['dados'], ';'));

        for($a = 0; $a < count($array); $a++){

            $dadosArray = explode('|', $array[$a]);

            $data['data'] = $dadosArray[0];
            $data['chapa'] = $dadosArray[1];
            $data['ocorrencia'] = $dadosArray[2];
			

            $this->CadastrarOcorrenciaJustificativa($data);

        }

        return responseJson('success', 'Justificativa cadastrada com sucesso.');

    }

    // -------------------------------------------------------
    // Cadastrar informação complementar
    // -------------------------------------------------------
    public function CadastrarInformacaoComplementarColetiva($dados){
        
        $informacao_complementar = $dados['informacao_complementar'];
        $array = explode(';', rtrim($dados['dados'], ';'));

        for($a = 0; $a < count($array); $a++){

            $dadosArray = explode('|', $array[$a]);

            $data['data'] = $dadosArray[0];
            $data['chapa'] = $dadosArray[1];
            $data['ocorrencia'] = $dadosArray[2];
            $data['observacao'] = $informacao_complementar;

            $this->CadastrarOcorrenciaObservacao($data);

        }

        return responseJson('success', 'Informação complementar cadastrada com sucesso.');

    }

    // -------------------------------------------------------
    // Lista ocorrências para enviar por email
    // -------------------------------------------------------
	 public function ListarOcorrenciaTotalWorflow($dados){

        $data_inicio = $dados['data_inicio'] ?? null;
        $data_fim = $dados['data_fim'] ?? null;
        $coligada = $dados['coligada'];

        $mHierarquia = model('HierarquiaModel');
        $Secoes = $mHierarquia->ListarHierarquiaSecaoPodeVer($dados['chapa'], $coligada);

        $in_secao = " AND 1 = 2 ";
        if($Secoes){
            $in_secao = "";
            foreach($Secoes as $key =>$CodSecao){
                $in_secao .= "'{$CodSecao['codsecao']}',";
            }
            $in_secao = " AND B.CODSECAO IN (".rtrim($in_secao, ',').") ";
        }

        $mAcesso = model('AcessoModel');
        $perfil_rh = $mAcesso->VerificaPerfil('PONTO_OCORRENCIA_RH');
		if($mAcesso->VerificaPerfil('GLOBAL_RH')) $perfil_rh = true;
        // if($perfil_rh) $in_secao = "";

       
        
        // filtro por seção
        if(is_array($dados['secao'] ?? "")){
            $in_secao = "";
            foreach($dados['secao'] as $key => $CodSecao){
                $in_secao .= "'{$CodSecao}',";
            }
            $in_secao = " AND B.CODSECAO IN (".rtrim($in_secao, ',').") ";
        }

        $query = "
            SELECT 
                SUM(CASE WHEN A.ocorrencia = 'extra_permitido' THEN 1 ELSE 0 END) extra_permitido,
                SUM(CASE WHEN A.ocorrencia = 'extra' THEN 1 ELSE 0 END) extra,
                SUM(CASE WHEN A.ocorrencia = 'jornada' THEN 1 ELSE 0 END) jornada,
                SUM(CASE WHEN A.ocorrencia = 'trabalho_dsr_folga' THEN 1 ELSE 0 END) trabalho_dsr_folga,
				SUM(CASE WHEN A.ocorrencia = 'trabalho_dsr_folga_descanso' THEN 1 ELSE 0 END) trabalho_dsr_folga_descanso,
                SUM(CASE WHEN A.ocorrencia = 'trabalho_ferias_afastamento' THEN 1 ELSE 0 END) trabalho_ferias_afastamento,
                SUM(CASE WHEN A.ocorrencia = 'registro_manual' THEN 1 ELSE 0 END) registro_manual,
                SUM(CASE WHEN A.ocorrencia = 'trabalho_6dias' THEN 1 ELSE 0 END) trabalho_6dias,
                SUM(CASE WHEN A.ocorrencia = 'excesso_abono_gestor' THEN 1 ELSE 0 END) excesso_abono_gestor,
                SUM(CASE WHEN A.ocorrencia = 'interjornada' THEN 1 ELSE 0 END) interjornada,
                SUM(CASE WHEN A.ocorrencia = 'registro_britanico' THEN 1 ELSE 0 END) registro_britanico,
                SUM(CASE WHEN A.ocorrencia = 'sobreaviso' THEN 1 ELSE 0 END) sobreaviso,
                SUM(CASE WHEN A.ocorrencia = 'troca_menor_6_meses' THEN 1 ELSE 0 END) troca_menor_6_meses,
                SUM(CASE WHEN A.ocorrencia = 'troca_menor_10_dias' THEN 1 ELSE 0 END) troca_menor_10_dias,
                SUM(CASE WHEN A.ocorrencia = 'pendente_termo_aditivo' THEN 1 ELSE 0 END) pendente_termo_aditivo,
                SUM(1) total
            FROM 
                zcrmportal_ocorrencia A
                INNER JOIN ".DBRM_BANCO."..PFUNC B ON A.chapa = B.CHAPA COLLATE Latin1_General_CI_AS AND A.codcoligada = B.CODCOLIGADA
            WHERE
                A.codcoligada = '{$coligada}'
                AND A.data BETWEEN '{$data_inicio}' AND '{$data_fim}'
                AND A.codmotivo IS NOT NULL
                {$in_secao}
                
        ";
		// echo '<textarea>'.$query.'</textarea>';exit();
		
        $result = $this->dbportal->query($query);
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }
	public function Workflow(){

		$configWorkflow = self::ListaConfiguracaoWorkflow();
		if($this->now < $configWorkflow[0]->data1){
			echo 'não enviar';
			return false;
		}
			
		$hoje =date('Y-m-d');
		
		$diaAnterior =  date('Y-m-d', strtotime('-1 days', strtotime($hoje)));

		$periodo = $this->ListarPeriodoPontoworflow();
		$TiposOcorrencia = $this->ListarOcorrenciaTipoPortal()[0];
			  
		$gestores = $this->ListaGestores5();  
		foreach($periodo as $aux => $value){	  
			$key = 0;
			
			
			$Dados['data_inicio'] =  $periodo[$aux]['INICIOMENSAL'];
			
			$Dados['data_fim'] =  $periodo[$aux]['FIMMENSAL'];
			if($aux == 0){
				//$Dados['data_fim'] = $diaAnterior;
			}
			
			
			if($gestores){
			foreach ($gestores as $key => $value) {
				$Dados['coligada'] = $gestores[$key]['COLIGADA'];
				$Dados['chapa'] = $gestores[$key]['CHAPA'];
				$idx = 0;
				$resTratadas = false;
				
				
				
				$resOcorrencias = $this->ListarOcorrenciaWorflow($Dados);

				// echo'<pre>';var_dump($resOcorrencias);exit();

				$resTratadas = $this->ListarOcorrenciaTotalWorflow($Dados);
				
				 if(!empty($resOcorrencias)){
					
					$qnt_ocorrencia = array();
					$qnt_ocorrencia['extra_permitido']= 0;
					$qnt_ocorrencia['extra']= 0;
					$qnt_ocorrencia['jornada']= 0;
					$qnt_ocorrencia['trabalho_dsr_folga']= 0;
					$qnt_ocorrencia['trabalho_dsr_folga_descanso']= 0;
					$qnt_ocorrencia['trabalho_ferias_afastamento']= 0;
					$qnt_ocorrencia['registro_manual']= 0;
					$qnt_ocorrencia['trabalho_6dias']= 0;
					$qnt_ocorrencia['excesso_abono_gestor']= 0;
					$qnt_ocorrencia['interjornada']= 0;
					$qnt_ocorrencia['registro_britanico']= 0;
					$qnt_ocorrencia['sobreaviso']= 0;
					$qnt_ocorrencia['troca_menor_6_meses']= 0;
					$qnt_ocorrencia['troca_menor_10_dias']= 0;
					$qnt_ocorrencia['pendente_termo_aditivo']= 0;

					$mensagem = 'Prezado Gestor <strong>'.$gestores[$key]['nome'].'</strong> WORKFLOW 1,<br>
					<br>
					Foram observadas ocorrências de jornada de trabalho no ponto de sua equipe do periodo '. date('d/m/Y', strtotime($Dados["data_inicio"])).' até '. date('d/m/Y', strtotime($Dados["data_fim"])).', segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>, onde você deverá detalhar as respectivas situações.<br>
					<br>
					<table width="100%" bgcolor="#cccccc" cellpadding="2" cellspacing="1">';
						$mensagem .= '<thead>
                                            <tr>
                                                <th>OCORRÊNCIA</th>
                                                <th class="text-center">TRATADO</th>
                                                <th class="text-center">NÃO TRATADO</th>
                                            </tr>
											</thead>
                                        ';
										
						
						foreach($resOcorrencias as $idx => $V){
							
							switch($resOcorrencias[$idx]['OCORRENCIA']){
                                case 'extra_permitido': $qnt_ocorrencia['extra_permitido'] += 1 ; break;
                                case 'extra': $qnt_ocorrencia['extra'] += 1 ; break;
                                case 'jornada':$qnt_ocorrencia['jornada'] += 1 ; break;
                                case 'trabalho_dsr_folga':$qnt_ocorrencia['trabalho_dsr_folga'] += 1 ; break;
								case 'trabalho_dsr_folga_descanso':$qnt_ocorrencia['trabalho_dsr_folga_descanso'] += 1 ; break;
                                case 'trabalho_ferias_afastamento':$qnt_ocorrencia['trabalho_ferias_afastamento']+= 1 ; break;
								case 'registro_manual': $qnt_ocorrencia['registro_manual'] += 1 ; break;
                              
                                case 'trabalho_6dias':$qnt_ocorrencia['trabalho_6dias'] += 1  ; break;
								case 'excesso_abono_gestor':$qnt_ocorrencia['excesso_abono_gestor'] += 1  ; break;
								case 'interjornada': $qnt_ocorrencia['interjornada'] += 1 ; break;
								case 'registro_britanico': 
									$qnt_ocorrencia['registro_britanico'] += 1 ;
									break;
								case 'sobreaviso':$qnt_ocorrencia['sobreaviso'] += 1 ; break;
								case 'troca_menor_6_meses':$qnt_ocorrencia['troca_menor_6_meses'] += 1 ; break;
								case 'troca_menor_10_dias':$qnt_ocorrencia['troca_menor_10_dias'] += 1  ; break;
								case 'pendente_termo_aditivo':$qnt_ocorrencia['pendente_termo_aditivo']+= 1 ; break;
                
                            }
							
							 
							
						}
						
						
						$LogOcorrencia = array();
						$mensagem .= '<tbody>';

						if($TiposOcorrencia['excesso_gestor'] == 1){
							$LogOcorrencia['excesso_abono_gestor'] = (($resTratadas[0]["excesso_abono_gestor"]) ? $resTratadas[0]["excesso_abono_gestor"]: '0');
							$LogOcorrencia['excesso_abono_gestor_nao_tratado'] = (($qnt_ocorrencia["excesso_abono_gestor"]) ? $qnt_ocorrencia["excesso_abono_gestor"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="excesso_abono_gestor" class="col-form-label text-left pt-0 pb-0">Excesso de Abono Gestor (Superior a 5 dias consecutivos)</label></td>
                                                <td data-excesso_abono_gestor_tratado  align="center" class="text-center">'.(($resTratadas[0]["excesso_abono_gestor"]) ? $resTratadas[0]["excesso_abono_gestor"]: '0').'</td>
                                                <td data-excesso_abono_gestor_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["excesso_abono_gestor"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['extra_acima'] == 1){
							$LogOcorrencia['extra_permitido'] = (($resTratadas[0]["extra_permitido"]) ? $resTratadas[0]["extra_permitido"]: '0');
							$LogOcorrencia['extra_permitido_nao_tratado'] = (($qnt_ocorrencia["extra_permitido"]) ? $qnt_ocorrencia["extra_permitido"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="extra_permitido" class="col-form-label text-left pt-0 pb-0">Extra Acima do Permitido</label></td>
                                                <td data-extra_permitido_tratado  align="center" class="text-center">'.(($resTratadas[0]["extra_permitido"]) ? $resTratadas[0]["extra_permitido"]: '0').'</td>
                                                <td data-extra_permitido_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["extra_permitido"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['extra_especial'] == 1){
							$LogOcorrencia['extra'] = (($resTratadas[0]["extra"]) ? $resTratadas[0]["extra"]: '0');
							$LogOcorrencia['extra_nao_tratado'] = (($qnt_ocorrencia["extra"]) ? $qnt_ocorrencia["extra"]: '0');
							
							$mensagem .= '<tr>
                                                <td> <label for="extra" class="col-form-label text-left pt-0 pb-0">Extra em Escala Especifica</label></td>
                                                <td data-extra_tratado  align="center" class="text-center">'.(($resTratadas[0]["extra"]) ? $resTratadas[0]["extra"]: '0').'</td>
                                                <td data-extra_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["extra"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['interjornada'] == 1){
							$LogOcorrencia['interjornada'] = (($resTratadas[0]["interjornada"]) ? $resTratadas[0]["interjornada"]: '0');
							$LogOcorrencia['interjornada_nao_tratado'] = (($qnt_ocorrencia["interjornada"]) ? $qnt_ocorrencia["interjornada"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="interjornada" class="col-form-label text-left pt-0 pb-0">Interjornada</label></td>
                                                <td data-interjornada_tratado  align="center" class="text-center">'.(($resTratadas[0]["interjornada"]) ? $resTratadas[0]["interjornada"]: '0').'</td>
                                                <td data-interjornada_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["interjornada"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['excesso_jornada'] == 1){
							$LogOcorrencia['jornada'] = (($resTratadas[0]["jornada"]) ? $resTratadas[0]["jornada"]: '0');
							$LogOcorrencia['jornada_nao_tratado'] = (($qnt_ocorrencia["jornada"]) ? $qnt_ocorrencia["jornada"]: '0');
							$mensagem .= '<tr>
                                                <td> <label for="jornada" class="col-form-label text-left pt-0 pb-0">Jornada > 10 e 11h</label></td>
                                                <td data-jornada_tratado  align="center" class="text-center">'.(($resTratadas[0]["jornada"]) ? $resTratadas[0]["jornada"]: '0').'</td>
                                                <td data-jornada_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["jornada"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['registro_bri'] == 1){
							$LogOcorrencia['registro_britanico'] = (($resTratadas[0]["registro_britanico"]) ? $resTratadas[0]["registro_britanico"]: '0');
							$LogOcorrencia['registro_britanico_nao_tratado'] = (($qnt_ocorrencia["registro_britanico"]) ? $qnt_ocorrencia["registro_britanico"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="registro_britanico" class="col-form-label text-left pt-0 pb-0">Registro Britânico</label></td>
                                                <td data-registro_britanico_tratado  align="center" class="text-center">'.(($resTratadas[0]["registro_britanico"]) ? $resTratadas[0]["registro_britanico"]: '0').'</td>
                                                <td data-registro_britanico_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["registro_britanico"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['registro_manual'] == 1){
							$LogOcorrencia['registro_manual'] = (($resTratadas[0]["registro_manual"]) ? $resTratadas[0]["registro_manual"]: '0');
							$LogOcorrencia['registro_manual_nao_tratado'] = (($qnt_ocorrencia["registro_manual"]) ? $qnt_ocorrencia["registro_manual"]: '0');
							$mensagem .= '<tr>
                                                <td> <label for="registro_manual" class="col-form-label text-left pt-0 pb-0">Registro Manual</label></td>
                                                <td data-registro_manual_tratado  align="center" class="text-center">'.(($resTratadas[0]["registro_manual"]) ? $resTratadas[0]["registro_manual"]: '0').'</td>
                                                <td data-registro_manual_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["registro_manual"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['req_troca'] == 1){
							$LogOcorrencia['pendente_termo_aditivo'] = (($resTratadas[0]["pendente_termo_aditivo"]) ? $resTratadas[0]["pendente_termo_aditivo"]: '0');
							$LogOcorrencia['pendente_termo_aditivo_nao_tratado'] = (($qnt_ocorrencia["pendente_termo_aditivo"]) ? $qnt_ocorrencia["pendente_termo_aditivo"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="pendente_termo_aditivo" class="col-form-label text-left pt-0 pb-0">Req. troca de escala pendente termo aditivo</label></td>
                                                <td data-pendente_termo_aditivo_tratado  align="center" class="text-center">'.(($resTratadas[0]["pendente_termo_aditivo"]) ? $resTratadas[0]["pendente_termo_aditivo"]: '0').'</td>
                                                <td data-pendente_termo_aditivo_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["pendente_termo_aditivo"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['sobreaviso'] == 1){
							$LogOcorrencia['sobreaviso'] = (($resTratadas[0]["sobreaviso"]) ? $resTratadas[0]["sobreaviso"]: '0');
							$LogOcorrencia['sobreaviso_nao_tratado'] = (($qnt_ocorrencia["sobreaviso"]) ? $qnt_ocorrencia["sobreaviso"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="sobreaviso" class="col-form-label text-left pt-0 pb-0">Sobreaviso</label></td>
                                                <td data-sobreaviso_tratado  align="center" class="text-center">'.(($resTratadas[0]["sobreaviso"]) ? $resTratadas[0]["sobreaviso"]: '0').'</td>
                                                <td data-sobreaviso_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["sobreaviso"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['trabalho_dsr'] == 1){
							$LogOcorrencia['trabalho_dsr_folga'] = (($resTratadas[0]["trabalho_dsr_folga"]) ? $resTratadas[0]["trabalho_dsr_folga"]: '0');
							$LogOcorrencia['trabalho_dsr_folga_nao_tratado'] = (($qnt_ocorrencia["trabalho_dsr_folga"]) ? $qnt_ocorrencia["trabalho_dsr_folga"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="trabalho_dsr_folga" class="col-form-label text-left pt-0 pb-0">Trabalho em DSR ou Folga</label></td>
                                                <td data-trabalho_dsr_folga_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_dsr_folga"]) ? $resTratadas[0]["trabalho_dsr_folga"]: '0').'</td>
                                                <td data-trabalho_dsr_folga_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_dsr_folga"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['trabalho_dsr_descanso'] == 1){
							$LogOcorrencia['trabalho_dsr_folga_descanso'] = (($resTratadas[0]["trabalho_dsr_folga_descanso"]) ? $resTratadas[0]["trabalho_dsr_folga_descanso"]: '0');
							$LogOcorrencia['trabalho_dsr_folga_descanso_nao_tratado'] = (($qnt_ocorrencia["trabalho_dsr_folga_descanso"]) ? $qnt_ocorrencia["trabalho_dsr_folga_descanso"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="trabalho_dsr_folga" class="col-form-label text-left pt-0 pb-0">Excesso de jornada semanal</label></td>
                                                <td data-trabalho_dsr_folga_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_dsr_folga_descanso"]) ? $resTratadas[0]["trabalho_dsr_folga_descanso"]: '0').'</td>
                                                <td data-trabalho_dsr_folga_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_dsr_folga_descanso"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['trabalho_AfastFerias'] == 1){
							$LogOcorrencia['trabalho_ferias_afastamento'] = (($resTratadas[0]["trabalho_ferias_afastamento"]) ? $resTratadas[0]["trabalho_ferias_afastamento"]: '0');
							$LogOcorrencia['trabalho_ferias_afastamento_nao_tratado'] = (($qnt_ocorrencia["trabalho_ferias_afastamento"]) ? $qnt_ocorrencia["trabalho_ferias_afastamento"]: '0');
							$mensagem .= '<tr>
                                                <td> <label for="trabalho_ferias_afastamento" class="col-form-label text-left pt-0 pb-0">Trabalho em Férias ou Afastamentos</label></td>
                                                <td data-trabalho_ferias_afastamento_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_ferias_afastamento"]) ? $resTratadas[0]["trabalho_ferias_afastamento"]: '0').'</td>
                                                <td data-trabalho_ferias_afastamento_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_ferias_afastamento"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['trabalho_sup6'] == 1){
							$LogOcorrencia['trabalho_6dias'] = (($resTratadas[0]["trabalho_6dias"]) ? $resTratadas[0]["trabalho_6dias"]: '0');
							$LogOcorrencia['trabalho_6dias_nao_tratado'] = (($qnt_ocorrencia["trabalho_6dias"]) ? $qnt_ocorrencia["trabalho_6dias"]: '0');
							$mensagem .= '<tr>
                                                <td> <label for="trabalho_6dias" class="col-form-label text-left pt-0 pb-0">Trabalho superior à 6 (seis) dias consecutivos sem folga</label></td>
                                                <td data-trabalho_6dias_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_6dias"]) ? $resTratadas[0]["trabalho_6dias"]: '0').'</td>
                                                <td data-trabalho_6dias_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_6dias"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['troca_menor10'] == 1){
							$LogOcorrencia['troca_menor_10_dias'] = (($resTratadas[0]["troca_menor_10_dias"]) ? $resTratadas[0]["troca_menor_10_dias"]: '0');
							$LogOcorrencia['troca_menor_10_dias_nao_tratado'] = (($qnt_ocorrencia["troca_menor_10_dias"]) ? $qnt_ocorrencia["troca_menor_10_dias"]: '0');
							$mensagem .= '<tr>
                                                <td> <label for="troca_menor_10_dias" class="col-form-label text-left pt-0 pb-0">Troca de escala menor que 3 dias</label></td>
                                                <td data-troca_menor_10_dias_tratado  align="center" class="text-center">'.(($resTratadas[0]["troca_menor_10_dias"]) ? $resTratadas[0]["troca_menor_10_dias"]: '0').'</td>
                                                <td data-troca_menor_10_dias_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["troca_menor_10_dias"].'</td>
                                            </tr>';
						}
                        if($TiposOcorrencia['troca_menor6'] == 1){
							$LogOcorrencia['troca_menor_6_meses'] = (($resTratadas[0]["troca_menor_6_meses"]) ? $resTratadas[0]["troca_menor_6_meses"]: '0');
							$LogOcorrencia['troca_menor_6_meses_nao_tratado'] = (($qnt_ocorrencia["troca_menor_6_meses"]) ? $qnt_ocorrencia["troca_menor_6_meses"]: '0');
							$mensagem .= '<tr>
                                                <td><label for="troca_menor_6_meses" class="col-form-label text-left pt-0 pb-0">Troca de escala menor que 6 meses</label></td>
                                                <td data-troca_menor_6_meses_tratado  align="center" class="text-center">'.(($resTratadas[0]["troca_menor_6_meses"]) ? $resTratadas[0]["troca_menor_6_meses"]: '0').'</td>
                                                <td  align="center"data-troca_menor_6_meses_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["troca_menor_6_meses"].'</td>
                                            </tr>';
						}
						$mensagem .= '</tbody>';
					
					$mensagem .= '</table><br>';
					

					$htmlEmail = templateEmail($mensagem, '95%');
					
					
					$response = enviaEmail('tiago.moselli@outserv.com.br', 'Ocorrência de Ponto', $htmlEmail);
					
					// $response = enviaEmail($gestores[$key]['email'], 'Ocorrência de Ponto', $htmlEmail);
					//$response = enviaEmail('samuel.santana@crmservices.com.br', 'Ocorrência de Ponto ('.$gestores[$key]['email'].')', $htmlEmail);
					if($response){
						$this->dbportal->query("
							INSERT INTO zcrmportal_ocorrencia_workflow_log
							(coligada, descricao, nome, email, dtcad, enviado) VALUES
							('{$gestores[$key]['COLIGADA']}', '".serialize($LogOcorrencia)."', '{$gestores[$key]['nome']}', '{$gestores[$key]['email']}', '".date('Y-m-d H:i:s')."', 1)
						");
					}else{
						$this->dbportal->query("
							INSERT INTO zcrmportal_ocorrencia_workflow_log
							(coligada, descricao, nome, email, dtcad, enviado) VALUES
							('{$gestores[$key]['COLIGADA']}', '".serialize($LogOcorrencia)."', '{$gestores[$key]['nome']}', '{$gestores[$key]['email']}', '".date('Y-m-d H:i:s')."', 0)
						");
					}

					// break;
					
					
				} 
				
			}
			
			}
		
		}

		$this->dbportal
				->table('zcrmportal_ocorrencia_workflow')
				->set([
					'data1' => somarDias(dtEn($configWorkflow[0]->data1, true), $configWorkflow[0]->ciclo1).' '.m2h($configWorkflow[0]->horario1,4)
				])
				->where('codcoligada', $this->coligada)
				->update();
		
	}
		
		
		
		
		
	public function Workflow2(){

		$configWorkflow = self::ListaConfiguracaoWorkflow();
		if($this->now < $configWorkflow[0]->data2){
			echo 'não enviar';
			return false;
		}
		
		$hoje = date('Y-m-d');
		// $hoje = '2022-09-30';
		
		$diaAnterior =  date('Y-m-d', strtotime('-7 days', strtotime($hoje)));

		$periodo = $this->ListarPeriodoPontoworflow();
		$TiposOcorrencia = $this->ListarOcorrenciaTipoPortal()[0];
			  
		$gestores = $this->ListaGestores4();  
		foreach($periodo as $aux => $value){	  
				$key = 0;
				
				
				$Dados['data_inicio'] =  $periodo[$aux]['INICIOMENSAL'];
				
				$Dados['data_fim'] =  $periodo[$aux]['FIMMENSAL'];
				if($aux == 0){
					
					$Dados['data_fim'] = $diaAnterior;
				}
				if($aux == 1){
					if(strtotime($diaAnterior) < strtotime($periodo[$aux]['FIMMENSAL'])){
						$Dados['data_fim'] = $diaAnterior;
					}
					
				}
				//echo count($gestores)
				if($gestores){
				foreach ($gestores as $key => $value) {
					$Dados['coligada'] = $gestores[$key]['COLIGADA'];
					$Dados['chapa'] = $gestores[$key]['CHAPA'];
					$idx = 0;
					
					$resOcorrencias = $this->ListarOcorrenciaWorflow($Dados);

					$resTratadas = $this->ListarOcorrenciaTotalWorflow($Dados);
					
					if(!empty($resOcorrencias)){
						
						$qnt_ocorrencia = array();
						$qnt_ocorrencia['extra_permitido']= 0;
						$qnt_ocorrencia['extra']= 0;
						$qnt_ocorrencia['jornada']= 0;
						$qnt_ocorrencia['trabalho_dsr_folga']= 0;
						$qnt_ocorrencia['trabalho_dsr_folga_descanso']= 0;
						$qnt_ocorrencia['trabalho_ferias_afastamento']= 0;
						$qnt_ocorrencia['registro_manual']= 0;
						$qnt_ocorrencia['trabalho_6dias']= 0;
						$qnt_ocorrencia['excesso_abono_gestor']= 0;
						$qnt_ocorrencia['interjornada']= 0;
						$qnt_ocorrencia['registro_britanico']= 0;
						$qnt_ocorrencia['sobreaviso']= 0;
						$qnt_ocorrencia['troca_menor_6_meses']= 0;
						$qnt_ocorrencia['troca_menor_10_dias']= 0;
						$qnt_ocorrencia['pendente_termo_aditivo']= 0;
						$mensagem = 'Prezado Gestor <strong>'.$gestores[$key]['nome'].'</strong>, WORKFLOW 2<br>
						<br>
						Foram observadas ocorrências de jornada de trabalho no ponto de sua equipe do periodo '. date('d/m/Y', strtotime($Dados["data_inicio"])).' até '. date('d/m/Y', strtotime($Dados["data_fim"])).', segue abaixo link para acesso ao Portal RH <a href="'.base_url().'" target="_blank">'.base_url().'</a>, onde você deverá detalhar respectivas situações.<br>
						<br>
						<table width="100%" bgcolor="#cccccc" cellpadding="2" cellspacing="1">';
							$mensagem .= '<thead>
												<tr>
													<th>OCORRÊNCIA</th>
													<th class="text-center">TRATADO</th>
													<th class="text-center">NÃO TRATADO</th>
												</tr>
												</thead>
											';
											
							
							foreach($resOcorrencias as $idx => $V){
								
								switch($resOcorrencias[$idx]['OCORRENCIA']){
									case 'extra_permitido': $qnt_ocorrencia['extra_permitido'] += 1 ; break;
									case 'extra': $qnt_ocorrencia['extra'] += 1 ; break;
									case 'jornada':$qnt_ocorrencia['jornada'] += 1 ; break;
									case 'trabalho_dsr_folga':$qnt_ocorrencia['trabalho_dsr_folga'] += 1 ; break;
									case 'trabalho_dsr_folga_descanso':$qnt_ocorrencia['trabalho_dsr_folga_descanso'] += 1 ; break;
									case 'trabalho_ferias_afastamento':$qnt_ocorrencia['trabalho_ferias_afastamento']+= 1 ; break;
									case 'registro_manual': $qnt_ocorrencia['registro_manual'] += 1 ; break;
								
									case 'trabalho_6dias':$qnt_ocorrencia['trabalho_6dias'] += 1  ; break;
									case 'excesso_abono_gestor':$qnt_ocorrencia['excesso_abono_gestor'] += 1  ; break;
									case 'interjornada': $qnt_ocorrencia['interjornada'] += 1 ; break;
									case 'registro_britanico': 
										$qnt_ocorrencia['registro_britanico'] += 1 ;
										break;
									case 'sobreaviso':$qnt_ocorrencia['sobreaviso'] += 1 ; break;
									case 'troca_menor_6_meses':$qnt_ocorrencia['troca_menor_6_meses'] += 1 ; break;
									case 'troca_menor_10_dias':$qnt_ocorrencia['troca_menor_10_dias'] += 1  ; break;
									case 'pendente_termo_aditivo':$qnt_ocorrencia['pendente_termo_aditivo']+= 1 ; break;
					
								}
								
							}
							
							
							$mensagem .= ' <tbody>';
							
							$LogOcorrencia = array();
							if($TiposOcorrencia['excesso_gestor'] == 1){
								$LogOcorrencia['excesso_abono_gestor'] = (($resTratadas[0]["excesso_abono_gestor"]) ? $resTratadas[0]["excesso_abono_gestor"]: '0');
								$LogOcorrencia['excesso_abono_gestor_nao_tratado'] = (($qnt_ocorrencia["excesso_abono_gestor"]) ? $qnt_ocorrencia["excesso_abono_gestor"]: '0');
								$mensagem .= '<tr>
													<td><label for="excesso_abono_gestor" class="col-form-label text-left pt-0 pb-0">Excesso de Abono Gestor (Superior a 5 dias consecutivos)</label></td>
													<td data-excesso_abono_gestor_tratado  align="center" class="text-center">'.(($resTratadas[0]["excesso_abono_gestor"]) ? $resTratadas[0]["excesso_abono_gestor"]: '0').'</td>
													<td data-excesso_abono_gestor_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["excesso_abono_gestor"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['extra_acima'] == 1){
								$LogOcorrencia['extra_permitido'] = (($resTratadas[0]["extra_permitido"]) ? $resTratadas[0]["extra_permitido"]: '0');
								$LogOcorrencia['extra_permitido_nao_tratado'] = (($qnt_ocorrencia["extra_permitido"]) ? $qnt_ocorrencia["extra_permitido"]: '0');
								$mensagem .= '<tr>
													<td><label for="extra_permitido" class="col-form-label text-left pt-0 pb-0">Extra Acima do Permitido</label></td>
													<td data-extra_permitido_tratado  align="center" class="text-center">'.(($resTratadas[0]["extra_permitido"]) ? $resTratadas[0]["extra_permitido"]: '0').'</td>
													<td data-extra_permitido_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["extra_permitido"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['extra_especial'] == 1){
								$LogOcorrencia['extra'] = (($resTratadas[0]["extra"]) ? $resTratadas[0]["extra"]: '0');
								$LogOcorrencia['extra_nao_tratado'] = (($qnt_ocorrencia["extra"]) ? $qnt_ocorrencia["extra"]: '0');
								$mensagem .= '<tr>
													<td> <label for="extra" class="col-form-label text-left pt-0 pb-0">Extra em Escala Especifica</label></td>
													<td data-extra_tratado  align="center" class="text-center">'.(($resTratadas[0]["extra"]) ? $resTratadas[0]["extra"]: '0').'</td>
													<td data-extra_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["extra"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['interjornada'] == 1){
								$LogOcorrencia['interjornada'] = (($resTratadas[0]["interjornada"]) ? $resTratadas[0]["interjornada"]: '0');
								$LogOcorrencia['interjornada_nao_tratado'] = (($qnt_ocorrencia["interjornada"]) ? $qnt_ocorrencia["interjornada"]: '0');
								$mensagem .= '<tr>
													<td><label for="interjornada" class="col-form-label text-left pt-0 pb-0">Interjornada</label></td>
													<td data-interjornada_tratado  align="center" class="text-center">'.(($resTratadas[0]["interjornada"]) ? $resTratadas[0]["interjornada"]: '0').'</td>
													<td data-interjornada_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["interjornada"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['intrajornada'] == 1){
								$LogOcorrencia['intrajornada'] = (($resTratadas[0]["intrajornada"]) ? $resTratadas[0]["intrajornada"]: '0');
								$LogOcorrencia['intrajornada_nao_tratado'] = (($qnt_ocorrencia["intrajornada"]) ? $qnt_ocorrencia["intrajornada"]: '0');
								$mensagem .= '<tr>
													<td><label for="intrajornada" class="col-form-label text-left pt-0 pb-0">intrajornada</label></td>
													<td data-intrajornada_tratado  align="center" class="text-center">'.(($resTratadas[0]["intrajornada"]) ? $resTratadas[0]["intrajornada"]: '0').'</td>
													<td data-intrajornada_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["intrajornada"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['excesso_jornada'] == 1){
								$LogOcorrencia['jornada'] = (($resTratadas[0]["jornada"]) ? $resTratadas[0]["jornada"]: '0');
								$LogOcorrencia['jornada_nao_tratado'] = (($qnt_ocorrencia["jornada"]) ? $qnt_ocorrencia["jornada"]: '0');
								$mensagem .= '<tr>
													<td> <label for="jornada" class="col-form-label text-left pt-0 pb-0">Jornada > 10 e 11h</label></td>
													<td data-jornada_tratado  align="center" class="text-center">'.(($resTratadas[0]["jornada"]) ? $resTratadas[0]["jornada"]: '0').'</td>
													<td data-jornada_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["jornada"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['registro_bri'] == 1){
								$LogOcorrencia['registro_britanico'] = (($resTratadas[0]["registro_britanico"]) ? $resTratadas[0]["registro_britanico"]: '0');
								$LogOcorrencia['registro_britanico_nao_tratado'] = (($qnt_ocorrencia["registro_britanico"]) ? $qnt_ocorrencia["registro_britanico"]: '0');
								$mensagem .= '<tr>
													<td><label for="registro_britanico" class="col-form-label text-left pt-0 pb-0">Registro Britânico</label></td>
													<td data-registro_britanico_tratado  align="center" class="text-center">'.(($resTratadas[0]["registro_britanico"]) ? $resTratadas[0]["registro_britanico"]: '0').'</td>
													<td data-registro_britanico_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["registro_britanico"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['registro_manual'] == 1){
								$LogOcorrencia['registro_manual'] = (($resTratadas[0]["registro_manual"]) ? $resTratadas[0]["registro_manual"]: '0');
								$LogOcorrencia['registro_manual_nao_tratado'] = (($qnt_ocorrencia["registro_manual"]) ? $qnt_ocorrencia["registro_manual"]: '0');
								$mensagem .= '<tr>
													<td> <label for="registro_manual" class="col-form-label text-left pt-0 pb-0">Registro Manual</label></td>
													<td data-registro_manual_tratado  align="center" class="text-center">'.(($resTratadas[0]["registro_manual"]) ? $resTratadas[0]["registro_manual"]: '0').'</td>
													<td data-registro_manual_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["registro_manual"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['req_troca'] == 1){
								$LogOcorrencia['pendente_termo_aditivo'] = (($resTratadas[0]["pendente_termo_aditivo"]) ? $resTratadas[0]["pendente_termo_aditivo"]: '0');
								$LogOcorrencia['pendente_termo_aditivo_nao_tratado'] = (($qnt_ocorrencia["pendente_termo_aditivo"]) ? $qnt_ocorrencia["pendente_termo_aditivo"]: '0');
								$mensagem .= '<tr>
													<td><label for="pendente_termo_aditivo" class="col-form-label text-left pt-0 pb-0">Req. troca de escala pendente termo aditivo</label></td>
													<td data-pendente_termo_aditivo_tratado  align="center" class="text-center">'.(($resTratadas[0]["pendente_termo_aditivo"]) ? $resTratadas[0]["pendente_termo_aditivo"]: '0').'</td>
													<td data-pendente_termo_aditivo_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["pendente_termo_aditivo"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['sobreaviso'] == 1){
								$LogOcorrencia['sobreaviso'] = (($resTratadas[0]["sobreaviso"]) ? $resTratadas[0]["sobreaviso"]: '0');
								$LogOcorrencia['sobreaviso_nao_tratado'] = (($qnt_ocorrencia["sobreaviso"]) ? $qnt_ocorrencia["sobreaviso"]: '0');
								$mensagem .= '<tr>
													<td><label for="sobreaviso" class="col-form-label text-left pt-0 pb-0">Sobreaviso</label></td>
													<td data-sobreaviso_tratado  align="center" class="text-center">'.(($resTratadas[0]["sobreaviso"]) ? $resTratadas[0]["sobreaviso"]: '0').'</td>
													<td data-sobreaviso_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["sobreaviso"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['trabalho_dsr'] == 1){
								$LogOcorrencia['trabalho_dsr_folga'] = (($resTratadas[0]["trabalho_dsr_folga"]) ? $resTratadas[0]["trabalho_dsr_folga"]: '0');
								$LogOcorrencia['trabalho_dsr_folga_nao_tratado'] = (($qnt_ocorrencia["trabalho_dsr_folga"]) ? $qnt_ocorrencia["trabalho_dsr_folga"]: '0');
								$mensagem .= '<tr>
													<td><label for="trabalho_dsr_folga" class="col-form-label text-left pt-0 pb-0">Trabalho em DSR ou Folga</label></td>
													<td data-trabalho_dsr_folga_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_dsr_folga"]) ? $resTratadas[0]["trabalho_dsr_folga"]: '0').'</td>
													<td data-trabalho_dsr_folga_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_dsr_folga"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['trabalho_dsr_descanso'] == 1){
								$LogOcorrencia['trabalho_dsr_folga_descanso'] = (($resTratadas[0]["trabalho_dsr_folga_descanso"]) ? $resTratadas[0]["trabalho_dsr_folga_descanso"]: '0');
								$LogOcorrencia['trabalho_dsr_folga_descanso_nao_tratado'] = (($qnt_ocorrencia["trabalho_dsr_folga_descanso"]) ? $qnt_ocorrencia["trabalho_dsr_folga_descanso"]: '0');
								$mensagem .= '<tr>
													<td><label for="trabalho_dsr_folga" class="col-form-label text-left pt-0 pb-0">Excesso de jornada semanal</label></td>
													<td data-trabalho_dsr_folga_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_dsr_folga_descanso"]) ? $resTratadas[0]["trabalho_dsr_folga_descanso"]: '0').'</td>
													<td data-trabalho_dsr_folga_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_dsr_folga_descanso"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['trabalho_AfastFerias'] == 1){
								$LogOcorrencia['trabalho_ferias_afastamento'] = (($resTratadas[0]["trabalho_ferias_afastamento"]) ? $resTratadas[0]["trabalho_ferias_afastamento"]: '0');
								$LogOcorrencia['trabalho_ferias_afastamento_nao_tratado'] = (($qnt_ocorrencia["trabalho_ferias_afastamento"]) ? $qnt_ocorrencia["trabalho_ferias_afastamento"]: '0');
								$mensagem .= '<tr>
													<td> <label for="trabalho_ferias_afastamento" class="col-form-label text-left pt-0 pb-0">Trabalho em Férias ou Afastamentos</label></td>
													<td data-trabalho_ferias_afastamento_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_ferias_afastamento"]) ? $resTratadas[0]["trabalho_ferias_afastamento"]: '0').'</td>
													<td data-trabalho_ferias_afastamento_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_ferias_afastamento"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['trabalho_sup6'] == 1){
								$LogOcorrencia['trabalho_6dias'] = (($resTratadas[0]["trabalho_6dias"]) ? $resTratadas[0]["trabalho_6dias"]: '0');
								$LogOcorrencia['trabalho_6dias_nao_tratado'] = (($qnt_ocorrencia["trabalho_6dias"]) ? $qnt_ocorrencia["trabalho_6dias"]: '0');
								$mensagem .= '<tr>
													<td> <label for="trabalho_6dias" class="col-form-label text-left pt-0 pb-0">Trabalho superior à 6 (seis) dias consecutivos sem folga</label></td>
													<td data-trabalho_6dias_tratado  align="center" class="text-center">'.(($resTratadas[0]["trabalho_6dias"]) ? $resTratadas[0]["trabalho_6dias"]: '0').'</td>
													<td data-trabalho_6dias_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["trabalho_6dias"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['troca_menor10'] == 1){
								$LogOcorrencia['troca_menor_10_dias'] = (($resTratadas[0]["troca_menor_10_dias"]) ? $resTratadas[0]["troca_menor_10_dias"]: '0');
								$LogOcorrencia['troca_menor_10_dias_nao_tratado'] = (($qnt_ocorrencia["troca_menor_10_dias"]) ? $qnt_ocorrencia["troca_menor_10_dias"]: '0');
								$mensagem .= '<tr>
													<td> <label for="troca_menor_10_dias" class="col-form-label text-left pt-0 pb-0">Troca de escala menor que 3 dias</label></td>
													<td data-troca_menor_10_dias_tratado  align="center" class="text-center">'.(($resTratadas[0]["troca_menor_10_dias"]) ? $resTratadas[0]["troca_menor_10_dias"]: '0').'</td>
													<td data-troca_menor_10_dias_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["troca_menor_10_dias"].'</td>
												</tr>';
							}
							if($TiposOcorrencia['troca_menor6'] == 1){
								$LogOcorrencia['troca_menor_6_meses'] = (($resTratadas[0]["troca_menor_6_meses"]) ? $resTratadas[0]["troca_menor_6_meses"]: '0');
								$LogOcorrencia['troca_menor_6_meses_nao_tratado'] = (($qnt_ocorrencia["troca_menor_6_meses"]) ? $qnt_ocorrencia["troca_menor_6_meses"]: '0');
								$mensagem .= '<tr>
													<td><label for="troca_menor_6_meses" class="col-form-label text-left pt-0 pb-0">Troca de escala menor que 6 meses</label></td>
													<td data-troca_menor_6_meses_tratado  align="center" class="text-center">'.(($resTratadas[0]["troca_menor_6_meses"]) ? $resTratadas[0]["troca_menor_6_meses"]: '0').'</td>
													<td  align="center"data-troca_menor_6_meses_nao_tratado  align="center" class="text-center">'.$qnt_ocorrencia["troca_menor_6_meses"].'</td>
												</tr>';
							}
							$mensagem .= '</tbody>';
						
						$mensagem .= '</table><br>';
						
						$htmlEmail = templateEmail($mensagem, '95%');
						
						
						$response = enviaEmail('tiago.moselli@outserv.com.br', 'Ocorrência de Ponto', $htmlEmail);
						
						// $response = enviaEmail($gestores[$key]['email'], 'Ocorrência de Ponto', $htmlEmail);
						//$response = enviaEmail('samuel.santana@crmservices.com.br', 'Ocorrência de Ponto ('.$gestores[$key]['email'].')', $htmlEmail);
						if($response){
							$this->dbportal->query("
								INSERT INTO zcrmportal_ocorrencia_workflow_log
								(coligada, descricao, nome, email, dtcad, enviado) VALUES
								('{$gestores[$key]['COLIGADA']}', '".serialize($LogOcorrencia)."', '{$gestores[$key]['nome']}', '{$gestores[$key]['email']}', '".date('Y-m-d H:i:s')."', 1)
							");
						}else{
							$this->dbportal->query("
								INSERT INTO zcrmportal_ocorrencia_workflow_log
								(coligada, descricao, nome, email, dtcad, enviado) VALUES
								('{$gestores[$key]['COLIGADA']}', '".serialize($LogOcorrencia)."', '{$gestores[$key]['nome']}', '{$gestores[$key]['email']}', '".date('Y-m-d H:i:s')."', 0)
							");
						}
						
					}
					
				}
				break;
				
				
				
				
			
			
			}
		}

		$this->dbportal
		->table('zcrmportal_ocorrencia_workflow')
		->set([
			'data2' => somarDias(dtEn($configWorkflow[0]->data2, true), $configWorkflow[0]->ciclo2).' '.m2h($configWorkflow[0]->horario2, 4)
		])
		->where('codcoligada', $this->coligada)
		->update();
		
	} 
	
	
    public function NotificarOcorrenciaGestor(){
        set_time_limit(60 * 5);

        $union_abono = "";
        $resAbono = $this->ListarOcorrenciaAbono();
        if($resAbono){
            foreach($resAbono as $key => $Abono){

                $union_abono .= "
                UNION ALL
            
                /*Excesso de Abono Gestor (Superior a 5 dias consecutivos);*/
                SELECT
                    CODCOLIGADA,
                    CHAPA,
                    NOME,
                    CODFUNCAO,
                    NOMEFUNCAO,
                    CODSECAO,
                    NOMESECAO,
                    DATA,
                    'excesso_abono_gestor' OCORRENCIA,
                    (UM + DOIS + TRES + QUATRO + CINCO) VALOR,
                    NULL COMPLEMENTO
                FROM(
                    SELECT
                        A.CODCOLIGADA,
                        A.CHAPA,
                        B.NOME,
                        B.CODFUNCAO,
                        C.NOME NOMEFUNCAO,
                        B.CODSECAO,
                        D.DESCRICAO NOMESECAO,
                        A.DATA,
                        (CASE WHEN (SELECT COUNT(*) FROM AABONFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.CODABONO = '{$Abono['CODIGO']}' AND A.BASE > 0)>0 THEN 1 ELSE 0 END)UM,
                        (CASE WHEN (SELECT COUNT(*) FROM AABONFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.CODABONO = '{$Abono['CODIGO']}' AND A.BASE > 0)>0 THEN 1 ELSE 0 END)DOIS,
                        (CASE WHEN (SELECT COUNT(*) FROM AABONFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.CODABONO = '{$Abono['CODIGO']}' AND A.BASE > 0)>0 THEN 1 ELSE 0 END)TRES,
                        (CASE WHEN (SELECT COUNT(*) FROM AABONFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.CODABONO = '{$Abono['CODIGO']}' AND A.BASE > 0)>0 THEN 1 ELSE 0 END)QUATRO,
                        (CASE WHEN (SELECT COUNT(*) FROM AABONFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.CODABONO = '{$Abono['CODIGO']}' AND A.BASE > 0)>0 THEN 1 ELSE 0 END)CINCO
                
                    FROM
                        AAFHTFUN A
                        LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
                        INNER JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                        INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
                        INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
                        
                    WHERE
                        A.CODCOLIGADA = P.COLIGADA
                        AND A.DATA BETWEEN P.INICIO AND P.FIM
                        --AND B.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                        
                        
                        
                )X
                WHERE (UM + DOIS + TRES + QUATRO + CINCO) >= 5
                ";

            }
        }

        $query = "
            WITH PAR AS (

                SELECT 
                    '1' COLIGADA,
                    '2022-07-16'	INICIO,
                    '2022-08-15'	    FIM,
                    '00008,002'		    HORARIO,
                    '029'          ABONO
                    
            )
            
            SELECT
                X.*,
                Z.codmotivo,
                Z.descricao_motivo,
                U.nome_solicitante nome_gestor,
                U.email_solicitante email_gestor
            FROM (
            
            /*Horas Extras acima do permitido*/
            SELECT
                A.CODCOLIGADA,
                A.CHAPA,
                A.NOME,
                A.CODFUNCAO,
                C.NOME NOMEFUNCAO,
                A.CODSECAO,
                D.DESCRICAO NOMESECAO,
                B.DATA,
                'extra_permitido' OCORRENCIA,
                B.EXTRAAUTORIZADO VALOR,
                NULL COMPLEMENTO
            
            FROM 
                PFUNC (NOLOCK) A
                    LEFT JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
                    INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
                    INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
            
            WHERE
                    A.CODCOLIGADA = (SELECT COLIGADA FROM PAR)
                AND B.DATA BETWEEN (SELECT INICIO FROM PAR) AND (SELECT FIM FROM PAR)
                AND B.EXTRAAUTORIZADO >= '240'
                --AND A.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                
                
            
                
            UNION ALL
            
            /*Horas Extras em escalas específicas (Zero HE)*/
            SELECT
                A.CODCOLIGADA,
                A.CHAPA,
                A.NOME,
                A.CODFUNCAO,
                C.NOME NOMEFUNCAO,
                A.CODSECAO,
                D.DESCRICAO NOMESECAO,
                B.DATA,
                'extra' OCORRENCIA,
                B.EXTRAAUTORIZADO VALOR,
                NULL COMPLEMENTO
            
            FROM 
                PFUNC A
                    LEFT JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
                    INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
                    INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
                
            
            WHERE
                A.CODCOLIGADA = (SELECT COLIGADA FROM PAR)
                AND B.DATA BETWEEN (SELECT INICIO FROM PAR) AND (SELECT FIM FROM PAR)
                AND A.CODHORARIO IN(SELECT part FROM DBO.VARPAR((SELECT HORARIO FROM PAR)))
                AND B.EXTRAAUTORIZADO > 0
                --AND A.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                
                
            
            UNION ALL
            
            /*Jornada Diária acima de 08 e 10 horas*/
            SELECT
                A.CODCOLIGADA,
                A.CHAPA,
                A.NOME,
                A.CODFUNCAO,
                C.NOME NOMEFUNCAO,
                A.CODSECAO,
                D.DESCRICAO NOMESECAO,
                B.DATA,
                'jornada' OCORRENCIA,
                B.HTRAB VALOR,
                NULL COMPLEMENTO
            
            FROM 
                PFUNC (NOLOCK) A 
                    LEFT JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
                    INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
                    INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
                
            
            WHERE
                A.CODCOLIGADA = (SELECT COLIGADA FROM PAR)
                AND B.DATA BETWEEN (SELECT INICIO FROM PAR) AND (SELECT FIM FROM PAR)
                AND B.HTRAB > 480
                --AND A.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                
                
            
            UNION ALL
            
            /*Trabalho em DSR ou Folga*/
            SELECT
                A.CODCOLIGADA,
                A.CHAPA,
                A.NOME,
                A.CODFUNCAO,
                C.NOME NOMEFUNCAO,
                A.CODSECAO,
                D.DESCRICAO NOMESECAO,
                B.DATA,
                'trabalho_dsr_folga' OCORRENCIA,
                B.DESCANSO VALOR,
                NULL COMPLEMENTO
                --(SELECT COUNT(*) FROM ABATFUN G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) VALOR
            
            FROM 
                PFUNC A
                LEFT JOIN AAFHTFUN B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
                INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
                INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
            
            WHERE
                A.CODCOLIGADA = (SELECT COLIGADA FROM PAR)
                AND B.DATA BETWEEN (SELECT INICIO FROM PAR) AND (SELECT FIM FROM PAR)
                AND B.DESCANSO > 0
                --AND A.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                AND (SELECT COUNT(*) FROM ABATFUN G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) > 0
                
                
            
            UNION ALL
            
            /*Falta de registro adequado de jornada de trabalho (Registro Manual); */
            SELECT
                A.CODCOLIGADA,
                A.CHAPA,
                B.NOME,
                B.CODFUNCAO,
                C.NOME NOMEFUNCAO,
                B.CODSECAO,
                D.DESCRICAO NOMESECAO,
                A.DATA,
                'registro_manual' OCORRENCIA,
                A.BATIDA VALOR,
                NULL COMPLEMENTO
            
            FROM 
                ABATFUN A
                LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
                INNER JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
                INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
            
            WHERE
                    A.CODCOLIGADA = P.COLIGADA
                AND A.DATA BETWEEN P.INICIO AND P.FIM
                AND A.STATUS = 'D'
                --AND B.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                
                
            
            UNION ALL

            /*Registros britânicos (Apontamentos Manuais); */
            SELECT
                A.CODCOLIGADA,
                A.CHAPA,
                B.NOME,
                B.CODSECAO,
                C.DESCRICAO NOMESECAO,
                B.CODFUNCAO,
                D.NOME NOMEFUNCAO,
                A.DATA,
                'registro_britanico' OCORRENCIA,
                1 VALOR,
                CONCAT(MIN(B1.BATIDA), ' ', MAX(B1.BATIDA)) COMPLEMENTO
                
            FROM
                AAFHTFUN (NOLOCK) A
                INNER JOIN PFUNC (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                INNER JOIN PSECAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODSECAO
                INNER JOIN PFUNCAO (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
                INNER JOIN ABATFUN (NOLOCK) B1 ON B1.CODCOLIGADA = A.CODCOLIGADA AND B1.CHAPA = A.CHAPA AND ISNULL(B1.DATAREFERENCIA, B1.DATA) = A.DATA AND B1.STATUS NOT IN ('T')
                INNER JOIN AJORHOR J1 ON J1.CODCOLIGADA = A.CODCOLIGADA AND J1.CODHORARIO = (SELECT CODHORARIO FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
                            (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA))

                LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA

                
            WHERE
                    A.CODCOLIGADA = P.COLIGADA
                AND A.DATA BETWEEN P.INICIO AND P.FIM

            GROUP BY
                A.CODCOLIGADA,
                A.CHAPA,
                A.DATA,
                B.NOME,
                B.CODSECAO,
                C.DESCRICAO,
                B.CODFUNCAO,
                D.NOME

            HAVING
                    MIN(B1.BATIDA) = MIN(J1.BATINICIO)
                AND MAX(B1.BATIDA) = MAX(J1.BATFIM)


            UNION ALL
            
            /*Trabalho superior à 6 (seis) dias consecutivos sem folga;*/
            SELECT
                CODCOLIGADA,
                CHAPA,
                NOME,
                CODFUNCAO,
                NOMEFUNCAO,
                CODSECAO,
                NOMESECAO,
                DATA,
                'trabalho_6dias' OCORRENCIA,
                (UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) VALOR,
                NULL COMPLEMENTO
            
            FROM(
                SELECT
                    A.CODCOLIGADA,
                    A.CHAPA,
                    B.NOME,
                    B.CODFUNCAO,
                    C.NOME NOMEFUNCAO,
                    B.CODSECAO,
                    D.DESCRICAO NOMESECAO,
                    A.DATA,
                    (SELECT COUNT(B.CHAPA) FROM AAFHTFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.HTRAB >0)UM,
                    (SELECT COUNT(B.CHAPA) FROM AAFHTFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.HTRAB >0)DOIS,
                    (SELECT COUNT(B.CHAPA) FROM AAFHTFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.HTRAB >0)TRES,
                    (SELECT COUNT(B.CHAPA) FROM AAFHTFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.HTRAB >0)QUATRO,
                    (SELECT COUNT(B.CHAPA) FROM AAFHTFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.HTRAB >0)CINCO,
                    (SELECT COUNT(B.CHAPA) FROM AAFHTFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-5 = B.DATA AND B.HTRAB >0)SEIS,
                    (SELECT COUNT(B.CHAPA) FROM AAFHTFUN B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)SETE
                
                    
                FROM
                    AAFHTFUN A
                    LEFT JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
                    INNER JOIN PFUNC B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
                    INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
                    INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
                    
                WHERE
                        A.CODCOLIGADA = P.COLIGADA
                    AND A.DATA BETWEEN P.INICIO AND P.FIM
                    --AND B.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                    
                    
                    
            )X
                WHERE (UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) > 6
            
            UNION ALL
            
            /* Interjornada */
            SELECT
                AAVISOCALCULADO.CODCOLIGADA,
                AAVISOCALCULADO.CHAPA,
                B.NOME,
                B.CODFUNCAO,
                C.NOME NOMEFUNCAO,
                B.CODSECAO,
                D.DESCRICAO NOMESECAO,
                AAVISOCALCULADO.DATAREFERENCIA,
                'interjornada' OCORRENCIA,
                1 VALOR,
                NULL COMPLEMENTO
                
            FROM 
                AAVISOCALCULADO
                    INNER JOIN PFUNC B ON B.CODCOLIGADA = AAVISOCALCULADO.CODCOLIGADA AND B.CHAPA = AAVISOCALCULADO.CHAPA
                    INNER JOIN PFUNCAO C ON C.CODCOLIGADA = AAVISOCALCULADO.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
                    INNER JOIN PSECAO D ON D.CODCOLIGADA = AAVISOCALCULADO.CODCOLIGADA AND D.CODIGO = B.CODSECAO
                , 
                AAVISO,
                PAR
                
            WHERE 
                AAVISOCALCULADO.CODCOLIGADA = PAR.COLIGADA
                AND AAVISOCALCULADO.CODAVISO = AAVISO.CODAVISO 
                AND AAVISOCALCULADO.CODCOLIGADA = PAR.COLIGADA
                AND AAVISOCALCULADO.DATAREFERENCIA BETWEEN PAR.INICIO AND PAR.FIM
                AND AAVISOCALCULADO.CODAVISO = '1'
                --AND B.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                
                
            
            {$union_abono}
            
            UNION ALL
            
            /*Trabalho em Férias ou Afastamentos*/
            SELECT
                CODCOLIGADA,
                CHAPA,
                NOME,
                CODFUNCAO,
                NOMEFUNCAO,
                CODSECAO,
                NOMESECAO,
                DATA,
                'trabalho_ferias_afastamento' OCORRENCIA,
                CASE 
                    WHEN FERIAS IS NOT NULL THEN 1
                    WHEN AFAST IS NOT NULL THEN 2
                    ELSE NULL
                END VALOR,
                NULL COMPLEMENTO
            
            FROM (
            
                SELECT 
                    AX.* 
                FROM(
            
                    SELECT
                        A.CODCOLIGADA,
                        A.CHAPA,
                        A.NOME,
                        A.CODFUNCAO,
                        C.NOME NOMEFUNCAO,
                        A.CODSECAO,
                        D.DESCRICAO NOMESECAO,
                        B.DATA,
                        (SELECT 'Férias' FROM PFUFERIASPER F  WHERE F.CODCOLIGADA = A.CODCOLIGADA AND F.CHAPA = A.CHAPA AND B.DATA BETWEEN F.DATAINICIO AND F.DATAFIM) FERIAS,
                        (SELECT 'Afastamento' FROM PFHSTAFT H WHERE H.CODCOLIGADA = A.CODCOLIGADA AND H.CHAPA = A.CHAPA AND B.DATA BETWEEN H.DTINICIO AND  ISNULL(H.DTFINAL, '2050-12-01') ) AFAST,
                        (SELECT COUNT(*) FROM ABATFUN G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) BAT
                    
                    FROM 
                        PFUNC A
                        LEFT JOIN AAFHTFUN B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
                        INNER JOIN PFUNCAO C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
                        INNER JOIN PSECAO D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
                        
                    
                    WHERE
                            A.CODCOLIGADA = (SELECT COLIGADA FROM PAR)
                        AND B.DATA BETWEEN (SELECT INICIO FROM PAR) AND (SELECT FIM FROM PAR)
                        --AND A.CODSECAO IN ('009.34312.001','045.34382.001','045.34382.003','045.34382.005','045.34312.003','009.34351.004','009.34619.001','045.34614.001','045.34619.001','045.34625.001','045.34631.001','045.34859.001')
                        
                        
            
                    )AX
                    
                    WHERE	
                        (AX.FERIAS IS NOT NULL OR AX.AFAST IS NOT NULL) AND AX.BAT > 0
                )BX
            
            
            )X
            
                LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia Z ON Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA
                LEFT JOIN ".DBPORTAL_BANCO."..GESTOR_CHAPA G ON G.CHAPA = X.CHAPA AND G.CODCOLIGADA = X.CODCOLIGADA
                LEFT JOIN ".DBPORTAL_BANCO."..GESTOR_DO_USUARIO U ON U.chapa_solicitante = G.GESTOR_CHAPA COLLATE Latin1_General_CI_AS AND U.coligada_solicitante = X.CODCOLIGADA
                
                WHERE Z.codmotivo IS NULL 
            ORDER BY
                X.NOME,
                X.DATA
        
        ";
        /*echo '<pre>';
        echo $query;
        exit();*/
        $result = $this->dbrm->query($query);
        if($result->getNumRows() > 0){
            $emails_gestor = false;

            $response = $result->getResultArray();
            foreach($response as $key => $Dados){
                if(strlen(trim($Dados['email_gestor'])) > 0){
                    $emails_gestor[$Dados['email_gestor']]['EMAIL'] = $Dados['email_gestor'];
                    $emails_gestor[$Dados['email_gestor']]['NOME'] = $Dados['nome_gestor'];
                }
            }

        }
        
        if(is_array($emails_gestor)){
            foreach($emails_gestor as $key => $Gestor){
                echo $Gestor['NOME'].' - '.$Gestor['EMAIL'].'<br>';

                $mensagem = 'Prezado Gestor <strong>'.$Gestor['NOME'].'</strong>,<br>
                <br>
                Foram observadas ocorrências de jornada de trabalho no ponto de sua equipe, segue abaixo link para acesso ao Portal RH, onde você deverá detalhar as respectivas situações.<br>
                <br>
                <table width="100%" bgcolor="#cccccc" cellpadding="2" cellspacing="1">';
                    $mensagem .= '<tr bgcolor="#328955">
                        <td align="center"><strong style="color: #ffffff;">CHAPA</strong></td>
                        <td><strong style="color: #ffffff;">NOME</strong></td>
                        <td><strong style="color: #ffffff;">FUNÇÃO</strong></td>
                        <td><strong style="color: #ffffff;">SEÇÃO</strong></td>
                        <td align="center"><strong style="color: #ffffff;">DATA</strong></td>
                        <td align="center"><strong style="color: #ffffff;">OCORRÊNCIA</strong></td>
                        <td align="center"><strong style="color: #ffffff;">VALOR</strong></td>
                    </tr>';
                    
                    foreach($response as $key => $Dados){
                        if($Dados['email_gestor'] == $Gestor['EMAIL']){

                            $ocorrencia = "";
                            switch($Dados['OCORRENCIA']){
                                case 'extra_permitido': $ocorrencia = 'Extra Acima do Permitido'; $valor_ocorrencia = m2h($Dados['VALOR']); break;
                                case 'extra': $ocorrencia = 'Extra Executado'; $valor_ocorrencia = m2h($Dados['VALOR']); break;
                                case 'jornada': $ocorrencia = 'Jornada > 8h e 10h'; $valor_ocorrencia = m2h($Dados['VALOR']); break;
                                case 'trabalho_dsr_folga': $ocorrencia = 'Trabalho em DSR ou Folga'; $valor_ocorrencia = m2h($Dados['VALOR']); break;
                                case 'trabalho_ferias_afastamento': $ocorrencia = 'Trabalho em Férias ou Afastamentos'; $valor_ocorrencia = ($Dados['VALOR'] == 1) ? "Férias" : "Afastamento"; break;
                                case 'registro_manual': $ocorrencia = 'Registro Manual'; $valor_ocorrencia = m2h($Dados['VALOR']); break;
                                case 'trabalho_6dias': $ocorrencia = 'Trabalho superior à 6 (seis) dias consecutivos sem folga'; $valor_ocorrencia = $Dados['VALOR']." Dias"; break;
                                case 'excesso_abono_gestor': $ocorrencia = 'Excesso de Abono Gestor (Superior a 5 dias consecutivos)'; $valor_ocorrencia = $Dados['VALOR']." Dias"; break;
                                case 'interjornada': $ocorrencia = 'Interjornada'; $valor_ocorrencia = "Tempo Mínimo Entre Jornadas (Inter-Jornadas)"; break;
                                case 'registro_britanico': $ocorrencia = 'Registro Britânico'; 
                                                    $valor_ocorrencia = $Dados['COMPLEMENTO']; 
                                                    $valor_ocorrencia = explode(' ', $valor_ocorrencia);
                                                    $valor_ocorrencia = m2h($valor_ocorrencia[0],4).' '.m2h($valor_ocorrencia[1],4);
                                                    break;
                                default : $ocorrencia = '--'; $valor_ocorrencia = "--";
                            }

                            $mensagem .= '<tr bgcolor="#ffffff">
                                <td align="center">'.$Dados['CHAPA'].'</td>
                                <td>'.$Dados['NOME'].'</td>
                                <td>'.$Dados['NOMEFUNCAO'].'</td>
                                <td>'.$Dados['NOMESECAO'].'</td>
                                <td align="center">'.dtBr($Dados['DATA']).'</td>
                                <td align="center">'.$ocorrencia.'</td>
                                <td align="center">'.$valor_ocorrencia.'</td>
                            </tr>';
                        }
                    }
                $mensagem .= '</table><br>';

                $htmlEmail = templateEmail($mensagem, '95%');

                enviaEmail('tiago.moselli@crmservices.com.br', 'Ocorrência de Ponto', $htmlEmail);
                exit();

                

            }
        }

    }

	public function ListarWorkflowLog($dados){

		$data_inicio = ($dados['data_inicio'] != "") ? $dados['data_inicio'] : date('Y-m-d');
		$data_fim = ($dados['data_fim'] != "") ? $dados['data_fim'] : date('Y-m-t');

		$query = " SELECT * FROM zcrmportal_ocorrencia_workflow_log WHERE dtcad BETWEEN '{$data_inicio} 00:00:00' AND '{$data_fim} 23:59:59' ORDER BY id DESC ";
		$result = $this->dbportal->query($query);

        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

	}

	public function GravaLogOcorrenciaColigadas(){

		$mPortal = model('PortalModel');
		$coligadas = $mPortal->ListarColigada(false, 'S');

		if($coligadas){
			foreach($coligadas as $key => $Coligada){
				$this->GravaLogOcorrencia($Coligada['CODCOLIGADA']);
			}
		}
	
	}

	public function GravaLogOcorrencia($coligada){

		$query = "
			SELECT 
				TOP 1 
				CODCOLIGADA,
				INICIOMENSAL, 
				FIMMENSAL, 
				SUBSTRING(CONVERT(VARCHAR, FIMMENSAL, 112), 0, 7), 
				SUBSTRING(CONVERT(VARCHAR, GETDATE(), 112), 0, 7) 
			FROM 
				APERIODO 
			WHERE
				SUBSTRING(CONVERT(VARCHAR, FIMMENSAL, 112), 0, 7) < SUBSTRING(CONVERT(VARCHAR, GETDATE(), 112), 0, 7)
				AND CODCOLIGADA = {$coligada}
			ORDER BY 
				INICIOMENSAL DESC
		";
		$result = $this->dbrm->query($query);
		$Periodo = $result->getResultArray();

		$query = "
		            
		WITH 

		PAR AS 
			(
			SELECT 
				COLIGADA = '{$coligada}'
				, INICIO   = '".date('Y-m-d', strtotime($Periodo[0]['INICIOMENSAL']))."'
				, FIM      = '".date('Y-m-d', strtotime($Periodo[0]['FIMMENSAL']))."'
				, HORARIO  = '020,	0012,	0013,	0005,	0033,	Cons.010,	0004,	0055,	006,	0017,	0011,	007,	07,	0202,	0203,	015,	TESTE,	019,	Cons.009,	0007,	0208,	0027,	0023,	108,	110,	092,	104,	084,	118,	0207,	0204,	113,	095,	0032,	03,	100,	0048,	0041,	0042,	0043,	0044,	0026,	0040'        
				, ABONO    = '029'
			)

INSERT INTO ".DBPORTAL_BANCO."..zcrmportal_ocorrencia 

SELECT
X.CODCOLIGADA,
X.CHAPA,
X.DATA,
NULL,
NULL,
0,
GETDATE(),
X.OCORRENCIA,
NULL,
NULL,
NULL,
NULL,
'RM',
MAX(X.VALOR),
NULL,
X.COMPLEMENTO

		FROM (





		/***************************************************************************************************************/
		/* Horas Extras acima do permitido                                                                             */
		/***************************************************************************************************************/
			SELECT 
				* 
			FROM (
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'extra_permitido' OCORRENCIA
					, NULL COMPLEMENTO
					, (CASE WHEN BASE  = 420 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN BASE  = 480 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN BASE  = 528 AND EXTRAEXECUTADO >  72 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN BASE  = 540 AND EXTRAEXECUTADO >  60 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												THEN HTRAB 
							ELSE 0 END) VALOR
				
					, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
				FROM 
					PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					  
					
					AND (CASE WHEN BASE  = 420 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN BASE  = 480 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN BASE  = 528 AND EXTRAEXECUTADO >  72 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN BASE  = 540 AND EXTRAEXECUTADO >  60 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							  WHEN FERIADO > 0 AND HTRAB > 0
							  AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
														(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												THEN HTRAB
						   ELSE 0 END) > 0
			)XX
			 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS NOT IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(ESCALA_ESPECIAL,0) = 1)

		UNION ALL

		/***************************************************************************************************************/
		/* Horas Extras em escalas específicas (Zero HE)                                                               */
		/***************************************************************************************************************/
			/* Horários 12x36 */
			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, A.NOME
				, A.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, A.CODSECAO
				, D.DESCRICAO NOMESECAO
				, B.DATA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
				, 'extra' OCORRENCIA
				, NULL COMPLEMENTO
				, (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
					   ELSE 0 END) VALOR
				
				, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO 
			FROM 
				PFUNC (NOLOCK) A
					LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
					INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
					INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
					INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
			
			WHERE
					B.DATA BETWEEN M.INICIO AND M.FIM
				  
				 
				AND (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
					   ELSE 0 END) > 0
			
			UNION ALL
			
			/* Horários de Revezamento */
			SELECT 
				* 
			FROM (
			  
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'extra' OCORRENCIA
					, NULL COMPLEMENTO
					, (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												 THEN HTRAB
							ELSE 0 END) VALOR
					
					, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
				FROM 
					PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					
					
					AND (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												 THEN HTRAB
							ELSE 0 END) > 0
			)XX
			 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(ESCALA_ESPECIAL,0) = 1 )

		UNION ALL
		/***************************************************************************************************************/
		/* Jornada Diária acima de 11 e 10 horas                                                                       */
		/***************************************************************************************************************/
			SELECT * FROM(
				SELECT

					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'jornada' OCORRENCIA
					, NULL COMPLEMENTO
					, B.HTRAB VALOR
					
					, BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
				FROM 
					PFUNC (NOLOCK) A 
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					AND B.HTRAB > 600
					AND B.BASE <= 540
					 
					 
				)x where  
					(SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA <= x.DATA)) = 0
							
		UNION ALL
		/***************************************************************************************************************/
		/* Falta de registro adequado de jornada de trabalho (Registro Manual)                                         */
		/***************************************************************************************************************/
			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, B.NOME
				, B.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, B.CODSECAO
				, D.DESCRICAO NOMESECAO
				, A.DATA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO	    
				, 'registro_manual' OCORRENCIA
				, NULL COMPLEMENTO
				, A.BATIDA VALOR
				
				, NULL BASE, NULL HTRAB, NULL EXTRAAUTORIZADO, NULL COMPENSADO, NULL DESCANSO, NULL FERIADO
			FROM 
				ABATFUN (NOLOCK) A
				INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				INNER JOIN PAR              M ON M.COLIGADA = A.CODCOLIGADA
			
			WHERE
					A.DATA BETWEEN M.INICIO AND M.FIM
				AND A.STATUS = 'D'
				 
						
				 UNION ALL
				 /***************************************************************************************************************/
				 /* Trabalho em DSR/Folga [COMPENSADO]                                                                          */
				 /***************************************************************************************************************/
					 SELECT
						   A.CODCOLIGADA
						 , A.CHAPA
						 , A.NOME
						 , A.CODFUNCAO
						 , C.NOME NOMEFUNCAO
						 , A.CODSECAO
						 , D.DESCRICAO NOMESECAO
						 , B.DATA
						 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						 , 'trabalho_dsr_folga' OCORRENCIA
						 , NULL COMPLEMENTO
						 , (CASE WHEN (COMPENSADO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) VALOR
					 
						 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
						 
					 FROM 
						 PFUNC (NOLOCK) A
							 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
							 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
							 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
					 
					 WHERE
							 B.DATA BETWEEN M.INICIO AND M.FIM
						 
						  
						 AND (CASE WHEN (COMPENSADO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) > 0
				 
				 
				 UNION ALL
				 
				 /***************************************************************************************************************/
				 /* Trabalho em DSR/Folga [DESCANSO]                                                                            */
				 /***************************************************************************************************************/
				SELECT 
					* 
				FROM (
					 SELECT
						   A.CODCOLIGADA
						 , A.CHAPA
						 , A.NOME
						 , A.CODFUNCAO
						 , C.NOME NOMEFUNCAO
						 , A.CODSECAO
						 , D.DESCRICAO NOMESECAO
						 , B.DATA
						 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						 , 'trabalho_dsr_folga_descanso' OCORRENCIA
						 , NULL COMPLEMENTO
						 , (CASE WHEN (DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) VALOR
					 
						 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
						 
					 FROM 
						 PFUNC (NOLOCK) A
							 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
							 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
							 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
					 
					 WHERE
							 B.DATA BETWEEN M.INICIO AND M.FIM
						 
						  
						 AND (CASE WHEN (DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) > 0              
					)XX
				      WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(EXTRA_FERIADO,0) = 1 )
					
		UNION ALL
		/***************************************************************************************************************/
		/* Trabalho em Férias/Afastamentos                                                                             */
		/***************************************************************************************************************/

			SELECT
				  CODCOLIGADA
				, CHAPA
				, NOME
				, CODFUNCAO
				, NOMEFUNCAO
				, CODSECAO
				, NOMESECAO
				, DATA
				, HORARIO
				, OCORRENCIA
				, CASE 
					WHEN FERIAS IS NOT NULL THEN 'Férias'
					WHEN AFAST IS NOT NULL THEN 'Afastamento/Atestado'
					ELSE NULL
				  END COMPLEMENTO
				, CASE 
					WHEN FERIAS IS NOT NULL THEN 1
					WHEN AFAST IS NOT NULL THEN 2
					ELSE NULL
				  END VALOR
				, NULL BASE
				, NULL HTRAB
				, NULL EXTRAAUTORIZADO
				, NULL COMPENSADO
				, NULL DESCANSO
				, NULL FERIADO
				
			FROM (
			
				SELECT 
					AX.* 
				FROM(
				
					SELECT
						  A.CODCOLIGADA
						, A.CHAPA
						, A.NOME
						, A.CODFUNCAO
						, C.NOME NOMEFUNCAO
						, A.CODSECAO
						, D.DESCRICAO NOMESECAO
						, B.DATA
						, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						, 'trabalho_ferias_afastamento' OCORRENCIA
						, (SELECT 'Férias' FROM PFUFERIASPER (NOLOCK) F  WHERE F.CODCOLIGADA = A.CODCOLIGADA AND F.CHAPA = A.CHAPA AND B.DATA BETWEEN F.DATAINICIO AND F.DATAFIM) FERIAS
						, (SELECT 'Afastamento' FROM PFHSTAFT (NOLOCK) H WHERE H.CODCOLIGADA = A.CODCOLIGADA AND H.CHAPA = A.CHAPA AND B.DATA BETWEEN H.DTINICIO AND  ISNULL(H.DTFINAL, '2050-12-01') ) AFAST
						, (SELECT COUNT(*) FROM ABATFUN (NOLOCK) G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) BAT
						, B.ABONO
					
					FROM 
						PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
						
					
					WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
										
				
				)AX
				WHERE	
					(AX.FERIAS IS NOT NULL OR AX.AFAST IS NOT NULL) AND AX.BAT > 0 AND ( ABONO = 0 OR ABONO >= 240) /* A pedido ignorado se o abono foi menor que 4horas*/
									
			)BX
					   


		UNION ALL
		/***************************************************************************************************************/
		/* InterJornada                                                                                                */
		/***************************************************************************************************************/

		SELECT 
			  CODCOLIGADA
			, CHAPA
			, NOME
			, CODFUNCAO
			, NOMEFUNCAO
			, CODSECAO
			, NOMESECAO
			, DATAREFERENCIA
			, HORARIO
			, OCORRENCIA
			, AVISO + ' [' +
				(CASE WHEN DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) IS NULL or DATEDIFF(minute ,BAT_anterior, BAT_atual) > 660 THEN '[Bat.Inválida]'
					  ELSE DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) END) 
				+ ']' COMPLEMENTO
			, 1 VALOR

			, NULL BASE
			, NULL HTRAB
			, NULL EXTRAAUTORIZADO
			, NULL COMPENSADO
			, NULL DESCANSO
			, NULL FERIADO

		FROM(

			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, B.NOME
				, B.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, B.CODSECAO
				, D.DESCRICAO NOMESECAO
				, A.DATAREFERENCIA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATAREFERENCIA)) HORARIO
				, 'interjornada' OCORRENCIA
				, 1 VALOR
				, AAVISO.DESCRICAO AVISO 
				,(SELECT MAX(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA-1 = M.DATAREFERENCIA AND NATUREZA = 1) BAT_anterior
				,(SELECT MIN(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA = M.DATAREFERENCIA AND NATUREZA = 0) BAT_atual
			
			FROM 
				AAVISOCALCULADO A (NOLOCK)
					INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				, 
				AAVISO,
				PAR
				
			WHERE 
					
					A.CODCOLIGADA = PAR.COLIGADA
					AND A.CODAVISO = AAVISO.CODAVISO 
					AND A.CODCOLIGADA = PAR.COLIGADA
					AND A.DATAREFERENCIA BETWEEN PAR.INICIO AND PAR.FIM
					AND A.CODAVISO = '1'
					

					)X
			UNION ALL
			/***************************************************************************************************************/
			/* IntraJornada    [tipo de retorno COMPLEMENTO]                                                               */
			/***************************************************************************************************************/
			  
			  SELECT 
				  CODCOLIGADA
				, CHAPA
				, NOME
				, CODFUNCAO
				, NOMEFUNCAO
				, CODSECAO
				, NOMESECAO
				, DATA
				, HORARIO
				, OCORRENCIA
				, ' Tempo Mínimo de refeição (IntraJornada) ' + dbo.mintotime(REF_OBRIGATORIO) + ' ' + 'não realizado [' + dbo.mintotime(REF_REALIZADO) + ']' COMPLEMENTO
				, VALOR
				
				, BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO
			  FROM (
			  
				SELECT 
						 ISNULL((SELECT (FIM-INICIO) TEMPO
						FROM ABATHOR M
						WHERE 
						M.CODCOLIGADA = XX.CODCOLIGADA
						AND M.CODHORARIO  = XX.HORARIO
						AND TIPO = 4 
						AND INDICE = 1
						),0) REF_OBRIGATORIO
				  , *
					
				FROM (
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , A.NOME
					  , A.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , A.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , B.DATA
					  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					  , 'interjornada' OCORRENCIA
					  , NULL VALOR
					  
					  ,ISNULL((SELECT SUM(DATEDIFF(MINUTE,INICIO,FIM)) FROM AOCORRENCIACALCULADA P
					  WHERE 
						A.CODCOLIGADA = P.CODCOLIGADA 
					  AND A.CHAPA = P.CHAPA 
					  AND B.DATA = P.DATAREFERENCIA
					  AND TIPOOCORRENCIA IN ('AREF')),0) REF_REALIZADO
				  
					  , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
				  FROM 
					  PFUNC (NOLOCK) A
						  LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						  INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						  INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						  INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				  
				  WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					  AND B.HTRAB > 0
					   
					   
			  
				)XX
				   WHERE XX.HORARIO IN(
										  SELECT 
											DISTINCT CODHORARIO
										  FROM ABATHOR, PAR
										  WHERE ABATHOR.CODCOLIGADA = PAR.COLIGADA
										  AND TIPO = 5
									 )
			  )XY
			  WHERE REF_OBRIGATORIO-REF_REALIZADO > 10


			UNION ALL
			/***************************************************************************************************************/
			/* Registros britânicos (Apontamentos Manuais)    [tipo de retorno COMPLEMENTO]                                */
			/***************************************************************************************************************/ 
			 
			  SELECT
					A.CODCOLIGADA
				  , A.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , D.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , C.DESCRICAO NOMESECAO
				  , A.DATA
				  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
				  , 'registro_britanico' OCORRENCIA
				  , CONCAT(dbo.mintotime(MIN(B1.BATIDA)), ' ', dbo.mintotime(MAX(B1.BATIDA))) COMPLEMENTO
				  , 1 VALOR
					
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
				  
				  
			  FROM AAFHTFUN (NOLOCK) A
				  INNER JOIN PFUNC   (NOLOCK) B  ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				  INNER JOIN PSECAO  (NOLOCK) C  ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODSECAO
				  INNER JOIN PFUNCAO (NOLOCK) D  ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
				  INNER JOIN ABATFUN (NOLOCK) B1 ON B1.CODCOLIGADA = A.CODCOLIGADA AND B1.CHAPA = A.CHAPA AND ISNULL(B1.DATAREFERENCIA, B1.DATA) = A.DATA AND B1.STATUS NOT IN ('T')
				  INNER JOIN AJORHOR (NOLOCK) J1 ON J1.CODCOLIGADA = A.CODCOLIGADA AND J1.CODHORARIO = (SELECT CODHORARIO FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
							  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA))
				  LEFT  JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
			  
				  
			  WHERE
					  A.CODCOLIGADA = P.COLIGADA
				  AND A.DATA BETWEEN P.INICIO AND P.FIM
				  
			  GROUP BY
				  A.CODCOLIGADA,
				  A.CHAPA,
				  A.DATA,
				  B.NOME,
				  B.CODSECAO,
				  C.DESCRICAO,
				  B.CODFUNCAO,
				  D.NOME
			  
			  HAVING
					  MIN(B1.BATIDA) = MIN(J1.BATINICIO)
				  AND MAX(B1.BATIDA) = MAX(J1.BATFIM)
							

			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA MENOS DE 6 MESES    [tipo de retorno COMPLEMENTO]                                           */
			/***************************************************************************************************************/ 
			 
			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , ULT_MUD_DATA DATA
				  , NULL HORARIO
				  , 'troca_menor_6_meses' OCORRENCIA
				  , CONVERT(VARCHAR(2),DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA)) +
					+ (CASE WHEN DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) > 1 THEN ' (meses) --->' ELSE ' (mês) --->' END) 
					+ CONVERT(VARCHAR(10),ULT_MUD_DATA,103) + ' ['+ULT_MUD_CODHOR+']' + ' - ' + CONVERT(VARCHAR(10),PEN_MUD_DATA,103) + ' [' + PEN_MUD_CODHOR + ']'  COMPLEMENTO
				  , DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) VALOR
				  
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
			  FROM (
			  
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, B.NOME NOMEFUNCAO
					, A.CODSECAO
					, C.DESCRICAO NOMESECAO
					, (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ULT_MUD_DATA
					
					, (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA  AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ) ULT_MUD_CODHOR
				  
					  ,(SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
						  AND DTMUDANCA >
										  (SELECT 
											  CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
															ELSE MAX(DTMUDANCA) END
										   FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
											  AND DTMUDANCA <=
												  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
														  AND DTMUDANCA <> 
															  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
														   AND CODHORARIO <> 
															  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
											  AND CODHORARIO <> 
													(SELECT 
															CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
															ELSE CODHORARIO END
														FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
														(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
															  AND DTMUDANCA <> 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
															   AND CODHORARIO <> 
																  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
												 )
										  )	
						  ) PEN_MUD_DATA
	  
					  ,(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
						   (SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
							  AND DTMUDANCA >
											  (SELECT 
												  CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
																ELSE MAX(DTMUDANCA) END
											   FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
												  AND DTMUDANCA <=
													  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
															  AND DTMUDANCA <> 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
															   AND CODHORARIO <> 
																  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
												  AND CODHORARIO <> 
														(SELECT 
																CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
																ELSE CODHORARIO END
															FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																  AND DTMUDANCA <> 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																   AND CODHORARIO <> 
																	  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																		  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
													 )
											  )	
							 )
						 ) PEN_MUD_CODHOR

				FROM
					PFUNC A
					INNER JOIN PFUNCAO (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
					INNER JOIN PSECAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
					INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
			  
				WHERE
						A.CODSITUACAO NOT IN ('D')
					AND (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) BETWEEN P.INICIO AND P.FIM
					

				  
			  )X
				 WHERE 
					DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) < 6 
			  
			  
			  
			  
			  
			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA MENOR DE 10 DIAS    [tipo de retorno VALOR]                                                 */
			/***************************************************************************************************************/

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , ULT_DTMUDANCA DATA
				  , NULL HORARIO
				  , 'troca_menor_10_dias' OCORRENCIA
				, NULL COMPLEMENTO
				  , DIAS_ALTERACAO VALOR

				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
			  FROM (
				  SELECT
					  A.CODCOLIGADA,
					  A.CHAPA,
					  A.NOME,
					  A.CODFUNCAO,
					  B.NOME NOMEFUNCAO,
					  A.CODSECAO,
					  C.DESCRICAO NOMESECAO,
					  (
						SELECT
							MAX(ABS(DATEDIFF(DAY, H.RECCREATEDON, H.DTMUDANCA)))
						FROM
							PFHSTHOR H
						WHERE
							H.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
						) DIAS_ALTERACAO,
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ULT_DTMUDANCA
					  
				  FROM
					  PFUNC A
					  INNER JOIN PFUNCAO  (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
					  INNER JOIN PSECAO   (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
					  INNER JOIN PAR               P ON P.COLIGADA = A.CODCOLIGADA
				  WHERE
						  A.CODSITUACAO NOT IN ('D')
					AND (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) BETWEEN P.INICIO AND P.FIM
					
					   
				  
			  )X
				  WHERE 
					  DIAS_ALTERACAO < 10
			  
			  
			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA SEM ANEXO DO TERMO ADITIVO    [tipo de retorno VALOR]                                       */
			/***************************************************************************************************************/
						
			  SELECT 
					B.CODCOLIGADA
				  , B.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , C.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , D.DESCRICAO NOMESECAO
				  , A.datamudanca DATA
				  , NULL HORARIO
				  , 'pendente_termo_aditivo' OCORRENCIA
				  , NULL COMPLEMENTO
				  , A.id VALOR
				  
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
			  FROM 
				  ".DBPORTAL_BANCO."..zcrmportal_escala A
				  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
				  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				  INNER JOIN PAR              P ON P.COLIGADA = A.coligada
			  WHERE
					  CAST(A.documento AS VARCHAR(10)) IS NULL
				  AND A.datamudanca BETWEEN P.INICIO AND P.FIM
				 
				 
				 
				  
			UNION ALL
			/***************************************************************************************************************/
			/* SOBREAVISO     [tipo de retorno VALOR]                                                                      */
			/***************************************************************************************************************/
			  
			  SELECT 
					B.CODCOLIGADA
				  , B.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , C.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , D.DESCRICAO NOMESECAO
				  , CONVERT(DATE, A.dtcad, 3) DATA
				  , NULL HORARIO
				  , 'sobreaviso' OCORRENCIA
				, NULL COMPLEMENTO
				  , SUM(A.HORAS) VALOR

				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
				  
				  --,DATEADD(DAY,6,P.INICIO) INICIO
				  --,DATEADD(DAY,6,P.FIM) FIM
				  
			  FROM 
				  ".DBPORTAL_BANCO."..zcrmportal_substituicao_sobreaviso (NOLOCK) A
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  INNER JOIN PAR              P ON A.COLIGADA = P.COLIGADA
				  
			  WHERE 
					A.dtcad BETWEEN DATEADD(DAY,6,P.INICIO) AND DATEADD(DAY,6,P.FIM)
				  AND A.situacao = 2
				   
					
				  
			  GROUP BY 
				  B.CODCOLIGADA,
				  B.CHAPA,
				  B.NOME,
				  B.CODFUNCAO,
				  C.NOME,
				  B.CODSECAO,
				  D.DESCRICAO,
				  CONVERT(DATE, A.dtcad, 3)
				
			  HAVING 
				  SUM(A.horas) > 5760
			  
			  
							
			UNION ALL
			/***************************************************************************************************************/
			/* Excesso de Abono Gestor (Superior a 5 dias consecutivos)    [tipo de retorno COMPLEMENTO]                   */
			/***************************************************************************************************************/ 

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , DATA
				  , NULL HORARIO
				  , 'excesso_abono_gestor' OCORRENCIA
				  , '5 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)   COMPLEMENTO
				  , (UM + DOIS + TRES + QUATRO + CINCO) VALOR
				  
				  , BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO
			  FROM(
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , B.NOME
					  , B.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , B.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , A.DATA
					  , P.ABONO
					  , A.BASE
					, A.HTRAB
					, A.EXTRAAUTORIZADO
					, A.COMPENSADO
					, A.DESCANSO
					, A.FERIADO
			  
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.CODABONO = P.ABONO AND A.BASE > 0)>0 THEN 1 ELSE 0 END)UM
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 1) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) DOIS
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 2) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) TRES
																								
				  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 3) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) QUATRO                                                                            
				  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 4) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) CINCO                                                                           
				  , (SELECT DATA 
					FROM (
					  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
					  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
					  )X WHERE seq = 4) DATAFINAL
					  
				  FROM AAFHTFUN (NOLOCK) A
					  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  
				  WHERE
					  A.DATA BETWEEN P.INICIO AND P.FIM
					  
					  
					  
			  
			  )X
			   WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1


			  
			UNION ALL
			/***************************************************************************************************************/
			/* Trabalho superior à 6 (seis) dias consecutivos sem folga  [tipo de retorno COMPLEMENTO]                     */
			/***************************************************************************************************************/

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , DATA
				  , HORARIO
				  , 'trabalho_6dias' OCORRENCIA
				  , '7 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)  COMPLEMENTO
				  , (UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) VALOR
				
					, BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO
			  FROM(
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , B.NOME
					  , B.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , B.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , A.DATA
					  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.HTRAB >0)UM
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.HTRAB >0)DOIS
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.HTRAB >0)TRES
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.HTRAB >0)QUATRO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.HTRAB >0)CINCO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-5 = B.DATA AND B.HTRAB >0)SEIS
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)SETE
					  
					  ,(SELECT B.DATA FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)DATAFINAL
					  
					  , A.BASE
					, A.HTRAB
					, A.EXTRAAUTORIZADO
					, A.COMPENSADO
					, A.DESCANSO
					, A.FERIADO
					  
				  FROM AAFHTFUN (NOLOCK) A
					  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  
				  WHERE
					  A.DATA BETWEEN P.INICIO AND P.FIM
					   
						
					  
					  
			  )X
				WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1 AND SEIS = 1 AND SETE = 1



		)X

		LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia (NOLOCK) Z ON Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA

		--WHERE Z.codmotivo IS NULL 

		 WHERE ISNULL((SELECT COUNT(*) FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia  WHERE codcoligada = X.CODCOLIGADA AND chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND data = X.DATA AND ocorrencia = X.OCORRENCIA),0) <= 0

GROUP BY
	   X.NOME,
	   X.CODCOLIGADA,
	X.CHAPA,
	X.DATA,
	X.OCORRENCIA,
	X.COMPLEMENTO

		 

		ORDER BY
		X.NOME,
		X.DATA,
		X.OCORRENCIA

		";
		$this->dbrm->query($query);
		
	}
	
	public function ImportacaoOcorrencias($dados){

		try {

			$documento = $dados['documento'];
			
			$file_name = $documento['arquivo_importacao']['name'] ?? null;
			$file_type = $documento['arquivo_importacao']['type'] ?? null;
			$file_size = $documento['arquivo_importacao']['size'] ?? null;
			
			if($file_name == null) return responseJson('error', 'Nome do arquivo inválido.');
			if($file_type == null) return responseJson('error', 'Tipo do arquivo inválido.');
			if($file_size == null) return responseJson('error', 'Tamanho do arquivo inválido.');

			$arquivo_csv = fopen($documento['arquivo_importacao']['tmp_name'], "r");

			// valida dados do CSV
			while($line = fgetcsv($arquivo_csv, 1000, ";")){

				// CODCOLIGADA;CHAPA;DATA;SISTEMA;TIPOOCORRENCIA;RESULTADO
				if(preg_replace("/[^A-Z]/", "", $line[0]) != "CODCOLIGADA") return responseJson('error', 'Arquivo no formato inválido.1');
				if(preg_replace("/[^A-Z]/", "", $line[1]) != "CHAPA") return responseJson('error', 'Arquivo no formato inválido.2');
				if(preg_replace("/[^A-Z]/", "", $line[2]) != "DATA") return responseJson('error', 'Arquivo no formato inválido.3');
				if(preg_replace("/[^A-Z]/", "", $line[3]) != "SISTEMA") return responseJson('error', 'Arquivo no formato inválido.4');
				if(preg_replace("/[^A-Z]/", "", $line[4]) != "TIPOOCORRENCIA") return responseJson('error', 'Arquivo no formato inválido.5');
				if(preg_replace("/[^A-Z]/", "", $line[5]) != "RESULTADO") return responseJson('error', 'Arquivo no formato inválido.6');
				
				break;

			}

			// faz um bkp antes de importar
			$this->dbportal->query(" INSERT INTO zcrmportal_ocorrencia_bkp 
				SELECT *, getdate(), {$this->log_id}, null FROM zcrmportal_ocorrencia WHERE codmotivo IS NOT NULL AND uuid IS NOT NULL
			");

			// limpa a tabela para importar novamente
			$this->dbportal->query(" DELETE FROM zcrmportal_ocorrencia WHERE uuid IS NOT NULL ");

			$uuid = Uuid::uuid4()->toString();

			$row = 0;
			while($line = fgetcsv($arquivo_csv, 1000, ";")){

				$coligada   = $line[0];
				$chapa      = e0($line[1], 9);
				$data       = dtEn($line[2]);
				$sistema    = $line[3];
				$ocorrencia = mb_strtolower($line[4]);
				$resultado  = str_replace('"', '',$line[5]);

				$this->dbportal->query(" INSERT INTO zcrmportal_ocorrencia
						(
							codcoligada,
							chapa,
							data,
							usucad,
							dtcad,
							ocorrencia,
							sistema,
							resultado,
							uuid
						) VALUES (
							'{$coligada}',
							'{$chapa}',
							'{$data}',
							'{$this->log_id}',
							'{$this->now}',
							'{$ocorrencia}',
							'{$sistema}',
							'{$resultado}',
							'{$uuid}'
						)
					");
			}

			// atualiza justificativa dos gestores
			$this->dbportal->query("
				UPDATE
					zcrmportal_ocorrencia
				SET
					codmotivo = b.codmotivo,
					observacao = b.observacao,
					gestor = b.gestor,
					usualt = b.usualt,
					dtalt = b.dtalt
				FROM
					zcrmportal_ocorrencia a
					INNER JOIN zcrmportal_ocorrencia_bkp b 
						 ON b.codcoligada = a.codcoligada
						AND b.chapa = a.chapa
						AND b.data = a.data
						AND b.ocorrencia = a.ocorrencia
						AND b.sistema = a.sistema
						AND b.restaurado IS NULL
				WHERE
						a.sistema NOT IN ('RM')
					AND a.codcoligada = '{$this->coligada}'
			");
			if($this->dbportal->affectedRows() > 0){
				$this->dbportal->query(" UPDATE zcrmportal_ocorrencia_bkp SET restaurado = 1 WHERE codcoligada = '{$this->coligada}' AND restaurado IS NULL ");
			}

			// grava log
			$this->dbportal->query(" INSERT INTO zcrmportal_ocorrencia_importacao_log 
			(
				arquivo,
				datacadastro,
				sucesso
			) VALUES (
				'".base64_encode(file_get_contents($documento['arquivo_importacao']['tmp_name']))."', 
				'{$this->now}', 
				1) 
			");
		
			notificacao('success', 'Importação concluída com sucesso');
			return responseJson('success', 'Importação concluída com sucesso.');
		} catch (\Exception | \Error $e) {

			// grava log de erro
			try {
				$query = $this->dbportal->query(" INSERT INTO zcrmportal_ocorrencia_importacao_log 
				(
					arquivo,
					datacadastro,
					erro
				) VALUES (
					'".base64_encode(file_get_contents($documento['arquivo_importacao']['tmp_name']))."', 
					'{$this->now}', 
					'".$this->dbportal->escapeString($e->getMessage())."'
					)
				");
			} catch (\Exception | \Error $e) {
				return responseJson('error', '<b>Erro interno:</b><br><code class="language-markup">'.$e->getMessage().'</code>');
			}
			
			return responseJson('error', '<b>Erro interno:</b><br><code class="language-markup">'.$e->getMessage().'</code>');
			
		}

	}




	public function CargaDadosOcorrencia(){

		$query = "
		            
		WITH 

		PAR AS 
			(
			SELECT 
				COLIGADA = '1'
				, INICIO   = '2023-05-16'
				, FIM      = '2023-06-15'
				, HORARIO  = '020,	0012,	0013,	0005,	0033,	Cons.010,	0004,	0055,	006,	0017,	0011,	007,	07,	0202,	0203,	015,	TESTE,	019,	Cons.009,	0007,	0208,	0027,	0023,	108,	110,	092,	104,	084,	118,	0207,	0204,	113,	095,	0032,	03,	100,	0048,	0041,	0042,	0043,	0044,	0026,	0040'        
				, ABONO    = '029'
			)


SELECT
X.CODCOLIGADA,
X.CHAPA,
X.DATA,
X.VALOR,
NULL,
1,
GETDATE(),
X.OCORRENCIA,
NULL,
NULL,
NULL,
NULL,
X.COMPLEMENTO

		FROM (





		/***************************************************************************************************************/
		/* Horas Extras acima do permitido                                                                             */
		/***************************************************************************************************************/
			SELECT 
				* 
			FROM (
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'extra_permitido' OCORRENCIA
					, NULL COMPLEMENTO
					, (CASE WHEN BASE  = 420 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN BASE  = 480 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN BASE  = 528 AND EXTRAEXECUTADO >  72 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN BASE  = 540 AND EXTRAEXECUTADO >  60 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												THEN HTRAB 
							ELSE 0 END) VALOR
				
					, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
				FROM 
					PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					  
					
					AND (CASE WHEN BASE  = 420 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN BASE  = 480 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN BASE  = 528 AND EXTRAEXECUTADO >  72 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN BASE  = 540 AND EXTRAEXECUTADO >  60 AND FERIADO = 0 THEN EXTRAEXECUTADO
							  WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							  WHEN FERIADO > 0 AND HTRAB > 0
							  AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
														(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												THEN HTRAB
						   ELSE 0 END) > 0
			)XX
			 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS NOT IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(ESCALA_ESPECIAL,0) = 1 )

		UNION ALL

		/***************************************************************************************************************/
		/* Horas Extras em escalas específicas (Zero HE)                                                               */
		/***************************************************************************************************************/
			/* Horários 12x36 */
			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, A.NOME
				, A.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, A.CODSECAO
				, D.DESCRICAO NOMESECAO
				, B.DATA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
				, 'extra' OCORRENCIA
				, NULL COMPLEMENTO
				, (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
					   ELSE 0 END) VALOR
				
				, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO 
			FROM 
				PFUNC (NOLOCK) A
					LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
					INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
					INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
					INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
			
			WHERE
					B.DATA BETWEEN M.INICIO AND M.FIM
				  
				 
				AND (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
					   ELSE 0 END) > 0
			
			UNION ALL
			
			/* Horários de Revezamento */
			SELECT 
				* 
			FROM (
			  
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'extra' OCORRENCIA
					, NULL COMPLEMENTO
					, (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												 THEN HTRAB
							ELSE 0 END) VALOR
					
					, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
				FROM 
					PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					
					
					AND (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
							WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
							WHEN FERIADO > 0 AND HTRAB > 0
							AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
													(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
												 THEN HTRAB
							ELSE 0 END) > 0
			)XX
			 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(ESCALA_ESPECIAL,0) = 1 )

		UNION ALL
		/***************************************************************************************************************/
		/* Jornada Diária acima de 11 e 10 horas                                                                       */
		/***************************************************************************************************************/
			SELECT * FROM(
				SELECT

					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, C.NOME NOMEFUNCAO
					, A.CODSECAO
					, D.DESCRICAO NOMESECAO
					, B.DATA
					, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					, 'jornada' OCORRENCIA
					, NULL COMPLEMENTO
					, B.HTRAB VALOR
					
					, BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
				FROM 
					PFUNC (NOLOCK) A 
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				
				WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					AND B.HTRAB > 600
					AND B.BASE <= 540
					 
					 
				)x where  
					(SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA <= x.DATA)) = 0
							
		UNION ALL
		/***************************************************************************************************************/
		/* Falta de registro adequado de jornada de trabalho (Registro Manual)                                         */
		/***************************************************************************************************************/
			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, B.NOME
				, B.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, B.CODSECAO
				, D.DESCRICAO NOMESECAO
				, A.DATA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO	    
				, 'registro_manual' OCORRENCIA
				, NULL COMPLEMENTO
				, A.BATIDA VALOR
				
				, NULL BASE, NULL HTRAB, NULL EXTRAAUTORIZADO, NULL COMPENSADO, NULL DESCANSO, NULL FERIADO
			FROM 
				ABATFUN (NOLOCK) A
				INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				INNER JOIN PAR              M ON M.COLIGADA = A.CODCOLIGADA
			
			WHERE
					A.DATA BETWEEN M.INICIO AND M.FIM
				AND A.STATUS = 'D'
				 
						
				 UNION ALL
				 /***************************************************************************************************************/
				 /* Trabalho em DSR/Folga [COMPENSADO]                                                                          */
				 /***************************************************************************************************************/
					 SELECT
						   A.CODCOLIGADA
						 , A.CHAPA
						 , A.NOME
						 , A.CODFUNCAO
						 , C.NOME NOMEFUNCAO
						 , A.CODSECAO
						 , D.DESCRICAO NOMESECAO
						 , B.DATA
						 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						 , 'trabalho_dsr_folga' OCORRENCIA
						 , NULL COMPLEMENTO
						 , (CASE WHEN (COMPENSADO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) VALOR
					 
						 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
						 
					 FROM 
						 PFUNC (NOLOCK) A
							 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
							 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
							 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
					 
					 WHERE
							 B.DATA BETWEEN M.INICIO AND M.FIM
						 
						  
						 AND (CASE WHEN (COMPENSADO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) > 0
				 
				 
				 UNION ALL
				 
				 /***************************************************************************************************************/
				 /* Trabalho em DSR/Folga [DESCANSO]                                                                            */
				 /***************************************************************************************************************/
				SELECT 
					* 
				FROM ( 
					 SELECT
						   A.CODCOLIGADA
						 , A.CHAPA
						 , A.NOME
						 , A.CODFUNCAO
						 , C.NOME NOMEFUNCAO
						 , A.CODSECAO
						 , D.DESCRICAO NOMESECAO
						 , B.DATA
						 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						 , 'trabalho_dsr_folga_descanso' OCORRENCIA
						 , NULL COMPLEMENTO
						 , (CASE WHEN (DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) VALOR
					 
						 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
						 
					 FROM 
						 PFUNC (NOLOCK) A
							 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
							 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
							 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
					 
					 WHERE
							 B.DATA BETWEEN M.INICIO AND M.FIM
						 
						  
						 AND (CASE WHEN (DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
								 WHEN FERIADO > 0 AND HTRAB > 0
									 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
															 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													  THEN HTRAB
								 ELSE 0 END) > 0              
					)XX
					WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(EXTRA_FERIADO,0) = 1 )
					
		UNION ALL
		/***************************************************************************************************************/
		/* Trabalho em Férias/Afastamentos                                                                             */
		/***************************************************************************************************************/

			SELECT
				  CODCOLIGADA
				, CHAPA
				, NOME
				, CODFUNCAO
				, NOMEFUNCAO
				, CODSECAO
				, NOMESECAO
				, DATA
				, HORARIO
				, OCORRENCIA
				, CASE 
					WHEN FERIAS IS NOT NULL THEN 'Férias'
					WHEN AFAST IS NOT NULL THEN 'Afastamento/Atestado'
					ELSE NULL
				  END COMPLEMENTO
				, CASE 
					WHEN FERIAS IS NOT NULL THEN 1
					WHEN AFAST IS NOT NULL THEN 2
					ELSE NULL
				  END VALOR
				, NULL BASE
				, NULL HTRAB
				, NULL EXTRAAUTORIZADO
				, NULL COMPENSADO
				, NULL DESCANSO
				, NULL FERIADO
				
			FROM (
			
				SELECT 
					AX.* 
				FROM(
				
					SELECT
						  A.CODCOLIGADA
						, A.CHAPA
						, A.NOME
						, A.CODFUNCAO
						, C.NOME NOMEFUNCAO
						, A.CODSECAO
						, D.DESCRICAO NOMESECAO
						, B.DATA
						, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
							(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
						, 'trabalho_ferias_afastamento' OCORRENCIA
						, (SELECT 'Férias' FROM PFUFERIASPER (NOLOCK) F  WHERE F.CODCOLIGADA = A.CODCOLIGADA AND F.CHAPA = A.CHAPA AND B.DATA BETWEEN F.DATAINICIO AND F.DATAFIM) FERIAS
						, (SELECT 'Afastamento' FROM PFHSTAFT (NOLOCK) H WHERE H.CODCOLIGADA = A.CODCOLIGADA AND H.CHAPA = A.CHAPA AND B.DATA BETWEEN H.DTINICIO AND  ISNULL(H.DTFINAL, '2050-12-01') ) AFAST
						, (SELECT COUNT(*) FROM ABATFUN (NOLOCK) G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) BAT
						, B.ABONO
					
					FROM 
						PFUNC (NOLOCK) A
						LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
						
					
					WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
										
				
				)AX
				WHERE	
					(AX.FERIAS IS NOT NULL OR AX.AFAST IS NOT NULL) AND AX.BAT > 0 AND ( ABONO = 0 OR ABONO >= 240) /* A pedido ignorado se o abono foi menor que 4horas*/
									
			)BX
					   


		UNION ALL
		/***************************************************************************************************************/
		/* InterJornada                                                                                                */
		/***************************************************************************************************************/

		SELECT 
			  CODCOLIGADA
			, CHAPA
			, NOME
			, CODFUNCAO
			, NOMEFUNCAO
			, CODSECAO
			, NOMESECAO
			, DATAREFERENCIA
			, HORARIO
			, OCORRENCIA
			, AVISO + ' [' +
				(CASE WHEN DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) IS NULL or DATEDIFF(minute ,BAT_anterior, BAT_atual) > 660 THEN '[Bat.Inválida]'
					  ELSE DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) END) 
				+ ']' COMPLEMENTO
			, 1 VALOR

			, NULL BASE
			, NULL HTRAB
			, NULL EXTRAAUTORIZADO
			, NULL COMPENSADO
			, NULL DESCANSO
			, NULL FERIADO

		FROM(

			SELECT
				  A.CODCOLIGADA
				, A.CHAPA
				, B.NOME
				, B.CODFUNCAO
				, C.NOME NOMEFUNCAO
				, B.CODSECAO
				, D.DESCRICAO NOMESECAO
				, A.DATAREFERENCIA
				, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATAREFERENCIA)) HORARIO
				, 'interjornada' OCORRENCIA
				, 1 VALOR
				, AAVISO.DESCRICAO AVISO 
				,(SELECT MAX(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA-1 = M.DATAREFERENCIA AND NATUREZA = 1) BAT_anterior
				,(SELECT MIN(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA = M.DATAREFERENCIA AND NATUREZA = 0) BAT_atual
			
			FROM 
				AAVISOCALCULADO A (NOLOCK)
					INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				, 
				AAVISO,
				PAR
				
			WHERE 
					
					A.CODCOLIGADA = PAR.COLIGADA
					AND A.CODAVISO = AAVISO.CODAVISO 
					AND A.CODCOLIGADA = PAR.COLIGADA
					AND A.DATAREFERENCIA BETWEEN PAR.INICIO AND PAR.FIM
					AND A.CODAVISO = '1'
					

					)X
			UNION ALL
			/***************************************************************************************************************/
			/* IntraJornada    [tipo de retorno COMPLEMENTO]                                                               */
			/***************************************************************************************************************/
			  
			  SELECT 
				  CODCOLIGADA
				, CHAPA
				, NOME
				, CODFUNCAO
				, NOMEFUNCAO
				, CODSECAO
				, NOMESECAO
				, DATA
				, HORARIO
				, OCORRENCIA
				, ' Tempo Mínimo de refeição (IntraJornada) ' + dbo.mintotime(REF_OBRIGATORIO) + ' ' + 'não realizado [' + dbo.mintotime(REF_REALIZADO) + ']' COMPLEMENTO
				, VALOR
				
				, BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO
			  FROM (
			  
				SELECT 
						 ISNULL((SELECT (FIM-INICIO) TEMPO
						FROM ABATHOR M
						WHERE 
						M.CODCOLIGADA = XX.CODCOLIGADA
						AND M.CODHORARIO  = XX.HORARIO
						AND TIPO = 4 
						AND INDICE = 1
						),0) REF_OBRIGATORIO
				  , *
					
				FROM (
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , A.NOME
					  , A.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , A.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , B.DATA
					  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
					  , 'interjornada' OCORRENCIA
					  , NULL VALOR
					  
					  ,ISNULL((SELECT SUM(DATEDIFF(MINUTE,INICIO,FIM)) FROM AOCORRENCIACALCULADA P
					  WHERE 
						A.CODCOLIGADA = P.CODCOLIGADA 
					  AND A.CHAPA = P.CHAPA 
					  AND B.DATA = P.DATAREFERENCIA
					  AND TIPOOCORRENCIA IN ('AREF')),0) REF_REALIZADO
				  
					  , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
				  FROM 
					  PFUNC (NOLOCK) A
						  LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
						  INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
						  INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
						  INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
				  
				  WHERE
						B.DATA BETWEEN M.INICIO AND M.FIM
					  AND B.HTRAB > 0
					   
					   
			  
				)XX
				   WHERE XX.HORARIO IN(
										  SELECT 
											DISTINCT CODHORARIO
										  FROM ABATHOR, PAR
										  WHERE ABATHOR.CODCOLIGADA = PAR.COLIGADA
										  AND TIPO = 5
									 )
			  )XY
			  WHERE REF_OBRIGATORIO-REF_REALIZADO > 10


			UNION ALL
			/***************************************************************************************************************/
			/* Registros britânicos (Apontamentos Manuais)    [tipo de retorno COMPLEMENTO]                                */
			/***************************************************************************************************************/ 
			 
			  SELECT
					A.CODCOLIGADA
				  , A.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , D.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , C.DESCRICAO NOMESECAO
				  , A.DATA
				  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
				  , 'registro_britanico' OCORRENCIA
				  , CONCAT(dbo.mintotime(MIN(B1.BATIDA)), ' ', dbo.mintotime(MAX(B1.BATIDA))) COMPLEMENTO
				  , 1 VALOR
					
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
				  
				  
			  FROM AAFHTFUN (NOLOCK) A
				  INNER JOIN PFUNC   (NOLOCK) B  ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
				  INNER JOIN PSECAO  (NOLOCK) C  ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODSECAO
				  INNER JOIN PFUNCAO (NOLOCK) D  ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
				  INNER JOIN ABATFUN (NOLOCK) B1 ON B1.CODCOLIGADA = A.CODCOLIGADA AND B1.CHAPA = A.CHAPA AND ISNULL(B1.DATAREFERENCIA, B1.DATA) = A.DATA AND B1.STATUS NOT IN ('T')
				  INNER JOIN AJORHOR (NOLOCK) J1 ON J1.CODCOLIGADA = A.CODCOLIGADA AND J1.CODHORARIO = (SELECT CODHORARIO FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
							  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA))
				  LEFT  JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
			  
				  
			  WHERE
					  A.CODCOLIGADA = P.COLIGADA
				  AND A.DATA BETWEEN P.INICIO AND P.FIM
				  
			  GROUP BY
				  A.CODCOLIGADA,
				  A.CHAPA,
				  A.DATA,
				  B.NOME,
				  B.CODSECAO,
				  C.DESCRICAO,
				  B.CODFUNCAO,
				  D.NOME
			  
			  HAVING
					  MIN(B1.BATIDA) = MIN(J1.BATINICIO)
				  AND MAX(B1.BATIDA) = MAX(J1.BATFIM)
							

			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA MENOS DE 6 MESES    [tipo de retorno COMPLEMENTO]                                           */
			/***************************************************************************************************************/ 
			 
			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , ULT_MUD_DATA DATA
				  , NULL HORARIO
				  , 'troca_menor_6_meses' OCORRENCIA
				  , CONVERT(VARCHAR(2),DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA)) +
					+ (CASE WHEN DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) > 1 THEN ' (meses) --->' ELSE ' (mês) --->' END) 
					+ CONVERT(VARCHAR(10),ULT_MUD_DATA,103) + ' ['+ULT_MUD_CODHOR+']' + ' - ' + CONVERT(VARCHAR(10),PEN_MUD_DATA,103) + ' [' + PEN_MUD_CODHOR + ']'  COMPLEMENTO
				  , DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) VALOR
				  
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
			  FROM (
			  
				SELECT
					  A.CODCOLIGADA
					, A.CHAPA
					, A.NOME
					, A.CODFUNCAO
					, B.NOME NOMEFUNCAO
					, A.CODSECAO
					, C.DESCRICAO NOMESECAO
					, (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ULT_MUD_DATA
					
					, (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA  AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ) ULT_MUD_CODHOR
				  
					  ,(SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
						  AND DTMUDANCA >
										  (SELECT 
											  CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
															ELSE MAX(DTMUDANCA) END
										   FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
											  AND DTMUDANCA <=
												  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
														  AND DTMUDANCA <> 
															  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
														   AND CODHORARIO <> 
															  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
											  AND CODHORARIO <> 
													(SELECT 
															CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
															ELSE CODHORARIO END
														FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
														(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
															  AND DTMUDANCA <> 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
															   AND CODHORARIO <> 
																  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
												 )
										  )	
						  ) PEN_MUD_DATA
	  
					  ,(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
						   (SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
							  AND DTMUDANCA >
											  (SELECT 
												  CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
																ELSE MAX(DTMUDANCA) END
											   FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
												  AND DTMUDANCA <=
													  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
															  AND DTMUDANCA <> 
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
															   AND CODHORARIO <> 
																  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
												  AND CODHORARIO <> 
														(SELECT 
																CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
																ELSE CODHORARIO END
															FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
															(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																  AND DTMUDANCA <> 
																	  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																   AND CODHORARIO <> 
																	  (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																		  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
													 )
											  )	
							 )
						 ) PEN_MUD_CODHOR

				FROM
					PFUNC A
					INNER JOIN PFUNCAO (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
					INNER JOIN PSECAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
					INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
			  
				WHERE
						A.CODSITUACAO NOT IN ('D')
					AND (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) BETWEEN P.INICIO AND P.FIM
					

				  
			  )X
				 WHERE 
					DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) < 6 
			  
			  
			  
			  
			  
			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA MENOR DE 10 DIAS    [tipo de retorno VALOR]                                                 */
			/***************************************************************************************************************/

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , ULT_DTMUDANCA DATA
				  , NULL HORARIO
				  , 'troca_menor_10_dias' OCORRENCIA
				, NULL COMPLEMENTO
				  , DIAS_ALTERACAO VALOR

				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
			  FROM (
				  SELECT
					  A.CODCOLIGADA,
					  A.CHAPA,
					  A.NOME,
					  A.CODFUNCAO,
					  B.NOME NOMEFUNCAO,
					  A.CODSECAO,
					  C.DESCRICAO NOMESECAO,
					  (
						SELECT
							MAX(ABS(DATEDIFF(DAY, H.RECCREATEDON, H.DTMUDANCA)))
						FROM
							PFHSTHOR H
						WHERE
							H.DTMUDANCA = (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
						) DIAS_ALTERACAO,
						(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ULT_DTMUDANCA
					  
				  FROM
					  PFUNC A
					  INNER JOIN PFUNCAO  (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
					  INNER JOIN PSECAO   (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
					  INNER JOIN PAR               P ON P.COLIGADA = A.CODCOLIGADA
				  WHERE
						  A.CODSITUACAO NOT IN ('D')
					AND (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) BETWEEN P.INICIO AND P.FIM
					
					   
				  
			  )X
				  WHERE 
					  DIAS_ALTERACAO < 10
			  
			  
			UNION ALL
			/***************************************************************************************************************/
			/* TROCA DE ESCALA SEM ANEXO DO TERMO ADITIVO    [tipo de retorno VALOR]                                       */
			/***************************************************************************************************************/
						
			  SELECT 
					B.CODCOLIGADA
				  , B.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , C.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , D.DESCRICAO NOMESECAO
				  , A.datamudanca DATA
				  , NULL HORARIO
				  , 'pendente_termo_aditivo' OCORRENCIA
				  , NULL COMPLEMENTO
				  , A.id VALOR
				  
				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
			  FROM 
				  ".DBPORTAL_BANCO."..zcrmportal_escala A
				  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
				  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
				  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
				  INNER JOIN PAR              P ON P.COLIGADA = A.coligada
			  WHERE
					  CAST(A.documento AS VARCHAR(10)) IS NULL
				  AND A.datamudanca BETWEEN P.INICIO AND P.FIM
				 
				 
				 
				  
			UNION ALL
			/***************************************************************************************************************/
			/* SOBREAVISO     [tipo de retorno VALOR]                                                                      */
			/***************************************************************************************************************/
			  
			  SELECT 
					B.CODCOLIGADA
				  , B.CHAPA
				  , B.NOME
				  , B.CODFUNCAO
				  , C.NOME NOMEFUNCAO
				  , B.CODSECAO
				  , D.DESCRICAO NOMESECAO
				  , CONVERT(DATE, A.dtcad, 3) DATA
				  , NULL HORARIO
				  , 'sobreaviso' OCORRENCIA
				, NULL COMPLEMENTO
				  , SUM(A.HORAS) VALOR

				  , NULL BASE
				  , NULL HTRAB
				  , NULL EXTRAAUTORIZADO
				  , NULL COMPENSADO
				  , NULL DESCANSO
				  , NULL FERIADO
				  
				  --,DATEADD(DAY,6,P.INICIO) INICIO
				  --,DATEADD(DAY,6,P.FIM) FIM
				  
			  FROM 
				  ".DBPORTAL_BANCO."..zcrmportal_substituicao_sobreaviso (NOLOCK) A
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  INNER JOIN PAR              P ON A.COLIGADA = P.COLIGADA
				  
			  WHERE 
					A.dtcad BETWEEN DATEADD(DAY,6,P.INICIO) AND DATEADD(DAY,6,P.FIM)
				  AND A.situacao = 2
				   
					
				  
			  GROUP BY 
				  B.CODCOLIGADA,
				  B.CHAPA,
				  B.NOME,
				  B.CODFUNCAO,
				  C.NOME,
				  B.CODSECAO,
				  D.DESCRICAO,
				  CONVERT(DATE, A.dtcad, 3)
				
			  HAVING 
				  SUM(A.horas) > 5760
			  
			  
							
			UNION ALL
			/***************************************************************************************************************/
			/* Excesso de Abono Gestor (Superior a 5 dias consecutivos)    [tipo de retorno COMPLEMENTO]                   */
			/***************************************************************************************************************/ 

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , DATA
				  , NULL HORARIO
				  , 'excesso_abono_gestor' OCORRENCIA
				  , '5 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)   COMPLEMENTO
				  , (UM + DOIS + TRES + QUATRO + CINCO) VALOR
				  
				  , BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO
			  FROM(
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , B.NOME
					  , B.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , B.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , A.DATA
					  , P.ABONO
					  , A.BASE
					, A.HTRAB
					, A.EXTRAAUTORIZADO
					, A.COMPENSADO
					, A.DESCANSO
					, A.FERIADO
			  
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.CODABONO = P.ABONO AND A.BASE > 0)>0 THEN 1 ELSE 0 END)UM
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 1) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) DOIS
					  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 2) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) TRES
																								
				  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 3) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) QUATRO                                                                            
				  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																								  AND 
																								(SELECT DATA FROM (
																								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																								  )X WHERE seq = 4) 
																								= B.DATA )>0 THEN 1 ELSE 0 END) CINCO                                                                           
				  , (SELECT DATA 
					FROM (
					  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
					  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
					  )X WHERE seq = 4) DATAFINAL
					  
				  FROM AAFHTFUN (NOLOCK) A
					  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  
				  WHERE
					  A.DATA BETWEEN P.INICIO AND P.FIM
					  
					  
					  
			  
			  )X
			   WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1


			  
			UNION ALL
			/***************************************************************************************************************/
			/* Trabalho superior à 6 (seis) dias consecutivos sem folga  [tipo de retorno COMPLEMENTO]                     */
			/***************************************************************************************************************/

			  SELECT
					CODCOLIGADA
				  , CHAPA
				  , NOME
				  , CODFUNCAO
				  , NOMEFUNCAO
				  , CODSECAO
				  , NOMESECAO
				  , DATA
				  , HORARIO
				  , 'trabalho_6dias' OCORRENCIA
				  , '7 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)  COMPLEMENTO
				  , (UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) VALOR
				
					, BASE
				  , HTRAB
				  , EXTRAAUTORIZADO
				  , COMPENSADO
				  , DESCANSO
				  , FERIADO
			  FROM(
			  
				  SELECT
						A.CODCOLIGADA
					  , A.CHAPA
					  , B.NOME
					  , B.CODFUNCAO
					  , C.NOME NOMEFUNCAO
					  , B.CODSECAO
					  , D.DESCRICAO NOMESECAO
					  , A.DATA
					  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
					(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.HTRAB >0)UM
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.HTRAB >0)DOIS
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.HTRAB >0)TRES
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.HTRAB >0)QUATRO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.HTRAB >0)CINCO
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-5 = B.DATA AND B.HTRAB >0)SEIS
					  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)SETE
					  
					  ,(SELECT B.DATA FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)DATAFINAL
					  
					  , A.BASE
					, A.HTRAB
					, A.EXTRAAUTORIZADO
					, A.COMPENSADO
					, A.DESCANSO
					, A.FERIADO
					  
				  FROM AAFHTFUN (NOLOCK) A
					  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
					  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
					  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
					  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
					  
				  WHERE
					  A.DATA BETWEEN P.INICIO AND P.FIM
					   
						
					  
					  
			  )X
				WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1 AND SEIS = 1 AND SETE = 1



		)X

	
	
GROUP BY
	   X.NOME,
	   X.CODCOLIGADA,
	X.CHAPA,
	X.DATA,
	X.VALOR,
	X.OCORRENCIA,
	X.COMPLEMENTO

		 

		ORDER BY
		X.NOME,
		X.DATA,
		X.OCORRENCIA

		";
		$result = $this->dbrm->query($query);
		$dados = $result->getResultArray();
		foreach($dados as $key => $Ocorrencia){

			$this->dbportal->query(" UPDATE zcrmportal_ocorrencia SET resultado = '{$Ocorrencia['VALOR']}', complemento = '{$Ocorrencia['COMPLEMENTO']}', sistema = 'RM' WHERE codcoligada = '{$Ocorrencia['CODCOLIGADA']}' AND chapa = '{$Ocorrencia['CHAPA']}' AND data = '".date('Y-m-d', strtotime($Ocorrencia['DATA']))."' AND ocorrencia = '{$Ocorrencia['OCORRENCIA']}' AND resultado IS NULL ");

			unset($dados[$key], $key, $Ocorrencia);
		}

		
	exit('fim');
		
	}

	// -------------------------------------------------------
    // Lista ocorrências
    // -------------------------------------------------------
    public function ListarOcorrenciaArquivoMorto($dados){
		
        $data_inicio = $dados['data_inicio'] ?? null;
        $data_fim = $dados['data_fim'] ?? null;
        $filial = $dados['filial'];
        $ja_justificados = $dados['ja_justificados'] ?? null;
        $abono = '029';
		
		$in_secao = "";

        $where_filial = (strlen(trim($filial)) > 0) ? " AND A.CODFILIAL = '{$filial}' " : "";

        // tipos de ocorrencias
        $where_extra_permitido = (($dados['extra_permitido'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_extra = (($dados['extra'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_jornada = (($dados['jornada'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_trabalho_dsr_folga = (($dados['trabalho_dsr_folga'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_trabalho_ferias_afastamento = (($dados['trabalho_ferias_afastamento'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_registro_manual = (($dados['registro_manual'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_trabalho_6dias = (($dados['trabalho_6dias'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_excesso_abono_gestor = (($dados['excesso_abono_gestor'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_interjornada = (($dados['interjornada'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_registro_britanico = (($dados['registro_britanico'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_ja_justificados = ($ja_justificados == NULL) ? " WHERE Z.codmotivo IS NULL " : "";
        $where_sobreaviso = (($dados['sobreaviso'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_troca_menor_6_meses = (($dados['troca_menor_6_meses'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_troca_menor_10_dias = (($dados['troca_menor_10_dias'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        $where_pendente_termo_aditivo = (($dados['pendente_termo_aditivo'] ?? 0) != 1) ? " AND 1 = 2 " : "";
        
        // filtro por seção
        if(is_array($dados['secao'] ?? "")){
            $in_secao = "";
            foreach($dados['secao'] as $key => $CodSecao){
                $in_secao .= "'{$CodSecao}',";
            }
            $in_secao = " AND A.CODSECAO IN (".rtrim($in_secao, ',').") ";
        }


		/*
		$dados['periodo'] = "2023-03-012023-04-05";
		$data_inicio = "2023-03-01";
		$data_fim = "2023-04-05";
		*/
		

         $query = "
            
					WITH 

					PAR AS 
						(
						SELECT 
							COLIGADA = '{$this->coligada}'
							, INICIO   =  '{$data_inicio}'
							, FIM      = '{$data_fim}'
							, HORARIO  = '020,	0012,	0013,	0005,	0033,	Cons.010,	0004,	0055,	006,	0017,	0011,	007,	07,	0202,	0203,	015,	TESTE,	019,	Cons.009,	0007,	0208,	0027,	0023,	108,	110,	092,	104,	084,	118,	0207,	0204,	113,	095,	0032,	03,	100,	0048,	0041,	0042,	0043,	0044,	0026,	0040'        
							, ABONO    = '{$abono}'
						)


					/*SELECT
						X.CODCOLIGADA,
						X.CHAPA,
						X.NOME,
						X.CODFUNCAO,
						X.NOMEFUNCAO,
						X.CODSECAO,
						X.NOMESECAO,
						X.DATA,
						X.OCORRENCIA,
						X.VALOR,
						X.COMPLEMENTO,
					  Z.codmotivo,
					  Z.descricao_motivo,
					  Z.ocorrencia log_ocorrencia,
					  Z.observacao,
					  Z.gestor*/

					  [SELECT]


					FROM (





					/***************************************************************************************************************/
					/* Horas Extras acima do permitido                                                                             */
					/***************************************************************************************************************/
						SELECT 
							* ,
							'RM' SISTEMA
						FROM (
							SELECT
								  A.CODCOLIGADA
								, A.CHAPA
								, A.NOME
								, A.CODFUNCAO
								, C.NOME NOMEFUNCAO
								, A.CODSECAO
								, D.DESCRICAO NOMESECAO
								, B.DATA
								, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
								, 'extra_permitido' OCORRENCIA
								, NULL COMPLEMENTO
								, (CASE WHEN BASE  = 420 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN BASE  = 480 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN BASE  = 528 AND EXTRAEXECUTADO >  72 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN BASE  = 540 AND EXTRAEXECUTADO >  60 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN BASE  = 440 AND EXTRAEXECUTADO >  120 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
										WHEN FERIADO > 0 AND HTRAB > 0
										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
															THEN HTRAB 
										ELSE 0 END) VALOR
							
								, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
							FROM 
								PFUNC (NOLOCK) A
									LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
									INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
									INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
									INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							
							WHERE
									B.DATA BETWEEN M.INICIO AND M.FIM
								 {$in_secao} 
								{$where_filial}
								AND (CASE WHEN BASE  = 420 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
										  WHEN BASE  = 480 AND EXTRAEXECUTADO > 120 AND FERIADO = 0 THEN EXTRAEXECUTADO
										  WHEN BASE  = 528 AND EXTRAEXECUTADO >  72 AND FERIADO = 0 THEN EXTRAEXECUTADO
										  WHEN BASE  = 540 AND EXTRAEXECUTADO >  60 AND FERIADO = 0 THEN EXTRAEXECUTADO
										  WHEN BASE  = 440 AND EXTRAEXECUTADO >  120 AND FERIADO = 0 THEN EXTRAEXECUTADO
										  WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
										  WHEN FERIADO > 0 AND HTRAB > 0
										  AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																	(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
															THEN HTRAB
									   ELSE 0 END) > 0
									   AND (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
						)XX
						 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS NOT IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(ESCALA_ESPECIAL,0) = 1 )

					UNION ALL

					/***************************************************************************************************************/
					/* Horas Extras em escalas específicas (Zero HE)                                                               */
					/***************************************************************************************************************/
						/* Horários 12x36 */
						SELECT
							  A.CODCOLIGADA
							, A.CHAPA
							, A.NOME
							, A.CODFUNCAO
							, C.NOME NOMEFUNCAO
							, A.CODSECAO
							, D.DESCRICAO NOMESECAO
							, B.DATA
							, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
							, 'extra' OCORRENCIA
							, NULL COMPLEMENTO
							, (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
								   ELSE 0 END) VALOR
							
							, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO ,
							'RM' SISTEMA
						FROM 
							PFUNC (NOLOCK) A
								LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
								INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
								INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
								INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
						
						WHERE
								B.DATA BETWEEN M.INICIO AND M.FIM
							 {$in_secao} 
							 {$where_filial}
							AND (CASE WHEN BASE  > 540 AND HTRAB > 0 AND EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
								   ELSE 0 END) > 0
							AND (SELECT extra_especial FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
						
						UNION ALL
						
						/* Horários de Revezamento */
						SELECT 
							* ,
							'RM' SISTEMA
						FROM (
						  
							SELECT
								  A.CODCOLIGADA
								, A.CHAPA
								, A.NOME
								, A.CODFUNCAO
								, C.NOME NOMEFUNCAO
								, A.CODSECAO
								, D.DESCRICAO NOMESECAO
								, B.DATA
								, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
								, 'extra' OCORRENCIA
								, NULL COMPLEMENTO
								, (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
										WHEN FERIADO > 0 AND HTRAB > 0
										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
															 THEN HTRAB
										ELSE 0 END) VALOR
								
								, BASE, HTRAB, EXTRAEXECUTADO, COMPENSADO, DESCANSO, FERIADO
							FROM 
								PFUNC (NOLOCK) A
									LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
									INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
									INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
									INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							
							WHERE
									B.DATA BETWEEN M.INICIO AND M.FIM
								{$in_secao}
								{$where_filial}
								AND (CASE WHEN EXTRAEXECUTADO > 0 AND FERIADO = 0 THEN EXTRAEXECUTADO
										WHEN (COMPENSADO > 0 OR DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
										WHEN FERIADO > 0 AND HTRAB > 0
										AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
															 THEN HTRAB
										ELSE 0 END) > 0
								AND (SELECT extra_especial FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
						)XX
						 WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(ESCALA_ESPECIAL,0) = 1 )

					UNION ALL
					/***************************************************************************************************************/
					/* Jornada Diária acima de 11 e 10 horas                                                                       */
					/***************************************************************************************************************/
						SELECT *,
							'RM' SISTEMA FROM(
							SELECT

								  A.CODCOLIGADA
								, A.CHAPA
								, A.NOME
								, A.CODFUNCAO
								, C.NOME NOMEFUNCAO
								, A.CODSECAO
								, D.DESCRICAO NOMESECAO
								, B.DATA
								, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
								, 'jornada' OCORRENCIA
								, NULL COMPLEMENTO
								, B.HTRAB VALOR
								
								, BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
							FROM 
								PFUNC (NOLOCK) A 
									LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
									INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
									INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
									INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							
							WHERE
									B.DATA BETWEEN M.INICIO AND M.FIM
								AND B.HTRAB > 600
								AND (SELECT excesso_jornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
								AND B.BASE <= 540
								 {$in_secao}
								 {$where_filial}
							)x where  
								(SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE x.CODCOLIGADA = N.CODCOLIGADA AND x.CHAPA = N.CHAPA AND N.DTMUDANCA <= x.DATA)) = 0
										
					UNION ALL
					/***************************************************************************************************************/
					/* Falta de registro adequado de jornada de trabalho (Registro Manual)                                         */
					/***************************************************************************************************************/
						SELECT
							  A.CODCOLIGADA
							, A.CHAPA
							, B.NOME
							, B.CODFUNCAO
							, C.NOME NOMEFUNCAO
							, B.CODSECAO
							, D.DESCRICAO NOMESECAO
							, A.DATA
							, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO	    
							, 'registro_manual' OCORRENCIA
							, NULL COMPLEMENTO
							, A.BATIDA VALOR
							
							, NULL BASE, NULL HTRAB, NULL EXTRAAUTORIZADO, NULL COMPENSADO, NULL DESCANSO, NULL FERIADO,
							'RM' SISTEMA
						FROM 
							ABATFUN (NOLOCK) A
							INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
							INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
							INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
							INNER JOIN PAR              M ON M.COLIGADA = A.CODCOLIGADA
						
						WHERE
								A.DATA BETWEEN M.INICIO AND M.FIM
							AND A.STATUS = 'D'
							 ".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)."
							 ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							 AND (SELECT registro_manual FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
									
							 UNION ALL
							 /***************************************************************************************************************/
							 /* Trabalho em DSR ou Folga [COMPENSADO]        JORNADA SEMANAL                                                                  */
							 /***************************************************************************************************************/
								 SELECT
									   A.CODCOLIGADA
									 , A.CHAPA
									 , A.NOME
									 , A.CODFUNCAO
									 , C.NOME NOMEFUNCAO
									 , A.CODSECAO
									 , D.DESCRICAO NOMESECAO
									 , B.DATA
									 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
									 , 'trabalho_dsr_folga_descanso' OCORRENCIA
									 , NULL COMPLEMENTO
									 , (CASE WHEN (COMPENSADO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB    ------> COLOCAR COMPENSADO?
											 --WHEN FERIADO > 0 AND HTRAB > 0
												 --AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
											--							 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
											--					  THEN HTRAB
											 ELSE 0 END) VALOR
								 
									 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO,
									'RM' SISTEMA
									 
								 FROM 
									 PFUNC (NOLOCK) A
										 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
										 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
										 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
										 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
								 
								 WHERE
										 B.DATA BETWEEN M.INICIO AND M.FIM
										{$in_secao}
										{$where_filial}
									 AND (CASE WHEN (COMPENSADO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB   ----- 
										--	 WHEN FERIADO > 0 AND HTRAB > 0
											--	 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
												--						 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
													--			  THEN HTRAB
											 ELSE 0 END) > 0
								 AND (SELECT trabalho_dsr FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
									 
							 
							 
							 UNION ALL
							 
							 /***************************************************************************************************************/
							 /* Trabalho em DSR ou Folga [DESCANSO]                                                                            */
							 /***************************************************************************************************************/
							SELECT 
								* 
							FROM ( 
								 SELECT
									   A.CODCOLIGADA
									 , A.CHAPA
									 , A.NOME
									 , A.CODFUNCAO
									 , C.NOME NOMEFUNCAO
									 , A.CODSECAO
									 , D.DESCRICAO NOMESECAO
									 , B.DATA
									 , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
									 , 'trabalho_dsr_folga' OCORRENCIA 
									 , NULL COMPLEMENTO
									 , (CASE WHEN (DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
											 WHEN FERIADO > 0 AND HTRAB > 0
												 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																		 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
																  THEN HTRAB
											 ELSE 0 END) VALOR
								 
									 , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO,
									'RM' SISTEMA
									 
								 FROM 
									 PFUNC (NOLOCK) A
										 LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
										 INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
										 INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
										 INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
								 
								 WHERE
										 B.DATA BETWEEN M.INICIO AND M.FIM
									{$in_secao}
									{$where_filial}
									 AND (CASE WHEN (DESCANSO > 0) AND FERIADO = 0 AND HTRAB > 0 THEN HTRAB
											 WHEN FERIADO > 0 AND HTRAB > 0
												 AND (SELECT O.CONSFERIADO FROM PFHSTHOR (NOLOCK) N, AHORARIO (NOLOCK) O WHERE N.CODCOLIGADA = O.CODCOLIGADA AND N.CODHORARIO = O.CODIGO AND A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
																		 (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) = 0
																  THEN HTRAB
											 ELSE 0 END) > 0
									AND (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1               
							)XX
						    WHERE XX.HORARIO COLLATE Latin1_General_CI_AS  IN(SELECT CODIGO FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_horario WHERE coligada =  (SELECT COLIGADA FROM PAR) AND ISNULL(EXTRA_FERIADO,0) = 1 )
								
					UNION ALL
					/***************************************************************************************************************/
					/* Trabalho em Férias ou Afastamentos                                                                             */
					/***************************************************************************************************************/

						SELECT
							  CODCOLIGADA
							, CHAPA
							, NOME
							, CODFUNCAO
							, NOMEFUNCAO
							, CODSECAO
							, NOMESECAO
							, DATA
							, HORARIO
							, OCORRENCIA
							, CASE 
								WHEN FERIAS IS NOT NULL THEN 'Férias'
								WHEN AFAST IS NOT NULL THEN 'Afastamento/Atestado'
								ELSE NULL
							  END COMPLEMENTO
							, CASE 
								WHEN FERIAS IS NOT NULL THEN 1
								WHEN AFAST IS NOT NULL THEN 2
								ELSE NULL
							  END VALOR
							, NULL BASE
							, NULL HTRAB
							, NULL EXTRAAUTORIZADO
							, NULL COMPENSADO
							, NULL DESCANSO
							, NULL FERIADO,
							'RM' SISTEMA
							
						FROM (
						
							SELECT 
								AX.* 
							FROM(
							
								SELECT
									  A.CODCOLIGADA
									, A.CHAPA
									, A.NOME
									, A.CODFUNCAO
									, C.NOME NOMEFUNCAO
									, A.CODSECAO
									, D.DESCRICAO NOMESECAO
									, B.DATA
									, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
										(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
									, 'trabalho_ferias_afastamento' OCORRENCIA
									, (SELECT 'Férias' FROM PFUFERIASPER (NOLOCK) F  WHERE F.CODCOLIGADA = A.CODCOLIGADA AND F.CHAPA = A.CHAPA AND B.DATA BETWEEN F.DATAINICIO AND F.DATAFIM) FERIAS
									, (SELECT 'Afastamento' FROM PFHSTAFT (NOLOCK) H WHERE H.CODCOLIGADA = A.CODCOLIGADA AND H.CHAPA = A.CHAPA AND B.DATA BETWEEN H.DTINICIO AND  ISNULL(H.DTFINAL, '2050-12-01') ) AFAST
									, (SELECT COUNT(*) FROM ABATFUN (NOLOCK) G WHERE G.CODCOLIGADA = A.CODCOLIGADA AND G.CHAPA = A.CHAPA AND ISNULL(G.DATAREFERENCIA,G.DATA) = B.DATA) BAT
									, B.ABONO
								
								FROM 
									PFUNC (NOLOCK) A
									LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
									INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
									INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
									INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
									
								
								WHERE
									B.DATA BETWEEN M.INICIO AND M.FIM
									{$in_secao}
									 {$where_filial}
								AND (SELECT trabalho_AfastFerias FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
													
							
							)AX
							WHERE	
								(AX.FERIAS IS NOT NULL OR AX.AFAST IS NOT NULL) AND AX.BAT > 0 AND ( ABONO = 0 OR ABONO >= 240) /* A pedido ignorado se o abono foi menor que 4horas*/
												
						)BX
								   


					UNION ALL
					/***************************************************************************************************************/
					/* InterJornada                                                                                                */
					/***************************************************************************************************************/

					SELECT 
						  CODCOLIGADA
						, CHAPA
						, NOME
						, CODFUNCAO
						, NOMEFUNCAO
						, CODSECAO
						, NOMESECAO
						, DATAREFERENCIA
						, HORARIO
						, OCORRENCIA
						, AVISO + ' [' +
							(CASE WHEN DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) IS NULL or DATEDIFF(minute ,BAT_anterior, BAT_atual) > 660 THEN '[Bat.Inválida]'
								  ELSE DBO.MINTOTIME(DATEDIFF(minute ,BAT_anterior, BAT_atual)) END) 
							+ ']' COMPLEMENTO
						, 1 VALOR

						, NULL BASE
						, NULL HTRAB
						, NULL EXTRAAUTORIZADO
						, NULL COMPENSADO
						, NULL DESCANSO
						, NULL FERIADO,
						'RM' SISTEMA

					FROM(

						SELECT
							  A.CODCOLIGADA
							, A.CHAPA
							, B.NOME
							, B.CODFUNCAO
							, C.NOME NOMEFUNCAO
							, B.CODSECAO
							, D.DESCRICAO NOMESECAO
							, A.DATAREFERENCIA
							, (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATAREFERENCIA)) HORARIO
							, 'interjornada' OCORRENCIA
							, 1 VALOR
							, AAVISO.DESCRICAO AVISO 
							,(SELECT MAX(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA-1 = M.DATAREFERENCIA AND NATUREZA = 1) BAT_anterior
							,(SELECT MIN(CONVERT(DATETIME, CONVERT(CHAR, ISNULL(CASE WHEN DATA > DATAREFERENCIA THEN DATAREFERENCIA +1 ELSE DATAREFERENCIA END,DATA),23) + ' ' + CONVERT(CHAR, dbo.MINTOTIME(BATIDA),8))) FROM ABATFUN M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND A.DATAREFERENCIA = M.DATAREFERENCIA AND NATUREZA = 0) BAT_atual
						
						FROM 
							AAVISOCALCULADO A (NOLOCK)
								INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
								INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
								INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
							, 
							AAVISO,
							PAR
							
						WHERE 
								
								A.CODCOLIGADA = PAR.COLIGADA
								AND A.CODAVISO = AAVISO.CODAVISO 
								AND A.CODCOLIGADA = PAR.COLIGADA
								AND A.DATAREFERENCIA BETWEEN PAR.INICIO AND PAR.FIM
								AND A.CODAVISO = '1'
								".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)."
								".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
								AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								

								)X
						UNION ALL
						/***************************************************************************************************************/
						/* IntraJornada    [tipo de retorno COMPLEMENTO]                                                               */
						/***************************************************************************************************************/
						  
						  SELECT 
							  CODCOLIGADA
							, CHAPA
							, NOME
							, CODFUNCAO
							, NOMEFUNCAO
							, CODSECAO
							, NOMESECAO
							, DATA
							, HORARIO
							, OCORRENCIA
							, ' Tempo Mínimo de refeição (IntraJornada) ' + dbo.mintotime(REF_OBRIGATORIO) + ' ' + 'não realizado [' + dbo.mintotime(REF_REALIZADO) + ']' COMPLEMENTO
							, VALOR
							
							, BASE
							  , HTRAB
							  , EXTRAAUTORIZADO
							  , COMPENSADO
							  , DESCANSO
							  , FERIADO,
							'RM' SISTEMA
						  FROM (
						  
							SELECT 
							 		ISNULL((SELECT (FIM-INICIO) TEMPO
									FROM ABATHOR M
									WHERE 
									M.CODCOLIGADA = XX.CODCOLIGADA
									AND M.CODHORARIO  = XX.HORARIO
									AND TIPO = 4 
									AND INDICE = 1
									),0) REF_OBRIGATORIO
							  , *
								
							FROM (
						  
							  SELECT
									A.CODCOLIGADA
								  , A.CHAPA
								  , A.NOME
								  , A.CODFUNCAO
								  , C.NOME NOMEFUNCAO
								  , A.CODSECAO
								  , D.DESCRICAO NOMESECAO
								  , B.DATA
								  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= B.DATA)) HORARIO
								  , 'interjornada' OCORRENCIA
								  , NULL VALOR
								  
								  ,ISNULL((SELECT SUM(DATEDIFF(MINUTE,INICIO,FIM)) FROM AOCORRENCIACALCULADA P
								  WHERE 
									A.CODCOLIGADA = P.CODCOLIGADA 
								  AND A.CHAPA = P.CHAPA 
								  AND B.DATA = P.DATAREFERENCIA
								  AND TIPOOCORRENCIA IN ('AREF')),0) REF_REALIZADO
							  
								  , BASE, HTRAB, EXTRAAUTORIZADO, COMPENSADO, DESCANSO, FERIADO
							  FROM 
								  PFUNC (NOLOCK) A
									  LEFT  JOIN AAFHTFUN (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
									  INNER JOIN PFUNCAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODFUNCAO
									  INNER JOIN PSECAO   (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = A.CODSECAO
									  INNER JOIN PAR               M ON A.CODCOLIGADA = M.COLIGADA
							  
							  WHERE
									B.DATA BETWEEN M.INICIO AND M.FIM
								  AND B.HTRAB > 0
								  AND (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								  {$in_secao} 
								   {$where_filial}
						  
							)XX
							   WHERE XX.HORARIO IN(
													  SELECT 
														DISTINCT CODHORARIO
													  FROM ABATHOR, PAR
													  WHERE ABATHOR.CODCOLIGADA = PAR.COLIGADA
													  AND TIPO = 5
												 )
						  )XY
						  WHERE REF_OBRIGATORIO-REF_REALIZADO > 10


						UNION ALL
						/***************************************************************************************************************/
						/* Registros britânicos (Apontamentos Manuais)    [tipo de retorno COMPLEMENTO]                                */
						/***************************************************************************************************************/ 
						 
						  SELECT
								A.CODCOLIGADA
							  , A.CHAPA
							  , B.NOME
							  , B.CODFUNCAO
							  , D.NOME NOMEFUNCAO
							  , B.CODSECAO
							  , C.DESCRICAO NOMESECAO
							  , A.DATA
							  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
							  , 'registro_britanico' OCORRENCIA
							  , CONCAT(dbo.mintotime(MIN(B1.BATIDA)), ' ', dbo.mintotime(MAX(B1.BATIDA))) COMPLEMENTO
							  , 1 VALOR
								
							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
								'RM' SISTEMA
							  
							  
						  FROM AAFHTFUN (NOLOCK) A
							  INNER JOIN PFUNC   (NOLOCK) B  ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
							  INNER JOIN PSECAO  (NOLOCK) C  ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODSECAO
							  INNER JOIN PFUNCAO (NOLOCK) D  ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODFUNCAO
							  INNER JOIN ABATFUN (NOLOCK) B1 ON B1.CODCOLIGADA = A.CODCOLIGADA AND B1.CHAPA = A.CHAPA AND ISNULL(B1.DATAREFERENCIA, B1.DATA) = A.DATA AND B1.STATUS NOT IN ('T')
							  INNER JOIN AJORHOR (NOLOCK) J1 ON J1.CODCOLIGADA = A.CODCOLIGADA AND J1.CODHORARIO = (SELECT CODHORARIO FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA = 
										  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) M WHERE M.CODCOLIGADA = A.CODCOLIGADA AND M.CHAPA = A.CHAPA AND DTMUDANCA <= A.DATA))
							  LEFT  JOIN PAR P ON P.COLIGADA = A.CODCOLIGADA
						  
							  
						  WHERE
								  A.CODCOLIGADA = P.COLIGADA
							  AND A.DATA BETWEEN P.INICIO AND P.FIM
							  ".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)."
							   ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							   AND (SELECT registro_bri FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
							  
						  GROUP BY
							  A.CODCOLIGADA,
							  A.CHAPA,
							  A.DATA,
							  B.NOME,
							  B.CODSECAO,
							  C.DESCRICAO,
							  B.CODFUNCAO,
							  D.NOME
						  
						  HAVING
								  MIN(B1.BATIDA) = MIN(J1.BATINICIO)
							  AND MAX(B1.BATIDA) = MAX(J1.BATFIM)
										

						UNION ALL
						/***************************************************************************************************************/
						/* TROCA DE ESCALA MENOS DE 6 MESES    [tipo de retorno COMPLEMENTO]                                           */
						/***************************************************************************************************************/ 
						 
						  SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , ULT_MUD_DATA DATA
							  , NULL HORARIO
							  , 'troca_menor_6_meses' OCORRENCIA
							  , CONVERT(VARCHAR(2),DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA)) +
								+ (CASE WHEN DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) > 1 THEN ' (meses) --->' ELSE ' (mês) --->' END) 
								+ CONVERT(VARCHAR(10),ULT_MUD_DATA,103) + ' ['+ULT_MUD_CODHOR+']' + ' - ' + CONVERT(VARCHAR(10),PEN_MUD_DATA,103) + ' [' + PEN_MUD_CODHOR + ']'  COMPLEMENTO
							  , DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) VALOR
							  
							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
						  FROM (
						  
							SELECT
								  A.CODCOLIGADA
								, A.CHAPA
								, A.NOME
								, A.CODFUNCAO
								, B.NOME NOMEFUNCAO
								, A.CODSECAO
								, C.DESCRICAO NOMESECAO
								, (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ULT_MUD_DATA
								
								, (SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA  AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
									(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) ) ULT_MUD_CODHOR
							  
						  		,(SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
						  			AND DTMUDANCA >
										  			(SELECT 
										  				CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
										  					  		ELSE MAX(DTMUDANCA) END
										  			 FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
										  				AND DTMUDANCA <=
															  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																  	AND DTMUDANCA <> 
																  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																   	AND CODHORARIO <> 
																	  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																	  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
										  				AND CODHORARIO <> 
										  					  (SELECT 
										  					  		CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
										  					  		ELSE CODHORARIO END
										  					  	FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
											  					  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																	  	AND DTMUDANCA <> 
																	  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																	   	AND CODHORARIO <> 
																		  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																		  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
															 )
										  			)	
						  	   	 ) PEN_MUD_DATA
			  	
			  					,(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
				   				    (SELECT MIN(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA 
							  			AND DTMUDANCA >
											  			(SELECT 
											  				CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN MAX(DTMUDANCA)-1
											  					  		ELSE MAX(DTMUDANCA) END
											  			 FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
											  				AND DTMUDANCA <=
																  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																	  	AND DTMUDANCA <> 
																	  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																	   	AND CODHORARIO <> 
																		  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																		  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  	
											  				AND CODHORARIO <> 
											  					  (SELECT 
											  					  		CASE WHEN (SELECT COUNT(DISTINCT CODHORARIO) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) < 3 THEN 'XYZ'
											  					  		ELSE CODHORARIO END
											  					  	FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA = 
												  					  (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM 
																		  	AND DTMUDANCA <> 
																		  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)
																		   	AND CODHORARIO <> 
																			  	(SELECT CODHORARIO FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM AND DTMUDANCA = 
																			  		(SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM)) )		  		
																 )
											  			)	
							  	   	)
						  	   	) PEN_MUD_CODHOR

							FROM
								PFUNC A
								INNER JOIN PFUNCAO (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CODIGO = A.CODFUNCAO
								INNER JOIN PSECAO  (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = A.CODSECAO
								INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
						  
							WHERE
									A.CODSITUACAO NOT IN ('D')
								AND (SELECT MAX(DTMUDANCA) FROM PFHSTHOR WHERE CODCOLIGADA = A.CODCOLIGADA AND CHAPA = A.CHAPA AND DTMUDANCA <= P.FIM) BETWEEN P.INICIO AND P.FIM
								AND (SELECT troca_menor6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								{$in_secao} 
								{$where_filial}

							  
						  )X
							 WHERE 
								DATEDIFF(MONTH, PEN_MUD_DATA, ULT_MUD_DATA) < 6 
						  
						  
						  
						  
						  
						UNION ALL
						/***************************************************************************************************************/
						/* TROCA DE ESCALA MENOR DE 10 DIAS    [tipo de retorno VALOR]                                                 */
						/***************************************************************************************************************/					  
						  SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , DTMUDANCA DATA
							  , NULL HORARIO
							  , 'troca_menor_10_dias' OCORRENCIA
							  , NULL COMPLEMENTO
							  , (DATEDIFF(DAY, DATAALTERACAO, DTMUDANCA)) VALOR


							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
						
						  FROM(
							  SELECT 
							  	  A.CODCOLIGADA 
							  	, A.CHAPA
							  	, B.NOME
							  	, B.CODFUNCAO
							  	, C.NOME NOMEFUNCAO
							  	, B.CODSECAO
							  	, D.DESCRICAO NOMESECAO
							  	, A.DTMUDANCA
							  	, A.CODHORARIO
							  	, A.DATAALTERACAO 
							  	, (SELECT M.DTMUDANCA FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA = 
							  			(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA < A.DTMUDANCA) ) PEN_DTMUDANCA
							    , ISNULL((SELECT M.CODHORARIO FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA = 
							  				(SELECT MAX(DTMUDANCA) FROM PFHSTHOR M WHERE A.CODCOLIGADA = M.CODCOLIGADA AND A.CHAPA = M.CHAPA AND M.DTMUDANCA < A.DTMUDANCA) ),A.CODHORARIO) PEN_CODHORARIO
							  	
	
	
							  FROM PFHSTHOR A
							  	INNER JOIN PFUNC   (NOLOCK) B ON A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA
							  	LEFT  JOIN PFUNCAO (NOLOCK) C ON B.CODCOLIGADA = C.CODCOLIGADA AND B.CODFUNCAO = C.CODIGO
							  	LEFT  JOIN PSECAO  (NOLOCK) D ON B.CODCOLIGADA = D.CODCOLIGADA AND B.CODSECAO = D.CODIGO
							  	INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
							  WHERE 
							  	B.CODSITUACAO NOT IN ('D')
							  AND A.DTMUDANCA BETWEEN P.INICIO AND P.FIM
								AND (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1
								".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)."
							   ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
						  )X 
							WHERE (DATEDIFF(DAY, DATAALTERACAO, DTMUDANCA)) < 3
							AND CODHORARIO <> PEN_CODHORARIO

						  
						  
						UNION ALL
						/***************************************************************************************************************/
						/* TROCA DE ESCALA SEM ANEXO DO TERMO ADITIVO    [tipo de retorno VALOR]                                       */
						/***************************************************************************************************************/
									
						  SELECT 
								B.CODCOLIGADA
							  , B.CHAPA
							  , B.NOME
							  , B.CODFUNCAO
							  , C.NOME NOMEFUNCAO
							  , B.CODSECAO
							  , D.DESCRICAO NOMESECAO
							  , A.datamudanca DATA
							  , NULL HORARIO
							  , 'pendente_termo_aditivo' OCORRENCIA
							  , NULL COMPLEMENTO
							  , A.id VALOR
							  
							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
						  FROM 
							  ".DBPORTAL_BANCO."..zcrmportal_escala A
							  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
							  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
							  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
							  INNER JOIN PAR              P ON P.COLIGADA = A.coligada
						  WHERE
								  CAST(A.documento AS VARCHAR(10)) IS NULL
							  AND A.datamudanca BETWEEN P.INICIO AND P.FIM
							  AND (SELECT req_troca FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
							   ".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)." 
							 
							 ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							 
							  
						UNION ALL
						/***************************************************************************************************************/
						/* SOBREAVISO     [tipo de retorno VALOR]                                                                      */
						/***************************************************************************************************************/
						  
						  SELECT 
								B.CODCOLIGADA
							  , B.CHAPA
							  , B.NOME
							  , B.CODFUNCAO
							  , C.NOME NOMEFUNCAO
							  , B.CODSECAO
							  , D.DESCRICAO NOMESECAO
							  , CONVERT(DATE, A.dtcad, 3) DATA
							  , NULL HORARIO
							  , 'sobreaviso' OCORRENCIA
							, NULL COMPLEMENTO
							  , SUM(A.HORAS) VALOR

							  , NULL BASE
							  , NULL HTRAB
							  , NULL EXTRAAUTORIZADO
							  , NULL COMPENSADO
							  , NULL DESCANSO
							  , NULL FERIADO,
							'RM' SISTEMA
							  
							  --,DATEADD(DAY,6,P.INICIO) INICIO
							  --,DATEADD(DAY,6,P.FIM) FIM
							  
						  FROM 
							  ".DBPORTAL_BANCO."..zcrmportal_substituicao_sobreaviso (NOLOCK) A
								  INNER JOIN PFUNC   (NOLOCK) B ON B.CHAPA = A.chapa COLLATE Latin1_General_CI_AS AND B.CODCOLIGADA = A.coligada
								  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = B.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
								  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = B.CODCOLIGADA AND D.CODIGO = B.CODSECAO
								  INNER JOIN PAR              P ON A.COLIGADA = P.COLIGADA
							  
						  WHERE 
								A.dtcad BETWEEN DATEADD(DAY,6,P.INICIO) AND DATEADD(DAY,6,P.FIM)
							  AND A.situacao = 2
							  AND (SELECT sobreaviso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
							   ".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)."
							    ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
							  
						  GROUP BY 
							  B.CODCOLIGADA,
							  B.CHAPA,
							  B.NOME,
							  B.CODFUNCAO,
							  C.NOME,
							  B.CODSECAO,
							  D.DESCRICAO,
							  CONVERT(DATE, A.dtcad, 3)
							
						  HAVING 
							  SUM(A.horas) > 5760
						  
						  
										
						UNION ALL
						/***************************************************************************************************************/
						/* Excesso de Abono Gestor (Superior a 5 dias consecutivos)    [tipo de retorno COMPLEMENTO]                   */
						/***************************************************************************************************************/ 

						  SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , DATA
							  , NULL HORARIO
							  , 'excesso_abono_gestor' OCORRENCIA
							  , '5 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)   COMPLEMENTO
							  , (UM + DOIS + TRES + QUATRO + CINCO) VALOR
							  
							  , BASE
							  , HTRAB
							  , EXTRAAUTORIZADO
							  , COMPENSADO
							  , DESCANSO
							  , FERIADO,
							'RM' SISTEMA
						  FROM(
						  
							  SELECT
									A.CODCOLIGADA
								  , A.CHAPA
								  , B.NOME
								  , B.CODFUNCAO
								  , C.NOME NOMEFUNCAO
								  , B.CODSECAO
								  , D.DESCRICAO NOMESECAO
								  , A.DATA
								  , P.ABONO
								  , A.BASE
								, A.HTRAB
								, A.EXTRAAUTORIZADO
								, A.COMPENSADO
								, A.DESCANSO
								, A.FERIADO
						  
								  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.CODABONO = P.ABONO AND A.BASE > 0)>0 THEN 1 ELSE 0 END)UM
								  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 1) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) DOIS
								  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 2) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) TRES
																											
							  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 3) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) QUATRO                                                                            
							  ,(CASE WHEN (SELECT COUNT(*) FROM AABONFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND B.CODABONO = P.ABONO AND A.BASE > 0 
																											  AND 
																											(SELECT DATA FROM (
																											  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
																											  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
																											  )X WHERE seq = 4) 
																											= B.DATA )>0 THEN 1 ELSE 0 END) CINCO                                                                           
							  , (SELECT DATA 
								FROM (
								  SELECT TOP 5 AA.DATA ,ROW_NUMBER() OVER(ORDER BY AA.DATA DESC) SEQ
								  FROM AAFHTFUN AA WHERE A.CODCOLIGADA = AA.CODCOLIGADA AND A.CHAPA = AA.CHAPA AND AA.DATA < A.DATA AND AA.BASE > 0 
								  )X WHERE seq = 4) DATAFINAL
								  
							  FROM AAFHTFUN (NOLOCK) A
								  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
								  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
								  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
								  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
								  
							  WHERE
								  A.DATA BETWEEN P.INICIO AND P.FIM
								  AND (SELECT excesso_gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								  ".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)."
								  ".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
								  
						  
						  )X
						   WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1


						  
						UNION ALL
						/***************************************************************************************************************/
						/* Trabalho superior à 6 (seis) dias consecutivos sem folga  [tipo de retorno COMPLEMENTO]                     */
						/***************************************************************************************************************/

						  SELECT
								CODCOLIGADA
							  , CHAPA
							  , NOME
							  , CODFUNCAO
							  , NOMEFUNCAO
							  , CODSECAO
							  , NOMESECAO
							  , DATA
							  , HORARIO
							  , 'trabalho_6dias' OCORRENCIA
							  , '7 dias de ' + CONVERT(VARCHAR(10),DATAFINAL,103) + ' à ' + CONVERT(VARCHAR(10),DATA,103)  COMPLEMENTO
							  , (UM + DOIS + TRES + QUATRO + CINCO + SEIS + SETE) VALOR
							
								, BASE
							  , HTRAB
							  , EXTRAAUTORIZADO
							  , COMPENSADO
							  , DESCANSO
							  , FERIADO,
							'RM' SISTEMA
						  FROM(
						  
							  SELECT
									A.CODCOLIGADA
								  , A.CHAPA
								  , B.NOME
								  , B.CODFUNCAO
								  , C.NOME NOMEFUNCAO
								  , B.CODSECAO
								  , D.DESCRICAO NOMESECAO
								  , A.DATA
								  , (SELECT N.CODHORARIO FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA = 
								(SELECT MAX(DTMUDANCA) FROM PFHSTHOR (NOLOCK) N WHERE A.CODCOLIGADA = N.CODCOLIGADA AND A.CHAPA = N.CHAPA AND N.DTMUDANCA <= A.DATA)) HORARIO
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA   = B.DATA AND B.HTRAB >0)UM
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-1 = B.DATA AND B.HTRAB >0)DOIS
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-2 = B.DATA AND B.HTRAB >0)TRES
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-3 = B.DATA AND B.HTRAB >0)QUATRO
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-4 = B.DATA AND B.HTRAB >0)CINCO
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-5 = B.DATA AND B.HTRAB >0)SEIS
								  , (SELECT COUNT(B.CHAPA) FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)SETE
								  
								  ,(SELECT B.DATA FROM AAFHTFUN (NOLOCK) B WHERE A.CODCOLIGADA = B.CODCOLIGADA AND A.CHAPA = B.CHAPA AND A.DATA-6 = B.DATA AND B.HTRAB >0)DATAFINAL
								  
								  , A.BASE
								, A.HTRAB
								, A.EXTRAAUTORIZADO
								, A.COMPENSADO
								, A.DESCANSO
								, A.FERIADO
								  
							  FROM AAFHTFUN (NOLOCK) A
								  INNER JOIN PAR              P ON P.COLIGADA = A.CODCOLIGADA
								  INNER JOIN PFUNC   (NOLOCK) B ON B.CODCOLIGADA = A.CODCOLIGADA AND B.CHAPA = A.CHAPA
								  INNER JOIN PFUNCAO (NOLOCK) C ON C.CODCOLIGADA = A.CODCOLIGADA AND C.CODIGO = B.CODFUNCAO
								  INNER JOIN PSECAO  (NOLOCK) D ON D.CODCOLIGADA = A.CODCOLIGADA AND D.CODIGO = B.CODSECAO
								  
							  WHERE
								  A.DATA BETWEEN P.INICIO AND P.FIM
								  AND (SELECT trabalho_sup6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo where id = 1) = 1 
								   ".str_replace('A.CODSECAO', 'B.CODSECAO', $in_secao)."
									".str_replace('A.CODFILIAL', 'B.CODFILIAL', $where_filial)."
								  
								  
						  )X
							WHERE UM = 1 AND DOIS = 1 AND TRES = 1 AND QUATRO = 1 AND CINCO = 1 AND SEIS = 1 AND SETE = 1


/*************************************************************************************
							 * ocorrencias de outros sistema
							 ************************************************************************************/		
								UNION ALL

								SELECT 
									CAST(a.codcoligada AS INT) CODCOLIGADA,
									a.chapa COLLATE Latin1_General_CI_AS CHAPA,
									b.NOME,
									b.CODFUNCAO,
									c.NOME NOMEFUNCAO,
									b.CODSECAO,
									d.DESCRICAO NOMESECAO,
									a.data DATA,
									NULL HORARIO,
									a.ocorrencia OCORRENCIA,
									a.resultado COLLATE Latin1_General_CI_AS COMPLEMENTO,
									NULL VALOR
									, NULL BASE
									, NULL HTRAB
									, NULL EXTRAAUTORIZADO
									, NULL COMPENSADO
									, NULL DESCANSO
									, NULL FERIADO
									, a.sistema SISTEMA
								FROM 
									".DBPORTAL_BANCO."..zcrmportal_ocorrencia a (NOLOCK)
									INNER JOIN PFUNC b (NOLOCK) ON b.CHAPA COLLATE Latin1_General_CI_AS = a.chapa AND b.CODCOLIGADA = a.codcoligada COLLATE Latin1_General_CI_AS
									INNER JOIN PFUNCAO c (NOLOCK) ON c.CODIGO = b.CODFUNCAO AND c.CODCOLIGADA = b.CODCOLIGADA
									INNER JOIN PSECAO d (NOLOCK) ON d.CODIGO = b.CODSECAO AND d.CODCOLIGADA = b.CODCOLIGADA
									INNER JOIN PAR p ON p.COLIGADA = CAST(a.codcoligada AS INT) 
								WHERE 
										a.codcoligada = {$this->coligada} 
									AND a.sistema NOT IN ('RM')
									AND a.data BETWEEN p.INICIO AND p.FIM
									".str_replace('A.CODSECAO', 'b.CODSECAO', $in_secao)."
									".(($ja_justificados != NULL) ? ' AND a.codmotivo IS NOT NULL ' : '')."
									AND 1 = 
										CASE
											WHEN a.ocorrencia = 'excesso_abono_gestor' THEN (SELECT excesso_gestor FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'extra_permitido' THEN (SELECT extra_acima FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'extra' THEN 1
											WHEN a.ocorrencia = 'jornada' THEN 1
											WHEN a.ocorrencia = 'trabalho_dsr_folga' THEN 1
											WHEN a.ocorrencia = 'trabalho_dsr_folga_descanso' THEN (SELECT trabalho_dsr_descanso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'trabalho_ferias_afastamento' THEN 1
											WHEN a.ocorrencia = 'registro_manual' THEN (SELECT registro_manual FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'trabalho_6dias' THEN (SELECT trabalho_sup6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'interjornada' THEN (SELECT interjornada FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'registro_britanico' THEN (SELECT registro_bri FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'sobreaviso' THEN (SELECT sobreaviso FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'troca_menor_6_meses' THEN (SELECT troca_menor6 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'troca_menor_10_dias' THEN (SELECT troca_menor10 FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											WHEN a.ocorrencia = 'pendente_termo_aditivo' THEN (SELECT req_troca FROM ".DBPORTAL_BANCO."..zcrmportal_ocorrencia_tipo WHERE coligada = {$this->coligada} )
											ELSE 1
										END

					)X

					LEFT JOIN ".DBPORTAL_BANCO."..zcrmportal_ocorrencia (NOLOCK) Z ON Z.chapa = X.CHAPA COLLATE Latin1_General_CI_AS AND Z.codcoligada = X.CODCOLIGADA AND Z.data = X.DATA AND Z.ocorrencia = X.OCORRENCIA

					--WHERE Z.codmotivo IS NULL 

					[WHERE]
					 

					ORDER BY
					X.NOME,
					X.DATA,
					X.OCORRENCIA
        ";
		

          // MONTA SQL PARA VISUALIZAÇÃO DAS OCORRÊNCIAS
        $SELECT = "
            SELECT
                X.*,
                Z.codmotivo,
                Z.descricao_motivo,
                Z.ocorrencia log_ocorrencia,
                Z.observacao,
                Z.gestor
        ";
        $WHERE = " {$where_ja_justificados} ";

        $array_de = array('[SELECT]', '[WHERE]');
        $array_para = array($SELECT, $WHERE);
        $query = str_replace($array_de, $array_para, $query);

if($_SESSION['log_id'] == 1){
echo '<pre>';
echo $query;
exit();
}
		
        $result = $this->dbrm->query($query);
		if(!$result) return false;
        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

    }

	public function ConfiguracaoHorario($request)
	{

		if($request['field'] != 'escala_especial' && $request['field'] != 'tipo_horario' && $request['field'] != 'intervalo_obrigatorio' && $request['field'] != 'intervalo_fracionado') $request['marcado'] = h2m($request['marcado']);
		if(strlen(trim($request['marcado'])) <= 0) $request['marcado'] = null;

		$result = 	$this->dbportal
					->table('zcrmportal_ocorrencia_horario')
					->selectCount('codigo')
					->where('coligada', $this->coligada)
					->where('codigo', $request['codhorario']);

		if($result->get()->getResult()[0]->codigo > 0){

			$this->dbportal
			->table('zcrmportal_ocorrencia_horario')
			->set([
				$request['field'] 	=> $request['marcado'],
				'usualt' 			=> $this->log_id,
				'dtalt' 			=> $this->now
			])
			->where('codigo', $request['codhorario'])
			->where('coligada', $this->coligada)
			->update();

		}else{

			$data = [
				'codigo' 			=> $request['codhorario'],
				'coligada' 			=> $this->coligada,
				$request['field'] 	=> $request['marcado'],
				'usucad' 			=> $this->log_id,
				'dtcad' 			=> $this->now
			];

			$this->dbportal
			->table('zcrmportal_ocorrencia_horario')
			->set($data)
			->insert();

		}

		return 	($this->dbportal->affectedRows() > 0) 
				? responseJson('success', 'Configuração realizada com sucesso')
				: responseJson('error', 'Não foi configurar este horário');

	}

	public function DadosAnexo($id_anexo)
	{

		$result = 	$this->dbportal
					->table('zcrmportal_ocorrencia_anexo')
					->select('*')
					->where('coligada', $this->coligada)
					->where('id', $id_anexo);

		return ($result)
				? $result->get()->getResult()
				: false;

	}

	public function ExcluirAnexo($request)
	{

		// grava log de alteração
		$this->dbportal->query("
			INSERT INTO zcrmportal_ocorrencia_log_alteracao 
			SELECT *, '{$this->log_id}', '{$this->now}' FROM zcrmportal_ocorrencia WHERE codcoligada = '{$this->coligada}' AND chapa = '{$request['chapa']}' AND data = '{$request['data']}' 
		");

		$this->dbportal
			->table('zcrmportal_ocorrencia')
			->set([
				'id_anexo' => NULL
			])
			->where('chapa', $request['chapa'])
			->where('data', $request['data'])
			->where('codcoligada', $this->coligada)
			->update();
		
			$result = $this->dbportal->affectedRows();

			$this->dbportal->query(" DELETE A FROM zcrmportal_ocorrencia_anexo A WHERE NOT EXISTS(SELECT chapa FROM zcrmportal_ocorrencia WHERE id_anexo = A.id) ");

		return 	($result > 0) 
			? responseJson('success', 'Anexo excluído com sucesso')
			: responseJson('error', 'Não foi possivel excluir este anexo');
	}

	public function PeriodoAtivo()
	{
		$result = $this->dbrm
					->table('APERIODO')
					->select('FIMMENSAL')
					->where('CODCOLIGADA', $this->coligada)
					->where('ATIVO', 1);

		return ($result)
			? $result->get()->getResult()
			: false;
	}

	public function DadosHorario($horario){

		$query = "SELECT * FROM AHORARIO WHERE CODCOLIGADA = '{$this->coligada}' AND CODIGO = '{$horario}'";

		$result = $this->dbrm->query($query);

        return ($result->getNumRows() > 0) 
                ? $result->getResultArray() 
                : false;

	}


	public function ListaDadosHorario($request)
	{

		self::geraMelhoria3();

		$result = $this->dbrm
			->table('Z_OUTSERV_MELHORIAS3')
			->select('*')
			->where('CODCOLIGADA', $this->coligada)
			->where('CODHORARIO', $request['codhorario'])
			->orderBy('CODINDICE ASC');

			

		if($result){
			$dados = $result->get()->getResult();
			$data = [];
			foreach($dados as $key => $Dados){

				$data[$key] = $Dados;

				$data[$key]->ENTRADA1 					= (strlen(trim($data[$key]->ENTRADA1)) > 0) ? m2h($Dados->ENTRADA1) : '';
				$data[$key]->SAIDA1 					= (strlen(trim($data[$key]->SAIDA1)) > 0) ? m2h($Dados->SAIDA1) : '';
				$data[$key]->ENTRADA2 					= (strlen(trim($data[$key]->ENTRADA2)) > 0) ? m2h($Dados->ENTRADA2) : '';
				$data[$key]->SAIDA2 					= (strlen(trim($data[$key]->SAIDA2)) > 0) ? m2h($Dados->SAIDA2) : '';
				$data[$key]->TOLERANCIA_INICIO	= (strlen(trim($data[$key]->TOLERANCIA_INICIO)) > 0) ? m2h($Dados->TOLERANCIA_INICIO,4) : '';
				$data[$key]->TOLERANCIA_FIM	= (strlen(trim($data[$key]->TOLERANCIA_FIM)) > 0) ? m2h($Dados->TOLERANCIA_FIM,4) : '';
				$data[$key]->TOLERANCIADIA	= (strlen(trim($data[$key]->TOLERANCIADIA)) > 0) ? m2h($Dados->TOLERANCIADIA,4) : '';
				$data[$key]->INTERVALO					= (strlen(trim($data[$key]->INTERVALO)) > 0) ? m2h($Dados->INTERVALO) : '';
				$data[$key]->HEXTRA_DIARIA 				= m2h($data[$key]->HEXTRA_DIARIA,4);
				$data[$key]->QTD2 						= m2h($data[$key]->QTD2);
				$data[$key]->EXCESSO_JORNADA_SEMANAL	= (strlen(trim($data[$key]->EXCESSO_JORNADA_SEMANAL)) > 0) ? m2h($Dados->EXCESSO_JORNADA_SEMANAL) : '';
				
			

				if($data[$key]->ENTRADA2 == '24:00') $data[$key]->ENTRADA2 = "+0:00";
				if($data[$key]->SAIDA2 == '24:00') $data[$key]->SAIDA2 = "+0:00";

				$data[$key]->TIPO = ($data[$key]->TIPO == 'TRABALHO') ? '<span class="badge badge-dark text-white">'.$data[$key]->TIPO.'</span>' : '<span class="badge badge-primary">'.$data[$key]->TIPO.'</span>';
				

			}

			return $data;
		}

		return false;

	}

	public function geraMelhoria3()
	{

		$query = "
		DECLARE @DATABASEHOR DATETIME, @CODIGO VARCHAR(20), @CODCOLIGADA DCODCOLIGADA;

		DECLARE MEUCURSOR CURSOR  FOR
		SELECT DATABASEHOR, CODIGO, CODCOLIGADA FROM AHORARIO WHERE CODCOLIGADA = '{$this->coligada}' AND INATIVO = 0 AND CODIGO NOT IN ('0013','0063') AND CODIGO NOT IN (SELECT CODHORARIO FROM Z_OUTSERV_MELHORIAS3 WHERE CODCOLIGADA = '{$this->coligada}' GROUP BY CODHORARIO);
		
		OPEN MEUCURSOR;
		
		FETCH NEXT FROM MEUCURSOR INTO @DATABASEHOR, @CODIGO, @CODCOLIGADA;
		
		WHILE @@FETCH_STATUS = 0
		BEGIN
			
			
			-----------------
			WITH MEUHORARIO AS (
		
				SELECT
					Y.*,
					(CASE 
					  WHEN DATEPART(DW,DATA) = 1 THEN 'Dom'
					  WHEN DATEPART(DW,DATA) = 2 THEN 'Seg'
					  WHEN DATEPART(DW,DATA) = 3 THEN 'Ter'
					  WHEN DATEPART(DW,DATA) = 4 THEN 'Qua'
					  WHEN DATEPART(DW,DATA) = 5 THEN 'Qui'
					  WHEN DATEPART(DW,DATA) = 6 THEN 'Sex'
					  WHEN DATEPART(DW,DATA) = 7 THEN 'Sab'
				  END)SEM
				,  DATEPART(wk,DATA)NRO_SEM
				,ISNULL((CASE WHEN TIPO = 'TRABALHO' THEN DBO.MINTOTIME(ISNULL((CASE WHEN SAIDA1 < ENTRADA1 THEN SAIDA1+1440 - ENTRADA1 ELSE SAIDA1 - ENTRADA1 END),0) 
						 + ISNULL((CASE WHEN SAIDA2 < ENTRADA2 THEN SAIDA2+1440 - ENTRADA2 ELSE SAIDA2 - ENTRADA2 END),0))
					   ELSE NULL END),'00:00')QTD  
					   
					   
					  ,ISNULL((CASE WHEN TIPO = 'TRABALHO' THEN (ISNULL((CASE WHEN SAIDA1 < ENTRADA1 THEN SAIDA1+1440 - ENTRADA1 ELSE SAIDA1 - ENTRADA1 END),0) 
						 + ISNULL((CASE WHEN SAIDA2 < ENTRADA2 THEN SAIDA2+1440 - ENTRADA2 ELSE SAIDA2 - ENTRADA2 END),0))
					   ELSE NULL END),'0')QTD2,
					ROW_NUMBER() OVER (PARTITION BY Y.CODINDICE ORDER BY Y.DATA) AS LINHA
				FROM 
				(
					SELECT
						A.*,
						dbo.CalculoCicloHorario(A.CODINDICE, A.CODHORARIO, A.CODCOLIGADA) CICLO
					FROM
						dbo.CALCULO_HORARIO(CONVERT(VARCHAR, @DATABASEHOR, 103), @CODIGO, 1, @CODCOLIGADA, (SELECT MAX(INDINICIOHOR) FROM AINDHOR WHERE CODHORARIO = @CODIGO AND CODCOLIGADA = @CODCOLIGADA)) A
				)Y
		)
		
		
		INSERT INTO Z_OUTSERV_MELHORIAS3
		SELECT
			CODHORARIO,
			DATA,
			CODINDICE,
			CODCOLIGADA,
			TIPO,
			ENTRADA1,
			SAIDA1,
			ENTRADA2,
			SAIDA2,
			CICLO,
			SEM,
			NRO_SEM,
			QTD,
			QTD2,
			LINHA,
			0 HEXTRA_DIARIA,
			NULL EXCESSO_JORNADA_SEMANAL,
			(ENTRADA2 - SAIDA1) INTERVALO,
			NULL TOLERANCIADIA,
			NULL TOLERANCIA_INICIO,
			NULL TOLERANCIA_FIM

		FROM
			MEUHORARIO
		ORDER BY
			DATA 
			-----------------
			
			
			
			FETCH NEXT FROM MEUCURSOR INTO @DATABASEHOR, @CODIGO, @CODCOLIGADA;
		END
		
		CLOSE MEUCURSOR;
		DEALLOCATE MEUCURSOR;
		";
		$this->dbrm->query($query);

		$result = 	$this->dbrm
					->table('Z_OUTSERV_MELHORIAS4')
					->select('CODHORARIO')
					->where('CODCOLIGADA', $this->coligada)
					->groupBy('CODHORARIO');

		$in = "";
		if($result){
			foreach($result->get()->getResultArray() as $Horario){
				$in .= "'{$Horario['CODHORARIO']}',";
			}

			$in = rtrim($in,',');
		}else{
			return;
		}

		$query = "
			DECLARE @DATABASEHOR DATETIME, @CODIGO VARCHAR(20), @CODCOLIGADA DCODCOLIGADA;
			DECLARE MEUCURSOR CURSOR  FOR
			SELECT A.DATABASEHOR, A.CODIGO, A.CODCOLIGADA FROM AHORARIO A WHERE A.CODCOLIGADA = '{$this->coligada}' AND A.INATIVO = 0 AND CODIGO NOT IN (".$in.");
			
			OPEN MEUCURSOR;
			
			FETCH NEXT FROM MEUCURSOR INTO @DATABASEHOR, @CODIGO, @CODCOLIGADA;
			
			WHILE @@FETCH_STATUS = 0
			BEGIN
				
			-----------------
				WITH MEUHORARIO AS (
			
					SELECT
						DATA,
						CODHORARIO,
						CODCOLIGADA,
						CODINDICE
					FROM
					dbo.CALCULO_HORARIO(CONVERT(VARCHAR,@DATABASEHOR,103), @CODIGO, 1, '{$this->coligada}', DATEDIFF(DAY, @DATABASEHOR, '2050-12-31'))
			)
			
			
			INSERT INTO Z_OUTSERV_MELHORIAS4
			SELECT 
				*
			FROM 
				MEUHORARIO;
			
				-----------------
				
				
				
				FETCH NEXT FROM MEUCURSOR INTO @DATABASEHOR, @CODIGO, @CODCOLIGADA;
			END
			
			CLOSE MEUCURSOR;
			DEALLOCATE MEUCURSOR;
		";

		$this->dbrm->query($query);

	}

	public function SalvaDadosHorario($request)
	{

		foreach($request as $key => $post){

			$ciclo = (int)$post['ciclo'];
			if($ciclo <= 0) $ciclo = 1;

			$excesso = (strlen(trim($post['excesso'])) <= 0) ? null : h2m($post['excesso']);
			$tolerancia_inicio = (strlen(trim($post['tolerancia_inicio'])) <= 0) ? null : h2m($post['tolerancia_inicio']);
			$tolerancia_fim = (strlen(trim($post['tolerancia_fim'])) <= 0) ? null : h2m($post['tolerancia_fim']);

			$this->dbrm
				->table('Z_OUTSERV_MELHORIAS3')
				->set([
					'CICLO' 					=> $ciclo,
					'HEXTRA_DIARIA' 			=> h2m($post['extra']),
					'EXCESSO_JORNADA_SEMANAL'	=> $excesso,
					'TOLERANCIA_INICIO'			=> $tolerancia_inicio,
					'TOLERANCIA_FIM'			=> $tolerancia_fim
				])
				->where('CODHORARIO', $post['codhorario'])
				->where('CODINDICE', $post['codindice'])
				->where('CODCOLIGADA', $this->coligada)
				->update();

		}

		return 	($this->dbrm->affectedRows() > 0) 
				? responseJson('success', 'Configuração realizada com sucesso')
				: responseJson('error', 'Não foi possível configurar este horário');

	}

	public function ListaBatidasDia($request)
	{

		try{

			$Espelho = model('Ponto/EspelhoModel');

			$chapa 		= $request['chapa'];
			$periodo 	= $request['data'].$request['data'];

			$data = [];
			$data2 = [];

			$getDadosHorario = $Espelho->getDadosHorario($periodo, $chapa);

			$result = $Espelho->ListarEspelhoDias($periodo, $chapa);

			$getIndice = $Espelho->getIndice($chapa, $result[0]['DATA']);

			$data2['INDICE'] 			= $getIndice[0]['IND_CALC'];
			$data2['HORARIO'] 			= $getDadosHorario[0]['CODIGO'];
			$data2['DESCHORARIO'] 		= $getDadosHorario[0]['DESCRICAO'];
			$data2['HORARIOESCALA'] 	= $getIndice[0]['CODHORARIO'];
			$data2['DESCHORARIOESCALA'] = $getIndice[0]['DESCRICAO'];
			$data2['DATA'] 				= dtBr($result[0]['DATA']);
			$data2['DATA'] 				= dtBr($result[0]['DATA']);
			$data2['SEMANA'] 			= diaSemana($result[0]['DATA'], true);
			$data2['FALTA'] 			= m2h($result[0]['FALTA']);
			$data2['ATRASO'] 			= m2h($result[0]['ATRASO']);
			$data2['HTRAB'] 			= m2h($result[0]['HTRAB']);
			$data2['EXTRAEXECUTADO'] 	= m2h($result[0]['EXTRAEXECUTADO']);
			$data2['EXTRAAUTORIZADO']	= m2h($result[0]['EXTRAAUTORIZADO']);
			$data2['ABONO'] 			= m2h($result[0]['ABONO']);
			$data2['EXTRA_1AFAIXA'] 	= m2h($result[0]['EXTRA_1AFAIXA']);
			$data2['EXTRA_2AFAIXA'] 	= m2h($result[0]['EXTRA_2AFAIXA']);
			$data2['EXTRA_100'] 		= m2h($result[0]['EXTRA_100']);
			
			$resBatidasEspelho = $Espelho->ListarEspelhoBatidas($periodo, $chapa, true);

			$batidas = [0];

			$nbatida = 1;

			$DiasEspelho2['DATA'] = array(
				$result[0]['DATA'],
				somarDias($result[0]['DATA'],2).' 00:00:00.000',
				somarDias($result[0]['DATA'],-2).' 00:00:00.000',
			);


			
			$indice_ent = 1;
			$indice_sai = 1;

			$ent[1] = '';
			$sai[1] = '';
			$ent[2] = '';
			$sai[2] = '';
			$ent[3] = '';
			$sai[3] = '';
			$ent[4] = '';
			$sai[4] = '';

			$ent_portal[1] = '';
			$sai_portal[1] = '';
			$ent_portal[2] = '';
			$sai_portal[2] = '';
			$ent_portal[3] = '';
			$sai_portal[3] = '';
			$ent_portal[4] = '';
			$sai_portal[4] = '';

			$ent_motivo_reprova[1] = '';
			$sai_motivo_reprova[1] = '';
			$ent_motivo_reprova[2] = '';
			$sai_motivo_reprova[2] = '';
			$ent_motivo_reprova[3] = '';
			$sai_motivo_reprova[3] = '';
			$ent_motivo_reprova[4] = '';
			$sai_motivo_reprova[4] = '';

			$status_ent[1] = false;
			$status_sai[1] = false;
			$status_ent[2] = false;
			$status_sai[2] = false;
			$status_ent[3] = false;
			$status_sai[3] = false;
			$status_ent[4] = false;
			$status_sai[4] = false;

			$dataref_forcado_ent[1] = false;
			$dataref_forcado_sai[1] = false;
			$dataref_forcado_ent[2] = false;
			$dataref_forcado_sai[2] = false;
			$dataref_forcado_ent[3] = false;
			$dataref_forcado_sai[3] = false;
			$dataref_forcado_ent[4] = false;
			$dataref_forcado_sai[4] = false;

			$dataref_ent[1] = false;
			$dataref_sai[1] = false;
			$dataref_ent[2] = false;
			$dataref_sai[2] = false;
			$dataref_ent[3] = false;
			$dataref_sai[3] = false;
			$dataref_ent[4] = false;
			$dataref_sai[4] = false;

			$data_ent[1] = false;
			$data_sai[1] = false;
			$data_ent[2] = false;
			$data_sai[2] = false;
			$data_ent[3] = false;
			$data_sai[3] = false;
			$data_ent[4] = false;
			$data_sai[4] = false;

			$idaafdt_ent[1] = false;
			$idaafdt_sai[1] = false;
			$idaafdt_ent[2] = false;
			$idaafdt_sai[2] = false;
			$idaafdt_ent[3] = false;
			$idaafdt_sai[3] = false;
			$idaafdt_ent[4] = false;
			$idaafdt_sai[4] = false;

			// ent 1
			foreach($DiasEspelho2['DATA'] as $Data){
				$DiasEspelho['DATA'] = $Data;
				
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['natureza'] == 0) {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['idaafdt']);
					$indice_ent++;
				} else {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][0]['idaafdt']);
					$indice_sai++;
				}
			}

			// sai 1
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['natureza'] == 1) {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['idaafdt']);
					$indice_sai++;
				} else {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][1]['idaafdt']);
					$indice_ent++;
				}
			}

			// ent 2
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['natureza'] == 0) {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['idaafdt']);
					$indice_ent++;
				} else {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][2]['idaafdt']);
					$indice_sai++;
				}
			}

			// sai 2
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['natureza'] == 1) {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['idaafdt']);
					$indice_sai++;
				} else {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][3]['idaafdt']);
					$indice_ent++;
				}
			}

			// ent 3
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['natureza'] == 0) {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['idaafdt']);
					$indice_ent++;
				} else {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][4]['idaafdt']);
					$indice_sai++;
				}
			}

			// sai 3
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['natureza'] == 1) {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['idaafdt']);
					$indice_sai++;
				} else {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][5]['idaafdt']);
					$indice_ent++;
				}
			}

			// ent 4
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['natureza'] == 0) {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['idaafdt']);
					$indice_ent++;
				} else {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][6]['idaafdt']);
					$indice_sai++;
				}
			}

			// sai 4
			if (isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'])) {
				$concat = false;
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'] > $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']) $concat = '-';
				if (strlen(trim($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'])) > 0 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia'] < $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']) $concat = '+';
				if ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['natureza'] == 1) {
					$sai[$indice_sai] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'], 4);
					$sai_portal[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] == 'T') $sai_portal[$indice_sai] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$sai_motivo_reprova[$indice_sai] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['motivo_reprova'];
					$status_sai[$indice_sai] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] : 'D';
					$dataref_forcado_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['forcado'] == 1) ? 1 : 0;
					$dataref_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia']);
					$data_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']);
					$idaafdt_sai[$indice_sai] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['idaafdt']);
					$indice_sai++;
				} else {
					$ent[$indice_ent] = $concat . m2h($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['batida'], 4);
					$ent_portal[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['portal'] == 1 && $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] != 'T') ? '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>' : '';

					if($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] == 'T') $ent_portal[$indice_ent] = '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>';

					$ent_motivo_reprova[$indice_ent] = $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['motivo_reprova'];
					$status_ent[$indice_ent] = isset($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status']) ? $resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['status'] : 'D';
					$dataref_forcado_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['forcado'] == 1) ? 1 : 0;
					$dataref_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['datareferencia']);
					$data_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['data']);
					$idaafdt_ent[$indice_ent] = ($resBatidasEspelho[$chapa][$DiasEspelho['DATA']]['batidas'][7]['idaafdt']);
					$indice_ent++;
				}
			}
		}





			$data2['BATIDA_1'] = $ent[1];
			$data2['BATIDA_2'] = $sai[1];
			$data2['BATIDA_3'] = $ent[2];
			$data2['BATIDA_4'] = $sai[2];
			$data2['BATIDA_5'] = $ent[3];
			$data2['BATIDA_6'] = $sai[3];
			$data2['BATIDA_7'] = $ent[4];
			$data2['BATIDA_8'] = $sai[4];

			$data2['BATIDA_1_PORTAL'] = (strlen(trim($ent_motivo_reprova[1])) > 0) ? '#fe0200' : (($ent_portal[1] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($ent_portal[1] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));
			$data2['BATIDA_2_PORTAL'] = (strlen(trim($sai_motivo_reprova[1])) > 0) ? '#fe0200' : (($sai_portal[1] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($sai_portal[1] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));
			$data2['BATIDA_3_PORTAL'] = (strlen(trim($ent_motivo_reprova[2])) > 0) ? '#fe0200' : (($ent_portal[2] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($ent_portal[2] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));
			$data2['BATIDA_4_PORTAL'] = (strlen(trim($sai_motivo_reprova[2])) > 0) ? '#fe0200' : (($sai_portal[2] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($sai_portal[2] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));
			$data2['BATIDA_5_PORTAL'] = (strlen(trim($ent_motivo_reprova[3])) > 0) ? '#fe0200' : (($ent_portal[3] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($ent_portal[3] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));
			$data2['BATIDA_6_PORTAL'] = (strlen(trim($sai_motivo_reprova[3])) > 0) ? '#fe0200' : (($sai_portal[3] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($sai_portal[3] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));
			$data2['BATIDA_7_PORTAL'] = (strlen(trim($ent_motivo_reprova[4])) > 0) ? '#fe0200' : (($ent_portal[4] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($ent_portal[4] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));
			$data2['BATIDA_8_PORTAL'] = (strlen(trim($sai_motivo_reprova[4])) > 0) ? '#fe0200' : (($sai_portal[4] == '<i title="Aguardando Aprovação Gestor" class="mdi mdi-square" style="font-size:20px; color: #ace5ff;"></i>') ? '#ace5ff' : (($sai_portal[4] == '<i title="Pendente Aprovação RH" class="mdi mdi-square" style="font-size:20px; color: #acffcd;"></i>') ? '#acffcd' : '#ffffff'));

			
			$aviso = '';
			if($result[0]['AFASTAMENTO'] > 0){
				$aviso = '<span class="badge badge-purple" style="background: #004d95 !important;">'.$result[0]['AFASTAMENTO'].'</span>';
			}else
			if($result[0]['FERIADO'] > 0){
				$aviso = '<span class="badge badge-success" style="background: #04609d !important;">FERIADO</span>';
			}else
			if($result[0]['COMPENSADO'] > 0){
				$aviso = '<span class="badge badge-purple" style="background: #004d95 !important;">COMPENSADO</span>';
			}else
			if($result[0]['DESCANSO'] > 0){
				$aviso = '<span class="badge badge-purple" style="background: #004d95 !important;">DESCANSO</span>';
			}else
			if($result[0]['FERIAS'] > 0){
				$aviso = '<span class="badge badge-purple" style="background: #004d95 !important;">FERIAS</span>';
			}
			$data2['AVISO'] = $aviso;

			return $data2;
			exit();

			foreach($result as $key => $row){
				foreach($row as $dados){
					
					$data['BATIDA_'.$nbatida] = ($dados['batidas'][0] ?? '') ? m2h($dados['batidas'][0]['batida']) : '';
					$batida_rh_1 = (($dados['batidas'][0]['status'] ?? '') == 'T') ? '#acffcd' : '#FFFFFF';
					$motivo_reprova_1 = (($dados['batidas'][0]['motivo_reprova'] ?? '') == '') ? $batida_rh_1 : '#fe0200';
					$data['BATIDA_'.$nbatida.'_PORTAL'] = (($dados['batidas'][0]['portal'] ?? '') == 1 && (($dados['batidas'][0]['motivo_reprova'] ?? '') == '')) ? '#ace5ff' : $motivo_reprova_1;
					$nbatida++;
					
					if(($dados['batidas'][1] ?? '') != ''){
						$data['BATIDA_'.$nbatida] = ($dados['batidas'][1] ?? '') ? m2h($dados['batidas'][1]['batida']) : '';
						$batida_rh_2 = (($dados['batidas'][1]['status'] ?? '') == 'T') ? '#acffcd' : '#FFFFFF';
						$motivo_reprova_2 = (($dados['batidas'][1]['motivo_reprova'] ?? '') == '') ? $batida_rh_2 : '#fe0200';
						$data['BATIDA_'.$nbatida.'_PORTAL'] = (($dados['batidas'][1]['portal'] ?? '') == 1 && (($dados['batidas'][1]['motivo_reprova'] ?? '') == '')) ? '#ace5ff' : $motivo_reprova_2;
						$nbatida++;
					}
					
					if(($dados['batidas'][2] ?? '') != ''){
						$data['BATIDA_'.$nbatida] = ($dados['batidas'][2] ?? '') ? m2h($dados['batidas'][2]['batida']) : '';
						$batida_rh_3 = (($dados['batidas'][2]['status'] ?? '') == 'T') ? '#acffcd' : '#FFFFFF';
						$motivo_reprova_3 = (($dados['batidas'][2]['motivo_reprova'] ?? '') == '') ? $batida_rh_3 : '#fe0200';
						$data['BATIDA_'.$nbatida.'_PORTAL'] = (($dados['batidas'][2]['portal'] ?? '') == 1 && (($dados['batidas'][2]['motivo_reprova'] ?? '') == '')) ? '#ace5ff' : $motivo_reprova_3;
						$nbatida++;
					}
					
					if(($dados['batidas'][3] ?? '') != ''){
						$data['BATIDA_'.$nbatida] = ($dados['batidas'][3] ?? '') ? m2h($dados['batidas'][3]['batida']) : '';
						$batida_rh_4 = (($dados['batidas'][3]['status'] ?? '') == 'T') ? '#acffcd' : '#FFFFFF';
						$motivo_reprova_4 = (($dados['batidas'][3]['motivo_reprova'] ?? '') == '') ? $batida_rh_4 : '#fe0200';
						$data['BATIDA_'.$nbatida.'_PORTAL'] = (($dados['batidas'][3]['portal'] ?? '') == 1 && (($dados['batidas'][3]['motivo_reprova'] ?? '') == '')) ? '#ace5ff' : $motivo_reprova_4;
						$nbatida++;
					}
					
					if(($dados['batidas'][4] ?? '') != ''){
						$data['BATIDA_'.$nbatida] = ($dados['batidas'][4] ?? '') ? m2h($dados['batidas'][4]['batida']) : '';
						$batida_rh_5 = (($dados['batidas'][4]['status'] ?? '') == 'T') ? '#acffcd' : '#FFFFFF';
						$motivo_reprova_5 = (($dados['batidas'][4]['motivo_reprova'] ?? '') == '') ? $batida_rh_5 : '#fe0200';
						$data['BATIDA_'.$nbatida.'_PORTAL'] = (($dados['batidas'][4]['portal'] ?? '') == 1 && (($dados['batidas'][4]['motivo_reprova'] ?? '') == '')) ? '#ace5ff' : $motivo_reprova_5;
						$nbatida++;
					}
					
					if(($dados['batidas'][5] ?? '') != ''){
						$data['BATIDA_'.$nbatida] = ($dados['batidas'][5] ?? '') ? m2h($dados['batidas'][5]['batida']) : '';
						$batida_rh_6 = (($dados['batidas'][5]['status'] ?? '') == 'T') ? '#acffcd' : '#FFFFFF';
						$motivo_reprova_6 = (($dados['batidas'][5]['motivo_reprova'] ?? '') == '') ? $batida_rh_6 : '#fe0200';
						$data['BATIDA_'.$nbatida.'_PORTAL'] = (($dados['batidas'][5]['portal'] ?? '') == 1 && (($dados['batidas'][5]['motivo_reprova'] ?? '') == '')) ? '#ace5ff' : $motivo_reprova_6;
						$nbatida++;
					}
					
					$batidas[0] = $data;

				}
			}
			
			while($nbatida){
				$batidas[0]['BATIDA_'.$nbatida] = '';
				$batidas[0]['BATIDA_'.$nbatida.'_PORTAL'] = '';
				$nbatida++;

				if($nbatida > 6) break;
			}
			$data2 += $batidas[0];

			return $data2;

		} catch (\Exception | \Error $e) {
			echo $e;
			return false;
		}

	}

	public function ListaConfiguracaoWorkflow()
	{
		$result = $this->dbportal
				->table('zcrmportal_ocorrencia_workflow')
				->select('*')
				->where('codcoligada', $this->coligada);
		if($result){
			return $result->get()->getResult() ?? false;
		}

		return false;
			
	}

	public function ListaConfiguracaoWorkflowRH()
	{
		$result = $this->dbportal
				->table('zcrmportal_espelho_config')
				->select('*')
				->where('coligada', $this->coligada);
		if($result){
			return $result->get()->getResult() ?? false;
		}

		return false;
			
	}

	public function ConfiguracaoWorkflow($request)
	{
		try{

			$data = [
				'data1'			=> $request['data1'].' '.$request['horario1'],
				'data2'			=> $request['data2'].' '.$request['horario2'],
				'horario1' 		=> h2m($request['horario1']),
				'horario2' 		=> h2m($request['horario2']),
				'ciclo1' 		=> $request['ciclo1'],
				'ciclo2' 		=> $request['ciclo2'],
				'usucad'		=> $this->log_id,
				'usualt'		=> $this->log_id,
				'dtcad'			=> $this->now,
				'dtalt'			=> $this->now,
				'codcoligada'	=> $this->coligada,
			];

			$result = self::ListaConfiguracaoWorkflow();

			if($result){
				
				$this->dbportal
				->table('zcrmportal_ocorrencia_workflow')
				->set($data)
				->where('codcoligada', $this->coligada)
				->update();

			}else{

				$this->dbportal
				->table('zcrmportal_ocorrencia_workflow')
				->set($data)
				->insert();

			}

			return 	($this->dbportal->affectedRows() > 0) 
					? responseJson('success', 'Configuração realizada com sucesso')
					: responseJson('error', 'Não foi possivel configurar este workflow');

		} catch (\Exception | \Error $e) {
			return responseJson('error', 'Erro interno: '.$e->getMessage());
		}

	}

	public function ConfiguracaoWorkflowRH($request)
	{
		try{

			$data = [
				'wflow_dias_notif' 		 => $request['dgestor1'],
				'wflow_dias_notif_acima' => $request['dgestor2'],
			];

			$result = self::ListaConfiguracaoWorkflow();

			$this->dbportal
			->table('zcrmportal_espelho_config')
			->set($data)
			->where('coligada', $this->coligada)
			->update();

			return 	($this->dbportal->affectedRows() > 0) 
					? responseJson('success', 'Configuração realizada com sucesso')
					: responseJson('error', 'Não foi possivel configurar este workflow');

		} catch (\Exception | \Error $e) {
			return responseJson('error', 'Erro interno: '.$e->getMessage());
		}

	}

	public function ListaConfiguracaoMotorita($codfuncao = false)
	{

		$result = $this->dbportal
				->table('zcrmportal_ocorrencia_motorista')
				->select('*')
				->where('codcoligada', $this->coligada)
				->where(($codfuncao) ? " codfuncao = '{$codfuncao}' " : ' codfuncao IS NOT NULL ')
				->where('dtdel IS NULL');
		if($result){
			return $result->get()->getResult() ?? false;
		}

		return false;

	}

	public function ConfiguracaoMotorista($request)
	{

		try{

			$existe = self::ListaConfiguracaoMotorita($request['codfuncao']);
			if($existe){
				return responseJson('error', 'Função já cadastrada.');
			}

			$this->dbportal
			->table('zcrmportal_ocorrencia_motorista')
			->set([
				'codcoligada' 	=> $this->coligada,
				'codfuncao' 	=> $request['codfuncao'],
				'usucad' 		=> $this->log_id,
				'dtcad' 		=> $this->now
			])
			->insert();

			return 	($this->dbportal->affectedRows() > 0) 
					? responseJson('success', 'Função cadastrada com sucesso')
					: responseJson('error', 'Não foi possivel cadastrar esta função');

		} catch (\Exception | \Error $e) {
			return responseJson('error', 'Erro interno: '.$e->getMessage());
		}


	}

	public function ConfigMotoristaCheck($request){

		try{

			$existe = self::ListaConfiguracaoMotorita($request['codfuncao']);
			if(!$existe){
				return responseJson('error', 'Função não cadastrada.');
			}

			$this->dbportal
			->table('zcrmportal_ocorrencia_motorista')
			->set([
				'usualt' => $this->log_id, 
				'dtalt' => $this->now,
				'intervalo_obrigatorio' => $request['obrigatorio'],
				'intervalo_fracionado' => $request['fracionado']
				
			])
			->where('codcoligada', $this->coligada)
			->where('codfuncao', $request['codfuncao'])
			->update();

			return 	($this->dbportal->affectedRows() > 0) 
						? responseJson('success', 'Parametrização realizada com sucesso')
						: responseJson('error', 'Não foi possivel alterar essa configuração');

		} catch (\Exception | \Error $e) {
			return responseJson('error', 'Erro interno: '.$e->getMessage());
		}

	}

	public function SalvarDadosMotorista($request)
	{
		try{

			$existe = self::ListaConfiguracaoMotorita($request['codfuncao']);
			if(!$existe){
				return responseJson('error', 'Função não cadastrada.');
			}

			$request['valor'] = (strlen(trim($request['valor'])) <= 0 ? '0:00' : $request['valor']);

			$this->dbportal
			->table('zcrmportal_ocorrencia_motorista')
			->set([
				'usualt' => $this->log_id, 
				'dtalt' => $this->now, 
				"{$request['datafield']}" => h2m($request['valor'])
			])
			->where('codcoligada', $this->coligada)
			->where('codfuncao', $request['codfuncao'])
			->update();

			return 	($this->dbportal->affectedRows() > 0) 
						? responseJson('success', 'Parametrização realizada com sucesso')
						: responseJson('error', 'Não foi possivel cadastrar esta função');

		} catch (\Exception | \Error $e) {
			return responseJson('error', 'Erro interno: '.$e->getMessage());
		}

	}

	public function ExcluirMotorista($request)
	{

		try{

			$existe = self::ListaConfiguracaoMotorita($request['codfuncao']);
			if(!$existe){
				return responseJson('error', 'Função não cadastrada.');
			}

			$this->dbportal
			->table('zcrmportal_ocorrencia_motorista')
			->set([
				'usudel' => $this->log_id, 
				'dtdel' => $this->now,
			])
			->where('codcoligada', $this->coligada)
			->where('codfuncao', $request['codfuncao'])
			->update();

			return 	($this->dbportal->affectedRows() > 0) 
						? responseJson('success', 'Função excluída com sucesso')
						: responseJson('error', 'Não foi possivel excluir esta função');

		} catch (\Exception | \Error $e) {
			return responseJson('error', 'Erro interno: '.$e->getMessage());
		}

	}

	public function cron_indice_horario()
	{

		ini_set("pcre.backtrack_limit", "50000000");
        set_time_limit(60*90);
        ini_set('max_execution_time', 60*90);

		$delete = $this->dbrm->query(' DELETE FROM Z_OUTSERV_MELHORIAS4 ');
		if($delete){

			$coligadas = $this->dbrm->query(" SELECT CODCOLIGADA FROM GCOLIGADA ");
			$resColigada = $coligadas->getResult();

			if($resColigada){
				foreach($resColigada as $Coligada){

					$query = "
						DECLARE @DATABASEHOR DATETIME, @CODIGO VARCHAR(20), @CODCOLIGADA DCODCOLIGADA;
						DECLARE MEUCURSOR CURSOR  FOR
						SELECT A.DATABASEHOR, A.CODIGO, A.CODCOLIGADA FROM AHORARIO A WHERE A.CODCOLIGADA = '{$Coligada->CODCOLIGADA}' AND A.INATIVO = 0;
						
						OPEN MEUCURSOR;
						
						FETCH NEXT FROM MEUCURSOR INTO @DATABASEHOR, @CODIGO, @CODCOLIGADA;
						
						WHILE @@FETCH_STATUS = 0
						BEGIN
							
						-----------------
							WITH MEUHORARIO AS (
						
								SELECT
									DATA,
									CODHORARIO,
									'{$Coligada->CODCOLIGADA}' CODCOLIGADA,
									CODINDICE
								FROM
								dbo.CALCULO_HORARIO(CONVERT(VARCHAR,@DATABASEHOR,103), @CODIGO, 1, '{$Coligada->CODCOLIGADA}', DATEDIFF(DAY, @DATABASEHOR, '2050-12-31'))
						)
						
						
						INSERT INTO Z_OUTSERV_MELHORIAS4
						SELECT 
							*
						FROM 
							MEUHORARIO;
						
							-----------------
							
							
							
							FETCH NEXT FROM MEUCURSOR INTO @DATABASEHOR, @CODIGO, @CODCOLIGADA;
						END
						
						CLOSE MEUCURSOR;
						DEALLOCATE MEUCURSOR;
					";

					$this->dbrm->query($query);

				}
			}
			
		}

	}

}
?>