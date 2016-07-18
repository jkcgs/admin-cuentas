<?php defined("INCLUDED") or die("Acceso denegado.");

session_destroy();
$_SESSION['logged'] = false;

echo json_data(["logged" => $_SESSION['logged']]);