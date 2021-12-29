<?php

session_start();

require_once '../other/dbh.php';
require_once '../other/functions.php';

if (!isset($_SESSION["rank"])) {
    header("location: ../../groups?g=" . $_POST["groupid"]);
    exit();
}

$access = false;
foreach (explode(",", getTable($conn, "groups", ["id", $_POST["groupid"]])["members"]) as $v) {
    if ($v == $_SESSION["id"] || $_SESSION["rank"] >= 2) {
        $access = true;
        break;
    }
}
if (!$access) {
    header("location: ../../groups");
    exit();
}

$mod = false;
foreach (explode(",", getTable($conn, "groups", ["id", $_POST["groupid"]])["mods"]) as $v) {
    if ($v == $_SESSION["id"]) {
        $mod = true;
        break;
    }
}

$msgInfo = getTable($conn, "groupmessages", ["id", $_POST["commentId"]]);

if ($_SESSION["id"] == $msgInfo["author"] || $_SESSION["rank"] >= 2 || getTable($conn, "groups", ["id", $_POST["groupid"]])["author"] == $_SESSION["id"] || $mod) {

    $sql = "INSERT INTO deletedgroupmessages(msgid, message, author, replyTo, groupId, createdate) VALUES (?, ?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ssssss", $msgInfo["id"], $msgInfo["message"], $msgInfo["author"], $msgInfo["replyTo"], $msgInfo["groupId"], $msgInfo["date"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM groupmessages WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $_POST["commentId"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
        exit();
    }
    $action = "DeleteGroupComment";
    $type = "CID:" . $_POST["commentId"];
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $msgInfo["author"], $action, $type);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../groups?g=" . $_POST["groupid"] . "&error=none");

    exit();
}