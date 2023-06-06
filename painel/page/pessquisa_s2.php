
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Pessoa Física</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Pessoa Física <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">

					<?php

$busca = false;
if($_POST['form']){

	include('class/SUS.class.php');

	$busca = true;
	$_POST['dataNascimento'] = date('d/m/Y', strtotime($_POST['dataNascimento']));

	$retorno = buscarSUS($_POST, $_POST['identico']);

	if($retorno['erro']){
		echo "<script>alert('".$retorno['erro']."');</script>";
	}else if($retorno['total'] > 0){

		// registra a consulta
		include('class/Conexao.class.php');
		$con = new Conexao();
		$con->insert('usuario_consulta', array('usuario'=>$_SESSION['usuario'], 'plataforma'=>'s', 'data'=>'NOW()'));

		$codigo_consulta = $con->getCodigo();

	}

}
?>

					
					<?php if($busca){ ?>

						<div class="form-group text-right">
				        	<button onclick="location.href='<?php echo URL; ?>/pesquisa_s';" class="btn btn-default">Pesquisar Novamente</button>
				        </div>

						<table id="tabela" class="table table-bordered table-hover table-stripped ">
				            <thead>
				              <tr>
				                <th>Nome</th>
				                <th>Mãe</th>
				                <th>Pai</th>
				                <th>Sexo</th>
				                <th>Nascimento</th>
				                <th>País de Nascimento</th>
				                <th>Município de Nascimento</th>

				              </tr>
				            </thead>
				            <tbody>
				            	<?php if(is_array($retorno['registro'])){ foreach($retorno['registro'] as $pessoa){ ?> 
					              	<tr onclick="return mais_info('<?php echo $pessoa['numeroCns']; ?>');">
						              	<td><?php echo exibir($pessoa['nome']); ?></td>
						              	<td><?php echo exibir($pessoa['nomeMae']); ?></td>
						              	<td><?php echo exibir($pessoa['nomePai']); ?></td>
						              	<td><?php echo exibir($pessoa['sexo']); ?></td>
						              	<td><?php echo exibir($pessoa['dataNascimento']); ?></td>
						              	<td><?php echo exibir($pessoa['paisNascimento']); ?></td>
						              	<td><?php echo exibir($pessoa['municipioNascimento']); ?></td>
					              	</tr>
					            <?php } } else{ ?>
									<tr>
										<td colspan="7">Nenhum resultado encontrado.</td>
									</tr>
					            <?php } ?>
				            </tbody>
				        </table>
				        
			        <?php }else{ ?>
					<form action="" method="post">
						
						<input type="hidden" name="form" value="true">

						<div class="form-group col-sm-4">
							<label for="">Nome</label>
							<input type="text" onchange="disable_inputs();" name="nome" class="form-control">
						</div>
						
						<!--
						<div class="form-group col-sm-4">
							<label for="">Apelido</label>
							<input type="text" onchange="disable_inputs();" name="apelido" class="form-control">
						</div>-->

						<div class="form-group col-sm-4">
							<label for="">Nome da Mãe</label>
							<input type="text" onchange="disable_inputs();" name="nomeMae" class="form-control">
						</div>

						<div class="form-group col-sm-3">
							<label for="">Nome do Pai</label>
							<input type="text" onchange="disable_inputs();" name="nomePai" class="form-control">
						</div>

						<div class="form-group col-sm-3">
							<label for="">Data de Nascimento</label>
							<input type="date" onchange="disable_inputs();" name="dataNascimento" class="data form-control">
						</div>

						<div class="form-group col-sm-3">
							<label for="">CPF</label>
							<input type="text" onchange="disable_inputs();" name="cpf" class="onlyNumbers form-control">
						</div>

						<div class="clearfix"></div>

						<div class="form-group text-center">
							<button type="submit" name="identico" value="true" class="btn btn-primary">Pesquisar Idênticos</button>
							<button type="submit" name="similar"  value="true"  class="btn btn-primary">Pesquisar Similares</button>
						</div>


					</form>
			        <?php } ?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->

<div class="modal fade" id="modal_s" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Resultado</h4>
      </div>
      <div class="modal-body">


		<div id="result_final"></div>

		<div class="form-group">
			<form action="<?php echo URL; ?>/page/imprimir.php" target="_Blank" method="post">
				<input type="hidden" name="consulta" value="<?php echo $codigo_consulta; ?>">
				<input type="hidden" name="usuario" value="<?php echo $_SESSION['usuario']; ?>">
				<input type="hidden" name="info" id="info" value="">
				<button type="submit"  class="btn btn-default">Imprimir</button>
			</form>
		</div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
	
	function disable_inputs(){

		//var nomeMae, dataNasc;

	}

	function mais_info(cns){	

		if(1){

			show_loading();
			$('#info').val(cns);

			$.post('<?php echo URL; ?>/page/webservice_cns.php', {info:cns, consulta:<?php echo $codigo_consulta; ?>, usuario:<?php echo $_SESSION['usuario']; ?>}).done(function(res){

				end_loading();

				if(res == 'error'){
					alert('Nada encontrado');
				}else{
					$('#result_final').html(res);
					$('#modal_s').modal('show');
				}

			}).fail(function(res){
				console.log(res);
			});

		}else{
			console.log('teste');
		}

	}

</script>

<style>
	#tabela tr:hover{
		background-color:#eaeaea;
		cursor:pointer;
	}
</style>


