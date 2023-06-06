<link rel="stylesheet" href="css/formulario-10.css">
<link rel="stylesheet" href="css/modalConsulta.css">
<link rel="stylesheet" href="css/score-4.css">
<link rel="stylesheet" href="css/papel.css">
<link rel="stylesheet" href="css/balcao.css">
<link rel="stylesheet" href="css/accordion-2.css">
<link rel="stylesheet" href="css/lightwindow-1.css">
<link rel="stylesheet" href="css/collectionScore.css">

<?php

require_once("simple_html_dom.php");

class SPC {

	private $login = "1758770", $senha = "meupag2018", $frase = "lucimaria2016";

	private $useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36";

	private $referer = 'https://servicos.spc.org.br/spc/controleacesso/autenticacao/authenticate.action';

	private $cookie = __DIR__ . DIRECTORY_SEPARATOR . 'cookie/cookieConsultaSpc.txt';

	private function getStr($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);

	    if ($ini == 0) return '';
	    	$ini += strlen($start);

	    $len = strpos($string, $end, $ini) - $ini;

	    return substr($string, $ini, $len);
	}

	private function Mask($mask, $str){
	    $str = str_replace(" ", "", $str);
	    for($i=0; $i<strlen($str); $i++){
	        $mask[strpos($mask,"#")] = $str[$i];
	    }

	    return $mask;
	}

	function doLogin() {
		$params = http_build_query([ "j_username" => $this->login, "j_password" => $this->senha, "Entrar" => "Entrar" ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://servicos.spc.org.br/spc/controleacesso/autenticacao/authenticate.action");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_REFERER, $this->referer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_VERBOSE, true);

		$verbose = fopen('php://temp', 'w+');
		curl_setopt($ch, CURLOPT_STDERR, $verbose);

		$result = curl_exec($ch);

		if ($result === FALSE) {
		    printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
		           htmlspecialchars(curl_error($handle)));
		}
		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);
		echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";


		curl_close($ch);

		$params2 = http_build_query([ "action" => "validarFraseSecreta", "passphrase" => $this->frase, "EntrarPassphrase" => "Entrar" ]);

		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, "https://servicos.spc.org.br/spc/controleacesso/autenticacao/passphrase.action");
		curl_setopt($ch2, CURLOPT_POST, true);
		curl_setopt($ch2, CURLOPT_POSTFIELDS, $params2);
		curl_setopt($ch2, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch2, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch2, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch2, CURLOPT_REFERER, $this->referer);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);

		curl_exec($ch2);
		curl_close($ch2);

		return null;
	}

	function ConsultaCPF($cpf) {
		$cpf = preg_replace('/[^0-9]/', '', $cpf);

		$cpf = $this->Mask('###.###.###-##', $cpf);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://servicos.spc.org.br/spc/menu.action?idSubsistema=5");
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_REFERER, "https://servicos.spc.org.br/spc/controleacesso/painelcontrole/init.action");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$__r = $this->getStr($response, 'url="/spc/consulta/insumo/initFilter.action?idProdutoWeb=257&__idFuncionalidade=79089&__r=', '"');

		$urlMixTOP = "https://servicos.spc.org.br/spc/consulta/insumo/initFilter.action?idProdutoWeb=257&__idFuncionalidade=79089&__r={$__r}";

		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $urlMixTOP);
		curl_setopt($ch2, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch2, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch2, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch2, CURLOPT_REFERER, "https://servicos.spc.org.br/spc/menu.action?idSubsistema=5");
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch2);
		curl_close($ch2);

		$token = $this->getStr($response, '<input id="token" name="token" type="hidden" value="', '"/>');
		$sessid = $this->getStr($response, '<input type="hidden" id="__sessId" name="__sessId" value="', '"/>');
		$operlog = $this->getStr($response, '<input type="hidden" id="__operLog" name="__operLog" value="', '"/>');

		$ch3 = curl_init();
		curl_setopt($ch3, CURLOPT_URL, "https://servicos.spc.org.br/spc/consulta/insumo/cadastro/validarAtualizacaoCadastral.action?tipoPessoa=F&documentoAtualizacao={$cpf}&permiteCadastroConsumidorPeloProduto=true&idProduto=257");
		curl_setopt($ch3, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch3, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch3, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch3, CURLOPT_REFERER, $urlMixTOP);
		curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch3, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch3);
		curl_close($ch3);

		$params = http_build_query(
		[
			"listaInsumosOpcionais" => "3082",
			"listaInsumosOpcionais" => "18",
			"listaInsumosOpcionais" => "5195",
			"listaInsumosOpcionais" => "5194",
			"listaInsumosOpcionais" => "5190",
			"listaInsumosOpcionais" => "5142",
			"listaInsumosOpcionais" => "24",
			"listaInsumosOpcionais" => "17",
			"listaInsumosOpcionais" => "5122",
			"listaInsumosOpcionais" => "78",
			"listaInsumosOpcionais" => "77",
			"idProdutoWeb" => "257",
			"tipoPessoa" => "F",
			"_" => ""
		]);

		$ch4 = curl_init();
		curl_setopt($ch4, CURLOPT_URL, "https://servicos.spc.org.br/spc/consulta/insumo/buscaInsumoPopUp.action");
		curl_setopt($ch4, CURLOPT_POST, true);
		curl_setopt($ch4, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch4, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch4, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch4, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch4, CURLOPT_REFERER, $urlMixTOP);
		curl_setopt($ch4, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch4, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch4);
		curl_close($ch4);

		$params2 = http_build_query(
		[
			"__sessId" => $sessid,
			"__operLog" => $operlog,
			"reiniciarConsulta" => "false",
			"gerarCheques" => "false",
			"indiceNavegar" => "0",
			"indiceRemover" => "",
			"indiceChequeRemover" => "",
			"indiceAtual" => "0",
			"idProdutoWeb" => "257",
			"token" => $token,
			"permiteConsultaMultiplosDocumentosPorPacote" => "true",
			"produtoCadastroPositivo" => "false",
			"quantidadeConsultas" => "1",
			"filtros[0].avisoLimiteConsulta" => "false",
			"filtros[0].tipoPessoa" => "F",
			"filtros[0].numeroDocumento" => $cpf,
			"filtros[0].dataNascimento" => "",
			"filtros[0].telefones[0].numeroDDD" => "",
			"filtros[0].telefones[0].numero" => "",
			"filtros[0].cepConsumidor" => "80420-160",
			"filtros[0].insumosOpcionais" => "3082",
			"filtros[0].insumosOpcionais" => "18",
			"filtros[0].insumosOpcionais" => "5195",
			"filtros[0].insumosOpcionais" => "5194",
			"filtros[0].insumosOpcionais" => "5190",
			"filtros[0].insumosOpcionais" => "5142",
			"filtros[0].insumosOpcionais" => "24",
			"filtros[0].insumosOpcionais" => "17",
			"filtros[0].insumosOpcionais" => "5122",
			"filtros[0].insumosOpcionais" => "78",
			"filtros[0].insumosOpcionais" => "77",
			"idProdutoOrigem" => "",
			"filtros[0].tipoInformacaoCheque" => "CMC7",
			"filtros[0].CMC7_1" => "",
			"filtros[0].CMC7_2" => "",
			"filtros[0].CMC7_3" => "",
			"filtros[0].numeroBanco" => "",
			"filtros[0].numeroAgencia" => "",
			"filtros[0].numeroContaCorrente" => "",
			"filtros[0].digitoContaCorrente" => "",
			"filtros[0].numeroChequeInicial" => "",
			"filtros[0].digitoChequeInicial" => "",
			"filtros[0].quantidadeCheque" => "",
			"filtros[0].totalCheques" => "0,00",
			"tipoPlacaVeiculo" => "",
			"tipoPlacaVeiculo" => "",
			"tipoPlaca" => "",
			"filtros[1].avisoLimiteConsulta" => "false",
			"filtros[2].avisoLimiteConsulta" => "false",
			"filtros[3].avisoLimiteConsulta" => "false",
			"filtros[4].avisoLimiteConsulta" => "false",
			"filtros[5].avisoLimiteConsulta" => "false",
			"filtros[6].avisoLimiteConsulta" => "false",
			"filtros[7].avisoLimiteConsulta" => "false",
			"filtros[8].avisoLimiteConsulta" => "false",
			"filtros[9].avisoLimiteConsulta" => "false",
			"parametrosAtualizacao.tipoPessoa" => "",
			"parametrosAtualizacao.documentoAtualizacao" => "",
			"parametrosAtualizacao.idProduto" => "",
			"parametrosAtualizacao.fluxoProcurase" => "",
			"parametrosAtualizacao.idConsumidor" => "",
			"idProcessoBiometria" => "",
			"sujeitoCadastrado" => ""
		]);

		$findurl = "https://servicos.spc.org.br/spc/consulta/insumo/find.action?t={$token}";

		$ch5 = curl_init();
		curl_setopt($ch5, CURLOPT_URL, $findurl);
		curl_setopt($ch5, CURLOPT_POST, true);
		curl_setopt($ch5, CURLOPT_POSTFIELDS, $params2);
		curl_setopt($ch5, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch5, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch5, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch5, CURLOPT_REFERER, $urlMixTOP);
		curl_setopt($ch5, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch5, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch5, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch5);
		curl_close($ch5);

		$ch6 = curl_init();
		curl_setopt($ch6, CURLOPT_URL, "https://servicos.spc.org.br/spc/consulta/insumo/detailResultadoConsultaUnica.action");
		curl_setopt($ch6, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch6, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch6, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch6, CURLOPT_REFERER, $findurl);
		curl_setopt($ch6, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch6, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch6, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch6);
		curl_close($ch6);

		$response = str_replace('/spc/images/silhueta_masc.jpg', 'images/silhueta_masc.jpg', $response);
		$response = str_replace('/spc/images/score/banner/banner_farol_score_collection_score.jpg', 'images/banner_farol_score_collection_score.jpg', $response);
		$response = str_replace('/spc/images/icon/consulta.png', 'images/icon/consulta.png', $response);
		$response = str_replace('/spc/images/icon/cross.png', 'images/cross.png', $response);
		$response = str_replace('/spc/images/icon/exclamation.png', 'images/exclamation.png', $response);
		$response = str_replace('/spc/images/icon/document_text.png', 'images/document_text.png', $response);
		$response = str_replace('/spc/images/icon/information_frame.png', 'images/information_frame.png', $response);
		$response = str_replace('/spc/images/icon/topo.png', 'images/topo.png', $response);
		$response = str_replace('/spc/script/justgage.js', 'js/justgage.js', $response);
		$response = str_replace('/spc/script/raphael-2.1.4.min.js', 'js/raphael-2.1.4.min.js', $response);

		$html = new simple_html_dom();
		$html->load($response);

		$result = curl_exec($handle);
if ($result === FALSE) {
    printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
           htmlspecialchars(curl_error($handle)));
}

rewind($verbose);
$verboseLog = stream_get_contents($verbose);

echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

		$html->find('div[id=nomeOperador]')[0]->innertext = "USUÁRIO";
		$html->find('ul[class=accordion]')[0]->outertext = "";
		$html->find('div[class=wrapper_header]')[0]->outertext = "";
		$html = $html->find('form[id=form]')[0];
		$html->find('table')[0]->outertext = "";
		$html->find('div[class=warning]')[0]->outertext = "";
		$html->find('input[value=INCLUIR CRED. CONCEDIDO SIMPL.]')[0]->outertext = "";
		$html->find('input[value=INCLUIR CREDITO CONCEDIDO]')[0]->outertext = "";
		$html->find('input[value=IMPRIMIR]')[0]->outertext = "";
		$html->find('input[value=VOLTAR]')[0]->outertext = "";

		foreach ($html->find('a[class=lightwindow page-options]') as $tira) {
			$tira->outertext = "";
		}

		foreach ($html->find('div[id=removerImpressao]') as $tira) {
			$tira->outertext = "";
		}

		return $html;
	}

	function ConsultaCNPJ($cnpj) {
		$cnpj = preg_replace('/[^0-9]/', '', $cnpj);

		$cnpj = $this->Mask('##.###.###/####-##', $cnpj);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://servicos.spc.org.br/spc/menu.action?idSubsistema=5");
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_REFERER, "https://servicos.spc.org.br/spc/controleacesso/painelcontrole/init.action");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$__r = $this->getStr($response, 'url="/spc/consulta/insumo/initFilter.action?idProdutoWeb=257&__idFuncionalidade=79089&__r=', '"');

		$urlMixTOP = "https://servicos.spc.org.br/spc/consulta/insumo/initFilter.action?idProdutoWeb=257&__idFuncionalidade=79089&__r={$__r}";

		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $urlMixTOP);
		curl_setopt($ch2, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch2, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch2, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch2, CURLOPT_REFERER, "https://servicos.spc.org.br/spc/menu.action?idSubsistema=5");
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch2);
		curl_close($ch2);

		$token = $this->getStr($response, '<input id="token" name="token" type="hidden" value="', '"/>');
		$sessid = $this->getStr($response, '<input type="hidden" id="__sessId" name="__sessId" value="', '"/>');
		$operlog = $this->getStr($response, '<input type="hidden" id="__operLog" name="__operLog" value="', '"/>');


		$params3 = http_build_query(
		[
			"__sessId" => $sessid,
			"__operLog" => $operlog,
			"reiniciarConsulta" => "false",
			"gerarCheques" => "false",
			"indiceNavegar" => "0",
			"indiceRemover" => "",
			"indiceChequeRemover" => "",
			"indiceAtual" => "0",
			"idProdutoWeb" => "257",
			"token" => $token,
			"permiteConsultaMultiplosDocumentosPorPacote" => "true",
			"produtoCadastroPositivo" => "false",
			"quantidadeConsultas" => "1",
			"filtros[0].avisoLimiteConsulta" => "false",
			"filtros[0].tipoPessoa" => "J",
			"filtros[0].numeroDocumento" => "",
			"filtros[0].dataNascimento" => "",
			"filtros[0].telefones[0].numeroDDD" => "",
			"filtros[0].telefones[0].numero" => "",
			"filtros[0].cepConsumidor" => "",
			"idProdutoOrigem" => "",
			"filtros[0].tipoInformacaoCheque" => "CMC7",
			"filtros[0].CMC7_1" => "",
			"filtros[0].CMC7_2" => "",
			"filtros[0].CMC7_3" => "",
			"filtros[0].numeroBanco" => "",
			"filtros[0].numeroAgencia" => "",
			"filtros[0].numeroContaCorrente" => "",
			"filtros[0].digitoContaCorrente" => "",
			"filtros[0].numeroChequeInicial" => "",
			"filtros[0].digitoChequeInicial" => "",
			"filtros[0].quantidadeCheque" => "",
			"filtros[0].totalCheques" => "0,00",
			"tipoPlacaVeiculo" => "",
			"tipoPlacaVeiculo" => "",
			"tipoPlaca" => "",
			"filtros[1].avisoLimiteConsulta" => "false",
			"filtros[2].avisoLimiteConsulta" => "false",
			"filtros[3].avisoLimiteConsulta" => "false",
			"filtros[4].avisoLimiteConsulta" => "false",
			"filtros[5].avisoLimiteConsulta" => "false",
			"filtros[6].avisoLimiteConsulta" => "false",
			"filtros[7].avisoLimiteConsulta" => "false",
			"filtros[8].avisoLimiteConsulta" => "false",
			"filtros[9].avisoLimiteConsulta" => "false",
			"parametrosAtualizacao.tipoPessoa" => "",
			"parametrosAtualizacao.documentoAtualizacao" => "",
			"parametrosAtualizacao.idProduto" => "",
			"parametrosAtualizacao.fluxoProcurase" => "",
			"parametrosAtualizacao.idConsumidor" => "",
			"idProcessoBiometria" => "",
			"sujeitoCadastrado" => ""
		]);

		$ch10 = curl_init();
		curl_setopt($ch10, CURLOPT_URL, "https://servicos.spc.org.br/spc/consulta/insumo/initFilter.action");
		curl_setopt($ch10, CURLOPT_POST, true);
		curl_setopt($ch10, CURLOPT_POSTFIELDS, $params3);
		curl_setopt($ch10, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch10, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch10, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch10, CURLOPT_REFERER, $urlMixTOP);
		curl_setopt($ch10, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch10, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch10, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch10);
		curl_close($ch10);

		// n entendi a necessiade desse ch10 pra funcionar, mas funcionou pelo menos

		//

		$ch3 = curl_init();
		curl_setopt($ch3, CURLOPT_URL, "https://servicos.spc.org.br/spc/consulta/insumo/cadastro/validarAtualizacaoCadastral.action?tipoPessoa=J&documentoAtualizacao={$cnpj}&permiteCadastroConsumidorPeloProduto=true&idProduto=257");
		curl_setopt($ch3, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch3, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch3, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch3, CURLOPT_REFERER, $urlMixTOP);
		curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch3, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch3);
		curl_close($ch3);

		$params = http_build_query(
		[
			"listaInsumosOpcionais" => "18",
			"listaInsumosOpcionais" => "5185",
			"listaInsumosOpcionais" => "24",
			"listaInsumosOpcionais" => "17",
			"listaInsumosOpcionais" => "5186",
			"listaInsumosOpcionais" => "5184",
			"listaInsumosOpcionais" => "78",
			"listaInsumosOpcionais" => "77",
			"listaInsumosOpcionais" => "257",
			"idProdutoWeb" => "257",
			"tipoPessoa" => "J",
			"_" => ""
		]);

		$ch4 = curl_init();
		curl_setopt($ch4, CURLOPT_URL, "https://servicos.spc.org.br/spc/consulta/insumo/buscaInsumoPopUp.action");
		curl_setopt($ch4, CURLOPT_POST, true);
		curl_setopt($ch4, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch4, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch4, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch4, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch4, CURLOPT_REFERER, $urlMixTOP);
		curl_setopt($ch4, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch4, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch4);
		curl_close($ch4);

		$params2 = http_build_query(
		[
			"__sessId" => $sessid,
			"__operLog" => $operlog,
			"reiniciarConsulta" => "false",
			"gerarCheques" => "false",
			"indiceNavegar" => "0",
			"indiceRemover" => "",
			"indiceChequeRemover" => "",
			"indiceAtual" => "0",
			"idProdutoWeb" => "257",
			"token" => $token,
			"permiteConsultaMultiplosDocumentosPorPacote" => "true",
			"produtoCadastroPositivo" => "false",
			"quantidadeConsultas" => "1",
			"filtros[0].avisoLimiteConsulta" => "false",
			"filtros[0].tipoPessoa" => "J",
			"filtros[0].numeroDocumento" => $cnpj,
			"filtros[0].dataNascimento" => "",
			"filtros[0].telefones[0].numeroDDD" => "",
			"filtros[0].telefones[0].numero" => "",
			"filtros[0].cepConsumidor" => "",
			"filtros[0].insumosOpcionais" => "18",
			"filtros[0].insumosOpcionais" => "5185",
			"filtros[0].insumosOpcionais" => "24",
			"filtros[0].insumosOpcionais" => "17",
			"filtros[0].insumosOpcionais" => "5186",
			"filtros[0].insumosOpcionais" => "5184",
			"filtros[0].insumosOpcionais" => "78",
			"filtros[0].insumosOpcionais" => "77",
			"idProdutoOrigem" => "",
			"filtros[0].tipoInformacaoCheque" => "CMC7",
			"filtros[0].CMC7_1" => "",
			"filtros[0].CMC7_2" => "",
			"filtros[0].CMC7_3" => "",
			"filtros[0].numeroBanco" => "",
			"filtros[0].numeroAgencia" => "",
			"filtros[0].numeroContaCorrente" => "",
			"filtros[0].digitoContaCorrente" => "",
			"filtros[0].numeroChequeInicial" => "",
			"filtros[0].digitoChequeInicial" => "",
			"filtros[0].quantidadeCheque" => "",
			"filtros[0].totalCheques" => "0,00",
			"tipoPlacaVeiculo" => "",
			"tipoPlacaVeiculo" => "",
			"tipoPlaca" => "",
			"filtros[1].avisoLimiteConsulta" => "false",
			"filtros[2].avisoLimiteConsulta" => "false",
			"filtros[3].avisoLimiteConsulta" => "false",
			"filtros[4].avisoLimiteConsulta" => "false",
			"filtros[5].avisoLimiteConsulta" => "false",
			"filtros[6].avisoLimiteConsulta" => "false",
			"filtros[7].avisoLimiteConsulta" => "false",
			"filtros[8].avisoLimiteConsulta" => "false",
			"filtros[9].avisoLimiteConsulta" => "false",
			"parametrosAtualizacao.tipoPessoa" => "",
			"parametrosAtualizacao.documentoAtualizacao" => "",
			"parametrosAtualizacao.idProduto" => "",
			"parametrosAtualizacao.fluxoProcurase" => "",
			"parametrosAtualizacao.idConsumidor" => "",
			"idProcessoBiometria" => "",
			"sujeitoCadastrado" => ""
		]);

		$findurl = "https://servicos.spc.org.br/spc/consulta/insumo/find.action?t={$token}";

		$ch5 = curl_init();
		curl_setopt($ch5, CURLOPT_URL, $findurl);
		curl_setopt($ch5, CURLOPT_POST, true);
		curl_setopt($ch5, CURLOPT_POSTFIELDS, $params2);
		curl_setopt($ch5, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch5, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch5, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch5, CURLOPT_REFERER, $urlMixTOP);
		curl_setopt($ch5, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch5, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch5, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch5);
		curl_close($ch5);

		$response = str_replace('/spc/images/silhueta_masc.jpg', 'images/silhueta_masc.jpg', $response);
		$response = str_replace('/spc/images/score/banner/banner_farol_score_collection_score.jpg', 'images/banner_farol_score_collection_score.jpg', $response);
		$response = str_replace('/spc/images/icon/consulta.png', 'images/icon/consulta.png', $response);
		$response = str_replace('/spc/images/icon/cross.png', 'images/cross.png', $response);
		$response = str_replace('/spc/images/icon/exclamation.png', 'images/exclamation.png', $response);
		$response = str_replace('/spc/images/icon/document_text.png', 'images/document_text.png', $response);
		$response = str_replace('/spc/images/icon/information_frame.png', 'images/information_frame.png', $response);
		$response = str_replace('/spc/images/icon/topo.png', 'images/topo.png', $response);
		$response = str_replace('/spc/script/justgage.js', 'js/justgage.js', $response);
		$response = str_replace('/spc/script/raphael-2.1.4.min.js', 'js/raphael-2.1.4.min.js', $response);

		$html = new simple_html_dom();
		$html->load($response);

		$html->find('div[id=nomeOperador]')[0]->innertext = "USUÁRIO";
		$html = $html->find('div[class=wrapper]')[0];
		$html = $html->find('div[id=conteudo]')[0];
		$html->find('td[class=btn_footer_left]')[0]->outertext = "";
		$html->find('div[class=warning]')[0]->outertext = "";
		$html->find('div[class=footer_map]')[0]->outertext = "";

		foreach ($html->find('a[class=lightwindow page-options]') as $tira) {
			$tira->outertext = "";
		}

		foreach ($html->find('div[id=removerImpressao]') as $tira) {
			$tira->outertext = "";
		}

		return $html;
	}
}

if(isset($_GET['teste']))
{

	$spc = new SPC();
	$spc->doLogin();
	echo $spc->ConsultaCPF('49759337699');

}
