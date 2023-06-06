
<?php

include('../class/Conexao.class.php');
include('../class/onlyNumbers.function.php');
/* variaveis de ambiente */
$nome_p      = "Consultas Salvas";
$con         = new Conexao();

$pesq = onlyNumbers($_GET['pesq']);
if($pesq){

	$dados       = $con->select('resultado')->from('consultas_salvas')->where("cpf = '$pesq'")->limit(1)->executeNGet('resultado');
	$conteudo = $dados;

}else
	$conteudo = false;

if($conteudo){
	echo '<meta charset="utf-8">';
	echo '<link href="https://probusca.com/painel/css/boavista.css" rel="stylesheet">
	<link href="https://probusca.com/painel/css/styles.css" rel="stylesheet">
	<style>
	body{background-color:white; max-width:1000px;}
	</style><div><img src="https://probusca.com/img/logo.png" height="150" alt=""><h1>Resultado da Consulta</h1>';
	echo $conteudo;

}  
?>
