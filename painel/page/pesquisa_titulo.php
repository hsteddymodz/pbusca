<?php

error_reporting(0);

include("class/Conexao.class.php");
include('class/LimitarConsulta.function.php');
include('class/Token.class.php');
$con = new Conexao();
$token = new Token();
$token = $token->get_token();
limitarConsulta($con, $_SESSION['usuario'], 'tit');

?>
<script>

	function PrintElem(elem)
	{
	    var mywindow = window.open('', 'PRINT', 'height=400,width=600');
	    var html     = document.getElementById(elem).innerHTML;

	    mywindow.document.write('<html><head><title>Imprimir Resultado</title>');
	    mywindow.document.write('</head><body>');
	    mywindow.document.write(html);
	    mywindow.document.write('</body></html>');

	    mywindow.document.close(); // necessary for IE >= 10
	    mywindow.focus(); // necessary for IE >= 10*/

	    mywindow.print();
	    mywindow.close();

	    return true;
	}

	function exibirConsultaLocalVotacao(){
		location.href='/painel/pesquisa_titulo';
	}

	function pesquisar() {
		let cpf = $('#cpf').val();
		let token = $('#token').val();
		if(cpf.length < 11 || !token)
			return alert('CPF Inválido!');
		show_loading();
		$.ajax({
			type:'POST', 
			url:'<?= $_SESSION['endpoint']; ?>/buscaTitulo', 
			data:{
				token: token, 
				cpf:cpf
			}, 
			dataType:"json"
		}).done(function(r){
			console.log(r);
			end_loading();
			if(r.error)
				return alert(r.msg);
			$('#resultado').html(r.msg + '<div class="form-group"><p><button id="imprimir" class="btn btn-primary">Imprimir</button></p></div>');
			$('#imprimir').click(function(){
				PrintElem('resultado');
			});
		}).fail(function(r){
			console.log(r);
			end_loading();
		});
	}
</script>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Título</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Título</h1>
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
				<div class="panel-body table-responsive" id="resultado">
		
					<form id="form"  method="post">
						
						<input type="hidden" name="form" value="true">

						<div class="form-group col-sm-3">
							<label for="">CPF*</label>
							<input type="hidden" id="token" name="token" value="<?= $token; ?>">
							<input type="text" id="cpf" name="cpf" required class="onlyNumbers form-control">
						</div>

						<div class="clearfix"></div>
	
						<div class="form-group col-sm-3">
							<button type="button" name="buscar" onclick="pesquisar();"  value="true"  class="btn btn-primary">Pesquisar</button>
						</div>


					</form>

				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->

