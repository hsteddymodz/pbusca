<?php


if(!$_SESSION) @session_start();

include('class/Conexao.class.php');
$con = new Conexao();
include('class/LimitarConsulta.function.php');
limitarConsulta($con, $_SESSION['usuario'], 'icep');

$nome_arquivo = md5(time());
include('class/Token.class.php');
$token = new Token();
$token = $token->get_token();

?>
<style>
	
	.tbl_results th, td{
		padding-bottom:15px;
	}

	.tbl_results tr:hover{
		background-color: #eaeaea;
		cursor: pointer;
	}
	
	.title{
		font-weight: bold;
	}

	.tbl_results .nome{
		color:blue;
	}

	
	.tbl_results .nome:hover{
		color:black;
	}

	.dados:after {
	    content:"\a";
	    white-space: pre;
	}

	.headertitle{
		margin-top:15px;
		margin-bottom:15px;
		font-size:1.4em;
	}
	span.link{
		content:"\a";
	    white-space: pre;
	    color:blue;
	    cursor:pointer;
	}

	#tabela tr:hover{
		background-color:#eaeaea;
		cursor:pointer;
	}

</style>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Pessoa Física</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Pessoa Física <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
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

					<?php if($_POST['form']){ ?>

					<div class="form-group text-right">
						<button onclick="location.href='<?php echo URL; ?>/pesquisa_s';" class="btn btn-default">Pesquisar Novamente</button>
					</div>

					<table id="tabela" class="table table-bordered table-hover table-stripped ">
						<thead>
							<tr>
								<th>Nome</th>
								<th>Mãe</th>
								<th>Pai</th>
								<th>Sexo</th>
								<th>Nascimento</th>
								<th>País de Nascimento</th>
								<th>Município de Nascimento</th>

							</tr>
						</thead>
						<tbody id="corpo_tabela">

						</tbody>
					</table>

					<?php }else{ ?>
						

                        <form id="form_cep" onsubmit='return verifyform(this);'>
												
                            <div class="form-group col-sm-3">
								<label for="">CEP</label>
								<input type="text" onchange="disable_inputs(this);" name="txt_cep" class=" form-control">
							</div>

							<div class="form-group col-sm-3">
								<label for="">Nome</label>
								<input type="text" onchange="disable_inputs(this);" name="txt_nome" class=" form-control">
							</div>

							<div class="form-group col-sm-3">
								<label for="">Número</label>
								<input type="text" onchange="disable_inputs(this);" name="txt_num" class=" form-control">
							</div>

							<div class="form-group col-sm-3">
								<label for="">Número até</label>
								<input type="text" onchange="disable_inputs(this);" name="txt_num1" class=" form-control">
							</div>
							<div class="form-group text-center">
								<input  class="btn btn-primary" type='button' onclick="do_consulta('form_cep');" value='Executar' id='btn_sub' name='btn_sub'>
							</div>
						</form>

						<div class="clearfix"></div>

                        <div id="results" class="col-md-4"></div>
                        <div style="border-left:2px solid #333;" id="results2" class="col-md-8"></div>

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

<script>

	function PrintElem(elem)
    {
        Popup($(elem).html());
    }

    function Popup(data)
    {
        var mywindow = window.open('', 'new div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Imprimir</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('<style>@media print{.no-print, .no-print *{display: none !important;}}.dados:after,span.link{content:"\a";white-space:pre}#tabela tr:hover,.tbl_results tr:hover{background-color:#eaeaea;cursor:pointer}.tbl_results th,td{padding-bottom:15px}.title{font-weight:700}.headertitle{margin-top:15px;margin-bottom:15px;font-size:1.4em}span.link{color:#00f;cursor:pointer}</style></head><body >');
        mywindow.document.write('<img src="https://probusca.com/img/logo.png" height="150" alt=""><h3>Resultado da Pesquisa</h3>' + data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();

        return true;
    }

	function disable_inputs(element){

		if($(element).val() == '')
			return;

		$.each($('#form_basica input'), function(){

			if($(element).prop('name') != $(this).prop('name'))
				$(this).val("");

		});

	}

	function get_selected_input(){

		var campo = '';
		$.each($('#form_basica input'), function(){
 	
			if($(this).val() != ''){
				campo =  $(this).prop('name');
			}
				

		});

		return campo;

	}

	function selme(){
		return;
	}

	function montar_post(form){

		//action, plataforma, pesquisa, getdados

		var dados = $('#' + form).serialize();

		//console.log(form);

		dados += "&token=<?= $token; ?>&icep=true";

		if(form == 'form_basica'){

			var input = get_selected_input();
			if(input == '')
				return alert("Preencha pelo menos um campo.");

			dados += '&pesquisa=basica&input=' + input + '&q=' + $('input[name="'+input+'"]').val();

		}

		if(form == 'form_cep')dados += "&pesquisa=cep";
		if(form == 'form_nome') dados += "&pesquisa=nome";
		if(form == 'form_ende') dados += "&pesquisa=endereco";

		//console.log(dados);

		return dados;

	}


	function getdadosveic(id){
		getdados(id, 'getdadosveic');
	}

	function getdadosemail(id){
		getdados(id, 'getdadosemail');
	}

	function getdadosende(id){
		getdados(id, 'getdadosende');
	}

	function getdadostel(id){
		getdados(id, 'getdadostel');
	}


	function getdados(id, tipo = 'getdados'){


		// atributos Obrigatorios
		show_loading();
		var dados = "icep=true&token=<?= $token; ?>&pesquisa="+tipo+"&"+tipo+"="+id;

		$.ajax({type:'POST', url:'<?= $_SESSION['endpoint']; ?>/buscaSL', data:dados, dataType:"text"}).done(function(res){

			$("html, body").animate({ scrollTop: 0 }, "slow");
			//console.log(res);
			end_loading();
			var res = JSON.parse(res);
			if(res.erro == 1) alert(res.msg);
			else if(res.resultado) $('#results2').html(res.resultado + '<div class="form-group text-center"><input type="button" class="btn" value="Imprimir" onclick="PrintElem(\'#results2\')" /></div>');

		}).fail(function(res){
			console.log(res);
			end_loading();
		});

	}

	function do_consulta(form, captcha = false){

		show_loading();

		dados = montar_post(form, captcha);
		//console.log(dados);

		$.ajax({type:'POST', url:'<?= $_SESSION['endpoint']; ?>/buscaSL', data:dados, dataType:"json"}).done(function(res){

			//console.log(res);
			var html = res;

			if(res.erro == 1){
				end_loading();
				return alert(res.msg);
			}
			
			$('#results').html('');
			$('#results2').html('');


			if(!res.conteudo){
				$('#results').html("<p><b>CLIQUE</b> em um dos resultados abaixo:</p>" + res.resultado);
				$('#results2').html('');
			}else{
				$('#results').html("<p><b>CLIQUE</b> em um dos resultados abaixo:</p>" + res.resultado);
				if(res.conteudo) $('#results2').html(res.conteudo + '<div class="form-group text-center"><input type="button" class="no-print btn" value="Imprimir" onclick="PrintElem(\'#results2\')" /></div>');
			}
			
			$('#resultado_seekloc').modal('hide');
			end_loading();

		}).fail(function(res){
			console.log(res);
			end_loading();
			alert("Falha ao pesquisar. Tente novamente, por favor!");
		});

	}

</script>