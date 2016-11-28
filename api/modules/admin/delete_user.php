<?php defined("INCLUDED") or die("Denied");

if(!isset($route_args[0])) {
    throw_error("No se envió el ID del usuario");
}

if($UID == $route_args[0]) {
    throw_error("No puedes suicidarte! No, por favor! <3");
}

delete_obj("usuarios", $route_args[0]);