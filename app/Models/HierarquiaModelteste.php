<?php
namespace App\Models;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class HierarquiaModel extends Model {

    protected $dbportal;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
    }

    public function ListarHierarquiaSecaoPodeVer($chapas = false, $Codcoligada = false, $hierarquiaLider = false){
    	if($Codcoligada){
    		$coligada = $Codcoligada;
    	}else{
        		$coligada = session()->get('func_coligada');
        }
        if($chapas){
        	$chapa = $chapas;
        }else {
        	$chapa = util_chapa(session()->get('func_chapa'))['CHAPA'];
        }

		// verifica se é lider de BP
		$isLiderBP = $this->getSecaoPodeVerLiderBP();
		if($isLiderBP) return $isLiderBP;

        //$isLider = $this->dbportal->query(" SELECT * FROM zcrmportal_hierarquia_lider_func_ponto WHERE chapa = '{$chapa}' AND coligada = '{$coligada}' AND inativo IS NULL AND '".date('Y-m-d')."' BETWEEN A.perini AND (CASE WHEN A.perfim IS NOT NULL THEN A.perfim ELSE '2090-12-31' END) ");
		$isLider = $this->isLider($chapa, $coligada);
		if(!$isLider && $hierarquiaLider) $isLider = self::isLiderExcecao($chapa, $coligada);


        if($isLider && $hierarquiaLider){

			

            $query = "
			SELECT * FROM (
				SELECT
					C.chapa
				FROM
					zcrmportal_hierarquia_lider_ponto A
					INNER JOIN zcrmportal_hierarquia B ON B.id = A.id_hierarquia AND B.coligada = A.coligada
					INNER JOIN zcrmportal_hierarquia_lider_func_ponto C ON A.id = C.id_lider
				WHERE
						A.chapa = '{$chapa}'
					AND A.coligada = '{$coligada}'
					AND A.inativo IS NULL
					AND B.inativo IS NULL
					AND C.inativo IS NULL
					AND '".date('Y-m-d')."' BETWEEN A.perini AND (CASE WHEN A.perfim IS NOT NULL THEN A.perfim ELSE '2090-12-31' END)
				GROUP BY
					C.chapa
					
				UNION ALL 
	
				SELECT
					C.chapa
				FROM
					zcrmportal_hierarquia_lider_excecao_ponto X
					INNER JOIN zcrmportal_hierarquia_lider_ponto A ON A.id = X.id_lider AND X.coligada = A.coligada
					INNER JOIN zcrmportal_hierarquia B ON B.id = A.id_hierarquia AND B.coligada = A.coligada
					INNER JOIN zcrmportal_hierarquia_lider_func_ponto C ON A.id = C.id_lider
				WHERE
						X.chapa = '{$chapa}'
					AND X.coligada = '{$coligada}'
					AND A.inativo IS NULL
					AND B.inativo IS NULL
					AND C.inativo IS NULL
					AND X.inativo IS NULL 
					AND '".date('Y-m-d')."' BETWEEN A.perini AND (CASE WHEN A.perfim IS NOT NULL THEN A.perfim ELSE '2090-12-31' END)
				GROUP BY
					C.chapa
			)X GROUP BY chapa
            ";

			// exit();
			$result = $this->dbportal->query($query);
			return ($result->getNumRows() > 0) 
					? $result->getResultArray() 
					: false;

        }else{

            $query = "
                SELECT 
					XC.codsecao 
				FROM

				(
					--------------------
					-- NIVEL 1
					--------------------
					SELECT 
						CASE WHEN C.id_hierarquia IS NOT NULL THEN C.id_hierarquia ELSE A.id_hierarquia END id_hierarquia
					FROM 
						zcrmportal_hierarquia_chapa A
							LEFT JOIN zcrmportal_hierarquia_chapa_sub C ON C.id_hierarquia = A.id_hierarquia
						,
						zcrmportal_hierarquia B
					WHERE
						(A.chapa = '{$chapa}' OR C.chapa = '{$chapa}')
						AND A.id_hierarquia = B.id
						AND B.coligada = '{$coligada}'
						AND B.inativo IS NULL
						
					
					UNION ALL
					
					--------------------
					-- NIVEL 2
					--------------------
					SELECT 
						A.id id_hierarquia
					FROM
						zcrmportal_hierarquia A
					WHERE
						A.id_hierarquia IN (
							SELECT 
								CASE WHEN C.id_hierarquia IS NOT NULL THEN C.id_hierarquia ELSE A.id_hierarquia END id_hierarquia
							FROM 
								zcrmportal_hierarquia_chapa A
									LEFT JOIN zcrmportal_hierarquia_chapa_sub C ON C.id_hierarquia = A.id_hierarquia
								,
								zcrmportal_hierarquia B
							WHERE
								(A.chapa = '{$chapa}' OR C.chapa = '{$chapa}')
								AND A.id_hierarquia = B.id
								AND B.coligada = '{$coligada}'
								AND B.inativo IS NULL
						)
					
					UNION ALL
					
					--------------------
					-- NIVEL 3
					--------------------
					SELECT 
						A.id id_hierarquia
					FROM
						zcrmportal_hierarquia A
					WHERE
						A.id_hierarquia IN (
							SELECT 
								A.id id_hierarquia
							FROM
								zcrmportal_hierarquia A
							WHERE
								A.id_hierarquia IN (
									SELECT 
										CASE WHEN C.id_hierarquia IS NOT NULL THEN C.id_hierarquia ELSE A.id_hierarquia END id_hierarquia
									FROM 
										zcrmportal_hierarquia_chapa A
											LEFT JOIN zcrmportal_hierarquia_chapa_sub C ON C.id_hierarquia = A.id_hierarquia
										,
										zcrmportal_hierarquia B
									WHERE
										(A.chapa = '{$chapa}' OR C.chapa = '{$chapa}')
										AND A.id_hierarquia = B.id
										AND B.coligada = '{$coligada}'
										AND B.inativo IS NULL
								)
						)
					
					UNION ALL
					
					--------------------
					-- NIVEL 4
					--------------------
					SELECT 
						A.id id_hierarquia
					FROM
						zcrmportal_hierarquia A
					WHERE
						A.id_hierarquia IN (
							SELECT 
								A.id id_hierarquia
							FROM
								zcrmportal_hierarquia A
							WHERE
								A.id_hierarquia IN (
									SELECT 
										A.id id_hierarquia
									FROM
										zcrmportal_hierarquia A
									WHERE
										A.id_hierarquia IN (
											SELECT 
												CASE WHEN C.id_hierarquia IS NOT NULL THEN C.id_hierarquia ELSE A.id_hierarquia END id_hierarquia
											FROM 
												zcrmportal_hierarquia_chapa A
													LEFT JOIN zcrmportal_hierarquia_chapa_sub C ON C.id_hierarquia = A.id_hierarquia
												,
												zcrmportal_hierarquia B
											WHERE
												(A.chapa = '{$chapa}' OR C.chapa = '{$chapa}')
												AND A.id_hierarquia = B.id
												AND B.coligada = '{$coligada}'
												AND B.inativo IS NULL
										)
								)
						)
					
					UNION ALL
					
					--------------------
					-- NIVEL 5
					--------------------
					SELECT 
						A.id id_hierarquia
					FROM
						zcrmportal_hierarquia A
					WHERE
						A.id_hierarquia IN (
							SELECT 
								A.id id_hierarquia
							FROM
								zcrmportal_hierarquia A
							WHERE
								A.id_hierarquia IN (
									SELECT 
										A.id id_hierarquia
									FROM
										zcrmportal_hierarquia A
									WHERE
										A.id_hierarquia IN (
											SELECT 
												A.id id_hierarquia
											FROM
												zcrmportal_hierarquia A
											WHERE
												A.id_hierarquia IN (
													SELECT 
														CASE WHEN C.id_hierarquia IS NOT NULL THEN C.id_hierarquia ELSE A.id_hierarquia END id_hierarquia
													FROM 
														zcrmportal_hierarquia_chapa A
															LEFT JOIN zcrmportal_hierarquia_chapa_sub C ON C.id_hierarquia = A.id_hierarquia
														,
														zcrmportal_hierarquia B
													WHERE
														(A.chapa = '{$chapa}' OR C.chapa = '{$chapa}')
														AND A.id_hierarquia = B.id
														AND B.coligada = '{$coligada}'
														AND B.inativo IS NULL
												)
										)
								)
						)
					
					UNION ALL
					
					--------------------
					-- NIVEL 6
					--------------------
					SELECT 
						A.id id_hierarquia
					FROM
						zcrmportal_hierarquia A
					WHERE
						A.id_hierarquia IN (
							SELECT 
								A.id id_hierarquia
							FROM
								zcrmportal_hierarquia A
							WHERE
								A.id_hierarquia IN (
									SELECT 
										A.id id_hierarquia
									FROM
										zcrmportal_hierarquia A
									WHERE
										A.id_hierarquia IN (
											SELECT 
												A.id id_hierarquia
											FROM
												zcrmportal_hierarquia A
											WHERE
												A.id_hierarquia IN (
													SELECT 
														A.id id_hierarquia
													FROM
														zcrmportal_hierarquia A
													WHERE
														A.id_hierarquia IN (
															SELECT 
																CASE WHEN C.id_hierarquia IS NOT NULL THEN C.id_hierarquia ELSE A.id_hierarquia END id_hierarquia
															FROM 
																zcrmportal_hierarquia_chapa A
																	LEFT JOIN zcrmportal_hierarquia_chapa_sub C ON C.id_hierarquia = A.id_hierarquia
																,
																zcrmportal_hierarquia B
															WHERE
																(A.chapa = '{$chapa}' OR C.chapa = '{$chapa}')
																AND A.id_hierarquia = B.id
																AND B.coligada = '{$coligada}'
																AND B.inativo IS NULL
														)
												)
										)
								)
						)
				)XA,
				zcrmportal_hierarquia_frentetrabalho XB,
				zcrmportal_frente_trabalho XC
				WHERE
					XA.id_hierarquia = XB.id_hierarquia
					AND XC.id = XB.id_frentetrabalho
					AND XC.coligada = '{$coligada}'
					AND XB.inativo IS NULL
            ";

			$result = $this->dbportal->query($query);

			$arraySecao = [];

			if($result->getNumRows() > 0){
				foreach($result->getResultArray() as $key => $dados){
					$arraySecao[] = [
						'codsecao' => $dados['codsecao']
					];
				}
			}
			
			// verifica se o acesso é de BP
			$isBP = $this->getSecaoPodeVerBP();
			if($isBP){
				foreach($isBP as $key2 => $dadosbp){
					$arraySecao[] = [
						'codsecao' => $dadosbp['codsecao']
					];
				}
			}

			return $arraySecao;

        }

    }

	public function isLider($chapa = false, $coligada = false){

		$coligada = (!$coligada) ? (session()->get('func_coligada') ?? null) : $coligada;
        $chapa = (!$chapa) ? (util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null) : $chapa;

		if($coligada === null || $chapa === null) return false;

		$query = " SELECT * FROM zcrmportal_hierarquia_lider_ponto WHERE chapa = '{$chapa}' AND coligada = '{$coligada}' AND inativo IS NULL AND '".date('Y-m-d')."' BETWEEN perini AND (CASE WHEN perfim IS NOT NULL THEN perfim ELSE '2090-12-31' END) ";
		
		$result = $this->dbportal->query($query);
		return ($result->getNumRows() > 0) 
                ? true
                : self::isLiderExcecao($chapa, $coligada);

	}

	public function isLiderExcecao($chapa = false, $coligada = false){

		$coligada = (!$coligada) ? (session()->get('func_coligada') ?? null) : $coligada;
        $chapa = (!$chapa) ? (util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null) : $chapa;

		if($coligada === null || $chapa === null) return false;

		$query = " SELECT * FROM zcrmportal_hierarquia_lider_excecao_ponto WHERE chapa = '{$chapa}' AND coligada = '{$coligada}' AND inativo IS NULL AND '".date('Y-m-d')."' BETWEEN perini AND (CASE WHEN perfim IS NOT NULL THEN perfim ELSE '2090-12-31' END) ";
		// echo '<pre>'.$query;exit();
		$result = $this->dbportal->query($query);
		return ($result->getNumRows() > 0)
                ? true
                : false;

	}

	public function isGestor($chapa = false, $coligada = false){

		$coligada = (!$coligada) ? (session()->get('func_coligada') ?? null) : $coligada;
        $chapa = (!$chapa) ? (util_chapa(session()->get('func_chapa'))['CHAPA'] ?? null) : $chapa;

		if($coligada === null || $chapa === null) return false;
		
		$query = "
			SELECT DISTINCT chapa FROM (
				SELECT 
					a.chapa 
				FROM 
					zcrmportal_hierarquia_chapa a
					INNER JOIN zcrmportal_hierarquia b ON b.id = a.id_hierarquia AND b.inativo IS NULL
				WHERE 
						a.chapa = '{$chapa}' 
					AND a.inativo IS NULL 
					AND a.coligada = '{$coligada}'

				UNION ALL 
				
				SELECT 
					a.chapa 
				FROM 
					zcrmportal_hierarquia_chapa_sub a
					INNER JOIN zcrmportal_hierarquia b ON b.id = a.id_hierarquia AND b.inativo IS NULL
				WHERE 
						a.chapa = '{$chapa}' 
					AND a.inativo IS NULL 
					AND a.coligada = '{$coligada}'
			)X
		";
		$result = $this->dbportal->query($query);
		return ($result->getNumRows() > 0) 
                ? true
                : false;

	}
	
	public function getSecaoPodeVerBP(){

		$id_bp = $_SESSION['log_id'] ?? 0;
		
		$query = "
			SELECT 
				DISTINCT
				XC.codsecao 
			FROM

			(
				--------------------
				-- NIVEL 1
				--------------------
				SELECT 
					B.id id_hierarquia
				FROM 
					zcrmportal_hierarquia B
				WHERE
					B.id_bp = ".$id_bp."
					AND B.coligada = '".session()->get('func_coligada')."'
					
				
				UNION ALL
				
				--------------------
				-- NIVEL 2
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							B.id id_hierarquia
						FROM 
							zcrmportal_hierarquia B
						WHERE
							B.id_bp = ".$id_bp."
							AND B.coligada = '".session()->get('func_coligada')."'
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 3
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									B.id id_hierarquia
								FROM 
									zcrmportal_hierarquia B
								WHERE
									B.id_bp = ".$id_bp."
									AND B.coligada = '".session()->get('func_coligada')."'
							)
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 4
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									A.id id_hierarquia
								FROM
									zcrmportal_hierarquia A
								WHERE
									A.id_hierarquia IN (
										SELECT 
											B.id id_hierarquia
										FROM 
											zcrmportal_hierarquia B
										WHERE
											B.id_bp = ".$id_bp."
											AND B.coligada = '".session()->get('func_coligada')."'
									)
							)
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 5
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									A.id id_hierarquia
								FROM
									zcrmportal_hierarquia A
								WHERE
									A.id_hierarquia IN (
										SELECT 
											A.id id_hierarquia
										FROM
											zcrmportal_hierarquia A
										WHERE
											A.id_hierarquia IN (
												SELECT 
													B.id id_hierarquia
												FROM 
													zcrmportal_hierarquia B
												WHERE
													B.id_bp = ".$id_bp."
													AND B.coligada = '".session()->get('func_coligada')."'
											)
									)
							)
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 6
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									A.id id_hierarquia
								FROM
									zcrmportal_hierarquia A
								WHERE
									A.id_hierarquia IN (
										SELECT 
											A.id id_hierarquia
										FROM
											zcrmportal_hierarquia A
										WHERE
											A.id_hierarquia IN (
												SELECT 
													A.id id_hierarquia
												FROM
													zcrmportal_hierarquia A
												WHERE
													A.id_hierarquia IN (
														SELECT 
															B.id id_hierarquia
														FROM 
															zcrmportal_hierarquia B
														WHERE
															B.id_bp = ".$id_bp."
															AND B.coligada = '".session()->get('func_coligada')."'
													)
											)
									)
							)
					)
			)XA,
			zcrmportal_hierarquia_frentetrabalho XB,
			zcrmportal_frente_trabalho XC
			WHERE
				XA.id_hierarquia = XB.id_hierarquia
				AND XC.id = XB.id_frentetrabalho
				AND XC.coligada = '".session()->get('func_coligada')."'
		";

		//echo '<textarea>'.$query.'</textarea>';exit();
		$result = $this->dbportal->query($query);
		return ($result->getNumRows() > 0) 
				? $result->getResultArray() 
				: false;
		
	}

	public function getSecaoPodeVerLiderBP(){

		$chapa_lider_bp = util_chapa(session()->get('func_chapa'))['CHAPA'];
		
		$query = "
			SELECT 
				DISTINCT
				XC.codsecao 
			FROM

			(
				--------------------
				-- NIVEL 1
				--------------------
				SELECT 
					B.id id_hierarquia
				FROM 
					zcrmportal_hierarquia B
				WHERE
					B.lider_requisicao = '{$chapa_lider_bp}'
					AND B.coligada = '".session()->get('func_coligada')."'
					
				
				UNION ALL
				
				--------------------
				-- NIVEL 2
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							B.id id_hierarquia
						FROM 
							zcrmportal_hierarquia B
						WHERE
							B.lider_requisicao = '{$chapa_lider_bp}'
							AND B.coligada = '".session()->get('func_coligada')."'
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 3
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									B.id id_hierarquia
								FROM 
									zcrmportal_hierarquia B
								WHERE
									B.lider_requisicao = '{$chapa_lider_bp}'
									AND B.coligada = '".session()->get('func_coligada')."'
							)
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 4
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									A.id id_hierarquia
								FROM
									zcrmportal_hierarquia A
								WHERE
									A.id_hierarquia IN (
										SELECT 
											B.id id_hierarquia
										FROM 
											zcrmportal_hierarquia B
										WHERE
											B.lider_requisicao = '{$chapa_lider_bp}'
											AND B.coligada = '".session()->get('func_coligada')."'
									)
							)
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 5
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									A.id id_hierarquia
								FROM
									zcrmportal_hierarquia A
								WHERE
									A.id_hierarquia IN (
										SELECT 
											A.id id_hierarquia
										FROM
											zcrmportal_hierarquia A
										WHERE
											A.id_hierarquia IN (
												SELECT 
													B.id id_hierarquia
												FROM 
													zcrmportal_hierarquia B
												WHERE
													B.lider_requisicao = '{$chapa_lider_bp}'
													AND B.coligada = '".session()->get('func_coligada')."'
											)
									)
							)
					)
				
				UNION ALL
				
				--------------------
				-- NIVEL 6
				--------------------
				SELECT 
					A.id id_hierarquia
				FROM
					zcrmportal_hierarquia A
				WHERE
					A.id_hierarquia IN (
						SELECT 
							A.id id_hierarquia
						FROM
							zcrmportal_hierarquia A
						WHERE
							A.id_hierarquia IN (
								SELECT 
									A.id id_hierarquia
								FROM
									zcrmportal_hierarquia A
								WHERE
									A.id_hierarquia IN (
										SELECT 
											A.id id_hierarquia
										FROM
											zcrmportal_hierarquia A
										WHERE
											A.id_hierarquia IN (
												SELECT 
													A.id id_hierarquia
												FROM
													zcrmportal_hierarquia A
												WHERE
													A.id_hierarquia IN (
														SELECT 
															B.id id_hierarquia
														FROM 
															zcrmportal_hierarquia B
														WHERE
															B.lider_requisicao = '{$chapa_lider_bp}'
															AND B.coligada = '".session()->get('func_coligada')."'
													)
											)
									)
							)
					)
			)XA,
			zcrmportal_hierarquia_frentetrabalho XB,
			zcrmportal_frente_trabalho XC
			WHERE
				XA.id_hierarquia = XB.id_hierarquia
				AND XC.id = XB.id_frentetrabalho
				AND XC.coligada = '".session()->get('func_coligada')."'
		";

		//echo '<textarea>'.$query.'</textarea>';
		$result = $this->dbportal->query($query);
		return ($result->getNumRows() > 0) 
				? $result->getResultArray() 
				: false;
		
	}

}