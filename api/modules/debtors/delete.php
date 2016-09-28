<?php defined('INCLUDED') or die('Denied'); try_logged();

$id = verify_id('deudores');

$q = $db->query("DELETE FROM deudas WHERE deudor = '$id'");
if(!$q) {
    throw_error("No se pudo eliminar las deudas del deudor seleccionado: " . $db->error);
}

delete_obj('deudores');