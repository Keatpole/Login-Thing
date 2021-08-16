<?php

session_start();

require_once "../other/dbh.php";
require_once "../other/functions.php";

if (!isset($_SESSION["uid"])) {
    header("location: ../../.");
    exit();
}

$type = $_GET["t"];
$user = $_GET["u"];
$id = $_GET["i"];

$newtype = ($type == "friend" ? "friends" : "friendreq");

$table = getTable($conn, $newtype, ["id", $id]);

if ($table["user1"] != $_SESSION["id"] && $table["user2"] != $_SESSION["id"]) {
    if (isset($_GET["return"])) {
        header("location: ../../" . $_GET["return"] . "?error=authfailed");
    } else {
        header("location: ../../user?u=" . $user . "&error=authfailed");
    }
    exit();
}

$sql = "DELETE FROM " . $newtype . " WHERE id=?;";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../friends?error=stmtfailed");
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if (isset($_GET["return"])) {
    header("location: ../../" . $_GET["return"] . "?error=none");
} else {
    header("location: ../../user?u=" . $user . "&error=none");
}