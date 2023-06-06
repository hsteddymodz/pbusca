<?php
include('../class/simple_html_dom.php');
include('../class/Conexao.class.php');
include('../class/onlyNumbers.function.php');
/* variaveis de ambiente */

$con         = new Conexao();

if(!$_SESSION) @session_start();

$token = $_GET['token'];
$html = new simple_html_dom();
$resultado = file_get_contents("http://vuxtru.com/class/hiscon1.php?login=probusca&senha=QpKLyExF&nb=" . $_SESSION['autorizar_impressao'][$token]);
$html->load($resultado);

if(strpos($html->find('h2', 0)->outertext, 'Por favor insira um nb valido') === false){

	$html->find('.rodape',0)->outertext = '';
	$html->find('.botao', 0)->parent()->parent()->parent()->outertext = '';
	foreach($html->find('.tabela') as $th){
		$th->class ='tabela table table-responsive';
	}
	$resultado = $html->find('.tabelaGrande',0)->outertext;

}else{
	$resultado = -2;
}

if($resultado){
	echo '<meta charset="utf-8">';
	echo '<link href="https://probusca.com/painel/css/bootstrap.min.css" rel="stylesheet"><link href="https://probusca.com/painel/css/boavista.css" rel="stylesheet">
	
	<link href="https://probusca.com/painel/css/styles.css" rel="stylesheet">
	<style>
	body{background-color:white; max-width:1000px;}

	.bct {
		font-family: Arial;
		font-size: 9px;
		color: #999999;
		padding-left: 15px;

	}

	.bct a:link {
		color: #999999;
		text-decoration: none;

	}

	.bct a:hover {
		color: #999999;
		text-decoration: underline;

	}

	.bct a:active {
		color: #999999;
		text-decoration: none;

	}
	.bct a:visited {
		color: #999999;
		text-decoration: none;

	}.usuario {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		text-align: right;
		padding-right: 15px;


	}
	.titulopag {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		color: #003366;
		font-weight: bold;
	}
	.textoImperativo {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		padding-left: 10px;
		background-image: url(t_seta.gif);
		background-repeat: no-repeat;
		padding-bottom: 5px;
		padding-top: 5px;



	}
	.textoImperativo a:link {
		color: #003366;
		font-size: 11px;

	}
	.textoImperativo a:hover {
		color: #003366;
		font-size: 11px;

	}
	.textoImperativo a:visited {
		color: #003366;
		font-size: 11px;
	}
	.textoImperativo a:active {
		color: #003366;
		font-size: 11px;
	}
	.campo {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10px;
		color: #000000;
		border: 1px solid #003366;
		margin: 3px;
		font-weight: bold


	}

	.label {
		text-align: right;
		padding-right: 5px;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
	}

	.labelTitle {
		text-align: left;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		font-weight: bold;
	}

	select {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 10px;
		color: #000000;
		border: 1px solid #003366;
	}


	.tabelaGrande {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		padding: 10px;


	}
	.botaoIntuitivo {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
		color: #FFFFFF;
		background-color: #669966;
		border: 1px solid #003366;
		height: 22px;


	}
	.botao {

		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
		color: #669966;
		background-color: #FFFFFF;
		border: 1px solid #003366;
		height: 22px;
	}
	.botaodesabilitado {


		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		font-weight: bold;
		color: #bbbbbb;
		background-color: #FFFFFF;
		border: 1px solid #003366;
		height: 22px;
	}
	.textosimples {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		text-align: left;

	}
	.msgDica {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		padding-left: 25px;
		background-image: url(msg_verde.gif);
		background-repeat: no-repeat;
		padding-bottom: 8px;
		padding-top: 8px;
		background-position: left top;

	}
	.msgLembrete {

		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		padding-left: 25px;
		background-image: url(msg_azul.gif);
		background-repeat: no-repeat;
		padding-bottom: 8px;
		padding-top: 8px;
		background-position: left top;
	}
	.msgAtencao {


		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		padding-left: 25px;
		background-image: url(msg_amarela.gif);
		background-repeat: no-repeat;
		padding-bottom: 8px;
		padding-top: 8px;
		background-position: left top;
	}
	.msgErro {
		font-family: Arial;
		font-size: 11px;
		color: #882222;
		padding-left: 25px;
		background-image: url(msg_vermelha.gif);
		background-repeat: no-repeat;
		padding-bottom: 8px;
		padding-top: 8px;
		background-position: left top;
	}
	.msgErro a:link {
		color: #993333;
	}
	.msgErro a:hover {
		color: #993333;
	}
	.msgErro a:active {
		color: #993333;
	}
	.msgErro a:visited {
		color: #993333;
	}
	.tabela {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #000000;
		text-align: left;
		border-bottom-width: 1px;
		border-bottom-style: solid;
		border-bottom-color: #003366;
		padding-left: 5px;
		padding-right: 5px;


	}
	.tabela th {
		font-size: 11px;
		color: #ffffff;
		text-align: left;
		background-image: url(tab_th_bg.gif);
		background-repeat: repeat-x;
		height: 18px;
		background-color: #669966;
		font-weight: bold;
		padding-left: 5px;
	}
	.tabela th a:link {
		color: #ffffff;
		text-align: center;
		height: 18px;
		font-weight: bold;
		text-decoration: none;
	}
	.tabela th a:hover {
		color: #ffffff;
		text-align: center;
		height: 18px;
		font-weight: bold;
		text-decoration: underline;
	}
	.tabela th a:visited {
		color: #ffffff;
		text-align: center;
		height: 18px;
		font-weight: bold;
		text-decoration: none;
	}
	.tabela th a:active {
		color: #ffffff;
		text-align: center;
		height: 18px;
		font-weight: bold;
		text-decoration: none;
	}
	.tabela tr.even  {
		height: 17px;
		background-color: #f7f7f7;
	}
	.tabela tr.even a:hover  {
		height: 17px;
		background-color: #886633;
	}
	.tabela tr.odd  {
		height: 17px;
		background-color: #ebebeb;
	}
	.labelObrigatorio {

		text-align: right;
		padding-right: 5px;
		font-family: Arial, Helvetica, sans-serif;
		font-size: 11px;
		color: #882222;
	}
	.paginacao {
		font-family: Arial;
		font-size: 11px;
		color: #003366;
		height: 20px;
		text-align: right;
		background-position: top;

	}
	.paginacao a:link {
		color: #003366;
		text-decoration: none;
	}
	.paginacao a:hover {
		color: #003366;
		background-color: #88bb88;
		text-decoration: underline;
	}
	.paginacao a:visited {
		color: #003366;
		text-decoration: none;
	}
	.paginacao a:active {
		color: #003366;
		text-decoration: none;
	}

	</style><div><img src="https://probusca.com/img/logo.png" height="150" alt=""><h1>Resultado da Consulta</h1>';
	echo $resultado;

}  
?>
