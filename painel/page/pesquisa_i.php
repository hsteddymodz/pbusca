<?php

$busca = false;
if($_POST['cpf']){

	include('class/Intouch.class.php');

	include('class/Formatar.class.php');

	$f = new Formatar($_POST['cpf']);
	$dado = $f->getNumbers();

	$user = 'COBSERV';		
	$pass = 'yQ$46l7i';			
	$cliente = 'cobserv'; 
	
	$result = searchInfo($user, $pass, $cliente, $dado);

	if($result){

		// registra a consulta
		include('class/Conexao.class.php');
		$con = new Conexao();
		include('class/RegistrarConsulta.php');
							$codigo_consulta = registrarConsulta($con, $_SESSION['usuario'], 'i');

		$codigo_consulta = $con->getCodigo();

	}

	$busca = true;

}

function show_info($titulo, $info){

    if($info != 'SEM INFORMAÇÃO' && $info != '' && $info != ' |  |  | ' && $info != 'INVALIDO' && strlen(trim($info)) > 0)
        return "➜ <b> $titulo</b> $info<br>";

}


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
			<h1 class="page-header">Pesquisa Pessoa Física</h1>
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
		
					<?php if($busca){ ?>

						<div class="form-group text-center">
				        	<button onclick="location.href='<?php echo URL; ?>/pesquisa_i';" class="btn btn-default">Pesquisar Outra Pessoa</button>
				        </div>

						<?php
						if($result['CPF']){ 

							    echo show_info("Nome:", $result['nome']);
							    echo show_info("CPF:", $result['CPF']);
							    echo show_info("Sexo:", $result['sexo']);
							    echo show_info("Data de Nascimento:", $result['dataNasc']);
							    echo show_info("Idade:", $result['idade']);
							    echo show_info("Signo:", $result['signo']);
							    echo show_info("Nome da Mãe:", $result['nomeMae']);


							    if(is_array($result['telefones'])){

							        foreach($result['telefones'] as $tel)
							            echo show_info("Telefone:", $tel);

							    }

							    if(is_array($result['enderecos'])){

							        foreach($result['enderecos'] as $tel)
							            echo show_info("Endereço:", $tel);

							    }

							    if(is_array($result['email'])){

							        foreach($result['email'] as $tel)
							            echo show_info("E-mail:", $tel);

							    }


							}

							if($result['CNPJ']){

							    echo show_info("CNPJ:", $result['CNPJ']);
							    echo show_info("Razão Social:", $result['razaoSocial']);
							    echo show_info("Nome Fantasia:", $result['nomeFantasia']);
							    echo show_info("Data de Abertura:", $result['dataAbertura']);
							    echo show_info("CNAE:", $result['CNAE']);
							    echo show_info("Natureza Jurídica:", $result['naturezaJuridica']);

							    if(is_array($result['telefones'])){

							        foreach($result['telefones'] as $tel)
							            echo show_info("Telefone:", $tel);

							    }

							    if(is_array($result['enderecos'])){

							        foreach($result['enderecos'] as $tel)
							            echo show_info("Endereço:", $tel);

							    }



							}

							?>
						<?php if($result){ ?>
						<div class="col-lg-12 text-center">
							<form action="<?php echo URL; ?>/page/imprimir_i.php" target="_blank" method="post">		
								<?php foreach($result as $k=>$v){

									if(!is_array($result[$k])){

								?>
								<input type="hidden" name="<?php echo $k; ?>" value="<?php echo $v; ?>">
								<?php
									}else{
										foreach($result[$k] as $ch=>$val){
											echo '<input type="hidden" name="'.$k.'[]" value="'.$val.'">';
										}
									} 
								} ?>
								<button type="submit" class="btn btn-default">Imprimir</button>
							</form>
						</div>
						<?php } ?>
				        
				        
			        <?php }else{ ?>
					<form  method="post">
						
						<input type="hidden" name="form" value="true">


						<div class="form-group col-sm-3">
							<label for="">CPF ou CNPJ</label>
							<input type="text" name="cpf" class="onlyNumbers form-control">
						</div>

						<div class="clearfix"></div>
	
						<div class="form-group col-sm-3">
							<button type="submit" name="buscar"  value="true"  class="btn btn-primary">Pesquisar</button>
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

<style>
	#tabela tr:hover{
		background-color:#eaeaea;
		cursor:pointer;
	}
</style>


