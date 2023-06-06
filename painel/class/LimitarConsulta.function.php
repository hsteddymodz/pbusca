<?php

if(!function_exists('limitarConsulta')) {
	function limitarConsulta($c, $u, $p_tipo, $retornar_credito = false){	

		if(!$_SESSION) @session_start();

		if(!isset($_SESSION['usuario'])){
			unset($_SESSION);
			die("<script>location.href='https://probusca.com';</script>");
		}

		if($_SESSION['usuario'] == '2607' or $_SESSION['usuario'] == '8305' or $_SESSION['usuario'] == '8222'){
			if($retornar_credito) return 10;
			else return;
		}

		include('Credito.class.php');
		include('Conexao.class.php');

		$con = new Conexao();
		$credito = $con
			->select("numero  as credito")
			->from('plano_plataforma')
			->where("plano = (select plano from usuario where codigo = '".$u."') and plataforma in (select codigo from plataforma where tipo = '".$p_tipo."')")
			->orderby('rel_codigo desc')
			->limit(1)
			->executeNGet('credito');

		$debito  = $con
			->select("count(*) as debito")
			->from('usuario_consulta')
			->where("usuario = '".$u."' and plataforma = '".$p_tipo."' and date(data) = date(NOW())")
			->limit(1)
			->executeNGet('debito');
			
		$CRED = new Credito($con);
		//

		$n_consultas = intval($credito) - intval($debito);
		$n_consultas_geral = $CRED->get_num_creditos($p_tipo, $u);

		$con->close();
		if($retornar_credito)
			return intval($n_consultas + $n_consultas_geral);

		if(intval($n_consultas + $n_consultas_geral) > 0 ){
			return;
		}else{
			die("<script>
				alert('Número máximo de consultas já atingido.'); 
				location.href='https://probusca.com/painel';
			</script>");
		}

	}
}
?>