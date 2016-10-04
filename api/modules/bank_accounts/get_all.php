<?php defined("INCLUDED") or die("Denied"); try_logged();

$res_accounts = $db->query("SELECT id, tipo, nombre, user FROM cuenta_bancaria WHERE usuario_id = $UID;");
if(!$res_accounts) {
    throw_error("Error de DB: " . $db->error);
}

$res_types = $db->query("SELECT * FROM tipo_cuenta_bancaria;");
if(!$res_types) {
    throw_error("Error de DB: " . $db->error);
}

$accounts = db_fetch_all($res_accounts);
$types = db_fetch_all($res_types);
for($i = 0; $i < count($types); $i++) {
    $types[$i]['nombre'] = utf8_encode($types[$i]['nombre']);
}

throw_data([
    "accounts" => $accounts,
    "types" => $types,
    "banks" => getAllBanks()
]);