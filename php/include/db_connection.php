<?php

$host = "localhost";
$port = 3306;
$username = "root";
$password = "";
$database = "web-programming";


$link = new mysqli($host, $username, $password, $database);

if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
?>
