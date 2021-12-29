<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 2 || !isset($_GET["target"]) || !$settings->enable_appeal) {
    header("location: ../../moderation?appeals");
    exit();
}

$target = $_GET["target"];
$action = $_GET["action"];
$reason = $_GET["reason"];
$id = $_GET["id"];

$user = getTable($conn, "users", ["id", $target]);

if ($action != "-1" && $action != "4") {
    header("location: ../../moderation?appeals&error=authfailed");
    exit();
}

if ($user["rank"] > 1 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?appeals&error=targetisimmune");
    exit();
}

if ($action == "-1") {

    $banned = false;

    foreach (getTable($conn, "bans", "", true) as $v) {
        if ($v["target"] == $user["id"]) $banned = $v;
    }

    if (!$banned) {
        header("location: ../../moderation?appeals&error=usernotbanned");
        exit();
    }

    $sql = "DELETE FROM bans WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?appeals&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "i", $banned["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "UPDATE users SET rank = ? WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?appeals&error=stmtfailed");
        exit();
    }
    $zero = "0";
    mysqli_stmt_bind_param($stmt, "ss", $zero, $user["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM appeals WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?appeals&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?appeals&error=stmtfailed");
        exit();
    }
    $action = "ApproveAppeal";
    $type = "Unban";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $target, $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?appeals&error=none");
    exit();
} else if ($action == "4") {
    
    $muted = false;

    foreach (getTable($conn, "mutes", "", true) as $v) {
        if ($v["target"] == $user["id"]) $muted = $v;
    }

    if (!$muted) {
        header("location: ../../moderation?appeals&error=usernotmuted");
        exit();
    }

    $sql = "DELETE FROM mutes WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?appeals&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "i", $muted["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM appeals WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?appeals&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?appeals&error=stmtfailed");
        exit();
    }
    $action = "ApproveAppeal";
    $type = "Unmute";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $target, $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?appeals&error=none");
    exit();
}

$sql = "UPDATE users SET rank = ? WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?appeals&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "ss", $action, $target);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "DELETE FROM appeals WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?appeals&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?appeals&error=stmtfailed");
    exit();
}
$action = "ApproveAppeal - " . rankFromNum($action);
session_start();
mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $target, $action, $reason);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../moderation?appeals&error=none");
exit();