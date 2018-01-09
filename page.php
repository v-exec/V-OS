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
include 'assets/private/logcredentials.php';
include 'assets/private/loghelpers.php';
include 'assets/private/static.php';

//makes header information white (used when background image is dark)
function checkWhite() {
	global $artifact;

	if ($artifact->attributes['white'] == 'true'){
		echo
		'<style>
		.header-title, .header-link {
			color: #fff;
		}
		.long-divider {
			background-color: #fff;
		}
		</style>

		<script>
		V.colorR = 255;
		V.colorG = 255;
		V.colorB = 255;
		</script>';
	}
}

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
//if artifact doesn't exist, load 404
else $artifact = getArtifact('404');

//get template
ob_start();
include 'assets/private/template.php';
$page = ob_get_contents();
ob_end_clean();

//create files for static site?
$makeStatic = false;
if ($makeStatic == true) exportStatic();
else echo $page;
?>