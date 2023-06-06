<?php
//

include('class/Natt.class.php');
include('class/RegistrarConsulta.php');
include('class/LimitarConsulta.function.php');

$router = new Router($_GET['p']);
if(!$_SESSION) @session_start();

limitarConsulta(null, $_SESSION['usuario'], 'n');

if($_POST['type'])
	$natt = new Natt($_POST['type'], $_POST);
else
	$natt = false;

?>
<style>
	.titulo{
		background-color:#eaeaea;
		font-weight: bold;
		text-align: center;
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

						<?php
						//var_dump($_POST);

						if($natt != false){

							//echo "<PRE>";
							//var_dump($natt->get_dados());
							//echo "</pre>";

							if($natt->error())
								echo "ERRO: " . $natt->get_error();
							else{

								$dados = $natt->get_dados();

								if($natt->get_tipo() == 'cpf' && strlen($dados['dados']['nome_do_consultado']) > 0){

									registrarConsulta(null, $_SESSION['usuario'], 'n');

								?>
								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Informações Pessoais</td>
									</tr>
									<tr>
										<td class="sub_titulo">Nome</td>
										<td><?= $dados['dados']['nome_do_consultado']; ?></td>
										<td class="sub_titulo">CPF</td>
										<td><?= $dados['dados']['cpf']; ?></td>
									</tr>
									<tr>
										<td class="sub_titulo">Mâe</td>
										<td><?= $dados['dados']['mae']; ?></td>
										<td class="sub_titulo">Nascimento</td>
										<td><?= $dados['dados']['nascimento']; ?></td>
									</tr>
									<tr>
										<td class="sub_titulo">Sexo</td>
										<td><?= $dados['dados']['sexo']; ?></td>
										<td class="sub_titulo">E-mail</td>
										<td><?= $dados['dados']['email']; ?></td>
									</tr>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Endereços Relacionados</td>
									</tr>
									<?php foreach($dados['enderecos_relacionados'] as $e){ ?>
									<tr>
										<td><small>Endereço</small><br><?= $e['rua']; ?></td>
										<td><small>Nº</small><br><?= $e['numero']; ?></td>
										<td><small>Cidade</small><br><?= $e['cidade']; ?>/<?= $e['estado']; ?></td>
										<td><small>CEP</small><br><?= $e['cep']; ?></td>
									</tr>
									<tr>
										<td colspan="4"></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Celular</td>
									</tr>
									<?php foreach($dados['celular'] as $e){ ?>
									<tr>
										<td><small>Telefone</small><br><?= $e['telefone']; ?></td>
										<td><small>Endereço</small><br><?= $e['endereco']; ?></td>
									</tr>
									<tr>
										<td colspan="4"></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Telefones Relacionados</td>
									</tr>
									<?php foreach($dados['telefones_relacionados_socios'] as $e){ ?>
									<tr>
										<td><small>Telefone</small><br><?= $e['telefone']; ?></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Participação Societária</td>
									</tr>
									<?php foreach($dados['participacao_societaria'] as $e){ ?>
									<tr>
										<td class="sub_titulo">CNPJ</td>
										<td><?= $e['cnpj']; ?></td>
										<td class="sub_titulo">Razão Social</td>
										<td><?= $e['razao_social']; ?></td>
									</tr>
									<tr>
										<td class="sub_titulo">Atividade Primária</td>
										<td><?= $e['atividade_primaria']; ?></td>
										<td class="sub_titulo">Data de Entrada</td>
										<td><?= $e['data_entrada']; ?></td>
									</tr>
									<tr>
										<td colspan="4"></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Possíveis Familiares</td>
									</tr>
									<?php foreach($dados['possiveis_familiares'] as $e){ ?>
									<tr>
										<td class="sub_titulo">Nome</td>
										<td><?= $e['nome']; ?></td>
										<td class="sub_titulo">CPF</td>
										<td><?= $e['cpf']; ?></td>
									</tr>
									<?php } ?>
								</table>

								<?php
								}elseif($natt->get_tipo() == 'cnpj' && strlen($dados['dados']['cnpj']) > 0){

									registrarConsulta(null, $_SESSION['usuario'], 'n');

								?>
								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Informações Principais</td>
									</tr>
									<tr>
										<td class="sub_titulo">CNPJ</td>
										<td><?= $dados['dados']['cnpj']; ?></td>
										<td class="sub_titulo">Razão Social</td>
										<td><?= $dados['dados']['razao_social']; ?></td>
									</tr>
									<tr>
										<td class="sub_titulo">Fantasia</td>
										<td><?= $dados['dados']['fantasia']; ?></td>
										<td class="sub_titulo">Data de Abertura</td>
										<td><?= $dados['dados']['data_abertura']; ?></td>
									</tr>
									<tr>
										<td class="sub_titulo">Sexo</td>
										<td><?= $dados['dados']['sexo']; ?></td>
										<td class="sub_titulo">E-mail</td>
										<td><?= $dados['dados']['email']; ?></td>
									</tr>
									<tr>
										<td class="sub_titulo">Natureza</td>
										<td><?= $dados['dados']['natureza']; ?></td>
										<td class="sub_titulo">Atividade Principal</td>
										<td><?= $dados['dados']['atividade_principal']; ?></td>
									</tr>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Composição Societária</td>
									</tr>
									<?php foreach($dados['composicao_societaria'] as $e){ ?>
									<tr>
										<td class="sub_titulo">Nome</td>
										<td><?= $e['nome_socio']; ?></td>
										<td class="sub_titulo">CPF do Sócio</td>
										<td><?= $e['cpf']; ?></td>
									</tr>
									<tr>
										<td class="sub_titulo">Qualificação</td>
										<td><?= $e['qualificacao']; ?></td>
										<td class="sub_titulo">Participação</td>
										<td><?= $e['participacao']; ?></td>
									</tr>
									<tr>
										<td colspan="4"></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Endereços</td>
									</tr>
									<?php foreach($dados['enderecos_relacionados'] as $e){ ?>
									<tr>
										<td><small>Endereço</small><br><?= $e['rua']; ?></td>
										<td><small>Nº</small><br><?= $e['numero']; ?></td>
										<td><small>Cidade</small><br><?= $e['cidade']; ?>/<?= $e['estado']; ?></td>
										<td><small>CEP</small><br><?= $e['cep']; ?></td>
									</tr>

									<tr>
										<td colspan="4"></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Telefones Fixos</td>
									</tr>
									<?php foreach($dados['fixo'] as $e){ ?>
									<tr>
										<td><small>Telefone</small><br><?= $e['telefone']; ?></td>
										<td><small>Endereço</small><br><?= $e['endereco']; ?></td>
									</tr>
									<tr>
										<td colspan="4"></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Celulares</td>
									</tr>
									<?php foreach($dados['celular'] as $e){ ?>
									<tr>
										<td><small>Telefone</small><br><?= $e['telefone']; ?></td>
										<td><small>Endereço</small><br><?= $e['endereco']; ?></td>
									</tr>
									<tr>
										<td colspan="4"></td>
									</tr>
									<?php } ?>
								</table>

								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Telefones Relacionados</td>
									</tr>
									<?php foreach($dados['telefones_relacionados'] as $e){ ?>
									<tr>
										<td><small>Telefone</small><br><?= $e['telefone']; ?></td>
									</tr>
									<?php } ?>
								</table>

								<?php
								}elseif($natt->get_tipo() == 'nome' || $natt->get_tipo() == 'cep' || $natt->get_tipo() == 'telefone'){

								?>
								
								<table class="table table-bordered">
									<tr>
										<td colspan="4" class="titulo">Selecione o CPF</td>
									</tr>
									<?php foreach($dados['cpf_cnpj'] as $e){ ?>
									<tr>
										<td class="form-inline">
											<form action="" method="post">
												<input type="hidden" name="type" value="<?php if(strlen($e)==11) echo 'cpf'; else echo 'cnpj'; ?>">
												<input type="hidden" name="<?php if(strlen($e)==11) echo 'cpf'; else echo 'cnpj'; ?>" value="<?= $e; ?>">
												<?= $e; ?> <button type="submit" class="btn btn-xs">Mais Informações</button>
											</form></td>
									</tr>
									<?php } ?>
								</table>

								<?php

								}else{
									echo "<p>Nada encontrado sobre o(s) dado(s) informado(s).</p>";
								}

							}

						}else {
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
											<button type="submit" class="btn btn-success">Buscar CPF</button>
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
											<button type="submit"  class="btn btn-success">Buscar CNPJ</button>
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
											<button type="submit" class="btn btn-success">Buscar Telefone</button>
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
											<button type="submit" class="btn btn-success">Buscar Nome</button>
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

										<!--<div class="form-group col-sm-3">
											<label for="">Início</label>
											<input type="text" name="inicio" id="inicio" class="form-control">
										</div>

										<div class="form-group col-sm-3">
											<label for="">Fim</label>
											<input type="text" name="fim" id="fim" class="form-control">
										</div>-->

										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-success">Buscar CEP</button>
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

        window.open('<?php echo URL; ?>/pesquisa_n/' + cep + '/' + inicio + '/' + fim, '_blank');

	}

	function consultarNome(n){

        window.open('<?php echo URL; ?>/pesquisa_n/nome/' + n, '_blank');

	}

	function consultar(n){
		window.open('<?php echo URL; ?>/pesquisa_n/cpf/' + n, '_blank');
	}

	function consultarParente(n){
		window.open('<?php echo URL; ?>/pesquisa_n/cpf/' + n, '_blank');
	}

	function consultarTelefone(n){

        window.open('<?php echo URL; ?>/pesquisa_n/telefone/' + n, '_blank');

	}

</script>