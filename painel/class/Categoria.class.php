<?php

class Categoria{

	private $nome, $descricao;

	function __construct($n, $d = ''){

		$this->nome      = $n;
		$this->descricao = $d;

	}

	function persist(){

		include('Conexao.class.php');
		include('Formatar.class.php');

		if(strlen($this->nome) == 0)
			return false;

		$con = new Conexao();

		$url = new Formatar($this->nome);
		$url = $url->getUrl();

		// Verifica se existe alguma categoria com essa URL
		$num = $con
					->select("count(*) as n")
					->from("categoria")
					->where("cat_url = '$url'")
					->limit(1)
					->executeNGet('n');

		if($num == 0) // Se nao existir
			return $con->insert('categoria', array('cat_nome' => $this->nome, 'cat_url' => $url, 'cat_descricao'=>$this->descricao));
		else{

			$cod = $con->insert('categoria', array('cat_nome' => $this->nome, 'cat_url' => '', 'cat_descricao'=>$this->descricao));
			$con->update('categoria', array('cat_url'=> $cod.'-'.$url), $cod, 'cat_codigo');
			return $cod;
		}



	}

}

?>