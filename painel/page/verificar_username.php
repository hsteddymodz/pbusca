<?php


if($_POST['codigo'] && !$_POST['usuario'])
	die(json_encode(array('n'=>0)));

if($_POST['usuario'] && $_POST['search']){



	include('../class/Conexao.class.php');

	$con    = new Conexao();

	$retorno = array();

	if($_POST['codigo'] > 0)

		$retorno['n'] = $con

					->select('count(*) as n')

					->from('usuario')

					->where("usuario = '".$con->escape($_POST['usuario'])."' and codigo != '".intval($_POST['codigo'])."' and deletado is null")

					->limit(1)->executeNGet('n');

	else

		$retorno['n'] = $con

					->select('count(*) as n')

					->from('usuario')

					->where("usuario = '".$con->escape($_POST['usuario'])."' and deletado is null")

					->limit(1)->executeNGet('n');



	die(json_encode($retorno));

}





?>