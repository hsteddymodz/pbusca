<?php



if (!function_exists("registrarConsulta")) {

	function registrarConsulta($con, $usuario, $plataforma_tipo)
	{

		include("Credito.class.php");
		include("Conexao.class.php");

		$conexao = new Conexao();
		$CRED = new Credito($conexao);

		if ($CRED->get_num_creditos($plataforma_tipo, $usuario) > 0) {
			return $CRED->gastar_credito($usuario, $plataforma_tipo);
		}	else {
			$today = new DateTime();
			$dt = $today->format('Y-m-d H:i:s');
			$conexao->insert('usuario_consulta', array('usuario' => $usuario, 'plataforma' => $plataforma_tipo, 'data' => $dt));
			$conexao->execute("update login set ultima_atividade = '" . date('Y-m-d H:i:s') . "' where usuario = '$usuario' and data_logout IS NULL");
		}

		$codigo = $conexao->getCodigo();
		$conexao->close();
		return $codigo;
	}

}

?>