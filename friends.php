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

        if (isset($_SESSION["uid"])) {

            require_once 'includes/other/dbh.php';
            require_once 'includes/other/functions.php';
            
            $num = 0;

            foreach (mysqli_fetch_all(getTable($conn, "friendreq")) as $res) {
                
                if ($res[2] == $_SESSION["id"]) {
                    $num += 1;
                }

            }

            if ($num == 0) $num = "";
            else $num = " - " . $num;

            echo "<h3><a style=\"color: DarkGreen;\" href=\"friendreq\">Friend Requests" . $num . "</a></h3>";

            $has = false;

            foreach (mysqli_fetch_all(getTable($conn, "friends")) as $res) {

                if ($res[1] == $_SESSION["id"]) {

                    $user = getTable($conn, "users", ["id", $res[2]]);

                    if ($user["uid"] == null) {
                        echo "<h2><a style=\"color: black;\" href=\"includes/friends/remove?u=" . $res[2] . "&i=" . $res[0] ."&t=friend&return=friends\">[Account Deleted]</a></h2>";
                    } else {

                        if ($user["verified"] == 0) {
                            echo "<h2><a style=\"color: green;\" href=\"user?u=" . $res[2] . "\">" . $user["uid"] . "</a></h2>";
                        }
                        elseif ($user["verified"] == 1) {
                            echo "<h2><a style=\"color: green;\" href=\"user?u=" . $res[2] . "\">" . $user["uid"] . "</a><p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p></h2>";
                        }
                        $has = true;

                    }

                }
                elseif ($res[2] == $_SESSION["id"]) {

                    $user = getTable($conn, "users", ["id", $res[1]]);

                    if ($user["uid"] == null) {
                        echo "<h2><a style=\"color: black;\" href=\"includes/friends/remove?u=" . $res[1] . "&i=" . $res[0] ."&t=friend&return=friends\">[Account Deleted]</a></h2>";
                    } else {
                        if ($user["verified"] == 0) {
                            echo "<h2><a style=\"color: green;\" href=\"user?u=" . $res[1] . "\">" . $user["uid"] . "</a></h2>";
                        }
                        elseif ($user["verified"] == 1) {
                            echo "<h2><a style=\"color: green;\" href=\"user?u=" . $res[1] . "\">" . $user["uid"] . "</a><p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p></h2>";
                        }
                        $has = true;
                    }

                }

            }

            if (!$has) {
                echo "<h4>You have no friends :(</h4>";
            }

        } else {
            header("location: .?error=nologin");
            exit();
        }
    ?>
</body>
</html>