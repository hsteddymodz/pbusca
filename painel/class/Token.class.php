<?php
if(!class_exists("Token")){
class Token {
	private $token;
	function __construct($curta_duracao = false){
		if(!$_SESSION) @session_start();
		if(!$_SESSION['usuario']) die("Usuário não logado.");
		$usuario = $_SESSION['usuario'];
		include('Conexao.class.php');
		$con = new Conexao();
		// duracao do token

		if($curta_duracao)
			$validade = 2 * 60; // 2 minutos
		else 
			$validade = 15 * 60; // 15 minutos

		$last_token = $con->select('data')->from('token')->where("usuario = '$usuario'")->orderby('data desc')->limit(1)->executeNGet('data');
		if($last_token){
			if( (time()-strtotime($last_token)) < $validade ) {
				$this->token = $con->select('token')->from('token')->where("usuario = '$usuario'")->orderby('data desc')->limit(1)->executeNGet('token');
				return;
			}
		}
		// gera um novo token para o usuario
		$token_string = md5(time().$usuario.md5(rand()));
		// armazena ele no banco de dados
		$con->insert('token', array('token'=>$token_string, 'data'=>date('Y-m-d H:i:s'), 'usuario'=>$usuario));
		$this->token = $token_string;
	}
	function get_token(){
		return $this->token;
	}
}
}
?>