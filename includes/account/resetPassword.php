<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_POST["submit"]) || !$settings->enable_reset_pass) {
    header("location: ../../login");
    exit();
}

session_start();

$pwd = $_POST["pwd"];
$pwdRepeat = $_POST["pwdRepeat"];

if (password_verify($_POST["token"], getTable($conn, "passwordtokens", ["id", $_SESSION["ptid"]])["token"])) {

    if ($pwd !== $pwdRepeat) {
        header("location: ../../resetpass?t=" . $_POST["token"] . "&error=pwdmatch");
        exit();
    }

    updateTable($conn, "users", "pwd", password_hash($pwd, PASSWORD_DEFAULT), ["id", getTable($conn, "passwordtokens", ["id", $_SESSION["ptid"]])["userid"]]);
    deleteTable($conn, "passwordtokens", ["id", $_SESSION["ptid"]]);

    $_SESSION["ptid"] = null;

} else {
    header("location: ../../login?error=invalidtoken");
    exit();
}

header("location: logout?error=none");