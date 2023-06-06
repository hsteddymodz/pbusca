<?php

if(!$_SESSION) @session_start();

include('class/Conexao.class.php');
include('class/Token.class.php');
include('class/LimitarConsulta.function.php');
$con = new Conexao();
limitarConsulta($con, $_SESSION['usuario'], 's');

if(!$_SESSION) @session_start();

$token = new Token();
$token = $token->get_token();

?>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa Pessoa Física</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa Pessoa Física <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
		</div>
	</div><!--/.row-->

	


	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div class="panel-body table-responsive">

					<?php if($_POST['form']){ ?>

						<div class="form-group text-right">
				        	<button onclick="location.href='<?php echo URL; ?>/pesquisa_s';" class="btn btn-default">Pesquisar Novamente</button>
				        </div>

						<table id="tabela" class="table table-bordered table-hover table-stripped ">
				            <thead>
				              <tr>
				                <th>Nome</th>
				                <th>Mãe</th>
				                <th>Pai</th>
				                <th>Sexo</th>
				                <th>Nascimento</th>
				                <th>País de Nascimento</th>
				                <th>Município de Nascimento</th>

				              </tr>
				            </thead>
				            <tbody id="corpo_tabela">
				            	
				            </tbody>
				        </table>
				        
			        <?php }else{ ?>
					<form action="" method="post">
						
						<input type="hidden" name="form" value="true">

						<div class="form-group col-sm-4">
							<label for="">Nome</label>
							<input type="text" onchange="disable_inputs();" name="nome" class="form-control">
						</div>

						<div class="form-group col-sm-4">
							<label for="">Nome da Mãe</label>
							<input type="text" onchange="disable_inputs();" name="nomeMae" class="form-control">
						</div>

						<div class="form-group col-sm-3">
							<label for="">Nome do Pai</label>
							<input type="text" onchange="disable_inputs();" name="nomePai" class="form-control">
						</div>

						<div class="form-group col-sm-3">
							<label for="">Data de Nascimento</label>
							<input type="text" onchange="disable_inputs();" name="dataNascimento" class="data form-control">
						</div>

						<div class="form-group col-sm-3">
							<label for="">CPF</label>
							<input type="text" onchange="disable_inputs();" name="cpf" class="onlyNumbers form-control">
						</div>

						<div class="clearfix"></div>

						<div class="form-group text-center">
							<button type="submit" name="identico" value="true" class="btn btn-primary">Pesquisar Idênticos</button>
							<button type="submit" name="similar"  value="true"  class="btn btn-primary">Pesquisar Similares</button>
						</div>


					</form>
			        <?php } ?>
				</div>
			</div>
		</div>
	</div><!--/.row-->	


</div><!--/.main-->

<div class="modal fade" id="modal_s" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Resultado</h4>
      </div>
      <div class="modal-body">


		<div id="result_final"></div>

		<div class="form-group">
			<form action="<?php echo URL; ?>/page/imprimir.php" target="_Blank" method="post">
				<input type="hidden" name="consulta" value="<?php echo $codigo_consulta; ?>">
				<input type="hidden" name="usuario" value="<?php echo $_SESSION['usuario']; ?>">
				<input type="hidden" name="info" id="info" value="">
				<button type="submit"  class="btn btn-default">Imprimir</button>
			</form>
		</div>


      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="resultado_cns" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Consulta Detalhada</h4>
      </div>
      <div class="modal-body">
		
		<p id="table_cns">
			
		</p>

		<div class="form-group">
			<button type="button" class="btn btn-default" id="botao_imprimir">Imprimir</button>
		</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>

	function exibir(info){

		if(!info || info === undefined)
			return '';

	    if(info == 'SEM INFORMAÇÃO')
	        return '';

	    if(info == 'INVALIDO')
	        return '';

	    if(info == 'NÃO CONSTA DO REGISTRO CIVIL')
	    	return '';

	    if(info == 'AUXENTE')
	    	return '';

	    if(info == 'INVALIDO - XX')
	        return 'SEM REGISTRO';
	    
	    return info;

	}

	function e_line(titulo = ''){

		if(titulo == '')
			$('#table_cns').append("<p><hr /></p>");
		else
			$('#table_cns').append("<p><h3>"+titulo+"</h3></p>");

	}

	function e_cns(chave, valor){

		$('#table_cns').append("➜ <b> "+chave+": </b>" + exibir(valor) + "<br>");

	}

	function registrar_consulta(){

		return;

	}

	<?php 
	if($_POST['form']){

		include('class/SUS.class.php');
	?>

	function consulta_detalhada(str){
		//alert(str);
		console.log("Em progresso");
		show_loading();
		$.ajax({type:"POST", url:"https://nevoahost.com:15000", data: {"token":"<?= $token; ?>", "plataforma":"cadsus", "action":"consulta_cns", "cns":str}, dataType:"text"})
		.done(function(r){
			console.log(r);
			r = JSON.parse(r);
			end_loading();

			if(!r)
				return alert('Falha ao obter informações detalhadas!');

			if(r.erro == 1)
				return alert(r.resultado);

			$('#table_cns').html('');


			e_cns("Nome", r.nome);
			e_cns("Nome da Mãe", r.nomeMae);
			e_cns("Nome do Pai", r.nomePai);
			e_cns("Sexo", r.sexoDescricao);
			e_cns("Data de Nascimento", r.dataNascimento);
			e_cns("Município de Nascimento", r.municipioNascimento);
			e_cns("CPF", r.cpf);
			
			var rg  = '';

			if(r.rgNumero)
				rg += r.rgNumero;

			if(r.rgOrgaoEmissorDescricao)
				rg += ' | ' + r.rgOrgaoEmissorDescricao;

			if(r.rgUf)
				rg += ' | ' + r.rgUf;

			if(rg != '') e_cns("RG", rg);
			
			e_cns("Estado", r.enderecoMunicipio);
			if(r.enderecoLogradouro && r.enderecoNumero)
				e_cns("Endereço", r.enderecoLogradouro + " Nº " + r.enderecoNumero);
			else if(r.enderecoLogradouro)
				e_cns("Endereço", r.enderecoLogradouro);

			e_cns("Bairro", r.enderecoBairro);
			e_cns("CEP", r.enderecoCep);
			if(r.telefone && r.telefone[0])
			e_cns("Telefone", r.telefone[0].ddd + " " + r.telefone[0].numero);

			e_line("RG");

			e_cns("Data de Emissão", r.rgDataEmissao);
			e_cns("Orgão Omissor", r.rgOrgaoEmissorDescricao);
			e_cns("Número do RG", r.rgNumero);
			e_cns("UF", r.rgUf);

			e_line("Título Eleitor");
			
			e_cns("Número", r.tituloEleitorNumero);
			e_cns("Zona", r.tituloEleitorZona);
			e_cns("Seção", r.tituloEleitorSecao);

			if(r.dataObito){
				e_line("Data de Óbito");
				e_cns("Número", r.dataObito);
			}

			$('#botao_imprimir').click(function(){
				window.open('page/imprimir.php?str=' + JSON.stringify(r), '_blank');
			});

			
			$('#resultado_cns').modal('show');

		}).fail(function(erro){
			end_loading();
			console.log(erro);
			alert('Falha ao consultar dados detalhados!');
		});
	}

	function do_consulta(){

		show_loading();

		$.ajax({type:'POST', url:'https://nevoahost.com:15000', data:{<?= decode_post_sus($_POST); ?>}, dataType:"text"})
		.done(function(res){

			end_loading();
			console.log(res);

			try{
				var outra_resposta = JSON.parse(res);
				if(outra_resposta.erro == 1) return alert(outra_resposta.resultado);
			}catch(e){
				//console.log(e);
				// faz nada
			}

			var resposta = JSON.parse(res.substring(1, res.length-1));

			var html = "";

			if(resposta.erro){
				alert("Sua pesquisa retornou mais que 50 resultados. Por favor, refine-a.");
				return location.href='<?php echo URL; ?>/pesquisa_s';
			}

			if(resposta.total == 0){
				alert("Nenhum registro encontrado!");
				html += "<tr><td colspan=\"7\">Nenhum resultado encontrado.</td></tr>";
				$('#corpo_tabela').html(html);
				return;
			}
				
			var registro = resposta.registro;
			for(var k = 0; k < registro.length; k++){
				html += 
					"<tr onclick=\"return consulta_detalhada('" + (registro[k].numeroCns) + "');\">\
						<td>"+ exibir(registro[k].nome) +"</td>\
						<td>"+ exibir(registro[k].nomeMae) +"</td>\
						<td>"+ exibir(registro[k].nomePai) +"</td>\
						<td>"+ exibir(registro[k].sexo) +"</td>\
						<td>"+ exibir(registro[k].dataNascimento) +"</td>\
						<td>"+ exibir(registro[k].paisNascimento) +"</td>\
						<td>"+ exibir(registro[k].municipioNascimento) +"</td>\
					</tr>";
			}	

			$('#corpo_tabela').html(html);

		}).fail(function(res){
			end_loading();
			console.log(res);
		});

	}

	

	document.addEventListener("DOMContentLoaded", function(event) { 
		//do work
		do_consulta();
	});

	<?php } ?>

</script>

<style>
	#tabela tr:hover{
		background-color:#eaeaea;
		cursor:pointer;
	}
</style>


