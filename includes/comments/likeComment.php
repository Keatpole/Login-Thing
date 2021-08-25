<?php

session_start();

require_once '../other/dbh.php';
require_once '../other/functions.php';

if (!isset($_SESSION["rank"])) {
    header("location: ../../.");
    exit();
}

$msgInfo = getTable($conn, "messages", ["id", $_POST["commentId"]]);

if (isset($_POST["delete"])) {
        
    if ($_SESSION["id"] == $_POST["commentAuthor"] && $msgInfo["author"] == $_POST["commentAuthor"] || $_SESSION["rank"] >= 2) {

        $sql = "INSERT INTO deletedmessages(msgid, message, author, likes, replyTo, createdate) VALUES (?, ?, ?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ssssss", $msgInfo["id"], $msgInfo["message"], $msgInfo["author"], $msgInfo["likes"], $msgInfo["replyTo"], $msgInfo["date"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM messages WHERE id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "i", $_POST["commentId"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../.?error=stmtfailed");
            exit();
        }
        $action = "DeleteComment";
        session_start();
        mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $_POST["commentAuthor"], $action, $_POST["commentId"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($_POST["return"]) {
            header("location: ../../" . $_POST["return"] . "error=none");
        } else {
            header("location: ../../.?error=none");
        }
        exit();
    }
    elseif ($_SESSION["rank"] == 1) {

        $sql = "INSERT INTO modsuggestions(targetsUid, type) VALUES (?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../". $_POST["return"] ."error=stmtfailed");
            exit();
        }
        $action = "DeleteComment";
        mysqli_stmt_bind_param($stmt, "ss", $_POST["commentId"], $action);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "INSERT INTO log (uid, targetsUid, action, type) VALUES (?, ?, ?, ?);";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../moderation" . $_POST["return"] . "error=stmtfailed");
            exit();
        }
        $type = "Mod";
        mysqli_stmt_bind_param($stmt, "ssss", $_SESSION["uid"], $_POST["commentId"], $type, $action);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ../../" . $_POST["return"] . "error=none");
        exit();
        
    } else {
        header("location: ../../.");
        exit();
    }
}

if (!isset($_POST["submit"]) || !$settings->enable_likes) {
    header("location: ../../.");
    exit();
}

session_start();

$commentLikesInfo = getTable($conn, "messagelikes", ["msgid", $_POST["commentId"]], true);

foreach ($commentLikesInfo as $result) {
    if ($result["userid"] == $_SESSION["id"]) {
        if ($_POST["return"]) {
            header("location: ../../" . $_POST["return"] . "error=alreadyliked");
        } else {
            header("location: ../../.?error=alreadyliked");
        }
        exit();
    }
}

$sql = "INSERT INTO messagelikes (msgid, userid) VALUES (?, ?);";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../.?error=stmtfailed");
    exit();
}

mysqli_stmt_bind_param($stmt, "ii", $_POST["commentId"], $_SESSION["id"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$commentLikes = getTable($conn, "messages", ["id", $_POST["commentId"]]);

$sql = "UPDATE messages SET likes = ? WHERE id = ?;";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../.?error=stmtfailed");
    exit();
}


$newLikes = $commentLikes["likes"] + 1;
mysqli_stmt_bind_param($stmt, "ii", $newLikes, $_POST["commentId"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($_POST["return"]) {
    header("location: ../../" . $_POST["return"] . "error=none");
} else {
    header("location: ../../.?error=none");
}
exit();