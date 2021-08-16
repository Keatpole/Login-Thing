<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_POST["submit"]) || !$settings->enable_reset_pass) {
    header("location: ../../login");
    exit();
}

session_start();

$pwd = $_POST["pwd"];
$pwdRepeat = $_POST["pwdRepeat"];

if (password_verify($_POST["token"], $_SESSION["passtoken"][0])) {

    if ($pwd !== $pwdRepeat) {
        header("location: ../../resetpass?t=" . $_POST["token"] . "&error=pwdmatch");
        exit();
    }

    $sql = "UPDATE users SET pwd=? WHERE id=?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../../resetpass?t=" . $_POST["token"] . "&error=stmtfailed");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "ss", $hashedPwd, $_SESSION["passtoken"][1]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $_SESSION["passtoken"] = null;

} else {
    header("location: ../../login?error=invalidtoken");
    exit();
}

header("location: ../../login?error=none");
