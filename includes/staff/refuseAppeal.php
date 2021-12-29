<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["rank"]) || $_SESSION["rank"] < 2 || !isset($_GET["id"]) || !$settings->enable_report) {
    header("location: ../../moderation?appeals");
    exit();
}

$sql = "DELETE FROM appeals WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?appeals&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $_GET["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?appeals&error=stmtfailed");
    exit();
}
$action = "RefuseAppeal";
session_start();
mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $_GET["target"], $action, $_GET["reason"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../moderation?appeals&error=none");
exit();