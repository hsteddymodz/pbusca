<?php

include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Usuario.class.php');

$con    = new Conexao();
$router = new Router($_GET['p']);
$not    = false;

if($_POST['salvar']){

	unset($_POST['salvar']);
	if($router->param(0)){
		$con->update('conta_bancaria', $_POST, $router->param(0));
		die("<script>alert('Conta alterada!'); location.href='".URL."/conta'; </script>");
	}else{
		$_POST['usuario'] = $_SESSION['usuario'];
		$_POST['data'] = date('Y-m-d H:i:s');
		$con->insert('conta_bancaria', $_POST);
		die("<script>alert('Conta cadastrada!'); location.href='".URL."/conta'; </script>");
	}


		

}

$cc = intval($router->param(0));
$dados = $con->select('*')->from('conta_bancaria')->where("codigo = '$cc'")->limit(1)->executeNGet();

?>


<form action="" id="formulario" enctype="multipart/form-data" method="post">

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li><a href="<?php echo URL; ?>/conta">Contas Bancárias</a></li>
				<li class="active">Alterar Contas Bancárias</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Alterar Conta Bancária</h1>
			</div>
		</div><!--/.row-->


		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						

							<input type="hidden" name="salvar" value="true">
	
							<div class="form-group form-inline">
								<button type="button" onclick="location.href='<?php echo URL; ?>/plano';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>

								<button type="submit"  class="btn btn-primary btn-xs">
									<i class="glyphicon glyphicon-floppy-disk"></i> Salvar
								</button>
		
							</div>

					</div>
					<div class="panel-body">

						<div class="col-md-12">
				        	<?php if($not) $not->show(); ?>
				        </div>


						<div class="col-xs-4">

							<input type="hidden" name="salvar" value="true">

							<div class="form-group">
								<label>Banco</label>
								<input type="text" value="<?php echo $dados['banco']; ?>" required name="banco" required class="form-control">
							</div>

							<div class="form-group">
								<label>Agência</label>
								<input type="text" value="<?php echo $dados['agencia']; ?>" required name="agencia" required class="form-control">
							</div>

							<div class="form-group">
								<label>Conta</label>
								<input type="text" value="<?php echo $dados['conta']; ?>" required name="conta" required class="form-control">
							</div>

							<div class="form-group">
								<label>Observação</label>
								<input type="text" value="<?php echo $dados['observacao']; ?>" name="observacao" required class="form-control">
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


		var senhaA = $('#aut_senha').val();
		var senhaB = $('#aut_senha2').val();

		<?php if($router->param(0) > 0){ ?> 
		if(senhaA.length == 0)
			console.log('a senha não será alterada');
		else <?php } ?>if(senhaA.length < 6 || senhaA.length > 16)
			return alert('A senha deve ter entre 6 e 16 caracteres.');

		if(senhaA != senhaB)
			return alert('As senhas não batem.');

		$('#formulario').submit();

	}

</script>