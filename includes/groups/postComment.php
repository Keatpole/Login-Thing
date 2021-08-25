<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_posting_comments) {
    header("location: ../../groups?g=" . $_POST["groupid"]);
    exit();
}

$group = getTable($conn, "groups", ["id", $_POST["groupid"]]);

$access = false;
foreach (explode(",", $group["members"]) as $v) {
    if ($v == $_SESSION["id"]) $access = true;
}
if (!$access) {
    header("location: ../../groups");
    exit();
}


// Commands
if (str_starts_with($_POST["message"], "!")) {

    if ($group["author"] != $_SESSION["id"]) {
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=authfailed");
        exit();
    }

    if (str_starts_with($_POST["message"], "!kick")) {
        $target = getTable($conn, "users", ["uid", explode("!kick ", $_POST["message"])[1]]);

        # Checking if user is valid.
        if ($target == null) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=usernotfound");
            exit();
        }
        $in = false;
        foreach (explode(",", $group["members"]) as $v) {
            if ($v == $target["id"]) {
                $in = true;
            }
        }
        if (!$in) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfkick");
            exit();
        }
        if ($target["id"] == $_SESSION["id"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=targetisimmune");
            exit();
        }

        $sql = "UPDATE groups SET members=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?error=stmtfailed");
            exit();
        }
        # --------------------------

        $newMembers = "";

        foreach (explode(",", $group["members"]) as $v) {
            if ($v != $target["id"]) $newMembers .= $v . ",";
        }

        $newMembers = substr($newMembers, 0, -1);
        
        mysqli_stmt_bind_param($stmt, "si", $newMembers, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);  

    }
    elseif (str_starts_with($_POST["message"], "!delete")) {
        
        if ($_POST["message"] != "!delete " . $group["name"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfdelete");
            exit();
        }

        $sql = "DELETE FROM groups WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?error=stmtfailed");
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "i", $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ../../groups?error=gcdone");
        exit();

    }
    elseif (str_starts_with($_POST["message"], "!help")) {
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gchelp");
        exit();
    }
    else {
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcnotfound");
        exit();
    }

    header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcdone");
    exit();
}


if (isset($_POST["replyid"])) {
    $sql = "INSERT INTO groupmessages(message, author, replyTo, groupId) VALUES (?, ?, ?, ?);";
} else {
    $sql = "INSERT INTO groupmessages(message, author, groupId) VALUES (?, ?, ?)";
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