<?php
// mostrar os erros
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

set_time_limit(300);


// precisa usar o simple_html_dom, to mandando o arquivo junto
// n quiser usar e preferir outra coisa tudo bem, é só pra esconder as informações do login q ta sendo usado :x
require_once('simple_html_dom.php');


/*if(isset($_GET['cpf'])){
	$nett = new Nett();
	$nett->doLogin();
	$resp = $nett->ConsultaNomePT2("1", "Diego Rodrigues da Silva", "mg");
	echo "<pre>";
	print_r($resp);
}
*/

class Nett{

	private $login = "111323", $senha = "C2WDa5X*Uh";

	// só muda o tocaptchakey pra key de vcs do 2captcha
	private $tocaptchaKey = "890760df7fc4433f4519d0df6b75ff88",
	$method = "userrecaptcha",
	$gkey = "6LctajMUAAAAAD-TLZEaUlNUKoI-KqTbiqbbIvlI",
	$loginURL = "https://www.natt.com.br/sistema/consultas/index.php";

	private $useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36";

	// não precisa alterar o cookie (só se vc quiser trocar de pasta), ele já pega o diretório e identifica se a barra é a normal ou invertida
	private $cookie = __DIR__ . DIRECTORY_SEPARATOR . 'cookie/cookieNett.txt';

	function __construct() {

	}

	private function Mask($mask, $str){
	    $str = str_replace(" ", "", $str);
	    for($i=0; $i<strlen($str); $i++){
	        $mask[strpos($mask,"#")] = $str[$i];
	    }

	    return $mask;
	}

	function getRecaptcha() {

		/*
		$request = file_get_contents("http://2captcha.com/in.php?key={$this->tocaptchaKey}&method={$this->method}&googlekey={$this->gkey}&pageurl={$this->loginURL}&json=1");
		*/

		$ch = curl_init();

	  $params = ['key'=>$this->tocaptchaKey, 'method'=>$this->method, 'googlekey'=>$this->gkey, 'pageurl'=>$this->loginURL, 'json'=>1];

	  curl_setopt($ch, CURLOPT_URL, 'http://2captcha.com/in.php');
	  curl_setopt($ch, CURLOPT_POST, true);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	  $request = curl_exec($ch);
	  curl_close($ch);

		$request = json_decode($request)->request;

    $response = file_get_contents("http://2captcha.com/res.php?key={$this->tocaptchaKey}&action=get&id={$request}&json=1");
    $response = json_decode($response);

    $status = $response->status;

    $maxAttempts = 10;

    while ($status == 0) {
    	sleep(10);

      $response = file_get_contents("http://2captcha.com/res.php?key={$this->tocaptchaKey}&action=get&id={$request}&json=1");
	    $response = json_decode($response);

	    $status = $response->status;

	    if(--$maxAttempts == 0)
	       	die('captcha took too much time');
      }

      return $response->request;
	}

	function doLogin() {
		// nao usa o unlink($this->cookie) pq ta bugando tudo e deixando a sessão inválida
		// qnd vc faz login o arquivo do cookie já ta sendo substituido automaticamente

		$captcha = $this->getRecaptcha();

		$params = http_build_query([ "user" => $this->login, "pass" => $this->senha, "g-recaptcha-response" => $captcha, "NattLogin" => "Logar" ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/index.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


		$resp = curl_exec($ch);
		curl_close($ch);

		return null;
	}

	function ConsultaCPF($cpf) {
		$cpf = preg_replace('#[^0-9]#', '', $cpf);
		$cpf = $this->Mask("###.###.###-##", $cpf);

		$params = http_build_query([ "e_cpf" => $cpf, "e_cnpj" => "", "e_telefone" => "" ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/cpf/Resposta0101.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch);
		curl_close($ch);


		$response = str_replace('../../img/logo_nat_brasil.gif', 'https://www.natt.com.br/sistema/img/logo_nat_brasil.gif', $response);
		$response = str_replace('../../img/bsi.jpg', 'https://www.natt.com.br/sistema/img/bsi.jpg', $response);
		$response = str_replace('../images/cancelar2.png', 'https://www.natt.com.br/sistema/consultas/images/cancelar2.png', $response);


		$html = str_get_html($response);

		foreach($html->find('a') as $element)
		{
		  $element->outertext = '';
		}

		if(isset($html->find('div[id=login]')[0]))
		{
				$html->find('div[id=login]')[0]->innertext = '';
		}

		foreach($html->find('input[type=submit]') as $element)
		{
			$element->outertext = '';
		}

		foreach($html->find('script') as $element)
		{
		  $element->outertext = '';
		}

		if(isset($html->find(".article")[0]))
		{
			$html->find(".article")[0]->outertext = "";
		}

		foreach($html->find('.header') as $element)
		{
		  $element->outertext = '';
		}

		foreach($html->find('.elink') as $element)
		{
		  $element->outertext = '';
		}

		$html->save(__DIR__ . DIRECTORY_SEPARATOR . "resultadoNett.html");

		return ['error'=>false, "result"=>"https://probusca.com/painel/class/resultadoNett.html"];
	}

	function ConsultaNomePT1($nome) {
		$params = http_build_query([ "e_nome" => $nome ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/nome/Resposta0201.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch);
		curl_close($ch);

		$response = str_replace('../../../img/logo_nat_brasil.gif', 'https://www.natt.com.br/sistema/img/logo_nat_brasil.gif', $response);
		$response = str_replace('../../../img/bsi.jpg', 'https://www.natt.com.br/sistema/img/bsi.jpg', $response);

		return $response;
	}

	function ConsultaNomePT2($pag, $nome, $uf) {
		// pag = pagina
		// cada pagina suporta no maximo 15 pessoas
		// ai se por exemplo
		// no paraná morar mais de 15 lucas hoffmann
		// tu vai ter que ir pra segunda pagina

		$uf = strtoupper($uf);
		//Retira acentos do nome
		$nome = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$nome);
		//Deixa o nome em caixa alta
		$nome = strtoupper($nome);

		$params = http_build_query([ "e_pag" => $pag, "e_nome" => $nome, "e_uf" => $uf ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/nome/Resposta0202.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch);
		curl_close($ch);

		//Primeiro, verifica se ocorreu algum erro
		if(preg_match("/Undefined index: Nattlogin/", $response))
		{
			return "Undefined index: Nattlogin";
		}
		elseif(preg_match("/You don't have permission to access/", $response))
		{
			return "You don't have permission to access";
		}

		//Extrai os dados em um array
		$html = str_get_html($response);
		//Salva os dados no arquivo, que serve como um Debug
		$html->save(__DIR__ . DIRECTORY_SEPARATOR . "resultadoNett.html");
		//Pega a tabela HTML e transforma em uma string Json
		$tableData = [];
		foreach($html->find('table[id=cTable] tr') as $tr)
		{
		  $line = [];
		  foreach ($tr->find("td") as $td)
		  {
		    $line[] = $td->innertext;
		  }
		  $tableData[] = $line;
		}
		unset($tableData[0]);
		$data = [];
		foreach ($tableData as $person)
		{
		  array_push($data,array("id"=>$person[0], "cpfCnpj"=>$person[1], "nome"=>$person[2], "cidade"=>$person[3]));
		}

		return ['error'=>false, "data"=>$data];
	}

	function ConsultaCNPJ($cnpj) {
		// cnpj nao precisa de mask \o/
		$cnpj = preg_replace('#[^0-9]#', '', $cnpj);

		$params = http_build_query([ "e_cpf" => "", "e_cnpj" => $cnpj, "e_telefone" => "" ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/cpf/Resposta0102-1.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		// sem return transfer pq ele n retorna nada
		//https://www.natt.com.br/sistema/consultas/cpf/Resposta0102-1.php?e_cnpj=11475040000106

		$response = curl_exec($ch);
		curl_close($ch);

		// agr a gente vai pegar o resultado

		$url = "https://www.natt.com.br/sistema/consultas/cpf/Resposta0102-1.php?e_cnpj={$cnpj}";

		$ch2 = curl_init();
		curl_setopt($ch2, CURLOPT_URL, $url);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch2,  CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch2, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch2, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch2, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch2);
		curl_close($ch2);

		$response = str_replace('../../../img/logo_nat_brasil.gif', 'https://www.natt.com.br/sistema/img/logo_nat_brasil.gif', $response);
		$response = str_replace('../../../img/bsi.jpg', 'https://www.natt.com.br/sistema/img/bsi.jpg', $response);
		$response = str_replace('../images/cancelar2.png', 'https://www.natt.com.br/sistema/consultas/images/cancelar2.png', $response);

		$html = str_get_html($response);

		foreach($html->find('a') as $element)
		{
			$element->outertext = '';
		}

		if(isset($html->find('div[id=login]')[0]))
		{
				$html->find('div[id=login]')[0]->innertext = '';
		}

		foreach($html->find('input[type=submit]') as $element)
		{
			$element->outertext = '';
		}

		foreach($html->find('script') as $element)
		{
			$element->outertext = '';
		}

		if(isset($html->find(".article")[0]))
		{
			$html->find(".article")[0]->outertext = "";
		}

		foreach($html->find('.header') as $element)
		{
			$element->outertext = '';
		}

		foreach($html->find('.elink') as $element)
		{
			$element->outertext = '';
		}

		$html->save(__DIR__ . DIRECTORY_SEPARATOR . "resultadoNett.html");

		return ['error'=>false, "data"=>"https://probusca.com/painel/class/resultadoNett.html"];
	}

	function ConsultaTelefone($telefone) {
		// telefone precisa de mask tbm
		// ##-####-##### ou ##-####-####

		$telefone = preg_replace('#[^0-9]#', '', $telefone);

		if (strlen($telefone) == 10)
			// telefone fixo
			$telefone = $this->Mask('##-####-####', $telefone);
		else if (strlen($telefone) == 11)
			// telefone celular
			$telefone = $this->Mask('##-####-#####', $telefone);


		$params = http_build_query([ "e_cpf" => "", "e_cnpj" => "", "e_telefone" => $telefone ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/telefone/Resposta0301.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch);
		curl_close($ch);

		$html = str_get_html($response);
		$doc = false;

		foreach($html->find('form input[type=hidden]') as $element)
		{
			if($element->attr['name'] == "e_cpf")
			{
				$doc = $element->attr['value'];
			}
		}

		if(!$doc)
		{
			return "NOT FOUND";
		}

		$params = [ "e_cpf" => $doc];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/cpf/Resposta0101.php?telefone={$telefone}");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch);
		curl_close($ch);

		//Faz um tratamento do HTML e só mantém os dados importantes
		$html = str_get_html($response);

		foreach($html->find('a') as $element)
		{
		  $element->outertext = '';
		}

		if(isset($html->find('div[id=login]')[0]))
		{
				$html->find('div[id=login]')[0]->innertext = '';
		}

		foreach($html->find('input[type=submit]') as $element)
		{
			$element->outertext = '';
		}

		foreach($html->find('script') as $element)
		{
		  $element->outertext = '';
		}

		if(isset($html->find(".article")[0]))
		{
			$html->find(".article")[0]->outertext = "";
		}

		foreach($html->find('.header') as $element)
		{
		  $element->outertext = '';
		}

		foreach($html->find('.elink') as $element)
		{
		  $element->outertext = '';
		}

		return $html;
	}

	function ConsultaEndereco($uf, $cidade, $endereco, $cep) {
		$uf = strtoupper($uf);

		$params = http_build_query([ "e_uf" => $uf, "e_cidade" => $cidade, "e_log" => $endereco, "rua_id" => $cep ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/endereco/Resposta0403.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch);
		curl_close($ch);

		$response = str_replace('../../../img/logo_nat_brasil.gif', 'https://www.natt.com.br/sistema/img/logo_nat_brasil.gif', $response);
		$response = str_replace('../../../img/bsi.jpg', 'https://www.natt.com.br/sistema/img/bsi.jpg', $response);


		return $response;
	}

	function ConsultaCEP($cep, $numInicial = null, $numFinal = null, $pagina = null)
	{
		$params = [ "e_numero_inicio" => $numInicial, "e_numero_fim" => $numFinal, "e_cep" => $cep ];

		if ($pagina != null) $params = [ "e_numero_inicio" => $numInicial, "e_numero_fim" => $numFinal, "e_cep" => $cep, "e_pag" => $pagina ];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/cep/Resposta0501.php");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);

		$response = curl_exec($ch);
		curl_close($ch);

		$response = str_replace('../../../img/logo_nat_brasil.gif', 'https://www.natt.com.br/sistema/img/logo_nat_brasil.gif', $response);
		$response = str_replace('../../../img/bsi.jpg', 'https://www.natt.com.br/sistema/img/bsi.jpg', $response);

		$html = str_get_html($response);

		$tableData = [];
		foreach($html->find('table[id=cTable] tr') as $tr)
		{
			$line = [];
			foreach ($tr->find("td") as $td)
			{
				$line[] = $td->innertext;
			}
			$tableData[] = $line;
		}

		unset($tableData[0]);
		$data = [];
		foreach ($tableData as $person)
		{
			array_push($data,array(
				"cpfCnpj"=>$person[0],
				"nome"=>$person[1],
				"logradouro"=>$person[2],
				"numero"=>$person[3],
				"complemento"=>$person[4],
				"bairro"=>$person[5],
				"cep"=>$person[6]
			));
		}

		//Salva os dados no arquivo, que serve como um Debug
		$html->save(__DIR__ . DIRECTORY_SEPARATOR . "resultadoNett.html");

		return ['error'=>false, "result"=>json_encode($data)];
	}


	// --------------------------------------------
	// CONSULTA DE ENDEREÇO
	// --------------------------------------------



	function getCidades($uf) {
		$uf = strtoupper($uf);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/cep/Cidades.php?e_uf={$uf}");
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	function getCodigoCidade($cidade, $uf) {
		$uf = strtoupper($uf);

		//$cidades = file_get_contents("https://www.natt.com.br/sistema/consultas/cep/Cidades.php?e_uf={$uf}");
		$cidades = file_get_contents("https://pastebin.com/raw/AP9Mnjfj");
		$cidades = json_decode($cidades);

		$idCidade = '';

		for ($i=0; $i < count($cidades); $i++) {
			if (preg_match("/{$cidade}/i", $cidades[$i]->descricao)) {
				$idCidade = $cidades[$i]->id_cidade;
			}
		}

		return $idCidade;
	}

	function getRuas($cidade, $uf, $rua)
	{
		$uf = strtoupper($uf);

		//$cidades = file_get_contents("https://www.natt.com.br/sistema/consultas/cep/Cidades.php?e_uf={$uf}");
		$cidades = file_get_contents("https://pastebin.com/raw/AP9Mnjfj");
		$cidades = json_decode($cidades);

		$idCidade = '';

		for ($i=0; $i < count($cidades); $i++) {
			if (preg_match("/{$cidade}/i", $cidades[$i]->descricao)) {
				$idCidade = $cidades[$i]->id_cidade;
			}
		}

		$ruas = file_get_contents("https://www.natt.com.br/sistema/consultas/cep/Ruas.php?term={$rua}&e_uf=PR&e_cidade={$idCidade}");

		return $ruas;
	}

	function getRuas2($uf, $e_cidade, $term)
	{
		//uf -> sigla do Estado
		//e_cidade -> id da cidade, obtido em *getCidades()*
		//term -> rua, avenida, etc
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/cep/Ruas.php?term={$term}&e_uf={$uf}&e_cidade={$e_cidade}");
		curl_setopt($ch, CURLOPT_POST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

}


?>
