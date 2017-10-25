<?php
$host="horton.elephantsql.com";
$user="uqyeqrkf";
$port = "5432";
    $db="uqyeqrkf";
    $pw = "fkWB4OYI2KF-SN2XoV5GI8WsrlLIN5rB";

    $conn_str = "host=$host dbname=$db user=$user password=$pw port=$port";
    $conn = pg_connect($conn_str) or die("Error, cannot connect to DB: ". pg_last_error());
    $query = 'SELECT * FROM Users';
    $result = pg_query($conn, $query) or die("Query failed: " . pg_last_error());
    var_dump($result);
?>