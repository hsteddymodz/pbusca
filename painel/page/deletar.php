<?php

$router = new Router($_GET['p']);
$con    = new Conexao();



switch($router->param(0)){

	case 'administrador':
		$obj    = "Administrador";
		$retorno= 'administrador';
		$tabela = 'usuario';
		$pk     = $router->param(1);
		break;

	case 'conta_bancaria':
		$obj    = "Conta Bancária";
		$retorno= 'conta_bancaria';
		$tabela = 'conta_bancaria';
		$pk     = $router->param(1);
		break;

	case 'plano':
		$obj    = "Plano";
		$retorno= 'plano';
		$tabela = 'plano';
		$pk     = $router->param(1);
		break;

	case 'usuario':
		$obj    = "Usuário";
		$retorno= 'usuario';
		$tabela = 'usuario';
		$pk     = $router->param(1);
		break;

	case 'revendedor':
		$obj    = "Revendedor";
		$retorno= 'revendedor';
		$tabela = 'usuario';
		$pk     = $router->param(1);
		break;

}

if($router->param(2) == 'confirm'){

	$tabela = $con->escape($tabela);

	if($tabela == 'conta_bancaria'){
		$con->execute("delete from conta_bancaria where codigo = '$pk'");
		$retorno = 'conta';
	}

	elseif($retorno == 'administrador' && $pk > 0){

		$num_usuarios = $con->select('count(*) as n')->from('usuario')->where("administrador = '$pk' and deletado is null")->limit(1)->executeNGet('n');

		if($_SESSION['tipo'] != 4)
			echo "<script>alert('Você não é um administrador MASTER para deletar administradores.');</script>";
		elseif($num_usuarios == 0){
			$con->execute("update usuario set deletado = 1, quemdeletou = '".$_SESSION['usuario']."', quandodeletou = NOW() where codigo = '$pk'");
		}else{
			echo "<script>alert('Você não pode deletar este administrador porque ele possui usuários.');</script>";
		}

	}

	elseif($tabela == 'plano'){

		$num_usuarios = $con->select('count(*) as n')->from('usuario')->where("plano = '$pk' and deletado is null")->limit(1)->executeNGet('n');

		if($num_usuarios == 0){
			$con->execute("delete from plano_plataforma where plano = '$pk'");
			$con->execute("delete from plano where codigo = '$pk'");
		}else{
			echo "<script>alert('Você não pode deletar este plano porque alguns usuários o utilizam.');</script>";
		}

	}

	elseif($tabela == 'usuario' && $pk > 0){

		$admin_dono   = $con->select('administrador, tipo, teste')->from('usuario')->where("codigo = '$pk'")->limit(1)->executeNGet();

		if($_SESSION['tipo'] == 3 && !$admin_dono['teste']){
			// se for revendedor, verifica se esse usuario pertence ao revendedor
			die('<script>alert("Revendedores não podem deletar usuários."); location.href="'.URL.'";</script>');
			
		}elseif($_SESSION['tipo'] == 3 && $admin_dono['teste']){

			$con->execute("update usuario set deletado = 1, quemdeletou = '".$_SESSION['usuario']."', quandodeletou = NOW() where codigo = '$pk'");
			$retorno = "usuario_teste";
		}elseif($_SESSION['tipo'] == 2 || $_SESSION['tipo'] == 4){

			// verifica se aquele usuario pertence a um revendedor que pertence ao administrador
			
			if($admin_dono['teste'] == 1){

				$con->execute("update usuario set deletado = 1, quemdeletou = '".$_SESSION['usuario']."', quandodeletou = NOW() where codigo = '$pk'");
				$retorno = 'usuario_teste';

			}elseif($admin_dono['tipo'] == 3){

				// admin deletando um revendedor

				// verifica quantos usuarios cadastrados ele possui
				$contar_usuarios_do_revendedor = $con->select('count(*) as n')->from('usuario')->where("revendedor = '$pk' and deletado is null")->limit(1)->executeNGet('n');

				if($contar_usuarios_do_revendedor > 0) // se possuir algum, nao deleta
					die("<script>alert('Você não pode deletar esse revendedor porque ele possui usuários cadastrados. Delete os usuários dele antes.'); location.href='".URL."/revendedor';</script>");
				else{// se nao possuir nenhum, pode deletar7
					$con->execute("update usuario set deletado = 1, quemdeletou = '".$_SESSION['usuario']."', quandodeletou = NOW() where codigo = '$pk'");
				}

			} elseif($admin_dono['tipo'] == 1){

				// admin deletando um usuario comum
				$con->execute("update usuario set deletado = 1, quemdeletou = '".$_SESSION['usuario']."', quandodeletou = NOW() where codigo = '$pk'");
				$vencimento = strtotime($con->select('vencimento')->from('usuario')->where("codigo = '$pk'")->limit(1)->executeNGet('vencimento'));
				$ultimo_credito = $con->select('codigo')->from('revendedor_credito')->where("favorecido = '$pk'")->orderby('codigo desc')->limit(1)->executeNGet('codigo');

				if($vencimento >= time()){
					// devolve ao revendedor os creditos referentes ao cadastro daquele usuario
					$con->execute("delete from revendedor_credito where codigo = '$ultimo_credito'");
				}
				

			}else if($admin_dono['administrador'] != $_SESSION['usuario']) // se o usuario nao pertence aquele administrador, nao pode deletar
				die( "<script>alert('Este usuário não pertence à um revendedor seu. Você não pode deletá-lo.'); location.href='".URL."/usuario';</script>");

		}
		

		$tipo = $con->select('tipo, teste')->from("usuario")->where("codigo = '".$pk."'")->limit(1)->executeNGet();

		 if($tipo['teste'])
		    $retorno = 'usuario_teste';
		  elseif($tipo['tipo'] == 3)
		    $retorno = 'revendedor';
		  elseif($tipo['tipo'] == 1)
		    $retorno = 'usuario';
		  elseif($tipo['tipo'] == 2)
		    $retorno = 'administrador';


	}

	die("<script>location.href='".URL."/".$retorno."';</script>");

}

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li><a href="<?php echo URL; ?>/<?php echo $retorno; ?>"><?php echo $obj; ?></a></li>
			<li class="active">Deletar <?php echo $obj; ?></li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Deletar <?php echo $obj; ?></h1>
		</div>
	</div><!--/.row-->


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php if($obj != 'autor'){ ?>
					Tem certeza que deseja deletar?
					<?php }else{ ?>
					Ao deletar este autor, todos as postagens dele também serão deletadas. Tem certeza disso?
					<?php } ?>
				</div>
				<div class="panel-body">
					<p><small>Esta ação não poderá ser desfeita.</small></p>
					<button type="button" onclick="location.href='<?php echo URL; ?>/<?php echo $retorno; ?>';" class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-remove"></i> Não</button>
					<button type="button" onclick="location.href='<?php echo URL; ?>/deletar/<?php if($retorno == 'administrador') echo 'administrador'; else echo $tabela; ?>/<?php echo $pk; ?>/confirm';" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-check"></i> Sim</button>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->