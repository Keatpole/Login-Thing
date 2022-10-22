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

        if (isset($_SESSION["accessverify"])) {
            # password_hash($user["uid"] . "doasij" . $user["rank"] * 3 . $user["rank"] / 1.2 . $user["verified"] + 1 * 11 . $i * 432 .  $i / 3.14, PASSWORD_DEFAULT);
            if (!isset($_GET["u"])) header("location: ./?error=invaliduid");

            $user = getTable($conn, "users", ["id", $_GET["u"]]);

            $hash = "uhusifhoasuif9hfuio43289fhwudiwa" . strtolower($user["uid"]) . "dojkaflhs7ag8hfduaasij" . $user["rank"] * 3 . $user["rank"] / 1.2 . $user["verified"] + 1 * 11 . $user["id"] + $user["rank"] / ($user["verified"] + $user["id"]) * 92 . $user["id"] / 3.14;

            if (!password_verify($hash, $_SESSION["accessverify"])) {
                header("location: ./?error=authfailed");
                exit();
            }
        } else {
            header("location: ./?error=authfailed");
            exit();
        }
    ?>
    <h1>Answer Security Codes</h1>

    <form action="includes/account/login" method="post">
            <?php

            $i = 0;
            foreach (explode(",", getTable($conn, "securitycodes", ["uuid", $_GET["u"]])["questions"]) as $v) {
                $i++;
                $v = $v[-1] == "?" ? $v : $v . "?";

                ?>
                    <h3><?=$v?></h3>
                    <textarea name="a<?=$i?>" placeholder='Answer...'></textarea>

                <?php
            }
            
            ?>
        </br></br><input type="password" name="pwd" placeholder="Password...">
        </br><button type="submit" name="submit" class="button">Submit</button>
        <input type="hidden" name="uid" value="<?=getTable($conn, "users", ["id", $_GET["u"]])["uid"]?>">
    </form>

</body>
</html>