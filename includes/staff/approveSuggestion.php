<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 2 || !isset($_GET["uid"]) || !$settings->enable_suggestions) {
    header("location: ../../moderation?suggestions");
    exit();
}

$username = $_GET["uid"];
$type = $_GET["type"];
$id = $_GET["id"];

$user = getTable($conn, "users", ["uid", $username]);

if ($_GET["type"] == "DeleteComment") {

    $msgInfo = getTable($conn, "messages", ["id", $username]);

    $sql = "INSERT INTO `deletedmessages`(`msgid`, `message`, `author`, `likes`, `createdate`) VALUES (?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?suggestions&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "sssss", $msgInfo["id"], $msgInfo["message"], $msgInfo["author"], $msgInfo["likes"], $msgInfo["date"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM messages WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?suggestions&error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM modsuggestions WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?suggestions&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?suggestions&error=stmtfailed");
        exit();
    }
    $action = "ApproveSuggestion";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $username, $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?suggestions&error=none");
    exit();
}
elseif ($_GET["type"] == "(Un)Mute") {

    $muted = null;

    foreach (getTable($conn, "mutes", "", true) as $v) {
        if ($v["target"] == $user["id"]) $muted = $v;
    }

    if ($muted) {

        $sql = "DELETE FROM mutes WHERE id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "i", $v["id"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    } else {

        $sql = "INSERT INTO mutes (muter, target) VALUES (?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION["id"], $user["id"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    }

    $sql = "DELETE FROM modsuggestions WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?suggestions&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?suggestions&error=stmtfailed");
        exit();
    }
    $action = "ApproveSuggestion";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $username, $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?suggestions&error=none");
    exit();
}

if ($_GET["type"] >= 2 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?suggestions&error=targetisimmune");
    exit();
}

$user = getTable($conn, "users", ["uid", $username]);

if ($user["rank"] >= 2 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?suggestions&error=targetisimmune");
    exit();
}

$sql = "UPDATE users SET rank = ? WHERE uid = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?suggestions&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "ss", $type, $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "DELETE FROM modsuggestions WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?suggestions&error=stmtfailed");
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?suggestions&error=stmtfailed");
    exit();
}
$action = "ApproveSuggestion";
session_start();
mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../moderation?suggestions&error=none");
exit();