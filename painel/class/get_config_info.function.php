<?php

/*function get_config_info(){

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"https://servidor.probusca.com:15000/config");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_PORT, 15000);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "token=5ZERAhzpwYIh");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $result = curl_exec($ch);

    if (isset($_GET['debug2'])) {
	    printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
	           htmlspecialchars(curl_error($ch)));

	    rewind($verbose);
		$verboseLog = stream_get_contents($verbose);

		echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
	}

	$server_output = curl_exec($ch);

	return json_decode($server_output, 1);

}

*/

function get_config_info() {

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL,"https://servidor.probusca.com/configuration");
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, '54.94.171.46:k5wzDMqFSR4udgT4QZWBNqYETKr2FRnfF8PR1FKdVK' );

    $result = curl_exec($ch);

    /*if (isset($_GET['debug2'])) {
	    printf("cUrl error (#%d): %s<br>\n", curl_errno($ch), htmlspecialchars(curl_error($ch)));

	    rewind($verbose);
		$verboseLog = stream_get_contents($verbose);

		echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
	}*/

	$server_output = curl_exec($ch);

	return json_decode($server_output, 1);

}

if(isset($_GET['debug2']))
	var_dump(get_config_info());
?>