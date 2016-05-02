<?php define("INCLUDED", 1);
include "../include/config.php";
include "../include/auth.php";
include "../include/functions.php";
include "../include/db.php";

$k = array_keys($_GET);
if(count($k) == 0) {
	header("Location:../");
	die();
}

$act = preg_replace("/[^a-zA-Z0-9\-_\/]/", "", $k[0]);
if(strpos($act, "/") === 0 || $act == "index" || !file_exists($act.".php")) {
    header("Content-Type: text/json");
    die(jerr("Acción incorrecta"));
}

include $act.".php";