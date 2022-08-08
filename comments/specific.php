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

        if (!isset($_SESSION["uid"])) {
            header("location: ../.?error=404");
        }

        echo "<h4 class='center'>You're viewing a comment</h4>";

        if ($settings->enable_posting_comments) {


            ?>

                <form action="includes/comments/comment" method="post">
                    <input name="message" id="inputmsg" style="line-height: 3.3em" placeholder="Reply..." />
                    <input type="hidden" name="replyid" value="<?= htmlspecialchars($_GET["c"], ENT_QUOTES, "UTF-8") ?>">
                    <input type="hidden" name="return" value="reply?c=<?= htmlspecialchars($_GET["c"], ENT_QUOTES, "UTF-8") ?>#<?= htmlspecialchars($_GET["c"], ENT_QUOTES, "UTF-8") ?>&">
                    <button class="button" type="submit" name="submit">Post</button>
                </form>

            <?php


        }
        else {
            echo "<p>Posting comments is temporarily disabled.</p>";
        }

        if (!$settings->enable_likes) {
            echo "<p>Likes are temporarily disabled.</p>";
        }
        
        if ($settings->enable_viewing_comments) {

            echo "</br></br><div class=\"comments\">";

            $has = false;

            $hasreply = false;

            foreach (mysqli_fetch_all(getTable($conn, "messages")) as $res) {
                if ($res[4] != "0") {
                    $hasreply = true;
                    break;
                }
            }

            foreach (mysqli_fetch_all(getTable($conn, "messages")) as $res) {
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

                if ($res[0] != $_GET["c"] && $res[4] != $_GET["c"]) {
                    $contin = false;
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
                        $result .= "<a style=\"color: red;\" href=\"?hashtag=" . urlencode(strtolower(substr($v, 1))) . "\">" . $v . "</a>";
                    }
                    else {
                        $result .= $v;
                    }
                    $result .= " ";
                }

                $replyTo = "";

                echo "<p>" . $replyTo . htmlspecialchars_decode($result, ENT_QUOTES) . "</p>";

                echo "</div>";

                if ($res[0] == $_GET["c"] && $hasreply) {
                    echo "<hr style=\"border: 1px dotted black;\">";
                }

            }

            if (!$has) {
                echo "No comments found.";
            }

            echo "</div>";

        } else {
            echo "<p>Viewing comments is temporarily disabled.</p>";
        }
    ?>
</body>
</html>