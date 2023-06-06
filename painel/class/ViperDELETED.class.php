<?php

if(!class_exists("Viper")){

	class Viper {
	
		private $username = "agile", $password = "102030", $html, $ch, $tipo, $data, $codigo_inicial, $nb;

		function __construct($cpf_ou_nb, $tipo_da_pesquisa){

			include("simple_html_dom.php");
			$this->html = new simple_html_dom();
			$this->data = $cpf_ou_nb;
			$this->tipo = $tipo_da_pesquisa;

		}

		function get_consulta_bruta(){
			return $this->codigo_inicial;
		}

		function do_login(){

			$url = "http://sis21.viperconsig.com.br/acesso/login"; 
			$postinfo = "LoginForm[name]=".$this->username."&LoginForm[password]=".$this->password;


			$path             = DOC_ROOT."/ctemp";
			$cookie_file_path = "$path/cookie.txt";

			$this->ch = curl_init();
			curl_setopt($this->ch, CURLOPT_HEADER, false);
			curl_setopt($this->ch, CURLOPT_NOBODY, false);
			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);

			curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookie_file_path);
			//set the cookie the site has for certain features, this is optional
			//curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");
			curl_setopt($this->ch, CURLOPT_USERAGENT,
			    "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
			curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($this->ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postinfo);
			$res = curl_exec($this->ch);

		}

		function do_consulta(){

			$this->do_login();

			if($this->tipo == 'inss'){

				$string = "compe=201707&status=consulta&cpf_nb=" . $this->data;
				$url    = "http://sis21.viperconsig.com.br/Pesqext/pesq";

			}else if($this->tipo == 'siape'){

				$string = "cpf_matricula=" . $this->data;
				$url    = "http://sis21.viperconsig.com.br/Pesqcad/pesq";

			}else if($this->tipo == 'exercito'){

				$string = "cpf_prec=" . $this->data;
				$url = "http://sis21.viperconsig.com.br/Pesqmex/pesq";

			}else if($this->tipo == 'aeronautica'){

				$string = "cpf_ordem=" . $this->data;
				$url = "http://sis21.viperconsig.com.br/Pesqaero/pesq";

			}else{

				$string = "compe=201702&status=consulta&cpf_nb=" . $this->data;
				$url = "http://sis21.viperconsig.com.br/Pesqcad/pesq";

			}

			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($this->ch, CURLOPT_POST, 1);
			curl_setopt($this->ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
			curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($this->ch, CURLOPT_POSTFIELDS, $string);

			$this->codigo_inicial = curl_exec($this->ch);

			$this->html->load($this->codigo_inicial);


		}

		function get_consulta(){

			if(!$this->html->find('#nomepesq', 0))
				return "Nada encontrado para o NB/CPF fornecido.";

			$this->html->find('#nomepesq', 0)->value = trim(str_replace('Nome: ', '', $this->html->find('div.impressao',0)->children(1)->children(0)->children(0)->innertext));

			$this->html->find('.alert', 0)->outertext ='';
			$this->html->find('.btn-primary', 1)->outertext ='';
			foreach($this->html->find('.box-title') as $a) $a->outertext ='';
			foreach($this->html->find('.btn-group') as $a) $a->outertext ='';
			foreach($this->html->find('.btn-box-tool') as $a) $a->outertext ='';
			$this->html->find('.box-body', 3)->outertext = '';
			$this->html->find('.btn-primary', 0)->outertext = '';

			return $this->html->find('.col-md-11', 0)->innertext;

		}

		function get_extrato_historico($link_onclick){

			$compes = explode(',', $link_onclick);
			$compe = "";
			for($k=0; $k < strlen($compes[0]); $k++){
				if(is_numeric($compes[0][$k])) $compe .= $compes[0][$k];
			}

			$reserva = $this->html->find('#reserva'.$this->nb, 0)->innertext;

			$desc      = $this->html->find('#desc_mrg'.$this->nb, 0)->innertext;
			$url_extra = "http://sis21.viperconsig.com.br/extratos/ExtratoHist/nb/".$this->nb."/?desc={$desc}&compe={$compe}&reserva={$reserva}&st=0";

			curl_setopt($this->ch, CURLOPT_URL, $url_extra);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
				'Host: sis21.viperconsig.com.br',
				'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0',
				'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3',
				'Referer: http://sis21.viperconsig.com.br/Pesqext/pesq',
				'Connection: keep-alive',
				'Upgrade-Insecure-Requests: 1'
		    ));

			return curl_exec($this->ch);

		}

		function get_extrato(){

			$this->html->load($this->codigo_inicial);
			$exp     = explode('/', $this->html->find('a.iframe', 0)->href);
			$this->nb      = $exp[4];

			$especie = substr($this->html->find(".form-control", 2)->value, 0, 2);
			$url     = "http://sis21.viperconsig.com.br/extratos/Extrato/nb/".$this->nb."/?especie=$especie";

			curl_setopt($this->ch, CURLOPT_URL, $url);
			curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
				'Host: sis21.viperconsig.com.br',
				'User-Agent: Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:56.0) Gecko/20100101 Firefox/56.0',
				'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3',
				'Referer: http://sis21.viperconsig.com.br/Pesqext/pesq',
				'Connection: keep-alive',
				'Upgrade-Insecure-Requests: 1'
		    ));

			$html_extrato = curl_exec($this->ch);

			$pos = strpos($html_extrato, "CONTATE O ATENDIMENTO") || strpos($html_extrato, "TENTE NOVAMENTE") || strpos($html_extrato, "Sistema Viperconsig");

			//echo $html_extrato;
			if($pos !== false){

				// nao deu certo, tem que puxar o último extrato
				if(!$this->html) return false;

				if(!$this->html->find('.dropdown-menu',2)) return false;

				foreach($this->html->find('.dropdown-menu',2)->find('a') as $link){

					$html_extrato = $this->get_extrato_historico($link->onclick);
					if(strpos($html_extrato, "CONTATE O ATENDIMENTO") >= 0 || strpos($html_extrato, "TENTE NOVAMENTE") >= 0){
						// segue o
						return false;
					}else
						return $html_extrato;

				}

			}else
				return $html_extrato;
			

			return false;

		}



	}
}
?>