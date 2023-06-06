<?php

include("class/protect.function.php");
protect(array(4));

$con         = new Conexao();
$router      = new Router($_GET['p']);

if(!$_SESSION)
	@session_start();

$infos = $con->select('*')->from('login')->where(" data_logout IS NOT NULL AND usuario = '{$_SESSION['usuario']}' ")->orderby('codigo DESC')->limit(1)->executeNGet();


$pla_codigo  = intval($router->param(0));
if($_POST['enviar']){

	$con->update('plataforma', array('nome'=>$_POST['nome'], 'quem_alterou' => $_SESSION['nome'], 'ip_alterou' => $infos['ip']), $pla_codigo);

	if($_FILES['novo_logo']['size'] > 0){

		if($_FILES['novo_logo']['size'] > 100000)
			echo("<script>alert('Tamanho do arquivo muito grande!');</script>");
		else {

			$uploaddir = 'upload/';
			$nome      = md5(time()).substr(basename($_FILES['novo_logo']['name']), -4);
			$uploadfile = $uploaddir . $nome;

			if (preg_match('/\b(\.jpg|\.JPG|\.png|\.PNG|\.gif|\.GIF|\.JPEG|\.jpeg)\b/', $uploadfile)) {
				$uploadfile = $uploaddir . $nome;
			} else {
				echo $uploadfile;
				echo "<br>";
				$newName = preg_replace('/\.(php|php4|php5|sh|py|js)/', md5(time()), $uploadfile);
				$uploadfile = $newName.'.'.md5(time());
				echo $uploadfile;
			}

			if (move_uploaded_file($_FILES['novo_logo']['tmp_name'], $uploadfile)) {

				$old_logo = $con->select('logo')->from('plataforma')->where("codigo = '$pla_codigo'")->limit(1)->executeNGet('logo');

				if(is_file('upload/'.$old_logo)) unlink('upload/'.$old_logo);

				$con->update('plataforma', array('logo'=>$nome, 'quem_alterou' => $_SESSION['nome'], 'ip_alterou' => $infos['ip']), $pla_codigo);
			    die("<script>alert('Novo logo enviado com sucesso!'); location.href='".URL."/plataforma';</script>");

			} else {
			    echo("<script>alert('Falha ao realizar o upload do arquivo');</script>");
			}
		}

	}else
		die("<script>alert('Plataforma alterada com sucesso!'); location.href='".URL."/plataforma';</script>");

}

/* variaveis de ambiente */
$nome_p      = "Editar Plataforma";


$dados       = $con->select('*')->from('plataforma')->where("codigo = '$pla_codigo'")->limit(1)->executeNGet();

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active"><?php echo $nome_p; ?></li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header"><?php echo $nome_p; ?></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							
						</div>
					</form>
				</div>
				<div class="panel-body">
					<div class="col-xs-4">

						<form action="" method="post" enctype="multipart/form-data">

							<div class="form-group">
								<label for="">Nome</label>
								<input type="text" value="<?php echo $dados['nome']; ?>" required class="form-control" name="nome">
							</div>

							<div class="form-group">
								<label for="">Enviar novo logo</label>
								<input type="file" class="form-control" name="novo_logo">
							</div>

							<div class="form-group">
								<button type="submit" name="enviar" value="true" class="btn btn-success">Enviar</button>
							</div>

						</form>

					</div>

					<div class="col-xs-4">
						<label for="">Logo Atual</label>
						<img class="img-responsive" src="<?php echo URL.'/upload/'.$dados['logo']; ?>">
					</div>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->