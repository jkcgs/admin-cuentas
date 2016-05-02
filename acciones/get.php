<?php defined("INCLUDED") or die("nel");

header("Content-Type: text/json");
if(!isset($_GET['t']) || empty($_GET['t'])) {
	die(jerr("Tipo no definido"));
}

$ex = array(
	"cuentas" => "order by id desc, fecha_facturacion desc",
	"haber" => "order by id desc, fecha desc",
	"deudores" => "order by id asc"
);

if(!isset($ex[$_GET['t']])) {
	die(jerr("Tipo incorrecto"));
}

if(!isset($_GET['id'])) {
    $q = $db->query("SELECT * FROM {$_GET['t']} {$ex[$_GET['t']]}");

    $r = array();
    while ($row = $q->fetch_assoc()) {
	    $r[] = $row;
	}
    echo json_encode($r);
} else {
    echo get_id($_GET['t']);
}
