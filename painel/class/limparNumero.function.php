<?php



function limparNumero($string){

	$ret = '';
	for($k = 0; $k < strlen($string); $k++){
		if(is_numeric($string[$k]))
			$ret .= $string[$k];
	}

	return $ret;

}



?>