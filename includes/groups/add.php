<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"])) {
    header("location: ../../groups");
    exit();
}

$group = getTable($conn, "groups", ["id", $_POST["group"]]);

$exit = true;

$in = false;
foreach (explode(",", $group["members"]) as $v) {
    if ($v == $_POST["user"]) {
        $in = true;
        break;
    }
}
if ($in || $target["id"] == $group["author"]) {
    header("location: ../../groups?error=gcfaddin");
    exit();
}

foreach (getTable($conn, "friends", "", True) as $v) {
    if ($v["user1"] == $_POST["user"] && $v["user2"] == $_SESSION["id"] || $v["user2"] == $_POST["user"] && $v["user1"] == $_SESSION["id"] || $_SESSION["rank"] > 2) {
        $exit = false;
        break;
    }
}

if ($exit) {
    header("location: ../../groups?error=gcfaddfriend");
    exit();
}

if ($group["author"] != $_SESSION["id"]) {
    header("location: ../../groups?error=authfailed");
    exit();
}

$sql = "UPDATE groups SET members=? WHERE id=?;";

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../groups?error=stmtfailed");
    exit();
}


$newMembers = $group["members"] . "," . $_POST["user"];

mysqli_stmt_bind_param($stmt, "si", $newMembers, $_POST["group"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../groups?error=none");
exit();