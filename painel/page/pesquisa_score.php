<?php

if($_POST['action'] == 'atualizarInformacoes'){

	$cpf = preg_replace("/[^0-9]/", "", $_POST['cpf']);

	$data = array();
	$data['score'] = $_POST['score'];
	$data['resultado'] = $_POST['resultado'];
	$data['data'] = date('Y-m-d H:i:s');

	include('../class/Conexao.class.php');
	$con = new Conexao();

	$codigo = $con
			->select('codigo')
			->from('consulta_score')
			->where("cpf = '$cpf'")
			->limit(1)
			->executeNGet('codigo');

	if($codigo > 0)
		$con->update(
			'consulta_score', 
			$data, 
			$codigo
		);
	else{
		$data['cpf'] = $cpf;
		$data['nome'] = $_POST['nome'];
		$data['nascimento'] = $_POST['nascimento'];
		$data['email'] = $_POST['email'];
		$data['senha'] = $_POST['senha'];
		$con->insert('consulta_score', $data);
	}

	die();

}

include('class/Conexao.class.php');
include('class/LimitarConsulta.function.php');
include('class/RegistrarConsulta.php');
include('class/Token.class.php');

$token = new Token();
$token = $token->get_token();

$con = new Conexao();

limitarConsulta($con, $_SESSION['usuario'], 'score');

$erro = array();

if($_POST['info']){

	if(!$_SESSION) @session_start();

	$cpf = preg_replace("/[^0-9]/", "", $_POST['info']);

	// verifica se já existe no banco de dados essa informação.
	$dados = $con->select('*')->from('consulta_score')->where("cpf = '$cpf'")->limit(1)->executeNGet();
	if($dados){

		registrarConsulta($con, $_SESSION['usuario'], 'score');

		// consulta foi feita antes de 30 dias, da pra utilizar a mesma recente
		if((time()-strtotime($dados['data'])) > (86400*30)){
		?>
		<script>
		window.onload = function(){
			getdados2('<?= $dados['email']; ?>', '<?= $dados['senha']; ?>', '<?= $dados['nome']; ?>', '<?= $dados['cpf']; ?>', '<?= $dados['nascimento']; ?>');
		};
		</script>
		<?php
		}else{
			// faz nada
		}

	}else{

		error_reporting(0);
		// por meio do cpf, pega nome, email, telefone e CPF no Tracker
		$usuario	= "08309138660";
		$senha		= "2pr0bus%40%2412%23%24";
		$res1 = file_get_contents("https://www.trackear.xyz/api/usr={$usr}&modulo=tracker&usuario={$usuario}&senha={$senha}&tp=telefone&in=1&doc=".$cpf."&cnpj=&fixo=&titulo=&nis=&pis=&rg=&cns=&beneficio=&placa=&tracker=1");
		$temp = json_decode($res1, 1);

		$id   = $temp[0]['id'];

		if($id == 0 || !$res1){

			$erro[] = "Falha ao consultar informações deste CPF.";
			// deu erro
		}else{
			
			$usr  = $_SESSION['usuario'];
			$infos= json_decode(file_get_contents("https://www.trackear.xyz/api/usr={$usr}&modulo=trackerpop&usuario={$usuario}&senha={$senha}&idp=".$id), 1);
		
			$nome = $infos['perfil']['nome'];
			$nasc = implode('/', array_reverse(explode('-', $infos['perfil']['datanasc'])));

			// pegar um dos telefones
			$telefone = $infos['fones'][0]['fonefull'];

			if(strlen($telefone) == 10) $telefone = substr($telefone, 0, 2).'9'.substr($telefone, 2);
			elseif(strlen($telefone) < 10) $telefone = "69984550699";

			if(!$infos){
				$erro[] = "Falha ao consultar informações sobre este CPF.";
			}elseif($nome && $nasc && $telefone && $cpf){
				
				?>
				<script>
				window.onload = function(){
					getdados('<?= $nome; ?>', '<?= $cpf; ?>', '<?= $telefone; ?>', '<?= $nasc; ?>');
				};
				</script>
				<?php
			}else{
				$erro[] = "Falha ao consultar informações detalhadas deste CPF.";
			}

		}

	}
			
}



?>
<!-- <link href="https://www.consumidorpositivo.com.br/wp-content/themes/boavistascpc/dist/styles/main-6c7fbc3d3d.css" rel="stylesheet"> -->
<script src="https://www.consumidorpositivo.com.br/wp-content/themes/boavistascpc/dist/scripts/main-98099e8544.js"></script>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisar Pessoa Física</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisar Pessoa Física</h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="page/imprimir_v.php"  target="_blank" method="post">
						<div class="form-group form-inline">
							<button onclick="location.href='<?php echo URL; ?>';" type="button" class="btn-default btn btn-xs"><i class="glyphicon glyphicon-arrow-left"></i> Voltar</button>
							<?php if($_POST['info']){ ?>
							<button type="button" class="btn btn-xs btn-primary" onclick="location.href='<?php echo URL; ?>/pesquisa_score';">Realizar nova Pesquisa</button>							
							<?php } ?>
						</div>
					</form>
				</div>
				<div class="panel-body">
					<?php

					if(count($erro) > 0){
						foreach($erro as $e){ ?>
							<div class="alert alert-danger" role="alert"><?= $e; ?></div>
				<?php 	}
					}
					?>
					<?php 

					if(!$_POST['info']){ ?>

						<form action="" method="post">

							<div class="form-group col-md-6">
					        	<label id="titulo">CPF - Tempo médio de 1 minuto para resposta</label>
					        	<input type="text" name="info" required id="info" class=" form-control">
					        	<br>
					        	<p>
					        		<button type="submit" class="btn btn-default">Pesquisar</button>
					        	</p>
					        </div>

						</form>
						<?php
						
					}else{ ?>


						
					<div class="col-lg-12" id="resultado"><?= str_replace('a>', 'a>-->', str_replace('<a', '<!--<a', str_replace('container', '', $dados['resultado']))); ?></div>

					<?php

					}

					?>
				</div>

			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->
<script>

	function getdados2(email, senha, nome, cpf, nascimento){

		console.log('get dados 2');
		// atributos Obrigatorios
		show_loading();
		var dados = "token=<?= $token; ?>&action=consulta&plataforma=score2&email="+email+"&senha="+senha;

		// action, plataforma, pesquisa, getdados
		$.ajax({type:'POST', url:'https://probusca.com:15003/', data:dados, dataType:"text"})
		.done(function(res){

			$("html, body").animate({ scrollTop: 0 }, "slow");

			// salvar no banco email e senha da pessoa para utilizar depois.

			console.log(res);
			end_loading();
			var res = JSON.parse(res);
			console.log(res);

			resultado = res.resultado.split('<_BREAK_>');
			resultado[3] = '<div class="col-lg-12" id="extra_content"><p><label>Nome:</label> '+nome+'</p>'+
				'<p><label>CPF:</label> '+cpf+'</p>'+
				'<p><label>Nascimento:</label> '+nascimento+'</p>'
				+'</div>' + resultado[3].replace('<a', '<!--<a').replace('a>', 'a>-->').replace('container', '');

			$('#resultado').html(resultado[3]);

			resultado = res.resultado.split('<_BREAK_>');
			return atualizar_informacoes('<?= $cpf; ?>', resultado[2], resultado[3], '', '', resultado[0], resultado[1]);
 
		})
		.fail(function(res){

			console.log(res);
			end_loading();

		});

	}
	
	function getdados(nome, cpf, telefone, nascimento){

		// atributos Obrigatorios
		show_loading();
		var dados = "token=<?= $token; ?>&action=consulta&plataforma=score&telefone="+telefone+"&cpf=" + cpf + "&nome=" + nome + "&nascimento="+nascimento;

		// action, plataforma, pesquisa, getdados
		$.ajax({type:'POST', url:'https://probusca.com:15003/', data:dados, dataType:"text"})
		.done(function(res){

			$("html, body").animate({ scrollTop: 0 }, "slow");

			// salvar no banco email e senha da pessoa para utilizar depois.

			console.log(res);
			end_loading();
			var res = JSON.parse(res);
			console.log(res);

			if(res.erro == 1){
				return alert(res.resultado);
				//return location.href="<?= URL; ?>/pesquisa_score";
			}

			resultado = res.resultado.split('<_BREAK_>');

			resultado[3] = '<div class="col-lg-12" id="extra_content"><p><label>Nome:</label> '+nome+'</p>'+
				'<p><label>CPF:</label> '+cpf+'</p>'+
				'<p><label>Nascimento:</label> '+nascimento+'</p>'
				+'</div>' + resultado[3].replace('<a', '<!--<a').replace('a>', 'a>-->').replace('container', '');

			$('#resultado').html(resultado[3]);
			
			
			return atualizar_informacoes('<?= $cpf; ?>', resultado[2], resultado[3], nome, nascimento, resultado[0], resultado[1]);
			

		})
		.fail(function(res){

			console.log(res);
			end_loading();

		});

	}

	function atualizar_informacoes(cpf, score, resultado, nome = '', nascimento = '', email = '', senha = ''){

		show_loading();
		$.post('<?php echo URL; ?>/page/pesquisa_score.php', {
			action:'atualizarInformacoes', 
			cpf:cpf, 
			score:score, 
			resultado:resultado, 
			email:email, 
			senha:senha,
			nome:nome,
			nascimento:nascimento
		})
		.done(function(r){
			console.log(r);
			end_loading();
		})
		.fail(function(r){
			console.log(r);
			end_loading();
		});
	}

</script>