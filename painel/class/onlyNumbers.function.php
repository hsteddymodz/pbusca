<?php

function onlyNumbers($str){

	$final = '';
	for($k = 0; $k < strlen($str); $k++){
		if(is_numeric($str[$k])) $final .= $str[$k];
	}
	
	return $final;

}

?>