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

    $sql = "SHOW TABLES";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../.?error=stmtfailed");
        exit();
    }
    mysqli_stmt_execute($stmt);

    foreach (mysqli_stmt_get_result($stmt) as $_ => $v) {
        foreach ($v as $_ => $l) {
            $sql = "DELETE FROM " . $l . ";";
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
}