<?php



class Natt {



	private $token = "65687960-88E2-4B7F-AAF1-DA55D8F9789A", $curl, $cookieFile, $tipo;



	function __construct($tipo = 'cpf'){

		if(!function_exists('str_get_html'))
			include('simple_html_dom.php');

		// OK cool - then let's create a new cURL resource handle

	    $ch = curl_init();
	    $this->cookieFile = 'NattCookie.txt';
	    $this->tipo       = $tipo;

	    curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/auth/");
	    curl_setopt($ch, CURLOPT_REFERER, "https://natt.com.br/sistema/consultas/auth/");
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_POST, 1);

	    if($tipo == 'cpf')
			curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->token."&type=cpf&cpf=01831958252");
		elseif($tipo == 'cnpj')
			curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->token."&type=cnpj&cnpj=26093733000101");
		elseif($tipo == 'telefone')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->token."&type=telefone&telefone=2433533095");
	 	elseif($tipo == 'endereco')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->token."&type=endereco&endereco=rua+prefeito+tigre+maia&cidade=itajuba&estado=mg");
	 	elseif($tipo == 'nome')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->token."&type=nome&nome=joao victor costa de oliveira");
	 	elseif($tipo == 'cep')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS, "token=".$this->token."&type=cep&cep=37500208&ninicio=100&nfim=1000");

	    $output = curl_exec($ch);

	    if(isset($_GET['debug']))
	    	var_dump($output);

	    if(curl_error($ch)) die(curl_error($ch));
	    $this->curl = $ch;

	}



	function consultaNome($nome){

		$ch = $this->curl;
		curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/nome/Resposta0201.php");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "e_nome=".$nome);

	    $output = curl_exec($ch);

	    if(isset($_GET['debug']))
	    	var_dump($output);

	    if(curl_error($ch)) die(curl_error($ch));
	    if($this->verifyContent($output) == false) return false;
	    
	    $html = str_get_html($output);
	    $arr_estados = array();
	    foreach($html->find('#cTable tr') as $tr){
	    	if($tr->children(1)){
	    		$qtd = strip_tags($tr->children(1)->find('font', 0)->innertext);
	    		$tmp = explode("','", $tr->onclick);
	    		$uf  = str_replace("')", '', $tmp[2]);
	    		if($uf != 'XX')
	    			$arr_estados[$uf] = $qtd;
	    	}
	    }

	    return $arr_estados;


	}



	function consultaNomeEstado($nome, $estado, $pagina = 1){



		$nome = urlencode(strtoupper($nome));



		$ch = $this->curl;

		curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/nome/Resposta0202.php");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);



	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);



	    curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, "e_pag=$pagina&e_nome=$nome&e_uf=$estado");



	    $output            = curl_exec($ch);

	    if(isset($_GET['debug']))
	    	var_dump($output);



	    if(curl_error($ch)) die(curl_error($ch));



	    if($this->verifyContent($output) == false) return false;



	    $html              = str_get_html($output);



	    // puxa os nomes e cpfs

	    $linhas            = $html->find('.elink');

	    $pessoas           = array();

	    $k                 = 0;



	    if(count($linhas) == 0 || !is_array($linhas)) return false;



	    foreach($linhas as $tr){



	    	$pessoas[$k]['cpf']    = trim($tr->children(1)->innertext);

	    	$pessoas[$k]['nome']   = trim($tr->children(2)->innertext);

	    	$pessoas[$k]['cidade'] = trim($tr->children(3)->innertext);



	    	$k++;



	    }



	    // contabiliza a quantidade de paginas

	    $qtd_paginas       = count($html->find('.paginacao a'))-1;



	    // se não for a primeira página, retorna o array de valores já

	    if($pagina > 1 || $qtd_paginas == 0) return $pessoas;



	    for($k = 2; $k <= $qtd_paginas; $k++){

	    	$pessoas = array_merge($this->consultaNomeEstado($nome, $estado, $k), $pessoas);

	    }



	    return $pessoas;





	}







	function consultaCpf($cpf){



		$ch = $this->curl;

		curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/cpf/Resposta0101.php");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);



	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);



	    curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS,

            "e_cpf=".$cpf);



		// Download the given URL, and return output

	    $output = curl_exec($ch);

	    if(isset($_GET['debug']))
	    	var_dump($output);

	    if(curl_error($ch)) die(curl_error($ch));







	    $html = str_get_html($output);



	    if($html->find('#cTable td',1)){

	    	$tmp  = explode('<h3>', $html->find('#cTable td',1)->find('h3', 0)->innertext);

	   		if(strlen(trim($tmp[0])) == 0) return false;

	   	}else{



	   		$natt = new Natt($this->tipo);

	   		return $natt->consultaCpf($cpf);



	   	}



	    return $this->prepareContent($output);





	}



	function consultaCep($cep, $inicio, $fim, $pagina = 1){



		$ch = $this->curl;

		curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/cep/Resposta0501.php");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);



	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);



	    curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS,

            "e_cep=$cep&e_numero_inicio=$inicio&e_numero_fim=$fim&e_pag=$pagina");



		// Download the given URL, and return output

	    $output = curl_exec($ch);

	    if(isset($_GET['debug']))
	    	var_dump($output);

	    if(curl_error($ch)) die(curl_error($ch));



	    if($this->verifyContent($output) == false) return false;



	    $html              = str_get_html($output);



	    // puxa os nomes e cpfs

	    $linhas            = $html->find('.elink');

	    $pessoas           = array();

	    $k                 = 0;



	    if(count($linhas) == 0 || !is_array($linhas)) return array();



	    foreach($linhas as $tr){



	    	$pessoas[$k]['cpf']         = trim($tr->children(0)->innertext);

	    	$pessoas[$k]['nome']        = trim($tr->children(1)->innertext);

	    	$pessoas[$k]['logradouro']  = trim($tr->children(2)->innertext);

	    	$pessoas[$k]['numero']      = trim($tr->children(3)->innertext);

	    	$pessoas[$k]['complemento'] = trim($tr->children(4)->innertext);

	    	$pessoas[$k]['bairro']      = trim($tr->children(5)->innertext);

	    	$pessoas[$k]['cep']         = trim($tr->children(6)->innertext);



	    	$k++;



	    }



	    // contabiliza a quantidade de paginas

	    $qtd_paginas       = count($html->find('.paginacao a'))-1;



	    // se não for a primeira página, retorna o array de valores já

	    if($pagina > 1 || $qtd_paginas == 0) return $pessoas;



	    for($k = 2; $k <= $qtd_paginas; $k++){

	    	$pessoas = array_merge($this->consultaCep($cep, $inicio, $fim, $k), $pessoas);

	    }



	    return $pessoas;





	}



	function consultaCnpj($cnpj){



		$ch = $this->curl;

		curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/cpf/Resposta0102.php");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);



	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);



	    curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS,

            "e_cnpj=".$cnpj);



		// Download the given URL, and return output

	    $output = curl_exec($ch);
	    if(isset($_GET['debug']))
	    	var_dump($output);


	   	if(curl_error($ch)) die(curl_error($ch));



	   	$output = str_replace('https://www.natt.com.br/sistema/maps/mapa.php?', 'https://probusca.com/painel/page/mapa.php?', $output);



	    $html = str_get_html($output);



	    if(!$html->find('#aTable h3', 0)) return false;



	    $tmp = explode('<h3', $html->find('#aTable h3', 0)->innertext);



	    if(strlen(trim($tmp[0])) == 0) return false;



	    return $this->prepareContent($output);





	}



	function verifyContent($str){



		$strpos = strpos($str, 'Tempo de Acesso Excedido');

		$strpos2 = strpos($str, 'Natt Consultas');



		if ($strpos === false && $strpos2 === false) 

			return $str;

		else

			return false;



	}



	function prepareContent($str){	





		$strpos = strpos($str, 'Tempo de Acesso Excedido');

		$strpos2 = strpos($str, 'Natt Consultas');

		$strpos3 = strpos($str, 'Selecione o Produto');



		$html = str_get_html($str);

		

		if($html->find('.header', 0)) $html->find('.header', 0)->outertext = '';

		if($html->find('.article', 0)) $html->find('.article', 0)->outertext = '';



		if ($strpos === false && $strpos2 === false && $strpos3 == false) 

			return $html->outertext;

		else

			return false;



	}



	function consultaTelefone($tel){



		$ch = $this->curl;

		curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/telefone/Resposta0301.php");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);



	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);



	    curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS,

            "e_telefone=".$tel);



		// Download the given URL, and return output

	    $output = curl_exec($ch);

	    if(isset($_GET['debug']))
	    	var_dump($output);

	    if(curl_error($ch)) die(curl_error($ch));



	    $html = str_get_html($output);



	    $url  = str_replace('../', '', $html->find('#form1', 0)->action);

	    $valor= $html->find('input[name=e_cpf]', 0)->value;



	    curl_setopt($ch, CURLOPT_URL, "https://natt.com.br/sistema/consultas/" . $url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);

		curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);



	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);



	    curl_setopt($ch, CURLOPT_POST, 1);

		curl_setopt($ch, CURLOPT_POSTFIELDS, "e_cpf=".$valor);



		// Download the given URL, and return output

	    $output = curl_exec($ch);



	    return $this->prepareContent($output);



	}





}

/* CONSULTA POR TELEFONE */

if(isset($_GET['debug'])) {

	/*
	$n = new Natt('telefone');
	var_dump ($n->consultaTelefone('2433533095'));


	
	$n = new Natt('nome');
	var_dump($n->consultaNome('JOAO VICTOR COSTA DE OLIVEIRA'));


	$n = new Natt('cpf');
	echo ($n->consultaCpf('01831958252'));

	*/

	$n = new Natt('cep');
	var_dump($n->consultaCep('37500208', '100', '1000'));
	

}

?>