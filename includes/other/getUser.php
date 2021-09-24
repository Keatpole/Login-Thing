<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (isset($_SESSION["id"]) && getTable($conn, "bans", ["target", $_SESSION["id"]]) == null) {
    if (isset($_GET["user"]) || isset($_POST["user"])) {

        if (isset($_POST["user"])) {
            $user = getTable($conn, "users", ["uid", $_POST["user"]]);
            $user = ($user == null ? getTable($conn, "users", ["id", $_POST["user"]]) : $user);
        }
        elseif (isset($_GET["user"])) {
            $user = getTable($conn, "users", ["uid", $_GET["user"]]);
            $user = ($user == null ? getTable($conn, "users", ["id", $_GET["user"]]) : $user);
        }

        if ($user == null) {
            header("location: ../../.?error=usernotfound");
        }

        $blacklist = ["pwd", "email"];

        $finished = "";

        foreach ($user as $k => $v) {
            if (!in_array($k, $blacklist)) $finished .= $k . ":" . $v . ",";
        }

        echo substr($finished, 0, -1);
    } else {
        header("location: ../../.");
    }
} else {
    header("location: ../../.");
}