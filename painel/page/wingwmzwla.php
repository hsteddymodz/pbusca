<?php

include("class/protect.function.php");
include("class/get_config_info.function.php");
protect(array(4));

/* variaveis de ambiente */
$nome_p = " ";

if($_POST['action']){

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://servidor.probusca.com/configuration');
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_USERPWD, '54.94.171.46:k5wzDMqFSR4udgT4QZWBNqYETKr2FRnfF8PR1FKdVK' );

	$server_output = curl_exec($ch);
	if(curl_errno($ch)) die(curl_error($ch));

	$resultado = json_decode($server_output, 1);

	if(isset($resultado['error']))
		echo "<script>alert('Falha: {$resultado['error']}');</script>";
	else
		echo "<script>alert('{$resultado['msg']}');</script>";

}

$ini_file = get_config_info();

$credenciais_boa_vista = $ini_file['boavista'];
$credenciais_cadsus    = $ini_file['cadsus'];
$credenciais_seekloc   = $ini_file['seekloc'];
$credenciais_cnh       = $ini_file['cnh'];
$credenciais_spc       = $ini_file['spc'];
$credenciais_score     = $ini_file['score'];
$credenciais_hiscon    = $ini_file['hiscon'];
$credenciais_cnis      = $ini_file['cnis'];
$credenciais_hiscon2   = $ini_file['hiscon2'];
$credenciais_credilink = $ini_file['credilink'];
$credenciais_intouch   = $ini_file['intouch'];

$credenciais_catta = $ini_file['catta'];
$credenciais_localize = $ini_file['localize'];

if($_SESSION['autorizado'] OR (isset($_POST['senha']) && $_POST['senha'] == 'f6G6dFo3sQ53')){
	$_SESSION['autorizado'] = 1;
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

	<form action="" method="post">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="form-group form-inline">
						<button class="btn btn-success" name="action" value="update" type="submit">Salvar</button>
					</div>
				</div>
				<div class="panel-body">


					<input type="hidden" name="action" value="update">

					<div class="col-sm-3">

						<div class="form-group">
							<h3>Credenciais do Boa Vista</h3>
						</div>

						<div class="form-group">
							<label for="">Usuário</label>
							<input type="text" class="form-control" value="<?= $credenciais_boa_vista['usuario']; ?>" required name="usuario_bv">
						</div>

						<div class="form-group">
							<label for="">Senha</label>
							<input type="text" class="form-control" value="<?= $credenciais_boa_vista['senha']; ?>" required name="senha_bv">
						</div>

						<div class="form-group">
							<label for="">Nome da Empresa</label>
							<input type="text" class="form-control" value="<?= $credenciais_boa_vista['nome_empresa_bv']; ?>" required name="nome_empresa_bv">
						</div>

						<div class="form-group">
							<label for="">Proxy</label>
							<input type="text" class="form-control" value="<?= $credenciais_boa_vista['proxy']; ?>" required name="proxy">
						</div>

						

					</div>

					<div class="col-sm-3">

						<div class="form-group">
							<h3>Credenciais InTouch</h3>
						</div>



						<div class="form-group">
							<label for="">Nome</label>
							<input type="text" class="form-control" value="<?= $credenciais_intouch['usuario']; ?>" name="in_usuario">
						</div>

						<div class="form-group">
							<label for="">Senha</label>
							<input type="text" class="form-control" value="<?= $credenciais_intouch['senha']; ?>" name="in_senha">
						</div>

						<div class="form-group">
							<label for="">Empresa</label>
							<input type="text" class="form-control" value="<?= $credenciais_intouch['empresa']; ?>" name="in_empresa">
						</div>

						<div class="form-group">
							<label for="">Proxy</label>
							<input type="text" class="form-control" value="<?= $credenciais_intouch['proxy']; ?>" name="in_proxy">
						</div>

						
					</div>

					<div class="col-sm-3">

						<div class="form-group">
							<h3>Credenciais Seekloc</h3>
						</div>

						<div class="form-group">
							<label for="">Nome</label>
							<input type="text" class="form-control" value="<?= $credenciais_seekloc['nome']; ?>" required name="nome_sl">
						</div>

						<div class="form-group">
							<label for="">Senha</label>
							<input type="password" class="form-control" value="<?= $credenciais_seekloc['senha']; ?>" required name="senha_sl">
						</div>

						<div class="form-group">
							<label for="">Empresa</label>
							<input type="text" class="form-control" value="<?= $credenciais_seekloc['empresa']; ?>" required name="empresa_sl">
						</div>

						<div class="form-group">
							<label for="">Proxy</label>
							<input type="text" class="form-control" value="<?= $credenciais_seekloc['proxy']; ?>" required name="proxy_sl">
						</div>

						
					</div>

					<div class="col-sm-3">

						<div class="form-group">
							<h3>Credenciais SPC</h3>
						</div>

						<div class="form-group">
							<label for="">Usuário</label>
							<input type="text" class="form-control" value="<?= $credenciais_spc['usuario']; ?>" required name="usuario_spc">
						</div>

						<div class="form-group">
							<label for="">Senha</label>
							<input type="text" class="form-control" value="<?= $credenciais_spc['senha']; ?>" required name="senha_spc">
						</div>

						<div class="form-group">
							<label for="">Frase</label>
							<input type="text" class="form-control" value="<?= $credenciais_spc['frase']; ?>" required name="frase_spc">
						</div>

						
					</div>

					<div class="col-sm-4">

						<div class="form-group">
							<h3>Link Crawler CNH</h3>
						</div>

						<div class="form-group">
							<label for="">Link</label>
							<input type="url" class="form-control" value="<?= $credenciais_cnh['link']; ?>" required name="link_cnh">
						</div>
						
					</div>


					<div class="col-sm-4">

						<div class="form-group">
							<h3>Link Pesquisa Score</h3>
						</div>

						<div class="form-group">
							<label for="">Link</label>
							<input type="url" class="form-control" value="<?= $credenciais_score['link']; ?>" required name="link_score">
						</div>
						
					</div>

					<div class="col-sm-4">

						<div class="form-group">
							<h3>Link HISCON</h3>
						</div>

						<div class="form-group">
							<label for="">Link</label>
							<input type="url" class="form-control" value="<?= $credenciais_hiscon['link']; ?>" required name="link_hiscon">
						</div>
						
					</div>

					<div class="col-sm-4">

						<div class="form-group">
							<h3>Link e Chave Crawler CNIS</h3>
						</div>

						<div class="form-group">
							<label for="">Link</label>
							<input type="url" class="form-control" value="<?= $credenciais_cnis['link']; ?>" required name="link_cnis">
						</div>
						<div class="form-group">
							<label for="">Chave</label>
							<input type="password" class="form-control" value="<?= $credenciais_cnis['chave']; ?>" required name="chave_cnis">
						</div>

					</div>

					<div class="col-sm-4">

						<div class="form-group">
							<h3>Link e Chave Hiscon 2</h3>
						</div>

						<div class="form-group">
							<label for="">Link</label>
							<input type="url" class="form-control" value="<?= $credenciais_hiscon2['link']; ?>" required name="link_hiscon2">
						</div>
						<div class="form-group">
							<label for="">Chave</label>
							<input type="password" class="form-control" value="<?= $credenciais_hiscon2['chave']; ?>" required name="chave_hiscon2">
						</div>

					</div>

					<div class="col-sm-4">

						<div class="form-group">
							<h3>Catta</h3>
						</div>

						<div class="form-group">
							<label for="">Usuário</label>
							<input type="text" class="form-control" value="<?= $credenciais_catta['usuario']; ?>" required name="usuario_catta">
						</div>
						<div class="form-group">
							<label for="">Senha</label>
							<input type="text" class="form-control" value="<?= $credenciais_catta['senha']; ?>" required name="senha_catta">
						</div>

						<div class="form-group">
							<label for="">Proxy</label>
							<input type="text" class="form-control" value="<?= $credenciais_catta['proxy']; ?>" name="proxy_catta">
						</div>

					</div>

					<div class="col-sm-4">

						<div class="form-group">
							<h3>API Localize</h3>
						</div>

						<div class="form-group">
							<label for="">Token</label>
							<input type="text" class="form-control" value="<?= $credenciais_localize['token']; ?>" required name="token_localize">
						</div>
						<div class="form-group">
							<label for="">URL</label>
							<input type="text" class="form-control" value="<?= $credenciais_localize['url']; ?>" required name="url_localize">
						</div>

					</div>


					<div class="col-sm-4">

						<div class="form-group">
							<h3>CREDILINK</h3>
						</div>

						<div class="form-group">
							<label for="">Usuário</label>
							<input type="text" class="form-control" value="<?= $credenciais_credilink['usuario']; ?>" required name="usuario_credilink">
						</div>
						<div class="form-group">
							<label for="">Proxy</label>
							<input type="text" class="form-control" value="<?= $credenciais_credilink['proxy']; ?>" required name="proxy_credilink">
						</div>
						<div class="form-group">
							<label for="">Senha</label>
							<input type="text" class="form-control" value="<?= $credenciais_credilink['senha']; ?>" required name="senha_credilink">
						</div>

					</div>


				</div>
			</div>
		</div>
	</div><!--/.row-->	
	</form>


</div><!--/.main-->
<?php }else{ ?>
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

	<form action="" method="post">
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					Digite sua senha
				</div>
				<div class="panel-body">

					<div class="col-lg-3">
						
						<form action="" method="post">
							<div class="form-group">
								<input type="password" name="senha" class="form-control" required>
							</div>
							
							<div class="form-group">
								<button type="submit" class="btn btn-primary">Salvar</button>
							</div>

						</form>

					</div>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
	</form>


</div><!--/.main-->


<?php } ?>