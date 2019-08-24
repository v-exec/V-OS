<?php
//get artifact to load
if (isset($_GET['v'])) {
	if ($_GET['v']) {
		$v = strtolower($_GET['v']);
	} else $v = 'home';
} else {
	$_GET['v'] = 'home';
	$v = $_GET['v'];
}

include 'assets/private/parser.php';
include 'assets/private/artifact.php';
include 'assets/private/customartifact.php';
include 'assets/private/logcredentials.php';
include 'assets/private/loghelpers.php';

//name of directory for artifact declarations
$pageDirectory = 'pages';

//single parser for all artifacts
$parser = new Parser();

//array holding artifacts
$artifacts = array();

//creates and formats artifacts
createArtifacts();
formatArtifacts();

//load artifact
if (getArtifact($v) != null) {
	$artifact = getArtifact($v);
} else if(substr($v, 0, 4) === "tag-") {
	//check if looking at generated tag-artifact
	$tag = substr($v, 4, strlen($v));
	$tagCount = 0;

	for ($i = 0; $i < sizeof($artifacts); $i++) {
		if ($tagCount > 0) break;
		if ($artifacts[$i]->hasTag($tag)) {
			$tagCount++;
		}
	}

	if ($tagCount > 0) {
		//generate custom artifact
		$artifact = new CustomArtifact();
		$artifact->attributes['name'] =  "Tagged: " . $tag;
		$artifact->attributes['title'] = "Artifacts tagged with _[" . $tag . "].";
		$artifact->attributes['content'] = '-[' . $tag . ']';
		$artifact->path = ['home'];
		$parser->firstFormat($artifact);
		$parser->secondFormat($artifact);
	} else {
		//load 404
		$artifact = getArtifact('404');
	}
} else {
	//if artifact doesn't exist, load 404
	$artifact = getArtifact('404');
}

//get template
ob_start();
include 'assets/private/template.php';
$page = ob_get_contents();
ob_end_clean();
echo $page;
?>