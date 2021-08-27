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

        if (!isset($_SESSION["uid"])) {
            header("location: .?error=nologin");
            exit();
        }
    ?>

    <h1>Search for User</h1>
    <form action="includes/account/search" method="post">
        <input type="text" name="username" placeholder="Username / ID..."></br></br>
        <button type="submit" name="submit" class="button">Search</button>
    </form>
</body>
</html>