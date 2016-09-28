<?php defined("INCLUDED") or die("Acceso denegado.");

if(!isset($_SESSION['logged'])) {
    $_SESSION['logged'] = false;
}

echo json_data(["logged" => $_SESSION['logged']]);