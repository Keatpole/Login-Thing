<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_posting_comments) {
    header("location: ../../.");
    exit();
}

foreach (getTable($conn, "mutes", "", true) as $v) {
    if ($v["target"] == $_SESSION["id"]) {
        header("location: ../../.");
        exit();
    }
}

if (isset($_POST["replyid"])) {
    $sql = "INSERT INTO `messages`(`message`, `author`, `replyTo`) VALUES (?, ?, ?)";
} else {
    $sql = "INSERT INTO `messages`(`message`, `author`) VALUES (?, ?)";
}

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../.?error=stmtfailed");
    exit();
}

session_start();

$message = htmlspecialchars($_POST["message"], ENT_QUOTES, 'UTF-8');

if (isset($_POST["replyid"])) {
    mysqli_stmt_bind_param($stmt, "sis", $message, $_SESSION["id"], $_POST["replyid"]);
} else {
    mysqli_stmt_bind_param($stmt, "si", $message, $_SESSION["id"]);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../.?error=none");
exit();