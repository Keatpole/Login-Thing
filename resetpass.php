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

        if (isset($_GET["t"]) && password_verify($_GET["t"], getTable($conn, "passwordtokens", ["id", $_SESSION["ptid"]])["token"])) {
            if (isset($_GET["invalidate"])) {
                deleteTable($conn, "passwordtokens", ["id", $_SESSION["ptid"]]);
                $_SESSION["ptid"] = null;
                header("location: login?error=none");
                exit();
            }
        }
        else {
            header("location: login?error=invalidtoken");
            exit();
        }
        
    ?>
    
    <h2>Reset Password</h2>

    <?php
        if (!$settings->enable_reset_pass) {
            echo "<p>Resetting your password is temporarily disabled.</p>";
        } else {
            ?>
                <form action="includes/account/resetPassword" method="post">
                    <?php
                        echo "<input type=\"hidden\" name=\"token\" value=\"" . htmlspecialchars($_GET["t"], ENT_QUOTES, "UTF-8") . "\">";
                    ?>
                    <input type="password" name="pwd" placeholder="New Password..."></br>
                    <input type="password" name="pwdRepeat" placeholder="Repeat Password..."></br></br>
                    <button type="submit" name="submit" class="button">Reset</button>
                </form>
                </br></br>
                <h2>Change Username</h2>
                <form action="includes/account/changeUsername" method="post">
                    <?php
                        echo "<input type=\"hidden\" name=\"token\" value=\"" . htmlspecialchars($_GET["t"], ENT_QUOTES, "UTF-8") . "\">";
                    ?>
                    <input name="uid" placeholder="New Username..."></br>
                    <input name="uidRepeat" placeholder="Repeat Username..."></br></br>
                    <button type="submit" name="submit" class="button">Change</button>
                </form>
                </br></br>
                <?php
                echo "<a href=\"?t=" . htmlspecialchars($_GET["t"], ENT_QUOTES, "UTF-8") . "&invalidate\" style=\"color: green;\">Invalidate Token</a>";
                ?>
            <?php
        }
    ?>
</body>
</html>