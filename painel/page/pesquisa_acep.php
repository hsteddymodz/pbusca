<?php


if(!$_SESSION) @session_start();

include('class/Conexao.class.php');
$con = new Conexao();
include('class/LimitarConsulta.function.php');
include('class/Token.class.php');
$token = new Token();
$token = $token->get_token();
limitarConsulta($con, $_SESSION['usuario'], 'acep');

$form = json_decode(file_get_contents($_SESSION['endpoint'] . '/buscaA'), 1);

foreach($form['tabs'] as $f){
	if($f['name'] =='formNome'){
		$tab = $f;
		break;
	}
}
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Pessoa Física</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Pessoa Física</h1>
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
							
							?>
							<li role="presentation" class="active" >
								<a href="#tab_<?= $tab['name']; ?>" aria-controls="tab_<?= $tab['name']; ?>" role="tab" data-toggle="tab"><?= $tab['label']; ?></a>
							</li>
						</ul>

						<!-- Tab panes -->
						<div class="tab-content">

							<div role="tabpanel" class="tab-pane active" id="tab_<?= $tab['name']; ?>">
								<form action="">
									<input type="hidden" name="form" value="<?= $tab['name']; ?>">
									<input type="hidden" name="acep" value="true">
									<input type="hidden" name="token" value="<?= $token; ?>">
									
									<?php foreach($tab['inputs'] as $input) { ?>
										<div class="col-lg-4">
											<div class="form-group">
												<?php if($input['type'] == 'select'){ ?>
												<label for=""><?= $input['label']; ?></label>
												<select <?php if($input['*']) echo 'required'; ?> name="<?= $input['name']; ?>" class="form-control">
													<?php foreach($input['values'] as $val){ ?>
													<option value="<?= $val['value']; ?>"><?= $val['text']; ?></option>
													<?php } ?>
												</select>
												<?php } else if($input['type'] == 'check') { ?>
												<p><label for="<?= $input['name']; ?>"><input id="<?= $input['name']; ?>" name="<?= $input['name']; ?>" type="checkbox"> <?= $input['label']; ?></label></p>
												<?php } else { ?>
													<label for=""><?= $input['label']; ?></label>
													<input type="text" <?php if($input['*']) echo 'required'; ?> name="<?= $input['name']; ?>" class="form-control">
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
						</div>
					</div>
				</div>


				<div id="results" class="col-md-12 table-responsive">


				</div>

			</div>
		</div>
	</div>
</div><!--/.row-->	


</div><!--/.main-->

<div style="display:none;">
	<form action="">
		<input type="hidden" name="acep" value="true">
		<input type="hidden" id="form_auxiliar" name="form" value="formCPF">
		<input type="hidden" name="token" value="<?= $token; ?>">
		<input type="text" id="cpf_auxiliar" name="cpf" class="form-control">
		<div class="clearfix"></div>
		<div class="form-group">
			<button id="btn_auxiliar" type="button" class="btn btn-success">Pesquisar</button>
		</div>
	</form>
</div>

<script>

function consulta_aux(cpf){
	if(cpf.length == 11){
		$('#form_auxiliar').val('formCPF');
		$('#cpf_auxiliar').attr('name', 'cpf');
	}
	else{
		$('#form_auxiliar').val('formCNPJ');
		$('#cpf_auxiliar').attr('name', 'cnpj');
	}
	
	$('#cpf_auxiliar').val(cpf);
	do_consulta('#btn_auxiliar');
}

function do_consulta(el){
	let form = $(el).parent().parent();
	let inputs = form.serialize();
	console.log(inputs);
	show_loading();
	$.post('<?= $_SESSION['endpoint']; ?>/buscaA', inputs)
	.done(function(r){
		end_loading();
		console.log(r);
		if(r.erro)
			return alert(r.resultado);

		if(r.list){
			let html = '<table class="table table-bordred">\
			<thead>\
				<tr><td>Documento</td> <td>Tipo</td> <td>Nome</td> <td>Nascimento</td> <td>Cidade</td> <td></td></tr>\
			</thead>';
			let cidade = '';

			for(let k = 0; k < r.list.length; k++){
				cidade = '';
				if(r.list[k].cidade)
					cidade = r.list[k].cidade + ' - ' + r.list[k].uf;
				html += '<tr><td>'+r.list[k].documento+'</td> <td>'+r.list[k].tipo+'</td> <td>'+r.list[k].nome+'</td> <td>'+r.list[k].dataNascimento+'</td> <td>'+cidade + '</td> <td><button onclick="consulta_aux(\''+r.list[k].documento+'\');" class="btn btn-primary"><i class="fa fa-search"></i></button></td></tr>';
			}
			html += '<tbody>\
			</tbody>\
			</table>';
			$('#results').html(html);
			return;
		}

		var newWindow = window.open();
		if(!newWindow)
			alert("Popup não autorizado pelo navegador! Autorize a exibição de pop-ups no seu navegador para visualizar o resultado.");
		else
			newWindow.document.write(r.resultado);
		
	}).fail(function(r){
		end_loading();
		console.log(r);
	});
}

</script>