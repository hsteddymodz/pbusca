<?php

if(!$_SESSION) @session_start();
if(!isset($_SESSION['usuario']) || $_SESSION['usuario'] <= 0) die("alert('Usuário inválido!'); location.href='index.php';");
include('class/LimitarConsulta.function.php');
limitarConsulta(null, $_SESSION['usuario'], 'tcpf');



?><link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
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

	var cpf = $('input[name=doc]').val();
	var cnpj = $('input[name=cnpj]').val();
	var new_cpf = '';
	var new_cnpj = '';

	for(var k = 0; k < cpf.length; k++){
		if(!isNaN(cpf[k]) && cpf[k] != ' ') new_cpf += cpf[k];
	}

	for(var k = 0; k < cnpj.length; k++){
		if(!isNaN(cnpj[k]) && cnpj[k] != ' ') new_cnpj += cnpj[k];
	}

	$('input[name=doc]').val(new_cpf);
	$('input[name=cnpj]').val(new_cnpj);

	var dados = $(formulario).serialize();
	console.log(dados);
	
	$.ajax({
		type: "POST",
		url: "https://probusca.com/painel/class/TrackerN.class.php",
		dataType: "json",
		data:$(formulario).serialize(),
		beforeSend: function() {
			resetar();
			$('#responsecontainer').html("<center><img src='https://probusca.com/painel/img/spinner.gif' /></center>");
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

/*fixo.oninput = function () {
    if (this.value.length > 2)
        this.value = this.value.slice(0,11);
}*/


});
function montarow (data) {
	$('#loadicon').html("<center><img src='https://probusca.com/painel/img/spinner.gif' /></center>");
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
			if(data[i].cpf_cnpj.length != 19) {
				var row = $( '<tr><td><a href="javascript: void(0);" onclick="consulta('+ data[i].id +');" >' + data[i].nome + '</a></td> <td>' + data[i].cidade + '</td> <td>' + data[i].uf + '</td> <td><span class="mais label label-primary" onclick="consulta('+ data[i].id +')"> Mais... </span></td> </tr>' );
			} else {
				var row = $( '<tr><td><a href="javascript: void(0);" onclick="consultapj('+ data[i].id +');" >' + data[i].nome + '</a></td> <td>' + data[i].cidade + '</td> <td>' + data[i].uf + '</td> <td><span class="mais label label-primary" onclick="consultapj('+ data[i].id +')"> Mais... </span></td> </tr>' );
			}
		}
		table.append(row);
	}
	table.append("</table>");
	$('#responsecontainer').append(table);
	$('#loadicon').html("");
}

function _calculateAge(birthday) { // birthday is a date
    let ageDifMs = Date.now() - birthday.getTime();
    let ageDate = new Date(ageDifMs); // miliseconds from epoch
    return Math.abs(ageDate.getUTCFullYear() - 1970);
}

function consulta(trackid) {
	$.ajax({
		type: 'POST',
		url : 'https://probusca.com/painel/class/TrackerN.class.php',
		dataType: 'json',
		delay: 250,
		data: {tracker: 1, idp: trackid},
		beforeSend: function() {
			$('#loadicon').html("<center><img src='https://probusca.com/painel/img/spinner.gif' /></center>");
		},
		success: function(data){
			var conteudo = $("<div class='form-group'>")
			//DADOS CADASTRAIS
			console.log(data);

			

			conteudo.append("<p><b><h4>Dados Cadastrais</h4></b></p>");
			if(data.cadastral.cpf)
				conteudo.append("<p><b>CPF: </b>" + data.cadastral.cpf + "</p>");
			
			conteudo.append("<p><b>Nome: </b>" + data.cadastral.nome + "</p>");
			if(data.cadastral.sexo) {
				conteudo.append("<p><b>Sexo: </b>" + data.cadastral.sexo + "</p>");
			}
			if(data.cadastral.nasc) {
				if(data.cadastral.aniversario) {
					conteudo.append("<p style='color:red' class='fa fa-birthday-cake' >");
				} else {
					conteudo.append("<p>");
				}
				let idade = _calculateAge(new Date(data.cadastral.nasc.split('/').reverse().join('-')));
				conteudo.append("<b>Data de Nascimento: </b>" + data.cadastral.nasc + " | Idade: " + idade + " | " + data.cadastral.signo +"</p>");
			}
			if(data.cadastral.mae) {
				conteudo.append("<p><b>Nome da Mãe: </b>" + data.cadastral.mae + "</p>");
			}
			if(data.cadastral.titulo) {
				conteudo.append("<p><b>Título de Eleitor: </b>" + data.cadastral.titulo + "</p>");
			}
			if(data.cadastral.rg) {
				conteudo.append("<p><b>RG: </b>" + data.cadastral.rg + "</p>");
			}
			if(data.cadastral.cns) {
				conteudo.append("<p><b>CNS: </b>" + data.cadastral.cns + "</p>");
			}
			if(data.pis[0]) {
				conteudo.append("<p><b>PIS: </b>" + data.pis[0].pis + "</p>");
			}
			if(data.cadastral.escolaridade) {
				conteudo.append("<p><b>Escolaridade: </b>" + data.cadastral.escolaridade + "</p>");
			}
			if(data.cadastral.classe_social) {
				conteudo.append("<p><b>Classe Social: </b>" + data.cadastral.classe_social + "</p>");
			}
			if(data.cadastral.credit_target) {
				conteudo.append("<p><b>Credit Target: </b>" + data.cadastral.credit_target + "</p>");
			}
			if(data.empregos) {
				let emprego = '';
				for(let i = 0; i < data.empregos.length; i++) {
					emprego = data.empregos[i].cbo;
				}
				conteudo.append("<p><b>CBO: </b>" + emprego + "</p>");
			}
			if(data.cadastral.renda) {
				conteudo.append("<p><b>Renda Presumida: </b>" + data.cadastral.renda + "</p>");
			}
			var perfila = 0;
			if(data.cadastral.mosaicdesc2) {
				perfila++;
				//conteudo.append("<p><b>Grupo Perfil 1: </b>" + data.perfil.mosaicgrupo1 + "</p>");
				//conteudo.append("<p><b>Tipo Perfil 1: </b>" + data.perfil.mosaictipo1 + "</p>");
				conteudo.append("<p><b>Perfil " + perfila + ": </b>" + data.cadastral.mosaicdesc2 + "</p>");
			}
			if(data.cadastral.mosaicdesc1) {
				perfila++;
				//conteudo.append("<p><b>Grupo Perfil 2: </b>" + data.perfil.mosaicgrupo2 + "</p>");
				//conteudo.append("<p><b>Tipo Perfil 2: </b>" + data.perfil.mosaictipo2 + "</p>");
				conteudo.append("<p><b>Perfil " + perfila + ": </b>" + data.cadastral.mosaicdesc1 + "</p>");
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
			if(data.telefones) {
				conteudo.append("<hr><p><b><h4>Telefones ( " + data.telefones.length + " )</h4></b></p>");
				for(i=0; i< data.telefones.length; i++){
					if(data.telefones[i].telefone)
						conteudo.append("<p style='font-weight: bold;color:blue;cursor:pointer' onclick='telefone(" + data.telefones[i].telefone + ")'>" + data.telefones[i].telefone + "</p>");
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
		url : 'https://probusca.com/painel/class/TrackerN.class.php',
		dataType: 'json',
		delay: 250,
		data: {tracker:1, idj: trackid},
		beforeSend: function() {
			$('#loadicon').html("<center><img src='https://probusca.com/painel/img/spinner.gif' /></center>");
		},
		success: function(data){
			var conteudo = $("<div class='form-group'>");
			//DADOS CADASTRAIS
			console.log(data);
			conteudo.append("<p><b><h4>Dados Cadastrais</h4></b></p>");
			if(data.cadastral.cnpj) {
				conteudo.append("<p><b>CNPJ: </b>" + data.cadastral.cnpj + "</p>");
			}
			conteudo.append("<p><b>Razão Social: </b>" + data.cadastral.razao_social + "</p>");
			if(data.cadastral.nome_fantasia) {
				conteudo.append("<p><b>Nome Fantasia: </b>" + data.cadastral.nome_fantasia + "</p>");
			}
			if(data.cadastral.data_fundacao) {
				conteudo.append("<p><b>Data da Fundação: </b>" + data.cadastral.data_fundacao.split('-').reverse().join('/') + "</p>");
			}
			if(data.cadastral.ibge) {
				conteudo.append("<p><b>IBGE: </b>" + data.cadastral.ibge + "</p>");
			}
			if(data.cadastral.capital_social) {
				conteudo.append("<p><b>Capital Social: </b>R$ " + parseFloat(data.cadastral.capital_social).toFixed(2) + "</p>");
			}
			if(data.cadastral.cnae) {
				conteudo.append("<p><b>CNAE: </b>" + data.cadastral.cnae + "</p>");
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
			if(data.telefones) {
				conteudo.append("<hr><p><b><h4>Telefones ( " + data.telefones.length + " )</h4></b></p>");
				for(i=0; i< data.telefones.length; i++){
					if(data.telefones[i].telefone) {
						conteudo.append("<p style='font-weight: bold;color:blue;cursor:pointer' onclick='telefone(" + data.telefones[i].telefone + ")'>" + data.telefones[i].telefone + "</p>");
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
		url : 'https://probusca.com/painel/class/TrackerN.class.php',
		dataType: 'json',
		delay: 250,
		data: {tracker:1,cep:cep,numero: numero,tp:'nome',cidade:0,bairro:0},
		beforeSend: function() {
			$('#loadicon').html("<center><img src='https://probusca.com/painel/img/spinner.gif' /></center>");
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
		url : 'https://probusca.com/painel/class/TrackerN.class.php',
		dataType: 'json',
		delay: 250,
		data: {tracker:1,fixo:numero,tp:'telefone'},
		beforeSend: function() {
			$('#loadicon').html("<center><img src='https://probusca.com/painel/img/spinner.gif' /></center>");
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
			<li class="active">Pesquisa Tracker CPF</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Tracker CPF</h1>
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
				<div class="panel-body">

								<form action="" method="POST" id="tracking2">

									<input type="hidden" id="tp" name="tp" value="telefone" />
									<input type="hidden" name="in" value="1" />
									<p><b>Preencha somente 1 campo por pesquisa.</b></p>
									<div class="row">
										<div class="col-sm-4">
											<div class="form-group">
												<label>CPF</label>
												<input type="text" id="doc" name="doc" class=" form-control" placeholder="CPF">
											</div>
										</div>
										<div class="col-sm-4">
											<div class="form-group">
												<label>CNPJ</label>
												<input type="text" id="doc" name="cnpj"  class=" form-control" placeholder="CNPJ">
											</div>
										</div>
										<!--<div class="col-sm-4">
											<div class="form-group">
												<label>Telefone</label>
												<input type="text" id="fixo" max="9999999999" name="fixo" class="numeric form-control" placeholder="Fixo ou Celular">
											</div>
										</div>-->
									</div>
									<!-- <div class="row"> 
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
									</div>-->
									<div class="col-sm-12">
										<div class="form-group">
										<center>
											<input class="display btn btn-default" type="button" id="display2" value="Procurar" />
										</center>
										</div>
									</div>
									<input type="hidden" name="tracker" value="1">
								</form>

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