<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 2 || !isset($_GET["target"]) || !$settings->enable_appeal) {
    header("location: ../../moderation?appeals");
    exit();
}

$target = $_GET["target"];
$action = $_GET["action"];
$reason = $_GET["reason"];
$id = $_GET["id"];

$user = getTable($conn, "users", ["id", $target]);

if ($action != "-1" && $action != "4") {
    header("location: ../../moderation?appeals&error=authfailed");
    exit();
}

if ($user["rank"] > 1 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?appeals&error=targetisimmune");
    exit();
}

deleteTable($conn, "appeals", ["id", $id]);

if ($action == "-1") {

    $banned = false;

    foreach (getTable($conn, "bans", "", true) as $v) {
        if ($v["target"] == $user["id"]) $banned = $v;
    }

    if (!$banned) {
        header("location: ../../moderation?appeals&error=usernotbanned");
        exit();
    }

    deleteTable($conn, "bans", ["id", $banned["id"]]);
    updateTable($conn, "users", "rank", "0", ["id", $user["id"]]);
    logAction($conn, $_SESSION["id"], "ApproveAppeal", "UnBan", $target);

    header("location: ../../moderation?appeals&error=none");
    exit();
} else if ($action == "4") {
    
    $muted = false;

    foreach (getTable($conn, "mutes", "", true) as $v) {
        if ($v["target"] == $user["id"]) $muted = $v;
    }

    if (!$muted) {
        header("location: ../../moderation?appeals&error=usernotmuted");
        exit();
    }

    deleteTable($conn, "mutes", ["id", $muted["id"]]);
    logAction($conn, $_SESSION["id"], $target, "ApproveAppeal", "UnMute");

    header("location: ../../moderation?appeals&error=none");
    exit();
}

updateTable($conn, "users", "rank", $action, ["id", $target]);
logAction($conn, $_SESSION["id"], $target, "ApproveAppeal - " . rankFromNum($action), $reason);

header("location: ../../moderation?appeals&error=none");
exit();