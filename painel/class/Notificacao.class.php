<?php

class Notificacao{

	private $msg, $titulo, $tipo;

	function __construct($msg, $titulo, $tipo = 'success'){

		// $msg: Mensagem a ser exibida
		// $tipo: Tipo da Postagem
		// $titulo: Titulo da Postagem

		$this->titulo = $titulo;
		$this->msg    = $msg;
		$this->tipo   = $tipo;

	}

	function show(){

		echo '
		<div class="alert alert-'.$this->tipo.' alert-block"> <a class="close" data-dismiss="alert" href="#">×</a>
              <h4 class="alert-heading">'.$this->titulo.'</h4>
              '.$this->msg.'
          </div>';

	}

}

?>