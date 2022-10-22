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

        $group = getTable($conn, "modhelpgroups", ["id", $_GET["g"]]);

        if ($group == null) {
            header("location: groups");
            exit();
        }

        $isowner = 0;
        if ($_SESSION["rank"] <= 0) {
            if (isset($_SESSION["modhelpgroup"])) {
                if ($_SESSION["modhelpgroup"] != $group["id"]) {
                    header("location: groups");
                    exit();
                }
                $isowner = 1;
            }
            else {
                header("location: groups");
                exit();
            }
        }

        if ($group["verified"] && $isowner) {
            $rng = bin2hex(random_bytes(36));
            $ptid = insertTable($conn, "passwordtokens", ["userid" => $user["id"], "token" => password_hash($rng, PASSWORD_DEFAULT), "expiredate" => date("Y-m-d H:i:s", time() + 3600)]);

            $_SESSION["ptid"] = $ptid;
            header("location: resetpass?t=" . $rng);
            exit();
        }

        echo "<h4 class='center'>You're viewing modhelp group " . htmlspecialchars($group["name"], ENT_QUOTES, "UTF-8") . "</h4>";

        echo "<h6 class='center'>Comment \"!help\" for a list of commands.</h6>";

        if ($settings->enable_posting_comments) {


            ?>

            <form action="includes/modhelp/comment" method="post">

                <?php
                    echo "<input name=\"groupid\" type=hidden value=\"" . htmlspecialchars($_GET["g"], ENT_QUOTES, "UTF-8") . "\" />";
                    echo "<input name=\"message\" id=\"inputmsg\" style=\"line-height: 3.3em\" placeholder=\"What do you need help with?\" />";
                ?>

                <button class="button" type="submit" name="submit">Post</button></br></br>

            </form>

            <?php


        }
        else {
            echo "<p>Posting comments is temporarily disabled.</p>";
        }
        
        if ($settings->enable_viewing_comments) {

            echo "</br></br><div class=\"comments\">";

            $has = false;

            foreach (mysqli_fetch_all(getTable($conn, "modhelpmessages", "", true)) as $res) {
                if ($res[4] != $_GET["g"]) continue;
                else $has = true;
            }

            if ($has) {
                echo "<h2>Comments:</h2>";
            }

            $hasreply = false;

            foreach (mysqli_fetch_all(getTable($conn, "modhelpmessages", "", true)) as $res) {
                if ($res[4] != $_GET["g"]) {
                    continue;
                }

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

                    if ($isowner) {
                        $verified = "";
                        $admin = "";
                    }

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
                        $result .= "<a style=\"color: red;\" href=\"groups?mhg=" . $_GET["g"] . "&hashtag=" . urlencode(strtolower(substr($v, 1))) . "\">" . $v . "</a>";
                    }
                    else {
                        $result .= $v;
                    }
                    $result .= " ";
                }

                $replyTo = "";

                if ($res[3] != null) {
                    $replyTo = "[<a href=\"groups?mhg=" . $_GET["g"] . "#" . $res[0] . "\">Reply To</a>] ";
                }

                echo "<p>" . $replyTo . htmlspecialchars_decode($result, ENT_QUOTES) . "</p>";

                echo "<form action=\"includes/groups/deleteComment\" method=\"post\"><input type=hidden name=\"groupid\" value=" . $res[4] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input> <button type=\"submit\" name=\"delete\" class=\"button\">Delete</button></form>";

                echo "</div>";

            }

            if (!$has) {
                echo "This modhelp group has no comments";
            }

            echo "</div>";

        } else {
            echo "<p>Viewing comments is temporarily disabled.</p>";
        }
    ?>
</body>
</html>