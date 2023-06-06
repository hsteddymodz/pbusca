<?php

require_once  ("simple_html_dom.php");

/////////////////////////////////////////
//	 	FOR DEBUGGIN PROPOUSES 		  //
//ini_set('display_errors', 'On');	 //
//error_reporting(E_ALL);				//
/////////////////////////////////////


//////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  CRAWLER intouch.unitfour.com.br																			//
//	Versão 1.0																								//
// 	USO DA FUNCAO:																							//
//	searchInfo($usuario_intouch, $password_intouch, $cliente_intouch, $cnpj_ou_cpf)	;						//
//		FORMATO DO CPF 	: XXX.XXX.XXX-XX ou XXXXXXXXXXX - Use somente numeros								//
//		FORMATO DO CNPJ : XX.XXX.XXX/XXXX-XX ou XXXXXXXXXXXXXX - Use somente numeros						//
//	RETORNO																									//
//		CPF Valido: Array com as informacoes do sistema:													//
//			- CPF, Nome, Sexo, Data de Nasc., Dia de Nasc., Idade, 											//
//			- Signo, Nome da Mae, Array de Telefones, Array de Endereços, Array de E-mails					//
//		CNPJ Valido: Array com as informacoes do sistema													//
//			- CNPJ, Razao Social, Nome Fantasia, Data de Abertura, CNAE, Natureza Juridica					//
//			- Array de Telefones, Array de Endereços, Array de E-mails										//
//		CPF Invalido : Null																					//
//		CNPJ Invalido : Null																				//
//	DEPENDENCIAS																							//
//		CLASSE PHP Simple HTML DOM Parser - http://simplehtmldom.sourceforge.net/ - Ja inclusa no pacote	//
//		PHP5 cURL habilitado no web server																	//
//		Estabilidade de campos, cabecalhos http, variaveis de post do sistema InTouch						//
//	OBSERVAÇOES																								//
//		Testado em 2017-05-31 - 16:37																		//
//		Funcao de exemplo no final do arquivo ->> exemplos()												//
//		Checagem de login retirada pois impactava muito na performance, cerca de 30% a mais de tempo de		//
//			execucao, portanto,	assegure a consistencia os dados de login  !!								//
//////////////////////////////////////////////////////////////////////////////////////////////////////////////



function get_contents_search($user_param, $pass_param, $client_param, $cpfCnpj){
		
	$user_unit = $user_param;		
	$pass_unit = $pass_param;			
	$client_unit = $client_param;
	$txtCPF = $cpfCnpj;
	
	///////////////////////////////////////////
	//	PHASE 1 - GET PAGE INPUTS TO LOGIN 	//
	/////////////////////////////////////////


	// Create curl connection
	$url = 'http://intouch.unitfour.com.br/Login.aspx';
	$cookieFile = 'cookie.txt';

	$ch = curl_init();
	
	//$f = fopen('request.txt', 'w');
	//curl_setopt($ch, CURLOPT_VERBOSE , 1);
	//curl_setopt($ch, CURLOPT_STDERR  , $f);

	// We must request the login page and get the ViewState and EventValidation hidden values
	// and pass those along with the post request.

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

	$curl_scraped_page = curl_exec($ch);
	
	// Grab ViewState and EventValidation data
	$html = str_get_html($curl_scraped_page);
	$viewState = $html->find("#__VIEWSTATE", 0);
	$eventValidation = $html->find("#__EVENTVALIDATION", 0);
	$viewStateGenerator = $html->find("#__VIEWSTATEGENERATOR", 0);

	///////////////////////////////////////
	//	PHASE 2 - POST DATA TO LOGIN 	//
	//////////////////////////////////////

	// Create array of data to be posted
	// This matches exactly what is posted on Chrome/Firefox
	$post_data['__EVENTTARGET'] = '';
	$post_data['__EVENTARGUMENT'] = '';
	$post_data['__VIEWSTATE'] = $viewState->value;
	$post_data['__VIEWSTATEGENERATOR'] = $viewStateGenerator->value;
	$post_data['__EVENTVALIDATION'] = $eventValidation->value;

	//User data to login in the app
	$post_data['LoginTextBoxUsuario'] = $user_unit;
	$post_data['LoginTextBoxSenha'] = $pass_unit;
	$post_data['LoginTextBoxCliente'] = $client_unit;
	$post_data['btnLogin'] = "Fazer+Login";

	//Prepare data for posting 
	foreach ( $post_data as $key => $value) {
		if($key!='btnLogin')
			$post_items[] = rawurlencode($key) . '=' . rawurlencode($value);
		else
			$post_items[] = rawurlencode($key) . '=' . ($value);
	}
	$post_string = implode ('&', $post_items);

	$headersForPosting = [
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
		'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3',
		'Accept-Encoding: gzip, deflate',	
		'Origin: http://intouch.unitfour.com.br',
		'Host: intouch.unitfour.com.br',
		'Referer: http://intouch.unitfour.com.br/',
		'Content-Type: application/x-www-form-urlencoded',
		'Connection: keep-alive'
	];

	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setOpt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER,$headersForPosting);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
	curl_setopt($ch, CURLOPT_URL, $url);  
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

	$response = curl_exec($ch);
	$urlHome = 'intouch.unitfour.com.br/home.aspx';

	//Options to get Home.Aspx

	curl_setOpt($ch, CURLOPT_POST, false);
	curl_setOpt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');
	curl_setopt($ch, CURLOPT_REFERER, 'http://intouch.unitfour.com.br/Login.aspx');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_URL, $urlHome);
	curl_setopt($ch,CURLOPT_ENCODING , "");

	$curl_scraped_page = curl_exec($ch);
	//echo $curl_scraped_page;

	$html = str_get_html($curl_scraped_page);
	$vst = urlencode($html->find('input[id=__VSTATE]',0)->value);

	$post_data = "";
	$post_data .= "__EVENTTARGET=&";
	$post_data .= "__EVENTARGUMENT=&";
	$post_data .= "__VSTATE=".$vst."&";
	$post_data .= "ctl00%24ContentPlaceHolder1%24txtPesqCpfCnpj=".$txtCPF."&";
	//$post_data .= "ctl00%24ContentPlaceHolder1%24txtPesqCpfCnpj=18.025.940/0001-09&";
	$post_data .= "ctl00%24ContentPlaceHolder1%24btnNovaConsulta=Buscar&";
	$post_data .= "ctl00%24ContentPlaceHolder1%24radio=rbtLocalizacao";


	curl_setOpt($ch, CURLOPT_POST, true);
	curl_setOpt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:53.0) Gecko/20100101 Firefox/53.0');
	curl_setopt($ch, CURLOPT_REFERER, 'http://intouch.unitfour.com.br/Login.aspx');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_URL, $urlHome);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch,CURLOPT_ENCODING , "");

	$curl_scraped_page = curl_exec($ch);
	//echo $curl_scraped_page;


	///////////////////////////////////////////
	//	CLOSING THE DEBUG AND CONNECTION	//
	/////////////////////////////////////////
	curl_close($ch);
	//fclose($f);
	return $curl_scraped_page;
}

function checkCPF_CNPJ($txtCC){
	
	$var_aux = str_replace(".","",$txtCC);
	$var_aux = str_replace("/","",$var_aux);
	$var_aux = str_replace("-","",$var_aux);
	if(strlen($var_aux)==11)
			return "CPF";
	else if (strlen($var_aux)==14)
		return "CNPJ";
	else
		return "ERROR";
	
}

///////////////////////////////////////////////////
//		THIS FUNCTION CHECKS THE LOGIN INFO		//
// 		USE IT ONLY TO DEBUG THE CODE		   //
// 		LEAVE COMMENTED ON searchInfo	      //
// 		FOR PERFOMANCE 						 //
//////////////////////////////////////////////


function checkLogin($user_param, $pass_param, $client_param){
	return true;
}
///////////////////////////////////////////////////////////////

function extract_info_cpf($content_curl){

	$html = str_get_html($content_curl);
	
	if($html->find('input[id=ctl00_ContentPlaceHolder1_txtCpf]',0) == null)
		return null;

	$dados['CPF'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtCpf]',0)->value;
	$dados['nome'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtNome]',0)->value;

	if(($html->find('input[id=ctl00_ContentPlaceHolder1_radionBtnSexo_0]',0)->checked)=="checked")
		$dados['sexo'] = "M";
	if(($html->find('input[id=ctl00_ContentPlaceHolder1_radionBtnSexo_1]',0)->checked)=="checked")
		$dados['sexo'] = "F";
	if(($html->find('input[id=ctl00_ContentPlaceHolder1_radionBtnSexo_2]',0)->checked)=="checked")
		$dados['sexo'] = "INDEFINIDO";
		
	$dados['dataNasc'] = substr($html->find('input[id=ctl00_ContentPlaceHolder1_txtDataNascimento]',0)->value,0,10);
	$dados['diaSemanaNasc'] = substr($html->find('input[id=ctl00_ContentPlaceHolder1_txtDataNascimento]',0)->value,13);
	
	$dados['idade'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtIdade]',0)->value;
	$dados['signo'] = $html->find('input[id=ctl00_ContentPlaceHolder1_lblSigno]',0)->value;
	
	$dados['nomeMae'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtNomeMae]',0)->value;
	
	$i=0;
	foreach( $html->find('div[class=Telefone-Main]') as $tel){
		$dados['telefones'][$i] = str_replace(" ","",$tel->innertext);
		$i++;
	}
	
	$i=0;
	foreach( $html->find('div[class=Endereco-Main]') as $end){
		$dados['enderecos'][$i] = str_replace('  ','',$end->innertext);
		$i++;
	}

	$i=0;
	foreach( $html->find('div[class=Email-Main]') as $em){
		$dados['email'][$i] = str_replace(' ','',$em->innertext);
		$i++;
	}
	return ($dados);
}

function extract_info_cnpj($content_curl){
	
	$html = str_get_html($content_curl);

	if($html->find('input[id=ctl00_ContentPlaceHolder1_txtCnpj]',0) == null )
			return null;

	$dados['CNPJ'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtCnpj]',0)->value;
	$dados['razaoSocial'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtRazao]',0)->value;
	$dados['nomeFantasia'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtNomeFantasia]',0)->value;

	
	$dados['dataAbertura'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtDataAbertura]',0)->value;
	
	$dados['CNAE'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtCnae]',0)->value;
	$dados['naturezaJuridica'] = $html->find('input[id=ctl00_ContentPlaceHolder1_txtNaturezaJuridica]',0)->value;
	
	
	$i=0;
	foreach( $html->find('div[class=Telefone-Main]') as $tel){
		$dados['telefones'][$i] = str_replace(" ","",$tel->innertext);
		$i++;
	}
	
	$i=0;
	foreach( $html->find('div[class=Endereco-Main]') as $end){
		$dados['enderecos'][$i] = str_replace('  ','',$end->innertext);
		$i++;
	}

	$i=0;
	foreach( $html->find('div[class=Email-Main]') as $em){
		$dados['email'][$i] = str_replace(' ','',$em->innertext);
		$i++;
	}
	return ($dados);
}



function searchInfo($user_param, $pass_param, $client_param, $cpfCnpj){
	
	$var_aux = checkCPF_CNPJ($cpfCnpj);
	
	if($var_aux == "ERROR")
	{
		return "ERROR : CHECK CNPJ OR CPF FOR MISSPELLING";
		//return null;
	}
	
	if(!checkLogin($user_param, $pass_param, $client_param))
	{
		return "ERROR : CHECK YOUR CONNECTION DATA!";
		//return null;
	}
	
	$content = get_contents_search($user_param, $pass_param, $client_param, $cpfCnpj);
	
	//echo $content;	
	if($var_aux == "CPF")
	{
		return extract_info_cpf($content);
	}
	else{
		return extract_info_cnpj($content);
	}
}


/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////
// 				TESTS					   //
/////////////////////////////////////////////
/////////////////////////////////////////////
/////////////////////////////////////////////

/*
function exemplos(){
	//Dados de Acesso
	$user = 'COBSERV';		
	$pass = 'yQ$46l7i';			
	$cliente = 'cobserv'; 
	
	//Dados
	$cpf_certo = '07505347616';
	$cnpj_certo = '18.025.940/0001-09';
	$cpf_errado = '07505347617';
	$cnpj_errado = '18025.940000108';
	
	echo "Pesquisa com CPF correto: <br />";
	var_dump(searchInfo($user, $pass, $cliente, $cpf_certo));
	
	echo "<br />Pesquisa com CPF incorreto: <br />";
	var_dump(searchInfo($user, $pass, $cliente, $cpf_errado));
	
	
	echo "<br />Pesquisa com CNPJ correto: <br />";
	var_dump(searchInfo($user, $pass, $cliente, $cnpj_certo));
	
	echo "<br />Pesquisa com CNPJ incorreto: <br />";
	var_dump(searchInfo($user, $pass, $cliente, $cnpj_errado));
 
}

exemplos();

*/

?>