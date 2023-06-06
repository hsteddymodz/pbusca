<?php

if (!class_exists("Credito")) {

	class Credito
	{

		private $con;

		function __construct($conexao)
		{
			$this->con = $conexao;
		}

		function gastar_credito($usuario, $plataforma)
		{
			$today = new DateTime();
			$dt = $today->format('Y-m-d H:i:s');

			$plataforma = ($plataforma);
			$usuario = intval($usuario);

			if ($this->con->insert('credito', array('valor' => '-1', 'usuario' => $usuario, 'plataforma' => $plataforma, 'data' => $dt))) {
				$this->con->insert('usuario_consulta', array('usuario' => $usuario, 'plataforma' => $plataforma, 'data' => $dt));
				$this->con->execute("update login set ultima_atividade = '" . date('Y-m-d H:i:s') . "' where usuario = '$usuario' and data_logout IS NULL");
				return array('erro' => 0, 'msg' => 'Crédito gasto com sucesso!');
			} else {
				return array('erro' => 1, 'msg' => 'Falha ao gastar crédito!');
			}
		}



		function add_credito($quantidade, $plataforma, $usuario, $vencimento)
		{


			if (!$quantidade)
				return array('erro' => 1, 'msg' => 'A quantidade de créditos precisa ser inteira e numérica.');

			$plataforma = ($plataforma);
			$usuario = intval($usuario);
			$quantidade = intval($quantidade);

			if (substr_count($vencimento, '/') == 2)
				$vencimento = implode('-', array_reverse(explode('/', $vencimento)));

			elseif (substr_count($vencimento, '-') == 2)
				$vencimento = $vencimento;

			elseif (is_numeric($vencimento))
				$vencimento = date('Y-m-d', strtotime('+' . $vencimento . ' days'));


			$dados = array('valor' => $quantidade, 'usuario' => $usuario, 'plataforma' => $plataforma, 'vencimento' => $vencimento, 'data' => date("Y-m-d H:i:s"));


			if ($this->con->insert('credito', $dados))
				return array('erro' => 0, 'msg' => 'Crédito adicionado com sucesso!');
			else
				return array('erro' => 1, 'msg' => 'Falha ao adicionar crédito!');

		}



		function get_num_creditos($plataforma, $usuario)
		{
			return $this->con->select('sum(valor) as c')->from('credito')->where("usuario = '$usuario' AND plataforma = '$plataforma' and (vencimento is NULL or vencimento = '' or vencimento >= NOW())")->limit(1)->executeNGet('c');
		}



		function get_extrato($usuario)
		{

			return $this->con->select('*')->from('credito c, plataforma p')->where("c.usuario = '$usuario' and p.codigo = c.plataforma")->executeNGet();

		}



	}



}

?>