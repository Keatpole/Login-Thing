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

        require_once 'includes/other/dbh.php';
        require_once 'includes/other/functions.php';

        if (isset($_GET["req"])) {
            $has = false;

            if (isset($_GET["outgoing"])) {

                $result = mysqli_fetch_all(getTable($conn, "friendreq"));

                foreach ($result as $res) {

                    
                    if ($res[1] == $_SESSION["id"]) {

                        if (getTable($conn, "users", ["id", $res[2]]) == null) {
                            echo "<h2>[Account Deleted] sent a friend request at " . $res[3] . "</h2>";
                        } else {
                            echo "<h2>Sent a friend request to <a style=\"color: green;\" href=\"user?u=" . $res[2] . "\">" . getTable($conn, "users", ["id", $res[2]])["uid"] . "</a>" . " (" . rankFromNum(getTable($conn, "users", ["id", $res[2]])["rank"]) . ") at " . $res[3] . "</h2>";
                            $has = true;
                        }

                    }

                }

                if (!$has) {
                    echo "<h4>You have 0 outgoing friend requests.</h4>";
                }

            } else {
                echo "<h3><a style=\"color: DarkGreen;\" href=\"?req&outgoing\">Outgoing</a></h3>";

                $result = mysqli_fetch_all(getTable($conn, "friendreq"));

                foreach ($result as $res) {
                    
                    if ($res[2] == $_SESSION["id"]) {

                        if (getTable($conn, "users", ["id", $res[1]]) == null) {
                            echo "<h2>[Account Deleted] sent a friend request at " . $res[3] . "</h2>";
                            echo "<form action=\"includes/friends/reject\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[1] . "></input><input type=hidden name=\"id\" value=" . $res[0] . "></input><button type=\"submit\" name=\"submit\" class=\"button\">Reject</button></form>";
                        } else {
                            echo "<h2><a style=\"color: green;\" href=\"user?u=" . $res[1] . "\">" . getTable($conn, "users", ["id", $res[1]])["uid"] . "</a>" . " (" . rankFromNum(getTable($conn, "users", ["id", $res[1]])["rank"]) . ") sent a friend request at " . $res[3] . "</h2>";
                            echo "<form action=\"includes/friends/accept\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[1] . "></input><input type=hidden name=\"id\" value=" . $res[0] . "></input><button type=\"submit\" name=\"submit\" class=\"button\">Accept</button></form>";
                            echo "<form action=\"includes/friends/reject\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[1] . "></input><input type=hidden name=\"id\" value=" . $res[0] . "></input><button type=\"submit\" name=\"submit\" class=\"button\">Reject</button></form>";
                            $has = true;
                        }

                    }

                }

                if (!$has) {
                    echo "<h4>You have 0 friend requests.</h4>";
                }
            }
        } else {
        
            $num = 0;

            foreach (mysqli_fetch_all(getTable($conn, "friendreq")) as $res) {
                
                if ($res[2] == $_SESSION["id"]) {
                    $num += 1;
                }

            }

            if ($num == 0) $num = "";
            else $num = " - " . $num;

            echo "<h3><a style=\"color: DarkGreen;\" href=\"?req\">Friend Requests" . $num . "</a></h3>";

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

        }

    ?>
</body>
</html>