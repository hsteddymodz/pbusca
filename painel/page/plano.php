<?php

if($_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 4)
	die("<script>location.href='".URL."';</script>");

include("class/protect.function.php");
protect(array(2,4));

/* variaveis de ambiente */
$nome_p      = "Planos";
$tabela      = "plano";
$pk          = "codigo";
$col_mostrar = array("nome"=>"Nome");
$col_pesquisar = $col_mostrar;
$pagina_cad  = "plano-cadastrar";

function show_info($d, $n){

	switch($n){

		default:
			return $d;

	}
}

$con         = new Conexao();

$sql_adicional = "1 = 1";
if($_SESSION['tipo'] == 2)
	$sql_adicional = " usuario.codigo = plano.administrador and plano.administrador = '".$_SESSION['usuario']."'";

if($_SESSION['tipo'] == 4){
	$sql_adicional .= " and usuario.codigo = plano.administrador ";
}

if($_POST['pesquisar']){

	$where = '';
	$q     = $con->escape($_POST['pesquisar']);

	foreach($col_pesquisar as $col=>$x) $where .= " $col like '%{$q}%' or";

	$where = substr($where, 0, -2);

	$dados       =  $con
	              ->select('plano.*, usuario.nome as quemcadastrou')
	              ->from($tabela.", usuario")
	              ->where($sql_adicional . " and (".$where.")")
	              ->executeNGet();
}else
	$dados       =  $con
	              ->select('plano.*, usuario.nome as quemcadastrou')
	              ->from($tabela.", usuario")
	              ->where( $sql_adicional )
	              ->executeNGet();


?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active"><?php echo $nome_p; ?></li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo $nome_p; ?> <small><?php echo count($dados); ?></small></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i> Adicionar</button>

							<span class="pull-right">
								<input type="text" value="<?php echo $_POST['pesquisar']; ?>" placeholder="Pesquisar <?php echo $nome_p; ?>..." name="pesquisar" class="form-control input-xs">
								<button type="submit" class="btn-success btn btn-xs"><i class="glyphicon glyphicon-search"></i> Pesquisar</button>
								<?php if(strlen($_POST['pesquisar']) > 0){  ?>
								<button type="button" onclick="location.href='<?php echo "http://" . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']; ?>';" class="btn btn-default btn-xs">Limpar Pesquisa</button>
								<?php } ?>
							</span>
						</div>
					</form>
				</div>
				<div class="panel-body">
					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			              	<?php foreach($col_mostrar as $c) echo "<th>$c</th>"; ?>
			              	<?php if($_SESSION['tipo'] == 4){
			                  	?>
			                  	<th>Quem cadastrou</th>
			                  	<?php
			                  } ?>
			                <th></th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($dados) > 0){
			                foreach($dados as $p){ ?>
			                <tr class="<?php if($p['inativo'] == 1) echo 'bg-warning'; ?>">
			                  <?php foreach($col_mostrar as $k=>$c) echo "<td>".show_info($p[$k], $k)."</td>"; ?>
			                  <?php if($_SESSION['tipo'] == 4){
			                  	?>
			                  	<td><?php echo $p['quemcadastrou']; ?></td>
			                  	<?php
			                  } ?>
			                  <td class="text-right">
			                  	<?php if(/*$_SESSION['usuario']->getCodigo() != $p[$pk]*/ 1){ ?>
			                    <button onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>/<?php echo $p[$pk]; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i> Editar</button>
			                  	<button onclick="location.href='<?php echo URL; ?>/deletar/plano/<?php echo $p[$pk]; ?>';" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Deletar</button>
			                  	<?php } ?>
			                  </td>
			                </tr>
			                <?php 
			                }
			              }else{ ?>
			              <tr>
			              	<?php if(strlen($_POST['pesquisar']) > 0){  ?>
			                <td class="text-center" colspan="<?php echo count($col_mostrar)+1; ?>">Nenhum registro encontrado para o termo <strong><?php echo $_POST['pesquisar']; ?></strong>.</td>
			                <?php } else { ?>
							<td class="text-center" colspan="<?php echo count($col_mostrar)+1; ?>">Nenhum registro encontrado.</td>
			            	<?php } ?>
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