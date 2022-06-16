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

    insertTable($conn, "users", ["email" => "a@a.a", "uid" => "Test", "pwd" => password_hash("123", PASSWORD_DEFAULT)]);
    updateTable($conn, "users", "rank", 3, ["uid", "Test"]);
    updateTable($conn, "users", "verified", 1, ["uid", "Test"]);

    header("location: ../account/logout");
}