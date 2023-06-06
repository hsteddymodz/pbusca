<?php


include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Usuario.class.php');

include("class/protect.function.php");

$con    = new Conexao();
$router = new Router($_GET['p']);

if($_POST['salvar']){
	// Validações
	$erro = false;

	$usu = new Usuario();
	
	$senhaantiga = $con->select('senha')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('senha');

	$ultima_ateracao_senha = $con->select('ultima_alteracao_senha')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('ultima_alteracao_senha');

	// Verifica quando foi a ultima alteração de senha (a troca só pode ser efetuada uma vez ao mes)
	if ($ultima_ateracao_senha < strtotime('-30 days')){
		$erro = true;
		$ultima_alteracao = date('d/m/Y', strtotime($ultima_ateracao_senha));
		$nova_data_alteracao = date('d/m/Y', strtotime($ultima_ateracao_senha. ' + 30 days'));
		$not = new Notificacao('Senha recentemente trocada.', 'Você realizou a troca de senha no dia <b>'.$ultima_alteracao.'</b>. Só poderá alterá-la novamente no dia <b>'.$nova_data_alteracao.'</b>.', 'danger');
	}

	if($_POST['senha'] || $_SESSION['tipo'] != 4){

		if(strcmp($usu->criptografar($_POST['senhaa']), $senhaantiga) != 0){
			$erro = true;
			$not = new Notificacao('A senha antiga não bateu.', 'A senha antiga informada está errada.', 'danger');
		}

		if(strcmp($_POST['senha'], $_POST['senha2']) != 0){
			$erro = true;
			$not = new Notificacao('Senhas diferentes.', 'Os campos "Nova Senha" e "Repita a Senha" precisam estar iguais.', 'danger');
		}

		if(strlen($_POST['senha']) < 6){
			$erro = true;
			$not = new Notificacao('Senha muito curta.', 'Escolha uma nova senha com pelo menos 6 caracteres.', 'danger');
		}

	}

	if($_SESSION['tipo'] == 4){

		$count_usuarios = $con->select('count(*) as n')->from('usuario')->where("codigo != ".$_SESSION['usuario']." and usuario = '".$con->escape($_POST['usuario'])."' and deletado is null")->limit(1)->executeNGet('n');
		if($count_usuarios > 0){
			$erro = true;
			$not = new Notificacao('O nome de usuário <b>'.$_POST['usuario'].'</b> que você escolheu já pertence a outro usuário.', 'Nome de Usuário já existe.', 'danger');
		}

	}

	if(!$erro){

		$dados = array();

		if($_SESSION['tipo'] == 4){
			// se for admin master, pode editar nome e nome de usuario
			$dados['nome'] = $_POST['nome'];
			$dados['usuario'] = $_POST['usuario'];
		}

		if($_POST['senha'])
			$dados['senha'] = $usu->criptografar($_POST['senha']);
			$dados['ultima_alteracao_senha'] = date('Y-m-d H:i:s');

		$res = $con->update('usuario', $dados, $_SESSION['usuario']);

		if($res){

			die("<script>alert('Alterações salvas!'); location.href='".URL."/minhaconta';</script>");

		}else
			$not = new Notificacao('Falha ao inserir usuário!', 'Uma falha ocorreu na inserção do usuário. Solicite ajuda com um administrador.', 'danger');

	}

}


$usuario = $con->select('*')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->limit(1)->executeNGet();


?>


<form action="" id="formulario" enctype="multipart/form-data" method="post">

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li class="active">Minha Conta</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Minha Conta</h1>
			</div>
		</div><!--/.row-->
		
		<form action="" method="post">

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						

							<input type="hidden" name="salvar" value="true">
	
							<div class="form-group form-inline">

								<button type="submit" class="btn btn-primary btn-xs">
									<i class="glyphicon glyphicon-floppy-disk"></i> Salvar
								</button>
								

							</div>

					</div>
					<div class="panel-body">

						<div class="col-md-12">
				        	<?php if($not) $not->show(); ?>
				        </div>

						<div class="col-lg-4">

							<?php

							if($_SESSION['tipo'] == 4){ ?>
							<div class="form-group">
								<label for="">Nome</label>
								<input type="text" required value="<?php echo $usuario['nome']; ?>" name="nome" class="form-control">
							</div>
							<div class="form-group">
								<label for="">Usuário</label>
								<input type="text" required value="<?php echo $usuario['usuario']; ?>" name="usuario" class="form-control">
							</div>
							<?php
							}

							?>
							
							<div class="form-group">
								<label>Senha Antiga*</label>
								<input type="text" value="" name="senhaa" minlength="6" id="senhaa" class="form-control">
							</div>

							<div class="form-group">
								<label>Nova Senha*</label>
								<input type="text" value="" name="senha" minlength="6" id="senha" class="form-control">
							</div>
							
							<div class="form-group">
								<label>Repita a Senha*</label>
								<input type="text" value="" name="senha2" id="senha2" class="form-control">
							</div>

						</div>

						<div class="col-lg-8">
							<h3>Requisitos para troca de senha</h3>
							<ul>
								<li>A senha deve possuir pelo menos 6 dígitos (caracteres);</li>
								<li>Caracteres maísculos e minísculos <b>são diferenciados</b> pelo sistema;</li>
								<li>A senha só poderá ser trocada <b>uma vez</b> a cada 30 dias;</li>
								<li>Tenha cuidado e atenção ao digitar sua nova senha;</li>
								<li>É recomendável, mas não obrigatório, o uso de caracteres especiais (!@/$%).</li>
							</ul>
						</div>

					</div>
				</div>
			</div>
		</div><!--/.row-->	

		</form>


	</div><!--/.main-->

</form>