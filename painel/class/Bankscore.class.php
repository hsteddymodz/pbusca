<?php

include('curl_get_contents.function.php');
if (!class_exists('Bankscore')) {

	class Bankscore
	{

		private $usuario = 'ricardo.souza', $senha = '4005e005a2299b5648026e461575103f', $key;
		public $retorno;

		function __construct() {
			$res = json_decode(file_get_contents('http://ws.bcitecnologia.com.br/api/bs/v1/login?usuario=' . $this->usuario . '&senha=' . $this->senha), 1);
			if($_GET['teste'])
				var_dump($res);

			if ($res['status'] == 1) $this->key = $res['key'];
			else die('Credenciais expiradas.');
		}

		function pesquisa_cpf($cpf)
		{
			$cpf = preg_replace("/[^0-9]/", "", $cpf);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY: ' . $this->key));
			curl_setopt($ch, CURLOPT_URL, "http://ws.bcitecnologia.com.br/api/bs/v1/consulta/produto/54");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "cpf=" . $cpf);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);
			curl_close($ch);

			if($_GET['teste'])
				var_dump($server_output);

			$this->retorno = json_decode($server_output, 1);
		}

		function pesquisa_cnpj($cnpj)
		{
			$cnpj = preg_replace("/[^0-9]/", "", $cnpj);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY: ' . $this->key));
			curl_setopt($ch, CURLOPT_URL, "http://ws.bcitecnologia.com.br/api/bs/v1/consulta/produto/71");
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			curl_setopt($ch, CURLOPT_POSTFIELDS, "cnpj=" . $cnpj);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);
			curl_close($ch);

			if($_GET['teste'])
				var_dump($server_output);

			$this->retorno = json_decode($server_output, 1);
		}



		function houve_erro()
		{
			if ($this->retorno['status'] == 1)
				return false;
			else
				return true;
		}



		function get_erro()
		{
			if ($this->retorno['status'] == 2)
				return "Registro não encontrado";
			elseif ($this->retorno['status'] == 3)
				return "Falha na execução";
			else
				return "Falha desconhecida";
		}



		function get_retorno()
		{
			if ($this->retorno['status'] == 1)
				return $this->retorno['resultado'];
			else
				return $this->get_erro();
		}
	}
}



if (!function_exists('verificar_resultado')) {



	function verificar_resultado($arr, $ch)
	{



		$retornoar = false;

		foreach ($arr as $a) {

			if ($a[$ch]) return true;

		}



	}



}

if($_GET['teste']) {
	$bs = new Bankscore();
	$bs->pesquisa_cnpj('26093733000101');
}

?>