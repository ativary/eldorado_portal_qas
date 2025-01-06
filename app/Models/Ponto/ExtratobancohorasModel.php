<?php
namespace App\Models\Ponto;
use CodeIgniter\Model;

//---------------------------------------------------------------------------------
// MODEL PRINCIPAL DO PORTAL
//---------------------------------------------------------------------------------
class ExtratobancohorasModel extends Model {

    protected $dbportal;
    protected $dbrm;
    private $log_id;
    private $now;
    private $coligada;
    
    public function __construct() {
        $this->dbportal = db_connect('dbportal');
        $this->dbrm     = db_connect('dbrm');
        $this->log_id   = session()->get('log_id');
        $this->coligada = session()->get('func_coligada');
        $this->now      = date('Y-m-d H:i:s');

        if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		if(DBRM_TIPO == 'oracle') $this->dbrm->query("ALTER SESSION SET NLS_NUMERIC_CHARACTERS='.,'");
    }

    public function geraRelatorioBHFunc($request)
    {

        $ft_chapa = ($request['chapa'] != null) ? " and pfunc.chapa = '{$request['chapa']}' " : '';
        $ft_secao = ($request['codsecao'] != null) ? " and pfunc.codsecao = '{$request['codsecao']}' " : '';

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		$isGestorLider = false;
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " pfunc.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
			$isGestorLider = true;
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
			$filtro_secao_gestor = " pfunc.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
			$isGestorLider = true;
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

        if($request['rh']) $qr_secao = "";
		if(!$isGestorLider && $request['chapa'] == null){
			$qr_secao = " AND 1 = 2 ";
		}else{
			if($request['chapa'] != null) $qr_secao = "";
		}

		// $qr_secao = "";
		// $ft_chapa = " AND pfunc.chapa = '050002709'";

        $query = "
			declare @MesP int, @AnoP int, @CodCol int, @CodCCO varchar(25)

			set	@MesP = ".$request['mescomp']."
			set @AnoP = ".$request['anocomp']."
			set @CodCol = ".$_SESSION['func_coligada']."
			--set @CodCCO = 30103


			SELECT 
				coligada,
				filial,
				secao,
				codcusto,
				centrodecusto,
				chapa,
				funcionario,
				funcao,
				sindicato,
				inicio,
				saldo,
				fim
			from
				
					(
					
			select	 pfunc.codcoligada																	as [Coligada]
					,pfunc.codfilial																	as [Filial]
					,psecao.descricao																	as [Secao]
					,gccusto.codccusto																	as [CodCusto]
					,gccusto.nome																		as [CentrodeCusto]
					,pfunc.chapa																		as [Chapa]
					,pfunc.nome																			as [Funcionario]
					,pfuncao.nome																		as [Funcao]
					,pfunc.salario																		as [Salario]
					,abancohorfundetalhe.data															as [Data]
					,case when pevento.provdescbase in ('P','B') then pevento.descricao
						  else null
					 end																				as [Evento Positivo]
					,case when pevento.provdescbase in ('P','B') then abancohorfundetalhe.valor
						  else null
					 end																				as [Horas Positivas Minutos]
					,case when pevento.provdescbase in ('P','B') then dbo.FN_HORA(abancohorfundetalhe.valor)
						  else null
					 end																				as [Horas Positivas]
					,case when pevento.provdescbase in ('D')	 then pevento.descricao
						  else null
					 end																				as [Evento Negativo]
					,case when pevento.provdescbase in ('D')	 then abancohorfundetalhe.valor
						  else null
					 end																				as [Horas Negativas Minutos]
					,case when pevento.provdescbase in ('D')	 then dbo.FN_HORA(abancohorfundetalhe.valor)
						  else null
					 end																				as [Horas Negativas]
					,(sum(asaldobancohor.extraant)  + sum(asaldobancohor.extraatu)) -
					 (sum(asaldobancohor.atrasoant) + sum(asaldobancohor.atrasoatu)+
					  sum(asaldobancohor.faltaant)  + sum(asaldobancohor.faltaatu))						as [Saldo]
					,datefromparts(@AnoP,@MesP,01)														as [Periodo]
					,aparcol.descricao																	as [Sindicato]
					,aperiodo.iniciomensal																as [Inicio]
					,aperiodo.fimmensal																	as [Fim]
					,pevento.descricao																	as [Evento]


			from pfunc
			  join abancohorfundetalhe
			   on	pfunc.codcoligada = abancohorfundetalhe.codcoligada
				and	pfunc.chapa = abancohorfundetalhe.chapa
			  join psecao 
			   on	pfunc.codcoligada = psecao.codcoligada
				and pfunc.codsecao = psecao.codigo
			  join pfuncao 
			   on	pfunc.codcoligada = pfuncao.codcoligada
				and pfunc.codfuncao = pfuncao.codigo
			  join aperiodo
			   on	aperiodo.codcoligada = abancohorfundetalhe.codcoligada
			  join gccusto
			   on	gccusto.codcoligada = psecao.codcoligada
				and	gccusto.codccusto = psecao.nrocencustocont
			  join	aparfun
			   on	aparfun.codcoligada = pfunc.codcoligada
				and	aparfun.chapa = pfunc.chapa
			  join	aevepcol
			   on	aevepcol.codcoligada = aparfun.codcoligada
				and	aevepcol.codparcol = aparfun.codparcol
				and	aevepcol.codevepto = abancohorfundetalhe.codevento
			  join	pevento
			   on	pevento.codcoligada = aevepcol.codcoligada
				and	pevento.codigo = aevepcol.codevebanco
			  join	aperiodo aperiodoant
			   on	aperiodoant.codcoligada = abancohorfundetalhe.codcoligada
				and	aperiodoant.mescomp = case when @MesP = 1 then 12 else @MesP-1 end
				and	aperiodoant.anocomp = case when @MesP = 1 then @AnoP-1 else @AnoP end
			  left outer join asaldobancohor 
			   on	asaldobancohor.codcoligada = pfunc.codcoligada
				and	asaldobancohor.chapa = pfunc.chapa
				and	asaldobancohor.inicioper >= aperiodoant.iniciomensal
				and	asaldobancohor.fimper <= aperiodoant.fimmensal
			  join	aparcol
			   on	aparcol.codcoligada = aparfun.codcoligada
				and aparcol.codigo = aparfun.codparcol


			where	abancohorfundetalhe.data >= aperiodo.iniciomensal
			  and	abancohorfundetalhe.data <= aperiodo.fimmensal
			  
			  ".$ft_chapa."
			  ".$ft_secao."
			  ".$qr_secao."
			  
			  and	aperiodo.mescomp = @MesP
			  and	aperiodo.anocomp = @AnoP
			  and	abancohorfundetalhe.codcoligada = @CodCol

			group by pfunc.codcoligada
					,pfunc.codfilial
					,psecao.descricao
					,gccusto.codccusto
					,gccusto.nome
					,pfunc.chapa
					,pfunc.nome
					,pfuncao.nome
					,pfunc.salario
					,abancohorfundetalhe.data
					,pevento.provdescbase
					,pevento.descricao
					,abancohorfundetalhe.valor
					,aparcol.descricao
					,aperiodo.iniciomensal
					,aperiodo.fimmensal
			
			)X
			GROUP BY 
				coligada,
				filial,
				secao,
				codcusto,
				centrodecusto,
				chapa,
				funcionario,
				funcao,
				sindicato,
				inicio,
				saldo,
				fim
			ORDER BY funcionario
		";
        $result = $this->dbrm->query($query);

        return  ($result)
                ? $result->getResultObject()
                : false;

    }

    public function geraRelatorioBH($request)
    {

        $ft_chapa = ($request['chapa'] != null) ? " and pfunc.chapa = '{$request['chapa']}' " : '';
        $ft_secao = ($request['codsecao'] != null) ? " and pfunc.codsecao = '{$request['codsecao']}' " : '';

        //-----------------------------------------
		// filtro das chapas que o lider pode ver
		//-----------------------------------------
        $mHierarquia = Model('HierarquiaModel');
		$objFuncLider = $mHierarquia->ListarHierarquiaSecaoPodeVer(false, false, true);
		$isLider = $mHierarquia->isLider();

		$filtro_chapa_lider = "";
		$filtro_secao_lider = "";
		$isGestorLider = false;
		if($isLider){
			$chapas_lider = "";
			$codsecoes = "";
			foreach($objFuncLider as $idx => $value){
				$chapas_lider .= "'".$objFuncLider[$idx]['chapa']."',";
			}
			$filtro_secao_lider = " pfunc.CHAPA IN (".substr($chapas_lider, 0, -1).") OR ";
			$isGestorLider = true;
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
			$filtro_secao_gestor = " pfunc.CODSECAO IN (".substr($codsecoes, 0, -1).") OR ";
			$isGestorLider = true;
		}
		//-----------------------------------------
		
		// monta o where das seções
		if($filtro_secao_lider != "" && $filtro_secao_gestor == "") $filtro_secao_lider = rtrim($filtro_secao_lider, "OR ");
		if($filtro_secao_lider == "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		if($filtro_secao_lider != "" && $filtro_secao_gestor != "") $filtro_secao_gestor = rtrim($filtro_secao_gestor, "OR ");
		$qr_secao = " AND (".$filtro_secao_lider." ".$filtro_secao_gestor.") ";

        if($request['rh']) $qr_secao = "";
		if(!$isGestorLider && $request['chapa'] == null){
			$qr_secao = " AND 1 = 2 ";
		}else{
			if($request['chapa'] != null) $qr_secao = "";
		}

		// $qr_secao = "";
		// $ft_chapa = " AND pfunc.chapa = '050002709'";

        $query = "
			
			select	 
				 pfunc.codcoligada																	as [coligada]
				,pfunc.codfilial																	as [filial]
				,psecao.descricao																	as [secao]
				,gccusto.codccusto																	as [codcusto]
				,gccusto.nome																		as [centrodecusto]
				,pfunc.chapa																		as [chapa]
				,pfunc.nome																			as [funcionario]
				,pfuncao.nome																		as [funcao]
				,pfunc.salario																		as [salario]
				,abancohorfundetalhe.data															as [data]
				,case when pevento.provdescbase in ('p','b') then pevento.descricao
					  else null
				 end																				as [eventopositivo]
				,case when pevento.provdescbase in ('p','b') then abancohorfundetalhe.valor
					  else null
				 end																				as [horaspositivasminutos]
				,case when pevento.provdescbase in ('p','b') then dbo.fn_hora(abancohorfundetalhe.valor)
					  else null
				 end																				as [horaspositivas]
				,case when pevento.provdescbase in ('d')	 then pevento.descricao
					  else null
				 end																				as [eventonegativo]
				,case when pevento.provdescbase in ('d')	 then abancohorfundetalhe.valor
					  else null
				 end																				as [horasnegativasminutos]
				,case when pevento.provdescbase in ('d')	 then dbo.fn_hora(abancohorfundetalhe.valor)
					  else null
				 end																				as [horasnegativas]
				,(sum(asaldobancohor.extraant)  + sum(asaldobancohor.extraatu)) -
				 (sum(asaldobancohor.atrasoant) + sum(asaldobancohor.atrasoatu)+
				  sum(asaldobancohor.faltaant)  + sum(asaldobancohor.faltaatu))						as [saldo]
				,datefromparts(2019,8,01)														as [periodo]
				,aparcol.descricao																	as [sindicato]
				,aperiodo.iniciomensal																as [inicio]
				,aperiodo.fimmensal																	as [fim]
				,pevento.descricao																	as [evento]


		from pfunc
		  join abancohorfundetalhe
		   on	pfunc.codcoligada = abancohorfundetalhe.codcoligada
			and	pfunc.chapa = abancohorfundetalhe.chapa
		  join psecao 
		   on	pfunc.codcoligada = psecao.codcoligada
			and pfunc.codsecao = psecao.codigo
		  join pfuncao 
		   on	pfunc.codcoligada = pfuncao.codcoligada
			and pfunc.codfuncao = pfuncao.codigo
		  join aperiodo
		   on	aperiodo.codcoligada = abancohorfundetalhe.codcoligada
		  join gccusto
		   on	gccusto.codcoligada = psecao.codcoligada
			and	gccusto.codccusto = psecao.nrocencustocont
		  join	aparfun
		   on	aparfun.codcoligada = pfunc.codcoligada
			and	aparfun.chapa = pfunc.chapa
		  join	aevepcol
		   on	aevepcol.codcoligada = aparfun.codcoligada
			and	aevepcol.codparcol = aparfun.codparcol
			and	aevepcol.codevepto = abancohorfundetalhe.codevento
		  join	pevento
		   on	pevento.codcoligada = aevepcol.codcoligada
			and	pevento.codigo = aevepcol.codevebanco
		  join	aperiodo aperiodoant
		   on	aperiodoant.codcoligada = abancohorfundetalhe.codcoligada
			and	aperiodoant.mescomp = case when ".$request['mescomp']." = 1 then 12 else ".$request['mescomp']."-1 end
			and	aperiodoant.anocomp = case when ".$request['mescomp']." = 1 then ".$request['anocomp']."-1 else ".$request['anocomp']." end
		  left outer join asaldobancohor 
		   on	asaldobancohor.codcoligada = pfunc.codcoligada
			and	asaldobancohor.chapa = pfunc.chapa
			and	asaldobancohor.inicioper >= aperiodoant.iniciomensal
			and	asaldobancohor.fimper <= aperiodoant.fimmensal
		  join	aparcol
		   on	aparcol.codcoligada = aparfun.codcoligada
			and aparcol.codigo = aparfun.codparcol


		where	abancohorfundetalhe.data >= aperiodo.iniciomensal
		  and	abancohorfundetalhe.data <= aperiodo.fimmensal
		  ".$ft_chapa."
		  ".$ft_secao."
		  and	aperiodo.mescomp = ".$request['mescomp']."
		  and	aperiodo.anocomp = ".$request['anocomp']."
		  ".$qr_secao."
		  and	abancohorfundetalhe.codcoligada = '".$_SESSION['func_coligada']."'

		group by pfunc.codcoligada
				,pfunc.codfilial
				,psecao.descricao
				,gccusto.codccusto
				,gccusto.nome
				,pfunc.chapa
				,pfunc.nome
				,pfuncao.nome
				,pfunc.salario
				,abancohorfundetalhe.data
				,pevento.provdescbase
				,pevento.descricao
				,abancohorfundetalhe.valor
				,aparcol.descricao
				,aperiodo.iniciomensal
				,aperiodo.fimmensal
		
		";
		// exit('<pre>'.$query);
        $result = $this->dbrm->query($query);

        return  ($result)
                ? $result->getResultObject()
                : false;

    }

}