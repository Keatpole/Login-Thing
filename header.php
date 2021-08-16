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

                $style = "style=\"font: 400 13.3333px Arial; font-size: 16px;\"";

                if (isset($_COOKIE["userid"]) && password_verify($_COOKIE["id"], $_COOKIE["userid"]) && password_verify($_COOKIE["userid"], $_COOKIE["user"])) {
                    $user = getTable($conn, "users", ["id", $_COOKIE["id"]]);

                    if ($user == null) {
                        $_SESSION["rank"] = 0;
                        echo "<h1>Your account has been deleted. Press <a style=\"color: red;\" href=\"includes/account/logout\">here</a> to log out.</h1>";
                        exit();
                    }

                    $_SESSION["id"] = $user["id"];
                    $_SESSION["uid"] = $user["uid"];
                    $_SESSION["rank"] = $user["rank"];
                }

                if (isset($_SESSION["uid"])) {

                    if (getTable($conn, "users", ["uid", $_SESSION["uid"]]) == null || $_SESSION["id"] != getTable($conn, "users", ["uid", $_SESSION["uid"]])["id"]) {
                        $_SESSION["rank"] = 0;
                        echo "<h1>Your account has been deleted. Press <a style=\"color: red;\" href=\"includes/account/logout\">here</a> to log out.</h1>";
                        exit();
                    }

                    $_SESSION["rank"] = getTable($conn, "users", ["uid", $_SESSION["uid"]])["rank"];

                    if ($_SESSION["rank"] <= -1) {
                        if (!isset($_GET["banned"]) || $_GET["webname"] !== "index") {
                            header("location: .?banned");
                        }
                        echo "<li><a href='includes/account/logout' " . $style . ">Log out</a></li>";
                        echo "<h1>You have been banned</h1>";
                        die();
                    } else {
                        if (isset($_GET["banned"])) {
                            if ($_GET["webname"] == "index") {
                                header("location: .");
                                exit();
                            }
                            header("location: " . $_GET["webname"]);
                        }
                        echo "<li><a href='.' " . $style . ">Home</a></li>";
                        echo "<li><a href='user' " . $style . ">Profile</a></li>";
                        echo "<li><a href='friends' " . $style . ">Friends</a></li>";
                        echo "<li><a href='search' " . $style . ">Search</a></li>";
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
                    header("location: .?error=404");
                    break;
                case 'alreadyliked':
                    $say = "You already liked this comment!";
                    break;
                case 'usernotfound':
                    $say = "That user does not exist!";
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
                case 'targetisimmune':
                    $say = "The target is immune! (You can only affect rank 1 and below)";
                    break;
                case 'authfailed':
                    $say = "Could not authorize access to this command! Make sure you are logged in to the right account.";
                    break;
                case 'stmtfailed':
                    $say = "Something went wrong! (error code: stmtfailed-" . $_GET["webname"] . ")";
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

        if (isset($_SESSION["passtoken"]) && $_SESSION["passtoken"][2] && strtotime(date("F j, Y, H:i")) >= strtotime($_SESSION["passtoken"][2])) {
            $_SESSION["passtoken"] = null;
            if ($_GET["webname"] == "resetpass") {
                header("location: login?error=invalidtoken");
            }
        }
    
    ?>
</body>