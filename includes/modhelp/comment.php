<?php

session_start();

$prevent_temp_logout = true;

require_once '../other/functions.php';
require_once '../other/dbh.php';

$prevent_temp_logout = false;

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_posting_comments) {
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

$message = strtolower($_POST["message"]);

$owner = getTable($conn, "users", ["uid", substr(explode("Mod Help (", $group["name"])[1], 0, -1)]);

// Commands
if (str_starts_with($message, "!")) {
    // User commands:
    if (str_starts_with($message, "!help")) {
        if ($_SESSION["rank"] >= 1) header("location: ../../groups?mhg=" . $_POST["groupid"] . "&error=mhgchelpstaff");
        else header("location: ../../groups?mhg=" . $_POST["groupid"] . "&error=mhgchelpmember");
        exit();
    }
    # Mod Help Commands
    if (str_starts_with($message, "!close")) {
        deleteTable($conn, "modhelpgroups", ["id", $_POST["groupid"]]);
        deleteTable($conn, "modhelpmessages", ["groupId", $_POST["groupid"]]);

        header("location: ../../groups?error=gcdone");
        exit();
    }
    elseif (str_starts_with($message, "!bump")) updateTable($conn, "modhelpgroups", "bumps", $group["bumps"] + 1, ["id", $group["id"]]);
    
    elseif (str_starts_with($message, "!ownerbump") && $_SESSION["rank"] >= 1) updateTable($conn, "modhelpgroups", "ownerbumps", $group["ownerbumps"] + 1, ["id", $group["id"]]);
    elseif (str_starts_with($message, "!vote") && $_SESSION["rank"] >= 1) {
        $hasvoted = false;
        foreach (explode(",", $group["votees"]) as $v) {
            if ($v == $_SESSION["id"]) {
                $hasvoted = true;
                break;
            }
        }
        if ($hasvoted) {
            header("location: ../../groups?mhg=" . $_POST["groupid"] . "&error=mhgcfvote");
            exit();
        }

        $power = $_SESSION["rank"] == 1 ? 1 : ($_SESSION["rank"] == 2 ? 5 : ($_SESSION["rank"] == 3 ? 10 : 0));

        $votees = $group["votees"] ? $group["votees"] . "," . $_SESSION["id"] : $_SESSION["id"];

        updateTable($conn, "modhelpgroups", "votes", $group["votes"] + $power, ["id", $group["id"]]);
        updateTable($conn, "modhelpgroups", "votees", $votees, ["id", $group["id"]]);
        
        if ($group["votes"] + $power >= 10) {
            updateTable($conn, "modhelpgroups", "verified", 1, ["id", $group["id"]]);
        }
    }
    elseif (str_starts_with($message, "!email") && $_SESSION["rank"] >= 1) {
        header("location: ../../groups?mhg=" . $_POST["groupid"] . "&msg=Email: " . $owner["email"]);
    }

    header("location: ../../groups?mhg=" . $_POST["groupid"] . "&error=none");
    exit();
}

$message = htmlspecialchars($_POST["message"], ENT_QUOTES, 'UTF-8');

if (isset($_POST["replyid"])) {
    insertTable($conn, "modhelpmessages", ["message" => $message, "author" => $_SESSION["id"], "replyTo" => $_POST["replyid"], "groupId" => $_POST["groupid"]]);
} else {
    insertTable($conn, "modhelpmessages", ["message" => $message, "author" => $_SESSION["id"], "groupId" => $_POST["groupid"]]);
}

header("location: ../../groups?mhg=" . $_POST["groupid"] . "&error=none");
exit();