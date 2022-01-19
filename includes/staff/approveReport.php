<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 2 || !isset($_GET["target"]) || !$settings->enable_report) {
    header("location: ../../moderation?reports");
    exit();
}

$target = $_GET["target"];
$action = $_GET["action"];
$reason = $_GET["reason"];
$id = $_GET["id"];

$user = getTable($conn, "users", ["id", $target]);

if ($action != "-1" && $action != "4") {
    header("location: ../../moderation?reports&error=authfailed");
    exit();
}

if ($user["rank"] > 1 && $_SESSION["rank"] <= 2) {
    header("location: ../../moderation?reports&error=targetisimmune");
    exit();
}

deleteTable($conn, "reports", ["id", $id]);

if ($action == "-1") {

    $banned = false;

    foreach (getTable($conn, "bans", "", true) as $v) {
        if ($v["target"] == $user["id"]) $banned = true;
    }

    if ($banned) {
        header("location: ../../moderation?reports&error=useralreadybanned");
        exit();
    }

    insertTable($conn, "bans", ["banner" => $_SESSION["id"], "target" => $user["id"]]);
    updateTable($conn, "users", "rank", "-1", ["id", $user["id"]]);
    logAction($conn, $_SESSION["id"], $target, "ApproveReport", "Ban");

    header("location: ../../moderation?reports&error=none");
    exit();
} else if ($action == "4") {
    
    $muted = false;

    foreach (getTable($conn, "mutes", "", true) as $v) {
        if ($v["target"] == $user["id"]) $muted = true;
    }

    if ($muted) {
        header("location: ../../moderation?reports&error=useralreadymuted");
        exit();
    }

    insertTable($conn, "mutes", ["muter" => $_SESSION["id"], "target" => $user["id"]]);
    logAction($conn, $_SESSION["id"], $target, "ApproveReport", "Mute");

    header("location: ../../moderation?reports&error=none");
    exit();
}

updateTable($conn, "users", "rank", $action, ["id", $target]);
logAction($conn, $_SESSION["id"], $target, "ApproveReport - " . rankFromNum($action), $reason);

header("location: ../../moderation?reports&error=none");
exit();