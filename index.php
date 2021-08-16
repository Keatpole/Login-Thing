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
            if (!isset($_GET["includefromprofile"])) {
                echo "<h4 class='center'>Welcome back, " . $_SESSION["uid"] . "</h4>";

                if ($settings->enable_posting_comments) {


                    ?>

                    <form action="includes/comments/postComment" method="post">
                        <?php

                            if (isset($_GET["reply"])) {
                                echo "<input name=\"replyid\" type=hidden value=\"" . $_GET["reply"] . "\" /><input name=\"message\" id=\"inputmsg\" style=\"line-height: 3.3em\" placeholder=\"Reply...\" />";
                            } else {
                                echo "<input name=\"message\" id=\"inputmsg\" style=\"line-height: 3.3em\" placeholder=\"What's on your mind?\" />";
                            }

                        ?>

                        <button class="button" type="submit" name="submit">Post</button>
                    </form>

                    <?php


                }
                else {
                    echo "<p>Posting comments is temporarily disabled.</p>";
                }

            }

            if (!$settings->enable_likes) {
                echo "<p>Likes are temporarily disabled.</p>";
            }
        
            if ($settings->enable_viewing_comments) {

                $has = false;

                foreach (mysqli_fetch_all(getTable($conn, "messages")) as $res) {
                    if (isset($_GET["includefromprofile"]) && $res[2] != $_GET["includefromprofile"]) {
                        continue;
                    }

                    echo "<div id=" . $res[0] . " tabindex=\"-1\">";

                    $contin = true;

                    if (isset($_GET["hashtag"])) {    
                        $contin = false;

                        foreach (explode(" ", $res[1]) as $v) {
                            if (strtolower(urldecode($_GET["hashtag"])) == strtolower(substr($v, 1))) {
                                $contin = true;
                            }
                        }
                    }

                    if (!$contin) continue;

                    $has = true;

                    if (getTable($conn, "users", ["id", $res[2]]) == null) {
                        if ($_SESSION["rank"] > 0) {
                            echo "<h2>[" . $res[0] . "] [Account Deleted]:</h2>";
                        } else {
                            echo "<h2>[Account Deleted]:</h2>";
                        }
                    } else {
                        $user = getTable($conn, "users", ["id", $res[2]]);
                        if ($_SESSION["rank"] > 0) {

                            if ($user["verified"] == 0) {

                                

                                ?>

                                    <h2>
                                        [<?= $res[0] ?>]
                                        <a style="color: green;" href="user?u=<?= $res[2] ?>"><?= $user["uid"] ?></a>:
                                    </h2>

                                <?php

                            }
                            elseif ($user["verified"] == 1) {
                                echo "<h2>[" . $res[0] . "] <a style=\"color: green;\" href=\"user?u=" . $res[2] . "\">" . $user["uid"] . "</a><p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p>:</h2>";
                            }
                        } else {
                            if ($user["verified"] == 0) {
                                echo "<h2><a style=\"color: green;\" href=\"user?u=" . $res[2] . "\">" . $user["uid"] . "</a>:</h2>";
                            }
                            elseif ($user["verified"] == 1) {
                                echo "<h2><a style=\"color: green;\" href=\"user?u=" . $res[2] . "\">" . $user["uid"] . "</a><p style=\"display: inline;color: #ccaa00;\" title=\"Verified\">✔</p>:</h2>";
                            }
                        }
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
                            $result .= "<a style=\"color: red;\" href=\".?hashtag=" . urlencode(strtolower(substr($v, 1))) . "\">" . $v . "</a>";
                        }
                        else {
                            $result .= $v;
                        }
                        $result .= " ";
                    }

                    $replyTo = "";

                    if ($res[4] != null) {
                        $replyTo = "[<a href=\".#" . $res[4] . "\">Reply To</a>] ";
                    }

                    echo "<p>" . $replyTo . $result . "</p>";
                    echo "<p>" . $res[3] . " Likes"  . "</p>";

                    if ($settings->enable_likes) {
                        if ($_SESSION["id"] == $res[2] || $_SESSION["rank"] >= 2) {
                            echo "<form action=\"includes/comments/likeComment\" method=\"post\"><a href=\"?reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a> <input type=hidden name=\"commentAuthor\" value=" . $res[2] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\".?\"> <button type=\"submit\" name=\"submit\" class=\"button\">Like</button> <button type=\"submit\" name=\"delete\" class=\"button\">Delete</button></form>";
                        }
                        elseif ($_SESSION["id"] == $res[2] || $_SESSION["rank"] == 1) {
                            echo "<form action=\"includes/comments/likeComment\" method=\"post\"><input type=hidden name=\"commentAuthor\" value=" . $res[2] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\".?\"><a href=\"?reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a> <button type=\"submit\" name=\"submit\" class=\"button\">Like</button> <button type=\"submit\" name=\"delete\" class=\"button\">Request Delete</button></form>";
                        }
                        else {
                            echo "<form action=\"includes/comments/likeComment\" method=\"post\"><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\".?\"><a href=\"?reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a> <button type=\"submit\" name=\"submit\" class=\"button\">Like</button></form>";
                        }
                    } else {
                        if ($_SESSION["id"] == $res[2] || $_SESSION["rank"] >= 2) {
                            echo "<form action=\"includes/comments/likeComment\" method=\"post\"><input type=hidden name=\"commentAuthor\" value=" . $res[2] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\".?\"><a href=\"?reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a> <button type=\"submit\" name=\"delete\" class=\"button\">Delete</button></form>";
                        }
                        elseif ($_SESSION["id"] == $res[2] || $_SESSION["rank"] == 1) {
                            echo "<form action=\"includes/comments/likeComment\" method=\"post\"><input type=hidden name=\"commentAuthor\" value=" . $res[2] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\".?\"><a href=\"?reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a> <button type=\"submit\" name=\"delete\" class=\"button\">Request Delete</button></form>";
                        }
                        else {
                            echo "<form action=\"includes/comments/likeComment\" method=\"post\"><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><input type=hidden name=\"return\" value=\".?\"><a href=\"?reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a></form>";
                        }
                    }

                    echo "</div>";

                }

                if (!$has) {
                    $say = "</br>You have no comments";
                    if (isset($_GET["includefromprofile"])) {
                        if ($_GET["includefromprofile"] == $_SESSION["id"]) $say = "You have no comments";
                        else $say = "This user has no comments";
                    }
                    echo $say;
                }

            } else {
                echo "<p>Viewing comments is temporarily disabled.</p>";
            }

        } else if (!isset($_SESSION["uid"])) {
            ?>
            
                <h1 class='center'>Welcome</h1></br>
                <p class='center'>Make an <a href='signup' style='color: darkblue; text-decoration: none;'>account</a> to access more <a href='features' style='color: darkblue; text-decoration: none;'>features</a>.</p>

            <?php
        }
    ?>
</body>
</html>