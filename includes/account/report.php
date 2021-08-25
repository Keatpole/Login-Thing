<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_report) {
    header("location: ../../report");
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


if (getTable($conn, "users", ["uid", $_POST["user"]]) != null) {

    $sql = "INSERT INTO reports (reporter, target, reason, otherreason) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../report?error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], getTable($conn, "users", ["uid", $_POST["user"]])["id"], $reason, $_POST["otherreason"]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("location: ../../report?error=none");

}
else {
    header("location: ../../report?error=usernotfound");
    exit();
}

