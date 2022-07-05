<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

$banned = null;

foreach (getTable($conn, "bans", "", true) as $v) {
    if ($v["target"] == $user["id"]) $banned = $v;
}

if (isset($_SESSION["rank"]) && $banned) {
    header("location: ../../user");
    exit();
}


updateTable($conn, "users", "deleted", 1, ["id", $_SESSION["id"]]);
updateTable($conn, "users", "deletedate", date("Y-m-d H:i:s", strtotime("+1 Month")), ["id", $_SESSION["id"]]);

$date = getTable($conn, "users", ["id", $_SESSION["id"]])["deletedate"];

session_unset();
session_destroy();

header("location: ../../.?error=userdeleted&deletedate=" . urlencode($date));