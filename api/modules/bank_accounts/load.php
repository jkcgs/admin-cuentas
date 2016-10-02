<?php defined("INCLUDED") or die("Denied"); try_logged();

$type_id = count($route_args) > 0 ? trim($route_args[0]) : "";

if(empty($type_id)) {
    throw_error("Tipo de cuenta no enviado");
    exit;
}

$types_id = [];
$types_res = $db->query("SELECT id FROM tipo_cuenta_bancaria");
if(!$types_res) {
    throw_error("Error de DB: " . $db->error);
}

while($type = $types_res->fetch_assoc()) {
    $types_id[] = $type["id"];
}
$types_res->close();

if(!in_array($type_id, $types_id)) {
    throw_error("Tipo de cuenta incorrecto");
}

$q = $db->query("SELECT * from cuenta_bancaria WHERE usuario_id = $UID AND tipo = $type_id");
if($q->num_rows < 1) {
    throw_data([]);
    exit;
}

$accounts = [];
while($acc = $q->fetch_assoc()) {
    $dec_pass = Encryption::decrypt_user_pass($acc["password"]);
    if(!$dec_pass) throw_error("No se pudo desencriptar una contraseña");
    
    $acc["password"] = $dec_pass;
    $accounts[] = $acc;
}

$acc_data = [];
$mod_path = "includes/";
$mod_prefix = "";

switch ($type_id) {
    case "1":
        $mod_path .= "banks";
        $mod_prefix = "Bank_";
        break;
    case "2":
        $mod_path .= "credit_banks";
        $mod_prefix = "Credit_";
        break;
    default:
        throw_error("Error desconocido");
}

foreach ($accounts as $acc) {
    if(!file_exists("$mod_path/{$acc['nombre']}.php")) continue;

    require_once "$mod_path/{$acc['nombre']}.php";
    $class_name = $mod_prefix . ucfirst($acc['nombre']);
    $ins = new $class_name($acc['user'], $acc['password']);

    $response = $ins->getAccounts(true);
    @$ins->logout();

    if($response && count($response) > 0) {
        $acc_data = array_merge($acc_data, $response);
    } else {
        throw_error($response);
    }
}

throw_data($acc_data);