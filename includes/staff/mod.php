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

if ($action == 4) {

    $sql = "INSERT INTO modsuggestions (suggester, targetsUid, type) VALUES (?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $action = "(Un)Mute";
    mysqli_stmt_bind_param($stmt, "sss", $_SESSION["id"], $username, $action);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $type = "Mod";
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $username, $type, $action);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}
elseif ($user != null) {

    $sql = "INSERT INTO modsuggestions (suggester, targetsUid, type) VALUES (?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "sss", $_SESSION["id"], $username, $action);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $type = "Mod";
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $user["id"], $type, $action);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
elseif ($action == 3) {

    $sql = "INSERT INTO modsuggestions (suggester, targetsUid, type) VALUES (?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $action = "DeleteComment";
    mysqli_stmt_bind_param($stmt, "sss", $_SESSION["id"], $username, $action);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../moderation?error=stmtfailed");
        exit();
    }
    $type = "Mod";
    mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["id"], $username, $type, $action);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

}
else {
    header("location: ../../moderation?error=usernotfound");
    exit();
}

header("location: ../../moderation?error=none");