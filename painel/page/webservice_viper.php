<?php
error_reporting(1);


if($_POST['viper_info']){
	//username and password of account
	$username = trim("agile");
	$password = trim("102030");
	$cpf = $_POST['viper_info'];
	$tipo = $_POST['tipo'];

	//set the directory for the cookie using defined document root var
	$dir = DOC_ROOT."/ctemp";
	//build a unique path with every request to store 
	//the info per user with custom func. 
	$path = ($dir);

	//login form action url
	$url="http://sis21.viperconsig.com.br/acesso/login"; 
	$postinfo = "LoginForm[username]=".$username."&LoginForm[password]=".$password;

	$cookie_file_path = $path."/cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	//set the cookie the site has for certain features, this is optional
	curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
	curl_setopt($ch, CURLOPT_USERAGENT,
	    "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
	curl_exec($ch);

	// CURL AGAIN
	curl_setopt($ch, CURLOPT_URL, "http://sis21.viperconsig.com.br/Pesqext/pesq");

	if($tipo == 'inss'){
		$string = "compe=201702&status=consulta&cpf_nb=" . $cpf;
	}else if($tipo == 'siape'){
		$string = "cpf_matricula=" . $cpf;
	}else if($tipo == 'exercito'){
		$string = "cpf_prec=" . $cpf;
	}else if($tipo == 'aeronautica'){
		$string = "cpf_ordem=" . $cpf;
	}else
		$string = "compe=201702&status=consulta&cpf_nb=" . $cpf;


	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $string);

	//do stuff with the info with DomDocument() etc
	$html = curl_exec($ch);

	//echo $html;

	$regex= '#<div class="col-md-11">(.*?)<!-- FIM DADOS PRINCIPAIS -->#s';
	$code = preg_match_all($regex, $html, $res);

	curl_close($ch);


	include('class/Conexao.class.php');
	$con = new Conexao();
	include('class/RegistrarConsulta.php');
		$codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'viper');

	$eliminar = array(
		'#<div>(.*?)busca_cad_novo()(.*?)</button>(.*?)</div>#s',
		'#<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>#s',
		'#<p><button class="btn btn btn-primary" onclick="print_ext(.*?)</p>#s',
		'#<!-- BOTOES EXTRATOS -->(.*?)<!-- FIM BOTOES -->#s',
		'#<!-- INI SIMULADOR -->(.*?)<!-- FIM SIMULADOR -->#s', 
		'#<button type="button" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="" data-original-title="Histórico de extratos salvos">EXTRATOS ANTERIORES</button>#s',
		'#<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">(.*?)</button>#s',
		'#<div class="alert bg-danger alert-dismissable">(.*?)BASE OFFLINE(.*?)</div>#s',
		'#<h3 class="box-title" align="center">(.*?)</h3>#s',
		'#<h3 class="box-title">DADOS(.*?)IO(.*?)</h3>#s');

}

?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Resultado da Consulta</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Resultado da Consulta</h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button onclick="location.href='<?php echo URL; ?>';" type="button" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>
							<?php if($_POST['viper_info']){ ?>
							<button type="button" class="btn btn-xs btn-primary" onclick="location.href='<?php echo URL; ?>/webservice_viper';">Realizar nova Pesquisa</button>
							<?php } ?>
						</div>
					</form>
				</div>
				<div class="panel-body">
					<?php 

					if($_POST['viper_info']){


						if($res[1][0]){

							$final = $res[1][0];
							foreach($eliminar as $e){
								$final = preg_replace($e, '', $final); 
							}
							echo $final;
							
						}else echo "<p>Nenhum registro encontrado para <b>".$_POST['viper_info']."</b></p>";

					}else{

					?>
					<form action="" method="post">
							
						<div class="form-group col-lg-4">
							<p>Ferramentas:</p>
							<label><input name="tipo" required onchange="change_ferramenta();" value="inss" type="radio"> INSS</label><br>
							<label><input name="tipo" required onchange="change_ferramenta();" value="siape" type="radio"> SIAPE</label><br>
							<label><input name="tipo" required onchange="change_ferramenta();" value="exercito" type="radio"> Exército</label><br>
							<label><input name="tipo" required onchange="change_ferramenta();" value="aeronautica" type="radio"> Aeronáutica</label><br>
						</div>

						<div class="form-group col-lg-4">
				        	<label id="titulo">CPF</label>
				        	<input type="text" name="viper_info" required id="viper_info" class="form-control">
				        	<br>
				        	<p>
				        		<button type="submit" class="btn btn-default">Pesquisar</button>
				        	</p>
				        </div>

					</form>


					<?php

					}

					?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->
<script>
	function change_ferramenta(){

		var opcao_selecionada = $('input[name=tipo]:checked').val(), titulo = $('#titulo');

		console.log(opcao_selecionada);

		if(opcao_selecionada){

			switch(opcao_selecionada){

				case 'inss':
				titulo.html('CPF ou NB'); break;
				case 'siape':
				titulo.html('CPF ou Matrícula'); break;
				case 'exercito':
				titulo.html('CPF ou PREC-CP'); break;
				case 'aeronautica':
				titulo.html('CPF ou N.ORDEM'); break;
				default:
				titulo.html('CPF'); break;

			}
			

		}

	}
</script>