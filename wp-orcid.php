<?php
$orcidID = "0000-0002-7353-1799";
$pubLimit = "10";
//$orcidID = "0000-0002-1566-7006";
//collectXML('0000-0002-0359-1147', 'email');

//fetch($orcidID, "nome", $pubLimit);
//fetch($orcidID, "bio", $pubLimit);
fetch($orcidID, "publica", $pubLimit);


function fetch($orcidID, $elementID, $pubLimit) {
	$url = 'https://pub.orcid.org/v2.0/' . $orcidID;

		// Escolha de elementos
	if ($elementID == "nome") {
		$sliceProfile = 1;
		$caminho ="/record:record/person:person/person:name";
	} elseif ($elementID == "bio") {
		$sliceProfile = 1;
		$caminho = "/record:record/person:person/person:biography/personal-details:content";
	} elseif ($elementID == "publica") {
		$url = "https://pub.orcid.org/v2.0/" . $orcidID . "/works";
		$sliceProfile = 2;
		$caminho = "//activities:works/activities:group/work:work-summary";
		//$caminho = "works.xml";
	}

	$curlXML = curl_init("$url");
	curl_setopt($curlXML, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curlXML, CURLOPT_HEADER, false);
	curl_setopt($curlXML, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curlXML, CURLOPT_RETURNTRANSFER, TRUE);
	$output = curl_exec($curlXML);
	curl_close($curlXML);

	$profile = new SimpleXMLElement($output);

	echo "<meta charset='UTF-8'>";
	if ($sliceProfile == 1) {
		if ($elementID == "bio") {
			$slice = $profile->xpath($caminho);
			$resultado = implode("|", $slice);
			echo "<h2>Biografia</h2><p>" . $resultado . "</p>";
		} else {
			$slice = $profile->xpath($caminho . "/personal-details:credit-name");

			if (empty($slice)) {
				$sliceFirst = $profile->xpath($caminho . "/personal-details:given-names");
				$sliceSecond = $profile->xpath($caminho . "/personal-details:family-name");
				$primeiroNome = implode("|", $sliceFirst);
				$segundoNome = implode("|", $sliceSecond);
				echo "<h1>Perfil de " . $primeiroNome . " " . $segundoNome . "</h1>";
			} else {
				$resultado = implode("|", $slice);
				echo "<h1>Perfil de " . $resultado . "</h1>";
			}
		}
	} else {
		echo "<h2>Publicações</h2>";
		$i = 0;
		foreach ($profile->xpath($caminho) as $journalID) {
			if (++$i == $pubLimit + 1) break;
			$pubID = $journalID["put-code"];

			// Array de elementos a mostrar para cada publicação
			$publicaElementos = [
				"Nome" => "./work:title/common:title",
				"Data" => "./common:publication-date/common:year",
				"Tipo" => "./work:type",
			];

			// Preencher as variáveis com os elementos
			foreach ($publicaElementos as $chave => $outCaminho) {
				foreach ($journalID->xpath($outCaminho) as ${"pub" . $chave}) {
				}
			}
			
			// Chamar a função para obter nome do local de publicação e URL
			foreach ($journalID->xpath("./work:title/common:title") as $callJournalName) {
				$dadosJournal = fetchJournalName_URL($orcidID, $pubID);
			}

			// Output
			echo "<div class='publica'><strong>" . $pubNome . "</strong>";
			echo "<p>" . $dadosJournal["nome"] . "</p>";
			echo "<p><strong>Data de Publicação: </strong>" . $pubData . "</p>";
			echo "<p><strong>Tipo de Publicação: </strong>" . $pubTipo . "</p>";
			echo "<p><strong>URL: </strong><a href='" . $dadosJournal["url"] . "'>" . $dadosJournal["url"] . "</a><hr /></div>";
		}
	}
}


function fetchJournalName_URL(&$orcidID, &$pubID) {
	$urlPubName = "https://pub.orcid.org/v2.0/" . $orcidID . "/works/" . $pubID;
	$caminho = "//bulk:bulk/work:work";

	$curlXML = curl_init("$urlPubName");
	curl_setopt($curlXML, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curlXML, CURLOPT_HEADER, false);
    curl_setopt($curlXML, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curlXML, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($curlXML);
    curl_close($curlXML);

    $journalProfile = new SimpleXMLElement($output);

	foreach ($journalProfile->xpath($caminho . "/work:journal-title") as $journalFetchNome) {
	}
	foreach ($journalProfile->xpath($caminho . "/work:url") as $journalFetchURL) {
	}
	$out["nome"] = $journalFetchNome;
	$out["url"] = $journalFetchURL;
	return $out;
}