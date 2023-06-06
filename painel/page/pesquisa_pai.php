<?php

include("class/Conexao.class.php");
include('class/LimitarConsulta.function.php');
include('class/Token.class.php');
include('class/RegistrarConsulta.php');

$con = new Conexao();

limitarConsulta($con, $_SESSION['usuario'], 'pai');

$token = new Token();
$token = $token->get_token();

$busca = false;
if($_POST['cpf']){
	$ativar_javascript = true;
}

if($ativar_javascript){ ?>
<script>
function get_dados(){
	
	show_loading();
	var dados = "&token=<?= $token; ?>&cpf=<?= $_POST['cpf']; ?>";

	$.ajax({
		type: 'POST', 
		url: '<?= $_SESSION['endpoint']; if($_POST['bp'] == 2) echo '/buscaPai'; else echo '/buscaPaiT'; ?>',
		data: dados, 
		dataType: "text"
	}).done(function(r){

		console.log(r);
		r = JSON.parse(r);
		end_loading();

		if(r.erro > 0)
			return alert(r.msg);

		$('#resultado').html("<div id=\"predata\">\
        	<p>CPF: "+r.dados.cpf+"</p>\
        	<p>NOME: "+r.dados.nome+"</p>\
        	<p>SEXO: "+r.dados.sexo+"</p>\
        	<p>NASCIMENTO: "+r.dados.nascimento+" | Idade: "+r.dados.idade+" | Signo: "+r.dados.signo+"</p>\
        	<p>MÃE: "+r.dados.mae+"</p>\
        	</div><p><b>Nome do Pai: </b> "+ r.dados.pai +"</p>");


	})
	.fail(function(r){
		console.log(r);
		end_loading();
	});
}

window.onload = function(){
	get_dados();
};
</script>
<?php } ?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Pai</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Pai</h1>
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
		
					<?php if($pai){
						echo "<p><b>Nome do Pai: </b> $pai</p>";
						echo "<div class=\"form-group text-center\"><button type=\"button\" onclick=\"location.href='".URL."/pesquisa_pai';\" class=\"btn btn-primary btn-xs\">Pesquisar Novamente</button></div>";
			        }elseif($ativar_javascript){
			        	echo "<div id=\"resultado\"></div>";
			        	echo "<div class=\"form-group text-center\"><button type=\"button\" onclick=\"location.href='".URL."/pesquisa_pai';\" class=\"btn btn-primary btn-xs\">Pesquisar Novamente</button></div>";
			        }else{ ?>
					<form  method="post">
						
						<input type="hidden" name="form" value="true">

						<div class="form-group col-sm-12">
							<p>Tipo de Pesquisa:</p>
							<label for="bp1">
								<input id="bp1" name="bp" required value="1" type="radio"> Busca Pai 1.0
							</label>
							<label for="bp2">
								<input id="bp2" name="bp" required value="2" type="radio"> Busca Pai 2.0
							</label>
						</div>


						<div class="form-group col-sm-3">
							<label for="">Digite o CPF*</label>
							<input type="text" required name="cpf" class="onlyNumbers form-control" placeholder="Digite um CPF">
						</div>

						<div class="clearfix"></div>
	
						<div class="form-group col-sm-3">
							<button type="submit" name="buscar"  value="true"  class="btn btn-primary">Pesquisar</button>
						</div>


					</form>
			        <?php } ?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->
