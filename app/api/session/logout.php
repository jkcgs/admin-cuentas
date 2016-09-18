<?php defined("INCLUDED") or die("Acceso denegado.");

session_destroy();
$_SESSION['logged'] = false;
$_SESSION['logged_user'] = null;

echo json_data(["logged" => $_SESSION['logged'], "user" => $_SESSION['logged_user']]);