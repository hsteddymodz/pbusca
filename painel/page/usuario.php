<?php
define("USUARIOS_POR_PAGINA", 40);

if(!isset($_SESSION)) @session_start();

if($_POST['maisinfo']){

	include("../class/Conexao.class.php");
	$con = new Conexao();
	$cod = intval($_POST['maisinfo']);

	$administrador = '';
	$revendedor    = '';
	$quemcadastrou = '';
	$p['telefone'] = '';
	if($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4){

		$p = $con->select('administrador, revendedor, quemcadastrou, telefone, senha')->from('usuario')->where("codigo = '$cod'")->limit(1)->executeNGet();

		$administrador = $con
		->select('a.nome as administrador')
		->from('usuario a')
		->where("a.codigo = '".$p['administrador']."'")
		->limit(1)
		->executeNGet('administrador');

		$revendedor    = $con
		->select('a.nome as administrador')
		->from('usuario a')
		->where("a.codigo = '".$p['revendedor']."'")
		->limit(1)
		->executeNGet('administrador');

		$quemcadastrou    = $con
		->select('a.nome as administrador')
		->from('usuario a')
		->where("a.codigo = '".$p['quemcadastrou']."'")
		->limit(1)
		->executeNGet('administrador');

		if(!$p['telefone']) $p['telefone'] = '';
	}

	die(json_encode(array('administrador'=>$administrador, 'senha'=>'', 'revendedor'=>$revendedor, 'quemcadastrou'=>$quemcadastrou, 'telefone'=>$p['telefone'])));

}

if($_POST['request_consultas']){

	include("../class/Conexao.class.php");
	$con = new Conexao();
	$usuario = intval($_POST['request_consultas']);

	$resultado = $con->select('count(*) as n, p.nome')->from('usuario_consulta uc, plataforma p')->where("uc.usuario = '$usuario' and p.tipo = uc.plataforma")->groupby('uc.plataforma')->executeNGet();
	
	$logs   = $con->select('l.*')
	->from('login l')
	->where("l.usuario = '$usuario' and l.ip is not null and l.data_login >= '".date("Y-m-d", strtotime("-7 days"))."'")
	->orderby('ultima_atividade DESC')
	->executeNGet();


	?>

	<h2>Consultas realizadas hoje</h2>

	<?php if(!$resultado) echo "<p>Nenhuma consulta foi feita hoje.</p>"; else { ?>

	<table class="table table-bordered table-stripped">
		<thead>
			<tr>
				<td>Módulo</td>
				<td>Nº de Consultas</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($resultado as $r){ ?>
			<tr>
				<td><?php echo $r['nome']; ?></td>
				<td><?php echo $r['n']; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	

	<?php } ?>
	
	<h2>Acessos nos últimos 7 dias</h2>

	<?php if(!$logs) echo "<p>Nenhum acesso foi feito.</p>"; else { ?>

	<table class="table table-bordered table-stripped">

		<thead>
			<tr>
				<td>Data</td>
				<td>IP</td>
			</tr>
		</thead>
		<tbody>

			<?php foreach($logs as $r){ ?>
			<tr>
				<td>
					<?php if(date('Y-m-d', strtotime($r['data_login'])) == date('Y-m-d')) echo 'Hoje '.date("H:i", strtotime($r['data_login'])); else  echo date('d/m/Y H:i', strtotime($r['data_login'])); ?> 
					<?php if( (time() - 60) < strtotime($r['ultima_atividade']) && $r['ultima_atividade']) echo '<span class="label label-success">Logado</span>'; ?>
				</td>
				<td><a target="_blank" href="https://check-host.net/ip-info?host=<?php echo $r['ip']; ?>"><?php echo $r['ip']; ?></a></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php }

	die();

}

if($_SESSION['tipo'] == 1)
	die('<script>location.href="'.URL.'/painel";</script>');


$router = new Router($_GET['p']);
$con    = new Conexao();

include("class/protect.function.php");
protect(array(2,3,4));


/* variaveis de ambiente */
$nome_p      = "Usuários";


if($router->param(0)){
	$entidade = "";
	if($_SESSION['tipo'] == 2)
		$entidade = 'do Revendedor';

	$nome_p = "Usuários do $entidade <b>".$con->select('usuario')->from('usuario')->where("codigo = '".intval($router->param(0))."'")->limit(1)->executeNGet('usuario')."</b>";
}
$tabela      = "usuario";
$pk          = "codigo";
$col_mostrar = array("nome"=>"Nome", "usuario"=>"Login", "plano"=>"Plano");
$col_pesquisar = $col_mostrar;
$pagina_cad  = "usuario-cadastrar";

function show_info($d, $n,  $array_completo, $con, $pl = array()){

	switch($n){
		case 'usuario':
		return "<a onclick=\"consultar_usuario('".$array_completo['codigo']."');\" href=\"javascript:void(0);\">".$d."</a>";


		case 'plano':
		return $pl[$d];

		default:
		return $d;

	}
}

if($_SESSION['tipo'] == 3)
	$sql_adicional = " and revendedor = '".$_SESSION['usuario']."' ";
elseif($router->param(0) && $_SESSION['tipo'] == 2)
	$sql_adicional = " and revendedor = '".intval($router->param(0))."' and administrador = '".$_SESSION['usuario']."' ";
elseif($_SESSION['tipo'] == 2)
	$sql_adicional = " and administrador = '".$_SESSION['usuario']."' ";
elseif($_SESSION['tipo'] == 4 && $router->param(0))
	$sql_adicional = " and administrador = '".intval($router->param(0))."' ";
else
	$sql_adicional = '';

$sql_final = '';
if($_POST['reordenar'])
	$sql_final = "order by ".$_POST['coluna']." ".$_POST['order'];
else
	$sql_final = "";

$sql_logado = "";
if($_POST['logado']){
	$sql_logado = " and codigo in (select usuario from login where data_logout is null and ultima_atividade >= '".date("Y-m-d H:i:s", time()-600)."' order by codigo desc)";
}

if($_POST['pesquisar']){

	$where = '';
	$q     = $con->escape($_POST['pesquisar']);

	foreach($col_pesquisar as $col=>$x) $where .= " $col like '%{$q}%' or";

	$where = substr($where, 0, -2);

	$where = "(deletado is null and teste is null and tipo != 3) $sql_logado $sql_usuarios_do_admin and codigo != '".$_SESSION['usuario']."' $sql_adicional and (" . $where. " ) ".$sql_final;
	$count = $con->select('count(*) as n')->from('usuario')->where($where)->limit(1)->executeNGet('n');

	$limit = USUARIOS_POR_PAGINA;
	if($_POST['pagina']){
		$limit = ($_POST['pagina']*USUARIOS_POR_PAGINA).", ".USUARIOS_POR_PAGINA;
	}

	$dados       =  $con
	->select('usuario.*')
	->from("usuario")
	->where($where)
	->limit($limit)
	->executeNGet();

}else{
	
	$where = "(deletado is null and teste is null and tipo != 3) $sql_logado $sql_usuarios_do_admin and codigo != '".$_SESSION['usuario']."' $sql_adicional and tipo != 3 ".$sql_final;
	$count = $con->select('count(*) as n')->from('usuario')->where($where)->limit(1)->executeNGet('n');
	
	$limit = USUARIOS_POR_PAGINA;
	if($_POST['pagina']){
		$limit = ($_POST['pagina']*USUARIOS_POR_PAGINA).", ".USUARIOS_POR_PAGINA;
	}

	$dados       =  $con
	->select('usuario.*')
	->from("usuario")
	->where($where)
	->limit($limit)
	->executeNGet();
}

$planos = $con->select('*')->from('plano')->where("codigo in (select plano from usuario group by plano)")->executeNGet();
$pl_final = array();
foreach($planos as $p){
	$pl_final[$p['codigo']] = $p['nome'];
}


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
			<h1 class="page-header"><?php echo $nome_p; ?> <small><?php echo ($count);  ?></small></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" id="myform" method="post">
						<div class="form-group form-inline">

							<button type="button" onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>';" class="btn btn-primary btn-md"><i class="glyphicon glyphicon-plus"></i> Adicionar Usuário</button>
							

							<span class="pull-right">
								<input type="text" value="<?php echo $_POST['pesquisar']; ?>"  placeholder="Pesquisar <?php echo $nome_p; ?>..." name="pesquisar" class="form-control input-md">
								<input type="hidden" value="<?php echo $_POST['logado']; ?>" id="logado" name="logado">
								<button type="submit" class="btn-success btn btn-md"><i class="glyphicon glyphicon-search"></i> Pesquisar</button>
								<?php if(strlen($_POST['pesquisar']) > 0){  ?>
								<button type="button" onclick="location.href='<?php echo "http://" . $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']; ?>';" class="btn btn-default btn-xs">Limpar Pesquisa</button>
								<?php } ?>
							</span>
						</div>
					</form>
				</div>
				<div class="panel-body">

					<form method="POST" action="" id="reordenar">
						<input type="hidden" id="reordenar" value="1" name="reordenar">
						<input type="hidden" id="coluna" name="coluna">
						<input type="hidden" value="<?php echo $_POST['logado']; ?>" id="logado" name="logado">
						<input type="hidden" id="order" value="<?php if(!$_POST['order'] || $_POST['order'] != 'asc') echo 'asc'; else echo 'desc'; ?>" name="order">
					</form>

					<p><form method="POST" action="" id="formLogado">
						
						<input type="hidden" value="<?php echo $_POST['pesquisar']; ?>" name="pesquisar" >
						<input type="hidden" value="<?php echo $_POST['reordenar']; ?>" id="reordenar2" value="1" name="reordenar">
						<input type="hidden" value="<?php echo $_POST['coluna']; ?>" id="coluna2" name="coluna">

						<input type="hidden" value="<?php echo $_POST['order']; ?>" id="order2" value="<?php if(!$_POST['order'] || $_POST['order'] != 'asc') echo 'asc'; else echo 'desc'; ?>" name="order">

						<label for="check">
							<input type="checkbox" onchange="javascript: document.getElementById('formLogado').submit();" <?php if($_POST['logado']) echo 'checked'; ?> name="logado" id="check"> Mostrar apenas usuários logados.
						</label>
					</form></p>
					
					<table class="table table-bordered table-stripped ">
						<thead>
							<tr>

								<th><a onclick="reordenar('nome');" href="javascript: void(0);">Nome</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'nome') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'nome') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>

								<th><a onclick="reordenar('usuario');" href="javascript: void(0);">Login</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'usuario') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'usuario') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>

								<th><a onclick="reordenar('plano');" href="javascript: void(0);">Plano</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'plano') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'plano') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>

								<th>Status</th>

								<th><a onclick="reordenar('data');" href="javascript: void(0);">Data de Cadastro</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'data') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'data') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>
								<th><a onclick="reordenar('vencimento');" href="javascript: void(0);">Vencimento</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'vencimento') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'vencimento') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>

								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if(count($dados) > 0){
								foreach($dados as $p){ ?>
								<tr class=" <?php if($p['inativo'] == 1) echo 'bg-warning'; ?>">
									<?php foreach($col_mostrar as $k=>$c) echo "<td>".show_info($p[$k], $k, $p, $con, $pl_final)."</td>"; ?>
									<td><?php

									$t = $con->select('ultima_atividade, data_logout')->from('login')->where("usuario = '".$p['codigo']."'")->orderby("codigo desc")->limit(1)->executeNGet();

									if(strtotime($t['ultima_atividade']) > (time() - 600) && !$t['data_logout'])
										echo ' <span class="label label-success">online</span>';
									else
										echo ' <span class="label label-danger">offline</span>';
									?></td>
									<td><?php echo date('d/m/Y H:i', strtotime($p['data'])); ?></td>
									<td <?php if(strtotime($p['vencimento']) < time()) echo 'class="bg-danger"'; ?> ><?php echo date('d/m/Y H:i', strtotime($p['vencimento'])); ?></td>

									<td class="text-right">



										<button class="btn btn-default btn-xs" onclick="maisinfo(<?php  echo $p['codigo'] ?>);" type="button">+ info</button>

										<?php if($_SESSION['tipo'] == 3){ ?>
										<button onclick="adicionar_credito(<?php echo $p['codigo']; ?>, '<?php echo $p['usuario']; ?>');" class="btn btn-xs btn-default">Adicionar Tempo</button>
										<?php } ?>
										<?php if($p['inativo']){ ?>
										<?php if($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4){ ?>
										<button onclick="if(confirm('Tem certeza que quer ativar este usuário?')) location.href='<?php echo URL; ?>/onlyAtivar/<?php echo $p[$pk]; ?>';" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-check"></i> Ativar</button>
										<?php } else { ?>
										<button onclick="location.href='<?php echo URL; ?>/ativar/<?php echo $p[$pk]; ?>';" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-check"></i> Ativar</button>
										<?php } ?>
										<?php }elseif($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4){ ?>
										<button onclick="desativaUsuario()" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-check"></i> Desativar</button>
										<?php } if($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 2){ ?>
										<button onclick="location.href='<?php echo URL; ?>/deletar/usuario/<?php echo $p[$pk]; ?>';" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Deletar</button>
										<?php } ?>
										<?php if(/*$_SESSION['usuario']->getCodigo() != $p[$pk]*/ 1){ ?>
										<button onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>/<?php echo $p[$pk]; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i> Editar</button>

										<?php } ?>
									</td>
								</tr>
								<?php 
							}
						}else{ ?>
						<tr>
							<?php if(strlen($_POST['pesquisar']) > 0){  ?>
							<td class="text-center" colspan="7">Nenhum registro encontrado para o termo <strong><?php echo $_POST['pesquisar']; ?></strong>.</td>
							<?php } else { ?>
							<td class="text-center" colspan="7">Nenhum registro encontrado.</td>
							<?php } ?>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>

			<div class="form-group text-center">
				<form method="POST" action="" >
					<?php 
					$n_paginas = ceil($count/USUARIOS_POR_PAGINA);
					foreach($_POST as $k=>$v){ echo '<input type="hidden" name="'.$k.'" value="'.$v.'">';}
					for($k = 0; $k < $n_paginas; $k++){ ?>
						<button type="submit" name="pagina" value="<?= $k; ?>" class="btn btn-xs <?php if($_POST['pagina'] == $k) echo 'btn-primary'; else echo 'btn-default' ?>"><?= $k+1; ?></button>
					<?php } ?>
				</form>
			</div>

			
		</div>
	</div>
</div>
</div><!--/.row-->	


</div><!--/.main-->

<div class="modal fade" id="infos_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Extrato de Consultas</h4>
			</div>
			<div class="modal-body">
				<p id="resultado_consulta"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php if($_SESSION['tipo'] == 3) { ?>

<div class="modal fade" id="modal_credito" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Adicionar Tempo</h4>
			</div>
			<form action="" method="post" id="">
				<div class="modal-body">

					<p>Adicionar tempo na conta do usuário <b id="usuario_nome"></b></p>

					<div class="form-group">
						<input type="hidden" value="-1" id="usuario_codigo">
						<label for="">Tempo Adicional</label>
						<p>
							<label for="tempo_adicional_1"><input onchange="enable_input_meses();" value="7" id="tempo_adicional_1" name="tempo_adicional" type="radio"> 7 dias  <small>por -0,25 créditos</small></label><br>
							<label for="tempo_adicional_2"><input onchange="enable_input_meses();" value="15" id="tempo_adicional_2" name="tempo_adicional" type="radio"> 15 dias <small>por -0,5 créditos</small></label><br>
							<label for="tempo_adicional_3"><input onchange="enable_input_meses();" value="n" id="tempo_adicional_3" name="tempo_adicional" type="radio">
								<input type="number" onkeyup="atualizar_creditos_perdidos()" onkeydown="atualizar_creditos_perdidos()" style="width:5em" maxlength="4" width="2" size="4" id="quantidade_de_meses" disabled min="1"> meses 
								<small>por <span id="creditos_perdidos"></span></small>
							</label>
						</p>
					</div>


				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
					<button type="button" onclick="add_creditos();" class="btn btn-primary">Adicionar Crédito</button>
				</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php } ?>

<script>

function desativaUsuario() {
	if (confirm('Tem certeza que deseja desativar este usuário?')){
		var motivo_desativacao = prompt("Digite um motivo para o desativamento:");
		location.href='<?php echo URL; ?>/desativar/<?php echo $p[$pk]; ?>&motivo_desativacao='+motivo_desativacao;
	} 
}

function maisinfo(c){

	show_loading();
	$.post('page/usuario.php', {maisinfo:c}).done(function(data){
		console.log(data);
		data = JSON.parse(data);
		end_loading();
		$('#maisrevendedor').html(data.revendedor);
		$('#maisadministrador').html(data.administrador);
		$('#maiswhatsapp').html(data.telefone);
		$('#maisquemcadastrou').html(data.quemcadastrou);
		$('#maissenha').html('Criptografada');
		$('#maisinfo').modal('show');

	}).fail(function(data){
		console.log(data);
		end_loading();
	});

		/*
		$('#maisrevendedor').html(r);
		$('#maisadministrador').html(a);
		$('#maiswhatsapp').html(w);
		$('#maisquemcadastrou').html(qc);

		$('#maisinfo').modal('show');
		*/
	}

	</script>

	<div class="modal fade" id="maisinfo" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Mais Informações</h4>
				</div>
				<form action="" method="post" id="">
					<div class="modal-body">

						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<th>Revendedor</th>
									<th>Administrador</th>
									<th>Whatsapp</th>
									<th>Quem cadastrou</th>
									<th>Senha</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td id="maisrevendedor"></td>
									<td id="maisadministrador"></td>
									<td id="maiswhatsapp"></td>
									<td id="maisquemcadastrou"></td>
									<td id="maissenha"></td>
								</tr>
							</tbody>
						</table>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
					</div>
				</form>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

	<script>

	
	function reordenar(coluna){
		$('#coluna').val(coluna);
		$('#reordenar').submit();
	}

	function consultar_usuario(user){

		$.post('page/usuario.php', {request_consultas:user}).done(function(res){

			$('#resultado_consulta').html(res);
			$('#infos_modal').modal('show');
			console.log(res);

		}).fail(function(res){
			console.log(res);
		});

	}

	<?php if($_SESSION['tipo'] == 3) { ?> 
		function atualizar_creditos_perdidos(){

			$('#creditos_perdidos').html('-' + $('#quantidade_de_meses').val() + ' créditos');

		}

		function add_creditos(){

			var novo_tempo = $('input[name="tempo_adicional"]:checked').val();
			var meses      = $('#quantidade_de_meses').val();
			var usuario    = $('#usuario_codigo').val();

			show_loading();
			$.post('page/inicio.php', {tempo_novo:novo_tempo, meses:meses, usuario:usuario}).done(
				function(res){
					alert(res);
					console.log(res);
					end_loading();
					$('#modal_credito').modal('hide');
					location.href='<?php echo URL; ?>/usuario';
				}).fail(function(res){
					console.log(res);
					end_loading();
				});


			}

			function enable_input_meses(){

				var checked = $('#tempo_adicional_3').prop('checked');
				if(checked)
					$('#quantidade_de_meses').attr('disabled', false);
				else{
					$('#quantidade_de_meses').attr('disabled', true);
					$('#quantidade_de_meses').val('');
					$('#creditos_perdidos').html('');
				}


			}

			function adicionar_credito(usuario, usuario_nome){

				$('#usuario_nome').html(usuario_nome);
				$('#usuario_codigo').val(usuario);
				$('#modal_credito').modal('show');

			}
			<?php } ?>
			</script>