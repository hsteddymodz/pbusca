<?php

class Unitfour {

	private $usuario = 'AGILECOBRANCA_teste', $senha = 'ISlvSQ0', $cliente = 'Teste';

	function login(){

		$cookies = md5(time()).".txt"; //NAO MECHA

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, "");

		curl_setopt($ch, CURLOPT_AUTOREFERER, true);

	    curl_setopt($ch, CURLOPT_URL, "http://intouch.unitfour.com.br/Login.aspx");//NAO MECHA
		curl_setopt($ch, CURLOPT_REFERER, "http://intouch.unitfour.com.br/Login.aspx");//NAO MECHA

		curl_setopt($ch, CURLOPT_COOKIESESSION, true );
	    curl_setopt($ch, CURLOPT_COOKIE, $cookies);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies);
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies);

	    $data = "LoginTextBoxUsuario=".$usuario."&LoginTextBoxSenha=".$senha."&LoginTextBoxCliente=".$cliente."&btnLogin=Fazer+Login";


    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects 

	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    

	    if(curl_errno($ch)){
	    	print_r(curl_error($ch));
	    	die();
	    }

	    $neylog1 = curl_exec($ch);
	    echo ($neylog1);

	}

	function get_web_page( $url, $cookiesIn = ''){

		$x = curl_init();

		$data = "LoginTextBoxUsuario=".$usuario."&LoginTextBoxSenha=".$senha."&LoginTextBoxCliente=".$cliente."&btnLogin=Fazer+Login";

		//header('Content-type: image/jpeg');

		curl_setopt_array($x, [
		    CURLOPT_URL => "http://intouch.unitfour.com.br/Login.aspx",
		    CURLOPT_POST => true,
		    CURLOPT_POSTFIELDS => $data,
		    CURLOPT_SSL_VERIFYPEER => false,
		    CURLOPT_HTTPHEADER => [
		        'Accept-Encoding: gzip, deflate',
		        'Host: intouch.unitfour.com.br',
		        'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3',
		        'User-Agent: Mozilla/5.0 (Windows NT 6.3; WOW64; rv:53.0) Gecko/20100101 Firefox/53.0',
		        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		        'Referer: http://intouch.unitfour.com.br',
		        'Content-Type: application/x-www-form-urlencoded',
		        'Connection: keep-alive',
		        'Upgrade-Insecure-Requests: 1'
		    ]
		]);

		curl_exec($x);

	}

} 

$var = new Unitfour();
echo $var->get_web_page('http://intouch.unitfour.com.br/Login.aspx', md5(time()));

?>