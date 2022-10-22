<?php

require_once "settings.php";
require_once "dbh.php";

if (isset($_SESSION["tempacc"]) && $_SESSION["tempacc"] && !in_array($_GET["webname"], ["modhelp", "groups", "resetpass"])) {
    if (isset($prevent_temp_logout) && $prevent_temp_logout) {
        
    } else {
        deleteTable($conn, "modhelpgroups", ["id", $_SESSION["modhelpgroup"]]);
        deleteTable($conn, "modhelpmessages", ["groupId", $_SESSION["modhelpgroup"]]);
        header("location: includes/account/logout");
        exit();
    }
}

if (!$settings->enable_public && $_SERVER['HTTP_HOST'] != "localhost") {
    #echo "<link rel=\"stylesheet\" href=\"../../style.css\">";
    die("<h1>This website is not public at the moment.</h1>");
}

$session_started = session_status() == PHP_SESSION_ACTIVE;
if (!$session_started) session_start();

$_USER = null;

if (isset($_SESSION["id"])) $_USER = getTable($conn, "users", ["id", $_SESSION["id"]]);

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
        $result = mysqli_stmt_get_result($stmt);
        if (!$multiple) $result = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $result;
    } else {
        $sql = "SELECT * FROM " . $table . " WHERE " . $where[0] . " = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: .?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $where[1]);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (!$multiple) $result = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);
        return $result;
    }
}

function insertTable($conn, $table, $colValues) {
    $sql = "INSERT INTO " . $table . " (";
    $sql .= implode(", ", array_keys($colValues));
    $sql .= ") VALUES (";
    $sql .= implode(", ", array_fill(0, count($colValues), "?"));
    $sql .= ");";

    $values = array_values($colValues);
    $values = array_map("htmlspecialchars", $values);

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../.?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, str_repeat("s", count($colValues)), ...$values);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return mysqli_insert_id($conn);
}

function updateTable($conn, $table, $key, $value, $where="", $bind_types="ss") {
    if ($where == "") {
        $sql = "UPDATE " . $table . " SET " . $key . " = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $value);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $sql = "UPDATE " . $table . " SET " . $key . " = ? WHERE " . $where[0] . " = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, $bind_types, $value, $where[1]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function alterTable($conn, $table, $alter) {
    $sql = "ALTER TABLE " . $table . " " . $alter . ";";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../.?error=stmtfailed");
        exit();
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function deleteTable($conn, $table, $where="") {
    if ($where == "") {
        $sql = "DELETE FROM " . $table . ";";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $sql = "DELETE FROM " . $table . " WHERE " . $where[0] . " = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $where[1]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

function showTables($conn) {
    $sql = "SHOW TABLES;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../.?error=stmtfailed");
        exit();
    }
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
}

function logAction($conn, $user, $target, $action, $type) {
    insertTable($conn, "log", ["uid" => $user, "targetsUid" => $target, "action" => $action, "type" => $type]);
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

function login($conn, $id, $cookies = true) {
    $user = getTable($conn, "users", ["id", $id]);

    if ($cookies) {
        $token = bin2hex(random_bytes(36));
        setcookie("token", $token, time() + 31536000, "/");

        $proxyip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "";
        $proxyip = $proxyip ? $proxyip : "";

        $_SESSION["sessionid"] = insertTable($conn, "sessions", ["userid" => $user["id"], "token" => password_hash($token, PASSWORD_DEFAULT), "ip" => $_SERVER['REMOTE_ADDR'], "proxyip" => $proxyip]);
    }

    $_SESSION["uid"] = $user["uid"];
    $_SESSION["id"] = $user["id"];
    $_SESSION["rank"] = $user["rank"];
}

function sqldate_to_date($date, $only_show_date = false, $show_format = true, $separator = " @ ") {
    $date = explode(" ", $date);

    $time = $separator . $date[1];
    $format = " (D/M/Y" . $separator . "H:M:S)";

    if ($only_show_date) {
        $time = "";
        $format = " (D/M/Y)";
    }
    if (!$show_format) $format = "";

    return join("/", array_reverse(explode("-", $date[0]))) . $time . $format;
}