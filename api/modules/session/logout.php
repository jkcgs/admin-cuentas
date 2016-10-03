<?php defined("INCLUDED") or die("Acceso denegado.");

session_destroy();
$_SESSION['logged'] = false;
$_SESSION['logged_id'] = null;

throw_data([
    "logged" => $_SESSION['logged']
]);