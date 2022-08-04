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
    
    <h2>Reset Password / Username</h2>

    <?php
        if (!$settings->enable_reset_pass) {
            echo "<p>Resetting your password is temporarily disabled.</p>";
        } else {
            ?>
                <form action="modhelp" method="post">
                    <input type="text" name="uid" placeholder="Username/Email..."></br></br>
                    <button type="submit" name="submit" class="button">Reset</button>
                </form>
            <?php
        }
        ?>
</body>
</html>