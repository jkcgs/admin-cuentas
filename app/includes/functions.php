<?php defined("INCLUDED") or die("Denied");

require "gump.class.php";
GUMP::add_filter("upper", function($value, $params = NULL) {
    return strtoupper($value);
});
GUMP::add_filter("default", function($value, $params = array('')) {
    return empty($value) ? $params[0] : $value;
});
GUMP::add_validator("yearmonth", function($field, $input, $param = NULL) {
    if(!isset($input[$field])) return false;
    $val = $input[$field];
    return strlen($val) == 7 && preg_match("/[0-9]{4}\-[0-9]{2}/", $val);
});
GUMP::add_validator("text", function($field, $input, $param = NULL) {
    if(!isset($input[$field])) return false;
    $val = $input[$field];
    return preg_match("/[a-zA-Z0-9 \\-_\\$\\.\X]*/", $val);
});


function throw_error($msg, $data = null) {
    $m = json_encode([
        "success" => false,
        "message" => $msg,
        "data" => $data
    ]);

    die($m);
}

function throw_success($data = null) {
    die(json_encode([
        "success" => true,
        "data" => $data
    ]));
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
