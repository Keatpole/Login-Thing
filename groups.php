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

        if (!isset($_SESSION["id"])) {
            header("location: .?error=nologin");
            exit();
        }

        $groups = getTable($conn, "groups", "", true);

        if (isset($_GET["g"])) {

            include_once "comments/group.php";

            exit();

        }

    ?>

    <h2>Create a Group</h2>
    <form action="includes/groups/create" method="post">
        <input type="text" name="name" placeholder="Name..."></br>
        </br><button type="submit" name="submit" class="button">Create</button>
    </form>

    <?php

    // View groups
    $has = false;
    $in = false;
    foreach ($groups as $i) {
        if ($i != null) $has = true;
        foreach (explode(",", $i["members"]) as $v) {
            if ($v == $_SESSION["id"]) {
                $in = true;
                break;
            }
        }
        if ($has && $in) break;
    }
    if ($has && $in) echo "</br></br><h2>View Groups</h2>";

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
        foreach ($groups as $i) {
            if ($i != null) $has = true;
        }

        if ($has) echo "</br></br><h2>Add to Group</h2>";

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

                <form action="includes/groups/add" method="post">
                    <input type="hidden" name="group" value="<?= $i["id"] ?>">
                    <input type="hidden" name="user" value="<?= htmlspecialchars($_GET["u"], ENT_QUOTES, "UTF-8") ?>">
                    <button type="submit" name="submit" class="button">Add <?= htmlspecialchars(getTable($conn, "users", ["id", $_GET["u"]])["uid"], ENT_QUOTES, "UTF-8") ?> to group <?= $i["name"] ?>.</button>
                </form></br>

            <?php
        }

    }

    if ($_SESSION["rank"] >= 2 && $has) {
        
        $admin_h2_displayed = false;

        foreach ($groups as $i) {

            $in = false;
            foreach (explode(",", $i["members"]) as $v) {
                if ($v == $_SESSION["id"]) {
                    $in = true;
                    break;
                }
            }
            if ($in) continue;

            if (!$admin_h2_displayed) {
                echo "</br></br></br><h2>Admin</h2>";
                $admin_h2_displayed = true;
            }
            
            ?>
    
                <button class="button"><a style="color: white; text-decoration: none;" href="?g=<?= $i["id"] ?>">View group <?= $i["name"] ?>.</a></button>
    
            <?php
        }

    }

    ?>

</body>
</html>