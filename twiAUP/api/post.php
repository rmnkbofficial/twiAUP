<?php

session_start();
include "../config.php";
include "../functions.php";
include "../private_functions.php";

error_log("flit is:".$_GET['flit'].", coorx is:".$_GET['coorx'].", coory is:".$_GET['coory']);
if(isset($_SESSION['auth']) && $_SESSION['auth'] == 1 && isset($_GET['flit']) && isset($_GET['coorx']) && isset($_GET['coory'])) {
	$dbh = db_connect($MY_HOST, $MY_DB_PORT, $MY_DB, $DB_USER, $DB_PW);
	$res = post_post($dbh, $_GET['title'], $_GET['flit'], $_GET['coorx'], $_GET['coory'], $_SESSION['user']);
	close_db_connection($dbh);
	echo json_encode($res);
} else {
	echo json_encode(array("status" => -1));
}

?>
