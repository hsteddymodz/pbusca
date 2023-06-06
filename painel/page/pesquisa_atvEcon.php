<?php

// classe de conexao ao banco de dados
include('../class/Conexao.class.php');
include($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'painel/class/LimitarConsulta.function.php');
include($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'painel/class/Token.class.php');


$con = new Conexao();

// verificamos se o usuario ainda esta logado
if(!$_SESSION) @session_start();

// limita o numero de consultas
limitarConsulta($con, $_SESSION['usuario'], 'cs');

// geramos um novo token pro usuario
$token = new Token();
$token = $token->get_token();

?>

<!DOCTYPE html>


<style>
      h3{
        margin: 20px 0 20px 0;
      }

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
  </style>


  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

    <div class="row">
  		<ol class="breadcrumb">
  				<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
  				<li class="active">Ativida Econômica</li>
  			</ol>
  	</div><!--/.row-->

  	<div class="row">
  			<div class="col-lg-12">
  				<h1 class="page-header">Pesquisa Ativida Econômica <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
  			</div>
  		</div><!--/.row-->

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


  </div><!--endContainer-->

  <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">

    <!--Esta DIV é utilizada para mostrat uma lista de resultados-->
    <div id="aprList"></div>

    <!--Utilizada para armazenar a mensagem que informa que nada foi encontrado-->
    <div class="alert alert-warning" style="display: none;" id="apr_mensagem">
        <h3><span class="glyphicon glyphicon-warning-sign"></span> Nenhum resultado válido foi encontrado!</h3>
      </div>

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

  </div>



<script>

  window.onload = function() {
    /*Inica máscara de dados*/
    $('.cep').mask('00000-000');
    $('.date').mask('00/00/0000');

    /*-------------------->Variáveis Globais<----------------*/
    var tipoRequisicao = '';
    var respostaConsulta;
    var formUsuario;
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

    var cidadesBrasileiras = `
    <option value="0"></option>
    <option value="3100104">Abadia dos Dourados</option>
    <option value="3100203">Abaeté</option>
    <option value="3100302">Abre Campo</option>
    <option value="3100401">Acaiaca</option>
    <option value="3100500">Açucena</option>
    <option value="3100609">Água Boa</option>
    <option value="3100708">Água Comprida</option>
    <option value="3100807">Aguanil</option>
    <option value="3100906">Águas Formosas</option>
    <option value="3101003">Águas Vermelhas</option>
    <option value="3101102">Aimorés</option>
    <option value="3101201">Aiuruoca</option>
    <option value="3101300">Alagoa</option>
    <option value="3101409">Albertina</option>
    <option value="3101508">Além Paraíba</option>
    <option value="3101607">Alfenas</option>
    <option value="3101631">Alfredo Vasconcelos</option>
    <option value="3101706">Almenara</option>
    <option value="3101805">Alpercata</option>
    <option value="3101904">Alpinópolis</option>
    <option value="3102001">Alterosa</option>
    <option value="3102050">Alto Caparaó</option>
    <option value="3153509">Alto Jequitibá</option>
    <option value="3102100">Alto Rio Doce</option>
    <option value="3102209">Alvarenga</option>
    <option value="3102308">Alvinópolis</option>
    <option value="3102407">Alvorada de Minas</option>
    <option value="3102506">Amparo do Serra</option>
    <option value="3102605">Andradas</option>
    <option value="3102803">Andrelândia</option>
    <option value="3102852">Angelândia</option>
    <option value="3102902">Antônio Carlos</option>
    <option value="3103009">Antônio Dias</option>
    <option value="3103108">Antônio Prado de Minas</option>
    <option value="3103207">Araçaí</option>
    <option value="3103306">Aracitaba</option>
    <option value="3103405">Araçuaí</option>
    <option value="3103504">Araguari</option>
    <option value="3103603">Arantina</option>
    <option value="3103702">Araponga</option>
    <option value="3103751">Araporã</option>
    <option value="3103801">Arapuá</option>
    <option value="3103900">Araújos</option>
    <option value="3104007">Araxá</option>
    <option value="3104106">Arceburgo</option>
    <option value="3104205">Arcos</option>
    <option value="3104304">Areado</option>
    <option value="3104403">Argirita</option>
    <option value="3104452">Aricanduva</option>
    <option value="3104502">Arinos</option>
    <option value="3104601">Astolfo Dutra</option>
    <option value="3104700">Ataléia</option>
    <option value="3104809">Augusto de Lima</option>
    <option value="3104908">Baependi</option>
    <option value="3105004">Baldim</option>
    <option value="3105103">Bambuí</option>
    <option value="3105202">Bandeira</option>
    <option value="3105301">Bandeira do Sul</option>
    <option value="3105400">Barão de Cocais</option>
    <option value="3105509">Barão de Monte Alto</option>
    <option value="3105608">Barbacena</option>
    <option value="3105707">Barra Longa</option>
    <option value="3105905">Barroso</option>
    <option value="3106002">Bela Vista de Minas</option>
    <option value="3106101">Belmiro Braga</option>
    <option value="3106200">Belo Horizonte</option>
    <option value="3106309">Belo Oriente</option>
    <option value="3106408">Belo Vale</option>
    <option value="3106507">Berilo</option>
    <option value="3106655">Berizal</option>
    <option value="3106606">Bertópolis</option>
    <option value="3106705">Betim</option>
    <option value="3106804">Bias Fortes</option>
    <option value="3106903">Bicas</option>
    <option value="3107000">Biquinhas</option>
    <option value="3107109">Boa Esperança</option>
    <option value="3107208">Bocaina de Minas</option>
    <option value="3107307">Bocaiúva</option>
    <option value="3107406">Bom Despacho</option>
    <option value="3107505">Bom Jardim de Minas</option>
    <option value="3107604">Bom Jesus da Penha</option>
    <option value="3107703">Bom Jesus do Amparo</option>
    <option value="3107802">Bom Jesus do Galho</option>
    <option value="3107901">Bom Repouso</option>
    <option value="3108008">Bom Sucesso</option>
    <option value="3108107">Bonfim</option>
    <option value="3108206">Bonfinópolis de Minas</option>
    <option value="3108255">Bonito de Minas</option>
    <option value="3108305">Borda da Mata</option>
    <option value="3108404">Botelhos</option>
    <option value="3108503">Botumirim</option>
    <option value="3108552">Brasilândia de Minas</option>
    <option value="3108602">Brasília de Minas</option>
    <option value="3108909">Brasópolis</option>
    <option value="3108701">Brás Pires</option>
    <option value="3108800">Braúnas</option>
    <option value="3109006">Brumadinho</option>
    <option value="3109105">Bueno Brandão</option>
    <option value="3109204">Buenópolis</option>
    <option value="3109253">Bugre</option>
    <option value="3109303">Buritis</option>
    <option value="3109402">Buritizeiro</option>
    <option value="3109451">Cabeceira Grande</option>
    <option value="3109501">Cabo Verde</option>
    <option value="3109600">Cachoeira da Prata</option>
    <option value="3109709">Cachoeira de Minas</option>
    <option value="3102704">Cachoeira de Pajeú</option>
    <option value="3109808">Cachoeira Dourada</option>
    <option value="3109907">Caetanópolis</option>
    <option value="3110004">Caeté</option>
    <option value="3110103">Caiana</option>
    <option value="3110202">Cajuri</option>
    <option value="3110301">Caldas</option>
    <option value="3110400">Camacho</option>
    <option value="3110509">Camanducaia</option>
    <option value="3110608">Cambuí</option>
    <option value="3110707">Cambuquira</option>
    <option value="3110806">Campanário</option>
    <option value="3110905">Campanha</option>
    <option value="3111002">Campestre</option>
    <option value="3111101">Campina Verde</option>
    <option value="3111150">Campo Azul</option>
    <option value="3111200">Campo Belo</option>
    <option value="3111309">Campo do Meio</option>
    <option value="3111408">Campo Florido</option>
    <option value="3111507">Campos Altos</option>
    <option value="3111606">Campos Gerais</option>
    <option value="3111705">Canaã</option>
    <option value="3111804">Canápolis</option>
    <option value="3111903">Cana Verde</option>
    <option value="3112000">Candeias</option>
    <option value="3112059">Cantagalo</option>
    <option value="3112109">Caparaó</option>
    <option value="3112208">Capela Nova</option>
    <option value="3112307">Capelinha</option>
    <option value="3112406">Capetinga</option>
    <option value="3112505">Capim Branco</option>
    <option value="3112604">Capinópolis</option>
    <option value="3112653">Capitão Andrade</option>
    <option value="3112703">Capitão Enéas</option>
    <option value="3112802">Capitólio</option>
    <option value="3112901">Caputira</option>
    <option value="3113008">Caraí</option>
    <option value="3113107">Caranaíba</option>
    <option value="3113206">Carandaí</option>
    <option value="3113305">Carangola</option>
    <option value="3113404">Caratinga</option>
    <option value="3113503">Carbonita</option>
    <option value="3113602">Careaçu</option>
    <option value="3113701">Carlos Chagas</option>
    <option value="3113800">Carmésia</option>
    <option value="3113909">Carmo da Cachoeira</option>
    <option value="3114006">Carmo da Mata</option>
    <option value="3114105">Carmo de Minas</option>
    <option value="3114204">Carmo do Cajuru</option>
    <option value="3114303">Carmo do Paranaíba</option>
    <option value="3114402">Carmo do Rio Claro</option>
    <option value="3114501">Carmópolis de Minas</option>
    <option value="3114550">Carneirinho</option>
    <option value="3114600">Carrancas</option>
    <option value="3114709">Carvalhópolis</option>
    <option value="3114808">Carvalhos</option>
    <option value="3114907">Casa Grande</option>
    <option value="3115003">Cascalho Rico</option>
    <option value="3115102">Cássia</option>
    <option value="3115300">Cataguases</option>
    <option value="3115359">Catas Altas</option>
    <option value="3115409">Catas Altas da Noruega</option>
    <option value="3115458">Catuji</option>
    <option value="3115474">Catuti</option>
    <option value="3115508">Caxambu</option>
    <option value="3115607">Cedro do Abaeté</option>
    <option value="3115706">Central de Minas</option>
    <option value="3115805">Centralina</option>
    <option value="3115904">Chácara</option>
    <option value="3116001">Chalé</option>
    <option value="3116100">Chapada do Norte</option>
    <option value="3116159">Chapada Gaúcha</option>
    <option value="3116209">Chiador</option>
    <option value="3116308">Cipotânea</option>
    <option value="3116407">Claraval</option>
    <option value="3116506">Claro dos Poções</option>
    <option value="3116605">Cláudio</option>
    <option value="3116704">Coimbra</option>
    <option value="3116803">Coluna</option>
    <option value="3116902">Comendador Gomes</option>
    <option value="3117009">Comercinho</option>
    <option value="3117108">Conceição da Aparecida</option>
    <option value="3115201">Conceição da Barra de Minas</option>
    <option value="3117306">Conceição das Alagoas</option>
    <option value="3117207">Conceição das Pedras</option>
    <option value="3117405">Conceição de Ipanema</option>
    <option value="3117504">Conceição do Mato Dentro</option>
    <option value="3117603">Conceição do Pará</option>
    <option value="3117702">Conceição do Rio Verde</option>
    <option value="3117801">Conceição dos Ouros</option>
    <option value="3117836">Cônego Marinho</option>
    <option value="3117876">Confins</option>
    <option value="3117900">Congonhal</option>
    <option value="3118007">Congonhas</option>
    <option value="3118106">Congonhas do Norte</option>
    <option value="3118205">Conquista</option>
    <option value="3118304">Conselheiro Lafaiete</option>
    <option value="3118403">Conselheiro Pena</option>
    <option value="3118502">Consolação</option>
    <option value="3118601">Contagem</option>
    <option value="3118700">Coqueiral</option>
    <option value="3118809">Coração de Jesus</option>
    <option value="3118908">Cordisburgo</option>
    <option value="3119005">Cordislândia</option>
    <option value="3119104">Corinto</option>
    <option value="3119203">Coroaci</option>
    <option value="3119302">Coromandel</option>
    <option value="3119401">Coronel Fabriciano</option>
    <option value="3119500">Coronel Murta</option>
    <option value="3119609">Coronel Pacheco</option>
    <option value="3119708">Coronel Xavier Chaves</option>
    <option value="3119807">Córrego Danta</option>
    <option value="3119906">Córrego do Bom Jesus</option>
    <option value="3119955">Córrego Fundo</option>
    <option value="3120003">Córrego Novo</option>
    <option value="3120102">Couto de Magalhães de Minas</option>
    <option value="3120151">Crisólita</option>
    <option value="3120201">Cristais</option>
    <option value="3120300">Cristália</option>
    <option value="3120409">Cristiano Otoni</option>
    <option value="3120508">Cristina</option>
    <option value="3120607">Crucilândia</option>
    <option value="3120706">Cruzeiro da Fortaleza</option>
    <option value="3120805">Cruzília</option>
    <option value="3120839">Cuparaque</option>
    <option value="3120870">Curral de Dentro</option>
    <option value="3120904">Curvelo</option>
    <option value="3121001">Datas</option>
    <option value="3121100">Delfim Moreira</option>
    <option value="3121209">Delfinópolis</option>
    <option value="3121258">Delta</option>
    <option value="3121308">Descoberto</option>
    <option value="3121407">Desterro de Entre Rios</option>
    <option value="3121506">Desterro do Melo</option>
    <option value="3121605">Diamantina</option>
    <option value="3121704">Diogo de Vasconcelos</option>
    <option value="3121803">Dionísio</option>
    <option value="3121902">Divinésia</option>
    <option value="3122009">Divino</option>
    <option value="3122108">Divino das Laranjeiras</option>
    <option value="3122207">Divinolândia de Minas</option>
    <option value="3122306">Divinópolis</option>
    <option value="3122355">Divisa Alegre</option>
    <option value="3122405">Divisa Nova</option>
    <option value="3122454">Divisópolis</option>
    <option value="3122470">Dom Bosco</option>
    <option value="3122504">Dom Cavati</option>
    <option value="3122603">Dom Joaquim</option>
    <option value="3122702">Dom Silvério</option>
    <option value="3122801">Dom Viçoso</option>
    <option value="3122900">Dona Eusébia</option>
    <option value="3123007">Dores de Campos</option>
    <option value="3123106">Dores de Guanhães</option>
    <option value="3123205">Dores do Indaiá</option>
    <option value="3123304">Dores do Turvo</option>
    <option value="3123403">Doresópolis</option>
    <option value="3123502">Douradoquara</option>
    <option value="3123528">Durandé</option>
    <option value="3123601">Elói Mendes</option>
    <option value="3123700">Engenheiro Caldas</option>
    <option value="3123809">Engenheiro Navarro</option>
    <option value="3123858">Entre Folhas</option>
    <option value="3123908">Entre Rios de Minas</option>
    <option value="3124005">Ervália</option>
    <option value="3124104">Esmeraldas</option>
    <option value="3124203">Espera Feliz</option>
    <option value="3124302">Espinosa</option>
    <option value="3124401">Espírito Santo do Dourado</option>
    <option value="3124500">Estiva</option>
    <option value="3124609">Estrela Dalva</option>
    <option value="3124708">Estrela do Indaiá</option>
    <option value="3124807">Estrela do Sul</option>
    <option value="3124906">Eugenópolis</option>
    <option value="3125002">Ewbank da Câmara</option>
    <option value="3125101">Extrema</option>
    <option value="3125200">Fama</option>
    <option value="3125309">Faria Lemos</option>
    <option value="3125408">Felício dos Santos</option>
    <option value="3125606">Felisburgo</option>
    <option value="3125705">Felixlândia</option>
    <option value="3125804">Fernandes Tourinho</option>
    <option value="3125903">Ferros</option>
    <option value="3125952">Fervedouro</option>
    <option value="3126000">Florestal</option>
    <option value="3126109">Formiga</option>
    <option value="3126208">Formoso</option>
    <option value="3126307">Fortaleza de Minas</option>
    <option value="3126406">Fortuna de Minas</option>
    <option value="3126505">Francisco Badaró</option>
    <option value="3126604">Francisco Dumont</option>
    <option value="3126752">Franciscópolis</option>
    <option value="3126703">Francisco Sá</option>
    <option value="3126802">Frei Gaspar</option>
    <option value="3126901">Frei Inocêncio</option>
    <option value="3126950">Frei Lagonegro</option>
    <option value="3127008">Fronteira</option>
    <option value="3127057">Fronteira dos Vales</option>
    <option value="3127073">Fruta de Leite</option>
    <option value="3127107">Frutal</option>
    <option value="3127206">Funilândia</option>
    <option value="3127305">Galiléia</option>
    <option value="3127339">Gameleiras</option>
    <option value="3127354">Glaucilândia</option>
    <option value="3127370">Goiabeira</option>
    <option value="3127388">Goianá</option>
    <option value="3127404">Gonçalves</option>
    <option value="3127503">Gonzaga</option>
    <option value="3127602">Gouveia</option>
    <option value="3127701">Governador Valadares</option>
    <option value="3127800">Grão Mogol</option>
    <option value="3127909">Grupiara</option>
    <option value="3128006">Guanhães</option>
    <option value="3128105">Guapé</option>
    <option value="3128204">Guaraciaba</option>
    <option value="3128253">Guaraciama</option>
    <option value="3128303">Guaranésia</option>
    <option value="3128402">Guarani</option>
    <option value="3128501">Guarará</option>
    <option value="3128600">Guarda-Mor</option>
    <option value="3128709">Guaxupé</option>
    <option value="3128808">Guidoval</option>
    <option value="3128907">Guimarânia</option>
    <option value="3129004">Guiricema</option>
    <option value="3129103">Gurinhatã</option>
    <option value="3129202">Heliodora</option>
    <option value="3129301">Iapu</option>
    <option value="3129400">Ibertioga</option>
    <option value="3129509">Ibiá</option>
    <option value="3129608">Ibiaí</option>
    <option value="3129657">Ibiracatu</option>
    <option value="3129707">Ibiraci</option>
    <option value="3129806">Ibirité</option>
    <option value="3129905">Ibitiúra de Minas</option>
    <option value="3130002">Ibituruna</option>
    <option value="3130051">Icaraí de Minas</option>
    <option value="3130101">Igarapé</option>
    <option value="3130200">Igaratinga</option>
    <option value="3130309">Iguatama</option>
    <option value="3130408">Ijaci</option>
    <option value="3130507">Ilicínea</option>
    <option value="3130556">Imbé de Minas</option>
    <option value="3130606">Inconfidentes</option>
    <option value="3130655">Indaiabira</option>
    <option value="3130705">Indianópolis</option>
    <option value="3130804">Ingaí</option>
    <option value="3130903">Inhapim</option>
    <option value="3131000">Inhaúma</option>
    <option value="3131109">Inimutaba</option>
    <option value="3131158">Ipaba</option>
    <option value="3131208">Ipanema</option>
    <option value="3131307">Ipatinga</option>
    <option value="3131406">Ipiaçu</option>
    <option value="3131505">Ipuiúna</option>
    <option value="3131604">Iraí de Minas</option>
    <option value="3131703">Itabira</option>
    <option value="3131802">Itabirinha</option>
    <option value="3131901">Itabirito</option>
    <option value="3132008">Itacambira</option>
    <option value="3132107">Itacarambi</option>
    <option value="3132206">Itaguara</option>
    <option value="3132305">Itaipé</option>
    <option value="3132404">Itajubá</option>
    <option value="3132503">Itamarandiba</option>
    <option value="3132602">Itamarati de Minas</option>
    <option value="3132701">Itambacuri</option>
    <option value="3132800">Itambé do Mato Dentro</option>
    <option value="3132909">Itamogi</option>
    <option value="3133006">Itamonte</option>
    <option value="3133105">Itanhandu</option>
    <option value="3133204">Itanhomi</option>
    <option value="3133303">Itaobim</option>
    <option value="3133402">Itapagipe</option>
    <option value="3133501">Itapecerica</option>
    <option value="3133600">Itapeva</option>
    <option value="3133709">Itatiaiuçu</option>
    <option value="3133758">Itaú de Minas</option>
    <option value="3133808">Itaúna</option>
    <option value="3133907">Itaverava</option>
    <option value="3134004">Itinga</option>
    <option value="3134103">Itueta</option>
    <option value="3134202">Ituiutaba</option>
    <option value="3134301">Itumirim</option>
    <option value="3134400">Iturama</option>
    <option value="3134509">Itutinga</option>
    <option value="3134608">Jaboticatubas</option>
    <option value="3134707">Jacinto</option>
    <option value="3134806">Jacuí</option>
    <option value="3134905">Jacutinga</option>
    <option value="3135001">Jaguaraçu</option>
    <option value="3135050">Jaíba</option>
    <option value="3135076">Jampruca</option>
    <option value="3135100">Janaúba</option>
    <option value="3135209">Januária</option>
    <option value="3135308">Japaraíba</option>
    <option value="3135357">Japonvar</option>
    <option value="3135407">Jeceaba</option>
    <option value="3135456">Jenipapo de Minas</option>
    <option value="3135506">Jequeri</option>
    <option value="3135605">Jequitaí</option>
    <option value="3135704">Jequitibá</option>
    <option value="3135803">Jequitinhonha</option>
    <option value="3135902">Jesuânia</option>
    <option value="3136009">Joaíma</option>
    <option value="3136108">Joanésia</option>
    <option value="3136207">João Monlevade</option>
    <option value="3136306">João Pinheiro</option>
    <option value="3136405">Joaquim Felício</option>
    <option value="3136504">Jordânia</option>
    <option value="3136520">José Gonçalves de Minas</option>
    <option value="3136579">Josenópolis</option>
    <option value="3136553">José Raydan</option>
    <option value="3136652">Juatuba</option>
    <option value="3136702">Juiz de Fora</option>
    <option value="3136801">Juramento</option>
    <option value="3136900">Juruaia</option>
    <option value="3136959">Juvenília</option>
    <option value="3137007">Ladainha</option>
    <option value="3137106">Lagamar</option>
    <option value="3137205">Lagoa da Prata</option>
    <option value="3137304">Lagoa dos Patos</option>
    <option value="3137403">Lagoa Dourada</option>
    <option value="3137502">Lagoa Formosa</option>
    <option value="3137536">Lagoa Grande</option>
    <option value="3137601">Lagoa Santa</option>
    <option value="3137700">Lajinha</option>
    <option value="3137809">Lambari</option>
    <option value="3137908">Lamim</option>
    <option value="3138005">Laranjal</option>
    <option value="3138104">Lassance</option>
    <option value="3138203">Lavras</option>
    <option value="3138302">Leandro Ferreira</option>
    <option value="3138351">Leme do Prado</option>
    <option value="3138401">Leopoldina</option>
    <option value="3138500">Liberdade</option>
    <option value="3138609">Lima Duarte</option>
    <option value="3138625">Limeira do Oeste</option>
    <option value="3138658">Lontra</option>
    <option value="3138674">Luisburgo</option>
    <option value="3138682">Luislândia</option>
    <option value="3138708">Luminárias</option>
    <option value="3138807">Luz</option>
    <option value="3138906">Machacalis</option>
    <option value="3139003">Machado</option>
    <option value="3139102">Madre de Deus de Minas</option>
    <option value="3139201">Malacacheta</option>
    <option value="3139250">Mamonas</option>
    <option value="3139300">Manga</option>
    <option value="3139409">Manhuaçu</option>
    <option value="3139508">Manhumirim</option>
    <option value="3139607">Mantena</option>
    <option value="3139706">Maravilhas</option>
    <option value="3139805">Mar de Espanha</option>
    <option value="3139904">Maria da Fé</option>
    <option value="3140001">Mariana</option>
    <option value="3140100">Marilac</option>
    <option value="3140159">Mário Campos</option>
    <option value="3140209">Maripá de Minas</option>
    <option value="3140308">Marliéria</option>
    <option value="3140407">Marmelópolis</option>
    <option value="3140506">Martinho Campos</option>
    <option value="3140530">Martins Soares</option>
    <option value="3140555">Mata Verde</option>
    <option value="3140605">Materlândia</option>
    <option value="3140704">Mateus Leme</option>
    <option value="3171501">Mathias Lobato</option>
    <option value="3140803">Matias Barbosa</option>
    <option value="3140852">Matias Cardoso</option>
    <option value="3140902">Matipó</option>
    <option value="3141009">Mato Verde</option>
    <option value="3141108">Matozinhos</option>
    <option value="3141207">Matutina</option>
    <option value="3141306">Medeiros</option>
    <option value="3141405">Medina</option>
    <option value="3141504">Mendes Pimentel</option>
    <option value="3141603">Mercês</option>
    <option value="3141702">Mesquita</option>
    <option value="3141801">Minas Novas</option>
    <option value="3141900">Minduri</option>
    <option value="3142007">Mirabela</option>
    <option value="3142106">Miradouro</option>
    <option value="3142205">Miraí</option>
    <option value="3142254">Miravânia</option>
    <option value="3142304">Moeda</option>
    <option value="3142403">Moema</option>
    <option value="3142502">Monjolos</option>
    <option value="3142601">Monsenhor Paulo</option>
    <option value="3142700">Montalvânia</option>
    <option value="3142809">Monte Alegre de Minas</option>
    <option value="3142908">Monte Azul</option>
    <option value="3143005">Monte Belo</option>
    <option value="3143104">Monte Carmelo</option>
    <option value="3143153">Monte Formoso</option>
    <option value="3143203">Monte Santo de Minas</option>
    <option value="3143302">Montes Claros</option>
    <option value="3143401">Monte Sião</option>
    <option value="3143450">Montezuma</option>
    <option value="3143500">Morada Nova de Minas</option>
    <option value="3143609">Morro da Garça</option>
    <option value="3143708">Morro do Pilar</option>
    <option value="3143807">Munhoz</option>
    <option value="3143906">Muriaé</option>
    <option value="3144003">Mutum</option>
    <option value="3144102">Muzambinho</option>
    <option value="3144201">Nacip Raydan</option>
    <option value="3144300">Nanuque</option>
    <option value="3144359">Naque</option>
    <option value="3144375">Natalândia</option>
    <option value="3144409">Natércia</option>
    <option value="3144508">Nazareno</option>
    <option value="3144607">Nepomuceno</option>
    <option value="3144656">Ninheira</option>
    <option value="3144672">Nova Belém</option>
    <option value="3144706">Nova Era</option>
    <option value="3144805">Nova Lima</option>
    <option value="3144904">Nova Módica</option>
    <option value="3145000">Nova Ponte</option>
    <option value="3145059">Nova Porteirinha</option>
    <option value="3145109">Nova Resende</option>
    <option value="3145208">Nova Serrana</option>
    <option value="3136603">Nova União</option>
    <option value="3145307">Novo Cruzeiro</option>
    <option value="3145356">Novo Oriente de Minas</option>
    <option value="3145372">Novorizonte</option>
    <option value="3145406">Olaria</option>
    <option value="3145455">Olhos-d'Água</option>
    <option value="3145505">Olímpio Noronha</option>
    <option value="3145604">Oliveira</option>
    <option value="3145703">Oliveira Fortes</option>
    <option value="3145802">Onça de Pitangui</option>
    <option value="3145851">Oratórios</option>
    <option value="3145877">Orizânia</option>
    <option value="3145901">Ouro Branco</option>
    <option value="3146008">Ouro Fino</option>
    <option value="3146107">Ouro Preto</option>
    <option value="3146206">Ouro Verde de Minas</option>
    <option value="3146255">Padre Carvalho</option>
    <option value="3146305">Padre Paraíso</option>
    <option value="3146404">Paineiras</option>
    <option value="3146503">Pains</option>
    <option value="3146552">Pai Pedro</option>
    <option value="3146602">Paiva</option>
    <option value="3146701">Palma</option>
    <option value="3146750">Palmópolis</option>
    <option value="3146909">Papagaios</option>
    <option value="3147006">Paracatu</option>
    <option value="3147105">Pará de Minas</option>
    <option value="3147204">Paraguaçu</option>
    <option value="3147303">Paraisópolis</option>
    <option value="3147402">Paraopeba</option>
    <option value="3147501">Passabém</option>
    <option value="3147600">Passa Quatro</option>
    <option value="3147709">Passa Tempo</option>
    <option value="3147808">Passa-Vinte</option>
    <option value="3147907">Passos</option>
    <option value="3147956">Patis</option>
    <option value="3148004">Patos de Minas</option>
    <option value="3148103">Patrocínio</option>
    <option value="3148202">Patrocínio do Muriaé</option>
    <option value="3148301">Paula Cândido</option>
    <option value="3148400">Paulistas</option>
    <option value="3148509">Pavão</option>
    <option value="3148608">Peçanha</option>
    <option value="3148707">Pedra Azul</option>
    <option value="3148756">Pedra Bonita</option>
    <option value="3148806">Pedra do Anta</option>
    <option value="3148905">Pedra do Indaiá</option>
    <option value="3149002">Pedra Dourada</option>
    <option value="3149101">Pedralva</option>
    <option value="3149150">Pedras de Maria da Cruz</option>
    <option value="3149200">Pedrinópolis</option>
    <option value="3149309">Pedro Leopoldo</option>
    <option value="3149408">Pedro Teixeira</option>
    <option value="3149507">Pequeri</option>
    <option value="3149606">Pequi</option>
    <option value="3149705">Perdigão</option>
    <option value="3149804">Perdizes</option>
    <option value="3149903">Perdões</option>
    <option value="3149952">Periquito</option>
    <option value="3150000">Pescador</option>
    <option value="3150109">Piau</option>
    <option value="3150158">Piedade de Caratinga</option>
    <option value="3150208">Piedade de Ponte Nova</option>
    <option value="3150307">Piedade do Rio Grande</option>
    <option value="3150406">Piedade dos Gerais</option>
    <option value="3150505">Pimenta</option>
    <option value="3150539">Pingo-d'Água</option>
    <option value="3150570">Pintópolis</option>
    <option value="3150604">Piracema</option>
    <option value="3150703">Pirajuba</option>
    <option value="3150802">Piranga</option>
    <option value="3150901">Piranguçu</option>
    <option value="3151008">Piranguinho</option>
    <option value="3151107">Pirapetinga</option>
    <option value="3151206">Pirapora</option>
    <option value="3151305">Piraúba</option>
    <option value="3151404">Pitangui</option>
    <option value="3151503">Piumhi</option>
    <option value="3151602">Planura</option>
    <option value="3151701">Poço Fundo</option>
    <option value="3151800">Poços de Caldas</option>
    <option value="3151909">Pocrane</option>
    <option value="3152006">Pompéu</option>
    <option value="3152105">Ponte Nova</option>
    <option value="3152131">Ponto Chique</option>
    <option value="3152170">Ponto dos Volantes</option>
    <option value="3152204">Porteirinha</option>
    <option value="3152303">Porto Firme</option>
    <option value="3152402">Poté</option>
    <option value="3152501">Pouso Alegre</option>
    <option value="3152600">Pouso Alto</option>
    <option value="3152709">Prados</option>
    <option value="3152808">Prata</option>
    <option value="3152907">Pratápolis</option>
    <option value="3153004">Pratinha</option>
    <option value="3153103">Presidente Bernardes</option>
    <option value="3153202">Presidente Juscelino</option>
    <option value="3153301">Presidente Kubitschek</option>
    <option value="3153400">Presidente Olegário</option>
    <option value="3153608">Prudente de Morais</option>
    <option value="3153707">Quartel Geral</option>
    <option value="3153806">Queluzito</option>
    <option value="3153905">Raposos</option>
    <option value="3154002">Raul Soares</option>
    <option value="3154101">Recreio</option>
    <option value="3154150">Reduto</option>
    <option value="3154200">Resende Costa</option>
    <option value="3154309">Resplendor</option>
    <option value="3154408">Ressaquinha</option>
    <option value="3154457">Riachinho</option>
    <option value="3154507">Riacho dos Machados</option>
    <option value="3154606">Ribeirão das Neves</option>
    <option value="3154705">Ribeirão Vermelho</option>
    <option value="3154804">Rio Acima</option>
    <option value="3154903">Rio Casca</option>
    <option value="3155009">Rio Doce</option>
    <option value="3155108">Rio do Prado</option>
    <option value="3155207">Rio Espera</option>
    <option value="3155306">Rio Manso</option>
    <option value="3155405">Rio Novo</option>
    <option value="3155504">Rio Paranaíba</option>
    <option value="3155603">Rio Pardo de Minas</option>
    <option value="3155702">Rio Piracicaba</option>
    <option value="3155801">Rio Pomba</option>
    <option value="3155900">Rio Preto</option>
    <option value="3156007">Rio Vermelho</option>
    <option value="3156106">Ritápolis</option>
    <option value="3156205">Rochedo de Minas</option>
    <option value="3156304">Rodeiro</option>
    <option value="3156403">Romaria</option>
    <option value="3156452">Rosário da Limeira</option>
    <option value="3156502">Rubelita</option>
    <option value="3156601">Rubim</option>
    <option value="3156700">Sabará</option>
    <option value="3156809">Sabinópolis</option>
    <option value="3156908">Sacramento</option>
    <option value="3157005">Salinas</option>
    <option value="3157104">Salto da Divisa</option>
    <option value="3157203">Santa Bárbara</option>
    <option value="3157252">Santa Bárbara do Leste</option>
    <option value="3157278">Santa Bárbara do Monte Verde</option>
    <option value="3157302">Santa Bárbara do Tugúrio</option>
    <option value="3157336">Santa Cruz de Minas</option>
    <option value="3157377">Santa Cruz de Salinas</option>
    <option value="3157401">Santa Cruz do Escalvado</option>
    <option value="3157500">Santa Efigênia de Minas</option>
    <option value="3157609">Santa Fé de Minas</option>
    <option value="3157658">Santa Helena de Minas</option>
    <option value="3157708">Santa Juliana</option>
    <option value="3157807">Santa Luzia</option>
    <option value="3157906">Santa Margarida</option>
    <option value="3158003">Santa Maria de Itabira</option>
    <option value="3158102">Santa Maria do Salto</option>
    <option value="3158201">Santa Maria do Suaçuí</option>
    <option value="3158300">Santana da Vargem</option>
    <option value="3158409">Santana de Cataguases</option>
    <option value="3158508">Santana de Pirapama</option>
    <option value="3158607">Santana do Deserto</option>
    <option value="3158706">Santana do Garambéu</option>
    <option value="3158805">Santana do Jacaré</option>
    <option value="3158904">Santana do Manhuaçu</option>
    <option value="3158953">Santana do Paraíso</option>
    <option value="3159001">Santana do Riacho</option>
    <option value="3159100">Santana dos Montes</option>
    <option value="3159209">Santa Rita de Caldas</option>
    <option value="3159407">Santa Rita de Ibitipoca</option>
    <option value="3159308">Santa Rita de Jacutinga</option>
    <option value="3159357">Santa Rita de Minas</option>
    <option value="3159506">Santa Rita do Itueto</option>
    <option value="3159605">Santa Rita do Sapucaí</option>
    <option value="3159704">Santa Rosa da Serra</option>
    <option value="3159803">Santa Vitória</option>
    <option value="3159902">Santo Antônio do Amparo</option>
    <option value="3160009">Santo Antônio do Aventureiro</option>
    <option value="3160108">Santo Antônio do Grama</option>
    <option value="3160207">Santo Antônio do Itambé</option>
    <option value="3160306">Santo Antônio do Jacinto</option>
    <option value="3160405">Santo Antônio do Monte</option>
    <option value="3160454">Santo Antônio do Retiro</option>
    <option value="3160504">Santo Antônio do Rio Abaixo</option>
    <option value="3160603">Santo Hipólito</option>
    <option value="3160702">Santos Dumont</option>
    <option value="3160801">São Bento Abade</option>
    <option value="3160900">São Brás do Suaçuí</option>
    <option value="3160959">São Domingos das Dores</option>
    <option value="3161007">São Domingos do Prata</option>
    <option value="3161056">São Félix de Minas</option>
    <option value="3161106">São Francisco</option>
    <option value="3161205">São Francisco de Paula</option>
    <option value="3161304">São Francisco de Sales</option>
    <option value="3161403">São Francisco do Glória</option>
    <option value="3161502">São Geraldo</option>
    <option value="3161601">São Geraldo da Piedade</option>
    <option value="3161650">São Geraldo do Baixio</option>
    <option value="3161700">São Gonçalo do Abaeté</option>
    <option value="3161809">São Gonçalo do Pará</option>
    <option value="3161908">São Gonçalo do Rio Abaixo</option>
    <option value="3125507">São Gonçalo do Rio Preto</option>
    <option value="3162005">São Gonçalo do Sapucaí</option>
    <option value="3162104">São Gotardo</option>
    <option value="3162203">São João Batista do Glória</option>
    <option value="3162252">São João da Lagoa</option>
    <option value="3162302">São João da Mata</option>
    <option value="3162401">São João da Ponte</option>
    <option value="3162450">São João das Missões</option>
    <option value="3162500">São João del Rei</option>
    <option value="3162559">São João do Manhuaçu</option>
    <option value="3162575">São João do Manteninha</option>
    <option value="3162609">São João do Oriente</option>
    <option value="3162658">São João do Pacuí</option>
    <option value="3162708">São João do Paraíso</option>
    <option value="3162807">São João Evangelista</option>
    <option value="3162906">São João Nepomuceno</option>
    <option value="3162922">São Joaquim de Bicas</option>
    <option value="3162948">São José da Barra</option>
    <option value="3162955">São José da Lapa</option>
    <option value="3163003">São José da Safira</option>
    <option value="3163102">São José da Varginha</option>
    <option value="3163201">São José do Alegre</option>
    <option value="3163300">São José do Divino</option>
    <option value="3163409">São José do Goiabal</option>
    <option value="3163508">São José do Jacuri</option>
    <option value="3163607">São José do Mantimento</option>
    <option value="3163706">São Lourenço</option>
    <option value="3163805">São Miguel do Anta</option>
    <option value="3163904">São Pedro da União</option>
    <option value="3164001">São Pedro dos Ferros</option>
    <option value="3164100">São Pedro do Suaçuí</option>
    <option value="3164209">São Romão</option>
    <option value="3164308">São Roque de Minas</option>
    <option value="3164407">São Sebastião da Bela Vista</option>
    <option value="3164431">São Sebastião da Vargem Alegre</option>
    <option value="3164472">São Sebastião do Anta</option>
    <option value="3164506">São Sebastião do Maranhão</option>
    <option value="3164605">São Sebastião do Oeste</option>
    <option value="3164704">São Sebastião do Paraíso</option>
    <option value="3164803">São Sebastião do Rio Preto</option>
    <option value="3164902">São Sebastião do Rio Verde</option>
    <option value="3165206">São Thomé das Letras</option>
    <option value="3165008">São Tiago</option>
    <option value="3165107">São Tomás de Aquino</option>
    <option value="3165305">São Vicente de Minas</option>
    <option value="3165404">Sapucaí-Mirim</option>
    <option value="3165503">Sardoá</option>
    <option value="3165537">Sarzedo</option>
    <option value="3165560">Sem-Peixe</option>
    <option value="3165578">Senador Amaral</option>
    <option value="3165602">Senador Cortes</option>
    <option value="3165701">Senador Firmino</option>
    <option value="3165800">Senador José Bento</option>
    <option value="3165909">Senador Modestino Gonçalves</option>
    <option value="3166006">Senhora de Oliveira</option>
    <option value="3166105">Senhora do Porto</option>
    <option value="3166204">Senhora dos Remédios</option>
    <option value="3166303">Sericita</option>
    <option value="3166402">Seritinga</option>
    <option value="3166501">Serra Azul de Minas</option>
    <option value="3166600">Serra da Saudade</option>
    <option value="3166709">Serra dos Aimorés</option>
    <option value="3166808">Serra do Salitre</option>
    <option value="3166907">Serrania</option>
    <option value="3166956">Serranópolis de Minas</option>
    <option value="3167004">Serranos</option>
    <option value="3167103">Serro</option>
    <option value="3167202">Sete Lagoas</option>
    <option value="3165552">Setubinha</option>
    <option value="3167301">Silveirânia</option>
    <option value="3167400">Silvianópolis</option>
    <option value="3167509">Simão Pereira</option>
    <option value="3167608">Simonésia</option>
    <option value="3167707">Sobrália</option>
    <option value="3167806">Soledade de Minas</option>
    <option value="3167905">Tabuleiro</option>
    <option value="3168002">Taiobeiras</option>
    <option value="3168051">Taparuba</option>
    <option value="3168101">Tapira</option>
    <option value="3168200">Tapiraí</option>
    <option value="3168309">Taquaraçu de Minas</option>
    <option value="3168408">Tarumirim</option>
    <option value="3168507">Teixeiras</option>
    <option value="3168606">Teófilo Otoni</option>
    <option value="3168705">Timóteo</option>
    <option value="3168804">Tiradentes</option>
    <option value="3168903">Tiros</option>
    <option value="3169000">Tocantins</option>
    <option value="3169059">Tocos do Moji</option>
    <option value="3169109">Toledo</option>
    <option value="3169208">Tombos</option>
    <option value="3169307">Três Corações</option>
    <option value="3169356">Três Marias</option>
    <option value="3169406">Três Pontas</option>
    <option value="3169505">Tumiritinga</option>
    <option value="3169604">Tupaciguara</option>
    <option value="3169703">Turmalina</option>
    <option value="3169802">Turvolândia</option>
    <option value="3169901">Ubá</option>
    <option value="3170008">Ubaí</option>
    <option value="3170057">Ubaporanga</option>
    <option value="3170107">Uberaba</option>
    <option value="3170206">Uberlândia</option>
    <option value="3170305">Umburatiba</option>
    <option value="3170404">Unaí</option>
    <option value="3170438">União de Minas</option>
    <option value="3170479">Uruana de Minas</option>
    <option value="3170503">Urucânia</option>
    <option value="3170529">Urucuia</option>
    <option value="3170578">Vargem Alegre</option>
    <option value="3170602">Vargem Bonita</option>
    <option value="3170651">Vargem Grande do Rio Pardo</option>
    <option value="3170701">Varginha</option>
    <option value="3170750">Varjão de Minas</option>
    <option value="3170800">Várzea da Palma</option>
    <option value="3170909">Varzelândia</option>
    <option value="3171006">Vazante</option>
    <option value="3171030">Verdelândia</option>
    <option value="3171071">Veredinha</option>
    <option value="3171105">Veríssimo</option>
    <option value="3171154">Vermelho Novo</option>
    <option value="3171204">Vespasiano</option>
    <option value="3171303">Viçosa</option>
    <option value="3171402">Vieiras</option>
    <option value="3171600">Virgem da Lapa</option>
    <option value="3171709">Virgínia</option>
    <option value="3171808">Virginópolis</option>
    <option value="3171907">Virgolândia</option>
    <option value="3172004">Visconde do Rio Branco</option>
    <option value="3172103">Volta Grande</option>
    <option value="3172202">Wenceslau Braz</option>
    `;

  /*Carrega os campos com os Estados Brasileiros*/
  $.each(estadosBrasileiros.UF, function(key, value){
    $('.selectEstados').append('<option value="'+value.sigla+'">'+value.nome+'</option>');
  });
  /*Carrega os campos com as Cidades Brasileiras*/
  $(".selectCidades").append(cidadesBrasileiras);
  /*Criar o efeito nos campos Cidades*/
  $("select.flexselect").flexselect();

  var options = {
    dataType: 'text',
    url: 'https://probusca.com/painel/page/request_catta.php',
    beforeSubmit: validate,
    success: showResponse
  };

  $('form').ajaxForm(options);

  /*----------------------------FUNÇÕES AUXILIARES----------------------------------*/

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
|                                 ANTES DO SUBMIT                                             |
|                                                                                             |
|                                                                                             |
|---------------------------------------------------------------------------------------------*/

    /*Antes de submeter o formulário....*/
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
      respValidacao = validacao_consultaAtividadeEconomica(formData);

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
|---------------------------------------------------------------------------------------------*/

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

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                                  RESPOSTA DA REQUISIÇÃO AJAX                                |
|                                                                                             |
|                                                                                             |
|--------------------------------------------------------------------------------------------*/

function showResponse(resp)
{
      /*Esconde a tela de carregamento*/
      end_loading();

      try
      {
        console.log(resp);
        respostaConsulta = JSON.parse(resp);
      }
      catch(err)
      {
        return alert("Um erro ocorreu! Por favor, recarregue a página. Se o problema persistir, favor informar ao administrados do Sistema.");
      }

      /*Primeiramente, checa se não aconteceu erro de token ou de código*/
      if(typeof respostaConsulta['erros'] !== 'undefined' && respostaConsulta['erros'] == true)
      {
        return alert(respostaConsulta['message']);
      }

      /*Verifica se o resultado está vazio*/
      try
      {
        if(jQuery.isEmptyObject(respostaConsulta['retorno']['resultado']))
        {
          msg_nadaEncontrado.fadeIn(1000);
          return false;
        }
        }
        catch(err)
        {
        try
        {
          if(jQuery.isEmptyObject(respostaConsulta['resultados']))
        {
          msg_nadaEncontrado.fadeIn(1000);
          return false;
        }
        }catch(err2)
        {
          console.log(err2);
        }
        }

        geraTabela_consultaAtividadeEcon();

    }/*endFunction*/

/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                 FUNÇÃO PREPARA OS DADOS A SEREM APRESENTADOS AO USUÁRIO                     |
|                                                                                             |
|                                                                                             |
|--------------------------------------------------------------------------------------------*/

function carregaDadosPessoa(pessoa, type)
{
  let dados_telefonesAtualizados = [];
  let dados_telefonesAbsoletos = [];
  let dados_enderecosAtualizados = [];
  let dados_enderecosAbsoletos = [];

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

  $.each(pessoa['socios'], function(indice, item){
    $("#apr_identificacaoSocios_tabela").append(`<tr>
          <td colspan="2" id="apr_nome" class="tableTitle"></td>
        <tr>
          <tr>
            <td><b>CPF:</b> ${item['cpfCnpj']}</td>
            <td><b>Nome:</b> ${deixaMaiusculo(item['nome'])}</td>
            <td><b>Participação:</b> ${item['participacao']}%</td>
          <tr>`);
    });


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


/*--------------------------------------------------------------------------------------------|
|                                                                                             |
|                              MONITORAMENTO DE EVENTOS                                       |
|                                                                                             |
|                                                                                             |
---------------------------------------------------------------------------------------------*/


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

/*Evento: solicitação de mais dados*/
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
      geraTabela_consultaAtividadeEcon(true, d);
    })

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


};/*end*/



</script>
