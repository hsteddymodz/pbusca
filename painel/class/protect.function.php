<?php

if(!function_exists('protect')){

		function protect($tipos_autorizados = array()){

			if(!isset($_SESSION)) @session_start();
			if(!isset($_SESSION['usuario']) || $_SESSION['usuario'] == '' || !isset($_SESSION['tipo'])){
				unset($_SESSION);
				die('<script>location.href="https://probusca.com";</script>');
			}

			if(in_array($_SESSION['tipo'], $tipos_autorizados))
				return true;
			else{
				unset($_SESSION);
				die('<script>location.href="https://probusca.com";</script>');
			}

		}

}

?>