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

if ($_GET["a"] == "logs") {
	$location = $_GET["l"];
	$artifact;

	if (getArtifact($location) != null) $artifact = getArtifact($location);
	else {
		echo "The artifact ".$location." does not exist.";
		return;
	}

	if ($artifact->hasTag('hub')) {
		$hours = getAllHours(null, null);
		$logs = getAllLogs(null, null);

		echo 'This is a hub page. It contains all logs, amounting to '.$hours.' '.pluralize('hour', $hours).' and '.$logs.' '.pluralize('log', $logs).'.<br><br>Using the <i>logs</i> command on project and division pages will show more detailed information about the current page.<br><br>Full log data can be found in the <a href="https://log.v-os.ca"/>Log</a>.';
	} else {

		if ($artifact->hasTag('nav')) {
			$firstDate = getExtremeDate($location, 'division', 0);

			if ($firstDate == null) {

				echo 'There are no logs for this page.';
				return;
			}

			$lastDate = getExtremeDate($location, 'division', 1);
			$hours = getAllHours($location, 'division');
			$logs = getAllLogs($location, 'division');
			$days = getAllDays($location, 'division');
			$divisionStats = getDivisionRatio(null, null, $location, 'division');

		} else if ($artifact->hasTag('project')) {
			$firstDate = getExtremeDate($location, 'project', 0);

			if ($firstDate == null) {
				echo 'There are no logs for this page.';
				return;
			}

			$lastDate = getExtremeDate($location, 'project', 1);
			$hours = getAllHours($location, 'project');
			$logs = getAllLogs($location, 'project');
			$days = getAllDays($location, 'project');
			$divisionStats = getDivisionRatio(null, null, $location, 'project');
		} else {
			echo 'There are no logs for this page.';
			return;
		}

		$hourDayAverage = number_format($hours / $days, 1);

		$divisionStatsString;

		for ($i = 0; $i < sizeof($divisionStats); $i++) {
			if ($i != 0) $divisionStatsString = $divisionStatsString . ' · ';
			$divisionStatsString = $divisionStatsString . $divisionStats[$i][0] . ': ' . number_format($divisionStats[$i][1] / $hours * 100, 1) . '%';
		}

		echo
			ucfirst($location).' · '.$firstDate.' · '.$lastDate.'<br><br>'
			.$hours.' '.pluralize('hour', $hours).' · '.$logs.' '.pluralize('log', $logs).'<br><br>'
			.$days.' '.pluralize('day', $days).' · '.$hourDayAverage.' '.pluralize('hour', $hourDayAverage).' / day<br><br>'
			.$divisionStatsString.'<br><br>'
			.'Full log entry found in the <a href="https://log.v-os.ca/'.$location.'">Log</a>.';
	}

} else if ($_GET["a"] == "explore") {
	$location = $_GET["l"];
	$artifact;

	if (getArtifact($location) != null) $artifact = getArtifact($location);
	else {
		echo "The artifact ".$location." does not exist.";
		return;
	}

	if ($artifact->attributes['details']) echo $artifact->attributes['details'];
	else echo 'I unfortunately have no additional information about this page.';
} else if ($_GET["a"] == "index") {
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
?>