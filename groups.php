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

        $groups = getTable($conn, "groups", "", true);

        if (isset($_GET["g"])) {

            $access = false;
            foreach (explode(",", getTable($conn, "groups", ["id", $_GET["g"]])["members"]) as $v) {
                if ($v == $_SESSION["id"]) $access = true;
            }
            if (!$access) {
                header("location: groups");
                exit();
            }

            if ($settings->enable_posting_comments) {


                ?>

                <h4 class='center'>You're viewing group <?= getTable($conn, "groups", ["id", $_GET["g"]])["name"] ?></h4>

                <form action="includes/groups/postComment" method="post">

                    <?php

                        echo "<input name=\"groupid\" type=hidden value=\"" . $_GET["g"] . "\" />";

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
        
            if ($settings->enable_viewing_comments) {

                $has = false;

                foreach (mysqli_fetch_all(getTable($conn, "groupmessages")) as $res) {

                    if ($res[4] != $_GET["g"]) continue;

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
                            $result .= "<a style=\"color: red;\" href=\"?g=" . $_GET["g"] . "&hashtag=" . urlencode(strtolower(substr($v, 1))) . "\">" . $v . "</a>";
                        }
                        else {
                            $result .= $v;
                        }
                        $result .= " ";
                    }

                    $replyTo = "";

                    if ($res[3] != null) {
                        $replyTo = "[<a href=\"#" . $res[3] . "\">Reply To</a>] ";
                    }

                    echo "<p>" . $replyTo . $result . "</p>";

                    
                    if ($_SESSION["id"] == $res[2] || $_SESSION["rank"] >= 2 || getTable($conn, "groups", ["id", $_GET["g"]])["author"] == $_SESSION["id"]) echo "<form action=\"includes/groups/deleteComment\" method=\"post\"><input type=hidden name=\"groupid\" value=" . $res[4] . "><input type=hidden name=\"commentAuthor\" value=" . $res[2] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><a href=\"?g=" . $_GET["g"] . "&reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a> <button type=\"submit\" name=\"delete\" class=\"button\">Delete</button></form>";
                    else echo "<form action=\"includes/groups/deleteComment\" method=\"post\"><input type=hidden name=\"groupid\" value=" . $res[4] . "><input type=hidden name=\"commentId\" value=" . $res[0] . "></input><a href=\"?g=" . $_GET["g"] . "&reply=" . $res[0] . "\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reply</a></form>";
                    
                    echo "</div>";

                }

                if (!$has) echo "</br>There are no comments";

            } else {
                echo "<p>Viewing comments is temporarily disabled.</p>";
            }

            exit();

        }

    ?>

    <h2>Create a Group</h2>
    <form action="includes/groups/createGroup" method="post">
        <input type="text" name="name" placeholder="Name..."></br>
        </br><button type="submit" name="submit" class="button">Create</button>
    </form>

    <?php

    // View groups
    $has = false;
    foreach ($groups as $i) {
        if ($i != null) $has = true;
    }
    if ($has) echo "</br></br><h2>View Groups</h2>";

    foreach ($groups as $i) {

        $in = false;
        foreach (explode(",", $i["members"]) as $v) {
            if ($v == $_SESSION["id"]) {
                $in = true;
                break;
            }
        }
        if (!$in) continue;
        
        ?>

            <button class="button"><a style="color: white; text-decoration: none;" href="?g=<?= $i["id"] ?>">View group <?= $i["name"] ?>.</a></button>

        <?php
    }

    if (isset($_GET["u"])) {

        if (getTable($conn, "users", ["id", $_GET["u"]]) == null || $_GET["u"] == $_SESSION["id"]) {
            header("location: groups");
            exit();
        }

        // Add to group

        $has = false;
        $in = false;
        foreach ($groups as $i) {
            if ($i != null) $has = true;
            foreach (explode(",", $i["members"]) as $v) {
                if ($v == $_GET["u"]) {
                    $in = true;
                    break;
                }
            }
        }
        if ($has && !$in) echo "</br></br><h2>Add to Group</h2>";

        foreach ($groups as $i) {

            if ($i["author"] != $_SESSION["id"]) continue;

            $in = false;
            foreach (explode(",", $i["members"]) as $v) {
                if ($v == $_GET["u"]) {
                    $in = true;
                    break;
                }
            }
            if ($in) continue;
            
            ?>

                <form action="includes/groups/addToGroup" method="post">
                    <input type="hidden" name="group" value="<?= $i["id"] ?>">
                    <input type="hidden" name="user" value="<?= $_GET["u"] ?>">
                    <button type="submit" name="submit" class="button">Add <?= getTable($conn, "users", ["id", $_GET["u"]])["uid"] ?> to group <?= $i["name"] ?>.</button>
                </form></br>

            <?php
        }

    }

    ?>

</body>
</html>