<?php


include('class/RegistrarConsulta.php');
include('class/Conexao.class.php');
include('class/Token.class.php');
include('class/LimitarConsulta.function.php');

$con = new Conexao();
limitarConsulta($con, $_SESSION['usuario'], 'v');


if($_POST['info']){

	$token = new Token();
	$token = $token->get_token();

	?>
	<link rel="stylesheet" type="text/css" href="https://cdn.probusca.com/painel/css/vip.css">
	<script>
		var extrato, pagamento, carta;

		function showContent(tipo){

			var conteudo = '';
			if(tipo == 'extrato')
				conteudo = extrato;
			else if(tipo == 'carta')
				conteudo = carta;
			else
				conteudo = pagamento;

			$('#conteudo').html(conteudo);
			$('#modal').modal('show');
		}

		function do_consulta(first_try = true){

			show_loading();
			$.ajax({
				type:'POST', 
				url:'<?= $_SESSION['endpoint']; ?>/buscaV', 
				data:{token:'<?= $token; ?>', dado:'<?= $_POST['info']; ?>', tipo:'eo'}, 
				dataType:"json"
			}).done(function(r){

				console.log(r);
				end_loading();
				$('#showExtrato').css('display', 'none');

				if(r.erro > 0){
					return alert(r.msg);
				}

				if(r.extrato == "") { 
					alert("Não foi possível recuperar as informações, por favor confira o NB e tente novamente.") 
					window.location.href = "//probusca.com/painel/pesquisa_eo";
				}

				if(r.extrato){
					extrato = r.extrato;
					$('#showExtrato').css('display', 'inline-block');
					$('#showExtrato').attr('onclick', "showContent('extrato');");
					//$('#modal').modal('toggle');
					$('.panel-body').prepend('<h3>Sua pesquisa está pronta!</h3>');
				}

				$('#resultado').html(r.resultado);

			}).fail(function(r){

				console.log(r);
				end_loading();

				if(first_try)
					do_consulta(false);

			});

		}

		window.onload = function(){
			var jsPrint = document.createElement("script");
			jsPrint.type = "text/javascript";
			jsPrint.src = "https://probusca.com/painel/js/printThis.js";
			$("head").append(jsPrint);
			do_consulta();
		};

	</script>
	<?php
}

?>

<style>
	#showExtrato {
		display:none;
	}
	.stat-item-inss {
	    padding-left: 1.25rem;
	    width: 135px;
	    border-left: 1px solid rgba(0, 0, 0, 0.1);
	    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
	    border-right: 1px solid rgba(0, 0, 0, 0.1);
	    float:left;
	}

	.margem {
		width: 50%;
	    margin-top: 10px;
	    border: 1px solid #eaeaea;
	    border-radius: 10px;
	    padding: 0;
	    float: left;
	    margin: 30px 30px 30px 0;
	}

	#margem > p{
		text-align: center;
   	padding: 10px;
    font-size: 18pt;
	}

	#margem > h6 {
		background-color: #388EA8;
	    color: white;
	    padding: 10px;
	    text-align: center;
	    margin: 0;
	    border-radius: 5px;
	}

	.stat-item > p{
		font-weight: bold;
		font-size:1.3em;
		color:black;
		    line-height: 0.7em;
	}

	#informacoes_principais > div > div > div > div:nth-child(3) > div > div:nth-child(1){
		margin-left:0;
		margin-top:10rem;
	}

	h4 {
		color:black;
		font-weight:bold;
	}

	.nav-item {
		color: #C2C2C2;
		font-weight: bold;
	}

	.nav-tabs>li.active>a, .nav-tabs>li.active>a:hover, .nav-tabs>li.active>a:focus {
		color: crimson;
		background-color: #e9ecf2 !important; 
	}

	.panel-default .panel-heading{
		background-color: #e9ecf2 !important; 
	}

	.panel-title {
		color: #388EA8;
		font-weight: bold;
	}

	.table > thead {
		font-weight:bold;
		font-size:16px;
		text-trasnform: uppercase !important;
	}

	.tab-content {
		background: #e9ecf2;
	}

	@media screen {
		#printSection {
				display: none;
		}
	}

	@media print {
		body * {
			visibility:hidden;
		}
		#printSection, #printSection * {
			visibility:visible;
		}
		#printSection {
			position:absolute;
			left:0;
			top:0;
		}
	}
</style>


<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Resultado da Consulta</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Resultado da Consulta</h1>
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
								<button type="button" class="btn btn-md btn-primary" onclick="location.href='<?php echo URL; ?>/pesquisa_eo';">Realizar nova Pesquisa</button>
								<!--<button type="button" id="showExtrato" class="btn btn-md btn-success pull-right">Extrato Online</button> -->
							<?php } ?>

						</div>

					</form>

				</div>

				<div class="panel-body">
					<button type="button" id="showExtrato" class="btn btn-md btn-success pull-left">Extrato Online</button>
					<?php 

					if($_POST['info'])  
						echo '<script>console.log("consulta realizada")</script>';
					else { ?>

						<form action="" method="post">
							<div class="form-group col-lg-4">
					        	<label id="titulo">CPF ou NB</label>
					        	<input type="hidden" name="tipo" value="INSS">
					        	<input type="text" name="info" required id="info" class=" form-control" placeholder="Digite um CPF ou Número do Benefício">
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
				<h4 class="modal-title">Resultado</h4>
				<center><button type="button" class="btn btn-danger" id="btnPrint" data-dismiss="modal" onclick="printModal('.modal-body');"><i class="fa fa-print"></i> Imprimir Extrato</button></center>
			</div>
				<div class="modal-body">
					<div class="form-group" id="conteudo"></div>
				</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>


<script>
//printar o modal
function printModal(elem) {
	console.log(elem);
	$(elem).printThis({
		pageTitle: "Extrato Online- probusca.com",
		header: "<h3>Extrato Online- probusca.com</h3>",
		base:false,
	});
}
</script>