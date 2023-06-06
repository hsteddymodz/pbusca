<?php

// verificamos se o usuario ainda esta logado
if(!isset($_SESSION)) @session_start();
if(!$_SESSION['usuario'] || !isset($_POST))
  die('ACESSO NEGADO');

//Inclui os arquivos externos necessários
include('../class/RegistrarConsulta.php');
include('../class/LimitarConsulta.function.php');
include('../class/onlyNumbers.function.php');
include('../class/Nett.class.php');
include('../class/Token.class.php');


//Valida Token recebido
/*
$token = new Token();
$token = $token->get_token();
if($_POST['token'] != $token)
{
  echo "TOKEN INVALIDO";
  exit();
}
*/


$nett = new Nett();

$resultado_da_pesquisa = false;

if(!isset($_POST['tipoRequisicao']))
{
  exit();
}

//Verifica o tipo de Pesquisa solicitado pelo usuário
switch ($_POST['tipoRequisicao'])
{

 case 'ConsultaCPF':
    $nett->doLogin();
    $resp = $nett->ConsultaCPF($_POST['cpf']);
    $resultado_da_pesquisa = file_get_contents($resp['result']);
 break;
 case 'ConsultaNome':
    $page = isset($_POST['page']) ? $_POST['page'] : "1";
    $nome = isset($_POST['nome']) ? $_POST['nome'] : null;
    $uf = isset($_POST['uf']) ? $_POST['uf'] : "mg";
    $nett->doLogin();
    $resp = $nett->ConsultaNomePT2((string)$page, $nome, $uf);
    if(!is_array($resp))
    {
      $resultado_da_pesquisa = $resp;
    }
    else
    {
      $resultado_da_pesquisa = json_encode($resp['data']);
    }
 break;
 case 'ConsultaCNPJ':
    $nett->doLogin();
    $resp = $nett->ConsultaCNPJ($_POST['cnpj']);
    $resultado_da_pesquisa = file_get_contents($resp['data']);
 break;
 case 'ConsultaTelefone':
    $nett->doLogin();
    $resultado_da_pesquisa = $nett->ConsultaTelefone($_POST['telefone']);
 break;
 case 'ConsultaCEP':
    $cep = isset($_POST['endereco']) ? $_POST['endereco'] : $_POST['cep'];
    $numInicio = isset($_POST['numInicio']) ? $_POST['numInicio'] : null;
    $numFinal = isset($_POST['numFinal']) ? $_POST['numFinal'] : null;
    $page = isset($_POST['page']) ? $_POST['page'] : 1;
    $nett->doLogin();
    $resp = $nett->ConsultaCEP($cep, $numInicio, $numFinal, $page);
    $resultado_da_pesquisa = $resp['result'];
 break;
 case 'getCidades':
    $resultado_da_pesquisa = $nett->getCidades($_POST['uf']);
 break;
 case 'getRuas':
    $resultado_da_pesquisa = $nett->getRuas2($_POST['uf'], $_POST['cidade'], $_POST['term']);
 break;
 default:
    throw new Exception('Tipo de consulta inválida: ' . $tipoRequisicao);
 break;
}

//Se a consulta retornar resultado válido, registra a consulta
if(preg_match("/Undefined index: Nattlogin/", $resultado_da_pesquisa))
{
}
elseif(preg_match("/You don't have permission to access/", $resultado_da_pesquisa))
{
}
elseif(preg_match("/NOT FOUND/", $resultado_da_pesquisa))
{
}
else
{
  registrarConsulta(null, $_SESSION['usuario'] , 'nett');
}


echo $resultado_da_pesquisa;
