<?php

if(!$_SESSION) @session_start();

if(!isset($_SESSION['usuario']))
	die("Credenciais inválidas!");

if($_POST['dismiss']){

	include('../class/Conexao.class.php');
	$con         = new Conexao();
	$con->update('comprovante', array('confirmado'=>1), intval($_POST['dismiss']));
	die(1);
}

if($_POST['recarregar'] && isset($_SESSION['usuario'])){

	include('../class/Conexao.class.php');
	$con         = new Conexao();

	if($_SESSION['tipo'] != 2 && $_SESSION['tipo'] != 4)
		die("Apenas administradores podem recarregar créditos de usuários.");
	else{

		// verifica se o usuario possui ao administrador
		$usuario = intval($_POST['usuario']);

		$adm = $con->select('administrador')->from('usuario')->where("codigo = '$usuario'")->limit(1)->executeNGet('administrador');

		if($adm != $_SESSION['usuario'] && $_SESSION['tipo'] != 4)
			die("Você não pode adicionar créditos à este usuário porque ele pertence à outro administrador.");
		else{
			$credito = floatval($_POST['credito']);
			$con->insert('revendedor_credito', array('valor'=>$credito, 'usuario'=>$usuario, 'data'=>date('Y-m-d H:i:s'), 'observacao'=>"Adição de crédito."));
			die("Créditos adicionados com sucesso!");
		}

	}

}

if($_SESSION['tipo'] == 1 || $_SESSION['tipo'] == 3){
	die('<script>location.href="'.URL.'/painel";</script>');
}

if($_POST['consultar_extrato']){

	include('../class/Conexao.class.php');
		$con         = new Conexao();

					$usuario = intval($_POST['consultar_extrato']);

					$extrato = $con->select('rc.*, u.usuario')
					->from("revendedor_credito rc left join usuario u on u.codigo = rc.favorecido")
					->where("rc.usuario = '$usuario'")
					->orderby('data DESC')
					->executeNGet();


					?>
					<table class="table table-bordered table-striped">
						<tr>
							<td>Valor</td>
							<td>Motivo</td>
							<td>Favorecido</td>
							<td>Data</td>
						</tr>
						<?php foreach($extrato as $ex){ ?>
						<tr>
							<td><b><?php echo $ex['valor']; ?></b> créditos</td>
							<td><?php echo $ex['observacao']; ?></td>
							<td><?php echo $ex['usuario']; ?></td>
							<td><?php echo date("d/m/Y H:i", strtotime($ex['data'])); ?></td>
							
						</tr>
						<?php } ?>
					</table>

<?php die(); }

include("class/protect.function.php");
protect(array(2,4));

/* variaveis de ambiente */
$nome_p      = "Revendedores";
$tabela      = "usuario";
$pk          = "codigo";
$col_mostrar = array("nome"=>"Nome", "usuario"=>"Login", "plano"=>"Plano");
$col_pesquisar = $col_mostrar;
$pagina_cad  = "revendedor-cadastrar";

function show_info($d, $n, $pl = array()){

	switch($n){

		case 'plano':
			return $pl[$d];

		default:
			return $d;

	}
}

$con         = new Conexao();

if($_SESSION['tipo'] == 2){
	// se for um revendedor
	$sql_filter =  " and administrador = '".$_SESSION['usuario']."' ";
}elseif($_SESSION['tipo'] == 4){
	$sql_filter =  " ";
}

if($_POST['pesquisar']){

	$where = '';
	$q     = $con->escape($_POST['pesquisar']);

	foreach($col_pesquisar as $col=>$x) $where .= " $col like '%{$q}%' or";

	$where = substr($where, 0, -2);

	$dados       =  $con
	              ->select('*')
	              ->from($tabela)
	              ->where("deletado is null and tipo = 3 $sql_filter and codigo != '".$_SESSION['usuario']."'  and " . $where)
	              ->executeNGet();
}else
	$dados       =  $con
	              ->select('*')
	              ->from($tabela)
	              ->where("deletado is null and tipo = 3 $sql_filter and codigo != '".$_SESSION['usuario']."'")
	              ->executeNGet();

$planos = $con->select('*')->from('plano')->where("codigo in (select plano from usuario group by plano)")->executeNGet();
$pl_final = array();
foreach($planos as $p){
	$pl_final[$p['codigo']] = $p['nome'];
}

$numeros = $con->select('count(*) as n, revendedor')->from('usuario')->where('revendedor is not null and deletado is null and teste is null and tipo = 1')->groupby('revendedor')->executeNGet();
$final = array();
foreach($numeros as $obj){
	$final[$obj['revendedor']] = $obj['n'];
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
			<h1 class="page-header"><?php echo $nome_p; ?> <small><?php echo count($dados); ?></small></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/<?php echo $pagina_cad; ?>';" class="btn btn-primary btn-xs"><i class="glyphicon glyphicon-plus"></i> Adicionar Revendedor</button>

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
					


					<table class="table table-bordered table-stripped ">
			            <thead>
			              <tr>
			              	<th></th>
			              	<?php foreach($col_mostrar as $c) echo "<th>$c</th>"; ?>
			              	<th>Status</th>
			              	<th>Usuários Cadastrados</th>
			                <th></th>
			              </tr>
			            </thead>
			            <tbody>
			              <?php 
			              if(count($dados) > 0){
			                foreach($dados as $p){ ?>
			                <tr class="<?php if($p['inativo'] == 1 && $_SESSION['tipo'] == 2) echo 'bg-warning'; ?>">
			                	<td><?php echo $p[$pk]; ?></td>
			                  <?php foreach($col_mostrar as $k=>$c) echo "<td>".show_info($p[$k], $k, $pl_final)."</td>"; ?>
			                  <td><?php

			                  $t = $con->select('ultima_atividade, data_logout')->from('login')->where("usuario = '".$p['codigo']."'")->orderby("codigo desc")->limit(1)->executeNGet();
			                 
			                  if(strtotime($t['ultima_atividade']) > (time() - 600) && !$t['data_logout'])
				echo ' <span class="label label-success">online</span>';
			else
				echo ' <span class="label label-danger">offline</span>';
			?></td>
			                  <td><?php echo intval($final[$p[$pk]]); ?> <?php if( $p['max_usuarios']){ ?> de <?php echo $p['max_usuarios']; ?><?php } ?></td>
			                  <td class="text-right">
								

								
			                  	<buton class="btn btn-default btn-xs" onclick="maisinfo(<?php echo $p['codigo']; ?>);">+ info</buton>
			                  	<buton class="btn btn-default btn-xs" type="button" onclick="javascript: window.open('http://probusca.com/painel/log/0/<?php echo $p['usuario']; ?>', '_blank');">Logs</buton>

			                  	<button onclick="consultar_extrato(<?php echo $p['codigo']; ?>);" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-usd"></i></button>
								<?php if($p['inativo']){ ?>
									<?php if($_SESSION['tipo'] == 2){ ?>
				                  		<button onclick="if(confirm('Tem certeza que quer ativar este usuário?')) location.href='<?php echo URL; ?>/ativar/<?php echo $p[$pk]; ?>';" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-check"></i> Ativar</button>
			                  			
			                  		<?php } else { ?>
										<button onclick="location.href='<?php echo URL; ?>/ativar/<?php echo $p[$pk]; ?>';" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-check"></i> Ativar</button>
			                  		<?php } ?>
			                  	<?php }elseif($_SESSION['tipo'] == 2){ ?>
			                  		<button onclick="window.open('<?php echo URL; ?>/usuario/<?php echo $p[$pk]; ?>', '_blank');" class="btn btn-default btn-xs"><i class="glyphicon glyphicon-user"></i></button>
			                  		<button onclick="if(confirm('Tem certeza que quer desativar este usuário?')) location.href='<?php echo URL; ?>/desativar/<?php echo $p[$pk]; ?>';" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i> Desativar</button>
			                  	<?php } ?>
			                  	<?php if(intval($final[$p[$pk]]) == 0 && ($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 2)){ ?>
			                  	<button onclick="location.href='<?php echo URL; ?>/deletar/revendedor/<?php echo $p[$pk]; ?>';" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Deletar</button>
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
			                <td class="text-center" colspan="6">Nenhum registro encontrado para o termo <strong><?php echo $_POST['pesquisar']; ?></strong>.</td>
			                <?php } else { ?>
							<td class="text-center" colspan="6">Nenhum registro encontrado.</td>
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

<div class="modal fade" id="modal_extrato" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Extrato de Créditos</h4>
      </div>
      <div class="modal-body">

      	<div class="form-group form-inline">
        	<label for="">Adicionar Créditos</label>
        	<input type="hidden" id="usu_codigo" value="-1">
        	<input type="text" name="creditos" id="creditos" class="form-control">
        	<button onclick="add_creditos();" class="btn btn-success">Adicionar Créditos</button>
        </div>


        <p id="div_extrato"></p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
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
			$('#maissenha').html(data.senha);
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

	function add_creditos(){


		var creditos = parseFloat($('#creditos').val());
		if(creditos == 0)
			return;

		else if(!confirm('Confirma a adição de ' + creditos + ' créditos?'))
			return;

		show_loading();
		$.post('page/revendedor.php', {credito:$('#creditos').val(), usuario:$('#usu_codigo').val(), recarregar:true})
		.done(function(resposta){
			console.log(resposta);
			alert(resposta);
			end_loading();
			$('#creditos').val('');
			consultar_extrato($('#usu_codigo').val());
		})
		.fail(function(resposta){
			console.log(respostas);
			alert(resposta);
			end_loading();
		});

	}
	
	function consultar_extrato(revendedor){

		$('#usu_codigo').val(revendedor);

		$.post('page/revendedor.php', {consultar_extrato:revendedor}).done(function(res){
			console.log(res);
			$('#div_extrato').html(res);
			$('#modal_extrato').modal('show');
		}).fail(function(res){
			console.log(res);
		});

	}
</script>