<?php

if(!$_SESSION) @session_start();

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa N3</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa N3</h1>
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
								<li role="presentation" class="active"><a href="#cpf" aria-controls="cpf" role="tab" data-toggle="tab">CPF</a></li>
								<li role="presentation"><a href="#cnpj" aria-controls="cnpj" role="tab" data-toggle="tab">CNPJ</a></li>
								<li role="presentation"><a href="#telefone" aria-controls="telefone" role="tab" data-toggle="tab">Telefone</a></li>
								<li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">E-mail</a></li>
								<li role="presentation"><a href="#endereco" aria-controls="endereco" role="tab" data-toggle="tab">Nome/Endereço</a></li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane active" id="cpf">
									<form action="" id="form_cpf" method="post">
										<input type="hidden" name="tipo" value="cpf">
										<div class="form-group col-sm-3">
											<label for="">CPF</label>
											<input type="text" name="cpf" class="form-control">
										</div>

										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar CPF</button>
										</div>
									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="cnpj">
									<form action="" id="form_cnpj" method="post">
										<input type="hidden" name="tipo" value="cnpj">
										<div class="form-group col-sm-3">
											<label for="">CNPJ</label>
											<input type="text" name="cnpj" id="cnpj" class="onlyNumbers form-control">
										</div>
										<div class="form-group col-lg-12">
											<button type="submit"  class="btn btn-primary">Buscar CNPJ</button>
										</div>
									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="telefone">
									<form action="" id="form_telefone" method="post">
										<input type="hidden" name="tipo" value="telefone">
										<div class="form-group col-sm-3">
											<label for="">Telefone</label>
											<input type="text" name="telefone" id="telefone" class="onlyNumbers form-control">
										</div>
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Telefone</button>
										</div>
									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="email">
									<form action="" id="form_email" method="post">
										<input type="hidden" name="tipo" value="email">
										<div class="form-group col-sm-3">
											<label for="">E-mail</label>
											<input type="text" name="email" id="email" class="form-control">
										</div>
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar E-mail</button>
										</div>
									</form>
								</div>

								<div role="tabpanel" class="tab-pane" id="endereco">
									<form action="" id="form_nome" method="post">
										<input type="hidden" name="tipo" value="endereco">

										<div class="form-group col-sm-3">
											<label for="">Nome</label>
											<input type="text" name="nome" id="nome" class="form-control">
										</div>
										<div class="form-group col-sm-3">
											<label for="">Data de Nascimento</label>
											<input type="text" name="dataNasc" id="dataNasc" class="form-control">
										</div>
										<div class="form-group col-sm-3">
											<label for="">Sexo</label>
											<select name="sexo" class="form-control">
												<option value=""></option>
												<option value="M">Masculino</option>
												<option value="F">Feminino</option>
											</select>
										</div>

										<div class="clearfix"></div>

										<div class="form-group col-sm-2">
											<label for="">UF</label>
											<select class="form-control" name="uf">
												<option value="">(Selecione)</option>
												<option value="AC">Acre</option>
												<option value="AL">Alagoas</option>
												<option value="AP">Amapá</option>
												<option value="AM">Amazonas</option>
												<option value="BA">Bahia</option>
												<option value="CE">Ceará</option>
												<option value="DF">Distrito Federal</option>
												<option value="ES">Espírito Santo</option>
												<option value="GO">Goiás</option>
												<option value="MA">Maranhão</option>
												<option value="MT">Mato Grosso</option>
												<option value="MS">Mato Grosso do Sul</option>
												<option value="MG">Minas Gerais</option>
												<option value="PA">Pará</option>
												<option value="PB">Paraíba</option>
												<option value="PR">Paraná</option>
												<option value="PE">Pernambuco</option>
												<option value="PI">Piauí</option>
												<option value="RJ">Rio de Janeiro</option>
												<option value="RN">Rio Grande do Norte</option>
												<option value="RS">Rio Grande do Sul</option>
												<option value="RO">Rondônia</option>
												<option value="RR">Roraima</option>
												<option value="SC">Santa Catarina</option>
												<option value="SP">São Paulo</option>
												<option value="SE">Sergipe</option>
												<option value="TO">Tocantins</option>
											</select>
										</div>

										<div class="form-group col-sm-2">
											<label for="">Cidade</label>
											<input type="text" name="cidade" class="form-control">
										</div>

										<div class="form-group col-sm-2">
											<label for="">Bairro</label>
											<input type="text" name="bairro" class="form-control">
										</div>

										<div class="form-group col-sm-3">
											<label for="">Complemento</label>
											<input type="text" name="complemento" class="form-control">
										</div>

										<div class="form-group col-sm-3">
											<label for="">Endereço ou CEP</label>
											<input type="text" name="endOuCep" class="form-control">
											<small>Não pode abreviar o nome da rua</small>
										</div>

										<div class="form-group col-sm-2">
											<label for="">Número Inicial</label>
											<input type="text" name="numInicial" class="form-control">
										</div>

										<div class="form-group col-sm-2">
											<label for="">Número Final</label>
											<input type="text" name="numFinal" class="form-control">
										</div>

										<div class="clearfix"></div>
	
										<div class="form-group col-sm-12">
											<label for="nomeMatchCompleto">
												<input id="nomeMatchCompleto" name="nomeMatchCompleto" value="true" type="checkbox"> Puxar apenas por nome completo
											</label>
										</div>

										<div class="form-group col-sm-12">
											<label for="juridico">
												<input id="juridico" name="juridico" value="true" type="checkbox"> Puxar apenas por empresas
											</label>
										</div>
										
										<div class="form-group col-lg-12">
											<button type="submit" class="btn btn-primary">Buscar Nome/Endereço</button>
										</div>


									</form>
								</div>
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
			$.post('/painel/class/AsAPI.class.php', dados)
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