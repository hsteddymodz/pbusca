<?php

$resultado_da_pesquisa = false;

// a pagina faz o POST pra si mesma
if($_POST['action'] == 'pesquisar' && !empty($_POST['token'])){

	include('../class/RegistrarConsulta.php');
	include('../class/Conexao.class.php');
	include('../class/onlyNumbers.function.php');
	include('../class/Token.class.php');

	// classe a seguir nao eixste, exemplo apenas
	include('../class/NomeDaClasseDoCrawler.class.php');

	$con = new Conexao();

	// validamos o token do usuario
	$token = new Token();
	$token = $token->get_token();

	if($token != $token_recebido)
		die(json_encode(array('error'=>array('Token Inválido!'))));

	// registra a consulta quando der certo
	registrarConsulta($con, $_SESSION['usuario'] , 'catta');

}

// classe de conexao ao banco de dados
include('class/Conexao.class.php');

// funcao que limpa uma string removendo tudo que nao é numero, ex: onlyNumbers("abcdjah123123asd"); retorna 123123. Util pra limpar CPFs e CNPJs
include('class/onlyNumbers.function.php');

include('class/LimitarConsulta.function.php');
include('class/Token.class.php');

$con = new Conexao();

// verificamos se o usuario ainda esta logado
if(!$_SESSION) @session_start();

// limita o numero de consultas
limitarConsulta($con, $_SESSION['usuario'], 'cs');

// geramos um novo token pro usuario
$token = new Token();
$token = $token->get_token();


?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa NOME_DA_PESQUISA</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa NOME_DA_PESQUISA </h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">
					
					<form action="" method="POST">
						<div id="remover" class="col-md-3">
							<div class="form-group">
								<label for="">CPF</label>
								<input type="text" name="cpf" class="form-control" placeholder="Digite um CPF">
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-primary">Pesquisar</button>
							</div>
						</div>
					</form>
					<div class="col-md-12" id="resultado">
						<?php if($resultado_da_pesquisa !== false) echo $resultado_da_pesquisa; ?>
					</div>

				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->