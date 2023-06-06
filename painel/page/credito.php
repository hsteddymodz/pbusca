<?php

if($_POST['phrase']){

	if(!$_SESSION) @session_start();

	if($_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 4) die("Você não pode.");

	include("../class/Conexao.class.php");
	$con = new Conexao();
	$phrase = $con->escape($_POST['phrase']);
	$usuarios = $con->select("usuario")->from('usuario')->where("usuario like '%$phrase%' and (deletado is NULL or deletado = '') and (inativo is null or inativo = '') and vencimento >= NOW()")->executeNGet();

	die(json_encode($usuarios));

}

if($_POST['get_extrato']){

	if(!$_SESSION) @session_start();

	if($_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 4) die("Você não pode.");

	include("../class/Conexao.class.php");
	$con = new Conexao();

	$usuario = $con->escape($_POST['get_extrato']);
	die(json_encode(array('credito' => $con->select('p.nome as plataforma, c.valor as valor, DATE_FORMAT(c.data, "%d/%m/%Y %h:%i") as data, DATE_FORMAT(c.vencimento, "%d/%m/%Y") as vencimento')->from('credito c, plataforma p')->where("c.usuario in (select codigo from usuario where usuario = '$usuario' and (deletado is NULL or deletado = '') and (inativo is null or inativo = '') and vencimento >= date(NOW())) and p.tipo = c.plataforma")->orderby('c.data DESC')->executeNGet())));

}

include("class/Credito.class.php");
include("class/protect.function.php");
protect(array(2,4));
$con = new Conexao();

if($_POST['usuario']){

	$credito = new Credito($con);

	$usuario    = $con->select('codigo')->from('usuario')->where("usuario = '".$con->escape($_POST['usuario'])."' and (deletado is NULL or deletado = '') and (inativo is null or inativo = '') and vencimento >= date(NOW())")->limit(1)->executeNGet('codigo');
	$vencimento = $_POST['vencimento'];

	$add = $credito->add_credito($_POST['credito'], $_POST['plataforma'], $usuario, $vencimento);

	if($add['erro'])
		$msg[] = $add['msg'];
	else
		$msg[] = "Credito adicionado com sucesso!";
	
}

$plataformas = $con->select('*')->from('plataforma')->executeNGet();

?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Créditos</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Créditos</h1>
		</div>
	</div><!--/.row-->
	
	<form action="" enctype="multipart/form-data" method="post">

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					
						
					
				</div>
				<div class="panel-body">

					<?php if(is_array($msg)){ 
						foreach($msg as $m){ ?>
						<div class="col-lg-12 label bg-primary">
							<?= $m; ?>
						</div>
						<?php }
					}
					 ?>

					<div class="col-sm-4">

						<div class="form-group">

						<h3>Adicionar Créditos</h3>
						</div>
				
						<div class="form-group">
							<label for="">Usuário</label>
							<input type="text"  name="usuario" required id="usuario" class="form-control">
						</div>

						<div class="form-group">
							<label for="">Plataforma</label>
							<select name="plataforma" id="plataforma" required class="form-control">
								<option value=""></option>
								<?php foreach($plataformas as $p){ ?>
								<option value="<?= $p['tipo']; ?>"><?= $p['nome']; ?></option>
								<?php } ?>
							</select>
						</div>

						<div class="form-group">
							<label for="">Créditos</label>
							<input type="number" class="form-control" required name="credito">
						</div>

						<div class="form-group">
							<label for="">Data de Vencimento</label>
							<input type="text" class="data form-control" placeholder="00/00/0000" required name="vencimento">
						</div>

						<div class="form-group form-inline">
							<button type="submit"  name="enviar" value="true" onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>';" class="btn btn-primary btn-success">
								<i class="glyphicon glyphicon-floppy-disk"></i> Adicionar
							</button>

						</div>
						
						
					</div>

					<div class="col-sm-8">

						<div class="form-group">
							<h3>Puxar Extrato</h3>
						</div>
						<div class="form-group">
							<label for="">Nome do usuário:</label>
							<input type="text" placeholder="Nome do Usuário" class="form-control" id="extrato">
							
						</div>
						<div class="form-group"><button class="btn btn-primary" type="Button" onclick="puxar_extrato();">Pesquisar</button></div>

						<div class="form-group">
							<table class="table table-bordered table-hover">
								<thead>
									<tr>
										<td>Plataforma</td>
										<td>Crédito</td>
										<td>Data</td>
										<td>Vencimento</td>
									</tr>
								</thead>
								<tbody id="tbody">
									<tr>
										<td></td>
										<td></td>
										<td></td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

				</div>
				
			</div>
		</div>
	</div><!--/.row-->	

	</form>


</div><!--/.main-->
<script>
	
	function puxar_extrato(){

		var usuario = $('#extrato').val();
		show_loading();
		$.post('page/credito.php', {get_extrato:usuario})
		.done(function(r){
			console.log(r);
			end_loading();

			var tbl = $('#tbody');

			tbl.html('');

			r = JSON.parse(r);
			r = r.credito;

			if(r.length == 0)
				return tbl.append("<tr><td colspan=\"4\">Nenhum crédito foi adquirido ou gasto.</td></tr>");

			for(k = 0; k < r.length; k++){
				tbl.append('<tr><td>'+ r[k].plataforma +'</td><td>'+ r[k].valor +'</td><td>'+ r[k].data +'</td><td>'+ r[k].vencimento +'</td></tr>')
			}

		})
		.fail(function(r){
			console.log(r);
			end_loading();
		});

	}
</script>
