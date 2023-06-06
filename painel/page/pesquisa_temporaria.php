<?php
//

include('class/Natt.class.php');

$con = new Conexao();
$router = new Router($_GET['p']);
if(!$_SESSION) @session_start();

include('class/LimitarConsulta.function.php');
limitarConsulta($con, $_SESSION['usuario'], 'n');

if($_POST['type'])
	$natt = new Natt($_POST['type'], $_POST);
else
	$natt = false;

?>
<style>
	.titulo{
		background-color:#eaeaea;
		font-weight: bold;
		text-align:center;
	}
	.sub_titulo{
		font-weight: bold;
	}
</style>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa </li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa </h1>
		</div>
	</div><!--/.row-->
	
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">

						<div class="form-group form-inline">
							<button type="button"  name="enviar" value="true" onclick="location.href='<?php echo URL; ?>/pesquisa_n';" class="btn btn-primary btn-xs">
								<i class="glyphicon glyphicon-back"></i> Pesquisar Novamente
							</button>

						</div>

					</div>
					<div class="panel-body">
						<table class="table table-bordered">
							<tr>
								<td colspan="4" class="titulo">Informações Pessoais</td>
							</tr>
							<tr>
								<td class="sub_titulo">Nome</td>
								<td>{nome_do_consultado}</td>
								<td class="sub_titulo">CPF</td>
								<td>{cpf}</td>
							</tr>
							<tr>
								<td class="sub_titulo">Mâe</td>
								<td>{mae}</td>
								<td class="sub_titulo">Nascimento</td>
								<td>{nascimento}</td>
							</tr>
							<tr>
								<td class="sub_titulo">Sexo</td>
								<td>{sexo}</td>
								<td class="sub_titulo">E-mail</td>
								<td>{email}</td>
							</tr>
							<tr>
								<td colspan="4"></td>
							</tr>
						</table>
					</div>

			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->