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

    insertTable($conn, "deletedprivatemessages", ["msgid" => $msgInfo["id"], "message" => $msgInfo["message"], "author" => $msgInfo["author"], "receiver" => $msgInfo["receiver"], "createdate" => $msgInfo["date"]]);
    deleteTable($conn, "privatemessages", ["id", $_POST["commentId"]]);
    logAction($conn, $_SESSION["id"], $msgInfo["author"], "DeletePrivateMessage", "CID:" . $_POST["commentId"]);

    header("location: ../../pm?u=" . $_POST["user"] . "&error=none");

    exit();
}

exit();