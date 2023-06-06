<?php

if(!function_exists('curl_get_contents')) {
    function curl_get_contents($url, $timeout = 10) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, intval($timeout*0.5));
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // <-- don't forget this
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // <-- and this
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        if(curl_errno($ch))
            echo curl_error($ch);
        curl_close($ch);
        return $data;
    }
}
