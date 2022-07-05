<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

session_start();

if (isset($_GET["expire_all_sessions"])) {
    deleteTable($conn, "sessions", ["userid", $_SESSION["id"]]);
}

foreach (mysqli_fetch_all(getTable($conn, "sessions", ["userid", $_SESSION["id"]], true)) as $res) {
    $hash = $res[2];

    $verify = password_verify($_COOKIE["token"], $hash);

    if ($verify) {
        deleteTable($conn, "sessions", ["id", $res[0]]);
        break;
    }
}

unset($_COOKIE['token']); 
setcookie('token', null, -1, "/");

session_unset();
session_destroy();

if ($_GET["error"]) {
    header("location: ../../login?error=" . $_GET["error"]);
} else {
    header("location: ../../.");
}
exit();