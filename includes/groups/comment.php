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
    if ($v == $_SESSION["id"] || $_SESSION["rank"] >= 2) {
        $access = true;
        break;
    }
}
if (!$access) {
    header("location: ../../groups");
    exit();
}

$mod = false;
foreach (explode(",", getTable($conn, "groups", ["id", $_POST["groupid"]])["mods"]) as $v) {
    if ($v == $_SESSION["id"]) {
        $mod = true;
        break;
    }
}

$message = strtolower($_POST["message"]);

// Commands
if (str_starts_with($message, "!")) {

    // User commands:
    if (str_starts_with($message, "!members")) {

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
    
        $users = "";
        foreach (explode(",", $group["members"]) as $v) {
            $user = getTable($conn, "users", ["id", $v]);
            if ($user && $user["id"] != $group["author"]) $users .= $user["uid"] . ", ";
        }
        $users = "Members: " . ($users == "" ? "None" : substr($users, 0, -2));    
        
        $message = htmlspecialchars($users, ENT_QUOTES, 'UTF-8');
        
        if (isset($_POST["replyid"])) {
            mysqli_stmt_bind_param($stmt, "ssss", $message, $_SESSION["id"], $_POST["replyid"], $_POST["groupid"]);
        } else {
            mysqli_stmt_bind_param($stmt, "sss", $message, $_SESSION["id"], $_POST["groupid"]);
        }
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=none");
        exit();
    
    }
    elseif (str_starts_with($message, "!mods")) {

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
    
        $users = "";
        foreach (explode(",", $group["mods"]) as $v) {
            $user = getTable($conn, "users", ["id", $v]);
            if ($user && $user["id"] != $group["author"]) $users .= $user["uid"] . ", ";
        }
        $users = "Moderators: " . ($users == "" ? "None" : substr($users, 0, -2));    
        
        $message = htmlspecialchars($users, ENT_QUOTES, 'UTF-8');
        
        if (isset($_POST["replyid"])) {
            mysqli_stmt_bind_param($stmt, "ssss", $message, $_SESSION["id"], $_POST["replyid"], $_POST["groupid"]);
        } else {
            mysqli_stmt_bind_param($stmt, "sss", $message, $_SESSION["id"], $_POST["groupid"]);
        }
        
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=none");
        exit();
    
    }
    elseif (str_starts_with($message, "!leave")) {

        if ($_SESSION["id"] == $group["author"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfleaveauthor");
            exit();
        }

        $sql = "UPDATE groups SET members=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }

        $newMembers = "";

        foreach (explode(",", $group["members"]) as $v) {
            if ($v != $_SESSION["id"]) $newMembers .= $v . ",";
        }

        $newMembers = substr($newMembers, 0, -1);
        
        mysqli_stmt_bind_param($stmt, "si", $newMembers, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "UPDATE groups SET mods=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }

        $newMods = "";

        foreach (explode(",", $group["mods"]) as $v) {
            if ($v != $_SESSION["id"]) $newMods .= $v . ",";
        }

        $newMods = substr($newMods, 0, -1);
        
        mysqli_stmt_bind_param($stmt, "si", $newMods, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);  

    }
    elseif (str_starts_with($message, "!help")) {
        if ($_SESSION["id"] == $group["author"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gchelpauthor");
            exit();
        }
        elseif ($mod) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gchelpstaff");
            exit();
        }
        else {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gchelpmember");
            exit();
        }
    }

    $rank = "Member";

    // Admin commands
    if ($group["author"] == $_SESSION["id"] || $_SESSION["rank"] >= 3) {
        $rank = "Author";
    }
    elseif ($mod) {
        $rank = "Mod";
    }

    if ($rank == "Member") {
        header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfauthfailed");
        exit();
    }

    if (str_starts_with($message, "!add") && $rank == "Author") {

        $target = getTable($conn, "users", ["uid", explode("!add ", $message)[1]]);

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
        if ($in || $target["id"] == $group["author"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfaddin");
            exit();
        }

        $exit = true;

        foreach (getTable($conn, "friends", "", True) as $v) {
            if ($v["user1"] == $target["id"] && $v["user2"] == $_SESSION["id"] || $v["user2"] == $target["id"] && $v["user1"] == $_SESSION["id"] || $_SESSION["rank"] > 2) {
                $exit = false;
                break;
            }
        }

        if ($exit) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfaddfriend");
            exit();
        }
        # --------------------------

        $sql = "UPDATE groups SET members=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }

        $newMembers = $group["members"] . "," . $target["id"];

        mysqli_stmt_bind_param($stmt, "si", $newMembers, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
    }
    elseif (str_starts_with($message, "!mod") && $rank == "Author") {

        $target = getTable($conn, "users", ["uid", explode("!mod ", $message)[1]]);

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
        if (!$in || $target["id"] == $group["author"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfmodin");
            exit();
        }
        $in = false;
        foreach (explode(",", $group["mods"]) as $v) {
            if ($v == $target["id"]) {
                $in = true;
            }
        }
        if ($in) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfmodin");
            exit();
        }
        # --------------------------

        $sql = "UPDATE groups SET mods=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }

        $newMods = $group["mods"] . "," . $target["id"];

        mysqli_stmt_bind_param($stmt, "si", $newMods, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
    }
    elseif (str_starts_with($message, "!unmod") && $rank == "Author") {
        $target = getTable($conn, "users", ["uid", explode("!unmod ", $message)[1]]);

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
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfaddin");
            exit();
        }
        $in = false;
        foreach (explode(",", $group["mods"]) as $v) {
            if ($v == $target["id"]) {
                $in = true;
            }
        }
        if (!$in) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfmodin");
            exit();
        }
        if ($target["id"] == $group["author"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=targetisimmune");
            exit();
        }
        # --------------------------

        $sql = "UPDATE groups SET mods=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }

        $newMods = "";

        foreach (explode(",", $group["mods"]) as $v) {
            if ($v != $target["id"]) $newMods .= $v . ",";
        }

        $newMods = substr($newMods, 0, -1);
        
        mysqli_stmt_bind_param($stmt, "si", $newMods, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);  

    }
    elseif (str_starts_with($message, "!kick")) {
        $target = getTable($conn, "users", ["uid", explode("!kick ", $message)[1]]);

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
        if ($target["id"] == $group["author"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=targetisimmune");
            exit();
        }
        # --------------------------

        $sql = "UPDATE groups SET members=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }

        $newMembers = "";

        foreach (explode(",", $group["members"]) as $v) {
            if ($v != $target["id"]) $newMembers .= $v . ",";
        }

        $newMembers = substr($newMembers, 0, -1);
        
        mysqli_stmt_bind_param($stmt, "si", $newMembers, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "UPDATE groups SET mods=? WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }

        $newMods = "";

        foreach (explode(",", $group["mods"]) as $v) {
            if ($v != $target["id"]) $newMods .= $v . ",";
        }

        $newMods = substr($newMods, 0, -1);
        
        mysqli_stmt_bind_param($stmt, "si", $newMods, $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    }
    elseif (str_starts_with($message, "!delete") && $rank == "Author") {
        
        if ($_POST["message"] != "!delete " . $group["name"]) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=gcfdelete");
            exit();
        }

        $sql = "DELETE FROM groups WHERE id=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "i", $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM groupmessages WHERE groupId=?;";

        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("location: ../../groups?g=" . $_POST["groupid"] . "&error=stmtfailed");
            exit();
        }
        
        mysqli_stmt_bind_param($stmt, "i", $_POST["groupid"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("location: ../../groups?error=gcdone");
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