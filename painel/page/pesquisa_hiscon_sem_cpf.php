<?php
$con = new Conexao();
if(!$_SESSION) @session_start();

include('class/Conexao.class.php');
include('class/PesquisaHiscon.class.php');
include('class/onlyNumbers.function.php');
include('class/Token.class.php');

$token = new Token();
$token = $token->get_token();

limitarConsulta($con, $_SESSION['usuario'], 'hisconscpf');
?>

<style>
#frame_pdf {
	height:600px;
	width:100%;
}

</style>



<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Hiscon</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Hiscon <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<p style="margin-left:1%;">Ao consultar, aguarde até a pesquisa acabar. Um PDF será disponibilizado nesta página.</p>
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
								<form id="form_nb" method="post">
									<input type="hidden" name="semCpf" value="true">
									<div class="form-group col-sm-3">
										<label for="">Número do Benefício</label>
										<input type="text" id="numero_beneficio" name="numero_beneficio" class="onlyNumbers form-control" placeholder="Digite o NB...">
									</div>
									<div class="clearfix"></div>
								</form>
								<button class="btn btn-primary" id="pesquisaNB">Pesquisar NB</button> 
								<!-- Button trigger modal
								<button hidden type="button" id="botaoResultado" class="btn btn-success btn-md" data-toggle="modal" data-target="#myModal">Exibir Resultado</button> -->
							</div>
					</form>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->

<div class="col-md-12" id="resultado"></div>

<script>
	$(document).ready(function() { 
	    $("#pesquisaNB").click(function() {
			show_loading();
			var numero_beneficio = $("#numero_beneficio").val();
			var formulario = "#form_nb";
			var dados = $(formulario).serialize();
	      
	      	$.ajax({
				type: "POST",
				url: "https://probusca.com/painel/class/PesquisaHiscon.class.php",
				//dataType: "text",
				data: dados,
				success: function(html){


					//console.log(obj);
					/*if(obj.response != 'OK')
						return alert('Nada encontrado!');*/

					//$("#resultado").html('');
	      			/*console.log(obj)
					var caminhoPDF = "https://probusca.com/painel/class/"+obj.path;
					console.log(caminhoPDF);*/
					$("#resultado").html(html);
					end_loading();
	        	},
	        	error: function (request, status, error) {
	          		console.log('Erro')
					console.log(request);
					end_loading();
					if(request.responseText == 'unavailable')
						return alert('Serviço indisponível no horário atual');
					
	        	}
	      	});
	    });	
	});
</script>

