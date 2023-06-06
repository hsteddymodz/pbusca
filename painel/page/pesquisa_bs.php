<?php

if(!$_SESSION) @session_start();

include('class/Conexao.class.php');
include('class/LimitarConsulta.function.php');
include('class/Bankscore.class.php');
include('class/RegistrarConsulta.php');

$con = new Conexao();
limitarConsulta($con, $_SESSION['usuario'], 'bs');

if($_POST['documento']){

	$documento = preg_replace("/[^0-9]/", "", $_POST['documento']); 
	$erro = false;

	if(strlen($documento) != 11 && strlen($documento) != 14){
		$erro = true;
		$msg = "CPF precisa ter 11 números enquanto o CNPJ precisa ter 14";
	}else{

		$bs = new Bankscore();
		if(strlen($documento) == 11)
			$bs->pesquisa_cpf($documento);
		else
			$bs->pesquisa_cnpj($documento);


	}

}

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa BS</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa BS</h1>
		</div>
	</div><!--/.row-->

	


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">

					<?php if($_POST['documento']){ ?>

						<div class="form-group text-right">
				        	<button onclick="location.href='<?php echo URL; ?>/pesquisa_bs';" class="btn btn-default">Pesquisar Novamente</button>
				        	<button type="button" class="btn-primary btn" onclick="PrintElem('print');">Imprimir</button>
				        </div>
						<?php if($erro || $bs->houve_erro()){ ?>
				        <div class="col-lg-12">
				        	<p><?php if($erro) echo $msg; else echo $bs->get_erro(); ?></p>
				        </div>
				        <?php }else{ 
				        	$resultado = $bs->get_retorno(); 

				        	if(strlen($documento) == 11){
								registrarConsulta($con, $_SESSION['usuario'], 'bs');
				        		// pesquisa CPF
					        	?>
					 
								<div id="print" class="table-responsive">
									<table class="table table-bordered">
										<?php if(count($resultado['QUEMCONSULTOU']) > 0 && verificar_resultado($resultado['QUEMCONSULTOU'], 'EMPRESA')){ ?>
											<tr class="cinza">
												<td colspan="14">Quem Consultou</td>
											</tr>
											<?php foreach($resultado['QUEMCONSULTOU'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['QTDE'] ?> consulta feita por <b><?= $qc['EMPRESA'] ?></b> <small>em <?= $qc['ULTIMA_CONSULTA'] ?></small></td>
												</tr>
											<?php } // fim foreach ?>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>

										<?php } // fim if ?>
										<?php if(count($resultado['TELEFONESRELACIONADOS']) > 0 && verificar_resultado($resultado['TELEFONESRELACIONADOS'], 'RELACAO')){ ?>
											<tr class="cinza">
												<td colspan="14">Telefones Relacionados</td>
											</tr>
											<?php foreach($resultado['TELEFONESRELACIONADOS'] as $qc){ ?>
												<tr>
													<td colspan="2"><b>Relação</b></td>
													<td colspan="2"><?= $qc['RELACAO']; ?></td>

													<td ><b>Idade</b></td>
													<td><?= $qc['IDADE']; ?></td>

													<td colspan="2"><b>Nome</b></td>
													<td colspan="2"><?= $qc['NOME']; ?></td>

													<td colspan="2"><b>Telefone</b></td>
													<td colspan="2"><?= $qc['TELEFONE']; ?> <small><?= $qc['OPERADORA']; ?></small></td>

												</tr>
											<?php } // fim foreach ?>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>

										<?php } // fim if ?>

										<?php if(count($resultado['ENDERECOS']) > 0  && verificar_resultado($resultado['ENDERECOS'], 'LOGRADOURO')){ ?>
											<tr class="cinza">
												<td colspan="14">Endereços</td>
											</tr>
											<?php foreach($resultado['ENDERECOS'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['LOGRADOURO']; ?></td>
												</tr>
											<?php } // fim foreach ?>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>

										<?php } // fim if ?>

										<?php if(count($resultado['EMAILS']) > 0){ ?>
											<tr class="cinza">
												<td colspan="14">E-mails</td>
											</tr>
											<?php foreach($resultado['EMAILS'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['EMAIL']; ?></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(count($resultado['FULLTELEFONES']) > 0  && verificar_resultado($resultado['FULLTELEFONES'], 'TELEFONE')){ ?>
											<tr class="cinza">
												<td colspan="14">Telefones</td>
											</tr>
											<?php foreach($resultado['FULLTELEFONES'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['DDD']; ?> <?= $qc['TELEFONE']; ?> <small>(<?= $qc['OPERADORA']; ?>)</small></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										
										<?php if(count($resultado['PARTICIPACAOEMPRESARIAL']) > 0 && verificar_resultado($resultado['PARTICIPACAOEMPRESARIAL'], 'EMPRESA')){ ?>
											<tr class="cinza">
												<td colspan="14">Participação Empresarial</td>
											</tr>
											<?php foreach($resultado['PARTICIPACAOEMPRESARIAL'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['EMPRESA']; ?> <small>(<?= $qc['CNPJ']; ?>)</small></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(count($resultado['PERFIL_CONSUMO']) > 0){ ?>
											<tr class="cinza">
												<td colspan="14">Perfil de Consumo</td>
											</tr>
											<?php foreach($resultado['PERFIL_CONSUMO'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['AFINIDADE']; ?></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(count($resultado['ESCOLARIDADE']) > 0 && verificar_resultado($resultado['ESCOLARIDADE'], 'DESCRICAO')){ ?>
											<tr class="cinza">
												<td colspan="14">Escolaridade</td>
											</tr>
											<?php foreach($resultado['ESCOLARIDADE'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['DESCRICAO']; ?></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(count($resultado['RENDAS']) > 0 && verificar_resultado($resultado['RENDAS'], 'RENDA')){ ?>
											<tr class="cinza">
												<td colspan="14">Rendas</td>
											</tr>
											<?php foreach($resultado['RENDAS'] as $qc){ ?>
												<tr>
													<td colspan="14">R$ <?= $qc['RENDA']; ?></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(count($resultado['OCUPACAO']) > 0 && verificar_resultado($resultado['OCUPACAO'], 'OCUPACAO')){ ?>
											<tr class="cinza">
												<td colspan="14">Ocupação</td>
											</tr>
											<?php foreach($resultado['OCUPACAO'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['OCUPACAO']; ?></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(($resultado['CLASSESOCIAL'] && $resultado['CLASSESOCIAL']['RENDA'])){ ?>
											<tr class="cinza">
												<td colspan="14">Classe Social</td>
											</tr>

											<tr>
												<td colspan="14"><?= $resultado['CLASSESOCIAL']['RENDA']; ?></td>
											</tr>
							
										<?php } // fim if ?>

										<?php if(count($resultado['FLAGOBITO']) > 0 && verificar_resultado($resultado['FLAGOBITO'], 'FLAG_OBITO')){ ?>
											<tr class="cinza">
												<td colspan="14">Óbito</td>
											</tr>
											<?php foreach($resultado['FLAGOBITO'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['FLAG_OBITO']; ?></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(count($resultado['SITUACAORECEITA']) > 0 && verificar_resultado($resultado['SITUACAORECEITA'], 'SITUACAO_RECEITA')){ ?>
											<tr class="cinza">
												<td colspan="14">Situação Receita</td>
											</tr>
											<?php foreach($resultado['SITUACAORECEITA'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['SITUACAO_RECEITA']; ?></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if(($resultado['DADOS_CADASTRAIS'])){ ?>
											<tr class="cinza">
												<td colspan="14">Dados Cadastrais</td>
											</tr>

											<tr>
												<td colspan="2"><b>CPF</b></td>
												<td colspan="2"><?= $resultado['DADOS_CADASTRAIS']['CPF']; ?></td>

												<td colspan="2"><b>NOME</b></td>
												<td colspan="3"><?= $resultado['DADOS_CADASTRAIS']['NOME']; ?></td>

												<td colspan="3"><b>ÚLTIMO NOME</b></td>
												<td colspan="2"><?= $resultado['DADOS_CADASTRAIS']['NOME_ULTIMO']; ?></td>
											</tr>
											<tr>

												<td colspan="1"><b>SEXO</b></td>
												<td colspan="1"><?= $resultado['DADOS_CADASTRAIS']['SEXO']; ?></td>

												<td colspan="2"><b>DATA DE NASCIMENTO</b></td>
												<td colspan="3"><?= $resultado['DADOS_CADASTRAIS']['DATANASC']; ?> <small><?= $resultado['DADOS_CADASTRAIS']['IDADE']; ?> anos</small></td>
												
												<td colspan="2"><b>MÃE</b></td>
												<td colspan="3"><?= $resultado['DADOS_CADASTRAIS']['NOME_MAE']; ?></td>

												<td colspan="1"><b>SIGNO</b></td>
												<td colspan="1"><?= $resultado['DADOS_CADASTRAIS']['SIGNO']; ?></td>
											</tr>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
							
										<?php } // fim if ?>

									</table>
								</div>
								<?php }else{

								registrarConsulta($con, $_SESSION['usuario'], 'bs');
								?>
								<div id="print" class="table-responsive">
									<table class="table table-bordered">
										<?php if(count($resultado['QSA']) > 0 && verificar_resultado($resultado['QSA'], 'NOME')){ ?>
											<tr class="cinza">
												<td colspan="14">Quadro Societário</td>
											</tr>
											<?php foreach($resultado['QSA'] as $qc){ ?>
												<tr>
													<td colspan="2"><b>NOME</b></td>
													<td colspan="3"><?= $qc['NOME'] ?></td>

													<td colspan="2"><b>DOCUMENTO</b></td>
													<td colspan="3"><?= $qc['DOCUMENTO'] ?></td>

													<td colspan="2"><b>CARGO</b></td>
													<td colspan="2"><?= $qc['CARGO_SOCIO'] ?></td>

												</tr>
												<tr>
													<td colspan="2" class="bold">DATA DE ENTRADA</td>
													<td colspan="2"><?= $qc['DATA_ENTRADA'] ?></td>
													<td colspan="2" class="bold">PARTICIPAÇÃO</td>
													<td colspan="3"><?= $qc['PCT_PARTICIPACAO'] ?> %</td>
													<td colspan="5"></td>
												</tr>
											<?php } // fim foreach ?>
											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>
										

										<?php if($resultado['CAPFAT'] && $resultado['CAPFAT']['FAT_PRESUMIDO'] && $resultado['CAPFAT']['VALOR_CAPITAL_SOCIAL']){ ?>
											<tr class="cinza">
												<td colspan="14">Capital/Faturamento</td>
											</tr>

											<tr>
												<td colspan="3" class="bold"><b>FATURAMENTO PRESUMIDO</b></td>
												<td colspan="4">R$ <?= $resultado['CAPFAT']['FAT_PRESUMIDO']; ?></td>

												<td colspan="3" class="bold"><b>CAPITAL SOCIAL</b></td>
												<td colspan="4">R$ <?= $resultado['CAPFAT']['VALOR_CAPITAL_SOCIAL']; ?></td>
											</tr>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
							
										<?php } // fim if ?>

										<?php if(count($resultado['ENDERECOS']) > 0  && verificar_resultado($resultado['ENDERECOS'], 'LOGRADOURO')){ ?>
											<tr class="cinza">
												<td colspan="14">Endereços</td>
											</tr>
											<?php foreach($resultado['ENDERECOS'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['LOGRADOURO']; ?></td>
												</tr>
											<?php } // fim foreach ?>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
										<?php } // fim if ?>

										<?php if($resultado['CNAENATSIT'] && $resultado['CNAENATSIT']['CNAE']){ ?>
											<tr class="cinza">
												<td colspan="14">Classificação Nacional de Atividades Econômicas (CNAE)</td>
											</tr>

											<tr>
												<td colspa="2" class="bold">PORTE</td>
												<td colspa="2"><?= $resultado['CNAENATSIT']['PORTE']; ?></td>

												<td colspa="2" class="bold">CNAE</td>
												<td colspa="2"><?= $resultado['CNAENATSIT']['CNAE']; ?></td>

												<td colspa="2" class="bold">Descrição CNAE</td>
												<td colspa="4"><?= $resultado['CNAENATSIT']['DESCRICAO_CNAE']; ?></td>

											</tr>

											<tr>
												<td colspa="2" class="bold">Nat. Jurídica</td>
												<td colspa="5"><?= $resultado['CNAENATSIT']['COD_NAT_JURIDICA']; ?> - <?= $resultado['CNAENATSIT']['DESCRICAO_NATUREZA']; ?></td>

												<td colspa="2" class="bold">DATA DE SITUAÇÂO CADASTRAL</td>
												<td colspa="2"><?= $resultado['CNAENATSIT']['DATA_SITUACAO_CADASTRAL']; ?></td>

												<td colspa="1" class="bold">Situação</td>
												<td colspa="2"><?= $resultado['CNAENATSIT']['DESCRICAO_SITUACAO_RECEITA']; ?></td>

											</tr>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
							
										<?php } // fim if ?>

										<?php if(count($resultado['FULLTELEFONES']) > 0  && verificar_resultado($resultado['FULLTELEFONES'], 'TELEFONE')){ ?>
											<tr class="cinza">
												<td colspan="14">Telefones</td>
											</tr>
											<?php foreach($resultado['FULLTELEFONES'] as $qc){ ?>
												<tr>
													<td colspan="14"><?= $qc['DDD']; ?> <?= $qc['TELEFONE']; ?> <small>(<?= $qc['OPERADORA']; ?>)</small></td>
												</tr>
											<?php } // fim foreach ?>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>

										<?php } // fim if ?>

										<?php if($resultado['DADOS_CADASTRAIS'] && $resultado['DADOS_CADASTRAIS']['CNPJ']){ ?>
											<tr class="cinza">
												<td colspan="14">Dados Cadastrais</td>
											</tr>

											<tr>
												<td colspa="2"  class="bold">CNPJ</td>
												<td colspa="2"><?= $resultado['DADOS_CADASTRAIS']['CNPJ']; ?></td>

												<td colspa="2"  class="bold">QTD. FUNCIONÁRIOS</td>
												<td colspa="2"><?= $resultado['DADOS_CADASTRAIS']['QTDEFUNCIONARIOS']['QTD_FUNCIONARIOS']; ?></td>

												<td colspa="2"  class="bold">ABERTURA</td>
												<td colspa="4"><?= $resultado['DADOS_CADASTRAIS']['DT_ABERTURA']; ?></td>

											</tr>

											<tr>
												<td colspa="3"  class="bold">RAZÃO SOCIAL</td>
												<td colspa="5"><?= $resultado['DADOS_CADASTRAIS']['RAZAO']; ?></td>

												<td colspa="2"  class="bold">NOME FANTASIA</td>
												<td colspa="4"><?= $resultado['DADOS_CADASTRAIS']['RAZAO']; ?></td>

											</tr>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>
							
										<?php } // fim if ?>

										<?php if(count($resultado['TOMADORESDECISAO']) > 0  && verificar_resultado($resultado['TOMADORESDECISAO'], 'NOME')){ ?>
											<tr class="cinza">
												<td colspan="14">Tomadores de Decisão</td>
											</tr>
											<?php foreach($resultado['TOMADORESDECISAO'] as $qc){ ?>
												<tr>
													<td colspan="2" class="bold">CPF</td>
													<td colspan="3"><?= $qc['CPF'] ?></td>

													<td colspan="2" class="bold">NOME</td>
													<td colspan="3"><?= $qc['NOME'] ?></td>

													<td colspan="2" class="bold">CARGO</td>
													<td colspan="3"><?= $qc['CARGO'] ?></td>

												</tr>
												<tr>
													<td colspan="3" class="bold">E-MAIL</td>
													<td colspan="4"><?= $qc['EMAIL'] ?></td>

													<td colspan="3" class="bold">TELEFONES</td>
													<td colspan="4"><?= $qc['TELEFONES'] ?></td>
												</tr>
												<tr class="cinza">
													<td colspan="14"></td>
												</tr>
											<?php } // fim foreach ?>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>

										<?php } // fim if ?>

										<?php if(count($resultado['TELEFONESRELACIONADOS']) > 0 && verificar_resultado($resultado['TELEFONESRELACIONADOS'], 'TELEFONE')){ ?>
											<tr class="cinza">
												<td colspan="14">Telefones Relacionados</td>
											</tr>
											<?php foreach($resultado['TELEFONESRELACIONADOS'] as $qc){ ?>
												<tr>
													<td colspan="2" class="bold"><b>Relação</b></td>
													<td colspan="2"><?= $qc['RELACAO']; ?></td>

													<td colspan="2" class="bold"><b>Nome</b></td>
													<td colspan="4"><?= $qc['NOME']; ?></td>

													<td colspan="2" class="bold"><b>Telefone</b></td>
													<td colspan="2"><?= $qc['TELEFONE']; ?> <small><?= $qc['OPERADORA']; ?></small></td>

												</tr>
												<tr class="cinza">
												<td colspan="14"></td>
											</tr>
											<?php } // fim foreach ?>

											<!-- pular linha --><tr><td colspan="14" class="spacer"></td></tr>

										<?php } // fim if ?>

									</table>
								</div>
								<?php } ?>
				        <?php } ?>

						
				        
			        <?php }else{ ?>
					<form action="" method="post">

						<div class="form-group col-sm-4">
							<label for="">CPF ou CNPJ</label>
							<input type="text" name="documento" class="form-control">
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
	</div><!--/.row-->	


</div><!--/.main-->
<style id="mstyle">
	.cinza{
		background-color:#eaeaea;
		font-weight: bold;
		text-align: center;
	}
	.bold{
		font-weight:bold;
		text-transform: uppercase;
	}
	.spacer{
		
	}
</style>
<script>
	function PrintElem(elem)
{
    var mywindow = window.open('', 'PRINT', 'height=400,width=600');
    var html     = document.getElementById(elem).innerHTML;

    mywindow.document.write('<html><head><title>Imprimir Consulta: <?= $_POST['documento']; ?></title>');
    mywindow.document.write('</head><body>');
    mywindow.document.write('<h1>Imprimir Consulta: <?= $_POST['documento']; ?></h1>');
    mywindow.document.write('<style>' + $('#mstyle').html() + '</style>' + html);
    mywindow.document.write('</body></html>');

    mywindow.document.close(); // necessary for IE >= 10
    mywindow.focus(); // necessary for IE >= 10*/

    mywindow.print();
    mywindow.close();

    return true;
}
</script>