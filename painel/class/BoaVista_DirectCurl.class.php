<?php

//error_reporting(0);
ini_set('max_execution_time', 120); //300 seconds = 5 minutes

include('simple_html_dom.php');
include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');
include('Conexao.class.php');
include('get_config_info.function.php');
include('onlyNumbers.function.php');
include('curl_get_contents.function.php');

class BoaVista {

	private $login = '11000250347';
	private $senha = '1315';
	private $twocaptcha_key = '550365c52bdf5dd9681c319f96fe5d61';
	private $login_url = 'https://web2.bvsnet.com.br/transacional/login.php';
	private $cookie_file = 'cookie/BoaVistaNodeGenerated.txt';
	private $nome_empresa = 'KLIN PROD INFANTIS LTDA';
	private $proxy = '18.222.100.7:3128';

	private $show_debug = false;

	function __construct(){

        $ini_file  = get_config_info();
        
        $credenciais_boa_vista = $ini_file['boavista'];
        $this->login = $credenciais_boa_vista['usuario'];
        $this->senha = $credenciais_boa_vista['senha'];
        $this->nome_empresa = $credenciais_boa_vista['nome_empresa_bv']; 
        $this->proxy = $credenciais_boa_vista['proxy']; 

        $proxyInfo = curl_get_contents('http://pubproxy.com/api/proxy?api=L3VsTy9GR3ZTQ0xPdGEvRXNDN0NVZz09&last_check=60&country=BR&https=true&post=true&user_agent=true&cookies=true');
        $proxyInfo = json_decode($proxyInfo, 1);

        //var_dump($proxyInfo['data'][0]['ipPort']);

        $this->proxy = $proxyInfo['data'][0]['ipPort']; 

	}

	function debug($str){
		if($this->show_debug)
			echo "<p>$str</p>";
	}

	function do_login(){

		// get google key
		$html_code = $this->do_curl($this->login_url, $this->login_url);
		$parsed_html = str_get_html($html_code);

		echo $html_code;

		$google_site_key = $parsed_html->find('.g-recaptcha', 0)->getAttribute('data-sitekey');

		$this->debug("Google site key: $google_site_key");

		// BREAK CAPTCHA
		$twocaptcha_request = file_get_contents("http://2captcha.com/in.php?key=" 
			. $this->twocaptcha_key
			. "&method=userrecaptcha&googlekey=" 
			. $google_site_key 
			. "&pageurl=" 
			. $this->login_url 
			. "&json=true");

        $request_id = json_decode($twocaptcha_request)->request;
        $url2 = "http://2captcha.com/res.php?key=" 
        	. $this->twocaptcha_key 
        	. "&action=get&id=" 
        	. $request_id
        	. "&json=1";
        
        do {
            sleep(5);
            $responseCaptcha = json_decode(file_get_contents($url2));
        } while ($responseCaptcha->status == 0);

        $captcha_token = $responseCaptcha->request; 

        $this->debug("Recaptcha TOKEN: $captcha_token");
        
        $params = array(
        	'lk_codig' => $this->login,
            'lk_senha' => $this->senha,
            'lk_width' => '123',
            'lk_suaft' => '',
			'cd_usuario' => $this->login,
			'cd_cpf' => '',
			'cd_senha' => $this->senha,
			'email' => '',
            'g-recaptcha-response' => $captcha_token,
			'lk_manut' => 'https://www.servicodeprotecaoaocredito.com.br/bvs_login.htm',
			'lk_urlesquecisenha' => 'https://www.bvsnet.com.br/cgi-bin/db2www/NETPO101.mbr/RecuperaSenha'
		);
        
        $this->do_curl('https://web2.bvsnet.com.br/transacional/autenticacao.php', 'https://web2.bvsnet.com.br/transacional/login.php', $params);

        $resultado = $this->consulta_cpf('29432901653');

        if($resultado === false)
        	echo json_encode(array('error'=>true));
        else
        	echo json_encode(array('error'=>false));

	}

	function do_curl($url, $referer, $params = array()){

		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if(count($params) > 0)
        	curl_setopt($ch, CURLOPT_POST, false);

        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookie_file);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');

        if(count($params) > 0)
        	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($referer)
        	curl_setopt($ch, CURLOPT_REFERER, $referer);

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        
        if($this->proxy)
        	curl_setopt($ch, CURLOPT_PROXY, $this->proxy);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        return $result;

	}

	function consulta_cnpj($cnpj){

		$cnpj = onlyNumbers($cnpj);

		$content = $this->do_curl('https://web2.bvsnet.com.br/transacional/produtos.php?p=DEFINE_NEGOCIO&t=J', 'https://web2.bvsnet.com.br/transacional/menu.php');
        $html_code = str_get_html($content);

        try {
        	$nova_url = $html_code->find('form', 0)->getAttribute('action');
        } catch(Error $e){
			$this->do_login();
			$this->consulta_cnpj($cnpj);
			return false;
        }

        $params = array();
        foreach($html_code->find('form input') as $input){
        	$params[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $content = $this->do_curl($nova_url, 'https://web2.bvsnet.com.br/transacional/produtos.php?p=DEFINE_NEGOCIO&t=J', $params);

        $content = $this->do_curl('https://define.bvsnet.com.br/DefineWeb/resultadoNegocioConsulta?cnpj=' . $cnpj . '&QUADRO_SOCIAL_RESTRICAO=YES&PARTICIPACOES_RESTRICAO=YES&FATURAMENTO=YES&LIMITE_CREDITO=YES&ANVISA=YES&EMPRESA_MESMO_ENDERECO=YES&radioCheque=SUPERIOR&banco=&agencia=&contaCorrente=&digitoConta=&numeroCheque=&digitoCheque=&totalCheque=&dataCheque=&valor=0%2C00&cmc7Campo1=&cmc7Campo2=&cmc7Campo3=&totalChequeCMC7=&valorCMC7=0%2C00', 'https://define.bvsnet.com.br/DefineWeb/formularioNegocio');

        return $content;

	}	

	function consulta_cpf($cpf){

		$cpf = onlyNumbers($cpf);

		$url_consulta = 'https://web2.bvsnet.com.br/transacional/produtos.php?p=ACERTA_COMPLETO&t=F';
		$content = $this->do_curl($url_consulta, 'https://web2.bvsnet.com.br/transacional/menu.php');
        $html_code = str_get_html($content);
        
        try {
        	$nova_url = $html_code->find('form', 0)->getAttribute('action');
        } catch(Error $e){
        	$this->do_login();
			$this->consulta_cnpj($cnpj);
			return false;
        }
        

        $params = array();
        foreach($html_code->find('form input') as $input){
        	$params[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $content = $this->do_curl($nova_url, $url_consulta, $params);

        $params = array(
        	'quantidade'=>'',
			'valor'=>'0,00',
			'documento'=> $cpf,
			'documentos'=>',,,,,' . $cpf,
			'cepConfirmacao'=>'',
			'opcaoCpf'=>'doc',
			'nomeFormulario'=>'Acerta Completo',
			'consulta'=>'doc',
			'cpf1'=>'',
			'cpf2'=>'', 
			'cpf3'=>'', 
			'cpf4'=>'', 
			'cpf5'=>'', 
			'multiplasPaginas'=>'true',
			'comboScoreResult'=>'07', 
			'comboScore'=>'07',
			'comboCreditoResult'=>'',
			'comboTipoCredito'=>'CD',
			'txtTelefone'=>'',
			'txtTelefone'=>'',
			'chkCheque'=>'on',
			'chequeSimples'=>'simples',
			'cheque'=>'S',
			'cmc7Mascara1'=>'',
			'cmc7Mascara2'=>'',
			'cmc7Mascara3'=>'',
			'cmc7TotalChequesMascara'=>'',
			'cmc7ValorMascara'=>'0,00',
			'bancoMascara'=>'',
			'agenciaMascara'=>'',
			'contaCorrenteMascara'=>'',
			'digitoContaMascara'=>'',
			'numeroChequeMascara'=>'',
			'digitoChequeMascara'=>'',
			'totalChequeMascara'=>'',
			'dataChequeMascara'=>'',
			'valorMascara'=>'0,00'
		);

        $content = $this->do_curl('https://acerta.bvsnet.com.br/FamiliaAcertaPFWeb/resultadoConsulta', 'https://acerta.bvsnet.com.br/FamiliaAcertaPFWeb/formularioAcertaCompleto', $params);

        return $content;

	}	

	function limpar_consulta($content){
		$html = str_get_html($content);
		if($html)
			$remove = $html->find('#iframeClickGold', 0);
		else
			return $response;
		
		if($remove){
			$remove->outertext = '';
		}
        

        if($html->find('.help-back'))
        	foreach($html->find('.help-back') as $rem)
        		$rem->outertext = '';
        	
        $response = $html->find('body', 0);

        $response = str_replace("Painel Boa vista SCPC", 'Resultado da Pesquisa', $response);
        $response = str_replace($this->nome_empresa, '', $response);
        $response = str_replace('="resources/images/', '="https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/images/', $response);
        //$response = str_replace('/FamiliaAcertaPFWeb/resources/', 'https://consumer.bvsnet.com.br/FamiliaAcertaPFWeb/resources/', $response);

        return $response;
		
	}

}

if(isset($_GET['keepAlive'])){
	$bv = new BoaVista();
	$bv->do_login();
}

/*
DESATIVADO TEMPORARIAMENTE PRA NINGUEM TENTAR NADA

if(isset($_GET['keepAlive'])){
	$bv = new BoaVista();
	$bv->do_login();
}
if(isset($_POST['cnpj'])){

	if(!$_SESSION) @session_start();

	$bv = new BoaVista();
	$con = new Conexao();

	if(limitarConsulta($con, $_SESSION['usuario'], 'bv3', 1) <= 0)
		die("<script>alert('Créditos insuficientes!');</script>"); 

	$result = $bv->limpar_consulta($bv->consulta_cnpj($_POST['cnpj']));

	registrarConsulta($con, $_SESSION['usuario'], 'bv3');
    
    echo $result;

}

if(isset($_POST['cpf'])){

	if(!$_SESSION) @session_start();

	$bv = new BoaVista();
	$con = new Conexao();

	if(limitarConsulta($con, $_SESSION['usuario'], 'bv2', 1) <= 0)
		die("<script>alert('Créditos insuficientes!');</script>"); 

	$result = $bv->limpar_consulta($bv->consulta_cpf($_POST['cpf']));

	registrarConsulta($con, $_SESSION['usuario'], 'bv2');
    
    echo $result;
}*/
