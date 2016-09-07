<?php defined("INCLUDED") or die("Denied");

function session_var_init($key, $val) {
    if(!isset($_SESSION[$key])) {
        $_SESSION[$key] = $val;
    }
}

function throw_error($msg, $data = null) {
    $m = json_encode([
        "success" => false,
        "message" => $msg,
        "data" => $data
    ]);

    die($m);
}

function throw_success($data = null) {
    $res = ["success" => true];
    if($data != null) {
        $res["data"] = $data;
    }

    die(json_encode($res));
}

function json_data($data) {
    return json_encode([
        "success" => true,
        "message" => null,
        "data" => $data
    ]);
}

function throw_data($data) {
    die(json_data($data));
}

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