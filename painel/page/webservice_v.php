<?php


include('class/RegistrarConsulta.php');
include('class/Token.class.php');
include('class/LimitarConsulta.function.php');

limitarConsulta(null, $_SESSION['usuario'], 'v');


if($_POST['info']){

	$token = new Token();
	$token = $token->get_token();

	?>
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

			console.log("Showing content...");

			$('#conteudo').html(conteudo);
			$('#modal').modal('show');
		}

		function do_consulta(first_try = true){

			show_loading();
			$.ajax({
				type:'POST', 
				url:'<?= $_SESSION['endpoint']; ?>/buscaV', 
				data:{token:'<?= $token; ?>', dado:'<?= $_POST['info']; ?>', tipo:'v'}, 
				dataType:"json"
			}).done(function(r){

				console.log(r);
				end_loading();

				$('#showExtrato').css('display', 'none');
				$('#showPagamento').css('display', 'none');
				$('#showCarta').css('display', 'none');

				if(r.erro > 0){
					return alert(r.msg);
				}

				//if (r.extrato == '') { alert("Não foi possível recuperar as informações. Tente novamente.")  }

				if(r.extrato){
					extrato = r.extrato;
					$('#showExtrato').css('display', '');
					$('#showExtrato').attr('onclick', "showContent('extrato');");
				}

				if(r.pagamento){
					pagamento = r.pagamento;
					$('#showPagamento').css('display', '');
					$('#showPagamento').attr('onclick', "showContent('pagamento');");
				}

				if(r.carta){
					carta = r.carta;
					$('#showCarta').css('display', '');
					$('#showCarta').attr('onclick', "showContent('carta');");
				}

				$('#resultado').html(r.resultado);

			}).fail(function(r){

				console.log('ERROR', r);
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
	.stat-item {
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

	@media print {
   		#resultPage {
			 font-size:5px;
			width: 100%;
			height: auto;
			overflow: visible;
	   }
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
			<input id="numero_beneficio" hidden value="<?php echo $_POST['info']?>">
			<?php echo '<h3><strong>Benefício:</strong>'.$_POST['info'].' </h3>' ?>
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
								<button type="button" class="btn btn-md btn-primary" onclick="location.href='<?php echo URL; ?>/webservice_v';">Realizar nova Pesquisa</button>

								<button type="button" id="showPagamento" style="margin-left:15px;" class="btn btn-md btn-success pull-right">Extrato de Pagamento</button>
								<button type="button" id="showCarta" style="margin-left:15px;" class="btn btn-md btn-success pull-right">Carta de Concessão</button>
								<button type="button" id="showExtrato" class="btn btn-md btn-success pull-right">Extrato Online</button>
								<button type="button" class="btn btn-md btn-danger" id="btnPrintPage" onclick="printPage('.panel-body');"><i class="fa fa-print"></i> Imprimir</button>
								<button id="btnPdfPage" onclick="savePdfPage()" class="btn btn-md btn-warning"><i class="fa fa-download"></i> PDF</button>

							<?php } ?>

						</div>

					</form>

				</div>

				<div class="panel-body" id="resultPage">
					<?php 

					if($_POST['info'])
						echo '<div id="resultado"></div>';
					else{ ?>

						<form action="" method="post">
							<div class="form-group col-lg-4">
					        	<label id="titulo">CPF ou NB</label>
					        	<input type="hidden" name="tipo" value="INSS">
					        	<input type="text" name="info" required id="info" class=" form-control">
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
				<center>
					<button type="button" class="btn btn-warning" id="btnPdfExtrato" data-dismiss="modal"><i class="fa fa-download"></i> PDF</button>
					<button type="button" class="btn btn-danger" id="btnPrint" data-dismiss="modal" onclick="printModal('.modal-body');"><i class="fa fa-print"></i> Imprimir Extrato</button>
				</center>
			</div>
				<div class="modal-body" id="conteudo_modal">
					<div class="form-group" id="conteudo"></div>
				</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>


<script>
	function changeTab(el){

		var id = el.id;

		id = id.substr(0, id.indexOf('-'));

		console.log("ID do Conteúdo: " + id);

		$('.nav-item.active').removeClass('active');
		$('.tab-pane.active').removeClass('active');

		$('#' + id).addClass('active');

		$(el).parent().addClass('active');

	}
</script>

<script>

function printPage(elem) {
	console.log(elem);
	$(elem).printThis({
		pageTitle: "ProConsig - probusca.com",
		header: "<h3>ProConsig - probusca.com</h3> <br> <b><p>Utilize modo paisagem para melhor visualização</p></b>",
		base:false,       
	});
}

function printModal(elem) {
	console.log(elem);
	$(elem).printThis({
		pageTitle: "Extrato ProConsig - probusca.com",
		header: "<h3>Extrato ProConsig - probusca.com</h3>",
		base:false,
	});
}

function savePdfExtrato() {
	var num_beneficio = document.getElementById("numero_beneficio").value;
	var opt = {
		filename: 'Extrato'+num_beneficio+'.pdf',
		jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
	};
	var source = document.getElementsByClassName("texto-centro")[0];
	html2pdf().set(opt).from(source).save();
};


function savePdfPage() {
	alert("Aguarde enquanto seu PDF é gerado!");
	var num_beneficio = document.getElementById("numero_beneficio").value;
	var opt = {
		filename: 'Proconsig'+num_beneficio+'.pdf',
		html2canvas:  { dpi: 192 },
		image:	{ type: 'jpeg', quality: 1 },
		enableLinks: false,
		jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' }
	};
	var souce_informacoes_principais = document.getElementById("informacoes_principais");
	var source_dados_pessoais = document.getElementById("dados_pessoais");
	var source_dados_bancarios = document.getElementById("dados_bancarios");
	var source_historico = document.getElementsByClassName("historico_emprestimos_inss")[0];
	var source_contatos = document.getElementById("contatos");

	let pages = Array();
	pages.push(souce_informacoes_principais);
	pages.push(source_dados_pessoais);
	pages.push(source_dados_bancarios);
	pages.push(source_historico);
	pages.push(source_contatos);

	let doc = html2pdf().set(opt).from(pages[0]).toPdf()
      for (let j = 1; j < pages.length; j++) {
        doc = doc.get('pdf').then(
          pdf => { pdf.addPage() }
        ).from(pages[j]).toContainer().toCanvas().toPdf()
      }
  doc.save()  
	//html2pdf().set(opt).from(source).save();
};

var element = document.getElementById("btnPdfExtrato");
element.addEventListener("click", savePdfExtrato);

</script>