<?php defined("INCLUDED") or die("nel");

$is_win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
define('NULFILE', $is_win ? 'NUL' : '/dev/null');

include "gump.class.php";
GUMP::add_filter("upper", function($value, $params = NULL) {
    return strtoupper($value);
});
GUMP::add_filter("default", function($value, $params = array('')) {
    return empty($value) ? $params[0] : $value;
});
GUMP::add_validator("yearmonth", function($field, $input, $param = NULL) {
    $val = $input[$field];
    return strlen($val) == 7 && preg_match("/[0-9]{4}\-[0-9]{2}/", $val);
});
GUMP::add_validator("text", function($field, $input, $param = NULL) {
    $val = $input[$field];
    return preg_match("/[a-zA-Z0-9 \\-_\\$\\.\X]*/", $val);
});

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

function get_id($obj, $id = true) {
    require_once "db.php";
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
        //if your lazy and didn't want to write out each param to bind.
        $res[] = $column;
    }

    return $res;
}

function j($t, $m){ return json_encode(array("{$t}" => $m)); }
function jerr($msg){ return j('error', $msg); }
function jmsg($msg){ return j('message', $msg); }

function borrar_obj($obj, $id = true, $ddie = true) {
    require_once "db.php";
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

function find_text($text, $init, $end = false, $reverse = false) {
    $text_init = ($reverse ? strrpos($text, $init) : strpos($text, $init)) + strlen($init);
    $text_end = !$end ? strlen($text) : strpos($text, $end, $text_init);
    if($text_end === false) $text_end = strlen($text);

    return substr($text, $text_init, $text_end - $text_init);
}

function wget($url, $ch = null) {
    $ch = $ch == null ? curl_init() : $ch;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, NULFILE);

    return curl_exec ($ch);
}

function wpost($url, $data, $ch = null, $headers = null) {
    $ch = $ch == null ? curl_init() : $ch;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, NULFILE);

    if($headers != null) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    return curl_exec ($ch);
}