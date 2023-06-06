<?php

$endpoint_form = '/formCL';
$endpoint_busca = '/buscaCL';
$plataforma_tipo = 'cl';

if(!$_SESSION) @session_start();

include('class/LimitarConsulta.function.php');
include('class/Token.class.php');
include('class/curl_get_contents.function.php');
$token = new Token();
$token = $token->get_token();
limitarConsulta(null, $_SESSION['usuario'], $plataforma_tipo);

$form = json_decode(curl_get_contents($_SESSION['endpoint'] . $endpoint_form), 1);
?>
<style>
	#responseDiv td, #responseDiv th{
		padding: 10px;
	}
	#responseDiv table {
		border-collapse: collapse;
	}
	#responseDiv table, #responseDiv th, #responseDiv td {
		border:1px solid #eaeaea;
	}
	.item {
		padding: 10px;
		border:1px solid black;
		margin-bottom: 20px;
	}
</style>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa CREDMASTER</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa CREDMASTER</h1>
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



					<div class="col-md-12">
						<!-- Nav tabs --><div class="card">
						<ul class="nav nav-tabs" role="tablist">
							<?php 
							$start = 'class="active"';
							foreach($form['tabs'] as $tab) { ?>
								<li role="presentation" <?= $start; ?> >
									<a href="#tab_<?= $tab['name']; ?>" aria-controls="tab_<?= $tab['name']; ?>" role="tab" data-toggle="tab"><?= $tab['label']; ?></a>
								</li>
								<?php 
								$start = '';
							} ?>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">

							<?php 
							$start = 'active';
							foreach($form['tabs'] as $tab) { ?>
								<div role="tabpanel" class="tab-pane <?= $start; ?>" id="tab_<?= $tab['name']; ?>">
									<form action="" method="post">
										<input type="hidden" name="form" value="<?= $tab['name']; ?>">
										<input type="hidden" name="token" value="<?= $token; ?>">
										<?php foreach($tab['inputs'] as $input) { ?>
											<div class="col-lg-4">
												<div class="form-group">
													<?php if($input['type'] == 'select'){ ?>
													<label for=""><?= $input['label']; ?></label>
													<select <?php if($input['*']) echo 'required'; ?> name="inputs_<?= $input['name']; ?>" class="form-control">
														<?php foreach($input['values'] as $val){ ?>
														<option value="<?= $val['value']; ?>"><?= $val['text']; ?></option>
														<?php } ?>
													</select>
													<?php } else if($input['type'] == 'check') { ?>
													<p><label for="<?= $input['name']; ?>"><input id="<?= $input['name']; ?>" name="inputs_<?= $input['name']; ?>" type="checkbox"> <?= $input['label']; ?></label></p>
													<?php } else { ?>
														<label for=""><?= $input['label']; ?></label>
														<input type="text" <?php if($input['*']) echo 'required'; ?> name="inputs_<?= $input['name']; ?>" class="form-control">
													<?php } ?>
												</div>
											</div>
										<?php } ?>
										
										<div class="clearfix"></div>
										<div class="form-group">
											<button onclick="do_consulta(this);" type="button" class="btn btn-success">Pesquisar</button>
										</div>
									</form>
									
								</div>
								<?php 
								$start = '';
							} ?>
						</div>
					</div>
				</div>


				<div id="responseDiv" class="col-md-12 table-responsive content"></div>

			</div>
		</div>
	</div>
</div><!--/.row-->	

</div><!--/.main-->
<script>
document.querySelector('form').addEventListener('submit', function(e){
	e.preventDefault();
}, false);
document.addEventListener('keydown',function(e){if(e.keyIdentifier=='U+000A'||e.keyIdentifier=='Enter'||e.keyCode==13){if(e.target.nodeName=='INPUT'&&e.target.type=='text'){e.preventDefault();return false;}}},true);

function do_consulta(el){

	let form = $(el).parent().parent();
	let nameFormulario = form.find('input[name="form"]').val();
	let inputs = form.serialize();

	show_loading();
	$.post('<?= $_SESSION['endpoint'] . $endpoint_busca; ?>', inputs)
	.done(function(r){
		end_loading();
		if(r.erro)
			return alert(r.resultado);

		$('#responseDiv').html(r.resultado);
		$('#responseDiv img').each(function(){
			let img = $(this);
			if(img.prop('src').indexOf('painel') >= 0)
				img.prop('src', img.prop('src').replace('painel/images', 'painel/img/cl'));
			else
				img.prop('src', img.prop('src').replace('images', 'painel/img/cl'));
			if(img.prop('src').indexOf('whatsapp') >= 0)
				img.css('width', '20px');
		});

		let pesquisa_atual = form.find('input[name="form"]').val();
		console.log(pesquisa_atual);
		if(['cpf_cnpj', 'telefone'].indexOf(pesquisa_atual) >= 0) {

			$('update:first-child').remove();
	        $('update:first-child').remove();
	        $('extension:last-child').remove();
			$('update:last-child').remove();
			$('update:last-child').remove();
			$('update:last-child').remove();
			$('update:last-child').remove();
			$('update:last-child').remove();
			$('.pager').remove();

		} else {

			$('partial-response:first-child changes:first-child update:first-child').remove();
			$('partial-response:first-child changes:first-child update:first-child').remove();
			$('partial-response:first-child changes:first-child update:first-child').remove();

			$('.pager').remove();
			$('extension').remove();
		}
		

	}).fail(function(r){
		end_loading();
		console.log(r);
	});
}

</script>