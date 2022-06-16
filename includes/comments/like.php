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
        
    if ($_SESSION["id"] == $msgInfo["author"] || $_SESSION["rank"] >= 2) {
        insertTable($conn, "deletedmessages", ["msgid" => $msgInfo["id"], "message" => $msgInfo["message"], "author" => $msgInfo["author"], "likes" => $msgInfo["likes"], "replyTo" => $msgInfo["replyTo"], "createdate" => $msgInfo["date"]]);
        deleteTable($conn, "messages", ["id", $_POST["commentId"]]);
        deleteTable($conn, "messages", ["replyTo", $_POST["commentId"]]);
        logAction($conn, $_SESSION["id"], $msgInfo["author"], "DeleteComment", "CID:" . $_POST["commentId"]);

        if ($_POST["return"]) {
            header("location: ../../" . $_POST["return"] . "error=none");
        } else {
            header("location: ../../.?error=none");
        }
        exit();
    }
    elseif ($_SESSION["rank"] == 1) {
        insertTable($conn, "modsuggestions", ["suggester" => $_SESSION["id"], "targetsUid" => $_POST["commentId"], "type" => "DeleteComment"]);
        logAction($conn, $_SESSION["id"], $_POST["commentId"], "Mod - DeleteComment", "CID:" . $_POST["commentId"]);

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
        if (isset($_POST["return"])) {
            header("location: ../../" . $_POST["return"] . "error=alreadyliked");
        } else {
            header("location: ../../.?error=alreadyliked");
        }
        exit();
    }
}

insertTable($conn, "messagelikes", ["msgid" => $_POST["commentId"], "userid" => $_SESSION["id"]]);

$commentLikes = getTable($conn, "messages", ["id", $_POST["commentId"]]);

updateTable($conn, "messages", "likes", $commentLikes["likes"] + 1, ["id", $_POST["commentId"]]);

if (isset($_POST["return"])) {
    header("location: ../../" . $_POST["return"] . "error=none");
} else {
    header("location: ../../.?error=none");
}
exit();