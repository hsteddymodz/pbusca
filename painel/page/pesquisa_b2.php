<?php

include('class/Conexao.class.php');
include('class/LimitarConsulta.function.php');
include('class/onlyNumbers.function.php');
include('class/Token.class.php');

$con = new Conexao();
if(!$_SESSION) @session_start();

$token = new Token();
$token = $token->get_token();

limitarConsulta($con, $_SESSION['usuario'], 'b');

?>
<link href="//probusca.com/assets/css/boavista.css" rel="stylesheet">
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Score</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Score</h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">
				
					<div id="remover" class="col-md-3">
						<div class="form-group">
							<label for="">CPF ou CNPJ</label>
							<input type="text" id="doc" class="form-control" placeholder="Digite um CPF ou CNPJ">
						</div>

						<div class="form-group">
							<button onclick="doPesquisa();" class="btn btn-primary">Pesquisar</button>
						</div>
					</div>
					<div class="col-md-12" id="resultado"></div>

				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->
<script>
	function doPesquisa(final = false){

		var doc = $('#doc').val().replace(/\D/g,'');
		console.log("DOC: " + doc);;

		if(doc.length != 11 && doc.length != 14)
			return alert('CPF ou CNPJ inválido!');

		show_loading();
		$.post('<?= $_SESSION['endpoint']; ?>/buscaBV', {doc:doc, token:'<?= $token; ?>'})
		.done(function(r){

			console.log(r);
			end_loading();

			if(r.erro > 0)
				return alert(r.msg);
			else{
				var newWin =  window.open();
				if(!newWin || newWin.closed || typeof newWin.closed=='undefined') 
				{
					$('#remover').remove();
				    $('#resultado').html(r.resultado);
				    $('.panel-body').prepend('<div class="col-lg-12"><div class="form-group"><button onclick="location.href=\'https://probusca.com/painel/pesquisa_b2\';" class="btn btn-xs btn-primary">Pesquisar Novamente</button></div></div>');
				}else{
					newWin.document.write('<link href="//probusca.com/assets/css/boavista.css" rel="stylesheet">' + r.resultado);
				}

			}

		})
		.fail(function(r){
			console.log(r);
			end_loading();
			if(final == false){
				return doPesquisa(true);
			}else
				return alert('Falha ao consultar!');
		});

	}
</script>