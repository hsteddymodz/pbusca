<?php 

$usuario	= "08309138660";
$senha		= "3928028429034";

if(!isset($_SESSION))
	@session_start();

include('../class/LimitarConsulta.function.php');
include('../class/curl_get_contents.function.php');
include('../class/RegistrarConsulta.php');

if(!isset($_SESSION['usuario']))
	die();

if(limitarConsulta(null, $_SESSION['usuario'], 't', 1) <= 0)
	die(json_encode(array('erro'=>1, 'msg'=>"Créditos insuficientes!")));

if($_POST['tracker']) {
	$url = http_build_query($_POST);
	//$con->insert('usuario_consulta', array('usuario'=>$_SESSION['usuario'], 'plataforma'=>'t', 'data'=>'NOW()'));
	registrarConsulta(null, $_SESSION['usuario'], 't');
	echo curl_get_contents("https://www.trackear.xyz/api/usr={$_SESSION['usuario']}&modulo=tracker&usuario={$usuario}&senha={$senha}&".$url);
} elseif ($_POST['trackerpop']) {
	extract($_POST);
	registrarConsulta(null, $_SESSION['usuario'], 't');
	if($idp) {
		$cod = "idp=".$idp;
	} elseif ($idj) {
		$cod = "idj=".$idj;
	}
	echo curl_get_contents("https://www.trackear.xyz/api/usr={$_SESSION['usuario']}&modulo=trackerpop&usuario={$usuario}&senha={$senha}&".$cod);
} elseif ($_POST['qualcep']) {
	unset($_POST['qualcep']);
	$url = http_build_query($_POST);
	registrarConsulta(null, $_SESSION['usuario'], 't');
	echo curl_get_contents("https://www.trackear.xyz/api/usr={$_SESSION['usuario']}&modulo=qualcep&usuario={$usuario}&senha={$senha}&".$url);
} 
die();
?>