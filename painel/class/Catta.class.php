<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
include('curl_get_contents.function.php');

class Catta {

	private $login = "infinitytecnologia", $senha = "fj140813", $proxy = false;

	private $useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36";

	//private $cookie = __DIR__ . DIRECTORY_SEPARATOR . 'cookie.txt';
	public $cookie = __DIR__ . DIRECTORY_SEPARATOR . 'cookie/CattaNodeGenerated{{cookie_id}}.txt';

	function __construct() {

		include('get_config_info.function.php');

		$ini_file  = get_config_info();
        
        $credenciais = $ini_file['catta'];
        $range = count(explode(',', $credenciais['usuario']));

        $cookie_id = rand(0, $range-1);
        $this->cookie = str_replace('{{cookie_id}}', $cookie_id, $this->cookie);

        if($credenciais['proxy'])
        	$this->proxy = $credenciais['proxy'];

        /*
        $this->login = explode(',', $credenciais['usuario']);
        $this->senha = explode(',', $credenciais['senha']);

        foreach($this->login as $indice=>$val) {
        	if(!isset($this->senha[$indice]))
        		unset($this->login[$indice]);	
        }*/


	}

	private function getStr($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);

	    if ($ini == 0) return '';
	    	$ini += strlen($start);

	    $len = strpos($string, $end, $ini) - $ini;

	    return substr($string, $ini, $len);
	}

	private function getToken($uf) {
		$url = "https://www.catta.com.br/{$uf}";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		$result = curl_exec($ch);
		curl_close($ch);

		return $this->getStr($result, "data: 'token=", "'");
	}

	function doLogin() {

		$index = rand(0, count($this->login)-1);
		$login = trim($this->login[$index]);
		$senha = trim($this->senha[$index]);

		$params = http_build_query([ "usuario" => $login, "senha" => $senha, "uf" => "" ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.catta.com.br/entrar");
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		// verbose test
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$verbose = fopen('php://temp', 'w+');
		curl_setopt($ch, CURLOPT_STDERR, $verbose);
		// end

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

		$result = curl_exec($ch);
		curl_close($ch);

		// verbose test
		if ($result === FALSE) {
		    printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
		           htmlspecialchars(curl_error($ch)));
		}else
			echo $result;

		rewind($verbose);
		$verboseLog = stream_get_contents($verbose);

		echo 'CURL';
		echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

		return null;
	}

	function KeepAlive() {
		// executar a cada 15 segundos (igual ta no site da catta)

		$token = $this->getToken("pr");

		$params = http_build_query([ "token" => $token ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.catta.com.br/conectado");
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

        if(isset($_GET['debug'])) {
            // CURLOPT_VERBOSE: TRUE to output verbose information. Writes output to STDERR, 
            // or the file specified using CURLOPT_STDERR.
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

		$response = curl_exec($ch);

		if (isset($_GET['debug'])) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                   htmlspecialchars(curl_error($ch)));

            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);

            echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
        }

		curl_close($ch);

		// response == 1 significa que esta tudo ok com a sessão
		// se n retornar 1, loga dnv
		if ($response != 1) $this->doLogin();
		else
			echo "ALIVE " .date('d/m/Y H:i:s');

		return null;
	}

	function ConsultaCpfCnpj($doc) {

		$token = $this->getToken("br");
		$doc = preg_replace('#[^0-9]#', '', $doc);
		$params = http_build_query([ "token" => $token, "deslocamento" => "0", "cpf_cnpj" => $doc, "cpf_cnpj_tipo_telefone" => "ambos" ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.catta.com.br/br/cpf-cnpj");
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

        if(isset($_GET['debug'])) {
            // CURLOPT_VERBOSE: TRUE to output verbose information. Writes output to STDERR, 
            // or the file specified using CURLOPT_STDERR.
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

		$response = curl_exec($ch);

		if (isset($_GET['debug'])) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                   htmlspecialchars(curl_error($ch)));

            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);

            echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
        }

		curl_close($ch);

		return $response;

	}

	function ConsultaNome($uf, $nome, $tipoPessoas = "todas", $deslocamento = 0, $endereco = "", $bairro = "", $cidade = 0) {
		$uf = strtolower($uf);
		// deslocamento é a quantiadade de pessoas que vão estar na pagina
		// por padrão é 0
		// mas por exemplo
		// se tem + de 20 pessoas na página, vc tem que setar o deslocamento pra 20
		// pra ele pegar + 20 pessoas e totalizar 40 na pagina
		// e se tiver + de 40 pessoas na pagina
		// vc tem que colocar o deslocamento pra 40 pra ele colocar 60 pessoas na pagina

		// endereco pode ser ou o endereço ou o cep

		// tipoPessoas pode ser ou fisica ou juridica
		// por padrao coloquei pra puxar todas

		$token = $this->getToken($uf);

		$url = "https://www.catta.com.br/" . $uf . "/nome";

		$params = http_build_query(
		[
			"token" => $token,
			"deslocamento" => $deslocamento,
			"nome" => $nome,
			"endereco" => $endereco,
			"bairro" => $bairro,
			"cidade" => $cidade,
			"nome_pessoa" => $tipoPessoas,
			"nome_tipo_telefone" => "ambos",
			"operadora_exibir" => "0",
			"operadora_ocultar" => "0"
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	function ConsultaTelefone($telefone, $uf, $cidade = 0, $deslocamento = 0) {
		$uf = strtolower($uf);
		$ddd = substr($telefone, 0, 2);
		$telefone = (strlen($telefone) == 10) ? $telefone = substr($telefone, 2, 10) : $telefone = substr($telefone, 2, 11); ;

		$token = $this->getToken($uf);

		$url = "https://www.catta.com.br/" . $uf . "/telefone";

		$params = http_build_query(
		[
			"token" => $token,
			"deslocamento" => $deslocamento,
			"ddd" => $ddd,
			"telefone" => $telefone,
			"cidade" => $cidade
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	function ConsultaMae($nome, $uf, $cidade = 0, $deslocamento = 0) {
		$uf = strtolower($uf);

		$token = $this->getToken($uf);

		$url = "https://www.catta.com.br/" . $uf . "/mae";

		$params = http_build_query(
		[
			"token" => $token,
			"deslocamento" => $deslocamento,
			"mae" => $nome,
			"cidade" => $cidade,
			"mae_tipo_telefone" => "ambos",
			"operadora_exibir" => "0",
			"operadora_ocultar" => "0"
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	function ConsultaNascimento($dia, $mes, $ano, $uf, $deslocamento = 0, $cidade = 0, $endereco = "", $bairro = "") {
		$uf = strtolower($uf);

		$token = $this->getToken($uf);

		$url = "https://www.catta.com.br/" . $uf . "/nascimento";

		$params = http_build_query(
		[
			"token" => $token,
			"deslocamento" => $deslocamento,
			"dia" => $dia,
			"mes" => $mes,
			"ano" => $ano,
			"endereco" => $endereco,
			"bairro" => $bairro,
			"cidade" => $cidade,
			"nascimento_tipo_telefone" => "ambos",
			"operadora_exibir" => "0",
			"operadora_ocultar" => "0"
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	function ConsultaSocio($nome, $cidade, $uf, $deslocamento = 0) {
		// nome pode ser ou nome ou cpf ou cnpj

		$uf = strtolower($uf);

		$token = $this->getToken($uf);

		$url = "https://www.catta.com.br/" . $uf . "/socio";

		$params = http_build_query(
		[
			"token" => $token,
			"deslocamento" => $deslocamento,
			"socio" => $nome,
			"cidade" => $cidade,
			"socio_tipo_telefone" => "ambos",
			"operadora_exibir" => "0",
			"operadora_ocultar" => "0"
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

	function ConsultaAtividadeEcon($atividade, $uf, $cidade = 0, $deslocamento = 0, $endereco = "", $bairro = "") {
		$uf = strtolower($uf);

		$token = $this->getToken($uf);

		$url = "https://www.catta.com.br/" . $uf . "/atividade";

		$params = http_build_query(
		[
			"token" => $token,
			"deslocamento" => $deslocamento,
			"atividade" => $atividade,
			"endereco" => $endereco,
			"bairro" => $bairro,
			"cidade" => $cidade,
			"atividade_tipo_telefone" => "ambos",
			"operadora_exibir" => "0",
			"operadora_ocultar" => "0"
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

        if(isset($_GET['debug'])) {
            // CURLOPT_VERBOSE: TRUE to output verbose information. Writes output to STDERR, 
            // or the file specified using CURLOPT_STDERR.
            curl_setopt($ch, CURLOPT_VERBOSE, true);

            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

		$response = curl_exec($ch);

		if (isset($_GET['debug'])) {
            printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                   htmlspecialchars(curl_error($ch)));

            rewind($verbose);
            $verboseLog = stream_get_contents($verbose);

            echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
        }

		curl_close($ch);

		return $response;
	}

	function ConsultaEndereco($uf, $cep, $numero = "", $complemento = "", $bairro = "", $cidade = 0, $deslocamento = 0) {
		// cep pode ser o nome da rua tbm

		$uf = strtolower($uf);

		$token = $this->getToken($uf);

		$url = "https://www.catta.com.br/" . $uf . "/endereco";

		$params = http_build_query(
		[
			"token" => $token,
			"deslocamento" => $deslocamento,
			"endereco" => $cep,
			"numero" => $numero,
			"complemento" => $complemento,
			"bairro" => $bairro,
			"cidade" => $cidade,
			"endereco_atualizado" => "atualizados",
			"endereco_pessoa" => "todas",
			"endereco_tipo_telefone" => "ambos",
			"operadora_exibir" => "0",
			"operadora_ocultar" => "0"
		]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		//curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie);
		if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}

}



if(isset($_GET['keepAlive'])){
	$catta= new Catta();
	$catta->KeepAlive();
}

if(isset($_GET['resetarCrawler'])){
	$catta = new Catta();
	if(is_file($catta->cookie))
		unlink($catta->cookie);
	$catta->KeepAlive();
}

if(!isset($_SESSION)) @session_start();
if(isset($_GET['debug'])){

	$catta = new Catta();
	echo $catta->ConsultaCpfCnpj('11523179899');
	//echo $catta->ConsultaAtividadeEcon('motorista',  'MG', 3132404, 0, "rua prefeito tigre maia", "");
}
?>
