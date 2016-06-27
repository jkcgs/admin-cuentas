<?php defined("INCLUDED") or die("nel");

if(!isset($_GET['action'])) {
	header("Location:../");
	die();
}

$act = preg_replace("/[^a-zA-Z0-9\-_\/]/", "", $_GET['action']);
$act_path = "app/controller/actions/$act.php";

if(strpos($act, "/") === 0 || $act == "index") {
	header("Content-Type: text/json");
    die(jerr("Acción incorrecta"));
}

if(!file_exists($act_path)) {
	header("Content-Type: text/json");
    die(jerr("La acción no existe: " . $act_path));
}

require($act_path);