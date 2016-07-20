<?php defined("INCLUDED") or die("Acceso denegado."); try_logged();

if(!isset($_GET['id'])) {
    $q = $db->query("SELECT * FROM cuentas order by id desc, fecha_facturacion desc");
    if(!$q) {
        throw_error($db->error);
    }

    $r = array();
    while ($row = $q->fetch_assoc()) {
	    $r[] = $row;
	}
    
    echo json_encode($r);
} else {
    echo get_id('cuentas');
}
