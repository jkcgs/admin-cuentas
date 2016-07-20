<?php defined("INCLUDED") or die("Acceso denegado."); try_logged();

$q1 = $db->query("SELECT * FROM deudores order by nombre asc");
if(!$q1) {
    throw_error($db->error);
}

$q2 = $db->query(
    "SELECT d.id, d.deudor, de.nombre AS nombreDeudor, d.descripcion, d.monto, d.fecha, d.pagada ".
    "FROM deudas AS d INNER JOIN deudores AS de ON d.deudor = de.id ".
    "ORDER BY id DESC, fecha DESC"
);
if(!$q2) {
    throw_error($db->error);
}

echo json_encode([
    "success" => true,
    "debtors" => dbGetAll($q1),
    "debts" => dbGetAll($q2)
]);
