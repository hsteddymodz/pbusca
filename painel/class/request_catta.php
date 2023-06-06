<?php

//Mostra todos os erros
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

// verificamos se o usuario ainda esta logado
if(!isset($_SESSION)) @session_start();

include('../class/Conexao.class.php');
include('../class/RegistrarConsulta.php');
include('../class/LimitarConsulta.function.php');
include('../class/onlyNumbers.function.php');
include('../class/Catta.class.php');

//Resultado da Pesquisa
$resp = false;


if(!isset($_POST['tipoRequisicao']) || !isset($_POST['token']))
{
  echo json_encode(array('erros'=>true, 'message'=>'Parâmetros necessários inválidos!'));
  exit();
}

//Valida Token recebido
include($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'painel/class/Token.class.php');
$token = new Token();
$token = $token->get_token();

if($_POST['token'] != $token)
{
  echo json_encode(array('erros'=>true, 'message'=>'Token inválido! Por favor, atualiza a página.'));
  exit();
}

try {
  
  //Obtém os campos vindos do $_POST
  $nome = isset($_POST['nome']) ? $nome = $_POST['nome'] : $nome = "";
  $doc = isset($_POST['doc']) ? $_POST['doc'] : "";
  $uf = isset($_POST['uf']) ? $uf = $_POST['uf'] : $uf = "MG";
  $cep = isset($_POST['cep']) ? $_POST['cep'] : "";
  $cidade = isset($_POST['cidade']) ? $cidade = $_POST['cidade'] : $cidade = 0;
  $bairro = isset($_POST['bairro']) ? $_POST['bairro'] : "";
  $endereco = isset($_POST['endereco']) ? $_POST['endereco'] : "";
  $numero = isset($_POST['numero']) ? $_POST['numero'] : "";
  $telefone = isset($_POST['telefone']) ? $_POST['telefone'] : "";
  $dataNascimento = isset($_POST['dataNascimento']) ? $_POST['dataNascimento'] : "";
  $atividade = isset($_POST['atividade']) ? $_POST['atividade'] : "";

  $deslocamento = 40;



}catch(Exception $e)
{
  echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
  exit();
}

$catta = new Catta();

try
{
  //Verifica o tipo de Pesquisa solicitado pelo usuário
  switch ($_POST['tipoRequisicao'])
  {
  case 'ConsultaCpfCnpj':
     $resp = $catta->ConsultaCpfCnpj($doc);
  break;
  case 'ConsultaNome':
     $resp = $catta->ConsultaNome($uf, $nome, "todas",$deslocamento,$endereco,$bairro,$cidade);
  break;
  case 'ConsultaTelefone':
     $resp = $catta->ConsultaTelefone($telefone, $uf, $cidade,$deslocamento);
  break;
  case 'ConsultaMae':
     $resp = $catta->ConsultaMae($nome, $uf, $cidade, $deslocamento);
  break;
  case 'ConsultaNascimento':
     $dataNascimento = explode('/' ,$dataNascimento);
     $ano = $dataNascimento[2];
     $mes = $dataNascimento[1];
     $dia = $dataNascimento[0];
     $resp = $catta->ConsultaNascimento($dia, $mes, $ano, $uf, $deslocamento,$cidade,$endereco,$bairro);
  break;
  case 'ConsultaSocio':
     $resp = $catta->ConsultaSocio($nome, $cidade, $uf, $deslocamento);
  break;
  case 'ConsultaAtividadeEcon':
     $resp = $catta->ConsultaAtividadeEcon($atividade, $uf, $cidade, $deslocamento, $endereco, $bairro);
  break;
  case 'ConsultaEndereco':
     $resp = $catta->ConsultaEndereco($uf, $cep, $numero, $complemento, $bairro, $cidade, $deslocamento);
  break;
  default:
     echo json_encode(array('erros'=>true, 'message'=>'Tipo de requisição inválida: '.$_POST['tipoRequisicao']));
     exit();
  break;
  }
}catch(Exception $e)
{
  echo json_encode(['error'=>true, 'message'=>$e->getMessage()]);
  exit();
}

//Se a consulta retornar resultado válido, registra a consulta
$response = json_decode($resp, true);
if(isset($response['retorno']['resultado']) && !empty($response['retorno']['resultado']))
{
  registrarConsulta(null, $_SESSION['usuario'] , 'catta');
}
elseif(isset($response['resultados']) && !empty($response['resultados']))
{
  registrarConsulta(null, $_SESSION['usuario'] , 'catta');
}

echo $resp;
