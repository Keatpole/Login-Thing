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

        if ($_SESSION["rank"] < 1) {
            header("location: .?error=permsreq");
            exit();
        }

        echo "<h1>Moderation Tools</h1>";

        echo "<p>You are currently " . (strpos("aeiou", strtolower(rankFromNum($_SESSION["rank"])[0])) !== false ? "an" : "a") . " " . rankFromNum($_SESSION["rank"]) . ".</p></br>";

        $usernameValue = (isset($_GET["u"]) ? $_GET["u"] : "");
        $selectedValue = (isset($_GET["sel"]) ? $_GET["sel"] : "");

        if ($_SESSION["rank"] == 1) {

            echo "<h1>Mod Panel</h1>";

            if ($settings->enable_mod_panel) {

                ?>

                    <form action="includes/staff/mod" method="post">
                        <input type="text" name="username" placeholder="Username..." value="<?= htmlspecialchars($usernameValue, ENT_QUOTES, "UTF-8") ?>"></br></br>
                        <select name="action" size="6">
                            <option value="0" <?php if ($selectedValue == "user"): ?> selected="selected" <?php endif; ?> >Set User</option>
                            <option value="1" <?php if ($selectedValue == "mod"): ?> selected="selected" <?php endif; ?> >Set Moderator</option>
                            <option value='2' <?php if ($selectedValue == "admin"): ?> selected="selected" <?php endif; ?> >Set Admin</option>
                            <option value='3' <?php if ($selectedValue == "delc"): ?> selected="selected" <?php endif; ?> >Delete Comment</option>
                            <option value='4' <?php if ($selectedValue == "unm"): ?> selected="selected" <?php endif; ?> >(Un)Mute</option>
                            <option value="-1" <?php if ($selectedValue == "unb"): ?> selected="selected" <?php endif; ?> >(Un)Ban</option>
                        </select></br></br>
                        <button type="submit" name="submit" class="button">Confirm</button>
                    </form>

                <?php

            } else {
                echo "<p>Mod Panel is temporarily disabled.</p>";
            }

        }
        elseif ($_SESSION["rank"] >= 2) {
            
            if (isset($_GET["suggestions"])) {
                
                echo "<div class=\"comments\">";

                echo "<h1>Suggestions</h1>";

                if (!$settings->enable_suggestions) {
                    echo "<p>Suggestions are temporarily disabled.</p>";
                    exit();
                }

                echo "<p>Click a suggestion to view it</p>";

                $result = getTable($conn, "modsuggestions");

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        if (isset($_GET["id"])) {
                            if ($row["id"] == $_GET["id"]) {

                                if ($row["type"] == "DeleteComment") {
                                    $msg = getTable($conn, "messages", ["id", $row["targetsUid"]]);
                                    echo "<h4 class=\"comment\"><a href=\"reply?c=" . $row["targetsUid"] . "#" . $row["targetsUid"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">Comment</a> - Type: Delete Comment - Suggester: <a href=\"user?u=" . $row["suggester"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></h4><hr style=\"border: 1px dotted black;\">";
                                    echo "<a class=\"button comment\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: #4CAF50; border: none; padding: 15px 56.2px;\" href='includes/staff/approveSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=DeleteComment'>Accept</a> <a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/refuseSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=DeleteComment'>Deny</a>";
                                }
                                elseif ($row["type"] == "(Un)Mute") {
                                    echo "<h4 class=\"comment\">Username: <a href=\"user?u=" . getTable($conn, "users", ["uid", $row["targetsUid"]])["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . $row["targetsUid"] . "</a> - Type: " . $row["type"] . " - Suggester: <a href=\"user?u=" . $row["suggester"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></h4><hr style=\"border: 1px dotted black;\">";
                                    echo "<a class=\"button comment\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: #4CAF50; border: none; padding: 15px 56.2px;\" href='includes/staff/approveSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Accept</a> <a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/refuseSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Deny</a>";
                                }
                                elseif ($row["type"] == "-1") {
                                    echo "<h4 class=\"comment\">Username: <a href=\"user?u=" . getTable($conn, "users", ["uid", $row["targetsUid"]])["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . $row["targetsUid"] . "</a> - Type: (Un)Ban - Suggester: <a href=\"user?u=" . $row["suggester"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></h4><hr style=\"border: 1px dotted black;\">";
                                    echo "<a class=\"button comment\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: #4CAF50; border: none; padding: 15px 56.2px;\" href='includes/staff/approveSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Accept</a> <a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/refuseSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Deny</a>";
                                }
                                else {
                                    echo "<h4 class=\"comment\">Username: <a href=\"user?u=" . getTable($conn, "users", ["uid", $row["targetsUid"]])["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . $row["targetsUid"] . "</a> - Type: " . rankFromNum($row["type"]) . " - Suggester: <a href=\"user?u=" . $row["suggester"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></h4><hr style=\"border: 1px dotted black;\">";
                                    echo "<a class=\"button comment\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: #4CAF50; border: none; padding: 15px 56.2px;\" href='includes/staff/approveSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Accept</a> <a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/refuseSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Deny</a>";
                                }

                                exit();

                            }
                            
                        } else {
                            if ($row["type"] == "DeleteComment") {
                                $msg = getTable($conn, "messages", ["id", $row["targetsUid"]]);
                                echo "<h4 class=\"comment\"><a href=\"reply?c=" . $row["targetsUid"] . "#" . $row["targetsUid"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">Comment</a><a style='color: black;text-decoration:none;' href='?suggestions&id=" . $row["id"] . "'> - Type: Delete Comment - Suggester: <a href=\"user?u=" . $row["suggester"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></a></h4>";

                            }
                            elseif ($row["type"] == "(Un)Mute") {
                                echo "<h4>Username: <a href=\"user?u=" . getTable($conn, "users", ["uid", $row["targetsUid"]])["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . $row["targetsUid"] . "</a> - Type: " . $row["type"] . " - Suggester: <a href=\"user?u=" . $row["suggester"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></h4>";
                                echo "<a class=\"button comment\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/approveSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Accept</a> <a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/refuseSuggestion?uid=" . $row["targetsUid"] . "&id=" . $row["id"] . "&type=" . $row["type"] . "'>Deny</a>";
                            }
                            else {
                                echo "<h4 class=\"comment\"><a style='color: black;text-decoration:none;' href='?suggestions&id=" . $row["id"]. "'>Username: <a href=\"user?u=" . getTable($conn, "users", ["uid", $row["targetsUid"]])["id"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . $row["targetsUid"] . "</a><a style='color: black;text-decoration:none;' href='?suggestions&id=" . $row["id"]. "'> - Type: " . rankFromNum($row["type"]) . " - Suggester: <a href=\"user?u=" . $row["suggester"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["suggester"]])["uid"] . "</a></a></h4><br>";
                            }
                        }

                    }
                } else {
                    echo "<h4>0 suggestions</h4>";
                }

                echo "</div>";

                exit();
                
            }
            elseif (isset($_GET["reports"])) {
    
                if (!$settings->enable_report) {
                    echo "<p>Reporting is temporarily disabled.</p>";
                    exit();
                }

                echo "<div class=\"comments\">";
    
                echo "<h1>Reports</h1>";
                echo "<p>Click a report to view it</p>";
    
                $result = getTable($conn, "reports");
    
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $details = ($row["otherreason"] != '') ? " - Details: " . $row["otherreason"] : '';

                        if (isset($_GET["id"])) {
                            if ($row["id"] == $_GET["id"]) {
    
                                echo "<h4 class=\"comment\">Username: <a href=\"user?u=" . $row["target"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["target"]])["uid"] . "</a> - Reason: " . $row["reason"] . " - Reporter: <a href=\"user?u=" . $row["reporter"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["reporter"]])["uid"] . "</a> " . $details . " </h4><hr style=\"border: 1px dotted black;\">";
                                echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/approveReport?target=" . $row["target"] . "&id=" . $row["id"] . "&reason=" . $row["reason"] . "&action=-1" . "'>Ban</a> ";
                                echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/approveReport?target=" . $row["target"] . "&id=" . $row["id"] . "&reason=" . $row["reason"] . "&action=4" . "'>Mute</a> ";
                                echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/refuseReport?target=" . $row["target"] . "&id=" . $row["id"] . "&reason=" . $row["reason"] . "'>Ignore</a> ";
                                exit();
    
                            }
                            
                        } else {
                            echo "<h4 class=\"comment\"><a style='color: black;text-decoration:none;' href='?reports&id=" . $row["id"]. "'>Username: <a href=\"user?u=" . $row["target"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["target"]])["uid"] . "</a><a style='color: black;text-decoration:none;' href='?reports&id=" . $row["id"] . "'> - Reason: " . $row["reason"] . " - Reporter: <a href=\"user?u=" . $row["reporter"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["reporter"]])["uid"] . "</a> <a style='color: black;text-decoration:none;' href='?reports&id=" . $row["id"]. "'>" . $details . " </a></h4><br>";
                        }
    
                    }
                } else {
                    echo "<h4>0 reports</h4>";
                }
    
                echo "</div>";

                exit();
            } elseif (isset($_GET["appeals"])) {
                if (!$settings->enable_appeal) {
                    echo "<p>Appeals are temporarily disabled.</p>";
                    exit();
                }
    
                echo "<div class=\"comments\">";

                echo "<h1>Appeals</h1>";
                echo "<p>Click an appeal to view it</p>";
    
                $result = getTable($conn, "appeals");
    
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $details = ($row["otherreason"] != '') ? " - Details: " . $row["otherreason"] : '';

                        if (isset($_GET["id"])) {
                            if ($row["id"] == $_GET["id"]) {
    
                                echo "<h4 class=\"comment\">Username: <a href=\"user?u=" . $row["appealer"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["appealer"]])["uid"] . "</a> - Reason: " . $row["reason"] . "</a> " . $details . " </h4><hr style=\"border: 1px dotted black;\">";
                                
                                if ($row["punishment"] == "0") {
                                    echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/approveAppeal?target=" . $row["appealer"] . "&id=" . $row["id"] . "&reason=" . $row["reason"] . "&action=-1" . "'>Unban</a> ";
                                } elseif ($row["punishment"] == "1") {
                                    echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px; background-color: darkred;\" href='includes/staff/approveAppeal?target=" . $row["appealer"] . "&id=" . $row["id"] . "&reason=" . $row["reason"] . "&action=4" . "'>Unmute</a> ";
                                }
                                echo "<a class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\" href='includes/staff/refuseAppeal?target=" . $row["appealer"] . "&id=" . $row["id"] . "&reason=" . $row["reason"] . "'>Ignore</a> ";
                                exit();
    
                            }
                            
                        } else {
                            echo "<h4 class=\"comment\"><a style='color: black;text-decoration:none;' href='?appeals&id=" . $row["id"]. "'>Username: <a href=\"user?u=" . $row["appealer"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["appealer"]])["uid"] . "</a><a style='color: black;text-decoration:none;' href='?appeals&id=" . $row["id"] . "'> - Reason: " . $row["reason"] . "</a> <a style='color: black;text-decoration:none;' href='?appeals&id=" . $row["id"]. "'>" . $details . " </a></h4><br>";
                        }
    
                    }
                } else {
                    echo "<h4>0 appeals</h4>";
                }

                echo "</div>";

                exit();
            } elseif (isset($_GET["log"])) {

                echo "<div class=\"comments\">";
    
                $result = getTable($conn, "log", "", true);
    
                if ($result->num_rows > 0) {
                    echo "<h2>Log</h2>";

                    while($row = $result->fetch_assoc()) {
                        $type = "";

                        switch ($row["type"]) {
                            case '0':
                                $type = "User";
                                break;
                            case '1':
                                $type = "Mod";
                                break;
                            case '2':
                                $type = "Admin";
                                break;
                            case '3':
                                $type = "DeleteComment";
                                break;
                            case '4':
                                $type = "(Un)Verify";
                                break;
                            case '5':
                                $type = "(Un)Mute";
                                break;
                            case '6':
                                $type = "Undelete";
                                break;
                            case '-1':
                                $type = "(Un)Ban";
                                break;
                            default:
                                $type = $row["type"];
                                break;
                        }

                        $type_str = "Type: " . $type;

                        if (str_starts_with($type, "CID:")) {
                            $type = explode(":", $type)[1];

                            $type_str = "<a href=\"reply?c=" . $row["targetsUid"] . "#" . $row["targetsUid"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">Comment ID: " . $type . "</a>";
                        }

                        $action = ltrim(preg_replace('/([A-Z])/', ' $1', $row["action"]));

                        $target = (!str_starts_with($type, "CID:") ? "Target: <a href=\"user?u=" . $row["targetsUid"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["targetsUid"]])["uid"] . "</a> - " : "");

                        echo "<h4 class=\"comment\">[" . $row["id"] . "] Username: <a href=\"user?u=" . $row["uid"] . "\" target=\"_blank\" style=\"text-decoration: none; color: green;\">" . getTable($conn, "users", ["id", $row["uid"]])["uid"] . "</a> - " . $target . "Action: " . $action . "</a> - " . $type_str . " </h4> ";
                    }
                } else {
                    echo "<h2>Log is empty</h2>";
                }

                echo "</div>";

                exit();
            } elseif (isset($_GET["eval"])) {
                if ($_SESSION["rank"] < 3) {
                    header("location: moderation?error=authfailed");
                    exit();
                }
                if ($settings->enable_eval_private && $_SERVER['HTTP_HOST'] != "localhost" && !$settings->enable_eval_public || !$settings->enable_eval_private && !$settings->enable_eval_public) {
                    header("location: moderation?error=authfailed");
                    exit();
                }

                echo "<h1>Evaluate Command</h1>";

                if (isset($_GET["result"])) {
                    echo "<h2>Result: <code>" . htmlspecialchars(urldecode($_GET["result"]), ENT_QUOTES, "UTF-8") . "</code></h2>";
                }
                elseif (isset($_GET["errresult"])) {
                    echo "<h2>Error: <code>" . htmlspecialchars(urldecode($_GET["errreult"]), ENT_QUOTES, "UTF-8") . "</code></h2>";
                }
                
                if ($settings->enable_admin_panel) {
                    ?>

                        <form action="includes/staff/eval" method="post">
                            <textarea type="text" name="cmd" placeholder="Evaluate..." resizable value="<?= htmlspecialchars($usernameValue, ENT_QUOTES, "UTF-8") ?>"></textarea></br></br>
                            <button type="submit" name="submit" class="button">Confirm</button>
                        </form>

                    <?php
                } else {
                    echo "<p>Admin Panel is temporarily disabled.</p>";
                }

                exit();
            } else {
                if (!$settings->enable_report) {
                    echo "<p>Reporting is temporarily disabled.</p>";
                } else {
                    echo "<a href=\"?reports\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Reports</a> ";
                }
                if (!$settings->enable_appeal) {
                    echo "<p>Appeals are temporarily disabled.</p>";
                } else {
                    echo "<a href=\"?appeals\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Appeals</a> ";
                }
    
                echo "<a href=\"?suggestions\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Suggestions</a> ";
                echo "<a href=\"?log\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Log</a> ";

                if ($_SESSION["rank"] >= 3) {
                    if ($settings->enable_eval_private || $settings->enable_eval_public) {
                        echo "<a href=\"?eval\" class=\"button\" style=\"font: 400 13.3333px Arial; font-size: 16px;\">Eval</a> ";
                    }
                }
            }
        
            echo "<h1>Admin Panel</h1>";

            $size = 5;
            if ($_SESSION["rank"] >= 3) $size = 8;
            
            if ($settings->enable_admin_panel) {
                ?>

                    <form action="includes/staff/admin" method="post">
                        <input type="text" name="username" placeholder="Username..." value="<?= htmlspecialchars($usernameValue, ENT_QUOTES, "UTF-8") ?>"></br></br>
                        <select name="action" size="<?= $size ?>">
                            <option value="0" <?php if ($selectedValue == "user"): ?> selected="selected" <?php endif; ?> >Set User</option>
                            <option value="1" <?php if ($selectedValue == "mod"): ?> selected="selected" <?php endif; ?>>Set Moderator</option>
                            <option value="3" <?php if ($selectedValue == "delc"): ?> selected="selected" <?php endif; ?>>Delete Comment</option>
                            <option value="5" <?php if ($selectedValue == "unm"): ?> selected="selected" <?php endif; ?>>(Un)Mute</option>
                            <option value="-1"<?php if ($selectedValue == "unb"): ?> selected="selected" <?php endif; ?>>(Un)Ban</option>
                            <?php
                                if ($_SESSION["rank"] >= 3) {
                                    echo "<option value='4' " . ($selectedValue == "unv" ? "selected=\"selected\"" : "") . ">(Un)Verify</option>";
                                    echo "<option value='6' " . ($selectedValue == "rec" ? "selected=\"selected\"" : "") . ">Recover Account</option>";
                                    echo "<option value='2' " . ($selectedValue == "admin" ? "selected=\"selected\"" : "") . ">Set Admin</option>";
                                }
                            ?>
                        </select></br></br>
                        <button type="submit" name="submit" class="button">Confirm</button>
                    </form>

                <?php
            } else {
                echo "<p>Admin Panel is temporarily disabled.</p>";
            }
        
        }

    ?>
</body>
</html>