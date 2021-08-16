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

if ($action == "3") {

    $msgInfo = getTable($conn, "messages", ["id", $username]);

    $sql = "INSERT INTO `deletedmessages`(`msgid`, `message`, `author`, `likes`, `createdate`) VALUES (?, ?, ?, ?, ?);";
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
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $username, $action, $type);
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
    $type = "Verify";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $username, $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../moderation?error=none");
    exit();
}

$user = getTable($conn, "users", ["uid", $username]);

if ($user == null) {
    header("location: ../../moderation?error=usernotfound");
    exit();
}

if ($user["rank"] >= 2 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?error=targetisimmune");
    exit();
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
mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $username, $type, $action);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../moderation?error=none");