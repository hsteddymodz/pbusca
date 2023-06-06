<?php

/* variaveis de ambiente */
$nome_p      = "Consultas Salvas";
$con         = new Conexao();
$dados       = $con->select('*')->from('consultas_salvas')->executeNGet();

include("class/protect.function.php");
protect(array(4));

$router = new Router($_GET['p']);
if($router->param(0)){

	$id = $router->param(0);
	$dados       = $con->select('resultado')->from('consultas_salvas')->where("codigo = '$id'")->limit(1)->executeNGet('resultado');
	$conteudo = $dados;

}else
	$conteudo = false;

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
			<h1 class="page-header"><?php echo $nome_p; ?> <small><?php if(!$conteudo) echo count($dados); ?></small></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
					
						</div>
					</form>
				</div>
				<div class="panel-body">

					<?php if($conteudo){

						echo '<link href="'.URL.'/css/boavista.css" rel="stylesheet"><link href="'.URL.'/css/styles.css" rel="stylesheet">';
						echo $conteudo;

					} else { ?>

					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			              	<th>CPF</th>
			                <th></th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($dados) > 0){
			                foreach($dados as $p){ ?>
			                <tr>
			                  <td valign="center"><?php echo $p['cpf']; ?></td>
			                  <td class="text-right">
			                  	
			                    <button onclick="window.open('<?php echo URL; ?>/consulta_salva/<?php echo $p['codigo']; ?>', '_blank');" class="btn btn-primary btn-xs">Ver</button>
			                  
			                  	
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
			          <?php } ?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->