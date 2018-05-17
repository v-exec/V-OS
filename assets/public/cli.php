<?php

include '../private/parser.php';
include '../private/artifact.php';
include '../private/logcredentials.php';
include '../private/loghelpers.php';

$pageDirectory = '../../pages';

$parser = new Parser();

$artifacts = array();

createArtifacts();
formatArtifacts();

if ($_GET["a"] == "index") {
	global $artifacts;

	$firstArtifact = false;

	for ($i = 0; $i < sizeof($artifacts); $i++) {
		if ($artifacts[$i]->hasTag('nav')) {
			if (!$firstArtifact) {
				echo $artifacts[$i]->attributes['name'].' - '.$artifacts[$i]->attributes['title'];
				$firstArtifact = true;
			} else echo '<br><br>'.$artifacts[$i]->attributes['name'].' - '.$artifacts[$i]->attributes['title'];
		}
	}
}

if ($_GET["a"] == "travel") {
	global $artifacts;

	$test = strtolower($_GET["b"]);
	$result;

	for ($j = 20; $j > 0; $j--) {
		for ($i = 0; $i < sizeof($artifacts); $i++) {
			if (levenshtein($test, $artifacts[$i]->attributes['name'], 1, 2, 1) < $j) {
				$result = $artifacts[$i]->attributes['name'];
			}
		}
	}

	if ($result) echo $result;
	else echo 'fail';
}
?>