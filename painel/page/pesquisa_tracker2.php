<?php

header('Content-Type: application/json; charset=utf-8');

if(!$_SESSION) @session_start();

include('class/Conexao.class.php');
include('class/Token.class.php');
include('class/LimitarConsulta.function.php');

$token = new Token();
$token = $token->get_token();

limitarConsulta(null, $_SESSION['usuario'], 'trackerv2');
?> 
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<style>
  a { 
    cursor:pointer;
  }
</style>

<script>
$(document).ready(function() {	
  var pag_atual = parseInt(document.getElementById("pagina_atual").value);

	$("#pesquisa").click(function() {
    if (pag_atual == 0) {
      $("#voltar_pagina").attr("disabled",true);
    } else {
      $("#voltar_pagina").attr("disabled",false);
    }

    show_loading();
    var formulario = "#form_pesquisa";
    $("#tbody_resultado").html('');

    dataString = {
      pag: '0',
      nome: document.getElementById("nome").value,
      cpf: document.getElementById("cpf").value,
      cnpj: document.getElementById("cnpj").value,
      titulo_de_eleitor: document.getElementById("titulo_de_eleitor").value,
      rg: document.getElementById("rg").value,
      cns: document.getElementById("cns").value,
      pis: document.getElementById("pis").value,
      telefone: document.getElementById("telefone").value,
      rua: document.getElementById("rua").value,
      numero: document.getElementById("numero").value,
      cidade: document.getElementById("cidade").value,
      uf: document.getElementById("uf").value
    };

    var jsonString = JSON.stringify(dataString);
    
    $.ajax({
      type: "POST",
      url: "https://probusca.com/painel/class/TrackerV2.class.php",
      dataType: "json",
      data: {data:jsonString},
      success: function(obj) {
        $("#resultado").attr("hidden",false);
        let retorno = JSON.parse(obj);
        $("#exibe_pagina_atual").html(pag_atual+1);
        $("#tbody_resultado").append(retorno.html);
        end_loading();
      }
    });
  });

  $("#avancar_pagina").click(function() {

    show_loading();
    pag_atual++;

    if (pag_atual > 0)
      $("#voltar_pagina").attr("disabled",false);

    
    var formulario = "#form_pesquisa";
    $("#tbody_resultado").html('');

    dataString = {
      pag: pag_atual,
      nome: document.getElementById("nome").value,
      cpf: document.getElementById("cpf").value,
      cnpj: document.getElementById("cnpj").value,
      titulo_de_eleitor: document.getElementById("titulo_de_eleitor").value,
      rg: document.getElementById("rg").value,
      cns: document.getElementById("cns").value,
      pis: document.getElementById("pis").value,
      telefone: document.getElementById("telefone").value,
      rua: document.getElementById("rua").value,
      numero: document.getElementById("numero").value,
      cidade: document.getElementById("cidade").value,
      uf: document.getElementById("uf").value
    };
    var jsonString = JSON.stringify(dataString);

    $.ajax({
      type: "POST",
      url: "https://probusca.com/painel/class/TrackerV2.class.php",
      dataType: "json",
      data: {data:jsonString},
      success: function(obj) {
        $("#resultado").attr("hidden",false);
        let retorno = JSON.parse(obj);
        $("#tbody_resultado").append(retorno.html);
        $("#exibe_pagina_atual").html(pag_atual+1);
        end_loading();
      }
    })
  });

  $("#voltar_pagina").click(function() {
    show_loading();
    if (pag_atual != 0)
      pag_atual--;

    if (pag_atual == 0)
      $("#voltar_pagina").attr("disabled",true);
    
    var formulario = "#form_pesquisa";
    $("#tbody_resultado").html('');

    dataString = {
      pag: pag_atual,
      nome: document.getElementById("nome").value,
      cpf: document.getElementById("cpf").value,
      cnpj: document.getElementById("cnpj").value,
      titulo_de_eleitor: document.getElementById("titulo_de_eleitor").value,
      rg: document.getElementById("rg").value,
      cns: document.getElementById("cns").value,
      pis: document.getElementById("pis").value,
      telefone: document.getElementById("telefone").value,
      rua: document.getElementById("rua").value,
      numero: document.getElementById("numero").value,
      cidade: document.getElementById("cidade").value,
      uf: document.getElementById("uf").value
    };
    var jsonString = JSON.stringify(dataString);

    $.ajax({
      type: "POST",
      url: "https://probusca.com/painel/class/TrackerV2.class.php",
      dataType: "json",
      data: {data:jsonString},
      success: function(obj) {
        $("#resultado").attr("hidden",false);
        let retorno = JSON.parse(obj);
        $("#tbody_resultado").append(retorno.html);
        $("#exibe_pagina_atual").html(pag_atual+1);
        end_loading();
      }
    })
  });
})

function z_contato(ip, email, senha, id, tipo) {
  var data_string = { ip:ip, email: email, senha:senha, id:id, tipo:tipo };
  var json_string = JSON.stringify(data_string);
  $.ajax({
      type: "POST",
      url: "https://probusca.com/painel/class/TrackerV2.class.php",
      dataType: "json",
      data: { cadUnico:json_string },
      success: function(obj) {
        let retorno = JSON.parse(obj);
        console.log(retorno);
        doModal(id, retorno);
        end_loading();
      }
    });
}

function doModal(id, formContent) {
  html =  '<div id="modalWindow'+id+'" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirm-modal" aria-hidden="true">';
    html += '<div class="modal-dialog">';
    html += '<div class="modal-content">';
    html += '<div class="modal-header">';
    html += '<a class="close" data-dismiss="modal">&times;</a>';
    html += '<h4>Resultado Tracker</h4>'
    html += '</div>';
    html += '<div class="modal-body">';

    html += '<h3>Dados Cadastrais</h3>';
    html += '<p>CPF: <span id="resultCpf">'+formContent.resultado.dados_cadastrais.cpf+'</span></p>';
    html += '<p>Nome: <span id="resultNome">'+formContent.resultado.dados_cadastrais.nome+'</span></p>';
    html += '<p>Sexo: <span id="resultSexo">'+formContent.resultado.dados_cadastrais.sexo+'</span></p>';
    html += '<p>Data de Nascimento: <span id="resultNascimento">'+formContent.resultado.dados_cadastrais.nascimento+' | Idade: '+formContent.resultado.dados_cadastrais.idade+' anos | Signo: '+formContent.resultado.dados_cadastrais.signo+'</span></p>';
    html += '<p>Nome da Mãe: <span id="resultNomeMae">'+formContent.resultado.dados_cadastrais.mae+'</span></p>';
    html += '<p>Título de Eleitor: <span id="resultTituloEleitor">'+formContent.resultado.dados_cadastrais.titulo_de_eleitor+'</span></p>';
    html += '<p>Credit Target: <span id="resultCreditTarget">'+formContent.resultado.dados_cadastrais.credit_target+'</span></p>';
    html += '<p>Renda Estimada: <span id="resultRenda">R$'+formContent.resultado.dados_cadastrais.renda+'</span></p>';
    html += '<p>Profissão: <span id="resultProf">'+formContent.resultado.dados_cadastrais.cbo+'</span></p>';

    html += '<h3>Endereços ('+formContent.resultado.enderecos.length+')</h3>'
    $.each(formContent.resultado.enderecos, function(index, value) {
      html += '<p>Endereço: <a href="https://www.google.com/maps?f=d&daddr='+value.rua+'+'+value.numero+'+'+value.cidade+'+'+value.uf+'+BR">'+value.rua+', '+value.numero+'</a></p>'
      html += '<p>Bairro: '+value.bairro+'</p>';
      html += '<p>CEP: '+value.cep+'</p>';
      html += '<p>Cidade: '+value.cidade+'</p>';
      html += '<hr>'
    });

    html += '<h3>Telefones ('+formContent.resultado.telefones.length+')</h3>';
    for(i = 0; i < formContent.resultado.telefones.length; i++) { 
      html += '<p> <b>('+(i+1)+')  </b>'+formContent.resultado.telefones[i].telefone+' | '+formContent.resultado.telefones[i].operadora+'</p>';
    };

    html += '<h3>Emails ('+formContent.resultado.emails.length+')</h3>';
    $.each(formContent.resultado.emails, function(index, value) {
      html += '<p>'+value.email+'</p>'
    });

    html += '</div>';
    html += '<div class="modal-footer">';
    html += '<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>';
    html += '</div>';  // footer
    html += '</div>';  // content
    html += '</div>';  // dialog
    html += '</div>';  // modalWindow
    $('body').append(html);
    $("#modalWindow"+id).modal();
    $("#modalWindow"+id).modal('show');
}
</script>




<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Tracker 2</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Tracker 2 <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
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
				<div hidden class="alert alert-danger" role="alert" id='alertErroAjax'></div>
				<div class="panel-body table-responsive">		
					<div class="card">
						<div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="home">
								<form id="form_pesquisa" method="post">

									<div class="form-group col-sm-4">
										<label>Nome da Pessoa</label>
										<input type="text" id="nome" name="nome" required class="form-control" placeholder="Digite um Nome...">
                  </div>
                  
                  <div class="form-group col-sm-4">
										<label>CPF</label>
										<input type="text" id="cpf" name="cpf" required class="form-control" placeholder="Digite um CPF...">
                  </div>
                  
                  <div class="form-group col-sm-4">
										<label>CNPJ</label>
										<input type="text" id="cnpj" name="cnpj" required class="form-control" placeholder="Digite um CNPJ...">
                  </div>

                  <div class="form-group col-sm-4">
										<label>Título de Eleitor</label>
										<input type="text" id="titulo_de_eleitor" name="titulo_de_eleitor" required class="form-control" placeholder="Digite um Título de Eleitor...">
                  </div>

                  <div class="form-group col-sm-4">
										<label>RG</label>
										<input type="text" id="rg" name="rg" required class="form-control" placeholder="Digite um RG...">
                  </div>
                  
                  <div class="form-group col-sm-4">
										<label>CNS</label>
										<input type="text" id="cns" name="cns" required class="form-control" placeholder="Digite um CNS...">
                  </div>

                  <div class="form-group col-sm-4">
										<label>PIS</label>
										<input type="text" id="pis" name="pis" required class="form-control" placeholder="Digite um PIS...">
                  </div>

                  <div class="form-group col-sm-4">
										<label>Telefone</label>
										<input type="text" id="telefone" name="telefone" required class="form-control" placeholder="Digite um Telefone...">
                  </div>
                  
                  <div class="form-group col-sm-4">
										<label>Rua</label>
										<input type="text" id="rua" name="rua" required class="form-control" placeholder="Digite um nome de rua ou CEP...">
                  </div>
                  
                  <div class="form-group col-sm-4">
										<label>Número</label>
										<input type="text" id="numero" name="numero" required class="form-control" placeholder="Digite um Número...">
									</div>

                  <div class="form-group col-sm-4">
										<label>Cidade</label>
										<input type="text" id="cidade" name="cidade" required class="form-control" placeholder="Digite uma Cidade...">
                  </div>
                  
                  <div class="form-group col-sm-4">
										<label>UF</label>
										<select name="uf" id="uf" class="form-control">
                      <option value="">Selecione um Estado</option>
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
                  
									<div class="clearfix"></div> 
									<input type="hidden" id="pagina_atual" value="0">
								</form>
								<input class="display btn btn-primary" type="button" id="pesquisa" value="Pesquisar" />					
							</div>				
					</form>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


  <div id="resultado" hidden>
    <h3>Página: <span id="exibe_pagina_atual"></span></h3>
    <button id="voltar_pagina" class="btn btn-primary btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
    <button id="avancar_pagina" class="btn btn-primary btn-md">Próxima <i class="fa fa-angle-right"></i> </button>
				<div style="padding: 10px">
					<table class="table table-hover">
						<thead>
							<tr>
								<th align="left">Nome</th>
								<th class="text-center">Cidade / Estado</th>
								<th class="text-center">Ação</th>
							</tr>
						</thead>
						<tbody id="tbody_resultado"></tbody>
					</table>
				</div>
	</div>

</div><!--/.main-->
