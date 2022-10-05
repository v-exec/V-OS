<?php
function connect() {
	global $servername;
	global $username;
	global $password;
	global $database;

	$conn = new mysqli($servername, $username, $password, $database);
	$conn->set_charset("utf8");

	return $conn;
}

//returns single number through request query ($q = query, $e = select result)
function getnum($q, $e) {
	$conn = connect();
	$r = "";
	$result = $conn->query($q);
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$r = $row[$e];
	}
	$conn->close();
	return $r;
}

//checks if current page has log data, and outputs it in sidebar
function getLogData() {
	global $artifact;
	$location = $artifact->attributes['name'];
	$location = addslashes($location);

	if ($artifact->hasTag('hub')) {
		$firstDate;
		$lastDate = getExtremeDate(null, null, null);

		$q = 'select log.date from log;';
			
		$conn = connect();
		$result = $conn->query($q);

		if ($result->num_rows > 0) {
			$rows = array();

			while ($row = $result->fetch_assoc()) {
				array_push($rows, $row['date']);
			}

			$date = new DateTime($rows[0]);
			$date = $date->format('Y.m.d');
			$firstDate = $date;
		}

		$conn->close();

		$hours = getAllHours(null, null);
		$logs = getAllLogs(null, null);
		$days = getAllDays(null, null);
		$divisionStats;

		$q = 'select division.name as division, sum(log.time) as hours from log left join project on project.id = log.project_id join task on task.id = log.task_id join division on division.id = log.division_id group by division order by hours desc;';

		$conn = connect();
		$result = $conn->query($q);

		if ($result->num_rows > 0) {
			$rows = array();

			//get query results
			while ($row = $result->fetch_assoc()) {
				if ($row['project'] != 'None' && $row['task'] != 'None' && $row['division'] != 'None') array_push($rows, [$row['division'], $row['hours']]);
			}
			$divisionStats = $rows;
		}
		$conn->close();

	} else if ($artifact->hasTag('nav')) {
		$firstDate = getExtremeDate($location, 'division', 0);

		if ($firstDate == null)
			return null;

		$lastDate = getExtremeDate($location, 'division', 1);
		$hours = number_format(str_replace(',', '', getAllHours($location, 'division')), null, null, '');
		$logs = str_replace(',', '', getAllLogs($location, 'division'));
		$days = str_replace(',', '', getAllDays($location, 'division'));
		$divisionStats = str_replace(',', '', getDivisionRatio(null, null, $location, 'division'));

	} else if ($artifact->hasTag('project')) {
		$firstDate = getExtremeDate($location, 'project', 0);

		if ($firstDate == null)
			return null;

		$lastDate = getExtremeDate($location, 'project', 1);
		$hours = number_format(str_replace(',', '', getAllHours($location, 'project')), null, null, '');
		$logs = str_replace(',', '', getAllLogs($location, 'project'));
		$days = str_replace(',', '', getAllDays($location, 'project'));
		$divisionStats = str_replace(',', '', getDivisionRatio(null, null, $location, 'project'));

	} else {
		return null;
	}

	$hourDayAverage = number_format(str_replace(',', '', $hours) / str_replace(',', '', $days), 1);

	$data = $data . '<span class="log-text">'.$firstDate.' · '.$lastDate.'</span>';

	for ($i = 0; $i < sizeof($divisionStats); $i++) {
		switch ($divisionStats[$i][0]) {
			case 'Code':
				$data = $data . '<span class="log-stat">COD</span>';
				break;

			case 'Visual':
				$data = $data . '<span class="log-stat">VIS</span>';
				break;

			case 'Abstract':
				$data = $data . '<span class="log-stat">ABS</span>';
				break;

			case 'Audio':
				$data = $data . '<span class="log-stat">AUD</span>';
				break;
		}
		//max-width: calc(100% - 30px);
		$percentage = number_format($divisionStats[$i][1] / $hours * 100, 1);
		$data = $data . '<div class="log-bar" style="width: calc('.$percentage.'% - '. (30 * $percentage / 100) .'px);"></div>';
		if ($i != (sizeof($divisionStats) - 1)) $data = $data . '<br>';
	}

	$data = $data . '<span class="log-text">'.$hours.' '.pluralize('hour', $hours).' · '.$logs.' '.pluralize('log', $logs).'</span>';
	$data = $data . '<span class="log-text">'.$days.' '.pluralize('day', $days).' · '.$hourDayAverage.' '.pluralize('hour', $hourDayAverage).' / day</span>';

	return $data;
}

//gets most recent log entries
function getRecentActivities($logLimit) {
	$q = 'select log.date, log.time, project.name as project, task.name as task, division.name as division, log.details from log left join project on project.id = log.project_id join task on task.id = log.task_id join division on division.id = log.division_id order by log.id asc;';

	$conn = connect();
	$result = $conn->query($q);

	if ($result->num_rows > 0) {
		$rows = array();

		while ($row = $result->fetch_assoc()) {
			if ($row['project'] != 'None' && $row['task'] != 'None' && $row['division'] != 'None') array_push($rows, [$row['date'], $row['time'], $row['project'], $row['task'], $row['details']]);
		}

		$displayLogCount = $logLimit;
		$size = 0;

		if (sizeof($rows) > $displayLogCount) $size = $displayLogCount;
		else $size = sizeof($rows);

		$string;

		for ($i = 0; $i < $size; $i++) {
			$date = new DateTime($rows[$i][0]);
			if ($i > 0) $string = $string . '<br><br>';
			if ($rows[$i][4] !== '') $string = $string . $date->format('m.d').' · '.number_format($rows[$i][1], 1, '.', '').'h · '.$rows[$i][2].' · '.$rows[$i][3].' · '.$rows[$i][4];
			else $string = $string . $date->format('m.d').' · '.number_format($rows[$i][1], 1, '.', '').'h · '.$rows[$i][2].' · '.$rows[$i][3];
		}
	}

	$conn->close();

	return $string;
}

function checkType($l) {
	$conn = connect();

	$result = $conn->query('select * from task where name = '."'".$l."'".';');
	if ($result->num_rows > 0) {
		$conn->close();
		return 'task';
	}

	$result = $conn->query('select * from project where name = '."'".$l."'".';');
	if ($result->num_rows > 0) {
		$conn->close();
		return 'project';
	}

	$result = $conn->query('select * from division where name = '."'".$l."'".';');
	if ($result->num_rows > 0) {
		$conn->close();
		return 'division';
	}

	$conn->close();
	return null;
}

function getDivisionRatio($contextPage, $contextType, $topic, $topicType) {
	if (!$contextPage) {
		$q = 'select '.$topicType.'.name as title, division.name as division, sum(log.time) as hours from log left join project on project.id = log.project_id join task on task.id = log.task_id join division on division.id = log.division_id where '.$topicType.'.name = '."'".$topic."'".' group by division order by hours desc;';
	} else {
		$q = 'select '.$topicType.'.name as title, division.name as division, sum(log.time) as hours from log left join project on project.id = log.project_id join task on task.id = log.task_id join division on division.id = log.division_id where '.$topicType.'.name = '."'".$topic."'".' and '.$contextType.'.name = '."'".$contextPage."'".' group by division order by hours desc;';
	}

	$conn = connect();
	$result = $conn->query($q);

	if ($result->num_rows > 0) {
		$rows = array();

		//get query results
		while ($row = $result->fetch_assoc()) {
			array_push($rows, [$row['division'], $row['hours']]);
		}
		return $rows;
	}
	$conn->close();
}

function pluralize($s, $n) {
	if(fmod($n, 1) !== 0.00){
		return $s.'s';
	} else {
		if ($n == 1) return $s;
		else return $s.'s';
	}
}

function getAllHours($l, $type) {
	if (!$l) {
		$q = 'select sum(time) as num_hours from log;';
		return number_format(getNum($q, 'num_hours'), 0, '.', '');
	} else {
		$q = 'select sum(log.time) as num_hours from log left join '.$type.' on '.$type.'.id = log.'.$type.'_id where '.$type.'.name = '."'".$l."'".';';
		return number_format(getnum($q, 'num_hours'), 1);
	}
}

function getAllLogs($l, $type) {
	if (!$l) {
		$q = 'select count(*) as num_logs from log;';
		return getNum($q, 'num_logs');
	} else {
		$q = 'select count(*) as num_logs from log left join '.$type.' on '.$type.'.id = log.'.$type.'_id where '.$type.'.name = '."'".$l."'".';';
		return getnum($q, 'num_logs');
	}
}

function getAllDays($l, $type) {
	if (!$l) {
		$q = 'select count(distinct(date)) as num_days from log;';
		return getNum($q, 'num_days');
	} else {
		$q = 'select count(distinct(date)) as num_days from log left join '.$type.' on '.$type.'.id = log.'.$type.'_id where '.$type.'.name = '."'".$l."'".';';
		return getnum($q, 'num_days');
	}
}

function getExtremeDate($l, $type, $end) {
	if (!$l) $q = 'select log.date from log';
	else $q = 'select log.date from log left join '.$type.' on '.$type.'.id = log.'.$type.'_id where '.$type.'.name = '."'".$l."'".';';
		
	$conn = connect();
	$result = $conn->query($q);

	if ($result->num_rows > 0) {
		$rows = array();

		while ($row = $result->fetch_assoc()) {
			array_push($rows, $row['date']);
		}

		if ($end == 0) $date = new DateTime($rows[sizeof($rows)-1]);
		else $date = $lastDate = new DateTime($rows[0]);
		$date = $date->format('Y.m.d');
	}

	$conn->close();

	if ($date == null) return null;
	else return $date;
}
?>