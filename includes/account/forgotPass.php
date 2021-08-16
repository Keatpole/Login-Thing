<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_POST["submit"]) || !$settings->enable_reset_pass) {
    header("location: ../../login");
    exit();
}

$email = $_POST["email"];

$user = getTable($conn, "users", ["uid", $email]);
if ($user == null) $user = getTable($conn, "users", ["email", $email]);
if ($user == null) {
    header("location: ../../login?error=usernotfound");
    exit();
}

$rng = bin2hex(random_bytes(128));

session_start();

$_SESSION["passtoken"] = [password_hash($rng, PASSWORD_DEFAULT), $user["id"], date("F j, Y, H:i", strtotime('+1 hour'))];

$msg = wordwrap("You have requested a password or username reset.\nClick here to reset it.\n\nhttp://{$_SERVER['HTTP_HOST']}/LoginThing/resetpass?t=" . $rng . "\n\nIf you did not request this reset, don't worry.\nThey have a 1 in 340 undecillion (340 followed by 36 zeroes) chance of guessing this token.\n\nRemember to open this link in the same window as the one you requested the password reset.", 70);
mail($user["email"], "Password Reset", $msg);

header("location: ../../login?error=noneemail");