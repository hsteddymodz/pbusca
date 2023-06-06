<?php

include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Usuario.class.php');

$con    = new Conexao();
$router = new Router($_GET['p']);
$not    = false;

if($_POST['salvar']){

	$dados = array();
	$dados['nome'] = $_POST['nome'];
	$dados['preco']= str_replace(',', '.', $_POST['preco']);

	$codigo = intval($router->param(0));

	if($codigo > 0){

		$plano = $codigo;
		$con->update('plano', $dados, $plano);

	}else{

		$dados['administrador'] = $_SESSION['usuario'];
		$plano = $con->insert('plano', $dados);

	}
		
	if(is_array($_POST['numero'])){
		foreach($_POST['numero'] as $pl=>$num){

			$num = intval($num);

			if(1){
				$res = $con->insert('plano_plataforma', array(
					'plataforma'=>$pl, 
					'numero'=>$num,
					'plano'=>$plano
					)
				);
			}
		}
	}

	if($res){
		die("<script>alert('Cadastrado!'); location.href='".URL."/plano';</script>");
	}else
      	$not = new Notificacao('Falha ao salvar.', 'Falha!', 'danger');

}


$plataformas = $con
				->select('*')
				->from('plataforma')
				->executeNGet();

$plan_codigo = intval($router->param(0));

$dados_plano = $con->select('nome, preco, administrador')->from('plano')->where("codigo = '$plan_codigo'")->limit(1)->executeNGet();

if($dados_plano['administrador'] != $_SESSION['usuario'] && $_SESSION['tipo'] != 4 && $plan_codigo)
	die("<script>alert('Você não pode editar esse plano.'); location.href='".URL."/plano';</script>");

$plataformas_definidas = $con->select('*')->from('plano_plataforma')->where("plano = '$plan_codigo'")->executeNGet();

$pl_final = array();
foreach($plataformas_definidas as $pd){
	$pl_final[$pd['plataforma']] = $pd;
}

?>


<form action="" id="formulario" enctype="multipart/form-data" method="post">

	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li><a href="<?php echo URL; ?>/pagina">Páginas</a></li>
				<li class="active">Alterar Página</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Alterar Página</h1>
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


						<div class="col-xs-6">

							<input type="hidden" name="salvar" value="true">

							<div class="form-group">
								<label>Nome do Plano</label>
								<input type="text" value="<?php echo $dados_plano['nome']; ?>" name="nome" required class="form-control">
							</div>

							<div class="form-group">
								<label>Preço</label>
								<input type="text" value="<?php echo $dados_plano['preco']; ?>" name="preco" required class="form-control">
							</div>

							<div class="form-group">
								<h3>Plataformas e Consultas</h3>
							</div>

							<div class="form-group">


								<table class="table table-hover">
									<tr>
										<th>Plataforma</th>
										<th>Consultas por Dia</th>

									</tr>
									<?php foreach($plataformas as $p){ ?>
									<tr>
										<td><?php echo $p['nome']; ?></td>
										<td><input type="number" min="0" name="numero[<?php echo $p['codigo']; ?>]" value="<?php if($pl_final[$p['codigo']]['numero'] > 0) echo $pl_final[$p['codigo']]['numero']; ?>" class="form-control"></td>
									
									</tr>
									<?php } ?>


								</table>
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