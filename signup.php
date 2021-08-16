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
    ?>

    <h2>Sign Up</h2>

    <?php

        if (!$settings->enable_signup) {
            echo "<p>Signing up is temporarily disabled.</p>";
            exit();
        }

    ?>

    <form action="includes/account/signup" method="post">
        <input type="text" name="uid" placeholder="Username..."></br>
        <input type="text" name="email" placeholder="Email..."></br>
        <input type="password" name="pwd" placeholder="Password..."></br>
        <input type="password" name="pwdrepeat" placeholder="Confirm Password..."></br></br>
        <button type="submit" name="submit" class="button">Sign Up</button>
    </form>

</body>
</html>