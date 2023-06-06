<style>
	.mensagem-erro {
		color:red;
		font-weight:bold;
	}
	.titulo {
		color:#5DB7FB;
		font-weight:bold;
	}
</style>
<?php


if(!$_SESSION) @session_start();


include('class/Conexao.class.php');
include('class/Token.class.php');

$token = new Token();
$token = $token->get_token();

limitarConsulta(null, $_SESSION['usuario'], 'cs2');
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<script>var qtd_pesquisas = 0;</script>

<script>
$(document).ready(function() {
	$(".display").click(function() {
		var tipo_pesquisa = this.id;

		if (tipo_pesquisa == "pesquisaCpf") {
			var formulario = "#form_cpf";
			$("#loadingSpinnerCpf").removeClass('hidden');
		}
		$("#alertPesquisaAjax").show();

		var cpf = $("#cns").val();
		var dados = $(formulario).serialize();

		if (qtd_pesquisas <= 4) {
			$.ajax({
				type: "POST",
				url: "https://probusca.com/painel/class/PesquisaCadSus.class.php",
				dataType: "json",
				data: dados,
				beforeSend: function() { },
				success: function(data){
					var obj = JSON.parse(data);
					console.log(obj)
					if(typeof obj['success'] !== 'undefined' && obj['success'] == 'false') {
						var mensagem_erro = obj['response'];
						//mensagens de erro pro usuario
						if (obj['response'] == 'CNSNAORECUPERADO') {
							mensagem_erro = '<p class="mensagem-erro"> O CNS não pode ser recuperado. Por favor, tente novamente</p>';
						} else if (obj['response'] == 'SISTEMAFORADOAR') {
							mensagem_erro = '<p class="mensagem-erro"> O sistema está fora do ar, tente novamente mais tarde.</p><p>Se o problema persistir, entre em contato com o administrador do sistema</p>';
						} else if(obj['response'] == 'CREDITOSINSUF')
							mensagem_erro = "Seus créditos de hoje se esgotaram.";
						else if(obj['response'] == 'USERLOGGEDOUT')
							mensagem_erro = "Você não está logado";
						else if(obj['response'] == 'ERROR') {
							mensagem_erro = "Um erro ocorreu. Se o problema persistir, entre em contato com o administrador do sistema.\n"
							mensagem_erro += "Descrição do problema: "+ obj['complement'];
						}
						$('#alertErroAjax').html(mensagem_erro);
						$('#alertErroAjax').show();
						$("#alertPesquisaAjax").hide();
						$("#loadingSpinnerCpf").addClass('hidden');
						$("#loadingSpinnerNome").addClass('hidden');
					} else {
						if (tipo_pesquisa == "pesquisaCpf") {
							$("#loadingSpinnerCpf").addClass('hidden');
						} else if(tipo_pesquisa == "pesquisaNome") {
							$("#loadingSpinnerNome").addClass('hidden');
						}

						// Remove classe de ativa da tab anterior
						$("#menu"+qtd_pesquisas).removeClass('active in');
						$("#liTab"+qtd_pesquisas).removeClass('active');
						$("#fechaPesquisa"+qtd_pesquisas).hide();
						// Antes de adicionar a variavel global
						// Depois de incrementa a variável global
						qtd_pesquisas++;
						const spinnerId = 's_' + qtd_pesquisas;
						$("#alertErroAjax").hide();
						$("#alertPesquisaAjax").hide();

						// Verifica a quantidade de pesquisas para exibir as tabs e seta a atual para ativa
						if (qtd_pesquisas < 4) {
							$("#navPesquisa").append('<li id="liTab'+qtd_pesquisas+'"><a data-toggle="tab" href="#menu'+qtd_pesquisas+'" id="valorTab'+qtd_pesquisas+'"></a></li>');
							$("#menu"+qtd_pesquisas).addClass('active in');
							$("#liTab"+qtd_pesquisas).addClass('active');
						}
						if (tipo_pesquisa == "pesquisaCpf") {
							$("#valorTab"+qtd_pesquisas).html(cpf);
						}
						$("#navPesquisa").show();

						var tabela = $('#tabelaResultado'+qtd_pesquisas+' tbody');
						tabela += 	'<tr>';
							tabela +=	'<td class="titulo">Nome</td>';
							tabela +=	'<td>'+ obj['nome'] +'</td>';
							tabela +=	'<td class="titulo">Número CNS</td>';
							tabela +=	'<td>'+ obj['numeroCns'] +'</td>';
							tabela +=	'<td class="titulo">Nome Social</td>';
							tabela +=	'<td>'+ obj['nomeSocial'] +'</td>';
						tabela +='</tr>';
						tabela +='<tr>';
							tabela +='<td class="titulo">Nome da Mãe</td>';
							tabela +='<td>'+ obj['nomeMae'] +'</td>';
							tabela +='<td class="titulo">Nome do Pai</td>';
							tabela +='<td>'+ obj['nomePai'] +'</td>';
							tabela +='<td class="titulo">Vivo?</td>';
								if (obj['vivo'] == true) {
									tabela +='<td>Sim</td>';
								} else {
									tabela +='<td>Não</td>';
								}
						tabela += '</tr>';
						tabela += 	'<tr>';
							tabela +=	'<td class="titulo">Sexo</td>';
							tabela +=	'<td>'+ obj['sexoDescricao'] +'</td>';
							tabela +=	'<td class="titulo">Cor</td>';
							tabela +=	'<td>'+ obj['racaCorDescricao'] +'</td>';
							tabela +=	'<td class="titulo">Data de Nascimento</td>';
							tabela +=	'<td>'+ obj['dataNascimento'] +'</td>';
						tabela +='</tr>';
						tabela += 	'<tr>';
							tabela +=	'<td class="titulo">Nacionalidade</td>';
							tabela +=	'<td>'+ obj['nacionalidade'] +'</td>';
							tabela +=	'<td class="titulo">País Natal</td>';
							tabela +=	'<td>'+ obj['paisNascimento'] +'</td>';
							tabela +=	'<td class="titulo">Munícipio de Nascimento</td>';
							tabela +=	'<td>'+ obj['municipioNascimento'] +'</td>';
						tabela +='</tr>';
						tabela += 	'<tr>';
							tabela +=	'<td class="titulo">País Residente</td>';
							tabela +=	'<td>'+ obj['paisResidenciaDescricao'] +'</td>';
							tabela +=	'<td class="titulo">Cidade</td>';
							tabela +=	'<td>'+ obj['enderecoMunicipio'] +'</td>';
							tabela +=	'<td class="titulo">Endereço Logradouro</td>';
							tabela +=	'<td>'+ obj['enderecoLogradouro'] +'</td>';
						tabela +='</tr>';
						tabela += 	'<tr>';
							tabela +=	'<td class="titulo">Número Residência</td>';
							tabela +=	'<td>'+ obj['enderecoNumero'] +'</td>';
							tabela +=	'<td class="titulo">Complemento</td>';
							tabela +=	'<td>'+ obj['enderecoComplemento'] +'</td>';
							tabela +=	'<td class="titulo">Bairro</td>';
							tabela +=	'<td>'+ obj['enderecoBairro'] +'</td>';
						tabela +='</tr>';
						tabela += 	'<tr>';
							tabela +=	'<td class="titulo">CEP</td>';
							tabela +=	'<td>'+ obj['enderecoCep'] +'</td>';
							tabela +=	'<td class="titulo">CPF</td>';
							tabela +=	'<td>'+ obj['cpf'] +'</td>';
							tabela +=	'<td class="titulo">Número do RG</td>';
							tabela +=	'<td>'+ obj['rgNumero'] +'</td>';
						tabela +='</tr>';
						tabela += 	'<tr>';
							tabela +=	'<td class="titulo">Orgão Emissor do RG</td>';
							tabela +=	'<td>'+ obj['rgOrgaoEmissorDescricao'] +'</td>';
							tabela +=	'<td class="titulo">UF do RG</td>';
							tabela +=	'<td>'+ obj['rgUf'] +'</td>';
							tabela +=	'<td class="titulo">Data de Emissão do RG</td>';
							tabela +=	'<td>'+ obj['rgDataEmissao'] +'</td>';
						tabela +='</tr>'
						$('#tabelaResultado'+qtd_pesquisas).append(tabela);
					}
				},
				error: function (request, status, error) {
					console.log('erro' + request)
					var msg = request.responseText;
				}
			});
		} else {
			alert("Você excedeu quatro pesquisas na mesma página!");
		}

	});
	// Ação de fechar as pesquisas do usuario
	$(".deletaPesquisa").click(function() {
		var fired_button = $(this).val();
		if (qtd_pesquisas > 0 && qtd_pesquisas <= 4) {
			if (qtd_pesquisas == 1) {
				// Remove a classe ativo do menu e deleta a tab
				$("#menu"+fired_button).removeClass('active in');
				$("#liTab"+fired_button).remove();
				$("#valorTab"+fired_button).remove();
				$("#tabelaResultado"+fired_button+" > tbody > tr").remove();
				// Esconde o menu e o botão de deletar
				$("#navPesquisa").hide();
				$("#fechaPesquisa"+fired_button).hide();
				qtd_pesquisas--;
			} else {
				// Remove a classe ativo do menu e deleta a tab
				$("#menu"+fired_button).removeClass('active in');
				$("#liTab"+fired_button).remove();
				$("#valorTab"+fired_button).remove();
				$("#tabelaResultado"+fired_button+" > tbody > tr").remove();
				qtd_pesquisas--;
				// Exibe botao de fechar da pesquisa anterior e adiciona a classe ativo
				$("#fechaPesquisa"+qtd_pesquisas).show();
				$("#menu"+qtd_pesquisas).addClass('active in');
				$("#liTab"+qtd_pesquisas).addClass('active');
			}
		}
	});
});

</script>


<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa BRBusca 2.0</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa BRBusca 2.0 <?php if($busca) echo '<small>'.$retorno['total'].'</small>'; ?></h1>
		</div>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
			<p style="margin-bottom:2%"><strong>Aviso:</strong> estamos trabalhando para melhorar o BRBusca. Caso tenha algum problema, entre em contato com o administrador do sistema.</p>
				<div class="panel-heading">
					<form action="" method="post">
						<div class="form-group form-inline">
							<button type="button" onclick="location.href='<?php echo URL; ?>/';" class="btn-default btn btn-md"><i class="fa fa-angle-left"></i> Voltar</button>
							Entre com as informações para pesquisar
						</div>
					</form>
				</div>
				<div hidden class="alert alert-danger" role="alert" id='alertErroAjax'></div>
				<div class="panel-body table-responsive">
					<!-- Nav tabs --><div class="card">
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="home">
								<form id="form_cpf" method="post">
									<div class="form-group col-sm-3">
										<label for="">CPF</label>
										<input type="text" id="cns" name="cpf" class="onlyNumbers form-control" placeholder="Digite um CPF...">
									</div>
									<div class="clearfix"></div>
									<input type="hidden" id="tracker" value="1">
								</form>
								<input class="display btn btn-primary" type="button" id="pesquisaCpf" value="Pesquisar CPF" />
							</div>
					</form>

				</div>
				<div hidden class="alert alert-warning" role="alert" id='alertPesquisaAjax'><i class="fas fa-spinner spin-the-spinner hidden" id="loadingSpinnerCpf"></i>	 Pesquisando... Por favor, aguarde</div>
				<ul class="nav nav-tabs" id="navPesquisa" >
				</ul>

				<div class="tab-content">
					<div hidden id="menu1" class="tab-pane fade">
						<table class="table table-bordered" id="tabelaResultado1">
							<tbody>
							</tbody>
						</table>
						<center><button class="deletaPesquisa btn btn-danger" id="fechaPesquisa1" value="1">Fechar Pesquisa</button></center>
					</div>
					<div hidden id="menu2" class="tab-pane fade">
						<table class="table table-bordered" id="tabelaResultado2">
							<tbody>
							</tbody>
						</table>
						<center><button hidden class="deletaPesquisa btn btn-danger" id="fechaPesquisa2" value="2">Fechar Pesquisa</button></center>
					</div>
					<div hidden id="menu3" class="tab-pane fade">
						<table class="table table-bordered" id="tabelaResultado3">
							<tbody>
							</tbody>
						</table>
						<center><button hidden class="deletaPesquisa btn btn-danger" id="fechaPesquisa3" value="3">Fechar Pesquisa</button></center>
					</div>
					<div hidden id="menu4" class="tab-pane fade">
						<table class="table table-bordered" id="tabelaResultado3">
							<tbody>
							</tbody>
						</table>
						<center><button hidden class="deletaPesquisa btn btn-danger" id="fechaPesquisa4" value="4">Fechar Pesquisa</button></center>
					</div>
				</div>
			</div>
		</div>
	</div><!--/.row-->


</div><!--/.main-->


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalLabelResultado">
	<div class="modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="modalLabelResultado">Resultado</h4>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer" style="text-align:center;">
				<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
			</div>
		</div>
	</div>
</div>
