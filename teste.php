<?php

$urlPubName = 'https://pub.orcid.org/v2.0/0000-0002-7353-1799/works';

	$ch = curl_init($urlPubName);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: application/json'));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($ch);
    curl_close($ch);

    $jsonInput = json_decode($output, true);
    $pubIDs = [];
    foreach ($jsonInput['group'] as $nivel1) {
                foreach ($nivel1['work-summary'] as $nivel2) {

                    // $pubID = $nivel2['put-code'];

                    echo array_push($pubIDs, $nivel2['put-code']);
                }
            }
?>