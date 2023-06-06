<?php

if(isset($_POST['cookie_data'])) {

	$_POST['cookie_data'] = json_decode($_POST['cookie_data'], 1);
	if(!is_array($_POST['cookie_data']))
		die(json_encode(array('error'=> 'cookie inválido')));

	header('Content-Type: application/json; charset=utf-8');

	$cookieFile = 'cookie/BoaVistaNodeGenerated.txt';
	$auxString = '{{domain}}	FALSE	/	FALSE	0	PHPSESSID	{{cookie}}';

	if(isset($_POST['catta'])) {
		$auxString = "#HttpOnly_{{domain}}	FALSE	/	TRUE	0	JSESSIONID	{{cookie}}";
		$cookieFile = 'cookie/CattaNodeGenerated' . intval($_POST['catta']) . '.txt';
	}

	$data = date('d/m/Y H:i');
	$file_data = "# Netscape HTTP Cookie File
# https://curl.haxx.se/docs/http-cookies.html
# This file was generated at $data
\n";

	foreach($_POST['cookie_data'] as $cd) {
		$file_data .= str_replace('{{cookie}}', $cd['value'], str_replace('{{domain}}', $cd['domain'], $auxString)) . "\n";
		//$file_data .= "{$cd['domain']}	FALSE	/	FALSE	0	PHPSESSID	{$cd['value']}\n";
	}

	$res = file_put_contents($cookieFile, $file_data);
	if($res)
		die(json_encode(array('error' => false, 'msg'=>'cookie file generated')));
	else
		die(json_encode(array('error' => true, 'msg'=>'cookie file NOT generated')));

}

die(json_encode(array('error' => true, 'msg'=>'cookie file NOT generated - invalid post data')));

?>