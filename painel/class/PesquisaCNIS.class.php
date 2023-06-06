<?php

include('LimitarConsulta.function.php');
include('RegistrarConsulta.php');

if(!isset($_SESSION)) @session_start();
$usr = $_SESSION['usuario'];
if(!$usr || $usr <= 0){
    echo json_encode('{"success":"false", "response":"USERLOGGEDOUT"}', 1);
    exit();
}
// Fim das validações de segurança 


class PesquisaCNIS {

    private $url;
    private $key;
    private $resultado;
    protected $apiUrl;

    function __construct($cpf) {
        $cpf = preg_replace("/[^0-9]/", "", $cpf);
        include('get_config_info.function.php');

        $ini_file = get_config_info();
        $this->url = $ini_file['cnis']['link'];
        $this->key = $ini_file['cnis']['chave'];

        $this->apiUrl = $this->url.'/?KEY='.$this->key.'&SERVICO=CNIS&OPCAO=extrato&TIPO=CPF&CPF='.$cpf;

        // Inicia o request
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "postman-token: 4a8b1d84-0dbf-0063-c5eb-135cab3eba25"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }

		// Debug de erro
		if(curl_errno($ch)) die(curl_error($ch));
        curl_close ($ch);

        $this->resultado = $response;
    }

    function get_resultado() {
		return $this->resultado;
	}

	function get_resultadoAjax() {
		return json_encode($this->resultado, 1);
	}

	function get_content() {
		return json_decode($this->content, 1);
	}
}

if (isset($_POST['cpf']) && !empty($_POST['cpf'])) {
    $pesquisa = new PesquisaCNIS($_POST['cpf']);
    $result = $pesquisa->get_resultado();
    
    if (isset($result) && !empty($result)) {
        if(limitarConsulta(null, $_SESSION['usuario'], 'cnis', 1) <= 0){
            echo json_encode('{"success":"false", "response":"CREDITOSINSUF"}', 1);
            exit();
        } else {
            registrarConsulta(null, $_SESSION['usuario'], 'cnis');
            echo json_encode($result);
        }
    } 
}
