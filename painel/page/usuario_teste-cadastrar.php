<!-- Modal -->
<div id="modalDetalhes" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Detalhes da Conta</h4>
      </div>
      <div class="modal-body">
        <p><textarea name="" id="input_cp" cols="30" class="form-control" rows="10"></textarea></p>
      </div>
      <div class="modal-footer">

      	
      	<button type="button" id="copiado" class="btn btn-success" onclick="copiar();">Copiar p/ Área de Transf.</button>
      	<button type="button" class="btn btn-primary" onclick="location.href='<?= URL; ?>/usuario_teste';">Lista de Usuários</button>
        <button type="button" class="btn btn-default" onclick="location.href='//probusca.com/painel/usuario_teste-cadastrar';">Cadastrar Outro</button>
      </div>
    </div>

  </div>
</div>
<script>


	function copiar() {
	  
	  var copyTextarea = document.getElementById('input_cp');
	  copyTextarea.select();

	  try {
	    var successful = document.execCommand('copy');
	    if(successful) document.getElementById('copiado').innerHTML = 'COPIADO!';
	  } catch (err) {
	    alert('Opa, Não conseguimos copiar o texto, é possivel que o seu navegador não tenha suporte, tente usar Crtl+C.');
	  }
	};

</script>
<?php

include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Usuario.class.php');

$con    = new Conexao();
$router = new Router($_GET['p']);


$usu_codigo = intval($router->param(0));
if($usu_codigo > 0){

	$seguranca = $con
		->select('revendedor, administrador')
		->from('usuario')
		->where("codigo = '$usu_codigo'")
		->limit(1)
		->executeNGet();

	if(
		$seguranca['administrador'] != $_SESSION['usuario'] && 
		$seguranca['revendedor'] != $_SESSION['usuario'] && 
		$_SESSION['tipo'] != 4 && 
		$_SESSION['tipo'] != 3
	)
		die("<script>alert('Você não pode alterar este usuário.'); location.href='../index.php';</script>");

}


if($_POST['salvar']){

	$usu = new Usuario();
	
	// Validações
	$erro = false;
	if(strcmp($_POST['senha'], $_POST['senha2']) != 0){
		$erro = true;
		$not = new Notificacao('Senhas diferentes!', 'As senhas tem que ser iguais.');
	}

	if(!$erro && $router->param(0) > 0){

		$dados = array();

		$dados['nome']    = $_POST['nome'];
		$dados['usuario'] = $_POST['usuario'];
		$dados['plano']   = $_POST['plano'];
		$dados['tipo'] = 1;

		if($_POST['senha'])
			$dados['senha'] = $usu->criptografar($_POST['senha']);

		$dados['log'] = date('d/m/y H:i:s') . "," . $_SESSION['usuario'] . "," . $_SESSION['tipo'];
		$res = $con->update('usuario', $dados, $router->param(0));

		if($res){

			// usuario de teste cadastrado com sucesso
			die("<script>alert('Alterações salvas!'); location.href='".URL."/usuario_teste';</script>");

		}else
			$not = new Notificacao('Falha ao inserir conta de teste!', 'Uma falha ocorreu na inserção da conta de teste. Solicite ajuda com um administrador.');

	}elseif(!$erro && ($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 2)){

		$dados = array();

		$dados['nome']    = $_POST['nome'];
		$dados['usuario'] = $_POST['usuario'];
		$dados['plano']   = $_POST['plano'];
		$dados['tipo'] = 1;
		$dados['senha'] = $usu->criptografar($_POST['senha']);

		$dados['quemcadastrou'] = $_SESSION['usuario'];

		if($_SESSION['tipo'] == 3){
			$dados['administrador'] = $con->select('administrador')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('administrador');
			$dados['revendedor'] = $_SESSION['usuario'];
		}else
			$dados['administrador'] = $_SESSION['usuario'];

		$dados['data'] = date('Y-m-d H:i:s');
		$dados['teste'] = 1;
		$dados['vencimento'] = date('Y-m-d H:i:s', strtotime('+'.$_POST['tempo_maximo'].' minutes'));

		$res = $con->insert('usuario', $dados);

		if($res){

			// usuario de teste cadastrado com sucesso
			die('<script src="' . URL . '/js/jquery-3.1.1.min.js"></script><script src="'.URL.'/js/bootstrap.min.js"></script><script>' 
				. "
				$('#input_cp').val('Link para Login: https://probusca.com".'\n'."Usuário: ".$dados['usuario']."".'\n'."Senha: ".$_POST['senha']."".'\n'."Validade da Conta: ".$_POST['tempo_maximo']." minutos após o login.');
				$('#modalDetalhes').modal('show');</script>");

		}else
			$not = new Notificacao('Falha ao inserir conta de teste!', 'Uma falha ocorreu na inserção da conta de teste. Solicite ajuda com um administrador.');
		

	}else
		$erro[] = "Permissões insuficientes";

}

if($router->param(0) > 0){
	$usu_codigo = intval($router->param(0));
	$revendedor = $con->select('*')->from('usuario')->where("codigo = (select codigo from usuario where revendedor = '$usu_codigo' or administrador = '$usu_codigo')")->limit(1)->executeNGet();
	$usuario    = $con->select('*')->from('usuario')->where("codigo = '$usu_codigo'")->limit(1)->executeNGet();

}else{
	$revendedor = array();
	$revendedor = $con->select('*')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->limit(1)->executeNGet();
	$usu_codigo = false;
}

if($_SESSION['tipo'] == 3){

	$planos = $con->select('*')->from('plano')->where("codigo in (select plano_codigo from teste_plano where revendedor_codigo = '".$_SESSION['usuario']."')")->executeNGet();
	$left_users = $con->select('count(*) as n')->from('usuario')->where("revendedor = '".$_SESSION['usuario']."' and teste = 1 and deletado is null")->executeNGet('n');
	$max_users  = $con->select('max_contas_teste as n')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->executeNGet('n');

	if($left_users >= $max_users)
		die("<script>alert('Você atingiu o limite de contas de teste.'); location.href='".URL."/usuario_teste';</script>");

}else
	$planos = $con->select('*')->from('plano')->where("administrador = '".$_SESSION['usuario']."'")->executeNGet();

if($router->param(0)){
	$teste_codigo = intval($router->param(0));
	$conta_teste = $con->select('*')->from('usuario u')->where("u.codigo = '$teste_codigo' and u.deletado is null and u.teste = 1")->limit(1)->executeNGet();
}else{
	$conta_teste = array();
}

if($_SESSION['tipo'] == 1 || !$_SESSION['tipo'])
	die("<script>alert('Você não pode cadastrar usuários de teste.'); location.href='".URL."/inicio';</script>");

if(count($planos) == 0){
	die("<script>alert('Cadastre algum plano antes de cadastrar um revendedor.'); location.href='".URL."/plano';</script>");
}

?>


<form action="" id="formulario" enctype="multipart/form-data" method="post">

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li><a href="<?php echo URL; ?>/usuario_teste">Contas de Teste</a></li>
				<li class="active">Cadastrar Conta de Teste</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php if($router->param(0)) echo "Editar"; else echo "Cadastrar"; ?> Conta de Teste</h1>
			</div>
		</div><!--/.row-->


		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						

							<input type="hidden" name="salvar" value="true">
	
							<div class="form-group form-inline">
								<button type="button" onclick="if(confirm('Tem certeza?')) location.href='<?php echo URL; ?>/usuario_teste';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>

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

						<div class="col-xs-4">


							<div class="form-group">
								<label>Nome*</label>
								<input type="text"required  value="<?php echo $conta_teste['nome']; ?>" name="nome" required class="form-control">
							</div>

							<div class="form-group">
								<label>Usuário*</label>
								<input type="text" required value="<?php echo $conta_teste['usuario']; ?>" placeholder="Nome de Usuário" name="usuario" id="usuario" class="form-control">
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
									<option <?php if($conta_teste['plano'] == $p['codigo']) echo 'selected'; ?> value="<?php echo $p['codigo']; ?>"><?php echo $p['nome']; ?></option>
									<?php } ?>
								</select>

							</div>			


							
							<?php if(!$router->param(0)) { ?>
							<div class="form-group">
								<label>Duração da Conta de Teste</label>
								<div class="input-group">
									<input type="number" id="tempo_maximo" min="0" 
									onkeyup="verificar_max();" onkeydown="verificar_max();"
									<?php if($_SESSION['tipo'] == 3) echo 'max="'.$revendedor['max_tempo_teste'].'"'; ?>
									required name="tempo_maximo" class="form-control" describedby="basic-addon2">
	  								<span class="input-group-addon" id="basic-addon2">em minutos</span>
								</div>
								<?php if($_SESSION['tipo'] == 3) echo "<p><small>Tempo Máximo: ".$revendedor['max_tempo_teste']."</small></p>"; ?>
							</div>

							<?php } ?>

						</div>

					</div>
				</div>
			</div>
		</div><!--/.row-->	


	</div><!--/.main-->

</form>

<script>
	
	function verificar_max(){

		var tempo = parseInt($('#tempo_maximo').val());

		var max = <?php echo intval($revendedor['max_tempo_teste']); ?>;

		if(max > 0 && tempo > max){
			alert('O tempo máximo é ' + max);
			$('#tempo_maximo').val(max);
			return $('#tempo_maximo').focus();
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