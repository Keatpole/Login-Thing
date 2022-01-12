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

deleteTable($conn, "modsuggestions", ["id", $id]);

if ($_GET["type"] == "DeleteComment") {

    $msgInfo = getTable($conn, "messages", ["id", $username]);

    insertTable($conn, "deletedmessages", [$msgInfo["id"], $msgInfo["message"], $msgInfo["author"], $msgInfo["likes"], $msgInfo["date"]]);
    deleteTable($conn, "messages", ["id", $username]);    
    logAction($conn, $_SESSION["id"], $username, "ApproveSuggestion", $type);

    header("location: ../../moderation?suggestions&error=none");
    exit();
}
elseif ($_GET["type"] == "(Un)Mute") {

    $muted = null;

    foreach (getTable($conn, "mutes", "", true) as $v) {
        if ($v["target"] == $user["id"]) $muted = $v;
    }

    if ($muted) {
        deleteTable($conn, "mutes", ["id", $muted["id"]]);
    } else {
        insertTable($conn, "mutes", [$_SESSION["id"], $user["id"]]);
    }

    logAction($conn, $_SESSION["id"], $username, "ApproveSuggestion", $type);

    header("location: ../../moderation?suggestions&error=none");
    exit();
}
elseif ($_GET["type"] == "-1") {

    $banned = null;

    foreach (getTable($conn, "bans", "", true) as $v) {
        if ($v["target"] == $user["id"]) $banned = $v;
    }

    if ($banned) {
        deleteTable($conn, "bans", ["id", $banned["id"]]);
        updateTable($conn, "users", "rank", "0", ["id", $user["id"]]);
    } else {
        insertTable($conn, "bans", [$_SESSION["id"], $user["id"]]);
        updateTable($conn, "users", "rank", "-1", ["id", $user["id"]]);
    }
    
    logAction($conn, $_SESSION["id"], $username, "ApproveSuggestion", "(Un)Ban");

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

updateTable($conn, "users", "rank", $type, ["uid", $username]);
logAction($conn, $_SESSION["id"], $user["id"], "ApproveSuggestion", $type);

header("location: ../../moderation?suggestions&error=none");
exit();