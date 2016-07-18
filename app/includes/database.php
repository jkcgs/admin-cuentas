<?php defined("INCLUDED") or die("nel");

$db_error = false;
@$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

/* Renombrar errores */
switch ($db->connect_errno) {
	case null:
        /* No hubo error */
		break;

	case 1045:
		die("No se pudo conectar al servidor MySQL: Permiso denegado (usando contraseña)");
		break;
	
	default:
		die("Fallo al conectar a MySQL: #{$db->connect_errno} {$db->connect_error}");
		break;
}


function get_id($obj, $id = true) {
    global $db;

    if($id === true) {
        $id = verificar_id($obj);
    }

    $s = $db->prepare("SELECT * FROM $obj WHERE id = ? LIMIT 1");
    $s->bind_param('i', $id);
    $s->execute();
    
    if($s->error) {
        return jerr($s->error);
    } else {
        $r = stmt_fetch_all($s);
        if ($r == NULL) {
            return throw_error("No se encontró un elemento con el ID seleccionado");
        } else {
            return json_encode($r);
        }
    }
}