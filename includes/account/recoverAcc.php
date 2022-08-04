<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

session_start();

if ($_SESSION["rank"] < 3 || isset($_SESSION["tempacc"]) && $_SESSION["tempacc"]) {
    $_SESSION["ptid"] = $_GET["ptid"];
    header("location: ../../resetpass?t=" . $_GET["t"]);
    exit();
}
if (!isset($_GET["u"]) || !$settings->enable_reset_pass) {
    header("location: ../../.?error=usernotfound");
    exit();
}

$u = $_GET["u"];

$user = getTable($conn, "users", ["uid", $u]);
if ($user == null) $user = getTable($conn, "users", ["email", $u]);
if ($user == null) {
    header("location: ../../login?error=usernotfound");
    exit();
}

$rng = bin2hex(random_bytes(36));

session_start();

$ptid = insertTable($conn, "passwordtokens", ["userid" => $user["id"], "token" => password_hash($rng, PASSWORD_DEFAULT), "expiredate" => date("Y-m-d H:i:s", time() + 3600)]);

echo "http://{$_SERVER['HTTP_HOST']}/LoginThing/includes/account/recoverAcc?t=" . $rng . "&ptid=" . $ptid;
exit();