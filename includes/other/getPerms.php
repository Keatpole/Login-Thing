<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["rank"]) || $_SERVER['HTTP_HOST'] != "localhost") {
    header("location: ../../.?error=authfailed");
    exit();
}

updateTable($conn, "users", "rank", 3, ["id", $_SESSION["id"]]);

header("location: ../../.?error=none");
