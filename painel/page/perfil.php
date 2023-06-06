<?php
die();
include("class/protect.function.php");
protect(array(3));

$con = new Conexao();

if($_POST['enviar']){

	$insert = array();
	$insert['whatsapp'] = $_POST['whatsapp'];
	$insert['skype'] = $_POST['skype'];
	$insert['email'] = $_POST['email'];
	$insert['conteudo'] = $_POST['conteudo'];
	$insert['usuario'] = $_SESSION['usuario'];

	if($_FILES['banner']){

		$extensao = substr($_FILES['banner']['name'], strlen($_FILES['banner']['name'])-4,  strlen($_FILES['banner']['name']));
		$nome = md5(time()) . $extensao;
		$uploadfile =  'capa/' . $nome;

		if(in_array($extensao, aray('.jpg', '.png', '.gif'))){
			die("<script>alert('Extensão de arquivo não permitida!');</script>");
		}else if (move_uploaded_file($_FILES['banner']['tmp_name'], $uploadfile)) {
		    $insert['capa'] = $nome;
		}
	}

	// verifica se ja existe perfil criado
	$existePerfil = $con->select('codigo')->from('perfil')->where("usuario = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('codigo');

	if($existePerfil)
		$con->update('perfil', $insert, $existePerfil);
	else
		$con->insert('perfil', $insert);

}

if($_SESSION['tipo'] == 3)
	$dados = $con->select('*')->from('perfil')->where("usuario = '".$_SESSION['usuario']."'")->limit(1)->executeNGet();
else{
	$dados = array();
	die("<script>alert('Apenas revendedores podem criar perfis.'); location.href='".URL."';</script>");
}
if(is_file('capa/'.$dados['capa']))
	$dados['capa'] = 'capa/'.$dados['capa'];
else
	$dados['capa'] = '../img/bg-banner.jpg';

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Meu Perfil</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Meu Perfil</h1>
		</div>
	</div><!--/.row-->
	
	<form action="" enctype="multipart/form-data" method="post">

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					
						<div class="form-group form-inline">
							<button type="submit"  name="enviar" value="true" onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>';" class="btn btn-primary btn-success btn-xs">
								<i class="glyphicon glyphicon-floppy-disk"></i> Salvar
							</button>

						</div>
					
				</div>
				<div class="panel-body">

					<div class="col-xs-4">

						<div class="form-group">
							<label for="">Whatsapp</label>
							<input type="text" value="<?php echo $dados['whatsapp']; ?>" name="whatsapp" class="form-control">
						</div>

						<div class="form-group">
							<label for="">Skype</label>
							<input type="text" value="<?php echo $dados['skype']; ?>" name="skype" class="form-control">
						</div>

						<div class="form-group">
							<label for="">E-mail</label>
							<input type="email" value="<?php echo $dados['email']; ?>" name="email" class="form-control">
						</div>
						
						
					</div>
					<div class="col-xs-8">
						
						<div class="form-group">
							<label for="">Capa Atual</label>
							<img class="img-responsive" src="<?php echo $dados['capa']; ?>" alt="">
						</div>

						<div class="form-group">
							<label for="">Capa</label>
							<p><small>O tamanho da capa deve ser 1920 x 404 pixels</small></p>
							<input name="banner" type="file" class="form-control">

						</div>

						
					</div>

					<div class="clearfix"></div>

					<div class="col-xs-12">
						<div class="form-group">
							<label for="">Conteúdo</label>
							<textarea name="conteudo" class="form-control wysig" id="conteudo" cols="30" rows="5"><?php echo $dados['conteudo']; ?></textarea>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div><!--/.row-->	

	</form>


</div><!--/.main-->