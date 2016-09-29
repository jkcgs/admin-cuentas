<?php defined("INCLUDED") or die("Acceso denegado.");

if(!isset($_SESSION['logged'])) {
    $_SESSION['logged'] = false;
}

$data = ["logged" => $_SESSION['logged']];
echo json_data($data);