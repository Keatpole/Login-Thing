<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["rank"]) || $_SESSION["rank"] < 2 || !isset($_GET["uid"]) || !$settings->enable_suggestions) {
    header("location: ../../moderation?suggestions");
    exit();
}

deleteTable($conn, "modsuggestions", ["id", $_GET["id"]]);

$user = getTable($conn, "users", ["uid", $_GET["uid"]]);

logAction($conn, $_SESSION["id"], $user["id"], "RefuseSuggestion", $_GET["type"]);

header("location: ../../moderation?suggestions&error=none");
exit();