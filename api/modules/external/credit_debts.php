<?php defined("INCLUDED") or die("Denied"); try_logged();

require_once "includes/credit_banks/ripley.php";
$data = array(
    'rut' => $config['external']['ripley']['user'],
    'password' => $config['external']['ripley']['pass']
);

if(empty($data['rut']) || empty($data['password'])) {
    throw_error("No se ha configurado la cuenta remota.");
}

$ripley = new Credit_Ripley($data['rut'], $data['password']);

//// Login
if(!$ripley->login()){
    throw_error("No se pudo iniciar sesión en el sitio externo");
}

$movimientos = $ripley->getAccounts();
if(!$movimientos) {
    throw_error("No se pudo obtener los datos desde el sitio externo");
}

throw_data($movimientos);
