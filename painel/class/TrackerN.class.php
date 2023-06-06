<?php

include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');
include('onlyNumbers.function.php');
include('curl_get_contents.function.php');
include('simple_html_dom.php');
include('Tracker.class.php');

if(!isset($_SESSION))
	@session_start();

if(isset($_SESSION['usuario']) && isset($_POST) && count($_POST) > 0) {

	if($_POST['tracker'] == 1) {

		if(limitarConsulta(null, $_SESSION['usuario'], 't', 1) <= 0)
			die('{"error":"Créditos insuficientes"}'); 

		if(!empty($_POST['doc']))
			$arr_dados = array(
				'tipo' => 'telefone',
				'cpf' => $_POST['doc']
			);
		else if(!empty($_POST['cnpj']))
			$arr_dados = array(
				'tipo' => 'empresa',
				'cnpj' => $_POST['cnpj']
			);
		else if(!empty($_POST['idp'])) {
			$arr_dados = array(
				'tipo' => 'idpf',
				'id' => $_POST['idp']
			);
		} else if(!empty($_POST['idj'])) 
			$arr_dados = array(
				'tipo' => 'idpj',
				'id' => $_POST['idj']
			);
		else
			die('{"error":"Requisição inválida."}');

		$resultado = do_consulta($arr_dados);

		if(isset($_POST['idj']) || isset($_POST['idp']))
			registrarConsulta(null, $_SESSION['usuario'], 't');

		die($resultado);

	} else {

		// lembrar de registrar a plataforma detran2
		if(limitarConsulta(null, $_SESSION['usuario'], 't2', 1) <= 0)
			die('{"error":"Créditos insuficientes"}'); 

		$resultado = do_consulta($_POST);

		if(isset($_POST['id']) && isset($_POST['tipo']) && ($_POST['tipo'] == 'idpj' || $_POST['tipo'] == 'idpf') && count($_POST) == 2)
			registrarConsulta(null, $_SESSION['usuario'], 't2');

		die($resultado);

	}

} else if(!isset($_SESSION['usuario']))
	die('{"error":"Credenciais inválidas! Faça Login."}');

?>