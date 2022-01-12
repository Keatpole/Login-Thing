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

deleteTable($conn, "friendreq", ["id", $_POST["id"]]);

if ($_POST["return"]) {
    header("location: ../../" . $_POST["return"] . "error=none");
} else {
    header("location: ../../friends?req&error=none");
}