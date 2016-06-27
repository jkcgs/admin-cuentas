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
            return jerr("No se encontró un elemento con el ID seleccionado");
        } else {
            return json_encode($r);
        }
    }
}

function stmt_fetch_all($stmt) {
    $res = array();
    $column = array();
    $stmt->execute();
    $metaResults = $stmt->result_metadata();
    $fields = $metaResults->fetch_fields();
    $statementParams='';
    //build the bind_results statement dynamically so I can get the results in an array
    foreach($fields as $field){
        if(empty($statementParams)){
            $statementParams.="\$column['".$field->name."']";
        } else {
            $statementParams.=", \$column['".$field->name."']";
        }
    }

    $statment="\$stmt->bind_result($statementParams);";
    eval($statment);
    while($stmt->fetch()){
        //Now the data is contained in the assoc array $column. Useful if you need to do a foreach, or 
        //if you're lazy and didn't want to write out each param to bind.
        $res[] = $column;
    }

    return $res;
}


function borrar_obj($obj, $id = true, $ddie = true) {
    global $db;
    if($id === true) {
        $id = verificar_id($obj);
    }

    $s = $db->prepare("DELETE FROM $obj WHERE id = ? LIMIT 1");
    $s->bind_param('i', $id);
    $s->execute();

    if($ddie) {
        header("Content-Type: text/json");
        if($s->error) {
            die(jerr($s->error));
        } else {
            die(jmsg("Eliminado correctamente"));
        }
    } else {
        return $s->error ? $s->error : true;
    }
    
}

function existe($itm, $id) {
    require_once "db.php";
    global $db;
    
    $s = $db->prepare("SELECT * FROM $itm WHERE id = ? LIMIT 1");
    if(!$s) {
        throw new Exception($db->error);
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

function existe_cuenta($id) {
    return existe('cuentas', $id);
}

function existe_deudor($id) {
    return existe('deudores', $id);
}

function verificar_id($obj) {
    if(!isset($_GET['id'])) {
        die('{"error":"ID no ingresado"}');
    } else {
        $id = $_GET['id'];
        if((string)(int)$id != $id) {
            header("Content-Type: text/json");
            die('{"error":"ID no numérica"}');
        }
        
        if(!existe($obj, $id)) {
            header("Content-Type: text/json");
            die('{"error":"La cuenta no existe", "ne": true}');
        }

        return $id;
    }
}
