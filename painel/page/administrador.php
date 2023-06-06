<?php

if($_POST['ver_usuario'] && $_POST['codigo']){

	include("../class/Conexao.class.php");
	$con = new Conexao();

	$tipo = intval($_POST['tipo']);
	$pai  = intval($_POST['codigo']);

	if(!$_SESSION) @session_start();

	if($_SESSION['tipo'] != 4)
		die("Você não pode visualizar essas informações.");

	$usuarios = $con
	->select('u.nome, u.usuario, u.senha,u.vencimento, p.nome as plano')
	->from('usuario u left join plano p on p.codigo = u.plano')
	->where("(u.quemcadastrou = '$pai' or u.administrador = '$pai') and u.tipo = '$tipo' and deletado is null")
	->orderby('u.data DESC')
	->executeNGet();

	?>
	<table class="table table-bordered table-responsive">
		<thead>
			<tr>
				<td>Nome</td>
				<td>Login</td>
				<td>Senha</td>
				<?php if($tipo == 1) { ?>
				<td>Vencimento</td>
				<?php } ?>
				<td>Plano</td>
			</tr>
		</thead>
		<tbody>
		
	<?php foreach($usuarios as $u){ ?>

			<tr >
				<td><?= $u['nome'] ?></td>
				<td><?= $u['usuario'] ?></td>
				<td><?= $u['senha'] ?></td>
				<?php if($tipo == 1) { ?>
				<td <?php if(strtotime($u['vencimento']) <= time()) echo "class='bg-warning'"; ?>><?= date('d/m/Y H:i', strtotime($u['vencimento'])) ?></td>
				<?php } ?>
				<td><?= $u['plano'] ?></td>
			</tr>
	
	<?php } ?>
		
			
		</tbody>
	</table>
	<?php
	die();
}

include("class/protect.function.php");
protect(array(4));

$router = new Router($_GET['p']);
$con         = new Conexao();

/* variaveis de ambiente */
$nome_p      = "Administradores";
$tabela      = "usuario";
$pk          = "codigo";
$col_mostrar = array("nome"=>"Nome", "usuario"=>"Login", "plano"=>"Plano", "senha"=>"Senha");
$col_pesquisar = $col_mostrar;
$pagina_cad  = "administrador-cadastrar";


function show_info($d, $n,  $array_completo, $pl = array()){

	switch($n){

		case 'senha':
			return 'Criptografada';

		case 'plano':
			return $pl[$d];

		default:
			return $d;

	}
}

$sql_final = '';
if($_POST['reordenar'])
	$sql_final = "order by ".$_POST['coluna']." ".$_POST['order'];
else
	$sql_final = "";

if($_POST['pesquisar']){

	$where = '';
	$q     = $con->escape($_POST['pesquisar']);

	foreach($col_pesquisar as $col=>$x) $where .= " $col like '%{$q}%' or";

	$where = substr($where, 0, -2);

	$dados       =  $con
	              ->select('usuario.*')
	              ->from("usuario")
	              ->where("(deletado is null and teste is null and tipo = 2) and (" . $where. " ) ".$sql_final)
	              ->executeNGet();
}else
	$dados       =  $con
	              ->select('usuario.*')
	              ->from("usuario")
	              ->where("(deletado is null and teste is null and tipo = 2) ".$sql_final)
	              ->executeNGet();


//left join (select data_logout, ultima_atividade, usuario as login_usuario from login  group by usuario order by codigo desc limit 1) as l on l.login_usuario = usuario.codigo
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
			<h1 class="page-header"><?php echo $nome_p; ?> <small><?php echo count($dados);  ?></small></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
				
							<button type="button" onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i> Adicionar Administrador</button>
							
							<span class="pull-right">
								<input type="text" value="<?php echo $_POST['pesquisar']; ?>" placeholder="Pesquisar <?php echo $nome_p; ?>..." name="pesquisar" class="form-control input-xs">
								<button type="submit" class="btn-success btn btn-xs"><i class="glyphicon glyphicon-search"></i> Pesquisar</button>
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
						<input type="hidden" id="order" value="<?php if(!$_POST['order'] || $_POST['order'] != 'asc') echo 'asc'; else echo 'desc'; ?>" name="order">
					</form>
					
					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			              	<th><a onclick="reordenar('nome');" href="javascript: void(0);">Nome</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'nome') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'nome') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>
			              	<th><a onclick="reordenar('usuario');" href="javascript: void(0);">Login</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'usuario') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'usuario') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>
			              	<th><a onclick="reordenar('plano');" href="javascript: void(0);">Plano</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'plano') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'plano') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>
			              	
			              	<th>Senha</th>
							<th>Status</th>
			              	<th><a onclick="reordenar('data');" href="javascript: void(0);">Data de Cadastro</a> <?php if($_POST['order'] == 'asc' && $_POST['coluna'] == 'data') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet\"></i>"; elseif($_POST['order'] == 'desc' && $_POST['coluna'] == 'data') echo "<i class=\"glyphicon glyphicon-sort-by-alphabet-alt\"></i>"; ?></th>
							
							<th>Nº de Usuários</th>
			
			                <th></th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($dados) > 0){
			                foreach($dados as $p){ ?>
			                <tr class="<?php if($p['inativo'] == 1 && $_SESSION['tipo'] == 2) echo 'bg-warning'; ?>">
			                  <?php foreach($col_mostrar as $k=>$c) echo "<td>".show_info($p[$k], $k, $p, $pl_final)."</td>"; ?>
			                  <td><?php
			                  //echo date('d/m/Y', strtotime($t['ultima_atividade']));
			                  $t = $con->select('ultima_atividade, data_logout')->from('login')->where("usuario = '".$p['codigo']."'")->orderby("codigo desc")->limit(1)->executeNGet();
			                 
			                  if(strtotime($t['ultima_atividade']) > (time() - 600) && !$t['data_logout'])
				echo ' <span class="label label-success">online</span>';
			else
				echo ' <span class="label label-danger">offline</span>';
			?></td>
			                  <td><?php echo date('d/m/Y H:i', strtotime($p['data'])); ?></td>

			                  <td>
			                  	<?php 
			                  $n_users = $con->select('count(*) as n, tipo')->from('usuario')->where("deletado is null and (quemcadastrou = '".$p['codigo']."' or administrador = '".$p['codigo']."')")->groupby('tipo')->executeNGet(); 
			                  $tipos = array(1=>'Usuário(s)', 2=>'Administradores', 3=>'Revendedores');

			                  $total_users =0; ?>
			                  	<?php foreach($n_users as $n){ $total_users += $n['n']; ?>
								<button type="button" onclick="ver_usuarios(<?php echo $p['codigo']; ?>, <?php echo $n['tipo']; ?>);" class="btn btn-default btn-xs"><?php echo $n['n']." ".$tipos[$n['tipo']]; ?></button><br>
								<?php } ?>
			                  	</td>

			                  <td class="text-right">

			                  	
								
								<?php if($p['inativo']){ ?>
								<button onclick="location.href='<?php echo URL; ?>/onlyAtivar/<?php echo $p['codigo']; ?>';" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-user"></i> Ativar</button>
								<?php } else { ?>
			                  	<button onclick="location.href='<?php echo URL; ?>/desativar/<?php echo $p['codigo']; ?>';" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-user"></i> Desativar</button>
								<?php } ?>

								<button onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>/<?php echo $p[$pk]; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i> Editar</button>
								
								<?php if($total_users == 0){ ?>
			                  	<button onclick="location.href='<?php echo URL; ?>/deletar/administrador/<?php echo $p[$pk]; ?>';" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Deletar</button>
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

<div class="modal fade" id="modal_ver_usuarios" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>

	      <div class="modal-body">
			
			<div id="ver_usuarios_resultado" class="form-group"></div>

	      </div>X
	      <div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
	      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>

	function ver_usuarios(id, tipo){

		show_loading();
		$.post('page/administrador.php', {ver_usuario:true, codigo:id, tipo:tipo}).done(function(res){

			end_loading();
			console.log(res);

			$('#modal_ver_usuarios .modal-body').html('<div id="ver_usuarios_resultado" class="form-group"></div>');
			if(tipo == 1){
				$('#modal_ver_usuarios .modal-title').html("Usuários");
				$('#modal_ver_usuarios .modal-body').prepend('<p><button type="button" onclick="window.open(\'<?php echo URL; ?>/usuario/'+id+'\', \'_blank\');" class="btn btn-default"><i class="glyphicon glyphicon-user"></i> Ver Página de Usuários Detalhada</button></p>');
			}
				
			else if(tipo == 2)
				$('#modal_ver_usuarios .modal-title').html("Administradores");
			else if(tipo == 3)
				$('#modal_ver_usuarios .modal-title').html("Revendedores");

			$('#ver_usuarios_resultado').html(res);
			$('#modal_ver_usuarios').modal('show');
			
		}).fail(function(res){
			end_loading();
			console.log(res);
		})

	}

	
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