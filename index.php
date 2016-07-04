<?php
define("INCLUDED", 1);
define("VERSION", "0.0.1-alpha");

if(!file_exists("app/config/config.php")) {
	die("El archivo de configuración no existe. Puede utilizar el archivo config.example.php para crearlo.");
}

require 'vendor/autoload.php';
require 'app/init.php';
