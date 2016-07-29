<?php

define("INCLUDED", true);
@session_start();
require "app/includes/config.php";
require "app/includes/functions_noreq.php";

session_var_init("logged", false);

header("Content-Type: application/json");
if($config['maintenance']) {
    throw_error("Sistema en mantención.");
}

if(!file_exists("vendor/autoload.php")) {
    throw_error(
        "No existe el archivo de autocarga de dependencias. " .
        "Pruebe a ejecutar 'composer install' en la raíz de la aplicación."
    );
}

require "vendor/autoload.php";
require "app/includes/functions.php";
require "app/includes/database.php";

reset($_GET);
$route = key($_GET);

if($route != preg_replace("/[^a-zA-Z0-9_\-\/]/", "", $route)) {
    throw_error("Carácteres inválidos en la ruta");
}

$route = is_dir($route) ? "app/api/$route/index.php" : "app/api/$route.php";
if(!file_exists($route)) {
    throw_error("API no encontrada");
}

try {
    require $route;
} catch(Exception $e) {
    header("HTTP/1.1 500 Internal Server Error");
    throw_error("PHP Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
}

