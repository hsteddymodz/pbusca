<?php


include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Usuario.class.php');

$con    = new Conexao();
$router = new Router($_GET['p']);

if($_POST['salvar']){


	// Validações
	$erro = false;
	if(!$router->param(0) || $_POST['senha'])
		if(strcmp($_POST['senha'], $_POST['senha2']) != 0){
			$erro = true;
			$not = new Notificacao('Senhas diferentes!', 'As senhas tem que ser iguais.');
		}

	if(!$erro){

		if($router->param(0)){


			// editar
			$dados['nome']    = $_POST['nome'];
			$dados['usuario'] = $_POST['usuario'];
			$dados['plano']   = $_POST['plano'];
			//$dados['max_usuarios'] = $_POST['max_usuarios'];
			$dados['max_tempo_teste'] = $_POST['tempo_maximo'];
			$dados['max_contas_teste'] = $_POST['contas_teste'];

			$usu = new Usuario();

			if($_POST['senha'])
				$dados['senha'] = $usu->criptografar($_POST['senha']);

			$dados['pagina_personalizada'] = ($_POST['pagina_personalizada'])? 1:0;

			if($dados['pagina_personalizada'])
				$dados['url'] = $_POST['url'];

			$dados['preco_recarga'] = str_replace(',', '.', $_POST['preco_recarga']);


			$res = $con->update('usuario', $dados, $router->param(0));

			$con->execute("delete from revendedor_plano where revendedor_codigo = '".$router->param(0)."'");
			$con->execute("delete from teste_plano where revendedor_codigo = '".$router->param(0)."'");

			if(is_array($_POST['planos']))
					foreach($_POST['planos'] as $pl){
						$con->insert('revendedor_plano', array('revendedor_codigo'=>$router->param(0), 'plano_codigo'=>$pl));
					}

			if(is_array($_POST['testes']))
					foreach($_POST['testes'] as $pl){
						$con->insert('teste_plano', array('revendedor_codigo'=>$router->param(0), 'plano_codigo'=>$pl));
					}

			die("<script>alert('Revendedor cadastrado!'); location.href='".URL."/revendedor';</script>");


		}else{
			// cadastrar

			$dados = array();

			$dados['nome']    = $_POST['nome'];
			$dados['usuario'] = $_POST['usuario'];
			$dados['plano']   = $_POST['plano'];
			//$dados['max_usuarios'] = $_POST['max_usuarios'];
			$dados['max_tempo_teste'] = $_POST['tempo_maximo'];
			$dados['max_contas_teste'] = $_POST['contas_teste'];
			$dados['tipo'] = 3;
			$dados['senha'] = $_POST['senha'];
			$dados['administrador'] = $_SESSION['usuario'];
			$dados['pagina_personalizada'] = ($_POST['pagina_personalizada'])? 1:0;
			$dados['data'] = date('Y-m-d H:i:s');
			$dados['preco_recarga'] = str_replace(',', '.', $_POST['preco_recarga']);
			$dados['quemcadastrou'] = $_SESSION['usuario'];

			if($dados['pagina_personalizada'])
				$dados['url'] = $_POST['url'];

			$res = $con->insert('usuario', $dados);

			$con->insert('revendedor_credito', array('valor'=>$_POST['credito'], 'usuario'=>$res, 'data'=>date('Y-m-d H:i:s'), 'observacao'=>"Adição de crédito"));

			if($res){
				// revendedor cadastrado com sucesso

				if(is_array($_POST['planos']))
					foreach($_POST['planos'] as $pl){
						$con->insert('revendedor_plano', array('revendedor_codigo'=>$res, 'plano_codigo'=>$pl));
					}

				if(is_array($_POST['testes']))
					foreach($_POST['testes'] as $pl){
						$con->insert('teste_plano', array('revendedor_codigo'=>$res, 'plano_codigo'=>$pl));
					}

				die("<script>alert('Revendedor cadastrado!'); location.href='".URL."/revendedor';</script>");

			}else
				$not = new Notificacao('Falha ao inserir usuário!', 'Uma falha ocorreu na inserção do usuário. Solicite ajuda com um administrador.');

		}

	}

}

if($router->param(0) > 0){

	$usu_codigo = intval($router->param(0));
	$revendedor = $con->select('*')->from('usuario')->where("codigo = '$usu_codigo'")->limit(1)->executeNGet();
	$planos     = $con->select('*')->from('revendedor_plano')->where("revendedor_codigo = '$usu_codigo'")->executeNGet();
	$testes     = $con->select('*')->from('teste_plano')->where("revendedor_codigo = '$usu_codigo'")->executeNGet();

	$lista_planos = array(); $lista_testes = array();
	foreach($planos as $pl){
		$lista_planos[] = $pl['plano_codigo'];
	}

	foreach($testes as $pl){
		$lista_testes[] = $pl['plano_codigo'];
	}

	$creditos   = floatval($con->select('sum(valor) as credito')->from('revendedor_credito')->where("usuario = '$usu_codigo'")->limit(1)->executeNGet('credito'));

}else{
	$revendedor = array();
	$usu_codigo = false;
	$planos     = array();
	$testes     = array();
	$lista_planos = array();
	$lista_testes = array();
}
if($_SESSION['tipo'] == 2)
	$planos = $con->select('*')->from('plano')->where("administrador = '".$_SESSION['usuario']."'")->executeNGet();
elseif($_SESSION['tipo'] == 4)
	$planos = $con->select('*')->from('plano')->executeNGet();
else
	die("<script>alert('Você não tem permissões para editar esta página.'); location.href='".URL."/';</script>");

if(count($planos) == 0){
	die("<script>alert('Cadastre algum plano antes de cadastrar um revendedor.'); location.href='".URL."/plano';</script>");
}

?>


<form action="" id="formulario" enctype="multipart/form-data" method="post">

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li><a href="<?php echo URL; ?>/revendedor">Revendedores</a></li>
				<li class="active">Cadastrar Revendedor</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php if($router->param(0)) echo "Editar"; else echo "Cadastrar"; ?> Revendedor</h1>
			</div>
		</div><!--/.row-->


		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						
							<input type="hidden" name="salvar" value="true">
	
							<div class="form-group form-inline">
								<button type="button" onclick="if(confirm('Tem certeza?')) location.href='<?php echo URL; ?>/revendedor';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>

								<button type="button" onclick="enviar();" class="btn btn-primary btn-xs">
									<i class="glyphicon glyphicon-floppy-disk"></i> Salvar
								</button>
								
								<?php if($_SESSION['admin']){ ?>
								<button type="button" onclick="if(confirm('Tem certeza?')) location.href='usuario-cadastrar/<?php echo $router->param(0); ?>/desativar';" class="btn btn-primary btn-xs">
									<i class="glyphicon glyphicon-close"></i> Desativar
								</button>
								<?php } ?>

							</div>

					</div>
					<div class="panel-body">

						<div class="col-md-12">
				        	<?php if($not) $not->show(); ?>
				        </div>

						<div class="col-xs-3">


							<div class="form-group">
								<label>Nome*</label>
								<input type="text"required  value="<?php echo $revendedor['nome']; ?>" name="nome" required class="form-control">
							</div>

							<div class="form-group">
								<label>Usuário*</label>
								<input type="text" required value="<?php echo $revendedor['usuario']; ?>" placeholder="Nome de Usuário" name="usuario" id="usuario" class="form-control">
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

						<div class="col-xs-3">

							<div class="form-group">
								<label>Plano</label>

								<select name="plano" id="plano" required class="form-control">
									<option value="">Selecione</option>
									<?php foreach($planos as $p){ ?>
									<option <?php if($revendedor['plano'] == $p['codigo']) echo 'selected'; ?> value="<?php echo $p['codigo']; ?>"><?php echo $p['nome']; ?></option>
									<?php } ?>
								</select>

							</div>			


							<!-- <div class="form-group ">
								<label>Número Máx de Usuários</label>
								<input type="number" id="max_usuarios" required name="max_usuarios" value="<?php echo $revendedor['max_usuarios']; ?>" class="form-control">
							</div> -->
							
							

							<div class="form-group">
								<label>Tempo máximo para as Contas de Teste</label>
								<div class="input-group">
									<input type="number" id="tempo_maximo"  required name="tempo_maximo" value="<?php echo $revendedor['max_tempo_teste']; ?>" class="form-control" describedby="basic-addon2">
	  								<span class="input-group-addon" id="basic-addon2">em minutos</span>
								</div>
							</div>

							<div class="form-group">
								<label>Número máximo de contas de teste</label>
								<input type="number" id="contas_teste" required name="contas_teste" value="<?php echo $revendedor['max_contas_teste']; ?>" class="form-control">
							</div>

						</div>

						<div id="div_planos" class="col-xs-3">

							
							<div class="form-group">
								<label>Créditos</label>
								<input type="number"  <?php if($router->param(0)) echo 'disabled value="'.$creditos.'"'; ?> id="credito" min="0" name="credito" class="form-control">
								<?php if($router->param(0)) echo '<p>Para adicionar créditos, <a target="_Blank" href="'.URL.'/revendedor_credito">clique aqui</a>.</p>'; ?>
							</div>

							<div class="form-group">
								<label>Preço da Recarga</label>
								<input type="text" id="preco_recarga" value="<?php echo $revendedor['preco_recarga']; ?>" name="preco_recarga" class="form-control">
								<p><small>Valor que será mostrado para o revendedor pagar caso os créditos dele se acabem.</small></p>
							</div>
							
							<div class="form-group">
								<label for="">Página Personalizada</label>
								<label for="pp"><input onchange="enable_custom_page();" id="pp" value="1" <?php if($revendedor['pagina_personalizada'] == 1) echo 'checked="checked"'; ?> type="checkbox" name="pagina_personalizada"> Este revendedor vai possuir uma página personalizada?</label>
								<p>probusca.com/nomeDoRevendedor</p>
							</div>


							<div class="form-group <?php if(!$revendedor['pagina_personalizada']) echo 'esconder'; ?> onlypp">

								<label for="">URL Customizada</label>
								<div class="input-group">
								  <span class="input-group-addon" id="basic-addon3">probusca.com/</span>
								  <input <?php if($revendedor['pagina_personalizada']) echo 'required'; ?> value="<?php echo $revendedor['url']; ?>" class="form-control" type="text" id="url" name="url" aria-describedby="basic-addon3">
								</div>

							</div>
							

						</div>

						<div class="col-xs-3">
							<div class="form-group">
								<label>Planos</label>
							</div>
							
							<div class="form-group">
								<p>Selecione o Plano dos Clientes deste Revendedor</p>
								
								<?php foreach($con->select('*')->from('plano')->orderby('nome asc')->where("administrador = '".$_SESSION['usuario']."'")->executeNGet() as $pl){ ?>
								<label for="plano_<?php echo $pl['codigo']; ?>">
									<input id="plano_<?php echo $pl['codigo']; ?>" <?php if(in_array($pl['codigo'], $lista_planos)) echo 'checked="checked"'; ?> name="planos[]" value="<?php echo $pl['codigo']; ?>" type="checkbox"> <?php echo $pl['nome']; ?> (R$ <?php echo $pl['preco']; ?>)
								</label><br>
								<?php } ?>

							</div>

							<div class="form-group">
								<p>Selecione os Planos de Teste deste Revendedor</p>
								
								<?php foreach($con->select('*')->from('plano')->orderby('nome asc')->where("administrador = '".$_SESSION['usuario']."'")->executeNGet() as $pl){ ?>
								<label for="teste_<?php echo $pl['codigo']; ?>">
									<input id="teste_<?php echo $pl['codigo']; ?>" <?php if(in_array($pl['codigo'], $lista_testes)) echo 'checked="checked"'; ?> name="testes[]" value="<?php echo $pl['codigo']; ?>" type="checkbox"> <?php echo $pl['nome']; ?> (R$ <?php echo $pl['preco']; ?>)
								</label><br>
								<?php } ?>

							</div>
						</div>


					</div>
				</div>
			</div>
		</div><!--/.row-->	


	</div><!--/.main-->

</form>


<script>

function enable_custom_page(){

	var pp = $('#pp').prop('checked');
	if(pp){
		$('#url').attr('required', true);
		$('.onlypp').removeClass('esconder');
	}else{
		$('#url').attr('required', false);
		$('.onlypp').addClass('esconder');
	}
}


	function verificar_tipo(val){

		var divs = $('.revendedor-only'), max_usuarios = $('#max_usuarios');

		if(val == 3){
			$('#div_planos').removeClass('esconder');
			$('input[name="planos"]').attr('required', true);
		}
		else{
			$('#div_planos').addClass('esconder');
			$('input[name="planos"]').attr('required', false);
		}
			

		if(val == 3){
			divs.removeClass('esconder');
			max_usuarios.attr('required', true);
		}else{
			divs.addClass('esconder');
			max_usuarios.attr('required', false);
		}

	}
	
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

			console.log(res);
			var tmp = JSON.parse(res);
			end_loading();
			if(tmp.n == 0)
				return $('#formulario').submit();
			else{
				
				alert('O usuário informado já existe.');
				return $('#usuario').focus();
			}
		}).fail(function(res){

			end_loading();
			console.log(res);
		});

		

	}

</script>