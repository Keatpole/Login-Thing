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
    if ($v == $_SESSION["id"]) $access = true;
}
if (!$access) {
    header("location: ../../groups");
    exit();
}

if ($_SESSION["id"] == $_POST["commentAuthor"] || $_SESSION["rank"] >= 2 || getTable($conn, "groups", ["id", $_POST["groupid"]])["author"] == $_SESSION["id"]) {

    $msgInfo = getTable($conn, "groupmessages", ["id", $_POST["commentId"]]);

    $sql = "INSERT INTO `deletedgroupmessages`(`msgid`, `message`, `author`, `replyTo`, `groupId`, `createdate`) VALUES (?, ?, ?, ?, ?, ?);";
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
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $_POST["commentAuthor"], $action, $_POST["commentId"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../groups?g=" . $_POST["groupid"] . "&error=none");

    exit();
}