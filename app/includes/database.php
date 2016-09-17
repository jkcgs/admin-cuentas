<?php defined("INCLUDED") or die("Denied");

$db_error = false;
@$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

/* Renombrar errores */
switch ($db->connect_errno) {
	case null:
        /* No hubo error */
		break;

	case 1045:
		throw_error("No se pudo conectar al servidor MySQL: Permiso denegado (usando contraseña)");
		break;
	
	default:
		throw_error("Fallo al conectar a MySQL: #{$db->connect_errno} {$db->connect_error}");
		break;
}

function dbGetAll($q) {
    $r = array();
    while ($row = $q->fetch_assoc()) {
	    $r[] = $row;
	}

    return $r;
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

function get_by_id($obj, $id = true) {
    global $db;

    if($id === true) {
        $id = verificar_id($obj);
    }

    $q = $db->query("SELECT * FROM $obj WHERE id = $id LIMIT 1");
    
    if(!$q) {
        return null;
    } else {
        return $q->fetch_assoc();
    }
}

function delete_obj($obj, $id = true, $ddie = true) {
    global $db;
    if($id === true) {
        $id = verify_id($obj);
    }

    $s = $db->prepare("DELETE FROM $obj WHERE id = ? LIMIT 1");
    $s->bind_param('i', $id);
    $s->execute();

    if($ddie) {
        if($s->error) {
            throw_error($s->error);
        } else {
            throw_success();
        }
    } else {
        return $s->error ? $s->error : true;
    }
}

function exists_obj($itm, $id) {
    global $db;
    
    $s = $db->prepare("SELECT * FROM $itm WHERE id = ? LIMIT 1");
    if(!$s) {
        throw_error($db->error);
    }

    $s->bind_param('i', $id);
    $s->execute();
    $s->store_result();
    
    if($s->error) {
        return null;
    } else {
        $num = $s->num_rows;
        $s->close();
        return $num > 0;
    }
}

// Checks if an ID has been sent and if exists
function verify_id($obj) {
    if(!isset($_GET['id'])) {
        throw_error("ID no ingresado");
    } else {
        $id = $_GET['id'];
        if((string)(int)$id != $id) {
            throw_error("ID no numérica");
        }
        
        if(!exists_obj($obj, $id)) {
            throw_error("ID no numérica");
            die('{"error":"La cuenta no existe", "ne": true}');
        }

        return $id;
    }
}
