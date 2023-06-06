<?php

/** Report de Erros */
error_reporting(E_ALL);
ini_set("display_errors", 1);

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


class TrackerV2 {

  private $email;
  private $senha;
  private $ip; 

  function __construct()
  {
    $this->email = 'probusca2@gmail.com';
    $this->senha = 'L3lO8eYI';
    $this->ip = '143.255.114.234';
    /*
    include('get_config_info.function.php');
    $ini_file  = get_config_info();
    $credenciais_trackerv2 = $ini_file['trackerv2'];
    $this->email = $credenciais_trackerv2['email'];
    $this->senha = $credenciais_trackerv2['senha'];
    $this->ip = $credenciais_trackerv2['ip'];
    */
  }

  function buscaCadastroUnico($parametros) {

    $params = http_build_query(
      array(
      'ip' => $this->ip,
      'email' => $this->email,
      'senha' => $this->senha,
      'tipo' => $parametros['tipo'],
      'id' => $parametros['id'],
    ));
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://23.239.119.67/api/busca.php",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $params,
      CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "Content-Type: application/x-www-form-urlencoded",
        "Host: 23.239.119.67",
        "Postman-Token: e87de80b-a9d1-4f51-8e2e-96859269467e,6580108c-ec7d-4f9f-8c35-69b8c33ac1ce",
        "User-Agent: PostmanRuntime/7.11.0",
        "accept-encoding: gzip, deflate",
        "cache-control: no-cache",
        "cookie: PHPSESSID=k8k7sraph66bi8mgkhsdl48md1"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      return json_encode($response);
    }
  }

  function buscaTracker($parametros) {
    //Verificações e página inicial = 0
    if(!isset($parametros['pag'])) 
      $parametros['pag'] = 0;
    if (!isset($parametros['nome'])) 
      $parametros['nome'] = '';
    if (!isset($parametros['cpf'])) 
      $parametros['cpf'] = '';
    if (!isset($parametros['cnpj'])) 
      $parametros['cnpj'] = '';
    if (!isset($parametros['titulo_de_eleitor'])) 
      $parametros['titulo_de_eleitor'] = '';
    if (!isset($parametros['rg'])) 
      $parametros['rg'] = '';
    if (!isset($parametros['cns'])) 
      $parametros['cns'] = '';
    if (!isset($parametros['pis'])) 
      $parametros['pis'] = '';
    if (!isset($parametros['telefome'])) 
      $parametros['telefone'] = '';
    if (!isset($parametros['rua'])) 
      $parametros['rua'] = '';
    if (!isset($parametros['numero'])) 
      $parametros['numero'] = '';
    if (!isset($parametros['cidade'])) 
      $parametros['cidade'] = '';
    if (!isset($parametros['uf'])) 
      $parametros['uf'] = '';


    $params = http_build_query(
      array(
      'ip' => $this->ip,
      'email' => $this->email,
      'senha' => $this->senha,
      'pag' => $parametros['pag'],
      'nome' => $parametros['nome'],
      'cpf' => $parametros['cpf'],
      'cnpj' => $parametros['cnpj'],
      'titulo_de_eleitor' => $parametros['titulo_de_eleitor'],
      'rg' => $parametros['rg'],
      'cns' => $parametros['cns'],
      'pis' => $parametros['pis'],
      'telefone' => $parametros['telefone'],
      'rua' => $parametros['rua'],
      'numero' => $parametros['numero'],
      'cidade' => $parametros['cidade'],
      'uf' => $parametros['uf'],
      'gravar' => '1'
    ));
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://23.239.119.67/z_contatos.php",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $params,
      CURLOPT_HTTPHEADER => array(
        "Content-Type: application/x-www-form-urlencoded",
        "Postman-Token: 374b3890-d92a-4bb3-98cc-750c7b8e1fd2",
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return "cURL Error #:" . $err;
    } else {
      $response = str_replace("z_contato('143.255.114.234', 'probusca2@gmail.com', 'L3lO8eYI'", "z_contato('56775747250570', 'hswkse5uakj6zb', 'jkh5gpag4n3735'", $response);
      return json_encode($response);
    }
  }
}

if (isset($_POST['data'])) {

  //Verificacao de seguranca
  if (limitarConsulta($con, $_SESSION['usuario'], 'trackerv2', 1) <= 0)
    die('{"success":"false", "response":"Créditos Insuficientes"}');

  $data = json_decode($_POST['data']);
  $parametros = array(
    'pag' => $data->pag,
    'nome' => $data->nome,
    'cpf' => $data->cpf,
    'cnpj' => $data->cnpj,
    'titulo_de_eleitor' => $data->titulo_de_eleitor,
    'rg' => $data->rg,
    'cns' => $data->cns,
    'pis' => $data->pis,
    'telefone' => $data->telefone,
    'rua' => $data->rua,
    'numero' => $data->numero,
    'cidade' => $data->cidade,
    'uf' => $data->uf
  );

  $class = new TrackerV2;
  $busca = $class->buscaTracker($parametros);
  if (isset($busca) && !empty($busca)) registrarConsulta($con, $_SESSION['usuario'], 'trackerv2');
  die($busca); 
}


if (isset($_POST['cadUnico'])) {
  //Verificacao de seguranca
  if (limitarConsulta($con, $_SESSION['usuario'], 'trackerv2', 1) <= 0)
    die('{"success":"false", "response":"Créditos Insuficientes"}');

  $data = json_decode($_POST['cadUnico']);
  $parametros = array(
    'ip' => $data->ip,
    'email' => $data->email,
    'senha' => $data->senha,
    'tipo' => $data->tipo,
    'id' => $data->id,
  );

  $class = new TrackerV2;
  $busca = $class->buscaCadastroUnico($parametros);
  if (isset($busca) && !empty($busca)) registrarConsulta($con, $_SESSION['usuario'], 'trackerv2');
  die($busca);
}