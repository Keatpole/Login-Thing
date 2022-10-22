<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php
        $_GET["webname"] = "comments/" . basename(__FILE__, '.php');
        include_once $_SERVER["DOCUMENT_ROOT"] . "/" . explode("/", $_SERVER["SCRIPT_NAME"])[1] . "/header.php"; # wtf??

        // Python: if __name__ == '__main__': 
        if (!count(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))) {
            header("location: ../.?error=404");
        }

        if (!isset($_SESSION["id"])) {
            header("location: ../.?error=404");
        }

        $exit = true;

        foreach (getTable($conn, "friends", "", True) as $v) {
            if ($v["user1"] == $_GET["u"] && $v["user2"] == $_SESSION["id"] || $v["user2"] == $_GET["u"] && $v["user1"] == $_SESSION["id"] || $_SESSION["rank"] > 2) {
                $exit = false;
                break;
            }
        }

        if ($exit) {
            header("location: ../.?error=notfriend");
            exit();
        }

        $pming = getTable($conn, "users", ["id", $_GET["u"]]);

        echo "<h4 class='center'>You're private messaging " . htmlspecialchars_decode($pming["uid"], ENT_QUOTES) . "</h4>";

        if ($settings->enable_posting_comments) {


            ?>

            <form action="includes/pm/comment" method="post">

                <?php

                    echo "<input name=\"user\" type=hidden value=\"" . htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") . "\" />";
                    echo "<input name=\"message\" id=\"inputmsg\" style=\"line-height: 3.3em\" placeholder=\"What's on your mind?\" />";


                ?>

                <button class="button" type="submit" name="submit">Post</button>

            </form>

            <?php


        }
        else {
            echo "<p>Posting comments is temporarily disabled.</p>";
        }
        
        if ($settings->enable_viewing_comments) {

            echo "</br></br><div class=\"comments\">";

            $has = false;

            foreach (mysqli_fetch_all(getTable($conn, "privatemessages", "", true)) as $res) {                
                if ($res[2] != $_GET["u"]) continue;
                else $has = true;
            }

            if ($has) {
                echo "<h2>Comments:</h2>";
            }

            $hasreply = false;

            foreach (mysqli_fetch_all(getTable($conn, "privatemessages", "", true)) as $res) {
                if ($res[3] == $_SESSION["id"] && $res[2] == $_GET["u"] || $res[3] == $_GET["u"] && $res[2] == $_SESSION["id"]) {}
                else continue;

                $contin = true;

                if (isset($_GET["hashtag"])) {    
                    $contin = false;

                    foreach (explode(" ", $res[1]) as $v) {
                        if (strtolower(urldecode($_GET["hashtag"])) == strtolower(substr($v, 1))) {
                            $contin = true;
                        }
                    }
                }

                if (isset($_GET["mentions"])) {    
                    $contin = false;

                    foreach (explode(" ", $res[1]) as $v) {
                        if (strtolower(urldecode($_GET["mentions"])) == strtolower(substr($v, 1))) {
                            $contin = true;
                        }
                    }
                }

                if (!$contin) continue;

                echo "<div id=" . $res[0] . " class=\"comment\" tabindex=\"-1\">";

                $has = true;

                if (getTable($conn, "users", ["id", $res[2]]) == null) {
                    if ($_SESSION["rank"] > 0) {
                        echo "<h2>[" . $res[0] . "] [Account Deleted]:</h2>";
                    } else {
                        echo "<h2>[Account Deleted]:</h2>";
                    }
                } else {
                    $user = getTable($conn, "users", ["id", $res[2]]);

                    $muted = "";

                    foreach (getTable($conn, "mutes", "", true) as $v) {
                        if ($v["target"] == $user["id"]) $muted = " (Muted)";
                    }

                    $banned = "";

                    foreach (getTable($conn, "bans", "", true) as $v) {
                        if ($v["target"] == $user["id"]) $banned = " (Banned)";
                    }

                    if ($_SESSION["rank"] < 1) $muted = "";

                    $verified = ($user["verified"] ? "<p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p>" : "");
                    $commentId = ($_SESSION["rank"] > 0 ? "[" . $res[0] . "] " : "");
                    $admin = ($user["rank"] >= 1 ? " <img src=\"img/admin.png\" alt=\"" . rankFromNum($user["rank"]) . "\" title=\"" . rankFromNum($user["rank"]) . "\">" : "");
                    $isyou = ($_SESSION["id"] == $res[2] ? " (You)" : "");

                    echo "<h2>" . $commentId . "<a style=\"color: green;\" href=\"user?u=" . $res[2] . "\">" . $user["uid"] . "</a>" . $admin . $verified . $muted . $banned . $isyou . ":</h2>";
                
                }
                                
                $result = "";
                foreach (explode(" ", $res[1]) as $v) {
                    if (str_starts_with($v, "@")) {
                        $user = getTable($conn, "users", ["uid", substr($v, 1)]);
                        if ($user) {
                            $result .= "<a style=\"color: green;\" href=\"user?u=" . $user["id"] . "\">" . $v . "</a>";
                        } else {
                            $result .= $v;
                        }
                    }
                    elseif (str_starts_with($v, "#")) {
                        $result .= "<a style=\"color: red;\" href=\"?u=" . $_GET["u"] . "&hashtag=" . urlencode(strtolower(substr($v, 1))) . "\">" . $v . "</a>";
                    }
                    else {
                        $result .= $v;
                    }
                    $result .= " ";
                }

                $replyTo = "";

                echo "<p>" . $replyTo . htmlspecialchars_decode($result, ENT_QUOTES) . "</p>";

                if ($_SESSION["id"] == $res[2] || $_SESSION["rank"] >= 2) echo "<form action=\"includes/pm/deleteComment\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[3] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><button type=\"submit\" name=\"delete\" class=\"button\">Delete</button></form>";
                else echo "<form action=\"includes/pm/deleteComment\" method=\"post\"><input type=hidden name=\"user\" value=" . $res[3] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input></form>";

                echo "</div>";

            }

            if (!$has) {                
                echo "There are no comments.";
            }

            echo "</div>";

        } else {
            echo "<p>Viewing comments is temporarily disabled.</p>";
        }
    ?>
</body>
</html>