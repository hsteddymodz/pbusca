<?php
function kickarOutrosUsuarios($user, $sessao, $url, $lastAction = false){

	$tempoLogout = 2; // quantidade de horas para deslogar
	if($lastAction){
		if((time()-$lastAction) > ($tempoLogout * 60*60)){
			unset($_SESSION);

			$con->execute("UPDATE login set 
		ultima_atividade = '".date('Y-m-d H:i:s')."', 
		data_logout = '".date('Y-m-d H:i:s')."' 
		where usuario = '".$user."' 
		and data_logout IS NULL");
			die("<script>alert('Voce foi deslogado por inatividade. Entre novamente, por favor.'); location.href='".URL_INDEX."';</script>");
		} 
	}

	@session_start();

	include('Conexao.class.php');
	$con = new Conexao(true);

	$data_logout = $con
					->select('*')
					->from('login')
					->where("usuario = '$user' and codigo = '$sessao'")
					->limit(1)
					->executeNGet();

	
	$expire      = $con->select('vencimento, deletado, inativo')->from('usuario')->where("codigo = '$user'")->limit(1)->executeNGet();

	if($expire['inativo']){
		unset($_SESSION);
		$con->execute("UPDATE login set 
		ultima_atividade = '".date('Y-m-d H:i:s')."', 
		data_logout = '".date('Y-m-d H:i:s')."' 
		where usuario = '".$user."' 
		and data_logout IS NULL");
		die("<script>alert('Sua conta foi desativada. Solicite a ativação aos administradores.'); location.href='".URL_INDEX."';</script>");
	}

	if($expire['vencimento']){
		if((strtotime($expire['vencimento'])) < time()){
			unset($_SESSION);
			$con->execute("UPDATE login set 
		ultima_atividade = '".date('Y-m-d H:i:s')."', 
		data_logout = '".date('Y-m-d H:i:s')."' 
		where usuario = '".$user."' 
		and data_logout IS NULL");
			die("<script>alert('Sua conta expirou. Solicite a renovação aos administradores.'); location.href='".URL_INDEX."';</script>");
		}
	}

	if($expire['deletado']){
		unset($_SESSION);
		$con->execute("UPDATE login set 
		ultima_atividade = '".date('Y-m-d H:i:s')."', 
		data_logout = '".date('Y-m-d H:i:s')."' 
		where usuario = '".$user."' 
		and data_logout IS NULL");
		die("<script>alert('Sua conta foi deletada. Solicite a renovação aos administradores.'); location.href='".URL_INDEX."';</script>");
	}

	if($data_logout['data_logout']){

		$ip_novo = $con
		->select('ip')
		->from('login')
		->where("usuario = '$user' and data_logout is null")
		->orderby('codigo DESC')
		->limit(1)
		->executeNGet('ip');

		$con->insert('login_duplo',
			array(
				'usuario'=>$user,
				'data'=>'NOW()',
				'ip_saiu'=>$data_logout['ip'],
				'ip_entrou'=>$ip_novo
			)
		);
		unset($_SESSION);
		
		die("<script>alert('Sua conta foi usada para logar em outro local. Você será deslogado agora.'); location.href='".URL_INDEX."';</script>");
	
	}

	return;

}
?>