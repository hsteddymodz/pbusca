<?php

/** Report de Erros */
error_reporting(E_ALL);
ini_set("display_errors",1);

if (!isset($_SESSION)) @session_start();

/** Classes de segurança e conexão */
include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');
include('Conexao.class.php');

// Validações de segurança
$con = new Conexao();
$usr = $_SESSION['usuario'];
if (!$usr || $usr <= 0) {
  echo json_encode('{"success":"false", "response":"Você precisa estar logado para fazer isso"}', 1);
  exit();
}
// Fim das validações de segurança

class LocalizacaoAPI
{

  private $authorization_token;
  private $api_assertiva;

  function __construct()
  {
    include('get_config_info.function.php');
    $ini_file  = get_config_info();
    $credenciais_localize = $ini_file['localize'];
    
    $this->authorization_token = $credenciais_localize['token'];
    $this->api_assertiva = $credenciais_localize['url'];


  }

  /** Consultas podem ser realizadas através dos seguintes parâmetros
     * @param CPF
     * @param CNPJ
     * @param Telefone
     * @param Email
     * @param Nome ou Endereço
    */
  function buscaAssertiva($cpf = null, $email = null, $cnpj = null, $telefone = null, $nome_endereco = null)
  {
    $curl = curl_init();

    if ($cpf != null)
      $params = http_build_query(array('cpf' => $cpf));

    if ($email != null)
      $params = http_build_query(array('email' => $email));

    if ($cnpj != null)
      $params = http_build_query(array('cnpj' => $cnpj));

    if ($telefone != null)
      $params = http_build_query(array('telefone' => $telefone));

    if ($nome_endereco != null)
      $params = http_build_query($nome_endereco);
    

    curl_setopt_array($curl, array(
      CURLOPT_URL => $this->api_assertiva,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $params,
      CURLOPT_HTTPHEADER => array(
        "Authorization: " . $this->authorization_token,
        "Postman-Token: f6b4abfe-eb95-4aea-8019-1e795dd3b03a",
        "cache-control: no-cache",
        "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      return $response;
    }
  }
}

/** BUSCA POR CPF */
if (isset($_POST['cpf'])) {

  //Verificacao de seguranca
  if (limitarConsulta($con, $_SESSION['usuario'], 'localiza_a', 1) <= 0)
    die('{"success":"false", "response":"Créditos Insuficientes"}');

  $cpf = $_POST['cpf'];
  $class = new LocalizacaoAPI;
  $busca = $class->buscaAssertiva($cpf, null);
  // Registrar Consulta
  if (isset($busca) && !empty($busca)) registrarConsulta($con, $_SESSION['usuario'], 'localiza_a');
  die($busca);

}

/** BUSCA POR EMAIL */
if (isset($_POST['email'])) {

  //Verificacao de seguranca
  if (limitarConsulta($con, $_SESSION['usuario'], 'localiza_a', 1) <= 0)
    die('{"success":"false", "response":"Créditos Insuficientes"}');

  $email = $_POST['email'];
  $class = new LocalizacaoAPI;
  $busca = $class->buscaAssertiva(null, $email);
  // Registrar Consulta
  if (isset($busca) && !empty($busca)) registrarConsulta($con, $_SESSION['usuario'], 'localiza_a');
  die($busca);

}

/** BUSCA POR CNPJ */
if (isset($_POST['cnpj'])) {

  //Verificacao de seguranca
  if (limitarConsulta($con, $_SESSION['usuario'], 'localiza_a', 1) <= 0)
    die('{"success":"false", "response":"Créditos Insuficientes"}');

  $cnpj = $_POST['cnpj'];
  $class = new LocalizacaoAPI;
  $busca = $class->buscaAssertiva(null, null, $cnpj);
  // Registrar Consulta
  if (isset($busca) && !empty($busca)) registrarConsulta($con, $_SESSION['usuario'], 'localiza_a');
  die($busca);
}

/** BUSCA POR TELEFONE */
if (isset($_POST['telefone'])) {

  //Verificacao de seguranca
  if (limitarConsulta($con, $_SESSION['usuario'], 'localiza_a', 1) <= 0)
    die('{"success":"false", "response":"Créditos Insuficientes"}');

  $telefone = $_POST['telefone'];
  $class = new LocalizacaoAPI;
  $busca = $class->buscaAssertiva(null, null, null, $telefone);
  // Registrar Consulta
  if (isset($busca) && !empty($busca)) registrarConsulta($con, $_SESSION['usuario'], 'localiza_a');
  die($busca);

}

/** BUSCA POR NOME OU ENDEREÇO */
if (
  $_POST['bairro'] || $_POST['cidade'] || $_POST['complemento'] || $_POST['dataNascimento'] || $_POST['enderecoOuCep'] ||
  $_POST['nome'] || $_POST['numeroFinal'] || $_POST['numeroInicial'] || $_POST['sexo'] || $_POST['uf']
) { 
  //Verificacao de seguranca
  if (limitarConsulta($con, $_SESSION['usuario'], 'localiza_a', 1) <= 0)
    die('{"success":"false", "response":"Créditos Insuficientes"}');

  $nome_endereco = array(
    'bairro' => $_POST['bairro'],
    'cidade' => $_POST['cidade'],
    'complemento' => $_POST['complemento'],
    'dataNascimento' => $_POST['dataNascimento'],
    'enderecoOuCep' => $_POST['enderecoOuCep'],
    'nome' => $_POST['nome'],
    'numeroFinal' => $_POST['numeroFinal'],
    'numeroInicial' => $_POST['numeroInicial'],
    'sexo' => $_POST['sexo'],
    'uf' => $_POST['sexo']
  );

  $class = new LocalizacaoAPI;
  $busca = $class->buscaAssertiva(null, null, null, null, $nome_endereco);
  // Registrar Consulta
  if (isset($busca) && !empty($busca)) registrarConsulta($con, $_SESSION['usuario'], 'localiza_a');
  die($busca);

}



