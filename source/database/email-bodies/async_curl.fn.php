<?php

if (version_compare(PHP_VERSION, "5.5.0", "<")) {
	define("CURLE_OPERATION_TIMEDOUT", CURLE_OPERATION_TIMEOUTED);
}

function async_curl($background_process){
	if (empty($background_process)) return false;
	//-------------get curl contents----------------
	$ch = curl_init($background_process);
	curl_setopt_array($ch, array(
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_NOSIGNAL => 1,				//to timeout immediately if the value is < 1000 ms
		CURLOPT_TIMEOUT_MS => 100,			//The maximum number of mseconds to allow cURL functions to execute
													//if too small can cause 0 bytes response.
		CURLOPT_VERBOSE => 1,
		CURLOPT_HEADER => 1
	));
	$out = curl_exec($ch);
	if (false === $out){
		//start debug -----------
		//echo curl_error($ch);
		//end debug -------------
		//if the url didn't send anything yet, there's the timeout.
		//for the email delivery purpose this shouldn't counted as error.
		return (curl_errno() === CURLE_OPERATION_TIMEDOUT);
	}
	//start debug -----------
	//echo $out;
	//end debug -------------
	//-------------parse curl contents----------------
	//$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	//$header = substr($out, 0, $header_size);
	//$body = substr($out, $header_size);
	curl_close($ch);
	return true;
}

?>