<?php

include("class/protect.function.php");

protect(array(1));

$con    = new Conexao();
$router = new Router($_GET['p']);

$registrosPorPagina = 250;
$limit = '0,'.$registrosPorPagina;

$total  = $con
	->select('count(*) as n')
	->from('login')
	->where("usuario = '{$_SESSION['usuario']}'")
	->limit(1)
	->executeNGet('n');

$where = '';
if(isset($_POST)) {
	if(isset($_POST['pesquisar']) && strlen($_POST['pesquisar']) > 0){
		$tmp = explode('/', $_POST['pesquisar']);
		$tmp[0] = intval($tmp[0]);
		$tmp[1] = intval($tmp[1]);
		$tmp[2] = intval($tmp[2]);

		if($tmp[0] > 0 && $tmp[1] > 0 && $tmp[2] > 0){
			$where = "and date(l.data_login) = '".$tmp[2]."-".$tmp[1]."-".$tmp[0]."' ";
			$limit = '99999';
			$total = 0;
		}else
			echo '<script>alert("Data inválida!");</script>';
	}
	if(isset($_POST['page']))
		$limit = intval($_POST['page']).', '.$registrosPorPagina;
}

$logs = $con
	->select('l.*, u.usuario')
	->from('login l, usuario u')
	->where("u.codigo = l.usuario AND u.codigo = '{$_SESSION['usuario']}' $where ")
	->orderby('data_logout ASC LIMIT '.$limit)
	->executeNGet();


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
				<form action="" method="post">
				<div class="panel-heading">
						<div class="form-group form-inline">
							<span class="pull-right">
								<input type="text" value="<?php if(isset($_POST['pesquisar'])) echo $_POST['pesquisar']; ?>" id="termo" placeholder="Filtrar por data..." name="pesquisar" class="form-control input-xs">
								<button type="submit" class="btn-success btn btn-xs"><i class="glyphicon glyphicon-search"></i> Pesquisar</button>
								<?php if(isset($_POST['pesquisar'])){  ?>
								<button type="button" onclick="location.href='//probusca.com/painel/log_cliente';" class="btn btn-default btn-xs">Limpar Pesquisa</button>
								<?php } ?>
							</span>
						</div>
				</div>
				</form>

				<div class="panel-body">
					<div class="form-group text-center">
						<form action="" method="post">
							<input type="hidden" name="pesquisar" value="<?php if(isset($_POST['pesquisar'])) echo $_POST['pesquisar']; ?>">
							<?php
							if($total > 0)
								for($k= 0; $k < ceil($total/$registrosPorPagina); $k++){
									echo '<button type="submit" name="page" value="'.$k*$registrosPorPagina.'" class="btn btn-xs">'.($k+1).'</button> ';
								}
							?>
						</form>
					</div>
					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			                <th>Data de Login</th>
			                <th>Data de Logout</th>
			                <th>IP</th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($logs) > 0){
			                foreach($logs as $p){
			              	?>
			                <tr>
			                	<td>
			                		<?php echo date('d/m/Y H:i', strtotime($p['data_login'])); ?> 
			                		<?php if((strtotime($p['data_login'])+60*60*3) > time() && !$p['data_logout']) echo '<span class="label label-success">Logado</span>'; ?>
			                	</td>
			                	<td><?php if($p['data_logout']) echo date('d/m/Y H:i', strtotime($p['data_logout'])); ?> </td>
			                	<td><a target="_blank" href="https://check-host.net/ip-info?host=<?php echo $p['ip']; ?>"><?php echo $p['ip']; ?></a></td>
			                </tr>
			                <?php 
			                }
			              }else{ ?>
			              <tr>
							<td class="text-center" colspan="3">Nenhum registro encontrado.</td>
			              </tr>
			              <?php } ?>
			            </tbody>
			          </table>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->