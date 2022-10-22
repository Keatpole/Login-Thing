<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_POST["submit"]) || !$settings->enable_login) {
    header("location: ../../login");
    exit();
}

session_unset();
session_destroy();
session_start();

$username = $_POST["uid"];
$pwd = $_POST["pwd"];

$user = getTable($conn, "users", ["uid", $username]);
if ($user === null) $user = getTable($conn, "users", ["email", $username]);
if ($user === null) {
    header("location: ../../login?error=wronglogin");
    exit();
}

$seccodestable = getTable($conn, "securitycodes", ["uuid", $user["id"]]);
if ($seccodestable && $seccodestable["questions"]) {
    $i = 0;
    foreach (explode(",", $seccodestable["answers"]) as $v) {
        $i++;

        if (!isset($_POST["a" . $i]) || !password_verify($_POST["a" . $i], $v)) {
            $_SESSION["accessverify"] = password_hash("uhusifhoasuif9hfuio43289fhwudiwa" . strtolower($user["uid"]) . "dojkaflhs7ag8hfduaasij" . $user["rank"] * 3 . $user["rank"] / 1.2 . $user["verified"] + 1 * 11 . $user["id"] + $user["rank"] / ($user["verified"] + $user["id"]) * 92 . $user["id"] / 3.14, PASSWORD_DEFAULT);

            $thing = !isset($_POST["a" . $i]) ? "" : "&error=wronglogin";

            header("location: ../../verifyuser?u=" . $user["id"] . $thing);
            exit();
        }
    }
}

if (empty($username) || empty($pwd)) {
    header("location: ../../login?error=emptyinput");
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