<?php defined("INCLUDED") or die("Denied"); try_logged();

if(!isset($_POST["ids"])) {
    throw_error("No se enviaron IDs");
}

$ids = $_POST["ids"];

foreach($ids as $id) {
    if(!preg_match("/[0-9]+/", $id)) {
        throw_error("Se envió un ID incorrecto");
        break;
    }
}

$sql_ids = join(", ", $ids);
$sql_check = "SELECT id FROM cuentas WHERE id IN ($sql_ids) AND usuario_id = $UID";
$db->query($sql_check);

if($db->affected_rows == -1) {
    throw_error("DB Error: " . $db->error);
}

if($db->affected_rows != count($ids)) {
    throw_error("Uno o más IDs de cuentas no existen");
}

$sql_upd = "UPDATE cuentas SET pagado = '1' WHERE id IN ($sql_ids) AND usuario_id = $UID";
$db->query($sql_upd);

if($db->affected_rows == -1) {
    throw_error("DB Error: " . $db->error);
}

throw_success();