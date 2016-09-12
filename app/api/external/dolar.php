<?php defined("INCLUDED") or die("Denied"); // try_logged();

$tmp_path = "app/tmp/";
$cache_path = $tmp_path . "dolar.json";
$cache_ttl = 60;

function dollar_get_cached() {
    global $cache_path;
    global $tmp_path;
    if(!is_dir($tmp_path)) {
        if(!mkdir($tmp_path, 0777, true) || !chmod($tmp_path, 0777)) {
            @rmdir($tmp_path);
            return null;
        }
    }

    if(!file_exists($cache_path) || !is_readable($cache_path)) {
        return null;
    }

    return json_decode(file_get_contents($cache_path));
}

function dollar_set_cache($val) {
    global $cache_path;
    if(!file_exists($cache_path) && !touch($cache_path)) {
        return false;
    }

    if(!is_writable($cache_path)) {
        return false;
    }

    $data = [
        "value" => $val,
        "ts" => time()
    ];

    $f = fopen($cache_path, "w");
    fwrite($f, json_encode($data));
    fclose($f);

    return true;
}

$cache = dollar_get_cached();
if(!$cache || (time() - $cache->ts) > $cache_ttl) {

    require_once "app/includes/dolar.class.php";
    $api = new DolarAPI();
    $valor = $api->getCurrent();
    dollar_set_cache($valor);

    throw_data($valor);
} else {
    throw_data($cache->valor);
}