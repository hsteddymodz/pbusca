<style>
	.mensagem-erro {
		color:red;
		font-weight:bold;
	}
	.titulo {
		color:#5DB7FB;
		font-weight:bold;
	}
</style>
<?php


if(!$_SESSION) @session_start();

include('class/Token.class.php');
include('class/LimitarConsulta.function.php');

$token = new Token();
$token = $token->get_token();

limitarConsulta(null, $_SESSION['usuario'], 'localiza_a');
?>  
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<script>

$(document).ready(function() {	
	$(".display").click(function() {
    var tipo_pesquisa = this.id;
    //console.log(tipo_pesquisa);
		show_loading();

    switch(tipo_pesquisa) {
      case "pesquisaCpf":
        var formulario = "#form_cpf";
        break;
      case "pesquisaEmail":
        var formulario = "#form_email";
        break;
      case "pesquisaCNPJ":
        var formulario = "#form_cnpj";
        break;
      case "pesquisaTelefone":
        var formulario = "#form_telefone";
        break;
      case "pesquisaNomeEndereco":
        var formulario = "#form_nome_endereco";
        break;
    } 

    $("#alertPesquisaAjax").show();
    var dados = $(formulario).serialize();

    //console.log(dados);
    
    $.ajax({
				type: "POST",
				url: "https://probusca.com/painel/class/LocalizacaoAPI.class.php",
				dataType: "json",
				data: dados,
				success: function(obj){

          
          let data = obj;
          console.log(obj);
					//var obj = JSON.parse(JSON.parse(data));
          
          //debug
          //console.log(obj);

          if(obj.erro != null) {
            end_loading();
            alert("Erro de código "+obj.erro.codigo+". Mensagem: "+obj.erro.mensagem);
            throw new Error(obj.erro.mensagem);
          }

          // Enconde o loading 
          end_loading();

          // Limpar Tabelas
          $('#tbodyTelefoneFixo').html('');
          $('#tbodyTelefoneMovel').html('');
          $('#tbodyEnderecos').html('');
          $('#tbodyEmails').html('');
          $('#tbodyBeneficios').html('');
          $('#tbodyPartEmpresas').html('');
          $('#tbodyVinculoEmpregaticio').html('');

          // Dispondo dos resultados
          $("#resultados").show();
          $("#tableTelefoneFixo").show();
          $("#tableTelefoneMovel").show();
          $("#tableEnderecos").show();
          $("#tableEmails").show();
          $("#tableBeneficios").show();
          $("#tablePartEmpresas").show();
          $("#tableVinculoEmpregaticio").show();

          // Parte Cadastral do Usuário
          $("#nomeCadastral").html(obj.cadastro.nome);
          $("#miniBiografia").html("Nasceu em "+obj.cadastro.dataNascimento+", "+obj.cadastro.idade+" anos, de "+obj.cadastro.signo+".");
          $("#documentoCadastral").html(formataCPF(obj.cadastro.cpf));
          $("#obitoProvavel").html(obj.cadastro.obitoProvavel);
          $("#maeCadastral").html(obj.cadastro.maeNome);
          if (obj.cadastro.sexo == "M") {
            $("#sexoCadastral").html("MASCULINO");
          } else if (obj.cadastro.sexo == "F") {
            $("#sexoCadastral").html("FEMININO");
          }
          $("#nascCadastral").html(obj.cadastro.dataNascimento);
          $("#faixaEtariaCadastral").html(obj.cadastro.faixaIdade);
          
          
          // TELEFONES FIXOS E MOVEIS
          var sizeTelefonesFixo = Object.keys(obj.telefones.fixos).length;
          var sizeTelefonesMovel = Object.keys(obj.telefones.moveis).length;

          $.each(obj.telefones.fixos, function(index, value) {
            tabelaResult = '<tr>';
              tabelaResult += '<td>'+ value.telefone +'</td>';
              tabelaResult += '<td>'+ value.operadora +'</td>';
            tabelaResult += '</tr>';
            $('#tbodyTelefoneFixo').append(tabelaResult);
          });

          $.each(obj.telefones.moveis, function(index, value) {
            tabelaResult = '<tr>';
              tabelaResult += '<td>'+ value.telefone +'</td>';
              tabelaResult += '<td>'+ value.operadora +'</td>';
            tabelaResult += '</tr>';
            $('#tbodyTelefoneMovel').append(tabelaResult);
          });

          // ENDEREÇOS
          var sizeEnderecos = Object.keys(obj.enderecos).length;

          $.each(obj.enderecos, function(index, value) {
            tabelaResult = '<tr>';
              tabelaResult += '<td>'+ value.tipoLogradouro +' '+ value.logradouro +', n° '+ value.numero +'</td>';
              if(value.bairro == null) {
                tabelaResult += '<td>-</td>'
              } else {
                tabelaResult += '<td>'+ value.bairro +'</td>';
              }
              tabelaResult += '<td>'+ value.cidade +'</td>';
              tabelaResult += '<td>'+ value.uf +'</td>';
              tabelaResult += '<td>'+ value.cep +'</td>';
            tabelaResult += '</tr>';
            $('#tbodyEnderecos').append(tabelaResult);
          });

          // EMAILS
          $.each(obj.emails, function(index, value) {
            tabelaResult = '<tr>';
              tabelaResult += '<td>'+ value.email +'</td>';
              if (value.grupo == null) {
                tabelaResult += '<td> - </td>';
              } else {
                tabelaResult += '<td>'+ value.grupo +'</td>';
              }
              if (value.pontuacao == null) {
                tabelaResult += '<td> - </td>';
              } else {
                tabelaResult += '<td>'+ value.pontuacao +'</td>';
              }
            tabelaResult += '</tr>';
            $('#tbodyEmails').append(tabelaResult);
          });
          
          // BENEFÍCIO ASSISTENCIAL E INSS
          $.each(obj.rendaBeneficioAssistencial, function(index, value) {
            tabelaResult = '<tr>';
              if (value.tipoBeneficio == null) { tabelaResult += '<td> - </td>'; } else { tabelaResult += '<td>'+ value.tipoBeneficio +'</td>'; }
              tabelaResult += '<td>'+ value.faixaBeneficio +'</td>';
              tabelaResult += '<td>'+ value.descricaoBeneficio +'</td>';
              tabelaResult += '<td>'+ formatDate(value.beneficioDataRef) +'</td>';
              tabelaResult += '<td>'+ value.codigoBeneficio +'</td>';
              if (value.statusBeneficio == null) { tabelaResult += '<td> - </td>'; } else { tabelaResult += '<td>'+ value.statusBeneficio +'</td>'; }
            tabelaResult += '</tr>';
            $('#tbodyBeneficios').append(tabelaResult);
          });

          // PARTICIPAÇÃO EM EMPRESAS
          $.each(obj.participacoesEmpresas, function(index, value) {
            tabelaResult = '<tr>';
              tabelaResult += '<td>'+ value.nome +'</td>';
              tabelaResult += '<td>'+ value.dataEntrada +'</td>';
              tabelaResult += '<td>'+ value.participacao +'</td>';
              tabelaResult += '<td>'+ value.qualificacaoSocio +'</td>';
            tabelaResult += '</tr>';
            $('#tbodyPartEmpresas').append(tabelaResult);
          });

          // VINCULOS EMPREGATICIOS
          $.each(obj.rendaEmpregador, function(index, value) {
            tabelaResult = '<tr>';
              tabelaResult += '<td>'+ value.empregador +'</td>';
              tabelaResult += '<td>'+ value.cboDescricao +'</td>';
              if (value.faixaRenda == null) { tabelaResult += '<td> - </td>'; } else { tabelaResult += '<td>'+ value.faixaRenda +'</td>'; }
              if (value.rendaEstimada == null) { tabelaResult += '<td> - </td>'; } else { tabelaResult += '<td> R$'+ value.rendaEstimada +'</td>'; }
              if (value.rendaDataRef == null) { tabelaResult += '<td> - </td>'; } else { tabelaResult += '<td>'+ formatDate(value.rendaDataRef) +'</td>'; }
              if (value.setorEmpregador == null) { tabelaResult += '<td> - </td>'; } else { tabelaResult += '<td>'+ value.setorEmpregador +'</td>'; }
            tabelaResult += '</tr>';
            $('#tbodyVinculoEmpregaticio').append(tabelaResult);
          });
          
        },
        error: function(XMLHttpRequest, textStatus, errorThrown){
          alert("Status: " + textStatus); alert("Error: " + errorThrown); 
          $("#loadingSpinnerCpf").addClass('hidden');
        }

    });
    //fim ajax
  });
  //fim click
});
//fim document ready


function formataCPF(cpf){
  //retira os caracteres indesejados...
  cpf = cpf.replace(/[^\d]/g, "");

  //realizar a formatação...
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
}

function formatDate(inputDate){  // expects Y-m-d
    var splitDate = inputDate.split('-');
    if(splitDate.count == 0){
        return null;
    }

    var year = splitDate[0];
    var month = splitDate[1];
    var day = splitDate[2]; 

    return day + '/' + month + '/' + year;
}


</script>


<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Localize</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Localize <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
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
					<!-- Nav tabs --><div class="card">
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">CPF</a></li>
						<li role="presentation"><a href="#cnpj" aria-controls="cnpj" role="tab" data-toggle="tab">CNPJ</a></li>
            <li role="presentation"><a href="#nome_endereco" aria-controls="nome_endereco" role="tab" data-toggle="tab">Nome ou Endereço</a></li>
            <li role="presentation"><a href="#telefone" aria-controls="telefone" role="tab" data-toggle="tab">Telefone</a></li>
						<li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">E-mail</a></li>  
					</ul>

						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="home">
								<form id="form_cpf" method="post">
									<div class="form-group col-sm-3">
										<label for="">CPF</label>
										<input type="text" id="cpf" maxlength="11" name="cpf" class="onlyNumbers form-control" placeholder="Digite um CPF...">
									</div>
									<div class="clearfix"></div>
									<input type="hidden" id="tracker" value="1">
								</form>
								<input class="display btn btn-primary" type="button" id="pesquisaCpf" value="Pesquisar CPF" /> 
							</div>
              <!-- tab email -->
							<div role="tabpanel" class="tab-pane" id="email">
								<form id="form_email" method="post">
									<div class="form-group col-sm-4">
										<label for="">E-mail</label>
										<input type="text" id="emailPessoa" name="email" required class="form-control" placeholder="Digite um E-mail...">
									</div>
									<div class="clearfix"></div> 
									<input type="hidden" id="tracker" value="2">
								</form>
								<input class="display btn btn-primary" type="button" id="pesquisaEmail" value="Pesquisar E-mail" />					
							</div>		
              <!-- fim tab email -->	
              <!-- tab cnpj -->
              <div role="tabpanel" class="tab-pane" id="cnpj">
								<form id="form_cnpj" method="post">
									<div class="form-group col-sm-4">
										<label for="">CNPJ</label>
										<input type="text" id="cnpj" name="cnpj" maxlenght="14" required class="form-control" placeholder="Digite um CNPJ...">
									</div>
									<div class="clearfix"></div> 
									<input type="hidden" id="tracker" value="3">
								</form>
								<input class="display btn btn-primary" type="button" id="pesquisaCNPJ" value="Pesquisar CNPJ" />					
							</div>		
              <!-- fim tab cnpj -->			
              <!-- tab telefone -->
              <div role="tabpanel" class="tab-pane" id="telefone">
								<form id="form_telefone" method="post">
									<div class="form-group col-sm-4">
										<label for="">Telefone</label>
										<input type="text" id="telefone" name="telefone" required class="form-control" placeholder="Digite um Telefone...">
									</div>
									<div class="clearfix"></div> 
									<input type="hidden" id="tracker" value="4">
								</form>
								<input class="display btn btn-primary" type="button" id="pesquisaTelefone" value="Pesquisar Telefone" />					
							</div>		
              <!-- fim tab telefone -->	
              <!-- tab nome ou endereço -->
              <div role="tabpanel" class="tab-pane" id="nome_endereco">
								<form id="form_nome_endereco" method="post">
									<div class="form-group col-sm-4">
										<label for="">Nome da Pessoa (Física/Jurídica)</label>
										<input type="text" id="nome" name="nome" required class="form-control" placeholder="Digite um Nome...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Data de Nascimento/Fundação</label>
										<input type="text" id="dataNascimento" name="dataNascimento" required class="form-control" placeholder="Digite uma Data...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Sexo</label>
										  <select name="sexo" class="form-control">
                        <option value="">Selecione um sexo</option>
                        <option value="F">Feminino</option>
                        <option value="M">Masculino</option>
										  </select>
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Nome da Rua ou CEP</label>
										<input type="text" id="enderecoOuCep" name="enderecoOuCep" required class="form-control" placeholder="Digite um nome de rua ou CEP...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Número Inicial</label>
										<input type="text" id="numeroInicial" name="numeroInicial" required class="form-control" placeholder="Digite um Número...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Número Final</label>
										<input type="text" id="numeroFinal" name="numeroFinal" required class="form-control" placeholder="Digite um Número...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Complemento</label>
										<input type="text" id="complemento" name="complemento" required class="form-control" placeholder="Digite um Complemento...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Bairro</label>
										<input type="text" id="bairro" name="bairro" required class="form-control" placeholder="Digite um Bairro...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">Cidade</label>
										<input type="text" id="cidade" name="cidade" required class="form-control" placeholder="Digite uma Cidade...">
									</div>
                  <div class="form-group col-sm-4">
										<label for="">UF</label>
										<select name="uf" class="form-control">
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
									<input type="hidden" id="tracker" value="5">
								</form>
								<input class="display btn btn-primary" type="button" id="pesquisaNomeEndereco" value="Pesquisar Nome/Endereço" />					
							</div>		
              <!-- fim tab telefone -->					
					</form>
					
				</div>
				<!--<div hidden class="alert alert-warning" role="alert" id='alertPesquisaAjax'><i class="fas fa-spinner spin-the-spinner hidden" id="loadingSpinner"></i>	 Pesquisando... Por favor, aguarde</div>-->
			</div>
		</div>
	</div><!--/.row-->	

<div id="resultados" class="container table-responsive" style="overflow-x:auto" hidden>
  <!-- Dados cadastrais -->
  <div id="dadosCadastrais">
    <h3 id="nomeCadastral" style="font-weight:700; color:#1a5fcb"></h3>
    <p id="miniBiografia"></p>
    <p><strong>Documento:</strong> <b><span id="documentoCadastral" style="color:#1a5fcb"></span></b></p>
    <p><strong>Provável óbito:</strong> <b><span id="obitoProvavel" style="color:#6cba4b"></span></b></p>
    <p><strong>Mãe:</strong> <span id="maeCadastral"></span></p>
    <p><strong>Sexo:</strong> <span id="sexoCadastral"></span></p>
    <p><strong>Nascimento:</strong> <span id="nascCadastral"></span></p> 
    <p><strong>Faixa de idade:</strong> <span id="faixaEtariaCadastral"></span></p>
  </div>

  <h3 style="color:	#e76838; margin-bottom:3%;">Telefones</h3>   
	<!-- Tabela de Resultados para Telefones Fixos -->
  <table hidden id="tableTelefoneFixo" class="table table-striped" style="margin-right:1%; float:left; width:48% !important">
    <thead>
      <tr>
        <th>Telefone Fixo</th>
        <th>Operadora Fixo</th>
      </tr>
    </thead>
    <tbody id="tbodyTelefoneFixo"></tbody>
  </table>

  <table hidden id="tableTelefoneMovel" class="table table-striped" style="float:left; width:50% !important">
    <thead>
      <tr>
        <th>Telefone Celular</th>
        <th>Operadora Celular</th>
      </tr>
    </thead>
    <tbody id="tbodyTelefoneMovel"></tbody>
  </table>

  <!-- Tabela de Endereços -->
  <h3 style="color:#e76838; margin-bottom:3%;">Endereços</h3> 
  <table hidden id="tableEnderecos" class="table table-striped">
    <thead>
      <tr>
        <th>Logradouro</th>
        <th>Bairro</th>
        <th>Cidade</th>
        <th>Estado</th>
        <th>CEP</th>
      </tr>
    </thead>
    <tbody id="tbodyEnderecos"></tbody>
  </table>

  <!-- Tabela de Emails -->
  <h3 style="color:#e76838; margin-bottom:3%;">Emails</h3> 
  <table hidden id="tableEmails" class="table table-striped">
    <thead>
      <tr>
        <th>Email</th>
        <th>Grupo</th>
        <th>Pontuação</th>
      </tr>
    </thead>
    <tbody id="tbodyEmails"></tbody>
  </table>

  <!-- Tabela de Benefício Assistencial e INSS -->
  <h3 style="color:#e76838; margin-bottom:3%;">Benefício Assistencial e INSS</h3> 
  <table hidden id="tableBeneficios" class="table table-striped">
    <thead>
      <tr>
        <th>Tipo do Benefício</th>
        <th>Faixa do Benefício</th>
        <th>Descrição do Benefício</th>
        <th>Data do Benefício</th>
        <th>Código</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody id="tbodyBeneficios"></tbody>
  </table>

  <!-- Tabela de Participação em Empresas -->
  <h3 style="color:#e76838; margin-bottom:3%;">Participação em Empresas</h3> 
  <table hidden id="tablePartEmpresas" class="table table-striped">
    <thead>
      <tr>
        <th>Nome</th>
        <th>Data de Entrada</th>
        <th>Participação (%)</th>
        <th>Qualificação</th>
      </tr>
    </thead>
    <tbody id="tbodyPartEmpresas"></tbody>
  </table>

  <!-- Tabela de Vínculos Empregatícios -->
  <h3 style="color:#e76838; margin-bottom:3%;">Vínculos Empregatícios com Renda Estimada</h3> 
  <table hidden id="tableVinculoEmpregaticio" class="table table-striped">
    <thead>
      <tr>
        <th>Empregador</th>
        <th>Cargo</th>
        <th>Faixa de Renda</th>
        <th>Renda Estimada</th>
        <th>Data da Renda</th>
        <th>Setor do Empregador</th>
      </tr>
    </thead>
    <tbody id="tbodyVinculoEmpregaticio"></tbody>
  </table>


</div>
<!-- fim div resultado -->

</div><!--/.main-->
