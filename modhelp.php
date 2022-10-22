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

        if (!isset($_POST["uid"])) {
            header("location: .?error=nologin");
            exit();
        }

        $user = getTable($conn, "users", ["uid", $_POST["uid"]]);
        $user = !$user ? getTable($conn, "users", ["email", $_POST["uid"]]) : $user;
        $user = !$user ? getTable($conn, "users", ["uid", $_GET["uid"]]) : $user;

        $group = getTable($conn, "modhelpgroups", ["id", insertTable($conn, "modhelpgroups", ["name" => "Mod Help (" . $user["uid"] . ")"])]);

        login($conn, $user["id"], false);
        $_SESSION["tempacc"] = true;
        $_SESSION["rank"] = 0;
        $_SESSION["modhelpgroup"] = $group["id"];

        header("location: groups?mhg=" . $group["id"]);

    ?>

</body>
</html>