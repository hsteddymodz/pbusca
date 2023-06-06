<?php

include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');
include('onlyNumbers.function.php');
include('curl_get_contents.function.php');
include('simple_html_dom.php');

function validarDados($tipo, $dados) {

	if(!in_array($tipo, array('cpf', 'cnpj', 'telefone', 'email', 'nome', 'endereco')))
		return 'Tipo de pesquisa inválido';

	switch($tipo) {
		case 'cpf':
			$cpf = onlyNumbers($dados['cpf']);
			if(strlen($cpf) != 11)
				return 'CPF Inválido';
			$dados['cpf'] = $cpf;
			break;
		case 'cnpj':
			$cnpj = onlyNumbers($dados['cnpj']);
			if(strlen($cnpj) != 14)
				return 'CNPJ Inválido';
			$dados['cnpj'] = $cnpj;
			break;
		case 'telefone':
			$telefone = onlyNumbers($dados['telefone']);
			if(strlen($telefone) != 10 && strlen($telefone) != 11)
				return 'Telefone Inválido';
			$dados['telefone'] = $telefone;
			break;
		case 'email':
			if(!filter_var($dados['email'], FILTER_VALIDATE_EMAIL))
				return 'E-mail inválido';
			break;
		default: // nome ou endereco
			if(strlen($dados['dataNasc']) > 0) {
				list($day, $month, $year) = explode('/', $dados['dataNasc']);
				if(! checkdate ( $month, $day, $year ))
					return 'Data de Nascimento Inválida';
			}
			if(!empty($dados['sexo']) && !in_array($dados['sexo'], array('M', 'F')))
				return 'Sexo inválido';
			if(!empty($dados['uf']) && strlen($dados['uf']) != 2)
				return 'UF Inválida';
			if(!empty($dados['endOuCep']) && empty($dados['uf']))
				return 'Para pesquisar por rua, preencha também o UF';
			break;
	}

	return $dados;

}

function fixPaths($content) {

	$html = str_get_html($content);
	if($html && $html->find('link'))
		foreach($html->find('link') as $link) {
			$url = 'https://api.ultradev.info/Assertiva/ljv3t4hbmka33b/' . $link->getAttribute('href');
			$parsed_url = parse_url($url);
			$parsed_url['path'] = basename($parsed_url['path']);
			// if the response page changes, we need to update this
			/*if(!is_file('asapi/' . $parsed_url['path']) && in_array(substr($parsed_url['path'], strrpos($parsed_url['path'], '.')+1), array('css', 'js'))) {
				$conteudo = curl_get_contents($url);
				file_put_contents('asapi/' . $parsed_url['path'], $conteudo);
			}*/
			$link->setAttribute("href", "https://probusca.com/painel/class/asapi/" . $parsed_url['path']);
			$link->removeAttribute('integrity');
			$link->removeAttribute('crossorigin');
		}

	if($html && $html->find('script'))
		foreach($html->find('script') as $link) {
			if(!$link->getAttribute('src'))
				continue;
			$url = 'https://api.ultradev.info/Assertiva/ljv3t4hbmka33b/' . $link->getAttribute('src');
			$parsed_url = parse_url($url);
			$parsed_url['path'] = basename($parsed_url['path']);
			// if the response page changes, we need to update this
			/*if(!is_file('asapi/' . $parsed_url['path']) && in_array(substr($parsed_url['path'], strrpos($parsed_url['path'], '.')+1), array('css', 'js'))) {
				$conteudo = curl_get_contents($url);
				file_put_contents('asapi/' . $parsed_url['path'], $conteudo);
			}*/
			$link->setAttribute("src", "https://probusca.com/painel/class/asapi/" . $parsed_url['path']);
			$link->removeAttribute('integrity');
			$link->removeAttribute('crossorigin');
		}

	return '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">' . $html;

}

function AssertivaAPI($tipo, $dados) {

	$validarDados = validarDados($tipo, $dados);
	if(!is_array($validarDados))
		return array('error'=>$validarDados);
	$dados = $validarDados;

	$url = false;
	switch($tipo) {

		case 'cpf':
			$url = 'https://api.ultradev.info/Assertiva/ljv3t4hbmka33b/cpf?cpf=' . $dados['cpf'];
			break;
		case  'cnpj':
			$url = 'https://api.ultradev.info/Assertiva/ljv3t4hbmka33b/cnpj?cnpj=' . $dados['cnpj'];
			break;
		case  'telefone':
			$url = 'https://api.ultradev.info/Assertiva/ljv3t4hbmka33b/telefone?telefone=' . $dados['telefone'];
			break;	
		case 'email':
			$url = 'https://api.ultradev.info/Assertiva/ljv3t4hbmka33b/email?email=' . $dados['email'];
			break;	
		default:
			$url = 'https://api.ultradev.info/Assertiva/ljv3t4hbmka33b/nomeOuEnd?' . http_build_query($dados);
			break;
	}

	return curl_get_contents($url, 40);
}

if(!isset($_SESSION))
	@session_start();

if(isset($_POST) && isset($_POST['tipo']) && isset($_SESSION['usuario'])) {

	if(limitarConsulta(null, $_SESSION['usuario'], 'asapi', 1) <= 0)
		die("Créditos insuficientes"); 

	$resultado = AssertivaAPI($_POST['tipo'], $_POST);
	if(is_array($resultado) && isset($resultado['error']))
		die($resultado['error']);

	registrarConsulta(null, $_SESSION['usuario'], 'asapi');

	die(fixPaths($resultado));

}