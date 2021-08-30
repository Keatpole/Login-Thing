<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["uid"]) || !isset($_POST["submit"])) {
    header("location: ../../.");
    exit();
}

if ($_SESSION["id"] != getTable($conn, "friendreq", ["id", $_POST["id"]])["user2"]) {
    header("location: ../../friends?req&error=authfailed");
    exit();
}

$sql = "INSERT INTO friends(user1, user2) VALUES (?, ?);";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../users?u=" . $_POST["user"] . "&error=stmtfailed");
    exit();
}

mysqli_stmt_bind_param($stmt, "ss", $_POST["user"], $_SESSION["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "DELETE FROM friendreq WHERE id=?;";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../friends?req&error=stmtfailed");
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $_POST["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($_POST["return"]) {
    header("location: ../../" . $_POST["return"] . "error=none");
} else {
    header("location: ../../friends?req&error=none");
}