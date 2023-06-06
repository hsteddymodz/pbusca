<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">	

	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Bem vindo<?php if($_SESSION['tipo'] == 2) echo ', Administrador' ?>.</h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							Em qual sistema você quer consultar informações?asd
							<?php if($_SESSION['vencimento']){ ?>
							<span class="pull-right">
								<small>Sua conta vence em <b><?php echo date('d/m/Y H:i', $_SESSION['vencimento']); ?></b></small>
							</span>
							<?php } ?>
						</div>
						
					</form>
				</div>
				<div class="panel-body">
					<?php

$url="http://200.201.193.100/seekloc/sistema.php"; 
	$postinfo = "uid=xavier&upw=111213&uemp=1464";
	$proxy = '115.249.2.192:8080';

	$cookie_file_path = $path."/cookie.txt";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_NOBODY, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_PROXY, $proxy);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
	//set the cookie the site has for certain features, this is optional
	curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
	curl_setopt($ch, CURLOPT_USERAGENT,
	    "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
	$res = curl_exec($ch);

	echo curl_error($ch);
	echo ($res);
						?>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->