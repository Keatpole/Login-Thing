<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 2 || !isset($_GET["uid"]) || !$settings->enable_suggestions) {
    header("location: ../../moderation?suggestions");
    exit();
}

$sql = "DELETE FROM modsuggestions WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?suggestions&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $_GET["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$user = getTable($conn, "users", ["uid", $_GET["uid"]]);

$sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?suggestions&error=stmtfailed");
    exit();
}
$action = "RefuseSuggestion";
session_start();
mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $user["id"], $action, $_GET["type"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../moderation?suggestions&error=none");
exit();