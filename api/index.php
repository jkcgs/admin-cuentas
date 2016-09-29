<?php

define("INCLUDED", true);
@session_start();
require "includes/functions_headless.php";
if(!file_exists("config.php")) {
    throw_error("No existe el archivo de configuración. ".
    "Prueba a copiar el archivo de configuración de ejemplo y completa los ajustes. ".
    "No olvides ejecutar 'composer install' en la carpeta 'api' de la aplicación.");
}

require "config.php";
session_var_init("logged", false);
session_var_init("logged_id", false);
if($_SESSION["logged_id"] !== false) {
    $UID = $_SESSION["logged_id"];
}

header("Content-Type: application/json");
if($config['maintenance']) {
    throw_error("Sistema en mantención.");
}

if(!file_exists("vendor/autoload.php")) {
    throw_error(
        "No existe el archivo de autocarga de dependencias. " .
        "¿Ejecutaste 'composer install' en la carpeta 'api' de la aplicación?"
    );
}

require "vendor/autoload.php";
require "includes/functions.php";

reset($_GET);
$route = key($_GET);

if($route != preg_replace("/[^a-zA-Z0-9_\-\/]+/", "", $route)) {
    throw_error("API no encontrada");
}

// AUTORUTEO
// Cuando algunos argumentos (separados por "/") no se detectan como una ruta existente,
// se pasan como argumentos de la ruta, para no tener que usar, por ejemplo, "?ruta/accion&id=1"
// entonces pasaría a ser "?ruta/accion/1", en donde el arreglo $route_args contendría ["1"]
// Adicionalmente, si no se especifica la acción, se extiende como "index", por ejemplo
// si tenemos la ruta "?ruta", y existe la acción ruta/index, entonces se agrega automáticamente
// Por último, se agregan automáticamente en el nivel de la acción los archivos index.php de la
// carpeta del módulo, nivel por nivel.
// Por ejemplo, para la ruta "ruta1/subruta1/accion", se incluyen automáticamente los archivos
// ruta1/index.php y ruta1/subruta1/index.php, en caso de existir. Esto no puede ser desactivado.

$route_exp = explode("/", trim($route, "/"));
$route_args = [];
$route_path = "";

foreach ($route_exp as $arg) {
    if (is_dir("modules/$route_path/$arg")) {
        array_shift($route_exp);
        if (empty($route_path)) {
            $route_path .= $arg;
        } else {
            $route_path .= "/$arg";
        }
    } elseif (is_file("modules/$route_path/$arg.php")) {
        $route_path = "$route_path/$arg.php";
        array_shift($route_exp);
    } else {
        $route_args = $route_exp;
    }
}

if (is_file("modules/$route_path/index.php")) {
    $route_path = "$route_path/index.php";
} elseif (is_dir("modules/$route_path")) {
    throw_error("API no encontrada");
}

if(empty($route_path)) {
    throw_error("API no encontrada");
}

try {
    $fpaths = explode("/", $route_path);
    $fcurrent = "";
    foreach ($fpaths as $fpath) {
        $fcurrent .= "/" . $fpath;
        if(is_file("modules$fcurrent/index.php")) {
            require_once "modules/$fcurrent/index.php";
        }
    }

    require "modules/$route_path";
} catch(Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    throw_error("PHP Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
}

