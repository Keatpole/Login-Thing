<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["uid"]) || !isset($_POST["submit"])) {
    header("location: ../../.");
    exit();
}

if ($_SESSION["id"] != getTable($conn, "friendreq", ["id", $_POST["id"]])["user2"]) {
    header("location: ../../friends?req&error=authfailed");
    exit();
}

$thing1 = getTable($conn, "friends", ["user1", $_POST["user"]]);
$thing2 = getTable($conn, "friends", ["user1", $_SESSION["id"]]);

if ($thing1 != null && $thing1["user2"] == $_SESSION["id"] || $thing2 != null && $thing2["user2"] == $_POST["user"]) {
    header("location: ../../friends?req&error=duplicateindb");
    exit();
}

insertTable($conn, "friends", ["user1" => $_POST["user"], "user2" => $_SESSION["id"]]);
deleteTable($conn, "friends", ["id", $_POST["id"]]);

if ($_POST["return"]) {
    header("location: ../../" . $_POST["return"] . "error=none");
} else {
    header("location: ../../friends?req&error=none");
}