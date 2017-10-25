<?php
session_start();
include "config.php";
if(!isset($_SESSION['auth']) || $_SESSION['auth'] == 0) {
  header('Location: '.$home.'login.php');
  die();
}
else {
header('Location: '.$home.'timeline.php');
die();
}
?>
<html>
	<head>
	</head>
	<body>
		lalalalalala
	</body>
</html>
