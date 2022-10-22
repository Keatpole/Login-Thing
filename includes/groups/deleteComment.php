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

    deleteTable($conn, "groupmessages", ["id", $_POST["commentId"]]);
    deleteTable($conn, "groupmessages", ["replyTo", $_POST["commentId"]]);
    logAction($conn, $_SESSION["id"], $msgInfo["author"], "DeleteGroupComment", "CID:" . $_POST["commentId"]);

    header("location: ../../groups?g=" . $_POST["groupid"] . "&error=none");

    exit();
}