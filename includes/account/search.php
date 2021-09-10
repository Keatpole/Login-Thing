<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["id"]) || !isset($_POST["submit"])) {
    header("location: ../../.");
    exit();
}

$username = $_POST["username"];

$result = getTable($conn, "users", ["id", $username]);
$result = ($result !== null ? $result : getTable($conn, "users", ["uid", $username]));

if ($result === null) {
    header("location: ../../search?error=usernotfound");
    exit();
}
header("location: ../../user?u=" . $result["id"]);

mysqli_stmt_close($stmt);
