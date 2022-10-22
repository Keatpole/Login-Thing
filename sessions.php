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

        if (!isset($_SESSION["rank"])) {
            header("location: .?error=nologin");
            exit();
        }

        echo "<h1>Sessions</h1>";
        echo "<p>Click \"Expire Session\" to log out of a location.</p>";

        echo "<div class='comments'>";

        $sessions = 0;
        $ids = [];
        foreach (mysqli_fetch_all(getTable($conn, "sessions", "", true)) as $res) {
            if ($res[1] == $_SESSION["id"]) {
                $sessions++;
                $ids[] = $res[0];
            }
        }

        $str = ($sessions == 1 ? "location" : "locations");

        if (sizeof($ids) <= 1) {
            echo "<p>You are not logged in at any other locations.</p>";

            echo "</div>";
            exit();
        }

        echo "<p>You are currently logged in at " . strval($sessions) . " " . $str . ".</p>";

        echo "<form action='includes/account/logout?expire_all_sessions' method='post'>";
        echo "<button class='button' style='background-color: darkred;'>Expire All Sessions</button>";
        echo "</form></br>";

        foreach ($ids as $id) {
            echo "<div class='comment'>";

            $res = getTable($conn, "sessions", ["id", $id]);

            if ($res["id"] == $_SESSION["sessionid"]) {
                continue;
            }

            echo "<form action='includes/account/expireSession' method='post'>";
            echo "<p>Someone logged in at " . sqldate_to_date($res["date"], false, false) . " and was last seen at " . sqldate_to_date($res["lastdate"], false, false) . ".</p>";
            echo "<input type='hidden' name='id' value='" . $id . "'>";
            echo "<input type='hidden' name='error' value='none'>";
            echo "<input type='hidden' name='website' value='sessions'>";
            echo "<button class='button'>Expire Session</button>";

            echo "</div>";
        }

        echo "</div>";

    ?>
</body>
</html>