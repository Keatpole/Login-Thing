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

        if (isset($_SESSION["id"])) {
            echo "<h4 class='center'>Welcome back, " . $_SESSION["uid"] . "</h4>";
            
            include_once "comments/default.php";

        } else {
            ?>
            
                <h1 class='center'>Welcome</h1></br>
                <p class='center'>Make an <a href='signup' style='color: darkblue; text-decoration: none;'>account</a> to access more <a href='features' style='color: darkblue; text-decoration: none;'>features</a>.</p>

            <?php
        }
    ?>
</body>
</html>