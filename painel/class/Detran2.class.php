<?php

include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');
include('onlyNumbers.function.php');
include('curl_get_contents.function.php');
include('simple_html_dom.php');

function consulta_detran($dados) {

	$tipo = key($dados);
	if(!in_array($tipo, array('placa', 'renavam', 'chassi', 'cpf', 'cnh')))
		return array('error'=>"Tipo de pesquisa $tipo inválido");

	$url_consulta = 'https://api.ultradev.info/ISP/7763b1c6d49/';
	if(in_array($tipo, array('placa', 'renavam', 'chassi')))
		$url_consulta .= 'placa?';
	else if(in_array($tipo, array('cpf', 'cnh')))
		$url_consulta .= 'cnh?';
	else 
		return array('error'=>'Dados inválidos');

	$url_consulta .= http_build_query($dados);

	return curl_get_contents($url_consulta);

}

function fixPathsNaturalidade($content) {
	$html = str_get_html($content);
	$k = 0;
	$resultado = '';
	foreach($html->find('.table tbody tr') as $tr) {
		if($k == 4 || $k == 1 || $k == 2)
			$resultado .= "<p>{$tr->outertext}</p>";
		$k++;
	}
	return $resultado;
}

function fixPaths($content) {

	$html = str_get_html($content);
	if($html->find('link'))
		foreach($html->find('link') as $link) {
			$url = 'https://api.ultradev.info/ISP/7763b1c6d49/' . $link->getAttribute('href');
			$parsed_url = parse_url($url);
			$parsed_url['path'] = basename($parsed_url['path']);
			// if the response page changes, we need to update this
			/*if(!is_file('detran/' . $parsed_url['path']) && in_array(substr($parsed_url['path'], strrpos($parsed_url['path'], '.')+1), array('css', 'js'))) {
				$conteudo = curl_get_contents($url);
				//echo 'copying ' . $url;
				file_put_contents('detran/' . $parsed_url['path'], $conteudo);
			}*/
			$link->setAttribute("href", "https://probusca.com/painel/class/detran/" . $parsed_url['path']);
			$link->removeAttribute('integrity');
			$link->removeAttribute('crossorigin');
		}

	if($html->find('script'))
		foreach($html->find('script') as $link) {
			if(!$link->getAttribute('src'))
				continue;
			$url = 'https://api.ultradev.info/ISP/7763b1c6d49/' . $link->getAttribute('src');
			$parsed_url = parse_url($url);
			$parsed_url['path'] = basename($parsed_url['path']);
			// if the response page changes, we need to update this
			/*if(!is_file('detran/' . $parsed_url['path']) && in_array(substr($parsed_url['path'], strrpos($parsed_url['path'], '.')+1), array('css', 'js'))) {
				$conteudo = curl_get_contents($url);
				//echo 'copying ' . $url;
				file_put_contents('detran/' . $parsed_url['path'], $conteudo);
			}*/
			$link->setAttribute("src", "https://probusca.com/painel/class/detran/" . $parsed_url['path']);
			$link->removeAttribute('integrity');
			$link->removeAttribute('crossorigin');
		}

	return '<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">' . $html;

}

if(!isset($_SESSION))
	@session_start();

if(isset($_POST) && count($_POST) > 0 && isset($_SESSION['usuario'])) {

	$tipo = key($_POST);
	$modulo = false;
	if(in_array($tipo, array('placa', 'renavam', 'chassi')))
		$modulo = 'buscacar';
	else if(in_array($tipo, array('cpf', 'cnh')))
		$modulo = 'procnh';
	else if(in_array($tipo, array('naturalidade'))) {
		$modulo = 'natural';
		$_POST['cpf'] = $_POST['naturalidade'];
		unset($_POST['naturalidade']);
	}else
		die("<h1>Requisição inválida $tipo</h1>"); 

	// lembrar de registrar a plataforma detran2
	if(limitarConsulta(null, $_SESSION['usuario'], $modulo, 1) <= 0)
		die("<h1>Créditos insuficientes</h1>"); 

	$content = consulta_detran($_POST);
	if((is_array($content) && isset($content['error'])) || strpos($content, 'Ocorre um erro') !== false) // consulta falhou
		die('<h1>Nenhuma informação encontrada.</h1>');

	registrarConsulta(null, $_SESSION['usuario'], $modulo);
	if($modulo == 'natural')
		die(fixPathsNaturalidade($content));

	die(fixPaths($content));
	
}
?>