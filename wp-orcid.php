#!/usr/bin/php
<?php
$cliend_id = 'APP-SW3689XNCE83LCEH';
$client_secret = '5dc14d0a-a7a4-433b-93a3-102484bcec99';
$grant_type = 'client_credentials';
$code = '/read-public';
$mainurl = 'https://orcid.org';

function getAuthorization($client_id, $client_secret, $grant_type, $code, $mainurl) {
	$uri = $mainurl . '/oauth/token';	
	
	$fields = array(
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => $grant_type,
		'code' => $code
	);
		foreach($fields as $key=>$value) { 
			$fields_string .= $key . '=' . $value . '&'; 
		}
	
	rtrim($fields_string, '&');				
					
	$curl_handle=curl_init();
	curl_setopt($curl_handle, CURLOPT_HEADER, false);
	curl_setopt($curl_handle, CURLINFO_HEADER_OUT, false); // enable tracking
	curl_setopt($curl_handle, CURLOPT_URL, $uri);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl_handle, CURLOPT_USERAGENT, 'TAMU_Library');
	curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array("Accept: application/json" )); 
	curl_setopt($curl_handle,CURLOPT_POST, count($fields));
	curl_setopt($curl_handle,CURLOPT_POSTFIELDS, $fields_string);
	$content = curl_exec($curl_handle);
	$http_status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
	curl_close($curl_handle);
		if (DEBUG_OUT) {
			echo 'Response:<br><pre>' .  htmlspecialchars($content) . '</pre></br>'; 	
		}
		
		if ($http_status == 200) {
			$obj = json_decode($content);
			return array($obj->{'access_token'}, $obj->{'orcid'});	
		} else { 
			return array(500);	
		}
	return; 
			
}

?>
