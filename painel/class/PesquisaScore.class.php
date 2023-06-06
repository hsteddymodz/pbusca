<?php
class PesquisaScore {

	private $resultado, $erro;

	function __construct($doc){

		include('limparNumero.function.php');
		include('get_config_info.function.php');

		$doc            = limparNumero($doc);

		if(strlen($doc) != 11 && strlen($doc) != 14){

			$this->erro = true;

		}else{

			$ini_file       = get_config_info();
			$credenciais_ps = $ini_file['score'];
			$url            = $credenciais_ps['link'];

			$this->erro = false;
			
			if(strlen($doc) == 11)
				$dados = "token=4af0c23d172c463916194f0da2c2c88de9ecff67d31c011f8afed8b0fbda9323&versao=&pesquisa=scoreCpf&doc=$doc";
			else
				$dados = "token=4af0c23d172c463916194f0da2c2c88de9ecff67d31c011f8afed8b0fbda9323&versao=&pesquisa=scoreCnpj&doc=$doc";

			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $url);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $dados);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);

			$content = curl_exec ($ch);

			if(curl_error($ch)) die(curl_error($ch));

			curl_close ($ch);

			$this->resultado = $content;

		}

	}

	function get_resultado(){

		if($this->erro == true)
			return false;
		
		include('simple_html_dom.php');
		$html = str_get_html($this->resultado);

		/*if(!$html->find('#container > div > div:nth-child(3) > table > tbody > tr > td', 0) && )
			return false;*/

		$html->find('style', 0)->outertext = '';

		$html->find('#container > div > div', 1)->outertext = '';
		$html->find('#divClick', 0)->outertext = '';
		$html->find('#container', 1)->outertext = '';
		foreach($html->find('.help-back') as $hb){
			$hb->outertext = '';
		}

		if(trim($html->outertext) == '')
			return false;

		return $html->outertext;


	}

}
