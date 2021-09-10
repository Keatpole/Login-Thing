<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

$banned = null;

foreach (getTable($conn, "bans", "", true) as $v) {
    if ($v["target"] == $user["id"]) $banned = $v;
}

if (isset($_SESSION["rank"]) && $banned) {
    header("location: ../../user");
    exit();
}

$sql = "DELETE FROM users WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../user?error=stmtfailed");
    exit();
}
session_start();
mysqli_stmt_bind_param($stmt, "s", $_SESSION["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

session_start();
session_unset();
session_destroy();

header("location: ../../.?error=none");