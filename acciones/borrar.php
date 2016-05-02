<?php defined("INCLUDED") or die("nel");
if(!isset($_GET['t'])) {
	die(jerr("Tipo no definido"));
}

if(!in_array($_GET['t'], array("cuentas", "deudores", "haber"))) {
	die(jerr("Tipo incorrecto"));
}

borrar_obj($_GET['t']);