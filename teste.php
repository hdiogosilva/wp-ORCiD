<?php

$url = "https://pub.orcid.org/v2.0/0000-0002-7353-1799";

$teste = file_get_contents($url);
echo $teste;
?>