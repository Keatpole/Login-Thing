<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_posting_comments) {
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

$user = getTable($conn, "users", ["id", $_POST["user"]]);

$message = strtolower($_POST["message"]);

insertTable($conn, "privatemessages", ["message" => $message, "author" => $_SESSION["id"], "receiver" => $_POST["user"]]);

header("location: ../../pm?u=" . $_POST["user"] . "&error=none");
exit();