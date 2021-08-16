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

        if ($_SESSION["rank"] <= 1) {
            header("location: .?error=permsreq");
            exit();
        }

    ?>

    <h1>Suggestions</h1>

    <?php

    if (!$settings->enable_suggestions) {
        echo "<p>Suggestions are temporarily disabled.</p>";
        exit();
    }

    ?>

    <p>Click a suggestion to view it</p>
    
    <?php

    $result = getTable($conn, "modsuggestions");

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if (isset($_GET["id"])) {
                if ($row["id"] == $_GET["id"]) {

                    if ($row["type"] == "DeleteComment") {
                        $msg = getTable($conn, "messages", ["id", $row["targetsUid"]]);
                        echo "<h4><a href=\".#" . $row["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">Comment</a> - Type: Delete Comment - Suggester: " . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</h4>";
                        echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/approveSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=DeleteComment'>Accept</a> <a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/refuseSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=DeleteComment'>Deny</a>";
                    }
                    else {
                        echo "<h4>Username: <a href=\"user?u=" . getTable($conn, "users", ["uid", $row["targetsUid"]])["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . $row["targetsUid"] . "</a> - Type: " . rankFromNum($row["type"]) . " - Suggester: " . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</h4><br>";
                        echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/approveSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Accept</a> <a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/refuseSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Deny</a>";
                    }

                    exit();

                }
                
            } else {
                if ($row["type"] == "DeleteComment") {
                    $msg = getTable($conn, "messages", ["id", $row["targetsUid"]]);
                    echo "<h4><a href=\".#" . $row["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">Comment</a><a style='color: black;text-decoration:none;' href='?suggestions&id=" . $row["id"] . "'> - Type: Delete Comment - Suggester: " . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></h4><br>";

                }
                else {
                    echo "<h4><a style='color: black;text-decoration:none;' href='?suggestions&id=" . $row["id"]. "'>Username: <a href=\"user?u=" . getTable($conn, "users", ["uid", $row["targetsUid"]])["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . $row["targetsUid"] . "</a><a style='color: black;text-decoration:none;' href='?suggestions&id=" . $row["id"]. "'> - Type: " . rankFromNum($row["type"]) . " - Suggester: " . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></h4><br>";
                }
            }

        }
    } else {
        echo "<h4>0 suggestions</h4>";
    }

    ?>
</body>
</html>