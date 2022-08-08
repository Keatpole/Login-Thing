<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php

    $_GET["webname"] = basename(__FILE__, '.php');
    include_once 'header.php';

    if (!isset($_SESSION["id"])) {
        header("location: .?error=nologin");
        exit();
    }

    if (isset($_GET["u"])) {

        $exit = true;

        foreach (getTable($conn, "friends", "", True) as $v) {
            if ($v["user1"] == $_GET["u"] && $v["user2"] == $_SESSION["id"] || $v["user2"] == $_GET["u"] && $v["user1"] == $_SESSION["id"] || $_SESSION["rank"] > 2) {
                $exit = false;
                break;
            }
        }

        if ($exit) {
            header("location: .?error=notfriend");
            exit();
        }

        if (getTable($conn, "users", ["id", $_GET["u"]]) == null || $_GET["u"] == $_SESSION["id"]) {
            header("location: .?error=usernotfound");
            exit();
        }

        include_once "comments/pm.php";

    } else {
        header("location: .?error=usernotfound");
        exit();
    }

    ?>

</body>
</html>