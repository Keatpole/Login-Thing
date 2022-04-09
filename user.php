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

            echo "<h1>" . $user["uid"] . "'s Profile</h1>";
            echo "<h4>Rank: " . rankFromNum($_SESSION["rank"]) . "</h4>";

            $friends = 0;
            foreach (mysqli_fetch_all(getTable($conn, "friends")) as $res) {
                if ($res[1] == $_SESSION["id"] || $res[2] == $_SESSION["id"]) {
                    if (getTable($conn, "users", ["id", $res[1]]) != null && getTable($conn, "users", ["id", $res[2]]) != null) {
                        $friends++;
                    }
                }
            }
            echo "<h4>Friends: " . strval($friends) . "</h4>";
            
            echo "<a href=\".?mentions=" . $user["uid"] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Mentions</a> ";

            if (isset($_GET["deleteConfirm"])) {
                echo "<a href='includes/account/delete'><button class='button'>Confirm Account Deletion</button></a> ";
            } else {
                echo "<a href='?deleteConfirm'><button class='button'>Delete Account</button></a> ";
            }

            echo "<a href=\"appeal\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Appeal</a> ";

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

            $user = getTable($conn, "users", ["id", $_GET["u"]]);

            if ($user == null) {
                header("location: user?error=usernotfound");
                exit();
            }

            $verified = ($user["verified"] ? "<p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p>" : "");

            echo "<h1>" . htmlspecialchars($user["uid"] . $verified, ENT_QUOTES, "UTF-8") . "'s Profile</h1>";
            echo "<h4>Rank: " . rankFromNum(htmlspecialchars($user["rank"], ENT_QUOTES, "UTF-8")) . "</h4>";

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
                if ($res[2] == $_SESSION["id"] && $res[1] == $_GET["u"]) {
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
                        echo "<form action=\"includes/friends/add\" method=\"post\"><input type=\"hidden\" name=\"user\" value=\"" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "\"><button type=\"submit\" name=\"submit\" class=\"button\">Friend</button></form> ";
                    } else {
                        echo "<p>Sending friend requests is temporarily disabled.</p>";
                    }
                } else {
                    echo "<button name=\"submit\" class=\"button\"><a style=\"color: white; text-decoration: none;\" href=\"includes/friends/remove?u=" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "&i=" . $btnId ."&t=" . $type . "\">Remove Friend</a></button> ";
                }
            }
            elseif ($sentReq != null) {
                echo "<h3>This user sent a friend request at " . $res[3] . "</h3>";
                echo "<form action=\"includes/friends/accept\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[1] . "></input><input type=hidden name=\"return\" value=\"user?u=" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "&\"><input type=hidden name=\"id\" value=" . $res[0] . "></input><button type=\"submit\" name=\"submit\" class=\"button\">Accept</button></form> ";
                echo "<form action=\"includes/friends/reject\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[1] . "></input><input type=hidden name=\"id\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\"user?u=" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "&\"><button type=\"submit\" name=\"submit\" class=\"button\">Reject</button></form> ";
            }

            if ($type == "friend") {
                echo "<a href=\"pm?u=" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">PM</a> ";
                echo "<a href=\"groups?u=" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Group</a> ";
            }

            if ($settings->enable_report) {
                echo "<a href=\"report?u=" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Report</a> ";
            } else {
                echo "<p>Reporting is temporarily disabled.</p>";
            }

            echo "<a href=\".?mentions=" . getTable($conn, "users", ["id", htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8")])["uid"] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Mentions</a>";

        }

        #echo "<h1>Comments:</h1>";

        $_GET["includefromprofile"] = $currentUser;
        include_once "comments.php";
            
    ?>
        
</body>
</html>