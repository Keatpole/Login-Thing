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
    if ($user["deleted"]) {
        #header("location: ../../login?error=userdeleted");
        #exit();

        # Undelete the user
        updateTable($conn, "users", "deleted", 0, ["uid", $username], "is");
    }

    session_start();

    $token = bin2hex(random_bytes(36));
    setcookie("token", $token, time() + 31536000, "/");

    insertTable($conn, "sessions", ["userid" => $user["id"], "token" => password_hash($token, PASSWORD_DEFAULT), "ip" => $_SERVER['REMOTE_ADDR'], "proxyip" => $_SERVER['HTTP_X_FORWARDED_FOR']]);

    $_SESSION["uid"] = $user["uid"];
    $_SESSION["id"] = $user["id"];
    $_SESSION["rank"] = $user["rank"];
    
    $_SESSION["passtoken"] = null;
    
    header("location: ../../.");
    exit();
}