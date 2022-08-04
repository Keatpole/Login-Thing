<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

session_start();

$session = getTable($conn, "sessions", ["id", $_POST["id"]]);

if ($session["userid"] != $_SESSION["id"]) {
    header("location: ../../login?error=authfailed");
    exit();
}

deleteTable($conn, "sessions", ["id", $_POST["id"]]);

$error = isset($_GET["error"]) ? $_GET["error"] : "";
$error = !$error && isset($_POST["error"]) ? $_POST["error"] : "";
$website = isset($_GET["website"]) ? $_GET["website"] : "";
$website = !$website && isset($_POST["website"]) ? $_POST["website"] : ".";

if ($error) {
    header("location: ../../" . $website . "?error=" . $error);
} else {
    header("location: ../../.");
}
exit();