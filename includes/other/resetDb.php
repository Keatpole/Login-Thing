<?php

session_start();

if (!isset($_SESSION["rank"]) || $_SESSION["rank"] < 3) {
    header("location: ../../.?error=authfailed");
    exit();
}

echo "<link rel=\"stylesheet\" href=\"../../style.css\">";
echo "<button class=\"button\"><a href=\"?confirm\" style=\"text-decoration: none; color: white;\">Are you sure?</a></button>";

if (isset($_GET["confirm"])) {

    require_once "../other/dbh.php";
    require_once "../other/functions.php";

    foreach (showTables($conn) as $_ => $v) {
        foreach ($v as $_ => $l) {
            deleteTable($conn, $l);
            alterTable($conn, $l, "AUTO_INCREMENT = 1");
        }
    }

    $email = "a@a.a";
    $uid = "Test";
    $pwd = "123";
    $rank = 3; # Owner
    $verified = 1;

    insertTable($conn, "users", ["email" => $email, "uid" => $uid, "pwd" => password_hash($pwd, PASSWORD_DEFAULT)]);
    updateTable($conn, "users", "rank", $rank, ["uid", $uid]);
    updateTable($conn, "users", "verified", $verified, ["uid", $uid]);

    # Log out
    session_unset();
    session_destroy();
    session_start();

    header("location: ../..");
}