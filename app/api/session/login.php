<?php defined("INCLUDED") or die("Acceso denegado.");

// Inicio de sesión

if(!isset($_POST['username']) || !isset($_POST['password'])) {
    throw_error("Por favor ingrese el usuario y la contraseña.");
}

// Cargar datos desde POST (la contraseña debe enviarse codificada en base64)
$user = $_POST['username'];
$pass = base64_decode($_POST['password']);

// Ejecutar sentencia sql
$user = $db->escape_string($user);
$sql = "SELECT user, password FROM usuarios WHERE user = '$user'";
$res = $db->query($sql);

if(!$res) {
	throw_error("No se pudo ejecutar una consulta en la base de datos: " . $db->error);
}

// Verificar si existe el usuario y si la contraseña está correcta
$user_data = $res->fetch_assoc();
if($db->affected_rows < 1 || !password_verify($pass, $user_data['password'])) {
    throw_error("Combinación usuario/contraseña incorrecta");
}

// Registrar sesión iniciada
$_SESSION["logged"] = true;
$_SESSION["logged_user"] = $user_data['user'];
throw_success();