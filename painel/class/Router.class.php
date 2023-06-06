<?php



class Router{



	public $pagina, $arquivo, $caminho = 'page/', $param;



	function __construct($t){



		$this->param = array();



		$tmp = explode('/', $t);

		

		if(count($tmp) > 0){



			$PAGE  = $tmp[0];



			for($i = 1; $i < count($tmp); $i++)

				$this->param[] = $tmp[$i];



		}else

			$PAGE = $t;







		if($PAGE){



			if(is_file("page/$PAGE.php")){



				

				$this->pagina = $PAGE;

				$this->arquivo = $PAGE.".php";



			}else{





				$this->pagina = "inicio";

				$this->arquivo = "inicio.php";



			}



		}else{



			$this->pagina = "inicio";

			$this->arquivo = "inicio.php";

			

		}



	}



	function param($i){

		return $this->param[$i];

	}

	function dumpParams() {
		var_dump($this->param);
	}



	function incluir(){

		include($this->caminho.$this->arquivo);

	}



}



?>