<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://probusca.com/painel/upload/select2.full.js"></script>
<link href="https://probusca.com/painel/upload/select2.min.css" rel="stylesheet" />


<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {

$(".display").click(function() {
	var botao = this.id;
	$("#valoresdentro").remove();
	$("#valores").hide();
	if(botao == "display") {
		var formulario = "#tracking";
	} else {
		var formulario = "#tracking2";
	}

	var dados = $(formulario).serialize();
	console.log(dados);
	
	$.ajax({
		type: "POST",
		url: "https://probusca.com/painel/upload/tracker_post.php",
		dataType: "json",
		data:$(formulario).serialize(),
		beforeSend: function() {
			resetar();
			$('#responsecontainer').html("<center><img src='https://probusca.com/img/LoaderIcon.gif' /></center>");
		},
		success: function(data){
			montarow(data);

		}

	});
});

$("#nome").click(function() {
	$("#tracking2")[0].reset();
});

$("#telefone").click(function() {
	$("#tracking")[0].reset();
});

doc.oninput = function () {
    if (this.value.length > 14)
        this.value = this.value.slice(0,14);
}

fixo.oninput = function () {
    if (this.value.length > 2)
        this.value = this.value.slice(0,11);
}


});
function montarow (data) {
	$('#loadicon').html("<center><img src='https://probusca.com/img/LoaderIcon.gif' /></center>");
	$('#responsecontainer').html("");
	$("#valores").show();
	$("#responsecontainer").show();
	var table = $('<table class="table table-bordered table-hover" id="valoresdentro" ><td><b>Nome</b></td><td><b>Cidade</b></td><td><b>Estado</b></td><td><b></b></td></tr>');
	for(i=0; i< data.length; i++){
		if (typeof data[i].cidade === 'undefined') {
			var row = $( '<tr><td>' + data[i].nome + '</td></tr>' );
			table.append(row);
		} else {
			if(data[i].nome == null) {
				data[i].nome = "";
			}
			if(data[i].cidade == null) {
				data[i].cidade = "";
			}
			if(data[i].uf == null) {
				data[i].uf = "";
			}
			if(data[i].idj == null) {
				var row = $( '<tr><td>' + data[i].nome + '</td> <td>' + data[i].cidade + '</td> <td>' + data[i].uf + '</td> <td><span class="mais label label-primary" onclick="consulta('+ data[i].id +')"> Mais... </span></td> </tr>' );
			} else {
				var row = $( '<tr><td>' + data[i].nome + '</td> <td>' + data[i].cidade + '</td> <td>' + data[i].uf + '</td> <td><span class="mais label label-primary" onclick="consultapj('+ data[i].id +')"> Mais... </span></td> </tr>' );
			}
		}
		table.append(row);
	}
	table.append("</table>");
	$('#responsecontainer').append(table);
	$('#loadicon').html("");
}
function consulta(trackid) {
	$.ajax({
		type: 'POST',
		url : 'https://probusca.com/painel/upload/tracker_post.php',
		dataType: 'json',
		delay: 250,
		data: {trackerpop:1,idp: trackid},
		beforeSend: function() {
			$('#loadicon').html("<center><img src='https://probusca.com/img/LoaderIcon.gif' /></center>");
		},
		success: function(data){
			var conteudo = $("<div class='form-group'>")
			//DADOS CADASTRAIS
			conteudo.append("<p><b><h4>Dados Cadastrais</h4></b></p>");
			if(data.perfil.doc) {
				conteudo.append("<p><b>Documento: </b>" + data.perfil.doc + "</p>");
			}
			conteudo.append("<p><b>Nome: </b>" + data.perfil.nome + "</p>");
			if(data.perfil.sexo) {
				conteudo.append("<p><b>Sexo: </b>" + data.perfil.sexo + "</p>");
			}
			if(data.perfil.nasc) {
				if(data.perfil.aniversario) {
					conteudo.append("<p style='color:red' class='fa fa-birthday-cake' >");
				} else {
					conteudo.append("<p>");
				}
				conteudo.append("<b>Data de Nascimento: </b>" + data.perfil.nasc + " | Idade: " + data.perfil.idade + " | " + data.perfil.signo +"</p>");
			}
			if(data.perfil.mae) {
				conteudo.append("<p><b>Nome da Mãe: </b>" + data.perfil.mae + "</p>");
			}
			if(data.perfil.tituloeleitor) {
				conteudo.append("<p><b>Título de Eleitor: </b>" + data.perfil.tituloeleitor + "</p>");
			}
			if(data.perfil.rg) {
				conteudo.append("<p><b>RG: </b>" + data.perfil.rg + "</p>");
			}
			if(data.perfil.cns) {
				conteudo.append("<p><b>CNS: </b>" + data.perfil.cns + "</p>");
			}
			if(data.perfil.pis) {
				conteudo.append("<p><b>PIS: </b>" + data.perfil.pis + "</p>");
			}
			if(data.perfil.escolaridade) {
				conteudo.append("<p><b>Escolaridade: </b>" + data.perfil.escolaridade + "</p>");
			}
			if(data.perfil.classe) {
				conteudo.append("<p><b>Classe Social: </b>" + data.perfil.classe + "</p>");
			}
			if(data.perfil.target) {
				conteudo.append("<p><b>Credit Target: </b>" + data.perfil.target + "</p>");
			}
			if(data.perfil.profissao) {
				conteudo.append("<p><b>CBO: </b>" + data.perfil.profissao + "</p>");
			}
			if(data.perfil.renda) {
				conteudo.append("<p><b>Renda Presumida: </b>" + data.perfil.renda + "</p>");
			}
			var perfila = 0;
			if(data.perfil.mosaicdesc2) {
				perfila++;
				//conteudo.append("<p><b>Grupo Perfil 1: </b>" + data.perfil.mosaicgrupo1 + "</p>");
				//conteudo.append("<p><b>Tipo Perfil 1: </b>" + data.perfil.mosaictipo1 + "</p>");
				conteudo.append("<p><b>Perfil " + perfila + ": </b>" + data.perfil.mosaicdesc2 + "</p>");
			}
			if(data.perfil.mosaicdesc1) {
				perfila++;
				//conteudo.append("<p><b>Grupo Perfil 2: </b>" + data.perfil.mosaicgrupo2 + "</p>");
				//conteudo.append("<p><b>Tipo Perfil 2: </b>" + data.perfil.mosaictipo2 + "</p>");
				conteudo.append("<p><b>Perfil " + perfila + ": </b>" + data.perfil.mosaicdesc1 + "</p>");
			}
			//BOLSA FAMILIA
			if(data.bolsa) {
				conteudo.append("<hr><p><b><h4>Bolsa Família</h4></b></p>");
				conteudo.append("<p><b>NIS: </b>" + data.bolsa.num_nis + "</p>");
				conteudo.append("<p><b>Dependentes: </b>" + data.bolsa.nr_dependentes + "</p>");
				conteudo.append("<p><b>Situação: </b>" + data.bolsa.des_situacao + "</p>");
				conteudo.append("<p><b>Valor: </b>" + data.bolsa.valor_beneficio + "</p>");

				if(data.bolsadep) {
					conteudo.append("<p><b><h4>Dependentes</h4></b></p>");
					for(i=0; i< data.bolsadep.length; i++){
						if(data.bolsadep[i].nome) {
							conteudo.append("<p><b>Nome: </b>" + data.bolsadep[i].nome + " (" + data.bolsadep[i].sexo + ")</p>");
						}
						if(data.bolsadep[i].nasc) {
							conteudo.append("<p><b>Nascimento: </b>" + data.bolsadep[i].nasc + "</p>");
						}
						if(data.bolsadep[i].cidade) {
							conteudo.append("<p><b>Cidade: </b>" + data.bolsadep[i].cidade + " / " + data.bolsadep[i].uf + "</p>");
						}
					}
				}
			}

			//ENDERECOS
			if(data.enderecos) {
				conteudo.append("<hr><p><b><h4>Endereços ( " + data.enderecos.length + " )</h4></b></p>");
				for(i=0; i< data.enderecos.length; i++){
					conteudo.append("<b>Registro: </b> " + (i+1));
					if(data.enderecos[i].endereco) {
						if(data.enderecos[i].cep && data.enderecos[i].cep > 0 && data.enderecos[i].numero > 0) {
							conteudo.append("<p style='color:blue;cursor:pointer' onclick='endereco("+data.enderecos[i].cep+","+data.enderecos[i].numero+")'><b>Endereço: </b>" + data.enderecos[i].endereco + ", " + data.enderecos[i].numero + "</p>");
						} else {
							conteudo.append("<p><b>Endereço: </b>" + data.enderecos[i].endereco + ", " + data.enderecos[i].numero + "</p>");
						}
					}
					if(data.enderecos[i].compl) {
						conteudo.append("<p><b>Complemento: </b>" + data.enderecos[i].compl + "</p>");
					}
					if(data.enderecos[i].bairro) {
						conteudo.append("<p><b>Bairro: </b>" + data.enderecos[i].bairro + "</p>");
					}
					if(data.enderecos[i].cep && data.enderecos[i].cep > 0) {
						conteudo.append("<p><b>CEP: </b>" + data.enderecos[i].cep + "</p>");
					}
					if(data.enderecos[i].cidade) {
						conteudo.append("<p><b>Cidade: </b>" + data.enderecos[i].cidade + "/" + data.enderecos[i].uf + "</p>");
					}
					if (data.enderecos[i].update != "0000-00-00" && data.enderecos[i].update) {
						conteudo.append("<p><b>Data: </b>" + data.enderecos[i].update + "</p>");
					}
					conteudo.append("<br>");
				}
			}
			if(data.fones) {
				conteudo.append("<hr><p><b><h4>Telefones ( " + data.fones.length + " )</h4></b></p>");
				for(i=0; i< data.fones.length; i++){
					var dataok = "";
					if(data.fones[i].update) {
						if(data.fones[i].update == "00/00/0000") {
							dataok = " | " + data.fones[i].incluido;
						} else {
							dataok = " | " + data.fones[i].update;
						}
					}
					if(data.fones[i].fonefull) {
						conteudo.append("<p style='font-weight: bold;color:blue;cursor:pointer' onclick='telefone(" + data.fones[i].fonefull + ")'>" + data.fones[i].fonefull + " | " + data.fones[i].operadora + dataok + "</p>");
					}
				}
			}

			if(data.emails) {
				conteudo.append("<hr><p><b><h4>Emails ( " + data.emails.length + " )</h4></b></p>");
				for(i=0; i< data.emails.length; i++){
					if(data.emails[i].email) {
						conteudo.append("<p>" + data.emails[i].email + "</p>");
					}
				}
			}
			if(data.placas) {
				conteudo.append("<hr><p><b><h4>Veículos ( " + data.placas.length + " )</h4></b></p>");
				for(i=0; i< data.placas.length; i++){
					conteudo.append("<b>Registro: </b> " + (i+1));
					if(data.placas[i].placa) {
						conteudo.append("<p><b>Placa: </b>" + data.placas[i].placa + "</p>");
					}
					if(data.placas[i].modelo) {
						conteudo.append("<p><b>Modelo: </b>" + data.placas[i].modelo + "</p>");
					}
					if(data.placas[i].renavan) {
						conteudo.append("<p><b>Renavan: </b>" + data.placas[i].renavan + "</p>");
					}
					if(data.placas[i].anomod) {
						conteudo.append("<p><b>Ano Modelo: </b>" + data.placas[i].anomod + "</p>");
					}
					if(data.placas[i].anofab) {
						conteudo.append("<p><b>Ano Fabricação: </b>" + data.placas[i].anofab + "</p>");
					}
					conteudo.append("<br/>");
				}
			}

			conteudo.append("</div>");
			BootstrapDialog.show({ nl2br: false, title: 'Resultado Tracker', message: conteudo });
			$('#loadicon').html("");
		}
		
	});
}
function consultapj(trackid) {
	$.ajax({
		type: 'POST',
		url : 'https://probusca.com/painel/upload/tracker_post.php',
		dataType: 'json',
		delay: 250,
		data: {trackerpop:1,idj: trackid},
		success: function(data){
			var conteudo = $("<div class='form-group'>")
			//DADOS CADASTRAIS
			conteudo.append("<p><b><h4>Dados Cadastrais</h4></b></p>");
			if(data.perfil.cnpj) {
				conteudo.append("<p><b>CNPJ: </b>" + data.perfil.cnpj + "</p>");
			}
			conteudo.append("<p><b>Razão Social: </b>" + data.perfil.razao + "</p>");
			if(data.perfil.nomefantasia) {
				conteudo.append("<p><b>Nome Fantasia: </b>" + data.perfil.nomefantasia + "</p>");
			}
			if(data.perfil.fundacao) {
				conteudo.append("<p><b>Data da Fundação: </b>" + data.perfil.fundacao + "</p>");
			}
			if(data.perfil.ibge) {
				conteudo.append("<p><b>IBGE: </b>" + data.perfil.ibge + "</p>");
			}
			if(data.perfil.capital) {
				conteudo.append("<p><b>Capital Social: </b>" + data.perfil.capital + "</p>");
			}
			if(data.perfil.cnae) {
				conteudo.append("<p><b>CNAE: </b>" + data.perfil.cnae + "</p>");
			}

			//ENDERECOS
			if(data.enderecos) {
				conteudo.append("<hr><p><b><h4>Endereços ( " + data.enderecos.length + " )</h4></b></p>");
				for(i=0; i< data.enderecos.length; i++){
					conteudo.append("<b>Registro: </b> " + (i+1));
					if(data.enderecos[i].endereco) {
						if(data.enderecos[i].cep && data.enderecos[i].cep > 0 && data.enderecos[i].numero > 0) {
							conteudo.append("<p style='color:blue;cursor:pointer' onclick='endereco("+data.enderecos[i].cep+","+data.enderecos[i].numero+")'><b>Endereço: </b>" + data.enderecos[i].endereco + ", " + data.enderecos[i].numero + "</p>");
						} else {
							conteudo.append("<p><b>Endereço: </b>" + data.enderecos[i].endereco + ", " + data.enderecos[i].numero + "</p>");
						}
					}
					if(data.enderecos[i].compl) {
						conteudo.append("<p><b>Complemento: </b>" + data.enderecos[i].compl + "</p>");
					}
					if(data.enderecos[i].bairro) {
						conteudo.append("<p><b>Bairro: </b>" + data.enderecos[i].bairro + "</p>");
					}
					if(data.enderecos[i].cep && data.enderecos[i].cep > 0) {
						conteudo.append("<p><b>CEP: </b>" + data.enderecos[i].cep + "</p>");
					}
					if(data.enderecos[i].cidade) {
						conteudo.append("<p><b>Cidade: </b>" + data.enderecos[i].cidade + "/" + data.enderecos[i].uf + "</p>");
					}
					if (data.enderecos[i].update != "0000-00-00" && data.enderecos[i].update) {
						conteudo.append("<p><b>Data: </b>" + data.enderecos[i].update + "</p>");
					}
					conteudo.append("<br>");
				}
			}
			if(data.fones) {
				conteudo.append("<hr><p><b><h4>Telefones ( " + data.fones.length + " )</h4></b></p>");
				for(i=0; i< data.fones.length; i++){
					var dataok = "";
					if(data.fones[i].update) {
						if(data.fones[i].update == "00/00/0000") {
							dataok = " | " + data.fones[i].incluido;
						} else {
							dataok = " | " + data.fones[i].update;
						}
					}
					if(data.fones[i].fonefull) {
						conteudo.append("<p style='font-weight: bold;color:blue;cursor:pointer' onclick='telefone(" + data.fones[i].fonefull + ")'>" + data.fones[i].fonefull + " | " + data.fones[i].operadora + dataok + "</p>");
					}
				}
			}

			if(data.emails) {
				conteudo.append("<hr><p><b><h4>Emails ( " + data.emails.length + " )</h4></b></p>");
				for(i=0; i< data.emails.length; i++){
					if(data.emails[i].email) {
						conteudo.append("<p>" + data.emails[i].email + "</p>");
					}
				}
			}
			if(data.placas) {
				conteudo.append("<hr><p><b><h4>Veículos ( " + data.placas.length + " )</h4></b></p>");
				for(i=0; i< data.placas.length; i++){
					conteudo.append("<b>Registro: </b> " + (i+1));
					if(data.placas[i].placa) {
						conteudo.append("<p><b>Placa: </b>" + data.placas[i].placa + "</p>");
					}
					if(data.placas[i].modelo) {
						conteudo.append("<p><b>Modelo: </b>" + data.placas[i].modelo + "</p>");
					}
					if(data.placas[i].renavan) {
						conteudo.append("<p><b>Renavan: </b>" + data.placas[i].renavan + "</p>");
					}
					if(data.placas[i].anomod) {
						conteudo.append("<p><b>Ano Modelo: </b>" + data.placas[i].anomod + "</p>");
					}
					if(data.placas[i].anofab) {
						conteudo.append("<p><b>Ano Fabricação: </b>" + data.placas[i].anofab + "</p>");
					}
					conteudo.append("<br/>");
				}
			}

			conteudo.append("</div>");
			BootstrapDialog.show({ nl2br: false, title: 'Resultado Tracker', message: conteudo });
			$('#loadicon').html("");
		}
	});
}
function endereco(cep,numero) {
	$.each(BootstrapDialog.dialogs, function(id, dialog){
		dialog.close();
	});

	$.ajax({
		type: 'POST',
		url : 'https://probusca.com/painel/upload/tracker_post.php',
		dataType: 'json',
		delay: 250,
		data: {tracker:1,cep:cep,numero: numero,tp:'nome',cidade:0,bairro:0},
		beforeSend: function() {
			$('#loadicon').html("<center><img src='https://probusca.com/img/LoaderIcon.gif' /></center>");
		},
		success: function(data){
			montarow(data);
			$('#loadicon').html("");
		}
	});

	resetar();
}
function telefone(numero) {
	$.each(BootstrapDialog.dialogs, function(id, dialog){
		dialog.close();
	});

	$.ajax({
		type: 'POST',
		url : 'https://probusca.com/painel/upload/tracker_post.php',
		dataType: 'json',
		delay: 250,
		data: {tracker:1,fixo:numero,tp:'telefone'},
		beforeSend: function() {
			$('#loadicon').html("<center><img src='https://probusca.com/img/LoaderIcon.gif' /></center>");
		},
		success: function(data){
			montarow(data);
			$('#loadicon').html("");
		}
	});

	resetar();
}
function resetar (){
	//RESESTAR FORM INICIO
	$("#tracking")[0].reset();
	$("#tracking2")[0].reset();
}
</script>

<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Mais informações</h4>
			</div>
			<div class="modal-body">
				<div class="te"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>

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
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body">
					<div class="box-body">
						<ul class="nav nav-tabs" role="tablist">
							<li class="active"><a href="#nome" role="tab" data-toggle="tab">Nome / Endereço</a></li>
							<li><a href="#telefone" role="tab" data-toggle="tab">Documento / Telefones</a></li>
						</ul>

						<div class="tab-content">
							<br>
							<div class="tab-pane active" id="nome">
								<form action="" method="POST" id="tracking">
									<input type="hidden" id="tp" name="tp" value="nome" />
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>Nome</label>
												<input type="text" id="nome" name="nome" class="form-control" placeholder="Procurar por nome">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Endereço</label>
												<input type="text" id="endereco" name="endereco" class="form-control" placeholder="Endereço Completo">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Número</label>
												<input type="text" id="numero" name="numero" class="numeric form-control" placeholder="Número da casa">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Bairro</label>
												<select class="select2-bairro" style="width: 100%;" name="bairro" id="bairro">
													<option value="0" selected="selected">Digite o Bairro</option>
												</select>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>CEP</label>
												<input type="text" id="cep" name="cep" class="numeric form-control" placeholder="Procurar por CEP">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Estado</label>
												<select name="uf" id="uf" class="form-control user-success">
													<option value=""></option>
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
										<div class="col-sm-4">
											<div class="form-group">
												<label>Cidade</label>
												<select class="select2-cidade" style="width: 100%;" name="cidade" id="cidade">
													<option value="0" selected="selected">Digite a cidade</option>
												</select>
											</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
										<center>
											<input class="display btn btn-default" type="button" id="display" value="Procurar" />
										</center>
										</div>
									</div>
									<input type="hidden" name="tracker" value="1">
								</form>
							</div>
							<div class="tab-pane" id="telefone">
								<form action="" method="POST" id="tracking2">

									<input type="hidden" id="tp" name="tp" value="telefone" />
									<input type="hidden" name="in" value="1" />
									<p><b>Preencha somente 1 campo por pesquisa.</b></p>
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>CPF</label>
												<input type="text" id="doc" name="doc" maxlength="11" class="numeric onlyNumbers form-control" placeholder="CPF">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>CNPJ</label>
												<input type="text" id="doc" name="cnpj" maxlength="14" class="numeric onlyNumbers form-control" placeholder="CNPJ">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>Telefone</label>
												<input type="text" id="fixo" max="9999999999" name="fixo" class="numeric form-control" placeholder="Fixo ou Celular">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>Titulo de Eleitor</label>
												<input type="text" id="titulo" max="9999999999" name="titulo" class="numeric form-control">
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
												<label>PIS</label>
												<input type="text" id="pis" max="9999999999" name="pis" class="numeric form-control">
											</div>
										</div>
									</div>
									<div class="row">
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
												<label>Benefício</label>
												<input type="text" id="beneficio" max="9999999999" name="beneficio" class="numeric form-control" placeholder="">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>Placa</label>
												<input type="text" id="placa" name="placa" maxlength="7" class="form-control user-success" placeholder="Placa do Veiculo">
											</div>
										</div>
									</div>
									<div class="col-sm-12">
										<div class="form-group">
										<center>
											<input class="display btn btn-default" type="button" id="display2" value="Procurar" />
										</center>
										</div>
									</div>
									<input type="hidden" name="tracker" value="1">
								</form>
							</div>
						</div>
					</div>
					<div id="loadicon">

					</div>
					<div class="box" style="display:none;" id="valores">
						<div class="box-body table-responsive no-padding" id="responsecontainer">

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	#tabela tr:hover{
		background-color:#eaeaea;
		cursor:pointer;
	}
</style>
<script>
$(document).on("hidden.bs.modal", ".modal:not(.local-modal)", function (e) {
	$(e.target).removeData("bs.modal").find(".modal-content").empty();
});

$( ".select2-cidade" ).select2({
	ajax: {
		type: 'POST',
		url : 'https://probusca.com/painel/upload/tracker_post.php',
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				qualcep: 1,
				cidade: params.term,
				uf: $("#uf").val(),
				tipo: 2
			};
		},
		processResults: function (data) {
			return {
				results: data
			};
		},
		cache: true
	},
	minimumInputLength: 2
});
$( ".select2-bairro" ).select2({
	ajax: {
		type: 'POST',
		url : 'https://probusca.com/painel/upload/tracker_post.php',
		dataType: 'json',
		delay: 250,
		data: function (params) {
			return {
				qualcep: 1,
				bairro: params.term,
				uf2: $("#uf").val(),
				cidade2: $("#cidade").val(),
				tipo: 2
			};
		},
		processResults: function (data) {
			return {
				results: data
			};
		},
		cache: true
	},
	minimumInputLength: 2
});
</script>