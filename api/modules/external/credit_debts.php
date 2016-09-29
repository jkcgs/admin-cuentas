<?php defined("INCLUDED") or die("Denied"); try_logged();

require_once "includes/encryption.class.php";
require_once "includes/database.php";
require_once "includes/credit_banks/ripley.php";
$res = $db->query("SELECT user, password FROM cuenta_bancaria WHERE usuario_id = $UID AND tipo = 2 AND nombre = 'ripley' LIMIT 1");
if($res->num_rows < 1) {
    throw_error("No hay una cuenta configurada");
}

$data = $res->fetch_assoc();
$ripley = new Credit_Ripley($data['user'], Encryption::decrypt_user_pass($data['password']));

//// Login
if(!$ripley->login()){
    throw_error("No se pudo iniciar sesión en el sitio externo");
}

$movimientos = $ripley->getAccounts();
if(!$movimientos) {
    throw_error("No se pudo obtener los datos desde el sitio externo");
}

throw_data($movimientos);
