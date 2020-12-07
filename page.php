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
if (getArtifact($v) != null) $artifact = getArtifact($v);
else if(substr($v, 0, 4) === "tag-") {
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
		//if attempt to create tag artifact results in no found entires, 404
		redirect($v);
	}
} else if (substr($v, 0, 4) === "404-") {
	//if slashes remain, sanitize and redirect
	if (strstr($v, '/')) {
		redirect(substr($v, 4, $v.length));
	}

	//create 404
	$name = substr($v, 4, $v.length);
	$artifact = new CustomArtifact();
	$artifact->attributes['name'] =  "404 - " . $name;
	$artifact->attributes['image'] = "404>1";
	$artifact->attributes['image name'] = "#[404]";
	$artifact->attributes['white'] = "true";
	$artifact->attributes['title'] = "Artifact _[" . $name . "] not found.";
	$artifact->attributes['content'] = "
	Seems like #[LOGO] hasn't indexed _[" . $name . "] yet, sorry about that.
	<br><br>
	If you're lost, take a look at the #[index], or check out some of the #[projects].
	<br><br>
	If you think this page should exist, please contact me through my @[email>victor.ivanov.design@gmail.com].
	";
	$artifact->path = ['home'];
	$parser->firstFormat($artifact);
	$parser->secondFormat($artifact);
} else {
	//if artifact doesn't exist, load 404
	redirect($v);
}

//get template
ob_start();
include 'assets/private/template.php';
$page = ob_get_contents();
ob_end_clean();
echo $page;

function redirect($search) {
	$search = sanitize($search);
	header('Location: https://v-os.ca/404-' . $search);
	die();
}

function sanitize($string) {
	$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	$string = htmlspecialchars($string, ENT_QUOTES);
	return $string;
}
?>