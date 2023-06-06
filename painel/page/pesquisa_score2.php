<?php

if(!$_SESSION) @session_start();

include('class/Conexao.class.php');
include('class/LimitarConsulta.function.php');
include('class/RegistrarConsulta.php');
include('class/PesquisaScore.class.php');

set_time_limit(10);

$con = new Conexao();
limitarConsulta($con, $_SESSION['usuario'], 'score2');
$res = false;
$nada_encontrado = false;

if($_POST['dado']){

	$consulta = new PesquisaScore($_POST['dado']);
	$res      = $consulta->get_resultado();
	if($res === false){
		$nada_encontrado = true;
	}else
		registrarConsulta($con, $_SESSION['usuario'], 'score2');

}

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
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">

					<?php if($_POST['dado']){ ?>

					<?php if($nada_encontrado){ ?>

					<p>Nada Encontrado!</p>

					<div class="form-group text-center">
						<button onclick="location.href='<?php echo URL; ?>/pesquisa_score2';" class="btn btn-default">Pesquisar Novamente</button>
					</div>

					<?php } else { ?>

					<div class="form-group text-center">
						<button onclick="location.href='<?php echo URL; ?>/pesquisa_score2';" class="btn btn-default">Pesquisar Novamente</button>
					</div>

					<?= $res; ?>

					<?php } ?>				

					<?php }else{ ?>
						
				
							<form action="" id="form_basica" method="post">

									<div class="form-group col-sm-4">
										<label for="">Digite o CPF ou CNPJ</label>
										<input type="text" name="dado" required class="form-control">
									</div>
									<div class="clearfix"></div>
	
									<div class="clearfix"></div>

									<div class="form-group text-left">
										<button type="submit" name="btn_sub" value="true" class="btn btn-primary">Pesquisar</button>
									</div>


							</form>
						
					
					<?php } ?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->

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
	</script>