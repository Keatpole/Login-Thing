<?php

$serverName = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "isak_login_thing";

$conn = mysqli_connect($serverName, $dbUsername, $dbPassword, $dbName);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}