<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["rank"]) || $_SESSION["rank"] < 3) {
    header("location: ../../.?error=authfailed");
    exit();
}

$sql = "SHOW TABLES";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../.?error=stmtfailed");
    exit();
}
mysqli_stmt_execute($stmt);

foreach (mysqli_stmt_get_result($stmt) as $i => $v) {
    foreach ($v as $k => $l) {
        $sql = "DELETE FROM `" . $l . "` WHERE 1";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        mysqli_stmt_execute($stmt);
        $sql = "ALTER TABLE " . $l . " AUTO_INCREMENT = 1;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        mysqli_stmt_execute($stmt);
    }
}

header("location: ../account/logout");