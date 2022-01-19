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

    insertTable($conn, "deletedmessages", ["msgid" => $msgInfo["id"], "message" => $msgInfo["message"], "author" => $msgInfo["author"], "likes" => $msgInfo["likes"], "replyTo" => $msgInfo["replyTo"], "createdate" => $msgInfo["date"]]);
    deleteTable($conn, "messages", ["id", $username]);
    logAction($conn, $_SESSION["id"], $user["id"], "Admin", "DeleteComment");

    header("location: ../../moderation?error=none");
    exit();
}
elseif ($action == "4") {
    $target = getTable($conn, "users", ["uid", $username]);

    updateTable($conn, "users", "verified", ($target["verified"] ? 0 : 1), ["id", $target["id"]]);
    logAction($conn, $_SESSION["id"], $user["id"], "Admin", "(Un)Verify");

    header("location: ../../moderation?error=none");
    exit();
}
elseif ($action == "5") {

    $muted = null;

    foreach (getTable($conn, "mutes", "", true) as $v) {
        if ($v["target"] == $user["id"]) $muted = $v;
    }

    if ($muted) {
        deleteTable($conn, "mutes", ["id", $muted["id"]]);
        logAction($conn, $_SESSION["id"], $user["id"], "Admin", "UnMute");

        header("location: ../../moderation?error=none");
        exit();
    } else {
        insertTable($conn, "mutes", ["muter" => $_SESSION["id"], "target" => $user["id"]]);
        logAction($conn, $_SESSION["id"], $user["id"], "Admin", "Mute");

        header("location: ../../moderation?error=none");
        exit();
    }

}
elseif ($action == "6") {
    updateTable($conn, "users", "deleted", 0, ["id", $user["id"]]);
    updateTable($conn, "users", "deletedate", "NULL", ["id", $user["id"]]);
    logAction($conn, $_SESSION["id"], $user["id"], "Admin", "Undelete");

    header("location: ../../moderation?error=none");
    exit();
}
elseif ($action == "-1") {

    $banned = null;

    foreach (getTable($conn, "bans", "", true) as $v) {
        if ($v["target"] == $user["id"]) $banned = $v;
    }

    if ($banned) {
        deleteTable($conn, "bans", ["id", $banned["id"]]);
        updateTable($conn, "users", "rank", "0", ["id", $user["id"]]);
        logAction($conn, $_SESSION["id"], $user["id"], "Admin", "UnBan");

        header("location: ../../moderation?error=none");
        exit();
    } else {
        insertTable($conn, "bans", ["banner" => $_SESSION["id"], "target" => $user["id"]]);
        updateTable($conn, "users", "rank", "-1", ["id", $user["id"]]);
        logAction($conn, $_SESSION["id"], $user["id"], "Admin", "Ban");

        header("location: ../../moderation?error=none");
        exit();
    }

}

updateTable($conn, "users", "rank", $action, ["uid", $username]);
logAction($conn, $_SESSION["id"], $user["id"], "Admin", $action);

header("location: ../../moderation?error=none");