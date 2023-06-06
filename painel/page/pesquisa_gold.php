<?php

include('class/LimitarConsulta.function.php');
if(!$_SESSION) @session_start();
limitarConsulta(null, $_SESSION['usuario'], 'gold');

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Pessoal Gold</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Pessoal Gold</h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
			<p style="margin-left:1%;">A consulta irá demorar alguns segundos, aguarde até que uma nova janela se abra.</p>
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">		
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="home">
							<form id="form_cpf" target="_blank" action="<?php echo URL; ?>/class/PesquisaPessoalGold.class.php" method="post">
								<div class="form-group col-sm-3">
									<label for="">CPF</label>
									<input type="text" id="cpf" name="cpf" class="onlyNumbers form-control" placeholder="Digite o CPF...">
								</div>
								<div class="clearfix"></div>
								<button type="submit" class="display btn btn-primary" id="pesquisaCpf">Pesquisar CPF</button> 
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->
