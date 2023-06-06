<?php

include('class/RegistrarConsulta.php');
//include('../class/Conexao.class.php');
include('class/onlyNumbers.function.php');
include('class/Token.class.php');
include('class/LimitarConsulta.function.php');


//$con = new Conexao();

//Verifica se o usuário está logado
if(!$_SESSION) @session_start();

//Gera Token
$token = new Token();
$token = $token->get_token();

//Verifica se o usuário pode realizar a Consulta
limitarConsulta(null, $_SESSION['usuario'], 'nett');

?>



  <style>

  h3{
    margin: 20px 0 20px 0;
  }

  #telaApresentacaoResultados{
    display: none;
  }

  #telaApresentacaoListaResultados{
    display: none;
  }

  #telaBotoesControle button{
    display: none;
    margin-bottom: 40px;
  }


  </style>



	<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

		<div class="row">
			<ol class="breadcrumb">
				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
				<li class="active">NETT</li>
			</ol>
		</div><!--/.row-->

		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Pesquisa de Informações <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
			</div>
		</div><!--/.row-->

    <div class="alert alert-warning">
      <p>Prezado usuário, ao utilizar este Módulo, dependendo do tipo de informação, <b>a pesquisa pode demorar até 5 minutos</b>.</p>
    </div>

      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
          <a href="#cpf" aria-controls="cpf" data-toggle="tab">CPF</a>
        </li>
				<li role="presentation" class="">
          <a href="#cnpj" aria-controls="cnpj" data-toggle="tab">CNPJ</a>
        </li>
				<li role="presentation" class="">
          <a href="#nome" aria-controls="nome" data-toggle="tab">Nome</a>
        </li>
				<li role="presentation" class="">
          <a href="#telefone" aria-controls="telefone" data-toggle="tab">Telefone</a>
        </li>
				<li role="presentation" class="">
          <a href="#endereco" aria-controls="endereco" data-toggle="tab">Endereço</a>
        </li>
      </ul>

      <div class="tab-content">
				<!--Pesquisa CPF-->
        <div class="tab-pane active" id="cpf" role="tabpanel">
            <form action="" method="post" id="ConsultaCPF">
              <div class="form-row">
                <div class="form-group col-6">
                  <label>CPF*</label>
                  <input type="text" class="form-control cpf" name="cpf" autocomplete="off" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-6">
                  <input type="hidden" name="tipoRequisicao" value="ConsultaCPF">
									<input type="hidden" name="token" value="<?php echo $token; ?>">
                  <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
                </div>
              </div>
            </form>
        </div>
				<!--Pesquisa CNPJ-->
	      <div class="tab-pane" id="cnpj" role="tabpanel">
	            <form action="" method="post" id="ConsultaCNPJ">
	              <div class="form-row">
	                <div class="form-group col-6">
	                  <label>CNPJ*</label>
	                  <input type="text" class="form-control cnpj" name="cnpj" autocomplete="off" required>
	                </div>
	              </div>
	              <div class="form-row">
	                <div class="form-group col-6">
	                  <input type="hidden" name="tipoRequisicao" value="ConsultaCNPJ">
										<input type="hidden" name="token" value="<?php echo $token; ?>">
	                  <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
	                </div>
	              </div>
	            </form>
	        </div>
				<!--Pesquisa Nome-->
        <div class="tab-pane" id="nome" role="tabpanel">
          <form action="" method="post" id="ConsultaNome">
            <div class="form-row">
							<div class="form-group col-6">
								<label>Estado*</label>
								<select type="text" class="form-control selectEstados flexselect" name="uf"></select>
							</div>
              <div class="form-group col-6">
                <label>Nome*</label>
                <input type="text" class="form-control" name="nome" autocomplete="off" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-6">
                <input type="hidden" name="page" value="1">
								<input type="hidden" name="tipoRequisicao" value="ConsultaNome">
								<input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" value="Pesquisar" class="btn btn-outline-primary">
              </div>
            </div>
          </form>
        </div>
			  <!--Pesquisa Telefone-->
        <div class="tab-pane" id="telefone" role="tabpanel">
          <form action="" method="post" id="ConsultaTelefone">
            <div class="form-row">
              <div class="form-group col-md-12">
                <label>Telefone (DDD + número)</label>
                <input type="text" name="telefone" class="form-control phone" autocomplete="off" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-6">
								<input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
                <input type="hidden" name="tipoRequisicao" value="ConsultaTelefone">
              </div>
            </div>
          </form>
        </div>
				<!--Pesquisa Endereço-->
        <div class="tab-pane fade" id="endereco" role="tabpanel" aria-labelledby="endereco-tab">
          <form action="" method="post" id="ConsultaCEP">

            <legend>Como você deseja fazer a Pesquisa?</legend>
            <div class="radio">
              <label>
                <input type="radio" name="radio_consultaCEP" value="1">
                CEP
              </label>
            </br>
              <label>
              <input type="radio" name="radio_consultaCEP" value="2">
                Endereço
              </label>
            </div>

            <section id="ConsultaCEP_byCEP" style="display:none;">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Estado*</label>
                  <select type="text" class="form-control selectEstados flexselect" id="form_consultaEndereco_uf" name="uf"></select>
                </div>
                <div class="form-group col-md-6">
                  <label>Cidade*</label>
                  <select class="form-control flexselect" id="form_consultaEndereco_cidade" name="cidade"></select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Rua, avenida, praça... </label>
                  <input type="text" class="form-control" id="form_consultaEndereco_term" name="term"></select>
                </div>
                <div class="form-group col-md-6">
                  <label>Rua, avenida, praça... </label>
                  <select class="form-control flexselect" id="form_consultaEndereco_endereco" name="endereco"></select>
                </div>
              </div>
            </section>

            <section id="ConsultaCEP_byAddress" style="display: none;">
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label>CEP*</label>
                  <input type="text" class="form-control cep" name="cep" autocomplete="off">
                </div>
              </div>
            </section>

            <section id="ConsultaCEP_byNumber" style="display: none;">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Número de Início</label>
                  <input type="number" class="form-control" name="numFinal" autocomplete="off">
                </div>
                <div class="form-group col-md-6">
                  <label>Número Final</label>
                  <input type="number" class="form-control" name="numInicio" autocomplete="off">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                  <input type="hidden" name="page" value="1">
                  <input type="hidden" name="token" value="<?php echo $token; ?>">
                  <input type="hidden" name="tipoRequisicao" value="ConsultaCEP">
                  <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
                </div>
              </div>
            </section>

          </form>
        </div>


      </div>


  </div>

  <!--Nesta DIV é armazenado informações detalhadas da pessoa-->
  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main" id="telaApresentacaoResultados">
  </div>

  <!--Nesta DIV é armazenado uma lista de resultados-->
  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main" id="telaApresentacaoListaResultados">
  </div>

  <!--DIV com os botões de controle-->
  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main" id="telaBotoesControle">
    <button class="btn btn-primary" id="btn_voltar"><span class="glyphicon glyphicon-arrow-left"></span> Voltar</button>
    <button class="btn btn-primary" id="btn_imprimir"><span class="glyphicon glyphicon-print"></span> Imprimir</button>
    <button class="btn btn-primary" id="btn_maisResultados"> Carregar Mais Páginas de Resultados</button>
  </div>

<script>

  window.onload = function(){

    /*--------------------------------------------------------------------------------------------|
    |                                                                                             |
    |                                  PONTEIROS PARA ELEMENTOS DO DOM                            |
    |                                        E VARIÁVEIS GLOBAIS                                  |
    |                                                                                             |
    ---------------------------------------------------------------------------------------------*/

    /*Popula os selectbox com o nome dos Estados brasileiros*/
    var estadosBrasileiros = {
    "UF": [
        {"nome": "(Selecione)", "sigla": ""},
        {"nome": "Acre", "sigla": "AC"},
        {"nome": "Alagoas", "sigla": "AL"},
        {"nome": "Amapá", "sigla": "AP"},
        {"nome": "Amazonas", "sigla": "AM"},
        {"nome": "Bahia", "sigla": "BA"},
        {"nome": "Ceará", "sigla": "CE"},
        {"nome": "Distrito Federal", "sigla": "DF"},
        {"nome": "Espírito Santo", "sigla": "ES"},
        {"nome": "Goiás", "sigla": "GO"},
        {"nome": "Maranhão", "sigla": "MA"},
        {"nome": "Mato Grosso", "sigla": "MT"},
        {"nome": "Mato Grosso do Sul", "sigla": "MS"},
        {"nome": "Minas Gerais", "sigla": "MG"},
        {"nome": "Pará", "sigla": "PA"},
        {"nome": "Paraíba", "sigla": "PB"},
        {"nome": "Paraná", "sigla": "PR"},
        {"nome": "Pernambuco", "sigla": "PE"},
        {"nome": "Piauí", "sigla": "PI"},
        {"nome": "Rio de Janeiro", "sigla": "RJ"},
        {"nome": "Rio Grande do Norte", "sigla": "RN"},
        {"nome": "Rio Grande do Sul", "sigla": "RS"},
        {"nome": "Rondônia", "sigla": "RO"},
        {"nome": "Roraima", "sigla": "RR"},
        {"nome": "Santa Catarina", "sigla": "SC"},
        {"nome": "São Paulo", "sigla": "SP"},
        {"nome": "Sergipe", "sigla": "SE"},
        {"nome": "Tocantins", "sigla": "TO"}
    ]
    };
    $(document).ready(function(){
        $.each(estadosBrasileiros.UF, function(key, value){
          $('.selectEstados').append('<option value="'+value.sigla+'">'+value.nome+'</option>');
        });
    });

    /*Carrega Ajax Form*/
    var options = {
        dataType: 'html',
        url: 'https://probusca.com/painel/page/request_nett.php',
        beforeSubmit: beforeSubmit,
        success: responseAjax,
        error: errorAjax
      };
    $('form').ajaxForm(options);

    /*Carregamento Plugin Flexselect*/
    /*$("select.flexselect").flexselect();*/

    /*Validação de Campos*/
    $('.time').mask('00:00:00');
    $('.date_time').mask('00/00/0000 00:00:00');
    $('.cep').mask('00000-000');
    $('.phone').mask('00-0000-00000');
    $('.phone_with_ddd').mask('(00) 0000-0000');
    $('.phone_us').mask('(000) 000-0000');
    $('.mixed').mask('AAA 000-S0S');
    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
    $('.money').mask('000.000.000.000.000,00', {reverse: true});
    $('.money2').mask("#.##0,00", {reverse: true});

    /*Ponteiros*/
    var dom_tela_apresentacao = $("#telaApresentacaoResultados");
    var dom_tela_apresentacao_listaResultados = $("#telaApresentacaoListaResultados");
    var dom_form_consultaCpf = $("#ConsultaCPF");
    var dom_form_consultaCNPJ = $("#ConsultaCNPJ");
    var dom_form_consultaNome = $("#ConsultaNome");
    var dom_form_consultaCEP = $("#ConsultaCEP");
    var dom_btnVoltar = $("#btn_voltar");
    var dom_btnImprimir = $("#btn_imprimir");
    var dom_btnMaisResultados = $("#btn_maisResultados");

    /*Variáveis globais*/
    var global_respostaConsulta;
    var global_tipoRequisicao;
    var global_indice_page = 1;
    var global_posicaoScroll = 0;
    var global_contador_tentativas = 0;

    /*--------------------------------------------------------------------------------------------|
    |                                                                                             |
    |                                        REQUISIÇÕES AJAX                                     |
    |                                                                                             |
    |                                                                                             |
    ---------------------------------------------------------------------------------------------*/

      function beforeSubmit(data)
      {
        console.log(data);
        /*Mostra tela de carregamento*/
        show_loading();
        /*Limpa a div onde é colocado o resultado da consulta*/
        dom_tela_apresentacao.empty();

        $.each(data, function(indice, item){
          /*Obtém o tipo de consulta*/
          if(item['name'] == "tipoRequisicao"){
            global_tipoRequisicao = item['value'];
          }
        });

        return true;
      }

      function responseAjax(dados)
      {
        end_loading();
        console.log(dados);
        /*Verifica erros de programação ou token*/
        if(dados.match(/ACESSO NEGADO/))
        {
          return aler("Erro no Script!");
        }
        if(dados.match(/TOKEN INVALIDO/))
        {
          return aler("Por favor, atualize a página!");
        }

        /*Verifica se ocorreu erro na Resolução do Captcha*/
        if(dados.match(/captcha took too much time/))
        {
          /*Verifica se a quantidade de tentativas é menor ou iguar a 5*/
          if(global_contador_tentativas > 4)
          {
            global_contador_tentativas = 0;
            return alert("Infelizmente não conseguimos obter o Dados. Por favor, tente mais tarde! Caso o problema persista, informe ao Administrador do Sistema!");
          }
          global_contador_tentativas++;

          /*alert("Um erro ocorreu! Por favor, tente novamente. Se o problema persistir, informe ao Administrador do Sistema!");*/
          /*Se acontecer erro, tenta novamente*/
          return $("#"+global_tipoRequisicao).submit();
        }
        /*Verifica se ocorreu erro ao capturar os dados*/
        if(dados.match(/Undefined index: Nattlogin/))
        {
          /*Verifica se a quantidade de tentativas é menor ou iguar a 5*/
          if(global_contador_tentativas > 4)
          {
            global_contador_tentativas = 0;
            return alert("Infelizmente não conseguimos obter o Dados. Por favor, tente mais tarde! Caso o problema persista, informe ao Administrador do Sistema!");
          }
          global_contador_tentativas++;
          /*return alert("Não foi possível obter os dados. Por favor, tente novamente! Se o problema persistir, informe ao Administrador do Sistema!");*/
          /*Se acontecer erro, tenta novamente*/
          return $("#"+global_tipoRequisicao).submit();
        }
        /*Verifica se ocorreu erro de Acesso Negado*/
        if(dados.match(/You don't have permission to access/))
        {
          /*Verifica se a quantidade de tentativas é menor ou iguar a 5*/
          if(global_contador_tentativas > 4)
          {
            return alert("Infelizmente não conseguimos obter o Dados. Por favor, tente mais tarde! Caso o problema persista, informe ao Administrador do Sistema!");
          }
          global_contador_tentativas++;
          /*return alert("Não foi possível obter os dados. Por favor, tente novamente! Se o problema persistir, informe ao Administrador do Sistema!");*/
          /*Se acontecer erro, tenta novamente*/
          return $("#"+global_tipoRequisicao).submit();
        }

        /*Zera o contador de Tentativas*/
        global_contador_tentativas = 0;

        /*Verifica se foi encontrado algo*/
        if(dados.match(/NOT FOUND/))
        {
          dom_tela_apresentacao.html(`
            <div class="alert alert-warning">
              <h3>Nenhum resultado foi encontrado!</h3>
            </div>
          `);
          return switchScreen(1);
        }

        /*Chama a Função responsável por apresentar os dados na Tela*/
        switch (global_tipoRequisicao)
        {
          case "ConsultaCPF":
            return ConsultaCPF(dados);
          break;
          case "ConsultaCNPJ":
            return ConsultaCNPJ(dados);
          break;
          case "ConsultaNome":
            return ConsultaNome(dados);
          break;
          case "ConsultaTelefone":
            return ConsultaTelefone(dados);
          break;
          case "ConsultaEndereco":
            return ConsultaEndereco(dados);
          break;
          case "ConsultaCEP":
            return ConsultaCEP(dados);
          break;
        }
      }

      function errorAjax()
      {
        end_loading();
        return alert("Um erro inesperado aconteceu! Por favor, tente novamente! Caso o problema persista, informe ao Administrador do Sistema.");
      }

    /*--------------------------------------------------------------------------------------------|
    |                                                                                             |
    |                                    FUNÇÕES AUXILIARES                                       |
    |                                                                                             |
    |                                                                                             |
    ---------------------------------------------------------------------------------------------*/

    function switchScreen(option)
    {
      /*Se 0, mostra a tela com a lista de resultados (tabela)*/
      if(option == 0)
      {
        /*Desabilita os botões de controle*/
        dom_btnVoltar.fadeOut(600);
        dom_btnImprimir.fadeOut(600);

        dom_tela_apresentacao.fadeOut(600);
        dom_tela_apresentacao_listaResultados.fadeIn(1200);
      }
      /*Se 1, mostra a tela com os detalhes de um resultado*/
      if(option == 1)
      {
        //Coloca logo do probusca
        try
        {
          let div = dom_tela_apresentacao.find(".article");
          div.prepend(`
            <img align="center" src="/assets/img/logo.png" alt="Logo do probusca" style="width: 162px;
            height: 120px; left: 50%; margin-left: -81px; position: relative;">`);
        }catch(err){
          console.log(err);
        }
        /*Desabilita botão de Mais Resultados*/
        dom_btnMaisResultados.fadeOut(1200);
        /*Habilita botão de imprimir*/
        dom_btnImprimir.fadeIn(1200);
        /*Se existir conteúdo na Tela com a Lista de Resultados, mostra o botãoptimize
        para retornar a esta DIV*/
        if(dom_tela_apresentacao_listaResultados.html() != "")
        {
          dom_btnVoltar.fadeIn(1000);
        }
        dom_tela_apresentacao_listaResultados.fadeOut(600);
        dom_tela_apresentacao.fadeIn(1200);
        /*Corrigi scroll, se necessário*/
        $("html, body").animate({
          scrollTop: dom_tela_apresentacao.position().top
        }, 1000).delay(1400);

      }
      /*Se -1, esconde ambas as telas e limpa elas*/
      if(option == -1)
      {
        dom_tela_apresentacao.fadeOut(600);
        dom_tela_apresentacao_listaResultados.fadeOut(600);
        dom_tela_apresentacao.empty();
        dom_tela_apresentacao_listaResultados.empty();
        dom_btnVoltar.fadeOut(600);
        dom_btnImprimir.fadeOut(600);
        dom_btnMaisResultados.fadeOut(600);
      }
    }

    function ConsultaCPF(dados)
    {
      /*Limpa a tela*/
      dom_tela_apresentacao.empty();
      /*Adiciona os Resulta na tela*/
      dom_tela_apresentacao.append(dados);
      /*Salva o resultado na variável global*/
      global_respostaConsulta = dados;
      /*Mostra a Tela para o Usuário*/
      switchScreen(1);
    }

    function ConsultaCNPJ(dados)
    {
      /*Limpa a tela*/
      dom_tela_apresentacao.empty();
      /*Adiciona os Resulta na tela*/
      global_respostaConsulta = dados;
      /*Salva o resultado na variável global*/
      dom_tela_apresentacao.append(dados);
      /*Mostra a Tela para o Usuário*/
      switchScreen(1);
    }

    function ConsultaNome(dados)
    {
      try
      {
        dados = JSON.parse(dados);
      }
      catch(err)
      {
        return alert("Um erro ocorreu! Tente novamente, caso o problema persista, informe ao Administrador do Sistema.");
      }

      if(dados.length >= 15)
      {
        dom_btnMaisResultados.fadeIn(600);
      }
      else
      {
        dom_btnMaisResultados.fadeOut(100)
      }

      /*Verifica qual o indice da página*/
      if(global_indice_page > 1)
      {
        /*Adiciona o resultado atual ao anterior*/
        let table_lines = "";
        $.each(dados, function(indice, item){
          global_respostaConsulta.push(item);
          table_lines += `
            <tr>
              <td>${item.id}</td>
              <td>${item.cpfCnpj}</td>
              <td>${item.nome}</td>
              <td>${item.cidade}</td>
              <td><button class="btn btn-default event_pesquisaCpf" data-cpf="${item.cpfCnpj}">Info.</button></td>
            </tr>
          `;
        });
        $("#tbl_listaResultados_body").append(table_lines);
      }
      else
      {
        /*Adiciona o Resultado atual a variável global*/
        global_respostaConsulta = dados;

        if(dados.length == 0)
        {
          dom_tela_apresentacao.append(`
            <div class="alert alert-warning">
              <h3>Nenhum dado foi encontrado!</h3>
            </div>
          `);
          return switchScreen(1);
        }

        let table_lines = "";
        $.each(dados, function(indice, item){
          table_lines += `
            <tr>
              <td>${item.id}</td>
              <td>${item.cpfCnpj}</td>
              <td>${item.nome}</td>
              <td>${item.cidade}</td>
              <td><button class="btn btn-default event_pesquisaCpf" data-cpf="${item.cpfCnpj}">Info.</button></td>
            </tr>
          `;
        });
        let table = `
          <table class="table table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>CPF/CNPJ</th>
                <th>NOME</th>
                <th colspan="2">CIDADE</th>
              </tr>
            </thead>
            <tbody id="tbl_listaResultados_body">
              ${table_lines}
            </tbody>
          </table>
        `;
        dom_tela_apresentacao_listaResultados.append(table);
      }

      switchScreen(0);
    }

    function ConsultaTelefone(dados)
    {
      /*Limpa a tela*/
      dom_tela_apresentacao.empty();
      /*Adiciona os Resulta na tela*/
      global_respostaConsulta = dados;
      /*Salva o resultado na variável global*/
      dom_tela_apresentacao.append(dados);
      /*Mostra a Tela para o Usuário*/
      switchScreen(1);
    }

    function ConsultaCEP(dados)
    {
      try
      {
        dados = JSON.parse(dados);
      }
      catch(err)
      {
        return alert("Um erro ocorreu! Tente novamente, caso o problema persista, informe ao Administrador do Sistema.");
      }

      /*Verifica se é para mostrar botão "Mais Resultados"*/
      if(dados.length >= 15)
      {
        dom_btnMaisResultados.fadeIn(600);
      }
      else
      {
        dom_btnMaisResultados.fadeOut(100)
      }

      /*Verifica qual o indice da página*/
      if(global_indice_page > 1)
      {
        /*Adiciona o resultado atual ao anterior*/
        let table_lines = "";
        $.each(dados, function(indice, item){
          global_respostaConsulta.push(item);
          table_lines += `
            <tr>
              <td>${item.cpfCnpj}</td>
              <td>${item.nome}</td>
              <td>${item.logradouro}</td>
              <td>${item.numero}</td>
              <td>${item.complemento}</td>
              <td>${item.bairro}</td>
              <td>${item.cep}</td>
              <td><button class="btn btn-default event_pesquisaCpf" data-cpf="${item.cpfCnpj}">Info.</button></td>
            </tr>
          `;
        });
        $("#tbl_listaResultados_body").append(table_lines);
      }
      else
      {
        /*Adiciona o Resultado atual a variável global*/
        global_respostaConsulta = dados;

        if(dados.length == 0)
        {
          dom_tela_apresentacao.append(`
            <div class="alert alert-warning">
              <h3>Nenhum dado foi encontrado!</h3>
            </div>
          `);
          return switchScreen(1);
        }

        let table_lines = "";
        $.each(dados, function(indice, item){
          table_lines += `
            <tr>
            <td>${item.cpfCnpj}</td>
            <td>${item.nome}</td>
            <td>${item.logradouro}</td>
            <td>${item.numero}</td>
            <td>${item.complemento}</td>
            <td>${item.bairro}</td>
            <td>${item.cep}</td>
              <td><button class="btn btn-default event_pesquisaCpf" data-cpf="${item.cpfCnpj}">Info.</button></td>
            </tr>
          `;
        });
        let table = `
          <table class="table table-hover">
            <thead>
              <tr>
                <th>CPF/CNPJ</th>
                <th>NOME</th>
                <th>LOGRADOURO</th>
                <th>NUMERO</th>
                <th>COMPLEMENTO</th>
                <th>BAIRRO</th>
                <th colspan="2">CEP</th>
              </tr>
            </thead>
            <tbody id="tbl_listaResultados_body">
              ${table_lines}
            </tbody>
          </table>
        `;
        dom_tela_apresentacao_listaResultados.append(table);
      }

      switchScreen(0);
    }


    /*--------------------------------------------------------------------------------------------|
    |                                                                                             |
    |                                 MONITORA EVENTOS DO DOM                                     |
    |                                                                                             |
    |                                                                                             |
    ---------------------------------------------------------------------------------------------*/

    /*Evento para buscar pessoa por CPF*/
    $(document).on("click", ".event_pesquisaCpf", function(){
      show_loading();
      /*Salava a posição do scroll*/
      global_posicaoScroll = $(this).position().top;
      let cpf = $(this).attr("data-cpf");
      /*Checa se é cpf ou cnpj */
      if(cpf.length > 14)
      {
        let campo_cnpj = dom_form_consultaCNPJ.find("input[name=cnpj]");
        campo_cnpj.val(cpf);
        dom_form_consultaCpf.submit();
      }
      else
      {
        let campo_cpf = dom_form_consultaCpf.find("input[name=cpf]");
        campo_cpf.val(cpf);
        dom_form_consultaCpf.submit();
      }
    });

    /*Evento responsável por obter mais resultados para uma dada Pesquisa*/
    dom_btnMaisResultados.on("click", function(){
      /*Faz um scroll para o final da Página*/
      $("html, body").animate({
        scrollTop: $(document).height()
      }, 1000);
      /*Incrementa o indice da página*/
      global_indice_page = parseInt(global_indice_page, "10") + 1;

      if(global_tipoRequisicao == "ConsultaNome")
      {
        let input_page = dom_form_consultaNome.find("input[name=page]");
        input_page.attr('value', global_indice_page);
        dom_form_consultaNome.submit();
      }

      if(global_tipoRequisicao == "ConsultaCEP")
      {
        let input_page = dom_form_consultaCEP.find("input[name=page]");
        input_page.attr('value', global_indice_page);
        dom_form_consultaCEP.submit();
      }

    });

    /*Evento mudança de opção de Pesquisa*/
    $(document).on("click", ".nav-tabs", function(){
      /*Reseta o indice de página para todos os input e variável global*/
      $("input[name=page]").val(1);
      global_indice_page = 1;
      /*Limpa e esconde as telas*/
      switchScreen(-1);
    });

    /*Obtém as Cidades para um dado Estado*/
    $(document).on("change", "#form_consultaEndereco_uf", function(){
      let uf = $(this).val();

      show_loading();
      let response = $.ajax({
        dataType: 'text',
        method: 'post',
        url: 'https://probusca.com/painel/page/request_nett.php',
        error: errorAjax,
        data : {tipoRequisicao:"getCidades", uf:uf}
      });
      response.done(function(d){
        console.log(d);
        d = JSON.parse(d);
        end_loading();
        $("#form_consultaEndereco_cidade").empty();
        $.each(d, function(indice, item){
          $("#form_consultaEndereco_cidade").append(`<option value="${item.id_cidade}">${item.descricao}</option>`);
        });
      });
    });

    /*Obtém as Ruas para uma dada Cidade*/
    $(document).on("keyup", "#form_consultaEndereco_term", function(){
      let uf = $("#form_consultaEndereco_uf").val();
      let cidade = $("#form_consultaEndereco_cidade").val();
      let term = $("#form_consultaEndereco_term").val();
      show_loading();
      let response = $.ajax({
        dataType: 'text',
        method: 'post',
        url: 'https://probusca.com/painel/page/request_nett.php',
        error: errorAjax,
        data : {tipoRequisicao:"getRuas", uf:uf, cidade:cidade, term:term}
      });
      response.done(function(d){
        end_loading();
        d = JSON.parse(d);
        $("#form_consultaEndereco_endereco").empty();
        $.each(d, function(indice, item){
          $("#form_consultaEndereco_endereco").append(`<option value="${item.id}">${item.label}</option>`);
        });
      });
    });

    /*Evento Voltar para a Tela de Resultados*/
    dom_btnVoltar.on("click", function(){
      switchScreen(0);
      /*coloca o scroll no valor antes do click no botao de Info*/
      $("html, body").animate({
        scrollTop: global_posicaoScroll
      }, 1000).delay(1400);
    });

    /*Monitora evento de impressão*/
    dom_btnImprimir.on("click", function(){
      let conteudo = document.getElementById('telaApresentacaoResultados').innerHTML;
      let tela_impressao = window.open('about:blank');
      tela_impressao.document.write(conteudo);
      tela_impressao.window.print();
      tela_impressao.window.close();
    });

    /*EVENTO: SUBMIT*/
    $(document).on("click", "input[type=submit]", function(){
      //Limpa tudo
      switchScreen(-1);
    });

    $(document).on("change", "input[name=radio_consultaCEP]", function(){
      if($(this).val() == 2)
      {
        $("#ConsultaCEP_byCEP").fadeIn(400);
        $("#ConsultaCEP_byAddress").fadeOut(200);
      }
      else
      {
        $("#ConsultaCEP_byCEP").fadeOut(200);
        $("#ConsultaCEP_byAddress").fadeIn(400);
      }

      $("#ConsultaCEP_byNumber").fadeIn(400);
    });

  };


</script>
