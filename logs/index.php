<?php

$sel_date = isset($_REQUEST['ladata']) ? $_REQUEST['ladata'] : time();

$day  = date('d', $sel_date);
$month = date('m', $sel_date);
$year = date('Y', $sel_date);

$log = $year.'/'.$month.'/log'.$year.'-'.$month.'-'.$day.'.html';
if (@is_file("$log")==false) {
	$log ="error.html";
}

echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
  <meta content="text/html; charset=iso-8859-1" http-equiv="content-type" />
  <title>IRC channel log</title>
</head>

<frameset rows="75,*">
	<frame name="top" src="top.html" scrolling="no" noresize="noresize" frameborder="0" />
	<frameset cols="220,*">
		<frame name="left" src="left.php" scrolling="no" noresize="noresize" frameborder="0" />
		<frame name="log" src="'.$log.'" scrolling="auto" />
	</frameset>
	<noframes>
		<body>
			Your browser does not support frames
		</body>
	</noframes>
</frameset>
</html>
';
?>

