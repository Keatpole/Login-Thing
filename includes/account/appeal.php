<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_POST["submit"]) || !$settings->enable_report) {
    header("location: ../../appeal");
    exit();
}

$reason = "None";

switch ($_POST["reason"]) {
    case 0:
        $reason = "Username";
        break;
    case 1:
        $reason = "Harassment";
        break;
    case 2:
        $reason = "Impersonation";
        break;
    case 3:
        $reason = "Threats";
        break;
    case 4:
        $reason = "Spam";
        break;
    case 5:
        $reason = "Scam";
        break;
    case 9:
        $reason = "Other";
        break;
    default:
        $reason = "None";
        break;
}

$punishment = $_POST["punishment"];

if (getTable($conn, "users", ["uid", $_POST["user"]]) != null) {
    insertTable($conn, "appeals", [getTable($conn, "users", ["uid", $_POST["user"]])["id"], $reason, $punishment, $_POST["otherreason"]]);
    header("location: ../../appeal?error=none");
} else {
    header("location: ../../appeal?error=usernotfound");
    exit();
}