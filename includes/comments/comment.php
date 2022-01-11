<?php

session_start();

require_once '../other/functions.php';
require_once '../other/dbh.php';

if (!isset($_SESSION["rank"]) || !isset($_POST["submit"]) || !$settings->enable_posting_comments) {
    header("location: ../../.");
    exit();
}

foreach (getTable($conn, "mutes", "", true) as $v) {
    if ($v["target"] == $_SESSION["id"]) {
        header("location: ../../.");
        exit();
    }
}

if (isset($_POST["replyid"])) {
    $sql = "INSERT INTO messages(message, author, replyTo) VALUES (?, ?, ?)";
} else {
    $sql = "INSERT INTO messages(message, author) VALUES (?, ?)";
}

$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("location: ../../.?error=stmtfailed");
    exit();
}

session_start();

$message = htmlspecialchars($_POST["message"], ENT_QUOTES, 'UTF-8');

# if the message starts with a !, it's a command
if (str_starts_with($message, "!")) {
    $message = substr($message, 1);
    $message = explode(" ", $message);
    $command = strtolower($message[0]);
    $message = implode(" ", array_slice($message, 1));

    # "user" command sends the user to "../../user?u=<username>"
    if ($command == "user") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../user?u=" . $result["id"]);
        exit();

    }

    # "setuser" command sends the user to "../../moderation?u=<username>&sel=user"
    if ($command == "setuser") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=user");
        exit();

    }

    # "setmod" command sends the user to "../../moderation?u=<username>&sel=mod"
    if ($command == "setmod") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=mod");
        exit();

    }

    # "setadmin" command sends the user to "../../moderation?u=<username>&sel=admin"
    if ($command == "setadmin") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=admin");
        exit();

    }

    # "mute" command sends the user to "../../moderation?u=<username>&sel=unm"
    if ($command == "mute" || $command == "unmute") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=unm");
        exit();

    }

    # "delete" command sends the user to "../../moderation?u=<comment>&sel=delc"
    if ($command == "delete") {
        $result = getTable($conn, "messages", ["id", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=delc");
        exit();

    }

    # "ban" command sends the user to "../../moderation?u=<username>&sel=unb"
    if ($command == "ban" || $command == "unban") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=unb");
        exit();

    }

    # "undelete" command sends the user to "../../moderation?u=<username>&sel=undel"
    if ($command == "undelete") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=undel");
        exit();

    }

    # "verify" command sends the user to "../../moderation?u=<username>&sel=unv"
    if ($command == "verify" || $command == "unverify") {
        $result = getTable($conn, "users", ["uid", $message]);

        if ($result === null) {
            header("location: ../../.?error=usernotfound");
            exit();
        }

        header("location: ../../moderation?u=" . $result["uid"] . "&sel=unv");
        exit();

    }

    header("location: ../../.?error=none");
    exit();

}

if (isset($_POST["replyid"])) {
    mysqli_stmt_bind_param($stmt, "sis", $message, $_SESSION["id"], $_POST["replyid"]);
} else {
    mysqli_stmt_bind_param($stmt, "si", $message, $_SESSION["id"]);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$return = (isset($_POST["return"]) ? $_POST["return"] : ".?");

header("location: ../../" . $return . "error=none");
exit();