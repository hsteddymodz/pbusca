<?php
die();
include('class/Notificacao.class.php');
include('class/Formatar.class.php');
include('class/Conexao.class.php');
include('class/Categoria.class.php');
$not = false;
$con       = new Conexao();
$router = new Router($_GET['p']);
if(!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 3)
  die("alert('Apenas revendedores podem recarregar suas contas.'); location.href='".URL."';");
if($_FILES){
  $uploaddir = 'comprovante';
  $extensao = substr($_FILES['userfile']['name'], -4);
  $nome = md5(time()) . $extensao;
  $uploadfile = $uploaddir . '/' . $nome;
  if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    $con->insert('comprovante', array('data'=>date('Y-m-d H:i:s'), 'usuario'=>$_SESSION['usuario'], 'arquivo'=>$nome));
    echo ("<script>alert('Arquivo enviado! Aguarde a liberação da conta.'); </script>");
  } else {
    echo ("<script>alert('Falha ao enviar arquivo'); </script>");
  }
  $con->insert('comprovante', array('data'=>date('Y-m-d H:i:s'), 'usuario'=>$_SESSION['usuario'], 'arquivo'=>$nome));
}
$conta_bancaria = $con->select('*')->from('conta_bancaria')->where(" usuario = '".$_SESSION['usuario']."' ")->executeNGet();
?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">     
  <div class="row">
    <ol class="breadcrumb">
      <li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
      <li><a href="<?php echo URL; ?>/">Início</a></li>
      <li class="active">Recarregar Conta</li>
    </ol>
  </div><!--/.row-->
  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header">Recarregar Conta</h1>
    </div>
  </div><!--/.row-->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            
            Sua conta ficou sem créditos ou venceu
            
          </div>
          <div class="panel-body">
            <div class="col-md-12">
              <h4>Conta Bancária</h4>
              <table class="table table-hover table-bordered">
             
              <?php foreach($conta_bancaria as $cb){ ?>
              <tr>
                  <td><b><?php echo $cb['banco']; ?></b></td>
                  <td>Ag: <?php echo $cb['agencia']; ?></td>
                  <td>Cc: <?php echo $cb['conta']; ?></td>
                  <td><?php if($cb['observacao'])  echo $cb['observacao']; ?></td>
                </tr>
              <?php } ?>
             </table>
            </div>
            
            <div class="col-md-12">
              <p>Para recarregar sua conta, realize o depósito na conta acima e envie o comprovante do pagamento à seguir.</p>
              
              <form action="" enctype="multipart/form-data" method="post">
                <div class="form-group form-inline">
                  <label for="">Valor da Recarga - R$ 40</label>
                  <input name="userfile" required class="form-control" type="file">
                </div>
                <div class="form-group">
                  <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
              </form>
                        
            </div>
            
            <div class="clearfix"></div>
            
          </div>
        </div>
      </div><!--/.row-->  
    </div><!--/.main-->
