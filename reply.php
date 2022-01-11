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

        if (!isset($_GET["c"])) {
            header("location: .?error=commentnotfound");
            exit();
        }

        $comment = getTable($conn, "messages", ["id", $_GET["c"]]);

        if ($comment == null) {
            header("location: .?error=commentnotfound");
            exit();
        }

        $_GET["specific"] = $_GET["c"];
        include_once 'comments.php';
    ?>
</body>
</html>