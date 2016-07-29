<?php defined("INCLUDED") or die("Denied");

$is_win = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
define('NULFILE', $is_win ? 'NUL' : '/dev/null');

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

////// Funciones cURL
use \Curl\Curl;

// User-Agent (Chrome 51, Win10 64 bits)
define("UA", "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");

// Inicializar Curl
function init_curl() {
    $curl = new Curl();
    $curl->setUserAgent(UA);
    $curl->setCookieFile(NULFILE);
    $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
    $curl->setOpt(CURLOPT_RETURNTRANSFER, true);
    $curl->setOpt(CURLOPT_FOLLOWLOCATION, true);

    return $curl;
}
