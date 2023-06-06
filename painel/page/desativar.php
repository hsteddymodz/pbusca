<?php
include('class/Conexao.class.php');

$not = false;
$con = new Conexao();

$router = new Router($_GET['p']);

if ($router->param(0) > 0) {

  if ($_GET['motivo_desativacao']) {
    $motivo_desativacao = $_GET['motivo_desativacao'];
    if (strlen($motivo_desativacao) === 0) {
      $motivo_desativacao = NULL;
    }
  } 

  if ($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4) {

    // se for admin, ativa automaticamente
    $tipo = $con->select('tipo, teste')->from("usuario")->where("codigo = '" . intval($router->param(0)) . "'")->limit(1)->executeNGet();

    $con->update('usuario', array('inativo' => 1, 'motivo_desativacao' => $motivo_desativacao), intval($router->param(0)));

    if ($tipo['tipo'] == 2 && $_SESSION['tipo'] == 4) {
      // se um administrador comum é desativado, todos seus usuarios tambem sao
      $con->execute("update usuario set inativo = 1 where administrador = '" . intval($router->param(0)) . "'");
    }

    if($_GET['crawler']) {
      die('<script>alert("Usuário desativado. \nLembre-se de alterar o plano do usuário para um sem nenhum crédito!"); location.href="' . URL . '/relatorio_crawler";</script>');
    }

    if ($tipo['teste']) {

      die('<script>alert("Usuário desativado. \nLembre-se de alterar o plano do usuário para um sem nenhum crédito!"); location.href="' . URL . '/usuario_teste";</script>');
    } elseif ($tipo['tipo'] == 3) {

      die('<script>alert("Usuário desativado. \nLembre-se de alterar o plano do usuário para um sem nenhum crédito!"); location.href="' . URL . '/revendedor";</script>');
    } elseif ($tipo['tipo'] == 1) {

      die('<script>alert("Usuário desativado. \nLembre-se de alterar o plano do usuário para um sem nenhum crédito!"); location.href="' . URL . '/usuario";</script>');
    } elseif ($tipo['tipo'] == 2) {
      // significa que um administrador foi desativado
      die('<script>alert("Usuário desativado. \nLembre-se de alterar o plano do usuário para um sem nenhum crédito!"); location.href="' . URL . '/administrador";</script>');
    }
  }
}





 