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

if (password_verify($_POST["token"], $_SESSION["passtoken"][0])) {

    if ($pwd !== $pwdRepeat) {
        header("location: ../../resetpass?t=" . $_POST["token"] . "&error=pwdmatch");
        exit();
    }

    updateTable($conn, "users", "pwd", password_hash($pwd, PASSWORD_DEFAULT), ["id", $_SESSION["passtoken"][1]]);

    $_SESSION["passtoken"] = null;

} else {
    header("location: ../../login?error=invalidtoken");
    exit();
}

header("location: ../../login?error=none");
