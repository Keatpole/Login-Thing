<?php

session_start();

require_once '../other/dbh.php';
require_once '../other/functions.php';

if (!isset($_SESSION["rank"])) {
    header("location: ../../pm?u=" . $_POST["user"]);
    exit();
}

$exit = true;

foreach (getTable($conn, "friends", "", True) as $v) {
    if ($v["user1"] == $_POST["user"] && $v["user2"] == $_SESSION["id"] || $v["user2"] == $_POST["user"] && $v["user1"] == $_SESSION["id"] || $_SESSION["rank"] > 2) {
        $exit = false;
        break;
    }
}

if ($exit) {
    header("location: ../../.?error=notfriend");
    exit();
}

$msgInfo = getTable($conn, "privatemessages", ["id", $_POST["commentId"]]);

if ($_SESSION["id"] == $msgInfo["author"] || $_SESSION["rank"] >= 2) {

    echo "Hi";

    $sql = "INSERT INTO deletedprivatemessages(msgid, message, author, receiver, createdate) VALUES (?, ?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../pm?u=" . $_POST["user"] . "&error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "sssss", $msgInfo["id"], $msgInfo["message"], $msgInfo["author"], $msgInfo["receiver"], $msgInfo["date"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM privatemessages WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../pm?u=" . $_POST["user"] . "&error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "i", $_POST["commentId"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../pm?u=" . $_POST["user"] . "&error=stmtfailed");
        exit();
    }
    $action = "DeletePrivateComment";
    session_start();
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $msgInfo["author"], $action, $_POST["commentId"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../pm?u=" . $_POST["user"] . "&error=none");

    exit();
}

exit();