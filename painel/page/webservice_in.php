<?php

function pegarValores($arr){

	$final = array();

	$k = 0;
	foreach($arr as $texto){

		$regex= '#name="(.*?)"#s';
		$code = preg_match_all($regex, $texto, $res);

		$regex= '#value="(.*?)"#s';
		$code = preg_match_all($regex, $texto, $val);

		if($k == 8)
			$campo_input = 'vlbasecalc';
		else
			$campo_input = $res[1][0];

		switch($campo_input){

			case 'nb':
				$campo = "Número do Benefício";
				break;

			case 'nome':
				$campo = "Nome do Segurado";
				break;

			case 'especie':
				$campo = "Espécie";
				break;

			case 'pagamentoatravesde':
				$campo =  "Pagamento através de";
				break;

			case 'situacao':
				$campo = "Situação";
				break;

			case 'possuirepresentantelegal':
				$campo = "Possui representante legal / procurador?";
				break;

			case 'pa':
				$campo = "É pensão alimentícia?";
				break;

			case 'bloqemp':
				$campo = "Bloqueado para empréstimo?";
				break;

			case 'valormr':
				$campo = "Valor da MR";
				break;

			case 'vlbasecalc':
				$campo = "Base de cálculo da margem consignável";
				break;

			

		}
		
		$final[$k]['valor'] = $val[1][0];

		if($k == 8)
			$final[$k]['valor'] = $val[1][1];

		$final[$k]['nome_input'] = $campo_input;
		$final[$k]['nome'] = $campo;

		$k ++;

	}

	return $final;

}

function get_data($inss){

	$url_get = 'http://45.35.154.168/hiscon/hiscon_online.php?login=fontes&pass=123456&nb=' . $inss;

	echo $url_get;

	$url  = file_get_contents($url_get);

	// verifica se ha saldo
	$regex= '#<pre><center><h1>(.*?)</h1></center></pre>#s';
	$code = preg_match_all($regex, $url, $matches);

	if(strtolower($matches[1][0]) == 'Usuário ou senha não localizado!'){
		echo 'usuario_invalido';
		return;
	}

	if(strtolower($matches[1][0]) == 'sem saldo suficiente')
	{
		mail('probuscadobrasil@gmail.com', "SEM SALDO", "Olá. O Sistema PROBusca esta sem saldo par aconsultas no INSS.");
		echo "sem_saldo";
		return;
	}

	$regex= '#<td colspan="4"><input(.*?)></td>#s';
	$code = preg_match_all($regex, $url, $matches);

	$res = pegarValores($matches[1]);

	$regex= '#<table width="1200" (.*?)</table>#s';
	$recode = preg_match_all($regex, $url, $matches);

	$tabelas = $matches[0];

	if($tabelas && $res){

		include('class/Conexao.class.php');
	    $con = new Conexao();
	    include('class/RegistrarConsulta.php');
		$codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'in');
		
		foreach($res as $campos){
			echo "➜<b> ".$campos['nome'].":</b> ".$campos['valor']." <br>";
		}

		echo "<hr>";

		echo "<h2>Empréstimos Bancários</h2>";

		echo $tabelas[0];

		echo "<h2>Reserva de Margem para Cartão de Crédito</h2>";

		echo $tabelas[1];

		echo "<h2>Descontos de Cartão de Crédito</h2>";

		echo $tabelas[2];
	    
	}else{
		echo 'Nada encontrado!';
	}

}



?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Resultados da Pesquisa</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Resultados de Pesquisa</h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>
							
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">
					<?php 
					if($_POST['inss'] || $_GET['inss']){

							get_data((($_GET['inss'])? $_GET['inss'] : $_POST['inss']));

						}else{

							echo 'Dados faltando!';

						}

					?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->
<style>
	table, table tr, table tr td{
		border:1px solid black;
	}
</style>