<?php

include("class/Conexao.class.php");
include('class/LimitarConsulta.function.php');
include('class/Token.class.php');
include('class/RegistrarConsulta.php');
include('class/limparNumero.function.php');

$con = new Conexao();

limitarConsulta(null, $_SESSION['usuario'], 'titulo2');

$token = new Token();
$token = $token->get_token();

$busca = false;
if($_POST['cpf']){ 

	$busca = limparNumero($_POST['cpf']);

	if(strlen($busca) != 11) { ?>
	<script>
		window.onload = function(){
			alert('CPF Inválido!');
		};
	</script>
	<?php }else{

		$resultado = ""; // verifica se ja existe esse resultado no banco de dados
		
		$res = $con
			->select('*')
			->from('consulta_titulo')
			->where("cpf = '$busca'")
			->limit(1)
			->orderby("data DESC")
			->executeNGet();

		if($res) $resultado = $res['resultado'];

		if($resultado == ""){
		?>
	<script>
	function get_dados(){
		
		show_loading();
		var dados = "token=<?= $token; ?>&cpf=<?= $_POST['cpf']; ?>";
		$.ajax({
			type:'POST', 
			url:'<?= $_SESSION['endpoint']; ?>/buscaTitulo', 
			timeout:0, 
			data:dados, 
			dataType:"json"
		}).done(function(r){

			console.log(r);
			end_loading();

			console.log(r.error);
			if(r.error)
				return alert(r.msg);

			if(!r.msg)
				return alert("Resposta inválida! Tente novamente");

			$('#resultado').html(r.msg);


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
	<?php } } ?>
<?php } ?>
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
					<p><strong>Aviso:</strong> Esta pesquisa pode demorar até 3 minutos para retornar o resultado.</p>	
				<div class="clearfix"></div>
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">
		
					<?php if($busca){
			        	echo "<div id=\"resultado\">$resultado</div>";
			        	echo "<div class=\"form-group text-center\"><button type=\"button\" onclick=\"location.href='".URL."/pesquisa_titulo2';\" class=\"btn btn-primary btn-xs\">Pesquisar Novamente</button></div>";
			        }else{ ?>
					<form  method="post">
						
						<div class="form-group col-sm-3">
							<label for=""><small style="color:red;">*</small>CPF</label>
							<input type="text" required name="cpf" class="onlyNumbers form-control" placeholder="Digite um CPF">
						</div>

						<div class="clearfix"></div>
						


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