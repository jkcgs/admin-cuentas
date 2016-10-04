<?php defined("INCLUDED") or die("Denied"); try_logged();

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);
$banks = getAllBanks();

$gump->validation_rules(
    array(
        'tipo' => 'required|integer',
        'nombre' => 'required|text',
        'user' => 'required|text',
        'password' => 'required|text'
    )
);

$gump->filter_rules(
    array(
        'tipo' => 'trim',
        'nombre' => 'trim',
        'user' => 'trim',
        'password' => 'trim'
    )
);

$data = $gump->run($_POST);
if ($data === false) {
    throw_error(var_export($gump->get_errors_array(), true));
}

$bank_ok = false;
foreach ($banks as $bank) {
    if($bank['id'] == $data['nombre'] && $bank['type'] == $data['tipo']) {
        $bank_ok = true;
        break;
    }
}

if(!$bank_ok) {
    throw_error("El banco es incorrecto");
}

$data['password'] = Encryption::encrypt_user_pass($data['password']);

$sql = "INSERT INTO cuenta_bancaria (usuario_id, tipo, nombre, user, password) VALUES (%s, '%s', '%s', '%s', '%s')";
$q = $db->query(sprintf($sql, $UID,
    $db->escape_string($data['tipo']),
    $db->escape_string($data['nombre']),
    $db->escape_string($data['user']),
    $data['password']
));

if(!$q) {
    throw_error("Error de DB: " . $db->error);
}

$data['id'] = $db->insert_id;
unset($data['password']);
throw_success($data);