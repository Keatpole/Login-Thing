<?php

session_start();

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_SESSION["id"]) || getTable($conn, "users", ["id", $_SESSION["id"]])["rank"] < 3 || !isset($_POST["submit"]) || !$settings->enable_eval_public && !$settings->enable_eval_private) {
    header("location: ../../.?error=authfailed");
    exit();
}

if ($settings->enable_eval_private && $_SERVER['HTTP_HOST'] != "localhost" && !$settings->enable_eval_public) {
    header("location: ../../moderation?error=authfailed");
    exit();
}

try {
    $result = eval($_POST["cmd"]);
} catch (ParseError $e) {
    header("location: ../../moderation?eval&error=evalfailed&errresult=" . urlencode($e->getMessage()));
    exit();
}

if ($result == null) {
    header("location: ../../moderation?eval&error=none");
    exit();
}

if (is_array($result)) {
    $result = json_encode($result);
}

header("location: ../../moderation?eval&error=none&result=" . urlencode($result));

exit();
