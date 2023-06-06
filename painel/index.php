<?php
if(!$_SESSION) @session_start();

include("class/Router.class.php");
include("class/Conexao.class.php");
include("class/kickar.php");


$con = new Conexao();



if($_SESSION['vencimento'] && $_SESSION['vencimento'] < time()){
	unset($_SESSION);
	$con->execute("UPDATE login set
		ultima_atividade = '".date('Y-m-d H:i:s')."',
		data_logout = '".date('Y-m-d H:i:s')."',
		where usuario = '".$_SESSION['usuario']."'
		and data_logout IS NULL");
	die('<script>alert("Conta vencida!"); location.href="https://probusca.com";</script>');
}

$config = parse_ini_file('class/config.ini');
define("URL", $config['url']);
define("URL_INDEX", $config['url_index']);
define("CDN", $config['cdn']);

$_SESSION['endpoint'] = 'https://servidor.probusca.com';
$_SESSION['lastAction'] = time();
kickarOutrosUsuarios($_SESSION['usuario'], $_SESSION['sessao'], URL, $_SESSION['lastAction']);

$router = new Router($_GET['p']);

$vencimento = false;
if(($_SESSION['vencimento'] - time()) >= 2000)
	$vencimento = -1;
else
	$vencimento -= time();

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Painel de Controle</title>
	
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-72317669-3"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-72317669-3');
	</script>


	<!--Icons-->
	<script src='https://www.google.com/recaptcha/api.js' async defer />
	<script src="<?php echo CDN; ?>/painel/js/lumino.glyphs.js"></script>

	<!-- Favicon -->
	<link HREF="<?php echo CDN; ?>/assets/img/favicon.ico" mce_HREF="<?php echo CDN; ?>/assets/img/favicon.ico" REL='icon'>
	<link HREF="<?php echo CDN; ?>/assets/img/favicon.ico" mce_HREF="<?php echo CDN; ?>/assets/img/favicon.ico" REL='shortcut icon'>

	<!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<script src="js/respond.min.js"></script>
	<![endif]-->
	<script src="<?php echo CDN; ?>/painel/js/number_format.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.1.2/handlebars.min.js"></script>

	<?php if($router->pagina == 'pesquisa_score') echo ' <link href="'.CDN.'/painel/css/score.css" rel="stylesheet">'; ?>

	<?php if($router->pagina == 'pesquisa_b3') echo ' <link href="'.CDN.'/painel/css/spc.css" rel="stylesheet">'; ?>

	<link href="<?php echo CDN; ?>/painel/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo CDN; ?>/painel/css/datepicker3.css" rel="stylesheet">
	<link href="<?php echo CDN; ?>/painel/css/styles.css" rel="stylesheet">
	<link href="<?php echo CDN; ?>/painel/css/easy-autocomplete.min.css" rel="stylesheet">
	<link href="<?php echo CDN; ?>/painel/css/easy-autocomplete.themes.min.css" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo CDN; ?>/painel/css/font-awesome.css">
	<!-- TimePicker -->
	<link rel="stylesheet" href="<?php echo CDN; ?>/assets/js/jquery.timepicker.css">
	<!--<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=p76ozj3cfcg6a92sit2a0ssxtd86uh4ai4ykj2io1uw45ww9"></script>-->



	<?php if(in_array($_GET['p'], array('webservice_v', 'pesquisa_ex', 'webservice_a', 'pesquisa_si'))){ ?>

	<link rel="stylesheet" type="text/css" href="<?= CDN; ?>/painel/css/vip.css">
	<script src="<?= CDN; ?>/painel/js/viper.js"></script>
	<script src="<?= CDN; ?>/painel/js/html2pdf.bundle.min.js"></script>

	<?php } ?>

	<style>
		@keyframes glyphicon-spin-r {
		    0% {
		        -webkit-transform: rotate(0deg);
		        transform: rotate(0deg);
		    }

		    100% {
		        -webkit-transform: rotate(359deg);
		        transform: rotate(359deg);
		    }
		}
		.spin-the-spinner {
		    -webkit-animation: glyphicon-spin-r 1s infinite linear;
		    animation: glyphicon-spin-r 1s infinite linear;
		}
	</style>
</head>

<body>

	<div id="loader" class="esconder"><img src="<?= CDN; ?>/painel/img/source.gif" height="100" alt="Carregando..."></div>

	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sidebar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#"><span>Pro</span>Busca</a>
				<!--
				<ul class="user-menu">
					<li class="dropdown pull-right">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><svg class="glyph stroked male-user"><use xlink:href="#stroked-male-user"></use></svg> <?php //echo $_SESSION['usuario']->getNome(); ?> <span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu">
							<li><a href="<?php echo URL; ?>/autor-cadastrar/<?php // echo $_SESSION['usuario']->getCodigo(); ?>"><svg class="glyph stroked male-user"><use xlink:href="#stroked-male-user"></use></svg> Meu Perfil</a></li>
							<li><a href="<?php echo URL; ?>/sair"><svg class="glyph stroked cancel"><use xlink:href="#stroked-cancel"></use></svg> Logout</a></li>
						</ul>
					</li>
				</ul>-->
				<?php if ($vencimento > 0 && $_SESSION['tipo'] == 1) { ?>
				<span style="color:white; padding-top:15px; font-size:1.2em;" class=" pull-right">Sua conta vence em
					<span id="time"><?php echo intval($vencimento/60).":".intval($vencimento % 60); ?></span>
				</span>
				<?php }else if($_SESSION['tipo'] == 3){ ?>
				<span style="color:white; padding-top:15px; font-size:1.2em;" class=" pull-right">Créditos:
					<b><?php echo $con->select('sum(valor) as v')->from('revendedor_credito')->where("usuario = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('v'); ?></b>
					<buttton onclick="location.href='<?php echo URL; ?>/recarregar';" class="btn btn-default btn-xs">Recarregar</buttton>
				</span>
				<?php } ?>
			</div>



		</div><!-- /.container-fluid -->
	</nav>

	<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">

		<ul class="nav menu">
			<li <?php if($router->pagina == 'inicio' || $router->pagina == '') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/"><svg class="glyph stroked dashboard-dial"><use xlink:href="#stroked-dashboard-dial"></use></svg> Início</a>
			</li>

			<?php if($_SESSION['tipo'] == 4) { ?>

			<li <?php if($router->pagina == 'administrador') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/administrador"><svg class="glyph stroked male user "><use xlink:href="#stroked-male-user"/></svg> Administradores</a>
			</li>



			<?php } ?>

			<?php if($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 3 ){ ?>

			<li <?php if($router->pagina == 'historico') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/historico"><svg class="glyph stroked table"><use xlink:href="#stroked-table"/></svg> Histórico</a>
			</li>

			<li <?php if($router->pagina == 'usuario') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/usuario"><svg class="glyph stroked male user "><use xlink:href="#stroked-male-user"/></svg> Usuários</a>
			</li>

			<li <?php if($router->pagina == 'usuario_teste') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/usuario_teste"><svg class="glyph stroked male user "><use xlink:href="#stroked-male-user"/></svg> Contas de Teste</a>
			</li>

			<?php } ?>



			<?php if($_SESSION['tipo'] == 2  || $_SESSION['tipo']  == 4){ ?>

			<li <?php if($router->pagina == 'credito') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/credito"><svg class="glyph stroked table"><use xlink:href="#stroked-table"/></svg> Créditos</a>
			</li>

			<li <?php if($router->pagina == 'revendedor') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/revendedor"><svg class="glyph stroked male user "><use xlink:href="#stroked-male-user"/></svg> Revendedores</a>
			</li>

			<li <?php if($router->pagina == 'plano') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/plano"><svg class="glyph stroked blank document"><use xlink:href="#stroked-blank-document"/></svg> Planos</a>
			</li>

			<?php } ?>

			<?php if($_SESSION['tipo']  == 4){ ?>

			<li <?php if($router->pagina == 'log') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/log"><svg class="glyph stroked clipboard with paper"><use xlink:href="#stroked-clipboard-with-paper"/></svg> Logs</a>
			</li>

			<li <?php if($router->pagina == 'plataforma') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/plataforma"><svg class="glyph stroked app window with"><use xlink:href="#stroked-app-window-with-content"/></svg> Plataformas</a>
			</li>

			<li <?php if($router->pagina == 'consulta_salva') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/consulta_salva"><svg class="glyph stroked blank document"><use xlink:href="#stroked-blank-document"/></svg> Consultas Salvas</a>
			</li>

			<li <?php if($router->pagina == 'login_duplo') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/login_duplo"><svg class="glyph stroked male user "><use xlink:href="#stroked-male-user"/></svg> Logins Simultâneos</a>
			</li>

			<li <?php if($router->pagina == 'relatorio_crawler') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/relatorio_crawler"><svg class="glyph stroked gear"><use xlink:href="#stroked-gear"/></svg> Relatório de Crawlers</a>
			</li>


			<?php } ?>

			<?php if($_SESSION['tipo'] == 3){ ?>



			<li <?php if($router->pagina == 'conta') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/conta"><svg class="glyph stroked pen tip"><use xlink:href="#stroked-pen-tip"/></svg> Contas Bancárias</a>
			</li>


			<li <?php if($router->pagina == 'perfil') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/perfil"><svg class="glyph stroked male user "><use xlink:href="#stroked-male-user"/></svg> Meu Perfil</a>
			</li>
			<?php } ?>

			

			<li <?php if($router->pagina == 'minhaconta') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/minhaconta"><svg class="glyph stroked gear"><use xlink:href="#stroked-gear"/></svg>  Minha Conta</a>
			</li>

			<?php if($_SESSION['tipo']  == 4){ ?>
			<li <?php if($router->pagina == 'configuracao') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/wingwmzwla"><svg class="glyph stroked gear"><use xlink:href="#stroked-gear"/></svg>  Configurações do Sistema</a>
			</li>
			<?php } ?>

			<?php if($_SESSION['tipo'] == 1){ ?>
			<li <?php if($router->pagina == 'log_cliente') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/log_cliente"><svg class="glyph stroked clipboard with paper"><use xlink:href="#stroked-pen-tip"/></svg> Logs</a>
			</li>
			<?php } ?>


			<li <?php if($router->pagina == 'sair') echo 'class="active"'; ?>>
				<a href="<?php echo URL; ?>/sair"><svg class="glyph stroked cancel"><use xlink:href="#stroked-cancel"/></svg>  Sair do Painel</a>
			</li>



			<!--


			<li><a href="widgets.html"><svg class="glyph stroked calendar"><use xlink:href="#stroked-calendar"></use></svg> Widgets</a></li>

			<li><a href="tables.html"><svg class="glyph stroked table"><use xlink:href="#stroked-table"></use></svg> Tables</a></li>
			<li><a href="forms.html"><svg class="glyph stroked pencil"><use xlink:href="#stroked-pencil"></use></svg> Forms</a></li>
			<li><a href="panels.html"><svg class="glyph stroked app-window"><use xlink:href="#stroked-app-window"></use></svg> Alerts &amp; Panels</a></li>
			<li><a href="icons.html"><svg class="glyph stroked star"><use xlink:href="#stroked-star"></use></svg> Icons</a></li>
			<li class="parent ">
				<a href="#">
					<span data-toggle="collapse" href="#sub-item-1"><svg class="glyph stroked chevron-down"><use xlink:href="#stroked-chevron-down"></use></svg></span> Dropdown
				</a>
				<ul class="children collapse" id="sub-item-1">
					<li>
						<a class="" href="#">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg> Sub Item 1
						</a>
					</li>
					<li>
						<a class="" href="#">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg> Sub Item 2
						</a>
					</li>
					<li>
						<a class="" href="#">
							<svg class="glyph stroked chevron-right"><use xlink:href="#stroked-chevron-right"></use></svg> Sub Item 3
						</a>
					</li>
				</ul>
			</li>
			<li role="presentation" class="divider"></li>
			<li><a href="login.html"><svg class="glyph stroked male-user"><use xlink:href="#stroked-male-user"></use></svg> Login Page</a></li>-->
		</ul>

	</div><!--/.sidebar-->

	<?php  $router->incluir();  ?>

	<script src="<?= CDN; ?>/painel/js/jquery-3.1.1.min.js"></script>

	<?php if(false) {

		$page = str_replace('/painel/', '', $_SERVER['REQUEST_URI']);

		// rewrite the $.post function to add the Captcha

		?>
		<script src="https://www.google.com/recaptcha/api.js?render=6Ld4iasUAAAAAFuUfl-AVHKZvJq6Svq7TVTm9FKX"></script>
	  	<script>
	  		
	  		console.log('<?= $page; ?>');
		  	/*grecaptcha.ready(function() {
		      	grecaptcha.execute('6Ld4iasUAAAAAFuUfl-AVHKZvJq6Svq7TVTm9FKX', {action: '<?= $page; ?>'}).then(function(token) {
		        
		      	});
		  	});*/
	  	</script>
	<?php } ?>

	<script src="<?= CDN; ?>/painel/js/bootstrap.min.js"></script>

	<?php if($_GET['p'] == 'pesquisa_catta') echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js"></script><link rel="stylesheet" href="//probusca.com/painel/js/jqueryflexselect/flexselect.css" type="text/css">
	<script src="//probusca.com/painel/js/jqueryflexselect/liquidmetal.js" type="text/javascript"></script>
	<script src="//probusca.com/painel/js/jqueryflexselect/jquery.flexselect.js" type="text/javascript"></script>'; ?>

	<?php if($_GET['p'] == 'pesquisa_atvEcon') echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js"></script><link rel="stylesheet" href="//probusca.com/painel/js/jqueryflexselect/flexselect.css" type="text/css">
	<script src="//probusca.com/painel/js/jqueryflexselect/liquidmetal.js" type="text/javascript"></script>
	<script src="//probusca.com/painel/js/jqueryflexselect/jquery.flexselect.js" type="text/javascript"></script>'; ?>

		<?php if($_GET['p'] == 'pesquisa_nett') echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script><script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js"></script><link rel="stylesheet" href="//probusca.com/painel/js/jqueryflexselect/flexselect.css" type="text/css">
		<script src="//probusca.com/painel/js/jqueryflexselect/liquidmetal.js" type="text/javascript"></script>
		<script src="//probusca.com/painel/js/jqueryflexselect/jquery.flexselect.js" type="text/javascript"></script>';  ?>

	<script src="<?php echo CDN; ?>/assets/js/jquery.timepicker.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>

	<script src="<?php echo CDN; ?>/painel/js/jquery.easy-autocomplete.min.js"></script>
	<!-- Script para impressão -->
	<script type="text/javascript" src="https://probusca.com/painel/js/printThis.js"></script>

	<script>

		$('#horario').timepicker({
        'minTime': '<?php echo date("h:i"); ?>pm',
        'showDuration': true
      	});

		$('.horario').timepicker();


		function show_loading(posicao = false){

			if(posicao === false)
				$('#loader').removeClass('esconder');
			else
				$(posicao).addClass('fas fa-spinner spin-the-spinner');

		}


		function end_loading(){
			$('#loader').addClass('esconder');
		}

		!function ($) {
		    $(document).on("click","ul.nav li.parent > a > span.icon", function(){
		        $(this).find('em:first').toggleClass("glyphicon-minus");
		    });
		    $(".sidebar span.icon").find('em:first').addClass("glyphicon-plus");
		}(window.jQuery);

		$(window).on('resize', function () {
		  if ($(window).width() > 768) $('#sidebar-collapse').collapse('show')
		})
		$(window).on('resize', function () {
		  if ($(window).width() <= 767) $('#sidebar-collapse').collapse('hide')
		})

		function onlyNumbers(event) {
		    var key = window.event ? event.keyCode : event.which;

		    if ( key < 48 || key > 57 ) {
		        return false;
		    } else {
		        return true;
		    }
		}

		$(document).ready(function(){
		    $('.onlyNumbers').keypress(onlyNumbers);
		});

		var TIMESTAMP = 0;

		function contagem_regressiva(venc){

			if(venc < 0)
				return;

			TIMESTAMP = venc;

			setInterval(function(){


				TIMESTAMP--;
				console.log(TIMESTAMP);
				var segundos, minutos;
				minutos = parseInt(TIMESTAMP/60);
				segundos = parseInt(TIMESTAMP%60);
				$('#time').html(minutos + ':' + segundos);

				if(minutos == 0 && segundos == 0)
					location.href='<?php echo URL; ?>';

			}, 1000);

		}

		$(document).ready(function(){

			contagem_regressiva(<?php echo $vencimento; ?>);

			$('[data-toggle="tooltip"]').tooltip();

			$('.data').mask('00/00/0000');

			tinymce.init({ selector:'.wysig' });

		});



<?php if($router->pagina == 'credito'){ ?>

var options = {

  url: function(phrase) {
    return "page/credito.php";
  },

  getValue: function(element) {
    return element.usuario;
  },

  ajaxSettings: {
    dataType: "json",
    method: "POST",
    data: {
      dataType: "json"
    },
    list:{
    	match: {
                        enabled: true
                    }
    },
    theme: "bootstrap"
  },

  preparePostData: function(data) {
    data.phrase = $("#usuario").val();
    return data;
  },

  requestDelay: 100
};

var options2 = {

  url: function(phrase) {
    return "page/credito.php";
  },

  getValue: function(element) {
    return element.usuario;
  },

  ajaxSettings: {
    dataType: "json",
    method: "POST",
    data: {
      dataType: "json"
    },
    list:{
    	match: {
                        enabled: true
                    }
    },
    theme: "bootstrap"
  },

  preparePostData: function(data) {
    data.phrase = $("#extrato").val();
    return data;
  },

  requestDelay: 100
};

$("#extrato").easyAutocomplete(options2);
$("#usuario").easyAutocomplete(options);
<?php } ?>

</script>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5c31618382491369baa0a899/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>

</html>
