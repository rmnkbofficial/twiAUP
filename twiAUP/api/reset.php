<?php

include "../config.php";
include "../functions.php";

if(isset($_GET['secret']) && $_GET['secret'] == "15415Reset") {
	$dbh = db_connect($host, $port, $db, $user, $password);
	$result = reset_database($dbh);
	close_db_connection($dbh);
	echo json_encode($result);
} else {
	echo json_encode(array("status" => 0));
}


?>
