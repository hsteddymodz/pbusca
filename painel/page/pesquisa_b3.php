<?php


include('class/RegistrarConsulta.php');
include('class/Conexao.class.php');
include('class/Token.class.php');
include('class/LimitarConsulta.function.php');

$TIPO_CONSULTA = 'spc';

$con = new Conexao();
limitarConsulta($con, $_SESSION['usuario'], $TIPO_CONSULTA);


if($_POST['info']){

	$token = new Token();
	$token = $token->get_token();

	?>
	<script>

		function gravarVisualizacaoConsulta(a,b,c){

		}

		function getCookie(a){

		}
	
		window.onload = function(){

			function do_consulta(first_try = true){

				show_loading();
				$.ajax({
					type:'POST', 
					url:'<?= $_SESSION['endpoint']; ?>/buscaB3', 
					data:{token:'<?= $token; ?>', doc:'<?= $_POST['info']; ?>'}, 
					dataType:"json"
				}).done(function(r){

					console.log(r);
					end_loading();

					if(r.erro > 0)
						return alert(r.msg);

					$('#resultado').html(r.resultado);

				}).fail(function(r){

					console.log(r);
					end_loading();

					if(first_try)
						do_consulta(false);

				});

			}

		
			do_consulta();
		};

	</script>
	<?php
}

?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Consulta</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Consulta</h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="page/imprimir_v.php"  target="_blank" method="post">
						<div class="form-group form-inline">

							<button onclick="location.href='<?php echo URL; ?>';" type="button" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>

							<?php if($_POST['info']){ ?>
								<button type="button" class="btn btn-xs btn-primary" onclick="location.href='<?php echo URL; ?>/pesquisa_b3';">Realizar nova Pesquisa</button>

							<?php } ?>

						</div>

					</form>

				</div>

				<div class="panel-body">
					<?php 

					if($_POST['info']){
						echo '<div id="resultado"></div>';
					}
					else{ ?>

						<form action="" method="post">
							<div class="form-group col-lg-4">
					        	<label id="titulo">CPF ou CNPJ</label>
					        	<input type="text" name="info" required id="info" class=" form-control" placeholder="Digite um CPF ou um CNPJ">
					        	<br>
					        	<p>
					        		<button type="submit" class="btn btn-primary">Pesquisar</button>
					        	</p>
					        </div>
						</form>
						<?php

					}
					?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	

</div><!--/.main-->

<!-- Modal -->
<div id="modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Extrato</h4>
      </div>
      <div class="modal-body">
        <div class="form-group" id="conteudo"></div>
        <div class="clearfix"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>

  </div>
</div>
