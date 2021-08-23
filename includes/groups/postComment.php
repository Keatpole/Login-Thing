<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_posting_comments) {
    header("location: ../../groups?g=" . $_POST["groupid"]);
    exit();
}

$access = false;
foreach (explode(",", getTable($conn, "groups", ["id", $_POST["groupid"]])["members"]) as $v) {
    if ($v == $_SESSION["id"]) $access = true;
}
if (!$access) {
    header("location: groups");
    exit();
}


if (str_starts_with($_POST["message"], "!") && getTable($conn, "groups", ["id", $_POST["groupid"]])["author"] == $_SESSION["id"]) {
    if (str_starts_with($_POST["message"], "!kick")) {
        $target = getTable($conn, "users", ["uid", explode("!kick ", $_POST["message"])[1]]);

        foreach (explode(",", getTable($conn, "groups", ["id", $_POST["groupid"]])["members"]) as $v) {
            if ($v == $target["id"]) {
                $sql = "UPDATE groups SET members=? WHERE id=?;";

                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("location: ../../groups?error=stmtfailed");
                    exit();
                }

                $newMembers = "";

                foreach (explode(",", getTable($conn, "groups", ["id", $_POST["groupid"]])["members"]) as $v) {
                    if ($v != $target["id"]) $newMembers .= $v . ",";
                }

                $newMembers = substr($newMembers, 0, -1);
                
                mysqli_stmt_bind_param($stmt, "si", $newMembers, $_POST["groupid"]);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

    }

    #exit();

    header("location: ../../groups?g=" . $_POST["groupid"] . "&error=none");
    exit();
}


if (isset($_POST["replyid"])) {
    $sql = "INSERT INTO `groupmessages`(`message`, `author`, `replyTo`, `groupId`) VALUES (?, ?, ?, ?);";
} else {
    $sql = "INSERT INTO `groupmessages`(`message`, `author`, `groupId`) VALUES (?, ?, ?)";
}

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
    exit();
}

session_start();

$message = htmlspecialchars($_POST["message"], ENT_QUOTES, 'UTF-8');

if (isset($_POST["replyid"])) {
    mysqli_stmt_bind_param($stmt, "ssss", $message, $_SESSION["id"], $_POST["replyid"], $_POST["groupid"]);
} else {
    mysqli_stmt_bind_param($stmt, "sss", $message, $_SESSION["id"], $_POST["groupid"]);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("location: ../../groups?g=" . $_POST["groupid"] . "&error=none");
exit();