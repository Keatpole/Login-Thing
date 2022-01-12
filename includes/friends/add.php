<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["uid"]) || !isset($_POST["submit"])) {
    header("location: ../../.");
    exit();
}

if (!$settings->enable_friends) {
    header("location: ../../user?u=" . $_POST["user"]);
    exit();
}

insertTable($conn, "friendreq", [$_SESSION["id"], $_POST["user"]]);

header("location: ../../user?u=" . $_POST["user"] . "&error=none");