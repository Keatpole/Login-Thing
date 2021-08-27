<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["uid"]) || !isset($_POST["submit"])) {
    header("location: ../../.");
    exit();
}

if (!$settings->enable_friends) {
    header("location: ../../user?u=" . $_POST["user"]);
    exit();
}

$sql = "INSERT INTO friendreq(user1, user2) VALUES (?, ?);";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../users?u=" . $_POST["user"] . "&error=stmtfailed");
    exit();
}

mysqli_stmt_bind_param($stmt, "ss", $_SESSION["id"], $_POST["user"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../user?u=" . $_POST["user"] . "&error=none");