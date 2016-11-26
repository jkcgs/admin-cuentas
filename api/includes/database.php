<?php defined("INCLUDED") or die("Denied");
if(!isset($config)) {
    $config = include dirname(__FILE__) . "/../config.php";
}

// Se muestra en el mensaje de error cuando se utilizó configuración por defecto
$db_defaults = false;

if(empty($config['db_host'])) {
    $config['db_host'] = "localhost";
    $db_defaults = true;
}

if(empty($config['db_user']) == "") {
    $config['db_user'] = "root";
    $db_defaults = true;
}

if(empty($config['db_name'])) {
    throw_error("No se ha definido el nombre de la base de datos");
    exit;
}

$db_error = false;
@$db = new mysqli($config['db_host'], $config['db_user'], $config['db_pass']);
$def_info = $db_defaults ? " - Se utilizó configuración por defecto." : "";

/* Renombrar errores */
switch ($db->connect_errno) {
	case null:
        /* No hubo error */
		break;

	case 1045:
		throw_error("No se pudo conectar al servidor MySQL: Permiso denegado (usando contraseña)" . $def_info);
		break;
	
	default:
		throw_error("Fallo al conectar a MySQL: #{$db->connect_errno} {$db->connect_error}" . $def_info);
		break;
}

if(!$db->select_db($config['db_name'])) {
    throw_error("No se pudo seleccionar la base de datos");
    exit;
}

// Usar UTF-8
$db->query('SET CHARACTER SET utf8');

function db_fetch_all($q) {
    $r = array();
    while ($row = $q->fetch_assoc()) {
	    $r[] = $row;
	}

    return $r;
}


function get_id($obj, $id = true, $owner_id = null) {
    global $db;

    if($id === true) {
        $id = verify_id($obj);
    }

    if($owner_id) {
        $s = $db->prepare("SELECT * FROM $obj WHERE id = ? AND usuario_id = ? LIMIT 1");
        $s->bind_param('ii', $id, $owner_id);
    } else {
        $s = $db->prepare("SELECT * FROM $obj WHERE id = ? LIMIT 1");
        $s->bind_param('i', $id);
    }
    
    $s->execute();
    
    if($s->error) {
        return jerr($s->error);
    } else {
        $r = $s->get_result();
        if ($r == NULL) {
            throw_error("No se encontró un elemento con el ID seleccionado");
        } else {
            return json_encode($r->fetch_assoc());
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

function delete_obj($obj, $id = true, $owner_id = null) {
    global $db;
    if($id === true) {
        $id = verify_id($obj, $owner_id, $id);
    }

    if($owner_id) {
        $s = $db->prepare("DELETE FROM $obj WHERE id = ? AND usuario_id = ? LIMIT 1");
        if(!$s) {
            throw_error($db->error);
        }
        $s->bind_param('ii', $id, $owner_id);
    } else {
        $s = $db->prepare("DELETE FROM $obj WHERE id = ? LIMIT 1");
        if(!$s) {
            throw_error($db->error);
        }
        $s->bind_param('i', $id);
    }
    
    $s->execute();

    if($s->error) {
        throw_error($s->error);
    } else {
        throw_success();
    }
}

function exists_obj($itm, $id, $owner_id = null) {
    global $db;
    
    if($owner_id) {
        $s = $db->prepare("SELECT * FROM $itm WHERE id = ? AND usuario_id = ? LIMIT 1");
        $s->bind_param('ii', $id, $owner_id);
    } else {
        $s = $db->prepare("SELECT * FROM $itm WHERE id = ? LIMIT 1");
        $s->bind_param('i', $id);
    }
    
    if(!$s) {
        throw_error($db->error);
    }

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
function verify_id($obj, $owner_id = null, $id = true) {
    $id = $id === true && isset($_GET['id']) ? $_GET['id'] : $id;
    if($id === null) {
        throw_error("ID no ingresado");
    } else {
        if((string)(int)$id != $id || !preg_match("/[0-9]/", $id)) {
            throw_error("ID no numérica");
        }
        
        if(!exists_obj($obj, $id, $owner_id)) {
            throw_error("El elemento no existe");
        }

        return $id;
    }
}


function get_user_data() {
    if(!isset($_SESSION['logged_id'])) {
        return false;
    }

    if(!preg_match("/^[0-9]+$/", $_SESSION['logged_id'])) {
        return null;
    }

    global $db;
    $id = $db->escape_string($_SESSION['logged_id']);
    $res = $db->query("SELECT * from usuarios WHERE id = $id LIMIT 1");

    if(!$res) {
        throw new Exception($db->error);
    }

    if($res && $res->num_rows > 0) {
        return $res->fetch_assoc();
    } else {
        return null;
    }

}