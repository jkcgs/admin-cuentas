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

// Bancos
$banks = [];
$path_debit = __DIR__ . "/../../includes/banks/";
$path_credit = __DIR__ . "/../../includes/credit_banks/";
$dir_debit = opendir($path_debit);
$dir_credit = opendir($path_credit);

// Débito
while($file = readdir($dir_debit)) {
    if(!is_file($path_debit . $file)) continue;

    require_once $path_debit . $file;
    $file_sub = substr($file, 0, strlen($file) - 4);
    $class_name = "Bank_" . ucfirst($file_sub);
    $ins = new $class_name('stub', 'stub');

    $banks[] = [
        "id" => $file_sub,
        "name" => $ins->getBankName(),
        "type" => 1
    ];
}

// Crédito
while($file = readdir($dir_credit)) {
    if(!is_file($path_credit . $file)) continue;

    require_once $path_credit . $file;
    $file_sub = substr($file, 0, strlen($file) - 4);
    $class_name = "Credit_" . ucfirst($file_sub);
    $ins = new $class_name('stub', 'stub');

    $banks[] = [
        "id" => $file_sub,
        "name" => $ins->getBankName(),
        "type" => 2
    ];
}

throw_data([
    "accounts" => $accounts,
    "types" => $types,
    "banks" => $banks
]);