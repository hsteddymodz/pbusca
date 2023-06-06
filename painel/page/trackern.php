<?php

include('class/Conexao.class.php');

if(!$_SESSION) @session_start();
if(!$_SESSION['usuario'] || $_SESSION['usuario'] <= 0) die("alert('Usuário inválido!'); location.href='index.php';");
include('class/LimitarConsulta.function.php');

limitarConsulta(null, $_SESSION['usuario'], 't2');

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Tracker</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Tracker</h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">

				<div class="col-lg-12">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-xmd"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body">
					<div class="box-body">
						<ul class="nav nav-tabs" role="tablist">
							<li class="active"><a href="#nome" role="tab" data-toggle="tab">Nome / Endereço</a></li>
							<li><a href="#telefone" role="tab" data-toggle="tab">Documento / Telefones</a></li>
							<li><a href="#empresa" role="tab" data-toggle="tab">Empresa</a></li>
						</ul>

						<div class="tab-content">
							<div class="tab-pane active" id="nome">
								<form action="" method="POST">
									<input type="hidden" name="tipo" value="nome" />
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label>Nome</label>
												<input type="text" id="nome" name="nome" class="form-control" placeholder="Procurar por nome">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>Endereço</label>
												<input type="text" id="endereco" name="endereco" class="form-control" placeholder="Endereço Completo">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>Número</label>
												<input type="text" id="numero" name="numero" class="numeric form-control" placeholder="Número da casa">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>Bairro</label>
												<input class="form-control"  name="bairro" id="bairro">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>CEP</label>
												<input type="text" id="cep" name="cep" class="numeric form-control" placeholder="Procurar por CEP">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>Estado</label>
												<select class="form-control" name="uf" id="uf">
													<option selected="" value="">Selecione o UF</option>
													<option value="AC">AC</option>
													<option value="AL">AL</option>
													<option value="AM">AM</option>
													<option value="AP">AP</option>
													<option value="BA">BA</option>
													<option value="CE">CE</option>
													<option value="DF">DF</option>
													<option value="ES">ES</option>
													<option value="GO">GO</option>
													<option value="MA">MA</option>
													<option value="MG">MG</option>
													<option value="MS">MS</option>
													<option value="MT">MT</option>
													<option value="PA">PA</option>
													<option value="PB">PB</option>
													<option value="PE">PE</option>
													<option value="PI">PI</option>
													<option value="PR">PR</option>
													<option value="RJ">RJ</option>
													<option value="RN">RN</option>
													<option value="RO">RO</option>
													<option value="RR">RR</option>
													<option value="RS">RS</option>
													<option value="SC">SC</option>
													<option value="SE">SE</option>
													<option value="SP">SP</option>
													<option value="TO">TO</option>
												</select>
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>Cidade</label>
												<input class="form-control" name="cidade" id="cidade">
											</div>
										</div>

										<div class="col-sm-12">
											<div class="form-group text-center">
												<button class="btn btn-primary" type="submit">Procurar</button>
											</div>
										</div>

									</div>
								</form>
							</div>
							<div class="tab-pane" id="telefone">
								<form action="" method="POST">

									<input type="hidden" name="tipo" value="telefone" />

									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>CPF</label>
												<input type="text" name="cpf" class=" form-control" placeholder="CPF">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Telefone</label>
												<input type="text" name="telefone" class="numeric form-control" placeholder="Fixo ou Celular">
											</div>
										</div>

										<div class="col-sm-4">
											<div class="form-group">
												<label>TSE</label>
												<input type="text" name="tse" class=" form-control">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>NIS</label>
												<input type="text" id="nis" max="9999999999" name="nis" class="numeric form-control">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>RG</label>
												<input type="text" id="rg" max="9999999999" name="rg" class="numeric form-control">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>CNS</label>
												<input type="text" id="cns" max="9999999999" name="cns" class="numeric form-control">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>PIS</label>
												<input type="text" id="pis" name="pis" class="form-control">
											</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
											<button class="btn btn-primary" type="submit">Procurar</button>
										</div>
									</div>
								</form>
							</div>

							<div class="tab-pane" id="empresa">
								<form action="" method="POST">

									<input type="hidden" name="tipo" value="empresa" />
									<div class="row">
										<div class="col-sm-3">
											<div class="form-group">
												<label>CNPJ</label>
												<input type="text" name="cnpj"  class=" form-control" placeholder="CNPJ">
											</div>
										</div>
										<div class="col-sm-3">
											<div class="form-group">
												<label>Telefone</label>
												<input type="text" name="telefone" class="numeric form-control" placeholder="Fixo ou Celular">
											</div>
										</div>

										<div class="col-sm-4">
											<div class="form-group">
												<label>Endereço</label>
												<input type="text" name="endereco" class="form-control">
											</div>
										</div>


										<div class="col-sm-2">
											<div class="form-group">
												<label>Número</label>
												<input type="text" name="numero" class="form-control">
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group">
												<label>Bairro</label>
												<input type="text" name="bairro" class="form-control">
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group">
												<label>CEP</label>
												<input type="text" name="cep" class="form-control">
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group">
												<label>UF</label>
												<select class="form-control" name="uf" id="uf">
													<option selected="" value="">Selecione o UF</option>
													<option value="AC">AC</option>
													<option value="AL">AL</option>
													<option value="AM">AM</option>
													<option value="AP">AP</option>
													<option value="BA">BA</option>
													<option value="CE">CE</option>
													<option value="DF">DF</option>
													<option value="ES">ES</option>
													<option value="GO">GO</option>
													<option value="MA">MA</option>
													<option value="MG">MG</option>
													<option value="MS">MS</option>
													<option value="MT">MT</option>
													<option value="PA">PA</option>
													<option value="PB">PB</option>
													<option value="PE">PE</option>
													<option value="PI">PI</option>
													<option value="PR">PR</option>
													<option value="RJ">RJ</option>
													<option value="RN">RN</option>
													<option value="RO">RO</option>
													<option value="RR">RR</option>
													<option value="RS">RS</option>
													<option value="SC">SC</option>
													<option value="SE">SE</option>
													<option value="SP">SP</option>
													<option value="TO">TO</option>
												</select>
											</div>
										</div>

										<div class="col-sm-3">
											<div class="form-group">
												<label>Cidade</label>
												<input type="text" name="cidade" class="form-control">
											</div>
										</div>

									</div>

									<div class="col-sm-12">
										<div class="form-group text-center">
											<button class="btn btn-primary" type="submit">Procurar</button>
										</div>
									</div>
								</form>
							</div>

						</div>
					</div>

					<div class="clearfix"></div>
					</div>
					<div id="loadicon">

					</div>
					<div class="col-lg-12" id="responsecontainer">

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Mais Informações <button type="button" data-clipboard-action="copy" data-clipboard-target="#copy" class="btn btn-copy btn-xs btn-sm btn-primary">Copiar Informações</button></h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>

  </div>
</div>
<textarea name="" id="copy" cols="30" rows="10" style="opacity:0;"></textarea>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
<script>

	

	var clipboard = new ClipboardJS('.btn-copy');

	clipboard.on('success', function(e) {
	    console.info('Action:', e.action);
	    console.info('Text:', e.text);
	    console.info('Trigger:', e.trigger);

	    e.clearSelection();
	});

	clipboard.on('error', function(e) {
	    console.error('Action:', e.action);
	    console.error('Trigger:', e.trigger);
	});


	function ucfirst(string) {
	    return string.charAt(0).toUpperCase() + string.slice(1);
	}

	function printPopup (data) {
        var mywindow = window.open('', 'new div', 'height=400,width=600');
        mywindow.document.write(data);
        mywindow.print();
        mywindow.close();

        return true;
    }

    function translate_col (col) {

    	let translate = {
    		'cpf':'CPF',
    		'endereco':'Endereço',
    		'classe_social':'Classe Social',
    		'rg':'RG',
    		'compl':'Complemento',
    		'cidade':'Cidade',
    		'uf':'UF',
    		'cep':'CEP',
    		'dataadmissao':'Data de Admissão',
    		'salario':"Salário",
    		'tiposalario':'Tipo do Salário',
    		'cnpj':'CNPJ',
    		'credit_target': 'Target de Crédito',
    		'nasc': 'Nascimento',
    		'mae':'Nome da mãe',
    		'razao':'Razão Social'
    	};

    	if(translate[col])
    		return translate[col];

    	return ucfirst(col);

    }

    function pesquisa_aux (id, tipo) {

    	show_loading();
    	$.post('/painel/class/TrackerN.class.php', {tipo:tipo, id:id})
		.done(function(r) {
			end_loading();
			try {

				let dados = JSON.parse(r);
				console.log(dados);

				let tabela = '<table cellpadding="10" width="100%">';
				let textContent = '';
				let cbo = '';
				for(let titulo in dados) {
					if(dados[titulo].length == 0 )
						continue;
					textContent += '\n\n' + ucfirst(titulo) + '\n\n';
					tabela += '<tr><td colspan="2"><h3>'+ucfirst(titulo)+'</h3></td></tr>';
					for(let col in dados[titulo]) {
						if(typeof dados[titulo][col] === 'object')  {
							let tmp = ''
							for(let subcol in dados[titulo][col]) {
								textContent += subcol + ': ' + dados[titulo][col][subcol] + '\n';
								tmp += "<b>" + translate_col(subcol) + ":</b> " + dados[titulo][col][subcol] + "<br>";

								if(subcol == 'cbo')
									cbo = dados[titulo][col][subcol];

							}
							textContent += '\n';
							if(titulo == 'telefones')
								tabela += '<tr><td colspan="2">'+tmp+'</td></tr></tr>';
							else
								tabela += '<tr><td colspan="2">'+tmp+'</td></tr><tr><td colspan="2"><hr /></td></tr>';
						} else {
							textContent += translate_col(col) + ': ' + dados[titulo][col] + '\n';
							tabela += '<tr><td><b>'+translate_col(col)+'</b></td><td>'+dados[titulo][col]+'</td></tr>';

							if(col == 'credit_target')
								tabela += '<tr><td><b>CBO</b></td><td id="cbo"></td></tr>';

							if(col == 'nasc') {

								function _calculateAge(birthday) { // birthday is a date
								    let ageDifMs = Date.now() - birthday.getTime();
								    let ageDate = new Date(ageDifMs); // miliseconds from epoch
								    return Math.abs(ageDate.getUTCFullYear() - 1970);
								}

								let idade = _calculateAge(new Date(dados[titulo][col].split('/').reverse().join('-')));
								tabela += '<tr><td><b>Idade</b></td><td>'+idade+' anos</td></tr>';
							}
						}

						/*if(subcol == 'credit_target')
									tmp += "<b>CBO:</b> <span id=\"#cbo\">" + dados[titulo][col][subcol] + "</span><br>";
								if(subcol == 'cbo' && $('#cbo').length)
									$('#cbo').html(dados[titulo][col][subcol]);*/
					}
				}
				$('#copy').val(textContent);
				tabela += '</table>';

				$('#myModal .modal-body').html(tabela);
				if($('#cbo').length)
					$('#cbo').html(cbo);
				end_loading();
				$('#myModal').modal('show');

			} catch(e) {
				end_loading();
				console.log(e);
				$('#myModal .modal-body').html('<h1>Nenhuma informação encontrada!</h1>');
				$('#myModal').modal('show');
				
			}
			
			//$('#responsecontainer').html(r);

		}).fail(function(r) {
			end_loading();
			console.log(r);
		});
    }
	
	let all_forms = document.querySelectorAll('form');
	for (var i = 0; i < all_forms.length; i++) {
		all_forms[i].addEventListener("submit", function(e) {

			e.preventDefault();
			$('#responsecontainer').html('');
			show_loading();
			
			let dados = $(this).serialize();
			console.log(dados);
			$.post('/painel/class/TrackerN.class.php', dados)
			.done(function(r) {
				console.log(r);
				end_loading();
				try {
					let dados = JSON.parse(r);
					console.log(dados);

					if(dados.error)
						return $('#responsecontainer').html('<h1>'+dados.error+'</h1>');

					if(dados.Error)
						return $('#responsecontainer').html('<h1>'+dados.Error+'</h1>');

					if(dados.length == 0)
						return $('#responsecontainer').html('<h1>Nenhum resultado encontrado!</h1>');
					
					let tabela = '<table class="table"><thead><tr><td>Nome</td><td>CPF/CNPJ</td><td>Nascimento</td><td>Relacionado</td><td>Cidade</td><td>Ação</td></tr></thead><tbody>';

					for(let i in dados) {
						let tipo = 'idpf';
						if(dados[i].cpf_cnpj.length != 14)
							tipo = 'idpj';

						for(let col in dados[i]) {
							if(dados[i][col] == null || dados[i][col] == '' || dados[i][col] == ' ')
								dados[i][col] = '-';
						}
						tabela += '<tr><td><a href="javascript:void(0);" onclick="pesquisa_aux(' + dados[i].id + ', \''+tipo+'\');">'+dados[i].nome+'</a></td><td>'+dados[i].cpf_cnpj+'</td><td>'+dados[i].nasc+'</td><td>'+dados[i].relacionado+'</td><td>'+dados[i].cidade+'</td><td><button type="button" onclick="pesquisa_aux(' + dados[i].id + ', \''+tipo+'\');" class="btn btn-primary btn-xs">+ info</button></td></tr>';
					}

					tabela += "</tbody></table>";

					$('#responsecontainer').html(tabela);

				} catch(e) {
					console.log(e);
					$('#responsecontainer').html('<h1>Nenhuma informação encontrada!</h1>');
				}
				
				//$('#responsecontainer').html(r);

			}).fail(function(r) {
				end_loading();
				console.log(r);
			});

			return false;

		});
	}

</script>