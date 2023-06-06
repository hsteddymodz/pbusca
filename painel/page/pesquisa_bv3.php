<?php
include('class/LimitarConsulta.function.php');
if(!isset($_SESSION)) @session_start();
limitarConsulta(null, $_SESSION['usuario'], 'bv3');
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Score CNPJ para Negócios</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Score CNPJ para Negócios</h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
			<p style="margin-left:1%;">A consulta irá demorar alguns segundos. Aguarde até que uma nova tela se abra.</p>
				<div class="panel-heading">
					<div class="form-group form-inline">
						<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
						Entre com as informações para pesquisar
					</div>
				</div>
				<div class="panel-body table-responsive">		
					<form target="_blank" action="<?php echo URL; ?>/class/Score.class.php" method="post">
						<div class="form-group col-sm-3">
							<label for="">CNPJ</label>
							<input type="text" id="cnpj" name="cnpj" class="onlyNumbers form-control" placeholder="Digite o CNPJ...">
						</div>
						<div class="clearfix"></div>
						<button type="submit" class="display btn btn-primary" id="pesquisaCNPJ">Pesquisar CNPJ</button>
					</form>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->