<?php

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_POST["submit"]) || !$settings->enable_signup) {
    header("location: ../../signup");
    exit();
}

$email = $_POST["email"];
$username = $_POST["uid"];
$pwd = $_POST["pwd"];
$pwdRepeat = $_POST["pwdrepeat"];

if (empty($email) || empty($username) || empty($pwd) || empty($pwdRepeat)) {
    header("location: ../../signup?error=emptyinput");
    exit();
}
if (!preg_match("/^[a-zA-Z0-9&-_., ]*$/", $username)) {
    header("location: ../../signup?error=invaliduid");
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("location: ../../signup?error=invalidemail");
    exit();
}
if ($pwd !== $pwdRepeat) {
    header("location: ../../signup?error=pwdmatch");
    exit();
}
if (uidExists($conn, $username, $email) !== false) {
    header("location: ../../signup?error=usernametaken");
    exit();
}

insertTable($conn, "users", [$email, $username, password_hash($pwd, PASSWORD_DEFAULT)], ["rank", "verified", "deleted", "deletedate"]);

header("location: ../../signup?error=none");
exit();