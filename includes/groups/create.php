<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"])) {
    header("location: ../../groups");
    exit();
}

$sql = "INSERT INTO groups(name, author, members, mods) VALUES (?, ?, ?, ?)";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../groups?error=stmtfailed");
    exit();
}

session_start();

$name = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');

mysqli_stmt_bind_param($stmt, "siss", $name, $_SESSION["id"], $_SESSION["id"], $_SESSION["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../groups?error=none");
exit();