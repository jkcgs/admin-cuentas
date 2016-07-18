<?php defined("INCLUDED") or die("Acceso denegado.");

if(!isset($_POST['username']) || !isset($_POST['password'])) {
    throw_error("Por favor ingrese el usuario y la contraseña.");
}

$user = $_POST['username'];
$pass = base64_decode($_POST['password']);

if($user != $config['web_user'] || $pass != $config['web_pass']) {
    throw_error("Combinación usuario/contraseña incorrecta");
}

$_SESSION["logged"] = true;
die(json_data(["logged" => true]));