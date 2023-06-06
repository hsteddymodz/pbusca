<?php

if(!$_SESSION) @session_start();

$router = new Router($_GET['p']);

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa <?php if($router->param(0) == 'buscacar') echo "Busca Car"; else echo "Pro CNH"; ?></li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa <?php if($router->param(0) == 'buscacar') echo "Busca Car"; elseif($router->param(0) == 'procnh') echo "Pro CNH"; else echo "Naturalidade"; ?></h1>
		</div>
	</div><!--/.row-->
	


		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">

						<div class="form-group form-inline">
						<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar

						</div>

					</div>
					<div class="panel-body">
						<div class="col-xs-12">
							<!-- Nav tabs --><div class="card">
							<ul class="nav nav-tabs" role="tablist">
								
								<?php if($router->param(0) == 'buscacar') { ?>
								<li role="presentation" class="active">
									<a href="#placa" aria-controls="placa" role="tab" data-toggle="tab">Placa</a>
								</li>
								<li role="presentation">
									<a href="#renavam" aria-controls="renavam" role="tab" data-toggle="tab">Renavan</a>
								</li>
								<li role="presentation">
									<a href="#chassi" aria-controls="chassi" role="tab" data-toggle="tab">Chassi</a>
								</li>
								<?php } else if($router->param(0) == 'procnh') { ?>
								<li role="presentation" class="active">
									<a href="#cpf" aria-controls="cpf" role="tab" data-toggle="tab">CPF</a>
								</li>
								<li role="presentation">
									<a href="#cnh" aria-controls="cnh" role="tab" data-toggle="tab">CNH</a>
								</li>
								<?php } else if($router->param(0) == 'naturalidade') { ?>
								<li role="presentation" class="active">
									<a href="#naturalidade" aria-controls="naturalidade" role="tab" data-toggle="tab">Naturalidade</a>
								</li>
								<?php } ?>
							
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								
								<?php if($router->param(0) == 'buscacar') { ?>
								<div role="tabpanel" class="tab-pane active" id="placa">
									<form action="" method="post">
										<div class="form-group col-sm-3">
											<label for="">Placa</label>
											<input type="text" required name="placa" class="form-control">
										</div>
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Placa</button>
										</div>
									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="renavam">
									<form action="" method="post">
										<div class="form-group col-sm-3">
											<label for="">Renavam</label>
											<input type="text" required name="renavam" class="form-control">
										</div>
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Renavam</button>
										</div>
									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="chassi">
									<form action="" method="post">
										<div class="form-group col-sm-3">
											<label for="">Chassi</label>
											<input type="text" required name="chassi" class="form-control">
										</div>
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Chassi</button>
										</div>
									</form>
								</div>
								<?php } else if($router->param(0) == 'procnh') { ?>
								<div role="tabpanel" class="tab-pane active" id="cpf">
									<form action="" method="post">
										<div class="form-group col-sm-3">
											<label for="">CPF</label>
											<input type="text" required name="cpf" class="form-control">
										</div>
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar CPF</button>
										</div>
									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="cnh">
									<form action="" method="post">
										<div class="form-group col-sm-3">
											<label for="">CNH</label>
											<input type="text" required name="cnh" class="form-control">
										</div>

										<div class="form-group col-sm-2">
											<label for="">Tipo</label>
											<select name="foto" required class="form-control">
												<option value="">Tipo</option>
												<option value="S">Nova</option>
												<option value="N">Antiga</option>
											</select>
										</div>


										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar CNH</button>
										</div>
									</form>
								</div>
								<?php } else if($router->param(0) == 'naturalidade') { ?>
								<div role="tabpanel" class="tab-pane active" id="naturalidade">
									<form action="" method="post">
										<div class="form-group col-sm-3">
											<label for="">CPF</label>
											<input type="text" required name="naturalidade" class="form-control">
											<small>Só serão mostradas as cidades de nascimentos dos CPFs que possuem uma Habilitação de Motorista (CNH).</small>
										</div>
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Naturalidade</button>
										</div>
									</form>
								</div>
								<?php } ?>
								
							</div>
						</div>
					</div>
					<div class="col-lg-12" id="iframe_div"></div>
				</div>

			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->
<script>

	function printPopup(data) {
        var mywindow = window.open('', 'new div', 'height=400,width=600');
        mywindow.document.write(data);
        mywindow.print();
        mywindow.close();

        return true;
    }
	
	let all_forms = document.querySelectorAll('form');
	for (var i = 0; i < all_forms.length; i++) {
		all_forms[i].addEventListener("submit", function(e) {

			$('#iframe_div').html('');
			show_loading();
			e.preventDefault();
			let dados = $(this).serialize();
			console.log(dados);
			$.post('/painel/class/Detran2.class.php', dados)
			.done(function(r) {
				$('#iframe_div').html('<div class="form-group text-right"><button id="print" class="btn btn-warning">Imprimir</button></div><iframe id="iframe" height="800" src="" width="100%" frameborder="0"></iframe>');
				document.getElementById('iframe').contentWindow.document.write(r);
				$('#print').click(function(){
					printPopup(r);
				});
				setTimeout(end_loading, 1000);
			})
			.fail(function(r) {
				end_loading();
				console.log(r);
			});

			return false;

		});
	}

</script>