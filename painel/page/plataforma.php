<?php

if($_POST['manutencao']){

	include("../class/Conexao.class.php");
	include("../class/protect.function.php");
	protect(array(4));
	$con = new Conexao();
	$codigo = intval($_POST['manutencao']);

	$con->update('plataforma', array('manutencao'=>intval($_POST['status'])), $codigo, 'codigo');
	die(json_encode(array('ok'=>1)));
	

}	

include("class/protect.function.php");
protect(array(4));

/* variaveis de ambiente */
$nome_p      = "Plataformas";
$tabela      = "plataforma";

$con         = new Conexao();
$dados       = $con->select('*')->from('plataforma')->executeNGet();


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
							As plataformas precisam ser criadas por um desenvolvedor.
						</div>
					</form>
				</div>
				<div class="panel-body">

					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			              	<th>Logo</th>
			              	<th>Nome</th>
			                <th></th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($dados) > 0){
			                foreach($dados as $p){ ?>
			                <tr<?php if($p['manutencao'] != '1'){ ?>class="bg-danger"<?php } ?>>
			                  <td width="30%"><img class="img-responsive" src="<?php echo URL.'/upload/'.$p['logo']; ?>"></td>
			                  <td valign="center">
			                  	<?php echo $p['nome']; ?>

			                  </td>
			                  <td class="text-right form-inline">
			                  	
			                    <p><button onclick="location.href='<?php echo URL; ?>/plataforma-cadastrar/<?php echo $p['codigo']; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i> Editar</button></p>
								
								<p>
									<select name="status" id="status" onchange="manutencao(<?= $p['codigo']; ?>, this);" class="form-control">
										<option value="0">Ativo</option>
										<option <?php if($p['manutencao'] == 1) echo 'selected'; ?> value="1">Em manutenção</option>
										<option <?php if($p['manutencao'] == 2) echo 'selected'; ?> value="2">Atualizando</option>
										<option <?php if($p['manutencao'] == 3) echo 'selected'; ?> value="3">Online</option>
									</select>
								</p>
			                  	
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
<script>
	function manutencao(codigo, btn){

		show_loading();
		$.post('page/plataforma.php', {manutencao:codigo, status:$(btn).val()})
		.done(function(r){

			console.log(r);
			r = JSON.parse(r);

			end_loading();

		}).fail(function(r){
			console.log(r);
			end_loading();
		});
	}
</script>