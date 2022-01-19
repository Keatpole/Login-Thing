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

$user = ($action != 3 ? getTable($conn, "users", ["uid", $username]) : "blablabla");

switch ($action) {
    case 3:
        $action = "DeleteComment";
        break;
    case 4:
        $action = "(Un)Mute";
        break;
    default:
        $action = $action;
        break;
}

if ($user != null || $action != "DeleteComment") {
    $thing = getTable($conn, "modsuggestions", ["targetsUid", $username]);

    if ($thing != null && $thing["type"] == $action) {
        header("location: ../../moderation?error=duplicateindb");
        exit();
    }
    
    insertTable($conn, "modsuggestions", ["suggester" => $_SESSION["id"], "targetsUid" => $username, "type" => $action]);
    logAction($conn, $_SESSION["id"], $username, "Mod - DeleteComment", "CID:" . $username);
} else {
    header("location: ../../moderation?error=usernotfound");
    exit();
}

header("location: ../../moderation?error=none");