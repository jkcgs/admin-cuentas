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

function clear_html($s) {
    return str_replace("<", "&lt;", $s);
}

function j($t, $m){ return json_encode(array("{$t}" => $m)); }
function jerr($msg){ return j('error', $msg); }
function jmsg($msg){ return j('message', $msg); }

function find_text($text, $init, $end = false, $reverse = false) {
    $text_init = ($reverse ? strrpos($text, $init) : strpos($text, $init)) + strlen($init);
    $text_end = !$end ? strlen($text) : strpos($text, $end, $text_init);
    if($text_end === false) $text_end = strlen($text);

    return substr($text, $text_init, $text_end - $text_init);
}

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
