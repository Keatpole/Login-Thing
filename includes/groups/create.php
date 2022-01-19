<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"])) {
    header("location: ../../groups");
    exit();
}

insertTable($conn, "groups", ["name" => htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8'), "author" => $_SESSION["id"], "members" => $_SESSION["id"], "mods" => $_SESSION["id"]]);

header("location: ../../groups?error=none");
exit();