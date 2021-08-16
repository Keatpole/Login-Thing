<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["id"]) || !isset($_POST["submit"])) {
    header("location: ../../.");
    exit();
}

$username = $_POST["username"];

$result = getTable($conn, "users", ["uid", $username]);
$result2 = getTable($conn, "users", ["id", $username]);

if ($result === null) {
    if ($result2 === null) {
        header("location: ../../search?error=usernotfound");
        exit();
    } else {
        $result = $result2;
    }
}
header("location: ../../user?u=" . $result["id"]);

mysqli_stmt_close($stmt);
