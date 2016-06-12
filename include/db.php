<?php defined("INCLUDED") or die("nel");

$db_error = false;
@$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

switch ($db->connect_errno) {
	case null:
		break;

	case 1045:
		die("No se pudo conectar al servidor MySQL: Permiso denegado (usando contraseña)");
		break;
	
	default:
		die("Fallo al conectar a MySQL: #{$db->connect_errno} {$db->connect_error}");
		break;
}
