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

        $currentUser = null;

        if (!isset($_GET["u"])) {

            $currentUser = $_SESSION["id"];

            $user = getTable($conn, "users", ["uid", $_SESSION["uid"]]);

            $verified = ($user["verified"] ? "<p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p>" : "");

            echo "<h1>" . $user["uid"] . $verified . "'s Profile</h1>";
            echo "<h4>Rank: " . rankFromNum($_SESSION["rank"]) . "</h4>";

            $friends = 0;
            foreach (mysqli_fetch_all(getTable($conn, "friends")) as $res) {
                if ($res[1] == $_SESSION["id"] || $res[2] == $_SESSION["id"]) {
                    $friends += 1;
                }
            }
            echo "<h4>Friends: " . strval($friends) . "</h4>";
            
            if (isset($_GET["deleteConfirm"])) {
                echo "<a href='includes/account/delete'><button class='button'>Confirm Account Deletion</button></a>";
            } else {
                echo "<a href='?deleteConfirm'><button class='button'>Delete Account</button></a>";
            }
        } else {
            if ($_GET["u"] == $_SESSION["id"]) {
                $sb = "";
                foreach ($_GET as $key => $value) {
                    if ($key != "u" && $key != "webname") {
                        $sb .= $key . "=" . $value;
                    }
                }
                if ($sb != "") {
                    header("location: user?" . $sb);
                } else {
                    header("location: user");
                }
                exit();
            }

            $currentUser = $_GET["u"];

            if (getTable($conn, "users", ["id", $_GET["u"]]) == null) {
                header("location: user?error=usernotfound");
                exit();
            }

            $user = getTable($conn, "users", ["id", $_GET["u"]]);

            $verified = ($user["verified"] ? "<p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p>" : "");

            echo "<h1>" . $user["uid"] . $verified . "'s Profile</h1>";
            echo "<h4>Rank: " . rankFromNum($user["rank"]) . "</h4>";

            $btnId = null;
            $btnShow = true;
            $type = null;
            $sentReq = null;

            $friends = 0;

            foreach (mysqli_fetch_all(getTable($conn, "friendreq")) as $res) {
                if ($res[1] == $_SESSION["id"]) {
                    if ($res[1] == $_GET["u"] || $res[2] == $_GET["u"]) {
                        $btnId = $res[0];
                        $type = "req";
                    }
                }
                if ($res[2] == $_SESSION["id"]) {
                    $btnShow = false;
                }
                if ($res[1] == $_GET["u"]) {
                    $sentReq = $res;
                }
            }
            foreach (mysqli_fetch_all(getTable($conn, "friends")) as $res) {
                if ($res[1] == $_SESSION["id"] || $res[2] == $_SESSION["id"]) {
                    if ($res[1] == $_GET["u"] || $res[2] == $_GET["u"]) {
                        $btnId = $res[0];
                        $type = "friend";
                        
                    }
                }
                if ($res[1] == $_GET["u"] || $res[2] == $_GET["u"]) {
                    $friends += 1;
                }
            }

            echo "<h4>Friends: " . strval($friends) . "</h4>";
            

            if ($btnShow) {
                if (!$btnId) {
                    if ($settings->enable_friends) {
                        echo "<form action=\"includes/friends/addFriend\" method=\"post\"><input type=\"hidden\" name=\"user\" value=\"" . $_GET["u"] . "\"><button type=\"submit\" name=\"submit\" class=\"button\">Friend</button></form>";
                    } else {
                        echo "<p>Sending friend requests is temporarily disabled.</p>";
                    }
                } else {
                    echo "<button name=\"submit\" class=\"button\"><a style=\"color: white; text-decoration: none;\" href=\"includes/friends/remFriend?u=" . $_GET["u"] . "&i=" . $btnId ."&t=" . $type . "\">Remove Friend</a></button></br>";
                }
            }
            elseif ($sentReq != null) {
                echo "<h3>This user sent a friend request at " . $res[3] . "</h3>";
                echo "<form action=\"includes/friends/acceptFriend\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[1] . "></input><input type=hidden name=\"return\" value=\"user?u=" . $_GET["u"] . "&\"><input type=hidden name=\"id\" value=" . $res[0] . "></input><button type=\"submit\" name=\"submit\" class=\"button\">Accept</button></form>";
                echo "<form action=\"includes/friends/rejectFriend\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[1] . "></input><input type=hidden name=\"id\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\"user?u=" . $_GET["u"] . "&\"><button type=\"submit\" name=\"submit\" class=\"button\">Reject</button></form></br>";
            }

            if ($type == "friend") {
                echo "</br><a href=\"groups?u=" . $_GET["u"] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Group</a></br>";
            }

            if ($settings->enable_report) {
                echo "</br><a href=\"report?u=" . $_GET["u"] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Report</a>";
            } else {
                echo "<p>Reporting is temporarily disabled.</p>";
            }

        }

        echo "<h1>Comments:</h1>";

        $_GET["includefromprofile"] = $currentUser;
        include_once "index.php";
            
    ?>
        
</body>
</html>