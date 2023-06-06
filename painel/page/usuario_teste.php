<?php

if($_POST['deletar_testes']){

	include("../class/Conexao.class.php");
	$con = new Conexao();

	if(!$_SESSION)
		@session_start();

	if($_SESSION['tipo'] == 3){
		// se for admin ou revendedor, pode deletar

		$con->execute("
			update usuario 
			set deletado = 1 
			where teste = 1 
			and vencimento <= NOW() 
			and revendedor = '".$_SESSION['usuario']."'");

	}elseif($_SESSION['tipo'] == 4){

		$con->execute("
			update usuario 
			set deletado = 1 
			where teste = 1 
			and vencimento <= NOW()");

	}elseif($_SESSION['tipo'] == 2){

		$con->execute("
			update usuario 
			set deletado = 1 
			where teste = 1 
			and vencimento <= NOW() 
			and (administrador = '".$_SESSION['usuario']."' or quemcadastrou = '".$_SESSION['usuario']."')");
		
	}

	die();

}

if($_POST['request_consultas']){

	include("../class/Conexao.class.php");
	$con = new Conexao();
	$usuario = intval($_POST['request_consultas']);
	$plataformas = $con->select('p.*')->from('usuario_credito uc, plataforma p')->where("usuario = '$usuario' and p.codigo = uc.plataforma")->groupby('plataforma')->executeNGet();
	foreach($plataformas as $pl){
		$resultado[$pl['codigo']]['credito'] = $con->select('sum(credito) as credito')->from('usuario_credito')->where("usuario = '$usuario' and plataforma = '".$pl['codigo']."'")->limit(1)->executeNGet('credito');
		$resultado[$pl['codigo']]['gasto']   = $con->select('count(*) as credito')->from('usuario_credito')->where("usuario = '$usuario' and plataforma = '".$pl['tipo']."'")->limit(1)->executeNGet('credito');
		$resultado[$pl['codigo']]['nome']    = $pl['nome'];
	}

	$logs   = $con->select('l.*')
	->from('login l')
	->where("l.usuario = '$usuario' and l.ip is not null and l.data_login >= '".date("Y-m-d", strtotime("-7 days"))."'")
	->orderby('ultima_atividade DESC')
	->executeNGet();


	?>
					
	<table class="table table-bordered table-stripped">
	<?php foreach($resultado as $r){ ?>
						<tr>
							<td><?php echo $r['nome']; ?></td>
							<td><?php echo $r['gasto']; ?> de <?php echo $r['credito']; ?></td>
						</tr>
						<?php } ?>
					</table>

	<h2>Acessos nos últimos 7 dias</h2>

	<table class="table table-bordered table-stripped">
		<?php foreach($logs as $r){ ?>
		<tr>
			<td>
				<?php if(date('Y-m-d', strtotime($r['data_login'])) == date('Y-m-d')) echo 'Hoje '.date("H:i", strtotime($r['data_login'])); echo date('d/m/Y H:i', strtotime($r['data_login'])); ?> 
				<?php if( (time() - 60) < strtotime($r['ultima_atividade']) && $r['ultima_atividade']) echo '<span class="label label-success">Logado</span>'; ?>
			</td>
			<td><a target="_blank" href="https://check-host.net/ip-info?host=<?php echo $r['ip']; ?>"><?php echo $r['ip']; ?></a></td>
		</tr>
		<?php } ?>
	</table>

<?php

	die();

}


if($_SESSION['tipo'] == 1){
	die('<script>location.href="'.URL.'/painel";</script>');
}

include("class/protect.function.php");
protect(array(2,3,4));

$router = new Router($_GET['p']);
$con         = new Conexao();

if($_SESSION['tipo'] == 3){
	// se for revendedor
	$max_users = $con->select('max_contas_teste')->from('usuario')->where("codigo = '".$_SESSION['usuario']."' and deletado is null")->limit(1)->executeNGet('max_contas_teste');
	$dados     = $con->select('u.*, p.nome as plano')->from('usuario u, plano p')->where("u.teste = 1 and p.codigo = u.plano and u.revendedor = '".$_SESSION['usuario']."' and u.deletado is null")->executeNGet();

}elseif($_SESSION['tipo'] == 2){
	$max_users = 999999;
	$dados     = $con
	->select('u.*, p.nome as plano, r.usuario as nome_usuario_revendedor')
	->from('plano p, usuario u left join usuario r on r.codigo= u.revendedor')
	->where("u.teste = 1 and p.codigo = u.plano and u.deletado is null and (u.revendedor in (select codigo from usuario where administrador = '".$_SESSION['usuario']."') or u.administrador = '".$_SESSION['usuario']."')")
	->executeNGet();
}elseif($_SESSION['tipo'] == 4){
	$max_users = 999999;
	$dados     = $con
	->select('u.*, p.nome as plano, r.usuario as nome_usuario_revendedor')
	->from('plano p, usuario u left join usuario r on r.codigo = u.revendedor')
	->where("u.teste = 1 and p.codigo = u.plano and u.deletado is null")
	->executeNGet();
}

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Contas de Teste</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Contas de Teste <small><?php echo count($dados);  ?></small></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<?php if(count($dados) < $max_users || $_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4){ ?>
							
							<button type="button" onclick="location.href='<?php echo URL; ?>/usuario_teste-cadastrar';" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-plus"></i> Adicionar Conta Teste</button>
							<?php 

							}

							if($router->param(0)){
								echo "Revendedor: ". $con->select('nome')->from('usuario')->where("codigo = '".intval($router->param(0))."'")->limit(1)->executeNGet('nome');
							}
							?>
							<button onclick="deletar_testes();" type="button" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Deletar Contas de Teste Vencidas</button>
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

					<p><?php echo "Você ainda pode cadastrar <b>".($max_users-count($dados))." usuário(s) de teste.</b>"; ?></p>

					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			              	<th>Nome</th>
			              	<th>Login</th>
			              	<th>Plano</th>
			              	<th>Senha</th>

			                <th>Vencimento</th>
			              	<?php if($_SESSION['tipo'] == 2){ ?>
			                <th>Revendedor</th>
			                <?php } ?>
			                <th></th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($dados) > 0){
			                foreach($dados as $p){ ?>
			                <tr class="<?php if(strtotime($p['vencimento']) < time()) echo 'bg-warning'; ?> <?php if(($p['inativo'])) echo 'bg-danger'; ?>">
			   
			                  <td><?php echo $p['nome']; ?></td>
			                  <td><?php echo $p['usuario']; ?></td>
			                  <td><?php echo $p['plano']; ?></td>
			                  <td>Criptografada</td>
			                  <td><?php echo date('d/m/Y H:i', strtotime($p['vencimento'])); ?></td>
			                  <?php if($_SESSION['tipo'] == 2){ ?>
			                  <td><?php echo $p['nome_usuario_revendedor']; ?></td>
			                  <?php } ?>
			                  <td class="text-right">
			                  	<button class="btn btn-default btn-xs" onclick="maisinfo(<?php  echo $p['codigo'] ?>);" type="button">+ info</button>
								<?php if($p['inativo']){ ?>
									<?php if($_SESSION['tipo'] == 2){ ?>
				                  		<button onclick="if(confirm('Tem certeza que quer ativar este usuário?')) location.href='<?php echo URL; ?>/onlyAtivar/<?php echo $p['codigo']; ?>';" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-check"></i> Ativar</button>
			                  		<?php } else { ?>
										<button onclick="location.href='<?php echo URL; ?>/onlyAtivar/<?php echo $p['codigo']; ?>';" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-check"></i> Ativar</button>
			                  		<?php } ?>
			                  	<?php }elseif($_SESSION['tipo'] == 2){ ?>
			                  		<button onclick="if(confirm('Tem certeza que quer desativar este usuário?')) location.href='<?php echo URL; ?>/desativar/<?php echo $p['codigo']; ?>';" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-check"></i> Desativar</button>
			                  	<?php } ?>
			                  	<button onclick="location.href='<?php echo URL; ?>/deletar/usuario/<?php echo $p['codigo']; ?>';" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Deletar</button>
			                  	<?php if(strtotime($p['vencimento']) > time()){ ?>
			                    <button onclick="location.href='<?php echo URL; ?>/usuario_teste-cadastrar/<?php echo $p['codigo']; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-pencil"></i> Editar</button>
			                  	
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
					
					</tr>
				</thead>
				<tbody>
					<tr>
						<td id="maisrevendedor"></td>
						<td id="maisadministrador"></td>
						<td id="maiswhatsapp"></td>
						<td id="maisquemcadastrou"></td>
					
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

	function deletar_testes(){

		$.post('page/usuario_teste.php', {deletar_testes:true}).done(function(res){
			console.log(res);
			alert('Usuários de teste deletados!');
			location.href='<?php echo URL; ?>/usuario_teste';
		}).fail(function(res){
			console.log(res);
			alert(res);
		});

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
</script>