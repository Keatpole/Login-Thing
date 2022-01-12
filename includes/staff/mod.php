<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if ($_SESSION["rank"] < 1 || !isset($_POST["submit"]) || !$settings->enable_mod_panel) {
    header("location: ../../moderation");
    exit();
}

$username = $_POST["username"];
$action = $_POST["action"];

$user = getTable($conn, "users", ["uid", $username]);

switch ($action) {
    case 3:
        $action = "DeleteComment";
        break;
    case 4:
        $action = "(Un)Mute";
        break;
    default:
        break;
}

if ($user != null) {
    insertTable($conn, "modsuggestions", [$_SESSION["id"], $username, $action]);
    logAction($conn, $_SESSION["id"], $username, $type, $action);
} else {
    header("location: ../../moderation?error=usernotfound");
    exit();
}

header("location: ../../moderation?error=none");