<?php defined("INCLUDED") or die("nel");
@session_start();
if(!isset($_SESSION['logged'])) $_SESSION['logged'] = false;

if(!$_SESSION['logged']) {
    $user = $config['web-user'];
    $pass = $config['web-pass'];

    $iuser = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "";
    $ipass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : "";

    if ($iuser != $user && $ipass != $pass) {
        header('WWW-Authenticate: Basic realm="Cuentas"');
        header('HTTP/1.0 401 Unauthorized');
        die('Acceso denegado');
    }

    $_SESSION['logged'] = true;
}
