<?php

// classe de conexao ao banco de dados
include('class/LimitarConsulta.function.php');
include('class/Token.class.php');

// verificamos se o usuario ainda esta logado
if(!$_SESSION) @session_start();

// limita o numero de consultas
limitarConsulta(null, $_SESSION['usuario'], 'cs');

// geramos um novo token pro usuario
$token = new Token();
$token = $token->get_token();

?>

<!DOCTYPE html>

<style>
.apresentacaoResultados{
  background-color: white;
  border-radius: 15px;
  padding: 15px;
  margin-bottom: 20px;

  display: none;
}

figure legend{
  width: 150px;
  text-align: center;
}

.tableTitle{
  font-size: 25px;
  font-weight: bold;
  color: black;
}

.tableTitle2{
  font-size: 20px;
  font-weight: bold;
  color: #00BFFF;
}

#apr_logoProbusca{
  width: 162px;
  height: 120px;
  left: 50%;
  margin-left: -81px;
  position: relative;
}

#.aprList{
  display: none;
}

#apr_btn_voltar_listaResultados{
  display: none;
}

#apr_botoes{
  margin-bottom: 15px;
}

h3{
  margin: 20px 0 20px 0;
}
</style>


    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

      <div class="row">
  			<ol class="breadcrumb">
  				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
  				<li class="active">CATRA</li>
  			</ol>
  		</div><!--/.row-->

  		<div class="row">
  			<div class="col-lg-12">
  				<h1 class="page-header">Pesquisa de Informações <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
  			</div>
  		</div><!--/.row-->

      <!--OPÇÕES DE PESQUISA-->
      <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item active" role="presentation">
          <a data-toggle="tab" href="#cpfCnpj" role="tab" aria-controls="cpfCnpj">CPF/CNPJ</a>
        </li>
        <li class="nav-item">
          <a data-toggle="tab" href="#nome" role="tab" aria-controls="nome" >Nome</a>
        </li>
        <li class="nav-item">
          <a data-toggle="tab" href="#telefone" role="tab" aria-controls="telefone">Telefone</a>
        </li>
        <li class="nav-item">
          <a data-toggle="tab" href="#mae" role="tab" aria-controls="mae">Mãe</a>
        </li>
        <li class="nav-item">
          <a data-toggle="tab" href="#nascimento" role="tab" aria-controls="nascimento" >Nascimento</a>
        </li>
        <li class="nav-item">
          <a data-toggle="tab" href="#socio" role="tab" aria-controls="socio">Sócio</a>
        </li>
        <li class="nav-item">
          <a data-toggle="tab" href="#atividadeEconomica" role="tab" aria-controls="atividadeEconomica">Ativ. Econ.</a>
        </li>
        <li class="nav-item">
          <a data-toggle="tab" href="#endereco" role="tab" aria-controls="endereco">Endereço</a>
        </li>
      </ul>
      <div class="tab-content">
        <!--CPF/CNPJ-->
        <div class="tab-pane active" id="cpfCnpj" role="tabpanel">
            <form action="" method="post">
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>CPF ou CNPJ</label>
                  <input type="text" class="form-control" name="doc">
                  <input type="hidden" name="token" value="<?php echo $token; ?>">
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                  <input type="hidden" name="tipoRequisicao" value="ConsultaCpfCnpj">
                  <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
                </div>
              </div>
            </form>
        </div>
        <!--NOME-->
        <div class="tab-pane" id="nome" role="tabpanel">
          <form action="" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Tipo de Pessoa*</label>
                <select class="form-control" name="tipoPessoas">
                  <option value="todas">Todas</option>
                  <option value="fisica">Físicas</option>
                  <option value="juridica">Jurídicas</option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label>Nome*</label>
                <input type="text" class="form-control" name="nome">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Estado*</label>
                <select class="form-control selectEstados" name="uf"></select>
              </div>
              <div class="form-group col-md-6">
                <label>Cidade</label>
                <select class="form-control selectCidades flexselect" name="cidade"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Bairro</label>
                <input type="text" class="form-control" name="bairro">
              </div>
              <div class="form-group col-md-6">
                <label>Endereço</label>
                <input type="text" class="form-control" name="endereco">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="hidden" name="tipoRequisicao" value="ConsultaNome">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
              </div>
            </div>
          </form>
        </div>
        <!--TELEFONE-->
        <div class="tab-pane" id="telefone" role="tabpanel">
          <form action="" method="post">
            <div class="form-row">
                <div class="form-group col-md-2">
                  <label>DDD*</label>
                  <input type="number" class="form-control" name="telefone_ddd" placeholder="XX" maxlength="2">
                </div>
                <div class="form-group col-md-10">
                  <label>Número*</label>
                  <input type="number" class="form-control" name="telefone_numero" placeholder="" maxlength="10">
                </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Estado*</label>
                <select type="text" class="form-control selectEstados" name="uf"></select>
              </div>
              <div class="form-group col-md-6">
                <label>Cidade</label>
                <select type="text" class="form-control selectCidades flexselect" name="cidade"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
                <input type="hidden" name="tipoRequisicao" value="ConsultaTelefone">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
              </div>
            </div>
          </form>
        </div>
        <!--MAE-->
        <div class="tab-pane" id="mae" role="tabpanel">
          <form action="" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nome da Mãe*</label>
                <input type="text" class="form-control" name="nome">
              </div>
              <div class="form-group col-md-6">
                <label>Estado*</label>
                <select class="form-control selectEstados" name="uf"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Cidade</label>
                <select class="form-control selectCidades flexselect" name="cidade"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="hidden" name="tipoRequisicao" value="ConsultaMae">
                <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
              </div>
              <div class="form-group col-md-6">
              </div>
            </div>
          </form>
        </div>
        <!--NASCIMENTO-->
        <div class="tab-pane" id="nascimento" role="tabpanel">
          <form action="" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Data de Nascimento</label>
                <input type="text" class="form-control date" name="dataNascimento">
              </div>
              <div class="form-group col-md-6">
                <label>Estado</label>
                <select class="form-control selectEstados" name="uf"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Cidade</label>
                <select name="cidade" class="form-control selectCidades flexselect"></select>
              </div>
              <div class="form-group col-md-6">
                <label>Bairro</label>
                <input type="text" class="form-control" name="bairro">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Endereço</label>
                <input type="text" class="form-control" name="endereco">
              </div>
              <div class="form-group col-md-6">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="hidden" name="tipoRequisicao" value="ConsultaNascimento">
                <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
              </div>
            </div>
          </form>
        </div>
        <!--SÓCIO-->
        <div class="tab-pane" id="socio" role="tabpanel">
          <form action="" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Nome</label>
                <input type="text" class="form-control" name="nome">
              </div>
              <div class="form-group col-md-6">
                <label>Estado</label>
                <select class="form-control selectEstados" name="uf"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Cidade</label>
                <select name="cidade" class="form-control selectCidades flexselect">
                </select>
              </div>
              <div class="form-group col-md-6">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="hidden" name="tipoRequisicao" value="ConsultaSocio" >
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
              </div>
            </div>
          </form>
        </div>
        <!--ATIVIDADE ECONÔMICA-->
        <div class="tab-pane" id="atividadeEconomica" role="tabpanel">
          <form action="" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Atividade Econômica*</label>
                <input type="text" class="form-control" name="atividade" placeholder="ex: serviço de transporte">
              </div>
              <div class="form-group col-md-6">
                <label>Estado*</label>
                <select class="form-control selectEstados" name="uf"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Cidade</label>
                <select name="cidade" class="form-control selectCidades flexselect"></select>
              </div>
              <div class="form-group col-md-6">
                <label>Bairro</label>
                <input type="text" class="form-control" name="bairro">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Endereço</label>
                <input type="text" class="form-control" name="endereco">
              </div>
              <div class="form-group col-md-6">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="hidden" name="tipoRequisicao" value="ConsultaAtividadeEcon">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" class="btn btn-outline-primary" value="Pesquisar">
              </div>
            </div>
          </form>
        </div>
        <!--ENDEREÇO-->
        <div class="tab-pane" id="endereco" role="tabpanel">
          <form action="" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Estado*</label>
                <select type="text" class="form-control selectEstados" name="uf"></select>
              </div>
              <div class="form-group col-md-6">
                <label>Cidade</label>
                <select class="form-control selectCidades flexselect" name="cidade"></select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Complemento</label>
                <input type="text" class="form-control" name="complemento">
              </div>
              <div class="form-group col-md-6">
                <label>CEP</label>
                <input type="text" class="form-control cep" name="cep">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Bairro</label>
                <input type="text" class="form-control" name="bairro">
              </div>
              <div class="form-group col-md-6">
                <label>Número</label>
                <input type="text" class="form-control" name="numero">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <input type="hidden" name="tipoRequisicao" value="ConsultaEndereco">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
                <input type="submit" value="Pesquisar" class="btn btn-outline-primary" value="Pesquisar">
              </div>
            </div>
          </form>
        </div>


      </div>

  </div><!--endContainer-->

  <!--TELAS DE APRESENTAÇÃO-->
  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <!--DIV com o resultado detalhado-->
    <div id="apr" class="apresentacaoResultados">
      <!--Logo Probusca-->
      <img class="logo-pro" id="apr_logoProbusca" src="/assets/img/logo.png" alt="Logo do probusca">
      <!--Opções-->
      <div class="row" id="apr_botoes">
        <div class="col-md-6">
          <button id="apr_btn_voltar_listaResultados" class="btn"><span class="glyphicon glyphicon-arrow-left"></span>Voltar</button>
          <button id="apr_btn_imprimir" class="btn"><span class="glyphicon glyphicon-print"></span>Imprimir</button>
        </div>
      </div>
        <!--IDENTIFICAÇÃO PESSOA FÍSICA-->
        <div class="row" id="apr_identificacaoPessoa">
          <div class="col-md-2" style="min-width:150px;">
            <figure>
              <img src="/painel/img/silhueta_masc.jpg">
              <legend id="apr_codigo"></legend>
            </figure>
          </div>
          <div class="col-md-10">
            <table class="table">
              <tr>
                <td colspan="2" id="apr_nome" class="tableTitle"></td>
              <tr>
                <tr>
                  <td><b>CPF:</b></td><td id="apr_cpf"></td>
                <tr>
                <tr>
                  <td><b>Nascimento:</b></td><td id="apr_nascimento"></td>
                <tr>
                <tr>
                  <td><b>Mãe:</b></td><td id="apr_mae"></td>
                <tr>
            </table>
          </div>
        </div>
        <!--IDENTIFICAÇÃO PESSO JURÍCA--->
        <div class="row" id="apr_identificacaoEmpresa" style="display:none;">
          <div class="col-md-12">
            <table class="table">
              <tbody id="apr_identificacaoEmpresa_tabela"></tbody>
            </table>
          </div>
        </div>
        <!--IDENTIFICAÇÃO SÓCIOS DA PESSOA JURÍDICA-->
        <div class="row" id="apr_identificacaoSocios" style="display:none;">
          <div class="col-md-12">
            <table class="table">
              <tr>
                <td colspan="8" class="tableTitle2">Sócios</td>
              </tr>
              <tbody id="apr_identificacaoSocios_tabela"></tbody>
            </table>
          </div>
        </div>
          <!--TELEFONES ATUALIZADOS-->
          <div class="row">
            <table class="table">
              <thead>
                <tr>
                  <td colspan="4" class="tableTitle2">Telefones Atualizados</td>
                </tr>
              </thead>
              <tbody id="apr_telefonesAtualizados">
              </tbody>
            </table>
          </div>
          <!--TELEFONES OBSOLETOS-->
          <div class="row">
            <table class="table">
              <thead>
                <tr>
                  <td colspan="4" class="tableTitle2">Telefones Obsoletos</td>
                </tr>
              </thead>
                <tbody id="apr_telefonesAbsoletos">
                </tbody>
            </table>
          </div>
          <!--ENDEREÇOS ATUALIZADOS-->
          <div class="row">
            <table class="table">
              <thead>
                <tr>
                  <td colspan="10" class="tableTitle2">Endereços Atualizados</td>
                </tr>
              </thead>
              <tr>
                <tbody id="apr_enderecosAtualizados">
                </tbody>
              </tr>
            </table>
          </div>
          <!--ENDEREÇOS OBSOLETOS-->
          <div class="row">
            <table class="table">
              <tr>
                <td colspan="10" id="telaResultados_nome" class="tableTitle2">Endereços Obsoletos</td>
              </tr>
              <tbody id="apr_enderecosAbsoletos">
              </tbody>
            </table>
          </div>
      </div><!--endBlocoApresentacao-->
    <!--Esta DIV é utilizada para mostrat uma lista de resultados-->
    <div id="aprList"></div>
    <!--Utilizada para armazenar a mensagem que informa que nada foi encontrado-->
    <div class="alert alert-warning" style="display: none;" id="apr_mensagem">
      <h3><span class="glyphicon glyphicon-warning-sign"></span> Nenhum resultado válido foi encontrado!</h3>
    </div>
  </div>



<script>

  window.onload = function() {

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                                  PONTEIROS PARA ELEMENTOS DO DOM                            |
|                                        E VARIÁVEIS GLOBAIS                                  |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

    var tipoRequisicao = '';
    var respostaConsulta;
    var formUsuario;
    var scrollTela;
    /*Obtém as referências no DOM*/
    /*Campos Fixos*/
    var pessoa_codigo = $("apr_codigo");
    var pessoa_nome = $("#apr_nome");
    var pessoa_cpf = $("#apr_cpf");
    var pessoa_nascimento = $("#apr_nascimento");
    var pessoa_mae = $("#apr_mae");
    /*Tabelas Dinâmicas*/
    var tabela_telefonesAtualizados = $("#apr_telefonesAtualizados");
    var tabela_telefonesAbsoletos = $("#apr_telefonesAbsoletos");
    var tabela_enderecosAtualizados = $("#apr_enderecosAtualizados");
    var tabela_enderecosAbsoletos = $("#apr_enderecosAbsoletos");
    /*Botões*/
    var dom_btnVoltar = $("#apr_btn_voltar_listaResultados");
    var dom_btnImprimir = $("#apr_btn_imprimir");

    /*Outros*/
    var telaApresentacao = $("#apr");
    var telaListaResultados = $("#aprList");
    var img_logoProbusca = $("#apr_logoProbusca");
    var msg_nadaEncontrado = $("#apr_mensagem");

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                                  CARREGAMENTO DE PLUGINS, ETC                               |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

    /*Inica máscara de dados*/
    $('.cep').mask('00000-000');
    $('.date').mask('00/00/0000');

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

    //$.getJSON('https://probusca.com/painel/js/cidades.json', function(data){console.log(data);});
    var todasCidadesBrasileiras = {};
    $.getJSON('https://probusca.com/painel/js/cidades.json', function(data) {
      cidadesBrasileiras = data;
    });

  /*Carrega os campos com os Estados Brasileiros*/
  $.each(estadosBrasileiros.UF, function(key, value){
    $('.selectEstados').append('<option value="'+value.sigla+'">'+value.nome+'</option>');
  });
  $('.selectEstados').change(function(){
    let estado = $(this).val().toLowerCase();
    $(".selectCidades option").remove();
    for(let cidadeId in cidadesBrasileiras[estado]) {
      //console.log(cidadesBrasileiras[cidadeId]);
      $(".selectCidades").append('<option value="'+cidadeId+'">'+cidadesBrasileiras[estado][cidadeId]+'</option>');
    }
    $("select.flexselect").flexselect();
  });
  /*Carrega os campos com as Cidades Brasileiras*/
  //$(".selectCidades").append(cidadesBrasileiras);
  /*Criar o efeito nos campos Cidades*/
  $("select.flexselect").flexselect();

  var options = {
    dataType: 'text',
    url: 'https://probusca.com/painel/page/request_catta.php',
    beforeSubmit: validate,
    success: showResponse
  };

  $('form').ajaxForm(options);

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                                      FUNÇÕES AUXILIARES                                     |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

/*Função deixa a primeira letra de cada palavra em maiúsculo*/
function deixaMaiusculo(text)
{
    if(text === 'undefined')
    {
      return "-";
    }
    try
    {
    var words = text.toLowerCase().split(" ");
    for (var a = 0; a < words.length; a++) {
        var w = words[a];
        words[a] = w[0].toUpperCase() + w.slice(1);
    }
    return words.join(" ");
    }catch(err)
    {
      return text;
    }
  }

  /*Limpa todos os campos da tela de apresentação*/
  function limpaTudo(limpaTelaListaResultados)
  {
    pessoa_codigo.empty();
    pessoa_nome.empty();
    pessoa_cpf.empty();
    pessoa_nascimento.empty();
    pessoa_mae.empty();
    /*Tabelas Dinâmicas*/
    tabela_telefonesAtualizados.empty();
    tabela_telefonesAbsoletos.empty();
    tabela_enderecosAtualizados.empty();
    tabela_enderecosAbsoletos.empty();
    /*Outros*/
    $("#apr_identificacaoEmpresa_tabela").empty();
    $("#apr_identificacaoSocios_tabela").empty();

    telaListaResultados.empty();
  }


/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                                      ANTES DO SUBMIT                                        |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

function validate(formData)
{
      /*Desabilita as telas envolvidas na pesquisa*/
      $("#apr_identificacaoPessoa").show();
      $("#apr_identificacaoEmpresa").hide();
      $("#apr_identificacaoSocios").hide();
      telaListaResultados.fadeOut(100);
      msg_nadaEncontrado.fadeOut(100);
      telaApresentacao.fadeOut(100);
      /*Esconde botão voltar*/
      dom_btnVoltar.fadeOut(100);

      limpaTudo();

      /*Salva o Formulário de pesquisa enviado pelo usuário*/
      formUsuario = formData;

      /*Verifica qual o tipo de consulta*/
      $.each(formData, function(indice, obj){
        if(obj['name'] == 'tipoRequisicao')
        {
          tipoRequisicao = obj['value'];
        }
      })
      /*Chama as funções de validação*/
      let respValidacao = true;
      switch(tipoRequisicao)
      {
        case 'ConsultaCpfCnpj':
          respValidacao = validacao_consultaCpfCnpj(formData);
        break;
        case 'ConsultaNome':
          respValidacao = validacao_consultaNome(formData);
        break;
        case 'ConsultaTelefone':
          respValidacao = validacao_consultaTelefone(formData);
        break;
        case 'ConsultaMae':
          respValidacao = validacao_consultaMae(formData);
        break;
        case 'ConsultaNascimento':
          respValidacao = validacao_consultaNascimento(formData);
        break;
        case 'ConsultaSocio':
          respValidacao = validacao_consultaSocio(formData);
        break;
        case 'ConsultaAtividadeEcon':
          respValidacao = validacao_consultaAtividadeEconomica(formData);
        break;
        case 'ConsultaEndereco':
          respValidacao = validacao_consultaEndereco(formData);
        break;
      }
      if(respValidacao)
      {
        /*Mostra a tela de carregamento*/
        show_loading();
        return true;
      }
      else
      {
        return false;
      }
    }

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                                 FUNÇÕES DE VALIDAÇÃO                                        |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

  function validacao_consultaCpfCnpj(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'doc' && object['value'].length < 11)
        {
          alert('CPF/CNPJ inválido!!')
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

  function validacao_consultaNome(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'nome' && object['value'].length == 0)
        {
          alert('Preencha o campo NOME!')
          isValid = false;
          return false;
        }
        if(object['name'] == 'uf' && object['value'].length == 0)
        {
          alert('Preencha o campo ESTADO!');
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

  function validacao_consultaTelefone(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'uf' && object['value'].length == 0)
        {
          alert('Preencha o campo ESTADO!')
          isValid = false;
          return false;
        }
        if(object['name'] == 'telefone' && object['value'].length == 0)
        {
          alert('Preencha o campo NÚMERO!');
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

  function validacao_consultaMae(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'uf' && object['value'].length == 0)
        {
          alert('Preencha o campo ESTADO!')
          isValid = false;
          return false;
        }
        if(object['name'] == 'nome' && object['value'].length == 0)
        {
          alert('Preencha o campo NOME DA MÃE!');
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

  function validacao_consultaNascimento(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'uf' && object['value'].length == 0)
        {
          alert('Preencha o campo ESTADO!')
          isValid = false;
          return false;
        }
        if(object['name'] == 'dataNascimento' && object['value'].length == 0)
        {
          alert('Preencha o campo DATA DE NASCIMENTO!');
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

  function validacao_consultaSocio(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'nome' && object['value'].length == 0)
        {
          alert('Preencha o campo NOME!');
          isValid = false;
          return false;
        }
        if(object['name'] == 'uf' && object['value'].length == 0)
        {
          alert('Preencha o campo ESTADO!');
          isValid = false;
          return false;
        }
        if(object['name'] == 'cidade' && object['value'].length == 0)
        {
          alert('Preencha o campo CIDADE!');
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

  function validacao_consultaAtividadeEconomica(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'uf' && object['value'].length == 0)
        {
          alert('Preencha o campo ESTADO!');
          isValid = false;
          return false;
        }
        if(object['name'] == 'atividade' && object['value'].length == 0)
        {
          alert('Preencha o campo ATIVIDADE ECONÔMICA!');
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

  function validacao_consultaEndereco(dados)
  {
      let isValid = true;
      $.each(dados, function(indice, object){
        if(object['name'] == 'uf' && object['value'].length == 0)
        {
          alert('Preencha o campo ESTADO!');
          isValid = false;
          return false;
        }
        if(object['name'] == 'cep' && object['value'].length == 0)
        {
          alert('Preencha o campo CEP!');
          isValid = false;
          return false;
        }
      });
      return isValid;
    }

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                         RESPOSTA DA REQUISIÇÃO AJAX                                         |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

function showResponse(resp) {

  console.log(resp);
  end_loading();
  try {
    respostaConsulta = JSON.parse(resp);
  } catch(err) {
    return alert("Um erro ocorreu! Por favor, recarregue a página. Se o problema persistir, favor informar ao administrados do Sistema.");
  }
  /*Primeiramente, checa se não aconteceu erro de token ou de código*/
  if(typeof respostaConsulta['erros'] !== 'undefined' && respostaConsulta['erros'] == true)
    return alert(respostaConsulta['message']);
  /*Verifica se o resultado está vazio*/
  try {
    if(jQuery.isEmptyObject(respostaConsulta['retorno']['resultado'])) {
      msg_nadaEncontrado.fadeIn(1000);
      return false;
    }
  } catch(err) {
    try {
      if(jQuery.isEmptyObject(respostaConsulta['resultados'])) {
        msg_nadaEncontrado.fadeIn(1000);
        return false;
      }
    } catch(err2)
    {
      console.log(err2);
    }
  }

  /*Chama as funções responsáveis por apresentar os dados*/

  if(tipoRequisicao == 'ConsultaCpfCnpj')
  {
    carregaDadosPessoa(respostaConsulta['retorno']);
  }
  if(tipoRequisicao == 'ConsultaNome')
  {
    geraTabela_consultaNome();
  }
  if(tipoRequisicao == 'ConsultaTelefone')
  {
    geraTabela_consultaTelefone();
  }
  if(tipoRequisicao == 'ConsultaMae')
  {
    geraTabela_consultaMae();
  }
  if(tipoRequisicao == 'ConsultaNascimento')
  {
    geraTabela_consultaNascimento();
  }
  if(tipoRequisicao == 'ConsultaSocio')
  {
    geraTabela_consultaSocio();
  }
  if(tipoRequisicao == 'ConsultaAtividadeEcon')
  {
    geraTabela_consultaAtividadeEcon();
  }
  if(tipoRequisicao == 'ConsultaEndereco')
  {
    geraTabela_consultaEndereco();
  }
}

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                          TRATAMENTO E APRESENTAÇÃO DOS DADOS                                |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

function carregaDadosPessoa(pessoa, type)
{
    let dados_telefonesAtualizados = [];
    let dados_telefonesAbsoletos = [];
    let dados_enderecosAtualizados = [];
    let dados_enderecosAbsoletos = [];

    /*Formato de Dados 1*/
    if(typeof pessoa['resultado'] !== 'undefined')
    {
      pessoa_nome.text(deixaMaiusculo(pessoa['resultado']['nome']));
      pessoa_codigo.text(pessoa['resultado']['codigo']);
      pessoa_cpf.text(pessoa['resultado']['cpfCnpj']);
      pessoa_nascimento.text(pessoa['resultado']['nascimento']+ ' ('+pessoa['resultado']['idade']+' anos)');
      pessoa_mae.text(deixaMaiusculo(pessoa['resultado']['mae']));

      $.each(pessoa, function(indice, obj){
        /*Obtém os telefones*/
        if(indice != 'resultado' && typeof obj['quantidadeTelefones'] != 'undefined' && obj['quantidadeTelefones'] > 0)
        {
          $.each(obj['telefones'], function(id, ob){
            if(ob['obsoleto'] == true)
            {
              dados_telefonesAbsoletos.push({telefone:'('+ob['ddd']+') '+ob['telefone'], operadora:ob['operadora']});
            }
            else
            {
              dados_telefonesAtualizados.push({telefone:'('+ob['ddd']+') '+ob['telefone'], operadora:ob['operadora']});
            }
          });
        }
        /*Obtém os endereços*/
        if(indice != 'resultado' && typeof obj['quantidadeEnderecos'] != 'undefined' && obj['quantidadeEnderecos'] > 0)
        {
          $.each(obj['enderecos'], function(id, ob){
            if(ob['obsoleto'] == true)
            {
              dados_enderecosAbsoletos.push({endereco:deixaMaiusculo(ob['endereco']), cep:ob['cep'], cidade:ob['cidade']+'-'+ob['uf'], bairro:deixaMaiusculo(ob['bairro']), numero:ob['numero']});
            }
            else
            {
              dados_enderecosAtualizados.push({endereco:deixaMaiusculo(ob['endereco']), cep:ob['cep'], cidade:ob['cidade']+'-'+ob['uf'], bairro:deixaMaiusculo(ob['bairro']), numero:ob['numero']});
            }
          });
        }
      });
    }
    //console.log(pessoa['resultado']['socios']);
    if(pessoa['resultado'] && pessoa['resultado']['socios'] && pessoa['resultado']['socios'].length > 0) {
      $.each(pessoa['resultado']['socios'], function(indice, item){
        $("#apr_identificacaoSocios_tabela").append(`<tr>
          <td colspan="2" id="apr_nome" class="tableTitle"></td>
        <tr>
          <tr>
            <td><b>CPF:</b> ${item['cpfCnpj']}</td>
            <td><b>Nome:</b> ${deixaMaiusculo(item['nome'])}</td>
            <td><b>Participação:</b> ${item['participacao']}%</td>
          <tr>`);
        $('#apr_identificacaoSocios').show();
      });
    }

    /*Formato de Dados 2*/
    if(typeof pessoa['telefonesEnderecos'] !== 'undefined')
    {

      /*Verifica se é pessoa física ou jurídica*/
      if(typeof type !== 'undefined' && type == 'ATV_ECON')
      {

        $("#apr_identificacaoPessoa").hide();
        $("#apr_identificacaoEmpresa").show();
        $("#apr_identificacaoSocios").show();

        /*Limpa as tabelas*/
        $("#apr_identificacaoEmpresa_tabela").empty();
        $("#apr_identificacaoSocios_tabela").empty();
        /*Adiciona o conteúdo*/
        $("#apr_identificacaoEmpresa_tabela").append(`<tr>
          <td colspan="2" class="tableTitle">${deixaMaiusculo(pessoa['nome'])}</td>
        <tr>
          <tr>
            <td><b>CNPJ:</b> ${pessoa['cpfCnpj']}</td>
          <tr>
          <tr>
            <td><b>Atividade:</b> ${deixaMaiusculo(pessoa['atividade'])}</td>
          <tr>`);

      
      }
      else
      {
        pessoa_nome.text(deixaMaiusculo(pessoa['nome']));
        pessoa_codigo.text(pessoa['codigo']);
        pessoa_cpf.text(pessoa['cpfCnpj']);
        pessoa_nascimento.text(pessoa['nascimento']+' ('+pessoa['idade']+' anos)');
        pessoa_mae.text(deixaMaiusculo(pessoa['mae']));
      }


      $.each(pessoa['telefonesEnderecos'], function(indice, ob){
        if(ob['obsoleto'] == true)
        {
          dados_telefonesAbsoletos.push({telefone:'('+ob['ddd']+') '+ob['telefone'], operadora:ob['operadora']});
          dados_enderecosAbsoletos.push({endereco:deixaMaiusculo(ob['endereco']), cep:ob['cep'], cidade:ob['cidade']+'-'+ob['uf'], bairro:deixaMaiusculo(ob['bairro']), numero:ob['numero']});
        }
        else
        {
          dados_telefonesAtualizados.push({telefone:'('+ob['ddd']+') '+ob['telefone'], operadora:ob['operadora']});
          dados_enderecosAtualizados.push({endereco:deixaMaiusculo(ob['endereco']), cep:ob['cep'], cidade:ob['cidade']+'-'+ob['uf'], bairro:deixaMaiusculo(ob['bairro']), numero:ob['numero']});
        }
      });
    }

    /*Carrega os Dados na Tela*/
    $.each(dados_telefonesAtualizados, function(indice, item){
      tabela_telefonesAtualizados.append(`
        <tr>
          <td><b>Telefone:</b> ${item.telefone}</td>
          <td><b>Operadora:</b> ${item.operadora}</td>
        </tr>`);
    });
    $.each(dados_telefonesAbsoletos, function(indice, item){
      tabela_telefonesAbsoletos.append(`
        <tr>
          <td><b>Telefone:</b> ${item.telefone}</td>
          <td><b>Operadora:</b> ${item.operadora}</td>
        </tr>`);
    });

    $.each(dados_enderecosAtualizados, function(indice, item){
      tabela_enderecosAtualizados.append(`
        <tr>
          <td><b>CEP:</b> ${item.cep}</td>
          <td><b>Cidade:</b> ${item.cidade}</td>
          <td><b>Bairro:</b> ${item.bairro}</td>
          <td><b>Endereço:</b> ${item.endereco}</td>
          <td><b>Nº:</b> ${item.numero}</td>
        </tr>`);
    });
    $.each(dados_enderecosAbsoletos, function(indice, item){
      tabela_enderecosAbsoletos.append(`
        <tr>
          <td><b>CEP:</b> ${item.cep}</td>
          <td><b>Cidade:</b> ${item.cidade}</td>
          <td><b>Bairro:</b> ${item.bairro}</td>
          <td><b>Endereço:</b> ${item.endereco}</td>
          <td><b>Nº:</b> ${item.numero}</td>
        </tr>`);
    });

    telaApresentacao.fadeIn(1000);
  }


/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                 FUNÇÕES GERADORAS DE TABELAS DE RESULTADOS                                  |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

function geraTabela_consultaNome(append, novosDados)
{
  /*Adiciona o conteúdo, ao invés de setar*/
  if(append !== 'undefined' && append == true)
  {
    $.each(novosDados['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Adiciona os novos resultados a variável global*/
    $.each(novosDados['resultados'], function(indice, item){
        respostaConsulta['resultados'].push(item);
    });
  }
  else
  {
    telaListaResultados.append('<table class="table"><thead><tr><th>Código</th><th>Nome</th><th>CPF</th><th></th></tr></thead><tbody id="tbl_listaDeResultados"></tbody></table>');

    $.each(respostaConsulta['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Se maior que 20 resultados, mostra botão mais resultados*/
    if(respostaConsulta['resultados'].length == 20)
    {
      telaListaResultados.append(`
          <div class="row">
            <button id="btn_deslocamento" class="btn btn-primary" style="margin-bottom: 60px; weight:10%; left: 45%;"><span class="glyphicon glyphicon-plus"></span> Resultados</button>
          </div>
        `);
    }
  }
  /*Mostra a tabela*/
  telaListaResultados.fadeIn(1000);
}

function geraTabela_consultaTelefone(append, novosDados)
{
  /*Adiciona o conteúdo, ao invés de setar*/
  if(append !== 'undefined' && append == true)
  {
    $.each(novosDados['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Adiciona os novos resultados a variável global*/
    $.each(novosDados['resultados'], function(indice, item){
        respostaConsulta['resultados'].push(item);
    });
  }
  else
  {
    telaListaResultados.append('<table class="table"><thead><tr><th>Código</th><th>Nome</th><th>CPF</th><th></th></tr></thead><tbody id="tbl_listaDeResultados"></tbody></table>');

    $.each(respostaConsulta['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Se maior que 20 resultados, mostra botão mais resultados*/
    if(respostaConsulta['resultados'].length == 20)
    {
      telaListaResultados.append(`
          <div class="row">
            <button id="btn_deslocamento" class="btn btn-primary" style="margin-bottom: 60px; weight:10%; left: 45%;"><span class="glyphicon glyphicon-plus"></span> Resultados</button>
          </div>
        `);
    }
  }
  /*Mostra a tabela*/
  telaListaResultados.fadeIn(1000);
}

function geraTabela_consultaMae(append, novosDados)
{
  /*Adiciona o conteúdo, ao invés de setar*/
  if(append !== 'undefined' && append == true)
  {
    $.each(novosDados['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Adiciona os novos resultados a variável global*/
    $.each(novosDados['resultados'], function(indice, item){
        respostaConsulta['resultados'].push(item);
    });
  }
  else
  {
    telaListaResultados.append('<table class="table"><thead><tr><th>Código</th><th>Nome</th><th>CPF</th><th></th></tr></thead><tbody id="tbl_listaDeResultados"></tbody></table>');

    $.each(respostaConsulta['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Se maior que 20 resultados, mostra botão mais resultados*/
    if(respostaConsulta['resultados'].length == 20)
    {
      telaListaResultados.append(`
          <div class="row">
            <button id="btn_deslocamento" class="btn btn-primary" style="margin-bottom: 60px; weight:10%; left: 45%;"><span class="glyphicon glyphicon-plus"></span> Resultados</button>
          </div>
        `);
    }
  }
  /*Mostra a tabela*/
  telaListaResultados.fadeIn(1000);
}

function geraTabela_consultaNascimento(append, novosDados)
{
  /*Adiciona o conteúdo, ao invés de setar*/
  if(append !== 'undefined' && append == true)
  {
    $.each(novosDados['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Adiciona os novos resultados a variável global*/
    $.each(novosDados['resultados'], function(indice, item){
        respostaConsulta['resultados'].push(item);
    });
  }
  else
  {
    telaListaResultados.append('<table class="table"><thead><tr><th>Código</th><th>Nome</th><th>CPF</th><th></th></tr></thead><tbody id="tbl_listaDeResultados"></tbody></table>');

    $.each(respostaConsulta['resultados'],function(indice, obj){
      $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoes" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Se maior que 20 resultados, mostra botão mais resultados*/
    if(respostaConsulta['resultados'].length == 20)
    {
      telaListaResultados.append(`
          <div class="row">
            <button id="btn_deslocamento" class="btn btn-primary" style="margin-bottom: 60px; weight:10%; left: 45%;"><span class="glyphicon glyphicon-plus"></span> Resultados</button>
          </div>
        `);
    }
  }
  /*Mostra a tabela*/
  telaListaResultados.fadeIn(1000);
}

function geraTabela_consultaSocio(append, novosDados)
{
  /*Adiciona o conteúdo, ao invés de setar*/
  if(append !== 'undefined' && append == true)
  {
    $.each(novosDados['resultados'],function(indice, obj){
      $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoesSocios" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Adiciona os novos resultados a variável global*/
    $.each(novosDados['resultados'], function(indice, item){
        respostaConsulta['resultados'].push(item);
    });
  }
  else
  {
    telaListaResultados.append('<table class="table"><thead><tr><th>Código</th><th>Nome</th><th>CPF</th><th></th></tr></thead><tbody id="tbl_listaDeResultados"></tbody></table>');

    $.each(respostaConsulta['resultados'],function(indice, obj){
      $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td>'+obj['cpfCnpj']+'</td><td><button class="btn btn_maisInformacoesSocios" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
    });
    /*Se maior que 20 resultados, mostra botão mais resultados*/
    if(respostaConsulta['resultados'].length == 20)
    {
      telaListaResultados.append(`
          <div class="row">
            <button id="btn_deslocamento" class="btn btn-primary" style="margin-bottom: 60px; weight:10%; left: 45%;"><span class="glyphicon glyphicon-plus"></span> Resultados</button>
          </div>
        `);
    }
  }
  /*Mostra a tabela*/
  telaListaResultados.fadeIn(1000);
}

function geraTabela_consultaAtividadeEcon(append, novosDados)
{
    /*Adiciona o conteúdo, ao invés de setar*/
    if(append !== 'undefined' && append == true)
    {
      $.each(novosDados['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['atividade'])+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td><button class="btn btn_maisInformacoesAtividadeEcon" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>').delay(100);
      });
      /*Adiciona os novos resultados a variável global*/
      $.each(novosDados['resultados'], function(indice, item){
          respostaConsulta['resultados'].push(item);
      });

    }
    else
    {
      telaListaResultados.append('<table class="table"><thead><tr><th>Código</th><th>Atividade</th><th>Nome da Empresa</th><th></th></tr></thead><tbody id="tbl_listaDeResultados"></tbody></table>');

      $.each(respostaConsulta['resultados'],function(indice, obj){
        $("#tbl_listaDeResultados").append('<tr><td>'+obj['codigo']+'</td><td>'+deixaMaiusculo(obj['atividade'])+'</td><td>'+deixaMaiusculo(obj['nome'])+'</td><td><button class="btn btn_maisInformacoesAtividadeEcon" data-codigo="'+obj['codigo']+'">Info.</button></td></tr>');
      });
      /*Se maior que 20 resultados, mostra botão mais resultados*/
      if(respostaConsulta['resultados'].length == 20)
      {
        telaListaResultados.append(`
            <div class="row">
              <button id="btn_deslocamento" class="btn btn-primary" style="margin-bottom: 60px; weight:10%; left: 45%;"><span class="glyphicon glyphicon-plus"></span> Resultados</button>
            </div>
          `);
      }
    }
    /*Mostra a tabela*/
    telaListaResultados.fadeIn(1000);
  }

function geraTabela_consultaEndereco(append, novosDados)
{
  /*Adiciona o conteúdo, ao invés de setar*/
  if(append !== 'undefined' && append == true)
  {
    $.each(novosDados['resultados'],function(indice, obj){
      $("#tbl_listaDeResultados").append(`
      <tr>
        <td>${obj['codigo']}</td>
        <td>${deixaMaiusculo(obj['nome'])}</td>
        <td>${deixaMaiusculo(obj['telefonesEnderecos'][0]['endereco'])}, ${obj['telefonesEnderecos'][0]['numero']} - ${deixaMaiusculo(obj['telefonesEnderecos'][0]['bairro'])}, ${deixaMaiusculo(obj['telefonesEnderecos'][0]['cidade'])} - ${obj['telefonesEnderecos'][0]['uf']}</td>
        <td><button class="btn btn_maisInformacoesEndereco" data-codigo="${+obj['codigo']}">Info.</button></td>
      </tr>
      `).delay(100);
    });
    /*Adiciona os novos resultados a variável global*/
    $.each(novosDados['resultados'], function(indice, item){
        respostaConsulta['resultados'].push(item);
    });
  }
  else
  {
    telaListaResultados.append('<table class="table"><thead><tr><th>Código</th><th>Nome</th><th>Endereço</th><th></th></tr></thead><tbody id="tbl_listaDeResultados"></tbody></table>');

    $.each(respostaConsulta['resultados'],function(indice, obj){
      $("#tbl_listaDeResultados").append(`
      <tr>
        <td>${obj['codigo']}</td>
        <td>${deixaMaiusculo(obj['nome'])}</td>
        <td>${deixaMaiusculo(obj['telefonesEnderecos'][0]['endereco'])}, ${obj['telefonesEnderecos'][0]['numero']} - ${deixaMaiusculo(obj['telefonesEnderecos'][0]['bairro'])}, ${deixaMaiusculo(obj['telefonesEnderecos'][0]['cidade'])} - ${obj['telefonesEnderecos'][0]['uf']}</td>
        <td><button class="btn btn_maisInformacoesEndereco" data-codigo="${+obj['codigo']}">Info.</button></td>
      </tr>
      `).delay(100);
    });
    /*Se maior que 20 resultados, mostra botão mais resultados*/
    if(respostaConsulta['resultados'].length == 20)
    {
      telaListaResultados.append(`
          <div class="row">
            <button id="btn_deslocamento" class="btn btn-primary" style="margin-bottom: 60px; weight:10%; left: 45%;"><span class="glyphicon glyphicon-plus"></span> Resultados</button>
          </div>
        `);
    }
  }
  /*Mostra a tabela*/
  telaListaResultados.fadeIn(1000);
  }

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                                    MONITORAMENTO DE EVENTOS                                 |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/

  /*Carrega o modal quando clicar no botão mais informações*/
  $(document).on("click", ".btn_maisInformacoes", function(){
    var codigo = $(this).attr('data-codigo');
    /*Habilita o botão de voltar*/
    dom_btnVoltar.fadeIn(1000);

    $.each(respostaConsulta['resultados'], function(indice, object){
      if(object['codigo'] == codigo)
      {
        telaListaResultados.fadeOut(500);
        return carregaDadosPessoa(respostaConsulta['resultados'][indice]);
      }
    });
  });

  /*Carrega o modal quando clicar no botão mais informações*/
  $(document).on("click", ".btn_maisInformacoesAtividadeEcon", function(){
    var codigo = $(this).attr('data-codigo');
    var resp = respostaConsulta;
    /*Habilita o botão de voltar*/
    dom_btnVoltar.fadeIn(1000);

    $.each(resp['resultados'], function(indice, object){
      if(object['codigo'] == codigo)
      {
        telaListaResultados.fadeOut(500);
        return carregaDadosPessoa(respostaConsulta['resultados'][indice], 'ATV_ECON');
      }
    });
  });

  /*Carrega o modal quando clicar no botão mais informações*/
  $(document).on("click", ".btn_maisInformacoesSocios", function(){
    var codigo = $(this).attr('data-codigo');
    var resp = respostaConsulta;
    /*Habilita o botão de voltar*/
    dom_btnVoltar.fadeIn(1000);

    $.each(resp['resultados'], function(indice, object){
      if(object['codigo'] == codigo)
      {
        telaListaResultados.fadeOut(500);
        return carregaDadosPessoa(respostaConsulta['resultados'][indice], 'ATV_ECON');
      }
    });
  });

  /*Quando o usuário solicita mais resultados para uma Pesquisa*/
  $(document).on("click", "#btn_deslocamento", function(){

    show_loading();

    formUsuario.push({name:"deslocamento", value:20, riquered:false, type:"text"});
    var data2 = new Object();
    $.each(formUsuario, function(indice, item){
      data2[item['name']] = item['value'];
    });

    var response = $.ajax({
      dataType: 'text',
      url: 'https://probusca.com/painel/page/request_catta.php',
      method: "post",
      data: data2
    });

    response.done(function(d){
      end_loading();
      d = JSON.parse(d);

      if(tipoRequisicao == 'ConsultaAtividadeEcon'){
        return geraTabela_consultaAtividadeEcon(true, d);
      }
      if(tipoRequisicao == 'ConsultaEndereco'){
        return geraTabela_consultaEndereco(true, d);
      }
      if(tipoRequisicao == 'ConsultaNome'){
        return geraTabela_consultaNome(true, d);
      }
      if(tipoRequisicao == 'ConsultaTelefone'){
        return geraTabela_consultaTelefone(true, d);
      }
      if(tipoRequisicao == 'ConsultaMae'){
        return geraTabela_consultaMae(true, d);
      }
      if(tipoRequisicao == 'ConsultaSocio'){
        return geraTabela_consultaSocio(true, d);
      }
      if(tipoRequisicao == 'ConsultaNascimento'){
        return geraTabela_consultaNascimento(true, d);
      }
    })

  });

  /*Carrega o modal quando clicar no botão mais informações*/
  $(document).on("click", ".btn_maisInformacoesEndereco", function(){
    var codigo = $(this).attr('data-codigo');
    var resp = respostaConsulta;
    /*Habilita o botão de voltar*/
    dom_btnVoltar.fadeIn(1000);

    $.each(resp['resultados'], function(indice, object){
      if(object['codigo'] == codigo)
      {
        telaListaResultados.fadeOut(500);
        return carregaDadosPessoa(respostaConsulta['resultados'][indice]);
      }
    });
  });

  /*Monitora Evento botão voltar*/
  dom_btnVoltar.on("click", function(){
    telaApresentacao.fadeOut(800);
    telaListaResultados.fadeIn(1200);

    /*Limpa Dados*/
    pessoa_codigo.empty();
    pessoa_nome.empty();
    pessoa_cpf.empty();
    pessoa_nascimento.empty();
    pessoa_mae.empty();
    /*Tabelas Dinâmicas*/
    tabela_telefonesAtualizados.empty();
    tabela_telefonesAbsoletos.empty();
    tabela_enderecosAtualizados.empty();
    tabela_enderecosAbsoletos.empty();
    /*Outros*/
    $("#apr_identificacaoEmpresa_tabela").empty();
    $("#apr_identificacaoSocios_tabela").empty();
  });

  /*Monitora evento de impressão*/
  dom_btnImprimir.on("click", function(){
    $("#apr_botoes").hide();
    var conteudo = document.getElementById('apr').innerHTML;
    tela_impressao = window.open('about:blank');
    tela_impressao.document.write(conteudo);
    tela_impressao.window.print();
    tela_impressao.window.close();
    $("#apr_botoes").show();
  });

  $(document).on("click", ".nav-tabs", function(){
    /*Desabilita as telas envolvidas na pesquisa*/
    $("#apr_identificacaoPessoa").show();
    $("#apr_identificacaoEmpresa").hide();
    $("#apr_identificacaoSocios").hide();
    telaListaResultados.fadeOut(100);
    msg_nadaEncontrado.fadeOut(100);
    telaApresentacao.fadeOut(100);
    /*Esconde botão voltar*/
    dom_btnVoltar.fadeOut(100);

    limpaTudo();
  });

};/*end*/




</script>
