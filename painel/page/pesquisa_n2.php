<?php

if(!$_SESSION) @session_start();

include('class/N.class.php');
include('class/LimitarConsulta.function.php');
include('class/limparNumero.function.php');
include('class/Token.class.php');

$token  = new Token(true);
$token = $token->get_token();

$con    = new Conexao();
$router = new Router($_GET['p']);

limitarConsulta($con, $_SESSION['usuario'], 'n2');

$contabilizar = true;

if($router->param(0) == 'cpf'){

	$contabilizar = false;
	if(strlen(limparNumero($router->param(1))) == 14){
		$_POST['type']   = 'cnpj';
		$_POST['cnpj']   = limparNumero($router->param(1));
	}else{
		$_POST['type']   = 'cpf';
		$_POST['cpf']   = limparNumero($router->param(1));
	}

}elseif($router->param(0) == 'telefone'){

	$contabilizar = false;
	$_POST['type']   = 'telefone';
	$_POST['telefone']   = limparNumero($router->param(1));

}elseif($router->param(0) == 'nome'){

	$contabilizar = false;
	$_POST['type']   = 'nome';
	$_POST['nome']   = $router->param(1);

}elseif(strlen(limparNumero($router->param(0))) >= 13){

	$cpf = limparNumero($router->param(0));

	if($_SESSION['autorizar'][$cpf] || $_SESSION['autorizar'][$cpf.'/'.$router->param(1)]){

		if(strlen($cpf) == 14){
			$_POST['type'] = 'cpf';
			$_POST['cpf']  = limparNumero($cpf);
		}else{
			$_POST['type'] = 'cnpj';
			$_POST['cnpj']  = $cpf.'/'.$router->param(1);
		}

		$contabilizar  = false;
	}

}elseif(strlen($router->param(0)) > 0 && $router->param(1) && $router->param(2)){

	// buscar vizinhos
	$_POST['type']   = 'cep';
	$_POST['cep']    = $router->param(0);
	$_POST['inicio'] = $router->param(1);
	$_POST['fim']    = $router->param(2);

	$contabilizar = false;

}



if($_POST['type']){

	$natt = new Natt($_POST['type']);

	$codigo = -2;

	if($contabilizar)
		$_SESSION['token_natt'] = $token;

	switch($_POST['type']){

		case 'cpf':
			$codigo = $natt->consultaCpf(limparNumero($_POST['cpf']));
			break;
		case 'cnpj':
			$codigo = $natt->consultaCnpj(limparNumero($_POST['cnpj']));
			break;
		case 'telefone':
			$codigo = $natt->consultaTelefone(limparNumero($_POST['telefone']));
			break;
		case 'nome':
			$array_ufs = $natt->consultaNome($_POST['nome']);
			$codigo = false;
			break;
		case 'cep':
			$array_nomes = $natt->consultaCep(limparNumero($_POST['cep']), $_POST['inicio'], $_POST['fim']);
			$codigo = false;
			break;
	}

}elseif($_POST['consulta_nome']){

	$natt = new Natt('nome');

	$_SESSION['token_natt'] = $token;

	$codigo = $natt->consultaNomeEstado($_POST['nome'], $_POST['estado']);

}

?>
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
						<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar

						</div>

					</div>
					<div class="panel-body">

						<?php
						//var_dump($_POST);

						if($array_nomes && count($array_nomes) > 0){

							if(!$_SESSION) @session_start();

							include('class/RegistrarConsulta.php');
							

							if($contabilizar || $_SESSION['token_natt'] != $token) $codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'n2');

						?>
						
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<td>CPF</td>
									<td>Nome</td>
									<td>Logradouro</td>
									<td>Número</td>
									<td>Complemento</td>
									<td>Bairro</td>
									<td>CEP</td>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach($array_nomes as $l){
									
									$_SESSION['autorizar'][$l['cpf']] = true; ?>

									<tr style="cursor:pointer;" onclick="window.open('<?php echo URL; ?>/pesquisa_n2/<?= $l['cpf'] ?>', '_blank');">
										<td><?= $l['cpf']; ?></td>
										<td><?= $l['nome']; ?></td>
										<td><?= $l['logradouro']; ?></td>
										<td><?= $l['numero']; ?></td>
										<td><?= $l['complemento']; ?></td>
										<td><?= $l['bairro']; ?></td>
										<td><?= $l['cep']; ?></td>
									</tr>
								<?php 
								} ?>
							</tbody>
						</table>


						<?php 

						}elseif($array_ufs){

							include('class/RegistrarConsulta.php');
							if($contabilizar || $_SESSION['token_natt'] != $token) $codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'n2');
							?>
							<div class="col-xs-4">
								<form method="post" action="<?php echo URL; ?>/pesquisa_n2">
									<input type="hidden" name="nome" value="<?php echo $_POST['nome']; ?>">
									<div class="form-group">
										<label for="">Selecione o Estado</label>
										<select name="estado" id="estado" class="form-control">
											<option value=""></option>
											<?php foreach($array_ufs as $uf=>$count){
												echo '<option value="'.$uf.'">'.$uf.' ('.$count.')</option>';
											} ?>
										</select>
									</div>
									<div class="form-group">
										<button name="consulta_nome" value="1" type="submit" class="btn btn-success">Selecionar</button>
									</div>
								</form>
							</div>
						<?php
						}elseif($codigo) {

							if(is_array($codigo)){

								if(count($codigo) == 0) echo "<p>Nada encontrado!</p>";
								else{ 
								//var_dump($codigo);
									if(!$_SESSION) @session_start();
									include('class/RegistrarConsulta.php');
							if($contabilizar  || $_SESSION['token_natt'] != $token) $codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'n2');

									?>
									<table class="table table-bordered table-hover">
										<thead>
											<tr>
												<td>CPF</td>
												<td>Nome</td>
												<td>Cidade</td>
											</tr>
										</thead>
										<tbody>
											<?php 
											$autorizar = array();
											foreach($codigo as $l){

												$_SESSION['autorizar'][$l['cpf']] = true;
											?>
											<tr style="cursor:pointer;" onclick="window.open('<?php echo URL; ?>/pesquisa_n2/<?= $l['cpf'] ?>', '_blank');">
												<td><?= $l['cpf']; ?></td>
												<td><?= $l['nome']; ?></td>
												<td><?= $l['cidade']; ?></td>
											</tr>
											<?php } ?>
										</tbody>
									</table>
									<?php
								}
							}else{
								echo $codigo;
								include('class/RegistrarConsulta.php');
								if($contabilizar  || $_SESSION['token_natt'] != $token) $codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'n2');
							}

						}elseif($codigo === false)
							echo "<p>Nada encontrado!</p>";
						else {
						?>

						<div class="col-xs-12">
							<!-- Nav tabs --><div class="card">
							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active"><a href="#cpf" aria-controls="cpf" role="tab" data-toggle="tab">CPF</a></li>
								<li role="presentation"><a href="#cnpj" aria-controls="cnpj" role="tab" data-toggle="tab">CNPJ</a></li>
								<li role="presentation"><a href="#telefone" aria-controls="telefone" role="tab" data-toggle="tab">Telefone</a></li>
								<li role="presentation"><a href="#nome" aria-controls="nome" role="tab" data-toggle="tab">Nome</a></li>
								<!-- <li role="presentation"><a href="#endereco" aria-controls="endereco" role="tab" data-toggle="tab">Endereço</a></li> -->
								<li role="presentation"><a href="#cep" aria-controls="cep" role="tab" data-toggle="tab">CEP</a></li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="cpf">
									<form action="" id="form_cpf" method="post">

										<input type="hidden" name="type" value="cpf">

										<div class="form-group col-sm-3">
											<label for="">CPF</label>
											<input type="text" name="cpf" id="cpf" class="form-control">
										</div>

										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar CPF</button>
										</div>


									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="cnpj">
									<form action="" id="form_cnpj" method="post">

										<input type="hidden" name="type" value="cnpj">

										<div class="form-group col-sm-3">
											<label for="">CNPJ</label>
											<input type="text" name="cnpj" id="cnpj" class="onlyNumbers form-control">
										</div>

										<div class="form-group col-lg-12">
											<button type="submit"  class="btn btn-primary">Buscar CNPJ</button>
										</div>


									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="telefone">
									<form action="" id="form_telefone" method="post">

										<input type="hidden" name="type" value="telefone">

										<div class="form-group col-sm-3">
											<label for="">Telefone</label>
											<input type="text" name="telefone" id="telefone" class="onlyNumbers form-control">
										</div>

										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Telefone</button>
										</div>


									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="nome">
									<form action="" id="form_nome" method="post">

										<input type="hidden" name="type" value="nome">

										<div class="form-group col-sm-3">
											<label for="">Nome</label>
											<input type="text" name="nome" id="nome" class="form-control">
										</div>

										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Nome</button>
										</div>


									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="cep">
									<form action="" id="form_nome" method="post">

										<input type="hidden" name="type" value="cep">

										<div class="form-group col-sm-3">
											<label for="">CEP</label>
											<input type="text" name="cep" id="cep" class="form-control">
										</div>

										<div class="form-group col-sm-3">
											<label for="">Início</label>
											<input type="text" name="inicio" id="inicio" class="form-control">
										</div>

										<div class="form-group col-sm-3">
											<label for="">Fim</label>
											<input type="text" name="fim" id="fim" class="form-control">
										</div>

										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar CEP</button>
										</div>


									</form>
								</div>


								
							</div>
						</div>
					</div>
					<?php } ?>
				</div>

			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->
<script>

	function consultarCep(bairro, cidade, cep, uf, a, b){

		var inicio = 0, fim = 100;

		var numb = a.match(/\d/g);
        numb = numb.join("");

        if (parseInt(numb) != "NaN") {
            var temp = numb
            inicio = parseInt(temp)-10;
            fim = parseInt(temp)+10;
        }

        window.open('<?php echo URL; ?>/pesquisa_n2/' + cep + '/' + inicio + '/' + fim, '_blank');

	}

	function consultarNome(n){

        window.open('<?php echo URL; ?>/pesquisa_n2/nome/' + n, '_blank');

	}

	function consultar(n){
		window.open('<?php echo URL; ?>/pesquisa_n2/cpf/' + n, '_blank');
	}

	function consultarParente(n){
		window.open('<?php echo URL; ?>/pesquisa_n2/cpf/' + n, '_blank');
	}

	function consultarTelefone(n){

        window.open('<?php echo URL; ?>/pesquisa_n2/telefone/' + n, '_blank');

	}

</script>