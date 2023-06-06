<?php
include('../class/simple_html_dom.php');
include('../class/Conexao.class.php');
include('../class/onlyNumbers.function.php');
/* variaveis de ambiente */

$con         = new Conexao();

if(!$_SESSION) @session_start();

$token = $_GET['token'];
$html = new simple_html_dom();
$resultado = file_get_contents("http://vuxtru.com/class/infben1.php?login=probusca&senha=QpKLyExF&nb=" . $_SESSION['autorizar_impressao'][$token]);


if(strpos($resultado, 'ERRO') === false){

	

}else{
	$resultado = -2;
}

if($resultado){
	echo '<meta charset="utf-8">';
	echo '<link href="https://probusca.com/painel/css/bootstrap.min.css" rel="stylesheet"><link href="https://probusca.com/painel/css/boavista.css" rel="stylesheet">
	
	<link href="https://probusca.com/painel/css/styles.css" rel="stylesheet">
	<style>
	body{background-color:white; max-width:1000px;}
	</style><div><img src="https://probusca.com/img/logo.png" height="150" alt=""><h1>Resultado da Consulta</h1>';
	echo $resultado;

}  
?>
