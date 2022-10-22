<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

if (!isset($_POST["submit"]) || !$settings->enable_login) {
    header("location: ../../seccodes?error=invaliddata");
    exit();
}

$q = $_POST["q"];
$a = $_POST["a"];
$pwd = $_POST["pwd"];

if (empty($q) || empty($a) || empty($pwd)) {
    header("location: ../../seccodes?error=emptyinput");
    exit();
}

$checkPwd = password_verify($pwd, $_USER["pwd"]);

if (!$checkPwd) {
    header("location: ../../seccodes?error=wronglogin");
    exit();
}
else {
    $table = getTable($conn, "securitycodes", ["uuid", $_SESSION["id"]]);
    if (!$table) $table = getTable($conn, "securitycodes", ["id", insertTable($conn, "securitycodes", ["uuid" => $_SESSION["id"]])]);

    $questions = $table["questions"] ? $table["questions"] . "," . $q : $q;
    $answers = $table["answers"] ? $table["answers"] . "," . password_hash($a, PASSWORD_DEFAULT) : password_hash($a, PASSWORD_DEFAULT);

    updateTable($conn, "securitycodes", "questions", $questions, ["id", $table["id"]]);
    updateTable($conn, "securitycodes", "answers", $answers, ["id", $table["id"]]);
    
    header("location: ../../seccodes?error=none");
    exit();
}