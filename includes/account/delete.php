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

$sql = "UPDATE `users` SET `deleted`=1,`deletedate`=? WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../user?error=stmtfailed");
    exit();
}
$current_date = date("Y-m-d H:i:s", strtotime("+1 Month"));
mysqli_stmt_bind_param($stmt, "ss", $current_date, $_SESSION["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

session_start();
session_unset();
session_destroy();

header("location: ../../.?error=none");