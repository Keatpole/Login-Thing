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

if (!$checkPwd) {
    header("location: ../../login?error=wronglogin");
    exit();
}
else {
    session_start();

    $_SESSION["uid"] = $user["uid"];
    $_SESSION["id"] = $user["id"];
    $_SESSION["rank"] = $user["rank"];
    
    $_SESSION["passtoken"] = null;
    
    header("location: ../../.");
    exit();
}
