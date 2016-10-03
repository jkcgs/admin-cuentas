<?php defined("INCLUDED") or die("Denied");

// Incluye funciones que no tienen dependencias de otros
// archivos o librerías

// Inicializa una variable de sesión
function session_var_init($key, $val) {
    if(!isset($_SESSION[$key])) {
        $_SESSION[$key] = $val;
    }
}

// Muestra datos JSON y termina el proceso
function throw_data($data, $success = true, $message = null) {
    die(json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]));
}

// Muestra datos JSON con flag de éxito
function throw_success($data = null) {
    throw_data($data);
}

// Muestra un error vía JSON
function throw_error($msg, $data = null) {
    throw_data($data, false, $msg);
}

// Si no se ha iniciado sesión, se genera un error con
// un estado HTTP 401 (no autorizado)
function try_logged() {
    if(!isset($_SESSION['logged']) || !$_SESSION['logged']) {
        header("HTTP/1.1 401 Unauthorized");
        throw_error("Not logged");
    }
}

function text_find($text, $init, $end = false, $reverse = false) {
	$pos_init = $reverse ? strrpos($text, $init) : strpos($text, $init);
    $offset = $pos_init + strlen($init);
	$length = $end === false ? null : (strpos($text, $end, $offset) - $offset);

    return substr($text, $offset, $length);
}