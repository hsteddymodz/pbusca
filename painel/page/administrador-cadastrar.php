<?php


include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Usuario.class.php');
include("class/protect.function.php");

protect(array(4));


$con    = new Conexao();
$router = new Router($_GET['p']);

if($_SESSION['tipo'] != 4)
	die("<script>alert('Você não pode cadastrar administradores.'); location.href='".URL."/inicio';</script>");

if($_POST['salvar']){


	// Validações
	$erro = false;
	if(strcmp($_POST['senha'], $_POST['senha2']) != 0){
		$erro = true;
		$not = new Notificacao('Senhas diferentes!', 'As senhas tem que ser iguais.');
	}

	$usu = new Usuario();

	if(!$erro && $router->param(0) > 0){

		$dados = array();

		$dados['nome']    = $_POST['nome'];
		$dados['usuario'] = $_POST['usuario'];
		$dados['plano']   = $_POST['plano'];

		if($_POST['senha'])
			$dados['senha'] = $usu->criptografar($_POST['senha']);

		$res = $con->update('usuario', $dados, $router->param(0));

		if($res){

			// usuario de teste cadastrado com sucesso
			die("<script>alert('Alterações salvas!'); location.href='".URL."/administrador';</script>");

		}else
			$not = new Notificacao('Falha ao inserir Administrador!', 'Uma falha ocorreu na inserção do Administrador.');

	}elseif(!$erro){


		$dados = array();

		$dados['nome']    = $_POST['nome'];
		$dados['usuario'] = $_POST['usuario'];
		$dados['plano']   = $_POST['plano'];
		$dados['tipo']    = 2;
		$dados['senha']   = $_POST['senha'];
		$dados['quemcadastrou'] = $_SESSION['usuario'];

		$dados['administrador'] = $_SESSION['usuario'];

		$dados['data'] = date('Y-m-d H:i:s');
	
		$res = $con->insert('usuario', $dados);

		if($res){

			// usuario de teste cadastrado com sucesso
			die("<script>alert('Administrador cadastrado!'); location.href='".URL."/administrador';</script>");

		}else
			$not = new Notificacao('Falha ao inserir Administrador!', 'Uma falha ocorreu na inserção do Administrador.');
		

	}

}





// lista de planos
$planos = $con->select('*')->from('plano')->executeNGet();

if($router->param(0)){
	$usu_codigo = intval($router->param(0));
	$usuario = $con->select('*')->from('usuario')->where("codigo = '$usu_codigo'")->limit(1)->executeNGet();
	if($usuario['tipo'] == 4){
		die("<script>alert('Você não pode editar um administrador MASTER.'); location.href='../';</script>");
	}
}else{
	$usuario = array();
}




?>


<form action="" id="formulario" enctype="multipart/form-data" method="post">

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li><a href="<?php echo URL; ?>/usuario">Administrador</a></li>
				<li class="active">Cadastrar Administrador</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php if($router->param(0)) echo "Editar"; else echo "Cadastrar"; ?> Administrador</h1>
			</div>
		</div><!--/.row-->


		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						

							<input type="hidden" name="salvar" value="true">
	
							<div class="form-group form-inline">
								<button type="button" onclick="if(confirm('Tem certeza?')) location.href='<?php echo URL; ?>/usuario';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>

								<button type="button" onclick="enviar();" class="btn btn-primary btn-xs">
									<i class="glyphicon glyphicon-floppy-disk"></i> Salvar
								</button>
								
							</div>

					</div>
					<div class="panel-body">

						<div class="col-md-12">
				        	<?php if($not) $not->show(); ?>
				        </div>

						<div class="col-xs-4">


							<div class="form-group">
								<label>Nome*</label>
								<input type="text"required  value="<?php echo $usuario['nome']; ?>" name="nome" required class="form-control">
							</div>

							<div class="form-group">
								<label>Usuário*</label>
								<input type="text" required value="<?php echo $usuario['usuario']; ?>" placeholder="Nome de Usuário" name="usuario" id="usuario" class="form-control">
							</div>

							<div class="form-group">
								<label>Senha*</label>
								<input type="text" required value="" name="senha" id="senha" class="form-control">
							</div>

							<div class="form-group">
								<label>Repita a Senha*</label>
								<input type="text" required value="" name="senha2" id="senha2" class="form-control">
							</div>

						</div>

						<div class="col-xs-4">

							<div class="form-group">
								<label>Plano</label>

								<select name="plano" id="plano" required class="form-control">
									<option value="">Selecione</option>
									<?php foreach($planos as $p){ ?>
									<option <?php if($usuario['plano'] == $p['codigo']) echo 'selected'; ?> value="<?php echo $p['codigo']; ?>"><?php echo $p['nome']; ?></option>
									<?php } ?>
								</select>

							</div>	



							

							
						</div>



					</div>
				</div>
			</div>
		</div><!--/.row-->	


	</div><!--/.main-->

</form>


<script>



	
	function enviar(){

		var senhaA = $('#senha').val();
		var senhaB = $('#senha2').val();
		var username = $('#usuario').val();

		<?php if($router->param(0) > 0){ ?> 
		if(senhaA.length == 0)
			console.log('a senha não será alterada');
		else <?php } ?>if(senhaA.length < 6 || senhaA.length > 16)
			return alert('A senha deve ter entre 6 e 16 caracteres.');

		if(senhaA != senhaB)
			return alert('As senhas não batem.');

		if(username.indexOf(' ') >= 0){
			return alert('O nome de usuário não pode conter espaços.');
		}

		show_loading();
		$.post(
			'<?php echo URL; ?>/page/verificar_username.php', 
			{search:true, usuario:$('#usuario').val(), codigo:<?php echo intval($router->param(0)); ?>}
		).done(function(res){

			var edit = <?php echo intval($router->param(0)); ?>;
			console.log(res);
			var tmp = JSON.parse(res);
			end_loading();

			if(tmp.n == 0){

				return $('#formulario').submit();


			}else{
				
				alert('O usuário informado já existe.');
				return $('#usuario').focus();
			}
		}).fail(function(res){

			end_loading();
			console.log(res);
		});

		

	}

</script>