<?php
// $orcidID = '0000-0002-7353-1799';
// $pubLimit = '5';

// fetch($orcidID, 'nome', $pubLimit);
// fetch($orcidID, 'bio', $pubLimit);
// fetch($orcidID, 'publica', $pubLimit);


function fetch($orcidID, $elementID, $pubLimit) {
		// Escolha de elementos
	if ($elementID == 'publica') {
		$url = 'https://pub.orcid.org/v2.0/' . $orcidID . '/works';
		$sliceProfile = 2;
	} else {
		$url = 'https://pub.orcid.org/v2.0/' . $orcidID . '/person';
		$sliceProfile = 1;
	}

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: application/json'));
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$output = curl_exec($ch);
	curl_close($ch);

	$jsonInput = json_decode($output, true);

	echo '<meta charset="UTF-8">';
	if ($sliceProfile == 1) {

		// Nome
		if ($elementID == 'nome'){
			if (!is_null($jsonInput['name']['credit-name'])) {
				echo '<h1>' . $jsonInput['name']['credit-name']['value'] . '</h1>';
			} else {
				$nome1 = $jsonInput['name']['given-names']['value'];
				$nome2 = $jsonInput['name']['family-name']['value'];
				echo '<h1>' . $nome1 . ' ' . $nome2 . '</h1>';
			}
		}

		// Biografia
		if ($elementID == 'bio'){
			echo '<h2>Biografia</h2>';
			echo $jsonInput['biography']['content'];
		}

	} else {
		echo '<h2>Publicações</h2>';
		$pubIDs = [];
		$i = 0;
			foreach ($jsonInput['group'] as $nivel1) {
				foreach ($nivel1['work-summary'] as $nivel2) {
					if($i==$pubLimit) break;
					array_push($pubIDs, $nivel2['put-code']);
					$i++;
				}
			}
				// Chamar a função para obter Publicações
				$dadosJournal = fetchJournalName_URL($orcidID, $pubIDs, $pubLimit);
	}
}


function fetchJournalName_URL(&$orcidID, &$pubIDs, &$pubLimit) {

		$i = 0;
		$varNum = 1;
		foreach ($pubIDs as $pubID) {
			$i++;
			if ($i == 1) {
				${'array' . $varNum} = [];
			} elseif ($i == 50) {
				$varNum++;
				${'array' . $varNum} = [];
				$i = 0;
			}
			array_push(${'array' . $varNum}, $pubID);
		}

	for ($i=1; $i <= $varNum ; $i++) { 
	$str = implode (",", ${'array' . $i});
	$urlPubName = 'https://pub.orcid.org/v2.0/' . $orcidID . '/works/' . $str;

	$ch = curl_init($urlPubName);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: application/json'));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($ch);
    curl_close($ch);

    $jsonInput = json_decode($output, true);

    foreach ($jsonInput['bulk'] as $nivel1) {
	 foreach ($nivel1['work']['title']['title'] as $tituloPub) {
	 	$journalFetchTitulo = $tituloPub;
	 	echo '<div><strong>' . $journalFetchTitulo . '</strong>';
	}
	
	if (!is_null($nivel1['work']['journal-title'])) { 
	foreach ($nivel1['work']['journal-title'] as $tituloJour) {
		$journalFetchNome = $tituloJour;
		echo '<p>' . $journalFetchNome . '</p>';
	}
	} else {
	}

	if (!is_null($nivel1['work']['publication-date'])) {
		if (!is_null($nivel1['work']['publication-date']['year'])) {
			foreach ($nivel1['work']['publication-date']['year'] as $pubData) {
				$journalFetchData = $pubData;
				echo '<p><strong>Data de Publicação: </strong>' . $journalFetchData . '</p>';
			}
		} else {
		}
	} else {
	}

	$journalFetchTipo = $nivel1['work']['type'];
	$journalTipoUnder = str_replace('_', ' ', $journalFetchTipo);
	$journalTipoCapital = ucwords(strtolower($journalTipoUnder));
	echo '<p><strong>Tipo de Publicação: </strong>' . $journalTipoCapital . '</p>';


	if (!is_null($nivel1['work']['url'])) {
	foreach ($nivel1['work']['url'] as $pubURL) {
		$journalFetchURL = $pubURL;
		echo '<p><strong>URL: </strong><a href="' . $journalFetchURL . '">' . $journalFetchURL . '</a><hr /></div>';
	}
	} else {
		echo '<hr /></div>';
	}
	}
	}
}