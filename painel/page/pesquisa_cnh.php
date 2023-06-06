<?php


if(!$_SESSION) @session_start();

include('class/LimitarConsulta.function.php');
include('class/Token.class.php');

limitarConsulta(null, $_SESSION['usuario'], 'cnh');

$token = new Token();
$token = $token->get_token();

?>
<script>

	function PrintElem(elem){
        Popup($(elem).html());
    }

    function Popup(data)
    {
        var mywindow = window.open('', 'new div', 'height=400,width=600');
        mywindow.document.write('<html><head><title>Imprimir</title>');
        /*optional stylesheet*/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
        mywindow.document.write('<style>@media print{.no-print, .no-print *{display: none !important;}}.dados:after,span.link{content:"\a";white-space:pre}#tabela tr:hover,.tbl_results tr:hover{background-color:#eaeaea;cursor:pointer}.tbl_results th,td{padding-bottom:15px}.title{font-weight:700}.headertitle{margin-top:15px;margin-bottom:15px;font-size:1.4em}span.link{color:#00f;cursor:pointer}</style></head><body >');
        mywindow.document.write('<img src="https://probusca.com/img/logo.png" height="150" alt=""><h3>Resultado da Pesquisa</h3>' + data);
        mywindow.document.write('</body></html>');

        mywindow.print();
        mywindow.close();

        return true;
    }

    let userData = false;

    function processar_dados(userData) {

		$.get('https://probusca.com/painel/page/pesquisa_cnh.html').done(function(htmlContent) {
			let templateScript = Handlebars.compile(htmlContent);
			let dataTemplate = {
				dadospessoais: {
					nome: userData.cadastral.nome,
					mae: userData.cadastral.mae,
					rg: userData.cnh.rg || 'Indisponível',
					nascimento: userData.cadastral.nasc,
					trabalho: userData.cadastral.cbo  || 'Indisponível',
					renda: 'R$ ' + number_format(userData.cadastral.renda, 2, ',', '.')
				},
				cnh: {
					cnh: userData.cnh.cnh,
					vencimento: userData.cnh.vencimento,
					habilitado: userData.cnh['habilitado?'] ? 'Sim':'Não',
					categoria: userData.cnh.categoria,
					orgao: userData.cnh.orgao,
					primeira_habilitacao: userData.cnh.primeiraHab,
					uf: userData.cnh.uf
				},
				endereco: {
					endereco: userData
				}
			};

			if(userData.enderecos && userData.enderecos.length > 0) {
				let indice = userData.enderecos.length-1;
				let cep = String(userData.enderecos[indice].cep);
				dataTemplate.endereco = {
					endereco: userData.enderecos[indice].endereco,
					numero: userData.enderecos[indice].numero || 'Indisponível',
					complemento: userData.enderecos[indice].compl || 'Indisponível',
					bairro: userData.enderecos[indice].bairro || 'Indisponível',
					cidade: userData.enderecos[indice].cidade || 'Indisponível',
					cep: cep.substr(0,5) + '-' + cep.substr(5, 3)
				};
			}
			if(userData.telefones && userData.telefones.length > 0) {
				dataTemplate.telefones = [];
				for(let i = 0; i < userData.telefones.length; i++)
					dataTemplate.telefones.push(userData.telefones[i].telefone);
			}
			if(userData.telefones && userData.telefones.length > 0) {
				dataTemplate.telefones = [];
				let final = (userData.telefones.length > 3)? 3:userData.telefones.length;
				for(let i = 0; i < final; i++) {
					let tel = String(userData.telefones[i].telefone);
					let telefoneFormatado = '(' + tel.substr(0, 2) + ') ';
					if(tel.length <= 10)
						telefoneFormatado += tel.substr(2, 4) + '-' + tel.substr(6, 4);
					else
						telefoneFormatado += tel.substr(2, 5) + '-' + tel.substr(7, 4);
					dataTemplate.telefones.push(telefoneFormatado);
				}
				dataTemplate.telefone_colspan = final;
			}
			if(userData.empregos && userData.empregos.length > 0) {
				dataTemplate.empregos = userData.empregos;
				for(let i in dataTemplate.empregos) {
					if(dataTemplate.empregos[i].cnpj) {
						let cnpj = String(dataTemplate.empregos[i].cnpj);
						dataTemplate.empregos[i].cnpj = cnpj.substr(0,2) + '.' + cnpj.substr(2,3) + '.' + cnpj.substr(5,3) + '/' + cnpj.substr(8,4) + '-' + cnpj.substr(12);
					}
					dataTemplate.empregos[i].nome = dataTemplate.empregos[i].nome || 'Indisponível';
					if(dataTemplate.empregos[i].cbo)
						dataTemplate.dadospessoais.trabalho = dataTemplate.empregos[i].cbo;
					if(dataTemplate.empregos[i].dataadmissao)
						dataTemplate.empregos[i].dataadmissao = dataTemplate.empregos[i].dataadmissao.split('-').reverse().join('/');
					else
						dataTemplate.empregos[i].dataadmissao = 'Indisponível';
					if(dataTemplate.empregos[i].salario && dataTemplate.empregos[i].salario > 0)
						dataTemplate.empregos[i].salario = 'R$ ' + number_format(dataTemplate.empregos[i].salario, 2, ',', '.');
					else
						dataTemplate.empregos[i].salario = 'Indisponível';
				}
				
			}

			let htmlString = templateScript(dataTemplate);
			$('#resultado').html(htmlString);
			end_loading();

		}).fail(function(r) {
			end_loading();
			console.log(r);
		});
	}

	function do_pesquisa(){

		show_loading();
		let dados = $('#form_basica').serialize();
		$.ajax({
			type: "POST",
			url: "https://probusca.com/painel/class/PesquisaCnhPlaca.class.php",
			dataType: "text",
			data: dados
		}).done(function(data) {

			let parsedData = {error:1,msg:'Falha ao pesquisar!'};
			try {
				parsedData = JSON.parse(data);
			} catch(e) {
				console.log(data);
				end_loading();
				return alert('Falha ao pesquisar!');
			}

			if(parsedData.error) {
				end_loading();
				return alert(parsedData.msg);
			}
			
			processar_dados(parsedData);

		}).fail(function(data){
			end_loading();
			console.log(data);
			alert('Erro! Tente novamente mais tarde.');
		});
	}
</script>
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="<?php echo URL; ?>"><svg class="glyph stroked home"><use xlink:href="#stroked-home"></use></svg></a></li>
			<li class="active">Pesquisa CNH</li>
		</ol>
	</div><!--/.row-->

	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Pesquisa CNH</h1>
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
					<form action="" id="form_basica" method="post">
						<div class="col-lg-2 col-sm-4 col-md-3">
							<input type="hidden" name="tipo" value="cpf">
							<input type="hidden" name="token" value="<?= $token; ?>">
							<div class="form-group">
								<label for="">CPF</label>
								<input type="text" name="dado" required class="form-control" placeholder="Digite o CPF">
							</div>
							<div class="clearfix"></div>
							<div class="form-group text-left">
								<button type="button" onclick="do_pesquisa();" name="btn_sub" value="true" class="btn btn-primary">Pesquisar</button>
							</div>
						</div>
					</form>
					<div class="col-lg-10 col-sm-8 col-md-9">
						<div class="form-group" id="resultado"></div>
					</div>
					
				</div>
			</div>
		</div>
	</div><!--/.row-->


</div><!--/.main-->
