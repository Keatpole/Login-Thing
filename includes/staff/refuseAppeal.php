<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["rank"]) || $_SESSION["rank"] < 2 || !isset($_GET["id"]) || !$settings->enable_report) {
    header("location: ../../moderation?appeals");
    exit();
}

deleteTable($conn, "appeals", ["id", $_GET["id"]]);
logAction($conn, $_SESSION["id"], $_GET["target"], "RefuseAppeal", $_GET["reason"]);

header("location: ../../moderation?appeals&error=none");
exit();