<?php

if(!class_exists('Validar')){

	class Validar{

		public $dado;

		function __construct($dado){

			$this->dado = $dado;
		}

		function get(){
			return $this->dado;
		}

		function validarSenha(){

			if(strlen($this->dado) < 6 || strlen($this->dado) > 16)
				return false;

			return true;

		}

		function validarTelefone(){

     		$tel = preg_replace("/[^0-9]/", "", $this->dado);
     		if(strlen($tel) < 10 || strlen($tel) > 11)
     			return false;

     		return true;

		}

		function validarEmail() {

			$email = $this->dado;
			$conta = "^[a-zA-Z0-9\._-]+@";
			$domino = "[a-zA-Z0-9\._-]+.";
			$extensao = "([a-zA-Z]{2,4})$";

			$pattern = '/'.$conta.$domino.$extensao.'/';
			if (preg_match($pattern, $email))
				return true;
			else
				return false;
		}


	}

}

?>