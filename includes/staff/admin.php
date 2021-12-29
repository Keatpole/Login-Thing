<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 2 || !isset($_POST["submit"]) || !$settings->enable_admin_panel) {
    header("location: ../../moderation");
    exit();
}

$username = $_POST["username"];
$action = $_POST["action"];

$user = getTable($conn, "users", ["uid", $username]);

if ($user == null) {
    header("location: ../../moderation?error=usernotfound");
    exit();
}

if ($user["rank"] >= 2 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?error=targetisimmune");
    exit();
}

if ($action == "3") {

    $msgInfo = getTable($conn, "messages", ["id", $username]);

    $sql = "INSERT INTO deletedmessages(msgid, message, author, likes, createdate) VALUES (?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "sssss", $msgInfo["id"], $msgInfo["message"], $msgInfo["author"], $msgInfo["likes"], $msgInfo["date"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM messages WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $action = "Admin";
    $type = "DeleteComment";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?error=none");
    exit();
}
elseif ($action == "4") {
    $target = getTable($conn, "users", ["uid", $username]);

    $sql = "UPDATE users SET verified=? WHERE id=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }

    $ver = 1;
    if ($target["verified"] == 1) { $ver = 0; }

    mysqli_stmt_bind_param($stmt, "ss", $ver, $target["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $action = "Admin";
    $type = "(Un)Verify";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?error=none");
    exit();
}
elseif ($action == "5") {

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

        $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        $action = "Admin";
        $type = "UnMute";
        session_start();
        mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ../../moderation?error=none");
        exit();

    }
    else {

        $sql = "INSERT INTO mutes (muter, target) VALUES (?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $_SESSION["id"], $user["id"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        $action = "Admin";
        $type = "Mute";
        session_start();
        mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ../../moderation?error=none");
        exit();

    }

}
elseif ($action == "6") {
    $sql = "UPDATE `users` SET `deleted`=0,`deletedate`=NULL WHERE id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../user?error=stmtfailed");
        exit();
    }
    session_start();
    mysqli_stmt_bind_param($stmt, "s", $user["id"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $action = "Admin";
    $type = "Undelete";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?error=none");
    exit();
}
elseif ($action == "-1") {

    $banned = null;

    foreach (getTable($conn, "bans", "", true) as $v) {
        if ($v["target"] == $user["id"]) $banned = $v;
    }

    if ($banned) {

        $sql = "DELETE FROM bans WHERE id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "i", $v["id"]);
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

        $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        $action = "Admin";
        $type = "UnBan";
        session_start();
        mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ../../moderation?error=none");
        exit();

    }
    else {

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
        $zero = "-1";
        mysqli_stmt_bind_param($stmt, "ss", $zero, $user["id"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation?error=stmtfailed");
            exit();
        }
        $action = "Admin";
        $type = "Ban";
        session_start();
        mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $action, $type);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ../../moderation?error=none");
        exit();

    }

}

$sql = "UPDATE users SET rank = ? WHERE uid = ?;";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?error=stmtfailed");
    exit();
}

mysqli_stmt_bind_param($stmt, "ss", $action, $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../moderation?error=stmtfailed");
    exit();
}
$type = "Admin";
session_start();
mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $type, $action);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../moderation?error=none");