<?php

include("class/protect.function.php");
protect(array(2,3,4));

$con    = new Conexao();

$router = new Router($_GET['p']);



$registrosPorPagina = 1000;



if($router->param(0) > 0)

	$limit = intval($router->param(0)).','.$registrosPorPagina;

else

	$limit = '0,'.$registrosPorPagina;







$filtro_tipo = "";

if($_SESSION['tipo'] == 2)

	$filtro_tipo = " and u.administrador = '".$_SESSION['usuario']."' ";

elseif($_SESSION['tipo'] == 3)

	$filtro_tipo = " and u.revendedor = '".$_SESSION['usuario']."' ";

elseif($_SESSION['tipo'] == 4)

	$filtro_tipo = "";



$total  = $con->select('count(*) as n')->from('usuario_consulta uc, usuario u')->where("uc.usuario > 0 and u.codigo = uc.usuario and u.deletado is null ".$filtro_tipo)->limit(1)->executeNGet('n');

	

if($_POST['pesquisar'] || $_POST['plataforma']){



	$termo = ' 1=1 ';

	if($_POST['pesquisar']){

		$termo = " u.usuario like '%".$con->escape($_POST['pesquisar'])."%' ";

	}



	if($_POST['data']){

		if(substr_count($_POST['data'], '/')  == 2){

			$exp = explode('/', $_POST['data']);

			$termo .= " and date(l.data) = '$exp[2]-$exp[1]-$exp[0]' ";

		}

	}



	if($_POST['plataforma'])

		$plataforma = " and p.codigo = '".intval($_POST['plataforma'])."' ";

	else

		$plataforma = "";



	$logs   = $con

	->select('l.*, u.usuario, p.nome as plataforma')

	->from('usuario_consulta l, usuario u, plataforma p')

	->where("u.codigo = l.usuario and u.deletado is null $filtro_tipo and l.usuario > 0 $plataforma and p.tipo = l.plataforma and ($termo)")

	->orderby('l.rel_codigo DESC')

	->executeNGet();



	$total = count($logs);



}else

	$logs   = $con

	->select('l.*, u.usuario, p.nome as plataforma')

	->from('usuario_consulta l, usuario u, plataforma p')

	->where("u.codigo = l.usuario and u.deletado is null $filtro_tipo and l.usuario > 0 and p.tipo = l.plataforma")

	->orderby('l.rel_codigo DESC')

	->limit($limit)

	->executeNGet();



$plataformas = $con->select('*')->from('plataforma')->orderby('nome asc')->executeNGet();

?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			

	<div class="row">

		<ol class="breadcrumb">

			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>

			<li class="active">Histórico de Consultas</li>

		</ol>

	</div><!--/.row-->



	<div class="row">

		<div class="col-lg-12">

			<h1 class="page-header">Histórico de Consultas</h1>

		</div>

	</div><!--/.row-->





	<div class="row">

		<div class="col-lg-12">

			<div class="panel panel-default">

				<div class="panel-heading">

				



						<div class="form-group form-inline">

				

							<span class="pull-right">

								<form action="" method="post">

									<label for="">Filtrar</label>

									<input type="text" value="<?php echo $_POST['pesquisar']; ?>" id="termo" placeholder="Usuário" name="pesquisar" class="form-control input-xs">

									<select name="plataforma" id="plataforma" class="form-control input-xs">

											<option value="">Plataforma</option>

											<?php foreach($plataformas as $pl){?>

											<option <?php if($pl['codigo'] == $_POST['plataforma']) echo 'selected'; ?> value="<?php echo $pl['codigo']; ?>"><?php echo $pl['nome']; ?></option>

											<?php } ?>

									</select>

									<input type="text" value="<?php echo $_POST['data']; ?>"  placeholder="00/00/0000" name="data" class="form-control input-xs">

									<button type="submit" class="btn-success btn btn-xs"><i class="glyphicon glyphicon-search"></i> Pesquisar</button>

									<?php if($_POST){  ?>

								<button type="button" onclick="location.href='<?php echo URL; ?>/historico';" class="btn btn-default btn-xs">Limpar Pesquisa</button>

								<?php } ?>

								</form>

								

							</span>

						</div>

					

				</div>

				<div class="panel-body">



					<?php if(!$_POST['pesquisar'] && !$_POST['plataforma']){ ?>



					<div class="form-group text-center">

						<?php



						if($total > 0)

						for($k= 0; $k < ceil($total/$registrosPorPagina); $k++){

							echo '<button onclick="location.href=\''.URL.'/historico/'.$k*$registrosPorPagina.'\';" class="btn btn-xs">'.($k+1).'</button> ';

						}

						?>



					</div>

					<?php } ?>

					

					<table class="table table-bordered table-stripped ">

			            <thead>

			              <tr>

			              	<th>Usuário</th>

			              	<th>Plataforma</th>

			                <th>Data</th>

			                

			              </tr>

			            </thead>

			            <tbody>

			            	<p>Total de <b><?= $total; ?></b> consultas</p>

			              <?php 

			              if(count($logs) > 0){

			                foreach($logs as $p){ ?>

			                <tr>

			                	<td><?php echo $p['usuario']; ?></td>

			                	<td><?php echo $p['plataforma']; ?></td>

			                	<td><?php echo date('d/m/Y H:i', strtotime($p['data'])); ?></td>

			                	



			                </tr>

			                <?php 

			                }

			              }else{ ?>

			              <tr>

							<td class="text-center" colspan="3">Nenhum registro encontrado.</td>

			              </tr>

			              <?php

			              }

			              ?>

			            </tbody>

			          </table>

				</div>

			</div>

		</div>

	</div><!--/.row-->	





</div><!--/.main-->



<script>	

	

	function goto(){



		var termo = $('#termo').val();

		location.href= '<?php echo URL; ?>/historico/<?php echo intval($router->param(0)); ?>/' + termo + '/' + $('#plataforma option:selected').val();



	}



</script>