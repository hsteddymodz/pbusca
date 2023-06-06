<?php

include('../class/Conexao.class.php');
$c = new Conexao();

if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 4)
	die('Acesso Negado');

$tmp_admins = $c->select('*')->from('usuario')->where('1=1')->executeNGet();
$admins = array();
foreach($tmp_admins as $ad) {
	$admins[$ad['codigo']] = $ad['usuario'];
}
$users = $c->select('*')->from('usuario')->where('log IS NOT NULL')->executeNGet();

$usuarios_editados = array();
foreach($users as $u) {
	//var_dump($u['log']);
	if(strpos($u['log'], 'pelo usuario') === false) {
		$tmp = explode(',', $u['log']);
		$tmp = $tmp[1];
	} else {
		$tmp = substr($u['log'], strpos($u['log'], ' = ')+3, strrpos($u['log'], ' do')-strpos($u['log'], ' = ')-3);
	}

	if(!isset($usuarios_editados[$admins[$tmp]]))
		$usuarios_editados[$admins[$tmp]] = array();

	$usuarios_editados[$admins[intval($tmp)]][] = $u['usuario'];
	
}

foreach($usuarios_editados as $admin =>$arr) {
	echo "<p><b>$admin editou: </b>" . implode(', ', $arr) . "</p>";
} 

?>