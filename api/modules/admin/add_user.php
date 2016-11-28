<?php defined("INCLUDED") or die("Denied");
require_once "includes/encryption.class.php";

$gump = new GUMP();
$_POST = $gump->sanitize($_POST);

$gump->validation_rules(
    array(
        'username' => 'required|text',
        'password' => 'required',
        'is_admin' => 'exact_len,1|contains,1 0',
        'enabled'  => 'exact_len,1|contains,1 0'
    )
);

$gump->filter_rules(
    array(
        'username' => 'trim',
        'password' => 'trim',
        'is_admin' => 'trim|default,0',
        'enabled'  => 'trim|default,1'
    )
);

$data = $gump->run($_POST);

if ($data === false) {
     throw_error(var_export($gump->get_errors_array(), true));
}

if(get_user($data['username'])) {
    throw_error("Ya existe un usuario con ese nombre");
}

$stmt = $db->prepare(
    'INSERT INTO usuarios (user, password, enabled, is_admin) VALUES (?, ?, ?, ?)'
);
   
if (!$stmt) {
    throw_error($db->error);
}

$data['password'] = Encryption::password_hash(base64_decode($data['password']));
$stmt->bind_param(
    'ssss', $data['username'], $data['password'], $data['enabled'], $data['is_admin']
);
$stmt->execute();

if (!empty($stmt->error)) {
    throw_error($stmt->error);
}

$data['id'] = $stmt->insert_id;
unset($data['password']);
throw_success($data);