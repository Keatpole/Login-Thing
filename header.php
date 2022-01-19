<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login Thing</title>
</head>
<body>
    <div class="wrapper">
        <ul class="testul">
            <?php

                session_start();
                require_once "includes/other/dbh.php";
                require_once "includes/other/functions.php";

                foreach (mysqli_fetch_all(getTable($conn, "users")) as $res) {
                    if ($res[6] == 1 && strtotime(date("Y-m-d H:i:s")) >= strtotime($res[7])) {
                        deleteTable($conn, "users", ["id", $res[0]]);
                    }
                }

                $style = "style=\"font: 400 13.3333px Arial; font-size: 16px;\"";

                if (isset($_SESSION["uid"])) {

                    $user = getTable($conn, "users", ["id", $_SESSION["id"]]);

                    if ($user == null || $_SESSION["id"] != $user["id"] || $user["deleted"]) {
                        $_SESSION["rank"] = 0;
                        echo "<h1>Your account has been deleted. Press <a style=\"color: red;\" href=\"includes/account/logout\">here</a> to log out.</h1>";
                        exit();
                    }

                    $_SESSION["rank"] = getTable($conn, "users", ["uid", $_SESSION["uid"]])["rank"];

                    if (isset($_SESSION["passtoken"]) && $_SESSION["passtoken"][2] && strtotime(date("F j, Y, H:i")) >= strtotime($_SESSION["passtoken"][2])) {
                        $_SESSION["passtoken"] = null;
                        if ($_GET["webname"] == "resetpass") {
                            header("location: login?error=invalidtoken");
                        }
                    }

                    $banned = null;

                    foreach (getTable($conn, "bans", "", true) as $v) {
                        if ($v["target"] == $user["id"]) $banned = $v;
                    }

                    if ($banned) {
                        if (!isset($_GET["banned"]) || $_GET["webname"] !== "index") {
                            header("location: .?banned");
                        }

                        $banner = getTable($conn, "users", ["id", $v["banner"]]);

                        echo "<li><a href='includes/account/logout' " . $style . ">Log out</a></li>";
                        echo "<h1>You have been banned by " . $banner["uid"] . " (" . rankFromNum($banner["rank"]) . ") on [" . join("/", array_reverse(explode("-", $v["date"]))) . " (D/M/Y)]</h1>";

                        die();
                    } else {
                        if (isset($_GET["banned"])) {
                            if ($_GET["webname"] == "index") {
                                header("location: .");
                                exit();
                            }
                            header("location: " . $_GET["webname"]);
                        }
                        echo "<li><a href=\"#main\" class=\"skipnavlink\" . " . $style . ">Skip Navigation</a></li>";
                        echo "<li><a href='.' " . $style . ">Home</a></li>";
                        echo "<li><a href='user' " . $style . ">Profile</a></li>";

                        $has = false;
                        foreach (mysqli_fetch_all(getTable($conn, "friendreq")) as $res) {
                            if ($res[2] == $_SESSION["id"]) {
                                if (getTable($conn, "users", ["id", $res[1]]) != null) {
                                    echo "<li><a href='friends' style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\">Friends</a></li>";
                                    $has = true;
                                    break;
                                }
                            }
                        }
                        if (!$has) {
                            echo "<li><a href='friends' style=\"font: 400 13.3333px Arial; font-size: 16px;\">Friends</a></li>";
                        }
                        echo "<li><a href='groups' " . $style . ">Groups</a></li>";
                        echo "<li><a href='search' " . $style . ">Search</a></li>";
                        echo "<li><a href='appeal' " . $style . ">Appeal</a></li>";
                        if ($_SESSION["rank"] >= 1) {
                            echo "<li><a href='moderation' " . $style . ">Moderation</a></li>";
                        }
                        echo "<li><a href='includes/account/logout' " . $style . ">Logout</a></li>";
                    }
                }
                else {
                    if (isset($_GET["banned"])) {
                        if ($_GET["webname"] == "index") {
                            header("location: .");
                            exit();
                        }
                        header("location: " . $_GET["webname"]);
                    }
                    echo "<li><a href='.' " . $style . ">Home</a></li>";
                    echo "<li><a href='signup' " . $style . ">Sign up</a></li>";
                    echo "<li><a href='login' " . $style . ">Login</a></li>";
                }
            ?>
        </ul>
    </div>
    <?php
        if (isset($_GET["error"])) {
            $say = "";
            switch ($_GET["error"]) {
                case 'none':
                    echo "<p style='color: green;'>Done!</p>";
                    break;
                case 'nologin':
                    $say = "You must be logged in to do that!";
                    break;
                case 'permsreq':
                    $say = "You need to be a staff to do this!";
                    break;
                case '404':
                    $say = "Page not found!";
                    break;
                case '404r':
                    header("location: http://{$_SERVER['HTTP_HOST']}/LoginThing/.?error=404");
                    break;
                case 'alreadyliked':
                    $say = "You already liked this comment!";
                    break;
                case 'usernotfound':
                    $say = "That user does not exist!";
                    break;
                case 'commentnotfound':
                    $say = "That comment does not exist!";
                    break;
                case 'wronglogin':
                    $say = "Incorrect login details!";
                    break;
                case 'usernametaken':
                    $say = "This username or email is already in use!";
                    break;
                case 'pwdmatch':
                    $say = "Passwords doesn't match!";
                    break;
                case 'noneemail':
                    echo "<p style='color: green;'>Done! Check your email.</p>";
                    break;
                case 'invalidtoken':
                    $say = "Could not validate token! This token may have been used before.";
                    break;
                case 'invalidemail':
                    $say = "Fill in a proper email!";
                    break;
                case 'emptyinput':
                    $say = "Fill in all fields!";
                    break;
                case 'invaliduid':
                    $say = "Fill in a proper username!";
                    break;
                case 'duplicateindb':
                    $say = "Cannot submit duplicates!";
                    break;
                case 'targetisimmune':
                    $say = "The target is immune!";
                    break;
                case 'authfailed':
                    $say = "Could not authorize access to this command! Make sure you are logged in to the right account.";
                    break;
                case 'useralreadybanned':
                    $say = "That user is already banned!";
                    break;
                case 'useralreadymuted':
                    $say = "That user is already muted!";
                    break;
                case 'usernotmuted':
                    $say = "That user is not muted!";
                    break;
                case 'usernotbanned':
                    $say = "That user is not banned!";
                    break;
                case 'userdeleted':
                    $say = "That account has been deleted!";
                    break;
                case 'stmtfailed':
                    $say = "Something went wrong! (error code: stmtfailed-" . $_GET["webname"] . ")";
                    break;
                case 'notfriend':
                    $say = "That user is not your friend! Add them as a friend then if they accept it, try this again.";
                    break;
                # gcf = group command failed
                case 'gcfauthfailed':
                    $say = "You can not use commands in this group.";
                    break;
                case 'gcfdelete':
                    $say = "To delete a group, please type: !delete (Group name)";
                    break;
                case 'gcfkick':
                    $say = "That user is not in this group!";
                    break;
                case 'gcfaddin':
                    $say = "That user is already in this group!";
                    break;
                case 'gcfmodin':
                    $say = "That user is already a moderator!";
                    break;
                case 'gcfunmodin':
                    $say = "That user is not a moderator!";
                    break;
                case 'gcfaddfriend':
                    $say = "That user is not your friend! To add someone to a group, add them as a friend then if they accept it, try this again.";
                    break;
                case 'gcfleaveauthor':
                    $say = "You can not leave as you created this group. In order to leave you need to type \"!delete (group name)\". WARNING: This will delete the group.";
                    break;
                case 'gcdone':
                    echo "<p style='color: green;'>Command executed!</p>";
                    break;
                case'gcnotfound':
                    $say = "Command not found!";
                    break;
                case 'gchelpauthor':
                    echo "<p>Commands: !kick (Username) - Kicks a user, !add (Username) - Adds a user to the group, !delete (Group name) - Deletes the group, !members - Displays a list of members</p>";
                    break;
                case 'gchelpstaff':
                    echo "<p>Commands: !kick (Username) - Kicks a user, !leave - Leaves the group, !members - Displays a list of members</p>";
                    break;
                case 'gchelpmember':
                    echo "<p>Commands: !leave - Leaves the group, !members - Displays a list of members</p>";
                    break;
                default:
                    if ($_GET["webname"] == "index") {
                        header("location: .");
                        break;
                    }
                    header("location: " . $_GET["webname"]);
                    break;
            }
            echo "<p style='color: red;'>" . $say . "</p>";

        }
        if (isset($_GET["refresh"])) header("refresh: " . $_GET["refresh"]);
    ?>

    <div id="main">
</body>