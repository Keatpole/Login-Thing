<?php

require_once "../other/functions.php";
require_once "../other/dbh.php";

session_start();
session_unset();
session_destroy();

header("location: ../../.");
exit();