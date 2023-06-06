<?php

if(!isset($_SESSION)) @session_start();
if($_POST['tempo_novo'] && $_POST['usuario'] && ($_SESSION['tipo'] == 4 || $_SESSION['tipo'] == 3)){

	include('../class/Conexao.class.php');
	include("../class/Credito.class.php");

	$valor = $_POST['tempo_novo'];
	$meses = $_POST['meses'];
	$usu   = intval($_POST['usuario']);
	$prazo = 0;

	$con = new Conexao();
	$creditos = new Credito($con);

	if($valor == 'n'){
		// meses
		$valor = intval($meses);
		$prazo = (30*$valor);

	}else{
		$valor = ($valor == 7)? 0.25:0.5;
		$prazo = $_POST['tempo_novo'];
	}

	if(!$_SESSION)
		@session_start();

	if(!$_SESSION['usuario'])
		die("Faça login para adicionar crédito!");

	$credito = $con->select('sum(valor) as v')->from('revendedor_credito')->where("usuario = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('v');

	if($credito < $valor){
		die("Créditos insuficientes! Você possui $credito créditos.");
	}else{

		$valor = $valor * -1;
	
		$verificarSeguranca = $con->select('revendedor, administrador')->from('usuario')->where("codigo = '$usu'")->limit(1)->executeNGet();

		if($verificarSeguranca['revendedor'] != $_SESSION['usuario'] && $verificarSeguranca['administrador'] != $_SESSION['usuario'] && $_SESSION['tipo'] != 4)
			die('Você não tem permissão para essa ação');

		$novo_prazo = date('Y-m-d H:i:s', ( time() + ($prazo * 86400) ));

		if($con->update('usuario', array('vencimento'=> $novo_prazo), $usu)){
			//die("creditos $usu $credito $valor $prazo $prazo_atual ". $novo_prazo);
			$con->insert('revendedor_credito', array(
				'valor'=>$valor, 
				'usuario'=>$_SESSION['usuario'], 
				'data'=>date('Y-m-d H:i:s'), 
				'observacao'=>"Adição de crédito.", 
				'favorecido'=>$usu
				)
			);
			
			die($prazo . " dias adicionados com sucesso!");

		}else
			die('Falha ao atualizar data de vencimento. ');

		
	}

}
include('class/Conexao.class.php');
$con = new Conexao();
$todos_creditos = $con
	->select("pp.numero as credito, pp.plataforma, p.nome")
	->from('plano_plataforma pp, plataforma p')
	->where("plano = {$_SESSION['plano']} and p.codigo = pp.plataforma")
	->executeNGet();

$todos_debitos  = $con
	->select("count(*) as debito, p.codigo as plataforma")
	->from('usuario_consulta uc, plataforma p')
	->where("usuario = '{$_SESSION['usuario']}' and date(data) = '".date('Y-m-d')."' and p.tipo = uc.plataforma")
	->groupby('plataforma')
	->executeNGet();

$credito_final = array();
$debito_final  = array();

$plataformas = $con
	->select('*')
	->from('plataforma')
	->where("desativar is null or desativar = 0")
	->orderby("ordem DESC")
	->executeNGet();

$temp = $con->select('p.tipo as plataforma, c.valor as valor, DATE_FORMAT(c.data, "%d/%m/%Y %h:%i") as data, DATE_FORMAT(c.vencimento, "%d/%m/%Y") as vencimento')->from('credito c, plataforma p')->where("c.usuario = '{$_SESSION['usuario']}' and p.tipo = c.plataforma and c.valor > 0")->orderby('p.ordem ASC')->executeNGet();

$creditos_plataforma = array();
foreach($temp as $t){
	if(!isset($creditos_plataforma[$t['plataforma']]))
		$creditos_plataforma[$t['plataforma']] = intval($t['valor']);
	else
		$creditos_plataforma[$t['plataforma']] += intval($t['valor']);
}


foreach($todos_creditos as $c){
	$credito_final[$c['plataforma']] = $c;
}
foreach($todos_debitos as $c){
	$debito_final[$c['plataforma']]  = $c;
}

$infos = $con->select('*')->from('login')->where(" data_logout IS NOT NULL AND usuario = '{$_SESSION['usuario']}' ")->orderby('codigo DESC')->limit(1)->executeNGet();

$contaTeste = $con->select('*')->from('usuario')->where("codigo = '{$_SESSION['usuario']}'")->limit(1)->executeNGet();


?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">	

	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Bem vindo<?php if($_SESSION['tipo'] == 2) echo ', Administrador'; else echo ", ".$_SESSION['nome']; ?>.</h1>
			<?php if ($contaTeste['teste'] == 1) {
				echo '<h2 style="color:red">Comprar Login <strong>APENAS</strong> nos contatos oficiais</h2>'; ?>
				<br>
				<p>Para adquirir sua conta, entre com contato com um dos seguintes números:</p>
				<ul style="font-size:14px"> 
							<li>Whats: +5076225-4167 (MAX)</li>
							<li>Whats: (11) 96411-5286 (JOAO)</li>
							<li>Whats: (11) 94481-7454 (JULIANO)</li>
							<li>Whats: (11) 97408-4269 (LEONARDO LINS)</li>
				</ul>
				<p><strong>NÃO</strong> nos responsabilizamos por logins adquiridos por contatos não oficiais</p>
			<?php
			}
			?>

			<span class="pull-right">
				<small>Última atividade: <?php echo $infos['ultima_atividade'];?> - IP que realizou login: <a target='_blank' href="https://check-host.net/ip-info?host=<?php echo $infos['ip'];?>"><?php echo $infos['ip'];?> </a></small>
			</span>
		</div>
	</div><!--/.row-->

	



	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							Em qual sistema você quer consultar informações?
							<?php if($_SESSION['vencimento']){ ?>
							<span class="pull-right">
								<small>Sua conta vence <b><?php if(date('d/m/Y') == date('d/m/Y', $_SESSION['vencimento'])) echo "às " . date('H:i', $_SESSION['vencimento']); else echo "em ".date('d/m/Y H:i', $_SESSION['vencimento']); ?></b></small>
							</span>
							<?php } ?>
						</div>
						
					</form>
				</div>
				<div class="panel-body">
					<?php 

					foreach($plataformas as $p){

						$debito = intval($debito_final[$p['codigo']]['debito']);
						$credito = intval($credito_final[$p['codigo']]['credito']);
						$n_consultas = $credito - $debito;

						$n_creditos = isset($creditos_plataforma[$p['tipo']])? $creditos_plataforma[$p['tipo']]:0;

						if($credito > 0 || $n_creditos > 0 || $_SESSION['tipo'] == 4){

							//echo $_SESSION['usuario'];
						?>
						
							<div class="col-lg-3 modulo col-md-4 col-sm-4 col-xs-12 text-center">
								
								<?php if(intval($p['manutencao']) == 1){ ?>
									<div class="manutencao" style="position: absolute; background-color: red; color: white;font-style:italic;left: 15px;right: 15px;top: 20px;">EM MANUTENÇÃO</div>
								<?php } ?>

								<?php if(intval($p['manutencao']) == 2){ ?>
									<div class="manutencao" style="position: absolute; background-color: red; color: white;font-style:italic;left: 15px;right: 15px;top: 20px;">ATUALIZANDO</div>
								<?php } ?>

								<?php if(intval($p['manutencao']) == 3){ ?>
									<div class="manutencao" style="position: absolute; background-color: green; color: white;font-style:italic;left: 15px;right: 15px;top: 20px;">ONLINE</div>
								<?php } ?>

								<a  data-toggle="tooltip" title="<?php if($n_creditos > 0) echo "Restam: $n_creditos consultas"; else echo $debito." / ".$credito; ?>" data-placement="top"  <?php 
								if($p['manutencao'] == '1' and $_SESSION['tipo'] != 4)
									echo 'href="javascript: alert(\'Em manutenção!\');"';
								else if($n_consultas > 0 || $_SESSION['tipo'] == 2  || $_SESSION['tipo'] == 4 || $n_creditos > 0) 
									echo 'href="'.$p['link'] .'"'; 
								else echo 'href="javascript: void(0);" onclick="limiteConsultas();"'; 
								?> >
									<img src="<?php echo URL_INDEX; ?>/painel/upload/<?php echo $p['logo']; ?>" alt="<?php echo $p['nome']; ?>" class="img-plataforma img-responsive">
								</a>

							</div>
						
					<?php } } ?>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->
<?php if($_SESSION['tipo'] == 2){ 

$dados       =  $con
	              ->select('u.*, c.codigo as sol, c.arquivo')
	              ->from('usuario u right join comprovante c on c.usuario = u.codigo and c.confirmado is null')
	              ->where("u.administrador = '{$_SESSION['usuario']}'  ")
	              ->executeNGet();

$planos = $con->select('*')->from('plano')->where("codigo in (select plano from usuario group by plano)")->executeNGet();
$pl_final = array();
foreach($planos as $p){
	$pl_final[$p['codigo']] = $p['nome'];
}


?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">	

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							Revendedores solicitando Créditos
						</div>
						
					</form>
				</div>
				<div class="panel-body">
					<table class="table  table-bordered">
						<thead>
							<tr>
								<th>Nome</th>
								<th>Usuário</th>
								<th>Plano</th>
								<th>Vencimento</th>
								<th>Créditos</th>
								<th>Comprovante</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($dados as $d){ ?>
							<tr>
								<td><?php echo $d['nome']; ?></td>
								<td><?php echo $d['usuario']; ?></td>
								<td><?php echo $pl_final[$d['plano']]; ?></td>
								<td><?php echo date('d/m/Y H:i', strtotime($d['vencimento'])); ?></td>
								<td><?php echo $con->select('sum(valor) as v')->from('revendedor_credito')->where("usuario = '".$d['codigo']."'")->limit(1)->executeNGet('v'); ?></td>
								<td><a href="<?php echo URL; ?>/comprovante/<?php echo $d['arquivo']; ?>" target="_Blank">abrir</a></td>
								<td align="right">
									<button onclick="add_credito(<?php echo $d['codigo']; ?>);" class="btn btn-xs btn-default">Adicionar Créditos</button>
									<button onclick="dismiss_sol(<?php echo $d['sol']; ?>);" class="btn btn-xs btn-warning">Remover Solicitação</button>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="clearfix"></div>
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
        	<button onclick="add_creditoss();" class="btn btn-success">Adicionar Créditos</button>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
	
	function dismiss_sol(solicitacao){

		show_loading();
		$.post('page/revendedor.php', {dismiss:solicitacao}).done(function(resposta){
			end_loading();
			
			location.href='<?php echo URL; ?>/inicio';
		}).fail(function(resposta){
			end_loading();
			alert(resposta);
		});

	}

	function add_creditoss(){


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
			location.href='<?php echo URL; ?>/inicio';
			end_loading();
			$('#creditos').val('');
			//consultar_extrato($('#usu_codigo').val());
		})
		.fail(function(resposta){
			console.log(respostas);
			alert(resposta);
			end_loading();
		});

	}

	function add_credito(revendedor){

		$('#usu_codigo').val(revendedor);
		$('#modal_extrato').modal('show');

	}


</script>


<?php } ?>


<?php if($_SESSION['tipo'] == 3){ 

$dados       =  $con
	              ->select('*')
	              ->from('usuario')
	              ->where("deletado is null and inativo is null and tipo = 1 and revendedor = '".$_SESSION['usuario']."' ")
	              ->executeNGet();

$planos = $con->select('*')->from('plano')->where("codigo in (select plano from usuario group by plano)")->executeNGet();
$pl_final = array();
foreach($planos as $p){
	$pl_final[$p['codigo']] = $p['nome'];
}

$max_users = $con->select('max_usuarios')->from('usuario')->where("codigo = '".$_SESSION['usuario']."'")->limit(1)->executeNGet('max_usuarios');

?>

<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">	

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							Você já cadastrou <b><?php echo count($dados); ?></b> usuário(s) dos <b><?php echo ($max_users); ?></b> que seu plano permite.
						</div>
						
					</form>
				</div>
				<div class="panel-body">
					<table class="table table-hover table-bordered">
						<thead>
							<tr>
								<th>Nome</th>
								<th>Usuário</th>
								<th>Plano</th>
								<th>Vencimento</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($dados as $d){ ?>
							<tr>
								<td><?php echo $d['nome']; ?></td>
								<td><?php echo $d['usuario']; ?></td>
								<td><?php echo $pl_final[$d['plano']]; ?></td>
								<td><?php echo date('d/m/Y H:i', strtotime($d['vencimento'])); ?></td>
								<td>
									<button onclick="adicionar_credito(<?php echo $d['codigo']; ?>);" class="btn btn-xs btn-default">Adicionar Tempo</button>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div><!--/.row-->	
</div><!--/.main-->

<?php } ?>
<div class="modal fade" id="modal_in" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Consultar na base do INSS</h4>
      </div>
      <form action="<?php echo URL; ?>/webservice_in" method="post" id="formulario_inss">
	      <div class="modal-body">
			

				<div id="all-form">
			        <div class="form-group">
			        	<label>Número do Beneficiário</label>
			        	<input type="text" required name="inss" id="inss" class="form-control">
			        </div>

			        <div class="form-group" id="resultado"></div>

				</div>
				<div id="result_in"></div>
			

	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
	        <button type="submit" class="btn btn-primary">Consultar</button>
	      </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

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

<script>
	
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
				location.href='<?php echo URL; ?>';
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
	
	function openModal(t){

		if(t == 'bs')
			location.href = '<?php echo URL; ?>/pesquisa_bs';

		if(t == 'pai')
			location.href = '<?php echo URL; ?>/pesquisa_pai';

		if(t == 'n2')
			location.href = '<?php echo URL; ?>/pesquisa_n2';

		if(t == 'cnh')
			location.href = '<?php echo URL; ?>/pesquisa_cnh';

		if(t == 'titulo2')
			location.href = '<?php echo URL; ?>/pesquisa_titulo2';

		if(t == 'placa')
			location.href = '<?php echo URL; ?>/pesquisa_placa';

		if(t == 'acep')
			location.href = '<?php echo URL; ?>/pesquisa_acep';

		if(t == 'eo')
			location.href = '<?php echo URL; ?>/pesquisa_eo';

		if(t == 'score2')
			location.href = '<?php echo URL; ?>/pesquisa_score2';

		if(t == 'si')
			location.href = '<?php echo URL; ?>/pesquisa_si';

		if(t == 'spc')
			location.href = '<?php echo URL; ?>/pesquisa_b3';

		if(t == 'ex')
			location.href = '<?php echo URL; ?>/pesquisa_ex';

		if(t == 'asse')
			location.href = '<?php echo URL; ?>/pesquisa_a';

		if(t == 'a')
			location.href = '<?php echo URL; ?>/webservice_a';

		if(t == 'b')
			location.href = '<?php echo URL; ?>/pesquisa_b2';

		if(t == 'hisc')
			location.href = '<?php echo URL; ?>/pesquisa_h';

		if(t == 'rg')
			location.href = '<?php echo URL; ?>/pesquisa_rg';

		if(t == 'n')
			location.href = '<?php echo URL; ?>/pesquisa_n';

		if(t == 'score')
			location.href = '<?php echo URL; ?>/pesquisa_score';

		if(t == 'i'){

			return consultarI();

		}

		if(t == 's'){

			return consultarS();

		}

		if(t == 'icep'){

			location.href='<?php echo URL; ?>/pesquisa_sl_cep';

		}

		if(t == 'tit'){

			location.href='<?php echo URL; ?>/pesquisa_titulo';

		}

		if(t == 'v'){
			location.href='<?php echo URL; ?>/webservice_v';
		}

		if(t == 'tcpf')
			location.href='<?php echo URL; ?>/tracker_cpf';

		if(t == 't')
			location.href='<?php echo URL; ?>/tracker';

		if (t == 'in') location.href='<?php echo URL; ?>/pesquisa_in';
	}


	function consultarS(){

		location.href='<?php echo URL; ?>/pesquisa_s';
		

	}

	function consultarI(){

		location.href='<?php echo URL; ?>/pesquisa_sl';

	}

	function limiteConsultas(){

		alert('Você esgotou seu número de consultas nessa plataforma. Recarregue sua conta.');

	}


</script>
<?php
$con->close();
?>