<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["id"]) || !isset($_POST["submit"])) {
    header("location: ../../.");
    exit();
}

if (!$settings->enable_friends) {
    header("location: ../../user?u=" . $_POST["user"]);
    exit();
}

$thing1 = getTable($conn, "friendreq", ["user1", $_POST["user"]]);
$thing2 = getTable($conn, "friendreq", ["user1", $_SESSION["id"]]);

if ($thing1 != null && $thing1["user2"] == $_SESSION["id"] || $thing2 != null && $thing2["user2"] == $_POST["user"]) {
    header("location: ../../user?u=" . $_POST["user"] . "&error=duplicateindb");
    exit();
}

insertTable($conn, "friendreq", ["user1" => $_SESSION["id"], "user2" => $_POST["user"]]);

header("location: ../../user?u=" . $_POST["user"] . "&error=none");