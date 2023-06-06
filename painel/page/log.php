<?php

include("class/protect.function.php");
protect(array(4));

$con    = new Conexao();

$router = new Router($_GET['p']);



$registrosPorPagina = 250;



if($router->param(0) > 0)

	$limit = intval($router->param(0)).','.$registrosPorPagina;

else

	$limit = '0,'.$registrosPorPagina;



$total  = $con->select('count(*) as n')->from('login')->limit(1)->executeNGet('n');



if($router->param(1)){



	if($router->param(2) && $router->param(3)){

		// significa q é uma data

		$exp = explode('/', $con->escape($router->param(1)));

		$where = "and date(l.data_login) = '".$router->param(3)."-".$router->param(2)."-".$router->param(1)."' ";

	}else

		$where = "and u.usuario like '%".$con->escape($router->param(1))."%' ";



	$limit = '99999';

	$total = 0;



}else

	$where = '';



$logs   = $con->select('l.*, u.usuario')->from('login l, usuario u')->where("u.codigo = l.usuario $where ")->orderby('ultima_atividade DESC')->limit($limit)->executeNGet();



?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			

	<div class="row">

		<ol class="breadcrumb">

			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>

			<li class="active">Logs</li>

		</ol>

	</div><!--/.row-->



	<div class="row">

		<div class="col-lg-12">

			<h1 class="page-header">Logs</h1>

		</div>

	</div><!--/.row-->





	<div class="row">

		<div class="col-lg-12">

			<div class="panel panel-default">

				<div class="panel-heading">

				

						<div class="form-group form-inline">

				

							<span class="pull-right">

								<input type="text" value="<?php if($router->param(2) && $router->param(3)) echo $router->param(1).'/'.$router->param(2).'/'.$router->param(3); else echo $router->param(1); ?>" id="termo" placeholder="Pesquisar <?php echo $nome_p; ?>..." name="pesquisar" class="form-control input-xs">

								<button type="button" onclick="goto();" class="btn-success btn btn-xs"><i class="glyphicon glyphicon-search"></i> Pesquisar</button>

								<?php if(strlen($router->param(1)) > 0){  ?>

								<button type="button" onclick="location.href='//probusca.com/painel/log';" class="btn btn-default btn-xs">Limpar Pesquisa</button>

								<?php } ?>

							</span>

						</div>

					

				</div>

				<div class="panel-body">



					<div class="form-group text-center">

						<?php



						if($total > 0)

						for($k= 0; $k < ceil($total/$registrosPorPagina); $k++){

							echo '<button onclick="location.href=\'//probusca.com/painel/log/'.$k*$registrosPorPagina.'\';" class="btn btn-xs">'.($k+1).'</button> ';

						}

						?>



					</div>

					

					<table class="table table-bordered table-stripped ">

			            <thead>

			              <tr>

			              	<th>Usuário</th>

			                <th>Data</th>

											<th>IP</th>
											
											<th>Navegador</th>

			              </tr>

			            </thead>

			            <tbody>

			              <?php 

			              if(count($logs) > 0){

			                foreach($logs as $p){ ?>

			                <tr>

			                	<td><?php echo $p['usuario']; ?></td>

			                	<td>

			                		<?php echo date('d/m/Y H:i', strtotime($p['data_login'])); ?> 

			                		<?php if( (time() - 60) < strtotime($p['ultima_atividade']) && $p['ultima_atividade']) echo '<span class="label label-success">Logado</span>'; ?>

			                	</td>

			                	<td><a target="_blank" href="https://check-host.net/ip-info?host=<?php echo $p['ip']; ?>"><?php echo $p['ip']; ?></a></td>

												<td><?php echo $p['browser'] ?></td>


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

		location.href= 'http://probusca.com/painel/log/<?php echo intval($router->param(0)); ?>/' + termo;



	}



</script>