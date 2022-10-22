<?php

session_start();

require_once '../other/dbh.php';
require_once '../other/functions.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["groupid"])) {
    header("location: ../../groups?mhg=" . $_POST["groupid"]);
    exit();
}

$group = getTable($conn, "modhelpgroups", ["id", $_POST["groupid"]]);

if ($_SESSION["rank"] <= 0) {
    if (isset($_SESSION["modhelpgroup"])) {
        if ($_SESSION["modhelpgroup"] != $group["id"]) {
            header("location: ../../groups");
            exit();
        }
    }
    else {
        header("location: ../../groups");
        exit();
    }
}

$msgInfo = getTable($conn, "modhelpmessages", ["id", $_POST["commentId"]]);

if ($_SESSION["id"] == $msgInfo["author"] || $_SESSION["rank"] >= 2) {

    logAction($conn, $_SESSION["id"], $msgInfo["author"], "DeleteModHelpComment", "CID:" . $_POST["commentId"] . ", Comment: " . $msgInfo["message"]);
    deleteTable($conn, "modhelpmessages", ["id", $_POST["commentId"]]);
    deleteTable($conn, "modhelpmessages", ["replyTo", $_POST["commentId"]]);

    header("location: ../../groups?mhg=" . $_POST["groupid"] . "&error=none");

    exit();
}