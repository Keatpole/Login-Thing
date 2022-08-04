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

    login($conn, $user["id"]);
    
    header("location: ../../.");
    exit();
}