<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

session_start();
session_unset();
session_destroy();

if (isset($_COOKIE["id"])) {
    $hour = time() - 3600 * 24 * 30;
    setcookie("user", "", $hour, "/");
    setcookie("userid", "", $hour, "/");
    setcookie("id", "", $hour, "/");
}

header("location: ../../.");
exit();