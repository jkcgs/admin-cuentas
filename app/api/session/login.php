<?php defined("INCLUDED") or die("Acceso denegado.");

if(!isset($_POST['username']) || !isset($_POST['password'])) {
    throw_error("Por favor ingrese el usuario y la contraseña.");
}

$user = $_POST['username'];
$pass = base64_decode($_POST['password']);

if($user != $config['web-user'] || $pass != $config['web-pass']) {
    throw_error("Combinación usuario/contraseña incorrecta");
}

$_SESSION["logged"] = true;
throw_success();