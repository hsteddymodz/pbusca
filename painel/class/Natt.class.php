<?php

class Natt {

	private $token = "65687960-88E2-4B7F-AAF1-DA55D8F9789A", $tipo = null, $dado = null, $resultado = null, $resultado_cru = null;

	function __construct($tipo, $dado){

		$this->tipo = $tipo;
		$this->dado = $dado;

		$ch = curl_init();
	 
	    // Set URL to download
	    curl_setopt($ch, CURLOPT_URL, "https://www.natt.com.br/sistema/consultas/api/");
	 	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_POST, 1);

	    if($tipo == 'cpf')
			curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$this->token."&consulta=cpf&cpf={$dado['cpf']}");
		elseif($tipo == 'cnpj')
			curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$this->token."&consulta=cnpj&cnpj={$dado['cnpj']}");
		elseif($tipo == 'telefone')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$this->token."&consulta=telefone&telefone={$dado['telefone']}");
	 	elseif($tipo == 'endereco')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$this->token."&consulta=endereco&endereco={$dado['endereco']}&cidade={$dado['cidade']}&estado={$dado['estado']}");
	 	elseif($tipo == 'nome')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$this->token."&consulta=nome&nome={$dado['nome']}");
	 	elseif($tipo == 'cep')
	 		curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$this->token."&consulta=cep&cep={$dado['cep']}&ninicio={$dado['inicio']}&nfim={$dado['fim']}");

	    // Download the given URL, and return output
	    $output = curl_exec($ch);

	    if($_GET['online_test'])
	    	var_dump($output);

	    if(curl_errno($ch)) die(curl_error($ch));

	    $this->resultado_cru = $output;

	   	$this->resultado = preg_replace('~<p(.*?)</p>~Usi', "", $output);

	   	curl_close($ch);

	}

	function get_resultado_cru(){
		return $this->resultado_cru;
	}

	function get_tipo(){
		return $this->tipo;
	}

	function error(){
		return (!$this->resultado['status']);
	}

	function get_error(){
		if($this->error())
			return $this->resultado['msg'];
	}

	function get_dados($debug = false){

		$retorno = json_decode($this->resultado, 1);

		if($debug) if(!$retorno['status']) die($retorno['msg']);
			
		return $retorno;

	}

}

if($_GET['online_test']){

	$n = new Natt('cep', '37500019');
	var_dump( $n->get_dados(1));

}
?>