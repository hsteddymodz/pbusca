<?php

include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Conexao.class.php');
include('class/Categoria.class.php');

$not = false;
$con       = new Conexao();

$router = new Router($_GET['p']);

if($_SESSION['tipo'] == 2){

  // se for admin, ativa automaticamente
  $con->update('usuario', array('inativo' => 0), $router->param(0));

  $tipo = $con->select('tipo')->from("usuario")->where("codigo = '".$router->param(0)."'")->limit(1)->executeNGet('tipo');

  if($tipo == 3)
    die('<script>alert("Usuário ativado!"); location.href="'.URL.'/revendedor";</script>');
  elseif($tipo == 1)
    die('<script>alert("Usuário ativado!"); location.href="'.URL.'/usuario";</script>');
  elseif($tipo == 2)
    die('<script>alert("Usuário ativado!"); location.href="'.URL.'/administrador";</script>');

}

$plano = $con->select('*')->from('plano')->where("codigo in (select plano from usuario where codigo = '".$router->param(0)."')")->limit(1)->executeNGet();

?>


<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">     
  <div class="row">
    <ol class="breadcrumb">
      <li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
      <li><a href="<?php echo URL; ?>/usuario">Usuário</a></li>
      <li class="active">Ativar Conta</li>
    </ol>
  </div><!--/.row-->

  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header">Ativar Conta</h1>
    </div>
  </div><!--/.row-->


  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading">

          <button type="button" onclick="if(confirm('Tem certeza?')) location.href='<?php echo URL; ?>/usuario';" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Cancelar</button>
          
          
        </div>
        <div class="panel-body">
          

          <div class="col-md-12">
            <?php if($not) $not->show(); ?>
          </div>


          <div class="col-md-8">

            <p>Para ativar a conta deste usuário, realize o pagamento do plano na conta bancária abaixo e envie o comprovante para: probuscadobrasil@gmail.com</p>

            <p>Valor: <b>R$ <?php echo number_format($plano['preco'], 2, ',', '.'); ?></b></p>
            <p>Banco:</p>
            <p>Conta:</p>
            <p>Agência:</p>

            
                      
          </div>
          

          <div class="clearfix"></div>
          

        </div>
      </div>
    </div><!--/.row-->  
  </div><!--/.main-->