<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_POST["submit"]) || !$settings->enable_login) {
    header("location: ../../login");
    exit();
}

$username = $_POST["uid"];
$pwd = $_POST["pwd"];

if (empty($username) || empty($pwd)) {
    header("location: ../../login?error=emptyinput");
    exit();
}

$user = getTable($conn, "users", ["uid", $username]);
if ($user === null) $user = getTable($conn, "users", ["email", $username]);
if ($user === null) {
    header("location: ../../login?error=wronglogin");
    exit();
}

$pwdHashed = $user["pwd"];
$checkPwd = password_verify($pwd, $pwdHashed);

if ($checkPwd === false) {
    header("location: ../../login?error=wronglogin");
    exit();
}
else if ($checkPwd === true) {
    session_start();

    $_SESSION["uid"] = $user["uid"];
    $_SESSION["id"] = $user["id"];
    $_SESSION["rank"] = $user["rank"];

    if (isset($_POST["remember"]) && $_POST["remember"] == "on") {
        $test = password_hash($user["id"], PASSWORD_DEFAULT);

        $hour = time() + 3600 * 24 * 30;
        setcookie("userid", $test, $hour, "/");
        setcookie("user", password_hash($test, PASSWORD_DEFAULT), $hour, "/");
        setcookie("id", $user["id"], $hour, "/");
    }

    $_SESSION["passtoken"] = null;
    
    header("location: ../../.");
    exit();
}
