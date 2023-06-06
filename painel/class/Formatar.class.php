<?php

if(!class_exists ('Formatar')){
	class Formatar {
		
		public $string;

		function __construct($ss){
			$this->string = $ss;
		}

		function getNumbers(){

			$ret = '';
			for($k = 0; $k < strlen($this->string); $k++){
				if(is_numeric($this->string[$k]))
					$ret .= $this->string[$k];
			}
			return $ret;

		}

		function getUrl(){

		    $str = strtolower(utf8_decode($this->string)); $i=1;
		    $str = strtr($str, utf8_decode('àáâãäåæçèéêëìíîïñòóôõöøùúûýýÿ'), 'aaaaaaaceeeeiiiinoooooouuuyyy');
		    $str = preg_replace("/([^a-z0-9])/",'-',utf8_encode($str));
		    while($i>0) $str = str_replace('--','-',$str,$i);
		    if (substr($str, -1) == '-') $str = substr($str, 0, -1);
		    return $str;

		}


	}
}

?>