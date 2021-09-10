<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 2 || !isset($_GET["target"]) || !$settings->enable_report) {
    header("location: ../../moderation?reports");
    exit();
}

$target = $_GET["target"];
$action = $_GET["action"];
$reason = $_GET["reason"];
$id = $_GET["id"];

$user = getTable($conn, "users", ["id", $target]);

if ($action != "-1") {
    header("location: ../../moderation?reports&error=authfailed");
    exit();
}

if ($user["rank"] > 1 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?reports&error=targetisimmune");
    exit();
}

if ($action == "-1") {

    $banned = false;

    foreach (getTable($conn, "bans", "", true) as $v) {
        if ($v["target"] == $user["id"]) $banned = true;
    }

    if ($banned) {
        header("location: ../../moderation?reports&error=useralreadybanned");
        exit();
    }

    $sql = "INSERT INTO bans (banner, target) VALUES (?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ss", $_SESSION["id"], $user["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "UPDATE users SET rank = ? WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?suggestions&error=stmtfailed");
        exit();
    }
    $zero = "0";
    mysqli_stmt_bind_param($stmt, "ss", $zero, $user["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM modsuggestions WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?reports&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?reports&error=stmtfailed");
        exit();
    }
    $action = "ApproveReport";
    $type = "(Un)Ban";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $target, $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?reports&error=none");
    exit();
}

$sql = "UPDATE users SET rank = ? WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?reports&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "ss", $action, $target);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "DELETE FROM reports WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?reports&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?reports&error=stmtfailed");
    exit();
}
$action = "ApproveReport - " . rankFromNum($action);
session_start();
mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $target, $action, $reason);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../moderation?reports&error=none");
exit();