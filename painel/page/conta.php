<?php
die();
include("class/protect.function.php");
protect(array(3));

/* variaveis de ambiente */
$nome_p      = "Suas Contas Bancárias";
$tabela      = "plataforma";

$con         = new Conexao();
$dados       = $con->select('*')->from('conta_bancaria')->executeNGet();

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
							<button type="button" onclick="location.href='<?php echo URL; ?>/conta-cadastrar';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i> Adicionar Conta</button>
						</div>
					</form>
				</div>
				<div class="panel-body">

					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			              	<th>Banco</th>
			              	<th>Agência</th>
			              	<th>Conta</th>
			              	<th>Observação</th>
			                <th></th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($dados) > 0){
			                foreach($dados as $p){ ?>
			                <tr>
			                  <td><?php echo $p['banco']; ?></td>
			                  <td><?php echo $p['agencia']; ?></td>
			                  <td><?php echo $p['conta']; ?></td>
			                  <td><?php echo $p['observacao']; ?></td>	
			                  <td class="text-right">
			                  	
			                    <button onclick="location.href='<?php echo URL; ?>/conta-cadastrar/<?php echo $p['codigo']; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i> Editar</button>
			      
			                  	<button onclick="location.href='<?php echo URL; ?>/deletar/conta_bancaria/<?php echo $p['codigo']; ?>';" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i> remover</button>
			                  </td>
			                </tr>
			                <?php 
			                }
			              }else{ ?>
			              <tr>
			              	<?php if(strlen($_POST['pesquisar']) > 0){  ?>
			                <td class="text-center" colspan="5">Nenhum registro encontrado para o termo <strong><?php echo $_POST['pesquisar']; ?></strong>.</td>
			                <?php } else { ?>
							<td class="text-center" colspan="5">Nenhum registro encontrado.</td>
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