<?php

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Calendar</title>
		<link href="style.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="script.js"></script>
	</head>
	<body>
';

setlocale(LC_ALL, 'en_EN'); // Locale

$sel_date = isset($_REQUEST['ladata']) ? $_REQUEST['ladata'] : time();

$days = date('t', $sel_date); // Number of days of the month
$today  = date('d', $sel_date); 
$month = date('m', $sel_date); // Current month
$year = date('Y', $sel_date); // Current year

$monthName[0] = "January";
$monthName[1] = "February";
$monthName[2] = "March";
$monthName[3] = "April";
$monthName[4] = "May";
$monthName[5] = "June";
$monthName[6] = "July";
$monthName[7] = "August";
$monthName[8] = "September";
$monthName[9] = "October";
$monthName[10] = "November";
$monthName[11] = "December";

$t = getdate($sel_date);

$nextMonth = mktime(0, 0, 0, $month + 1, 1, $year); // Next month
$previousMonth = mktime(0, 0, 0, $month - 1, 1, $year); // Previous month

// Weekday for day 1 of current month
if (($dayset = date('w', mktime(0, 0, 0, $month, 1, $year))) == 0) {
	$dayset = 7;
	$start = false;
	$sum = 0;
}

echo '<div align="center"><p><b>Pick a date:</b></p></div>
<table cellpadding="0" cellspacing="0" id="calendar" align="center">
	<thead>
		<tr>
			<td><a href="?ladata='.$previousMonth.'" title="Previous month"><img src="img/arrow-back.gif" alt="&lt;" /></a></td><td colspan="5"><b> '.$monthName[$month - 1].' '.$year.'</b> </td><td><a href="?ladata='.$nextMonth.'" title="Next month"><img src="img/arrow-forward.gif" alt="&gt;" /></a></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th><th>Su</th>
		</tr><tr>';
$day = 1;
for ($i = 1; $day <= $days ; $i++) {
	// On days belonging to another month, don't show anything
    if (!$start) {
		if ($i <= 7 && $i == $dayset) { 
			$start = true; 
			$sum = $i;
			
		} else {
			echo '<td class="inactive"></td>
			';
		}
	}
	if ($start) {
			if (($i - 1) % 7 == 0) {
			echo '</tr><tr>';
			}
			if ( $day < 10) {
				$log = $year.'/'.$month.'/log'.$year.'-'.$month.'-0'.$day.'.html';
			} else {
				$log = $year.'/'.$month.'/log'.$year.'-'.$month.'-'.$day.'.html';
			}
			if (@is_file("$log")==false) {
				echo '
					<td class="inactive">
					<p><font color="#FFFF00">' .$day. '</font></p>
					</td>';
			} else {
				echo '
					<td class="event">
						<p><a href="'.$log.'" target="log">' .$day. '</a></p>
					</td>';
			}
			$day++;
    }
}
echo '
</tr></tbody></table>
</body>
</html>
';
?>
