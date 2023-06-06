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
      	<button type="button" class="btn btn-primary" onclick="location.href='<?= URL; ?>/usuario';">Lista de Usuários</button>
        <button type="button" class="btn btn-default" onclick="location.href='//probusca.com/painel/usuario-cadastrar';">Cadastrar Outro</button>
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

// verifica se o usuário pertence ao revendedor
$usu_codigo = intval($router->param(0));
$seguranca = $con->select('revendedor, administrador')->from('usuario')->where("codigo = '$usu_codigo'")->limit(1)->executeNGet();

if(!isset($_SESSION))
	@session_start();

if($usu_codigo > 0){
	if(
		($seguranca['administrador'] != intval($_SESSION['usuario']) && 
		$seguranca['revendedor'] != intval($_SESSION['usuario']) && 
		$_SESSION['tipo'] != 4) || intval($_SESSION['usuario']) == 0
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

	if(!$erro && $router->param(0) > 0 && ($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 2)){

		$dados = array();

		if($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4){
			$dados['usuario'] = $_POST['usuario'];
			$dados['nome']    = $_POST['nome'];
		}

		if($_POST['plano'])
			$dados['plano']   = $_POST['plano'];

		if($_POST['vencimento']){

			if(!$_POST['vencimento2']) 
				$dados['vencimento'] = implode('-', array_reverse(explode('/', $_POST['vencimento']))) . ' 23:59:59';
			else{

				$data = implode('-', array_reverse(explode('/', $_POST['vencimento'])));
				$horario = explode(':', $_POST['vencimento2']);

				if(substr($horario[1], -2) == 'am'){
					$hora = $horario[0].":".str_replace('am', '', $horario[1]).":00";
				}

				if(substr($horario[1], -2) == 'pm'){

					$horario[0] = intval($horario[0]);
					$horario[1] = intval(str_replace('pm', '', $horario[1]));

					if($horario[0] == 12){

						$hora = "00:".str_pad($horario[1], 2, '0', STR_PAD_LEFT).":01";
						// pula um dia
						$data = date('Y-m-d', strtotime($data)+86400);

					}else{
						$hora = ($horario[0]+12).":00:00";
					}

				}

				$dados['vencimento'] = "{$data} {$hora}";

			}

		}

		if($_POST['senha'])
			$dados['senha'] = $usu->criptografar($_POST['senha']);

		$dados['log'] = date('d/m/y H:i:s') . "," . $_SESSION['usuario'] . "," . $_SESSION['tipo'];
		$res = $con->update('usuario', $dados, $router->param(0));

		if($res){

			if($_SESSION['usuario'] == $router->param(0))
				die("<script>alert('Alterações salvas!'); location.href='".URL."/usuario-cadastrar/".$router->param(0)."';</script>");
			// usuario de teste cadastrado com sucesso
			die("<script>alert('Alterações salvas!'); location.href='".URL."/usuario';</script>");

		}else
			$not = new Notificacao('Falha ao inserir usuário!', 'Uma falha ocorreu na inserção do usuário. Solicite ajuda com um administrador.');

	}elseif(!$erro && ($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 3 || $_SESSION['tipo'] == 2)){

		$descontado; $prazo_conta;

		$dados = array();

		if($_POST['vencimento']){

			$venc = explode('/', $_POST['vencimento']);
			if(!$_POST['vencimento2']) $_POST['vencimento2'] = '23:59';

			if(substr($_POST['vencimento2'], -2) == 'pm'){
				$tmp = explode(':', $_POST['vencimento2']);
				$_POST['vencimento2'] = ($tmp[0]+12) . ":" . $tmp[1];
			}else
				$_POST['vencimento2'] = substr($_POST['vencimento2'], 0, -2);


			
			$dados['vencimento'] = "$venc[2]-$venc[1]-$venc[0] ".str_replace('pm', '', $_POST['vencimento2']).":00";


		
		}elseif($_SESSION['tipo'] == 3 && !$router->param(0)){

			$credito = floatval($con->select('sum(valor) as credito')->from('revendedor_credito')->where("usuario = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('credito'));
			

			$descontado = 0;
			if($_POST['duracao'] > 0){

				$descontado = ($_POST['duracao'] == 7)? 0.25:0.5;
				$prazo_conta = $_POST['duracao']. " dias";

				$dados['vencimento'] = date('Y-m-d H:i:s', strtotime('+'.$_POST['duracao'].' days'));

			}elseif($_POST['tempo_maximo']){
				$descontado = floatval($_POST['tempo_maximo']);
				$prazo_conta = $_POST['tempo_maximo']. " mes(es)";
				$dados['vencimento'] = date('Y-m-d H:i:s', strtotime('+'.$_POST['tempo_maximo'].' months'));
			}

			if($descontado > $credito)
				die("<script>alert('Você não possui créditos suficientes!'); location.href='".URL."/usuario';</script>");	

		}


		

		$dados['nome']    = $_POST['nome'];
		$dados['usuario'] = $_POST['usuario'];

		$dados['quemcadastrou'] = $_SESSION['usuario'];

		if($_POST['plano'])
			$dados['plano']   = $_POST['plano'];

		$dados['tipo']    = 1;
		$dados['senha']   = $usu->criptografar($_POST['senha']);

		if($_SESSION['tipo'] == 3){
			$dados['administrador'] = $con->select('administrador')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('administrador');
			$dados['revendedor'] = $_SESSION['usuario'];
		}else
			$dados['administrador'] = $_SESSION['usuario'];

		$dados['data'] = date('Y-m-d H:i:s');		

		//die(var_dump($dados));

		$res = $con->insert('usuario', $dados);

		if($_SESSION['tipo'] == 3 && !$router->param(0)){

			// desconta os creditos se ja for revendedor
			$cr = array();
			$cr['valor'] = $descontado * -1;
			$cr['usuario'] = $_SESSION['usuario'];
			$cr['data'] = date('Y-m-d H:i:s');
			$cr['observacao'] = 'Cadastro de usuário com duração de ' . $prazo_conta;
			$cr['favorecido'] = $res;

			$creditos = $con->insert('revendedor_credito', $cr);

		}
		

		if($res){

			// usuario cadastrado com sucesso
			// usuario de teste cadastrado com sucesso
			die('<script src="' . URL . '/js/jquery-3.1.1.min.js"></script><script src="'.URL.'/js/bootstrap.min.js"></script><script>' 
				. "
				$('#input_cp').val('Link para Login: https://probusca.com".'\n'."Usuário: ".$dados['usuario']."".'\n'."Senha: ".$_POST['senha']."');
				$('#modalDetalhes').modal('show');</script>");

			// usuario de teste cadastrado com sucesso
			//die("<script>alert('Usuário cadastrado!'); location.href='".URL."/usuario';</script>");

		}else
			$not = new Notificacao('Falha ao inserir Usuário!', 'Uma falha ocorreu na inserção do usuário. Solicite ajuda com um administrador.');
		

	}

}



	if($_SESSION['tipo'] == 1)
		die("<script>alert('Você não pode cadastrar usuários.'); location.href='".URL."/inicio';</script>");

	// lista de planos
	if($_SESSION['tipo'] == 4){
		$planos = $con->select('*')->from('plano')->executeNGet();
		$credito = 9999999;
	}elseif($_SESSION['tipo'] == 2){
		$planos = $con->select('*')->from('plano')->where("administrador = '".$_SESSION['usuario']."'")->executeNGet();
		$credito = 9999999;
	}elseif($_SESSION['tipo'] == 3){
		$planos = $con->select('*')->from('plano')->where("codigo in (select plano_codigo from revendedor_plano where revendedor_codigo = '".$_SESSION['usuario']."')")->executeNGet();
		$credito = floatval($con->select('sum(valor) as credito')->from('revendedor_credito')->where("usuario = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('credito'));

		if($credito < 0.25)
			die("<script>alert('Você não possui créditos suficientes!'); location.href='".URL."/usuario';</script>");	
	}
	if(count($planos) == 0 ){
		die("<script>alert('Peça para o administrador cadastrar planos antes de adicionar o usuário.'); location.href='".URL."/usuario';</script>");
	}



if($router->param(0)){
	$usu_codigo = intval($router->param(0));
	$usuario = $con->select('*')->from('usuario')->where("codigo = '$usu_codigo'")->limit(1)->executeNGet();
}else{
	$usuario = array();
}


?>


<form action="" id="formulario" enctype="multipart/form-data" method="post">

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li><a href="<?php echo URL; ?>/usuario">Usuário</a></li>
				<li class="active">Cadastrar Usuário</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php if($router->param(0)) echo "Editar"; else echo "Cadastrar"; ?> Usuário</h1>
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
								
								<?php if($_SESSION['tipo'] == 3 && !$router->param(0)){ ?>
								Você possui <b><?php echo $credito; ?> créditos.</b>
								<?php } ?>

							</div>

					</div>
					<div class="panel-body">

						<div class="col-md-12">
				        	<?php if($not) $not->show(); ?>
				        </div>

						<div class="col-sm-6">

							<?php if($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 2 || !$router->param(0)){ ?>
							<div class="form-group">
								<label>Nome*</label>
								<input type="text" value="<?php echo $usuario['nome']; ?>" name="nome" required class="form-control">
							</div>


							<div class="form-group">
								<label>Usuário*</label>
								<input type="text" value="<?php echo $usuario['usuario']; ?>" placeholder="Nome de Usuário" name="usuario" id="usuario" class="form-control">
							</div>

							<?php } ?>

							<div class="form-group">
								<label>Senha*</label>
								<input type="text" required value="" name="senha" id="senha" class="form-control">
							</div>

							<div class="form-group">
								<label>Repita a Senha*</label>
								<input type="text" required value="" name="senha2" id="senha2" class="form-control">
							</div>

						</div>

						<div class="col-sm-6">

							<?php if($_SESSION['usuario'] != $router->param(0)){ ?>

							<div class="form-group">
								<label>Plano</label>

								<select name="plano" id="plano" required class="form-control">
									<option value="">Selecione</option>
									<?php foreach($planos as $p){ ?>
									<option <?php if($usuario['plano'] == $p['codigo']) echo 'selected'; ?> value="<?php echo $p['codigo']; ?>"><?php echo $p['nome']; ?></option>
									<?php } ?>
								</select>

							</div>	

							<?php } ?>

							<?php if(!($router->param(0) > 0) && $_SESSION['tipo'] == 3){ ?>	

							<div class="form-group">
								<label for="">Duração do Plano</label>
								<select name="duracao" id="duracao" onchange="disable_meses();" class="form-control">
									<option value="">Selecione</option>
									<?php if($credito > 0.25){ ?>
									<option value="7">1 semana</option>
									<?php } if($credito > 0.5){ ?>
									<option value="15">15 dias</option>
									<?php } ?>
								</select>
							</div>

							<div class="form-group">
								<label for="">ou</label>
							</div>

							<div class="form-group ">

								<div class="input-group">
									<input type="number" <?php if($credito < 1) echo 'disabled'; ?> id="tempo_maximo" onkeyup="verificar_max();" onkeydown="verificar_max();" required name="tempo_maximo" value="<?php echo $revendedor['max_tempo_teste']; ?>" class="form-control" describedby="basic-addon2">
	  								<span class="input-group-addon" id="basic-addon2">mes(es)</span>
								</div>
								
							</div>

							<?php }elseif(($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4)){ ?>

								<div class="form-group ">

									<label for="">Vencimento</label>
									<input type="text" name="vencimento" value="<?php

									if(date('H:i:s', strtotime($usuario['vencimento'])) == '00:00:01')
										echo date('d/m/Y', strtotime($usuario['vencimento']) -86400);
									else if(date('H', strtotime($usuario['vencimento'])) == '00'){
										echo date('d/m/Y', strtotime($usuario['vencimento']) -86400);
									}

									else if($usuario['vencimento']){
										echo date('d/m/Y', strtotime($usuario['vencimento'])); 
									}


									?>" class="data form-control">
									
								</div>

								<div class="form-group">
									<label for="">Horário</label>
									<input type="text" name="vencimento2"  value="<?php 

									if(substr($usuario['vencimento'], -8) == '00:00:01')
										echo '12:00pm';
									else if(date('H', strtotime($usuario['vencimento'])) == '00'){
										echo '12'.date(':i', strtotime($usuario['vencimento'])).'pm';
									}else if($usuario['vencimento']) 
										echo date('H:i', strtotime($usuario['vencimento']));


									?>" class="horario form-control">
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

	function disable_meses(){

		var duracao = $('#duracao option:selected').val();
		var credito = <?php echo $credito; ?>;

		if(duracao > 0)
			$('#tempo_maximo').attr('disabled', true);
		else{
			if(credito >= 1)
				$('#tempo_maximo').attr('disabled', false);
		}

	}
	
	function verificar_max(){

		var tempo = parseInt($('#tempo_maximo').val());
		$('#tempo_maximo').val(tempo);

		var max = <?php echo intval($credito); ?>;

		if(tempo > 0)
			$('#duracao').attr('disabled', true);
		else
			$('#duracao').attr('disabled', false);

		if(max > 0 && tempo > max){
			alert('Seu crédito só possui crédito para ' + max + ' mês.');
			$('#tempo_maximo').val(max);
			return $('#tempo_maximo').focus();
		}

	}

	function verificar_tipo(val){

		var divs = $('.revendedor-only');

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
		
		}else{
			divs.addClass('esconder');
			
		}

	}
	
	function enviar(){

		var senhaA = $('#senha').val();
		var senhaB = $('#senha2').val();
		var username = $('#usuario').val();

		var duracao = $('#duracao option:selected').val();
		var tempo = parseInt($('#tempo_maximo').val());
		var creditos = 0;

		console.log('duracao', duracao, 'tempo', tempo);

		<?php if(!($router->param(0) > 0) && $_SESSION['tipo'] == 3){ ?>	
		if(duracao == '' && isNaN(tempo))
			return alert('Defina a duração do plano!');
		<?php } ?>

		if(duracao > 0){
			if(duracao == 7) creditos = 0.25;
			if(duracao == 15) creditos = 0.5;
		}else if(tempo > 0){
			creditos = tempo;
		}

		<?php if($router->param(0) > 0){ ?> 
		if(senhaA.length == 0)
			console.log('a senha não será alterada');
		else <?php } ?>if(senhaA.length < 6 || senhaA.length > 16)
			return alert('A senha deve ter entre 6 e 16 caracteres.');

		if(senhaA != senhaB)
			return alert('As senhas não batem.');


		if(username && username.indexOf(' ') >= 0){
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

				if(edit != 0 )
					return $('#formulario').submit();

				<?php if($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 2) echo "return $('#formulario').submit();"; ?>
				
				if(confirm('Serão descontados ' + creditos + ' créditos da sua conta. Autoriza?'))
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