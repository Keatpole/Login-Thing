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
    
    <h2>Log In</h2>

    <?php
        if (!$settings->enable_login) {
            echo "<p>Login is temporarily disabled.</p>";
        } else {
            ?>
            <form action="includes/account/login" method="post">
                <input type="text" name="uid" placeholder="Username/Email..."></br>
                <input type="password" name="pwd" placeholder="Password..."></br></br>
                <span>Remember Me</span> <input type="checkbox" name="remember">
                </br></br><button type="submit" name="submit" class="button">Log In</button>
            </form>
            <?php
        }

        if (!$settings->enable_reset_pass) {
            echo "<p>Resetting your password is temporarily disabled.<p>";
        } else {
            ?>
                </br><a href="forgotpass" style="color: #007FFF;">Forgot your password / username?</a>
            <?php
        }
    ?>
</body>
</html>