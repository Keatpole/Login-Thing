<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_posting_comments) {
    header("location: ../../pm?u=" . $_POST["user"]);
    exit();
}

$exit = true;

foreach (getTable($conn, "friends", "", True) as $v) {
    if ($v["user1"] == $_POST["user"] && $v["user2"] == $_SESSION["id"] || $v["user2"] == $_POST["user"] && $v["user1"] == $_SESSION["id"] || $_SESSION["rank"] > 2) {
        $exit = false;
        break;
    }
}

if ($exit) {
    header("location: ../../.?error=notfriend");
    exit();
}

$user = getTable($conn, "users", ["id", $_POST["user"]]);

$message = strtolower($_POST["message"]);

$sql = "INSERT INTO privatemessages(message, author, receiver) VALUES (?, ?, ?)";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../pm?u=" . $_POST["user"] . "&error=stmtfailed");
    exit();
}

session_start();

$message = htmlspecialchars($_POST["message"], ENT_QUOTES, 'UTF-8');

mysqli_stmt_bind_param($stmt, "sss", $message, $_SESSION["id"], $_POST["user"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../pm?u=" . $_POST["user"] . "&error=none");
exit();