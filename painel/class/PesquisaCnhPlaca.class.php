<?php

header('Content-Type: application/json; charset=utf-8');
include('Tracker.class.php');
//error_reporting(E_ALL);

class PesquisaCnhPlaca {

	private $resultado, $content, $dado, $tipo;

	function __construct($dado, $tipo){

		$this->dado = $dado;
		$this->tipo = $tipo;

		if($tipo == 'cpf')
			$url = 'https://api.ultradev.info/consultar?token=do182omnh1ui3nhru21&pesquisa=cnh&versao=SIDS1&doc=' . $dado;

		else if($tipo == 'placa')
			$url = 'https://api.ultradev.info/consultar?token=do182omnh1ui3nhru21&pesquisa=placa&versao=SIDS1&doc=' . $dado;
		else
			return json_encode(array('error'=>1, 'msg'=> 'Tipo de pesquisa inválido.'));

		$ch = curl_init();
		//curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);

		curl_setopt ($ch, CURLOPT_TIMEOUT, 10);

		/*curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $dados);*/
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);

		$content = curl_exec ($ch);
		$this->content = $content;

		if(curl_error($ch)) die(curl_error($ch));

		curl_close ($ch);

		$this->resultado = $content;
	}

	function get_content(){
		return $this->content;
	}

	function get_resultado(){

		$start = strpos($this->content, '{"');
		$fixed_json_content = substr($this->content, $start);

		$conteudo_cnh = json_decode($fixed_json_content, 1);

		if($conteudo_cnh['status'] != 'OK')
			die(json_encode(array('error'=> '1', 'msg'=> $conteudo_cnh['status'])));

		$doc = $this->dado;
		if($this->tipo == 'placa'){
			//var_dump($conteudo_cnh);
			if(!isset($conteudo_cnh['doc']) || $conteudo_cnh['tipoDoc'] != 'CPF')
				die(json_encode(array('error'=> '1', 'msg'=> 'Dados do condutor não encontrados para esta placa.')));
			$doc = substr('000000000' . $conteudo_cnh['doc'], -11);
		}

		$conteudo_tracker1 = json_decode(do_consulta(array(
			'tipo' => 'telefone',
			'cpf' => $doc,
		)), 1);

		if(!isset($conteudo_tracker1[0]))
			die(json_encode(array('error'=> '1', 'msg'=> 'Não encontramos informações sobre o CPF informado.')));

		$conteudo_tracker2 = json_decode(do_consulta(array(
			'tipo' => 'idpf',
			'id' => $conteudo_tracker1[0]['id']
		)), 1);

		if($this->tipo == 'cpf')
			$final = array_merge(array('cnh' => $conteudo_cnh), $conteudo_tracker2);
		else
			$final = array_merge(array('carro' => $conteudo_cnh), $conteudo_tracker2);


		echo json_encode($final);

	}

}

if(!isset($_SESSION))
	@session_start();

if(isset($_GET['debug'])/* && isset($_POST['dado'])*/ && isset($_SESSION['usuario'])) {
	$pcp = new PesquisaCnhPlaca('01831958252', 'cpf');
	$pcp->get_resultado();
	die();
}

if(isset($_POST['dado']) && isset($_POST['tipo']) && isset($_POST['token']) && isset($_SESSION['usuario'])) {

	include('LimitarConsulta.function.php');
	include('RegistrarConsulta.php');

	$plataforma = ($_POST['tipo'] == 'cpf')? 'cnh':'placa';
	if(limitarConsulta(null, $_SESSION['usuario'], $plataforma, true) > 0) {
		$pcp = new PesquisaCnhPlaca($_POST['dado'], $_POST['tipo']);
		$pcp->get_resultado();
		registrarConsulta(null, $_SESSION['usuario'], $plataforma);
	} else
		die('{"error":true, "msg":"Você não tem créditos para isso!"}');
	
} 


?>
