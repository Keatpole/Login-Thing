<?php

require_once "settings.php";

if (!$settings->enable_public && $_SERVER['HTTP_HOST'] != "localhost") {
    #echo "<link rel=\"stylesheet\" href=\"../../style.css\">";
    die("<h1>This website is not public at the moment.</h1>");
}

function uidExists($conn, $username, $email) {
    $sql = "SELECT * FROM users WHERE uid = ? OR email = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../signup?error=stmtfailed");
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    }
    else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function getTable($conn, $table, $where="", $multiple=false) {
    if ($where == "") {
        $sql = "SELECT * FROM " . $table . ";";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: .?error=stmtfailed");
            exit();
        }
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $sql = "SELECT * FROM " . $table . " WHERE " . $where[0] . " = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: .?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $where[1]);
        mysqli_stmt_execute($stmt);

        if ($multiple) {
            return mysqli_stmt_get_result($stmt);
        } else {
            return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        }
        mysqli_stmt_close($stmt);
    }
}

function rankFromNum($num) {
    if ($num === null) { return "There is no account with this username"; }
    switch ($num) {
        case -1:
            return "Banned";
        case 0:
            return "User";
        case 1:
            return "Mod";
        case 2:
            return "Admin";
        case 3:
            return "Owner";
        default:
            return "Unknown (" . $num . ")";
    }
}
