<?php

include('class/Conexao.class.php');
include('class/LimitarConsulta.function.php');
include('class/onlyNumbers.function.php');

$con = new Conexao();
if(!$_SESSION) @session_start();

//limitarConsulta($con, $_SESSION['usuario'], 'b');

?>

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
							<?php if(!$_POST['cpf']) echo "Entre com as informações para pesquisar"; ?>
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">

					<?php if($_POST){ ?>
					<div class="form-group text-right">
				    	<button type="button" onclick="Print('<?php echo $_POST['cpf']; ?>');" class="btn btn-primary">Imprimir</button>
				    	<button onclick="location.href='<?php echo URL; ?>/pesquisa_b';" class="btn btn-default">Pesquisar Novamente</button>
				    </div>
					<?php  } ?>

					<div id="resultado">

					<?php if($_POST['cpf']){ ?>

						
							<?php 
							if(!$n)
								$resultado = $p->executar();
							else
								$resultado = $n['resultado'];

							if($resultado != false){
								if(!$n) $con->insert('consultas_salvas', array('cpf'=>onlyNumbers($_POST['cpf']), 'plataforma'=>'b', 'resultado'=>$resultado));
								
								include('class/RegistrarConsulta.php');
								$codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'b');

								echo $resultado;
							} ?>
						
				        
			        <?php }elseif($_POST['nome']){ 

			        	$retorno = $p->executar($_POST);
			        	if($retorno == false){
			        		echo "Nada encontrado!";
			        	}else{
			        		if(!$n) $con->insert('consultas_salvas', array('cpf'=>onlyNumbers($_POST['cpf']), 'plataforma'=>'b', 'resultado'=>$retorno));
			        		
			        		include('class/RegistrarConsulta.php');
							$codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'b');

							echo $retorno;
			        	}
			        		
			        }else{ ?>

			        <div class="form-group">
			        	<p>Tipo de Pesquisa</p>
			        	<label for="tpesquisa1"><input onchange="modificarTipo();" value="cpf" id="tpesquisa1" name="tpesquisa" type="radio"> por CPF</label><br>
			        	<label for="tpesquisa2"><input onchange="modificarTipo();" value="nome" id="tpesquisa2" name="tpesquisa" type="radio"> por NOME</label>
			        </div>
			        <form id="formNome" class="esconder" action="" method="post">

						<div class="form-group col-sm-3">
							<label for="">CPF</label>
							<input pattern=".{11,}" required title="Mínimo de 11 caracteres" type="number" name="cpf" class="onlyNumbers form-control">
						</div>

						<div class="clearfix"></div>

						<div class="form-group text-center">
							<button type="submit" name="identico" value="true" class="btn btn-primary">Pesquisar</button>
						</div>


					</form>

					<form id="formCpf" class="esconder" action="" method="post">

						<div class="form-group col-sm-8">
							<label for="">Nome</label>
							<input type="text" required name="nome" class="form-control">
						</div>
						<div class="form-group col-sm-4">
							<label for="">Data de Nascimento</label>
							<input type="text" required name="dtNascimento" class="data form-control">
						</div>

						<div class="form-group col-sm-4">
							<label for="">UF</label>
							<select onchange="consultarCidades(this.value);" class="form-control" name="uf" id="uf">
								<option selected="" value="">Selecione o UF</option>
							<option value="AC">AC</option><option value="AL">AL</option><option value="AM">AM</option><option value="AP">AP</option><option value="BA">BA</option><option value="CE">CE</option><option value="DF">DF</option><option value="ES">ES</option><option value="GO">GO</option><option value="MA">MA</option><option value="MG">MG</option><option value="MS">MS</option><option value="MT">MT</option><option value="PA">PA</option><option value="PB">PB</option><option value="PE">PE</option><option value="PI">PI</option><option value="PR">PR</option><option value="RJ">RJ</option><option value="RN">RN</option><option value="RO">RO</option><option value="RR">RR</option><option value="RS">RS</option><option value="SC">SC</option><option value="SE">SE</option><option value="SP">SP</option><option value="TO">TO</option></select>
						</div>

						<div class="form-group col-sm-8">
							<label for="">Cidade</label>
							<select name="cidade" id="cidade" class="form-control"></select>
						</div>

						<div class="clearfix"></div>

						<div class="form-group text-center">
							<button type="submit" name="identico" value="true" class="btn btn-primary">Pesquisar</button>
						</div>


					</form>
			        <?php } ?>

			        </div>
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

<div class="modal fade" id="resultado_cns" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Consulta Detalhada</h4>
      </div>
      <div class="modal-body">
		
		<p id="table_cns">
			
		</p>

		<div class="form-group">
			<button type="button" class="btn btn-default" id="botao_imprimir">Imprimir</button>
		</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>

	function Print(c)
    {
        window.open('<?php echo URL; ?>/page/imprimir_consulta_b.php?pesq='+c);
    }


	function modificarTipo(){

		var opc = $('input[name=tpesquisa]:checked').val();
		//alert(opc);
		if(opc == 'nome'){
			$('#formCpf').removeClass('esconder');
			$('#formNome').addClass('esconder');
		}else{
			$('#formCpf').addClass('esconder');
			$('#formNome').removeClass('esconder');
		}

	}

	 function consultarCidades(estado){
	 	console.log("pesquisando estado: " + estado);
				show_loading();
				var combo = $('#cidade');
				
				$('option',combo).remove();
				combo.append('<option value="">Selecione a cidade</option>');
				
				$.ajax({
					url: '<?php echo URL; ?>/page/pesquisa_b.php?getCidadesDoEstado='+estado,
					cache:false,
					success: function(data) {
						var cidades = JSON.parse(data);
						for (var i = 0; i < cidades.length; i++){
							combo.append('<option value="'+cidades[i].nome+'">'+cidades[i].nome+'</option>')
						}
						end_loading();
						//$('#loadingUf').hide();
					}
				});
			}

</script>

<style>
	#tabela tr:hover{
		background-color:#eaeaea;
		cursor:pointer;
	}
</style>


