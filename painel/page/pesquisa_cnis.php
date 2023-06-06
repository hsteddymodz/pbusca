<style>
	.mensagem-erro {
		color:red;
		font-weight:bold;
	}
	.titulo {
		color:#5DB7FB;
		font-weight:bold;
	}
	#alertPesquisaAjax {
		margin-top:2%;
	}
	.tableResults{
		padding:10px;
	}
	#btnPrint {
		display:none;
	}
</style>

<style media="print">
 @page {
  	size: auto;
  	margin: 0;
  }
</style>

<?php
if(!$_SESSION) @session_start();


include('class/Token.class.php');

$token = new Token();
$token = $token->get_token();

limitarConsulta(null, $_SESSION['usuario'], 'cnis');
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<script>
$(document).ready(function() {	
	var jsPrint = document.createElement("script");
	jsPrint.type = "text/javascript";
	jsPrint.src = "https://probusca.com/painel/js/printThis.js";
	$("head").append(jsPrint);
	$(".display").click(function() {
		$(this).prop("disabled", true);
		$("#alertPesquisaAjax").show();
		$("#loadingSpinner").removeClass('hidden');
		var formulario = "#form_cpf";
		var cpf = $("#cpf").val();
		var dados = $(formulario).serialize();
			$.ajax({
				type: "POST",
				url: "https://probusca.com/painel/class/PesquisaCNIS.class.php",
				dataType: "json",
				data: dados,
				success: function(data){
					$('.tableResults').html("");
					$("#pesquisaCpf").prop("disabled", false);
					var obj = data;
					if(obj['success'] == 'false') {
						var mensagem_erro = obj['response'];
						// Mensagens de erro pro usuario
						if (obj['response'] == 'CREDITOSINSUF') {
							mensagem_erro = "Seus créditos de hoje se esgotaram.";
						} else if(obj['response'] == 'USERLOGGEDOUT') {
							mensagem_erro = "Você não está logado";
						}	
						$('#alertErroAjax').html(mensagem_erro);
						$('#alertErroAjax').show();
						$("#alertPesquisaAjax").hide();
						$("#loadingSpinner").addClass('hidden');
					} else {
						$("#loadingSpinner").addClass('hidden');					
						$("#alertErroAjax").hide();
						$("#alertPesquisaAjax").hide();
						$("#btnPrint").css("display", "inline-block");
			
						var tabela = $('#tabelaResultado tbody');
						var tabelaBase = $("#tabelaDadosBasicos tbody");
						var counter;
						
						$.each(obj.relacoesPrevidenciarias, function(i, v) {
							counter = i;
							j = i+1;
							tabelaBasico = '<div class="tableResults table-responsive" style="margin-bottom:2%;"> <h2>'+j+') Dados Básicos do Benefício</h2><table class="table table-bordered" id="tabelaDadosBasicos'+i+'"> <thead class="titulo"> <th>Nome do Benefício</th> <th>Número</th> <th>Situação</th> <th>NIT</th> <th>Data de Início</th>	</thead> <tbody></tbody> </table> </div>'
							tabelaResultados = '<div class="tableResults"> <h2>Remunerações do Benefício '+j+'</h2> <table class="table table-bordered" id="tabelaResultado'+i+'"> <thead class="titulo"> <th>Competência</th> <th>Remuneração</th> <th>Mês</th> <th>Ano</th> </thead> <tbody></tbody> </table> </div>'
							tabelaBase += '<tr>'
							tabelaBase +=	'<td>'+ v.nome +'</td>';
							tabelaBase +=	'<td>'+ v.numero +'</td>';
							tabelaBase +=	'<td>'+ v.situacao +'</td>';
							tabelaBase +=	'<td>'+ v.nit +'</td>';
							if (v.dataInicio != 'undefined') {
								tabelaBase +=	'<td>'+ v.dataInicio +'</td>';
							}
							tabelaRemuneracao = '<tr>';
							if (v.remuneracoes == null) {
								alert("Essa pessoa não possui benefícios!");
							}
							for (i = 0; i < (v.remuneracoes).length; ++i) {
								tabelaRemuneracao += '<td>' + v.remuneracoes[i].competencia.competenciaAnoMes + '</td>';
								tabelaRemuneracao += '<td> R$ ' + v.remuneracoes[i].valor + '</td>';
								tabelaRemuneracao += '<td>' + v.remuneracoes[i].competencia.mes + '</td>';
								tabelaRemuneracao += '<td>' + v.remuneracoes[i].competencia.ano + '</td>';
								//tabelaRemuneracao += '<td>' + v.remuneracoes[i].indicadores + '</td>';
								tabelaRemuneracao += '</tr>';
							}
							tabelaBase += '</tr>'	
							$('#agrupaResultados').append(tabelaBasico);
							$('#agrupaResultados').append(tabelaResultados);
							$('#tabelaDadosBasicos'+counter).append(tabelaBase);
							$('#tabelaResultado'+counter).append(tabelaRemuneracao)
							tabelaBase = '';
						});
					}
				},
				error: function (request, status, error) {
					console.log('Erro');
					alert('Não foi possível recuperar nenhum dado');
					$("#alertPesquisaAjax").hide();
					$("#loadingSpinner").addClass('hidden');
					$("#pesquisaCpf").prop("disabled", false);
					$("#btnPrint").css("display", "none");
					var msg = request.responseText;				
				}
			});
		}); 
});
</script>


<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Pro Segurados</li>
		</ol>
	</div><!--/.row -->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Pro Segurados <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
			<!--<p style="margin-bottom:2%"><strong>Aviso:</strong> as consultas poderão demorar até 5 minutos, seja paciente.</p> -->
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
							<button type="button" class="btn btn-danger pull-right" id="btnPrint" data-dismiss="modal" onclick="printTable('#agrupaResultados');"><i class="fa fa-print"></i> Imprimir Extrato</button>
						</div>
					</form>
				</div>
				<div hidden class="alert alert-danger" role="alert" id='alertErroAjax'></div>
				<div class="panel-body">
					<div class="card">		
						<form id="form_cpf" method="post">
							<div class="form-group col-sm-3">
								<label for="">CPF</label>
								<input type="text" id="cpf" name="cpf" class="onlyNumbers form-control" placeholder="Digite um CPF...">
							</div>
							<div class="clearfix"></div>
							<input type="hidden" id="tracker" value="1">						
						</form>
						<input class="display btn btn-primary" type="button" id="pesquisaCpf" value="Pesquisar" />
						<div hidden class="alert alert-warning" role="alert" id='alertPesquisaAjax'><i class="fas fa-spinner spin-the-spinner hidden" id="loadingSpinner"></i>	   Pesquisando... Por favor, aguarde</div> 
					</div>
				</div>	
				<div class="clearfix"></div>
			</div>
			<div id="agrupaResultados"></div>
		</div>
	</div>
</div>

<script>

function printTable(elem) {
	console.log(elem);
	$(elem).printThis({
		pageTitle: "Extrato INSS - probusca.com",
		header: "<h3>Extrato INSS - probusca.com</h3>",
		base:false,
	});
}
</script>