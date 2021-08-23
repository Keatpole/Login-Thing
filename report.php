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
        
    ?>
    <h1>Report</h1>

    <?php

        if ($settings->enable_report) {
            ?>

                <form action="includes/account/report" method="post">
                    <?php
                        if (isset($_GET["u"])) {
                            echo "<input type=\"text\" name=\"user\" value=\"" . getTable($conn, "users", ["id", $_GET["u"]])["uid"] . "\"></br></br>";
                        } else {
                            echo "<input type=\"text\" name=\"user\" placeholder=\"Username...\"></br></br>";
                        }
                    ?>
                    <select name="reason" size="7">
                        <option value="0">Username</option>
                        <option value="1">Harassment</option>
                        <option value="2">Impersonation</option>
                        <option value="3">Threats</option>
                        <option value="4">Spam</option>
                        <option value="5">Scam</option>
                        <option value="9">Other</option>
                    </select></br></br>
                    <textarea name="otherreason" placeholder='If you chose "Other" or any comments'></textarea>
                    </br><button type="submit" name="submit" class="button">Confirm</button>
                </form>

            <?php
        } else {
            echo "<p>Reporting is temporarily disabled.</p>";
        }

    ?>
</body>
</html>