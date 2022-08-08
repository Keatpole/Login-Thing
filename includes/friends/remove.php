<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["id"])) {
    header("location: ../../.");
    exit();
}

$exit = true;
if ($_SESSION["id"] == getTable($conn, "friendreq", ["id", $_GET["i"]])["user2"] || $_SESSION["id"] == getTable($conn, "friendreq", ["id", $_GET["i"]])["user1"]) {
    $exit = false;
}
if ($_SESSION["id"] == getTable($conn, "friends", ["id", $_GET["i"]])["user2"] || $_SESSION["id"] == getTable($conn, "friends", ["id", $_GET["i"]])["user1"]) {
    $exit = false;
}

if ($exit) {
    header("location: ../../friends?error=authfailed");
    exit();
}

$type = $_GET["t"];
$user = $_GET["u"];
$id = $_GET["i"];

$newtype = ($type == "friend" ? "friends" : "friendreq");

$table = getTable($conn, $newtype, ["id", $id]);

if ($table["user1"] != $_SESSION["id"] && $table["user2"] != $_SESSION["id"]) {
    if (isset($_GET["return"])) {
        header("location: ../../" . $_GET["return"] . "?error=authfailed");
    } else {
        header("location: ../../user?u=" . $user . "&error=authfailed");
    }
    exit();
}

deleteTable($conn, $newtype, ["id", $id]);

if (isset($_GET["return"])) {
    header("location: ../../" . $_GET["return"] . "?error=none");
} else {
    header("location: ../../user?u=" . $user . "&error=none");
}